<?php
unset( $_SESSION['__currentTabDashboard'] );
if (isset( $_GET['action'] )) {
    $_SESSION['__currentTabDashboard'] = $_GET['action'];
}
$page = "";
if (isset( $_GET['action'] )) {
    $page = $_GET['action'];
}

$oHeadPublisher = & headPublisher::getSingleton();
global $RBAC;
switch ($page) {
    case "startCase":
        $oHeadPublisher->addExtJsScript( 'cases/casesStartCase', true ); //adding a javascript file .js
        $oHeadPublisher->addContent( 'cases/casesStartCase' ); //adding a html file  .html.
        G::LoadClass( 'configuration' );
        $c = new Configurations();
        $oHeadPublisher->assign( 'FORMATS', $c->getFormats() );
        break;
    case "documents":

        G::LoadClass( 'configuration' );
        $c = new Configurations();
        $configPage = $c->getConfiguration( 'documentsModule', 'pageSize', '', $_SESSION['USER_LOGGED'] );
        $configEnv = $c->getConfiguration( 'ENVIRONMENT_SETTINGS', '' );
        $Config['pageSize'] = isset( $configPage['pageSize'] ) ? $configPage['pageSize'] : 20;
        $oHeadPublisher->assign( 'CONFIG', $Config );
        $oHeadPublisher->assign( 'FORMATS', $c->getFormats() );

        $oHeadPublisher->assign( 'permitodelete', $RBAC->userCanAccess( 'PM_FOLDER_DELETE' ) );
        $oHeadPublisher->assign( 'permitoaddfile', $RBAC->userCanAccess( 'PM_FOLDERS_ADD_FILE' ) );
        $oHeadPublisher->assign( 'permitoaddfolder', $RBAC->userCanAccess( 'PM_FOLDERS_ADD_FOLDER' ) );

        $oHeadPublisher->addExtJsScript( 'cases/casesDocuments', false ); //adding a javascript file .js
        $oHeadPublisher->addContent( 'cases/casesDocuments' ); //adding a html file  .html.
        break;
    default:
        $oHeadPublisher->addExtJsScript( 'cases/casesStartPage', false ); //adding a javascript file .js
        $oHeadPublisher->addContent( 'cases/casesStartPage' ); //adding a html file  .html.
        break;

}
G::RenderPage( 'publish', 'extJs' );

