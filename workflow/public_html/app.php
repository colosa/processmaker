<?php

use Illuminate\Foundation\Http\Kernel;
use Maveriks\WebApplication;
use Maveriks\Http\Response;
use Maveriks\Pattern\Mvc\PhtmlView;
use ProcessMaker\Exception\RBACException;

// Because laravel has a __ helper function, it's important we include the class.g file to ensure our __ is used.
require_once __DIR__ . '/../../gulliver/system/class.g.php';
require_once __DIR__ . '/../../bootstrap/autoload.php';
require_once __DIR__ . '/../../bootstrap/app.php';


register_shutdown_function(
    create_function(
        "",
        "
        if (class_exists(\"Propel\")) {
            Propel::close();
        }
        "
    )
);

ini_set("session.cookie_httponly", 1);

if (isset($_SERVER['UNENCODED_URL'])) {
    $_SERVER['REQUEST_URI'] = $_SERVER['UNENCODED_URL'];
}

try {
    $rootDir = realpath(__DIR__ . "/../../") . DIRECTORY_SEPARATOR;

    $app = new WebApplication();

    $app->setRootDir($rootDir);
    $app->setRequestUri($_SERVER['REQUEST_URI']);
    $stat = $app->route();

    switch ($stat) {
        case WebApplication::RUNNING_WORKFLOW:
            include "sysGeneric.php";
            break;

        case WebApplication::RUNNING_API:
            $app->run(WebApplication::SERVICE_API);
            break;

        case WebApplication::RUNNING_OAUTH2:
            $app->run(WebApplication::SERVICE_OAUTH2);
            break;

        case WebApplication::RUNNING_INDEX:
            $response = new Response(file_get_contents("index.html"), 302);
            $response->send();
            break;

        case WebApplication::RUNNING_DEFAULT:
            $response = new Response("", 302);
            //TODO compose this def url with configuration data from env.ini
            $response->setHeader("location", "/sys/en/neoclassic/login/login");
            $response->send();
            break;
    }
} catch (RBACException $e) {
    G::header('location: ' . $e->getPath());
} catch (Exception $e) {
    $view = new PhtmlView($rootDir . "framework/src/templates/Exception.phtml");
    $view->set("message", $e->getMessage());
    $view->set("exception", $e);

    $response = new Response($view->getOutput(), 503);
    $response->send();
}

