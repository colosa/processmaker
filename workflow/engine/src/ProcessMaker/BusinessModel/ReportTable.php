<?php
namespace ProcessMaker\BusinessModel;

class ReportTable
{
    /**
     * Get report table default columns
     *
     * @param string $type
     *
     * @return object
     */
    private function __getDefaultColumns($type = 'NORMAL')
    {
        $defaultColumns = [];
        $application = new \stdClass(); //APPLICATION KEY
        $application->uid = '';
        $application->field_dyn = '';
        $application->field_uid = '';
        $application->field_name = 'APP_UID';
        $application->field_label = 'APP_UID';
        $application->field_type = 'VARCHAR';
        $application->field_size = 32;
        $application->field_dyn = '';
        $application->field_key = 1;
        $application->field_index = 1;
        $application->field_null = 0;
        $application->field_filter = false;
        $application->field_autoincrement = false;
        array_push($defaultColumns, $application);

        $application = new \stdClass(); //APP_NUMBER
        $application->uid = '';
        $application->field_dyn = '';
        $application->field_uid = '';
        $application->field_name = 'APP_NUMBER';
        $application->field_label = 'APP_NUMBER';
        $application->field_type = 'INTEGER';
        $application->field_size = 11;
        $application->field_dyn = '';
        $application->field_key = 0;
        $application->field_null = 0;
        $application->field_filter = false;
        $application->field_autoincrement = false;
        array_push($defaultColumns, $application);

        $application = new \stdClass(); //APP_STATUS
        $application->uid = '';
        $application->field_dyn = '';
        $application->field_uid = '';
        $application->field_name = 'APP_STATUS';
        $application->field_label = 'APP_STATUS';
        $application->field_type = 'VARCHAR';
        $application->field_size = 10;
        $application->field_dyn = '';
        $application->field_key = 0;
        $application->field_null = 0;
        $application->field_filter = false;
        $application->field_autoincrement = false;
        array_push($defaultColumns, $application);

        //If it is a grid report table
        if ($type == 'GRID') {
            //GRID INDEX
            $gridIndex = new \stdClass();
            $gridIndex->uid = '';
            $gridIndex->field_dyn = '';
            $gridIndex->field_uid = '';
            $gridIndex->field_name = 'ROW';
            $gridIndex->field_label = 'ROW';
            $gridIndex->field_type = 'INTEGER';
            $gridIndex->field_size = '11';
            $gridIndex->field_dyn = '';
            $gridIndex->field_key = 1;
            $gridIndex->field_null = 0;
            $gridIndex->field_filter = false;
            $gridIndex->field_autoincrement = false;
            array_push($defaultColumns, $gridIndex);
        }

        return $defaultColumns;
    }

    /**
     * Populate the data
     *
     * @param array $arrayTableData
     * @param array $tableNameMap
     *
     * @return string
     */
    private function __populateData(array $arrayTableData, array $tableNameMap)
    {
        try {
            $errors = '';

            foreach ($arrayTableData as $key => $value) {
                $tableName = $key;
                $contentData = $value;

                if (isset($tableNameMap[$tableName])) {
                    $tableName = $tableNameMap[$tableName];

                    $additionalTable = new \AdditionalTables();

                    $arrayAdditionalTableData = $additionalTable->loadByName($tableName);

                    if ($arrayAdditionalTableData !== false) {
                        $flagIsPmTable = $arrayAdditionalTableData['PRO_UID'] == '';

                        if ($flagIsPmTable && !empty($contentData)) {
                            $additionalTable->load($arrayAdditionalTableData['ADD_TAB_UID'], true);
                            $primaryKeys = $additionalTable->getPrimaryKeys();

                            //Obtain a list of columns
                            $primaryKeyColumn = [];

                            foreach ($contentData as $key => $row) {
                                $primaryKeyColumn[$key] = $row[$primaryKeys[0]['FLD_NAME']];
                            }

                            array_multisort($primaryKeyColumn, SORT_ASC, $contentData);

                            foreach ($contentData as $row) {
                                $arrayResult = $this->createRecord(
                                    [
                                        'id'   => $arrayAdditionalTableData['ADD_TAB_UID'],
                                        'rows' => base64_encode(serialize($row)),
                                    ],
                                    'base64'
                                );

                                if (!$arrayResult['success']) {
                                    $errors .= $arrayResult['message'];
                                }
                            }
                        }
                    }
                }
            }

            //Return
            return $errors;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Create record
     *
     * @param array  $arrayData
     * @param string $codification
     *
     * @return array
     */
    public function createRecord(array $arrayData, $codification = 'json')
    {
        try {
            $additionalTable = new \AdditionalTables();
            $arrayAdditionalTableData = $additionalTable->load($arrayData['id'], true);

            $additionalTableClassName = $arrayAdditionalTableData['ADD_TAB_CLASS_NAME'];
            $additionalTableClassPeerName = $additionalTableClassName . 'Peer';

            $row = ($codification == 'base64')?
                unserialize(base64_decode($arrayData['rows'])) : \G::json_decode($arrayData['rows']);
            $row = (array)($row);
            $row = array_merge(array_change_key_case($row, CASE_LOWER), array_change_key_case($row, CASE_UPPER));

            $flagSave = false;

            if (!file_exists(PATH_WORKSPACE . 'classes' . PATH_SEP . $additionalTableClassName . '.php')) {
                throw new Exception(\G::LoadTranslation('ID_PMTABLE_CLASS_DOESNT_EXIST', [$additionalTableClassName]));
            }

            require_once(PATH_WORKSPACE . 'classes' . PATH_SEP . $additionalTableClassName . '.php');

            if (!empty($row)) {
                eval('$con = \\Propel::getConnection(' . $additionalTableClassPeerName . '::DATABASE_NAME);');

                eval('$obj = new \\' . $additionalTableClassName . '();');
                $obj->fromArray($row, \BasePeer::TYPE_FIELDNAME);

                if ($obj->validate()) {
                    $obj->save();

                    $primaryKeysValues = [];

                    foreach ($additionalTable->getPrimaryKeys() as $primaryKey) {
                        $method = 'get' . \AdditionalTables::getPHPName($primaryKey['FLD_NAME']);
                        $primaryKeysValues[] = $obj->$method();
                    }

                    $index = \G::encrypt(implode(',', $primaryKeysValues), 'pmtable');

                    \G::auditLog(
                        'AddDataPmtable',
                        'Table Name: ' . $arrayAdditionalTableData['ADD_TAB_NAME'] .
                        ' Table ID: (' . $arrayAdditionalTableData['ADD_TAB_UID'] . ')'
                    );

                    $flagSave = true;
                } else {
                    $msg = '';

                    foreach ($obj->getValidationFailures() as $objValidationFailure) {
                        $msg .= $objValidationFailure->getMessage() . "\n";
                    }

                    throw new Exception(
                        \G::LoadTranslation('ID_ERROR_TRYING_INSERT') .
                        '"' . $arrayAdditionalTableData['ADD_TAB_NAME'] . "\"\n" . $msg
                    );
                }
            } else {
                $flagSave = false;
            }

            //Return
            return [
                'success' => $flagSave,
                'message' => ($flagSave)? \G::LoadTranslation('ID_RECORD_SAVED_SUCCESFULLY') : '',
                'rows'    => ($flagSave)? $obj->toArray(\BasePeer::TYPE_FIELDNAME) : [],
                'index'   => ($flagSave)? $index : '',
            ];
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Review the table schema and throw all errors
     *
     * @param array  $arrayTableSchema
     * @param string $processUid
     * @param bool   $flagFromAdmin
     * @param bool   $flagOverwrite
     * @param string $postProUid
     *
     * @return array
     */
    public function checkPmtFileThrowErrors(
        array $arrayTableSchema,
        $processUid,
        $flagFromAdmin,
        $flagOverwrite,
        $postProUid
    ) {
        try {
            $arrayError = [];

            //Ask for all Process
            $processMap = new \ProcessMap();
            $arrayProcessUid = [];

            foreach (\G::json_decode($processMap->getAllProcesses()) as $value) {
                if ($value->value != '') {
                    $arrayProcessUid[] = $value->value;
                }
            }

            $i = 0;

            foreach ($arrayTableSchema as $value) {
                $contentSchema = $value;

                //The table exists?
                $additionalTable = new \AdditionalTables();

                $arrayAdditionalTableData = $additionalTable->loadByName($contentSchema['ADD_TAB_NAME']);

                $tableProUid   = (isset($contentSchema['PRO_UID']))? $contentSchema['PRO_UID'] : $postProUid;
                $flagIsPmTable = ($contentSchema['PRO_UID'] == '')? true : false;

                if ($flagFromAdmin) {
                    if ($flagIsPmTable) {
                        if ($arrayAdditionalTableData !== false && !$flagOverwrite) {
                            $arrayError[$i]['NAME_TABLE'] = $contentSchema['ADD_TAB_NAME'];
                            $arrayError[$i]['ERROR_TYPE'] = 1; //ERROR_PM_TABLES_OVERWRITE
                            $arrayError[$i]['ERROR_MESS'] = \G::LoadTranslation('ID_OVERWRITE_PMTABLE', [$contentSchema['ADD_TAB_NAME']]);
                            $arrayError[$i]['IS_PMTABLE'] = $flagIsPmTable;
                            $arrayError[$i]['PRO_UID'] = $tableProUid;
                        }
                    } else {
                        if (!in_array($tableProUid, $arrayProcessUid)) {
                            $arrayError[$i]['NAME_TABLE'] = $contentSchema['ADD_TAB_NAME'];
                            $arrayError[$i]['ERROR_TYPE'] = 2; //ERROR_PROCESS_NOT_EXIST
                            $arrayError[$i]['ERROR_MESS'] = \G::LoadTranslation('ID_PROCESS_NOT_EXIST', [$contentSchema['ADD_TAB_NAME']]);
                            $arrayError[$i]['IS_PMTABLE'] = $flagIsPmTable;
                            $arrayError[$i]['PRO_UID'] = $tableProUid;
                        } else {
                            if ($arrayAdditionalTableData !== false && !$flagOverwrite) {
                                $arrayError[$i]['NAME_TABLE'] = $contentSchema['ADD_TAB_NAME'];
                                $arrayError[$i]['ERROR_TYPE'] = 3; //ERROR_RP_TABLES_OVERWRITE
                                $arrayError[$i]['ERROR_MESS'] = \G::LoadTranslation('ID_OVERWRITE_RPTABLE', [$contentSchema['ADD_TAB_NAME']]);
                                $arrayError[$i]['IS_PMTABLE'] = $flagIsPmTable;
                                $arrayError[$i]['PRO_UID'] = $tableProUid;
                            }
                        }
                    }
                } else {
                    if ($flagIsPmTable) {
                        $arrayError[$i]['NAME_TABLE'] = $contentSchema['ADD_TAB_NAME'];
                        $arrayError[$i]['ERROR_TYPE'] = 4; //ERROR_NO_REPORT_TABLE
                        $arrayError[$i]['ERROR_MESS'] = \G::LoadTranslation('ID_NO_REPORT_TABLE', [$contentSchema['ADD_TAB_NAME']]);
                        $arrayError[$i]['IS_PMTABLE'] = $flagIsPmTable;
                        $arrayError[$i]['PRO_UID'] = $tableProUid;
                    } else {
                        if ($tableProUid != $processUid) {
                            $arrayError[$i]['NAME_TABLE'] = $contentSchema['ADD_TAB_NAME'];
                            $arrayError[$i]['ERROR_TYPE'] = 5; //ERROR_OVERWRITE_RELATED_PROCESS
                            $arrayError[$i]['ERROR_MESS'] = \G::LoadTranslation('ID_OVERWRITE_RELATED_PROCESS', [$contentSchema['ADD_TAB_NAME']]);
                            $arrayError[$i]['IS_PMTABLE'] = $flagIsPmTable;
                            $arrayError[$i]['PRO_UID'] = $tableProUid;
                        } else {
                            if ($arrayAdditionalTableData !== false && !$flagOverwrite) {
                                $arrayError[$i]['NAME_TABLE'] = $contentSchema['ADD_TAB_NAME'];
                                $arrayError[$i]['ERROR_TYPE'] = 3; //ERROR_RP_TABLES_OVERWRITE
                                $arrayError[$i]['ERROR_MESS'] = \G::LoadTranslation('ID_OVERWRITE_RPTABLE', [$contentSchema['ADD_TAB_NAME']]);
                                $arrayError[$i]['IS_PMTABLE'] = $flagIsPmTable;
                                $arrayError[$i]['PRO_UID'] = $tableProUid;
                            }
                        }
                    }
                }

                $i++;
            }

            //Return
            return $arrayError;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Save structure of table
     *
     * @param array $arrayData
     * @param bool  $flagAlterTable
     *
     * @return object
     */
    public function saveStructureOfTable($arrayData, $flagAlterTable = true)
    {
        $result = new \stdClass();

        try {
            $additionalTableUid = $arrayData['REP_TAB_UID'];
            $flagNew = 0;

            $additionalTables = \AdditionalTablesPeer::retrieveByPK($arrayData['REP_TAB_UID']);

            if (!is_null($additionalTables)){
                $arrayData['REP_TAB_NAME'] = 'PMT_' . trim($arrayData['REP_TAB_NAME']);

                if ($additionalTables->getAddTabName() != $arrayData['REP_TAB_NAME']) {
                    $arrayData['REP_TAB_UID'] = '';
                    $flagNew = 1;
                }
            }

            ob_start();

            $arrayData['PRO_UID'] = trim($arrayData['PRO_UID']);
            $arrayData['columns'] = \G::json_decode(stripslashes($arrayData['columns'])); //Decofing data columns

            if ($flagNew == 1) {
                $arrayNewColumn = [];
                $counter = 0;

                foreach ($arrayData['columns'] as $value) {
                    $column = $value;

                    if (!preg_match('/^(?:APP_UID|APP_NUMBER|APP_STATUS|ROW)$/', $column->field_name)) {
                        $column->uid = '';
                        $column->_index = $counter;

                        $arrayNewColumn[] = $column;

                        $counter++;
                    }
                }

                $arrayData['columns'] = $arrayNewColumn;
            }

            $additionalTable = new \AdditionalTables();

            $repTabClassName = $additionalTable->getPHPName($arrayData['REP_TAB_NAME']);
            $flagIsReportTable = ($arrayData['PRO_UID'] != '')? true : false;
            $columns = $arrayData['columns'];

            //Reserved Words Table
            $reservedWords = [
                'ALTER', 'CLOSE', 'COMMIT', 'CREATE','DECLARE','DELETE','DROP','FETCH','FUNCTION','GRANT','INDEX',
                'INSERT','OPEN','REVOKE','ROLLBACK','SELECT','SYNONYM','TABLE','UPDATE','VIEW','APP_UID','ROW','PMTABLE'
            ];

            //Reserved Words Field
            $reservedWordsPhp = [
                'case','catch','cfunction','class','clone','const','continue','declare','default','do','else','elseif',
                'enddeclare','endfor','endforeach','endif','endswitch','endwhile','extends','final','for','foreach',
                'function','global','goto','if','implements','interface','instanceof','private','namespace','new',
                'old_function','or','throw','protected','public','static','switch','xor','try','use','var','while'
            ];

            $reservedWordsSql = \G::reservedWordsSql();

            //Verify if exists
            if ($arrayData['REP_TAB_UID'] == '' || (isset($arrayData['forceUid']) && $arrayData['forceUid'])) {
                //New report table
                if ($flagIsReportTable && $flagAlterTable) {
                    //Setting default columns
                    $defaultColumns = $this->__getDefaultColumns($arrayData['REP_TAB_TYPE']);
                    $columns = array_merge($defaultColumns, $columns);
                }

                //Validations
                if (is_array($additionalTable->loadByName($arrayData['REP_TAB_NAME']))) {
                    throw new \Exception(\G::LoadTranslation('ID_PMTABLE_ALREADY_EXISTS', [$arrayData['REP_TAB_NAME']]));
                }

                if (in_array(strtoupper($arrayData['REP_TAB_NAME']), $reservedWords) ||
                    in_array(strtoupper($arrayData['REP_TAB_NAME']), $reservedWordsSql)
                ) {
                    throw new \Exception(\G::LoadTranslation('ID_PMTABLE_INVALID_NAME', [$arrayData['REP_TAB_NAME']]));
                }
            }

            //Backward compatility
            foreach ($columns as $i => $column) {
                if (in_array(strtoupper($columns[$i]->field_name), $reservedWordsSql) ||
                    in_array(strtolower($columns[$i]->field_name), $reservedWordsPhp)
                ) {
                    throw new \Exception(\G::LoadTranslation('ID_PMTABLE_INVALID_FIELD_NAME', [$columns[$i]->field_name]));
                }

                switch ($column->field_type) {
                    case 'INT':
                        $columns[$i]->field_type = 'INTEGER';
                        break;
                    case 'TEXT':
                        $columns[$i]->field_type = 'LONGVARCHAR';
                        break;
                    case 'DATETIME':
                        //Propel: DATETIME equivalent is TIMESTAMP
                        $columns[$i]->field_type = 'TIMESTAMP';
                        break;
                }

                //Validations
                if ($columns[$i]->field_autoincrement) {
                    $typeCol = $columns[$i]->field_type;

                    if (!($typeCol === 'INTEGER' || $typeCol === 'TINYINT' || $typeCol === 'SMALLINT' || $typeCol === 'BIGINT')) {
                        $columns[$i]->field_autoincrement = false;
                    }
                }
            }

            $pmTable = new \PmTable($arrayData['REP_TAB_NAME']);
            $pmTable->setDataSource($arrayData['REP_TAB_CONNECTION']);
            $pmTable->setColumns($columns);
            $pmTable->setAlterTable($flagAlterTable);
            if (isset($arrayData['REP_TAB_NAME_OLD_NAME'])) {
                $pmTable->setOldTableName($arrayData['REP_TAB_NAME_OLD_NAME']);
            }

            if (isset($arrayData['keepData']) && $arrayData['keepData'] == 1) {
                //PM Table
                $pmTable->setKeepData(true);
            }

            $pmTable->build();
            
            $buildResult = ob_get_contents();

            ob_end_clean();

            //Updating additional table struture information
            $addTabData = [
                'ADD_TAB_UID'  => $arrayData['REP_TAB_UID'],
                'ADD_TAB_NAME' => $arrayData['REP_TAB_NAME'],
                'ADD_TAB_CLASS_NAME'  => $repTabClassName,
                'ADD_TAB_DESCRIPTION' => $arrayData['REP_TAB_DSC'],
                'ADD_TAB_PLG_UID' => '',
                'DBS_UID' => ($arrayData['REP_TAB_CONNECTION'])? $arrayData['REP_TAB_CONNECTION'] : 'workflow',
                'PRO_UID' => $arrayData['PRO_UID'],
                'ADD_TAB_TYPE' => $arrayData['REP_TAB_TYPE'],
                'ADD_TAB_GRID' => $arrayData['REP_TAB_GRID']
            ];

            if ($arrayData['REP_TAB_UID'] == '' || (isset($arrayData['forceUid']) && $arrayData['forceUid'])) {
                //New report table
                //create record
                $addTabUid = $additionalTable->create($addTabData);
            } else {
                //Editing report table
                //updating record
                $addTabUid = $arrayData['REP_TAB_UID'];
                $additionalTable->update($addTabData);

                //Removing old data fields references
                $oCriteria = new \Criteria('workflow');
                $oCriteria->add(\FieldsPeer::ADD_TAB_UID, $arrayData['REP_TAB_UID']);
                \FieldsPeer::doDelete($oCriteria);
            }

            //Updating pmtable fields
            $field = new \Fields();

            foreach ($columns as $i => $column) {
                $field->create([
                    'FLD_UID'     => $column->uid,
                    'FLD_INDEX'   => $i,
                    'ADD_TAB_UID' => $addTabUid,
                    'FLD_NAME' => $column->field_name,
                    'FLD_DESCRIPTION' => $column->field_label,
                    'FLD_TYPE' => $column->field_type,
                    'FLD_SIZE' => ($column->field_size == '')? null : $column->field_size,
                    'FLD_NULL' => ($column->field_null)? 1 : 0,
                    'FLD_AUTO_INCREMENT' => ($column->field_autoincrement)? 1 : 0,
                    'FLD_KEY' => ($column->field_key)? 1 : 0,
                    'FLD_TABLE_INDEX' => (isset($column->field_index) && $column->field_index)? 1 : 0,
                    'FLD_FOREIGN_KEY' => 0,
                    'FLD_FOREIGN_KEY_TABLE' => '',
                    'FLD_DYN_NAME' => $column->field_dyn,
                    'FLD_DYN_UID'  => $column->field_uid,
                    'FLD_FILTER'   => (isset($column->field_filter) && $column->field_filter)? 1 : 0
                ]);
            }

            if ($flagIsReportTable && $flagAlterTable) {
                //The table was create successfully but we're catching problems while populating table
                try {
                    $additionalTable->populateReportTable(
                        $arrayData['REP_TAB_NAME'],
                        $pmTable->getDataSource(),
                        $arrayData['REP_TAB_TYPE'],
                        $arrayData['PRO_UID'],
                        $arrayData['REP_TAB_GRID'],
                        $addTabUid
                    );
                } catch (\Exception $e) {
                    $result->message = $result->msg = $e->getMessage();
                }
            }

            //Audit Log
            $nFields = count($columns) - 1;
            $fieldsName = '';

            foreach ($columns as $i => $column) {
                if ($i != $nFields) {
                    $fieldsName = $fieldsName . $columns[$i]->field_name . ' [' . implode(', ', get_object_vars($column)) . '], ';
                } else {
                    $fieldsName = $fieldsName . $columns[$i]->field_name . ' [' . implode(', ', get_object_vars($column)) . '].';
                }
            }

            \G::auditLog(
                (isset($arrayData['REP_TAB_UID']) && $arrayData['REP_TAB_UID'] == '')?
                    'CreatePmtable' : 'UpdatePmtable', 'Fields: ' . $fieldsName
            );

            $result->success = true;
            $result->message = $result->msg = $buildResult;

            require_once(PATH_CORE . 'controllers/pmTablesProxy.php');

            if ($flagNew == 1) {
                $pmTablesProxy = new \pmTablesProxy();

                $obj = new \stdClass();
                $obj->rows = \G::json_encode([['id' => $additionalTableUid, 'type' => '']]);

                //Delete Report Table
                $resultDeleteReportTable = $pmTablesProxy->delete($obj);
            }
        } catch (\Exception $e) {
            $buildResult = ob_get_contents();

            ob_end_clean();

            $result->success = false;

            //If it is a propel exception message
            if (preg_match('/(.*)\s\[(.*):\s(.*)\]\s\[(.*):\s(.*)\]/', $e->getMessage(), $match)) {
                $result->message = $result->msg = $match[3];
                $result->type = ucfirst($pmTable->getDbConfig()->adapter);
            } else {
                $result->message = $result->msg = $e->getMessage();
                $result->type = \G::LoadTranslation('ID_EXCEPTION');
            }

            $result->trace = $e->getTraceAsString();
        }

        //Return
        return $result;
    }

    /**
     * Create the structure of tables
     *
     * @param array  $arrayTableSchema,
     * @param array  $arrayTableData,
     * @param string $processUid
     * @param bool   $flagFromAdmin
     * @param bool   $flagOverwrite
     * @param array  $arrayTablesToExclude
     * @param array  $arrayTablesToCreate
     *
     * @return string
     */
    public function createStructureOfTables(
        array $arrayTableSchema,
        array $arrayTableData,
        $processUid,
        $flagFromAdmin,
        $flagOverwrite = true,
        array $arrayTablesToExclude = [],
        array $arrayTablesToCreate = []
    ) {
        try {
            $errors = '';

            $tableNameMap = [];
            $processQueue = [];
            $processQueueTables = [];

            foreach ($arrayTableSchema as $value) {
                $contentSchema = $value;

                if (!in_array($contentSchema['ADD_TAB_NAME'], $arrayTablesToExclude)) {
                    $additionalTable = new \AdditionalTables();

                    $arrayAdditionalTableData = $additionalTable->loadByName($contentSchema['ADD_TAB_NAME']);

                    $tableNameMap[$contentSchema['ADD_TAB_NAME']] = $contentSchema['ADD_TAB_NAME'];

                    $tableData = new \stdClass();

                    if (isset( $contentSchema['PRO_UID'] )) {
                        $tableData->PRO_UID = $contentSchema['PRO_UID'];
                    } else {
                        $tableData->PRO_UID = $_POST['form']['PRO_UID'];
                    }

                    $flagIsPmTable = $contentSchema['PRO_UID'] === '';

                    if (!$flagFromAdmin && !$flagIsPmTable) {
                        $tableData->PRO_UID = $processUid;
                    }

                    $flagOverwrite2 = $flagOverwrite;

                    if (in_array($contentSchema['ADD_TAB_NAME'], $arrayTablesToCreate)) {
                        $flagOverwrite2 = false;
                    }

                    //Overwrite
                    if ($flagOverwrite2) {
                        if ($arrayAdditionalTableData !== false) {
                            $additionalTable->deleteAll($arrayAdditionalTableData['ADD_TAB_UID']);
                        }
                    } else {
                        if ($arrayAdditionalTableData !== false) {
                            //Some table exists with the same name
                            //renaming...
                            $tNameOld = $contentSchema['ADD_TAB_NAME'];
                            $newTableName = $contentSchema['ADD_TAB_NAME'] . '_' . date('YmdHis');
                            $contentSchema['ADD_TAB_UID'] = \G::generateUniqueID();
                            $contentSchema['ADD_TAB_NAME'] = $newTableName;
                            $contentSchema['ADD_TAB_CLASS_NAME'] = \AdditionalTables::getPHPName($newTableName);

                            //Mapping the table name for posterior uses
                            $tableNameMap[$tNameOld] = $contentSchema['ADD_TAB_NAME'];
                        }
                    }

                    //Validating invalid bds_uid in old tables definition -> mapped to workflow
                    if (!$contentSchema['DBS_UID'] || $contentSchema['DBS_UID'] == '0' || !$contentSchema['DBS_UID']) {
                        $contentSchema['DBS_UID'] = 'workflow';
                    }

                    $columns = [];

                    foreach ($contentSchema['FIELDS'] as $field) {
                        $columns[] = [
                            'uid'         => '',
                            'field_uid'   => '',
                            'field_name'  => $field['FLD_NAME'],
                            'field_dyn'   => (isset($field['FLD_DYN_NAME']))? $field['FLD_DYN_NAME'] : '',
                            'field_label' => (isset($field['FLD_DESCRIPTION']))? $field['FLD_DESCRIPTION'] : '',
                            'field_type'  => $field['FLD_TYPE'],
                            'field_size'  => $field['FLD_SIZE'],
                            'field_key'   => (isset($field['FLD_KEY']))? $field['FLD_KEY'] : 0,
                            'field_null'  => (isset($field['FLD_NULL']))? $field['FLD_NULL'] : 1,
                            'field_autoincrement' => (isset($field['FLD_AUTO_INCREMENT']))?
                                $field['FLD_AUTO_INCREMENT'] : 0
                        ];
                    }

                    $tableData->REP_TAB_UID = $contentSchema['ADD_TAB_UID'];
                    $tableData->REP_TAB_NAME = $contentSchema['ADD_TAB_NAME'];
                    $tableData->REP_TAB_DSC = $contentSchema['ADD_TAB_DESCRIPTION'];
                    $tableData->REP_TAB_CONNECTION = $contentSchema['DBS_UID'];
                    $tableData->REP_TAB_TYPE = (isset($contentSchema['ADD_TAB_TYPE']))? $contentSchema['ADD_TAB_TYPE'] : '';
                    $tableData->REP_TAB_GRID = (isset($contentSchema['ADD_TAB_GRID']))? $contentSchema['ADD_TAB_GRID'] : '';
                    $tableData->columns = \G::json_encode($columns);
                    $tableData->forceUid = true;

                    //Save the table
                    $alterTable = false;
                    $result = $this->saveStructureOfTable((array)($tableData), $alterTable);

                    if ($result->success) {
                        \G::auditLog(
                            'ImportTable', $contentSchema['ADD_TAB_NAME'] . ' (' . $contentSchema['ADD_TAB_UID'] . ')'
                        );

                        $processQueueTables[$contentSchema['DBS_UID']][] = $contentSchema['ADD_TAB_NAME'];
                    } else {
                        $errors .= \G::LoadTranslation('ID_ERROR_CREATE_TABLE') . $tableData->REP_TAB_NAME . '-> ' . $result->message . '\n\n';
                    }
                }
            }

            foreach ($processQueueTables as $dbsUid => $tables) {
                $pmTable = new \PmTable();

                ob_start();
                $pmTable->buildModelFor($dbsUid, $tables);
                $buildResult = ob_get_contents();
                ob_end_clean();

                $errors .= $pmTable->upgradeDatabaseFor($pmTable->getDataSource(), $tables);
            }

            if (!empty($tableNameMap)) {
                $errors = $this->__populateData($arrayTableData, $tableNameMap);
            }

            //Return
            return $errors;
        } catch (\Exception $e) {
            throw $e;
        }
    }
}

