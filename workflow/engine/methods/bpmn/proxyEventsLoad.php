<?php
try {
    G::LoadClass( 'processMap' );
    $oProcessMap = new processMap( new DBConnection() );
    if (isset( $_GET['startInterId'] )) {
        //Getting available Steps Criteria that have been not selected for a particular task
        require_once "classes/model/Event.php";
        $oEvent = new Event();
        $aRows = $oEvent->load( $_GET['startInterId'] );
        //$sch_uid = $oData['EVN_ACTION'];
        $result = G::json_encode( $aRows );
        $result = str_replace( "\\/", "/", '{success:true,data:' . $result . '}' ); // unescape the slashes
        /*else
     {
           $result = '{failure:true}'; // unescape the slashes
     }*/
        echo $result;
    }
    //print G::json_encode( $result ) ;
} catch (Exception $e) {
    print G::json_encode( $e->getMessage() );
}

