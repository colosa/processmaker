<?php
/*
  +---------------------------------------------------------------------------------+
  | Copyright (c) 2010 César Rodas and Menéame Comunicacions S.L.                   |
  +---------------------------------------------------------------------------------+
  | Redistribution and use in source and binary forms, with or without              |
  | modification, are permitted provided that the following conditions are met:     |
  | 1. Redistributions of source code must retain the above copyright               |
  |    notice, this list of conditions and the following disclaimer.                |
  |                                                                                 |
  | 2. Redistributions in binary form must reproduce the above copyright            |
  |    notice, this list of conditions and the following disclaimer in the          |
  |    documentation and/or other materials provided with the distribution.         |
  |                                                                                 |
  | 3. All advertising materials mentioning features or use of this software        |
  |    must display the following acknowledgement:                                  |
  |    This product includes software developed by César D. Rodas.                  |
  |                                                                                 |
  | 4. Neither the name of the César D. Rodas nor the                               |
  |    names of its contributors may be used to endorse or promote products         |
  |    derived from this software without specific prior written permission.        |
  |                                                                                 |
  | THIS SOFTWARE IS PROVIDED BY CÉSAR D. RODAS ''AS IS'' AND ANY                   |
  | EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED       |
  | WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE          |
  | DISCLAIMED. IN NO EVENT SHALL CÉSAR D. RODAS BE LIABLE FOR ANY                  |
  | DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES      |
  | (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;    |
  | LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND     |
  | ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT      |
  | (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS   |
  | SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE                     |
  +---------------------------------------------------------------------------------+
  | Authors: César Rodas <crodas@php.net>                                           |
  +---------------------------------------------------------------------------------+
*/

/**
 *  Runtime compiler
 *
 */
final class Haanga_Compiler_Runtime extends Haanga_Compiler
{

    // get_function_name($name=NULL) {{{
    /**
     *
     *
     */
    function get_function_name($name)
    {
        return "haanga_".sha1($name);
    }
    // }}}

    // set_template_name($path) {{{
    function set_template_name($path)
    {
        return $path;
    }
    // }}}

    // Override {% include %} {{{
    protected function generate_op_include($details, &$body)
    {
        $this->do_print($body,
            hexec('Haanga::Load', $details[0], $this->getScopeVariable(),
            TRUE,
            hvar('blocks'))
        );
    }
    // }}}

    // {% base "" %} {{{
    function expr_call_base_template()
    {
        return hexec('Haanga::Load', $this->subtemplate, 
            $this->getScopeVariable(), TRUE, hvar('blocks'));
    }
    // }}}

    // get_base_template($base) {{{
    function get_base_template($base)
    {
        $this->subtemplate = $base;
    }
    // }}}

    // Override get_Custom_tag {{{
    /**
     *  
     *
     */
    function get_custom_tag($name)
    {
        static $tag = NULL;
        if (!$tag) $tag = Haanga_Extension::getInstance('Tag');
        $loaded = &$this->tags;

        if (!isset($loaded[$name])) {
            $this->prepend_op->comment("Load tag {$name} definition");
            $this->prepend_op->do_exec('require_once', $tag->getFilePath($name, FALSE));
            $loaded[$name] = TRUE;
        }

        return $tag->getClassName($name)."::main";
    }
    // }}}

    // Override get_custom_filter {{{
    function get_custom_filter($name)
    {
        static $filter = NULL;
        if (!$filter) $filter=Haanga_Extension::getInstance('Filter');
        $loaded = &$this->filters;

        if (!isset($loaded[$name])) {
            $this->prepend_op->comment("Load filter {$name} definition");
            $this->prepend_op->do_exec('require_once', $filter->getFilePath($name, FALSE));
            $loaded[$name] = TRUE;
        }

        return $filter->getClassName($name)."::main";
    }
    // }}}
    
}

/*
 * Local variables:
 * tab-width: 4
 * c-basic-offset: 4
 * End:
 * vim600: sw=4 ts=4 fdm=marker
 * vim<600: sw=4 ts=4
 */
