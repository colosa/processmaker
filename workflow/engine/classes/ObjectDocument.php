<?php

/**
 * Object Document class
 *
 * @package workflow.engine.ProcessMaker
 */class ObjectDocument
{
    public $type;
    public $name;
    public $data;
    public $origin;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->type = '';
        $this->name = '';
        $this->data = '';
        $this->origin = '';
    }
}
