<?php
namespace ProcessMaker\BusinessModel;

use G;
use AdditionalTables;
use Fields;
use DynaformHandler;

class Table
{
    /**
     * List of Tables in process
     * @var string $pro_uid. Uid for process
     * @var string $reportFlag. If is report table
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     *
     * @return array
     */
    public function getTables($pro_uid = '', $reportFlag = false)
    {
        //VALIDATION
        if ($reportFlag) {
            $pro_uid = $this->validateProUid($pro_uid);
        }

        $reportTables = array();
        $oCriteria = new \Criteria('workflow');
        $oCriteria->addSelectColumn(\AdditionalTablesPeer::ADD_TAB_UID);
        $oCriteria->add(\AdditionalTablesPeer::PRO_UID, $pro_uid, \Criteria::EQUAL);
        $oDataset = \AdditionalTablesPeer::doSelectRS($oCriteria);
        $oDataset->setFetchmode(\ResultSet::FETCHMODE_ASSOC);
        while ($oDataset->next()) {
            $row = $oDataset->getRow();
            $reportTables[] = $this->getTable($row['ADD_TAB_UID'], $pro_uid, $reportFlag, false);
        }

        return $reportTables;
    }

    /**
     * Get data for Table
     * @var string $tab_uid. Uid for table
     * @var string $pro_uid. Uid for process
     * @var string $reportFlag. If is report table
     * @var string $validate. Flag for validate
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     *
     * @return array
     */
    public function getTable($tab_uid, $pro_uid = '', $reportFlag = false, $validate = true)
    {
        //VALIDATION
        if ($validate) {
            if ($reportFlag) {
                $pro_uid = $this->validateProUid($pro_uid);
                $tabData['PRO_UID'] = $pro_uid;
            }
            $tab_uid = $this->validateTabUid($tab_uid, $reportFlag);
        }

        $tabData = array();
        $additionalTables = new AdditionalTables();

        // TABLE PROPERTIES
        $table = $additionalTables->load( $tab_uid, true );
        $table['DBS_UID'] = $table['DBS_UID'] == null || $table['DBS_UID'] == '' ? 'workflow' : $table['DBS_UID'];

        // TABLE NUM ROWS DATA
        $tableData = $additionalTables->getAllData( $tab_uid, 0, 2 );

        if ($reportFlag) {
            $tabData['REP_UID']             = $tab_uid;
            $tabData['REP_TAB_NAME']        = $table['ADD_TAB_NAME'];
            $tabData['REP_TAB_DESCRIPTION'] = $table['ADD_TAB_DESCRIPTION'];
            $tabData['REP_TAB_CLASS_NAME']  = $table['ADD_TAB_CLASS_NAME'];
            $tabData['REP_TAB_CONNECTION']  = $table['DBS_UID'];
            $tabData['REP_TAB_TYPE']        = $table['ADD_TAB_TYPE'];
            $tabData['REP_TAB_GRID']        = $table['ADD_TAB_GRID'];
            $tabData['REP_NUM_ROWS']        = $tableData['count'];
        } else {
            $tabData['PMT_UID']             = $tab_uid;
            $tabData['PMT_TAB_NAME']        = $table['ADD_TAB_NAME'];
            $tabData['PMT_TAB_DESCRIPTION'] = $table['ADD_TAB_DESCRIPTION'];
            $tabData['PMT_TAB_CLASS_NAME']  = $table['ADD_TAB_CLASS_NAME'];
            $tabData['PMT_NUM_ROWS']        = $tableData['count'];
        }

        // TABLE FIELDS
        $hiddenFields = array(
            'fld_foreign_key', 'fld_foreign_key_table',
            'fld_dyn_name', 'fld_dyn_uid', 'fld_filter'
        );
        foreach ($table['FIELDS'] as $valField) {
            $fieldTemp = array();
            $fieldTemp = array_change_key_case($valField, CASE_LOWER);
            foreach ($fieldTemp as $key => $value) {
                if (in_array($key, $hiddenFields)) {
                    unset($fieldTemp[$key]);
                }
            }
            $tabData['FIELDS'][] = $fieldTemp;
        }

        $tabData = array_change_key_case($tabData, CASE_LOWER);
        return $tabData;
    }

    /**
     * Generate Data for Report Table
     * @var string $pro_uid. Uid for process
     * @var string $rep_uid. Uid for report table
     * @var string $validate. Flag for validate
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     *
     * @return void
     */
    public function generateDataReport($pro_uid, $rep_uid, $validate = true)
    {
        if ($validate) {
            $pro_uid = $this->validateProUid($pro_uid);
            $rep_uid = $this->validateTabUid($rep_uid);
        }

        $additionalTables = new AdditionalTables();
        $table = $additionalTables->load($rep_uid);
        $additionalTables->populateReportTable(
            $table['ADD_TAB_NAME'],
            \PmTable::resolveDbSource( $table['DBS_UID'] ),
            $table['ADD_TAB_TYPE'],
            $table['PRO_UID'],
            $table['ADD_TAB_GRID'],
            $table['ADD_TAB_UID']
        );
    }

    /**
     * Get data for Table
     * @var string $tab_uid. Uid for table
     * @var string $pro_uid. Uid for process
     * @var string $reportFlag. If is report table
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     *
     * @return array
     */
    public function getTableData($tab_uid, $pro_uid = '', $filter = null, $reportFlag = false, $search = '')
    {
        //Validation
        $inputFilter = new \InputFilter();
        $filter = $inputFilter->sanitizeInputValue($filter, 'nosql');

        //VALIDATION
        if ($reportFlag) {
            $pro_uid = $this->validateProUid($pro_uid);
        }
        $tab_uid = $this->validateTabUid($tab_uid, $reportFlag);

        $additionalTables = new AdditionalTables();
        $table  = $additionalTables->load($tab_uid, true);
        $result = $additionalTables->getAllData($tab_uid, null, null, null, $filter, false, $search);
        $primaryKeys = $additionalTables->getPrimaryKeys();
        if (is_array($result['rows'])) {
            foreach ($result['rows'] as $i => $row) {
                $result['rows'][$i] = array_change_key_case($result['rows'][$i], CASE_LOWER);
                $primaryKeysValues = array ();
                foreach ($primaryKeys as $key) {
                    $primaryKeysValues[] = isset( $row[$key['FLD_NAME']] ) ? $row[$key['FLD_NAME']] : '';
                }

                $result['rows'][$i]['__index__'] = G::encrypt( implode( ',', $primaryKeysValues ), 'pmtable' );
            }
        } else {
            $result['rows'] = array();
        }
        return $result;
    }

    /**
     * Save Data for Table
     * @var string $tab_data. Data for table
     * @var string $pro_uid. Uid for process
     * @var string $reportFlag. If is report table
     * @var string $createRep. Flag for create table
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     *
     * @return array
     */
    public function saveTable($tab_data, $pro_uid = '', $reportFlag = false, $createRep = true)
    {
        // CHANGE CASE UPPER TABLE
        $fieldsValidate = array();
        $tableName      = '';
        $tableCon       = 'workflow';
        $dataValidate   = array_change_key_case($tab_data, CASE_UPPER);
        $oAdditionalTables = new AdditionalTables();
        // VALIDATION TABLE DATA
        if ($reportFlag) {
            $pro_uid = $this->validateProUid($pro_uid);
            $dataValidate['TAB_UID']            = (isset($dataValidate['REP_UID'])) ? $dataValidate['REP_UID'] : '';
            $dataValidate['PRO_UID']            = $pro_uid;
            $dataValidate['REP_TAB_NAME']       = $this->validateTabName($dataValidate['REP_TAB_NAME'], $reportFlag);
            $tempRepTabName                     = $dataValidate['REP_TAB_CONNECTION'];
            $dataValidate['REP_TAB_CONNECTION'] = $this->validateRepConnection($tempRepTabName, $pro_uid);
            if ($dataValidate['REP_TAB_TYPE'] == 'GRID') {
                $dataValidate['REP_TAB_GRID']   = $this->validateRepGrid($dataValidate['REP_TAB_GRID'], $pro_uid);
            }
            $fieldsValidate = $this->getDynafields($pro_uid, $dataValidate['REP_TAB_TYPE'], $dataValidate['REP_TAB_GRID']);
            if (empty($fieldsValidate)) {
                $fieldsValidate['NAMES'] = array();
                $fieldsValidate['INDEXS'] = array();
                $fieldsValidate['UIDS'] = array();
            }
            $repTabClassName = $oAdditionalTables->getPHPName($dataValidate['REP_TAB_NAME']);
            $tableName = $dataValidate['REP_TAB_NAME'];
            $tableCon  = $dataValidate['REP_TAB_CONNECTION'];
        } else {
            $dataValidate['TAB_UID']            = (isset($dataValidate['PMT_UID'])) ? $dataValidate['PMT_UID'] : '';
            $dataValidate['PMT_TAB_NAME']       = $this->validateTabName($dataValidate['PMT_TAB_NAME']);
            $dataValidate['PMT_TAB_CONNECTION'] = 'workflow';
            $repTabClassName = $oAdditionalTables->getPHPName($dataValidate['PMT_TAB_NAME']);
            $tableName = $dataValidate['PMT_TAB_NAME'];
            $tableCon  = $dataValidate['PMT_TAB_CONNECTION'];
        }

        // VERIFY COLUMNS TABLE
        $oFields = new Fields();
        $columns = $dataValidate['FIELDS'];

        // Reserved Words Table, Field, Sql
        $reservedWords = array ('ALTER','CLOSE','COMMIT','CREATE','DECLARE','DELETE',
            'DROP','FETCH','FUNCTION','GRANT','INDEX','INSERT','OPEN','REVOKE','ROLLBACK',
            'SELECT','SYNONYM','TABLE','UPDATE','VIEW','APP_UID','ROW','PMTABLE');
        $reservedWordsPhp = array ('case','catch','cfunction','class','clone','const','continue',
            'declare','default','do','else','elseif','enddeclare','endfor','endforeach','endif',
            'endswitch','endwhile','extends','final','for','foreach','function','global','goto',
            'if','implements','interface','instanceof','private','namespace','new','old_function',
            'or','throw','protected','public','static','switch','xor','try','use','var','while');
        $reservedWordsSql = G::reservedWordsSql();

        if ($reportFlag) {
            $defaultColumns = $this->getReportTableDefaultColumns($dataValidate['REP_TAB_TYPE']);
            $columns = array_merge( $defaultColumns, $columns );
        }

        // validations
        if ($createRep) {
            if (is_array( $oAdditionalTables->loadByName( $tableName ) )) {
                throw new \Exception(G::loadTranslation('ID_PMTABLE_ALREADY_EXISTS', array($tableName)));
            }
        }
        if (in_array( strtoupper( $tableName ), $reservedWords ) ||
            in_array( strtoupper( $tableName ), $reservedWordsSql )) {
            throw (new \Exception(G::LoadTranslation("ID_PMTABLE_INVALID_NAME", array($tableName))));
        }

        //backward compatility
        $flagKey = false;
        $columnsStd = array();
        foreach ($columns as $i => $column) {
            if (isset($columns[$i]['fld_dyn'])) {
                $columns[$i]['fld_dyn'] = ($reportFlag) ? $columns[$i]['fld_dyn'] : '';
                $columns[$i]['field_dyn'] = $columns[$i]['fld_dyn'];
                unset($columns[$i]['fld_dyn']);
            } else {
                $columns[$i]['fld_dyn'] = '';
            }

            if (isset($columns[$i]['fld_name'])) {
                $columns[$i]['field_name'] = G::toUpper($columns[$i]['fld_name']);
                unset($columns[$i]['fld_name']);
            }
            if (isset($columns[$i]['fld_label'])) {
                $columns[$i]['field_label'] = $columns[$i]['fld_label'];
                unset($columns[$i]['fld_label']);
            }
            if (isset($columns[$i]['fld_type'])) {
                $columns[$i]['field_type'] = $columns[$i]['fld_type'];
                unset($columns[$i]['fld_type']);
            }
            if (isset($columns[$i]['fld_size'])) {
                $columns[$i]['field_size'] = $columns[$i]['fld_size'];
                if (!is_int($columns[$i]['field_size'])) {
                    throw (new \Exception("The property fld_size: '". $columns[$i]['field_size'] . "' is incorrect numeric value."));
                } else {
                    $columns[$i]['field_size'] = (int)$columns[$i]['field_size'];
                }
                unset($columns[$i]['fld_size']);
            }
            if (isset($columns[$i]['fld_key'])) {
                $columns[$i]['field_key'] = $columns[$i]['fld_key'];
                unset($columns[$i]['fld_key']);
            }
            if (isset($columns[$i]['fld_null'])) {
                $columns[$i]['field_null'] = $columns[$i]['fld_null'];
                unset($columns[$i]['fld_null']);
            }
            if (isset($columns[$i]['fld_autoincrement'])) {
                $columns[$i]['field_autoincrement'] = $columns[$i]['fld_autoincrement'];
                unset($columns[$i]['fld_autoincrement']);
            }

            // VALIDATIONS
            if (in_array(strtoupper($columns[$i]['field_name']), $reservedWordsSql) ||
                in_array( strtolower( $columns[$i]['field_name']), $reservedWordsPhp ) ||
                $columns[$i]['field_name'] == '') {
                throw (new \Exception("The property fld_name: '". $columns[$i]['field_name'] . "' is incorrect value."));
            }
            if ($columns[$i]['field_label'] == '') {
                throw (new \Exception("The property fld_label: '". $columns[$i]['field_label'] . "' is incorrect value."));
            }
            $columns[$i]['field_type'] = $this->validateFldType($columns[$i]['field_type']);
            if (isset($columns[$i]['field_autoincrement']) && $columns[$i]['field_autoincrement']) {
                $typeCol = $columns[$i]['field_type'];
                if (! ($typeCol === 'INTEGER' || $typeCol === 'TINYINT' || $typeCol === 'SMALLINT' || $typeCol === 'BIGINT')) {
                    $columns[$i]['field_autoincrement'] = false;
                }
            }
            if (isset($columns[$i]['field_dyn']) && $columns[$i]['field_dyn'] != '') {
                $res = array_search($columns[$i]['field_dyn'], $fieldsValidate['NAMES']);
                if ($res === false) {
                    throw (new \Exception("The property fld_dyn: '".$columns[$i]['field_dyn']."' is incorrect."));
                } else {
                    $columns[$i]['_index']    = $fieldsValidate['INDEXS'][$res];
                    $columns[$i]['field_uid'] = $fieldsValidate['UIDS'][$res];
                }
            }

            $temp = new \stdClass();
            foreach ($columns[$i] as $key => $valCol) {
                eval('$temp->' . str_replace('fld', 'field', $key) . " = '" . $valCol . "';");
            }
            $temp->uid = (isset($temp->uid)) ? $temp->uid : '';
            $temp->_index = (isset($temp->_index)) ? $temp->_index : '';
            $temp->field_uid = (isset($temp->field_uid)) ? $temp->field_uid : '';
            $temp->field_dyn = (isset($temp->field_dyn)) ? $temp->field_dyn : '';

            $temp->field_key = (isset($temp->field_key)) ? $temp->field_key : 0;
            $temp->field_null = (isset($temp->field_null)) ? $temp->field_null : 1;
            $temp->field_dyn = (isset($temp->field_dyn)) ? $temp->field_dyn : '';
            $temp->field_filter = (isset($temp->field_filter)) ? $temp->field_filter : 0;
            $temp->field_autoincrement = (isset($temp->field_autoincrement)) ? $temp->field_autoincrement : 0;

            if (!$reportFlag) {
                unset($temp->_index);
                unset($temp->field_filter);
            }
            if ($temp->field_key == 1 || $temp->field_key == true) {
                $flagKey = true;
            }
            $columnsStd[$i] = $temp;
        }
        if (!$flagKey) {
            throw (new \Exception("The fields must have a key 'fld_key'"));
        }

        $pmTable = new \PmTable($tableName);
        $pmTable->setDataSource($tableCon);
        $pmTable->setColumns($columnsStd);
        $pmTable->setAlterTable(true);
        if (!$createRep) {
            $pmTable->setKeepData(true);
        }
        $pmTable->build();
        $buildResult = ob_get_contents();
        ob_end_clean();
        unset($buildResult);

        // Updating additional table struture information
        if ($reportFlag) {
            $addTabData = array(
                'ADD_TAB_UID' => $dataValidate['TAB_UID'],
                'ADD_TAB_NAME' => $dataValidate['REP_TAB_NAME'],
                'ADD_TAB_CLASS_NAME' => $repTabClassName,
                'ADD_TAB_DESCRIPTION' => $dataValidate['REP_TAB_DSC'],
                'ADD_TAB_PLG_UID' => '',
                'DBS_UID' => ($dataValidate['REP_TAB_CONNECTION'] ? $dataValidate['REP_TAB_CONNECTION'] : 'workflow'),
                'PRO_UID' => $dataValidate['PRO_UID'],
                'ADD_TAB_TYPE' => $dataValidate['REP_TAB_TYPE'],
                'ADD_TAB_GRID' => $dataValidate['REP_TAB_GRID']
            );
        } else {
            $addTabData = array(
                'ADD_TAB_UID' => $dataValidate['TAB_UID'],
                'ADD_TAB_NAME' => $dataValidate['PMT_TAB_NAME'],
                'ADD_TAB_CLASS_NAME' => $repTabClassName,
                'ADD_TAB_DESCRIPTION' => $dataValidate['PMT_TAB_DSC'],
                'ADD_TAB_PLG_UID' => '',
                'DBS_UID' => ($dataValidate['PMT_TAB_CONNECTION'] ? $dataValidate['PMT_TAB_CONNECTION'] : 'workflow'),
                'PRO_UID' => '',
                'ADD_TAB_TYPE' => '',
                'ADD_TAB_GRID' => ''
            );
        }
        if ($createRep) {
            //new report table
            //create record
            $addTabUid = $oAdditionalTables->create( $addTabData );
        } else {
            //editing report table
            //updating record
            $addTabUid = $dataValidate['TAB_UID'];
            $oAdditionalTables->update( $addTabData );

            //removing old data fields references
            $oCriteria = new \Criteria( 'workflow' );
            $oCriteria->add( \FieldsPeer::ADD_TAB_UID, $dataValidate['TAB_UID'] );
            \FieldsPeer::doDelete( $oCriteria );
        }
        // Updating pmtable fields
        foreach ($columnsStd as $i => $column) {
            $column = (array)$column;
            $field = array (
                'FLD_UID' => $column['uid'],
                'FLD_INDEX' => $i,
                'ADD_TAB_UID' => $addTabUid,
                'FLD_NAME' => $column['field_name'],
                'FLD_DESCRIPTION' => $column['field_label'],
                'FLD_TYPE' => $column['field_type'],
                'FLD_SIZE' => (!isset($column['field_size']) || $column['field_size'] == '') ? null : $column['field_size'],
                'FLD_NULL' => $column['field_null'] ? 1 : 0,
                'FLD_AUTO_INCREMENT' => $column['field_autoincrement'] ? 1 : 0,
                'FLD_KEY' => $column['field_key'] ? 1 : 0,
                'FLD_FOREIGN_KEY' => 0,
                'FLD_FOREIGN_KEY_TABLE' => '',
                'FLD_DYN_NAME' => $column['field_dyn'],
                'FLD_DYN_UID' => $column['field_uid'],
                'FLD_FILTER' => (isset($column['field_filter']) && $column['field_filter']) ? 1 : 0
            );
            $oFields->create( $field );
        }
        if ($reportFlag) {
            $rep_uid   = $addTabUid;
            $this->generateDataReport($pro_uid, $rep_uid, false);
        }
        if ($createRep) {
            $tab_uid   = $addTabUid;
            return $this->getTable($tab_uid, $pro_uid, $reportFlag, false);
        }
    }

    /**
     * Save Data for PmTable
     * @var string $pmt_uid. Uid for PmTable
     * @var string $pmt_data. Data for rows of PmTable
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     *
     * @return array
     */
    public function saveTableData ($pmt_uid, $pmt_data)
    {
        $pmt_uid = $this->validateTabUid($pmt_uid, false);
        $rows = $pmt_data;

        $additionalTables = new AdditionalTables();
        $table = $additionalTables->load($pmt_uid, true);
        $primaryKeys = $additionalTables->getPrimaryKeys();

        $className = $table['ADD_TAB_CLASS_NAME'];
        $classPeerName = $className . 'Peer';
        $row = (array)$rows;

        $row = array_merge( array_change_key_case( $row, CASE_LOWER ), array_change_key_case( $row, CASE_UPPER ) );
        $toSave = false;

        if (! file_exists( PATH_WORKSPACE . 'classes/' . $className . '.php' )) {
            throw new Exception( 'Create::' . G::loadTranslation( 'ID_PMTABLE_CLASS_DOESNT_EXIST', $className ) );
        }

        require_once PATH_WORKSPACE . 'classes/' . $className . '.php';
        eval( '$obj = new ' . $className . '();' );
        eval( '$con = Propel::getConnection(' . $classPeerName . '::DATABASE_NAME);' );
        $obj->fromArray( $row, \BasePeer::TYPE_FIELDNAME );
        if ($obj->validate()) {
            $affectedRows = $obj->save();
            if ($affectedRows == 0) {
                throw (new \Exception("The value of key column is required"));
            }
            $toSave = true;
            $primaryKeysValues = array ();
            foreach ($primaryKeys as $primaryKey) {
                $method = 'get' . AdditionalTables::getPHPName( $primaryKey['FLD_NAME'] );
                $primaryKeysValues[] = $obj->$method();
            }
        } else {
            $msg = '';
            foreach ($obj->getValidationFailures() as $objValidationFailure) {
                $msg .= $objValidationFailure->getMessage() . "\n";
            }
            throw new \Exception( G::LoadTranslation('ID_ERROR_TRYING_INSERT'). '"' . $table['ADD_TAB_NAME'] . "\"\n" . $msg );
        }

        $index = G::encrypt( implode( ',', $primaryKeysValues ), 'pmtable' );
        $rep = $obj->toArray(\BasePeer::TYPE_FIELDNAME);
        $rep = array_change_key_case($rep, CASE_LOWER);
        $rep['__index__'] = $index;
        return $rep;
    }

    /**
     * Update Data for Table
     * @var string $tab_data. Data for table
     * @var string $pro_uid. Uid for process
     * @var string $reportFlag. If is report table
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     *
     * @return void
     */
    public function updateTable($tab_data, $pro_uid = '', $reportFlag = false)
    {
        if ($reportFlag) {
            $tab_uid = $tab_data['rep_uid'];
            $pro_uid = $this->validateProUid($pro_uid);
        } else {
            $tab_uid = $tab_data['pmt_uid'];
        }
        $tab_uid = $this->validateTabUid($tab_uid, $reportFlag);

        $dataValidate =  array();
        $oCriteria = new \Criteria('workflow');
        $oCriteria->add(\AdditionalTablesPeer::ADD_TAB_UID, $tab_uid, \Criteria::EQUAL);
        $oDataset = \AdditionalTablesPeer::doSelectRS($oCriteria);
        $oDataset->setFetchmode(\ResultSet::FETCHMODE_ASSOC);

        if ($oDataset->next()) {
            $row = $oDataset->getRow();
            if ($reportFlag) {
                $dataValidate['rep_uid']  = $tab_uid;
                $dataValidate['rep_tab_name'] = $row['ADD_TAB_NAME'];
                $dataValidate['rep_tab_dsc']  = $tab_data['rep_tab_dsc'];
                $dataValidate['rep_tab_connection'] = $row['DBS_UID'];
                $dataValidate['rep_tab_type'] = $row['ADD_TAB_TYPE'];
                $dataValidate['rep_tab_grid'] = '';
                if (strpos($row['ADD_TAB_GRID'], '-')) {
                    list($gridName, $gridId) = explode( '-', $row['ADD_TAB_GRID'] );
                    $dataValidate['rep_tab_grid'] = $gridId;
                }
            } else {
                $dataValidate['pmt_uid']  = $tab_uid;
                $dataValidate['pmt_tab_name'] = $row['ADD_TAB_NAME'];
                $dataValidate['pmt_tab_dsc']  = $tab_data['pmt_tab_dsc'];
            }
            $dataValidate['fields']       = $tab_data['fields'];
        } else {
            if ($reportFlag) {
                throw (new \Exception("The property rep_uid: '$tab_uid' is incorrect."));
            } else {
                throw (new \Exception("The property pmt_uid: '$tab_uid' is incorrect."));
            }
        }
        $this->saveTable($dataValidate, $pro_uid, $reportFlag, false);
    }

    /**
     * Update Data for PmTable
     * @var string $pmt_uid. Uid for PmTable
     * @var string $pmt_data. Data for rows of PmTable
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     *
     * @return void
     */
    public function updateTableData($pmt_uid, $pmt_data)
    {
        $pmt_uid = $this->validateTabUid($pmt_uid, false);
        $rows = $pmt_data;
        $rows = array_merge( array_change_key_case( $rows, CASE_LOWER ), array_change_key_case( $rows, CASE_UPPER ) );

        $oAdditionalTables = new AdditionalTables();
        $table = $oAdditionalTables->load( $pmt_uid, true );
        $primaryKeys = $oAdditionalTables->getPrimaryKeys( 'keys' );

        foreach ($primaryKeys as $value) {
            if (!isset($rows[$value])) {
                throw (new \Exception("The field for column '$value' is required"));
            } else {
                $params[] = is_numeric($rows[$value]) ? $rows[$value] : "'".$rows[$value]."'";
            }
        }
        $className = $table['ADD_TAB_CLASS_NAME'];
        $classPeerName = $className . 'Peer';
        $sPath = PATH_DB . SYS_SYS . PATH_SEP . 'classes' . PATH_SEP;
        if (! file_exists( $sPath . $className . '.php' )) {
            throw new \Exception( 'Update:: ' . G::loadTranslation( 'ID_PMTABLE_CLASS_DOESNT_EXIST', $className ) );
        }
        require_once $sPath . $className . '.php';

        $obj = null;
        eval( '$obj = ' . $classPeerName . '::retrieveByPk(' . implode( ',', $params ) . ');' );
        if (is_object( $obj )) {
            foreach ($rows as $key => $value) {
                // validation, don't modify primary keys
                if (in_array(G::toUpper($key), $primaryKeys ) || in_array( G::toLower($key), $primaryKeys )) {
                    unset($rows[$key]);
                }
                $action = 'set' . AdditionalTables::getPHPName( $key );
                $obj->$action( $value );
            }
            if ($r = $obj->validate()) {
                $obj->save();
                $result = true;
            } else {
                $msg = '';
                foreach ($obj->getValidationFailures() as $objValidationFailure) {
                    $msg .= $objValidationFailure->getMessage() . "\n";
                }
                throw new \Exception( $msg );
            }
        } else {
            throw (new \Exception("The key " . implode(',', $params) . " not exist"));
        }
    }

    /**
     * Delete Table
     * @var string $tab_uid. Uid for table
     * @var string $pro_uid. Uid for process
     * @var string $reportFlag. If is report table
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     *
     * @return void
     */
    public function deleteTable($tab_uid, $pro_uid = '', $reportFlag = false)
    {
        if ($reportFlag) {
            $pro_uid = $this->validateProUid($pro_uid);
        }
        $tab_uid = $this->validateTabUid($tab_uid, $reportFlag);

        $at = new AdditionalTables();
        $table = $at->load( $tab_uid );

        if (! isset( $table )) {
            require_once 'classes/model/ReportTable.php';
            $rtOld = new ReportTable();
            $existReportTableOld = $rtOld->load($tab_uid);
            if (count($existReportTableOld) == 0) {
                throw new Exception(G::LoadTranslation('ID_TABLE_NOT_EXIST_SKIPPED'));
            }
        }
        $at->deleteAll($tab_uid);
    }

    /**
     * Delete Data for PmTable
     * @var string $pmt_uid. Uid for PmTable
     * @var string $rows. Data for rows of PmTable
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     *
     * @return void
     */
    public function deleteTableData($pmt_uid, $rows)
    {
        $pmt_uid = $this->validateTabUid($pmt_uid, false);
        $rows = array_merge( array_change_key_case( $rows, CASE_LOWER ), array_change_key_case( $rows, CASE_UPPER ) );

        $oAdditionalTables = new AdditionalTables();
        $table = $oAdditionalTables->load( $pmt_uid, true );
        $primaryKeys = $oAdditionalTables->getPrimaryKeys( 'keys' );

        foreach ($primaryKeys as $value) {
            if (!isset($rows[$value])) {
                throw (new \Exception("The field for column '$value' is required"));
            } else {
                $params[] = is_numeric($rows[$value]) ? $rows[$value] : "'".$rows[$value]."'";
            }
        }
        $className = $table['ADD_TAB_CLASS_NAME'];
        $classPeerName = $className . 'Peer';
        $sPath = PATH_DB . SYS_SYS . PATH_SEP . 'classes' . PATH_SEP;
        if (! file_exists( $sPath . $className . '.php' )) {
            throw new \Exception( 'Update:: ' . G::loadTranslation( 'ID_PMTABLE_CLASS_DOESNT_EXIST', $className ) );
        }
        require_once $sPath . $className . '.php';

        $obj = null;
        eval( '$obj = ' . $classPeerName . '::retrieveByPk(' . implode( ',', $params ) . ');' );
        if (is_object( $obj )) {
            foreach ($rows as $key => $value) {
                // validation, don't modify primary keys
                if (in_array(G::toUpper($key), $primaryKeys ) || in_array( G::toLower($key), $primaryKeys )) {
                    unset($rows[$key]);
                }
                $action = 'set' . AdditionalTables::getPHPName( $key );
                $obj->$action( $value );
            }
            if ($r = $obj->validate()) {
                $obj->delete();
            } else {
                $msg = '';
                foreach ($obj->getValidationFailures() as $objValidationFailure) {
                    $msg .= $objValidationFailure->getMessage() . "\n";
                }
                throw new \Exception( $msg );
            }
        } else {
            throw (new \Exception("The key " . implode(',', $params) . " not exist"));
        }
    }

    /**
     * Get Fields of Dynaforms
     * @var string $pro_uid. Uid for Process
     * @var string $rep_tab_type. Type the Report Table
     * @var string $rep_tab_grid. Uid for Grid
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     *
     * @return array
     */
    public function getDynafields ($pro_uid, $rep_tab_type, $rep_tab_grid = '')
    {

        $dynFields = array();
        $aFields   = array();
        $aFields['FIELDS']  = array();
        $aFields['PRO_UID'] = $pro_uid;

        if (isset( $rep_tab_type ) && $rep_tab_type == 'GRID') {
            list ($gridName, $gridId) = explode( '-', $rep_tab_grid );
            $dynFields = $this->_getDynafields($pro_uid, 'grid', $gridId);
        } else {
            $dynFields = $this->_getDynafields($pro_uid, 'xmlform');
        }

        $fieldReturn = array();
        foreach ($dynFields as $value) {
            $fieldReturn['NAMES'][]  = $value['FIELD_NAME'];
            $fieldReturn['UIDS'][]   = $value['FIELD_UID'];
            $fieldReturn['INDEXS'][] = $value['_index'];
        }
        return $fieldReturn;
    }

    /**
     * Get Fields of Dynaforms in xmlform
     * @var string $pro_uid. Uid for Process
     * @var string $type. Type the form
     * @var string $rep_tab_grid. Uid for Grid
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     *
     * @return array
     */
    public function _getDynafields ($pro_uid, $type = 'xmlform', $rep_tab_grid = '')
    {


        $oCriteria = new \Criteria( 'workflow' );
        $oCriteria->addSelectColumn( \DynaformPeer::DYN_FILENAME );
        $oCriteria->add( \DynaformPeer::PRO_UID, $pro_uid );
        $oCriteria->add( \DynaformPeer::DYN_TYPE, $type );

        if ($rep_tab_grid != '') {
            $oCriteria->add( \DynaformPeer::DYN_UID, $rep_tab_grid );
        }

        $oDataset = \DynaformPeer::doSelectRS( $oCriteria );
        $oDataset->setFetchmode( \ResultSet::FETCHMODE_ASSOC );

        $fields      = array();
        $fieldsNames = array();
        $labelFieldsTypeList = array('dropdown','radiogroup');
        $excludeFieldsList   = array(
            'title',
            'subtitle',
            'link',
            'file',
            'button',
            'reset',
            'submit',
            'listbox',
            'checkgroup',
            'grid',
            'javascript',
            ''
        );

        $index = 0;
        while ($oDataset->next()) {
            $aRow = $oDataset->getRow();
            if (file_exists( PATH_DYNAFORM . PATH_SEP . $aRow['DYN_FILENAME'] . '.xml' )) {
                $dynaformHandler = new DynaformHandler( PATH_DYNAFORM . $aRow['DYN_FILENAME'] . '.xml' );
                $nodeFieldsList = $dynaformHandler->getFields();

                foreach ($nodeFieldsList as $node) {
                    $arrayNode = $dynaformHandler->getArray( $node );
                    $fieldName = $arrayNode['__nodeName__'];
                    $fieldType = isset($arrayNode['type']) ? $arrayNode['type']: '';
                    $fieldValidate = ( isset($arrayNode['validate'])) ? $arrayNode['validate'] : '';
                    if (! in_array( $fieldType, $excludeFieldsList ) && ! in_array( $fieldName, $fieldsNames ) ) {
                        $fields[] = array(
                            'FIELD_UID' => $fieldName . '-' . $fieldType,
                            'FIELD_NAME' => $fieldName,
                            '_index' => $index++
                        );
                        $fieldsNames[] = $fieldName;
                        if (in_array( $fieldType, $labelFieldsTypeList ) && ! in_array( $fieldName . '_label', $fieldsNames )) {
                            $fields[] = array(
                                'FIELD_UID' => $fieldName . '_label' . '-' . $fieldType,
                                'FIELD_NAME' => $fieldName . '_label',
                                '_index' => $index++
                            );
                            $fieldsNames[] = $fieldName;
                        }
                    }
                }
            }
        }
        sort($fields);
        return $fields;
    }

    /**
     * Get Default Columns of Report Table
     * @var string $type. Type of Report Table
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     *
     * @return array
     */
    public function getReportTableDefaultColumns ($type = 'NORMAL')
    {
        $defaultColumns = array ();
        $application = array(
            'uid' => '',
            'field_dyn' => '',
            'field_uid' => '',
            'field_name' => 'APP_UID',
            'field_label' => 'APP_UID',
            'field_type' => 'VARCHAR',
            'field_size' => 32,
            'field_dyn' => '',
            'field_key' => 1,
            'field_null' => 0,
            'field_filter' => false,
            'field_autoincrement' => false
        ); //APPLICATION KEY

        array_push( $defaultColumns, $application );

        $application = array(
            'uid' => '',
            'field_dyn' => '',
            'field_uid' => '',
            'field_name' => 'APP_NUMBER',
            'field_label' => 'APP_NUMBER',
            'field_type' => 'INTEGER',
            'field_size' => 11,
            'field_dyn' => '',
            'field_key' => 0,
            'field_null' => 0,
            'field_filter' => false,
            'field_autoincrement' => false
        ); //APP_NUMBER

        array_push( $defaultColumns, $application );

        $application = array(
            'uid' => '',
            'field_dyn' => '',
            'field_uid' => '',
            'field_name' => 'APP_STATUS',
            'field_label' => 'APP_STATUS',
            'field_type' => 'VARCHAR',
            'field_size' => 10,
            'field_dyn' => '',
            'field_key' => 0,
            'field_null' => 0,
            'field_filter' => false,
            'field_autoincrement' => false
        ); //APP_STATUS

        array_push( $defaultColumns, $application );

        //if it is a grid report table
        if ($type == 'GRID') {
            //GRID INDEX
            $gridIndex = array(
                'uid' => '',
                'field_dyn' => '',
                'field_uid' => '',
                'field_name' => 'ROW',
                'field_label' => 'ROW',
                'field_type' => 'INTEGER',
                'field_size' => '11',
                'field_dyn' => '',
                'field_key' => 1,
                'field_null' => 0,
                'field_filter' => false,
                'field_autoincrement' => false
            );
            array_push( $defaultColumns, $gridIndex );
        }

        return $defaultColumns;
    }

    /**
     * Validate Process Uid
     * @var string $pro_uid. Uid for process
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     *
     * @return string
     */
    public function validateProUid ($pro_uid)
    {
        $pro_uid = trim($pro_uid);
        if ($pro_uid == '') {
            throw (new \Exception("The project with prj_uid: '' does not exist."));
        }
        $oProcess = new \Process();
        if (!($oProcess->processExists($pro_uid))) {
            throw (new \Exception("The project with prj_uid: '$pro_uid' does not exist."));
        }
        return $pro_uid;
    }

    /**
     * Validate Table Uid
     * @var string $tab_uid. Uid for table
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     *
     * @return string
     */
    public function validateTabUid ($tab_uid, $reportFlag = true)
    {
        if ($reportFlag) {
            $label = 'The report table with rep_uid:';
        } else {
            $label = 'The pm table with pmt_uid:';
        }
        $tab_uid = trim($tab_uid);
        if ($tab_uid == '') {
            throw (new \Exception($label . "'' does not exist."));
        }
        $oAdditionalTables = new \AdditionalTables();
        if (!($oAdditionalTables->exists($tab_uid))) {
            throw (new \Exception($label . "'$tab_uid' does not exist."));
        }
        return $tab_uid;
    }

    /**
     * Validate Table Name
     * @var string $rep_tab_name. Name for report table
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     *
     * @return string
     */
    public function validateTabName ($rep_tab_name, $reportFlag = false)
    {
        $rep_tab_name = trim($rep_tab_name);
        $nametype = ($reportFlag == false) ? 'pmt_tab_name' : 'rep_tab_name';
        if ((strpos($rep_tab_name, ' ')) || (strlen($rep_tab_name) < 4)) {
            throw (new \Exception("The property $nametype: '$rep_tab_name' is incorrect."));
        }
        $rep_tab_name = G::toUpper($rep_tab_name);
        if (substr($rep_tab_name, 0, 4) != 'PMT_') {
            $rep_tab_name = 'PMT_' . $rep_tab_name;
        }
        return $rep_tab_name;
    }

    /**
     * Validate Report Table Connection
     * @var string $rep_tab_connection. Connection for report table
     * @var string $pro_uid. Uid for process
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     *
     * @return string
     */
    public function validateRepConnection ($rep_tab_connection, $pro_uid)
    {
        $rep_tab_connection = trim($rep_tab_connection);
        if ($rep_tab_connection == '') {
            throw (new \Exception("The property rep_tab_connection: '$rep_tab_connection' is incorrect."));
        }

        $connections = array('workflow', 'rp');
        $oCriteria = new \Criteria('workflow');
        $oCriteria->addSelectColumn(\DbSourcePeer::DBS_UID);
        $oCriteria->add(\DbSourcePeer::PRO_UID, $pro_uid, \Criteria::EQUAL);
        $oDataset = \AdditionalTablesPeer::doSelectRS($oCriteria);
        $oDataset->setFetchmode(\ResultSet::FETCHMODE_ASSOC);
        while ($oDataset->next()) {
            $row = $oDataset->getRow();
            $connections[] = $row['DBS_UID'];
        }

        if (!in_array($rep_tab_connection, $connections)) {
            throw (new \Exception("The property rep_tab_connection: '$rep_tab_connection' is incorrect."));
        }
        return $rep_tab_connection;
    }

    /**
     * Validate Report Table Grid
     * @var string $rep_tab_grid. Grid for report table
     * @var string $pro_uid. Uid for process
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     *
     * @return string
     */
    public function validateRepGrid ($rep_tab_grid, $pro_uid)
    {
        $rep_tab_grid = trim($rep_tab_grid);
        if ($rep_tab_grid == '') {
            throw (new \Exception("The property rep_tab_grid: '$rep_tab_grid' is incorrect."));
        }


        $grids = array();
        $namesGrid = array();
        $aFieldsNames = array();

        $oCriteria = new \Criteria( 'workflow' );
        $oCriteria->addSelectColumn( \DynaformPeer::DYN_FILENAME );
        $oCriteria->add( \DynaformPeer::PRO_UID, $pro_uid );
        $oCriteria->add( \DynaformPeer::DYN_TYPE, 'xmlform' );
        $oDataset = \DynaformPeer::doSelectRS( $oCriteria );
        $oDataset->setFetchmode( \ResultSet::FETCHMODE_ASSOC );

        while ($oDataset->next()) {
            $aRow = $oDataset->getRow();
            $dynaformHandler = new DynaformHandler( PATH_DYNAFORM . $aRow['DYN_FILENAME'] . '.xml' );
            $nodeFieldsList = $dynaformHandler->getFields();
            foreach ($nodeFieldsList as $node) {
                $arrayNode = $dynaformHandler->getArray( $node );
                $fieldName = $arrayNode['__nodeName__'];
                $fieldType = $arrayNode['type'];
                if ($fieldType == 'grid') {
                    if (! in_array( $fieldName, $aFieldsNames )) {
                        $namesGrid[] = $fieldName;
                        $grids[] = str_replace( $pro_uid . '/', '', $arrayNode['xmlgrid']);
                    }
                }
            }
        }

        $find = array_search($rep_tab_grid, $grids);
        if ($find === false) {
            throw (new \Exception("The property rep_tab_grid: '$rep_tab_grid' is incorrect."));
        } else {
            $rep_tab_grid = $namesGrid[$find] . '-' . $rep_tab_grid;
        }
        return $rep_tab_grid;
    }

    /**
     * Validate Field Type
     * @var string $fld_type. Type for field
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     *
     * @return string
     */
    public function validateFldType ($fld_type)
    {
        $fld_type = trim($fld_type);
        if ($fld_type == '') {
            throw (new \Exception("The property fld_type: '$fld_type' is incorrect."));
        }

        switch ($fld_type) {
            case 'INT':
                $fld_type = 'INTEGER';
                break;
            case 'TEXT':
                $fld_type = 'LONGVARCHAR';
                break;
            case 'DATETIME':
                $fld_type = 'TIMESTAMP';
                break;
        }

        $columnsTypes = \PmTable::getPropelSupportedColumnTypes();
        $res = array_search($fld_type, $columnsTypes);
        if ($res === false) {
            throw (new \Exception("The property fld_type: '$fld_type' is incorrect."));
        }
        return $fld_type;
    }
}

