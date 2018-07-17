<?php
use ProcessMaker\Plugins\PluginRegistry;

class TriggerLibrary
{
    private $_aTriggerClasses_ = array();
    private static $instance = null;

    /**
     * __construct
     *
     * @return void
     */
    public function __construct()
    {
        //Initialize the Library and register the Default
        $this->registerFunctionsFileToLibrary(PATH_CORE . "classes" . PATH_SEP . "class.pmFunctions.php", "ProcessMaker Functions");

        //Register all registered PLugin Functions
        if (class_exists('folderData')) {
            $oPluginRegistry = PluginRegistry::loadSingleton();
            $aAvailablePmFunctions = $oPluginRegistry->getPmFunctions();
            $oPluginRegistry->setupPlugins(); //Get and setup enabled plugins
            foreach ($aAvailablePmFunctions as $key => $class) {
                $filePlugin = PATH_PLUGINS . $class . PATH_SEP . 'classes' . PATH_SEP . 'class.pmFunctions.php';

                if (file_exists($filePlugin) && !is_dir($filePlugin)) {
                    $this->registerFunctionsFileToLibrary($filePlugin, "ProcessMaker Functions");
                }
            }
        }
        //Add External Triggers
        $dir = G::ExpandPath("classes") . 'triggers';
        $filesArray = array();

        if (file_exists($dir)) {
            if ($handle = opendir($dir)) {
                while (false !== ($file = readdir($handle))) {
                    if ($file != "." && $file != ".." && !is_dir($dir . PATH_SEP . $file)) {
                        $this->registerFunctionsFileToLibrary($dir . PATH_SEP . $file, "ProcessMaker External Functions");
                    }
                }
                closedir($handle);
            }
        }
    }

    /**
     * &getSingleton
     *
     * @return self::$instance;
     */
    public function &getSingleton()
    {
        if (self::$instance == null) {
            self::$instance = new TriggerLibrary();
        }
        return self::$instance;
    }

    /**
     * serializeInstance
     *
     * @return serialize ( self::$instance );
     */
    public function serializeInstance()
    {
        return serialize(self::$instance);
    }

    /**
     * unSerializeInstance
     *
     * @param integer $serialized
     * @return void
     */
    public function unSerializeInstance($serialized)
    {
        if (self::$instance == null) {
            self::$instance = new PluginRegistry();
        }

        $instance = unserialize($serialized);
        self::$instance = $instance;
    }

    /**
     * registerFunctionsFileToLibrary
     *
     * @param string $filePath
     * @param string $libraryName
     * @return void
     */
    public function registerFunctionsFileToLibrary($filePath, $libraryName)
    {
        $aLibrary = $this->getMethodsFromLibraryFile($filePath);
        $aLibrary->libraryFile = $filePath;
        $aLibrary->libraryName = $libraryName;
        if (isset($aLibrary->info['className'])) {
            $this->_aTriggerClasses_[$aLibrary->info['className']] = $aLibrary;
        }
    }

    /**
     * getMethodsFromLibraryFile
     *
     * @param string $file
     * @return object(PHPClass) $parsedLibrary
     */
    public function getMethodsFromLibraryFile($file)
    {
        // parse class comments from file
        $parsedLibrary = new PHPClass();
        $success = $parsedLibrary->parseFromFile($file);

        return $parsedLibrary;
    }

    /**
     * getRegisteredClasses
     *
     * @return array ($this->_aTriggerClasses_)
     */
    public function getRegisteredClasses()
    {
        return ($this->_aTriggerClasses_);
    }

    /**
     * getLibraryDefinition
     *
     * @param string $libraryClassName
     * @return array ($this->_aTriggerClasses_[$libraryClassName])
     */
    public function getLibraryDefinition($libraryClassName)
    {
        return ($this->_aTriggerClasses_[$libraryClassName]);
    }

    /**
     * __destruct
     *
     * @return void
     */
    public function __destruct()
    {
        //TODO - Insert your code here
    }
}
