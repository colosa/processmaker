var wizard;

// Extend timeout for all Ext.Ajax.requests to 90 seconds.
// Ext.Ajax is a singleton, this statement will extend the timeout
// for all subsequent Ext.Ajax calls.
Ext.Ajax.timeout = 4 * 60 * 1000;

Ext.onReady(function(){

    Ext.QuickTips.init();

    wizard = new Ext.ux.Wiz({
      height: 555,
      width : 780,
      id    : 'wizard',
      closable: false,
      modal : false,
      draggable: false,

      headerConfig : {
        title : '&nbsp'
      },
      cardPanelConfig : {
        defaults : {
          bodyStyle : 'padding:20px 10px 10px 20px;background-color:#F6F6F6;',
          border    : false
        }
      },
      cards : steps,
      loadMaskConfig: {
        'default': 'Checking...',
        'finishing': 'Finishing...'
      },
      listeners: {
        finish: finishInstallation
      }
    });

    // show the wizard
    wizard.show();
});

function finishInstallation()
{
  Ext.MessageBox.show({
    msg: _('ID_INSTALLING_WORKSPACE'),
    progressText: 'Saving...',
    width:300,
    wait:true,
    waitConfig: {interval:200},
    animEl: 'mb7'
  });
  wizard.showLoadMask(true, _('ID_FINISH'));
  Ext.Ajax.request({
    url: 'createWorkspace',
    success: function(response){
      Ext.MessageBox.hide();
      var response = Ext.util.JSON.decode(response.responseText);
      Ext.getCmp('finish_message').setValue(getFieldOutput(response.message, response.result));
      wizard.showLoadMask(false);
      if (response.result) {
        wizard.onClientValidation(4, false);

        //Ext.msgBoxSlider.msgTopCenter(
        PMExt.info(
          _('ID_PROCESSMAKER_INSTALLATION'),
          response.messageFinish,
          function(){
            _redirect(response.uri);
          }
        );

        //setTimeout("_redirect('"+response.url+"')", 3000);
        // Ext.Msg.alert(
        //   'ProcessMaker was successfully installed',
        //   'Workspace "' + Ext.getCmp('workspace').getValue() + '" was installed correctly now you will be redirected to your new workspace.',
        //   function() {_redirectwindow.location = response.url;}
        // );
      }
      else {
        PMExt.error('ERROR', response.message, function(){
          if (response.canRedirect) {
            _redirect(response.uri);
          }
        })
      }
    },
    failure: function(){Ext.MessageBox.hide(); wizard.showLoadMask(false);},
    params: {
      'db_engine'     : Ext.getCmp('db_engine'        ).getValue(),
      'db_hostname'   : Ext.getCmp('db_hostname'      ).getValue(),
      'db_username'   : Ext.getCmp('db_username'      ).getValue(),
      'db_password'   : Ext.getCmp('db_password'      ).getValue(),
      'db_port'       : Ext.getCmp('db_port'          ).getValue(),
      'pathConfig'    : Ext.getCmp('pathConfig'       ).getValue(),
      'pathLanguages' : Ext.getCmp('pathLanguages'    ).getValue(),
      'pathPlugins'   : Ext.getCmp('pathPlugins'      ).getValue(),
      'pathXmlforms'  : Ext.getCmp('pathXmlforms'     ).getValue(),
      'pathShared'    : Ext.getCmp('pathShared'       ).getValue(),
      'workspace'     : Ext.getCmp('workspace'        ).getValue(),
      'adminUsername' : Ext.getCmp('adminUsername'    ).getValue(),
      'adminPassword' : Ext.getCmp('adminPassword'    ).getValue(),
      'wfDatabase'    : Ext.getCmp('wfDatabase'       ).getValue(),
      'rbDatabase'    : Ext.getCmp('rbDatabase'       ).getValue(),
      'rpDatabase'    : Ext.getCmp('rpDatabase'       ).getValue(),
      'deleteDB'      : Ext.getCmp('deleteDB'         ).getValue(),
      'userLogged'    : Ext.getCmp('createUserLogged' ).getValue()
    },
    timeout: 180000
  });
}

function _redirect(_uri){
  //console.log('redirecting:: '+_uri);
  window.location = _uri;
}

function getFieldOutput(txt, assert)
{
  if(assert == true) {
    img = 'dialog-ok-apply.png';
    size = 'width=12 height=12';
    color = 'green';
  } else {
    img = 'delete.png';
    size = 'width=15 height=15';
    color = 'red';
  }
  return  '<font color='+color+'>'+txt + '</font> <img src="/images/'+img+'" '+size+'/>';
}
