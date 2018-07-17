<?php
namespace ProcessMaker\Services\OAuth2;

use Luracast\Restler\iAuthenticate;
use Luracast\Restler\RestException;
use ProcessMaker\Core\System;

class Server implements iAuthenticate
{
    /**
     * @var OAuth2_Server
     */
    protected $server;
    /**
     * @var OAuth2_Storage_Pdo
     */
    protected $storage;
    protected $scope = array();

    protected static $pmClientId;
    protected static $userId;
    protected static $dbUser;
    protected static $dbPassword;
    protected static $dsn;
    protected static $dbUserRBAC;
    protected static $dbPasswordRBAC;
    protected static $dsnRBAC;
    protected static $isRBAC = false;
    protected static $workspace;

    public function __construct()
    {
        require_once 'PmPdo.php';

        $this->scope = array(
            'view_processes' => 'View Processes',
            'edit_processes' => 'Edit Processes',
            '*' => '*'
        );

        // $dsn is the Data Source Name for your database, for exmaple "mysql:dbname=my_oauth2_db;host=localhost"
        $cnn = array('dsn' => self::$dsn, 'username' => self::$dbUser, 'password' => self::$dbPassword);

        if (self::$isRBAC) {
            $config = array();
            $cnnrbac = array('dsn' => self::$dsnRBAC, 'username' => self::$dbUserRBAC, 'password' => self::$dbPasswordRBAC);
            $this->storage = new PmPdo($cnn, $config, $cnnrbac);
        } else {
            $this->storage = new PmPdo($cnn);
        }

        // Pass a storage object or array of storage objects to the OAuth2 server class
        $this->server = new \OAuth2\Server($this->storage, array('allow_implicit' => true, 'access_lifetime' => 86400));

        $this->server->setConfig('enforce_state', false);

        // Add the "Authorization Code" grant type (this is where the oauth magic happens)
        $this->server->addGrantType(new \OAuth2\GrantType\AuthorizationCode($this->storage));

        // Add the "Client Credentials" grant type (it is the simplest of the grant types)
        $this->server->addGrantType(new \ProcessMaker\Services\OAuth2\PmClientCredentials($this->storage));

        // Add the "Refresh token" grant type
        $this->server->addGrantType(new \OAuth2\GrantType\RefreshToken(
            $this->storage,
            array("always_issue_new_refresh_token" => true)
        ));

        // create some users in memory
        //$users = array('bshaffer' => array('password' => 'brent123', 'first_name' => 'Brent', 'last_name' => 'Shaffer'));
        // create a storage object
        //$storage = new \OAuth2\Storage\Memory(array('user_credentials' => $users));
        // create the grant type
        $grantType = new \OAuth2\GrantType\UserCredentials($this->storage);
        // add the grant type to your OAuth server
        $this->server->addGrantType($grantType);

        $scope = new \OAuth2\Scope(array('supported_scopes' => array_keys($this->scope)));
        $this->server->setScopeUtil($scope);
    }

    /**
     * @url POST /:token/expire
     *
     */
    public function doPostExpireToken($token)
    {
        try {
            $this->storage->expireToken($token);
        } catch (\Exception $e) {
            throw new RestException(400, $e->getMessage());
        }
    }

    /**
     * @url DELETE /:token
     *
     */
    public function doDeleteToken($token)
    {
        try {
            $this->storage->deleteToken($token);
        } catch (\Exception $e) {
            throw new RestException(400, $e->getMessage());
        }
    }

    public static function setDatabaseSource($user, $password = '', $dsn = '')
    {
        if (is_array($user)) {
            self::$dbUser = $user['username'];
            self::$dbPassword = $user['password'];
            self::$dsn = $user['dsn'];
        } else {
            self::$dbUser = $user;
            self::$dbPassword = $password;
            self::$dsn = $dsn;
        }
    }

    public static function setDatabaseSourceRBAC($user, $password = '', $dsn = '')
    {
        if (is_array($user)) {
            self::$dbUserRBAC = $user['username'];
            self::$dbPasswordRBAC = $user['password'];
            self::$dsnRBAC = $user['dsn'];
            self::$isRBAC = true;
        } else {
            self::$dbUserRBAC = $user;
            self::$dbPasswordRBAC = $password;
            self::$dsnRBAC = $dsn;
            self::$isRBAC = true;
        }
    }

    public static function setWorkspace($workspace)
    {
        self::$workspace = $workspace;
    }

    public function index()
    {
        $http = \G::is_https() ? 'https' : 'http';
        $host = $_SERVER['SERVER_NAME'] . ($_SERVER['SERVER_PORT'] != '80' ? ':' . $_SERVER['SERVER_PORT'] : '');
        $host = $http .'://'. $host;

        $applicationsLink = sprintf('%s/%s/oauth2/apps', $host, SYS_SYS);
        $authorizationLink = sprintf('%s/%s/oauth2/authorize?response_type=code&client_id=[the-client-id]&scope=*', $host, SYS_SYS);

        $view = new \Maveriks\Pattern\Mvc\SmartyView(PATH_CORE . "templates/oauth2/index.html");
        $view->assign('host', $host);
        $view->assign('workspace', self::$workspace);

        $view->render();
    }


    /**
     * @view oauth2/server/register.php
     * @format HtmlFormat
     */
    public function register()
    {
        static::$server->getResponse(\OAuth2\Request::createFromGlobals());
        return array('queryString' => $_SERVER['QUERY_STRING']);
    }

    /**
     * Stage 1: Client sends the user to this page
     *
     * User responds by accepting or denying
     *
     */
    public function authorize()
    {
        session_start();

        if (! isset($_SESSION['USER_LOGGED'])) {
            $http = \G::is_https() ? 'https' : 'http';
            $host = $http . '://' . $_SERVER['SERVER_NAME'] . ($_SERVER['SERVER_PORT'] != '80' ? ':' . $_SERVER['SERVER_PORT'] : '');
            $redirect = urlencode($host.'/'.self::$workspace.$_SERVER['REQUEST_URI']);

            $loginLink = sprintf('%s/sys%s/%s/%s/login/login?u=%s', $host, SYS_SYS, SYS_LANG, SYS_SKIN, $redirect);
            header('location: ' . $loginLink);
            die;
        }

        $this->scope = array(
            'view_processes' => 'View Processes',
            'edit_processes' => 'Edit Processes'
        );

        if (! array_key_exists('client_id', $_GET)) {
            throw new RestException(400, "Invalid request. The 'client_id' parameter is missing!");
        }
        if (! array_key_exists('response_type', $_GET)) {
            throw new RestException(400, "Invalid request. The 'response_type' parameter is missing!");
        }

        $clientId = $_GET['client_id'];
        $requestedScope = isset($_GET['scope']) ? $_GET['scope'] : '*';
        $requestedScope = empty($requestedScope) ? array() : explode(' ', $requestedScope);
        $client = $this->storage->getClientDetails($clientId);

        if (empty($client)) {
            // throw error, client does not exist.
            throw new RestException(400, "Error, unknown client. The client with id '".$clientId."' is not registered");
        }

        //echo '<pre>';print_r($client); echo '</pre>'; die;
        $client = array('name' => $client['client_name'], 'desc' => $client['client_description']);
        $user = array('name' => $_SESSION['USR_FULLNAME']);

        $view = new \Maveriks\Pattern\Mvc\SmartyView(PATH_CORE . "templates/oauth2/authorize.html");
        $view->assign('user', $user);
        $view->assign('client', $client);
        $view->assign('postUri', '/' . SYS_SYS . '/oauth2/authorize?' . $_SERVER['QUERY_STRING']);
        $view->render();
        exit();
    }

    /**
     * Stage 2: User response is captured here
     *
     * Success or failure is communicated back to the Client using the redirect
     * url provided by the client
     *
     * On success authorization code is sent along
     *
     * @format JsonFormat,UploadFormat
     */
    public function postAuthorize($authorize = null, $userId = null, $returnResponse = false, $cacheSessionValue = array())
    {
        @session_start();

        foreach ($cacheSessionValue as $key => $value) {
            $_SESSION[$key] = $value;
        }

        if (! isset($_SESSION['USER_LOGGED'])) {
            throw new RestException(400, "Local Authentication Error, user session is not started.");
        }

        if (empty($userId)) {
            $userId = $_SESSION['USER_LOGGED'];
        }
        if (empty($authorize)) {
            $authorize = array_key_exists('cancel', $_REQUEST)? false: true;
        }

        $request = \OAuth2\Request::createFromGlobals();
        $response = new \OAuth2\Response();

        $response = $this->server->handleAuthorizeRequest(
            $request,
            $response,
            (bool)$authorize,
            $userId
        );

        if ($returnResponse) {
            return $response;
        } else {
            $response->send();

            exit(0);
        }
    }


    /**
     * Stage 3: Client directly calls this api to exchange access token
     *
     * It can then use this access token to make calls to protected api
     *
     * @format JsonFormat,UploadFormat
     */
    public function postToken($request = null, $returnResponse = false)
    {
        \ProcessMaker\Policies\ControlUnderUpdating::verifyUnderUpgrading();
        
        // Handle a request for an OAuth2.0 Access Token and send the response to the client
        if ($request == null) {
            $request = \OAuth2\Request::createFromGlobals();
        }

        $response = $this->server->handleTokenRequest($request); //Set/Get token //PmPdo->setAccessToken()

        $token = $response->getParameters();

        if (array_key_exists('access_token', $token)
            && array_key_exists('refresh_token', $token)
        ) {
            if ($request == null) {
                session_start();
            }
            $data = $this->storage->getAccessToken($token['access_token']);

            // verify if the client is our local PM Designer client
            if ($data['client_id'] == self::getPmClientId()) {
                //error_log('do stuff - is a request from local pm client');
                //require_once "classes/model/PmoauthUserAccessTokens.php";

                $userToken = new \PmoauthUserAccessTokens();
                $userToken->setAccessToken($token['access_token']);
                $userToken->setRefreshToken($token['refresh_token']);
                $userToken->setUserId($data['user_id']);
                $userToken->setSessionId(session_id());
                $userToken->setSessionName(session_name());

                $userToken->save();
            }
        }

        if ($returnResponse) {
            return $response;
        } else {
            $response->send();

            exit(0);
        }
    }

    /**
     * Access verification method.
     *
     * API access will be denied when this method returns false
     *
     * @return boolean true when api access is allowed; false otherwise
     */
    public function __isAllowed()
    {
        $request = \OAuth2\Request::createFromGlobals();
        $allowed = $this->server->verifyResourceRequest($request);
        $token = $this->server->getAccessTokenData($request);
        self::$userId = $token['user_id'];
        // Session handling to prevent session lose in other places like, home, admin, etc
        // when user is using the new designer that have not session because it is using only the API

        if ($allowed && $token['client_id'] == self::getPmClientId()) {

            $pmAccessToken = new \PmoauthUserAccessTokens();
            $session = $pmAccessToken->getSessionData($token['ACCESS_TOKEN']);

            if ($session !== false &&  array_key_exists($session->getSessionName(), $_COOKIE)) {
                // increase the timeout for local php session cookie
                $config = \Bootstrap::getSystemConfiguration();
                if (isset($config['session.gc_maxlifetime'])) {
                    $lifetime = $config['session.gc_maxlifetime'];
                } else {
                    $lifetime = ini_get('session.gc_maxlifetime');
                }
                if (empty($lifetime)) {
                    $lifetime = 1440;
                }

                setcookie($session->getSessionName(), $_COOKIE[$session->getSessionName()], time() + $lifetime, "/", null, false, true);
            }

            //Set User Time Zone
            $user = \UsersPeer::retrieveByPK(self::$userId);

            if (!is_null($user)) {
                $userTimeZone = $user->getUsrTimeZone();

                if (trim($userTimeZone) == '') {
                    $arraySystemConfiguration = System::getSystemConfiguration('', '', SYS_SYS);

                    $userTimeZone = $arraySystemConfiguration['time_zone'];
                }

                $_SESSION['USR_TIME_ZONE'] = $userTimeZone;
            }
        }

        return $allowed;
    }

    public static function setPmClientId($clientId)
    {
        self::$pmClientId = $clientId;
    }

    public static function getPmClientId()
    {
        return self::$pmClientId;
    }

    public function getServer()
    {
        return $this->server;
    }

    public function getUserId()
    {
        return self::$userId;
    }

    public function getScope()
    {
        return array_keys($this->scope);
    }

    public function __getWWWAuthenticateString()
    {
        return "";
    }

}

