<?php

class Haanga_Extension_Filter_Truncatewords
{
    static function main($text, $limit)
    {
        $words = explode(" ", $text, $limit+1);
        if (count($words) == $limit+1) {
            $words[$limit] = '...';
        }
        return implode(" ", $words);
    }
}
