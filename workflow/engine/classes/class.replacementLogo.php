<?php

/**
 * class.replacementLogo.php
 *
 * @package workflow.engine.classes
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

/**
 *
 * @package workflow.engine.classes
 */

class replacementLogo
{

    //var $dir='';
    /**
     * This function is the constructor of the replacementLogo class
     * param
     *
     * @return void
     */
    public function replacementLogo ()
    {

    }

    /**
     * This function upload a file
     *
     *
     * @name upLoadFile
     *
     * @param string $dirUpload
     * @param string $namefile
     * @param string $typefile
     * @param string $errorfile
     * @param string $tpnfile
     * @param string $formf
     * @return string
     */
    public function upLoadFile ($dirUpload, $namefile, $typefile, $errorfile, $tpnfile, $formf)
    {

        //we are cheking the extension for file
        $aExt = explode( ".", $namefile );
        $infoupload = ''; //|| ($formf["type"]['uploadfile'] == "application/octet-stream")image/png
        if (($typefile == "image/jpeg") || ($typefile == "image/png")) {
            if ($errorfile > 0) {
                $infoupload = "Return Code: " . $errorfile . "<br />";
            } else {
                if (file_exists( $dirUpload . $namefile )) {
                    $infoupload = $namefile . " already exist. ";
                } else {
                    move_uploaded_file( $tpnfile, $dirUpload . $namefile );
                    $infoupload = "Stored in: " . $dirUpload . $namefile;
                }
            }
        } else {
            $infoupload = "- " . $typefile . " Invalid file your file should be jpeg";
        }
        return $infoupload;
    }

    /**
     * This function gets the logos' names
     *
     *
     * @name getNameLogo
     *
     * param
     * @return array
     */
    public function getNameLogo ($usrUid)
    {

        require_once 'classes/model/Configuration.php';
        $oCriteria = new Criteria( 'workflow' );
        $oCriteria->addSelectColumn( ConfigurationPeer::CFG_VALUE );
        $oCriteria->add( ConfigurationPeer::CFG_UID, 'USER_LOGO_REPLACEMENT' );
        $oDataset = ConfigurationPeer::doSelectRS( $oCriteria );
        $oDataset->next();
        $aRow = $oDataset->getRow();
        if (isset( $aRow[0] )) {
            $ainfoLogo = @unserialize( $aRow[0] );
        } else {
            $ainfoLogo = null;
        }
        return ($ainfoLogo);
    }
}

