<?php
require_once ('classes/model/AppCacheView.php');

$request = isset( $_POST['request'] ) ? $_POST['request'] : (isset( $_GET['request'] ) ? $_GET['request'] : null);

function testConnection($type, $server, $user, $passwd, $port = 'none', $dbName = "")
{

    if (($port == 'none') || ($port == '') || ($port == 0)) {
        //setting defaults ports
        switch ($type) {
            case 'mysql':
                $port = 3306;
                break;
            case 'pgsql':
                $port = 5432;
                break;
            case 'mssql':
                $port = 1433;
                break;
            case 'oracle':
                $port = 1521;
                break;
        }
    }

    G::LoadClass('net');
    $Server = new NET($server);

    if ($Server->getErrno() == 0) {
        $Server->scannPort($port);
        if ($Server->getErrno() == 0) {
            $Server->loginDbServer($user, $passwd);
            $Server->setDataBase($dbName, $port);
            if ($Server->errno == 0) {
                $response = $Server->tryConnectServer($type);
                if ($response->status == 'SUCCESS') {
                    if ($Server->errno == 0) {
                        $message = "";
                        $response = $Server->tryConnectServer($type);
                        $connDatabase = @mysql_connect($server, $user, $passwd);
                        $dbNameTest = "PROCESSMAKERTESTDC";
                        $db = @mysql_query("CREATE DATABASE " . $dbNameTest, $connDatabase);
                        $success = false;
                        if (!$db) {
                            $message = mysql_error();;
                        } else {
                            $usrTest = "wfrbtest";
                            $chkG = "GRANT ALL PRIVILEGES ON `" . $dbNameTest . "`.* TO " . $usrTest . "@'%' IDENTIFIED BY 'sample' WITH GRANT OPTION";
                            $ch = @mysql_query($chkG, $connDatabase);
                            if (!$ch) {
                                $message = mysql_error();
                            } else {
                                $sqlCreateUser = "CREATE USER '" . $user . "_usertest'@'%' IDENTIFIED BY 'sample'";
                                $result = @mysql_query($sqlCreateUser, $connDatabase);
                                if (!$result) {
                                    $message = mysql_error();
                                } else {
                                    $success = true;
                                    $message = G::LoadTranslation('ID_SUCCESSFUL_CONNECTION');
                                }
                                $sqlDropUser = "DROP USER '" . $user . "_usertest'@'%'";
                                @mysql_query($sqlDropUser, $connDatabase);

                                @mysql_query("DROP USER " . $usrTest . "@'%'", $connDatabase);
                            }
                            @mysql_query("DROP DATABASE " . $dbNameTest, $connDatabase);
                        }
                        return array($success, ($message != "")? $message : $Server->error);
                    } else {
                        return array(false, $Server->error);
                    }
                } else {
                    return array(false, $Server->error);
                }
            } else {
                return array(false, $Server->error);
            }
        } else {
            return array(false, $Server->error);
        }
    } else {
        return array(false, $Server->error);
    }
}

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
            $confParams = Array ('LANG' => (defined('SYS_LANG')) ? SYS_LANG : 'en','STATUS' => '');
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
        //load translations  G::LoadTranslation
        $result->info[] = array ('name' => G::LoadTranslation ( 'ID_CACHE_BUILDER_MYSQL_VERSION' ) ,'value' => $res);

        $res = $appCache->checkGrantsForUser( false );
        $currentUser = $res['user'];
        $currentUserIsSuper = $res['super'];
        $result->info[] = array ('name' => G::LoadTranslation ( 'ID_CACHE_BUILDER_CURRENT_USER' ) ,'value' => $currentUser);
        $result->info[] = array ('name' => G::LoadTranslation ( 'ID_CACHE_BUILDER_USER_SUPER_PRIVILEGE' ) ,'value' => $currentUserIsSuper);

        try {
            PROPEL::Init( PATH_METHODS . 'dbConnections/rootDbConnections.php' );
            $con = Propel::getConnection( "root" );
        } catch (Exception $e) {
            $result->info[] = array ('name' => 'Checking MySql Root user','value' => 'failed');
            $result->error = true;
            $result->errorMsg = $e->getMessage();
        }

        //if user does not have the SUPER privilege we need to use the root user and grant the SUPER priv. to normal user.
        if (! $currentUserIsSuper && ! $result->error) {
            $res = $appCache->checkGrantsForUser( true );
            if (! isset( $res['error'] )) {
                $result->info[] = array ('name' => 'Root User','value' => $res['user']);
                $result->info[] = array ('name' => 'Root User has SUPER privilege','value' => $res['super']);
            } else {
                $result->info[] = array ('name' => 'Error','value' => $res['msg']);
            }

            $res = $appCache->setSuperForUser( $currentUser );
            if (! isset( $res['error'] )) {
                $result->info[] = array ('name' => 'Setting SUPER privilege','value' => 'Successfully');
            } else {
                $result->error = true;
                $result->errorMsg = $res['msg'];
            }
            $currentUserIsSuper = true;

        }

        //now check if table APPCACHEVIEW exists, and it have correct number of fields, etc.
        $res = $appCache->checkAppCacheView();
        $result->info[] = array ('name' => G::LoadTranslation ( 'ID_CACHE_BUILDER_TABLE' ),'value' => $res['found']);

        $result->info[] = array ('name' => G::LoadTranslation ( 'ID_CACHE_BUILDER_ROWS' ),'value' => $res['count']);

        //now check if we have the triggers installed
        //APP_DELEGATION INSERT
        $res = $appCache->triggerAppDelegationInsert( $lang, false );
        $result->info[] = array ('name' => G::LoadTranslation ( 'ID_CACHE_BUILDER_TRIGGER_INSERT' ),'value' => $res);

        //APP_DELEGATION Update
        $res = $appCache->triggerAppDelegationUpdate( $lang, false );
        $result->info[] = array ('name' => G::LoadTranslation ( 'ID_CACHE_BUILDER_TRIGGER_UPDATE' ),'value' => $res);

        //APPLICATION UPDATE
        $res = $appCache->triggerApplicationUpdate( $lang, false );
        $result->info[] = array ('name' => G::LoadTranslation ( 'ID_CACHE_BUILDER_TRIGGER_APPLICATION_UPDATE' ),'value' => $res);

        //APPLICATION DELETE
        $res = $appCache->triggerApplicationDelete( $lang, false );
        $result->info[] = array ('name' => G::LoadTranslation ( 'ID_CACHE_BUILDER_TRIGGER_APPLICATION_DELETE' ),'value' => $res);

        //SUB_APPLICATION INSERT
        $res = $appCache->triggerSubApplicationInsert($lang, false);

        //CONTENT UPDATE
        $res = $appCache->triggerContentUpdate( $lang, false );
        $result->info[] = array ("name" => G::LoadTranslation ( 'ID_CACHE_BUILDER_TRIGGER_CONTENT_UPDATE' ),"value" => $res);

        //show language
        $result->info[] = array ('name' => G::LoadTranslation ( 'ID_CACHE_BUILDER_LANGUAGE' ),'value' => $lang);

        echo G::json_encode( $result );
        break;
    case 'getLangList':

        $Translations = G::getModel( 'Translation' );
        $result = new stdClass();
        $result->rows = Array ();

        $langs = $Translations->getTranslationEnvironments();
        foreach ($langs as $lang) {
            $result->rows[] = Array ('LAN_ID' => $lang['LOCALE'],'LAN_NAME' => $lang['LANGUAGE']);
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

            //SUB_APPLICATION INSERT
            $res = $appCache->triggerSubApplicationInsert($lang, false);

            //CONTENT UPDATE
            $res = $appCache->triggerContentUpdate( $lang, true );
            //$result->info[] = array("name" => "Trigger CONTENT UPDATE", "value" => $res);

            //build using the method in AppCacheView Class
            $res = $appCache->fillAppCacheView( $lang );
            //$result->info[] = array ('name' => 'build APP_CACHE_VIEW',              'value'=> $res);


            //set status in config table
            $confParams = Array ('LANG' => $lang,'STATUS' => 'active');
            $conf->aConfig = $confParams;
            $conf->saveConfig( 'APP_CACHE_VIEW_ENGINE', '', '', '' );

            $response = new StdClass();
            $result->success = true;
            $result->msg = G::LoadTranslation('ID_TITLE_COMPLETED');

            echo G::json_encode( $result );

        } catch (Exception $e) {
            $confParams = Array ('lang' => $lang,'status' => 'failed');
            $appCacheViewEngine = $oServerConf->setProperty( 'APP_CACHE_VIEW_ENGINE', $confParams );

            echo '{success: false, msg:"' . $e->getMessage() . '"}';
        }
        break;
    case 'recreate-root':
        $user = $_POST['user'];
        $passwd = $_POST['password'];
        $server = $_POST['host'];
        $aServer = split(":", $server);
        $serverName = $aServer[0];
        $port = (count($aServer)>1) ? $aServer[1] : "none";
        list($sucess, $msgErr) = testConnection(DB_ADAPTER, $serverName, $user, $passwd, $port);

        if ($sucess) {
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
        } else {
            echo $msgErr;
        }
        break;
}

