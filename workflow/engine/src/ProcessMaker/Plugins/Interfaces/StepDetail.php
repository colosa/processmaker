<?php

namespace ProcessMaker\Plugins\Interfaces;

use ProcessMaker\Plugins\Traits\Attributes;

/**
 * Class StepDetail
 * @package ProcessMaker\Plugins\Interfaces
 */
class StepDetail
{
    use Attributes;

    private $Namespace;
    private $StepId;
    private $StepName;
    private $StepTitle;
    private $SetupStepPage;

    /**
     * This function is the constructor of the StepDetail class
     * @param string $Namespace
     * @param string $StepId
     * @param string $StepName
     * @param string $StepTitle
     * @param string $SetupStepPage
     */
    public function __construct($Namespace, $StepId, $StepName, $StepTitle, $SetupStepPage)
    {
        $this->Namespace = $Namespace;
        $this->StepId = $StepId;
        $this->StepName = $StepName;
        $this->StepTitle = $StepTitle;
        $this->SetupStepPage = $SetupStepPage;
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
     * Get step id
     * @return string
     */
    public function getStepId()
    {
        return $this->StepId;
    }

    /**
     * Get step Title
     * @return string
     */
    public function getStepTitle()
    {
        return $this->StepTitle;
    }

    /**
     * Get step name
     * @return string
     */
    public function getStepName()
    {
        return $this->StepName;
    }

    /**
     * Get setup step page
     * @return string
     */
    public function getSetupStepPage()
    {
        return $this->SetupStepPage;
    }

    /**
     * Check if step id is equal to params
     * @param string $StepId
     * @return bool
     */
    public function equalStepIdTo($StepId)
    {
        return $StepId == $this->StepId;
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
}
