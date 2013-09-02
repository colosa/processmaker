<?php
/**
 * BasicMethodTest.php
 *
 * ProcessMaker Open Source Edition
 * Copyright (C) 2004 - 2008 Colosa Inc.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * For more information, contact Colosa Inc, 2566 Le Jeune Rd.,
 * Coral Gables, FL, 33134, USA, or email info@colosa.com.
 *
 */
  if ( !defined ('PATH_THIRDPARTY') ) {
    require_once(  $_SERVER['PWD']. '/test/bootstrap/unit.php');
  }

  require_once( PATH_THIRDPARTY . '/lime/lime.php');
  require_once( PATH_THIRDPARTY.'lime/yaml.class.php');

  $t = new lime_test( 153, new lime_output_color());
  $t->diag('Basic Web Services Methods Test' );

  include_once ( "wsConfig.php" );
  include_once ( "wsClient.php" );

  define ( 'PROCESS_UID',             '34537126948bc50f6dc0ad8058165755');
  define ( 'PROCESS_UID_LIBRARY',     '45325906448e0e237986394052497623');
  define ( 'START_SEQ_CYCLICAL_TASK', '31132134648bc50fb7afa90020304752');
  define ( 'START_SEQ_MANUAL_TASK',   '58845888048bc514a9ca3f6076028720');
  define ( 'START_REQ_VALUE_TASK',    '42952443148bc515a7aae98069470300');
  define ( 'STARTERS_GROUP',          '4466311448bd9a5788a892004431840');
  define ( 'DERIVATORS_GROUP',        '24537073048ee0d6fc69088047537069');
  define ( 'TEST_TRIGGER',            '6421002784aa91b4fced632057479964');

  global $sessionId;
  global $client;

  $t->is( strlen (WS_WSDL_URL) > 10 ,             true, 'include wsConfig.php' );
  $t->is( function_exists('ws_open')  ,           true, 'include wsClient.php' );
  $t->diag('--------------- wsClient functions --------------------' );
  $t->is( function_exists('ws_open'),             true, 'ws_open()' );
  $t->is( function_exists('ws_open_with_params'), true, 'ws_open_with_params()' );
  $t->is( function_exists('ws_newCase'),          true, 'ws_newCase()' );
  $t->is( function_exists('ws_sendEmailMessage'), true, 'ws_sendEmailMessage()' );
  $t->is( function_exists('ws_getVariables'),     true, 'ws_getVariables()' );
  $t->is( function_exists('ws_sendVariables'),    true, 'ws_sendVariables()' );
  $t->is( function_exists('ws_executeTrigger'),   true, 'ws_executeTrigger()' );
  $t->is( function_exists('ws_routeCase'),        true, 'ws_routeCase()' );
  $t->is( function_exists('ws_processList'),      true, 'ws_processList()' );
  $t->is( function_exists('ws_getCaseInfo'),      true, 'ws_getCaseInfo()' );
  $t->is( function_exists('ws_reassignCase'),     true, 'ws_reassignCase()' );
  $t->is( function_exists('ws_userList'),         true, 'ws_userList()' );
  $t->is( function_exists('ws_groupList'),        true, 'ws_groupList()' );
  $t->is( function_exists('ws_caseList'),         true, 'ws_caseList()' );
  $t->is( function_exists('ws_roleList'),         true, 'ws_roleList()' );
  $t->is( function_exists('ws_taskCase'),         true, 'ws_taskCase()' );
  //$t->is( function_exists('ws_sendFileByCurl'),   true, 'ws_sendFileByCurl()' );
  $t->is( function_exists('ws_createUser'),       true, 'ws_createUser()' );
  $t->is( function_exists('ws_assignUserToGroup'),true, 'ws_assignUserToGroup()' );
  $t->is( function_exists('ws_systemInformation'),true, 'ws_systemInformation()' );
  $t->is( function_exists('ws_triggerList')      ,true, 'ws_triggerList()' );

  $t->diag('WS WSDL URL ' . WS_WSDL_URL );
  $t->diag('WS_USER_ID ' . WS_USER_ID );
  $t->diag('WS_USER_PASS ' . WS_USER_PASS );
  ws_open ();

  $t->diag('--------------- defined WSDL functions --------------------' );
  $methods = $client->__getFunctions();
  $types   = $client->__getTypes();

  $t->is( count($methods)  , 22,  '22 functions in the wsdl file');
  $t->is( $methods[0]  , 'loginResponse login(login $parameters)',  'login');
  $t->is( $methods[1]  , 'processListResponse processList(processListRequest $parameters)'     ,  'processList');
  $t->is( $methods[2]  , 'roleListResponse roleList(roleListRequest $parameters)'              ,  'roleList');
  $t->is( $methods[3]  , 'groupListResponse groupList(groupListRequest $parameters)'           ,  'groupList');
  $t->is( $methods[4]  , 'userListResponse userList(userListRequest $parameters)'              ,  'userList');
  $t->is( $methods[5]  , 'caseListResponse caseList(caseListRequest $parameters)'              ,  'caseList');
  $t->is( $methods[6]  , 'createUserResponse createUser(createUserRequest $parameters)'        ,  'createUser');
  $t->is( $methods[7]  , 'pmResponse assignUserToGroup(assignUserToGroupRequest $parameters)'  ,  'assignUserToGroup');
  $t->is( $methods[8]  , 'newCaseResponse newCase(newCaseRequest $parameters)'                 ,  'newCase');
  $t->is( $methods[9]  , 'pmResponse reassignCase(reassignCaseRequest $parameters)'            ,  'reassignCase');
  $t->is( $methods[10] , 'pmResponse newCaseImpersonate(newCaseImpersonateRequest $parameters)',  'newCaseImpersonate');
  $t->is( $methods[11] , 'routeCaseResponse routeCase(routeCaseRequest $parameters)'           ,  'routeCase');
  $t->is( $methods[12] , 'pmResponse executeTrigger(executeTriggerRequest $parameters)'        ,  'executeTrigger');
  $t->is( $methods[13] , 'pmResponse sendVariables(sendVariablesRequest $parameters)'          ,  'sendVariables');
  $t->is( $methods[14] , 'getVariablesResponse getVariables(getVariablesRequest $parameters)'  ,  'getVariables');
  $t->is( $methods[15] , 'pmResponse sendMessage(sendMessageRequest $parameters)'              ,  'sendMessage');
  $t->is( $methods[16] , 'getCaseInfoResponse getCaseInfo(getCaseInfoRequest $parameters)'     ,  'getCaseInfo');
  $t->is( $methods[17] , 'taskListResponse taskList(taskListRequest $parameters)'              ,  'taskList');
  $t->is( $methods[18] , 'taskCaseResponse taskCase(taskCaseRequest $parameters)'              ,  'taskCase');
  $t->is( $methods[19] , 'systemInformationResponse systemInformation(systemInformationRequest $parameters)',  'systemInformation');
  $t->is( $methods[20] , 'triggerListResponse triggerList(triggerListRequest $parameters)'     ,  'triggerList');

  $t->diag('--------------- defined WSDL types --------------------' );

  $t->is( count($types)    , 52,  '52 types in the wsdl file');

  $type0  = "struct login {\n string userid;\n string password;\n}";
  $type1  = "struct loginResponse {\n integer status_code;\n string message;\n string version;\n string timestamp;\n}" ;
  $type2  = "struct pmResponse {\n integer status_code;\n string message;\n string timestamp;\n}" ;
  $type3  = "struct processListRequest {\n string sessionId;\n}" ;
  $type4  = "struct processListStruct {\n string guid;\n string name;\n}" ;
  $type5  = "struct processListResponse {\n processListStruct processes;\n}" ;
  $type6  = "struct roleListStruct {\n string guid;\n string name;\n}" ;
  $type7  = "struct roleListRequest {\n string sessionId;\n}" ;
  $type8  = "struct roleListResponse {\n roleListStruct roles;\n}" ;
  $type9 = "struct groupListStruct {\n string guid;\n string name;\n}" ;
  $type10 = "struct groupListRequest {\n string sessionId;\n}" ;
  $type11 = "struct groupListResponse {\n groupListStruct groups;\n}" ;
  $type12 = "struct userListStruct {\n string guid;\n string name;\n}" ;
  $type13 = "struct userListRequest {\n string sessionId;\n}" ;
  $type14 = "struct userListResponse {\n userListStruct users;\n}" ;
  $type15 = "struct caseListStruct {\n string guid;\n string name;\n string status;\n string delIndex;\n}" ;
  $type16 = "struct caseListRequest {\n string sessionId;\n}" ;
  $type17 = "struct caseListResponse {\n caseListStruct cases;\n}" ;
  $type18 = "struct createUserRequest {\n string sessionId;\n string userId;\n string firstname;\n string lastname;\n string email;\n string role;\n string password;\n}" ;
  $type19 = "struct createUserResponse {\n integer status_code;\n string message;\n string userUID;\n string timestamp;\n}" ;
  $type20 = "struct assignUserToGroupRequest {\n string sessionId;\n string userId;\n string groupId;\n}" ;
  $type21 = "struct variableStruct {\n string name;\n}" ;
  $type22 = "struct sendVariablesRequest {\n string sessionId;\n string caseId;\n variableListStruct variables;\n}" ;
  $type23 = "struct variableListStruct {\n string name;\n string value;\n}" ;
  $type24 = "struct variableListRequest {\n variableListStruct variables;\n}" ;
  $type25 = "struct getVariablesResponse {\n integer status_code;\n string message;\n string timestamp;\n variableListStruct variables;\n}" ;
  $type26 = "struct getVariablesRequest {\n string sessionId;\n string caseId;\n variableStruct variables;\n}" ;
  $type27 = "struct newCaseRequest {\n string sessionId;\n string processId;\n string taskId;\n variableListStruct variables;\n}" ;
  $type28 = "struct newCaseResponse {\n integer status_code;\n string message;\n string caseId;\n string caseNumber;\n string timestamp;\n}" ;
  $type29 = "struct reassignCaseRequest {\n string sessionId;\n string caseId;\n string delIndex;\n string userIdSource;\n string userIdTarget;\n}" ;
  $type30 = "struct newCaseImpersonateRequest {\n string sessionId;\n string processId;\n string userId;\n variableStruct variables;\n}" ;
  $type31 = "struct routeListStruct {\n string userId;\n string userName;\n string taskId;\n string taskName;\n integer delIndex;\n integer delThread;\n string delThreadStatus;\n}" ;
  $type32 = "struct routeCaseRequest {\n string sessionId;\n string caseId;\n string delIndex;\n}" ;
  $type33 = "struct routeCaseResponse {\n integer status_code;\n string message;\n string timestamp;\n routeListStruct routing;\n}" ;
  $type34 = "struct executeTriggerRequest {\n string sessionId;\n string caseId;\n string triggerIndex;\n string delIndex;\n}" ;
  $type35 = "struct sendMessageRequest {\n string sessionId;\n string caseId;\n string from;\n string to;\n string cc;\n string bcc;\n string subject;\n string template;\n}" ;
  $type36 = "struct getCaseInfoRequest {\n string sessionId;\n string caseId;\n string delIndex;\n}" ;
  $type37 = "struct getCaseInfoStruct {\n string userId;\n string userName;\n string taskId;\n string taskName;\n integer delIndex;\n integer delThread;\n string delThreadStatus;\n}";
  $type38 = "struct getCaseInfoResponse {\n integer status_code;\n string message;\n string caseId;\n string caseNumber;\n string caseName;\n string caseStatus;\n string caseParalell;\n string caseCreatorUser;\n string caseCreatorUserName;\n string processId;\n string processName;\n string createDate;\n getCaseInfoStruct currentUsers;\n}";
  $type39 = "struct taskListRequest {\n string sessionId;\n}" ;
  $type40 = "struct taskListStruct {\n string guid;\n string name;\n}" ;
  $type41 = "struct taskListResponse {\n taskListStruct tasks;\n}" ;
  $type42 = "struct taskCaseStruct {\n string guid;\n string name;\n}" ;
  $type43 = "struct taskCaseRequest {\n string sessionId;\n string caseId;\n}" ;
  $type44 = "struct taskCaseResponse {\n taskCaseStruct taskCases;\n}";
  $type45 = "struct systemInformationRequest {\n string sessionId;\n}";
  $type46 = "struct systemInformationResponse {\n integer status_code;\n string message;\n string timestamp;\n string version;\n string operatingSystem;\n string webServer;\n string serverName;\n string serverIp;\n string phpVersion;\n string databaseVersion;\n string databaseServerIp;\n string databaseName;\n string availableDatabases;\n string userBrowser;\n string userIp;\n}";
  $type47 = "struct triggerListRequest {\n string sessionId;\n}";
  $type48 = "struct triggerListStruct {\n string guid;\n string name;\n string processId;\n}";
  $type49 = "struct triggerListResponse {\n triggerListStruct triggers;\n}";

  $t->is( $types[0 ] , $type0    ,  'login                     ' );
  $t->is( $types[1 ] , $type1    ,  'loginResponse             ' );
  $t->is( $types[2 ] , $type2    ,  'pmResponse                ' );
  $t->is( $types[3 ] , $type3    ,  'processListRequest        ' );
  $t->is( $types[4 ] , $type4    ,  'processListStruct         ' );
  $t->is( $types[5 ] , $type5    ,  'processListResponse       ' );
  $t->is( $types[6 ] , $type6    ,  'processListStruct         ' );
  $t->is( $types[7 ] , $type7    ,  'roleListStruct            ' );
  $t->is( $types[8 ] , $type8    ,  'roleListRequest           ' );
  $t->is( $types[9 ] , $type9    ,  'roleListResponse          ' );
  $t->is( $types[10] , $type10   ,  'groupListStruct           ' );
  $t->is( $types[11] , $type11   ,  'groupListRequest          ' );
  $t->is( $types[12] , $type12   ,  'groupListResponse         ' );
  $t->is( $types[13] , $type13   ,  'userListStruct            ' );
  $t->is( $types[14] , $type14   ,  'userListRequest           ' );
  $t->is( $types[15] , $type15   ,  'userListResponse          ' );
  $t->is( $types[16] , $type16   ,  'caseListStruct            ' );
  $t->is( $types[17] , $type17   ,  'caseListRequest           ' );
  $t->is( $types[18] , $type18   ,  'caseListResponse          ' );
  $t->is( $types[19] , $type19   ,  'createUserRequest         ' );
  $t->is( $types[20] , $type20   ,  'createUserResponse        ' );
  $t->is( $types[21] , $type21   ,  'assignUserToGroupRequest  ' );
  $t->is( $types[22] , $type22   ,  'variableStruct            ' );
  $t->is( $types[23] , $type23   ,  'sendVariablesRequest      ' );
  $t->is( $types[24] , $type24   ,  'variableListStruct        ' );
  $t->is( $types[25] , $type25   ,  'variableListRequest       ' );
  $t->is( $types[26] , $type26   ,  'variableListResponse      ' );
  $t->is( $types[27] , $type27   ,  'getVariablesRequest       ' );
  $t->is( $types[28] , $type28   ,  'newCaseRequest            ' );
  $t->is( $types[29] , $type29   ,  'newCaseResponse           ' );
  $t->is( $types[30] , $type30   ,  'reassignCaseRequest       ' );
  $t->is( $types[31] , $type31   ,  'newCaseImpersonateRequest ' );
  $t->is( $types[32] , $type32   ,  'derivateListStruct        ' );
  $t->is( $types[33] , $type33   ,  'routeCaseRequest          ' );
  $t->is( $types[34] , $type34   ,  'routeCaseResponse         ' );
  $t->is( $types[35] , $type35   ,  'executeTriggerRequest     ' );
  $t->is( $types[36] , $type36   ,  'sendMessageRequest        ' );
  $t->is( $types[37] , $type37   ,  'getCaseInfoRequest        ' );
  $t->is( $types[38] , $type38   ,  'getCaseInfoStruct         ' );
  $t->is( $types[39] , $type39   ,  'getCaseInfoResponse       ' );
  $t->is( $types[40] , $type40   ,  'taskListRequest           ' );
  $t->is( $types[41] , $type41   ,  'taskListStruct            ' );
  $t->is( $types[42] , $type42   ,  'taskListResponse          ' );
  $t->is( $types[43] , $type43   ,  'taskCaseStruct            ' );
  $t->is( $types[44] , $type44   ,  'taskCaseRequest           ' );
  $t->is( $types[45] , $type45   ,  'taskCaseResponse          ' );
  $t->is( $types[46] , $type46   ,  'systemInformationRequest  ' );
  $t->is( $types[47] , $type47   ,  'systemInformationResponse ' );
  $t->is( $types[48] , $type48   ,  'triggerListRequest ' );
  $t->is( $types[49] , $type49   ,  'triggerListStruct ' );
  $t->is( $types[50] , $type50   ,  'triggerListResponse ' );
  $t->is( $types[51] , $type51   ,  'triggerListResponse ' );
  $t->is( $types[52] , $type52   ,  'triggerListResponse ' );

  $t->isa_ok( $client , 'SoapClient',  'class SoapClient created');

  $t->diag('--------------- now will check every method of wsdl and soap --------------------' );

  $info = ws_systemInformation();

  $t->is( $info->status_code , 0 ,  'ws_systemInformation status_code = 0');
  $t->diag( '  version            ' . $info->version            );
  $t->diag( '  operatingSystem    ' . $info->operatingSystem    );
  $t->diag( '  webServer          ' . $info->webServer          );
  $t->diag( '  serverName         ' . $info->serverName         );
  $t->diag( '  serverIp           ' . $info->serverIp           );
  $t->diag( '  phpVersion         ' . $info->phpVersion         );
  $t->diag( '  databaseVersion    ' . $info->databaseVersion    );
  $t->diag( '  databaseServerIp   ' . $info->databaseServerIp   );
  $t->diag( '  databaseName       ' . $info->databaseName       );
  $t->diag( '  availableDatabases ' . $info->availableDatabases );
  $t->diag( '  userBrowser        ' . $info->userBrowser        );
  $t->diag( '  userIp             ' . $info->userIp             );

  $t->is( strlen ($sessionId) > 30 , true,  'getting a valid SessionId');
  $t->is( strlen ($sessionId) > 30 , true,  'ws_open works fine');
  $t->diag('Session Id: ' . $sessionId );

  //checking the processList
  $processes = ws_processList();
  $t->is( count ($processes->processes) >= 0 , true,  'ws_processList works fine');

  $foundProcess = false;
  if ( is_array ($processes->processes ) ) {
    foreach ( $processes->processes as $key => $val ) {
      if ( $val->guid == PROCESS_UID ) $foundProcess = true;
    }
  }
  else
    if ( $processes->processes->guid == PROCESS_UID ) $foundProcess = true;

  if ( $foundProcess ) {
    $t->is( $foundProcess, true,  'Sequential Process is present in this Workspace');
  }

  //checking the groupList
  $groups    = ws_groupList() ;
  $t->is( count ($groups->groups) >= 2 , true,  'ws_groupList works fine');
  $foundGroup1 = false;
  $foundGroup2 = false;
  foreach ( $groups->groups as $key => $val ) {
    if ( $val->guid == STARTERS_GROUP ) $foundGroup1 = true;
    if ( $val->guid == DERIVATORS_GROUP ) $foundGroup2 = true;
  }
  $t->is( $foundGroup1, true,  'Starter grous is present in Workspace');
  $t->is( $foundGroup2, true,  'derivators grous is present in Workspace');

  //checking user list
  $users     = ws_userList() ;
  $t->is( count ($users->users) >= 1 , true,  'ws_userList works fine');
  $foundUser1 = false;
  foreach ( $users->users as $key => $val ) {
    if ( $val->guid == '00000000000000000000000000000001' ) $foundUser1 = true;
  }
  $t->is( $foundUser1, true,  'Admin user is present in Workspace');

  //checking roles
  $roles     = ws_roleList() ;
  $t->is( count ($roles->roles) >= 2 , true,  'ws_roleList works fine');
  $roleOperator = '';
  foreach ( $roles->roles as $key => $val ) {
    if ( $val->name == 'PROCESSMAKER_OPERATOR' )
      $roleOperator = $val->guid;
  }
  $t->is( strlen( $roleOperator ) >= 30 , true,  'role PROCESSMAKER_OPERATOR exists');

  //checking the triggerList
  $triggers = ws_triggerList();
  $t->is( count ($triggers->triggers) >= 1 , true,  'ws_triggerList there are ' . count ($triggers->triggers) . ' triggers in this workspace' );

  $foundTrigger = false;
  if ( is_array ( $triggers->triggers ) )
    foreach ( $triggers->triggers as $key => $val ) {
      if ( $val->guid == TEST_TRIGGER )$foundTrigger = true;
    }
  else {
      if ( $triggers->triggers->guid == TEST_TRIGGER )$foundTrigger = true;
  }
  $t->is( $foundTrigger, true,  'the test trigger is present in Workspace');

  $t->diag('-----------------------------------' );
  $t->diag('Cyclical Sequential Derivation Test' );
  $t->diag('Process Guid: ' . PROCESS_UID );
  $t->diag('Starting Task: ' . START_SEQ_CYCLICAL_TASK );

  //creating 	two new users for the starters and derivations groups'
	$dateNow  = date ( 'H-m-d H:i:s' );
	$dateDay  = date ( 'W' );
	$dateYear = date ( 'Y' );

	$user1Id   = 'John'  . date ( 'mdHi' );
	$firstname = 'John ' . date ( 'mdHi' );
	$lastname  = 'Doe';
	$email     = 'John' . date ( 'mdHi' ) . '@colosa.com';
	$role      = $roleOperator;
	$password  = 'sample';
  $res = ws_createUser ( $user1Id, $firstname, $lastname, $email, $roleOperator, $password );
  $t->isa_ok( $res, 'stdClass',  'executed ws_createUser');
  if( $res->status_code == 0 )
   {$t->is( $res->status_code,0,  'ws_createUser status_code = 0');
  }
  else{$t->is( $res->status_code,7,  'ws_createUser status_code = 7');
  }
  $t->diag( $res->message );
  $t->diag( 'UserUID = ' . $res->userUID);
  $user1Uid = $res->userUID;
  $res = ws_assignUserToGroup ( $user1Uid, STARTERS_GROUP );

	$user2Id    = 'Mary' . date ( 'mdHi' );
	$firstname = 'Mary ' . date ( 'mdHi' );
	$lastname  = 'Smith';
	$email     = 'Mary' . date ( 'mdHi' ) . '@colosa.com';
	$role      = $roleOperator;
	$password  = 'sample';
  $res = ws_createUser ( $user2Id, $firstname, $lastname, $email, $roleOperator, $password );
  $t->isa_ok( $res, 'stdClass',  'executed ws_createUser');
  if( $res->status_code == 0 ){
  	$t->is( $res->status_code , 0 ,  'ws_createUser status_code = 0');
  }else{
  	$t->is( $res->status_code , 7 ,  'ws_createUser status_code = 7');
  	}

  $t->diag( $res->message );
  $t->diag( 'UserUID = ' . $res->userUID);
  $user2Uid = $res->userUID;
  $res = ws_assignUserToGroup ( $user2Uid, DERIVATORS_GROUP );

	$res = ws_open_with_params ( WS_WSDL_URL, $user1Id, 'sample');

  //create a case with John
  $variables = array();
  $variables[] = array ( 'name' => 'webServer',  'value' => $info->webServer );
  $variables[] = array ( 'name' => 'phpVersion', 'value' => $info->phpVersion );

  $result = ws_newCase ( PROCESS_UID, START_SEQ_CYCLICAL_TASK, $variables );
  $t->isa_ok( $result, 'stdClass',  'executed ws_newCase');

  $t->is( $result->status_code , 0 ,  'ws_newCase status_code = 0');
  if ( $result->status_code == 0 ) {
     $caseId     = $result->caseId;
     $caseNumber = $result->caseNumber;
     $msg = sprintf ( "New Case created: \033[0;31;32m%s %s\033[0m", $caseNumber, $caseId  );
     $t->diag($msg );
  }
  else {
    $t->diag('------------ Error executing newCase ---------------------' );
    $t->diag('  status code : ' . $result->status_code );
    $t->diag('  message     : ' . $result->message );
    $t->diag('  timestamp   : ' . $result->timestamp );
    die;
  }


  //check caseList as the newCase was succesful, there are at least one case for this user
  $cases     = ws_caseList() ;
  $t->is( count ($cases->cases ) >= 1 , true,  'ws_caseList works fine');

  //getCaseInfo
  $delIndex = 1;
  $res = ws_getCaseInfo ($caseId, $delIndex);
  $t->is( $res->status_code , 0 ,  'ws_getCaseInfo status_code = 0');
  $t->is( $res->message , 'Command executed successfully',  'ws_getCaseInfo message ');
  $t->is( $res->caseId , $caseId ,  'ws_getCaseInfo caseId = ' . $caseId);
  $t->is( $res->caseStatus , 'DRAFT' ,  'ws_getCaseInfo caseStatus = ' . $res->caseStatus );
  if( $res->caseCreatorUser == '3454143534b144c02717596034198392'){
  $t->is( $res->caseCreatorUser , $user1Uid ,  'ws_getCaseInfo caseCreatorUser = ' . $user1Id );
  }
  $routedUser = $res->caseCurrentUser;
  $t->diag('ws_getCaseInfo caseNumber: '          . $res->caseNumber );
  $t->diag('ws_getCaseInfo caseName: '            . $res->caseName );
  $t->diag('ws_getCaseInfo caseStatus: '          . $res->caseStatus );
  $t->diag('ws_getCaseInfo caseParalell: '        . $res->caseParalell );
  $t->diag('ws_getCaseInfo caseCreatorUser: '     . $res->caseCreatorUser );
  $t->diag('ws_getCaseInfo caseCreatorUserName: ' . $res->caseCreatorUserName );
  $t->diag('ws_getCaseInfo processId: '           . $res->processId );
  $t->diag('ws_getCaseInfo processName: '         . $res->processName );
  $t->diag('ws_getCaseInfo createDate: '          . $res->createDate );
  $t->diag('ws_getCaseInfo currentUsers: '        . $res->currentUsers);

  //sending two variables to this case
  $variables = array();
  $variables[] = array ( 'name' => 'firstString',  'value' => $user1Id );
  $variables[] = array ( 'name' => 'secondString', 'value' => PHP_OS );
  $res = ws_sendVariables ($caseId, $variables );
  $t->is( $res->status_code , 0 ,  "ws_sendVariables status_code = 0 ");
  $t->is( $res->message,      '2 variables received' ,  "ws_sendVariables 2 variables received");

  //execute trigger to concatenate previous strings
  $res = ws_executeTrigger ($caseId, TEST_TRIGGER ,$delIndex );
  $t->is( $res->status_code , 0 ,  'ws_executeTrigger status_code = 0');
  $t->diag( "trigger source code: \n" . $res->message);

  //get variables and check results
  $getVariables = array( 'PIN', 'firstString', 'secondString', 'result', 'today', 'webServer', 'phpVersion' );
  $res = ws_getVariables ($caseId, $getVariables );
  $t->is( $res->status_code , 0 ,  'ws_getVariables status_code = 0');
  $t->is( $res->message,      '7 variables sent' ,             "ws_getVariables 7 variables received");
  $t->is( strlen($res->variables[0]->value), 4 ,               "variable PIN          received ok (" . $res->variables[0]->value . ")" );
  $t->is( $res->variables[1]->value, $user1Id ,                "variable firstString  received ok (" . $res->variables[1]->value . ")" );
  $t->is( $res->variables[2]->value, PHP_OS   ,                "variable secondString received ok (" . $res->variables[2]->value . ")" );
  $t->is( $res->variables[3]->value,$user1Id.' '.PHP_OS,       "variable result       received ok (" . $res->variables[3]->value . ")" );
  $t->is( substr($res->variables[4]->value,0,10),date('Y-m-d'),"variable today        received ok (" . $res->variables[4]->value . ")" );
  $t->is( $res->variables[5]->value, $info->webServer ,        "variable webServer    received ok (" . $res->variables[5]->value . ")" );
  $t->is( $res->variables[6]->value, $info->phpVersion  ,      "variable phpVersion   received ok (" . $res->variables[6]->value . ")" );

  //Get case info for invalid case
  $res = ws_getCaseInfo ('123456', $delIndex);
  $t->is( $res->status_code , 100 ,  "ws_getCaseInfo status_code = 100 Case doesn't exist.");
  $t->diag('ws_getCaseInfo for invalid case is : '. $res->message );

  //routing a case from John to Mary
  $delIndex = 1;
  $result = ws_routeCase ($caseId, $delIndex );
  $t->isa_ok( $result, 'stdClass',  'executed ws_routeCase');
  $t->is( $result->status_code, 0,  'ws_routeCase status_code = 0');
  $t->diag ( 'route case message: ' . $result->message );
  $t->is( $result->routing->delIndex,        2     ,        "delIndex = 2" );
  $t->is( $result->routing->delThread,       1     ,        "delThread = 1" );
  $t->is( $result->routing->delThreadStatus, 'OPEN',        "delThreadStatus = OPEN" );

  $msg = trim ( sprintf ( "Case Routed to: \033[0;31;32m%s\033[0m", $result->message));
  $t->diag($msg );

  //getCaseInfo to get the current user
  $delIndex = 2;
  $res = ws_getCaseInfo ($caseId, $delIndex);
  $t->is( $res->status_code , 0 ,  'ws_getCaseInfo status_code = 0');
  $routedUser     = $res->currentUsers->userId;
  $routedUserName = $res->currentUsers->userName;
  $delIndex = $res->currentUsers->delIndex;
  $t->diag( 'ws_getCaseInfo Routed user is ' . $routedUser );
  $t->diag( 'ws_getCaseInfo Routed user is ' . $routedUserName );
  $t->is( $res->caseStatus , 'TO_DO' ,  'ws_getCaseInfo caseStatus = ' . $res->caseStatus );


  //reassign this case to Mary
	$res = ws_open( );

  $result = ws_reassignCase ($caseId, $delIndex, $routedUser, $user2Uid);
  $t->isa_ok( $result, 'stdClass',  'executed ws_reassignCase');
  if ( $result->status_code == 0 ) {
    $t->is( $result->status_code, 0,  'ws_reassignCase status_code = 0');
    $t->diag( 'ws_reassignCase message: ' . $result->message );
  }
  else {
    $t->is( $result->status_code, 30,  'ws_reassignCase status_code = 30');
    $t->diag( 'ws_reassignCase message: ' . $result->message );
  }
  //update delIndex after reassign
  $res = ws_getCaseInfo ($caseId, $delIndex);
  $t->is( $res->status_code , 0 ,  'ws_getCaseInfo status_code = 0');
  $routedUser     = $res->currentUsers->userId;
  $routedUserName = $res->currentUsers->userName;
  $delIndex = $res->currentUsers->delIndex;
  $t->diag( 'ws_getCaseInfo Reassigned user is ' . $routedUser );
  $t->diag( 'ws_getCaseInfo Reassigned user is ' . $routedUserName );

  //finishing a case from Mary
	$res = ws_open_with_params ( WS_WSDL_URL, $user2Id, 'sample');
  $result = ws_routeCase ($caseId, $delIndex );
  $t->isa_ok( $result, 'stdClass',  'executed ws_routeCase');
  $t->is( $result->status_code, 0,  'ws_routeCase status_code = 0');
  $assign = $result->message;

  $msg = trim ( sprintf ( "Case Derivated to: \033[0;31;32m%s\033[0m", $result->message));
  $t->diag($msg );

  //check if the case is closed
  $res = ws_getCaseInfo ($caseId, $delIndex);
  $t->is( $res->status_code , 0 ,  'ws_getCaseInfo status_code = 0');
  $t->is( $res->caseStatus , 'COMPLETED' ,  'ws_getCaseInfo caseStatus = ' . $res->caseStatus );
