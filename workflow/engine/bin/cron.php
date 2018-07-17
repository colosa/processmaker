<?php

use ProcessMaker\Core\System;
use Illuminate\Foundation\Http\Kernel;

require_once __DIR__ . '/../../../gulliver/system/class.g.php';
require_once __DIR__ . '/../../../bootstrap/autoload.php';
require_once __DIR__ . '/../../../bootstrap/app.php';

try {
    //Set variables
    $cronName = pathinfo($_SERVER['SCRIPT_FILENAME'], PATHINFO_FILENAME);
    $osIsLinux = strtoupper(substr(PHP_OS, 0, 3)) != 'WIN';

    $arrayCronConfig = [
        'cron'             => ['title' => 'CRON'],
        'ldapcron'         => ['title' => 'LDAP Advanced CRON'],
        'messageeventcron' => ['title' => 'Message-Event CRON'],
        'timereventcron'   => ['title' => 'Timer-Event CRON'],
        'sendnotificationscron' => ['title' => 'Send-Notifications CRON']
    ];

    //Define constants
    define('PATH_SEP', ($osIsLinux)? '/' : '\\');

    $arrayPathToCron = [];
    $flagPathToCron = false;

    //Path to CRON by $_SERVER['SCRIPT_FILENAME']
    $arrayAux = explode(PATH_SEP, str_replace('engine' . PATH_SEP . 'bin', '', realpath($_SERVER['SCRIPT_FILENAME'])));

    array_pop($arrayAux);
    array_pop($arrayAux);

    if (!empty($arrayAux) && $arrayAux[count($arrayAux) - 1] == 'workflow') {
        $arrayPathToCron = $arrayAux;
        $flagPathToCron = true;
    }

    if (!$flagPathToCron) {
        throw new Exception('Error: Unable to execute the ' . $arrayCronConfig[$cronName]['title'] . ', the path is incorrect');
    }

    $pathHome = implode(PATH_SEP, $arrayPathToCron) . PATH_SEP;

    array_pop($arrayPathToCron);

    $pathTrunk = implode(PATH_SEP, $arrayPathToCron) . PATH_SEP;

    array_pop($arrayPathToCron);

    $pathOutTrunk = implode(PATH_SEP, $arrayPathToCron) . PATH_SEP;

    define('PATH_HOME',     $pathHome);
    define('PATH_TRUNK',    $pathTrunk);
    define('PATH_OUTTRUNK', $pathOutTrunk);

    //Check deprecated files
    switch ($cronName) {
        case 'ldapcron':
            $fileBinDeprecated = PATH_HOME . 'engine' . PATH_SEP . 'bin' . PATH_SEP . 'plugins' . PATH_SEP . 'ldapadvanced.php';

            if (file_exists($fileBinDeprecated)) {
                @unlink($fileBinDeprecated);

                if (file_exists($fileBinDeprecated)) {
                    throw new Exception('Error: ' . $arrayCronConfig[$cronName]['title'] . ' requires that the "' . $fileBinDeprecated . '" file has been deleted.');
                }
            }
            break;
    }

    //Include files
    require_once(PATH_HOME . 'engine' . PATH_SEP . 'config' . PATH_SEP . 'paths.php');
    require_once(PATH_TRUNK . 'framework' . PATH_SEP . 'src' . PATH_SEP . 'Maveriks' . PATH_SEP . 'Util' . PATH_SEP . 'ClassLoader.php');

    //Class Loader - /ProcessMaker/BusinessModel
    $classLoader = \Maveriks\Util\ClassLoader::getInstance();
    $classLoader->add(PATH_TRUNK . 'framework' . PATH_SEP . 'src' . PATH_SEP, 'Maveriks');
    $classLoader->add(PATH_TRUNK . 'workflow' . PATH_SEP . 'engine' . PATH_SEP . 'src' . PATH_SEP, 'ProcessMaker');
    $classLoader->add(PATH_TRUNK . 'workflow' . PATH_SEP . 'engine' . PATH_SEP . 'src' . PATH_SEP);
    $classLoader->addClass('Bootstrap', PATH_TRUNK . 'gulliver' . PATH_SEP . 'system' . PATH_SEP . 'class.bootstrap.php');

    $classLoader->addModelClassPath(PATH_TRUNK . 'workflow' . PATH_SEP . 'engine' . PATH_SEP . 'classes' . PATH_SEP . 'model' . PATH_SEP);
    //Load classes

    $arraySystemConfiguration = System::getSystemConfiguration();

    $e_all = (defined('E_DEPRECATED'))?            E_ALL  & ~E_DEPRECATED : E_ALL;
    $e_all = (defined('E_STRICT'))?                $e_all & ~E_STRICT     : $e_all;
    $e_all = ($arraySystemConfiguration['debug'])? $e_all                 : $e_all & ~E_NOTICE;

    app()->useStoragePath(realpath(PATH_DATA));
    app()->make(Kernel::class)->bootstrap();
    restore_error_handler();
    //Do not change any of these settings directly, use env.ini instead
    ini_set('display_errors',  $arraySystemConfiguration['debug']);
    ini_set('error_reporting', $e_all);
    ini_set('short_open_tag',  'On');
    ini_set('default_charset', 'UTF-8');
    ini_set('memory_limit',    $arraySystemConfiguration['memory_limit']);
    ini_set('soap.wsdl_cache_enabled', $arraySystemConfiguration['wsdl_cache']);
    ini_set('date.timezone', $arraySystemConfiguration['time_zone']);

    define('DEBUG_SQL_LOG',  $arraySystemConfiguration['debug_sql']);
    define('DEBUG_TIME_LOG', $arraySystemConfiguration['debug_time']);
    define('DEBUG_CALENDAR_LOG', $arraySystemConfiguration['debug_calendar']);
    define('MEMCACHED_ENABLED',  $arraySystemConfiguration['memcached']);
    define('MEMCACHED_SERVER',   $arraySystemConfiguration['memcached_server']);
    define('TIME_ZONE',          ini_get('date.timezone'));

    //CRON command options
    $arrayCommandOption = [
        'force' => '+force'
    ];

    //CRON status
    $flagIsRunning = false;
    $lastExecution = '';

    $force = false;

    if (in_array($arrayCommandOption['force'], $argv)) {
        unset($argv[array_search($arrayCommandOption['force'], $argv)]);

        $force = true;
    }

    if (!$force && file_exists(PATH_DATA . $cronName)) {
        //Windows flag
        //Get data of CRON file
        $arrayCron = unserialize(trim(file_get_contents(PATH_DATA . $cronName)));

        $flagIsRunning = (bool)((isset($arrayCron['flagIsRunning']))? $arrayCron['flagIsRunning'] : $arrayCron['bCronIsRunning']);
        $lastExecution = (isset($arrayCron['lastExecution']))? $arrayCron['lastExecution'] : $arrayCron['sLastExecution'];
    }

    if (!$force && $osIsLinux) {
        //Linux flag
        //Check if CRON it's running
        exec('ps -fea | grep ' . $cronName . '.php | grep -v grep', $arrayOutput);

        $counter = 0;

        foreach ($arrayOutput as $value) {
            if (preg_match('/^.*\s' . $cronName . '\.php.*$/', $value) ||
                preg_match('/^.*\s.+(?:\x2F|\x5C)' . $cronName . '\.php.*$/', $value)
            ) {
                $counter++;
            }
        }

        if ($counter > 1) {
            $flagIsRunning = true;
        }
    }

    if ($force || !$flagIsRunning) {
        //Start CRON
        $arrayCron = ['flagIsRunning' => '1', 'lastExecution' => date('Y-m-d H:i:s')];
        file_put_contents(PATH_DATA . $cronName, serialize($arrayCron));

        try {
            $cronSinglePath = PATH_CORE . 'bin' . PATH_SEP . 'cron_single.php';

            $workspace  = '';
            $dateSystem = date('Y-m-d H:i:s');
            $date       = '';
            $argvx      = '';

            for ($i = 1; $i <= count($argv) - 1; $i++) {
                if (!isset($argv[$i])) {
                    continue;
                }

                if (preg_match('/^\+w(.+)$/', $argv[$i], $arrayMatch)) {
                    $workspace = trim($arrayMatch[1], '"');
                } else {
                    $flagDate = false;

                    if (preg_match('/^\+d(.+)$/', $argv[$i], $arrayMatch) && in_array($cronName, ['cron'])) {
                        $date = trim($arrayMatch[1], '"');

                        $flagDate = true;
                    }

                    if (!$flagDate) {
                        $argvx = $argvx . (($argvx != '')? ' ' : '') . $argv[$i];
                    }
                }
            }

            if (!empty($date) && preg_match('/^' . '[1-9]\d{3}\-(?:0[1-9]|1[0-2])\-(?:0[1-9]|[12][0-9]|3[01])' . '(?:\s' . '(?:[0-1]\d|2[0-3])\:[0-5]\d\:[0-5]\d' . ')?$/', $date)) {
                eprintln('[Applying date filter: ' . $date . ']');
            } else {
                $date = $dateSystem;
            }

            $counterw = 0;

            if ($workspace == '') {
                $d = dir(PATH_DB);

                while (($entry = $d->read()) !== false) {
                    if ($entry != '' && $entry != '.' && $entry != '..') {
                        if (is_dir(PATH_DB . $entry)) {
                            if (file_exists(PATH_DB . $entry . PATH_SEP . 'db.php')) {
                                $counterw++;

                                passthru('php -f "' . $cronSinglePath . '" "' . base64_encode(PATH_HOME) . '" "' . base64_encode(PATH_TRUNK) . '" "' . base64_encode(PATH_OUTTRUNK) . '" ' . $cronName . ' ' . $entry . ' "' . $dateSystem . '" "' . $date . '" ' . $argvx);
                            }
                        }
                    }
                }
            } else {
                if (!is_dir(PATH_DB . $workspace) || !file_exists(PATH_DB . $workspace . PATH_SEP . 'db.php')) {
                    throw new Exception('Error: The workspace "' . $workspace . '" does not exist');
                }

                $counterw++;

                passthru('php -f "' . $cronSinglePath . '" "' . base64_encode(PATH_HOME) . '" "' . base64_encode(PATH_TRUNK) . '" "' . base64_encode(PATH_OUTTRUNK) . '" ' . $cronName . ' ' . $workspace . ' "' . $dateSystem . '" "' . $date . '" ' . $argvx);
            }

            eprintln('Finished ' . $counterw . ' workspaces processed');
        } catch (Exception $e) {
            throw $e;
        }

        //End CRON
        $arrayCron = ['flagIsRunning' => '0', 'lastExecution' => date('Y-m-d H:i:s')];
        file_put_contents(PATH_DATA . $cronName, serialize($arrayCron));
    } else {
        eprintln('The ' . $arrayCronConfig[$cronName]['title'] . ' is running, please wait for it to finish' . "\n" . 'Started in ' . $lastExecution);
        eprintln('If do you want force the execution use the option "' . $arrayCommandOption['force'] . '", example: php -f ' . $cronName . '.php +wworkflow ' . $arrayCommandOption['force'] ,'green');
    }

    echo 'Done!' . "\n";
} catch (Exception $e) {
    $token = strtotime("now");
    PMException::registerErrorLog($e, $token);
    G::outRes( G::LoadTranslation("ID_EXCEPTION_LOG_INTERFAZ", array($token)) . "\n" );
}

