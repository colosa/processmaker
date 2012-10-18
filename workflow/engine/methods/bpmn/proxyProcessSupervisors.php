<?php
try {
    G::LoadClass( 'processMap' );
    $oProcessMap = new processMap( new DBConnection() );

    $processUID = $_GET['pid'];
    $action = $_GET['action'];

    switch ($action) {
        case 'process_User':
            $rows = $oProcessMap->listExtProcessesUser( $processUID );
            break;
        case 'availableProcessesUser':
            $rows = $oProcessMap->listExtNoProcessesUser( $processUID );
            break;
        case 'supervisorDynaforms':
            $rows = $oProcessMap->getExtSupervisorDynaformsList( $processUID );
            break;
        case 'availableSupervisorDynaforms':
            $rows = $oProcessMap->getExtAvailableSupervisorDynaformsList( $processUID );
            break;
        case 'supervisorInputDoc':
            $rows = $oProcessMap->getExtSupervisorInputsList( $processUID );
            break;
        case 'availableSupervisorInputDoc':
            $rows = $oProcessMap->getExtAvailableSupervisorInputsList( $processUID );
            break;
    }

    $result['totalCount'] = count( $rows );
    $result['data'] = $rows;
    print G::json_encode( $result );

} catch (Exception $e) {
    print G::json_encode( $e->getMessage() );
}

