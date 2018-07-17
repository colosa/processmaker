<?php
  //this redirect a valid workspace to his encrypted url, for invalid workspaces show the create workspace page.
  
//  define ('RED_HAT_8', 1);
//  define("URL_KEY", 'c0l0s40pt1mu59r1m3' );
//  define("ENABLE_ENCRYPT", 'yes' );


  $filter = new InputFilter();
  $COMPLETE_URI = $filter->xssFilterHard($_SERVER["REQUEST_URI"]);

  $webAddress = substr($COMPLETE_URI,1);
  $COMPLETE_URI = strtolower ($COMPLETE_URI) . "/mNE/qsll/n9KX1Z4/n9KX1Z6hnKTd4A";

  if ( !is_file( "/shared/workflow_data/sites/".$webAddress."/db.php") ) {
 	  $server = getenv ( "SERVER_NAME");
 	  //redirect to https in case the workspace it doesn't exist
    if (( $server == 'www.processmaker.com' )||( $server == 'processmaker.com' ))
	  	$COMPLETE_URI = "https://www.processmaker.com";
	  else
	    //go to install workspace
	  	$COMPLETE_URI	= "/install/createNewWebSite.php?webAddress=".$webAddress;
  }

?>
<html>
<head>
<title>Redirector</title>
<meta http-equiv="PRAGMA" content="NO-CACHE" />
<meta http-equiv="CACHE-CONTROL" content="NO-STORE" />
<meta http-equiv="REFRESH" content="0;URL=<?php echo $COMPLETE_URI ?>" />
</head>
<body bgcolor="White">
</body>
</html>