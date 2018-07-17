<?php

namespace ProcessMaker\Plugins\Interfaces;

use ProcessMaker\Plugins\Traits\Attributes;

/**
 * Class FolderDetail
 * @package ProcessMaker\Plugins\Interfaces
 */
class FolderDetail
{
    use Attributes;
    private $Namespace;
    private $FolderId;
    private $FolderName;

    /**
     * This function is the constructor of the FolderDetail class
     * @param string $Namespace
     * @param string $FolderId
     * @param string $FolderName
     */
    public function __construct($Namespace, $FolderId, $FolderName)
    {
        $this->Namespace = $Namespace;
        $this->FolderId = $FolderId;
        $this->FolderName = $FolderName;
    }

    /**
     * Get folder name
     * @return string
     */
    public function getFolderName()
    {
        return $this->FolderName;
    }

    /**
     * Set folder name
     * @param string $FolderName
     */
    public function setFolderName($FolderName)
    {
        $this->FolderName = $FolderName;
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
     * Check if folder id is equal to params
     * @param string $folderId
     * @return bool
     */
    public function equalFolderIdTo($folderId)
    {
        return $folderId == $this->FolderId;
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
}
