Parallel
========

This is a library for introducing Parallelization into your project.
See the `example.php` file for an example how to use this library.

Theory of Operation
-------------------

This library will enable the developer to execute a given amount of tasks
(workers) in parallel. This is achieved by adding workers onto a manager,
optionally defining how many processes to run simultaneously and then execute
the manager.

Under Linux this library will try to detect the number of processors and allow
a maximum number of processes to run equal to the number of processors. If this
cannot be determined or the user is running Windows then a default of 2 is used.

Requirements and graceful degradation
-------------------------------------

Parallelization has several requirements. But to allow distribution, without
adding several requirements to your application, will this library execute the
given tasks in serie if the requirements are not met. And throw a E_USER_NOTICE
php error that explains to the user that dependencies are missing.

The requirements for this library are:

* A *NIX compatible operating system
* Scripts must not run from an apache module
* the PCNTL PHP extension (http://php.net/manual/en/book.pcntl.php)

Workers
-------

Workers are basically wrappers around callback functions or methods. As such you
can use anything in your existing project and parallelize it.

Do note that each parallel process is a duplicate of the original. This means
that, for example, if you pass an object (or other reference) and change that,
that the changes that you have made do not carry over to the caller.

The return value of the given callback is stored as result on the worker and
can be read using the `getResult()` method.

Any exception that is thrown will result in an error, where the `getReturnCode()`
method will return the exception code (be warned: this may be 0!) and the
`getError()` method will return the exception message.

Errors and exceptions
---------------------

if a task throws an exception it is caught and registered as an error. The
exception's code is used as error number, where the message is used as error
message.

By using this, instead of dying, you can continue execution of the other parallel
processes and handle errors yourself after all processes have been executed.

Examples
--------

### Fluent interface

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

### Array interface

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

TODO
----

* Improve docs
* More intelligent process slots; currently only the oldest in a 'set' of slots
  is waited on but if this runs for a longer time then the other slots than
  those will not be filled as long as the first slot is occupied.
* Last parts of IPC (Inter-Process Communication), to be able to return
  information from Workers to the Manager.

  * STDOUT
  * STDERR

