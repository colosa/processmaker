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

            $this->category = new \ProcessMaker\BusinessModel\ProcessCategory();

            $this->category->setFormatFieldNameInUppercase(false);
        } catch (\Exception $e) {
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
    }

    /**
     * 
     * @access protected
     * @class  AccessControl {@permission PM_SETUP_PROCESS_CATEGORIES}
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
     * 
     * @access protected
     * @class  AccessControl {@permission PM_SETUP_PROCESS_CATEGORIES}
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
     * 
     * @access protected
     * @class  AccessControl {@permission PM_SETUP_PROCESS_CATEGORIES}
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
     * Update category.
     *
     * @url PUT /category/:cat_uid
     *
     * @param string $cat_uid {@min 32}{@max 32}
     * @param array  $request_data
     *
     * @throws RestException
     *
     * @access protected
     * @class AccessControl {@permission PM_SETUP_PROCESS_CATEGORIES}
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
     * 
     * @access protected
     * @class  AccessControl {@permission PM_SETUP_PROCESS_CATEGORIES}
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

