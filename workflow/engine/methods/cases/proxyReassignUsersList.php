<?php
G::LoadClass( 'configuration' );
$callback = isset( $_POST['callback'] ) ? $_POST['callback'] : 'stcCallback1001';
$query = isset( $_POST['query'] ) ? $_POST['query'] : '';
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


function array_sort ($array, $on, $order = SORT_ASC, $query = '')
{
    $new_array = array ();
    $sortable_array = array ();

    if (count( $array ) > 0) {
        foreach ($array as $k => $v) {
            if (is_array( $v )) {
                foreach ($v as $k2 => $v2) {
                    if ($k2 == $on) {
                        $sortable_array[$k] = $v2;
                    }
                }
            } else {
                $sortable_array[$k] = $v;
            }
        }

        switch ($order) {
            case SORT_ASC:
                asort( $sortable_array );
                break;
            case SORT_DESC:
                arsort( $sortable_array );
                break;
        }

        foreach ($sortable_array as $k => $v) {
            if ($query == '') {
                $new_array[] = $array[$k];
            } else {
                if (preg_match( "/" . preg_quote($query, '/') . "/i", $array[$k]['userFullname'] )) {
                    $new_array[] = $array[$k];
                }
            }
        }
    }
    return $new_array;
}
//  $APP_UIDS          = explode(',', $_POST['APP_UID']);


$appUid = isset( $_POST['application'] ) ? $_POST['application'] : '';
//$processUid = isset($_POST['process'])     ? $_POST['process'] : '';
$TaskUid = isset( $_POST['task'] ) ? $_POST['task'] : '';
$sReassignFromUser = isset( $_POST['currentUser'] ) ? $_POST['currentUser'] : '';

G::LoadClass( 'case' );

$oCases = new Cases();
$oConf = new Configurations();

$aUsersInvolved = Array();

$ConfEnv = $oConf->getFormats();
G::LoadClass( 'tasks' );
$task = new Task();
$tasks = $task->load($_SESSION['TASK']);
$rows = $oCases->getUsersToReassign($TaskUid, $_SESSION['USER_LOGGED'], $tasks['PRO_UID']);
$flagSupervisors = false;
foreach ($rows as $row) {
    $sCaseUser = G::getFormatUserList( $ConfEnv['format'], $row );
    $aUsersInvolved[] = array ('userUid' => $row['USR_UID'], 'userFullname' => $sCaseUser);
}

//            $oTmp = new stdClass();
//            $oTmp->items = $aUsersInvolved;
$result = array ();
$aUsersInvolved = array_sort( $aUsersInvolved, 'userFullname', SORT_ASC, $query );
$result['data'] = $aUsersInvolved;
print G::json_encode( $result );

