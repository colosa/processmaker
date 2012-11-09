var newNoteAreaActive;
var caseNotesWindow;
var storeNotes;
var appUid;
var title;
var summaryWindowOpened = false;

var toolTipChkSendMail;

function closeCaseNotesWindow(){
  if(Ext.get("caseNotesWindowPanel")){
    Ext.get("caseNotesWindowPanel").destroy();
  }
}

function openCaseNotesWindow(appUid1, modalSw, appTitle)
{
  Ext.QuickTips.init();
  appUid = !appUid1 ? "": appUid1;
  title  = appTitle;
  var startRecord=0;
  var loadSize=10;

  storeNotes = new Ext.data.JsonStore({
    url : '../appProxy/getNotesList?appUid='+appUid,
    root: 'notes',
    totalProperty: 'totalCount',
    fields: ['USR_USERNAME','USR_FIRSTNAME','USR_LASTNAME','USR_FULL_NAME','NOTE_DATE','NOTE_CONTENT', 'USR_UID', 'user'],
    baseParams:{
      start:startRecord,
      limit:startRecord+loadSize
    },
    listeners:{
      load:function(){

        caseNotesWindow.setTitle(_('ID_CASES_NOTES') + ' (' + storeNotes.data.items.length + ')');

        if(storeNotes.getCount()<storeNotes.getTotalCount()){
          Ext.getCmp('CASES_MORE_BUTTON').show();
        }else{
          Ext.getCmp('CASES_MORE_BUTTON').hide();
        }
      }
    }
  });
  storeNotes.load();

  var panelNotes = new Ext.Panel({
    id:'notesPanel',
    frame:true,
    autoWidth:true,
    autoHeight:true,
    collapsible:false,
    items:[
      new Ext.DataView({
        store: storeNotes,
        loadingtext:_('ID_CASE_NOTES_LOADING'),
        emptyText: _('ID_CASE_NOTES_EMPTY'),
        cls: 'x-cnotes-view',
        tpl: '<tpl for=".">' +
                '<div class="x-cnotes-source"><table><tbody>' +
                    '<tr>' +
                      '<td class="x-cnotes-label"><img border="0" src="../users/users_ViewPhotoGrid?pUID={USR_UID}" width="40" height="40"/></td>' +
                      '<td class="x-cnotes-name">'+
                        '<p class="user-from">{user}</p>'+
                        '<p class="x-editable x-message">{NOTE_CONTENT}</p> '+
                        '<p class="x-editable"><small>'+_('ID_POSTED_AT')+'<i> {NOTE_DATE}</i></small></p>'+
                      '</td>' +
                    '</tr>' +
                '</tbody></table></div>' +
             '</tpl>',
        itemSelector: 'div.x-cnotes-source',
        overClass: 'x-cnotes-over',
        selectedClass: 'x-cnotes-selected',
        singleSelect: true,

        prepareData: function(data){
          //data.shortName = Ext.util.Format.ellipsis(data.name, 15);
          //data.sizeString = Ext.util.Format.fileSize(data.size);
          //data.dateString = data.lastmod.format("m/d/Y g:i a");

          data.user = _FNF(data.USR_USERNAME, data.USR_FIRSTNAME, data.USR_LASTNAME);
          data.NOTE_CONTENT = data.NOTE_CONTENT.replace(/\n/g,' <br/>');
          return data;
        },

        listeners: {
          selectionchange: {
            fn: function(dv,nodes){
              var l = nodes.length;
              var s = l != 1 ? 's' : '';
              //panelNotes.setTitle('Process ('+l+' item'+s+' selected)');
            }
          },
          click: {
            fn: function(dv,nodes,a){
            }
          }
        }
      }),{
        xtype:'button',
        id:'CASES_MORE_BUTTON',
        iconCls: '.x-pm-notes-btn',
        hidden:true,
        text:_('ID_CASE_NOTES_MORE'),
        align:'center',
        handler:function() {
          startRecord=startRecord+loadSize;
          limitRecord=startRecord+loadSize;
          storeNotes.load({
            params:{
              start:0,
              limit:startRecord+loadSize
            }
          });
        }
      }
    ]
  });

  caseNotesWindow = new Ext.Window({
    title: _('ID_CASES_NOTES'), //Title of the Window
    id: 'caseNotesWindowPanel', //ID of the Window Panel
    width: 350, //Width of the Window
    resizable: true, //Resize of the Window, if false - it cannot be resized
    closable: true, //Hide close button of the Window
    modal: modalSw, //When modal:true it make the window modal and mask everything behind it when displayed
    //iconCls: 'ICON_CASES_NOTES',
    autoCreate: true,
    height:400,
    shadow:true,
    minWidth:300,
    minHeight:200,
    proxyDrag: true,
    constrain: true,
    keys: {
      key: 27,
      fn  : function(){
        caseNotesWindow.hide();
      }
    },
    autoScroll:true,
    items:[panelNotes],
    tools:[
    {
      id:'refresh',
      handler:function() {
        storeNotes.load();
      }
    }
    ],
    tbar:[
        new Ext.form.TextArea({
          text   : _('ID_NEW_NOTE'),
          xtype  : 'textarea',
          id     : 'caseNoteText',
          name   : 'caseNoteText',
          width  : 330,
          grow   : true,
          height : 40,
          growMin: 40,
          growMax: 80,
          maxLengthText  : 500,
          allowBlank     :true,
          selectOnFocus  :true,
          enableKeyEvents: true,
          listeners : {
            scope  : this,
            keyup  : updateTextCtr,
            keydown: updateTextCtr
          }
        })
      ],
    rowtbar: [
      [
        {
            xtype: "checkbox",
            id: "chkSendMail",
            name: "chkSendMail",
            checked: true,
            boxLabel: _("ID_CASE_NOTES_LABEL_SEND")
        },
        '->',
        '<span id="countChar">500</span>',
        ' ',
        {
          id: 'sendBtn',
          text: _('ID_SEND'),
          cls: 'x-toolbar1',
          handler: sendNote
        }, ' ',
        {
          id: 'addCancelBtn',
          text: _('ID_CANCEL'),
          cls: 'x-toolbar1',
          //iconCls: 'xx',
          icon: '/images/add_notes.png',
          handler: newNoteHandler,
          tooltip: {
            title: _('ID_CASES_NOTES_ADD'),
            text: _('ID_CASE') + ': ' + title
          }
        }
      ]
    ],
    bbar:[
      new Ext.ux.StatusBar({
        defaultText : _('ID_NOTES_READY'),
        id : 'notesStatusPanel',
        //defaultIconCls: 'ICON_CASES_NOTES',
        text: _('ID_NOTES_READY'), // values to set initially:
        //iconCls: 'ready-icon',
        statusAlign: 'left',
        items: [] // any standard Toolbar items:
      })
    ],
    listeners: {
      show:function() {
        this.loadMask = new Ext.LoadMask(this.body, {
          msg:_('ID_LOADING')
        });
      },
      close:function(){
        if (Ext.get("caseNotes")) {
          Ext.getCmp("caseNotes").toggle(false);
          //Ext.getCmp('caseNotes').show();
        }
      }
    }
  });

  toolTipChkSendMail = new Ext.ToolTip({
      dismissDelay: 3000, //auto hide after 3 seconds
      title: _("ID_CASE_NOTES_HINT_SEND"),
      //html "",
      //text: "",
      width: 200
  });

  newNoteAreaActive = false;
  caseNotesWindow.show();
  newNoteHandler();
}

function updateTextCtr(body, event) {

  ctr = document.getElementById('countChar').innerHTML;

  text = Ext.getCmp('caseNoteText').getValue();
  maxLength = 500;

  if (text.length > maxLength) {
    Ext.getCmp('caseNoteText').setValue(Ext.getCmp('caseNoteText').getValue().substr(0,500));
  }
  else {
    document.getElementById('countChar').innerHTML = maxLength - text.length;
  }
}

function newNoteHandler()
{
  newNoteAreaActive = newNoteAreaActive ? false : true;
  if (newNoteAreaActive) {
    Ext.getCmp('addCancelBtn').setText('');
    Ext.getCmp('addCancelBtn').setTooltip({
      title: _('ID_CASES_NOTES_ADD'),
      text: _('ID_CASE') +': '+ title
    });

    Ext.getCmp('addCancelBtn').setIcon('/images/comment_add.gif');

    caseNotesWindow.getTopToolbar().hide();
    Ext.getCmp("chkSendMail").hide();
    Ext.getCmp("sendBtn").hide();
    document.getElementById('countChar').style.display = 'none';
    caseNotesWindow.doLayout();
  }
  else {
    toolTipChkSendMail.initTarget("chkSendMail");

    Ext.getCmp('addCancelBtn').setText('');
    Ext.getCmp('addCancelBtn').setTooltip({title: _('ID_CASES_NOTES_CANCEL')});
    Ext.getCmp('addCancelBtn').setIcon('/images/cancel.png');

    caseNotesWindow.getTopToolbar().show();
    Ext.getCmp("chkSendMail").show();
    Ext.getCmp("sendBtn").show();
    document.getElementById('countChar').style.display = 'block';
    Ext.getCmp('caseNoteText').focus();
    Ext.getCmp('caseNoteText').reset();
    document.getElementById('countChar').innerHTML = '500';
    caseNotesWindow.doLayout();
  }

  caseNotesWindow.doLayout();
}

function sendNote()
{
  var noteText = Ext.getCmp('caseNoteText').getValue();

  if (noteText == "") {
    return false;
  }

  newNoteHandler();

  Ext.getCmp('caseNoteText').focus();
  Ext.getCmp('caseNoteText').reset();
  statusBarMessage( _('ID_CASES_NOTE_POSTING'), true);
  Ext.Ajax.request({
    url : '../appProxy/postNote' ,
    params : {
      appUid: appUid,
      noteText: noteText,
      swSendMail: (Ext.getCmp("chkSendMail").checked == true)? 1 : 0
    },
    success: function ( result, request ) {
      var data = Ext.util.JSON.decode(result.responseText);
      if(data.success=="success"){
        statusBarMessage( _('ID_CASES_NOTE_POST_SUCCESS'), false,true);
        storeNotes.load();
      }
      else{
        statusBarMessage( _('ID_CASES_NOTE_POST_ERROR'), false,false);
        Ext.MessageBox.alert(_('ID_CASES_NOTE_POST_ERROR'), data.message);

      }
    },
    failure: function ( result, request) {
      statusBarMessage( _('ID_CASES_NOTE_POST_FAILED'), false,false);
      Ext.MessageBox.alert(_('ID_CASES_NOTE_POST_FAILED'), result.responseText);
    }
  });
}

function statusBarMessage( msg, isLoading, success ) {
  var statusBar = Ext.getCmp('notesStatusPanel');
  if( !statusBar ) return;

  if( isLoading ) {
    statusBar.showBusy(msg);
  }
  else {
    //statusBar.setStatus("Done.");
    statusBar.clearStatus();
    if( success ) {
      statusBar.setStatus({
        text: '' + msg,
        iconCls: 'x-status-valid',
        clear: true
      });
    } else {
      statusBar.setStatus({
        text: 'Error: ' + msg,
        iconCls: 'x-status-error',
        clear: true
      });
    }
  }
}



//-------------------------------------------------------------------------------------

/* Case Notes - End */

/* Case Summary - Start */
var openSummaryWindow = function(appUid, delIndex, action)
{
  if (summaryWindowOpened) {
    return;
  }
  summaryWindowOpened = true;
  Ext.Ajax.request({
    url : '../appProxy/requestOpenSummary',
    params : {
      appUid  : appUid,
      delIndex: delIndex,
      action: action
    },
    success: function (result, request) {
      var response = Ext.util.JSON.decode(result.responseText);
      if (response.success) {
        var sumaryInfPanel = PMExt.createInfoPanel('../appProxy/getSummary', {appUid: appUid, delIndex: delIndex, action: action});
        sumaryInfPanel.setTitle(_('ID_GENERATE_INFO'));

        var summaryWindow = new Ext.Window({
          title: _('ID_SUMMARY'),
          layout: 'fit',
          width: 600,
          height: 450,
          resizable: true,
          closable: true,
          modal: true,
          autoScroll:true,
          constrain: true,
          keys: {
            key: 27,
            fn: function() {
              summaryWindow.close();
            }
          }/*,
          buttons : [{
           text    : _('ID_CANCEL'),
           handler : function(){
            summaryWindow.close();
           }}
          ],*/
        });

        var tabs = new Array();
        tabs.push(sumaryInfPanel);
        if (response.dynUid != '') {
          tabs.push({title: Ext.util.Format.capitalize(_('ID_MORE_INFORMATION')), bodyCfg: {
            tag: 'iframe',
            id: 'summaryIFrame',
            src: '../cases/summary?APP_UID=' + appUid + '&DEL_INDEX=' + delIndex + '&DYN_UID=' + response.dynUid,
            style: {border: '0px none', height: '300px'},
            onload: ''
          }});
        }
        tabs.push({title: Ext.util.Format.capitalize(_('ID_UPLOADED_DOCUMENTS')), bodyCfg: {
          tag: 'iframe',
          id: 'summaryIFrame',
          src: '../cases/ajaxListener?action=uploadedDocumentsSummary',
          style: {border: '0px none', height: '300px'},
          onload: ''
        }});

        tabs.push({title: Ext.util.Format.capitalize(_('ID_GENERATED_DOCUMENTS')), bodyCfg: {
          tag: 'iframe',
          id: 'summaryIFrame',
          src: '../cases/ajaxListener?action=generatedDocumentsSummary',
          style: {border: '0px none',height: '450px'},
          onload: ''
        }});
        var summaryTabs = new Ext.TabPanel({
          activeTab: 0,
          items: tabs
        });
        summaryWindow.add(summaryTabs);
        summaryWindow.doLayout();
        summaryWindow.show();
      }
      else {
        PMExt.warning(_('ID_WARNING'), response.message);
      }
      summaryWindowOpened = false;
    },
    failure: function (result, request) {
      summaryWindowOpened = false;
    }
  });
}
/* Case Summary - End*/



Ext.Panel.prototype.originalonRender = Ext.Panel.prototype.onRender;

// override onRender method
Ext.Panel.prototype.onRender = function(ct, position) {
    this.originalonRender(ct, position);

    // use the custom rowtbar argument to add it to this TopToolbar
    if(this.tbar && this.rowtbar){
        var rowtbar = this.rowtbar;
        if(!Ext.isArray(rowtbar))
            return;

        for(var i = 0; i < rowtbar.length; i ++) {
            new Ext.Toolbar(rowtbar[i]).render(this.tbar);
        }
    }

    // use the custom rowbbar argument to add it to this BottomToolbar
    if(this.bbar && this.rowbbar) {
        var rowbbar = this.rowbbar;
        if(!Ext.isArray(rowbbar))
            return;

        for(var i = 0; i < rowbbar.length; i ++) {
            new Ext.Toolbar(rowbbar[i]).render(this.bbar);
        }
    }
}

