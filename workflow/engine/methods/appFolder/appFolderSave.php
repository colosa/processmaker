<?php

try {
    $form = $_POST['form'];
    $FolderUid = $form['FOLDER_UID'];
    $FolderParentUid = $form['FOLDER_PARENT_UID'];
    $FolderName = $form['FOLDER_NAME'];
    $FolderCreateDate = 'now';
    $FolderUpdateDate = 'now';


    require_once ( "classes/model/AppFolder.php" );

    //if exists the row in the database propel will update it, otherwise will insert.
    $tr = AppFolderPeer::retrieveByPK($FolderUid);
    if (!( is_object($tr) && get_class($tr) == 'AppFolder' )) {
        $tr = new AppFolder();
    }
    $tr->setFolderUid($FolderUid);
    $tr->setFolderParentUid($FolderParentUid);
    $tr->setFolderName($FolderName);
    $tr->setFolderCreateDate($FolderCreateDate);
    $tr->setFolderUpdateDate($FolderUpdateDate);

    if ($tr->validate()) {
        // we save it, since we get no validation errors, or do whatever else you like.
        $res = $tr->save();
    } else {
        // Something went wrong. We can now get the validationFailures and handle them.
        $msg = '';
        $validationFailuresArray = $tr->getValidationFailures();
        foreach ($validationFailuresArray as $objValidationFailure) {
            $msg .= $objValidationFailure->getMessage() . "<br/>";
        }
        //return array ( 'codError' => -100, 'rowsAffected' => 0, 'message' => $msg );
    }
    //return array ( 'codError' => 0, 'rowsAffected' => $res, 'message' => '');
    //to do: uniform  coderror structures for all classes
    //if ( $res['codError'] < 0 ) {
    //  G::SendMessageText ( $res['message'] , 'error' );
    //}
    G::Header('location: appFolderList');
} catch (Exception $e) {
    $G_PUBLISH = new Publisher;
    $aMessage['MESSAGE'] = $e->getMessage();
    $G_PUBLISH->AddContent('xmlform', 'xmlform', 'login/showMessage', '', $aMessage);
    G::RenderPage('publish', 'blank');
}
 