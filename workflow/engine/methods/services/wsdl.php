<?php
$filewsdl = PATH_METHODS . 'services' . PATH_SEP . 'pmos.wsdl';
$content = file_get_contents( $filewsdl );
$lang = defined( 'SYS_LANG' ) ? SYS_LANG : 'en';

$endpoint = 'http://' . $_SERVER['SERVER_NAME'] . ':' . $_SERVER['SERVER_PORT'] . '/sys' . SYS_SYS . '/' . $lang . '/classic/services/soap';
//print $endpoint; die;
$content = str_replace( "___SOAP_ADDRESS___", $endpoint, $content );

header( "Content-Type: application/xml;" );

print $content;

