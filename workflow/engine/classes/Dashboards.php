<?php


class Dashboards
{
	public function getListDashboards ($start=0, $limit=20, $sort='', $dir='DESC', $search='')
    {

        $limit_size = isset($limit) ? $limit: 20;
        $start = isset($_REQUEST['start']) ? $_REQUEST['start'] : 0;
        $limit = isset($_REQUEST['limit']) ? $_REQUEST['limit'] : $limit_size;
        $sort = isset($_REQUEST['sort']) ? $_REQUEST['sort'] : '';
        $dir = isset($_REQUEST['dir']) ? $_REQUEST['dir'] : 'ASC';
        
        $criteria = new Criteria('workflow');
        $criteria->addSelectColumn('COUNT(*) AS CNT');
        $criteria->add(DashboardPeer::DAS_STATUS, array('2'), Criteria::NOT_IN);

        $dataset = DashboardPeer::DoSelectRs($criteria);
        $dataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        $dataset->next();
        $row = $dataset->getRow();
        $totalRows = $row['CNT'];

        $criteria->clearSelectColumns();
        $criteria->addSelectColumn(DashboardPeer::DAS_UID);
        $criteria->addSelectColumn(DashboardPeer::DAS_TITLE);
        $criteria->addSelectColumn(DashboardPeer::DAS_DESCRIPTION);
        $criteria->addSelectColumn(DashboardPeer::DAS_UPDATE_DATE);
        $criteria->addSelectColumn(DashboardPeer::DAS_STATUS);

        $criteria->add(DashboardPeer::DAS_STATUS, array('2'), Criteria::NOT_IN);

        if ($sort != '') {
            if ($dir == 'ASC') {
                $criteria->addAscendingOrderByColumn($sort);
            } else {
                $criteria->addDescendingOrderByColumn($sort);
            }
        }
        $criteria->setOffset($start);
        $criteria->setLimit($limit);
        $dataset = DashboardPeer::DoSelectRs($criteria);
        $dataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);

        $rows = Array();
        $user = new Users();
        $group = new Groupwf();
        $owner = new DashboardDasInd();
        while ($dataset->next()) {
            $row = $dataset->getRow();
            $row['DAS_OWNER'] = '';
            try {
                $ownerDetail = $owner->loadByDashboards($row['DAS_UID']);
                foreach ($ownerDetail as $key => $value) {
                    $title = '';
                    $detail = '';
                    if ($value['OWNER_TYPE'] == 'USER') {
                        $detail = $user->load($value['OWNER_UID']);
                        $title = $detail['USR_FIRSTNAME'] . ' '. $detail['USR_LASTNAME'];
                    } else if ($value['OWNER_TYPE'] == 'GROUP') {
                        $detail = $group->load($value['OWNER_UID']);
                        $title = $detail['GRP_TITLE'];
                    }
                    $row['DAS_OWNER'] .= ($row['DAS_OWNER'] == '') ? $title : ', ' . $title;
                }

            } catch (exception $oError) {
                //
            }

            $row['DAS_LABEL_STATUS'] = ($row['DAS_STATUS'] == 1) ? G::loadTranslation('ID_ACTIVE') : G::loadTranslation('ID_INACTIVE');

            $rows[] = $row;
        }
        $response = Array();
        $response['totalCount'] = $totalRows;
        $response['start'] = $start;
        $response['limit'] = $limit;
        $response['sort']  = G::toLower($sort);
        $response['dir']   = G::toLower($dir);
        $response['data'] = $rows;

        return $response;
    }


    public function getOwnerByDasUid ($das_uid='', $start=0, $limit=20, $search='')
    {
    	require_once 'classes/model/Users.php';
    	require_once 'classes/model/Groupwf.php';
    	require_once 'classes/model/DashboardDasInd.php';
    
    	$das_uid = isset($_REQUEST['das_uid']) ? $_REQUEST['das_uid'] : $das_uid;
    	$start = isset($_REQUEST['start']) ? $_REQUEST['start'] : $start;
    	$limit = isset($_REQUEST['limit']) ? $_REQUEST['limit'] : $limit;
    	$search = isset($_REQUEST['search']) ? $_REQUEST['search'] : $search;
    
    	$criteria = new Criteria('workflow');
    	$criteria->addSelectColumn('COUNT(*) AS TOTAL');
    	$criteria->add(DashboardDasIndPeer::DAS_UID, $das_uid, Criteria::EQUAL);
    
    	$dataset = DashboardDasIndPeer::DoSelectRs($criteria);
    	$dataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
    	$dataset->next();
    	$row = $dataset->getRow();
    	$totalRows = $row['TOTAL'];
    
    	$criteria->clearSelectColumns();
    	$criteria->add(DashboardDasIndPeer::DAS_UID, $das_uid, Criteria::EQUAL);
    
    	$criteria->setOffset($start);
    	$criteria->setLimit($limit);
    	$dataset = DashboardDasIndPeer::DoSelectRs($criteria);
    	$dataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
    
    	$rows = Array();
    	$user = new Users();
    	$group = new Groupwf();
    	while ($dataset->next()) {
    		$row = $dataset->getRow();
    		$row['OWNER_LABEL'] = '---';
    		try {
    			if ($row['OWNER_TYPE'] == 'USER') {
    				$detail = $user->load($row['OWNER_UID']);
    				$row['OWNER_LABEL'] = $detail['USR_FIRSTNAME'] . ' '. $detail['USR_LASTNAME'];
    			} else if ($row['OWNER_TYPE'] == 'GROUP') {
    				$detail = $group->load($row['OWNER_UID']);
    				$row['OWNER_LABEL'] = $detail['GRP_TITLE'];
    			}
    		} catch (exception $oError) {
    			//
    		}
    		$rows[] = $row;
    	}
    	$response = Array();
    	$response['totalCount'] = $totalRows;
    	$response['start'] = $start;
    	$response['limit'] = $limit;
    	$response['data'] = $rows;
    
    	return $response;
    }

}
