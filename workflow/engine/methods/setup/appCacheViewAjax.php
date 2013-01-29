<?php
require_once ('classes/model/AppCacheView.php');

$request = isset( $_POST['request'] ) ? $_POST['request'] : (isset( $_GET['request'] ) ? $_GET['request'] : null);

switch ($request) {
    //check if the APP_CACHE VIEW table and their triggers are installed
    case 'info':
        $result = new stdClass();
        $result->info = Array ();

        //check the language, if no info in config about language, the default is 'en'
        G::loadClass( 'configuration' );
        $oConf = new Configurations();
        $oConf->loadConfig( $x, 'APP_CACHE_VIEW_ENGINE', '', '', '', '' );
        $appCacheViewEngine = $oConf->aConfig;

        if (isset( $appCacheViewEngine['LANG'] )) {
            $lang = (defined('SYS_LANG')) ? SYS_LANG : $appCacheViewEngine['LANG'];
            $status = strtoupper( $appCacheViewEngine['STATUS'] );
        } else {
            $confParams = Array ('LANG' => (defined('SYS_LANG')) ? SYS_LANG : 'en','STATUS' => ''
            );
            $oConf->aConfig = $confParams;
            $oConf->saveConfig( 'APP_CACHE_VIEW_ENGINE', '', '', '' );
            $lang = (defined('SYS_LANG')) ? SYS_LANG : 'en';
            $status = '';
        }

        //get user Root from hash
        $result->info = array ();
        $result->error = false;

        //setup the appcacheview object, and the path for the sql files
        $appCache = new AppCacheView();
        $appCache->setPathToAppCacheFiles( PATH_METHODS . 'setup' . PATH_SEP . 'setupSchemas' . PATH_SEP );

        $res = $appCache->getMySQLVersion();
        $result->info[] = array ('name' => 'MySQL Version','value' => $res
        );

        $res = $appCache->checkGrantsForUser( false );
        $currentUser = $res['user'];
        $currentUserIsSuper = $res['super'];
        $result->info[] = array ('name' => 'Current User','value' => $currentUser
        );
        $result->info[] = array ('name' => 'Current User has SUPER privilege','value' => $currentUserIsSuper
        );

        try {
            PROPEL::Init( PATH_METHODS . 'dbConnections/rootDbConnections.php' );
            $con = Propel::getConnection( "root" );
        } catch (Exception $e) {
            $result->info[] = array ('name' => 'Checking MySql Root user','value' => 'failed'
            );
            $result->error = true;
            $result->errorMsg = $e->getMessage();
        }

        //if user does not have the SUPER privilege we need to use the root user and grant the SUPER priv. to normal user.
        if (! $currentUserIsSuper && ! $result->error) {
            $res = $appCache->checkGrantsForUser( true );
            if (! isset( $res['error'] )) {
                $result->info[] = array ('name' => 'Root User','value' => $res['user']
                );
                $result->info[] = array ('name' => 'Root User has SUPER privilege','value' => $res['super']
                );
            } else {
                $result->info[] = array ('name' => 'Error','value' => $res['msg']
                );
            }

            $res = $appCache->setSuperForUser( $currentUser );
            if (! isset( $res['error'] )) {
                $result->info[] = array ('name' => 'Setting SUPER privilege','value' => 'Successfully'
                );
            } else {
                $result->error = true;
                $result->errorMsg = $res['msg'];
            }
            $currentUserIsSuper = true;

        }

        //now check if table APPCACHEVIEW exists, and it have correct number of fields, etc.
        $res = $appCache->checkAppCacheView();
        $result->info[] = array ('name' => 'Table APP_CACHE_VIEW','value' => $res['found']
        );

        $result->info[] = array ('name' => 'Rows in APP_CACHE_VIEW','value' => $res['count']
        );

        //now check if we have the triggers installed
        //APP_DELEGATION INSERT
        $res = $appCache->triggerAppDelegationInsert( $lang, false );
        $result->info[] = array ('name' => 'Trigger APP_DELEGATION INSERT','value' => $res
        );

        //APP_DELEGATION Update
        $res = $appCache->triggerAppDelegationUpdate( $lang, false );
        $result->info[] = array ('name' => 'Trigger APP_DELEGATION UPDATE','value' => $res
        );

        //APPLICATION UPDATE
        $res = $appCache->triggerApplicationUpdate( $lang, false );
        $result->info[] = array ('name' => 'Trigger APPLICATION UPDATE','value' => $res
        );

        //APPLICATION DELETE
        $res = $appCache->triggerApplicationDelete( $lang, false );
        $result->info[] = array ('name' => 'Trigger APPLICATION DELETE','value' => $res
        );

        //CONTENT UPDATE
        $res = $appCache->triggerContentUpdate( $lang, false );
        $result->info[] = array ("name" => "Trigger CONTENT UPDATE","value" => $res
        );

        //show language
        $result->info[] = array ('name' => 'Language','value' => $lang
        );

        echo G::json_encode( $result );
        break;
    case 'getLangList':

        $Translations = G::getModel( 'Translation' );
        $result = new stdClass();
        $result->rows = Array ();

        $langs = $Translations->getTranslationEnvironments();
        foreach ($langs as $lang) {
            $result->rows[] = Array ('LAN_ID' => $lang['LOCALE'],'LAN_NAME' => $lang['LANGUAGE']
            );
        }

        print (G::json_encode( $result )) ;
        break;
    case 'build':
        $sqlToExe = Array ();
        G::LoadClass( 'configuration' );
        $conf = new Configurations();

        //DEPRECATED $lang = $_POST['lang'];
        //there is no more support for other languages that english
        $lang = (defined('SYS_LANG')) ? SYS_LANG : 'en';

        try {
            //setup the appcacheview object, and the path for the sql files
            $appCache = new AppCacheView();
            $appCache->setPathToAppCacheFiles( PATH_METHODS . 'setup' . PATH_SEP . 'setupSchemas' . PATH_SEP );

            //Update APP_DELEGATION.DEL_LAST_INDEX data
            $res = $appCache->updateAppDelegationDelLastIndex($lang, true);
            //$result->info[] = array("name" => "update APP_DELEGATION.DEL_LAST_INDEX", "value" => $res);

            //APP_DELEGATION INSERT
            $res = $appCache->triggerAppDelegationInsert( $lang, true );
            //$result->info[] = array ('name' => 'Trigger APP_DELEGATION INSERT',           'value'=> $res);


            //APP_DELEGATION Update
            $res = $appCache->triggerAppDelegationUpdate( $lang, true );
            //$result->info[] = array ('name' => 'Trigger APP_DELEGATION UPDATE',           'value'=> $res);


            //APPLICATION UPDATE
            $res = $appCache->triggerApplicationUpdate( $lang, true );
            //$result->info[] = array ('name' => 'Trigger APPLICATION UPDATE',              'value'=> $res);


            //APPLICATION DELETE
            $res = $appCache->triggerApplicationDelete( $lang, true );
            //$result->info[] = array ('name' => 'Trigger APPLICATION DELETE',              'value'=> $res);


            //CONTENT UPDATE
            $res = $appCache->triggerContentUpdate( $lang, true );
            //$result->info[] = array("name" => "Trigger CONTENT UPDATE", "value" => $res);


            //build using the method in AppCacheView Class
            $res = $appCache->fillAppCacheView( $lang );
            //$result->info[] = array ('name' => 'build APP_CACHE_VIEW',              'value'=> $res);


            //set status in config table
            $confParams = Array ('LANG' => $lang,'STATUS' => 'active'
            );
            $conf->aConfig = $confParams;
            $conf->saveConfig( 'APP_CACHE_VIEW_ENGINE', '', '', '' );

            $response = new StdClass();
            $result->success = true;
            $result->msg = "Completed successfully";

            echo G::json_encode( $result );

        } catch (Exception $e) {
            $confParams = Array ('lang' => $lang,'status' => 'failed'
            );
            $appCacheViewEngine = $oServerConf->setProperty( 'APP_CACHE_VIEW_ENGINE', $confParams );

            echo '{success: false, msg:"' . $e->getMessage() . '"}';
        }
        break;
    case 'recreate-root':
        $sh = md5( filemtime( PATH_GULLIVER . "/class.g.php" ) );
        $h = G::encrypt( $_POST['host'] . $sh . $_POST['user'] . $sh . $_POST['password'] . $sh . (1), $sh );
        $insertStatements = "define ( 'HASH_INSTALLATION','{$h}' );  \ndefine ( 'SYSTEM_HASH', '{$sh}' ); \n";
        $lines = array ();
        $content = '';
        $filename = PATH_HOME . 'engine' . PATH_SEP . 'config' . PATH_SEP . 'paths_installed.php';
        $lines = file( $filename );

        $count = 1;
        foreach ($lines as $line_num => $line) {
            $pos = strpos( $line, "define" );
            if ($pos !== false && $count < 3) {
                $content = $content . $line;
                $count ++;
            }
        }
        $content = "<?php \n" . $content . "\n" . $insertStatements . "\n";
        if (file_put_contents( $filename, $content ) != false) {
            echo G::loadTranslation( 'ID_MESSAGE_ROOT_CHANGE_SUCESS' );
        } else {
            echo G::loadTranslation( 'ID_MESSAGE_ROOT_CHANGE_FAILURE' );
        }
        break;
}

