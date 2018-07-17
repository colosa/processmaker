<?php
namespace ProcessMaker\Services\Api;

use Luracast\Restler\RestException;
use ProcessMaker\Project\Bpmn;
use ProcessMaker\Services\Api;
use \ProcessMaker\Project\Adapter;
use \ProcessMaker\Util;
use \ProcessMaker\Util\DateTime;
use \ProcessMaker\BusinessModel\Validator;
use \ProcessMaker\BusinessModel\Migrator\GranularExporter;
use \ProcessMaker\BusinessModel\Migrator\ExportObjects;
use \ProcessMaker\Util\IO\HttpStream;
use \ProcessMaker\Util\Common;
use ProcessMaker\Project\Adapter\BpmnWorkflow;
use Exception;

/**
 * @package Services\Api\ProcessMaker
 * @protected
 * @access protected
 * @class AccessControl {@permission PM_FACTORY}
 */
class Project extends Api
{
    private $arrayFieldIso8601 = [
        "prj_create_date",
        "prj_update_date",
        "pro_update_date",
        "pro_create_date",
        "dyn_update_date"
    ];

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

            return DateTime::convertUtcToIso8601($projects, $this->arrayFieldIso8601);
        } catch (Exception $e) {
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
    }

    /**
     * @url GET /:prj_uid
     * @param string $prj_uid {@min 32}{@max 32}
     */
    public function doGetProject($prj_uid)
    {
        try {
            $project = Adapter\BpmnWorkflow::getStruct($prj_uid);

            $userProperty = new \UsersProperties();
            $property = $userProperty->loadOrCreateIfNotExists($this->getUserId());
            $project['usr_setting_designer'] = isset($property['USR_SETTING_DESIGNER']) ? \G::json_decode($property['USR_SETTING_DESIGNER']) : null;
            return DateTime::convertUtcToIso8601($project, $this->arrayFieldIso8601);
        } catch (Exception $e) {
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
    }

    /**
     * Post Project
     *
     * @param string $prj_name
     * @param array $request_data
     * @url POST
     * @status 201
     */
    public function post($prj_name, $request_data)
    {
        try {
            if (!isset($request_data['prj_author'])) {
                $request_data['prj_author'] = $this->getUserId();
            }
            Validator::throwExceptionIfDataNotMetIso8601Format($request_data, $this->arrayFieldIso8601);
            return Adapter\BpmnWorkflow::createFromStruct(DateTime::convertDataToUtc($request_data, $this->arrayFieldIso8601));
        } catch (Exception $e) {
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
    }

    /**
     * @url PUT /:prj_uid
     * @param string $prj_uid {@min 32}{@max 32}
     */
    public function doPutProject($prj_uid, $request_data)
    {
        try {
            if (array_key_exists('usr_setting_designer', $request_data)) {
                $oUserProperty = new \UsersProperties();
                $property = $oUserProperty->loadOrCreateIfNotExists($this->getUserId());
                $propertyArray = isset($property['USR_SETTING_DESIGNER']) ? \G::json_decode($property['USR_SETTING_DESIGNER'], true) : [];
                $usrSettingDesigner = array_merge($propertyArray, $request_data['usr_setting_designer']);
                $property['USR_SETTING_DESIGNER'] = \G::json_encode($usrSettingDesigner);
                $oUserProperty->update($property);
                unset($request_data['usr_setting_designer']);
            }

            Validator::throwExceptionIfDataNotMetIso8601Format($request_data, $this->arrayFieldIso8601);
            return Adapter\BpmnWorkflow::updateFromStruct($prj_uid, DateTime::convertDataToUtc($request_data, $this->arrayFieldIso8601));
        } catch (Exception $e) {
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
    }

    /**
     * @param string $prj_uid {@min 1}{@max 32}
     * @url DELETE /:prj_uid
     * @throws Exception
     */
    public function delete($prj_uid)
    {
        try {
            if (Bpmn::exists($prj_uid)) {
                $oBpmnWf = BpmnWorkflow::load($prj_uid);
                $oBpmnWf->remove();
            } else {
                throw new Exception("The project cannot be found or it was already deleted.");
            }
        } catch (Exception $e) {
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
    }

    /**
     * Bulk actions
     * 
     * @url POST /bulk
     * @access protected
     * @class AccessControl {@permission PM_FACTORY}
     * @param array $request_data
     * @return array $response
     * @throws Exception
     */
    public function bulk($request_data)
    {
        try {
            $response = Bpmn::doBulk($request_data);
            return $response;
        } catch (Exception $e) {
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
    }

    /**
     * @url GET /:prj_uid/export/listObjects
     * @param string $prj_uid {@min 32}{@max 32}
     * @return mixed|string
     * @throws RestException
     */
    public function objectList($prj_uid)
    {
        try {
            $exportProcess= new ExportObjects();
            $result = $exportProcess->objectList($prj_uid);
            return $result;
        } catch (Exception $e) {
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
    }

    /**
     * @url GET /:prj_uid/export-granular
     * @param string $prj_uid {@min 32}{@max 32}
     * @param string $objects
     */
    public function exportGranular($prj_uid, $objects)
    {
        $objects = \G::json_decode($objects);
        $granularExporter = new GranularExporter($prj_uid);
        $outputFilename = $granularExporter->export($objects);
        $outputFilename = PATH_DATA . 'sites' . PATH_SEP . SYS_SYS . PATH_SEP . 'files' . PATH_SEP . 'output' .
            PATH_SEP . $outputFilename;
        $httpStream = new HttpStream();
        $fileExtension = pathinfo($outputFilename, PATHINFO_EXTENSION);

        \G::auditLog('ExportProcess','Export process "' . $granularExporter->getProjectName() . '"');

        $httpStream->loadFromFile($outputFilename);
        $httpStream->setHeader("Content-Type", "application/xml; charset=UTF-8");
        $httpStream->send();
    }

    /**
     * @url GET /:prj_uid/export
     * @param string $prj_uid {@min 32}{@max 32}
     */
    public function export($prj_uid)
    {
        $exporter = new \ProcessMaker\Exporter\XmlExporter($prj_uid);
        $getProjectName = $exporter->truncateName($exporter->getProjectName(), false);

        $outputDir = PATH_DATA . "sites" . PATH_SEP . SYS_SYS . PATH_SEP . "files" . PATH_SEP . "output" . PATH_SEP;
        $version = Common::getLastVersionSpecialCharacters($outputDir, $getProjectName, "pmx") + 1;
        $outputFilename = $outputDir . sprintf("%s-%s.%s", str_replace(" ", "_", $getProjectName), $version, "pmx");

        $exporter->setMetadata("export_version", $version);
        $outputFilename = $outputDir . $exporter->saveExport($outputFilename);

        $httpStream = new \ProcessMaker\Util\IO\HttpStream();
        $fileExtension = pathinfo($outputFilename, PATHINFO_EXTENSION);

        \G::auditLog('ExportProcess','Export process "' . $exporter->getProjectName() . '"');

        $httpStream->loadFromFile($outputFilename);
        $httpStream->setHeader("Content-Type", "application/xml; charset=UTF-8");
        $httpStream->send();
    }

    /**
     * @url POST /import
     * @param array $request_data
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
        } catch (Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @url POST /save-as
     * @param string $prj_uid         {@from body}
     * @param string $prj_name        {@from body}
     * @param string $prj_description {@from body}
     * @param string $prj_category    {@from body}
     */
    public function doSaveAs($prj_uid, $prj_name, $prj_description = null, $prj_category = null)
    {
        $importer = new \ProcessMaker\Importer\XmlImporter();
        return $importer->saveAs($prj_uid, $prj_name, $prj_description, $prj_category, $this->getUserId());
    }

    /**
     * @url GET /:prj_uid/process
     * @param string $prj_uid {@min 32}{@max 32}
     */
    public function doGetProcess($prj_uid)
    {
        try {
            $process = new \ProcessMaker\BusinessModel\Process();
            $process->setFormatFieldNameInUppercase(false);
            $process->setArrayFieldNameForException(array("processUid" => "prj_uid"));

            $response = $process->getProcess($prj_uid);

            return DateTime::convertUtcToIso8601($response, $this->arrayFieldIso8601);

        } catch (Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @url PUT /:prj_uid/process
     * @param string $prj_uid      {@min 32}{@max 32}
     * @param array  $request_data
     */
    public function doPutProcess($prj_uid, $request_data)
    {
        try {
            Validator::throwExceptionIfDataNotMetIso8601Format($request_data, $this->arrayFieldIso8601);
            $process = new \ProcessMaker\BusinessModel\Process();
            $process->setFormatFieldNameInUppercase(false);
            $process->setArrayFieldNameForException(array("processUid" => "prj_uid"));

            $arrayData = $process->update($prj_uid, DateTime::convertDataToUtc($request_data, $this->arrayFieldIso8601));
        } catch (Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @url POST /generate-bpmn
     * @param array $request_data
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
        } catch (Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @url GET /:prj_uid/dynaforms
     * @param string $prj_uid {@min 32}{@max 32}
     */
    public function doGetDynaForms($prj_uid)
    {
        try {
            $process = new \ProcessMaker\BusinessModel\Process();
            $process->setFormatFieldNameInUppercase(false);
            $process->setArrayFieldNameForException(array("processUid" => "prj_uid"));

            $response = $process->getDynaForms($prj_uid);

            return DateTime::convertUtcToIso8601($response, $this->arrayFieldIso8601);
        } catch (Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @url GET /:prj_uid/input-documents
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
        } catch (Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @url GET /:prj_uid/variables
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
        } catch (Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @url GET /:prj_uid/grid/variables
     * @url GET /:prj_uid/grid/:grid_uid/variables
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
        } catch (Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @url GET /:prj_uid/trigger-wizards
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
        } catch (Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @url PUT /:prj_uid/update-route-order
     * @param string $prj_uid {@min 32}{@max 32}
     */
    public function doPutUpdateRouteOrder($prj_uid, $request_data)
    {
        try {
            $oRoute = new \Route();
            $result = $oRoute->updateRouteOrder($request_data);
            return $result;
        } catch (Exception $e) {
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
    }

    /**
     * @url PUT /:prj_uid/update-route-order-from-project
     * @param string $prj_uid {@min 32}{@max 32}
     */
    public function doPutUpdateRouteOrderFromProject($prj_uid)
    {
        try {
            $oRoute = new \Route();
            $result = $oRoute->updateRouteOrderFromProject($prj_uid);
            return $result;
        } catch (Exception $e) {
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
    }
}
