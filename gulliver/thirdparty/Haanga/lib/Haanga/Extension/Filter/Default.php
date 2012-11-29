<?php

class Haanga_Extension_Filter_Default
{
    static function generator($compiler, $args)
    {
        return hexpr_cond(hexpr(hexec('empty', $args[0]), '==', TRUE), $args[1], $args[0]);
    }
}
