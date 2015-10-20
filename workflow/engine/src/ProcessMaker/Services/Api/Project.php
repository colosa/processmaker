<?php
namespace ProcessMaker\Services\Api;

use Luracast\Restler\RestException;
use ProcessMaker\Services\Api;
use \ProcessMaker\Project\Adapter;
use \ProcessMaker\Util;

/**
 * Class Project
 *
 * @package Services\Api\ProcessMaker
 * @author Erik Amaru Ortiz <aortiz.erik@gmail.com, erik@colosa.com>
 *
 * @protected
 */
class Project extends Api
{
    /**
     * @url GET
     */
    public function doGetProjects()
    {
        try {
            $start = null;
            $limit = null;
            $filter = "";

            $projects = Adapter\BpmnWorkflow::getList($start, $limit, $filter, CASE_LOWER);

            return $projects;
        } catch (\Exception $e) {
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
    }

    /**
     * @url GET /:prj_uid
     *
     * @param string $prj_uid {@min 32}{@max 32}
     */
    public function doGetProject($prj_uid)
    {
        try {
            return Adapter\BpmnWorkflow::getStruct($prj_uid);
        } catch (\Exception $e) {
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
    }

    /**
     * Post Project
     *
     * @param string $prj_name
     * @param array $request_data
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     *
     * @url POST
     * @status 201
     */
    public function post($prj_name, $request_data)
    {
        try {
            if (!isset($request_data['prj_author'])) {
                $request_data['prj_author'] = $this->getUserId();
            }
            return Adapter\BpmnWorkflow::createFromStruct($request_data);
        } catch (\Exception $e) {
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
    }

    /**
     * @url PUT /:prj_uid
     *
     * @param string $prj_uid {@min 32}{@max 32}
     */
    public function doPutProject($prj_uid, $request_data)
    {
        try {
            return Adapter\BpmnWorkflow::updateFromStruct($prj_uid, $request_data);
        } catch (\Exception $e) {
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
    }

    /**
     * @param string $prj_uid {@min 1}{@max 32}
     * @url DELETE /:prj_uid
     */
    public function delete($prj_uid)
    {
        try {
            $oBpmnWf = Adapter\BpmnWorkflow::load($prj_uid);
            $oBpmnWf->remove();
        } catch (\Exception $e) {
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
    }

    /**
     * @url GET /:prj_uid/export
     *
     * @param string $prj_uid {@min 32}{@max 32}
     */
    public function export($prj_uid)
    {
        $exporter = new \ProcessMaker\Exporter\XmlExporter($prj_uid);
        $getProjectName = $exporter->truncateName($exporter->getProjectName(),false);

        $outputDir = PATH_DATA . "sites" . PATH_SEP . SYS_SYS . PATH_SEP . "files" . PATH_SEP . "output" . PATH_SEP;
        $version = \ProcessMaker\Util\Common::getLastVersion($outputDir . $getProjectName . "-*.pmx") + 1;
        $outputFilename = $outputDir . sprintf("%s-%s.%s", str_replace(" ", "_", $getProjectName), $version, "pmx");

        $exporter->setMetadata("export_version", $version);
        $outputFilename = $outputDir . $exporter->saveExport($outputFilename);

        $httpStream = new \ProcessMaker\Util\IO\HttpStream();
        $fileExtension = pathinfo($outputFilename, PATHINFO_EXTENSION);

        $httpStream->loadFromFile($outputFilename);
        $httpStream->setHeader("Content-Type", "application/xml; charset=UTF-8");
        $httpStream->send();
    }

    /**
     * @url POST /import
     *
     * @param array $request_data
     *
     * @status 201
     */
    public function doPostImport(array $request_data, $option = null, $option_group = null)
    {
        try {
            $importer = new \ProcessMaker\Importer\XmlImporter();

            $importer->setSaveDir(PATH_DOCUMENT . "input");
            $importer->setData("usr_uid", $this->getUserId());

            $arrayData = $importer->importPostFile(
                $request_data,
                $option,
                $option_group,
                array("projectFile" => "project_file", "option" => "option", "optionGroup" => "option_group")
            );

            $response = $arrayData;

            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }
    
    /**
     * @url POST /save-as
     * 
     * @param string $prj_uid
     * @param string $prj_name
     * @param string $prj_description
     * @param string $prj_category
     */
    public function doSaveAs($prj_uid, $prj_name, $prj_description, $prj_category)
    {
        $importer = new \ProcessMaker\Importer\XmlImporter();
        return $importer->saveAs($prj_uid, $prj_name, $prj_description, $prj_category);
    }

    /**
     * @url GET /:prj_uid/process
     *
     * @param string $prj_uid {@min 32}{@max 32}
     */
    public function doGetProcess($prj_uid)
    {
        try {
            $process = new \ProcessMaker\BusinessModel\Process();
            $process->setFormatFieldNameInUppercase(false);
            $process->setArrayFieldNameForException(array("processUid" => "prj_uid"));

            $response = $process->getProcess($prj_uid);

            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @url PUT /:prj_uid/process
     *
     * @param string $prj_uid      {@min 32}{@max 32}
     * @param array  $request_data
     */
    public function doPutProcess($prj_uid, $request_data)
    {
        try {
            $process = new \ProcessMaker\BusinessModel\Process();
            $process->setFormatFieldNameInUppercase(false);
            $process->setArrayFieldNameForException(array("processUid" => "prj_uid"));

            $arrayData = $process->update($prj_uid, $request_data);
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @url POST /generate-bpmn
     *
     * @param array $request_data
     *
     * @status 201
     */
    public function doPostGenerateBpmn(array $request_data)
    {
        try {
            //Set data
            $request_data = array_change_key_case($request_data, CASE_UPPER);

            //Verify data
            $process = new \ProcessMaker\BusinessModel\Process();

            $process->throwExceptionIfDataNotMetFieldDefinition(
                $request_data,
                array("PRO_UID" => array("type" => "string", "required" => true, "empty" => false, "defaultValues" => array(), "fieldNameAux" => "processUid")),
                array("processUid" => "pro_uid"),
                true
            );

            //Generate BPMN
            $workflowBpmn = new \ProcessMaker\Project\Adapter\WorkflowBpmn();

            $projectUid = $workflowBpmn->generateBpmn($request_data["PRO_UID"], "pro_uid", $this->getUserId());

            $arrayData = array_change_key_case(array_merge(array("PRJ_UID" => $projectUid), $request_data), CASE_LOWER);

            $response = $arrayData;

            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @url GET /:prj_uid/dynaforms
     *
     * @param string $prj_uid {@min 32}{@max 32}
     */
    public function doGetDynaForms($prj_uid)
    {
        try {
            $process = new \ProcessMaker\BusinessModel\Process();
            $process->setFormatFieldNameInUppercase(false);
            $process->setArrayFieldNameForException(array("processUid" => "prj_uid"));

            $response = $process->getDynaForms($prj_uid);

            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @url GET /:prj_uid/input-documents
     *
     * @param string $prj_uid {@min 32}{@max 32}
     */
    public function doGetInputDocuments($prj_uid)
    {
        try {
            $process = new \ProcessMaker\BusinessModel\Process();
            $process->setFormatFieldNameInUppercase(false);
            $process->setArrayFieldNameForException(array("processUid" => "prj_uid"));

            $response = $process->getInputDocuments($prj_uid);

            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @url GET /:prj_uid/variables
     *
     * @param string $prj_uid {@min 32}{@max 32}
     */
    public function doGetVariables($prj_uid)
    {
        try {
            $process = new \ProcessMaker\BusinessModel\Process();
            $process->setFormatFieldNameInUppercase(false);
            $process->setArrayFieldNameForException(array("processUid" => "prj_uid"));

            $response = $process->getVariables("ALL", $prj_uid);

            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @url GET /:prj_uid/grid/variables
     * @url GET /:prj_uid/grid/:grid_uid/variables
     *
     * @param string $prj_uid  {@min 32}{@max 32}
     * @param string $grid_uid
     */
    public function doGetGridVariables($prj_uid, $grid_uid = "")
    {
        try {
            $process = new \ProcessMaker\BusinessModel\Process();
            $process->setFormatFieldNameInUppercase(false);
            $process->setArrayFieldNameForException(array("processUid" => "prj_uid"));

            $response = ($grid_uid == "")? $process->getVariables("GRID", $prj_uid) : $process->getVariables("GRIDVARS", $prj_uid, $grid_uid);

            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @url GET /:prj_uid/trigger-wizards
     *
     * @param string $prj_uid {@min 32}{@max 32}
     */
    public function doGetTriggerWizards($prj_uid)
    {
        try {
            $process = new \ProcessMaker\BusinessModel\Process();
            $process->setFormatFieldNameInUppercase(false);
            $process->setArrayFieldNameForException(array("processUid" => "prj_uid", "libraryName" => "lib_name", "methodName" => "fn_name"));

            $response = $process->getLibraries($prj_uid);

            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }
    
    /**
     * @url PUT /:prj_uid/update-route-order
     *
     * @param string $prj_uid {@min 32}{@max 32}
     */
    public function doPutUpdateRouteOrder($prj_uid, $request_data)
    {
        try {
            $oRoute = new \Route();
            $result = $oRoute->updateRouteOrder($request_data);
            return $result;
        } catch (\Exception $e) {
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
    }
    
    /**
     * @url PUT /:prj_uid/update-route-order-from-project
     *
     * @param string $prj_uid {@min 32}{@max 32}
     */
    public function doPutUpdateRouteOrderFromProject($prj_uid)
    {
        try {
            $oRoute = new \Route();
            $result = $oRoute->updateRouteOrderFromProject($prj_uid);
            return $result;
        } catch (\Exception $e) {
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
    }
}

