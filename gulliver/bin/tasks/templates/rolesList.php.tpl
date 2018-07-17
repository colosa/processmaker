<?php
/**
 * roles_List.php
 *
 */


	$oCriteria = $RBAC->listAllRoles( '{rbacProjectName}');
  $G_MAIN_MENU            = '{projectName}';
  $G_SUB_MENU             = 'users';
  $G_ID_MENU_SELECTED     = 'USERS';
	$G_ID_SUB_MENU_SELECTED = 'ROLES';

  /*	
	$rs = RolesPeer::doSelectRs ( $oCriteria );
	$rs->setFetchmode(ResultSet::FETCHMODE_ASSOC);
  $rs->next();
  $row = $rs->getRow();
  while ( is_array ( $row ) ) {
      $rs->next();
      $row = $rs->getRow();
  }
  */
	$G_PUBLISH = new Publisher;
	$G_PUBLISH->AddContent('propeltable', 'paged-table', 'users/rolesList', $oCriteria);
	
	G::RenderPage('publish');
