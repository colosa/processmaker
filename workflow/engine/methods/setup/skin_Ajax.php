<?php
if (! isset( $_REQUEST['action'] )) {
    $res['success'] = false;
    $res['error'] = $res['message'] = G::LoadTranslation('ID_REQUEST_ACTION');

    print G::json_encode( $res );
    die();
}
if (! function_exists( $_REQUEST['action'] )) {
    $res['success'] = false;
    $res['error'] = $res['message'] = G::LoadTranslation('ID_REQUEST_ACTION_NOT_EXIST');

    print G::json_encode( $res );
    die();
}
$restrictedFunctions = array ('copy_skin_folder','addTarFolder'
);
if (in_array( $_REQUEST['action'], $restrictedFunctions )) {
    $res['success'] = false;
    $res['error'] = $res['message'] = G::LoadTranslation('ID_REQUEST_ACTION_NOT_EXIST');
    print G::json_encode( $res );
    die();
}

$functionName = $_REQUEST['action'];
$functionParams = isset( $_REQUEST['params'] ) ? $_REQUEST['params'] : array ();

$functionName();

function updatePageSize ()
{
    G::LoadClass( 'configuration' );
    $c = new Configurations();
    $arr['pageSize'] = $_REQUEST['size'];
    $arr['dateSave'] = date( 'Y-m-d H:i:s' );
    $config = Array ();
    $config[] = $arr;
    $c->aConfig = $config;
    $c->saveConfig( 'skinsList', 'pageSize', '', $_SESSION['USER_LOGGED'] );
    echo '{success: true}';
}

function skinList ()
{
    G::loadClass( 'system' );

    $skinList = System::getSkingList();
    $wildcard = '';
    if (isset( $_REQUEST['activeskin'] )) {
        $wildcard = '@';
    }

    $classicSkin = '';
    if (defined('PARTNER_FLAG')) {
        $classicSkin = '00000000000000000000000000000001';
    }

    foreach ($skinList['skins'] as $key => $value) {
        if (!isset($value['SKIN_ID']) || $value['SKIN_ID'] != $classicSkin) {
            if ($value['SKIN_FOLDER_ID'] != 'simplified' && $value['SKIN_FOLDER_ID'] != 'uxs' && $value['SKIN_FOLDER_ID'] != 'uxmodern') {
                if ($skinList['currentSkin'] == $value['SKIN_FOLDER_ID']) {
                    $value['SKIN_STATUS'] = $wildcard . G::LoadTranslation( 'ID_ACTIVE' );
                    $value['SKIN_NAME'] = $wildcard . $value['SKIN_NAME'];
                    $value['SKIN_WORKSPACE'] = $wildcard . $value['SKIN_WORKSPACE'];
                    $value['SKIN_DESCRIPTION'] = $wildcard . $value['SKIN_DESCRIPTION'];
                    $value['SKIN_AUTHOR'] = $wildcard . $value['SKIN_AUTHOR'];
                    $value['SKIN_CREATEDATE'] = $wildcard . $value['SKIN_CREATEDATE'];
                    $value['SKIN_MODIFIEDDATE'] = $wildcard . $value['SKIN_MODIFIEDDATE'];
                } else {
                    $value['SKIN_STATUS'] = G::LoadTranslation( 'ID_INACTIVE' );
                }

                $skinListArray['skins'][] = $value;
            }
        }
    }
    $skinListArray['currentSkin'] = $skinList['currentSkin'];
    echo G::json_encode( $skinListArray );
}

function newSkin ($baseSkin = 'classic')
{
    $skinBase = $baseSkin != "" ? strtolower( $baseSkin ) : 'classic';
    if ((isset( $_REQUEST['skinBase'] )) && ($_REQUEST['skinBase'] != "")) {
        $skinBase = strtolower( $_REQUEST['skinBase'] );
    }

    try {
        if (! (isset( $_REQUEST['skinName'] ))) {
            throw (new Exception( G::LoadTranslation( 'ID_SKIN_NAME_REQUIRED' ) ));
        }
        if (! (isset( $_REQUEST['skinFolder'] ))) {
            throw (new Exception( G::LoadTranslation( 'ID_SKIN_FOLDER_REQUIRED' ) ));
        }
        //Should validate skin folder name here
        //if....


        $skinName = $_REQUEST['skinName'];
        $skinFolder = $_REQUEST['skinFolder'];
        $skinDescription = isset( $_REQUEST['skinDescription'] ) ? $_REQUEST['skinDescription'] : '';
        $skinAuthor = isset( $_REQUEST['skinAuthor'] ) ? $_REQUEST['skinAuthor'] : 'ProcessMaker Team';

        if (is_dir( PATH_CUSTOM_SKINS . $skinFolder )) {
            throw (new Exception( G::LoadTranslation( 'ID_SKIN_ALREADY_EXISTS' ) ));
        }
        if (strtolower( $skinFolder ) == 'classic') {
            throw (new Exception( G::LoadTranslation( 'ID_SKIN_ALREADY_EXISTS' ) ));
        }

        //All validations OK then create skin
        switch ($skinBase) {
            //Validate skin base
            case 'uxmodern':
                copy_skin_folder( G::ExpandPath( "skinEngine" ) . 'uxmodern' . PATH_SEP, PATH_CUSTOM_SKINS . $skinFolder, array ("config.xml"
                ) );
                $pathBase = G::ExpandPath( "skinEngine" ) . 'base' . PATH_SEP;
                break;
            case 'classic':
                //Special Copy of this dir + xmlreplace
                copy_skin_folder( G::ExpandPath( "skinEngine" ) . 'base' . PATH_SEP, PATH_CUSTOM_SKINS . $skinFolder, array ("config.xml","baseCss"
                ) );
                $pathBase = G::ExpandPath( "skinEngine" ) . 'base' . PATH_SEP;
                break;
            case 'neoclassic':
                //Special Copy of this dir + xmlreplace
                copy_skin_folder( G::ExpandPath( "skinEngine" ) . 'neoclassic' . PATH_SEP, PATH_CUSTOM_SKINS . $skinFolder, array ("config.xml","baseCss"
                ) );
                $pathBase = G::ExpandPath( "skinEngine" ) . 'neoclassic' . PATH_SEP;
                break;
            default:
                //Commmon copy/paste of a folder + xmlrepalce
                copy_skin_folder( PATH_CUSTOM_SKINS . $skinBase, PATH_CUSTOM_SKINS . $skinFolder, array ("config.xml"
                ) );
                $pathBase = PATH_CUSTOM_SKINS . $skinBase . PATH_SEP;
                break;
        }

        //ReBuild config file
        //TODO: Improve this pre_replace lines
        $configFileOriginal = $pathBase . "config.xml";
        $configFileFinal = PATH_CUSTOM_SKINS . $skinFolder . PATH_SEP . 'config.xml';

        $xmlConfiguration = file_get_contents( $configFileOriginal );

        $workspace = ($_REQUEST['workspace'] == 'global') ? '' : SYS_SYS;

        $xmlConfigurationObj = G::xmlParser($xmlConfiguration);
        $skinInformationArray = $xmlConfigurationObj->result["skinConfiguration"]["__CONTENT__"]["information"]["__CONTENT__"];

        $xmlConfiguration = preg_replace( '/(<id>)(.+?)(<\/id>)/i', '<id>' . G::generateUniqueID() . '</id><!-- $2 -->', $xmlConfiguration );

        if (isset($skinInformationArray["workspace"]["__VALUE__"])) {
            $workspace = ($workspace != "" && !empty($skinInformationArray["workspace"]["__VALUE__"]))? $skinInformationArray["workspace"]["__VALUE__"] . "|" . $workspace : $workspace;

            $xmlConfiguration = preg_replace("/(<workspace>)(.*)(<\/workspace>)/i", "<workspace>" . $workspace . "</workspace><!-- $2 -->", $xmlConfiguration);
            $xmlConfiguration = preg_replace("/(<name>)(.*)(<\/name>)/i", "<name>" . $skinName . "</name><!-- $2 -->", $xmlConfiguration);
        } else {
            $xmlConfiguration = preg_replace("/(<name>)(.*)(<\/name>)/i", "<name>" . $skinName . "</name><!-- $2 -->\n<workspace>" . $workspace . "</workspace>", $xmlConfiguration);
        }

        $xmlConfiguration = preg_replace( "/(<description>)(.+?)(<\/description>)/i", "<description>" . $skinDescription . "</description><!-- $2 -->", $xmlConfiguration );
        $xmlConfiguration = preg_replace( "/(<author>)(.+?)(<\/author>)/i", "<author>" . $skinAuthor . "</author><!-- $2 -->", $xmlConfiguration );
        $xmlConfiguration = preg_replace( "/(<createDate>)(.+?)(<\/createDate>)/i", "<createDate>" . date( "Y-m-d H:i:s" ) . "</createDate><!-- $2 -->", $xmlConfiguration );
        $xmlConfiguration = preg_replace( "/(<modifiedDate>)(.+?)(<\/modifiedDate>)/i", "<modifiedDate>" . date( "Y-m-d H:i:s" ) . "</modifiedDate><!-- $2 -->", $xmlConfiguration );

        file_put_contents( $configFileFinal, $xmlConfiguration );
        $response['success'] = true;
        $response['message'] = G::LoadTranslation( 'ID_SKIN_SUCCESS_CREATE' );
        print_r( G::json_encode( $response ) );
    } catch (Exception $e) {
        $response['success'] = false;
        $response['message'] = $e->getMessage();
        $response['error'] = $e->getMessage();
        print_r( G::json_encode( $response ) );
    }
}

function importSkin ()
{
    try {
        if (! isset( $_FILES['uploadedFile'] )) {
            throw (new Exception( G::LoadTranslation( 'ID_SKIN_FILE_REQUIRED' ) ));
        }
        $uploadedInstances = count( $_FILES['uploadedFile']['name'] );
        $sw_error = false;
        $sw_error_exists = isset( $_FILES['uploadedFile']['error'] );
        $emptyInstances = 0;
        $quequeUpload = array ();

        // upload files & check for errors
        $tmp = $_FILES['uploadedFile']['tmp_name'];
        $items = stripslashes( $_FILES['uploadedFile']['name'] );
        if ($sw_error_exists) {
            $up_err = $_FILES['uploadedFile']['error'];
        } else {
            $up_err = (file_exists( $tmp ) ? 0 : 4);
        }

        if ($items == "" || $up_err == 4) {
            throw (new Exception( G::LoadTranslation( 'ID_SKIN_FILE_REQUIRED' ) ));
        }
        if ($up_err == 1 || $up_err == 2) {
            throw (new Exception( G::LoadTranslation( 'ID_FILE_TOO_BIG' ) ));
            //$errors[$i]='miscfilesize';
        }
        if ($up_err == 3) {
            throw (new Exception( G::LoadTranslation( 'ID_ERROR_UPLOAD_FILE_CONTACT_ADMINISTRATOR' ) ));
            //$errors[$i]='miscfilepart';
        }
        if (! @is_uploaded_file( $tmp )) {
            throw (new Exception( G::LoadTranslation( 'ID_ERROR_UPLOAD_FILE_CONTACT_ADMINISTRATOR' ) ));
            //$errors[$i]='uploadfile';
        }
        $fileInfo = pathinfo( $items );
        $validType = array ('tar','gz'
        );

        if (! in_array( $fileInfo['extension'], $validType )) {
            throw (new Exception( G::LoadTranslation( 'ID_FILE_UPLOAD_INCORRECT_EXTENSION' ) ));
            //$errors[$i]='wrongtype';
        }

        $filename = $items;
        $tempPath = PATH_CUSTOM_SKINS . '.tmp' . PATH_SEP;
        G::verifyPath( $tempPath, true );
        $tempName = $tmp;
        G::uploadFile( $tempName, $tempPath, $filename );
        G::LoadThirdParty( 'pear/Archive', 'Tar' );
        $tar = new Archive_Tar( $tempPath . $filename );
        $aFiles = $tar->listContent();
        $swConfigFile = false;

        foreach ($aFiles as $key => $val) {
            if (basename( $val['filename'] ) == 'config.xml') {
                $skinName = dirname( $val['filename'] );
                $skinArray = explode( "/", $skinName );
                if (count( $skinArray ) == 1) {
                    $swConfigFile = true;
                }
            }
        }

        if (! $swConfigFile) {
            @unlink( PATH_CUSTOM_SKINS . '.tmp' . PATH_SEP . $filename );
            throw (new Exception( G::LoadTranslation( 'ID_SKIN_CONFIGURATION_MISSING' ) ));
        }

        if (is_dir( PATH_CUSTOM_SKINS . $skinName )) {
            if ((isset( $_REQUEST['overwrite_files'] )) && ($_REQUEST['overwrite_files'] == 'on')) {
                G::rm_dir( PATH_CUSTOM_SKINS . $skinName, false );
            } else {
                throw (new Exception( G::LoadTranslation( 'ID_SKIN_ALREADY_EXISTS' ) ));
            }
        }
        $res = $tar->extract( PATH_CUSTOM_SKINS );
        if (! $res) {
            throw (new Exception( G::LoadTranslation( 'ID_SKIN_ERROR_EXTRACTING' ) ));
        }

        $configFileOriginal = PATH_CUSTOM_SKINS . $skinName . PATH_SEP . 'config.xml';
        $configFileFinal = PATH_CUSTOM_SKINS . $skinName . PATH_SEP . 'config.xml';
        $xmlConfiguration = file_get_contents( $configFileOriginal );

        $workspace = ($_REQUEST['workspace'] == 'global') ? '' : SYS_SYS;

        $xmlConfigurationObj = G::xmlParser($xmlConfiguration);
        $skinInformationArray = $xmlConfigurationObj->result["skinConfiguration"]["__CONTENT__"]["information"]["__CONTENT__"];

        if (isset($skinInformationArray["workspace"]["__VALUE__"])) {
            $workspace = ($workspace != "" && !empty($skinInformationArray["workspace"]["__VALUE__"]))? $skinInformationArray["workspace"]["__VALUE__"] . "|" . $workspace : $workspace;

            $xmlConfiguration = preg_replace("/(<workspace>)(.*)(<\/workspace>)/i", "<workspace>" . $workspace . "</workspace><!-- $2 -->", $xmlConfiguration);
        } else {
            $xmlConfiguration = preg_replace("/(<name>)(.*)(<\/name>)/i", "<name>" . $skinName . "</name><!-- $2 -->\n<workspace>" . $workspace . "</workspace>", $xmlConfiguration);
        }

        file_put_contents( $configFileFinal, $xmlConfiguration );

        //Delete Temporal
        @unlink( PATH_CUSTOM_SKINS . '.tmp' . PATH_SEP . $filename );

        $response['success'] = true;
        $response['message'] = G::LoadTranslation( 'ID_SKIN_SUCCESSFUL_IMPORTED' );
        print_r( G::json_encode( $response ) );
    } catch (Exception $e) {
        $response['success'] = false;
        $response['message'] = $e->getMessage();
        $response['error'] = $e->getMessage();
        print_r( G::json_encode( $response ) );
    }
}

function exportSkin ($skinToExport = "")
{
    try {
        if (! isset( $_REQUEST['SKIN_FOLDER_ID'] )) {
            throw (new Exception( G::LoadTranslation( 'ID_SKIN_NAME_REQUIRED' ) ));
        }

        $skinName = $_REQUEST['SKIN_FOLDER_ID'];

        $skinFolderBase = PATH_CUSTOM_SKINS . $skinName;
        $skinFolder = $skinFolderBase . PATH_SEP;
        $skinTar = PATH_CUSTOM_SKINS . $skinName . '.tar';
        if (! is_dir( $skinFolder )) {
            throw (new Exception( G::LoadTranslation( 'ID_SKIN_DOESNT_EXIST' ) ));
        }
        if (! file_exists( $skinFolder . "config.xml" )) {
            throw (new Exception( G::LoadTranslation( 'ID_SKIN_CONFIGURATION_MISSING' ) ));
        }

        if (file_exists( $skinTar )) {
            //try to delete
            if (! unlink( $skinTar )) {
                throw (new Exception( G::LoadTranslation( 'ID_SKIN_FOLDER_PERMISSIONS' ) ));
            }
        }

        //Try to generate tar file
        G::LoadThirdParty( 'pear/Archive', 'Tar' );
        $tar = new Archive_Tar( $skinTar );
        $tar->_compress = false;

        addTarFolder( $tar, $skinFolder, PATH_CUSTOM_SKINS );

        $response['success'] = true;
        $response['message'] = $skinTar;

        print_r( G::json_encode( $response ) );
    } catch (Exception $e) {
        $response['success'] = false;
        $response['message'] = $e->getMessage();
        print_r( G::json_encode( $response ) );
    }
}

function deleteSkin ()
{
    try {
        if (! (isset( $_REQUEST['SKIN_FOLDER_ID'] ))) {
            throw (new Exception( G::LoadTranslation( 'ID_SKIN_FOLDER_REQUIRED' ) ));
        }
        if (($_REQUEST['SKIN_FOLDER_ID']) == "classic") {
            throw (new Exception( G::LoadTranslation( 'ID_SKIN_FOLDER_NOT_DELETEABLE' ) ));
        }
        $folderId = $_REQUEST['SKIN_FOLDER_ID'];
        if (! is_dir( PATH_CUSTOM_SKINS . $folderId )) {
            throw (new Exception( G::LoadTranslation( 'ID_SKIN_NOT_EXISTS' ) ));
        }
        //Delete
        G::rm_dir( PATH_CUSTOM_SKINS . $folderId );
        $response['success'] = true;
        $response['message'] = "$folderId deleted";
    } catch (Exception $e) {
        $response['success'] = false;
        $response['error'] = $response['message'] = $e->getMessage();
        print_r( G::json_encode( $response ) );
    }
}

function streamSkin ()
{
    $skinTar = $_REQUEST['file'];
    $bDownload = true;
    G::streamFile( $skinTar, $bDownload, basename( $skinTar ) );
    @unlink( $fileTar );
}

function addTarFolder ($tar, $pathBase, $pluginHome)
{
    $aux = explode( PATH_SEP, $pathBase );
    if ($aux[count( $aux ) - 2] == '.svn') {
        return;
    }

    if ($handle = opendir( $pathBase )) {
        while (false !== ($file = readdir( $handle ))) {
            if (is_file( $pathBase . $file )) {
                //print "file $file \n";
                $tar->addModify( $pathBase . $file, '', $pluginHome );
            }
            if (is_dir( $pathBase . $file ) && $file != '..' && $file != '.') {
                //print "dir $pathBase$file \n";
                addTarFolder( $tar, $pathBase . $file . PATH_SEP, $pluginHome );
            }
        }
        closedir( $handle );
    }
}

function copy_skin_folder ($path, $dest, $exclude = array())
{
    $defaultExcluded = array (".",".."
    );
    $excludedItems = array_merge( $defaultExcluded, $exclude );
    if (is_dir( $path )) {
        @mkdir( $dest );
        $objects = scandir( $path );
        if (sizeof( $objects ) > 0) {
            foreach ($objects as $file) {
                if (in_array( $file, $excludedItems )) {
                    continue;
                }
                // go on
                if (is_dir( $path . PATH_SEP . $file )) {
                    copy_skin_folder( $path . PATH_SEP . $file, $dest . PATH_SEP . $file, $exclude );
                } else {
                    copy( $path . PATH_SEP . $file, $dest . PATH_SEP . $file );
                }
            }
        }
        return true;
    } elseif (is_file( $path )) {
        return copy( $path, $dest );
    } else {
        return false;
    }
}

