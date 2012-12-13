<?php
/**
 * login.php
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

$aFields = array();

if (!isset($_GET['u'])) {
    $aFields['URL'] = '';
} else {
    $aFields['URL'] = urldecode($_GET['u']);
}

if (!isset($_SESSION['G_MESSAGE'])) {
    $_SESSION['G_MESSAGE'] = '';
}

if (!isset($_SESSION['G_MESSAGE_TYPE'])) {
    $_SESSION['G_MESSAGE_TYPE'] = '';
}

$msg = $_SESSION['G_MESSAGE'];
$msgType = $_SESSION['G_MESSAGE_TYPE'];

if (!isset($_SESSION['FAILED_LOGINS'])) {
    $_SESSION['FAILED_LOGINS'] = 0;
}

$sFailedLogins = $_SESSION['FAILED_LOGINS'];

$aFields['LOGIN_VERIFY_MSG'] = G::loadTranslation('LOGIN_VERIFY_MSG');
//$aFields['LOGIN_VERIFY_MSG'] = Bootstrap::loadTranslation('LOGIN_VERIFY_MSG');

if (isset ($_SESSION['USER_LOGGED'])) {
    require_once 'classes/model/LoginLog.php';
    //close the session, if the current session_id was used in PM.
    $oCriteria = new Criteria('workflow');

    $oCriteria->add(LoginLogPeer::LOG_SID, session_id());
    $oCriteria->add(LoginLogPeer::USR_UID, isset($_SESSION['USER_LOGGED']) ? $_SESSION['USER_LOGGED'] : '-');
    $oCriteria->add(LoginLogPeer::LOG_STATUS, 'ACTIVE');
    $oCriteria->add(LoginLogPeer::LOG_END_DATE, null, Criteria::ISNULL);

    $oDataset = LoginLogPeer::doSelectRS($oCriteria);

    $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
    $oDataset->next();

    $aRow = $oDataset->getRow();

    if ($aRow) {
        setcookie("workspaceSkin", SYS_SKIN, time() + 24*60*60, "/sys".SYS_SYS);
        if ($aRow['LOG_STATUS'] != 'CLOSED' && $aRow['LOG_END_DATE'] == null) {
            $weblog = new LoginLog();

            $aLog['LOG_UID'] = $aRow['LOG_UID'];
            $aLog['LOG_STATUS'] = 'CLOSED';
            $aLog['LOG_IP'] = $aRow['LOG_IP'];
            $aLog['LOG_SID'] = session_id();
            $aLog['LOG_INIT_DATE'] = $aRow['LOG_INIT_DATE'];
            $aLog['LOG_END_DATE'] = date('Y-m-d H:i:s');
            $aLog['LOG_CLIENT_HOSTNAME'] = $aRow['LOG_CLIENT_HOSTNAME'];
            $aLog['USR_UID'] = $aRow['USR_UID'];

            $weblog->update($aLog);
        }
    }
} else {
    // Execute SSO trigger
    $pluginRegistry =& PMPluginRegistry::getSingleton();
    if (defined('PM_SINGLE_SIGN_ON')) {
        if ($pluginRegistry->existsTrigger(PM_SINGLE_SIGN_ON)) {
            if ($pluginRegistry->executeTriggers(PM_SINGLE_SIGN_ON, null)) {
                // Start new session
                @session_destroy();
                session_start();
                session_regenerate_id();

                // Authenticate
                require_once 'authentication.php';

                die();
            }
        }
    }
}
//end log

//start new session
@session_destroy();
session_start();
session_regenerate_id();

if (strlen($msg) > 0) {
    $_SESSION['G_MESSAGE'] = $msg;
}
if (strlen($msgType) > 0) {
    $_SESSION['G_MESSAGE_TYPE'] = $msgType;
}

$_SESSION['FAILED_LOGINS'] = $sFailedLogins;

//translation
//$Translations = G::getModel("Translation");
//require_once "classes/model/Translation.php";
$Translations = new Translation();
$translationsTable = $Translations->getTranslationEnvironments();

$availableLangArray = array ();
$availableLangArray [] = array ('LANG_ID' => 'char', 'LANG_NAME' => 'char' );
foreach ($translationsTable as $locale) {
    $row['LANG_ID'] = $locale['LOCALE'];

    if ($locale['COUNTRY'] != '.') {
        $row['LANG_NAME'] = $locale['LANGUAGE'] . ' (' . (ucwords(strtolower($locale['COUNTRY']))) . ')';
    } else {
        $row['LANG_NAME'] = $locale['LANGUAGE'];
    }

    $availableLangArray [] = $row;
}

global $_DBArray;
$_DBArray ['langOptions'] = $availableLangArray;

G::LoadClass('configuration');
//BootStrap::LoadClass('configuration');

$oConf = new Configurations();
$oConf->loadConfig($obj, 'ENVIRONMENT_SETTINGS', '');
$aFields['USER_LANG'] = isset($oConf->aConfig['login_defaultLanguage'])
                        ? $oConf->aConfig['login_defaultLanguage']
                        : 'en';

$G_PUBLISH = new Publisher();
$G_PUBLISH->AddContent('xmlform', 'xmlform', 'login/login', '', $aFields, SYS_URI . 'login/authentication.php');
G::LoadClass('serverConfiguration');
//Bootstrap::LoadClass('serverConfiguration');
//get the serverconf singleton, and check if we can send the heartbeat
$oServerConf = & serverConf::getSingleton();

$sflag = $oServerConf->getHeartbeatProperty('HB_OPTION', 'HEART_BEAT_CONF');
$sflag = (trim($sflag) != '') ? $sflag : '1';

//get date of next beat
$nextBeatDate = $oServerConf->getHeartbeatProperty('HB_NEXT_BEAT_DATE', 'HEART_BEAT_CONF');

//if flag to send heartbeat is enabled, and it is time to send heartbeat, sent it using asynchronous beat.
if (($sflag == "1") && ((strtotime("now") > $nextBeatDate) || is_null($nextBeatDate))) {
    $oHeadPublisher =& headPublisher::getSingleton();
    //To do: we need to change to ExtJs
    $oHeadPublisher->addScriptCode('var flagHeartBeat = 1;');
} else {
    $oHeadPublisher->addScriptCode('var flagHeartBeat = 0;');
}

//check if we show the panel with the getting started info

require_once 'classes/model/Configuration.php';
$oConfiguration = new Configuration ( );
$oCriteria = new Criteria ( 'workflow' );
$oCriteria->add(ConfigurationPeer::CFG_UID, 'getStarted');
$oCriteria->add(ConfigurationPeer::OBJ_UID, '');
$oCriteria->add(ConfigurationPeer::CFG_VALUE, '1');
$oCriteria->add(ConfigurationPeer::PRO_UID, '');
$oCriteria->add(ConfigurationPeer::USR_UID, '');
$oCriteria->add(ConfigurationPeer::APP_UID, '');
$flagGettingStarted =  ConfigurationPeer::doCount($oCriteria);
if ($flagGettingStarted == 0) {
    $oHeadPublisher->addScriptCode('var flagGettingStarted = 1;');
} else {
    $oHeadPublisher->addScriptCode('var flagGettingStarted = 0;');
}

$dummy = '';

$oConf->loadConfig($dummy, 'ENVIRONMENT_SETTINGS', '');
$flagForgotPassword = isset($oConf->aConfig['login_enableForgotPassword'])
                      ? $oConf->aConfig['login_enableForgotPassword']
                      : 'off';

$oHeadPublisher->addScriptCode("var flagForgotPassword = '$flagForgotPassword';");

G::RenderPage('publish');

