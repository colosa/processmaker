<?php

namespace ProcessMaker\Plugins;

use Archive_Tar;
use enterprisePlugin;
use Exception;
use G;
use Illuminate\Support\Facades\Cache;
use InputFilter;
use Language;
use PEAR;
use PluginsRegistry;
use ProcessMaker\Core\System;
use ProcessMaker\Plugins\Adapters\PluginAdapter;
use ProcessMaker\Plugins\Interfaces\CaseSchedulerPlugin;
use ProcessMaker\Plugins\Interfaces\CronFile;
use ProcessMaker\Plugins\Interfaces\CssFile;
use ProcessMaker\Plugins\Interfaces\DashboardPage;
use ProcessMaker\Plugins\Interfaces\FolderDetail;
use ProcessMaker\Plugins\Interfaces\ImportCallBack;
use ProcessMaker\Plugins\Interfaces\JsFile;
use ProcessMaker\Plugins\Interfaces\MenuDetail;
use ProcessMaker\Plugins\Interfaces\OpenReassignCallback;
use Maveriks\Util\ClassLoader;
use Maveriks\WebApplication;
use ProcessMaker\Plugins\Interfaces\PluginDetail;
use ProcessMaker\Plugins\Interfaces\RedirectDetail;
use ProcessMaker\Plugins\Interfaces\StepDetail;
use ProcessMaker\Plugins\Interfaces\TaskExtendedProperty;
use ProcessMaker\Plugins\Interfaces\ToolbarDetail;
use ProcessMaker\Plugins\Interfaces\TriggerDetail;
use ProcessMaker\Plugins\Traits\Attributes;
use ProcessMaker\Plugins\Traits\Init;
use ProcessMaker\Plugins\Traits\PluginStructure;
use Publisher;
use stdClass;

/**
 * Class PluginRegistry
 * @package ProcessMaker\Plugins
 */
class PluginRegistry
{
    use PluginStructure;
    use Attributes;
    use Init;

    const NAME_CACHE = SYS_SYS . __CLASS__;
    /**
     * Instance of de object PluginRegistry
     * @var PluginRegistry $instance
     */
    private static $instance = null;

    /**
     * Instance of the PluginAdapter class
     * @var PluginAdapter $adapter
     */
    private $adapter;

    /**
     * PluginRegistry constructor.
     */
    public function __construct()
    {
        $this->adapter = new PluginAdapter();
        $this->constructStructure();
    }

    /**
     * Load the singleton instance from stored
     * @return PluginRegistry
     */
    public static function loadSingleton()
    {
        if (self::$instance === null) {
            if (is_null($object = Cache::get(self::NAME_CACHE))) {
                $object = new PluginRegistry();
                Cache::put(self::NAME_CACHE, $object, config('app.cache_lifetime'));
            }
            self::$instance = $object;
        }
        return self::$instance;
    }

    /**
     * Load the singleton instance from a serialized stored file
     * @return PluginRegistry
     * @throws Exception
     */
    public static function newInstance()
    {
        self::$instance = new PluginRegistry();
        if (!is_object(self::$instance) || get_class(self::$instance) != "ProcessMaker\Plugins\PluginRegistry") {
            throw new Exception("Can't load main PluginRegistry object.");
        }
        return self::$instance;
    }

    /**
     * Register the plugin in the singleton
     * @param string $Namespace Name Plugin
     * @param string $Filename Path of the main plugin file
     */
    public function registerPlugin($Namespace, $Filename = null)
    {
        $ClassName = $Namespace . "Plugin";
        $plugin = new $ClassName($Namespace, $Filename);

        if (isset($this->_aPluginDetails[$Namespace])) {
            $this->_aPluginDetails[$Namespace]->setVersion($plugin->iVersion);
            return;
        }

        $detail = new PluginDetail(
            $Namespace,
            $ClassName,
            $Filename,
            $plugin->sFriendlyName,
            $plugin->sPluginFolder,
            $plugin->sDescription,
            $plugin->sSetupPage,
            $plugin->iVersion,
            isset($plugin->sCompanyLogo) ? $plugin->sCompanyLogo : '',
            isset($plugin->aWorkspaces) ? $plugin->aWorkspaces : [],
            isset($plugin->enable) ? $plugin->enable : false,
            isset($plugin->bPrivate) ? $plugin->bPrivate : false
        );
        $this->_aPluginDetails[$Namespace] = $detail;
    }

    /**
     * Unregister the plugin in the class
     * @param string $namespace Name Plugin
     * @return PluginDetail
     */
    public function unregisterPlugin($namespace)
    {
        $detail = null;
        if (isset($this->_aPluginDetails[$namespace])) {
            $detail = $this->_aPluginDetails[$namespace];
            unset($this->_aPluginDetails[$namespace]);
        }
        return $detail;
    }

    /**
     * Get setup Plugins
     * @return int
     */
    public function setupPlugins()
    {
        try {
            require_once(PATH_CORE . "methods" . PATH_SEP . "enterprise" . PATH_SEP . "enterprise.php");
            $iPlugins = 0;
            /** @var PluginDetail $pluginDetail */
            foreach ($this->_aPluginDetails as $pluginDetail) {
                if ($pluginDetail->isEnabled()) {
                    if (!empty($pluginDetail->getFile()) && file_exists($pluginDetail->getFile())) {
                        $arrayFileInfo = pathinfo($pluginDetail->getFile());
                        $Filename = (
                            ($pluginDetail->getNamespace() == "enterprise") ?
                                PATH_CORE . "methods" . PATH_SEP . "enterprise" . PATH_SEP :
                                PATH_PLUGINS
                            ) . $arrayFileInfo["basename"];
                        if (!file_exists($Filename)) {
                            continue;
                        }
                        require_once $Filename;
                        $className = $pluginDetail->getClassName();
                        if (class_exists($className)) {
                            /** @var enterprisePlugin|\PMPlugin $Plugin */
                            $Plugin = new $className($pluginDetail->getNamespace(), $pluginDetail->getFile());
                            $this->_aPlugins[$pluginDetail->getNamespace()] = $Plugin;
                            $iPlugins++;
                            $Plugin->registerPmFunction();
                            $this->init();
                            $Plugin->setup();
                        }
                    }
                }
            }
            return $iPlugins;
        } catch (Exception $e) {
            global $G_PUBLISH;
            $Message['MESSAGE'] = $e->getMessage();
            $G_PUBLISH = new Publisher();
            $G_PUBLISH->AddContent('xmlform', 'xmlform', 'login/showMessage', '', $Message);
            G::RenderPage('publish');
            die();
        }
    }

    /**
     * Save the current configuration of a plugin
     * @param string $Namespace Name Plugin
     */
    public function savePlugin($Namespace)
    {
        $newStructurePlugin = $this->getAllAttributes($Namespace);
        $plugin = $this->convertFieldTable($newStructurePlugin);
        if ($plugin['PLUGIN_NAMESPACE'] && $plugin['PLUGIN_CLASS_NAME'] && $plugin['PLUGIN_FILE']) {
            $fieldPlugin = PluginsRegistry::loadOrCreateIfNotExists(md5($plugin['PLUGIN_NAMESPACE']), $plugin);
            PluginsRegistry::update($fieldPlugin);
        }
        Cache::pull(self::NAME_CACHE);
    }

    /**
     * Get the plugin details, by filename
     * @param string $Filename
     * @return null|PluginDetail
     */
    public function getPluginDetails($Filename)
    {
        /** @var PluginDetail $detail */
        foreach ($this->_aPluginDetails as $detail) {
            if ($Filename == baseName($detail->getFile())) {
                return $detail;
            }
        }
        return null;
    }

    /**
     * Enable the plugin
     * @param string $Namespace Name of plugin
     * @return bool
     * @throws Exception
     */
    public function enablePlugin($Namespace)
    {
        /** @var PluginDetail $currentPlugin */
        if ($currentPlugin = $this->_aPluginDetails[$Namespace]) {
            $this->registerFolder($Namespace, $Namespace, $currentPlugin->getFolder());
            //register the default directory, later we can have more
            $currentPlugin->setEnabled(true);
            //PluginsRegistry::enable($Namespace);
            $className = $currentPlugin->getClassName();
            /** @var enterprisePlugin $Plugin */
            if (class_exists($className)) {
                $plugin = new $className(
                    $currentPlugin->getNamespace(),
                    $currentPlugin->getFile()
                );
                $this->_aPluginDetails[$Namespace]->sFriendlyName = $plugin->sFriendlyName;
                $this->_aPluginDetails[$Namespace]->sDescription = $plugin->sDescription;
            } else {
                $plugin = $currentPlugin;
            }
            $this->_aPlugins[$Namespace] = $plugin;
            if (method_exists($plugin, 'enable')) {
                $plugin->enable();
            }
            /*
             * 1. register <plugin-dir>/src directory for autoloading
             * 2. verify if rest service is enabled
             * 3. register rest service directory
             */
            $pluginSrcDir = PATH_PLUGINS . $currentPlugin->getNamespace() . PATH_SEP . 'src';

            if (is_dir($pluginSrcDir)) {
                $loader = ClassLoader::getInstance();
                $loader->add($pluginSrcDir);
            }

            if (array_key_exists($currentPlugin->getNamespace(), $this->_restServiceEnabled)
                && $this->_restServiceEnabled[$currentPlugin->getNamespace()] == true
            ) {
                $plugin->registerRestService();
            }
            return true;
        } else {
            throw new Exception("Unable to enable plugin '$Namespace' (plugin not found)");
        }
    }

    /**
     * Disable the plugin
     * @param string $Namespace Name of plugin
     * @param bool $eventPlugin Propagate disable event of plugin
     * @throws Exception
     */
    public function disablePlugin($Namespace, $eventPlugin = true)
    {
        /** @var PluginDetail $currentPlugin */
        $currentPlugin = $this->_aPluginDetails[$Namespace];
        // If plugin class exists check if disable method exist,
        // otherwise use default plugin details
        $className = $currentPlugin->getClassName();
        if ($currentPlugin && class_exists($className)) {
            $currentPlugin->setEnabled(false);
            //PluginsRegistry::disable($Namespace);
            if ($eventPlugin) {
                $plugin = new $className(
                    $currentPlugin->getNamespace(),
                    $currentPlugin->getFile()
                );
                if (method_exists($plugin, "disable")) {
                    $plugin->disable();
                }
            }
        } else {
            throw new Exception("Unable to disable plugin '$Namespace' (plugin not found)");
        }
    }

    /**
     * @param $Namespace
     * @return bool
     */
    public function checkFilePlugin($Namespace)
    {
        if (is_file(PATH_PLUGINS . $Namespace . ".php") && is_dir(PATH_PLUGINS . $Namespace)) {
            require_once(PATH_PLUGINS . $Namespace . ".php");
            return true;
        }
        return false;
    }

    /**
     * Get status plugin
     * @param string $name Name of Plugin
     * @return string Return a string with status plugin
     * @throws Exception
     */
    public function getStatusPlugin($name)
    {
        /** @var PluginDetail $currentPlugin */
        if (!empty($this->_aPluginDetails[$name]) && $currentPlugin = $this->_aPluginDetails[$name]) {
            return $currentPlugin->isEnabled() ? "enabled" : "disabled";
        }
        return false;
    }

    /**
     * Get status plugin
     * @param string $Namespace Name of Plugin
     * @return bool Return a boolean It's true if the plugin is active
     * It is true if the plugin is active and false if it is disabled
     * @throws Exception
     */
    public function isEnable($Namespace)
    {
        /** @var PluginDetail $currentPlugin */
        if (!empty($this->_aPluginDetails[$Namespace]) && $currentPlugin = $this->_aPluginDetails[$Namespace]) {
            return $currentPlugin->isEnabled();
        }
        return false;
    }

    /**
     * Install a plugin archive.
     * If pluginName is specified, the archive will
     * only be installed if it contains this plugin.
     * @param string $Filename
     * @param string $Namespace Name of plugin
     * @return bool true if enabled, false otherwise
     * @throws Exception
     */
    public function installPluginArchive($Filename, $Namespace)
    {
        $tar = new Archive_Tar($Filename);
        $files = $tar->listContent();
        $plugins = array();
        $namePlugin = array();
        foreach ($files as $f) {
            if (preg_match("/^([\w\.]*).ini$/", $f["filename"], $matches)) {
                $plugins[] = $matches[1];
            }
            if (preg_match("/^.*($Namespace)\.php$/", $f["filename"], $matches)) {
                $namePlugin[] = $matches[1];
            }
        }

        if (count($plugins) > 1) {
            throw new Exception("Multiple plugins in one archive are not supported currently");
        }

        if (isset($Namespace) && !in_array($Namespace, $namePlugin)) {
            throw new Exception("Plugin '$Namespace' not found in archive");
        }

        $pluginFile = "$Namespace.php";

        $res = $tar->extract(PATH_PLUGINS);
        if (!file_exists(PATH_PLUGINS . $pluginFile)) {
            throw (new Exception("File \"$pluginFile\" doesn't exist"));
        }
        $path = PATH_PLUGINS . $pluginFile;
        require_once($path);
        /** @var PluginDetail $details */
        $details = $this->getPluginDetails($pluginFile);

        $this->installPlugin($details->getNamespace());
        $this->enablePlugin($details->getNamespace());
        $this->savePlugin($details->getNamespace());
    }

    /**
     * Uninstall plugin
     * @param string $Namespace Name of plugin
     * @throws Exception
     */
    public function uninstallPlugin($Namespace)
    {
        $pluginFile = $Namespace . ".php";

        if (!file_exists(PATH_PLUGINS . $pluginFile)) {
            throw (new Exception("File \"$pluginFile\" doesn't exist"));
        }

        $path = PATH_PLUGINS . $pluginFile;
        $filter = new InputFilter();
        $path = $filter->validateInput($path, 'path');
        require_once($path);

        foreach ($this->_aPluginDetails as $namespace => $detail) {
            if ($namespace == $Namespace) {
                $this->enablePlugin($detail->sNamespace);
                $this->disablePlugin($detail->sNamespace);

                ///////
                $plugin = new $detail->sClassName($detail->sNamespace, $detail->sFilename);
                $this->_aPlugins[$detail->sNamespace] = $plugin;

                if (method_exists($plugin, "uninstall")) {
                    $plugin->uninstall();
                }

                ///////
                $this->savePlugin($detail->sNamespace);
                ///////
                $pluginDir = PATH_PLUGINS . $detail->sPluginFolder;

                if (isset($detail->sFilename) && !empty($detail->sFilename) && file_exists($detail->sFilename)) {
                    unlink($detail->sFilename);
                }

                if (isset($detail->sPluginFolder) && !empty($detail->sPluginFolder) && file_exists($pluginDir)) {
                    G::rm_dir($pluginDir);
                }

                ///////
                $this->uninstallPluginWorkspaces(array($Namespace));
                ///////
                break;
            }
        }
    }

    /**
     * Uninstall Plugin of Workspaces
     * @param array $arrayPlugin
     */
    public function uninstallPluginWorkspaces($arrayPlugin)
    {
        $workspace = System::listWorkspaces();

        foreach ($workspace as $indexWS => $ws) {
            $pluginRegistry = PluginRegistry::loadSingleton();
            $attributes = $pluginRegistry->getAttributes();

            foreach ($arrayPlugin as $index => $value) {
                if (isset($attributes["_aPluginDetails"][$value])) {
                    $pluginRegistry->disablePlugin($value, 0);
                    $pluginRegistry->savePlugin($value);
                }
            }
        }
    }

    /**
     * Install and setup the plugin
     * @param string $Namespace Name of plugin
     */
    public function installPlugin($Namespace)
    {
        try {
            if (isset($this->_aPluginDetails[$Namespace])) {
                /** @var PluginDetail $detail */
                $detail = $this->_aPluginDetails[$Namespace];
                $className = $detail->getClassName();
                /** @var enterprisePlugin $oPlugin */
                $oPlugin = new $className($detail->getNamespace(), $detail->getFile());
                $oPlugin->registerPmFunction();
                $detail->setEnabled(false);
                $this->init();
                $oPlugin->setup();
                $this->_aPlugins[$detail->getNamespace()] = $oPlugin;
                $oPlugin->install();
                //save in Table
                $this->savePlugin($detail->getNamespace());
            }
        } catch (Exception $e) {
            global $G_PUBLISH;
            $Message['MESSAGE'] = $e->getMessage();
            $G_PUBLISH = new Publisher();
            $G_PUBLISH->AddContent('xmlform', 'xmlform', 'login/showMessage', '', $Message);
            G::RenderPage('publish');
            die();
        }
    }

    /**
     * Register a menu
     * @param string $Namespace Name of plugin
     * @param string $MenuId Id of menu
     * @param string $Filename File of menu
     */
    public function registerMenu($Namespace, $MenuId, $Filename)
    {
        $found = false;
        /** @var MenuDetail $menu */
        foreach ($this->_aMenus as $menu) {
            if ($menu->equalMenuIdTo($MenuId) && $menu->equalNamespaceTo($Namespace)) {
                $found = true;
            }
        }
        if (!$found) {
            $menuDetail = new MenuDetail($Namespace, $MenuId, $Filename);
            $this->_aMenus[] = $menuDetail;
        }
    }

    /**
     * Register a dashlet class
     * @param string $Namespace
     */
    public function registerDashlets($Namespace)
    {
        $found = false;
        foreach ($this->_aDashlets as $row => $detail) {
            if ($Namespace == $detail) {
                $found = true;
            }
        }
        if (!$found) {
            $this->_aDashlets[] = $Namespace;
        }
    }

    /**
     * Register a stylesheet
     * @param string $Namespace Name of plugin
     * @param string $CssFile File css
     */
    public function registerCss($Namespace, $CssFile)
    {
        $found = false;
        /** @var CssFile $css */
        foreach ($this->_aCss as $css) {
            if ($css->equalCssFileTo($CssFile) && $css->equalNamespaceTo($Namespace)) {
                $css->setCssFile($CssFile);
                $found = true;
            }
        }
        if (!$found) {
            $cssFile = new CssFile($Namespace, $CssFile);
            $this->_aCss[] = $cssFile;
        }
    }

    /**
     * Return all css
     * @return array
     */
    public function getRegisteredCss()
    {
        return $this->_aCss;
    }

    /**
     * Register a plugin javascript to run with core js script at same runtime
     * @param string $Namespace Name of plugin
     * @param string $CoreJsFile Core js file
     * @param array|string $PluginJsFile File js of plugin
     * @throws Exception
     */
    public function registerJavascript($Namespace, $CoreJsFile, $PluginJsFile)
    {
        /** @var JsFile $js */
        foreach ($this->_aJs as $js) {
            if ($js->equalCoreJsFile($CoreJsFile) && $js->equalNamespaceTo($Namespace)) {
                $js->pushPluginJsFile($PluginJsFile);
                return;
            }
        }
        $js = new JsFile($Namespace, $CoreJsFile, []);
        $js->pushPluginJsFile($PluginJsFile);
        $this->_aJs[] = $js;
    }

    /**
     * Return all plugin javascripts
     * @return array
     */
    public function getRegisteredJavascript()
    {
        return $this->_aJs;
    }

    /**
     * Return all plugin javascripts given a core js file, from all namespaces or a single namespace
     * @param string $CoreJsFile Core js file
     * @param string $Namespace Name of plugin
     * @return array
     */
    public function getRegisteredJavascriptBy($CoreJsFile, $Namespace = '')
    {
        $scripts = array();
        if ($Namespace == '') {
            /** @var JsFile $js */
            foreach ($this->_aJs as $i => $js) {
                if ($js->equalCoreJsFile($CoreJsFile)) {
                    $scripts = array_merge($scripts, $js->getPluginJsFile());
                }
            }
        } else {
            /** @var JsFile $js */
            foreach ($this->_aJs as $i => $js) {
                if ($js->equalCoreJsFile($CoreJsFile) && $js->equalNamespaceTo($Namespace)) {
                    $scripts = array_merge($scripts, $js->getPluginJsFile());
                }
            }
        }
        return $scripts;
    }

    /**
     * Unregister all javascripts from a namespace or a js core file given
     * @param string $Namespace Name of plugin
     * @param string $CoreJsFile Core js file
     * @return array
     */
    public function unregisterJavascripts($Namespace, $CoreJsFile = '')
    {
        if ($CoreJsFile == '') {
            /** @var JsFile $js */
            foreach ($this->_aJs as $i => $js) {
                if ($js->equalNamespaceTo($Namespace)) {
                    unset($this->_aJs[$i]);
                }
            }
            // Re-index when all js were unregistered
            $this->_aJs = array_values($this->_aJs);
        } else {
            /** @var JsFile $js */
            foreach ($this->_aJs as $i => $js) {
                if ($js->equalCoreJsFile($CoreJsFile) && $js->equalNamespaceTo($Namespace)) {
                    unset($this->_aJs[$i]);
                    // Re-index for each js that was unregistered
                    $this->_aJs = array_values($this->_aJs);
                }
            }
        }
    }

    /**
     * Register a reports class
     * @param string $Namespace Name of plugin
     */
    public function registerReport($Namespace)
    {
        $found = false;
        foreach ($this->_aReports as $detail) {
            if ($Namespace == $detail) {
                $found = true;
            }
        }
        if (!$found) {
            $this->_aReports[] = $Namespace;
        }
    }

    /**
     * Register a PmFunction class in the singleton
     * @param string $Namespace Name of plugin
     */
    public function registerPmFunction($Namespace)
    {
        $found = false;
        foreach ($this->_aPmFunctions as $row => $detail) {
            if ($Namespace == $detail) {
                $found = true;
            }
        }
        if (!$found) {
            $this->_aPmFunctions[] = $Namespace;
        }
    }

    /**
     * Register a redirectLogin class in the singleton
     * @param string $Namespace Name of plugin
     * @param string $Role
     * @param string $PathMethod
     */
    public function registerRedirectLogin($Namespace, $Role, $PathMethod)
    {
        $found = false;
        /** @var RedirectDetail $redirectDetail */
        foreach ($this->_aRedirectLogin as $redirectDetail) {
            if (($redirectDetail->equalNamespaceTo($Namespace)) && ($redirectDetail->equalRoleCodeTo($Role))) {
                //Filters based on Workspace and Role Code
                $found = true;
            }
        }
        if (!$found) {
            $this->_aRedirectLogin[] = new RedirectDetail($Namespace, $Role, $PathMethod);
        }
    }

    /**
     * Register a folder for methods
     * @param string $Namespace Name of plugin
     * @param string $FolderId Id of folder
     * @param string $FolderName Name of folder
     */
    public function registerFolder($Namespace, $FolderId, $FolderName)
    {
        $found = false;
        /** @var FolderDetail $folder */
        foreach ($this->_aFolders as $folder) {
            if ($folder->equalFolderIdTo($FolderId) && $folder->equalNamespaceTo($Namespace)) {
                $found = true;
            }
        }

        if (!$found) {
            $this->_aFolders[] = new FolderDetail($Namespace, $FolderId, $FolderName);
        }
    }

    /**
     * Register a step for process
     * @param string $Namespace Name of plugin
     * @param string $StepId Id of step
     * @param string $StepName Name of step
     * @param string $StepTitle Title of step
     * @param string $SetupStepPage
     */
    public function registerStep($Namespace, $StepId, $StepName, $StepTitle, $SetupStepPage = '')
    {
        $found = false;
        /** @var StepDetail $detail */
        foreach ($this->_aSteps as $detail) {
            if ($detail->equalStepIdTo($StepId) && $detail->equalNamespaceTo($Namespace)) {
                $found = true;
            }
        }

        if (!$found) {
            $this->_aSteps[] = new StepDetail($Namespace, $StepId, $StepName, $StepTitle, $SetupStepPage);
        }
    }

    /**
     * Return true if the FolderName is registered
     * @param string $FolderName
     * @return bool
     */
    public function isRegisteredFolder($FolderName)
    {
        /** @var FolderDetail $folder */
        foreach ($this->_aFolders as $folder) {
            $folderName = $folder->getFolderName();
            if ($FolderName == $folderName && is_dir(PATH_PLUGINS . $folderName)) {
                return true;
            } elseif ($FolderName == $folderName &&
                is_dir(PATH_PLUGINS . $folder->getNamespace() . PATH_SEP . $folderName)) {
                return $folder->getNamespace();
            }
        }
        return false;
    }

    /**
     * Return all menus related to a menuId
     * @param string $MenuId
     */
    public function getMenus($MenuId)
    {
        /** @var MenuDetail $menu */
        foreach ($this->_aMenus as $menu) {
            if ($menu->equalMenuIdTo($MenuId) && $menu->exitsFile()) {
                $menu->includeFileMenu();
            }
        }
    }

    /**
     * Return all dashlets classes registered
     * @return array
     */
    public function getDashlets()
    {
        return $this->_aDashlets;
    }

    /**
     * This function returns all reports registered
     * @return array
     */
    public function getReports()
    {
        return $this->_aReports;
    }

    /**
     * This function returns all pmFunctions registered
     * @return array
     */
    public function getPmFunctions()
    {
        return $this->_aPmFunctions;
    }

    /**
     * This function returns all steps registered
     * @return array
     */
    public function getSteps()
    {
        return $this->_aSteps;
    }

    /**
     * This function returns all redirect registered
     * @return array
     */
    public function getRedirectLogins()
    {
        return $this->_aRedirectLogin;
    }

    /**
     * execute all triggers related to a triggerId
     * TODO
     *
     * @param string $TriggerId
     * @param $oData
     * @return mixed
     */
    public function executeTriggers($TriggerId, $oData)
    {
        /** @var TriggerDetail $trigger */
        foreach ($this->_aTriggers as $trigger) {
            if ($trigger->equalTriggerId($TriggerId)) {
                //review all folders registered for this namespace
                $found = false;
                $classFile = '';
                /** @var FolderDetail $folder */
                foreach ($this->_aFolders as $folder) {
                    $fname = $folder->equalNamespaceTo('enterprise') ?
                        PATH_CORE . 'classes' . PATH_SEP . 'class.' . $folder->getFolderName() . '.php' :
                        PATH_PLUGINS . $folder->getFolderName() . PATH_SEP . 'class.' . $folder->getFolderName() . '.php';
                    if ($trigger->equalNamespaceTo($folder->getNamespace()) && file_exists($fname)) {
                        $found = true;
                        $classFile = $fname;
                    }
                }
                if ($found) {
                    require_once($classFile);
                    $sClassNameA = preg_replace("/plugin$/i", 'Class', $this->_aPluginDetails[$trigger->getNamespace()]->getClassName());
                    $sClassNameB = preg_replace("/plugin$/i", 'class', $this->_aPluginDetails[$trigger->getNamespace()]->getClassName());
                    $sClassName = class_exists($sClassNameA) ? $sClassNameA : $sClassNameB;
                    $obj = new $sClassName();
                    $methodName = $trigger->getTriggerName();
                    $response = $obj->{$methodName}($oData);
                    if (PEAR::isError($response)) {
                        print $response->getMessage();
                        return;
                    }
                    return $response;
                } else {
                    print "error in call method " . $trigger->getTriggerName();
                }
            }
        }
    }

    /**
     * verify if exists triggers related to a triggerId
     * @param string $TriggerId
     * @return bool
     */
    public function existsTrigger($TriggerId)
    {
        $found = false;
        /** @var TriggerDetail $trigger */
        foreach ($this->_aTriggers as $trigger) {
            if ($trigger->equalTriggerId($TriggerId)) {
                //review all folders registered for this namespace
                /** @var FolderDetail $folder */
                foreach ($this->_aFolders as $folder) {
                    $folderName = $folder->getFolderName();
                    $fileName = $folder->getNamespace() == 'enterprise' ?
                        PATH_CORE . 'classes' . PATH_SEP . 'class.' . $folderName . '.php' :
                        PATH_PLUGINS . $folderName . PATH_SEP . 'class.' . $folderName . '.php';
                    if ($trigger->getNamespace() == $folder->getNamespace() && file_exists($fileName)) {
                        $found = true;
                    }
                }
            }
        }
        return $found;
    }

    /**
     * Return info related to a triggerId
     * @param int $triggerId
     * @return TriggerDetail
     */
    public function getTriggerInfo($triggerId)
    {
        $found = null;
        /** @var TriggerDetail $trigger */
        foreach ($this->_aTriggers as $trigger) {
            if ($trigger->equalTriggerId($triggerId)) {
                //review all folders registered for this namespace
                /** @var FolderDetail $folder */
                foreach ($this->_aFolders as $folder) {
                    $filename = PATH_PLUGINS . $folder->getFolderName() . PATH_SEP .
                        'class.' . $folder->getFolderName() . '.php';
                    if ($trigger->equalNamespaceTo($folder->getNamespace()) && file_exists($filename)) {
                        $found = $trigger;
                    }
                }
            }
        }
        return $found;
    }

    /**
     * Register a trigger
     * @param string $Namespace
     * @param string $TriggerId
     * @param string $TriggerName
     */
    public function registerTrigger($Namespace, $TriggerId, $TriggerName)
    {
        $found = false;
        /** @var TriggerDetail $trigger */
        foreach ($this->_aTriggers as $trigger) {
            if ($trigger->equalTriggerId($TriggerId) && $trigger->equalNamespaceTo($Namespace)) {
                $found = true;
            }
        }
        if (!$found) {
            $triggerDetail = new TriggerDetail($Namespace, $TriggerId, $TriggerName);
            $this->_aTriggers[] = $triggerDetail;
        }
    }

    /**
     * Get plugin
     * @param string $Namespace Name of plugin
     * @return mixed
     */
    public function &getPlugin($Namespace)
    {
        $oPlugin = null;
        if (array_key_exists($Namespace, $this->_aPlugins)) {
            $oPlugin = $this->_aPlugins[$Namespace];
        }
        return $oPlugin;
    }

    /**
     * Set company logo
     * @param string $Namespace Name of plugin
     * @param string $Filename File name logo
     * @return void
     */
    public function setCompanyLogo($Namespace, $Filename)
    {
        foreach ($this->_aPluginDetails as $detail) {
            if ($Namespace == $detail->sNamespace) {
                $this->_aPluginDetails[$Namespace]->sCompanyLogo = $Filename;
            }
        }
    }

    /**
     * Get company logo
     * @param string $default
     * @return mixed
     */
    public function getCompanyLogo($default)
    {
        $CompanyLogo = $default;
        /** @var PluginDetail $detail */
        foreach ($this->_aPluginDetails as $detail) {
            if (trim($detail->getCompanyLogo()) != '' && $detail->isEnabled()) {
                $CompanyLogo = $detail->getCompanyLogo();
            }
        }
        return $CompanyLogo;
    }

    /**
     * This function execute a Method
     * @param string $Namespace Name of plugin
     * @param string $MethodName
     * @param object $oData
     * @return mixed
     * @throws Exception
     */
    public function executeMethod($Namespace, $MethodName, $oData)
    {
        $response = null;
        try {
            $details = $this->_aPluginDetails[$Namespace];
            $pluginFolder = $details->sPluginFolder;
            $className = $details->sClassName;
            $classFile = PATH_PLUGINS . $pluginFolder . PATH_SEP . 'class.' . $pluginFolder . '.php';
            if (file_exists($classFile)) {
                $sClassName = substr_replace($className, "class", -6, 6);
                if (!class_exists($sClassName)) {
                    require_once $classFile;
                }
                $obj = new $sClassName();
                if (!in_array($MethodName, get_class_methods($obj))) {
                    throw (new Exception("The method '$MethodName' doesn't exist in class '$sClassName' "));
                }
                $obj->sNamespace = $details->sNamespace;
                $obj->sClassName = $details->sClassName;
                $obj->sFilename = $details->sFilename;
                $obj->iVersion = $details->iVersion;
                $obj->sFriendlyName = $details->sFriendlyName;
                $obj->sPluginFolder = $details->sPluginFolder;
                $response = $obj->{$MethodName}($oData);
            }
            return $response;
        } catch (Exception $e) {
            throw ($e);
        }
    }

    /**
     * this function gets Fields For Page on Setup
     * @param string $Namespace Name of plugin
     * @return object
     */
    public function getFieldsForPageSetup($Namespace)
    {
        $oData = null;
        return $this->executeMethod($Namespace, 'getFieldsForPageSetup', $oData);
    }

    /**
     * This function updates Fields For Page on Setup
     * @param string $Namespace
     * @param object $oData
     * @return mixed
     * @throws Exception
     */
    public function updateFieldsForPageSetup($Namespace, $oData)
    {
        if (!isset($this->_aPluginDetails[$Namespace])) {
            throw (new Exception("The namespace '$Namespace' doesn't exist in plugins folder."));
        }
        return $this->executeMethod($Namespace, 'updateFieldsForPageSetup', $oData);
    }

    /**
     * @deprecated
     */
    public function eevalidate()
    {
    }

    /**
     * Register a toolbar for dynaform editor
     * @param string $Namespace Name of plugin
     * @param string $ToolbarId (NORMAL, GRID)
     * @param string $Filename
     */
    public function registerToolbarFile($Namespace, $ToolbarId, $Filename)
    {
        $found = false;
        /** @var ToolbarDetail $toolbarFile */
        foreach ($this->_aToolbarFiles as $toolbarFile) {
            if ($toolbarFile->equalToolbarIdTo($ToolbarId) && $toolbarFile->equalNamespaceTo($Namespace)) {
                $found = true;
            }
        }
        if (!$found) {
            $toolbarDetail = new ToolbarDetail($Namespace, $ToolbarId, $Filename);
            $this->_aToolbarFiles[] = $toolbarDetail;
        }
    }

    /**
     * Return all toolbar files related to a ToolbarId
     * @param string $ToolbarId (NORMAL, GRID)
     */
    public function getToolbarOptions($ToolbarId)
    {
        /** @var ToolbarDetail $toolbarFile */
        foreach ($this->_aToolbarFiles as $toolbarFile) {
            if ($toolbarFile->equalToolbarIdTo($ToolbarId) && $toolbarFile->exitsFile()) {
                $toolbarFile->includeFile();
            }
        }
    }

    /**
     * Register a Case Scheduler Plugin
     * @param string $Namespace Name of plugin
     * @param string $ActionId
     * @param string $ActionForm
     * @param string $ActionSave
     * @param string $ActionExecute
     * @param string $ActionGetFields
     */
    public function registerCaseSchedulerPlugin(
        $Namespace,
        $ActionId,
        $ActionForm,
        $ActionSave,
        $ActionExecute,
        $ActionGetFields
    )
    {
        $found = false;
        /** @var CaseSchedulerPlugin $caseScheduler */
        foreach ($this->_aCaseSchedulerPlugin as $caseScheduler) {
            if ($caseScheduler->equalActionIdTo($ActionId) && $caseScheduler->equalNamespaceTo($Namespace)) {
                $found = true;
            }
        }

        if (!$found) {
            $this->_aCaseSchedulerPlugin[] = new CaseSchedulerPlugin(
                $Namespace,
                $ActionId,
                $ActionForm,
                $ActionSave,
                $ActionExecute,
                $ActionGetFields
            );
        }
    }

    /**
     * This function returns all Case Scheduler Plugins registered
     * @return array
     */
    public function getCaseSchedulerPlugins()
    {
        return $this->_aCaseSchedulerPlugin;
    }

    /**
     * Register a Task Extended property page
     * @param string $Namespace Name of plugin
     * @param string $Page
     * @param string $Name
     * @param string $Icon
     */
    public function registerTaskExtendedProperty($Namespace, $Page, $Name, $Icon)
    {
        $found = false;
        /** @var TaskExtendedProperty $task */
        foreach ($this->_aTaskExtendedProperties as $task) {
            if ($task && $task->equalPageTo($Page) && $task->equalNamespaceTo($Namespace)) {
                $task->setName($Name);
                $task->setIcon($Icon);
                $found = true;
            }
        }
        if (!$found) {
            $taskExtendedProperty = new TaskExtendedProperty($Namespace, $Page, $Name, $Icon);
            $this->_aTaskExtendedProperties[] = $taskExtendedProperty;
        }
    }

    /**
     * Register a dashboard page for cases
     * @param string $Namespace
     * @param string $Page
     * @param string $Name
     * @param string $Icon
     */
    public function registerDashboardPage($Namespace, $Page, $Name, $Icon)
    {
        $found = false;
        /** @var DashboardPage $dashboardPage */
        foreach ($this->_aDashboardPages as $dashboardPage) {
            if ($dashboardPage->equalPageTo($Page) && $dashboardPage->equalNamespaceTo($Namespace)) {
                $dashboardPage->setName($Name);
                $dashboardPage->setIcon($Icon);
                $found = true;
            }
        }
        if (!$found) {
            $dashboardPage = new DashboardPage($Namespace, $Page, $Name, $Icon);
            $this->_aDashboardPages[] = $dashboardPage;
        }
    }

    /**
     * Register a rest service class from a plugin to be served by processmaker
     * @param string $Namespace The namespace for the plugin
     * @return bool
     */
    public function registerRestService($Namespace)
    {
        $baseSrcPluginPath = PATH_PLUGINS . $Namespace . PATH_SEP . "src";
        $apiPath = PATH_SEP . "Services" . PATH_SEP . "Api" . PATH_SEP . ucfirst($Namespace);
        $classesList = (new \Bootstrap())->rglob('*', 0, $baseSrcPluginPath . $apiPath);

        foreach ($classesList as $classFile) {
            if (pathinfo($classFile, PATHINFO_EXTENSION) === 'php') {
                $ns = str_replace(
                    '/',
                    '\\',
                    str_replace('.php', '', str_replace($baseSrcPluginPath, '', $classFile))
                );

                // Ensure that is registering only existent classes.
                if (class_exists($ns)) {
                    $this->_restServices[$Namespace][] = array(
                        "filepath" => $classFile,
                        "namespace" => $ns
                    );
                }
            }
        }

        WebApplication::purgeRestApiCache(basename(PATH_DATA_SITE));

        return true;
    }

    /**
     * Register a extend rest service class from a plugin to be served by processmaker
     *
     * @param string $Namespace The namespace for the plugin
     * @param string $ClassName The service (api) class name
     */
    public function registerExtendsRestService($Namespace, $ClassName)
    {
        $baseSrcPluginPath = PATH_PLUGINS . $Namespace . PATH_SEP . 'src';
        $apiPath = PATH_SEP . 'Services' . PATH_SEP . 'Ext' . PATH_SEP;
        $classFile = $baseSrcPluginPath . $apiPath . 'Ext' . $ClassName . '.php';
        if (file_exists($classFile)) {
            if (empty($this->_restExtendServices[$Namespace])) {
                $this->_restExtendServices[$Namespace] = new stdClass();
            }
            $this->_restExtendServices[$Namespace]->{$ClassName} = new stdClass();
            $this->_restExtendServices[$Namespace]->{$ClassName}->filePath = $classFile;
            $this->_restExtendServices[$Namespace]->{$ClassName}->classParent = $ClassName;
            $this->_restExtendServices[$Namespace]->{$ClassName}->classExtend = 'Ext' . $ClassName;
        }
    }

    /**
     * Get a extend rest service class from a plugin to be served by processmaker
     * @param string $ClassName The service (api) class name
     * @return array
     */
    public function getExtendsRestService($ClassName)
    {
        $responseRestExtendService = [];
        foreach ($this->_restExtendServices as $Namespace => $restExtendService) {
            if (isset($restExtendService->{$ClassName})) {
                $responseRestExtendService = $restExtendService->{$ClassName};
                break;
            }
        }
        return $responseRestExtendService;
    }

    /**
     * Remove a extend rest service class from a plugin to be served by processmaker
     * @param string $Namespace
     * @param string $ClassName The service (api) class name
     * @return bool
     */
    public function disableExtendsRestService($Namespace, $ClassName = '')
    {
        if (empty($ClassName)) {
            unset($this->_restExtendServices[$Namespace]);
        } elseif (isset($this->_restExtendServices[$Namespace]->{$ClassName})) {
            unset($this->_restExtendServices[$Namespace]->{$ClassName});
        }
    }

    /**
     * Unregister a rest service class of a plugin
     * @param string $Namespace The namespace for the plugin
     */
    public function unregisterRestService($Namespace)
    {
        if ($this->_restServices) {
            unset($this->_restServices[$Namespace]);
            WebApplication::purgeRestApiCache(basename(PATH_DATA_SITE));
        }
    }

    /**
     * Return all rest services registered
     * @return array
     */
    public function getRegisteredRestServices()
    {
        return $this->_restServices;
    }

    /**
     * Return all dashboard pages
     * @return array
     */
    public function getDashboardPages()
    {
        return $this->_aDashboardPages;
    }

    /**
     * Return all task extended properties
     * @return array
     */
    public function getTaskExtendedProperties()
    {
        return $this->_aTaskExtendedProperties;
    }

    /**
     * Register a dashboard plugin to be served by processmaker
     */
    public function registerDashboard()
    {
        // Dummy function for backwards compatibility
    }

    /**
     * Verify Translation of plugin
     * @param string $Namespace Name of plugin
     */
    public function verifyTranslation($Namespace)
    {
        $language = new Language();
        $pathPluginTranslations = PATH_PLUGINS . $Namespace . PATH_SEP . 'translations' . PATH_SEP;
        if (file_exists($pathPluginTranslations . 'translations.php')) {
            if (!file_exists($pathPluginTranslations . $Namespace . '.' . SYS_LANG . '.po')) {
                $language->createLanguagePlugin($Namespace, SYS_LANG);
            }
            $language->updateLanguagePlugin($Namespace, SYS_LANG);
        }
    }

    /**
     * Register a cron file
     * @param string $Namespace Name of Plugin
     * @param string $CronFile
     */
    public function registerCronFile($Namespace, $CronFile)
    {
        $found = false;
        /** @var CronFile $cronFile */
        foreach ($this->_aCronFiles as $cronFile) {
            if ($cronFile->equalCronFileTo($CronFile) && $cronFile->equalNamespaceTo($Namespace)) {
                $cronFile->setCronFile($CronFile);
                $found = true;
            }
        }
        if (!$found) {
            $CronFile = new CronFile($Namespace, $CronFile);
            $this->_aCronFiles[] = $CronFile;
        }
    }

    /**
     * Function to enable rest service for plugins
     * @param string $Namespace Name of plugin
     * @param bool $enable
     */
    public function enableRestService($Namespace, $enable)
    {
        $this->_restServiceEnabled[$Namespace] = $enable;
    }

    /**
     * Return all cron files registered
     * @return array
     */
    public function getCronFiles()
    {
        return $this->_aCronFiles;
    }

    /**
     * Update the plugin attributes in all workspaces
     * @param string $workspace Name workspace
     * @param string $namespace Name of Plugin
     * @throws Exception
     */
    public function updatePluginAttributesInAllWorkspaces($workspace, $namespace)
    {
        try {
            //Set variables
            $pluginFileName = $namespace . ".php";

            //Verify data
            if (!file_exists(PATH_PLUGINS . $pluginFileName)) {
                throw new Exception("Error: The plugin not exists");
            }

            //remove old data plugin
            $pmPluginRegistry = PluginRegistry::loadSingleton();
            $pluginDetails = $pmPluginRegistry->unregisterPlugin($namespace);

            //Load plugin attributes
            require_once(PATH_PLUGINS . $pluginFileName);

            if (is_array($pluginDetails->getWorkspaces()) && !in_array($workspace, $pluginDetails->getWorkspaces())) {
                $pmPluginRegistry->disablePlugin($namespace);
            }
            $pmPluginRegistry->savePlugin($namespace);
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Register designer menu file
     * @param string $pluginName Plugin name
     * @param string $file Designer menu file
     * @throws Exception
     */
    public function registerDesignerMenu($pluginName, $file)
    {
        try {
            $flagFound = false;

            foreach ($this->_arrayDesignerMenu as $value) {
                if ($value->pluginName == $pluginName && $value->file == $file) {
                    $flagFound = true;
                    break;
                }
            }

            if (!$flagFound) {
                $obj = new stdClass();
                $obj->pluginName = $pluginName;
                $obj->file = $file;

                $this->_arrayDesignerMenu[] = $obj;
            }
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Return all designer menu files registered
     * @return array
     * @throws Exception
     */
    public function getDesignerMenu()
    {
        try {
            return $this->_arrayDesignerMenu;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Replace new options to menu
     * @param string $namespace
     * @param array $from
     * @param array $options
     * @return void
     */
    public function registerMenuOptionsToReplace($namespace, $from, $options)
    {
        if (isset($from["section"]) && isset($from["menuId"])) {
            $section = $from["section"];
            $oMenuFromPlugin = $this->_aMenuOptionsToReplace;
            if (array_key_exists($section, $oMenuFromPlugin)) {
                unset($this->_aMenuOptionsToReplace[$from["section"]]);
            }
            $this->_aMenuOptionsToReplace[$from["section"]][$from["menuId"]][] = $options;
        }
    }

    /**
     * Return all menu Options from a specific section
     * @param string $strMenuName
     * @return array
     */
    public function getMenuOptionsToReplace($strMenuName)
    {
        $oMenuFromPlugin = $this->_aMenuOptionsToReplace;
        if (sizeof($oMenuFromPlugin)) {
            if (array_key_exists($strMenuName, $oMenuFromPlugin)) {
                return $oMenuFromPlugin[$strMenuName];
            }
        }
    }

    /**
     * Register a callBackFile in the singleton
     * @param string $Namespace
     * @param string $CallBackFile
     * @throws Exception
     */
    public function registerImportProcessCallback($Namespace, $CallBackFile)
    {
        try {
            $found = false;
            /** @var ImportCallBack $import */
            foreach ($this->_aImportProcessCallbackFile as $import) {
                if ($import->equalCallBackFileTo($CallBackFile) && $import->equalNamespaceTo($Namespace)) {
                    $import->setCallBackFile($CallBackFile);
                    $found = true;
                }
            }
            if (!$found) {
                $CallBackFile = new ImportCallBack($Namespace, $CallBackFile);
                $this->_aImportProcessCallbackFile[] = $CallBackFile;
            }
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Return all callBackFiles registered
     * @return array
     */
    public function getImportProcessCallback()
    {
        return $this->_aImportProcessCallbackFile;
    }

    /**
     * Register a callBackFile
     * @param string $callBackFile
     * @throws Exception
     */
    public function registerOpenReassignCallback($callBackFile)
    {
        try {
            $found = false;
            /** @var OpenReassignCallback $open */
            foreach ($this->_aOpenReassignCallback as $open) {
                if ($open->equalCallBackFileTo($callBackFile)) {
                    $open->setCallBackFile($callBackFile);
                    $found = true;
                }
            }
            if (!$found) {
                $callBackFile = new OpenReassignCallback($callBackFile);
                $this->_aOpenReassignCallback[] = $callBackFile;
            }
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Return all callBackFiles registered
     *
     * @return array
     */
    public function getOpenReassignCallback()
    {
        return $this->_aOpenReassignCallback;
    }

    /**
     * Return all plugins
     * @return array
     */
    public function getPluginsData()
    {
        return $this->_aPlugins;
    }

    /**
     * The following function method extracts the plugin if exists one
     * with the same uppercase characters, this is required for the
     *
     * @param string $code
     * @return array|object
     */
    public function getPluginByCode($code)
    {
        $plugin = new stdClass();
        foreach ($this->_aPlugins as $plugin) {
            $plugin = (array)$plugin;
            if (strtoupper($plugin['sNamespace']) == $code) {
                return (object)$plugin;
            }
        }
        return $plugin;
    }

    /**
     * Checks if the plugin name is Enterprise Plugin
     *
     * @param string $pluginName Plugin name
     * @param string $path Path to plugin
     *
     * @return bool Returns TRUE when plugin name is Enterprise Plugin, FALSE otherwise
     */
    public function isEnterprisePlugin($pluginName, $path = null)
    {
        $path = (!is_null($path) && $path != '') ? rtrim($path, '/\\') . PATH_SEP : PATH_PLUGINS;
        $pluginFile = $pluginName . '.php';

        //Return
        return preg_match(
            '/^.*class\s+' . $pluginName . 'Plugin\s+extends\s+(?:enterprisePlugin)\s*\{.*$/i',
            str_replace(["\n", "\r", "\t"], ' ', file_get_contents($path . $pluginFile))
        );
    }

    /**
     * Registry in an array routes for js or css files.
     * @param string $pluginName
     * @param string $pathFile
     * @throws Exception
     */
    public function registerDesignerSourcePath($pluginName, $pathFile)
    {
        try {
            $flagFound = false;

            foreach ($this->_arrayDesignerSourcePath as $designer) {
                if ($designer->pluginName == $pluginName && $designer->pathFile == $pathFile) {
                    $flagFound = true;
                    break;
                }
            }

            if (!$flagFound) {
                $obj = new stdClass();
                $obj->pluginName = $pluginName;
                $obj->pathFile = $pathFile;

                $this->_arrayDesignerSourcePath[] = $obj;
            }
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * You obtain an array containing the routes recorded by the
     * function registerDesignerSourcePath.
     * @return array
     * @throws Exception
     */
    public function getDesignerSourcePath()
    {
        try {
            return $this->_arrayDesignerSourcePath;
        } catch (Exception $e) {
            throw $e;
        }
    }
}
