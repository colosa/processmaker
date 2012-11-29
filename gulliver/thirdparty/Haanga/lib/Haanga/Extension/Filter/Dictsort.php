<?php

class Haanga_Extension_Filter_Dictsort
{
    /**
     *  Sorted a nested array by '$sort_by'
     *  property on each sub-array. This 
     *  filter is included at rendering time, if you want 
     *  to see the generated version see tags/dictsort.php
     */
    static function main($array, $sort_by)
    {
        $field = array();
        foreach ($array as $key => $item) {
            $field[$key] = $item[$sort_by];
        }
        array_multisort($field, SORT_REGULAR, $array);
        return $array;

    }
}
