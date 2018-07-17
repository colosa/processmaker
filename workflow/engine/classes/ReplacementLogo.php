<?php

/**
 *
 * @package workflow.engine.classes
 */
class ReplacementLogo
{

    //var $dir='';
    /**
     * This function is the constructor of the ReplacementLogo class
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
