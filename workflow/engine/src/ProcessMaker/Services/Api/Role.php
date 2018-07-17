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

    private $arrayFieldIso8601 = [
        "rol_create_date",
        "rol_update_date"
    ];

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
     * @access protected
     * @class  AccessControl {@permission PM_USERS}
     * @url GET
     */
    public function index($filter = null, $start = null, $limit = null)
    {
        try {
            $response = $this->role->getRoles(array("filter" => $filter), null, null, $start, $limit);

            return \ProcessMaker\Util\DateTime::convertUtcToIso8601($response, $this->arrayFieldIso8601);
        } catch (\Exception $e) {
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
    }

    /**
     * @access protected
     * @class  AccessControl {@permission PM_USERS}
     * @url GET /:rol_uid
     *
     * @param string $rol_uid {@min 32}{@max 32}
     */
    public function doGet($rol_uid)
    {
        try {
            $response = $this->role->getRole($rol_uid);

            return \ProcessMaker\Util\DateTime::convertUtcToIso8601($response, $this->arrayFieldIso8601);
        } catch (\Exception $e) {
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
    }

    /**
     * @access protected
     * @class  AccessControl {@permission PM_USERS}
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
     * Update a role.
     *
     * @url PUT /:rol_uid
     *
     * @param string $rol_uid      {@min 32}{@max 32}
     * @param array  $request_data
     *
     * @throws RestException
     *
     * @access protected
     * @class AccessControl {@permission PM_USERS}
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
     * @access protected
     * @class  AccessControl {@permission PM_USERS}
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

