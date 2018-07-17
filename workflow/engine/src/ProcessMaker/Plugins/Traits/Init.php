<?php

namespace ProcessMaker\Plugins\Traits;

/**
 * Trait Init
 * @package ProcessMaker\Plugins\Traits
 */
trait Init
{
    /**
     * Initialization of functions and others of a plugin
     */
    public function init()
    {
        $this->initFunction();
    }

    /**
     * Initialization of plugin pmFunctions
     */
    private function initFunction()
    {
        $pmFunctions = $this->getPmFunctions();
        foreach ($pmFunctions as $namespace) {
            $filePmFunctions = PATH_PLUGINS . $namespace . PATH_SEP . 'classes' . PATH_SEP . 'class.pmFunctions.php';
            if (file_exists($filePmFunctions) && $this->isEnable($namespace)) {
                include_once($filePmFunctions);
            }
        }
    }

}
