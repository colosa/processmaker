<?php
global $RBAC;
$RBAC->requirePermissions( 'PM_SETUP' );

$c = new Configurations();
$oHeadPublisher = & headPublisher::getSingleton();
$oHeadPublisher->addExtJsScript( 'setup/environmentSettings', true );
//$conf->aConfig['startCaseHideProcessInf']
$oHeadPublisher->assign( 'FORMATS', $c->getFormats() );
G::RenderPage( 'publish', 'extJs' );

