<?php

sleep(1);
global $RBAC;
if ($RBAC->userCanAccess('PM_FACTORY') == 1) {
    if (isset($_SESSION['processes_upload'])) {
        $form = $_SESSION['processes_upload'];
        $app = new Processes();
        if (!$app->processExists($form['PRO_UID'])) {
            $result = 0;
            $msg = G::LoadTranslation('ID_PROCESS_UID_NOT_DEFINED');
            echo "{'result': $result, 'msg':'$msg'}";
            die;
        }
        switch ($form['MAIN_DIRECTORY']) {
            case 'mailTemplates':
                $sDirectory = PATH_DATA_MAILTEMPLATES . $form['PRO_UID'] . PATH_SEP . ($form['CURRENT_DIRECTORY'] != '' ? $form['CURRENT_DIRECTORY'] . PATH_SEP : '');
                break;
            case 'public':
                $sDirectory = PATH_DATA_PUBLIC . $form['PRO_UID'] . PATH_SEP . ($form['CURRENT_DIRECTORY'] != '' ? $form['CURRENT_DIRECTORY'] . PATH_SEP : '');
                break;
            default:
                die();
                break;
        }
    }

    $fileName = $_FILES['form']['name'];
    $canUploadPhpFile = true;
    $extension = pathinfo($fileName, PATHINFO_EXTENSION);
    if (\Bootstrap::getDisablePhpUploadExecution() === 1 && $extension === 'php') {
        $message = \G::LoadTranslation('THE_UPLOAD_OF_PHP_FILES_WAS_DISABLED');
        \Bootstrap::registerMonologPhpUploadExecution('phpUpload', 550, $message, $fileName);
        $canUploadPhpFile = false;
    }

    if ($_FILES['form']['error'] == "0" && $canUploadPhpFile) {
        G::uploadFile($_FILES['form']['tmp_name'], $sDirectory, $fileName);
        $msg = "Uploaded (" . (round((filesize($sDirectory . $fileName) / 1024) * 10) / 10) . " kb)";
        $result = 1;
    } else {
        $msg = "Failed";
        if ($canUploadPhpFile === false) {
            $msg = $message;
        }
        $result = 0;
    }

    echo "{'result': $result, 'msg':'$msg'}";
}
