<?php

try {

    $G_MAIN_MENU = 'processmaker';
    $G_SUB_MENU = '';
    $G_ID_MENU_SELECTED = 'FOLDERS';
    $G_ID_SUB_MENU_SELECTED = '';

    $G_PUBLISH = new Publisher();

    if ((isset( $_POST['form']['FOLDER_UID'] )) && (isset( $_POST['form']['MOVE_FOLDER_PATH'] ))) {
        $oAppDocument = new AppDocument();

        //Move files to another FOLDER_UID'
        $folderUid = $_POST['form']['FOLDER_UID'];
        $filesArrayAux = explode( ";", $_POST['form']['MOVE_FOLDER_PATH'] );
        $filesArray = array ();
        foreach ($filesArrayAux as $value) {
            if ($value != "") {
                $valueAux = explode( "|", $value );
                $filesArray[$valueAux[1]] = $valueAux[0];
            }
        }
        foreach ($filesArray as $keyDoc => $sw) {
            if ($sw == "true") {
                $keyDocArray = explode( "_", $keyDoc );
                $aFields = array ('APP_DOC_UID' => $keyDocArray[0],'DOC_VERSION' => $keyDocArray[1],'FOLDER_UID' => $folderUid
                );
                $oAppDocument->update( $aFields );
            }
        }
    }

    //$rootFolder='5320083284b210ceb511e43070218744';
    $rootFolder = '0';
    //$rootFolder='4977070264b54bf093aef68069996372';


    $G_PUBLISH->AddContent( 'view', 'appFolder/appFolderTree' );
    $G_PUBLISH->AddContent( 'smarty', 'appFolder/appFolderFileList', '', '', array () );
    G::RenderPage( "publish-treeview", 'blank' );

} catch (Exception $e) {
    $G_PUBLISH = new Publisher();
    $aMessage['MESSAGE'] = $e->getMessage();
    $G_PUBLISH->AddContent( 'xmlform', 'xmlform', 'login/showMessage', '', $aMessage );
    G::RenderPage( 'publish', 'blank' );
}
?>
<script>


  openPMFolder('<?php echo $rootFolder ?>','<?php echo $rootFolder ?>');
 </script>
