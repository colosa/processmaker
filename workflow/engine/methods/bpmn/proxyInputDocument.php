<?php
try {
    G::LoadClass( 'processMap' );
    $oProcessMap = new processMap( new DBConnection() );
    if (isset( $_GET['pid'] )) {
        $rows = $oProcessMap->getExtInputDocumentsCriteria( $_GET['pid'] );
        array_shift( $rows );
    }

    if (isset( $_GET['INP_DOC_UID'] )) {
        require_once 'classes/model/InputDocument.php';
        $oInputDocument = new InputDocument();
        $rows = $oInputDocument->load( $_GET['INP_DOC_UID'] );
    }
    //    $result['totalCount'] = count($rows);
    //    $result['data'] = $rows;
    //    print G::json_encode( $result) ;
    $tmpData = G::json_encode( $rows );
    $tmpData = str_replace( "\\/", "/", '{success:true,data:' . $tmpData . '}' ); // unescape the slashes


    $result = $tmpData;
    echo $result;
} catch (Exception $e) {
    print G::json_encode( $e->getMessage() );
}
