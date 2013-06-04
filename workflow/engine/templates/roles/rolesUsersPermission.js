/*
 * @author: Qennix
 * Jan 19th, 2011
 */

//Keyboard Events
new Ext.KeyMap(document, {
	 key: Ext.EventObject.F5,
     fn: function(keycode, e) {
    	if (! e.ctrlKey) {
    		if (Ext.isIE) {
            // IE6 doesn't allow cancellation of the F5 key, so trick it into
            // thinking some other key was pressed (backspace in this case)
    			e.browserEvent.keyCode = 8;
    		}
    		e.stopEvent();
    		document.location = document.location;
    	}else{
    		Ext.Msg.alert( _('ID_REFRESH_LABEL'), _('ID_REFRESH_MESSAGE'));
    	}
    }
});

var storeP;
var storeA;
var cmodelP;
var smodelA;
var smodelP;
var storeU;
var storeX;
var cmodelU;
var smodelU;
var smodelX;

var availableGrid;
var assignedGrid;
var availableUGrid;
var assignedUGrid;

var PermissionsPanel;
var UsersPanel;
var northPanel;
var tabsPanel;
var viewport;

var assignButton;
var assignAllButton;
var removeButton;
var removeAllButton;
var assignUButton;
var assignUAllButton;
var removeUButton;
var removeUAllButton;
var backButton;
var editForm;

var sw_func_permissions;
var sw_func_users;

var pm_admin = '00000000000000000000000000000002';

Ext.onReady(function(){
	
	sw_func_permissions = false;
	sw_func_users = false;
	
	  editPermissionsButton = new Ext.Action({
	    text: _('ID_EDIT_PERMISSIONS'),
	    iconCls: 'button_menu_ext ss_sprite  ss_key_add',
	    handler: EditPermissionsAction
	  });
	  
	  editPermissionsContentsButton = new Ext.Action({
	    text: _('ID_EDIT_PERMISSIONS_CONTENT'),
	    iconCls: 'button_menu_ext ss_sprite  ss_key_add',
	    handler: EditPermissionsContentsAction
	  });

	  cancelEditPermissionsButton = new Ext.Action({
	    text: _('ID_CLOSE'),
	    iconCls: 'button_menu_ext ss_sprite ss_cancel',
	    handler: CancelEditPermissionsAction
	  });
	  
	  editPermissionsUButton = new Ext.Action({
		    text: _('ID_ASSIGN_USERS'),
		    iconCls: 'button_menu_ext ss_sprite  ss_user_add',
		    handler: EditPermissionsActionU
		  });

		  cancelEditPermissionsUButton = new Ext.Action({
		    text: _('ID_CLOSE'),
		    iconCls: 'button_menu_ext ss_sprite ss_cancel',
		    handler: CancelEditPermissionsActionU
		  });
	
	backButton = new Ext.Action({
		text: _('ID_BACK'),
		iconCls: 'button_menu_ext ss_sprite ss_arrow_redo',
		handler: BackToRoles
	});
    
	storeP = new Ext.data.GroupingStore( {
        proxy : new Ext.data.HttpProxy({
            url: 'data_rolesPermissions?rUID=' + ROLES.ROL_UID + '&type=list'
          }),
    	reader : new Ext.data.JsonReader( {
    		root: 'permissions',
    		fields : [
    		    {name : 'PER_UID'},
    		    {name : 'PER_CODE'},
    		    {name : 'PER_NAME'},
    		    {name : 'PER_CREATE_DATE'},
    		    {name : 'PER_STATUS'}
    		    ]
    	})
    });
	
	storeA = new Ext.data.GroupingStore( {
        proxy : new Ext.data.HttpProxy({
            url: 'data_rolesPermissions?rUID=' + ROLES.ROL_UID + '&type=show'
          }),
    	reader : new Ext.data.JsonReader( {
    		root: 'permissions',
    		fields : [
    		    {name : 'PER_UID'},
    		    {name : 'PER_CODE'},
    		    {name : 'PER_NAME'},
    		    {name : 'PER_CREATE_DATE'},
    		    {name : 'PER_STATUS'}
    		    ]
    	})
    });
	
	cmodelP = new Ext.grid.ColumnModel({
        defaults: {
            width: 50,
            sortable: true
        },
        columns: [
            {id:'PER_UID', dataIndex: 'PER_UID', hidden:true, hideable:false},
            {header: _('ID_PERMISSION_CODE'), dataIndex: 'PER_CODE', width: 60, align:'left', hidden: !PARTNER_FLAG ? false : true},
            {header: _('ID_PERMISSION_NAME'), dataIndex: 'PER_NAME', width: 60, align:'left'}
        ]
    });
	
	smodelA = new Ext.grid.RowSelectionModel({
		selectSingle: false,
		listeners:{
			selectionchange: function(sm){
    			switch(sm.getCount()){
    			case 0: Ext.getCmp('assignButton').disable(); break;
    			default: Ext.getCmp('assignButton').enable(); break;	
    			}
    		}
		}
	});
	
	smodelP = new Ext.grid.RowSelectionModel({
		selectSingle: false,
		listeners:{
			selectionchange: function(sm){
                switch (sm.getCount()) {
                case 0: Ext.getCmp('removeButton').disable(); break;
                default:
                    Ext.getCmp('removeButton').enable();
                    if (ROLES.ROL_UID == pm_admin) {
                        var permissionUid = assignedGrid.getSelectionModel().getSelections();
                        permissionUid = permissionUid[0].get('PER_UID');
                        for (var i=0; i < permissionsAdmin.length; i++)
                        {
                            if (permissionUid == permissionsAdmin[i]['PER_UID']) {
                                Ext.getCmp('removeButton').disable();
                                break;
                            }
                        }
                    }
                    break;
                }
            }
        }
    });

	searchTextA = new Ext.form.TextField ({
        id: 'searchTextA',
        ctCls:'pm_search_text_field',
        allowBlank: true,
        width: 110,
        emptyText: _('ID_ENTER_SEARCH_TERM'),
        listeners: {
          specialkey: function(f,e){
            if (e.getKey() == e.ENTER) {
            	DoSearchA();
            }
          }
        }
    });
	
	clearTextButtonA = new Ext.Action({
    	text: 'X',
    	ctCls:'pm_search_x_button',
    	handler: GridByDefaultA
    });
	
	searchTextP = new Ext.form.TextField ({
        id: 'searchTextP',
        ctCls:'pm_search_text_field',
        allowBlank: true,
        width: 110,
        emptyText: _('ID_ENTER_SEARCH_TERM'),
        listeners: {
          specialkey: function(f,e){
            if (e.getKey() == e.ENTER) {
            	DoSearchP();
            }
          }
        }
    });
	
	clearTextButtonP = new Ext.Action({
    	text: 'X',
    	ctCls:'pm_search_x_button',
    	handler: GridByDefaultP
    });
	
  	availableGrid = new Ext.grid.GridPanel({
  		    layout			: 'fit',
  		    title           : _('ID_AVAILABLE_PERMISSIONS'),
  		    region          : 'center',
        	ddGroup         : 'assignedGridDDGroup',
            store           : storeA,
            cm          	: cmodelP,
            sm				: smodelA,
            enableDragDrop  : true,
            stripeRows      : true,
            autoExpandColumn: 'PER_CODE',
            iconCls			: 'icon-grid',
            id				: 'availableGrid',
        	height			: 100,
        	autoWidth 		: true,
        	stateful 		: true,
        	stateId 		: 'grid',
        	enableColumnResize : true,
        	enableHdMenu	: true,
        	frame			: false,
        	columnLines		: false,
        	viewConfig		: {forceFit:true},
            tbar: [cancelEditPermissionsButton, {xtype: 'tbfill'},'-',searchTextA,clearTextButtonA],
            //bbar: [{xtype: 'tbfill'}, assignAllButton],
            listeners: {rowdblclick: AssignPermissionAction},
            hidden: true
    });

  	assignedGrid = new Ext.grid.GridPanel({
  		    layout			: 'fit',
  		    title: _('ID_ASSIGNED_PERMISSIONS'),
  			ddGroup         : 'availableGridDDGroup',
            store           : storeP,
            cm          	: cmodelP,
            sm				: smodelP,
            enableDragDrop  : (ROLES.ROL_UID==pm_admin) ? false : true,
            stripeRows      : true,
            autoExpandColumn: 'PER_CODE',
            iconCls			: 'icon-grid',
            id				: 'assignedGrid',
        	height			: 100,
        	autoWidth 		: true,
        	stateful 		: true,
        	stateId 		: 'grid',
        	enableColumnResize : true,
        	enableHdMenu	: true,
        	frame			: false,
        	columnLines		: false,
        	viewConfig		: {forceFit:true},
            tbar: [editPermissionsButton, /*editPermissionsContentsButton,*/ {xtype: 'tbfill'},'-',searchTextP,clearTextButtonP],
            //bbar: [{xtype: 'tbfill'},removeAllButton],
        	listeners: {rowdblclick: function(){
        	      (availableGrid.hidden)? DoNothing() :RemovePermissionAction();}},
        	view: new Ext.grid.GroupingView({
              forceFit:true,
              groupTextTpl: '{text}'
            })
    });
  	
  	buttonsPanel = new Ext.Panel({
	    width	 	 : 40,
		layout       : {
            type:'vbox',
            padding:'0',
            pack:'center',
            align:'center'
        },
        defaults:{margins:'0 0 35 0'},
        items:[
               {xtype:'button',text: '>', handler: AssignPermissionAction, id: 'assignButton', disabled: true},
               {xtype:'button',text: '&lt;', handler: RemovePermissionAction, id: 'removeButton', disabled: true},
               {xtype:'button',text: '>>', handler: AssignAllPermissionsAction, id: 'assignButtonAll', disabled: false},
               {xtype:'button',text: '&lt;&lt;', handler: RemoveAllPermissionsAction, id: 'removeButtonAll', disabled: false}
               ],
        hidden : true
    });
  	
  	RefreshPermissions();

  	//PERMISSIONS DRAG AND DROP PANEL
    PermissionsPanel = new Ext.Panel({
    	    title		 : _('ID_PERMISSIONS'),
    		autoWidth	 : true,
    		layout       : 'hbox',
   		    defaults     : { flex : 1 }, //auto stretch
    		layoutConfig : { align : 'stretch' },
    		items        : [availableGrid,buttonsPanel,assignedGrid],
    		viewConfig	 : {forceFit:true}
    		//bbar: [{xtype: 'tbfill'},editPermissionsButton, cancelEditPermissionsButton]

    });
    
    storeU = new Ext.data.GroupingStore({
    	proxy : new Ext.data.HttpProxy({
            url: 'data_rolesUsers?rUID=' + ROLES.ROL_UID + '&type=list'
          }),
    	reader : new Ext.data.JsonReader( {
    		root: 'users',
    		fields : [
    		    {name : 'USR_UID'},
    		    {name : 'USR_USERNAME'},
    		    {name : 'USR_FIRSTNAME'},
    		    {name : 'USR_LASTNAME'}
    		    ]
    	})
    });
    
    storeX = new Ext.data.GroupingStore({
    	proxy : new Ext.data.HttpProxy({
            url: 'data_rolesUsers?rUID=' + ROLES.ROL_UID + '&type=show'
          }),
    	reader : new Ext.data.JsonReader( {
    		root: 'users',
    		fields : [
    		    {name : 'USR_UID'},
    		    {name : 'USR_USERNAME'},
    		    {name : 'USR_FIRSTNAME'},
    		    {name : 'USR_LASTNAME'}
    		    ]
    	})
    });
    
    cmodelU = new Ext.grid.ColumnModel({
        defaults: {
            width: 50,
            sortable: true
        },
        columns: [
            {id:'USR_UID', dataIndex: 'USR_UID', hidden:true, hideable:false},
            {header: _('ID_FIRST_NAME'), dataIndex: 'USR_FIRSTNAME', width: 60, align:'left'},
            {header: _('ID_LAST_NAME'), dataIndex: 'USR_LASTNAME', width: 60, align:'left'},
            {header: _('ID_USER_NAME'), dataIndex: 'USR_USERNAME', width: 60, align:'left'}
        ]
    });
    
    smodelX = new Ext.grid.RowSelectionModel({
		selectSingle: false,
		listeners:{
			selectionchange: function(sm){
    			switch(sm.getCount()){
    			case 0: Ext.getCmp('assignUButton').disable(); break;
    			default: Ext.getCmp('assignUButton').enable(); break;	
    			}
    		}
		}
	});
	
	smodelU = new Ext.grid.RowSelectionModel({
		selectSingle: false,
		listeners:{
			selectionchange: function(sm){
    			switch(sm.getCount()){
    			case 0: Ext.getCmp('removeUButton').disable(); break;
    			default: Ext.getCmp('removeUButton').enable(); break;	
    			}
    		}
		}
	});
	
	searchTextU = new Ext.form.TextField ({
        id: 'searchTextU',
        ctCls:'pm_search_text_field',
        allowBlank: true,
        width: 110,
        emptyText: _('ID_ENTER_SEARCH_TERM'),
        listeners: {
          specialkey: function(f,e){
            if (e.getKey() == e.ENTER) {
            	DoSearchU();
            }
          }
        }
    });
	
	clearTextButtonU = new Ext.Action({
    	text: 'X',
    	ctCls:'pm_search_x_button',
    	handler: GridByDefaultU
    });
	
	searchTextX = new Ext.form.TextField ({
        id: 'searchTextX',
        ctCls:'pm_search_text_field',
        allowBlank: true,
        width: 110,
        emptyText: _('ID_ENTER_SEARCH_TERM'),
        listeners: {
          specialkey: function(f,e){
            if (e.getKey() == e.ENTER) {
            	DoSearchX();
            }
          }
        }
    });
	
	clearTextButtonX = new Ext.Action({
    	text: 'X',
    	ctCls:'pm_search_x_button',
    	handler: GridByDefaultX
    });
    
    availableUGrid = new Ext.grid.GridPanel({
	    layout			: 'fit',
	    title			: _('ID_AVAILABLE_USERS'),
	    region          : 'center',
    	ddGroup         : 'assignedUGridDDGroup',
        store           : storeX,
        cm          	: cmodelU,
        sm				: smodelX,
        enableDragDrop  : true,
        stripeRows      : true,
        autoExpandColumn: 'USR_USERNAME',
        iconCls			: 'icon-grid',
        id				: 'availableUGrid',
    	height			: 100,
    	autoWidth 		: true,
    	stateful 		: true,
    	stateId 		: 'grid',
    	enableColumnResize : true,
    	enableHdMenu	: true,
    	frame			: false,
    	columnLines		: false,
    	viewConfig		: {forceFit:true},
        tbar: [cancelEditPermissionsUButton,{xtype: 'tbfill'},'-',searchTextU, clearTextButtonU],
        //bbar: [{xtype: 'tbfill'}, assignUAllButton],
        listeners: {rowdblclick: AssignUserAction},
        hidden : true
    });
    
    assignedUGrid = new Ext.grid.GridPanel({
	    layout			: 'fit',
	    title			: _('ID_ASSIGNED_USERS'),
		ddGroup         : 'availableUGridDDGroup',
        store           : storeU,
        cm          	: cmodelU,
        sm				: smodelU,
        enableDragDrop  : true,
        stripeRows      : true,
        autoExpandColumn: 'USR_USERNAME',
        iconCls			: 'icon-grid',
        id				: 'assignedUGrid',
    	height			: 100,
    	autoWidth 		: true,
    	stateful 		: true,
    	stateId 		: 'grid',
    	enableColumnResize : true,
    	enableHdMenu	: true,
    	frame			: false,
    	columnLines		: false,
    	viewConfig		: {forceFit:true},
        tbar: [editPermissionsUButton,{xtype: 'tbfill'},'-',searchTextX, clearTextButtonX],
        //bbar: [{xtype: 'tbfill'},removeUAllButton],
    	listeners: {rowdblclick: function(){
  	      (availableUGrid.hidden)? DoNothing() : RemoveUserAction();}} 
    });
    
    buttonsUPanel = new Ext.Panel({
	    width	 	 : 40,
		layout       : {
            type:'vbox',
            padding:'0',
            pack:'center',
            align:'center'
        },
        defaults:{margins:'0 0 35 0'},
        items:[
               {xtype:'button',text: '>', handler: AssignUserAction, id: 'assignUButton', disabled: true},
               {xtype:'button',text: '&lt;', handler: RemoveUserAction, id: 'removeUButton', disabled: true},
               {xtype:'button',text: '>>', handler: AssignAllUsersAction, id: 'assignUButtonAll', disabled: false},
               {xtype:'button',text: '&lt;&lt;', handler: RemoveAllUsersAction, id: 'removeUButtonAll', disabled: false}
               ],
        hidden: true
    });
    
    RefreshUsers();

  	//PERMISSIONS DRAG AND DROP PANEL
    UsersPanel = new Ext.Panel({
    	    title		 : _('ID_USERS'),
    		autoWidth	 : true,
    		layout       : 'hbox',
   		    defaults     : { flex : 1 }, //auto stretch
    		layoutConfig : { align : 'stretch' },
    		items        : [availableUGrid,buttonsUPanel,assignedUGrid],
    		viewConfig	 : {forceFit:true}//,
    		//bbar: [{xtype: 'tbfill'},editPermissionsUButton, cancelEditPermissionsUButton]
    });
    
    //NORTH PANEL WITH TITLE AND ROLE DETAILS
    northPanel = new Ext.Panel({
    	region: 'north',
    	xtype: 'panel',
    	tbar: ['<b>'+_('ID_ROLE') + ' : ' + ROLES.ROL_CODE+'</b>',{xtype: 'tbfill'},backButton]
    });
    
    //TABS PANEL
    tabsPanel = new Ext.TabPanel({
       	region: 'center',
    	activeTab: ROLES.CURRENT_TAB,
    	items:[UsersPanel,PermissionsPanel],
    	listeners:{
    		tabchange: function(p,t){
    			switch(t.title){
    			case _('ID_PERMISSIONS'):
    				sw_func_permissions ? DoNothing() : DDLoadPermissions();
    				break;
    			case _('ID_USERS'):
    				sw_func_users ? DoNothing() : DDLoadUsers();
    				break;
    			}
    		}
    	}
    });
    
    //LOAD ALL PANELS
    viewport = new Ext.Viewport({
    	layout: 'border',
    	items: [northPanel, tabsPanel]
    });
    
});

//Do Nothing Function
DoNothing = function(){};

//Return to Roles Main Page
BackToRoles = function(){
	location.href = 'roles_List';
};

//Loads Drag N Drop Functionality for Permissions
DDLoadPermissions = function(){
	//PERMISSIONS DRAG N DROP AVAILABLE
	if (ROLES.ROL_UID!=pm_admin){
    var availableGridDropTargetEl =  availableGrid.getView().scroller.dom;
    var availableGridDropTarget = new Ext.dd.DropTarget(availableGridDropTargetEl, {
                    ddGroup    : 'availableGridDDGroup',
                    notifyDrop : function(ddSource, e, data){
                            var records =  ddSource.dragData.selections;
                            var arrAux = new Array();
                            for (var r=0; r < records.length; r++){
                            	arrAux[r] = records[r].data['PER_UID'];
                            }
                            DeletePermissionsRole(arrAux,RefreshPermissions,FailureProcess);
                            return true;
                    }
    });
	}
    //PERMISSIONS DRAG N DROP ASSIGNED
    var assignedGridDropTargetEl = assignedGrid.getView().scroller.dom;
    var assignedGridDropTarget = new Ext.dd.DropTarget(assignedGridDropTargetEl, {
                    ddGroup    : 'assignedGridDDGroup',
                    notifyDrop : function(ddSource, e, data){
                            var records =  ddSource.dragData.selections;
                            var arrAux = new Array();
                            for (var r=0; r < records.length; r++){
                            	arrAux[r] = records[r].data['PER_UID'];
                            }
                            SavePermissionsRole(arrAux,RefreshPermissions,FailureProcess);
                            return true;
                    }
     });
    sw_func_permissions = true;
};

DDLoadUsers = function(){
	//USERS DRAG N DROP AVAILABLE
	var availableUGridDropTargetEl =  availableUGrid.getView().scroller.dom;
    var availableUGridDropTarget = new Ext.dd.DropTarget(availableUGridDropTargetEl, {
                    ddGroup    : 'availableUGridDDGroup',
                    notifyDrop : function(ddSource, e, data){
                            var records =  ddSource.dragData.selections;
                            var arrAux = new Array();
                            for (var r=0; r < records.length; r++){
                            	arrAux[r] = records[r].data['USR_UID'];
                            }
                            DeleteUsersRole(arrAux,RefreshUsers,FailureProcess);
                            return true;
                    }
    });
	
    //USERS DRAG N DROP ASSIGNED
    var assignedUGridDropTargetEl = assignedUGrid.getView().scroller.dom;
    var assignedUGridDropTarget = new Ext.dd.DropTarget(assignedUGridDropTargetEl, {
                    ddGroup    : 'assignedUGridDDGroup',
                    notifyDrop : function(ddSource, e, data){
                            var records =  ddSource.dragData.selections;
                            var arrAux = new Array();
                            for (var r=0; r < records.length; r++){
                            	arrAux[r] = records[r].data['USR_UID'];
                            }
                            SaveUsersRole(arrAux,RefreshUsers,FailureProcess);
                            return true;
                    }
     });
     sw_func_users = true;
};

//REFRESH PERMISSION GRIDS
RefreshPermissions = function(){
	DoSearchA();
	DoSearchP();
};

//REFRESH USERS GRIDS
RefreshUsers = function(){
	DoSearchX();
	DoSearchU();
};

//FAILURE AJAX FUNCTION
FailureProcess = function(){
	Ext.Msg.alert(_('ID_ROLES'), _('ID_MSG_AJAX_FAILURE'));
};

//ASSIGN PERMISSION TO A ROLE
SavePermissionsRole = function(arr_per, function_success, function_failure){
	var sw_response;
	viewport.getEl().mask(_('ID_PROCESSING'));
	Ext.Ajax.request({
		url: 'roles_Ajax',
		params: {request: 'assignPermissionToRoleMultiple', ROL_UID: ROLES.ROL_UID, PER_UID: arr_per.join(',')},
		success: function(){
					function_success();
					viewport.getEl().unmask();
				  },
		failure: function(){
					function_failure();
					viewport.getEl().unmask();
		}
	});
};

//REMOVE PERMISSION FROM A ROLE
DeletePermissionsRole = function(arr_per, function_success, function_failure){
	var sw_response;
	viewport.getEl().mask(_('ID_PROCESSING'));
	Ext.Ajax.request({
		url: 'roles_Ajax',
		params: {request: 'deletePermissionToRoleMultiple', ROL_UID: ROLES.ROL_UID, PER_UID: arr_per.join(',')},
		success: function(){
			        function_success();
					viewport.getEl().unmask();			        
		},
		failure: function(){
					function_failure();
					viewport.getEl().unmask();
		}
	});
};

//AssignButton Functionality
AssignPermissionAction = function(){
	rowsSelected = availableGrid.getSelectionModel().getSelections();
	var arrAux = new Array();
	for(var a=0; a < rowsSelected.length; a++){
		arrAux[a] = rowsSelected[a].get('PER_UID');
	}
	SavePermissionsRole(arrAux,RefreshPermissions,FailureProcess);
};

//RemoveButton Functionality
RemovePermissionAction = function(){
    rowsSelected = assignedGrid.getSelectionModel().getSelections();
    var arrAux = new Array();
    var sw;
    for(var a=0; a < rowsSelected.length; a++){
        sw = true;
        if (ROLES.ROL_UID == pm_admin) {
            for (var i=0; i < permissionsAdmin.length; i++)
            {
                if (permissionsAdmin[i]['PER_UID'] == rowsSelected[a].get('PER_UID')) {
                    sw = false;
                    break;
                }
            }
        }
        if (sw) {
            arrAux[a] = rowsSelected[a].get('PER_UID');
        }
    }
    DeletePermissionsRole(arrAux,RefreshPermissions,FailureProcess);
};

//AssignALLButton Functionality
AssignAllPermissionsAction = function(){
	var allRows = availableGrid.getStore();
	var arrAux = new Array();
	if (allRows.getCount()>0){
		for (var r=0; r < allRows.getCount(); r++){
			row = allRows.getAt(r);
			arrAux[r] = row.data['PER_UID'];
		}
		SavePermissionsRole(arrAux,RefreshPermissions,FailureProcess);
	}
};

//RevomeALLButton Functionality
RemoveAllPermissionsAction = function(){
    var allRows = assignedGrid.getStore();
    var arrAux = new Array();
    if (allRows.getCount()>0){
        var sw;
        for (var r=0; r < allRows.getCount(); r++){
            row = allRows.getAt(r);
            sw = true;
            if (ROLES.ROL_UID == pm_admin) {
                for (var i=0; i < permissionsAdmin.length; i++)
                {
                    if (permissionsAdmin[i]['PER_UID'] == row.data['PER_UID']) {
                        sw = false;
                        break;
                    }
                }
            }
            if (sw) {
                arrAux[r] = row.data['PER_UID'];
            }
        }
        DeletePermissionsRole(arrAux,RefreshPermissions,FailureProcess);
    }
};

//ASSIGN USERS TO A ROLE
SaveUsersRole = function(arr_usr, function_success, function_failure){
	var sw_response;
	viewport.getEl().mask(_('ID_PROCESSING'));
	Ext.Ajax.request({
		url: 'roles_Ajax',
		params: {request: 'assignUserToRole', ROL_UID: ROLES.ROL_UID, aUsers: arr_usr.join(',')},
		success: function( result, request ){
				    var data = Ext.util.JSON.decode(result.responseText);
		            if( data.userRole ) {
		             Ext.Msg.show({
		                  title: _('ID_WARNING'),
		                  msg: _('ID_ADMINISTRATOR_ROLE_CANT_CHANGED'),
		                  animEl: 'elId',
		                  icon: Ext.MessageBox.WARNING,
		                  buttons: Ext.MessageBox.OK
		             });
		            }
					viewport.getEl().unmask();
					function_success();
				  },
		failure: function(){
					viewport.getEl().unmask();
					function_failure();
		}
	});
};

//REMOVE USERS FROM A ROLE
DeleteUsersRole = function(arr_usr, function_success, function_failure){
	var sw_response;
	viewport.getEl().mask(_('ID_PROCESSING'));
	Ext.Ajax.request({
		url: 'roles_Ajax',
		params: {request: 'deleteUserRoleMultiple', ROL_UID: ROLES.ROL_UID, USR_UID: arr_usr.join(',')},
		success: function(){
			        function_success();
			        viewport.getEl().unmask();
		},
		failure: function(){
					function_failure();
					viewport.getEl().unmask();
		}
	});
};

//AssignUButton Functionality
AssignUserAction = function(){
	rowsSelected = availableUGrid.getSelectionModel().getSelections();
	var arrAux = new Array();
	for(var a=0; a < rowsSelected.length; a++){
		arrAux[a] = rowsSelected[a].get('USR_UID');
	}
	SaveUsersRole(arrAux,RefreshUsers,FailureProcess);
};

//RemoveUButton Functionality
RemoveUserAction = function(){
	rowsSelected = assignedUGrid.getSelectionModel().getSelections();
	var arrAux = new Array();
	for(var a=0; a < rowsSelected.length; a++){
		arrAux[a] = rowsSelected[a].get('USR_UID');
	}
	DeleteUsersRole(arrAux,RefreshUsers,FailureProcess);
};

//AssignUALLButton Functionality
AssignAllUsersAction = function(){
	var allRows = availableUGrid.getStore();
	if (allRows.getCount()>0){
		Ext.Msg.confirm(_('ID_CONFIRM'), _('ID_MSG_CONFIRM_ASSIGN_ALL_USERS'),
	        function(btn, text){
	            if (btn=="yes"){
	            	var arrAux = new Array();
	            	for (var r=0; r < allRows.getCount(); r++){
	            		row = allRows.getAt(r);
	            		arrAux[r] = row.data['USR_UID'];
	            	}
	            	SaveUsersRole(arrAux,RefreshUsers,FailureProcess);
	            }
			}
		);
	}
};

//RevomeALLButton Functionality
RemoveAllUsersAction = function(){
	var allRows = assignedUGrid.getStore();
	var arrAux = new Array();
	if (allRows.getCount()>0){
		for (var r=0; r < allRows.getCount(); r++){
			row = allRows.getAt(r);
			arrAux[r] = row.data['USR_UID'];
		}
		DeleteUsersRole(arrAux,RefreshUsers,FailureProcess);
	}
};

//update the content table, using php layer & update the Extjs table
updatePermissionContent = function() {
  rowSelected = assignedGrid.getSelectionModel().getSelections();
  permission_name = editForm.getForm().findField('name').getValue();
  permission_name.trim();
  if (permission_name != '') {
      viewport.getEl().mask(_('ID_PROCESSING'));
     
      Ext.Ajax.request({
        url: 'roles_Ajax',
        params: {request: 'updatePermissionContent', PER_NAME: permission_name, PER_UID: rowSelected[0].get('PER_UID')},
        success: function(r,o) {
          viewport.getEl().unmask();
        },
        failure: function(r,o) {
          viewport.getEl().unmask();
        }
      });
  }
  Ext.getCmp('w').hide();
  editPermissionsContentsButton.enable();
  editPermissionsButton.enable(); 
};

//Close Popup Window
closeWindow = function(){
  Ext.getCmp('w').hide();
  editPermissionsContentsButton.enable();
  editPermissionsButton.enable();
};

editForm = new Ext.FormPanel({
    url: 'permissions_Ajax?request=updatePermission',
    frame: true,
    items:[
           {xtype: 'textfield', name: 'per_uid', hidden: true },
           {xtype: 'textfield', fieldLabel: _('ID_CODE'), name: 'code', width: 250, allowBlank: false, readOnly: true },
           {xtype: 'textfield', fieldLabel: _('ID_NAME'), name: 'name', width: 200, allowBlank: false},
          ],
    buttons: [
              {text: _('ID_SAVE'), handler: updatePermissionContent},
              {text: _('ID_CANCEL'), handler: closeWindow}
             ]
  });

//Edit Selected Permission
EditPermissionsWindow = function(){
  var permissionUid = assignedGrid.getSelectionModel().getSelections();
  if (permissionUid.length > 0){
    if (permissionUid[0].get('PER_UID') == '00000000000000000000000000000002'){
      PMExt.warning(_('ID_PERMISSION'),_('ID_PERMISSION_MSG'));
    }else{
      editForm.getForm().findField('per_uid').setValue(permissionUid[0].get('PER_UID'));
      editForm.getForm().findField('code').setValue(permissionUid[0].get('PER_CODE'));
      editForm.getForm().findField('name').setValue(permissionUid[0].get('PER_NAME'));
      w = new Ext.Window({
        autoHeight: true,
        id: 'w',
        modal: true,
        width: 420,
        title: _('ID_EDIT_PERMISSION_TITLE'),
        items: [editForm]
      });
      w.show();
    }
  }
};

//Function DoSearch Available
DoSearchA = function(){
	availableGrid.store.load({params: {textFilter: searchTextA.getValue()}});
};

//Function DoSearch Assigned
DoSearchP = function(){
	assignedGrid.store.load({params: {textFilter: searchTextP.getValue()}});
};

//Load Grid By Default Available Members
GridByDefaultA = function(){
	searchTextA.reset();
	availableGrid.store.load();
};

//Load Grid By Default Assigned Members
GridByDefaultP = function(){
	searchTextP.reset();
	assignedGrid.store.load();
};

//Function DoSearch Available
DoSearchU = function(){
	availableUGrid.store.load({params: {textFilter: searchTextU.getValue()}});
};

//Function DoSearch Assigned
DoSearchX = function(){
	assignedUGrid.store.load({params: {textFilter: searchTextX.getValue()}});
};

//Load Grid By Default Available Members
GridByDefaultU = function(){
	searchTextU.reset();
	availableUGrid.store.load();
};

//Load Grid By Default Assigned Members
GridByDefaultX = function(){
	searchTextX.reset();
	assignedUGrid.store.load();
};

//edit permissions action
EditPermissionsAction = function(){
  availableGrid.show();
  buttonsPanel.show();
  editPermissionsButton.disable();
  //cancelEditPermissionsButton.show();
  PermissionsPanel.doLayout();
};

EditPermissionsContentsAction = function(){
  //availableGrid.show();
  //buttonsPanel.show();
  editPermissionsContentsButton.disable();
  editPermissionsButton.disable();
  EditPermissionsWindow();
};

//CancelEditPermissions Function
CancelEditPermissionsAction = function(){
  availableGrid.hide();
  buttonsPanel.hide();
  editPermissionsButton.enable();
  //cancelEditPermissionsButton.hide();
  PermissionsPanel.doLayout();
};

//edit users action
EditPermissionsActionU = function(){
  availableUGrid.show();
  buttonsUPanel.show();
  editPermissionsUButton.disable();
  //cancelEditPermissionsUButton.show();
  UsersPanel.doLayout();
};

//CancelEditUsers Function
CancelEditPermissionsActionU = function(){
  availableUGrid.hide();
  buttonsUPanel.hide();
  editPermissionsUButton.enable();
  //cancelEditPermissionsUButton.hide();
  UsersPanel.doLayout();
};