<?php
/**
 * cases_Scheduler_Log_Detail.php
 *
 * ProcessMaker Open Source Edition
 * Copyright (C) 2004 - 2010 Colosa Inc.23
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 * For more information, contact Colosa Inc, 2566 Le Jeune Rd.,
 * Coral Gables, FL, 33134, USA, or email info@colosa.com.
 */
try {
    global $RBAC;

    /*
  switch ($RBAC->userCanAccess('PM_FACTORY'))
  {
  	case -2:
  	  G::SendTemporalMessage('ID_USER_HAVENT_RIGHTS_SYSTEM', 'error', 'labels');
  	  G::header('location: ../login/login');
  	  die;
  	break;
  	case -1:
  	  G::SendTemporalMessage('ID_USER_HAVENT_RIGHTS_PAGE', 'error', 'labels');
  	  G::header('location: ../login/login');
  	  die;
  	break;
  }
*/

/*
  $aFields['MESSAGE0']   = str_replace("\r\n","<br>",G::LoadTranslation('ID_USER_REGISTERED')) . '!';
  $aFields['MESSAGE1']   = str_replace("\r\n","<br>",G::LoadTranslation('ID_MSG_ERROR_USR_USERNAME'));
  $aFields['MESSAGE2']   = str_replace("\r\n","<br>",G::LoadTranslation('ID_MSG_ERROR_DUE_DATE'));
  $aFields['MESSAGE3']   = str_replace("\r\n","<br>",G::LoadTranslation('ID_NEW_PASS_SAME_OLD_PASS'));
  $aFields['MESSAGE4']   = str_replace("\r\n","<br>",G::LoadTranslation('ID_MSG_ERROR_USR_FIRSTNAME'));
  $aFields['MESSAGE5']   = str_replace("\r\n","<br>",G::LoadTranslation('ID_MSG_ERROR_USR_LASTNAME'));
  // the default role variable sets the value that will be showed as the default for the role field.
  $aFields['DEFAULT_ROLE']   = 'PROCESSMAKER_OPERATOR';
  $aFields['START_DATE'] = date('Y-m-d');
  $aFields['END_DATE']   = date('Y-m-d', mktime(0, 0, 0, date('m'), date('d'), date('Y') + 5));
  $aFields['USR_DUE_DATE']= date('Y-m-d', mktime(0, 0, 0, date('m'), date('d'), date('Y') + 1));
*/
  if (! class_exists( 'LogCasesSchedulerPeer' )) {
        require_once ('classes/model/LogCasesScheduler.php');
    }

    $G_PUBLISH = new Publisher();
    $oCriteria = new Criteria( 'workflow' );
    //  var_dump(htmlspecialchars($_GET['WS_ROUTE']));
    //  var_dump(htmlentities($_GET['WS_ROUTE']));


    $oCriteria->add( LogCasesSchedulerPeer::LOG_CASE_UID, $_REQUEST['LOG_CASE_UID'] );
    $result = LogCasesSchedulerPeer::doSelectRS( $oCriteria );
    $result->next();
    $row = $result->getRow();
    $aFields['PRO_UID'] = $row[1];
    $aFields['TAS_UID'] = $row[2];
    $aFields['SCH_UID'] = $row[7];
    $aFields['USR_NAME'] = $row[3];
    $aFields['EXEC_DATE'] = $row[4];
    $aFields['EXEC_HOUR'] = $row[5];
    $aFields['RESULT'] = $row[6];
    $aFields['WS_CREATE_CASE_STATUS'] = $row[8];
    $aFields['WS_ROUTE_CASE_STATUS'] = htmlentities( $row[9] );
    //var_dump($aFields);
    //$aFields = $_GET;
    $G_PUBLISH->AddContent( 'xmlform', 'xmlform', 'cases/cases_Scheduler_Log_Detail.xml', '', $aFields, '' );
    G::RenderPage( 'publishBlank', 'blank' );

} catch (Exception $oException) {
    die( $oException->getMessage() );
}

