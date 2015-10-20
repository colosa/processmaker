<?php

/** @class: InputFilter (PHP4 & PHP5, with comments)
  * @project: PHP Input Filter
  * @date: 10-05-2005
  * @version: 1.2.2_php4/php5
  * @author: Daniel Morris
  * @contributors: Gianpaolo Racca, Ghislain Picard, Marco Wandschneider, Chris Tobin and Andrew Eddie.
  * @copyright: Daniel Morris
  * @email: dan@rootcube.com
  * @license: GNU General Public License (GPL)
  */
class InputFilter
{
    public $tagsArray;// default = empty array
    public $attrArray;// default = empty array

    public $tagsMethod;// default = 0
    public $attrMethod;// default = 0

    public $xssAuto;           // default = 1
    public $tagBlacklist = array('applet', 'body', 'bgsound', 'base', 'basefont', 'embed', 'frame', 'frameset', 'head', 'html', 'id', 'iframe', 'ilayer', 'layer', 'link', 'meta', 'name', 'object', 'script', 'style', 'title', 'xml');
    public $attrBlacklist = array('action', 'background', 'codebase', 'dynsrc', 'lowsrc');  // also will strip ALL event handlers

    /** 
      * Constructor for inputFilter class. Only first parameter is required.
      * @access constructor
      * @param Array $tagsArray - list of user-defined tags
      * @param Array $attrArray - list of user-defined attributes
      * @param int $tagsMethod - 0= allow just user-defined, 1= allow all but user-defined
      * @param int $attrMethod - 0= allow just user-defined, 1= allow all but user-defined
      * @param int $xssAuto - 0= only auto clean essentials, 1= allow clean blacklisted tags/attr
      */
    public function inputFilter($tagsArray = array(), $attrArray = array(), $tagsMethod = 0, $attrMethod = 0, $xssAuto = 1)
    {
        // make sure user defined arrays are in lowercase
        for ($i = 0; $i < count($tagsArray); $i++) {
            $tagsArray[$i] = strtolower($tagsArray[$i]);
        }
        for ($i = 0; $i < count($attrArray); $i++) {
            $attrArray[$i] = strtolower($attrArray[$i]);
        }
        // assign to member vars
        $this->tagsArray = (array) $tagsArray;
        $this->attrArray = (array) $attrArray;
        $this->tagsMethod = $tagsMethod;
        $this->attrMethod = $attrMethod;
        $this->xssAuto = $xssAuto;
    }

    /** 
      * Method to be called by another php script. Processes for XSS and specified bad code.
      * @access public
      * @param Mixed $source - input string/array-of-string to be 'cleaned'
      * @return String $source - 'cleaned' version of input parameter
      */
    public function process($source)
    {
        // clean all elements in this array
        if (is_array($source)) {
            foreach ($source as $key => $value) {
                // filter element for XSS and other 'bad' code etc.
                if (is_string($value)) {
                    $source[$key] = $this->remove($this->decode($value));
                }
            }
            return $source;
            // clean this string
        } elseif (is_string($source)) {
            // filter source for XSS and other 'bad' code etc.
            return $this->remove($this->decode($source));
        } else {
            // return parameter as given
            return $source;
        }
    }

    /** 
      * Internal method to iteratively remove all unwanted tags and attributes
      * @access protected
      * @param String $source - input string to be 'cleaned'
      * @return String $source - 'cleaned' version of input parameter
      */
    public function remove($source)
    {
        $loopCounter=0;
        // provides nested-tag protection
        while ($source != $this->filterTags($source)) {
            $source = $this->filterTags($source);
            $loopCounter++;
        }
        return $source;
    }

    /** 
      * Internal method to strip a string of certain tags
      * @access protected
      * @param String $source - input string to be 'cleaned'
      * @return String $source - 'cleaned' version of input parameter
      */
    public function filterTags($source)
    {
        // filter pass setup
        $preTag = null;
        $postTag = $source;
        // find initial tag's position
        $tagOpen_start = strpos($source, '<');
        // interate through string until no tags left
        while ($tagOpen_start !== false) {
            // process tag interatively
            $preTag .= substr($postTag, 0, $tagOpen_start);
            $postTag = substr($postTag, $tagOpen_start);
            $fromTagOpen = substr($postTag, 1);
            // end of tag
            $tagOpen_end = strpos($fromTagOpen, '>');
            if ($tagOpen_end === false) {
                break;
            }
            // next start of tag (for nested tag assessment)
            $tagOpen_nested = strpos($fromTagOpen, '<');
            if (($tagOpen_nested !== false) && ($tagOpen_nested < $tagOpen_end)) {
                $preTag .= substr($postTag, 0, ($tagOpen_nested+1));
                $postTag = substr($postTag, ($tagOpen_nested+1));
                $tagOpen_start = strpos($postTag, '<');
                continue;
            }
            $tagOpen_nested = (strpos($fromTagOpen, '<') + $tagOpen_start + 1);
            $currentTag = substr($fromTagOpen, 0, $tagOpen_end);
            $tagLength = strlen($currentTag);
            if (!$tagOpen_end) {
                $preTag .= $postTag;
                $tagOpen_start = strpos($postTag, '<');
            }
            // iterate through tag finding attribute pairs - setup
            $tagLeft = $currentTag;
            $attrSet = array();
            $currentSpace = strpos($tagLeft, ' ');
            // is end tag
            if (substr($currentTag, 0, 1) == "/") {
                $isCloseTag = true;
                list($tagName) = explode(' ', $currentTag);
                $tagName = substr($tagName, 1);
                // is start tag
            } else {
                $isCloseTag = false;
                list($tagName) = explode(' ', $currentTag);
            }
            // excludes all "non-regular" tagnames OR no tagname OR remove if xssauto is on and tag is blacklisted
            if ((!preg_match("/^[a-z][a-z0-9]*$/i",$tagName)) || (!$tagName) || ((in_array(strtolower($tagName), $this->tagBlacklist)) && ($this->xssAuto))) {
                $postTag = substr($postTag, ($tagLength + 2));
                $tagOpen_start = strpos($postTag, '<');
                // don't append this tag
                continue;
            }
            // this while is needed to support attribute values with spaces in!
            while ($currentSpace !== false) {
                $fromSpace = substr($tagLeft, ($currentSpace+1));
                $nextSpace = strpos($fromSpace, ' ');
                $openQuotes = strpos($fromSpace, '"');
                $closeQuotes = strpos(substr($fromSpace, ($openQuotes+1)), '"') + $openQuotes + 1;
                // another equals exists
                if (strpos($fromSpace, '=') !== false) {
                    // opening and closing quotes exists
                    if (($openQuotes !== false) && (strpos(substr($fromSpace, ($openQuotes+1)), '"') !== false)) {
                        $attr = substr($fromSpace, 0, ($closeQuotes+1));
                    } else {
                        // one or neither exist
                        $attr = substr($fromSpace, 0, $nextSpace);
                    }
                    // no more equals exist
                } else {
                    $attr = substr($fromSpace, 0, $nextSpace);
                }
                // last attr pair
                if (!$attr) {
                    $attr = $fromSpace;
                }
                // add to attribute pairs array
                $attrSet[] = $attr;
                // next inc
                $tagLeft = substr($fromSpace, strlen($attr));
                $currentSpace = strpos($tagLeft, ' ');
            }
            // appears in array specified by user
            $tagFound = in_array(strtolower($tagName), $this->tagsArray);
            // remove this tag on condition
            if ((!$tagFound && $this->tagsMethod) || ($tagFound && !$this->tagsMethod)) {
                // reconstruct tag with allowed attributes
                if (!$isCloseTag) {
                    $attrSet = $this->filterAttr($attrSet);
                    $preTag .= '<' . $tagName;
                    for ($i = 0; $i < count($attrSet); $i++) {
                        $preTag .= ' ' . $attrSet[$i];
                    }
                    // reformat single tags to XHTML
                    if (strpos($fromTagOpen, "</" . $tagName)) {
                        $preTag .= '>';
                    } else {
                        $preTag .= ' />';
                    }
                    // just the tagname
                } else {
                    $preTag .= '</' . $tagName . '>';
                }
            }
            // find next tag's start
            $postTag = substr($postTag, ($tagLength + 2));
            $tagOpen_start = strpos($postTag, '<');
        }
        // append any code after end of tags
        $preTag .= $postTag;
        return $preTag;
    }

    /** 
      * Internal method to strip a tag of certain attributes
      * @access protected
      * @param Array $attrSet
      * @return Array $newSet
      */
    public function filterAttr($attrSet)
    {
        $newSet = array();
        // process attributes
        for ($i = 0; $i <count($attrSet); $i++) {
            // skip blank spaces in tag
            if (!$attrSet[$i]) {
                continue;
            }
            // split into attr name and value
            $attrSubSet = explode('=', trim($attrSet[$i]));
            list($attrSubSet[0]) = explode(' ', $attrSubSet[0]);
            // removes all "non-regular" attr names AND also attr blacklisted
            if ((!eregi("^[a-z]*$",$attrSubSet[0])) || (($this->xssAuto) && ((in_array(strtolower($attrSubSet[0]), $this->attrBlacklist)) || (substr($attrSubSet[0], 0, 2) == 'on')))) {
                continue;
            }
            // xss attr value filtering
            if ($attrSubSet[1]) {
                // strips unicode, hex, etc
                $attrSubSet[1] = str_replace('&#', '', $attrSubSet[1]);
                // strip normal newline within attr value
                $attrSubSet[1] = preg_replace('/\s+/', '', $attrSubSet[1]);
                // strip double quotes
                $attrSubSet[1] = str_replace('"', '', $attrSubSet[1]);
                // [requested feature] convert single quotes from either side to doubles (Single quotes shouldn't be used to pad attr value)
                if ((substr($attrSubSet[1], 0, 1) == "'") && (substr($attrSubSet[1], (strlen($attrSubSet[1]) - 1), 1) == "'")) {
                    $attrSubSet[1] = substr($attrSubSet[1], 1, (strlen($attrSubSet[1]) - 2));
                }
                // strip slashes
                $attrSubSet[1] = stripslashes($attrSubSet[1]);
            }
            // auto strip attr's with "javascript:
            if (((strpos(strtolower($attrSubSet[1]), 'expression') !== false) &&(strtolower($attrSubSet[0]) == 'style')) ||
                    (strpos(strtolower($attrSubSet[1]), 'javascript:') !== false) ||
                    (strpos(strtolower($attrSubSet[1]), 'behaviour:') !== false) ||
                    (strpos(strtolower($attrSubSet[1]), 'vbscript:') !== false) ||
                    (strpos(strtolower($attrSubSet[1]), 'mocha:') !== false) ||
                    (strpos(strtolower($attrSubSet[1]), 'livescript:') !== false)
            ) {
                continue;
            }

            // if matches user defined array
            $attrFound = in_array(strtolower($attrSubSet[0]), $this->attrArray);
            // keep this attr on condition
            if ((!$attrFound && $this->attrMethod) || ($attrFound && !$this->attrMethod)) {
                // attr has value
                if ($attrSubSet[1]) {
                    $newSet[] = $attrSubSet[0] . '="' . $attrSubSet[1] . '"';
                } elseif ($attrSubSet[1] == "0") {
                    // attr has decimal zero as value
                    $newSet[] = $attrSubSet[0] . '="0"';
                } else {
                    // reformat single attributes to XHTML
                    $newSet[] = $attrSubSet[0] . '="' . $attrSubSet[0] . '"';
                }
            }
        }
        return $newSet;
    }

    /** 
      * Try to convert to plaintext
      * @access protected
      * @param String $source
      * @return String $source
      */
    public function decode($source)
    {
        // url decode
        $source = html_entity_decode($source, ENT_QUOTES, "ISO-8859-1");
        // convert decimal
        $source = preg_replace('/&#(\d+);/me',"chr(\\1)", $source);// decimal notation
        // convert hex
        $source = preg_replace('/&#x([a-f0-9]+);/mei',"chr(0x\\1)", $source);// hex notation
        return $source;
    }

    /** 
      * Method to be called by another php script. Processes for SQL injection
      * @access public
      * @param Mixed $source - input string/array-of-string to be 'cleaned'
      * @param Buffer $connection - An open MySQL connection
      * @return String $source - 'cleaned' version of input parameter
      */
    public function safeSQL($source, &$connection)
    {
        // clean all elements in this array
        if (is_array($source)) {
            foreach ($source as $key => $value) {
                // filter element for SQL injection
                if (is_string($value)) {
                    $source[$key] = $this->quoteSmart($this->decode($value), $connection);
                }
            }
            return $source;
            // clean this string
        } elseif (is_string($source)) {
            // filter source for SQL injection
            if (is_string($source)) {
                return $this->quoteSmart($this->decode($source), $connection);
            }
            // return parameter as given
        } else {
            return $source;
        }
    }

    /** 
      * @author Chris Tobin
      * @author Daniel Morris
      * @access protected
      * @param String $source
      * @param Resource $connection - An open MySQL connection
      * @return String $source
      */
    public function quoteSmart($source, &$connection)
    {
        // strip slashes
        if (get_magic_quotes_gpc()) {
            $source = stripslashes($source);
        }
        // quote both numeric and text
        $source = $this->escapeString($source, $connection);
        return $source;
    }

    /** 
      * @author Chris Tobin
      * @author Daniel Morris
      * @access protected
      * @param String $source
      * @param Resource $connection - An open MySQL connection
      * @return String $source
      */
    public function escapeString($string, &$connection)
    {
        // depreciated function
        if (version_compare(phpversion(),"4.3.0", "<")) {
            mysql_escape_string($string);
        } else {
            // current function
            mysql_real_escape_string($string);
        }
        return $string;
    }
    
    /** 
      * Internal method removes tags/special characters
      * @author Marcelo Cuiza
      * @access protected
      * @param Array or String $input
      * @param String $type
      * @return Array or String $input
      */
    public function xssFilter($input, $type = "")
    {
        if(is_array($input)) {
            if(sizeof($input)) {
                foreach($input as $i => $val) {
                    if(is_array($val) && sizeof($val)) {
                        $input[$i] = $this->xssFilter($val);
                    } else {
                        if(!empty($val)) {
                            if($type != "url") {
                                $inputFiltered = addslashes(htmlspecialchars(filter_var($val, FILTER_SANITIZE_STRING), ENT_COMPAT, 'UTF-8'));
                            } else {
                                $inputFiltered = filter_var($val, FILTER_SANITIZE_STRING);
                            }
                        } else {
                            $inputFiltered = "";
                        }
                        $input[$i] = $inputFiltered;
                    }
                }
            }    
            return $input;
        } else {
            if(!isset($input) || trim($input) === '' || $input === NULL ) {
                return '';
            } else {
                if($type != "url") {
                    return addslashes(htmlspecialchars(filter_var($input, FILTER_SANITIZE_STRING), ENT_COMPAT, 'UTF-8'));
                } else {
                    return filter_var($input, FILTER_SANITIZE_STRING);
                }
            }
        }
    }
    
    /** 
      * Internal method: remove malicious code, fix missing end tags, fix illegal nesting, convert deprecated tags, validate CSS, preserve rich formatting 
      * @author Marcelo Cuiza
      * @access protected
      * @param Array or String $input
      * @param String $type (url)
      * @return Array or String $input
      */
    function xssFilterHard($input, $type = "")
    { 
        require_once (PATH_THIRDPARTY . 'HTMLPurifier/HTMLPurifier.auto.php'); 
        $config = HTMLPurifier_Config::createDefault();
        $purifier = new HTMLPurifier($config);
        if(is_array($input)) {
            if(sizeof($input)) {
                foreach($input as $i => $val) { 
                    if(is_array($val) || is_object($val) && sizeof($val)) {
                        $input[$i] = $this->xssFilterHard($val);
                    } else {
                        if(!empty($val)) {
                            if(!is_object(G::json_decode($val))) {
                                $inputFiltered = $purifier->purify($val);
                                if($type != "url" && !strpos(basename($val), "=")) {
                                    $inputFiltered = htmlspecialchars($inputFiltered, ENT_NOQUOTES, 'UTF-8');   
                                } else {
                                    $inputFiltered = str_replace('&amp;','&',$inputFiltered);
                                }
                            } else {
                                $jsArray = G::json_decode($val,true);  
                                if(is_array($jsArray) && sizeof($jsArray)) {
                                    foreach($jsArray as $j => $jsVal){
                                        if(is_array($jsVal) && sizeof($jsVal)) {
                                            $jsArray[$j] = $this->xssFilterHard($jsVal);
                                        } else {
                                            if(!empty($jsVal)) {
                                                $jsArray[$j] = $purifier->purify($jsVal);
                                            }
                                        }
                                    }
                                    $inputFiltered = G::json_encode($jsArray);
                                } else {
                                    $inputFiltered = $val;
                                }
                            }    
                        } else {
                            $inputFiltered = "";
                        }
                        $input[$i] = $inputFiltered;
                    }
                }
            }
            return $input;
        } else {
            if(!isset($input) || empty($input)) {
                return '';
            } else {
                if(is_object($input)) {
                    if(sizeof($input)) {
                        foreach($input as $j => $jsVal){
                            if(is_array($jsVal) || is_object($jsVal) && sizeof($jsVal)) {
                                $input->j = $this->xssFilterHard($jsVal);
                            } else {
                                if(!empty($jsVal)) {
                                    $input->j = $purifier->purify($jsVal);
                                }
                            }
                        }
                    }
                    return $input;
                }
                if(!is_object(G::json_decode($input))) {
                    $input = $purifier->purify($input);
                    if($type != "url" && !strpos(basename($input), "=")) {
                        $input = addslashes(htmlspecialchars($input, ENT_COMPAT, 'UTF-8'));
                    } else {
                        $input = str_replace('&amp;','&',$input);
                    }
                } else {
                    $jsArray = G::json_decode($input,true);
                    if(is_array($jsArray) && sizeof($jsArray)) {
                        foreach($jsArray as $j => $jsVal){
                            if(is_array($jsVal) || is_object($jsVal) && sizeof($jsVal)) {
                                $jsArray[$j] = $this->xssFilterHard($jsVal);
                            } else {
                                if(!empty($jsVal)) {
                                    $jsArray[$j] = $purifier->purify($jsVal);
                                }
                            }
                        }
                        $input = G::json_encode($jsArray);
                    }
                }        
                return $input;
            }
        }
    }
    
    /** 
      * Internal method: protect against SQL injection 
      * @author Marcelo Cuiza
      * @access protected
      * @param String $con
      * @param String $query
      * @param Array $values
      * @return String $query
      */
    function preventSqlInjection($query, $values = Array(), $con = NULL)
    {
        if(is_array($values) && sizeof($values)) {
            foreach($values as $k1 => $val1) {
                    $values[$k1] = mysql_real_escape_string($val1);
            }
            
            if ( get_magic_quotes_gpc() ) {
                foreach($values as $k => $val) {
                    $values[$k] = stripslashes($val);
                }
            }
            $newquery = vsprintf($query,$values);
        } else {
            //$newquery = mysql_real_escape_string($query);
            $newquery = $this->quoteSmart($this->decode($query), $con);
        }
        return $newquery;
    }
    
    /** 
      * Internal method: validate user input 
      * @author Marcelo Cuiza
      * @access protected
      * @param String $value (required)
      * @param Array or String $types ( string | int | float | boolean | path | nosql )
      * @param String $valType ( validate | sanitize )
      * @return String $value
      */
    function validateInput($value, $types = 'string', $valType = 'sanitize')
    {
        if(!isset($value) || empty($value)) {
            return '';
        } 
        
        if(is_array($types) && sizeof($types)){
            foreach($types as $type){
                if($valType == 'sanitize') {
                    $value = $this->sanitizeInputValue($value, $type);
                } else {
                    $value = $this->validateInputValue($value, $type);        
                }
            }    
        } elseif(is_string($types)) {
            if($types == 'sanitize' || $types == 'validate') {
                $valType = $types;
                $types = 'string';
            }
            if($valType == 'sanitize') {
                $value = $this->sanitizeInputValue($value, $types);
            } else {
                $value = $this->validateInputValue($value, $types);        
            }
        }
         
        return $value;
    }
    
    function sanitizeInputValue($value, $type) {
        
        switch($type) {
            case 'float':
                $value = filter_var($value, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION | FILTER_FLAG_ALLOW_THOUSAND);
            break;
            case 'int':
                $value = (int)filter_var($value, FILTER_SANITIZE_NUMBER_INT);
            break;
            case 'boolean':
                $value = (boolean)filter_var($value, FILTER_VALIDATE_BOOLEAN,FILTER_NULL_ON_FAILURE);
            break;
            case 'path':
                if(!file_exists($value)) {
                    if(!is_dir($value)) {
                        $value = '';
                    }
                }
            break;
            case 'nosql':
                $value = (string)filter_var($value, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH);
                if(preg_match('/\b(or|and|xor|drop|insert|update|delete|select)\b/i' , $value, $matches, PREG_OFFSET_CAPTURE)) {
                       $value = substr($value,0,$matches[0][1]);
                }
            break;
            default:
                $value = (string)filter_var($value, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);
        }
        
        return $value;    
    } 
    
   
    function validateInputValue($value, $type) {
        
        switch($type) {
            case 'float':
                $value = str_replace(',', '.', $value);
                if(!filter_var($value, FILTER_VALIDATE_FLOAT)) {
                    throw new Exception('not a float value'); 
                }
            break;
            case 'int':
                if(!filter_var($value, FILTER_VALIDATE_INT)) {
                    throw new Exception('not a int value');    
                }
            break;
            case 'boolean':
                if(!preg_match('/\b(yes|no|false|true|1|0)\b/i' , $value)) {
                    throw new Exception('not a boolean value');
                }
            break;
            case 'path':
                if(!file_exists($value)) {
                    if(!is_dir($value)) {
                        throw new Exception('not a valid path');
                    }
                }
            break;
            case 'nosql':
                if(preg_match('/\b(or|and|xor|drop|insert|update|delete|select)\b/i' , $value)) {
                    throw new Exception('sql command found');
                }
            break;
            default:
                if(!is_string($value)) {
                    throw new Exception('not a string value');
                }
        }
     
 
    } 
}
