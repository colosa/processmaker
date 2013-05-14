var bFunctionAlreadyAssigned = false;
var aTaskFlag = [false,false,false,false,false,false];
var iLastTab   = -1;
var oTaskData  = {};

var re = /&/g;
var re2 = /@amp@/g;

var saveDataTaskTemporal = function(iForm)
{
  oAux = getField('TAS_UID');

  if (oAux)
  {
    switch (iLastTab)
    {
      case 1:
      case '1':
        oTaskData.TAS_TITLE       = getField('TAS_TITLE').value.replace(re, "@amp@");
        oTaskData.TAS_DESCRIPTION = getField('TAS_DESCRIPTION').value.replace(re, "@amp@");
        oTaskData.TAS_START       = (getField('TAS_START').checked ? 'TRUE' : 'FALSE');
        oTaskData.TAS_PRIORITY_VARIABLE = getField('TAS_PRIORITY_VARIABLE').value;
        oTaskData.TAS_DERIVATION_SCREEN_TPL = getField('TAS_DERIVATION_SCREEN_TPL').value;

        var fieldEval = new input(getField('TAS_TITLE'));
        if (getField('TAS_TITLE').value.trim() == '') {
          fieldEval.failed();
          new leimnud.module.app.alert().make( {
            label : _('ID_NAME_TAS_TITLE_REQUIRE')
          });
          return false;
        } else {
          fieldEval.passed();
        }
      break;
      case 2:
      case '2':
        var sw = 0;
        var msg = "";
        var fieldEval;

        if (getField("TAS_SELFSERVICE_TIMEOUT").checked) {
            sw = 1;

            fieldEval = new input(getField("TAS_SELFSERVICE_TIME"));

            if (getField("TAS_SELFSERVICE_TIME").value.trim() == "") {
                sw = 0;
                msg = msg + ((msg != "")? "<br />" : "") + _("ID_TIME_REQUIRED");

                fieldEval.failed();
            } else {
                fieldEval.passed();
            }

            fieldEval = new input(getField("TAS_SELFSERVICE_TRIGGER_UID"));

            if (getField("TAS_SELFSERVICE_TRIGGER_UID").value.trim() == "") {
                sw = 0;
                msg = msg + ((msg != "")? "<br />" : "") + _("ID_TRIGGER_REQUIRED");

                fieldEval.failed();
            } else {
                fieldEval.passed();
            }

            if (sw == 0) {
                new leimnud.module.app.alert().make({
                    label: msg
                });

                return false;
            }
        }

        if (getField('TAS_ASSIGN_TYPE][SELF_SERVICE').checked)
        {
          oTaskData.TAS_ASSIGN_TYPE = 'SELF_SERVICE';
        }
        if (getField("TAS_ASSIGN_TYPE][SELF_SERVICE_EVALUATE") && getField("TAS_ASSIGN_TYPE][SELF_SERVICE_EVALUATE").checked)
        {
          oTaskData.TAS_ASSIGN_TYPE = 'SELF_SERVICE_EVALUATE';
        }
        if (getField('TAS_ASSIGN_TYPE][REPORT_TO').checked)
        {
          oTaskData.TAS_ASSIGN_TYPE = 'REPORT_TO';
        }
        if (getField('TAS_ASSIGN_TYPE][BALANCED').checked)
        {
          oTaskData.TAS_ASSIGN_TYPE = 'BALANCED';
        }
        if (getField('TAS_ASSIGN_TYPE][MANUAL').checked)
        {
          oTaskData.TAS_ASSIGN_TYPE = 'MANUAL';
        }
        if (getField('TAS_ASSIGN_TYPE][EVALUATE').checked)
        {
          oTaskData.TAS_ASSIGN_TYPE = 'EVALUATE';
        }
        /* this feature is temporarily disabled
        if (getField('TAS_ASSIGN_TYPE][STATIC_MI').checked)
        {
          oTaskData.TAS_ASSIGN_TYPE = 'STATIC_MI';
        }
        if (getField('TAS_ASSIGN_TYPE][CANCEL_MI').checked)
        {
          oTaskData.TAS_ASSIGN_TYPE = 'CANCEL_MI';
        }*/
        oTaskData.TAS_ASSIGN_VARIABLE = getField('TAS_ASSIGN_VARIABLE').value;
        oTaskData.TAS_GROUP_VARIABLE = getField('TAS_GROUP_VARIABLE').value;
        /* this feature is temporarily disabled
        oTaskData.TAS_MI_INSTANCE_VARIABLE = getField('TAS_MI_INSTANCE_VARIABLE').value;
        oTaskData.TAS_MI_COMPLETE_VARIABLE = getField('TAS_MI_COMPLETE_VARIABLE').value;*/

        sw = ((oTaskData.TAS_ASSIGN_TYPE == "SELF_SERVICE" || oTaskData.TAS_ASSIGN_TYPE == "SELF_SERVICE_EVALUATE") && getField("TAS_SELFSERVICE_TIMEOUT").checked)? 1 : 0;

        oTaskData.TAS_SELFSERVICE_TIMEOUT = sw;
        oTaskData.TAS_SELFSERVICE_TIME = (sw == 1)? getField("TAS_SELFSERVICE_TIME").value : "";
        oTaskData.TAS_SELFSERVICE_TIME_UNIT = (sw == 1)? getField("TAS_SELFSERVICE_TIME_UNIT").value : "";
        oTaskData.TAS_SELFSERVICE_TRIGGER_UID = (sw == 1)? getField("TAS_SELFSERVICE_TRIGGER_UID").value : "";
        break;
      case 3:
      case '3':
        oTaskData.TAS_DURATION     = getField('TAS_DURATION').value;
        oTaskData.TAS_TIMEUNIT     = getField('TAS_TIMEUNIT').value;
        oTaskData.TAS_TYPE_DAY     = getField('TAS_TYPE_DAY').value;
        oTaskData.TAS_CALENDAR     = getField('TAS_CALENDAR').value;
        oTaskData.TAS_TRANSFER_FLY = (getField('TAS_TRANSFER_FLY').checked ? 'TRUE' : 'FALSE');

        var fieldEval = new input(getField('TAS_DURATION'));
        if (getField('TAS_DURATION').value.trim() == '') {
          fieldEval.failed();
          new leimnud.module.app.alert().make( {
            label : _('ID_TAS_DURATION_REQUIRE')
          });
          return false;
        } else {
          fieldEval.passed();
        }
      break;
      case 4:
      case '4':
      break;
      case 5:
      case '5':
        /*oTaskData.TAS_CAN_CANCEL                    = (getField('TAS_CAN_CANCEL').checked ? 'TRUE' : 'FALSE');
        oTaskData.TAS_CAN_PAUSE                     = (getField('TAS_CAN_PAUSE').checked ? 'TRUE' : 'FALSE');*/
        oTaskData.TAS_TYPE                          = (getField('TAS_TYPE').checked ? 'ADHOC' : 'NORMAL');
        /*oTaskData.TAS_CAN_SEND_MESSAGE              = (getField('TAS_CAN_SEND_MESSAGE').checked ? 'TRUE' : 'FALSE');
        oTaskData.TAS_CAN_UPLOAD                    = (getField('TAS_CAN_UPLOAD').checked ? 'TRUE' : 'FALSE');
        oTaskData.TAS_VIEW_UPLOAD                   = (getField('TAS_VIEW_UPLOAD').checked ? 'TRUE' : 'FALSE');
        oTaskData.TAS_VIEW_ADDITIONAL_DOCUMENTATION = (getField('TAS_VIEW_ADDITIONAL_DOCUMENTATION').checked ? 'TRUE' : 'FALSE');
        oTaskData.TAS_CAN_DELETE_DOCS               = getField('TAS_CAN_DELETE_DOCS').value;*/
      break;
      case 6:
      case '6':
        oTaskData.TAS_DEF_TITLE       = getField('TAS_DEF_TITLE').value;
        oTaskData.TAS_DEF_DESCRIPTION = getField('TAS_DEF_DESCRIPTION').value;
        //oTaskData.TAS_DEF_PROC_CODE   = getField('TAS_DEF_PROC_CODE').value;
        //oTaskData.SEND_EMAIL          = (getField('SEND_EMAIL').checked ? 'TRUE' : 'FALSE');
        //oTaskData.TAS_DEF_MESSAGE     = getField('TAS_DEF_MESSAGE').value;
      break;
      case 7:
      case '7':
        if ( getField('SEND_EMAIL') != null && (typeof (getField('SEND_EMAIL')) != 'undefined' ) ) {
          oTaskData.SEND_EMAIL                = getField('SEND_EMAIL').checked ? 'TRUE' : 'FALSE';
          oTaskData.TAS_DEF_MESSAGE_TYPE      = getField('TAS_DEF_MESSAGE_TYPE').value;
          oTaskData.TAS_DEF_MESSAGE           = getField('TAS_DEF_MESSAGE').value.replace(re, "@amp@");
          oTaskData.TAS_DEF_SUBJECT_MESSAGE   = getField('TAS_DEF_SUBJECT_MESSAGE').value;
          oTaskData.TAS_DEF_MESSAGE_TEMPLATE  = getField('TAS_DEF_MESSAGE_TEMPLATE').value;

          // validate fields TAS_DEF_SUBJECT_MESSAGE, TAS_DEF_MESSAGE
          if (getField('SEND_EMAIL').checked) {
            var fieldEval = new input(getField('TAS_DEF_SUBJECT_MESSAGE'));
            if (getField('TAS_DEF_SUBJECT_MESSAGE').value.trim() == '') {
              fieldEval.failed();
              new leimnud.module.app.alert().make( {
                label : G_STRINGS.ID_SUBJECT_FIELD_REQUIRED
              });
              return false;
            } else {
              fieldEval.passed();
            }
            switch (getField('TAS_DEF_MESSAGE_TYPE').value) {
              case 'text' :
                var vmesn = new input(getField('TAS_DEF_MESSAGE'));
                if (getField('TAS_DEF_MESSAGE').value.trim() == '' ) {
                  vmesn.failed();
                  new leimnud.module.app.alert().make( {
                    label : G_STRINGS.ID_MESSAGE_FIELD_REQUIRED
                  });
                  return false;
                } else {
                  vmesn.passed();
                }
                break;
              case 'template' :
                break;
            }
        }

        if(typeof getField('SEND_EMAIL') != 'undefined')
          oTaskData.SEND_EMAIL      = getField('SEND_EMAIL').checked ? 'TRUE' : 'FALSE';
        else
          oTaskData.SEND_EMAIL      = 'FALSE';
      }
      break;
    }
  }
  else
  {
    oTaskData = {};
  }
  iLastTab = iForm;
  return true;
};

var saveTaskData = function(oForm, iForm, iType)
{
  iLastTab = iForm;

  if (!saveDataTaskTemporal(iForm)) {
    return false;
  }

  oTaskData.TAS_UID = getField("TAS_UID").value;

  /*
  while (oTaskData.TAS_TITLE.charAt(0)==" "){
  oTaskData.TAS_TITLE = oTaskData.TAS_TITLE.substring(1,oTaskData.TAS_TITLE.length) ;
  }
  */

  oTaskData.TAS_TITLE = oTaskData.TAS_TITLE.trim();

  if (oTaskData.TAS_TITLE == "") {
    alert(G_STRINGS.ID_REQ_TITLE );

    return false;
  }

  //Set AJAX
  var sParameters = "function=saveTaskData";

  var oRPC = new leimnud.module.rpc.xmlhttp({
    url: "../tasks/tasks_Ajax",
    method: "POST",
    args: sParameters + "&oData=" + oTaskData.toJSONString()
  });

  oRPC.callback = function (rpc) {
    var res = rpc.xmlhttp.responseText.parseJSON();

    if (oTaskData.TAS_TITLE) {
      Pm.data.db.task[getField("INDEX").value].label = Pm.data.db.task[getField("INDEX").value].object.elements.label.innerHTML = htmlentities(oTaskData.TAS_TITLE, 'ENT_QUOTES');
    }

    if (oTaskData.TAS_START) {
      oTaskData.TAS_START = ((oTaskData.TAS_START == "TRUE")? true : false);
      Pm.data.render.setTaskINI({task: oTaskData.TAS_UID, value: oTaskData.TAS_START});
    }

    try {
      var option = {
        label: changesSavedLabel
      }

      switch (res.status) {
          case "CRONCL":
              option = {
                label: changesSavedLabel + "<br /><br />" + _("APP_TITLE_CASE_LABEL_UPDATE"),
                width: 350,
                height: 175
              }
              break;
      }

      new leimnud.module.app.info().make(option);
    }
    catch (e) {
      //No show confirmation
    }

    Pm.tmp.propertiesPanel.remove();
  }.extend(this);

  oRPC.make();
};

var showTriggers = function(sStep, sType)
{
  var idAux = "";
  var sStepAux = sStep;

  var oRPC = new leimnud.module.rpc.xmlhttp({
    url   : "../steps/steps_Ajax",
    async : false,
    method: "POST",
    args  : "action=showTriggers&sProcess=" + Pm.options.uid + "&sStep=" + sStep + "&sType=" + sType
  });

  oRPC.make();
  document.getElementById("triggersSpan_" + sStep + "_" + sType).innerHTML = oRPC.xmlhttp.responseText;
  scs = oRPC.xmlhttp.responseText.extractScript();
  scs.evalScript();
  var tri = document.getElementById("TRIG_"+sStep+"_" + sType);

  if (tri) {
  	oRPC = new leimnud.module.rpc.xmlhttp({
      url   : "../steps/steps_Ajax",
      async : false,
      method: "POST",
      args  : "action=counterTriggers&sStep="+sStep+"&sType="+sType
    });

    oRPC.make();
    aAux = oRPC.xmlhttp.responseText.split("|");
    tri.innerHTML = aAux[1];
    idAux = (sStepAux.charAt(0) != "-")? "TRIG_" + sStepAux : "TRIG_";
    var tri = document.getElementById(idAux);

    if (tri) {
    	tri.innerHTML = aAux[0];
    }
  }
};

var editTriggerCondition = function(sStep, sTrigger, sType)
{
  popupWindow('', '../steps/steps_Ajax?action=editTriggerCondition&sStep=' + sStep + '&sTrigger=' + sTrigger + '&sType=' + sType, 500, 220);
};

var upTrigger = function(sStep, sTrigger, sType, iPosition)
{
  var oRPC = new leimnud.module.rpc.xmlhttp({
    url   : '../steps/steps_Ajax',
    async : false,
    method: 'POST',
    args  : 'action=upTrigger&sStep=' + sStep + '&sTrigger=' + sTrigger + '&sType=' + sType + '&iPosition=' + iPosition
  });
  oRPC.make();
  showTriggers(sStep, sType);
};

var downTrigger = function(sStep, sTrigger, sType, iPosition)
{
  var oRPC = new leimnud.module.rpc.xmlhttp({
    url   : '../steps/steps_Ajax',
    async : false,
    method: 'POST',
    args  : 'action=downTrigger&sStep=' + sStep + '&sTrigger=' + sTrigger + '&sType=' + sType + '&iPosition=' + iPosition
  });
  oRPC.make();
  showTriggers(sStep, sType);
};

var availableTriggers = function(sStep, sType)
{
  popupWindow('', '../steps/steps_Ajax?action=availableTriggers&sProcess=' + Pm.options.uid + '&sStep=' + sStep + '&sType=' + sType, 500, 250);
};

var ofToAssignTrigger = function(sStep, sTrigger, sType, iPosition)
{
	new leimnud.module.app.confirm().make({
    label:G_STRINGS.ID_MSG_CONFIRM_REMOVE_TRIGGER,
    action:function()
    {
      var oRPC = new leimnud.module.rpc.xmlhttp({
        url   : '../steps/steps_Ajax',
        async : false,
        method: 'POST',
        args  : 'action=ofToAssignTrigger&sStep=' + sStep + '&sTrigger=' + sTrigger + '&sType=' + sType + '&iPosition=' + iPosition
      });
      oRPC.make();
      showTriggers(sStep, sType);
    }.extend(this)
  });
};

