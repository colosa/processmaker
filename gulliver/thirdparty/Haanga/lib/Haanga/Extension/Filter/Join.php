<?php

class Haanga_Extension_Filter_Join
{
    public static function generator($compiler, $args)
    {
        if (count($args) == 1) {
            $args[1] = "";
        }
        return hexec("implode", $args[1], $args[0]);
    }
}
