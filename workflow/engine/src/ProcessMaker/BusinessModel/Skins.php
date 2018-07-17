<?php
namespace ProcessMaker\BusinessModel;

use ProcessMaker\Core\System;
use Exception;
use G;

/**
 * Skins business model
 */
class Skins
{
    /**
     * Get a list of skins.
     *
     * @category HOR-3208,PROD-181
     * @return array
     */
    public function getSkins()
    {
        $list = System::getSkingList();
        return $list['skins'];
    }

    /**
     * Create a new skin.
     *
     * @param string $skinName
     * @param string $skinFolder
     * @param string $skinDescription
     * @param string $skinAuthor
     * @param string $skinWorkspace
     * @param string $skinBase
     * @return array
     * @throws Exception
     */
    public function createSkin(
        $skinName,
        $skinFolder,
        $skinDescription = '',
        $skinAuthor = 'ProcessMaker Team',
        $skinWorkspace = 'global',
        $skinBase = 'neoclassic'
    ) {
        try {
            if (!(isset($skinName))) {
                throw (new Exception(G::LoadTranslation('ID_SKIN_NAME_REQUIRED')));
            }
            if (!(isset($skinFolder))) {
                throw (new Exception(G::LoadTranslation('ID_SKIN_FOLDER_REQUIRED')));
            }

            if (is_dir(PATH_CUSTOM_SKINS.$skinFolder)) {
                throw (new Exception(G::LoadTranslation('ID_SKIN_ALREADY_EXISTS')));
            }
            if (strtolower($skinFolder) == 'classic') {
                throw (new Exception(G::LoadTranslation('ID_SKIN_ALREADY_EXISTS')));
            }

            //All validations OK then create skin
            switch ($skinBase) {
                //Validate skin base
                case 'uxmodern':
                    $this->copySkinFolder(G::ExpandPath("skinEngine").'uxmodern'.PATH_SEP,
                                                          PATH_CUSTOM_SKINS.$skinFolder,
                                                          array("config.xml"
                    ));
                    $pathBase = G::ExpandPath("skinEngine").'base'.PATH_SEP;
                    break;
                case 'classic':
                    //Special Copy of this dir + xmlreplace
                    $this->copySkinFolder(G::ExpandPath("skinEngine").'base'.PATH_SEP,
                                                          PATH_CUSTOM_SKINS.$skinFolder,
                                                          array("config.xml", "baseCss"
                    ));
                    $pathBase = G::ExpandPath("skinEngine").'base'.PATH_SEP;
                    break;
                case 'neoclassic':
                    //Special Copy of this dir + xmlreplace
                    $this->copySkinFolder(G::ExpandPath("skinEngine").'neoclassic'.PATH_SEP,
                                                          PATH_CUSTOM_SKINS.$skinFolder,
                                                          array("config.xml", "baseCss"
                    ));
                    $pathBase = G::ExpandPath("skinEngine").'neoclassic'.PATH_SEP;
                    break;
                default:
                    //Commmon copy/paste of a folder + xmlrepalce
                    $this->copySkinFolder(PATH_CUSTOM_SKINS.$skinBase,
                                            PATH_CUSTOM_SKINS.$skinFolder,
                                            array("config.xml"
                    ));
                    $pathBase = PATH_CUSTOM_SKINS.$skinBase.PATH_SEP;
                    break;
            }

            //@todo Improve this pre_replace lines
            $configFileOriginal = $pathBase."config.xml";
            $configFileFinal = PATH_CUSTOM_SKINS.$skinFolder.PATH_SEP.'config.xml';

            $xmlConfiguration = file_get_contents($configFileOriginal);

            $workspace = ($skinWorkspace == 'global') ? '' : SYS_SYS;

            $xmlConfigurationObj = G::xmlParser($xmlConfiguration);
            $skinInformationArray = $xmlConfigurationObj->result["skinConfiguration"]["__CONTENT__"]["information"]["__CONTENT__"];

            $xmlConfiguration = preg_replace('/(<id>)(.+?)(<\/id>)/i',
                                             '<id>'.G::generateUniqueID().'</id><!-- $2 -->',
                                             $xmlConfiguration);

            if (isset($skinInformationArray["workspace"]["__VALUE__"])) {
                $workspace = ($workspace != "" && !empty($skinInformationArray["workspace"]["__VALUE__"]))
                        ? $skinInformationArray["workspace"]["__VALUE__"]."|".$workspace
                        : $workspace;

                $xmlConfiguration = preg_replace("/(<workspace>)(.*)(<\/workspace>)/i",
                                                 "<workspace>".$workspace."</workspace><!-- $2 -->",
                                                 $xmlConfiguration);
                $xmlConfiguration = preg_replace("/(<name>)(.*)(<\/name>)/i",
                                                 "<name>".$skinName."</name><!-- $2 -->",
                                                 $xmlConfiguration);
            } else {
                $xmlConfiguration = preg_replace("/(<name>)(.*)(<\/name>)/i",
                                                 "<name>".$skinName."</name><!-- $2 -->\n<workspace>".$workspace."</workspace>",
                                                 $xmlConfiguration);
            }

            $xmlConfiguration = preg_replace("/(<description>)(.+?)(<\/description>)/i",
                                             "<description>".$skinDescription."</description><!-- $2 -->",
                                             $xmlConfiguration);
            $xmlConfiguration = preg_replace("/(<author>)(.+?)(<\/author>)/i",
                                             "<author>".$skinAuthor."</author><!-- $2 -->",
                                             $xmlConfiguration);
            $xmlConfiguration = preg_replace("/(<createDate>)(.+?)(<\/createDate>)/i",
                                             "<createDate>".date("Y-m-d H:i:s")."</createDate><!-- $2 -->",
                                                                 $xmlConfiguration);
            $xmlConfiguration = preg_replace("/(<modifiedDate>)(.+?)(<\/modifiedDate>)/i",
                                             "<modifiedDate>".date("Y-m-d H:i:s")."</modifiedDate><!-- $2 -->",
                                                                   $xmlConfiguration);

            file_put_contents($configFileFinal, $xmlConfiguration);
            $response['success'] = true;
            $response['message'] = G::LoadTranslation('ID_SKIN_SUCCESS_CREATE');
            G::auditLog("CreateSkin", "Skin Name: ".$skinName);
            return $response;
        } catch (Exception $e) {
            $response['success'] = false;
            $response['message'] = $e->getMessage();
            $response['error'] = $e->getMessage();
            return $response;
        }
    }

    private function copySkinFolder($path, $dest, $exclude = array())
    {
        $defaultExcluded = array(".", "..");
        $excludedItems = array_merge($defaultExcluded, $exclude);
        if (is_dir($path)) {
            mkdir($dest);
            $objects = scandir($path);
            if (sizeof($objects) > 0) {
                foreach ($objects as $file) {
                    if (in_array($file, $excludedItems)) {
                        continue;
                    }
                    if (is_dir($path.PATH_SEP.$file)) {
                        $this->copySkinFolder($path.PATH_SEP.$file,
                                                $dest.PATH_SEP.$file, $exclude);
                    } else {
                        copy($path.PATH_SEP.$file, $dest.PATH_SEP.$file);
                    }
                }
            }
            return true;
        } elseif (is_file($path)) {
            return copy($path, $dest);
        } else {
            return false;
        }
    }
}
