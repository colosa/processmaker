<?php
G::LoadClass("webResource");
G::LoadClass("pmFunctions");

class AdditionalTablesConsolidated extends AdditionalTables
{
    public function createPropelClasses($sTableName, $sClassName, $aFields, $sAddTabUid)
    {
        try {
            $aTypes = array('VARCHAR' => 'string',
                            'TEXT'    => 'string',
                            'DATE'    => 'int',
                            'INT'     => 'int',
                            'FLOAT'   => 'double');
            $aCreoleTypes = array('VARCHAR' => 'VARCHAR',
                                 'TEXT'    => 'LONGVARCHAR',
                                 'DATE'    => 'TIMESTAMP',
                                 'INT'     => 'INTEGER',
                                 'FLOAT'   => 'DOUBLE');
            if ($sClassName == '') {
                $sClassName = $this->getPHPName($sTableName);
            }

            $sPath = PATH_DB . SYS_SYS . PATH_SEP . 'classes' . PATH_SEP;
            if (!file_exists($sPath)) {
                G::mk_dir($sPath);
                G::mk_dir($sPath . 'map');
                G::mk_dir($sPath . 'om');
            }
            $aData = array();
            $aData['pathClasses']    = substr(PATH_DB, 0, -1);
            $aData['tableName']      = $sTableName;
            $aData['className']      = $sClassName;
            $aData['GUID']           = $sAddTabUid;
            $aData['firstColumn']    = $aFields[1]['FLD_NAME'];
            $aData['totalColumns']   = count($aFields);
            $aData['useIdGenerator'] = 'false';
            $oTP1  = new TemplatePower(PATH_TPL . 'additionalTables' . PATH_SEP . 'Table.tpl');
            $oTP1->prepare();
            $oTP1->assignGlobal($aData);
            file_put_contents($sPath . $sClassName . '.php', $oTP1->getOutputContent());
            $oTP2  = new TemplatePower(PATH_TPL . 'additionalTables' . PATH_SEP . 'TablePeer.tpl');
            $oTP2->prepare();
            $oTP2->assignGlobal($aData);
            file_put_contents($sPath . $sClassName . 'Peer.php', $oTP2->getOutputContent());
            $aColumns = array();
            $aPKs     = array();
            $aNotPKs  = array();
            $i        = 0;
            foreach ($aFields as $iKey => $aField) {
                $aColumn    = array('name'        => $aField['FLD_NAME'],
                                    'phpName'     => $this->getPHPName($aField['FLD_NAME']),
                                    'type'        => $aTypes[$aField['FLD_TYPE']],
                                    'creoleType'  => $aCreoleTypes[$aField['FLD_TYPE']],
                                    'notNull'     => ($aField['FLD_NULL'] == 'on' ? 'true' : 'false'),
                                    'size'        => (($aField['FLD_TYPE'] == 'VARCHAR') || ($aField['FLD_TYPE'] == 'INT') || ($aField['FLD_TYPE'] == 'FLOAT') ? $aField['FLD_SIZE'] : 'null'),
                                    'var'         => strtolower($aField['FLD_NAME']),
                                    'attribute'   => (($aField['FLD_TYPE'] == 'VARCHAR') || ($aField['FLD_TYPE'] == 'TEXT') || ($aField['FLD_TYPE'] == 'DATE') ? '$' . strtolower($aField['FLD_NAME']) . " = ''" : '$' . strtolower($aField['FLD_NAME']) . ' = 0'),
                                    'index'       => $i,
                                    );
                if ($aField['FLD_TYPE'] == 'DATE') {
                    $aColumn['getFunction'] = '
                    /**
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
                        if (($this->' . $aColumn['var'] . ' == "0000-00-00 00:00:00") || ($this->' . $aColumn['var'] . ' == "0000-00-00") || !$this->' . $aColumn['var'] . ') {
                          $ts = "0";
                        }
                        else {
                          $ts = strtotime($this->' . $aColumn['var'] . ');
                        }
                        if ($ts === -1 || $ts === false) { // in PHP 5.1 return value changes to FALSE
                          throw new PropelException("Unable to parse value of [' . $aColumn['var'] . '] as date/time value: " . var_export($this->' . $aColumn['var'] . ', true));
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
                    $aColumn['getFunction'] = '
                    /**
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
                        $aColumn['setFunction'] = '
                        // Since the native PHP type for this column is string,
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
                        $aColumn['setFunction'] = '
                        if ($v !== null && !is_int($v)) {
                            // if($v == \'\')
                            //   $ts = null;
                            // else
                              $ts = strtotime($v);
                            if ($ts === -1 || $ts === false) { // in PHP 5.1 return value changes to FALSE
                              //throw new PropelException("Unable to parse date/time value for [' . $aColumn['var'] . '] from input: " . var_export($v, true));
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
                        $aColumn['setFunction'] = '
                        // Since the native PHP type for this column is integer,
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
                        $aColumn['setFunction'] = '
                        if ($this->' . $aColumn['var'] . ' !== $v || $v === 0) {
                          $this->' . $aColumn['var'] . ' = $v;
                          $this->modifiedColumns[] = ' . $aData['className'] . 'Peer::' . $aColumn['name'] . ';
                        }';
                        break;
                }
                $aColumns[] = $aColumn;
                if ($aField['FLD_KEY'] == 'on') {
                    $aPKs[] = $aColumn;
                } else {
                    $aNotPKs[] = $aColumn;
                }
                if ($aField['FLD_AUTO_INCREMENT'] == 'on') {
                    $aData['useIdGenerator'] = 'true';
                }
                $i++;
            }

            $oTP3  = new TemplatePower(PATH_TPL . 'additionalTables' . PATH_SEP . 'map' . PATH_SEP . 'TableMapBuilder.tpl');
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
            file_put_contents($sPath . PATH_SEP . 'map' . PATH_SEP . $sClassName . 'MapBuilder.php', $oTP3->getOutputContent());
            $oTP4  = new TemplatePower(PATH_TPL . 'additionalTables' . PATH_SEP . 'om' . PATH_SEP . 'BaseTable.tpl');
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
                        $aData['getPrimaryKeyFunction'] .= '$pks[' . $iIndex . '] = $this->get' . $aColumn['phpName'] . '();' . "\n";
                        $aData['setPrimaryKeyFunction'] .= '$this->set' . $aColumn['phpName'] . '($keys[' . $iIndex . ']);' . "\n";
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
            file_put_contents($sPath . PATH_SEP . 'om' . PATH_SEP . 'Base' . $sClassName . '.php', $oTP4->getOutputContent());
            $oTP5  = new TemplatePower(PATH_TPL . 'additionalTables' . PATH_SEP . 'om' . PATH_SEP . 'BaseTablePeer.tpl');
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
            file_put_contents($sPath . PATH_SEP . 'om' . PATH_SEP . 'Base' . $sClassName . 'Peer.php', $oTP5->getOutputContent());
        } catch (Exception $oError) {
            throw($oError);
        }
    }
}

class ajax_con extends WebResource
{
    public function con_save_properties($sTasUid, $sDynUid, $sStatus, $sProUid, $sRepTabUid, $tableName, $title, $swOverwrite)
    {
        G::LoadClass("reportTables");

        if ($sStatus == "1" && $sDynUid != "") {
            //Verified as not to duplicate the name of the table

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

            $_POST['form']['PRO_UID'] = $sProUid;
            $_POST['form']['REP_TAB_UID']  = $sRepTabUid;
            $_POST['form']['REP_TAB_NAME'] = $tableName;
            $_POST['form']['REP_TAB_TYPE'] = "GRID";
            $_POST['form']['REP_TAB_GRID'] = $sProUid . "-" . $sDynUid;
            $_POST['form']['REP_TAB_CONNECTION'] = 'wf';
            $_POST['form']['REP_TAB_CREATE_DATE'] = date("Y-m-d H:i:s");
            $_POST['form']['REP_TAB_STATUS'] = 'ACTIVE';
            $_POST['form']['REP_TAB_TITLE'] = $title;

            $_POST['form']['FIELDS'] = array();

            G::LoadClass("reportTables");

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

            $_POST['form']['REP_TAB_TYPE'] = 'GRID';

            $oReportVar = new ReportVar();
            $oReportTables = new ReportTables();
            $oReportTables->deleteAllReportVars($_POST['form']['REP_TAB_UID']);

            $aFields = array();

            if ($_POST['form']['REP_TAB_TYPE'] == 'GRID') {
                $aAux = explode('-', $_POST['form']['REP_TAB_GRID']);
                global $G_FORM;

                G::LoadClass("formBatchRouting");
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

        $oCaseConsolidated = CaseConsolidatedCorePeer::retrieveByPK($sTasUid);

        if (!(is_object($oCaseConsolidated)) || get_class($oCaseConsolidated) != 'CaseConsolidated') {
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

        return ($sRepTabUid);
    }
}

$o = new ajax_con($_SERVER["REQUEST_URI"], $_POST);

