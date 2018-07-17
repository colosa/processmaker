<?php

use ProcessMaker\Core\Installer;

global $RBAC;

$RBAC->allows(basename(__FILE__), basename(__FILE__));

if (isset( $_POST['form']['NW_TITLE'] )) {
    $action = (isset( $_POST['form']['ACTION'] )) ? trim( $_POST['form']['ACTION'] ) : 'test';
    $name = trim( $_POST['form']['NW_TITLE'] );
    $inst = new Installer();

    $isset = $inst->isset_site( $name );

    $new = ((! $isset)) ? true : false;
    $user = (isset( $_POST['form']['NW_USERNAME'] )) ? trim( $_POST['form']['NW_USERNAME'] ) : 'admin';
    $pass = (isset( $_POST['form']['NW_PASSWORD'] )) ? $_POST['form']['NW_PASSWORD'] : 'admin';
    $pass1 = (isset( $_POST['form']['NW_PASSWORD2'] )) ? $_POST['form']['NW_PASSWORD2'] : 'admin';

    $ao_db_drop = (isset( $_POST['form']['AO_DB_DROP'] )) ? true : false;

    $ao_db_wf = (isset( $_POST['form']['AO_DB_WF'] )) ? $_POST['form']['AO_DB_WF'] : false;
    $ao_db_rb = (isset( $_POST['form']['AO_DB_WF'] )) ? $_POST['form']['AO_DB_WF'] : false;
    $ao_db_rp = (isset( $_POST['form']['AO_DB_WF'] )) ? $_POST['form']['AO_DB_WF'] : false;

    $result = $inst->create_site( Array ('isset' => true,'name' => $name,'admin' => Array ('username' => $user,'password' => $pass
    ),'advanced' => Array ('ao_db_drop' => $ao_db_drop,'ao_db_wf' => $ao_db_wf,'ao_db_rb' => $ao_db_rb,'ao_db_rp' => $ao_db_rp
    )
    ), ($action === 'create') ? true : false );
    $result['result']['admin']['password'] = ($pass === $pass1) ? true : false;
    $result['result']['action'] = $action;
    //$json = new Services_JSON();
    /*$ec;
    $ec->created=($new)?true:false;
    $ec->name=$name;
    $ec->message=($new)?"Workspace created":"Workspace already exists or Name invalid";*/
    echo Bootstrap::json_encode( $result );
} else {
    global $RBAC;
    switch ($RBAC->userCanAccess( 'PM_SETUP_ADVANCE' )) {
        case - 2:
            G::SendTemporalMessage( 'ID_USER_HAVENT_RIGHTS_SYSTEM', 'error', 'labels' );
            G::header( 'location: ../login/login' );
            die();
            break;
        case - 3:
        case - 1:
            G::SendTemporalMessage( 'ID_USER_HAVENT_RIGHTS_PAGE', 'error', 'labels' );
            G::header( 'location: ../login/login' );
            die();
            break;
    }
    $G_PUBLISH = new Publisher();

    $c = new Configurations();
    $configPage = $c->getConfiguration( 'usersList', 'pageSize', '', $_SESSION['USER_LOGGED'] );
    $Config['pageSize'] = isset( $configPage['pageSize'] ) ? $configPage['pageSize'] : 20;

    $oHeadPublisher = & headPublisher::getSingleton();
    $oHeadPublisher->addExtJsScript( 'setup/newSite', false ); //adding a javascript file .js
    $oHeadPublisher->addContent( 'setup/newSite' ); //adding a html file  .html.
    //  $oHeadPublisher->assign('CONFIG', $Config);
    //  $oHeadPublisher->assign('FORMATS',$c->getFormats());
    $oHeadPublisher->assign( "SYS_LANG", SYS_LANG );
    $oHeadPublisher->assign( "SYS_SKIN", SYS_SKIN );

    G::RenderPage( 'publish', 'extJs' );

}

