<?php

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
$oHeadPublisher->assign( 'USR_UID', '' );
$oHeadPublisher->assign( 'MODE', $_GET['MODE'] );
$oHeadPublisher->assign( 'MAX_FILES_SIZE', ' (' . $UPLOAD_MAX_SIZE . ') ' );
G::RenderPage( 'publish', 'extJs' );

