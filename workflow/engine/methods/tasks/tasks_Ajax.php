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

    if (isset($aData["TAS_TITLE"])) {
        $aData["TAS_TITLE"] = str_replace("__ADD__", "+", $aData["TAS_TITLE"]);
    }
    if (isset($aData["TAS_DESCRIPTION"])) {
        $aData["TAS_DESCRIPTION"] = str_replace("__ADD__", "+", $aData["TAS_DESCRIPTION"]);
    }

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
            $aTaskInfo = $oTask->load($aData['TAS_UID']);

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
                //$aTaskInfo = $oTask->load($aData['TAS_UID']);
                $aData['TAS_SEND_LAST_EMAIL'] = is_null($aTaskInfo['TAS_SEND_LAST_EMAIL']) ? 'FALSE' : $aTaskInfo['TAS_SEND_LAST_EMAIL'];
            }

            //Additional configuration
            if (isset( $aData['TAS_DEF_MESSAGE_TYPE'] ) && isset( $aData['TAS_DEF_MESSAGE_TEMPLATE'] )) {

                $oConf = new Configurations();
                $oConf->aConfig = array ('TAS_DEF_MESSAGE_TYPE' => $aData['TAS_DEF_MESSAGE_TYPE'],'TAS_DEF_MESSAGE_TEMPLATE' => $aData['TAS_DEF_MESSAGE_TEMPLATE']
                );

                $oConf->saveConfig( 'TAS_EXTRA_PROPERTIES', $aData['TAS_UID'], '', '' );

                unset( $aData['TAS_DEF_MESSAGE_TYPE'] );
                unset( $aData['TAS_DEF_MESSAGE_TEMPLATE'] );
            }

            //Validating TAS_ASSIGN_VARIABLE value
            $sw = false;
            if (!isset($aData['TAS_ASSIGN_TYPE'])) {
                $sw = true;
                if (isset($aTaskInfo['TAS_ASSIGN_TYPE'])) {
                    switch($aTaskInfo['TAS_ASSIGN_TYPE']) {
                        case 'SELF_SERVICE':
                        case 'SELF_SERVICE_EVALUATE':
                            $aData['TAS_ASSIGN_TYPE'] = ($aTaskInfo['TAS_GROUP_VARIABLE'] == '') ? 'SELF_SERVICE':'SELF_SERVICE_EVALUATE';
                            $aData['TAS_GROUP_VARIABLE'] = $aTaskInfo['TAS_GROUP_VARIABLE'];
                            break;
                        default:
                            $aData['TAS_ASSIGN_TYPE'] = $aTaskInfo['TAS_ASSIGN_TYPE'];
                            break;
                    }
                } else {
                    $derivateType = $oTask->kgetassigType($_SESSION['PROCESS'],$aData['TAS_UID']);
                    if (is_null($derivateType)){
                        $aData['TAS_ASSIGN_TYPE'] = 'BALANCED';
                    } else {
                        $aData['TAS_ASSIGN_TYPE'] = $derivateType['TAS_ASSIGN_TYPE'];
                    }
                }
            }
            switch($aData['TAS_ASSIGN_TYPE']) {
                case 'SELF_SERVICE':
                case 'SELF_SERVICE_EVALUATE':
                    if ($aData['TAS_ASSIGN_TYPE'] == 'SELF_SERVICE_EVALUATE') {
                        $aData['TAS_ASSIGN_TYPE'] = 'SELF_SERVICE';
                        if(trim($aData['TAS_GROUP_VARIABLE']) == '') {
                           $aData['TAS_GROUP_VARIABLE'] = '@@SYS_GROUP_TO_BE_ASSIGNED';
                        }
                    } else {
                        $aData['TAS_GROUP_VARIABLE'] = '';
                    }
                    break;
                default:
                    if (isset($aTaskInfo['TAS_ASSIGN_TYPE']) && $sw) {
                        $aData['TAS_ASSIGN_TYPE'] = $aTaskInfo['TAS_ASSIGN_TYPE'];
                    }
                    $aData['TAS_GROUP_VARIABLE'] = '';
                    break;
            }
                       
            $result = $oTask->update( $aData );
            $oTaskNewPattern = new Task();
            $taskInfo=$oTaskNewPattern->load($aData['TAS_UID']);
            $titleTask=$taskInfo['TAS_TITLE'];
            $taskProperties='';
            foreach ($aData as $key => $value){
                if ($value!='') {
                    $taskProperties.=$key.' -> '.$value.' ';
                }
            }
            G::auditLog("SaveTaskProperties","Task Properties DETAILS : ".$taskProperties);
            $response["status"] = "OK";

            if ($result == 3) {
                $response["status"] = "CRONCL";
            }

            echo G::json_encode( $response );
            break;
    }
} catch (Exception $oException) {
    $token = strtotime("now");
    PMException::registerErrorLog($oException, $token);
    G::outRes($oException->getMessage());
    die;
}

