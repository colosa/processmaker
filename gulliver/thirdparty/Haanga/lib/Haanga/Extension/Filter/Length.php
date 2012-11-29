<?php

class Haanga_Extension_Filter_Length
{
    public $is_safe = TRUE; /* a number if safe */
    static function generator($compiler, $args)
    {
        $count  = hexec('count', $args[0]);
        $strlen = hexec('strlen', $args[0]);
        $guess  = hexpr_cond(hexec('is_array', $args[0]), hexec('count', $args[0]),
                            hexec('strlen', $args[0]));

        if (Haanga_AST::is_var($args[0])) {
            /* if it is a variable, best effort to detect
               its type at compile time */
            $value = $compiler->get_context($args[0]['var']);
            if (is_array($value)) {
                return $count;
            } else if  (is_string($value)) {
                return $strlen;
            } else {
                return $guess;
            }
        }

        if (Haanga_AST::is_str($args[0])) {
            return $strlen;
        }

        return $guess;
    }
}
