<?php

echo '    
<script>
    document.body.onload = function(){
        
//        delete Array.prototype.isArray;
//        delete Array.prototype.isObject;
//        delete Array.prototype.onlyInt;
//        delete Object.prototype.propertyIsEnumerable;
//        delete Array.prototype.toStr;

//        delete Object.prototype.toStr;
//        delete Object.prototype.concat;
//        delete Object.prototype.get_by_key;
//        delete Object.prototype.expand;
//        delete Object.prototype.setParent;
//        delete Object.prototype.isset_key;

//        delete Function.prototype.extend;        
        
        tinyMCE.baseURL = "/js/tinymce/jscripts/tiny_mce";
        tinyMCE.init({                
            theme   : "advanced",                
            plugins : "fullpage",
            mode    : "specific_textareas",
            editor_selector : "tmceEditor",
            width   : "640",
            height  : "200",
            theme_advanced_buttons3_add : "fullpage"
        });
        
//        alert($("#fcontent").val());
/*
        tinyMCE.init({                
            theme   : "advanced",                
            mode    : "specific_textareas",
            editor_selector : "tmceEditor",
            
                plugins : "autolink,lists,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,wordcount,advlist,autosave,visualblocks",

		// Theme options
		theme_advanced_buttons1 : "save,newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,styleselect,formatselect,fontselect,fontsizeselect",
		theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor",
		theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|,fullscreen",
		theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,pagebreak,restoredraft,visualblocks",
		theme_advanced_toolbar_location : "top",
		theme_advanced_toolbar_align : "left",
		theme_advanced_statusbar_location : "bottom",
		theme_advanced_resizing : true,

		// Example content CSS (should be your site CSS)
		content_css : "css/content.css",

		// Drop lists for link/image/media/template dialogs
		template_external_list_url : "lists/template_list.js",
		external_link_list_url : "lists/link_list.js",
		external_image_list_url : "lists/image_list.js",
		media_external_list_url : "lists/media_list.js",

		// Style formats
		style_formats : [
			{title : \'Bold text\', inline : \'b\'},
			{title : \'Red text\', inline : \'span\', styles : {color : \'#ff0000\'}},
			{title : \'Red header\', block : \'h1\', styles : {color : \'#ff0000\'}},
			{title : \'Example 1\', inline : \'span\', classes : \'example1\'},
			{title : \'Example 2\', inline : \'span\', classes : \'example2\'},
			{title : \'Table styles\'},
			{title : \'Table row 1\', selector : \'tr\', classes : \'tablerow1\'}
		],

		// Replace values for the template plugin
		template_replace_values : {
			username : "Some User",
			staffid : "991234"
		}
        });
   */     
        //alert(\'loaded\');
    }
    
    
</script>


';
?>

<textarea name="form[fcontent]" id="form[fcontent]" class="tmceEditor"><p>news<table><tr><td>cell 1</td></tr><tr><td>cell 2</td></tr></table></p></textarea>
<!--<textarea name="new" id="new" class="tmceEditor">another one</textarea>-->

<?php


