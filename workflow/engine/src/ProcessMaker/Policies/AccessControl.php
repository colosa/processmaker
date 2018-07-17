<?php
namespace ProcessMaker\Policies;

use Luracast\Restler\iAuthenticate;
use Luracast\Restler\RestException;
use Luracast\Restler\Defaults;
use Luracast\Restler\Util;
use Luracast\Restler\Scope;
use OAuth2\Request;
use ProcessMaker\Services\OAuth2\Server;
use ProcessMaker\BusinessModel\User;
use RBAC;

class AccessControl implements iAuthenticate
{
    public static $role;
    public static $permission;
    public static $className;
    private $userUid = null;
    private $oUser;

    /**
     * @var RBAC $rbac
     */
    private $rbac;

    const SYSTEM = 'PROCESSMAKER';

    /**
     * This method checks if an endpoint permission or permissions access
     *
     * @return bool
     * @throws RestException
     */
    public function __isAllowed()
    {
        $response = true;
        $oServerOauth = new Server();
        $this->oUser = new User();
        $server = $oServerOauth->getServer();
        $request = Request::createFromGlobals();
        $allowed = $server->verifyResourceRequest($request);
        $this->userUid = $oServerOauth->getUserId();
        $this->oUser->loadUserRolePermission(self::SYSTEM, $this->userUid);
        $this->loadRbacUser($this->userUid);
        $metadata = Util::nestedValue($this->restler, 'apiMethodInfo', 'metadata');
        $permissions = $this->getPermissions();
        if ($allowed && !empty($this->userUid) && (!empty($metadata['access']) && $metadata['access'] == 'protected')) {
            $parameters = Util::nestedValue($this->restler, 'apiMethodInfo', 'parameters');
            if (!is_null(self::$className) && is_string(self::$className)) {
                $authObj = Scope::get(self::$className);
                $authObj->parameters = $parameters;
                $authObj->permission = $permissions;
                if (!method_exists($authObj, Defaults::$authenticationMethod)) {
                    throw new RestException (
                        500,
                        'Authentication Class should implement iAuthenticate');
                } elseif (!$authObj->{Defaults::$authenticationMethod}()) {
                    throw new RestException(403, "You don't have permission to access this endpoint or resource on this server.");
                }
            } elseif (!$this->verifyAccess($permissions)) {
                throw new RestException(401);
            }
        }
        return $response;
    }

    /**
     * @return string
     */
    public function __getWWWAuthenticateString()
    {
        return '';
    }

    /**
     * Verify the permissions required to access the endpoint.
     *
     * @param $permissions
     * @return bool
     */
    public function verifyAccess($permissions)
    {
        $response = false;
        $access = -1;
        if (!is_array($permissions)) {
            $access = $this->userCanAccess($permissions);
        } elseif (count($permissions) > 0) {
            foreach ($permissions as $perm) {
                $access = $this->userCanAccess($perm);
                if ($access == 1) {
                    break;
                }
            }
        }
        if ($access == 1 || empty($permissions)) {
            $response = true;
        }
        return $response;
    }

    /**
     * Verify if the user has a right over the permission.
     *
     * @param string $perm
     * @return int
     */
    public function userCanAccess($perm)
    {
        return $this->rbac->userCanAccess($perm);
    }

    /**
     * Get the required permission(s) of the endpoint.
     *
     * @return mixed
     */
    private function getPermissions()
    {
        if (is_string(self::$permission)) {
            $permission = trim(self::$permission);
        } elseif (is_array(self::$permission)) {
            $permission = [];
            foreach (self::$permission as $perm) {
                $permission[] = trim($perm);
            }
        } else {
            $permission = self::$permission;
        }
        return $permission;
    }

    /**
     * Load the RBAC object to validate the user permissions.
     *
     * @param string $userUid
     */
    private function loadRbacUser($userUid)
    {
        $this->rbac = new RBAC;
        $this->rbac->initRBAC();
        $this->rbac->loadUserRolePermission(self::SYSTEM, $userUid);
    }
}
