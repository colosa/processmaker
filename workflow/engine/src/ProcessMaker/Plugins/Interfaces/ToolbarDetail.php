<?php

namespace ProcessMaker\Plugins\Interfaces;

use ProcessMaker\Plugins\Traits\Attributes;

/**
 * Class ToolbarDetail
 * @package ProcessMaker\Plugins\Interfaces
 */
class ToolbarDetail
{
    use Attributes;
    private $Namespace;
    private $ToolbarId;
    private $Filename;

    /**
     * This function is the constructor of the ToolbarDetail class
     * @param string $Namespace Name of Plugin
     * @param string $ToolbarId (NORMAL, GRID)
     * @param string $Filename
     */
    public function __construct($Namespace, $ToolbarId, $Filename)
    {
        $this->Namespace = $Namespace;
        $this->ToolbarId = $ToolbarId;
        $this->Filename = $Filename;
    }

    /**
     * Check if name of plugin is equal to params
     * @param string $Namespace
     * @return bool
     */
    public function equalNamespaceTo($Namespace)
    {
        return $Namespace == $this->Namespace;
    }

    /**
     * Check if toolbar id is equal to params
     * @param string $ToolbarId
     * @return bool
     */
    public function equalToolbarIdTo($ToolbarId)
    {
        return $ToolbarId == $this->ToolbarId;
    }

    /**
     * Check if file exists to params
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
    public function includeFile()
    {
        include($this->Filename);
    }
}
