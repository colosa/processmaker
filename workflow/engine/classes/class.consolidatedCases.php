<?php
G::LoadClass("pmFunctions");
G::LoadClass("reportTables");

class ConsolidatedCases
{
    function saveConsolidated ($data)
    {
        $status         = $data['con_status'];
        $sTasUid        = $data['tas_uid'];
        $sDynUid        = $data['dyn_uid'];
        $sProUid        = $data['pro_uid'];
        $sRepTabUid     = $data['rep_uid'];
        $tableName      = $data['table_name'];
        $title          = $data['title'];

        if ($sRepTabUid != '') {
            if (!$status) {
                $oCaseConsolidated = new CaseConsolidatedCore();
                $oCaseConsolidated = CaseConsolidatedCorePeer::retrieveByPK($sTasUid);
                if (!(is_object($oCaseConsolidated)) || get_class($oCaseConsolidated) != 'CaseConsolidatedCore') {
                    $oCaseConsolidated = new CaseConsolidatedCore();
                    $oCaseConsolidated->setTasUid($sTasUid);
                    $oCaseConsolidated->setConStatus('INACTIVE');
                    $oCaseConsolidated->save();
                }else{
                    $oCaseConsolidated->delete();
                }
                return 1;
            }
            $rptUid = null;
            $criteria = new Criteria();
            $criteria->addSelectColumn(ReportTablePeer::REP_TAB_UID);
            $criteria->add(ReportTablePeer::REP_TAB_UID, $sRepTabUid);
            $rsCriteria = ReportTablePeer::doSelectRS($criteria);
            
            if ($rsCriteria->next()) {
                $row = $rsCriteria->getRow();
                $rptUid = $row[0];
            }

            $rpts = new ReportTables();
            if ($rptUid != null) {
                $rpts->deleteReportTable($rptUid);
            }

            $sClassName = $tableName;
            $sPath = PATH_DB . SYS_SYS . PATH_SEP . 'classes' . PATH_SEP;

            @unlink($sPath . $sClassName . '.php');
            @unlink($sPath . $sClassName . 'Peer.php');
            @unlink($sPath . PATH_SEP . 'map' . PATH_SEP . $sClassName . 'MapBuilder.php');
            @unlink($sPath . PATH_SEP . 'om' . PATH_SEP . 'Base' . $sClassName . '.php');
            @unlink($sPath . PATH_SEP . 'om' . PATH_SEP . 'Base' . $sClassName . 'Peer.php');

            $sRepTabUid = '';
        }

        $_POST['form']['PRO_UID'] = $sProUid;
        $_POST['form']['REP_TAB_UID']  = $sRepTabUid;
        $_POST['form']['REP_TAB_NAME'] = $tableName;
        $_POST['form']['REP_TAB_TYPE'] = "NORMAL";
        $_POST['form']['REP_TAB_GRID'] = '';
        $_POST['form']['REP_TAB_CONNECTION'] = 'wf';
        $_POST['form']['REP_TAB_CREATE_DATE'] = date("Y-m-d H:i:s");
        $_POST['form']['REP_TAB_STATUS'] = 'ACTIVE';
        $_POST['form']['REP_TAB_TITLE'] = $title;
        $_POST['form']['FIELDS'] = array();

        G::LoadClass("reportTables");
        $oReportTable = new ReportTable();

        $sOldTableName  = $_POST['form']['REP_TAB_NAME'];
        $sOldConnection = $_POST['form']['REP_TAB_CONNECTION'];
        $oReportTable->create($_POST['form']);

        $_POST['form']['REP_TAB_UID'] = $oReportTable->getRepTabUid();

        $oReportVar = new ReportVar();
        $oReportTables = new ReportTables();
        $oReportTables->deleteAllReportVars($_POST['form']['REP_TAB_UID']);

        $aFields = array();
        G::LoadClass("pmDynaform");
        $pmDyna = new pmDynaform(array());
        $pmDyna->fields["CURRENT_DYNAFORM"] = $sDynUid;
        $dataDyna = $pmDyna->getDynaform();
        $json = G::json_decode($dataDyna["DYN_CONTENT"]);
        $fieldsDyna = $json->items[0]->items;
        foreach ($fieldsDyna as $value) {            
            foreach ($value as $val) {
                if(isset($val->type)){
                    if ($val->type == 'text' || $val->type == 'textarea' || $val->type == 'dropdown' || $val->type == 'checkbox' || $val->type == 'datetime' || $val->type == 'yesno' || $val->type == 'date' || $val->type == 'hidden' || $val->type == 'currency' || $val->type == 'percentage' || $val->type == 'link'){
                        $_POST['form']['FIELDS'][] = $val->name . '-' . $val->type;
                    }
                }                
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
            if ($aField[1] == 'title' || $aField[1] == 'submit') {
                continue;
            }
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

        $oCaseConsolidated = CaseConsolidatedCorePeer::retrieveByPK($sTasUid);
        if (!(is_object($oCaseConsolidated)) || get_class($oCaseConsolidated) != 'CaseConsolidatedCore') {
            $oCaseConsolidated = new CaseConsolidatedCore();
            $oCaseConsolidated->setTasUid($sTasUid);
        }

        $criteria = new Criteria();
        $criteria->addSelectColumn(CaseConsolidatedCorePeer::TAS_UID);
        $criteria->add(CaseConsolidatedCorePeer::TAS_UID, $sTasUid);
        $rsCriteria = CaseConsolidatedCorePeer::doSelectRS($criteria);
        if ($rsCriteria->next()) {
            $row = $rsCriteria->getRow();
            $oCaseConsolidated->delete();
            $oCaseConsolidated = CaseConsolidatedCorePeer::retrieveByPK($sTasUid);
        }

        if (!(is_object($oCaseConsolidated)) || get_class($oCaseConsolidated) != 'CaseConsolidatedCore') {
            $oCaseConsolidated = new CaseConsolidatedCore();
            $oCaseConsolidated->setTasUid($sTasUid);
         }

        $oCaseConsolidated->setConStatus('ACTIVE');
        $oCaseConsolidated->setDynUid($sDynUid);
        $oCaseConsolidated->setRepTabUid($sRepTabUid);
        $oCaseConsolidated->save();

        $sClassName = $tableName;
        $oAdditionalTables = new AdditionalTables();
        $oAdditionalTables->createPropelClasses($tableName, $sClassName, $aFieldsClases, $sTasUid);
    }
}
