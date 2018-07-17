<?php

namespace ProcessMaker\Plugins\Interfaces;

use ProcessMaker\Plugins\Traits\Attributes;

/**
 * Class CaseSchedulerPlugin
 * @package ProcessMaker\Plugins\Interfaces
 */
class CaseSchedulerPlugin
{
    use Attributes;

    private $Namespace;
    private $ActionId;
    private $ActionForm;
    private $ActionSave;
    private $ActionExecute;
    private $ActionGetFields;

    /**
     * This function is the constructor of the CaseSchedulerPlugin class
     * @param string $Namespace
     * @param string $ActionId
     * @param string $ActionForm
     * @param string $ActionSave
     * @param string $ActionExecute
     * @param string $ActionGetFields
     */
    public function __construct($Namespace, $ActionId, $ActionForm, $ActionSave, $ActionExecute, $ActionGetFields)
    {
        $this->Namespace = $Namespace;
        $this->ActionId = $ActionId;
        $this->ActionForm = $ActionForm;
        $this->ActionSave = $ActionSave;
        $this->ActionExecute = $ActionExecute;
        $this->ActionGetFields = $ActionGetFields;
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
     * Get action Id
     * @return string
     */
    public function getActionId()
    {
        return $this->ActionId;
    }

    /**
     * Get action form
     * @return string
     */
    public function getActionForm()
    {
        return $this->ActionForm;
    }

    /**
     * Get action Save
     * @return string
     */
    public function getActionSave()
    {
        return $this->ActionSave;
    }

    /**
     * Get action execute
     * @return string
     */
    public function getActionExecute()
    {
        return $this->ActionExecute;
    }

    /**
     * Get action fields
     * @return string
     */
    public function getActionGetFields()
    {
        return $this->ActionGetFields;
    }

    /**
     * Check if CaseSchedulerPlugin name of plugin is equal to params
     * @param string $Namespace
     * @return bool
     */
    public function equalNamespaceTo($Namespace)
    {
        return $Namespace == $this->Namespace;
    }

    /**
     * Check if CaseSchedulerPlugin Action Id is equal to params
     * @param string $ActionId
     * @return bool
     */
    public function equalActionIdTo($ActionId)
    {
        return $ActionId == $this->ActionId;
    }
}
