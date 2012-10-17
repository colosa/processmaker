<?php
$ipaddress = $_SERVER['REMOTE_ADDR'];
try {
    $aux = explode( '|', $_GET['id'] );

    $index = 0;
    $ObjUid = str_replace( '"', '', $aux[$index ++] );
    if (isset( $_GET['v'] ))
        $versionReq = $_GET['v'];

        //downloading the file
    $localPath = PATH_DOCUMENT . 'input' . PATH_SEP;
    G::mk_dir( $localPath );
    $newfilename = G::GenerateUniqueId() . '.pm';

    $downloadUrl = PML_DOWNLOAD_URL . '?id=' . $ObjUid . (isset( $_GET['s'] ) ? '&s=' . $_GET['s'] : '');
    //print "<hr>$downloadUrl<hr>";die;


    G::LoadClass( 'processes' );
    $oProcess = new Processes();
    $oProcess->downloadFile( $downloadUrl, $localPath, $newfilename );

    //getting the ProUid from the file recently downloaded
    $oData = $oProcess->getProcessData( $localPath . $newfilename );
    if (is_null( $oData )) {
        throw new Exception( 'Error' );
    }
    $Fields['IMPORT_OPTION'] = 2;
    $Fields['PRO_FILENAME'] = $newfilename;
    $Fields['OBJ_UID'] = $ObjUid;
    $sProUid = $oData->process['PRO_UID'];
    $oData->process['PRO_UID_OLD'] = $sProUid;
    //print $sProUid;die;
    //if the process exists, we need to ask what kind of re-import the user wants,
    if ($oProcess->processExists( $sProUid )) {
        $G_MAIN_MENU = 'processmaker';
        $G_ID_MENU_SELECTED = 'PROCESSES';
        $G_PUBLISH = new Publisher();
        $G_PUBLISH->AddContent( 'xmlform', 'xmlform', 'processes/processes_ImportExisting', '', $Fields, 'downloadPML_ImportExisting' );
        G::RenderPage( 'publish', 'blank' );
        die();
    }

    //creating the process
    $oProcess->createProcessFromData( $oData, $localPath . $newfilename );

    //show the info after the imported process
    G::LoadClass( 'processes' );
    $oProcess = new Processes();
    $oProcess->ws_open_public();
    $processData = $oProcess->ws_processGetData( $ObjUid );

    $Fields['pro_title'] = $processData->title;
    $Fields['installSteps'] = nl2br( $processData->installSteps );
    $Fields['category'] = (isset( $processData->category ) ? $processData->category : '');
    $Fields['version'] = $processData->version;
    $G_MAIN_MENU = 'processmaker';
    $G_ID_MENU_SELECTED = 'PROCESSES';
    $G_PUBLISH = new Publisher();
    $Fields['PRO_UID'] = $sProUid;
    $processmapLink = "processes_Map?PRO_UID=$sProUid";
    $G_PUBLISH->AddContent( 'xmlform', 'xmlform', 'processes/processes_ImportSucessful', '', $Fields, $processmapLink );
    G::RenderPage( 'publish', 'blank' );
    die();

} catch (Exception $e) {
    $G_PUBLISH = new Publisher();
    $aMessage['MESSAGE'] = $e->getMessage();
    $G_PUBLISH->AddContent( 'xmlform', 'xmlform', 'login/showMessage', '', $aMessage );
    G::RenderPage( 'publish', 'blank' );
}
