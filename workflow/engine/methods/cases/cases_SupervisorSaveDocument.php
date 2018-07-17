<?php

use ProcessMaker\Plugins\PluginRegistry;

try {

    $appDocument = new AppDocument();
    $fields = array(
        "APP_UID" => $_GET["APP_UID"],
        "DEL_INDEX" => 100000,
        "USR_UID" => $_SESSION["USER_LOGGED"],
        "DOC_UID" => $_GET["UID"],
        "APP_DOC_TYPE" => $_POST["form"]["APP_DOC_TYPE"],
        "APP_DOC_CREATE_DATE" => date("Y-m-d H:i:s"),
        "APP_DOC_COMMENT" => isset($_POST["form"]["APP_DOC_COMMENT"]) ? $_POST["form"]["APP_DOC_COMMENT"] : "",
        "APP_DOC_TITLE" => "",
        "APP_DOC_FILENAME" => isset($_FILES["form"]["name"]["APP_DOC_FILENAME"]) ? $_FILES["form"]["name"]["APP_DOC_FILENAME"] : ""
    );
    if (!empty($_GET["APP_DOC_UID"])) {
        $fields['APP_DOC_UID'] = $_GET["APP_DOC_UID"];
    }
    if (!empty($_GET["DOC_VERSION"])) {
        $fields['DOC_VERSION'] = $_GET["DOC_VERSION"];
    }

    $appDocument->create($fields);
    $appDocUid = $appDocument->getAppDocUid();
    $info = pathinfo($appDocument->getAppDocFilename());
    $ext = (isset($info['extension']) ? $info['extension'] : '');

    //Save the file
    if (!empty($_FILES['form'])) {
        if ($_FILES['form']['error']['APP_DOC_FILENAME'] == 0) {
            $pathName = PATH_DOCUMENT . G::getPathFromUID($_GET['APP_UID']) . PATH_SEP;
            $fileName = $appDocUid . '.' . $ext;
            $originalName = $_FILES['form']['name']['APP_DOC_FILENAME'];
            G::uploadFile($_FILES['form']['tmp_name']['APP_DOC_FILENAME'], $pathName, $fileName);

            //Plugin Hook PM_UPLOAD_DOCUMENT for upload document
            $pluginRegistry = PluginRegistry::loadSingleton();
            if ($pluginRegistry->existsTrigger(PM_UPLOAD_DOCUMENT) && class_exists('uploadDocumentData')) {
                $data['APP_UID'] = $_GET['APP_UID'];
                $documentData = new uploadDocumentData(
                    $_GET['APP_UID'],
                    $_SESSION['USER_LOGGED'],
                    $pathName . $fileName,
                    $fields['APP_DOC_FILENAME'],
                    $appDocUid
                );
                $pluginRegistry->executeTriggers(PM_UPLOAD_DOCUMENT, $documentData);
                unlink($pathName . $fileName);
            }
            //end plugin

            //Update AppData with the current file uploaded
            $case = new Cases();
            $appDataFields = $case->loadCase($_GET['APP_UID']);

            $criteria = new Criteria('workflow');
            $criteria->addSelectColumn(AppDelegationPeer::TAS_UID);
            $criteria->add(AppDelegationPeer::APP_UID, $_GET['APP_UID'], CRITERIA::EQUAL);
            $criteria->addAscendingOrderByColumn(AppDelegationPeer::DEL_INDEX);
            $dataset = AppDelegationPeer::doSelectRS($criteria);
            $dataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);

            $dataset->next();
            $task = new Tasks();

            $dynaforms = [];
            while ($row = $dataset->getRow()) {
                $steps = $task->getStepsOfTask($row['TAS_UID']);
                if (is_array($steps)) {
                    foreach ($steps as $key => $value) {
                        $criteriaStep = new Criteria('workflow');
                        $criteriaStep->addSelectColumn(StepPeer::STEP_UID_OBJ);
                        $stepId = (isset($value['STEP_UID'])) ? $value['STEP_UID'] : 0;
                        $criteriaStep->add(StepPeer::STEP_UID, $stepId, CRITERIA::EQUAL);
                        $criteriaStep->add(StepPeer::STEP_TYPE_OBJ, 'DYNAFORM', CRITERIA::EQUAL);
                        $dataSetStep = StepPeer::doSelectRS($criteriaStep);
                        $dataSetStep->setFetchmode(ResultSet::FETCHMODE_ASSOC);
                        $dataSetStep->next();
                        $rowStep = $dataSetStep->getRow();

                        if (is_array($rowStep) && !in_array($rowStep['STEP_UID_OBJ'], $dynaforms)) {
                            $dynaforms[] = $rowStep['STEP_UID_OBJ'];
                        }
                    }
                    unset($value);
                }
                $dataset->next();
            }


            if (count($dynaforms) > 0) {
                require_once("classes/model/Dynaform.php");
                $dynInstance = new Dynaform();
                foreach ($dynaforms as $key => $value) {
                    $allFields = $dynInstance->getDynaformFields($value);
                    if (is_array($allFields)) {
                        foreach ($allFields as $kInput => $input) {
                            if (!isset($input->input)) {
                                continue;
                            }

                            if ($input->type == 'file' && $input->input == $_GET['UID'] && !empty($appDataFields['APP_DATA'][$kInput])) {
                                $appDataFields['APP_DATA'][$kInput] = $originalName;
                                $case->updateCase($_GET['APP_UID'], $appDataFields);
                            }
                        }
                        unset($input);
                    }
                }
                unset($value);
            }
            //End Update AppData with the current file uploaded
        }
    }

    //Go to the next step
    if (!isset($_POST['form']['MORE'])) {
        $case = new Cases();
        $fields = $case->loadCase($_GET['APP_UID']);
        $nextStep = $case->getNextSupervisorStep($fields['PRO_UID'], $_GET['position'], 'INPUT_DOCUMENT');
        G::header('location: ' . 'cases_StepToReviseInputs?type=INPUT_DOCUMENT&INP_DOC_UID=' . $nextStep['UID'] . '&position=' . $nextStep['POSITION'] . '&APP_UID=' . $_GET['APP_UID'] . '&DEL_INDEX=');
        die();
    } else {
        G::header('location: ' . $_SERVER['HTTP_REFERER']);
        die();
    }
} catch (Exception $e) {
    /* Render Error page */
    $message = [];
    $message['MESSAGE'] = $e->getMessage();
    $G_PUBLISH = new Publisher();
    $G_PUBLISH->AddContent('xmlform', 'xmlform', 'login/showMessage', '', $message);
    G::RenderPage('publish');
}
