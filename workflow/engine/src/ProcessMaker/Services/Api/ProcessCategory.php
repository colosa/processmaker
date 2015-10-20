<?php
namespace ProcessMaker\Services\Api;

use \ProcessMaker\Services\Api;
use \Luracast\Restler\RestException;

/**
 * ProcessCategory Api Controller
 *
 * @protected
 */
class ProcessCategory extends Api
{
    private $category;

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

            if (!$user->checkPermission($usrUid, "PM_SETUP")) {
                throw new \Exception(\G::LoadTranslation("ID_USER_NOT_HAVE_PERMISSION", array($usrUid)));
            }

            $this->category = new \ProcessMaker\BusinessModel\ProcessCategory();

            $this->category->setFormatFieldNameInUppercase(false);
        } catch (\Exception $e) {
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
    }

    /**
     * @url GET /categories
     */
    public function doGetCategories($filter = null, $start = null, $limit = null)
    {
        try {
            $response = $this->category->getCategories(array("filter" => $filter), null, null, $start, $limit);

            return $response;
        } catch (\Exception $e) {
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
    }

    /**
     * @url GET /category/:cat_uid
     *
     * @param string $cat_uid {@min 32}{@max 32}
     */
    public function doGetCategory($cat_uid)
    {
        try {
            $response = $this->category->getCategory($cat_uid);

            return $response;
        } catch (\Exception $e) {
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
    }

    /**
     * @url POST /category
     *
     * @param array $request_data
     *
     * @status 201
     */
    public function doPostCategory(array $request_data)
    {
        try {
            $arrayData = $this->category->create($request_data);

            $response = $arrayData;

            return $response;
        } catch (\Exception $e) {
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
    }

    /**
     * @url PUT /category/:cat_uid
     *
     * @param string $cat_uid      {@min 32}{@max 32}
     * @param array  $request_data
     */
    public function doPutCategory($cat_uid, array $request_data)
    {
        try {
            $arrayData = $this->category->update($cat_uid, $request_data);
        } catch (\Exception $e) {
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
    }

    /**
     * @url DELETE /category/:cat_uid
     *
     * @param string $cat_uid {@min 32}{@max 32}
     */
    public function doDeleteCategory($cat_uid)
    {
        try {
            $this->category->delete($cat_uid);
        } catch (\Exception $e) {
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
    }
}

