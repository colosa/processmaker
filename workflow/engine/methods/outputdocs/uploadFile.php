<?php
try {
    require_once 'classes/model/OutputDocument.php';

    if (empty( $_FILES['form'] ) || $_FILES['form']['name']['OUT_DOC_FILE'] == '') {
        throw (new Exception( 'you must upload a file.' ));
    }
    $uid = $_POST['form']['OUT_DOC_UID'];
    $oOutputDocument = new OutputDocument();
    $aFields = $oOutputDocument->load( $uid );
    $type = $aFields['OUT_DOC_TYPE'];

    $aExtension = explode( '.', strtolower( basename( $_FILES['form']['name']['OUT_DOC_FILE'] ) ) );
    $extension = $aExtension[count( $aExtension ) - 1];
    if ($extension != 'jrxml' && $type == 'JRXML') {
        throw (new Exception( "'$extension' is not a valid extension, you must upload a .jrxml file." ));
    }

    if ($extension != 'pdf' && $type == 'ACROFORM') {
        throw (new Exception( "'$extension' is not a valid extension, you must upload a .pdf file." ));
    }
    $fileJrxml = PATH_DYNAFORM . $aFields['PRO_UID'] . PATH_SEP . $aFields['OUT_DOC_UID'] . '.' . $extension;
    if (! empty( $_FILES['form'] )) {
        if ($_FILES['form']['error']['OUT_DOC_FILE'] == 0) {
            G::uploadFile( $_FILES['form']['tmp_name']['OUT_DOC_FILE'], dirname( $fileJrxml ), basename( $fileJrxml ) );
        }
    }
    print "<font face='Arial' size='2' >File uploaded.</font>";

} catch (Exception $e) {
    print "<font face='Arial' size='2' color='red' >Error: " . $e->getMessage() . "</font>";
}

