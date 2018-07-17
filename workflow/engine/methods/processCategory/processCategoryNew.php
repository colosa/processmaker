<?php

if ($RBAC->userCanAccess( 'PM_SETUP' ) != 1 && $RBAC->userCanAccess( 'PM_SETUP_ADVANCE' ) != 1) {
    G::SendTemporalMessage( 'ID_USER_HAVENT_RIGHTS_PAGE', 'error', 'labels' );
    //G::header('location: ../login/login');
    die();
}

$fields['CATEGORY_UID'] = G::GenerateUniqueID();
;

$fields['CATEGORY_PARENT'] = '';
$fields['CATEGORY_NAME'] = '';
$fields['CATEGORY_ICON'] = '';

$G_MAIN_MENU = 'workflow';
$G_SUB_MENU = 'processCategory';
$G_ID_MENU_SELECTED = '';
$G_ID_SUB_MENU_SELECTED = '';

$G_PUBLISH = new Publisher();
$G_PUBLISH->AddContent( 'xmlform', 'xmlform', 'processCategory/processCategoryNew', '', $fields, 'processCategorySave' );
G::RenderPage( 'publishBlank', 'blank' );
?>
