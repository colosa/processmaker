var grdNumRows = 0; //
var grdRowLabel = []; //
var fieldGridGral = '';
var fieldGridGralVal = '';




Ext.ns("Ext.ux.renderer", "Ext.ux.grid");

Ext.ux.grid.ComboColumn = Ext.extend(Ext.grid.Column, {
  //@cfg {String} gridId
  //The id of the grid this column is in. This is required to be able to refresh the view once the combo store has loaded

  gridId: undefined,

  constructor: function (cfg) {
    Ext.ux.grid.ComboColumn.superclass.constructor.call(this, cfg);

    //Detect if there is an editor and if it at least extends a combobox, otherwise just treat it as a normal column and render the value itself
    this.renderer = (this.editor && this.editor.triggerAction)? Ext.ux.renderer.ComboBoxRenderer(this.editor, this.gridId) : function (value) { return value; };
  }
});

Ext.grid.Column.types["combocolumn"] = Ext.ux.grid.ComboColumn;

//A renderer that makes a editorgrid panel render the correct value
Ext.ux.renderer.ComboBoxRenderer = function(combo, gridId) {
  //Get the displayfield from the store or return the value itself if the record cannot be found

  //var str = combo.getId().substring(3); //
  //var i = str.lastIndexOf("_"); //
  //var fieldName  = str.substring(0, i); //
  //var processUID = str.substring(i + 1); //
  var comboBoxField = combo.getId().substring(3); //
  var str = ""; //

  var getValueComboBox = function (value) {
    var idx = combo.store.find(combo.valueField, value);
    var rec = combo.store.getAt(idx);
    if (rec) {
      //return rec.get(combo.displayField);
      if (grdNumRows > 1 || grdNumRows == 0) {
        return rec.get(combo.displayField);
      } else {
        str = rec.get(combo.displayField);
        grdRowLabel[comboBoxField] = str;
        return str;
      }
    }

    //return value;
    if (grdNumRows > 1 || grdNumRows == 0) {
      return value;
    } else {
      if (value) {
        grdRowLabel[comboBoxField] = value;
      } else {
        value = grdRowLabel[comboBoxField];
      }

      return value;
    }
  }

  return function (value) {
    //If we are trying to load the displayField from a store that is not loaded, add a single listener to the combo store's load event to refresh the grid view

    if (combo.store.getCount() == 0 && gridId) {
      combo.store.on(
        "load",
        function () {
          var grid = Ext.getCmp(gridId);
          if (grid) {
            grid.getView().refresh();
          }
        },
        {
        single: true
        }
      );

      //return value;
      if (grdNumRows > 1 || grdNumRows == 0) {
        return value;
      } else {
        if (typeof(grdRowLabel[comboBoxField]) == "undefined") {
          grdRowLabel[comboBoxField] = value;
          return grdRowLabel[comboBoxField];
        } else {
          return grdRowLabel[comboBoxField];
        }
      }
    }

    //return getValueComboBox(value);
    str = getValueComboBox(value); //
    if (grdNumRows > 1 || grdNumRows == 0) {
      return str;
    } else {
      return grdRowLabel[comboBoxField];
    }
  };
};

























Ext.QuickTips.init();

Ext.namespace("Ext.ux");

/*
Ext.ux.comboBoxRenderer = function(combo) {
  return function(value) {
    var idx = combo.store.find(combo.valueField, value);
    var rec = combo.store.getAt(idx);
    return rec.get(combo.displayField);
  };
};
*/

//
//screen.width, screen.height
var browserWidth = 0;
var browserHeight = 0;

//The more standards compliant browsers (mozilla/netscape/opera/IE7) use window.innerWidth and window.innerHeight
if (typeof window.innerWidth != "undefined") {
  browserWidth = window.innerWidth;
  browserHeight = window.innerHeight;
}
else {
  //IE6 in standards compliant mode (i.e. with a valid doctype as the first line in the document)
  if (typeof document.documentElement != "undefined" && typeof document.documentElement.clientWidth != "undefined" &&
      document.documentElement.clientWidth != 0) {
    browserWidth = document.documentElement.clientWidth;
    browserHeight = document.documentElement.clientHeight;
  } else {
    if (typeof document.documentElement != "undefined" && typeof document.documentElement.offsetHeight != "undefined") {
      //windows
      browserWidth = document.documentElement.offsetWidth;
      browserHeight = document.documentElement.offsetHeight;
    } else {
      //Older versions of IE
      browserWidth = document.getElementsByTagName("body")[0].clientWidth;
      browserHeight = document.getElementsByTagName("body")[0].clientHeight;
    }
  }
}
//

/*
var gridHeight;
if (window.innerHeight){
  gridHeight = window.innerHeight - 30;
}else {
  gridHeight = 350;
}
*/

new Ext.KeyMap(document, {
  key: Ext.EventObject.F5,
  fn: function(keycode, e) {
    if (!e.ctrlKey) {
      if (Ext.isIE) {
        // IE6 doesn't allow cancellation of the F5 key, so trick it into
        // thinking some other key was pressed (backspace in this case)
        e.browserEvent.keyCode = 8;
      }
      e.stopEvent();
      //document.location = document.location;
      //Ext.getCmp('storeConsolidatedGrid').reload();
      storeConsolidated.reload();
    } else {
        Ext.Msg.alert(_("ID_REFRESH_LABEL"), _("ID_REFRESH_MESSAGE"));
    }
  }
});

//var comboStore = new Ext.data.JsonStore({
//  proxy: new Ext.data.HttpProxy({
//    url: "proxyDataCombobox"
//  }),
//  root: "records",
//  fields: [{name: "value"},
//           {name: "id"}
//          ]
//});

//var gridId = Ext.id();
var gridId = "editorGridPanelMain";
var storeAux;

//Global variables
var storeConsolidated;
var toolbarconsolidated;
//var tb;
var consolidatedGrid;
var grid;
var textJump;
var readerCasesList;
var writerCasesList;
var proxyCasesList ;
var htmlMessage;
//var currentFieldEdited;

//var rowLabels = [];

var smodel;

function openCase(){
  var rowModel = consolidatedGrid.getSelectionModel().getSelected();
  if(rowModel){
    var appUid    = rowModel.data.APP_UID;
    var delIndex  = rowModel.data.DEL_INDEX;
    var caseTitle = (rowModel.data.APP_TITLE) ? rowModel.data.APP_TITLE : rowModel.data.APP_UID;

    Ext.Msg.show({
      msg: _("ID_OPEN_CASE") + " " + caseTitle,
      width:300,
      wait:true,
      waitConfig: {
        interval:200
      }
    });
    params = '';
    switch(action){
      case 'consolidated':
      default:
        params += 'APP_UID=' + appUid;
        params += '&DEL_INDEX=' + delIndex;
        requestFile = '../../'+varSkin+'/cases/open';
        break;
    }
    params += '&action=' + 'todo';
    redirect(requestFile + '?' + params);

  } else {
      msgBox(_("ID_INFORMATION"), _("ID_SELECT_ONE_AT_LEAST"));
  }
}

function jumpToCase(appNumber){
  Ext.MessageBox.show({ msg: _('ID_PROCESSING'), wait:true,waitConfig: {interval:200} });
  Ext.Ajax.request({
    url: 'cases_Ajax',
    success: function(response) {
      var res = Ext.decode(response.responseText);
      if (res.exists === true) {
        params = 'APP_NUMBER=' + appNumber;
        params += '&action=jump';
        requestFile = '../cases/open';
        redirect(requestFile + '?' + params);
      } else {
        Ext.MessageBox.hide();
        var message = new Array();
        message['CASE_NUMBER'] = appNumber;
        msgBox(_('ID_INPUT_ERROR'), _('ID_CASE_DOES_NOT_EXIST_JS', appNumber), 'error');
      }
    },
    params: {action:'previusJump', appNumber: appNumber}
  });
}

function pauseCase(date){
  rowModel = consolidatedGrid.getSelectionModel().getSelected();
  unpauseDate = date.format('Y-m-d');

  Ext.Msg.confirm(
    _("ID_CONFIRM"),
    _("ID_PAUSE_CASE_TO_DATE") + " " + date.format("M j, Y"),
    function(btn, text){
      if ( btn == 'yes' ) {
        Ext.MessageBox.show({
          msg: _("ID_PROCESSING"),
          wait:true,
          waitConfig: {
            interval:200
          }
        });
        Ext.Ajax.request({
          url: '../cases/cases_Ajax',
          success: function(response) {
            parent.updateCasesView();
            parent.updateCasesTree();
            Ext.MessageBox.hide();
          },
          params: {
            action:'pauseCase',
            unpausedate:unpauseDate,
            APP_UID:rowModel.data.APP_UID,
            DEL_INDEX: rowModel.data.DEL_INDEX
          }
        });
      }
    }
    );
}

function redirect(href){
  window.location.href = href;
}

function strReplace(strs, strr, str)
{
  var expresion = eval("/" + strs + "/gi");
  return (str.replace(expresion, strr));
}

function toolTipTab(str, show)
{
    document.getElementById("toolTipTab").innerHTML = str;
    document.getElementById("toolTipTab").style.left = "3px"; //x
    document.getElementById("toolTipTab").style.top = "27px"; //y
    document.getElementById("toolTipTab").style.display = (show == 1)? "inline" : "none";
}

var pnlMain;

Ext.apply(Ext.form.VTypes,{
    "int": function (value, field)
    {
        return /^\d*$/.test(value);
    },
    intText: "This field should only contain numbers",
    intMask: /[\d]/,

    real: function (value, field)
    {
        return /^\d*\.?\d*$/.test(value);
    },
    realText: "This field should only contain numbers and the point",
    realMask: /[\d\.]/
});

Ext.onReady(function () {
  pnlMain = new Ext.Panel({
    title        : '',
    renderTo     : 'cases-grid',
    //autoHeight  : true,
    //height       : 300,
    layout       : 'fit',
    layoutConfig : {
      align : 'stretch'
    }
  });

  //pnlMain = new Ext.Panel({
  //  id: "pnlMain",
  //
  //  region: "center",
  //  margins: {top:3, right:3, bottom:3, left:3},
  //  //bodyStyle: "padding: 25px 25px 25px 25px;", //propiedades ...
  //  border: false
  //});

  //LOAD ALL PANELS
  //var viewport = new Ext.Viewport({
  //  layout:"fit",
  //  items:[pnlMain]
  //});

  parent._action = action;

  optionMenuOpen = new Ext.Action({
    text: _("ID_OPEN_CASE"),
    iconCls: 'ICON_CASES_OPEN',
    handler: openCase
  });

  optionMenuPause = new Ext.Action({
    text: _("ID_PAUSE_CASE"),
    iconCls: 'ICON_CASES_PAUSED',
    menu: new Ext.menu.DateMenu({
      //vtype: 'daterange',
      handler: function(dp, date){
        pauseCase(date);
      }
    })

  });

  var buttonProcess = new Ext.Action({
    text: "Derivate",
    //iconCls: 'ICON_CASES_PAUSED',
    handler : function (){
      Ext.Msg.confirm('Confirm Routing', 'Route cases per batch?',
        function(btn, text){
          if (btn=='yes'){
              htmlMessage = "";
              var selectedRow = Ext.getCmp(gridId).getSelectionModel().getSelections();
              var maxLenght = selectedRow.length;
              for (var i in selectedRow) {
                rowGrid = selectedRow[i].data
                for (fieldGrid in rowGrid){
                  if(fieldGrid != 'APP_UID' && fieldGrid != 'APP_NUMBER' && fieldGrid != 'APP_TITLE' && fieldGrid != 'DEL_INDEX' ){
                       fieldGridGral = fieldGrid;
                       fieldGridGralVal = rowGrid[fieldGrid];
                  }
                }
                if (selectedRow[i].data) {
                    ajaxDerivationRequest(selectedRow[i].data["APP_UID"], selectedRow[i].data["DEL_INDEX"], maxLenght, selectedRow[i].data["APP_NUMBER"],fieldGridGral, fieldGridGralVal);
                }
              }
          }
         }
      );
    }
  });
  switch(action){
    case 'consolidated':
      menuItems = [buttonProcess, optionMenuOpen];
      break;
    default:
      menuItems = [];
      break;
  }

  var tabs = new Ext.TabPanel({
    autoWidth: true,
    enableTabScroll: true,
    activeTab: 0,
    //resizeTabs: true,
    style: {
      //height: "1.55em"
      height: "1.65em"
      //,
      //border: "5px solid blue"
    },
    defaults:{
      autoScroll: true
    },
    items: eval(Items),
    plugins: new Ext.ux.TabCloseMenu()
  });

  smodel = new Ext.grid.CheckboxSelectionModel({
    listeners:{
      selectionchange: function(sm){
        var count_rows = sm.getCount();
        switch(count_rows){
          case 0:
            break;
          default:
            break;
        }
      }
    }
  });

  var textSearch = new Ext.form.TextField ({
    allowBlank: true,
    ctCls:'pm_search_text_field',
    width: 150,
    emptyText: _("ID_EMPTY_SEARCH"),
    listeners: {
      specialkey: function(f,e){
        if (e.getKey() == e.ENTER) {
          doSearch();
        }
      }
    }
  });

  var btnSearch = new Ext.Button ({
    text: _("ID_SEARCH"),
    handler: doSearch
  });

  function doSearch(){
    searchText = textSearch.getValue();
    storeConsolidated.setBaseParam( 'search', searchText);
    storeConsolidated.load({
      params:{
        start : 0 ,
        limit : 20
      }
    });
  }

  var resetSearchButton = {
    text:'X',
    ctCls:'pm_search_x_button',
    handler: function(){
      textSearch.setValue('');
      doSearch();
    }
  };

  textJump = {
    xtype: 'numberfield',
    id   : 'textJump',
    allowBlank: true,
    width: 50,
    emptyText: _("ID_CASESLIST_APP_UID"),
    listeners: {
      specialkey: function(f,e){
        if (e.getKey() == e.ENTER) {
          // defining an id and using the Ext.getCmp method improves the accesibility of Ext components
          caseNumber = parseFloat(Ext.util.Format.trim(Ext.getCmp('textJump').getValue()));
          if (caseNumber) {
              jumpToCase(caseNumber);
          } else {
              msgBox('Input Error', 'You have set a invalid Application Number', 'error');
          }
        }
      }
    }
  };

  var btnJump = new Ext.Button ({
    text: _("ID_OPT_JUMP"),
    handler: function(){
      var caseNumber = parseFloat(Ext.util.Format.trim(Ext.getCmp('textJump').getValue()));
      if (caseNumber) {
        jumpToCase(caseNumber);
      } else {
        msgBox('Input Error', 'You have set a invalid Application Number', 'error');
      }
    }
  });

  function enableDisableMenuOption(){
    var rl = Ext.getCmp(gridId).store.getModifiedRecords();
    //alert ('-'+rl+'-');
    var rows = consolidatedGrid.getSelectionModel().getSelections();
    if (rl.toString()!='') {
      //alert(rl);
      optionMenuOpen.setDisabled(true);
      optionMenuPause.setDisabled(true);
      buttonProcess.setDisabled(true);
      return;
    }
    switch(action){
      case 'consolidated':
        if (rows.length == 0) {
          optionMenuOpen.setDisabled(true);
          optionMenuPause.setDisabled(true);
          buttonProcess.setDisabled(true);
        } else if( rows.length == 1 ) {
          optionMenuOpen.setDisabled(false);
          optionMenuPause.setDisabled(false);
          buttonProcess.setDisabled(false);
        } else {
          optionMenuOpen.setDisabled(true);
          optionMenuPause.setDisabled(true);
          buttonProcess.setDisabled(false);
        }
        break;
    }

  }

  toolbarconsolidated = [
  {
      xtype: "button",
      text: _("ID_ACTIONS"),
      menu: menuItems,
      listeners: {
          menushow: enableDisableMenuOption
      }
  },
  "->",
  {
      xtype: "checkbox",
      id: "chk_allColumn",
      name: "chk_allColumn",
      boxLabel: "Apply changes to all rows"
  },
  "",
  "-",
  textSearch,
  resetSearchButton,
  btnSearch,
  "-",
  textJump,
  "",
  btnJump
  ];

  //tb = new Ext.Toolbar({
  //  height: 33,
  //  items: toolbarconsolidated
  //});

  var viewport = new Ext.Viewport({
    layout: "fit",
    autoScroll: true,

    //items:[tabs, {id:"myDiv", border:false}]
    items: [tabs]
  });

  //routine to hide the debug panel if it is open
  if (parent.PANEL_EAST_OPEN) {
    parent.PANEL_EAST_OPEN = false;
    var debugPanel = parent.Ext.getCmp('debugPanel');
    debugPanel.hide();
    debugPanel.ownerCt.doLayout();
  }

  _nodeId = '';
  switch(action){
    case 'consolidated':
      _nodeId = "ID_CASES_CONSOLIDATED";
      break;
  }

  if (_nodeId != '') {
    treePanel1 = parent.Ext.getCmp('tree-panel');
    if (treePanel1) {
      node = treePanel1.getNodeById(_nodeId);
    }
    if (node) {
      node.select();
    }
  }

  //parent.updateCasesView();
  parent.updateCasesTree();

  function inArray(arr, obj) {
    for (var i=0; i<arr.length; i++) {
      if (arr[i] == obj) return true;
    }
    return false;
  }

  // Add the additional 'advanced' VTypes -- [Begin]
  Ext.apply(Ext.form.VTypes, {
    daterange : function(val, field) {
      var date = field.parseDate(val);

      if (!date) {
        return;
      }
      if (field.startDateField && (!this.dateRangeMax || (date.getTime() != this.dateRangeMax.getTime()))) {
        var start = Ext.getCmp(field.startDateField);
        start.setMaxValue(date);
        start.validate();
        this.dateRangeMax = date;
      }
      else if (field.endDateField && (!this.dateRangeMin || (date.getTime() != this.dateRangeMin.getTime()))) {
        var end = Ext.getCmp(field.endDateField);
        end.setMinValue(date);
        end.validate();
        this.dateRangeMin = date;
      }

      //Always return true since we're only using this vtype to set the
      //min/max allowed values (these are tested for after the vtype test)
      return true;
    }
  });
// Add the additional 'advanced' VTypes -- [End]

});

function msgBox(title, msg, type){
  if (typeof('type') == 'undefined') {
    type = 'info';
  }

  switch(type){
    case 'error':
      icon = Ext.MessageBox.ERROR;
      break;
    case 'info':
    default:
      icon = Ext.MessageBox.INFO;
      break;
  }

  Ext.Msg.show({
    title: title,
    msg: msg,
    fn: function(){},
    animEl: 'elId',
    icon: icon,
    buttons: Ext.MessageBox.OK
  });
}

//function formatDate(value){
//	return Ext.isDate(value) ? value.dateFormat('d/m/Y') : value;
//return value ? value : '';
//}
function renderTitle(val, p, r)
{  //appUid = "'" + r.data['APP_UID'] + "'";
   //return '<img src="/images/ext/default/s.gif" class="x-tree-node-icon ICON_CASES_NOTES" unselectable="off" id="extdd-17" onClick="openCaseNotesWindow('+appUid+',true)">';
   return ("<a href=\"javascript:;\" onclick=\"openCase(); return (false);\">" + val + "</a>");
}

function renderSummary (val, p, r) {
   var summaryIcon = '<img src="/images/ext/default/s.gif" class="x-tree-node-icon ss_layout_header" unselectable="off" id="extdd-17" ';
   summaryIcon += 'onclick="openSummaryWindow(' + "'" + r.data['APP_UID'] + "'" + ', ' + r.data['DEL_INDEX'] + ')" title="' + _('ID_SUMMARY') + '" />';
   return summaryIcon;
 };

function generateGridClassic(proUid, tasUid, dynUid){

   var pager = 20; //pageSize
   var pagei = 0; //start
   Ext.Ajax.request({
     url: '../pmConsolidatedCL/proxyGenerateGrid',
     success: function(response) {
       var dataResponse = Ext.util.JSON.decode(response.responseText);
       var viewConfigObject;
       var textArea = dataResponse.hasTextArea;

       if (textArea == false) {
         viewConfigObject = { //forceFit: true
         };
       } else {
         viewConfigObject = {
          //forceFit:true,
          enableRowBody:true,
          showPreview:true,
          getRowClass : function(record, rowIndex, p, store){
            if (this.showPreview) {
              p.body = '<p><br /></p>';
              return 'x-grid3-row-expanded';
            }
            return 'x-grid3-row-collapsed';
          }
        };
      }

      storeConsolidated = new Ext.data.Store({
        id: "storeConsolidatedGrid",
        remoteSort: true,
        proxy: new Ext.data.HttpProxy({
          url: "../pmConsolidatedCL/proxyConsolidated",
          api: {
            read: "../pmConsolidatedCL/proxyConsolidated",
            //update: "../pmConsolidatedCL/proxySaveConsolidated"
            update: "../pmConsolidatedCL/consolidatedUpdateAjax"
          }
        }),

        reader: new Ext.data.JsonReader({
          fields: dataResponse.readerFields,
          totalProperty: "totalCount",
          //successProperty: "success",
          idProperty: "APP_UID",
          root: "data",
          messageProperty: "message"
        }),

        writer: new Ext.data.JsonWriter({
          encode: true,
          writeAllFields: false
        }), //<-- plug a DataWriter into the store just as you would a Reader
        autoSave: true, //<-- false would delay executing create, update, destroy requests until specifically told to do so with some [save] buton.

        //sortInfo:{
        //  field: 'APP_CACHE_VIEW.APP_NUMBER',
        //  direction: "DESC"
        //}

        //,
        listeners: { //
          beforeload:function (store, options) { //
            grdNumRows = 0; //
          }, //

          load:function (store, records, options) { //
            grdNumRows = store.getCount(); //

            consolidatedGrid.setDisabled(false);
          } //
        } //
      });

       var xColumns = dataResponse.columnModel;
       xColumns.unshift(smodel);


       var cm = new Ext.grid.ColumnModel(xColumns);
       cm.config[2].renderer = renderTitle; //Case Number
       cm.config[3].renderer = renderTitle; //Case Title
       cm.config[4].renderer = renderSummary;//Case Summary

       storeConsolidated.setBaseParam("limit", pager);
       storeConsolidated.setBaseParam("start", pagei);
       storeConsolidated.setBaseParam('tasUid', tasUid);
       storeConsolidated.setBaseParam('dynUid', dynUid);
       storeConsolidated.setBaseParam('proUid', proUid);
       storeConsolidated.setBaseParam('dropList', Ext.util.JSON.encode(dataResponse.dropList));
       storeConsolidated.load();

       consolidatedGrid = new Ext.grid.EditorGridPanel({
         id: gridId,
         region: "center",

         store: storeConsolidated,
         cm: cm,
         sm: smodel,
         //autoHeight: true,

         //height: pnlMain.getSize().height - pnlMain.getFrameHeight(), //
         width: pnlMain.getSize().width, //
         height: browserHeight - 35, //

         layout: 'fit',
         //plugins: filters,
         viewConfig: viewConfigObject,

         listeners: {
           beforeedit: function (e) {
             var selRow = Ext.getCmp(gridId).getSelectionModel().getSelected();

             var swDropdown = 0;
             for (var i = 0; i <= dataResponse.dropList.length - 1 && swDropdown == 0; i++) {
               if (dataResponse.dropList[i] == e.field) {
                 swDropdown = 1;
               }
             }

             var swYesNo = 0;
             for (var i = 0; i <= dataResponse.comboBoxYesNoList.length - 1 && swYesNo == 0; i++) {
               if (dataResponse.comboBoxYesNoList[i] == e.field) {
                 swYesNo = 1;
               }
             }

             if (swDropdown == 1 && swYesNo == 0) {
               storeAux = Ext.StoreMgr.get("store" + e.field + "_" + proUid);
               storeAux.setBaseParam("appUid", selRow.data["APP_UID"]);
               storeAux.setBaseParam("dynUid", dynUid);
               storeAux.setBaseParam("proUid", proUid);
               storeAux.setBaseParam("fieldName", e.field);
               //currentFieldEdited = e.field;
               storeAux.load();
             }
           },

           afteredit: function (e) {
             //var store = consolidatedGrid.getStore();

             if (Ext.getCmp("chk_allColumn").checked) {
               Ext.Msg.show({
                 title: "",
                 msg: "The modification will be applied to all rows in your selection.",
                 buttons: Ext.Msg.YESNO,
                 fn: function (btn) {
                   if (btn == "yes") {
                     //storeConsolidated.each(function (record) {
                     //  record.set(e.field, e.value);
                     //});

                     consolidatedGrid.setDisabled(true);

                     var dataUpdate = "";
                     var strValue = "";
                     var sw = 0;

                     if (e.value instanceof Date) {
                         var mAux = e.value.getMonth() + 1;
                         var dAux = e.value.getDate();
                         var hAux = e.value.getHours();
                         var iAux = e.value.getMinutes();
                         var sAux = e.value.getSeconds();

                         strValue = e.value.getFullYear() + "-" + ((mAux <= 9)? "0" : "") + mAux + "-" + ((dAux <= 9)? "0" : "") + dAux;
                         strValue = strValue + " " + ((hAux <= 9)? "0" + ((hAux == 0)? "0" : hAux) : hAux) + ":" + ((iAux <= 9)? "0" + ((iAux == 0)? "0" : iAux) : iAux) + ":" + ((sAux <= 9)? "0" + ((sAux == 0)? "0" : sAux) : sAux);
                     } else {
                         strValue = strReplace("\"", "\\\"", e.value + "");
                     }

                     storeConsolidated.each(function (record) {
                       dataUpdate = dataUpdate + ((sw == 1)? "(sep1 /)": "") + record.data["APP_UID"] + "(sep2 /)" + e.field + "(sep2 /)" + strValue;
                       sw = 1;
                     });

                     ///////
                     Ext.Ajax.request({
                       url: "consolidatedUpdateAjax",
                       method: "POST",
                       params: {
                         "option":      "ALL",
                         "dynaformUid": dynUid,
                         "dataUpdate":  dataUpdate
                       },

                       success: function (response, opts) {
                         var dataResponse = eval("(" + response.responseText + ")"); //json

                         if (dataResponse.status && dataResponse.status == "OK") {
                           if (typeof(storeConsolidated.lastOptions.params) != "undefined") {
                             pagei = storeConsolidated.lastOptions.params.start;
                           }

                           storeConsolidated.setBaseParam("start", pagei);
                           storeConsolidated.load();
                         } else {
                           //
                         }
                       }
                     });
                   }
                 },
                 //animEl: "elId",
                 icon: Ext.MessageBox.QUESTION
               });
             }
           },

           mouseover: function (e, cell) {
            var rowIndex = consolidatedGrid.getView().findRowIndex(cell);
            if (!(rowIndex === false)) {
              var record = consolidatedGrid.store.getAt(rowIndex);
              var msg = record.get('APP_TITLE');
              Ext.QuickTips.register({
                text: msg,
                target: e.target
              });
            } else {
              Ext.QuickTips.unregister(e.target);
            }
          },

          mouseout: function (e, cell) {
            Ext.QuickTips.unregister(e.target);
          }
         },

        //tbar: tb,

        tbar: new Ext.Toolbar({
          height: 33,
          items: toolbarconsolidated
        }),

        bbar: new Ext.PagingToolbar({
          pageSize: pager,
          store: storeConsolidated,
          displayInfo: true,
          displayMsg: _("ID_DISPLAY_ITEMS"),
          emptyMsg: _("ID_DISPLAY_EMPTY")
        })
       });

      //Ext.ComponentMgr.get("myId").body.update("");
      //pnlMain.removeAll(false);
      pnlMain.removeAll();
      //adicion del grid definido con anterioridad
      pnlMain.add(consolidatedGrid);
      pnlMain.doLayout();
    },

    failure: function(){
      alert("Failure...");
    },

    params: {
      xaction: 'read',
      tasUid: tasUid,
      dynUid: dynUid,
      proUid: proUid
    }
  });
}

function generateGrid(proUid, tasUid, dynUid)
{  
    var pager = 20; //pageSize
    var pagei = 0; //start

    Ext.Ajax.request({
      url: urlProxy + 'generate/' + proUid + '/' + tasUid + '/' + dynUid,
      //url: '../pmConsolidatedCL/proxyGenerateGrid',
      headers: {
        'Content-Type': 'application/json',
        'Authorization': 'Bearer ' + credentials.access_token
      },
     success: function(response) {
       var dataResponse = Ext.util.JSON.decode(response.responseText);
       var viewConfigObject;
       var textArea = dataResponse.hasTextArea;

       if (textArea == false) {
         viewConfigObject = { //forceFit: true
         };
       } else {
         viewConfigObject = {
          //forceFit:true,
          enableRowBody:true,
          showPreview:true,
          getRowClass : function(record, rowIndex, p, store){
            if (this.showPreview) {
              p.body = '<p><br /></p>';
              return 'x-grid3-row-expanded';
            }
            return 'x-grid3-row-collapsed';
          }
        };
      }
      storeConsolidated = new Ext.data.Store({
        id: "storeConsolidatedGrid",
        remoteSort: true,
        proxy: new Ext.data.HttpProxy({
          method: 'GET',
          url: urlProxy + 'cases/' + proUid + '/' + tasUid + '/' + dynUid,
          api: {
            read: {
              method: 'GET',
              url: urlProxy + 'cases/' + proUid + '/' + tasUid + '/' + dynUid
            },
            update: {
              method: 'PUT',
              url: urlProxy + 'cases/' + proUid + '/' + tasUid + '/' + dynUid
            }
          },
          headers: {
            'Content-Type': 'application/json',
            'Authorization': 'Bearer ' + credentials.access_token
          }
        }),
        reader: new Ext.data.JsonReader({
          fields: dataResponse.readerFields,
          totalProperty: "totalCount",
          //successProperty: "success",
          idProperty: "APP_UID",
          root: "data",
          messageProperty: "message"
        }),

        writer: new Ext.data.JsonWriter({
          encode: false,
          writeAllFields: false
        }), //<-- plug a DataWriter into the store just as you would a Reader
        autoSave: true, //<-- false would delay executing create, update, destroy requests until specifically told to do so with some [save] buton.

        listeners: { //
          beforeload:function (store, options) { //
            grdNumRows = 0; //
          }, //

          load:function (store, records, options) { //
            grdNumRows = store.getCount(); //

            consolidatedGrid.setDisabled(false);
          } //
        } //
      });

       //carga de datos del data store via un request en Ajax
       //storeConsolidated.load();
       //ejemplo de un load con parametros para un data store
       //storeConsolidated.load({params:{start: 0 , limit: pageSize, action: 'todo'}});
       //definicion del column model basados en la respuesta del servidor
       //shorthand alias
       //var fm = Ext.form;
       var xColumns = dataResponse.columnModel;
       xColumns.unshift(smodel);

       var cm = new Ext.grid.ColumnModel(xColumns);
       cm.config[2].renderer = renderTitle; //Case Number
       cm.config[3].renderer = renderTitle; //Case Title
       cm.config[4].renderer = renderSummary;//Case Summary

       //generacion del grid basados en los atributos definidos con anterioridad
       /*
       storeConsolidated.setBaseParam("limit", pager);
       storeConsolidated.setBaseParam("start", pagei);
       storeConsolidated.setBaseParam('tasUid', tasUid);
       storeConsolidated.setBaseParam('dynUid', dynUid);
       storeConsolidated.setBaseParam('proUid', proUid);
       storeConsolidated.setBaseParam('dropList', Ext.util.JSON.encode(dataResponse.dropList));
       */
       storeConsolidated.load();

       consolidatedGrid = new Ext.grid.EditorGridPanel({
         id: gridId,
         region: "center",

         store: storeConsolidated,
         cm: cm,
         sm: smodel,
         //autoHeight: true,

         //height: pnlMain.getSize().height - pnlMain.getFrameHeight(), //
         width: pnlMain.getSize().width, //
         height: browserHeight - 35, //

         layout: 'fit',
         //plugins: filters,
         viewConfig: viewConfigObject,

         listeners: {
           beforeedit: function (e) {
             var selRow = Ext.getCmp(gridId).getSelectionModel().getSelected();

             var swDropdown = 0;
             for (var i = 0; i <= dataResponse.dropList.length - 1 && swDropdown == 0; i++) {
               if (dataResponse.dropList[i] == e.field) {
                 swDropdown = 1;
               }
             }

             var swYesNo = 0;
             for (var i = 0; i <= dataResponse.comboBoxYesNoList.length - 1 && swYesNo == 0; i++) {
               if (dataResponse.comboBoxYesNoList[i] == e.field) {
                 swYesNo = 1;
               }
             }

             //Saving the row previous values in order to fix the change label bug
             //Este bug se reproduce: cuando se cambia un dropdown y luego de otro row por ej un text se cambia su valor, el text del dropdown se copiaba a la fila del text actual en su dropdown, y asi
             //if (typeof(rowLabels["APP_UID"]) == "undefined" || selRow.data["APP_UID"] != rowLabels["APP_UID"]){
             //  for (var key in selRow.data) {
             //    rowLabels[key] = selRow.data[key];
             //  }
             //}

             //if (typeof(rowLabels["APP_UID"]) == "undefined"){
             //  for (var key in selRow.data) {
             //    rowLabels[key] = selRow.data[key];
             //  }
             //}
             //else {
             //  if(selRow.data["APP_UID"] != rowLabels["APP_UID"]) {
             //    for (var key in selRow.data) {
             //      rowLabels[key] = selRow.data[key];
             //    }
             //  }
             //  //else {
             //  //
             //  //}
             //}

             if (swDropdown == 1 && swYesNo == 0) {
               //comboStore.setBaseParam('appUid', selRow.data['APP_UID']);
               //comboStore.setBaseParam('dynUid', dynUid);
               //comboStore.setBaseParam('proUid', proUid);
               //comboStore.setBaseParam('fieldName', e.field);
               //currentFieldEdited = e.field;
               //comboStore.load();

             }
           },

           afteredit: function (e) {
             //var store = consolidatedGrid.getStore();

             if (Ext.getCmp("chk_allColumn").checked) {
               Ext.Msg.show({
                 title: "",
                 msg: "The modification will be applied to all rows in your selection.",
                 buttons: Ext.Msg.YESNO,
                 fn: function (btn) {
                   if (btn == "yes") {
                     //storeConsolidated.each(function (record) {
                     //  record.set(e.field, e.value);
                     //});

                     consolidatedGrid.setDisabled(true);

                     var dataUpdate = "";
                     var strValue = "";
                     var sw = 0;

                     if (e.value instanceof Date) {
                         var mAux = e.value.getMonth() + 1;
                         var dAux = e.value.getDate();
                         var hAux = e.value.getHours();
                         var iAux = e.value.getMinutes();
                         var sAux = e.value.getSeconds();

                         strValue = e.value.getFullYear() + "-" + ((mAux <= 9)? "0" : "") + mAux + "-" + ((dAux <= 9)? "0" : "") + dAux;
                         strValue = strValue + " " + ((hAux <= 9)? "0" + ((hAux == 0)? "0" : hAux) : hAux) + ":" + ((iAux <= 9)? "0" + ((iAux == 0)? "0" : iAux) : iAux) + ":" + ((sAux <= 9)? "0" + ((sAux == 0)? "0" : sAux) : sAux);
                     } else {
                         strValue = strReplace("\"", "\\\"", e.value + "");
                     }

                     storeConsolidated.each(function (record) {
                       dataUpdate = dataUpdate + ((sw == 1)? "(sep1 /)": "") + record.data["APP_UID"] + "(sep2 /)" + e.field + "(sep2 /)" + strValue;
                       sw = 1;
                     });

                     ///////
                     Ext.Ajax.request({
                      url: "consolidatedUpdateAjax",
                      method: 'PUT',
                      url: urlProxy + 'cases/' + proUid + '/' + tasUid + '/' + dynUid,                      
                      headers: {
                        'Content-Type': 'application/json',
                        'Authorization': 'Bearer ' + credentials.access_token
                      },
                      jsonData: {
                        "option":      "ALL",
                        "dynaformUid": dynUid,
                        "dataUpdate":  dataUpdate
                      },
                       success: function (response, opts) {
                         var dataResponse = eval("(" + response.responseText + ")"); //json

                         if (dataResponse.status && dataResponse.status == "OK") {
                           if (typeof(storeConsolidated.lastOptions.params) != "undefined") {
                             pagei = storeConsolidated.lastOptions.params.start;
                           }

                           storeConsolidated.setBaseParam("start", pagei);
                           storeConsolidated.load();
                         } else {
                           //
                         }
                       }
                     });
                   }
                 },
                 //animEl: "elId",
                 icon: Ext.MessageBox.QUESTION
               });
             }
           },

           mouseover: function (e, cell) {
            var rowIndex = consolidatedGrid.getView().findRowIndex(cell);
            if (!(rowIndex === false)) {
              var record = consolidatedGrid.store.getAt(rowIndex);
              var msg = record.get('APP_TITLE');
              Ext.QuickTips.register({
                text: msg,
                target: e.target
              });
            } else {
              Ext.QuickTips.unregister(e.target);
            }
          },

          mouseout: function (e, cell) {
            Ext.QuickTips.unregister(e.target);
          }
         },

        //tbar: tb,

        tbar: new Ext.Toolbar({
          height: 33,
          items: toolbarconsolidated
        }),

        bbar: new Ext.PagingToolbar({
          pageSize: pager,
          store: storeConsolidated,
          displayInfo: true,
          displayMsg: _("ID_DISPLAY_ITEMS"),
          emptyMsg: _("ID_DISPLAY_EMPTY")
        })
       });

      //remocion de todos los elementos del panel principal donde se carga el grid
      //Ext.ComponentMgr.get("myId").body.update("");
      //pnlMain.removeAll(false);
      pnlMain.removeAll();
      //adicion del grid definido con anterioridad
      pnlMain.add(consolidatedGrid);
      //recarga de los elementos del grid, para su visualizacion.
      pnlMain.doLayout();
    },

    //en caso de fallo ejecutar la siguiente funcion.
    failure: function(){
      alert("Failure...");
    }
  });
}

function ajaxDerivationRequest(appUid, delIndex, maxLenght, appNumber, fieldGridGral, fieldGridGralVal){
  Ext.Ajax.request({
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'Authorization': 'Bearer ' + credentials.access_token
    },
    url: urlProxy + 'derivate/' + appUid + '/' + appNumber + '/' + delIndex + '/' + fieldGridGral + '/' + fieldGridGralVal + '/',
    success: function(response) {
      var dataResponse;
      var fullResponseText = response.responseText;

      if (fullResponseText.charAt(0) != "<") {
        dataResponse = Ext.util.JSON.decode(response.responseText);
      } else {
        dataResponse = Ext.util.JSON.decode("{message:\"Case Derivated\"}");
        storeConsolidated.reload();
      }

      htmlMessage = htmlMessage + dataResponse.message + "<br />";
      var tmpIndex = htmlMessage.split("<br />");
      index = tmpIndex.length - 1;

      if (index == maxLenght) {
        Ext.MessageBox.show({
          title: "Derivation Result",
          msg: htmlMessage,

          fn: function (btn, text, opt) {
            if (maxLenght == storeConsolidated.getCount()) {
              window.location.reload();
            }

            if (fullResponseText.charAt(0) != "<" && parent.document.getElementById("batchRoutingCasesNumRec") != null) {
                parent.document.getElementById("batchRoutingCasesNumRec").innerHTML = parseInt(dataResponse.casesNumRec);
            }

            storeConsolidated.reload();
          }
        });
      }
      //if an exception die trigger happens
    },

    failure: function() {
      index = tmpIndex.length - 1;
      htmlMessage = htmlMessage + "failed: " + appUid;

      if (index == maxLenght) {
        Ext.Msg.show({
          title: "Derivation Result",
          msg: htmlMessage
        });
        storeConsolidated.reload();
      }
    }
  });
}

function linkRenderer(value)
{
    return "<a href=\"" + value + "\" onclick=\"window.open('" + value + "', '_blank'); return false;\">" + value + "</a>";
}

