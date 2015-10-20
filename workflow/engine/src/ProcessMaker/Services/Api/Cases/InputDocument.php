<?php
namespace ProcessMaker\Services\Api\Cases;

use \ProcessMaker\Services\Api;
use \Luracast\Restler\RestException;

/**
 * Cases\InputDocument Api Controller
 *
 * @protected
 */
class InputDocument extends Api
{
    /**
     * @url GET /:app_uid/input-documents
     *
     * @param string $app_uid     {@min 32}{@max 32}
     */
    public function doGetInputDocuments($app_uid)
    {
        try {
            $userUid = $this->getUserId();
            $inputDocument = new \ProcessMaker\BusinessModel\Cases\InputDocument();

            $response = $inputDocument->getCasesInputDocuments($app_uid, $userUid);

            if (empty($response)) {
                $response = $inputDocument->getCasesInputDocumentsBySupervisor($app_uid, $userUid);
            }

            //Return
            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @url GET /:app_uid/input-document/:inp_doc_uid
     *
     * @param string $app_uid     {@min 32}{@max 32}
     * @param string $inp_doc_uid     {@min 32}{@max 32}
     */
    public function doGetInputDocument($app_uid, $inp_doc_uid)
    {
        try {
            $userUid = $this->getUserId();
            $inputDocument = new \ProcessMaker\BusinessModel\Cases\InputDocument();
            $response = $inputDocument->getCasesInputDocument($app_uid, $userUid, $inp_doc_uid);
            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @url DELETE /:app_uid/:del_index/input-document/:app_doc_uid
     *
     * @param string $app_uid     {@min 32}{@max 32}
     * @param int    $del_index   {@min 1}
     * @param string $app_doc_uid {@min 32}{@max 32}
     */
    public function doDeleteInputDocument($app_uid, $del_index, $app_doc_uid)
    {
        try {
            $inputDocument = new \ProcessMaker\BusinessModel\Cases\InputDocument();

            $inputDocument->throwExceptionIfHaventPermissionToDelete($app_uid, $del_index, $this->getUserId(), $app_doc_uid);
            $inputDocument->throwExceptionIfInputDocumentNotExistsInSteps($app_uid, $del_index, $app_doc_uid);
            $inputDocument->removeInputDocument($app_doc_uid);
        } catch (\Exception $e) {
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
    }

    /**
     * @url POST /:app_uid/input-document
     *
     * @param string $app_uid         { @min 32}{@max 32}
     * @param string $tas_uid         {@min 32}{@max 32}
     * @param string $app_doc_comment
     * @param string $inp_doc_uid     {@min 32}{@max 32}
     */
    public function doPostInputDocument($app_uid, $tas_uid, $app_doc_comment, $inp_doc_uid)
    {
        try {
            $userUid = $this->getUserId();
            $inputDocument = new \ProcessMaker\BusinessModel\Cases\InputDocument();
            $response = $inputDocument->addCasesInputDocument($app_uid, $tas_uid, $app_doc_comment, $inp_doc_uid, $userUid);
            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }
}

