<?php
/**
 * class.wysiwygEditor.php
 *
 * @package gulliver.system
 *
 * ProcessMaker Open Source Edition
 * Copyright (C) 2004 - 2012 Colosa Inc.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 * For more information, contact Colosa Inc, 2566 Le Jeune Rd.,
 * Coral Gables, FL, 33134, USA, or email info@colosa.com.
 *
 */
//XmlForm_Field_DVEditor
/**
 * XmlForm_Field_HTML class definition
 * It is useful to see dynaforms how are built
 *
 * @package gulliver.system
 * @author
 *
 * @copyright (C) 2012 by Colosa Development Team.
 *
 */
class XmlForm_Field_WYSIWYG_EDITOR extends XmlForm_Field
{
    //public $toolbarSet = '';
    public $width = '100%';
    public $height = '300';
    public $defaultValue = '<br/>';
    public $editorType = '';
    public $processID = '';
    /**
     * render function returns the HTML definition for the Dynaform Field
     *
     * @author
     *
     *
     * @access public
     * @param string $value
     * @param string $owner
     * @return string
     *
     */
    public function render ($value, $owner = null)
    {
        $value = ($value == '') ? '<br/>' : $value;
        $html  = "<textArea class='tmceEditor' id='form[" . $this->name . "]' name='form[" . $this->name . "]' >" . htmlentities( $value, ENT_QUOTES, 'UTF-8' ) . "</textarea>";
        return $html;
    }

    /**
     * attachEvents method returns some javascript code in order to initialize
     * the Dynaform Field configuration, attributes, and additional stuff.
     *
     * @author
     *
     *
     * @access public
     * @param string $element
     * @return string
     *
     */
    public function attachEvents ($element)
    {
        
        $editorDefinition = 'tinyMCE.baseURL = "/js/tinymce/jscripts/tiny_mce"; ';
        
        switch ($this->editorType){
            case 'EMAIL_TEMPLATE':
                $editorDefinition .= '
                // is necessary the process uid variable in order to load the picker correctly
                var formProcessID = document.getElementById("form[pro_uid]").value;               
                tinyMCE.init({
                    theme   : "advanced",
                    plugins : "advhr,advimage,advlink,advlist,autolink,autoresize,autosave,contextmenu,directionality,emotions,example,example_dependency,fullpage,fullscreen,iespell,inlinepopups,insertdatetime,layer,legacyoutput,lists,media,nonbreaking,noneditable,pagebreak,paste,preview,print,save,searchreplace,spellchecker,style,tabfocus,table,template,visualblocks,visualchars,wordcount,xhtmlxtras,pmSimpleUploader,pmVariablePicker",
                    mode    : "specific_textareas",
                    editor_selector : "tmceEditor",
                    width   : 760,
                    height  : "'.$this->height.'",
                    //theme_advanced_buttons1 : "pmSimpleUploader,|,pmVariablePicker,|,bold,italic,underline,|,justifyleft,justifycenter,justifyright,justifyfull",
                    //theme_advanced_buttons2 : "bold,italic,underline,|,justifyleft,justifycenter,justifyright,justifyfull",
                    
                    theme_advanced_buttons1 : "pmSimpleUploader,|,pmVariablePicker,|,bold,italic,underline,|,justifyleft,justifycenter,justifyright,justifyfull,|,fontselect,fontsizeselect,|,cut,copy,paste,|,bullist,numlist,|,outdent,indent,blockquote",
                    theme_advanced_buttons2 : "tablecontrols,|,undo,redo,|,link,unlink,image,|,forecolor,backcolor,|,hr,removeformat,visualaid,|,sub,sup,|,ltr,rtl,|,code",
                    oninit: function (){
                        tinyMCE.activeEditor.processID =formProcessID;
                    },
                    onchange_callback: function(inst) {
                		if(inst.isDirty()) {
                			inst.save();
                		}
                		return true;
                	},
                    handle_event_callback : function(e) {
                		if(this.isDirty()) {
                			this.save();
                		}
                		return true;
                	}
                });
                ';
                break;
            case 'OUTPUT_DOCUMENT':
                $editorDefinition .= '                    
                // is necessary the process uid variable in order to load the picker correctly
                var formProcessID = document.getElementById("form[PRO_UID]").value;
                tinyMCE.init({
                    theme   : "advanced",
                    plugins : "advhr,advimage,advlink,advlist,autolink,autoresize,autosave,contextmenu,directionality,emotions,example,example_dependency,fullpage,fullscreen,iespell,inlinepopups,insertdatetime,layer,legacyoutput,lists,media,nonbreaking,noneditable,pagebreak,paste,preview,print,save,searchreplace,spellchecker,style,tabfocus,table,template,visualblocks,visualchars,wordcount,xhtmlxtras,pmSimpleUploader,pmVariablePicker",
                    mode    : "specific_textareas",
                    editor_selector : "tmceEditor",
                    width   : "770",
                    height  : "305",
                    //theme_advanced_buttons1 : "pmSimpleUploader,|,pmVariablePicker",
                    //theme_advanced_buttons2 : "fontselect,bold,italic,underline,forecolor,backcolor,|,justifyleft,justifycenter,justifyright,justifyfull,|,link,numlist,bullist,|,code",
                    
                    theme_advanced_buttons1 : "pmSimpleUploader,|,pmVariablePicker,|,bold,italic,underline,|,justifyleft,justifycenter,justifyright,justifyfull,|,fontselect,fontsizeselect,|,cut,copy,paste,|,bullist,numlist,|,outdent,indent,blockquote",
                    theme_advanced_buttons2 : "tablecontrols,|,undo,redo,|,link,unlink,image,|,forecolor,backcolor,|,hr,removeformat,visualaid,|,sub,sup,|,ltr,rtl,|,code",
                    oninit: function (){
                        tinyMCE.activeEditor.processID=formProcessID;                        
                    },
                    onchange_callback: function(inst) {
                        if(inst.isDirty()) {
                            inst.save();
                        }
                        return true;
                    }
                });
                ';
                break;

            case 'DYNAFORM_TEMPLATE':
                $editorDefinition .= '
                tinyMCE.init({
                    theme   : "advanced",
                    plugins : "advhr,advimage,advlink,advlist,autolink,autoresize,autosave,contextmenu,directionality,emotions,example,example_dependency,fullpage,fullscreen,iespell,inlinepopups,insertdatetime,layer,legacyoutput,lists,media,nonbreaking,noneditable,pagebreak,paste,preview,print,save,searchreplace,spellchecker,style,tabfocus,table,template,visualblocks,visualchars,wordcount,xhtmlxtras",
                    mode    : "specific_textareas",
                    //apply_source_formatting : true,
                    //remove_linebreaks: false,
                    editor_selector : "tmceEditor",
                    width   : "700",
                    height  : "300",
                    theme_advanced_buttons1 : "pmSimpleUploader,|,pmVariablePicker,|,bold,italic,underline,|,justifyleft,justifycenter,justifyright,justifyfull,|,fontselect,fontsizeselect,|,cut,copy,paste,|,bullist,numlist",
                    theme_advanced_buttons2 : "outdent,indent,blockquote,|,undo,redo,|,link,unlink,image,|,forecolor,backcolor,|,hr,removeformat,visualaid,|,sub,sup,|,ltr,rtl,|,code",
                    
                    skin : "o2k7",
                    skin_variant : "silver",

                    template_external_list_url : "js/template_list.js",
                    external_link_list_url : "js/link_list.js",
                    external_image_list_url : "js/image_list.js",
                    media_external_list_url : "js/media_list.js",

                    template_replace_values : {
                        username : "Some User",
                        staffid : "991234"
                    },
                    handle_event_callback : function(e) {
                        if(this.isDirty()) {
                            this.save();
                        }
                        return true;
                    }
                });
                
                ';
                break;
            default:
                $editorDefinition .= '
                    tinyMCE.init({
                        theme   : "advanced",
                        plugins : "fullpage",
                        mode    : "specific_textareas",
                        editor_selector : "tmceEditor",
                        width   : "'. $this->width. '",
                        height  : "'. $this->height. '",
                        theme_advanced_buttons3_add : "fullpage",                        
                        handle_event_callback : function(e) {
                                if(this.isDirty()) {
                                        this.save();
                                }
                                return true;
                        }
                    });
                ';
                break;
        }
        return $editorDefinition;
    }
}
