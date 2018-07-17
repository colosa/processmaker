<?php

/**
 * class, helping to set some not desirable settings but necesary
 * @author reav
 *
 */
abstract class Patch
{
    static protected $isPathchable = false;
    static public $dbAdapter = 'mysql';
    abstract static public function isApplicable();
    abstract static public function execute();
}
