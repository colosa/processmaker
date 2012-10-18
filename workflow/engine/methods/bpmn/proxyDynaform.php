<?php
try {
    G::LoadClass( 'processMap' );
    $oProcessMap = new processMap( new DBConnection() );
    if (isset( $_GET['pid'] )) {
        //Getting Dynaform List
        $rows = $oProcessMap->getExtDynaformsList( $_GET['pid'] );
        array_shift( $rows );
    } else if (isset( $_GET['tabId'] )) {
        $oAdditionalTables = new AdditionalTables();
        $aData = $oAdditionalTables->load( $_GET['tabId'], true );
        $addTabName = $aData['ADD_TAB_NAME'];

        foreach ($aData['FIELDS'] as $iRow => $aRow) {
            if ($aRow['FLD_KEY'] == 1) {
                $rows[] = $aRow;
            }
        }
    }     //Getting Additional PM tables list created by user for combobox
    else {
        //Getting Dynaform List
        $rows = $oProcessMap->getExtAdditionalTablesList();
    }

    $result['totalCount'] = count( $rows );
    $result['data'] = $rows;
    print G::json_encode( $result );

} catch (Exception $e) {
    print G::json_encode( $e->getMessage() );
}

