<head>
	<title>Upload an Output Document</title>
	<script type="text/javascript" src="../../tiny_mce_popup.js" ></script>
	<script type="text/javascript" src="editor_plugin_src.js" ></script>
	<base target="_self" />
</head>
<body>
<?php
	$Action = isset($_GET["q"]) ? $_GET["q"] : "none";

	if($Action =="none"){
            displayUploadForm();
        }else if($Action=="upload"){
            uploadVariablePicker();
        }
?>
</body>
</html>

<?php

// displays the upload form
function displayUploadForm()
{/*
	echo '<form action="uploader.php?'.$_SERVER["QUERY_STRING"].'&q=upload" method="post" enctype="multipart/form-data" onsubmit="">'
                //.'Variable Name: <br/>'
	            .'<div id="d_variables">'
                .'<table width="80%">'

                .'<tr>'
                .'<td width="33%">'
                .'<label for="type_label">Type Variable</label>'
                .'</td>'

                .'<td width="33%">'
                .'<label for="prefix_label">Prefix</label>'
                .'</td>'

                .'<td width="33%">'
                .'<label for="variables_label">Variables</label>'
                .'</td>'
                .'</tr>'

                .'<tr>'
                .'<td>'
                .'<select name="type_variables">'
                .'<option value="all">All Variables</option>'
                .'<option value="system">System Variables</option>'
                .'<option value="process">Process Variables</option>'
                .'</select> &nbsp;&nbsp;&nbsp;&nbsp;'
                .'</td>'

                .'<td width="33%">'
                .'<select name="prefix">'
                .'<option value="quotes">@#</option>'
                .'<option value="float">@@</option>'
                .'<option value="encoding">@?</option>'
                .'</select> &nbsp;&nbsp;&nbsp;&nbsp;'
                .'</td>'

                .'<td width="33%">'
                .'<select name="variables">'
                .'<option value="quotes">@@SYS_SYS</option>'
                .'<option value="float">@@SYS_LANG</option>'
                .'<option value="encoding">@@SYS_SKIN</option>'
                .'</select>'
                .'</td>'
                .'</tr>'
                .'</table>'
                .'</div>'

                .'<br>'
                .'<div id="desc_variables">'
                .'<table border="1" width="100%">'
                .'<tr width="40%">'
                .'<td>Result</td>'
                .'<td>@#SYS_LANG</td>'
                .'</tr>'
                .'<tr width="60%">'
                .'<td>Description</td>'
                .'<td>Description @#SYS_LANG</td>'
                .'</tr>'
                .'</table>'
                .'</div>'

                .'<br>'
                .'<div id="desc_variables">'
                .'<label for="desc_prefix">* @# Replace de value in quotes</label>'
                .'</div>'

                //.'<br/><input type="text" size="20" name="upload_variable" ID="Text1"/>'
                // .'<input type="submit" name="Variable" value="Variable" style="width: 100px;" onclick="document.getElementById(\'progress_div\').style.visibility=\'visible\';"/>'
                //.'  <div id="progress_div" style="visibility: hidden;"><img src="progress.gif" alt="wait..." style="padding-top: 5px;"></div>'
             .'</form>';
             */
}
// uploads the file to the destination path, and returns a link with link path substituted for destination path
function uploadVariablePicker()
{
    $StatusMessage = "";
	$ActualFileName = "";
	$FileObject = $_REQUEST["upload_variable"]; // find data on the file

	updateEditorContent(trim($FileObject));
    closeWindow();
}


function showPopUp($PopupText)
{
	echo "<script type=\"text/javascript\" language=\"javascript\">alert (\"$PopupText\");</script>";
}

function updateEditorContent($serializedHTML)
{
    echo "<script type=\"text/javascript\" language=\"javascript\">updateEditorContent(\"".$serializedHTML."\");</script>";
}

function closeWindow()
{
	echo '
            <script language="javascript" type="text/javascript">
                    closePluginPopup();
            </script>
         ';
}
?>

