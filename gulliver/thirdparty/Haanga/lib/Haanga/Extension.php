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

Abstract Class Haanga_Extension
{
    private static $_instances;

    final private function __construct()
    {
    }

    final static function getInstance($name)
    {
        $name = 'Haanga_Extension_'.$name;
        if (!class_exists($name)) {
            throw new Haanga_Compiler_Exception("{$name} is not a class");
        }
        if (!is_subclass_of($name, __CLASS__)) {
            throw new Haanga_Compiler_Exception("{$name} is not a sub-class of ".__CLASS__);
        }

        if (!isset(self::$_instances[$name])) {
            self::$_instances[$name] = new $name;
        }
        return self::$_instances[$name];
    }

    abstract function isValid($name);
    abstract function getClassName($name);

    final function getFilePath($name, $rel=TRUE, $pref=NULL)
    {
        try {
            $reflection = new ReflectionClass($this->getClassName($name));
            $file = $reflection->getFileName();
        } catch (Exception $e) {
            $file = "";
        }
        return $file;
    }

    final public function getFunctionAlias($name)
    {
        if (!$this->isValid($name)) {
            return NULL;
        }
        $zclass     = $this->getClassName($name);
        $properties = get_class_vars($zclass);
        if (isset($properties['php_alias'])) {
            return $properties['php_alias'];
        }
        return NULL;
    }

    final public function isSafe($name)
    {
        if (!$this->isValid($name)) {
            return NULL;
        }
        $zclass     = $this->getClassName($name);
        $properties = get_class_vars($zclass);
        return isset($properties['is_safe']) ? $properties['is_safe'] : FALSE;
    }

    // generator(string $name, Haanga_Compiler $compiler, Array $args) {{{
    /**
     *  Executer the generator method of the extension. If 
     *  the extension doesn't has any generator method, an empty
     *  will be returned.
     *
     *  @param string       $name extension name
     *  @param Haanga_Compiler  Compiler object
     *  @param array        Arrays
     *  @param mixed        Extra param
     *
     *  @return array
     */
    function generator($name, Haanga_Compiler $compiler, $args, $extra=NULL)
    {
        if (!$this->hasGenerator($name)) {
            return array();
        }
        $zclass = $this->getClassName($name);
        return call_user_func(array($zclass, 'generator'), $compiler, $args, $extra);
    }
    // }}}

    // hasGenerator(string $name) {{{
    /** 
     *  Return TRUE if the extension has a  
     *  generator method
     *
     *  @param string $name Extension name
     *
     *  @return bool
     */
    function hasGenerator($name)
    {
        if (!$this->isValid($name)) {
            return NULL;
        }
        $zclass = $this->getClassName($name);
        return is_callable(array($zclass, 'generator'));
    }
    // }}}

    // getFunctionBody(string $name, string $name) {{{
    /**
     *  Return the body function of the custom extension main method.
     *
     *  @param string $name
     *  @param string $name
     *
     *  @return string
     */
    function getFunctionBody($tag_name, $name)
    {
        if (!$this->isValid($tag_name)) {
            return NULL;
        }
        $zclass  = $this->getClassName($tag_name);
        if (!is_callable(array($zclass, 'main'))) {
            throw new Haanga_Compiler_Exception("{$name}: missing main method in {$zclass} class");
        }
        
        $reflection = new ReflectionMethod($zclass, 'main');
        $content    = file($this->getFilePath($tag_name));

        $start   = $reflection->getStartLine()-1;
        $end     = $reflection->getEndLine();
        $content = array_slice($content, $start, $end-$start); 

        $content[0] = str_replace("main", $name, $content[0]);
        

        return "if (!function_exists('{$name}')) {\n".implode("", $content)."}";
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
