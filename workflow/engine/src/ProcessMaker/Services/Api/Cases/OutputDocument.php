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
    public function __isAllowed()
    {
        try {
            $methodName = $this->restler->apiMethodInfo->methodName;
            $arrayArgs  = $this->restler->apiMethodInfo->arguments;

            switch ($methodName) {
                case 'doGetOutputDocumentFile':
                    $applicationUid = $this->parameters[$arrayArgs['app_uid']];
                    $appDocumentUid = $this->parameters[$arrayArgs['app_doc_uid']];
                    $userUid = $this->getUserId();

                    //Check whether the process supervisor
                    $case = new \ProcessMaker\BusinessModel\Cases();
                    $arrayApplicationData = $case->getApplicationRecordByPk($applicationUid, [], false);

                    $supervisor = new \ProcessMaker\BusinessModel\ProcessSupervisor();
                    $flagps = $supervisor->isUserProcessSupervisor($arrayApplicationData['PRO_UID'], $userUid);

                    if ($flagps) {
                        return true;
                    }

                    //Verify if you have permissions of the process
                    $outputDocument = new \ProcessMaker\BusinessModel\Cases\OutputDocument();
                    $flagpp = $outputDocument->checkProcessPermission($applicationUid, null, $userUid, $appDocumentUid, 'VIEW');

                    if ($flagpp) {
                        return true;
                    }

                    //Check whether the user has created the output document
                    $flaguser = $outputDocument->checkUser($applicationUid, $appDocumentUid, $userUid);

                    if ($flaguser) {
                        return true;
                    }
                    break;
            }

            //Return
            return false;
        } catch (\Exception $e) {
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
    }

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
     * @url GET /:app_uid/output-document/:app_doc_uid/file
     *
     * @access protected
     * @class  AccessControl {@className \ProcessMaker\Services\Api\Cases\OutputDocument}
     *
     * @param string $app_uid     {@min 32}{@max 32}
     * @param string $app_doc_uid {@min 32}{@max 32}
     */
    public function doGetOutputDocumentFile($app_uid, $app_doc_uid, $extension = null)
    {
        try {
            $caseOutdoc = new \ProcessMaker\BusinessModel\Cases\OutputDocument();
            $response = $caseOutdoc->streamFile($app_doc_uid, $app_uid, $extension);

            //Return
            return $response;
        } catch (\Exception $e) {
            throw  new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
    }

    /**
     * @url DELETE /:app_uid/output-document/:app_doc_uid
     * @access protected
     * @class AccessControl {@permission PM_CASES}
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
     * Generate a specified Output Document for a given case, meaning that a PDF, 
     * a DOC or both files (depending on options selected in the definition of the 
     * Output Document) will be created, inserting any variables in the template. 
     * If the Output Document already exists, then it will be regenerated. 
     * If versioning is enabled, then the regenerated files will be given a new 
     * version number and document index number, but if versioning is NOT enabled, 
     * then the existing files will be overwritten with the same version number 
     * and document index number.
     * 
     * @url POST /:app_uid/:del_index/output-document/:out_doc_uid
     * 
     * @param string $app_uid     {@min 32}{@max 32}
     * @param int    $del_index   {@min 1}
     * @param string $out_doc_uid {@min 32}{@max 32}
     * 
     * @return object
     * @throws RestException 
     * 
     * @access protected
     * @class AccessControl {@permission PM_CASES}
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
