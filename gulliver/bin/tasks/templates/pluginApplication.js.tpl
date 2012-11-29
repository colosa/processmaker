Ext.namespace("{className}");

{className}.application = {
  init:function(){
    storeUserProcess = function (n, r, i) {
      var myMask = new Ext.LoadMask(Ext.getBody(), {msg:"Load users..."});
      myMask.show();

      Ext.Ajax.request({
        url: "{className}ApplicationAjax",
        method: "POST",
        params: {"option": "LST", "pageSize": n, "limit": r, "start": i},
                         
        success:function (result, request) {
                  storeUser.loadData(Ext.util.JSON.decode(result.responseText));
                  myMask.hide();
                },
        failure:function (result, request) {
                  myMask.hide();
                  Ext.MessageBox.alert("Alert", "Failure users load");
                }
      });
    };
    
    onMnuContext = function(grid, rowIndex,e) {
      e.stopEvent();
      var coords = e.getXY();
      mnuContext.showAt([coords[0], coords[1]]);
    };
    
    //Variables declared in html file
    var pageSize = parseInt(CONFIG.pageSize);
    var message = CONFIG.message;
    
    //stores
    var storeUser = new Ext.data.Store({
      proxy:new Ext.data.HttpProxy({
        url:    "{className}ApplicationAjax",
        method: "POST"
      }),
      
      //baseParams: {"option": "LST", "pageSize": pageSize},
            
      reader:new Ext.data.JsonReader({
        root: "resultRoot",
        totalProperty: "resultTotal",
        fields: [{name: "ID"},
                 {name: "NAME"},
                 {name: "AGE"},
                 {name: "BALANCE"}
                ]
      }),
      
      //autoLoad: true, //First call
      
      listeners:{
        beforeload:function (store) {
          this.baseParams = {"option": "LST", "pageSize": pageSize};
        }
      }
    });
    
    var storePageSize = new Ext.data.SimpleStore({
      fields: ["size"],
      data: [["15"], ["25"], ["35"], ["50"], ["100"]],
      autoLoad: true
    });
    
    //
    var btnNew = new Ext.Action({
      id: "btnNew",
      
      text: "New",
      iconCls: "button_menu_ext ss_sprite ss_add",
      
      handler: function() {
        Ext.MessageBox.alert("Alert", message);
      }
    });
    
    var btnEdit = new Ext.Action({
      id: "btnEdit",
      
      text: "Edit",
      iconCls: "button_menu_ext ss_sprite ss_pencil",
      disabled: true,
      
      handler: function() {
        Ext.MessageBox.alert("Alert", message);
      }
    });
    
    var btnDelete = new Ext.Action({
      id: "btnDelete",
      
      text: "Delete",
      iconCls: "button_menu_ext ss_sprite ss_delete",
      disabled: true,
      
      handler: function() {
        Ext.MessageBox.alert("Alert", message);
      }
    });
    
    var btnSearch = new Ext.Action({
      id: "btnSearch",
      
      text: "Search",
      
      handler: function() {
        Ext.MessageBox.alert("Alert", message);
      }
    });
    
    var mnuContext = new Ext.menu.Menu({
      id: "mnuContext",
      
      items: [btnEdit, btnDelete]
    });
    
    var txtSearch = new Ext.form.TextField({
      id: "txtSearch",
      
      emptyText: "Enter search term",
      width: 150,
      allowBlank: true,
      
      listeners:{
        specialkey: function (f, e) {
          if (e.getKey() == e.ENTER) {
            Ext.MessageBox.alert("Alert", message);
          }
        }
      }
    });
    
    var btnTextClear = new Ext.Action({
      id: "btnTextClear",
      
      text: "X",
      ctCls: "pm_search_x_button",
      handler: function() {
        txtSearch.reset();
      }
    });
    
    var cboPageSize = new Ext.form.ComboBox({
      id: "cboPageSize",
      
      mode: "local",
      triggerAction: "all",
      store: storePageSize,
      valueField: "size",
      displayField: "size",
      width: 50,
      editable: false,
      
      listeners:{
        select: function (combo, record, index) {
          pageSize = parseInt(record.data["size"]);
          
          pagingUser.pageSize = pageSize;
          pagingUser.moveFirst();
        }
      }
    });
    
    var pagingUser = new Ext.PagingToolbar({
      id: "pagingUser",
      
      pageSize: pageSize,
      store: storeUser,
      displayInfo: true,
      displayMsg: "Displaying users " + "{" + "0" + "}" + " - " + "{" + "1" + "}" + " of " + "{" + "2" + "}",
      emptyMsg: "No roles to display",
      items: ["-", "Page size:", cboPageSize]
    });
       
    var cmodel = new Ext.grid.ColumnModel({
      defaults: {
        width:50,
        sortable:true
      },
      columns:[{id: "ID", dataIndex: "ID", hidden: true},
               {header: "Name", dataIndex: "NAME", align: "left"},
               {header: "Age", dataIndex: "AGE", width: 25, align: "center"},
               {header: "Balance", dataIndex: "BALANCE", width: 25, align: "left"}
              ]
    });
    
    var smodel = new Ext.grid.RowSelectionModel({
      singleSelect: true,
      listeners: {
        rowselect: function (sm) {
          btnEdit.enable();
          btnDelete.enable();
        },
        rowdeselect: function (sm) {
          btnEdit.disable();
          btnDelete.disable();
        }
      }
    });
    
    var grdpnlUser = new Ext.grid.GridPanel({
      id: "grdpnlUser",
      
      store: storeUser,
      colModel: cmodel,
      selModel: smodel,
      
      columnLines: true,
      viewConfig: {forceFit: true},
      enableColumnResize: true,
      enableHdMenu: true, //Menu of the column
      
      tbar: [btnNew, "-", btnEdit, btnDelete, "-", "->", txtSearch, btnTextClear, btnSearch],
      bbar: pagingUser,
      
      style: "margin: 0 auto 0 auto;",
      width: 550,
      height: 450, 
      title: "Users",      
      
      renderTo: "divMain",
      
      listeners:{
      }
    });
    
    //Initialize events
    storeUserProcess(pageSize, pageSize, 0);
    
    grdpnlUser.on("rowcontextmenu", 
      function (grid, rowIndex, evt) {
        var sm = grid.getSelectionModel();
        sm.selectRow(rowIndex, sm.isSelected(rowIndex));
      },
      this
    );
    
    grdpnlUser.addListener("rowcontextmenu", onMnuContext, this);
    
    cboPageSize.setValue(pageSize);
  }
}

Ext.onReady({className}.application.init, {className}.application);