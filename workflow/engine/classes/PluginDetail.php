<?php

//@todo: Pending until class.plugin.php is solved.
require_once 'class.plugin.php';

/**
 *
 * @package workflow.engine.classes
 */
class PluginDetail
{
    public $sNamespace;
    public $sClassName;
    public $sFriendlyName = null;
    public $sDescription = null;
    public $sSetupPage = null;
    public $sFilename;
    public $sPluginFolder = '';
    public $sCompanyLogo = '';
    public $iVersion = 0;
    public $enabled = false;
    public $aWorkspaces = null;
    public $bPrivate = false;

    /**
     * This function is the constructor of the pluginDetail class
     *
     * @param string $sNamespace
     * @param string $sClassName
     * @param string $sFilename
     * @param string $sFriendlyName
     * @param string $sPluginFolder
     * @param string $sDescription
     * @param string $sSetupPage
     * @param integer $iVersion
     * @return void
     */
    public function __construct($sNamespace, $sClassName, $sFilename, $sFriendlyName = '', $sPluginFolder = '', $sDescription = '', $sSetupPage = '', $iVersion = 0)
    {
        $this->sNamespace = $sNamespace;
        $this->sClassName = $sClassName;
        $this->sFriendlyName = $sFriendlyName;
        $this->sDescription = $sDescription;
        $this->sSetupPage = $sSetupPage;
        $this->iVersion = $iVersion;
        $this->sFilename = $sFilename;
        if ($sPluginFolder == '') {
            $this->sPluginFolder = $sNamespace;
        } else {
            $this->sPluginFolder = $sPluginFolder;
        }
    }
}
