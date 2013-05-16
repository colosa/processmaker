<?php
$response = new stdclass();
$response->status = isset($_SESSION['USER_LOGGED']);
if (isset($_REQUEST['dynaformEditorParams'])) {
    $_SESSION['Current_Dynafom']['Parameters'] = unserialize(stripslashes($_REQUEST['dynaformEditorParams']));
}
if (isset($_REQUEST['dynaformRestoreValues'])) {
    $aRetValues = unserialize(stripslashes($_REQUEST['dynaformRestoreValues']));
    if (isset($aRetValues['APPLICATION'])) {
        $_SESSION['APPLICATION'] = $aRetValues['APPLICATION'];
    }
    if (isset($aRetValues['PROCESS'])) {
        $_SESSION['PROCESS'] = $aRetValues['PROCESS'];
    }
    if (isset($aRetValues['TASK'])) {
        $_SESSION['TASK'] = $aRetValues['TASK'];
    }
    if (isset($aRetValues['INDEX'])) {
        $_SESSION['INDEX'] = $aRetValues['INDEX'];
    }
    if (isset($aRetValues['TRIGGER_DEBUG'])) {
        $_SESSION['TRIGGER_DEBUG'] = $aRetValues['TRIGGER_DEBUG'];
    }
    if (isset($aRetValues['APP_DATA'])) {
        $_SESSION['APP_DATA'] = $aRetValues['APP_DATA'];
    }
}
die(G::json_encode($response));