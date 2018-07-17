<?php
namespace ProcessMaker\Services\Api\Project;

use \ProcessMaker\Services\Api;
use \Luracast\Restler\RestException;

/**
 * Project\OutputDocuments Api Controller
 *
 * @protected
 */
class OutputDocuments extends Api
{
    /**
     * @param string $prjUid {@min 32} {@max 32}
     *
     * @url GET /:prjUid/output-documents
     */
    public function doGetProjectOutputDocuments($prjUid)
    {
        try {
            $outputDocument = new \ProcessMaker\BusinessModel\OutputDocument();
            $arrayData = $outputDocument->getOutputDocuments($prjUid);
            //Response
            $response = $arrayData;
        } catch (\Exception $e) {
            //Response
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
        return $response;
    }

    /**
     * @param string $prjUid {@min 32} {@max 32}
     * @param string $outputDocumentUid {@min 32} {@max 32}
     *
     * @url GET /:prjUid/output-document/:outputDocumentUid
     */
    public function doGetProjectOutputDocument($prjUid, $outputDocumentUid)
    {
        try {
            $outputDocument = new \ProcessMaker\BusinessModel\OutputDocument();
            $objectData = $outputDocument->getOutputDocument($prjUid, $outputDocumentUid);
            //Response
            $response = $objectData;
        } catch (\Exception $e) {
            //Response
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
        return $response;
    }

    /**
     * Create a new output document for a project.
     * 
     * @url POST /:prjUid/output-document
     * @status 201
     * 
     * @param string $prjUid {@min 32} {@max 32}
     * @param OutputDocumentStructure $request_data
     * 
     * @return array
     * @throws RestException
     * 
     * @access protected
     * @class AccessControl {@permission PM_FACTORY}
     */
    public function doPostProjectOutputDocument($prjUid, OutputDocumentStructure $request_data = null)
    {
        try {
            $request_data = (array)($request_data);
            $outputDocument = new \ProcessMaker\BusinessModel\OutputDocument();
            $arrayData = $outputDocument->addOutputDocument($prjUid, $request_data);
            //Response
            $response = $arrayData;
        } catch (\Exception $e) {
            //Response
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
        return $response;
    }

    /**
     * Update project output document.
     *
     * @url PUT /:prjUid/output-document/:outputDocumentUid
     *
     * @param string $prjUid {@min 32} {@max 32}
     * @param string $outputDocumentUid {@min 32} {@max 32}
     * @param array $request_data
     *
     * @throws RestException
     *
     * @access protected
     * @class AccessControl {@permission PM_FACTORY}
     */
    public function doPutProjectOutputDocument($prjUid, $outputDocumentUid, $request_data)
    {
        try {
            $request_data = (array)($request_data);
            $outputDocument = new \ProcessMaker\BusinessModel\OutputDocument();
            $outputDocument->updateOutputDocument($prjUid, $request_data, 0, $outputDocumentUid);
        } catch (\Exception $e) {
            //Response
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
    }

    /**
     * @url DELETE /:prjUid/output-document/:outputDocumentUid
     * @access protected
     * @class AccessControl {@permission PM_FACTORY}
     *
     * @param string $prjUid {@min 32} {@max 32}
     * @param string $outputDocumentUid {@min 32} {@max 32}
     *
     */
    public function doDeleteProjectOutputDocument($prjUid, $outputDocumentUid)
    {
        try {
            $outputDocument = new \ProcessMaker\BusinessModel\OutputDocument();
            $outputDocument->deleteOutputDocument($prjUid, $outputDocumentUid);
        } catch (\Exception $e) {
            //Response
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
    }
}

class OutputDocumentStructure
{
    /**
     * @var string {@from body}
     */
    public $out_doc_title;

    /**
     * @var string {@from body} {@required false}
     */
    public $out_doc_description;

    /**
     * @var string {@from body}
     */
    public $out_doc_filename;

    /**
     * @var string {@from body} {@required false}
     */
    public $out_doc_template;

    /**
     * @var string {@from body} {@choice TCPDF,HTML2PDF}
     */
    public $out_doc_report_generator;

    /**
     * @var int {@from body} {@choice 0,1}
     */
    public $out_doc_landscape;

    /**
     * @var string {@from body} {@min 0} {@max 10}
     */
    public $out_doc_media;

    /**
     * @var int {@from body}
     */
    public $out_doc_left_margin;

    /**
     * @var int {@from body}
     */
    public $out_doc_right_margin;

    /**
     * @var int {@from body}
     */
    public $out_doc_top_margin;

    /**
     * @var int {@from body}
     */
    public $out_doc_bottom_margin;

    /**
     * @var string {@from body} {@choice PDF,DOC,BOTH}
     */
    public $out_doc_generate;

    /**
     * @var string {@from body} {@min 0} {@max 32}
     */
    public $out_doc_type;

    /**
     * @var int {@from body} {@required false}
     */
    public $out_doc_current_revision;

    /**
     * @var string {@from body} {@required false}
     */
    public $out_doc_field_mapping;

    /**
     * @var int {@from body}
     */
    public $out_doc_versioning;

    /**
     * @var string {@from body} {@required false}
     */
    public $out_doc_destination_path;

    /**
     * @var string {@from body} {@required false}
     */
    public $out_doc_tags;

    /**
     * @var int {@from body} {@choice 0,1}
     */
    public $out_doc_pdf_security_enabled;

    /**
     * @var string {@from body} {@min 0} {@max 32} {@required false}
     */
    public $out_doc_pdf_security_open_password;

    /**
     * @var string {@from body} {@min 0} {@max 32} {@required false}
     */
    public $out_doc_pdf_security_owner_password;

    /**
     * @var string {@from body} {@min 0} {@max 150} {@required false}
     */
    public $out_doc_pdf_security_permissions;

    /**
     * @var int {@from body} {@choice 0,1}
     */
    public $out_doc_open_type;
}

