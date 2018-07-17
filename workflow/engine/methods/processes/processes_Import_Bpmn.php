<?php

ini_set("max_execution_time", 0);

$filter = new InputFilter();
$_FILES = $filter->xssFilterHard($_FILES);

if (isset($_FILES["PROCESS_FILENAME"]) &&
        pathinfo($_FILES["PROCESS_FILENAME"]["name"], PATHINFO_EXTENSION) == "bpmn"
) {
    try {
        $createMode = $_REQUEST["createMode"];
        $createMode = $filter->xssFilterHard($createMode);
        $name = pathinfo($_FILES["PROCESS_FILENAME"]["name"], PATHINFO_FILENAME);
        $data = array(
            "type" => "bpmnProject",
            "PRO_TITLE" => $name,
            "PRO_DESCRIPTION" => "",
            "PRO_CATEGORY" => "",
            "PRO_CREATE_USER" => $_SESSION['USER_LOGGED']
        );
        if ($createMode === "overwrite") {
            $process = Process::getByProTitle($data["PRO_TITLE"]);
            if ($process !== null) {
                $oProcess = new Process();
                $oProcess->remove($process["PRO_UID"]);
            }
        }
        if ($createMode === "rename") {
            $data["PRO_TITLE"] = Process::getNextTitle($data["PRO_TITLE"]);
        }
        $project = new \ProcessMaker\Project\Adapter\WorkflowBpmn($data);
        copy($_FILES["PROCESS_FILENAME"]["tmp_name"], PATH_DOCUMENT . $project->getUid());
        $result = array(
            "success" => true,
            "catchMessage" => "",
            "prj_uid" => $project->getUid(),
            "createMode" => $createMode
        );
    } catch (Exception $e) {
        $result = array(
            "success" => "confirm",
            "catchMessage" => $e->getMessage(),
            "createMode" => $createMode
        );
    }
    echo G::json_encode($result);
    exit(0);
} else {
    $result = array(
        "success" => "error",
        "catchMessage" => G::LoadTranslation("ID_FILE_UPLOAD_INCORRECT_EXTENSION")
    );
    echo G::json_encode($result);
    exit(0);
}
