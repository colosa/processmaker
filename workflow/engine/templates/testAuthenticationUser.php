<?php

  $strPass = $_POST['form'][PASS];

  if ( $strPass == '' ) 
    return;

  $userId   = $_SESSION['CURRENT_USER'];

	$tpl = new TemplatePower( PATH_TPL . 'testAuthenticationSource.html' );
	$tpl->prepare();
  $tpl->assign( "STYLE_CSS" , STYLE_CSS );
  $tpl->assign( "title" , $G_TABLE->title );

  $curAuthSource = $_SESSION['CURRENT_AUTH_SOURCE'];
  $dbc = new DBConnection(DB_HOST, DB_RBAC_USER, DB_RBAC_PASS, DB_RBAC_NAME );

  //Class user
  $user = new RBAC_User;
  $user->SetTo ($dbc);
  $user->Load ($userId);

  //crear nueva authentication source
  $obj = new authenticationSource;
  $obj->SetTo( $dbc );
  $res = $obj->verifyPassword ( $userId, $user->Fields['USR_LDAP_DN'] , $strPass,  $user->Fields['USR_LDAP_SOURCE'] );

//print "<textarea rows=10 cols=60>"; print_r ($obj->vlog );
//print "</textarea >"; 

  foreach ( $obj->vlog as $line ) {
    if ( stristr ($line, 'error' ) !== false ) $line = "<font color='Red'>" . $line . '</font>';
    if ( stristr ($line, 'sucess' ) !== false ) $line = "<font color='Green'>" . $line . '</font>';

	  $tpl->newBlock( "lines" );
    $tpl->assign( "text" , $line );
  }
  $tpl->gotoBlock( "_ROOT" );
	$tpl->printToScreen();

?>
