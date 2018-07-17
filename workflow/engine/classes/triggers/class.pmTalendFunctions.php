<?php

/**
 * class.pmTalend.pmFunctions.php
 *
 * ProcessMaker Open Source Edition
 * Copyright (C) 2004 - 2008 Colosa Inc.
 * *
 */

////////////////////////////////////////////////////
// pmTalend PM Functions
//
// Copyright (C) 2007 COLOSA
//
// License: LGPL, see LICENSE
////////////////////////////////////////////////////
use ProcessMaker\Core\System;


/**
 * Talend ETL Integration
 * @class pmTalend
 *
 * @name Talend ETL Integration
 * @icon /images/triggers/TalendOpenStudio.gif
 * @className class.pmTalend.pmFunctions.php
 */

/**
 *
 * @method
 *
 * Executes a Talend Web Service..
 *
 * @name executeTalendWebservice
 * @label Executes a Talend Web Service.
 *
 * @param string | $wsdl | Talend Web Service (including ?WSDL)
 * @param array(array(n1 v1) array(n2 v2) array(nN vN)) | $params | Array of params. Pairs of param Name Value
 * @param string | $message | Message to be displayed
 * @return array | $return | Talend Array |
 *
 */
function executeTalendWebservice ($wsdl, $message, $params = array())
{
    $client = new SoapClient( $wsdl, array ('trace' => 1
    ) );

    //Apply proxy settings
    $sysConf = System::getSystemConfiguration();
    if ($sysConf['proxy_host'] != '') {
        curl_setopt( $client, CURLOPT_PROXY, $sysConf['proxy_host'] . ($sysConf['proxy_port'] != '' ? ':' . $sysConf['proxy_port'] : '') );
        if ($sysConf['proxy_port'] != '') {
            curl_setopt( $client, CURLOPT_PROXYPORT, $sysConf['proxy_port'] );
        }
        if ($sysConf['proxy_user'] != '') {
            curl_setopt( $client, CURLOPT_PROXYUSERPWD, $sysConf['proxy_user'] . ($sysConf['proxy_pass'] != '' ? ':' . $sysConf['proxy_pass'] : '') );
        }
        curl_setopt( $client, CURLOPT_HTTPHEADER, array ('Expect:'
        ) );
    }

    $params[0] = "";
    foreach ($params as $paramO) {
        $params[] = "--context_param" . $paramO[0] . "=" . $paramO[1];
    }
    $result = $client->__SoapCall( 'runJob', array ($params
    ) );

    /*
     $params[1]="--context_param nb_line=".@=Quantity;

     $result = $client->__SoapCall('runJob', array($params));
     foreach ($result->item as $keyItem => $item){
     $gridRow=$keyItem+1;
     @=USERSINFO[$gridRow]['NAME']=$item->item[1];
     @=USERSINFO[$gridRow]['LASTNAME']=$item->item[2];
     @=USERSINFO[$gridRow]['DATE']=$item->item[0];
     @=USERSINFO[$gridRow]['STREET']=$item->item[3];
     @=USERSINFO[$gridRow]['CITY']=$item->item[4];
     @=USERSINFO[$gridRow]['STATE']=$item->item[5];
     @=USERSINFO[$gridRow]['STATEID']=$item->item[6];

     }
    */
    G::SendMessageText( "<font color='blue'>Information from Talend ETL webservice</font><font color='darkgray'><br>" . $wsdl . "</font>", "INFO" );
}

