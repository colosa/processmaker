<?php
use ProcessMaker\Core\System;

global$RBAC;

$access = $RBAC->userCanAccess( 'PM_SETUP_ADVANCE' );
if ($access != 1) {
    switch ($access) {
        case - 1:
            G::SendTemporalMessage( 'ID_USER_HAVENT_RIGHTS_PAGE', 'error', 'labels' );
            G::header( 'location: ../login/login' );
            die();
            break;
        case - 2:
            G::SendTemporalMessage( 'ID_USER_HAVENT_RIGHTS_SYSTEM', 'error', 'labels' );
            G::header( 'location: ../login/login' );
            die();
            break;
        default:
            G::SendTemporalMessage( 'ID_USER_HAVENT_RIGHTS_PAGE', 'error', 'labels' );
            G::header( 'location: ../login/login' );
            die();
            break;
    }
}
//calculating the max upload file size;
$POST_MAX_SIZE = ini_get( 'post_max_size' );
$mul = substr( $POST_MAX_SIZE, - 1 );
$mul = ($mul == 'M' ? 1048576 : ($mul == 'K' ? 1024 : ($mul == 'G' ? 1073741824 : 1)));
$postMaxSize = (int) $POST_MAX_SIZE * $mul;

$UPLOAD_MAX_SIZE = ini_get( 'upload_max_filesize' );
$mul = substr( $UPLOAD_MAX_SIZE, - 1 );
$mul = ($mul == 'M' ? 1048576 : ($mul == 'K' ? 1024 : ($mul == 'G' ? 1073741824 : 1)));
$uploadMaxSize = (int) $UPLOAD_MAX_SIZE * $mul;

if ($postMaxSize < $uploadMaxSize)
    $uploadMaxSize = $postMaxSize;

$G_MAIN_MENU = 'processmaker';
$G_SUB_MENU = 'setup';
$G_ID_MENU_SELECTED = 'SETUP';
$G_ID_SUB_MENU_SELECTED = 'UPGRADE';

$Fields['PM_VERSION'] = System::getVersion();
$Fields['MAX_FILE_SIZE'] = $uploadMaxSize . " (" . $UPLOAD_MAX_SIZE . ") ";

$G_PUBLISH = new Publisher();
$G_PUBLISH->AddContent( 'xmlform', 'xmlform', 'setup/upgrade', '', $Fields, 'upgrade_System' );
G::RenderPage( 'publishBlank', 'blank' );

