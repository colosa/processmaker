<?php

$sTasUid        = $_REQUEST['tas_uid'];
$sDynUid        = $_REQUEST['dyn_uid'];
$sStatus        = $_REQUEST['status'];
$sProUid        = $_REQUEST['pro_uid'];
$sRepTabUid     = $_REQUEST['rep_uid'];
$tableName      = $_REQUEST['table_name'];
$title          = $_REQUEST['title'];
$swOverwrite    = $_REQUEST['overwrite'];
$isBPMN         = $_REQUEST['isBPMN'];

if ($sStatus == "1" && $sDynUid != "") {
    switch ($swOverwrite) {
        case 1:
            //Delete report table
            $criteria = new Criteria();

            $criteria->addSelectColumn(ReportTablePeer::REP_TAB_UID);
            $criteria->add(ReportTablePeer::REP_TAB_NAME, $tableName);

            $rsCriteria = ReportTablePeer::doSelectRS($criteria);

            $rptUid = null;

            if ($rsCriteria->next()) {
                $row = $rsCriteria->getRow();

                $rptUid = $row[0];
            }

            $rpts = new ReportTables();

            if ($rptUid != null) {
                $rpts->deleteReportTable($rptUid);
            }

            $sRepTabUid = "";
            break;
        case 2:
            //Delete table
            $rpts = new ReportTables();
            $rpts->dropTable($tableName, "wf");

            $sRepTabUid = "";
            break;
    }

    $criteria = new Criteria();
    $criteria->addSelectColumn(ReportTablePeer::REP_TAB_UID);
    //$criteria->add(ReportTablePeer::PRO_UID, $sProUid);
    $criteria->add(ReportTablePeer::REP_TAB_NAME, $tableName);

    $result = ReportTablePeer::doSelectRS($criteria);
    $result->setFetchmode(ResultSet::FETCHMODE_ASSOC);

    if ($result->next()) {
        $dataRes = $result->getRow();

        if ($dataRes["REP_TAB_UID"] != $sRepTabUid) {
            return 1;
        }
    } else {
        //check if table $tableName exists
        $con = Propel::getConnection("workflow");
        $stmt = $con->createStatement();

        $sql="SHOW TABLES";
        $rs1 = $stmt->executeQuery($sql, ResultSet::FETCHMODE_NUM);
        $rs1->next();
        while ( is_array($row = $rs1->getRow() )) {
            if ( $row[0] == $tableName ) {
                return 2;
            }
            $rs1->next();
        }
    }

    if ($isBPMN) {
        $_POST['form']['PRO_UID'] = $sProUid;
        $_POST['form']['REP_TAB_UID']  = $sRepTabUid;
        $_POST['form']['REP_TAB_NAME'] = $tableName;
        $_POST['form']['REP_TAB_TYPE'] = "NORMAL";
        $_POST['form']['REP_TAB_GRID'] = '';
        $_POST['form']['REP_TAB_CONNECTION'] = 'wf';
        $_POST['form']['REP_TAB_CREATE_DATE'] = date("Y-m-d H:i:s");
        $_POST['form']['REP_TAB_STATUS'] = 'ACTIVE';
        $_POST['form']['REP_TAB_TITLE'] = $title;
    } else {
        $_POST['form']['PRO_UID'] = $sProUid;
        $_POST['form']['REP_TAB_UID']  = $sRepTabUid;
        $_POST['form']['REP_TAB_NAME'] = $tableName;
        $_POST['form']['REP_TAB_TYPE'] = "GRID";
        $_POST['form']['REP_TAB_GRID'] = $sProUid . "-" . $sDynUid;
        $_POST['form']['REP_TAB_CONNECTION'] = 'wf';
        $_POST['form']['REP_TAB_CREATE_DATE'] = date("Y-m-d H:i:s");
        $_POST['form']['REP_TAB_STATUS'] = 'ACTIVE';
        $_POST['form']['REP_TAB_TITLE'] = $title;
    }

    $_POST['form']['FIELDS'] = array();

    $oReportTable = new ReportTable();
    //if (!isset($_POST['form']['REP_TAB_CONNECTION'])) {
    //  $_POST['form']['REP_TAB_CONNECTION'] = 'report';
    //}
    if ($_POST['form']['REP_TAB_UID'] != "") {
        $aReportTable   = $oReportTable->load($_POST['form']['REP_TAB_UID']);
        $sOldTableName  = $aReportTable['REP_TAB_NAME'];
        $sOldConnection = $aReportTable['REP_TAB_CONNECTION'];
    } else {
        $sOldTableName  = $_POST['form']['REP_TAB_NAME'];
        $sOldConnection = $_POST['form']['REP_TAB_CONNECTION'];
        $_POST['form']['REP_TAB_TYPE'] = 'NORMAL';
        $oReportTable->create($_POST['form']);
        $_POST['form']['REP_TAB_UID'] = $oReportTable->getRepTabUid();
    }

    $_POST['form']['REP_TAB_TYPE'] = 'NORMAL';
    $oReportTable->update($_POST['form']);

    $oReportVar = new ReportVar();
    $oReportTables = new ReportTables();
    $oReportTables->deleteAllReportVars($_POST['form']['REP_TAB_UID']);

    $aFields = array();

    if ($isBPMN) {

        $pmDyna = new PmDynaform(array());
        $pmDyna->fields["CURRENT_DYNAFORM"] = $sDynUid;
        $dataDyna = $pmDyna->getDynaform();
        $json = G::json_decode($dataDyna["DYN_CONTENT"]);
        $data = $pmDyna->jsonr($json);
        G::pr($data); die;
    } else {
        $aAux = explode('-', $_POST['form']['REP_TAB_GRID']);
        global $G_FORM;

        require_once "classes/class.formBatchRouting.php";

        $G_FORM = new FormBatchRouting($_POST["form"]["PRO_UID"] . PATH_SEP . $aAux[1], PATH_DYNAFORM, SYS_LANG, false);
        $aAux = $G_FORM->getVars(false);

        foreach ($aAux as $aField) {
            $_POST['form']['FIELDS'][] = $aField['sName'] . '-' . $aField['sType'];
        }
    }

    $aFieldsClases = array();
    $i = 1;
    $aFieldsClases[$i]['FLD_NAME'] = 'APP_UID';
    $aFieldsClases[$i]['FLD_NULL'] = 'off';
    $aFieldsClases[$i]['FLD_KEY'] = 'on';
    $aFieldsClases[$i]['FLD_AUTO_INCREMENT'] = 'off';
    $aFieldsClases[$i]['FLD_DESCRIPTION'] = '';
    $aFieldsClases[$i]['FLD_TYPE'] = 'VARCHAR' ;
    $aFieldsClases[$i]['FLD_SIZE'] = 32;
    $i++;
    $aFieldsClases[$i]['FLD_NAME'] = 'APP_NUMBER';
    $aFieldsClases[$i]['FLD_NULL'] = 'off';
    $aFieldsClases[$i]['FLD_KEY'] = 'on';
    $aFieldsClases[$i]['FLD_AUTO_INCREMENT'] = 'off';
    $aFieldsClases[$i]['FLD_DESCRIPTION'] = '';
    $aFieldsClases[$i]['FLD_TYPE'] = 'VARCHAR' ;
    $aFieldsClases[$i]['FLD_SIZE'] = 255;

    foreach ($_POST['form']['FIELDS'] as $sField) {
        $aField = explode('-', $sField);
        $i++;
        $aFieldsClases[$i]['FLD_NAME'] = $aField[0];
        $aFieldsClases[$i]['FLD_NULL'] = 'off';
        $aFieldsClases[$i]['FLD_KEY'] = 'off';
        $aFieldsClases[$i]['FLD_AUTO_INCREMENT'] = 'off';
        $aFieldsClases[$i]['FLD_DESCRIPTION'] = '';

        switch ($aField[1]) {
            case 'currency':
            case 'percentage':
                $sType = 'number';
                $aFieldsClases[$i]['FLD_TYPE'] = 'FLOAT' ;
                $aFieldsClases[$i]['FLD_SIZE'] = 255;
                break;
            case 'text':
            case 'password':
            case 'dropdown':
            case 'yesno':
            case 'checkbox':
            case 'radiogroup':
            case 'hidden':
            case "link":
                $sType = 'char';
                $aFieldsClases[$i]['FLD_TYPE'] = 'VARCHAR' ;
                $aFieldsClases[$i]['FLD_SIZE'] = 255;
                break;
            case 'textarea':
                $sType = 'text';
                $aFieldsClases[$i]['FLD_TYPE'] = 'TEXT' ;
                $aFieldsClases[$i]['FLD_SIZE'] = '';
                break;
            case 'date':
                $sType = 'date';
                $aFieldsClases[$i]['FLD_TYPE'] = 'DATE' ;
                $aFieldsClases[$i]['FLD_SIZE'] = '';
                break;
            default:
                $sType = 'char';
                $aFieldsClases[$i]['FLD_TYPE'] = 'VARCHAR' ;
                $aFieldsClases[$i]['FLD_SIZE'] = 255;
                break;
        }

        $oReportVar->create(array('REP_TAB_UID'  => $_POST['form']['REP_TAB_UID'],
                                  'PRO_UID'      => $_POST['form']['PRO_UID'],
                                  'REP_VAR_NAME' => $aField[0],
                                  'REP_VAR_TYPE' => $sType));
        $aFields[] = array('sFieldName' => $aField[0], 'sType' => $sType);
    }

    $_POST['form']['REP_TAB_TYPE'] = "NORMAL";
    $oReportTables->dropTable($sOldTableName, $sOldConnection);
    $oReportTables->createTable($_POST['form']['REP_TAB_NAME'], $_POST['form']['REP_TAB_CONNECTION'], $_POST['form']['REP_TAB_TYPE'], $aFields);
    $oReportTables->populateTable($_POST['form']['REP_TAB_NAME'], $_POST['form']['REP_TAB_CONNECTION'], $_POST['form']['REP_TAB_TYPE'], $aFields, $_POST['form']['PRO_UID'], '');

    $sRepTabUid = $_POST['form']['REP_TAB_UID'];

    //clases
} else {
    $oReportTables = new ReportTables();
    if ($sRepTabUid != "") {
        $oReportTables->deleteReportTable($sRepTabUid);
    }
    $sRepTabUid = "";
}

require_once ("classes/model/CaseConsolidatedPeer.php");
require_once ("classes/model/CaseConsolidated.php");

$oCaseConsolidated = CaseConsolidatedPeer::retrieveByPK($sTasUid);

if (!(is_object($oCaseConsolidated)) || get_class($oCaseConsolidated) != 'CaseConsolidated') {
    $oCaseConsolidated = new CaseConsolidated();
    $oCaseConsolidated->setTasUid($sTasUid);
}

if ($sStatus == '1') {
    $oCaseConsolidated->setConStatus('ACTIVE');
} else {
    $oCaseConsolidated->setConStatus('INACTIVE');
}

$oCaseConsolidated->setDynUid($sDynUid);
$oCaseConsolidated->setRepTabUid($sRepTabUid);
$oCaseConsolidated->save();

$sClassName = $tableName;//'__' . $sTasUid;

if ($sStatus == '1') {
    //$oAdditionalTables->createPropelClasses($sTableName, $sClassName, $aFields, $sAddTabUid)
    //require_once 'classes/model/AdditionalTables.php';
    //$oAdditionalTables = new AdditionalTables();
    $oAdditionalTables = new AdditionalTables();//AdditionalTablesConsolidated

    $oAdditionalTables->createPropelClasses($tableName, $sClassName, $aFieldsClases, $sTasUid);
} else {
    $sPath = PATH_DB . SYS_SYS . PATH_SEP . 'classes' . PATH_SEP;
    @unlink($sPath . $sClassName . '.php');
    @unlink($sPath . $sClassName . 'Peer.php');
    @unlink($sPath . PATH_SEP . 'map' . PATH_SEP . $sClassName . 'MapBuilder.php');
    @unlink($sPath . PATH_SEP . 'om' . PATH_SEP . 'Base' . $sClassName . '.php');
    @unlink($sPath . PATH_SEP . 'om' . PATH_SEP . 'Base' . $sClassName . 'Peer.php');
}
