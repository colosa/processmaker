<?php
ini_set("soap.wsdl_cache_enabled", "1"); // enabling WSDL cache


function parseItemArray($array) {
  if (! isset($array->item) && ! is_array($array)) {
    return null;
  }

  $result = array ();
  if (isset($array->item)) {
    foreach ( $array->item as $key => $value ) {
      $result[$value->key] = $value->value;
    }
  } else {
    foreach ( $array as $key => $value ) {
      $result[$value->key] = $value->value;
    }
  }
  return $result;
}

/**
 * function convertFormToWSObjects
 * @param $form
 * Convert a normal POST form into the correspondent valid structure for webservice
 * @return array of objects
 */
function convertFormToWSObjects($form) {

  foreach ( $form as $key => $val ) {
    if (! is_array($val)) { //Normal Variables
      $obj = new stdClass();
      $obj->name = $key;
      $obj->value = $val;
      $aVariables[] = $obj;
    } else {
      foreach ( $val as $gridKey => $gridRow ) { //Sp�cial Variables like grids or checkgroups
        if (is_array($gridRow)) { //Grids
          foreach ( $gridRow as $col => $colValue ) {
            $obj = new stdClass();
            $obj->name = $key . "][" . $gridKey . "][" . $col;
            $obj->value = $colValue;
            $aVariables[] = $obj;
          }
        } else { //Checkgroups, Radiogroups
          $obj = new stdClass();
          $obj->name = $key;
          $obj->value = implode("|", $val);
          $aVariables[] = $obj;
        }
      }
    }
  }

  return $aVariables;
}

//this function is not necesary for version 2 of PM webservices
function convertSoapArrayToArray($object) {
  $result = array ();
  $properties = get_object_vars($object);

  foreach ( $properties as $keyProperties => $valProperties ) {
    $array = array ();
    foreach ( $valProperties as $keyItems => $valItems ) {
      $node = array ();
      if (isset($valItems->item)) {
        foreach ( $valItems->item as $keyNode => $valNode ) {
          $node[$valNode->key] = $valNode->value;
        }
      }
      if (is_array($valItems)) {
        foreach ( $valItems as $keyNode => $valNode ) {
          $node[$valNode->key] = $valNode->value;
        }
      }
      $array[] = $node;
    }
    $result[$keyProperties] = $array;
  }
  return $result;
}

function ws_parser($result) {
  $rows = array ();
  if (isset($result->derivation))
    if (is_array($result->derivation->item)) {
      foreach ( $result->derivation->item as $index => $val ) {
        $rows[$val->key] = $val->value;
      }
    } else {
      $rows[$result->derivation->item->key] = $result->derivation->item->value;
    }

  return $rows;
}

function ws_open() {
  global $sessionId;
  global $client;
  $endpoint = WS_WSDL_URL;
  $sessionId = '';
  @$client = new SoapClient($endpoint);

  $user = WS_USER_ID;
  $pass = WS_USER_PASS;

  $params = array (
    'userid' => $user,
    'password' => $pass
  );
  $result = $client->__SoapCall('login', array (
    $params
  ));

  if ($result->status_code == 0) {
    $sessionId = $result->message;
    return 1;
  }
  throw (new Exception($result->message));
}

function ws_open_with_params($endpoint, $user, $pass) {
  global $sessionId;
  global $client;
  $sessionId = '';
  @$client = new SoapClient($endpoint);

  $params = array (
    'userid' => $user,
    'password' => $pass
  );
  $result = $client->__SoapCall('login', array (
    $params
  ));

  if ($result->status_code == 0) {
    $sessionId = $result->message;
    return 1;
  }
  throw (new Exception($result->message));
}

function ws_sendEmailMessage($caseId, $toEmail, $sSubject, $ccEmail, $bccEmail, $template) {
  global $sessionId;
  global $client;
  $params = array (
    'sessionId' => $sessionId,
    'caseId' => $caseId,
    'from' => 'soporte <support@colosa.com>',
    'to' => $toEmail,
    'cc' => $ccEmail,
    'bcc' => $bccEmail,
    'subject' => $sSubject,
    'template' => $template
  );
  $result = $client->__SoapCall('sendMessage', array (
    $params
  ));
  return $result;
}

function ws_sendMessage($caseId, $toEmail, $sSubject, $ccEmail, $bccEmail, $template) {
  global $sessionId;
  global $client;
  $params = array (
    'sessionId' => $sessionId,
    'caseId' => $caseId,
    'from' => 'soporte <support@colosa.com>',
    'to' => $toEmail,
    'cc' => $ccEmail,
    'bcc' => $bccEmail,
    'subject' => $sSubject,
    'template' => $template
  );
  $result = $client->__SoapCall('sendMessage', array (
    $params
  ));
  return $result;
}

function ws_getVariables($caseId, $variables) {
  global $sessionId;
  global $client;

  $aVariables = array ();
  foreach ( $variables as $key => $val ) {
    $obj = new stdClass();
    $obj->name = $val;
    $aVariables[] = $obj;
  }

  $params = array (
    'sessionId' => $sessionId,
    'caseId' => $caseId,
    'variables' => $aVariables
  );
  $result = $client->__SoapCall('getVariables', array (
    $params
  ));
  return $result;
}

function ws_newCase($proUid, $taskUid, $variables) {
  global $sessionId;
  global $client;

  $params = array (
    'sessionId' => $sessionId,
    'processId' => $proUid,
    'taskId' => $taskUid,
    'variables' => $variables
  );

  $result = $client->__SoapCall('newCase', array (
    $params
  ));

  return $result;
}

function ws_sendVariables($caseId, $variables) {
  global $sessionId;
  global $client;

  $params = array (
    'sessionId' => $sessionId,
    'caseId' => $caseId,
    'variables' => $variables
  );
  $result = $client->__SoapCall('sendVariables', array (
    $params
  ));

  return $result;
}

function ws_executeTrigger($caseId, $triggerId, $delIndex) {
  global $sessionId;
  global $client;

  $params = array (
    'sessionId' => $sessionId,
    'caseId' => $caseId,
    'triggerIndex' => $triggerId,
    'delIndex' => $delIndex
  );
  $result = $client->__SoapCall('executeTrigger', array (
    $params
  ));
  return $result;
}

//only for backward compatibility
function ws_derivateCase($caseId, $delId) {
  global $sessionId;
  global $client;

  $rows = array ();
  $params = array (
    'sessionId' => $sessionId,
    'caseId' => $caseId,
    'delIndex' => $delId
  );
  $result = $client->__SoapCall('derivateCase', array (
    $params
  ));
  $rows = ws_parser($result);

  $result->derivation = $rows;
  //print_r($result);
  return $result;
}

function ws_routeCase($caseId, $delId) {
  global $sessionId;
  global $client;

  $rows = array ();
  $params = array (
    'sessionId' => $sessionId,
    'caseId' => $caseId,
    'delIndex' => $delId
  );
  $result = $client->__SoapCall('routeCase', array (
    $params
  ));
  return $result;
}

function ws_processList() {
  global $sessionId;
  global $client;

  $params = array (
    'sessionId' => $sessionId
  );
  $result = $client->__SoapCall('processList', array (
    $params
  ));
  //if ( $result->status_code == 0 ) {
  return $result;
  //}
//throw ( new Exception ( $result->message ) );
}

function ws_groupList() {
  global $sessionId;
  global $client;

  $params = array (
    'sessionId' => $sessionId
  );
  $result = $client->__SoapCall('groupList', array (
    $params
  ));
  return $result;
}

function ws_departmentList() {
  global $sessionId;
  global $client;

  $params = array (
    'sessionId' => $sessionId
  );
  $result = $client->__SoapCall('departmentList', array (
    $params
  ));

  if ( !is_array($result->departments) ) {
  	$res = new StdClass();
    $res->departments[0] = $result->departments;
    return $res;
  }

  return $result;
}

function ws_roleList() {
  global $sessionId;
  global $client;

  $params = array (
    'sessionId' => $sessionId
  );
  $result = $client->__SoapCall('roleList', array (
    $params
  ));
  return $result;
}

function ws_caseList() {
  global $sessionId;
  global $client;

  $params = array (
    'sessionId' => $sessionId
  );
  $result = $client->__SoapCall('caseList', array (
    $params
  ));
  return $result;
}

function ws_userList() {
  global $sessionId;
  global $client;

  $users = array ();
  $params = array (
    'sessionId' => $sessionId
  );
  $result = $client->__SoapCall('userList', array (
    $params
  ));
  return $result;
}

function ws_triggerList() {
  global $sessionId;
  global $client;

  $users = array ();
  $params = array (
    'sessionId' => $sessionId
  );
  $result = $client->__SoapCall('triggerList', array (
    $params
  ));
  return $result;
}

function ws_getCaseInfo($caseId, $delIndex = NULL) {
  global $sessionId;
  global $client;

  $params = array (
    'sessionId' => $sessionId,
    'caseId' => $caseId,
    'delIndex' => $delIndex
  );
  $result = $client->__SoapCall('getCaseInfo', array (
    $params
  ));
  return $result;
}

function ws_reassignCase($caseId, $delIndex, $userIdSource, $userIdTarget) {
  global $sessionId;
  global $client;

  $params = array (
    'sessionId' => $sessionId,
    'caseId' => $caseId,
    'delIndex' => $delIndex,
    'userIdSource' => $userIdSource,
    'userIdTarget' => $userIdTarget
  );
  $result = $client->__SoapCall('reassignCase', array (
    $params
  ));
  //if ( $result->status_code == 0 ) {
  //  return $result;
  //}
  return $result;
  //throw ( new Exception ( $result->message ) );
}

function ws_taskCase($caseId) {
  global $sessionId;
  global $client;

  $params = array (
    'caseId' => $caseId
  );
  //$result = $client->__SoapCall( 'sessionId' => $sessionId, 'taskCase', array($params) );
  //  $result = $client->__SoapCall( 'sessionId' => $sessionId, 'taskCase', array($params) );
  if ($result->status_code == 0) {
    return $result;
  }
  throw (new Exception($result->message));
}

function ws_sendFile(
    $FILENAME,
    $USR_UID,
    $APP_UID,
    $DEL_INDEX=1,
    $DOC_UID=null,
    $APP_DOC_FIELDNAME=null,
    $title=null,
    $comment=null
) {
    $DOC_UID = ($DOC_UID != null)? $DOC_UID : -1;
    $APP_DOC_TYPE = ($DOC_UID == -1)? "ATTACHED" : "INPUT";
    $title = ($title != null)? $title : $FILENAME;
    $comment = ($comment != null)? $comment : null;

    $params = array(
        "ATTACH_FILE" => "@$FILENAME",
        "APPLICATION" => $APP_UID,
        "INDEX" => $DEL_INDEX,
        "DOC_UID" => $DOC_UID,
        "USR_UID" => $USR_UID,
        "APP_DOC_TYPE" => $APP_DOC_TYPE,
        "APP_DOC_FIELDNAME" => $APP_DOC_FIELDNAME,
        "TITLE" => $title,
        "COMMENT" => $comment
    );

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, WS_UPLOAD_URL);
    //curl_setopt($ch, CURLOPT_VERBOSE, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    $response = curl_exec($ch);
    curl_close($ch);

    return $response;
}

function ws_updateFile($APP_DOC_UID, $FILENAME, $DOC_VERSION, $APP_DOC_TYPE=NULL, $USR_UID=NULL, $APP_UID=NULL, $DEL_INDEX=NULL, $DOC_UID=NULL, $title=NULL, $comment=NULL) {

  $params = array (
    'APP_DOC_UID' => $APP_DOC_UID,
    'DOC_VERSION' => $DOC_VERSION,
    'ATTACH_FILE' => "@$FILENAME"
  );

  if( $APP_UID != NULL)
    $params['APPLICATION'] = $APP_UID;
  if( $DEL_INDEX != NULL)
    $params['INDEX'] = $DEL_INDEX;
  if( $USR_UID != NULL)
    $params['USR_UID'] = $USR_UID;
  if( $DOC_UID != NULL)
    $params['DOC_UID'] = $DOC_UID;
  if( $APP_DOC_TYPE != NULL)
    $params['APP_DOC_TYPE'] = $APP_DOC_TYPE;
  if( $title != NULL)
    $params['TITLE'] = $title;
  if( $comment != NULL)
    $params['COMMENT'] = $comment;

  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, WS_UPLOAD_URL);
  //curl_setopt($ch, CURLOPT_VERBOSE, 1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_POST, 1);
  curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
  $response = curl_exec($ch);
  curl_close($ch);

  return $response;
}

//create a new user
function ws_createUser($userId, $firstname, $lastname, $email, $role, $password) {
  global $sessionId;
  global $client;

  $params = array (
    'sessionId' => $sessionId,
    'userId' => $userId,
    'firstname' => $firstname,
    'lastname' => $lastname,
    'email' => $email,
    'role' => $role,
    'password' => $password
  );

  $result = $client->__SoapCall('createUser', array (
    $params
  ));

  return $result;
}

//create a new group
function ws_createGroup( $groupName ) {
  global $sessionId;
  global $client;

  $params = array (
    'sessionId' => $sessionId,
    'name' => $groupName
  );
  $result = $client->__SoapCall('createGroup', array ( $params ));
  return $result;
}

//create a new group
function ws_createDepartment( $depName, $depParentId ) {
  global $sessionId;
  global $client;

  $params = array (
    'sessionId' => $sessionId,
    'name' => $depName,
    'parentUID' => $depParentId
  );
  $result = $client->__SoapCall('createDepartment', array ( $params ));
  return $result;
}

//assignUserToGroup
function ws_assignUserToGroup($userId, $groupId) {
  global $sessionId;
  global $client;

  $params = array (
    'sessionId' => $sessionId,
    'userId' => $userId,
    'groupId' => $groupId
  );

  $result = $client->__SoapCall('assignUserToGroup', array (
    $params
  ));
  return $result;
}

//assignUserToGroup
function ws_assignUserToDepartment($userId, $depId, $manager ) {
  global $sessionId;
  global $client;

  $params = array (
    'sessionId' => $sessionId,
    'userId' => $userId,
    'departmentId' => $depId,
    'manager' => $manager
  );

  $result = $client->__SoapCall('assignUserToDepartment', array (
    $params
  ));
  return $result;
}

function ws_systemInformation() {
  global $sessionId;
  global $client;

  $params = array (
    'sessionId' => $sessionId
  );
  $result = $client->__SoapCall('systemInformation', array (
    $params
  ));
  return $result;
}

function ws_InputDocumentList($caseId) {
  global $sessionId;
  global $client;

  $params = array (
    'sessionId' => $sessionId,
    'caseId' => $caseId
  );

  $result = $client->__SoapCall('InputDocumentList', array (
    $params
  ));

  return $result;
}

function ws_outputDocumentList($caseId) {
  global $sessionId;
  global $client;

  $params = array (
    'sessionId' => $sessionId,
    'caseId' => $caseId
  );

  $result = $client->__SoapCall('outputDocumentList', array (
    $params
  ));

  return $result;
}

function ws_removeDocument($appDocUid) {
  global $sessionId;
  global $client;

  $params = array (
    'sessionId' => $sessionId,
    'appDocUid' => $appDocUid
  );

  $result = $client->__SoapCall('RemoveDocument', array (
    $params
  ));

  return $result;
}
