<?php
try {
    G::LoadClass( 'processMap' );
    $oProcessMap = new processMap( new DBConnection() );

    if (isset( $_GET['pid'] )) {
        $rows = $oProcessMap->getExtCriteriaDBSList( $_GET['pid'] );
    }
    if (isset( $_GET['tid'] )) {
        require_once 'classes/model/DbSource.php';
        $o = new DbSource();
        $rows = $o->load( $_GET['tid'] );
    }

    $tmpData = G::json_encode( $rows );
    $tmpData = str_replace( "\\/", "/", '{success:true,data:' . $tmpData . '}' ); // unescape the slashes


    $result = $tmpData;
    echo $result;
} catch (Exception $e) {
    print G::json_encode( $e->getMessage() );
}

