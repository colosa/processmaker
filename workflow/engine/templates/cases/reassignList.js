
  var proxyUsersToReassignList = new Ext.data.HttpProxy({
    api: {
      read :   'proxyReassignUsersList'
    }
  });

  var readerUsersToReassignList = new Ext.data.JsonReader({
    //totalProperty: 'totalCount',
    //successProperty: 'success',
    //idProperty: 'index',
    root: 'data',
    fields: [
        // map Record's 'firstname' field to data object's key of same name
        {name: 'userUid', mapping: 'userUid'},
        // map Record's 'job' field to data object's 'occupation' key
        {name: 'userFullname', mapping: 'userFullname'}
    ]

    //messageProperty: 'message'
    }
  );


  // The new DataWriter component.
  //currently we are not using this in casesList, but it is here just for complete definition
  var writerUsersToReassignList = new Ext.data.JsonWriter({
    encode: true,
    writeAllFields: true
  });

  
  var storeUsersToReassign = new Ext.data.Store({
    remoteSort: false,
    autoLoad:true,
    proxy : proxyUsersToReassignList,
    reader: readerUsersToReassignList,
    writer: writerUsersToReassignList,  // <-- plug a DataWriter into the store just as you would a Reader
    autoSave: false // <-- false would delay executing create, update, destroy requests until specifically told to do so with some [save] buton.
  });
  
  Ext.util.Format.comboRenderer = function(combo){
    return function(value){
      var record   = combo.findRecord(combo.valueField, value);
      //getting the parent gridpanel.
      /*var gp = combo.findParentBy (
        function (ct, cmt) {return (ct instanceof Ext.grid.GridPanel) ? true : false;}
      );*/

      //storeUsersToReassign.load();
      //alert(gp);
      // var record = array();
      return record ? record.get(combo.displayField) : combo.valueNotFoundText;
    }
  }

  // create the combo instance
  var comboUsersToReassign = new Ext.form.ComboBox({

    fieldLabel    : _('ID_SEARCH'),    
    editable      : true,
    forceSelection: false,    
    minChars      : 0,
    valueField    : 'userId',
    displayField  : 'userFullname',
    selectOnFocus : true,
    typeAhead     : false,
    autocomplete  : true,
    hideTrigger   : Boolean,
    alignTo       : 'right',
    mode          : 'remote',
    triggerAction : 'all',
    emptyText     : _('ID_ENTER_SEARCH_TERM'),
    disabled      : true,
    width         : 280,
    boxMaxWidth   : 180,

    //lazyRender    : true,
//    store         : new Ext.data.Store(),
    store         : storeUsersToReassign,
    listeners:{     
      'select': function(comp, record, index) {
        var row = Ext.getCmp('TasksToReassign').getSelectionModel().getSelected();
        row.set('APP_REASSIGN_USER_UID', record.get('userUid'));
        row.set('APP_REASSIGN_USER', record.get('userFullname'));
        this.setValue(record.get('userFullname'));
      }
    }

  });

//alert (this.fields.);