<?php
try {
    G::LoadClass( 'processMap' );
    $oProcessMap = new processMap( new DBConnection() );

    if (isset( $_GET['pid'] ) && ! isset( $_GET['type'] )) {
        $rows = $oProcessMap->getExtReportTables( $_GET['pid'] );
    }

    else if (isset( $_GET['pid'] ) && $_GET['type'] == 'NORMAL') {
        $aTheFields = array ();
        $aTheFields = getDynaformsVars( $_GET['pid'], false );
        foreach ($aTheFields as $aField) {
            $rows[] = array ('FIELD_UID' => $aField['sName'] . '-' . $aField['sType'],'FIELD_NAME' => $aField['sName']
            );
        }
    }

    else if (isset( $_GET['pid'] ) && $_GET['type'] == 'GRID') {
        $aTheFields = array ();
        $aTheFields = getGridsVars( $_GET['pid'] );
        foreach ($aTheFields as $aField) {
            $rows[] = array ('FIELD_UID' => $aField['sName'] . '-' . $aField['sXmlForm'],'FIELD_NAME' => $aField['sName']
            );
        }
    }
    if (isset( $_GET['tid'] )) {
        require_once 'classes/model/ReportTable.php';
        $o = new ReportTable();
        $rows = $o->load( $_GET['tid'] );
    }

    $result['totalCount'] = count( $rows );
    $result['data'] = $rows;
    print G::json_encode( $result );

} catch (Exception $e) {
    print G::json_encode( $e->getMessage() );
}

