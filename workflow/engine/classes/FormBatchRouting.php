<?php

class FormBatchRouting extends Form
{
    public function getVars($bWhitSystemVars = true)
    {
        $aFields = array ();
        if ($bWhitSystemVars) {
            $aAux = G::getSystemConstants();
            foreach ($aAux as $sName => $sValue) {
                $aFields[] = array ("sName" => $sName, "sType" => "system");
            }
        }
        foreach ($this->fields as $k => $v) {
            if (($v->type != "title") && ($v->type != "subtitle") && ($v->type != "file") && ($v->type != "button") && ($v->type != "reset") && ($v->type != "submit") && ($v->type != "listbox") && ($v->type != "checkgroup") && ($v->type != "grid") && ($v->type != "javascript")) {
                $aFields[] = array ('sName' => trim( $k ),'sType' => trim( $v->type ));
            }
        }
        return $aFields;
    }
}
