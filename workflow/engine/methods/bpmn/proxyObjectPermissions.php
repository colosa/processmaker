<?php
G::LoadClass( 'processMap' );
$oProcessMap = new processMap( new DBConnection() );

if (isset( $_GET['pid'] ) && ! isset( $_GET['action'] )) {
    $rows = $oProcessMap->getExtObjectsPermissions( $_GET['pid'] );
}

if (isset( $_GET['pid'] ) && isset( $_GET['action'] )) {
    $rows = $oProcessMap->newExtObjectPermission( $_GET['pid'], $_GET['action'] );
    array_shift( $rows );
}
$result['totalCount'] = count( $rows );
$result['data'] = $rows;
print G::json_encode( $result );

