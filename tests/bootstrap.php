<?php
// ProcessMaker Test Unit Bootstrap

// Defining the PATH_SEP constant, he we are defining if the the path separator symbol will be '\\' or '/'
define('PATH_SEP', '/');

if (!defined('__DIR__')) {
  define ('__DIR__', dirname(__FILE__));
}

// Defining the Home Directory
define('PATH_TRUNK', realpath(__DIR__ . '/../') . PATH_SEP);
define('PATH_HOME',  PATH_TRUNK . 'workflow' . PATH_SEP);

require  PATH_HOME . 'engine' . PATH_SEP . 'config' . PATH_SEP . 'paths.php';

set_include_path(
    PATH_CORE . PATH_SEPARATOR .
    PATH_THIRDPARTY . PATH_SEPARATOR .
    PATH_THIRDPARTY . 'pear'. PATH_SEPARATOR .
    PATH_RBAC_CORE . PATH_SEPARATOR .
    get_include_path()
);


/* @translation
*/
global $translation;

$translation = array( 'ABOUT' => 'About',
                      'ACCOUNT_FROM' => 'Account From',
                      'ADD_USERS_TO_DEPARTMENT' => 'Add users to department',
                      'CANCELLED' => 'Canceled',
                      'CANT_DEL_LANGUAGE' => 'This language cannot be deleted  because it  is currently being  used.',
                      'CASES' => 'Cases'
                    );
