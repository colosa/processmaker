<?php
/**
 * cases_SchedulerValidateUser.php
 *
 * ProcessMaker Open Source Edition
 * Copyright (C) 2004 - 2010 Colosa Inc.23
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 * For more information, contact Colosa Inc, 2566 Le Jeune Rd.,
 * Coral Gables, FL, 33134, USA, or email info@colosa.com.
 */

/**
 * process_SchedulerValidate_User
 * validates if the username and password are valid data and if the user assigned
 * to the process and task has the rights and persmissions required to create a cron task
 */

$sWS_USER = trim( $_REQUEST['USERNAME'] );
$sWS_PASS = trim( $_REQUEST['PASSWORD'] );

if (G::is_https()) {
    $http = 'https://';
} else {
    $http = 'http://';
}

$endpoint = $http . $_SERVER['HTTP_HOST'] . '/sys' . SYS_SYS . '/' . SYS_LANG . '/' . SYS_SKIN . '/services/wsdl2';
@$client = new SoapClient( $endpoint );

$user = $sWS_USER;
$pass = $sWS_PASS;

$params = array ('userid' => $user,'password' => $pass);
$result = $client->__SoapCall( 'login', array ($params) );

if ($result->status_code == 0) {
    if (! class_exists( 'Users' )) {
        require ("classes/model/UsersPeer.php");
    }
    $oCriteria = new Criteria( 'workflow' );
    $oCriteria->addSelectColumn( 'USR_UID' );
    $oCriteria->add( UsersPeer::USR_USERNAME, $sWS_USER );
    $resultSet = UsersPeer::doSelectRS( $oCriteria );
    $resultSet->next();
    $user_id = $resultSet->getRow();
    $result->message = $user_id[0];

    G::LoadClass( 'case' );
    $caseInstance = new Cases();
    if (! $caseInstance->canStartCase( $result->message, $_REQUEST['PRO_UID'] )) {
        $result->status_code = - 1000;
        $result->message = G::LoadTranslation( 'ID_USER_CASES_NOT_START' );
    }
}

die( G::json_encode( $result ) );

