<?php

class Haanga_Extension_Filter_Slugify
{
    static function generator($compiler, $args)
    {
        if (count($args) != 1) {
            $compiler->Error("slugify filter only needs one parameter");
        }
      
        $arg = hexec('strtolower', $args[0]);
        $arg = hexec('str_replace'," ","-",$arg);
        $arg = hexec('preg_replace',"/[^\d\w-_]/",'',$arg);
        return $arg;
    }
}
