<?php

namespace ProcessMaker\Plugins\Interfaces;

use ProcessMaker\Plugins\Traits\Attributes;

/**
 * Class RedirectDetail
 * @package ProcessMaker\Plugins\Interfaces
 */
class RedirectDetail
{
    use Attributes;

    private $Namespace;
    private $RoleCode;
    private $PathMethod;

    /**
     * This function is the constructor of the RedirectDetail class
     * @param string $Namespace
     * @param string $RoleCode
     * @param string $PathMethod
     */
    public function __construct($Namespace, $RoleCode, $PathMethod)
    {
        $this->Namespace = $Namespace;
        $this->RoleCode = $RoleCode;
        $this->PathMethod = $PathMethod;
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
     * Get role code
     * @return string
     */
    public function getRoleCode()
    {
        return $this->RoleCode;
    }

    /**
     * Get path method
     * @return string
     */
    public function getPathMethod()
    {
        return $this->PathMethod;
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
     * Check if role code is equal to params
     * @param string $RoleCode
     * @return bool
     */
    public function equalRoleCodeTo($RoleCode)
    {
        return $RoleCode == $this->RoleCode;
    }
}
