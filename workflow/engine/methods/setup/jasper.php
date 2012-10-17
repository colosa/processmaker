<?php
G::LoadClass( 'jasperReports' );
$oJasper = new jasperReports( '192.168.0.51', 8080, 'jasperadmin', 'jasperadmin' );

$response = $oJasper->ws_list( "/" );

if (is_object( $response ) && get_class( $response ) == 'SOAP_Fault') {
    $errorMessage = $response->getFault()->faultstring;
} else {
    $folders = $oJasper->getResourceDescriptors( $response );
}

//$result = $oJasper->ws_put();
krumo( $response );

//execute a report


$currentUri = "/reports/samples/Employees";
$result = $oJasper->ws_get( $currentUri );

$folders = $oJasper->getResourceDescriptors( $result );

if (count( $folders ) != 1 || $folders[0]['type'] != 'reportUnit') {
    echo "<H1>Invalid RU ($currentUri)</H1>";
    echo "<pre>$result</pre>";
    exit();
}

$reportUnit = $folders[0];

// 2. Prepare the parameters array looking in the $_GET for params
// starting with PARAM_ ...
//


$report_params = array ();

$moveToPage = "jasper?uri=$currentUri";

foreach (array_keys( $_GET ) as $param_name) {
    if (strncmp( "PARAM_", $param_name, 6 ) == 0) {
        $report_params[substr( $param_name, 6 )] = $_GET[$param_name];
    }

    //    if ($param_name != "page" && $param_name != "uri") {
    //      $moveToPage .= "&".urlencode($param_name)."=". urlencode($_GET[$param_name]);
    //    }
}

$formatReport = RUN_OUTPUT_FORMAT_XML;
$formatReport = RUN_OUTPUT_FORMAT_CSV;
$formatReport = RUN_OUTPUT_FORMAT_RTF;
$formatReport = RUN_OUTPUT_FORMAT_PDF;
$formatReport = RUN_OUTPUT_FORMAT_HTML;
$moveToPage .= "&page=";

// 3. Execute the report
$output_params = array ();
$output_params[RUN_OUTPUT_FORMAT] = $formatReport;

if ($formatReport == RUN_OUTPUT_FORMAT_HTML) {
    //$pageReport = isset ( $_GET['page'] ) ? $_GET['page'] : 1;
    //$output_params[RUN_OUTPUT_PAGE] = $pageReport;
    //$output_params[RUN_OUTPUT_IMAGES_URI] = '/sysos/'. SYS_LANG. '/classic';
}

$result = $oJasper->ws_runReport( $currentUri, $report_params, $output_params, $attachments );

// 4.
if (is_object( $result ) && get_class( $result ) == 'SOAP_Fault') {
    $errorMessage = $result->getFault()->faultstring;

    echo $errorMessage;
    exit();
}

$operationResult = $oJasper->getOperationResult( $result );

if ($operationResult['returnCode'] != '0') {
    echo "Error executing the report:<br><font color=\"red\">" . $operationResult['returnMessage'] . "</font>";
    exit();
}

if (is_array( $attachments )) {
    //krumo ($attachments);


    switch ($formatReport) {
        case RUN_OUTPUT_FORMAT_PDF:
            header( "Content-type: application/pdf" );
            echo ($attachments["cid:report"]);
            break;
        case RUN_OUTPUT_FORMAT_HTML:
            // 1. Save attachments....
            // 2. Print the report....
            header( "Content-type: text/html" );
            foreach (array_keys( $attachments ) as $key) {
                if ($key != "cid:report") {
                    $f = fopen( "images/" . substr( $key, 4 ), "w" );
                    fwrite( $f, $attachments[$key] );
                    fclose( $f );
                }
            }

            echo "<center>";
            $prevpage = ($pageReport > 0) ? $pageReport - 1 : 0;
            $nextpage = $pageReport + 1;

            echo "<a href=\"" . $moveToPage . $prevpage . "\">Prev page</a> | <a href=\"" . $moveToPage . $nextpage . "\">Next page</a>";
            echo "</center><hr>";

            echo $attachments["cid:report"];
            //print_r(array_keys($attachments));
            break;
        case RUN_OUTPUT_FORMAT_CSV:
        case RUN_OUTPUT_FORMAT_XLS:
            header( 'Content-type: application/xls' );
            header( 'Content-Disposition: attachment; filename="report.xls"' );
            echo ($attachments["cid:report"]);
            break;
        case RUN_OUTPUT_FORMAT_RTF:
            header( 'Content-type: text/rtf' );
            header( 'Content-Disposition: attachment; filename="report.rtf"' );
            echo ($attachments["cid:report"]);
            break;
        default:
            //header ( 'Content-type: application/xls' );
            //header ( 'Content-Disposition: attachment; filename="report.xls"');
            echo ($attachments["cid:report"]);
            break;
    }
    exit();
} else
    echo "No attachment found!";

