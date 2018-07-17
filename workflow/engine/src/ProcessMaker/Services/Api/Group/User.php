<?php
namespace ProcessMaker\Services\Api\Group;

use \ProcessMaker\Services\Api;
use \Luracast\Restler\RestException;

/**
 * Group\User Api Controller
 *
 * @protected
 */
class User extends Api
{
    /**
     * Assign a user to a specified group.
     * 
     * @url POST /:grp_uid/user
     * @status 201
     * 
     * @param string $grp_uid      {@min 32}{@max 32}
     * @param array  $request_data
     * 
     * @return array
     * @throws RestException 
     * 
     * @access protected
     * @class AccessControl {@permission PM_USERS}
     */
    public function doPostUser($grp_uid, $request_data)
    {
        try {
            $groupUser = new \ProcessMaker\BusinessModel\Group\User();
            $groupUser->setFormatFieldNameInUppercase(false);

            $arrayData = $groupUser->create($grp_uid, $request_data);
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * Assign a group of users to a specified group or groups.
     * 
     * @url POST /batch-users
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
    public function doPostBatchUsers($request_data)
    {
        try {
            $user = new \ProcessMaker\BusinessModel\User();

            $usrUid = $this->getUserId();

            if (!$user->checkPermission($usrUid, 'PM_USERS')) {
                throw new \Exception(\G::LoadTranslation('ID_USER_NOT_HAVE_PERMISSION', array($usrUid)));
            }

            $groupUser = new \ProcessMaker\BusinessModel\Group\User();
            $groupUser->setFormatFieldNameInUppercase(false);

            $arrayData = $groupUser->createBatch($request_data);

            //Return
            return $arrayData;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @url DELETE /:grp_uid/user/:usr_uid
     * @access protected
     * @class AccessControl {@permission PM_USERS}
     *
     * @param string $grp_uid {@min 32}{@max 32}
     * @param string $usr_uid {@min 32}{@max 32}
     */
    public function doDeleteUser($grp_uid, $usr_uid)
    {
        try {
            $groupUser = new \ProcessMaker\BusinessModel\Group\User();
            $groupUser->setFormatFieldNameInUppercase(false);

            $groupUser->delete($grp_uid, $usr_uid);
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }
}

