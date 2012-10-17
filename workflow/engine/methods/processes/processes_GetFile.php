<?php
switch ($_GET['MAIN_DIRECTORY']) {
    case 'mailTemplates':
        $sDirectory = PATH_DATA_MAILTEMPLATES . $_GET['PRO_UID'] . PATH_SEP . ($_GET['CURRENT_DIRECTORY'] != '' ? $_GET['CURRENT_DIRECTORY'] . PATH_SEP : '');
        break;
    case 'public':
        $sDirectory = PATH_DATA_PUBLIC . $_GET['PRO_UID'] . PATH_SEP . ($_GET['CURRENT_DIRECTORY'] != '' ? $_GET['CURRENT_DIRECTORY'] . PATH_SEP : '');
        break;
    default:
        die();
        break;
}
//fixed: added a file extension when is a javascript file by krlos
$_GET['FILE'] .= ($_GET['sFilextension'] != '' && $_GET['sFilextension'] == 'javascript') ? '.js' : '';

if (file_exists( $sDirectory . $_GET['FILE'] )) {
    G::streamFile( $sDirectory . $_GET['FILE'], true );
}
