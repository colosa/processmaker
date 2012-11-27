<?php
/**
 * class.ldap.php
 *
 * ProcessMaker Open Source Edition
 * Copyright (C) 2004 - 2008 Colosa Inc.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * For more information, contact Colosa Inc, 2566 Le Jeune Rd.,
 * Coral Gables, FL, 33134, USA, or email info@colosa.com.
 *
 */
/**
 *
 * LDAP plugin for RBAC class
 *
 * @author Fernando Ontiveros
 * @package  rbac-classes-model
 * @access public

 */

class LDAP
{
  var $sAuthSource = '';

  var $aUserInfo = array();
  var $sSystem = '';
  var $sLdapLog = '';

  static private $instance = NULL;

  function __construct() {
  }

  function &getSingleton() {
    if (self::$instance == NULL) {
      self::$instance = new RBAC();
    }
    return self::$instance;
  }

  function log ( $_link , $text ) {
    $this->sLdapLog .= $text . ": ". @ldap_errno($_link) . ','.  @ldap_error($_link) . "\n";
  }


  /**
   * Autentificacion de un usuario a traves de la clase RBAC_user
   *
   * verifica que un usuario tiene derechos de iniciar una aplicacion
   *
   * @author Fernando Ontiveros Lira <fernando@colosa.com>
   * @access public

   * @param  string $strUser    UserId  (login) de usuario
   * @param  string $strPass    Password
   * @return
   *  -1: no existe usuario
   *  -2: password errado
   *  -3: usuario inactivo
   *  -4: usuario vencido
   *  n : uid de usuario
   */
  function VerifyLogin( $strUser, $strPass) {
    //get the AuthSource properties
    if ( strlen($strPass) == 0) return -2;
    $RBAC = RBAC::getSingleton();
    $aAuthSource = $RBAC->authSourcesObj->load($this->sAuthSource );

    $sAuthHost    = $aAuthSource['AUTH_SOURCE_SERVER_NAME'];
    $sAuthPort    = $aAuthSource['AUTH_SOURCE_PORT'];
    $sAuthTls     = $aAuthSource['AUTH_SOURCE_ENABLED_TLS'];
    $sAuthBaseDn  = $aAuthSource['AUTH_SOURCE_BASE_DN'];
    $sAuthFilter  = $aAuthSource['AUTH_SOURCE_OBJECT_CLASSES'];
    $sAuthType    = 'AD';
    $sAuthVersion = $aAuthSource['AUTH_SOURCE_VERSION'];
    $aAttributes  = $aAuthSource['AUTH_SOURCE_ATTRIBUTES'];//array ('dn',"cn", "samaccountname", "givenname", "sn", "mail");
    $sAuthUser    = $aAuthSource['AUTH_SOURCE_SEARCH_USER'];
    $sAuthPass    = $aAuthSource['AUTH_SOURCE_PASSWORD'];

    $_link = @ldap_connect( $sAuthHost, $sAuthPort );
    $this->log ( $_link, "ldap connect" );

    ldap_set_option($_link, LDAP_OPT_PROTOCOL_VERSION, $sAuthVersion);
    $this->log ( $_link, "ldap set Protocol Version $sAuthVersion" );

    ldap_set_option($_link, LDAP_OPT_REFERRALS, 0);
    $this->log ( $_link, "ldap set option Referrals" );

    if ( isset($sAuthTls) && $sAuthTls ) {
      @ldap_start_tls($_link);
      $this->log ( $_link, "start tls" );
    }

    $bind = @ldap_bind($_link);
    $this->log ( $_link, "ldap bind anonymous" );

    $validUserPass = @ldap_bind($_link, $strUser,$strPass );
    $this->log ( $_link, "ldap binding with user $strUser" );

    return $validUserPass ;
  }

  function searchUsers($sKeyword) {
    $sKeyword     = trim($sKeyword);
    $RBAC         = RBAC::getSingleton();
    $aAuthSource  = $RBAC->authSourcesObj->load($this->sAuthSource);
    $pass =explode("_",$aAuthSource['AUTH_SOURCE_PASSWORD']);
    foreach($pass as $index => $value) {
      if($value == '2NnV3ujj3w'){
        $aAuthSource['AUTH_SOURCE_PASSWORD'] = G::decrypt($pass[0],$aAuthSource['AUTH_SOURCE_SERVER_NAME']);
      }
    }
    $oLink = @ldap_connect($aAuthSource['AUTH_SOURCE_SERVER_NAME'], $aAuthSource['AUTH_SOURCE_PORT']);
    @ldap_set_option($oLink, LDAP_OPT_PROTOCOL_VERSION, $aAuthSource['AUTH_SOURCE_VERSION']);
    @ldap_set_option($oLink, LDAP_OPT_REFERRALS, 0);
    if (isset($aAuthSource['AUTH_SOURCE_ENABLED_TLS']) && $aAuthSource['AUTH_SOURCE_ENABLED_TLS']) {
      @ldap_start_tls($oLink);
    }
    if ($aAuthSource['AUTH_ANONYMOUS'] == '1') {
      $bBind = @ldap_bind($oLink);
    }
    else {
      $bBind = @ldap_bind($oLink, $aAuthSource['AUTH_SOURCE_SEARCH_USER'], $aAuthSource['AUTH_SOURCE_PASSWORD']);
    }
    if ( !$bBind ) {
    	throw new Exception('Unable to bind to server : ' . $aAuthSource['AUTH_SOURCE_SERVER_NAME'] . ' in port ' . $aAuthSource['AUTH_SOURCE_PORT']);
    }
    if (substr($sKeyword , -1) != '*') {
      if ($sKeyword != '') {
        $sKeyword = '*' . $sKeyword . '*';
      }
      else {
        $sKeyword .= '*';
      }
    }

    $additionalFilter = isset($aAuthSource['AUTH_SOURCE_DATA']['AUTH_SOURCE_ADDITIONAL_FILTER']) ? trim($aAuthSource['AUTH_SOURCE_DATA']['AUTH_SOURCE_ADDITIONAL_FILTER']) : '';

    $sFilter  = '(&(|(objectClass=*))';

    if ( isset( $aAuthSource['AUTH_SOURCE_DATA']['LDAP_TYPE']) && $aAuthSource['AUTH_SOURCE_DATA']['LDAP_TYPE'] == 'ad' ) {
      $sFilter = "(&(|(objectClass=*))(|(samaccountname=$sKeyword)(userprincipalname=$sKeyword))$additionalFilter)";
    }
    else
      $sFilter = "(&(|(objectClass=*))(|(uid=$sKeyword)(cn=$sKeyword))$additionalFilter)";

    //G::pr($sFilter);
    $aUsers  = array();
    $oSearch = @ldap_search($oLink, $aAuthSource['AUTH_SOURCE_BASE_DN'], $sFilter, array('dn','uid','samaccountname', 'cn','givenname','sn','mail','userprincipalname','objectcategory', 'manager'));

    if ($oError = @ldap_errno($oLink)) {
      return $aUsers;
    }
    else {
      if ($oSearch) {
        if (@ldap_count_entries($oLink, $oSearch) > 0) {
          $sUsername = '';
          $oEntry    = @ldap_first_entry($oLink, $oSearch);
          $uidUser = isset ( $aAuthSource['AUTH_SOURCE_DATA']['AUTH_SOURCE_IDENTIFIER_FOR_USER'] ) ? $aAuthSource['AUTH_SOURCE_DATA']['AUTH_SOURCE_IDENTIFIER_FOR_USER'] : 'uid';
          do {
            $aAttr = $this->getLdapAttributes ( $oLink, $oEntry );
            $sUsername = isset($aAttr[ $uidUser ]) ? $aAttr[ $uidUser ] : '';
            if ($sUsername != '') {
              // note added by gustavo cruz gustavo-at-colosa.com
              // assign the givenname and sn fields if these are set
              $aUsers[] = array('sUsername' => $sUsername,
                              'sFullname' => $aAttr['cn'],
                              'sFirstname' => isset($aAttr['givenname']) ? $aAttr['givenname'] : '',
                              'sLastname' => isset($aAttr['sn']) ? $aAttr['sn'] : '',
                              'sEmail' => isset($aAttr['mail']) ? $aAttr['mail'] : ( isset($aAttr['userprincipalname'])?$aAttr['userprincipalname'] : '') ,
                              'sDN' => $aAttr['dn'] );
            }
          } while ($oEntry = @ldap_next_entry($oLink, $oEntry));
        }
      }
      return $aUsers;
    }
  }

  function getLdapAttributes ( $oLink, $oEntry ) {
    $aAttrib['dn'] = @ldap_get_dn($oLink, $oEntry);
    $aAttr = @ldap_get_attributes($oLink, $oEntry);
    for ( $iAtt = 0 ; $iAtt < $aAttr['count']; $iAtt++ ) {
      switch ( $aAttr[ $aAttr[$iAtt] ]['count'] ) {
        case 0: $aAttrib[ strtolower($aAttr[$iAtt]) ]= '';
                break;
        case 1: $aAttrib[ strtolower($aAttr[$iAtt]) ]= $aAttr[ $aAttr[$iAtt] ][0];
                break;
        default:
                $aAttrib[ strtolower($aAttr[$iAtt]) ]= $aAttr[ $aAttr[$iAtt] ];
                unset( $aAttrib[ $aAttr[$iAtt] ]['count'] );
                break;
      }
    }
    return $aAttrib;
  }
}
