<?php

/**
 * Description: This is a additional configuration for load all connections; if exist in a particular proccess
 * @Date: 15-05-2008
 *
 * @author : Erik Amaru Ortiz <erik@colosa.com>
 */
if (isset( $_SESSION['PROCESS'] )) {
    $pro = include (PATH_CORE . "config/databases.php");
    G::LoadClass( 'dbConnections' );

    $oDbConnections = new dbConnections( $_SESSION['PROCESS'] );
    foreach ($oDbConnections->connections as $db) {
        $db['DBS_PASSWORD'] = $oDbConnections->getPassWithoutEncrypt( $db );
        $dbsPort = ($db['DBS_PORT'] == '') ? ('') : (':' . $db['DBS_PORT']);
        $ENCODE = (trim( $db['DBS_ENCODE'] ) == '') ? '' : '?encoding=' . $db['DBS_ENCODE'];
        if (strpos( $db['DBS_SERVER'], "\\" ) && $db['DBS_TYPE'] == 'mssql') {
            $pro['datasources'][$db['DBS_UID']]['connection'] = $db['DBS_TYPE'] . '://' . $db['DBS_USERNAME'] . ':' . $db['DBS_PASSWORD'] . '@' . $db['DBS_SERVER'] . '/' . $db['DBS_DATABASE_NAME'] . $ENCODE;
        } else {
            $pro['datasources'][$db['DBS_UID']]['connection'] = $db['DBS_TYPE'] . '://' . $db['DBS_USERNAME'] . ':' . $db['DBS_PASSWORD'] . '@' . $db['DBS_SERVER'] . $dbsPort . '/' . $db['DBS_DATABASE_NAME'] . $ENCODE;
        }
        $pro['datasources'][$db['DBS_UID']]['adapter'] = $db['DBS_TYPE'];
    }
    return $pro;
}

