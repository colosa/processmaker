<?php
$RBAC->requirePermissions('PM_CASES/strict');

$G_MAIN_MENU = 'processmaker';
$G_ID_MENU_SELECTED = 'CASES';

$_POST['qs'] = isset($_SERVER['QUERY_STRING'])
    && $_SERVER['QUERY_STRING'] != '' ? '?' . $_SERVER['QUERY_STRING'] : '';

$G_PUBLISH = new Publisher();
$G_PUBLISH->AddContent('view', 'cases/cases_Load');
$oHeadPublisher = & headPublisher::getSingleton();
$oHeadPublisher->addScriptFile('/jscore/src/PM.js');
$oHeadPublisher->addScriptFile('/jscore/src/Sessions.js');
G::RenderPage('publish');

