<?php
/**
 * cases_Events_Save.php
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
    $oJSON = new Services_JSON();
    if (isset( $_POST['sData'] )) {
        $oData = $oJSON->decode( $_POST['sData'] );
    }

    //Convert Object into Array
    foreach ($oData as $key => $value) {
        $aData[$key] = $value;
    }

    require_once 'classes/model/Event.php';

    $oEvent = new Event();
    if (! isset( $aData['EVN_UID'] ) && $aData['EVN_UID'] == '') {
        $sEventUid = $oEvent->create( $aData );
    } else {
        $sEventUid = $oEvent->update( $aData );
    }
    $_SESSION['EVN_UID'] = $sEventUid;
    echo "{success: true,data:'.$sEventUid.'}";
    //G::header('location: cases_Scheduler_List');
} catch (Exception $oException) {
    die( $oException->getMessage() );
}

