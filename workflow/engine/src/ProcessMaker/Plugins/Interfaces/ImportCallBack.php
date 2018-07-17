<?php

namespace ProcessMaker\Plugins\Interfaces;

use ProcessMaker\Plugins\Traits\Attributes;

class ImportCallBack
{
    use Attributes;
    private $Namespace;
    private $CallBackFile;

    /**
     * This function is the constructor of the ImportCallBack class
     * @param string $Namespace
     * @param string $CallBackFile
     */
    public function __construct($Namespace, $CallBackFile)
    {
        $this->Namespace = $Namespace;
        $this->CallBackFile = $CallBackFile;
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
     * Get callback file
     * @return string
     */
    public function getCallBackFile()
    {
        return $this->CallBackFile;
    }

    /**
     * Set callback file
     * @param string $CallBackFile
     */
    public function setCallBackFile($CallBackFile)
    {
        $this->CallBackFile = $CallBackFile;
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
     * Check if callback file is equal to params
     * @param string $CallBackFile
     * @return bool
     */
    public function equalCallBackFileTo($CallBackFile)
    {
        return $CallBackFile == $this->CallBackFile;
    }
}
