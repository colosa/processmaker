<?php
/**
 * replacementLogo.php
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
try { //ini_set('display_errors','1');
    global $RBAC;
    switch ($RBAC->userCanAccess( 'PM_LOGIN' )) {
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

    function changeNamelogo ($snameLogo)
    {
        // The ereg_replace function has been DEPRECATED as of PHP 5.3.0.
        // $snameLogo = ereg_replace("[Ã¡Ã Ã¢Ã£Âª]","a",$snameLogo);
        // $snameLogo = ereg_replace("[Ã�Ã€Ã‚Ãƒ]","A",$snameLogo);
        // $snameLogo = ereg_replace("[Ã�ÃŒÃŽ]","I",$snameLogo);
        // $snameLogo = ereg_replace("[Ã­Ã¬Ã®]","i",$snameLogo);
        // $snameLogo = ereg_replace("[Ã©Ã¨Ãª]","e",$snameLogo);
        // $snameLogo = ereg_replace("[Ã‰ÃˆÃŠ]","E",$snameLogo);
        // $snameLogo = ereg_replace("[Ã³Ã²Ã´ÃµÂº]","o",$snameLogo);
        // $snameLogo = ereg_replace("[Ã“Ã’Ã”Ã•]","O",$snameLogo);
        // $snameLogo = ereg_replace("[ÃºÃ¹Ã»]","u",$snameLogo);
        // $snameLogo = ereg_replace("[ÃšÃ™Ã›]","U",$snameLogo);
        $snameLogo = preg_replace( "/[Ã¡Ã Ã¢Ã£Âª]/", "a", $snameLogo );
        $snameLogo = preg_replace( "/[Ã�Ã€Ã‚Ãƒ]/", "A", $snameLogo );
        $snameLogo = preg_replace( "/[Ã�ÃŒÃŽ]/", "I", $snameLogo );
        $snameLogo = preg_replace( "/[Ã­Ã¬Ã®]/", "i", $snameLogo );
        $snameLogo = preg_replace( "/[Ã©Ã¨Ãª]/", "e", $snameLogo );
        $snameLogo = preg_replace( "/[Ã‰ÃˆÃŠ]/", "E", $snameLogo );
        $snameLogo = preg_replace( "/[Ã³Ã²Ã´ÃµÂº]/", "o", $snameLogo );
        $snameLogo = preg_replace( "/[Ã“Ã’Ã”Ã•]/", "O", $snameLogo );
        $snameLogo = preg_replace( "/[ÃºÃ¹Ã»]/", "u", $snameLogo );
        $snameLogo = preg_replace( "/[ÃšÃ™Ã›]/", "U", $snameLogo );
        $snameLogo = str_replace( "Ã§", "c", $snameLogo );
        $snameLogo = str_replace( "Ã‡", "C", $snameLogo );
        $snameLogo = str_replace( "[Ã±]", "n", $snameLogo );
        $snameLogo = str_replace( "[Ã‘]", "N", $snameLogo );
        return ($snameLogo);
    }

    $sfunction = $_GET['function'];
    switch ($sfunction) {
        case 'replacementLogo':
            $snameLogo = urldecode( $_GET['NAMELOGO'] );
            $snameLogo = trim( $snameLogo );
            $snameLogo = changeNamelogo( $snameLogo );
            G::loadClass( 'configuration' );
            $oConf = new Configurations();
            $aConf = Array ('WORKSPACE_LOGO_NAME' => SYS_SYS,'DEFAULT_LOGO_NAME' => $snameLogo
            );

            $oConf->aConfig = $aConf;
            $oConf->saveConfig( 'USER_LOGO_REPLACEMENT', '', '', '' );

            G::SendTemporalMessage( 'ID_REPLACED_LOGO', 'tmp-info', 'labels' );
            //header('location: uplogo.php');
            //G::header('location: uplogo');
            break;
        case 'restoreLogo':
            $snameLogo = $_GET['NAMELOGO'];
            G::loadClass( 'configuration' );
            $oConf = new Configurations();
            $aConf = Array ('WORKSPACE_LOGO_NAME' => '','DEFAULT_LOGO_NAME' => ''
            );

            $oConf->aConfig = $aConf;
            $oConf->saveConfig( 'USER_LOGO_REPLACEMENT', '', '', '' );

            G::SendTemporalMessage( 'ID_REPLACED_LOGO', 'tmp-info', 'labels' );
            break;

    }

} catch (Exception $oException) {
    die( $oException->getMessage() );
}

