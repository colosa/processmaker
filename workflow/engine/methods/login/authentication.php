<?php
/**
 * authentication.php
 *
 * ProcessMaker Open Source Edition
 * Copyright (C) 2004 - 2008 Colosa Inc.23
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * For more information, contact Colosa Inc, 2566 Le Jeune Rd.,
 * Coral Gables, FL, 33134, USA, or email info@colosa.com.
 *
 */

try {
    if (!$RBAC->singleSignOn) {
        if (!isset($_POST['form']) ) {
            G::SendTemporalMessage ('ID_USER_HAVENT_RIGHTS_SYSTEM', 'error');
            G::header('Location: login');
            die();
        }

        $frm = $_POST['form'];
        $usr = '';
        $pwd = '';

        if (isset($frm['USR_USERNAME'])) {
            $usr = mb_strtolower(trim($frm['USR_USERNAME']), 'UTF-8');
            $pwd = trim($frm['USR_PASSWORD']);
        }

        $uid = $RBAC->VerifyLogin($usr , $pwd);
        $RBAC->cleanSessionFiles(72); //cleaning session files older than 72 hours

        switch ($uid) {
            //The user does doesn't exist
            case -1:
                $errLabel = 'WRONG_LOGIN_CREDENTIALS';
                break;
            //The password is incorrect
            case -2:
                $errLabel = 'WRONG_LOGIN_CREDENTIALS';
                if (isset($_SESSION['__AUTH_ERROR__'])) {
                    G::SendMessageText($_SESSION['__AUTH_ERROR__'], "warning");
                    unset($_SESSION['__AUTH_ERROR__']);
                }
                break;
            //The user is inactive
            case -3:
                require_once 'classes/model/Users.php';
                $user = new Users();
                $aUser = $user->loadByUsernameInArray($usr);

                switch ($aUser['USR_STATUS']) {
                    case 'VACATION':
                        $uid = $aUser['USR_UID'];
                        $RBAC->changeUserStatus($uid, 1);
                        $aUser['USR_STATUS'] = 'ACTIVE';
                        $user->update($aUser);
                        break;
                    case 'INACTIVE':
                        $errLabel = 'ID_USER_INACTIVE';
                        break;
                }
                break;
            //The Due date is finished
            case -4:
                $errLabel = 'ID_USER_INACTIVE_BY_DATE';
                break;
            case -5:
                $errLabel = 'ID_AUTHENTICATION_SOURCE_INVALID';
                break;
        }

        //to avoid empty string in user field.  This will avoid a weird message "this row doesn't exist"
        if ( !isset($uid) ) {
            $uid = -1;
            $errLabel = 'WRONG_LOGIN_CREDENTIALS';
        }

        if (!isset($uid) || $uid < 0) {
            if (isset($_SESSION['FAILED_LOGINS'])) {
                $_SESSION['FAILED_LOGINS']++;
            }
            if (!defined('PPP_FAILED_LOGINS')) {
                define('PPP_FAILED_LOGINS', 0);
            }
            if (PPP_FAILED_LOGINS > 0) {
                if ($_SESSION['FAILED_LOGINS'] >= PPP_FAILED_LOGINS) {
                    $oConnection = Propel::getConnection('rbac');
                    $oStatement  = $oConnection->prepareStatement("SELECT USR_UID FROM USERS WHERE USR_USERNAME = '" . $usr . "'");
                    $oDataset    = $oStatement->executeQuery();
                    if ($oDataset->next()) {
                        $sUserUID = $oDataset->getString('USR_UID');
                        $oConnection = Propel::getConnection('rbac');
                        $oStatement  = $oConnection->prepareStatement("UPDATE USERS SET USR_STATUS = 0 WHERE USR_UID = '" . $sUserUID . "'");
                        $oStatement->executeQuery();
                        $oConnection = Propel::getConnection('workflow');
                        $oStatement  = $oConnection->prepareStatement("UPDATE USERS SET USR_STATUS = 'INACTIVE' WHERE USR_UID = '" . $sUserUID . "'");
                        $oStatement->executeQuery();
                        unset($_SESSION['FAILED_LOGINS']);
                        G::SendMessageText(G::LoadTranslation('ID_ACCOUNT') . ' "' . $usr . '" ' . G::LoadTranslation('ID_ACCOUNT_DISABLED_CONTACT_ADMIN'), 'warning');
                    }
                }
            }

            if (strpos($_SERVER['HTTP_REFERER'], 'home/login') !== false) {
                $d = serialize(array('u'=>$usr, 'p'=>$pwd, 'm'=>G::LoadTranslation($errLabel)));
                $loginUrl = '../home/login?d='.base64_encode($d);
            } else {
                G::SendTemporalMessage($errLabel, "warning");

                if (substr(SYS_SKIN, 0, 2) !== 'ux') {
                    $loginUrl = 'login';
                } else {
                    $loginUrl = '../main/login';
                }
            }

            G::header("location: $loginUrl");
            die;
        }

        if (!isset( $_SESSION['WORKSPACE'] ) ) {
            $_SESSION['WORKSPACE'] = SYS_SYS;
        }

        //Execute the SSO Script from plugin
        $oPluginRegistry =& PMPluginRegistry::getSingleton();
        if ($oPluginRegistry->existsTrigger ( PM_LOGIN )) {
            $lSession="";
            $loginInfo = new loginInfo ($usr, $pwd, $lSession  );
            $oPluginRegistry->executeTriggers ( PM_LOGIN , $loginInfo );
        }
        $_SESSION['USER_LOGGED']  = $uid;
        $_SESSION['USR_USERNAME'] = $usr;
    } else {
        $uid = $RBAC->userObj->fields['USR_UID'];
        $usr = $RBAC->userObj->fields['USR_USERNAME'];
        $_SESSION['USER_LOGGED']  = $uid;
        $_SESSION['USR_USERNAME'] = $usr;
    }

    $aUser = $RBAC->userObj->load($_SESSION['USER_LOGGED']);
    $RBAC->loadUserRolePermission($RBAC->sSystem, $_SESSION['USER_LOGGED']);
    //$rol = $RBAC->rolesObj->load($RBAC->aUserInfo['PROCESSMAKER']['ROLE']['ROL_UID']);
    $_SESSION['USR_FULLNAME'] = $aUser['USR_FIRSTNAME'] . ' ' . $aUser['USR_LASTNAME'];
    //$_SESSION['USR_ROLENAME'] = $rol['ROL_NAME'];

    unset($_SESSION['FAILED_LOGINS']);

    // increment logins in heartbeat
    G::LoadClass('serverConfiguration');
    $oServerConf =& serverConf::getSingleton();
    $oServerConf->sucessfulLogin();

    // Assign the uid of user to userloggedobj
    $RBAC->loadUserRolePermission($RBAC->sSystem, $uid);
    $res = $RBAC->userCanAccess('PM_LOGIN');
    if ($res != 1 ) {
        if ($res == -2) {
            G::SendTemporalMessage ('ID_USER_HAVENT_RIGHTS_SYSTEM', "error");
        } else {
            G::SendTemporalMessage ('ID_USER_HAVENT_RIGHTS_PAGE', "error");
        }
        G::header  ("location: login.html");
        die;
    }

    if (isset($frm['USER_LANG'])) {
        if ($frm['USER_LANG'] != '') {
            $lang = $frm['USER_LANG'];
        }
    } else {
        if (defined('SYS_LANG')) {
            $lang = SYS_LANG;
        } else {
            $lang = 'en';
        }
    }

    /**log in table Login**/
    require_once 'classes/model/LoginLog.php';
    $weblog=new LoginLog();
    $aLog['LOG_UID']            = G::generateUniqueID();
    $aLog['LOG_STATUS']         = 'ACTIVE';
    $aLog['LOG_IP']             = $_SERVER['REMOTE_ADDR'];
    $aLog['LOG_SID']            = session_id();
    $aLog['LOG_INIT_DATE']      = date('Y-m-d H:i:s');
    //$aLog['LOG_END_DATE']       = '0000-00-00 00:00:00';
    $aLog['LOG_CLIENT_HOSTNAME']= $_SERVER['HTTP_HOST'];
    $aLog['USR_UID']            = $_SESSION['USER_LOGGED'];
    $weblog->create($aLog);
    /**end log**/

    //************** background processes, here we are putting some back office routines **********
    $heartBeatNWIDate = $oServerConf->getHeartbeatProperty('HB_NEXT_GWI_DATE','HEART_BEAT_CONF');
    if (is_null($heartBeatNWIDate)) {
        $heartBeatNWIDate = time();
    }
    if (time() >= $heartBeatNWIDate) {
        $oServerConf->setWsInfo(SYS_SYS, $oServerConf->getWorkspaceInfo(SYS_SYS));
        $oServerConf->setHeartbeatProperty('HB_NEXT_GWI_DATE', strtotime('+1 day'), 'HEART_BEAT_CONF');
    }

    //**** defining and saving server info, this file has the values of the global array $_SERVER ****
    //this file is useful for command line environment (no Browser), I mean for triggers, crons and other executed over command line

    $_CSERVER = $_SERVER;
    unset($_CSERVER['REQUEST_TIME']);
    unset($_CSERVER['REMOTE_PORT']);
    $cput = serialize($_CSERVER);
    if (!is_file(PATH_DATA_SITE . PATH_SEP . '.server_info')) {
        file_put_contents(PATH_DATA_SITE . PATH_SEP . '.server_info', $cput);
    } else {
        $c = file_get_contents(PATH_DATA_SITE . PATH_SEP . '.server_info');
        if (md5($c) != md5($cput)) {
            file_put_contents(PATH_DATA_SITE . PATH_SEP . '.server_info', $cput);
        }
    }

    /* Check password using policy - Start */
    require_once 'classes/model/UsersProperties.php';
    $oUserProperty = new UsersProperties();

    // getting default user location
    if (isset($_REQUEST['form']['URL']) && $_REQUEST['form']['URL'] != '') {
        if (isset($_SERVER['HTTP_REFERER'])) {
            if (strpos($_SERVER['HTTP_REFERER'], 'processes/processes_Map?PRO_UID=') !== false) {
                $sLocation = $_SERVER['HTTP_REFERER'];
            } else {
                $sLocation = $_REQUEST['form']['URL'];
            }
        } else {
            $sLocation = $_REQUEST['form']['URL'];
        }
    } else {
        if (isset($_REQUEST['u']) && $_REQUEST['u'] != '') {
            $sLocation = $_REQUEST['u'];
        } else {
            $sLocation = $oUserProperty->redirectTo($_SESSION['USER_LOGGED'], $lang);
        }
    }

    if ($RBAC->singleSignOn) {
        G::header('Location: ' . $sLocation);
        die();
    }

    $aUserProperty = $oUserProperty->loadOrCreateIfNotExists($_SESSION['USER_LOGGED'], array('USR_PASSWORD_HISTORY' => serialize(array(md5($pwd)))));
    $aErrors       = $oUserProperty->validatePassword($_POST['form']['USR_PASSWORD'], $aUserProperty['USR_LAST_UPDATE_DATE'], $aUserProperty['USR_LOGGED_NEXT_TIME']);

    if (!empty($aErrors)) {
        if (!defined('NO_DISPLAY_USERNAME')) {
            define('NO_DISPLAY_USERNAME', 1);
        }
        $aFields = array();
        $aFields['DESCRIPTION']  = '<span style="font-weight:normal;">';
        $aFields['DESCRIPTION'] .= G::LoadTranslation('ID_POLICY_ALERT').':<br /><br />';
        foreach ($aErrors as $sError) {
            switch ($sError) {
                case 'ID_PPP_MINIMUM_LENGTH':
                    $aFields['DESCRIPTION'] .= ' - ' . G::LoadTranslation($sError).': ' . PPP_MINIMUM_LENGTH . '<br />';
                    $aFields[substr($sError, 3)] = PPP_MINIMUM_LENGTH;
                    break;
                case 'ID_PPP_MAXIMUM_LENGTH':
                    $aFields['DESCRIPTION'] .= ' - ' . G::LoadTranslation($sError).': ' . PPP_MAXIMUM_LENGTH . '<br />';
                    $aFields[substr($sError, 3)] = PPP_MAXIMUM_LENGTH;
                    break;
                case 'ID_PPP_EXPIRATION_IN':
                    $aFields['DESCRIPTION'] .= ' - ' . G::LoadTranslation($sError).' ' . PPP_EXPIRATION_IN . ' ' . G::LoadTranslation('ID_DAYS') . '<br />';
                    $aFields[substr($sError, 3)] = PPP_EXPIRATION_IN;
                    break;
                default:
                    $aFields['DESCRIPTION'] .= ' - ' . G::LoadTranslation($sError).'<br />';
                    $aFields[substr($sError, 3)] = 1;
                    break;
            }
        }
        $aFields['DESCRIPTION'] .= '<br />' . G::LoadTranslation('ID_PLEASE_CHANGE_PASSWORD_POLICY') . '<br /><br /></span>';
        $G_PUBLISH = new Publisher;
        $G_PUBLISH->AddContent('xmlform', 'xmlform', 'login/changePassword', '', $aFields, 'changePassword');
        G::RenderPage('publish');
        die;
    }

    ///// VERIFY PLUGIN ENTERPRISE IF IS ENABLED AND PARTNER FLAG EXIST
    $partnerFlag = (defined('PARTNER_FLAG')) ? PARTNER_FLAG : false;
    if ($partnerFlag) {
        $flagEnableEnterprise = true;
        G::loadClass('PMPluginRegistry');
        $sSerializedFile = PATH_DATA_SITE . 'plugin.singleton';
        $oPluginRegistry = & PMPluginRegistry::getSingleton();
        if (file_exists( $sSerializedFile )) {
            $oPluginRegistry->unSerializeInstance( file_get_contents( $sSerializedFile ) );
            $attributes = $oPluginRegistry->getAttributes();
            if ( isset($attributes['_aPluginDetails']['enterprise']) &&
                 $attributes['_aPluginDetails']['enterprise']->enabled == 1
                ) {
                $flagEnableEnterprise = false;
            }
        }

        if ($flagEnableEnterprise) {
            $pluginFile = 'enterprise.php';
            require_once (PATH_PLUGINS . $pluginFile);
            $details = $oPluginRegistry->getPluginDetails( $pluginFile );
            @$oPluginRegistry->enablePlugin( $details->sNamespace );
            @$oPluginRegistry->setupPlugins();

            $language = new Language();
            $pathPluginTranslations = PATH_PLUGINS . 'enterprise' . PATH_SEP . 'translations' . PATH_SEP;
            if (file_exists($pathPluginTranslations . 'translations.php')) {
                if (!file_exists($pathPluginTranslations . 'enterprise' . '.' . SYS_LANG . '.po')) {
                    @$language->createLanguagePlugin('enterprise', SYS_LANG);
                }
                @$language->updateLanguagePlugin('enterprise', SYS_LANG);
            }
        }
    }

    $oHeadPublisher = &headPublisher::getSingleton();
    $oHeadPublisher->extJsInit = true;

    $oHeadPublisher->addExtJsScript('login/init', false);    //adding a javascript file .js
    $oHeadPublisher->assign('uriReq', $sLocation);
    G::RenderPage('publish', 'extJs');
    //G::header('Location: ' . $sLocation);
    die;
} catch ( Exception $e ) {
    $aMessage['MESSAGE'] = $e->getMessage();
    $G_PUBLISH = new Publisher;
    $G_PUBLISH->AddContent('xmlform', 'xmlform', 'login/showMessage', '', $aMessage );
    G::RenderPage( 'publish' );
    die;
}

