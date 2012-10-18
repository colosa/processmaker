<?php

/**
 * autoinstallProcess.php
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
G::LoadClass( 'Installer' );
$inst = new Installer();

G::LoadClass( 'processes' );
$oProcess = new Processes();

//Get Available autoinstall process
$availableProcess = $inst->getDirectoryFiles( PATH_OUTTRUNK . "autoinstall", "pm" );

$path = PATH_OUTTRUNK . "autoinstall" . PATH_SEP;
$message = "";
foreach ($availableProcess as $processfile) {

    $oData = $oProcess->getProcessData( $path . $processfile );
    $Fields['PRO_FILENAME'] = $processfile;
    $Fields['IMPORT_OPTION'] = 2;
    $sProUid = $oData->process['PRO_UID'];
    if ($oProcess->processExists( $sProUid )) {
        $message .= "$processfile - Not imported (process exist)<br>";

    } else {
        $oProcess->createProcessFromData( $oData, $path . $processfile );
        $message .= "$processfile - OK<br>";
    }
}

echo $message;

