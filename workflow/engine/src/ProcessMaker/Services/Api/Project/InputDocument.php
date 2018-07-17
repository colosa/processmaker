<?php
namespace ProcessMaker\Services\Api\Project;

use \ProcessMaker\Services\Api;
use \Luracast\Restler\RestException;

/**
 * Project\InputDocument Api Controller
 *
 * @protected
 */
class InputDocument extends Api
{
    /**
     * @url GET /:prj_uid/input-document/:inp_doc_uid
     *
     * @param string $inp_doc_uid {@min 32}{@max 32}
     * @param string $prj_uid     {@min 32}{@max 32}
     */
    public function doGetInputDocument($inp_doc_uid, $prj_uid)
    {
        try {
            $inputDocument = new \ProcessMaker\BusinessModel\InputDocument();
            $inputDocument->setFormatFieldNameInUppercase(false);
            $inputDocument->setArrayFieldNameForException(array("processUid" => "prj_uid"));

            $response = $inputDocument->getInputDocument($inp_doc_uid);

            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * Create a new Input Document in a project.
     * 
     * @url POST /:prj_uid/input-document
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
    public function doPostInputDocument($prj_uid, $request_data)
    {
        try {
            $inputDocument = new \ProcessMaker\BusinessModel\InputDocument();
            $inputDocument->setFormatFieldNameInUppercase(false);
            $inputDocument->setArrayFieldNameForException(array("processUid" => "prj_uid"));

            $arrayData = $inputDocument->create($prj_uid, $request_data);

            $response = $arrayData;

            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * Update input document.
     *
     * @url PUT /:prj_uid/input-document/:inp_doc_uid
     *
     * @param string $inp_doc_uid  {@min 32}{@max 32}
     * @param string $prj_uid      {@min 32}{@max 32}
     * @param array  $request_data
     *
     * @throws RestException
     *
     * @access protected
     * @class AccessControl {@permission PM_FACTORY}
     */
    public function doPutInputDocument($inp_doc_uid, $prj_uid, $request_data)
    {
        try {
            $inputDocument = new \ProcessMaker\BusinessModel\InputDocument();
            $inputDocument->setFormatFieldNameInUppercase(false);
            $inputDocument->setArrayFieldNameForException(array("processUid" => "prj_uid"));

            $arrayData = $inputDocument->update($inp_doc_uid, $request_data);
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @url DELETE /:prj_uid/input-document/:inp_doc_uid
     * @access protected
     * @class AccessControl {@permission PM_FACTORY}
     *
     * @param string $inp_doc_uid {@min 32}{@max 32}
     * @param string $prj_uid     {@min 32}{@max 32}
     */
    public function doDeleteInputDocument($inp_doc_uid, $prj_uid)
    {
        try {
            $inputDocument = new \ProcessMaker\BusinessModel\InputDocument();
            $inputDocument->setFormatFieldNameInUppercase(false);
            $inputDocument->setArrayFieldNameForException(array("processUid" => "prj_uid"));

            $inputDocument->delete($inp_doc_uid);
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }
}

