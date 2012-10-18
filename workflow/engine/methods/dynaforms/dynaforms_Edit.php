<?php
/**
 * dynaforms_Edit.php
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
if (($RBAC_Response = $RBAC->userCanAccess( "PM_FACTORY" )) != 1) {
    return $RBAC_Response;
}

    //G::genericForceLogin( 'WF_MYINFO' , 'login/noViewPage', $urlLogin = 'login/login' );


require_once ('classes/model/Dynaform.php');
require_once ('classes/model/AdditionalTables.php');

$oCriteria = new Criteria( 'workflow' );
$oCriteria->addSelectColumn( AdditionalTablesPeer::ADD_TAB_UID );
$oCriteria->addSelectColumn( AdditionalTablesPeer::ADD_TAB_NAME );
$oCriteria->add( AdditionalTablesPeer::ADD_TAB_UID, '', Criteria::NOT_EQUAL );

$oDataset = AdditionalTablesPeer::doSelectRS( $oCriteria );
$oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );

$aTablesList = Array ();
array_push( $aTablesList, array ('ADD_TAB_UID' => '','ADD_TAB_NAME' => '----------------') );
while ($oDataset->next()) {
    array_push( $aTablesList, $oDataset->getRow() );
}

$filedNames = Array ("ADD_TAB_UID","ADD_TAB_NAME");

$aTablesList = array_merge( Array ($filedNames), $aTablesList );

$_DBArray['ADDITIONAL_TABLES'] = $aTablesList;
$_SESSION['_DBArray'] = $_DBArray;

$dynUid = (isset( $_GET['DYN_UID'] )) ? urldecode( $_GET['DYN_UID'] ) : '';
$dynaform = new dynaform();
if ($dynUid == '') {
    $aFields['DYN_UID'] = $dynUid;
} else {
    $aFields = $dynaform->load( $dynUid );
}
$aFields['PRO_UID'] = isset( $dynaform->Fields['PRO_UID'] ) ? $dynaform->Fields['PRO_UID'] : $_GET['PRO_UID'];

$aFields['ACTION'] = isset( $_GET['ACTION'] ) ? $_GET['ACTION'] : '';
//$aFields['READ_ONLY'] = ($_GET['ACTION']=='normal')?0:1;
G::LoadClass( 'xmlfield_InputPM' );
$G_PUBLISH = new Publisher();
$G_PUBLISH->AddContent( 'xmlform', 'xmlform', 'dynaforms/dynaforms_Edit', '', $aFields, SYS_URI . 'dynaforms/dynaforms_Save' );

G::RenderPage( "publish-raw", "raw" );

