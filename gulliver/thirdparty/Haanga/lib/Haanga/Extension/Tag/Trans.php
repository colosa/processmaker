<?php

class Haanga_Extension_Tag_Trans
{
    public $is_block = FALSE;

    static function generator($cmp, $args, $redirect)
    {
        $code = hcode();

        $exec = hexec('_', $args[0]);

        if (count($args) > 1) {
            $exec = hexec('sprintf', $exec);
            foreach ($args as $id => $arg) {
                if ($id !== 0) {
                    $exec->param($arg);
                }
            }
        }


        if ($redirect) {
            $code->decl($redirect, $exec);
        } else {
            $cmp->do_print($code, $exec);
        }

        return $code;
    }

}
