<?php

class Haanga_Extension_Tag_FirstOf
{
    /**
     *  firstof tag
     *
     */
    static function generator($cmp, $args)
    {
        $count = count($args);
        $args  = array_reverse($args);
        for ($i=0; $i < $count; $i++) {
            if (isset($expr) && Haanga_AST::is_var($args[$i])) {
                $expr = hexpr_cond(hexpr(hexec('empty', $args[$i]),'==', FALSE), $args[$i], $expr);
            } else {
                $expr = $args[$i];
            }
        }
        return $expr;
    }
}
