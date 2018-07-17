<?php

namespace ProcessMaker\Plugins\Interfaces;

use ProcessMaker\Plugins\Traits\Attributes;

/**
 * Class PluginDetail
 * @package ProcessMaker\Plugins\Interfaces
 */
class PluginDetail
{
    use Attributes;

    /** @var string */
    public $sNamespace;
    /** @var string */
    public $sDescription = '';
    /** @var string */
    public $sClassName;
    /** @var string */
    public $sFriendlyName = '';
    /** @var string */
    public $sFilename;
    /** @var string */
    public $sPluginFolder = '';
    /** @var string */
    public $sSetupPage = '';
    /** @var string */
    public $sCompanyLogo = '';
    /** @var array */
    public $aWorkspaces = [];
    /** @var bool */
    public $enabled = false;
    /** @var bool */
    public $bPrivate = false;
    /** @var int */
    public $iVersion = 0;

    /**
     * This function is the constructor of the pluginDetail class
     * @param string $sNamespace
     * @param string $sClassName
     * @param string $sFilename
     * @param string $sFriendlyName
     * @param string $sPluginFolder
     * @param string $sDescription
     * @param string $sSetupPage
     * @param string $sCompanyLogo
     * @param array $aWorkspaces
     * @param bool $enable
     * @param bool $bPrivate
     * @param integer $iVersion
     */
    public function __construct(
        $sNamespace,
        $sClassName,
        $sFilename,
        $sFriendlyName = '',
        $sPluginFolder = '',
        $sDescription = '',
        $sSetupPage = '',
        $iVersion = 0,
        $sCompanyLogo = '',
        $aWorkspaces = [],
        $enable = false,
        $bPrivate = false
    ) {
        $this->sNamespace = $sNamespace;
        $this->sDescription = $sDescription;
        $this->sClassName = $sClassName;
        $this->sFriendlyName = $sFriendlyName;
        $this->sFilename = $sFilename;
        $this->sPluginFolder = $sNamespace;
        if ($sPluginFolder) {
            $this->sPluginFolder = $sPluginFolder;
        }
        $this->sSetupPage = $sSetupPage;
        $this->sCompanyLogo = $sCompanyLogo;
        $this->aWorkspaces = $aWorkspaces;
        $this->enabled = $enable;
        $this->bPrivate = $bPrivate;
        $this->iVersion = $iVersion;
    }

    /**
     * Get name of plugin
     * @return string
     */
    public function getNamespace()
    {
        return $this->sNamespace;
    }

    /**
     * Set name of plugin
     * @param string $PluginNamespace
     */
    public function setNamespace($PluginNamespace)
    {
        $this->sNamespace = $PluginNamespace;
    }

    /**
     * Get description
     * @return string
     */
    public function getDescription()
    {
        return $this->sDescription;
    }

    /**
     * Set description
     * @param string $PluginDescription
     */
    public function setDescription($PluginDescription)
    {
        $this->sDescription = $PluginDescription;
    }

    /**
     * Get class name
     * @return string
     */
    public function getClassName()
    {
        return $this->sClassName;
    }

    /**
     * Set class name
     * @param string $PluginClassName
     */
    public function setClassName($PluginClassName)
    {
        $this->sClassName = $PluginClassName;
    }

    /**
     * Get friendly name
     * @return string
     */
    public function getFriendlyName()
    {
        return $this->sFriendlyName;
    }

    /**
     * Set friendly name
     * @param string $PluginFriendlyName
     */
    public function setFriendlyName($PluginFriendlyName)
    {
        $this->sFriendlyName = $PluginFriendlyName;
    }

    /**
     * Get path file
     * @return string
     */
    public function getFile()
    {
        return $this->sFilename;
    }

    /**
     * Set path file
     * @param string $PluginFile
     */
    public function setFile($PluginFile)
    {
        $this->sFilename = $PluginFile;
    }

    /**
     * Get name folder
     * @return string
     */
    public function getFolder()
    {
        return $this->sPluginFolder;
    }

    /**
     * Set name folder
     * @param string $PluginFolder
     */
    public function setFolder($PluginFolder)
    {
        $this->sPluginFolder = $PluginFolder;
    }

    /**
     * Get setup page
     * @return string
     */
    public function getSetupPage()
    {
        return $this->sSetupPage;
    }

    /**
     * Set setup page
     * @param string $PluginSetupPage
     */
    public function setSetupPage($PluginSetupPage)
    {
        $this->sSetupPage = $PluginSetupPage;
    }

    /**
     * Get company logo
     * @return string
     */
    public function getCompanyLogo()
    {
        return $this->sCompanyLogo;
    }

    /**
     * Set company logo
     * @param string $PluginCompanyLogo
     */
    public function setCompanyLogo($PluginCompanyLogo)
    {
        $this->sCompanyLogo = $PluginCompanyLogo;
    }

    /**
     * Get workspace allowed
     * @return array
     */
    public function getWorkspaces()
    {
        return $this->aWorkspaces;
    }

    /**
     * Set workspace allowed
     * @param array $PluginWorkspaces
     */
    public function setWorkspaces($PluginWorkspaces)
    {
        $this->aWorkspaces = $PluginWorkspaces;
    }

    /**
     * Get plugin is enable
     * @return bool
     */
    public function isEnabled()
    {
        return $this->enabled;
    }

    /**
     * Set status plugin
     * @param bool $PluginEnable
     */
    public function setEnabled($PluginEnable)
    {
        $this->enabled = $PluginEnable;
    }

    /**
     * Get if plugin is private
     * @return bool
     */
    public function isPrivate()
    {
        return $this->bPrivate;
    }

    /**
     * Set status private
     * @param bool $PluginPrivate
     */
    public function setPrivate($PluginPrivate)
    {
        $this->bPrivate = $PluginPrivate;
    }

    /**
     * Get version of plugin
     * @return int
     */
    public function getVersion()
    {
        return $this->iVersion;
    }

    /**
     * Set version of plugin
     * @param int $PluginVersion
     */
    public function setVersion($PluginVersion)
    {
        $this->iVersion = $PluginVersion;
    }
}
