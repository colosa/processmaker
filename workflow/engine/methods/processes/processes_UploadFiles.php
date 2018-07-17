<?php
global $RBAC;
if ( $RBAC->userCanAccess('PM_FACTORY') == 1) {

    $app = new Processes();
    if (!$app->processExists($_POST['form']['PRO_UID'])) {
        echo G::LoadTranslation('ID_PROCESS_UID_NOT_DEFINED');
        die;
    }
    switch ($_POST['form']['MAIN_DIRECTORY']) {
        case 'mailTemplates':
            $sDirectory = PATH_DATA_MAILTEMPLATES . $_POST['form']['PRO_UID'] . PATH_SEP . ($_POST['form']['CURRENT_DIRECTORY'] != '' ? $_POST['form']['CURRENT_DIRECTORY'] . PATH_SEP : '');
            break;
        case 'public':
            $sDirectory = PATH_DATA_PUBLIC . $_POST['form']['PRO_UID'] . PATH_SEP . ($_POST['form']['CURRENT_DIRECTORY'] != '' ? $_POST['form']['CURRENT_DIRECTORY'] . PATH_SEP : '');
            break;
        default:
            die();
            break;
    }
    for ($i = 1; $i <= 5; $i ++) {
        if ($_FILES['form']['tmp_name']['FILENAME' . (string) $i] != '') {
            G::uploadFile( $_FILES['form']['tmp_name']['FILENAME' . (string) $i], $sDirectory, $_FILES['form']['name']['FILENAME' . (string) $i] );
        }
    }
}
die( '<script type="text/javascript">parent.goToDirectoryforie(\'' . $_POST['form']['PRO_UID'] . '\', \'' . $_POST['form']['MAIN_DIRECTORY'] . '\', \'' . $_POST['form']['CURRENT_DIRECTORY'] . '\');</script>' );
