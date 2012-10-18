<?php
G::LoadClass( 'processMap' );
$oProcessMap = new processMap( new DBConnection() );

//$_GET['sid'] gets STEP_UID and sTYPE(i.e BEFORE and AFTER) in format STEP_UID-sTYPE
if (isset( $_GET['stepid'] )) {
    $aStepTypeId = explode( '|', $_GET['stepid'] );
    $_SESSION['stepUID'] = $_GET['stepid'];
    //$aStepTypeId = explode('-','2517180104cd42c25cc39e4071099227-BEFORE');
    $sStep = $aStepTypeId[0];
    $sType = $aStepTypeId[1];
}

//$_GET['sid'] gets STEP_UID and sTYPE(i.e BEFORE and AFTER) in format STEP_UID-sTYPE
if (isset( $_GET['stepid'] )) {
    $aStepTypeId = explode( '|', $_GET['stepid'] );
    $_SESSION['stepUID'] = $_GET['stepid'];
    //$aStepTypeId = explode('-','2517180104cd42c25cc39e4071099227-BEFORE');
    $sStep = $aStepTypeId[0];
    $sType = $aStepTypeId[1];
}

$start = isset( $_POST['start'] ) ? $_POST['start'] : 0;
$limit = isset( $_POST['limit'] ) ? $_POST['limit'] : '';

switch ($_GET['action']) {
    case 'getDynaformList':
        $result = $oProcessMap->getExtDynaformsList( $start, $limit, $_GET['pid'] );
        print G::json_encode( $result );
        break;

    case 'getPMTableDynaform':
        $oAdditionalTables = new AdditionalTables();
        $aData = $oAdditionalTables->load( $_GET['tabId'], true );
        $addTabName = $aData['ADD_TAB_NAME'];
        foreach ($aData['FIELDS'] as $iRow => $aRow) {
            if ($aRow['FLD_KEY'] == 1) {
                $rows[] = $aRow;
            }
        }
        $result['totalCount'] = count( $rows );
        $result['data'] = $rows;
        print G::json_encode( $result );
        break;

    case 'getAdditionalTables':
        $rows = $oProcessMap->getExtAdditionalTablesList();
        $result['totalCount'] = count( $rows );
        $result['data'] = $rows;
        print G::json_encode( $result );
        break;

    case 'getInputDocumentList':
        $rows = $oProcessMap->getExtInputDocumentsCriteria( $start, $limit, $_GET['pid'] );
        $result['totalCount'] = $oProcessMap->getAllInputDocumentCount();
        array_shift( $rows );
        $result['data'] = $rows;
        print G::json_encode( $result );
        break;

    case 'editInputDocument':
        require_once 'classes/model/InputDocument.php';
        $oInputDocument = new InputDocument();
        $rows = $oInputDocument->load( $_GET['INP_DOC_UID'] );
        $tmpData = G::json_encode( $rows );
        $tmpData = str_replace( "\\/", "/", '{success:true,data:' . $tmpData . '}' ); // unescape the slashes


        $result = $tmpData;
        echo $result;
        break;

    case 'getOutputDocument':
        $rows = $oProcessMap->getExtOutputDocumentsCriteria( $start, $limit, $_GET['pid'] );
        $result['totalCount'] = $oProcessMap->getAllOutputDocumentCount();
        array_shift( $rows );
        $result['data'] = $rows;
        print G::json_encode( $result );
        break;

    case 'editObjectPermission':
        $rows = $oProcessMap->editExtObjectPermission( $_GET['pid'], $_GET['op_uid'] );
        //array_shift($rows);
        $tmpData = G::json_encode( $rows );
        $tmpData = str_replace( "\\/", "/", '{success:true,data:' . $tmpData . '}' ); // unescape the slashes


        $result = $tmpData;
        echo $result;
        break;

    case 'editOutputDocument':
        require_once 'classes/model/OutputDocument.php';
        $oOutputDocument = new OutputDocument();
        $rows = $oOutputDocument->load( $_GET['tid'] );
        $tmpData = G::json_encode( $rows );
        $tmpData = str_replace( "\\/", "/", '{success:true,data:' . $tmpData . '}' ); // unescape the slashes


        $result = $tmpData;
        echo $result;
        break;

    case 'getReportTables':
        $rows = $oProcessMap->getExtReportTables( $start, $limit, $_GET['pid'] );
        $result['totalCount'] = $oProcessMap->getAllReportTableCount();
        $result['data'] = $rows;
        print G::json_encode( $result );
        break;

    case 'editReportTables':
        require_once 'classes/model/ReportTable.php';
        $oReportTable = new ReportTable();
        $rows = $oReportTable->load( $_GET['REP_TAB_UID'], $_GET['pid'] );
        $tmpData = G::json_encode( $rows );
        $tmpData = str_replace( "\\/", "/", '{success:true,data:' . $tmpData . '}' ); // unescape the slashes


        $result = $tmpData;
        echo $result;
        break;

    case 'getReportTableType':
        if (isset( $_GET['pid'] ) && $_GET['type'] == 'NORMAL') {
            $aTheFields = array ();
            $aTheFields = getDynaformsVars( $_GET['pid'], false );
            foreach ($aTheFields as $aField) {
                $rows[] = array ('FIELD_UID' => $aField['sName'] . '-' . $aField['sType'],'FIELD_NAME' => $aField['sName']
                );
            }
        }

        else if (isset( $_GET['pid'] ) && $_GET['type'] == 'GRID') {
            $aTheFields = array ();
            $aTheFields = getGridsVars( $_GET['pid'] );
            foreach ($aTheFields as $aField) {
                $rows[] = array ('FIELD_UID' => $aField['sName'] . '-' . $aField['sXmlForm'],'FIELD_NAME' => $aField['sName']
                );
            }
        }
        $result['totalCount'] = count( $rows );
        $result['data'] = $rows;
        print G::json_encode( $result );
        break;

    case 'getDatabaseConnectionList':
        $rows = $oProcessMap->getExtCriteriaDBSList( $start, $limit, $_GET['pid'] );
        $result['totalCount'] = $oProcessMap->getAllDbSourceCount();
        $result['data'] = $rows;
        print G::json_encode( $result );
        break;

    case 'editDatabaseConnection':
        require_once 'classes/model/DbSource.php';
        $o = new DbSource();
        $rows = $o->load( $_GET['dbs_uid'], $_GET['pid'] );
        $tmpData = G::json_encode( $rows );
        $tmpData = str_replace( "\\/", "/", '{success:true,data:' . $tmpData . '}' ); // unescape the slashes


        $result = $tmpData;
        echo $result;
        break;

    case 'process_Supervisors':
        $rows = $oProcessMap->listExtProcessesSupervisors( $start, $limit, $_GET['pid'] );
        $result['totalCount'] = $oProcessMap->getAllProcessSupervisorsCount();
        $result['data'] = $rows;
        print G::json_encode( $result );
        break;

    case 'availableProcessesSupervisors':
        $rows = $oProcessMap->listExtNoProcessesUser( $_GET['pid'] );
        $result['totalCount'] = count( $rows );
        $result['data'] = $rows;
        print G::json_encode( $result );
        break;

    case 'supervisorDynaforms':
        $rows = $oProcessMap->getExtSupervisorDynaformsList( $start, $limit, $_GET['pid'] );
        $result['totalCount'] = $oProcessMap->getAllSupervisorDynaformsCount();
        $result['data'] = $rows;
        print G::json_encode( $result );
        break;
    case 'availableSupervisorDynaforms':
        $rows = $oProcessMap->getExtAvailableSupervisorDynaformsList( $_GET['pid'] );
        $result['totalCount'] = count( $rows );
        $result['data'] = $rows;
        print G::json_encode( $result );
        break;
    case 'supervisorInputDoc':
        $rows = $oProcessMap->getExtSupervisorInputsList( $start, $limit, $_GET['pid'] );
        $result['totalCount'] = $oProcessMap->getAllSupervisorInputsCount();
        $result['data'] = $rows;
        print G::json_encode( $result );
        break;
    case 'availableSupervisorInputDoc':
        $rows = $oProcessMap->getExtAvailableSupervisorInputsList( $_GET['pid'] );
        $result['totalCount'] = count( $rows );
        $result['data'] = $rows;
        print G::json_encode( $result );
        break;

    case 'getAssignedCaseTrackerObjects':
        $rows = $oProcessMap->getExtCaseTrackerObjectsCriteria( $start, $limit, $_GET['pid'] );
        $result['totalCount'] = $oProcessMap->getAllCaseTrackerObjectCount();
        array_shift( $rows );
        $result['data'] = $rows;
        print G::json_encode( $result );
        break;

    case 'getAvailableCaseTrackerObjects':
        $rows = $oProcessMap->getAvailableExtCaseTrackerObjects( $_GET['pid'] );
        array_shift( $rows );
        $result['totalCount'] = count( $rows );
        $result['data'] = $rows;
        print G::json_encode( $result );
        break;

    case 'getAvailableSteps':
        $rows = $oProcessMap->getExtAvailableBBCriteria( $_GET['pid'], $_GET['tid'] );
        array_shift( $rows );
        $result['totalCount'] = count( $rows );
        $result['data'] = $rows;
        print G::json_encode( $result );
        break;

    case 'getAssignedSteps':
        $rows = $oProcessMap->getExtStepsCriteria( $start, $limit, $_GET['tid'] );
        $result['totalCount'] = $oProcessMap->getAllStepCount();
        array_shift( $rows );
        $result['data'] = $rows;
        print G::json_encode( $result );
        break;

    case 'getAssignedUsersList':
        $rows = $oProcessMap->usersExtList( $start, $limit, $_GET['pid'], $_GET['tid'] );
        $result['totalCount'] = $oProcessMap->getAllTaskUserCount();
        array_shift( $rows );
        $result['data'] = $rows;
        print G::json_encode( $result );
        break;

    case 'getAvailableUsersList':
        $rows = $oProcessMap->getAvailableExtUsersCriteria( $_GET['tid'] );
        $result['totalCount'] = count( $rows );
        $result['data'] = $rows;
        print G::json_encode( $result );
        break;

    case 'getAvailableStepTriggers':
        $aStepTypeId = explode( '|', $_SESSION['stepUID'] );
        $sStep = $aStepTypeId[0];
        $sType = $aStepTypeId[1];
        //Getting available Steps Criteria that have been not selected for a particular task
        $rows = $oProcessMap->getExtAvailableStepTriggersCriteria( $_GET['pid'], $sStep, $_GET['tid'], $sType );
        $result['totalCount'] = count( $rows );
        $result['data'] = $rows;
        print G::json_encode( $result );
        break;

    case 'getAssignedStepTriggers':
        $rows = $oProcessMap->getExtStepTriggersCriteria( $start, $limit, $sStep, $_GET['tid'], $sType );
        $result['totalCount'] = $oProcessMap->getAllStepTriggerCount();
        $result['data'] = $rows;
        print G::json_encode( $result );
        break;

    case 'availableUsers':
        $rows = $oProcessMap->getExtAvailableUsersList( $_GET['tid'] );
        array_shift( $rows );
        $result['totalCount'] = count( $rows );
        $result['data'] = $rows;
        print G::json_encode( $result );
        break;

    case 'assignedUsers':
        $rows = $oProcessMap->getExtusersadhoc( $start, $limit, $_GET['pid'], $_GET['tid'] );
        $result['totalCount'] = $oProcessMap->getAllTaskUserCount();
        array_shift( $rows );
        $result['data'] = $rows;
        print G::json_encode( $result );
        break;

    case 'getTaskPropertiesList':
        require_once 'classes/model/Task.php';
        $oTask = new Task();
        $rows = $oTask->load( $_GET['tid'] );
        while (list ($key, $value) = each( $rows )) {
            if ($value == 'TRUE')
                $rows[$key] = true;
            else if ($value == 'FALSE')
                $rows[$key] = false;

            if ($key == 'TAS_TYPE_DAY' && $value == '1')
                $rows[$key] = 'Work Days';
            else if ($key == 'TAS_TYPE_DAY' && $value == '2')
                $rows[$key] = 'Calendar Days';

            if ($key == 'TAS_TYPE' && $value == 'NORMAL')
                $rows[$key] = false;
            else if ($key == 'TAS_TYPE' && $value == 'ADHOC')
                $rows[$key] = true;
        }
        $tmpData = G::json_encode( $rows );
        $tmpData = str_replace( "\\/", "/", '{success:true,data:' . $tmpData . '}' ); // unescape the slashes


        $result = $tmpData;
        echo $result;
        break;

    case 'getSubProcessProperties':
        if ($_GET['type'] == 2)         //Loading sub process details
{
            $rows = $oProcessMap->subProcessExtProperties( $_GET['pid'], $_GET['tid'], '', '0' );
            $tmpData = G::json_encode( $rows );
            $tmpData = str_replace( "\\/", "/", '{success:true,data:' . $tmpData . '}' ); // unescape the slashes


            $result = $tmpData;
            print $result;
        } else {
            $rows = $oProcessMap->subProcessExtProperties( $_GET['pid'], $_GET['tid'], '', $_GET['type'] );
            $result['totalCount'] = count( $rows );
            $result['data'] = $rows;
            print G::json_encode( $result );
        }
        break;
    case 'getObjectPermission':
        $rows = $oProcessMap->getExtObjectsPermissions( $start, $limit, $_GET['pid'] );
        $result['totalCount'] = $oProcessMap->getAllObjectPermissionCount();
        $result['data'] = $rows;
        print G::json_encode( $result );
        break;

    case 'getObjectPermissionType':
        $rows = $oProcessMap->newExtObjectPermission( $_GET['pid'], $_GET['objectType'] );
        array_shift( $rows );
        $result['totalCount'] = count( $rows );
        $result['data'] = $rows;
        print G::json_encode( $result );
        break;

    case 'process_Edit':
        $rows = $oProcessMap->editProcessNew( $_GET['pid'] );
        $tmpData = G::json_encode( $rows );
        $tmpData = str_replace( "\\/", "/", '{success:true,data:' . $tmpData . '}' ); // unescape the slashes


        $result = $tmpData;
        echo $result;
        break;

    case 'getTriggersList':
        $rows = $oProcessMap->getExtTriggersList( $start, $limit, $_GET['pid'] );
        $result['totalCount'] = $oProcessMap->getAllTriggersCount();
        array_shift( $rows );
        $result['data'] = $rows;
        print G::json_encode( $result );
        break;

    case 'editTriggers':
        require_once ('classes/model/Triggers.php');

        if (isset( $_GET['TRI_UID'] )) {
            $oTrigger = new Triggers();
            $rows = $oTrigger->load( $_GET['TRI_UID'] );
        }
        $tmpData = G::json_encode( $rows );
        $tmpData = str_replace( "\\/", "/", '{success:true,data:' . $tmpData . '}' ); // unescape the slashes
        $result = $tmpData;
        echo $result;
        break;

    case 'getCaseTracker':
        //$rows = $oProcessMap->caseTracker($_GET['pid']);
        $oCaseTracker = new CaseTracker();
        $rows = $oCaseTracker->load( $_GET['pid'] );
        $tmpData = G::json_encode( $rows );
        $tmpData = str_replace( "\\/", "/", '{success:true,data:' . $tmpData . '}' ); // unescape the slashes
        $result = $tmpData;
        echo $result;
        break;

    case 'getVariables':
        $aFields = getDynaformsVars( $_GET['pid'] );
        if (isset( $_GET['type'] ))

            $aType = $_GET['type'];

        else
            $aType = '';

        $rows[0] = Array ('fieldname' => 'char','variable' => 'char','type' => 'type','label' => 'char'
        );
        foreach ($aFields as $aField) {
            switch ($aType) {
                case "system":
                    if ($aField['sType'] == "system") {
                        $rows[] = Array ('fieldname' => $_GET['sFieldName'],'variable' => $_GET['sSymbol'] . $aField['sName'],'variable_label' => '<div class="pm__dynavars"> <a id="dynalink" href=# onclick="insertFormVar(\'' . $_GET['sFieldName'] . '\',\'' . $_GET['sSymbol'] . $aField['sName'] . '\');">' . $_GET['sSymbol'] . $aField['sName'] . '</a></div>','type' => $aField['sType'],'label' => $aField['sLabel']
                        );
                    }
                    break;
                case "process":
                    if ($aField['sType'] != "system") {
                        $rows[] = Array ('fieldname' => $_GET['sFieldName'],'variable' => $_GET['sSymbol'] . $aField['sName'],'variable_label' => '<div class="pm__dynavars"> <a id="dynalink" href=# onclick="insertFormVar(\'' . $_GET['sFieldName'] . '\',\'' . $_GET['sSymbol'] . $aField['sName'] . '\');">' . $_GET['sSymbol'] . $aField['sName'] . '</a></div>','type' => $aField['sType'],'label' => $aField['sLabel']
                        );
                    }
                    break;
                default:
                    $rows[] = Array ('fieldname' => $_GET['sFieldName'],'variable' => $_GET['sSymbol'] . $aField['sName'],'variable_label' => '<div class="pm__dynavars"> <a id="dynalink" href=# onclick="insertFormVar(\'' . $_GET['sFieldName'] . '\',\'' . $_GET['sSymbol'] . $aField['sName'] . '\');">' . $_GET['sSymbol'] . $aField['sName'] . '</a></div>','type' => $aField['sType'],'label' => $aField['sLabel']
                    );
                    break;
            }

        }

        array_shift( $rows );
        $result['totalCount'] = count( $rows );
        $result['data'] = $rows;
        print G::json_encode( $result );
        break;

}
   //$result['data'] = $rows;
   //print G::json_encode( $result ) ;
    /*$tmpData = G::json_encode( $rows ) ;
    $tmpData = str_replace("\\/","/",'{success:true,data:'.$tmpData.'}'); // unescape the slashes
    $result = $tmpData;
    echo $result;*/

