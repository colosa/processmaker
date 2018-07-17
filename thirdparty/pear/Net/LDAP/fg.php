<?php

require_once('../../../../config/dmsDefaults.php');
require_once(KT_LIB_DIR . '/authentication/authenticationutil.inc.php');
require_once(KT_LIB_DIR . '/authentication/authenticationsource.inc.php');

require_once('Net/LDAP.php');

$oKTConfig =& KTConfig::getSingleton();

$oAuthenticator = KTAuthenticationUtil::getAuthenticatorForSource(2);

$config = array(
    'dn' => $oAuthenticator->sSearchUser,
    'password' => $oAuthenticator->sSearchPassword,
    'host' => $oAuthenticator->sLdapServer,
    'base' => $oAuthenticator->sBaseDN,
);

$oLdap =& Net_LDAP::connect($config);
if (PEAR::isError($oLdap)) {
    var_dump($oLdap);
    exit(0);
}

$aParams = array(
    'scope' => 'sub',
    'attributes' => array('cn', 'dn', 'displayClass'),
);
$rootDn = $oAuthenticator->sBaseDN;
if (is_array($rootDn)) {
    $rootDn = join(",", $rootDn);
}
$oResults = $oLdap->search($rootDn, '(objectClass=group)', $aParams);
foreach ($oResults->entries() as $oEntry) {
    var_dump($oEntry->dn());
}

