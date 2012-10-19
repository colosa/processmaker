<?php
/**
 * cases_Scheduler_List.php
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

if (($RBAC_Response = $RBAC->userCanAccess( "PM_LOGIN" )) != 1) {
    return $RBAC_Response;
}
global $RBAC;
/*switch ($RBAC->userCanAccess('PM_USERS'))
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
}*/

$G_MAIN_MENU = 'processmaker';
$G_SUB_MENU = 'cases';

$G_ID_MENU_SELECTED = 'CASES';
$G_ID_SUB_MENU_SELECTED = 'CASES_SCHEDULER';
require_once 'classes/model/CaseScheduler.php';
$process = isset( $_GET['PRO_UID'] ) ? $_GET['PRO_UID'] : $_SESSION['PROCESS'];
$sDelimiter = DBAdapter::getStringDelimiter();

$oCaseScheduler = new CaseScheduler();
$aRows = $oCaseScheduler->getAllByProcess( $process );

//$oCaseScheduler->caseSchedulerCron();
// g::pr($aRows); die;


$fieldNames = Array ('SCH_UID' => 'char','SCH_NAME' => 'char','PRO_UID' => 'char','TAS_UID' => 'char','SCH_TIME_NEXT_RUN' => 'char','SCH_LAST_RUN_TIME' => 'char','SCH_STATE' => 'char','SCH_LAST_STATE' => 'char','USR_UID' => 'char','SCH_OPTION' => 'char','SCH_START_TIME' => 'char','SCH_START_DATE' => 'char','SCH_DAYS_PERFORM_TASK' => 'char','SCH_EVERY_DAYS' => 'char','SCH_WEEK_DAYS' => 'char','SCH_START_DAY' => 'char','SCH_MONTHS' => 'char','SCH_END_DATE' => 'char','SCH_REPEAT_EVERY' => 'char','SCH_REPEAT_UNTIL' => 'char','SCH_REPEAT_STOP_IF_RUNNING' => 'char','PRO_PARENT' => 'char','PRO_TIME' => 'char','PRO_TIMEUNIT' => 'char','PRO_STATUS' => 'char','PRO_TYPE_DAY' => 'char','PRO_TYPE' => 'char','PRO_ASSIGNMENT' => 'char','PRO_SHOW_MAP' => 'char','PRO_SHOW_MESSAGE' => 'char',
                'PRO_SUBPROCESS' => 'char','PRO_TRI_DELETED' => 'char','PRO_TRI_CANCELED' => 'char','PRO_TRI_PAUSED' => 'char','PRO_TRI_REASSIGNED' => 'char','PRO_SHOW_DELEGATE' => 'char','PRO_SHOW_DYNAFORM' => 'char','PRO_CATEGORY' => 'char','PRO_SUB_CATEGORY' => 'char','PRO_INDUSTRY' => 'char','PRO_UPDATE_DATE' => 'char','PRO_CREATE_DATE' => 'char','PRO_CREATE_USER' => 'char','PRO_HEIGHT' => 'char','PRO_WIDTH' => 'char','PRO_TITLE_X' => 'char','PRO_TITLE_Y' => 'char','PRO_DEBUG' => 'char','PRO_TITLE' => 'char','PRO_DESCRIPTION' => 'char','TAS_TYPE' => 'char','TAS_DURATION' => 'char','TAS_DELAY_TYPE' => 'char','TAS_TEMPORIZER' => 'char','TAS_TYPE_DAY' => 'char','TAS_TIMEUNIT' => 'char','TAS_ALERT' => 'char','TAS_PRIORITY_VARIABLE' => 'char','TAS_ASSIGN_TYPE' => 'char',
                'TAS_ASSIGN_VARIABLE' => 'char','TAS_ASSIGN_LOCATION' => 'char','TAS_ASSIGN_LOCATION_ADHOC' => 'char','TAS_TRANSFER_FLY' => 'char','TAS_LAST_ASSIGNED' => 'char','TAS_USER' => 'char','TAS_CAN_UPLOAD' => 'char','TAS_VIEW_UPLOAD' => 'char','TAS_VIEW_ADDITIONAL_DOCUMENTATION' => 'char','TAS_CAN_CANCEL' => 'char','TAS_OWNER_APP' => 'char','STG_UID' => 'char','TAS_CAN_PAUSE' => 'char','TAS_CAN_SEND_MESSAGE' => 'char','TAS_CAN_DELETE_DOCS' => 'char','TAS_SELF_SERVICE' => 'char','TAS_START' => 'char','TAS_TO_LAST_USER' => 'char','TAS_SEND_LAST_EMAIL' => 'char','TAS_DERIVATION' => 'char','TAS_POSX' => 'char','TAS_POSY' => 'char','TAS_COLOR' => 'char','TAS_TITLE' => 'char','TAS_DESCRIPTION' => 'char','TAS_DEF_TITLE' => 'char','TAS_DEF_DESCRIPTION' => 'char',
                'TAS_DEF_PROC_CODE' => 'char','TAS_DEF_MESSAGE' => 'char'
);

$aRows = array_merge( Array ($fieldNames
), $aRows );
//krumo ($aRows);
for ($j = 0; $j < count( $aRows ); $j ++) {
    if ($aRows[$j]['SCH_STATE'] == 'PROCESSED') {
        $aRows[$j]['SCH_TIME_NEXT_RUN'] = '';
    }
}
// g::pr($aRows); die;


global $_DBArray;
$_DBArray['cases_scheduler'] = $aRows;
$_SESSION['_DBArray'] = $_DBArray;
G::LoadClass( 'ArrayPeer' );
$oCriteria = new Criteria( 'dbarray' );
$oCriteria->setDBArrayTable( 'cases_scheduler' );
//krumo ($oCriteria);
//var_dump ($oCriteria);
//$oCriteria->add('PRO_UID', $_SESSION['PROCESS']);
//krumo($_SESSION);


$G_PUBLISH = new Publisher();
$G_PUBLISH->ROWS_PER_PAGE = 10;
$G_PUBLISH->AddContent( 'propeltable', 'paged-table', 'cases/cases_Scheduler_List', $oCriteria, array ('CONFIRM' => G::LoadTranslation( 'ID_MSG_CONFIRM_DELETE_CASE_SCHEDULER' )
) );
$G_PUBLISH->oPropelTable->rowsPerPage = 10;
G::RenderPage( 'publishBlank', 'blank' );

