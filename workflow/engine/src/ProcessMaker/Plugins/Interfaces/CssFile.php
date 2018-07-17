<?php

namespace ProcessMaker\Plugins\Interfaces;

use ProcessMaker\Plugins\Traits\Attributes;

/**
 * Class CssFile
 * @package ProcessMaker\Plugins\Interfaces
 */
class CssFile
{
    use Attributes;

    private $Namespace;
    private $CssFile;

    /**
     * This function is the constructor of the CssFile class
     * @param string $Namespace
     * @param string $CssFile
     */
    public function __construct($Namespace, $CssFile)
    {
        $this->Namespace = $Namespace;
        $this->CssFile = $CssFile;
    }

    /**
     * Get css file
     * @return string
     */
    public function getCssFile()
    {
        return $this->CssFile;
    }

    /**
     * Set css file
     * @param string $CssFile
     */
    public function setCssFile($CssFile)
    {
        $this->CssFile = $CssFile;
    }

    /**
     * Check if css file is equal to params
     * @param string $CssFile
     * @return bool
     */
    public function equalCssFileTo($CssFile)
    {
        return $CssFile == $this->CssFile;
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
