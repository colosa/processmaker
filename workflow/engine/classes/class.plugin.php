<?php

use ProcessMaker\Plugins\PluginRegistry;

/**
 * @package workflow.engine.classes
 */
class PMPlugin
{
    public $sNamespace;
    public $sClassName;
    public $sFilename = null;
    public $iVersion = 0;
    public $sFriendlyName = null;
    public $sPluginFolder = '';
    public $aWorkspaces = null;
    public $bPrivate = false;

    /**
     * This function sets values to the plugin
     * @param string $sNamespace
     * @param string $sFilename
     * @return void
     */
    public function PMPlugin($sNamespace, $sFilename = null)
    {
        $this->sNamespace = $sNamespace;
        $this->sClassName = $sNamespace . 'Plugin';
        $this->sPluginFolder = $sNamespace;
        $this->sFilename = $sFilename;
    }

    /**
     * With this function we can register the MENU
     * @param string $menuId
     * @param string $menuFilename
     * @return void
     */
    public function registerMenu($menuId, $menuFilename)
    {
        $sMenuFilename = ($this->sClassName == 'enterprisePlugin') ? PATH_CORE . 'methods' . PATH_SEP . 'enterprise' . PATH_SEP . $menuFilename : PATH_PLUGINS . $this->sPluginFolder . PATH_SEP . $menuFilename;
        PluginRegistry::loadSingleton()->registerMenu($this->sNamespace, $menuId, $sMenuFilename);
    }

    /**
     * With this function we can register a dashlet class
     * param
     * @return void
     */
    public function registerDashlets()
    {
        PluginRegistry::loadSingleton()->registerDashlets($this->sNamespace);
    }

    /**
     * With this function we can register the report
     * param
     * @return void
     */
    public function registerReport()
    {
        PluginRegistry::loadSingleton()->registerReport($this->sNamespace);
    }

    /**
     * With this function we can register the pm's function
     * param
     * @return void
     */
    public function registerPmFunction()
    {
        PluginRegistry::loadSingleton()->registerPmFunction($this->sNamespace);
    }

    /**
     * With this function we can set the company's logo
     * param
     * @return void
     */
    public function setCompanyLogo($filename)
    {
        PluginRegistry::loadSingleton()->setCompanyLogo($this->sNamespace, $filename);
    }

    /**
     * With this function we can register the pm's function
     * param
     * @return void
     */
    public function redirectLogin($role, $pathMethod)
    {
        PluginRegistry::loadSingleton()->registerRedirectLogin($this->sNamespace, $role, $pathMethod);
    }

    /**
     * Register a folder for methods
     *
     * @param unknown_type $sFolderName
     */
    public function registerFolder($sFolderId, $sFolderName)
    {
        PluginRegistry::loadSingleton()->registerFolder($this->sNamespace, $sFolderId, $sFolderName);
    }

    /**
     * With this function we can register the steps
     * param
     * @return void
     */
    public function registerStep($sStepId, $sStepName, $sStepTitle, $sSetupStepPage = '')
    {
        PluginRegistry::loadSingleton()->registerStep($this->sNamespace, $sStepId, $sStepName, $sStepTitle, $sSetupStepPage);
    }

    /**
     * With this function we can register the triggers
     * @param string $sTriggerId
     * @param string $sTriggerName
     * @return void
     */
    public function registerTrigger($sTriggerId, $sTriggerName)
    {
        PluginRegistry::loadSingleton()->registerTrigger($this->sNamespace, $sTriggerId, $sTriggerName);
    }

    /**
     * With this function we can delete a file
     * @param string $sFilename
     * @param string $bAbsolutePath
     * @return void
     */
    public function delete($sFilename, $bAbsolutePath = false)
    {
        if (!$bAbsolutePath) {
            $sFilename = PATH_PLUGINS . $this->sPluginFolder . PATH_SEP . $sFilename;
        }
        @unlink($sFilename);
    }

    /**
     * With this function we can copy a files
     * @param string $sSouce
     * @param string $sTarget
     * @param string $bSourceAbsolutePath
     * @param string $bTargetAbsolutePath
     * @return void
     */
    public function copy($sSouce, $sTarget, $bSourceAbsolutePath = false, $bTargetAbsolutePath = false)
    {
        if (!$bSourceAbsolutePath) {
            $sSouce = PATH_PLUGINS . $this->sPluginFolder . PATH_SEP . $sSouce;
        }
        if (!$bTargetAbsolutePath) {
            $sTarget = PATH_PLUGINS . $this->sPluginFolder . PATH_SEP . $sTarget;
        }

        G::verifyPath(dirname($sTarget), true);
        @copy($sSouce, $sTarget);
    }

    /**
     * With this function we can rename a files
     * @param string $sSouce
     * @param string $sTarget
     * @param string $bSourceAbsolutePath
     * @param string $bTargetAbsolutePath
     * @return void
     */
    public function rename($sSouce, $sTarget, $bSourceAbsolutePath = false, $bTargetAbsolutePath = false)
    {
        if (!$bSourceAbsolutePath) {
            $sSouce = PATH_PLUGINS . $this->sPluginFolder . PATH_SEP . $sSouce;
        }
        if (!$bTargetAbsolutePath) {
            $sTarget = PATH_PLUGINS . $this->sPluginFolder . PATH_SEP . $sTarget;
        }

        G::verifyPath(dirname($sTarget), true);
        @chmod(dirname($sTarget), 0777);
        @rename($sSouce, $sTarget);
    }

    /**
     * This function registers a page who is break
     * @param string $pageId
     * @param string $templateFilename
     * @return void
     */
    public function registerBreakPageTemplate($pageId, $templateFilename)
    {
        $sPageFilename = PATH_PLUGINS . $this->sPluginFolder . PATH_SEP . $templateFilename;
        PluginRegistry::loadSingleton()->registerBreakPageTemplate($this->sNamespace, $pageId, $sPageFilename);
    }

    /**
     * With this function we can register a CSS
     * @param string $sPage
     * @return void
     */
    public function registerCss($sCssFile)
    {
        PluginRegistry::loadSingleton()->registerCss($this->sNamespace, $sCssFile);
    }

    /**
     * With this function we can register the toolbar file for dynaform editor
     * @param string $menuId
     * @param string $menuFilename
     * @return void
     */
    public function registerToolbarFile($sToolbarId, $filename)
    {
        $sFilename = PATH_PLUGINS . $this->sPluginFolder . PATH_SEP . $filename;
        PluginRegistry::loadSingleton()->registerToolbarFile($this->sNamespace, $sToolbarId, $sFilename);
    }

    /**
     * With this function we can register a Case Scheduler Plugin/Addon
     * param
     * @return void
     */
    public function registerCaseSchedulerPlugin($sActionId, $sActionForm, $sActionSave, $sActionExecute, $sActionGetFields)
    {
        PluginRegistry::loadSingleton()->registerCaseSchedulerPlugin(
                $this->sNamespace, $sActionId, $sActionForm, $sActionSave, $sActionExecute, $sActionGetFields
        );
    }

    /**
     * With this function we can register a task extended property
     * @param string $sPage
     * @return void
     */
    public function registerTaskExtendedProperty($sPage, $sName, $sIcon = "")
    {
        PluginRegistry::loadSingleton()->registerTaskExtendedProperty($this->sNamespace, $sPage, $sName, $sIcon);
    }

    /**
     * Register a plugin javascript to run with core js script at same runtime
     * @param string $coreJsFile
     * @param array/string $pluginJsFile
     * @return void
     */
    public function registerJavascript($sCoreJsFile, $pluginJsFile)
    {
        PluginRegistry::loadSingleton()->registerJavascript($this->sNamespace, $sCoreJsFile, $pluginJsFile);
    }

    /**
     * Unregister a plugin javascript
     * @param string $coreJsFile
     * @param array/string $pluginJsFile
     * @return void
     */
    public function unregisterJavascript($sCoreJsFile, $pluginJsFile)
    {
        PluginRegistry::loadSingleton()->unregisterJavascript($this->sNamespace, $sCoreJsFile, $pluginJsFile);
    }

    public function registerDashboard()
    { // Dummy function for backwards compatibility
    }

    public function getExternalStepAction()
    {
        return PluginRegistry::loadSingleton()->getSteps();
    }

    /**
     * Register a rest service and expose it
     *
     * @author  Erik Amaru Ortiz <erik@colosa.com>
     * @param string $coreJsFile
     * @param array/string $pluginJsFile
     * @return void
     */
    public function registerRestService()
    {
        PluginRegistry::loadSingleton()->registerRestService($this->sNamespace);
    }

    /**
     * Register a extend rest service and expose it
     *
     * @param string $className that is name class to extends
     */
    public function registerExtendsRestService($className)
    {
        PluginRegistry::loadSingleton()->registerExtendsRestService($this->sNamespace, $className);
    }

    /**
     * Register a extend rest service and expose it
     *
     * @param string $className that is name class to extends
     */
    public function disableExtendsRestService($className)
    {
        PluginRegistry::loadSingleton()->disableExtendsRestService($this->sNamespace, $className);
    }

    /**
     * Unregister a rest service
     *
     * @author  Erik Amaru Ortiz <erik@colosa.com>
     * @param string $coreJsFile
     * @param array/string $pluginJsFile
     * @return void
     */
    public function unregisterRestService($classname, $path)
    {
        PluginRegistry::loadSingleton()->unregisterRestService($this->sNamespace, $classname, $path);
    }

    /**
     * With this function we can register a cron file
     * param string $cronFile
     * @return void
     */
    public function registerCronFile($cronFile)
    {
        PluginRegistry::loadSingleton()->registerCronFile($this->sNamespace, $cronFile);
    }

    public function enableRestService($enable)
    {
        PluginRegistry::loadSingleton()->enableRestService($this->sNamespace, $enable);
    }

    /**
     * Register designer menu file
     *
     * @param string $file Designer menu file
     *
     * @return void
     */
    public function registerDesignerMenu($file)
    {
        try {
            PluginRegistry::loadSingleton()->registerDesignerMenu($this->sNamespace, $file);
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Changes the menu properties from the given processmaker section and menu id
     *
     * @param array $from
     *
     * @param array $options
     *
     * @return void
     */
    public function registerMenuOptionsToReplace($from = array(), $options = array())
    {
        try {
            PluginRegistry::loadSingleton()->registerMenuOptionsToReplace($this->sNamespace, $from, $options);
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * callBack File after import process
     *
     * @param string $callBackFile
     *
     * @return void
     */
    public function registerImportProcessCallback($callBackFile = '')
    {
        try {
            PluginRegistry::loadSingleton()->registerImportProcessCallback($this->sNamespace, $callBackFile);
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * callBack File on reassign
     *
     * @param string $callBackFile
     *
     * @return void
     */
    public function registerOpenReassignCallback($callBackFile = '')
    {
        try {
            PluginRegistry::loadSingleton()->registerOpenReassignCallback($callBackFile);
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Path registry to file js or css.
     * @param type $pathFile
     * @param string $scope
     * @throws Exception
     */
    public function registerDesignerSourcePath($pathFile, $scope = null)
    {
        if ($scope === null) {
            $scope = '/plugin/' . $this->sNamespace . '/';
        }
        try {
            PluginRegistry::loadSingleton()->registerDesignerSourcePath($this->sNamespace, $scope . $pathFile);
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Enable build js or css into build.json paths.
     * @param type $sourcePath
     */
    public function enableExtensionSources($sourcePath = 'config/build.json')
    {
        $path = PATH_PLUGINS . $this->sPluginFolder . "/";
        $buildFile = $path . $sourcePath;
        if (is_file($buildFile)) {
            $buildObjects = G::json_decode(file_get_contents($buildFile));
            foreach ($buildObjects as $item) {
                $item->path = $path . rtrim($item->path, "/\\");
                $extensionPath = "extension-" . $item->name . "-" . G::browserCacheFilesGetUid() . "." . $item->extension;
                $file = $path . "public_html/" . $extensionPath;
                @file_put_contents($file, "", LOCK_EX);
                foreach ($item->files as $name) {
                    $content = file_get_contents($item->path . "/" . $name) . "\n";
                    @file_put_contents($file, $content, FILE_APPEND | LOCK_EX);
                }
                $this->registerDesignerSourcePath($extensionPath);
            }
        }
    }

    /**
     * Gets an array of plugins that are in the processmaker plugin directory.
     * @param string $workspace
     * @return array
     */
    public static function getListPluginsManager($workspace)
    {
        $items = array();
        $aPluginsPP = array();
        if (is_file(PATH_PLUGINS . 'enterprise/data/data')) {
            $aPlugins = unserialize(trim(file_get_contents(PATH_PLUGINS . 'enterprise/data/data')));
            foreach ($aPlugins as $aPlugin) {
                $aPluginsPP[] = substr($aPlugin['sFilename'], 0, strpos($aPlugin['sFilename'], '-')) . '.php';
            }
        }
        $oPluginRegistry = PluginRegistry::loadSingleton();
        if ($handle = opendir(PATH_PLUGINS)) {
            while (false !== ($file = readdir($handle))) {
                if (in_array($file, $aPluginsPP)) {
                    continue;
                }
                if (strpos($file, '.php', 1) && is_file(PATH_PLUGINS . $file)) {
                    include_once(PATH_PLUGINS . $file);
                    /** @var \ProcessMaker\Plugins\Interfaces\PluginDetail $pluginDetail */
                    $pluginDetail = $oPluginRegistry->getPluginDetails($file);
                    if ($pluginDetail === null) {
                        continue;
                    }
                    $status_label = $pluginDetail->isEnabled() ? G::LoadTranslation('ID_ENABLED') : G::LoadTranslation('ID_DISABLED');
                    $status = $pluginDetail->isEnabled() ? 1 : 0;
                    if ($pluginDetail->getWorkspaces()) {
                        if (!is_array($pluginDetail->getWorkspaces())) {
                            $pluginDetail->setWorkspaces(array());
                        }
                        if (!in_array($workspace, $pluginDetail->getWorkspaces())) {
                            continue;
                        }
                    }
                    $setup = $pluginDetail->getSetupPage() != '' && $pluginDetail->isEnabled() ? '1' : '0';

                    if (isset($pluginDetail) && !$pluginDetail->isPrivate()) {
                        $items[] = [
                            'id' => (count($items) + 1),
                            'namespace' => $pluginDetail->sNamespace,
                            'title' => $pluginDetail->sFriendlyName . "\n(" . $pluginDetail->sNamespace . '.php)',
                            'className' => $pluginDetail->sNamespace,
                            'description' => $pluginDetail->sDescription,
                            'version' => $pluginDetail->iVersion,
                            'setupPage' => $pluginDetail->sSetupPage,
                            'status_label' => $status_label,
                            'status' => $status,
                            'setup' => $setup,
                            'sFile' => $file,
                            'sStatusFile' => $pluginDetail->enabled
                        ];
                    }
                }
            }
            closedir($handle);
        }
        return $items;
    }

    /**
     * Gets a general list of all plugins within processmaker per workspace.
     *
     * @param string $workspace
     * @return array
     */
    public static function getListAllPlugins($workspace)
    {
        $oPluginRegistry = PluginRegistry::loadSingleton();
        $items = [];
        if ($handle = opendir(PATH_PLUGINS)) {
            while (false !== ($file = readdir($handle))) {
                if (strpos($file, '.php', 1) && is_file(PATH_PLUGINS . $file)) {
                    include_once(PATH_PLUGINS . $file);
                    /** @var \ProcessMaker\Plugins\Interfaces\PluginDetail $detail */
                    $detail = $oPluginRegistry->getPluginDetails($file);
                    if ($detail !== null) {
                        $items[] = $detail;
                    }
                }
            }
            closedir($handle);
        }
        return $items;
    }
}

class menuDetail
{
    public $sNamespace;
    public $sMenuId;
    public $sFilename;

    /**
     * This function is the constructor of the menuDetail class
     * param string $sNamespace
     * param string $sMenuId
     * param string $sFilename
     * @return void
     */
    public function __construct($sNamespace, $sMenuId, $sFilename)
    {
        $this->sNamespace = $sNamespace;
        $this->sMenuId = $sMenuId;
        $this->sFilename = $sFilename;
    }
}

class toolbarDetail
{
    public $sNamespace;
    public $sToolbarId;
    public $sFilename;

    /**
     * This function is the constructor of the menuDetail class
     * param string $sNamespace
     * param string $sMenuId
     * param string $sFilename
     * @return void
     */
    public function __construct($sNamespace, $sToolbarId, $sFilename)
    {
        $this->sNamespace = $sNamespace;
        $this->sToolbarId = $sToolbarId;
        $this->sFilename = $sFilename;
    }
}

class cssFile
{
    public $sNamespace;
    public $sCssFile;

    /**
     * This function is the constructor of the cssFile class
     * param string $sNamespace
     * param string $sPage
     * @return void
     */
    public function __construct($sNamespace, $sCssFile)
    {
        $this->sNamespace = $sNamespace;
        $this->sCssFile = $sCssFile;
    }
}

class triggerDetail
{
    public $sNamespace;
    public $sTriggerId;
    public $sTriggerName;

    /**
     * This function is the constructor of the triggerDetail class
     * param string $sNamespace
     * param string $sTriggerId
     * param string $sTriggerName
     * @return void
     */
    public function __construct($sNamespace, $sTriggerId, $sTriggerName)
    {
        $this->sNamespace = $sNamespace;
        $this->sTriggerId = $sTriggerId;
        $this->sTriggerName = $sTriggerName;
    }
}

class folderDetail
{
    public $sNamespace;
    public $sFolderId;
    public $sFolderName;

    /**
     * This function is the constructor of the folderDetail class
     * param string $sNamespace
     * param string $sFolderId
     * param string $sFolderName
     * @return void
     */
    public function __construct($sNamespace, $sFolderId, $sFolderName)
    {
        $this->sNamespace = $sNamespace;
        $this->sFolderId = $sFolderId;
        $this->sFolderName = $sFolderName;
    }
}

class stepDetail
{
    public $sNamespace;
    public $sStepId;
    public $sStepName;
    public $sStepTitle;
    public $sSetupStepPage;

    /**
     * This function is the constructor of the stepDetail class
     * param string $sNamespace
     * param string $sStepId
     * param string $sStepName
     * param string $sStepTitle
     * param string $sSetupStepPage
     * @return void
     */
    public function __construct($sNamespace, $sStepId, $sStepName, $sStepTitle, $sSetupStepPage)
    {
        $this->sNamespace = $sNamespace;
        $this->sStepId = $sStepId;
        $this->sStepName = $sStepName;
        $this->sStepTitle = $sStepTitle;
        $this->sSetupStepPage = $sSetupStepPage;
    }
}

class redirectDetail
{
    public $sNamespace;
    public $sRoleCode;
    public $sPathMethod;

    /**
     * This function is the constructor of the redirectDetail class
     * param string $sNamespace
     * param string $sRoleCode
     * param string $sPathMethod
     * @return void
     */
    public function __construct($sNamespace, $sRoleCode, $sPathMethod)
    {
        $this->sNamespace = $sNamespace;
        $this->sRoleCode = $sRoleCode;
        $this->sPathMethod = $sPathMethod;
    }
}

class folderData
{
    public $sProcessUid;
    public $sProcessTitle;
    public $sApplicationUid;
    public $sApplicationTitle;
    public $sUserUid;
    public $sUserLogin;
    public $sUserFullName;

    /**
     * This function is the constructor of the folderData class
     * param string $sProcessUid
     * param string $sProcessTitle
     * param string $sApplicationUid
     * param string $sApplicationTitle
     * param string $sUserUid
     * param string $sUserLogin
     * param string $sUserFullName
     * @return void
     */
    public function __construct($sProcessUid, $sProcessTitle, $sApplicationUid, $sApplicationTitle, $sUserUid, $sUserLogin = '', $sUserFullName = '')
    {
        $this->sProcessUid = $sProcessUid;
        $this->sProcessTitle = $sProcessTitle;
        $this->sApplicationUid = $sApplicationUid;
        $this->sApplicationTitle = $sApplicationTitle;
        $this->sUserUid = $sUserUid;
        $this->sUserLogin = $sUserLogin;
        $this->sUserFullName = $sUserFullName;
    }
}

class uploadDocumentData
{
    public $sApplicationUid;
    public $sUserUid;
    public $sFilename;
    public $sFileTitle;
    public $sDocumentUid;
    public $bUseOutputFolder;
    public $iVersion;

    /**
     * This function is the constructor of the uploadDocumentData class
     * param string $sApplicationUid
     * param string $sUserUid
     * param string $sFilename
     * param string $sFileTitle
     * param string $sDocumentUid
     * param integer $iVersion
     * @return void
     */
    public function __construct($sApplicationUid, $sUserUid, $sFilename, $sFileTitle, $sDocumentUid, $iVersion = 1)
    {
        $this->sApplicationUid = $sApplicationUid;
        $this->sUserUid = $sUserUid;
        $this->sFilename = $sFilename;
        $this->sFileTitle = $sFileTitle;
        $this->sDocumentUid = $sDocumentUid;
        $this->bUseOutputFolder = false;
        $this->iVersion = $iVersion;
    }
}

class loginInfo
{
    public $lName;
    public $lPassword;
    public $lSession;

    /**
     * This function is the constructor of the loginInfo class
     * param string $lName
     * param string $lPassword
     * param string $lSession
     * @return void
     */
    public function __construct($lName, $lPassword, $lSession)
    {
        $this->lName = $lName;
        $this->lPassword = $lPassword;
        $this->lSession = $lSession;
    }
}

class caseSchedulerPlugin
{
    public $sNamespace;
    public $sActionId;
    public $sActionForm;
    public $sActionSave;
    public $sActionExecute;
    public $sActionGetFields;

    /**
     * This function is the constructor of the caseSchedulerPlugin class
     * param string $sNamespace
     * param string $sActionId
     * param string $sActionForm
     * param string $sActionSave
     * param string $sActionExecute
     * param string $sActionGetFields
     * @return void
     */
    public function __construct($sNamespace, $sActionId, $sActionForm, $sActionSave, $sActionExecute, $sActionGetFields)
    {
        $this->sNamespace = $sNamespace;
        $this->sActionId = $sActionId;
        $this->sActionForm = $sActionForm;
        $this->sActionSave = $sActionSave;
        $this->sActionExecute = $sActionExecute;
        $this->sActionGetFields = $sActionGetFields;
    }
}

class taskExtendedProperty
{
    public $sNamespace;
    public $sPage;
    public $sName;
    public $sIcon;

    /**
     * This function is the constructor of the taskExtendedProperty class
     * param string $sNamespace
     * param string $sPage
     * param string $sName
     * param string $sIcon
     * @return void
     */
    public function __construct($sNamespace, $sPage, $sName, $sIcon)
    {
        $this->sNamespace = $sNamespace;
        $this->sPage = $sPage;
        $this->sName = $sName;
        $this->sIcon = $sIcon;
    }
}

class dashboardPage
{
    public $sNamespace;
    public $sPage;
    public $sName;
    public $sIcon;

    /**
     * This function is the constructor of the dashboardPage class
     * param string $sNamespace
     * param string $sPage
     * @return void
     */
    public function __construct($sNamespace, $sPage, $sName, $sIcon)
    {
        $this->sNamespace = $sNamespace;
        $this->sPage = $sPage;
        $this->sName = $sName;
        $this->sIcon = $sIcon;
    }
}

class cronFile
{
    public $namespace;
    public $cronFile;

    /**
     * This function is the constructor of the cronFile class
     * param string $namespace
     * param string $cronFile
     * @return void
     */
    public function __construct($namespace, $cronFile)
    {
        $this->namespace = $namespace;
        $this->cronFile = $cronFile;
    }
}

class importCallBack
{
    public $namespace;
    public $callBackFile;

    /**
     * This function is the constructor of the cronFile class
     * param string $namespace
     * param string $callBackFile
     * @return void
     */
    public function __construct($namespace, $callBackFile)
    {
        $this->namespace = $namespace;
        $this->callBackFile = $callBackFile;
    }
}

class OpenReassignCallback
{
    public $callBackFile;

    /**
     * This function is the constructor of the cronFile class
     * param string $namespace
     * param string $callBackFile
     * @return void
     */
    public function __construct($callBackFile)
    {
        $this->callBackFile = $callBackFile;
    }
}

