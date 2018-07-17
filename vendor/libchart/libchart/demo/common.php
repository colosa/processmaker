<?php
	$autoloader = '../../vendor/autoload.php';
	if (!file_exists($autoloader)) {
		die($autoloader . 'not found. Please make sure you run "composer install" before running the tests.');
	}
	require_once $autoloader;
?>