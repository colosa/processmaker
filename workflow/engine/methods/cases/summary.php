<?php
/**
 * summary.php
 *
 * ProcessMaker Open Source Edition
 * Copyright (C) 2004 - 2011 Colosa Inc.
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
    switch ($RBAC->userCanAccess( 'PM_CASES' )) {
        case - 2:
            throw new Exception( G::LoadTranslation( 'ID_USER_HAVENT_RIGHTS_SYSTEM' ) );
            break;
        case - 1:
            throw new Exception( G::LoadTranslation( 'ID_USER_HAVENT_RIGHTS_PAGE' ) );
            break;
    }

    if (! isset( $_REQUEST['APP_UID'] ) || ! isset( $_REQUEST['DEL_INDEX'] ) || ! isset( $_REQUEST['DYN_UID'] )) {
        throw new Exception( G::LoadTranslation( 'ID_REQUIRED_FIELDS_ERROR' ) . ' (APP_UID, DEL_INDEX, DYN_UID)' );
    }

    if ($_REQUEST['APP_UID'] == '' || $_REQUEST['DEL_INDEX'] == '' || $_REQUEST['DYN_UID'] == '') {
        throw new Exception( G::LoadTranslation( 'ID_REQUIRED_FIELDS_ERROR' ) . ' (APP_UID, DEL_INDEX, DYN_UID)' );
    }
    G::LoadClass( 'case' );
    $case = new Cases();
    if ($RBAC->userCanAccess( 'PM_ALLCASES' ) < 0 && $case->userParticipatedInCase( $_REQUEST['APP_UID'], $_SESSION['USER_LOGGED'] ) == 0) {
        throw new Exception( G::LoadTranslation( 'ID_NO_PERMISSION_NO_PARTICIPATED' ) );
    }
    $applicationFields = $case->loadCase( $_REQUEST['APP_UID'], $_REQUEST['DEL_INDEX'] );

    if (file_exists( PATH_DYNAFORM . $applicationFields['PRO_UID'] . PATH_SEP . $_REQUEST['DYN_UID'] . '.xml' )) {
        $applicationFields['APP_DATA']['__DYNAFORM_OPTIONS']['PREVIOUS_STEP_LABEL'] = '';
        $applicationFields['APP_DATA']['__DYNAFORM_OPTIONS']['PREVIOUS_STEP'] = '#';
        $applicationFields['APP_DATA']['__DYNAFORM_OPTIONS']['NEXT_STEP_LABEL'] = '';
        $applicationFields['APP_DATA']['__DYNAFORM_OPTIONS']['NEXT_ACTION'] = '#';

        G::LoadClass( 'dbConnections' );
        $_SESSION['PROCESS'] = $applicationFields['PRO_UID'];
        $dbConnections = new dbConnections( $_SESSION['PROCESS'] );
        $dbConnections->loadAdditionalConnections();
        $_SESSION['CURRENT_DYN_UID'] = $_REQUEST['DYN_UID'];

        global $G_PUBLISH;
        $G_PUBLISH = new Publisher();
        $G_PUBLISH->AddContent( 'dynaform', 'xmlform', $applicationFields['PRO_UID'] . '/' . $_REQUEST['DYN_UID'], '', $applicationFields['APP_DATA'], '', '', 'view' );
        G::RenderPage( 'publish', 'blank' );
    } else {
        throw new Exception( G::LoadTranslation( 'INVALID_FILE' ) . ': ' . $_REQUEST['DYN_UID'] );
    }
} catch (Exception $error) {
    global $G_PUBLISH;
    $G_PUBLISH = new Publisher();
    $G_PUBLISH->AddContent( 'xmlform', 'xmlform', 'login/showMessage', '', array ('MESSAGE' => $error->getMessage()
    ) );
    G::RenderPage( 'publish', 'blank' );
    die();
}

