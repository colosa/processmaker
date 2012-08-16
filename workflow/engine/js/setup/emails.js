/**
 * @author erik <erik@colosa.com>
 */

var PROCESS_REQUEST_FILE = '../setup/emails_Ajax';
var oPanel;

function verifyFields(oForm) {
	switch (getField('MESS_ENGINE').value) {
    case 'PHPMAILER':
      verifyPassword = 0;
      oAuxS = $('form[MESS_SERVER]').value.trim();
      if (oAuxS == '') {
        new leimnud.module.app.alert().make({
          label : G_STRINGS.ID_SERVER_REQUIRED
        });
        return false;
      } else {
        oAuxA = $('form[MESS_ACCOUNT]').value;
        if (oAuxA == '') {
          new leimnud.module.app.alert().make({
            label : G_STRINGS.ID_MESS_ACCOUNT_REQUIRED
          });
          return false;
        } else {
          if ($('form[MESS_RAUTH]').checked) {
            oAuxP = $('form[MESS_PASSWORD]').value;
            oAuxPH = $('form[MESS_PASSWORD_HIDDEN]').value;
            if ((oAuxP == '')&&(oAuxPH == '')) {
              new leimnud.module.app.alert().make({
                label : G_STRINGS.ID_PASSWORD_REQUIRED
              });
              return false;
            } else {
              verifyPassword = 1;
            }
          } else {
            verifyPassword = 1;
          }
          if (verifyPassword == 1) {
            if ($('form[MESS_TEST_MAIL]').checked) {
              oAuxE = $('form[MESS_TEST_MAIL_TO]').value;
              if (oAuxE == '') {
                new leimnud.module.app.alert().make({
                  label : G_STRINGS.ID_EMAIL_REQUIRED
                });
                return false;
              } else {
                testConnection();
              }
            } else {
              testConnection();
            }
          }
        }
      }
      break;
    case 'MAIL':
      if ($('form[MESS_TEST_MAIL]').checked) {
        oAuxE = $('form[MESS_TEST_MAIL_TO]').value.trim();
        if (oAuxE == '') {
          new leimnud.module.app.alert().make({
            label : G_STRINGS.ID_EMAIL_REQUIRED
          });
          return false;
        } else {
          testConnectionMail();
        }
      } else {
        testConnectionMail();
      }
      break;
	}
}

function testConnection() {
	resultset = true;
	params  = 'srv=' + getField('MESS_SERVER').value.trim();
	params += '&port='+ ((getField('MESS_PORT').value.trim() != '') ? getField('MESS_PORT').value : 'default');
	params += '&account=' + getField('MESS_ACCOUNT').value;
	if (getField('MESS_PASSWORD_HIDDEN').value =='') {
	  params += '&passwd=' + getField('MESS_PASSWORD').value;
	}
	else {
	  params += '&passwd=' + getField('MESS_PASSWORD_HIDDEN').value;
	}
	params += '&auth_required='+ (getField('MESS_RAUTH').checked ? 'yes' : 'no');
	params += '&send_test_mail='+ (getField('MESS_TEST_MAIL').checked ? 'yes' : 'no');
	params += '&mail_to=' + $('form[MESS_TEST_MAIL_TO]').value;

	if(getField('SMTPSecure][ssl').checked) {
	  params +='&SMTPSecure=ssl';
	} else if(getField('SMTPSecure][tls').checked) {
	  params +='&SMTPSecure=tls';
	} else {
	  params +='&SMTPSecure=';
	}

	oPanel = new leimnud.module.panel();
	oPanel.options = {
		size : {w : 590, h : 350},
		position : {x : 0, y : 0, center : true},
		title : 'SMTP Server Connection',
		theme : "processmaker",
		statusBar : false,
		control : {resize : false, roll : false, drag : true},
		fx : {modal : true, opacity : true, blinkToFront : false, fadeIn : false, drag : true}
	};
	oPanel.events = {
		remove : function() {
			delete (oPanel);
		}.extend(this)
	};

	oPanel.make();
	oPanel.loader.show();

	var oRPC = new leimnud.module.rpc.xmlhttp({
		url : PROCESS_REQUEST_FILE,
		args : 'request=init&' + params
	});
	oRPC.callback = function(rpc) {
		oPanel.loader.hide();
		oPanel.addContent(rpc.xmlhttp.responseText);
		testSMTPHost(1, params); // execution de init test
	}.extend(this);
	oRPC.make();
};

function testConnectionMail() {
	resultset = true;
	if ($('form[MESS_TEST_MAIL]').checked) {
		send_test_mail = 'yes';
		mail_to = $('form[MESS_TEST_MAIL_TO]').value.trim();
		var uri = 'send_test_mail=' + send_test_mail + '&mail_to=' + mail_to;
		var oRPC = new leimnud.module.rpc.xmlhttp({
			url : PROCESS_REQUEST_FILE,
			args : 'request=mailTestMail_Show' + '&' + uri
		});
		oRPC.callback = function(rpc) {
			oresp = rpc.xmlhttp.responseText.split(',');
			result = oresp[0].trim();
			if (typeof oresp[1] == "undefined") {
				msg = '';
				result = 'INVALID';
			} else {
				msg = oresp[1].trim();
			}
			if (result == 'SUCCESSFUL') {
				if (msg != '') {
				}
				$('form[SAVE_CHANGES]').disabled = false;
			} else {
				if (result == 'FAILED') {
					alert(G_STRINGS.ID_MAIL_FAILED);
				}
				if (result == 'INVALID') {
					alert(G_STRINGS.ID_INVALID_EMAIL);
				}
			}
		}.extend(this);
		oRPC.make();
	} else {
		alert(G_STRINGS.ID_CHECK_REQUIRED);
	}
};

var resultset = true;
function testSMTPHost(step, params) {
	$("test_" + step).style.display = "block";

	var requestfile = PROCESS_REQUEST_FILE;
	var uri = 'request=testConnection&step=' + step +'&'+ params;

	var ajax = AJAX();
	ajax.open("POST", requestfile, true);
	ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded;");
	ajax.onreadystatechange = function() {
		if (ajax.readyState == 4) {
			try {
				response = ajax.responseText.trim();
				oresp = response.split(',');
				result = oresp[0].trim();
				msg = oresp[1].trim();
				if (result == 'SUCCESSFUL') {
					$('status_' + step).innerHTML = '';
					if (msg != '') {
						$('status2_' + step).innerHTML = '<img src="/images/row_down.png" width="15" height="11" align="left" border="0"/>'+ msg + '</b></font><br/>';
					}
					$('status_' + step).innerHTML += '<img src="/images/ok.png" width="13" height="13" align="left" border="0"/>';
				} else {
					$('status_' + step).innerHTML = '';
					if (result == 'FAILED') {
						$('status2_' + step).innerHTML = '<img src="/images/alert.gif" width="12" height="12" align="left" border="0"/><font color=red>' + msg + '</font><br/>';
						$('status_' + step).innerHTML += '<img src="/images/cross.gif" width="12" height="12" align="left" border="0"/>';
						resultset = false;
					} else {
						setTimeout(response);
					}
				}
				step += 1;
				testSMTPHost(step, params);
			} catch (e) {
				if (resultset) {
					$('form[SAVE_CHANGES]').disabled = false;
				} else {
					$('form[SAVE_CHANGES]').disabled = true;
				}
				$('bnt_ok').style.display = 'block';
				return;
			};
		} else {
			$('status_' + step).innerHTML = "<img src='/images/ajax-loader.gif' width=12 height=12 border=0><br/>";
		}
	}
	ajax.send(uri);
}

function cancelTestConnection() {
	oPanel.remove();
	resultset = true;
}

// /************* Adds routines *************///
String.prototype.trim = function () {
    return this.replace(/^\s+|\s+$/g, "");
}

function $(id) {
	return document.getElementById(id);
}
function AJAX() {
	try {
		xmlhttp = new XMLHttpRequest();
	} catch (generic_error) {
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

function initSet() {
	// $('form[MESS_RAUTH]').checked = true;
	hideRowById('MESS_TEST_MAIL_TO');
	if (!($('form[MESS_ENABLED]').checked)) {
		hideRowById('MESS_ENGINE');
		hideRowById('MESS_SERVER');
		hideRowById('MESS_PORT');
		hideRowById('MESS_ACCOUNT');
		hideRowById('MESS_PASSWORD');
		hideRowById('MESS_BACKGROUND');
		hideRowById('MESS_EXECUTE_EVERY');
		hideRowById('MESS_SEND_MAX');
		hideRowById('MESS_TRY_SEND_INMEDIATLY');
		hideRowById('MESS_RAUTH');
		hideRowById('MESS_TEST_MAIL');
		hideRowById('MESS_TEST_MAIL_TO');
		hideRowById('TEST');
		hideRowById('SMTPSecure');
		hideRowById('SAVE_CHANGES');
		$('form[SAVE_CHANGES]').disabled = false;
	} else {
		hideRowById('SAVE_CHANGES2');
		if (getField('MESS_ENGINE').value == 'MAIL') {
			hideRowById('MESS_RAUTH');
			hideRowById('MESS_TEST_MAIL_TO');
			showRowById('TEST');
			showRowById('SAVE_CHANGES');
		}
	}
}

var verifyData = function(oForm) {

	if (getField('MESS_ENABLED').checked) {
    if (getField('MESS_RAUTH').checked) {
      if (getField('MESS_PASSWORD') == '') {
    	  getField('MESS_PASSWORD').value = getField('MESS_PASSWORD_HIDDEN').value;
      }
    }
		switch (getField('MESS_ENGINE').value) {
		case 'PHPMAILER':
		case 'OPENMAIL':
			oAux = getField('MESS_SERVER');
			if (oAux.value.trim() == '') {
				alert(G_STRINGS.ID_MESS_SERVER_REQUIRED);
				oAux.focus();
				return;
			}
			break;
		}
		if (getField('MESS_BACKGROUND').checked) {
			oAux = getField('MESS_EXECUTE_EVERY');
			if (oAux.value == '') {
				alert(G_STRINGS.ID_MESS_EXECUTE_EVERY_REQUIRED);
				oAux.focus();
				return;
			}
			oAux = getField('MESS_SEND_MAX');
			if (oAux.value == '') {
				alert(G_STRINGS.ID_MESS_SEND_MAX_REQUIRED);
				oAux.focus();
				return;
			}
		}
	}
	oForm.submit();
};

var oPanel;

var testEmailConfiguration = function() {
	if (getField('MESS_ENGINE').value != 'MAIL') {
		oAux = getField('MESS_SERVER');
		if (oAux.value.trim() == '') {
			alert(G_STRINGS.ID_MESS_SERVER_REQUIRED);
			oAux.focus();
			return;
		}
	}
	oPanel = new leimnud.module.panel();
	oPanel.options = {
		size : {
			w : 400,
			h : 200
		},
		position : {
			x : 0,
			y : 0,
			center : true
		},
		title : "",
		theme : "processmaker",
		statusBar : false,
		control : {
			resize : false,
			roll : false,
			drag : false
		},
		fx : {
			modal : true,
			opacity : true,
			blinkToFront : false,
			fadeIn : false,
			drag : false
		}
	};
	oPanel.events = {
		remove : function() {
			delete (oPanel);
		}.extend(this)
	};
	oPanel.make();
	oPanel.loader.show();
	var oRPC = new leimnud.module.rpc.xmlhttp({
		url : 'emails_Ajax',
		args : 'action=testEmailConfiguration&usermail=' + account
	});
	oRPC.callback = function(rpc) {
		oPanel.loader.hide();
		oPanel.addContent(rpc.xmlhttp.responseText);
		var scs = rpc.xmlhttp.responseText.extractScript();
		scs.evalScript();
	}.extend(this);
	oRPC.make();
};

var closeTestPanel = function() {
	oPanel.remove();
};
