<?php
/**
 * location.php
 *
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
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 * For more information, contact Colosa Inc, 2566 Le Jeune Rd.,
 * Coral Gables, FL, 33134, USA, or email info@colosa.com.
 */
if (($RBAC_Response = $RBAC->userCanAccess( "PM_SETUP" )) != 1)
    return $RBAC_Response;

$G_ENABLE_BLANK_SKIN = true;

$uid = $_SESSION['USER_LOGGED'];

$dbc = new DBConnection();
$ses = new DBSession( $dbc );

G::LoadClass( 'templateTable' );

$query = $ses->execute( 'select USR_COUNTRY ,USR_CITY ,USR_LOCATION from USER where UID = "' . $uid . '"' );
$param = $query->read();

$city = $param['USR_CITY'];
$query = $ses->execute( 'SELECT UID,CAPTION FROM TERRITORY WHERE TERRITORY="LOCATION" AND RELATION="' . $city . '"' );
/*
$table=new templateTable('list_template.html','DIV_LOCATIONS');
	$table->formatTitleCol(0,'width="80%"');
	$table->formatTitleCol(1,'align="center"');
	$table->formatCol(0,'width="80%" ');
	$table->formatCol(1,'align="center"');

$table->addTitle(array(G::LoadTranslation('ID_LOCATION'),''));
for($r=0;$r<$query->count();$r++)
{
	$row=$query->read();
	$vrow=array($row['CAPTION']);
	$vrow[]='<a href="#" onclick="deleteLocation(\''.$row['UID'].'\')">'.G::LoadTranslation('ID_DELETE').'</a>';
	$table->addRow($r,$vrow);
}
$table->addRowTag($r,'lastRow','','');
	$table->addRow($r,
		array(
			$table->getBlock('text',array('value' => '', 'properties' => 'id="newLocation"')),
			'<a href="#" onclick="addLocation();">'.G::LoadTranslation('ID_ADD').'</a>'
			));
*/

$G_PUBLISH = new Publisher();
$G_PUBLISH->AddContent( 'xmlform', 'xmlform', 'setup/location', '', $param );
//$G_PUBLISH->AddContent('template', '', '', '', $table->tpl);


G::RenderPage( 'publish' );

?>

<script language="javascript">
rowNumber=<?php echo $r?>;
attachFunctionEventOnChange(document.getElementById('form[USR_CITY]'),changeRegion);
attachFunctionEventOnChange(document.getElementById('form[USR_COUNTRY]'),changeRegion);
function changeCity()
{
	var country=document.getElementById('form[USR_COUNTRY]');
	var city=document.getElementById('form[USR_CITY]');
	ajax_function('<?php echo G::encryptLink('cityAjax.php')?>','changeCity','row='+rowNumber+'&country='+encodeURIComponent(country.value)+'&city='+encodeURIComponent(city.value));
}
function addLocation()
{
	var lr = document.getElementById('lastRow');
	var city=document.getElementById('form[USR_CITY]');
	if (newLocation.value=='') return;
	lr.outerHTML=ajax_function('<?php echo G::encryptLink('cityAjax.php')?>','newLocation','row='+rowNumber+'&location='+encodeURIComponent(newLocation.value)+'&city='+encodeURIComponent(city.value));
	rowNumber++;
	newLocation.value='';
}
function deleteLocation(locat)
{
	var lr = document.getElementById('DIV_LOCATIONS');
	var city=document.getElementById('form[USR_CITY]');
	lr.innerHTML=ajax_function('<?php echo G::encryptLink('cityAjax.php')?>','delLocation','row='+rowNumber+'&uid='+encodeURIComponent(locat)+'&city='+encodeURIComponent(city.value));
	rowNumber--;
}
function changeRegion()
{
	if (document.getElementById('form[USR_CITY]').options.length==0)
	{
		changeCities();
	}
	else
	{
		var city=document.getElementById('form[USR_CITY]');
		var lr = document.getElementById('DIV_LOCATIONS');
		lr.innerHTML=ajax_function('<?php echo G::encryptLink('cityAjax.php')?>','changeRegion','city='+encodeURIComponent(city.value));
		rowNumber=ajax_function('<?php echo G::encryptLink('cityAjax.php')?>','getRowRegion','city='+encodeURIComponent(city.value));
	}
}
function changeCities()
{
	var country=document.getElementById('form[USR_COUNTRY]');
	var lr = document.getElementById('DIV_LOCATIONS');
	lr.innerHTML=ajax_function('<?php echo G::encryptLink('cityAjax.php')?>','changecities','country='+encodeURIComponent(country.value));
	rowNumber=ajax_function('<?php echo G::encryptLink('cityAjax.php')?>','getRowCities','country='+encodeURIComponent(country.value));
}
function addCity()
{
	var lr = document.getElementById('lastRow');
	var country=document.getElementById('form[USR_COUNTRY]');
	if (newCity.value=='') return;
	lr.outerHTML=ajax_function('<?php echo G::encryptLink('cityAjax.php')?>','addCity','row='+rowNumber+'&city='+encodeURIComponent(newCity.value)+'&country='+encodeURIComponent(country.value));
	rowNumber++;
	newCity.value='';
	//Refresh the city's dropdown
	attachFunctionEventOnChange(document.getElementById('form[USR_CITY]'),null);
	attachFunctionEventOnChange(document.getElementById('form[USR_COUNTRY]'),null);
	document.getElementById('form[USR_COUNTRY]').onchange();
	attachFunctionEventOnChange(document.getElementById('form[USR_CITY]'),changeRegion);
	attachFunctionEventOnChange(document.getElementById('form[USR_COUNTRY]'),changeRegion);
}
function deleteCity(locat)
{
	var lr = document.getElementById('DIV_LOCATIONS');
	var country=document.getElementById('form[USR_COUNTRY]');
	lr.innerHTML=ajax_function('<?php echo G::encryptLink('cityAjax.php')?>','delCity','row='+rowNumber+'&uid='+encodeURIComponent(locat)+'&country='+encodeURIComponent(country.value));
	rowNumber--;
	//Refresh the city's dropdown
	attachFunctionEventOnChange(document.getElementById('form[USR_CITY]'),null);
	attachFunctionEventOnChange(document.getElementById('form[USR_COUNTRY]'),null);
	document.getElementById('form[USR_COUNTRY]').onchange();
	attachFunctionEventOnChange(document.getElementById('form[USR_CITY]'),changeRegion);
	attachFunctionEventOnChange(document.getElementById('form[USR_COUNTRY]'),changeRegion);
}
function ajax_function(ajax_server, funcion, parameters)
{
    objetus = get_xmlhttp();
    var response;
    try
    {
    	if (parameters) parameters = '&' + encodeURI(parameters);
    	objetus.open("GET", ajax_server + "?function=" + funcion + parameters, false);
  	}catch(ss)
  	{
  		alert("error"+ss.message);
  	}
    objetus.send(null);
    return objetus.responseText;
}
</script>

