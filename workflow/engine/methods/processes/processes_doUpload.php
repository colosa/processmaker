<?php
sleep( 1 );

if (isset( $_SESSION['processes_upload'] )) {
    $form = $_SESSION['processes_upload'];
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

if ($_FILES['form']['error'] == "0") {
    G::uploadFile( $_FILES['form']['tmp_name'], $sDirectory, $_FILES['form']['name'] );
    $msg = "Uploaded (" . (round( (filesize( $sDirectory . $_FILES['form']['name'] ) / 1024) * 10 ) / 10) . " kb)";
    $result = 1;
    //echo $sDirectory.$_FILES['form']['name'];
} else {
    $msg = "Failed";
    $result = 0;
}

echo "{'result': $result, 'msg':'$msg'}";