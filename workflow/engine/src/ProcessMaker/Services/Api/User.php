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
            $user = new \ProcessMaker\BusinessModel\User();
            $user->setFormatFieldNameInUppercase(false);

            $arrayFilterData = array(
                "filter"       => (!is_null($filter))? $filter : ((!is_null($lfilter))? $lfilter : ((!is_null($rfilter))? $rfilter : null)),
                "filterOption" => (!is_null($filter))? ""      : ((!is_null($lfilter))? "LEFT"   : ((!is_null($rfilter))? "RIGHT"  : ""))
            );

            $response = $user->getUsers($arrayFilterData, null, null, $start, $limit);

            return $response["data"];
        } catch (\Exception $e) {
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
    }

    /**
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
            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
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
     * @url PUT /:usr_uid
     *
     * @param string $usr_uid      {@min 32}{@max 32}
     * @param array  $request_data
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

