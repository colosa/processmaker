<?php
namespace ProcessMaker\Services;

/**
 * Abstract Class Api
 *
 * Api class be be extended by Restler Classes
 *
 * @package ProcessMaker\Services
 * @author Erik Amaru Ortiz <aortiz.erik@gmail.com, erik@colosa.com>
 */
abstract class Api
{
    private static $workspace;
    private static $userId;

    const STAT_CREATED = 201;
    const STAT_APP_EXCEPTION = 400;

    public function __construct()
    {
        self::$workspace = null;
    }

    public static function setWorkspace($workspace)
    {
        self::$workspace = $workspace;
    }

    public function getWorkspace()
    {
        return self::$workspace;
    }

    public static function setUserId($userId)
    {
        self::$userId = $userId;
    }

    public function getUserId()
    {
        return \ProcessMaker\Services\OAuth2\Server::getUserId();
    }
}

