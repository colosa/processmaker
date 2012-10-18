<?php
$callback = isset( $_POST['callback'] ) ? $_POST['callback'] : 'stcCallback1001';
$dir = isset( $_POST['dir'] ) ? $_POST['dir'] : 'DESC';
$sort = isset( $_POST['sort'] ) ? $_POST['sort'] : '';
$start = isset( $_POST['start'] ) ? $_POST['start'] : '0';
$limit = isset( $_POST['limit'] ) ? $_POST['limit'] : '25';
$filter = isset( $_POST['filter'] ) ? $_POST['filter'] : '';
$search = isset( $_POST['search'] ) ? $_POST['search'] : '';
$process = isset( $_POST['process'] ) ? $_POST['process'] : '';
$user = isset( $_POST['user'] ) ? $_POST['user'] : '';
$status = isset( $_POST['status'] ) ? strtoupper( $_POST['status'] ) : '';
$action = isset( $_GET['action'] ) ? $_GET['action'] : (isset( $_POST['action'] ) ? $_POST['action'] : 'todo');
$type = isset( $_GET['type'] ) ? $_GET['type'] : (isset( $_POST['type'] ) ? $_POST['type'] : 'extjs');
$user = isset( $_POST['user'] ) ? $_POST['user'] : '';

$sentUids = explode( ',', $_POST['APP_UIDS'] );

$allUidsRecords = array ();
$allTasUids = array ();

// getting all App Uids and task Uids
foreach ($sentUids as $sentUid) {
    $aItem = explode( '|', $sentUid );
    $allUidsRecords[] = array ('APP_UID' => $aItem[0],'TAS_UID' => $aItem[1],'DEL_INDEX' => $aItem[2]);
}

$sReassignFromUser = isset( $_POST['user'] ) ? $_POST['user'] : '';
$sProcessUid = isset( $_POST['process'] ) ? $_POST['process'] : '';

G::LoadClass( 'tasks' );
G::LoadClass( 'groups' );
G::LoadClass( 'case' );
G::LoadClass( 'users' );
require_once ("classes/model/AppCacheView.php");

$oTasks = new Tasks();
$oGroups = new Groups();
$oUser = new Users();
$oCases = new Cases();

$aCasesList = Array ();
$vard = 0;
foreach ($allUidsRecords as $aRecord) {
    $vard = $vard + 1;
    $APP_UID = $aRecord['APP_UID'];
    $delIndex = $aRecord['DEL_INDEX'];
    $aCase = $oCases->loadCaseByDelegation( $APP_UID, $delIndex );

    $aUsersInvolved = Array ();
    $aCaseGroups = $oTasks->getGroupsOfTask( $aCase['TAS_UID'], 1 );

    foreach ($aCaseGroups as $aCaseGroup) {
        $aCaseUsers = $oGroups->getUsersOfGroup( $aCaseGroup['GRP_UID'] );
        foreach ($aCaseUsers as $aCaseUser) {
            if ($aCaseUser['USR_UID'] != $sReassignFromUser) {
                $aCaseUserRecord = $oUser->load( $aCaseUser['USR_UID'] );
                $aUsersInvolved[] = array ('userUid' => $aCaseUser['USR_UID'],'userFullname' => $aCaseUserRecord['USR_FIRSTNAME'] . ' ' . $aCaseUserRecord['USR_LASTNAME']);
                // . ' (' . $aCaseUserRecord['USR_USERNAME']
            }
        }
    }

    $aCaseUsers = $oTasks->getUsersOfTask( $aCase['TAS_UID'], 1 );
    foreach ($aCaseUsers as $aCaseUser) {
        if ($aCaseUser['USR_UID'] != $sReassignFromUser) {
            $aCaseUserRecord = $oUser->load( $aCaseUser['USR_UID'] );
            $aUsersInvolved[] = array ('userUid' => $aCaseUser['USR_UID'],'userFullname' => $aCaseUserRecord['USR_FIRSTNAME'] . ' ' . $aCaseUserRecord['USR_LASTNAME']
            );
        }
    }
    $oTmp = $aUsersInvolved;
    $aCase['USERS'] = $oTmp;
    array_push( $aCasesList, $aCase );
}

$filedNames = Array ("APP_UID","APP_NUMBER","APP_UPDATE_DATE","DEL_PRIORITY","DEL_INDEX","TAS_UID","DEL_INIT_DATE","DEL_FINISH_DATE","USR_UID","APP_STATUS","DEL_TASK_DUE_DATE","APP_CURRENT_USER","APP_TITLE","APP_PRO_TITLE","APP_TAS_TITLE","APP_DEL_PREVIOUS_USER","USERS"
);

$aCasesList = array_merge( Array ($filedNames
), $aCasesList );
$rows = array ();
$i = $start;
for ($j = 0; $j < $limit; $j ++) {
    $i ++;
    if (isset( $aCasesList[$i] )) {
        $rows[] = $aCasesList[$i];
    }
}
$totalCount = count( $aCasesList ) - 1;
$result = array ();
$result['totalCount'] = $totalCount;

$index = $start;
$result['data'] = $rows;
//print the result in json format
print G::json_encode( $result );

