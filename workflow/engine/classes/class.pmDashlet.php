<?php

require_once 'classes/interfaces/dashletInterface.php';
require_once 'classes/model/Dashlet.php';
require_once 'classes/model/DashletInstance.php';

class PMDashlet extends DashletInstance implements DashletInterface
{

    // Own properties
    private $dashletInstance;
    private $dashletObject;

    // Interface functions


    public static function getAdditionalFields ($className)
    {
        try {
            if (! class_exists( $className )) {
                self::setIncludePath();
                require_once 'classes' . PATH_SEP . 'class.' . $className . '.php';
            }
            G::LoadClass( $className );
            eval( "\$additionalFields = $className::getAdditionalFields(\$className);" );
            return $additionalFields;
        } catch (Exception $error) {
            throw $error;
        }
    }

    public function setup ($dasInsUid)
    {
        try {
            $this->dashletInstance = $this->loadDashletInstance( $dasInsUid );
            
            if (! isset( $this->dashletInstance['DAS_CLASS'] )) {
                throw new Exception( G::LoadTranslation( 'ID_ERROR_OBJECT_NOT_EXISTS' ) . ' - Probably the plugin related is disabled' );
            }
            $className = $this->dashletInstance['DAS_CLASS'];
            G::LoadClass( $className );
            if (! class_exists( $className )) {
                self::setIncludePath();
                require_once 'classes' . PATH_SEP . 'class.' . $className . '.php';
            }
            $this->dashletObject = new $className();
            $this->dashletObject->setup( $this->dashletInstance );
        } catch (Exception $error) {
            throw $error;
        }
    }

    public function render ($width = 300)
    {
        try {
            if (is_null( $this->dashletObject )) {
                throw new Exception( 'Please call to the function "setup" before call the function "render".' );
            }
            $this->dashletObject->render( $width );
        } catch (Exception $error) {
            throw $error;
        }
    }

    // Getter and Setters


    public function getDashletInstance ()
    {
        return $this->dashletInstance;
    }

    public function getDashletObject ()
    {
        return $this->dashletObject;
    }

    // Own functions


    public function getDashletsInstances ($start = null, $limit = null)
    {   
        try {
            $dashletsInstances = array ();
            $criteria = new Criteria( 'workflow' );
            $criteria->addSelectColumn( '*' );
            $criteria->addJoin( DashletInstancePeer::DAS_UID, DashletPeer::DAS_UID, Criteria::INNER_JOIN );
            if (! is_null( $start )) {
                $criteria->setOffset( $start );
            }
            if (! is_null( $limit )) {
                $criteria->setLimit( $limit );
            }
            $dataset = DashletInstancePeer::doSelectRS( $criteria );
            $dataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );
            $dataset->next();
            while ($row = $dataset->getRow()) {
                $arrayField = unserialize( $row["DAS_INS_ADDITIONAL_PROPERTIES"] );

                if (strstr($row['DAS_TITLE'], '*')) {
                    $row['DAS_TITLE'] = G::LoadTranslationPlugin('advancedDashboards', str_replace("*","",$row['DAS_TITLE']));    
                }
                $row['DAS_INS_STATUS_LABEL'] = ($row['DAS_INS_STATUS'] == '1' ? G::LoadTranslation( 'ID_ACTIVE' ) : G::LoadTranslation( 'ID_INACTIVE' ));
                $row['DAS_INS_TITLE'] = (isset( $arrayField['DAS_INS_TITLE'] ) && ! empty( $arrayField['DAS_INS_TITLE'] )) ? $arrayField['DAS_INS_TITLE'] : '';
                if (! class_exists( $row['DAS_CLASS'] )) {
                    self::setIncludePath();
                    @include 'classes' . PATH_SEP . 'class.' . $row['DAS_CLASS'] . '.php';
                    if (! class_exists( $row['DAS_CLASS'] )) {
                        $dataset->next();
                        continue;
                    }
                }
                eval( "\$row['DAS_VERSION'] = defined('" . $row['DAS_CLASS'] . "::version') ? " . $row['DAS_CLASS'] . "::version : \$row['DAS_VERSION'];" );

                switch ($row['DAS_INS_OWNER_TYPE']) {
                    case 'EVERYBODY':
                        $row['DAS_INS_OWNER_TITLE'] = G::LoadTranslation( 'ID_ALL_USERS' );
                        break;
                    case 'USER':
                        require_once 'classes/model/Users.php';
                        $userInstance = new Users();
                        try {
                            $user = $userInstance->load( $row['DAS_INS_OWNER_UID'] );
                            $row['DAS_INS_OWNER_TITLE'] = $user['USR_FIRSTNAME'] . ' ' . $user['USR_LASTNAME'];
                        } catch (Exception $error) {
                            $this->remove( $row['DAS_INS_UID'] );
                            $row['DAS_INS_UID'] = '';
                        }
                        break;
                    case 'DEPARTMENT':
                        require_once 'classes/model/Department.php';
                        $departmentInstance = new Department();
                        try {
                            $department = $departmentInstance->load( $row['DAS_INS_OWNER_UID'] );
                            $row['DAS_INS_OWNER_TITLE'] = $department['DEPO_TITLE'];
                        } catch (Exception $error) {
                            $this->remove( $row['DAS_INS_UID'] );
                            $row['DAS_INS_UID'] = '';
                        }
                        break;
                    case 'GROUP':
                        require_once 'classes/model/Groupwf.php';
                        $groupInstance = new Groupwf();
                        try {
                            $group = $groupInstance->load( $row['DAS_INS_OWNER_UID'] );
                            $row['DAS_INS_OWNER_TITLE'] = $group['GRP_TITLE'];
                        } catch (Exception $error) {
                            $this->remove( $row['DAS_INS_UID'] );
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

    public function loadDashletInstance ($dasInsUid)
    {
        try {
            $dashletInstance = $this->load( $dasInsUid );
            //Load data from the serialized field
            $dashlet = new Dashlet();
            $dashletFields = $dashlet->load( $dashletInstance['DAS_UID'] );
            if (is_null( $dashletFields )) {
                $dashletFields = array ();
            }
            return array_merge( $dashletFields, $dashletInstance );
        } catch (Exception $error) {
            throw $error;
        }
    }

    public function saveDashletInstance ($data)
    {
        try {
            $this->createOrUpdate( $data );
        } catch (Exception $error) {
            throw $error;
        }
    }

    public function deleteDashletInstance ($dasInsUid)
    {
        try {
            $this->remove( $dasInsUid );
        } catch (Exception $error) {
            throw $error;
        }
    }

    public function getDashletsInstancesForUser ($userUid)
    {
        try {
            $dashletsInstances = array ();
            // Include required classes
            require_once 'classes/model/Department.php';
            require_once 'classes/model/Users.php';
            // Check for "public" dashlets
            $criteria = new Criteria( 'workflow' );
            $criteria->addSelectColumn( DashletInstancePeer::DAS_INS_UID );
            $criteria->addSelectColumn( DashletInstancePeer::DAS_INS_ADDITIONAL_PROPERTIES );
            $criteria->addSelectColumn( DashletPeer::DAS_CLASS );
            $criteria->addSelectColumn( DashletPeer::DAS_TITLE );
            $criteria->addJoin( DashletInstancePeer::DAS_UID, DashletPeer::DAS_UID, Criteria::INNER_JOIN );
            $criteria->add( DashletInstancePeer::DAS_INS_OWNER_TYPE, 'EVERYBODY' );
            $dataset = DashletInstancePeer::doSelectRS( $criteria );
            $dataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );
            $dataset->next();
            while ($row = $dataset->getRow()) {
                if (! isset( $dashletsInstances[$row['DAS_INS_UID']] )) {
                    $arrayField = unserialize( $row["DAS_INS_ADDITIONAL_PROPERTIES"] );

                    if (self::verifyPluginDashlet($row['DAS_CLASS'])) {
                        $row['DAS_XTEMPLATE'] = $this->getXTemplate( $row['DAS_CLASS'] );
                        $row["DAS_TITLE"] = (isset( $arrayField["DAS_INS_TITLE"] ) && ! empty( $arrayField["DAS_INS_TITLE"] )) ? $arrayField["DAS_INS_TITLE"] : $row["DAS_TITLE"];
                        $row["DAS_TITLE"] = $row["DAS_TITLE"] . ((isset( $arrayField["DAS_INS_SUBTITLE"] ) && ! empty( $arrayField["DAS_INS_SUBTITLE"] )) ? str_replace( "@@USR_USERNAME", $_SESSION["USR_USERNAME"], $arrayField["DAS_INS_SUBTITLE"] ) : null);

                        $dashletsInstances[$row['DAS_INS_UID']] = $row;
                    }
                }
                $dataset->next();
            }
            // Check for the direct assignments
            $usersInstance = new Users();
            $criteria = new Criteria( 'workflow' );
            $criteria->addSelectColumn( DashletInstancePeer::DAS_INS_UID );
            $criteria->addSelectColumn( DashletInstancePeer::DAS_INS_ADDITIONAL_PROPERTIES );
            $criteria->addSelectColumn( DashletPeer::DAS_CLASS );
            $criteria->addSelectColumn( DashletPeer::DAS_TITLE );
            $criteria->addJoin( DashletInstancePeer::DAS_UID, DashletPeer::DAS_UID, Criteria::INNER_JOIN );
            $criteria->add( DashletInstancePeer::DAS_INS_OWNER_TYPE, 'USER' );
            $criteria->add( DashletInstancePeer::DAS_INS_OWNER_UID, $userUid );
            $dataset = DashletInstancePeer::doSelectRS( $criteria );
            $dataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );
            $dataset->next();
            while ($row = $dataset->getRow()) {
                if (! isset( $dashletsInstances[$row['DAS_INS_UID']] )) {
                    $arrayField = unserialize( $row["DAS_INS_ADDITIONAL_PROPERTIES"] );

                    if (self::verifyPluginDashlet($row['DAS_CLASS'])) {
                        $row['DAS_XTEMPLATE'] = $this->getXTemplate( $row['DAS_CLASS'] );
                        $row["DAS_TITLE"] = (isset( $arrayField["DAS_INS_TITLE"] ) && ! empty( $arrayField["DAS_INS_TITLE"] )) ? $arrayField["DAS_INS_TITLE"] : $row["DAS_TITLE"];
                        $row["DAS_TITLE"] = $row["DAS_TITLE"] . ((isset( $arrayField["DAS_INS_SUBTITLE"] ) && ! empty( $arrayField["DAS_INS_SUBTITLE"] )) ? str_replace( "@@USR_USERNAME", $_SESSION["USR_USERNAME"], $arrayField["DAS_INS_SUBTITLE"] ) : null);

                        $dashletsInstances[$row['DAS_INS_UID']] = $row;
                    }
                }
                $dataset->next();
            }
            // Check for department assigments
            $departmentInstance = new Department();
            $departments = $departmentInstance->getDepartmentsForUser( $userUid );
            foreach ($departments as $depUid => $department) {
                $criteria = new Criteria( 'workflow' );
                $criteria->addSelectColumn( DashletInstancePeer::DAS_INS_UID );
                $criteria->addSelectColumn( DashletInstancePeer::DAS_INS_ADDITIONAL_PROPERTIES );
                $criteria->addSelectColumn( DashletPeer::DAS_CLASS );
                $criteria->addSelectColumn( DashletPeer::DAS_TITLE );
                $criteria->addJoin( DashletInstancePeer::DAS_UID, DashletPeer::DAS_UID, Criteria::INNER_JOIN );
                $criteria->add( DashletInstancePeer::DAS_INS_OWNER_TYPE, 'DEPARTMENT' );
                $criteria->add( DashletInstancePeer::DAS_INS_OWNER_UID, $depUid );
                $dataset = DashletInstancePeer::doSelectRS( $criteria );
                $dataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );
                $dataset->next();
                while ($row = $dataset->getRow()) {
                    if (! isset( $dashletsInstances[$row['DAS_INS_UID']] )) {
                        $arrayField = unserialize( $row["DAS_INS_ADDITIONAL_PROPERTIES"] );

                        if (self::verifyPluginDashlet($row["DAS_CLASS"])) {
                            $row['DAS_XTEMPLATE'] = $this->getXTemplate( $row['DAS_CLASS'] );
                            $row["DAS_TITLE"] = (isset( $arrayField["DAS_INS_TITLE"] ) && ! empty( $arrayField["DAS_INS_TITLE"] )) ? $arrayField["DAS_INS_TITLE"] : $row["DAS_TITLE"];
                            $row["DAS_TITLE"] = $row["DAS_TITLE"] . ((isset( $arrayField["DAS_INS_SUBTITLE"] ) && ! empty( $arrayField["DAS_INS_SUBTITLE"] )) ? str_replace( "@@USR_USERNAME", $_SESSION["USR_USERNAME"], $arrayField["DAS_INS_SUBTITLE"] ) : null);

                            $dashletsInstances[$row['DAS_INS_UID']] = $row;
                        }
                    }
                    $dataset->next();
                }
            }
            // Check for group assignments
            G::LoadClass( 'groups' );
            $groupsInstance = new Groups();
            $groups = $groupsInstance->getGroupsForUser( $userUid );
            foreach ($groups as $grpUid => $group) {
                $criteria = new Criteria( 'workflow' );
                $criteria->addSelectColumn( DashletInstancePeer::DAS_INS_UID );
                $criteria->addSelectColumn( DashletInstancePeer::DAS_INS_ADDITIONAL_PROPERTIES );
                $criteria->addSelectColumn( DashletPeer::DAS_CLASS );
                $criteria->addSelectColumn( DashletPeer::DAS_TITLE );
                $criteria->addJoin( DashletInstancePeer::DAS_UID, DashletPeer::DAS_UID, Criteria::INNER_JOIN );
                $criteria->add( DashletInstancePeer::DAS_INS_OWNER_TYPE, 'GROUP' );
                $criteria->add( DashletInstancePeer::DAS_INS_OWNER_UID, $grpUid );
                $dataset = DashletInstancePeer::doSelectRS( $criteria );
                $dataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );
                $dataset->next();
                while ($row = $dataset->getRow()) {
                    if (! isset( $dashletsInstances[$row['DAS_INS_UID']] )) {
                        $arrayField = unserialize( $row["DAS_INS_ADDITIONAL_PROPERTIES"] );

                        if (self::verifyPluginDashlet($row["DAS_CLASS"])) {
                            $row['DAS_XTEMPLATE'] = $this->getXTemplate( $row['DAS_CLASS'] );
                            $row["DAS_TITLE"] = (isset( $arrayField["DAS_INS_TITLE"] ) && ! empty( $arrayField["DAS_INS_TITLE"] )) ? $arrayField["DAS_INS_TITLE"] : $row["DAS_TITLE"];
                            $row["DAS_TITLE"] = $row["DAS_TITLE"] . ((isset( $arrayField["DAS_INS_SUBTITLE"] ) && ! empty( $arrayField["DAS_INS_SUBTITLE"] )) ? str_replace( "@@USR_USERNAME", $_SESSION["USR_USERNAME"], $arrayField["DAS_INS_SUBTITLE"] ) : null);

                            $dashletsInstances[$row['DAS_INS_UID']] = $row;
                        }
                    }
                    $dataset->next();
                }
            }
            // Check for role assigments
            // ToDo: Next release
            // Check for permission assigments
            // ToDo: Next release
            return array_values( $dashletsInstances );
        } catch (Exception $error) {
            throw $error;
        }
    }

    public static function getXTemplate ($className)
    {
        try {
            if (! class_exists( $className )) {
                self::setIncludePath();
                require_once 'classes' . PATH_SEP . 'class.' . $className . '.php';
            }
            G::LoadClass( $className );
            eval( "\$additionalFields = $className::getXTemplate(\$className);" );
            return $additionalFields;
        } catch (Exception $error) {
            throw $error;
        }
    }

    public static function verifyPluginDashlet ($className)
    {
        // 1-- if name class is in core
        $fileExist = PATH_CORE . 'classes' . PATH_SEP . 'class.' . $className . '.php';
        if (file_exists($fileExist)) {
            return true;
        }

        // 2-- if name class is in plugin

        //---- verify the name plugin of the class
        $pluginName = '';
        $oPluginRegistry = &PMPluginRegistry::getSingleton();
        $pluginsDashlets = $oPluginRegistry->getDashlets();

        foreach ($pluginsDashlets as $pluginDashlet) {
            $fileExist = PATH_PLUGINS . $pluginDashlet . PATH_SEP . 'classes' . PATH_SEP . 'class.' . $className . '.php';
            if (file_exists($fileExist)) {
                $pluginName = $pluginDashlet;
                break;
            }
        }

        //---- verify if the plugin is active
        if ($pluginName == '') {
            return false;
        } else {
            if ($handle = opendir( PATH_PLUGINS )) {
                while (false !== ($file = readdir( $handle ))) {
                    if (strpos( $file, '.php', 1 ) && is_file( PATH_PLUGINS . $file )) {
                        include_once (PATH_PLUGINS . $file);
                        $pluginDetail = $oPluginRegistry->getPluginDetails( $file );
                        if ($pluginDetail->sNamespace == $pluginName) {
                            if ($pluginDetail->enabled == '1') {
                                return true;
                            } else {
                                return false;
                            }
                        }
                    }
                }
                closedir( $handle );
            }
            return true;
        }
    }

    private static function setIncludePath ()
    {
        $oPluginRegistry = &PMPluginRegistry::getSingleton();
        $pluginsDashlets = $oPluginRegistry->getDashlets();
        foreach ($pluginsDashlets as $pluginDashlet) {
            set_include_path( get_include_path() . PATH_SEPARATOR . PATH_PLUGINS . $pluginDashlet . PATH_SEP );
        }
    }
}