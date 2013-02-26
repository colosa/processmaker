<?php
/**
 * class.plugin.php
 * @package workflow.engine.classes
 *
 * ProcessMaker Open Source Edition
 * Copyright (C) 2004 - 2011 Colosa Inc.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * For more information, contact Colosa Inc, 2566 Le Jeune Rd.,
 * Coral Gables, FL, 33134, USA, or email info@colosa.com.
 */

require_once 'class.pluginRegistry.php';

define('G_PLUGIN_CLASS',             1);
define('PM_CREATE_CASE',             1001);
define('PM_UPLOAD_DOCUMENT',         1002);
define('PM_CASE_DOCUMENT_LIST',      1003);
define('PM_BROWSE_CASE',             1004);
define('PM_NEW_PROCESS_LIST',        1005);
define('PM_NEW_PROCESS_SAVE',        1006);
define('PM_NEW_DYNAFORM_LIST',       1007);
define('PM_NEW_DYNAFORM_SAVE',       1008);
define('PM_EXTERNAL_STEP',           1009);
define('PM_CASE_DOCUMENT_LIST_ARR',  1010);
define('PM_LOGIN',                   1011);
define('PM_UPLOAD_DOCUMENT_BEFORE',  1012);
define('PM_CREATE_NEW_DELEGATION',   1013);
define('PM_SINGLE_SIGN_ON',          1014);
define('PM_GET_CASES_AJAX_LISTENER', 1015);

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
        $this->sNamespace    = $sNamespace;
        $this->sClassName    = $sNamespace . 'Plugin';
        $this->sPluginFolder = $sNamespace;
        $this->sFilename     = $sFilename;
    }

    /**
     * With this function we can register the MENU
     * @param string $menuId
     * @param string $menuFilename
     * @return void
     */
    public function registerMenu($menuId, $menuFilename)
    {
        $oPluginRegistry =& PMPluginRegistry::getSingleton();
        $sMenuFilename   = PATH_PLUGINS . $this->sPluginFolder . PATH_SEP . $menuFilename;
        $oPluginRegistry->registerMenu($this->sNamespace, $menuId, $sMenuFilename);
    }

    /**
     * With this function we can register a dashlet class
     * param
     * @return void
     */
    public function registerDashlets()
    {
        $oPluginRegistry =& PMPluginRegistry::getSingleton();
        $oPluginRegistry->registerDashlets($this->sNamespace);
    }


    /**
     * With this function we can register the report
     * param
     * @return void
     */
    public function registerReport()
    {
        $oPluginRegistry =& PMPluginRegistry::getSingleton();
        $oPluginRegistry->registerReport($this->sNamespace);
    }

    /**
     * With this function we can register the pm's function
     * param
     * @return void
     */
    public function registerPmFunction()
    {
        $oPluginRegistry =& PMPluginRegistry::getSingleton();
        $oPluginRegistry->registerPmFunction($this->sNamespace);
    }

    /**
     * With this function we can set the company's logo
     * param
     * @return void
     */
    public function setCompanyLogo($filename)
    {
        $oPluginRegistry =& PMPluginRegistry::getSingleton();
        $oPluginRegistry->setCompanyLogo($this->sNamespace, $filename);
    }

    /**
     * With this function we can register the pm's function
     * param
     * @return void
     */
    public function redirectLogin($role, $pathMethod)
    {
        $oPluginRegistry =& PMPluginRegistry::getSingleton();
        $oPluginRegistry->registerRedirectLogin($this->sNamespace, $role, $pathMethod);
    }

    /**
     * Register a folder for methods
     *
     * @param unknown_type $sFolderName
     */
    public function registerFolder($sFolderId, $sFolderName)
    {
        $oPluginRegistry =& PMPluginRegistry::getSingleton();
        $oPluginRegistry->registerFolder($this->sNamespace, $sFolderId, $sFolderName);
    }

    /**
     * With this function we can register the steps
     * param
     * @return void
     */
    public function registerStep($sStepId, $sStepName, $sStepTitle, $sSetupStepPage  = '')
    {
        $oPluginRegistry =& PMPluginRegistry::getSingleton();
        $oPluginRegistry->registerStep( $this->sNamespace, $sStepId, $sStepName, $sStepTitle, $sSetupStepPage );
    }

    /**
     * With this function we can register the triggers
     * @param string $sTriggerId
     * @param string $sTriggerName
     * @return void
     */
    public function registerTrigger($sTriggerId, $sTriggerName)
    {
        $oPluginRegistry =& PMPluginRegistry::getSingleton();
        $oPluginRegistry->registerTrigger($this->sNamespace, $sTriggerId, $sTriggerName);
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
        $oPluginRegistry =& PMPluginRegistry::getSingleton();
        $sPageFilename = PATH_PLUGINS . $this->sPluginFolder . PATH_SEP . $templateFilename;
        $oPluginRegistry->registerBreakPageTemplate ($this->sNamespace, $pageId, $sPageFilename);
    }

    /**
     * With this function we can register a CSS
     * @param string $sPage
     * @return void
     */
    public function registerCss($sCssFile)
    {
        $oPluginRegistry =& PMPluginRegistry::getSingleton();
        $oPluginRegistry->registerCss($this->sNamespace, $sCssFile);
    }

    /**
     * With this function we can register the toolbar file for dynaform editor
     * @param string $menuId
     * @param string $menuFilename
     * @return void
     */
    public function registerToolbarFile($sToolbarId, $filename)
    {
        $oPluginRegistry =& PMPluginRegistry::getSingleton();
        $sFilename   = PATH_PLUGINS . $this->sPluginFolder . PATH_SEP . $filename;
        $oPluginRegistry->registerToolbarFile($this->sNamespace, $sToolbarId, $sFilename);
    }

    /**
     * With this function we can register a Case Scheduler Plugin/Addon
     * param
     * @return void
     */
    public function registerCaseSchedulerPlugin(
        $sActionId,
        $sActionForm,
        $sActionSave,
        $sActionExecute,
        $sActionGetFields
    ) {
        $oPluginRegistry =& PMPluginRegistry::getSingleton();
        $oPluginRegistry->registerCaseSchedulerPlugin(
            $this->sNamespace, $sActionId, $sActionForm, $sActionSave, $sActionExecute, $sActionGetFields
        );
    }

    /**
     * With this function we can register a task extended property
     * @param string $sPage
     * @return void
     */
    public function registerTaskExtendedProperty($sPage, $sName, $sIcon="")
    {
        $oPluginRegistry =& PMPluginRegistry::getSingleton();
        $oPluginRegistry->registerTaskExtendedProperty ( $this->sNamespace, $sPage, $sName, $sIcon );
    }

    /**
     * Register a plugin javascript to run with core js script at same runtime
     * @param string $coreJsFile
     * @param array/string $pluginJsFile
     * @return void
     */
    function registerJavascript($sCoreJsFile, $pluginJsFile)
    {
        $oPluginRegistry =& PMPluginRegistry::getSingleton();
        $oPluginRegistry->registerJavascript($this->sNamespace, $sCoreJsFile, $pluginJsFile);
    }

    /**
     * Unregister a plugin javascript
     * @param string $coreJsFile
     * @param array/string $pluginJsFile
     * @return void
     */
    public function unregisterJavascript($sCoreJsFile, $pluginJsFile)
    {
        $oPluginRegistry =& PMPluginRegistry::getSingleton();
        $oPluginRegistry->unregisterJavascript($this->sNamespace, $sCoreJsFile, $pluginJsFile);
    }

    public function registerDashboard()
    { // Dummy function for backwards compatibility
    }

    public function getExternalStepAction()
    {
        $oPluginRegistry =& PMPluginRegistry::getSingleton();
        return $oPluginRegistry->getSteps();
    }

    /**
     * Register a rest service and expose it
     *
     * @author  Erik Amaru Ortiz <erik@colosa.com>
     * @param string $coreJsFile
     * @param array/string $pluginJsFile
     * @return void
     */
    function registerRestService($classname, $path = '')
    {
        $oPluginRegistry =& PMPluginRegistry::getSingleton();
        $oPluginRegistry->registerRestService($this->sNamespace, $classname, $path);
    }

    /**
     * Unregister a rest service
     *
     * @author  Erik Amaru Ortiz <erik@colosa.com>
     * @param string $coreJsFile
     * @param array/string $pluginJsFile
     * @return void
     */
    function unregisterRestService($classname, $path)
    {
        $oPluginRegistry =& PMPluginRegistry::getSingleton();
        $oPluginRegistry->unregisterRestService($this->sNamespace, $classname, $path);
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
        $this->sMenuId    = $sMenuId;
        $this->sFilename  = $sFilename;
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
        $this->sToolbarId    = $sToolbarId;
        $this->sFilename  = $sFilename;
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
        $this->sCssFile    = $sCssFile;
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
        $this->sNamespace   = $sNamespace;
        $this->sTriggerId   = $sTriggerId;
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
        $this->sNamespace  = $sNamespace;
        $this->sFolderId   = $sFolderId;
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
        $this->sNamespace     = $sNamespace;
        $this->sStepId        = $sStepId;
        $this->sStepName      = $sStepName;
        $this->sStepTitle     = $sStepTitle;
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
        $this->sNamespace  = $sNamespace;
        $this->sRoleCode   = $sRoleCode;
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
    public function __construct(
        $sProcessUid,
        $sProcessTitle,
        $sApplicationUid,
        $sApplicationTitle,
        $sUserUid,
        $sUserLogin = '',
        $sUserFullName =''
    ) {
        $this->sProcessUid       = $sProcessUid;
        $this->sProcessTitle     = $sProcessTitle;
        $this->sApplicationUid   = $sApplicationUid;
        $this->sApplicationTitle = $sApplicationTitle;
        $this->sUserUid          = $sUserUid;
        $this->sUserLogin        = $sUserLogin;
        $this->sUserFullName     = $sUserFullName;
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
        $this->sUserUid        = $sUserUid;
        $this->sFilename       = $sFilename;
        $this->sFileTitle      = $sFileTitle;
        $this->sDocumentUid    = $sDocumentUid;
        $this->bUseOutputFolder = false;
        $this->iVersion    = $iVersion;
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
        $this->lPassword    = $lPassword;
        $this->lSession  = $lSession;
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
        $this->sNamespace       = $sNamespace;
        $this->sActionId        = $sActionId;
        $this->sActionForm      = $sActionForm;
        $this->sActionSave      = $sActionSave;
        $this->sActionExecute   = $sActionExecute;
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
        $this->sPage    = $sPage;
        $this->sName    = $sName;
        $this->sIcon    = $sIcon;
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
        $this->sPage    = $sPage;
        $this->sName    = $sName;
        $this->sIcon    = $sIcon;
    }
}

