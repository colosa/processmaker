<?php
/**
 * class.reportTables.php
 *
 * @package workflow.engine.ProcessMaker
 *
 * ProcessMaker Open Source Edition
 * Copyright (C) 2004 - 2011 Colosa Inc.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 * For more information, contact Colosa Inc, 2566 Le Jeune Rd.,
 * Coral Gables, FL, 33134, USA, or email info@colosa.com.
 *
 */

G::LoadClass( 'case' );

/**
 * ReportTables - Report tables class
 *
 * @package workflow.engine.ProcessMaker
 * @author Julio Cesar Laura Avenda�o
 * @copyright 2007 COLOSA
 */

class ReportTables
{
    private $aDef = array ('mysql' => array ('number' => 'DOUBLE','char' => 'VARCHAR(255)','text' => 'TEXT','date' => 'DATETIME'
    ),'pgsql' => array ('number' => 'DOUBLE','char' => 'VARCHAR(255)','text' => 'TEXT','date' => 'DATETIME'
    ),'mssql' => array ('number' => 'FLOAT','char' => 'NVARCHAR(255)','text' => 'TEXT','date' => 'CHAR(19)'
    ) /* Changed DATETIME CHAR(19) for compatibility issues. */ );
    //private $sPrefix = 'REP_';
    private $sPrefix = '';

    /**
     * Function deleteAllReportVars
     * This function delete all reports
     *
     * @access public
     * @param string $$sRepTabUid
     * @return void
     */
    public function deleteAllReportVars ($sRepTabUid = '')
    {
        try {
            $oCriteria = new Criteria( 'workflow' );
            $oCriteria->add( ReportVarPeer::REP_TAB_UID, $sRepTabUid );
            ReportVarPeer::doDelete( $oCriteria );
        } catch (Exception $oError) {
            throw ($oError);
        }
    }

    /**
     * Function prepareQuery
     * This function removes the table
     *
     * @access public
     * @param string $sTableName Table name
     * @param string $sConnection Conexion
     * @return void
     */
    public function dropTable ($sTableName, $sConnection = 'report')
    {
        $sTableName = $this->sPrefix . $sTableName;
        //we have to do the propel connection
        $PropelDatabase = $this->chooseDB( $sConnection );
        $con = Propel::getConnection( $PropelDatabase );
        $stmt = $con->createStatement();
        try {
            switch (DB_ADAPTER) {
                case 'mysql':
                    $rs = $stmt->executeQuery( 'DROP TABLE IF EXISTS `' . $sTableName . '`' );
                    break;
                case 'mssql':
                    $rs = $stmt->executeQuery( "IF OBJECT_ID (N'" . $sTableName . "', N'U') IS NOT NULL
																		  DROP TABLE [" . $sTableName . "]" );
                    break;
            }
        } catch (Exception $oError) {
            throw ($oError);
        }
    }

    /**
     * Function createTable
     * This Function creates the table
     *
     * @access public
     * @param string $sTableName Table name
     * @param string $sConnection Connection name
     * @param string $sType
     * @param array $aFields
     * @param string $bDefaultFields
     * @return void
     */
    public function createTable ($sTableName, $sConnection = 'report', $sType = 'NORMAL', $aFields = array(), $bDefaultFields = true)
    {
        $sTableName = $this->sPrefix . $sTableName;
        //we have to do the propel connection
        $PropelDatabase = $this->chooseDB( $sConnection );
        $con = Propel::getConnection( $PropelDatabase );
        $stmt = $con->createStatement();
        try {
            switch (DB_ADAPTER) {
                case 'mysql':
                    $sQuery = 'CREATE TABLE IF NOT EXISTS `' . $sTableName . '` (';
                    if ($bDefaultFields) {
                        $sQuery .= "`APP_UID` VARCHAR(32) NOT NULL DEFAULT '',`APP_NUMBER` INT NOT NULL,";
                        if ($sType == 'GRID') {
                            $sQuery .= "`ROW` INT NOT NULL,";
                        }
                    }
                    foreach ($aFields as $aField) {
                        switch ($aField['sType']) {
                            case 'number':
                                $sQuery .= '`' . $aField['sFieldName'] . '` ' . $this->aDef['mysql'][$aField['sType']] . " NOT NULL DEFAULT '0',";
                                break;
                            case 'char':
                                $sQuery .= '`' . $aField['sFieldName'] . '` ' . $this->aDef['mysql'][$aField['sType']] . " NOT NULL DEFAULT '',";
                                break;
                            case 'text':
                                $sQuery .= '`' . $aField['sFieldName'] . '` ' . $this->aDef['mysql'][$aField['sType']] . " ,";
                                break;
                            case 'date':
                                $sQuery .= '`' . $aField['sFieldName'] . '` ' . $this->aDef['mysql'][$aField['sType']] . " NULL,";
                                break;
                        }
                    }
                    if ($bDefaultFields) {
                        $sQuery .= 'PRIMARY KEY (APP_UID' . ($sType == 'GRID' ? ',ROW' : '') . ')) ';
                    }
                    $sQuery .= ' DEFAULT CHARSET=utf8;';
                    $rs = $stmt->executeQuery( $sQuery );
                    break;
                case 'mssql':
                    $sQuery = 'CREATE TABLE [' . $sTableName . '] (';
                    if ($bDefaultFields) {
                        $sQuery .= "[APP_UID] VARCHAR(32) NOT NULL DEFAULT '', [APP_NUMBER] INT NOT NULL,";
                        if ($sType == 'GRID') {
                            $sQuery .= "[ROW] INT NOT NULL,";
                        }
                    }
                    foreach ($aFields as $aField) {
                        switch ($aField['sType']) {
                            case 'number':
                                $sQuery .= '[' . $aField['sFieldName'] . '] ' . $this->aDef['mssql'][$aField['sType']] . " NOT NULL DEFAULT '0',";
                                break;
                            case 'char':
                                $sQuery .= '[' . $aField['sFieldName'] . '] ' . $this->aDef['mssql'][$aField['sType']] . " NOT NULL DEFAULT '',";
                                break;
                            case 'text':
                                $sQuery .= '[' . $aField['sFieldName'] . '] ' . $this->aDef['mssql'][$aField['sType']] . " NOT NULL DEFAULT '',";
                                break;
                            case 'date':
                                $sQuery .= '[' . $aField['sFieldName'] . '] ' . $this->aDef['mssql'][$aField['sType']] . " NULL,";
                                break;
                        }
                    }
                    if ($bDefaultFields) {
                        $sQuery .= 'PRIMARY KEY (APP_UID' . ($sType == 'GRID' ? ',ROW' : '') . ')) ';
                    } else {
                        $sQuery .= ' ';
                    }

                    $rs = $stmt->executeQuery( $sQuery );
                    break;

            }
        } catch (Exception $oError) {
            throw ($oError);
        }
    }

    /**
     * Function populateTable
     * This Function fills the table
     *
     * @access public
     * @param string $sTableName Table name
     * @param string $sConnection Connection name
     * @param string $sType
     * @param array $aFields
     * @param string $sProcessUid
     * @param string $sGrid
     * @return void
     */
    public function populateTable ($sTableName, $sConnection = 'report', $sType = 'NORMAL', $aFields = array(), $sProcessUid = '', $sGrid = '')
    {
        $sTableName = $this->sPrefix . $sTableName;
        //we have to do the propel connection
        $PropelDatabase = $this->chooseDB( $sConnection );
        $con = Propel::getConnection( $PropelDatabase );
        $stmt = $con->createStatement();
        if ($sType == 'GRID') {
            $aAux = explode( '-', $sGrid );
            $sGrid = $aAux[0];
        }
        try {
            switch (DB_ADAPTER) {
                case 'mysql':
                    //select cases for this Process, ordered by APP_NUMBER
                    $oCriteria = new Criteria( 'workflow' );
                    $oCriteria->add( ApplicationPeer::PRO_UID, $sProcessUid );
                    $oCriteria->addAscendingOrderByColumn( ApplicationPeer::APP_NUMBER );
                    $oDataset = ApplicationPeer::doSelectRS( $oCriteria );
                    $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );
                    $oDataset->next();
                    while ($aRow = $oDataset->getRow()) {
                        $aData = unserialize( $aRow['APP_DATA'] );
                        //delete previous record from this report table ( previous records in case this is a grid )
                        $deleteSql = 'DELETE FROM `' . $sTableName . "` WHERE APP_UID = '" . $aRow['APP_UID'] . "'";
                        $rsDel = $stmt->executeQuery( $deleteSql );
                        if ($sType == 'NORMAL') {
                            $sQuery = 'INSERT INTO `' . $sTableName . '` (';
                            $sQuery .= '`APP_UID`,`APP_NUMBER`';
                            foreach ($aFields as $aField) {
                                $sQuery .= ',`' . $aField['sFieldName'] . '`';
                            }
                            $sQuery .= ") VALUES ('" . $aRow['APP_UID'] . "'," . (int) $aRow['APP_NUMBER'];
                            foreach ($aFields as $aField) {
                                switch ($aField['sType']) {
                                    case 'number':
                                        $sQuery .= ',' . (isset( $aData[$aField['sFieldName']] ) ? (float) str_replace( ',', '', $aData[$aField['sFieldName']] ) : '0');
                                        break;
                                    case 'char':
                                    case 'text':
                                        if (! isset( $aData[$aField['sFieldName']] )) {
                                            $aData[$aField['sFieldName']] = '';
                                        }
                                        $sQuery .= ",'" . (isset( $aData[$aField['sFieldName']] ) ? mysql_real_escape_string( $aData[$aField['sFieldName']] ) : '') . "'";
                                        break;
                                    case 'date':
                                        $value = (isset( $aData[$aField['sFieldName']] ) && trim( $aData[$aField['sFieldName']] )) != '' ? "'" . $aData[$aField['sFieldName']] . "'" : 'NULL';
                                        $sQuery .= "," . $value;
                                        break;
                                }
                            }
                            $sQuery .= ')';
                            $rs = $stmt->executeQuery( $sQuery );
                        } else {
                            if (isset( $aData[$sGrid] )) {
                                foreach ($aData[$sGrid] as $iRow => $aGridRow) {
                                    $sQuery = 'INSERT INTO `' . $sTableName . '` (';
                                    $sQuery .= '`APP_UID`,`APP_NUMBER`,`ROW`';
                                    foreach ($aFields as $aField) {
                                        $sQuery .= ',`' . $aField['sFieldName'] . '`';
                                    }
                                    $sQuery .= ") VALUES ('" . $aRow['APP_UID'] . "'," . (int) $aRow['APP_NUMBER'] . ',' . $iRow;
                                    foreach ($aFields as $aField) {
                                        switch ($aField['sType']) {
                                            case 'number':
                                                $sQuery .= ',' . (isset( $aGridRow[$aField['sFieldName']] ) ? (float) str_replace( ',', '', $aGridRow[$aField['sFieldName']] ) : '0');
                                                break;
                                            case 'char':
                                            case 'text':
                                                if (! isset( $aGridRow[$aField['sFieldName']] )) {
                                                    $aGridRow[$aField['sFieldName']] = '';
                                                }
                                                $sQuery .= ",'" . (isset( $aGridRow[$aField['sFieldName']] ) ? mysql_real_escape_string( $aGridRow[$aField['sFieldName']] ) : '') . "'";
                                                break;
                                            case 'date':
                                                $value = (isset( $aGridRow[$aField['sFieldName']] ) && trim( $aGridRow[$aField['sFieldName']] )) != '' ? "'" . $aGridRow[$aField['sFieldName']] . "'" : 'NULL';
                                                $sQuery .= "," . $value;
                                                break;
                                        }
                                    }
                                    $sQuery .= ')';
                                    $rs = $stmt->executeQuery( $sQuery );
                                }
                            }
                        }
                        $oDataset->next();
                    }
                    break;

                /**
                 * For SQLServer code
                 */
                case 'mssql':
                    $oCriteria = new Criteria( 'workflow' );
                    $oCriteria->add( ApplicationPeer::PRO_UID, $sProcessUid );
                    $oCriteria->addAscendingOrderByColumn( ApplicationPeer::APP_NUMBER );
                    $oDataset = ApplicationPeer::doSelectRS( $oCriteria );
                    $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );
                    $oDataset->next();
                    while ($aRow = $oDataset->getRow()) {
                        $aData = unserialize( $aRow['APP_DATA'] );
                        mysql_query( 'DELETE FROM [' . $sTableName . "] WHERE APP_UID = '" . $aRow['APP_UID'] . "'" );
                        if ($sType == 'NORMAL') {
                            $sQuery = 'INSERT INTO [' . $sTableName . '] (';
                            $sQuery .= '[APP_UID],[APP_NUMBER]';
                            foreach ($aFields as $aField) {
                                $sQuery .= ',[' . $aField['sFieldName'] . ']';
                            }
                            $sQuery .= ") VALUES ('" . $aRow['APP_UID'] . "'," . (int) $aRow['APP_NUMBER'];
                            foreach ($aFields as $aField) {
                                switch ($aField['sType']) {
                                    case 'number':
                                        $sQuery .= ',' . (isset( $aData[$aField['sFieldName']] ) ? (float) str_replace( ',', '', $aData[$aField['sFieldName']] ) : '0');
                                        break;
                                    case 'char':
                                    case 'text':
                                        if (! isset( $aData[$aField['sFieldName']] )) {
                                            $aData[$aField['sFieldName']] = '';
                                        }
                                        $sQuery .= ",'" . (isset( $aData[$aField['sFieldName']] ) ? mysql_real_escape_string( $aData[$aField['sFieldName']] ) : '') . "'";
                                        break;
                                    case 'date':
                                        $sQuery .= ",'" . (isset( $aData[$aField['sFieldName']] ) ? $aData[$aField['sFieldName']] : '') . "'";
                                        break;
                                }
                            }
                            $sQuery .= ')';
                            $rs = $stmt->executeQuery( $sQuery );
                        } else {
                            if (isset( $aData[$sGrid] )) {
                                foreach ($aData[$sGrid] as $iRow => $aGridRow) {
                                    $sQuery = 'INSERT INTO [' . $sTableName . '] (';
                                    $sQuery .= '`APP_UID`,`APP_NUMBER`,`ROW`';
                                    foreach ($aFields as $aField) {
                                        $sQuery .= ',[' . $aField['sFieldName'] . ']';
                                    }
                                    $sQuery .= ") VALUES ('" . $aRow['APP_UID'] . "'," . (int) $aRow['APP_NUMBER'] . ',' . $iRow;
                                    foreach ($aFields as $aField) {
                                        switch ($aField['sType']) {
                                            case 'number':
                                                $sQuery .= ',' . (isset( $aGridRow[$aField['sFieldName']] ) ? (float) str_replace( ',', '', $aGridRow[$aField['sFieldName']] ) : '0');
                                                break;
                                            case 'char':
                                            case 'text':
                                                if (! isset( $aGridRow[$aField['sFieldName']] )) {
                                                    $aGridRow[$aField['sFieldName']] = '';
                                                }
                                                $sQuery .= ",'" . (isset( $aGridRow[$aField['sFieldName']] ) ? mysql_real_escape_string( $aGridRow[$aField['sFieldName']] ) : '') . "'";
                                                break;
                                            case 'date':
                                                $sQuery .= ",'" . (isset( $aGridRow[$aField['sFieldName']] ) ? $aGridRow[$aField['sFieldName']] : '') . "'";
                                                break;
                                        }
                                    }
                                    $sQuery .= ')';
                                    $rs = $stmt->executeQuery( $sQuery );
                                }
                            }
                        }
                        $oDataset->next();
                    }
                    break;

            }
        } catch (Exception $oError) {
            throw ($oError);
        }
    }

    /**
     * Function getTableVars
     *
     * @access public
     * @param string $sRepTabUid
     * @param boolean $bWhitType
     * @return void
     */
    public function getTableVars ($sRepTabUid, $bWhitType = false)
    {
        try {
            $oCriteria = new Criteria( 'workflow' );
            $oCriteria->add( ReportVarPeer::REP_TAB_UID, $sRepTabUid );
            $oDataset = ReportVarPeer::doSelectRS( $oCriteria );
            $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );
            $oDataset->next();
            $aVars = array ();
            $aImportedVars = array (); //This array will help to control if the variable already exist
            while ($aRow = $oDataset->getRow()) {
                if ($bWhitType) {
                    if (! in_array( $aRow['REP_VAR_NAME'], $aImportedVars )) {
                        $aImportedVars[] = $aRow['REP_VAR_NAME'];
                        $aVars[] = array ('sFieldName' => $aRow['REP_VAR_NAME'],'sType' => $aRow['REP_VAR_TYPE']
                        );
                    }
                } else {
                    $aVars[] = $aRow['REP_VAR_NAME'];
                }
                $oDataset->next();
            }
            return $aVars;
        } catch (Exception $oError) {
            throw ($oError);
        }
    }

    /**
     * Function deleteReportTable
     * This Function deletes report table
     *
     * @access public
     * @param string $sRepTabUid
     * @return void
     */
    public function deleteReportTable ($sRepTabUid)
    {
        try {
            $oReportTable = new ReportTable();
            $aFields = $oReportTable->load( $sRepTabUid );
            if (! (empty( $aFields ))) {
                $this->dropTable( $aFields['REP_TAB_NAME'], $aFields['REP_TAB_CONNECTION'] );
                $oCriteria = new Criteria( 'workflow' );
                $oCriteria->add( ReportVarPeer::REP_TAB_UID, $sRepTabUid );
                $oDataset = ReportVarPeer::doDelete( $oCriteria );
                $oReportTable->remove( $sRepTabUid );
            }
        } catch (Exception $oError) {
            throw ($oError);
        }
    }

    /**
     * Function getSplitDate
     * This function gets the split date
     *
     * @access public
     * @param date $date
     * @param string $mask
     * @return array
     */
    public function getSplitDate ($date, $mask)
    {
        $sw1 = false;
        for ($i = 0; $i < 3; $i ++) {
            $item = substr( $mask, $i * 2, 1 );
            switch ($item) {
                case 'Y':
                    switch ($i) {
                        case 0:
                            $d1 = substr( $date, 0, 4 );
                            break;
                        case 1:
                            $d1 = substr( $date, 3, 4 );
                            break;
                        case 2:
                            $d1 = substr( $date, 6, 4 );
                            break;
                    }
                    $sw1 = true;
                    break;
                case 'y':
                    switch ($i) {
                        case 0:
                            $d1 = substr( $date, 0, 2 );
                            break;
                        case 1:
                            $d1 = substr( $date, 3, 2 );
                            break;
                        case 2:
                            $d1 = substr( $date, 6, 2 );
                            break;
                    }
                    break;
                case 'm':
                    switch ($i) {
                        case 0:
                            $d2 = substr( $date, 0, 2 );
                            break;
                        case 1:
                            $d2 = ($sw1) ? substr( $date, 5, 2 ) : substr( $date, 3, 2 );
                            break;
                        case 2:
                            $d2 = ($sw1) ? substr( $date, 8, 2 ) : substr( $date, 5, 2 );
                            break;
                    }
                    break;
                case 'd':
                    switch ($i) {
                        case 0:
                            $d3 = substr( $date, 0, 2 );
                            break;
                        case 1:
                            $d3 = ($sw1) ? substr( $date, 5, 2 ) : substr( $date, 3, 2 );
                            break;
                        case 2:
                            $d3 = ($sw1) ? substr( $date, 8, 2 ) : substr( $date, 5, 2 );
                            break;
                    }
                    break;
            }
        }
        return Array (isset( $d1 ) ? $d1 : '',isset( $d2 ) ? $d2 : '',isset( $d3 ) ? $d3 : ''
        );
    }

    /**
     * Function getFormatDate
     * This function returns the date formated
     *
     * @access public
     * @param date $sDate
     * @param date $sMask
     * @return date
     */
    public function getFormatDate ($sDate, $sMask)
    {
        //print $sDate." *** ". $sMask."<BR>";
        $dateTime = explode( " ", $sDate ); //To accept the Hour part
        $aDate = explode( '-', str_replace( "/", "-", $dateTime[0] ) );
        $bResult = true;
        foreach ($aDate as $sDate) {
            if (! is_numeric( $sDate )) {
                $bResult = false;
                break;
            }
        }
        if ($sMask != '') {
            $aDate = $this->getSplitDate( $dateTime[0], $sMask );
            $aDate[0] = ($aDate[0] == '') ? date( 'Y' ) : $aDate[0];
            $aDate[1] = ($aDate[1] == '') ? date( 'm' ) : $aDate[1];
            $aDate[2] = ($aDate[2] == '') ? date( 'd' ) : $aDate[2];
            if (checkdate( $aDate[1], $aDate[2], $aDate[0] )) {
            } else {
                return false;
            }
        }
        $sDateC = '';
        for ($i = 0; $i < count( $aDate ); $i ++) {
            $sDateC .= (($i == 0) ? "" : "-") . $aDate[$i];
        }
        return ($sDateC);
    }

    /**
     * Function updateTables
     * This function updated the Report Tables
     *
     * @access public
     * @param string $sProcessUid
     * @param string $sApplicationUid
     * @param string $iApplicationNumber
     * @param string $aFields
     * @return void
     */
    public function updateTables ($sProcessUid, $sApplicationUid, $iApplicationNumber, $aFields)
    {
        try {
            //get all Active Report Tables
            $oCriteria = new Criteria( 'workflow' );
            $oCriteria->add( ReportTablePeer::PRO_UID, $sProcessUid );
            $oCriteria->add( ReportTablePeer::REP_TAB_STATUS, 'ACTIVE' );
            $oDataset = ReportTablePeer::doSelectRS( $oCriteria );
            $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );
            $oDataset->next();
            $aVars = array ();
            while ($aRow = $oDataset->getRow()) {
                $aRow['REP_TAB_NAME'] = $this->sPrefix . $aRow['REP_TAB_NAME'];
                $PropelDatabase = $this->chooseDB( $aRow['REP_TAB_CONNECTION'] );
                $con = Propel::getConnection( $PropelDatabase );
                $stmt = $con->createStatement();
                switch (DB_ADAPTER) {
                    case 'mysql':
                        $aTableFields = $this->getTableVars( $aRow['REP_TAB_UID'], true );
                        if ($aRow['REP_TAB_TYPE'] == 'NORMAL') {
                            $sqlExists = "SELECT * FROM `" . $aRow['REP_TAB_NAME'] . "` WHERE APP_UID = '" . $sApplicationUid . "'";
                            $rsExists = $stmt->executeQuery( $sqlExists, ResultSet::FETCHMODE_ASSOC );
                            $rsExists->next();
                            $aRow2 = $rsExists->getRow();

                            if (is_array( $aRow2 )) {
                                $sQuery = 'UPDATE `' . $aRow['REP_TAB_NAME'] . '` SET ';
                                foreach ($aTableFields as $aField) {
                                    $sQuery .= '`' . $aField['sFieldName'] . '` = ';
                                    switch ($aField['sType']) {
                                        case 'number':
                                            $sQuery .= (isset( $aFields[$aField['sFieldName']] ) ? (float) str_replace( ',', '', $aFields[$aField['sFieldName']] ) : '0') . ',';
                                            break;
                                        case 'char':
                                        case 'text':
                                            if (! isset( $aFields[$aField['sFieldName']] )) {
                                                $aFields[$aField['sFieldName']] = '';
                                            }
                                            $sQuery .= "'" . (isset( $aFields[$aField['sFieldName']] ) ? mysql_real_escape_string( $aFields[$aField['sFieldName']] ) : '') . "',";
                                            break;
                                        case 'date':
                                            $mysqlDate = (isset( $aFields[$aField['sFieldName']] ) ? $aFields[$aField['sFieldName']] : '');
                                            if ($mysqlDate != '') {
                                                $mysqlDate = str_replace( '/', '-', $mysqlDate );
                                                $mysqlDate = date( 'Y-m-d', strtotime( $mysqlDate ) );
                                            }
                                            $value = trim( $mysqlDate ) != '' ? "'" . $mysqlDate . "'" : 'NULL';
                                            $sQuery .= $value . ",";
                                            break;
                                    }
                                }
                                $sQuery = substr( $sQuery, 0, - 1 );
                                $sQuery .= " WHERE APP_UID = '" . $sApplicationUid . "'";
                            } else {
                                $sQuery = 'INSERT INTO `' . $aRow['REP_TAB_NAME'] . '` (';
                                $sQuery .= '`APP_UID`,`APP_NUMBER`';
                                foreach ($aTableFields as $aField) {
                                    $sQuery .= ',`' . $aField['sFieldName'] . '`';
                                }
                                $sQuery .= ") VALUES ('" . $sApplicationUid . "'," . (int) $iApplicationNumber;
                                foreach ($aTableFields as $aField) {
                                    switch ($aField['sType']) {
                                        case 'number':
                                            $sQuery .= ',' . (isset( $aFields[$aField['sFieldName']] ) ? (float) str_replace( ',', '', $aFields[$aField['sFieldName']] ) : '0');
                                            break;
                                        case 'char':
                                        case 'text':
                                            if (! isset( $aFields[$aField['sFieldName']] )) {
                                                $aFields[$aField['sFieldName']] = '';
                                            }
                                            $sQuery .= ",'" . (isset( $aFields[$aField['sFieldName']] ) ? mysql_real_escape_string( $aFields[$aField['sFieldName']] ) : '') . "'";
                                            break;
                                        case 'date':
                                            $mysqlDate = (isset( $aFields[$aField['sFieldName']] ) ? $aFields[$aField['sFieldName']] : '');
                                            if ($mysqlDate != '') {
                                                $mysqlDate = str_replace( '/', '-', $mysqlDate );
                                                $mysqlDate = date( 'Y-m-d', strtotime( $mysqlDate ) );
                                            }
                                            $value = trim( $mysqlDate ) != '' ? "'" . $mysqlDate . "'" : 'NULL';
                                            $sQuery .= "," . $value;
                                            break;
                                    }
                                }
                                $sQuery .= ')';
                            }
                            $rs = $stmt->executeQuery( $sQuery );
                        } else {
                            //remove old rows from database
                            $sqlDelete = 'DELETE FROM `' . $aRow['REP_TAB_NAME'] . "` WHERE APP_UID = '" . $sApplicationUid . "'";
                            $rsDelete = $stmt->executeQuery( $sqlDelete );

                            $aAux = explode( '-', $aRow['REP_TAB_GRID'] );
                            if (isset( $aFields[$aAux[0]] )) {
                                foreach ($aFields[$aAux[0]] as $iRow => $aGridRow) {
                                    $sQuery = 'INSERT INTO `' . $aRow['REP_TAB_NAME'] . '` (';
                                    $sQuery .= '`APP_UID`,`APP_NUMBER`,`ROW`';
                                    foreach ($aTableFields as $aField) {
                                        $sQuery .= ',`' . $aField['sFieldName'] . '`';
                                    }
                                    $sQuery .= ") VALUES ('" . $sApplicationUid . "'," . (int) $iApplicationNumber . ',' . $iRow;
                                    foreach ($aTableFields as $aField) {
                                        switch ($aField['sType']) {
                                            case 'number':
                                                $sQuery .= ',' . (isset( $aGridRow[$aField['sFieldName']] ) ? (float) str_replace( ',', '', $aGridRow[$aField['sFieldName']] ) : '0');
                                                break;
                                            case 'char':
                                            case 'text':
                                                if (! isset( $aGridRow[$aField['sFieldName']] )) {
                                                    $aGridRow[$aField['sFieldName']] = '';
                                                }
                                                $sQuery .= ",'" . (isset( $aGridRow[$aField['sFieldName']] ) ? mysql_real_escape_string( $aGridRow[$aField['sFieldName']] ) : '') . "'";
                                                break;
                                            case 'date':
                                                $sQuery .= ",'" . (isset( $aGridRow[$aField['sFieldName']] ) ? $aGridRow[$aField['sFieldName']] : '') . "'";
                                                break;
                                        }
                                    }
                                    $sQuery .= ')';
                                    $rs = $stmt->executeQuery( $sQuery );
                                }
                            }
                        }
                        break;

                    /**
                     * For SQLServer code
                     */
                    case 'mssql':
                        $aTableFields = $this->getTableVars( $aRow['REP_TAB_UID'], true );
                        if ($aRow['REP_TAB_TYPE'] == 'NORMAL') {
                            $oDataset2 = mssql_query( "SELECT * FROM [" . $aRow['REP_TAB_NAME'] . "] WHERE APP_UID = '" . $sApplicationUid . "'" );
                            if ($aRow2 = mssql_fetch_row( $oDataset2 )) {
                                $sQuery = 'UPDATE [' . $aRow['REP_TAB_NAME'] . '] SET ';
                                foreach ($aTableFields as $aField) {
                                    $sQuery .= '[' . $aField['sFieldName'] . '] = ';
                                    switch ($aField['sType']) {
                                        case 'number':
                                            $sQuery .= (isset( $aFields[$aField['sFieldName']] ) ? (float) str_replace( ',', '', $aFields[$aField['sFieldName']] ) : '0') . ',';
                                            break;
                                        case 'char':
                                        case 'text':
                                            if (! isset( $aFields[$aField['sFieldName']] )) {
                                                $aFields[$aField['sFieldName']] = '';
                                            }
                                            $sQuery .= "'" . (isset( $aFields[$aField['sFieldName']] ) ? mysql_real_escape_string( $aFields[$aField['sFieldName']] ) : '') . "',";
                                            break;
                                        case 'date':
                                            $sQuery .= "'" . (isset( $aFields[$aField['sFieldName']] ) ? $aFields[$aField['sFieldName']] : '') . "',";
                                            break;
                                    }
                                }
                                $sQuery = substr( $sQuery, 0, - 1 );
                                $sQuery .= " WHERE APP_UID = '" . $sApplicationUid . "'";
                            } else {
                                $sQuery = 'INSERT INTO [' . $aRow['REP_TAB_NAME'] . '] (';
                                $sQuery .= '[APP_UID],[APP_NUMBER]';
                                foreach ($aTableFields as $aField) {
                                    $sQuery .= ',[' . $aField['sFieldName'] . ']';
                                }
                                $sQuery .= ") VALUES ('" . $sApplicationUid . "'," . (int) $iApplicationNumber;
                                foreach ($aTableFields as $aField) {
                                    switch ($aField['sType']) {
                                        case 'number':
                                            $sQuery .= ',' . (isset( $aFields[$aField['sFieldName']] ) ? (float) str_replace( ',', '', $aFields[$aField['sFieldName']] ) : '0');
                                            break;
                                        case 'char':
                                        case 'text':
                                            if (! isset( $aFields[$aField['sFieldName']] )) {
                                                $aFields[$aField['sFieldName']] = '';
                                            }
                                            $sQuery .= ",'" . (isset( $aFields[$aField['sFieldName']] ) ? mysql_real_escape_string( $aFields[$aField['sFieldName']] ) : '') . "'";
                                            break;
                                        case 'date':
                                            $sQuery .= ",'" . (isset( $aFields[$aField['sFieldName']] ) ? $aFields[$aField['sFieldName']] : '') . "'";
                                            break;
                                    }
                                }
                                $sQuery .= ')';
                            }
                            $rs = $stmt->executeQuery( $sQuery );
                        } else {
                            mysql_query( 'DELETE FROM [' . $aRow['REP_TAB_NAME'] . "] WHERE APP_UID = '" . $sApplicationUid . "'" );
                            $aAux = explode( '-', $aRow['REP_TAB_GRID'] );
                            if (isset( $aFields[$aAux[0]] )) {
                                foreach ($aFields[$aAux[0]] as $iRow => $aGridRow) {
                                    $sQuery = 'INSERT INTO [' . $aRow['REP_TAB_NAME'] . '] (';
                                    $sQuery .= '[APP_UID],[APP_NUMBER],[ROW]';
                                    foreach ($aTableFields as $aField) {
                                        $sQuery .= ',[' . $aField['sFieldName'] . ']';
                                    }
                                    $sQuery .= ") VALUES ('" . $sApplicationUid . "'," . (int) $iApplicationNumber . ',' . $iRow;
                                    foreach ($aTableFields as $aField) {
                                        switch ($aField['sType']) {
                                            case 'number':
                                                $sQuery .= ',' . (isset( $aGridRow[$aField['sFieldName']] ) ? (float) str_replace( ',', '', $aGridRow[$aField['sFieldName']] ) : '0');
                                                break;
                                            case 'char':
                                            case 'text':
                                                if (! isset( $aGridRow[$aField['sFieldName']] )) {
                                                    $aGridRow[$aField['sFieldName']] = '';
                                                }
                                                $sQuery .= ",'" . (isset( $aGridRow[$aField['sFieldName']] ) ? mysql_real_escape_string( $aGridRow[$aField['sFieldName']] ) : '') . "'";
                                                break;
                                            case 'date':
                                                $sQuery .= ",'" . (isset( $aGridRow[$aField['sFieldName']] ) ? $aGridRow[$aField['sFieldName']] : '') . "'";
                                                break;
                                        }
                                    }
                                    $sQuery .= ')';
                                    $rs = $stmt->executeQuery( $sQuery );
                                }
                            }
                        }
                        break;

                }
                $oDataset->next();
            }
        } catch (Exception $oError) {
            throw ($oError);
        }
    }

    /**
     * Function tableExist
     * Check if table exists
     *
     * @access public
     * @return boolean
     */
    public function tableExist ()
    {
        /*
        $bExists  = true;
        $oConnection = mysql_connect(DB_HOST, DB_USER, DB_PASS);
        mysql_select_db(DB_NAME);
        $oDataset = mysql_query('SELECT COUNT(*) FROM REPORT_TABLE') || ($bExists = false);
        return $bExists;
        */
        $bExists = true;
        $sDataBase = 'database_' . strtolower( DB_ADAPTER );
        if (G::LoadSystemExist( $sDataBase )) {
            G::LoadSystem( $sDataBase );
            $oDataBase = new database();
            $bExists = $oDataBase->reportTableExist();
        }
        return $bExists;
    }

    /**
     * Function chooseDB
     * Choose the database to connect
     *
     * @access public
     * @param string $TabConnectionk
     * @return string
     */
    public function chooseDB ($TabConnectionk)
    {
        $repTabConnection = trim( strtoupper( $TabConnectionk ) );
        $PropelDatabase = 'rp';
        if ($repTabConnection == '' || $repTabConnection == 'REPORT') {
            $PropelDatabase = 'rp';
        }
        if ($repTabConnection == 'RBAC') {
            $PropelDatabase = 'rbac';
        }
        if ($repTabConnection == 'WF') {
            $PropelDatabase = 'workflow';
        }
        return ($PropelDatabase);
    }
}

