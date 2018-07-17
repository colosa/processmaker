<?php
    
  $aux = explode ( '|', isset($_GET['id']) ? $_GET['id'] : '' );
  $FolderUid = str_replace ( '"', '', $aux[0] );
  
  //if exists the row in the database propel will update it, otherwise will insert.
  $tr = AppFolderPeer::retrieveByPK( $FolderUid  );
  
  if ( ( is_object ( $tr ) &&  get_class ($tr) == 'AppFolder' ) ) { 
     $fields['FOLDER_UID'] = $tr->getFolderUid();
     $fields['FOLDER_PARENT_UID'] = $tr->getFolderParentUid();
     $fields['FOLDER_NAME'] = $tr->getFolderName();
     $fields['FOLDER_CREATE_DATE'] = $tr->getFolderCreateDate();
     $fields['FOLDER_UPDATE_DATE'] = $tr->getFolderUpdateDate();
  }
  else
    $fields = array();  
  
  $G_MAIN_MENU = 'workflow';
  $G_SUB_MENU = 'appFolder';
  $G_ID_MENU_SELECTED = '';
  $G_ID_SUB_MENU_SELECTED = '';


  $G_PUBLISH = new Publisher;
  $G_PUBLISH->AddContent('xmlform', 'xmlform', 'appFolder/appFolderEdit', '', $fields, 'appFolderSave' );
  G::RenderPage('publish');   
?>