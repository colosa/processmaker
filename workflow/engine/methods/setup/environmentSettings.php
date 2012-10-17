<?php
G::loadClass( 'configuration' );
$c = new Configurations();
$oHeadPublisher = & headPublisher::getSingleton();
$oHeadPublisher->addExtJsScript( 'setup/environmentSettings', true );
//$conf->aConfig['startCaseHideProcessInf']
$oHeadPublisher->assign( 'FORMATS', $c->getFormats() );
G::RenderPage( 'publish', 'extJs' );

