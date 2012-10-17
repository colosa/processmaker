<?php
$request = isset( $_REQUEST['action'] ) ? $_REQUEST['action'] : "";
G::LoadClass( 'serverConfiguration' );
$oServerConf = &serverConf::getSingleton();
$oServerConf->setHeartbeatProperty( 'HB_BEAT_URL', 'http://heartbeat.processmaker.com/syspmLicenseSrv/en/green/services/beat', 'HEART_BEAT_CONF' );

switch ($request) {
    case 'processInformation':
        try {
            $heartBeatUrl = $oServerConf->getHeartbeatProperty( 'HB_BEAT_URL', 'HEART_BEAT_CONF' );
            //Test connection
            if (! (validateConnectivity( $heartBeatUrl ))) {
                $oServerConf->setHeartbeatProperty( 'HB_NEXT_BEAT_DATE', strtotime( "+1 day" ), 'HEART_BEAT_CONF' );
                throw new Exception( "Heartbeat::No connection" );
            }
            //Build Data to be sent
            $params = buildData();

            //Send the information
            postHeartBeat( $params );
        } catch (Exception $e) {
            G::pr( $e->getMessage() );
        }
        break;
}

function validateConnectivity ($url)
{
    ini_set( 'allow_url_fopen', 1 );
    $sContent = file_get_conditional_contents( $url );
    $sw_connect = true;
    //if ($sContent == '' || $sContent === false || strpos ( $sContent, 'address location' ) === false ) {    		4
    if ($sContent == '' || $sContent === false) {
        $sw_connect = false;
    }
    return $sw_connect;
}

function file_get_conditional_contents ($szURL)
{

    $pCurl = curl_init();
    curl_setopt( $pCurl, CURLOPT_URL, $szURL );
    curl_setopt( $pCurl, CURLOPT_RETURNTRANSFER, true );
    curl_setopt( $pCurl, CURLOPT_HEADER, true );
    curl_setopt( $pCurl, CURLOPT_FOLLOWLOCATION, false );
    curl_setopt( $pCurl, CURLOPT_AUTOREFERER, true );
    //To avoid SSL error
    curl_setopt( $pCurl, CURLOPT_SSL_VERIFYHOST, 0 );
    curl_setopt( $pCurl, CURLOPT_SSL_VERIFYPEER, 0 );

    //To avoid timeouts
    curl_setopt( $pCurl, CURLOPT_CONNECTTIMEOUT, 10 );
    curl_setopt( $pCurl, CURLOPT_TIMEOUT, 20 );

    curl_setopt( $pCurl, CURLOPT_NOPROGRESS, false );
    curl_setopt( $pCurl, CURLOPT_VERBOSE, true );

    //Apply proxy settings
    $sysConf = System::getSystemConfiguration();
    if ($sysConf['proxy_host'] != '') {
        curl_setopt( $pCurl, CURLOPT_PROXY, $sysConf['proxy_host'] . ($sysConf['proxy_port'] != '' ? ':' . $sysConf['proxy_port'] : '') );
        if ($sysConf['proxy_port'] != '') {
            curl_setopt( $pCurl, CURLOPT_PROXYPORT, $sysConf['proxy_port'] );
        }
        if ($sysConf['proxy_user'] != '') {
            curl_setopt( $pCurl, CURLOPT_PROXYUSERPWD, $sysConf['proxy_user'] . ($sysConf['proxy_pass'] != '' ? ':' . $sysConf['proxy_pass'] : '') );
        }
        curl_setopt( $pCurl, CURLOPT_HTTPHEADER, array ('Expect:'
        ) );
    }

    $szContents = curl_exec( $pCurl );
    $aInfo = curl_getinfo( $pCurl );

    $curl_session = curl_getinfo( $pCurl, CURLINFO_HTTP_CODE );
    $headers = curl_getinfo( $pCurl );
    $header = substr( $szContents, 0, $headers['header_size'] );
    $content = substr( $szContents, $headers['header_size'] );

    if ($aInfo['http_code'] === 200) {
        return $content;
    }

    return false;
}

function buildData ()
{
    require_once ("classes/model/Users.php");

    G::LoadClass( "serverConfiguration" );
    G::LoadClass( "system" );

    $oServerConf = &serverConf::getSingleton();

    $os = '';
    if (file_exists( '/etc/redhat-release' )) {
        $fnewsize = filesize( '/etc/redhat-release' );
        $fp = fopen( '/etc/redhat-release', 'r' );
        $os = trim( fread( $fp, $fnewsize ) );
        fclose( $fp );
    }
    $os .= " (" . PHP_OS . ")";

    $params = array ();
    $params['ip'] = getenv( 'SERVER_ADDR' );
    $oServerConf->setHeartbeatProperty( 'HB_BEAT_INDEX', intval( $oServerConf->getHeartbeatProperty( 'HB_BEAT_INDEX', 'HEART_BEAT_CONF' ) ) + 1, 'HEART_BEAT_CONF' );

    $params['index'] = $oServerConf->getHeartbeatProperty( 'HB_BEAT_INDEX', 'HEART_BEAT_CONF' ); //$this->index;
    $params['beatType'] = is_null( $oServerConf->getHeartbeatProperty( 'HB_BEAT_TYPE', 'HEART_BEAT_CONF' ) ) ? "starting" : $oServerConf->getHeartbeatProperty( 'HB_BEAT_TYPE', 'HEART_BEAT_CONF' ); //1;//$this->beatType;
    $params['date'] = date( 'Y-m-d H:i:s' );
    $params['host'] = getenv( 'SERVER_NAME' );
    $params['os'] = $os;
    $params['webserver'] = getenv( 'SERVER_SOFTWARE' );
    $params['php'] = phpversion();
    $params['pmVersion'] = System::getVersion();
    if (class_exists( 'pmLicenseManager' )) {
        $params['pmProduct'] = 'PMEE';
    } else {
        $params['pmProduct'] = 'PMCE';
    }

    $params['logins'] = $oServerConf->logins;
    $params['workspaces'] = serialize( $oServerConf->getWSList() );
    $params['plugins'] = serialize( $oServerConf->getPluginsList() );
    $params['dbVersion'] = $oServerConf->getDBVersion();
    //$params ['errors'] = serialize( $oServerConf->errors );
    if ($licInfo = $oServerConf->getProperty( 'LICENSE_INFO' )) {
        $params['license'] = serialize( $licInfo );
    }

    ///////
    $criteria = new Criteria( "workflow" );

    $criteria->addSelectColumn( "COUNT(USERS.USR_UID) AS USERS_NUMBER" );
    $criteria->add( UsersPeer::USR_UID, null, Criteria::ISNOTNULL );

    $rs = UsersPeer::doSelectRS( $criteria );
    $rs->setFetchmode( ResultSet::FETCHMODE_ASSOC );
    $rs->next();
    $row = $rs->getRow();

    $params["users"] = $row["USERS_NUMBER"];

    ///////
    $ee = null;

    if (file_exists( PATH_PLUGINS . "enterprise" . PATH_SEP . "VERSION" )) {
        $ee = trim( file_get_contents( PATH_PLUGINS . "enterprise" . PATH_SEP . "VERSION" ) );
    } else {
        $pluginRegistry = &PMPluginRegistry::getSingleton();
        $details = $pluginRegistry->getPluginDetails( "enterprise.php" );

        $ee = (! ($details == null)) ? $details->iVersion : null;
    }

    $params["ee"] = $ee;

    ///////
    $addonNumber = 0;
    $addonEnabledNumber = 0;

    $pluginRegistry = &PMPluginRegistry::getSingleton();

    $arrayAddon = array ();

    if (file_exists( PATH_DATA_SITE . "ee" )) {
        $arrayAddon = unserialize( trim( file_get_contents( PATH_DATA_SITE . "ee" ) ) );

        $arrayAddon["enterprise"] = array ("sFilename" => "enterprise-1.tar"
        );
    }

    foreach ($arrayAddon as $addon) {
        $sFileName = substr( $addon["sFilename"], 0, strpos( $addon["sFilename"], "-" ) );

        if (file_exists( PATH_PLUGINS . $sFileName . ".php" )) {
            $addonDetails = $pluginRegistry->getPluginDetails( $sFileName . ".php" );
            $enabled = 0;

            if ($addonDetails) {
                $enabled = ($addonDetails->enabled) ? 1 : 0;
            }

            if ($enabled == 1) {
                $addonEnabledNumber = $addonEnabledNumber + 1;
            }

            $addonNumber = $addonNumber + 1;
        }
    }

    $params["addonNumber"] = $addonNumber;
    $params["addonEnabledNumber"] = $addonEnabledNumber;

    ///////
    $licenseID = null;
    $licenseType = null;
    $licenseDomainWorkspace = null;
    $licenseNumber = 0;

    if (file_exists( PATH_PLUGINS . "enterprise" . PATH_SEP . "class.pmLicenseManager.php" ) && class_exists( 'enterprisePlugin' )) {
        $licenseManager = &pmLicenseManager::getSingleton();

        preg_match( "/^license_(.*).dat$/", $licenseManager->file, $matches );

        $licenseID = $matches[1];
        $licenseType = $licenseManager->type;
        $licenseDomainWorkspace = $licenseManager->info["DOMAIN_WORKSPACE"];

        ///////
        $criteria = new Criteria( "workflow" );

        $criteria->addSelectColumn( "COUNT(LICENSE_MANAGER.LICENSE_UID) AS LICENSE_NUMBER" );
        $criteria->add( LicenseManagerPeer::LICENSE_UID, null, Criteria::ISNOTNULL );

        $rs = LicenseManagerPeer::doSelectRS( $criteria );
        $rs->setFetchmode( ResultSet::FETCHMODE_ASSOC );
        $rs->next();
        $row = $rs->getRow();

        ///////
        $licenseNumber = ($row["LICENSE_NUMBER"] > 0) ? $row["LICENSE_NUMBER"] : count( glob( PATH_DATA_SITE . "licenses" . PATH_SEP . "*.dat" ) );
    }

    $params["licenseID"] = $licenseID;
    $params["licenseType"] = $licenseType;
    $params["licenseDomainWorkspace"] = $licenseDomainWorkspace;
    $params["licenseNumber"] = $licenseNumber;

    ///////
    return $params;
}

function postHeartBeat ($params)
{
    if (is_array( $params )) {
        //No matter what happens with the result let's set the nextBeat to 2 hours from now
        G::LoadClass( 'serverConfiguration' );
        $oServerConf = & serverConf::getSingleton();
        $oServerConf->setHeartbeatProperty( 'HB_NEXT_BEAT_DATE', strtotime( "+2 hour" ), 'HEART_BEAT_CONF' );
        $nextBeatDate = $oServerConf->getHeartbeatProperty( 'HB_NEXT_BEAT_DATE', 'HEART_BEAT_CONF' );

        $heartBeatUrl = $oServerConf->getHeartbeatProperty( 'HB_BEAT_URL', 'HEART_BEAT_CONF' );

        $ch = curl_init();
        curl_setopt( $ch, CURLOPT_URL, $heartBeatUrl );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $ch, CURLOPT_HEADER, true );
        curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, false );
        curl_setopt( $ch, CURLOPT_AUTOREFERER, true );
        //To avoid SSL error
        curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, 0 );
        curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, 0 );

        curl_setopt( $ch, CURLOPT_POST, 1 );
        curl_setopt( $ch, CURLOPT_POSTFIELDS, $params );

        //To avoid timeouts
        curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT, 10 );
        curl_setopt( $ch, CURLOPT_TIMEOUT, 20 );

        //Apply proxy settings
        $sysConf = System::getSystemConfiguration();
        if ($sysConf['proxy_host'] != '') {
            curl_setopt( $ch, CURLOPT_PROXY, $sysConf['proxy_host'] . ($sysConf['proxy_port'] != '' ? ':' . $sysConf['proxy_port'] : '') );
            if ($sysConf['proxy_port'] != '') {
                curl_setopt( $ch, CURLOPT_PROXYPORT, $sysConf['proxy_port'] );
            }
            if ($sysConf['proxy_user'] != '') {
                curl_setopt( $ch, CURLOPT_PROXYUSERPWD, $sysConf['proxy_user'] . ($sysConf['proxy_pass'] != '' ? ':' . $sysConf['proxy_pass'] : '') );
            }
            curl_setopt( $ch, CURLOPT_HTTPHEADER, array ('Expect:'
            ) );
        }

        $response = curl_exec( $ch );
        $curl_session = curl_getinfo( $ch, CURLINFO_HTTP_CODE );
        $headers = curl_getinfo( $ch );
        $header = substr( $response, 0, $headers['header_size'] );
        $content = substr( $response, $headers['header_size'] );
        curl_close( $ch );

        if ($headers['http_code'] == 200) {
            $oServerConf->setHeartbeatProperty( 'HB_BEAT_TYPE', 'beat', 'HEART_BEAT_CONF' );
            $oServerConf->resetLogins();
            $oServerConf->setHeartbeatProperty( 'HB_NEXT_BEAT_DATE', strtotime( "+7 day" ), 'HEART_BEAT_CONF' );
            //Reset Errors


        } else {
            //Catch the error


            $oServerConf->setHeartbeatProperty( 'HB_NEXT_BEAT_DATE', strtotime( "+1 day" ), 'HEART_BEAT_CONF' );
        }

    }
    /*

    $ch = curl_init ();
    curl_setopt ( $ch, CURLOPT_URL, $heartBeatUrl );
    curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true );
    curl_setopt ( $ch, CURLOPT_HEADER, true );
    curl_setopt ( $ch, CURLOPT_FOLLOWLOCATION, false );
    curl_setopt ( $ch, CURLOPT_AUTOREFERER, true );
    //To avoid SSL error
    curl_setopt ( $ch, CURLOPT_SSL_VERIFYHOST, 0 );
    curl_setopt ( $ch, CURLOPT_SSL_VERIFYPEER, 0 );

    curl_setopt ( $ch, CURLOPT_POST, 1 );
    curl_setopt ( $ch, CURLOPT_POSTFIELDS, $params );

    //To avoid timeouts
    curl_setopt ( $ch, CURLOPT_CONNECTTIMEOUT, 10 );
    curl_setopt ( $ch, CURLOPT_TIMEOUT, 20 );

    $response = curl_exec ( $ch );
    $curl_session = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $headers = curl_getinfo ( $ch );
    $header = substr ( $response, 0, $headers ['header_size'] );
    $content = substr ( $response, $headers ['header_size'] );
    curl_close ( $ch );

    if ($headers ['http_code'] == 200) {
      $this->beatType = 'beat';
      $this->resetLogins ();
      $this->nextBeatDate = strtotime ( "+7 day" ); //next beat in 7 days
      //Reset Errors
      $this->errors=array();
    } else {
        //Catch the error
        $this->errors[]=$curl_session;
      $this->nextBeatDate = strtotime ( "+1 day" ); //retry in 30 mins
    }

    $this->saveSingleton ();
    */
}

