<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">

<html>
<head>
	<title>PEAR SOAP Interop</title>
</head>
<?php
require_once 'config.php';
require_once 'registrationAndNotification.php';

$tests = array('Round 2 Base',
                'Round 2 Group B', 
                'Round 2 Group C', 
                'Round 3 Group D Compound 1',
                'Round 3 Group D Compound 2',
                'Round 3 Group D DocLit',
                'Round 3 Group D DocLitParams',
                'Round 3 Group D Import 1',
                'Round 3 Group D Import 2',
                'Round 3 Group D Import 3',
                'Round 3 Group D RpcEnc'
        );

// get our endpoint
$baseurl = 'http://'.$_SERVER['SERVER_NAME'].':'.$_SERVER['SERVER_PORT'].$interopConfig['basedir'];
?>
<body>

<h2 align='center'>PEAR SOAP Interop</h2>
<p>Welcome to the PEAR SOAP Interop pages.  These pages are set up for
SOAP Builder interop tests.</p>
<table width="90%" border="1" cellspacing="0" cellpadding="2" align="center">
<?php

foreach ($tests as $test) {
    $ep = getLocalInteropServer($test,0,$baseurl);
    echo "<tr><td>$test</td><td>\n";
    echo "WSDL: <a href=\"{$ep->wsdlURL}\">{$ep->wsdlURL}</a><br>\n";
    echo "Endpoint: {$ep->endpointURL}<br>\n";
    echo "</td></tr>\n";
}

?>
</table>
<h3>Interop Client</h3>

<p>
Notes:
Tests are done both "Direct" and with "WSDL".  WSDL tests use the supplied interop WSDL
to run the tests against.  The Direct method uses an internal prebuilt list of methods and parameters
for the test.</p>
<p>
Tests are also run against two methods of generating method parameters.  The first, 'php', attempts
to directly serialize PHP variables into soap values.  The second method, 'soapval', uses a SOAP_Value
class to define what the type of the value is.  The second method is more interopable than the first
by nature.
</p>

<h3>Interop Client Test Results</h3>
<p>This is a database of the current test results using PEAR SOAP Clients against interop servers.</p>
<p>
More detail (wire) about errors (marked yellow or red) can be obtained by clicking on the
link in the result box.  If we have an HTTP error
attempting to connect to the endpoint, we will mark all consecutive attempts as errors, and skip
testing that endpoint.  This reduces the time it takes to run the tests if a server is unavailable.
WSDLCACHE errors mean we cannot retreive the WSDL file specified for the endpoint.
</p>

<ul>
<li><a href="interop_client_results.php?test=Round+2+Base&amp;type=php&amp;wsdl=0">Base results using PHP native types</a></li>
<li><a href="interop_client_results.php?test=Round+2+Base&amp;type=soapval&amp;wsdl=0">Base results using SOAP types</a></li>
<li><a href="interop_client_results.php?test=Round+2+Base&amp;type=php&amp;wsdl=1">Base results using PHP native types with WSDL</a></li>

<li><a href="interop_client_results.php?test=Round+2+Group+B&amp;type=php&amp;wsdl=0">Group B results using PHP native types</a></li>
<li><a href="interop_client_results.php?test=Round+2+Group+B&amp;type=soapval&amp;wsdl=0">Group B results using SOAP types</a></li>
<li><a href="interop_client_results.php?test=Round+2+Group+B&amp;type=php&amp;wsdl=1">Group B results using PHP native types with WSDL</a></li>

<li><a href="interop_client_results.php?test=Round+2+Group+C&amp;type=php&amp;wsdl=0">Group C results using PHP native types</a></li>
<li><a href="interop_client_results.php?test=Round+2+Group+C&amp;type=soapval&amp;wsdl=0">Group C results using SOAP types</a></li>
<li><a href="interop_client_results.php?test=Round+2+Group+C&amp;type=php&amp;wsdl=1">Group C results using PHP native types with WSDL</a></li>

<li><a href="interop_client_results.php?test=Round+3+Group+D+Compound+1&amp;type=php&amp;wsdl=1">Group D Compound 1 results using PHP native types with WSDL</a></li>
<li><a href="interop_client_results.php?test=Round+3+Group+D+Compound+2&amp;type=php&amp;wsdl=1">Group D Compound 2 results using PHP native types with WSDL</a></li>
<li><a href="interop_client_results.php?test=Round+3+Group+D+DocLit&amp;type=php&amp;wsdl=1">Group D DocLit results using PHP native types with WSDL</a></li>
<li><a href="interop_client_results.php?test=Round+3+Group+D+DocLitParams&amp;type=php&amp;wsdl=1">Group D DocLitParams results using PHP native types with WSDL</a></li>
<li><a href="interop_client_results.php?test=Round+3+Group+D+Import+1&amp;type=php&amp;wsdl=1">Group D Import 1 results using PHP native types with WSDL</a></li>
<li><a href="interop_client_results.php?test=Round+3+Group+D+Import+2&amp;type=php&amp;wsdl=1">Group D Import 2 results using PHP native types with WSDL</a></li>
<li><a href="interop_client_results.php?test=Round+3+Group+D+Import+3&amp;type=php&amp;wsdl=1">Group D Import 3 results using PHP native types with WSDL</a></li>
<li><a href="interop_client_results.php?test=Round+3+Group+D+RpcEnc&amp;type=php&amp;wsdl=1">Group D RpcEnc results using PHP native types with WSDL</a></li>

<li><a href="interop_client_results.php">Show All Results</a></li>
</ul>
</body>
</html>
