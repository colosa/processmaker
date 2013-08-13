Ext.onReady(function() {
  var store = new Ext.data.JsonStore({
    proxy : new Ext.data.HttpProxy({
      url : '../adminProxy/getListImage', method: 'POST'
    }),
    root   : 'images',
    fields : [
      'name', 'url',
      {name : 'size',    type : 'float'},
      {name : 'lastmod', type : 'date', dateFormat: 'timestamp'},
      'thumb_url'
    ]
  });
  store.load();

  var tpl = new Ext.XTemplate(
      '<ul>',
        '<tpl for=".">',
          '<li class="pagedTableDefault" >',
          '<div class="thumb-wrap" id="{name}">',
          '<img src="{thumb_url}" title="{name}">',
          '</div>',
          '</li>',
          '<span class="x-editable">{shortName}</span></div>',
        '</tpl>',
        '<div class="x-clear"></div>',
      '</ul>'
  );
  var tplDetail = new Ext.XTemplate(
    '<div class="details">',
      '<tpl for=".">',
      '<img src="{thumb_url}"><div class="details-info">',
      '<b>Image Name:</b>',
      '<span>{name}</span>',
      '<span><a href="{url}" target="_blank">view original</a></span></div>',
      '</tpl>',
    '</div>'
  );
  var tbar = new Ext.Toolbar();
  tbar.add({
    text    : _('ID_APPLY'),
    icon    : '/images/ext/default/accept.png',
    id      : 'tbarAply',
    disabled: true,
    handler : function() {
      var records = datav.getSelectedRecords();
      if (records.length != 0) {
        if(records.length == 1) {

          var myMask = new Ext.LoadMask(Ext.getBody(), {msg:_('ID_LOADING')});
          myMask.show();

          var imageName = records[0].data.name;
          Ext.Ajax.request({
             url     : '../adminProxy/replacementLogo',
             method  : 'post',
             params  : {nameFunction: 'replacementLogo', NAMELOGO:imageName},
             success : function() {
              if (typeof parent.parent.Ext != 'undefined') {
                parent.parent.location.href = '../main?st=admin';
              }
              else {
                window.parent.window.parent.location.href = window.parent.window.parent.window.location.href
              }
             }
          });
        }
        else {
          PMExt.notify( _('ID_NOTICE'), _('ID_YOU_ARE_NOT_CAN_SELECT_PHOTO'));
        } 
      }
      else {
        PMExt.notify( _('ID_NOTICE'), _('ID_SELECT_AN_IMAGE'));
      }
    }
  });
  
  tbar.add({
    text    : _('ID_DELETE'),
    icon    : '/images/delete-16x16.gif',
    id      : 'tbarDelete',
    disabled: true,
    handler : function() {
      var records = datav.getSelectedRecords();
      var isCurrentLogo = false;
      if (records.length != 0) {
        if (records.length == 1) {
          var imgName = '';
          for (var i = 0; i < records.length; i++) {
            imgName = imgName + records[i].data.name + ';';
          }
          Ext.Ajax.request({
            url     : '../adminProxy/isCurrentLogo',
            method  : 'post',
            params  : { selectLogo : imgName },
            success : function(response) {
              store.load();
              oResponse = Ext.decode( response.responseText );
              if (oResponse.success == true) {
                 Ext.Msg.alert(_('ID_LOGO'), _('ID_SELECTED_IMAGE_IS_LOGO'));
                 isCurrentLogo = true;
              } 
            }
          });
          if(isCurrentLogo == false) {
            Ext.Msg.show({
               title  : _('ID_LOGO'),
               msg    : _('ID_DELETE_SELECTED_LOGO'),
               buttons: Ext.Msg.YESNO,
               fn     : function(btn) {
                          if(btn == 'yes') {
                            Ext.Ajax.request({
                               url     : '../adminProxy/deleteImage',
                               method  : 'post',
                               params  : {images : imgName},
                               success : function(response) {
                                 store.load();
                                 oResponse = Ext.decode( response.responseText );
                                 if (oResponse.success == true) {
                                   PMExt.notify( _('ID_NOTICE'), _('ID_SELECTED_IMAGE_DELETED'));
                                 } 
                                 else {
                                   PMExt.notify( _('ID_NOTICE'), _('ID_SELECTED_IMAGE_IS_LOGO'));
                                 }
                               }
                            });

                          }
                        },
               animEl : 'elId',
               icon   : Ext.MessageBox.QUESTION
            });
          }
        }
        else {
          PMExt.notify( _('ID_NOTICE'), _('ID_YOU_ARE_NOT_CAN_SELECT_PHOTO'));
        } 
      }
      else {
        PMExt.notify( _('ID_NOTICE'), _('ID_SELECT_AN_IMAGE'));
      }
    }
  });

  tbar.add({
    text    : _('ID_RESTORE_DEFAULT'),
    icon    : '/images/icon-pmlogo-15x15.png',
    handler : function() {        
      var records = datav.getSelectedRecords();
      var myMask  = new Ext.LoadMask(Ext.getBody(), {msg : _('ID_LOADING')});
      myMask.show();
      var imageName = 'name';
      Ext.Ajax.request({
         url     : '../adminProxy/replacementLogo',
         method  : 'post',
         params  : {nameFunction: 'restoreLogo', NAMELOGO:imageName},
         success : function() {
          if (typeof parent.parent.Ext != 'undefined') {
            parent.parent.location.href = '../main?st=admin';
          }
          else {
            window.parent.window.parent.location.href = window.parent.window.parent.window.location.href
          }
         }
      });
    }
  });

  tbar.add('-', {
    text    : _('ID_UPLOAD'),
    iconCls : 'silk-add',
    icon    : '/images/import.gif',
    handler : function(){
      uploadWin.show();
    }
  })

  var datav = new Ext.DataView({
    autoScroll  : true,
    store       : store,
    tpl         : tpl,
    autoHeight  : false,
    height      : 800,
    multiSelect : true,
    autoScroll: true,
    overClass   : 'x-view-over', 
    itemSelector: 'div.thumb-wrap',
    emptyText   : _('ID_NO_IMAGES_TO_DISPLAY'),

    listeners   : {
      selectionchange : {
        fn : function(dv,nodes){
          var l = nodes.length;
          var s = l != 1 ? 's' : '';
          panelLeft.setTitle(_('PHOTO_GALLERY') + '(' + l + ' ' + _('ID_IMAGE') + s + ' ' + _('ID_SELECTED') + ')');
          if (nodes.length > 0) {
            Ext.getCmp('tbarAply').enable();
            Ext.getCmp('tbarDelete').enable();            
          }
          else {
            Ext.getCmp('tbarAply').disable();
            Ext.getCmp('tbarDelete').disable();
          }

        }
      }
//      ,
//      click : {
//        fn : function() {
//        }
//      }
      
    }
  })

  var panelLeft = new Ext.Panel({
    region     : 'center',
    id         : 'images-view',
    frame      : true,
    width      : 520,
    autoHeight : true,
    layout     : 'auto',
    title      : _('PHOTO_GALLERY') + '(0 ' + _('ID_IMAGES_SELECTED') + ')',
    items      : [tbar, datav]
  });


  var panelRightTop = new Ext.FormPanel({
    title      : _('ID_UPLOAD_IMAGE'),
    width      : 270,
    labelAlign : 'right',
    fileUpload : true,
    frame      : true,
    defaults   : {
      anchor     : '90%',
      allowBlank : false,
      msgTarget  : 'side'
    },
    items:
    [
      {
        xtype      : 'fileuploadfield',
        emptyText  : '',
        fieldLabel : _('ID_IMAGE'),
        buttonText : _('ID_SELECT_FILE'),
        name       : 'img'
      }
    ],
    buttons :
    [
      {
        text    : _('ID_UPLOAD'),
        handler : function() {
          panelRightTop.getForm().submit({
            url     : '../adminProxy/uploadImage',
            waitMsg : _('ID_LOADING'),
            waitTitle : "",
            success : function(form, o) {
              obj = Ext.util.JSON.decode(o.response.responseText);
              if (obj.failed == '0' && obj.uploaded != '0') {
                PMExt.notify(_('ID_SUCCESS'), _('ID_YOUR_IMAGE_HAS_BEEN_SUCCESSFULLY'));
              }
              else {
                var messageError = "";
                if (obj.failed == "1") {
                  //| 1-> Fail in the type of the image
                  messageError = _('ID_ERROR_UPLOADING_IMAGE_TYPE');
                }
                else if(obj.failed == "2") {
                //| 2-> Fail in the size of the image
                  messageError = _('ID_UPLOADING_IMAGE_WRONG_SIZE');
                }
                else if(obj.failed == "3") {
                //| 3-> fail in de mime of the image
                  messageError = _('ID_ERROR_UPLOADING_IMAGE_TYPE');
                }

                PMExt.notify(_('ID_SUCCESS'), messageError);
              }
              panelRightTop.getForm().reset();
              store.load();
              uploadWin.hide();
            }
          });
        }
      },
      {
        text    : _('ID_CANCEL'),
        handler : function() {
          uploadWin.hide();
        }
      }
    ]
  });

  uploadWin = new Ext.Window({
    title       : '',
    id          : 'uploadWin',
    layout      : 'fit',
    width       : 420,
    height      : 140,
    modal       : false,
    autoScroll  : true,
    closeAction : 'hide',
    maximizable : false,
    items       : [panelRightTop]
  });

  var panelRightBottom = new Ext.Panel({
    region       : 'east',
    title        : _('IMAGE_DETAIL'),
    frame        : true,
    width        : 200,
    height       : 255,
    split        : true,
    collapsible  : true,
    collapseMode : 'mini',
    margins      : '0 0 0 0',
    id           : 'panelDetail',
    tpl          : tplDetail
  });

  var viewport = new Ext.Viewport({
    layout     : 'border',
    autoScroll : false,
    items      : [ panelLeft ]
  });
});
