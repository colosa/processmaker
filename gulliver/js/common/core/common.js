/* PACKAGE : COMMON
 */
  function get_xmlhttp() {
    try {
      xmlhttp = new ActiveXObject("Msxml2.XMLHTTP1");
    } catch (e) {
      try {
        xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
      } catch (E) {
        xmlhttp = false;
      }
    }
    if (!xmlhttp && typeof XMLHttpRequest!='undefined') {
        xmlhttp = new XMLHttpRequest();
    }
    return xmlhttp;
  }
  /* ajax_function
   * Envia una solicitud GET a ajax_server con la variables "function" y las definidas en parameters.
   * @author       Julio Cesar Laura Avendano <juliocesar@colosa.com, julces2000@gmail.com>
   * @version 1.0
   * @package ajax
   * @param string ajax_server  url de la pagina servidor
   * @param string function     funci�n solicitada en el lado del servidor
   * @param string parameters   variables pasadas por url. Ej. variable=valor&otravariable=suvalor
   */
  function ajax_function(ajax_server, funcion, parameters, method)
  {
      var objetus;
      objetus = get_xmlhttp();
      var response;
      try
      {
      	if (parameters) parameters = '&' + encodeURI(parameters);
      	if (!method ) method ="POST";
      	data = "function=" + funcion + parameters;
      	questionMark = (ajax_server.split('?').length > 1 ) ? '&' : '?';
        var callServer;
        callServer = new leimnud.module.rpc.xmlhttp({
        		url			: ajax_server,
        		async   : false,
        		method	: method,
        		args    : data
        	});
      	callServer.make();
      	response = callServer.xmlhttp.responseText;
      	var scs = callServer.xmlhttp.responseText.extractScript();

	  	scs.evalScript();
      	delete callServer;
    	} catch(ss){
    		alert("Error: "+ss.message+var_dump(ss));
    	}
      return response;//objetus.responseText;
  }
  /* ajax_message
   * Envia una solicitud GET a ajax_server con la variables "function" y las definidas en parameters.
   * @author       Julio Cesar Laura Avendano <juliocesar@colosa.com, julces2000@gmail.com>
   * @version 1.0
   * @package ajax
   * @param string ajax_server  url de la pagina servidor
   * @param string function     funci�n solicitada en el lado del servidor
   * @param string parameters   variables pasadas por url. Ej. variable=valor&otravariable=suvalor
   */
  function ajax_message(ajax_server, funcion, parameters, method, callback)
  {
      var objetus;
      objetus = get_xmlhttp();
      var response;
      try
      {
      	if (parameters) parameters = '&' + encodeURI(parameters);
      	if (!method ) method ="POST";
      	data = "function=" + funcion + parameters;
      	questionMark = (ajax_server.split('?').length > 1 ) ? '&' : '?';
      	objetus.open( method, ajax_server + ((method==='GET')? questionMark+data : '') , true );
        objetus.onreadystatechange=function() {
          if ( objetus.readyState==4)
          {
            if( objetus.status==200)
            {
                if ( callback ) callback(objetus.responseText);
            }
          }
        }
        if (method==='POST') objetus.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        objetus.send(((method==='GET')? null : data));
    	}catch(ss)
    	{
    		alert("error"+ss.message);
    	}
  }
  /* ajax_post
   * Envia una solicitud GET/POST a ajax_server con los parametros definidos
   * o los campos de un formulario
   * @author       Julio Cesar Laura Avendano <juliocesar@colosa.com, julces2000@gmail.com>
   * @version 1.0
   * @package ajax
   * @param string ajax_server  url de la pagina servidor
   * @param string function     funci�n solicitada en el lado del servidor
   * @param string parameters   variables pasadas por url o formulario.
   * @example: ajax_post('foo.com', document.form[0], "POST", callback )
   */
  function ajax_post(ajax_server, parameters, method, callback, asynchronous )
  {
      var objetus;
      objetus = get_xmlhttp();
      var response;
      try
      {
        if (typeof(parameters)==='object') parameters = ajax_getForm(parameters);
      	if (!method ) method ="POST";
      	if (typeof(asynchronous)==='undefined') asynchronous = false;
      	data = parameters;
      	questionMark = (ajax_server.split('?').length > 1 ) ? '&' : '?';
      	if ((method==='POST')||(method==='GET/POST')) {
      	  objetus.open( 'POST', ajax_server + ((data.length<1024)?(questionMark+data):''), asynchronous );
      	} else {
      	  objetus.open( method, ajax_server + ((method==='GET')? questionMark+data : '') , asynchronous );
      	}
        objetus.onreadystatechange=function() {
          if ( objetus.readyState==4)
          {
            if( objetus.status==200)
            {
                if ( callback ) callback(objetus.responseText);
            }
          }
        }
        if ((method==='POST')||(method==='GET/POST')) objetus.setRequestHeader("Content-Type", "application/x-www-form-urlencoded; charset=utf-8");
        objetus.send(((method==='GET')? null : data));
      	if (!asynchronous)
      	{
          if ( callback ) callback(objetus.responseText);
      	  return objetus.responseText;
        }
    	}catch(ss)
    	{
    		alert("Error: "+ var_dump(ss));
    	}
  }

  /*
   * Refactoring.......................
   * Fixed by checkboxes binary values
   * By Neyek <erik@colosa.com>
   * Date March 27th, 2009 12:20:00 GMT-4
   */

  function ajax_getForm( thisform ) {
	var formdata='';

	// Loop through form fields
	for (var i=0; i < thisform.length; i++) {
	  if ( formdata!=='' ) formdata += '&';
	  //Build Send String
	  if(thisform.elements[i].type == "text") { //Handle Textbox's
		  formdata += thisform.elements[i].name + "=" + encodeURIComponent(thisform.elements[i].value);
	  } else if(thisform.elements[i].type == "textarea") { //Handle textareas
		  formdata += thisform.elements[i].name + "=" + encodeURIComponent(thisform.elements[i].value);
	  } else if(thisform.elements[i].type == "checkbox") { //Handle checkbox's
          formdata += thisform.elements[i].name + '=' + ((thisform.elements[i].checked)? (typeof(thisform.elements[i].value) != 'undefined' ? thisform.elements[i].value : 'On') : '');
	  } else if(thisform.elements[i].type == "radio") { //Handle Radio buttons
		  if(thisform.elements[i].checked==true){
			  formdata += thisform.elements[i].name + "=" + thisform.elements[i].value;
		  }
	  } else if(thisform.elements[i].type == "select-multiple") { //Handle list box
		  for(var j=0; j<thisform.elements[i].options.length ;j++) {
			  if ( j!==0 ) formdata += '&';
			  formdata += ( (thisform.elements[i].options[j].selected)? thisform.elements[i].name.replace('[]', '[' + j + ']') + "=" + encodeURIComponent(thisform.elements[i].options[j].value): '');
		  }
	  } else {
		  //finally, this should theoretically this is a select box.
		  formdata += thisform.elements[i].name + "=" + encodeURIComponent(thisform.elements[i].value);
	  }
	}
	return formdata;
  }

/* COMMON FUNCTIONS
 */


function isNumber (sValue)
{
	var sValue = new String(sValue);
  var bDot   = false;
  var i, sCharacter;
  if ((sValue == null) || (sValue.length == 0))
  {
    if (isNumber.arguments.length == 1)
    {
    	return false;
    }
    else
    {
    	return (isNumber.arguments[1] == true);
    }
  }
  for (i = 0; i < sValue.length; i++)
  {
    sCharacter = sValue.charAt(i);
    if (i != 0)
    {
      if (sCharacter == '.')
      {
        if (!bDot)
        {
          bDot = true;
        }
        else
        {
          return false;
        }
      }
      else
      {
        if (!((sCharacter >= '0') && (sCharacter <= '9')))
        {
        	return false;
        }
      }
    }
    else
    {
      if (sCharacter == '.')
      {
        if (!bDot)
        {
          bDot = true;
        }
        else
        {
          return false;
        }
      }
      else
      {
        if (!((sCharacter >= '0') && (sCharacter <= '9') && (sCharacter != '-') || (sCharacter == '+')))
        {
        	return false;
        }
      }
    }
  }
  return true;
}

function roundNumber(iNumber, iDecimals)
{
  if(typeof(iDecimals) === 'undefined')
    iDecimals = 2;
	var iNumber   = parseFloat(iNumber || 0);
  var iDecimals = parseFloat(iDecimals || 0);
	return Math.round(iNumber * Math.pow(10, iDecimals)) / Math.pow(10, iDecimals);
}

function toMaskNumber(iNumber,dec)
{
	iNumber = fix(iNumber.toString(),dec || 2);
	var t=iNumber.split(".");
	var arrayResult=iNumber.replace(/\D/g,'').replace(/^0*/,'').split("").reverse();
	var result="";
	var aux=0;
	var sep=0;
	for(var i=0;i<arrayResult.length;i++)
	{
		if(i==1)
		{
			result="."+arrayResult[i]+result;
		}
		else
		{
			if(i>1 && aux>=3 && ((aux%3)==0))
			{
				result=arrayResult[i]+","+result;
				aux+=1;
				sep+=1;
			}
			else
			{
				result=arrayResult[i]+result;
				if(i>1)
				{
					aux+=1;
				}
			}
		}
	}
	return result;
}

function fix(val, dec)
{
	var a = val.split(".");
	var r="";
	if(a.length==1)
	{
		r=a[0]+"."+creaZero(dec);
	}
	else
	{
		if(a[1].length<=dec)
		{
			r=a[0]+"."+a[1]+creaZero(dec-a[1].length);
		}
		else
		{
			r=a[0]+"."+a[1].substr(0,dec);
		}
	}
	return r;
}

function creaZero(cant)
{
	var a="";
	for(var i=0;i<cant;i++)
	{
		a+="0";
	}
	return a;
}

function toUnmaskNumber(iNumber)
{
	var aux = "";
	var num = new String (iNumber);
	var len = num.length;
	var i = 0;
	for (i = 0; i < len; i++ ) {
		if (num.charAt ( i) != ',' && num.charAt (i) != '$' && num.charAt (i) != ' ' && num.charAt (i) != '%' ) aux = aux + num.charAt ( i);
	}
	return parseFloat(aux,2);
}

function compareDates(datea, dateB,porDias)
{
	var a = datea.split('/');

	var b = dateB.split('/');
	x = new Date(a[2], a[1], (porDias)?1:a[0]);
	y = new Date(b[2], b[1], (porDias)?1:b[0]);
	return ((x - y) <= 0) ? false : true;
}

/****THE ANSWER*****/
/*diferencia entre 2 fechas*/
function diff_date(fecha1, fecha2)
{ var f1 = fecha1.split('-');
	fecha1 = new Date();
	fecha1.setDate(f1[2]);
	fecha1.setMonth(f1[1]);
	fecha1.setYear(f1[0]);

  var f2 = fecha2.split('-');
	fecha2 = new Date();
	fecha2.setDate(f2[2]);
	fecha2.setMonth(f2[1]);
	fecha2.setYear(f2[0]);

	var dias = Math.floor((fecha1.getTime()-fecha2.getTime())/(3600000*24));
	return dias;
}

/*
 * author <julces2000@gmail.com>
 */
function getField( fieldName , formId )
{
  if (formId)
  {
    var form = document.getElementById(formId);
    if (!form) {form=document.getElementsByName(formId);
      if (form) {
      	if (form.length > 0) {
      	  form = form[0];
        }
      }
    }
    if (form.length > 0) {
      return form.elements[ 'form[' + fieldName + ']' ];
    }
    else {
    	//return null;
    	return document.getElementById( 'form[' + fieldName + ']' );
    }
  }
  else
  {
    return document.getElementById( 'form[' + fieldName + ']' );
  }
}

/*
 * author <julces2000@gmail.com>
 */
function getElementByName( fieldName )
{
  var elements = document.getElementsByName( fieldName );
  try{
    var x=0;
    if (elements.length === 1)
      return elements[0];
    else if (elements.length === 0)
      return elements[0];
    else
      return elements;
  } catch (E)
  {}
}


var myDialog;
function commonDialog ( type, title , text, buttons, values, callbackFn )  {
	myDialog = new leimnud.module.panel();
	myDialog.options = {
	  size:{w:400,h:200},
	  position:{center:true},
		title: title,
		control: { close	:false, roll	:false, drag	:true, resize	:false },
    fx: {
      //shadow	:true,
      blinkToFront:false,
      opacity	:true,
      drag:false,
      modal: true
    },
	  theme:"processmaker"
	};

	myDialog.make();
    switch (type) {
    case 'question':
       icon = 'question.gif';
       break
    case 'warning':
       icon = 'warning.gif';
       break
    case 'error':
       icon = 'error.gif';
       break
    default:
       icon = 'information.gif';
       break
    }

    var contentStr = '';
    contentStr += "<div><table border='0' width='100%' > <tr height='70'><td width='60' align='center' >";
    contentStr += "<img src='/js/maborak/core/images/" + icon + "'></td><td >" + text + "</td></tr>";
    contentStr += "<tr height='35' valign='bottom'><td colspan='2' align='center'> ";
    if ( buttons.custom && buttons.customText )
      contentStr += "<input type='button' value='" + buttons.customText + "' onclick='myDialog.dialogCallback(4); ';> &nbsp; ";
    if ( buttons.cancel )
      contentStr += "<input type='button' value='Cancel' onclick='myDialog.dialogCallback(0);'> &nbsp; ";
    if ( buttons.yes )
      contentStr += "<input type='button' value=' Yes ' onclick='myDialog.dialogCallback(1);'> &nbsp; ";
    if ( buttons.no )
      contentStr += "<input type='button' value=' No ' onclick='myDialog.dialogCallback(2);'> &nbsp; ";
    contentStr += "</td></tr>";
    contentStr += "</table>";

    myDialog.addContent( contentStr );
    myDialog.values = values;
	  myDialog.dialogCallback = function ( dialogResult ) {
		  myDialog.remove( );
      if ( callbackFn )
        callbackFn ( dialogResult );
    }

}
function var_dump(obj)
{
	var o,dump;
	dump='';
	if (typeof(obj)=='object') {
  	for(o in obj) if (typeof(obj[o])!=='function')
  	{
  		dump+=o+'('+typeof(obj[o])+'):'+obj[o]+"\n";
  	}
  }
	else
	dump=obj;
	return dump;
}

/*
 * @author David Callizaya
 */
var currentPopupWindow;
function popupWindow ( title , url, width, height, callbackFn , autoSizeWidth, autoSizeHeight,modal,showModalColor)  {
	modal = (modal===false)?false:true;
	showModalColor = (showModalColor===false)?false:true;
	var myPanel = new leimnud.module.panel();
	currentPopupWindow = myPanel;
	myPanel.options = {
		size:{w:width,h:height},
		position:{center:true},
		title: title,
		theme: "processmaker",
		control: { close :true, roll	:false, drag	:true, resize	:false},
		fx: {
			//shadow	:true,
			blinkToFront:true,
			opacity	:true,
			drag:true,
			modal: modal
		      //opacityModal:{static:'1'}
		}
	};
	if(showModalColor===true)
	{
		//Panel.setStyle={modal:{backgroundColor:"#ECF3F6"}};
	}
	else
	{
		myPanel.styles.fx.opacityModal.Static='0';
	}
	myPanel.make();
	myPanel.loader.show();
	var r = new leimnud.module.rpc.xmlhttp({url:url});
	r.callback=leimnud.closure({Function:function(rpc,myPanel){
  		myPanel.addContent(rpc.xmlhttp.responseText);
  		var myScripts = myPanel.elements.content.getElementsByTagName('SCRIPT');
  		for(var rr=0; rr<myScripts.length ; rr++){
  		  try {
  		    if (myScripts[rr].innerHTML!=='')
  		      if (window.execScript)
  	          window.execScript( myScripts[rr].innerHTML, 'javascript' );
  	        else
  	          window.setTimeout( myScripts[rr].innerHTML, 0 );
  		  } catch (e) {
  		    alert(e.description);
  		  }
  		}
  		/* Autosize of panels, to fill only the first child of the
  		 * rendered page (take note)
  		 */
  		var panelNonContentHeight = 62;
  		var panelNonContentWidth  = 28;
  		myPanel.elements.content.style.padding="0px;";
  		try {
  		  if (autoSizeWidth)
    		  myPanel.resize({w:myPanel.elements.content.childNodes[0].clientWidth+panelNonContentWidth});
  		  if (autoSizeHeight)
    		  myPanel.resize({h:myPanel.elements.content.childNodes[0].clientHeight+panelNonContentHeight});
  	  } catch (e) {
  	    alert(_('ID_MSG_AJAX_FAILURE'));
  	  }
  		delete newdiv;
  		delete myScripts;
			myPanel.command(myPanel.loader.hide);
	},args:[r, myPanel]});
	r.make();

/*
  myPanel.dialogCallback = function (  ) {
  }
*/
  delete myPanel;
}

var lastPopupWindow;
function popupWindowObject ( title , url, width, height, callbackFn , autoSizeWidth, autoSizeHeight,modal,showModalColor)  {
	modal = (modal===false)?false:true;
	showModalColor = (showModalColor===false)?false:true;
	var myPanel = new leimnud.module.panel();
        lastPopupWindow = myPanel;
	myPanel.options = {
		size:{w:width,h:height},
		position:{center:true},
		title: title,
		theme: "processmaker",
		control: { close :true, roll	:false, drag	:true, resize	:false},
		fx: {
			//shadow	:true,
			blinkToFront:true,
			opacity	:true,
			drag:true,
			modal: modal
		      //opacityModal:{static:'1'}
		}
	};
	if(showModalColor===true)
	{
		//Panel.setStyle={modal:{backgroundColor:"#ECF3F6"}};
	}
	else
	{
		myPanel.styles.fx.opacityModal.Static='0';
	}
	myPanel.make();
	myPanel.loader.show();
	var r = new leimnud.module.rpc.xmlhttp({url:url});
	r.callback=leimnud.closure({Function:function(rpc,myPanel){
  		myPanel.addContent(rpc.xmlhttp.responseText);
  		var myScripts = myPanel.elements.content.getElementsByTagName('SCRIPT');
  		for(var rr=0; rr<myScripts.length ; rr++){
  		  try {
  		    if (myScripts[rr].innerHTML!=='')
  		      if (window.execScript)
  	          window.execScript( myScripts[rr].innerHTML, 'javascript' );
  	        else
  	          window.setTimeout( myScripts[rr].innerHTML, 0 );
  		  } catch (e) {
  		    alert(e.description);
  		  }
  		}
  		/* Autosize of panels, to fill only the first child of the
  		 * rendered page (take note)
  		 */
  		var panelNonContentHeight = 62;
  		var panelNonContentWidth  = 28;
  		myPanel.elements.content.style.padding="0px;";
  		try {
  		  if (autoSizeWidth)
    		  myPanel.resize({w:myPanel.elements.content.childNodes[0].clientWidth+panelNonContentWidth});
  		  if (autoSizeHeight)
    		  myPanel.resize({h:myPanel.elements.content.childNodes[0].clientHeight+panelNonContentHeight});
  	  } catch (e) {
  	    alert(_('ID_MSG_AJAX_FAILURE'));
  	  }
  		delete newdiv;
  		delete myScripts;
			myPanel.command(myPanel.loader.hide);
	},args:[r, myPanel]});
	r.make();

/*
  myPanel.dialogCallback = function (  ) {
  }
*/
    return myPanel;
}

// Get an object left position from the upper left viewport corner
// Tested with relative and nested objects
function getAbsoluteLeft(o) {
	oLeft = o.offsetLeft            // Get left position from the parent object
	while(o.offsetParent!=null) {   // Parse the parent hierarchy up to the document element
		oParent = o.offsetParent    // Get parent object reference
		oLeft += oParent.offsetLeft // Add parent left position
		o = oParent
	}
	// Return left postion
	return oLeft
}
// Get an object top position from the upper left viewport corner
// Tested with relative and nested objects
function getAbsoluteTop(o) {
	oTop = o.offsetTop            // Get top position from the parent object
	while(o.offsetParent!=null) { // Parse the parent hierarchy up to the document element
		oParent = o.offsetParent  // Get parent object reference
		oTop += oParent.offsetTop // Add parent top position
		o = oParent
	}
	// Return top position
	return oTop
}
/*
 */
function showHideElement(id)
{
  var element;
  if (typeof(id)=='object') element=id;
  else element=document.getElementById(id);
  if (element.style.display==='none') {
    switch(element.type) {
      case 'table':
        element.style.display = 'table';
        break;
      default:
        element.style.display = '';
    }
  } else {
    element.style.display = 'none';
  }
}
/*
 */
function showHideSearch(id,aElement,openText,closeText)
{
  var element=document.getElementById(id);
  if (element.style.display==='none') {
    if (!closeText) closeText=G_STRINGS.ID_CLOSE_SEARCH;
    if (aElement) {
      aElement.innerHTML=closeText;
      var bullet = document.getElementById(aElement.id+'[bullet]');
      bullet.src='/images/bulletButtonDown.gif';
    }
    switch(element.type) {
      case 'table':
        document.getElementById(id).style.display = 'table';
        break;
      default:
        document.getElementById(id).style.display = '';
    }
  } else {
    if (!openText) openText=G_STRINGS.ID_OPEN_SEARCH;
    if (aElement) {
      aElement.innerHTML=openText;
      var bullet = document.getElementById(aElement.id+'[bullet]');
      bullet.src='/images/bulletButton.gif';
    }
    document.getElementById(id).style.display = 'none';
  }
}
/* Loads a page but in a non visible div with absolute on (x,y)
 * and execute the javascript node that it contains.
 */
function loadPage ( url, x, y , visibility , div )  {
    visibility = typeof(visibility)==='undefined'?'hidden':visibility;
  	var r = new leimnud.module.rpc.xmlhttp({url:url});
  	r.callback=leimnud.closure({Function:function(rpc,div){
  	    if (typeof(div)==='undefined') div=createDiv('');
        if (typeof(x)!=='undefined') div.style.left=x;
        if (typeof(y)!=='undefined') div.style.top =y;
        div.innerHTML=rpc.xmlhttp.responseText;
    		var myScripts = div.getElementsByTagName('SCRIPT');
    		for(var rr=0; rr<myScripts.length ; rr++){
    		  try {
    		    if (myScripts[rr].innerHTML!=='')
    		      if (window.execScript)
    		          window.execScript( myScripts[rr].innerHTML, 'javascript' );
    		        else
    		          window.setTimeout( myScripts[rr].innerHTML, 0 );
    		  } catch (e) {
    		    alert(e.description);
    		  }
    		}
    		delete div;
    		delete myScripts;
  	},args:[r,div]});
  	r.make();
}
function createDiv(id) {

   var newdiv = document.createElement('div');
   newdiv.setAttribute('id', id);

   newdiv.style.position = "absolute";
   newdiv.style.left = 0;
   newdiv.style.top = 0;

   newdiv.style.visibility="hidden";

   document.body.appendChild(newdiv);

   return newdiv;
}

/* THIS FUNCTIONS WHERE COPIED FROM JSFORMS */

/*if (window.attachEvent)
  window.attachEvent('onload', _OnLoad_);
else
  window.addEventListener('load', _OnLoad_, true);*/

//function _OnLoad_() {


/*onload=function(){

	if (self.setNewDates)
    self.setNewDates();

  if (self.setReloadFields)
    self.setReloadFields();

  if (self.enableHtmlEdit)
    self.enableHtmlEdit();

  if (self.dynaformOnloadUsers)
    self.dynaformOnloadUsers();

  if (self.dynaformOnload)
    self.dynaformOnload();


}*/



function refillText( fldName, ajax_server, values ) {
	var objetus;
    objetus = get_xmlhttp();
    objetus.open ("GET", ajax_server + "?" + values, false);
    objetus.onreadystatechange=function() {
        if ( objetus.readyState == 1 )
        {
          var textfield = document.getElementById( 'form[' + fldName + ']' );
          if ( ! isdefined( textfield ))
            var textfield = document.getElementById( fldName );
          textfield.value = '';

        }
        else if ( objetus.readyState==4)
        {
            if( objetus.status==200)
            {
//              alert ( objetus.responseText );
              var xmlDoc = objetus.responseXML;
              if ( xmlDoc ) {
                 var textfield = document.getElementById( 'form[' + fldName + ']' );
                 if ( ! isdefined( textfield ))
                   var textfield = document.getElementById( fldName );
                 var dataArray = xmlDoc.getElementsByTagName('value');
                 if (dataArray[0].firstChild)
                 	 if((dataArray[0].firstChild.xml)!='_vacio'){
                 		 textfield.value = dataArray[0].firstChild.xml;
                 		 if(textfield.type != 'hidden')
                 		   if ( textfield.onchange )
                 			   textfield.onchange();
                 	 }
              }
            }
            else
            {
                window.alert('error-['+ objetus.status +']-' + objetus.responseText );
            }
        }
    }
    objetus.send(null);
}

function refillCaption( fldName, ajax_server, values ){
	var objetus;
    objetus = get_xmlhttp();
    objetus.open ("GET", ajax_server + "?" + values, false);
    objetus.onreadystatechange=function() {
        if ( objetus.readyState == 1 )
        {
          var textfield = document.getElementById( 'FLD_' + fldName );
          textfield.innerHTML = '';

        }
        else if ( objetus.readyState==4)
        {
            if( objetus.status==200)
            {
              var xmlDoc = objetus.responseXML;
              if ( xmlDoc ) {
                 var textfield = document.getElementById( 'FLD_' + fldName );
                 var dataArray = xmlDoc.getElementsByTagName('value');
                 if (dataArray[0].firstChild)
                 	  if((dataArray[0].firstChild.xml)!='_vacio')
                 		  //textfield.innerHTML = '<font size="1">' + dataArray[0].firstChild.xml + '</font>';
                 		  textfield.innerHTML = dataArray[0].firstChild.xml;
              }
            }
            else
            {
                window.alert('error-['+ objetus.status +']-' + objetus.responseText );
            }
        }
    }
    objetus.send(null);
}


function refillDropdown( fldName, ajax_server, values , InitValue)
{

	var objetus;
    objetus = get_xmlhttp();
    objetus.open ("GET", ajax_server + "?" + values, false);
    objetus.onreadystatechange=function() {
        if ( objetus.readyState == 1 )
        {
          var dropdown = document.getElementById( 'form[' + fldName + ']' );

          while ( dropdown.hasChildNodes() )
            dropdown.removeChild(dropdown.childNodes[0]);

        }
        else if ( objetus.readyState==4)
        {
            if( objetus.status==200)
            {
              var xmlDoc = objetus.responseXML;

              if ( xmlDoc ) {
                 var dropdown = document.getElementById( 'form[' + fldName + ']' );
                 var dataArray = xmlDoc.getElementsByTagName('item');
                 itemsNumber = dataArray.length;

                 if(InitValue == true) itemsNumber = dataArray.length-1;
                 for (var i=0; i<itemsNumber; i++){
                    dropdown.options[ dropdown.length] = new Option(dataArray[i].firstChild.xml, dataArray[i].attributes[0].value );
                    if(InitValue == true) {
                    	if(dropdown.options[ dropdown.length-1].value == dataArray[dataArray.length-1].firstChild.xml)
                    		dropdown.options[i].selected = true;
                    }
                 }
                 dropdown.onchange();
              }
            }
            else
            {
                window.alert('error-['+ objetus.status +']-' + objetus.responseText );
            }
        }
    }
    objetus.send(null);
}

function iframe_get_xmlhttp() {
  try {
    xmlhttp = new ActiveXObject('Msxml2.XMLHTTP2');
  } catch (e) {
    try {
      xmlhttp = new ActiveXObject('Microsoft.XMLHTTP');
    } catch (E) {
      xmlhttp = false;
    }
  }
  if (!xmlhttp && typeof XMLHttpRequest != 'undefined') {
    xmlhttp = new XMLHttpRequest();
  }
  return xmlhttp;
}

function get_xmlhttp() {
  try {
  	xmlhttp = false;
  	if ( window.ActiveXObject )
      xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
  }
  catch (e) {
    try {
      xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
    }
    catch (E) {
      xmlhttp = false;
    }
  }
  if (!xmlhttp && typeof XMLHttpRequest!='undefined') {
    xmlhttp = new XMLHttpRequest();
  }
  return xmlhttp;
}

function refillTextError( div_container, fldName, ajax_server, values )
{
	var objetus;
    objetus = get_xmlhttp();
    objetus.open ("GET", ajax_server + "?" + values, false);
    objetus.onreadystatechange=function() {
        if ( objetus.readyState == 1 )
        {
          var textfield = document.getElementById( 'form[' + fldName + ']' );
          textfield.value = '';
          document.getElementById(div_container).innerHTML = '';

        }
        else if ( objetus.readyState==4)
        {
            if( objetus.status==200)
            {
              var xmlDoc = objetus.responseXML;
              if ( xmlDoc ) {
                 var textfield = document.getElementById( 'form[' + fldName + ']' );
                 var dataArray = xmlDoc.getElementsByTagName('value');
                 textfield.value = dataArray[0].firstChild.xml;
                 var dataArray = xmlDoc.getElementsByTagName('message');
                 if ( dataArray[0].firstChild )
                   document.getElementById(div_container).innerHTML = '<b>' + dataArray[0].firstChild.xml + '</b>';
              }
            }
            else
            {
                window.alert('error-['+ objetus.status +']-' + objetus.responseText );
            }
        }
    }
    objetus.send(null);
}



function iframe_get_xmlhttp() {
  try {
    xmlhttp = new ActiveXObject('Msxml2.XMLHTTP5');
  } catch (e) {
    try {
      xmlhttp = new ActiveXObject('Microsoft.XMLHTTP');
    } catch (E) {
      xmlhttp = false;
    }
  }
  if (!xmlhttp && typeof XMLHttpRequest != 'undefined') {
    xmlhttp = new XMLHttpRequest();
  }
  return xmlhttp;
}

function iframe_ajax_init(ajax_server, div_container, values, callback) {
	var objetus;
  objetus = iframe_get_xmlhttp();
  objetus.open ('GET', ajax_server + '?' + values, true);
  objetus.onreadystatechange = function() {
    if ( objetus.readyState == 1 ) {
      document.getElementById(div_container).style.display = '';
      document.getElementById(div_container).innerHTML = '...';
    }
    else if (objetus.readyState==4) {
      if (objetus.status==200) {
        document.getElementById(div_container).innerHTML = objetus.responseText;
        if (callback != '')
          callback();
      }
      else {
        window.alert('error-['+ objetus.status +']-' + objetus.responseText );
      }
    }
  }
  objetus.send(null);
}

function iframe_ajax_init_2(ajax_server, div_container, values, callback) {
	var objetus;
  objetus = iframe_get_xmlhttp();
  objetus.open ('GET', ajax_server + '?' + values, true);
  objetus.onreadystatechange = function() {
    if ( objetus.readyState == 1 ) {
      div_container.style.display = '';
      div_container.innerHTML = '...';
    }
    else if (objetus.readyState==4) {
      if (objetus.status==200) {
        div_container.innerHTML = objetus.responseText;
        if (callback != '')
          callback();
      }
      else {
        window.alert('error-['+ objetus.status +']-' + objetus.responseText );
      }
    }
  }
  objetus.send(null);
}

function myEmptyCallback() {
}

function disable (obj) {
  obj.disabled = true;
  return;
}

function enable (obj) {
  obj.disabled = false;
  return;
}

function disableById (id) {
  obj = getField(id);
  obj.disabled = true;
  return;
}

function enableById (id) {
  obj = getField(id);
  obj.disabled = false;
  return;
}

function visible (obj) {
  if (obj.style) {
    obj.style.visibility = 'visible';
  }
  return;
}

function hidden (obj) {
  if (obj.style) {
    obj.style.visibility = 'hidden';
  }
  return;
}

function visibleById (id) {
  obj = getField(id);
  obj.style.visibility = 'visible';
  return;
}

function hiddenById (id) {
  obj = getField(id);
  obj.style.visibility = 'hidden';
  return;
}

function hiddenRowById (id) {
	row = 'DIV_'+ id +'.style.visibility = \'hidden\';';
	hiden = 'DIV_'+ id +'.style.display = \'none\';';
	eval(row);
	eval(hiden);
  return;
}
function visibleRowById (id) {
	row = 'DIV_'+ id +'.style.visibility = \'visible\';';
	block = 'DIV_'+ id +'.style.display = \'block\';';
	eval(row);
	eval(block);
  return;
}

function setFocus (obj) {
  obj.focus();
  return;
}

function setFocusById (id) {
  obj = getField (id);
  setFocus(obj);
  return;
}

function submitForm () {
    document.forms[0].submit();
  return;
}

function changeValue(id, newValue) {
  obj = getField(id);
  obj.value = newValue;
  return ;
}

function getValue(obj) {
  return obj.value;
}

function getValueById (id) {
  obj = getField(id);
  return obj.value;
}

function removeCurrencySign (snumber) {
   var aux = '';
   var num = new String (snumber);
   var len = num.length;
   var i = 0;
   for (i=0; !(i>=len); i++)
     if (num.charAt(i) != ',' && num.charAt(i) != '$' && num.charAt(i) != ' ') aux = aux + num.charAt(i);
   return aux;
 }

 function removePercentageSign (snumber) {
   var aux = '';
   var num = new String (snumber);
   var len = num.length;
   var i = 0;
   for (i=0; !(i>=len); i++)
     if (num.charAt(i) != ',' && num.charAt(i) != '%' && num.charAt(i) != ' ') aux = aux + num.charAt(i);
   return aux;
 }

 function toReadOnly(obj) {
 	 if (obj) {
     obj.readOnly = 'readOnly';
     obj.style.background = '#CCCCCC';
   }
   return;
 }

 function toReadOnlyById(id) {
   obj = getField(id);
   if (obj) {
     obj.readOnly = 'readOnly';
     obj.style.background = '#CCCCCC';
   }
   return ;
 }

function getGridField(Grid, Row, Field) {
	obj = document.getElementById('form[' + Grid + ']' + '[' + Row + ']' + '[' + Field + ']');
  return obj;
}

function getGridValueById(Grid, Row, Field) {
  obj = getGridField(Grid, Row, Field);
  if (obj)
    return obj.value;
  else
    return '';
}

function Number_Rows_Grid(Grid, Field) {
	Number_Rows = 1;
	if (getGridField(Grid, Number_Rows, Field)) {
		Number_Rows = 0;
	  while (getGridField(Grid, (Number_Rows + 1), Field))
	    Number_Rows++;
	  return Number_Rows;
	}
	else
	  return 0;
}

function attachFunctionEventOnChange(Obj, TheFunction) {
	Obj.oncustomize = TheFunction;
}

function attachFunctionEventOnChangeById(Id, TheFunction) {
	Obj = getField(Id);
	Obj.oncustomize = TheFunction;
}

function attachFunctionEventOnKeypress(Obj, TheFunction) {
	Obj.attachEvent('onkeypress', TheFunction);
}

function attachFunctionEventOnKeypressById(Id, TheFunction) {
	Obj = getField(Id);
	Obj.attachEvent('onkeypress', TheFunction);
}

function unselectOptions ( field ) {
var radios = document.getElementById('form[' + field + ']');
	if (radios) {
	  var inputs = radios.getElementsByTagName ('input');
	  if (inputs) {
		  for(var i = 0; i < inputs.length; ++i) {
		  	inputs[i].checked = false;
			}
	  }
	}
}

/* @author Alvaro Campos Sanchez
 */
function validDate(TheField, Required) {

     var date = TheField.split("-");
     var date1 = date[0];
     var date2 = date[1];
     var date3 = date[2];
     var TheDay,TheMonth,TheYear;

     if ((date1.length==4)&&(!TheYear))
      TheYear = date1;
     if (date1.length==2)
       if ((date1>0)&&(date1<=12)&&(!TheMonth))
         TheMonth = date1;
       else
         if ((date1>0)&&(date1<=31)&&(!TheDay))
           TheDay = date1;
         else
           TheYear = date1;

     if ((date2.length==4)&&(!TheYear))
       TheYear = date2;
     if (date2.length==2)
       if ((date2>0)&&(date2<=12)&&(!TheMonth))
         TheMonth = date2;
       else
         if ((date2>0)&&(date2<=31)&&(!TheDay))
           TheDay = date2;
         else
           TheYear = date2;

     if((date3.length==4)&&(!TheYear))
       TheYear = date3;
     if (date3.length==2)
       if ((date3>0)&&(date3<=12)&&(!TheMonth))
         TheMonth = date3;
       else
         if ((date3>0)&&(date3<=31)&&(!TheDay))
           TheDay = date3;
         else
           TheYear = date3;

     if (!TheYear || !TheMonth || !TheDay)
       return false;
     if ((Required)||(Required=='true'))
       if ((TheYear == 0) || (TheMonth == 0) || (TheDay == 0))
         return false;
     if (TheMonth == 02)
       if (TheDay > 29)
         return false;
     if ((TheMonth != 02)&&(TheMonth < 13)&&(TheMonth > 0))
       if (TheDay > 30)
         return false;

 return true;

}

/* @author David S. Callizaya S.
 */
function globalEval(scriptCode) {
  if (scriptCode!=='')
    if (window.execScript)
      window.execScript( scriptCode, 'javascript' );
    else
      window.setTimeout( scriptCode, 0 );
}
function switchImage(oImg,url1,url2){
  if (oImg && (url2!=='')) {
    oImg.src=(oImg.src.substr(oImg.src.length-url1.length,url1.length)===url1)? url2: url1;
  }
}
function MM_preloadImages() { //v3.0
  var d=document; if(d.images){ if(!d.MM_p) d.MM_p=new Array();
    var i,j=d.MM_p.length,a=MM_preloadImages.arguments; for(i=0; i<a.length; i++)
    if (a[i].indexOf("#")!=0){ d.MM_p[j]=new Image; d.MM_p[j++].src=a[i];}}
}
function backImage(oImg,p){
  oImg.style.background=p;
}

/* javascript para el toolbar */
var lc=false;
var sh=function(a,i)
{
    var c = (a.nextSibling.nextSibling)?a.nextSibling.nextSibling:a.nextSibling;
    if(lc && lc.c!=i){
        lc.d.style.display='none';
        lc.a.style.color='#666';
    }
    lc={d:c,c:i,a:a};
    a.style.color=(c.style.display=='' || c.style.display=='none')?"black":"#666";
    c.style.display=(!c.style.display || c.style.display=='none')?"block":"none";
}

/**
 * Set focus to first field from a dynaform
 *
 * @Author Erik Amaru Ortiz <erik@colosa.com, aortiz.erik@gmail.com>
 * @return false
 */
function dynaformSetFocus(){
  /* looking for the inputs fields */
  var inputs = document.getElementsByTagName('input');
  if(inputs.length > 0){
    for(i in inputs){
      type = inputs[i].type;
      if(type == "text" || type == "radio" || type == "checkbox" || type == "file" || type == "password"){
        try {
          inputs[i].focus();
        } catch (e) {
          //nothing
        }
        return false;
      }
    }
  } else {
	/* if there is no inputs fields, maybe it has a textarea field */
	var ta = document.getElementsByTagName('textarea');
	if(ta.length > 0){
	  inputs[0].focus();
	  return false;
    }
  }
  return false;
}

/**
 * Set id from id_label if it does not exist
 *
 * @Author alvaro <alvaro@colosa.com, alvaro.cs@live.com>
 * @return false
 */
function idSet(name){
  var inputs = document.getElementsByTagName('input');
  if(inputs.length > 0){
    for(i in inputs){
      id = inputs[i].id;
      if(id == "form["+name+"_label]"){

         if(inputs[i].value.trim())
           var valueLabel = inputs[i].value;
       else
           var valueLabel = "Empty";
      }

      if(id == "form["+name+"]"){
        try {
          if(valueLabel !="Empty"){
            if (! inputs[i].value)
              inputs[i].value =  valueLabel;
          }else
             inputs[i].value =  "";

        } catch (e) {
          //nothing
        }
      }
    }
  }
  return false;
}

/**
 * ********************************* Misc Functions by Neyek ****************************************
 */

/**
 * get the htmlentities from a string
 *
 * @Author Erik Amaru Ortiz <erik@colosa.com, aortiz.erik@gmail.com>
 * @Param (string) string within the htmlentities to parse
 * @Param (string) quote_style it can be (ENT_QUOTES or ENT_NOQUOTES)
 * @Return (string) the parsed string with htmlentities at the string passed such as parameter
 */
function htmlentities (string, quote_style, charset, double_encode) {
    // Convert all applicable characters to HTML entities
    //
    // version: 1109.2015
    // discuss at: http://phpjs.org/functions/htmlentities    // +   original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +    revised by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +   improved by: nobbler
    // +    tweaked by: Jack
    // +   bugfixed by: Onno Marsman    // +    revised by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +    bugfixed by: Brett Zamir (http://brett-zamir.me)
    // +      input by: Ratheous
    // +   improved by: Rafał Kukawski (http://blog.kukawski.pl)
    // +   improved by: Dj (http://phpjs.org/functions/htmlentities:425#comment_134018)    // -    depends on: get_html_translation_table
    // *     example 1: htmlentities('Kevin & van Zonneveld');
    // *     returns 1: 'Kevin &amp; van Zonneveld'
    // *     example 2: htmlentities("foo'bar","ENT_QUOTES");
    // *     returns 2: 'foo&#039;bar'
    var hash_map = get_html_translation_table('HTML_ENTITIES', quote_style), symbol = '';

    string = string == null ? '' : string + '';

    if (!hash_map) {
      return false;
    }

    if (quote_style && quote_style === 'ENT_QUOTES') {
        hash_map["'"] = '&#039;';    }

    if (!!double_encode || double_encode == null) {
        for (symbol in hash_map) {
            if (hash_map.hasOwnProperty(symbol)) {
              string = string.split(symbol).join(hash_map[symbol]);
            }
        }
    } else {
        string = string.replace(/([\s\S]*?)(&(?:#\d+|#x[\da-f]+|[a-zA-Z][\da-z]*);|$)/g, function (ignore, text, entity) {
          for (symbol in hash_map) {
            if (hash_map.hasOwnProperty(symbol)) {
              text = text.split(symbol).join(hash_map[symbol]);
            }
          }
          return text + entity;
        });
    }
     return string.toString();
}


function utf8_encode (argString) {
  var utftext = "",
  start, end, stringl = 0;
  var string = argString;
  start = end = 0;
  stringl = string.length;
    for (var n = 0; n < stringl; n++) {
      var c1 = string.charCodeAt(n);
      var enc = null;
	if (c1 < 128) {
	  end++;
	}
	else if (c1 > 127 && c1 < 2048) {
	  enc = String.fromCharCode((c1 >> 6) | 192) + String.fromCharCode((c1 & 63) | 128);
	}
	else {
	  enc = String.fromCharCode((c1 >> 12) | 224) + String.fromCharCode(((c1 >> 6) & 63) | 128) + String.fromCharCode((c1 & 63) | 128);
	}
	if (enc !== null) {
	    if (end > start) {
	      utftext += string.slice(start, end);
	    }
	  utftext += enc;
	  start = end = n + 1;
	}
    }
    if (end > start) {
      utftext += string.slice(start, stringl);
    }
  return utftext;
}
function base64_encode (data) {
  var b64 = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=";
  var o1, o2, o3, h1, h2, h3, h4, bits, i = 0,
  ac = 0,
  enc = "",
  tmp_arr = [];
    if (!data) {
      return data;
    }
  data = utf8_encode(data + '');
    do {
      o1 = data.charCodeAt(i++);
      o2 = data.charCodeAt(i++);
      o3 = data.charCodeAt(i++);
      bits = o1 << 16 | o2 << 8 | o3;
      h1 = bits >> 18 & 0x3f;
      h2 = bits >> 12 & 0x3f;
      h3 = bits >> 6 & 0x3f;
      h4 = bits & 0x3f;
      tmp_arr[ac++] = b64.charAt(h1) + b64.charAt(h2) + b64.charAt(h3) + b64.charAt(h4);
    } while (i < data.length);
  enc = tmp_arr.join('');
    switch (data.length % 3) {
      case 1:
	enc = enc.slice(0, -2) + '==';
      break;
      case 2:
	enc = enc.slice(0, -1) + '=';
      break;
    }
  return enc;
}

function get_html_translation_table (table, quote_style) {
    // Returns the internal translation table used by htmlspecialchars and htmlentities
    //
    // version: 1109.2015
    // discuss at: http://phpjs.org/functions/get_html_translation_table    // +   original by: Philip Peterson
    // +    revised by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +   bugfixed by: noname
    // +   bugfixed by: Alex
    // +   bugfixed by: Marco    // +   bugfixed by: madipta
    // +   improved by: KELAN
    // +   improved by: Brett Zamir (http://brett-zamir.me)
    // +   bugfixed by: Brett Zamir (http://brett-zamir.me)
    // +      input by: Frank Forte    // +   bugfixed by: T.Wild
    // +      input by: Ratheous
    // %          note: It has been decided that we're not going to add global
    // %          note: dependencies to php.js, meaning the constants are not
    // %          note: real constants, but strings instead. Integers are also supported if someone    // %          note: chooses to create the constants themselves.
    // *     example 1: get_html_translation_table('HTML_SPECIALCHARS');
    // *     returns 1: {'"': '&quot;', '&': '&amp;', '<': '&lt;', '>': '&gt;'}
    var entities = {},
        hash_map = {},        decimal;
    var constMappingTable = {},
        constMappingQuoteStyle = {};
    var useTable = {},
        useQuoteStyle = {};
    // Translate arguments
    constMappingTable[0] = 'HTML_SPECIALCHARS';
    constMappingTable[1] = 'HTML_ENTITIES';
    constMappingQuoteStyle[0] = 'ENT_NOQUOTES';    constMappingQuoteStyle[2] = 'ENT_COMPAT';
    constMappingQuoteStyle[3] = 'ENT_QUOTES';

    useTable = !isNaN(table) ? constMappingTable[table] : table ? table.toUpperCase() : 'HTML_SPECIALCHARS';
    useQuoteStyle = !isNaN(quote_style) ? constMappingQuoteStyle[quote_style] : quote_style ? quote_style.toUpperCase() : 'ENT_COMPAT';
    if (useTable !== 'HTML_SPECIALCHARS' && useTable !== 'HTML_ENTITIES') {
        throw new Error("Table: " + useTable + ' not supported');
        // return false;
    }
    entities['38'] = '&amp;';
    if (useTable === 'HTML_ENTITIES') {
        entities['160'] = '&nbsp;';
        entities['161'] = '&iexcl;';        entities['162'] = '&cent;';
        entities['163'] = '&pound;';
        entities['164'] = '&curren;';
        entities['165'] = '&yen;';
        entities['166'] = '&brvbar;';        entities['167'] = '&sect;';
        entities['168'] = '&uml;';
        entities['169'] = '&copy;';
        entities['170'] = '&ordf;';
        entities['171'] = '&laquo;';        entities['172'] = '&not;';
        entities['173'] = '&shy;';
        entities['174'] = '&reg;';
        entities['175'] = '&macr;';
        entities['176'] = '&deg;';        entities['177'] = '&plusmn;';
        entities['178'] = '&sup2;';
        entities['179'] = '&sup3;';
        entities['180'] = '&acute;';
        entities['181'] = '&micro;';        entities['182'] = '&para;';
        entities['183'] = '&middot;';
        entities['184'] = '&cedil;';
        entities['185'] = '&sup1;';
        entities['186'] = '&ordm;';        entities['187'] = '&raquo;';
        entities['188'] = '&frac14;';
        entities['189'] = '&frac12;';
        entities['190'] = '&frac34;';
        entities['191'] = '&iquest;';        entities['192'] = '&Agrave;';
        entities['193'] = '&Aacute;';
        entities['194'] = '&Acirc;';
        entities['195'] = '&Atilde;';
        entities['196'] = '&Auml;';        entities['197'] = '&Aring;';
        entities['198'] = '&AElig;';
        entities['199'] = '&Ccedil;';
        entities['200'] = '&Egrave;';
        entities['201'] = '&Eacute;';        entities['202'] = '&Ecirc;';
        entities['203'] = '&Euml;';
        entities['204'] = '&Igrave;';
        entities['205'] = '&Iacute;';
        entities['206'] = '&Icirc;';        entities['207'] = '&Iuml;';
        entities['208'] = '&ETH;';
        entities['209'] = '&Ntilde;';
        entities['210'] = '&Ograve;';
        entities['211'] = '&Oacute;';        entities['212'] = '&Ocirc;';
        entities['213'] = '&Otilde;';
        entities['214'] = '&Ouml;';
        entities['215'] = '&times;';
        entities['216'] = '&Oslash;';        entities['217'] = '&Ugrave;';
        entities['218'] = '&Uacute;';
        entities['219'] = '&Ucirc;';
        entities['220'] = '&Uuml;';
        entities['221'] = '&Yacute;';        entities['222'] = '&THORN;';
        entities['223'] = '&szlig;';
        entities['224'] = '&agrave;';
        entities['225'] = '&aacute;';
        entities['226'] = '&acirc;';        entities['227'] = '&atilde;';
        entities['228'] = '&auml;';
        entities['229'] = '&aring;';
        entities['230'] = '&aelig;';
        entities['231'] = '&ccedil;';        entities['232'] = '&egrave;';
        entities['233'] = '&eacute;';
        entities['234'] = '&ecirc;';
        entities['235'] = '&euml;';
        entities['236'] = '&igrave;';        entities['237'] = '&iacute;';
        entities['238'] = '&icirc;';
        entities['239'] = '&iuml;';
        entities['240'] = '&eth;';
        entities['241'] = '&ntilde;';        entities['242'] = '&ograve;';
        entities['243'] = '&oacute;';
        entities['244'] = '&ocirc;';
        entities['245'] = '&otilde;';
        entities['246'] = '&ouml;';        entities['247'] = '&divide;';
        entities['248'] = '&oslash;';
        entities['249'] = '&ugrave;';
        entities['250'] = '&uacute;';
        entities['251'] = '&ucirc;';        entities['252'] = '&uuml;';
        entities['253'] = '&yacute;';
        entities['254'] = '&thorn;';
        entities['255'] = '&yuml;';
    }
    if (useQuoteStyle !== 'ENT_NOQUOTES') {
        entities['34'] = '&quot;';
    }
    if (useQuoteStyle === 'ENT_QUOTES') {        entities['39'] = '&#39;';
    }
    entities['60'] = '&lt;';
    entities['62'] = '&gt;';

    // ascii decimals to real symbols
    for (decimal in entities) {
        if (entities.hasOwnProperty(decimal)) {
            hash_map[String.fromCharCode(decimal)] = entities[decimal];        }
    }

    return hash_map;
}

function stripslashes (str) {
    return (str+'').replace(/\\(.?)/g, function (s, n1) {
        switch (n1) {
            case '\\':
                return '\\';
            case '0':
                return '\0';
            case '':
                return '';
            default:
                return n1;
        }
    });
}

function addslashes (str) {
    return (str+'').replace(/([\\"'])/g, "\\$1").replace(/\u0000/g, "\\0");
}

/**
 * This function sets netsted properties to dom elements
 *
 * @Author Erik Amaru Ortiz <erik@colosa.com, aortiz.erik@gmail.com>
 * @Param (object) dom element refered by ID
 * @Param (array) nested array of properties strings
 * @Param (string) value to set
 * @Return <none>
 *
 * Example:
 * 		setNestedProperty(document, ['body','style','backgroundColor'], 'black');
 */
function setNestedProperty(obj, propertyName, propertyValue){
	var oTarget = obj;
	for (var i=0; i<propertyName.length; i++){
		if (i == (propertyName.length - 1)){
			oTarget[propertyName[i]] = propertyValue;
			return false;
		}
		oTarget = oTarget[propertyName[i]];
	}
}

/**
 * This function gets the user client browser and its version
 *
 * @Author Erik Amaru Ortiz <erik@colosa.com, aortiz.erik@gmail.com>
 * @Param <none>
 * @Return (objeect) {browser:sBrowser, version:sVersion}
 */
function getBrowserClient(){
  var aBrowFull = new Array("opera", "msie", "firefox", "opera", "safari");
  var sInfo = navigator.userAgent.toLowerCase();
  sBrowser = "";
  for (var i = 0; i < aBrowFull.length; i++){
    if ((sBrowser == "") && (sInfo.indexOf(aBrowFull[i]) != -1)){
      sBrowser = aBrowFull[i];
      sVersion = String(parseFloat(sInfo.substr(sInfo.indexOf(aBrowFull[i]) + aBrowFull[i].length + 1)));
      return {name:sBrowser, browser:sBrowser, version:sVersion}
    }
  }
  return false;
};
var _BROWSER = getBrowserClient();

/**
 * Create and send cookie
 *
 * @Author Erik Amaru Ortiz <erik@colosa.com, aortiz.erik@gmail.com>
 * @Param (string) cookie name
 * @Param (string) cookie value
 * @Param (int) cookie number of days for expires
 * @Return <none>
 */
function createCookie(name, value, days){
	if (days) {
		var date = new Date();
		date.setTime(date.getTime()+(days*24*60*60*1000));
		var expires = "; expires="+date.toGMTString();
	} else var expires = "";
	document.cookie = name+"="+value+expires+"; path=/";
}

/**
 * read a cookie
 *
 * @Author Erik Amaru Ortiz <erik@colosa.com, aortiz.erik@gmail.com>
 * @Param cookie name
 * @Return (string) cookie content
 */
function readCookie(name){
	var ca = document.cookie.split(';');
	var nameEQ = name + "=";
	for(var i=0; i < ca.length; i++) {
		var c = ca[i];
		while (c.charAt(0)==' ') c = c.substring(1, c.length); //delete spaces
		if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
	}
	return null;
}

/**
 * delete a cookie
 *
 * @Author Erik Amaru Ortiz <erik@colosa.com, aortiz.erik@gmail.com>
 * @Param cookie name
 * @Return <none>
 */
function eraseCookie(name){
	createCookie(name, "", -1);
}

/**
 * highlightRow a html element (setting background)
 *
 * @Author Erik Amaru Ortiz <erik@colosa.com, aortiz.erik@gmail.com>
 * @Param (object) html element
 * @Param (string) some color in hexadecimal format
 * @Return <none>
 */
function highlightRow(o, color){
	o.style.background = color
}

/**
 * left and right delete the blank characteres (String prototype)
 *
 * Example:
 *	var str = String("  some_string_with_spaces ");
 *  str.trim(); //clean the blank characteres
 *
 * @Author Erik Amaru Ortiz <erik@colosa.com, aortiz.erik@gmail.com>
 * @Param <none>
 * @Return <none>
 */
String.prototype.trim = function () {
    return this.replace(/^\s+|\s+$/g, "");
}

function clearCalendar(id){
	document.getElementById(id).value='';
	document.getElementById(id+'[div]').innerHTML = '';
	setTimeout('enableCalendar()', 350);
}

function lockCalendar(){
	G_CALENDAR_MEM_OFFSET = 'lock';

	//G_CALENDAR_CURRENT_OBJ.hide();
}

function enableCalendar(){
	G_CALENDAR_MEM_OFFSET = 'enable';
}

function parseDateFromMask (inputArray, mask){
	/* inputArray is an associative array with properties
	year, month, day, hour, minute 	*/

	/* format mask
	 * Y 	-> 2009
	 * y	-> 09
	 * m	-> 02
	 * d	-> 01
	 *
	 * h	-> 12
	 * i	-> 59
	 *
	 * d/m/y -> 01/02/09
	 * d/m/Y -> 01/02/2009
	 * Y-m-d -> 2009-02-01
	 *
	 * Y-m-d h:m -> 2009-02-01 12:59
	 *
	 */

	result = mask;
	result = result.replace("Y", inputArray.year);

	year = new String(inputArray.year);
	result = result.replace("y", year.substr(2,3));
	result = result.replace("m", inputArray.month);
	result = result.replace("d", inputArray.day);
	result = result.replace("h", inputArray.hour);
	result = result.replace("i", inputArray.minute);

	return result;

}

Array.prototype.walk = function( funcionaplicada ) {
    for(var i=0, parar=false; i<this.length && !parar; i++ )
        parar = funcionaplicada( this[i], i);
    return (this.length==i)? false : (i-1);
}

Array.prototype.find = function(q) {
    var dev = this.walk(function(elem) {
        if( elem==q )
            return true;
    } );
    if( this[dev]==q ) return dev;
    else return -1;
}

Array.prototype.drop = function(x) {
    this.splice(x,1);
}

Array.prototype.deleteByValue = function(val) {
    var eindex = this.find(val);
    this.drop(eindex);
}

/**
 * schedule a action js callback
 *
 * @Author Erik Amaru Ortiz <erik@colosa.com, aortiz.erik@gmail.com>
 * @Param (string) function name
 * @Param (int) time in seconds
 * @Return <none>
 */
function Timer(functionName, time) {
	setTimeout(functionName, time*1000);
}

function PMOS_TemporalMessage(timeToHide){
    fade('temporalMessageTD', 'inOut');
    if( typeof(timeToHide) != 'undefined' ) {
        Timer(
            function(){
                try{
                    document.getElementById('temporalMessageTD').style.display = 'none';
                }catch(e){}
            },
            timeToHide
        );
    }
}

/**
 * fast messagesbox
 *
 * @Param msg (string) : your message
 * @Param type (string): {alert|info|confirm}
 * @Param type (string):  xcallback, callback function name to execute after at user click on Accept
 * @Author <erik@colosa.com>
 */

function msgBox(msg, type, callbackAccept, callbackCancel){

	//setting default type
	type = typeof(type) != 'undefined'? type: 'info';

	//setting up the callback action
	acceptEv = typeof(callbackAccept) != 'undefined'? callbackAccept: false;
	cancelEv = typeof(callbackCancel) != 'undefined'? callbackCancel: false;

	switch(type){
		case 'alert':
		    new leimnud.module.app.alert().make({
			  	label: msg,
			   	width: 350,
			   	action:function(){
		    		if( acceptEv ){
		    			setTimeout(acceptEv, 1);
		    		}
		    	}.extend(this)
			 });
			break;
		case 'info':
			new leimnud.module.app.info().make({
			 	label: msg,
			   	width: 350,
			   	action:function(){
					if( acceptEv ){
		    			setTimeout(acceptEv, 1);
		    		}
				}.extend(this)
			});
			break;
		case 'confirm':
			if( cancelEv ){
				new leimnud.module.app.confirm().make({
			  	label: msg,
			    action: function(){
					  if( acceptEv ){
			    	  setTimeout(acceptEv, 0);
			    	}
					}.extend(this),
					cancel: function(){
						setTimeout(cancelEv, 1);
					}.extend(this)
			  });
			} else {
				new leimnud.module.app.confirm().make({
			  	label: msg,
			   	action: function(){
					  if( acceptEv ){
			  	    setTimeout(acceptEv, 1);
			   	  }
				  }.extend(this)
			  });
			}
			break;
	}

}


function executeEvent(id, ev){
	switch(ev){
		case 'click':
			document.getElementById(id).checked = true;
			if(document.getElementById(id).onclick){
				try{
					document.getElementById(id).onclick();
			    }catch(e){}
			}
			break;
	}
}

/*
 * @By Erik
 */
function getClientWindowSize() {
  var wSize = [1024, 768];
  if (typeof window.innerWidth != 'undefined'){
	wSize = [
      window.innerWidth,
      window.innerHeight
    ];
  } else if (typeof document.documentElement != 'undefined' && typeof document.documentElement.clientWidth != 'undefined' && document.documentElement.clientWidth != 0){
	wSize = [
      document.documentElement.clientWidth,
      document.documentElement.clientHeight
    ];
  } else {
     var body = document.getElementsByTagName('body');
     if(typeof body != 'undefined' && typeof body[0] != 'undefined' && typeof body[0].clientWidth != 'undefined') {
	wSize = [
          document.getElementsByTagName('body')[0].clientWidth,
          document.getElementsByTagName('body')[0].clientHeight
        ];
     }
  }
  return {width:wSize[0], height:wSize[1]};
}

function popUp(URL, width, height, left, top, resizable) {
    window.open(URL, '', 'toolbar=0,scrollbars=1,location=0,statusbar=0,menubar=1,resizable='+resizable+',width='+width+',height='+height+',left = '+left+',top = '+top+'');
}

/**
 * Alias for tedious large definition for ajax object - By Erik A.O. <erik@colosa.com>
 */
function XHRequest(){
	return new leimnud.module.rpc.xmlhttp;
}

function removeValue(id){
  if( document.getElementById('form['+id+']') )
    document.getElementById('form['+id+']').value = '';
  else if( document.getElementById(id) )
    document.getElementById(id).value = '';

  fireEvent(document.getElementById(id), 'change');
}

function datePicker4(obj, id, mask, startDate, endDate, showTIme, idIsoDate)
{
  __lastMask__ = mask;

  if (showTIme=='false') {
    showTIme = false;
  }

  Calendar.setup({
    inputField: id,
    dateFormat: mask,
    trigger: id + "[btn]",
    bottomBar: true,
    min:startDate,
    max:endDate,
    animation: _BROWSER.name =='msie'? false: true,
    showTime: showTIme,
    opacity: 1,
    onSelect: function() {
        this.hide();
        /* disabled temporarily by wrong functionality
        auxid     = id;
        idIsoDate = auxid.substring(0,auxid.length-1)+'_isodate]';
        var field= document.getElementById(idIsoDate);
        field.value=this.selection.print("%Y-%m-%d", ""); */
        fireEvent(document.getElementById(id), 'change');
    }
  });
}

function fireEvent(element, event)
{
  if (document.createEventObject){
    // dispatch for IE
    var evt = document.createEventObject();
    return element.fireEvent('on' + event,evt)
  }
  else {
    // dispatch for firefox + others
    var evt = document.createEvent("HTMLEvents");
    evt.initEvent(event, true, true ); // event type,bubbling,cancelable
    return !element.dispatchEvent(evt);
  }
}

function elementAttributesNS(e, ns)
{
  if (!this.__namespaceRegexps)
    this.__namespaceRegexps = {};
  var regexp = this.__namespaceRegexps[ns];
  if (!regexp) {
    this.__namespaceRegexps[ns] = regexp =
    ns ? eval("/^" + ns + ":(.+)/") : /^([^:]*)$/;
  }
  var result = {};
  var atts = e.attributes;
  var l = atts.length;
  for (var i = 0; i < l; i++) {
    var m = atts[i].name.match(regexp);
    if (m)
      result[m[1]] = atts[i].value;
  }
  return result;
}

/**
 * Translator function for internationalization
 */
function _()
{
  var argv = _.arguments;
  var argc = argv.length;

  if( typeof TRANSLATIONS != 'undefined' && TRANSLATIONS) {
    if( typeof TRANSLATIONS[argv[0]] != 'undefined' ) {
      if (argc > 1) {
        trn = TRANSLATIONS[argv[0]];
        for (i = 1; i < argv.length; i++) {
          trn = trn.replace('{'+(i-1)+'}', argv[i]);
        }
      }
      else {
        trn = TRANSLATIONS[argv[0]];
      }
    }
    else {
      trn = '**' + argv[0] + '**';
    }
  }
  else {
    PMExt.error('Processmaker JS Core Error', 'The TRANSLATIONS global object is not loaded!');
    trn = '';
  }
  return trn;
}

/**
 * String Replace function, if StrSearch has special characters "(", "[", must be escape "\\(", "\\[".
 */
function stringReplace(strSearch, stringReplace, str)
{
    var expression = eval("/" + strSearch + "/g");

    return str.replace(expression, stringReplace);
}

var mb_strlen = function(str) {
    str = str || '';
    var len = 0;
    for (var i = 0; i < str.length; i++) {
        len += str.charCodeAt(i) < 0 || str.charCodeAt(i) > 255 ? 2 : 1;
    }
    return len;
};

var stripNonNumeric = function (str) {
   str += ''; //force str to be a string
   var rgx = /^\d|\.|-$/;
   var out = '';
   for (var i = 0; i < str.length; i++) {
      if (rgx.test(str.charAt(i))) {
         if (!((str.charAt(i) == '.' && out.indexOf('.') != -1) || (str.charAt(i) == '-' && out.length != 0 ))) {
            out += str.charAt(i);
         }
      }
   }
   return out;
};