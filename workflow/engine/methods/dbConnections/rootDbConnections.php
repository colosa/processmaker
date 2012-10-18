<?php
/**
 * ription:This is a additional configuration for load all connections; if exist in a particular proccess
 * @Date: 15-05-2008
 *
 * @author : Erik Amaru Ortiz <erik@colosa.com>
 */

$dbHash = @explode( SYSTEM_HASH, G::decrypt( HASH_INSTALLATION, SYSTEM_HASH ) );

$host = $dbHash[0];
$user = $dbHash[1];
$pass = $dbHash[2];
$dbName = DB_NAME;

$pro = include (PATH_CORE . "config/databases.php");

$pro['datasources']['root'] = Array ();
$pro['datasources']['root']['connection'] = "mysql://$user:$pass@$host/$dbName?encoding=utf8";
$pro['datasources']['root']['adapter'] = "mysql";

return $pro;

