<?php

class Haanga_Extension_Filter_Truncatechars
{
    static function main($text, $limit)
    {
        if(strlen($text) <= $limit)
                return $text;
        $trunctext = substr($text, 0, $limit);
        $trunctext[$limit-3] = '.';
        $trunctext[$limit-2] = '.';
        $trunctext[$limit-1] = '.';
        return $trunctext;
    }
}

