<?php

namespace ProcessMaker\Plugins\Interfaces;

use ProcessMaker\Plugins\Traits\Attributes;

/**
 * Class OpenReassignCallback
 * @package ProcessMaker\Plugins\Interfaces
 */
class OpenReassignCallback
{
    use Attributes;
    private $CallBackFile;

    /**
     * This function is the constructor of the OpenReassignCallback class
     * @param string $CallBackFile
     */
    public function __construct($CallBackFile)
    {
        $this->CallBackFile = $CallBackFile;
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
     * Check if callback file is equal to params
     * @param string $CallBackFile
     * @return bool
     */
    public function equalCallBackFileTo($CallBackFile)
    {
        return $CallBackFile == $this->CallBackFile;
    }
}
