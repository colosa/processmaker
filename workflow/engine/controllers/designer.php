<?php

use ProcessMaker\Core\System;
use ProcessMaker\Plugins\PluginRegistry;

/**
 * Designer Controller
 *
 * @inherits Controller
 * @access public
 */

use Maveriks\Util\ClassLoader;
use \OAuth2\Request;
use \ProcessMaker\BusinessModel\Light\Tracker;
use \ProcessMaker\Services\OAuth2\Server;

class Designer extends Controller
{
    protected $clientId = 'x-pm-local-client';

    public function __construct()
    {

    }

    /**
     * Index Action
     *
     * @param string $httpData (opional)
     */
    public function index($httpData)
    {
        $proUid = isset($httpData->prj_uid) ? $httpData->prj_uid : '';
        $appUid = isset($httpData->app_uid) ? $httpData->app_uid : '';
        $proReadOnly = isset($httpData->prj_readonly) ? $httpData->prj_readonly : 'false';

        $clientToken = $this->getCredentials($httpData);
        $debug = false; //System::isDebugMode();

        $consolidated = 0;
        $enterprise = 0;
        $distribution = 0;

        /*----------------------------------********---------------------------------*/

        $this->setVar('prj_uid', htmlspecialchars($proUid));
        $this->setVar('app_uid', htmlspecialchars($appUid));
        $this->setVar('consolidated', $consolidated);
        $this->setVar('enterprise', $enterprise);
        $this->setVar('prj_readonly', $proReadOnly);
        $this->setVar('credentials', base64_encode(json_encode($clientToken)));
        $this->setVar('isDebugMode', $debug);
        $this->setVar("distribution", $distribution);
        $this->setVar("SYS_SYS", SYS_SYS);
        $this->setVar("SYS_LANG", SYS_LANG);
        $this->setVar("SYS_SKIN", SYS_SKIN);
        $this->setVar('HTTP_SERVER_HOSTNAME', System::getHttpServerHostnameRequestsFrontEnd());

        if ($debug) {
            if (!file_exists(PATH_HTML . "lib-dev/pmUI/build.cache")) {
                throw new RuntimeException("Development JS Files were are not generated!.\nPlease execute: \$>rake pmBuildDebug in pmUI project");
            }
            if (!file_exists(PATH_HTML . "lib-dev/mafe/build.cache")) {
                throw new RuntimeException("Development JS Files were are not generated!.\nPlease execute: \$>rake pmBuildDebug in MichelangeloFE project");
            }

            $mafeFiles = file(PATH_HTML . "lib-dev/mafe/build.cache", FILE_IGNORE_NEW_LINES);
            $mafeCssFiles = array();
            $mafeJsFiles = array();

            foreach ($mafeFiles as $file) {
                if (substr($file, -3) == ".js") {
                    $mafeJsFiles[] = $file;
                } else {
                    $mafeCssFiles[] = $file;
                }
            }

            $this->setVar('pmuiJsCacheFile', file(PATH_HTML . "lib-dev/pmUI/build.cache", FILE_IGNORE_NEW_LINES));
            $this->setVar('pmuiCssCacheFile', file(PATH_HTML . "lib-dev/pmUI/css.cache", FILE_IGNORE_NEW_LINES));

            $this->setVar('designerCacheFile', file(PATH_HTML . "lib-dev/mafe/applications.cache", FILE_IGNORE_NEW_LINES));
            $this->setVar('mafeJsFiles', $mafeJsFiles);
            $this->setVar('mafeCssFiles', $mafeCssFiles);
        } else {
            $buildhashFile = PATH_HTML . "lib/buildhash";
            if (!file_exists($buildhashFile)) {
                throw new RuntimeException("CSS and JS Files were are not generated!.\nPlease review install process");
            }
            $buildhash = file_get_contents($buildhashFile);
            $this->setVar('buildhash', $buildhash);
        }

        $translationMafe = "/translations/translationsMafe.js";
        $this->setVar('translationMafe', $translationMafe);
        if (!file_exists(PATH_HTML . "translations" . PATH_SEP . 'translationsMafe' . ".js")) {
            $translation = new Translation();
            $translation->generateFileTranslationMafe();
        }

        $this->setVar('sys_skin', SYS_SKIN);

        //Verify user
        $criteria = new Criteria('workflow');

        $criteria->addSelectColumn(OauthAccessTokensPeer::ACCESS_TOKEN);
        $criteria->addSelectColumn(OauthAccessTokensPeer::USER_ID);
        $criteria->add(OauthAccessTokensPeer::ACCESS_TOKEN, $clientToken['access_token'], Criteria::EQUAL);
        $rsCriteria = OauthAccessTokensPeer::doSelectRS($criteria);
        $rsCriteria->setFetchmode(ResultSet::FETCHMODE_ASSOC);

        if ($rsCriteria->next()) {
            $row = $rsCriteria->getRow();

            $user = new \ProcessMaker\BusinessModel\User();

            if ($user->checkPermission($row['USER_ID'], 'PM_FACTORY') || $proReadOnly == 'true') {
                $this->setView('designer/index');
            } else {
                G::header('Location: /errors/error403.php');
                die();
            }
        }

        //plugin set source path
        $sourceCss = array();
        $sourceJs = array();

        $pluginRegistry = PluginRegistry::loadSingleton();
        $srcPath = $pluginRegistry->getDesignerSourcePath();

        foreach ($srcPath as $value) {
            $ext = pathinfo($value->pathFile, PATHINFO_EXTENSION);
            if ($ext === "css") {
                $sourceCss[] = $value->pathFile;
            }
            if ($ext === "js") {
                $sourceJs[] = $value->pathFile;
            }
        }

        $this->setVar('sourceCss', $sourceCss);
        $this->setVar('sourceJs', $sourceJs);

        $this->render();
    }

    protected function getClientCredentials()
    {
        $oauthQuery = new ProcessMaker\Services\OAuth2\PmPdo($this->getDsn());
        return $oauthQuery->getClientDetails($this->clientId);
    }

    protected function getAuthorizationCode($client)
    {
        Server::setDatabaseSource($this->getDsn());
        Server::setPmClientId($client['CLIENT_ID']);

        $oauthServer = new Server();

        if (isset($client["tracker_designer"]) && $client["tracker_designer"] == 1) {
            $_SESSION["USER_LOGGED"] = "00000000000000000000000000000001";
        }

        $userId = $_SESSION['USER_LOGGED'];
        $authorize = true;
        $_GET = array_merge($_GET, array(
            'response_type' => 'code',
            'client_id' => $client['CLIENT_ID'],
            'scope' => implode(' ', $oauthServer->getScope())
        ));

        $response = $oauthServer->postAuthorize($authorize, $userId, true);
        $code = substr($response->getHttpHeader('Location'), strpos($response->getHttpHeader('Location'), 'code=') + 5, 40);

        if (isset($client["tracker_designer"]) && $client["tracker_designer"] == 1) {
            unset($_SESSION["USER_LOGGED"]);
        }

        return $code;
    }

    private function getDsn()
    {
        list($host, $port) = strpos(DB_HOST, ':') !== false ? explode(':', DB_HOST) : array(DB_HOST, '');
        $port = empty($port) ? '' : ";port=$port";
        $dsn = DB_ADAPTER . ':host=' . $host . ';dbname=' . DB_NAME . $port;

        return array('dsn' => $dsn, 'username' => DB_USER, 'password' => DB_PASS);
    }

    /**
     * Return credentials oauth2
     *
     * @param object $httpData
     * @return array credentials
     */
    public function getCredentials($httpData = null)
    {
        $client = $this->getClientCredentials();

        if (!empty($httpData->tracker_designer) && $httpData->tracker_designer == 1) {
            try {
                if (!isset($_SESSION['CASE']) && !isset($_SESSION['PIN'])) {
                    throw (new \Exception(
                        \G::LoadTranslation('ID_CASE_NOT_EXISTS') . "\n" . \G::LoadTranslation('ID_PIN_INVALID')
                    ));
                }
                Tracker::authentication($_SESSION['CASE'], $_SESSION['PIN']);
            } catch (\Exception $e) {
                Bootstrap::registerMonolog('CaseTracker', 400, $e->getMessage(), [], SYS_SYS, 'processmaker.log');
                \G::header('Location: /errors/error403.php');
                die();
            }
            $client["tracker_designer"] = 1;
        }

        $authCode = $this->getAuthorizationCode($client);

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
}
