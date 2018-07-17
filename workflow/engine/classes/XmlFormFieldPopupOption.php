<?php

/**
 * XmlFormFieldpopupOption - XmlFormFieldPopupOption class
 *
 * @package workflow.engine.ProcessMaker
 */
class XmlFormFieldPopupOption extends XmlFormField
{
    public $launch = '';

    /**
     * Get Events
     *
     * @return string
     */
    public function getEvents()
    {
        $script = '{name:"' . $this->name . '",text:"' . addcslashes($this->label, '\\"') . '", launch:leimnud.closure({Function:function(target){' . $this->launch . '}, args:target})}';
        return $script;
    }
}
