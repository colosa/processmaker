<?php
namespace ProcessMaker\Services\Api\Cases;

use \ProcessMaker\Services\Api;
use \Luracast\Restler\RestException;
use ProcessMaker\BusinessModel\Cases\InputDocument AS CasesInputDocument;
use Exception;

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
     * @url GET /:app_uid/input-document/:app_doc_uid/file
     *
     * @param string $app_uid {@min 32}{@max 32}
     * @param string $app_doc_uid {@min 32}{@max 32}
     * @param int $v {@from path}
     * @throws \Exception
     */
    public function doDownloadInputDocument($app_uid, $app_doc_uid, $v = 0)
    {
        try {
            $inputDocument = new \ProcessMaker\BusinessModel\Cases\InputDocument();
            $inputDocument->downloadInputDocument($app_uid, $app_doc_uid, $v);
        } catch (\Exception $e) {
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
    }

    /**
     * @url DELETE /:app_uid/:del_index/input-document/:app_doc_uid
     * @access protected
     * @class AccessControl {@permission PM_CASES}
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
     * Uploads a new Input Document file to a specified case. Note that the 
     * logged-in user must either be currently assigned to work on the case or a 
     * Process Supervisor with permissions to access the Input Document; otherwise, 
     * this endpoint returns an HTTP status code of 302.
     * 
     * @url POST /:app_uid/input-document
     * 
     * @param string $app_uid         { @min 32}{@max 32}
     * @param string $tas_uid         {@min 32}{@max 32}
     * @param string $app_doc_comment
     * @param string $inp_doc_uid     {@min 32}{@max 32}
     * 
     * @return array
     * @throws RestException 
     * 
     * @access protected
     * @class AccessControl {@permission PM_CASES}
     */
    public function doPostInputDocument($app_uid, $tas_uid, $app_doc_comment, $inp_doc_uid)
    {
        try {
            $userUid = $this->getUserId();

            $inputDocument = new \ProcessMaker\BusinessModel\Cases\InputDocument();
            $response = $inputDocument->addCasesInputDocument($app_uid, $tas_uid, $app_doc_comment, $inp_doc_uid, $userUid, false);
            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @url GET /:app_uid/input-document/:app_doc_uid/versions
     *
     * @param string $app_uid {@min 32}{@max 32}
     * @param string $app_doc_uid {@min 32}{@max 32}
     * @return array $response
     * @throws Exception
     */
    public function doGetDocumentByVersion($app_uid, $app_doc_uid)
    {
        try {
            $inputDocument = new CasesInputDocument();
            $response = $inputDocument->getAllVersionByDocUid($app_uid, $app_doc_uid);
            return $response;
        } catch (Exception $e) {
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
    }
}

