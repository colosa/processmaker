<?php
/**
 * users_ReassignCases.php
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
try {
    global $G_PUBLISH;
    $G_PUBLISH = new Publisher();
    $_GET['iStep'] = (int) $_GET['iStep'];
    switch ($_GET['iStep']) {
        case 1:
            $G_PUBLISH->AddContent( 'xmlform', 'xmlform', 'users/users_ReassignSelectType', '', array ('USR_UID' => $_GET['USR_UID']
            ), '' );
            break;
        case 2:
            switch ($_POST['TYPE']) {
                case 'ANY_USER':
                    $G_PUBLISH->AddContent( 'xmlform', 'xmlform', 'users/users_ReassignSelectSubType', '', $_POST, '' );
                    break;
            }
            break;
        case 3:
            switch ($_POST['SUB_TYPE']) {
                case 'PROCESS':
                    require_once 'classes/model/Users.php';
                    $oCriteria = new Criteria( 'workflow' );
                    $oCriteria->addSelectColumn( UsersPeer::USR_UID );
                    /*
            $usr_completename_col = "CONCAT(USR_LASTNAME, ' ', USR_FIRSTNAME, ' (', USR_USERNAME, ')')";
          */
                    $sDataBase = 'database_' . strtolower( DB_ADAPTER );
                    if (G::LoadSystemExist( $sDataBase )) {

                        $oDataBase = new database();
                        $usr_completename_col = $oDataBase->concatString( "USR_LASTNAME", "' '", "USR_FIRSTNAME", " '('", "USR_USERNAME", "')'" );
                    }

                    $oCriteria->addAsColumn( 'USR_COMPLETENAME', $usr_completename_col );

                    $oCriteria->add( UsersPeer::USR_UID, $_POST['USR_UID'], Criteria::NOT_EQUAL );
                    $oCriteria->add( UsersPeer::USR_STATUS, array ('CLOSED'
                    ), Criteria::NOT_IN );
                    $oDataset = UsersPeer::doSelectRS( $oCriteria );
                    $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );
                    $oDataset->next();
                    $sUsers = '<option value=""> - ' . G::LoadTranslation( 'ID_NO_REASSIGN' ) . ' - </option>';
                    while ($aRow = $oDataset->getRow()) {
                        $sUsers .= '<option value="' . $aRow['USR_UID'] . '">' . $aRow['USR_COMPLETENAME'] . '</option>';
                        $oDataset->next();
                    }
                    $aProcesses = array ();
                    $aProcesses[] = array ('CHECKBOX' => 'char','PROCESS' => 'char','CANTITY' => 'char','USERS' => 'char'
                    );
                    $del = DBAdapter::getStringDelimiter();
                    require_once 'classes/model/AppDelegation.php';
                    $oCriteria = new Criteria( 'workflow' );
                    $oCriteria->addSelectColumn( AppDelegationPeer::PRO_UID );
                    $oCriteria->addSelectColumn( ProcessPeer::PRO_TITLE );
                    $oCriteria->addSelectColumn( 'COUNT(' . AppDelegationPeer::PRO_UID . ') AS CANTITY' );
                    $oCriteria->addJoin(AppDelegationPeer::PRO_UID, ProcessPeer::PRO_UID);
                    $oCriteria->add( AppDelegationPeer::USR_UID, $_POST['USR_UID'] );
                    $oCriteria->add( AppDelegationPeer::DEL_FINISH_DATE, null, Criteria::ISNULL );
                    $oCriteria->addGroupByColumn( AppDelegationPeer::PRO_UID );

                    /*
           * Adding grouped by standardization.
           */
                    $oCriteria->addGroupByColumn( ContentPeer::CON_VALUE );

                    $oDataset = AppDelegationPeer::doSelectRS( $oCriteria );
                    $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );
                    $oDataset->next();
                    while ($aRow = $oDataset->getRow()) {
                        $aProcesses[] = array ('CHECKBOX' => '<input type="checkbox" name="PROCESS[' . $aRow['PRO_UID'] . ']" id="PROCESS[' . $aRow['PRO_UID'] . ']" />','PROCESS' => $aRow['PRO_TITLE'],'CANTITY' => $aRow['CANTITY'],'USERS' => '<select name="USER[' . $aRow['PRO_UID'] . ']" id="USER[' . $aRow['PRO_UID'] . ']">' . $sUsers . '</select>'
                        );
                        $oDataset->next();
                    }
                    global $_DBArray;
                    $_DBArray['processesToReassign'] = $aProcesses;
                    $_SESSION['_DBArray'] = $_DBArray;

                    $oCriteria = new Criteria( 'dbarray' );
                    $oCriteria->setDBArrayTable( 'processesToReassign' );
                    $G_PUBLISH->AddContent( 'propeltable', 'cases/paged-table-reassign', 'users/users_ReassignCases', $oCriteria, $_POST );
                    break;
            }
            break;
    }
    G::RenderPage( 'publish', 'raw' );
} catch (Exception $oException) {
    $token = strtotime("now");
    PMException::registerErrorLog($oException, $token);
    G::outRes( G::LoadTranslation("ID_EXCEPTION_LOG_INTERFAZ", array($token)) );
    die;
}

