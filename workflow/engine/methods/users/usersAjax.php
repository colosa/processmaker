<?php

$filter = new InputFilter();
$_POST = $filter->xssFilterHard($_POST);
if(isset($_SESSION['USER_LOGGED'])) {
	$_SESSION['USER_LOGGED'] = $filter->xssFilterHard($_SESSION['USER_LOGGED']);
}
if(isset($_SESSION['USR_USERNAME'])) {
	$_SESSION['USR_USERNAME'] = $filter->xssFilterHard($_SESSION['USR_USERNAME']);
}

global $RBAC;
$result = new StdClass();

switch ($_POST['action']) {
    case 'countryList':
        require_once ("classes/model/IsoCountry.php");
        $c = new Criteria();
        $c->add(IsoCountryPeer::IC_UID, null, Criteria::ISNOTNULL);
        $c->addAscendingOrderByColumn(IsoCountryPeer::IC_NAME);

        $countries = IsoCountryPeer::doSelect($c);
        foreach ($countries as $rowid => $row) {
            $oData[] = Array('IC_UID' => $row->getICUid(), 'IC_NAME' => $row->getICName());
        }
        print (G::json_encode($oData));
        break;
    case 'stateList':
        require_once ("classes/model/IsoSubdivision.php");
        $c = new Criteria();
        $country = $_POST['IC_UID'];
        $c->add(IsoSubdivisionPeer::IC_UID, $country, Criteria::EQUAL);
        $c->addAscendingOrderByColumn(IsoSubdivisionPeer::IS_NAME);
        $locations = IsoSubdivisionPeer::doSelect($c);

        $oData = Array();
        foreach ($locations as $rowid => $row) {
            if (($row->getISUid() != '') && ($row->getISName() != '')) {
                $oData[] = Array('IS_UID' => $row->getISUid(), 'IS_NAME' => $row->getISName());
            }
        }
        print (G::json_encode($oData));
        break;
    case 'locationList':
        require_once ("classes/model/IsoLocation.php");
        $c = new Criteria();
        $country = $_POST['IC_UID'];
        $state = $_POST['IS_UID'];
        $c->add(IsoLocationPeer::IC_UID, $country, Criteria::EQUAL);
        $c->add(IsoLocationPeer::IS_UID, $state, Criteria::EQUAL);
        $c->addAscendingOrderByColumn(IsoLocationPeer::IL_NAME);
        $locations = IsoLocationPeer::doSelect($c);

        $oData = Array();
        foreach ($locations as $rowid => $row) {
            if (($row->getILUid() != '') && ($row->getILName() != '')) {
                $oData[] = Array('IL_UID' => $row->getILUid(), 'IL_NAME' => $row->getILName());
            }
        }
        print (G::json_encode($oData));
        break;
    case 'usersList':
        $filter = (isset($_POST['filter']))? $_POST['filter'] : '';

        $arrayUser = [];

        $user = new \ProcessMaker\BusinessModel\User();
        $conf = new Configurations();

        $arrayConfFormat = $conf->getFormats();

        $arrayCondition = [[UsersPeer::USR_STATUS, ['ACTIVE', 'VACATION'], Criteria::IN]];

        if (isset($_POST['USR_UID'])) {
            $arrayCondition[] = [UsersPeer::USR_UID, $_POST['USR_UID'], Criteria::NOT_EQUAL];
        }

        $result = $user->getUsers(['condition' => $arrayCondition, 'filter' => $filter], null, null, null, 25);

        foreach ($result['data'] as $record) {
            $arrayUser[] = [
                'USR_UID'       => $record['USR_UID'],
                'USER_FULLNAME' => G::getFormatUserList($arrayConfFormat['format'], $record)
            ];
        }

        echo G::json_encode($arrayUser);
        break;
    case 'availableCalendars':
        $calendar = new Calendar();
        $calendarObj = $calendar->getCalendarList(true, true);
        $oData[] = array('CALENDAR_UID' => '', 'CALENDAR_NAME' => '- ' . G::LoadTranslation('ID_NONE') . ' -');
        foreach ($calendarObj['array'] as $rowid => $row) {
            if ($rowid > 0) {
                $oData[] = array('CALENDAR_UID' => $row['CALENDAR_UID'], 'CALENDAR_NAME' => $row['CALENDAR_NAME']);
            }
        }
        print (G::json_encode($oData));
        break;
    case 'rolesList':
        require_once PATH_RBAC . "model/Roles.php";
        $roles = new Roles();
        $rolesData = $roles->getAllRoles();
        foreach ($rolesData as $rowid => $row) {
            $oData[] = array('ROL_UID' => $row['ROL_CODE'], 'ROL_CODE' => $row['ROL_NAME']);
        }
        print (G::json_encode($oData));
        break;
    case 'getUserLogedRole':
        require_once 'classes/model/Users.php';
        $oUser = new Users();
        $aUserLog = $oUser->loadDetailed($_SESSION['USER_LOGGED']);
        print (G::json_encode(array(
            'USR_UID' => $aUserLog['USR_UID'],
            'USR_USERNAME' => $aUserLog['USR_USERNAME'],
            'USR_ROLE' => $aUserLog['USR_ROLE']
        )));
        break;
    case 'languagesList':
        $Translations = new Translation();
        $langs = $Translations->getTranslationEnvironments();
        $oData[] = array('LAN_ID' => '', 'LAN_NAME' => '- ' . G::LoadTranslation('ID_NONE') . ' -');
        foreach ($langs as $lang) {
            $oData[] = array ('LAN_ID' => $lang['LOCALE'],'LAN_NAME' => $lang['LANGUAGE']
            );
        }
        print (G::json_encode($oData));
        break;
    case 'saveUser':
    case 'savePersonalInfo':
        try {
            verifyCsrfToken($_POST);
            $user = new \ProcessMaker\BusinessModel\User();
            $form = $_POST;
            $permissionsToSaveData = $user->getPermissionsForEdit();
            $form = $user->checkPermissionForEdit($_SESSION['USER_LOGGED'], $permissionsToSaveData, $form);

            switch ($_POST['action']) {
                case 'saveUser';
                    if (!$user->checkPermission($_SESSION['USER_LOGGED'], 'PM_USERS')) {
                        throw new Exception(G::LoadTranslation('ID_USER_NOT_HAVE_PERMISSION', [$_SESSION['USER_LOGGED']]));
                    }
                    break;
                case 'savePersonalInfo':
                    if (!$user->checkPermission($_SESSION['USER_LOGGED'], 'PM_USERS') &&
                        !$user->checkPermission($_SESSION['USER_LOGGED'], 'PM_EDITPERSONALINFO')
                    ) {
                        throw new Exception(G::LoadTranslation('ID_USER_NOT_HAVE_PERMISSION', [$_SESSION['USER_LOGGED']]));
                    }
                    break;
                default:
                    throw new Exception(G::LoadTranslation('ID_INVALID_DATA'));
                    break;
            }

            if (array_key_exists('USR_LOGGED_NEXT_TIME', $form)) {
                $form['USR_LOGGED_NEXT_TIME'] = ($form['USR_LOGGED_NEXT_TIME']) ? 1 : 0;
            }

            $userUid = '';
            $auditLogType = '';
            if ($form['USR_UID'] == '') {
                $arrayUserData = $user->create($form);
                $userUid = $arrayUserData['USR_UID'];
                $auditLogType = 'INS';
            } else {
                if (array_key_exists('USR_NEW_PASS', $form) && $form['USR_NEW_PASS'] == '') {
                    unset($form['USR_NEW_PASS']);
                }

                $result = $user->update($form['USR_UID'], $form, $_SESSION['USER_LOGGED']);
                $userUid = $form['USR_UID'];
                $arrayUserData = $user->getUserRecordByPk($userUid, [], false);
                $auditLogType = 'UPD';
            }

            $user->auditLog($auditLogType, array_merge(['USR_UID' => $userUid, 'USR_USERNAME' => $arrayUserData['USR_USERNAME']], $form));
            /* Saving preferences */
            $def_lang = isset($form['PREF_DEFAULT_LANG']) ? $form['PREF_DEFAULT_LANG'] : '';
            $def_menu = isset($form['PREF_DEFAULT_MENUSELECTED']) ? $form['PREF_DEFAULT_MENUSELECTED'] : '';
            $def_cases_menu = isset($form['PREF_DEFAULT_CASES_MENUSELECTED']) ? $form['PREF_DEFAULT_CASES_MENUSELECTED'] : '';
            $oConf = new Configurations();
            $aConf = Array('DEFAULT_LANG' => $def_lang, 'DEFAULT_MENU' => $def_menu, 'DEFAULT_CASES_MENU' => $def_cases_menu);
            $oConf->aConfig = $aConf;
            $oConf->saveConfig('USER_PREFERENCES', '', '', $userUid);

            if ($user->checkPermission($userUid, 'PM_EDIT_USER_PROFILE_PHOTO')) {
                try {
                    $user->uploadImage($userUid);
                } catch (Exception $e) {
                    $result = new stdClass();
                    $result->success = false;
                    $result->fileError = true;

                    echo G::json_encode($result);
                    exit(0);
                }
            }

            if ($_SESSION['USER_LOGGED'] == $form['USR_UID']) {
                /* UPDATING SESSION VARIABLES */
                $aUser = $RBAC->userObj->load($_SESSION['USER_LOGGED']);
                $_SESSION['USR_FULLNAME'] = $aUser['USR_FIRSTNAME'] . ' ' . $aUser['USR_LASTNAME'];
            }

            $result = new stdClass();
            $result->success = true;
            print (G::json_encode($result));
        } catch (Exception $e) {
            $result = new stdClass();
            $result->success = false;
            $result->error = $e->getMessage();
            print (G::json_encode($result));
        }
        break;
    case 'userData':
        require_once 'classes/model/Users.php';
        $_SESSION['CURRENT_USER'] = $_POST['USR_UID'];
        $oUser = new Users();
        $aFields = $oUser->loadDetailed($_POST['USR_UID']);

        //Load Calendar options and falue for this user
        $calendar = new Calendar();
        $calendarInfo = $calendar->getCalendarFor($_POST['USR_UID'], $_POST['USR_UID'], $_POST['USR_UID']);
        //If the function returns a DEFAULT calendar it means that this object doesn't have assigned any calendar
        $aFields['USR_CALENDAR'] = $calendarInfo['CALENDAR_APPLIED'] != 'DEFAULT' ? $calendarInfo['CALENDAR_UID'] : "";
        $aFields['CALENDAR_NAME'] = $calendarInfo['CALENDAR_NAME'];

        #verifying if it has any preferences on the configurations table
        $oConf = new Configurations();
        $oConf->loadConfig($x, 'USER_PREFERENCES', '', '', $aFields['USR_UID'], '');

        $aFields['PREF_DEFAULT_MENUSELECTED'] = '';
        $aFields['PREF_DEFAULT_CASES_MENUSELECTED'] = '';
        if (sizeof($oConf->Fields) > 0) {
            // this user has a configuration record
            $aFields['PREF_DEFAULT_LANG'] = $oConf->aConfig['DEFAULT_LANG'];
            $aFields['PREF_DEFAULT_MENUSELECTED'] = isset($oConf->aConfig['DEFAULT_MENU']) ? $oConf->aConfig['DEFAULT_MENU'] : '';
            $aFields['PREF_DEFAULT_CASES_MENUSELECTED'] = isset($oConf->aConfig['DEFAULT_CASES_MENU']) ? $oConf->aConfig['DEFAULT_CASES_MENU'] : '';
        } else {
            switch ($RBAC->aUserInfo['PROCESSMAKER']['ROLE']['ROL_CODE']) {
                case 'PROCESSMAKER_ADMIN':
                    $aFields['PREF_DEFAULT_MENUSELECTED'] = 'PM_SETUP';
                    break;
                case 'PROCESSMAKER_OPERATOR':
                    $aFields['PREF_DEFAULT_MENUSELECTED'] = 'PM_CASES';
                    break;
            }
            $aFields['PREF_DEFAULT_LANG'] = SYS_LANG;
        }
        if ($aFields['USR_REPLACED_BY'] != '') {
            $user = new Users();
            $u = $user->load($aFields['USR_REPLACED_BY']);
            if ($u['USR_STATUS'] == 'CLOSED') {
                $replaced_by = '';
                $aFields['USR_REPLACED_BY'] = '';
            } else {
                $c = new Configurations();
                $arrayConfFormat = $c->getFormats();

                $replaced_by = G::getFormatUserList($arrayConfFormat['format'], $u);
            }
        } else {
            $replaced_by = '';
        }

        $aFields['REPLACED_NAME'] = $replaced_by;

        $menuSelected = '';

        if ($aFields['PREF_DEFAULT_MENUSELECTED'] != '') {
            foreach ($RBAC->aUserInfo['PROCESSMAKER']['PERMISSIONS'] as $permission) {
                if ($aFields['PREF_DEFAULT_MENUSELECTED'] == $permission['PER_CODE']) {
                    switch ($permission['PER_CODE']) {
                        case 'PM_USERS':
                        case 'PM_SETUP':
                            $menuSelected = strtoupper(G::LoadTranslation('ID_SETUP'));
                            break;
                        case 'PM_CASES':
                            $menuSelected = strtoupper(G::LoadTranslation('ID_CASES'));
                            break;
                        case 'PM_FACTORY':
                            $menuSelected = strtoupper(G::LoadTranslation('ID_APPLICATIONS'));
                            break;
                        case 'PM_DASHBOARD':
                            $menuSelected = strtoupper(G::LoadTranslation('ID_DASHBOARD'));
                            break;
                    }
                } else {
                    if ($aFields['PREF_DEFAULT_MENUSELECTED'] == 'PM_STRATEGIC_DASHBOARD') {
                        $menuSelected = strtoupper(G::LoadTranslation('ID_STRATEGIC_DASHBOARD'));
                    }
                }
            }
        }

        $aFields['MENUSELECTED_NAME'] = $menuSelected;

        $oMenu = new Menu();
        $oMenu->load('cases');
        $casesMenuSelected = '';

        if ($aFields['PREF_DEFAULT_CASES_MENUSELECTED'] != '') {
            foreach ($oMenu->Id as $i => $item) {

                if ($aFields['PREF_DEFAULT_CASES_MENUSELECTED'] == $item) {
                    $casesMenuSelected = $oMenu->Labels[$i];
                }
            }
        }

        require_once 'classes/model/Users.php';
        $oUser = new Users();
        $aUserLog = $oUser->loadDetailed($_SESSION['USER_LOGGED']);
        $aFields['USER_LOGGED_NAME'] = $aUserLog['USR_USERNAME'];
        $aFields['USER_LOGGED_ROLE'] = $aUserLog['USR_ROLE'];

        $aFields['CASES_MENUSELECTED_NAME'] = $casesMenuSelected;

        require_once 'classes/model/UsersProperties.php';
        $oUserProperty = new UsersProperties();
        $aUserProperty = $oUserProperty->loadOrCreateIfNotExists($aFields['USR_UID'], array('USR_PASSWORD_HISTORY' => serialize(array($oUser->getUsrPassword()))));
        $aFields['USR_LOGGED_NEXT_TIME'] = $aUserProperty['USR_LOGGED_NEXT_TIME'];

        if (array_key_exists('USR_PASSWORD', $aFields)) {
            unset($aFields['USR_PASSWORD']);
        }

        $userPermissions = new \ProcessMaker\BusinessModel\User();
        $permissions = $userPermissions->loadDetailedPermissions($aFields);

        $result->success = true;
        $result->user = $aFields;
        $result->permission = $permissions;

        print (G::json_encode($result));
        break;
    case 'defaultMainMenuOptionList':
        foreach ($RBAC->aUserInfo['PROCESSMAKER']['PERMISSIONS'] as $permission) {
            switch ($permission['PER_CODE']) {
                case 'PM_USERS':
                case 'PM_SETUP':
                    $rows[] = Array('id' => 'PM_SETUP', 'name' => strtoupper(G::LoadTranslation('ID_SETUP'))
                    );
                    break;
                case 'PM_CASES':
                    $rows[] = Array('id' => 'PM_CASES', 'name' => strtoupper(G::LoadTranslation('ID_CASES'))
                    );
                    break;
                case 'PM_FACTORY':
                    $rows[] = Array('id' => 'PM_FACTORY', 'name' => strtoupper(G::LoadTranslation('ID_APPLICATIONS'))
                    );
                    break;
                case 'PM_DASHBOARD':
                    $rows[] = Array('id' => 'PM_DASHBOARD', 'name' => strtoupper(G::LoadTranslation('ID_DASHBOARD'))
                    );
                    /*----------------------------------********---------------------------------*/
                    break;
            }
        }
        print (G::json_encode($rows));
        break;
    case 'defaultCasesMenuOptionList':

        $oMenu = new Menu();
        $oMenu->load('cases');

        foreach ($oMenu->Id as $i => $item) {
            if ($oMenu->Types[$i] != 'blockHeader') {
                $rowsCasesMenu[] = Array('id' => $item, 'name' => $oMenu->Labels[$i]);
            }
        }
        print (G::json_encode($rowsCasesMenu));
        break;
    case 'testPassword':
        require_once 'classes/model/UsersProperties.php';
        $oUserProperty = new UsersProperties();

        $aFields = array();
        $color = '';
        $img = '';
        $dateNow = date('Y-m-d H:i:s');
        $aErrors = $oUserProperty->validatePassword($_POST['PASSWORD_TEXT'], $dateNow, $dateNow);

        if (!empty($aErrors)) {
            $img = '/images/delete.png';
            $color = 'red';
            if (!defined('NO_DISPLAY_USERNAME')) {
                define('NO_DISPLAY_USERNAME', 1);
            }
            $aFields = array();
            $aFields['DESCRIPTION'] = G::LoadTranslation('ID_POLICY_ALERT') . ':<br />';

            foreach ($aErrors as $sError) {
                switch ($sError) {
                    case 'ID_PPP_MINIMUM_LENGTH':
                        $aFields['DESCRIPTION'] .= ' - ' . G::LoadTranslation($sError) . ': ' . PPP_MINIMUM_LENGTH . '<br />';
                        $aFields[substr($sError, 3)] = PPP_MINIMUM_LENGTH;
                        break;
                    case 'ID_PPP_MAXIMUM_LENGTH':
                        $aFields['DESCRIPTION'] .= ' - ' . G::LoadTranslation($sError) . ': ' . PPP_MAXIMUM_LENGTH . '<br />';
                        $aFields[substr($sError, 3)] = PPP_MAXIMUM_LENGTH;
                        break;
                    case 'ID_PPP_EXPIRATION_IN':
                        $aFields['DESCRIPTION'] .= ' - ' . G::LoadTranslation($sError) . ' ' . PPP_EXPIRATION_IN . ' ' . G::LoadTranslation('ID_DAYS') . '<br />';
                        $aFields[substr($sError, 3)] = PPP_EXPIRATION_IN;
                        break;
                    default:
                        $aFields['DESCRIPTION'] .= ' - ' . G::LoadTranslation($sError) . '<br />';
                        $aFields[substr($sError, 3)] = 1;
                        break;
                }
            }

            $aFields['DESCRIPTION'] .= G::LoadTranslation('ID_PLEASE_CHANGE_PASSWORD_POLICY') . '</span>';
            $aFields['STATUS'] = false;
        } else {
            $color = 'green';
            $img = '/images/dialog-ok-apply.png';
            $aFields['DESCRIPTION'] = G::LoadTranslation('ID_PASSWORD_COMPLIES_POLICIES') . '</span>';
            $aFields['STATUS'] = true;
        }
        $span = '<span style="color: ' . $color . '; font: 9px tahoma,arial,helvetica,sans-serif;">';
        $gif = '<img width="13" height="13" border="0" src="' . $img . '">';
        $aFields['DESCRIPTION'] = $span . $gif . $aFields['DESCRIPTION'];
        print (G::json_encode($aFields));
        break;
    case 'testUsername':
        require_once 'classes/model/Users.php';
        $_POST['NEW_USERNAME'] = trim($_POST['NEW_USERNAME']);
        $USR_UID = isset($_POST['USR_UID']) ? $_POST['USR_UID'] : '';

        $response = array("success" => true);

        $oCriteria = new Criteria();
        $oCriteria->addSelectColumn(UsersPeer::USR_USERNAME);

        $oCriteria->add(UsersPeer::USR_USERNAME, utf8_encode($_POST['NEW_USERNAME']));
        if ($USR_UID != '') {
            $oCriteria->add(UsersPeer::USR_UID, array($_POST['USR_UID']), Criteria::NOT_IN);
        }
        $oDataset = UsersPeer::doSelectRS($oCriteria);
        $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        $oDataset->next();
        $aRow = $oDataset->getRow();

        if (is_array($aRow) || $_POST['NEW_USERNAME'] == '') {
            $color = 'red';
            $img = '/images/delete.png';
            $dataVar['USER_ID'] = $_POST['NEW_USERNAME'];
            $text = G::LoadTranslation('ID_USERNAME_ALREADY_EXISTS', $dataVar);
            $text = ($_POST['NEW_USERNAME'] == '') ? G::LoadTranslation('ID_MSG_ERROR_USR_USERNAME') : $text;
            $response['exists'] = true;
        } else {
            $color = 'green';
            $img = '/images/dialog-ok-apply.png';
            $text = G::LoadTranslation('ID_USERNAME_CORRECT');
            $response['exists'] = false;
        }

        $span = '<span style="color: ' . $color . '; font: 9px tahoma,arial,helvetica,sans-serif;">';
        $gif = '<img width="13" height="13" border="0" src="' . $img . '">';
        $response['descriptionText'] = $span . $gif . $text . '</span>';
        echo G::json_encode($response);
        break;
    case "passwordValidate":
        $messageResultLogin = "";
        $password = $_POST["password"];
        $resultLogin = $RBAC->VerifyLogin($_SESSION["USR_USERNAME"], $password);

        if($resultLogin ==  $_SESSION["USER_LOGGED"]) {
            $messageResultLogin = "OK";
        } else {
            $messageResultLogin = "ERROR";
        }

        $response = array();
        $response["result"] = $messageResultLogin;
        echo G::json_encode($response);
        break;
}
