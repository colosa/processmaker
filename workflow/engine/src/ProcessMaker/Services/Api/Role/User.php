<?php
namespace ProcessMaker\Services\Api\Role;

use \ProcessMaker\Services\Api;
use \Luracast\Restler\RestException;

/**
 * Role\User Api Controller
 *
 * @protected
 */
class User extends Api
{
    private $roleUser;

    /**
     * Constructor of the class
     *
     * return void
     */
    public function __construct()
    {
        try {
            $this->roleUser = new \ProcessMaker\BusinessModel\Role\User();

            $this->roleUser->setFormatFieldNameInUppercase(false);
        } catch (\Exception $e) {
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
    }

    /**
     * @access protected
     * @class  AccessControl {@permission PM_USERS}
     * @url GET /:rol_uid/users
     * @url GET /:rol_uid/available-users
     *
     * @param string $rol_uid {@min 32}{@max 32}
     */
    public function doGetUsers($rol_uid, $filter = null, $start = null, $limit = null)
    {
        try {
            $option = (preg_match('/^.*\/users$/', $this->restler->url))? 'USERS' : 'AVAILABLE-USERS';

            $response = $this->roleUser->getUsers(
                $rol_uid, $option, ['filter' => $filter, 'filterOption' => ''], null, null, $start, $limit
            );

            return $response['data'];
        } catch (\Exception $e) {
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
    }

    /**
     * @access protected
     * @class  AccessControl {@permission PM_USERS}
     * @url POST /:rol_uid/user
     *
     * @param string $rol_uid      {@min 32}{@max 32}
     * @param array  $request_data
     *
     * @status 201
     */
    public function doPostUser($rol_uid, array $request_data)
    {
        try {
            $arrayData = $this->roleUser->create($rol_uid, $request_data);
        } catch (\Exception $e) {
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
    }

    /**
     * @url DELETE /:rol_uid/user/:usr_uid
     * @access protected
     * @class  AccessControl {@permission PM_USERS}
     *
     * @param string $rol_uid {@min 32}{@max 32}
     * @param string $usr_uid {@min 32}{@max 32}
     */
    public function doDeleteUser($rol_uid, $usr_uid)
    {
        try {
            $this->roleUser->delete($rol_uid, $usr_uid);
        } catch (\Exception $e) {
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
    }
}

