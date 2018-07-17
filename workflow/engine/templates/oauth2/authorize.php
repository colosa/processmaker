<?php
require_once PATH_CORE . 'src/ProcessMaker/Services/OAuth2/PmPdo.php';

$filter = new InputFilter();
$_GET = $filter->xssFilterHard($_GET,"url");
$_SERVER["QUERY_STRING"] = $filter->xssFilterHard($_SERVER["QUERY_STRING"],"url");

list($host, $port) = strpos(DB_HOST, ':') !== false ? explode(':', DB_HOST) : array(DB_HOST, '');
$port = empty($port) ? '' : ";port=$port";

$dsn      = DB_ADAPTER.':host='.$host.';dbname='.DB_NAME.$port;
$username = DB_USER;
$password = DB_PASS;

$this->scope = array(
    'view_processes' => 'View Processes',
    'edit_processes' => 'Edit Processes'
);

// $dsn is the Data Source Name for your database, for exmaple "mysql:dbname=my_oauth2_db;host=localhost"
$storage = new ProcessMaker\Services\OAuth2\PmPdo(array('dsn' => $dsn, 'username' => $username, 'password' => $password));



$clientId = $_GET['client_id'];
$requestedScope = isset($_GET['scope']) ? $_GET['scope'] : '';
$requestedScope = empty($requestedScope) ? array() : explode(' ', $requestedScope);

if (! empty($clientId)) {
    $clientDetails = $storage->getClientDetails($clientId);
    //g::pr($clientDetails); die;
}

$response = array(
    'client_details' => $clientDetails,
    'query_string'   => $_SERVER['QUERY_STRING'],
    'supportedScope' => $this->scope,
    'requestedScope' => $requestedScope
);
$response = $filter->xssFilterHard($response,"url");
?>

<table width="100%" cellspacing="0" cellpadding="0" border="0">
    <tbody>
    <tr>
        <td width="100%" style="height:25px"></td>
    </tr>
    <tr>
        <td width="100%" align="center">
            <table width="100%" cellspacing="0" cellpadding="0" border="0" style="padding-top: 3px">
                <tbody><tr>
                    <td align="center">
                        <div align="center" style="; margin:0px;" id="publisherContent[0]">
                            <form  style="margin:0px;" enctype="multipart/form-data" method="post" class="formDefault" action="authorize?<?php echo $response['query_string']?>" name="authorizeForm" id="authorizeForm">
                                <div style="width:400px; padding-left:0; padding-right:0; border-width:1;" class="borderForm">
                                    <div class="boxTop"><div class="a"></div><div class="b"></div><div class="c"></div></div>
                                    <div style="height:100%;" class="content">
                                        <table width="99%">
                                            <tbody><tr>
                                                <td valign="top">
                                                    <table width="100%" cellspacing="0" cellpadding="0" border="0">
                                                        <tbody>
                                                        <tr>
                                                            <td align="" colspan="2" class="FormTitle">
                                                                <span name="form[TITLE]" id="form[TITLE]">
                                                                    <strong><?php echo $response['client_details']['CLIENT_NAME']?></strong> would like to access the following data:
                                                                </span>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td align="" colspan="2" class="FormSubTitle">
                                                                <span name="form[TITLE]" id="form[TITLE]">
                                                                    <ul>
                                                                        <?php foreach($response['requestedScope'] as $scope) {?>
                                                                        <li><?php echo $response['supportedScope'][$scope] ?></li>
                                                                        <?php } ?>
                                                                    </ul>
                                                                    <p>It will use this data to:</p>
                                                                    <ul>
                                                                        <li>integrate with ProcessMaker</li>
                                                                        <li>miscellaneous purposes</li>
                                                                    </ul>

                                                                    <div align="center">
                                                                        <input type="submit" value="Yes, I authorize this request" name="authorize" class="module_app_button___gray " value="1">
                                                                        <input type="button" value="Reject this request" name="reject_btn" id="reject_btn" class="module_app_button___gray " onclick="doSubmit()">
                                                                        <input type="hidden" name="authorize" id="authorize" value="1">
                                                                    </div>
                                                                </span>
                                                            </td>
                                                        </tr>
                                                        </tbody>
                                                    </table>
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="boxBottom"><div class="a"></div><div class="b"></div><div class="c"></div></div>
                                </div>
                            </form>
                        </div>
                    </td>
                </tr>
                </tbody>
            </table>
        </td>
    </tr>
    </tbody>
</table>


<script>
function doSubmit()
{
    document.getElementById('authorize').value = '0';
    document.forms[0].submit();
}

</script>