<?php

class Haanga_Extension_Tag_Inline
{
    public static function generator($cmp, $args, $redirected)
    {
        if (count($args) != 1) {
            $cmp->Error("inline needs one argument");
        }

        if ($redirected) {
            $cmp->Error("inline can't be redirected to one variable");
        }

        if (!Haanga_AST::is_str($args[0])) {
            $cmp->Error("The argument to inline must be an string");
        }
        $file = $args[0]['string'];

        if (class_exists('Haanga')) {
            $file = Haanga::GetTemplateDir().'/'.$file;
        }

        if (!is_file($file)) {
            $cmp->Error("{$file} is not a template");
        }

        return $cmp->getOpCodes(file_get_contents($file), $file);
    }
}
