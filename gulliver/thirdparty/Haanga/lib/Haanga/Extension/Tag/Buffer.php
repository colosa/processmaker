<?php

class Haanga_Extension_Tag_Buffer
{
    public $is_block = TRUE;

    static function generator($cmp, $args, $redirected)
    {
        if (count($args) != 2) {
            $cmp->Error("buffer filter must have one parameter");
        }

        /* get new code object */
        $code = hcode();
        /* redirect buffer to $args[1] */
        $code->decl($args[1], $args[0]);
        /* telling to Haanga that we're handling the output */
        $code->doesPrint = TRUE;

        /* $args[1] is already safe (it might have HTML) */
        $cmp->set_safe($args[1]['var']);

        return $code;
    }
}
