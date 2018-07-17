<?php
namespace ProcessMaker\Exporter;

use ProcessMaker\Core\System;
use ProcessMaker\Project;
use ProcessMaker\Util;

abstract class Exporter
{
    /**
     * @var string The Project UID
     */
    protected $prjUid;

    /**
     * Exporter version
     */
    const VERSION = "3.0";

    /**
     * @var \ProcessMaker\Project\Adapter\BpmnWorkflow
     */
    protected $bpmnProject;

    /**
     * @var array
     */
    protected $projectData = array();

    /**
     * @var array
     */
    protected $metadata = array();

    public function __construct($prjUid)
    {
        $this->prjUid = $prjUid;

        $this->bpmnProject = Project\Bpmn::load($prjUid);
        $this->projectData = $this->bpmnProject->getProject();

        $this->metadata = array(
            "vendor_version" => System::getVersion(),
            "vendor_version_code" => "Michelangelo",
            "export_timestamp" => date("U"),
            "export_datetime" => date("Y-m-d\TH:i:sP"),
            "export_server_addr" => isset($_SERVER["SERVER_ADDR"]) ? $_SERVER["SERVER_ADDR"].":".$_SERVER["SERVER_PORT"] : "Unknown",
            "export_server_os" => PHP_OS ,
            "export_server_php_version" => PHP_VERSION_ID,
        );
    }

    /**
     * Builds Output content of exported project
     *
     * @return string xml output of exported project
     */
    public abstract function export();

    /**
     * Builds Output content of exported project and save it into a given file path
     *
     * @param string $outputFile path of output file
     * @return mixed
     */
    public abstract function saveExport($outputFile);

    /**
     * Builds exported content of a Project
     *
     * @return mixed
     */
    public abstract function build();

    public function getProjectName()
    {
        return $this->projectData["PRJ_NAME"];
    }

    public function getProjectUid()
    {
        return $this->projectData["PRJ_UID"];
    }

    /**
     * Builds Project Data Structure
     *
     * @return array
     */
    protected function buildData()
    {
        $data = array();

        $data["metadata"] = $this->getMetadata();
        $data["metadata"]["workspace"] = defined("SYS_SYS") ? SYS_SYS : "Unknown";
        $data["metadata"]["name"] = $this->getProjectName();
        $data["metadata"]["uid"] = $this->getProjectUid();

        $bpmnStruct["ACTIVITY"] = \BpmnActivity::getAll($this->prjUid);
        $bpmnStruct["ARTIFACT"] = \BpmnArtifact::getAll($this->prjUid);
        $bpmnStruct["BOUND"] = \BpmnBound::getAll($this->prjUid);
        $bpmnStruct["DATA"] = \BpmnData::getAll($this->prjUid);
        $bpmnStruct["DIAGRAM"] = \BpmnDiagram::getAll($this->prjUid);
        $bpmnStruct["DOCUMENTATION"] = array();
        $bpmnStruct["EVENT"] = \BpmnEvent::getAll($this->prjUid);
        $bpmnStruct["EXTENSION"] = array();
        $bpmnStruct["FLOW"] = \BpmnFlow::getAll($this->prjUid, null, null, "", CASE_UPPER, false);
        $bpmnStruct["GATEWAY"] = \BpmnGateway::getAll($this->prjUid);
        $bpmnStruct["LANE"] = \BpmnLane::getAll($this->prjUid);
        $bpmnStruct["LANESET"] = \BpmnLaneset::getAll($this->prjUid);
        $bpmnStruct["PARTICIPANT"] = \BpmnParticipant::getAll($this->prjUid);
        $bpmnStruct["PROCESS"] = \BpmnProcess::getAll($this->prjUid);
        $bpmnStruct["PROJECT"] = array(\BpmnProjectPeer::retrieveByPK($this->prjUid)->toArray());

        $workflow = new \ProcessMaker\Project\Workflow();

        list($workflowData, $workflowFile) = $workflow->getData($this->prjUid);

        $data["bpmn-definition"] = $bpmnStruct;
        $data["workflow-definition"] = $workflowData;
        $data["workflow-files"] = $workflowFile;

        return $data;
    }

    /**
     * Returns the container name of project data structure
     *
     * @return string
     */
    public static function getContainerName()
    {
        return "ProcessMaker-Project";
    }

    /**
     * Returns the exporter version
     *
     * @return string
     */
    public function getVersion()
    {
        return self::VERSION;
    }

    public function setMetadata($key, $value)
    {
        $this->metadata[$key] = $value;
    }

    /**
     * Returns all metadata to include on export content
     *
     * @return array
     */
    public function getMetadata($key = "", $default = "")
    {
        if (! empty($key)) {
            return isset($this->metadata[$key]) ? $this->metadata[$key] : $default;
        } else {
            return $this->metadata;
        }
    }
}

