<?php
/**
 * pmTablesProxy
 *
 * @author Erik Amaru Ortiz <erik@colosa.com, aortiz.erik@gmail.com>
 * @inherits HttpProxyController
 * @access public
 */

use ProcessMaker\Core\System;

header("Content-type: text/html;charset=utf-8");
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
        $configurations = new Configurations();
        $processMap = new ProcessMap();

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
            $addTables = AdditionalTables::getAll( false, false, $filter, $process );

            $c = $processMap->getReportTablesCriteria( $pro_uid );
            $oDataset = RoutePeer::doSelectRS( $c );
            $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );
            $reportTablesOldList = array ();
            while ($oDataset->next()) {
                $reportTablesOldList[] = $oDataset->getRow();
            }
            foreach ($reportTablesOldList as $i => $oldRepTab) {
            	if($filter != ''){
            		if((stripos($oldRepTab['REP_TAB_NAME'], $filter) !== false) || (stripos($oldRepTab['REP_TAB_TITLE'], $filter) !== false)){
            			$addTables['rows'][] = array ('ADD_TAB_UID' => $oldRepTab['REP_TAB_UID'],'PRO_UID' => $oldRepTab['PRO_UID'],'DBS_UID' => ($oldRepTab['REP_TAB_CONNECTION'] == 'wf' ? 'workflow' : 'rp'),'ADD_TAB_DESCRIPTION' => $oldRepTab['REP_TAB_TITLE'],'ADD_TAB_NAME' => $oldRepTab['REP_TAB_NAME'],'ADD_TAB_TYPE' => $oldRepTab['REP_TAB_TYPE'],'TYPE' => 'CLASSIC' );
            		}
            	} else {
            		$addTables['rows'][] = array ('ADD_TAB_UID' => $oldRepTab['REP_TAB_UID'],'PRO_UID' => $oldRepTab['PRO_UID'],'DBS_UID' => ($oldRepTab['REP_TAB_CONNECTION'] == 'wf' ? 'workflow' : 'rp'),'ADD_TAB_DESCRIPTION' => $oldRepTab['REP_TAB_TITLE'],'ADD_TAB_NAME' => $oldRepTab['REP_TAB_NAME'],'ADD_TAB_TYPE' => $oldRepTab['REP_TAB_TYPE'],'TYPE' => 'CLASSIC' );
            	}
            }
            $addTables['count'] = count($addTables['rows']);
            if($start != 0){
           	    $addTables['rows'] = array_splice($addTables['rows'], $start);
            }
            $addTables['rows'] = array_splice($addTables['rows'], 0, $limit);
        } else {
            $addTables = AdditionalTables::getAll( $start, $limit, $filter );
        }

        foreach ($addTables['rows'] as $i => $table) {
            try {
                $con = Propel::getConnection( PmTable::resolveDbSource( $table['DBS_UID'] ) );
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
        $proUid = $_POST['PRO_UID'];
        $dbConn = new DbConnections();
        $dbConnections = $dbConn->getConnectionsProUid( $proUid, array('mysql') );

        $workSpace = new WorkspaceTools(SYS_SYS);
        $workspaceDB = $workSpace->getDBInfo();

        if ($workspaceDB['DB_NAME'] == $workspaceDB['DB_RBAC_NAME']) {
            $defaultConnections = array (array ('DBS_UID' => 'workflow','DBS_NAME' => 'Workflow'));
        } else {
            $defaultConnections = array (array ('DBS_UID' => 'workflow','DBS_NAME' => 'Workflow'),
                                         array ('DBS_UID' => 'rp','DBS_NAME' => 'REPORT'));
        }

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

        $aFields['FIELDS'] = array ();
        $aFields['PRO_UID'] = $httpData->PRO_UID;
        $dynFields = array ();

        if (isset($httpData->loadField) && $httpData->loadField) {
            unset($_SESSION['_cache_pmtables']);
        }

        $httpData->textFilter = (isset($httpData->textFilter))? $httpData->textFilter : null;

        if (isset( $httpData->TYPE ) && $httpData->TYPE == 'GRID') {
            if (isset( $httpData->GRID_UID )) {
                list($gridId, $dynaFormUid) = explode('-', $httpData->GRID_UID);

                $this->dynUid = $dynaFormUid;
                $this->gridId = $gridId;

                $dynFields = $this->_getDynafields($aFields['PRO_UID'], 'grid', $httpData->start, $httpData->limit, $httpData->textFilter);
            } else {
                $gridFields = $this->_getGridFields($aFields['PRO_UID']);

                foreach ($gridFields as $value) {
                    $dynFields[] = [
                        'FIELD_UID'  => $value['gridId'] . '-' . $value['uid'],
                        'FIELD_NAME' => $value['gridName']
                    ];
                }
            }
        } else {
            // normal dynaform
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
        try {
            $reportTable = new \ProcessMaker\BusinessModel\ReportTable();

            return $reportTable->saveStructureOfTable((array)($httpData), $alterTable);
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * delete pm table
     *
     * @param string $httpData->rows
     */
    public function delete ($httpData)
    {
        $result = new stdClass();
        $rows = G::json_decode( stripslashes( $httpData->rows ) );
        $errors = '';
        $count = 0;
        $result = new StdClass();

        $tableCasesList = array();
        $conf = new Configurations();
        $confCasesListDraft = $conf->getConfiguration( 'casesList', 'draft');
        $confCasesListPaused = $conf->getConfiguration( 'casesList', 'paused');
        $confCasesListSent = $conf->getConfiguration( 'casesList', 'sent');
        $confCasesListTodo = $conf->getConfiguration( 'casesList', 'todo');
        $confCasesListUnassigned = $conf->getConfiguration( 'casesList', 'unassigned');
        $tableCasesList['draft'] = ($confCasesListDraft != null) ? (isset($confCasesListDraft['PMTable']) ? $confCasesListDraft['PMTable'] : '') : '';
        $tableCasesList['paused'] = ($confCasesListPaused != null) ? (isset($confCasesListPaused['PMTable']) ? $confCasesListPaused['PMTable'] : '') : '';
        $tableCasesList['sent'] = ($confCasesListSent != null) ? (isset($confCasesListSent['PMTable']) ? $confCasesListSent['PMTable'] : '') : '';
        $tableCasesList['todo'] = ($confCasesListTodo != null) ? (isset($confCasesListTodo['PMTable']) ? $confCasesListTodo['PMTable'] : '') : '';
        $tableCasesList['unassigned'] = ($confCasesListUnassigned != null) ? (isset($confCasesListUnassigned['PMTable']) ? $confCasesListUnassigned['PMTable'] : '') : '';

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

                foreach ($tableCasesList as $action => $idTable) {
                    if ($idTable == $row->id) {
                        $conf = new Configurations();
                        $resultJson = $conf->casesListDefaultFieldsAndConfig($action);
                        $conf->saveObject($resultJson, "casesList", $action, "", "", "");
                    }
                }

                if ($row->type == 'CLASSIC') {
                    $rp = new ReportTables();
                    $rp->deleteReportTable( $row->id );
                    $count ++;
                } else {
                    $at->deleteAll( $row->id );
                    $count ++;
                }

                $oCriteria = new Criteria('workflow');
                $oCriteria->add(CaseConsolidatedCorePeer::REP_TAB_UID, $row->id);
                $oResult = CaseConsolidatedCorePeer::doSelectOne($oCriteria);
                if(!empty($oResult)) {
                    $sTasUid = $oResult->getTasUid();
                    $oCaseConsolidated = new CaseConsolidatedCore();
                    $oCaseConsolidated = CaseConsolidatedCorePeer::retrieveByPK($sTasUid);
                    $oCaseConsolidated->delete();
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
            G::auditLog("DeletePmtable", "Table Name: ". $table['ADD_TAB_NAME']." Table ID: (".$table['ADD_TAB_UID'].") ");
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
     * @param string $httpData->appUid
     */
    public function dataView ($httpData)
    {
        $co = new Configurations();
        $config = $co->getConfiguration( 'additionalTablesData', 'pageSize', '', $_SESSION['USER_LOGGED'] );
        $limit_size = isset( $config['pageSize'] ) ? $config['pageSize'] : 20;
        $start = isset( $httpData->start ) ? $httpData->start : 0;
        $limit = isset( $httpData->limit ) ? $httpData->limit : $limit_size;
        $appUid = isset( $httpData->appUid ) ? $httpData->appUid : false;
        $appUid = ($appUid == "true") ? true : false;
        $filter = isset( $httpData->textFilter ) ? $httpData->textFilter : '';
        $additionalTables = new AdditionalTables();
        $table = $additionalTables->load( $httpData->id, true );

        if ($filter != '') {
            $result = $additionalTables->getAllData( $httpData->id, $start, $limit, true, $filter, $appUid);
        } else {
            $result = $additionalTables->getAllData( $httpData->id, $start, $limit );
        }

        $primaryKeys = $additionalTables->getPrimaryKeys();

        if (is_array($result['rows'])) {
            foreach ($result['rows'] as $i => $row) {
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
     * create pm tables record
     *
     * @param string $httpData->rows
     */
    public function dataCreate ($httpData, $codification = 'json')
    {
        $result = new stdClass();

        try {
            $reportTable = new \ProcessMaker\BusinessModel\ReportTable();

            $arrayResult = $reportTable->createRecord((array)($httpData), $codification);

            if ($arrayResult['success']) {
                $result->success = true;
                $result->message = $arrayResult['message'];
                $result->rows    = $arrayResult['rows'];
                $result->rows['__index__'] = $arrayResult['index'];
            } else {
                $result->success = false;
                $result->message = '$$';
                $result->rows = [];
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

        $rows = G::json_decode( $httpData->rows );

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

        if ($result) {
            G::auditLog("UpdateDataPmtable", "Table Name: ".$table['ADD_TAB_NAME']." Table ID: (".$table['ADD_TAB_UID'].") ");
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

        G::auditLog("DeleteDataPmtable", "Table Name: ".$table['ADD_TAB_NAME']." Table ID: (".$table['ADD_TAB_UID'].") ");

        $this->success = $this->_dataDestroy( $httpData->rows );
        $this->message = $this->success ? G::loadTranslation( 'ID_DELETED_SUCCESSFULLY' ) : G::loadTranslation( 'ID_DELETE_FAILED' );
    }

    /**
     * Import pmTable from CSV file
     * @param $httpData
     */
    public function importCSV($httpData)
    {
        $filter = new InputFilter();
        $countRow = 250;
        $tmpfilename = $_FILES['form']['tmp_name']['CSV_FILE'];
        if (preg_match('/[\x00-\x08\x0b-\x0c\x0e\x1f]/', file_get_contents($tmpfilename)) === 0) {
            $filename = $_FILES['form']['name']['CSV_FILE'];
            if ($oFile = fopen($filter->xssFilterHard($tmpfilename, 'path'), 'r')) {
                require_once 'classes/model/AdditionalTables.php';
                $oAdditionalTables = new AdditionalTables();
                $aAdditionalTables = $oAdditionalTables->load($_POST['form']['ADD_TAB_UID'], true);
                $sErrorMessages = '';
                $i = 1;
                $conData = 0;
                $insert = 'REPLACE INTO ' . $aAdditionalTables['ADD_TAB_NAME'] . ' (';
                $query = '';
                $swHead = false;
                while (($aAux = fgetcsv($oFile, 4096, $_POST['form']['CSV_DELIMITER'], '"', '"')) !== false) {
                    if (!is_null($aAux[0])) {
                        if (count($aAdditionalTables['FIELDS']) > count($aAux)) {
                            $this->success = false;
                            $this->message = G::LoadTranslation('INVALID_FILE');
                            return 0;
                        }
                        if ($i == 1) {
                            $j = 0;
                            foreach ($aAdditionalTables['FIELDS'] as $aField) {
                                $insert .= $aField['FLD_NAME'] . ', ';
                                if ($aField['FLD_NAME'] === $aAux[$j]) {
                                    $swHead = true;
                                }
                                $j++;
                            }
                            $insert = substr($insert, 0, -2);
                            $insert .= ') VALUES ';
                        }

                        if ($swHead == false) {
                            $queryRow = '(';
                            $j = 0;
                            foreach ($aAdditionalTables['FIELDS'] as $aField) {
                                $conData++;
                                if (array_key_exists($j, $aAux)) {
                                    $temp = '"' . addslashes(G::is_utf8($aAux[$j]) ? $aAux[$j] : utf8_encode($aAux[$j])) . '"';
                                } else {
                                    $temp = '""';
                                }
                                if ($temp == '') {
                                    switch ($aField['FLD_TYPE']) {
                                        case 'DATE':
                                        case 'TIMESTAMP':
                                            $temp = 'NULL';
                                            break;
                                    }
                                }
                                $j++;
                                $queryRow .= $temp . ',';
                            }
                            $query .= substr($queryRow, 0, -1) . '),';
                            try {
                                if ($conData == $countRow) {
                                    $query = substr($query, 0, -1);
                                    executeQuery($insert . $query . ';', $aAdditionalTables['DBS_UID']);
                                    $query = '';
                                    $conData = 0;
                                }
                            } catch (Exception $oError) {
                                $sErrorMessages .= G::LoadTranslation('ID_ERROR_INSERT_LINE') . ': ' . G::LoadTranslation('ID_LINE') . ' ' . $i . '. ';
                            }
                        } else {
                            $swHead = false;
                        }
                        $i++;
                    }
                }
                fclose($oFile);
                if ($conData > 0) {
                    $query = substr($query, 0, -1);
                    executeQuery($insert . $query . ';', $aAdditionalTables['DBS_UID']);
                }
            }
            if ($sErrorMessages != '') {
                $this->success = false;
                $this->message = $sErrorMessages;
            } else {
                $this->success = true;
                $this->message = G::loadTranslation('ID_FILE_IMPORTED_SUCCESSFULLY', array($filename
                ));
                G::auditLog("ImportTable", $filename);
            }
        } else {
            $sMessage = G::LoadTranslation('ID_UPLOAD_VALID_CSV_FILE');
            $this->success = false;
            $this->message = $sMessage;
        }
    }

    /**
     * import a CSV to pm tables record
     *
     * @param string $httpData->id
     */
    public function importCSVDeprecated ($httpData)
    {

        $filter = new InputFilter();
        $tmpfilename = $_FILES['form']['tmp_name']['CSV_FILE'];
        //$tmpfilename = $filter->xssFilterHard($tmpfilename, 'path');
        if (preg_match( '/[\x00-\x08\x0b-\x0c\x0e\x1f]/', file_get_contents( $tmpfilename ) ) === 0) {
            $filename = $_FILES['form']['name']['CSV_FILE'];
            $filename = $filter->xssFilterHard($filename, 'path');
            if ($oFile = fopen( $filter->xssFilterHard($tmpfilename, 'path'), 'r' )) {
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
                G::auditLog("ImportTable", $filename);
            }
        } else {
            $sMessage = G::LoadTranslation( 'ID_UPLOAD_VALID_CSV_FILE' );
            $this->success = false;
            $this->message = $sMessage;
        }
    }

    /**
     * Export pmTable to CSV format
     * @param $httpData
     * @return StdClass
     */
    public function exportCSV($httpData)
    {
        $result = new StdClass();
        try {

            $link = '';
            $size = '';
            $META = 'Content';
            $bytesSaved = 0;

            require_once 'classes/model/AdditionalTables.php';
            $oAdditionalTables = new AdditionalTables();
            $aAdditionalTables = $oAdditionalTables->load($_POST['ADD_TAB_UID'], true);
            $sErrorMessages = '';
            $sDelimiter = $_POST['CSV_DELIMITER'];

            $resultData = $oAdditionalTables->getAllData($_POST['ADD_TAB_UID'], null, null, false);
            $rows = $resultData['rows'];
            $count = $resultData['count'];

            $PUBLIC_ROOT_PATH = PATH_DATA . 'sites' . PATH_SEP . SYS_SYS . PATH_SEP . 'public' . PATH_SEP;
            $filenameOnly = strtolower($aAdditionalTables['ADD_TAB_NAME'] . "_" . date("Y-m-d") . '_' . date("Hi") . ".csv");
            $filename = $PUBLIC_ROOT_PATH . $filenameOnly;
            $fp = fopen($filename, "wb");
            $swColumns = true;
            foreach ($rows as $keyCol => $cols) {
                if ($swColumns) {
                    fputcsv($fp, array_keys($cols), $sDelimiter, '"', "\\");
                    $swColumns = false;
                }
                fputcsv($fp, $cols, $sDelimiter, '"');
            }

            fclose($fp);
            $filenameLink = "streamExported?f=$filenameOnly";
            $size = filesize($filename);
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
        define('ERROR_PM_TABLES_OVERWRITE', 1);
        define('ERROR_PROCESS_NOT_EXIST', 2);
        define('ERROR_RP_TABLES_OVERWRITE', 3);
        define('ERROR_NO_REPORT_TABLE', 4);
        define('ERROR_OVERWRITE_RELATED_PROCESS', 5);

        $fromAdmin = false;
        if (isset( $_POST["form"]["TYPE_TABLE"] ) && ! empty( $_POST["form"]["TYPE_TABLE"] )) {
            if($_POST["form"]["TYPE_TABLE"] == 'admin') {
                $fromAdmin = true;
            }
        }

        try {
            $result = new stdClass();
            $errors = '';
            $fromConfirm = false;

            $overWrite = isset( $_POST['form']['OVERWRITE'] ) ? true : false;

            if (isset( $_POST["form"]["FROM_CONFIRM"] ) && ! empty( $_POST["form"]["FROM_CONFIRM"] )) {
                $fromConfirm = $_POST["form"]["FROM_CONFIRM"];
                $_FILES['form'] = $_SESSION['FILES_FORM'];
            }

            //save the file
            if ($_FILES['form']['error']['FILENAME'] !== 0) {
                throw new Exception( G::loadTranslation( 'ID_PMTABLE_UPLOADING_FILE_PROBLEM' ) );
            }
            $_SESSION['FILES_FORM'] = $_FILES['form'];


            $PUBLIC_ROOT_PATH = PATH_DATA . 'sites' . PATH_SEP . SYS_SYS . PATH_SEP . 'public' . PATH_SEP;
            $filename = $_FILES['form']['name']['FILENAME'];
            $tempName = $_FILES['form']['tmp_name']['FILENAME'];

            if(!$fromConfirm) {
                G::uploadFile( $tempName, $PUBLIC_ROOT_PATH, $filename );
            }

            if ($fromConfirm == 'clear') {
                $fromConfirm = true;
            }

            $fileContent = file_get_contents( $PUBLIC_ROOT_PATH . $filename );

            if (strpos( $fileContent, '-----== ProcessMaker Open Source Private Tables ==-----' ) === false) {
                $result->success = false;
                $result->errorType = 'notice';
                $result->message = G::loadTranslation( 'ID_PMTABLE_INVALID_FILE', array ($filename));
                return $result;
            }

            $currentProUid = '';
            if (isset( $_POST["form"]["PRO_UID_HELP"] ) && !empty($_POST["form"]["PRO_UID_HELP"])) {
                $currentProUid = $_POST["form"]["PRO_UID_HELP"];
            } else {
                if(isset( $_POST["form"]["PRO_UID"]) && !empty( $_POST["form"]["PRO_UID"])){
                    $currentProUid = $_POST["form"]["PRO_UID"];
                    $_SESSION['PROCESS'] = $currentProUid;
                } else{
                    $currentProUid = $_SESSION['PROCESS'];
                }
            }

            //Get Additional Tables
            $arrayTableSchema = [];
            $arrayTableData = [];

            $f = fopen($PUBLIC_ROOT_PATH . $filename, 'rb');

            $fdata = intval(fread($f, 9));
            $type = fread($f, $fdata);

            while (!feof($f)) {
                switch ($type) {
                    case '@META':
                        $fdata = intval(fread($f, 9));
                        $metadata = fread($f, $fdata);
                        break;
                    case '@SCHEMA':
                        $fdataUid = intval(fread($f, 9));
                        $uid = fread($f, $fdataUid );

                        $fdata = intval(fread($f, 9));
                        $schema = fread($f, $fdata);

                        $arrayTableSchema[] = unserialize($schema);
                        break;
                    case '@DATA':
                        $fdata = intval(fread($f, 9));
                        $tableName = fread($f, $fdata);

                        $fdata = intval(fread($f, 9));

                        if ($fdata > 0) {
                            $data = fread($f, $fdata);

                            $arrayTableData[$tableName] = unserialize($data);
                        }
                        break;
                }

                $fdata = intval(fread($f, 9));

                if ($fdata > 0) {
                    $type = fread($f, $fdata);
                } else {
                    break;
                }
            }

            fclose($f);

            //First Validate the file
            $reportTable = new \ProcessMaker\BusinessModel\ReportTable();

            $arrayOverwrite = array();
            $arrayRelated = array();
            $arrayMessage = array();
            $validationType = 0;
            if(!$fromConfirm){
                $aErrors = $reportTable->checkPmtFileThrowErrors(
                    $arrayTableSchema, $currentProUid, $fromAdmin, $overWrite, $_POST['form']['PRO_UID']
                );
                $countC = 0;
                $countM = 0;
                $countI = 0;
                foreach($aErrors as $row){
                    if($row['ERROR_TYPE'] == ERROR_PM_TABLES_OVERWRITE || $row['ERROR_TYPE'] == ERROR_RP_TABLES_OVERWRITE){
                        $arrayOverwrite[$countC] = $row;
                        $countC++;
                    } else {
                        if($row['ERROR_TYPE'] == ERROR_OVERWRITE_RELATED_PROCESS){
                            $arrayRelated[$countI] = $row;
                            $countI++;
                        } else {
                            $arrayMessage[$countM] = $row;
                            $countM++;
                        }
                    }
                }
                if(sizeof($aErrors)){
                   $validationType = 1; //Yes no
                   throw new Exception(G::loadTranslation( 'ID_PMTABLE_IMPORT_WITH_ERRORS', array ($filename)));
                }
            }
            //Then create the tables
            if(isset($_POST["form"]["TABLES_OF_NO"])){
                $arrayOfNo = $_POST["form"]["TABLES_OF_NO"];
                $arrayOfNew = $_POST["form"]["TABLES_OF_NEW"];
                $aTablesCreateNew = explode('|',$arrayOfNew);
                $aTablesNoCreate = explode('|',$arrayOfNo);
                $errors = $reportTable->createStructureOfTables(
                    $arrayTableSchema,
                    $arrayTableData,
                    $currentProUid,
                    $fromAdmin,
                    true,
                    $aTablesNoCreate,
                    $aTablesCreateNew
                );
            } else {
                $errors = $reportTable->createStructureOfTables(
                    $arrayTableSchema,
                    $arrayTableData,
                    $currentProUid,
                    $fromAdmin,
                    true
                );
            }

            if ($errors == '') {
                $result->success = true;
                $msg = G::loadTranslation( 'ID_DONE' );
            } else {
                $result->success = false;
                $result->errorType = 'warning';
                $msg = G::loadTranslation( 'ID_PMTABLE_IMPORT_WITH_ERRORS', array ($filename) ) . "\n\n" . $errors;
            }

            $result->message = $msg;
        } catch (Exception $e) {
            $result = new stdClass();
            $result->fromAdmin = $fromAdmin;
            $result->arrayMessage = $arrayMessage;
            $result->arrayRelated = $arrayRelated;
            $result->arrayOverwrite = $arrayOverwrite;
            $result->validationType = $validationType;
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
            $result = new stdClass();
            $net = new Net( G::getIpAddress() );

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
                G::auditLog("ExportTable", $table->ADD_TAB_NAME." (".$table->ADD_TAB_UID.") ");
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
            $result = new stdClass();
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
        $result = new stdClass();

        $result->message = '';
        $result->success = true;

        $additionalTables = new AdditionalTables();
        $table = $additionalTables->load( $httpData->id );
        if ($table['PRO_UID'] != '') {
            $additionalTables->populateReportTable( $table['ADD_TAB_NAME'], PmTable::resolveDbSource( $table['DBS_UID'] ), $table['ADD_TAB_TYPE'], $table['PRO_UID'], $table['ADD_TAB_GRID'], $table['ADD_TAB_UID'] );
            $result->message = 'generated for table ' . $table['ADD_TAB_NAME'];
        }

        return $result;
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

            $excludeFieldsList = array ('multipleFile','title','subtitle','link','file','button','reset','submit','listbox','checkgroup','grid','javascript','location','scannerCode','array'
            );

            $labelFieldsTypeList = array ('dropdown','radiogroup');

            $index = 0;

            while ($aRow = $oDataset->getRow()) {
                if (file_exists( PATH_DYNAFORM . PATH_SEP . $aRow['DYN_FILENAME'] . '.xml' )) {
                    $dynaformHandler = new DynaformHandler( PATH_DYNAFORM . $aRow['DYN_FILENAME'] . '.xml' );
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

            // getting bpmn projects
            $bpmn = new \ProcessMaker\Project\Bpmn();

            if ($bpmn->exists($proUid)) {
                switch ($type) {
                    case 'xmlform':
                        $arrayDataTypeToExclude = ['array', 'grid'];
                        $arrayTypeToExclude = ['multipleFile', 'title', 'subtitle', 'link', 'file', 'button', 'reset', 'submit', 'listbox', 'grid', 'array', 'javascript', 'location', 'scannerCode'];

                        $arrayControlSupported = [];

                        $dynaformAllControl = $this->getDynaformVariables($proUid, $arrayTypeToExclude, true, 'DATA');

                        foreach ($dynaformAllControl as $value) {
                            $arrayControl = array_change_key_case($value, CASE_UPPER);

                            if (isset($arrayControl['DATATYPE']) && isset($arrayControl['TYPE'])) {
                                if (!in_array($arrayControl['DATATYPE'], $arrayDataTypeToExclude) &&
                                    !in_array($arrayControl['TYPE'], $arrayTypeToExclude)
                                ) {
                                    $arrayControlSupported[$arrayControl['VAR_UID']] = $arrayControl['TYPE'];
                                }
                            }
                        }

                        $dynaformNotAllowedVariables = $this->getDynaformVariables($proUid, $arrayTypeToExclude, false);

                        $criteria = new Criteria('workflow');

                        $criteria->addSelectColumn(ProcessVariablesPeer::VAR_UID);
                        $criteria->addSelectColumn(ProcessVariablesPeer::VAR_NAME);
                        $criteria->addSelectColumn(ProcessVariablesPeer::VAR_FIELD_TYPE);
                        $criteria->add(ProcessVariablesPeer::PRJ_UID, $proUid, Criteria::EQUAL);

                        $rsCriteria = ProcessVariablesPeer::doSelectRS($criteria);
                        $rsCriteria->setFetchmode(ResultSet::FETCHMODE_ASSOC);

                        $index = 0;

                        while ($rsCriteria->next()) {
                            $record = $rsCriteria->getRow();

                            if (!in_array($record['VAR_NAME'], $dynaformNotAllowedVariables) &&
                                !in_array($record['VAR_FIELD_TYPE'], $arrayTypeToExclude) &&
                                !in_array($record['VAR_NAME'], $fieldsNames)
                            ) {
                                $fields[] = [
                                    'FIELD_UID'  => $record['VAR_NAME'] . '-' . $record['VAR_FIELD_TYPE'],
                                    'FIELD_NAME' => $record['VAR_NAME'],
                                    'FIELD_VALIDATE' => 'any',
                                    '_index' => $index++,
                                    '_isset' => true
                                ];

                                $fieldsNames[] = $record['VAR_NAME'];
                            }

                            if (isset($arrayControlSupported[$record['VAR_UID']]) &&
                                !in_array($record['VAR_NAME'] . '_label', $fieldsNames)
                            ) {
                                $fields[] = [
                                    'FIELD_UID'  => $record['VAR_NAME'] . '_label' . '-' . $arrayControlSupported[$record['VAR_UID']],
                                    'FIELD_NAME' => $record['VAR_NAME'] . '_label',
                                    'FIELD_VALIDATE' => 'any',
                                    '_index' => $index++,
                                    '_isset' => true
                                ];

                                $fieldsNames[] = $record['VAR_NAME'] . '_label';
                            }
                        }
                        break;
                    case 'grid':
                        $dynaForm = new \ProcessMaker\BusinessModel\DynaForm();

                        $dynaFormUid = $this->dynUid;
                        $gridId = $this->gridId;

                        $arrayDynaFormData = $dynaForm->getDynaFormRecordByPk($dynaFormUid, [], false);

                        if ($arrayDynaFormData !== false) {
                            $arrayGrid = PmDynaform::getGridsAndFields($arrayDynaFormData['DYN_CONTENT']);

                            if ($arrayGrid !== false && isset($arrayGrid[$gridId])) {
                                $grid = $arrayGrid[$gridId];

                                $arrayValidTypes = [
                                    'text'     => ['type' => 'text',     'label' => false],
                                    'textarea' => ['type' => 'textarea', 'label' => false],
                                    'dropdown' => ['type' => 'dropdown', 'label' => true],
                                    'checkbox' => ['type' => 'checkbox', 'label' => false],
                                    'datetime' => ['type' => 'date',     'label' => false],
                                    'suggest'  => ['type' => 'suggest',  'label' => false],
                                    'hidden'   => ['type' => 'hidden',   'label' => false]
                                ];

                                $index = 0;

                                foreach ($grid->columns as $value) {
                                    $field = $value;

                                    if (isset($field->type) && isset($arrayValidTypes[$field->type]) &&
                                        isset($field->id) && $field->id != '' && isset($field->name) && $field->name != ''
                                    ) {
                                        if (!in_array($field->id, $fieldsNames)) {
                                            $fields[] = [
                                                'FIELD_UID'  => $field->id . '-' . $arrayValidTypes[$field->type]['type'],
                                                'FIELD_NAME' => $field->id,
                                                'FIELD_VALIDATE' => 'any',
                                                '_index' => $index++,
                                                '_isset' => true
                                            ];

                                            $fieldsNames[] = $field->id;
                                        }

                                        if ($arrayValidTypes[$field->type]['label'] &&
                                            !in_array($field->id . '_label', $fieldsNames)
                                        ) {
                                            $fields[] = [
                                                'FIELD_UID'  => $field->id . '_label' . '-' . $arrayValidTypes[$field->type]['type'],
                                                'FIELD_NAME' => $field->id . '_label',
                                                'FIELD_VALIDATE' => 'any',
                                                '_index' => $index++,
                                                '_isset' => true
                                            ];

                                            $fieldsNames[] = $field->id . '_label';
                                        }
                                    }
                                }
                            }
                        }
                        break;
                }
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
        try {

            $bpmn = new \ProcessMaker\Project\Bpmn();
            $flagIsBpmn = $bpmn->exists($proUid);

            $arrayField = [];
            $arrayFieldName = [];

            $delimiter = DBAdapter::getStringDelimiter();

            $criteria = new Criteria('workflow');

            $criteria->addSelectColumn(DynaformPeer::DYN_UID);
            $criteria->addSelectColumn(DynaformPeer::DYN_FILENAME);
            $criteria->addSelectColumn(DynaformPeer::DYN_CONTENT);
            $criteria->addSelectColumn(DynaformPeer::DYN_TITLE);
            $criteria->add(DynaformPeer::PRO_UID, $proUid, Criteria::EQUAL);
            $criteria->add(DynaformPeer::DYN_TYPE, 'xmlform', Criteria::EQUAL);

            $rsCriteria = DynaformPeer::doSelectRS($criteria);
            $rsCriteria->setFetchmode(ResultSet::FETCHMODE_ASSOC);

            while ($rsCriteria->next()) {
                $record = $rsCriteria->getRow();

                if ($flagIsBpmn) {
                    $arrayGrid = PmDynaform::getGridsAndFields($record['DYN_CONTENT']);

                    if ($arrayGrid !== false) {
                        foreach ($arrayGrid as $value) {
                            $grid = $value;

                            $arrayField[] = [
                                'uid'      => $record['DYN_UID'], //dynaFormUid
                                'gridId'   => $grid->id,
                                'gridName' => $grid->id . ' (' . $record['DYN_TITLE'] . ')'
                            ];
                        }
                    }
                } else {
                    $dynaformHandler = new DynaformHandler(PATH_DYNAFORM . $record['DYN_FILENAME'] . '.xml');
                    $nodeFieldsList = $dynaformHandler->getFields();

                    foreach ($nodeFieldsList as $node) {
                        $arrayNode = $dynaformHandler->getArray($node);
                        $fieldName = $arrayNode['__nodeName__'];
                        $fieldType = $arrayNode['type'];

                        if ($fieldType == 'grid') {
                            if (!in_array($fieldName, $arrayFieldName)) {
                                $arrayField[] = [
                                    'uid'      => str_replace($proUid . '/', '', $arrayNode['xmlgrid']), //dynaFormUid (Grid)
                                    'gridId'   => $fieldName,
                                    'gridName' => $fieldName
                                ];

                                $arrayFieldName[] = $fieldName;
                            }
                        }
                    }
                }
            }

            //Return
            return $arrayField;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Get all dynaform variables
     *
     * @param $sProcessUID
     */
    public function getDynaformVariables($sProcessUID, $excludeFieldsList, $allowed = true, $option = "VARIABLE")
    {
        $dynaformVariables = array();
        $oC = new Criteria( 'workflow' );
        $oC->addSelectColumn( DynaformPeer::DYN_CONTENT );
        $oC->add( DynaformPeer::PRO_UID, $sProcessUID );
        $oData = DynaformPeer::doSelectRS( $oC );
        $oData->setFetchmode( ResultSet::FETCHMODE_ASSOC );
        $oData->next();
        while ($aRowd = $oData->getRow()) {
            $dynaform = G::json_decode($aRowd['DYN_CONTENT'],true);
            if(is_array($dynaform) && sizeof($dynaform)) {
                $items = $dynaform['items'][0]['items'];
                foreach($items as $key => $val){
                    foreach($val as $column) {
                        if($allowed) {
                            if(isset($column['type']) && !in_array( $column['type'], $excludeFieldsList )){
                                switch ($option) {
                                    case "VARIABLE":
                                        if (array_key_exists("variable", $column)) {
                                            if($column["variable"] != "") {
                                                $dynaformVariables[] = $column["variable"];
                                            }
                                        }
                                        break;
                                    case "DATA":
                                        $dynaformVariables[] = $column;
                                        break;
                                }
                            }
                        } else {
                            if(isset($column['type']) && in_array( $column['type'], $excludeFieldsList )){
                                switch ($option) {
                                    case "VARIABLE":
                                        if (array_key_exists("variable", $column)) {
                                            if($column["variable"] != "") {
                                                $dynaformVariables[] = $column["variable"];
                                            }
                                        }
                                        break;
                                    case "DATA":
                                        $dynaformVariables[] = $column;
                                        break;
                                }
                            }
                        }
                    }
                }
            }
            $oData->next();
        }

        if ($option == "VARIABLE") {
            return array_unique($dynaformVariables);
        } else {
            return $dynaformVariables;
        }
    }
}
