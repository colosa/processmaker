/**
* Name: editor_plugin_src.js (for pmVariablePicker tinyMCE plugin)
**/

(function(){
    // set the base url setting
    tinyMCE.baseURL = "/js/tinymce/jscripts/tiny_mce";
    var strPluginURL;
    // the plugin init settings
    tinymce.create('tinymce.plugins.pmVariablePickerPlugin', {
    	init: function(ed, url) 
        {   
            strPluginURL = url;                                         // store the URL for future use..
            ed.addCommand('mcepmVariablePicker', function() {
                pmVariablePicker();              
            });
            ed.addButton('pmVariablePicker', {
                title: 'pmVariablePicker',
                label : '  @#',
                cmd: 'mcepmVariablePicker',
                image: url + '/img/picker.png'
            }); 
        },
        createControl: function(n, cm) {
            return null;
        },
        getPluginURL: function() {
            return strPluginURL;
        }
    });
    tinymce.PluginManager.add('pmVariablePicker', tinymce.plugins.pmVariablePickerPlugin);
})();

// this function can get called from the plugin inint (above) or from the callback on advlink/advimg plugins..
// in the latter case, win and type will be set.. 
/**
 * @function pmVariablePicker
 * @description Opens the plugin popup, loading the form inside it.
 * @param field_name deprecated
 * @param type deprecated
 * @param win deprecated
 * 
 */
function pmVariablePicker(field_name, type, win) {    
    
    var uloc=String(location);
    var new_text = uloc.split('/');
    var loc='/'+new_text[3]+'/'+new_text[4]+'/'+new_text[5]+'/controls/varsAjax?displayOption=tinyMCE&sSymbol=@@&&sProcess='+tinyMCE.activeEditor.processID;
    var strPluginPath  = tinyMCE.activeEditor.plugins.pmVariablePicker.getPluginURL();                               // get the path to the uploader plugin    
    var strUploaderURL = strPluginPath + "/uploader.php";                                                           // generate the path to the uploader script    
    var strUploadPath  = tinyMCE.activeEditor.getParam('plugin_pmVariablePicker_upload_path');                       // get the relative upload path
    var strSubstitutePath = tinyMCE.activeEditor.getParam('plugin_pmVariablePicker_upload_substitute_path');        // get the path we'll substitute for the for the upload path (i.e. fully qualified)
    
    if (strUploaderURL.indexOf("?") < 0){                                                                            // if we were called without any GET params
        strUploaderURL = strUploaderURL + "?type=" + type + "&d=" + strUploadPath + "&subs=" + strSubstitutePath;   // add our own params 
    } else {
        strUploaderURL = strUploaderURL + "&type=" + type + "&d=" + strUploadPath + "&subs=" + strSubstitutePath;
    }
    //tinyMCE.activeEditor.anyVariable='path/to/ProcessMaker' 
    tinyMCE.activeEditor.windowManager.open({                                                                       // open the plugin popup
        file            : loc,
        title           : 'Upload Variable',
        width           : '600px',
        height          : '330px',
        resizable       : "yes",
        scrollbars      : "no",
        overflow        : false,
        inline          : 1,        // This parameter only has an effect if you use the inlinepopups plugin!
        close_previous  : "no"
    }, {
        window : win,
        input : field_name
    });

    return false;
}

/**
 * @function closePluginPopup
 * @description closes the tinyMCE popup window
 */
function closePluginPopup(){
    tinyMCEPopup.close();	                                                                    // close popup window
}

/**
 * @function updateEditorContent
 * @description insert the editor content with a html code string
 * @params serializedHTML String html code
 */
function updateEditorContent(serializedHTML){
    tinyMCE.activeEditor.execCommand('mceInsertContent', false, serializedHTML);
}

/**
 * @function insertFormVar
 * @description alternate version of updateEditorContent this function is 
 *              compatible with the variable picker calls, also closes the popup
 *              window
 * @param fieldName String deprecated
 * @param serializedHTML String the html code to be added. 
 */
function insertFormVar(fieldName,serializedHTML){
        tinyMCEPopup.execCommand('mceInsertContent', false, serializedHTML);
    closePluginPopup();
}
