<?php

class Haanga_Extension_Filter_Reverse
{
    static function generator($compiler, $args)
    {
        if (count($args) != 1) {
            $compiler->Error("Reverse only needs one parameter");
        }

        return hexec('array_reverse', $args[0], TRUE);
    }
}
