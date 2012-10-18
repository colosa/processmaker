<?php
try {
    G::LoadClass( 'processMap' );
    $oProcessMap = new processMap( new DBConnection() );
    if (isset( $_GET['pid'] )) {
        //Getting available Steps Criteria that have been not selected for a particular task
        $rows = $oProcessMap->getExtAvailableBBCriteria( $_GET['pid'], $_GET['tid'] );
        array_shift( $rows );
    } else {
        //Getting all Steps Criteria that have been selected for a particular task
        $rows = $oProcessMap->getExtStepsCriteria( $_GET['tid'] );
        array_shift( $rows );
    }

    $result['totalCount'] = count( $rows );
    $result['data'] = $rows;
    print G::json_encode( $result );

} catch (Exception $e) {
    print G::json_encode( $e->getMessage() );
}

