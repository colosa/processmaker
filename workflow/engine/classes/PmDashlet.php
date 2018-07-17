<?php

use ProcessMaker\Plugins\PluginRegistry;


class PmDashlet extends DashletInstance implements DashletInterface
{

    // Own properties
    private $dashletInstance;
    private $dashletObject;

    // Interface functions

    /**
     * verify if file exists or no, return the path of file.
     *
     * @param $name file
     * @param string $type CORE or PLUGIN
     * @return stdClass information of file
     */
    private function verifyExistsFile($name, $type = 'CORE')
    {
        $response = new stdClass();
        $response->exists = false;
        $response->path = '';
        $response->plugin = '';
        $response->className = $name;

        //Compatibility with old files
        $paths = [
            'classes' . PATH_SEP . $name . '.php',
            'classes' . PATH_SEP . ucfirst($name) . '.php',
            'classes' . PATH_SEP . 'class.' . $name . '.php'
        ];

        switch ($type) {
            case 'CORE':
                foreach ($paths as $key => $path) {
                    if (file_exists(PATH_CORE . $path)) {
                        $response->exists = true;
                        $response->path = PATH_CORE . $path;
                        $response->className = $key == 1 ? ucfirst($name) : $name;
                        break;
                    }
                }
                break;
            case 'PLUGIN':
                foreach (self::getIncludePath() as $plugin => $pathPlugin) {
                    foreach ($paths as $key => $path) {
                        if (file_exists($pathPlugin . $path)) {
                            $response->exists = true;
                            $response->path = $pathPlugin . $path;
                            $response->className = $key == 1 ? ucfirst($name) : $name;
                            $response->plugin = $plugin;
                            break 2;
                        }
                    }
                }
                break;
            default:
                break;
        }

        return $response;
    }


    public static function getAdditionalFields($className)
    {
        try {
            if (!G::classExists($className)) {
                $file = self::verifyExistsFile($className, 'PLUGIN');
                if ($file->exists) {
                    $className = $file->className;
                    require_once $file->path;
                }
            } else {
                $className = G::nameClass($className);
            }
            eval("\$additionalFields = $className::getAdditionalFields(\$className);");
            return $additionalFields;
        } catch (Exception $error) {
            throw $error;
        }
    }

    public function setup($dasInsUid)
    {
        try {
            $this->dashletInstance = $this->loadDashletInstance($dasInsUid);

            if (!isset($this->dashletInstance['DAS_CLASS'])) {
                throw new Exception(G::LoadTranslation('ID_ERROR_OBJECT_NOT_EXISTS') . ' - Probably the plugin related is disabled');
            }
            $className = $this->dashletInstance['DAS_CLASS'];

            if (!G::classExists($className)) {
                $file = self::verifyExistsFile($className, 'PLUGIN');
                if ($file->exists) {
                    $className = $file->className;
                    require_once $file->path;
                }
            }
            $this->dashletObject = new $className();
            $this->dashletObject->setup($this->dashletInstance);
        } catch (Exception $error) {
            throw $error;
        }
    }

    public function render($width = 300)
    {
        try {
            if (is_null($this->dashletObject)) {
                throw new Exception('Please call to the function "setup" before call the function "render".');
            }
            $this->dashletObject->render($width);
        } catch (Exception $error) {
            throw $error;
        }
    }

    // Getter and Setters


    public function getDashletInstance()
    {
        return $this->dashletInstance;
    }

    public function getDashletObject()
    {
        return $this->dashletObject;
    }

    // Own functions


    public function getDashletsInstances($start = null, $limit = null)
    {
        try {
            $dashletsInstances = array();
            $criteria = new Criteria('workflow');
            $criteria->addSelectColumn('*');
            $criteria->addJoin(DashletInstancePeer::DAS_UID, DashletPeer::DAS_UID, Criteria::INNER_JOIN);
            if (!is_null($start)) {
                $criteria->setOffset($start);
            }
            if (!is_null($limit)) {
                $criteria->setLimit($limit);
            }
            $dataset = DashletInstancePeer::doSelectRS($criteria);
            $dataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
            $dataset->next();
            while ($row = $dataset->getRow()) {
                $arrayField = unserialize($row["DAS_INS_ADDITIONAL_PROPERTIES"]);

                if (strstr($row['DAS_TITLE'], '*')) {
                    $row['DAS_TITLE'] = G::LoadTranslationPlugin('advancedDashboards', str_replace("*", "", $row['DAS_TITLE']));
                }
                $row['DAS_INS_STATUS_LABEL'] = ($row['DAS_INS_STATUS'] == '1' ? G::LoadTranslation('ID_ACTIVE') : G::LoadTranslation('ID_INACTIVE'));
                $row['DAS_INS_TITLE'] = (isset($arrayField['DAS_INS_TITLE']) && !empty($arrayField['DAS_INS_TITLE'])) ? $arrayField['DAS_INS_TITLE'] : '';
                if (!G::classExists($row['DAS_CLASS'])) {
                    $file = self::verifyExistsFile($row['DAS_CLASS'], 'PLUGIN');
                    if ($file->exists) {
                        $row['DAS_CLASS'] = $file->className;
                        require_once $file->path;
                    } else {
                        $dataset->next();
                        continue;
                    }
                }
                eval("\$row['DAS_VERSION'] = defined('" . $row['DAS_CLASS'] . "::version') ? " . $row['DAS_CLASS'] . "::version : \$row['DAS_VERSION'];");

                switch ($row['DAS_INS_OWNER_TYPE']) {
                    case 'EVERYBODY':
                        $row['DAS_INS_OWNER_TITLE'] = G::LoadTranslation('ID_ALL_USERS');
                        break;
                    case 'USER':
                        $userInstance = new Users();
                        try {
                            $user = $userInstance->load($row['DAS_INS_OWNER_UID']);
                            $row['DAS_INS_OWNER_TITLE'] = $user['USR_FIRSTNAME'] . ' ' . $user['USR_LASTNAME'];
                        } catch (Exception $error) {
                            $this->remove($row['DAS_INS_UID']);
                            $row['DAS_INS_UID'] = '';
                        }
                        break;
                    case 'DEPARTMENT':
                        $departmentInstance = new Department();
                        try {
                            $department = $departmentInstance->load($row['DAS_INS_OWNER_UID']);
                            $row['DAS_INS_OWNER_TITLE'] = $department['DEP_TITLE'];
                        } catch (Exception $error) {
                            $this->remove($row['DAS_INS_UID']);
                            $row['DAS_INS_UID'] = '';
                        }
                        break;
                    case 'GROUP':
                        $groupInstance = new Groupwf();
                        try {
                            $group = $groupInstance->load($row['DAS_INS_OWNER_UID']);
                            $row['DAS_INS_OWNER_TITLE'] = $group['GRP_TITLE'];
                        } catch (Exception $error) {
                            $this->remove($row['DAS_INS_UID']);
                            $row['DAS_INS_UID'] = '';
                        }
                        break;
                    default:
                        $row['DAS_INS_OWNER_TITLE'] = $row['DAS_INS_OWNER_TYPE'];
                        break;
                }
                if ($row['DAS_INS_UID'] != '') {
                    $dashletsInstances[] = $row;
                }
                $dataset->next();
            }

            return $dashletsInstances;
        } catch (Exception $error) {
            throw $error;
        }
    }

    public function loadDashletInstance($dasInsUid)
    {
        try {
            $dashletInstance = $this->load($dasInsUid);
            //Load data from the serialized field
            $dashlet = new Dashlet();
            $dashletFields = $dashlet->load($dashletInstance['DAS_UID']);
            if (is_null($dashletFields)) {
                $dashletFields = array();
            }
            return array_merge($dashletFields, $dashletInstance);
        } catch (Exception $error) {
            throw $error;
        }
    }

    public function saveDashletInstance($data)
    {
        try {
            $this->createOrUpdate($data);
        } catch (Exception $error) {
            throw $error;
        }
    }

    public function deleteDashletInstance($dasInsUid)
    {
        try {
            $this->remove($dasInsUid);
        } catch (Exception $error) {
            throw $error;
        }
    }

    public function getDashletsInstancesForUser($userUid)
    {
        try {
            $dashletsInstances = array();
            // Check for "public" dashlets
            $criteria = new Criteria('workflow');
            $criteria->addSelectColumn(DashletInstancePeer::DAS_INS_UID);
            $criteria->addSelectColumn(DashletInstancePeer::DAS_INS_ADDITIONAL_PROPERTIES);
            $criteria->addSelectColumn(DashletPeer::DAS_CLASS);
            $criteria->addSelectColumn(DashletPeer::DAS_TITLE);
            $criteria->add(DashletInstancePeer::DAS_INS_STATUS, '1');
            $criteria->addJoin(DashletInstancePeer::DAS_UID, DashletPeer::DAS_UID, Criteria::INNER_JOIN);
            $criteria->add(DashletInstancePeer::DAS_INS_OWNER_TYPE, 'EVERYBODY');
            $dataset = DashletInstancePeer::doSelectRS($criteria);
            $dataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
            $dataset->next();
            while ($row = $dataset->getRow()) {
                if (!isset($dashletsInstances[$row['DAS_INS_UID']])) {
                    $arrayField = unserialize($row["DAS_INS_ADDITIONAL_PROPERTIES"]);

                    if (self::verifyPluginDashlet($row['DAS_CLASS'])) {
                        $row['DAS_XTEMPLATE'] = $this->getXTemplate($row['DAS_CLASS']);
                        $row["DAS_TITLE"] = (isset($arrayField["DAS_INS_TITLE"]) && !empty($arrayField["DAS_INS_TITLE"])) ? $arrayField["DAS_INS_TITLE"] : $row["DAS_TITLE"];
                        $row["DAS_TITLE"] = $row["DAS_TITLE"] . ((isset($arrayField["DAS_INS_SUBTITLE"]) && !empty($arrayField["DAS_INS_SUBTITLE"])) ? str_replace("@@USR_USERNAME", $_SESSION["USR_USERNAME"], $arrayField["DAS_INS_SUBTITLE"]) : null);

                        $dashletsInstances[$row['DAS_INS_UID']] = $row;
                    }
                }
                $dataset->next();
            }
            // Check for the direct assignments
            $usersInstance = new Users();
            $criteria = new Criteria('workflow');
            $criteria->addSelectColumn(DashletInstancePeer::DAS_INS_UID);
            $criteria->addSelectColumn(DashletInstancePeer::DAS_INS_ADDITIONAL_PROPERTIES);
            $criteria->addSelectColumn(DashletPeer::DAS_CLASS);
            $criteria->addSelectColumn(DashletPeer::DAS_TITLE);
            $criteria->addJoin(DashletInstancePeer::DAS_UID, DashletPeer::DAS_UID, Criteria::INNER_JOIN);
            $criteria->add(DashletInstancePeer::DAS_INS_STATUS, '1');
            $criteria->add(DashletInstancePeer::DAS_INS_OWNER_TYPE, 'USER');
            $criteria->add(DashletInstancePeer::DAS_INS_OWNER_UID, $userUid);
            $dataset = DashletInstancePeer::doSelectRS($criteria);
            $dataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
            $dataset->next();
            while ($row = $dataset->getRow()) {
                if (!isset($dashletsInstances[$row['DAS_INS_UID']])) {
                    $arrayField = unserialize($row["DAS_INS_ADDITIONAL_PROPERTIES"]);

                    if (self::verifyPluginDashlet($row['DAS_CLASS'])) {
                        $row['DAS_XTEMPLATE'] = $this->getXTemplate($row['DAS_CLASS']);
                        $row["DAS_TITLE"] = (isset($arrayField["DAS_INS_TITLE"]) && !empty($arrayField["DAS_INS_TITLE"])) ? $arrayField["DAS_INS_TITLE"] : $row["DAS_TITLE"];
                        $row["DAS_TITLE"] = $row["DAS_TITLE"] . ((isset($arrayField["DAS_INS_SUBTITLE"]) && !empty($arrayField["DAS_INS_SUBTITLE"])) ? str_replace("@@USR_USERNAME", $_SESSION["USR_USERNAME"], $arrayField["DAS_INS_SUBTITLE"]) : null);

                        $dashletsInstances[$row['DAS_INS_UID']] = $row;
                    }
                }
                $dataset->next();
            }
            // Check for department assigments
            $departmentInstance = new Department();
            $departments = $departmentInstance->getDepartmentsForUser($userUid);
            foreach ($departments as $depUid => $department) {
                $criteria = new Criteria('workflow');
                $criteria->addSelectColumn(DashletInstancePeer::DAS_INS_UID);
                $criteria->addSelectColumn(DashletInstancePeer::DAS_INS_ADDITIONAL_PROPERTIES);
                $criteria->addSelectColumn(DashletPeer::DAS_CLASS);
                $criteria->addSelectColumn(DashletPeer::DAS_TITLE);
                $criteria->addJoin(DashletInstancePeer::DAS_UID, DashletPeer::DAS_UID, Criteria::INNER_JOIN);
                $criteria->add(DashletInstancePeer::DAS_INS_STATUS, '1');
                $criteria->add(DashletInstancePeer::DAS_INS_OWNER_TYPE, 'DEPARTMENT');
                $criteria->add(DashletInstancePeer::DAS_INS_OWNER_UID, $depUid);
                $dataset = DashletInstancePeer::doSelectRS($criteria);
                $dataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
                $dataset->next();
                while ($row = $dataset->getRow()) {
                    if (!isset($dashletsInstances[$row['DAS_INS_UID']])) {
                        $arrayField = unserialize($row["DAS_INS_ADDITIONAL_PROPERTIES"]);

                        if (self::verifyPluginDashlet($row["DAS_CLASS"])) {
                            $row['DAS_XTEMPLATE'] = $this->getXTemplate($row['DAS_CLASS']);
                            $row["DAS_TITLE"] = (isset($arrayField["DAS_INS_TITLE"]) && !empty($arrayField["DAS_INS_TITLE"])) ? $arrayField["DAS_INS_TITLE"] : $row["DAS_TITLE"];
                            $row["DAS_TITLE"] = $row["DAS_TITLE"] . ((isset($arrayField["DAS_INS_SUBTITLE"]) && !empty($arrayField["DAS_INS_SUBTITLE"])) ? str_replace("@@USR_USERNAME", $_SESSION["USR_USERNAME"], $arrayField["DAS_INS_SUBTITLE"]) : null);

                            $dashletsInstances[$row['DAS_INS_UID']] = $row;
                        }
                    }
                    $dataset->next();
                }
            }
            // Check for group assignments
            $groupsInstance = new Groups();
            $groups = $groupsInstance->getGroupsForUser($userUid);
            foreach ($groups as $grpUid => $group) {
                $criteria = new Criteria('workflow');
                $criteria->addSelectColumn(DashletInstancePeer::DAS_INS_UID);
                $criteria->addSelectColumn(DashletInstancePeer::DAS_INS_ADDITIONAL_PROPERTIES);
                $criteria->addSelectColumn(DashletPeer::DAS_CLASS);
                $criteria->addSelectColumn(DashletPeer::DAS_TITLE);
                $criteria->addJoin(DashletInstancePeer::DAS_UID, DashletPeer::DAS_UID, Criteria::INNER_JOIN);
                $criteria->add(DashletInstancePeer::DAS_INS_STATUS, '1');
                $criteria->add(DashletInstancePeer::DAS_INS_OWNER_TYPE, 'GROUP');
                $criteria->add(DashletInstancePeer::DAS_INS_OWNER_UID, $grpUid);
                $dataset = DashletInstancePeer::doSelectRS($criteria);
                $dataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
                $dataset->next();
                while ($row = $dataset->getRow()) {
                    if (!isset($dashletsInstances[$row['DAS_INS_UID']])) {
                        $arrayField = unserialize($row["DAS_INS_ADDITIONAL_PROPERTIES"]);

                        if (self::verifyPluginDashlet($row["DAS_CLASS"])) {
                            $row['DAS_XTEMPLATE'] = $this->getXTemplate($row['DAS_CLASS']);
                            $row["DAS_TITLE"] = (isset($arrayField["DAS_INS_TITLE"]) && !empty($arrayField["DAS_INS_TITLE"])) ? $arrayField["DAS_INS_TITLE"] : $row["DAS_TITLE"];
                            $row["DAS_TITLE"] = $row["DAS_TITLE"] . ((isset($arrayField["DAS_INS_SUBTITLE"]) && !empty($arrayField["DAS_INS_SUBTITLE"])) ? str_replace("@@USR_USERNAME", $_SESSION["USR_USERNAME"], $arrayField["DAS_INS_SUBTITLE"]) : null);

                            $dashletsInstances[$row['DAS_INS_UID']] = $row;
                        }
                    }
                    $dataset->next();
                }
            }
            foreach ($dashletsInstances as $key => $field) {
                $dashletsInstances[$key]['DAS_TITLE'] = htmlentities($field['DAS_TITLE'], ENT_QUOTES, 'UTF-8') . '</span><span style="float:right; font: bold;" id="' . $field['DAS_INS_UID'] . '">';
            }
            // Check for role assigments
            // ToDo: Next release
            // Check for permission assigments
            // ToDo: Next release
            return array_values($dashletsInstances);
        } catch (Exception $error) {
            throw $error;
        }
    }

    /**
     * Get template for class
     *
     * @param $className string name of file
     * @return mixed string template dashboard
     * @throws Exception
     */
    public static function getXTemplate($className)
    {
        try {
            if (!G::classExists($className)) {
                $file = self::verifyExistsFile($className, 'PLUGIN');
                if ($file->exists) {
                    $className = $file->className;
                    require_once $file->path;
                }
            } else {
                $className = G::nameClass($className);
            }

            eval("\$additionalFields = $className::getXTemplate(\$className);");
            return $additionalFields;
        } catch (Exception $error) {
            throw $error;
        }
    }

    public static function verifyPluginDashlet($className)
    {
        $fileExists = false;
        if (G::classExists($className)) {
            $fileExists = true;
        } else {
            // 2-- if name class is in plugin
            $file = self::verifyExistsFile($className, 'PLUGIN');
            if ($file->exists && !empty($file->plugin)) {
                //---- verify if the plugin is active
                if ($handle = opendir(PATH_PLUGINS)) {
                    $oPluginRegistry = PluginRegistry::loadSingleton();
                    while (false !== ($filePlugin = readdir($handle))) {
                        if (strpos($filePlugin, '.php', 1) && is_file(PATH_PLUGINS . $filePlugin)) {
                            include_once(PATH_PLUGINS . $filePlugin);
                            $pluginDetail = $oPluginRegistry->getPluginDetails($filePlugin);
                            if ($pluginDetail->getNamespace() == $file->plugin) {
                                $fileExists = $pluginDetail->isEnabled();
                            }
                        }
                    }
                    closedir($handle);
                }
            }
        }
        return true;
    }

    private static function getIncludePath()
    {
        $oPluginRegistry = PluginRegistry::loadSingleton();
        $pluginsDashlets = $oPluginRegistry->getDashlets();
        $paths = [];
        foreach ($pluginsDashlets as $pluginDashlet) {
            $paths[$pluginDashlet] = PATH_PLUGINS . $pluginDashlet . PATH_SEP;
        }
        return $paths;
    }
}