<?php
namespace ProcessMaker\BusinessModel;

class TriggerWizard
{
    private $arrayFieldDefinition = array(
        "TRI_UID"         => array("type" => "string", "required" => false, "empty" => false, "defaultValues" => array(),         "fieldNameAux" => "triggerUid"),

        "TRI_TITLE"       => array("type" => "string", "required" => true,  "empty" => false, "defaultValues" => array(),         "fieldNameAux" => "triggerTitle"),
        "TRI_DESCRIPTION" => array("type" => "string", "required" => false, "empty" => true,  "defaultValues" => array(),         "fieldNameAux" => "triggerDescription"),
        "TRI_TYPE"        => array("type" => "string", "required" => true,  "empty" => false, "defaultValues" => array("SCRIPT"), "fieldNameAux" => "triggerType"),
        "TRI_WEBBOT"      => array("type" => "string", "required" => false, "empty" => true,  "defaultValues" => array(),         "fieldNameAux" => "triggerWebbot"),
        "TRI_PARAM"       => array("type" => "string", "required" => false, "empty" => true,  "defaultValues" => array(),         "fieldNameAux" => "triggerParam")
    );

    private $formatFieldNameInUppercase = true;

    private $arrayFieldNameForException = array(
        "processUid"    => "PRO_UID",
        "libraryName"   => "LIB_NAME",
        "methodName"    => "MTH_NAME",
        "triggerParams" => "TRI_PARAMS"
    );

    private $library;

    /**
     * Constructor of the class
     *
     * return void
     */
    public function __construct()
    {
        try {
            foreach ($this->arrayFieldDefinition as $key => $value) {
                $this->arrayFieldNameForException[$value["fieldNameAux"]] = $key;
            }

            //Library

            $this->library = \TriggerLibrary::getSingleton();
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Set the format of the fields name (uppercase, lowercase)
     *
     * @param bool $flag Value that set the format
     *
     * return void
     */
    public function setFormatFieldNameInUppercase($flag)
    {
        try {
            $this->formatFieldNameInUppercase = $flag;

            $this->setArrayFieldNameForException($this->arrayFieldNameForException);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Set exception messages for fields
     *
     * @param array $arrayData Data with the fields
     *
     * return void
     */
    public function setArrayFieldNameForException($arrayData)
    {
        try {
            foreach ($arrayData as $key => $value) {
                $this->arrayFieldNameForException[$key] = $this->getFieldNameByFormatFieldName($value);
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Get the name of the field according to the format
     *
     * @param string $fieldName Field name
     *
     * return string Return the field name according the format
     */
    public function getFieldNameByFormatFieldName($fieldName)
    {
        try {
            return ($this->formatFieldNameInUppercase)? strtoupper($fieldName) : strtolower($fieldName);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Verify if doesn't exists the name of the library
     *
     * @param string $libraryName                  Library name
     * @param string $libraryFieldNameForException Field name for the exception
     *
     * return void Throw exception if doesn't exists the name of the library
     */
    public function throwExceptionIfNotExistsLibrary($libraryName, $libraryFieldNameForException)
    {
        try {
            $arrayLibrary = $this->library->getRegisteredClasses();

            if (!isset($arrayLibrary[$this->libraryGetLibraryName($libraryName)])) {
                throw new \Exception(\G::LoadTranslation("ID_LIBRARY_DOES_NOT_EXIST", array($libraryFieldNameForException, $libraryName)));
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Verify if doesn't exists the method in the library
     *
     * @param string $libraryName                  Library name
     * @param string $methodName                   Method name
     * @param string $libraryFieldNameForException Field name for the exception
     * @param string $methodFieldNameForException  Field name for the exception
     *
     * return void Throw exception if doesn't exists the method in the library
     */
    public function throwExceptionIfNotExistsMethodInLibrary($libraryName, $methodName, $libraryFieldNameForException, $methodFieldNameForException)
    {
        try {
            $this->throwExceptionIfNotExistsLibrary($libraryName, $libraryFieldNameForException);

            $library = $this->library->getLibraryDefinition($this->libraryGetLibraryName($libraryName));

            if (!isset($library->methods[$methodName])) {
                throw new \Exception(\G::LoadTranslation("ID_LIBRARY_FUNCTION_DOES_NOT_EXIST", array($methodFieldNameForException, $methodName)));
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Verify if wizard is invalid for the trigger
     *
     * @param string $libraryName                     Library name
     * @param string $methodName                      Method name
     * @param string $triggerUid                      Unique id of Trigger
     * @param string $libraryFieldNameForException    Field name for the exception
     * @param string $methodFieldNameForException     Field name for the exception
     * @param string $triggerUidFieldNameForException Field name for the exception
     *
     * return void Throw exception if wizard is invalid for the trigger
     */
    public function throwExceptionIfLibraryAndMethodIsInvalidForTrigger($libraryName, $methodName, $triggerUid, $libraryFieldNameForException, $methodFieldNameForException, $triggerUidFieldNameForException)
    {
        try {
            //Verify data
            $this->throwExceptionIfNotExistsMethodInLibrary($libraryName, $methodName, $libraryFieldNameForException, $methodFieldNameForException);

            $trigger = new \ProcessMaker\BusinessModel\Trigger();

            $trigger->throwExceptionIfNotExistsTrigger($triggerUid, "", $triggerUidFieldNameForException);

            //Get data
            $trigger = new \Triggers();

            $arrayTriggerData = $trigger->load($triggerUid);

            $triggerParam = unserialize($arrayTriggerData["TRI_PARAM"]);

            if ($arrayTriggerData["TRI_PARAM"] == "" || !isset($triggerParam["hash"])) {
                throw new \Exception(\G::LoadTranslation("ID_TRIGGER_HAS_NOT_BEEN_CREATED_WITH_WIZARD", array($triggerUidFieldNameForException, $triggerUid)));
            }

            $arrayTriggerData["TRI_PARAM"] = $triggerParam;

            if (md5($arrayTriggerData["TRI_WEBBOT"]) != $arrayTriggerData["TRI_PARAM"]["hash"]) {
                throw new \Exception(\G::LoadTranslation("ID_TRIGGER_HAS_BEEN_MODIFIED_MANUALLY_INVALID_FOR_WIZARD", array($triggerUidFieldNameForException, $triggerUid)));
            }

            $triggerParamLibraryName = (preg_match("/^class\.?(.*)\.pmFunctions\.php$/", $arrayTriggerData["TRI_PARAM"]["params"]["LIBRARY_CLASS"], $arrayMatch))? ((isset($arrayMatch[1]) && $arrayMatch[1] != "")? $arrayMatch[1] : "pmFunctions") : $arrayTriggerData["TRI_PARAM"]["params"]["LIBRARY_CLASS"];
            $triggerParamMethodName = $arrayTriggerData["TRI_PARAM"]["params"]["PMFUNTION_NAME"];

            if ($libraryName != $triggerParamLibraryName || $methodName != $triggerParamMethodName) {
                throw new \Exception(\G::LoadTranslation("ID_WIZARD_LIBRARY_AND_FUNCTION_IS_INVALID_FOR_TRIGGER", array($libraryName, $methodName, $triggerUidFieldNameForException, $triggerUid)));
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Validate TRI_PARAMS data
     *
     * @param string $libraryName                  Library name
     * @param string $methodName                   Method name
     * @param array  $arrayData                    Data
     * @param string $libraryFieldNameForException Field name for the exception
     * @param string $methodFieldNameForException  Field name for the exception
     * @param string $fieldNameForException        Field name for the exception
     *
     * return array Return array. Throw exception otherwise, if TRI_PARAMS data has an invalid value
     */
    public function throwExceptionIfDataNotMetTriggerParamsDefinition($libraryName, $methodName, $arrayData, $libraryFieldNameForException, $methodFieldNameForException, $fieldNameForException)
    {
        try {
            $arrayData = array_change_key_case($arrayData, CASE_UPPER);

            $arrayParamData = array();

            //Verify data
            $this->throwExceptionIfNotExistsMethodInLibrary($libraryName, $methodName, $libraryFieldNameForException, $methodFieldNameForException);

            //Set variables
            $nInputParam  = count($this->methodGetInputParams($libraryName, $methodName));
            $nOutputParam = count($this->methodGetOutputParams($libraryName, $methodName));

            if ($nInputParam > 0 || $nOutputParam > 0) {
                if (!isset($arrayData["TRI_PARAMS"])) {
                    throw new \Exception(\G::LoadTranslation("ID_UNDEFINED_VALUE_IS_REQUIRED", array($fieldNameForException)));
                }

                if (!is_array($arrayData["TRI_PARAMS"])) {
                    throw new \Exception(\G::LoadTranslation("ID_INVALID_VALUE_THIS_MUST_BE_ARRAY", array($fieldNameForException)));
                }

                $arrayData["TRI_PARAMS"] = array_change_key_case($arrayData["TRI_PARAMS"], CASE_UPPER);

                if ($nInputParam > 0) {
                    if (!isset($arrayData["TRI_PARAMS"]["INPUT"])) {
                        throw new \Exception(\G::LoadTranslation("ID_UNDEFINED_VALUE_IS_REQUIRED", array($this->getFieldNameByFormatFieldName($fieldNameForException . ".INPUT"))));
                    }

                    if (!is_array($arrayData["TRI_PARAMS"]["INPUT"])) {
                        throw new \Exception(\G::LoadTranslation("ID_INVALID_VALUE_THIS_MUST_BE_ARRAY", array($this->getFieldNameByFormatFieldName($fieldNameForException . ".INPUT"))));
                    }

                    $arrayParamData["input"] = $arrayData["TRI_PARAMS"]["INPUT"];
                }

                if ($nOutputParam > 0) {
                    if (!isset($arrayData["TRI_PARAMS"]["OUTPUT"])) {
                        throw new \Exception(\G::LoadTranslation("ID_UNDEFINED_VALUE_IS_REQUIRED", array($this->getFieldNameByFormatFieldName($fieldNameForException . ".OUTPUT"))));
                    }

                    if (!is_array($arrayData["TRI_PARAMS"]["OUTPUT"])) {
                        throw new \Exception(\G::LoadTranslation("ID_INVALID_VALUE_THIS_MUST_BE_ARRAY", array($this->getFieldNameByFormatFieldName($fieldNameForException . ".OUTPUT"))));
                    }

                    $arrayParamData["output"] = $arrayData["TRI_PARAMS"]["OUTPUT"];
                }

                $this->throwExceptionIfDataNotMetParamDefinition($libraryName, $methodName, $arrayParamData, $libraryFieldNameForException, $methodFieldNameForException);
            }

            return $arrayParamData;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Validate data by parameter definition
     *
     * @param string $libraryName                  Library name
     * @param string $methodName                   Method name
     * @param array  $arrayParamData               Data
     * @param string $libraryFieldNameForException Field name for the exception
     * @param string $methodFieldNameForException  Field name for the exception
     *
     * return void Throw exception if data has an invalid value
     */
    public function throwExceptionIfDataNotMetParamDefinition($libraryName, $methodName, $arrayParamData, $libraryFieldNameForException, $methodFieldNameForException)
    {
        try {
            //Verify data
            $this->throwExceptionIfNotExistsMethodInLibrary($libraryName, $methodName, $libraryFieldNameForException, $methodFieldNameForException);

            //Set variables
            $arrayMethodInputParam = $this->methodGetInputParams($libraryName, $methodName);
            $arrayMethodOutputParam = $this->methodGetOutputParams($libraryName, $methodName);

            foreach ($arrayMethodOutputParam as $key => $value) {
                $arrayMethodOutputParam[$key]["type"] = "string";
            }

            $arrayVerify = array(
                array("paramDefinition" => $arrayMethodInputParam,  "paramData" => (isset($arrayParamData["input"]))? $arrayParamData["input"] : array()),
                array("paramDefinition" => $arrayMethodOutputParam, "paramData" => (isset($arrayParamData["output"]))? $arrayParamData["output"] : array())
            );

            $process = new \ProcessMaker\BusinessModel\Process();

            foreach ($arrayVerify as $key1 => $value1) {
                if (count($value1["paramDefinition"]) > 0) {
                    $arrayParamDefinition = array();
                    $arrayParamNameForException = array();

                    foreach ($value1["paramDefinition"] as $key2 => $value2) {
                        $arrayParamDefinition[$value2["name"]] = array("type" => $value2["type"], "required" => $value2["required"], "empty" => !$value2["required"], "defaultValues" => array(), "fieldNameAux" => $value2["name"]);
                        $arrayParamNameForException[$value2["name"]] = $value2["name"];
                    }

                    $process->throwExceptionIfDataNotMetFieldDefinition($value1["paramData"], $arrayParamDefinition, $arrayParamNameForException, true);
                }
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Get the filename of the library
     *
     * @param string $libraryName Library name
     *
     * return string Return the filename of the library
     */
    public function libraryGetLibraryName($libraryName)
    {
        try {
            if (!preg_match("/\.pmFunctions\.php$/", $libraryName)) {
                $libraryName = ($libraryName != "pmFunctions")? $libraryName . ".pmFunctions" : $libraryName;
                $libraryName = "class." . $libraryName . ".php";
            }

            return $libraryName;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Get all input parameters of a method
     *
     * @param string $libraryName Library name
     * @param string $methodName  Method name
     *
     * return array Return an array with all input parameters of a method
     */
    public function methodGetInputParams($libraryName, $methodName)
    {
        try {
            $arrayParam = array();

            //Verify data
            $this->throwExceptionIfNotExistsMethodInLibrary($libraryName, $methodName, $this->arrayFieldNameForException["libraryName"], $this->arrayFieldNameForException["methodName"]);

            //Get data
            $library = $this->library->getLibraryDefinition($this->libraryGetLibraryName($libraryName));
            $method = $library->methods[$methodName];

            $arrayParameter = array_keys($method->params);

            foreach ($arrayParameter as $key => $value) {
                $strParam = $value;

                if ($strParam != "") {
                    $arrayp = explode("|", $strParam);

                    //Get param
                    $arrayTypeAndMaxLength = array();

                    if (preg_match("/^\s*(.+)\s*[\{\[\(]\s*(\d+)\s*[\)\]\}].*$/", $arrayp[0], $arrayMatch)) {
                        $arrayTypeAndMaxLength = array("type" => $arrayMatch[1], "maxLength" => (int)($arrayMatch[2]));
                    } else {
                        $arrayTypeAndMaxLength = array("type" => trim($arrayp[0]));
                    }

                    $arrayNameAndDefaultValue = array();

                    $arrayNameAndDefaultValue["name"] = "";

                    if (preg_match("/^\s*\\\$(\w+)(.*)$/", $arrayp[1], $arrayMatch)) {
                        $arrayNameAndDefaultValue["name"] = $arrayMatch[1];

                        $arrayp[1] = $arrayMatch[2];
                    }

                    if (preg_match("/^\s*=\s*(.*)$/", $arrayp[1], $arrayMatch)) {
                        $arrayNameAndDefaultValue["defaultValue"] = trim(trim($arrayMatch[1]), "\"'");
                    }

                    //Set param
                    $arrayData = array(
                        "name"        => $arrayNameAndDefaultValue["name"],
                        "type"        => $arrayTypeAndMaxLength["type"],
                        "label"       => (isset($arrayp[2]))? trim($arrayp[2]) : $arrayNameAndDefaultValue["name"],
                        "description" => (isset($arrayp[3]))? trim($arrayp[3]) : "",
                        "required"    => !isset($arrayNameAndDefaultValue["defaultValue"])
                    );

                    if (isset($arrayNameAndDefaultValue["defaultValue"])) {
                        $arrayData["default_value"] = $arrayNameAndDefaultValue["defaultValue"];
                    }

                    if (isset($arrayTypeAndMaxLength["maxLength"])) {
                        $arrayData["max_length"] = $arrayTypeAndMaxLength["maxLength"];
                    }

                    $arrayParam[] = $arrayData;
                }
            }

            //Return
            return $arrayParam;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Get all output parameters of a method
     *
     * @param string $libraryName Library name
     * @param string $methodName  Method name
     *
     * return array Return an array with all output parameters of a method
     */
    public function methodGetOutputParams($libraryName, $methodName)
    {
        try {
            $arrayParam = array();

            //Verify data
            $this->throwExceptionIfNotExistsMethodInLibrary($libraryName, $methodName, $this->arrayFieldNameForException["libraryName"], $this->arrayFieldNameForException["methodName"]);

            //Get data
            $library = $this->library->getLibraryDefinition($this->libraryGetLibraryName($libraryName));
            $method = $library->methods[$methodName];

            if (isset($method->info["return"]) && $method->info["return"] != "") {
                $strParam = $method->info["return"];

                $arrayp = explode("|", $strParam);

                if (isset($arrayp[0]) && isset($arrayp[1]) && trim(strtoupper($arrayp[0])) != strtoupper(\G::LoadTranslation("ID_NONE"))) {
                    $description = "";

                    if (isset($arrayp[3])) {
                        $description = (trim(strtoupper($arrayp[3])) == strtoupper(\G::LoadTranslation("ID_NONE")))? \G::LoadTranslation("ID_NOT_REQUIRED") : trim($arrayp[3]);
                    } else {
                        $description = $strParam;
                    }

                    //Set param
                    $arrayParam[] = array(
                        "name"        => "tri_answer",
                        "type"        => trim($arrayp[0]),
                        "label"       => \G::LoadTranslation("ID_TRIGGER_RETURN_LABEL"),
                        "description" => $description,
                        "required"    => isset($arrayp[1]) //(trim($arrayp[1]) != "")? true : false
                    );
                }
            }

            //Return
            return $arrayParam;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Set values in Trigger fields (TRI_WEBBOT, TRI_PARAM)
     *
     * @param string $libraryName    Library name
     * @param string $methodName     Method name
     * @param string $triggerUid     Unique id of Trigger
     * @param array  $arrayParamData Data
     *
     * return void
     */
    public function setData($libraryName, $methodName, $triggerUid, $arrayParamData)
    {
        try {
            //Verify data
            $this->throwExceptionIfNotExistsMethodInLibrary($libraryName, $methodName, $this->arrayFieldNameForException["libraryName"], $this->arrayFieldNameForException["methodName"]);

            $trigger = new \ProcessMaker\BusinessModel\Trigger();

            $trigger->throwExceptionIfNotExistsTrigger($triggerUid, "", $this->arrayFieldNameForException["triggerUid"]);

            //Set variables
            $arrayMethodInputParam = $this->methodGetInputParams($libraryName, $methodName);
            $arrayMethodOutputParam = $this->methodGetOutputParams($libraryName, $methodName);

            $arrayTriggerParam = array();
            $arrayScriptMethodParam = array();

            $strParamsNamePhp = "";
            $strParamsType = "";
            $strParamsRequired = "";

            //Set variables - Load Trigger
            $trigger = new \Triggers();

            $arrayTriggerData = $trigger->load($triggerUid);

            $arrayTriggerParam["TRI_UID"] = $triggerUid;
            $arrayTriggerParam["TRI_TITLE"] = $arrayTriggerData["TRI_TITLE"];
            $arrayTriggerParam["TRI_DESCRIPTION"] = $arrayTriggerData["TRI_DESCRIPTION"];
            $arrayTriggerParam["TRI_TYPE"] = $arrayTriggerData["TRI_TYPE"];
            $arrayTriggerParam["PRO_UID"] = $arrayTriggerData["PRO_UID"];

            //Set variables - Input
            foreach ($arrayMethodInputParam as $key => $value) {
                $paramName = $value["name"];
                $paramType = $value["type"];
                $paramRequired = $value["required"];
                $paramDefaultValue = (isset($value["default_value"]))? $value["default_value"] : "";

                //TRI_PARAM
                if ($paramType != "array") {
                    $arrayTriggerParam[$paramName] = (isset($arrayParamData["input"][$paramName]))? $arrayParamData["input"][$paramName] : $paramDefaultValue;
                } else {
                    if (isset($arrayParamData["input"][$paramName])) {
                        if (is_array($arrayParamData["input"][$paramName])) {
                            $strArrayElements = "";

                            foreach ($arrayParamData["input"][$paramName] as $key2 => $value2) {
                                $strKey = (is_string($key2))? "\"" . $key2 . "\"" : $key2;
                                $strValue = (is_string($value2))? "\"" . str_replace("\"", "\\\"", $value2) . "\"" : $value2;

                                $strArrayElements = $strArrayElements . (($strArrayElements != "")? ", " : "") . $strKey . " => " . $strValue;
                            }

                            $arrayParamData["input"][$paramName] = "array(" . $strArrayElements . ")";
                        }

                        $arrayTriggerParam[$paramName] = $arrayParamData["input"][$paramName];
                    } else {
                        $arrayTriggerParam[$paramName] = $paramDefaultValue;
                    }
                }

                //Variables
                $strParamsNamePhp = $strParamsNamePhp . (($strParamsNamePhp != "")? "," : "") . "\$" . $paramName;
                $strParamsType = $strParamsType . (($strParamsType != "")? "," : "") . $paramType;

                if ($paramRequired) {
                    $strParamsRequired = $strParamsRequired . (($strParamsRequired != "")? "," : "") . $paramName;
                }

                //Method parameters
                $paramValue = "\"" . $paramDefaultValue . "\"";

                if (isset($arrayParamData["input"][$paramName])) {
                    if (preg_match("/^.*@@.*$/", $arrayParamData["input"][$paramName])) {
                        $paramValue = trim($arrayParamData["input"][$paramName]);
                    } else {
                        switch ($paramType) {
                            case "int":
                            case "integer":
                                $paramValue = intval($arrayParamData["input"][$paramName]);
                                break;
                            case "float":
                            case "real":
                            case "double":
                                $paramValue = floatval($arrayParamData["input"][$paramName]);
                                break;
                            case "bool":
                            case "boolean":
                                $paramValue = 'true';
                                if($arrayParamData["input"][$paramName]!=''){
                                  $paramValue = $arrayParamData["input"][$paramName];
                                }
                                break;
                            case "array":
                                $paramValue = trim($arrayParamData["input"][$paramName]);
                                break;
                            case "string":
                                $paramValue = "\"" . str_replace("\"", "\\\"", $arrayParamData["input"][$paramName]) . "\"";
                                break;
                            default:
                                if (is_numeric($arrayParamData["input"][$paramName]) ||
                                    is_bool($arrayParamData["input"][$paramName]) ||
                                    preg_match("/^\s*array\s*\(.*\)\s*$/", $arrayParamData["input"][$paramName])
                                ) {
                                    $paramValue = trim($arrayParamData["input"][$paramName]);
                                } else {
                                    $paramValue = "\"" . str_replace("\"", "\\\"", $arrayParamData["input"][$paramName]) . "\"";
                                }
                                break;
                        }
                    }
                }

                $arrayScriptMethodParam[] = $paramValue;
            }

            //Set variables - Output
            $varReturn = "";

            foreach ($arrayMethodOutputParam as $key => $value) {
                $paramName = $value["name"];
                $paramRequired = $value["required"];

                if ($paramRequired && isset($arrayParamData["output"][$paramName]) && trim($arrayParamData["output"][$paramName]) != "") {
                    $arrayTriggerParam[strtoupper($paramName)] = trim($arrayParamData["output"][$paramName]);

                    $strParamsRequired = $strParamsRequired . (($strParamsRequired != "")? "," : "") . strtoupper($paramName);

                    $varReturn = trim($arrayParamData["output"][$paramName]) . " = ";
                    break;
                }
            }

            //Set data
            $library = $this->library->getLibraryDefinition($this->libraryGetLibraryName($libraryName));
            $method = $library->methods[$methodName];

            $strScript =                     "/*******************************************************";
            $strScript = $strScript . "\n" . " *";
            $strScript = $strScript . "\n" . " * Generated by ProcessMaker Trigger Wizard";
            $strScript = $strScript . "\n" . " * Library: " . trim($library->info["name"]);
            $strScript = $strScript . "\n" . " * Method: " . trim($method->info["label"]);
            $strScript = $strScript . "\n" . " * Date: " . date("Y-m-d H:i:s");
            $strScript = $strScript . "\n" . " *";
            $strScript = $strScript . "\n" . " * ProcessMaker " . date("Y");
            $strScript = $strScript . "\n" . " *";
            $strScript = $strScript . "\n" . " *******************************************************/";
            $strScript = $strScript . "\n";
            $strScript = $strScript . "\n" . $varReturn . $methodName . "(" . implode(", ", $arrayScriptMethodParam) . ");";

            $arrayTriggerParam["TRI_WEBBOT"] = $strScript;

            $arrayTriggerParam["__notValidateThisFields__"] = "";
            $arrayTriggerParam["DynaformRequiredFields"]    = "[]";
            $arrayTriggerParam["PAGED_TABLE_ID"]            = "";

            $arrayTriggerParam["LIBRARY_NAME"]  = trim($library->info["name"]);
            $arrayTriggerParam["LIBRARY_CLASS"] = $this->libraryGetLibraryName($libraryName);

            $arrayTriggerParam["PMFUNTION_NAME"]  = $methodName;
            $arrayTriggerParam["PMFUNTION_LABEL"] = trim($method->info["label"]);

            $arrayTriggerParam["ALLFUNCTION"]      = $strParamsNamePhp;
            $arrayTriggerParam["ALLFUNCTION_TYPE"] = $strParamsType;
            $arrayTriggerParam["FIELDS_REQUIRED"]  = $strParamsRequired;

            //Update
            $result = $trigger->update(array("TRI_UID" => $triggerUid, "TRI_WEBBOT" => $strScript, "TRI_PARAM" => serialize(array("hash" => md5($strScript), "params" => $arrayTriggerParam))));
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Create Trigger for a Process
     *
     * @param string $libraryName Library name
     * @param string $methodName  Method name
     * @param string $processUid  Unique id of Process
     * @param array  $arrayData   Data
     *
     * return array Return data of the new Trigger created
     */
    public function create($libraryName, $methodName, $processUid, $arrayData)
    {
        try {
            $arrayData = array_change_key_case($arrayData, CASE_UPPER);

            //Verify data
            $process = new \ProcessMaker\BusinessModel\Process();
            $trigger = new \ProcessMaker\BusinessModel\Trigger();

            $this->throwExceptionIfNotExistsMethodInLibrary($libraryName, $methodName, $this->arrayFieldNameForException["libraryName"], $this->arrayFieldNameForException["methodName"]);

            $process->throwExceptionIfNotExistsProcess($processUid, $this->arrayFieldNameForException["processUid"]);

            $process->throwExceptionIfDataNotMetFieldDefinition($arrayData, $this->arrayFieldDefinition, $this->arrayFieldNameForException, true);

            $arrayParamData = $this->throwExceptionIfDataNotMetTriggerParamsDefinition($libraryName, $methodName, $arrayData, $this->arrayFieldNameForException["libraryName"], $this->arrayFieldNameForException["methodName"], $this->arrayFieldNameForException["triggerParams"]);

            $trigger->throwExceptionIfExistsTitle($processUid, $arrayData["TRI_TITLE"], $this->arrayFieldNameForException["triggerTitle"]);

            //TRI_PARAMS
            if (isset($arrayData["TRI_PARAMS"])) {
                $arrayData["TRI_PARAMS"] = array_change_key_case($arrayData["TRI_PARAMS"], CASE_UPPER);
            }

            //Create
            $trigger = new \Triggers();

            $arrayData["PRO_UID"] = $processUid;

            $result = $trigger->create($arrayData);

            $triggerUid = $trigger->getTriUid();

            $this->setData($libraryName, $methodName, $triggerUid, $arrayParamData);

            //Return
            unset($arrayData["PRO_UID"]);

            $arrayData = array_merge(array("TRI_UID" => $triggerUid), $arrayData);

            if (!$this->formatFieldNameInUppercase) {
                if (isset($arrayData["TRI_PARAMS"])) {
                    $arrayData["TRI_PARAMS"] = array_change_key_case($arrayData["TRI_PARAMS"], CASE_LOWER);
                }

                $arrayData = array_change_key_case($arrayData, CASE_LOWER);
            }

            return $arrayData;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Update Trigger
     *
     * @param string $libraryName Library name
     * @param string $methodName  Method name
     * @param string $triggerUid  Unique id of Trigger
     * @param array  $arrayData   Data
     *
     * return array Return data of the Trigger updated
     */
    public function update($libraryName, $methodName, $triggerUid, $arrayData)
    {
        try {
            $arrayData = array_change_key_case($arrayData, CASE_UPPER);

            //Verify data
            $this->throwExceptionIfLibraryAndMethodIsInvalidForTrigger($libraryName, $methodName, $triggerUid, $this->arrayFieldNameForException["libraryName"], $this->arrayFieldNameForException["methodName"], $this->arrayFieldNameForException["triggerUid"]);

            //Load Trigger
            $trigger = new \Triggers();

            $arrayTriggerData = $trigger->load($triggerUid);

            $processUid = $arrayTriggerData["PRO_UID"];

            //Verify data
            $process = new \ProcessMaker\BusinessModel\Process();
            $trigger = new \ProcessMaker\BusinessModel\Trigger();

            $process->throwExceptionIfDataNotMetFieldDefinition($arrayData, $this->arrayFieldDefinition, $this->arrayFieldNameForException, false);

            if (isset($arrayData["TRI_PARAMS"])) {
                $arrayParamData = $this->throwExceptionIfDataNotMetTriggerParamsDefinition($libraryName, $methodName, $arrayData, $this->arrayFieldNameForException["libraryName"], $this->arrayFieldNameForException["methodName"], $this->arrayFieldNameForException["triggerParams"]);
            }

            if (isset($arrayData["TRI_TITLE"])) {
                $trigger->throwExceptionIfExistsTitle($processUid, $arrayData["TRI_TITLE"], $this->arrayFieldNameForException["triggerTitle"], $triggerUid);
            }

            //TRI_PARAMS
            if (isset($arrayData["TRI_PARAMS"])) {
                $arrayData["TRI_PARAMS"] = array_change_key_case($arrayData["TRI_PARAMS"], CASE_UPPER);
            }

            //Update
            $trigger = new \Triggers();

            $arrayData["TRI_UID"] = $triggerUid;

            $result = $trigger->update($arrayData);

            if (isset($arrayData["TRI_PARAMS"])) {
                $this->setData($libraryName, $methodName, $triggerUid, $arrayParamData);
            }

            //Return
            unset($arrayData["TRI_UID"]);

            if (!$this->formatFieldNameInUppercase) {
                if (isset($arrayData["TRI_PARAMS"])) {
                    $arrayData["TRI_PARAMS"] = array_change_key_case($arrayData["TRI_PARAMS"], CASE_LOWER);
                }

                $arrayData = array_change_key_case($arrayData, CASE_LOWER);
            }

            return $arrayData;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Get Method of the Library
     *
     * @param string $libraryName Library name
     * @param string $methodName  Method name
     *
     * return array Return an array with the Method of the Library
     */
    public function getMethod($libraryName, $methodName)
    {
        try {
            $arrayMethod = array();

            //Verify data
            $arrayMethodInputParam = $this->methodGetInputParams($libraryName, $methodName);
            $arrayMethodOutputParam = $this->methodGetOutputParams($libraryName, $methodName);

            //Get data
            $library = $this->library->getLibraryDefinition($this->libraryGetLibraryName($libraryName));
            $method = $library->methods[$methodName];

            $arrayMethod[$this->getFieldNameByFormatFieldName("FN_NAME")] = trim($method->info["name"]);
            $arrayMethod[$this->getFieldNameByFormatFieldName("FN_DESCRIPTION")] = trim(str_replace("*", "", implode("", $method->info["description"])));
            $arrayMethod[$this->getFieldNameByFormatFieldName("FN_LABEL")] = trim($method->info["label"]);
            $arrayMethod[$this->getFieldNameByFormatFieldName("FN_LINK")] = (isset($method->info["link"]) && trim($method->info["link"]) != "")? trim($method->info["link"]) : "";

            if ($this->formatFieldNameInUppercase) {
                $arrayMethodInputParam = \G::array_change_key_case2($arrayMethodInputParam, CASE_UPPER);
                $arrayMethodOutputParam = \G::array_change_key_case2($arrayMethodOutputParam, CASE_UPPER);
            }

            if (count($arrayMethodInputParam) > 0) {
                $arrayMethod[$this->getFieldNameByFormatFieldName("FN_PARAMS")][$this->getFieldNameByFormatFieldName("INPUT")] = $arrayMethodInputParam;
            }

            if (count($arrayMethodOutputParam) > 0) {
                $arrayMethod[$this->getFieldNameByFormatFieldName("FN_PARAMS")][$this->getFieldNameByFormatFieldName("OUTPUT")] = $arrayMethodOutputParam;
            }

            //Return
            return $arrayMethod;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Get Library
     *
     * @param string $libraryName Library name
     *
     * return array Return an array with the Library
     */
    public function getLibrary($libraryName)
    {
        try {
            $arrayLibrary = array();

            //Verify data
            $this->throwExceptionIfNotExistsLibrary($libraryName, $this->arrayFieldNameForException["libraryName"]);

            //Get data
            $library = $this->library->getLibraryDefinition($this->libraryGetLibraryName($libraryName));

            $arrayLibrary[$this->getFieldNameByFormatFieldName("LIB_NAME")] = $libraryName;
            $arrayLibrary[$this->getFieldNameByFormatFieldName("LIB_TITLE")] = trim($library->info["name"]);
            $arrayLibrary[$this->getFieldNameByFormatFieldName("LIB_DESCRIPTION")] = trim(str_replace("*", "", implode("", $library->info["description"])));
            $arrayLibrary[$this->getFieldNameByFormatFieldName("LIB_ICON")] = (isset($library->info["icon"]) && trim($library->info["icon"]) != "")? trim($library->info["icon"]) : "/images/browse.gif";
            $arrayLibrary[$this->getFieldNameByFormatFieldName("LIB_CLASS_NAME")] = trim($library->info["className"]);

            $arrayMethod = array();

            if (count($library->methods) > 0) {
                ksort($library->methods, SORT_STRING);

                foreach ($library->methods as $key => $value) {
                    $methodName = $key;

                    $arrayMethod[] = $this->getMethod($libraryName, $methodName);
                }
            }

            $arrayLibrary[$this->getFieldNameByFormatFieldName("LIB_FUNCTIONS")] = $arrayMethod;

            //Return
            return $arrayLibrary;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Get data of a Trigger
     *
     * @param string $libraryName Library name
     * @param string $methodName  Method name
     * @param string $triggerUid  Unique id of Trigger
     *
     * return array Return an array with data of a Trigger
     */
    public function getTrigger($libraryName, $methodName, $triggerUid)
    {
        try {
            //Verify data
            $this->throwExceptionIfLibraryAndMethodIsInvalidForTrigger($libraryName, $methodName, $triggerUid, $this->arrayFieldNameForException["libraryName"], $this->arrayFieldNameForException["methodName"], $this->arrayFieldNameForException["triggerUid"]);

            //Get data
            $trigger = new \Triggers();

            $arrayTriggerData = $trigger->load($triggerUid);

            $arrayTriggerData["TRI_PARAM"] = unserialize($arrayTriggerData["TRI_PARAM"]);

            $arrayMethodInputParam = $this->methodGetInputParams($libraryName, $methodName);
            $arrayMethodOutputParam = $this->methodGetOutputParams($libraryName, $methodName);

            //Params
            $arrayMethodInputParamValue = array();

            foreach ($arrayMethodInputParam as $key => $value) {
                $paramName = $value["name"];
                $paramDefaultValue = (isset($value["default_value"]))? $value["default_value"] : "";

                $arrayMethodInputParamValue[$paramName] = (isset($arrayTriggerData["TRI_PARAM"]["params"][$paramName]))? $arrayTriggerData["TRI_PARAM"]["params"][$paramName] : $paramDefaultValue;
            }

            $arrayMethodOutputParamValue = array();

            foreach ($arrayMethodOutputParam as $key => $value) {
                $paramName = $value["name"];
                $paramRequired = $value["required"];

                if ($paramRequired) {
                    if (isset($arrayTriggerData["TRI_PARAM"]["params"][strtolower($paramName)])) {
                        $paramValue = trim($arrayTriggerData["TRI_PARAM"]["params"][strtolower($paramName)]);
                    } else {
                        if (isset($arrayTriggerData["TRI_PARAM"]["params"][strtoupper($paramName)])) {
                            $paramValue = trim($arrayTriggerData["TRI_PARAM"]["params"][strtoupper($paramName)]);
                        } else {
                            $paramValue = "";
                        }
                    }

                    $arrayMethodOutputParamValue[$paramName] = $paramValue;
                }
            }

            if (count($arrayMethodInputParamValue) > 0) {
                $arrayTriggerData[$this->getFieldNameByFormatFieldName("TRI_PARAMS")][$this->getFieldNameByFormatFieldName("INPUT")] = $arrayMethodInputParamValue;
            }

            if (count($arrayMethodOutputParamValue) > 0) {
                $arrayTriggerData[$this->getFieldNameByFormatFieldName("TRI_PARAMS")][$this->getFieldNameByFormatFieldName("OUTPUT")] = $arrayMethodOutputParamValue;
            }

            //Return
            unset($arrayTriggerData["PRO_UID"]);
            unset($arrayTriggerData["TRI_WEBBOT"]);
            unset($arrayTriggerData["TRI_PARAM"]);

            if (!$this->formatFieldNameInUppercase) {
                $arrayTriggerData = array_change_key_case($arrayTriggerData, CASE_LOWER);
            }

            return $arrayTriggerData;
        } catch (\Exception $e) {
            throw $e;
        }
    }
}

