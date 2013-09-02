<?php
/**
 * bugRelease09Test.php.php
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
   //checking the processList

  function processList($t, $uid_Process,$process_uid_library )
  {
  $processes = ws_processList();
  $t->is( count ($processes->processes) >= 0 , true,  'ws_processList works fine');

  $foundProcess = false;
  if ( is_array ($processes->processes ) ) {
    foreach ( $processes->processes as $key => $val ) {
      if ( $val->guid == $uid_Process  ){
       $foundProcess = true;

      }
    }
  }
  else
    if ( $processes->processes->guid == $uid_Process  ){
        $foundProcess = true;
    }

  if ( $foundProcess ) {
    $t->is( $foundProcess, true,  'Process for' .$uidName[1]. 'is present in this Workspace');
  }

  }
  //creating an user, if this user exists just skip this lines
  function verifyUserExists ( $t, $username ) {
  	global $roleOperator;

    $firstname = $username ;
    $lastname  = 'Doe';
    $email     = $username . '@colosa.com';
    $role      = $roleOperator;
    $password  = 'sample';

    $res = ws_createUser ( $username, $firstname, $lastname, $email, $roleOperator, $password );
    $t->isa_ok( $res, 'stdClass',  'executed ws_createUser');
    if ($res->status_code == 7  ) {
      $t->is( $res->status_code , 7 ,  $username . ' is already created ');
    }

    if ($res->status_code == 0  ) {
      $t->is( $res->status_code , 0 ,  'ws_createUser status_code = 0 for user ' . $username );
      $t->diag( $res->message );
      $t->diag( 'UserUID = ' . $res->userUID);
    }

  }

  //Archivos requeridos
  if ( !defined ('PATH_THIRDPARTY') ) {
    require_once(  $_SERVER['PWD']. '/test/bootstrap/unit.php');
  }
  require_once( PATH_THIRDPARTY . '/lime/lime.php');
  require_once( PATH_THIRDPARTY.'lime/yaml.class.php');
  include_once ( "wsConfig.php" );
  include_once ( "wsClient.php" );

  //*******  Constates Procesos Release 09 ******* //
  //Constates Procesos Bug 3947
  define ( 'PROCESS_UID',           '5041008614abc53492778f2062174269');
  define ( 'PROCESS_UID_LIBRARY',   '4024267104ac4af2771b844012334031');
  define ( 'START_TASK_UID',        '2376437854abc5355ba93e8020811984');

  //Constates Procesos Bug 3500
  define ( 'PROCESS_UID3500',           '5492768614ad6a341a8d302080479261');
  define ( 'PROCESS_UID_LIBRARY3500',   '7048004704ac6834046dff5024006812');
  define ( 'START_TASK_UID3500',        '2225149194ad6a34a675960047603206');

   //Constates Procesos Bug 3948
  define ( 'PROCESS_UID3948',           '67593524648f370c469c043033436171');
  define ( 'PROCESS_UID_LIBRARY3948',   '3779354504ad72506358ef5025405839');
  define ( 'START_TASK_UID3948',        '2225149194ad6a34a675960047603206');

   //Constates Procesos Bug 3757
  define ( 'PROCESS_UID3757',           '7019135294acd8f11cc1275089288026');
  define ( 'PROCESS_UID_LIBRARY3757',   '8470043374ad729196bc233052722688');
  define ( 'START_TASK_UID3757',        '8783364554acd8f2f5aa371001390670');


  //******* Constates Procesos Release 11 *************//

  //Constates Procesos Bug 4138
  define ( 'PROCESS_UID4138',           '7403318024afbd568328557093846607');
  define ( 'PROCESS_UID_LIBRARY4138',   '618889924b0d72ee1e8d48041492421');
  define ( 'START_TASK_UID4138',        '');

  //Constates Procesos Bug 3227
  define ( 'PROCESS_UID3227',           '5961202124afbbb96071641087311915');
  define ( 'PROCESS_UID_LIBRARY3227',   '2341590934afc26725dae19067296505');
  define ( 'START_TASK_UID3227',        '8985149674afbbb9f5e7b02093180322');

  //Constates Procesos Bug 4025
  define ( 'PROCESS_UID4025',           '7226950814af80ca8482ea8038668461');
  define ( 'PROCESS_UID_LIBRARY4025',   '8185117214af9c139378dc8084788430');
  define ( 'START_TASK_UID4025',        '8326587904af80cad7f72c8087571555');

  //Constates Procesos Bug 3216
  define ( 'PROCESS_UID3216',           '3182159894ade14309d0151096572870');
  define ( 'PROCESS_UID_LIBRARY3216',   '3990660714af9baeb8d04a6038902756');
  define ( 'START_TASK_UID3216',        '7099649794ade14365e5a55015058276');

  //Constates Procesos Bug 3495
  define ( 'PROCESS_UID3495',           '5384638834af26792c75720068072932');
  define ( 'PROCESS_UID_LIBRARY3495',   '6443124704afc8405bae5c6095427535');
  define ( 'START_TASK_UID3495',        '8345191624af2679a6b74a5066292659');

  //Constates Procesos Bug 3943
  define ( 'PROCESS_UID3943',           '447CEAF7BE6AEB');
  define ( 'PROCESS_UID_LIBRARY3943',   '23310008148f211b3abef31074370823');
  define ( 'START_TASK_UID3943',        '447CEAF7BE6AEB');


  //Constates Procesos Bug 3904
  define ( 'PROCESS_UID3904',           '6863908894afd6a750802b7096073967');
  define ( 'PROCESS_UID_LIBRARY3904',   '190051244b01c27c5d3bc4073716555');
  define ( 'START_TASK_UID3904',        '8489595944afd6a7f062852065972316');

  //Constates Procesos Bug 3903
  define ( 'PROCESS_UID3903',           '8320886204b031419a70f84016574892');
  define ( 'PROCESS_UID_LIBRARY3903',   '5651557564b05d34f0c6a54085262893');
  define ( 'START_TASK_UID3903',        '');
  //Constates Procesos Bug 4120
  define ( 'PROCESS_UID4120',           '3393197484afd256abf8111021901109');
  define ( 'PROCESS_UID_LIBRARY4120',   '6544108344b06d0b55f8c44001258903');
  define ( 'START_TASK_UID4120',        '');


  //**********************************************************************//

  global $sessionId;
  global $client;
  global $roleOperator;



  $t = new lime_test( 66, new lime_output_color());
  $t->diag('Basic Web Services Methods Test' );


  $t->is( function_exists('ws_open')  , true, 'include wsClient.php' );

  $t->diag('WS WSDL URL ' . WS_WSDL_URL );
  $t->diag('WS_USER_ID ' . WS_USER_ID );
  $t->diag('WS_USER_PASS ' . WS_USER_PASS );
  ws_open ();

  $t->isa_ok( $client , 'SoapClient',  'class SoapClient created');

  //Creación importación de procesos de la librería 09
  processList($t, PROCESS_UID		 , PROCESS_UID_LIBRARY		 );
  processList($t, PROCESS_UID3500, PROCESS_UID_LIBRARY3500 );
  processList($t, PROCESS_UID3948, PROCESS_UID_LIBRARY3948 );
  processList($t, PROCESS_UID3757, PROCESS_UID_LIBRARY3757 );

  //Creación importación de procesos de la librería 11
  processList($t, PROCESS_UID4138, PROCESS_UID_LIBRARY4138 );
  processList($t, PROCESS_UID3227, PROCESS_UID_LIBRARY3227 );
  processList($t, PROCESS_UID4025, PROCESS_UID_LIBRARY4025 );
  processList($t, PROCESS_UID3216, PROCESS_UID_LIBRARY3216 );
  processList($t, PROCESS_UID3495, PROCESS_UID_LIBRARY3495 );
  processList($t, PROCESS_UID3943, PROCESS_UID_LIBRARY3943 );
  processList($t, PROCESS_UID3904, PROCESS_UID_LIBRARY3904 );
  processList($t, PROCESS_UID3903, PROCESS_UID_LIBRARY3903 );
  processList($t, PROCESS_UID4120, PROCESS_UID_LIBRARY4120 );


  //checking the groupList
  $groups    = ws_groupList() ;
  $t->is( count ($groups->groups) >= 0 , true,  'ws_groupList works fine');
  $foundGroup1 = false;
  $foundGroup2 = false;
  $foundGroup3 = false;
  $foundGroup4 = false;
  $foundGroup5 = false;
  $foundGroup6 = false;
  $foundGroup7 = false;
  $employees   = false;
  $supervisors = false;
  $finance     = false;

  foreach ( $groups->groups as $key => $val ) {

    if ( $val->name == "Group One" ) $foundGroup1 = true;
    if ( $val->name == "Group Two" ) $foundGroup2 = true;
    if ( $val->name == "Group Three") $foundGroup3 = true;
    if ( $val->name == "Group Four" ) $foundGroup1 = true;
    if ( $val->name == "Group Five" ) $foundGroup2 = true;
    if ( $val->name == "Group Six" ) $foundGroup3 = true;
    if ( $val->name == "Group Seven" ) $foundGroup1 = true;
    if ( $val->name == "Employees" ) $employees = true;
    if ( $val->name == "supervisors" ) $supervisors = true;
    if ( $val->name == "employees" ) $finance = true;

  }
  $t->is( $foundGroup1, true,  'ONE grous is present in Workspace');
  $t->is( $foundGroup2, true,  'TWO   grous is present in Workspace');
  $t->is( $foundGroup2, true,  'THREE grous is present in Workspace');
  $t->is( $foundGroup2, true,  'FOUR grous is present in Workspace');
  $t->is( $foundGroup2, true,  'FIVE grous is present in Workspace');
  $t->is( $foundGroup2, true,  'SIX grous is present in Workspace');
  $t->is( $foundGroup2, true,  'SEVEN grous is present in Workspace');
  $t->is( $foundGroup2, true,  'employees grous is present in Workspace');
  $t->is( $foundGroup2, true,  'supervisors grous is present in Workspace');
  $t->is( $foundGroup2, true,  'finance grous is present in Workspace');

  //checking roles
  $roles     = ws_roleList() ;
  $t->is( count ($roles->roles) >= 2 , true,  'ws_roleList works fine');
  $roleOperator = '';
  foreach ( $roles->roles as $key => $val ) {
    if ( $val->name == 'PROCESSMAKER_OPERATOR' )
      $roleOperator = $val->guid;
  }
  $t->is( strlen( $roleOperator ) >= 30 , true,  'role PROCESSMAKER_OPERATOR exists');


   //crear los 3 usuarios

  verifyUserExists ( $t, 'user1' );
  verifyUserExists ( $t, 'user2' );
  verifyUserExists ( $t, 'user3' );
  verifyUserExists ( $t, 'user4' );
  verifyUserExists ( $t, 'user5' );
  verifyUserExists ( $t, 'user6' );
  verifyUserExists ( $t, 'user7' );


  //checking user list and verify the three users are created
  $users     = ws_userList() ;

  $t->is( count ($users->users) >= 1 , true,  'ws_userList works fine');

  $foundUser1 = false;
  $foundUser2 = false;
  $foundUser3 = false;
  $foundUser4 = false;
  $foundUser5 = false;
  $foundUser6 = false;
  $foundUser7 = false;

  $foundGroup1 = false;
  $foundGroup2 = false;
  $foundGroup3 = false;
  $foundGroup4 = false;
  $foundGroup5 = false;
  $foundGroup6 = false;
  $foundGroup7 = false;

  $supervisors = false;
  $employees 	 = false;
  $finance   	 = false;
  //
  $users = ws_userList();
  foreach ( $users->users as $key => $valuser ) {
   if ( $valuser->name == 'user1' ) {$valuser1=$valuser->guid; $foundUser1 = true;}
   if ( $valuser->name == 'user2' ) {$valuser2=$valuser->guid; $foundUser2 = true;}
   if ( $valuser->name == 'user3' ) {$valuser3=$valuser->guid; $foundUser3 = true;}
   if ( $valuser->name == 'user4' ) {$valuser4=$valuser->guid; $foundUser4 = true;}
   if ( $valuser->name == 'user5' ) {$valuser5=$valuser->guid; $foundUser5 = true;}
   if ( $valuser->name == 'user6' ) {$valuser6=$valuser->guid; $foundUser6 = true;}
   if ( $valuser->name == 'user7' ) {$valuser7=$valuser->guid; $foundUser7 = true;}

  }

  $groups = ws_groupList();
  foreach ( $groups->groups as $key => $valgroups ) {

   if ( $valgroups ->name == 'Group One'  ){$valgroups1=$valgroups->guid; $foundGroup1 = true;}
   if ( $valgroups ->name == 'Group Two'  ){$valgroups2=$valgroups->guid; $foundGroup2 = true;}
   if ( $valgroups ->name == 'Group Three'){$valgroups3=$valgroups->guid; $foundGroup3 = true;}
   if ( $valgroups ->name == 'Group Four' ){$valgroups4=$valgroups->guid; $foundGroup4 = true;}
   if ( $valgroups ->name == 'Group Five' ){$valgroups5=$valgroups->guid; $foundGroup5 = true;}
   if ( $valgroups ->name == 'Group Six'  ){$valgroups6=$valgroups->guid; $foundGroup6 = true;}
   if ( $valgroups ->name == 'Group Seven'){$valgroups7=$valgroups->guid; $foundGroup7 = true;}

   if ( $valgroups ->name == 'supervisors'){$valgroupsSupervisors =$valgroups->guid; $supervisors = true;}
   if ( $valgroups ->name == 'employees')	 {$valgroupsEmployees   =$valgroups->guid; $employees   = true;}
   if ( $valgroups ->name == 'finance')    {$valgroupsFinance     =$valgroups->guid; $finance     = true;}
  }
 //Test unit
 $groups= ws_assignUserToGroup ( $valuser1, $valgroups1 );
 $groups= ws_assignUserToGroup ( $valuser2, $valgroups2 );
 $groups= ws_assignUserToGroup ( $valuser3, $valgroups3 );
 $groups= ws_assignUserToGroup ( $valuser4, $valgroups4 );
 $groups= ws_assignUserToGroup ( $valuser5, $valgroups5 );
 $groups= ws_assignUserToGroup ( $valuser6, $valgroups6 );
 $groups= ws_assignUserToGroup ( $valuser7, $valgroups7 );




 // Expend report
 $groups= ws_assignUserToGroup ( $valuser1, $valgroupsSupervisors);
 $groups= ws_assignUserToGroup ( $valuser2, $valgroupsEmployees );
 $groups= ws_assignUserToGroup ( $valuser3, $valgroupsFinance );

  $t->is( $foundUser1, true,  'user1 is present in Group One');
  $t->is( $foundUser2, true,  'user2 is present in Group Two');
  $t->is( $foundUser3, true,  'user3 is present in Group Three');
  $t->is( $foundUser3, true,  'user4 is present in Group Four');
  $t->is( $foundUser3, true,  'user5 is present in Group Five');
  $t->is( $foundUser3, true,  'user6 is present in Group Six');
  $t->is( $foundUser3, true,  'user7 is present in Group Seven');

  $t->is( $foundUser1, true,  'user1 is present in Group supervisors');
  $t->is( $foundUser2, true,  'user2 is present in Group employees');
  $t->is( $foundUser3, true,  'user3 is present in Group finance');

