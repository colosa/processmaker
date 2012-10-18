<?php

//$req = $_POST['request'];
$req = (isset($_POST['request']))? $_POST['request']:((isset($_REQUEST['request']))? $_REQUEST['request'] : 'No hayyy tal');

require_once 'classes/model/Content.php';

switch($req){
    case 'showUsers':
        /*
        $sql = "SELECT USR_UID, USR_EMAIL, CONCAT(USR_FIRSTNAME, ' ' , USR_LASTNAME) AS USR_FULLNAME FROM USERS WHERE USR_STATUS = 'ACTIVE' AND USR_EMAIL <> ''";
        */
        $sDataBase = 'database_' . strtolower(DB_ADAPTER);
        if (G::LoadSystemExist($sDataBase)) {
            G::LoadSystem($sDataBase);
            $oDataBase = new database();
            $sConcat = $oDataBase->concatString("USR_FIRSTNAME", "' '" , "USR_LASTNAME") ;
        }
        $sql = " SELECT USR_UID, USR_EMAIL, " .
            $sConcat .
            " AS USR_FULLNAME FROM USERS " .
            " WHERE USR_STATUS = 'ACTIVE' AND USR_EMAIL <> ''";

        $oCriteria = new Criteria('workflow');
        $del = DBAdapter::getStringDelimiter();

        $con = Propel::getConnection("workflow");
        $stmt = $con->prepareStatement($sql);
        $rs = $stmt->executeQuery();

        $aRows[] = array('USR_UID'=>'char', 'USR_EMAIL'=>'char', 'USR_FULLNAME'=>'char');
        while ($rs->next()) {
            $aRows[] = array('USR_UID'=>$rs->getString('USR_UID'), 'USR_EMAIL'=>$rs->getString('USR_EMAIL'), 'USR_FULLNAME'=>$rs->getString('USR_FULLNAME'));
        }
        //echo '<pre>';     print_r($aRows);

        global $_DBArray;
        $_DBArray['virtualtable']   = $aRows;
        $_SESSION['_DBArray'] = $_DBArray;
        G::LoadClass('ArrayPeer');
        $oCriteria = new Criteria('dbarray');
        $oCriteria->setDBArrayTable('virtualtable');
        $G_PUBLISH = new Publisher();
        $G_PUBLISH->AddContent('propeltable', 'paged-table', 'events/usermailList', $oCriteria);
        G::RenderPage('publish', 'raw');
        break;
    case 'showGroups':
        G::LoadClass('groups');
        $groups = new Groups();
        $allGroups= $groups->getAllGroups();

        $aRows[] = array('GRP_UID' => 'char', 'GROUP_TITLE' => 'char');
        foreach ($allGroups as $group) {
            $UID         = htmlentities($group->getGrpUid());
            $GROUP_TITLE = strip_tags($group->getGrpTitle());
            $aRows[] = array('GRP_UID'=>$UID, 'GROUP_TITLE'=>$GROUP_TITLE);
        }
        global $_DBArray;
        $_DBArray['virtualtable']   = $aRows;
        $_SESSION['_DBArray'] = $_DBArray;
        G::LoadClass('ArrayPeer');
        $oCriteria = new Criteria('dbarray');
        $oCriteria->setDBArrayTable('virtualtable');
        $G_PUBLISH = new Publisher();
        $G_PUBLISH->AddContent('propeltable', 'paged-table', 'events/groupmailList', $oCriteria);
        G::RenderPage('publish', 'raw');
        break;
    case 'showDynavars':
        G::LoadClass('xmlfield_InputPM');
        $dynaformFields = getDynaformsVars($_SESSION['PROCESS'], false, false);
        $fields = array(array('id' => 'char', 'dynaform' => 'char', 'name' => 'char'));
        foreach ($dynaformFields as $dynaformField) {
            $fields[] = array('id' => $dynaformField['sName'],
                              'name' => '<a href="#" style="color: black;" onclick="e.toAdd(\'' . $dynaformField['sName'] . '\', \'' . $dynaformField['sName'] . '\', \'dyn\');oPanel.remove();return false;">@#' . $dynaformField['sName'] . '</a>', 'label' => $dynaformField['sLabel']);
        }
        global $_DBArray;
        $_DBArray['virtualtable'] = $fields;
        $_SESSION['_DBArray'] = $_DBArray;
        G::LoadClass('ArrayPeer');
        $oCriteria = new Criteria('dbarray');
        $oCriteria->setDBArrayTable('virtualtable');
        $G_PUBLISH = new Publisher();
        $G_PUBLISH->AddContent('propeltable', 'paged-table', 'events/dynavarsList', $oCriteria);
        G::RenderPage('publish', 'raw');
        break;
    case 'eventList':
        $start      = (isset($_REQUEST['start']))?      $_REQUEST['start']      : '0';
        $limit      = (isset($_REQUEST['limit']))?      $_REQUEST['limit']      : '25';
        $proUid     = (isset($_REQUEST['process']))?    $_REQUEST['process']    : '';
        $evenType   = (isset($_REQUEST['type']))?       $_REQUEST['type']       : '';
        $evenStatus = (isset($_REQUEST['status']))?     $_REQUEST['status']     : '';
        $sort       = isset($_REQUEST['sort']) ?        $_REQUEST['sort']       : '';
        $dir        = isset($_REQUEST['dir']) ?         $_REQUEST['dir']        : 'ASC';

        require_once 'classes/model/AppEvent.php';
        $oAppEvent = new AppEvent();
        // Initialize response object
        $response = new stdclass();
        $response->status = 'OK';

        $criteria = new Criteria();
        $criteria = $oAppEvent->getAppEventsCriteria($proUid, $evenStatus, $evenType);
        $result = AppEventPeer::doSelectRS($criteria);
        $result->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        $data = Array();
        while ( $result->next() ) {
            $data[] = $result->getRow();
        }
        $totalCount = count($data);

        $criteria = new Criteria();
        $criteria = $oAppEvent->getAppEventsCriteria($proUid, $evenStatus, $evenType);
        if ($sort != '') {
            if ($dir == 'ASC') {
                $criteria->addAscendingOrderByColumn($sort);
            } else {
                $criteria->addDescendingOrderByColumn($sort);
            }
        } else {
            $oCriteria->addDescendingOrderByColumn(AppEventPeer::APP_EVN_ACTION_DATE);
        }
        if ($limit != '') {
            $criteria->setLimit($limit);
            $criteria->setOffset($start);
        }
        $result = AppEventPeer::doSelectRS($criteria);
        $result->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        $data = Array();
        $dataPro = array();
        $index = 0;
        while ( $result->next() ) {
            $data[] = $result->getRow();
        }
        $response = array();
        $response['totalCount'] = $totalCount;
        $response['data']       = $data;
        die(G::json_encode($response));
        break;
}

