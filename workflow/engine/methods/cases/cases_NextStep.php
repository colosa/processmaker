<?php
/**
 * cases_NextStep.php
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
if (!isset($_SESSION['USER_LOGGED'])) {
    G::SendTemporalMessage( 'ID_LOGIN_AGAIN', 'warning', 'labels' );
    die( '<script type="text/javascript">
              parent.location = "../cases/casesStartPage?action=startCase";
          </script>');
}
if (($RBAC_Response = $RBAC->userCanAccess( "PM_CASES" )) != 1) {
    return $RBAC_Response;
}

$oCase = new Cases();
$aNextStep = $oCase->getNextStep( $_SESSION['PROCESS'], $_SESSION['APPLICATION'], $_SESSION['INDEX'], $_SESSION['STEP_POSITION'] );
$_SESSION['STEP_POSITION'] = $aNextStep['POSITION'];
G::header( 'location: ' . $aNextStep['PAGE'] );

