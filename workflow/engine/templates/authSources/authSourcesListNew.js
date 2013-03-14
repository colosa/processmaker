/*
 * @author Carlos P.C <carlos@colosa.com, pckrlos@gmail.com>
 * Oct 20th, 2011
 */
Ext.onReady(function(){

var storeAuthSources = new Ext.data.GroupingStore({
    autoLoad: true,
    proxy : new Ext.data.HttpProxy({
      url: 'authSources_Ajax?action=authSourcesNew&cmb=yes'
    }),
    reader : new Ext.data.JsonReader({
      root: 'sources',
      fields: [
        {name: 'sType'},
        {name: 'sLabel'}
      ]
    })
  });

var my_values = [
    ['ldap'],
    ['krlos']
];
var cboxAuthSourse = new Ext.form.ComboBox({
    fieldLabel: _('ID_PROVIDER'),
    hiddenName: 'AUTH_SOURCE_PROVIDER',
    mode: 'local',
    triggerAction: 'all',
    store: storeAuthSources,
    valueField: 'sType',
    displayField: 'sLabel',
    emptyText: _('ID_CHOOSE_OPTION') + '...',
    width: 160,
    editable: false,
    //value: _('ID_ALL'),
    listeners:{
      select: function(c,d,i){
      //nothing to do
      }
    }
  });

  componAuthSourse = new Ext.form.FieldSet({
    title: _('ID_AVAILABLE_AUTHENTICATION_SOURCES'),
    items: [
      cboxAuthSourse
    ]    
  });

  formAuthSourceOptoins = new Ext.FormPanel({
    id:'formAuthSourceOptoins',
    labelWidth: 250,
    labelAlign: 'right',
    autoScroll: true,
    fileUpload: true,
    width:800,
    bodyStyle:'padding:10px',
    waitMsgTarget: true,
    frame: true,
    defaults: {
      anchor: '100%',
      allowBlank: false,
      resizable: true,
      msgTarget: 'side',
      align:'center'
    },
    items:[
    componAuthSourse
      ],
    buttons: [
      {
        text: _('ID_CONTINUE'),
        handler: gotypesAuthSources
      },
      {
        text: _('ID_CANCEL'),
        handler: goBackform
      }
    ]
    
  });

    formAuthSourceOptoins.render(document.body);
    
 });
 function goBackform(){
     window.location = 'authSources_List';
 }
 function gotypesAuthSources(){ 
     /*if(formAuthSourceOptoins.getForm().findField('AUTH_SOURCE_PROVIDER').getValue()=='ldap')
       window.location = 'authSources_kindof';
      else
       window.location = 'authSources_New?AUTH_SOURCE_PROVIDER='+formAuthSourceOptoins.getForm().findField('AUTH_SOURCE_PROVIDER').getValue();
       return false;*/
    formAuthSourceOptoins.getForm().submit({ 
    url: '../adminProxy/testingOption',
    params: {
    action     : 'test',
    optionAuthS: formAuthSourceOptoins.getForm().findField('AUTH_SOURCE_PROVIDER').getValue()
    },
    method: 'POST',
    waitMsg : _('ID_LOADING_GRID'),
    timeout : 500,
    success: function(f,a){
    resp = Ext.util.JSON.decode(a.response.responseText);
//                            alert(resp.optionAuthS);return false;
//                            alert(resp.sUID);return false;
    if (resp.success){
      if(resp.optionAuthS=='ldap')
//       window.location = 'authSources_kindof?sUID='+resp.sUID+'&sprovider='+resp.optionAuthS;
        window.location = 'authSources_kindof?sprovider='+resp.optionAuthS;
      else
       window.location = 'authSources_New?AUTH_SOURCE_PROVIDER='+resp.optionAuthS;
    }

    },
    failure: function(f,a){
        if (a.failureType === Ext.form.Action.CONNECT_FAILURE){
            Ext.Msg.alert( _('ID_FAILURE'), _('ID_SERVER_REPORTED')+':'+a.response.status+' '+a.response.statusText);
        }
        if (a.failureType === Ext.form.Action.SERVER_INVALID){
            Ext.Msg.alert( _('ID_WARNING'), _('ID_YOU_HAVE_ERROR'));
        }
    }
});
 }