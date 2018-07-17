<?php

namespace ProcessMaker\Plugins\Interfaces;

use ProcessMaker\Plugins\Traits\Attributes;

/**
 * Class TaskExtendedProperty
 * @package ProcessMaker\Plugins\Interfaces
 */
class TaskExtendedProperty
{
    use Attributes;

    private $Namespace;
    private $Page;
    private $Name;
    private $Icon;

    /**
     * This function is the constructor of the TaskExtendedProperty class
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
     * Set name
     * @param string $Name
     */
    public function setName($Name)
    {
        $this->Name = $Name;
    }

    /**
     * Set icon
     * @param string $Icon
     */
    public function setIcon($Icon)
    {
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
     * Get page
     * @return string
     */
    public function getPage()
    {
        return $this->Page;
    }

    /**
     * Get name
     * @return string
     */
    public function getName()
    {
        return $this->Name;
    }

    /**
     * Get icon
     * @return string
     */
    public function getIcon()
    {
        return $this->Icon;
    }

    /**
     * Check if nmae of plugin is equal to params
     * @param string $Namespace
     * @return bool
     */
    public function equalNamespaceTo($Namespace)
    {
        return $Namespace == $this->Namespace;
    }

    /**
     * Check if page is equal to params
     * @param string $Page
     * @return bool
     */
    public function equalPageTo($Page)
    {
        return $Page == $this->Page;
    }
}
