<?php
/**
 * reports_Description.php
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

global $RBAC;
switch ($RBAC->userCanAccess( 'PM_REPORTS' )) {
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

$G_MAIN_MENU = 'processmaker';
$G_ID_MENU_SELECTED = 'REPORTS';

$PRO_UID = $_POST['PRO_UID'];

$oReport = new Report();

/*
$sw=0;
if(isset($_POST['FROM']) && isset($_POST['TO'])&&  isset($_POST['STARTEDBY']))
{
	if($_POST['FROM']!='0000-00-00' || $_POST['TO']!='0000-00-00')$sw=1;
}

if($sw==0)*/
$c = $oReport->descriptionReport1( $PRO_UID );
/*
else
      $c = $oReport->reports_Description_filter($_POST['FROM'], $_POST['TO'], $_POST['STARTEDBY'], $PRO_UID);
*/
$fields['PRO_UID'] = $PRO_UID;

$G_PUBLISH = new Publisher();
$G_PUBLISH->AddContent( 'propeltable', 'paged-table', 'reports/reports_Description', $c );
//$G_PUBLISH->AddContent('xmlform', 'xmlform', 'reports/reports_Description_search', '', $fields);
G::RenderPage( 'publish', 'raw' );

