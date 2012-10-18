<?php
try {
    G::LoadClass( 'processMap' );
    $oProcessMap = new processMap( new DBConnection() );
    if (isset( $_GET['pid'] )) {
        $rows = $oProcessMap->getExtOutputDocumentsCriteria( $_GET['pid'] );
        array_shift( $rows );
    }
    //   else
    //   {
    //       $rows        = $oProcessMap->getExtInputDocumentsCriteria($_GET['pid']);
    //   }


    if (isset( $_GET['tid'] )) {
        require_once 'classes/model/OutputDocument.php';
        $oOutputDocument = new OutputDocument();
        $rows = $oOutputDocument->load( $_GET['tid'] );
    }

    $tmpData = G::json_encode( $rows );
    $tmpData = str_replace( "\\/", "/", '{success:true,data:' . $tmpData . '}' ); // unescape the slashes


    $result = $tmpData;
    echo $result;

    /*   $result['totalCount'] = count($rows);
    $result['data'] = $rows;
    print G::json_encode( $result) ;*/

} catch (Exception $e) {
    print G::json_encode( $e->getMessage() );
}

