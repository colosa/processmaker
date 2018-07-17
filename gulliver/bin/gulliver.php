<?php
use Illuminate\Foundation\Console\Kernel;

// Because laravel has a __ helper function, it's important we include the class.g file to ensure our __ is used.
require_once __DIR__ . '/../../bootstrap/autoload.php';
require_once __DIR__ . '/../../bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

error_reporting(error_reporting() & ~E_DEPRECATED & ~E_STRICT);

if (!PATH_THIRDPARTY) {
    die("You must launch gulliver command line with the gulliver script\n");
}

require_once(PATH_CORE . 'config' . PATH_SEP . 'environments.php');

// trap -V before pake
if (in_array('-V', $argv) || in_array('--version', $argv)) {
    printf("Gulliver version %s\n", pakeColor::colorize(trim(file_get_contents(PATH_GULLIVER . 'VERSION')), 'INFO'));
    exit(0);
}

if (count($argv) <= 1) {
    $argv[] = '-T';
}

// register tasks
$dir = PATH_GULLIVER_HOME . 'bin' . PATH_SEP . 'tasks';
$tasks = pakeFinder::type('file')->name('pake*.php')->in($dir);

foreach ($tasks as $task) {
    include_once($task);
}

// run task
pakeApp::get_instance()->run(null, null, false);

exit(0);
