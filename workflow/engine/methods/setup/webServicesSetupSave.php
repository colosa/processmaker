<?php
unset( $_SESSION['WS_SESSION_ID'] );

if ($_POST['form']['WS_PROTOCOL'] != '' && $_POST['form']['WS_WORKSPACE'] != '') {
    if ($_POST['form']['WS_PORT'] != '') {
        $_SESSION['END_POINT'] = $_POST['form']['WS_PROTOCOL'] . '://' . $_POST['form']['WS_HOST'] . ':' . $_POST['form']['WS_PORT'] . '/sys' . $_POST['form']['WS_WORKSPACE'] . '/en/classic/services/wsdl2';
        G::header( 'location: webServices?x=1' );
    } else {
        $_SESSION['END_POINT'] = $_POST['form']['WS_PROTOCOL'] . '://' . $_POST['form']['WS_HOST'] . '/sys' . $_POST['form']['WS_WORKSPACE'] . '/en/classic/services/wsdl2';
        G::header( 'location: webServices?x=1' );
    }
    $_SESSION['WS_WORKSPACE'] = $_POST['form']['WS_WORKSPACE'];
} else {
    G::header( 'location: webServices?x=0' );
}

