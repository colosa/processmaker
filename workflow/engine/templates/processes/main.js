/*

 * @author: Erik A. Ortiz

 * Aug 20th, 2010

 */

var processesGrid;

var store;

var comboCategory;

var winDesigner;

var newTypeProcess;





/**

 * Global variables and variable initialization for import process.

 */

var importProcessGlobal = {};

importProcessGlobal.proFileName = "";

importProcessGlobal.groupBeforeAccion="";

importProcessGlobal.sNewProUid = "";

importProcessGlobal.importOption = "";

importProcessGlobal.processFileType = "";



new Ext.KeyMap(document, {

  key: Ext.EventObject.F5,

  fn: function(keycode, e) {

      if (! e.ctrlKey) {

        if (Ext.isIE)

            e.browserEvent.keyCode = 8;

        e.stopEvent();

        document.location = document.location;

      }

      else

        Ext.Msg.alert( _('ID_REFRESH_LABEL') , _('ID_REFRESH_MESSAGE') );

  }

});



Ext.apply(Ext.form.VTypes, {

    textWithoutTags: function (value, field)

    {

        var strAux = "a|applet|b|body|br|button|code|div|em|embed|form|frame|frameset|head|header|html|iframe|img|input|noscript|object|script|select|style|table|textarea";



        return !(eval("/^.*\\x3C[\\s\\x2F]*(?:" + strAux + ")\\s*.*\\x3E.*$/").test(value));

    },

    textWithoutTagsText: ""

});



Ext.onReady(function(){

  Ext.state.Manager.setProvider(new Ext.state.CookieProvider());

  Ext.QuickTips.init();



  store = new Ext.data.GroupingStore( {

  //var store = new Ext.data.Store( {

	remoteSort: true,

    proxy : new Ext.data.HttpProxy({

      url: 'processesList'

    }),



    reader : new Ext.data.JsonReader( {

      totalProperty: 'totalCount',

      root: 'data',

      fields : [

        {name : 'PRO_DESCRIPTION'},

        {name : 'PRO_UID'},

        {name : 'PRO_CATEGORY_LABEL'},

        {name : 'PRO_TITLE'},

        {name : 'PRO_STATUS'},

        {name : 'PRO_STATUS_LABEL'},

        {name : 'PRO_CREATE_DATE'},

        {name : 'PRO_DEBUG'},

        {name : 'PRO_DEBUG_LABEL'},

        {name : 'PRO_CREATE_USER_LABEL'},

        {name : 'CASES_COUNT', type:'float'},

        {name : 'CASES_COUNT_DRAFT', type:'float'},

        {name : 'CASES_COUNT_TO_DO', type:'float'},

        {name : 'CASES_COUNT_COMPLETED', type:'float'},

        {name : 'CASES_COUNT_CANCELLED', type:'float'},

        {name : 'PROJECT_TYPE', type:'string'}

        /*----------------------------------********---------------------------------*/

        ,{name : "PRO_UPDATE_DATE"}

      ]

    }),



    //sortInfo:{field: 'PRO_TITLE', direction: "ASC"}

    //groupField:'PRO_CATEGORY_LABEL'



    listeners: {

      load: function (store) {

        Ext.ComponentMgr.get("export").setDisabled(true);

      }

    }

  });



  var expander = new Ext.ux.grid.RowExpander({

    tpl : new Ext.Template(

        '<p><b>' + _('ID_PRO_DESCRIPTION') + ':</b> {PRO_DESCRIPTION}</p><br>'

    )

  });



  comboCategory = new Ext.form.ComboBox({

      fieldLabel : 'Categoty',

      hiddenName : 'category',

      store : new Ext.data.Store( {

        proxy : new Ext.data.HttpProxy( {

          url : '../processProxy/categoriesList',

          method : 'POST'

        }),

        reader : new Ext.data.JsonReader( {

          fields : [ {

            name : 'CATEGORY_UID'

          }, {

            name : 'CATEGORY_NAME'

          } ]

        })

      }),

      valueField : 'CATEGORY_UID',

      displayField : 'CATEGORY_NAME',

      triggerAction : 'all',

      emptyText : _('ID_SELECT'),

      selectOnFocus : true,

      editable : true,

      width: 180,

      allowBlank : true,

      autocomplete: true,

      typeAhead: true,

      allowBlankText : _('ID_SHOULD_SELECT_LANGUAGE_FROM_LIST'),

      listeners:{

      scope: this,

      'select': function() {

        filter = comboCategory.value;

        store.setBaseParam( 'category', filter);

        var searchTxt = Ext.util.Format.trim(Ext.getCmp('searchTxt').getValue());



        if( searchTxt == '' ){

          store.setBaseParam( 'processName', '');

        }



        store.load({params: {category: filter, start: 0, limit: 25}});

      }}

    })

/*  storePageSize = new Ext.data.SimpleStore({

    fields: ['size'],

    data: [['20'],['30'],['40'],['50'],['100']],

    autoLoad: true

  });



  var comboPageSize = new Ext.form.ComboBox({

    typeAhead     : false,

    mode          : 'local',

    triggerAction : 'all',

    store: storePageSize,

    valueField: 'size',

    displayField: 'size',

    width: 50,

    editable: false,

    listeners:{

      select: function(c,d,i){

        //UpdatePageConfig(d.data['size']);

        bbar.pageSize = parseInt(d.data['size']);

        bbar.moveFirst();



        //Ext.getCmp('bbar').setPageSize(comboPageSize.getValue());

      }

    }

  });



  comboPageSize.setValue(pageSize);





  var bbar = new Ext.PagingToolbar({

    id: 'bbar',

    pageSize: '15',

    store: store,

    displayInfo: true,

    displayMsg: 'Displaying Processes {0} - {1} of {2}',

    emptyMsg: "",

    items:[_('ID_PAGE_SIZE')+':',comboPageSize]

  })  */



  var mnuNewBpmnProject = {

      text: _("ID_NEW_BPMN_PROJECT"),

      iconCls: "silk-add",

      icon: "",

      pmTypeProject: "bpmnProject",

      handler: function ()

      {

          newProcess({type:"bpmnProject"});

      }

  };



  var mnuNewProject = {

      text: _("ID_NEW_PROJECT"),

      iconCls: "silk-add",

      icon: "",

      pmTypeProject: "classicProject",

      handler: function ()

      {

          newProcess({type: "classicProject"});

      }

  };



  var arrayMenuNewOption = [];



  if (typeof(arrayFlagMenuNewOption["bpmn"]) != "undefined") {

      arrayMenuNewOption.push(mnuNewBpmnProject);

  }



  if (typeof(arrayFlagMenuNewOption["pm"]) != "undefined") {

      arrayMenuNewOption.push(mnuNewProject);

  }



  for (var i = 0; i <= arrayMenuNewOptionPlugin.length - 1; i++) {

      try {

          if (typeof(arrayMenuNewOptionPlugin[i].handler) != "undefined") {

              eval("arrayMenuNewOptionPlugin[i].handler = " + arrayMenuNewOptionPlugin[i].handler + ";");

          }



          arrayMenuNewOption.push(arrayMenuNewOptionPlugin[i]);

      } catch (e) {

      }

  }



  if (arrayMenuNewOption.length > 1) {

      newTypeProcess = {

          xtype: "tbsplit",

          text: _("ID_NEW"),

          iconCls: "button_menu_ext ss_sprite ss_add",

          menu: arrayMenuNewOption,

          listeners: {

              "click": function (obj, e)

              {

                  obj.showMenu();

              }

          }

      };

  } else {

      newTypeProcess = {

          text: _("ID_NEW"),

          iconCls: "button_menu_ext ss_sprite ss_add",

          handler: function ()

          {

              newProcess({type: arrayMenuNewOption[0].pmTypeProject});

          }

      };

  }



  processesGrid = new Ext.grid.GridPanel( {

    region: 'center',

    layout: 'fit',

    id: 'processesGrid',

    height:500,

    //autoWidth : true,

    width:'',

    title : '',

    stateful : true,

    stateId : 'gridProcessMain',

    enableColumnResize: true,

    enableHdMenu: true,

    frame:false,

    plugins: expander,

    cls : 'grid_with_checkbox',

    columnLines: true,





    /*view: new Ext.grid.GroupingView({

        //forceFit:true,

        //groupTextTpl: '{text} ({[values.rs.length]} {[values.rs.length > 1 ? "Items" : "Item"]})'

        groupTextTpl: '{text}'

    }),*/

    viewConfig: {

      forceFit:true,

      cls:"x-grid-empty",

      emptyText: _('ID_NO_RECORDS_FOUND')

    },

    cm: new Ext.grid.ColumnModel({

      defaults: {

          width: 200,

          sortable: true

      },

      columns: [

        expander,

        {id:'PRO_UID', dataIndex: 'PRO_UID', hidden:true, hideable:false},

        {header: "", dataIndex: 'PRO_STATUS', width: 50, hidden:true, hideable:false},

        {header: _('ID_PRO_DESCRIPTION'), dataIndex: 'PRO_DESCRIPTION',hidden:true, hideable:false},

        {header: _('ID_PRO_TITLE'), dataIndex: 'PRO_TITLE', width: 300, renderer:function(v,p,r){

            // TODO Labels for var 'type' are hardcoded, they must be replaced on the future

            var color = r.get('PROJECT_TYPE') == 'bpmn'? 'green': 'blue';

            var type = r.get('PROJECT_TYPE') == 'bpmn'? ' (BPMN Project)': '';

            return Ext.util.Format.htmlEncode(v) + ' ' + String.format("<font color='{0}'>{1}</font>", color, type);

        }},

        {header: _('ID_TYPE'), dataIndex: 'PROJECT_TYPE', width: 60, hidden:false},

        {header: _('ID_CATEGORY'), dataIndex: 'PRO_CATEGORY_LABEL', width: 100, hidden:false},

        {header: _('ID_STATUS'), dataIndex: 'PRO_STATUS_LABEL', width: 50, renderer:function(v,p,r){

          color = r.get('PRO_STATUS') == 'ACTIVE'? 'green': 'red';

          return String.format("<font color='{0}'>{1}</font>", color, v);

        }},

        {header: _('ID_PRO_USER'), dataIndex: 'PRO_CREATE_USER_LABEL', width: 150},

        {header: _('ID_PRO_CREATE_DATE'), dataIndex: 'PRO_CREATE_DATE', width: 90},

        {header: _('ID_INBOX'), dataIndex: 'CASES_COUNT_TO_DO', width: 50, align:'right'},

        {header: _('ID_DRAFT'), dataIndex: 'CASES_COUNT_DRAFT', width: 50, align:'right'},

        {header: _('ID_COMPLETED'), dataIndex: 'CASES_COUNT_COMPLETED', width: 70, align:'right'},

        {header: _('ID_CANCELLED'), dataIndex: 'CASES_COUNT_CANCELLED', width: 70, align:'right'},

        {header: _('ID_TOTAL_CASES'), dataIndex: 'CASES_COUNT', width: 75,renderer:function(v){return "<b>"+v+"</b>";}, align:'right'},

        {header: _('ID_PRO_DEBUG'), dataIndex: 'PRO_DEBUG_LABEL', width: 50, align:'center'}

        /*----------------------------------********---------------------------------*/

        ,{header: _("ID_LAN_UPDATE_DATE"), dataIndex: "PRO_UPDATE_DATE", width: 75, align:"left"}

      ]

    }),

    store: store,

    tbar:[

      newTypeProcess,/*

      {

        text: _('ID_NEW'),

        iconCls: 'button_menu_ext ss_sprite ss_add',

        //icon: '/images/addc.png',

        handler: newProcess

      },*/

    	'-'

      ,{

        text: _('ID_EDIT'),

        iconCls: 'button_menu_ext ss_sprite  ss_pencil',

        //icon: '/images/edit.gif',

        handler: editProcess

      },/*{

        text: 'Edit (New Editor)',

        iconCls: 'button_menu_ext',

        icon: '/images/pencil_beta.png',

        handler: editNewProcess

      },*/{

        text: _('ID_STATUS'),

        id:'activator',

        icon: '',

        iconCls: 'silk-add',

        handler: activeDeactive,

        disabled:true

      },{

        text: _('ID_DELETE'),

        iconCls: "button_menu_ext ss_sprite ss_cross",

        handler:deleteProcess

      },{

        xtype: 'tbseparator'

      },{

        id: "export",

        disabled: true,

        text: _("ID_EXPORT"),

        iconCls: "silk-add",

        icon: "/images/export.png",

        handler: function () {

          exportProcess();

        }

      },{

        text: _('ID_IMPORT'),

        iconCls: 'silk-add',

        icon: '/images/import.gif',

        // handler:importProcess

        handler : function(){

          importProcessGlobal.processFileType = "pm";

          importProcess();

        }

      },{

        xtype: 'tbfill'

      },{

        xtype: 'tbseparator'

      },

      _('ID_CATEGORY'),

      comboCategory,{

        xtype: 'tbseparator'

      },new Ext.form.TextField ({

        id: 'searchTxt',

        ctCls:'pm_search_text_field',

        allowBlank: true,

        width: 150,

        emptyText: _('ID_EMPTY_SEARCH'),//'enter search term',

        listeners: {

          specialkey: function(f,e){

            if (e.getKey() == e.ENTER) {

              doSearch();

            }

          }

        }

      }),{

        text:'X',

        ctCls:'pm_search_x_button_des',

        handler: function(){

          //store.setBaseParam( 'category', '<reset>');

          store.setBaseParam('processName', '');

          store.load({params: {start: 0, limit: 25}});

          Ext.getCmp('searchTxt').setValue('');

          //comboCategory.setValue('');

          //store.reload();

        }

      },{

        text: _('ID_SEARCH'),

        handler: doSearch

      }

    ],

    // paging bar on the bottom

    bbar: new Ext.PagingToolbar({

        pageSize: 25,

        store: store,

        displayInfo: true,

        displayMsg: _('ID_DISPLAY_PROCESSES'),

        emptyMsg: "",

        items:[]

    }),

    listeners: {

      rowdblclick: editProcess,

      render: function(){

        this.loadMask = new Ext.LoadMask(this.body, {msg:'Loading...'});

        processesGrid.getSelectionModel().on('rowselect', function(){

          var rowSelected = processesGrid.getSelectionModel().getSelected();

          var activator = Ext.getCmp('activator');



          activator.setDisabled(false);

          Ext.ComponentMgr.get("export").setDisabled(false);



          if( rowSelected.data.PRO_STATUS == 'ACTIVE' ){

            activator.setIcon('/images/deactivate.png');

            activator.setText( _('ID_DEACTIVATE') );

          } else {

            activator.setIcon('/images/activate.png');

            activator.setText( _('ID_ACTIVATE') );

          }

        });

      }

    }

  });



  processesGrid.store.load({params: {"function": "languagesList", "start": 0, "limit": 25}});

  processesGrid.addListener('rowcontextmenu', onMessageContextMenu,this);

  processesGrid.on('rowcontextmenu', function (grid, rowIndex, evt) {

    var sm = grid.getSelectionModel();

    sm.selectRow(rowIndex, sm.isSelected(rowIndex));



    var rowSelected = Ext.getCmp('processesGrid').getSelectionModel().getSelected();

    var activator = Ext.getCmp('activator2');

    var debug = Ext.getCmp('debug');



    if( rowSelected.data.PRO_STATUS == 'ACTIVE' ){

      activator.setIconClass('icon-deactivate');

      activator.setText( _('ID_DEACTIVATE') );

    } else {

      activator.setIconClass('icon-activate');

      activator.setText( _('ID_ACTIVATE') );

    }



    if( rowSelected.data.PRO_DEBUG == 1){

      debug.setIconClass('icon-debug-disabled');

      debug.setText(_('ID_DISABLE_DEBUG'));

    } else {

      debug.setIconClass('icon-debug');

      debug.setText(_('ID_ENABLE_DEBUG'));

    }



    if (rowSelected.data.PROJECT_TYPE == "bpmn"){

        Ext.getCmp("mnuGenerateBpmn").setDisabled(true);

    } else {

        Ext.getCmp("mnuGenerateBpmn").setDisabled(false);

    }

  }, this);

  processesGrid.on('contextmenu', function (evt) {

      evt.preventDefault();

  }, this);



  function onMessageContextMenu(grid, rowIndex, e) {

    e.stopEvent();

    var coords = e.getXY();

    messageContextMenu.showAt([coords[0], coords[1]]);

  }



  var arrayContextMenuOption = [

      {

          text: _("ID_EDIT"),

          iconCls: "button_menu_ext ss_sprite ss_pencil",

          handler: editProcess

      },

      {

          id: "activator2",

          text: "",

          icon: "",

          handler: activeDeactive

      },

      {

          id: "debug",

          text: "",

          handler: enableDisableDebug

      },

      {

          text: _("ID_DELETE"),

          iconCls: "button_menu_ext ss_sprite ss_cross",

          handler: deleteProcess

      },

      {

          text: _("ID_EXPORT"),

          icon: "/images/export.png",

          handler: function ()

          {

              exportProcess();

          }

      },

      {

          id: "mnuGenerateBpmn",

          text: _("ID_GENERATE_BPMN_PROJECT"),

          iconCls: "button_menu_ext ss_sprite ss_page_white_go",

          hidden: true,

          handler: function ()

          {

              generateBpmn();

          }

      }

  ];



  for (var i = 0; i <= arrayContextMenuOptionPlugin.length - 1; i++) {

      try {

          if (typeof(arrayContextMenuOptionPlugin[i].handler) != "undefined") {

              eval("arrayContextMenuOptionPlugin[i].handler = " + arrayContextMenuOptionPlugin[i].handler + ";");

          }



          arrayContextMenuOption.push(arrayContextMenuOptionPlugin[i]);

      } catch (e) {

      }

  }



  var messageContextMenu = new Ext.menu.Menu({

      id: "messageContextMenu",

      items: arrayContextMenuOption

  });



  var viewport = new Ext.Viewport({

    layout: 'border',

    autoScroll: true,

    items: [

      processesGrid

    ]

  });

});





function newProcess(params)

{

  params = typeof params == 'undefined' ? {type:'classicProject'} : params;



  // TODO this variable have hardcoded labels, it must be changed on the future

  var formTitle = (params.type == "classicProject")? _("ID_NEW_PROJECT") : _("ID_NEW_BPMN_PROJECT");



    //  window.location = 'processes_New';

  var ProcessCategories = new Ext.form.ComboBox({

    fieldLabel : _('ID_CATEGORY'),

    hiddenName : 'PRO_CATEGORY',

    valueField : 'CATEGORY_UID',

    displayField : 'CATEGORY_NAME',

    triggerAction : 'all',

    selectOnFocus : true,

    editable : false,

    width: 180,

    allowBlank : true,

    value: '',

    store : new Ext.data.Store( {

      autoLoad: true,  //autoload the data

      proxy : new Ext.data.HttpProxy( {

        url : '../processProxy/getCategoriesList',

        method : 'POST'

      }),



      reader : new Ext.data.JsonReader( {

        fields : [ {

          name : 'CATEGORY_UID'

        }, {

          name : 'CATEGORY_NAME'

        } ]

      })

    })

  });

  ProcessCategories.store.on('load',function(store) {

    ProcessCategories.setValue(store.getAt(0).get('CATEGORY_UID'));

  });



  var frm = new Ext.FormPanel( {

    id: 'newProcessForm',

    labelAlign : 'right',

    bodyStyle : 'padding:5px 5px 0',

    width : 400,

    items : [ {

        id: 'PRO_TITLE',

        fieldLabel: _('ID_TITLE'),

        xtype:'textfield',

        width: 260,

        maskRe: /^(?!^(PRN|AUX|CLOCK\$|NUL|CON|COM\d|LPT\d|\...*)(\..+)?$)[^\x00-\x1f\\?*\";|/]+$/i,

        allowBlank: false,

        vtype: "textWithoutTags",

        listeners: {

            'focus' : function(value){

                document.getElementById("PRO_TITLE").onpaste = function() {

                    return false;

                };

            }

        }

      },  {

        id: 'PRO_DESCRIPTION',

        fieldLabel: _('ID_DESCRIPTION'),

        xtype:'textarea',

        width: 260

      },

      ProcessCategories/*,

      {

        id: 'editor',

        xtype: 'radiogroup',

        fieldLabel: _('ID_OPEN_WITH'),

        items: [

          {boxLabel: _('ID_CLASSIC_EDITOR'), name: 'editor', inputValue: 'classic', checked: true},

          {boxLabel: _('ID_BPMN_EDITOR'), name: 'editor', inputValue: 'bpmn'}

        ]

      }*/

    ],

    buttons : [{

      text : _('ID_CREATE'),

      handler : saveProcess

    },{

      text : _('ID_CANCEL'),

      handler : function() {

        win.close();

      }

    }]

  });



  var win = new Ext.Window({

    id: 'newProjectWin',

    title: formTitle, //_('ID_CREATE_PROCESS'),

    width: 470,

    height: 220,

    layout:'fit',

    autoScroll:true,

    modal: true,

    maximizable: false,

    items: [frm],

    _projectType: params.type

  });

  win.show();

}



function saveProcess()

{

  var projectType = Ext.getCmp('newProjectWin')._projectType;



  Ext.getCmp('PRO_TITLE').setValue((Ext.getCmp('PRO_TITLE').getValue()).trim());

  if (Ext.getCmp('newProcessForm').getForm().isValid()) {

        Ext.getCmp('newProcessForm').getForm().submit({

          url : '../processProxy/saveProcess?type=' + projectType,

          waitMsg : _('ID_SAVING_PROCESS'),

          waitTitle : "&nbsp;",

          timeout : 36000,

          success : function(obj, resp) {

            if (projectType == 'classicProject') {

              location.href = 'processes_Map?PRO_UID='+resp.result.PRO_UID;

            } else {

                    openWindowIfIE('../designer?prj_uid=' + resp.result.PRO_UID);

            }

          },

          failure: function(obj, resp) {

            PMExt.error( _('ID_ERROR'), resp.result.msg);

          }

        });

    } else {

        PMExt.error( _('ID_ERROR'), _('ID_INVALID_PROCESS_NAME'));

    }

}



function doSearch(){

  if(comboCategory.getValue() == '')

    store.setBaseParam( 'category', '<reset>');

  filter = Ext.getCmp('searchTxt').getValue();

  store.setBaseParam('processName', filter);



  store.load({params:{processName: filter, start: 0 , limit: 25}});

}



editProcess = function(typeParam)

{

  var rowSelected = processesGrid.getSelectionModel().getSelected();

  if (!rowSelected) {

      Ext.Msg.show({

          title: _("ID_INFORMATION"),

          msg: _('ID_NO_SELECTION_WARNING'),

          buttons: Ext.Msg.INFO,

          fn: function () {

          },

          animEl: 'elId',

          icon: Ext.MessageBox.INFO,

          buttons: Ext.MessageBox.OK

      });

      return;

  }



    switch (rowSelected.data.PROJECT_TYPE) {

        case "bpmn":

            openWindowIfIE("../designer?prj_uid=" + rowSelected.data.PRO_UID);

            break;

        case "classic":

            location.assign("processes_Map?PRO_UID=" + rowSelected.data.PRO_UID);

            break;

        default:

            var fn = rowSelected.data.PROJECT_TYPE;

            fn = fn.replace(/\s/g, "_");

            fn = fn.replace(/\-/g, "_");

            fn = fn + "DesignerGridRowDblClick";



            eval("var flag = typeof(" + fn + ") == \"function\";");



            if (flag) {

                eval(fn + "(rowSelected.data);");

            } else {

                Ext.MessageBox.show({

                    title: _("ID_ERROR"),

                    msg: _("ID_DESIGNER_PROCESS_DESIGNER_IS_DISABLED"),

                    icon: Ext.MessageBox.ERROR,

                    buttons: Ext.MessageBox.OK

                });

            }

            break;

    }

}



editNewProcess = function(){

  var rowSelected = processesGrid.getSelectionModel().getSelected();

  if( rowSelected ) {

    location.href = '../designer?pro_uid='+rowSelected.data.PRO_UID

  } else {

     Ext.Msg.show({

      title: _("ID_INFORMATION"),

      msg: _('ID_NO_SELECTION_WARNING'),

      buttons: Ext.Msg.INFO,

      fn: function(){},

      animEl: 'elId',

      icon: Ext.MessageBox.INFO,

      buttons: Ext.MessageBox.OK

    });

  }

}



deleteProcess = function(){

  var rows = processesGrid.getSelectionModel().getSelections();

  if( rows.length > 0 ) {

    isValid = true;

    errLog = Array();



    //verify if the selected rows have not any started or delegated cases

    for(i=0; i<rows.length; i++){

      if( rows[i].get('CASES_COUNT') != 0 ){

        errLog.push(i);

        isValid = false;

      }

    }



    if( isValid ){

      ids = Array();

      for(i=0; i<rows.length; i++)

        ids[i] = rows[i].get('PRO_UID');



      PRO_UIDS = ids.join(',');



      Ext.Msg.confirm(

        _('ID_CONFIRM'),

        (rows.length == 1) ? _('ID_PROCESS_DELETE_LABEL') : _('ID_PROCESS_DELETE_ALL_LABEL'),

        function(btn, text){

          if ( btn == 'yes' ){

            Ext.MessageBox.show({ msg: _('ID_DELETING_ELEMENTS'), wait:true,waitConfig: {interval:200} });

            Ext.Ajax.request({

              url: 'processes_Delete',

              success: function(response) {

                Ext.MessageBox.hide();

                processesGrid.store.reload();

                result = Ext.util.JSON.decode(response.responseText);



                if(result){

                  if(result.status != 0){

                    Ext.MessageBox.show({

                      title: _('ID_ERROR'),

                      msg: result.msg,

                      buttons: Ext.MessageBox.OK,

                      icon: Ext.MessageBox.ERROR

                    });

                  }

                } else

                  Ext.MessageBox.show({

                    title: _('ID_ERROR'),

                    msg: response.responseText,

                    buttons: Ext.MessageBox.OK,

                    icon: Ext.MessageBox.ERROR

                  });

              },

              params: {PRO_UIDS:PRO_UIDS}

            });

          }

        }

      );

    } else {

      errMsg = '';

      for(i=0; i<errLog.length; i++){

        e = _('ID_PROCESS_CANT_DELETE');

        e = e.replace('{0}', rows[errLog[i]].get('PRO_TITLE'));

        e = e.replace('{1}', rows[errLog[i]].get('CASES_COUNT'));

        errMsg += e + '<br/>';

      }

      Ext.MessageBox.show({

        title: _('ID_ERROR'),

        msg: errMsg,

        buttons: Ext.MessageBox.OK,

        icon: Ext.MessageBox.ERROR

      });

    }

  } else {

    Ext.Msg.show({

      title: _("ID_INFORMATION"),

      msg: _('ID_NO_SELECTION_WARNING'),

      buttons: Ext.Msg.INFO,

      fn: function(){},

      animEl: 'elId',

      icon: Ext.MessageBox.INFO,

      buttons: Ext.MessageBox.OK

    });

  }

}



function exportProcess() {

  var record = processesGrid.getSelectionModel().getSelections();



  if(record.length == 1) {

    var myMask = new Ext.LoadMask(Ext.getBody(), {msg: _("ID_LOADING")});

    var proUid   = record[0].get("PRO_UID");



    myMask.show();



    Ext.Ajax.request({

      url: "../processes/processes_Export",

      method: "GET",

      params: {"pro_uid": proUid},

      success: function (response) {

        var result = JSON.parse(response.responseText);

        myMask.hide();



        if (result.success) {

          window.location = "../processes/processes_DownloadFile?file_hash=" + result.file_hash;

        } else {

          Ext.Msg.show({title: "", msg: result.message, icon: Ext.MessageBox.ERROR, buttons: Ext.MessageBox.OK});

        }

      },



      failure: function (response, opts) {

        myMask.hide();

      }

    });

  }

  else {

    Ext.Msg.show({

      title: _("ID_INFORMATION"),

      msg: _("ID_NO_SELECTION_WARNING"),

      icon: Ext.MessageBox.INFO,

      buttons: Ext.MessageBox.OK

    });

  }

}



function generateBpmn()

{

    var record = processesGrid.getSelectionModel().getSelections();



    if (typeof(record) != "undefined") {

        if (record.length == 1) {

            var loadMaskGenerateBpmn = new Ext.LoadMask(Ext.getBody(), {msg: _("ID_PROCESSING")});

            var processUid = record[0].get("PRO_UID");



            loadMaskGenerateBpmn.show();



            Ext.Ajax.request({

                url: "../processProxy/generateBpmn",

                method: "POST",

                params: {

                    processUid: processUid

                },



                success: function (response, opts)

                {

                    var dataResponse = Ext.util.JSON.decode(response.responseText);



                    if (dataResponse.status) {

                        if (dataResponse.status == "OK") {

                            //processesGrid.store.reload();

                            location.assign("../designer?prj_uid=" + dataResponse.projectUid);

                        } else {

                            Ext.MessageBox.show({

                                title: _("ID_ERROR"),

                                icon: Ext.MessageBox.ERROR,

                                buttons: Ext.MessageBox.OK,

                                msg: dataResponse.message

                            });

                        }

                    }



                    loadMaskGenerateBpmn.hide();

                },

                failure: function (response, opts)

                {

                    loadMaskGenerateBpmn.hide();

                }

            });

        } else {

            Ext.MessageBox.show({

                title: _("ID_INFORMATION"),

                icon: Ext.MessageBox.INFO,

                buttons: Ext.MessageBox.OK,

                msg: _("ID_NO_SELECTION_WARNING")

            });

        }

    }

}



importProcessExistGroup = function()

{



  var processFileTypeTitle = (processFileType == "pm") ? "" : " " + processFileType;



  proFileName         = importProcessGlobal.proFileName;

  groupBeforeAccion   = importProcessGlobal.groupBeforeAccion;

  sNewProUid          = importProcessGlobal.sNewProUid;

  importOption        = importProcessGlobal.importOption;

  var processFileType = importProcessGlobal.processFileType;



  var w = new Ext.Window({

    id          : 'importProcessExistGroupWindow',

    title       : _('ID_IMPORT_PROCESS') + processFileTypeTitle,

    header      : false,

    width       : 460,

    height      : 230,

    modal       : true,

    autoScroll  : false,

    maximizable : false,

    resizable   : false,

    items : [

      new Ext.form.FormPanel({

        title      : _('ID_LAN_UPLOAD_TITLE'),

        header     : false,

        id         : 'formUploadExistGroup',

        fileUpload : false,

        width      : 440,

        frame      : true,

        autoHeight : false,

        bodyStyle  : 'padding: 10px 10px 0 10px;',

        labelWidth : 50,

        defaults : {

          anchor     : '90%',

          allowBlank : false,

          msgTarget  : 'side'

        },

        items : [

          {

            xtype  : 'box',

            autoEl : {

              tag  : 'div',

              html : '<div ><img src="/images/ext/default/window/icon-warning.gif" style="display:inline;float:left;" /><div style="float:left;display:inline;width:300px;margin-left:5px;">' + _('ID_PROCESS_EXIST_SOME_GROUP') + '</div><div style="width:300px;" >&nbsp</div></div>'

            }

          }, {

            xtype  : 'spacer',

            height : 10

          }, {

            items: [

              {

                xtype      : "radio",

                boxLabel   : _('ID_PROCESS_GROUP_RENAME'),

                name       : "optionGroupExistInDatabase",

                inputValue : '1',

                tabIndex   : 1

              }

            ]

          }, {

            items:[

              {

                xtype      : "radio",

                boxLabel   : _('ID_PROCESS_GROUP_MERGE_PREEXISTENT'),

                tabIndex   : 2,

                name       : "optionGroupExistInDatabase",

                inputValue : '2',

                checked    : "checked"

              }

            ]

          }, {

            xtype : 'hidden',

            name  : 'ajaxAction',

            value : groupBeforeAccion

          }, {

            xtype : 'hidden',

            name  : 'PRO_FILENAME',

            value : proFileName

          }, {

            xtype : 'hidden',

            name  : 'sNewProUid',

            value : sNewProUid

          }, {

            xtype : 'hidden',

            name  : 'IMPORT_OPTION',

            value : importOption

          }, {

            name  : 'processFileType',

            xtype : 'hidden',

            value : processFileType

          }, {

            xtype  : 'spacer',

            height : 10

          }

        ],

        buttons : [

          {

            text    : _('ID_SAVE'),

            handler : function() {

              var uploader = Ext.getCmp('formUploadExistGroup');



              if (uploader.getForm().isValid()) {

                uploader.getForm().submit({

                  url     : 'processes_Import_Ajax',

                  waitMsg : _('ID_UPLOADING_PROCESS_FILE'),

                  waitTitle : "&nbsp;",

                  success : function(o, resp) {

                    var resp_            = Ext.util.JSON.decode(resp.response.responseText);

                    var sNewProUid       = resp_.sNewProUid;



                    if (typeof(resp_.project_type) != "undefined" && resp_.project_type == "bpmn") {

                        openWindowIfIE("../designer?prj_uid=" + sNewProUid);

                    } else {

                        window.location.href = "processes_Map?PRO_UID=" + sNewProUid;

                    }

                  },

                  failure: function(o, resp) {

                    w.close();

                    Ext.MessageBox.show({

                      title   : _('ID_ERROR'),

                      msg     : resp.result.msg,

                      buttons : Ext.MessageBox.OK,

                      animEl  : 'mb9',

                      fn      : function(){},

                      icon    : Ext.MessageBox.ERROR

                    });

                  }

                });

              }

            }

          }, {

            text    : _('ID_CANCEL'),

            handler : function(){

              w.close();

            }

          }

        ]

      })

    ]

  });

  w.show();

};



importProcessExistProcess = function()

{



  var processFileTypeTitle = (processFileType == "pm") ? "" : " " + processFileType;

  var processFileType      = importProcessGlobal.processFileType;

  var proFileName          = importProcessGlobal.proFileName;



  var w = new Ext.Window({

    id          : 'importProcessExistProcessWindow',

    title       : _('ID_IMPORT_PROCESS') + processFileTypeTitle,

    header      : false,

    width       : 460,

    height      : 230,

    autoHeight  : true,

    modal       : true,

    autoScroll  : false,

    maximizable : false,

    resizable   : false,

    items : [

      new Ext.form.FormPanel({

        title      : _('ID_LAN_UPLOAD_TITLE'),

        header     : false,

        id         : 'formUploader',

        fileUpload : false,

        width      : 440,

        frame      : true,

        autoHeight : true,

        bodyStyle  : 'padding: 10px 10px 0 10px;',

        labelWidth : 50,

        defaults : {

          anchor     : '90%',

          allowBlank : false,

          msgTarget  : 'side'

        },

        items : [

          {

            xtype   : 'box',

            autoEl : {

              tag  : 'div',

              html : '<div ><img src="/images/ext/default/window/icon-warning.gif" style="display:inline;float:left;" /><div style="float:left;display:inline;width:300px;margin-left:5px;">' + _('ID_IMPORT_ALREADY_EXISTS') + '</div><div style="width:300px;" >&nbsp</div></div>'

            }

          }, {

            xtype  : 'spacer',

            height : 10

          },

          {

            items: [

                {

                    xtype: "radio",

                    name:  "IMPORT_OPTION",

                    inputValue: "3",

                    boxLabel:   _("IMPORT_PROCESS_NEW"),

                    tabIndex:   3,

                    checked:    "checked"

                }

            ]

          },

          //{

          //  items: [

          //      {

          //          xtype: "radio",

          //          name:  "IMPORT_OPTION",

          //          inputValue: "2",

          //          boxLabel:   _("IMPORT_PROCESS_DISABLE"),

          //          tabIndex:   2

          //      }

          //  ]

          //},

          {

            items: [

                {

                    xtype: "radio",

                    name:  "IMPORT_OPTION",

                    inputValue: "1",

                    boxLabel:   _("IMPORT_PROCESS_OVERWRITING"),

                    tabIndex:   1

                }

            ]

          },

          {

            xtype : 'hidden',

            name  : 'ajaxAction',

            value : 'uploadFileNewProcessExist'

          }, {

            xtype : 'hidden',

            name  : 'PRO_FILENAME',

            value : proFileName

          }, {

          name  : 'processFileType',

          xtype : 'hidden',

          value : processFileType

          }, {

            xtype  : 'spacer',

            height : 10

          }

        ],

        buttons:[

          {

            text    : _('ID_SAVE'),

            handler : function() {

              var uploader = Ext.getCmp('formUploader');

              if (uploader.getForm().isValid()) {

                uploader.getForm().submit({

                  url     : 'processes_Import_Ajax',

                  waitMsg : _('ID_UPLOADING_PROCESS_FILE'),

                  waitTitle : "&nbsp;",

                  success: function(o, resp) {

                    var resp_      = Ext.util.JSON.decode(resp.response.responseText);

                    var sNewProUid = resp_.sNewProUid;



                    if (resp_.ExistGroupsInDatabase == 0) {

                        if (typeof(resp_.project_type) != "undefined" && resp_.project_type == "bpmn") {

                            openWindowIfIE("../designer?prj_uid=" + sNewProUid);

                        } else {

                            window.location.href = "processes_Map?PRO_UID=" + sNewProUid;

                        }

                    }

                    else {

                      importProcessGlobal.proFileName       = resp_.proFileName;

                      importProcessGlobal.groupBeforeAccion = resp_.groupBeforeAccion;

                      importProcessGlobal.sNewProUid        = resp_.sNewProUid;

                      importProcessGlobal.importOption      = resp_.importOption;

                      importProcessExistGroup();

                    }

                  },

                  failure : function(o, resp) {

                    w.close();

                    Ext.MessageBox.show({

                      title   : _('ID_ERROR'),

                      msg     : resp.result.msg,

                      buttons : Ext.MessageBox.OK,

                      animEl  : 'mb9',

                      fn      : function(){},

                      icon    : Ext.MessageBox.ERROR

                    });

                  }

                });

              }

            }

          }, {

            text : _('ID_CANCEL'),

            handler : function(){

              w.close();

            }

          }

        ]

      })

    ]

  });

  w.show();

};



//importProcess = function(){

//  window.location = 'processes_Import';

//}



importProcess = function()

{

    var processFileType      = importProcessGlobal.processFileType;

    var processFileTypeTitle = (processFileType == "pm") ? "" : " " + processFileType;



    var w = new Ext.Window({

      id          : 'importProcessWindow',

      title       : _('ID_IMPORT_PROCESS')+processFileTypeTitle,

      width       : 420,

      height      : 130,

      modal       : true,

      autoScroll  : false,

      maximizable : false,

      resizable   : false,

      items: [

        new Ext.FormPanel({

          id         : 'uploader',

          fileUpload : true,

          width      : 400,

          height     : 90,

          frame      : true,

          title      : _('ID_IMPORT_PROCESS'),

          header     : false,

          autoHeight : false,

          bodyStyle  : 'padding: 10px 10px 0 10px;',

          labelWidth : 50,

          defaults : {

            anchor     : '90%',

            allowBlank : false,

            msgTarget  : 'side'

          },

          items : [

            {

              name  : 'ajaxAction',

              xtype : 'hidden',

              value : 'uploadFileNewProcess'

            }, {

            name  : 'processFileType',

            xtype : 'hidden',

            value : processFileType

            },{

                name: "createMode",

                xtype: "hidden",

                value: "create"

            }, {

              xtype      : 'fileuploadfield',

              id         : 'form-file',

              emptyText  : _('ID_SELECT_PROCESS_FILE'),

              fieldLabel : _('ID_LAN_FILE'),

              name       : 'PROCESS_FILENAME',

              buttonText : '',

              buttonCfg : {

                iconCls : 'upload-icon'

              }

            }

          ],

          buttons : [{

              text    : _('ID_UPLOAD'),

              handler : function(){

                  var arrayMatch = [];



                  if ((arrayMatch = eval("/^.+\.(" + arrayFlagImportFileExtension.join("|") + ")$/i").exec(Ext.getCmp("form-file").getValue()))) {

                      var fileExtension = arrayMatch[1];



                      switch (fileExtension) {

                          case "pm":

                          case "pmx":

                              var uploader = Ext.getCmp("uploader");



                              if (uploader.getForm().isValid()) {

                                  uploader.getForm().submit({

                                      url      : "processes_Import_Ajax",

                                      waitMsg  : _("ID_UPLOADING_PROCESS_FILE"),

                                      waitTitle: "&nbsp;",

                                      success: function(o, resp)

                                      {

                                          var resp_ = Ext.util.JSON.decode(resp.response.responseText);



                                          if (resp_.status) {

                                              if (resp_.status == "DISABLED-CODE") {

                                                  Ext.MessageBox.show({

                                                      title: _("ID_ERROR"),

                                                      msg: "<div style=\"overflow: auto; width: 500px; height: 150px;\">" + stringReplace("\\x0A", "<br />", resp_.message) + "</div>", //\n 10

                                                      width: 570,

                                                      height: 250,

                                                      icon: Ext.MessageBox.ERROR,

                                                      buttons: Ext.MessageBox.OK

                                                  });



                                                  return;

                                              }

                                          }



                                          if (resp_.catchMessage == "") {

                                              if (resp_.ExistProcessInDatabase == "0") {

                                                  if (resp_.ExistGroupsInDatabase == "0") {

                                                      var sNewProUid = resp_.sNewProUid;



                                                      if (typeof(resp_.project_type) != "undefined" && resp_.project_type == "bpmn") {

                                                          openWindowIfIE("../designer?prj_uid=" + sNewProUid);

                                                      } else {

                                                          window.location.href = "processes_Map?PRO_UID=" + sNewProUid;

                                                      }

                                                  } else {

                                                      importProcessGlobal.sNewProUid        = resp_.sNewProUid;

                                                      importProcessGlobal.proFileName       = resp_.proFileName;

                                                      importProcessGlobal.groupBeforeAccion = resp_.groupBeforeAccion;

                                                      importProcessExistGroup();

                                                  }

                                              } else if (resp_.ExistProcessInDatabase == "1") {

                                                  importProcessGlobal.proFileName = resp_.proFileName;

                                                  importProcessExistProcess();

                                              }

                                          } else {

                                              w.close();



                                              Ext.MessageBox.show({

                                                  title  : "",

                                                  msg    : resp_.catchMessage,

                                                  buttons: Ext.MessageBox.OK,

                                                  animEl : "mb9",

                                                  fn     : function(){},

                                                  icon   : Ext.MessageBox.ERROR

                                              });

                                          }

                                      },

                                      failure : function(o, resp)

                                      {

                                          w.close();



                                          Ext.MessageBox.show({

                                              title  : "",

                                              msg    : resp.catchMessage,

                                              buttons: Ext.MessageBox.OK,

                                              animEl : "mb9",

                                              fn     : function(){},

                                              icon   : Ext.MessageBox.ERROR

                                          });

                                      }

                                  });

                              }

                              break;

                          case "bpmn":

                              importProcessGlobal.processFileType = "bpmn";

                              importProcessBpmnSubmit();

                              break;

                      }

                  } else {

                      Ext.MessageBox.alert(_("ID_ERROR"), _("ID_FILE_UPLOAD_INCORRECT_EXTENSION"));

                  }

              }

          },{

            text: _('ID_CANCEL'),

            handler: function(){

              w.close();

            }

          }]

        })

      ]

    });

  w.show();

}



var windowbpmnoption = new Ext.Window({

    id: 'windowBpmnOptionWindow',

    title: _('ID_IMPORT_PROCESS'),

    header: false,

    width: 420,

    height: 200,

    modal: true,

    autoScroll: false,

    maximizable: false,

    resizable: false,

    closeAction: 'hide',

    items: [

        {

            xtype: 'panel',

            border: false,

            bodyStyle: 'padding:15px;background-color:#e8e8e8;',

            items: [

                {

                    xtype: 'box',

                    autoEl: {

                        tag: 'div',

                        html: '<div style="margin-bottom:15px;background-color:#e8e8e8;"><img style="display:inline-block;vertical-align:top;" src="/images/ext/default/window/icon-warning.gif"/><div style="display:inline-block;width:338px;margin-left:5px;">' +

                                _('ID_IMPORT_ALREADY_EXISTS_BPMN') + "<br><br>" + _('ID_IMPORT_ALREADY_EXISTS_BPMN_NOTE') +

                                '</div></div>'

                    }

                }

            ]

        }

    ],

    buttons: [

        {

            text: _('ID_CREATE_NEW'),

            handler: function () {

                Ext.getCmp('uploader').getForm().setValues({"createMode": "rename"})

                importProcessBpmnSubmit();

            }

        }, {

            text: _('ID_OVERWRITE'),

            handler: function () {

                Ext.getCmp('uploader').getForm().setValues({"createMode": "overwrite"})

                importProcessBpmnSubmit();

            }

        }, {

            text: _('ID_CANCEL'),

            handler: function () {

                Ext.getCmp('importProcessWindow').close();

                windowbpmnoption.hide();

            }

        }

    ]

});



importProcessBpmnSubmit = function () {

    windowbpmnoption.hide();

    var uploader = Ext.getCmp('uploader');

    if (uploader.getForm().isValid()) {

        uploader.getForm().submit({

            url: 'processes_Import_Bpmn',

            waitMsg: _('ID_UPLOADING_PROCESS_FILE'),

            waitTitle: "&nbsp;",

            success: function (o, resp) {

                var resp_ = Ext.util.JSON.decode(resp.response.responseText);

                if (resp_.success === "error") {

                    Ext.MessageBox.show({

                        title: '',

                        msg: resp_.catchMessage,

                        buttons: Ext.MessageBox.OK,

                        animEl: 'mb9',

                        fn: function () {

                        },

                        icon: Ext.MessageBox.ERROR

                    });

                    return;

                }

                if (resp_.success === "confirm") {

                    windowbpmnoption.show();

                    return;

                }

                Ext.getCmp('importProcessWindow').close();

                if (typeof(importProcessGlobal.processFileType) != "undefined" && importProcessGlobal.processFileType == "bpmn") {

                    openWindowIfIE("../designer?prj_uid=" + resp_.prj_uid);

                } else {

                    window.location.href = "processes_Map?PRO_UID=" + resp_.prj_uid;

                }

            },

            failure: function (o, resp) {

                Ext.getCmp('importProcessWindow').close();

                Ext.MessageBox.show({

                    title: '',

                    msg: resp.catchMessage,

                    buttons: Ext.MessageBox.OK,

                    animEl: 'mb9',

                    fn: function () {

                    },

                    icon: Ext.MessageBox.ERROR

                });

            }

        });

    }

};



function activeDeactive(){

  var rows = processesGrid.getSelectionModel().getSelections();



  if( rows.length > 0 ) {

    var ids = '';

    for(i=0; i<rows.length; i++) {

      if(i != 0 ) ids += ',';

      ids += rows[i].get('PRO_UID');

    }



    Ext.Ajax.request({

      url : '../processProxy/changeStatus',

      params : { UIDS : ids },

      success: function ( result, request ) {

        store.reload();

        var activator = Ext.getCmp('activator');

        activator.setDisabled(true);

        activator.setText(_('ID_STATUS'));

        activator.setIcon('');

      },

      failure: function ( result, request) {

        Ext.MessageBox.alert(_('ID_FAILED'), result.responseText);

      }

    });

  } else {

     Ext.Msg.show({

      title: _("ID_INFORMATION"),

      msg: _('ID_NO_SELECTION_WARNING'),

      buttons: Ext.Msg.INFO,

      fn: function(){},

      animEl: 'elId',

      icon: Ext.MessageBox.INFO,

      buttons: Ext.MessageBox.OK

    });

  }

}



function enableDisableDebug()

{

  var rows = processesGrid.getSelectionModel().getSelections();



  if( rows.length > 0 ) {

    var ids = '';

    for(i=0; i<rows.length; i++)

      ids += (i != 0 ? ',': '') + rows[i].get('PRO_UID');



    Ext.Ajax.request({

      url : '../processProxy/changeDebugMode' ,

      params : {UIDS: ids},

      success: function ( result, request ) {

        store.reload();

        var activator = Ext.getCmp('activator');

        activator.setDisabled(true);

        activator.setText(_('ID_STATUS'));

        activator.setIcon('');

      },

      failure: function ( result, request) {

        Ext.MessageBox.alert(_('ID_FAILED'), result.responseText);

      }

    });

  } else {

    Ext.Msg.show({

      title: _("ID_INFORMATION"),

      msg: _('ID_NO_SELECTION_WARNING'),

      buttons: Ext.Msg.INFO,

      fn: function(){},

      animEl: 'elId',

      icon: Ext.MessageBox.INFO,

      buttons: Ext.MessageBox.OK

    });

  }



}



Ext.EventManager.on(window, 'beforeunload', function () {

    if (winDesigner)

        winDesigner.close();

});





function openWindowIfIE(pathDesigner) {

    if ((navigator.userAgent.indexOf("MSIE") != -1) || (navigator.userAgent.indexOf("Trident") != -1)) {

        if (Ext.getCmp('newProjectWin'))

            Ext.getCmp('newProjectWin').close();

        if (Ext.getCmp('importProcessWindow'))

            Ext.getCmp('importProcessWindow').close();

        if (Ext.getCmp('importProcessExistGroupWindow'))

            Ext.getCmp('importProcessExistGroupWindow').close();

        if (Ext.getCmp('importProcessExistProcessWindow'))

            Ext.getCmp('importProcessExistProcessWindow').close();

        if (Ext.getCmp('windowBpmnOptionWindow'))

            Ext.getCmp('windowBpmnOptionWindow').close();

        processesGrid.store.reload();

        if (winDesigner && winDesigner.closed === false) {

            if (winDesigner.window.PMDesigner.project.isDirty()) {

                Ext.Msg.alert(_('ID_REFRESH_LABEL'), _('ID_UNSAVED_TRIGGERS_WINDOW'));

            } else {

                winDesigner = window.open(pathDesigner, 'winDesigner');

            }

        } else {

            winDesigner = window.open(pathDesigner, 'winDesigner');

        }

        return;

    }

    location.href = pathDesigner;

}


