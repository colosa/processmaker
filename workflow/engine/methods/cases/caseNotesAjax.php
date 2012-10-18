<?php
if (! isset( $_REQUEST['action'] )) {
    $res['success'] = 'failure';
    $res['message'] = 'You may request an action';
    print G::json_encode( $res );
    die();
}
if (! function_exists( $_REQUEST['action'] )) {
    $res['success'] = 'failure';
    $res['message'] = 'The requested action does not exist';
    header( "Content-Type: application/json" );
    print G::json_encode( $res );
    die();
}

$functionName = $_REQUEST['action'];
$functionParams = isset( $_REQUEST['params'] ) ? $_REQUEST['params'] : array ();

$functionName( $functionParams );

function getExtJSParams ()
{
    $validParams = array ('callback' => '','dir' => 'DESC','sort' => '','start' => 0,'limit' => 25,'filter' => '','search' => '','action' => '','xaction' => '','data' => '','status' => '','query' => '','fields' => ""
    );
    $result = array ();
    foreach ($validParams as $paramName => $paramDefault) {
        $result[$paramName] = isset( $_REQUEST[$paramName] ) ? $_REQUEST[$paramName] : isset( $_REQUEST[$paramName] ) ? $_REQUEST[$paramName] : $paramDefault;
    }
    return $result;
}

function sendJsonResultGeneric ($response, $callback)
{
    header( "Content-Type: application/json" );
    $finalResponse = G::json_encode( $response );
    if ($callback != '') {
        print $callback . "($finalResponse);";
    } else {
        print $finalResponse;
    }
}

function getNotesList ()
{
    extract( getExtJSParams() );
    require_once ("classes/model/AppNotes.php");
    if ((isset( $_REQUEST['appUid'] )) && (trim( $_REQUEST['appUid'] ) != "")) {
        $appUid = $_REQUEST['appUid'];
    } else {
        $appUid = $_SESSION['APPLICATION'];
    }
    $usrUid = (isset( $_SESSION['USER_LOGGED'] )) ? $_SESSION['USER_LOGGED'] : "";
    $appNotes = new AppNotes();
    $response = $appNotes->getNotesList( $appUid, '', $start, $limit );
    sendJsonResultGeneric( $response['array'], $callback );
}

function postNote ()
{
    extract( getExtJSParams() );
    if ((isset( $_REQUEST['appUid'] )) && (trim( $_REQUEST['appUid'] ) != "")) {
        $appUid = $_REQUEST['appUid'];
    } else {
        $appUid = $_SESSION['APPLICATION'];
    }
    $usrUid = (isset( $_SESSION['USER_LOGGED'] )) ? $_SESSION['USER_LOGGED'] : "";
    require_once ("classes/model/AppNotes.php");

    $noteContent = addslashes( $_POST['noteText'] );

    $appNotes = new AppNotes();
    $response = $appNotes->postNewNote( $appUid, $usrUid, $noteContent );

    sendJsonResultGeneric( $response, $callback );
}

