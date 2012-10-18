<?php

class webEntryProxy extends HttpProxyController
{
    //Delete Web Entry
    public function delete ($params)
    {
        require_once 'classes/model/Event.php';
        $pro_uid = $params->PRO_UID;
        $filename = $params->FILE_NAME;
        $filename = $filename . '.php';
        $evn_uid = $params->EVN_UID;

        $event = new Event();

        $editEvent = array ();
        $editEvent['EVN_UID'] = $evn_uid;
        $editEvent['EVN_ACTION'] = '';
        $editEvent['EVN_CONDITIONS'] = null;
        $event->update( $editEvent );

        unlink( PATH_DATA . "sites" . PATH_SEP . SYS_SYS . PATH_SEP . "public" . PATH_SEP . $pro_uid . PATH_SEP . $filename );
        unlink( PATH_DATA . "sites" . PATH_SEP . SYS_SYS . PATH_SEP . "public" . PATH_SEP . $pro_uid . PATH_SEP . str_replace( ".php", "Post", $filename ) . ".php" );

        $this->success = true;
        $this->msg = G::LoadTranslation( 'ID_WEB_ENTRY_SUCCESS_DELETE' );
    }
    //Check Credentials
    public function checkCredentials ($params)
    {
        require_once 'classes/model/Event.php';
        require_once 'classes/model/Users.php';
        require_once 'classes/model/TaskUser.php';
        require_once 'classes/model/GroupUser.php';
        $sPRO_UID = $params->PRO_UID;
        $sEVN_UID = $params->EVN_UID;
        //$sDYNAFORM = $params->DYN_UID;
        $sWS_USER = trim( $params->WS_USER );
        $sWS_PASS = trim( $params->WS_PASS );

        if (G::is_https()) {
            $http = 'https://';
        } else {
            $http = 'http://';
        }

        $endpoint = $http . $_SERVER['HTTP_HOST'] . '/sys' . SYS_SYS . '/' . SYS_LANG . '/' . SYS_SKIN . '/services/wsdl2';
        @$client = new SoapClient( $endpoint );

        $user = $sWS_USER;
        $pass = $sWS_PASS;

        $parameters = array ('userid' => $user,'password' => $pass);
        $result = $client->__SoapCall( 'login', array ($parameters) );

        $fields['status_code'] = $result->status_code;
        $fields['message'] = 'ProcessMaker WebService version: ' . $result->version . "\n" . $result->message;
        $fields['version'] = $result->version;
        $fields['time_stamp'] = $result->timestamp;
        $messageCode = true;
        $message = $result->message;

        G::LoadClass( 'Task' );
        //G::LoadClass ( 'Event' );
        G::LoadClass( 'User' );
        G::LoadClass( 'TaskUser' );
        G::LoadClass( 'Groupwf' );

        $event = new Event();
        $event->load( $sEVN_UID );
        $sTASKS = $event->getEvnTasUidTo();

        $task = new Task();
        $task->load( $sTASKS );
        $sTASKS_SEL = $task->getTasTitle();

        if (! class_exists( 'GroupUser' )) {
            G::LoadClass( 'GroupUser' );
        }
        // if the user has been authenticated, then check if has the rights or
        // permissions to create the webentry
        if ($result->status_code == 0) {
            $oCriteria = new Criteria( 'workflow' );
            $oCriteria->addSelectColumn( UsersPeer::USR_UID );
            $oCriteria->addSelectColumn( TaskUserPeer::USR_UID );
            $oCriteria->addSelectColumn( TaskUserPeer::TAS_UID );
            $oCriteria->addJoin( TaskUserPeer::USR_UID, UsersPeer::USR_UID, Criteria::LEFT_JOIN );
            $oCriteria->add( TaskUserPeer::TAS_UID, $sTASKS );
            $oCriteria->add( UsersPeer::USR_USERNAME, $sWS_USER );
            //$oCriteria->add(TaskUserPeer::TU_RELATION,1);
            $userIsAssigned = TaskUserPeer::doCount( $oCriteria );
            // if the user is not assigned directly, maybe a have the task a group with the user
            if ($userIsAssigned < 1) {
                $oCriteria = new Criteria( 'workflow' );
                $oCriteria->addSelectColumn( UsersPeer::USR_UID );
                $oCriteria->addJoin( UsersPeer::USR_UID, GroupUserPeer::USR_UID, Criteria::LEFT_JOIN );
                $oCriteria->addJoin( GroupUserPeer::GRP_UID, TaskUserPeer::USR_UID, Criteria::LEFT_JOIN );
                $oCriteria->add( TaskUserPeer::TAS_UID, $sTASKS );
                $oCriteria->add( UsersPeer::USR_USERNAME, $sWS_USER );
                $userIsAssigned = GroupUserPeer::doCount( $oCriteria );
                if (! ($userIsAssigned >= 1)) {
                    $messageCode = false;
                    $message = "The User \"" . $sWS_USER . "\" doesn't have the task \"" . $sTASKS_SEL . "\" assigned.";
                }
            }

        } else {
            $messageCode = false;
        }

        $this->success = $messageCode;
        $this->msg = $message;
    }
    //Save New WebEntry
    public function save ($params)
    {
        require_once 'classes/model/Event.php';
        global $G_FORM;
        $sPRO_UID = $params->pro_uid;
        $sEVN_UID = $params->evn_uid;
        $sDYNAFORM = $params->initDyna;
        $sWS_USER = trim( $params->username );
        $sWS_PASS = trim( $params->password );
        $sWS_ROUNDROBIN = '';
        $sWE_USR = '';
        $xDYNA = $params->dynaform;

        if ($xDYNA != '') {
            $pro_uid = $params->pro_uid;
            $filename = $xDYNA;
            $filename = $filename . '.php';
            unlink( PATH_DATA . "sites" . PATH_SEP . SYS_SYS . PATH_SEP . "public" . PATH_SEP . $pro_uid . PATH_SEP . $filename );
            unlink( PATH_DATA . "sites" . PATH_SEP . SYS_SYS . PATH_SEP . "public" . PATH_SEP . $pro_uid . PATH_SEP . str_replace( ".php", "Post", $filename ) . ".php" );
        }

        //return $params;


        G::LoadClass( "system" );

        $pathProcess = PATH_DATA_SITE . 'public' . PATH_SEP . $sPRO_UID . PATH_SEP;
        G::mk_dir( $pathProcess, 0777 );

        $oEvent = new Event();
        $oEvent->load( $sEVN_UID );
        $sTASKS = $oEvent->getEvnTasUidTo();

        $oTask = new Task();
        $oTask->load( $sTASKS );
        $tas_title = $oTask->getTasTitle();

        if (G::is_https()) {
            $http = 'https://';
        } else {
            $http = 'http://';
        }

        $sContent = '';
        $SITE_PUBLIC_PATH = '';
        if (file_exists( $SITE_PUBLIC_PATH . '' )) {
        }

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
        //creating the third file, only if this wsClient.php file doesn't exists.
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

        $aDataEvent['EVN_UID'] = $sEVN_UID;
        $aDataEvent['EVN_RELATED_TO'] = 'MULTIPLE';
        $aDataEvent['EVN_ACTION'] = $sDYNAFORM;
        $aDataEvent['EVN_CONDITIONS'] = $sWS_USER;
        $output = $oEvent->update( $aDataEvent );

        $link = $http . $_SERVER['HTTP_HOST'] . '/sys' . SYS_SYS . '/' . SYS_LANG . '/' . SYS_SKIN . '/' . $sPRO_UID . '/' . $dynTitle . '.php';

        $this->success = true;
        $this->msg = G::LoadTranslation( 'ID_WEB_ENTRY_SUCCESS_NEW' );
        $this->W_LINK = $link;
        $this->TAS_TITLE = $tas_title;
        $this->DYN_TITLE = $dynTitle;
        $this->USR_UID = $sWS_USER;
    }

    public function load ($params)
    {
        G::LoadClass( 'processMap' );
        $oProcessMap = new processMap( new DBConnection() );
        $PRO_UID = $params->PRO_UID;
        $EVN_UID = $params->EVN_UID;
        $sOutput = $oProcessMap->listNewWebEntry( $PRO_UID, $EVN_UID );
        return $sOutput;
    }
}

