<?php

ini_set( "default_charset", "UTF-8" );

function AuthenticationBasicHTTP ($realm)
{
    if (empty( $_SERVER['PHP_AUTH_USER'] ) && empty( $_SERVER['REDIRECT_REMOTE_USER'] )) {
        header( 'WWW-Authenticate: Basic realm="' . $realm . '"' );
        header( 'HTTP/1.0 401 Unauthorized' );
        die( '401 Unauthorized' );
    }

    global $RBAC;
    $uid = $RBAC->VerifyLogin( $_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW'] );
    if ($uid > 0) {
        // Asign the uid of user to userloggedobj
        $RBAC->loadUserRolePermission( $RBAC->sSystem, $uid );
        $res = $RBAC->userCanAccess( 'PM_WEBDAV' );
        if ($res != 1) {
            if ($res == - 2)
                $msg = G::LoadTranslation( 'ID_USER_HAVENT_RIGHTS_SYSTEM' );
            else
                $msg = G::LoadTranslation( 'ID_USER_HAVENT_RIGHTS_PAGE' );
            header( 'WWW-Authenticate: Basic realm="' . $realm . '"' );
            header( 'HTTP/1.0 401 ' . $msg );
            die( '401 ' . $msg );
            return false;
            die();
        }

        return true;
    }

    header( 'WWW-Authenticate: Basic realm="' . $realm . '"' );
    header( 'HTTP/1.0 401 Unauthorized' );
    die( '401 Unauthorized' );
    return false;
}

$realm = 'ProcessMaker Filesystem for Workspace ' . SYS_SYS;

# Choice an authentification type Digest or Basic
//AuthenticationDigestHTTP($realm, $users, $phpcgi);
AuthenticationBasicHTTP( $realm );

G::LoadClass( "webdav" );

$server = new ProcessMakerWebDav();

# Real path of your site
$server->ServeRequest( "" );

