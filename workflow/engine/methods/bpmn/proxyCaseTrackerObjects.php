<?php
G::LoadClass( 'processMap' );
$oProcessMap = new processMap( new DBConnection() );

if (isset( $_GET['pid'] )) {
    $rows = $oProcessMap->getExtCaseTrackerObjectsCriteria( $_GET['pid'] );
}

if (isset( $_GET['tid'] )) {
    $rows = $oProcessMap->getAvailableExtCaseTrackerObjects( $_GET['tid'] );
}
array_shift( $rows );
$result['totalCount'] = count( $rows );
$result['data'] = $rows;
print G::json_encode( $result );

