<?php
namespace ProcessMaker\Project;

use ProcessMaker\Core\System;
use ProcessMaker\Util\Logger;

/**
 * Class Handler
 *
 * @package ProcessMaker\Project
 * @author Erik Amaru Ortiz <aortiz.erik@gmail.com, erik@colosa.com>
 */
abstract class Handler
{
    public static function load($uid)
    {   // This method must be implemented on children classes, this is not declared abstract since PHP 5.3.x
        // don't allow any more static abstract methods.
        return null;
    }

    public abstract function create($data);
    //public abstract function update();
    public abstract function remove();

    protected static function diffArrayByKey($key, $list, $targetList)
    {
        $uid = array();
        $diff = array();

        foreach ($list as $item) {
            if (array_key_exists($key, $item)) {
                $uid[] = $item[$key];
            }
        }

        foreach ($targetList as $item) {
            if (! in_array($item[$key], $uid)) {
                $diff[] = $item[$key];
            }
        }

        return $diff;
    }

    protected static function getArrayChecksum($list, $key = null)
    {
        $checksum = array();

        foreach ($list as $k => $item) {
            if (empty($key)) {
                $checksum[$k] = self::getChecksum($item);
            } else {
                $checksum[$item[$key]] = self::getChecksum($item);
            }
        }

        return $checksum;
    }

    protected static function getChecksum($data)
    {
        if (! is_string($data)) {
            ksort($data);
            $data = print_r($data, true);
        }

        return sha1($data);
    }

    public static function filterCollectionArrayKeys($data, $filter = array())
    {
        $result = array();

        foreach ($data as $row) {
            $result[] = self::filterArrayKeys($row, $filter);
        }

        return $result;
    }

    public static function filterArrayKeys($data, $filter = array())
    {
        $result = array();

        foreach ($data as $key => $value) {
            if (! in_array($key, $filter)) {
                $result[$key] = $value;
            }
        }

        return $result;
    }

    public static function isEquals($array, $arrayCompare)
    {
        //self::log("ONE: ", $array, "TWO: ", $arrayCompare);
        //$ret = array_diff_assoc("ONE: ", $array, "TWO: ", $arrayCompare);
        return (self::getChecksum($array) === self::getChecksum($arrayCompare));
    }

    /**
     * Log in ProcessMaker Standard Output if debug mode is enabled.
     *
     * @author Erik Amaru Ortiz <aortiz.erik at icloud dot com>
     * @internal param $args this method receives N-Arguments dynamically with any type, string, array, object, etc
     *                       it means that you ca use it by example:
     *
     * self::log("Beginning transaction");
     * self::log("Method: ", __METHOD__, 'Returns: ', $result);
     *
     */
    public static function logstr($str)
    {
        if (System::isDebugMode()) {
            Logger::getInstance()->setLog($str);
        }
    }

    public static function logInline()
    {
        if (System::isDebugMode()) {
            call_user_func_array(array(Logger::getInstance(), 'setLogInline'), func_get_args());
        }
    }

    public static function log()
    {
        if (System::isDebugMode()) {
            $logger = Logger::getInstance();
            call_user_func_array(array($logger, 'setLogLine'), func_get_args());
        }
    }
}