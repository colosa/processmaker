<?php
namespace Maveriks;

use Maveriks\Util;
use ProcessMaker\Services;
use ProcessMaker\Services\Api;
use Luracast\Restler\RestException;

/**
 * Web application bootstrap
 *
 * @author Erik Amaru Ortiz <aortiz.erik@gmail.com>
 */
class WebApplication
{
    const RUNNING_DEFAULT = "default.running";
    const RUNNING_INDEX = "index.running";
    const RUNNING_WORKFLOW = "workflow.running";
    const RUNNING_API = "api.running";
    const RUNNING_OAUTH2 = "api.oauth2";
    const SERVICE_API = "service.api";
    const SERVICE_OAUTH2 = "service.oauth2";
    const REDIRECT_DEFAULT = "redirect.default";

    /**
     * @var string application root directory
     */
    protected $rootDir = "";
    /**
     * @var string workflow directory
     */
    protected $workflowDir = "";
    /**
     * @var string workspace directory located into shared directory
     */
    protected $workspaceDir = "";
    /**
     * @var string workspace cache directory
     */
    protected $workspaceCacheDir = "";
    /**
     * @var string request location uri
     */
    protected $requestUri = "";
    /**
     * @var array holds multiple request response
     */
    protected $responseMultipart = array();
    /**
     * @var \Maveriks\Extension\Restler main REST dispatcher object
     */
    protected $rest;

    /**
     * class constructor
     */
    public function __construct()
    {
        defined("DS") || define("DS", DIRECTORY_SEPARATOR);
    }

    /**
     * @param string $rootDir
     */
    public function setRootDir($rootDir)
    {
        $this->rootDir = rtrim($rootDir, DS);
        $this->workflowDir = $rootDir . DS . "workflow" . DS;
    }

    /**
     * @return string
     */
    public function getRootDir()
    {
        return $this->rootDir;
    }

    /**
     * @param string $requestUri
     */
    public function setRequestUri($requestUri)
    {
        $this->requestUri = $requestUri;
    }

    /**
     * @return string
     */
    public function getRequestUri()
    {
        return $this->requestUri;
    }

    /**
     * Routes the request to dispatch
     * @return string
     */
    public function route()
    {
        if ($this->requestUri === "/") {
            if (file_exists("index.html")) {
                return self::RUNNING_INDEX;
            } else {
                return self::RUNNING_DEFAULT;
            }
        } elseif (substr($this->requestUri, 1, 3) === "api"
            && count(explode("/", $this->requestUri)) >= 4 // url api pattern: /api/1.0/<workspace>/<resource>
        ) {
            return self::RUNNING_API;
        } else {
            list($this->requestUri,) = explode('?', $this->requestUri);
            $uriParts = explode('/', $this->requestUri);

            if (isset($uriParts[2]) && $uriParts[2] == "oauth2") {
                return self::RUNNING_OAUTH2;
            } else {
                return self::RUNNING_WORKFLOW;
            }
        }
    }

    /**
     * Run application
     * @param string $type the request type to run and dispatch, by now only self::SERVICE_API is accepted
     */
    public function run($type = "")
    {
        switch ($type) {
            case self::SERVICE_API:
                $request = $this->parseApiRequestUri();

                if ($request["version"] != $this->getApiVersion()) {
                    $rest = new \Maveriks\Extension\Restler();
                    $rest->setMessage(new RestException(Api::STAT_APP_EXCEPTION, "Invalid API version."));

                    exit(0);
                }

                $this->loadEnvironment($request["workspace"]);

                Util\Logger::log("REST API Dispatching url: ".$_SERVER["REQUEST_METHOD"]." ".$request["uri"]);

                if (isset($_SERVER["HTTP_X_REQUESTED_WITH"]) && strtoupper($_SERVER["HTTP_X_REQUESTED_WITH"]) == 'MULTIPART') {
                    $this->dispatchMultipleApiRequest($request["uri"], $request["version"]);
                } else {
                    $this->dispatchApiRequest($request["uri"], $request["version"]);
                }
                Util\Logger::log("API::End Dispatch");
                break;

            case self::SERVICE_OAUTH2:
                $uriTemp = explode('/', $_SERVER['REQUEST_URI']);
                array_shift($uriTemp);
                $workspace = array_shift($uriTemp);
                $uri = '/' . implode('/', $uriTemp);

                $this->loadEnvironment($workspace);
                $this->dispatchApiRequest($uri, $version = "1.0");
                break;
        }
    }

    /**
     * Dispatch multiple api request
     *
     * @param string $uri the request uri
     * @param string $version version of api
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     */
    public function dispatchMultipleApiRequest($uri, $version = "1.0")
    {

        $stringInput = file_get_contents('php://input');

        if (empty($stringInput)) {
            $rest = new \Maveriks\Extension\Restler();
            $rest->setMessage(new RestException(Api::STAT_APP_EXCEPTION, "Invalid Request, multipart without body."));
            exit();
        } else {
            $input = json_decode($stringInput);
            if (empty($input->calls)) {
                $rest = new \Maveriks\Extension\Restler();
                $rest->setMessage(new RestException(Api::STAT_APP_EXCEPTION, "Invalid Request, multipart body without calls."));
                exit();
            }
        }

        $baseUrl = (empty($input->base_url)) ? $uri : $input->base_url;

        foreach($input->calls as $value) {
            $_SERVER["REQUEST_METHOD"] = empty($value->method) ? 'GET' : $value->method;
            $uriTemp = trim($baseUrl) . trim($value->url);

            if (strpos($uriTemp, '?') !== false) {
                $dataGet = explode('?', $uriTemp);
                parse_str($dataGet[1], $_GET);
            }

            $inputExecute = empty($value->data) ? '' : json_encode($value->data);
            $this->responseMultipart[] = $this->dispatchApiRequest($uriTemp, $version, true, $inputExecute);
        }

        echo json_encode($this->responseMultipart);
    }

    /**
     * This method dispatch rest/api service
     * @author Erik Amaru Ortiz <erik@colosa.com>
     */
    public function dispatchApiRequest($uri, $version = "1.0", $multipart = false, $inputExecute = '')
    {
        $uri = $this->initRest($uri, "1.0", $multipart);

        // to handle a request with "OPTIONS" method
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS, HEADERS');
            header('Access-Control-Allow-Headers: authorization, content-type');
            header("Access-Control-Allow-Credentials", "false");
            header('Access-Control-Allow-Origin: *');
            header('Access-Control-Max-Age: 60');
            die();
        }

        /*
         * Enable this header to allow "Cross Domain AJAX" requests;
         * This works because processmaker is handling correctly requests with method 'OPTIONS'
         * that automatically is sent by a client using XmlHttpRequest or similar.
         */
        header('Access-Control-Allow-Origin: *');

        $_SERVER['REQUEST_URI'] = $uri;

        $this->rest->inputExecute = $inputExecute;
        $this->rest->handle();

        if ($this->rest->flagMultipart === true) {
            return $this->rest->responseMultipart;
        }
    }

    /**
     * create a new instance of local $rest Restler object
     */
    protected function initRest($uri, $version, $multipart = false)
    {
        require_once $this->rootDir . "/framework/src/Maveriks/Extension/Restler/UploadFormat.php";

        // $servicesDir contains directory where Services Classes are allocated
        $servicesDir = $this->workflowDir . 'engine' . DS . 'src' . DS . 'ProcessMaker' . DS . 'Services' . DS;
        // $apiDir - contains directory to scan classes and add them to Restler
        $apiDir = $servicesDir . 'Api' . DS;
        // $apiIniFile - contains file name of api ini configuration
        $apiIniFile = $servicesDir . DS . 'api.ini';
        // $authenticationClass - contains the class name that validate the authentication for Restler
        $authenticationClass = 'ProcessMaker\\Services\\OAuth2\\Server';
        // $pmOauthClientId - contains PM Local OAuth Id (Web Designer)
        $pmOauthClientId = 'x-pm-local-client';

        /*
         * Load Api ini file for Rest Service
         */
        $config = array();

        if (file_exists($apiIniFile)) {
            $cachedConfig = $this->workspaceCacheDir . "api-config.php";

            // verify if config cache file exists, is array and the last modification date is the same when cache was created.
            if (! file_exists($cachedConfig) || ! is_array($config = include($cachedConfig)) || $config["_chk"] != filemtime($apiIniFile)) {
                $config = Util\Common::parseIniFile($apiIniFile);
                $config["_chk"] = filemtime($apiIniFile);
                if (! is_dir(dirname($cachedConfig))) {
                    Util\Common::mk_dir(dirname($cachedConfig));
                }
                file_put_contents($cachedConfig, "<?php return " . var_export($config, true).";");
                Util\Logger::log("Configuration cache was loaded and cached to: $cachedConfig");
            } else {
                Util\Logger::log("Loading Api Configuration from: $cachedConfig");
            }
        }

        // Setting current workspace to Api class
        Services\Api::setWorkspace(SYS_SYS);
        $cacheDir = defined("PATH_C")? PATH_C: sys_get_temp_dir();

        $sysConfig = \System::getSystemConfiguration();

        \Luracast\Restler\Defaults::$cacheDirectory = $cacheDir;
        $productionMode = (bool) !(isset($sysConfig["service_api_debug"]) && $sysConfig["service_api_debug"]);

        Util\Logger::log("Serving API mode: " . ($productionMode? "production": "development"));

        // create a new Restler instance
        //$rest = new \Luracast\Restler\Restler();
        $this->rest = new \Maveriks\Extension\Restler($productionMode);
        // setting flag for multipart to Restler
        $this->rest->setFlagMultipart($multipart);
        // setting api version to Restler
        $this->rest->setAPIVersion($version);
        // adding $authenticationClass to Restler
        $this->rest->addAuthenticationClass($authenticationClass, '');

        // Setting database connection source
        list($host, $port) = strpos(DB_HOST, ':') !== false ? explode(':', DB_HOST) : array(DB_HOST, '');
        $port = empty($port) ? '' : ";port=$port";
        Services\OAuth2\Server::setDatabaseSource(DB_USER, DB_PASS, DB_ADAPTER.":host=$host;dbname=".DB_NAME.$port);
        if (DB_NAME != DB_RBAC_NAME) { //it's PM < 3
            list($host, $port) = strpos(DB_RBAC_HOST, ':') !== false ? explode(':', DB_RBAC_HOST) : array(DB_RBAC_HOST, '');
            $port = empty($port) ? '' : ";port=$port";
            Services\OAuth2\Server::setDatabaseSourceRBAC(DB_RBAC_USER, DB_RBAC_PASS, DB_ADAPTER.":host=$host;dbname=".DB_RBAC_NAME.$port);
        }

        // Setting default OAuth Client id, for local PM Web Designer
        Services\OAuth2\Server::setPmClientId($pmOauthClientId);

        $this->rest->setOverridingFormats('JsonFormat', 'UploadFormat');

        // scan all api directory to find api classes
        $classesList = Util\Common::rglob($apiDir . "/*");

        foreach ($classesList as $classFile) {
            if (pathinfo($classFile, PATHINFO_EXTENSION) === 'php') {
                $relClassPath = str_replace('.php', '', str_replace($servicesDir, '', $classFile));
                $namespace = '\\ProcessMaker\\Services\\' . str_replace(DS, '\\', $relClassPath);
                $namespace = strpos($namespace, "//") === false? $namespace: str_replace("//", '', $namespace);

                //if (! class_exists($namespace)) {
                require_once $classFile;
                //}

                $this->rest->addAPIClass($namespace);
            }
        }
        // adding aliases for Restler
        if (array_key_exists('alias', $config)) {
            foreach ($config['alias'] as $alias => $aliasData) {
                if (is_array($aliasData)) {
                    foreach ($aliasData as $label => $namespace) {
                        $namespace = '\\' . ltrim($namespace, '\\');
                        $this->rest->addAPIClass($namespace, $alias);
                    }
                }
            }
        }

        // 
        // Register API Plugins classes
        $isPluginRequest = strpos($uri, '/plugin-') !== false ? true : false;

        if ($isPluginRequest) {
            $tmp = explode('/', $uri);
            array_shift($tmp);
            $tmp = array_shift($tmp);
            $tmp = explode('-', $tmp);
            $pluginName = $tmp[1];
            $uri = str_replace('plugin-'.$pluginName, strtolower($pluginName), $uri);
        }
        
        // hook to get rest api classes from plugins
        if (class_exists('PMPluginRegistry') && file_exists(PATH_DATA_SITE . 'plugin.singleton')) {
            $pluginRegistry = \PMPluginRegistry::loadSingleton(PATH_DATA_SITE . 'plugin.singleton');
            $plugins = $pluginRegistry->getRegisteredRestServices();

            if (! empty($plugins)) {
                foreach ($plugins as $pluginName => $plugin) {
                    $pluginSourceDir = PATH_PLUGINS . $pluginName . DIRECTORY_SEPARATOR . 'src';

                    $loader = \Maveriks\Util\ClassLoader::getInstance();
                    $loader->add($pluginSourceDir);
                    
                    foreach ($plugin as $class) {
                        if (class_exists($class['namespace'])) {
                            $this->rest->addAPIClass($class['namespace'], strtolower($pluginName));
                        }             
                    }                    
                }
            }
        }

        Services\OAuth2\Server::setWorkspace(SYS_SYS);
        $this->rest->addAPIClass('\ProcessMaker\\Services\\OAuth2\\Server', 'oauth2');

        return $uri;
    }

    public function parseApiRequestUri()
    {
        $url = explode("/", $this->requestUri);
        array_shift($url);
        array_shift($url);
        $version = array_shift($url);
        $workspace = array_shift($url);
        $restUri = "";

        foreach ($url as $urlPart) {
            $restUri .= "/" . $urlPart;
        }

        return array(
            "uri" => $restUri,
            "version" => $version,
            "workspace" => $workspace
        );
    }

    public function loadEnvironment($workspace = "")
    {
        define("PATH_SEP", DIRECTORY_SEPARATOR);

        define("PATH_TRUNK",    $this->rootDir . PATH_SEP);
        define("PATH_OUTTRUNK", realpath($this->rootDir . "/../") . PATH_SEP);
        define("PATH_HOME",     $this->rootDir . PATH_SEP . "workflow" . PATH_SEP);

        define("PATH_HTML", PATH_HOME . "public_html" . PATH_SEP);
        define("PATH_RBAC_HOME", PATH_TRUNK . "rbac" . PATH_SEP);
        define("PATH_GULLIVER_HOME", PATH_TRUNK . "gulliver" . PATH_SEP);
        define("PATH_GULLIVER", PATH_GULLIVER_HOME . "system" . PATH_SEP); //gulliver system classes
        define("PATH_GULLIVER_BIN", PATH_GULLIVER_HOME . "bin" . PATH_SEP); //gulliver bin classes
        define("PATH_TEMPLATE", PATH_GULLIVER_HOME . "templates" . PATH_SEP);
        define("PATH_THIRDPARTY", PATH_GULLIVER_HOME . "thirdparty" . PATH_SEP);
        define("PATH_RBAC", PATH_RBAC_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP); //to enable rbac version 2
        define("PATH_RBAC_CORE", PATH_RBAC_HOME . "engine" . PATH_SEP);
        define("PATH_CORE", PATH_HOME . "engine" . PATH_SEP);
        define("PATH_CLASSES", PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP);
        define("PATH_SKINS", PATH_CORE . "skins" . PATH_SEP);
        define("PATH_SKIN_ENGINE", PATH_CORE . "skinEngine" . PATH_SEP);
        define("PATH_METHODS", PATH_CORE . "methods" . PATH_SEP);
        define("PATH_XMLFORM", PATH_CORE . "xmlform" . PATH_SEP);
        define("PATH_CONFIG", PATH_CORE . "config" . PATH_SEP);
        define("PATH_PLUGINS", PATH_CORE . "plugins" . PATH_SEP);
        define("PATH_HTMLMAIL", PATH_CORE . "html_templates" . PATH_SEP);
        define("PATH_TPL", PATH_CORE . "templates" . PATH_SEP);
        define("PATH_TEST", PATH_CORE . "test" . PATH_SEP);
        define("PATH_FIXTURES", PATH_TEST . "fixtures" . PATH_SEP);
        define("PATH_RTFDOCS", PATH_CORE . "rtf_templates" . PATH_SEP);
        define("PATH_DYNACONT", PATH_CORE . "content" . PATH_SEP . "dynaform" . PATH_SEP);
        define("SYS_UPLOAD_PATH", PATH_HOME . "public_html/files/" );
        define("PATH_UPLOAD", PATH_HTML . "files" . PATH_SEP);
        define("PATH_WORKFLOW_MYSQL_DATA", PATH_CORE . "data" . PATH_SEP . "mysql" . PATH_SEP);
        define("PATH_RBAC_MYSQL_DATA", PATH_RBAC_CORE . "data" . PATH_SEP . "mysql" . PATH_SEP);
        define("FILE_PATHS_INSTALLED", PATH_CORE . "config" . PATH_SEP . "paths_installed.php");
        define("PATH_WORKFLOW_MSSQL_DATA", PATH_CORE . "data" . PATH_SEP . "mssql" . PATH_SEP);
        define("PATH_RBAC_MSSQL_DATA", PATH_RBAC_CORE . "data" . PATH_SEP . "mssql" . PATH_SEP);
        define("PATH_CONTROLLERS", PATH_CORE . "controllers" . PATH_SEP);
        define("PATH_SERVICES_REST", PATH_CORE . "services" . PATH_SEP . "rest" . PATH_SEP);


        \Bootstrap::registerSystemClasses();


        $config = \System::getSystemConfiguration();

        // Do not change any of these settings directly, use env.ini instead
        ini_set( "display_errors", $config["display_errors"]);
        ini_set( "error_reporting", $config["error_reporting"]);
        ini_set( "short_open_tag", "On" ); // ??
        ini_set( "default_charset", "UTF-8" ); // ??
        ini_set( "memory_limit", $config["memory_limit"] );
        ini_set( "soap.wsdl_cache_enabled", $config["wsdl_cache"] );
        ini_set( "date.timezone", $config["time_zone"] );

        define("DEBUG_SQL_LOG", $config["debug_sql"]);
        define("DEBUG_TIME_LOG", $config["debug_time"]);
        define("DEBUG_CALENDAR_LOG", $config["debug_calendar"]);
        define("MEMCACHED_ENABLED",  $config["memcached"]);
        define("MEMCACHED_SERVER",   $config["memcached_server"]);
        define("TIME_ZONE", $config["time_zone"]);
        define("SYS_SKIN", $config["default_skin"]);

        // set include path
        set_include_path(
            PATH_CORE . PATH_SEPARATOR .
            PATH_THIRDPARTY . PATH_SEPARATOR .
            PATH_THIRDPARTY . "pear" . PATH_SEPARATOR .
            PATH_RBAC_CORE . PATH_SEPARATOR .
            get_include_path()
        );
        ///print_r(get_include_path()); die;

        /*
         * Setting Up Workspace
         */

        if (! file_exists( FILE_PATHS_INSTALLED )) {
            throw new \Exception("Can't locate system file: " . FILE_PATHS_INSTALLED);
        }

        // include the server installed configuration
        require_once PATH_CORE . "config" . PATH_SEP . "paths_installed.php";

        // defining system constant when a valid server environment exists
        define("PATH_LANGUAGECONT", PATH_DATA . "META-INF" . PATH_SEP );
        define("PATH_CUSTOM_SKINS", PATH_DATA . "skins" . PATH_SEP );
        define("PATH_TEMPORAL", PATH_C . "dynEditor/");
        define("PATH_DB", PATH_DATA . "sites" . PATH_SEP);

        \Bootstrap::setLanguage();

        \Bootstrap::LoadTranslationObject((defined("SYS_LANG"))? SYS_LANG : "en");

        if (empty($workspace)) {
            return true;
        }

        define("SYS_SYS", $workspace);

        if (!file_exists(PATH_DB . SYS_SYS . PATH_SEP . "db.php")) {
            $rest = new \Maveriks\Extension\Restler();
            $rest->setMessage(new RestException(Api::STAT_APP_EXCEPTION, \G::LoadTranslation("ID_NOT_WORKSPACE")));

            exit(0);
        }

        require_once (PATH_DB . SYS_SYS . "/db.php");

        // defining constant for workspace shared directory
        $this->workspaceDir = PATH_DB . SYS_SYS . PATH_SEP;
        $this->workspaceCacheDir = PATH_DB . SYS_SYS . PATH_SEP . "cache" . PATH_SEP;

        define("PATH_WORKSPACE", $this->workspaceDir);
        // including workspace shared classes -> particularlly for pmTables

        set_include_path(get_include_path() . PATH_SEPARATOR . PATH_WORKSPACE);

        // smarty constants
        define( "PATH_SMARTY_C", PATH_C . "smarty" . PATH_SEP . "c" );
        define( "PATH_SMARTY_CACHE", PATH_C . "smarty" . PATH_SEP . "cache" );

        define("PATH_DATA_SITE",                PATH_DATA      . "sites/" . SYS_SYS . "/");
        define("PATH_DOCUMENT",                 PATH_DATA_SITE . "files/");
        define("PATH_DATA_MAILTEMPLATES",       PATH_DATA_SITE . "mailTemplates/");
        define("PATH_DATA_PUBLIC",              PATH_DATA_SITE . "public/");
        define("PATH_DATA_REPORTS",             PATH_DATA_SITE . "reports/");
        define("PATH_DYNAFORM",                 PATH_DATA_SITE . "xmlForms/");
        define("PATH_IMAGES_ENVIRONMENT_FILES", PATH_DATA_SITE . "usersFiles" . PATH_SEP);
        define("PATH_IMAGES_ENVIRONMENT_USERS", PATH_DATA_SITE . "usersPhotographies" . PATH_SEP);

        /**
         * Global definitions, before it was the defines.php file
         */

        // URL Key
        define( "URL_KEY", 'c0l0s40pt1mu59r1m3' );

        // Other definitions
        define( 'TIMEOUT_RESPONSE', 100 ); //web service timeout
        define( 'APPLICATION_CODE', 'ProcessMaker' ); //to login like workflow system
        define( 'MAIN_POFILE', 'processmaker' );
        define( 'PO_SYSTEM_VERSION', 'PM 4.0.1' );

        // Environment definitions
        define( 'G_PRO_ENV', 'PRODUCTION' );
        define( 'G_DEV_ENV', 'DEVELOPMENT' );
        define( 'G_TEST_ENV', 'TEST' );

        // Number of files per folder at PATH_UPLOAD (cases documents)
        define( 'APPLICATION_DOCUMENTS_PER_FOLDER', 1000 );

        // Server of ProcessMaker Library
        define( 'PML_SERVER', 'http://library.processmaker.com' );
        define( 'PML_WSDL_URL', PML_SERVER . '/syspmLibrary/en/green/services/wsdl' );
        define( 'PML_UPLOAD_URL', PML_SERVER . '/syspmLibrary/en/green/services/uploadProcess' );
        define( 'PML_DOWNLOAD_URL', PML_SERVER . '/syspmLibrary/en/green/services/download' );

        // create memcached singleton
        //\Bootstrap::LoadClass("memcached");
        //$memcache = PMmemcached::getSingleton( SYS_SYS );

        \Propel::init(PATH_CONFIG . "databases.php");

        return true;
    }

    public function getApiVersion()
    {
        try {
            $arrayConfig = array();

            //$apiIniFile - Contains file name of api ini configuration
            $apiIniFile = $this->workflowDir . "engine" . DS . "src" . DS . "ProcessMaker" . DS . "Services" . DS . "api.ini";

            if (file_exists($apiIniFile)) {
                $arrayConfig = Util\Common::parseIniFile($apiIniFile);
            }

            return (isset($arrayConfig["api"]["version"]))? $arrayConfig["api"]["version"] : "1.0";
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public static function purgeRestApiCache($workspace)
    {
        @unlink(PATH_DATA . 'compiled' . DS . 'routes.php');
        @unlink(PATH_DATA . 'sites' . DS . $workspace . DS . 'api-config.php');
    }
}

