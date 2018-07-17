<?php
$request = isset( $_REQUEST['request'] ) ? $_REQUEST['request'] : null;

switch ($request) {
    case 'getLangList':

        $Translations = new Translation();
        $result = new stdClass();
        $result->rows = Array ();

        $langs = $Translations->getTranslationEnvironments();
        foreach ($langs as $lang) {
            $result->rows[] = Array ('LAN_ID' => $lang['LOCALE'],'LAN_NAME' => $lang['LANGUAGE']
            );
        }

        $result->rows[] = array ("LAN_ID" => "", "LAN_NAME" => G::LoadTranslation("ID_USE_LANGUAGE_URL"));

        print (G::json_encode( $result )) ;
        break;
    case 'saveSettings':
        $memcache = & PMmemcached::getSingleton( defined( 'SYS_SYS' ) ? SYS_SYS : '' );

        $conf = new Configurations();
        $conf->loadConfig( $obj, 'ENVIRONMENT_SETTINGS', '' );

        $conf->aConfig['login_enableForgotPassword'] = isset( $_REQUEST['forgotPasswd'] );
        $conf->aConfig['login_enableVirtualKeyboard'] = isset( $_REQUEST['virtualKeyboad'] );
        $conf->aConfig['login_defaultLanguage'] = isset( $_REQUEST['lang'] ) ? $_REQUEST['lang'] : 'en';

        $conf->saveConfig( 'ENVIRONMENT_SETTINGS', '' );

        $lang = isset( $_REQUEST['lang'] ) ? $_REQUEST['lang'] : 'en';
        //remove from memcache when this value is updated/created
        $memcache->delete( 'flagForgotPassword' );

        $response = new stdclass();
        $response->success = true;

        $messEnableForgotPassword = (isset($conf->aConfig["login_enableForgotPassword"]) && $conf->aConfig["login_enableForgotPassword"] == "1")? G::LoadTranslation("ID_YES") : G::LoadTranslation("ID_NO");
        G::auditLog("UpdateLoginSettings", "DefaultLanguage-> " . $lang . " EnableForgotPassword-> " . $messEnableForgotPassword);

        echo G::json_encode( $response );

        break;
}

