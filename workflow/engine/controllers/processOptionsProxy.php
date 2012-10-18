<?php

class processOptionsProxy extends HttpProxyController
{

    public function loadInputDocuments ($params)
    {
        G::LoadClass( 'processMap' );
        $oProcessMap = new processMap( new DBConnection() );

        $pro_uid = $params->PRO_UID;
        $start = isset( $params->start ) ? $params->start : 0;
        $limit = isset( $params->limit ) ? $params->limit : '';

        $rows = $oProcessMap->getExtInputDocumentsCriteria( $start, $limit, $pro_uid );
        $total = $oProcessMap->getAllInputDocumentCount();
        $aDocs = $oProcessMap->getAllInputDocsByTask( $pro_uid );
        array_shift( $rows );

        $this->PRO_UID = $pro_uid;
        $this->success = true;
        $this->idocs = $rows;
        $this->total_idocs = $total;
    }

    public function canDeleteInputDoc ($params)
    {
        G::LoadClass( 'processMap' );
        $oProcessMap = new processMap( new DBConnection() );
        $aRows = $oProcessMap->getAllInputDocsByTask( $params->PRO_UID );
        $response = isset( $aRows[$params->IDOC_UID] ) ? false : true;
        $this->success = $response;
    }

    public function deleteInputDoc ($params)
    {
        require_once 'classes/model/StepSupervisor.php';
        require_once 'classes/model/ObjectPermission.php';
        require_once 'classes/model/InputDocument.php';
        G::LoadClass( 'processMap' );

        $oStepSupervisor = new StepSupervisor();
        $fields2 = $oStepSupervisor->loadInfo( $params->IDOC_UID );
        $oStepSupervisor->remove( $fields2['STEP_UID'] );

        $oPermission = new ObjectPermission();
        $fields3 = $oPermission->loadInfo( $params->IDOC_UID );
        if (is_array( $fields3 )) {
            $oPermission->remove( $fields3['OP_UID'] );
        }

        $oInputDocument = new InputDocument();
        $fields = $oInputDocument->load( $params->IDOC_UID );

        $oInputDocument->remove( $params->IDOC_UID );

        $oStep = new Step();
        $oStep->removeStep( 'INPUT_DOCUMENT', $params->IDOC_UID );

        $oOP = new ObjectPermission();
        $oOP->removeByObject( 'INPUT', $params->IDOC_UID );

        //refresh dbarray with the last change in inputDocument
        $oMap = new processMap();
        $oCriteria = $oMap->getInputDocumentsCriteria( $params->PRO_UID );

        $this->success = true;
        $this->msg = G::LoadTranslation( 'ID_INPUT_DOC_SUCCESS_DELETE' );
    }

    public function saveInputDoc ($params)
    {
        require_once 'classes/model/InputDocument.php';
        G::LoadClass( 'processMap' );

        $aData = array ();
        $aData['PRO_UID'] = $params->PRO_UID;
        $aData['INP_DOC_UID'] = $params->INP_DOC_UID;
        $aData['INP_DOC_TITLE'] = $params->INP_DOC_TITLE;
        $aData['INP_DOC_FORM_NEEDED'] = $params->INP_DOC_FORM_NEEDED;
        if ($aData['INP_DOC_FORM_NEEDED'] != 'VIRTUAL') {
            $aData['INP_DOC_ORIGINAL'] = $params->INP_DOC_ORIGINAL;
        } else {
            $aData['INP_DOC_ORIGINAL'] = 'ORIGINAL';
        }
        $aData['INP_DOC_VERSIONING'] = $params->INP_DOC_VERSIONING;
        $aData['INP_DOC_DESCRIPTION'] = $params->INP_DOC_DESCRIPTION;
        $aData['INP_DOC_DESTINATION_PATH'] = $params->INP_DOC_DESTINATION_PATH;
        $aData['INP_DOC_TAGS'] = $params->INP_DOC_TAGS;

        $oInputDocument = new InputDocument();
        if ($aData['INP_DOC_UID'] == '') {
            unset( $aData['INP_DOC_UID'] );
            $oInputDocument->create( $aData );
            $this->msg = G::LoadTranslation( 'ID_INPUT_DOC_SUCCESS_NEW' );
        } else {
            $oInputDocument->update( $aData );
            $this->msg = G::LoadTranslation( 'ID_INPUT_DOC_SUCCESS_UPDATE' );
        }

        //refresh dbarray with the last change in inputDocument
        $oMap = new processMap();
        $oCriteria = $oMap->getInputDocumentsCriteria( $aData['PRO_UID'] );
        $this->success = true;
    }

    public function loadInputDoc ($params)
    {
        require_once 'classes/model/InputDocument.php';
        $oInputDocument = new InputDocument();
        $fields = $oInputDocument->load( $params->IDOC_UID );
        $this->success = true;
        $this->data = $fields;
    }
}

