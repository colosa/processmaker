<?php

use ProcessMaker\Util\ParseSoapVariableName;

ini_set("soap.wsdl_cache_enabled", 0); //disabling WSDL cache

define('WEB_SERVICE_VERSION', '2.0');

//$wsdl = PATH_METHODS . "services" . PATH_SEP . "pmos.wsdl";
$wsdl = PATH_METHODS . "services" . PATH_SEP . "pmos2.wsdl";

function login($params)
{
    $ws = new WsBase();
    $res = $ws->login($params->userid, $params->password);

    return array('status_code' => $res->status_code, 'message' => $res->message, 'version' => WEB_SERVICE_VERSION, 'timestamp' => $res->timestamp);
}

function ProcessList($params)
{
    $vsResult = isValidSession($params->sessionId);

    if ($vsResult->status_code !== 0) {
        $o->guid = $vsResult->status_code . ' ' . $vsResult->message;
        $o->name = '';

        return array("processes" => $o);
    }

    if (ifPermission($params->sessionId, 'PM_CASES') != 0) {
        $ws = new WsBase();
        $res = $ws->processList();

        return array("processes" => $res);
    }

    if (ifPermission($params->sessionId, 'PM_FACTORY') == 0) {
        $o->guid = "2" . G::LoadTranslation('ID_INSUFFICIENT_PRIVILEGES_FUNCTION');
        $o->name = '';

        return array("processes" => $o);
    }

    /**
     * if you are not an admin user, then this function will return only your valid process *
     */
    if (ifPermission($params->sessionId, 'PM_FACTORY') == 0) {
        $oSessions = new Sessions();
        $session = $oSessions->getSessionUser($params->sessionId);
        $userId = $session['USR_UID'];

        $ws = new WsBase();
        $res = $ws->processListVerified($userId);

        return array("processes" => $res);
    }

    $ws = new WsBase();
    $res = $ws->processList();

    return array("processes" => $res);
}

function RoleList($params)
{
    $vsResult = isValidSession($params->sessionId);

    if ($vsResult->status_code !== 0) {
        $o->guid = $vsResult->status_code . ' ' . $vsResult->message;
        $o->name = '';

        return array("roles" => $o);
    }

    if (ifPermission($params->sessionId, 'PM_USERS') == 0) {
        $o->guid = "2" . G::LoadTranslation('ID_INSUFFICIENT_PRIVILEGES_FUNCTION');
        $o->name = '';

        return array("roles" => $o);
    }

    $ws = new WsBase();
    $res = $ws->roleList();

    return array("roles" => $res);
}

function GroupList($params)
{
    $vsResult = isValidSession($params->sessionId);

    if ($vsResult->status_code !== 0) {
        $o->guid = $vsResult->status_code . ' ' . $vsResult->message;
        $o->name = '';

        return array("groups" => $o);
    }

    if (ifPermission($params->sessionId, 'PM_USERS') == 0) {
        $o->guid = "2" . G::LoadTranslation('ID_INSUFFICIENT_PRIVILEGES_FUNCTION');
        $o->name = '';

        return array("groups" => $o);
    }

    $ws = new WsBase();
    $res = $ws->groupList();

    return array("groups" => $res);
}

function DepartmentList($params)
{
    $vsResult = isValidSession($params->sessionId);

    if ($vsResult->status_code !== 0) {
        $o->guid = $vsResult->status_code . ' ' . $vsResult->message;
        $o->name = '';

        return array("departments" => $o);
    }

    if (ifPermission($params->sessionId, 'PM_USERS') == 0) {
        $o->guid = "2" . G::LoadTranslation('ID_INSUFFICIENT_PRIVILEGES_FUNCTION');
        $o->name = '';

        return array("departments" => $o);
    }

    $ws = new WsBase();
    $res = $ws->departmentList();

    return array("departments" => $res);
}

function CaseList($params)
{
    $vsResult = isValidSession($params->sessionId);

    if ($vsResult->status_code !== 0) {
        $o->guid = $vsResult->status_code . ' ' . $vsResult->message;
        $o->name = '';
        $o->status = '';
        $o->delIndex = '';
        $o->processId = '';

        return array("cases" => $o);
    }

    if (ifPermission($params->sessionId, 'PM_CASES') == 0) {
        $o->guid = "2" . G::LoadTranslation('ID_INSUFFICIENT_PRIVILEGES_FUNCTION');
        $o->name = '';
        $o->status = '';
        $o->delIndex = '';
        $o->processId = '';

        return array("cases" => $o);
    }

    $oSessions = new Sessions();
    $session = $oSessions->getSessionUser($params->sessionId);
    $userId = $session['USR_UID'];

    $ws = new WsBase();
    $res = $ws->caseList($userId);

    return array("cases" => $res);
}

function UnassignedCaseList($params)
{
    $vsResult = isValidSession($params->sessionId);
    if ($vsResult->status_code !== 0) {
        $o->guid = $vsResult->status_code . ' ' . $vsResult->message;
        $o->name = '';
        $o->delIndex = '';

        return array("cases" => $o);
    }

    if (ifPermission($params->sessionId, 'PM_CASES') == 0) {
        $o->guid = "2 Insufficient privileges to execute this function";
        $o->name = '';
        $o->delIndex = '';

        return array("cases" => $o);
    }

    $oSessions = new Sessions();
    $session = $oSessions->getSessionUser($params->sessionId);
    $userId = $session['USR_UID'];

    $ws = new WsBase();
    $res = $ws->unassignedCaseList($userId);

    return array("cases" => $res);
}

function UserList($params)
{
    $vsResult = isValidSession($params->sessionId);

    if ($vsResult->status_code !== 0) {
        $o->guid = $vsResult->status_code . ' ' . $vsResult->message;
        $o->name = '';

        return array("users" => $o);
    }

    if (ifPermission($params->sessionId, 'PM_USERS') == 0) {
        $o->guid = "2" . G::LoadTranslation('ID_INSUFFICIENT_PRIVILEGES_FUNCTION');
        $o->name = '';

        return array("users" => $o);
    }

    $ws = new WsBase();
    $res = $ws->userList();

    return array("users" => $res);
}

function triggerList($params)
{
    $vsResult = isValidSession($params->sessionId);

    if ($vsResult->status_code !== 0) {
        $o->guid = $vsResult->status_code . ' ' . $vsResult->message;
        $o->name = '';
        $o->processId = '';

        return array("triggers" => $o);
    }

    if (ifPermission($params->sessionId, 'PM_CASES') == 0) {
        $o->guid = "2" . G::LoadTranslation('ID_INSUFFICIENT_PRIVILEGES_FUNCTION');
        $o->name = '';
        $o->processId = '';

        return array("triggers" => $o);
    }

    $ws = new WsBase();
    $res = $ws->triggerList();

    return array("triggers" => $res);
}

function outputDocumentList($params)
{
    $vsResult = isValidSession($params->sessionId);

    if ($vsResult->status_code !== 0) {
        $o->guid = $vsResult->status_code . ' ' . $vsResult->message;
        $o->filename = '';
        $o->docId = '';
        $o->version = '';
        $o->createDate = '';
        $o->createBy = '';
        $o->type = '';
        $o->index = '';
        $o->link = '';

        return array("documents" => $o);
    }

    if (ifPermission($params->sessionId, 'PM_CASES') == 0) {
        $o->guid = "2" . G::LoadTranslation('ID_INSUFFICIENT_PRIVILEGES_FUNCTION');
        $o->filename = '';
        $o->docId = '';
        $o->version = '';
        $o->createDate = '';
        $o->createBy = '';
        $o->type = '';
        $o->index = '';
        $o->link = '';

        return array("documents" => $o);
    }

    $oSessions = new Sessions();
    $session = $oSessions->getSessionUser($params->sessionId);
    $userId = $session['USR_UID'];

    $ws = new WsBase();
    $res = $ws->outputDocumentList($params->caseId, $userId);

    return array("documents" => $res);
}

function inputDocumentList($params)
{
    $vsResult = isValidSession($params->sessionId);

    if ($vsResult->status_code !== 0) {
        $o->guid = $vsResult->status_code . ' ' . $vsResult->message;
        $o->filename = '';
        $o->docId = '';
        $o->version = '';
        $o->createDate = '';
        $o->createBy = '';
        $o->type = '';
        $o->index = '';
        $o->link = '';

        return array("documents" => $o);
    }

    if (ifPermission($params->sessionId, 'PM_CASES') == 0) {
        $o->guid = "2" . G::LoadTranslation('ID_INSUFFICIENT_PRIVILEGES_FUNCTION');
        $o->filename = '';
        $o->docId = '';
        $o->version = '';
        $o->createDate = '';
        $o->createBy = '';
        $o->type = '';
        $o->index = '';
        $o->link = '';

        return array("documents" => $o);
    }

    $oSessions = new Sessions();
    $session = $oSessions->getSessionUser($params->sessionId);
    $userId = $session['USR_UID'];

    $ws = new WsBase();
    $res = $ws->inputDocumentList($params->caseId, $userId);

    return array("documents" => $res);
}

function inputDocumentProcessList($params)
{
    $vsResult = isValidSession($params->sessionId);

    if ($vsResult->status_code !== 0) {
        $o->guid = $vsResult->status_code . ' ' . $vsResult->message;
        $o->name = '';
        $o->description = '';

        return array("documents" => $o);
    }

    if (ifPermission($params->sessionId, 'PM_CASES') == 0) {
        $o->guid = "2" . G::LoadTranslation('ID_INSUFFICIENT_PRIVILEGES_FUNCTION');
        $o->name = '';
        $o->description = '';

        return array("documents" => $o);
    }

    $ws = new WsBase();
    $res = $ws->inputDocumentProcessList($params->processId);

    return array("documents" => $res);
}

function removeDocument($params)
{
    $vsResult = isValidSession($params->sessionId);

    if ($vsResult->status_code !== 0) {
        return $vsResult;
    }

    if (ifPermission($params->sessionId, 'PM_CASES') == 0) {
        $result = new WsResponse(2, G::LoadTranslation('ID_INSUFFICIENT_PRIVILEGES_FUNCTION'));

        return $result;
    }

    $ws = new WsBase();
    $res = $ws->removeDocument($params->appDocUid);

    return $res;
}

function SendMessage($params)
{
    $vsResult = isValidSession($params->sessionId);

    if ($vsResult->status_code !== 0) {
        return $vsResult->getPayloadArray();
    }

    if (ifPermission($params->sessionId, 'PM_CASES') == 0) {
        $result = new WsResponse(2, G::LoadTranslation('ID_NOT_PRIVILEGES'));

        return $result->getPayloadArray();
    }

    $ws = new WsBase();
    $res = $ws->sendMessage($params->caseId, $params->from, $params->to, $params->cc, $params->bcc, $params->subject, $params->template);

    return $res->getPayloadArray();
}

function getCaseInfo($params)
{
    $vsResult = isValidSession($params->sessionId);

    if ($vsResult->status_code !== 0) {
        return $vsResult;
    }

    if (ifPermission($params->sessionId, 'PM_CASES') == 0) {
        $result = new WsResponse(2, G::LoadTranslation('ID_NOT_PRIVILEGES'));

        return $result;
    }

    $ws = new WsBase();
    $res = $ws->getCaseInfo($params->caseId, $params->delIndex);

    return $res;
}

function SendVariables($params)
{
    $vsResult = isValidSession($params->sessionId);

    if ($vsResult->status_code !== 0) {
        return $vsResult;
    }

    if (ifPermission($params->sessionId, 'PM_CASES') == 0) {
        $result = new WsResponse(2, G::LoadTranslation('ID_NOT_PRIVILEGES'));

        return $result;
    }

    $ws = new WsBase();
    $variables = $params->variables;
    $Fields = array();

    if (is_object($variables)) {
        $Fields[$variables->name] = $variables->value;
    } elseif (is_array($variables)) {
        foreach ($variables as $index => $obj) {
            if (is_object($obj) && isset($obj->name) && isset($obj->value)) {
                $Fields[$obj->name] = $obj->value;
            }
        }
    }

    $params->variables = $Fields;
    $res = $ws->sendVariables($params->caseId, $params->variables);

    return $res->getPayloadArray();
}

function GetVariables($params)
{
    if (!is_array($params->variables)) {
        $params->variables = array($params->variables);
    }

    $vsResult = isValidSession($params->sessionId);

    if ($vsResult->status_code !== 0) {
        return $vsResult;
    }

    if (ifPermission($params->sessionId, 'PM_CASES') == 0) {
        $result = new wsGetVariableResponse(2, G::LoadTranslation('ID_NOT_PRIVILEGES'), null);

        return $result;
    }

    $ws = new WsBase();

    $res = $ws->getVariables($params->caseId, $params->variables);

    return $res;
}

function GetVariablesNames($params)
{
    $vsResult = isValidSession($params->sessionId);

    if ($vsResult->status_code !== 0) {
        return $vsResult;
    }

    if (ifPermission($params->sessionId, 'PM_CASES') == 0) {
        $result = new wsGetVariableResponse(2, G::LoadTranslation('ID_NOT_PRIVILEGES'), null);

        return $result;
    }

    $ws = new WsBase();

    $res = $ws->getVariablesNames($params->caseId);

    return $res;
}

function DerivateCase($params)
{
    $oSession = new Sessions();

    $vsResult = isValidSession($params->sessionId);

    if ($vsResult->status_code !== 0) {
        return $vsResult;
    }

    if (ifPermission($params->sessionId, 'PM_CASES') == 0) {
        $result = new WsResponse(2, G::LoadTranslation('ID_NOT_PRIVILEGES'));

        return $result;
    }

    $user = $oSession->getSessionUser($params->sessionId);

    $oStd->stored_system_variables = true;
    $oStd->wsSessionId = $params->sessionId;

    $ws = new WsBase($oStd);
    $res = $ws->derivateCase($user["USR_UID"], $params->caseId, $params->delIndex, true);

    return $res;
}

function RouteCase($params)
{
    $oSession = new Sessions();

    $vsResult = isValidSession($params->sessionId);

    if ($vsResult->status_code !== 0) {
        return $vsResult;
    }

    if (ifPermission($params->sessionId, 'PM_CASES') == 0) {
        $result = new WsResponse(2, G::LoadTranslation('ID_NOT_PRIVILEGES'));

        return $result;
    }

    $user = $oSession->getSessionUser($params->sessionId);

    $oStd = new stdclass();
    $oStd->stored_system_variables = true;
    $oStd->wsSessionId = $params->sessionId;

    $ws = new WsBase($oStd);
    $res = $ws->derivateCase($user["USR_UID"], $params->caseId, $params->delIndex, true);

    return $res;
}

function executeTrigger($params)
{
    $vsResult = isValidSession($params->sessionId);

    if ($vsResult->status_code !== 0) {
        return $vsResult;
    }

    if (ifPermission($params->sessionId, 'PM_CASES') == 0) {
        $result = new WsResponse(2, G::LoadTranslation('ID_NOT_PRIVILEGES'));

        return $result;
    }

    $oSession = new Sessions();
    $user = $oSession->getSessionUser($params->sessionId);

    $ws = new WsBase();
    $delIndex = (isset($params->delIndex)) ? $params->delIndex : 1;
    $res = $ws->executeTrigger($user['USR_UID'], $params->caseId, $params->triggerIndex, $delIndex);

    return $res->getPayloadArray();
}

function NewCaseImpersonate($params)
{
    $vsResult = isValidSession($params->sessionId);

    if ($vsResult->status_code !== 0) {
        return $vsResult;
    }

    if (ifPermission($params->sessionId, "PM_CASES") == 0) {
        $result = new WsResponse(2, G::LoadTranslation('ID_NOT_PRIVILEGES'));

        return $result;
    }

    ///////
    $variables = $params->variables;

    $field = array();

    if (is_object($variables)) {
        $field[$variables->name] = $variables->value;
    } else {
        if (is_array($variables)) {
            foreach ($variables as $index => $obj) {
                if (is_object($obj) && isset($obj->name) && isset($obj->value)) {
                    $field[$obj->name] = $obj->value;
                }
            }
        }
    }

    $params->variables = $field;

    ///////
    $ws = new WsBase();
    $res = $ws->newCaseImpersonate($params->processId, $params->userId, $params->variables, $params->taskId);

    return $res;
}

/**
 * Begins a new case under the name of the logged-in user.
 * Where the parameter value is:
 * - string sessionId: The ID of the session, which is obtained during login.
 * - string processId: The ID of the process where the case should start, which
 *   can be obtained with processList().
 * - string taskId: The ID of the task where the case should start. This will
 *   generally be the first task in a process, which can be obtained with taskList().
 * - array variables: An array of variableStruct objects which contain information
 *   to start the case. This array has the following format.
 *
 * @param object $params
 *
 * @return object
 */
function NewCase($params)
{
    $parseSoapVariableVame = new ParseSoapVariableName();

    $vsResult = isValidSession($params->sessionId);

    if ($vsResult->status_code !== 0) {
        return $vsResult;
    }

    if (ifPermission($params->sessionId, 'PM_CASES') == 0) {
        $result = new WsResponse(2, G::LoadTranslation('ID_NOT_PRIVILEGES'));

        return $result;
    }

    $oSession = new Sessions();
    $session = $oSession->getSessionUser($params->sessionId);
    $userId = $session['USR_UID'];
    $variables = isset($params->variables) ? $params->variables : null;

    $field = array();

    if (is_object($variables) && $variables->name === '__POST_VARIABLES__') {
        $field = G::json_decode($variables->value, true);
        $variables = null;
    }

    if (is_object($variables)) {
        $field[$variables->name] = $variables->value;
    }

    if (is_array($variables)) {
        foreach ($variables as $val) {
            if (!is_object($val->value)) {
                $parseSoapVariableVame->buildVariableName($field, $val->name, $val->value);
            }
        }
    }

    $params->variables = $field;

    $ws = new WsBase();

    $res = $ws->newCase($params->processId, $userId, $params->taskId, $params->variables, (isset($params->executeTriggers)) ? (int) ($params->executeTriggers) : 0);

    // we need to register the case id for a stored session variable. like a normal Session.
    $oSession->registerGlobal('APPLICATION', $res->caseId);

    return $res;
}

function AssignUserToGroup($params)
{
    $vsResult = isValidSession($params->sessionId);

    if ($vsResult->status_code !== 0) {
        return $vsResult->getPayloadArray();
    }

    if (ifPermission($params->sessionId, 'PM_USERS') == 0) {
        $result = new WsResponse(2, G::LoadTranslation('ID_NOT_PRIVILEGES'));

        return $result->getPayloadArray();
    }

    $sessions = new Sessions();
    $user = $sessions->getSessionUser($params->sessionId);

    if (!is_array($user)) {
        return new WsResponse(3, 'User not registered in the system');
    }

    $ws = new WsBase();
    $res = $ws->assignUserToGroup($params->userId, $params->groupId);

    return $res->getPayloadArray();
}

function AssignUserToDepartment($params)
{
    $vsResult = isValidSession($params->sessionId);

    if ($vsResult->status_code !== 0) {
        return $vsResult->getPayloadArray();
    }

    if (ifPermission($params->sessionId, 'PM_USERS') == 0) {
        $result = new WsResponse(2, G::LoadTranslation('ID_NOT_PRIVILEGES'));

        return $result->getPayloadArray();
    }

    $sessions = new Sessions();
    $user = $sessions->getSessionUser($params->sessionId);

    if (!is_array($user)) {
        return new WsResponse(3, G::LoadTranslation('ID_USER_NOT_REGISTERED_SYSTEM'));
    }

    $ws = new WsBase();
    $res = $ws->AssignUserToDepartment($params->userId, $params->departmentId, $params->manager);

    return $res->getPayloadArray();
}

function CreateUser($params)
{
    $vsResult = isValidSession($params->sessionId);

    if ($vsResult->status_code !== 0) {
        return $vsResult;
    }

    if (ifPermission($params->sessionId, 'PM_USERS') == 0) {
        $result = new wsCreateUserResponse(2, G::LoadTranslation('ID_NOT_PRIVILEGES'));

        return $result;
    }

    $ws = new WsBase();

    try {
        $res = $ws->createUser($params->userId, $params->firstname, $params->lastname, $params->email, $params->role, $params->password, ((isset($params->dueDate)) ? $params->dueDate : null), ((isset($params->status)) ? $params->status : null));
    } catch (Exception $oError) {
        return $oError->getMessage();
    }

    return $res;
}

function updateUser($params)
{
    $result = isValidSession($params->sessionId);

    if ($result->status_code != 0) {
        return $result;
    }

    if (ifPermission($params->sessionId, "PM_USERS") == 0) {
        $result = new WsResponse(2, G::LoadTranslation('ID_NOT_PRIVILEGES'));

        return $result;
    }

    $ws = new WsBase();

    $result = $ws->updateUser($params->userUid, $params->userName, ((isset($params->firstName)) ? $params->firstName : null), ((isset($params->lastName)) ? $params->lastName : null), ((isset($params->email)) ? $params->email : null), ((isset($params->dueDate)) ? $params->dueDate : null), ((isset($params->status)) ? $params->status : null), ((isset($params->role)) ? $params->role : null), ((isset($params->password)) ? $params->password : null));

    return $result;
}

function informationUser($params)
{
    $result = isValidSession($params->sessionId);

    if ($result->status_code != 0) {
        return $result;
    }

    if (ifPermission($params->sessionId, "PM_USERS") == 0) {
        $result = new WsResponse(2, G::LoadTranslation('ID_NOT_PRIVILEGES'));

        return $result;
    }

    $ws = new WsBase();
    $result = $ws->informationUser($params->userUid);

    return $result;
}

function CreateGroup($params)
{
    $vsResult = isValidSession($params->sessionId);

    if ($vsResult->status_code !== 0) {
        $result = new WsCreateGroupResponse($vsResult->status_code, $vsResult->message, '');

        return $result;
    }

    if (ifPermission($params->sessionId, 'PM_USERS') == 0) {
        $result = new WsCreateGroupResponse(2, G::LoadTranslation('ID_NOT_PRIVILEGES'), '');

        return $result;
    }

    $ws = new WsBase();
    $res = $ws->createGroup($params->name);

    return $res;
}

function CreateDepartment($params)
{
    $vsResult = isValidSession($params->sessionId);

    if ($vsResult->status_code !== 0) {
        return $vsResult;
    }

    if (ifPermission($params->sessionId, 'PM_USERS') == 0) {
        $result = new wsCreateUserResponse(2, G::LoadTranslation('ID_NOT_PRIVILEGES'));

        return $result;
    }

    $ws = new WsBase();
    $res = $ws->CreateDepartment($params->name, $params->parentUID);

    return $res;
}

function TaskList($params)
{
    $vsResult = isValidSession($params->sessionId);

    if ($vsResult->status_code !== 0) {
        $o->guid = $vsResult->status_code . ' ' . $vsResult->message;
        $o->name = '';

        return array("tasks" => $o);
    }

    if (ifPermission($params->sessionId, 'PM_CASES') == 0) {
        $o->guid = "2" . G::LoadTranslation('ID_INSUFFICIENT_PRIVILEGES_FUNCTION');
        $o->name = '';

        return array("tasks" => $o);
    }

    $ws = new WsBase();
    $oSessions = new Sessions();
    $session = $oSessions->getSessionUser($params->sessionId);
    $userId = $session['USR_UID'];
    $res = $ws->taskList($userId);

    return array("tasks" => $res);
}

function TaskCase($params)
{
    $vsResult = isValidSession($params->sessionId);

    if ($vsResult->status_code !== 0) {
        $o->guid = $vsResult->status_code . ' ' . $vsResult->message;
        $o->name = '';

        return array("taskCases" => $o);
    }

    if (ifPermission($params->sessionId, 'PM_CASES') == 0) {
        $o->guid = "2" . G::LoadTranslation('ID_INSUFFICIENT_PRIVILEGES_FUNCTION');
        $o->name = '';

        return array("taskCases" => $o);
    }

    $ws = new WsBase();
    $res = $ws->taskCase($params->caseId);

    return array("taskCases" => $res);
}

function ReassignCase($params)
{
    $vsResult = isValidSession($params->sessionId);
    if ($vsResult->status_code !== 0) {
        return $vsResult;
    }

    $ws = new WsBase();
    $res = $ws->reassignCase($params->sessionId, $params->caseId, $params->delIndex, $params->userIdSource, $params->userIdTarget);

    return $res;
}

function systemInformation($params)
{
    $vsResult = isValidSession($params->sessionId);

    if ($vsResult->status_code !== 0) {
        return $vsResult;
    }

    $ws = new WsBase();
    $res = $ws->systemInformation();

    return $res;
}

function getCaseNotes($params)
{
    $vsResult = isValidSession($params->sessionId);

    if ($vsResult->status_code !== 0) {
        return $vsResult;
    }

    $ws = new WsBase();
    $res = $ws->getCaseNotes($params->applicationID, $params->userUid);

    return $res;
}

/**
 * **********
 * #added By Erik AO <erik@colosa.com> in datetime 26.06.2008 10:00:00
 * # modified 12-01-2010 by erik
 */
function isValidSession($sessionId)
{
    $oSessions = new Sessions();
    $session = $oSessions->verifySession($sessionId);

    if (is_array($session)) {
        return new WsResponse(0, G::LoadTranslation('ID_SESSION_ACTIVE'));
    } else {
        return new WsResponse(9, G::LoadTranslation('ID_SESSION_EXPIRED'));
    }
}

//add removeUserFromGroup
function removeUserFromGroup($params)
{
    $vsResult = isValidSession($params->sessionId);

    if ($vsResult->status_code !== 0) {
        return $vsResult;
    }

    $ws = new WsBase();
    $res = $ws->removeUserFromGroup($params->userId, $params->groupId);

    return $res;
}

//end add
function ifPermission($sessionId, $permission)
{
    global $RBAC;

    $RBAC->initRBAC();

    $oSession = new Sessions();
    $user = $oSession->getSessionUser($sessionId);

    $oRBAC = RBAC::getSingleton();
    $oRBAC->loadUserRolePermission($oRBAC->sSystem, $user['USR_UID']);
    $sw = $oRBAC->userCanAccess($permission) === 1 ? 1 : 0;

    return $sw;
}

function deleteCase($params)
{
    $result = isValidSession($params->sessionId);

    if ($result->status_code != 0) {
        return $result;
    }

    if (ifPermission($params->sessionId, "PM_CASES") == 0) {
        $result = new WsResponse(2, G::LoadTranslation('ID_NOT_PRIVILEGES'));

        return $result;
    }

    $ws = new WsBase();
    $result = $ws->deleteCase($params->caseUid);

    return $result;
}

function cancelCase($params)
{
    $result = isValidSession($params->sessionId);

    if ($result->status_code != 0) {
        return $result;
    }

    if (ifPermission($params->sessionId, "PM_CASES") == 0) {
        $result = new WsResponse(2, G::LoadTranslation('ID_NOT_PRIVILEGES'));

        return $result;
    }

    $ws = new WsBase();
    $result = $ws->cancelCase($params->caseUid, $params->delIndex, $params->userUid);

    return $result;
}

function pauseCase($params)
{
    $result = isValidSession($params->sessionId);

    if ($result->status_code != 0) {
        return $result;
    }

    if (ifPermission($params->sessionId, "PM_CASES") == 0) {
        $result = new WsResponse(2, G::LoadTranslation('ID_NOT_PRIVILEGES'));

        return $result;
    }

    $ws = new WsBase();

    $result = $ws->pauseCase($params->caseUid, $params->delIndex, $params->userUid, ((isset($params->unpauseDate)) ? $params->unpauseDate : null));

    return $result;
}

function unpauseCase($params)
{
    $result = isValidSession($params->sessionId);

    if ($result->status_code != 0) {
        return $result;
    }

    if (ifPermission($params->sessionId, "PM_CASES") == 0) {
        $result = new WsResponse(2, G::LoadTranslation('ID_NOT_PRIVILEGES'));

        return $result;
    }

    $ws = new WsBase();
    $result = $ws->unpauseCase($params->caseUid, $params->delIndex, $params->userUid);

    return $result;
}

function addCaseNote($params)
{
    $result = isValidSession($params->sessionId);

    if ($result->status_code != 0) {
        return $result;
    }

    if (ifPermission($params->sessionId, "PM_CASES") == 0) {
        $result = new WsResponse(2, G::LoadTranslation('ID_NOT_PRIVILEGES'));

        return $result;
    }

    $ws = new WsBase();
    $result = $ws->addCaseNote(
            $params->caseUid, $params->processUid, $params->taskUid, $params->userUid, $params->note, (isset($params->sendMail)) ? $params->sendMail : 1
    );

    return $result;
}

function claimCase($params)
{
    $vsResult = isValidSession($params->sessionId);
    if ($vsResult->status_code !== 0) {
        return $vsResult;
    }

    if (ifPermission($params->sessionId, 'PM_CASES') == 0) {
        $result = new WsResponse(2, G::LoadTranslation('ID_NOT_PRIVILEGES'));

        return $result;
    }

    $oSessions = new Sessions();
    $session = $oSessions->getSessionUser($params->sessionId);

    $ws = new WsBase();
    $res = $ws->claimCase($session['USR_UID'], $params->guid, $params->delIndex);

    return $res;
}
$options = array(
    'cache_wsdl' => WSDL_CACHE_NONE
);
$server = new SoapServer($wsdl, $options);

$server->addFunction("Login");
$server->addFunction("ProcessList");
$server->addFunction("CaseList");
$server->addFunction("UnassignedCaseList");
$server->addFunction("RoleList");
$server->addFunction("GroupList");
$server->addFunction("DepartmentList");
$server->addFunction("UserList");
$server->addFunction("TriggerList");
$server->addFunction("outputDocumentList");
$server->addFunction("inputDocumentList");
$server->addFunction("inputDocumentProcessList");
$server->addFunction("removeDocument");
$server->addFunction("SendMessage");
$server->addFunction("SendVariables");
$server->addFunction("GetVariables");
$server->addFunction("GetVariablesNames");
$server->addFunction("DerivateCase");
$server->addFunction("RouteCase");
$server->addFunction("executeTrigger");
$server->addFunction("NewCaseImpersonate");
$server->addFunction("NewCase");
$server->addFunction("AssignUserToGroup");
$server->addFunction("AssignUserToDepartment");
$server->addFunction("CreateGroup");
$server->addFunction("CreateDepartment");
$server->addFunction("CreateUser");
$server->addFunction("updateUser");
$server->addFunction("informationUser");
$server->addFunction("getCaseInfo");
$server->addFunction("TaskList");
$server->addFunction("TaskCase");
$server->addFunction("ReassignCase");
$server->addFunction("systemInformation");
$server->addFunction("removeUserFromGroup");
$server->addFunction("getCaseNotes");
$server->addFunction("deleteCase");
$server->addFunction("cancelCase");
$server->addFunction("pauseCase");
$server->addFunction("unpauseCase");
$server->addFunction("addCaseNote");
$server->addFunction("claimCase");
$server->handle();
