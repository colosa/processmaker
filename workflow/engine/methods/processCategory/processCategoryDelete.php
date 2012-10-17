<?php
if ($RBAC->userCanAccess( 'PM_SETUP' ) != 1 && $RBAC->userCanAccess( 'PM_SETUP_ADVANCE' ) != 1) {
    G::SendTemporalMessage( 'ID_USER_HAVENT_RIGHTS_PAGE', 'error', 'labels' );
    //G::header('location: ../login/login');
    die();
}

//to do: improve the way to pass two or more parameters in the paged-table ( link )


$aux = explode( '|', $_GET['id'] );
$index = 0;
$CategoryUid = str_replace( '"', '', $aux[$index ++] );

require_once ("classes/model/ProcessCategory.php");
//if exists the row in the database propel will update it, otherwise will insert.
$tr = ProcessCategoryPeer::retrieveByPK( $CategoryUid );

if ((is_object( $tr ) && get_class( $tr ) == 'ProcessCategory')) {
    $fields['CATEGORY_UID'] = $tr->getCategoryUid();
    $fields['LABEL_CATEGORY_UID'] = $tr->getCategoryUid();
    $fields['CATEGORY_PARENT'] = $tr->getCategoryParent();
    $fields['LABEL_CATEGORY_PARENT'] = $tr->getCategoryParent();
    $fields['CATEGORY_NAME'] = $tr->getCategoryName();
    $fields['LABEL_CATEGORY_NAME'] = $tr->getCategoryName();
    $fields['CATEGORY_ICON'] = $tr->getCategoryIcon();
    $fields['LABEL_CATEGORY_ICON'] = $tr->getCategoryIcon();
} else
    $fields = array ();

$G_MAIN_MENU = 'workflow';
$G_SUB_MENU = 'processCategory';
$G_ID_MENU_SELECTED = '';
$G_ID_SUB_MENU_SELECTED = '';

$G_PUBLISH = new Publisher();
$G_PUBLISH->AddContent( 'xmlform', 'xmlform', 'processCategory/processCategoryDelete', '', $fields, 'processCategoryDeleteExec' );
G::RenderPage( 'publishBlank', 'blank' );
?>