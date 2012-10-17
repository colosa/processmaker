<?php
/**
 * weekend.php
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
G::LoadInclude( 'ajax' );

$G_ENABLE_BLANK_SKIN = true;

$ARR_WEEKDAYS[0] = array ('SUNDAY','MONDAY','TUESDAY','WEDNESDAY','THURSDAY','FRIDAY','SATURDAY'
);
$ARR_WEEKDAYS['es'] = array ("Domingo?","Lunes?","Martes?","Miércoles?","Jueves?","Viernes?","Sábado?"
);
$ARR_WEEKDAYS['en'] = array ("Sunday?","Monday?","Tuesday?","Wednesday?","Thursday?","Friday?","Saturday?"
);
$ARR_WEEKDAYS['fa'] = array ('یکشنبه','دوشنبه','سه شنبه','چهارشنبه','پنجشنبه ','جمعه','آدینه'
);

$dbc = new DBConnection();
$ses = new DBSession( $dbc );

$holidays = $ses->execute( "SELECT LEX_VALUE FROM LEXICO WHERE LEX_TOPIC ='NOWORKINGDAY' " );

$config = array ();
for ($id = 0; $id < 7; $id ++) {
    $res = $ses->execute( " SELECT * FROM LEXICO WHERE LEX_KEY = '" . $ARR_WEEKDAYS[0][$id] . "' AND LEX_TOPIC ='HOLIDAY' " );
    $res = $res->read();
    $config[$ARR_WEEKDAYS[0][$id]] = $res['LEX_VALUE'];
}
$G_PUBLISH = new Publisher();
$G_PUBLISH->AddContent( 'xmlform', 'xmlform', 'setup/weekend', '', $config, '' );
G::RenderPage( 'publish' );
?>
<script language="JavaScript">
function var_dump(obj)
{
	msg='';
	if (typeof(obj)=='object')
	for(a in obj)
	{
		msg+=a;//+':'+obj[a];
		msg+="\t";
	}
	else
		msg=obj;
	alert(msg);
}
function on_submit(myForm)
{
	days='';values='';
	for(cbi in myForm.elements)
	if (cbi.substr(0,4)=='form')
	{
		cb=myForm.elements[cbi];
		days+=','+cb.id;
		values+=','+cb.checked;
	}
	ajax_function('<?php echo G::encryptLink('weekendAjax.php');?>','setDays','days='+days+'&values='+values);
	document.location.reload(true);
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