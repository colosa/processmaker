<?php
// ProcessMaker Test Unit Bootstrap

// Defining the PATH_SEP constant, he we are defining if the the path separator symbol will be '\\' or '/'
define('PATH_SEP', '/');

if (!defined('__DIR__')) {
  define ('__DIR__', dirname(__FILE__));
}

// Defining the Home Directory
define('PATH_TRUNK', realpath(__DIR__ . '/../') . PATH_SEP);
define('PATH_HOME',  PATH_TRUNK . 'workflow' . PATH_SEP);

define('SYS_SYS', $GLOBALS['SYS_SYS']);
define('SYS_LANG', $GLOBALS['SYS_LANG']);
define('SYS_SKIN', $GLOBALS['SYS_SKIN']);
//define('DB_ADAPTER', $GLOBALS['DB_ADAPTER']);
//define('DB_NAME', $GLOBALS['DB_NAME']);
//define('DB_USER', $GLOBALS['DB_USER']);
//define('DB_PASS', $GLOBALS['DB_PASS']);
//define('DB_HOST', $GLOBALS['DB_HOST']);
define('PATH_DB', $GLOBALS['PATH_DB']);
define('PATH_DATA', $GLOBALS['PATH_DATA']);
define('PATH_C', PATH_TRUNK . 'tmp/' );
define('PATH_SMARTY_C', PATH_TRUNK . 'tmp/' );
define('PATH_SMARTY_CACHE', PATH_TRUNK . 'tmp/' );

@mkdir (PATH_C);

//require  PATH_HOME . 'engine' . PATH_SEP . 'config' . PATH_SEP . 'paths.php';
  // Defining RBAC Paths constants
  define( 'PATH_RBAC_HOME',     PATH_TRUNK . 'rbac' . PATH_SEP );

  // Defining Gulliver framework paths constants
  define( 'PATH_GULLIVER_HOME', PATH_TRUNK . 'gulliver'  . PATH_SEP );
  define( 'PATH_GULLIVER',      PATH_GULLIVER_HOME . 'system' . PATH_SEP );   //gulliver system classes
  define( 'PATH_GULLIVER_BIN',  PATH_GULLIVER_HOME . 'bin' . PATH_SEP );   //gulliver bin classes
  define( 'PATH_TEMPLATE',      PATH_GULLIVER_HOME . 'templates' . PATH_SEP );
  define( 'PATH_THIRDPARTY',    PATH_GULLIVER_HOME . 'thirdparty' . PATH_SEP );
  define( 'PATH_RBAC',          PATH_RBAC_HOME .     'engine'  . PATH_SEP . 'classes' . PATH_SEP );  //to enable rbac version 2
  define( 'PATH_RBAC_CORE',     PATH_RBAC_HOME .     'engine'  . PATH_SEP );
  define( 'PATH_HTML',          PATH_HOME .          'public_html' . PATH_SEP );

  // Defining PMCore Path constants
  define( 'PATH_CORE',        PATH_HOME . 'engine'       . PATH_SEP );
  define( 'PATH_SKINS',       PATH_CORE . 'skins'        . PATH_SEP );
  define( 'PATH_SKIN_ENGINE', PATH_CORE . 'skinEngine'   . PATH_SEP );
  define( 'PATH_METHODS',     PATH_CORE . 'methods'      . PATH_SEP );
  define( 'PATH_XMLFORM',     PATH_CORE . 'xmlform'      . PATH_SEP );
  define( 'PATH_CONFIG',      PATH_CORE . 'config'       . PATH_SEP );
  define( 'PATH_PLUGINS',     PATH_CORE . 'plugins'      . PATH_SEP  );
  define( 'PATH_HTMLMAIL',    PATH_CORE . 'html_templates' . PATH_SEP );
  define( 'PATH_TPL',         PATH_CORE . 'templates'    . PATH_SEP );
  define( 'PATH_TEST',        PATH_CORE . 'test'         . PATH_SEP );
  define( 'PATH_FIXTURES',    PATH_TEST . 'fixtures'     . PATH_SEP );
  define( 'PATH_RTFDOCS' ,    PATH_CORE . 'rtf_templates' . PATH_SEP );
  define( 'PATH_DYNACONT',    PATH_CORE . 'content' . PATH_SEP . 'dynaform' . PATH_SEP );
  //define( 'PATH_LANGUAGECONT',PATH_CORE . 'content' . PATH_SEP . 'languages' . PATH_SEP );
  define( 'SYS_UPLOAD_PATH',  PATH_HOME . "public_html/files/" );
  define( 'PATH_UPLOAD',      PATH_HTML . 'files' . PATH_SEP);

  define( 'PATH_WORKFLOW_MYSQL_DATA', PATH_CORE . 'data' . PATH_SEP.'mysql'.PATH_SEP);
  define( 'PATH_RBAC_MYSQL_DATA',     PATH_RBAC_CORE . 'data' . PATH_SEP.'mysql'.PATH_SEP);
  define( 'FILE_PATHS_INSTALLED',     PATH_CORE . 'config' . PATH_SEP . 'paths_installed.php' );
  define( 'PATH_WORKFLOW_MSSQL_DATA', PATH_CORE . 'data' . PATH_SEP.'mssql'.PATH_SEP);
  define( 'PATH_RBAC_MSSQL_DATA',     PATH_RBAC_CORE . 'data' . PATH_SEP.'mssql'.PATH_SEP);
  define( 'PATH_CONTROLLERS',         PATH_CORE . 'controllers' . PATH_SEP );
  define( 'PATH_SERVICES_REST',       PATH_CORE . 'services' . PATH_SEP . 'rest' . PATH_SEP);

  define("URL_KEY", 'c0l0s40pt1mu59r1m3' );

set_include_path(
    PATH_CORE . PATH_SEPARATOR .
    PATH_THIRDPARTY . PATH_SEPARATOR .
    PATH_THIRDPARTY . 'pear'. PATH_SEPARATOR .
    PATH_RBAC_CORE . PATH_SEPARATOR .
    get_include_path()
);
  // include Gulliver Class
//  require_once( PATH_GULLIVER . PATH_SEP . 'class.g.php');

// perpare propel env.
//require_once "propel/Propel.php";
//require_once "creole/Creole.php";


//initialize required classes
//G::LoadClass ('dbtable');
//G::LoadClass ('system');


//testing the autoloader feature
spl_autoload_register(array('Bootstrap', 'autoloadClass'));
Bootstrap::registerClass('headPublisher', PATH_GULLIVER . "class.headPublisher.php");
Bootstrap::registerClass('G', PATH_GULLIVER . "class.g.php");
Bootstrap::registerClass('publisher', PATH_GULLIVER . "class.publisher.php");
Bootstrap::registerClass('xmlform', PATH_GULLIVER . "class.xmlform.php");
Bootstrap::registerClass('XmlForm_Field', PATH_GULLIVER . "class.xmlform.php");
Bootstrap::registerClass('xmlformExtension', PATH_GULLIVER . "class.xmlformExtension.php");
Bootstrap::registerClass('form',         PATH_GULLIVER . "class.form.php");
Bootstrap::registerClass('menu',         PATH_GULLIVER . "class.menu.php");
Bootstrap::registerClass('Xml_Document', PATH_GULLIVER . "class.xmlDocument.php");
Bootstrap::registerClass('DBSession',    PATH_GULLIVER . "class.dbsession.php");
Bootstrap::registerClass('DBConnection', PATH_GULLIVER . "class.dbconnection.php");
Bootstrap::registerClass('DBRecordset',  PATH_GULLIVER . "class.dbrecordset.php");
Bootstrap::registerClass('DBTable',      PATH_GULLIVER . "class.dbtable.php");
Bootstrap::registerClass('xmlMenu',      PATH_GULLIVER . "class.xmlMenu.php");
Bootstrap::registerClass('XmlForm_Field_XmlMenu', PATH_GULLIVER . "class.xmlMenu.php");
Bootstrap::registerClass('XmlForm_Field_HTML',  PATH_GULLIVER . "class.dvEditor.php");
Bootstrap::registerClass('XmlForm_Field_WYSIWYG_EDITOR',  PATH_GULLIVER . "class.wysiwygEditor.php");
Bootstrap::registerClass('Controller',          PATH_GULLIVER . "class.controller.php");
Bootstrap::registerClass('HttpProxyController', PATH_GULLIVER . "class.httpProxyController.php");
Bootstrap::registerClass('templatePower',            PATH_GULLIVER . "class.templatePower.php");
Bootstrap::registerClass('XmlForm_Field_SimpleText', PATH_GULLIVER . "class.xmlformExtension.php");
Bootstrap::registerClass('System',        PATH_HOME . "engine/classes/class.system.php");
Bootstrap::registerClass('Propel',          PATH_THIRDPARTY . "propel/Propel.php");
Bootstrap::registerClass('Creole',          PATH_THIRDPARTY . "creole/Creole.php");
Bootstrap::registerClass('Groups',       PATH_HOME . "engine/classes/class.groups.php");
Bootstrap::registerClass('Tasks',        PATH_HOME . "engine/classes/class.tasks.php");
Bootstrap::registerClass('Calendar',     PATH_HOME . "engine/classes/class.calendar.php");
Bootstrap::registerClass('processMap',   PATH_HOME . "engine/classes/class.processMap.php");

//DATABASE propel classes used in 'Cases' Options
Bootstrap::registerClass('Entity_Base',        PATH_HOME . "engine/classes/entities/Base.php");

Bootstrap::registerClass('BaseContent',        PATH_HOME . "engine/classes/model/om/BaseContent.php");
Bootstrap::registerClass('Content',            PATH_HOME . "engine/classes/model/Content.php");
Bootstrap::registerClass('BaseContentPeer',    PATH_HOME . "engine/classes/model/om/BaseContentPeer.php");
Bootstrap::registerClass('ContentPeer',        PATH_HOME . "engine/classes/model/ContentPeer.php");
Bootstrap::registerClass('BaseApplication',    PATH_HOME . "engine/classes/model/om/BaseApplication.php");
Bootstrap::registerClass('ApplicationPeer',    PATH_HOME . "engine/classes/model/ApplicationPeer.php");
Bootstrap::registerClass('Application',        PATH_HOME . "engine/classes/model/Application.php");

Bootstrap::registerClass('BaseAppDelegation',  PATH_HOME . "engine/classes/model/om/BaseAppDelegation.php");
Bootstrap::registerClass('BaseHoliday',        PATH_HOME . "engine/classes/model/om/BaseHoliday.php");
Bootstrap::registerClass('BaseHolidayPeer',    PATH_HOME . "engine/classes/model/om/BaseHolidayPeer.php");
Bootstrap::registerClass('BaseTask',           PATH_HOME . "engine/classes/model/om/BaseTask.php");
Bootstrap::registerClass('BaseTaskPeer',       PATH_HOME . "engine/classes/model/om/BaseTaskPeer.php");
Bootstrap::registerClass('HolidayPeer',        PATH_HOME . "engine/classes/model/HolidayPeer.php");
Bootstrap::registerClass('Holiday',            PATH_HOME . "engine/classes/model/Holiday.php");
Bootstrap::registerClass('Task',               PATH_HOME . "engine/classes/model/Task.php");
Bootstrap::registerClass('TaskPeer',           PATH_HOME . "engine/classes/model/TaskPeer.php");
Bootstrap::registerClass('dates',              PATH_HOME . "engine/classes/class.dates.php");
Bootstrap::registerClass('AppDelegation',      PATH_HOME . "engine/classes/model/AppDelegation.php");
Bootstrap::registerClass('AppDelegationPeer',  PATH_HOME . "engine/classes/model/AppDelegationPeer.php");
Bootstrap::registerClass('BaseAppDelay',       PATH_HOME . "engine/classes/model/om/BaseAppDelay.php");
Bootstrap::registerClass('AppDelayPeer',       PATH_HOME . "engine/classes/model/AppDelayPeer.php");
Bootstrap::registerClass('AppDelay',           PATH_HOME . "engine/classes/model/AppDelay.php");
Bootstrap::registerClass('BaseAdditionalTables',PATH_HOME . "engine/classes/model/om/BaseAdditionalTables.php");
Bootstrap::registerClass('AdditionalTables',   PATH_HOME . "engine/classes/model/AdditionalTables.php");
Bootstrap::registerClass('BaseAppCacheView',   PATH_HOME . "engine/classes/model/om/BaseAppCacheView.php");
Bootstrap::registerClass('AppCacheView',       PATH_HOME . "engine/classes/model/AppCacheView.php");
Bootstrap::registerClass('BaseAppCacheViewPeer',PATH_HOME . "engine/classes/model/om/BaseAppCacheViewPeer.php");
Bootstrap::registerClass('AppCacheViewPeer',   PATH_HOME . "engine/classes/model/AppCacheViewPeer.php");

Bootstrap::registerClass('BaseInputDocument',  PATH_HOME . "engine/classes/model/om/BaseInputDocument.php");
Bootstrap::registerClass('InputDocument',      PATH_HOME . "engine/classes/model/InputDocument.php");
Bootstrap::registerClass('BaseAppDocument',    PATH_HOME . "engine/classes/model/om/BaseAppDocument.php");
Bootstrap::registerClass('AppDocument',        PATH_HOME . "engine/classes/model/AppDocument.php");
Bootstrap::registerClass('AppDocumentPeer',    PATH_HOME . "engine/classes/model/AppDocumentPeer.php");

Bootstrap::registerClass('BaseAppEvent',       PATH_HOME . "engine/classes/model/om/BaseAppEvent.php");
Bootstrap::registerClass('AppEvent',           PATH_HOME . "engine/classes/model/AppEvent.php");
Bootstrap::registerClass('AppEventPeer',       PATH_HOME . "engine/classes/model/AppEventPeer.php");

Bootstrap::registerClass('BaseAppHistory',     PATH_HOME . "engine/classes/model/om/BaseAppHistory.php");
Bootstrap::registerClass('AppHistory',         PATH_HOME . "engine/classes/model/AppHistory.php");
Bootstrap::registerClass('AppHistoryPeer',     PATH_HOME . "engine/classes/model/AppHistoryPeer.php");

Bootstrap::registerClass('BaseAppFolder',      PATH_HOME . "engine/classes/model/om/BaseAppFolder.php");
Bootstrap::registerClass('AppFolder',          PATH_HOME . "engine/classes/model/AppFolder.php");
Bootstrap::registerClass('AppFolderPeer',      PATH_HOME . "engine/classes/model/AppFolderPeer.php");

Bootstrap::registerClass('BaseAppMessage',     PATH_HOME . "engine/classes/model/om/BaseAppMessage.php");
Bootstrap::registerClass('AppMessage',         PATH_HOME . "engine/classes/model/AppMessage.php");
Bootstrap::registerClass('BaseAppMessagePeer', PATH_HOME . "engine/classes/model/om/BaseAppMessagePeer.php");
Bootstrap::registerClass('AppMessagePeer',     PATH_HOME . "engine/classes/model/AppMessagePeer.php");

Bootstrap::registerClass('BaseAppNotes',        PATH_HOME . "engine/classes/model/om/BaseAppNotes.php");
Bootstrap::registerClass('AppNotes',            PATH_HOME . "engine/classes/model/AppNotes.php");
Bootstrap::registerClass('AppNotesPeer',        PATH_HOME . "engine/classes/model/AppNotesPeer.php");

Bootstrap::registerClass('BaseAppOwner',        PATH_HOME . "engine/classes/model/om/BaseAppOwner.php");
Bootstrap::registerClass('AppOwner',            PATH_HOME . "engine/classes/model/AppOwner.php");
Bootstrap::registerClass('AppOwnerPeer',        PATH_HOME . "engine/classes/model/AppOwnerPeer.php");

Bootstrap::registerClass('BaseAppSolrQueue',    PATH_HOME . "engine/classes/model/om/BaseAppSolrQueue.php");
Bootstrap::registerClass('Entity_AppSolrQueue', PATH_HOME . "engine/classes/entities/AppSolrQueue.php");
Bootstrap::registerClass('AppSolrQueue',        PATH_HOME . "engine/classes/model/AppSolrQueue.php");
Bootstrap::registerClass('AppSolrQueuePeer',    PATH_HOME . "engine/classes/model/AppSolrQueuePeer.php");

Bootstrap::registerClass('BaseAppThread',       PATH_HOME . "engine/classes/model/om/BaseAppThread.php");
Bootstrap::registerClass('AppThread',           PATH_HOME . "engine/classes/model/AppThread.php");
Bootstrap::registerClass('AppThreadPeer',       PATH_HOME . "engine/classes/model/AppThreadPeer.php");

Bootstrap::registerClass('BaseCaseScheduler',   PATH_HOME . "engine/classes/model/om/BaseCaseScheduler.php");
Bootstrap::registerClass('CaseScheduler',       PATH_HOME . "engine/classes/model/CaseScheduler.php");

Bootstrap::registerClass('BaseCaseTracker',     PATH_HOME . "engine/classes/model/om/BaseCaseTracker.php");
Bootstrap::registerClass('CaseTracker',         PATH_HOME . "engine/classes/model/CaseTracker.php");

Bootstrap::registerClass('BaseCaseTrackerObject',PATH_HOME . "engine/classes/model/om/BaseCaseTrackerObject.php");
Bootstrap::registerClass('CaseTrackerObject',    PATH_HOME . "engine/classes/model/CaseTrackerObject.php");

Bootstrap::registerClass('BaseConfiguration',   PATH_HOME . "engine/classes/model/om/BaseConfiguration.php");
Bootstrap::registerClass('Configuration',       PATH_HOME . "engine/classes/model/Configuration.php");

Bootstrap::registerClass('BaseDbSource',        PATH_HOME . "engine/classes/model/om/BaseDbSource.php");
Bootstrap::registerClass('DbSource',            PATH_HOME . "engine/classes/model/DbSource.php");

Bootstrap::registerClass('XMLDB',              PATH_HOME . "engine/classes/class.xmlDb.php");
Bootstrap::registerClass('dynaFormHandler',    PATH_GULLIVER . "class.dynaformhandler.php");
Bootstrap::registerClass('DynaFormField',      PATH_HOME . "engine/classes/class.dynaFormField.php");
Bootstrap::registerClass('BaseDynaform',       PATH_HOME . "engine/classes/model/om/BaseDynaform.php");
Bootstrap::registerClass('Dynaform',           PATH_HOME . "engine/classes/model/Dynaform.php");
Bootstrap::registerClass('DynaformPeer',       PATH_HOME . "engine/classes/model/DynaformPeer.php");

Bootstrap::registerClass('BaseEvent',          PATH_HOME . "engine/classes/model/om/BaseEvent.php");
Bootstrap::registerClass('Event',              PATH_HOME . "engine/classes/model/Event.php");

Bootstrap::registerClass('BaseEventPeer',      PATH_HOME . "engine/classes/model/om/BaseEventPeer.php");
Bootstrap::registerClass('EventPeer',          PATH_HOME . "engine/classes/model/EventPeer.php");

Bootstrap::registerClass('BaseFields',         PATH_HOME . "engine/classes/model/om/BaseFields.php");
Bootstrap::registerClass('Fields',             PATH_HOME . "engine/classes/model/Fields.php");

Bootstrap::registerClass('BaseGateway',        PATH_HOME . "engine/classes/model/om/BaseGateway.php");
Bootstrap::registerClass('Gateway',            PATH_HOME . "engine/classes/model/Gateway.php");

Bootstrap::registerClass('BaseGroupUser',      PATH_HOME . "engine/classes/model/om/BaseGroupUser.php");
Bootstrap::registerClass('Groupwf',            PATH_HOME . "engine/classes/model/Groupwf.php");
Bootstrap::registerClass('GroupUser',          PATH_HOME . "engine/classes/model/GroupUser.php");

Bootstrap::registerClass('BaseInputDocumentPeer',PATH_HOME . 'engine/classes/model/om/BaseInputDocumentPeer.php');
Bootstrap::registerClass('InputDocumentPeer',  PATH_HOME . 'engine/classes/model/InputDocumentPeer.php');

Bootstrap::registerClass('BaseIsoCountry',     PATH_HOME . "engine/classes/model/om/BaseIsoCountry.php");
Bootstrap::registerClass('IsoCountry',         PATH_HOME . "engine/classes/model/IsoCountry.php");
Bootstrap::registerClass('BaseTranslation',    PATH_HOME . "engine/classes/model/om/BaseTranslation.php");
Bootstrap::registerClass('Translation',        PATH_HOME . "engine/classes/model/Translation.php");
Bootstrap::registerClass('BaseLanguage',       PATH_HOME . "engine/classes/model/om/BaseLanguage.php");
Bootstrap::registerClass('Language',           PATH_HOME . "engine/classes/model/Language.php");

Bootstrap::registerClass('BaseLogCasesScheduler',PATH_HOME . "engine/classes/model/om/BaseLogCasesScheduler.php");
Bootstrap::registerClass('LogCasesScheduler',  PATH_HOME . "engine/classes/model/LogCasesScheduler.php");

Bootstrap::registerClass('BaseObjectPermission',PATH_HOME . "engine/classes/model/om/BaseObjectPermission.php");
Bootstrap::registerClass('ObjectPermission',    PATH_HOME . "engine/classes/model/ObjectPermission.php");
Bootstrap::registerClass('ObjectPermissionPeer',PATH_HOME . "engine/classes/model/ObjectPermissionPeer.php");

Bootstrap::registerClass('BaseOutputDocument',  PATH_HOME . "engine/classes/model/om/BaseOutputDocument.php");
Bootstrap::registerClass('OutputDocument',      PATH_HOME . "engine/classes/model/OutputDocument.php");
Bootstrap::registerClass('OutputDocumentPeer',  PATH_HOME . "engine/classes/model/OutputDocumentPeer.php");

Bootstrap::registerClass('BaseProcess',         PATH_HOME . "engine/classes/model/om/BaseProcess.php");
Bootstrap::registerClass('BaseProcessCategory', PATH_HOME . "engine/classes/model/om/BaseProcessCategory.php");
Bootstrap::registerClass('ProcessCategory',     PATH_HOME . "engine/classes/model/ProcessCategory.php");
Bootstrap::registerClass('ProcessCategoryPeer', PATH_HOME . "engine/classes/model/ProcessCategoryPeer.php");
Bootstrap::registerClass('ProcessPeer',         PATH_HOME . "engine/classes/model/ProcessPeer.php");
Bootstrap::registerClass('Process',             PATH_HOME . "engine/classes/model/Process.php");

Bootstrap::registerClass('BaseProcessUser',     PATH_HOME . "engine/classes/model/om/BaseProcessUser.php");
Bootstrap::registerClass('ProcessUser',         PATH_HOME . "engine/classes/model/ProcessUser.php");

Bootstrap::registerClass('BaseProcessUserPeer', PATH_HOME . "engine/classes/model/om/BaseProcessUserPeer.php");
Bootstrap::registerClass('ProcessUserPeer',     PATH_HOME . "engine/classes/model/ProcessUserPeer.php");

Bootstrap::registerClass('BaseReportTable',     PATH_HOME . "engine/classes/model/om/BaseReportTable.php");
Bootstrap::registerClass('ReportTable',         PATH_HOME . "engine/classes/model/ReportTable.php");
Bootstrap::registerClass('ReportTablePeer',     PATH_HOME . "engine/classes/model/ReportTablePeer.php");

Bootstrap::registerClass('BaseReportVar',       PATH_HOME . "engine/classes/model/om/BaseReportVar.php");
Bootstrap::registerClass('ReportVar',           PATH_HOME . "engine/classes/model/ReportVar.php");

Bootstrap::registerClass('BaseRoute',           PATH_HOME . "engine/classes/model/om/BaseRoute.php");
Bootstrap::registerClass('Route',               PATH_HOME . "engine/classes/model/Route.php");
Bootstrap::registerClass('RoutePeer',           PATH_HOME . "engine/classes/model/RoutePeer.php");

Bootstrap::registerClass('BaseStep',            PATH_HOME . "engine/classes/model/om/BaseStep.php");
Bootstrap::registerClass('Step',                PATH_HOME . "engine/classes/model/Step.php");
Bootstrap::registerClass('StepPeer',            PATH_HOME . "engine/classes/model/StepPeer.php");

Bootstrap::registerClass('BaseStepSupervisor',  PATH_HOME . "engine/classes/model/om/BaseStepSupervisor.php");
Bootstrap::registerClass('StepSupervisor',      PATH_HOME . "engine/classes/model/StepSupervisor.php");

Bootstrap::registerClass('BaseStepSupervisorPeer',PATH_HOME . "engine/classes/model/om/BaseStepSupervisorPeer.php");
Bootstrap::registerClass('StepSupervisorPeer',  PATH_HOME . "engine/classes/model/StepSupervisorPeer.php");

Bootstrap::registerClass('BaseStepTrigger',     PATH_HOME . "engine/classes/model/om/BaseStepTrigger.php");
Bootstrap::registerClass('StepTrigger',         PATH_HOME . "engine/classes/model/StepTrigger.php");
Bootstrap::registerClass('StepTriggerPeer',     PATH_HOME . "engine/classes/model/StepTriggerPeer.php");

Bootstrap::registerClass('SolrRequestData',     PATH_HOME . "engine/classes/entities/SolrRequestData.php");

Bootstrap::registerClass('SolrUpdateDocument',  PATH_HOME . "engine/classes/entities/SolrUpdateDocument.php");

Bootstrap::registerClass('BaseSwimlanesElements',PATH_HOME . "engine/classes/model/om/BaseSwimlanesElements.php");
Bootstrap::registerClass('SwimlanesElements',   PATH_HOME . "engine/classes/model/SwimlanesElements.php");
Bootstrap::registerClass('BaseSwimlanesElementsPeer',PATH_HOME ."engine/classes/model/om/BaseSwimlanesElementsPeer.php");
Bootstrap::registerClass('SwimlanesElementsPeer',PATH_HOME . "engine/classes/model/SwimlanesElementsPeer.php");

Bootstrap::registerClass('BaseSubApplication',  PATH_HOME . "engine/classes/model/om/BaseSubApplication.php");
Bootstrap::registerClass('SubApplication',      PATH_HOME . "engine/classes/model/SubApplication.php");
Bootstrap::registerClass('SubApplicationPeer',  PATH_HOME . "engine/classes/model/SubApplicationPeer.php");

Bootstrap::registerClass('BaseSubProcess',      PATH_HOME . "engine/classes/model/om/BaseSubProcess.php");
Bootstrap::registerClass('SubProcess',          PATH_HOME . "engine/classes/model/SubProcess.php");

Bootstrap::registerClass('BaseTask',            PATH_HOME . "engine/classes/model/om/BaseTask.php");
Bootstrap::registerClass('Task',                PATH_HOME . "engine/classes/model/Task.php");

Bootstrap::registerClass('BaseTaskUser',        PATH_HOME . "engine/classes/model/om/BaseTaskUser.php");
Bootstrap::registerClass('TaskUserPeer',        PATH_HOME . "engine/classes/model/TaskUserPeer.php");
Bootstrap::registerClass('TaskUser',            PATH_HOME . "engine/classes/model/TaskUser.php");

Bootstrap::registerClass('BaseTriggers',        PATH_HOME . "engine/classes/model/om/BaseTriggers.php");
Bootstrap::registerClass('Triggers',            PATH_HOME . "engine/classes/model/Triggers.php");
Bootstrap::registerClass('BaseTriggersPeer',    PATH_HOME . "engine/classes/model/om/BaseTriggersPeer.php");
Bootstrap::registerClass('TriggersPeer',        PATH_HOME . "engine/classes/model/TriggersPeer.php");

Bootstrap::registerClass('BaseUsers',           PATH_HOME . "engine/classes/model/om/BaseUsers.php");
Bootstrap::registerClass('IsoCountry',          PATH_HOME . "engine/classes/model/IsoCountry.php");
Bootstrap::registerClass('BaseIsoSubdivision',  PATH_HOME . "engine/classes/model/om/BaseIsoSubdivision.php");
Bootstrap::registerClass('IsoSubdivision',      PATH_HOME . "engine/classes/model/IsoSubdivision.php");
Bootstrap::registerClass('BaseIsoLocation',     PATH_HOME . "engine/classes/model/om/BaseIsoLocation.php");
Bootstrap::registerClass('IsoLocation',         PATH_HOME . "engine/classes/model/IsoLocation.php");
Bootstrap::registerClass('Users',               PATH_HOME . "engine/classes/model/Users.php");
Bootstrap::registerClass('UsersPeer',           PATH_HOME . "engine/classes/model/UsersPeer.php");

Bootstrap::registerClass('Xml_Node',            PATH_GULLIVER . "class.xmlDocument.php");


require_once "pear/Net/JSON.php";
Propel::init( PATH_CORE . "config/databases.php" );

//read memcached configuration
$config = System::getSystemConfiguration ('', '', SYS_SYS);
define ('MEMCACHED_ENABLED', $config ['memcached']);
define ('MEMCACHED_SERVER', $config ['memcached_server']);
define ('TIME_ZONE', $config ['time_zone']);
