<?php

Class Haanga_Extension_Filter_Cut
{

    /**
     *  We implement "cut" filter at compilation time, to 
     *  avoid senseless includes for simple things.
     *
     *  We can also define an "php_alias" that will simple
     *  call this function (therefore it must exists at
     *  rendering time).
     *
     *  Also a Main() static method could be declared, this will
     *  included at runtime  or copied as a function if the CLI is used (more
     *  or less django style).
     *  
     */
    static function generator($compiler, $args)
    {
        return hexec('str_replace', $args[1], "", $args[0]);
    }
}
