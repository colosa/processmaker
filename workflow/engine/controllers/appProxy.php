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

        if (isset( $httpData->appUid ) && trim( $httpData->appUid ) != "") {
            $appUid = $httpData->appUid;
        } else {
            if (isset( $_SESSION['APPLICATION'] )) {
                $appUid = $_SESSION['APPLICATION'];
            }
        }

        G::LoadClass( 'case' );
        $case = new Cases();
        $caseLoad = '';

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

        $respView = $case->getAllObjectsFrom( $proUid, $appUid, $tasUid, $usrUid, 'VIEW' );
        $respBlock = $case->getAllObjectsFrom( $proUid, $appUid, $tasUid, $usrUid, 'BLOCK' );

        if ($respView['CASES_NOTES'] == 0 && $respBlock['CASES_NOTES'] == 0) {
            return array ('totalCount' => 0,'notes' => array (),'noPerms' => 1
            );
        }

        //require_once ("classes/model/AppNotes.php");

        if (! isset( $appUid )) {
            throw new Exception( G::LoadTranslation('ID_RESOLVE_APPLICATION_ID' ) );
        }

        $usrUid = isset( $_SESSION['USER_LOGGED'] ) ? $_SESSION['USER_LOGGED'] : "";
        $appNotes = new AppNotes();
        $response = $appNotes->getNotesList( $appUid, '', $httpData->start, $httpData->limit );

        require_once ("classes/model/Content.php");
        $content = new Content();
        $response['array']['appTitle'] = $content->load('APP_TITLE', '', $appUid, SYS_LANG);

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
        //require_once ("classes/model/AppNotes.php");

        //extract(getExtJSParams());
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
        $response = $appNote->addCaseNote($appUid, $usrUid, $noteContent, intval($httpData->swSendMail));

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

        G::LoadClass( 'case' );
        $case = new Cases();

        if ($RBAC->userCanAccess( 'PM_ALLCASES' ) < 0 && $case->userParticipatedInCase( $httpData->appUid, $_SESSION['USER_LOGGED'] ) == 0) {
            throw new Exception( G::LoadTranslation( 'ID_NO_PERMISSION_NO_PARTICIPATED' ) );
        }

        if ($httpData->action == 'sent') { // Get the last valid delegation for participated list
            $criteria = new Criteria();
            $criteria->addSelectColumn(AppDelegationPeer::DEL_INDEX);
            $criteria->add(AppDelegationPeer::APP_UID, $httpData->appUid);
            $criteria->add(AppDelegationPeer::DEL_FINISH_DATE, null, Criteria::ISNULL);
            $criteria->addDescendingOrderByColumn(AppDelegationPeer::DEL_INDEX);
            if (AppDelegationPeer::doCount($criteria) > 0) {
                $dataset = AppDelegationPeer::doSelectRS($criteria);
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
        $labels = array ();
        $form = new Form( 'cases/cases_Resume', PATH_XMLFORM, SYS_LANG ); //este es el problema!!!!!
        G::LoadClass( 'case' );
        $case = new Cases();

        foreach ($form->fields as $fieldName => $field) {
            $labels[$fieldName] = $field->label;
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
                    $dataset = AppDelegationPeer::doSelectRS($criteria);
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

        $data[] = array ('label' => $labels['PRO_TITLE'],'value' => $processData['PRO_TITLE'],'section' => $labels['TITLE1']);
        $data[] = array ('label' => $labels['TITLE'],'value' => $applicationFields['TITLE'],'section' => $labels['TITLE1']);
        $data[] = array ('label' => $labels['APP_NUMBER'],'value' => $applicationFields['APP_NUMBER'],'section' => $labels['TITLE1']);
        $data[] = array ('label' => $labels['STATUS'],'value' => $applicationFields['STATUS'],'section' => $labels['TITLE1']);
        $data[] = array ('label' => $labels['APP_UID'],'value' => $applicationFields['APP_UID'],'section' => $labels['TITLE1']);
        $data[] = array ('label' => $labels['CREATOR'],'value' => $applicationFields['CREATOR'],'section' => $labels['TITLE1']);
        $data[] = array ('label' => $labels['CREATE_DATE'],'value' => $applicationFields['CREATE_DATE'],'section' => $labels['TITLE1']);
        $data[] = array ('label' => $labels['UPDATE_DATE'],'value' => $applicationFields['UPDATE_DATE'],'section' => $labels['TITLE1']);
        $data[] = array ('label' => $labels['DESCRIPTION'],'value' => $applicationFields['DESCRIPTION'],'section' => $labels['TITLE1']);

        // note added by krlos pacha carlos[at]colosa[dot]com
        //getting this field if it doesn't exist. Related 7994 bug
        $taskData['TAS_TITLE'] = (array_key_exists( 'TAS_TITLE', $taskData )) ? $taskData['TAS_TITLE'] : Content::Load( "TAS_TITLE", "", $applicationFields['TAS_UID'], SYS_LANG );

        $data[] = array ('label' => $labels['TAS_TITLE'],'value' => $taskData['TAS_TITLE'],'section' => $labels['TITLE2']);
        $data[] = array ('label' => $labels['CURRENT_USER'],'value' => $currentUser,'section' => $labels['TITLE2']);
        $data[] = array ('label' => $labels['DEL_DELEGATE_DATE'],'value' => $applicationFields['DEL_DELEGATE_DATE'],'section' => $labels['TITLE2']);
        $data[] = array ('label' => $labels['DEL_INIT_DATE'],'value' => $applicationFields['DEL_INIT_DATE'],'section' => $labels['TITLE2']);
        $data[] = array ('label' => $labels['DEL_TASK_DUE_DATE'],'value' => $applicationFields['DEL_TASK_DUE_DATE'],'section' => $labels['TITLE2']);
        $data[] = array ('label' => $labels['DEL_FINISH_DATE'],'value' => $applicationFields['DEL_FINISH_DATE'],'section' => $labels['TITLE2']);
        //$data[] = array('label'=>$labels['DYN_UID'] ,           'value' => $processData['PRO_DYNAFORMS']['PROCESS'];, 'section'=>$labels['DYN_UID']);
        return $data;
    }
}

