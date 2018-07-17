<?php
/**
 * login_Ajax.php
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
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * For more information, contact Colosa Inc, 2566 Le Jeune Rd.,
 * Coral Gables, FL, 33134, USA, or email info@colosa.com.
 *
 */
try {
    if (isset ( $_POST ['form'] )) {
        $_POST = $_POST ['form'];
    }
    $_POST ['function'] = get_ajax_value ( 'function' );
    switch ($_POST ['function']) {
        case 'getStarted_save':
            require_once 'classes/model/Configuration.php';
            $aData ['CFG_UID'] = 'getStarted';
            $aData ['OBJ_UID'] = '';
            $aData ['CFG_VALUE'] = '1';
            $aData ['PRO_UID'] = '';
            $aData ['USR_UID'] = '';
            $aData ['APP_UID'] = '';
            $oConfig = new Configuration ( );
            $oConfig->create ( $aData );
            break;
    }
} catch ( Exception $oException ) {
    $token = strtotime("now");
    PMException::registerErrorLog($oException, $token);
    G::outRes( G::LoadTranslation("ID_EXCEPTION_LOG_INTERFAZ", array($token)) );
    die;
}

