<?php
/**
 * users_Reassign.php
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
    $oCase = new Cases();
    foreach ($_POST['USER'] as $sProcessUID => $sUserUID) {
        if ($sUserUID != '') {
            $oCriteria = new Criteria( 'workflow' );
            $oCriteria->addSelectColumn( AppDelegationPeer::APP_UID );
            $oCriteria->addSelectColumn( AppDelegationPeer::DEL_INDEX );
            $oCriteria->add( AppDelegationPeer::PRO_UID, $sProcessUID );
            $oCriteria->add( AppDelegationPeer::USR_UID, $_POST['USR_UID'] );
            $oCriteria->add( AppDelegationPeer::DEL_FINISH_DATE, null, Criteria::ISNULL );
            $oDataset = AppDelegationPeer::doSelectRS( $oCriteria );
            $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );
            $oDataset->next();
            while ($aRow = $oDataset->getRow()) {
                $oCase->reassignCase( $aRow['APP_UID'], $aRow['DEL_INDEX'], $_SESSION['USER_LOGGED'], $sUserUID );
                $oDataset->next();
            }
        }
    }
} catch (Exception $oException) {
    $token = strtotime("now");
    PMException::registerErrorLog($oException, $token);
    G::outRes( G::LoadTranslation("ID_EXCEPTION_LOG_INTERFAZ", array($token)) );
    die;
}

