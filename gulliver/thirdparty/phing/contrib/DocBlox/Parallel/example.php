<?php
/**
 * DocBlox
 *
 * PHP Version 5
 *
 * @category  DocBlox
 * @package   Parallel
 * @author    Mike van Riel <mike.vanriel@naenius.com>
 * @copyright 2010-2011 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://docblox-project.org
 */

/** Include the manager as we do not autoload */
require_once 'Manager.php';

/** Include the worker as we do not autoload */
require_once 'Worker.php';

/** Include the worker's pipe as we do not autoload */
require_once 'WorkerPipe.php';

// -----------------------------------------------------------------------------
// method 1: using a fluent interface and the addWorker helper.
// -----------------------------------------------------------------------------

$mgr = new DocBlox_Parallel_Manager();
$mgr
  ->addWorker(new DocBlox_Parallel_Worker(function() { sleep(1); return 'a'; }))
  ->addWorker(new DocBlox_Parallel_Worker(function() { sleep(1); return 'b'; }))
  ->addWorker(new DocBlox_Parallel_Worker(function() { sleep(1); return 'c'; }))
  ->addWorker(new DocBlox_Parallel_Worker(function() { sleep(1); return 'd'; }))
  ->addWorker(new DocBlox_Parallel_Worker(function() { sleep(1); return 'e'; }))
  ->execute();

/** @var DocBlox_Parallel_Worker $worker */
foreach ($mgr as $worker) {
    var_dump($worker->getResult());
}

// -----------------------------------------------------------------------------
// method 2: using the manager as worker array
// -----------------------------------------------------------------------------

$mgr = new DocBlox_Parallel_Manager();
$mgr[] = new DocBlox_Parallel_Worker(function() { sleep(1); return 'f'; });
$mgr[] = new DocBlox_Parallel_Worker(function() { sleep(1); return 'g'; });
$mgr[] = new DocBlox_Parallel_Worker(function() { sleep(1); return 'h'; });
$mgr[] = new DocBlox_Parallel_Worker(function() { sleep(1); return 'i'; });
$mgr[] = new DocBlox_Parallel_Worker(function() { sleep(1); return 'j'; });
$mgr->execute();

/** @var DocBlox_Parallel_Worker $worker */
foreach ($mgr as $worker) {
    var_dump($worker->getResult());
}
