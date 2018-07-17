<?php

  //to do: improve the way to pass two or more parameters in the paged-table ( link )
    
  $aux = explode ( '|', $_GET['id'] );
  $index=0;
  $FolderUid = str_replace ( '"', '', $aux[$index++] );

  //if exists the row in the database propel will update it, otherwise will insert.
  $tr = AppFolderPeer::retrieveByPK( $FolderUid  );
  
  if ( ( is_object ( $tr ) &&  get_class ($tr) == 'AppFolder' ) ) { 
     $fields['FOLDER_UID'] = $tr->getFolderUid();
     $fields['LABEL_FOLDER_UID'] = $tr->getFolderUid();
     $fields['FOLDER_PARENT_UID'] = $tr->getFolderParentUid();
     $fields['LABEL_FOLDER_PARENT_UID'] = $tr->getFolderParentUid();
     $fields['FOLDER_NAME'] = $tr->getFolderName();
     $fields['LABEL_FOLDER_NAME'] = $tr->getFolderName();
     $fields['FOLDER_CREATE_DATE'] = $tr->getFolderCreateDate();
     $fields['LABEL_FOLDER_CREATE_DATE'] = $tr->getFolderCreateDate();
     $fields['FOLDER_UPDATE_DATE'] = $tr->getFolderUpdateDate();
     $fields['LABEL_FOLDER_UPDATE_DATE'] = $tr->getFolderUpdateDate();
  }
  else
    $fields = array();  
  
  $G_MAIN_MENU = 'workflow';
  $G_SUB_MENU = 'appFolder';
  $G_ID_MENU_SELECTED = '';
  $G_ID_SUB_MENU_SELECTED = '';


  $G_PUBLISH = new Publisher;
  $G_PUBLISH->AddContent('xmlform', 'xmlform', 'appFolder/appFolderDelete', '', $fields, 'appFolderDeleteExec' );
  G::RenderPage('publish');   
?>