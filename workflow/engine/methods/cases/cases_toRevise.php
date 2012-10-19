<?php

switch ($RBAC->userCanAccess( 'PM_SUPERVISOR' )) {
    case - 2:
        G::SendTemporalMessage( 'ID_USER_HAVENT_RIGHTS_SYSTEM', 'error', 'labels' );
        G::header( 'location: ../login/login' );
        die();
        break;
    case - 1:
        G::SendTemporalMessage( 'ID_USER_HAVENT_RIGHTS_PAGE', 'error', 'labels' );
        G::header( 'location: ../login/login' );
        die();
        break;
}

if (($RBAC_Response = $RBAC->userCanAccess( "PM_USERS" )) != 1) {
    return $RBAC_Response;
}

$G_MAIN_MENU = 'processmaker';
$G_SUB_MENU = 'cases';
$G_ID_MENU_SELECTED = 'CASES';
$G_ID_SUB_MENU_SELECTED = 'CASES_TO_REVISE';

$G_PUBLISH = new Publisher();
$G_PUBLISH->AddContent( 'view', 'cases/cases_toRevise' );
$G_PUBLISH->AddContent( 'smarty', 'cases/cases_toReviseIn', '', '', array () );

G::RenderPage( "publish-treeview" );

