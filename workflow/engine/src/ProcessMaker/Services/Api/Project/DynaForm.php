<?php
namespace ProcessMaker\Services\Api\Project;

use ProcessMaker\Services\Api;
use Luracast\Restler\RestException;
use ProcessMaker\Util\DateTime;
use PmDynaform;

/**
 * Project\DynaForm Api Controller
 *
 * @protected
 */
class DynaForm extends Api
{
    private $arrayFieldIso8601 = [
        "dyn_update_date"
    ];

    /**
     * @url GET /:prj_uid/dynaform/:dyn_uid
     *
     * @param string $dyn_uid {@min 32}{@max 32}
     * @param string $prj_uid {@min 32}{@max 32}
     */
    public function doGetDynaForm($dyn_uid, $prj_uid)
    {
        try {
            $dynaForm = new \ProcessMaker\BusinessModel\DynaForm();
            $dynaForm->setFormatFieldNameInUppercase(false);

            $response = $dynaForm->getDynaForm($dyn_uid);

            return DateTime::convertUtcToIso8601($response, $this->arrayFieldIso8601);
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }
    
    /**
     * Create Dynaform.
     * 
     * @url POST /:prj_uid/dynaform
     * @status 201
     * 
     * @param string $prj_uid      {@min 32}{@max 32}
     * @param array  $request_data
     * 
     * @return array
     * @throws RestException 
     * 
     * @access protected
     * @class AccessControl {@permission PM_FACTORY}
     */
    public function doPostDynaForm($prj_uid, $request_data)
    {
        try {
            $dynaForm = new \ProcessMaker\BusinessModel\DynaForm();
            $dynaForm->setFormatFieldNameInUppercase(false);
            $dynaForm->setArrayFieldNameForException(array("processUid" => "prj_uid"));

            $arrayData = $dynaForm->executeCreate($prj_uid, $request_data);

            $response = $arrayData;

            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * Update dynaform.
     *
     * @url PUT /:prj_uid/dynaform/:dyn_uid
     *
     * @param string $dyn_uid      {@min 32}{@max 32}
     * @param string $prj_uid      {@min 32}{@max 32}
     * @param array  $request_data
     *
     * @throws RestException
     *
     * @access protected
     * @class AccessControl {@permission PM_FACTORY}
     */
    public function doPutDynaForm($dyn_uid, $prj_uid, $request_data)
    {
        try {
            $dynaForm = new \ProcessMaker\BusinessModel\DynaForm();
            $dynaForm->setFormatFieldNameInUppercase(false);

            $arrayData = $dynaForm->update($dyn_uid, $request_data);
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @url DELETE /:prj_uid/dynaform/:dyn_uid
     * @access protected
     * @class AccessControl {@permission PM_FACTORY}
     *
     * @param string $dyn_uid {@min 32}{@max 32}
     * @param string $prj_uid {@min 32}{@max 32}
     */
    public function doDeleteDynaForm($dyn_uid, $prj_uid)
    {
        try {
            $dynaForm = new \ProcessMaker\BusinessModel\DynaForm();
            $dynaForm->setFormatFieldNameInUppercase(false);

            $dynaForm->delete($dyn_uid);
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @url GET /:prj_uid/dynaform/:dyn_uid/fields
     *
     * @param string $dyn_uid {@min 32}{@max 32}
     * @param string $prj_uid {@min 32}{@max 32}
     */
    public function doGetDynaFormFields($dyn_uid, $prj_uid)
    {
        try {
            $dynaForm = new \ProcessMaker\BusinessModel\DynaForm();
            $dynaForm->setFormatFieldNameInUppercase(false);

            $response = $dynaForm->getDynaFormFields($prj_uid, $dyn_uid);

            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }
    
    /**
     * @url GET /:prj_uid/dynaform/:dyn_uid/download-language/:lang
     *
     * @param string $dyn_uid {@min 32}{@max 32}
     * @param string $prj_uid {@min 32}{@max 32}
     */
    public function doGetDynaFormLanguage($dyn_uid, $prj_uid, $lang)
    {
        try {

            $pmDynaform = new PmDynaform();
            return $pmDynaform->downloadLanguage($dyn_uid, $lang);
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * Upload language for a Dynaform.
     * 
     * @url POST /:prj_uid/dynaform/:dyn_uid/upload-language
     *
     * @param string $dyn_uid {@min 32}{@max 32}
     * @param string $prj_uid {@min 32}{@max 32}
     * 
     * @return void
     * @throws RestException 
     * 
     * @access protected
     * @class AccessControl {@permission PM_FACTORY}
     */
    public function doPostDynaFormLanguage($dyn_uid, $prj_uid)
    {
        try {

            $pmDynaform = new PmDynaform();
            $pmDynaform->uploadLanguage($dyn_uid);
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * Delete language from a Dynaform.
     * 
     * @url POST /:prj_uid/dynaform/:dyn_uid/delete-language/:lang
     *
     * @param string $dyn_uid {@min 32}{@max 32}
     * @param string $prj_uid {@min 32}{@max 32}
     * @param string $lang
     * 
     * @return void
     * @throws RestException 
     * 
     * @access protected
     * @class AccessControl {@permission PM_FACTORY}
     */
    public function doDeleteDynaFormLanguage($dyn_uid, $prj_uid, $lang)
    {
        try {

            $pmDynaform = new PmDynaform();
            $pmDynaform->deleteLanguage($dyn_uid, $lang);
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @url GET /:prj_uid/dynaform/:dyn_uid/list-language
     *
     * @param string $dyn_uid {@min 32}{@max 32}
     * @param string $prj_uid {@min 32}{@max 32}
     */
    public function doGetListDynaFormLanguage($dyn_uid, $prj_uid)
    {
        try {

            $pmDynaform = new PmDynaform();
            return $pmDynaform->listLanguage($dyn_uid);
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * Get Dynaform's history.
     * 
     * @url POST /:prj_uid/dynaform/:dyn_uid/history
     *
     * @param string $dyn_uid {@min 32}{@max 32}
     * @param string $prj_uid {@min 32}{@max 32}
     * @param array  $request_data
     * 
     * @return array
     * @throws RestException 
     * 
     * @access protected
     * @class AccessControl {@permission PM_FACTORY}
     */
    public function doGetDynaFormHistory($dyn_uid, $prj_uid, $request_data)
    {
        try {
            $dynaForm = new \ProcessMaker\BusinessModel\DynaForm();
            $dynaForm->setFormatFieldNameInUppercase(false);

            $response = $dynaForm->getDynaFormHistory($prj_uid, $dyn_uid, $request_data);

            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

}
