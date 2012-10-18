<?php
try {
    G::LoadClass( 'processMap' );
    $oProcessMap = new processMap( new DBConnection() );

    //array_shift($rows);
    if ($_GET['type'] == 2)     //Loading sub process details
{
        $rows = $oProcessMap->subProcessExtProperties( $_GET['pid'], $_GET['tid'], '', '0' );
        $tmpData = G::json_encode( $rows );
        $tmpData = str_replace( "\\/", "/", '{success:true,data:' . $tmpData . '}' ); // unescape the slashes


        $result = $tmpData;
        print $result;
    } else {
        $rows = $oProcessMap->subProcessExtProperties( $_GET['pid'], $_GET['tid'], '', $_GET['type'] );
        $result['totalCount'] = count( $rows );
        $result['data'] = $rows;
        print G::json_encode( $result );
    }

} catch (Exception $e) {
    print G::json_encode( $e->getMessage() );
}

