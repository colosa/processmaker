/**
 * @author: Erik Amaru O. <erik@colosa.com>
 */

var store;
var cmodel;
var usersGrid;
var groupsGrid;
var smodel;

var searchButton;
var searchText;


var user_admin = '00000000000000000000000000000001';
var pageSize;
var fullNameFormat;
var dateFormat;

Ext.onReady(function(){
  Ext.state.Manager.setProvider(new Ext.state.CookieProvider());
  Ext.QuickTips.init();

  fullNameFormat = CONFIG.fullNameFormat;
  dateFormat = CONFIG.dateFormat;
  pageSize = parseInt(CONFIG.pageSize);

  searchButton = new Ext.Action({
    text: _('ID_SEARCH'),
    handler: DoSearch
  });

  searchText = new Ext.form.TextField ({
    id: 'searchTxt',
    ctCls:'pm_search_text_field',
    allowBlank: true,
    width: 150,
    emptyText: _('ID_ENTER_SEARCH_TERM'),//'enter search term',
    listeners: {
      specialkey: function(f,e){
        if (e.getKey() == e.ENTER) {
          DoSearch();
        }
      },
      focus: function(f,e) {
        var row = usersGrid.getSelectionModel().getSelected();
         usersGrid.getSelectionModel().deselectRow(usersGrid.getStore().indexOf(row));
      }
    }
  });

  clearTextButton = new Ext.Action({
    text: 'X',
     ctCls:'pm_search_x_button',
     handler: GridByDefault
  });

  smodel = new Ext.grid.RowSelectionModel({
    singleSelect: true,
     listeners:{
      rowselect: function(sm){
        //editButton.enable();
      },
      rowdeselect: function(sm){
        //editButton.disable();
      }
    }
  });

  var reader = new Ext.data.JsonReader( {
    root: 'users',
    totalProperty: 'total_users',
    idProperty: 'USR_UID',
    fields : [
      {name : 'USR_UID'},
      {name : 'USR_USERNAME'},
      {name : 'USR_FIRSTNAME'},
      {name : 'USR_LASTNAME'},
      {name : 'USR_EMAIL'},
      {name : 'USR_ROLE'},
      {name : 'USR_DUE_DATE'},
      {name : 'DEP_TITLE'},
      {name : 'LAST_LOGIN'},
      {name : 'USR_STATUS'},
      {name : 'USR_UX'},
      {name : 'TOTAL_CASES',type:'int'},
      {name : 'DUE_DATE_OK'},
      {name : 'USR_AUTH_SOURCE'}
    ]
  });

  var proxy = new Ext.data.HttpProxy({
    api: {
      read : '../users/users_Ajax?function=usersList',
      //create : 'app.php/users/create',
      update: '../adminProxy/uxUserUpdate'//,
      //destroy: 'app.php/users/destroy'
    }
  });

  // The new DataWriter component.
  var writer = new Ext.data.JsonWriter({
      encode: true,
      writeAllFields: false
  });

  store = new Ext.data.GroupingStore( {
    // proxy: new Ext.data.HttpProxy({
    //   url: '../users/users_Ajax?function=usersList'
    // }),
    proxy: proxy,
    reader: reader,
    writer: writer,  // <-- plug a DataWriter into the store just as you would a Reader
    autoSave: true // <-- false would delay executing create, update, destroy requests until specifically told to do so with some [save] buton.
  });

  comboPageSize = new Ext.form.ComboBox({
    typeAhead     : false,
    mode          : 'local',
    triggerAction : 'all',
    store: new Ext.data.SimpleStore({
      fields: ['size'],
       data: [['20'],['30'],['40'],['50'],['100']],
       autoLoad: true
    }),
    valueField: 'size',
    displayField: 'size',
    width: 50,
    editable: false,
    listeners:{
      select: function(c,d,i){
        //UpdatePageConfig(d.data['size']);
        bbarpaging.pageSize = parseInt(d.data['size']);
        bbarpaging.moveFirst();
      }
    }
  });

  comboPageSize.setValue(pageSize);

  bbarpaging = new Ext.PagingToolbar({
    pageSize: pageSize,
     store: store,
     displayInfo: true,
     displayMsg: _('ID_GRID_PAGE_DISPLAYING_USERS_MESSAGE') + '&nbsp; &nbsp; ',
     emptyMsg: _('ID_GRID_PAGE_NO_USERS_MESSAGE'),
     items: ['-',_('ID_PAGE_SIZE')+':',comboPageSize]
  });

  cmodel = new Ext.grid.ColumnModel({
    columns: [
      {id:'USR_UID', dataIndex: 'USR_UID', hidden:true, hideable:false},
      //{header: '', dataIndex: 'USR_UID', width: 30, align:'center', sortable: false, renderer: photo_user},
      {header: _('ID_USER_NAME'), dataIndex: 'USR_USERNAME', width: 90, hidden:false, align:'left'},
      {header: _('ID_FULL_NAME'), dataIndex: 'USR_USERNAME', width: 50, align:'left', renderer: full_name},
      {header: _('ID_ROLE'), dataIndex: 'USR_ROLE', width: 50, hidden:false, align:'left'},
      {header: _('ID_STATUS'), dataIndex: 'USR_STATUS', width: 50, hidden: true, align: 'center', renderer: render_status},
      {
        header: _('ID_USER_EXPERIENCE'),
        dataIndex: 'USR_UX',
        width: 50,
        editor: new Ext.form.ComboBox({
          listClass: 'x-combo-list-small',
          mode: 'local',
          displayField:'name',
          lazyRender: true,
          triggerAction: 'all',
          valueField:'id',
          editable: false,
          store: new Ext.data.ArrayStore({
            fields: ['id', 'name'],
            data : uxTypes
          }),
          listeners: {
            select: function(a, b) {
              var row = usersGrid.getSelectionModel().getSelected();
              role = row.get('USR_ROLE');

              if (role == 'PROCESSMAKER_ADMIN' && (this.value == 'SIMPLIFIED' || this.value == 'SINGLE')) {
                PMExt.warning(_('ID_WARNING'), _('ID_ADMINS_CANT_USE_UXS'));
                this.setValue('NORMAL');
              }
            }
          }
        })
      }
    ]
  });

  usersGrid = new Ext.grid.EditorGridPanel({
    title: _('ID_USERS'),
    //region: 'center',
    layout: 'fit',
    id: 'usersGrid',
    height:100,
    autoWidth : true,
    stateful : true,
    stateId : 'grid',
    enableColumnResize: true,
    enableHdMenu: true,
    frame:false,
    columnLines: false,
    viewConfig: {
      forceFit:true
    },
    clicksToEdit: 1,
    store: store,
    cm: cmodel,
    sm: smodel,
    tbar: [{xtype: 'tbfill'}, searchText,clearTextButton,searchButton],
    bbar: bbarpaging,
    listeners: {
      rowdblclick: function(){

      }
    },
    view: new Ext.grid.GroupingView({
      forceFit:true,
      groupTextTpl: '{text}'
    })
  });

  // GROUPS
  var proxyGroups = new Ext.data.HttpProxy({
    api: {
      read: '../groups/groups_Ajax?action=groupsList'
    }
  });

  var readerGroups = new Ext.data.JsonReader( {
    root: 'groups',
    totalProperty: 'total_groups',
    idProperty: 'GRP_UID',
    fields : [
      {name : 'GRP_UID'},
      {name : 'GRP_STATUS'},
      {name : 'CON_VALUE'},
      {name : 'GRP_TASKS', type: 'int'},
      {name : 'GRP_USERS', type: 'int'},
      {name : 'GRP_UX'}
    ]
  });

  // The new DataWriter component.
  var writerGroups = new Ext.data.JsonWriter({
      encode: true,
      writeAllFields: false
  });

  storeGroups = new Ext.data.GroupingStore({
    proxy: proxyGroups,
    reader: readerGroups,
    writer: writerGroups,  // <-- plug a DataWriter into the store just as you would a Reader
    autoSave: false // <-- false would delay executing create, update, destroy requests until specifically told to do so with some [save] buton.
  });

  cmodelGroups = new Ext.grid.ColumnModel({
    viewConfig: {
      cls:"x-grid-empty",
      emptyText: _('ID_NO_RECORDS_FOUND')
    }
    ,
    columns: [
      {id:'GRP_UID', dataIndex: 'USR_UID', hidden:true, hideable:false},
      {header: _('ID_GROUP_NAME'), dataIndex: 'CON_VALUE', width: 100, align:'left'},
      {header: _('ID_STATUS'), dataIndex: 'GRP_STATUS', width: 100, align:'center', renderer: render_status},
      {header: _('ID_USER_EXPERIENCE'),
        dataIndex: 'GRP_UX',
        width: 50,
        editor: new Ext.form.ComboBox({
          listClass: 'x-combo-list-small',
          mode: 'local',
          id:'GRP_UXCombo',
          displayField:'name',
          lazyRender: true,
          triggerAction: 'all',
          valueField:'id',
          editable: false,
          store: new Ext.data.ArrayStore({
            fields: ['id', 'name'],
            data : uxTypes
          }),
          listeners: {
            select: function(a, b) {
              Ext.Ajax.request({
                url: '../adminProxy/uxGroupUpdate',
                params: {GRP_UID: groupsGrid.getSelectionModel().selection['record'].data['GRP_UID'],
                         GRP_UX:  Ext.getCmp('GRP_UXCombo').getValue()},
                success: function(result, request) {
                  var response = Ext.decode(result.responseText);
                  if (!response.success) {
                    a.setValue('NORMAL');
                    PMExt.warning(_('ID_WARNING'), _('ID_ADMINS_CANT_USE_UXS') + '<br/> <b>' + _('ID_USERS_LIST') + ':</b> ' + response.users);
                  }
                  else {
                    a.fireEvent('blur');
                  }
                }
              });
            },
            specialkey: function (f, e) {
              if (e.getKey() == e.ENTER) {
                return false;
              }
              return true;
            }
          }
        })
      }
    ]
  });

  comboPageSizeGroups = new Ext.form.ComboBox({
    typeAhead     : false,
    mode          : 'local',
    triggerAction : 'all',
    store: new Ext.data.SimpleStore({
      fields: ['size'],
       data: [['20'],['30'],['40'],['50'],['100']],
       autoLoad: true
    }),
    valueField: 'size',
    displayField: 'size',
    width: 50,
    editable: false,
    listeners:{
      select: function(c,d,i){
        //UpdatePageConfig(d.data['size']);
        bbarpaging.pageSize = parseInt(d.data['size']);
        bbarpaging.moveFirst();
      }
    }
  });
  comboPageSizeGroups.setValue(pageSize);

  bbarpagingGroups = new Ext.PagingToolbar({
    pageSize: pageSize,
    store: storeGroups,
    displayInfo: true,
    displayMsg: _('ID_GRID_PAGE_DISPLAYING_GROUPS_MESSAGE') + '&nbsp; &nbsp; ',
    emptyMsg: _('ID_GRID_PAGE_NO_GROUPS_MESSAGE'),
    items: ['-',_('ID_PAGE_SIZE')+':',comboPageSizeGroups]
  });

  var searchButtonGroups = new Ext.Action({
    text: _('ID_SEARCH'),
    handler: DoSearchGroups
  });

  var searchTextGroups = new Ext.form.TextField ({
    id: 'searchTxtGroups',
    ctCls:'pm_search_text_field',
    allowBlank: true,
    width: 150,
    emptyText: _('ID_ENTER_SEARCH_TERM'),//'enter search term',
    listeners: {
      specialkey: function(f,e){
        if (e.getKey() == e.ENTER) {
          DoSearchGroups();
        }
      }
    }
  });

  var clearTextButtonGroups = new Ext.Action({
    text: 'X',
     ctCls:'pm_search_x_button',
     handler: GridByDefaultGroups
  });

  groupsGrid = new Ext.grid.EditorGridPanel({
    title : _('ID_GROUPS'),
    //region: 'center',
    layout: 'fit',
    id: 'groupsGrid',
    height:100,
    autoWidth : true,
    stateful : true,
    stateId : 'grid',
    enableColumnResize: true,
    enableHdMenu: true,
    frame:false,
    columnLines: false,
    viewConfig: {
      forceFit:true
    },
    clicksToEdit: 1,
    store: storeGroups,
    cm: cmodelGroups,
    //sm: smodel,
    tbar: [{xtype: 'tbfill'}, searchTextGroups,clearTextButtonGroups,searchButtonGroups],
    bbar: bbarpagingGroups,
    // listeners: {
    //   rowdblclick: EditGroupWindow
    // },
    view: new Ext.grid.GroupingView({
      forceFit:true,
      groupTextTpl: '{text}',
      cls:"x-grid-empty",
      emptyText: _('ID_NO_RECORDS_FOUND')
    })
  });

  store.load();
  storeGroups.load();

  viewport = new Ext.Viewport({
    layout: 'fit',
    autoScroll: false,
    items: [{
      xtype: 'tabpanel',
      region: 'center',
      activeTab: 0,
      items : [
        usersGrid,
        groupsGrid
      ]
    }]
  });
});


//Render Full Name
full_name = function(v,x,s){
  return _FNF(v, s.data.USR_FIRSTNAME, s.data.USR_LASTNAME);
};

//Render Status
render_status = function(v){
  switch(v){
  case 'ACTIVE': return '<font color="green">' + _('ID_ACTIVE') + '</font>'; break;
  case 'INACTIVE': return '<font color="red">' + _('ID_INACTIVE') + '</font>'; break;
  case 'VACATION': return '<font color="blue">' + _('ID_VACATION') + '</font>'; break;
  }
};

//Render Due Date
render_duedate = function(v,x,s){
  if (s.data.DUE_DATE_OK)
    return _DF(v);
  else
    return '<font color="red">' + _DF(v) + '</font>';
};

render_lastlogin = function(v){
	return _DF(v);
};

//Load Grid By Default
GridByDefault = function(){
  searchText.reset();
  usersGrid.store.load();
};

//Do Search Function
DoSearch = function(){
  usersGrid.store.load({params: {textFilter: searchText.getValue()}});
};

GridByDefaultGroups = function(){
  searchText.reset();
  groupsGrid.store.load();
};

//Do Search Function
DoSearchGroups = function(){
  groupsGrid.store.load({params: {textFilter: Ext.getCmp('searchTxtGroups').getValue()}});
};



//Update Page Size Configuration
UpdatePageConfig = function(pageSize){
  Ext.Ajax.request({
  url: 'users_Ajax',
  params: {'function':'updatePageSize', size: pageSize}
  });
};

//Update Authentication Source Filter
UpdateAuthSource = function(index){
  searchText.reset();
  usersGrid.store.load({params: {auths: index}});
};

