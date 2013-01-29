<?php
$response = new stdclass();
$response->status = isset($_SESSION['USER_LOGGED']);
if (isset($_REQUEST['dynaformEditorParams'])) {
  $_SESSION['Current_Dynafom']['Parameters'] = unserialize(stripslashes($_REQUEST['dynaformEditorParams']));
}
die(G::json_encode($response));