<?php

try {
    if (isset( $_REQUEST['status'] )) {
        $oServerConf = & ServerConf::getSingleton();
        /*you can use SYS_TEMP or SYS_SYS ON HEAR_BEAT_CONF to save for each workspace*/
        if ($_REQUEST['status']) {
            echo "ACTIVE (Thanks!)";
            $oServerConf->setHeartbeatProperty( 'HB_OPTION', 1, 'HEART_BEAT_CONF' );
            $oServerConf->unsetHeartbeatProperty( 'HB_NEXT_BEAT_DATE', 'HEART_BEAT_CONF' );
        } else {
            echo "INACTIVE";
            $oServerConf->setHeartbeatProperty( 'HB_OPTION', 0, 'HEART_BEAT_CONF' );
            $oServerConf->unsetHeartbeatProperty( 'HB_NEXT_BEAT_DATE', 'HEART_BEAT_CONF' );
        }
    } else {
        echo "Nothing to do";
    }
} catch (Exception $e) {
    $token = strtotime("now");
    PMException::registerErrorLog($e, $token);
    G::outRes( G::LoadTranslation("ID_EXCEPTION_LOG_INTERFAZ", array($token)) );
}

