<?php

namespace ProcessMaker\Plugins\Traits;

/**
 * Trait Attributes
 * @package ProcessMaker\Plugins\Traits
 */
trait Attributes
{
    /**
     * Return all properties of instance
     * @return array
     */
    public function getAttributes()
    {
        return get_object_vars($this);
    }
}
