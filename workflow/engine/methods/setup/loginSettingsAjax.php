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

        print (G::json_encode( $result )) ;
        break;
    case 'saveSettings':
        $memcache = & PMmemcached::getSingleton( defined( 'SYS_SYS' ) ? SYS_SYS : '' );
        G::LoadClass( 'configuration' );
        $conf = new Configurations();
        $conf->loadConfig( $obj, 'ENVIRONMENT_SETTINGS', '' );

        $conf->aConfig['login_enableForgotPassword'] = isset( $_REQUEST['forgotPasswd'] );
        $conf->aConfig['login_enableVirtualKeyboard'] = isset( $_REQUEST['virtualKeyboad'] );
        $conf->aConfig['login_defaultLanguage'] = isset( $_REQUEST['lang'] ) ? $_REQUEST['lang'] : 'en';

        $conf->saveConfig( 'ENVIRONMENT_SETTINGS', '' );

        //remove from memcache when this value is updated/created
        $memcache->delete( 'flagForgotPassword' );

        $response->success = true;
        echo G::json_encode( $response );

        break;
}

