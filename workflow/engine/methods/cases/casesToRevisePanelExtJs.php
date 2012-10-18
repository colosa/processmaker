<?php
//  $oHeadPublisher =& headPublisher::getSingleton();
//  $TRANSLATIONS = array_merge($TRANSLATIONS, $TRANSLATIONS2);

$delIndex = $_GET['DEL_INDEX'];
$appUid = $_GET['APP_UID'];
//  $oHeadPublisher->assign( 'TRANSLATIONS',   $TRANSLATIONS); //translations
$casesPanelUrl = 'casesToReviseTreeContent?APP_UID=' . $appUid . '&DEL_INDEX=' . $delIndex;
$oHeadPublisher->assign( 'casesPanelUrl', $casesPanelUrl ); //translations
$oHeadPublisher->assign( 'treeTitle', G::loadtranslation( 'ID_STEP_LIST' ) ); //translations
$oHeadPublisher->addExtJsScript( 'cases/casesToRevisePanel', false ); //adding a javascript file .js
$oHeadPublisher->addContent( 'cases/casesToRevisePanel' ); //adding a html file  .html.

G::RenderPage( 'publish', 'extJs' );

