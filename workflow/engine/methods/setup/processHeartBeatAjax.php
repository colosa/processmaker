<?php

switch ($_GET['action']) {
    case 'saveOption':
        try {
            G::LoadClass( 'serverConfiguration' );
            $oServerConf = & serverConf::getSingleton();

            /*you can use SYS_TEMP or SYS_SYS ON HEAR_BEAT_CONF to save for each workspace*/
            $oServerConf->unsetHeartbeatProperty( 'HB_BEAT_TYPE', 'HEART_BEAT_CONF' );
            if (isset( $_POST['acceptHB'] )) {
                $oServerConf->setHeartbeatProperty( 'HB_OPTION', 1, 'HEART_BEAT_CONF' );
                $oServerConf->unsetHeartbeatProperty( 'HB_NEXT_BEAT_DATE', 'HEART_BEAT_CONF' );
                $response->enable = true;
            } else {
                $oServerConf->setHeartbeatProperty( 'HB_OPTION', 0, 'HEART_BEAT_CONF' );
                $oServerConf->unsetHeartbeatProperty( 'HB_NEXT_BEAT_DATE', 'HEART_BEAT_CONF' );
                $oServerConf->setHeartbeatProperty( 'HB_BEAT_TYPE', 'endbeat', 'HEART_BEAT_CONF' );
                $response->enable = false;
            }
            $response->success = true;

        } catch (Exception $e) {
            $response->success = false;
            $response->msg = $e->getMessage();
        }
        echo G::json_encode( $response );
        break;
}

