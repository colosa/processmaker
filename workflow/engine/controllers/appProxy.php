<?php
if (!isset($_SESSION['USER_LOGGED'])) {
    $response = new stdclass();
    $response->message = G::LoadTranslation('ID_LOGIN_AGAIN');
    $response->lostSession = true;
    print G::json_encode( $response );
    die();
}
/**
 * App controller
 *
 * @author Erik Amaru Ortiz <erik@colosa.com, aortiz.erik@gmail.com>
 * @herits Controller
 * @access public
 */

class AppProxy extends HttpProxyController
{

    /**
     * Get Notes List
     *
     * @param int $httpData->start
     * @param int $httpData->limit
     * @param string $httpData->appUid (optionalif it is not passed try use $_SESSION['APPLICATION'])
     * @return array containg the case notes
     */
    function getNotesList ($httpData)
    {
        if (!isset($_SESSION['USER_LOGGED'])) {
            $response = new stdclass();
            $response->message = G::LoadTranslation('ID_LOGIN_AGAIN');
            $response->lostSession = true;
            print G::json_encode( $response );
            die();
        }

        $appUid = null;

        if (isset($httpData->appUid) && trim($httpData->appUid) != "") {
            $appUid = trim($httpData->appUid);
        } else {
            if (isset($_SESSION["APPLICATION"])) {
                $appUid = $_SESSION["APPLICATION"];
            }
        }

        $delIndex = 0;

        if (isset($httpData->delIndex) && trim($httpData->delIndex) != "") {
            $delIndex = (int)(trim($httpData->delIndex));
        } else {
            if (isset($_SESSION["INDEX"])) {
                $delIndex = (int)($_SESSION["INDEX"]);
            }
        }

        if (!isset($appUid)) {
            throw new Exception(G::LoadTranslation("ID_RESOLVE_APPLICATION_ID"));
        }

        $case = new Cases();

        if (!isset($_SESSION['PROCESS']) && !isset($httpData->pro)) {
            $caseLoad = $case->loadCase($appUid);
            $httpData->pro = $caseLoad['PRO_UID'];
        }

        if(!isset($httpData->pro) || empty($httpData->pro) )
        {
            $proUid = $_SESSION['PROCESS'];
        } else {
            $proUid = $httpData->pro;
        }

        if(!isset($httpData->tas) || empty($httpData->tas))
        {
            $tasUid = $_SESSION['TASK'];
        } else {
            $tasUid = $httpData->tas;
        }
        //$proUid = (!isset($httpData->pro)) ? $_SESSION['PROCESS'] : $httpData->pro;
        //$tasUid = (!isset($httpData->tas)) ? ((isset($_SESSION['TASK'])) ? $_SESSION['TASK'] : '') : $httpData->tas;
        $usrUid = $_SESSION['USER_LOGGED'];

        $respView  = $case->getAllObjectsFrom($proUid, $appUid, $tasUid, $usrUid, "VIEW",  $delIndex);
        $respBlock = $case->getAllObjectsFrom($proUid, $appUid, $tasUid, $usrUid, "BLOCK", $delIndex);

        if ($respView['CASES_NOTES'] == 0 && $respBlock['CASES_NOTES'] == 0) {
            return array ('totalCount' => 0,'notes' => array (),'noPerms' => 1
            );
        }

        $usrUid = isset( $_SESSION['USER_LOGGED'] ) ? $_SESSION['USER_LOGGED'] : "";
        $appNotes = new AppNotes();
        $response = $appNotes->getNotesList( $appUid, '', $httpData->start, $httpData->limit );

        require_once ("classes/model/Application.php");
        $oApplication = new Application();
        $aApplication = $oApplication->Load($appUid);
        $response['array']['appTitle'] = $aApplication['APP_TITLE'];

        return $response['array'];
    }

    /**
     * post Note Action
     *
     * @param string $httpData->appUid (optional, if it is not passed try use $_SESSION['APPLICATION'])
     * @return array containg the case notes
     */
    function postNote ($httpData)
    {
        if (isset( $httpData->appUid ) && trim( $httpData->appUid ) != "") {
            $appUid = $httpData->appUid;
        } else {
            $appUid = $_SESSION['APPLICATION'];
        }

        if (! isset( $appUid )) {
            throw new Exception(G::LoadTranslation("ID_CANT_RESOLVE_APPLICATION"));
        }

        $usrUid = (isset( $_SESSION['USER_LOGGED'] )) ? $_SESSION['USER_LOGGED'] : "";
        $noteContent = addslashes( $httpData->noteText );

        //Disabling the controller response because we handle a special behavior
        $this->setSendResponse(false);

        //Add note case
        $appNote = new AppNotes();
        try {
            $response = $appNote->addCaseNote($appUid, $usrUid, $noteContent, intval($httpData->swSendMail));
        } catch (Exception $error) {
            $response = new stdclass();
            $response->success  = 'success';
            $response->message  = G::LoadTranslation('ID_ERROR_SEND_NOTIFICATIONS');
            $response->message .= '<br /><br />' . $error->getMessage() . '<br /><br />';
            $response->message .= G::LoadTranslation('ID_CONTACT_ADMIN');
            die(G::json_encode($response));
        }

        //Send the response to client
        @ini_set("implicit_flush", 1);
        ob_start();
        if (!isset($_SESSION['USER_LOGGED'])) {
            $response = new stdclass();
            $response->message = G::LoadTranslation('ID_LOGIN_AGAIN');
            $response->lostSession = true;
            print G::json_encode( $response );
            die();
        }
        echo G::json_encode($response);
        @ob_flush();
        @flush();
        @ob_end_flush();
        ob_implicit_flush(1);
    }

    /**
     * request to open the case summary
     *
     * @param string $httpData->appUid
     * @param string $httpData->delIndex
     * @return object bool $result->succes, string $result->message(is an exception was thrown), string $result->dynUid
     */
    function requestOpenSummary ($httpData)
    {
        global $RBAC;
        $this->success = true;
        $this->dynUid = '';

        switch ($RBAC->userCanAccess( 'PM_CASES' )) {
            case - 2:
                throw new Exception( G::LoadTranslation( 'ID_USER_HAVENT_RIGHTS_SYSTEM' ) );
                break;
            case - 1:
                throw new Exception( G::LoadTranslation( 'ID_USER_HAVENT_RIGHTS_PAGE' ) );
                break;
        }

        $case = new Cases();
        
        if ($httpData->action == 'sent') { // Get the last valid delegation for participated list
            $criteria = new Criteria();
            $criteria->addSelectColumn(AppDelegationPeer::DEL_INDEX);
            $criteria->add(AppDelegationPeer::APP_UID, $httpData->appUid);
            $criteria->add(AppDelegationPeer::DEL_FINISH_DATE, null, Criteria::ISNULL);
            $criteria->addDescendingOrderByColumn(AppDelegationPeer::DEL_INDEX);
            if (AppDelegationPeer::doCount($criteria) > 0) {
                $dataset = AppDelegationPeer::doSelectRS($criteria, Propel::getDbConnection('workflow_ro'));
                $dataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
                $dataset->next();
                $row = $dataset->getRow();
                $httpData->delIndex = $row['DEL_INDEX'];
            }
        }
        $applicationFields = $case->loadCase( $httpData->appUid, $httpData->delIndex );
        $process = new Process();
        $processData = $process->load( $applicationFields['PRO_UID'] );

        if (isset( $processData['PRO_DYNAFORMS']['PROCESS'] )) {
            $this->dynUid = $processData['PRO_DYNAFORMS']['PROCESS'];
        }

        $_SESSION['_applicationFields'] = $applicationFields;
        $_SESSION['_processData'] = $processData;
        $_SESSION['APPLICATION'] = $httpData->appUid;
        $_SESSION['INDEX'] = $httpData->delIndex;
        $_SESSION['PROCESS'] = $applicationFields['PRO_UID'];
        $_SESSION['TASK'] = $applicationFields['TAS_UID'];
        $_SESSION['STEP_POSITION'] = '';
    }

    /**
     * get the case summary data
     *
     * @param string $httpData->appUid
     * @param string $httpData->delIndex
     * @return array containg the case summary data
     */
    function getSummary ($httpData)
    {
        $labelsCaseProperties = array ();
        $labelsCurrentTaskProperties = array ();
        $labelTitleCurrentTasks = array ();

        $formCaseProperties = new Form( 'cases/cases_Resume', PATH_XMLFORM, SYS_LANG );
        $formCaseTitle = new Form( 'cases/cases_Resume_Current_Task_Title', PATH_XMLFORM, SYS_LANG ); 
        $formCurrentTaskProperties = new Form( 'cases/cases_Resume_Current_Task', PATH_XMLFORM, SYS_LANG ); 

        $case = new Cases();

        foreach ($formCaseProperties->fields as $fieldName => $field) {
            $labelsCaseProperties[$fieldName] = $field->label;
        }

        foreach ($formCaseTitle->fields as $fieldName => $field) {
            $labelTitleCurrentTasks[$fieldName] = $field->label;
        }

        foreach ($formCurrentTaskProperties->fields as $fieldName => $field) {
            $labelsCurrentTaskProperties[$fieldName] = $field->label;
        }

        if (isset( $_SESSION['_applicationFields'] ) && $_SESSION['_processData']) {
            $applicationFields = $_SESSION['_applicationFields'];
            unset( $_SESSION['_applicationFields'] );
            $processData = $_SESSION['_processData'];
            unset( $_SESSION['_processData'] );
        } else {
            if ($httpData->action == 'sent') { // Get the last valid delegation for participated list
                $criteria = new Criteria();
                $criteria->addSelectColumn(AppDelegationPeer::DEL_INDEX);
                $criteria->add(AppDelegationPeer::APP_UID, $httpData->appUid);
                $criteria->add(AppDelegationPeer::DEL_FINISH_DATE, null, Criteria::ISNULL);
                $criteria->addDescendingOrderByColumn(AppDelegationPeer::DEL_INDEX);
                if (AppDelegationPeer::doCount($criteria) > 0) {
                    $dataset = AppDelegationPeer::doSelectRS($criteria, Propel::getDbConnection('workflow_ro') );
                    $dataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
                    $dataset->next();
                    $row = $dataset->getRow();
                    $httpData->delIndex = $row['DEL_INDEX'];
                }
            }
            $applicationFields = $case->loadCase( $httpData->appUid, $httpData->delIndex );
            $process = new Process();
            $processData = $process->load( $applicationFields['PRO_UID'] );
        }

        $data = array ();
        $task = new Task();
        $taskData = $task->load( $applicationFields['TAS_UID'] );
        $currentUser = $applicationFields['CURRENT_USER'] != '' ? $applicationFields['CURRENT_USER'] : '[' . G::LoadTranslation( 'ID_UNASSIGNED' ) . ']';

        $data[] = array ('label' => $labelsCaseProperties['PRO_TITLE'],'value' => $processData['PRO_TITLE'],'section' => $labelsCaseProperties['TITLE1']);
        $data[] = array ("label" => $labelsCaseProperties["TITLE"], "value" => htmlentities($applicationFields["TITLE"], ENT_QUOTES, "UTF-8"), "section" => $labelsCaseProperties["TITLE1"]);
        $data[] = array ('label' => $labelsCaseProperties['APP_NUMBER'],'value' => $applicationFields['APP_NUMBER'],'section' => $labelsCaseProperties['TITLE1']);
        $data[] = array ('label' => $labelsCaseProperties['STATUS'],'value' => $applicationFields['STATUS'],'section' => $labelsCaseProperties['TITLE1']);
        $data[] = array ('label' => $labelsCaseProperties['APP_UID'],'value' => $applicationFields['APP_UID'],'section' => $labelsCaseProperties['TITLE1']);
        $data[] = array ('label' => $labelsCaseProperties['CREATOR'],'value' => $applicationFields['CREATOR'],'section' => $labelsCaseProperties['TITLE1']);
        $data[] = array ('label' => $labelsCaseProperties['CREATE_DATE'],'value' => $applicationFields['CREATE_DATE'],'section' => $labelsCaseProperties['TITLE1']);
        $data[] = array ('label' => $labelsCaseProperties['UPDATE_DATE'],'value' => $applicationFields['UPDATE_DATE'],'section' => $labelsCaseProperties['TITLE1']);
        $data[] = array ("label" => $labelsCaseProperties["DESCRIPTION"], "value" => htmlentities($applicationFields["DESCRIPTION"], ENT_QUOTES, "UTF-8"), "section" => $labelsCaseProperties["TITLE1"]);

        // note added by krlos pacha carlos[at]colosa[dot]com
        //getting this field if it doesn't exist. Related 7994 bug
        $oTask = new \Task();
        $aTasks = $oTask->load($applicationFields['TAS_UID']);
        $taskData['TAS_TITLE'] = (array_key_exists( 'TAS_TITLE', $taskData )) ? $taskData['TAS_TITLE'] : $aTasks["TAS_TITLE"];
        $data[] = array ("label" => $labelsCurrentTaskProperties["TAS_TITLE"], "value" => htmlentities($taskData["TAS_TITLE"], ENT_QUOTES, "UTF-8"), "section" => $labelTitleCurrentTasks["TITLE2"]);
        $data[] = array ('label' => $labelsCurrentTaskProperties['CURRENT_USER'],'value' => $currentUser,'section' => $labelTitleCurrentTasks['TITLE2']);
        $data[] = array ('label' => $labelsCurrentTaskProperties['DEL_DELEGATE_DATE'],'value' => $applicationFields['DEL_DELEGATE_DATE'],'section' => $labelTitleCurrentTasks['TITLE2']);
        $data[] = array ('label' => $labelsCurrentTaskProperties['DEL_INIT_DATE'],'value' => $applicationFields['DEL_INIT_DATE'],'section' => $labelTitleCurrentTasks['TITLE2']);
        $data[] = array ('label' => $labelsCurrentTaskProperties['DEL_TASK_DUE_DATE'],'value' => $applicationFields['DEL_TASK_DUE_DATE'],'section' => $labelTitleCurrentTasks['TITLE2']);
        $data[] = array ('label' => $labelsCurrentTaskProperties['DEL_FINISH_DATE'],'value' => $applicationFields['DEL_FINISH_DATE'],'section' => $labelTitleCurrentTasks['TITLE2']);
        //$data[] = array('label'=>$labelsCurrentTaskProperties['DYN_UID'] ,           'value' => $processData['PRO_DYNAFORMS']['PROCESS'];, 'section'=>$labelsCurrentTaskProperties['DYN_UID']);
        
        $data = \ProcessMaker\Util\DateTime::convertUtcToTimeZone($data);
        return $data;
    }
}

