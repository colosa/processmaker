<?php

class Haanga_Extension_Tag_Templatetag
{
    static function generator($compiler, $args)
    {
        if (count($args) != 1) {
            $compiler->Error("templatetag only needs one parameter");
        }

        if (Haanga_AST::is_var($args[0])) {
            $type = $args[0]['var'];
            if (!is_string($type)) {
                $compiler->Error("Invalid parameter");
            }
        } else if (Haanga_AST::is_str($args[0])) {
            $type = $args[0]['string'];
        }

        switch ($type)
        {
        case 'openblock':
            $str = '{%';
            break;
        case 'closeblock':
            $str = '%}';
            break;
        case 'openbrace':
            $str = '{';
            break;
        case 'closebrace':
            $str = '}';
            break;
        case 'openvariable':
            $str = '{{';
            break;
        case 'closevariable':
            $str = '}}';
            break;
        case 'opencomment':
            $str = '{#';
            break;
        case 'closecomment':
            $str = '#}';
            break;
        default:
            $compiler->Error("Invalid parameter");
            break;
        }

        $code = hcode();
        $compiler->do_print($code, Haanga_AST::str($str));

        return $code;
    }
}
