<?php
global $G_PUBLISH;

use ProcessMaker\Core\System;

try {
    if ($RBAC->singleSignOn) {
        $_SESSION['__USER_LOGGED_SSO__']  = $RBAC->userObj->fields['USR_UID'];
        $_SESSION['__USR_USERNAME_SSO__'] = $RBAC->userObj->fields['USR_USERNAME'];
    } else {
        if (!isset($_SESSION['__USER_LOGGED_SSO__'])) {
            $u = '';

            if (isset($_POST['form']['URL']) && $_POST['form']['URL'] != '') {
                $u = $_POST['form']['URL'];
            } else {
                if (isset($_GET['u']) && $_GET['u'] != '') {
                    $u = $_GET['u'];
                }
            }

            header(
                'Location: /sys' . SYS_SYS . '/' . SYS_LANG . '/' . SYS_SKIN .
                '/login/login' . (($u != '')? '?u=' . $u : '')
            );

            exit(0);
        }
    }

    $userUid = (isset($_SESSION['USER_LOGGED']))? $_SESSION['USER_LOGGED'] : ((isset($_SESSION['__USER_LOGGED_SSO__']))? $_SESSION['__USER_LOGGED_SSO__'] : '');

    /*----------------------------------********---------------------------------*/

    $arraySystemConfiguration = System::getSystemConfiguration('', '', SYS_SYS);

    //Set User Time Zone
    $user = UsersPeer::retrieveByPK($userUid);

    if (!is_null($user)) {
        $userTimeZone = $user->getUsrTimeZone();

        if (trim($userTimeZone) == '') {
            $userTimeZone = $arraySystemConfiguration['time_zone'];
        }

        $_SESSION['USR_TIME_ZONE'] = $userTimeZone;
    }

    //Get default user location
    if (isset($_POST['form']['URL']) && $_POST['form']['URL'] != '') {
        $location = $_POST['form']['URL'];
    } else {
        if (isset($_GET['u']) && $_GET['u'] != '') {
            $location = $_GET['u'];
        } else {
            $userProperty = new UsersProperties();

            $location = $userProperty->redirectTo($userUid);
        }
    }

    /*----------------------------------********---------------------------------*/

    setcookie('singleSignOn', '1', time() + (24 * 60 * 60), '/');

    initUserSession(
        $_SESSION['__USER_LOGGED_SSO__'],
        $_SESSION['__USR_USERNAME_SSO__']
    );
    unset($_SESSION['__USER_LOGGED_SSO__'], $_SESSION['__USR_USERNAME_SSO__']);

    G::header('Location: ' . $location);
} catch (Exception $e) {
    $arrayData = [];
    $arrayData['MESSAGE'] = $e->getMessage();

    $G_PUBLISH = new Publisher();
    $G_PUBLISH->AddContent('xmlform', 'xmlform', 'login/showMessage', '', $arrayData);

    G::RenderPage('publish');
}
