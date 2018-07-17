<html>
    <head>
        <script>
<?php
/**
 * This page is redirected from the login page.
 */
$userUid = $_SESSION['USER_LOGGED'];
$userInfo = UsersPeer::retrieveByPK($userUid);
if (empty($userInfo)) {
    $result = [
        'user_logged' => $userUid,
        'userName'    => '',
        'firstName'   => '',
        'lastName'    => '',
        'mail'        => '',
        'image'       => '../users/users_ViewPhoto?t=' . microtime(true),
    ];
} else {
    $result = [
        'user_logged' => $userUid,
        'userName'    => $userInfo->getUsrUsername(),
        'firstName'   => $userInfo->getUsrFirstName(),
        'lastName'    => $userInfo->getUsrLastName(),
        'mail'        => $userInfo->getUsrEmail(),
        'image'       => '../users/users_ViewPhoto?t=' . microtime(true),
    ];
}
?>
                parent.fullfill(<?= G::json_encode($result) ?>);
        </script>
    </head>
</html>