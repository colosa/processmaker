<?php

namespace ProcessMaker\Plugins\Interfaces;

use ProcessMaker\Plugins\Traits\Attributes;

/**
 * Class DashboardPage
 * @package ProcessMaker\Plugins\Interfaces
 */
class DashboardPage
{
    use Attributes;

    private $Namespace;
    private $Page;
    private $Name;
    private $Icon;

    /**
     * This function is the constructor of the DashboardPage class
     * @param string $Namespace
     * @param string $Page
     * @param string $Name
     * @param string $Icon
     */
    public function __construct($Namespace, $Page, $Name, $Icon)
    {
        $this->Namespace = $Namespace;
        $this->Page = $Page;
        $this->Name = $Name;
        $this->Icon = $Icon;
    }

    /**
     * Get name of plugin
     * @return string
     */
    public function getNamespace()
    {
        return $this->Namespace;
    }

    /**
     * Set name of plugin
     * @param string $Namespace
     */
    public function setNamespace($Namespace)
    {
        $this->Namespace = $Namespace;
    }

    /**
     * Get page of Dashboard
     * @return string
     */
    public function getPage()
    {
        return $this->Page;
    }

    /**
     * Set page of Dashboard
     * @param string $Page
     */
    public function setPage($Page)
    {
        $this->Page = $Page;
    }

    /**
     * Get name of Dashboard
     * @return string
     */
    public function getName()
    {
        return $this->Name;
    }

    /**
     * Set name of Dashboard
     * @param string $Name
     */
    public function setName($Name)
    {
        $this->Name = $Name;
    }

    /**
     * Get icon of Dashboard
     * @return string
     */
    public function getIcon()
    {
        return $this->Icon;
    }

    /**
     * Set icon of Dashboard
     * @param string $Icon
     */
    public function setIcon($Icon)
    {
        $this->Icon = $Icon;
    }

    /**
     * Check if name plugin is equal to params
     * @param string $Namespace
     * @return bool
     */
    public function equalNamespaceTo($Namespace)
    {
        return $Namespace == $this->Namespace;
    }

    /**
     * Check if dashboard page is equal to params
     * @param string $Page
     * @return bool
     */
    public function equalPageTo($Page)
    {
        return $Page == $this->Page;
    }
}
