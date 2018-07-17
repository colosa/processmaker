<?php

/**
 * class.pmScript.php
 *
 * @package workflow.engine.ProcessMaker
 *
 * ProcessMaker Open Source Edition
 * Copyright (C) 2004 - 2011 Colosa Inc.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 * For more information, contact Colosa Inc, 2566 Le Jeune Rd.,
 * Coral Gables, FL, 33134, USA, or email info@colosa.com.
 *
 */
////////////////////////////////////////////////////
// Execute and evaluate PMScripts
//
// Copyright (C) 2007 COLOSA
//
// License: LGPL, see LICENSE
////////////////////////////////////////////////////

use ProcessMaker\Plugins\PluginRegistry;

/**
 * PMScript - PMScript class
 *
 * @author Julio Cesar Laura Avenda�o <juliocesar@colosa.com>
 * last modify 2008.08.13 by Erik Amaru Ortiz <erik@colosa.com>
 * last modify comment was added and adapted the catch errors
 * @copyright 2007 COLOSA
 */
function __autoload ($sClassName)
{
    if (defined( 'SYS_SYS' )) {
        $sPath = PATH_DB . SYS_SYS . PATH_SEP . 'classes' . PATH_SEP;
        if (file_exists( $sPath . $sClassName . '.php' )) {
            require_once $sPath . $sClassName . '.php';
        }
    }
}

if (defined('SYS_SYS') && (!defined('PATH_DATA_SITE') || !defined('PATH_WORKSPACE'))) {
    Bootstrap::setConstantsRelatedWs(SYS_SYS);
}

//Add External Triggers
$dir = G::ExpandPath( "classes" ) . 'triggers';
$filesArray = array ();
if (file_exists( $dir )) {
    if ($handle = opendir( $dir )) {
        while (false !== ($file = readdir( $handle ))) {
            if (($file != ".") && ($file != "..")) {
                $extFile = explode( ".", $file );
                if ($extFile[sizeof( $extFile ) - 1] == 'php') {
                    include_once ($dir . PATH_SEP . $file);
                }
            }
        }
        closedir( $handle );
    }
}

/**
 * PMScript - PMScript class
 *
 * @copyright 2007 COLOSA
 * @package workflow.engine.ProcessMaker
 */
class PMScript
{
    /**
     * @var array $dataTrigger
     */
    public $dataTrigger = array();

    /**
     * Original fields
     */
    public $aOriginalFields = array ();

    /**
     * Fields to use
     */
    public $aFields = array ();

    /**
     * Script
     */
    public $sScript = '';

    /**
     * Error has happened?
     */
    public $bError = false;

    /**
     * Affected fields
     */
    public $affected_fields = [];

    public $scriptExecutionTime = 0;

    public $sRegexp = '/\@(?:([\@\%\#\?\$\=\&])([a-zA-Z\_]\w*)|([a-zA-Z\_][\w\-\>\:]*)\(((?:[^\\\\\)]*(?:[\\\\][\w\W])?)*)\))((?:\s*\[[\'"]?\w+[\'"]?\])+|\-\>([a-zA-Z\_]\w*))?/';

    /**
     * Constructor of the class PMScript
     *
     * @return void
     */
    public function PMScript ()
    {
        $this->aFields['__ERROR__'] = 'none';
    }

    /**
     * Set the fields to use
     *
     * @param array $aFields
     * @return void
     */
    public function setFields ($aFields = array())
    {
        if (! is_array( $aFields )) {
            $aFields = array ();
        }
        $this->aOriginalFields = $this->aFields = $aFields;
    }

    /**
     * Set the current script
     *
     * @param string $sScript
     * @return void
     */
    public function setScript ($sScript = '')
    {
        if (!defined("T_ML_COMMENT")) {
            define("T_ML_COMMENT", T_COMMENT);
        } else {
            if (!defined("T_DOC_COMMENT")) {
                define("T_DOC_COMMENT", T_ML_COMMENT);
            }
        }

        $script = "<?php " . $sScript;
        $tokens = token_get_all($script);
        $result = "";

        foreach ($tokens as $token) {
            if (is_string($token)) {
                $result .= $token;
            } else {
                list($id, $text) = $token;

                switch ($id) {
                    case T_OPEN_TAG:
                    case T_CLOSE_TAG:
                    case T_COMMENT:
                    case T_ML_COMMENT:  //we've defined this
                    case T_DOC_COMMENT: //and this
                        if ($text != '<?php ' && $text != '<?php' && $text != '<? ' && $text != '<?' && $text != '<% ' && $text != '<%') {
                            $result .= $text;
                        }
                        break;
                    default:
                        $result .= $text;
                        break;
                }
            }
        }

        $this->sScript = trim($result);
    }

    /**
     * Verify the syntax
     *
     * @param string $sScript
     * @return boolean
     */
    public function validSyntax ($sScript)
    {
        return true;
    }

    /**
     * @param $dataTrigger
     */
    public function setDataTrigger($dataTrigger)
    {
        $this->dataTrigger = is_array($dataTrigger) ? $dataTrigger : array();
    }

    /**
     * @param $sScript
     * @param $sCode
     */
    public function executeAndCatchErrors($sScript, $sCode)
    {
        ob_start('handleFatalErrors');
        set_error_handler('handleErrors');
        $_SESSION['_CODE_'] = $sCode;
        $_SESSION['_DATA_TRIGGER_'] = $this->dataTrigger;
        $_SESSION['_DATA_TRIGGER_']['_EXECUTION_TIME_'] = microtime(true);
        eval($sScript);
        $this->scriptExecutionTime = round(microtime(true) -
            $_SESSION['_DATA_TRIGGER_']['_EXECUTION_TIME_'], 5);
        $this->evaluateVariable();
        ob_end_flush();

        //log trigger execution in processmaker.log
        G::logTriggerExecution($_SESSION, '', '', $this->scriptExecutionTime);
        unset($_SESSION['_CODE_']);
        unset($_SESSION['_DATA_TRIGGER_']);
    }

    /**
     * Execute the current script
     *
     * @return void
     */
    public function execute ()
    {
        $sScript = "";
        $iAux = 0;
        $iOcurrences = preg_match_all( $this->sRegexp, $this->sScript, $aMatch, PREG_PATTERN_ORDER | PREG_OFFSET_CAPTURE );
        if ($iOcurrences) {
            for ($i = 0; $i < $iOcurrences; $i ++) {
                $bEqual = false;
                $sAux = substr( $this->sScript, $iAux, $aMatch[0][$i][1] - $iAux );
                if (! $bEqual) {
                    if (strpos($sAux, "==") !== false || strpos($sAux, "!=") !== false || strpos($sAux, ">") !== false || strpos($sAux, "<") !== false || strpos($sAux, ">=") !== false || strpos($sAux, "<=") !== false || strpos($sAux, "<>") !== false || strpos($sAux, "===") !== false || strpos($sAux, "!==") !== false) {
                        $bEqual = false;
                    } else {
                        if (strpos($sAux, "=") !== false || strpos($sAux, "+=") !== false || strpos($sAux, "-=") !== false || strpos($sAux, "*=") !== false || strpos($sAux, "/=") !== false || strpos($sAux, "%=") !== false || strpos($sAux, ".=") !== false) {
                            $bEqual = true;
                        }
                    }
                }
                if ($bEqual) {
                    if (strpos( $sAux, ';' ) !== false) {
                        $bEqual = false;
                    }
                }
                if ($bEqual) {
                    if (! isset( $aMatch[5][$i][0] )) {
                        eval( "if (!isset(\$this->aFields['" . $aMatch[2][$i][0] . "'])) { \$this->aFields['" . $aMatch[2][$i][0] . "'] = " . ($aMatch[1][$i][0] == "&" ? "new stdclass()" : "null") . "; }" );
                    } else {
                        if ($aMatch[1][$i][0] == "&") {
                            eval( "if (!isset(\$this->aFields['" . $aMatch[2][$i][0] . "'])) { \$this->aFields['" . $aMatch[2][$i][0] . "'] = new stdclass(); }" );
                        }
                        eval( "if (!isset(\$this->aFields" . (isset( $aMatch[2][$i][0] ) ? "['" . $aMatch[2][$i][0] . "']" : '') . $aMatch[5][$i][0] . ")) { \$this->aFields" . (isset( $aMatch[2][$i][0] ) ? "['" . $aMatch[2][$i][0] . "']" : '') . $aMatch[5][$i][0] . " = null; }" );
                    }
                } else {
                    if ($aMatch[1][$i][0] == "&") {
                        eval( "if (!isset(\$this->aFields['" . $aMatch[2][$i][0] . "'])) { \$this->aFields['" . $aMatch[2][$i][0] . "'] = new stdclass(); }" );
                    }
                }
                $sScript .= $sAux;
                $iAux = $aMatch[0][$i][1] + strlen( $aMatch[0][$i][0] );
                switch ($aMatch[1][$i][0]) {
                    case '@':
                        if ($bEqual) {
                            if (! isset( $aMatch[5][$i][0] )) {
                                $sScript .= "pmToString(\$this->aFields['" . $aMatch[2][$i][0] . "'])";
                            } else {
                                $sScript .= "pmToString(\$this->aFields" . (isset( $aMatch[2][$i][0] ) ? "['" . $aMatch[2][$i][0] . "']" : '') . $aMatch[5][$i][0] . ")";
                            }
                        } else {
                            if (! isset( $aMatch[5][$i][0] )) {
                                $sScript .= "\$this->aFields['" . $aMatch[2][$i][0] . "']";
                            } else {
                                $sScript .= "\$this->aFields" . (isset( $aMatch[2][$i][0] ) ? "['" . $aMatch[2][$i][0] . "']" : '') . $aMatch[5][$i][0];
                            }
                        }
                        break;
                    case '%':
                        if ($bEqual) {
                            if (! isset( $aMatch[5][$i][0] )) {
                                $sScript .= "pmToInteger(\$this->aFields['" . $aMatch[2][$i][0] . "'])";
                            } else {
                                $sScript .= "pmToInteger(\$this->aFields" . (isset( $aMatch[2][$i][0] ) ? "['" . $aMatch[2][$i][0] . "']" : '') . $aMatch[5][$i][0] . ")";
                            }
                        } else {
                            if (! isset( $aMatch[5][$i][0] )) {
                                $sScript .= "\$this->aFields['" . $aMatch[2][$i][0] . "']";
                            } else {
                                $sScript .= "\$this->aFields" . (isset( $aMatch[2][$i][0] ) ? "['" . $aMatch[2][$i][0] . "']" : '') . $aMatch[5][$i][0];
                            }
                        }
                        break;
                    case '#':
                        if ($bEqual) {
                            if (! isset( $aMatch[5][$i][0] )) {
                                $sScript .= "pmToFloat(\$this->aFields['" . $aMatch[2][$i][0] . "'])";
                            } else {
                                $sScript .= "pmToFloat(\$this->aFields" . (isset( $aMatch[2][$i][0] ) ? "['" . $aMatch[2][$i][0] . "']" : '') . $aMatch[5][$i][0] . ")";
                            }
                        } else {
                            if (! isset( $aMatch[5][$i][0] )) {
                                $sScript .= "\$this->aFields['" . $aMatch[2][$i][0] . "']";
                            } else {
                                $sScript .= "\$this->aFields" . (isset( $aMatch[2][$i][0] ) ? "['" . $aMatch[2][$i][0] . "']" : '') . $aMatch[5][$i][0];
                            }
                        }
                        break;
                    case '?':
                        if ($bEqual) {
                            if (! isset( $aMatch[5][$i][0] )) {
                                $sScript .= "pmToUrl(\$this->aFields['" . $aMatch[2][$i][0] . "'])";
                            } else {
                                $sScript .= "pmToUrl(\$this->aFields" . (isset( $aMatch[2][$i][0] ) ? "['" . $aMatch[2][$i][0] . "']" : '') . $aMatch[5][$i][0] . ")";
                            }
                        } else {
                            if (! isset( $aMatch[5][$i][0] )) {
                                $sScript .= "\$this->aFields['" . $aMatch[2][$i][0] . "']";
                            } else {
                                $sScript .= "\$this->aFields" . (isset( $aMatch[2][$i][0] ) ? "['" . $aMatch[2][$i][0] . "']" : '') . $aMatch[5][$i][0];
                            }
                        }
                        break;
                    case '$':
                        if ($bEqual) {
                            if (! isset( $aMatch[5][$i][0] )) {
                                $sScript .= "pmSqlEscape(\$this->aFields['" . $aMatch[2][$i][0] . "'])";
                            } else {
                                $sScript .= "pmSqlEscape(\$this->aFields" . (isset( $aMatch[2][$i][0] ) ? "['" . $aMatch[2][$i][0] . "']" : '') . $aMatch[5][$i][0] . ")";
                            }
                        } else {
                            if (! isset( $aMatch[5][$i][0] )) {
                                $sScript .= "\$this->aFields['" . $aMatch[2][$i][0] . "']";
                            } else {
                                $sScript .= "\$this->aFields" . (isset( $aMatch[2][$i][0] ) ? "['" . $aMatch[2][$i][0] . "']" : '') . $aMatch[5][$i][0];
                            }
                        }
                        break;
                    case '=':
                    case '&':
                        if ($bEqual) {
                            if (! isset( $aMatch[5][$i][0] )) {
                                $sScript .= "\$this->aFields['" . $aMatch[2][$i][0] . "']";
                            } else {
                                $sScript .= "\$this->aFields" . (isset( $aMatch[2][$i][0] ) ? "['" . $aMatch[2][$i][0] . "']" : '') . $aMatch[5][$i][0];
                            }
                        } else {
                            if (! isset( $aMatch[5][$i][0] )) {
                                $sScript .= "\$this->aFields['" . $aMatch[2][$i][0] . "']";
                            } else {
                                $sScript .= "\$this->aFields" . (isset( $aMatch[2][$i][0] ) ? "['" . $aMatch[2][$i][0] . "']" : '') . $aMatch[5][$i][0];
                            }
                        }
                        break;
                }
                $this->affected_fields[] = $aMatch[2][$i][0];
            }
        }
        $sScript .= substr( $this->sScript, $iAux );
        $sScript = "try {\n" . $sScript . "\n} catch (Exception \$oException) {\n " . " \$this->aFields['__ERROR__'] = utf8_encode(\$oException->getMessage());\n}";
        //echo '<pre>-->'; print_r($this->aFields); echo '<---</pre>';

        $this->executeAndCatchErrors($sScript, $this->sScript);

        $this->aFields["__VAR_CHANGED__"] = implode(",", $this->affected_fields);
        for ($i = 0; $i < count( $this->affected_fields ); $i ++) {
            $_SESSION['TRIGGER_DEBUG']['DATA'][] = Array ('key' => $this->affected_fields[$i],'value' => isset( $this->aFields[$this->affected_fields[$i]] ) ? $this->aFields[$this->affected_fields[$i]] : ''
            );
        }
        //echo '<pre>-->'; print_r($_SESSION['TRIGGER_DEBUG']['DATA']); echo '<---</pre>';
    }

    /**
     * Evaluate the current script
     *
     * @return boolean
     */
    public function evaluate ()
    {
        $bResult = null;
        $sScript = '';
        $iAux = 0;
        $bEqual = false;
        $variableIsDefined = true;
        $iOcurrences = preg_match_all( $this->sRegexp, $this->sScript, $aMatch, PREG_PATTERN_ORDER | PREG_OFFSET_CAPTURE );
        if ($iOcurrences) {
            for ($i = 0; $i < $iOcurrences; $i ++) {
                // if the variables for that condition has not been previously defined then $variableIsDefined
                // is set to false
                if (!isset($this->aFields[$aMatch[2][$i][0]]) && !isset($aMatch[5][$i][0])) {
                    eval( "if (!isset(\$this->aFields['" . $aMatch[2][$i][0] . "'])) { \$this->aFields['" . $aMatch[2][$i][0] . "'] = " . ($aMatch[1][$i][0] == "&" ? "new stdclass()" : "null") . "; }" );
                } else {
                    if ($aMatch[1][$i][0] == "&") {
                        eval( "if (!isset(\$this->aFields['" . $aMatch[2][$i][0] . "'])) { \$this->aFields['" . $aMatch[2][$i][0] . "'] = new stdclass(); }" );
                    }
                    if (!isset($this->aFields[$aMatch[2][$i][0]])) {
                        eval("\$this->aFields['" . $aMatch[2][$i][0] . "']" . $aMatch[5][$i][0] . " = '';");

                    } else {
                        if (isset($aMatch[5][$i][0])) {
                            eval("if (!isset(\$this->aFields['" . $aMatch[2][$i][0] . "']" . $aMatch[5][$i][0] . ")) {\$this->aFields['" . $aMatch[2][$i][0] . "']" . $aMatch[5][$i][0] . " = '';}");
                        } else {
                            eval("if (!isset(\$this->aFields['" . $aMatch[2][$i][0] . "'])) {\$this->aFields['" . $aMatch[2][$i][0] . "'] = " . ($aMatch[1][$i][0] == "&" ? "new stdclass()" : "''") . ";}");
                        }
                    }
                }
                $sAux = substr( $this->sScript, $iAux, $aMatch[0][$i][1] - $iAux );
                if (! $bEqual) {
                    if (strpos( $sAux, '=' ) !== false) {
                        $bEqual = true;
                    }
                }
                if ($bEqual) {
                    if (strpos( $sAux, ';' ) !== false) {
                        $bEqual = false;
                    }
                }
                $sScript .= $sAux;
                $iAux = $aMatch[0][$i][1] + strlen( $aMatch[0][$i][0] );
                switch ($aMatch[1][$i][0]) {
                    case '@':
                        if ($bEqual) {
                            if (! isset( $aMatch[5][$i][0] )) {
                                $sScript .= "pmToString(\$this->aFields['" . $aMatch[2][$i][0] . "'])";
                            } else {
                                $sScript .= "pmToString(\$this->aFields" . (isset( $aMatch[2][$i][0] ) ? "['" . $aMatch[2][$i][0] . "']" : '') . $aMatch[5][$i][0] . ")";
                            }
                        } else {
                            if (! isset( $aMatch[5][$i][0] )) {
                                $sScript .= "\$this->aFields['" . $aMatch[2][$i][0] . "']";
                            } else {
                                $sScript .= "\$this->aFields" . (isset( $aMatch[2][$i][0] ) ? "['" . $aMatch[2][$i][0] . "']" : '') . $aMatch[5][$i][0];
                            }
                        }
                        break;
                    case '%':
                        if ($bEqual) {
                            if (! isset( $aMatch[5][$i][0] )) {
                                $sScript .= "pmToInteger(\$this->aFields['" . $aMatch[2][$i][0] . "'])";
                            } else {
                                $sScript .= "pmToInteger(\$this->aFields" . (isset( $aMatch[2][$i][0] ) ? "['" . $aMatch[2][$i][0] . "']" : '') . $aMatch[5][$i][0] . ")";
                            }
                        } else {
                            if (! isset( $aMatch[5][$i][0] )) {
                                $sScript .= "\$this->aFields['" . $aMatch[2][$i][0] . "']";
                            } else {
                                $sScript .= "\$this->aFields" . (isset( $aMatch[2][$i][0] ) ? "['" . $aMatch[2][$i][0] . "']" : '') . $aMatch[5][$i][0];
                            }
                        }
                        break;
                    case '#':
                        if ($bEqual) {
                            if (! isset( $aMatch[5][$i][0] )) {
                                $sScript .= "pmToFloat(\$this->aFields['" . $aMatch[2][$i][0] . "'])";
                            } else {
                                $sScript .= "pmToFloat(\$this->aFields" . (isset( $aMatch[2][$i][0] ) ? "['" . $aMatch[2][$i][0] . "']" : '') . $aMatch[5][$i][0] . ")";
                            }
                        } else {
                            if (! isset( $aMatch[5][$i][0] )) {
                                $sScript .= "\$this->aFields['" . $aMatch[2][$i][0] . "']";
                            } else {
                                $sScript .= "\$this->aFields" . (isset( $aMatch[2][$i][0] ) ? "['" . $aMatch[2][$i][0] . "']" : '') . $aMatch[5][$i][0];
                            }
                        }
                        break;
                    case '?':
                        if ($bEqual) {
                            if (! isset( $aMatch[5][$i][0] )) {
                                $sScript .= "pmToUrl(\$this->aFields['" . $aMatch[2][$i][0] . "'])";
                            } else {
                                $sScript .= "pmToUrl(\$this->aFields" . (isset( $aMatch[2][$i][0] ) ? "['" . $aMatch[2][$i][0] . "']" : '') . $aMatch[5][$i][0] . ")";
                            }
                        } else {
                            if (! isset( $aMatch[5][$i][0] )) {
                                $sScript .= "\$this->aFields['" . $aMatch[2][$i][0] . "']";
                            } else {
                                $sScript .= "\$this->aFields" . (isset( $aMatch[2][$i][0] ) ? "['" . $aMatch[2][$i][0] . "']" : '') . $aMatch[5][$i][0];
                            }
                        }
                        break;
                    case '$':
                        if ($bEqual) {
                            if (! isset( $aMatch[5][$i][0] )) {
                                $sScript .= "pmSqlEscape(\$this->aFields['" . $aMatch[2][$i][0] . "'])";
                            } else {
                                $sScript .= "pmSqlEscape(\$this->aFields" . (isset( $aMatch[2][$i][0] ) ? "['" . $aMatch[2][$i][0] . "']" : '') . $aMatch[5][$i][0] . ")";
                            }
                        } else {
                            if (! isset( $aMatch[5][$i][0] )) {
                                $sScript .= "\$this->aFields['" . $aMatch[2][$i][0] . "']";
                            } else {
                                $sScript .= "\$this->aFields" . (isset( $aMatch[2][$i][0] ) ? "['" . $aMatch[2][$i][0] . "']" : '') . $aMatch[5][$i][0];
                            }
                        }
                        break;
                    case '=':
                    case '&':
                        if ($bEqual) {
                            if (! isset( $aMatch[5][$i][0] )) {
                                $sScript .= "\$this->aFields['" . $aMatch[2][$i][0] . "']";
                            } else {
                                $sScript .= "\$this->aFields" . (isset( $aMatch[2][$i][0] ) ? "['" . $aMatch[2][$i][0] . "']" : '') . $aMatch[5][$i][0];
                            }
                        } else {
                            if (! isset( $aMatch[5][$i][0] )) {
                                $sScript .= "\$this->aFields['" . $aMatch[2][$i][0] . "']";
                            } else {
                                $sScript .= "\$this->aFields" . (isset( $aMatch[2][$i][0] ) ? "['" . $aMatch[2][$i][0] . "']" : '') . $aMatch[5][$i][0];
                            }
                        }
                        break;
                }
            }
        }
        $sScript .= substr( $this->sScript, $iAux );
        if(preg_match('/\b(or|and|xor)\b/i' , $sScript)) {
            $sScript = "( ".$sScript." )";
        }
        $sScript = '$bResult = ' . $sScript . ';';
        // checks if the syntax is valid or if the variables in that condition has been previously defined
        if ($this->validSyntax( $sScript ) && $variableIsDefined) {
            $this->bError = false;
            eval( $sScript );
        } else {
            // echo "<script> alert('".G::loadTranslation('MSG_CONDITION_NOT_DEFINED')."'); </script>";
            G::SendTemporalMessage( 'MSG_CONDITION_NOT_DEFINED', 'error', 'labels' );
            $this->bError = true;
        }
        return $bResult;
    }

    Public function evaluateVariable ()
    {
        $process = new Process();
        if(!$process->isBpmnProcess($_SESSION['PROCESS'])) {
            return;
        }
        require_once PATH_CORE.'controllers/pmTablesProxy.php';
        $pmTablesProxy = new pmTablesProxy();
        $variableModule = new ProcessMaker\BusinessModel\Variable();
        $searchTypes = array ('checkgroup','dropdown','suggest');
        $processVariables = $pmTablesProxy->getDynaformVariables($_SESSION['PROCESS'],$searchTypes,false);
        $variables = $this->affected_fields;
        $variables = (is_array($variables))? array_unique($variables) : $variables;
        $newFields = array();
        $arrayValues = array();
        $arrayLabels = array();
        if(is_array($variables) && is_array($processVariables)) {
            foreach($variables as $var) {
                if(strpos($var, '_label') === false) {
                    if(in_array($var,$processVariables)) {
                        if(isset($this->aFields[$var]) && is_array($this->aFields[$var][1]) ) {
                            $varLabel = $var.'_label';
                            $arrayValue = $this->aFields[$var];
                            if(is_array($arrayValue) && sizeof($arrayValue)) {
                                foreach($arrayValue as $val) {
                                    if(is_array($val)){
                                        $val = array_values($val);
                                        $arrayValues[] = $val[0];
                                        $arrayLabels[] = $val[1];
                                    }
                                }
                                if(sizeof($arrayLabels)) {
                                    $varInfo = $variableModule->getVariableTypeByName($_SESSION['PROCESS'],$var);
                                    if(is_array($varInfo) && sizeof($varInfo)) {
                                        $varType = $varInfo['VAR_FIELD_TYPE'];
                                        switch($varType) {
                                            case 'array':
                                                $arrayLabels = '["'.implode('","',$arrayLabels).'"]';
                                                $newFields[$var] = $arrayValues;
                                                $newFields[$varLabel] = $arrayLabels;
                                            break;
                                            case 'string':
                                                $newFields[$var] = $arrayValues[0];
                                                $newFields[$varLabel] = $arrayLabels[0];
                                            break;
                                        }
                                        $this->affected_fields[] = $varLabel;
                                        $this->aFields = array_merge($this->aFields,$newFields);
                                        unset($newFields);
                                        unset($arrayValues);
                                        unset($arrayLabels);
                                    }
                                }
                            }
                        }
                        if (isset($this->aFields[$var]) && is_string($this->aFields[$var])) {
                            $varInfo = $variableModule->getVariableTypeByName($_SESSION['PROCESS'], $var);
                            $options = G::json_decode($varInfo["VAR_ACCEPTED_VALUES"]);
                            $no = count($options);
                            for ($io = 0; $io < $no; $io++) {
                                if ($options[$io]->value === $this->aFields[$var]) {
                                    $this->aFields[$var . "_label"] = $options[$io]->label;
                                }
                            }
                            if ($varInfo["VAR_DBCONNECTION"] !== "" && $varInfo["VAR_DBCONNECTION"] !== "none" && $varInfo["VAR_SQL"] !== "") {
                                try {
                                    $cnn = Propel::getConnection($varInfo["VAR_DBCONNECTION"]);
                                    $stmt = $cnn->createStatement();
                                    $sql = G::replaceDataField($varInfo["VAR_SQL"], $this->aFields);
                                    $rs = $stmt->executeQuery($sql, \ResultSet::FETCHMODE_NUM);
                                    while ($rs->next()) {
                                        $row = $rs->getRow();
                                        if ($row[0] === $this->aFields[$var]) {
                                            $this->aFields[$var . "_label"] = isset($row[1]) ? $row[1] : $row[0];
                                        }
                                    }
                                } catch (Exception $e) {

                                }
                            }
                        }
                    }
                }
            }
        }
    }
}