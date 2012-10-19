<?php
/**
 * cases_CatchExecute.php
 *
 * ProcessMaker Open Source Edition
 * Copyright (C) 2004 - 2008 Colosa Inc.23
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

/* Permissions */
switch ($RBAC->userCanAccess( 'PM_CASES' )) {
    case - 2:
        G::SendTemporalMessage( 'ID_USER_HAVENT_RIGHTS_SYSTEM', 'error', 'labels' );
        G::header( 'location: ../login/login' );
        die();
        break;
    case - 1:
        G::SendTemporalMessage( 'ID_USER_HAVENT_RIGHTS_PAGE', 'error', 'labels' );
        G::header( 'location: ../login/login' );
        die();
        break;
}

if (isset( $_POST['form']['BTN_CANCEL'] )) {
    header( "Location: ../cases/main" );
    die();
}

/* Includes */
G::LoadClass( 'case' );

$oCase = new Cases();
$sAppUid = $_SESSION['APPLICATION'];
$iDelIndex = $_SESSION['INDEX'];

$oAppDelegation = new AppDelegation();
$aDelegation = $oAppDelegation->load( $sAppUid, $iDelIndex );

//if there are no user in the delegation row, this case is still in selfservice
if ($aDelegation['USR_UID'] == "") {
    $oCase->setCatchUser( $_SESSION['APPLICATION'], $_SESSION['INDEX'], $_SESSION['USER_LOGGED'] );
} else {
    G::SendMessageText( G::LoadTranslation( 'ID_CASE_ALREADY_DERIVATED' ), 'error' );
}

$validation = (SYS_SKIN != 'uxs') ? 'true' : 'false';

die( '<script type="text/javascript">
  if (' . $validation . ') {
      if (window.parent.frames.length != 0) {
          parent.location = "open?APP_UID=' . $_SESSION['APPLICATION'] . '&DEL_INDEX=' . $_SESSION['INDEX'] . '&action=unassigned";
      } else {
          window.location = "../cases/cases_Open?APP_UID=' . $_SESSION['APPLICATION'] . '&DEL_INDEX=' . $_SESSION['INDEX'] . '&action=unassigned";
      }
  } else {
      window.location = "../cases/cases_Open?APP_UID=' . $_SESSION['APPLICATION'] . '&DEL_INDEX=' . $_SESSION['INDEX'] . '&action=unassigned";
  }
  </script>' );

