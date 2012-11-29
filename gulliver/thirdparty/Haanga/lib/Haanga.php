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

if (!defined('HAANGA_VERSION')) {
    /* anyone can override this value to force recompilation */
    define('HAANGA_VERSION', '1.0.4');
}


/**
 *  Haanga Runtime class
 *
 *  Simple class to call templates efficiently. This class aims
 *  to reduce the compilation of a template as less a possible. Also
 *  it will not load in memory the compiler, except when there is not
 *  cache (compiled template) or it is out-dated.
 *
 */
class Haanga
{
    protected static $cache_dir;
    protected static $templates_dir='.';
    protected static $debug;
    protected static $bootstrap = NULL;
    protected static $check_ttl;
    protected static $check_get;
    protected static $check_set;
    protected static $use_autoload  = TRUE;
    protected static $hash_filename = TRUE;
    protected static $compiler = array();

    public static $has_compiled;

    private function __construct()
    {
        /* The class can't be instanced */
    }

    public static function getTemplateDir()
    {
        return self::$templates_dir; 
    }

    // configure(Array $opts) {{{
    /**
     *  Configuration to load Haanga
     *
     *  Options:
     *
     *      - (string)   cache_dir 
     *      - (string)   tempalte_dir
     *      - (callback) on_compile
     *      - (boolean)  debug
     *      - (int)      check_ttl
     *      - (callback) check_get
     *      - (callback) check_set
     *      - (boolean)  autoload
     *      - (boolean)  use_hash_filename
     *
     *  @return void
     */
    final public static function configure(Array $opts)
    {
        foreach ($opts as $option => $value) {
            switch (strtolower($option)) {
            case 'cache_dir':
        		self::$cache_dir = $value;
                break;
            case 'template_dir':
        		self::$templates_dir = $value;
                break;
            case 'bootstrap':
                if (is_callable($value)) {
                    self::$bootstrap = $value;
                }
                break;
            case 'debug':
                self::enableDebug((bool)$value);
                break;
            case 'check_ttl':
                self::$check_ttl = (int)$value;
                break;
            case 'check_get':
                if (is_callable($value)) {
                    self::$check_get = $value;
                }
                break;
            case 'check_set':
                if (is_callable($value)) {
                    self::$check_set = $value;
                }
                break;
            case 'autoload':
                self::$use_autoload = (bool)$value;
                break;
            case 'use_hash_filename':
                self::$hash_filename = (bool)$value;
                break;
            case 'compiler':
                if (is_array($value)) {
                    self::$compiler = $value;
                }
                break;
            default:
                continue;
            }
        }
    }
    // }}}

    // checkCacheDir(string $dir) {{{
    /**
     *  Check the directory where the compiled templates
     *  are stored.
     *
     *  @param string $dir 
     *
     *  @return void
     */
    public static function checkCacheDir()
    {
        $dir = self::$cache_dir;
        if (!is_dir($dir)) { 
            $old = umask(0);
            if (!mkdir($dir, 0777, TRUE)) {
                throw new Haanga_Exception("{$dir} is not a valid directory");
            }
            umask($old);
        }
        if (!is_writable($dir)) {
            throw new Haanga_Exception("{$dir} can't be written");
        }
    }
    // }}}

    // enableDebug($bool) {{{
    public static function enableDebug($bool)
    {
        self::$debug = $bool;
    }
    // }}}

    // getCompiler($checkdir=TRUE) {{{
    /**
     *  This function is a singleton for the Haanga_Compiler_Runtime class.
     *  The instance is already set up properly and resetted.
     *
     *
     *  @param bool  $checkdir TRUE
     *
     *  @return Haanga_Compiler_Runtime
     */
    protected static function getCompiler($checkdir=TRUE)
    {
        static $compiler;
        static $has_checkdir = FALSE;

        if (!$compiler) {

            /* Load needed files (to avoid autoload as much as possible) */
            $dir = dirname(__FILE__);
            require_once "{$dir}/Haanga/AST.php";
            require_once "{$dir}/Haanga/Compiler.php";
            require_once "{$dir}/Haanga/Compiler/Runtime.php";
            require_once "{$dir}/Haanga/Compiler/Parser.php";
            require_once "{$dir}/Haanga/Compiler/Tokenizer.php";
            require_once "{$dir}/Haanga/Generator/PHP.php";
            require_once "{$dir}/Haanga/Extension.php";
            require_once "{$dir}/Haanga/Extension/Filter.php";
            require_once "{$dir}/Haanga/Extension/Tag.php";

            /* load compiler (done just once) */
            if (self::$use_autoload) {
                require_once "{$dir}/Haanga/Loader.php";
            }

            $compiler = new Haanga_Compiler_Runtime;

            if (self::$bootstrap) {
                /* call bootstrap hook, just the first time */
                call_user_func(self::$bootstrap);
            }

            if (count(self::$compiler) != 0) {
                foreach (self::$compiler as $opt => $value) {
                    Haanga_Compiler::setOption($opt, $value);
                }
            }

        }

        if ($checkdir && !$has_checkdir) {
            self::checkCacheDir();
            $has_checkdir = TRUE; 
        }

        $compiler->reset();
        return $compiler;
    }
    // }}}

    // callback compile(string $tpl, $context=array()) {{{
    /**
     *  Compile one template and return a PHP function
     *
     *  @param string $tpl  Template body
     *  @param array $context  Context variables useful to generate efficient code (for array, objects and array)
     *
     *  @return callback($vars=array(), $return=TRUE, $block=array())
     */
    public static function compile($tpl, $context=array())
    {
        $compiler = self::getCompiler(FALSE);

        foreach ($context as $var => $value) {
            $compiler->set_context($var, $value);
        }

        $code = $compiler->compile($tpl);

        return create_function('$' . $compiler->getScopeVariable(NULL, TRUE) . '=array(), $return=TRUE, $blocks=array()', $code);
    }
    // }}}

    // safe_load(string $file, array $vars, bool $return, array $blocks) {{{
    public static function Safe_Load($file, $vars = array(), $return=FALSE, $blocks=array())
    {
        try {

            $tpl = self::$templates_dir.'/'.$file;
            if (file_exists($tpl)) {
                /* call load if the tpl file exists */
                return self::Load($file, $vars, $return, $blocks);
            }
        } Catch (Exception $e) {
        }
        /* some error but we don't care at all */
        return "";
    }
    // }}}

    // load(string $file, array $vars, bool $return, array $blocks) {{{
    /**
     *  Load
     *
     *  Load template. If the template is already compiled, just the compiled
     *  PHP file will be included an used. If the template is new, or it 
     *  had changed, the Haanga compiler is loaded in memory, and the template
     *  is compiled.
     *
     *
     *  @param string $file
     *  @param array  $vars 
     *  @param bool   $return
     *  @param array  $blocks   
     *
     *  @return string|NULL
     */
    public static function Load($file, $vars = array(), $return=FALSE, $blocks=array())
    {
        if (empty(self::$cache_dir)) {
            throw new Haanga_Exception("Cache dir or template dir is missing");
        }

        self::$has_compiled = FALSE;

        $tpl      = self::$templates_dir.'/'.$file;
        $fnc      = sha1($tpl);
        $callback = "haanga_".$fnc;

        if (is_callable($callback)) {
            return $callback($vars, $return, $blocks);
        }

        $php = self::$hash_filename ? $fnc : $file;
        $php = self::$cache_dir.'/'.$php.'.php';

        $check = TRUE;

        if (self::$check_ttl && self::$check_get && self::$check_set) {
            /* */
            if (call_user_func(self::$check_get, $callback)) {
                /* disable checking for the next $check_ttl seconds */
                $check = FALSE;
            } else {
                $result = call_user_func(self::$check_set, $callback, TRUE, self::$check_ttl);
            }
        } 
        
        if (!is_file($php) || ($check && filemtime($tpl) > filemtime($php))) {

            if (!is_file($tpl)) {
                /* There is no template nor compiled file */
                throw new Exception("View {$file} doesn't exists");
            }

            if (!is_dir(dirname($php))) {
                $old = umask(0);
                mkdir(dirname($php), 0777, TRUE);
                umask($old);
            }
            
            $fp = fopen($php, "a+");
            /* try to block PHP file */
            if (!flock($fp, LOCK_EX | LOCK_NB)) {
                /* couldn't block, another process is already compiling */
                fclose($fp);
                if (is_file($php)) {
                    /*
                    ** if there is an old version of the cache 
                    ** load it 
                    */
                    require $php;
                    if (is_callable($callback)) {
                        return $callback($vars, $return, $blocks);
                    }
                }
                /*
                ** no luck, probably the template is new
                ** the compilation will be done, but we won't
                ** save it (we'll use eval instead)
                */
                unset($fp);
            }

            /* recompile */
            $compiler = self::getCompiler();

            if (self::$debug) {
                $compiler->setDebug($php.".dump");
            }

            try {
                $code = $compiler->compile_file($tpl, FALSE, $vars);
            } catch (Exception $e) {
                if (isset($fp)) {
                    /*
                    ** set the $php file as old (to force future
                    ** recompilation)
                    */
                    touch($php, 300, 300);
                    chmod($php, 0777);
                }
                /* re-throw exception */
                throw $e;
            }

            if (isset($fp)) {
                ftruncate($fp, 0); // truncate file
                fwrite($fp, "<?php".$code);
                flock($fp, LOCK_UN); // release the lock
                fclose($fp);
            } else {
                /* local eval */
                eval($code);
            }

            self::$has_compiled = TRUE;
        }

        if (!is_callable($callback)) {
            /* Load the cached PHP file */
            require $php;
            if (!is_callable($callback)) {
                /* 
                   really weird case ($php is empty, another process is compiling
                   the $tpl for the first time), so create a lambda function
                   for the template
                 */
                $lambda= self::compile(file_get_contents($tpl), $vars);
                return $lambda($vars, $return, $blocks);
            }
        }

        if (!isset($HAANGA_VERSION) || $HAANGA_VERSION != HAANGA_VERSION) {
            touch($php, 300, 300);
            chmod($php, 0777);
        }

        return $callback($vars, $return, $blocks);
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
