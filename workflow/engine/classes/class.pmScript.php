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

//Start - Custom functions
G::LoadClass( 'pmFunctions' );
//End - Custom functions
//call plugin
if (class_exists( 'folderData' )) {
    //$folderData = new folderData($sProUid, $proFields['PRO_TITLE'], $sAppUid, $Fields['APP_TITLE'], $sUsrUid);
    $oPluginRegistry = &PMPluginRegistry::getSingleton();
    $aAvailablePmFunctions = $oPluginRegistry->getPmFunctions();
    foreach ($aAvailablePmFunctions as $key => $class) {
        $filePlugin = PATH_PLUGINS . $class . PATH_SEP . 'classes' . PATH_SEP . 'class.pmFunctions.php';
        if (file_exists( $filePlugin )) {
            include_once ($filePlugin);
        }
    }
}
//end plugin
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
    public $affected_fields;

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
        $this->sScript = $sScript;
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

    public function executeAndCatchErrors ($sScript, $sCode)
    {
        ob_start( 'handleFatalErrors' );
        set_error_handler( 'handleErrors' );
        $_SESSION['_CODE_'] = $sCode;
        eval( $sScript );
        unset( $_SESSION['_CODE_'] );
        ob_end_flush();
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
        $bEqual = false;
        $iOcurrences = preg_match_all( '/\@(?:([\@\%\#\?\$\=])([a-zA-Z\_]\w*)|([a-zA-Z\_][\w\-\>\:]*)\(((?:[^\\\\\)]' . '*(?:[\\\\][\w\W])?)*)\))((?:\s*\[[\'"]?\w+[\'"]?\])+)?/', $this->sScript, $aMatch, PREG_PATTERN_ORDER | PREG_OFFSET_CAPTURE );
        if ($iOcurrences) {
            for ($i = 0; $i < $iOcurrences; $i ++) {
                $sAux = substr( $this->sScript, $iAux, $aMatch[0][$i][1] - $iAux );
                if (! $bEqual) {
                    if (strpos( $sAux, '==' ) !== false) {
                        $bEqual = false;
                    } else {
                        if (strpos( $sAux, '=' ) !== false) {
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
                        eval( "if (!isset(\$this->aFields['" . $aMatch[2][$i][0] . "'])) { \$this->aFields['" . $aMatch[2][$i][0] . "'] = null; }" );
                    } else {
                        eval( "if (!isset(\$this->aFields" . (isset( $aMatch[2][$i][0] ) ? "['" . $aMatch[2][$i][0] . "']" : '') . $aMatch[5][$i][0] . ")) { \$this->aFields" . (isset( $aMatch[2][$i][0] ) ? "['" . $aMatch[2][$i][0] . "']" : '') . $aMatch[5][$i][0] . " = null; }" );
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
        $this->executeAndCatchErrors( $sScript, $this->sScript );
        for ($i = 0; $i < count( $this->affected_fields ); $i ++) {
            $_SESSION['TRIGGER_DEBUG']['DATA'][] = Array ('key' => $this->affected_fields[$i],'value' => isset( $this->aFields[$this->affected_fields[$i]] ) ? $this->aFields[$this->affected_fields[$i]] : ''
            );
        }
        //echo '<pre>-->'; print_r($_SESSION['TRIGGER_DEBUG']['DATA']); echo '<---</pre>';
    }

    /**
     * Evaluate the current script
     *
     * @return void
     */
    public function evaluate ()
    {
        $bResult = null;
        $sScript = '';
        $iAux = 0;
        $bEqual = false;
        $variableIsDefined = true;
        $iOcurrences = preg_match_all( '/\@(?:([\@\%\#\?\$\=])([a-zA-Z\_]\w*)|([a-zA-Z\_][\w\-\>\:]*)\(((?:[^\\\\\)]' . '*(?:[\\\\][\w\W])?)*)\))((?:\s*\[[\'"]?\w+[\'"]?\])+)?/', $this->sScript, $aMatch, PREG_PATTERN_ORDER | PREG_OFFSET_CAPTURE );
        if ($iOcurrences) {
            for ($i = 0; $i < $iOcurrences; $i ++) {
                // if the variables for that condition has not been previously defined then $variableIsDefined
                // is set to false
                if (!isset($this->aFields[$aMatch[2][$i][0]]) && !isset($aMatch[5][$i][0])) {
                    $this->aFields[$aMatch[2][$i][0]] = '';
                } else {
                    if (!isset($this->aFields[$aMatch[2][$i][0]])) {
                        eval("\$this->aFields['" . $aMatch[2][$i][0] . "']" . $aMatch[5][$i][0] . " = '';");
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
}

//Start - Private functions


/**
 * Convert to string
 *
 * @param variant $vValue
 * @return string
 */
function pmToString ($vValue)
{
    return (string) $vValue;
}

/**
 * Convert to integer
 *
 * @param variant $vValue
 * @return integer
 */
function pmToInteger ($vValue)
{
    return (int) $vValue;
}

/**
 * Convert to float
 *
 * @param variant $vValue
 * @return float
 */
function pmToFloat ($vValue)
{
    return (float) $vValue;
}

/**
 * Convert to Url
 *
 * @param variant $vValue
 * @return url
 */
function pmToUrl ($vValue)
{
    return urlencode( $vValue );
}

/**
 * Convert to data base escaped string
 *
 * @param variant $vValue
 * @return string
 */
function pmSqlEscape ($vValue)
{
    return G::sqlEscape( $vValue );
}

//End - Private functions


/* * *************************************************************************
 * Error handler
 * author: Julio Cesar Laura Avenda�o <juliocesar@colosa.com>
 * date: 2009-10-01
 * ************************************************************************* */
/*
 * Convert to data base escaped string
 * @param string $errno
 * @param string $errstr
 * @param string $errfile
 * @param string $errline
 * @return void
 */
function handleErrors ($errno, $errstr, $errfile, $errline)
{
    if ($errno != '' && ($errno != 8) && ($errno != 2048)) {
        if (isset( $_SESSION['_CODE_'] )) {
            $sCode = $_SESSION['_CODE_'];
            unset( $_SESSION['_CODE_'] );
            global $oPMScript;
            if (isset($oPMScript) && isset($_SESSION['APPLICATION'])) {
                G::LoadClass( 'case' );
                $oCase = new Cases();
                $oPMScript->aFields['__ERROR__'] = $errstr;
                $oCase->updateCase($_SESSION['APPLICATION'], array('APP_DATA' => $oPMScript->aFields));
            }
            registerError( 1, $errstr, $errline - 1, $sCode );
        }
    }
}

/*
 * Handle Fatal Errors
 * @param variant $buffer
 * @return buffer
 */

function handleFatalErrors ($buffer)
{
    G::LoadClass( 'case' );
    $oCase = new Cases();
    if (preg_match( '/(error<\/b>:)(.+)(<br)/', $buffer, $regs )) {
        $err = preg_replace( '/<.*?>/', '', $regs[2] );
        $aAux = explode( ' in ', $err );
        $sCode = $_SESSION['_CODE_'];
        unset( $_SESSION['_CODE_'] );
        registerError( 2, $aAux[0], 0, $sCode );
        if (strpos( $_SERVER['REQUEST_URI'], '/cases/cases_Step' ) !== false) {
            if (strpos( $_SERVER['REQUEST_URI'], '&ACTION=GENERATE' ) !== false) {
                $aNextStep = $oCase->getNextStep( $_SESSION['PROCESS'], $_SESSION['APPLICATION'], $_SESSION['INDEX'], $_SESSION['STEP_POSITION'] );
                if ($_SESSION['TRIGGER_DEBUG']['ISSET']) {
                    $_SESSION['TRIGGER_DEBUG']['TIME'] = 'AFTER';
                    $_SESSION['TRIGGER_DEBUG']['BREAKPAGE'] = $aNextStep['PAGE'];
                    $aNextStep['PAGE'] = $aNextStep['PAGE'] . '&breakpoint=triggerdebug';
                }
                global $oPMScript;
                if (isset($oPMScript) && isset($_SESSION['APPLICATION'])) {
                    $oPMScript->aFields['__ERROR__'] = $aAux[0];
                    $oCase->updateCase($_SESSION['APPLICATION'], array('APP_DATA' => $oPMScript->aFields));
                }
                G::header( 'Location: ' . $aNextStep['PAGE'] );
                die();
            }
            $_SESSION['_NO_EXECUTE_TRIGGERS_'] = 1;
            global $oPMScript;
            if (isset($oPMScript) && isset($_SESSION['APPLICATION'])) {
                $oPMScript->aFields['__ERROR__'] = $aAux[0];
                $oCase->updateCase($_SESSION['APPLICATION'], array('APP_DATA' => $oPMScript->aFields));
            }
            G::header( 'Location: ' . $_SERVER['REQUEST_URI'] );
            die();
        } else {
            $aNextStep = $oCase->getNextStep( $_SESSION['PROCESS'], $_SESSION['APPLICATION'], $_SESSION['INDEX'], $_SESSION['STEP_POSITION'] );
            if ($_SESSION['TRIGGER_DEBUG']['ISSET']) {
                $_SESSION['TRIGGER_DEBUG']['TIME'] = 'AFTER';
                $_SESSION['TRIGGER_DEBUG']['BREAKPAGE'] = $aNextStep['PAGE'];
                $aNextStep['PAGE'] = $aNextStep['PAGE'] . '&breakpoint=triggerdebug';
            }
            if (strpos( $aNextStep['PAGE'], 'TYPE=ASSIGN_TASK&UID=-1' ) !== false) {
                G::SendMessageText( 'Fatal error in trigger', 'error' );
            }
            global $oPMScript;
            if (isset($oPMScript) && isset($_SESSION['APPLICATION'])) {
                $oPMScript->aFields['__ERROR__'] = $aAux[0];
                $oCase->updateCase($_SESSION['APPLICATION'], array('APP_DATA' => $oPMScript->aFields));
            }
            G::header( 'Location: ' . $aNextStep['PAGE'] );
            die();
        }
    }
    return $buffer;
}

/*
 * Register Error
 * @param string $iType
 * @param string $sError
 * @param string $iLine
 * @param string $sCode
 * @return void
 */

function registerError ($iType, $sError, $iLine, $sCode)
{
    $sType = ($iType == 1 ? 'ERROR' : 'FATAL');
    $_SESSION['TRIGGER_DEBUG']['ERRORS'][][$sType] = $sError . ($iLine > 0 ? ' (line ' . $iLine . ')' : '') . ':<br /><br />' . $sCode;
}

/**
 * Obtain engine Data Base name
 *
 * @param type $connection
 * @return type
 */
function getEngineDataBaseName ($connection)
{
    $aDNS = $connection->getDSN();
    return $aDNS["phptype"];
}

/**
 * Execute Queries for Oracle Database
 *
 * @param type $sql
 * @param type $connection
 */
function executeQueryOci ($sql, $connection, $aParameter = array())
{

    $aDNS = $connection->getDSN();
    $sUsername = $aDNS["username"];
    $sPassword = $aDNS["password"];
    $sHostspec = $aDNS["hostspec"];
    $sDatabse = $aDNS["database"];
    $sPort = $aDNS["port"];

    if ($sPort != "1521") {
        // if not default port
        $conn = oci_connect( $sUsername, $sPassword, $sHostspec . ":" . $sPort . "/" . $sDatabse );
    } else {
        $conn = oci_connect( $sUsername, $sPassword, $sHostspec . "/" . $sDatabse );
    }

    if (! $conn) {
        $e = oci_error();
        trigger_error( htmlentities( $e['message'], ENT_QUOTES ), E_USER_ERROR );
        return $e;
    }

    switch (true) {
        case preg_match( "/^(SELECT|SHOW|DESCRIBE|DESC|WITH)\s/i", $sql ):
            $stid = oci_parse( $conn, $sql );
            if (count( $aParameter ) > 0) {
                foreach ($aParameter as $key => $val) {
                    oci_bind_by_name( $stid, $key, $val );
                }
            }
            oci_execute( $stid, OCI_DEFAULT );

            $result = Array ();
            $i = 1;
            while ($row = oci_fetch_array( $stid, OCI_ASSOC + OCI_RETURN_NULLS )) {
                $result[$i ++] = $row;
            }
            oci_free_statement( $stid );
            oci_close( $conn );
            return $result;
            break;
        case preg_match( "/^(INSERT|UPDATE|DELETE)\s/i", $sql ):
            $stid = oci_parse( $conn, $sql );
            $isValid = true;
            if (count( $aParameter ) > 0) {
                foreach ($aParameter as $key => $val) {
                    oci_bind_by_name( $stid, $key, $val );
                }
            }
            $objExecute = oci_execute( $stid, OCI_DEFAULT );
            $result = oci_num_rows ($stid);
            if ($objExecute) {
                oci_commit( $conn );
            } else {
                oci_rollback( $conn );
                $isValid = false;
            }
            oci_free_statement( $stid );
            oci_close( $conn );
            if ($isValid) {
                return $result;
            } else {
                return oci_error();
            }
            break;
        default:
            // Stored procedures
            $stid = oci_parse( $conn, $sql );
            $aParameterRet = array ();
            if (count( $aParameter ) > 0) {
                foreach ($aParameter as $key => $val) {
                    $aParameterRet[$key] = $val;
                    // The third parameter ($aParameterRet[$key]) returned a value by reference.
                    oci_bind_by_name( $stid, $key, $aParameterRet[$key] );
                }
            }
            $objExecute = oci_execute( $stid, OCI_DEFAULT );
            oci_free_statement( $stid );
            oci_close( $conn );
            return $aParameterRet;
            break;
    }
}

