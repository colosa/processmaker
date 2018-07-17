<?php

use ProcessMaker\Plugins\PluginRegistry;

$filter = new InputFilter();
$_POST = $filter->xssFilterHard($_POST);
$_REQUEST = $filter->xssFilterHard($_REQUEST);

if (!isset($_SESSION['USER_LOGGED'])) {
    $res = new stdclass();
    $res->message = G::LoadTranslation('ID_LOGIN_AGAIN');
    $res->lostSession = true;
    $res->success = true;
    print G::json_encode( $res );
    die();
}
if (! isset( $_REQUEST['action'] )) {
    $res['success'] = 'failure';
    $res['message'] = G::LoadTranslation( 'ID_REQUEST_ACTION' );
    print G::json_encode( $res );
    die();
}
if (! function_exists( $_REQUEST['action'] ) || !G::isUserFunction($_REQUEST['action'])) {
    $res['success'] = 'failure';
    $res['message'] = G::LoadTranslation( 'ID_REQUEST_ACTION_NOT_EXIST' );
    print G::json_encode( $res );
    die();
}

$functionName = $_REQUEST['action'];
$functionParams = isset( $_REQUEST['params'] ) ? $_REQUEST['params'] : array ();

$functionName( $functionParams );

function getProcessList ()
{
    $calendar = new Calendar();
    $oProcess = new Process();
    $oCase = new Cases();

    //Get ProcessStatistics Info
    $start = 0;
    $limit = '';
    $proData = $oProcess->getAllProcesses( $start, $limit, null, null, false, true );

    $bCanStart = $oCase->canStartCase( $_SESSION['USER_LOGGED'] );
    if ($bCanStart) {
        $processListInitial = $oCase->getStartCasesPerType( $_SESSION['USER_LOGGED'], 'category' );
        $processList = array ();
        foreach ($processListInitial as $key => $procInfo) {
            if (isset( $procInfo['pro_uid'] )) {
                if (trim( $procInfo['cat'] ) == "") {
                    $procInfo['cat'] = "_OTHER_";
                }
                $processList[$procInfo['catname']][$procInfo['value']] = $procInfo;
            }
        }
        ksort( $processList );
        foreach ($processList as $key => $processInfo) {
            ksort( $processList[$key] );
        }

        if (! isset( $_REQUEST['node'] )) {
            $node = 'root';
        } else {
            $node = $_REQUEST['node'];
        }

        foreach ($proData as $key => $proInfo) {
            $proData[$proInfo['PRO_UID']] = $proInfo;
        }

        $processListTree = array ();
        foreach ($processList as $key => $processInfo) {
            $tempTree['text'] = $key;
            $tempTree['id'] = G::encryptOld($key);
            $tempTree['cls'] = 'folder';
            $tempTree['draggable'] = true;
            $tempTree['optionType'] = "category";
            $tempTree['singleClickExpand'] = true;
            if ($key != "No Category") {
                $tempTree['expanded'] = true;
            } else {
                $tempTree['expanded'] = true;
            }
            $tempTreeChildren = array();
            foreach ($processList[$key] as $keyChild => $processInfoChild) {
                $tempTreeChild['text'] = $keyChild;
                $tempTreeChild['id'] = G::encryptOld($keyChild);
                $tempTreeChild['draggable'] = true;
                $tempTreeChild['leaf'] = true;
                $tempTreeChild['icon'] = '/images/icon.trigger.png';
                $tempTreeChild['allowChildren'] = false;
                $tempTreeChild['optionType'] = "startProcess";
                $tempTreeChild['pro_uid'] = $processInfoChild['pro_uid'];
                $tempTreeChild['tas_uid'] = $processInfoChild['uid'];
                $processInfoChild['myInbox'] = 0;
                $processInfoChild['totalInbox'] = 0;
                if (isset($proData[$processInfoChild['pro_uid']])) {
                    $tempTreeChild['otherAttributes'] = array_merge($processInfoChild, $proData[$processInfoChild['pro_uid']], $calendar->getCalendarFor($_SESSION['USER_LOGGED'], $processInfoChild['pro_uid'], $processInfoChild['uid']));
                    $tempTreeChild['otherAttributes']['PRO_TAS_TITLE'] = str_replace(")", "", str_replace("(", "", trim(str_replace($tempTreeChild['otherAttributes']['PRO_TITLE'], "", $tempTreeChild['otherAttributes']["value"]))));
                    $tempTreeChild['qtip'] = $tempTreeChild['otherAttributes']['PRO_DESCRIPTION'];
                    $tempTreeChildren[] = $tempTreeChild;
                }
            }
            $tempTree['children'] = $tempTreeChildren;
            $processListTree[] = $tempTree;
        }

        $processList = $processListTree;
    } else {
        $processList['success'] = 'failure';
        $processList['message'] = G::LoadTranslation('ID_USER_PROCESS_NOT_START');
    }
    print G::json_encode( $processList );
    die();
}

function ellipsis ($text, $numb)
{
    $text = html_entity_decode( $text, ENT_QUOTES );
    if (strlen( $text ) > $numb) {
        $text = substr( $text, 0, $numb );
        $text = substr( $text, 0, strrpos( $text, " " ) );
        //This strips the full stop:
        if ((substr( $text, - 1 )) == ".") {
            $text = substr( $text, 0, (strrpos( $text, "." )) );
        }
        $etc = "...";
        $text = $text . $etc;
    }

    return $text;
}

function lookinginforContentProcess ($sproUid)
{
    $oContent = new Content();
    ///we are looking for a pro title for this process $sproUid
    $oCriteria = new Criteria( 'workflow' );
    $oCriteria->add( ProcessPeer::PRO_UID, $sproUid );
    $oDataset = ProcessPeer::doSelectRS( $oCriteria );
    $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );
    $oDataset->next();
    $aRow = $oDataset->getRow();
    if (! is_array( $aRow )) {

        $oC = new Criteria( 'workflow' );
        $oC->addSelectColumn( TaskPeer::TAS_UID );
        $oC->addSelectColumn( TaskPeer::TAS_TITLE );
        $oC->add( TaskPeer::PRO_UID, $sproUid );
        $oDataset1 = TaskPeer::doSelectRS( $oC );
        $oDataset1->setFetchmode( ResultSet::FETCHMODE_ASSOC );

        while ($oDataset1->next()) {
            $aRow1 = $oDataset1->getRow();
            Content::insertContent( 'TAS_TITLE', '', $aRow1['TAS_UID'], 'en', $aRow1['TAS_TITLE'] );
        }
        $oC2 = new Criteria( 'workflow' );
        $oC2->addSelectColumn(ProcessPeer::PRO_UID);
        $oC2->addSelectColumn(ProcessPeer::PRO_TITLE);
        $oC2->add( ProcessPeer::PRO_UID, $sproUid );
        $oDataset3 = ProcessPeer::doSelectRS( $oC2 );
        $oDataset3->setFetchmode( ResultSet::FETCHMODE_ASSOC );
        $oDataset3->next();
        $aRow3 = $oDataset3->getRow();

        Content::insertContent( 'PRO_TITLE', '', $aRow3['PRO_UID'], 'en', $aRow3['PRO_TITLE'] );

    }
    return 1;

}

function startCase ()
{
    $filter = new InputFilter();
    $_POST = $filter->xssFilterHard($_POST);
    $_REQUEST = $filter->xssFilterHard($_REQUEST);

    /* GET , POST & $_SESSION Vars */
    /* unset any variable, because we are starting a new case */
    if (isset( $_SESSION['APPLICATION'] )) {
        unset( $_SESSION['APPLICATION'] );
    }
    if (isset( $_SESSION['PROCESS'] )) {
        unset( $_SESSION['PROCESS'] );
    }
    if (isset( $_SESSION['TASK'] )) {
        unset( $_SESSION['TASK'] );
    }
    if (isset( $_SESSION['INDEX'] )) {
        unset( $_SESSION['INDEX'] );
    }
    if (isset( $_SESSION['STEP_POSITION'] )) {
        unset( $_SESSION['STEP_POSITION'] );
    }

        /* Process */
    try {
        $oCase = new Cases();

        lookinginforContentProcess( $_POST['processId'] );

        $aData = $oCase->startCase( $_REQUEST['taskId'], $_SESSION['USER_LOGGED'] );
        $aData = $filter->xssFilterHard($aData);

        $_SESSION['APPLICATION'] = $aData['APPLICATION'];
        $_SESSION['INDEX'] = $aData['INDEX'];
        $_SESSION['PROCESS'] = $aData['PROCESS'];
        $_SESSION['TASK'] = $_REQUEST['taskId'];
        $_SESSION['STEP_POSITION'] = 0;

        $_SESSION['CASES_REFRESH'] = true;

        $oCase = new Cases();
        $aNextStep = $oCase->getNextStep( $_SESSION['PROCESS'], $_SESSION['APPLICATION'], $_SESSION['INDEX'], $_SESSION['STEP_POSITION'] );

        $aNextStep['PAGE'] = 'open?APP_UID=' . $aData['APPLICATION'] . '&DEL_INDEX=' . $aData['INDEX'] . '&action=draft';

        $_SESSION['BREAKSTEP']['NEXT_STEP'] = $aNextStep;
        $aData['openCase'] = $aNextStep;

        $aData['status'] = 'success';
        print (G::json_encode( $aData )) ;
    } catch (Exception $e) {
        $aData['status'] = 'failure';
        $aData['message'] = $e->getMessage();
        print_r( G::json_encode( $aData ) );
    }
}

function getSimpleDashboardData ()
{
    $sUIDUserLogged = $_SESSION['USER_LOGGED'];

    $Criteria = new Criteria( 'workflow' );

    $Criteria->clearSelectColumns();

    $Criteria->addSelectColumn( AppCacheViewPeer::PRO_UID );
    $Criteria->addSelectColumn( AppCacheViewPeer::APP_UID );
    $Criteria->addSelectColumn( AppCacheViewPeer::APP_NUMBER );
    $Criteria->addSelectColumn( AppCacheViewPeer::APP_STATUS );
    $Criteria->addSelectColumn( AppCacheViewPeer::DEL_INDEX );
    $Criteria->addSelectColumn( AppCacheViewPeer::APP_TITLE );
    $Criteria->addSelectColumn( AppCacheViewPeer::APP_PRO_TITLE );
    $Criteria->addSelectColumn( AppCacheViewPeer::APP_TAS_TITLE );
    $Criteria->addSelectColumn( AppCacheViewPeer::APP_DEL_PREVIOUS_USER );
    $Criteria->addSelectColumn( AppCacheViewPeer::DEL_TASK_DUE_DATE );
    $Criteria->addSelectColumn( AppCacheViewPeer::APP_UPDATE_DATE );
    $Criteria->addSelectColumn( AppCacheViewPeer::DEL_PRIORITY );
    $Criteria->addSelectColumn( AppCacheViewPeer::DEL_DELAYED );
    $Criteria->addSelectColumn( AppCacheViewPeer::USR_UID );
    $Criteria->addSelectColumn( AppCacheViewPeer::APP_THREAD_STATUS );

    $Criteria->add( AppCacheViewPeer::APP_STATUS, array ("TO_DO","DRAFT"), CRITERIA::IN );
    $Criteria->add( AppCacheViewPeer::USR_UID, array ($sUIDUserLogged,""), CRITERIA::IN );
    $Criteria->add( AppCacheViewPeer::DEL_FINISH_DATE, null, Criteria::ISNULL );
    //$Criteria->add ( AppCacheViewPeer::APP_THREAD_STATUS, 'OPEN' );
    $Criteria->add( AppCacheViewPeer::DEL_THREAD_STATUS, 'OPEN' );

    //execute the query
    $oDataset = AppCacheViewPeer::doSelectRS( $Criteria );
    $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );
    $oDataset->next();

    $oProcess = new Process();

    $rows = array ();
    $processNames = array ();
    while ($aRow = $oDataset->getRow()) {
        // G::pr($aRow);
        if (! isset( $processNames[$aRow['PRO_UID']] )) {
            $aProcess = $oProcess->load( $aRow['PRO_UID'] );
            $processNames[$aRow['PRO_UID']] = $aProcess['PRO_TITLE'];
        }

        if ($aRow['USR_UID'] == "") {
            $aRow['APP_STATUS'] = "UNASSIGNED";
        }
        if (((in_array( $aRow['APP_STATUS'], array ("TO_DO","UNASSIGNED"
        ) )) && ($aRow['APP_THREAD_STATUS'] == "OPEN")) || ($aRow['APP_STATUS'] == "DRAFT")) {
            $rows[$processNames[$aRow['PRO_UID']]][$aRow['APP_STATUS']][$aRow['DEL_DELAYED']][] = $aRow['APP_UID'];
            if (! isset( $rows[$processNames[$aRow['PRO_UID']]][$aRow['APP_STATUS']]['count'] )) {
                $rows[$processNames[$aRow['PRO_UID']]][$aRow['APP_STATUS']]['count'] = 0;
            }
            $rows[$processNames[$aRow['PRO_UID']]][$aRow['APP_STATUS']]['count'] ++;
        }

        $oDataset->next();
    }
    //Generate different groups of data for graphs
    $rowsResponse = array ();
    $i = 0;
    foreach ($rows as $processID => $processInfo) {
        $i ++;
        if ($i <= 10) {
            $rowsResponse['caseStatusByProcess'][] = array ('process' => $processID,'inbox' => isset( $processInfo['TO_DO']['count'] ) ? $processInfo['TO_DO']['count'] : 0,'draft' => isset( $processInfo['DRAFT']['count'] ) ? $processInfo['DRAFT']['count'] : 0,'unassigned' => isset( $processInfo['UNASSIGNED']['count'] ) ? $processInfo['UNASSIGNED']['count'] : 0);
        }
    }
    $rowsResponse['caseDelayed'][] = array ('delayed' => 'On Time','total' => 100);
    $rowsResponse['caseDelayed'][] = array ('delayed' => 'Delayed','total' => 50
    );

    print_r( G::json_encode( $rowsResponse ) );
}

function getRegisteredDashboards ()
{
    $oPluginRegistry = PluginRegistry::loadSingleton();
    $dashBoardPages = $oPluginRegistry->getDashboardPages();
    print_r( G::json_encode( $dashBoardPages ) );
}

function getDefaultDashboard ()
{
    $defaultDashboard['defaultTab'] = "mainDashboard";
    if (isset( $_SESSION['__currentTabDashboard'] )) {
        $defaultDashboard['defaultTab'] = $_SESSION['__currentTabDashboard'];
    }
    print_r( G::json_encode( $defaultDashboard ) );
}

