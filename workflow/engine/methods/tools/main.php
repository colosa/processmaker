<?php

$oHeadPublisher = & headPublisher::getSingleton();
$oHeadPublisher->addExtJsScript( 'tools/main', true );

//$oHeadPublisher->assign('_ENV_CURRENT_DATE', $conf->getSystemDate(date('Y-m-d')));
G::RenderPage( 'publish', 'extJs' );

