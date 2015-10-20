<?php
namespace ProcessMaker\Services\Api;

use \ProcessMaker\Services\Api;
use \Luracast\Restler\RestException;


/**
 * Department Api Controller
 *
 * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
 * @copyright Colosa - Bolivia
 *
 * @protected
 */
class Department extends Api
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
     *
     * @return array
     *
     */
    public function doGetDepartments()
    {
        try {
            $oDepartment = new \ProcessMaker\BusinessModel\Department();
            $response = $oDepartment->getDepartments();
            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @url GET /:dep_uid/assigned-user
     *
     * @param string $dep_uid {@min 1}{@max 32}
     *
     * @return array
     *
     */
    public function doGetAssignedUser($dep_uid)
    {
        try {
            $oDepartment = new \ProcessMaker\BusinessModel\Department();
            $response = $oDepartment->getAssignedUser($dep_uid);
            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @url GET /:dep_uid/available-user
     *
     * @param string $dep_uid {@min 1}{@max 32}
     * @param string $start   {@from path}
     * @param string $limit   {@from path}
     * @param string $search  {@from path}
     *
     * @return array
     *
     */
    public function doGetAvailableUser($dep_uid, $start = 0, $limit = 0, $search = '')
    {
        try {
            $oDepartment = new \ProcessMaker\BusinessModel\Department();
            $response = $oDepartment->getAvailableUser($dep_uid, $start, $limit, $search);
            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @url POST /:dep_uid/assign-user
     *
     * @param string $dep_uid      {@min 32}{@max 32}
     * @param array  $request_data
     *
     * @status 201
     *
     */
    public function doPostAssignUser($dep_uid, array $request_data)
    {
        try {
            $department = new \ProcessMaker\BusinessModel\Department();

            $arrayData = $department->assignUser($dep_uid, $request_data);
        } catch (\Exception $e) {
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
    }

    /**
     * @url PUT /:dep_uid/unassign-user/:usr_uid
     *
     * @param string $dep_uid {@min 1}{@max 32}
     * @param string $usr_uid {@min 1}{@max 32}
     *
     * @return array
     *
     */
    public function doPutUnassignUser($dep_uid, $usr_uid)
    {
        try {
            $oDepartment = new \ProcessMaker\BusinessModel\Department();
            $response = $oDepartment->unassignUser($dep_uid, $usr_uid);
            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @url PUT /:dep_uid/set-manager/:usr_uid
     *
     * @param string $dep_uid {@min 1}{@max 32}
     * @param string $usr_uid {@min 1}{@max 32}
     *
     * @return array
     *
     */
    public function doPutSetManager($dep_uid, $usr_uid)
    {
        try {
            $oDepartment = new \ProcessMaker\BusinessModel\Department();
            $response = $oDepartment->setManagerUser($dep_uid, $usr_uid);
            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @url GET /:dep_uid
     *
     * @param string $dep_uid {@min 1}{@max 32}
     *
     * @return array
     *
     */
    public function doGetDepartment($dep_uid)
    {
        try {
            $oDepartment = new \ProcessMaker\BusinessModel\Department();
            $response = $oDepartment->getDepartment($dep_uid);
            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @url POST
     *
     * @param array $request_data
     * @param string $dep_title {@from body} {@min 1}
     *
     * @return array
     *
     * @status 201
     *
     */
    public function doPost($request_data, $dep_title)
    {
        try {
            $oDepartment = new \ProcessMaker\BusinessModel\Department();
            $response = $oDepartment->saveDepartment($request_data);
            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @url PUT /:dep_uid
     *
     * @param string $dep_uid      {@min 1}{@max 32}
     * @param array  $request_data
     *
     */
    public function doPut($dep_uid, $request_data)
    {
        try {
            $request_data['dep_uid'] = $dep_uid;
            $oDepartment = new \ProcessMaker\BusinessModel\Department();
            $response = $oDepartment->saveDepartment($request_data, false);
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @url DELETE /:dep_uid
     *
     * @param string $dep_uid {@min 1}{@max 32}
     *
     * @return array
     *
     */
    public function doDelete($dep_uid)
    {
        try {
            $oDepartment = new \ProcessMaker\BusinessModel\Department();
            $oDepartment->deleteDepartment($dep_uid);
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }
}

