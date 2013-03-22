<?php
/**
 * pmTablesProxy
 *
 * @author Erik Amaru Ortiz <erik@colosa.com, aortiz.erik@gmail.com>
 * @inherits HttpProxyController
 * @access public
 */
require_once 'classes/model/AdditionalTables.php';

class pmTablesProxy extends HttpProxyController
{

    protected $className;
    protected $classPeerName;
    protected $dynUid;

    /**
     * get pmtables list
     *
     * @param string $httpData->start
     * @param string $httpData->limit
     * @param string $httpData->textFilter
     */
    public function getList ($httpData)
    {
        G::LoadClass( 'configuration' );
        G::LoadClass( 'processMap' );
        G::LoadClass( 'pmTable' );
        $configurations = new Configurations();
        $processMap = new processMap();

        // setting parameters
        $config = $configurations->getConfiguration( 'additionalTablesList', 'pageSize', '', $_SESSION['USER_LOGGED'] );
        $env = $configurations->getConfiguration( 'ENVIRONMENT_SETTINGS', '' );
        $limit_size = isset( $config->pageSize ) ? $config['pageSize'] : 20;
        $start = isset( $httpData->start ) ? $httpData->start : 0;
        $limit = isset( $httpData->limit ) ? $httpData->limit : $limit_size;
        $filter = isset( $httpData->textFilter ) ? $httpData->textFilter : '';
        $pro_uid = isset( $httpData->pro_uid ) ? $httpData->pro_uid : null;

        if ($pro_uid !== null) {
            $process = $pro_uid == '' ? array ('not_equal' => $pro_uid
            ) : array ('equal' => $pro_uid);
            $addTables = AdditionalTables::getAll( $start, $limit, $filter, $process );

            $c = $processMap->getReportTablesCriteria( $pro_uid );
            $oDataset = RoutePeer::doSelectRS( $c );
            $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );
            $reportTablesOldList = array ();
            while ($oDataset->next()) {
                $reportTablesOldList[] = $oDataset->getRow();
            }
            $addTables['count'] += count( $reportTablesOldList );

            foreach ($reportTablesOldList as $i => $oldRepTab) {
                $addTables['rows'][] = array ('ADD_TAB_UID' => $oldRepTab['REP_TAB_UID'],'PRO_UID' => $oldRepTab['PRO_UID'],'DBS_UID' => ($oldRepTab['REP_TAB_CONNECTION'] == 'wf' ? 'workflow' : 'rp'),'ADD_TAB_DESCRIPTION' => $oldRepTab['REP_TAB_TITLE'],'ADD_TAB_NAME' => $oldRepTab['REP_TAB_NAME'],'ADD_TAB_TYPE' => $oldRepTab['REP_TAB_TYPE'],'TYPE' => 'CLASSIC' );
            }
        } else {
            $addTables = AdditionalTables::getAll( $start, $limit, $filter );
        }

        foreach ($addTables['rows'] as $i => $table) {
            try {
                $con = Propel::getConnection( pmTable::resolveDbSource( $table['DBS_UID'] ) );
                $stmt = $con->createStatement();
                $rs = $stmt->executeQuery( 'SELECT COUNT(*) AS NUM_ROWS from ' . $table['ADD_TAB_NAME'] );
                if ($rs->next()) {
                    $r = $rs->getRow();
                    $addTables['rows'][$i]['NUM_ROWS'] = $r['NUM_ROWS'];
                } else {
                    $addTables['rows'][$i]['NUM_ROWS'] = 0;
                }

                //removing the prefix "PMT" to allow alphabetical order (just in view)
                if (substr( $addTables['rows'][$i]['ADD_TAB_NAME'], 0, 4 ) == 'PMT_') {
                    $addTables['rows'][$i]['ADD_TAB_NAME'] = substr( $addTables['rows'][$i]['ADD_TAB_NAME'], 4 );
                }
            } catch (Exception $e) {
                $addTables['rows'][$i]['NUM_ROWS'] = G::LoadTranslation( 'ID_TABLE_NOT_FOUND' );
            }
        }

        return $addTables;
    }

    /**
     * get processesList
     */
    public function getProcessList ()
    {
        require_once 'classes/model/Process.php';

        $process = new Process();
        return $process->getAll();
    }

    /**
     * get database connection list
     */
    public function getDbConnectionsList ()
    {
        if (! isset( $_SESSION['PROCESS'] )) {
            $_SESSION['PROCESS'] = $_POST['PRO_UID'];
        }
        G::LoadClass( 'dbConnections' );
        $proUid = $_POST['PRO_UID'];
        $dbConn = new DbConnections();
        $dbConnections = $dbConn->getConnectionsProUid( $proUid );
        $defaultConnections = array (array ('DBS_UID' => 'workflow','DBS_NAME' => 'Workflow'
        ),array ('DBS_UID' => 'rp','DBS_NAME' => 'REPORT'
        )
        );

        $dbConnections = array_merge( $defaultConnections, $dbConnections );

        return $dbConnections;
    }

    /**
     * get dynaform fields
     *
     * @param string $httpData->PRO_UID
     * @param string $httpData->TYPE
     * @param string $httpData->GRID_UID
     */
    public function getDynafields ($httpData)
    {
        G::LoadClass( 'reportTables' );

        $aFields['FIELDS'] = array ();
        $aFields['PRO_UID'] = $httpData->PRO_UID;
        $dynFields = array ();

        if (isset( $httpData->TYPE ) && $httpData->TYPE == 'GRID') {
            $aProcessGridFields = Array ();
            if (isset( $httpData->GRID_UID )) {
                list ($gridName, $gridId) = explode( '-', $httpData->GRID_UID );
                $this->dynUid = $gridId;

                $httpData->textFilter = isset( $httpData->textFilter ) ? $httpData->textFilter : null;
                $dynFields = $this->_getDynafields( $aFields['PRO_UID'], 'grid', $httpData->start, $httpData->limit, $httpData->textFilter );
            } else {
                if (isset( $_SESSION['_cache_pmtables'] )) {
                    unset( $_SESSION['_cache_pmtables'] );
                }
                $gridFields = $this->_getGridFields( $aFields['PRO_UID'] );

                foreach ($gridFields as $gfield) {
                    $dynFields[] = array ('FIELD_UID' => $gfield['name'] . '-' . $gfield['xmlform'],'FIELD_NAME' => $gfield['name']
                    );
                }
            }
        } else {
            // normal dynaform
            $httpData->textFilter = isset( $httpData->textFilter ) ? $httpData->textFilter : null;
            $dynFields = $this->_getDynafields( $aFields['PRO_UID'], 'xmlform', $httpData->start, $httpData->limit, $httpData->textFilter );
        }

        return $dynFields;
    }

    public function updateAvDynafields ($httpData)
    {
        $indexes = explode( ',', $httpData->indexes );
        $fields = array ();
        $httpData->isset = $httpData->isset == 'true' ? true : false;

        if (isset( $_SESSION['_cache_pmtables'] ) && $_SESSION['_cache_pmtables']['pro_uid'] == $httpData->PRO_UID) {
            foreach ($indexes as $i) {
                if (is_numeric( $i )) {
                    if (isset( $_SESSION['_cache_pmtables']['rows'][$i] )) {
                        $_SESSION['_cache_pmtables']['rows'][$i]['_isset'] = $httpData->isset;
                        if ($httpData->isset) {
                            $_SESSION['_cache_pmtables']['count'] ++;
                        } else {
                            $_SESSION['_cache_pmtables']['count'] --;
                        }

                        $fields[] = $_SESSION['_cache_pmtables']['rows'][$i]['FIELD_NAME'];
                    }
                } else {

                    $index = $_SESSION['_cache_pmtables']['indexes'][$i];
                    $_SESSION['_cache_pmtables']['rows'][$index]['_isset'] = $httpData->isset;
                }
            }
        }
        return $fields;
    }

    /**
     * save pm table
     */
    public function save ($httpData, $alterTable = true)
    {
        //require_once 'classes/model/AdditionalTables.php';
        //require_once 'classes/model/Fields.php';

        try {
            ob_start();
            $data = (array) $httpData;
            $data['PRO_UID'] = trim( $data['PRO_UID'] );
            $data['columns'] = G::json_decode( stripslashes( $httpData->columns ) ); //decofing data columns


            $isReportTable = $data['PRO_UID'] != '' ? true : false;
            $oAdditionalTables = new AdditionalTables();
            $oFields = new Fields();
            $repTabClassName = $oAdditionalTables->getPHPName( $data['REP_TAB_NAME'] );
            $columns = $data['columns'];

            // Reserved Words Table
            $reservedWords = array ('ALTER','CLOSE','COMMIT','CREATE','DECLARE','DELETE','DROP','FETCH','FUNCTION','GRANT','INDEX','INSERT','OPEN','REVOKE','ROLLBACK','SELECT','SYNONYM','TABLE','UPDATE','VIEW','APP_UID','ROW','PMTABLE' );

            // Reserved Words Field
            $reservedWordsPhp = array ('case','catch','cfunction','class','clone','const','continue','declare','default','do','else','elseif','enddeclare','endfor','endforeach','endif','endswitch','endwhile','extends','final','for','foreach','function','global','goto','if','implements','interface','instanceof','private','namespace','new','old_function','or','throw','protected','public','static','switch','xor','try','use','var','while'
            );

            $reservedWordsSql = G::reservedWordsSql();

            // verify if exists.
            if ($data['REP_TAB_UID'] == '' || (isset( $httpData->forceUid ) && $httpData->forceUid)) {
                //new report table
                if ($isReportTable && $alterTable) {
                    //setting default columns
                    $defaultColumns = $this->_getReportTableDefaultColumns( $data['REP_TAB_TYPE'] );
                    $columns = array_merge( $defaultColumns, $columns );
                }

                /**
                 * validations *
                 */
                if (is_array( $oAdditionalTables->loadByName( $data['REP_TAB_NAME'] ) )) {
                    throw new Exception( G::loadTranslation( 'ID_PMTABLE_ALREADY_EXISTS', array ($data['REP_TAB_NAME']
                    ) ) );
                }

                if (in_array( strtoupper( $data["REP_TAB_NAME"] ), $reservedWords ) || in_array( strtoupper( $data["REP_TAB_NAME"] ), $reservedWordsSql )) {
                    throw (new Exception( G::LoadTranslation( "ID_PMTABLE_INVALID_NAME", array ($data["REP_TAB_NAME"]
                    ) ) ));
                }
            }

            //backward compatility
            foreach ($columns as $i => $column) {
                if (in_array( strtoupper( $columns[$i]->field_name ), $reservedWordsSql ) || in_array( strtolower( $columns[$i]->field_name ), $reservedWordsPhp )) {
                    throw (new Exception( G::LoadTranslation( "ID_PMTABLE_INVALID_FIELD_NAME", array ($columns[$i]->field_name
                    ) ) ));
                }

                switch ($column->field_type) {
                    case 'INT':
                        $columns[$i]->field_type = 'INTEGER';
                        break;
                    case 'TEXT':
                        $columns[$i]->field_type = 'LONGVARCHAR';
                        break;
                    // propel DATETIME equivalent is TIMESTAMP
                    case 'DATETIME':
                        $columns[$i]->field_type = 'TIMESTAMP';
                        break;
                }

                // VALIDATIONS
                if ($columns[$i]->field_autoincrement) {
                    $typeCol = $columns[$i]->field_type;
                    if (! ($typeCol === 'INTEGER' || $typeCol === 'TINYINT' || $typeCol === 'SMALLINT' || $typeCol === 'BIGINT')) {
                        $columns[$i]->field_autoincrement = false;
                    }
                }
            }

            G::LoadClass("pmTable");

            $pmTable = new pmTable( $data['REP_TAB_NAME'] );
            $pmTable->setDataSource( $data['REP_TAB_CONNECTION'] );
            $pmTable->setColumns( $columns );
            $pmTable->setAlterTable( $alterTable );

            if (isset($data["keepData"]) && $data["keepData"] == 1) {
                //PM Table
                $pmTable->setKeepData(true);
            }

            $pmTable->build();

            $buildResult = ob_get_contents();
            ob_end_clean();

            // Updating additional table struture information
            $addTabData = array ('ADD_TAB_UID' => $data['REP_TAB_UID'],'ADD_TAB_NAME' => $data['REP_TAB_NAME'],'ADD_TAB_CLASS_NAME' => $repTabClassName,'ADD_TAB_DESCRIPTION' => $data['REP_TAB_DSC'],'ADD_TAB_PLG_UID' => '','DBS_UID' => ($data['REP_TAB_CONNECTION'] ? $data['REP_TAB_CONNECTION'] : 'workflow'),'PRO_UID' => $data['PRO_UID'],'ADD_TAB_TYPE' => $data['REP_TAB_TYPE'],'ADD_TAB_GRID' => $data['REP_TAB_GRID']
            );
            if ($data['REP_TAB_UID'] == '' || (isset( $httpData->forceUid ) && $httpData->forceUid)) {
                //new report table
                //create record
                $addTabUid = $oAdditionalTables->create( $addTabData );
            } else {
                //editing report table
                //updating record
                $addTabUid = $data['REP_TAB_UID'];
                $oAdditionalTables->update( $addTabData );

                //removing old data fields references
                $oCriteria = new Criteria( 'workflow' );
                $oCriteria->add( FieldsPeer::ADD_TAB_UID, $data['REP_TAB_UID'] );
                FieldsPeer::doDelete( $oCriteria );
            }

            // Updating pmtable fields
            foreach ($columns as $i => $column) {
                $field = array ('FLD_UID' => $column->uid,'FLD_INDEX' => $i,'ADD_TAB_UID' => $addTabUid,'FLD_NAME' => $column->field_name,'FLD_DESCRIPTION' => $column->field_label,'FLD_TYPE' => $column->field_type,'FLD_SIZE' => $column->field_size == '' ? null : $column->field_size,'FLD_NULL' => $column->field_null ? 1 : 0,'FLD_AUTO_INCREMENT' => $column->field_autoincrement ? 1 : 0,'FLD_KEY' => $column->field_key ? 1 : 0,'FLD_FOREIGN_KEY' => 0,'FLD_FOREIGN_KEY_TABLE' => '','FLD_DYN_NAME' => $column->field_dyn,'FLD_DYN_UID' => $column->field_uid,'FLD_FILTER' => (isset( $column->field_filter ) && $column->field_filter) ? 1 : 0
                );
                $oFields->create( $field );
            }

            if ($isReportTable && $alterTable) {
                // the table was create successfully but we're catching problems while populating table
                try {
                    $oAdditionalTables->populateReportTable( $data['REP_TAB_NAME'], $pmTable->getDataSource(), $data['REP_TAB_TYPE'], $data['PRO_UID'], $data['REP_TAB_GRID'], $addTabUid );
                } catch (Exception $e) {
                    $result->message = $result->msg = $e->getMessage();
                }
            }

            $result->success = true;
            $result->message = $result->msg = $buildResult;
        } catch (Exception $e) {
            $buildResult = ob_get_contents();
            ob_end_clean();
            $result->success = false;

            // if it is a propel exception message
            if (preg_match( '/(.*)\s\[(.*):\s(.*)\]\s\[(.*):\s(.*)\]/', $e->getMessage(), $match )) {
                $result->message = $result->msg = $match[3];
                $result->type = ucfirst( $pmTable->getDbConfig()->adapter );
            } else {
                $result->message = $result->msg = $e->getMessage();
                $result->type = G::loadTranslation( 'ID_EXCEPTION' );
            }

            $result->trace = $e->getTraceAsString();
        }

        return $result;
    }

    /**
     * delete pm table
     *
     * @param string $httpData->rows
     */
    public function delete ($httpData)
    {
        $rows = G::json_decode( stripslashes( $httpData->rows ) );
        $errors = '';
        $count = 0;

        foreach ($rows as $row) {
            try {
                $at = new AdditionalTables();
                $table = $at->load( $row->id );

                if (! isset( $table )) {
                    require_once 'classes/model/ReportTable.php';
                    $rtOld = new ReportTable();
                    $existReportTableOld = $rtOld->load( $row->id );
                    if (count($existReportTableOld) == 0) {
                        throw new Exception( G::LoadTranslation('ID_TABLE_NOT_EXIST_SKIPPED') );
                    }
                }

                if ($row->type == 'CLASSIC') {
                    G::LoadClass( 'reportTables' );
                    $rp = new reportTables();
                    $rp->deleteReportTable( $row->id );
                    $count ++;
                } else {
                    $at->deleteAll( $row->id );
                    $count ++;
                }
            } catch (Exception $e) {
                $tableName = isset( $table['ADD_TAB_NAME'] ) ? $table['ADD_TAB_NAME'] : $row->id;
                $errors .= $e->getMessage() . "\n";
                continue;
            }
        }

        if ($errors == '') {
            $result->success = true;
            $result->message = $count.G::LoadTranslation( 'ID_TABLES_REMOVED_SUCCESSFULLY' );
        } else {
            $result->success = false;
            $result->message = $count. G::LoadTranslation( 'ID_TABLES_REMOVED_WITH_ERRORS' ) .$errors;
        }

        $result->errors = $errors;

        return $result;
    }

    /**
     * get pm tables data
     *
     * @param string $httpData->id
     * @param string $httpData->start
     * @param string $httpData->limit
     */
    public function dataView ($httpData)
    {
        require_once 'classes/model/AdditionalTables.php';

        G::LoadClass( 'configuration' );
        $co = new Configurations();
        $config = $co->getConfiguration( 'additionalTablesData', 'pageSize', '', $_SESSION['USER_LOGGED'] );
        $limit_size = isset( $config['pageSize'] ) ? $config['pageSize'] : 20;
        $start = isset( $httpData->start ) ? $httpData->start : 0;
        $limit = isset( $httpData->limit ) ? $httpData->limit : $limit_size;

        $additionalTables = new AdditionalTables();
        $table = $additionalTables->load( $httpData->id, true );
        $result = $additionalTables->getAllData( $httpData->id, $start, $limit );

        $primaryKeys = $additionalTables->getPrimaryKeys();

        foreach ($result['rows'] as $i => $row) {
            $primaryKeysValues = array ();
            foreach ($primaryKeys as $key) {
                $primaryKeysValues[] = isset( $row[$key['FLD_NAME']] ) ? $row[$key['FLD_NAME']] : '';
            }

            $result['rows'][$i]['__index__'] = G::encrypt( implode( ',', $primaryKeysValues ), 'pmtable' );
        }

        return $result;
    }

    /**
     * create pm tables record
     *
     * @param string $httpData->rows
     */
    public function dataCreate ($httpData, $codification = 'json')
    {
        if ($codification == 'base64') {
            $rows = unserialize( base64_decode( $httpData->rows ) );
        } else {
            $rows = G::json_decode( $httpData->rows );
        }

        try {
            require_once 'classes/model/AdditionalTables.php';
            $additionalTables = new AdditionalTables();
            $table = $additionalTables->load( $httpData->id, true );
            $primaryKeys = $additionalTables->getPrimaryKeys();

            $this->className = $table['ADD_TAB_CLASS_NAME'];
            $this->classPeerName = $this->className . 'Peer';
            $row = (array) $rows;

            $row = array_merge( array_change_key_case( $row, CASE_LOWER ), array_change_key_case( $row, CASE_UPPER ) );
            $toSave = false;

            if (! file_exists( PATH_WORKSPACE . 'classes/' . $this->className . '.php' )) {
                throw new Exception( 'Create::' . G::loadTranslation( 'ID_PMTABLE_CLASS_DOESNT_EXIST', $this->className ) );
            }

            require_once PATH_WORKSPACE . 'classes/' . $this->className . '.php';
            eval( '$obj = new ' . $this->className . '();' );

            if (count( $row ) > 0) {
                eval( '$con = Propel::getConnection(' . $this->classPeerName . '::DATABASE_NAME);' );
                $obj->fromArray( $row, BasePeer::TYPE_FIELDNAME );

                if ($obj->validate()) {
                    $obj->save();
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
                    throw new Exception( G::LoadTranslation('ID_ERROR_TRYING_INSERT'). '"' . $table['ADD_TAB_NAME'] . "\"\n" . $msg );
                }

                $index = G::encrypt( implode( ',', $primaryKeysValues ), 'pmtable' );
            } else {
                $toSave = false;
            }

            if ($toSave) {
                $result->success = true;
                $result->message = G::LoadTranslation('ID_RECORD_SAVED_SUCCESFULLY');
                $result->rows = $obj->toArray( BasePeer::TYPE_FIELDNAME );
                $result->rows['__index__'] = $index;
            } else {
                $result->success = false;
                $result->rows = array ();
                $result->message = '$$';
            }
        } catch (Exception $e) {
            $result->success = false;
            $result->rows = array ();
            $result->message = $e->getMessage();
        }

        return $result;
    }

    /**
     * update pm tables record
     *
     * @param string $httpData->id
     */
    public function dataUpdate ($httpData)
    {
        require_once 'classes/model/AdditionalTables.php';
        $oAdditionalTables = new AdditionalTables();
        $table = $oAdditionalTables->load( $httpData->id, true );
        $primaryKeys = $oAdditionalTables->getPrimaryKeys( 'keys' );
        $this->className = $table['ADD_TAB_CLASS_NAME'];
        $this->classPeerName = $this->className . 'Peer';
        $sPath = PATH_DB . SYS_SYS . PATH_SEP . 'classes' . PATH_SEP;

        if (! file_exists( $sPath . $this->className . '.php' )) {
            throw new Exception( 'Update:: ' . G::loadTranslation( 'ID_PMTABLE_CLASS_DOESNT_EXIST', $this->className ) );
        }

        require_once $sPath . $this->className . '.php';

        $rows = G::json_decode( stripslashes( $httpData->rows ) );

        if (is_array( $rows )) {
            foreach ($rows as $row) {
                $row = (array) $row;
                $result = $this->_dataUpdate( $row, $primaryKeys );
            }
        } else {
            //then is object
            $row = (array) $rows;
            $result = $this->_dataUpdate( $row, $primaryKeys );
        }

        $this->success = $result;
        $this->message = $result ? G::loadTranslation( 'ID_UPDATED_SUCCESSFULLY' ) : G::loadTranslation( 'ID_UPDATE_FAILED' );
    }

    /**
     * remove a pm tables record
     *
     * @param string $httpData->id
     */
    public function dataDestroy ($httpData)
    {
        require_once 'classes/model/AdditionalTables.php';
        $oAdditionalTables = new AdditionalTables();
        $table = $oAdditionalTables->load( $httpData->id, true );
        $this->className = $table['ADD_TAB_CLASS_NAME'];
        $this->classPeerName = $this->className . 'Peer';
        $sPath = PATH_DB . SYS_SYS . PATH_SEP . 'classes' . PATH_SEP;

        if (! file_exists( $sPath . $this->className . '.php' )) {
            throw new Exception( 'Destroy:: ' . G::loadTranslation( 'ID_PMTABLE_CLASS_DOESNT_EXIST', $this->className ) );
        }

        require_once $sPath . $this->className . '.php';

        $this->success = $this->_dataDestroy( $httpData->rows );
        $this->message = $this->success ? G::loadTranslation( 'ID_DELETED_SUCCESSFULLY' ) : G::loadTranslation( 'ID_DELETE_FAILED' );
    }

    /**
     * import a CSV to pm tables record
     *
     * @param string $httpData->id
     */
    public function importCSV ($httpData)
    {
        if (preg_match( '/[\x00-\x08\x0b-\x0c\x0e\x1f]/', file_get_contents( $_FILES['form']['tmp_name']['CSV_FILE'] ) ) === 0) {
            $filename = $_FILES['form']['name']['CSV_FILE'];
            if ($oFile = fopen( $_FILES['form']['tmp_name']['CSV_FILE'], 'r' )) {
                require_once 'classes/model/AdditionalTables.php';
                $oAdditionalTables = new AdditionalTables();
                $aAdditionalTables = $oAdditionalTables->load( $_POST['form']['ADD_TAB_UID'], true );
                $sErrorMessages = '';
                $i = 1;
                $swHead = false;
                while (($aAux = fgetcsv( $oFile, 4096, $_POST['form']['CSV_DELIMITER'] )) !== false) {
                    if (! is_null( $aAux[0] )) {
                        if (count( $aAdditionalTables['FIELDS'] ) > count( $aAux )) {
                            $this->success = false;
                            $this->message = G::LoadTranslation( 'INVALID_FILE' );
                            return 0;
                        }
                        if ($i == 1) {
                            $j = 0;
                            foreach ($aAdditionalTables['FIELDS'] as $aField) {
                                if ($aField['FLD_NAME'] === $aAux[$j]) {
                                    $swHead = true;
                                }
                                $j ++;
                            }
                        }

                        if ($swHead == false) {
                            $aData = array ();
                            $j = 0;
                            foreach ($aAdditionalTables['FIELDS'] as $aField) {
                                $aData[$aField['FLD_NAME']] = (isset( $aAux[$j] ) ? $aAux[$j] : '');
                                if ($aData[$aField['FLD_NAME']] == '') {
                                    switch ($aField['FLD_TYPE']) {
                                        case 'DATE':
                                        case 'TIMESTAMP':
                                            $aData[$aField['FLD_NAME']] = null;
                                            break;
                                    }
                                }
                                $j ++;
                            }
                            try {
                                if (! $oAdditionalTables->saveDataInTable( $_POST['form']['ADD_TAB_UID'], $aData )) {
                                    $sErrorMessages .= G::LoadTranslation( 'ID_DUPLICATE_ENTRY_PRIMARY_KEY' ) . ', ' . G::LoadTranslation( 'ID_LINE' ) . ' ' . $i . '. ';
                                }
                            } catch (Exception $oError) {
                                $sErrorMessages .= G::LoadTranslation( 'ID_ERROR_INSERT_LINE' ) . ': ' . G::LoadTranslation( 'ID_LINE' ) . ' ' . $i . '. ';
                            }
                        } else {
                            $swHead = false;
                        }
                        $i ++;
                    }
                }
                fclose( $oFile );
            }
            if ($sErrorMessages != '') {
                $this->success = false;
                $this->message = $sErrorMessages;
            } else {
                $this->success = true;
                $this->message = G::loadTranslation( 'ID_FILE_IMPORTED_SUCCESSFULLY', array ($filename
                ) );
            }
        } else {
            $sMessage = G::LoadTranslation( 'ID_UPLOAD_VALID_CSV_FILE' );
            $this->success = false;
            $this->message = $sMessage;
        }
    }

    /**
     * export a pm tables record to CSV
     *
     * @param string $httpData->id
     */
    public function exportCSV ($httpData)
    {

        try {

            $link = '';
            $size = '';
            $META = 'Content';
            $bytesSaved = 0;

            require_once 'classes/model/AdditionalTables.php';
            $oAdditionalTables = new AdditionalTables();
            $aAdditionalTables = $oAdditionalTables->load( $_POST['ADD_TAB_UID'], true );
            $sErrorMessages = '';
            $sDelimiter = $_POST['CSV_DELIMITER'];

            $resultData = $oAdditionalTables->getAllData( $_POST['ADD_TAB_UID'], null, null, false );
            $rows = $resultData['rows'];
            $count = $resultData['count'];

            $PUBLIC_ROOT_PATH = PATH_DATA . 'sites' . PATH_SEP . SYS_SYS . PATH_SEP . 'public' . PATH_SEP;
            $filenameOnly = strtolower( $aAdditionalTables['ADD_TAB_NAME'] . "_" . date( "Y-m-d" ) . '_' . date( "Hi" ) . ".csv" );
            $filename = $PUBLIC_ROOT_PATH . $filenameOnly;
            $fp = fopen( $filename, "wb" );

            foreach ($rows as $keyCol => $cols) {
                $SDATA = "";
                $cnt = count( $cols );
                foreach ($cols as $key => $val) {
                    $SDATA .= $val;
                    if (-- $cnt > 0) {
                        $SDATA .= $sDelimiter;
                    }
                }
                $SDATA .= "\n";
                $bytesSaved += fwrite( $fp, $SDATA );
            }

            fclose( $fp );

            // $filenameLink = "pmTables/streamExported?f=$filenameOnly";
            $filenameLink = "streamExported?f=$filenameOnly";
            $size = round( ($bytesSaved / 1024), 2 ) . " Kb";
            $filename = $filenameOnly;
            $link = $filenameLink;

            $result->success = true;
            $result->filename = $filenameOnly;
            $result->link = $link;
            $result->message = "Generated file: $filenameOnly, size: $size";
        } catch (Exception $e) {
            $result->success = false;
            $result->message = $e->getMessage();
        }

        return $result;
    }

    /**
     * import a pm table
     *
     * @param string $httpData->id
     */
    public function import ($httpData)
    {
        require_once 'classes/model/AdditionalTables.php';
        try {
            $errors = '';

            $overWrite = isset( $_POST['form']['OVERWRITE'] ) ? true : false;

            //save the file
            if ($_FILES['form']['error']['FILENAME'] !== 0) {
                throw new Exception( G::loadTranslation( 'ID_PMTABLE_UPLOADING_FILE_PROBLEM' ) );
            }

            $oAdditionalTables = new AdditionalTables();
            $tableNameMap = array ();
            $processQueue = array ();
            $processQueueTables = array ();

            $PUBLIC_ROOT_PATH = PATH_DATA . 'sites' . PATH_SEP . SYS_SYS . PATH_SEP . 'public' . PATH_SEP;
            $filename = $_FILES['form']['name']['FILENAME'];
            $tempName = $_FILES['form']['tmp_name']['FILENAME'];
            G::uploadFile( $tempName, $PUBLIC_ROOT_PATH, $filename );

            $fileContent = file_get_contents( $PUBLIC_ROOT_PATH . $filename );

            if (strpos( $fileContent, '-----== ProcessMaker Open Source Private Tables ==-----' ) === false) {
                throw new Exception( G::loadTranslation( 'ID_PMTABLE_INVALID_FILE' ) );
            }

            $fp = fopen( $PUBLIC_ROOT_PATH . $filename, "rb" );
            $fsData = intval( fread( $fp, 9 ) ); //reading the metadata
            $sType = fread( $fp, $fsData );

            // first create the tables structures
            while (! feof( $fp )) {
                switch ($sType) {
                    case '@META':
                        $fsData = intval( fread( $fp, 9 ) );
                        $METADATA = fread( $fp, $fsData );
                        break;
                    case '@SCHEMA':
                        $fsUid = intval( fread( $fp, 9 ) );
                        $uid = fread( $fp, $fsUid );
                        $fsData = intval( fread( $fp, 9 ) );
                        $schema = fread( $fp, $fsData );
                        $contentSchema = unserialize( $schema );
                        $additionalTable = new additionalTables();
                        $tableExists = $additionalTable->loadByName( $contentSchema['ADD_TAB_NAME'] );
                        $tableNameMap[$contentSchema['ADD_TAB_NAME']] = $contentSchema['ADD_TAB_NAME'];

                        if ($overWrite) {
                            if ($tableExists !== false) {
                                $additionalTable->deleteAll( $tableExists['ADD_TAB_UID'] );
                            }
                        } else {
                            if ($tableExists !== false) {
                                // some table exists with the same name
                                // renaming...
                                $tNameOld = $contentSchema['ADD_TAB_NAME'];
                                $newTableName = $contentSchema['ADD_TAB_NAME'] . '_' . date( 'YmdHis' );
                                $contentSchema['ADD_TAB_UID'] = G::generateUniqueID();
                                $contentSchema['ADD_TAB_NAME'] = $newTableName;
                                $contentSchema['ADD_TAB_CLASS_NAME'] = additionalTables::getPHPName( $newTableName );
                                //mapping the table name for posterior uses
                                $tableNameMap[$tNameOld] = $contentSchema['ADD_TAB_NAME'];
                            }
                        }

                        // validating invalid bds_uid in old tables definition -> mapped to workflow
                        if (! $contentSchema['DBS_UID'] || $contentSchema['DBS_UID'] == '0' || ! $contentSchema['DBS_UID']) {
                            $contentSchema['DBS_UID'] = 'workflow';
                        }

                        $columns = array ();
                        foreach ($contentSchema['FIELDS'] as $field) {
                            $column = array ('uid' => '','field_uid' => '','field_name' => $field['FLD_NAME'],'field_dyn' => isset( $field['FLD_DYN_NAME'] ) ? $field['FLD_DYN_NAME'] : '','field_label' => isset( $field['FLD_DESCRIPTION'] ) ? $field['FLD_DESCRIPTION'] : '','field_type' => $field['FLD_TYPE'],'field_size' => $field['FLD_SIZE'],'field_key' => isset( $field['FLD_KEY'] ) ? $field['FLD_KEY'] : 0,'field_null' => isset( $field['FLD_NULL'] ) ? $field['FLD_NULL'] : 1,'field_autoincrement' => isset( $field['FLD_AUTO_INCREMENT'] ) ? $field['FLD_AUTO_INCREMENT'] : 0
                            );
                            $columns[] = $column;
                        }

                        $tableData = new stdClass();
                        $tableData->REP_TAB_UID = $contentSchema['ADD_TAB_UID'];
                        $tableData->REP_TAB_NAME = $contentSchema['ADD_TAB_NAME'];
                        $tableData->REP_TAB_DSC = $contentSchema['ADD_TAB_DESCRIPTION'];
                        $tableData->REP_TAB_CONNECTION = $contentSchema['DBS_UID'];

                        if (isset( $_POST["form"]["PRO_UID"] ) && ! empty( $_POST["form"]["PRO_UID"] )) {
                            $tableData->PRO_UID = $_POST["form"]["PRO_UID"];
                        } else {
                            $tableData->PRO_UID = isset( $contentSchema["PRO_UID"] ) ? $contentSchema["PRO_UID"] : "";
                        }

                        $tableData->REP_TAB_TYPE = isset( $contentSchema['ADD_TAB_TYPE'] ) ? $contentSchema['ADD_TAB_TYPE'] : '';
                        $tableData->REP_TAB_GRID = isset( $contentSchema['ADD_TAB_GRID'] ) ? $contentSchema['ADD_TAB_GRID'] : '';
                        $tableData->columns = G::json_encode( $columns );
                        $tableData->forceUid = true;

                        //save the table
                        $alterTable = false;
                        $result = $this->save( $tableData, $alterTable );

                        if ($result->success) {
                            $processQueueTables[$contentSchema['DBS_UID']][] = $contentSchema['ADD_TAB_NAME'];
                        } else {
                            $errors .= 'Error creating table: ' . $tableData->REP_TAB_NAME . '-> ' . $result->message . "\n\n";
                        }

                        break;
                    case '@DATA':
                        $fstName = intval( fread( $fp, 9 ) );
                        $tableName = fread( $fp, $fstName );
                        $fsData = intval( fread( $fp, 9 ) );
                        if ($fsData > 0) {
                            $data = fread( $fp, $fsData );
                        }
                        break;
                }

                $fsData = intval( fread( $fp, 9 ) ); //reading the metadata
                if ($fsData > 0) {
                    // reading next block type
                    $sType = fread( $fp, $fsData );
                } else {
                    break;
                }
            }

            fclose( $fp );
            G::loadClass( 'pmTable' );

            foreach ($processQueueTables as $dbsUid => $tables) {
                $pmTable = new pmTable();
                ob_start();
                $pmTable->buildModelFor( $dbsUid, $tables );
                $buildResult = ob_get_contents();
                ob_end_clean();
                $errors .= $pmTable->upgradeDatabaseFor( $pmTable->getDataSource(), $tables );
            }

            $fp = fopen( $PUBLIC_ROOT_PATH . $filename, "rb" );
            $fsData = intval( fread( $fp, 9 ) );
            $sType = fread( $fp, $fsData );
            // data processing


            while (! feof( $fp )) {

                switch ($sType) {
                    case '@META':
                        $fsData = intval( fread( $fp, 9 ) );
                        $METADATA = fread( $fp, $fsData );
                        break;
                    case '@SCHEMA':
                        $fsUid = intval( fread( $fp, 9 ) );
                        $uid = fread( $fp, $fsUid );
                        $fsData = intval( fread( $fp, 9 ) );
                        $schema = fread( $fp, $fsData );
                        $contentSchema = unserialize( $schema );
                        $additionalTable = new additionalTables();
                        $table = $additionalTable->loadByName( $tableNameMap[$contentSchema['ADD_TAB_NAME']] );
                        if ($table['PRO_UID'] != '') {
                            // is a report table, try populate it
                            $additionalTable->populateReportTable( $table['ADD_TAB_NAME'], pmTable::resolveDbSource( $table['DBS_UID'] ), $table['ADD_TAB_TYPE'], $table['PRO_UID'], $table['ADD_TAB_GRID'], $table['ADD_TAB_UID'] );
                        }
                        break;
                    case '@DATA':
                        $fstName = intval( fread( $fp, 9 ) );
                        $tableName = fread( $fp, $fstName );
                        $fsData = intval( fread( $fp, 9 ) );

                        if ($fsData > 0) {
                            $data = fread( $fp, $fsData );
                            $contentData = unserialize( $data );
                            $tableName = $tableNameMap[$tableName];

                            $oAdditionalTables = new AdditionalTables();
                            $table = $oAdditionalTables->loadByName( $tableName );
                            $isReport = $table['PRO_UID'] !== '' ? true : false;

                            if ($table !== false) {
                                if (! $isReport) {
                                    if (count( $contentData ) > 0) {
                                        foreach ($contentData as $row) {
                                            $data = new StdClass();
                                            $data->id = $table['ADD_TAB_UID'];
                                            $data->rows = base64_encode( serialize( $row ) );
                                            $res = $this->dataCreate( $data, 'base64' );
                                            if (! $res->success) {
                                                $errors .= $res->message;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                        break;
                }

                $fsData = intval( fread( $fp, 9 ) );
                if ($fsData > 0) {
                    $sType = fread( $fp, $fsData );
                } else {
                    break;
                }
            }

            ////////////


            if ($errors == '') {
                $result->success = true;
                $msg = G::loadTranslation( 'ID_PMTABLE_IMPORT_SUCCESS', array ($filename
                ) );
            } else {
                $result->success = false;
                $result->errorType = 'warning';
                $msg = G::loadTranslation( 'ID_PMTABLE_IMPORT_WITH_ERRORS', array ($filename
                ) ) . "\n\n" . $errors;
            }

            $result->message = $msg;
        } catch (Exception $e) {
            $result->errorType = 'error';
            $result->buildResult = ob_get_contents();
            ob_end_clean();
            $result->success = false;

            // if it is a propel exception message
            if (preg_match( '/(.*)\s\[(.*):\s(.*)\]\s\[(.*):\s(.*)\]/', $e->getMessage(), $match )) {
                $result->message = $match[3];
                $result->type = G::loadTranslation( 'ID_ERROR' );
            } else {
                $result->message = $e->getMessage();
                $result->type = G::loadTranslation( 'ID_EXCEPTION' );
            }
            //$result->trace = $e->getTraceAsString();
        }

        return $result;
    }

    /**
     * Export PM tables
     *
     * @author : Erik Amaru Ortiz <aortiz.erik@gmail.com>
     */
    public function export ($httpData)
    {
        require_once 'classes/model/AdditionalTables.php';
        $at = new AdditionalTables();
        $tablesToExport = G::json_decode( stripslashes( $httpData->rows ) );

        try {
            G::LoadCLass( 'net' );
            $net = new NET( G::getIpAddress() );

            G::LoadClass( "system" );

            $META = " \n-----== ProcessMaker Open Source Private Tables ==-----\n" . " @Ver: 1.0 Oct-2009\n" . " @Processmaker version: " . System::getVersion() . "\n" . " -------------------------------------------------------\n" . " @Export Date: " . date( "l jS \of F Y h:i:s A" ) . "\n" . " @Server address: " . getenv( 'SERVER_NAME' ) . " (" . getenv( 'SERVER_ADDR' ) . ")\n" . " @Client address: " . $net->hostname . "\n" . " @Workspace: " . SYS_SYS . "\n" . " @Export trace back:\n\n";

            $EXPORT_TRACEBACK = Array ();
            $c = 0;
            foreach ($tablesToExport as $table) {
                $tableRecord = $at->load( $table->ADD_TAB_UID );
                $tableData = $at->getAllData( $table->ADD_TAB_UID, null, null, false );
                $table->ADD_TAB_NAME = $tableRecord['ADD_TAB_NAME'];
                $rows = $tableData['rows'];
                $count = $tableData['count'];

                array_push( $EXPORT_TRACEBACK, Array ('uid' => $table->ADD_TAB_UID,'name' => $table->ADD_TAB_NAME,'num_regs' => $tableData['count'],'schema' => $table->_SCHEMA ? 'yes' : 'no','data' => $table->_DATA ? 'yes' : 'no'
                ) );
            }

            $sTrace = "TABLE UID                        TABLE NAME\tREGS\tSCHEMA\tDATA\n";

            foreach ($EXPORT_TRACEBACK as $row) {
                $sTrace .= "{$row['uid']}\t{$row['name']}\t\t{$row['num_regs']}\t{$row['schema']}\t{$row['data']}\n";
            }

            $META .= $sTrace;

            ///////////////EXPORT PROCESS
            $PUBLIC_ROOT_PATH = PATH_DATA . 'sites' . PATH_SEP . SYS_SYS . PATH_SEP . 'public' . PATH_SEP;

            $filenameOnly = strtolower( 'SYS-' . SYS_SYS . "_" . date( "Y-m-d" ) . '_' . date( "Hi" ) . ".pmt" );

            $filename = $PUBLIC_ROOT_PATH . $filenameOnly;
            $fp = fopen( $filename, "wb" );

            $bytesSaved = 0;
            $bufferType = '@META';
            $fsData = sprintf( "%09d", strlen( $META ) );
            $fsbufferType = sprintf( "%09d", strlen( $bufferType ) );
            $bytesSaved += fwrite( $fp, $fsbufferType ); //writing the size of $oData
            $bytesSaved += fwrite( $fp, $bufferType ); //writing the $oData
            $bytesSaved += fwrite( $fp, $fsData ); //writing the size of $oData
            $bytesSaved += fwrite( $fp, $META ); //writing the $oData


            foreach ($tablesToExport as $table) {

                if ($table->_SCHEMA) {
                    $oAdditionalTables = new AdditionalTables();
                    $aData = $oAdditionalTables->load( $table->ADD_TAB_UID, true );

                    $bufferType = '@SCHEMA';
                    $SDATA = serialize( $aData );
                    $fsUid = sprintf( "%09d", strlen( $table->ADD_TAB_UID ) );
                    $fsData = sprintf( "%09d", strlen( $SDATA ) );
                    $fsbufferType = sprintf( "%09d", strlen( $bufferType ) );

                    $bytesSaved += fwrite( $fp, $fsbufferType ); //writing the size of $oData
                    $bytesSaved += fwrite( $fp, $bufferType ); //writing the $oData
                    $bytesSaved += fwrite( $fp, $fsUid ); //writing the size of xml file
                    $bytesSaved += fwrite( $fp, $table->ADD_TAB_UID ); //writing the xmlfile
                    $bytesSaved += fwrite( $fp, $fsData ); //writing the size of xml file
                    $bytesSaved += fwrite( $fp, $SDATA ); //writing the xmlfile
                }

                if ($table->_DATA) {
                    //export data
                    $oAdditionalTables = new additionalTables();
                    $tableData = $oAdditionalTables->getAllData( $table->ADD_TAB_UID, null, null, false );

                    $SDATA = serialize( $tableData['rows'] );
                    $bufferType = '@DATA';

                    $fsbufferType = sprintf( "%09d", strlen( $bufferType ) );
                    $fsTableName = sprintf( "%09d", strlen( $table->ADD_TAB_NAME ) );
                    $fsData = sprintf( "%09d", strlen( $SDATA ) );

                    $bytesSaved += fwrite( $fp, $fsbufferType ); //writing type size
                    $bytesSaved += fwrite( $fp, $bufferType ); //writing type
                    $bytesSaved += fwrite( $fp, $fsTableName ); //writing the size of xml file
                    $bytesSaved += fwrite( $fp, $table->ADD_TAB_NAME ); //writing the xmlfile
                    $bytesSaved += fwrite( $fp, $fsData ); //writing the size of xml file
                    $bytesSaved += fwrite( $fp, $SDATA ); //writing the xmlfile
                }
            }

            fclose( $fp );

            $filenameLink = "pmTables/streamExported?f=$filenameOnly";
            $size = round( ($bytesSaved / 1024), 2 ) . " Kb";
            $meta = "<pre>" . $META . "</pre>";
            $filename = $filenameOnly;
            $link = $filenameLink;

            $result->success = true;
            $result->filename = $filenameOnly;
            $result->link = $link;
            $result->message = "Generated file: $filenameOnly, size: $size";
        } catch (Exception $e) {
            $result->success = false;
            $result->message = $e->getMessage();
        }

        return $result;
    }

    public function exportList ()
    {
        require_once 'classes/model/AdditionalTables.php';

        $oCriteria = new Criteria( 'workflow' );
        $oCriteria->addSelectColumn( AdditionalTablesPeer::ADD_TAB_UID );
        $oCriteria->addSelectColumn( AdditionalTablesPeer::ADD_TAB_NAME );
        $oCriteria->addSelectColumn( AdditionalTablesPeer::ADD_TAB_DESCRIPTION );
        $oCriteria->addSelectColumn( "'" . G::LoadTranslation( 'ID_ACTION_EXPORT' ) . "' as 'CH_SCHEMA'" );
        $oCriteria->addSelectColumn( "'" . G::LoadTranslation( 'ID_ACTION_EXPORT' ) . "' as 'CH_DATA'" );

        $uids = explode( ',', $_GET['id'] );

        foreach ($uids as $UID) {
            if (! isset( $CC )) {
                $CC = $oCriteria->getNewCriterion( AdditionalTablesPeer::ADD_TAB_UID, $UID, Criteria::EQUAL );
            } else {
                $CC->addOr( $oCriteria->getNewCriterion( AdditionalTablesPeer::ADD_TAB_UID, $UID, Criteria::EQUAL ) );
            }
        }
        $oCriteria->add( $CC );
        $oCriteria->addAnd( $oCriteria->getNewCriterion( AdditionalTablesPeer::ADD_TAB_UID, '', Criteria::NOT_EQUAL ) );

        $oDataset = AdditionalTablesPeer::doSelectRS( $oCriteria );
        $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );

        $addTables = Array ();
        while ($oDataset->next()) {
            $addTables[] = $oDataset->getRow();
        }

        return $addTables;
    }

    public function updateTag ($httpData)
    {
        require_once 'classes/model/AdditionalTables.php';
        $oAdditionalTables = new AdditionalTables();
        $uid = $_REQUEST['ADD_TAB_UID'];
        $value = $_REQUEST['value'];

        $repTabData = array ('ADD_TAB_UID' => $uid,'ADD_TAB_TAG' => $value
        );
        $oAdditionalTables->update( $repTabData );
    }

    /**
     * - protected functions (non callable from controller outside) -
     */

    /**
     * Update data from a addTable record
     *
     * @param $row
     */
    public function _dataUpdate ($row, $primaryKeys)
    {
        $keys = G::decrypt( $row['__index__'], 'pmtable' );
        $keys = explode( ',', $keys );
        unset( $row['__index__'] );

        $params = array ();

        foreach ($keys as $key) {
            $params[] = is_numeric( $key ) ? $key : "'$key'";
        }

        $obj = null;
        eval( '$obj = ' . $this->classPeerName . '::retrieveByPk(' . implode( ',', $params ) . ');' );

        if (is_object( $obj )) {
            foreach ($row as $key => $value) {
                // validation, don't modify primary keys
                if (in_array( $key, $primaryKeys )) {
                    throw new Exception( G::loadTranslation( 'ID_DONT_MODIFY_PK_VALUE', array ($key
                    ) ) );
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
                throw new Exception( $msg );
            }
        } else {
            $result = false;
        }

        return $result;
    }

    /**
     * Update data from a addTable record
     *
     * @param $row
     */
    public function _dataDestroy ($row)
    {
        $row = G::decrypt( $row, 'pmtable' );
        $row = str_replace( '"', '', $row );
        $keys = explode( ',', $row );
        $params = array ();
        foreach ($keys as $key) {
            $params[] = is_numeric( $key ) ? $key : "'$key'";
        }

        $obj = null;
        eval( '$obj = ' . $this->classPeerName . '::retrieveByPk(' . implode( ',', $params ) . ');' );

        if (is_object( $obj )) {
            $obj->delete();
            return true;
        } else {
            return false;
        }
    }

    public function genDataReport ($httpData)
    {
        G::loadClass( 'pmTable' );
        require_once 'classes/model/AdditionalTables.php';
        $result->message = '';
        $result->success = true;

        $additionalTables = new AdditionalTables();
        $table = $additionalTables->load( $httpData->id );
        if ($table['PRO_UID'] != '') {
            $additionalTables->populateReportTable( $table['ADD_TAB_NAME'], pmTable::resolveDbSource( $table['DBS_UID'] ), $table['ADD_TAB_TYPE'], $table['PRO_UID'], $table['ADD_TAB_GRID'], $table['ADD_TAB_UID'] );
            $result->message = 'generated for table ' . $table['ADD_TAB_NAME'];
        }

        return $result;
    }

    /**
     * Get report table default columns
     *
     * @param $type
     */
    protected function _getReportTableDefaultColumns ($type = 'NORMAL')
    {
        $defaultColumns = array ();
        $application = new stdClass(); //APPLICATION KEY
        $application->uid = '';
        $application->field_dyn = '';
        $application->field_uid = '';
        $application->field_name = 'APP_UID';
        $application->field_label = 'APP_UID';
        $application->field_type = 'VARCHAR';
        $application->field_size = 32;
        $application->field_dyn = '';
        $application->field_key = 1;
        $application->field_null = 0;
        $application->field_filter = false;
        $application->field_autoincrement = false;
        array_push( $defaultColumns, $application );

        $application = new stdClass(); //APP_NUMBER
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
        array_push( $defaultColumns, $application );

        $application = new stdClass(); //APP_STATUS
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
        array_push( $defaultColumns, $application );

        //if it is a grid report table
        if ($type == 'GRID') {
            //GRID INDEX
            $gridIndex = new stdClass();
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
            array_push( $defaultColumns, $gridIndex );
        }

        return $defaultColumns;
    }

    /**
     * Get all dynaform fields from a process (without grid fields)
     *
     * @param $proUid
     * @param $type [values:xmlform/grid]
     */
    public function _getDynafields2 ($proUid, $type = 'xmlform')
    {
        require_once 'classes/model/Dynaform.php';
        $fields = array ();
        $fieldsNames = array ();

        $oCriteria = new Criteria( 'workflow' );
        $oCriteria->addSelectColumn( DynaformPeer::DYN_FILENAME );
        $oCriteria->add( DynaformPeer::PRO_UID, $proUid );
        $oCriteria->add( DynaformPeer::DYN_TYPE, $type );
        $oDataset = DynaformPeer::doSelectRS( $oCriteria );
        $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );
        $oDataset->next();

        $excludeFieldsList = array ('title','subtitle','link','file','button','reset','submit','listbox','checkgroup','grid','javascript'
        );

        $labelFieldsTypeList = array ('dropdown','checkbox','radiogroup','yesno'
        );

        while ($aRow = $oDataset->getRow()) {
            if (file_exists( PATH_DYNAFORM . PATH_SEP . $aRow['DYN_FILENAME'] . '.xml' )) {
                $G_FORM = new Form( $aRow['DYN_FILENAME'], PATH_DYNAFORM, SYS_LANG );

                if ($G_FORM->type == 'xmlform' || $G_FORM->type == '') {
                    foreach ($G_FORM->fields as $fieldName => $fieldNode) {
                        if (! in_array( $fieldNode->type, $excludeFieldsList ) && ! in_array( $fieldName, $fieldsNames )) {
                            $fields[] = array ('name' => $fieldName,'type' => $fieldNode->type,'label' => $fieldNode->label
                            );
                            $fieldsNames[] = $fieldName;

                            if (in_array( $fieldNode->type, $labelFieldsTypeList ) && ! in_array( $fieldName . '_label', $fieldsNames )) {
                                $fields[] = array ('name' => $fieldName . '_label','type' => $fieldNode->type,'label' => $fieldNode->label . '_label'
                                );
                                $fieldsNames[] = $fieldName;
                            }
                        }
                    }
                }
            }
            $oDataset->next();
        }

        return $fields;
    }

    public function _getDynafields ($proUid, $type = 'xmlform', $start = null, $limit = null, $filter = null)
    {
        $cache = 1;
        if (! isset( $_SESSION['_cache_pmtables'] ) || (isset( $_SESSION['_cache_pmtables'] ) && $_SESSION['_cache_pmtables']['pro_uid'] != $proUid) || (isset( $_SESSION['_cache_pmtables'] ) && $_SESSION['_cache_pmtables']['dyn_uid'] != $this->dynUid)) {

            require_once 'classes/model/Dynaform.php';
            $cache = 0;
            $fields = array ();
            $fieldsNames = array ();

            $oCriteria = new Criteria( 'workflow' );
            $oCriteria->addSelectColumn( DynaformPeer::DYN_FILENAME );
            $oCriteria->add( DynaformPeer::PRO_UID, $proUid );
            $oCriteria->add( DynaformPeer::DYN_TYPE, $type );

            if (isset( $this->dynUid )) {
                $oCriteria->add( DynaformPeer::DYN_UID, $this->dynUid );
            }

            $oDataset = DynaformPeer::doSelectRS( $oCriteria );
            $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );
            $oDataset->next();

            $excludeFieldsList = array ('title','subtitle','link','file','button','reset','submit','listbox','checkgroup','grid','javascript', ''
            );

            $labelFieldsTypeList = array ('dropdown','radiogroup');
            G::loadSystem( 'dynaformhandler' );
            $index = 0;

            while ($aRow = $oDataset->getRow()) {
                if (file_exists( PATH_DYNAFORM . PATH_SEP . $aRow['DYN_FILENAME'] . '.xml' )) {
                    $dynaformHandler = new dynaformHandler( PATH_DYNAFORM . $aRow['DYN_FILENAME'] . '.xml' );
                    $nodeFieldsList = $dynaformHandler->getFields();

                    foreach ($nodeFieldsList as $node) {
                        $arrayNode = $dynaformHandler->getArray( $node );
                        $fieldName = $arrayNode['__nodeName__'];
                        $fieldType = isset($arrayNode['type']) ? $arrayNode['type']: '';
                        $fieldValidate = ( isset($arrayNode['validate'])) ? $arrayNode['validate'] : '';

                        if (! in_array( $fieldType, $excludeFieldsList ) && ! in_array( $fieldName, $fieldsNames ) ) {
                            $fields[] = array (
                                'FIELD_UID' => $fieldName . '-' . $fieldType,
                                'FIELD_NAME' => $fieldName,
                                'FIELD_VALIDATE'=>$fieldValidate,
                                '_index' => $index ++,
                                '_isset' => true
                            );
                            $fieldsNames[] = $fieldName;

                            if (in_array( $fieldType, $labelFieldsTypeList ) && ! in_array( $fieldName . '_label', $fieldsNames )) {
                                $fields[] = array (
                                    'FIELD_UID' => $fieldName . '_label' . '-' . $fieldType,
                                    'FIELD_NAME' => $fieldName . '_label',
                                    'FIELD_VALIDATE'=>$fieldValidate,
                                    '_index' => $index ++,
                                    '_isset' => true
                                );
                                $fieldsNames[] = $fieldName;
                            }
                        }
                    }
                }
                $oDataset->next();
            }

            sort( $fields );

            // if is a editing
            $fieldsEdit = array ();
            if (isset( $_SESSION['ADD_TAB_UID'] )) {
                require_once 'classes/model/AdditionalTables.php';

                $additionalTables = new AdditionalTables();
                $table = $additionalTables->load( $_SESSION['ADD_TAB_UID'], true );

                foreach ($table['FIELDS'] as $i => $field) {
                    array_push( $fieldsEdit, $field['FLD_DYN_NAME'] );
                }
            } //end editing

            $indexes = array();
            foreach ($fields as $i => $field) {
                $fields[$i]['_index'] = $i;
                $indexes[$field['FIELD_NAME']] = $i;

                if (in_array( $field['FIELD_NAME'], $fieldsEdit )) {
                    $fields[$i]['_isset'] = false;
                }
            }

            $_SESSION['_cache_pmtables']['pro_uid'] = $proUid;
            $_SESSION['_cache_pmtables']['dyn_uid'] = $this->dynUid;
            $_SESSION['_cache_pmtables']['rows'] = $fields;
            $_SESSION['_cache_pmtables']['count'] = count( $fields );
            $_SESSION['_cache_pmtables']['indexes'] = $indexes;
        } //end reload


        $fields = array ();
        $tmp = array ();

        foreach ($_SESSION['_cache_pmtables']['rows'] as $i => $row) {
            if (isset( $filter ) && $filter != '') {
                if ($row['_isset'] && stripos( $row['FIELD_NAME'], $filter ) !== false) {
                    $tmp[] = $row;
                }
            } else {
                if ($row['_isset']) {
                    $tmp[] = $row;
                }
            }
        }

        $fields = array_slice( $tmp, $start, $limit );

        return array ('cache' => $cache,'count' => count( $tmp ),'rows' => $fields
        );
    }

    /**
     * Get all dynaform grid fields from a process
     *
     * @param $proUid
     * @param $gridId
     */
    public function _getGridDynafields ($proUid, $gridId)
    {
        $fields = array ();
        $fieldsNames = array ();
        $excludeFieldsList = array ('title','subtitle','link','file','button','reset','submit','listbox','checkgroup','grid','javascript'
        );

        $labelFieldsTypeList = array ('dropdown','checkbox','radiogroup','yesno'
        );

        $G_FORM = new Form( $proUid . '/' . $gridId, PATH_DYNAFORM, SYS_LANG, false );

        if ($G_FORM->type == 'grid') {
            foreach ($G_FORM->fields as $fieldName => $fieldNode) {
                if (! in_array( $fieldNode->type, $excludeFieldsList ) && ! in_array( $fieldName, $fieldsNames )) {
                    $fields[] = array ('name' => $fieldName,'type' => $fieldNode->type,'label' => $fieldNode->label
                    );
                    $fieldsNames[] = $fieldName;

                    if (in_array( $fieldNode->type, $labelFieldsTypeList ) && ! in_array( $fieldName . '_label', $fieldsNames )) {
                        $fields[] = array ('name' => $fieldName . '_label','type' => $fieldNode->type,'label' => $fieldNode->label . '_label'
                        );
                        $fieldsNames[] = $fieldName;
                    }
                }
            }
        }

        return $fields;
    }

    /**
     * Get all dynaform fields inside all grids from a process
     *
     * @param $proUid
     */
    public function _getGridFields ($proUid)
    {
        require_once 'classes/model/Dynaform.php';
        G::loadSystem( 'dynaformhandler' );
        $aFields = array ();
        $aFieldsNames = array ();

        $oCriteria = new Criteria( 'workflow' );
        $oCriteria->addSelectColumn( DynaformPeer::DYN_FILENAME );
        $oCriteria->add( DynaformPeer::PRO_UID, $proUid );
        $oCriteria->add( DynaformPeer::DYN_TYPE, 'xmlform' );
        $oDataset = DynaformPeer::doSelectRS( $oCriteria );
        $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );
        $oDataset->next();

        while ($aRow = $oDataset->getRow()) {
            //$G_FORM  = new Form($aRow['DYN_FILENAME'], PATH_DYNAFORM, SYS_LANG);
            $dynaformHandler = new dynaformHandler( PATH_DYNAFORM . $aRow['DYN_FILENAME'] . '.xml' );
            $nodeFieldsList = $dynaformHandler->getFields();

            foreach ($nodeFieldsList as $node) {
                $arrayNode = $dynaformHandler->getArray( $node );
                $fieldName = $arrayNode['__nodeName__'];
                $fieldType = $arrayNode['type'];

                if ($fieldType == 'grid') {

                    if (! in_array( $fieldName, $aFieldsNames )) {
                        $aFields[] = array ('name' => $fieldName,'xmlform' => str_replace( $proUid . '/', '', $arrayNode['xmlgrid'] )
                        );
                        $aFieldsNames[] = $fieldName;
                    }
                }
            }

            $oDataset->next();
        }
        return $aFields;
    }
}

