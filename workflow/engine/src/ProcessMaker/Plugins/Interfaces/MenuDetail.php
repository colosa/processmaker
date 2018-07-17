<?php

namespace ProcessMaker\Plugins\Interfaces;

use ProcessMaker\Plugins\Traits\Attributes;

/**
 * Class MenuDetail
 * @package ProcessMaker\Plugins\Interfaces
 */
class MenuDetail
{
    use Attributes;
    private $Namespace;
    private $MenuId;
    private $Filename;

    /**
     * This function is the constructor of the MenuDetail class
     * @param string $Namespace
     * @param string $MenuId
     * @param string $Filename
     */
    public function __construct($Namespace, $MenuId, $Filename)
    {
        $this->Namespace = $Namespace;
        $this->MenuId = $MenuId;
        $this->Filename = $Filename;
    }

    /**
     * Check if menu id is equal to params
     * @param string $menuId
     * @return bool
     */
    public function equalMenuIdTo($menuId)
    {
        return $menuId == $this->MenuId;
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
     * Check if file exists
     * @return bool
     */
    public function exitsFile()
    {
        return file_exists($this->Filename);
    }

    /**
     * Include file
     * @return bool
     */
    public function includeFileMenu()
    {
        include($this->Filename);
    }
}
