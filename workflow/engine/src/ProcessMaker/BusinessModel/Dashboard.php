<?php
namespace ProcessMaker\BusinessModel;

use \G;

/**
 * @author Jenny Murillo <jennylee@colosa.com>
 * @copyright Colosa - Bolivia
 */
class Dashboard {

	/**
	 * Get DashboardUid by UserUid
	 *
	 * @param string $usr_uid Unique id of User
	 *
	 * return uid
	 * 
	 * @author Jenny Murillo <jennylee@colosa.com>
	 */
    public function getDashboardsUidByUser($usr_uid)
    {
    	require_once (PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "DashboardDasInd.php");
    	$oDashboardDasInd = new \DashboardDasInd();

    	$response = $oDashboardDasInd->loadOwnerByUserId($usr_uid);
        return $response;
    }

    /**
     * Get Dashboard Data by UserUid
     *
     * @param string $usr_uid Unique id of User
     *
     * return uid
     *
     * @author Jenny Murillo <jennylee@colosa.com>
     */
    public function getDashboardDataByUser($usr_uid)
    {
        $resp = array();
        $dashboards = $this->getDashboardsUidByUser($usr_uid);
        $existFavorite = false;
		$cont = 0;
        foreach($dashboards as $i=>$x) {
            //$resp[$i] = $this->getDashboard($x['DAS_UID']);
            $dashboardUser = $this->getDashboard($x['DAS_UID']);
            if ($dashboardUser['DAS_STATUS'] == 0) {
                continue;
            }
            $resp[$cont] = $dashboardUser;
            $Dashboard = new \ProcessMaker\BusinessModel\Dashboard();
            $dashConfig = $Dashboard->getConfig($usr_uid);
            $resp[$cont]['DAS_FAVORITE'] = 0;
            foreach ($dashConfig as $dashId=>$dashData) {
                if($dashId == $x['DAS_UID'] ) {
                    $resp[$cont]['DAS_FAVORITE'] = $dashData['dashFavorite'];
                    if ($dashData['dashFavorite']==1) {
                        $existFavorite = true;
                    }
                }
            }
			$cont++;
        }
        //if no favorite is set, the default favorite is the first one
        if ($existFavorite == false && $resp != null &&  sizeof($resp)>0) {
            $resp[0]['DAS_FAVORITE'] = 1;
        }
        return $resp;
    }

    /**
     * Get Users of a dashboard
     *
     * @param string $das_uid Unique id of the Dashboard
     *
     * return uid
     *
     * @author Jenny Murillo <jennylee@colosa.com>
     */
    public function getUsersOfDashboard($das_uid)
    {
    	require_once (PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "DashboardDasInd.php");
    	$oDashboardDasInd = new \DashboardDasInd();
    
    	$response = $oDashboardDasInd->loadByDashboards($das_uid);
    	return $response;
    }

    /**
     * Get dashboard data
     *
     * @param string $das_uid Unique id of the Dashboard
     *
     * return uid
     *
     * @author Jenny Murillo <jennylee@colosa.com>
     */
    public function getDashboard($das_uid)
    {
    	$oDashboard = new \Dashboard();
    	$response = $oDashboard->load($das_uid);
    	return $response;
    }

    /**
     * Get dashboard indicators
     *
     * @param string $dasInd_uid Unique id of the Dashboard indicator
     *
     * return id
     *
     * @author Jenny Murillo <jennylee@colosa.com>
     */
    public function getIndicator($dasInd_uid)
    {
    	require_once (PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "DashboardIndicator.php");
    	$oDashboardIndicator = new \DashboardIndicator();
    	 
    	$response = $oDashboardIndicator->load($dasInd_uid);
    	return $response;
    }
    
    
    /**
     * Get dashboard indicators by das_uid
     *
     * @param string $das_uid Unique id of the Dashboard
     * @param string $dateIni
     * @param string $dateFin
     * @param string $usrUid
     *
     * return uid
     *
     * @author Jenny Murillo <jennylee@colosa.com>
     */
    public function getIndicatorsByDasUid($das_uid, $dateIni, $dateFin, $usrUid)
    {
    	$oDashboardIndicator = new \DashboardIndicator();

    	$response = $oDashboardIndicator->loadbyDasUid($das_uid, $dateIni, $dateFin, $usrUid);
    	return $response;
    }
 
    /**
     * Get list All dashboards
     *
     * @access public
     * @param array $options, Data for list
     * @return array
     *
     * @author Marco Antonio Nina <marco.antonio.nina@colosa.com>
     * @copyright Colosa - Bolivia
     */
    public function getListDashboards($options = array())
    {
        Validator::isArray($options, '$options');

        $dir = isset( $options["dir"] ) ? $options["dir"] : "DESC";
        $sort = isset( $options["sort"] ) ? $options["sort"] : "DASHBOARD.DAS_TITLE";
        $start = isset( $options["start"] ) ? $options["start"] : "0";
        $limit = isset( $options["limit"] ) ? $options["limit"] : "";
        $search = isset( $options["search"] ) ? $options["search"] : "";
        $paged = isset( $options["paged"] ) ? $options["paged"] : true;
        $type = "extjs";

        $start = (int)$start;
        $start = abs($start);
        if ($start != 0) {
            $start--;
        }
        $limit = (int)$limit;
        $limit = abs($limit);
        if ($limit == 0) {
            $conf = new \Configurations();
            $configList = $conf->getConfiguration('ENVIRONMENT_SETTINGS', '');
            if (isset($configList['casesListRowNumber'])) {
                $limit = (int)$configList['casesListRowNumber'];
            } else {
                $limit = 25;
            }
        } else {
            $limit = (int)$limit;
        }

        if ($sort != 'DASHBOARD.DAS_TITLE') {
            $sort = G::toUpper($sort);
            $columnsAppCacheView = DashboardPeer::getFieldNames(\BasePeer::TYPE_FIELDNAME);
            if (!(in_array($sort, $columnsAppCacheView))) {
                $sort = 'APP_CACHE_VIEW.APP_NUMBER';
            }
        }
        $dir = G::toUpper($dir);
        if (!($dir == 'DESC' || $dir == 'ASC')) {
            $dir = 'DESC';
        }

        $dashboards = new \Dashboards();
        $result = $dashboards->getListDashboards($start, $limit, $sort, $dir, $search);

        if ($paged == false) {
            $response = $result['data'];
        } else {
            $response['total'] = $result['totalCount'];
            $response['start'] = $start+1;
            $response['limit'] = $limit;
            $response['sort']  = G::toLower($sort);
            $response['dir']   = G::toLower($dir);
            $response['search']   = $search;

            $response['data'] = $result['data'];
        }
        return $response;
    }

    /**
     * Get list All owners of dashboards
     *
     * @access public
     * @param array $options, Data for list
     * @return array
     *
     * @author Marco Antonio Nina <marco.antonio.nina@colosa.com>
     * @copyright Colosa - Bolivia
     */
    public function getOwnerByDasUid($options = array())
    {
        Validator::isArray($options, '$options');

        $das_uid = isset( $options["das_uid"] ) ? $options["das_uid"] : "";
        $start = isset( $options["start"] ) ? $options["start"] : "0";
        $limit = isset( $options["limit"] ) ? $options["limit"] : "";
        $search = isset( $options["search"] ) ? $options["search"] : "";
        $paged = isset( $options["paged"] ) ? $options["paged"] : true;
        $type = "extjs";

        $start = (int)$start;
        $start = abs($start);
        if ($start != 0) {
            $start--;
        }
        $limit = (int)$limit;
        $limit = abs($limit);
        if ($limit == 0) {
            $conf = new \Configurations();
            $configList = $conf->getConfiguration('ENVIRONMENT_SETTINGS', '');
            if (isset($configList['casesListRowNumber'])) {
                $limit = (int)$configList['casesListRowNumber'];
            } else {
                $limit = 25;
            }
        } else {
            $limit = (int)$limit;
        }

        $dashboards = new \Dashboards();
        $result = $dashboards->getOwnerByDasUid($das_uid, $start, $limit, $search);


        if ($paged == false) {
            $response = $result['data'];
        } else {
            $response['totalCount'] = $result['totalCount'];
            $response['start'] = $start+1;
            $response['limit'] = $limit;
            $response['search']   = $search;

            $response['owner'] = $result['data'];
        }
        return $response;
    }   

    /**
     * Create Dashboard 
     *
     * @param array $arrayData Data
     *
     * return id new Dashboard created
     * 
     * @author Marco Antonio Nina <marco.antonio.nina@colosa.com>
     */
    public function createDashboard($arrayData)
    {
        $dashboard = new \Dashboard();
        $response = $dashboard->createOrUpdate($arrayData);
        return $response;
    }

    /**
     * Delete Dashboard
     *
     * @param string $das_uid  Unique id
     * @param string $usr_uid
     *
     * return void
     * 
     * @author Marco Antonio Nina <marco.antonio.nina@colosa.com>
     */
    public function deletedashboard($das_uid, $usr_uid)
    {
        $dashboard = new \Dashboard();
        $response = $dashboard->remove($das_uid, $usr_uid);
        return $response;
    }

    /**
     * Create Dashboard Owner
     *
     * @param array $arrayData Data
     *
     * return id new Owner created
     * 
     * @author Marco Antonio Nina <marco.antonio.nina@colosa.com>
     */
    public function createOwner($arrayData)
    {
        $dashboard = new \DashboardDasInd();
        $response = $dashboard->create($arrayData);
        return $response;
    }

    /**
     * Delete Dashboard owner
     *
     * @param string $das_uid  
     * @param string $owner_uid
     * * @param string $usr_uid
     *
     * return void
     * 
     * @author Marco Antonio Nina <marco.antonio.nina@colosa.com>
     */
    public function deleteDashboardOwner($das_uid, $owner_uid, $usr_uid)
    {
        $dashboard = new \DashboardDasInd();
        $response = $dashboard->remove($das_uid, $owner_uid, $usr_uid);
        return $response;
    }

    /**
     * Create Dashboard Indicator
     *
     * @param array $arrayData Data
     *
     * return id new Indicator created
     * 
     * @author Marco Antonio Nina <marco.antonio.nina@colosa.com>
     */
    public function createIndicator($arrayData)
    {
        $dashboard = new \DashboardIndicator();
        $response = $dashboard->createOrUpdate($arrayData);
        return $response;
    }

    /**
     * Delete Indicator
     *
     * @param string $das_ind_uid  Unique id
     * @param string $usr_uid
     *
     * return void
     * 
     * @author Marco Antonio Nina <marco.antonio.nina@colosa.com>
     */
    public function delete($das_ind_uid, $usr_uid)
    {
        $dashboard = new \DashboardIndicator();
        $response = $dashboard->remove($das_ind_uid, $usr_uid);
        return $response;
    }
    
    /**
     * Post Dashboards User Configuration
     *
     * @param array $arrayData Data
     * @param string $usrUid
     *
     * return array Return data of the user configuration
     *
     * @author Jenny Murillo <jennylee@colosa.com>
     */
    public function postConfigByUsr($arrayData, $usrUid)
    {
    	$cnfgData[$arrayData['dashId']] = $arrayData;
    	
    	$data['CFG_UID'] = 'DASHBOARDS_SETTINGS';
    	$data['OBJ_UID'] = '';
    	$data['CFG_VALUE'] = serialize($cnfgData);
    	$data['USR_UID'] = $usrUid;
    	$data['PRO_UID'] = "";
    	$data['APP_UID'] = "";

    	$oConfig = new \Configuration();
    
    	$response = $oConfig->create($data);
    	return $response;
    }
    
    /**
     * Get Dashboard configuration by UserUid
     *
     * @param string $usr_uid Unique id of User
     *
     * return array
     *
     * @author Jenny Murillo <jennylee@colosa.com>
     */
    public function getConfig($usr_uid)
    {
    	$oConfig = new \Configuration();
    
    	$response = array();
    	if($oConfig->exists('DASHBOARDS_SETTINGS', '', '', $usr_uid, '') == true){
    		$data = $oConfig->load('DASHBOARDS_SETTINGS', '', '', $usr_uid, '');
    		$response = unserialize($data['CFG_VALUE']);
    	}
    
    	return $response;
    }
    
    /**
     * Put Dashboard configuration by UserUid
     *
     * @param array $arrayData Data
     * @param string $usrUid
     *
     * return array
     *
     * @author Jenny Murillo <jennylee@colosa.com>
     */
    public function putConfigByUsr($arrayData, $usrUid)
    {
    	$oConfig = new \Configuration();
    	
    	$cnfgData = array();
    	if($oConfig->exists('DASHBOARDS_SETTINGS', '', '', $usrUid, '') == true){
    		$data = $oConfig->load('DASHBOARDS_SETTINGS', '', '', $usrUid, '');
    		$cnfgData = unserialize($data['CFG_VALUE']);
    	}

    	if($arrayData['dashData']==""){
    		foreach($cnfgData as $dashId=>$dashData) {
    			$cnfgData[$dashData['dashId']]['dashFavorite'] = 0;
    		}
    		$cnfgData[$arrayData['dashId']]['dashId'] = $arrayData['dashId'];
    		$cnfgData[$arrayData['dashId']]['dashFavorite'] = $arrayData['dashFavorite'];
    		$cnfgData[$arrayData['dashId']]['dashData'] = $arrayData['dashData'];
    	} else{
    		$cnfgData[$arrayData['dashId']] = $arrayData;
    	}

    	$data['CFG_UID'] = 'DASHBOARDS_SETTINGS';
    	$data['OBJ_UID'] = '';
    	$data['CFG_VALUE'] = serialize($cnfgData);
    	$data['USR_UID'] = $usrUid;
    	$data['PRO_UID'] = "";
    	$data['APP_UID'] = "";

    	$response = $oConfig->update($data);
    	return $response;
    }
}

