<?php
global $G_FORM;
$sPRO_UID = $oData->PRO_UID;
$sTASKS = $oData->TASKS;
$sDYNAFORM = $oData->DYNAFORM;
$sWE_TYPE = $oData->WE_TYPE;
$sWS_USER = $oData->WS_USER;
$sWS_PASS = $oData->WS_PASS;
$sWS_ROUNDROBIN = $oData->WS_ROUNDROBIN;
$sWE_USR = $oData->WE_USR;

$withWS = $sWE_TYPE == 'WS';

G::LoadClass( "system" );

try {
    $pathProcess = PATH_DATA_SITE . 'public' . PATH_SEP . $sPRO_UID . PATH_SEP;
    G::mk_dir( $pathProcess, 0777 );

    $oTask = new Task();
    $TaskFields = $oTask->load( $sTASKS );
    $WE_EVN_UID = $oTask->getStartingEvent( $sTASKS );
    if ($TaskFields['TAS_ASSIGN_TYPE'] != 'BALANCED') {
        throw (new Exception( "The task '" . $TaskFields['TAS_TITLE'] . "' doesn't have a valid assignment type. The task needs to have a 'Cyclical Assignment'." ));
    }

    G::LoadClass( 'tasks' );
    $oTask = new Tasks();
    $user = $oTask->assignUsertoTask( $sTASKS );

    if ($user == 0) {
        throw (new Exception( G::LoadTranslation('ID_TASK') . "'" . $TaskFields['TAS_TITLE'] . "'" . G::LoadTranslation('ID_NOT_HAVE_USERS')));
    }

    if (G::is_https())
        $http = 'https://';
    else
        $http = 'http://';

    $sContent = '';

    if ($withWS) {
        //creating sys.info;
        $SITE_PUBLIC_PATH = '';
        if (file_exists( $SITE_PUBLIC_PATH . '' )) {
        }

        //creating the first file
        require_once 'classes/model/Dynaform.php';
        $oDynaform = new Dynaform();
        $aDynaform = $oDynaform->load( $sDYNAFORM );
        $dynTitle = str_replace( ' ', '_', str_replace( '/', '_', $aDynaform['DYN_TITLE'] ) );
        $sContent = "<?php\n";
        $sContent .= "global \$_DBArray;\n";
        $sContent .= "if (!isset(\$_DBArray)) {\n";
        $sContent .= "  \$_DBArray = array();\n";
        $sContent .= "}\n";
        $sContent .= "\$_SESSION['PROCESS'] = '" . $sPRO_UID . "';\n";
        $sContent .= "\$_SESSION['CURRENT_DYN_UID'] = '" . $sDYNAFORM . "';\n";
        $sContent .= "\$G_PUBLISH = new Publisher;\n";
        $sContent .= "\$G_PUBLISH->AddContent('dynaform', 'xmlform', '" . $sPRO_UID . '/' . $sDYNAFORM . "', '', array(), '" . $dynTitle . 'Post.php' . "');\n";
        $sContent .= "G::RenderPage('publish', 'blank');";
        file_put_contents( $pathProcess . $dynTitle . '.php', $sContent );
        //creating the second file, the  post file who receive the post form.
        $pluginTpl = PATH_CORE . 'templates' . PATH_SEP . 'processes' . PATH_SEP . 'webentryPost.tpl';
        $template = new TemplatePower( $pluginTpl );
        $template->prepare();
        $template->assign( 'wsdlUrl', $http . $_SERVER['HTTP_HOST'] . '/sys' . SYS_SYS . '/' . SYS_LANG . '/' . SYS_SKIN . '/services/wsdl2' );
        $template->assign( 'wsUploadUrl', $http . $_SERVER['HTTP_HOST'] . '/sys' . SYS_SYS . '/' . SYS_LANG . '/' . SYS_SKIN . '/services/upload' );
        $template->assign( 'processUid', $sPRO_UID );
        $template->assign( 'dynaformUid', $sDYNAFORM );
        $template->assign( 'taskUid', $sTASKS );
        $template->assign( 'wsUser', $sWS_USER );
        $template->assign( 'wsPass', 'md5:' . md5( $sWS_PASS ) );
        $template->assign( 'wsRoundRobin', $sWS_ROUNDROBIN );

        if ($sWE_USR == "2") {
            $template->assign( 'USR_VAR', "\$cInfo = ws_getCaseInfo(\$caseId);\n\t  \$USR_UID = \$cInfo->currentUsers->userId;" );
        } else {
            $template->assign( 'USR_VAR', '$USR_UID = -1;' );
        }

        $template->assign( 'dynaform', $dynTitle );
        $template->assign( 'timestamp', date( 'l jS \of F Y h:i:s A' ) );
        $template->assign( 'ws', SYS_SYS );
        $template->assign( 'version', System::getVersion() );

        $fileName = $pathProcess . $dynTitle . 'Post.php';
        file_put_contents( $fileName, $template->getOutputContent() );
        //creating the third file, only if this wsClient.php file doesn't exist.
        $fileName = $pathProcess . 'wsClient.php';
        $pluginTpl = PATH_CORE . 'test' . PATH_SEP . 'unit' . PATH_SEP . 'ws' . PATH_SEP . 'wsClient.php';

        if (file_exists( $fileName )) {
            if (filesize( $fileName ) != filesize( $pluginTpl )) {
                @copy( $fileName, $pathProcess . 'wsClient.php.bck' );
                @unlink( $fileName );

                $template = new TemplatePower( $pluginTpl );
                $template->prepare();
                file_put_contents( $fileName, $template->getOutputContent() );
            }
        } else {
            $template = new TemplatePower( $pluginTpl );
            $template->prepare();
            file_put_contents( $fileName, $template->getOutputContent() );
        }

        require_once 'classes/model/Event.php';
        $oEvent = new Event();
        $aDataEvent = array ();

        $aDataEvent['EVN_UID'] = $WE_EVN_UID; //$oData->WE_EVN_UID;
        $aDataEvent['EVN_RELATED_TO'] = 'MULTIPLE';
        $aDataEvent['EVN_ACTION'] = $sDYNAFORM;
        $aDataEvent['EVN_CONDITIONS'] = $sWS_USER;
        $output = $oEvent->update( $aDataEvent );
        //Show link
        $link = $http . $_SERVER['HTTP_HOST'] . '/sys' . SYS_SYS . '/' . SYS_LANG . '/' . SYS_SKIN . '/' . $sPRO_UID . '/' . $dynTitle . '.php';
        print $link;
        //print "\n<a href='$link' target='_new' > $link </a>";


    } else {
        $G_FORM = new Form( $sPRO_UID . '/' . $sDYNAFORM, PATH_DYNAFORM, SYS_LANG, false );
        $G_FORM->action = $http . $_SERVER['HTTP_HOST'] . '/sys' . SYS_SYS . '/' . SYS_LANG . '/' . SYS_SKIN . '/services/cases_StartExternal.php';

        $scriptCode = '';
        $scriptCode = $G_FORM->render( PATH_CORE . 'templates/' . 'xmlform' . '.html', $scriptCode );
        $scriptCode = str_replace( '/controls/', $http . $_SERVER['HTTP_HOST'] . '/controls/', $scriptCode );
        $scriptCode = str_replace( '/js/maborak/core/images/', $http . $_SERVER['HTTP_HOST'] . '/js/maborak/core/images/', $scriptCode );

        //render the template
        $pluginTpl = PATH_CORE . 'templates' . PATH_SEP . 'processes' . PATH_SEP . 'webentry.tpl';
        $template = new TemplatePower( $pluginTpl );
        $template->prepare();
        require_once 'classes/model/Step.php';
        $oStep = new Step();
        $sUidGrids = $oStep->lookingforUidGrids( $sPRO_UID, $sDYNAFORM );

        $browserCacheFilesUid = G::browserCacheFilesGetUid();

        $template->assign("URL_MABORAK_JS", G::browserCacheFilesUrl("/js/maborak/core/maborak.js"));
        $template->assign("siteUrl", $http . $_SERVER["HTTP_HOST"]);
        $template->assign("sysSys", SYS_SYS);
        $template->assign("sysLang", SYS_LANG);
        $template->assign("sysSkin", SYS_SKIN);
        $template->assign("processUid", $sPRO_UID);
        $template->assign("dynaformUid", $sDYNAFORM);
        $template->assign("taskUid", $sTASKS);
        $template->assign("dynFileName", $sPRO_UID . "/" . $sDYNAFORM);
        $template->assign("formId", $G_FORM->id);
        $template->assign("scriptCode", $scriptCode);

        if (sizeof( $sUidGrids ) > 0) {
            foreach ($sUidGrids as $k => $v) {
                $template->newBlock( 'grid_uids' );
                $template->assign( 'siteUrl', $http . $_SERVER['HTTP_HOST'] );
                $template->assign( 'gridFileName', $sPRO_UID . '/' . $v );
            }
        }

        print_r( '<textarea cols="70" rows="20">' . htmlentities( str_replace( '</body>', '</form></body>', str_replace( '</form>', '', $template->getOutputContent() ) ) ) . '</textarea>' );
    }

} catch (Exception $e) {
    $G_PUBLISH = new Publisher();
    $aMessage['MESSAGE'] = $e->getMessage();
    $G_PUBLISH->AddContent( 'xmlform', 'xmlform', 'login/showMessage', '', $aMessage );
    G::RenderPage( 'publish', 'raw' );
}

