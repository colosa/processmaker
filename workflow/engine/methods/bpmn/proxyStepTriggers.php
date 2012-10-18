<?php
try {
    G::LoadClass( 'processMap' );
    $oProcessMap = new processMap( new DBConnection() );

    //$_GET['sid'] gets STEP_UID and sTYPE(i.e BEFORE and AFTER) in format STEP_UID-sTYPE
    if (isset( $_GET['stepid'] )) {
        $aStepTypeId = explode( '|', $_GET['stepid'] );
        $_SESSION['stepUID'] = $_GET['stepid'];
        //$aStepTypeId = explode('-','2517180104cd42c25cc39e4071099227-BEFORE');
        $sStep = $aStepTypeId[0];
        $sType = $aStepTypeId[1];
    }

    if (isset( $_GET['pid'] ) && isset( $_SESSION['stepUID'] )) {
        $aStepTypeId = explode( '|', $_SESSION['stepUID'] );
        $sStep = $aStepTypeId[0];
        $sType = $aStepTypeId[1];
        //Getting available Steps Criteria that have been not selected for a particular task
        $rows = $oProcessMap->getExtAvailableStepTriggersCriteria( $_GET['pid'], $sStep, $_GET['tid'], $sType );
    } else {
        //Getting all Steps Criteria that have been selected for a particular task
        $rows = $oProcessMap->getExtStepTriggersCriteria( $sStep, $_GET['tid'], $sType );
    }

    $result['totalCount'] = count( $rows );
    $result['data'] = $rows;
    print G::json_encode( $result );

} catch (Exception $e) {
    print G::json_encode( $e->getMessage() );
}

