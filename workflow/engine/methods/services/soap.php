<?php
ini_set( "soap.wsdl_cache_enabled", "0" ); // disabling WSDL cache


$wsdl = PATH_METHODS . "services" . PATH_SEP . "pmos.wsdl";

require_once ("classes/model/Application.php");
require_once ("classes/model/AppDelegation.php");
require_once ("classes/model/AppThread.php");
require_once ("classes/model/Dynaform.php");
require_once ("classes/model/Groupwf.php");
require_once ("classes/model/InputDocument.php");
require_once ("classes/model/Language.php");
require_once ("classes/model/OutputDocument.php");
require_once ("classes/model/Process.php");
require_once ("classes/model/ReportTable.php");
require_once ("classes/model/ReportVar.php");
require_once ("classes/model/Step.php");
require_once ("classes/model/StepTrigger.php");
require_once ("classes/model/Task.php");
require_once ("classes/model/TaskUser.php");
require_once ("classes/model/Triggers.php");
require_once ("classes/model/Users.php");
require_once ("classes/model/Session.php");
require_once ("classes/model/Content.php");

function login ($params)
{
    G::LoadClass( 'wsBase' );
    $ws = new wsBase();
    $res = $ws->login( $params->userid, $params->password );
    return $res->getPayloadArray();
}

function ProcessList ($params)
{
    $x = ifPermission( $params->sessionId, 'PM_FACTORY' );
    //if you are not an admin user, then this function will return only
    //your valid process
    if ($x == 0) {
        G::LoadClass( 'sessions' );
        $oSessions = new Sessions();
        $session = $oSessions->getSessionUser( $params->sessionId );
        $userId = $session['USR_UID'];

        G::LoadClass( 'wsBase' );
        $ws = new wsBase();
        $res = $ws->processListVerified( $userId );
        return $res;
    }

    G::LoadClass( 'wsBase' );
    $ws = new wsBase();
    $res = $ws->processList();
    return array ("processes" => $res
    );
}

function RoleList ($params)
{
    $x = ifPermission( $params->sessionId, 'PM_USERS' );
    if ($x == 0) {
        $result[] = array ('guid' => 24,'name' => G::LoadTranslation('ID_NOT_PRIVILEGES'));
        return $result;
    }

    G::LoadClass( 'wsBase' );
    $ws = new wsBase();
    $res = $ws->roleList();
    return array ("roles" => $res
    );
}

function GroupList ($params)
{
    $x = ifPermission( $params->sessionId, 'PM_USERS' );
    if ($x == 0) {
        $result[] = array ('guid' => 24,'name' => G::LoadTranslation('ID_NOT_PRIVILEGES'));
        return $result;
    }

    G::LoadClass( 'wsBase' );
    $ws = new wsBase();
    $res = $ws->groupList();
    return array ("groups" => $res
    );
}

function CaseList ($params)
{
    ifSessionExpiredBreakThis( $params->sessionId );
    $x = ifPermission( $params->sessionId, 'PM_CASES' );
    if ($x == 0) {
        G::LoadClass( 'wsResponse' );
        return new wsResponse( 9, G::LoadTranslation('ID_SESSION_EXPIRED') );
    }

    G::LoadClass( 'sessions' );
    $oSessions = new Sessions();
    $session = $oSessions->getSessionUser( $params->sessionId );
    $userId = $session['USR_UID'];

    G::LoadClass( 'wsBase' );
    $ws = new wsBase();
    $res = $ws->caseList( $userId );
    return array ("cases" => $res
    );
}

function UserList ($params)
{
    $x = ifPermission( $params->sessionId, 'PM_USERS' );
    if ($x == 0) {
        $result[] = array ('guid' => 24,'name' => G::LoadTranslation('ID_NOT_PRIVILEGES') );
        return $result;
    }

    G::LoadClass( 'wsBase' );
    $ws = new wsBase();
    $res = $ws->userList();
    return array ("users" => $res
    );
}

function SendMessage ($params)
{
    ifSessionExpiredBreakThis( $params->sessionId );
    $x = ifPermission( $params->sessionId, 'PM_CASES' );
    if ($x == 0) {
        G::LoadClass( 'wsResponse' );
        $result = new wsResponse( 24, G::LoadTranslation('ID_NOT_PRIVILEGES') );
        return $result;
    }
    G::LoadClass( 'wsBase' );
    $ws = new wsBase();
    $res = $ws->sendMessage( $params->caseId, $params->from, $params->to, $params->cc, $params->bcc, $params->subject, $params->template );
    return $res->getPayloadArray();
}

function getCaseInfo ($params)
{
    ifSessionExpiredBreakThis( $params->sessionId );
    $x = ifPermission( $params->sessionId, 'PM_CASES' );
    if ($x == 0) {
        G::LoadClass( 'wsResponse' );
        $result = new wsResponse( 24, "You do not have privileges" );
        return $result;
    }
    G::LoadClass( 'wsBase' );
    $ws = new wsBase();
    $res = $ws->getCaseInfo( $params->caseId, $params->delIndex );
    return $res;
}

function SendVariables ($params)
{
    ifSessionExpiredBreakThis( $params->sessionId );
    $x = ifPermission( $params->sessionId, 'PM_CASES' );
    if ($x == 0) {
        G::LoadClass( 'wsResponse' );
        $result = new wsResponse( 24, G::LoadTranslation('ID_NOT_PRIVILEGES') );
        return $result;
    }
    G::LoadClass( 'wsBase' );
    $ws = new wsBase();
    $variables = $params->variables;
    if (is_object( $variables )) {
        $Fields[$variables->name] = $variables->value;
    }

    if (is_array( $variables )) {
        foreach ($variables as $key => $val) {
            $name = $val->name;
            $value = $val->value;
            eval( '$Fields[ ' . $val->name . ' ]= $val->value ;' );
        }
    }
    $params->variables = $Fields;
    $res = $ws->sendVariables( $params->caseId, $params->variables );
    return $res->getPayloadArray();
}

function GetVariables ($params)
{
    ifSessionExpiredBreakThis( $params->sessionId );
    $x = ifPermission( $params->sessionId, 'PM_CASES' );
    if ($x == 0) {
        G::LoadClass( 'wsResponse' );
        $result = new wsResponse( 24, G::LoadTranslation('ID_NOT_PRIVILEGES') );
        return $result;
    }

    G::LoadClass( 'wsBase' );
    $ws = new wsBase();

    $res = $ws->getVariables( $params->caseId, $params->variables );
    return array ("variables" => $res
    );
}

function DerivateCase ($params)
{
    ifSessionExpiredBreakThis( $params->sessionId );
    $x = ifPermission( $params->sessionId, 'PM_CASES' );
    if ($x == 0) {
        G::LoadClass( 'wsResponse' );
        $result = new wsResponse( 24, G::LoadTranslation('ID_NOT_PRIVILEGES') );
        return $result;
    }

    $oSession = new Sessions();
    $user = $oSession->getSessionUser( $params->sessionId );

    G::LoadClass( 'wsBase' );
    $ws = new wsBase();
    $res = $ws->derivateCase( $user['USR_UID'], $params->caseId, $params->delIndex );
    return $res;

    //return  $res->getPayloadArray ( );
}

function executeTrigger ($params)
{
    ifSessionExpiredBreakThis( $params->sessionId );
    $x = ifPermission( $params->sessionId, 'PM_CASES' );
    if ($x == 0) {
        G::LoadClass( 'wsResponse' );
        $result = new wsResponse( 24, G::LoadTranslation('ID_NOT_PRIVILEGES') );
        return $result;
    }

    $oSession = new Sessions();
    $user = $oSession->getSessionUser( $params->sessionId );

    G::LoadClass( 'wsBase' );
    $ws = new wsBase();
    $delIndex = (isset( $params->delIndex )) ? $params->delIndex : 1;
    $res = $ws->executeTrigger( $user['USR_UID'], $params->caseId, $params->triggerIndex, $delIndex );
    return $res->getPayloadArray();
}

function NewCaseImpersonate ($params)
{
    ifSessionExpiredBreakThis( $params->sessionId );
    $x = ifPermission( $params->sessionId, 'PM_CASES' );
    if ($x == 0) {
        G::LoadClass( 'wsResponse' );
        $result = new wsResponse( 24, G::LoadTranslation('ID_NOT_PRIVILEGES') );
        return $result;
    }
    G::LoadClass( 'wsBase' );
    $ws = new wsBase();
    $variables = $params->variables;
    foreach ($variables as $key => $val) {
        $name = $val->name;
        $value = $val->value;
        eval( '$Fields[ ' . $val->name . ' ]= $val->value ;' );
    }
    $params->variables = $Fields;
    $res = $ws->newCaseImpersonate( $params->processId, $params->userId, $params->variables );
    return $res->getPayloadArray();
}

function NewCase ($params)
{
    G::LoadClass( 'wsBase' );
    G::LoadClass( 'sessions' );
    ifSessionExpiredBreakThis( $params->sessionId );
    $x = ifPermission( $params->sessionId, 'PM_CASES' );
    if ($x == 0) {
        G::LoadClass( 'wsResponse' );
        $result = new wsResponse( 24, G::LoadTranslation('ID_NOT_PRIVILEGES') );
        return $result;
    }

    $oSessions = new Sessions();
    $session = $oSessions->getSessionUser( $params->sessionId );
    $userId = $session['USR_UID'];
    $variables = $params->variables;

    if (! isset( $params->variables )) {
        $variables = array ();
        $Fields = array ();
    } else {
        if (is_object( $variables )) {
            /*foreach ( $variables as $key=>$val ) {
            $name  = $val->name;
            $value = $val->value;
            $Fields[ $val->name ]= $val->value ;
            }*/
            $Fields[$variables->name] = $variables->value;
        }

        if (is_array( $variables )) {
            foreach ($variables as $key => $val) {
                $name = $val->name;
                $value = $val->value;
                if (! is_object( $val->value )) {
                    eval( '$Fields[ ' . $val->name . ' ]= $val->value ;' );
                } else {
                    if (is_array( $val->value->item )) {
                        $i = 1;
                        foreach ($val->value->item as $key1 => $val1) {
                            if (isset( $val1->value )) {
                                if (is_array( $val1->value->item )) {
                                    foreach ($val1->value->item as $key2 => $val2) {
                                        $Fields[$val->name][$i][$val2->key] = $val2->value;
                                    }
                                }
                            }
                            $i ++;
                        }
                    }
                }
            }
        }
    }

    $params->variables = $Fields;
    //$result = new wsResponse (900, print_r($params->variables,1));
    //return $result;
    $ws = new wsBase();
    $res = $ws->newCase( $params->processId, $userId, $params->taskId, $params->variables );
    return $res;
}

function AssignUserToGroup ($params)
{
    ifSessionExpiredBreakThis( $params->sessionId );
    $x = ifPermission( $params->sessionId, 'PM_USERS' );
    if ($x == 0) {
        G::LoadClass( 'wsResponse' );
        $result = new wsResponse( 24, G::LoadTranslation('ID_NOT_PRIVILEGES') );
        return $result;
    }
    G::LoadClass( 'sessions' );
    $sessions = new Sessions();
    $user = $sessions->getSessionUser( $params->sessionId );
    if (! is_array( $user )) {
        G::LoadClass( 'wsResponse' );
        return new wsResponse( 3, G::LoadTranslation('ID_USER_NOT_REGISTERED_SYSTEM') );
    }

    G::LoadClass( 'wsBase' );
    $ws = new wsBase();
    $res = $ws->assignUserToGroup( $params->userId, $params->groupId );
    return $res->getPayloadArray();
}

function CreateUser ($params)
{
    ifSessionExpiredBreakThis( $params->sessionId );
    $x = ifPermission( $params->sessionId, 'PM_USERS' );
    if ($x == 0) {
        G::LoadClass( 'wsResponse' );
        $result = new wsResponse( 24, G::LoadTranslation('ID_NOT_PRIVILEGES') );
        return $result;
    }
    G::LoadClass( 'wsBase' );
    $ws = new wsBase();
    $res = $ws->createUser( $params->userId, $params->firstname, $params->lastname, $params->email, $params->role, $params->password );
    return $res->getPayloadArray();
}

function TaskList ($params)
{
    $x = ifPermission( $params->sessionId, 'PM_CASES' );
    if ($x == 0) {
        $result[] = array ('guid' => 24,'name' => G::LoadTranslation('ID_NOT_PRIVILEGES') );
        return $result;
    }
    G::LoadClass( 'wsBase' );
    G::LoadClass( 'sessions' );
    $ws = new wsBase();
    $oSessions = new Sessions();
    $session = $oSessions->getSessionUser( $params->sessionId );
    $userId = $session['USR_UID'];
    $res = $ws->taskList( $userId );
    return array ("tasks" => $res
    );
}

function TaskCase ($params)
{
    ifSessionExpiredBreakThis( $params->sessionId );
    $x = ifPermission( $params->sessionId, 'PM_CASES' );
    if ($x == 0) {
        $result[] = array ('guid' => 24,'name' => G::LoadTranslation('ID_NOT_PRIVILEGES') );
        return $result;
    }
    G::LoadClass( 'wsBase' );
    $ws = new wsBase();
    $res = $ws->taskCase( $params->caseId );
    return array ("taskCases" => $res
    );
}

function ReassignCase ($params)
{
    ifSessionExpiredBreakThis( $params->sessionId );
    //G::LoadClass('wsResponse');
    //return new wsResponse (1, print_r($params,1));
    G::LoadClass( 'wsBase' );
    $ws = new wsBase();
    $res = $ws->reassignCase( $params->sessionId, $params->caseId, $params->delIndex, $params->userIdSource, $params->userIdTarget );
    return $res;
}

function ifSessionExpiredBreakThis ($sessionId)
{ #added By Erik AO <erik@colosa.com> in datetime 26.06.2008 10:00:00
    G::LoadClass( 'sessions' );
    $oSessions = new Sessions();
    $session = $oSessions->verifySession( $sessionId );
    if ($session == '') {
        G::LoadClass( 'wsResponse' );
        return new wsResponse( 9, G::LoadTranslation('ID_SESSION_EXPIRED') );
    }
}

function ifPermission ($sessionId, $permission)
{
    global $RBAC;
    $RBAC->initRBAC();
    G::LoadClass( 'sessions' );
    $oSession = new Sessions();
    $user = $oSession->getSessionUser( $sessionId );

    $oRBAC = RBAC::getSingleton();
    $oRBAC->loadUserRolePermission( $oRBAC->sSystem, $user['USR_UID'] );
    $aPermissions = $oRBAC->aUserInfo[$oRBAC->sSystem]['PERMISSIONS'];
    $sw = 0;
    foreach ($aPermissions as $aPermission) {
        if ($aPermission['PER_CODE'] == $permission) {
            $sw = 1;
        }
    }
    return $sw;
}
$server = new SoapServer( $wsdl );
$server->addFunction( "Login" );
$server->addFunction( "ProcessList" );
$server->addFunction( "CaseList" );
$server->addFunction( "RoleList" );
$server->addFunction( "GroupList" );
$server->addFunction( "UserList" );
$server->addFunction( "SendMessage" );
$server->addFunction( "SendVariables" );
$server->addFunction( "GetVariables" );
$server->addFunction( "DerivateCase" );
$server->addFunction( "executeTrigger" );
$server->addFunction( "NewCaseImpersonate" );
$server->addFunction( "NewCase" );
$server->addFunction( "AssignUserToGroup" );
$server->addFunction( "CreateUser" );
$server->addFunction( "getCaseInfo" );
$server->addFunction( "TaskList" );
$server->addFunction( "TaskCase" );
$server->addFunction( "ReassignCase" );

$server->handle();

