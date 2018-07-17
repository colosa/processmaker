<?php

/**
 * ObjectDocument Collection
 *
 * @package workflow.engine.ProcessMaker
 */
class ObjectCollection
{
    public $num;
    public $swapc;
    public $objects;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->objects = array();
        $this->num = 0;
        $this->swapc = $this->num;
        array_push($this->objects, 'void');
    }

    /**
     * add in the collecetion a new object Document
     *
     * @param $name name object document
     * @param $type type object document
     * @param $data data object document
     * @param $origin origin object document
     * @return void
     */
    public function add($name, $type, $data, $origin)
    {
        $o = new ObjectDocument();
        $o->name = $name;
        $o->type = $type;
        $o->data = $data;
        $o->origin = $origin;

        $this->num++;
        array_push($this->objects, $o);
        $this->swapc = $this->num;
    }

    /**
     * get the collection of ObjectDocument
     *
     * @param $name name object document
     * @param $type type object document
     * @param $data data object document
     * @param $origin origin object document
     * @return void
     */
    public function get()
    {
        if ($this->swapc > 0) {
            $e = $this->objects[$this->swapc];
            $this->swapc--;
            return $e;
        } else {
            $this->swapc = $this->num;
            return false;
        }
    }
}
