<?php

namespace ProcessMaker\Plugins\Interfaces;

use Exception;
use ProcessMaker\Plugins\Traits\Attributes;

/**
 * Class JsFile
 * @package ProcessMaker\Plugins\Interfaces
 */
class JsFile
{
    use Attributes;
    private $Namespace;
    private $CoreJsFile;
    private $PluginJsFile;

    /**
     * This function is the constructor of the JsFile class
     * @param string $Namespace
     * @param string $CoreJsFile
     * @param array $PluginJsFile
     */
    public function __construct($Namespace, $CoreJsFile, $PluginJsFile)
    {
        $this->Namespace = $Namespace;
        $this->CoreJsFile = $CoreJsFile;
        $this->PluginJsFile = $PluginJsFile;
    }
    /**
     * Get js files
     * @return array
     */
    public function getPluginJsFile()
    {
        return $this->PluginJsFile;
    }

    /**
     * Check if core js file is equal to params
     * @param string $CoreJsFile
     * @return bool
     */
    public function equalCoreJsFile($CoreJsFile)
    {
        return $CoreJsFile == $this->CoreJsFile;
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
     * Push one or more elements onto the PluginJsFile
     * @param string|array $PluginJsFile
     * @throws Exception
     */
    public function pushPluginJsFile($PluginJsFile)
    {
        if (is_string($PluginJsFile)) {
            if (!in_array($PluginJsFile, $this->PluginJsFile)) {
                $this->PluginJsFile[] = $PluginJsFile;
            }
        } elseif (is_array($PluginJsFile)) {
            $this->PluginJsFile = array_unique(
                array_merge($PluginJsFile, $this->PluginJsFile)
            );
        } else {
            throw new Exception(
                'Invalid third param, $pluginJsFile should be a string or array - ' .
                gettype($PluginJsFile) .
                ' given.'
            );
        }
    }
}
