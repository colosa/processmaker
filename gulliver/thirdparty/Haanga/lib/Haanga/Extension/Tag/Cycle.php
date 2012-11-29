<?php

class Haanga_Extension_Tag_Cycle
{
    public $is_block = FALSE;

    static function generator($cmp, $args, $declared)
    {
        static $cycle = 0;
        if (!isset($cmp->cycle)) {
            $cmp->cycle = array();
        }

        $code = hcode();

        $index = 'index_'.$cycle;
        $def   = 'def_cycle_'.$cycle; 

        if (count($args) == 1 && Haanga_AST::is_var($args[0]) && isset($cmp->cycle[$args[0]['var']])) {
            $id    = $cmp->cycle[$args[0]['var']];
            $index = 'index_'.$id;
            $def   = 'def_cycle_'.$id; 
        } else {
            if (!$declared) {
                $code->do_if(hexpr(hexec('isset', hvar($def)), '==', FALSE));
            }
            $code->decl($def, $args);
            if (!$declared) {
                $code->do_endif();
            }
        }

        /* isset($var) == FALSE */
        $expr = hexpr(hexec('isset', hvar($index)), '==', FALSE);
        $inc  = hexpr(hexpr(hexpr(hvar($index), '+', 1)), '%', hexec('count', hvar($def)));
        

        if (!$declared) {
            if (isset($id)) {
                $code->decl($index, $inc);
            } else {
                $code->decl($index, hexpr_cond($expr, 0, $inc));
            }
            $code->end();
            $var = hvar($def, hvar($index));
            $cmp->do_print($code, $var);
        } else {
            $code->decl($index, -1);
            $cmp->cycle[$declared] = $cycle;
        }

        $cycle++;

        return $code;

    }
}
