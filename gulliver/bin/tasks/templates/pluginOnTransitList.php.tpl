<?php
/**
 * OnTransit.php for plugin {className}
 *
 *
 */
  /* Permissions */
  //if (($RBAC_Response = $RBAC->userCanAccess("PM_CASES"))!=1) return $RBAC_Response;

  /* Includes */

  /* GET , POST & $_SESSION Vars */
  $conf = new Configurations();

  $sTypeList = 'to_do';

  $sUIDUserLogged = $_SESSION['USER_LOGGED'];

  /* Menues */
  $G_MAIN_MENU            = 'processmaker';
  $G_SUB_MENU             = '{className}/menu{className}';
  $G_ID_MENU_SELECTED     = '{menuId}';
  $G_ID_SUB_MENU_SELECTED = '{menuId}';

  $oCases = new Cases();
  list($Criteria,$xmlfile) = $oCases->getConditionCasesList( $sTypeList, $sUIDUserLogged);
  $xmlfile = '{className}/{className}OnTransitList';
  /* Render page */

  $G_PUBLISH = new Publisher;
  $G_PUBLISH->AddContent( 'propeltable', '{className}/paged-table', '{className}/{className}OnTransitList', $Criteria );
  G::RenderPage( "publish" );
