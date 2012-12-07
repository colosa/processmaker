/**
* Name: editor_plugin_src.js (for pmGrids tinyMCE plugin)
**/

(function(){
    var strPluginURL;
    tinymce.create('tinymce.plugins.pmGridsPlugin', {
    	init: function(ed, url) 
        {   
            strPluginURL = url;                                         // store the URL for future use..
            ed.addCommand('mcepmGrids', function() {
                pmGrids();              
            });
            ed.addButton('pmGrids', {
                title: 'pmGrids',
                label : '  @#',
                cmd: 'mcepmGrids',
                image: url + '/img/grids.png'
            }); 
        },
        createControl: function(n, cm) {
            return null;
        },
        getPluginURL: function() {
            return strPluginURL;
        }
    });
    tinymce.PluginManager.add('pmGrids', tinymce.plugins.pmGridsPlugin);
})();

/**
 * @function pmGrids
 * @description The function intializes the plugin and also creates the popup
 *              window
 * @param field_name deprecated
 * @param win deprecated
 */
function pmGrids(field_name, win) {    
    //tinyMCE.activeEditor.anyVariable='path/to/ProcessMaker' 
    var strPluginPath  = tinyMCE.activeEditor.plugins.pmGrids.getPluginURL(); // get the path to the uploader plugin    
    var strScriptURL   = strPluginPath + "/pmGrids.html"; // loading the form
    
    tinyMCE.activeEditor.windowManager.open({                                                                       // open the plugin popup
        file            : strScriptURL,
        title           : 'ProcessMaker Grid Wizard',
        width           : '600px',
        height          : '230px',
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
 * @description closes the plugin popup
 */
function closePluginPopup(){    
    tinyMCEPopup.close();  // close popup window
}


/**
 * @function updateEditorContent
 * @description insert the editor content with a html code string
 * @params serializedHTML String html code
 */
function updateEditorContent(serializedHTML){
    tinyMCE.activeEditor.execCommand('mceInsertRawHTML', false, serializedHTML);
    closePluginPopup();
}

