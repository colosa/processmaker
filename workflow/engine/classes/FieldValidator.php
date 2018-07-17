<?php

class FieldValidator
{
    /**
     * Checks if value of an variable is integer number
     *
     * @param int $num The variable being evaluated
     *
     * @return bool Returns true if $num is an integer, false otherwise
     */
    public static function isInt($num)
    {
        $num = $num . "";

        return (preg_match("/^[\+\-]?(?:0|[1-9]\d*)$/", $num))? true : false;
    }

    /**
     * Checks if value of an variable is real number
     *
     * @param float $num The variable being evaluated
     *
     * @return bool Returns true if $num is an real, false otherwise
     */
    public static function isReal($num)
    {
        $num = $num . "";

        return (preg_match("/^[\+\-]?(?:0|[1-9]\d*)(?:\.\d+)?$/", $num))? true : false;
    }

    /**
     * Checks if value of an variable is boolean
     *
     * @param bool $bool The variable being evaluated
     *
     * @return bool Returns true if $bool is an boolean, false otherwise
     */
    public static function isBool($bool)
    {
        if (is_bool($bool) === true) {
            return true;
        }

        $bool = $bool . "";

        return (preg_match("/^(?:true|false)$/i", $bool))? true : false;
    }

    /**
     * Checks if value of an variable have valid URL format
     *
     * @param string $url The variable being evaluated
     *
     * @return bool Returns true if $bool have valid URL format, false otherwise
     */
    public static function isUrl($url)
    {
        return (preg_match("/(((^https?)|(^ftp)):\/\/([\-\w]+\.)+\w{2,3}(\/[%\-\w]+(\.\w{2,})?)*(([\w\-\.\?\/+\\@&#;`~=%!]*)(\.\w{2,})?)*\/?)/i", $url))? true : false;
    }

    /**
     * Checks if value of an variable have valid email format
     *
     * @param string $email The variable being evaluated
     *
     * @return bool Returns true if $bool have valid email format, false otherwise
     */
    public static function isEmail($email)
    {
        return (preg_match("/^(\w+)([\-+.\'][\w]+)*@(\w[\-\w]*\.){1,5}([A-Za-z]){2,6}$/", $email))? true : false;
    }

    /**
     * Checks if value of an variable have valid IP format
     *
     * @param string $ip The variable being evaluated
     *
     * @return bool Returns true if $bool have valid IP format, false otherwise
     */
    public static function isIp($ip)
    {
        return (preg_match("/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}$/", $ip))? true : false;
    }

    /**
     * Validate fields
     *
     * @param array $arrayData           Fields to be validate
     * @param array $arrayDataValidators Validator for each field
     *
     * @return array Returns an array with key "succes" in true or false
     */
    public static function validate($arrayData, $arrayDataValidators)
    {
        $result = array();
        $arrayError = array();

        $result["success"] = true;

        try {
            if (!is_array($arrayData)) {
                throw (new Exception("Fields no is array"));
            }

            if (!is_array($arrayDataValidators)) {
                throw (new Exception("Validators no is array"));
            }

            if (count($arrayData) == 0) {
                throw (new Exception("Fields is empty"));
            }

            if (count($arrayDataValidators) == 0) {
                throw (new Exception("Validators is empty"));
            }

            foreach ($arrayDataValidators as $key1 => $value1) {
                $field = $key1;
                $arrayValidators = $value1;

                if (is_array($arrayValidators) && count($arrayValidators) > 0) {
                    if (isset($arrayValidators["type"])) {
                        if (isset($arrayData[$field])) {
                            switch ($arrayValidators["type"]) {
                                case "int":
                                    if (!self::isInt($arrayData[$field])) {
                                        $result["success"] = false;

                                        $arrayError[] = str_replace(
                                            array("{0}"),
                                            array($field),
                                            "Field \"{0}\" not is an integer number"
                                        );
                                    }
                                    break;
                                case "real":
                                    if (!self::isReal($arrayData[$field])) {
                                        $result["success"] = false;

                                        $arrayError[] = str_replace(
                                            array("{0}"),
                                            array($field),
                                            "Field \"{0}\" not is an real number"
                                        );
                                    }
                                    break;
                                case "bool":
                                case "boolean":
                                    if (!self::isBool($arrayData[$field])) {
                                        $result["success"] = false;

                                        $arrayError[] = str_replace(
                                            array("{0}"),
                                            array($field),
                                            "Field \"{0}\" not is an boolean"
                                        );
                                    }
                                    break;
                                default:
                                    //string
                                    break;
                            }
                        }
                    }

                    if (isset($arrayValidators["validation"])) {
                        if (isset($arrayData[$field])) {
                            switch ($arrayValidators["validation"]) {
                                case "url":
                                    if (!self::isUrl($arrayData[$field])) {
                                        $result["success"] = false;

                                        $arrayError[] = str_replace(
                                            array("{0}"),
                                            array($field),
                                            "Field \"{0}\" have not an valid URL format"
                                        );
                                    }
                                    break;
                                case "email":
                                    if (!self::isEmail($arrayData[$field])) {
                                        $result["success"] = false;

                                        $arrayError[] = str_replace(
                                            array("{0}"),
                                            array($field),
                                            "Field \"{0}\" have not an valid email format"
                                        );
                                    }
                                    break;
                                case "ip":
                                    if (!self::isIp($arrayData[$field])) {
                                        $result["success"] = false;

                                        $arrayError[] = str_replace(
                                            array("{0}"),
                                            array($field),
                                            "Field \"{0}\" have not an valid IP format"
                                        );
                                    }
                                    break;
                            }
                        }
                    }

                    if (isset($arrayValidators["min_size"])) {
                        if (isset($arrayData[$field]) && !(strlen($arrayData[$field] . "") >= (int)($arrayValidators["min_size"]))) {
                            $result["success"] = false;

                            $arrayError[] = str_replace(
                                array("{0}", "{1}", "{2}"),
                                array($field, $arrayValidators["min_size"], strlen($arrayData[$field] . "")),
                                "Field \"{0}\" should be min {1} chars, {2} given"
                            );
                        }
                    }

                    if (isset($arrayValidators["required"])) {
                        if ($arrayValidators["required"] && (!isset($arrayData[$field]) || (isset($arrayData[$field]) && $arrayData[$field] . "" == ""))) {
                            $result["success"] = false;

                            $arrayError[] = str_replace(
                                array("{0}"),
                                array($field),
                                "Field \"{0}\" is required"
                            );
                        }
                    }
                }
            }
        } catch (Exception $e) {
            $arrayError[] = $e->getMessage();

            $result["success"] = false;
        }

        $result["errors"] = $arrayError;

        return $result;
    }
}
