<?php
/**
 * dynaforms_Save.php
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
require_once ('classes/model/Content.php');

$oJSON = new Services_JSON();

if (isset( $_POST['function'] )) {
    $sfunction = $_POST['function'];
} elseif (isset( $_POST['functions'] )) {
    $sfunction = $_POST['functions'];
}

if (isset( $sfunction ) && $sfunction == 'lookforNameDynaform') {

    $snameDyanform = urldecode( $_POST['NAMEDYNAFORM'] );
    $sPRO_UID = urldecode( $_POST['proUid'] );

    $oCriteria = new Criteria( 'workflow' );
    $oCriteria->addSelectColumn( DynaformPeer::DYN_UID );
    $oCriteria->add( DynaformPeer::PRO_UID, $sPRO_UID );
    $oDataset = DynaformPeer::doSelectRS( $oCriteria );
    $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );
    $flag = true;

    while ($oDataset->next() && $flag) {
        $aRow = $oDataset->getRow();

        $oCriteria1 = new Criteria( 'workflow' );
        $oCriteria1->addSelectColumn( 'COUNT(*) AS DYNAFORMS' );
        $oCriteria1->add( ContentPeer::CON_CATEGORY, 'DYN_TITLE' );
        $oCriteria1->add( ContentPeer::CON_ID, $aRow['DYN_UID'] );
        $oCriteria1->add( ContentPeer::CON_VALUE, $snameDyanform );
        $oCriteria1->add( ContentPeer::CON_LANG, SYS_LANG );
        $oDataset1 = ContentPeer::doSelectRS( $oCriteria1 );
        $oDataset1->setFetchmode( ResultSet::FETCHMODE_ASSOC );
        $oDataset1->next();
        $aRow1 = $oDataset1->getRow();

        if ($aRow1['DYNAFORMS']) {
            $flag = false;
        }
    }
    print $flag;

} else {
    $dynaform = new dynaform();
    if (isset( $_POST['form'] )) {
        $aData = $_POST['form']; //For old process map form
        if ($aData['DYN_UID'] === '') {
            unset( $aData['DYN_UID'] );
        }
    } else {
        $aData = $_POST; //For Extjs (Since we are not using form in ExtJS)
        $aFields = array ();
        $aVariables = array ();
        if (isset( $aData['FIELDS'] )) {
            $aFields = G::json_decode( $_POST['FIELDS'] );
            $aVariables = G::json_decode( $_POST['VARIABLES'] );
        }
        $aData['FIELDS'] = array ();
        for ($i = 0; $i < count( $aFields ); $i ++) {
            $aData['FIELDS'][$i + 1]['FLD_NAME'] = $aFields[$i];
            $aData['FIELDS'][$i + 1]['PRO_VARIABLE'] = $aVariables[$i];
        }
    }
    //if ($aData['DYN_UID']==='') unset($aData['DYN_UID']);


    if (isset( $aData['DYN_UID'] )) {
        $dynaform->Save( $aData );
    } else {
        if (! isset( $aData['ADD_TABLE'] ) || $aData['ADD_TABLE'] == "") {
            $aFields = $dynaform->create( $aData );
        } else {
            $aFields = $dynaform->createFromPMTable( $aData, $aData['ADD_TABLE'] );
        }
        $aData['DYN_UID'] = $dynaform->getDynUid();
        $dynaform->update( $aData );
    }
    echo $dynaform->getDynUid();
}

