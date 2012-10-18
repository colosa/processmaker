<?php
/**
 * triggers_Delete.php
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

/**
 * Triggers Ajax Request HAndler
 *
 * @author Erik A.O. <erik@colosa.com, aortiz.erik@gmail.com>
 * @date Apr 29th, 2010
 */

$request = isset( $_POST['request'] ) ? $_POST['request'] : '';

switch ($request) {
    case 'verifyDependencies':
        require_once 'classes/model/Triggers.php';

        if (! isset( $_POST['TRI_UID'] )) {
            throw new Exception( 'Missing trigger ID for the request [verifyDependencies]' );
            exit( 0 );
        }

        $oTrigger = new Triggers();
        $oResult = $oTrigger->verifyDependecies( $_POST['TRI_UID'] );
        $oResult->passed = false;
        if ($oResult->code == 0) {
            $oResult->passed = true;
            $oResult->message = G::LoadTranslation( 'ID_TRIGGERS_VALIDATION' ); //"No Dependencies were found for this trigger in Events definitions\n";
        } else {
            $oResult->message = '';
            foreach ($oResult->dependencies as $Object => $aDeps) {
                $nDeps = count( $aDeps );
                $message = str_replace( '{N}', $nDeps, G::LoadTranslation( 'ID_TRIGGERS_VALIDATION_ERR2' ) );
                $message = str_replace( '{Object}', $Object, $message );
                $oResult->message .= $message . "\n";
                foreach ($aDeps as $dep) {
                    if (substr( $Object, - 1 ) == 's') {
                        $Object = substr( $Object, 0, strlen( $Object ) - 1 );
                    }
                    $message = str_replace( '{Object}', $Object, G::LoadTranslation( 'ID_TRIGGERS_VALIDATION_ERR3' ) );
                    $message = str_replace( '{Description}', '"' . $dep['DESCRIPTION'] . '"', $message );
                    $oResult->message .= $message . "\n";
                }
                $oResult->message .= "\n";
            }
        }
        $oResult->success = true;
        //print_r($oResult);
        print G::json_encode( $oResult );
        break;
    default:
        echo 'default';
}

