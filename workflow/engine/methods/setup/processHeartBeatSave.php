<?php

if ($RBAC->userCanAccess( 'PM_SETUP' ) != 1 && $RBAC->userCanAccess( 'PM_SETUP_ADVANCE' ) != 1) {
    G::SendTemporalMessage( 'ID_USER_HAVENT_RIGHTS_PAGE', 'error', 'labels' );
    //G::header('location: ../login/login');
    die();
}
try {

    G::LoadClass( 'serverConfiguration' );
    $oServerConf = & serverConf::getSingleton();
    /*you can use SYS_TEMP or SYS_SYS ON HEAR_BEAT_CONF to save for each workspace*/
    $sflag = $_POST['HB_OPTION'];
    $oServerConf->unsetHeartbeatProperty( 'HB_BEAT_TYPE', 'HEART_BEAT_CONF' );
    if ($sflag) {
        $oServerConf->setHeartbeatProperty( 'HB_OPTION', 1, 'HEART_BEAT_CONF' );
        $oServerConf->unsetHeartbeatProperty( 'HB_NEXT_BEAT_DATE', 'HEART_BEAT_CONF' );
        echo "active";
    } else {
        $oServerConf->setHeartbeatProperty( 'HB_OPTION', 0, 'HEART_BEAT_CONF' );
        $oServerConf->unsetHeartbeatProperty( 'HB_NEXT_BEAT_DATE', 'HEART_BEAT_CONF' );
        $oServerConf->setHeartbeatProperty( 'HB_BEAT_TYPE', 'endbeat', 'HEART_BEAT_CONF' );
        echo "inactive";

    }
    //$oServerConf->setHeartbeatProperty('HB_OPTION',$_POST['HB_OPTION'],'HEART_BEAT_CONF');


} catch (Exception $e) {
    $G_PUBLISH = new Publisher();
    $aMessage['MESSAGE'] = $e->getMessage();
    $G_PUBLISH->AddContent( 'xmlform', 'xmlform', 'login/showMessage', '', $aMessage );
    G::RenderPage( 'publishBlank', 'blank' );
}

