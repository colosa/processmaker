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


class Haanga_Extension_Tag extends Haanga_Extension
{
    /**
     *  isValid
     *
     *  Check if the current $tag (string) is registered as a custom
     *  tag, if so, it check wether it is just a custom tag or a custom block.
     *
     *  This method is called from the lexer for each alpha (within {% %}), 
     *  to avoid parsing conflicts.
     *
     *  @param string $tag  Tag to check
     *
     *  @return int|bool HG_Parser::T_CUSTOM_TAG, HG_Parser::T_CUSTOM_TAG or FALSE
     */
    final function isValid($tag)
    {
        static $cache = array();
        $tag = strtolower($tag);

        if (!isset($cache[$tag])) {
            $class_name = $this->getClassName($tag);
            if (class_exists($class_name)) {
                $properties = get_class_vars($class_name);
                $is_block   = FALSE;
                if (isset($properties['is_block'])) {
                    $is_block = (bool)$properties['is_block'];
                }
                $cache[$tag] = $is_block ? HG_Parser::T_CUSTOM_BLOCK : HG_Parser::T_CUSTOM_TAG;
            }
            if (!isset($cache[$tag])) {
                $cache[$tag] = FALSE;
            }
        }

        return $cache[$tag];
    }

    final function getClassName($tag)
    {
        $tag = str_replace("_", "", ucfirst($tag));
        return "Haanga_Extension_Tag_{$tag}";
    }


}

/*
 * Local variables:
 * tab-width: 4
 * c-basic-offset: 4
 * End:
 * vim600: sw=4 ts=4 fdm=marker
 * vim<600: sw=4 ts=4
 */
