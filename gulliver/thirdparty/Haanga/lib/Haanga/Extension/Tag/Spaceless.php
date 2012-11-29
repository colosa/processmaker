<?php

/**
 *  Spaceless custom tag
 *
 *  @author crodas
 */
class Haanga_Extension_Tag_Spaceless
{
    /* This tag is a block */
    public $is_block  = TRUE;

    /**
     *  main() {{{
     *
     *  This static function contains the definition of spaceless
     *  tag, it is important not to refence to $compiler since it
     *  will copied and paste in the generated PHP code from the 
     *  template as a static function.
     *
     *  It is also important to put the start and the end of the 
     *  static function in new lines.
     *
     *
    static static function main($html)
    {
        $regex = array(
            '/>[ \t\r\n]+</sU',
            '/^[ \t\r\n]+</sU',
            '/>[ \t\r\n]+$/sU',
        );
        $replaces = array('><', '<', '>');
        $html     = preg_replace($regex, $replaces, $html);
        return $html;
    } }}} */

    /**
     *  spaceless now uses generated code instead of 
     *  calling Spaceless_Tag::main() at everytime.
     *
     */
    static function generator($compiler, $args)
    {
        $regex = array('/>[ \t\r\n]+</sU','/^[ \t\r\n]+</sU','/>[ \t\r\n]+$/sU');
        $repl  = array('><', '<', '>');

        return hexec('preg_replace', $regex, $repl, $args[0]);
    }
    

}
