<?php
namespace ProcessMaker\Services\Api;

use \ProcessMaker\Services\Api;
use \Luracast\Restler\RestException;
use \ProcessMaker\Util\DateTime;


/**
 * Dashboard Api Controller
 *
 * @author Jenny Murillo <jennylee@colosa.com>
 * @copyright Colosa - Bolivia
 *
 * @protected
 */
class Dashboard extends Api
{
    /**
     * Get dashboards UID by user_uid
     * 
     * @param string $usr_uid {@from path}
     *
     *
     * @url GET /owner/:usr_uid
     *
     */
    public function doGetDashboardsUidByUser($usr_uid)
    {
        try {
            $Dashboard = new \ProcessMaker\BusinessModel\Dashboard();
            $response = $Dashboard->getDashboardsUidByUser($usr_uid);
            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * Get dashboards data by user_uid
     *
     * @param string $usr_uid {@from path}
     *
     *
     * @url GET /ownerData/:usr_uid
     *
     */
    public function doGetDashboardsDataByUser($usr_uid)
    {
        try {
            $Dashboard = new \ProcessMaker\BusinessModel\Dashboard();
            $response = $Dashboard->getDashboardDataByUser($usr_uid);
            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * Get users by dashboards uid
     *
     * @param string $das_uid {@from path}
     *
     *
     * @url GET /users/:das_uid
     *
     */
    public function doGetDashboardUsers($das_uid)
    {
        try {
            $Dashboard = new \ProcessMaker\BusinessModel\Dashboard();
            $response = $Dashboard->getUsersOfDashboard($das_uid);
            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * Get dashboards data by uid
     *
     * @param string $das_uid {@from path}
     *
     *
     * @url GET /:das_uid
     *
     */
    public function doGetDashboardData($das_uid)
    {
        try {
            $Dashboard = new \ProcessMaker\BusinessModel\Dashboard();
            $response = $Dashboard->getDashboard($das_uid);
            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * Get dashboards indicator by dasInd_uid
     *
     * @param string $dasInd_uid {@from path}
     *
     *
     * @url GET /indicator/:dasInd_uid
     *
     */
    public function doGetDashboardIndicator($dasInd_uid)
    {
        try {
            $Dashboard = new \ProcessMaker\BusinessModel\Dashboard();
            $response = $Dashboard->getIndicator($dasInd_uid);
            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * Get dashboards indicators by dashboardUid
     *
     * @param string $das_uid {@from path}
     * @param string $dateIni {@from path}
     * @param string $dateFin {@from path}
     *
     *
     * @url GET /:das_uid/indicator
     *
     */
    public function doGetIndicatorsbyDasUid($das_uid, $dateIni="", $dateFin="")
    {
        try {
            if ($dateIni == "") {
                $dateTimezone = new \DateTime("now", new \DateTimeZone('UTC'));
                $dateIni = $dateTimezone->format('Y-m-d H:i:s');
            } else {
                $dateIni = $this->normalizedTimeZone($dateIni);
            }
            if ($dateFin == "") {
                $dateTimezone = new \DateTime("now", new \DateTimeZone('UTC'));
                $dateFin = $dateTimezone->format('Y-m-d H:i:s');
            } else {
                $dateFin = $this->normalizedTimeZone($dateFin);
            }

            $usrUid = $this->getUserId();
            $Dashboard = new \ProcessMaker\BusinessModel\Dashboard();
            $response = $Dashboard->getIndicatorsByDasUid($das_uid, $dateIni, $dateFin, $usrUid);
            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * Get list Dashboards
     *
     * @param int $start {@from path}
     * @param int $limit {@from path}
     * @param string $sort {@from path}
     * @param string $dir {@from path}
     * @param string $search {@from path}
     * @return array
     *
     *
     * @url GET
     */
    public function doGetListDashboards(
        $start = 0,
        $limit = 0,
        $sort = 'DASHBOARD.DAS_TITLE',
        $dir = 'DESC',
        $search = ''
    ) {
        try {
            $options['start'] = $start;
            $options['limit'] = $limit;
            $options['sort'] = $sort;
            $options['dir'] = $dir;
            $options['search'] = $search;
            $dashboard = new \ProcessMaker\BusinessModel\Dashboard();
            $response = $dashboard->getListDashboards($options);
            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * Get Owners by das_uid
     *
     * @param string $das_uid {@from path}
     * @param int $start {@from path}
     * @param int $limit {@from path}
     * @param string $search {@from path}
     *
     * @return array
     *
     *
     * @url GET /:das_uid/owners
     *
     */
    public function doGetOwnersByDasUid(
        $das_uid,
        $start = 0,
        $limit = 0,
        $search = '')
    {
        try {
            $options['das_uid'] = $das_uid;
            $options['start'] = $start;
            $options['limit'] = $limit;
            $options['search'] = $search;
            $dashboard = new \ProcessMaker\BusinessModel\Dashboard();
            $response = $dashboard->getOwnerByDasUid($options);
            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * Create dashboard.
     * 
     * @url POST
     * @status 201
     * 
     * @param array $request_data
     * 
     * @return integer
     * @throws RestException 
     * 
     * @access protected
     * @class AccessControl {@permission PM_DASHBOARD}
     */
    public function doPostDashboard($request_data)
    {
        try {
            $dashboard = new \ProcessMaker\BusinessModel\Dashboard();
            $request_data['USR_UID'] = $this->getUserId();
            $response = $dashboard->createDashboard($request_data);
            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * Put dashboards configuration
     *
     * @url PUT
     *
     * @param array $request_data
     *
     * @return string
     * @throws RestException
     *
     * @access protected
     * @class AccessControl {@permission PM_DASHBOARD}
     */
    public function doPutDashboard($request_data)
    {
        try {
            $dashboard = new \ProcessMaker\BusinessModel\Dashboard();
            $request_data['USR_UID'] = $this->getUserId();
            $response = $dashboard->createDashboard($request_data);
            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @url DELETE /:das_uid
     * @access protected
     * @class AccessControl {@permission PM_DASHBOARD}
     *
     * @param string $das_uid  {@min 32}{@max 32}
     *
     */
    public function doDeleteDashboard($das_uid)
    {
        try {
            $dashboard = new \ProcessMaker\BusinessModel\Dashboard();
            $request_data['USR_UID'] = $this->getUserId();
            $response = $dashboard->deletedashboard($das_uid, $this->getUserId());
            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * Create owner
     * 
     * @url POST /owner
     * @status 201
     * 
     * @param array $request_data
     * 
     * @return object
     * @throws RestException 
     * 
     * @access protected
     * @class AccessControl {@permission PM_DASHBOARD}
     */
    public function doPostOwner($request_data)
    {
        try {
            $dashboard = new \ProcessMaker\BusinessModel\Dashboard();
            $request_data['USR_UID'] = $this->getUserId();
            $response = $dashboard->createOwner($request_data);
            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @url DELETE /:das_uid/owner/:owner_uid
     * @access protected
     * @class AccessControl {@permission PM_DASHBOARD}
     *
     * @param string $das_uid  {@min 32}{@max 32}
     * @param string $owner_uid  {@min 32}{@max 32}
     *
     */
    public function doDeleteDashboardOwner($das_uid, $owner_uid)
    {
        try {
            $dashboard = new \ProcessMaker\BusinessModel\Dashboard();
            $response = $dashboard->deleteDashboardOwner($das_uid, $owner_uid, $this->getUserId());
            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * Create indicator.
     * 
     * @url POST /indicator
     * @status 201
     * 
     * @param array $request_data
     * 
     * @return string
     * @throws RestException 
     * 
     * @access protected
     * @class AccessControl {@permission PM_DASHBOARD}
     */
    public function doPostIndicator($request_data)
    {
        try {
            $dashboard = new \ProcessMaker\BusinessModel\Dashboard();
            $request_data['USR_UID'] = $this->getUserId();
            $response = $dashboard->createIndicator($request_data);
            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * Put Indicator
     *
     * @param array $request_data
     *
     * @return string
     * @throws RestException
     *
     * @class AccessControl {@permission PM_DASHBOARD}
     * @access protected
     */
    public function doPutIndicator($request_data)
    {
        try {
            $dashboard = new \ProcessMaker\BusinessModel\Dashboard();
            $request_data['USR_UID'] = $this->getUserId();
            $response = $dashboard->createIndicator($request_data);
            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @url DELETE /indicator/:ind_uid
     * @access protected
     * @class AccessControl {@permission PM_DASHBOARD}
     *
     * @param string $ind_uid  {@min 32}{@max 32}
     *
     */
    public function doDeleteIndicator($ind_uid)
    {
        try {
            $dashboard = new \ProcessMaker\BusinessModel\Dashboard();
            $response = $dashboard->delete($ind_uid, $this->getUserId());
            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * Post dashboards configuration by userUid
     *
     * @url POST /config/
     * 
     * @param array $request_data
     * 
     * @return integer
     * @throws RestException 
     * 
     * @access protected
     * @class AccessControl {@permission PM_DASHBOARD}
     */
    public function doPostDashboardConfigByUsrUid($request_data)
    {
    	try {
    		$usrUid = $this->getUserId();

    		$ConfigDashboards = new \ProcessMaker\BusinessModel\Dashboard();
    		$response = $ConfigDashboards->postConfigByUsr($request_data, $usrUid);
    		return $response;
    	} catch (\Exception $e) {
    		throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
    	}
    }

    /**
     * Get dashboards configuration by usr_uid
     *
     * @url GET /config/
     *
     */
    public function doGetDashboardConfigActualUsr()
    {
    	try {
    		$usrUid = $this->getUserId();

    		$Dashboard = new \ProcessMaker\BusinessModel\Dashboard();
    		$response = $Dashboard->getConfig($usrUid);
    		return $response;
    	} catch (\Exception $e) {
    		throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
    	}
    }

    /**
     * Put dashboards configuration by usr_uid
     *
     * @url PUT /config
     *
     * @param array $request_data
     *
     * @return array
     * @throws RestException
     *
     * @access protected
     * @class AccessControl {@permission PM_DASHBOARD}
     */
    public function doPutDashboardConfigByUsrUid($request_data)
    {
    	try {
    		$usrUid = $this->getUserId();

    		$ConfigDashboards = new \ProcessMaker\BusinessModel\Dashboard();
    		$response = $ConfigDashboards->putConfigByUsr($request_data, $usrUid);
    		return $response;
    	} catch (\Exception $e) {
    		throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
    	}
    }

    public function normalizedTimeZone($date)
    {
        $result = $date;
        $dateTimezone = new \DateTime($date, new \DateTimeZone('UTC'));
        $dateTimezone = DateTime::convertDataToUtc($dateTimezone);

        if (!(isset($_SESSION['__SYSTEM_UTC_TIME_ZONE__']) && $_SESSION['__SYSTEM_UTC_TIME_ZONE__'])) {
            $result = $dateTimezone->format('Y-m-d H:i:s');
        }
        else {
            $result = (new \DateTime($dateTimezone->date))->format('Y-m-d H:i:s');
        }
        return $result;
    }
}

