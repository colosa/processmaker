<?php
global $RBAC;

require_once 'classes/model/Users.php';
unset( $_SESSION['CURRENT_USER'] );
$oUser = new Users();
$aFields = $oUser->load( $_SESSION['USER_LOGGED'] );

if ($RBAC->userCanAccess( 'PM_EDITPERSONALINFO' ) == 1) { //he has permitions for edit his profile
    $canEdit = false;
} else { //he has not permitions for edit his profile, so just view mode will be displayed
    $canEdit = true;
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

$oHeadPublisher = & headPublisher::getSingleton();
$oHeadPublisher->addExtJsScript( 'users/users', true ); //adding a javascript file .js
// $oHeadPublisher->addContent('users/users'); //adding a html file  .html.
$oHeadPublisher->assign( 'USR_UID', $aFields['USR_UID'] );
$oHeadPublisher->assign( 'ROLE', $aFields['USR_ROLE']);
$oHeadPublisher->assign( 'infoMode', true );
$oHeadPublisher->assign( 'EDITPROFILE', 1);
$oHeadPublisher->assign( 'canEdit', $canEdit );
$oHeadPublisher->assign( 'MAX_FILES_SIZE', ' (' . $UPLOAD_MAX_SIZE . ') ' );
$oHeadPublisher->assign( 'MODE', '' );
G::RenderPage( 'publish', 'extJs' );

