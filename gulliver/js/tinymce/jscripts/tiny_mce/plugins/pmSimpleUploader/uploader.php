<html>
<head>
	<title>Upload an Output Document</title>
	<script type="text/javascript" src="../../tiny_mce_popup.js" ></script>
	<script type="text/javascript" src="editor_plugin_src.js" ></script>
	<base target="_self" />
    <script type="text/javascript">
    function validateForm()
    {
        var flagv = true;
        var msgv  = "";

        var fileName = document.getElementById("File1").value;

        if (fileName == "") {
            flagv = false;
            msgv  = msgv + ((msgv != "")? "\n" : "") + "No file chosen";
        }

        if (fileName != "" && !/\.html?$/i.test(fileName)) {
            flagv = false;
            msgv  = msgv + ((msgv != "")? "\n" : "") + "Extension of file invalid, only allowed extensions html and htm";
        }

        if (flagv) {
            document.getElementById("containerDataForm").style.display = "none";
            document.getElementById("containerProgressBar").style.display = "inline";
        } else {
            alert(msgv);
        }

        return flagv;
    }
    </script>
</head>
<body>
<?php
    $filter = new InputFilter();
    if(isset($_GET["q"])) {
        $_GET["q"] = $filter->xssFilterHard($_GET["q"]);
        $Action = $_GET["q"];
    } else {
        $Action = "none";
    }
	//$Action = isset($_GET["q"]) ? $_GET["q"] : "none";
	if($Action =="none"){
            displayUploadForm();
        }else if($Action=="upload"){
            uploadContentFile();
        }
?>
</body>
</html>

<?php

// displays the upload form
function displayUploadForm()
{
    $filter = new InputFilter();
    if(isset($_SERVER["QUERY_STRING"])) {
        $_SERVER["QUERY_STRING"] = $filter->xssFilterHard($_SERVER["QUERY_STRING"],'url');
    }
    
    $html = "
    <div id=\"containerDataForm\">
        <form method=\"post\" enctype=\"multipart/form-data\" action=\"uploader.php?" . $_SERVER["QUERY_STRING"] . "&q=upload\" onsubmit=\"return validateForm();\">
            <br/>
            File:&nbsp;
            <input type=\"file\" id=\"File1\" name=\"upload_file\" />&nbsp;(*.html, *.htm)
            <br/>
            <input type=\"submit\" name=\"Upload File\" value=\"Upload File\" style=\"border: 1px solid #1ba385; float: right; margin-top: 30px; margin-right: -4px; font-size: 10px; display: inline-block; text-decoration: none; padding: 8px 24px; -moz-border-radius: 2px; -ms-border-radius: 2px; -o-border-radius: 2px; background-color: #1fbc99; color: white;\" />
            <input type=\"button\" onclick=\"tinyMCEPopup.close();\"name=\"Cancel\" value=\"Cancel\" style=\"border: 1px solid #e14333; float: right; margin-top: 30px; margin-right: 8px; font-size: 10px; display: inline-block; text-decoration: none; padding: 8px 24px; -moz-border-radius: 2px; -ms-border-radius: 2px; -o-border-radius: 2px; background-color: #e4655f; color: white;\" />
        </form>
    </div>

    <div id=\"containerProgressBar\" style=\"display: none;\">
        Uploading...&nbsp;<img src=\"progress.gif\" alt=\"\" title=\"Uploading...\" />
    </div>
    ";

    echo $html;
}
// uploads the file to the destination path, and returns a link with link path substituted for destination path
function uploadContentFile()
{
    $filter = new InputFilter();
    $_FILES["upload_file"] = $filter->xssFilterHard($_FILES["upload_file"]);
    
	$StatusMessage = "";
	$ActualFileName = "";
    $DestPath = sys_get_temp_dir();
    $aux='';
    $chain = preg_replace("/\r\n+|\r+|\n+|\t+/i", " ", file_get_contents($_FILES["upload_file"]["tmp_name"]));
    $chain=preg_replace('#<head(.*?)>(.*?)</head>#is', ' ', $chain);
    $chain=strip_tags($chain, '<address><label><canvas><option><ol><u><textarea><em><h1><h2><h3><h4><h5><h6><section><tbody><tr><th><td><hr><center><br><b><img><p><a><table><caption><thead><div><ul><li><form><input><strong><span><small><button><figure>');
    $chain=str_replace ('"',"'",$chain);
    updateEditorContent(trim($chain));
    closeWindow();
}


function showPopUp($PopupText)
{

    $filter = new InputFilter();
    $PopupText = $filter->xssFilterHard($PopupText);
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

