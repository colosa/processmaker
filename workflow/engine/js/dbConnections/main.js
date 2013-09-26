/*
 * Data base connection javascript routines
 * @Author Erik Amatu Ortiz <erik@colosa.com>
 * @Update date May 20th, 2009
 */

var PROCESS_REQUEST_FILE = '../dbConnections/dbConnectionsAjax';

String.prototype.trim = function () {
    return this.replace(/^\s+|\s+$/g, "");
}

var oPanel;
function newDbConnection() {
	oPanel = new leimnud.module.panel();
	oPanel.options = {
		limit :true,
	  	size	:{w:450,h:380},
	  	position:{x:0,y:0,center:true},
	  	title	:G_STRINGS.ID_DBS_NEW,
	  	theme	:"processmaker",
	  	statusBar:false,
	  	control	:{resize:false,roll:false,drag:true},
	  	fx	:{modal:true,opacity:true,blinkToFront:false,fadeIn:false,drag:true}
  	};
  	oPanel.events = {
  		remove: function() { delete(oPanel); }.extend(this)
  	};
	oPanel.make();
	oPanel.loader.show();
	var oRPC = new leimnud.module.rpc.xmlhttp({
	  	url : PROCESS_REQUEST_FILE,
	  	args: 'action=newDdConnection'
  	});
  	oRPC.callback = function(rpc) {
	  	oPanel.loader.hide();
	  	var scs=rpc.xmlhttp.responseText.extractScript();
	  	oPanel.addContent(rpc.xmlhttp.responseText);
	  	scs.evalScript();
	  	$('form[CREATE]').disabled = true;
  	}.extend(this);
	oRPC.make();
};

var saveDBConnection = function() {
	if( getField('DBS_PORT').value.trim() == '' || getField('DBS_PORT').value.trim() == '0' ) {
		onChangeType();
	}

	var type 	= $('form[DBS_TYPE]').value;
	var server	= $('form[DBS_SERVER]').value;
	var db_name = $('form[DBS_DATABASE_NAME]').value;
	var user 	= $('form[DBS_USERNAME]').value;
	var passwd 	= $('form[DBS_PASSWORD]').value;
	var port 	= $('form[DBS_PORT]').value;
	var desc 	= $('form[DBS_DESCRIPTION]').value;
	var enc 	= $('form[DBS_ENCODE]').value;

  	var uri = 'action=saveConnection&type='+type+'&server='+server+'&db_name='+db_name+'&user='+user+'&passwd='+passwd+'&port='+port+'&desc='+desc+'&enc='+enc;

  	var oRPC = new leimnud.module.rpc.xmlhttp({
  		url : PROCESS_REQUEST_FILE,
  		args: uri
  	});

  	oRPC.callback = function(rpc){
	  	oPanel.clearContent();
	  	oPanel.addContent(rpc.xmlhttp.responseText);

	  	var oRPC = new leimnud.module.rpc.xmlhttp({
		    url   : PROCESS_REQUEST_FILE,
		    async : false,
		    method: 'POST',
		    args  : 'action=showConnections'
	  	});
		oRPC.make();
		mainPanel.clearContent();
		mainPanel.addContent(oRPC.xmlhttp.responseText);
		oPanel.remove();
	}.extend(this);
	oRPC.make();
};

function saveEditDBConnection()
{
	if( getField('DBS_PORT').value.trim() == '' || getField('DBS_PORT').value.trim() == '0' ) {
		onChangeType();
	}

	var dbs_uid = currentEditDBS_UID;
	var type 	= $('form[DBS_TYPE]').value;
	var server	= $('form[DBS_SERVER]').value;
	var db_name = $('form[DBS_DATABASE_NAME]').value;
	var user 	= $('form[DBS_USERNAME]').value;
	var passwd 	= $('form[DBS_PASSWORD]').value;
	var port 	= $('form[DBS_PORT]').value;
	var desc 	= $('form[DBS_DESCRIPTION]').value;
	var enc 	= $('form[DBS_ENCODE]').value;

  	var uri = 'action=saveEditConnection&type='+type+'&server='+server+'&db_name='+db_name+'&user='+user+'&passwd='+passwd+'&port='+port+'&dbs_uid='+dbs_uid+'&desc='+desc+'&enc='+enc;

  	var oRPC = new leimnud.module.rpc.xmlhttp({
  		url : PROCESS_REQUEST_FILE,
  		args: uri
  	});

  	oRPC.callback = function(rpc){
	  	oPanel.clearContent();
	  	oPanel.addContent(rpc.xmlhttp.responseText);

	  	var oRPC = new leimnud.module.rpc.xmlhttp({
		    url   : PROCESS_REQUEST_FILE,
		    async : false,
		    method: 'POST',
		    args  : 'action=showConnections'
	  	});
		oRPC.make();
		mainPanel.clearContent();
		mainPanel.addContent(oRPC.xmlhttp.responseText);
		oPanel.remove();
	}.extend(this);
	oRPC.make();
}

var currentEditDBS_UID;

function editDbConnection(DBS_UID)
{
	currentEditDBS_UID = DBS_UID;

	oPanel = new leimnud.module.panel();
	oPanel.options = {
		limit :true,
		size	:{w:450,h:380},
	  	position:{x:0,y:0,center:true},
	  	title	:G_STRINGS.ID_DBS_EDIT,
	  	theme	:"processmaker",
	  	statusBar:false,
	  	control	:{resize:false,roll:false,drag:false},
	  	fx	:{modal:true,opacity:true,blinkToFront:false,fadeIn:false,drag:false}
  	};
  	oPanel.events = {
  		remove: function() { delete(oPanel); }.extend(this)
  	};
	oPanel.make();
	oPanel.loader.show();
	var oRPC = new leimnud.module.rpc.xmlhttp({
	  	url : PROCESS_REQUEST_FILE,
	  	args: 'action=editDdConnection&DBS_UID=' + DBS_UID
  	});
  	oRPC.callback = function(rpc) {
	  	oPanel.loader.hide();
	  	var scs=rpc.xmlhttp.responseText.extractScript();
	  	oPanel.addContent(rpc.xmlhttp.responseText);
	  	scs.evalScript();
	  	$('form[CREATE]').disabled = true;
  	}.extend(this);
	oRPC.make();
}

function deleteDbConnection(DBS_UID,PRO_UID)
{

   isokDependent = ajax_function(PROCESS_REQUEST_FILE,'loadInfoAssigConnecctionDB','DBS_UID='+DBS_UID+'&PRO_UID='+PRO_UID,'POST');

   if(!isokDependent){
	  msgBox(G_STRINGS.ID_DB_CONNECTION_ASSIGN,"alert");
	  return;
   }
  	new leimnud.module.app.confirm().make({
  		label:G_STRINGS.ID_MSG_CONFIRM_REMOVE_DBS,
    	action:function(){

			var uri = 'action=deleteDbConnection&dbs_uid='+DBS_UID;

		  	var oRPC = new leimnud.module.rpc.xmlhttp({
		  		url : PROCESS_REQUEST_FILE,
		  		args: uri
		  	});

		  	oRPC.callback = function(rpc){

			  	var oRPC = new leimnud.module.rpc.xmlhttp({
				    url   : PROCESS_REQUEST_FILE,
				    async : false,
				    method: 'POST',
				    args  : 'action=showConnections'
			  	});
				oRPC.make();
				mainPanel.clearContent();
				mainPanel.addContent(oRPC.xmlhttp.responseText);

			}.extend(this);
			oRPC.make();

		}.extend(this)
  	});
}

function $(id){
	return document.getElementById(id);
}

function AJAX()
{
	try	{
		xmlhttp = new XMLHttpRequest();
	}
	catch(generic_error) {
		try {
			xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
		} catch (microsoft_old_error) {
			try {
				xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
			} catch (microsoft_error) {
				xmlhttp = false;
			}
		}
	}
	return xmlhttp;
}

var currentPopupWindow;

function testDBConnection()
{
	if(validateFields()) {
		var type = $('form[DBS_TYPE]').value;
		var server= $('form[DBS_SERVER]').value;
		var db_name = $('form[DBS_DATABASE_NAME]').value;
		var user = $('form[DBS_USERNAME]').value;
		var passwd = $('form[DBS_PASSWORD]').value;
		var port = $('form[DBS_PORT]').value;
		if(port.trim() == ''){
			port = 'default';
		}
		var myPanel = new leimnud.module.panel();
		currentPopupWindow = myPanel;
		myPanel.options = {
			limit :true,
			size:{w:500,h:400},
			position:{center:true},
			title: G_STRINGS.DBCONNECTIONS_TEST,
			theme: "processmaker",
			control: { close: false, roll: false, drag: true, resize: false},
			fx: {
				shadow	:true,
				blinkToFront:true,
				opacity	:true,
				drag:true,
				modal: true
			}
		};

		myPanel.make();
		myPanel.loader.show();

		var requestfile = PROCESS_REQUEST_FILE;
	    var uri = 'action=showTestConnection&type='+type+'&server='+server+'&db_name='+db_name+'&user='+user+'&passwd='+passwd+'&port='+port;

		var ajax = AJAX();
		ajax.open("POST", requestfile, true);
		ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded;");
		ajax.onreadystatechange = function() {
			if(ajax.readyState == 4) {
				currentPopupWindow.clearContent();
				currentPopupWindow.addContent(ajax.responseText);
				myPanel.command(myPanel.loader.hide);
				testHost(1);
			}
		}
		ajax.send(uri);
	}
}

var resultset = true;
var mainRequest;
mainRequestAbort=false;
function testHost(step)
{
	mainRequestAbort = false;
	$("test_"+step).style.display = "block";

	var type = $('form[DBS_TYPE]').value;
	var server= $('form[DBS_SERVER]').value;
	var db_name = $('form[DBS_DATABASE_NAME]').value;
	var user = $('form[DBS_USERNAME]').value;

	if($('form[DBS_PASSWORD]').value != '') {
		var passwd = $('form[DBS_PASSWORD]').value;
	} else {
		var passwd = 'none';
	}

	if($('form[DBS_PORT]').value.trim() != '') {
		var port = $('form[DBS_PORT]').value;
	} else {
		var port = 'none';
	}

	var requestfile = PROCESS_REQUEST_FILE;
	var uri = 'action=testConnection&step='+step+'&type='+type+'&server='+server+'&db_name='+db_name+'&user='+user+'&port='+port+'&passwd='+passwd;

	var ajax = AJAX();
	mainRequest = ajax;
	ajax.open("POST", requestfile, true);
	ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded;");
	ajax.onreadystatechange = function() {
		if(ajax.readyState == 4) {
			try{
				response = ajax.responseText.trim();
				oresp = response.split(',');
				result = oresp[0].trim();
				msg = oresp[1].trim();

				if( result == 'SUCCESSFULL' ) {
					$('status_'+step).innerHTML = '';
					if(msg != '') {
						$('status_'+step).innerHTML = '<img src="/images/row_down.png" width="15" height="11" align="left" border="0"/><b>'+G_STRINGS.DBCONNECTIONS_MSGR+'<font color=#000>'+msg+'</b></font><br/>';
					}
					$('status_'+step).innerHTML += '<img src="/images/ok.png" width="13" height="13" align="left" border="0"/><b>'+G_STRINGS.DBCONNECTIONS_MSGT+' <font color="#749AF9">'+G_STRINGS.DBCONNECTIONS_MSGS+'</b></font>';
				}
				else {
					if( result == 'FAILED' ) {
						$('status_'+step).innerHTML = '<img src="/images/alert.gif" width="12" height="12" align="left" border="0"/><b>'+G_STRINGS.DBCONNECTIONS_MSGR+' <font color=red>'+msg+'</b></font><br/>';
						$('status_'+step).innerHTML += '<img src="/images/cross.gif" width="12" height="12" align="left" border="0"/><b>'+G_STRINGS.DBCONNECTIONS_MSGT+' <font color=red>'+G_STRINGS.DBCONNECTIONS_MSG2+'</b></font>';
						resultset = false;
					} else {
						alert(response);
						return;
					}
				}
				step += 1;
				if(!mainRequestAbort) {
					testHost(step);
				} else {
					return;
				}
			} catch (e) {
				if(resultset){
					$('form[CREATE]').disabled = false;
				}
				else {
					resultset = true;
					$('form[CREATE]').disabled = true;
				}
				$('bnt_abort').style.display = 'none';
				$('bnt_ok').style.display = 'block';
				return;
			};
		} else {
			var html = "<img src='/images/activityanimation.gif'><br/><center>"+G_STRINGS.DBCONNECTIONS_MSG3+"....</center>"; //
		    $('status_'+step).innerHTML = html;
		}
	}
	ajax.send(uri);
}

function abortTestConnection()
{
	mainRequestAbort = true;
	mainRequest.abort();
	currentPopupWindow.clearContent();
	currentPopupWindow.addContent("<br/><br/><center><font color=red><b>"+G_STRINGS.DBCONNECTIONS_MSGA+"</b></font></center>");
	setTimeout("currentPopupWindow.remove()",2000);
}

function cancelTestConnection()
{
	currentPopupWindow.remove();
}



function validateFields()
{
	if( getField('DBS_PORT').value.trim() == '' || getField('DBS_PORT').value.trim() == '0' ) {
		onChangeType();
	}

	var res = true;
	var o = new input(getField('DBS_SERVER'));
	if($('form[DBS_SERVER]').value == '') {
		//new leimnud.module.app.alert().make({label: G_STRINGS.DBCONNECTIONS_MSG4});
		o.failed();
		res = false;
	} else
		o.passed();

	var o = new input(getField('DBS_DATABASE_NAME'));
	if($('form[DBS_DATABASE_NAME]').value == '') {
		//new leimnud.module.app.alert().make({label: G_STRINGS.DBCONNECTIONS_MSG5});
		o.failed();
		res = false;
	} else
		o.passed();

	var o = new input(getField('DBS_USERNAME'));
	if($('form[DBS_USERNAME]').value == '') {
		//new leimnud.module.app.alert().make({label: G_STRINGS.DBCONNECTIONS_MSG6});
		o.failed();
		res = false;
	} else
		o.passed();

	/*var o = new input(getField('DBS_PORT'));
	if($('form[DBS_PORT]').value == '') {
		o.failed();
		res = false;
	} else
		o.passed();*/

	var o = new input(getField('DBS_TYPE'));
	if($('form[DBS_TYPE]').value == '0') {
		o.failed();
		res = false;
	} else
		o.passed();

	oType = getField('DBS_TYPE');
	if( oType.value != 'mssql' && oType.value != 'oracle' ){
		var o = new input(getField('DBS_ENCODE'));
		if($('form[DBS_ENCODE]').value == '0') {
			o.failed();
			res = false;
		} else
			o.passed();
	}


	if(!res){
		new leimnud.module.app.alert().make({label: G_STRINGS.DBCONNECTIONS_ALERT});
	}
	return res;
}

var onChangeType = function() {
    var oAux = getField('DBS_PORT');

	switch(getField('DBS_TYPE').value) {
		case 'mysql':
		    oAux.value = '3306';
		break;
		case 'pgsql':
		    oAux.value = '5432';
		break;
		case 'mssql':
		    oAux.value = '1433';
		break;
		case 'oracle':
		    oAux.value = '1521';
		break;
		default:
			oAux.value = '';
	}

};

function showEncodes(pre){
	oType = getField('DBS_TYPE');
	//if( oType.value != 'mssql' && oType.value != 'oracle' ){
	if( oType.value != 'oracle' ){
		showRowById('DBS_ENCODE');
		var o = new input(getField('DBS_TYPE'));
		if($('form[DBS_TYPE]').value == '0') {
			o.failed();
			res = false;
		} else
			o.passed();

		var o = getField('DBS_TYPE');

		var oRPC = new leimnud.module.rpc.xmlhttp({
	  		url : PROCESS_REQUEST_FILE,
	  		args: 'action=showEncodes&engine='+o.value
	  	});

	  	oRPC.callback = function(rpc){
	  		var oEnc = getField('DBS_ENCODE');
			response = eval('{'+oRPC.xmlhttp.responseText+'}');

			oEnc.options.length = 0;
			//set news
			for(i=0; i<response.length; i++){
				var newOption = new Option(response[i][1], response[i][0]);
				oEnc.options[i] = newOption;
			}

			if(pre != null)
				oEnc.value = pre;


			var o = new input(getField('DBS_ENCODE'));
			if($('form[DBS_ENCODE]').value == '0') {
				o.failed();
				res = false;
			} else
				o.passed();
		}.extend(this);
		oRPC.make();
	} else {
		hideRowById('DBS_ENCODE');
	}
}
