<?php
/** 
 * ProcessMaker Open Source Edition
 * Copyright (C) 2004 - 2008 Colosa Inc.23
 * 
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 * 
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 * 
 * For more information, contact Colosa Inc, 2566 Le Jeune Rd., 
 * Coral Gables, FL, 33134, USA, or email info@colosa.com.
 * 
 */

 /*
  * @Author Erik Amaru Ortiz <erik@colosa.com>
  * @Date Aug 26th, 2009 
  */ 
  if(!((isset( $_SESSION['USER_LOGGED'] ))&&(!(isset($_GET['sid']))))||!isset($_SESSION['Current_Dynafom'])) {
    $oHeadPublisher =& headPublisher::getSingleton();
    $oHeadPublisher->addScriptCode("
    window.parent.location.href = '../processes/mainInit';
    ");    
    G::RenderPage('publish');
    exit();
  }
 
?>
<html>
	<head>
		<link type="text/css" href="/js/jquery/css/redmond/jquery-ui-1.7.2.custom.css" rel="stylesheet" />
		<link type="text/css" href="/skin/<?php echo SYS_SKIN;?>/style.css" rel="stylesheet" />
		<style> body{ background-color: #fff; }</style>
		<script type="text/javascript" src="/js/jquery/jquery-1.3.2.min.js"></script>
		<script type="text/javascript" src="/js/jquery/jquery-ui-1.7.2.custom.min.js"></script>
		<script type="text/javascript" src="/jscore/dynaforms/dynaforms_fieldsHandler.js"></script>
	</head>
<?php 
	$content = file_get_contents(PATH_DYNAFORM.$_SESSION['Current_Dynafom']['Parameters']['FILE'].".xml");
	$oXxml = G::xmlParser($content);
	
	if( !isset($oXxml->result['dynaForm']['__CONTENT__']) ){
?>
	<br/>
	<div class="ui-widget-header ui-corner-all" style="height:17px" align="center">
		<?php echo G::loadTranslation('ID_NO_FIELD_FOUND')?>
	</div>
	<script>
        parent.document.getElementById('light').style.display='none';
        parent.document.getElementById('fade').style.display='none';
	</script>
<?php
	  die();
	} 
	
	$elements = $oXxml->result['dynaForm']['__CONTENT__'];
	$dynaformAttributes = $oXxml->result['dynaForm']['__ATTRIBUTES__'];
     
    $dynaformType = $dynaformAttributes['type'];
	
	foreach($elements as $node_name=>$node){
		if( $node_name == "___pm_boot_strap___"){
      $boot_strap = $elements[$node_name];
			$hidden_fields = G::decrypt($boot_strap['__ATTRIBUTES__']['meta'], 'dynafieldsHandler');
			//echo $hidden_fields;
			$hidden_fields_list = explode(',', $hidden_fields);
			unset($elements[$node_name]);

      ?>
      <script>
        parent.jsMeta = "<?php echo $boot_strap['__ATTRIBUTES__']['meta'] ?>";
      </script>
      <?php
		}
	} 
?>
	<body>
	<table border="0" width="100%" cellpadding="0" cellspacing="0" class="fieldshandler_item">	
	<tr>
	<td width="15%"  valign="top" align="left">
	     <a href='#' onmouseout="parent.hideTooltip()" onmouseover="parent.showTooltip(event,document.getElementById('help').innerHTML);return false;">
	 	    <image src="/images/help4.gif" width="16" height="16" border="0"/>
	 	 </a>
	</td>
	<td valign="top" width="990px"><center>
	
		<div style="width:100%">
		<div class="ui-widget-header ui-corner-all" style="height:17px">
			<table border="0" width="100%" cellpadding="0" cellspacing="0">
			  <td width="7%"><div style="font-size:9px; color:#fff;" align="left">&nbsp;<b><?php echo G::loadTranslation('ID_VISIBLE')?></b></div></td>
			  <td width="15%"><div style="font-size:12px;color:#fff; font-weight:bold" align="left">&nbsp;<?php echo G::loadTranslation('ID_TYPE')?></div></td>
			  <td width="28%"><div style="font-size:12px;color:#fff; font-weight:bold" align="left">&nbsp;<?php echo G::loadTranslation('ID_NAME')?></div></td>
			  <td width="6"><div style="font-size:12px;color:#fff; font-weight:bold" align="left">&nbsp;<?php echo G::loadTranslation('ID_LABEL')?></div></td>
			</table>
		</div>
		</div>
		
		<div id="dynafields"> 
		  <ul id="sortable" style="margin:0; padding:0;">
			<?php foreach($elements as $node_name=>$node){
				    if( isset($hidden_fields_list) ){
					   $checked = !(in_array($node_name, $hidden_fields_list))? 'checked="checked"': '';
					} else {
						$checked = 'checked="checked"';
					}
				?>
			<li style="list-style:none;" id="<?php echo $node_name?>" class="ui-state-default" onmouseover="setClass(this, 'ui-state-hover')" onmouseout="setClass(this, 'ui-state-default')">
				<table class="dynalist" border="0" width="100%" cellpadding="0" cellspacing="0" id="fieldshandler_items_table">
				<tr>
					<td width="7%">
						<?php if($node['__ATTRIBUTES__']['type'] != 'javascript' && $dynaformType != 'grid') {?>
						<input id="chk@<?php echo $node_name?>" type="checkbox" onclick="parent.jsMeta = fieldsHandlerSaveHidden();" <?php echo $checked?> />
						<?php } else {?>
						&nbsp;
						<?php }?>
					</td>
					<td width="15%" >
					<?php $type = $node['__ATTRIBUTES__']['type'];
					switch($type){
						case 'yesno':       	$type = 'yes_no';       		break;
						case 'listbox':    		$type = 'list_box';     		break;
						case 'checkgroup': $type = 'check_group';  	break;
						case 'radiogroup': $type = 'radio_group';  	break;
						case 'file':        		$type = 'upload_files'; 	break;
					}?>
          <?php if ( is_file(PATH_HTML.'images'.PATH_SEP.'dynamicForm'.PATH_SEP."$type.gif") ){?>
					<img src="/images/dynamicForm/<?php echo $type?>.gif"/>
          <?php } else {?>
          <img src="/images/unknown_icon.gif" border="0" width="20" height="16"/>
          <?php }?>
          <span style="font-size:10px;">&nbsp;<?php echo "({$node['__ATTRIBUTES__']['type']})";?></span>
					</td>
					
          <td width="28%"  style="font-size:12px;"> 
              &nbsp;<?php echo "$node_name";?>
          </td>      
					<td><p style="font-size:12px; color:#1C3166; font-weight:bold">
					<?php if( isset($node['__CONTENT__'][SYS_LANG]['__VALUE__']) ){
						  if( strlen($node['__CONTENT__'][SYS_LANG]['__VALUE__']) > 30 ){
						  	$label = substr(trim(strip_tags(G::stripCDATA($node['__CONTENT__'][SYS_LANG]['__VALUE__']))), 0, 30 ) . '...';
						  } else {
						  	$label = $node['__CONTENT__'][SYS_LANG]['__VALUE__'];
						  }
					      print($label);
					    } else {
					      print("&nbsp;");	
					    }
  					?></p>
					</td>
					<td width="40px" class="options" align="right">
            <?php if( in_array($node['__ATTRIBUTES__']['type'], $_POST['fieldsList']) ){ ?>
						<!-- <div class="tool"><img src="/images/options.png" width="12" height="12" border="0"/> </div>-->
						<div class="jq-checkpointSubhead" style="display:block">
							<a title="<?php echo G::loadTranslation('ID_EDIT_FIELD')?>" href="#" onclick="__ActionEdit('<?php echo $node_name?>'); return false;"><img src="/images/e_Edit.png" width="15" height="15" border="0" onmouseout="backImage(this,'')" onmouseover="backImage(this,'url(/images/dynamicForm/hover.gif) no-repeat')"/></a>
							<a title="<?php echo G::loadTranslation('ID_REMOVE_FIELD')?>" href="#" onclick="__ActionDelete('<?php echo $node_name?>', '<?php echo $node['__ATTRIBUTES__']['type'];?>');return false;"><img src="/images/e_Delete.png" width="15" height="15" border="0" onmouseout="backImage(this,'')" onmouseover="backImage(this,'url(/images/dynamicForm/hover.gif) no-repeat')"/></a>
						</div>
            <?php } else {?>
                <div class="tool"><img src="/images/options.png" width="12" height="12" border="0"/> </div>
                <div class="jq-checkpointSubhead" style="display:none">
                    <a title="<?php echo G::loadTranslation('ID_REMOVE_FIELD')?>" href="#" onclick="__ActionDelete('<?php echo $node_name?>', '<?php echo $node['__ATTRIBUTES__']['type'];?>');return false;"><img src="/images/e_Delete.png" width="15" height="15" border="0" onmouseout="backImage(this,'')" onmouseover="backImage(this,'url(/images/dynamicForm/hover.gif) no-repeat')"/></a>
                </div>
            <?php }?>
					</td>
				</tr>
				</table>	
			</li>
			<?php }?>
		  </ul>
		</div>
	 </center>
	 <br/><br/>
	 </td>
	 <td valign="top" align="right" width="300" style="text-align:right">
		 <div id="help" style="display:none">
			<h3 class="ui-widget-header ui-corner-all">Processmaker - DynaFields Handler</h3>
			<b><?php echo G::LoadTranslation('ID_FIELD_HANDLER_HELP1');?></b><br/><br/>
			<li> <?php echo G::LoadTranslation('ID_FIELD_HANDLER_HELP2');?><br/>
			<li> <?php echo G::LoadTranslation('ID_FIELD_HANDLER_HELP3');?> 
		</div>
	 </td>
	 </tr>
	 </table>
	</body>
	<script language="javascript">
	function __ActionEdit(uid){
		var client_window = parent.getClientWindowSize(); 
		h = client_window.height;
		h1 = (h / 100) * 92;
		window.parent.popupWindow('', "fields_Edit?A=<?php echo $_SESSION['Current_Dynafom']['Parameters']['URL']?>&XMLNODE_NAME="+ uid , 600, h1);
		
	}
	
	function __ActionDelete(uid, ftype){
		new window.parent.leimnud.module.app.confirm().make({
      label: '<?php echo G::LoadTranslation('ID_FIELD_HANDLER_ACTION_DELETE');?>' + ' ' + ftype + "?",
			action:function(){
				$.ajax({
				   type: "POST",
				   url: "fields_Delete",
				   data: 'A=<?php echo $_SESSION['Current_Dynafom']['Parameters']['URL']?>&XMLNODE_NAME='+uid,
				   success: function(httpResponse){
						window.parent.dynaformEditor.refreshFieldsList();
				   }
				});
			}
		});
	}
	
    window.onload = function() {
        parent_divs = parent.document.getElementsByTagName('div');
        for(i=0; i<parent_divs.length; i++){
            if(parent_divs[i].className == 'panel_containerWindow___processmaker'){
                content_div = parent_divs[i];
            }
        }
        h = content_div.style.height.split('px');
        window.parent.document.getElementById('dynaframe').height = (h[0]-120);

        parent.document.getElementById('light').style.display='none';
        parent.document.getElementById('fade').style.display='none';
    }
	</script>
</html>

