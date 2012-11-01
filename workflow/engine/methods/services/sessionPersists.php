<?php
$response = new stdclass();
$response->status = isset($_SESSION['USER_LOGGED']);
die(G::json_encode($response));