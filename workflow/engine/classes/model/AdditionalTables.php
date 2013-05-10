<?php

/**
 * AdditionalTables.php
 * @package    workflow.engine.classes.model
 */
require_once 'classes/model/om/BaseAdditionalTables.php';

/**
 * Skeleton subclass for representing a row from the 'ADDITIONAL_TABLES' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 * <juliocesar@colosa.com, julces2000@gmail.com>
 *
 * @package    workflow.engine.classes.model
 */

function validateType ($value, $type)
{
    switch ($type) {
        case 'INTEGER':
            $value = str_replace(",", "", $value);
            $value = str_replace(".", "", $value);
            break;
        case 'FLOAT':
        case 'DOUBLE':
            $pos = strrpos($value, ",");
            $pos = ($pos === false) ? 0 : $pos;

            $posPoint = strrpos($value, ".");
            $posPoint = ($posPoint === false) ? 0 : $posPoint;

            if ($pos > $posPoint) {
                $value2 = substr($value, $pos+1);
                $value1 = substr($value, 0, $pos);
                $value1 = str_replace(".", "", $value1);
                $value = $value1.".".$value2;
            } else {
                $value2 = substr($value, $posPoint+1);
                $value1 = substr($value, 0, $posPoint);
                $value1 = str_replace(",", "", $value1);
                $value = $value1.".".$value2;
            }
            break;
        default:
            break;
    }
    return $value;
}

class AdditionalTables extends BaseAdditionalTables
{
    public $fields = array();
    public $primaryKeys = array();

    /**
     * Function load
     * access public
     */
    public function load($sUID, $bFields = false)
    {
        $oAdditionalTables = AdditionalTablesPeer::retrieveByPK($sUID);

        if (is_null($oAdditionalTables)) {
            return null;
        }

        $aFields = $oAdditionalTables->toArray(BasePeer::TYPE_FIELDNAME);
        $this->fromArray($aFields, BasePeer::TYPE_FIELDNAME);

        if ($bFields) {
            $aFields['FIELDS'] = $this->getFields();
        }

        return $aFields;
    }

    public function getFields()
    {
        if (count($this->fields) > 0) {
            return $this->fields;
        }

        require_once 'classes/model/Fields.php';
        $oCriteria = new Criteria('workflow');

        $oCriteria->addSelectColumn(FieldsPeer::FLD_UID);
        $oCriteria->addSelectColumn(FieldsPeer::FLD_INDEX);
        $oCriteria->addSelectColumn(FieldsPeer::FLD_NAME);
        $oCriteria->addSelectColumn(FieldsPeer::FLD_DESCRIPTION);
        $oCriteria->addSelectColumn(FieldsPeer::FLD_TYPE);
        $oCriteria->addSelectColumn(FieldsPeer::FLD_SIZE);
        $oCriteria->addSelectColumn(FieldsPeer::FLD_NULL);
        $oCriteria->addSelectColumn(FieldsPeer::FLD_AUTO_INCREMENT);
        $oCriteria->addSelectColumn(FieldsPeer::FLD_KEY);
        $oCriteria->addSelectColumn(FieldsPeer::FLD_FOREIGN_KEY);
        $oCriteria->addSelectColumn(FieldsPeer::FLD_FOREIGN_KEY_TABLE);
        $oCriteria->addSelectColumn(FieldsPeer::FLD_DYN_NAME);
        $oCriteria->addSelectColumn(FieldsPeer::FLD_DYN_UID);
        $oCriteria->addSelectColumn(FieldsPeer::FLD_FILTER);
        $oCriteria->add(FieldsPeer::ADD_TAB_UID, $this->getAddTabUid());
        $oCriteria->addAscendingOrderByColumn(FieldsPeer::FLD_INDEX);

        $oDataset = FieldsPeer::doSelectRS($oCriteria);
        $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);

        while ($oDataset->next()) {
            $this->fields[] = $oDataset->getRow();
        }

        return $this->fields;
    }

    public function getPrimaryKeys($type = 'complete')
    {
        $this->primaryKeys = array();
        foreach ($this->fields as $field) {
            if ($field['FLD_KEY'] == '1') {
                if ($type == 'complete') {
                    $this->primaryKeys[] = $field;
                } else {
                    // just field names
                    $this->primaryKeys[] = $field['FLD_NAME'];
                }
            }
        }
        return $this->primaryKeys;
    }

    public function loadByName($name)
    {
        try {
            $oCriteria = new Criteria('workflow');
            $oCriteria->addSelectColumn(AdditionalTablesPeer::ADD_TAB_UID);
            $oCriteria->addSelectColumn(AdditionalTablesPeer::ADD_TAB_NAME);
            $oCriteria->addSelectColumn(AdditionalTablesPeer::ADD_TAB_CLASS_NAME);
            $oCriteria->addSelectColumn(AdditionalTablesPeer::ADD_TAB_DESCRIPTION);
            //DEPRECATED! $oCriteria->addSelectColumn(AdditionalTablesPeer::ADD_TAB_SDW_LOG_INSERT);
            // $oCriteria->addSelectColumn(AdditionalTablesPeer::ADD_TAB_SDW_LOG_UPDATE);
            // $oCriteria->addSelectColumn(AdditionalTablesPeer::ADD_TAB_SDW_LOG_DELETE);
            // $oCriteria->addSelectColumn(AdditionalTablesPeer::ADD_TAB_SDW_LOG_SELECT);
            // $oCriteria->addSelectColumn(AdditionalTablesPeer::ADD_TAB_SDW_MAX_LENGTH);
            // $oCriteria->addSelectColumn(AdditionalTablesPeer::ADD_TAB_SDW_AUTO_DELETE);
            $oCriteria->addSelectColumn(AdditionalTablesPeer::ADD_TAB_PLG_UID);
            $oCriteria->addSelectColumn(AdditionalTablesPeer::DBS_UID);
            $oCriteria->addSelectColumn(AdditionalTablesPeer::PRO_UID);
            $oCriteria->addSelectColumn(AdditionalTablesPeer::ADD_TAB_TYPE);
            $oCriteria->addSelectColumn(AdditionalTablesPeer::ADD_TAB_GRID);
            $oCriteria->addSelectColumn(AdditionalTablesPeer::ADD_TAB_TAG);

            // AdditionalTablesPeer::ADD_TAB_NAME is unique
            $oCriteria->add(AdditionalTablesPeer::ADD_TAB_NAME, $name, Criteria::EQUAL);

            $oDataset = AdditionalTablesPeer::doSelectRS($oCriteria);
            $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);

            $oDataset->next();

            return $oDataset->getRow();
        } catch (Exception $oError) {
            throw($oError);
        }
    }

    /**
     * Create & Update function
     */
    public function create($aData, $aFields = array())
    {
        if (!isset($aData['ADD_TAB_UID']) || (isset($aData['ADD_TAB_UID']) && $aData['ADD_TAB_UID'] == '')) {
            $aData['ADD_TAB_UID'] = G::generateUniqueID();
        }

        $oConnection = Propel::getConnection(AdditionalTablesPeer::DATABASE_NAME);

        try {
            $oAdditionalTables = new AdditionalTables();
            $oAdditionalTables->fromArray($aData, BasePeer::TYPE_FIELDNAME);

            if ($oAdditionalTables->validate()) {
                $oConnection->begin();
                $iResult = $oAdditionalTables->save();
                $oConnection->commit();
                /*                 * **DEPRECATED
                  require_once 'classes/model/ShadowTable.php';
                  $oShadowTable = new ShadowTable();
                  $oShadowTable->create(array('ADD_TAB_UID' => $aData['ADD_TAB_UID'],
                  'SHD_ACTION'  => 'CREATE',
                  'SHD_DETAILS' => serialize($aFields),
                  'USR_UID'     => (isset($_SESSION['USER_LOGGED']) ? $_SESSION['USER_LOGGED'] : ''),
                  'APP_UID'     => '',
                  'SHD_DATE'    => date('Y-m-d H:i:s')));
                 */
                return $aData['ADD_TAB_UID'];
            } else {
                $sMessage = '';
                $aValidationFailures = $oAdditionalTables->getValidationFailures();
                foreach ($aValidationFailures as $oValidationFailure) {
                    $sMessage .= $oValidationFailure->getMessage() . '<br />';
                }
                throw(new Exception('The registry cannot be created!<br />' . $sMessage));
            }
        } catch (Exception $oError) {
            $oConnection->rollback();
            throw($oError);
        }
    }

    public function update($aData, $aFields = array())
    {
        $oConnection = Propel::getConnection(AdditionalTablesPeer::DATABASE_NAME);
        try {
            $oAdditionalTables = AdditionalTablesPeer::retrieveByPK($aData['ADD_TAB_UID']);
            if (!is_null($oAdditionalTables)) {
                $oAdditionalTables->fromArray($aData, BasePeer::TYPE_FIELDNAME);
                if ($oAdditionalTables->validate()) {
                    $oConnection->begin();
                    $iResult = $oAdditionalTables->save();
                    $oConnection->commit();
                } else {
                    $sMessage = '';
                    $aValidationFailures = $oAdditionalTables->getValidationFailures();
                    foreach ($aValidationFailures as $oValidationFailure) {
                        $sMessage .= $oValidationFailure->getMessage() . '<br />';
                    }
                    throw(new Exception('The registry cannot be updated!<br />' . $sMessage));
                }
            } else {
                throw(new Exception('This row doesn\'t exist!'));
            }
        } catch (Exception $oError) {
            $oConnection->rollback();
            throw($oError);
        }
    }

    public function remove($sUID)
    {
        $oConnection = Propel::getConnection(AdditionalTablesPeer::DATABASE_NAME);
        try {
            $oAdditionalTables = AdditionalTablesPeer::retrieveByPK($sUID);
            if (!is_null($oAdditionalTables)) {
                $aAdditionalTables = $oAdditionalTables->toArray(BasePeer::TYPE_FIELDNAME);
                $oConnection->begin();
                $iResult = $oAdditionalTables->delete();
                $oConnection->commit();

                return $iResult;
            } else {
                throw(new Exception('This row doesn\'t exist!'));
            }
        } catch (Exception $oError) {
            $oConnection->rollback();
            throw($oError);
        }
    }

    public function deleteAll($id)
    {
        //deleting pm table
        $additionalTable = AdditionalTables::load($id);
        AdditionalTables::remove($id);

        //deleting fields
        require_once 'classes/model/Fields.php';
        $criteria = new Criteria('workflow');
        $criteria->add(FieldsPeer::ADD_TAB_UID, $id);
        FieldsPeer::doDelete($criteria);

        //remove all related to pmTable
        G::loadClass('pmTable');
        $pmTable = new pmTable($additionalTable['ADD_TAB_NAME']);
        $pmTable->setDataSource($additionalTable['DBS_UID']);
        $pmTable->remove();
    }

    public function getPHPName($sName)
    {
        $sName = trim($sName);
        $aAux = explode('_', $sName);
        foreach ($aAux as $iKey => $sPart) {
            $aAux[$iKey] = ucwords(strtolower($sPart));
        }
        return implode('', $aAux);
    }

    public function deleteMultiple($arrUID)
    {
        $arrUIDs = explode(",", $arrUID);
        foreach ($arrUIDs as $UID) {
            $this->deleteAll($UID);
        }
    }

    public function getDataCriteria($sUID)
    {
        try {
            $aData = $this->load($sUID, true);
            $sPath = PATH_DB . SYS_SYS . PATH_SEP . 'classes' . PATH_SEP;
            $sClassName = ($aData['ADD_TAB_CLASS_NAME'] != ''
                          ? $aData['ADD_TAB_CLASS_NAME']
                          : $this->getPHPName($aData['ADD_TAB_NAME']));

            if (file_exists($sPath . $sClassName . '.php')) {
                require_once $sPath . $sClassName . '.php';
            } else {
                return null;
            }

            $sClassPeerName = $sClassName . 'Peer';
            $con = Propel::getConnection($aData['DBS_UID']);
            $oCriteria = new Criteria($aData['DBS_UID']);

            //eval('$oCriteria->addSelectColumn(' . $sClassPeerName . '::PM_UNIQUE_ID);');
            eval('$oCriteria->addSelectColumn("\'1\' AS DUMMY");');
            foreach ($aData['FIELDS'] as $aField) {
                eval('$oCriteria->addSelectColumn(' . $sClassPeerName . '::' . $aField['FLD_NAME'] . ');');
            }

            switch ($aField['FLD_TYPE']) {
                case 'VARCHAR':
                case 'TEXT':
                case 'DATE':
                    // if($aField['FLD_NULL']!=1)
                    // eval('$oCriteria->add(' . $sClassPeerName . '::' . $aField['FLD_NAME']
                    //. ', \'(�_�_�)\', Criteria::NOT_EQUAL);');
                    break;
                case 'INT';
                case 'FLOAT':
                    eval('$oCriteria->add(' . $sClassPeerName . '::' . $aField['FLD_NAME']
                        .', -99999999999, Criteria::NOT_EQUAL);');
                    break;
            }
            //eval('$oCriteria->addAscendingOrderByColumn(' . $sClassPeerName . '::PM_UNIQUE_ID);');
            //echo $oCriteria->toString();
            return $oCriteria;
        } catch (Exception $oError) {
            throw($oError);
        }
    }

    public function getAllData($sUID, $start = null, $limit = null, $keyOrderUppercase = true)
    {
        $addTab = new AdditionalTables();
        $aData = $addTab->load($sUID, true);
        if (!isset($_SESSION['PROCESS'])) {
            $_SESSION["PROCESS"] = $aData['PRO_UID'];
        }
        $aData['DBS_UID'] = $aData['DBS_UID'] ? $aData['DBS_UID'] : 'workflow';
        $sPath = PATH_DB . SYS_SYS . PATH_SEP . 'classes' . PATH_SEP;
        $sClassName = ($aData['ADD_TAB_CLASS_NAME'] != ''
                       ? $aData['ADD_TAB_CLASS_NAME']
                       : $this->getPHPName($aData['ADD_TAB_NAME']));

        if (file_exists($sPath . $sClassName . '.php')) {
            require_once $sPath . $sClassName . '.php';
        } else {
            return null;
        }

        $sClassPeerName = $sClassName . 'Peer';
        $con = Propel::getConnection($aData['DBS_UID']);
        $oCriteria = new Criteria($aData['DBS_UID']);

        //eval('$oCriteria->addSelectColumn("\'1\' AS DUMMY");');
        if ($keyOrderUppercase == true) {
            foreach ($aData['FIELDS'] as $aField) {
                eval('$oCriteria->addSelectColumn(' . $sClassPeerName . '::' . $aField['FLD_NAME'] . ');');
                if ($aField['FLD_KEY'] == '1') {
                    eval('$oCriteria->addAscendingOrderByColumn('. $sClassPeerName . '::' . $aField['FLD_NAME'] . ');');
                }
            }
        }
        $oCriteriaCount = clone $oCriteria;
        //$count = $sClassPeerName::doCount($oCriteria);
        eval('$count = ' . $sClassPeerName . '::doCount($oCriteria);');

        if (isset($limit)) {
            $oCriteria->setLimit($limit);
        }
        if (isset($start)) {
            $oCriteria->setOffset($start);
        }
        //$rs = $sClassPeerName::doSelectRS($oCriteria);
        eval('$rs = ' . $sClassPeerName . '::doSelectRS($oCriteria);');
        $rs->setFetchmode(ResultSet::FETCHMODE_ASSOC);

        $rows = Array();
        while ($rs->next()) {
            $rows[] = $rs->getRow();
        }

        return array('rows' => $rows, 'count' => $count);
    }

    public function checkClassNotExist($sUID)
    {
        try {
            $aData = $this->load($sUID, true);
            $sPath = PATH_DB . SYS_SYS . PATH_SEP . 'classes' . PATH_SEP;
            $sClassName = ($aData['ADD_TAB_CLASS_NAME'] != ''
                          ? $aData['ADD_TAB_CLASS_NAME']
                          : $this->getPHPName($aData['ADD_TAB_NAME']));

            if (file_exists($sPath . $sClassName . '.php')) {
                return $sClassName;
            } else {
                return '';
            }
        } catch (Exception $oError) {
            throw($oError);
        }
    }

    public function saveDataInTable($sUID, $aFields)
    {
        try {
            $aData = $this->load($sUID, true);
            $sPath = PATH_DB . SYS_SYS . PATH_SEP . 'classes' . PATH_SEP;
            $sClassName = ($aData['ADD_TAB_CLASS_NAME'] != ''
                          ? $aData['ADD_TAB_CLASS_NAME']
                          : $this->getPHPName($aData['ADD_TAB_NAME']));
            // $oConnection = Propel::getConnection(FieldsPeer::DATABASE_NAME);
            $oConnection = Propel::getConnection($aData['DBS_UID']);
            $stmt = $oConnection->createStatement();
            require_once $sPath . $sClassName . '.php';
            $sKeys = '';
            $keysAutoIncrement = 0;
            $keyUIDAutoIncrement = '';
            foreach ($aData['FIELDS'] as $aField) {
                if ($aField['FLD_KEY'] == 1) {
                    if ($aField['FLD_AUTO_INCREMENT'] == 1) {
                        if ($keysAutoIncrement == 0) {
                            $keyUIDAutoIncrement = $aField['FLD_NAME'];
                        }
                        $keysAutoIncrement++;
                    }
                    $vValue = $aFields[$aField['FLD_NAME']];
                    eval('$' . $aField['FLD_NAME'] . ' = $vValue;');
                    $sKeys .= '$' . $aField['FLD_NAME'] . ',';
                }
            }
            $sKeys = substr($sKeys, 0, -1);
            $oClass = new $sClassName;
            foreach ($aFields as $sKey => $sValue) {
                if (!preg_match("/\(?\)/", $sKey)) {
                    eval('$oClass->set' . $this->getPHPName($sKey) . '($aFields["' . $sKey . '"]);');
                }
            }
            if ($oClass->validate()) {
                $iResult = $oClass->save();
                if ($keysAutoIncrement == 1 && $aFields[$keyUIDAutoIncrement] == '' && isset($_SESSION['APPLICATION']) && $_SESSION['APPLICATION'] != '') {
                    G::LoadClass('case');
                    $oCaseKeyAuto = new Cases();
                    $newId = $oClass->getId();
                    $aFields = $oCaseKeyAuto->loadCase($_SESSION['APPLICATION']);
                    $aFields['APP_DATA'][$keyUIDAutoIncrement] = $newId;
                    if (isset($_POST['form'])) {
                        $_POST['form'][$keyUIDAutoIncrement] = $newId;
                    }
                    $oCaseKeyAuto->updateCase($_SESSION['APPLICATION'], $aFields);
                }
            }
            return true;
        } catch (Exception $oError) {
            throw($oError);
        }
    }

    public function getDataTable($sUID, $aKeys)
    {
        try {
            $aData = $this->load($sUID, true);
            $sPath = PATH_DB . SYS_SYS . PATH_SEP . 'classes' . PATH_SEP;
            $sClassName = ($aData['ADD_TAB_CLASS_NAME'] != ''
                          ? $aData['ADD_TAB_CLASS_NAME']
                          : $this->getPHPName($aData['ADD_TAB_NAME']));
            require_once $sPath . $sClassName . '.php';
            $sKeys = '';
            foreach ($aKeys as $sName => $vValue) {
                eval('$' . $sName . ' = $vValue;');
                $sKeys .= '$' . $sName . ',';
            }
            $sKeys = substr($sKeys, 0, -1);
            eval('$oClass = ' . $sClassName . 'Peer::retrieveByPK(' . $sKeys . ');');
            //eval('$oClass = ' . $sClassName . 'Peer::retrieveByPK($sPMUID);');
            if (!is_null($oClass)) {
                return $oClass->toArray(BasePeer::TYPE_FIELDNAME);
            } else {
                return false;
            }
        } catch (Exception $oError) {
            throw($oError);
        }
    }

    public function updateDataInTable($sUID, $aFields)
    {
        try {
            //$sPMUID = $aFields['PM_UNIQUE_ID'];
            $aData = $this->load($sUID, true);
            $sPath = PATH_DB . SYS_SYS . PATH_SEP . 'classes' . PATH_SEP;
            $sClassName = ($aData['ADD_TAB_CLASS_NAME'] != ''
                          ? $aData['ADD_TAB_CLASS_NAME']
                          : $this->getPHPName($aData['ADD_TAB_NAME']));
            $oConnection = Propel::getConnection(FieldsPeer::DATABASE_NAME);
            require_once $sPath . $sClassName . '.php';
            $sKeys = '';
            foreach ($aData['FIELDS'] as $aField) {
                if ($aField['FLD_KEY'] == 1) {
                    $vValue = $aFields[$aField['FLD_NAME']];
                    eval('$' . $aField['FLD_NAME'] . ' = $vValue;');
                    $sKeys .= '$' . $aField['FLD_NAME'] . ',';
                }
            }
            $sKeys = substr($sKeys, 0, -1);
            eval('$oClass = ' . $sClassName . 'Peer::retrieveByPK(' . $sKeys . ');');
            //eval('$oClass = ' . $sClassName . 'Peer::retrieveByPK($sPMUID);');
            if (!is_null($oClass)) {
                $oClass->fromArray($aFields, BasePeer::TYPE_FIELDNAME);
                if ($oClass->validate()) {
                    $oConnection->begin();
                    $iResult = $oClass->save();
                    $oConnection->commit();
                    return $iResult;
                }
            } else {
                $sMessage = '';
                if ($oClass) {
                    $aValidationFailures = $oClass->getValidationFailures();
                    foreach ($aValidationFailures as $oValidationFailure) {
                        $sMessage .= $oValidationFailure->getMessage() . '<br />';
                    }
                } else {
                    $sMessage = 'Error, row cannot updated';
                    return false;
                }
                throw(new Exception('The registry cannot be updated!<br />' . $sMessage));
            }
        } catch (Exception $oError) {
            throw($oError);
        }
    }

    public function deleteDataInTable($sUID, $aKeys)
    {
        try {
            $aData = $this->load($sUID, true);
            $sPath = PATH_DB . SYS_SYS . PATH_SEP . 'classes' . PATH_SEP;
            $sClassName = ($aData['ADD_TAB_CLASS_NAME'] != ''
                          ? $aData['ADD_TAB_CLASS_NAME']
                          : $this->getPHPName($aData['ADD_TAB_NAME']));
            $oConnection = Propel::getConnection(FieldsPeer::DATABASE_NAME);
            require_once $sPath . $sClassName . '.php';
            $sKeys = '';
            foreach ($aKeys as $sName => $vValue) {
                eval('$' . $sName . ' = $vValue;');
                $sKeys .= '$' . $sName . ',';
            }
            $sKeys = substr($sKeys, 0, -1);
            eval('$oClass = ' . $sClassName . 'Peer::retrieveByPK(' . $sKeys . ');');
            //eval('$oClass = ' . $sClassName . 'Peer::retrieveByPK($sPMUID);');
            if (!is_null($oClass)) {
                if ($oClass->validate()) {
                    $oConnection->begin();
                    $iResult = $oClass->delete();
                    $oConnection->commit();
                    return $iResult;
                }
            } else {
                $sMessage = '';
                $aValidationFailures = $oConnection-- > getValidationFailures();
                foreach ($aValidationFailures as $oValidationFailure) {
                    $sMessage .= $oValidationFailure->getMessage() . '<br />';
                }
                throw(new Exception('The registry cannot be updated!<br />' . $sMessage));
            }
        } catch (Exception $oError) {
            throw($oError);
        }
    }

    /**
     * Populate the report table with all case data
     * @param string $sType
     * @param string $sProcessUid
     * @param string $sGrid
     * @return number
     */
    public function populateReportTable($tableName, $sConnection = 'rp', $type = 'NORMAL', $processUid = '', $gridKey = '', $addTabUid = '')
    {
        require_once "classes/model/Application.php";

        $this->className = $this->getPHPName($tableName);
        $this->classPeerName = $this->className . 'Peer';

        if (!file_exists(PATH_WORKSPACE . 'classes/' . $this->className . '.php')) {
            throw new Exception( "ERROR: " . PATH_WORKSPACE . 'classes/' . $this->className . '.php'
                                . " class file doesn't exit!");
        }

        require_once PATH_WORKSPACE . 'classes/' . $this->className . '.php';

        //select cases for this Process, ordered by APP_NUMBER
        $con = Propel::getConnection($sConnection);
        $stmt = $con->createStatement();
        $criteria = new Criteria('workflow');
        $criteria->add(ApplicationPeer::PRO_UID, $processUid);
        $criteria->addAscendingOrderByColumn(ApplicationPeer::APP_NUMBER);
        $dataset = ApplicationPeer::doSelectRS($criteria);
        $dataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);

        while ($dataset->next()) {
            $row = $dataset->getRow();
            //remove old applications references
            $deleteSql = "DELETE FROM $tableName WHERE APP_UID = '" . $row['APP_UID'] . "'";
            $rs = $stmt->executeQuery($deleteSql);
            // getting the case data
            $caseData = unserialize($row['APP_DATA']);

            $fieldTypes = array();

            if ($addTabUid != '') {
                require_once 'classes/model/Fields.php';
                $criteriaField = new Criteria('workflow');
                $criteriaField->add(FieldsPeer::ADD_TAB_UID, $addTabUid);
                $datasetField = FieldsPeer::doSelectRS($criteriaField);
                $datasetField->setFetchmode(ResultSet::FETCHMODE_ASSOC);
                while ($datasetField->next()) {
                    $rowfield = $datasetField->getRow();
                    switch ($rowfield['FLD_TYPE']) {
                        case 'FLOAT':
                        case 'DOUBLE':
                        case 'INTEGER':
                            $fieldTypes[] = array($rowfield['FLD_NAME']=>$rowfield['FLD_TYPE']);
                            break;
                        default:
                            break;
                    }
                }
            }

            // quick fix
            // map all empty values as NULL for Database
            foreach ($caseData as $dKey => $dValue) {
                if (!is_array($dValue)) {
                    foreach ($fieldTypes as $key => $fieldType) {
                        foreach ($fieldType as $name => $theType) {
                            if (strtoupper($dKey) == $name) {
                                $caseData[$dKey] = validateType ($dValue, $theType);
                                unset($name);
                            }
                        }
                    }
                    // normal fields
                    if (trim($dValue) === '') {
                        $caseData[$dKey] = null;
                    }
                } else {
                    // grids
                    foreach ($caseData[$dKey] as $dIndex => $dRow) {
                        foreach ($dRow as $k => $v) {
                            if (trim($v) === '') {
                                $caseData[$dKey][$dIndex][$k] = null;
                            }
                        }
                    }
                }
            }

            if ($type == 'GRID') {
                list($gridName, $gridUid) = explode('-', $gridKey);
                $gridData = isset($caseData[$gridName]) ? $caseData[$gridName] : array();

                foreach ($gridData as $i => $gridRow) {
                    eval('$obj = new ' . $this->className . '();');
                    $obj->fromArray($caseData, BasePeer::TYPE_FIELDNAME);
                    $obj->setAppUid($row['APP_UID']);
                    $obj->setAppNumber($row['APP_NUMBER']);
                    if (method_exists($obj, 'setAppStatus')) {
                        $obj->setAppStatus($row['APP_STATUS']);
                    }
                    $obj->fromArray(array_change_key_case($gridRow, CASE_UPPER), BasePeer::TYPE_FIELDNAME);
                    $obj->setRow($i);
                    $obj->save();
                    eval('$obj = new ' . $this->className . '();');
                }
            } else {
                eval('$obj = new ' . $this->className . '();');
                $obj->fromArray(array_change_key_case($caseData, CASE_UPPER), BasePeer::TYPE_FIELDNAME);
                $obj->setAppUid($row['APP_UID']);
                $obj->setAppNumber($row['APP_NUMBER']);
                if (method_exists($obj, 'setAppStatus')) {
                    $obj->setAppStatus($row['APP_STATUS']);
                }
                $obj->save();
                $obj = null;
            }
        }
    }

    /**
     * Update the report table with a determinated case data
     * @param string $proUid
     * @param string $appUid
     * @param string $appNumber
     * @param string $caseData
     */
    public function updateReportTables($proUid, $appUid, $appNumber, $caseData, $appStatus)
    {
        G::loadClass('pmTable');
        //get all Active Report Tables
        $criteria = new Criteria('workflow');
        $criteria->add(AdditionalTablesPeer::PRO_UID, $proUid);
        $dataset = AdditionalTablesPeer::doSelectRS($criteria);
        $dataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);

        // accomplish all related  report tables for this process that contain case data
        // for the target ($appUid) application
        while ($dataset->next()) {
            $row = $dataset->getRow();
            $className = $row['ADD_TAB_CLASS_NAME'];
            // verify if the report table class exists
            if (!file_exists(PATH_WORKSPACE . 'classes/' . $className . '.php')) {
                continue;
            }
            // the class exists then load it.
            require_once PATH_WORKSPACE . 'classes/' . $className . '.php';
            // create a criteria object of report table class
            $c = new Criteria(pmTable::resolveDbSource($row['DBS_UID']));
            // select all related records with this $appUid
            eval('$c->add(' . $className . 'Peer::APP_UID, \'' . $appUid . '\');');
            eval('$records = ' . $className . 'Peer::doSelect($c);');

            switch ($row['ADD_TAB_TYPE']) {
                //switching by report table type
                case 'NORMAL':
                    require_once 'classes/model/Fields.php';
                    $criteriaField = new Criteria('workflow');
                    $criteriaField->add(FieldsPeer::ADD_TAB_UID, $row['ADD_TAB_UID']);
                    $datasetField = FieldsPeer::doSelectRS($criteriaField);
                    $datasetField->setFetchmode(ResultSet::FETCHMODE_ASSOC);
                    $fieldTypes = array();
                    while ($datasetField->next()) {
                        $rowfield = $datasetField->getRow();
                        switch ($rowfield['FLD_TYPE']) {
                            case 'FLOAT':
                            case 'DOUBLE':
                            case 'INTEGER':
                                $fieldTypes[] = array($rowfield['FLD_NAME']=>$rowfield['FLD_TYPE']);
                                break;
                            default:
                                break;
                        }
                    }

                    // parsing empty values to null
                    foreach ($caseData as $i => $v) {
                        foreach ($fieldTypes as $key => $fieldType) {
                            foreach ($fieldType as $name => $type) {
                                if ( strtoupper ( $i) == $name) {
                                    $v = validateType ($v, $type);
                                    unset($name);
                                }
                            }
                        }
                        $caseData[$i] = $v === '' ? null : $v;
                    }

                    if (is_array($records) && count($records) > 0) {
                        // if the record already exists on the report table
                        foreach ($records as $record) {
                            //update all records
                            if (method_exists($record, 'setAppStatus')) {
                                $record->setAppStatus($appStatus);
                            }
                            $record->fromArray(array_change_key_case($caseData, CASE_UPPER), BasePeer::TYPE_FIELDNAME);
                            if ($record->validate()) {
                                $record->save();
                            }
                        }
                    } else {
                        // there are not any record for this application on the table, then create it
                        eval('$obj = new ' . $className . '();');
                        $obj->fromArray(array_change_key_case($caseData, CASE_UPPER), BasePeer::TYPE_FIELDNAME);
                        $obj->setAppUid($appUid);
                        $obj->setAppNumber($appNumber);
                        if (method_exists($obj, 'setAppStatus')) {
                            $obj->setAppStatus($appStatus);
                        }
                        $obj->save();
                    }
                    break;
                case 'GRID':
                    list($gridName, $gridUid) = explode('-', $row['ADD_TAB_GRID']);
                    $gridData = isset($caseData[$gridName]) ? $caseData[$gridName] : array();

                    // delete old records
                    if (is_array($records) && count($records) > 0) {
                        foreach ($records as $record) {
                            $record->delete();
                        }
                    }
                    // save all grid rows on grid type report table
                    foreach ($gridData as $i => $gridRow) {
                        eval('$obj = new ' . $className . '();');
                        $obj->fromArray(array_change_key_case($gridRow, CASE_UPPER), BasePeer::TYPE_FIELDNAME);
                        $obj->setAppUid($appUid);
                        $obj->setAppNumber($appNumber);
                        if (method_exists($obj, 'setAppStatus')) {
                            $obj->setAppStatus($appStatus);
                        }
                        $obj->setRow($i);
                        $obj->save();
                    }
                    break;
            }
        }
    }

    public function getTableVars($uid, $bWhitType = false)
    {
        require_once 'classes/model/Fields.php';
        try {
            $oCriteria = new Criteria('workflow');
            $oCriteria->addSelectColumn(FieldsPeer::ADD_TAB_UID);
            $oCriteria->addSelectColumn(FieldsPeer::FLD_NAME);
            $oCriteria->addSelectColumn(FieldsPeer::FLD_TYPE);
            $oCriteria->addSelectColumn(FieldsPeer::FLD_DYN_NAME);
            $oCriteria->add(FieldsPeer::ADD_TAB_UID, $uid);
            $oDataset = ReportVarPeer::doSelectRS($oCriteria);
            $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
            $oDataset->next();
            $aVars = array();
            $aImportedVars = array(); //This array will help to control if the variable already exist
            while ($aRow = $oDataset->getRow()) {
                if ($bWhitType) {
                    if (!in_array($aRow['FLD_NAME'], $aImportedVars)) {
                        $aImportedVars[] = $aRow['FLD_NAME'];
                        $aVars[] = array('sFieldName'    => $aRow['FLD_NAME'],
                                         'sFieldDynName' => $aRow['FLD_DYN_NAME'],
                                         'sType'         => $aRow['FLD_TYPE']);
                    }
                } else {
                    $aVars[] = $aRow['FLD_NAME'];
                }
                $oDataset->next();
            }
            return $aVars;
        } catch (Exception $oError) {
            throw($oError);
        }
    }

    public function getAll($start = 0, $limit = 20, $filter = '', $process = null)
    {
        $oCriteria = new Criteria('workflow');
        $oCriteria->addSelectColumn(AdditionalTablesPeer::ADD_TAB_UID);
        $oCriteria->addSelectColumn(AdditionalTablesPeer::ADD_TAB_NAME);
        $oCriteria->addSelectColumn(AdditionalTablesPeer::ADD_TAB_DESCRIPTION);
        $oCriteria->addSelectColumn(AdditionalTablesPeer::ADD_TAB_TYPE);
        $oCriteria->addSelectColumn(AdditionalTablesPeer::ADD_TAB_TAG);
        $oCriteria->addSelectColumn(AdditionalTablesPeer::PRO_UID);
        $oCriteria->addSelectColumn(AdditionalTablesPeer::DBS_UID);

        if (isset($process)) {
            foreach ($process as $key => $pro_uid) {
                if ($key == 'equal') {
                    $oCriteria->add(AdditionalTablesPeer::PRO_UID, $pro_uid, Criteria::EQUAL);
                } else {
                    $oCriteria->add(AdditionalTablesPeer::PRO_UID, $pro_uid, Criteria::NOT_EQUAL);
                }
            }
        }

        if ($filter != '' && is_string($filter)) {
            $oCriteria->add(
            $oCriteria->getNewCriterion(AdditionalTablesPeer::ADD_TAB_NAME, '%' . $filter . '%', Criteria::LIKE)->addOr(
            $oCriteria->getNewCriterion(AdditionalTablesPeer::ADD_TAB_DESCRIPTION, '%' . $filter . '%', Criteria::LIKE))
            );
        }

        if (isset($_POST['sort'])) {
            if ($_POST['dir'] == 'ASC') {
                eval('$oCriteria->addAscendingOrderByColumn(AdditionalTablesPeer::' . $_POST['sort'] . ');');
            } else {
                eval('$oCriteria->addDescendingOrderByColumn(AdditionalTablesPeer::' . $_POST['sort'] . ');');
            }
        } else {
            //$oCriteria->addAscendingOrderByColumn(AdditionalTablesPeer::ADD_TAB_UID);
        }

        $criteriaCount = clone $oCriteria;
        $count = AdditionalTablesPeer::doCount($criteriaCount);

        $oCriteria->setLimit($limit);
        $oCriteria->setOffset($start);

        $oDataset = AdditionalTablesPeer::doSelectRS($oCriteria);
        $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);

        $addTables = Array();
        $proUids = Array();

        while ($oDataset->next()) {
            $row = $oDataset->getRow();
            $row['PRO_TITLE'] = $row['PRO_DESCRIPTION'] = '';
            $addTables[] = $row;
            if ($row['PRO_UID'] != '') {
                $proUids[] = $row['PRO_UID'];
            }
        }

        //process details will have the info about the processes
        $procDetails = Array();

        if (count($proUids) > 0) {
            //now get the labels for all process, using an array of Uids,
            $c = new Criteria('workflow');
            //$c->add ( ContentPeer::CON_CATEGORY, 'PRO_TITLE', Criteria::EQUAL );
            $c->add(ContentPeer::CON_LANG, defined('SYS_LANG') ? SYS_LANG : 'en', Criteria::EQUAL);
            $c->add(ContentPeer::CON_ID, $proUids, Criteria::IN);

            $dt = ContentPeer::doSelectRS($c);
            $dt->setFetchmode(ResultSet::FETCHMODE_ASSOC);

            while ($dt->next()) {
                $row = $dt->getRow();
                $procDetails[$row['CON_ID']][$row['CON_CATEGORY']] = $row['CON_VALUE'];
            }

            foreach ($addTables as $i => $addTable) {
                if (isset($procDetails[$addTable['PRO_UID']]['PRO_TITLE'])) {
                    $addTables[$i]['PRO_TITLE'] = $procDetails[$addTable['PRO_UID']]['PRO_TITLE'];
                }

                if (isset($procDetails[$addTable['PRO_UID']]['PRO_DESCRIPTION'])) {
                    $addTables[$i]['PRO_DESCRIPTION'] = $procDetails[$addTable['PRO_UID']]['PRO_DESCRIPTION'];
                }
            }
        }

        return array('rows' => $addTables, 'count' => $count);
    }

    /**
     * DEPRECATED createPropelClasses()
     *
     * Don't use this method, it was left only for backward compatibility
     * for some external plugins that still is using it
     */
    public function createPropelClasses($sTableName, $sClassName, $aFields, $sAddTabUid, $connection = 'workflow')
    {
        try {
            /* $aUID = array('FLD_NAME'           => 'PM_UNIQUE_ID',
              'FLD_TYPE'           => 'INT',
              'FLD_KEY'            => 'on',
              'FLD_SIZE'           => '11',
              'FLD_NULL'           => '',
              'FLD_AUTO_INCREMENT' => 'on');
              array_unshift($aFields, $aUID); */
            $aTypes = array(
                'VARCHAR' => 'string',
                'TEXT'    => 'string',
                'DATE'    => 'int',
                'INT'     => 'int',
                'FLOAT'   => 'double'
            );
            $aCreoleTypes = array(
                'VARCHAR' => 'VARCHAR',
                'TEXT'    => 'LONGVARCHAR',
                'DATE'    => 'TIMESTAMP',
                'INT'     => 'INTEGER',
                'FLOAT'   => 'DOUBLE'
            );
            if ($sClassName == '') {
                $sClassName = $this->getPHPName($sTableName);
            }

            $sPath = PATH_DB . SYS_SYS . PATH_SEP . 'classes' . PATH_SEP;
            if (!file_exists($sPath)) {
                G::mk_dir($sPath);
            }
            if (!file_exists($sPath . 'map')) {
                G::mk_dir($sPath . 'map');
            }
            if (!file_exists($sPath . 'om')) {
                G::mk_dir($sPath . 'om');
            }
            $aData = array();
            $aData['pathClasses'] = substr(PATH_DB, 0, -1);
            $aData['tableName'] = $sTableName;
            $aData['className'] = $sClassName;
            $aData['connection'] = $connection;
            $aData['GUID'] = $sAddTabUid;

            $aData['firstColumn'] = isset($aFields[0])
                                    ? strtoupper($aFields[0]['FLD_NAME'])
                                    : ($aFields[1]['FLD_NAME']);
            $aData['totalColumns'] = count($aFields);
            $aData['useIdGenerator'] = 'false';
            $oTP1 = new TemplatePower(PATH_TPL . 'additionalTables' . PATH_SEP . 'Table.tpl');
            $oTP1->prepare();
            $oTP1->assignGlobal($aData);
            file_put_contents($sPath . $sClassName . '.php', $oTP1->getOutputContent());
            $oTP2 = new TemplatePower(PATH_TPL . 'additionalTables' . PATH_SEP . 'TablePeer.tpl');
            $oTP2->prepare();
            $oTP2->assignGlobal($aData);
            file_put_contents($sPath . $sClassName . 'Peer.php', $oTP2->getOutputContent());
            $aColumns = array();
            $aPKs = array();
            $aNotPKs = array();
            $i = 0;
            foreach ($aFields as $iKey => $aField) {
                $aField['FLD_NAME'] = strtoupper($aField['FLD_NAME']);
                if ($aField['FLD_TYPE'] == 'DATE') {
                    $aField['FLD_NULL'] = '';
                }
                $aColumn = array(
                    'name' => $aField['FLD_NAME'],
                    'phpName' => $this->getPHPName($aField['FLD_NAME']),
                    'type' => $aTypes[$aField['FLD_TYPE']],
                    'creoleType' => $aCreoleTypes[$aField['FLD_TYPE']],
                    'notNull' => ($aField['FLD_NULL'] == 'on' ? 'true' : 'false'),
                    'size' => (($aField['FLD_TYPE'] == 'VARCHAR')
                              || ($aField['FLD_TYPE'] == 'INT')
                              || ($aField['FLD_TYPE'] == 'FLOAT') ? $aField['FLD_SIZE'] : 'null'),
                    'var' => strtolower($aField['FLD_NAME']),
                    'attribute' => (($aField['FLD_TYPE'] == 'VARCHAR')
                                   || ($aField['FLD_TYPE'] == 'TEXT')
                                   || ($aField['FLD_TYPE'] == 'DATE')
                                   ? '$' . strtolower($aField['FLD_NAME']) . " = ''"
                                   : '$' . strtolower($aField['FLD_NAME']) . ' = 0'),
                    'index' => $i,
                );
                if ($aField['FLD_TYPE'] == 'DATE') {
                    $aColumn['getFunction'] = '/**
   * Get the [optionally formatted] [' . $aColumn['var'] . '] column value.
   *
   * @param      string $format The date/time format string (either date()-style or strftime()-style).
   *              If format is NULL, then the integer unix timestamp will be returned.
   * @return     mixed Formatted date/time value as string or integer unix timestamp (if format is NULL).
   * @throws     PropelException - if unable to convert the date/time to timestamp.
   */
  public function get' . $aColumn['phpName'] . '($format = "Y-m-d")
  {

    if ($this->' . $aColumn['var'] . ' === null || $this->' . $aColumn['var'] . ' === "") {
      return null;
    } elseif (!is_int($this->' . $aColumn['var'] . ')) {
      // a non-timestamp value was set externally, so we convert it
      if (($this->' . $aColumn['var'] . ' == "0000-00-00 00:00:00")
           || ($this->' . $aColumn['var'] . ' == "0000-00-00") || !$this->' . $aColumn['var'] . ') {
        $ts = "0";
      }
      else {
        $ts = strtotime($this->' . $aColumn['var'] . ');
      }
      if ($ts === -1 || $ts === false) { // in PHP 5.1 return value changes to FALSE
        throw new PropelException("Unable to parse value of [' . $aColumn['var'] . '] as date/time value: "
                                 . var_export($this->' . $aColumn['var'] . ', true));
      }
    } else {
      $ts = $this->' . $aColumn['var'] . ';
    }
    if ($format === null) {
      return $ts;
    } elseif (strpos($format, "%") !== false) {
      return strftime($format, $ts);
    } else {
      return date($format, $ts);
    }
  }';
                } else {
                    $aColumn['getFunction'] = '/**
   * Get the [' . $aColumn['var'] . '] column value.
   *
   * @return     string
   */
  public function get' . $aColumn['phpName'] . '()
  {

    return $this->' . $aColumn['var'] . ';
  }';
                }
                switch ($aField['FLD_TYPE']) {
                    case 'VARCHAR':
                    case 'TEXT':
                        $aColumn['setFunction'] = '// Since the native PHP type for this column is string,
    // we will cast the input to a string (if it is not).
    if ($v !== null && !is_string($v)) {
      $v = (string) $v;
    }

    if ($this->' . $aColumn['var'] . ' !== $v) {
      $this->' . $aColumn['var'] . ' = $v;
      $this->modifiedColumns[] = ' . $aData['className'] . 'Peer::' . $aColumn['name'] . ';
    }';
                        break;
                    case 'DATE':
                        $aColumn['setFunction'] = 'if ($v !== null && !is_int($v)) {
      // if($v == \'\')
      //   $ts = null;
     // else
       $ts = strtotime($v);
     if ($ts === -1 || $ts === false) { // in PHP 5.1 return value changes to FALSE
       //throw new PropelException("Unable to parse date/time value for [' . $aColumn['var'] . '] from input: "
       //                          . var_export($v, true));
     }
   } else {
     $ts = $v;
   }
   if ($this->' . $aColumn['var'] . ' !== $ts) {
     $this->' . $aColumn['var'] . ' = $ts;
     $this->modifiedColumns[] = ' . $aData['className'] . 'Peer::' . $aColumn['name'] . ';
   }';
                        break;
                    case 'INT':
                        $aColumn['setFunction'] = '// Since the native PHP type for this column is integer,
   // we will cast the input value to an int (if it is not).
   if ($v !== null && !is_int($v) && is_numeric($v)) {
     $v = (int) $v;
   }
   if ($this->' . $aColumn['var'] . ' !== $v || $v === 1) {
     $this->' . $aColumn['var'] . ' = $v;
     $this->modifiedColumns[] = ' . $aData['className'] . 'Peer::' . $aColumn['name'] . ';
   }';
                        break;
                    case 'FLOAT':
                        $aColumn['setFunction'] = 'if ($this->' . $aColumn['var'] . ' !== $v || $v === 0) {
     $this->' . $aColumn['var'] . ' = $v;
     $this->modifiedColumns[] = ' . $aData['className'] . 'Peer::' . $aColumn['name'] . ';
   }';
                        break;
                }
                $aColumns[] = $aColumn;
                if ($aField['FLD_KEY'] == 1 || $aField['FLD_KEY'] === 'on') {
                    $aPKs[] = $aColumn;
                } else {
                    $aNotPKs[] = $aColumn;
                }
                if ($aField['FLD_AUTO_INCREMENT'] == 1 || $aField['FLD_AUTO_INCREMENT'] === 'on') {
                    $aData['useIdGenerator'] = 'true';
                }
                $i++;
            }
            $oTP3 = new TemplatePower(PATH_TPL . 'additionalTables' . PATH_SEP . 'map'
                                    . PATH_SEP . 'TableMapBuilder.tpl');
            $oTP3->prepare();
            $oTP3->assignGlobal($aData);
            foreach ($aPKs as $iIndex => $aColumn) {
                $oTP3->newBlock('primaryKeys');
                $aKeys = array_keys($aColumn);
                foreach ($aKeys as $sKey) {
                    $oTP3->assign($sKey, $aColumn[$sKey]);
                }
            }
            $oTP3->gotoBlock('_ROOT');
            foreach ($aNotPKs as $iIndex => $aColumn) {
                $oTP3->newBlock('columnsWhitoutKeys');
                $aKeys = array_keys($aColumn);
                foreach ($aKeys as $sKey) {
                    $oTP3->assign($sKey, $aColumn[$sKey]);
                }
            }
            file_put_contents($sPath . PATH_SEP . 'map' . PATH_SEP . $sClassName
                           . 'MapBuilder.php', $oTP3->getOutputContent());
            $oTP4 = new TemplatePower(PATH_TPL . 'additionalTables' . PATH_SEP . 'om' . PATH_SEP . 'BaseTable.tpl');
            $oTP4->prepare();
            switch (count($aPKs)) {
                case 0:
                    $aData['getPrimaryKeyFunction'] = 'return null;';
                    $aData['setPrimaryKeyFunction'] = '';
                    break;
                case 1:
                    $aData['getPrimaryKeyFunction'] = 'return $this->get' . $aPKs[0]['phpName'] . '();';
                    $aData['setPrimaryKeyFunction'] = '$this->set' . $aPKs[0]['phpName'] . '($key);';
                    break;
                default:
                    $aData['getPrimaryKeyFunction'] = '$pks = array();' . "\n";
                    $aData['setPrimaryKeyFunction'] = '';
                    foreach ($aPKs as $iIndex => $aColumn) {
                        $aData['getPrimaryKeyFunction'] .= '$pks[' . $iIndex . '] = $this->get'
                                                         . $aColumn['phpName'] . '();' . "\n";
                        $aData['setPrimaryKeyFunction'] .= '$this->set' . $aColumn['phpName']
                                                         . '($keys[' . $iIndex . ']);' . "\n";
                    }
                    $aData['getPrimaryKeyFunction'] .= 'return $pks;' . "\n";
                    break;
            }
            $oTP4->assignGlobal($aData);
            foreach ($aColumns as $iIndex => $aColumn) {
                $oTP4->newBlock('allColumns1');
                $aKeys = array_keys($aColumn);
                foreach ($aKeys as $sKey) {
                    $oTP4->assign($sKey, $aColumn[$sKey]);
                }
                $oTP4->newBlock('allColumns2');
                $aKeys = array_keys($aColumn);
                foreach ($aKeys as $sKey) {
                    $oTP4->assign($sKey, $aColumn[$sKey]);
                }
                $oTP4->newBlock('allColumns3');
                $aKeys = array_keys($aColumn);
                foreach ($aKeys as $sKey) {
                    $oTP4->assign($sKey, $aColumn[$sKey]);
                }
                $oTP4->newBlock('allColumns4');
                $aKeys = array_keys($aColumn);
                foreach ($aKeys as $sKey) {
                    $oTP4->assign($sKey, $aColumn[$sKey]);
                }
                $oTP4->newBlock('allColumns5');
                $aKeys = array_keys($aColumn);
                foreach ($aKeys as $sKey) {
                    $oTP4->assign($sKey, $aColumn[$sKey]);
                }
                $oTP4->newBlock('allColumns6');
                $aKeys = array_keys($aColumn);
                foreach ($aKeys as $sKey) {
                    $oTP4->assign($sKey, $aColumn[$sKey]);
                }
                $oTP4->newBlock('allColumns7');
                $aKeys = array_keys($aColumn);
                foreach ($aKeys as $sKey) {
                    $oTP4->assign($sKey, $aColumn[$sKey]);
                }
                $oTP4->newBlock('allColumns8');
                $aKeys = array_keys($aColumn);
                foreach ($aKeys as $sKey) {
                    $oTP4->assign($sKey, $aColumn[$sKey]);
                }
                $oTP4->newBlock('allColumns9');
                $aKeys = array_keys($aColumn);
                foreach ($aKeys as $sKey) {
                    $oTP4->assign($sKey, $aColumn[$sKey]);
                }
            }
            $oTP4->gotoBlock('_ROOT');
            foreach ($aPKs as $iIndex => $aColumn) {
                $oTP4->newBlock('primaryKeys1');
                $aKeys = array_keys($aColumn);
                foreach ($aKeys as $sKey) {
                    $oTP4->assign($sKey, $aColumn[$sKey]);
                }
            }
            $oTP4->gotoBlock('_ROOT');
            foreach ($aPKs as $iIndex => $aColumn) {
                $oTP4->newBlock('primaryKeys2');
                $aKeys = array_keys($aColumn);
                foreach ($aKeys as $sKey) {
                    $oTP4->assign($sKey, $aColumn[$sKey]);
                }
            }
            $oTP4->gotoBlock('_ROOT');
            foreach ($aNotPKs as $iIndex => $aColumn) {
                $oTP4->newBlock('columnsWhitoutKeys');
                $aKeys = array_keys($aColumn);
                foreach ($aKeys as $sKey) {
                    $oTP4->assign($sKey, $aColumn[$sKey]);
                }
            }
            file_put_contents($sPath . PATH_SEP . 'om' . PATH_SEP . 'Base'
                            . $sClassName . '.php', $oTP4->getOutputContent());
            $oTP5 = new TemplatePower(PATH_TPL . 'additionalTables' . PATH_SEP . 'om' . PATH_SEP . 'BaseTablePeer.tpl');
            $oTP5->prepare();
            $sKeys = '';
            foreach ($aPKs as $iIndex => $aColumn) {
                $sKeys .= '$' . $aColumn['var'] . ', ';
            }
            $sKeys = substr($sKeys, 0, -2);
            //$sKeys = '$pm_unique_id';
            if ($sKeys != '') {
                $aData['sKeys'] = $sKeys;
            } else {
                $aData['sKeys'] = '$DUMMY';
            }
            $oTP5->assignGlobal($aData);
            foreach ($aColumns as $iIndex => $aColumn) {
                $oTP5->newBlock('allColumns1');
                $aKeys = array_keys($aColumn);
                foreach ($aKeys as $sKey) {
                    $oTP5->assign($sKey, $aColumn[$sKey]);
                }
                $oTP5->newBlock('allColumns2');
                $aKeys = array_keys($aColumn);
                foreach ($aKeys as $sKey) {
                    $oTP5->assign($sKey, $aColumn[$sKey]);
                }
                $oTP5->newBlock('allColumns3');
                $aKeys = array_keys($aColumn);
                foreach ($aKeys as $sKey) {
                    $oTP5->assign($sKey, $aColumn[$sKey]);
                }
                $oTP5->newBlock('allColumns4');
                $aKeys = array_keys($aColumn);
                foreach ($aKeys as $sKey) {
                    $oTP5->assign($sKey, $aColumn[$sKey]);
                }
                $oTP5->newBlock('allColumns5');
                $aKeys = array_keys($aColumn);
                foreach ($aKeys as $sKey) {
                    $oTP5->assign($sKey, $aColumn[$sKey]);
                }
                $oTP5->newBlock('allColumns6');
                $aKeys = array_keys($aColumn);
                foreach ($aKeys as $sKey) {
                    $oTP5->assign($sKey, $aColumn[$sKey]);
                }
                $oTP5->newBlock('allColumns7');
                $aKeys = array_keys($aColumn);
                foreach ($aKeys as $sKey) {
                    $oTP5->assign($sKey, $aColumn[$sKey]);
                }
                $oTP5->newBlock('allColumns8');
                $aKeys = array_keys($aColumn);
                foreach ($aKeys as $sKey) {
                    $oTP5->assign($sKey, $aColumn[$sKey]);
                }
                $oTP5->newBlock('allColumns9');
                $aKeys = array_keys($aColumn);
                foreach ($aKeys as $sKey) {
                    $oTP5->assign($sKey, $aColumn[$sKey]);
                }
                $oTP5->newBlock('allColumns10');
                $aKeys = array_keys($aColumn);
                foreach ($aKeys as $sKey) {
                    $oTP5->assign($sKey, $aColumn[$sKey]);
                }
            }
            $oTP5->gotoBlock('_ROOT');
            foreach ($aPKs as $iIndex => $aColumn) {
                $oTP5->newBlock('primaryKeys1');
                $aKeys = array_keys($aColumn);
                foreach ($aKeys as $sKey) {
                    $oTP5->assign($sKey, $aColumn[$sKey]);
                }
            }
            foreach ($aPKs as $iIndex => $aColumn) {
                $oTP5->newBlock('primaryKeys2');
                $aKeys = array_keys($aColumn);
                foreach ($aKeys as $sKey) {
                    $oTP5->assign($sKey, $aColumn[$sKey]);
                }
            }
            file_put_contents($sPath . PATH_SEP . 'om' . PATH_SEP . 'Base'
                            . $sClassName . 'Peer.php', $oTP5->getOutputContent());
        } catch (Exception $oError) {
            throw($oError);
        }
    }
}

