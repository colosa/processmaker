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

class HG_Parser Extends Haanga_Compiler_Parser
{
    /* subclass to made easier references to constants */
}


/**
 *  Hand-written Tokenizer class inspired by SQLite's tokenize.c
 *
 */
class Haanga_Compiler_Tokenizer
{
    /* they are case sensitive and sorted! */
    static $keywords = array(
        'AND'           => HG_Parser::T_AND,
        'FALSE'         => HG_Parser::T_FALSE,
        'NOT'           => HG_Parser::T_NOT,
        'OR'            => HG_Parser::T_OR,
        'TRUE'          => HG_Parser::T_TRUE,
        '_('            => HG_Parser::T_INTL,
        'as'            => HG_Parser::T_AS,
        'autoescape'    => HG_Parser::T_AUTOESCAPE,
        'block'         => HG_Parser::T_BLOCK,
        'by'            => HG_Parser::T_BY,
        'else'          => HG_Parser::T_ELSE,
        'empty'         => HG_Parser::T_EMPTY,
        'extends'       => HG_Parser::T_EXTENDS,
        'filter'        => HG_Parser::T_FILTER,
        'for'           => HG_Parser::T_FOR,
        'if'            => HG_Parser::T_IF,
        'ifchanged'     => HG_Parser::T_IFCHANGED,
        'ifequal'       => HG_Parser::T_IFEQUAL,
        'ifnotequal'    => HG_Parser::T_IFNOTEQUAL,
        'in'            => HG_Parser::T_IN,
        'include'       => HG_Parser::T_INCLUDE,
        'load'          => HG_Parser::T_LOAD,
        'not'           => HG_Parser::T_NOT,
        'regroup'       => HG_Parser::T_REGROUP,
        'set'           => HG_Parser::T_SET,
        'spacefull'     => HG_Parser::T_SPACEFULL,
        'step'          => HG_Parser::T_STEP,
        'with'          => HG_Parser::T_WITH,
    );

    /* common operations */
    static $operators_single = array(
        '!'     => HG_Parser::T_NOT,
        '%'     => HG_Parser::T_MOD,
        '&'     => HG_Parser::T_BITWISE,
        '('     => HG_Parser::T_LPARENT,
        ')'     => HG_Parser::T_RPARENT,
        '*'     => HG_Parser::T_TIMES,
        '+'     => HG_Parser::T_PLUS,
        ','     => HG_Parser::T_COMMA,
        '-'     => HG_Parser::T_MINUS,
        '.'     => HG_Parser::T_DOT,
        '/'     => HG_Parser::T_DIV, 
        ':'     => HG_Parser::T_COLON, 
        '<'     => HG_Parser::T_LT,
        '='     => HG_Parser::T_ASSIGN,
        '>'     => HG_Parser::T_GT,
        '['     => HG_Parser::T_BRACKETS_OPEN,
        ']'     => HG_Parser::T_BRACKETS_CLOSE,
        '|'     => HG_Parser::T_PIPE,
    );
    static $operators = array(
        '!=='   => HG_Parser::T_NE,
        '!='    => HG_Parser::T_NE,
        '&&'    => HG_Parser::T_AND,
        '->'    => HG_Parser::T_OBJ,
        '..'    => HG_Parser::T_DOTDOT,
        '::'    => HG_Parser::T_CLASS,
        '<<'    => HG_Parser::T_BITWISE,
        '<='    => HG_Parser::T_LE,
        '==='   => HG_Parser::T_EQ,
        '=='    => HG_Parser::T_EQ,
        '>='    => HG_Parser::T_GE,
        '>>'    => HG_Parser::T_BITWISE,
        '||'    => HG_Parser::T_OR,
    );

    static $close_tags = array();

    static $open_tag     = "{%";
    static $end_tag      = "%}";
    static $open_comment = "{#";
    static $end_comment  = "#}";
    static $open_print   = "{{";
    static $end_print    = "}}";

    public $open_tags;
    public $value;
    public $token;
    public $status = self::IN_NONE;

    const IN_NONE    = 0;
    const IN_HTML    = 1;
    const IN_TAG     = 2;
    const IN_ECHO    = 3;

    function __construct($data, $compiler, $file)
    {
        $this->data     = $data;
        $this->compiler = $compiler;
        $this->line     = 1;
        $this->N        = 0;
        $this->file     = $file;
        $this->length   = strlen($data);


        /*$tmp1 = self::$operators;
        $tmp2 = $tmp1;
        ksort($tmp2);
        var_dump($tmp2, $tmp1 === $tmp2);die();/**/

        self::$close_tags =array(
            self::$end_tag   => HG_Parser::T_TAG_CLOSE,
            self::$end_print => HG_Parser::T_PRINT_CLOSE,
        );


        $this->open_tags = array(
            self::$open_tag     => HG_Parser::T_TAG_OPEN,
            self::$open_print   => HG_Parser::T_PRINT_OPEN,
            self::$open_comment => HG_Parser::T_COMMENT,
        );
    }

    function yylex()
    {
        $this->token = NULL;

        if ($this->length == $this->N) {
            if ($this->status != self::IN_NONE && $this->status != self::IN_HTML) {
                $this->Error("Unexpected end");
            }
            return FALSE;
        }

        if ($this->status == self::IN_NONE) {
            $i    = &$this->N;
            $data = substr($this->data, $i, 12);

            static $lencache = array();
            foreach ($this->open_tags as $value => $token) {
                if (!isset($lencache[$value])) {
                    $lencache[$value] = strlen($value);
                }
                $len = $lencache[$value];
                if (strncmp($data, $value, $len) == 0) {
                    $this->value  = $value;
                    $this->token  = $token;
                    $i += $len;
                    switch ($this->token) {
                    case HG_Parser::T_TAG_OPEN:
                        $this->status = self::IN_TAG;
                        break;
                    case HG_Parser::T_COMMENT:
                        $zdata = & $this->data;

                        if (($pos=strpos($zdata, self::$end_comment, $i)) === FALSE) {
                            $this->error("unexpected end");
                        }

                        $this->value  = substr($zdata, $i, $pos-2);
                        $this->status = self::IN_NONE; 
                        $i = $pos + 2;
                        break;
                    case HG_Parser::T_PRINT_OPEN:
                        $this->status = self::IN_ECHO;
                        break;
                    }
                    return TRUE;
                }
            }

            $this->status = self::IN_HTML;
        }
    
        switch ($this->status)
        {
            case self::IN_TAG:
            case self::IN_ECHO:
                $this->yylex_main();
                break;
            default:
                $this->yylex_html();
        }


        if (empty($this->token)) {
            if ($this->status != self::IN_NONE && $this->status != self::IN_HTML) {
                $this->Error("Unexpected end");
            }
            return FALSE;
        }

        return TRUE;

    }

    function yylex_html()
    {
        $data = &$this->data;
        $i    = &$this->N;

        foreach ($this->open_tags as $value => $status) {
            $pos = strpos($data, $value, $i);
            if ($pos === FALSE) {
                continue;
            }
            if (!isset($lowest_pos) || $lowest_pos > $pos) {
                $lowest_pos = $pos;
            }
        }

        if (isset($lowest_pos)) {
            $this->value  = substr($data, $i, $lowest_pos-$i);
            $this->token  = HG_Parser::T_HTML;
            $this->status = self::IN_NONE;
            $i += $lowest_pos - $i;
        } else {
            $this->value  = substr($data, $i);
            $this->token  = HG_Parser::T_HTML;
            $i = $this->length;
        }

        $this->line += substr_count($this->value, "\n");

    }


    function yylex_main()
    {
        $data = &$this->data;

        for ($i=&$this->N; is_null($this->token) && $i < $this->length; ++$i) {
            switch ($data[$i]) {

            /* strings {{{ */
            case '"':
            case "'":
                $end   = $data[$i];
                $value = "";
                while ($data[++$i] != $end) {
                    switch ($data[$i]) {
                    case "\\":
                        switch ($data[++$i]) {
                        case "n":
                            $value .= "\n";
                            break;
                        case "t":
                            $value .= "\t";
                            break;
                        default:
                            $value .= $data[$i];
                        }
                        break;
                    case $end:
                        --$i;
                        break 2;
                    default:
                        if ($data[$i] == "\n") {
                            $this->line++;
                        }
                        $value .= $data[$i];
                    }
                    if (!isset($data[$i+1])) {
                        $this->Error("unclosed string");
                    }
                }
                $this->value = $value;
                $this->token = HG_Parser::T_STRING;
                break;
            /* }}} */

            /* number {{{ */
            case '0': case '1': case '2': case '3': case '4':
            case '5': case '6': case '7': case '8': case '9': 
                $value = "";
                $dot   = FALSE;
                for ($e=0; $i < $this->length; ++$e, ++$i) {
                    switch ($data[$i]) {
                    case '0': case '1': case '2': case '3': case '4': 
                    case '5': case '6': case '7': case '8': case '9': 
                        $value .= $data[$i];
                        break;
                    case '.':
                        if (!$dot) {
                            $value .= ".";
                            $dot    = TRUE;
                        } else {
                            $this->error("Invalid number");
                        }
                        break;
                    default: 
                        break 2; /* break the main loop */
                    }
                }
                if (!$this->is_token_end($data[$i]) &&
                    !isset(self::$operators_single[$data[$i]]) || $value[$e-1] == '.') {
                    $this->error("Unexpected '{$data[$i]}'");
                }
                $this->value = $value;
                $this->token = HG_Parser::T_NUMERIC;
                break 2;
            /* }}} */

            case "\n": case " ": case "\t": case "\r": case "\f":
                for (; is_null($this->token) && $i < $this->length; ++$i) {
                    switch ($data[$i]) {
                    case "\n":
                        $this->line++;
                    case " ": case "\t": case "\r": case "\f":
                        break;
                    case '.':
                        if ($data[$i+1] != '.') {
                            $this->token = HG_Parser::T_CONCAT;
                            $this->value = '.';
                            $i++;
                            return;
                        }
                    default:
                        /* break main loop */
                        /* and decrease because last processed byte */
                        /* wasn't a dot (T_CONCAT)                  */
                        --$i;  
                        break 2; 
                    }
                }
                break; /* whitespaces are ignored */
            default: 
                if (!$this->getTag() && !$this->getOperator()) {
                    $alpha = $this->getAlpha();
                    if ($alpha === FALSE) {
                        $this->error("error: unexpected ".substr($data, $i));
                    }
                    static $tag=NULL;
                    if (!$tag) {
                        $tag = Haanga_Extension::getInstance('Tag');
                    }
                    $value = $tag->isValid($alpha);
                    $this->token = $value ? $value : HG_Parser::T_ALPHA;
                    $this->value = $alpha;

                }
                break 2;
            }
        }

        if ($this->token == HG_Parser::T_TAG_CLOSE ||
            $this->token == HG_Parser::T_PRINT_CLOSE) {
            $this->status = self::IN_NONE;
        }

    }

    function getTag()
    {
        static $lencache = array();

        $i    = &$this->N;
        $data = substr($this->data, $i, 12);
        foreach (self::$close_tags as $value => $token) {
            if (!isset($lencache[$value])) {
                $lencache[$value] = strlen($value);
            }
            $len = $lencache[$value];
            if (strncmp($data, $value, $len) == 0) {
                $this->token = $token;
                $this->value = $value;
                $i += $len;
                return TRUE;
            }
        }

        foreach (self::$keywords as $value => $token) {
            if (!isset($lencache[$value])) {
                $lencache[$value] = strlen($value);
            }
            $len = $lencache[$value];
            switch (strncmp($data, $value, $len)) {
            case -1:
                break 2;
            case 0: // match 
                if (isset($data[$len]) && !$this->is_token_end($data[$len])) {
                    /* probably a variable name TRUEfoo (and not TRUE) */
                    continue;
                }
                $this->token = $token;
                $this->value = $value;
                $i += $len;
                return TRUE;
            }
        }

        /* /end([a-zA-Z][a-zA-Z0-9]*)/ */
        if (strncmp($data, "end", 3) == 0) {
            $this->value = $this->getAlpha();
            $this->token = HG_Parser::T_CUSTOM_END;
            return TRUE;
        }
        
        return FALSE;
    }

    function Error($text)
    {
        throw new Haanga_Compiler_Exception($text." in ".$this->file.":".$this->line);
    }

    function getOperator()
    {
        static $lencache = array();

        $i    = &$this->N;
        $data = substr($this->data, $i, 12);

        foreach (self::$operators as $value => $token) {
            if (!isset($lencache[$value])) {
                $lencache[$value] = strlen($value);
            }
            $len = $lencache[$value];
            switch (strncmp($data, $value, $len)) {
            case -1:
                if (strlen($data) == $len) {
                    break 2;
                }
                break;
            case 0:
                $this->token = $token;
                $this->value = $value;
                $i += $len;
                return TRUE;
            }
        }

        $data = $this->data[$i];
        foreach (self::$operators_single as $value => $token) {
            if ($value == $data) {
                $this->token = $token;
                $this->value = $value;
                $i += 1;
                return TRUE;
            } else if ($value > $data) {
                break;
            }
        }


        return FALSE;
    }


    /**
     *  Return TRUE if $letter is a valid "token_end". We use token_end
     *  to avoid confuse T_ALPHA TRUEfoo with TRUE and foo (T_ALPHA)
     *
     *  @param string $letter
     *
     *  @return bool
     */
    protected function is_token_end($letter)
    {
        /* [^a-zA-Z0-9_] */
        return !(
            ('a' <= $letter && 'z' >= $letter) ||
            ('A' <= $letter && 'Z' >= $letter) || 
            ('0' <= $letter && '9' >= $letter) || 
            $letter == "_" 
        );
    }

    function getAlpha()
    {
        /* [a-zA-Z_][a-zA-Z0-9_]* */
        $i    = &$this->N;
        $data = &$this->data;

        if (  !('a' <= $data[$i] && 'z' >= $data[$i]) &&
            !('A' <= $data[$i] && 'Z' >= $data[$i]) && $data[$i] != '_') {
            return FALSE;
        }

        $value  = "";
        for (; $i < $this->length; ++$i) {
            if (
                ('a' <= $data[$i] && 'z' >= $data[$i]) ||
                ('A' <= $data[$i] && 'Z' >= $data[$i]) || 
                ('0' <= $data[$i] && '9' >= $data[$i]) || 
                $data[$i] == "_"
            ) {
                $value .= $data[$i];
            } else {
                break;
            }
        }

        return $value;
    }

    function getLine()
    {
        return $this->line;
    }


    static function init($template, $compiler, $file='')
    {
        $lexer  = new Haanga_Compiler_Tokenizer($template, $compiler, $file);
        $parser = new Haanga_Compiler_Parser($lexer, $file);

        $parser->compiler = $compiler;

        try {
            for($i=0; ; $i++) {
                if  (!$lexer->yylex()) {
                    break;
                }
                $parser->doParse($lexer->token, $lexer->value);
            }
        } catch (Exception $e) {
            /* destroy the parser */
            try {
                $parser->doParse(0,0); 
            } catch (Exception $y) {}
            throw $e; /* re-throw exception */
        }

        $parser->doParse(0, 0);

        return (array)$parser->body;

    }
}
