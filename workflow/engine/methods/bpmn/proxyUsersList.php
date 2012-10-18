<?php
try {
    G::LoadClass( 'processMap' );
    $oProcessMap = new processMap( new DBConnection() );
    if (isset( $_GET['pid'] )) {
        $rows = $oProcessMap->usersExtList( $_GET['pid'], $_GET['tid'] );
        array_shift( $rows );
    } else {
        $rows = $oProcessMap->getAvailableExtUsersCriteria( $_GET['tid'] );
    }

    $result['totalCount'] = count( $rows );
    $result['data'] = $rows;
    print G::json_encode( $result );

} catch (Exception $e) {
    print G::json_encode( $e->getMessage() );
}

