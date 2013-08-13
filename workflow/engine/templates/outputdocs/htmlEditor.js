function setGridHtml(outdocHtml, swEdit)
{
    var outdocHtmlAux = outdocHtml;

    outdocHtmlAux = stringReplace("\\x0A", "(n /)", outdocHtmlAux); //\n 10
    outdocHtmlAux = stringReplace("\\x0D", "(r /)", outdocHtmlAux); //\r 13
    outdocHtmlAux = stringReplace("\\x09", "(t /)", outdocHtmlAux); //\t  9

    var arrayMatch1 = [];
    var outdocHtmlAux1 = "";
    var strHtml = "";

    ///////
    outdocHtmlAux1 = outdocHtmlAux;
    strHtml = "";

    //@>
    if (swEdit == 1) {
        while ((arrayMatch1 = /^(.*)<tr>[\(\)nrt\s\/]*<td>[\(\)nrt\s\/]*(@>[a-zA-Z\_]\w*)[\(\)nrt\s\/]*<\/td>[\(\)nrt\s\/]*<\/tr>(.*)$/ig.exec(outdocHtmlAux1))) {
            outdocHtmlAux1 = arrayMatch1[1];
            strHtml = arrayMatch1[2] + arrayMatch1[3] + strHtml;
        }
    } else {
        while ((arrayMatch1 = /^(.*<table.*>.*)(@>[a-zA-Z\_]\w*)(.*<\/table>.*)$/ig.exec(outdocHtmlAux1))) {
            outdocHtmlAux1 = arrayMatch1[1];
            strHtml = "<tr><td>" + arrayMatch1[2] + "</td></tr>" + arrayMatch1[3] + strHtml;
        }
    }

    strHtml = outdocHtmlAux1 + strHtml;

    ///////
    outdocHtmlAux1 = strHtml;
    strHtml = "";

    //@< //Copy of @>
    if (swEdit == 1) {
        while ((arrayMatch1 = /^(.*)<tr>[\(\)nrt\s\/]*<td>[\(\)nrt\s\/]*(@<[a-zA-Z\_]\w*)[\(\)nrt\s\/]*<\/td>[\(\)nrt\s\/]*<\/tr>(.*)$/ig.exec(outdocHtmlAux1))) {
            outdocHtmlAux1 = arrayMatch1[1];
            strHtml = arrayMatch1[2] + arrayMatch1[3] + strHtml;
        }
    } else {
        while ((arrayMatch1 = /^(.*<table.*>.*)(@<[a-zA-Z\_]\w*)(.*<\/table>.*)$/ig.exec(outdocHtmlAux1))) {
            outdocHtmlAux1 = arrayMatch1[1];
            strHtml = "<tr><td>" + arrayMatch1[2] + "</td></tr>" + arrayMatch1[3] + strHtml;
        }
    }

    strHtml = outdocHtmlAux1 + strHtml;

    ///////
    strHtml = stringReplace("\\(n \\/\\)", "\n", strHtml);
    strHtml = stringReplace("\\(r \\/\\)", "\r", strHtml);
    strHtml = stringReplace("\\(t \\/\\)", "\t", strHtml);

    outdocHtml = strHtml;

    return outdocHtml;
}

function setHtml(outdocHtml, swEdit)
{
    if (outdocHtml.indexOf("@>") > 0 || outdocHtml.indexOf("@&gt;") > 0) {
        if (swEdit == 1) {
            outdocHtml = stringReplace("@&gt;", "@>", outdocHtml);
            outdocHtml = stringReplace("@&lt;", "@<", outdocHtml);

            outdocHtml = setGridHtml(outdocHtml, swEdit);
        } else {
            outdocHtml = setGridHtml(outdocHtml, swEdit);

            outdocHtml = stringReplace("@>", "@&gt;", outdocHtml);
            outdocHtml = stringReplace("@<", "@&lt;", outdocHtml);
        }
    }

    return outdocHtml;
}





var importOption;

Ext.onReady(function(){
  Ext.QuickTips.init();

  // turn on validation errors beside the field globally
  Ext.form.Field.prototype.msgTarget = 'side';

  var bd = Ext.getBody();
  var sourceEdit = 0;

  importOption = new Ext.Action({
    text: _('ID_LOAD_FROM_FILE'),
    iconCls: 'silk-add',
    icon: '/images/import.gif',
    handler: function(){
      var w = new Ext.Window({
        title: '',
        width: 420,
        height: 140,
        modal: true,
        autoScroll: false,
        maximizable: false,
        resizable: false,

        items: [
          new Ext.FormPanel({
            /*renderTo: 'form-panel',*/
            id:'uploader',
            fileUpload: true,
            width: 400,
            frame: true,
            title: _('ID_OUT_PUT_DOC_UPLOAD_TITLE'),
            autoHeight: false,
            bodyStyle: 'padding: 10px 10px 0 10px;',
            labelWidth: 50,
            defaults: {
                anchor: '90%',
                allowBlank: false,
                msgTarget: 'side'
            },
            items: [{
                xtype: 'fileuploadfield',
                id: 'form-file',
                emptyText: _('ID_SELECT_TEMPLATE_FILE'),
                fieldLabel: _('ID_FILE'),
                name: 'templateFile',
                buttonText: '',
                buttonCfg: {
                    iconCls: 'upload-icon'
                }
            }],
            buttons: [{
                text: _('ID_UPLOAD'),
                handler: function(){
                  var uploader = Ext.getCmp('uploader');
                  if(uploader.getForm().isValid()){
                    uploader.getForm().submit({
                      url: 'outputdocs_Ajax?action=setTemplateFile',
                      waitMsg: _('ID_UPLOADING_FILE'),
                      waitTitle : "",
                      success: function (o, resp) {
                        w.close();

                        Ext.Ajax.request({
                            url: "outputdocs_Ajax?action=getTemplateFile&r=" + Math.random(),
                            success: function (response) {
                                Ext.getCmp("OUT_DOC_TEMPLATE").setValue(setHtml(response.responseText, sourceEdit));

                                if (Ext.getCmp("OUT_DOC_TEMPLATE").getValue() == "") {
                                    Ext.Msg.alert(_("ID_ALERT_MESSAGE"), _("ID_INVALID_FILE"));
                                }
                            },
                            failure: function () {},
                            params: {request: "getRows"}
                        });
                      },
                      failure: function(o, resp){
                        w.close();
                        //alert('ERROR "'+resp.result.msg+'"');
                        Ext.MessageBox.show({title: '', msg: resp.result.msg, buttons:
                        Ext.MessageBox.OK, animEl: 'mb9', fn: function(){}, icon:
                        Ext.MessageBox.ERROR});
                        //setTimeout(function(){Ext.MessageBox.hide(); }, 2000);
                      }
                    });
                  }
                }
            },{
              text: _('ID_CANCEL'),
              handler: function(){ w.close(); }
            }]
          })
        ]
      });
      w.show();
    }
  });


    var top = new Ext.FormPanel({
        labelAlign: 'top',
        frame:true,
        title: '',
        bodyStyle:'padding:5px 5px 0',
        width: 790,
        tbar:[importOption],
        items: [
        {
            xtype:'htmleditor',
            id:'OUT_DOC_TEMPLATE',
            fieldLabel: _('ID_OUTPUT_DOCUMENT_TEMPLATE'),
            height:300,
            anchor:'98%',
            listeners: {
              editmodechange: function (he, srcEdit) {
                sourceEdit = (srcEdit == true)? 1 : 0;

                he.setValue(setHtml(he.getRawValue(), sourceEdit));
              },
              beforepush: function (he, outdocHtml) {
                  //
              }
              //,
              //beforesync: function (he, h) {
              //}
            }
        }],

        buttons: [{
          text: _('ID_SAVE'),
          handler: function(){
            Ext.Ajax.request({
                url: "outputdocs_Save",
                success: function (response) {
                    Ext.Msg.show({
                        title: "",
                        msg: _("ID_SAVED_SUCCESSFULLY"),
                        fn: function () {},
                        animEl: "elId",
                        icon: Ext.MessageBox.INFO,
                        buttons: Ext.MessageBox.OK
                    });
                },
                failure: function () {},
                params: {
                  "form[OUT_DOC_UID]": OUT_DOC_UID,
                  "form[OUT_DOC_TEMPLATE]": setHtml(Ext.getCmp("OUT_DOC_TEMPLATE").getValue(), 1)
                }
            });
          }
        },{
          text: _('ID_CANCEL'),
          handler: function(){
            var sInfo = navigator.userAgent.toLowerCase();
            if ( sInfo.indexOf('msie') != -1 )
              self.close();
            else
              parent.outputdocsEditor.remove();
          }
        }]
    });

    top.render(document.body);

    Ext.Ajax.request({
        url: "outputdocs_Ajax?action=loadTemplateContent&r=" + Math.random(),
        success: function(response){
            Ext.getCmp("OUT_DOC_TEMPLATE").setValue(setHtml(response.responseText, 0));
        },
        failure: function () {},
        params: {OUT_DOC_UID: OUT_DOC_UID}
    });
});

