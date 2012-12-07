/**
* Name: editor_plugin_src.js (for pmSimpleUploader tinyMCE plugin)
**/

(function(){
    var strPluginURL;
    tinymce.create('tinymce.plugins.pmSimpleUploaderPlugin', {
        init: function(ed, url) 
        {
            strPluginURL = url;                                         // store the URL for future use..
            ed.addCommand('mcepmSimpleUploader', function() {
                pmSimpleUploader();              
            });
            ed.addButton('pmSimpleUploader', {
                title: 'pmSimpleUploader',
                label : 'Upload File',
                cmd: 'mcepmSimpleUploader',
                image: url + '/img/pmSimpleUploader.png'
            }); 
        },
        createControl: function(n, cm) {
            return null;
        },
        getPluginURL: function() {
            return strPluginURL;
        }
    });
    tinymce.PluginManager.add('pmSimpleUploader', tinymce.plugins.pmSimpleUploaderPlugin);
})();

/**
 * this function can get called from the plugin inint (above) or from the callback on advlink/advimg plugins..
 * in the latter case, win and type will be set.. In the rist case, we will just update the main editor window
 * with the path of the uploaded file
 */
function pmSimpleUploader(field_name, url, type, win) {    
    var strPluginPath  = tinyMCE.activeEditor.plugins.pmSimpleUploader.getPluginURL();                               // get the path to the uploader plugin    
    var strUploaderURL = strPluginPath + "/uploader.php";                                                           // generate the path to the uploader script    
    var strUploadPath  = tinyMCE.activeEditor.getParam('plugin_pmSimpleUploader_upload_path');                       // get the relative upload path
    var strSubstitutePath = tinyMCE.activeEditor.getParam('plugin_pmSimpleUploader_upload_substitute_path');        // get the path we'll substitute for the for the upload path (i.e. fully qualified)

    if (strUploaderURL.indexOf("?") < 0){                                                                            // if we were called without any GET params
        strUploaderURL = strUploaderURL + "?type=" + type + "&d=" + strUploadPath + "&subs=" + strSubstitutePath;   // add our own params 
    } else {
        strUploaderURL = strUploaderURL + "&type=" + type + "&d=" + strUploadPath + "&subs=" + strSubstitutePath;
    }
    tinyMCE.activeEditor.windowManager.open({                                                                       // open the plugin popup
        file            : strUploaderURL,
        title           : 'Upload from file',
        width           : 500,  
        height          : 100,
        resizable       : "yes", 
        inline          : 1,        // This parameter only has an effect if you use the inlinepopups plugin!
        close_previous  : "no"
    }, {
        window : win,
        input : field_name
    });
  
    return false;
}

/**
 * This function will get called when the uploader is done uploading the file and ready to update
 * calling dialog and close the upload popup
 * strReturnURL should be the string with the path to the uploaded file
 */

function closePluginPopup(){    
    tinyMCEPopup.close();	       // close popup window
}

/**
 * This function update the content editor with the content template file
 */

function updateEditorContent(serializedHTML){
    tinyMCE.activeEditor.execCommand('mceSetContent', false, serializedHTML);
}