<?php

namespace ProcessMaker\Plugins\Interfaces;

use ProcessMaker\Plugins\Traits\Attributes;

/**
 * Class CronFile
 * @package ProcessMaker\Plugins\Interfaces
 */
class CronFile
{
    use Attributes;
    private $Namespace;
    private $CronFile;

    /**
     * This function is the constructor of the CronFile class
     * @param string $Namespace
     * @param string $CronFile
     */
    public function __construct($Namespace, $CronFile)
    {
        $this->Namespace = $Namespace;
        $this->CronFile = $CronFile;
    }

    /**
     * Set value to cron file
     * @param string $CronFile
     */
    public function setCronFile($CronFile)
    {
        $this->CronFile = $CronFile;
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
     * Check if cron file is equal to params
     * @param string $CronFile
     * @return bool
     */
    public function equalCronFileTo($CronFile)
    {
        return $CronFile == $this->CronFile;
    }
}
