<?php

$action = isset( $_REQUEST['action'] ) ? $_REQUEST['action'] :'';

// Function call from ajax_function for calling to lookForNameOutput.
if ($action == '') {
    $action = isset( $_REQUEST['function'] ) ? $_REQUEST['function'] : '';
}

switch ($action) {
    case 'setTemplateFile':
        //print_r($_FILES);
        $_SESSION['outpudocs_tmpFile'] = PATH_DATA . $_FILES['templateFile']['name'];
        //    file_put_contents($_FILES['templateFile']['name'], file_get_contents($_FILES['templateFile']['tmp_name']));
        copy( $_FILES['templateFile']['tmp_name'], $_SESSION['outpudocs_tmpFile'] );
        $result = new stdClass();

        $result->success = true;
        $result->msg = 'success - saved ' . $_SESSION['outpudocs_tmpFile'];
        echo G::json_encode( $result );
        break;

    case 'getTemplateFile':
        $aExtensions = array ("exe","com","dll","ocx","fon","ttf","doc","xls","mdb","rtf","bin","jpeg","jpg","jif","jfif","gif","tif","tiff","png","bmp","pdf","aac","mp3","mp3pro","vorbis","realaudio","vqf","wma","aiff","flac","wav","midi","mka","ogg","jpeg","ilbm","tar","zip","rar","arj","gzip","bzip2","afio","kgb","gz","asf","avi","mov","iff","ogg","ogm","mkv","3gp"
        );
        $sFileName = strtolower( $_SESSION['outpudocs_tmpFile'] );
        $strRev = strrev( $sFileName );
        $searchPos = strpos( $strRev, '.' );
        $pos = (strlen( $sFileName ) - 1) - $searchPos;
        $sExtension = substr( $sFileName, $pos + 1, strlen( $sFileName ) );
        if (! in_array( $sExtension, $aExtensions ))
            echo $content = file_get_contents( $_SESSION['outpudocs_tmpFile'] );
        break;

    case 'loadTemplateContent':
        require_once 'classes/model/OutputDocument.php';
        $ooutputDocument = new OutputDocument();
        if (isset( $_POST['OUT_DOC_UID'] )) {
            $aFields = $ooutputDocument->load( $_POST['OUT_DOC_UID'] );

            echo $aFields['OUT_DOC_TEMPLATE'];
        }
        break;

    case 'lookForNameOutput':
        require_once ('classes/model/Content.php');
        require_once ("classes/model/OutputDocument.php");

        $snameInput = urldecode( $_POST['NAMEOUTPUT'] );
        $sPRO_UID = urldecode( $_POST['proUid'] );

        $oCriteria = new Criteria( 'workflow' );
        $oCriteria->addSelectColumn( OutputDocumentPeer::OUT_DOC_UID );
        $oCriteria->add( OutputDocumentPeer::PRO_UID, $sPRO_UID );
        $oDataset = OutputDocumentPeer::doSelectRS( $oCriteria );
        $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );
        $flag = true;
        while ($oDataset->next() && $flag) {
            $aRow = $oDataset->getRow();

            $oCriteria1 = new Criteria( 'workflow' );
            $oCriteria1->addSelectColumn( 'COUNT(*) AS OUTPUTS' );
            $oCriteria1->add( ContentPeer::CON_CATEGORY, 'OUT_DOC_TITLE' );
            $oCriteria1->add( ContentPeer::CON_ID, $aRow['OUT_DOC_UID'] );
            $oCriteria1->add( ContentPeer::CON_VALUE, $snameInput );
            $oCriteria1->add( ContentPeer::CON_LANG, SYS_LANG );
            $oDataset1 = ContentPeer::doSelectRS( $oCriteria1 );
            $oDataset1->setFetchmode( ResultSet::FETCHMODE_ASSOC );
            $oDataset1->next();
            $aRow1 = $oDataset1->getRow();

            if ($aRow1['OUTPUTS'])
                $flag = false;
        }
        echo $flag;
        // G::json_encode($flag);
        break;

    case 'loadOutputEditor':
        global $G_PUBLISH;
        $G_PUBLISH = new Publisher();
        $fcontent  = '';
        $proUid    = '';
        $filename  = '';
        $title  = '';

        require_once 'classes/model/OutputDocument.php';
        $oOutputDocument = new OutputDocument();
        if (isset( $_REQUEST['OUT_DOC_UID'] )) {
            $aFields = $oOutputDocument->load( $_REQUEST['OUT_DOC_UID'] );
            $fcontent = $aFields['OUT_DOC_TEMPLATE'];
            $proUid   = $aFields['PRO_UID'];
            $filename = $aFields['OUT_DOC_FILENAME'];
            $title    = $aFields['OUT_DOC_TITLE'];
        }

        $aData = Array ( 'PRO_UID' => $proUid,'OUT_DOC_TEMPLATE' => $fcontent, 'FILENAME' => $filename, 'OUT_DOC_UID'=> $_REQUEST['OUT_DOC_UID'], 'OUT_DOC_TITLE'=> $title);

        $G_PUBLISH->AddContent( 'xmlform', 'xmlform', 'outputdocs/outputdocs_Edit', '', $aData );
        
        G::RenderPage( 'publish', 'raw' );
        
        break;
}