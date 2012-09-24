<?php
G::LoadClass('sessions');
G::LoadClass('wsBase');

class Services_Rest_Auth implements iAuthenticate
{
    public $realm = 'Restricted API';

    public static $userId = '';
    public static $authKey = '';

    function __isAuthenticated()
    {
        return true;
        if (array_key_exists('HTTP_AUTH_KEY', $_SERVER)) {
            $authKey = $_SERVER['HTTP_AUTH_KEY'];
        } elseif (array_key_exists('auth_key', $_GET)) {
            $authKey = $_GET['auth_key'];
        } else {
            throw new RestException(401, 'Authentication Required');
        }

        $sessions = new Sessions();
        $session  = $sessions->verifySession($authKey);

        if (is_array($session)) {
            $sesInfo = $sessions->getSessionUser($authKey);
            self::$userId = $sesInfo['USR_UID'];
            self::$authKey = $authKey;

            return true;
        }

        throw new RestException(401, 'Wrong Credentials!');
    }
}
