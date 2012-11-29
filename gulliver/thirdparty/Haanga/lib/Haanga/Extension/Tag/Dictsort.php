<?php

class Haanga_Extension_Tag_Dictsort
{

    /**
     *  Sorted a nested array by '$sort_by'
     *  property on each sub-array. , if you want 
     *  to see the original php file look filters/dictsort.php
     */
    static function generator($cmp, $args, $redirected)
    {
        if (!$redirected) {
            $cmp->Error("dictsort must be redirected to a variable using AS <varname>");
        }
        if (count($args) != 2) {
            $cmp->Error("Dictsort must have two params");
        }

        if (!Haanga_AST::is_var($args[0])) {
            $cmp->Error("Dictsort: First parameter must be an array");
        }
        
        $var = $cmp->get_context($args[0]['var']);
        $cmp->set_context($redirected, $var);

        $redirected = hvar($redirected);
        $field      = hvar('field');
        $key        = hvar('key');

        $code = hcode();
        $body = hcode();

        $body->decl(hvar('field', $key), hvar('item', $args[1]));

        $code->decl($redirected, $args[0]);
        $code->decl($field, array());
        $code->do_foreach($redirected, 'item', $key, $body);
        $code->do_exec('array_multisort', $field, hconst('SORT_REGULAR'), $redirected);

        return $code;
    }
}
