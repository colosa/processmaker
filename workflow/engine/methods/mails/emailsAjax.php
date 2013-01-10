<?php
$req = (isset($_POST['request']))? $_POST['request']:((isset($_REQUEST['request']))? $_REQUEST['request'] : 'No hayyy tal');

require_once 'classes/model/Content.php';
require_once 'classes/model/AppMessage.php';
require_once 'classes/model/AppDelegation.php';
require_once 'classes/model/Application.php';
switch($req){
    case 'MessageList':
        $start      = (isset($_REQUEST['start']))?      $_REQUEST['start']      : '0';
        $limit      = (isset($_REQUEST['limit']))?      $_REQUEST['limit']      : '25';
        $proUid     = (isset($_REQUEST['process']))?    $_REQUEST['process']    : '';
        $eventype   = (isset($_REQUEST['type']))?       $_REQUEST['type']       : '';
        $emailStatus = (isset($_REQUEST['status']))?     $_REQUEST['status']     : '';
        $sort       = isset($_REQUEST['sort']) ?        $_REQUEST['sort']       : '';
        $dir        = isset($_REQUEST['dir']) ?         $_REQUEST['dir']        : 'ASC';
        $dateFrom   = isset( $_POST["dateFrom"] ) ? substr( $_POST["dateFrom"], 0, 10 ) : "";
        $dateTo     = isset( $_POST["dateTo"] ) ? substr( $_POST["dateTo"], 0, 10 ) : "";

        $response = new stdclass();
        $response->status = 'OK';

        $criteria = new Criteria();
        $criteria->addJoin(AppMessagePeer::APP_UID, ApplicationPeer::APP_UID);
        if ($emailStatus != '') {
            $criteria->add( AppMessagePeer::APP_MSG_STATUS, $emailStatus);
        }
        if ($proUid != '') {
            $criteria->add( ApplicationPeer::PRO_UID, $proUid);
        }
        if ($dateFrom != '') {
            if ($dateTo != '') {
                $criteria->add( $criteria->getNewCriterion( AppMessagePeer::APP_MSG_DATE, $dateFrom, Criteria::GREATER_EQUAL )->addAnd( $criteria->getNewCriterion( AppMessagePeer::APP_MSG_DATE, $dateTo, Criteria::LESS_EQUAL ) ) );
            } else {
                $criteria->add( AppMessagePeer::APP_MSG_DATE, $dateFrom, Criteria::GREATER_EQUAL );
            }
        } elseif ($dateTo != '') {
            $criteria->add( AppMessagePeer::APP_MSG_DATE, $dateTo, Criteria::LESS_EQUAL );
        }

        $result = AppMessagePeer::doSelectRS($criteria);
        $result->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        $data = Array();
        while ( $result->next() ) {
            $data[] = $result->getRow();
        }
        $totalCount = count($data);

        $criteria = new Criteria();
        $criteria->addSelectColumn(AppMessagePeer::APP_MSG_UID);
        $criteria->addSelectColumn(AppMessagePeer::APP_UID);
        $criteria->addSelectColumn(AppMessagePeer::DEL_INDEX);
        $criteria->addSelectColumn(AppMessagePeer::APP_MSG_TYPE);
        $criteria->addSelectColumn(AppMessagePeer::APP_MSG_SUBJECT);
        $criteria->addSelectColumn(AppMessagePeer::APP_MSG_FROM);
        
        $criteria->addSelectColumn(AppMessagePeer::APP_MSG_TO);
        $criteria->addSelectColumn(AppMessagePeer::APP_MSG_BODY);
        $criteria->addSelectColumn(AppMessagePeer::APP_MSG_STATUS);
        $criteria->addSelectColumn(AppMessagePeer::APP_MSG_DATE);
        $criteria->addSelectColumn(AppMessagePeer::APP_MSG_SEND_DATE);
        $criteria->addSelectColumn(AppMessagePeer::APP_MSG_SHOW_MESSAGE);

        $criteria->addSelectColumn(ApplicationPeer::PRO_UID);
        $criteria->addSelectColumn(ApplicationPeer::APP_NUMBER);

        $criteria->addAsColumn('PRO_TITLE', 'C2.CON_VALUE');
        $criteria->addAlias('C2', 'CONTENT');

        if ($emailStatus != '') {
            $criteria->add( AppMessagePeer::APP_MSG_STATUS, $emailStatus);
        }
        if ($proUid != '') {
            $criteria->add( ApplicationPeer::PRO_UID, $proUid);
        }
        if ($dateFrom != '') {
            if ($dateTo != '') {
                $criteria->add( $criteria->getNewCriterion( AppMessagePeer::APP_MSG_DATE, $dateFrom, Criteria::GREATER_EQUAL )->addAnd( $criteria->getNewCriterion( AppMessagePeer::APP_MSG_DATE, $dateTo, Criteria::LESS_EQUAL ) ) );
            } else {
                $criteria->add( AppMessagePeer::APP_MSG_DATE, $dateFrom, Criteria::GREATER_EQUAL );
            }
        } elseif ($dateTo != '') {
            $criteria->add( AppMessagePeer::APP_MSG_DATE, $dateTo, Criteria::LESS_EQUAL );
        }

        if ($sort != '') {
            if ($dir == 'ASC') {
                $criteria->addAscendingOrderByColumn($sort);
            } else {
                $criteria->addDescendingOrderByColumn($sort);
            }
        } else {
            $oCriteria->addDescendingOrderByColumn(AppMessagePeer::APP_MSG_SEND_DATE );
        }
        if ($limit != '') {
            $criteria->setLimit($limit);
            $criteria->setOffset($start);
        }
        $criteria->addJoin(AppMessagePeer::APP_UID, ApplicationPeer::APP_UID);

        $conditions = array();
        $conditions[] = array(ApplicationPeer::PRO_UID, 'C2.CON_ID');
        $conditions[] = array(
            'C2.CON_CATEGORY', DBAdapter::getStringDelimiter() . 'PRO_TITLE' . DBAdapter::getStringDelimiter()
        );
        $conditions[] = array(
            'C2.CON_LANG', DBAdapter::getStringDelimiter() . SYS_LANG . DBAdapter::getStringDelimiter()
        );
        $criteria->addJoinMC($conditions, Criteria::LEFT_JOIN);
        $result = AppMessagePeer::doSelectRS($criteria);
        $result->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        $data = Array();
        $dataPro = array();
        $index = 1;
        $content = new Content();
        $tasTitleDefault = G::LoadTranslation('ID_TASK_NOT_RELATED');
        while ( $result->next() ) {
            $row = $result->getRow();
            $row['APP_MSG_FROM'] =htmlentities($row['APP_MSG_FROM'], ENT_QUOTES, "UTF-8");
            $row['APP_MSG_STATUS'] = ucfirst ( $row['APP_MSG_STATUS']);
            $row['TAS_TITLE'] = $tasTitleDefault;
            if ($row['DEL_INDEX'] != 0) {
                $criteria = new Criteria();
                $criteria->addSelectColumn(AppDelegationPeer::PRO_UID);
                $criteria->addSelectColumn(AppDelegationPeer::TAS_UID);
                $criteria->addSelectColumn(AppDelegationPeer::DEL_INDEX);
                $criteria->add(AppDelegationPeer::APP_UID, $row['APP_UID']);
                $resultDelegation = AppDelegationPeer::doSelectRS($criteria);
                $resultDelegation->setFetchmode(ResultSet::FETCHMODE_ASSOC);
                $row['TAS_TITLE'] = '-';
                while ($resultDelegation->next()) {
                    $rowDelegation = $resultDelegation->getRow();
                    if ($row['DEL_INDEX'] == $rowDelegation['DEL_INDEX']) {
                        $index = $row['DEL_INDEX'];
                        $row['TAS_TITLE'] = $content->load( 'TAS_TITLE', '', $rowDelegation['TAS_UID'], SYS_LANG );
                        break;
                    }
                }
            }

            $criteria = new Criteria();
            $criteria->addSelectColumn(AppCacheViewPeer::APP_TITLE);
            $criteria->add(AppCacheViewPeer::APP_UID, $row['APP_UID']);
            $criteria->add(AppCacheViewPeer::DEL_INDEX, $index);
            $resultCacheView = AppCacheViewPeer::doSelectRS($criteria);
            $resultCacheView->setFetchmode(ResultSet::FETCHMODE_ASSOC);
            $row['APP_TITLE'] = '-';
            while ($resultCacheView->next()) {
                $rowCacheView = $resultCacheView->getRow();
                $row['APP_TITLE'] = $rowCacheView['APP_TITLE'];
            }
            $data[] = $row;
        }
        $response = array();
        $response['totalCount'] = $totalCount;
        $response['data']       = $data;
        die(G::json_encode($response));
        break;
    case 'updateStatusMessage':
        if (isset($_REQUEST['APP_MSG_UID']) && isset($_REQUEST['APP_MSG_STATUS'])) {
            $message = new AppMessage();
            $result = $message->updateStatus($_REQUEST['APP_MSG_UID'], $_REQUEST['APP_MSG_STATUS']);
        }
        break;
}

