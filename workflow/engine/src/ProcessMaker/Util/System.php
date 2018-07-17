<?php

namespace ProcessMaker\Util;

use Bootstrap;
use Exception;
use Maveriks\Util\ClassLoader;
use PMPlugin;
use Processmaker\Core\System AS PmSystem;
use ProcessMaker\Plugins\Interfaces\PluginDetail;
use ProcessMaker\Plugins\PluginRegistry;
use ProcessMaker\Services\OAuth2\PmPdo;
use ProcessMaker\Services\OAuth2\Server;
use OAuth2\Request;
use Propel;

class System
{
    const CLIENT_ID = 'x-pm-local-client';

    /**
     * Get Time Zone
     *
     * @return string Return Time Zone
     * @throws Exception
     */
    public static function getTimeZone()
    {
        try {
            $arraySystemConfiguration = PmSystem::getSystemConfiguration('', '', SYS_SYS);

            //Return
            return $arraySystemConfiguration['time_zone'];
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Returns workspace objects from an array of workspace names.
     *
     * @param array $args an array of workspace names
     * @param bool $includeAll if true and no workspace is specified in args,
     *                          returns all available workspaces
     * @return array of workspace objects
     */
    public static function getWorkspacesFromArgs($args, $includeAll = true)
    {
        $workspaces = array();
        foreach ($args as $arg) {
            $workspaces[] = new \WorkspaceTools($arg);
        }
        if (empty($workspaces) && $includeAll) {
            $workspaces = PmSystem::listWorkspaces();
        }
        return $workspaces;
    }

    /**
     * Flush the cache files for the specified workspace.
     *
     * @param object $workspace
     */
    public static function flushCache($workspace)
    {
        try {
            //Update singleton file by workspace
            Bootstrap::setConstantsRelatedWs($workspace->name);
            Propel::init(PATH_CORE . "config/databases.php");
            $oPluginRegistry = PluginRegistry::loadSingleton();
            $items = PMPlugin::getListAllPlugins($workspace->name);
            /** @var PluginDetail $item */
            foreach ($items as $item) {
                if ($item->isEnabled()) {
                    require_once($item->getFile());
                    /** @var PluginDetail $details */
                    $details = $oPluginRegistry->getPluginDetails(basename($item->getFile()));
                    //Only if the API directory structure is defined
                    $pathApiDirectory = PATH_PLUGINS . $details->getFolder() . PATH_SEP . "src" . PATH_SEP . "Services" . PATH_SEP . "Api";
                    if (is_dir($pathApiDirectory)) {
                        $pluginSrcDir = PATH_PLUGINS . $details->getNamespace() . PATH_SEP . 'src';
                        $loader = ClassLoader::getInstance();
                        $loader->add($pluginSrcDir);
                        $oPluginRegistry->registerRestService($details->getNamespace());
                        $className = $details->getClassName();
                        if (class_exists($className)) {
                            $oPlugin = new $className($details->getNamespace(), $details->getFile());
                            $oPlugin->setup();
                        }
                    }
                }
            }

            //flush the cache files
            \G::rm_dir(PATH_C);
            \G::mk_dir(PATH_C, 0777);
            \G::rm_dir($workspace->path . "/cache");
            \G::mk_dir($workspace->path . "/cache", 0777);
            \G::rm_dir($workspace->path . "/cachefiles");
            \G::mk_dir($workspace->path . "/cachefiles", 0777);
            if (file_exists($workspace->path . '/routes.php')) {
                unlink($workspace->path . '/routes.php');
            }
        } catch (Exception $e) {
            throw new Exception("Error: cannot perform this task. " . $e->getMessage());
        }
    }

    /**
     * Get Token with USER_LOGGED saved in $_SESSION
     *
     * @return array
     */
    public static function tokenUserLogged()
    {
        $client = self::getClientCredentials();

        $authCode = self::getAuthorizationCodeUserLogged($client);

        $loader = ClassLoader::getInstance();
        $loader->add(PATH_TRUNK . 'vendor/bshaffer/oauth2-server-php/src/', "OAuth2");

        $request = array(
            'grant_type' => 'authorization_code',
            'code' => $authCode
        );
        $server = array(
            'REQUEST_METHOD' => 'POST'
        );
        $headers = array(
            "PHP_AUTH_USER" => $client['CLIENT_ID'],
            "PHP_AUTH_PW" => $client['CLIENT_SECRET'],
            "Content-Type" => "multipart/form-data;",
            "Authorization" => "Basic " . base64_encode($client['CLIENT_ID'] . ":" . $client['CLIENT_SECRET'])
        );

        $request = new Request(array(), $request, array(), array(), array(), $server, null, $headers);
        $oauthServer = new Server();
        $response = $oauthServer->postToken($request, true);
        $clientToken = $response->getParameters();
        $clientToken["client_id"] = $client['CLIENT_ID'];
        $clientToken["client_secret"] = $client['CLIENT_SECRET'];
        return $clientToken;
    }

    /**
     * Get client credentials
     * @return array
     */
    protected static function getClientCredentials()
    {
        $oauthQuery = new PmPdo(self::getDsn());
        return $oauthQuery->getClientDetails(self::CLIENT_ID);
    }

    /**
     * Get DNS of workspace
     * @return array
     */
    protected static function getDsn()
    {
        list($host, $port) = strpos(DB_HOST, ':') !== false ? explode(':', DB_HOST) : array(DB_HOST, '');
        $port = empty($port) ? '' : ";port=$port";
        $dsn = DB_ADAPTER . ':host=' . $host . ';dbname=' . DB_NAME . $port;

        return array('dsn' => $dsn, 'username' => DB_USER, 'password' => DB_PASS);
    }

    /**
     * Get authorization code for user logged in session
     * @param $client
     * @return bool|string
     */
    protected static function getAuthorizationCodeUserLogged($client)
    {
        Server::setDatabaseSource(self::getDsn());
        Server::setPmClientId($client['CLIENT_ID']);

        $oauthServer = new Server();

        $userId = $_SESSION['USER_LOGGED'];
        $authorize = true;
        $_GET = array_merge($_GET, array(
            'response_type' => 'code',
            'client_id' => $client['CLIENT_ID'],
            'scope' => implode(' ', $oauthServer->getScope())
        ));

        $response = $oauthServer->postAuthorize($authorize, $userId, true);
        $code = substr($response->getHttpHeader('Location'), strpos($response->getHttpHeader('Location'), 'code=') + 5, 40);
        return $code;
    }
}
