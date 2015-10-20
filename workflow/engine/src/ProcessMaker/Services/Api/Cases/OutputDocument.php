<?php
namespace ProcessMaker\Services\Api\Cases;

use \ProcessMaker\Services\Api;
use \Luracast\Restler\RestException;

/**
 * Cases\OutputDocument Api Controller
 *
 * @protected
 */
class OutputDocument extends Api
{
    /**
     * @url GET /:app_uid/output-documents
     *
     * @param string $app_uid     {@min 32}{@max 32}
     */
    public function doGetOutputDocuments($app_uid)
    {
        try {
            $userUid = $this->getUserId();
            $outputDocument = new \ProcessMaker\BusinessModel\Cases\OutputDocument();
            $response = $outputDocument->getCasesOutputDocuments($app_uid, $userUid);
            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @url GET /:app_uid/output-document/:app_doc_uid
     *
     * @param string $app_uid     {@min 32}{@max 32}
     * @param string $app_doc_uid     {@min 32}{@max 32}
     */
    public function doGetOutputDocument($app_uid, $app_doc_uid)
    {
        try {
            $userUid = $this->getUserId();
            $outputDocument = new \ProcessMaker\BusinessModel\Cases\OutputDocument();
            $response = $outputDocument->getCasesOutputDocument($app_uid, $userUid, $app_doc_uid);
            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @url DELETE /:app_uid/output-document/:app_doc_uid
     *
     * @param string $app_uid     {@min 32}{@max 32}
     * @param string $app_doc_uid     {@min 32}{@max 32}
     */
    public function doDeleteOutputDocument($app_uid, $app_doc_uid)
    {
        try {
            $outputDocument = new \ProcessMaker\BusinessModel\Cases\OutputDocument();
            $outputDocument->throwExceptionIfHaventPermissionToDelete($app_uid, 0, $this->getUserId(), $app_doc_uid);
            $outputDocument->removeOutputDocument($app_doc_uid);
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @url POST /:app_uid/:del_index/output-document/:out_doc_uid
     *
     * @param string $app_uid     {@min 32}{@max 32}
     * @param int    $del_index   {@min 1}
     * @param string $out_doc_uid {@min 32}{@max 32}
     */
    public function doPostOutputDocument($app_uid, $del_index, $out_doc_uid)
    {
        try {
            $userUid = $this->getUserId();

            $case = new \ProcessMaker\BusinessModel\Cases();
            $outputDocument = new \ProcessMaker\BusinessModel\Cases\OutputDocument();
            $outputDocument->throwExceptionIfCaseNotIsInInbox($app_uid, $del_index, $userUid);
            $outputDocument->throwExceptionIfOuputDocumentNotExistsInSteps($app_uid, $del_index, $out_doc_uid);
            $response = $outputDocument->addCasesOutputDocument($app_uid, $out_doc_uid, $userUid);

            //Return
            return $response;
        } catch (\Exception $e) {
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
    }
}
