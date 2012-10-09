<?php
/**
 *
 * @author Hugo Loza <hugo@colosa.com>
 *
 * This class Helps registering and implementing Wizard for Triggers
 */
G::LoadThirdParty( 'html2ps_pdf/classes', 'include' );
G::LoadThirdParty( 'html2ps_pdf/classes/org/active-link/doc', 'PHPClass' );

/**
 *
 * @package workflow.engine.ProcessMaker
 */

class triggerLibrary
{

    private $_aTriggerClasses_ = array ();

    private static $instance = NULL;

    /**
     * __construct
     *
     * @return void
     */
    function __construct ()
    {
        //Initialize the Library and register the Default
        $this->registerFunctionsFileToLibrary( PATH_CORE . "classes" . PATH_SEP . "class.pmFunctions.php", "ProcessMaker Functions" );

        //Register all registered PLugin Functions
        if (class_exists( 'folderData' )) {
            //$folderData = new folderData($sProUid, $proFields['PRO_TITLE'], $sAppUid, $Fields['APP_TITLE'], $sUsrUid);
            $oPluginRegistry = &PMPluginRegistry::getSingleton();
            $aAvailablePmFunctions = $oPluginRegistry->getPmFunctions();
            foreach ($aAvailablePmFunctions as $key => $class) {
                $filePlugin = PATH_PLUGINS . $class . PATH_SEP . 'classes' . PATH_SEP . 'class.pmFunctions.php';

                if (file_exists( $filePlugin ) && ! is_dir( $filePlugin )) {
                    $this->registerFunctionsFileToLibrary( $filePlugin, "ProcessMaker Functions" );
                }
            }

        }
        //Add External Triggers
        $dir = G::ExpandPath( "classes" ) . 'triggers';
        $filesArray = array ();

        if (file_exists( $dir )) {
            if ($handle = opendir( $dir )) {
                while (false !== ($file = readdir( $handle ))) {
                    if ($file != "." && $file != ".." && ! is_dir( $dir . PATH_SEP . $file )) {
                        $this->registerFunctionsFileToLibrary( $dir . PATH_SEP . $file, "ProcessMaker External Functions" );
                    }
                }
                closedir( $handle );
            }
        }
    }

    /**
     * &getSingleton
     *
     * @return self::$instance;
     */
    function &getSingleton ()
    {
        if (self::$instance == NULL) {
            self::$instance = new triggerLibrary();
        }
        return self::$instance;
    }

    /**
     * serializeInstance
     *
     * @return serialize ( self::$instance );
     */
    function serializeInstance ()
    {
        return serialize( self::$instance );
    }

    /**
     * unSerializeInstance
     *
     * @param integer $serialized
     * @return void
     */
    function unSerializeInstance ($serialized)
    {
        if (self::$instance == NULL) {
            self::$instance = new PMPluginRegistry();
        }

        $instance = unserialize( $serialized );
        self::$instance = $instance;
    }

    /**
     * registerFunctionsFileToLibrary
     *
     * @param string $filePath
     * @param string $libraryName
     * @return void
     */
    function registerFunctionsFileToLibrary ($filePath, $libraryName)
    {
        $aLibrary = $this->getMethodsFromLibraryFile( $filePath );
        $aLibrary->libraryFile = $filePath;
        $aLibrary->libraryName = $libraryName;
        if (isset( $aLibrary->info['className'] )) {
            $this->_aTriggerClasses_[$aLibrary->info['className']] = $aLibrary;
        }

    }

    /**
     * getMethodsFromLibraryFile
     *
     * @param string $file
     * @return object(PHPClass) $parsedLibrary
     */
    function getMethodsFromLibraryFile ($file)
    {
        // parse class comments from file
        $parsedLibrary = new PHPClass();
        //$success = $parsedLibrary->parseFromFile ( PATH_CORE . "classes" . PATH_SEP . $file );
        $success = $parsedLibrary->parseFromFile( $file );

        return $parsedLibrary;
    }

    /**
     * getRegisteredClasses
     *
     * @return array ($this->_aTriggerClasses_)
     */
    function getRegisteredClasses ()
    {
        return ($this->_aTriggerClasses_);
    }

    /**
     * getLibraryDefinition
     *
     * @param string $libraryClassName
     * @return array ($this->_aTriggerClasses_[$libraryClassName])
     */
    function getLibraryDefinition ($libraryClassName)
    {
        return ($this->_aTriggerClasses_[$libraryClassName]);
    }

    /**
     * __destruct
     *
     * @return void
     */
    function __destruct ()
    {

        //TODO - Insert your code here
    }
}

?>