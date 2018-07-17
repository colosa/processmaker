<?php
namespace ProcessMaker\Services\Api;

use \ProcessMaker\Services\Api;
use \Luracast\Restler\RestException;

/**
 * Group Api Controller
 *
 * @protected
 */
class Group extends Api
{
    /**
     * Constructor of the class
     *
     * return void
     */
    public function __construct()
    {
        try {
            $user = new \ProcessMaker\BusinessModel\User();

            $usrUid = $this->getUserId();

            if (!$user->checkPermission($usrUid, "PM_USERS")) {
                throw new \Exception(\G::LoadTranslation("ID_USER_NOT_HAVE_PERMISSION", array($usrUid)));
            }
        } catch (\Exception $e) {
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
    }

    /**
     * @url GET
     */
    public function index($filter = null, $lfilter = null, $rfilter = null, $start = null, $limit = null)
    {
        try {
            $group = new \ProcessMaker\BusinessModel\Group();
            $group->setFormatFieldNameInUppercase(false);

            $arrayFilterData = array(
                "filter"       => (!is_null($filter))? $filter : ((!is_null($lfilter))? $lfilter : ((!is_null($rfilter))? $rfilter : null)),
                "filterOption" => (!is_null($filter))? ""      : ((!is_null($lfilter))? "LEFT"   : ((!is_null($rfilter))? "RIGHT"  : ""))
            );

            $response = $group->getGroups($arrayFilterData, null, null, $start, $limit);

            return $response["data"];
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @url GET /:grp_uid
     *
     * @param string $grp_uid {@min 32}{@max 32}
     */
    public function doGet($grp_uid)
    {
        try {
            $group = new \ProcessMaker\BusinessModel\Group();
            $group->setFormatFieldNameInUppercase(false);

            $response = $group->getGroup($grp_uid);

            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * Create a new group.
     * 
     * @url POST
     * @status 201
     * 
     * @param array $request_data
     * 
     * @return array
     * @throws RestException 
     * 
     * @access protected
     * @class AccessControl {@permission PM_USERS}
     */
    public function doPost($request_data)
    {
        try {
            $group = new \ProcessMaker\BusinessModel\Group();
            $group->setFormatFieldNameInUppercase(false);

            $arrayData = $group->create($request_data);

            $response = $arrayData;

            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * Update group.
     *
     * @url PUT /:grp_uid
     *
     * @param string $grp_uid      {@min 32}{@max 32}
     * @param array  $request_data
     *
     * @throws RestException
     *
     * @access protected
     * @class AccessControl {@permission PM_USERS}
     */
    public function doPut($grp_uid, $request_data)
    {
        try {
            $group = new \ProcessMaker\BusinessModel\Group();
            $group->setFormatFieldNameInUppercase(false);

            $arrayData = $group->update($grp_uid, $request_data);
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @url DELETE /:grp_uid
     * @access protected
     * @class AccessControl {@permission PM_USERS}
     *
     * @param string $grp_uid {@min 32}{@max 32}
     */
    public function doDelete($grp_uid)
    {
        try {
            $group = new \ProcessMaker\BusinessModel\Group();
            $group->setFormatFieldNameInUppercase(false);

            $group->delete($grp_uid);
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @url GET /:grp_uid/users
     *
     * @param string $grp_uid {@min 32}{@max 32}
     */
    public function doGetUsers($grp_uid, $filter = null, $start = null, $limit = null)
    {
        try {
            $group = new \ProcessMaker\BusinessModel\Group();
            $group->setFormatFieldNameInUppercase(false);

            $response = $group->getUsers("USERS", $grp_uid, array("filter" => $filter), null, null, $start, $limit);

            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @url GET /:grp_uid/available-users
     *
     * @param string $grp_uid {@min 32}{@max 32}
     */
    public function doGetAvailableUsers($grp_uid, $filter = null, $start = null, $limit = null)
    {
        try {
            $group = new \ProcessMaker\BusinessModel\Group();
            $group->setFormatFieldNameInUppercase(false);

            $response = $group->getUsers("AVAILABLE-USERS", $grp_uid, array("filter" => $filter), null, null, $start, $limit);

            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @url GET /:grp_uid/supervisor-users
     *
     * @param string $grp_uid {@min 32}{@max 32}
     */
    public function doGetSupervisorUsers($grp_uid, $filter = null, $start = null, $limit = null)
    {
        try {
            $group = new \ProcessMaker\BusinessModel\Group();
            $group->setFormatFieldNameInUppercase(false);

            $response = $group->getUsers("SUPERVISOR", $grp_uid, array("filter" => $filter), null, null, $start, $limit);

            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }
}

