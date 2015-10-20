<?php
namespace ProcessMaker\Services\Api;

use \ProcessMaker\Services\Api;
use \Luracast\Restler\RestException;

/**
 * Role Api Controller
 *
 * @protected
 */
class Role extends Api
{
    private $role;

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

            $this->role = new \ProcessMaker\BusinessModel\Role();

            $this->role->setFormatFieldNameInUppercase(false);
        } catch (\Exception $e) {
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
    }

    /**
     * @url GET
     */
    public function index($filter = null, $start = null, $limit = null)
    {
        try {
            $response = $this->role->getRoles(array("filter" => $filter), null, null, $start, $limit);

            return $response;
        } catch (\Exception $e) {
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
    }

    /**
     * @url GET /:rol_uid
     *
     * @param string $rol_uid {@min 32}{@max 32}
     */
    public function doGet($rol_uid)
    {
        try {
            $response = $this->role->getRole($rol_uid);

            return $response;
        } catch (\Exception $e) {
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
    }

    /**
     * @url POST
     *
     * @param array $request_data
     *
     * @status 201
     */
    public function doPost(array $request_data)
    {
        try {
            $arrayData = $this->role->create($request_data);

            $response = $arrayData;

            return $response;
        } catch (\Exception $e) {
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
    }

    /**
     * @url PUT /:rol_uid
     *
     * @param string $rol_uid      {@min 32}{@max 32}
     * @param array  $request_data
     */
    public function doPut($rol_uid, array $request_data)
    {
        try {
            $arrayData = $this->role->update($rol_uid, $request_data);
        } catch (\Exception $e) {
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
    }

    /**
     * @url DELETE /:rol_uid
     *
     * @param string $rol_uid {@min 32}{@max 32}
     */
    public function doDelete($rol_uid)
    {
        try {
            $this->role->delete($rol_uid);
        } catch (\Exception $e) {
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
    }
}

