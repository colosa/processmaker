<?php

  $fields['FOLDER_UID'] = G::GenerateUniqueID();;

  $fields['FOLDER_PARENT_UID'] = '';
  $fields['FOLDER_NAME'] = '';
  $fields['FOLDER_CREATE_DATE'] = '';
  $fields['FOLDER_UPDATE_DATE'] = '';

  $G_MAIN_MENU = 'workflow';
  $G_SUB_MENU = 'appFolder';
  $G_ID_MENU_SELECTED = '';
  $G_ID_SUB_MENU_SELECTED = '';


  $G_PUBLISH = new Publisher;
  $G_PUBLISH->AddContent('xmlform', 'xmlform', 'appFolder/appFolderEdit', '', $fields, 'appFolderSave' );  
  G::RenderPage('publish');   
?>