<?php

namespace ProcessMaker\Plugins\Interfaces;

use ProcessMaker\Plugins\Traits\Attributes;

/**
 * Class TriggerDetail
 * @package ProcessMaker\Plugins\Interfaces
 */
class TriggerDetail
{
    use Attributes;
    private $Namespace;
    private $TriggerId;
    private $TriggerName;

    /**
     * This function is the constructor of the TriggerDetail class
     * @param string $Namespace
     * @param string $TriggerId
     * @param string $TriggerName
     */
    public function __construct($Namespace, $TriggerId, $TriggerName)
    {
        $this->Namespace = $Namespace;
        $this->TriggerId = $TriggerId;
        $this->TriggerName = $TriggerName;
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
     * Get trigger name
     * @return string
     */
    public function getTriggerName()
    {
        return $this->TriggerName;
    }

    /**
     * Check if trigger id is equal to params
     * @param string $triggerId
     * @return bool
     */
    public function equalTriggerId($triggerId)
    {
        return $triggerId == $this->TriggerId;
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
}
