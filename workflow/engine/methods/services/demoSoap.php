<html>
<head>
<title>SugarCRM test webservices</title>
</head>
<style type="text/css">
ul.krumo-node {
	margin: 0px;
	padding: 0px;
	background-color: white;
}

ul.krumo-node ul {
	margin-left: 20px;
}

* html ul.krumo-node ul {
	margin-left: 24px;
}

div.krumo-root {
	border: solid 1px black;
	margin: 1em 0em;
	text-align: left;
}

ul.krumo-first {
	font: normal 11px tahoma, verdana;
	border: solid 1px white;
}

li.krumo-child {
	display: block;
	list-style: none;
	padding: 0px;
	margin: 0px;
	overflow: hidden;
}

div.krumo-element {
	cursor: default;
	display: block;
	clear: both;
	white-space: nowrap;
	background-color: white;
	background-image: url(/Krumo/skins/schablon.com/empty.gif);
	background-repeat: no-repeat;
	background-position: 6px 5px;
	padding: 2px 0px 3px 20px;
}

* html div.krumo-element {
	padding-bottom: 3px;
	line-height: 13px;
}

a.krumo-name {
	color: navy;
	font: bold 13px courier new;
	line-height: 12px;
}
</style>
<?php

if (isset( $_POST["epr"] )) {
    $_SESSION['END_POINT'] = $_POST["epr"];
}
$endpoint = isset( $_SESSION['END_POINT'] ) ? $_SESSION['END_POINT'] : 'http://sugar.opensource.colosa.net/soap.php';

$sessionId = isset( $_SESSION['SESSION_ID'] ) ? $_SESSION['SESSION_ID'] : '';
?>
<form method="post" action="">

	<div class="krumo-root">
		<ul class="krumo-node krumo-first">
			<div class="krumo-element">
				End Point <input type="text" size="80" maxlength="160" name="epr"
					value="<?php echo $endpoint ?>"> <input type="submit"
					value="change endpoint" name="action">
			</div>
		</ul>
	</div>
</form>

<form method="post" action="">
	<div class="krumo-root">
		<a class="krumo-name">login</a>
		<ul class="krumo-node krumo-first">
			<div>
				User Id <input type="text" size="16" maxlength="20" name="user"
					value="admin"> Password <input type="text" size="16" maxlength="20"
					name="pass" value="sample"> <input type="submit" value="login"
					name="action">
			</div>
		</ul>
	</div>
</form>

<form method="post" action="">
	<div class="krumo-root">
		<a class="krumo-name">createUser</a>
		<ul class="krumo-node krumo-first">
			<div class="krumo-element">
				Session Id <input type="text" size="36" maxlength="32"
					name="sessionid" value="<?php print $sessionId ?>">
			</div>
			<div class="krumo-element">
				User Id <input type="text" size="16" maxlength="20" name="userid"
					value=""> <img src="/Krumo/skins/schablon.com/empty.gif"> First
				Name <input type="text" size="16" maxlength="20" name="firstname"
					value=""> <img src="/Krumo/skins/schablon.com/empty.gif"> Last Name
				<input type="text" size="16" maxlength="20" name="lastname" value="">
				<img src="/Krumo/skins/schablon.com/empty.gif"> Email <input
					type="text" size="16" maxlength="20" name="email" value=""> <img
					src="/Krumo/skins/schablon.com/empty.gif"> Role <input type="text"
					size="16" maxlength="20" name="role" value=""> <img
					src="/Krumo/skins/schablon.com/empty.gif"> <input type="submit"
					value="createUser" name="action">
			</div>
		</ul>
	</div>
</form>

<form method="post" action="">
	<div class="krumo-root">
		<a class="krumo-name">assignUserToGroup</a>
		<ul class="krumo-node krumo-first">
			<div class="krumo-element">
				Session Id <input type="text" size="36" maxlength="32"
					name="sessionid" value="<?php print $sessionId ?>">
			</div>
			<div class="krumo-element">
				User Id <input type="text" size="16" maxlength="20" name="userid"
					value=""> <img src="/Krumo/skins/schablon.com/empty.gif"> Group Id
				<input type="text" size="16" maxlength="20" name="groupid" value="">
				<input type="submit" value="assignUserToGroup" name="action">
			</div>
		</ul>
	</div>
</form>


<form method="post" action="">
	<div class="krumo-root">
		<a class="krumo-name">newCase</a>
		<ul class="krumo-node krumo-first">
			<div class="krumo-element">
				Session Id <input type="text" size="36" maxlength="32"
					name="sessionid" value="<?php print $sessionId ?>">
			</div>
			<div class="krumo-element">
				Process Id <input type="text" size="16" maxlength="20"
					name="processid" value=""> <img
					src="/Krumo/skins/schablon.com/empty.gif"> Variables <input
					type="text" size="16" maxlength="20" name="variables" value=""> <input
					type="submit" value="newCase" name="action">
			</div>
		</ul>
	</div>
</form>

<?php

if (! isset( $_POST["action"] ) or $_POST["action"] == 'change endpoint') {
    die();
}
$action = $_POST["action"];
//krumo ($_POST);


ini_set( "soap.wsdl_cache_enabled", "0" ); // disabling WSDL cache


switch ($action) {
    case 'login':
        $user = $_POST["user"];
        $pass = md5( $_POST["pass"] );
        $wsdl = $endpoint;
        //$client = new SoapClient( $endpoint );
        $client = new SoapClient( null, array ('location' => $endpoint,'uri' => 'http://www.sugarcrm.com/sugarcrm','soap_version' => SOAP_1_1,        //SOAP_1_2 - 1.2 not supported by sugar nusoap
'trace' => 1,'exceptions' => 0,'compression' => SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_GZIP | 5
        ) );

        $params = array ('user_name' => $user,'password' => $pass,'version' => '1'
        );
        $result = $client->__SoapCall( 'login', array ('user_auth' => $params,'application_name' => 'ProcessMaker'
        ) );
        if ($result->error->number == 0) {
            $_SESSION['SESSION_ID'] = $result->id;
            $session = $result->id;

            $res = $client->__getFunctions();
            krumo( $res );

            $params = array ('session' => $result->id
            );
            $res = $client->__SoapCall( 'is_user_admin', array ($session
            ) );
            if ($res == 1)
                print "is Administrator user";

            $first_name = 'juan';
            $last_name = 'perez';
            $phone = '7235131';
            $fax = '2454545';
            $companyname = 'ABC company';
            $prod_desc = 'descripcion del prod 1 ';
            $user_guid = '';
            $set_entry_params = array ('session' => $session,'module_name' => 'Leads',
                            'name_value_list' => array (array ('name' => 'last_name','value' => $last_name
                            ),array ('name' => 'status','value' => 'New'
                            ),array ('name' => 'phone_work','value' => $phone
                            ),array ('name' => 'phone_fax','value' => $fax
                            ),array ('name' => 'account_name','value' => $companyname
                            ),array ('name' => 'lead_source','value' => 'Web Site'
                            ),array ('name' => 'description','value' => $prod_desc
                            ),array ('name' => 'email1','value' => 'juan@colosa.com'
                            ),array ('name' => 'assigned_user_id','value' => $user_guid
                            )
                            )
            );
            $res = $client->__SoapCall( 'set_entry', $set_entry_params );
            krumo( $res );

            //$query = "contacts.email1 != '' ";
            //$orderby = 'email1 desc ';
            $query = '';
            $orderby = '';
            $fields = array ('id','first_name','last_name','account_name','account_id','email1','phone_work'
            );
            $params = array ($session,'Leads',$query,$orderby,0,$fields,100,false
            );
            $res = $client->__SoapCall( 'get_entry_list', $params );
            krumo( $res );
        }
        break;
    case 'processList':
        $wsdl = PATH_METHODS . "services" . PATH_SEP . "pmos.wsdl";
        $endpoint = $wsdl;
        $client = new SoapClient( $endpoint );
        $params = array ('sessionId' => $sessionId
        );
        $result = $client->__SoapCall( 'processesList', array ($params
        ) );

        krumo( $result );
        die();
        break;
    default:
        krumo( $_POST );
        die();
}

?>
<div class="krumo-root">
	<ul class="krumo-node krumo-first">
		<li class="krumo-child">
			<div class="krumo-element">
				status_code (<em class="krumo-type">Integer</em>) <strong
					class="krumo-integer"><?php echo $result->error->number ?></strong>
			</div>
			<div class="krumo-element">
				message (<em class="krumo-type">string</em>) <strong
					class="krumo-integer"><?php echo $result->error->name ?></strong>
			</div>
			<div class="krumo-element">
				timestamp (<em class="krumo-type">string</em>) <strong
					class="krumo-integer"><?php echo $result->error->description ?></strong>
			</div>
		</li>
	</ul>
</div>


