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

class Haanga_Compiler
{

    // properties {{{
    protected static $block_var=NULL;
    protected $generator;
    protected $forloop = array();
    protected $forid = 0;
    protected $sub_template = FALSE;
    protected $name;
    protected $check_function = FALSE;
    protected $blocks=array();
    protected $line=0;
    protected $file;
    /**
     *  number of blocks :-)
     */
    protected $in_block=0;
    /**
     *  output buffers :-)
     */
    protected $ob_start=0;
    protected $append;
    protected $prepend_op;
    /**
     *  Context at compile time
     */
    protected $context;
    /**
     *  Table which contains all variables
     *  aliases defined in the template
     */
    protected $var_alias;
    /**
     *  Flag the current variable as safe. This means
     *  that escape won't be called if autoescape is
     *  activated (which is activated by default)
     */
    public $var_is_safe=FALSE;
    public $safes;

    /* compiler options */
    static protected $autoescape = TRUE;
    static protected $if_empty   = TRUE;
    static protected $dot_as_object = TRUE;
    static protected $strip_whitespace = FALSE;
    static protected $is_exec_enabled  = FALSE;
    static protected $global_context = array();
    static protected $echo_concat = '.';
    static protected $enable_load = TRUE;

    /**
     *  Debug file
     */
    protected $debug;
    // }}}

    function __construct()
    {
        $this->generator = new Haanga_Generator_PHP;
        if (self::$block_var===NULL) {
            self::$block_var = '{{block.'.md5('super').'}}';
        }
    }

    public function getScopeVariable($part = NULL, $string = FALSE)
    {
        static $var = NULL;

        if ($var === NULL) {
            $var = 'vars' . uniqid(true);
        }

        if ($string) {
            return $var;
        }

        if ($part !== NULL) {
            return hvar($var, $part);
        }
        return hvar($var);
    }

    // getOption($option) {{{
    public static function getOption($option)
    {
        $value = NULL;
        switch (strtolower($option)) {
        case 'enable_load':
            $value = self::$enable_load;
            break;
        case 'if_empty':
            $value = self::$if_empty;
            break;
        case 'autoescape':
            $value = self::$autoescape;
            break;
        case 'dot_as_object':
            $value = self::$dot_as_object;
            break;
        case 'echo_concat':
            $value = self::$echo_concat;
            break;
        case 'strip_whitespace':
            $value = self::$strip_whitespace;
            break;
        case 'is_exec_enabled':
        case 'allow_exec':
            $value = self::$is_exec_enabled;
            break;
        case 'global':
            $value = self::$global_context;
            break;
        }
        return $value;
    }
    // }}}

    // setOption($option, $value) {{{
    /**
     *  Set Compiler option.
     *
     *  @return void
     */
    public static function setOption($option, $value)
    {
        switch (strtolower($option)) {
        case 'if_empty':
            self::$if_empty = (bool)$value;
            break;
        case 'enable_load':
            self::$enable_load = (bool)$value;
        case 'echo_concat':
            if ($value == '.' || $value == ',') {
                self::$echo_concat = $value;
            }
            break;

        case 'autoescape':
            self::$autoescape = (bool)$value;
            break;
        case 'dot_as_object':
            self::$dot_as_object = (bool)$value;
            break;
        case 'strip_whitespace':
            self::$strip_whitespace = (bool)$value;
            break;
        case 'is_exec_enabled':
        case 'allow_exec':
            self::$is_exec_enabled = (bool)$value;
            break;
        case 'global':
            if (!is_array($value)) {
                $value = array($value);
            }
            self::$global_context = $value;
            break;
        }
    }
    // }}}

    // setDebug($file) {{{
    function setDebug($file)
    {
        $this->debug = $file;
    }
    // }}}

    // reset() {{{
    function reset()
    {
        foreach (array_keys(get_object_vars($this)) as $key) {
            if (isset($avoid_cleaning[$key])) {
                continue;
            }
            $this->$key = NULL;
        }
        $this->generator = new Haanga_Generator_PHP;
        $this->blocks = array();
        $this->cycle  = array();
    }
    // }}}

    // get_template_name() {{{
    final function get_template_name()
    {
        return $this->name;
    }
    // }}}

    // Set template name {{{
    function set_template_name($path)
    {
        $file = basename($path);
        $pos  = strpos($file,'.');
        return ($this->name = substr($file, 0, $pos));
    }
    // }}}

    // get_function_name(string $name) {{{
    function get_function_name($name)
    {
        return "{$name}_template";
    }
    // }}}

    // Compile ($code, $name=NULL) {{{
    final function compile($code, $name=NULL, $file=NULL)
    {
        $this->name = $name;

        if (count(self::$global_context) > 0) {
            /* add global variables (if any) to the current context */
            foreach (self::$global_context as $var) {
                $this->set_context($var, $GLOBALS[$var]);
            }
        }

        $parsed = Haanga_Compiler_Tokenizer::init($code, $this, $file);
        $code   = "";
        $this->subtemplate = FALSE;

        $body = new Haanga_AST;
        $this->prepend_op = hcode();

        if (isset($parsed[0]) && $parsed[0]['operation'] == 'base') {
            /* {% base ... %} found */
            $base  = $parsed[0][0];
            $code .= $this->get_base_template($base);
            unset($parsed[0]);
        }

        if (defined('HAANGA_VERSION')) {
            $body->decl('HAANGA_VERSION', HAANGA_VERSION);
        }

        if ($name) {
            $func_name = $this->get_function_name($name);
            if ($this->check_function) {
                $body->do_if(hexpr(hexec('function_exists', $func_name), '===', FALSE));
            }
            if (!empty($this->file)) {
                $body->comment("Generated from ".$this->file);
            }

            $body->declare_function($func_name);
        }
        if (count(self::$global_context) > 0) {
            $body->do_global(self::$global_context);
        }


        $body->do_exec('extract', $this->getScopeVariable());
        $body->do_if(hexpr(hvar('return'), '==', TRUE));
        $body->do_exec('ob_start');
        $body->do_endif();


        $this->generate_op_code($parsed, $body);
        if ($this->subtemplate) {
            $expr = $this->expr_call_base_template();
            $this->do_print($body, $expr);
        }

        $body->do_if(hexpr(hvar('return'), '==', TRUE));
        $body->do_return(hexec('ob_get_clean'));
        $body->do_endif();

        if ($name) {
            $body->do_endfunction();
            if ($this->check_function) {
                $body->do_endif();
            }
        }

        if ($this->prepend_op->stack_size() > 0) {
            $this->prepend_op->append_ast($body);
            $body = $this->prepend_op;
        }

        $op_code = $body->getArray(TRUE);


        $code   .= $this->generator->getCode($op_code, $this->getScopeVariable(NULL, TRUE));
        if (!empty($this->append)) {
            $code .= $this->append;
        }

        if (!empty($this->debug)) {
            $op_code['php'] = $code;
            file_put_contents($this->debug, print_r($op_code, TRUE), LOCK_EX);
        }
        return $code;
    }
    // }}}

    // compile_file($file) {{{
    /**
     *  Compile a file
     *
     *  @param string $file File path
     *  @param bool   $safe Whether or not add check if the function is already defined
     *
     *  @return Generated PHP code
     */
    final function compile_file($file, $safe=FALSE, $context=array())
    {
        if (!is_readable($file)) {
            throw new Haanga_Compiler_Exception("$file is not a file");
        }

        $this->_base_dir      = dirname($file);
        $this->file           = realpath($file);
        $this->line           = 0;
        $this->check_function = $safe;
        $this->context        = $context;
        $name                 = $this->set_template_name($file);
        try {
            return $this->compile(file_get_contents($file), $name, $file);
        } catch (Exception $e) {
            $this->Error((string)$e);
        }
    }
    // }}}

    // getOpCodes($code, $file='') {{{
    /**
     *  Compile the $code and return the "opcodes"
     *  (the Abstract syntax tree).
     *
     *  @param string $code Template content
     *  @param string $file File path (used for erro reporting)
     *
     *  @return Haanga_AST
     *
     */
    public function getOpCodes($code, $file)
    {
        $oldfile    = $this->file;
        $this->file = $file;
        $parsed = Haanga_Compiler_Tokenizer::init($code, $this, $file);
        $body = new Haanga_AST;
        if (isset($parsed[0]) && $parsed[0]['operation'] == 'base') {
            throw new Exception("{% base is not supported on inlines %}");
        }
        $body = new Haanga_AST;
        $this->generate_op_code($parsed, $body);
        $this->file = $oldfile;
        return $body;
    }
    // }}}

    // Error($errtxt) {{{
    /**
     *  Throw an exception and appends information about the template (the path and
     *  the last processed line).
     *
     *
     */
    public function Error($err)
    {
        throw new Haanga_Compiler_Exception("{$err} in {$this->file}:$this->line");
    }
    // }}}

    // is_expr methods {{{
    function is_var_filter($cmd)
    {
        return isset($cmd['var_filter']);
    }

    // }}}

    // expr_call_base_template() {{{
    /**
     *  Generate code to call base template
     *
     */
    function expr_call_base_template()
    {
        return hexec(
            $this->get_function_name($this->subtemplate),
            $this->getScopeVariable(), TRUE,
            hvar('blocks')
        );
    }
    // }}}

    // get_base_template($base) {{{
    /**
     *  Handle {% base  "" %} definition. By default only
     *  static (string) are supported, but this can be overrided
     *  on subclasses.
     *
     *  This method load the base class, compile it and return
     *  the generated code.
     *
     *  @param array $base Base structure
     *
     *  @return string Generated source code
     */
    function get_base_template($base)
    {
        if (!Haanga_AST::is_str($base)) {
            throw new Exception("Dynamic inheritance is not supported for compilated templates");
        }
        $file = $base['string'];
        list($this->subtemplate, $new_code) = $this->compile_required_template($file);
        return $new_code."\n\n";
    }
    // }}}

    // {% base "foo.html" %} {{{
    protected function generate_op_base()
    {
        throw new Exception("{% base %} can be only as first statement");
    }
    // }}}

    // Main Loop {{{
    protected function generate_op_code($parsed, &$body)
    {
        if (!is_array($parsed)) {
            throw new Exception("Invalid \$parsed array");
        }
        foreach ($parsed as $op) {
            if (!is_array($op)) {
                continue;
            }
            if (!isset($op['operation'])) {
                throw new Exception("Malformed array:".print_r($op, TRUE));
            }
            if (isset($op['line'])) {
                $this->line = $op['line'];
            }

            if ($this->subtemplate && $this->in_block == 0 && $op['operation'] != 'block') {
                /* ignore most of tokens in subtemplates */
                continue;
            }

            $method = "generate_op_".$op['operation'];
            if (!is_callable(array($this, $method))) {
                throw new Exception("Compiler: Missing method $method");
            }
            $this->$method($op, $body);
        }
    }
    // }}}

    // Check the current expr  {{{
    protected function check_expr(&$expr)
    {
        if (Haanga_AST::is_expr($expr)) {
            if ($expr['op_expr'] == 'in') {
                for ($id=0; $id < 2; $id++) {
                    if ($this->is_var_filter($expr[$id])) {
                        $expr[$id] = $this->get_filtered_var($expr[$id]['var_filter'], $var);
                    }
                }
                if (Haanga_AST::is_str($expr[1])) {
                    $expr = hexpr(hexec('strpos', $expr[1], $expr[0]), '!==', FALSE);
                } else {
                    $expr = hexpr(
                        hexpr_cond(
                            hexec('is_array', $expr[1]),
                            hexec('array_search', $expr[0], $expr[1]),
                           hexec('strpos', $expr[1], $expr[0])
                        )
                        ,'!==', FALSE
                    );
                }
            }
            if (is_object($expr)) {
                $expr = $expr->getArray();
            }
            $this->check_expr($expr[0]);
            $this->check_expr($expr[1]);
        } else if (is_array($expr)) {
            if ($this->is_var_filter($expr)) {
                $expr = $this->get_filtered_var($expr['var_filter'], $var);
            } else if (isset($expr['args'])) {
                /* check every arguments */
                foreach ($expr['args'] as &$v) {
                    $this->check_expr($v);
                }
                unset($v);
            } else  if (isset($expr['expr_cond'])) {
                /* Check expr conditions */
                $this->check_expr($expr['expr_cond']);
                $this->check_expr($expr['true']);
                $this->check_expr($expr['false']);
            }
        }
    }
    // }}}

    // ifequal|ifnot equal <var_filtered|string|number> <var_fitlered|string|number> ... else ... {{{
    protected function generate_op_ifequal($details, &$body)
    {
        $if['expr'] = hexpr($details[1], $details['cmp'], $details[2])->getArray();
        $if['body'] = $details['body'];
        if (isset($details['else'])) {
            $if['else'] =  $details['else'];
        }
        $this->generate_op_if($if, $body);
    }
    // }}}

    // {% if <expr> %} HTML {% else %} TWO {% endif $} {{{
    protected function generate_op_if($details, &$body)
    {
        if (self::$if_empty && $this->is_var_filter($details['expr']) && count($details['expr']['var_filter']) == 1) {
            /* if we are doing if <Variable> it should check
               if it exists without throw any warning */
            $expr = $details['expr'];
            $expr['var_filter'][] = 'empty';

            $variable = $this->get_filtered_var($expr['var_filter'], $var);

            $details['expr'] = hexpr($variable, '===', FALSE)->getArray();
        }
        $this->check_expr($details['expr']);
        $expr = Haanga_AST::fromArrayGetAST($details['expr']);
        $body->do_if($expr);
        $this->generate_op_code($details['body'], $body);
        if (isset($details['else'])) {
            $body->do_else();
            $this->generate_op_code($details['else'], $body);
        }
        $body->do_endif();
    }
    // }}}

    // Override template {{{
    protected function compile_required_template($file)
    {
        if (!is_file($file)) {
            if (isset($this->_base_dir)) {
                $file = $this->_base_dir.'/'.$file;
            }
        }
        if (!is_file($file)) {
           throw new Exception("can't find {$file} file template");
        }
        $class = get_class($this);
        $comp  = new  $class;
        $comp->reset();
        $code = $comp->compile_file($file, $this->check_function);
        return array($comp->get_template_name(), $code);
    }
    // }}}

    // include "file.html" | include <var1> {{{
    protected function generate_op_include($details, &$body)
    {
        if (!$details[0]['string']) {
            throw new Exception("Dynamic inheritance is not supported for compilated templates");
        }
        list($name,$code) = $this->compile_required_template($details[0]['string']);
        $this->append .= "\n\n{$code}";
        $this->do_print($body,
            hexec($this->get_function_name($name),
            $this->getScopeVariable(), TRUE, hvar('blocks'))
        );
    }
    // }}}

    // Handle HTML code {{{
    protected function generate_op_html($details, &$body)
    {
        $string = Haanga_AST::str($details['html']);
        $this->do_print($body, $string);
    }
    // }}}

    function isMethod($varname, &$expr)
    {
        if (is_array($varname)) {
            $tmp    = $varname;
            $method = array_pop($tmp);
            $object = $this->get_context($tmp);
            if (!empty($method['object']) && is_string($method['object'])) {
                $property = $method['object'];
                if (is_object($object) && !isset($object->$property) && is_callable(array($object, $property))) {
                    $expr = hexec($varname);
                    return TRUE;
                }
            }
        }
        return FALSE;
    }

    // get_var_filtered {{{
    /**
     *  This method handles all the filtered variable (piped_list(X)'s
     *  output in the parser.
     *
     *
     *  @param array $variable      (Output of piped_list(B) (parser))
     *  @param array &$varname      Variable name
     *  @param bool  $accept_string  TRUE is string output are OK (ie: block.parent)
     *
     *  @return expr
     *
     */
    function get_filtered_var($variable, &$varname, $accept_string=NULL)
    {
        $this->var_is_safe = FALSE;

        if ($accept_string === NULL && is_array($variable[0])) {
            $accept_string = $variable[0][0] === 'block';
        }

        if (count($variable) > 1) {
            $count  = count($variable);
            $target = $this->generate_variable_name($variable[0]);

            if (!Haanga_AST::is_var($target)) {
                /* block.super can't have any filter */
                throw new Exception("This variable can't have any filter");
            }

            if ($this->isMethod($target['var'], $return)) {
                $target = $return;
            }

            for ($i=1; $i < $count; $i++) {
                $func_name = $variable[$i];
                if ($func_name == 'escape') {
                    /* to avoid double cleaning */
                    $this->var_is_safe = TRUE;
                }
                $args = array(isset($exec) ? $exec : $target);
                $exec = $this->do_filtering($func_name, $args);
            }
            unset($variable);
            $varname = $args[0];
            $details = $exec;
        } else {
            $details = $this->generate_variable_name($variable[0]);
            $varname = $variable[0];

            if ($this->isMethod($varname, $return)) {
                return $return;
            }

            if (!Haanga_AST::is_var($details) && !$accept_string) {
                /* generate_variable_name didn't replied a variable, weird case
                    currently just used for {{block.super}}.
                */
                throw new Exception("Invalid variable name {$variable[0]}");
            }
        }

        return $details;
    }
    // }}}

    // generate_op_print_var {{{
    /**
     *  Generate code to print a variable with its filters, if there is any.
     *
     *  All variable (except those flagged as |safe) are automatically
     *  escaped if autoescape is "on".
     *
     */
    protected function generate_op_print_var($details, &$body)
    {
        $expr = $details['expr'];
        $this->check_expr($expr);


        if (!$this->is_safe($expr) && self::$autoescape) {
            $args    = array($expr);
            $expr = $this->do_filtering('escape', $args);
        }

        $this->do_print($body, $expr);
    }
    // }}}

    // {# something #} {{{
    protected function generate_op_comment($details, &$body)
    {
        /* comments are annoying */
        //$body->comment($details['comment']);
    }
    // }}}

    // {% block 'name' %} ... {% endblock %} {{{
    protected function generate_op_block($details, &$body)
    {
        if (is_array($details['name'])) {
            $name = "";
            foreach ($details['name'] as $part) {
                if (is_string($part)) {
                    $name .= "{$part}";
                } else if (is_array($part)) {
                    if (Haanga_AST::is_str($part)) {
                        $name .= "{$part['string']}";
                    } elseif (isset($part['object'])) {
                        $name .= "{$part['object']}";
                    } else {
                        throw new Exception("Invalid blockname");
                    }
                }
                $name .= ".";
            }
            $details['name'] = substr($name, 0, -1);
        }
        $this->in_block++;
        $this->blocks[] = $details['name'];
        $block_name = hvar('blocks', $details['name']);

        $this->ob_start($body);
        $buffer_var = 'buffer'.$this->ob_start;

        $content = hcode();
        $this->generate_op_code($details['body'], $content);

        $body->append_ast($content);
        $this->ob_start--;

        $buffer = hvar($buffer_var);

        /* {{{ */
        /**
         *  isset previous block (parent block)?
         *  TRUE
         *      has reference to self::$block_var ?
         *      TRUE
         *          replace self::$block_var for current block value (buffer)
         *      FALSE
         *          print parent block
         *  FALSE
         *      print current block
         *
         */
        $declare = hexpr_cond(
            hexec('isset', $block_name),
            hexpr_cond(
                hexpr(hexec('strpos', $block_name, self::$block_var), '===', FALSE),
                $block_name,
                hexec('str_replace', self::$block_var, $buffer, $block_name)
            ), $buffer);
        /* }}} */

        if (!$this->subtemplate) {
            $this->do_print($body, $declare);
        } else {
            $body->decl($block_name, $declare);
            if ($this->in_block > 1) {
                $this->do_print($body, $block_name);
            }
        }
        array_pop($this->blocks);
        $this->in_block--;

    }
    // }}}

    // regroup <var1> by <field> as <foo> {{{
    protected function generate_op_regroup($details, &$body)
    {
        $body->comment("Temporary sorting");

        $array = $this->get_filtered_var($details['array'], $varname);

        if (Haanga_AST::is_exec($array)) {
            $varname = hvar($details['as']);
            $body->decl($varname, $array);
        }
        $var = hvar('item', $details['row']);

        $body->decl('temp_group', array());

        $body->do_foreach($varname, 'item', NULL,
            hcode()->decl(hvar('temp_group', $var, NULL), hvar('item'))
        );

        $body->comment("Proper format");
        $body->decl($details['as'], array());
        $body->do_foreach('temp_group', 'item', 'group',
            hcode()->decl(
                hvar($details['as'], NULL),
                array("grouper" => hvar('group'), "list"    => hvar('item'))
            )
        );
        $body->comment("Sorting done");
    }
    // }}}

    // variable context {{{
    /**
     *  Variables context
     *
     *  These two functions are useful to detect if a variable
     *  separated by dot (foo.bar) is an array or object. To avoid
     *  overhead we decide it at compile time, rather than
     *  ask over and over at rendering time.
     *
     *  foo.bar:
     *      + If foo exists at compile time,
     *        and it is an array, it would be foo['bar']
     *        otherwise it'd be foo->bar.
     *      + If foo don't exists at compile time,
     *        it would be foo->bar if the compiler option
     *        dot_as_object is TRUE (by default) otherwise
     *        it'd be foo['bar']
     *
     *  @author crodas
     *  @author gallir (ideas)
     *
     */
    function set_context($varname, $value)
    {
        $this->context[$varname] = $value;
    }

    function get_context($variable)
    {
        if (!is_array($variable)) {
            $variable = array($variable);
        }
        $varname = $variable[0];
        if (isset($this->context[$varname])) {
            if (count($variable) == 1) {
                return $this->context[$varname];
            }
            $var = & $this->context[$varname];
            foreach ($variable as $id => $part) {
                if ($id != 0) {
                    if (is_array($part) && isset($part['object'])) {
                        if (is_array($part['object']) && isset($part['object']['var'])) {
                            /* object $foo->$bar */
                            $name = $part['object']['var'];
                            $name = $this->get_context($name);
                            if (!isset($var->$name)) {
                                return NULL;
                            }
                            $var = &$var->$name;
                        } else {
                            if (!isset($var->$part['object'])) {
                                return NULL;
                            }
                            $var = &$var->$part['object'];
                        }
                    } else if (is_object($var)) {
                        if (!isset($var->$part)) {
                            return NULL;
                        }
                        $var = &$var->$part;
                    } else {
                        if (!is_scalar($part) || empty($part) || !isset($var[$part])) {
                            return NULL;
                        }
                        $var = &$var[$part];
                    }
                }
            }
            $variable = $var;
            unset($var);
            return $variable;
        }

        return NULL;
    }

    function var_is_object(Array $variable, $default=NULL)
    {
        $varname = $variable[0];
        switch ($varname) {
        case 'GLOBALS':
        case '_SERVER':
        case '_GET':
        case '_POST':
        case '_FILES':
        case '_COOKIE':
        case '_SESSION':
        case '_REQUEST':
        case '_ENV':
        case 'forloop':
        case 'block':
            return FALSE; /* these are arrays */
        }

        $variable = $this->get_context($variable);
        if (is_array($variable) || is_object($variable)) {
            return $default ? is_object($variable) : is_object($variable) && !$variable InstanceOf Iterator && !$variable Instanceof ArrayAccess;
        }

        return $default===NULL ? self::$dot_as_object : $default;
    }
    // }}}

    // Get variable name {{{
    function generate_variable_name($variable, $special=true)
    {
        if (is_array($variable)) {
            switch ($variable[0]) {
            case 'forloop':
                if (!$special) {
                    return array('var' => $variable);
                }
                if (!$this->forid) {
                    throw new Exception("Invalid forloop reference outside of a loop");
                }

                switch ($variable[1]['object']) {
                case 'counter':
                    $this->forloop[$this->forid]['counter'] = TRUE;
                    $variable = 'forcounter1_'.$this->forid;
                    break;
                case 'counter0':
                    $this->forloop[$this->forid]['counter0'] = TRUE;
                    $variable = 'forcounter0_'.$this->forid;
                    break;
                case 'last':
                    $this->forloop[$this->forid]['counter'] = TRUE;
                    $this->forloop[$this->forid]['last']    = TRUE;
                    $variable = 'islast_'.$this->forid;
                    break;
                case 'first':
                    $this->forloop[$this->forid]['first']    = TRUE;
                    $variable = 'isfirst_'.$this->forid;
                    break;
                case 'revcounter':
                    $this->forloop[$this->forid]['revcounter'] = TRUE;
                    $variable = 'revcount_'.$this->forid;
                    break;
                case 'revcounter0':
                    $this->forloop[$this->forid]['revcounter0'] = TRUE;
                    $variable = 'revcount0_'.$this->forid;
                    break;
                case 'parentloop':
                    unset($variable[1]);
                    $this->forid--;
                    $variable = $this->generate_variable_name(array_values($variable));
                    $variable = $variable['var'];
                    $this->forid++;
                    break;
                default:
                    throw new Exception("Unexpected forloop.{$variable[1]}");
                }
                /* no need to escape it */
                $this->var_is_safe = TRUE;
                break;
            case 'block':
                if (!$special) {
                    return array('var' => $variable);
                }
                if ($this->in_block == 0) {
                    throw new Exception("Can't use block.super outside a block");
                }
                if (!$this->subtemplate) {
                    throw new Exception("Only subtemplates can call block.super");
                }
                /* no need to escape it */
                $this->var_is_safe = TRUE;
                return Haanga_AST::str(self::$block_var);
                break;
            default:
                /* choose array or objects */

                if ($special) {
                    // this section is resolved on the parser.y
                    return array('var' => $variable);
                }

                for ($i=1; $i < count($variable); $i++) {
                    $var_part = array_slice($variable, 0, $i);
                    $def_arr  = TRUE;

                    if (is_array($variable[$i])) {
                        if (isset($variable[$i]['class'])) {
                            // no type guess for static properties
                            continue;
                        }
                        if (isset($variable[$i]['object'])) {
                            $def_arr = FALSE;
                        }
                        if (!Haanga_AST::is_var($variable[$i])) {
                            $variable[$i] = current($variable[$i]);
                        } else {
                            $variable[$i] = $this->generate_variable_name($variable[$i]['var']);
                        }
                    }

                    $is_obj = $this->var_is_object($var_part, 'unknown');

                    if ( $is_obj === TRUE || ($is_obj == 'unknown' && !$def_arr)) {
                        $variable[$i] = array('object' => $variable[$i]);
                    }
                }

                break;
            }

        } else if (isset($this->var_alias[$variable])) {
            $variable = $this->var_alias[$variable]['var'];
        }

        return hvar($variable)->getArray();
    }
    // }}}

    // Print {{{
    public function do_print(Haanga_AST $code, $stmt)
    {
        /* Flag this object as a printing one */
        $code->doesPrint = TRUE;

        if (self::$strip_whitespace && Haanga_AST::is_str($stmt)) {
            $stmt['string'] = preg_replace('/\s+/', ' ', $stmt['string']);
        }

        if ($this->ob_start == 0) {
            $code->do_echo($stmt);
            return;
        }

        $buffer = hvar('buffer'.$this->ob_start);
        $code->append($buffer, $stmt);

    }

    // }}}

    // for [<key>,]<val> in <array> {{{
    protected function generate_op_loop($details, &$body)
    {
        if (isset($details['empty'])) {
            $body->do_if(hexpr(hexec('count', hvar($details['array'])), '==', 0));
            $this->generate_op_code($details['empty'], $body);
            $body->do_else();
        }

        /* ForID */
        $oldid       = $this->forid;
        $this->forid = $oldid+1;
        $this->forloop[$this->forid] = array();

        if (isset($details['range'])) {
            $this->set_safe($details['variable']);
        } else {
            /* check variable context */

            /* Check if the array to iterate is an object */
            $var = &$details['array'][0];
            if (is_string($var) && $this->var_is_object(array($var), FALSE)) {
                /* It is an object, call to get_object_vars */
                $body->decl($var.'_arr', hexec('get_object_vars', hvar($var)));
                $var .= '_arr';
            }
            unset($var);
            /* variables */
            $array = $this->get_filtered_var($details['array'], $varname);

            /* Loop body */
            if ($this->is_safe(hvar($varname))) {
                $this->set_safe(hvar($details['variable']));
            }

            if ($array Instanceof Haanga_AST) {
                // filtered var
                $tmp = hvar('tmp'.($oldid+1));
                $body->decl($tmp, $array);
                $array = $tmp;
            }
            $details['array'] = $array;
        }

        /* for_body {{{ */
        $for_body = hcode();
        $this->generate_op_code($details['body'], $for_body);


        $oid  = $this->forid;
        $size = hvar('psize_'.$oid);

        // counter {{{
        if (isset($this->forloop[$oid]['counter'])) {
            $var   = hvar('forcounter1_'.$oid);
            $body->decl($var, 1);
            $for_body->decl($var, hexpr($var, '+', 1));
        }
        // }}}

        // counter0 {{{
        if (isset($this->forloop[$oid]['counter0'])) {
            $var   = hvar('forcounter0_'.$oid);
            $body->decl($var, 0);
            $for_body->decl($var, hexpr($var, '+', 1));
        }
        // }}}

        // last {{{
        if (isset($this->forloop[$oid]['last'])) {
            if (!isset($cnt)) {
                $body->decl('psize_'.$oid, hexec('count', hvar_ex($details['array'])));
                $cnt = TRUE;
            }
            $var  = 'islast_'.$oid;
            $body->decl($var, hexpr(hvar('forcounter1_'.$oid), '==', $size));
            $for_body->decl($var, hexpr(hvar('forcounter1_'.$oid), '==', $size));
        }
        // }}}

        // first {{{
        if (isset($this->forloop[$oid]['first'])) {
            $var = hvar('isfirst_'.$oid);
            $body->decl($var, TRUE);
            $for_body->decl($var, FALSE);
        }
        // }}}

        // revcounter {{{
        if (isset($this->forloop[$oid]['revcounter'])) {
            if (!isset($cnt)) {
                $body->decl('psize_'.$oid, hexec('count', hvar_ex($details['array'])));
                $cnt = TRUE;
            }
            $var = hvar('revcount_'.$oid);
            $body->decl($var, $size);
            $for_body->decl($var, hexpr($var, '-', 1));
        }
         // }}}

        // revcounter0 {{{
        if (isset($this->forloop[$oid]['revcounter0'])) {
            if (!isset($cnt)) {
                $body->decl('psize_'.$oid, hexec('count', hvar_ex($details['array'])));
                $cnt = TRUE;
            }
            $var = hvar('revcount0_'.$oid);
            $body->decl($var, hexpr($size, "-", 1));
            $for_body->decl($var, hexpr($var, '-', 1));
        }
        // }}}

        /* }}} */

        /* Restore old ForID */
        $this->forid = $oldid;

        /* Merge loop body  */
        if (!isset($details['range'])) {
            $body->do_foreach($array, $details['variable'], $details['index'], $for_body);

            if ($this->is_safe(hvar($varname))) {
                $this->set_unsafe($details['variable']);
            }
        } else {
            for ($i=0; $i < 2; $i++) {
                if (Haanga_AST::is_var($details['range'][$i])) {
                    $details['range'][$i] = $this->generate_variable_name($details['range'][$i]['var']);
                }
            }

            if (Haanga_AST::is_var($details['step'])) {
                $details['step'] = $this->generate_variable_name($details['step']['var']);
            }
            $body->do_for($details['variable'], $details['range'][0], $details['range'][1], $details['step'], $for_body);
            $this->set_unsafe(hvar($details['variable']));
        }

        if (isset($details['empty'])) {
            $body->do_endif();
        }
    }
    // }}}

    function generate_op_set($details, &$body)
    {
        $var = $this->generate_variable_name($details['var']);
        $this->check_expr($details['expr']);
        $body->decl_raw($var, $details['expr']);
        $body->decl($this->getScopeVariable($var['var']), $var);
    }


    // ifchanged [<var1> <var2] {{{
    protected function generate_op_ifchanged($details, &$body)
    {
        static $ifchanged = 0;

        $ifchanged++;
        $var1 = 'ifchanged'.$ifchanged;
        if (!isset($details['check'])) {
            /* ugly */
            $this->ob_start($body);
            $var2 = hvar('buffer'.$this->ob_start);


            $this->generate_op_code($details['body'], $body);
            $this->ob_start--;
            $body->do_if(hexpr(hexec('isset', hvar($var1)), '==', FALSE, '||', hvar($var1), '!=', $var2));
            $this->do_print($body, $var2);
            $body->decl($var1, $var2);
        } else {
            /* beauty :-) */
            foreach ($details['check'] as $id=>$type) {
                if (!Haanga_AST::is_var($type)) {
                    throw new Exception("Unexpected string {$type['string']}, expected a varabile");
                }

                $this_expr = hexpr(hexpr(
                    hexec('isset', hvar($var1, $id)), '==', FALSE,
                    '||', hvar($var1, $id), '!=', $type
                ));

                if (isset($expr)) {
                    $this_expr = hexpr($expr, '&&', $this_expr);
                }

                $expr = $this_expr;

            }
            $body->do_if($expr);
            $this->generate_op_code($details['body'], $body);
            $body->decl($var1, $details['check']);
        }

        if (isset($details['else'])) {
            $body->do_else();
            $this->generate_op_code($details['else'], $body);
        }
        $body->do_endif();
    }
    // }}}

    // autoescape ON|OFF {{{
    function generate_op_autoescape($details, &$body)
    {
        $old_autoescape   = self::$autoescape;
        self::$autoescape = strtolower($details['value']) == 'on';
        $this->generate_op_code($details['body'], $body);
        self::$autoescape = $old_autoescape;
    }
    // }}}

    // {% spacefull %} Set to OFF strip_whitespace for a block (the compiler option) {{{
    function generate_op_spacefull($details, &$body)
    {
        $old_strip_whitespace   = self::$strip_whitespace;
        self::$strip_whitespace = FALSE;
        $this->generate_op_code($details['body'], $body);
        self::$strip_whitespace = $old_strip_whitespace;
    }
    // }}}

    // ob_Start(array &$body) {{{
    /**
     *  Start a new buffering
     *
     */
    function ob_start(&$body)
    {
        $this->ob_start++;
        $body->decl('buffer'.$this->ob_start, "");
    }
    // }}}

    // Custom Tags {{{
    function get_custom_tag($name)
    {
        $function = $this->get_function_name($this->name).'_tag_'.$name;
        $this->append .= "\n\n".Haanga_Extension::getInstance('Tag')->getFunctionBody($name, $function);
        return $function;
    }

    /**
     *  Generate needed code for custom tags (tags that aren't
     *  handled by the compiler).
     *
     */
    function generate_op_custom_tag($details, &$body)
    {
        static $tags;
        if (!$tags) {
            $tags = Haanga_Extension::getInstance('Tag');
        }

        foreach ($details['list'] as $id => $arg) {
            if (Haanga_AST::is_var($arg)) {
                $details['list'][$id] = $this->generate_variable_name($arg['var']);
            }
        }


        $tag_name    = $details['name'];
        $tagFunction = $tags->getFunctionAlias($tag_name);

        if (!$tagFunction && !$tags->hasGenerator($tag_name)) {
            $function = $this->get_custom_tag($tag_name, isset($details['as']));
        } else {
            $function = $tagFunction;
        }
        if (isset($details['body'])) {
            /*
               if the custom tag has 'body'
               then it behave the same way as a filter
            */
            $this->ob_start($body);
            $this->generate_op_code($details['body'], $body);
            $target = hvar('buffer'.$this->ob_start);
            if ($tags->hasGenerator($tag_name)) {
                $args = array_merge(array($target), $details['list']);
                $exec = $tags->generator($tag_name, $this, $args);
                if (!$exec InstanceOf Haanga_AST) {
                    throw new Exception("Invalid output of custom filter {$tag_name}");
                }
                if ($exec->stack_size() >= 2 || $exec->doesPrint) {
                    /*
                        The generator returned more than one statement,
                        so we assume the output is already handled
                        by one of those stmts.
                    */
                    $body->append_ast($exec);
                    $this->ob_start--;
                    return;
                }
            } else {
                $exec = hexec($function, $target);
            }
            $this->ob_start--;
            $this->do_print($body, $exec);
            return;
        }

        $var  = isset($details['as']) ? $details['as'] : NULL;
        $args = array_merge(array($function), $details['list']);

        if ($tags->hasGenerator($tag_name)) {
            $exec = $tags->generator($tag_name, $this, $details['list'], $var);
            if ($exec InstanceOf Haanga_AST) {
                if ($exec->stack_size() >= 2 || $exec->doesPrint || $var !== NULL) {
                    /*
                        The generator returned more than one statement,
                        so we assume the output is already handled
                        by one of those stmts.
                    */
                    $body->append_ast($exec);
                    return;
                }
            } else {
                throw new Exception("Invalid output of the custom tag {$tag_name}");
            }
        } else {
            $fnc  = array_shift($args);
            $exec = hexec($fnc);
            foreach ($args as $arg) {
                $exec->param($arg);
            }
        }

        if ($var) {
            $body->decl($var, $exec);
        } else {
            $this->do_print($body, $exec);
        }
    }
    // }}}

    // with <variable> as <var> {{{
    /**
     *
     *
     */
    function generate_op_alias($details, &$body)
    {
        $this->var_alias[ $details['as'] ] = $this->generate_variable_name($details['var']);
        $this->generate_op_code($details['body'], $body);
        unset($this->var_alias[ $details['as'] ] );
    }
    // }}}

    // Custom Filters {{{
    function get_custom_filter($name)
    {
        $function = $this->get_function_name($this->name).'_filter_'.$name;
        $this->append .= "\n\n".Haanga_Extension::getInstance('Filter')->getFunctionBody($name, $function);
        return $function;
    }


    function do_filtering($name, $args)
    {
        static $filter;
        if (!$filter) {
            $filter = Haanga_Extension::getInstance('Filter');
        }

        if (is_array($name)) {
            /*
               prepare array for ($func_name, $arg1, $arg2 ... )
               where $arg1 = last expression and $arg2.. $argX is
               defined in the template
             */
            $args = array_merge($args, $name['args']);
            $name = $name[0];
        }

        if (!$filter->isValid($name)) {
            throw new Exception("{$name} is an invalid filter");
        }

        if ($filter->isSafe($name)) {
            /* check if the filter is return HTML-safe data (to avoid double scape) */
            $this->var_is_safe = TRUE;
        }


        if ($filter->hasGenerator($name)) {
            return $filter->generator($name, $this, $args);
        }
        $fnc = $filter->getFunctionAlias($name);
        if (!$fnc) {
            $fnc = $this->get_custom_filter($name);
        }

        $args = array_merge(array($fnc), $args);
        $exec = call_user_func_array('hexec', $args);

        return $exec;
    }

    function generate_op_filter($details, &$body)
    {
        $this->ob_start($body);
        $this->generate_op_code($details['body'], $body);
        $target = hvar('buffer'.$this->ob_start);
        foreach ($details['functions'] as $f) {
            $param = (isset($exec) ? $exec : $target);
            $exec  = $this->do_filtering($f, array($param));
        }
        $this->ob_start--;
        $this->do_print($body, $exec);
    }
    // }}}

    /* variable safety {{{ */
    function set_safe($name)
    {
        if (!Haanga_AST::is_Var($name)) {
            $name = hvar($name)->getArray();
        }
        $this->safes[serialize($name)] = TRUE;
    }

    function set_unsafe($name)
    {
        if (!Haanga_AST::is_Var($name)) {
            $name = hvar($name)->getArray();
        }
        unset($this->safes[serialize($name)]);
    }

    function is_safe($name)
    {
        if ($this->var_is_safe) {
            return TRUE;
        }
        if (isset($this->safes[serialize($name)])) {
            return TRUE;
        }
        return FALSE;
    }
    /* }}} */

    final static function main_cli()
    {
        $argv   = $GLOBALS['argv'];
        $haanga = new Haanga_Compiler;
        $code   = $haanga->compile_file($argv[1], TRUE);
        if (!isset($argv[2]) || $argv[2] != '--notags') {
            $code = "<?php\n\n$code";
        }
        echo $code;
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
