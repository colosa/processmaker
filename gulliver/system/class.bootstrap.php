<?php
/**
 * class.bootstrap.php
 *
 * @package gulliver.system
 *
 */

class Bootstrap
{
    static $includeClassPaths = array();

    protected $relativeIncludePaths = array();

    //below here only approved methods

    /* the autoloader functions */

    static function autoloadClass($class)
    {
        //error_log( "$class ");
        $className = strtolower($class);
        if (array_key_exists($className, BootStrap::$includeClassPaths)) {
            require_once BootStrap::$includeClassPaths[$className];
            return true;
        } else {
          //print "$class "; //die;
          //print_r ( debug_backtrace(false));
        }
        return;
    }

    public function registerClass($classname, $includeFile)
    {
        BootStrap::$includeClassPaths[strtolower($classname)] = $includeFile;
        return;
    }

    /*
     * these functions still under revision
    */
    public function getSystemConfiguration ($globalIniFile = '', $wsIniFile = '', $wsName = '')
    {
        $readGlobalIniFile = false;
        $readWsIniFile = false;

        if (empty( $globalIniFile )) {
            $globalIniFile = PATH_CORE . 'config' . PATH_SEP . 'env.ini';
        }

        if (empty( $wsIniFile )) {
            if (defined( 'PATH_DB' )) {
                // if we're on a valid workspace env.
                if (empty( $wsName )) {
                    $uriParts = explode( '/', getenv( "REQUEST_URI" ) );
                    if (isset( $uriParts[1] )) {
                        if (substr( $uriParts[1], 0, 3 ) == 'sys') {
                            $wsName = substr( $uriParts[1], 3 );
                        }
                    }
                }
                $wsIniFile = PATH_DB . $wsName . PATH_SEP . 'env.ini';
            }
        }

        $readGlobalIniFile = file_exists( $globalIniFile ) ? true : false;
        $readWsIniFile = file_exists( $wsIniFile ) ? true : false;

        if (isset( $_SESSION['PROCESSMAKER_ENV'] )) {
            $md5 = array ();

            if ($readGlobalIniFile) {
                $md5[] = md5_file( $globalIniFile );
            }
            if ($readWsIniFile) {
                $md5[] = md5_file( $wsIniFile );
            }
            $hash = implode( '-', $md5 );

            if ($_SESSION['PROCESSMAKER_ENV_HASH'] === $hash) {
                $_SESSION['PROCESSMAKER_ENV']['from_cache'] = 1;
                return $_SESSION['PROCESSMAKER_ENV'];
            }
        }

        // default configuration
        $config = array ('debug' => 0,'debug_sql' => 0,'debug_time' => 0,'debug_calendar' => 0,'wsdl_cache' => 1,'memory_limit' => '128M','time_zone' => 'America/New_York','memcached' => 0,'memcached_server' => '','default_skin' => 'classic','default_lang' => 'en','proxy_host' => '','proxy_port' => '','proxy_user' => '','proxy_pass' => ''
        );

        // read the global env.ini configuration file
        if ($readGlobalIniFile && ($globalConf = @parse_ini_file( $globalIniFile )) !== false) {
            $config = array_merge( $config, $globalConf );
        }

        // Workspace environment configuration
        if ($readWsIniFile && ($wsConf = @parse_ini_file( $wsIniFile )) !== false) {
            $config = array_merge( $config, $wsConf );
        }

        // validation debug config, only binary value is valid; debug = 1, to enable
        $config['debug'] = $config['debug'] == 1 ? 1 : 0;

        if ($config['proxy_pass'] != '') {
            $config['proxy_pass'] = G::decrypt( $config['proxy_pass'], 'proxy_pass' );
        }

        $md5 = array ();
        if ($readGlobalIniFile) {
            $md5[] = md5_file( $globalIniFile );
        }
        if ($readWsIniFile) {
            $md5[] = md5_file( $wsIniFile );
        }
        $hash = implode( '-', $md5 );

        $_SESSION['PROCESSMAKER_ENV'] = $config;
        $_SESSION['PROCESSMAKER_ENV_HASH'] = $hash;

        return $config;
    }

    public static function registerSystemClasses()
    {
        //DATABASE propel classes used in "Cases" Options
        self::registerClass("Entity_Base",        PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "entities" . PATH_SEP . "Base.php");

        self::registerClass("BaseContent",        PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "om" . PATH_SEP . "BaseContent.php");
        self::registerClass("Content",            PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "Content.php");
        self::registerClass("BaseContentPeer",    PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "om" . PATH_SEP . "BaseContentPeer.php");
        self::registerClass("ContentPeer",        PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "ContentPeer.php");
        self::registerClass("BaseApplication",    PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "om" . PATH_SEP . "BaseApplication.php");
        self::registerClass("ApplicationPeer",    PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "ApplicationPeer.php");
        self::registerClass("Application",        PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "Application.php");

        self::registerClass("BaseAppDelegation",  PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "om" . PATH_SEP . "BaseAppDelegation.php");
        self::registerClass("BaseHoliday",        PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "om" . PATH_SEP . "BaseHoliday.php");
        self::registerClass("BaseHolidayPeer",    PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "om" . PATH_SEP . "BaseHolidayPeer.php");
        self::registerClass("BaseTask",           PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "om" . PATH_SEP . "BaseTask.php");
        self::registerClass("BaseTaskPeer",       PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "om" . PATH_SEP . "BaseTaskPeer.php");
        self::registerClass("HolidayPeer",        PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "HolidayPeer.php");
        self::registerClass("Holiday",            PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "Holiday.php");
        self::registerClass("Task",               PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "Task.php");
        self::registerClass("TaskPeer",           PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "TaskPeer.php");
        self::registerClass("dates",              PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "class.dates.php");
        self::registerClass("AppDelegation",      PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "AppDelegation.php");
        self::registerClass("BaseAppDelegationPeer",PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "om" . PATH_SEP . "BaseAppDelegationPeer.php");
        self::registerClass("AppDelegationPeer",  PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "AppDelegationPeer.php");
        self::registerClass("BaseAppDelay",       PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "om" . PATH_SEP . "BaseAppDelay.php");
        self::registerClass("AppDelayPeer",       PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "AppDelayPeer.php");
        self::registerClass("AppDelay",           PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "AppDelay.php");
        self::registerClass("BaseAdditionalTables",PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "om" . PATH_SEP . "BaseAdditionalTables.php");
        self::registerClass("AdditionalTables",   PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "AdditionalTables.php");
        self::registerClass("BaseAppCacheView",   PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "om" . PATH_SEP . "BaseAppCacheView.php");
        self::registerClass("AppCacheView",       PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "AppCacheView.php");
        self::registerClass("BaseAppCacheViewPeer",PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "om" . PATH_SEP . "BaseAppCacheViewPeer.php");
        self::registerClass("AppCacheViewPeer",   PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "AppCacheViewPeer.php");

        self::registerClass("BaseInputDocument",  PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "om" . PATH_SEP . "BaseInputDocument.php");
        self::registerClass("InputDocument",      PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "InputDocument.php");
        self::registerClass("BaseAppDocument",    PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "om" . PATH_SEP . "BaseAppDocument.php");
        self::registerClass("AppDocument",        PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "AppDocument.php");
        self::registerClass("AppDocumentPeer",    PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "AppDocumentPeer.php");

        self::registerClass("BaseAppEvent",       PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "om" . PATH_SEP . "BaseAppEvent.php");
        self::registerClass("AppEvent",           PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "AppEvent.php");
        self::registerClass("AppEventPeer",       PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "AppEventPeer.php");

        self::registerClass("BaseAppHistory",     PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "om" . PATH_SEP . "BaseAppHistory.php");
        self::registerClass("AppHistory",         PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "AppHistory.php");
        self::registerClass("AppHistoryPeer",     PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "AppHistoryPeer.php");

        self::registerClass("BaseAppFolder",      PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "om" . PATH_SEP . "BaseAppFolder.php");
        self::registerClass("AppFolder",          PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "AppFolder.php");
        self::registerClass("AppFolderPeer",      PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "AppFolderPeer.php");

        self::registerClass("BaseAppMessage",     PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "om" . PATH_SEP . "BaseAppMessage.php");
        self::registerClass("AppMessage",         PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "AppMessage.php");

        self::registerClass("BaseAppMessagePeer", PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "om" . PATH_SEP . "BaseAppMessagePeer.php");
        self::registerClass("AppMessagePeer",     PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "AppMessagePeer.php");

        self::registerClass("BaseAppNotesPeer",    PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "om" . PATH_SEP . "BaseAppNotesPeer.php");
        self::registerClass("AppNotesPeer",        PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "AppNotesPeer.php");

        self::registerClass("BaseAppNotes",        PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "om" . PATH_SEP . "BaseAppNotes.php");
        self::registerClass("AppNotes",            PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "AppNotes.php");

        self::registerClass("BaseAppOwner",        PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "om" . PATH_SEP . "BaseAppOwner.php");
        self::registerClass("AppOwner",            PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "AppOwner.php");
        self::registerClass("AppOwnerPeer",        PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "AppOwnerPeer.php");

        self::registerClass("BaseAppSolrQueue",    PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "om" . PATH_SEP . "BaseAppSolrQueue.php");
        self::registerClass("Entity_AppSolrQueue", PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "entities" . PATH_SEP . "AppSolrQueue.php");
        self::registerClass("AppSolrQueue",        PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "AppSolrQueue.php");
        self::registerClass("AppSolrQueuePeer",    PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "AppSolrQueuePeer.php");

        self::registerClass("BaseAppThread",       PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "om" . PATH_SEP . "BaseAppThread.php");
        self::registerClass("AppThread",           PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "AppThread.php");
        self::registerClass("AppThreadPeer",       PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "AppThreadPeer.php");

        self::registerClass("BaseCaseScheduler",   PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "om" . PATH_SEP . "BaseCaseScheduler.php");
        self::registerClass("CaseScheduler",       PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "CaseScheduler.php");

        self::registerClass("BaseCaseSchedulerPeer",PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "om" . PATH_SEP . "BaseCaseSchedulerPeer.php");
        self::registerClass("CaseSchedulerPeer",    PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "CaseSchedulerPeer.php");

        self::registerClass("BaseCaseTracker",     PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "om" . PATH_SEP . "BaseCaseTracker.php");
        self::registerClass("CaseTracker",         PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "CaseTracker.php");

        self::registerClass("BaseCaseTrackerPeer", PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "om" . PATH_SEP . "BaseCaseTrackerPeer.php");
        self::registerClass("CaseTrackerPeer",     PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "CaseTrackerPeer.php");

        self::registerClass("BaseCaseTrackerObject",PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "om" . PATH_SEP . "BaseCaseTrackerObject.php");
        self::registerClass("CaseTrackerObject",    PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "CaseTrackerObject.php");

        self::registerClass("BaseCaseTrackerObjectPeer",PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "om" . PATH_SEP . "BaseCaseTrackerObjectPeer.php");
        self::registerClass("CaseTrackerObjectPeer",    PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "CaseTrackerObjectPeer.php");

        self::registerClass("BaseConfiguration",   PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "om" . PATH_SEP . "BaseConfiguration.php");
        self::registerClass("Configuration",       PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "Configuration.php");

        self::registerClass("BaseDbSource",        PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "om" . PATH_SEP . "BaseDbSource.php");
        self::registerClass("DbSource",            PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "DbSource.php");

        self::registerClass("XMLDB",              PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "class.xmlDb.php");
        self::registerClass("dynaFormHandler",    PATH_GULLIVER . "class.dynaformhandler.php");
        self::registerClass("DynaFormField",      PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "class.dynaFormField.php");
        self::registerClass("BaseDynaform",       PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "om" . PATH_SEP . "BaseDynaform.php");
        self::registerClass("Dynaform",           PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "Dynaform.php");
        self::registerClass("DynaformPeer",       PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "DynaformPeer.php");

        self::registerClass("BaseEvent",          PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "om" . PATH_SEP . "BaseEvent.php");
        self::registerClass("Event",              PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "Event.php");

        self::registerClass("BaseEventPeer",      PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "om" . PATH_SEP . "BaseEventPeer.php");
        self::registerClass("EventPeer",          PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "EventPeer.php");

        self::registerClass("BaseFields",         PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "om" . PATH_SEP . "BaseFields.php");
        self::registerClass("Fields",             PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "Fields.php");

        self::registerClass("BaseGateway",        PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "om" . PATH_SEP . "BaseGateway.php");
        self::registerClass("Gateway",            PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "Gateway.php");

        self::registerClass("BaseGroupUser",      PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "om" . PATH_SEP . "BaseGroupUser.php");
        self::registerClass("Groupwf",            PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "Groupwf.php");
        self::registerClass("GroupUser",          PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "GroupUser.php");

        self::registerClass("BaseGroupUserPeer",  PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "om" . PATH_SEP . "BaseGroupUserPeer.php");
        self::registerClass("GroupUserPeer",      PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "GroupUserPeer.php");

        self::registerClass("BaseGroupwfPeer",    PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "om" . PATH_SEP . "BaseGroupwfPeer.php");
        self::registerClass("GroupwfPeer",        PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "GroupwfPeer.php");

        self::registerClass("BaseInputDocumentPeer",PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "om" . PATH_SEP . "BaseInputDocumentPeer.php");
        self::registerClass("InputDocumentPeer",  PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "InputDocumentPeer.php");

        self::registerClass("BaseIsoCountry",     PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "om" . PATH_SEP . "BaseIsoCountry.php");
        self::registerClass("IsoCountry",         PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "IsoCountry.php");
        self::registerClass("BaseTranslation",    PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "om" . PATH_SEP . "BaseTranslation.php");
        self::registerClass("Translation",        PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "Translation.php");
        self::registerClass("BaseLanguage",       PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "om" . PATH_SEP . "BaseLanguage.php");
        self::registerClass("Language",           PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "Language.php");

        self::registerClass("BaseLogCasesScheduler",PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "om" . PATH_SEP . "BaseLogCasesScheduler.php");
        self::registerClass("LogCasesScheduler",  PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "LogCasesScheduler.php");

        self::registerClass("BaseObjectPermission",PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "om" . PATH_SEP . "BaseObjectPermission.php");
        self::registerClass("ObjectPermission",    PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "ObjectPermission.php");
        self::registerClass("ObjectPermissionPeer",PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "ObjectPermissionPeer.php");

        self::registerClass("BaseOutputDocument",  PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "om" . PATH_SEP . "BaseOutputDocument.php");
        self::registerClass("OutputDocument",      PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "OutputDocument.php");
        self::registerClass("OutputDocumentPeer",  PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "OutputDocumentPeer.php");

        self::registerClass("BaseProcess",         PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "om" . PATH_SEP . "BaseProcess.php");
        self::registerClass("BaseProcessCategory", PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "om" . PATH_SEP . "BaseProcessCategory.php");
        self::registerClass("ProcessCategory",     PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "ProcessCategory.php");
        self::registerClass("ProcessCategoryPeer", PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "ProcessCategoryPeer.php");
        self::registerClass("ProcessPeer",         PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "ProcessPeer.php");
        self::registerClass("Process",             PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "Process.php");

        self::registerClass("BaseProcessUser",     PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "om" . PATH_SEP . "BaseProcessUser.php");
        self::registerClass("ProcessUser",         PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "ProcessUser.php");

        self::registerClass("BaseProcessUserPeer", PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "om" . PATH_SEP . "BaseProcessUserPeer.php");
        self::registerClass("ProcessUserPeer",     PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "ProcessUserPeer.php");

        self::registerClass("BaseReportTable",     PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "om" . PATH_SEP . "BaseReportTable.php");
        self::registerClass("ReportTable",         PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "ReportTable.php");
        self::registerClass("ReportTablePeer",     PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "ReportTablePeer.php");

        self::registerClass("BaseReportVar",       PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "om" . PATH_SEP . "BaseReportVar.php");
        self::registerClass("ReportVar",           PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "ReportVar.php");

        self::registerClass("BaseReportVarPeer",   PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "om" . PATH_SEP . "BaseReportVarPeer.php");
        self::registerClass("ReportVarPeer",       PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "ReportVarPeer.php");

        self::registerClass("BaseRoute",           PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "om" . PATH_SEP . "BaseRoute.php");
        self::registerClass("Route",               PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "Route.php");
        self::registerClass("RoutePeer",           PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "RoutePeer.php");

        self::registerClass("BaseStep",            PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "om" . PATH_SEP . "BaseStep.php");
        self::registerClass("Step",                PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "Step.php");
        self::registerClass("StepPeer",            PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "StepPeer.php");

        self::registerClass("BaseStepSupervisor",  PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "om" . PATH_SEP . "BaseStepSupervisor.php");
        self::registerClass("StepSupervisor",      PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "StepSupervisor.php");

        self::registerClass("BaseStepSupervisorPeer",PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "om" . PATH_SEP . "BaseStepSupervisorPeer.php");
        self::registerClass("StepSupervisorPeer",  PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "StepSupervisorPeer.php");

        self::registerClass("BaseStepTrigger",     PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "om" . PATH_SEP . "BaseStepTrigger.php");
        self::registerClass("StepTrigger",         PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "StepTrigger.php");
        self::registerClass("StepTriggerPeer",     PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "StepTriggerPeer.php");

        self::registerClass("SolrRequestData",     PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "entities" . PATH_SEP . "SolrRequestData.php");

        self::registerClass("SolrUpdateDocument",  PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "entities" . PATH_SEP . "SolrUpdateDocument.php");

        self::registerClass("BaseSwimlanesElements",PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "om" . PATH_SEP . "BaseSwimlanesElements.php");
        self::registerClass("SwimlanesElements",   PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "SwimlanesElements.php");
        self::registerClass("BaseSwimlanesElementsPeer",PATH_HOME ."engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "om" . PATH_SEP . "BaseSwimlanesElementsPeer.php");
        self::registerClass("SwimlanesElementsPeer",PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "SwimlanesElementsPeer.php");

        self::registerClass("BaseSubApplication",  PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "om" . PATH_SEP . "BaseSubApplication.php");
        self::registerClass("SubApplication",      PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "SubApplication.php");
        self::registerClass("SubApplicationPeer",  PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "SubApplicationPeer.php");

        self::registerClass("BaseSubProcess",      PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "om" . PATH_SEP . "BaseSubProcess.php");
        self::registerClass("SubProcess",          PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "SubProcess.php");

        self::registerClass("BaseSubProcessPeer",  PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "om" . PATH_SEP . "BaseSubProcessPeer.php");
        self::registerClass("SubProcessPeer",      PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "SubProcessPeer.php");

        self::registerClass("BaseTask",            PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "om" . PATH_SEP . "BaseTask.php");
        self::registerClass("Task",                PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "Task.php");

        self::registerClass("BaseTaskUser",        PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "om" . PATH_SEP . "BaseTaskUser.php");
        self::registerClass("TaskUserPeer",        PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "TaskUserPeer.php");
        self::registerClass("TaskUser",            PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "TaskUser.php");

        self::registerClass("BaseTriggers",        PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "om" . PATH_SEP . "BaseTriggers.php");
        self::registerClass("Triggers",            PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "Triggers.php");
        self::registerClass("BaseTriggersPeer",    PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "om" . PATH_SEP . "BaseTriggersPeer.php");
        self::registerClass("TriggersPeer",        PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "TriggersPeer.php");

        self::registerClass("BaseUsers",           PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "om" . PATH_SEP . "BaseUsers.php");
        self::registerClass("IsoCountry",          PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "IsoCountry.php");
        self::registerClass("BaseIsoSubdivision",  PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "om" . PATH_SEP . "BaseIsoSubdivision.php");
        self::registerClass("IsoSubdivision",      PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "IsoSubdivision.php");
        self::registerClass("BaseIsoLocation",     PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "om" . PATH_SEP . "BaseIsoLocation.php");
        self::registerClass("IsoLocation",         PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "IsoLocation.php");
        self::registerClass("Users",               PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "Users.php");
        self::registerClass("UsersPeer",           PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "UsersPeer.php");

        self::registerClass("Xml_Node",            PATH_GULLIVER . "class.xmlDocument.php");
    }

    //below this line, still not approved methods

	/**
	 * mk_dir , copied from class.G.php
	 *
	 * @return void
	 */
	public function mk_dir($strPath, $rights = 0777) {
		$folder_path = array ( $strPath	);
		$oldumask = umask ( 0 );
		while ( ! @is_dir ( dirname ( end ( $folder_path ) ) ) && dirname ( end ( $folder_path ) ) != '/' && dirname ( end ( $folder_path ) ) != '.' && dirname ( end ( $folder_path ) ) != '' ) {
			array_push ( $folder_path, dirname ( end ( $folder_path ) ) );
			// var_dump($folder_path);
			// die;
		}

		while ( $parent_folder_path = array_pop ( $folder_path ) ) {
			if (! @is_dir ( $parent_folder_path )) {
				if (! @mkdir ( $parent_folder_path, $rights )) {
					// trigger_error ("Can't create folder
					// \"$parent_folder_path\".", E_USER_WARNING);
					umask ( $oldumask );
				}
			}
		}
	}

	/**
	 * verify if all files & directories passed by param.
	 * are writable
	 *
	 * @author Erik Amaru Ortiz <erik@colosa.com>
	 * @param $resources array
	 *        	a list of files to verify write access
	 */
	public function verifyWriteAccess($resources) {
		$noWritable = array ();
		foreach ( $resources as $i => $resource ) {
			if (! is_writable ( $resource )) {
				$noWritable [] = $resource;
			}
		}

		if (count ( $noWritable ) > 0) {
			$e = new Exception ( "Write access not allowed for ProcessMaker resources" );
			$e->files = $noWritable;
			throw $e;
		}
	}

	/**
	 * render a smarty template
	 *
	 * @author Erik Amaru Ortiz <erik@colosa.com>
	 * @param $template string
	 *        	containing the template filename on /gulliver/templates/
	 *        	directory
	 * @param $data associative
	 *        	array containig the template data
	 */
	public function renderTemplate($template, $data = array()) {
		if (! defined ( 'PATH_THIRDPARTY' )) {
			throw new Exception ( 'System constant (PATH_THIRDPARTY) is not defined!' );
		}

		require_once PATH_THIRDPARTY . 'smarty/libs/Smarty.class.php';
		$fInfo = pathinfo ( $template );

		$tplExists = true;

		// file has absolute path
		if (substr ( $template, 0, 1 ) != PATH_SEP) {
			$template = PATH_TEMPLATE . $template;
		}

		// fix for template that have dot in its name but is not a valid
		// extension
		if (isset ( $fInfo ['extension'] ) && ($fInfo ['extension'] != 'tpl' || $fInfo ['extension'] != 'html')) {
			unset ( $fInfo ['extension'] );
		}

		if (! isset ( $fInfo ['extension'] )) {
			if (file_exists ( $template . '.tpl' )) {
				$template .= '.tpl';
			} elseif (file_exists ( $template . '.html' )) {
				$template .= '.html';
			} else {
				$tplExists = false;
			}
		} else {
			if (! file_exists ( $template )) {
				$tplExists = false;
			}
		}

		if (! $tplExists) {
			throw new Exception ( "Template: $template, doesn't exist!" );
		}

		$smarty = new Smarty ();
		$smarty->compile_dir = Bootstrap::sys_get_temp_dir ();
		$smarty->cache_dir = Bootstrap::sys_get_temp_dir ();
		$smarty->config_dir = PATH_THIRDPARTY . 'smarty/configs';

		$smarty->template_dir = PATH_TEMPLATE;
		$smarty->force_compile = true;

		foreach ( $data as $key => $value ) {
			$smarty->assign ( $key, $value );
		}

		$smarty->display ( $template );
	}

	/**
	 * Load Gulliver Classes
	 *
	 * @author Fernando Ontiveros Lira <fernando@colosa.com>
	 * @access public
	 * @param string $strClass
	 * @return void
	 */
	public function LoadSystem($strClass) {
		require_once (PATH_GULLIVER . 'class.' . $strClass . '.php');
	}

	/**
	 * Get the temporal directory path on differents O.S.
	 * i.e. /temp -> linux, C:/Temp -> win
	 *
	 * @author <erik@colosa.com>
	 */
	public function sys_get_temp_dir() {
		if (! function_exists ( 'sys_get_temp_dir' )) {
			// Based on http://www.phpit.net/
			// article/creating-zip-tar-archives-dynamically-php/2/
			// Try to get from environment variable
			if (! empty ( $_ENV ['TMP'] )) {
				return realpath ( $_ENV ['TMP'] );
			} elseif (! empty ( $_ENV ['TMPDIR'] )) {
				return realpath ( $_ENV ['TMPDIR'] );
			} elseif (! empty ( $_ENV ['TEMP'] )) {
				return realpath ( $_ENV ['TEMP'] );
			} else {
				// Detect by creating a temporary file
				// Try to use system's temporary directory as random name
				// shouldn't exist
				$temp_file = tempnam ( md5 ( uniqid ( rand (), true ) ), '' );
				if ($temp_file) {
					$temp_dir = realpath ( dirname ( $temp_file ) );
					unlink ( $temp_file );
					return $temp_dir;
				} else {
					return false;
				}
			}
		} else {
			return sys_get_temp_dir ();
		}
	}

	/**
	 * Transform a public URL into a local path.
	 *
	 * @author David S. Callizaya S. <davidsantos@colosa.com>
	 * @access public
	 * @param string $url
	 * @param string $corvertionTable
	 * @param string $realPath
	 *        	= local path
	 * @return boolean
	 */
    public function virtualURI ($url, $convertionTable, &$realPath)
    {
        foreach ($convertionTable as $urlPattern => $localPath) {
            //      $urlPattern = addcslashes( $urlPattern , '/');
            $urlPattern = addcslashes( $urlPattern, './' );
            $urlPattern = '/^' . str_replace( array ('*','?'
            ), array ('.*','.?'
            ), $urlPattern ) . '$/';
            if (preg_match( $urlPattern, $url, $match )) {
                if ($localPath === false) {
                    $realPath = $url;
                    return false;
                }
                if ($localPath != 'jsMethod') {
                    $realPath = $localPath . $match[1];
                } else {
                    $realPath = $localPath;
                }
                return true;
            }
        }
        $realPath = $url;
        return false;
    }

	/**
	 * streaming a file
	 *
	 * @author Fernando Ontiveros Lira <fernando@colosa.com>
	 * @access public
	 * @param string $file
	 * @param boolean $download
	 * @param string $downloadFileName
	 * @return string
	 */
    public function streamFile ($file, $download = false, $downloadFileName = '')
    {
        $folderarray = explode( '/', $file );
        $typearray = explode( '.', basename( $file ) );
        $typefile = $typearray[count( $typearray ) - 1];
        $filename = $file;

        //trick to generate the translation.language.js file , merging two files
        if (strtolower( $typefile ) == 'js' && $typearray[0] == 'translation') {
            Bootstrap::sendHeaders( $filename, 'text/javascript', $download, $downloadFileName );
            $output = Bootstrap::streamJSTranslationFile( $filename, $typearray[1] );
            echo $output;
            return;
        }

        //trick to generate the big css file for ext style .
        if (strtolower( $typefile ) == 'css' && $folderarray[count( $folderarray ) - 2] == 'css') {
            Bootstrap::sendHeaders( $filename, 'text/css', $download, $downloadFileName );
            $output = Bootstrap::streamCSSBigFile( $typearray[0] );
            echo $output;
            return;
        }

        if (file_exists( $filename )) {
            switch (strtolower( $typefile )) {
                case 'swf':
                    Bootstrap::sendHeaders( $filename, 'application/x-shockwave-flash', $download, $downloadFileName );
                    break;
                case 'js':
                    Bootstrap::sendHeaders( $filename, 'text/javascript', $download, $downloadFileName );
                    break;
                case 'htm':
                case 'html':
                    Bootstrap::sendHeaders( $filename, 'text/html', $download, $downloadFileName );
                    break;
                case 'htc':
                    Bootstrap::sendHeaders( $filename, 'text/plain', $download, $downloadFileName );
                    break;
                case 'json':
                    Bootstrap::sendHeaders( $filename, 'text/plain', $download, $downloadFileName );
                    break;
                case 'gif':
                    Bootstrap::sendHeaders( $filename, 'image/gif', $download, $downloadFileName );
                    break;
                case 'png':
                    Bootstrap::sendHeaders( $filename, 'image/png', $download, $downloadFileName );
                    break;
                case 'jpg':
                    Bootstrap::sendHeaders( $filename, 'image/jpg', $download, $downloadFileName );
                    break;
                case 'css':
                    Bootstrap::sendHeaders( $filename, 'text/css', $download, $downloadFileName );
                    break;
                case 'xml':
                    Bootstrap::sendHeaders( $filename, 'text/xml', $download, $downloadFileName );
                    break;
                case 'txt':
                    Bootstrap::sendHeaders( $filename, 'text/html', $download, $downloadFileName );
                    break;
                case 'doc':
                case 'pdf':
                case 'pm':
                case 'po':
                    Bootstrap::sendHeaders( $filename, 'application/octet-stream', $download, $downloadFileName );
                    break;
                case 'php':
                    if ($download) {
                        Bootstrap::sendHeaders( $filename, 'text/plain', $download, $downloadFileName );
                    } else {
                        require_once ($filename);
                        return;
                    }
                    break;
                case 'tar':
                    Bootstrap::sendHeaders( $filename, 'application/x-tar', $download, $downloadFileName );
                    break;
                default:
                    //throw new Exception ( "Unknown type of file '$file'. " );
                    Bootstrap::sendHeaders( $filename, 'application/octet-stream', $download, $downloadFileName );
                    break;
            }
        } else {
            if (strpos( $file, 'gulliver' ) !== false) {
                list ($path, $filename) = explode( 'gulliver', $file );
            }

            $_SESSION['phpFileNotFound'] = $file;
            Bootstrap::header( "location: /errors/error404.php?l=" . $_SERVER['REQUEST_URI'] );
        }

        if ( substr($filename,-10) == "ext-all.js" ) {
            $filename = PATH_GULLIVER_HOME . 'js/ext/min/ext-all.js';
        }
        @readfile( $filename );
    }

	/**
	 * Parsing the URI
	 *
	 * @author Fernando Ontiveros Lira <fernando@colosa.com>
	 * @access public
	 * @param string $urlLink
	 * @return string
	 */
	static public function parseURI($uri, $isRestRequest = false) {
		// *** process the $_POST with magic_quotes enabled
		// The magic_quotes_gpc feature has been DEPRECATED as of PHP 5.3.0.
		if (get_magic_quotes_gpc () === 1) {
			$_POST = g::strip_slashes ( $_POST );
		}

		$aRequestUri = explode ( '/', $uri );
		if ($isRestRequest) {
			$args = self::parseRestUri ( $aRequestUri );
		} else {
			$args = self::parseNormalUri ( $aRequestUri );
		}

		define ( "SYS_LANG", $args ['SYS_LANG'] );
        define('SYS_SKIN', $args ['SYS_SKIN']);
		define ( 'SYS_COLLECTION', $args ['SYS_COLLECTION'] );
		define ( 'SYS_TARGET', $args ['SYS_TARGET'] );

		if ($args ['SYS_COLLECTION'] == 'js2') {
			print "ERROR";
			die ();
		}
	}

	/**
	 * isPMUnderUpdating, Used to set a file flag to check if PM is upgrading.
	 *
	 * @setFlag Contains the flag to set or unset the temporary file:
	 * 0 to delete the temporary file flag
	 * 1 to set the temporary file flag.
	 * 2 or bigger to check if the temporary file exists.
	 * return true if the file exists, otherwise false.
	 */
	public function isPMUnderUpdating($setFlag = 2) {
        if (!defined('PATH_DATA')) {
           return false;
        }

		$fileCheck = PATH_DATA . "UPDATE.dat";
		if ($setFlag == 0) {
			if (file_exists ( $fileCheck )) {
				unlink ( $fileCheck );
			}
		} elseif ($setFlag == 1) {
			$fp = fopen ( $fileCheck, 'w' );
			$line = fputs ( $fp, "true" );
		}
		// checking temporary file
		if ($setFlag >= 1) {
			if (file_exists ( $fileCheck )) {
				return true;
			}
		}
		return false;
	}

	/**
	 * parse a smarty template and return teh result as string
	 *
	 * @author Erik Amaru Ortiz <erik@colosa.com>
	 * @param $template string
	 *        	containing the template filename on /gulliver/templates/
	 *        	directory
	 * @param $data associative
	 *        	array containig the template data
	 * @return $content string containing the parsed template content
	 */
	public function parseTemplate($template, $data = array()) {
		$content = '';

		ob_start ();
		g::renderTemplate ( $template, $data );
		$content = ob_get_contents ();
		ob_get_clean ();

		return $content;
	}

	/**
	 * If the class is not defined by the aplication, it
	 * attempt to load the class from gulliver.system
	 *
	 * @author Fernando Ontiveros Lira <fernando@colosa.com>, David S. Callizaya
	 * @access public
	 * @param string $strClass
	 * @return void
	 */
	public function LoadClass($strClass) {
		$classfile = Bootstrap::ExpandPath ( "classes" ) . 'class.' . $strClass . '.php';
		if (! file_exists ( $classfile )) {
			if (file_exists ( PATH_GULLIVER . 'class.' . $strClass . '.php' )) {
				return require_once (PATH_GULLIVER . 'class.' . $strClass . '.php');
			} else {
				return false;
			}
		} else {
			return require_once ($classfile);
		}
	}

	/**
	 * Loads a Class.
	 * If the class is not defined by the aplication, it
	 * attempt to load the class from gulliver.system
	 *
	 * @author Fernando Ontiveros Lira <fernando@colosa.com>, David S. Callizaya
	 * @access public
	 * @param string $strClass
	 * @return void
	 */
	public function LoadThirdParty($sPath, $sFile) {
		$classfile = PATH_THIRDPARTY . $sPath . '/' . $sFile . ((substr ( $sFile, 0, - 4 ) !== '.php') ? '.php' : '');
		return require_once ($classfile);
	}

	/**
	 * Function LoadTranslationObject
	 * It generates a global Translation variable that will be used in all the
	 * system.
	 * Per script
	 *
	 * @author Hugo Loza. <hugo@colosa.com>
	 * @access public
	 * @param  string lang
	 * @return void
	 */
	public function LoadTranslationObject($lang = SYS_LANG) {
		$defaultTranslations = Array ();
		$foreignTranslations = Array ();

		// if the default translations table doesn't exist we can't proceed
		if (! is_file ( PATH_LANGUAGECONT . 'translation.en' )) {
			return null;
		}
		// load the translations table
		require_once (PATH_LANGUAGECONT . 'translation.en');
		$defaultTranslations = $translation;

		// if some foreign language was requested and its translation file
		// exists
		if ($lang != 'en' && file_exists ( PATH_LANGUAGECONT . 'translation.' . $lang )) {
			require_once (PATH_LANGUAGECONT . 'translation.' . $lang); // load the foreign translations table
			$foreignTranslations = $translation;
		}

		global $translation;
		if (defined ( "SHOW_UNTRANSLATED_AS_TAG" ) && SHOW_UNTRANSLATED_AS_TAG != 0) {
			$translation = $foreignTranslations;
		} else {
			$translation = array_merge ( $defaultTranslations, $foreignTranslations );
		}
		return true;
	}

	/**
	 * Render Page
	 *
	 * @author Fernando Ontiveros Lira <fernando@colosa.com>
	 * @access public
	 * @param object $objContent
	 * @param string $strTemplate
	 * @param string $strSkin
	 * @return void
	 */
	public function RenderPage($strTemplate = "default", $strSkin = SYS_SKIN, $objContent = null, $layout = '') {
		global $G_CONTENT;
		global $G_TEMPLATE;
	    global $G_SKIN;
	    global $G_PUBLISH;

	    $G_CONTENT = $objContent;
	    $G_TEMPLATE = $strTemplate;
	    $G_SKIN = $strSkin;

	    try {
	 	   $file = Bootstrap::ExpandPath ( 'skinEngine' ) . 'skinEngine.php';
	 	   include $file;
	 	   $skinEngine = new SkinEngine ( $G_TEMPLATE, $G_SKIN, $G_CONTENT );
	 	   $skinEngine->setLayout ( $layout );
	 	   $skinEngine->dispatch ();
	    } catch ( Exception $e ) {
	 	   global $G_PUBLISH;
	 	   if (is_null ( $G_PUBLISH )) {
	 		   $G_PUBLISH = new Publisher ();
	 	   }
	 	   if (count ( $G_PUBLISH->Parts ) == 1) {
	 		   array_shift ( $G_PUBLISH->Parts );
	 	   }
    	 	global $oHeadPublisher;
    	 	$leimnudInitString = $oHeadPublisher->leimnudInitString;
    	 	$oHeadPublisher->clearScripts ();
    	 	$oHeadPublisher->leimnudInitString = $leimnudInitString;
    	 	$oHeadPublisher->addScriptFile ( '/js/maborak/core/maborak.js' );
    	 	$G_PUBLISH->AddContent ( 'xmlform', 'xmlform', 'login/showMessage', null, array (
    	 			'MESSAGE' => $e->getMessage ()
    	 	) );
    	 	if (class_exists ( 'SkinEngine' )) {
    	 		$skinEngine = new SkinEngine ( 'publish', 'blank', '' );
    	 		$skinEngine->dispatch ();
    	 	} else {
    	 		die ( $e->getMessage () );
    	 	}
	   }
	}

	/**
	 * SendTemporalMessage
	 *
	 * @param string $msgID
	 * @param string $strType
	 * @param string $sType
	 *        	default value 'LABEL'
	 * @param date $time
	 *        	default value null
	 * @param integer $width
	 *        	default value null
	 * @param string $customLabels
	 *        	default value null
	 *
	 * @return void
	 */
	public function SendTemporalMessage($msgID, $strType, $sType = 'LABEL', $time = null, $width = null, $customLabels = null) {
	 if (isset ( $width )) {
	 	$_SESSION ['G_MESSAGE_WIDTH'] = $width;
	 }
	 if (isset ( $time )) {
	 	$_SESSION ['G_MESSAGE_TIME'] = $time;
	 }
	 switch (strtolower ( $sType )) {
	 	case 'label' :
	 	case 'labels' :
	 		$_SESSION ['G_MESSAGE_TYPE'] = $strType;
	 		$_SESSION ['G_MESSAGE'] = nl2br ( Bootstrap::LoadTranslation ( $msgID ) );
	 		break;
	 	case 'string' :
	 		$_SESSION ['G_MESSAGE_TYPE'] = $strType;
	 		$_SESSION ['G_MESSAGE'] = nl2br ( $msgID );
	 		break;
	 }
	 if ($customLabels != null) {
	 	$message = $_SESSION ['G_MESSAGE'];
	 	foreach ( $customLabels as $key => $val ) {
	 		$message = str_replace ( '{' . nl2br ( $key ) . '}', nl2br ( $val ), $message );
	 	}
	 	$_SESSION ['G_MESSAGE'] = $message;
		}
	}

	/**
		* Redirect URL
		*
		* @author Fernando Ontiveros Lira <fernando@colosa.com>
		* @access public
		* @param string $parameter
		* @return string
		*/
	public function header($parameter) {
		if (defined ( 'ENABLE_ENCRYPT' ) && (ENABLE_ENCRYPT == 'yes') && (substr ( $parameter, 0, 9 ) == 'location:')) {
			$url = Bootstrap::encrypt ( substr ( $parameter, 10 ), URL_KEY );
			header ( 'location:' . $url );
		} else {
			header ( $parameter );
		}
		return;
	}

	/**
		* Include all model plugin files
		*
		* LoadAllPluginModelClasses
		*
		* @author Hugo Loza <hugo@colosa.com>
		* @access public
		* @return void
		*/
	public function LoadAllPluginModelClasses() {
		// Get the current Include path, where the plugins directories should be
		if (! defined ( 'PATH_SEPARATOR' )) {
			define ( 'PATH_SEPARATOR', (substr ( PHP_OS, 0, 3 ) == 'WIN') ? ';' : ':' );
		}
		$path = explode ( PATH_SEPARATOR, get_include_path () );

		foreach ( $path as $possiblePath ) {
			if (strstr ( $possiblePath, "plugins" )) {
				$baseDir = $possiblePath . 'classes' . PATH_SEP . 'model';
			 if (file_exists ( $baseDir )) {
			 	if ($handle = opendir ( $baseDir )) {
			 		while ( false !== ($file = readdir ( $handle )) ) {
			 			if (strpos ( $file, '.php', 1 ) && ! strpos ( $file, 'Peer.php', 1 )) {
			 				require_once ($baseDir . PATH_SEP . $file);
			 			}
			 		}
			 	}
			 	// Include also the extendGulliverClass that could have some
			 	// new definitions for fields
			 	if (file_exists ( $possiblePath . 'classes' . PATH_SEP . 'class.extendGulliver.php' )) {
			 		include_once $possiblePath . 'classes' . PATH_SEP . 'class.extendGulliver.php';
			 	}
			 }
			}
		}
	}

	/**
	 * Expand the path using the path constants
	 *
	 * @author Fernando Ontiveros Lira <fernando@colosa.com>
	 * @access public
	 * @param string $strPath
	 * @return string
	 */
	public function expandPath($strPath = '') {
		$res = "";
		$res = PATH_CORE;
		if ($strPath != "") {
			$res .= $strPath . "/";
		}
		return $res;
	}

	/**
	 * This method allow dispatch rest services using 'Restler' thirdparty library
	 *
	 * @author Erik Amaru Ortiz <aortiz.erik@gmail.com>
	 */
	public function dispatchRestService ($uri, $config, $apiClassesPath = '')
	{
		require_once 'restler/restler.php';

		$rest = new Restler();
		$rest->setSupportedFormats( 'JsonFormat', 'XmlFormat' );
		// getting all services class
		$restClasses = array ();
		$restClassesList = Bootstrap::rglob( '*', 0, PATH_CORE . 'services/' );
		foreach ($restClassesList as $classFile) {
			if (substr( $classFile, - 4 ) === '.php') {
			 $restClasses[str_replace( '.php', '', basename( $classFile ) )] = $classFile;
			}
		}
		if (! empty( $apiClassesPath )) {
			$pluginRestClasses = array ();
			$restClassesList = Bootstrap::rglob( '*', 0, $apiClassesPath . 'services/' );
			foreach ($restClassesList as $classFile) {
			 if (substr( $classFile, - 4 ) === '.php') {
			 	$pluginRestClasses[str_replace( '.php', '', basename( $classFile ) )] = $classFile;
			 }
			}
			$restClasses = array_merge( $restClasses, $pluginRestClasses );
		}
		// hook to get rest api classes from plugins
		if (class_exists( 'PMPluginRegistry' )) {
			$pluginRegistry = & PMPluginRegistry::getSingleton();
			$pluginClasses = $pluginRegistry->getRegisteredRestClassFiles();
			$restClasses = array_merge( $restClasses, $pluginClasses );
		}
		foreach ($restClasses as $key => $classFile) {
			if (! file_exists( $classFile )) {
				unset( $restClasses[$key] );
				continue;
			}
			//load the file, and check if exist the class inside it.
			require_once $classFile;
			$namespace = 'Services_Rest_';
			$className = str_replace( '.php', '', basename( $classFile ) );

			// if the core class does not exists try resolve the for a plugin
			if (! class_exists( $namespace . $className )) {
				$namespace = 'Plugin_Services_Rest_';
				// Couldn't resolve the class name, just skipp it
				if (! class_exists( $namespace . $className )) {
					unset( $restClasses[$key] );
					continue;
				}
			}
			// verify if there is an auth class implementing 'iAuthenticate'
			$classNameAuth = $namespace . $className;
			$reflClass = new ReflectionClass( $classNameAuth );
			// that wasn't from plugin
			if ($reflClass->implementsInterface( 'iAuthenticate' ) && $namespace != 'Plugin_Services_Rest_') {
				// auth class found, set as restler authentication class handler
				$rest->addAuthenticationClass( $classNameAuth );
			} else {
				// add api class
				$rest->addAPIClass( $classNameAuth );
			}
		}
		//end foreach rest class
		// resolving the class for current request
		$uriPart = explode( '/', $uri );
		$requestedClass = '';
		if (isset( $uriPart[1] )) {
			$requestedClass = ucfirst( $uriPart[1] );
		}
		if (class_exists( 'Services_Rest_' . $requestedClass )) {
			$namespace = 'Services_Rest_';
		} elseif (class_exists( 'Plugin_Services_Rest_' . $requestedClass )) {
			$namespace = 'Plugin_Services_Rest_';
		} else {
			$namespace = '';
		}
		// end resolv.
		// Send additional headers (if exists) configured on rest-config.ini
		if (array_key_exists( 'HEADERS', $config )) {
			foreach ($config['HEADERS'] as $name => $value) {
			 header( "$name: $value" );
			}
		}
		// to handle a request with "OPTIONS" method
		if (! empty( $namespace ) && $_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
			$reflClass = new ReflectionClass( $namespace . $requestedClass );
			// if the rest class has not a "options" method
			if (! $reflClass->hasMethod( 'options' )) {
				header( 'Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS, HEADERS' );
				header( 'Access-Control-Allow-Headers: authorization, content-type' );
				header( "Access-Control-Allow-Credentials", "false" );
				header( 'Access-Control-Max-Age: 60' );
				exit();
			}
		}
		// override global REQUEST_URI to pass to Restler library
		$_SERVER['REQUEST_URI'] = '/' . strtolower( $namespace ) . ltrim( $uri, '/' );
		// handle the rest request
		$rest->handle();
	}

	/**
		* function to calculate the time used to render a page
		*/
	public function logTimeByPage() {
		if (! defined ( PATH_DATA )) {
			return false;
		}

		$serverAddr = $_SERVER ['SERVER_ADDR'];
		global $startingTime;
		$endTime = microtime ( true );
		$time = $endTime - $startingTime;
		$fpt = fopen ( PATH_DATA . 'log/time.log', 'a' );
		fwrite ( $fpt, sprintf ( "%s.%03d %15s %s %5.3f %s\n", date ( 'Y-m-d H:i:s' ), $time, getenv ( 'REMOTE_ADDR' ), substr ( $serverAddr, - 4 ), $time, $_SERVER ['REQUEST_URI'] ) );
		fclose ( $fpt );
	}

	/**
	 * streaming a big JS file with small js files
	 *
	 * @author Fernando Ontiveros Lira <fernando@colosa.com>
	 * @access public
	 * @param string $file
	 * @param boolean $download
	 * @param string $downloadFileName
	 * @return string
	 */
	public function streamJSTranslationFile($filename, $locale = 'en') {
        $defaultTranslations = Array ();
        $foreignTranslations = Array ();

        //if the default translations table doesn't exist we can't proceed
        if (! is_file( PATH_LANGUAGECONT . 'translation.en' )) {
            return ;
        }
        //load the translations table
        require_once (PATH_LANGUAGECONT . 'translation.en');
        $defaultTranslations = $translation;

        //if some foreign language was requested and its translation file exists
        if ($locale != 'en' && file_exists( PATH_LANGUAGECONT . 'translation.' . $locale )) {
            require_once (PATH_LANGUAGECONT . 'translation.' . $locale); //load the foreign translations table
            $foreignTranslations = $translation;
        }

        if (defined( "SHOW_UNTRANSLATED_AS_TAG" ) && SHOW_UNTRANSLATED_AS_TAG != 0) {
            $translation = $foreignTranslations;
        } else {
            $translation = array_merge( $defaultTranslations, $foreignTranslations );
        }

        $calendarJs = '';
        $calendarJsFile = PATH_GULLIVER_HOME . "js/widgets/js-calendar/lang/" . $locale .".js";
        if (! file_exists($calendarJsFile)) {
            $calendarJsFile = PATH_GULLIVER_HOME . "js/widgets/js-calendar/lang/en.js";
        }
        $calendarJs = file_get_contents($calendarJsFile) . "\n";

        return $calendarJs . 'var TRANSLATIONS = ' . Bootstrap::json_encode( $translation ) . ';' ;
	}

	/**
	 * streaming a big JS file with small js files
	 *
	 * @author Fernando Ontiveros Lira <fernando@colosa.com>
	 * @access public
	 * @param string $file
	 * @return string
	 */
	public function streamCSSBigFile ($filename)
	{
		header( 'Content-Type: text/css' );

		//First get Skin info
		$filenameParts = explode( "-", $filename );
		$skinName = $filenameParts[0];
		$skinVariant = "skin";

		if (isset( $filenameParts[1] )) {
			$skinVariant = strtolower( $filenameParts[1] );
		}

		$configurationFile = '';
		if ($skinName == "jscolors") {
			$skinName = "classic";
		}
		if ($skinName == "xmlcolors") {
			$skinName = "classic";
		}
		if ($skinName == "classic") {
			$configurationFile = Bootstrap::ExpandPath( "skinEngine" ) . 'base' . PATH_SEP . 'config.xml';
		} else {
			$configurationFile = PATH_CUSTOM_SKINS . $skinName . PATH_SEP . 'config.xml';

			if (! is_file( $configurationFile )) {
				$configurationFile = Bootstrap::ExpandPath( "skinEngine" ) . $skinName . PATH_SEP . 'config.xml';
			}
		}

		$mtime = date ( 'U' );
		$gmt_mtime = gmdate( "D, d M Y H:i:s", $mtime ) . " GMT";
		header( 'Pragma: cache' );
		header( 'ETag: "' . md5( $mtime . $filename ) . '"' );
		header( "Last-Modified: " . $gmt_mtime );
		header( 'Cache-Control: public' );
		header( "Expires: " . gmdate( "D, d M Y H:i:s", time() + 30 * 60 * 60 * 24 ) . " GMT" ); //1 month
		//header("Expires: " . gmdate("D, d M Y H:i:s", time () + 60*60*24 ) . " GMT"); //1 day - tempor
		if (isset( $_SERVER['HTTP_IF_MODIFIED_SINCE'] )) {
			if ($_SERVER['HTTP_IF_MODIFIED_SINCE'] == $gmt_mtime) {
				header( 'HTTP/1.1 304 Not Modified' );
				exit();
			}
		}

		if (isset( $_SERVER['HTTP_IF_NONE_MATCH'] )) {
			if (str_replace( '"', '', stripslashes( $_SERVER['HTTP_IF_NONE_MATCH'] ) ) == md5( $mtime . $filename )) {
				header( "HTTP/1.1 304 Not Modified" );
				exit();
			}
		}

		//Read Configuration File
		$xmlConfiguration    = file_get_contents( $configurationFile );
		$xmlConfigurationObj = Bootstrap::xmlParser( $xmlConfiguration );
		$baseSkinDirectory   = dirname( $configurationFile );
		$directorySize       = Bootstrap::getDirectorySize( $baseSkinDirectory );
		$mtime = $directorySize['maxmtime'];

		//if userAgent (BROWSER) is MSIE we need special headers to avoid MSIE behaivor.
		//$userAgent = strtolower($_SERVER['HTTP_USER_AGENT']);

		$outputHeader = "/* Autogenerated CSS file by gulliver framework \n";
		$outputHeader .= "   Skin: $filename\n";
		$outputHeader .= "   Configuration: $configurationFile\n";
		$mtimeNow = date( 'U' );
		$gmt_mtimeNow = gmdate( "D, d M Y H:i:s", $mtimeNow ) . " GMT";
		$outputHeader .= "   Date: $gmt_mtimeNow*/\n";
		$output = "";

		//Base files
		switch (strtolower( $skinVariant )) {
			case "extjs":
				//Base
				$baseCSSPath = PATH_SKIN_ENGINE . "base" . PATH_SEP . "baseCss" . PATH_SEP;
				$output .= file_get_contents( $baseCSSPath . 'ext-all-notheme.css' );
				//$output .= file_get_contents ( $publicExtPath . 'ext-all.css' );

				//Classic Skin
				$extJsSkin = 'xtheme-gray';

				break;
			default:
				break;
		}

		//Get Browser Info
		$infoBrowser = Bootstrap::get_current_browser();
		$browserName = $infoBrowser['browser_working'];
		if (isset( $infoBrowser[$browserName . '_data'] )) {
			if ($infoBrowser[$browserName . '_data'][0] != "") {
				$browserName = $infoBrowser[$browserName . '_data'][0];
			}
		}

		//Read Configuration File
		$xmlConfiguration = file_get_contents ( $configurationFile );
		$xmlConfigurationObj = Bootstrap::xmlParser($xmlConfiguration);

		$skinFilesArray=$xmlConfigurationObj->result['skinConfiguration']['__CONTENT__']['cssFiles']['__CONTENT__'][$skinVariant]['__CONTENT__']['cssFile'] ;
		foreach ($skinFilesArray as $keyFile => $cssFileInfo) {
			$enabledBrowsers=explode(",",$cssFileInfo['__ATTRIBUTES__']['enabledBrowsers']);
			$disabledBrowsers=explode(",",$cssFileInfo['__ATTRIBUTES__']['disabledBrowsers']);

			if (((in_array($browserName, $enabledBrowsers))||(in_array('ALL', $enabledBrowsers)))&&(!(in_array($browserName, $disabledBrowsers)))) {
				if ($cssFileInfo['__ATTRIBUTES__']['file'] == 'rtl.css') {
					Bootstrap::LoadClass('serverConfiguration');
					$oServerConf =& serverConf::getSingleton();
					if (!(defined('SYS_LANG'))) {
						if (isset($_SERVER['HTTP_REFERER'])) {
							$syss = explode('://', $_SERVER['HTTP_REFERER']);
							$sysObjets =  explode('/', $syss['1']);
							$sysLang = $sysObjets['2'];
						} else {
							$sysLang = 'en';
						}
					} else {
						$sysLang = SYS_LANG;
					}
					if ($oServerConf->isRtl($sysLang)) {
						$output .= file_get_contents ( $baseSkinDirectory . PATH_SEP.'css'.PATH_SEP.$cssFileInfo['__ATTRIBUTES__']['file'] );
					}
				} else {
					$output .= file_get_contents ( $baseSkinDirectory . PATH_SEP.'css'.PATH_SEP.$cssFileInfo['__ATTRIBUTES__']['file'] );
				}
			}
		}

		//Remove comments..
		$regex = array ("`^([\t\s]+)`ism" => '',"`^\/\*(.+?)\*\/`ism" => "","`([\n\A;]+)\/\*(.+?)\*\/`ism" => "$1","`([\n\A;\s]+)//(.+?)[\n\r]`ism" => "$1\n","`(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+`ism" => "\n" );
		$output = preg_replace( array_keys( $regex ), $regex, $output );
		$output = $outputHeader . $output;

		return $output;
	}

	/**
	 * sendHeaders
	 *
	 * @param string $filename
	 * @param string $contentType
	 *        	default value ''
	 * @param boolean $download
	 *        	default value false
	 * @param string $downloadFileName
	 *        	default value ''
	 *
	 * @return void
	 */
	public function sendHeaders($filename, $contentType = '', $download = false, $downloadFileName = '') {
		if ($download) {
			if ($downloadFileName == '') {
				$aAux = explode ( '/', $filename );
				$downloadFileName = $aAux [count ( $aAux ) - 1];
			}
			header ( 'Content-Disposition: attachment; filename="' . $downloadFileName . '"' );
		}
		header ( 'Content-Type: ' . $contentType );

		// if userAgent (BROWSER) is MSIE we need special headers to avoid MSIE
		// behaivor.
		$userAgent = strtolower ( $_SERVER ['HTTP_USER_AGENT'] );
		if (preg_match ( "/msie/i", $userAgent )) {
			// if ( ereg("msie", $userAgent)) {
			header ( 'Pragma: cache' );

		    if (file_exists ( $filename )) {
				$mtime = filemtime ( $filename );
			} else {
				$mtime = date ( 'U' );
			}
			$gmt_mtime = gmdate ( "D, d M Y H:i:s", $mtime ) . " GMT";
			header ( 'ETag: "' . md5 ( $mtime . $filename ) . '"' );
			header ( "Last-Modified: " . $gmt_mtime );
			header ( 'Cache-Control: public' );
			header ( "Expires: " . gmdate ( "D, d M Y H:i:s", time () + 60 * 10 ) . " GMT" ); // ten
			// minutes
			return;
		}

		if (! $download) {

			header ( 'Pragma: cache' );

			if (file_exists ( $filename )) {
				$mtime = filemtime ( $filename );
			} else {
				$mtime = date ( 'U' );
			}
			$gmt_mtime = gmdate ( "D, d M Y H:i:s", $mtime ) . " GMT";
			header ( 'ETag: "' . md5 ( $mtime . $filename ) . '"' );
			header ( "Last-Modified: " . $gmt_mtime );
			header ( 'Cache-Control: public' );
			header ( "Expires: " . gmdate ( "D, d M Y H:i:s", time () + 90 * 60 * 60 * 24 ) . " GMT" );
			if (isset ( $_SERVER ['HTTP_IF_MODIFIED_SINCE'] )) {
				if ($_SERVER ['HTTP_IF_MODIFIED_SINCE'] == $gmt_mtime) {
					header ( 'HTTP/1.1 304 Not Modified' );
					exit ();
				}
			}

			if (isset ( $_SERVER ['HTTP_IF_NONE_MATCH'] )) {
				if (str_replace ( '"', '', stripslashes ( $_SERVER ['HTTP_IF_NONE_MATCH'] ) ) == md5 ( $mtime . $filename )) {
					header ( "HTTP/1.1 304 Not Modified" );
					exit ();
				}
			}
		}
	}

	/**
	 * Get checksum from multiple files
	 *
	 * @author erik amaru ortiz <erik@colosa.com>
	 */
	public function getCheckSum ($files)
	{
		Bootstrap::LoadClass( 'system' );
		$key = System::getVersion();

		if (! is_array( $files )) {
			$tmp = $files;
			$files = array ();
			$files[0] = $tmp;
		}

		$checkSum = '';
		foreach ($files as $file) {
			if (is_file( $file )) {
				$checkSum .= md5_file( $file );
			}
		}
		return md5( $checkSum . $key );
	}

	/**
	 * Get checksum from multiple files
	 *
	 * @author erik amaru ortiz <erik@colosa.com>
	 */
	public function getCacheFileNameByPattern ($path, $pattern)
	{
		if ($file = glob( $path . $pattern )) {
			preg_match( '/[a-f0-9]{32}/', $file[0], $match );
		} else {
			$file[0] = '';
		}
		return array ('filename' => $file[0],'checksum' => (isset( $match[0] ) ? $match[0] : ''));
	}

	/**
	 * trimSourceCodeFile
	 *
	 * @param string $filename
	 *
	 * @return string $output
	 */
	public function trimSourceCodeFile ($filename)
	{
		$handle = fopen( $filename, "r" );
		$lastChar = '';
		$firstChar = '';
		$content = '';
		$line = '';

		if ($handle) {
			while (! feof( $handle )) {
				//$line = trim( fgets($handle, 16096) ) . "\n" ;
				$line = fgets( $handle, 16096 );
				$content .= $line;
			}
			fclose( $handle );
		}
		return $content;

		$index = 0;
		$output = '';
		while ($index < strlen( $content )) {
			$car = $content[$index];
			$index ++;
			if ($car == '/' && isset( $content[$index] ) && $content[$index] == '*') {
				$endComment = false;
				$index ++;
				while ($endComment == false && $index < strlen( $content )) {
					if ($content[$index] == '*' && isset( $content[$index + 1] ) && $content[$index + 1] == '/') {
						$endComment = true;
						$index ++;
					}
					$index ++;
				}
				$car = '';
			}
			$output .= $car;
		}
		return $output;
	}


	/**
	 * strip_slashes
	 * @param  vVar
	 */
	public function strip_slashes ($vVar)
	{
		if (is_array( $vVar )) {
			foreach ($vVar as $sKey => $vValue) {
				if (is_array( $vValue )) {
					Bootstrap::strip_slashes( $vVar[$sKey] );
				} else {
					$vVar[$sKey] = stripslashes( $vVar[$sKey] );
				}
			}
		} else {
			$vVar = stripslashes( $vVar );
		}

		return $vVar;
	}


	/**
	 * Function LoadTranslation
	 *
	 * @author Aldo Mauricio Veliz Valenzuela. <mauricio@colosa.com>
	 * @access public
	 * @param eter string msgID
	 * @param eter string file
	 * @param eter array data // erik: associative array within data input to replace for formatted string i.e "any messsage {replaced_label} that contains a replace label"
	 * @return string
	 */
	public function LoadTranslation ($msgID, $lang = SYS_LANG, $data = null)
	{
		global $translation;

		// if the second parameter $lang is an array does mean it was especified to use as data
		if (is_array( $lang )) {
			$data = $lang;
			$lang = SYS_LANG;
		}

		if (isset( $translation[$msgID] )) {
			$translationString = preg_replace( "[\n|\r|\n\r]", ' ', $translation[$msgID] );

			if (isset( $data ) && is_array( $data )) {
				foreach ($data as $label => $value) {
					$translationString = str_replace( '{' . $label . '}', $value, $translationString );
				}
			}

			return $translationString;
		} else {
			if (defined( "UNTRANSLATED_MARK" )) {
				$untranslatedMark = strip_tags( UNTRANSLATED_MARK );
			} else {
				$untranslatedMark = "**";
			}
			return $untranslatedMark . $msgID . $untranslatedMark;
		}

	}

	/**
	 * Recursive version of glob php standard function
	 *
	 * @author Erik Amaru Ortiz <erik@colosa.com>
	 *
	 * @param $path path to scan recursively the write permission
	 * @param $flags to notive glob function
	 * @param $pattern pattern to filter some especified files
	 * @return <array> array containing the recursive glob results
	 */
	public function rglob($pattern = '*', $flags = 0, $path = '')
	{
		$paths = glob($path.'*', GLOB_MARK|GLOB_ONLYDIR|GLOB_NOSORT);
		$files = glob($path.$pattern, $flags);
		foreach ($paths as $path) {
			$files = array_merge($files, Bootstrap::rglob($pattern, $flags, $path));
		}
		return $files;
	}

	/**
	 * JSON encode
	 *
	 * @author Erik A.O. <erik@gmail.com, aortiz.erik@gmail.com>
	 */
	public function json_encode($Json)
	{
		if ( function_exists('json_encode') ) {
			return json_encode($Json);
		} else {
			Bootstrap::LoadThirdParty('pear/json', 'class.json');
			$oJSON = new Services_JSON();
			return $oJSON->encode($Json);
		}
	}

	/**
	 * JSON decode
	 *
	 * @author Erik A.O. <erik@gmail.com, aortiz.erik@gmail.com>
	 */
	public function json_decode($Json)
	{
		if (function_exists('json_decode')) {
			return json_decode($Json);
		} else {
			Bootstrap::LoadThirdParty('pear/json', 'class.json');
			$oJSON = new Services_JSON();
			return $oJSON->decode($Json);
		}
	}


	/**
	 * ************************************* init **********************************************
	 * Xml parse collection functions
	 * Returns a associative array within the xml structure and data
	 *
	 * @author Erik Amaru Ortiz <erik@colosa.com>
	 */
	public function xmlParser (&$string)
	{
		$parser = xml_parser_create();
		xml_parser_set_option( $parser, XML_OPTION_CASE_FOLDING, 0 );
		xml_parse_into_struct( $parser, $string, $vals, $index );

		$mnary = array ();
		$ary = &$mnary;
		foreach ($vals as $r) {
			$t = $r['tag'];
			if ($r['type'] == 'open') {
				if (isset( $ary[$t] )) {
					if (isset( $ary[$t][0] )) {
						$ary[$t][] = array ();
					} else {
						$ary[$t] = array ($ary[$t],array () );
					}
					$cv = &$ary[$t][count( $ary[$t] ) - 1];
				} else {
					$cv = &$ary[$t];
				}
				if (isset( $r['attributes'] )) {
					foreach ($r['attributes'] as $k => $v) {
						$cv['__ATTRIBUTES__'][$k] = $v;
					}
				}
				// note by gustavo cruz gustavo[at]colosa[dot]com
				// minor adjustments to validate if an open node have a value attribute.
				// for example a dropdown has many childs, but also can have a value attribute.
				if (isset( $r['value'] ) && trim( $r['value'] ) != '') {
					$cv['__VALUE__'] = $r['value'];
				}
				// end added code
				$cv['__CONTENT__'] = array ();
				$cv['__CONTENT__']['_p'] = &$ary;
				$ary = &$cv['__CONTENT__'];

			} elseif ($r['type'] == 'complete') {
				if (isset( $ary[$t] )) {
					if (isset( $ary[$t][0] )) {
						$ary[$t][] = array ();
					} else {
						$ary[$t] = array ($ary[$t],array ());
					}
					$cv = &$ary[$t][count( $ary[$t] ) - 1];
				} else {
					$cv = &$ary[$t];
				}
				if (isset( $r['attributes'] )) {
					foreach ($r['attributes'] as $k => $v) {
						$cv['__ATTRIBUTES__'][$k] = $v;
					}
				}
				$cv['__VALUE__'] = (isset( $r['value'] ) ? $r['value'] : '');

			} elseif ($r['type'] == 'close') {
				$ary = &$ary['_p'];
			}
		}

		self::_del_p( $mnary );

		$obj_resp->code = xml_get_error_code( $parser );
		$obj_resp->message = xml_error_string( $obj_resp->code );
		$obj_resp->result = $mnary;
		xml_parser_free( $parser );

		return $obj_resp;
	}

	/**
		*
		* @param unknown_type $path
		* @param unknown_type $maxmtime
		* @return Ambigous <number, unknown>
		*/
	public function getDirectorySize ($path, $maxmtime = 0)
	{
		$totalsize = 0;
		$totalcount = 0;
		$dircount = 0;
		if ($handle = opendir( $path )) {
			while (false !== ($file = readdir( $handle ))) {
				$nextpath = $path . '/' . $file;
				if ($file != '.' && $file != '..' && ! is_link( $nextpath ) && $file != '.svn') {
					if (is_dir( $nextpath )) {
						$dircount ++;
						$result = Bootstrap::getDirectorySize( $nextpath, $maxmtime );
						$totalsize += $result['size'];
						$totalcount += $result['count'];
						$dircount += $result['dircount'];
						$maxmtime = $result['maxmtime'] > $maxmtime ? $result['maxmtime'] : $maxmtime;
					} elseif (is_file( $nextpath )) {
						$totalsize += filesize( $nextpath );
						$totalcount ++;

						$mtime = filemtime( $nextpath );
						if ($mtime > $maxmtime) {
							$maxmtime = $mtime;
						}
					}
				}
			}
		}
		closedir( $handle );
		$total['size'] = $totalsize;
		$total['count'] = $totalcount;
		$total['dircount'] = $dircount;
		$total['maxmtime'] = $maxmtime;

		return $total;
	}


	/**
	 * _del_p
	 *
	 * @param string &$ary
	 *
	 * @return void
	 */
	// _Internal: Remove recursion in result array
	public function _del_p (&$ary)
	{
		foreach ($ary as $k => $v) {
			if ($k === '_p') {
				unset( $ary[$k] );
			} elseif (is_array( $ary[$k] )) {
				self::_del_p( $ary[$k] );
			}
		}
	}

	/**
	 * Refactor function
	 * @author Ralph A.
	 * @return multitype:array containing browser name and type
	 */
	public function get_current_browser()
	{
	    static $a_full_assoc_data, $a_mobile_data, $browser_user_agent;
	    static $browser_working, $moz_type, $webkit_type;

	    //initialize all variables with default values to prevent error
	    $a_full_assoc_data = '';
	    $a_mobile_data = '';
	    $browser_temp = '';
	    $browser_working = '';
	    $mobile_test = '';
	    $moz_type = '';
	    $ua_type = 'bot';// default to bot since you never know with bots
	    $webkit_type = '';

	    /*
	     make navigator user agent string lower case to make sure all versions get caught
	    isset protects against blank user agent failure. tolower also lets the script use
	    strstr instead of stristr, which drops overhead slightly.
	    */
	    $browser_user_agent = strtolower( $_SERVER['HTTP_USER_AGENT'] );

	    // known browsers, list will be updated routinely, check back now and then
	    $a_browser_types = array(
            array( 'opera', true, 'op', 'bro' ),
            array( 'msie', true, 'ie', 'bro' ),
            // webkit before gecko because some webkit ua strings say: like gecko
            array( 'webkit', true, 'webkit', 'bro' ),
            // konq will be using webkit soon
            array( 'konqueror', true, 'konq', 'bro' ),
            // covers Netscape 6-7, K-Meleon, Most linux versions, uses moz array below
            array( 'gecko', true, 'moz', 'bro' ),
            array( 'netpositive', false, 'netp', 'bbro' ),// beos browser
            array( 'lynx', false, 'lynx', 'bbro' ), // command line browser
            array( 'elinks ', false, 'elinks', 'bbro' ), // new version of links
            array( 'elinks', false, 'elinks', 'bbro' ), // alternate id for it
            array( 'links2', false, 'links2', 'bbro' ), // alternate links version
            array( 'links ', false, 'links', 'bbro' ), // old name for links
            array( 'links', false, 'links', 'bbro' ), // alternate id for it
            array( 'w3m', false, 'w3m', 'bbro' ), // open source browser, more features than lynx/links
            array( 'webtv', false, 'webtv', 'bbro' ),// junk ms webtv
            array( 'amaya', false, 'amaya', 'bbro' ),// w3c browser
            array( 'dillo', false, 'dillo', 'bbro' ),// linux browser, basic table support
            array( 'ibrowse', false, 'ibrowse', 'bbro' ),// amiga browser
            array( 'icab', false, 'icab', 'bro' ),// mac browser
            array( 'crazy browser', true, 'ie', 'bro' ),// uses ie rendering engine
            // search engine spider bots:
            array( 'bingbot', false, 'bing', 'bot' ),// bing
            array( 'exabot', false, 'exabot', 'bot' ),// exabot
            array( 'googlebot', false, 'google', 'bot' ),// google
            array( 'google web preview', false, 'googlewp', 'bot' ),// google preview
            array( 'mediapartners-google', false, 'adsense', 'bot' ),// google adsense
            array( 'yahoo-verticalcrawler', false, 'yahoo', 'bot' ),// old yahoo bot
            array( 'yahoo! slurp', false, 'yahoo', 'bot' ), // new yahoo bot
            array( 'yahoo-mm', false, 'yahoomm', 'bot' ), // gets Yahoo-MMCrawler and Yahoo-MMAudVid bots
            array( 'inktomi', false, 'inktomi', 'bot' ), // inktomi bot
            array( 'slurp', false, 'inktomi', 'bot' ), // inktomi bot
            array( 'fast-webcrawler', false, 'fast', 'bot' ),// Fast AllTheWeb
            array( 'msnbot', false, 'msn', 'bot' ),// msn search
            array( 'ask jeeves', false, 'ask', 'bot' ), //jeeves/teoma
            array( 'teoma', false, 'ask', 'bot' ),//jeeves teoma
            array( 'scooter', false, 'scooter', 'bot' ),// altavista
            array( 'openbot', false, 'openbot', 'bot' ),// openbot, from taiwan
            array( 'ia_archiver', false, 'ia_archiver', 'bot' ),// ia archiver
            array( 'zyborg', false, 'looksmart', 'bot' ),// looksmart
            array( 'almaden', false, 'ibm', 'bot' ),// ibm almaden web crawler
            array( 'baiduspider', false, 'baidu', 'bot' ),// Baiduspider asian search spider
            array( 'psbot', false, 'psbot', 'bot' ),// psbot image crawler
            array( 'gigabot', false, 'gigabot', 'bot' ),// gigabot crawler
            array( 'naverbot', false, 'naverbot', 'bot' ),// naverbot crawler, bad bot, block
            array( 'surveybot', false, 'surveybot', 'bot' ),//
            array( 'boitho.com-dc', false, 'boitho', 'bot' ),//norwegian search engine
            array( 'objectssearch', false, 'objectsearch', 'bot' ),// open source search engine
            array( 'answerbus', false, 'answerbus', 'bot' ),// http://www.answerbus.com/, web questions
            array( 'sohu-search', false, 'sohu', 'bot' ),// chinese media company, search component
            array( 'iltrovatore-setaccio', false, 'il-set', 'bot' ),
            // various http utility libaries
            array( 'w3c_validator', false, 'w3c', 'lib' ), // uses libperl, make first
            array( 'wdg_validator', false, 'wdg', 'lib' ), //
            array( 'libwww-perl', false, 'libwww-perl', 'lib' ),
            array( 'jakarta commons-httpclient', false, 'jakarta', 'lib' ),
            array( 'python-urllib', false, 'python-urllib', 'lib' ),
            // download apps
            array( 'getright', false, 'getright', 'dow' ),
            array( 'wget', false, 'wget', 'dow' ),// open source downloader, obeys robots.txt
            // netscape 4 and earlier tests, put last so spiders don't get caught
            array( 'mozilla/4.', false, 'ns', 'bbro' ),
            array( 'mozilla/3.', false, 'ns', 'bbro' ),
            array( 'mozilla/2.', false, 'ns', 'bbro' )
            );
        /*
        moz types array
        note the order, netscape6 must come before netscape, which  is how netscape 7 id's itself.
        rv comes last in case it is plain old mozilla. firefox/netscape/seamonkey need to be later
        Thanks to: http://www.zytrax.com/tech/web/firefox-history.html
        */
        $a_moz_types = array( 'bonecho', 'camino', 'epiphany', 'firebird', 'flock', 'galeon', 'iceape', 'icecat', 'k-meleon', 'minimo', 'multizilla', 'phoenix', 'songbird', 'swiftfox', 'seamonkey', 'shiretoko', 'iceweasel', 'firefox', 'minefield', 'netscape6', 'netscape', 'rv' );

        /*
        webkit types, this is going to expand over time as webkit b$browser_namerowsers spread
        konqueror is probably going to move to webkit, so this is preparing for that
        It will now default to khtml. gtklauncher is the temp id for epiphany, might
        change. Defaults to applewebkit, and will all show the webkit number.
        */
        $a_webkit_types = array( 'arora', 'chrome', 'epiphany', 'gtklauncher', 'konqueror', 'midori', 'omniweb', 'safari', 'uzbl', 'applewebkit', 'webkit' );

        /*
        run through the browser_types array, break if you hit a match, if no match, assume old browser
        or non dom browser.
        */
        $i_count = count( $a_browser_types );
        for ($i = 0; $i < $i_count; $i++) {
            //unpacks browser array, assigns to variables, need to not assign til found in string
            $browser_temp = $a_browser_types[$i][0];// text string to id browser from array
            if ( strstr( $browser_user_agent, $browser_temp ) ) {
                $browser_working = $a_browser_types[$i][2];// working name for browser
                $ua_type = $a_browser_types[$i][3];// sets whether bot or browser

                switch ( $browser_working ) {
                    case 'moz':
                        // this is to pull out specific mozilla versions, firebird, netscape etc..
                        $j_count = count( $a_moz_types );
                        for ($j = 0; $j < $j_count; $j++) {
                            if ( strstr( $browser_user_agent, $a_moz_types[$j] ) ) {
                                $moz_type = $a_moz_types[$j];
                                break;
                            }
                        }
                        if ( $moz_type == 'rv' ) {
                            $moz_type = 'mozilla';
                        }
                        break;
                    case 'webkit':
                        // this is to pull out specific webkit versions, safari, google-chrome etc..
                        $j_count = count( $a_webkit_types );
                        for ($j = 0; $j < $j_count; $j++) {
                            if (strstr( $browser_user_agent, $a_webkit_types[$j])) {
                                $webkit_type = $a_webkit_types[$j];
                                break;
                            }
                        }
                        break;
                    default:
                        break;
               }
               break;
            }
        }

        $mobile_test = Bootstrap::check_is_mobile( $browser_user_agent );
        if ( $mobile_test ) {
            $a_mobile_data = Bootstrap::get_mobile_data( $browser_user_agent );
            $ua_type = 'mobile';
        }

	    $a_full_assoc_data = array(
				'browser_working' => $browser_working,
				'ua_type' => $ua_type,
				'moz_data' => array($moz_type),
				'webkit_data' => array($webkit_type),
				'mobile_data' => array($a_mobile_data),
		);

	    return $a_full_assoc_data;
	}

	/**
	 * track total script execution time
	 */
	public function script_time ()
	{
		static $script_time;
		$elapsed_time = '';
		/*
		 note that microtime(true) requires php 5 or greater for microtime(true)
		*/
		if (sprintf( "%01.1f", phpversion() ) >= 5) {
			if (is_null( $script_time )) {
				$script_time = microtime( true );
			} else {
				// note: (string)$var is same as strval($var)
				// $elapsed_time = (string)( microtime(true) - $script_time );
				$elapsed_time = (microtime( true ) - $script_time);
				$elapsed_time = sprintf( "%01.8f", $elapsed_time );
				$script_time = null; // can't unset a static variable
				return $elapsed_time;
			}
		}
	}

	/**
	 *
	 * @param unknown_type $pv_browser_user_agent
	 * @param unknown_type $pv_search_string
	 * @param unknown_type $pv_b_break_last
	 * @param unknown_type $pv_extra_search
	 * @return string
	 */
	public function get_item_version($pv_browser_user_agent, $pv_search_string, $pv_b_break_last = '', $pv_extra_search = '')
	{
		$substring_length = 15;
		$start_pos = 0; // set $start_pos to 0 for first iteration
		$string_working_number = '';
		for ($i = 0; $i < 4; $i++) {
			//start the search after the first string occurrence
			if (strpos( $pv_browser_user_agent, $pv_search_string, $start_pos ) !== false) {
				$start_pos = strpos( $pv_browser_user_agent, $pv_search_string, $start_pos ) + strlen( $pv_search_string );
				if (!$pv_b_break_last || ( $pv_extra_search && strstr( $pv_browser_user_agent, $pv_extra_search ) )) {
					break;
				}
			} else {
				break;
			}
		}

		$start_pos += Bootstrap::get_set_count( 'get' );
		$string_working_number = substr( $pv_browser_user_agent, $start_pos, $substring_length );
		$string_working_number = substr( $string_working_number, 0, strcspn($string_working_number, ' );/') );
		if (!is_numeric( substr( $string_working_number, 0, 1 ))) {
			$string_working_number = '';
		}
		return $string_working_number;
	}

	/**
	 *
	 * @param unknown_type $pv_type
	 * @param unknown_type $pv_value
	 */
	public function get_set_count($pv_type, $pv_value = '')
	{
		static $slice_increment;
		$return_value = '';
		switch ( $pv_type ) {
			case 'get':
				if ( is_null( $slice_increment ) ) {
					$slice_increment = 1;
				}
				$return_value = $slice_increment;
				$slice_increment = 1; // reset to default
				return $return_value;
				break;
			case 'set':
				$slice_increment = $pv_value;
				break;
		}
	}

	/**
	 * gets which os from the browser string
	 */
	public function get_os_data ($pv_browser_string, $pv_browser_name, $pv_version_number)
	{
		// initialize variables
		$os_working_type = '';
		$os_working_number = '';
		/*
		 packs the os array. Use this order since some navigator user agents will put 'macintosh'
		in the navigator user agent string which would make the nt test register true
		*/
		$a_mac = array( 'intel mac', 'ppc mac', 'mac68k' );// this is not used currently
		// same logic, check in order to catch the os's in order, last is always default item
		$a_unix_types = array( 'dragonfly', 'freebsd', 'openbsd', 'netbsd', 'bsd', 'unixware', 'solaris', 'sunos', 'sun4', 'sun5', 'suni86', 'sun', 'irix5', 'irix6', 'irix', 'hpux9', 'hpux10', 'hpux11', 'hpux', 'hp-ux', 'aix1', 'aix2', 'aix3', 'aix4', 'aix5', 'aix', 'sco', 'unixware', 'mpras', 'reliant', 'dec', 'sinix', 'unix' );
		// only sometimes will you get a linux distro to id itself...
		$a_linux_distros = array( 'ubuntu', 'kubuntu', 'xubuntu', 'mepis', 'xandros', 'linspire', 'winspire', 'jolicloud', 'sidux', 'kanotix', 'debian', 'opensuse', 'suse', 'fedora', 'redhat', 'slackware', 'slax', 'mandrake', 'mandriva', 'gentoo', 'sabayon', 'linux' );
		$a_linux_process = array ( 'i386', 'i586', 'i686' );// not use currently
		// note, order of os very important in os array, you will get failed ids if changed
		$a_os_types = array( 'android', 'blackberry', 'iphone', 'palmos', 'palmsource', 'symbian', 'beos', 'os2', 'amiga', 'webtv', 'mac', 'nt', 'win', $a_unix_types, $a_linux_distros );

		//os tester
		$i_count = count( $a_os_types );
		for ($i = 0; $i < $i_count; $i++) {
			// unpacks os array, assigns to variable $a_os_working
			$os_working_data = $a_os_types[$i];
			/*
			 assign os to global os variable, os flag true on success
			!strstr($pv_browser_string, "linux" ) corrects a linux detection bug
			*/
			if (!is_array($os_working_data) && strstr($pv_browser_string, $os_working_data ) && !strstr( $pv_browser_string, "linux")) {
				$os_working_type = $os_working_data;

				switch ($os_working_type) {
					// most windows now uses: NT X.Y syntax
					case 'nt':
						if (strstr( $pv_browser_string, 'nt 6.1' )) {
							$os_working_number = 6.1;
						} elseif (strstr( $pv_browser_string, 'nt 6.0')) {
							$os_working_number = 6.0;
						} elseif (strstr( $pv_browser_string, 'nt 5.2')) {
							$os_working_number = 5.2;
						} elseif (strstr( $pv_browser_string, 'nt 5.1') || strstr( $pv_browser_string, 'xp')) {
							$os_working_number = 5.1;//
						} elseif (strstr( $pv_browser_string, 'nt 5') || strstr( $pv_browser_string, '2000')) {
							$os_working_number = 5.0;
						} elseif (strstr( $pv_browser_string, 'nt 4')) {
							$os_working_number = 4;
						} elseif (strstr( $pv_browser_string, 'nt 3')) {
							$os_working_number = 3;
						}
						break;
					case 'win':
						if (strstr( $pv_browser_string, 'vista')) {
							$os_working_number = 6.0;
							$os_working_type = 'nt';
						} elseif ( strstr( $pv_browser_string, 'xp')) {
							$os_working_number = 5.1;
							$os_working_type = 'nt';
						} elseif ( strstr( $pv_browser_string, '2003')) {
							$os_working_number = 5.2;
							$os_working_type = 'nt';
						}
						elseif ( strstr( $pv_browser_string, 'windows ce' ) )// windows CE
						{
							$os_working_number = 'ce';
							$os_working_type = 'nt';
						}
						elseif ( strstr( $pv_browser_string, '95' ) )
						{
							$os_working_number = '95';
						}
						elseif ( ( strstr( $pv_browser_string, '9x 4.9' ) ) || ( strstr( $pv_browser_string, ' me' ) ) )
						{
							$os_working_number = 'me';
						}
						elseif ( strstr( $pv_browser_string, '98' ) )
						{
							$os_working_number = '98';
						}
						elseif ( strstr( $pv_browser_string, '2000' ) )// windows 2000, for opera ID
						{
							$os_working_number = 5.0;
							$os_working_type = 'nt';
						}
						break;
					case 'mac':
						if (strstr($pv_browser_string, 'os x')) {
							if (strstr($pv_browser_string, 'os x ')) {
								$os_working_number = str_replace( '_', '.', Bootstrap::get_item_version( $pv_browser_string, 'os x' ) );
							} else {
								$os_working_number = 10;
							}
						} elseif ( ( $pv_browser_name == 'saf' ) || ( $pv_browser_name == 'cam' ) ||
								( ( $pv_browser_name == 'moz' ) && ( $pv_version_number >= 1.3 ) ) ||
								( ( $pv_browser_name == 'ie' ) && ( $pv_version_number >= 5.2 ) ) ) {
							$os_working_number = 10;
						}
						break;
					case 'iphone':
						$os_working_number = 10;
						break;
					default:
						break;
				}
				break;
			} elseif ( is_array( $os_working_data ) && ( $i == ( $i_count - 2 ) ) ) {
				$j_count = count($os_working_data);
				for ($j = 0; $j < $j_count; $j++) {
					if (strstr( $pv_browser_string, $os_working_data[$j])) {
						$os_working_type = 'unix'; //if the os is in the unix array, it's unix, obviously...
						$os_working_number = ( $os_working_data[$j] != 'unix' ) ? $os_working_data[$j] : '';// assign sub unix version from the unix array
						break;
					}
				}
			} elseif (is_array( $os_working_data ) && ( $i == ( $i_count - 1 ))) {
				$j_count = count($os_working_data);
				for ($j = 0; $j < $j_count; $j++) {
					if ( strstr( $pv_browser_string, $os_working_data[$j] )) {
						$os_working_type = 'lin';
						// assign linux distro from the linux array, there's a default
						//search for 'lin', if it's that, set version to ''
						$os_working_number = ( $os_working_data[$j] != 'linux' ) ? $os_working_data[$j] : '';
						break;
					}
				}
			}
		}

		// pack the os data array for return to main function
		$a_os_data = array( $os_working_type, $os_working_number );

		return $a_os_data;
	}

	/**
	 *
	 * @param unknown_type $pv_browser_user_agent
	 * @return string
	 */
	public function check_is_mobile($pv_browser_user_agent)
	{
		$mobile_working_test = '';
		$a_mobile_search = array(
				'android', 'epoc', 'linux armv', 'palmos', 'palmsource', 'windows ce', 'windows phone os', 'symbianos', 'symbian os', 'symbian', 'webos',
				// devices - ipod before iphone or fails
				'benq', 'blackberry', 'danger hiptop', 'ddipocket', ' droid', 'ipad', 'ipod', 'iphone', 'kindle', 'lge-cx', 'lge-lx', 'lge-mx', 'lge vx', 'lge ', 'lge-', 'lg;lx', 'nintendo wii', 'nokia', 'palm', 'pdxgw', 'playstation', 'sagem', 'samsung', 'sec-sgh', 'sharp', 'sonyericsson', 'sprint', 'zune', 'j-phone', 'n410', 'mot 24', 'mot-', 'htc-', 'htc_', 'htc ', 'sec-', 'sie-m', 'sie-s', 'spv ', 'vodaphone', 'smartphone', 'armv', 'midp', 'mobilephone',
				// browsers
				'avantgo', 'blazer', 'elaine', 'eudoraweb', 'iemobile',  'minimo', 'mobile safari', 'mobileexplorer', 'opera mobi', 'opera mini', 'netfront', 'opwv', 'polaris', 'semc-browser', 'up.browser', 'webpro', 'wms pie', 'xiino',
				// services - astel out of business
				'astel',  'docomo',  'novarra-vision', 'portalmmm', 'reqwirelessweb', 'vodafone'
		);

		// then do basic mobile type search, this uses data from: get_mobile_data()
		$j_count = count( $a_mobile_search );
		for ($j = 0; $j < $j_count; $j++) {
			if (strstr( $pv_browser_user_agent, $a_mobile_search[$j] )) {
				$mobile_working_test = $a_mobile_search[$j];
				break;
			}
		}
		return $mobile_working_test;
	}

	/**
	 *
	 * @param unknown_type $pv_browser_user_agent
	 */
	public function get_mobile_data ($pv_browser_user_agent)
	{
		$mobile_browser = '';
		$mobile_browser_number = '';
		$mobile_device = '';
		$mobile_device_number = '';
		$mobile_os = ''; // will usually be null, sorry
		$mobile_os_number = '';
		$mobile_server = '';
		$mobile_server_number = '';

		$a_mobile_browser = array( 'avantgo', 'blazer', 'elaine', 'eudoraweb', 'iemobile',  'minimo', 'mobile safari', 'mobileexplorer', 'opera mobi', 'opera mini', 'netfront', 'opwv', 'polaris', 'semc-browser', 'up.browser', 'webpro', 'wms pie', 'xiino' );
		$a_mobile_device = array( 'benq', 'blackberry', 'danger hiptop', 'ddipocket', ' droid', 'htc_dream', 'htc espresso', 'htc hero', 'htc halo', 'htc huangshan', 'htc legend', 'htc liberty', 'htc paradise', 'htc supersonic', 'htc tattoo', 'ipad', 'ipod', 'iphone', 'kindle', 'lge-cx', 'lge-lx', 'lge-mx', 'lge vx', 'lg;lx', 'nintendo wii', 'nokia', 'palm', 'pdxgw', 'playstation', 'sagem', 'samsung', 'sec-sgh', 'sharp', 'sonyericsson', 'sprint', 'zunehd', 'zune', 'j-phone', 'milestone', 'n410', 'mot 24', 'mot-', 'htc-', 'htc_',  'htc ', 'lge ', 'lge-', 'sec-', 'sie-m', 'sie-s', 'spv ', 'smartphone', 'armv', 'midp', 'mobilephone' );
		$a_mobile_os = array( 'android', 'epoc', 'cpu os', 'iphone os', 'palmos', 'palmsource', 'windows phone os', 'windows ce', 'symbianos', 'symbian os', 'symbian', 'webos', 'linux armv'  );
		$a_mobile_server = array( 'astel', 'docomo', 'novarra-vision', 'portalmmm', 'reqwirelessweb', 'vodafone' );

		$k_count = count( $a_mobile_browser );
		for ($k = 0; $k < $k_count; $k++) {
			if (strstr( $pv_browser_user_agent, $a_mobile_browser[$k] )) {
				$mobile_browser = $a_mobile_browser[$k];
				$mobile_browser_number = Bootstrap::get_item_version( $pv_browser_user_agent, $mobile_browser );
				break;
			}
		}
		$k_count = count( $a_mobile_device );
		for ($k = 0; $k < $k_count; $k++) {
			if (strstr( $pv_browser_user_agent, $a_mobile_device[$k] )) {
				$mobile_device = trim ( $a_mobile_device[$k], '-_' ); // but not space trims yet
				if ($mobile_device == 'blackberry') {
					Bootstrap::get_set_count( 'set', 0 );
				}
				$mobile_device_number = Bootstrap::get_item_version( $pv_browser_user_agent, $mobile_device );
				$mobile_device = trim( $mobile_device ); // some of the id search strings have white space
				break;
			}
		}
		$k_count = count( $a_mobile_os );
		for ($k = 0; $k < $k_count; $k++) {
			if (strstr( $pv_browser_user_agent, $a_mobile_os[$k] )) {
				$mobile_os = $a_mobile_os[$k];
				$mobile_os_number = str_replace( '_', '.', Bootstrap::get_item_version( $pv_browser_user_agent, $mobile_os ) );
				break;
			}
		}
		$k_count = count( $a_mobile_server );
		for ($k = 0; $k < $k_count; $k++) {
			if (strstr( $pv_browser_user_agent, $a_mobile_server[$k] )) {
				$mobile_server = $a_mobile_server[$k];
				$mobile_server_number = Bootstrap::get_item_version( $pv_browser_user_agent, $mobile_server );
				break;
			}
		}
		// just for cases where we know it's a mobile device already
		if (!$mobile_os && ( $mobile_browser || $mobile_device || $mobile_server ) && strstr( $pv_browser_user_agent, 'linux' ) ) {
			$mobile_os = 'linux';
			$mobile_os_number = Bootstrap::get_item_version( $pv_browser_user_agent, 'linux' );
		}

		$a_mobile_data = array( $mobile_device, $mobile_browser, $mobile_browser_number, $mobile_os, $mobile_os_number, $mobile_server, $mobile_server_number, $mobile_device_number );
		return $a_mobile_data;
	}

	/**
	 *
	 * @param unknown_type $requestUri
	 */
	public function parseRestUri ($requestUri)
	{
		$args = array ();
		//$args['SYS_TEMP'] = $requestUri[1];
		define( 'SYS_TEMP', $requestUri[2] );
		$restUri = '';

		for ($i = 3; $i < count( $requestUri ); $i ++) {
			$restUri .= '/' . $requestUri[$i];
		}

		$args['SYS_LANG'] = 'en'; // TODO, this can be set from http header
		$args['SYS_SKIN'] = '';
		$args['SYS_COLLECTION'] = '';
		$args['SYS_TARGET'] = $restUri;

		return $args;
	}

	/**
	 *
	 * @param unknown_type $aRequestUri
	 * @return multitype:string mixed Ambigous <number, string>
	 */
	public function parseNormalUri ($aRequestUri)
	{
		if (substr( $aRequestUri[1], 0, 3 ) == 'sys') {
			define( 'SYS_TEMP', substr( $aRequestUri[1], 3 ) );
		} else {
			define( "ENABLE_ENCRYPT", 'yes' );
			define( 'SYS_TEMP', $aRequestUri[1] );
			$plain = '/sys' . SYS_TEMP;

			for ($i = 2; $i < count( $aRequestUri ); $i ++) {
				$decoded = Bootstrap::decrypt( urldecode( $aRequestUri[$i] ), URL_KEY );
				if ($decoded == 'sWì›') {
					$decoded = $VARS[$i]; //this is for the string  "../"
				}
				$plain .= '/' . $decoded;
			}
			$_SERVER["REQUEST_URI"] = $plain;
		}

		$work = explode( '?', $_SERVER["REQUEST_URI"] );

		if (count( $work ) > 1) {
			define( 'SYS_CURRENT_PARMS', $work[1] );
		} else {
			define( 'SYS_CURRENT_PARMS', '' );
		}

		define( 'SYS_CURRENT_URI', $work[0] );

		if (! defined( 'SYS_CURRENT_PARMS' )) {
			define( 'SYS_CURRENT_PARMS', $work[1] );
		}

		$preArray = explode( '&', SYS_CURRENT_PARMS );
		$buffer = explode( '.', $work[0] );

		if (count( $buffer ) == 1) {
			$buffer[1] = '';
		}

		//request type
		define( 'REQUEST_TYPE', ($buffer[1] != "" ? $buffer[1] : 'html') );

		$toparse = substr( $buffer[0], 1, strlen( $buffer[0] ) - 1 );
		$uriVars = explode( '/', $toparse );

		unset( $work );
		unset( $buffer );
		unset( $toparse );
		array_shift( $uriVars );

		$args = array ();
		$args['SYS_LANG'] = array_shift( $uriVars );
		$args['SYS_SKIN'] = array_shift( $uriVars );
		$args['SYS_COLLECTION'] = array_shift( $uriVars );
		$args['SYS_TARGET'] = array_shift( $uriVars );

		//to enable more than 2 directories...in the methods structure
		while (count( $uriVars ) > 0) {
			$args['SYS_TARGET'] .= '/' . array_shift( $uriVars );
		}

		/* Fix to prevent use uxs skin outside siplified interface,
		 because that skin is not compatible with others interfaces*/
		if ($args['SYS_SKIN'] == 'uxs' && $args['SYS_COLLECTION'] != 'home' && $args['SYS_COLLECTION'] != 'cases') {
			$config = System::getSystemConfiguration();
			$args['SYS_SKIN'] = $config['default_skin'];
		}

		return $args;
	}

	/**
	 * * Encrypt and decrypt functions ***
	 */
	/**
	 * Encrypt string
	 *
	 * @author Fernando Ontiveros Lira <fernando@colosa.com>
	 * @access public
	 * @param string $string
	 * @param string $key
	 * @return string
	 */
	public function encrypt ($string, $key)
	{
		//print $string;
		//    if ( defined ( 'ENABLE_ENCRYPT' ) && ENABLE_ENCRYPT == 'yes' ) {
		if (strpos( $string, '|', 0 ) !== false) {
			return $string;
		}
		$result = '';
		for ($i = 0; $i < strlen( $string ); $i ++) {
			$char = substr( $string, $i, 1 );
			$keychar = substr( $key, ($i % strlen( $key )) - 1, 1 );
			$char = chr( ord( $char ) + ord( $keychar ) );
			$result .= $char;
		}

		$result = base64_encode( $result );
		$result = str_replace( '/', '°', $result );
		$result = str_replace( '=', '', $result );
		return $result;
		}

		/**
		 * Decrypt string
		 *
		 * @author Fernando Ontiveros Lira <fernando@colosa.com>
		 * @access public
		 * @param string $string
		 * @param string $key
		 * @return string
		 */
		public function decrypt ($string, $key)
		{
			//   if ( defined ( 'ENABLE_ENCRYPT' ) && ENABLE_ENCRYPT == 'yes' ) {
			//if (strpos($string, '|', 0) !== false) return $string;
			$result = '';
			$string = str_replace( '°', '/', $string );
			$string_jhl = explode( "?", $string );
			$string = base64_decode( $string );
			$string = base64_decode( $string_jhl[0] );

			for ($i = 0; $i < strlen( $string ); $i ++) {
				$char = substr( $string, $i, 1 );
				$keychar = substr( $key, ($i % strlen( $key )) - 1, 1 );
				$char = chr( ord( $char ) - ord( $keychar ) );
				$result .= $char;
			}
			if (! empty( $string_jhl[1] )) {
				$result .= '?' . $string_jhl[1];
			}
			return $result;
		}

		/**
		 *
		 * @param unknown_type $model
		 * @return unknown
		 */
		public function getModel($model)
		{
			require_once "classes/model/$model.php";
			return new $model();
		}

		/**
		 * Create an encrypted unique identifier based on $id and the selected scope id.
		 *
		 * @author David S. Callizaya S. <davidsantos@colosa.com>
		 * @access public
		 * @param string $scope
		 * @param string $id
		 * @return string
		 */
		public function createUID ($scope, $id)
		{
			$e = $scope . $id;
			$e = Bootstrap::encrypt( $e, URL_KEY );
			$e = str_replace( array ('+','/','='
			), array ('__','_','___'
			), base64_encode( $e ) );
			return $e;
		}

		/**
		 * (Create an encrypted unique identificator based on $id and the selected scope id.) ^-1
		 * getUIDName
		 *
		 * @author David S. Callizaya S. <davidsantos@colosa.com>
		 * @access public
		 * @param string $id
		 * @param string $scope
		 * @return string
		 */
		public function getUIDName ($uid, $scope = '')
		{
			$e = str_replace( array ('=','+','/'
			), array ('___','__','_'
			), $uid );
			$e = base64_decode( $e );
			$e = Bootstrap::decrypt( $e, URL_KEY );
			$e = substr( $e, strlen( $scope ) );
			return $e;
		}

		/**
		 * Merge 2 arrays
		 *
		 * @author Fernando Ontiveros Lira <fernando@colosa.com>
		 * @access public
		 * @return array
		 */
		public function array_merges ()
		{
			$array = array ();
			$arrays = & func_get_args();
			foreach ($arrays as $array_i) {
				if (is_array( $array_i )) {
					Bootstrap::array_merge_2( $array, $array_i );
				}
			}
			return $array;
		}

		/**
		 * Merge 2 arrays
		 *
		 * @author Fernando Ontiveros Lira <fernando@colosa.com>
		 * @access public
		 * @param string $array
		 * @param string $array_i
		 * @return array
		 */
		public function array_merge_2 (&$array, &$array_i)
		{
			foreach ($array_i as $k => $v) {
				if (is_array( $v )) {
					if (! isset( $array[$k] )) {
						$array[$k] = array ();
					}
					Bootstrap::array_merge_2( $array[$k], $v );
				} else {
					if (isset( $array[$k] ) && is_array( $array[$k] )) {
						$array[$k][0] = $v;
					} else {
						if (isset( $array ) && ! is_array( $array )) {
							$temp = $array;
							$array = array();
							$array[0] = $temp;
						}
						$array[$k] = $v;
					}
				}
			}
		}

		/* Returns a sql string with @@parameters replaced with its values defined
		 * in array $result using the next notation:
		* NOTATION:
		*     @@  Quoted parameter acording to the SYSTEM's Database
		*     @Q  Double quoted parameter \\  \"
		*     @q  Single quoted parameter \\  \'
		*     @%  URL string
		*     @#  Non-quoted parameter
		*     @!  Evaluate string : Replace the parameters in value and then in the sql string
		*     @fn()  Evaluate string with the function "fn"
		* @author David Callizaya <calidavidx21@hotmail.com>
		*/
		public function replaceDataField ($sqlString, $result, $DBEngine = 'mysql')
		{
			if (! is_array( $result )) {
				$result = array ();
			}
			$result = $result + Bootstrap::getSystemConstants();
			$__textoEval = "";
			$u = 0;
			//$count=preg_match_all('/\@(?:([\@\%\#\!Qq])([a-zA-Z\_]\w*)|([a-zA-Z\_][\w\-\>\:]*)\(((?:[^\\\\\)]*(?:[\\\\][\w\W])?)*)\))/',$sqlString,$match,PREG_PATTERN_ORDER | PREG_OFFSET_CAPTURE);
			$count = preg_match_all( '/\@(?:([\@\%\#\=\!Qq])([a-zA-Z\_]\w*)|([a-zA-Z\_][\w\-\>\:]*)\(((?:[^\\\\\)]*?)*)\))/', $sqlString, $match, PREG_PATTERN_ORDER | PREG_OFFSET_CAPTURE );
			if ($count) {
				for ($r = 0; $r < $count; $r ++) {
					if (! isset( $result[$match[2][$r][0]] )) {
						$result[$match[2][$r][0]] = '';
					}
					if (! is_array( $result[$match[2][$r][0]] )) {
						$__textoEval .= substr( $sqlString, $u, $match[0][$r][1] - $u );
						$u = $match[0][$r][1] + strlen( $match[0][$r][0] );
						//Mysql quotes scape
						if (($match[1][$r][0] == '@') && (isset( $result[$match[2][$r][0]] ))) {
							$__textoEval .= "\"" . Bootstrap::sqlEscape( $result[$match[2][$r][0]], $DBEngine ) . "\"";
							continue;
						}
						//URL encode
						if (($match[1][$r][0]=='%')&&(isset($result[$match[2][$r][0]]))) {
							$__textoEval.=urlencode($result[$match[2][$r][0]]);
							continue;
						}
						//Double quoted parameter
						if (($match[1][$r][0]=='Q')&&(isset($result[$match[2][$r][0]]))) {
							$__textoEval.='"'.addcslashes($result[$match[2][$r][0]],'\\"').'"';
							continue;
						}
						//Single quoted parameter
						if (($match[1][$r][0]=='q')&&(isset($result[$match[2][$r][0]]))) {
							$__textoEval.="'".addcslashes($result[$match[2][$r][0]],'\\\'')."'";
							continue;
						}
						//Substring (Sub replaceDataField)
						if (($match[1][$r][0]=='!')&&(isset($result[$match[2][$r][0]]))) {
							$__textoEval.=Bootstrap::replaceDataField($result[$match[2][$r][0]],$result);
							continue;
						}
						//Call function
						if (($match[1][$r][0]==='')&&($match[2][$r][0]==='')&&($match[3][$r][0]!=='')) {
							eval('$strAux = ' . $match[3][$r][0] . '(\'' . addcslashes(Bootstrap::replaceDataField(stripslashes($match[4][$r][0]),$result),'\\\'') . '\');');

							if ($match[3][$r][0] == "Bootstrap::LoadTranslation") {
								$arraySearch  = array("'");
								$arrayReplace = array("\\'");
								$strAux = str_replace($arraySearch, $arrayReplace, $strAux);
							}

							$__textoEval .= $strAux;
							continue;
						}
						//Non-quoted
						if (($match[1][$r][0]=='#')&&(isset($result[$match[2][$r][0]]))) {
							$__textoEval.=Bootstrap::replaceDataField($result[$match[2][$r][0]],$result);
							continue;
						}
						//Non-quoted =
						if (($match[1][$r][0]=='=')&&(isset($result[$match[2][$r][0]]))) {
							$__textoEval.=Bootstrap::replaceDataField($result[$match[2][$r][0]],$result);
							continue;
						}
					}
				}
			}
			$__textoEval.=substr($sqlString,$u);
			return $__textoEval;
		}

		/**
		 * microtime_float
		 *
		 * @return array_sum(explode(' ',microtime()))
		 */
		/*public static*/
		public function microtime_float ()
		{
			return array_sum( explode( ' ', microtime() ) );
		}

		/**
		 * Return the System defined constants and Application variables
		 *   Constants: SYS_*
		 *   Sessions : USER_* , URS_*
		 */
		public function getSystemConstants($params = null)
		{
			$t1 = Bootstrap::microtime_float();
			$sysCon = array();

			if (defined("SYS_LANG")) {
				$sysCon["SYS_LANG"] = SYS_LANG;
			}

			if (defined("SYS_SKIN")) {
				$sysCon["SYS_SKIN"] = SYS_SKIN;
			}

			if (defined("SYS_SYS")) {
				$sysCon["SYS_SYS"] = SYS_SYS;
			}

			$sysCon["APPLICATION"]  = (isset($_SESSION["APPLICATION"]))?  $_SESSION["APPLICATION"]  : "";
			$sysCon["PROCESS"]      = (isset($_SESSION["PROCESS"]))?      $_SESSION["PROCESS"]      : "";
			$sysCon["TASK"]         = (isset($_SESSION["TASK"]))?         $_SESSION["TASK"]         : "";
			$sysCon["INDEX"]        = (isset($_SESSION["INDEX"]))?        $_SESSION["INDEX"]        : "";
			$sysCon["USER_LOGGED"]  = (isset($_SESSION["USER_LOGGED"]))?  $_SESSION["USER_LOGGED"]  : "";
			$sysCon["USR_USERNAME"] = (isset($_SESSION["USR_USERNAME"]))? $_SESSION["USR_USERNAME"] : "";

			//###############################################################################################
			// Added for compatibility betweek aplication called from web Entry that uses just WS functions
			//###############################################################################################

			if ($params != null) {
				if (isset($params->option)) {
					switch ($params->option) {
						case "STORED SESSION":
							if (isset($params->SID)) {
								Bootstrap::LoadClass("sessions");

								$oSessions = new Sessions($params->SID);
								$sysCon = array_merge($sysCon, $oSessions->getGlobals());
							}
							break;
					}
				}

				if (isset($params->appData) && is_array($params->appData)) {
					$sysCon["APPLICATION"] = $params->appData["APPLICATION"];
					$sysCon["PROCESS"]     = $params->appData["PROCESS"];
					$sysCon["TASK"]        = $params->appData["TASK"];
					$sysCon["INDEX"]       = $params->appData["INDEX"];

					if (empty($sysCon["USER_LOGGED"])) {
						$sysCon["USER_LOGGED"]  = $params->appData["USER_LOGGED"];
						$sysCon["USR_USERNAME"] = $params->appData["USR_USERNAME"];
					}
				}
			}

			return $sysCon;
		}

		/**
		 * Escapes special characters in a string for use in a SQL statement
		 * @author David Callizaya <calidavidx21@hotmail.com>
		 * @param string $sqlString  The string to be escaped
		 * @param string $DBEngine   Target DBMS
		 */
		public function sqlEscape ($sqlString, $DBEngine = DB_ADAPTER)
		{
			$DBEngine = DB_ADAPTER;
			switch ($DBEngine) {
				case 'mysql':
					$con = Propel::getConnection( 'workflow' );
					return mysql_real_escape_string( stripslashes( $sqlString ), $con->getResource() );
					break;
				case 'myxml':
					$sqlString = str_replace( '"', '""', $sqlString );
					return str_replace( "'", "''", $sqlString );
					break;
				default:
					return addslashes( stripslashes( $sqlString ) );
					break;
			}
		}

		/**
		 * Load a template
		 *
		 * @author Fernando Ontiveros Lira <fernando@colosa.com>
		 * @access public
		 * @param string $strTemplateName
		 * @return void
		 */
		public function LoadTemplate ($strTemplateName)
		{
			if ($strTemplateName == '') {
				return;
			}

			$temp = $strTemplateName . ".php";
			$file = Bootstrap::ExpandPath( 'templates' ) . $temp;
			// Check if its a user template
			if (file_exists( $file )) {
				//require_once( $file );
				include ($file);
			} else {
				// Try to get the global system template
				$file = PATH_TEMPLATE . PATH_SEP . $temp;
				//require_once( $file );
				if (file_exists( $file )) {
					include ($file);
				}
			}
		}

		/**
		 * verify path
		 *
		 * @author Fernando Ontiveros Lira <fernando@colosa.com>
		 * @access public
		 * @param string $strPath path
		 * @param boolean $createPath if true this public function will create the path
		 * @return boolean
		 */
		public function verifyPath ($strPath, $createPath = false)
		{
			$folder_path = strstr( $strPath, '.' ) ? dirname( $strPath ) : $strPath;

			if (file_exists( $strPath ) || @is_dir( $strPath )) {
				return true;
			} else {
				if ($createPath) {
					//TODO:: Define Environment constants: Devel (0777), Production (0770), ...
					Bootstrap::mk_dir( $strPath, 0777 );
				} else {
					return false;
				}
			}
			return false;
		}

		/**
		 * getformatedDate
		 *
		 * @param date $date
		 * @param string $format default value 'yyyy-mm-dd',
		 * @param string $lang default value ''
		 *
		 * @return string $ret
		 */
		public function getformatedDate ($date, $format = 'yyyy-mm-dd', $lang = '')
		{
			/**
			 * ******************************************************************************************************
			 * if the year is 2008 and the format is yy then -> 08
			 * if the year is 2008 and the format is yyyy then -> 2008
			 *
			 * if the month is 05 and the format is mm then -> 05
			 * if the month is 05 and the format is m and the month is less than 10 then -> 5 else digit normal
			 * if the month is 05 and the format is MM or M then -> May
			 *
			 * if the day is 5 and the format is dd then -> 05
			 * if the day is 5 and the format is d and the day is less than 10 then -> 5 else digit normal
			 * if the day is 5 and the format is DD or D then -> five
			 * *******************************************************************************************************
			 */

			//scape the literal
			switch ($lang) {
				case 'es':
					$format = str_replace( ' de ', '[of]', $format );
					break;
			}

			//first we must formatted the string
			$format = str_replace( 'yyyy', '{YEAR}', $format );
			$format = str_replace( 'yy', '{year}', $format );

			$format = str_replace( 'mm', '{YONTH}', $format );
			$format = str_replace( 'm', '{month}', $format );
			$format = str_replace( 'M', '{XONTH}', $format );

			$format = str_replace( 'dd', '{DAY}', $format );
			$format = str_replace( 'd', '{day}', $format );

			$format = str_replace( 'h', '{h}', $format );
			$format = str_replace( 'i', '{i}', $format );
			$format = str_replace( 's', '{s}', $format );

			if ($lang === '') {
				$lang = defined( SYS_LANG ) ? SYS_LANG : 'en';
			}
			$aux = explode( ' ', $date ); //para dividir la fecha del dia
			$date = explode( '-', isset( $aux[0] ) ? $aux[0] : '00-00-00' ); //para obtener los dias, el mes, y el año.
			$time = explode( ':', isset( $aux[1] ) ? $aux[1] : '00:00:00' ); //para obtener las horas, minutos, segundos.


			$year = (int) ((isset( $date[0] )) ? $date[0] : '0'); //year
			$month = (int) ((isset( $date[1] )) ? $date[1] : '0'); //month
			$day = (int) ((isset( $date[2] )) ? $date[2] : '0'); //day


			$h = isset( $time[0] ) ? $time[0] : '00'; //hour
			$i = isset( $time[1] ) ? $time[1] : '00'; //minute
			$s = isset( $time[2] ) ? $time[2] : '00'; //second


			$MONTHS = Array ();
			for ($i = 1; $i <= 12; $i ++) {
				$MONTHS[$i] = Bootstrap::LoadTranslation( "ID_MONTH_$i", $lang );
			}

			$d = (int) $day;
			$dd = Bootstrap::complete_field( $day, 2, 1 );

			//missing D


			$M = $MONTHS[$month];
			$m = (int) $month;
			$mm = Bootstrap::complete_field( $month, 2, 1 );

			$yy = substr( $year, strlen( $year ) - 2, 2 );
			$yyyy = $year;

			$names = array ('{day}','{DAY}','{month}','{YONTH}','{XONTH}','{year}','{YEAR}','{h}','{i}','{s}'
			);
			$values = array ($d,$dd,$m,$mm,$M,$yy,$yyyy,$h,$i,$s
			);

			$ret = str_replace( $names, $values, $format );

			//recovering the original literal
			switch ($lang) {
				case 'es':
					$ret = str_replace( '[of]', ' de ', $ret );
					break;
			}

			return $ret;
		}

		/**
		 *
		 * @author Erik Amaru Ortiz <erik@colosa.com>
		 * @name complete_field($string, $lenght, $type={1:number/2:string/3:float})
		 */
		public function complete_field ($campo, $long, $tipo)
		{
			$campo = trim( $campo );
			switch ($tipo) {
				case 1: //number
					$long = $long - strlen( $campo );
					for ($i = 1; $i <= $long; $i ++) {
						$campo = "0" . $campo;
					}
					break;
				case 2: //string
					$long = $long - strlen( $campo );
					for ($i = 1; $i <= $long; $i ++) {
						$campo = " " . $campo;
					}
					break;
				case 3: //float
					if ($campo != "0") {
						$vals = explode( ".", $long );
						$ints = $vals[0];

						$decs = $vals[1];

						$valscampo = explode( ".", $campo );

						$intscampo = $valscampo[0];
						$decscampo = $valscampo[1];

						$ints = $ints - strlen( $intscampo );

						for ($i = 1; $i <= $ints; $i ++) {
							$intscampo = "0" . $intscampo;
						}

						//los decimales pueden ser 0 uno o dos
						$decs = $decs - strlen( $decscampo );
						for ($i = 1; $i <= $decs; $i ++) {
							$decscampo = $decscampo . "0";
						}

						$campo = $intscampo . "." . $decscampo;
					} else {
						$vals = explode( ".", $long );
						$ints = $vals[0];
						$decs = $vals[1];

						$campo = "";
						for ($i = 1; $i <= $ints; $i ++) {
							$campo = "0" . $campo;
						}
						$campod = "";
						for ($i = 1; $i <= $decs; $i ++) {
							$campod = "0" . $campod;
						}

						$campo = $campo . "." . $campod;
					}
					break;
			}
			return $campo;
		}


		/**
		 * evalJScript
		 *
		 * @param string $c
		 *
		 * @return void
		 */
		public function evalJScript ($c)
		{
			print ("<script language=\"javascript\">{$c}</script>") ;
		}

		/**
		 * Generate random number
		 *
		 * @author Fernando Ontiveros Lira <fernando@colosa.com>
		 * @access public
		 * @return int
		 */
		public function generateUniqueID ()
		{
		    do {
		        $sUID = str_replace( '.', '0', uniqid( rand( 0, 999999999 ), true ) );
		    } while (strlen( $sUID ) != 32);
		    return $sUID;
		    //return strtoupper(substr(uniqid(rand(0, 9), false),0,14));
		}

		/**
		 * Encrypt URL
		 *
		 * @author Fernando Ontiveros Lira <fernando@colosa.com>
		 * @access public
		 * @param string $urlLink
		 * @return string
		 */
		public function encryptlink ($url)
		{
		    if (defined( 'ENABLE_ENCRYPT' ) && ENABLE_ENCRYPT == 'yes') {
		        return urlencode( Bootstrap::encrypt( $url, URL_KEY ) );
		    } else {
		        return $url;
		    }
		}
    /**
    * isWinOs
    *
    * @return true if the 3 first letters of PHP_OS got 'WIN', otherwise false.
    */
    function isWinOs()
    {
        return strtoupper(substr(PHP_OS, 0, 3)) == "WIN";
    }
    /**
    * isNTOs
    *
    * @return true if PHP_OS is 'WINNT', otherwise false.
    */
    function isNTOs()
    {
        return PHP_OS == "WINNT";
    }
    /**
    * isLinuxOs
    *
    * @return true if PHP_OS (upper text) got 'LINUX', otherwise false.
    */
    function isLinuxOs()
    {
        return strtoupper(PHP_OS) == "LINUX";
    }
}

