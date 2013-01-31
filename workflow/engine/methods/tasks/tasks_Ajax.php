<?php
try {
    global $RBAC;

    switch ($RBAC->userCanAccess( 'PM_FACTORY' )) {
        case - 2:
            G::SendTemporalMessage( 'ID_USER_HAVENT_RIGHTS_SYSTEM', 'error', 'labels' );
            G::header( 'location: ../login/login' );
            die();
            break;
        case - 1:
            G::SendTemporalMessage( 'ID_USER_HAVENT_RIGHTS_PAGE', 'error', 'labels' );
            G::header( 'location: ../login/login' );
            die();
            break;
    }

    //$oJSON = new Services_JSON();
    $aData = get_object_vars( G::json_decode( $_POST['oData'] ));
    //$aData = get_object_vars( $oJSON->decode( $_POST['oData'] ) );

    if (isset( $_POST['function'] )) {
        $sAction = $_POST['function'];
    } else {
        $sAction = $_POST['functions'];
    }

    switch ($sAction) {
        case "saveTaskData":
            require_once ("classes/model/Task.php");

            $response = array ();

            $oTask = new Task();

            /**
             * routine to replace @amp@ by &
             * that why the char "&" can't be passed by XmlHttpRequest directly
             * @autor erik <erik@colosa.com>
             */

            foreach ($aData as $k => $v) {
                $aData[$k] = str_replace( '@amp@', '&', $v );
            }

            if (isset( $aData['SEND_EMAIL'] )) {
                $aData['TAS_SEND_LAST_EMAIL'] = $aData['SEND_EMAIL'] == 'TRUE' ? 'TRUE' : 'FALSE';
            } else {
                $aData['TAS_SEND_LAST_EMAIL'] = 'FALSE';
            }

            //Additional configuration
            if (isset( $aData['TAS_DEF_MESSAGE_TYPE'] ) && isset( $aData['TAS_DEF_MESSAGE_TEMPLATE'] )) {
                G::LoadClass( 'configuration' );

                $oConf = new Configurations();
                $oConf->aConfig = array ('TAS_DEF_MESSAGE_TYPE' => $aData['TAS_DEF_MESSAGE_TYPE'],'TAS_DEF_MESSAGE_TEMPLATE' => $aData['TAS_DEF_MESSAGE_TEMPLATE']
                );

                $oConf->saveConfig( 'TAS_EXTRA_PROPERTIES', $aData['TAS_UID'], '', '' );

                unset( $aData['TAS_DEF_MESSAGE_TYPE'] );
                unset( $aData['TAS_DEF_MESSAGE_TEMPLATE'] );
            }

            //Validating TAS_ASSIGN_VARIABLE value
            if (!isset($aData['TAS_ASSIGN_TYPE'])) {
                $aData['TAS_ASSIGN_TYPE'] = 'BALANCED';
            }
            if ($aData['TAS_ASSIGN_TYPE'] == 'SELF_SERVICE_EVALUATE') {
                $aData['TAS_ASSIGN_TYPE'] = 'SELF_SERVICE';
            } else {
                $aData['TAS_GROUP_VARIABLE'] = '';
            }

            $result = $oTask->update( $aData );

            $response["status"] = "OK";

            if ($result == 3) {
                $response["status"] = "CRONCL";
            }

            echo G::json_encode( $response );
            break;
    }
} catch (Exception $oException) {
    die( $oException->getMessage() );
}

