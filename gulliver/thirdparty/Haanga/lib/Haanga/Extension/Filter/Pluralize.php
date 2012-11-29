<?php

class Haanga_Extension_Filter_Pluralize
{
    static function generator($compiler, $args)
    {
        if (count($args) > 1) {
            if (!Haanga_AST::is_str($args[1])) {
                $compiler->Error("pluralize: First parameter must be an string");
            }
            $parts    = explode(",", $args[1]['string']);
            $singular = "";
            if (count($parts) == 1) {
                $plural = $parts[0];
            } else {
                $singular = $parts[0];
                $plural   = $parts[1];
            }
        } else {
            $singular = "";
            $plural   = "s";
        }

        return hexpr_cond(hexpr($args[0], '<=', 1), $singular, $plural);
    }
}
