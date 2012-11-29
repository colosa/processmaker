<?php

class Haanga_Extension_Filter_UrlEncode
{

    public static function generator($cmp, $args)
    {
        $cmp->var_is_safe = TRUE;
        return hexec('urlencode', $args[0]);
    }
}
