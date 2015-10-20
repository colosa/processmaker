Ext.onReady(function() {
  onMessageMnuContext = function (grid, rowIndex, e) {
    e.stopEvent();
    var coords = e.getXY();
    mnuContext.showAt([coords[0], coords[1]]);
  };

  ///////
  var storePM;

  var msgCt;

  //Stores
  storePM = new Ext.data.Store({
    proxy: new Ext.data.HttpProxy({
      url: "processMakerAjax",
      method: "POST"
    }),

    baseParams: {"option": "list"},

    reader: new Ext.data.JsonReader({
      root: "results",
      fields: [{name: "OBJ_UID", type: "string"}, {name: "OBJ_VERSION", type: "string"}, {name: "OBJ_VERSION_NAME", type: "string"}]
    }),

    autoLoad: true, //First call

    listeners: {
      exception: function (proxy, type, action, options, response, args){
        var dataResponse;
        var sw = 1;

        if (sw == 1 && !response.responseText) {
          sw = 0;
        }
        if (sw == 1 && response.responseText && response.responseText != "") {
          dataResponse = eval("(" + response.responseText + ")"); //json

          if (dataResponse.status && dataResponse.status == "ERROR") {
            sw = 0;
          }
        }

      },
      load: function (store, record, option) {
        //
      }
    }
  });

  function createBox(t, s){
    return ['<div class="msg">',
    '<div class="x-box-tl"><div class="x-box-tr"><div class="x-box-tc"></div></div></div>',
    '<div class="x-box-ml"><div class="x-box-mr"><div class="x-box-mc"><h3>', t, '</h3>', s, '</div></div></div>',
    '<div class="x-box-bl"><div class="x-box-br"><div class="x-box-bc"></div></div></div>',
    '</div>'].join('');
  }

  function message(title, arguments){
    if (!msgCt) {
      msgCt = Ext.DomHelper.insertFirst(document.body, {
        id:'msg-div'
      }, true);
    }
    msgCt.alignTo(document, 't-t');
    //var s = String.format.apply(String, Array.prototype.slice.call(arguments, 1));
    var m = Ext.DomHelper.append(msgCt, {
      html:createBox(title, arguments)
      }, true);
    m.slideIn('t').pause(10).ghost("t", {
      remove:true
    });
  }

  Ext.QuickTips.init();
  Ext.form.Field.prototype.msgTarget = 'side';

  var upgradeAddonId;
  var upgradeStoreId;

  function newLocation() {
    var site = '';
    if (SYS_SKIN.substring(0,2) == 'ux') {
        site = PROCESSMAKER_URL + "/main?st=admin&s=PMENTERPRISE";
    } else {
        site = PROCESSMAKER_URL + "/setup/main?s=PMENTERPRISE";
    }
    return site;
  }

  function upgradeStatus(addonId, storeId, record) {
    upgradeAddonId = addonId;
    upgradeStoreId = storeId;
    //console.log(record);
    //progressWindow.hide();
    progressWindow.show();
    //if (progress)
    //  Ext.ComponentMgr.get('upgrade-progress')
    if (record) {
      progress = record.get('progress');
      status = record.get('status');
      if (status == 'install') {
        msg = _('ID_WAIT_INSTALLING_PLUGIN');
      } else if (status == 'install-finish') {
        msg = _('ID_UPGRADE_FINISHED');
      } else {
        msg = _('ID_UPGRADING_PLUGIN');
      }
      if (status == "download" && progress) {
        msg = _('ID_DOWNLOADING_UPGRADE') + " " + progress + "%";
        Ext.ComponentMgr.get('upgrade-progress').show();
        Ext.ComponentMgr.get('upgrade-progress').updateProgress(progress/100, '', true);
      } else {
        Ext.ComponentMgr.get('upgrade-progress').hide();
      }
      Ext.ComponentMgr.get('finish-upgrade-button').setDisabled((status != "install-finish"));
      msg = '<h3>' + msg + '</h3>';
      logMsg = record.get('log');
      while (logMsg && logMsg.indexOf("\n") > -1) {
        logMsg = logMsg.replace("\n","<br/>");
      }
      if (logMsg && status != "download-start") {
        Ext.ComponentMgr.get('upgrade-log').update("<h4>" + _('ID_INSTALLATION_LOG') + "</h4><p>"+logMsg+"</p>");
      }
    } else {
      msg = "<h3> " + _('ID_UPGRADE_STARTING') + "</h3>";
    }
    Ext.ComponentMgr.get('upgrade-status').update(msg);
  }

  function installError(addonId, storeId, msg) {
    recordId = addonsStore.findBy(function(record) {
      return (record.get("id") == addonId && record.get("store") == storeId);
    });

    downloadLink = "";
    if (recordId != -1) {
      record = addonsStore.getAt(recordId);
      url = record.get("url");
      downloadLink = "<p>" + _('ID_DOWNLOAD_MANUALLY') + " <a href=\"" + url + "\"></a></p>";
    }

    if (msg === undefined) {
      msg = "<p><b>"+_('ID_ERROR')+":<b>" + _('ID_UNKNOWN') + "</p>";
    } else {
      msg = "<p><b>"+_('ID_ERROR')+":</b> " + msg + "</p>";
    }

    errorWindow = new Ext.Window({
      //applyTo: document.body,
      layout: "fit",
      width: 400,
      height: 250,
      plain: true,
      modal: true,

      items: [{
        id: "error",
        preventBodyReset: true,
        padding: 15,
        html: "<h3>" + _('ID_INSTALL_ERROR') + "</h3>" +
        "<p>" + _('ID_ERROR_INSTALLING_ADDON') + "</p>" +
        //downloadLink +
        msg
      }],

      buttons: [{
        text: _('ID_CLOSE'),
        handler: function(){
          errorWindow.hide();
        }
      }]
    });
    errorWindow.show(this);
  }

  function storeError(msg) {
    if (msg === undefined) {
      msg = "<p><b>"+_('ID_ERROR')+":<b> " + _('ID_UNKNOWN') + "</p>";
    } else {
      msg = "<p><b>"+_('ID_ERROR')+":</b> " + msg + "</p>";
    }

    errorWindow = new Ext.Window({
      //applyTo:document.body,
      layout:'fit',
      width:400,
      height:250,
      plain: true,
      modal: true,

      items: [{
        id: 'error',
        preventBodyReset : true,
        padding: 15,
        html: '<h3>' + _('ID_SERVER_ERROR') + '</h3>'+
        '<p>' + _('ID_MARKET_SERVER_CONTACTING') + '</p>'+
        msg
      }],

      buttons: [{
        text: _('ID_CLOSE'),
        handler: function(){
          errorWindow.hide();
        }
      }]
    });
    errorWindow.show(this);
  }

  function installAddon(addonId, storeId)
  {  var sw = 1;
     var msg = "";

     if (sw == 1 && PATH_PLUGINS_WRITABLE == 0) {
       sw = 0;
       msg = PATH_PLUGINS_WRITABLE_MESSAGE;
     }

     if (sw == 1) {
       swReloadTask = 0;
       reloadTask.cancel();

       recordId = addonsStore.findBy(function(record) {
         return (record.get("id") == addonId && record.get("store") == storeId);
       });

       //var addonEnabled = "";

       if (recordId != -1) {
         record = addonsStore.getAt(recordId);
         record.set("status", "download-start");
         record.commit();

         //addonEnabled = record.get("enabled");
       }

       Ext.Ajax.request({
         url: "addonsStoreAction",
         method: "POST",
         params: {
           "action": "install",
           "addon": addonId,
           "store": storeId
         },

         success: function (response, opts) {
          var dataResponse = eval("(" + response.responseText + ")"); //json
           swReloadTask = 1;

           if (dataResponse.status && dataResponse.status == "OK") {
             //parent.Ext.getCmp(parent.tabItems[1].id).getRootNode().reload();
             parent.parent.window.location.href = newLocation();
           } else {
             installError(addonId, storeId, dataResponse.message);

             addonsStore.load();
           }
         },

         failure: function (response, opts) {
           swReloadTask = 1;

           //installError(addonId, storeId);
         }
       });
     } else {
       Ext.MessageBox.alert(_('ID_WARNING'), msg);
     }
  }

  function addonAvailable(addonId)
  {
      if (INTERNET_CONNECTION == 1) {
          swReloadTask = 0;
          reloadTask.cancel();

          Ext.MessageBox.confirm(
            _('ID_CONFIRM'),
              _('ID_SALES_DEPARTMENT_REQUEST'),
              function (btn, text) {
                  if (btn == "yes") {
                      var myMask = new Ext.LoadMask(Ext.getBody(), {msg: _('ID_SENDING_REQUEST_SALES_DEPARTMENT')});
                      myMask.show();

                      Ext.Ajax.request({
                          url: "addonsStoreAction",
                          method: "POST",
                          params: {
                              "action": "available",
                              "addonId": addonId
                          },

                          success: function (response, opts) {
                              var dataResponse = eval("(" + response.responseText + ")"); //json

                              swReloadTask = 1;
                              myMask.hide();

                              if (dataResponse.status && dataResponse.status == "OK") {
                                  Ext.MessageBox.show({
                                      width: 400,
                                      icon: Ext.MessageBox.INFO,
                                      buttons: Ext.MessageBox.OK,

                                      title: _('ID_INFORMATION'),
                                      msg: _('ID_REQUEST_SENT')
                                      //fn: saveAddress
                                  });
                              } else {
                                Ext.MessageBox.alert(_('ID_WARNING'), dataResponse.message);
                              }

                              addonsStore.load();
                          },

                          failure: function (response, opts) {
                              swReloadTask = 1;
                              myMask.hide();
                          }
                      });
                  } else {
                      swReloadTask = 1;

                      addonsStore.load();
                  }
              }
          );
      } else {
          Ext.MessageBox.alert(_('ID_INFORMATION'), _('ID_NO_INTERNET_CONECTION'));
      }
  }

  function processMakerInstall()
  {
      var myMask = new Ext.LoadMask(Ext.getBody(), {msg: _('ID_WAIT_WHILE_UPGRADING_PROCESSMAKER')});
      myMask.show();


      var record = cboPm.findRecord(cboPm.valueField, cboPm.getValue());
      var index  = cboPm.store.indexOf(record);

      var uid         = cboPm.store.getAt(index).get("OBJ_UID");
      var version     = cboPm.getValue();
      var versionName = cboPm.store.getAt(index).get(cboPm.displayField);

      swReloadTask = 0;
      reloadTask.cancel();

      Ext.Ajax.timeout = 1800000; //milliseconds //1 millisecond = 0.001 second

      Ext.Ajax.request({
          url: "processMakerAjax",
          method: "POST",
          params: {
              "option": "install",
              "uid": uid,
              "version": version,
              "versionName": versionName,
              "processMakerVersion": PROCESSMAKER_VERSION
          },

          success: function (response, opts) {
          swReloadTask = 1;
          myMask.hide();

          var sw = 1;
          var msg = "";

          if (sw == 1 && response.responseText == "") {
              sw = 0;
              msg = "";
          }

          if (sw == 1 && !(/^.*\{.*\}.*$/.test(response.responseText))) {
              sw = 0;
              msg = "<br />" + response.responseText + "<br />";
          }

          if (sw == 1) {
              var dataResponse = eval("(" + response.responseText + ")"); //json

              if (dataResponse.status && dataResponse.status == "OK") {
                  //window.location.href = "";
                  //window.location.reload();
                  Ext.MessageBox.alert(_('ID_INFORMATION'), dataResponse.message + "<br />" + _('ID_LOG_AGAIN'), function () { parent.parent.window.location.href = PROCESSMAKER_URL + (SYS_SKIN.substring(0,2) == 'ux')? "/main/login" :"/setup/login/login"; });
              } else {
                  Ext.MessageBox.alert(_('ID_WARNING'), _('ID_ERROR_UPGRADING_SYSTEM') + "<br />" + dataResponse.message);
                 addonsStore.load();
              }
          } else {
              Ext.MessageBox.alert(_('ID_WARNING'), _('ID_ERROR_CHECK_FOR_UPDATE_DONE') + "<br />" + msg, function () { parent.parent.window.location.href = PROCESSMAKER_URL + (SYS_SKIN.substring(0,2) == 'ux')? "/main/login" :"/setup/login/login"; });
          }
        },

        failure: function (response, opts) {
            swReloadTask = 1;
            myMask.hide();
        }
     });
  }

    var enterpriseFileSupport = function () {
        var myMask = new Ext.LoadMask(Ext.getBody(), {msg: _('ID_PROCESSING')});
        myMask.show();
        //window.location = '../../uxmodern/main/generateInfoSupport';
        window.location = '../adminProxy/generateInfoSupport',
        myMask.hide();
    };

  function enterpriseProcessAjax(option)
  {
      switch (option) {
          case "SETUP":
              var myMask = new Ext.LoadMask(Ext.getBody(), {msg: _('ID_PROCESSING')});
              myMask.show();
              break;
      }

      var p = {
          "option": option
      };

      switch (option) {
          case "SETUP":
              eval("p.internetConnection = \"" + ((Ext.getCmp("chkEeInternetConnection").checked == true)? 1 : 0) + "\"");
              break;
      }

      Ext.Ajax.request({
          url: "enterpriseAjax",
          method: "POST",
          params: p,

          success: function (response, opts) {
              var dataResponse = eval("(" + response.responseText + ")"); //json

              switch (option) {
                  case "SETUP":
                      INTERNET_CONNECTION = (Ext.getCmp("chkEeInternetConnection").checked == true)? 1 : 0;

                      reloadTask.cancel();

                      addonsStore.load({
                          params: {
                              "force": true
                          }
                      });
                        addonsFeaturesStore.load({
                            params: {
                                "force": true
                            }
                        });

                      Ext.getCmp("refresh-btn").setDisabled(!Ext.getCmp("chkEeInternetConnection").checked);

                      myMask.hide();
                      break;
              }
          },

          failure: function (response, opts) {
              //
          }
      });
  }

  var addonsStore = new Ext.data.JsonStore({
    proxy: new Ext.data.HttpProxy({
      url: "addonsStoreAction",
      method: "POST"
    }),
    baseParams: {
        "action": "addonsList",
        "force" : true
    },

    //url: "addonsStoreAction?action=addonsList",

    autoDestroy: true,
    messageProperty: 'error',
    storeId: 'addonsStore',
    root: 'addons',
    idProperty: 'id',
    sortInfo: {
      field: 'nick',
      direction: 'ASC' // or 'DESC' (case sensitive for local sorting)
    },
    fields: ['id', 'name', 'store', 'nick', 'latest_version', 'version', 'status',
    'type', 'release_type', 'url', 'enabled', 'publisher', 'description',
    'log', 'progress'],
    listeners: {
      'beforeload': function(store, options) {
        //if (Ext.ComponentMgr.get('latest_version').getValue() == "")
        Ext.ComponentMgr.get('loading-indicator').setValue('<img src="/images/documents/_indicator.gif" />');
        return true;
      },
      "exception": function(e, type, action, options, response, arg) {
        //if (type == "response") {
        //  message("Error", "ProcessMaker had a problem completing this action. Please try again later");
        //} else {
        //  message("Error", response);
        //}

        Ext.ComponentMgr.get('loading-indicator').setValue('<span class="button_menu_ext ss_sprite ss_status_offline">&nbsp;</span>');
      },
      "load": function(store, records, options) {
        //reloadTask.delay(15000); //1 millisecond = 0.001 seconds

        Ext.ComponentMgr.get('loading-indicator').setValue("");
        progressWindow.hide();
        store.filterBy(function (record, id) {
            if (record.get('type') == 'core') {
                coreRecord = record.copy();
                status = record.get('status');
                if (status == "download-start" || status == "download" || status == "install" || status == "install-finish") {
                    upgradeStatus(record.get('id'), record.get('store'), record);
                }
                return false;
            }

          if (record.get('status') == 'download-start' || record.get('status') == 'download' || record.get('status') == 'cancel' || record.get('status') == 'install') {
              //
          }
          return true;
        });

        if (addonsGrid.disabled) {
          addonsGrid.enable();
        }

        errors = store.reader.jsonData.errors;
        for (var i = 0, n = errors.length; i<n; i++) {
          //console.log(errors[i]);
          error = errors[i];
          installError(error.addonId, error.storeId); ///////
        }

        store_errors = store.reader.jsonData.store_errors;
        //console.log(store_errors);
        error_msg = "";
        for (var i = 0, n = store_errors.length; i<n; i++) {
          //console.log(store_errors[i]);
          error_msg += "<p>" + store_errors[i].msg + "</p>";
        }

        //console.log(error_msg);
        //console.log(store_errors.length);
        if (store_errors.length > 0) {
          Ext.ComponentMgr.get('loading-indicator').setValue('<span class="button_menu_ext ss_sprite ss_status_offline" >&nbsp;</span>');
          //storeError(error_msg);
          reloadTask.cancel();
        } else {
          Ext.ComponentMgr.get('loading-indicator').setValue('<span class="button_menu_ext ss_sprite ss_status_online">&nbsp;</span>');
        }
      }
    }
  });


    var addonsFeaturesStore = new Ext.data.JsonStore({
        proxy: new Ext.data.HttpProxy({
            url: "addonsStoreAction",
            method: "POST"
        }),
        baseParams: {
            "action": "addonsList",
            "type" : "features"
        },
        autoDestroy: true,
        messageProperty: 'error',
        storeId: 'addonsFeaturesStore',
        root: 'addons',
        idProperty: 'id',
        sortInfo: {
            field: 'nick',
            direction: 'ASC' // or 'DESC' (case sensitive for local sorting)
        },
        fields: ['id', 'name', 'store', 'nick', 'latest_version', 'version', 'status',
        'type', 'release_type', 'url', 'enabled', 'publisher', 'description',
        'log', 'progress'],
        listeners: {
            'beforeload': function(store, options) {
                Ext.ComponentMgr.get('loading-features-indicator').setValue('<img src="/images/documents/_indicator.gif" />');
                return true;
            },
            "exception": function(e, type, action, options, response, arg) {
                Ext.ComponentMgr.get('loading-features-indicator').setValue('<span class="button_menu_ext ss_sprite ss_status_offline">&nbsp;</span>');
            },
            "load": function(store, records, options) {
                Ext.ComponentMgr.get('loading-features-indicator').setValue("");
                progressWindow.hide();
                store.filterBy(function (record, id) {
                    if (record.get('type') == 'core') {
                        coreRecord = record.copy();
                        status = record.get('status');
                        if (status == "download-start" || status == "download" || status == "install" || status == "install-finish") {
                          upgradeStatus(record.get('id'), record.get('store'), record);
                        }
                        return false;
                    }
                    return true;
                });

                if (addonsFeatureGrid.disabled) {
                    addonsFeatureGrid.enable();
                }

                errors = store.reader.jsonData.errors;
                for (var i = 0, n = errors.length; i<n; i++) {
                    //console.log(errors[i]);
                    error = errors[i];
                    installError(error.addonId, error.storeId);
                }

                store_errors = store.reader.jsonData.store_errors;
                error_msg = "";
                for (var i = 0, n = store_errors.length; i<n; i++) {
                    error_msg += "<p>" + store_errors[i].msg + "</p>";
                }

                if (store_errors.length > 0) {
                    Ext.ComponentMgr.get('loading-features-indicator').setValue('<span class="button_menu_ext ss_sprite ss_status_offline" >&nbsp;</span>');
                    //storeError(error_msg);
                    reloadTask.cancel();
                } else {
                    Ext.ComponentMgr.get('loading-features-indicator').setValue('<span class="button_menu_ext ss_sprite ss_status_online">&nbsp;</span>');
                }
            }
        }
      });

  var upgradeStore = new Ext.data.Store({
    recordType: addonsStore.recordType
  });

  var swReloadTask = 1;

  var reloadTask = new Ext.util.DelayedTask(
      function ()
      {
          if (swReloadTask == 1) {
              //addonsStore.load();
          }
      }
  );


  /**********               UI Controls               **********/

  var progressWindow = new Ext.Window({
    closable:false,
    autoHeight: false,
    autoScroll: false,
    modal: true,
    width:600,
    height:350,
    id: 'upgrade_window',
    title: _('ID_UPGRADE_LABEL'),
    bodyStyle: "font: normal 13px sans;",
    layout: 'vbox',
    layoutConfig: {
      align: 'stretch'
    },
    bbar: new Ext.Toolbar({
      buttonAlign: 'center',
      padding: 15,
      disabled: true,
      items: [{
        id: 'finish-upgrade-button',
        text: '<b> ' + "Finish" + ' </b>',
        handler: function() {
          Ext.ComponentMgr.get('finish-upgrade-button').setDisabled(true);
          Ext.Ajax.request({
            url: 'addonsStoreAction',
            params: {
              'action':'finish',
              'addon': upgradeAddonId,
              'store': upgradeStoreId
            }
          });
        }
      }]
    }),
    items: [{
      flex: 0,
      id: 'upgrade-status',
      preventBodyReset : true,
      padding: 5,
      html: '<h3>' + _('ID_UPGRADE_LABEL') + '</h3>'
    },{
      flex: 0,
      xtype: 'progress',
      hidden: true,
      id: 'upgrade-progress'
    },{
      flex: 1,
      id: 'upgrade-log',
      preventBodyReset : true,
      padding: 15,
      html: '',
      autoScroll: true
    }
    ]
  });

  var addLicenseWindow = new Ext.Window({
    title: _('ID_UPDATE_LICENSE'),
    closeAction: 'hide',
    id: 'upload-window',
    resizeable: false,
    modal: true,
    //frame: true,
    width: 500,
    //autoHeight: true,
    items: [{
      xtype: 'form',
      id: 'upload-form',
      fileUpload: true,
      frame: true,
      border: false,
      //bodyStyle: 'padding: 10px 10px 0 10px;',
      labelWidth: 100,
      defaults: {
        anchor: '90%'
      },
      items: [{
        xtype: "fileuploadfield",
        id: "upLicense",
        emptyText: _('ID_SELECT_LICENSE_FILE'),
        fieldLabel: _('ID_LICENSE_FILE'),
        width: 200,
        name: "upLicense"
      }],
      buttons: [{
        text: _('ID_UPLOAD'),
        handler: function (button, event) {
          var uploadForm = Ext.getCmp("upload-form");
          var sw = 1;

          var fileName = Ext.getCmp("upLicense").value;

          if (!uploadForm.getForm().isValid()) {
            sw = 0;
          }
          if (!fileName) {
            sw = 0;
            Ext.MessageBox.alert(_('ID_WARNING'), _('ID_WARNING_ENTERPRISE_LICENSE_MSG'));
          }

          if (fileName && !(/^.*\.dat$/.test(fileName))) {
            sw = 0;
            Ext.MessageBox.alert(_('ID_WARNING'), _('ID_WARNING_ENTERPRISE_LICENSE_MSG_DAT'));
          }

          if (sw == 1) {
            uploadForm.getForm().submit({
                url: "../enterprise/addonsStoreAction",
                params: {
                    action: "importLicense"
                },
                method: 'POST',
                waitTitle: _('ID_PLEASE_WAIT'),
                waitMsg: _('ID_UPDATING_LICENSE_MSG'),
                success: function (form, o) {
                    Ext.MessageBox.alert(_('ID_INFORMATION'), _('ID_SUCCESSFULLY_UPLOADED') + ' ' + _('ID_ENTERPRISE_INSTALLED'), function () {
                        parent.parent.window.location.href = newLocation();
                    });
                },
                failure: function (form, action) {
                    if (action.failureType == 'server') {
                        parent.parent.window.location.href = newLocation();
                        return;
                    }
                    var dataResponse = eval("(" + action.response.responseText.trim() + ")"); //json
                    Ext.MessageBox.alert(_('ID_WARNING'), (dataResponse.errors)? dataResponse.errors : _('ID_WARNING_ERROR_UPDATING'));
                }
            });
          }
        }
      },
      {
        text: _('ID_CANCEL'),
        handler: function() {
          Ext.getCmp("upload-window").hide();
        }
      }
      ]
    }]
  });

  var addPluginWindow = new Ext.Window({
    title: _('ID_UPLOAD_PLUGIN'),
    closeAction: 'hide',
    id: 'upload-plugin-window',
    closeAction: 'hide',
    resizable: false,
    modal: true,
    frame: true,
    width: 400,
    autoHeight: true,
    items: [{
      xtype: 'form',
      id: 'upload-plugin-form',
      fileUpload: true,
      frame: true,
      border: false,
      bodyStyle: 'padding: 10px 10px 0 10px;',
      labelWidth: 60,
      defaults: {
        anchor: '100%'
      },
      items: [{
        xtype: "fileuploadfield",
        id: "PLUGIN_FILENAME",

        emptyText: _('ID_SELECT_PLUGIN_FILE'),
        fieldLabel: _('ID_PLUGIN_FILE'),
        name: "form[PLUGIN_FILENAME]"
      }],
      buttons: [{
        text: _('ID_UPLOAD'),
        handler: function (button, event) {
          var uploadForm = Ext.getCmp("upload-plugin-form");
          var sw = 1;
          var msg = "";

          if (sw == 1 && !uploadForm.getForm().isValid()) {
            sw = 0;
            msg = "";
          }
          if (sw == 1 && !Ext.getCmp("PLUGIN_FILENAME").value) {
            sw = 0;
            msg = _('ID_SELECT_PLUGIN');
          }
          if (Ext.getCmp("PLUGIN_FILENAME").value.indexOf('enterprise-') > -1) {
            sw = 0;
            msg = _('ID_ENTERPRISE_PACK_CANT_UPLOAD');
          }

          if (sw == 1) {
            swReloadTask = 0;
            reloadTask.cancel();

            uploadForm.getForm().submit({
              url: "pluginsImportFile",
              params: {
                action: "installPlugin"
              },
              waitMsg: _('ID_INSTALLING_PLUGIN'),

              success: function (form, action) {
                var dataResponse = action.result; //json

                swReloadTask = 1;
                Ext.getCmp("upload-plugin-window").hide();

                parent.parent.window.location.href = newLocation();
              },

              failure: function (form, action) {
                var dataResponse = action.result; //json

                swReloadTask = 1;

                Ext.MessageBox.alert(_('ID_WARNING'), (dataResponse.message)? dataResponse.message : _('ID_ERROR_UPLOADING_PLUGIN'));

                addonsStore.load();
              }
            });
          } else {
            Ext.MessageBox.alert(_('ID_WARNING'), msg);
          }
        }
      },
      {
        text: _('ID_CANCEL'),
        handler: function() {
          Ext.getCmp("upload-plugin-window").hide();
        }
      }]
    }]
  });

    var pnlSetup = new Ext.FormPanel({
        frame: true,
        height: 160,
        disabled: !licensed,

        items: [
            {
                layout: "column",
                items: [
                    {
                        columnWidth: 0.80,
                        xtype: "container",
                        items: [
                            {
                                xtype: "checkbox",
                                id: "chkEeInternetConnection",
                                name: "chkEeInternetConnection",
                                checked: (INTERNET_CONNECTION == 1)? true : false,
                                boxLabel: _('ID_CHECK_UPDATES')
                            }
                        ]
                    },
                    {
                        columnWidth: 0.20,
                        xtype: "button",
                        id: "btnEeSetup",
                        text: _('ID_SAVE'),
                        handler: function () {
                            enterpriseProcessAjax("SETUP");
                        }
                    }
                ]
            }
        ]
    });

    var pnlSupport = new Ext.FormPanel({
        frame: true,
        height: 160,
        disabled: !licensed,

        items: [
            {
                layout: "column",
                items: [
                    {
                        columnWidth: 0.80,
                        xtype: "container",
                        items: [
                            {
                                xtype:'label',
                                text: _('ID_GENERATE_INFO_SUPPORT'),
                                name: 'lblGenerateInfoSupport',
                                labelStyle: 'font-weight:bold;',
                            }
                        ]
                    },
                    {
                        columnWidth: 0.20,
                        xtype: "button",
                        id: "btnGenerate",
                        text: _('ID_GENERATE'),
                        handler: function () {
                            enterpriseFileSupport();
                        }
                    }
                ]
            }
        ]
    });

  var licensePanel = new Ext.FormPanel( {
    frame: true,
    labelAlign: "right",
    defaultType: "displayfield",
    items: [
        {
            id: "license_name",
            fieldLabel: _('ID_CURRENT_LICENSE'),
            value: license_name
        },
        {
            id: "license_server",
            fieldLabel: _('ID_LICENSE_SERVER'),
            value: license_server
        },
        {
            id: "license_message",
            fieldLabel:_('ID_STATUS'),
            hidden: licensed,
            hideLabel: licensed,
            value: "<font color='red'>"+license_message+"</font>&nbsp;("+license_start_date+"/"+license_end_date+")<br />"+license_user
        },
        {
            id: "license_user",
            fieldLabel: _('ID_ISSUED_TO'),
            value: license_user,
            hidden: !licensed,
            hideLabel: !licensed
        },
        {
            id: "license_expires",
            fieldLabel: _('ID_EXPIRES'),
            value: license_expires+'/'+license_span+" ("+license_start_date+" / "+license_end_date+")",
            hidden: !licensed,
            hideLabel: !licensed
        }
    ],
    buttons : [
        {
          text: _('ID_IMPORT_LICENSE'),
          disable: false,
          handler: function() {
            addLicenseWindow.show();
          }
        },
        {
          text : _('ID_RENEW'),
          hidden: true,
          disabled : true
        }
    ]
  });

  var expander = new Ext.grid.RowExpander({
    tpl : new Ext.Template(
      '<p><b>'+_('ID_DESCRIPTION')+':</b> {description}</p>'
      )
  });

  var btnUninstall = new Ext.Action({
    //id: "uninstall-btn",
    text: _('ID_UNISTALL'),
    tooltip: _('ID_UNISTALL_TIP'),
    iconCls: "button_menu_ext ss_sprite  ss_delete",
    handler: function (b, e) {
      //The plugin is activated, please deactivate first to remove it.

      var sw = 1;
      var msg = "";

      if (sw == 1 && PATH_PLUGINS_WRITABLE == 0) {
       sw = 0;
       msg = PATH_PLUGINS_WRITABLE_MESSAGE;
      }

      if (sw == 1) {
        Ext.MessageBox.confirm(
          _('ID_CONFIRM'),
          __('ID_CONFIRM_DELETE_PLUGIN')+"<br /><br />"+__('ID_CONFIRM_DELETE_PLUGIN_WARNING'),
          function (btn, text) {
            if (btn == "yes") {
              swReloadTask = 0;
              reloadTask.cancel();

              var record = addonsGrid.getSelectionModel().getSelected();
              addonsGrid.disable();

              Ext.Ajax.request({
                url: "addonsStoreAction",
                params: {
                  "action": _('ID_UNISTALL'),
                  "addon": record.get("id"),
                  "store": record.get("store")
                },
                success: function (response, opts) {
                  var dataResponse = eval("(" + response.responseText + ")"); //json
                  swReloadTask = 1;

                  if (dataResponse.status && dataResponse.status == "OK") {
                    parent.parent.window.location.href = newLocation();
                  } else {
                    Ext.MessageBox.alert(_('ID_ERROR_UNISTALLING') + " " + record.get("name"), dataResponse.message);
                    addonsStore.load();
                  }
                }
              });
            }
          }
        );
      } else {
        Ext.MessageBox.alert(_('ID_WARNING'), msg);
      }
    }
  });

  var btnEnable = new Ext.Action({
    //id: "enable-btn",
    text: _('ID_ENABLE'),
    tooltip: _('ID_ENABLE_PLUGIN_TIP'),
    iconCls: "button_menu_ext ss_sprite ss_tag_green",
    disabled: true,
    handler: function (b, e) {
      var record = addonsGrid.getSelectionModel().getSelected();
      addonsGrid.disable();

      Ext.Ajax.request({
        url: "addonsStoreAction",
        params: {
          "action":"enable",
          "addon": record.get("id"),
          "store": record.get("store")
        },
        callback: function () {
          parent.parent.window.location.href = newLocation();
        },
        success: function (response) {
          var obj = eval("(" + response.responseText + ")"); //json

          if (!obj.success) {
            Ext.MessageBox.alert(_('ID_ERROR_ENABLING') + " " + record.get("name"), obj.error);
          }
        }
      });
    }
  });

  var btnDisable = new Ext.Action({
    //id: "disable-btn",
    text: _('ID_DISABLE'),
    tooltip: _('ID_DISABLE_PLUGIN_TIP'),
    iconCls: "button_menu_ext ss_sprite ss_tag_red",
    disabled: true,
    handler: function (b, e) {
      var record = addonsGrid.getSelectionModel().getSelected();
      addonsGrid.disable();

      Ext.Ajax.request({
        url: "addonsStoreAction",
        params: {
          "action":"disable",
          "addon": record.get("id"),
          "store": record.get("store")
          },
        callback: function () {
          parent.parent.window.location.href = newLocation();
        },
        success: function (response) {
          var obj = eval("(" + response.responseText + ")"); //json

          if (!obj.success) {
            Ext.MessageBox.alert(_('ID_ERROR_DISABLING') + " " + record.get("name"), obj.error);
          }
        }
      });
    }
  });

  var btnAdmin = new Ext.Action({
    text: _('ID_ADMIN'),
    tooltip: _('ID_ADMIN_PLUGIN_TIP'),
    //iconCls: "button_menu_ext ss_sprite ss_cog_edit",
    iconCls: "button_menu_ext ss_sprite ss_cog",
    disabled: true,
    handler: function () {
      var record = addonsGrid.getSelectionModel().getSelected();
      addonsGrid.disable();

      window.location.href = "pluginsSetup?id=" + record.get("id") + ".php";
    }
  });

  var mnuContext = new Ext.menu.Menu({
    //items: [btnUninstall, "-", btnEnable, btnDisable]
    items: [btnEnable, btnDisable, btnAdmin]
  });

    var addonsGrid = new Ext.grid.EditorGridPanel({
        store: addonsStore,
        defaultType: "displayfield",
        padding: 5,
        height: 335,
        disabled: !licensed,
        columns: [
            expander,
            {
              id       : 'icon-column',
              header   : '',
              width    : 30,
              //sortable : true,
              menuDisabled: true,
              hideable : false,
              dataIndex: 'status',
              renderer : function (val, metadata, record, rowIndex, colIndex, store) {
                return "<img src=\"/images/enterprise/" + val + ".png\" />";
              }
            },
            {
              id       :'nick-column',
              header   : _('ID_NAME'),
              width    : 160,
              //sortable : true,
              menuDisabled: true,
              dataIndex: 'nick',
              renderer: function (val, metadata, record, rowIndex, colIndex, store) {
                  if (record.get('release_type') == 'beta') {
                    return val + " <span style='color:red'> (Beta)</span>";
                  } else if (record.get('release_type') == 'localRegistry') {
                    return val + " <span style='color:gray'> (Local)</span>";
                  } else {
                    return val;
                  }
              }
            },
            {
              id       : 'publisher-column',
              header   : _('ID_PUBLISHER'),
              //sortable : true,
              menuDisabled: true,
              dataIndex: 'publisher'
            },
            {
              id       : 'version-column',
              header   : _('ID_VERSION'),
              //width    : 160,
              //sortable : true,
              menuDisabled: true,
              dataIndex: 'version'
            },
            {
              id       : 'latest-version-column',
              header   : _('ID_LATEST_VERSION'),
              //width    : 160,
              //sortable : true,
              menuDisabled: true,
              dataIndex: 'latest_version'
            },
            {
              id       : 'enabled-column',
              header   : _('ID_ENABLED'),
              width    : 60,
              //sortable : true,
              menuDisabled: true,
              dataIndex: 'enabled',
              renderer: function (val) {
                if (val === true) {
                  return "<img src=\"/images/enterprise/tick-white.png\" />";
                } else if (val === false) {
                  return "<img src=\"/images/enterprise/cross-white.png\" />";
                }
                return '';
              }
            },
            {
              id       : "status",
              header   : "",
              width    : 120,
              //sortable : true,
              menuDisabled: true,
              hideable : false,
              dataIndex: "status",
              renderer: function (val) {
                var str = "";
                var text = "";

                switch (val) {
                    case "available": text = _('ID_BUY_NOW'); break;
                    case "installed": text = _('ID_INSTALLED'); break;
                    case "ready":     text = _('ID_INSTALL_NOW'); break;
                    case "upgrade":   text = _('ID_UPGRADE_NOW'); break;
                    case "download":  text = _('ID_CANCEL'); break;
                    case "install":   text = _('ID_INSTALLING'); break;
                    case "cancel":    text = _('ID_CANCELLING'); break;
                    case "disabled":  text = _('ID_DISABLED'); break;
                    case "download-start": text = "<img src=\"/images/enterprise/loader.gif\" />"; break;
                    default: text = val; break;
                }

                switch (val) {
                  case "available":
                  case "ready":
                  case "upgrade":
                  case "download":
                  case "install":
                  case "cancel":
                  case "download-start":
                    str = "<div class=\"" + val + " roundedCorners\">" + text + "</div>";
                    break;

                  case "installed":
                  case "disabled":
                    str = "<div style=\"margin-right: 0.85em; font-weight: bold; text-align: center;\">" + text + "</div>";
                    break;

                  default:
                    str = "<div class=\"" + val + " roundedCorners\">" + text + "</div>";
                    break;
                }

                return (str);
              }
            }
        ],
        tbar:[
            btnEnable,
            btnDisable,
            btnAdmin,
            '-',
            {
                id: "import-btn",
                text: _('ID_INSTALL_FROM_FILE'),
                tooltip: _('ID_INSTALL_FROM_FILE_PLUGIN_TIP'),
                iconCls:"button_menu_ext ss_sprite ss_application_add",

                //ref: "../removeButton",
                disabled: false,
                handler: function () {
                  var sw = 1;
                  var msg = "";
                  if (sw == 1 && PATH_PLUGINS_WRITABLE == 0) {
                    sw = 0;
                    msg = PATH_PLUGINS_WRITABLE_MESSAGE;
                  }
                  if (sw == 1) {
                    addPluginWindow.show();
                  } else {
                    Ext.MessageBox.alert(_('ID_WARNING'), msg);
                  }
                }
            },
            '-',
            {
                id: 'refresh-btn',
                text:_('ID_REFRESH_LABEL'),
                iconCls:'button_menu_ext ss_sprite ss_database_refresh',
                tooltip: _('ID_REFRESH_LABEL_PLUGIN_TIP'),
                disabled: (INTERNET_CONNECTION == 1)? false : true,
                handler: function (b, e) {
                  reloadTask.cancel();
                  addonsStore.load({
                      params: {
                          "force": true
                      }
                  });
                }
            },
            '->',
            {
                xtype:"displayfield",
                id:'loading-indicator'
            }
        ],
        plugins: expander,
        collapsible: false,
        animCollapse: false,
        stripeRows: true,
        autoExpandColumn: 'nick-column',
        //title: _('ID_ENTERPRISE_PLUGINS'),
        sm: new Ext.grid.RowSelectionModel({
            singleSelect:true,
            listeners: {
                selectionchange: function (sel) {
                    if (sel.getCount() == 0 || sel.getSelected().get("name") == "enterprise") {
                        //btnUninstall.setDisabled(true);
                        btnEnable.setDisabled(true);
                        btnDisable.setDisabled(true);
                        btnAdmin.setDisabled(true);
                    } else {
                        record = sel.getSelected();
                        //btnUninstall.setDisabled(!(record.get("status") == "installed" || record.get("status") == "upgrade" || record.get("status") == "disabled"));
                        btnEnable.setDisabled(!(record.get("enabled") === false));
                        btnDisable.setDisabled(!(record.get("enabled") === true));
                        btnAdmin.setDisabled(!(record.get("enabled") === true));
                    }
                }
            }
        }),
        //config options for stateful behavior
        stateful: true,
        stateId: "grid",
        listeners: {
            "cellclick": function (grid, rowIndex, columnIndex, e) {
                var record = grid.getStore().getAt(rowIndex);
                var fieldName = grid.getColumnModel().getDataIndex(columnIndex);
                //var data = record.get(fieldName);

                if (fieldName != "status") {
                    return;
                }

                switch (record.get("status")) {
                    case "upgrade":
                    case "ready":
                        if (INTERNET_CONNECTION == 1) {
                            installAddon(record.get("id"), record.get("store"));
                        } else {
                            Ext.MessageBox.alert(_('ID_INFORMATION'), _('ID_NO_INTERNET_CONECTION'));
                        }
                        break;
                    case "download":
                        Ext.Ajax.request({
                            url: "addonsStoreAction",
                            params: {
                                "action": "cancel",
                                "addon": record.get("id"),
                                "store": record.get("store")
                            }
                        });
                        break;
                    case "available":
                        addonAvailable(record.get("id"));
                        break;
                }
            }
        }
    });

    // create the Grid Features
    var cmodel = new Ext.grid.ColumnModel({
        viewConfig: {
            forceFit:true,
            cls:"x-grid-empty",
            emptyText: _('ID_NO_RECORDS_FOUND')
        },
        defaults: {
            width: 50
        },
        columns: [
            {
                id       : 'icon-column-feature',
                header   : '',
                width    : 30,
                hideable : false,
                dataIndex: 'status',
                renderer : function (val, metadata, record, rowIndex, colIndex, store) {
                  return "<img src=\"/images/enterprise/" + val + ".png\" />";
                }
            },
            {
                id       :'nick-column-feature',
                header   : _('ID_NAME'),
                width    : 150,
                sortable : true,
                dataIndex: 'nick',
                renderer: function (val, metadata, record, rowIndex, colIndex, store) {
                    if (record.get('release_type') == 'beta') {
                      return val + " <span style='color:red'> (Beta)</span>";
                    } else {
                      return val;
                    }
                }
            },
            {
                id       :'description-column-feature',
                header   : _('ID_DESCRIPTION'),
                width    : 200,
                dataIndex: 'description'
            },
            {
                id       : 'enabled-column-feature',
                header   : _('ID_ENABLED'),
                width    : 60,
                dataIndex: 'enabled',
                renderer: function (val) {
                  if (val === true) {
                    return "<img src=\"/images/enterprise/tick-white.png\" />";
                  } else if (val === false) {
                    return "<img src=\"/images/enterprise/cross-white.png\" />";
                  }
                  return '';
                }
            }
        ]
    });

    var addonsFeatureGrid = new Ext.grid.EditorGridPanel({
        region: 'center',
        layout: 'fit',
        id: 'addonsFeatureGrid',
        autoHeight : true,
        autoWidth : true,
        stateful : true,
        stateId : 'addonsFeatureGrid',
        enableColumnResize: true,
        enableHdMenu: true,
        frame:false,
        columnLines: false,
        viewConfig: {
            forceFit:true
        },
        disabled: !licensed,
        store: addonsFeaturesStore,
        cm: cmodel,
        tbar:
        [
            {
              id: 'refresh-btn',
              text:_('ID_REFRESH_LABEL'),
              iconCls:'button_menu_ext ss_sprite ss_database_refresh',
              tooltip: _('ID_REFRESH_LABEL_PLUGIN_TIP'),
              disabled: (INTERNET_CONNECTION == 1)? false : true,
              handler: function (b, e) {
                reloadTask.cancel();
                addonsFeaturesStore.load({
                    params: {
                        "force": true
                    }
                });
              }
            },
            '->',
            {
              xtype:"displayfield",
              id:'loading-features-indicator'
            }
        ],
        listeners: {
            render: function(){
                this.loadMask = new Ext.LoadMask(this.body, {msg:_('ID_LOADING_GRID')});
            }
        }
    });

    var tabEnterprise = new Ext.TabPanel({
        activeTab: 0,
        height: 370,
        defaults:{
            autoScroll: true,
            layout:'form',
            frame:true,
        },
        items:[{
                title:  _('ID_ENTERPRISE_PLUGINS'),
                autoScroll: false,
                items : addonsGrid
            },{
                title:  _('ID_ENTERPRISE_FEATURES'),
                items : addonsFeatureGrid
            }
        ]
    });
    var tabSetup= new Ext.TabPanel({
        activeTab: 0,
        height: 190,
        defaults:{autoScroll: true},
        items:[{
                title:  _('ID_YOUR_LICENSE'),
                items : licensePanel
            },{
                title:  _('ID_SETUP_WEBSERVICES'),
                items : pnlSetup
            },{
                title:  _('ID_SUPPORT'),
                items : pnlSupport
            }
        ]
    });


    var fullBox = new Ext.Panel({
        id:'main-panel-vbox',
        region:'west',
        margins:'5 0 5 5',
        items:[ tabSetup, tabEnterprise]
    });

  addonsGrid.on("rowcontextmenu",
    function (grid, rowIndex, evt) {
      var sm = grid.getSelectionModel();
      sm.selectRow(rowIndex, sm.isSelected(rowIndex));
    },
    this
  );

  addonsGrid.addListener("rowcontextmenu", onMessageMnuContext, this);

    addonsFeatureGrid.on("rowcontextmenu",
        function (grid, rowIndex, evt) {
          var sm = grid.getSelectionModel();
          sm.selectRow(rowIndex, sm.isSelected(rowIndex));
        },
        this
    );

    addonsFeatureGrid.addListener("rowcontextmenu", onMessageMnuContext, this);

  ///////
  var viewport = new Ext.Viewport({
    layout: "anchor",
    anchorSize: {
      width:800,
      height:600
    },
    items:[fullBox]
  });

  if (licensed) {
    addonsStore.load();
    addonsFeaturesStore.load();
  }
});
