<?php
namespace ProcessMaker\Services\Api;

use \ProcessMaker\Services\Api;
use \Luracast\Restler\RestException;

/**
 * User Api Controller
 *
 * @protected
 */
class User extends Api
{
    private $arrayFieldIso8601 = [
        'usr_create_date',
        'usr_update_date'
    ];

    /**
     * @access protected
     * @class  AccessControl {@permission PM_USERS,PM_FACTORY}
     * @url GET
     */
    public function index($filter = null, $lfilter = null, $rfilter = null, $start = null, $limit = null, $status = null)
    {
        try {
            $user = new \ProcessMaker\BusinessModel\User();
            $user->setFormatFieldNameInUppercase(false);

            $arrayFilterData = array(
                "filter"       => (!is_null($filter))? $filter : ((!is_null($lfilter))? $lfilter : ((!is_null($rfilter))? $rfilter : null)),
                "filterOption" => (!is_null($filter))? ""      : ((!is_null($lfilter))? "LEFT"   : ((!is_null($rfilter))? "RIGHT"  : ""))
            );

            $response = $user->getUsers($arrayFilterData, null, null, $start, $limit, false, true, $status);

            return \ProcessMaker\Util\DateTime::convertUtcToIso8601($response['data'], $this->arrayFieldIso8601);
        } catch (\Exception $e) {
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
    }

    /**
     * @access protected
     * @class  AccessControl {@permission PM_USERS,PM_FACTORY}
     * @url GET /:usr_uid
     *
     * @param string $usr_uid {@min 32}{@max 32}
     */
    public function doGetUser($usr_uid)
    {
        try {
            $user = new \ProcessMaker\BusinessModel\User();
            $user->setFormatFieldNameInUppercase(false);

            $response = $user->getUser($usr_uid);

            return \ProcessMaker\Util\DateTime::convertUtcToIso8601($response, $this->arrayFieldIso8601);
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
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
    public function doPostUser($request_data)
    {
        try {
            $user = new \ProcessMaker\BusinessModel\User();
            $arrayData = $user->create($request_data);
            $response = $arrayData;
            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * Update a user.
     *
     * @url PUT /:usr_uid
     *
     * @param string $usr_uid      {@min 32}{@max 32}
     * @param array  $request_data
     *
     * @throws RestException
     *
     * @access protected
     * @class  AccessControl {@permission PM_USERS}
     */
    public function doPutUser($usr_uid, $request_data)
    {
        try {
            $userLoggedUid = $this->getUserId();
            $user = new \ProcessMaker\BusinessModel\User();
            $arrayData = $user->update($usr_uid, $request_data, $userLoggedUid);
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @access protected
     * @class  AccessControl {@permission PM_USERS}
     * @url DELETE /:usr_uid
     *
     * @param string $usr_uid {@min 32}{@max 32}
     */
    public function doDeleteUser($usr_uid)
    {
        try {
            $user = new \ProcessMaker\BusinessModel\User();
            $user->delete($usr_uid);
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @param string $usr_uid {@min 32} {@max 32}
     *
     * @access protected
     * @class  AccessControl {@permission PM_USERS}
     * @url POST /:usr_uid/image-upload
     */
    public function doPostUserImageUpload($usr_uid)
    {
        try {
            $user = new \ProcessMaker\BusinessModel\User();
            $user->uploadImage($usr_uid);
        } catch (\Exception $e) {
            //response
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
    }
}
