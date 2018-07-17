 <?php 
  try {  	

    $form = $_POST['form'];
    $FolderUid = $form['FOLDER_UID'];

    //if exists the row in the database propel will update it, otherwise will insert.
    $tr = AppFolderPeer::retrieveByPK( $FolderUid );
    if ( ( is_object ( $tr ) &&  get_class ($tr) == 'AppFolder' ) ) {
      $tr->delete();
    }

    G::Header('location: appFolderList');   
  
  }
  catch ( Exception $e ) {
    $G_PUBLISH = new Publisher;
    $aMessage['MESSAGE'] = $e->getMessage();
    $G_PUBLISH->AddContent('xmlform', 'xmlform', 'login/showMessage', '', $aMessage );
    G::RenderPage( 'publish', 'blank' );
  }      
   