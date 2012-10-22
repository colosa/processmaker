<?php
/**
 * reportTables_Ajax.php
 *
 * ProcessMaker Open Source Edition
 * Copyright (C) 2004 - 2011 Colosa Inc.23
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
 */

G::LoadClass( 'case' );

$action = $_REQUEST['action'];
unset( $_POST['action'] );

switch ($action) {
    case 'availableFieldsReportTables':
        G::LoadClass( 'reportTables' );
        G::LoadClass( 'xmlfield_InputPM' );

        $aFields['FIELDS'] = array ();
        $aFields['PRO_UID'] = $_POST['PRO_UID'];

        if (isset( $_POST['TYPE'] ) && $_POST['TYPE'] == 'GRID') {
            $aProcessGridFields = Array ();
            if (isset( $_POST['GRID_UID'] )) {
                global $G_FORM;
                list ($gridName, $gridId) = explode( '-', $_POST['GRID_UID'] );

                // $G_FORM = new Form($_POST['PRO_UID'] . '/' . $gridId, PATH_DYNAFORM, SYS_LANG, false);
                //$gridFields = $G_FORM->getVars(false);
                $gridFields = getGridDynafields( $_POST['PRO_UID'], $gridId );

                foreach ($gridFields as $gfield) {
                    $aProcessGridFields[] = array ('FIELD_UID' => $gfield['name'] . '-' . $gfield['type'],'FIELD_NAME' => $gfield['name']
                    );
                }
            } else {
                $gridFields = getGridFields( $aFields['PRO_UID'] );

                foreach ($gridFields as $gfield) {
                    $aProcessGridFields[] = array ('FIELD_UID' => $gfield['name'] . '-' . $gfield['xmlform'],'FIELD_NAME' => $gfield['name']
                    );
                }
            }
            $resultList['processFields'] = $aProcessGridFields;

        } else {
            $aProcessFields = Array ();
            //$dynFields = getDynaformsVars($aFields['PRO_UID'], false);
            $dynFields = getDynafields( $aFields['PRO_UID'] );

            foreach ($dynFields as $dfield) {
                $aProcessFields[] = array ('FIELD_UID' => $dfield['name'] . '-' . $dfield['type'],'FIELD_NAME' => $dfield['name']
                );
            }
            $resultList['processFields'] = $aProcessFields;
        }

        echo G::json_encode( $resultList );

        break;
    case 'fieldsList':

        G::LoadClass( 'reportTables' );
        G::LoadClass( 'xmlfield_InputPM' );
        $aFields['FIELDS'] = array ();
        $oReportTable = new ReportTable();
        $aFields = $oReportTable->load( $_POST['REP_TAB_UID'] );
        $aTheFields = getDynaformsVars( $aFields['PRO_UID'], false );
        $oReportTables = new ReportTables();
        $aVars = $oReportTables->getTableVars( $_POST['REP_TAB_UID'] );
        $aFields['FIELDS'] = array ();

        foreach ($aTheFields as $aField) {
            if (in_array( $aField['sName'], $aVars )) {

                $aResultFields[] = array ('FIELD_UID' => $aField['sName'] . '-' . $aField['sType'],'FIELD_NAME' => $aField['sName'],'FIELD_DYNAFORM' => $aField['sName']
                );
            }
        }

        $result->success = true;
        $result->data = $aResultFields;
        echo G::json_encode( $result );
        break;
    case 'getDbConnectionsList':
        G::LoadClass( 'dbConnections' );
        $proUid = $_POST['PRO_UID'];
        $dbConn = new DbConnections();
        $dbConnections = $dbConn->getConnectionsProUid( $proUid );
        $defaultConnections = array (array ('DBS_UID' => 'workflow','DBS_NAME' => 'Workflow'
        ),array ('DBS_UID' => 'rp','DBS_NAME' => 'REPORT'
        )
        );

        echo G::json_encode( array_merge( $defaultConnections, $dbConnections ) );

        break;
    case 'getProcessList':
        require_once 'classes/model/Process.php';

        $process = new Process();
        echo G::json_encode( $process->getAll() );
        break;
    case 'save':
        require_once 'classes/model/AdditionalTables.php';
        require_once 'classes/model/Fields.php';
        try {
            $data = $_POST;
            $data['columns'] = G::json_decode( $_POST['columns'] ); //decofing data columns


            // Reserved Words
            $aReservedWords = array ('ALTER','CLOSE','COMMIT','CREATE','DECLARE','DELETE','DROP','FETCH','FUNCTION','GRANT','INDEX','INSERT','OPEN','REVOKE','ROLLBACK','SELECT','SYNONYM','TABLE','UPDATE','VIEW','APP_UID','ROW'
            );

            $oAdditionalTables = new AdditionalTables();
            $oFields = new Fields();

            // verify if exists.
            $aNameTable = $oAdditionalTables->loadByName( $data['REP_TAB_NAME'] );

            $repTabClassName = to_camel_case( $data['REP_TAB_NAME'] );

            $repTabData = array ('ADD_TAB_UID' => $data['REP_TAB_UID'],'ADD_TAB_NAME' => $data['REP_TAB_NAME'],'ADD_TAB_CLASS_NAME' => $repTabClassName,'ADD_TAB_DESCRIPTION' => $data['REP_TAB_DSC'],'ADD_TAB_PLG_UID' => '','DBS_UID' => $data['REP_TAB_CONNECTION'],'PRO_UID' => $data['PRO_UID'],'ADD_TAB_TYPE' => $data['REP_TAB_TYPE'],'ADD_TAB_GRID' => $data['REP_TAB_GRID']
            );

            $columns = $data['columns'];

            if ($data['REP_TAB_UID'] == '') {
                //new report table
                //setting default columns
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
                array_push( $defaultColumns, $application );

                $application = new stdClass(); //APP_NUMBER
                $application->uid = '';
                $application->field_dyn = '';
                $application->field_uid = '';
                $application->field_name = 'APP_NUMBER';
                $application->field_label = 'APP_NUMBER';
                $application->field_type = 'INT';
                $application->field_size = 11;
                $application->field_dyn = '';
                $application->field_key = 1;
                $application->field_null = 0;
                $application->field_filter = false;
                array_push( $defaultColumns, $application );

                //if it is a grid report table
                if ($data['REP_TAB_TYPE'] == 'GRID') {
                    //GRID INDEX
                    $gridIndex = new stdClass();
                    $gridIndex->uid = '';
                    $gridIndex->field_dyn = '';
                    $gridIndex->field_uid = '';
                    $gridIndex->field_name = 'ROW';
                    $gridIndex->field_label = 'ROW';
                    $gridIndex->field_type = 'INT';
                    $gridIndex->field_size = '11';
                    $gridIndex->field_dyn = '';
                    $gridIndex->field_null = 0;
                    $gridIndex->field_filter = false;
                    array_push( $defaultColumns, $gridIndex );
                }

                $columns = array_merge( $defaultColumns, $columns );

                /**
                 * validations *
                 */
                if (is_array( $aNameTable )) {
                    throw new Exception( 'The table "' . $data['REP_TAB_NAME'] . '" already exits.' );
                }

                if (in_array( strtoupper( $data['REP_TAB_NAME'] ), $aReservedWords )) {
                    throw new Exception( 'Could not create the table with the name "' . $data['REP_TAB_NAME'] . '" because it is a reserved word.' );
                }
                //create record
                $addTabUid = $oAdditionalTables->create( $repTabData );

            } else {
                //editing report table
                $addTabUid = $data['REP_TAB_UID'];
                //loading old data before update
                $addTabBeforeData = $oAdditionalTables->load( $addTabUid, true );
                //updating record
                $oAdditionalTables->update( $repTabData );

                //removing old data fields references
                $oCriteria = new Criteria( 'workflow' );
                $oCriteria->add( FieldsPeer::ADD_TAB_UID, $data['REP_TAB_UID'] );
                //$oCriteria->add(FieldsPeer::FLD_NAME, 'APP_UID', Criteria::NOT_EQUAL);
                //$oCriteria->add(FieldsPeer::FLD_NAME, 'ROW', Criteria::NOT_EQUAL);
                FieldsPeer::doDelete( $oCriteria );

                //getting old fieldnames
                $oldFields = array ();
                foreach ($addTabBeforeData['FIELDS'] as $field) {
                    //if ($field['FLD_NAME'] == 'APP_UID' || $field['FLD_NAME'] == 'ROW') continue;
                    $oldFields[$field['FLD_UID']] = $field;
                }
            }

            $aFields = array ();
            $fieldsList = array ();
            $editFieldsList = array ();

            foreach ($columns as $i => $column) {
                $field = array ('FLD_UID' => $column->uid,'FLD_INDEX' => $i,'ADD_TAB_UID' => $addTabUid,'FLD_NAME' => $column->field_name,'FLD_DESCRIPTION' => $column->field_label,'FLD_TYPE' => $column->field_type,'FLD_SIZE' => $column->field_size,'FLD_NULL' => (isset( $column->field_null ) ? $column->field_null : 1),'FLD_AUTO_INCREMENT' => 0,'FLD_KEY' => (isset( $column->field_key ) ? $column->field_key : 0),'FLD_FOREIGN_KEY' => 0,'FLD_FOREIGN_KEY_TABLE' => '','FLD_DYN_NAME' => $column->field_dyn,'FLD_DYN_UID' => $column->field_uid,'FLD_FILTER' => (isset( $column->field_filter ) && $column->field_filter ? 1 : 0));

                $fieldUid = $oFields->create( $field );
                $fieldsList[] = $field;

                if ($data['REP_TAB_UID'] == '') {
                    //new
                    $aFields[] = array ('sType' => $column->field_type,'iSize' => $column->field_size,'sFieldName' => $column->field_name,'bNull' => (isset( $column->field_null ) ? $column->field_null : 1),'bAI' => 0,'bPrimaryKey' => (isset( $column->field_key ) ? $column->field_key : 0)
                    );
                } else {
                    //editing
                    $field['FLD_UID'] = $fieldUid;
                    $aFields[$fieldUid] = $field;
                }
            }
            if ($data['REP_TAB_UID'] == '') {
                //create a new report table
                $oAdditionalTables->createTable( $data['REP_TAB_NAME'], $data['REP_TAB_CONNECTION'], $aFields );
            } else {
                //editing
                //print_R($aFields);
                $oAdditionalTables->updateTable( $data['REP_TAB_NAME'], $data['REP_TAB_CONNECTION'], $aFields, $oldFields );
            }

            $oAdditionalTables->createPropelClasses( $data['REP_TAB_NAME'], $repTabClassName, $fieldsList, $addTabUid );

            $oAdditionalTables->populateReportTable( $data['REP_TAB_NAME'], $data['REP_TAB_CONNECTION'], $data['REP_TAB_TYPE'], $fieldsList, $data['PRO_UID'], $data['REP_TAB_GRID'] );

            $result->success = true;
        } catch (Exception $e) {
            $result->success = false;
            $result->msg = $e->getMessage();
            $result->trace = $e->getTraceAsString();
        }

        echo G::json_encode( $result );
        break;
    case 'delete':
        require_once 'classes/model/AdditionalTables.php';
        G::LoadClass( 'reportTables' );
        $rows = G::json_decode( $_REQUEST['rows'] );
        $rp = new reportTables();
        $at = new AdditionalTables();

        try {
            foreach ($rows as $row) {
                if ($row->type == 'CLASSIC') {
                    $rp->deleteReportTable( $row->id );
                } else {
                    $at->deleteAll( $row->id );
                }
            }
            $result->success = true;
        } catch (Exception $e) {
            $result->success = false;
            $result->msg = $e->getMessage();
        }
        echo G::json_encode( $result );
        break;
    case 'list':
        require_once 'classes/model/AdditionalTables.php';
        G::LoadClass( 'configuration' );
        G::LoadClass( 'processMap' );
        $configigurations = new Configurations();
        $oProcessMap = new processMap();

        $config = $configigurations->getConfiguration( 'additionalTablesList', 'pageSize', '', $_SESSION['USER_LOGGED'] );
        $env = $configigurations->getConfiguration( 'ENVIRONMENT_SETTINGS', '' );
        $limit_size = isset( $config['pageSize'] ) ? $config['pageSize'] : 20;
        $start = isset( $_REQUEST['start'] ) ? $_REQUEST['start'] : 0;
        $limit = isset( $_REQUEST['limit'] ) ? $_REQUEST['limit'] : $limit_size;
        $filter = isset( $_REQUEST['textFilter'] ) ? $_REQUEST['textFilter'] : '';
        $pro_uid = isset( $_REQUEST['pro_uid'] ) ? $_REQUEST['pro_uid'] : '';

        $process = $pro_uid == '' ? array ('not_equal' => $pro_uid
        ) : array ('equal' => $pro_uid
        );
        $addTab = AdditionalTables::getAll( $start, $limit, $filter, $process );

        if ($pro_uid != '') {
            $c = $oProcessMap->getReportTablesCriteria( $pro_uid );
            $oDataset = RoutePeer::doSelectRS( $c );
            $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );
            $reportTablesOldList = array ();
            while ($oDataset->next()) {
                $reportTablesOldList[] = $oDataset->getRow();
            }
            $addTab['count'] += count( $reportTablesOldList );

            foreach ($reportTablesOldList as $i => $oldRepTab) {
                $addTab['rows'][] = array ('ADD_TAB_UID' => $oldRepTab['REP_TAB_UID'],'PRO_UID' => $oldRepTab['PRO_UID'],'ADD_TAB_DESCRIPTION' => $oldRepTab['REP_TAB_TITLE'],'ADD_TAB_NAME' => $oldRepTab['REP_TAB_NAME'],'ADD_TAB_TYPE' => $oldRepTab['REP_TAB_TYPE'],'TYPE' => 'CLASSIC');
            }

        }

        echo G::json_encode( $addTab );
        break;
    case 'updateTag':
        require_once 'classes/model/AdditionalTables.php';
        $oAdditionalTables = new AdditionalTables();
        $uid = $_REQUEST['ADD_TAB_UID'];
        $value = $_REQUEST['value'];

        $repTabData = array ('ADD_TAB_UID' => $uid,'ADD_TAB_TAG' => $value
        );
        $oAdditionalTables->update( $repTabData );
        break;
}

/**
 * Translates a string with underscores into camel case (e.g.
 * first_name -> firstName)
 *
 * @param string $str String in underscore format
 * @param bool $capitalise_first_char If true, capitalise the first char in $str
 * @return string $str translated into camel caps
 */
function to_camel_case ($str, $capitalise_first_char = true)
{
    if ($capitalise_first_char) {
        $str[0] = strtoupper( $str[0] );
    }
    $func = create_function( '$c', 'return strtoupper($c[1]);' );
    return preg_replace_callback( '/_([a-z])/', $func, $str );
}

function getDynafields ($proUid, $type = 'xmlform')
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

    $excludeFieldsList = array ('title','subtitle','link','file','button','reset','submit','listbox','checkgroup','grid','javascript');

    $labelFieldsTypeList = array ('dropdown','checkbox','radiogroup','yesno');

    while ($aRow = $oDataset->getRow()) {
        if (file_exists( PATH_DYNAFORM . PATH_SEP . $aRow['DYN_FILENAME'] . '.xml' )) {
            $G_FORM = new Form( $aRow['DYN_FILENAME'], PATH_DYNAFORM, SYS_LANG );

            if ($G_FORM->type == 'xmlform' || $G_FORM->type == '') {
                foreach ($G_FORM->fields as $fieldName => $fieldNode) {
                    if (! in_array( $fieldNode->type, $excludeFieldsList ) && ! in_array( $fieldName, $fieldsNames )) {
                        $fields[] = array ('name' => $fieldName,'type' => $fieldNode->type,'label' => $fieldNode->label);
                        $fieldsNames[] = $fieldName;

                        if (in_array( $fieldNode->type, $labelFieldsTypeList ) && ! in_array( $fieldName . '_label', $fieldsNames )) {
                            $fields[] = array ('name' => $fieldName . '_label','type' => $fieldNode->type,'label' => $fieldNode->label . '_label');
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

function getGridDynafields ($proUid, $gridId)
{
    $fields = array ();
    $fieldsNames = array ();
    $excludeFieldsList = array ('title','subtitle','link','file','button','reset','submit','listbox','checkgroup','grid','javascript');

    $labelFieldsTypeList = array ('dropdown','checkbox','radiogroup','yesno');

    $G_FORM = new Form( $proUid . '/' . $gridId, PATH_DYNAFORM, SYS_LANG, false );

    if ($G_FORM->type == 'grid') {
        foreach ($G_FORM->fields as $fieldName => $fieldNode) {
            if (! in_array( $fieldNode->type, $excludeFieldsList ) && ! in_array( $fieldName, $fieldsNames )) {
                $fields[] = array ('name' => $fieldName,'type' => $fieldNode->type,'label' => $fieldNode->label
                );
                $fieldsNames[] = $fieldName;

                if (in_array( $fieldNode->type, $labelFieldsTypeList ) && ! in_array( $fieldName . '_label', $fieldsNames )) {
                    $fields[] = array ('name' => $fieldName . '_label','type' => $fieldNode->type,'label' => $fieldNode->label . '_label');
                    $fieldsNames[] = $fieldName;
                }
            }
        }
    }

    return $fields;
}

function getGridFields ($proUid)
{
    $aFields = array ();
    $aFieldsNames = array ();
    require_once 'classes/model/Dynaform.php';
    $oCriteria = new Criteria( 'workflow' );
    $oCriteria->addSelectColumn( DynaformPeer::DYN_FILENAME );
    $oCriteria->add( DynaformPeer::PRO_UID, $proUid );
    $oDataset = DynaformPeer::doSelectRS( $oCriteria );
    $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );
    $oDataset->next();
    while ($aRow = $oDataset->getRow()) {
        $G_FORM = new Form( $aRow['DYN_FILENAME'], PATH_DYNAFORM, SYS_LANG );
        if ($G_FORM->type == 'xmlform') {
            foreach ($G_FORM->fields as $k => $v) {
                if ($v->type == 'grid') {
                    if (! in_array( $k, $aFieldsNames )) {
                        $aFields[] = array ('name' => $k,'xmlform' => str_replace( $proUid . '/', '', $v->xmlGrid ));
                        $aFieldsNames[] = $k;
                    }
                }
            }
        }
        $oDataset->next();
    }
    return $aFields;
}

function getAllFields ($filepath, $includeTypes = array(), $excludeTypes = array())
{
    $G_FORM = new Form( $filepath, PATH_DYNAFORM, SYS_LANG );
    $fields = array ();
    $fieldsNames = array ();
    $labelFieldsTypeList = array ('dropdown','checkbox','radiogroup','yesno');

    if ($G_FORM->type == 'xmlform' || $G_FORM->type == '') {

        foreach ($G_FORM->fields as $fieldName => $fieldNode) {
            if (! in_array( $fieldNode->type, $excludeTypes )) {
                continue;
            }

            if (count( $includeTypes ) > 0) {
                if (in_array( $fieldNode->type, $includeTypes ) && ! in_array( $fieldName, $fieldsNames )) {
                    $fields[] = array ('name' => $fieldName,'type' => $fieldNode->type,'label' => $fieldNode->label);
                    $fieldsNames[] = $fieldName;

                    if (in_array( $fieldNode->type, $labelFieldsTypeList ) && ! in_array( $fieldName . '_label', $fieldsNames )) {
                        $fields[] = array ('name' => $fieldName . '_label','type' => $fieldNode->type,'label' => $fieldNode->label . '_label');
                        $fieldsNames[] = $fieldName;
                    }
                }
                continue;
            }

            if (! in_array( $fieldName, $fieldsNames )) {

                $fields[] = array ('name' => $fieldName,'type' => $fieldNode->type,'label' => $fieldNode->label);
                $fieldsNames[] = $fieldName;

                if (in_array( $fieldNode->type, $labelFieldsTypeList ) && ! in_array( $fieldName . '_label', $fieldsNames )) {
                    $fields[] = array ('name' => $fieldName . '_label','type' => $fieldNode->type,'label' => $fieldNode->label . '_label');
                    $fieldsNames[] = $fieldName;
                }
            }
        }
    }
    return $fields;
}

