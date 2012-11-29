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
 *  Simple AST (abstract syntax tree) helper class. This
 *  helps to generate array structure that is then translated by 
 *  the Haanga_Generator class.
 *
 */
class Haanga_AST
{
    public $stack = array();
    public $current = array();
    public $doesPrint = FALSE;


    // getLast() {{{
    /**
     *  Return a refernce to the last element
     *  of the AST stack.
     *
     *  @return array
     */
    function & getLast()
    {
        $f = array();
        if (count($this->stack) == 0) {
            return $f;
        }
        return $this->stack[count($this->stack)-1];
    }
    // }}}


    static protected function check_type($obj, $type)
    {
        if (is_string($obj)) {
            return FALSE;
        }
        if (is_object($obj)) {
            $obj = $obj->getArray();
        }
        return isset($obj[$type]);
    }

    public static function is_str($arr)
    {
        return self::check_type($arr, 'string');
    }

    public static function is_var($arr)
    {
        return self::check_type($arr, 'var');
    }

    public static function is_exec($arr)
    {
        return self::check_type($arr, 'exec');
    }

    public static function is_expr($arr)
    {
        return self::check_type($arr, 'op_expr');
    }


    public static function str($string)
    {
        return array("string" => $string);
    }

    public static function num($number)
    {
        return array("number" => $number);
    }

    function stack_size()
    {
        return count($this->stack);
    }

    function append_ast(Haanga_AST $obj)
    {
        $this->end();
        $obj->end();
        $this->stack = array_merge($this->stack, $obj->stack);

        return $this;
    }

    static function constant($str)
    {
        return array('constant' => $str);
    }

    function comment($str)
    {
        $this->stack[] = array("op" => "comment", 'comment' => $str);

        return $this;
    }

    function declare_function($name)
    {
        $this->stack[] = array('op' => 'function', 'name' => $name);

        return $this;
    }

    function do_return($name)
    {
        $this->getValue($name, $expr);
        $this->stack[] = array('op' => 'return', $expr);

        return $this;
    }

    function do_if($expr)
    {
        $this->getValue($expr, $vexpr);
        $this->stack[] = array('op' => 'if', 'expr' => $vexpr);

        return $this;
    }

    function do_else()
    {
        $this->stack[] = array('op' => 'else');

        return $this;
    }

    function do_endif()
    {
        $this->stack[] = array('op' => 'end_if');

        return $this;
    }

    function do_endfunction()
    {
        $this->stack[] = array('op' => 'end_function');

        return $this;
    }

    function v()
    {
        $var = array();
        foreach (func_get_args() as $id => $def) {
            if ($id == 0) {
                $var[$id] = $def;
            } else {
                $this->getValue($def, $value);
                $var[$id] = $value;
            }
        }
        if (count($var) == 1) {
            $var = $var[0];
        }
        $this->current = array('var' => $var);

        return $this;
    }

    final function __get($property)
    {
        $property = strtolower($property);
        if (isset($this->current[$property])) {
            return $this->current[$property];
        }
        return FALSE;
    }

    static function fromArrayGetAST($obj)
    {
        $class = __CLASS__;
        if ($obj InstanceOf $class) {
            return $obj;
        }
        foreach (array('op_expr', 'expr_cond', 'exec', 'var', 'string', 'number', 'constant') as $type) {
            if (isset($obj[$type])) {
                $nobj = new $class;
                $nobj->stack[] = $obj;
                return $nobj;
            }
        }
    }

    static function getValue($obj, &$value, $get_all=FALSE)
    {
        $class = __CLASS__;

        if ($obj InstanceOf $class) {
            $value = $obj->getArray($get_all);
        } else if (is_string($obj)) {
            $value = self::str($obj);
        } else if (is_numeric($obj) or $obj === 0) {
            $value = self::num($obj);
        } else if ($obj === FALSE) {
            $value = array('expr' => FALSE);
        } else if ($obj === TRUE) {
            $value = array('expr' => TRUE);
        } else if (is_array($obj)) {
            foreach (array('op_expr', 'exec', 'var', 'string', 'number', 'constant') as $type) {
                if (isset($obj[$type])) {
                    $value = $obj;
                    return;
                }
            }
            $h     = hcode()->arr();
            $first = 0;
            foreach($obj as $key => $value) {
                if ($key === $first) {
                    $key = NULL;
                    $first++;
                }
                $h->element($key, $value);
            }
            $value = $h->getArray();
        } else if ($obj === NULL) {
            $value = array();
        } else {
            var_Dump($obj);
            throw new Exception("Imposible to get the value of the object");
        }
    }

    function getArray($get_all=FALSE)
    {
        $this->end();
        if ($get_all) {
            return $this->stack;
        }
        return isset($this->stack[0]) ?  $this->stack[0] : NULL;
    }

    function do_for($index, $min, $max, $step, Haanga_AST $body)
    {
        $def = array(
            'op'    => 'for',
            'index' => $index,
            'min'   => $min,
            'max'   => $max,
            'step'  => $step,
        );

        $this->stack[] = $def;
        $this->stack   = array_merge($this->stack, $body->getArray(TRUE));
        $this->stack[] = array('op' => 'end_for');

        return $this;
    }

    function do_foreach($array, $value, $key, Haanga_AST $body)
    {
        foreach (array('array', 'value', 'key') as $var) {
            if ($$var === NULL) {
                continue;
            }
            $var1 = & $$var;
            if (is_string($var1)) {
                $var1 = hvar($var1);
            }
            if (is_object($var1)) {
                $var1 = $var1->getArray();
            }
            if (empty($var1['var'])) {
                throw new Exception("Can't iterate, apparently $var isn't a variable");
            }
            $var1 = $var1['var'];
        }
        $def = array('op' => 'foreach', 'array' => $array, 'value' => $value);
        if ($key) {
            $def['key'] = $key;
        }
        $this->stack[] = $def;
        $this->stack   = array_merge($this->stack, $body->getArray(TRUE));
        $this->stack[] = array('op' => 'end_foreach');

        return $this;
    }

    function do_echo($stmt)
    {
        $this->getValue($stmt, $value);
        $this->stack[] = array('op' => 'print', $value);
        return $this;
    }

    function do_global($array)
    {
        $this->stack[] = array('op' => 'global',  'vars' => $array);

        return $this;
    }

    function do_exec()
    {
        $params = func_get_args();
        $exec   = call_user_func_array('hexec', $params);
        $this->stack[] = array('op' => 'expr', $exec->getArray());

        return $this;
    }

    function exec($function)
    {
        $this->current = array('exec' => $function, 'args' => array());
        foreach (func_get_args() as $id => $param) {
            if ($id > 0) {
                $this->param($param);
            }
        }
        return $this;
    }

    function expr($operation, $term1, $term2=NULL)
    {
        $this->getValue($term1, $value1);
        if ($term2 !== NULL) {
            $this->getValue($term2, $value2);
        } else {
            $value2 = NULL;
        }
        $this->current = array('op_expr' => $operation, $value1, $value2);

        return $this;
    }

    function expr_cond($expr, $if_true, $if_false)
    {
        $this->getValue($expr, $vExpr);
        $this->getValue($if_true, $vIfTrue);
        $this->getValue($if_false, $vIfFalse);

        $this->current = array('expr_cond' => $vExpr, 'true' => $vIfTrue, 'false' => $vIfFalse);

        return $this;
    }


    function arr()
    {
        $this->current = array('array' => array());

        return $this;
    }

    function element($key=NULL, $value)
    {
        $last = & $this->current;

        if (!isset($last['array'])) {
            throw new Exception("Invalid call to element()");
        }

        $this->getValue($value, $val);
        if ($key !== NULL) {
            $this->getValue($key, $kval);
            $val = array('key' => array($kval, $val));
        }
        $last['array'][] = $val;
    }

    function decl_raw($name, $value)
    {
        if (is_string($name)) {
            $name = hvar($name);
        }
        $this->getValue($name, $name);
        $array = array('op' => 'declare', 'name' => $name['var']);
        foreach (func_get_args() as $id => $value) {
            if ($id != 0) {
                $array[] = $value;
            }
        }
        $this->stack[] = $array;
        return $this;
    }

    function decl($name, $value)
    {
        if (is_string($name)) {
            $name = hvar($name);
        }
        $this->getValue($name, $name);
        $array = array('op' => 'declare', 'name' => $name['var']);
        foreach (func_get_args() as $id => $value) {
            if ($id != 0) {
                $this->getValue($value, $stmt);
                $array[] = $stmt;
            }
        }
        $this->stack[] = $array;
        return $this;
    }

    function append($name, $value)
    {
        if (is_string($name)) {
            $name = hvar($name);
        }
        $this->getValue($value, $stmt);
        $this->getValue($name, $name);
        $this->stack[] = array('op' => 'append_var', 'name' => $name['var'], $stmt);
        return $this;
    }

    function param($param)
    {
        $last = & $this->current;

        if (!isset($last['exec'])) {
            throw new Exception("Invalid call to param()");
        }
        
        $this->getValue($param, $value);
        $last['args'][] = $value;

        return $this;
    }

    function end()
    {
        if (count($this->current) > 0) {
            $this->stack[] = $this->current;
            $this->current = array();
        }

        return $this;
    }
}

function hcode()
{
    return new Haanga_AST;
}

function hexpr($term1, $op='expr', $term2=NULL, $op2=NULL)
{
    $code = hcode();
    switch ($op2) {
    case '+':
    case '-':
    case '/':
    case '*':
    case '%':
    case '||':
    case '&&':
    case '<':
    case '>':
    case '<=':
    case '>=':
    case '==':
    case '!=':
        /* call recursive to resolve term2 */
        $args = func_get_args();
        $term2 = call_user_func_array('hexpr', array_slice($args, 2));
        break;
    }
    return $code->expr($op, $term1, $term2);
}

function hexpr_cond($expr, $if_true, $if_false)
{
    $code = hcode();
    $code->expr_cond($expr, $if_true, $if_false);

    return $code;
}

function hexec()
{
    $code = hcode();
    $args = func_get_args();
    return call_user_func_array(array($code, 'exec'), $args);
}

function hconst($str)
{
    return Haanga_AST::Constant($str);
}

// hvar() {{{
/**
 *  Create the representation of a variable
 *
 *  @return Haanga_AST
 */
function hvar()
{
    $args = func_get_args();
    return hvar_ex($args);
}

function hvar_ex($args)
{
    $code = hcode();
    return call_user_func_array(array($code, 'v'), $args);
}
// }}}

/*
 * Local variables:
 * tab-width: 4
 * c-basic-offset: 4
 * End:
 * vim600: sw=4 ts=4 fdm=marker
 * vim<600: sw=4 ts=4
 */
