<?php

namespace ProcessMaker\BusinessModel\Light;

use PmDynaform;

class Tracker
{


    function __construct()
    {
        \Creole::registerDriver('dbarray', 'creole.contrib.DBArrayConnection');
    }

    /**
     * Authentication for case tracker
     *
     * @param int $case number case
     * @param int $pin code pin access for case track
     * @return array
     * @throws \Exception
     */
    public static function authentication($case, $pin)
    {
        $cases = new \Cases();
        $response = array();
        $uid = $cases->verifyCaseTracker( $case, $pin );
        switch ($uid) {
            //The case doesn't exist
            case - 1:
                throw (new \Exception(\G::LoadTranslation('ID_CASE_NOT_EXISTS')));
                break;
            //The pin is invalid
            case - 2:
                throw (new \Exception(\G::LoadTranslation('ID_PIN_INVALID')));
                break;
        }
        $response['process'] = $uid['PRO_UID'];
        $response['app_uid'] = $uid['APP_UID'];
        return $response;
    }

    /**
     * Access granted for administrator in case tracker
     *
     * @param $pro_uid
     * @param $status
     * @return bool
     */
    public function permissions ($pro_uid, $status)
    {
        $cases = new \Cases();
        $caseTracker = $cases->caseTrackerPermissions( $pro_uid );
        switch ($status) {
            case "map":
                $return = $caseTracker['CT_MAP_TYPE'];
                break;
            case "messages":
                $return = $caseTracker['CT_MESSAGE_HISTORY'];
                break;
            case "history":
                $return = $caseTracker['CT_DERIVATION_HISTORY'];
                break;
            case "objects":
                $return = $caseTracker['DYNADOC'];
                break;
            default:
                $return = false;
                break;
        }
        return $return;
    }


    public function history($idProcess, $appUid)
    {
        $oCase = new \Cases();
        $aFields = $oCase->loadCase( $appUid );

        $oProcess = new \Process();
        $aProcessFieds = $oProcess->load( $idProcess );
        $noShowTitle = 0;
        if (isset( $aProcessFieds['PRO_SHOW_MESSAGE'] )) {
            $noShowTitle = $aProcessFieds['PRO_SHOW_MESSAGE'];
        }

        if (isset( $aFields['TITLE'] )) {
            $aFields['APP_TITLE'] = $aFields['TITLE'];
        }
        if ($aFields['APP_PROC_CODE'] != '') {
            $aFields['APP_NUMBER'] = $aFields['APP_PROC_CODE'];
        }
        $aFields['CASE'] = \G::LoadTranslation( 'ID_CASE' );
        $aFields['TITLE'] = \G::LoadTranslation( 'ID_TITLE' );

        $c = \Cases::getTransferHistoryCriteria( $appUid );
        $dataset = \AppDelegationPeer::doSelectRS( $c );
        $dataset->setFetchmode( \ResultSet::FETCHMODE_ASSOC );
        $dataset->next();
        $history = array();
        while ($row = $dataset->getRow()) {
            $history[] = $row;
            $dataset->next();
        }
        $response = $this->parserHistory($history);
        return $response;
    }

    public function parserHistory ($data)
    {
        $structure = array(
            'PRO_UID' => 'processId',
            'TAS_UID' => 'taskId',
            'APP_UID' => 'caseId',
            'user' => array(
                'USR_NAME'      => 'name',
                'USR_FIRSTNAME' => 'firstName',
                'USR_LASTNAME'  => 'lastName'
            ),
            'DEL_DELEGATE_DATE'       => 'delegateDate',
            'DEL_INDEX'               => 'index',
            'DEL_INIT_DATE'           => 'initDate',
            'APP_ENABLE_ACTION_DATE'  => 'enableAction',
            'APP_DISABLE_ACTION_DATE' => 'disableAction',
            'TAS_TITLE'               => 'taskTitle',
            'DEL_FINISH_DATE'         => 'finishDate',
            'APP_TYPE'                => 'type'
        );

        $response = $this->replaceFields($data, $structure);
        return $response;
    }

    public function messages($idProcess, $appUid)
    {
        $oCase = new \Cases();
        $aFields = $oCase->loadCase( $appUid );

        $oProcess = new \Process();
        $aProcessFieds = $oProcess->load( $idProcess );
        $noShowTitle = 0;
        if (isset( $aProcessFieds['PRO_SHOW_MESSAGE'] )) {
            $noShowTitle = $aProcessFieds['PRO_SHOW_MESSAGE'];
        }

        if (isset( $aFields['TITLE'] )) {
            $aFields['APP_TITLE'] = $aFields['TITLE'];
        }
        if ($aFields['APP_PROC_CODE'] != '') {
            $aFields['APP_NUMBER'] = $aFields['APP_PROC_CODE'];
        }
        $aFields['CASE'] = \G::LoadTranslation( 'ID_CASE' );
        $aFields['TITLE'] = \G::LoadTranslation( 'ID_TITLE' );

        $c = \Cases::getHistoryMessagesTracker( $appUid );
        $response = array();
        if ($c->getDbName() == 'dbarray') {
            $rs = \ArrayBasePeer::doSelectRs( $c );
            $rs->setFetchmode( \ResultSet::FETCHMODE_ASSOC );
            $rs->next();
            $messages = array();
            while ($row = $rs->getRow()) {
                $messages[] = $row;
                $rs->next();
            }
            $response = $this->parserMessages($messages);
        }

        return $response;
    }

    public function parserMessages ($data)
    {
        $structure = array(
            'APP_UID'          => 'caseId',
            'APP_MSG_UID'      => 'messageId',
            'APP_MSG_TYPE'     => 'messageType',
            'APP_MSG_SUBJECT'  => 'messageSubject',
            'APP_MSG_FROM'     => 'messageFrom',
            'APP_MSG_TO'       => 'messageTo',
            'APP_MSG_BODY'     => 'messageBody',
            'APP_MSG_DATE'     => 'messageDate',
            'APP_MSG_CC'       => 'messageCc',
            'APP_MSG_BCC'      => 'messageBcc',
            'APP_MSG_TEMPLATE' => 'messageTemplate',
            'APP_MSG_STATUS'   => 'messageStatus'
        );

        $response = $this->replaceFields($data, $structure);
        return $response;
    }

    public function objects($idProcess, $appUid)
    {
        $oProcessMap = new \ProcessMap();

        $oCase = new \Cases();

        $oProcess = new \Process();
        $aProcessFieds = $oProcess->load( $idProcess );
        $noShowTitle = 0;
        if (isset( $aProcessFieds['PRO_SHOW_MESSAGE'] )) {
            $noShowTitle = $aProcessFieds['PRO_SHOW_MESSAGE'];
        }

        $aFields = $oCase->loadCase( $appUid );
        if (isset( $aFields['TITLE'] )) {
            $aFields['APP_TITLE'] = $aFields['TITLE'];
        }
        if ($aFields['APP_PROC_CODE'] != '') {
            $aFields['APP_NUMBER'] = $aFields['APP_PROC_CODE'];
        }
        $aFields['CASE'] = \G::LoadTranslation( 'ID_CASE' );
        $aFields['TITLE'] = \G::LoadTranslation( 'ID_TITLE' );

        $c = $oProcessMap->getCaseTrackerObjectsCriteria( $idProcess );
        $response = array();
        if ($c->getDbName() == 'dbarray') {
            $rs = \ArrayBasePeer::doSelectRs( $c );
            $rs->setFetchmode( \ResultSet::FETCHMODE_ASSOC );
            $rs->next();
            $objects = array();
            while ($row = $rs->getRow()) {
                $objects[] = $row;
                $rs->next();
            }
            $response = $this->parserObjects($objects);
        }
        return $response;
    }

    public function parserObjects ($data)
    {
        $structure = array(
            //'CTO_UID'       => 'objectId',
            'CTO_TITLE'     => 'objectTitle',
            'CTO_TYPE_OBJ'  => 'objectType',
            'CTO_UID_OBJ'   => 'objectId',
            'CTO_CONDITION' => 'condition',
            'CTO_POSITION'  => 'position'
        );

        $response = $this->replaceFields($data, $structure);
        return $response;
    }

    public function showObjects ($pro_uid, $app_uid, $obj_uid, $typeObject)
    {
        switch ($typeObject) {
            case 'DYNAFORM':

                $oCase = new \Cases();
                $Fields = $oCase->loadCase( $app_uid );
                $Fields['APP_DATA']['__DYNAFORM_OPTIONS']['PREVIOUS_STEP_LABEL'] = '';
                $Fields['APP_DATA']['__DYNAFORM_OPTIONS']['NEXT_STEP_LABEL'] = '';
                $Fields['APP_DATA']['__DYNAFORM_OPTIONS']['NEXT_STEP'] = '#';
                $Fields['APP_DATA']['__DYNAFORM_OPTIONS']['NEXT_ACTION'] = 'alert("Sample"); return false;';
                $Fields['APP_DATA']['__DYNAFORM_OPTIONS']['PRINT_PREVIEW'] = '#';
                $Fields['APP_DATA']['__DYNAFORM_OPTIONS']['PRINT_PREVIEW_ACTION'] = 'tracker_PrintView?CTO_UID_OBJ=' . $obj_uid . '&CTO_TYPE_OBJ=PRINT_PREVIEW';

                $dynaForm = new \Dynaform();
                $arrayDynaFormData = $dynaForm->Load($obj_uid);

                if (isset($arrayDynaFormData["DYN_VERSION"]) && $arrayDynaFormData["DYN_VERSION"] == 2) {

                    $Fields["PRO_UID"] = $pro_uid;
                    $Fields["CURRENT_DYNAFORM"] = $obj_uid;

                    $pmDynaForm = new PmDynaform($Fields);

//                    if ($pmDynaForm->isResponsive()) {
//                        $pmDynaForm->printTracker();
//                    }
                    $response = $pmDynaForm;
                }
                break;
            case 'INPUT_DOCUMENT':

                $oCase = new \Cases();
                $c = $oCase->getAllUploadedDocumentsCriteriaTracker( $pro_uid, $app_uid, $obj_uid );

//                $response = array();
                if ($c->getDbName() == 'dbarray') {
                    $rs = \ArrayBasePeer::doSelectRs( $c );
                    $rs->setFetchmode( \ResultSet::FETCHMODE_ASSOC );
                    $rs->next();
                    $doc = array();
                    while ($row = $rs->getRow()) {
                        $doc[] = $row;
                        $rs->next();
                    }
                    //$response = $this->parserObjects($objects);
                }

                $response = $doc;
                break;

            case 'OUTPUT_DOCUMENT':

                $oCase = new \Cases();
                $c = $oCase->getAllGeneratedDocumentsCriteriaTracker( $pro_uid, $app_uid, $obj_uid );
                $response = $c;
                break;
        }
        return $response;
    }

    public function replaceFields ($data, $structure)
    {
        $response = array();
        foreach ($data as $field => $d) {
            if (is_array($d)) {
                $newData = array();
                foreach ($d as $field => $value) {
                    if (array_key_exists($field, $structure)) {
                        $newName           = $structure[$field];
                        $newData[$newName] = is_null($value) ? "":$value;
                    } else {
                        foreach ($structure as $name => $str) {
                            if (is_array($str) && array_key_exists($field, $str)) {
                                $newName                  = $str[$field];
                                $newData[$name][$newName] = is_null($value) ? "":$value;
                            }
                        }
                    }
                }
                if (count($newData) > 0)
                    $response[] = $newData;
            } else {
                if (array_key_exists($field, $structure)) {
                    $newName           = $structure[$field];
                    $response[$newName] = is_null($d) ? "":$d;
                } else {
                    foreach ($structure as $name => $str) {
                        if (is_array($str) && array_key_exists($field, $str)) {
                            $newName                  = $str[$field];
                            $response[$name][$newName] = is_null($d) ? "":$d;
                        }
                    }
                }
            }

        }
        return $response;
    }
}