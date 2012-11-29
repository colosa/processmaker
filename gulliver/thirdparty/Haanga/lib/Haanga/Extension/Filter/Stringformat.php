<?php

class Haanga_Extension_Filter_StringFormat
{
    static function generator($compiler, $args)
    {
        return hexec('sprintf', $args[1], $args[0]);
    }
}
