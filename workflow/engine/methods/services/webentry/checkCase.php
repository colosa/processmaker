<?php
/**
 * This service verify if the provided APP_UID and DEL_INDEX could be used
 * for the web entry.
 */

/* @var $RBAC RBAC */
global $RBAC;
G::LoadClass('pmFunctions');
try {
    if (empty($_REQUEST['app_uid'])) {
        throw new \Exception('Missing required field "app_uid"');
    }
    if (empty($_REQUEST['del_index'])) {
        throw new \Exception('Missing required field "del_index"');
    }
    if (empty($_SESSION['USER_LOGGED'])) {
        throw new \Exception('You are not logged');
    }

    $appUid = $_REQUEST['app_uid'];
    $delIndex = $_REQUEST['del_index'];
    $delegation = \AppDelegationPeer::retrieveByPK($appUid, $delIndex);

    $check = $delegation->getDelThreadStatus() === 'OPEN' &&
        $delegation->getUsrUid() === $_SESSION['USER_LOGGED'];

    $result = ["check" => $check];
} catch (\Exception $e) {
    $result = [
        'error' => $e->getMessage(),
    ];
    http_response_code(500);
}
echo G::json_encode($result);
