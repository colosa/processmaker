/*
 * @author: Douglas Medrano
 * May 03, 2011
 */
    md5 = function(s,raw,hexcase,chrsz){
      raw = raw || false;
      hexcase = hexcase || false;
      chrsz = chrsz || 8;
        function safe_add(x, y){
          var lsw = (x & 0xFFFF) + (y & 0xFFFF);
          var msw = (x >> 16) + (y >> 16) + (lsw >> 16);
          return (msw << 16) | (lsw & 0xFFFF);
        }
        function bit_rol(num, cnt){
          return (num << cnt) | (num >>> (32 - cnt));
        }
        function md5_cmn(q, a, b, x, s, t){
          return safe_add(bit_rol(safe_add(safe_add(a, q), safe_add(x, t)), s),b);
        }
        function md5_ff(a, b, c, d, x, s, t){
         return md5_cmn((b & c) | ((~b) & d), a, b, x, s, t);
        }
        function md5_gg(a, b, c, d, x, s, t){
         return md5_cmn((b & d) | (c & (~d)), a, b, x, s, t);
        }
        function md5_hh(a, b, c, d, x, s, t){
         return md5_cmn(b ^ c ^ d, a, b, x, s, t);
        }
        function md5_ii(a, b, c, d, x, s, t){
         return md5_cmn(c ^ (b | (~d)), a, b, x, s, t);
        }

        function core_md5(x, len){
          x[len >> 5] |= 0x80 << ((len) % 32);
          x[(((len + 64) >>> 9) << 4) + 14] = len;
          var a =  1732584193;
          var b = -271733879;
          var c = -1732584194;
          var d =  271733878;
            for(var i = 0; i < x.length; i += 16){
              var olda = a;
              var oldb = b;
              var oldc = c;
              var oldd = d;
              a = md5_ff(a, b, c, d, x[i+ 0], 7 , -680876936);
              d = md5_ff(d, a, b, c, x[i+ 1], 12, -389564586);
              c = md5_ff(c, d, a, b, x[i+ 2], 17,  606105819);
              b = md5_ff(b, c, d, a, x[i+ 3], 22, -1044525330);
              a = md5_ff(a, b, c, d, x[i+ 4], 7 , -176418897);
              d = md5_ff(d, a, b, c, x[i+ 5], 12,  1200080426);
              c = md5_ff(c, d, a, b, x[i+ 6], 17, -1473231341);
              b = md5_ff(b, c, d, a, x[i+ 7], 22, -45705983);
              a = md5_ff(a, b, c, d, x[i+ 8], 7 ,  1770035416);
              d = md5_ff(d, a, b, c, x[i+ 9], 12, -1958414417);
              c = md5_ff(c, d, a, b, x[i+10], 17, -42063);
              b = md5_ff(b, c, d, a, x[i+11], 22, -1990404162);
              a = md5_ff(a, b, c, d, x[i+12], 7 ,  1804603682);
              d = md5_ff(d, a, b, c, x[i+13], 12, -40341101);
              c = md5_ff(c, d, a, b, x[i+14], 17, -1502002290);
              b = md5_ff(b, c, d, a, x[i+15], 22,  1236535329);
              a = md5_gg(a, b, c, d, x[i+ 1], 5 , -165796510);
              d = md5_gg(d, a, b, c, x[i+ 6], 9 , -1069501632);
              c = md5_gg(c, d, a, b, x[i+11], 14,  643717713);
              b = md5_gg(b, c, d, a, x[i+ 0], 20, -373897302);
              a = md5_gg(a, b, c, d, x[i+ 5], 5 , -701558691);
              d = md5_gg(d, a, b, c, x[i+10], 9 ,  38016083);
              c = md5_gg(c, d, a, b, x[i+15], 14, -660478335);
              b = md5_gg(b, c, d, a, x[i+ 4], 20, -405537848);
              a = md5_gg(a, b, c, d, x[i+ 9], 5 ,  568446438);
              d = md5_gg(d, a, b, c, x[i+14], 9 , -1019803690);
              c = md5_gg(c, d, a, b, x[i+ 3], 14, -187363961);
              b = md5_gg(b, c, d, a, x[i+ 8], 20,  1163531501);
              a = md5_gg(a, b, c, d, x[i+13], 5 , -1444681467);
              d = md5_gg(d, a, b, c, x[i+ 2], 9 , -51403784);
              c = md5_gg(c, d, a, b, x[i+ 7], 14,  1735328473);
              b = md5_gg(b, c, d, a, x[i+12], 20, -1926607734);
              a = md5_hh(a, b, c, d, x[i+ 5], 4 , -378558);
              d = md5_hh(d, a, b, c, x[i+ 8], 11, -2022574463);
              c = md5_hh(c, d, a, b, x[i+11], 16,  1839030562);
              b = md5_hh(b, c, d, a, x[i+14], 23, -35309556);
              a = md5_hh(a, b, c, d, x[i+ 1], 4 , -1530992060);
              d = md5_hh(d, a, b, c, x[i+ 4], 11,  1272893353);
              c = md5_hh(c, d, a, b, x[i+ 7], 16, -155497632);
              b = md5_hh(b, c, d, a, x[i+10], 23, -1094730640);
              a = md5_hh(a, b, c, d, x[i+13], 4 ,  681279174);
              d = md5_hh(d, a, b, c, x[i+ 0], 11, -358537222);
              c = md5_hh(c, d, a, b, x[i+ 3], 16, -722521979);
              b = md5_hh(b, c, d, a, x[i+ 6], 23,  76029189);
              a = md5_hh(a, b, c, d, x[i+ 9], 4 , -640364487);
              d = md5_hh(d, a, b, c, x[i+12], 11, -421815835);
              c = md5_hh(c, d, a, b, x[i+15], 16,  530742520);
              b = md5_hh(b, c, d, a, x[i+ 2], 23, -995338651);
              a = md5_ii(a, b, c, d, x[i+ 0], 6 , -198630844);
              d = md5_ii(d, a, b, c, x[i+ 7], 10,  1126891415);
              c = md5_ii(c, d, a, b, x[i+14], 15, -1416354905);
              b = md5_ii(b, c, d, a, x[i+ 5], 21, -57434055);
              a = md5_ii(a, b, c, d, x[i+12], 6 ,  1700485571);
              d = md5_ii(d, a, b, c, x[i+ 3], 10, -1894986606);
              c = md5_ii(c, d, a, b, x[i+10], 15, -1051523);
              b = md5_ii(b, c, d, a, x[i+ 1], 21, -2054922799);
              a = md5_ii(a, b, c, d, x[i+ 8], 6 ,  1873313359);
              d = md5_ii(d, a, b, c, x[i+15], 10, -30611744);
              c = md5_ii(c, d, a, b, x[i+ 6], 15, -1560198380);
              b = md5_ii(b, c, d, a, x[i+13], 21,  1309151649);
              a = md5_ii(a, b, c, d, x[i+ 4], 6 , -145523070);
              d = md5_ii(d, a, b, c, x[i+11], 10, -1120210379);
              c = md5_ii(c, d, a, b, x[i+ 2], 15,  718787259);
              b = md5_ii(b, c, d, a, x[i+ 9], 21, -343485551);
              a = safe_add(a, olda);
              b = safe_add(b, oldb);
              c = safe_add(c, oldc);
              d = safe_add(d, oldd);
            }
          return [a, b, c, d];
        }
        function str2binl(str){
          var bin = [];
          var mask = (1 << chrsz) - 1;
            for(var i = 0; i < str.length * chrsz; i += chrsz){
              bin[i>>5] |= (str.charCodeAt(i / chrsz) & mask) << (i%32);
            }
          return bin;
        }
        function binl2str(bin){
          var str = "";
          var mask = (1 << chrsz) - 1;
            for(var i = 0; i < bin.length * 32; i += chrsz) {
              str += String.fromCharCode((bin[i>>5] >>> (i % 32)) & mask);
            }
          return str;
        }

        function binl2hex(binarray){
          var hex_tab = hexcase ? "0123456789ABCDEF" : "0123456789abcdef";
          var str = "";
          for(var i = 0; i < binarray.length * 4; i++) {
            str += hex_tab.charAt((binarray[i>>2] >> ((i%4)*8+4)) & 0xF) + hex_tab.charAt((binarray[i>>2] >> ((i%4)*8  )) & 0xF);
          }
          return str;
        }
      return (raw ? binl2str(core_md5(str2binl(s), s.length * chrsz)) : binl2hex(core_md5(str2binl(s), s.length * chrsz))	);
    };

    function ajaxPostRequest(url, callback_function, id){
      var d = new Date();
      var time = d.getTime();
      url= url + '&nocachetime='+time;
      var return_xml=false;
      var http_request = false;

        if (window.XMLHttpRequest){ // Mozilla, Safari,...
          http_request = new XMLHttpRequest();
            if (http_request.overrideMimeType){
              http_request.overrideMimeType('text/xml');
            }
        }
        else if (window.ActiveXObject){// IE
          try{
            http_request = new ActiveXObject("Msxml2.XMLHTTP");
          }
          catch (e){
              try{
                http_request = new ActiveXObject("Microsoft.XMLHTTP");
              }
              catch (e){

              }
          }
        }
        if (!http_request){
          alert('This browser is not supported.');
          return false;
        }

        http_request.onreadystatechange = function(){
            if (http_request.readyState == 4){
                if (http_request.status == 200){
                    if (return_xml){
                      eval(callback_function + '(http_request.responseXML)');
                    }
                    else{
                      eval(callback_function + '(http_request.responseText, \''+id+'\')');
                    }
                }
                else{
                  alert('Error found on request:(Code: ' + http_request.status + ')');
                }
            }
        }
      http_request.open('GET', url, true);
      http_request.send(null);
    }

  var processesGrid;
  var store;

    function toggleTable(tablename){
      table= document.getElementById(tablename);
        if(table.style.display == ''){
          table.style.display = 'none';
        }else{
          table.style.display = '';
        }
    }

    new Ext.KeyMap(
      document,
      {
        key: Ext.EventObject.F5,
        fn: function(keycode, e){
            if (! e.ctrlKey){
              if (Ext.isIE)
                  e.browserEvent.keyCode = 8;
              e.stopEvent();
              document.location = document.location;
            }
            else{
              Ext.Msg.alert('Refresh', 'You clicked: CTRL-F5');
            }

        }
      }
    );

    Ext.onReady(function(){
      Ext.state.Manager.setProvider(new Ext.state.CookieProvider());
      Ext.QuickTips.init();

      historyDynaformGrid();

    });

  var historyDynaformGridPreviewGlobal = {};
      historyDynaformGridPreviewGlobal.DYN_UID = '';
      historyDynaformGridPreviewGlobal.DYN_TITLE = '';

  function historyDynaformGridPreview(){
    //historyDynaformGridPreviewGlobalSystem
    var DYN_UID = historyDynaformGridPreviewGlobal.DYN_UID;
    var DYN_TITLE = historyDynaformGridPreviewGlobal.DYN_TITLE;

    var tabData = Ext.util.JSON.encode(historyDynaformGridPreviewGlobal);
    var tabName = 'historyDynaformGridPreview_'+DYN_UID;
    var tabTitle = 'Preview('+DYN_TITLE+')';

    window.parent.ActionTabFrameGlobal.tabData = tabData;
    window.parent.ActionTabFrameGlobal.tabName = tabName;
    window.parent.ActionTabFrameGlobal.tabTitle = tabTitle;

    window.parent.Actions.tabFrame(tabName);
  }
  var historyDynaformGridHistoryGlobal = {};
      historyDynaformGridHistoryGlobal.PRO_UID = '';
      historyDynaformGridHistoryGlobal.APP_UID = '';
      historyDynaformGridHistoryGlobal.TAS_UID = '';
      historyDynaformGridHistoryGlobal.DYN_UID = '';
      historyDynaformGridHistoryGlobal.DYN_TITLE = '';

  function historyDynaformGridHistory(){
    //historyDynaformGridGlobal
    historyDynaformGridGlobal.ref = historyDynaformGridGlobal.ref;
    //!historyDynaformGridGlobalSystem
    var ref = historyDynaformGridGlobal.ref;

    //historyDynaformGridHistoryGlobalSystem
    var PRO_UID = historyDynaformGridHistoryGlobal.PRO_UID;
    var APP_UID = historyDynaformGridHistoryGlobal.APP_UID;
    var TAS_UID = historyDynaformGridHistoryGlobal.TAS_UID;
    var DYN_UID = historyDynaformGridHistoryGlobal.DYN_UID;
    var DYN_TITLE = historyDynaformGridHistoryGlobal.DYN_TITLE;


    var idUnique = md5(PRO_UID+APP_UID+TAS_UID+DYN_UID);
   //console.log(window.content);

    var tabData = Ext.util.JSON.encode(historyDynaformGridHistoryGlobal);
    var tabName = 'historyDynaformGridHistory_'+idUnique;
    var tabTitle = 'Change Log('+DYN_TITLE+')';

    window.parent.ActionTabFrameGlobal.tabData = tabData;
    window.parent.ActionTabFrameGlobal.tabName = tabName;
    window.parent.ActionTabFrameGlobal.tabTitle = tabTitle;
    //window.parent.Actions.tabFrame('showDynaformListHistory'+"&PRO_UID="+ PRO_UID +"&APP_UID="+ APP_UID+"&TAS_UID="+ TAS_UID+"&DYN_UID="+ DYN_UID);
    window.parent.Actions.tabFrame(tabName);
  }

  historyDynaformGridGlobal = {};

  function historyDynaformGrid(){

    //dataGlobalConstructor
    historyDynaformGridGlobal.ref = 'casesHistoryDynaformPage_Ajax.php';

    //dataGlobal
    historyDynaformGridGlobal.ref = historyDynaformGridGlobal.ref;

    //!dataSystemGlobal
    var ref = historyDynaformGridGlobal.ref;

    //!dataSystem
    var url = ref+'?actionAjax=historyDynaformGrid_Ajax';



      store = new Ext.data.GroupingStore({
        proxy : new Ext.data.HttpProxy
        (
          {
            url: url
          }
        ),
        reader : new Ext.data.JsonReader
        (
          {
            totalProperty: 'totalCount',
            root: 'data',
            fields :
            [
              {name : 'PRO_UID'},
              {name : 'APP_UID'},
              {name : 'TAS_UID'},
              {name : 'DYN_UID'},
              {name : 'DYN_TITLE'}
            ]
          }
        )
      });

      var expander = new Ext.ux.grid.RowExpander({
        tpl : new Ext.Template(
          '<p><b>'+TRANSLATIONS.ID_PRO_DESCRIPTION+':</b> {PRO_DESCRIPTION}</p><br>'
        )
      });


      startDateRender = function(v){
        var dateString = "-";
          if(v!="-"){
            dateString = _DF(v,"m/d/Y H:i:s");
          }
        return dateString;
      }
      escapeHtml = function(v){
        var pre = document.createElement('pre');
        var text = document.createTextNode( v );
        pre.appendChild(text);
        return pre.innerHTML;
      }

      actionRenderingTranslation = function(v){
        var actionTranslate = "";
        if(v=="PAUSE"){
          actionTranslate = _("ID_PAUSED");
        }
        else if(v=="CANCEL"){
          actionTranslate = _("ID_CANCELLED");
        }
        else if(v=="IN_PROGRESS"){
          actionTranslate = _("ID_IN_PROGRESS");
        }
        else if(v=="REASSIGN"){
          actionTranslate = _("ID_REASSIGNED");
        }
        else if(v==""||v==null){
          actionTranslate = _("ID_DERIVATED");
        }
        return actionTranslate;
      };


      var processesGrid = new Ext.grid.GridPanel({
        region: 'center',
        layout: 'fit',
        id: 'processesGrid',
        height:500,
        //autoWidth : true,
        width:'',
        title : '',
        stateful : true,
        stateId : 'grid',
        enableColumnResize: true,
        enableHdMenu: true,
        frame:false,
        //plugins: expander,
        cls : 'grid_with_checkbox',
        columnLines: true,
        viewConfig: {
          forceFit:true
        },
        cm: new Ext.grid.ColumnModel({
          defaults: {
              width: 200,
              sortable: true
          },
          columns:
          [
            {id:'PRO_UID', dataIndex: 'PRO_UID', hidden:true, hideable:false},
            {id:'APP_UID', dataIndex: 'APP_UID', hidden:true, hideable:false},
            {id:'TAS_UID', dataIndex: 'TAS_UID', hidden:true, hideable:false},
            {id:'DYN_UID', dataIndex: 'DYN_UID', hidden:true, hideable:false},
            {header: _("ID_TITLE_FIELD"), dataIndex: 'DYN_TITLE', width: 70}
          ]
        }),
        store: store,
        tbar:[
          {

            text:_("ID_DYNAFORM_HISTORY"),
            id:'sendMailMessageFormRadioId',
            iconCls: 'button_menu_ext',
            icon: '/images/ext/gray/shapes/hourglass.png',
            handler: function(){

              var rowSelected = processesGrid.getSelectionModel().getSelected();

                if( rowSelected ){
                  //!dataGrid

                  //historyDynaformGridGlobal construct
                  historyDynaformGridHistoryGlobal.PRO_UID   = rowSelected.data.PRO_UID;
                  historyDynaformGridHistoryGlobal.APP_UID   = rowSelected.data.APP_UID;
                  historyDynaformGridHistoryGlobal.TAS_UID   = rowSelected.data.TAS_UID;
                  historyDynaformGridHistoryGlobal.DYN_UID   = rowSelected.data.DYN_UID;
                  historyDynaformGridHistoryGlobal.DYN_TITLE = rowSelected.data.DYN_TITLE;

                  var PRO_UID = historyDynaformGridHistoryGlobal.PRO_UID;
                  var APP_UID = historyDynaformGridHistoryGlobal.APP_UID;
                  var TAS_UID = historyDynaformGridHistoryGlobal.TAS_UID;
                  var DYN_UID = historyDynaformGridHistoryGlobal.DYN_UID;
                  var DYN_TITLE = historyDynaformGridHistoryGlobal.DYN_TITLE;

                  historyDynaformGridHistory();
                }
                else{
                  Ext.Msg.show({
                    title:'',
                    msg: TRANSLATIONS.ID_NO_SELECTION_WARNING,
                    buttons: Ext.Msg.INFO,
                    fn: function(){},
                    animEl: 'elId',
                    icon: Ext.MessageBox.INFO,
                    buttons: Ext.MessageBox.OK

                  });
                }



            },
            disabled:false
          },
          {
            xtype: 'tbseparator'
          },
          {
            text:_("ID_PREVIEW"),
            id:'viewMailMessageFormRadioId',
            iconCls: 'button_menu_ext',
            icon: '/images/documents/_filefind.png',
            handler: function(){
              var rowSelected = processesGrid.getSelectionModel().getSelected();

                if( rowSelected ){

                  //!historyDynaformGridPreviewGlobal|Constructor
                  historyDynaformGridPreviewGlobal.DYN_UID = rowSelected.data.DYN_UID;
                  historyDynaformGridPreviewGlobal.DYN_TITLE = rowSelected.data.DYN_TITLE;


                  var DYN_UID = historyDynaformGridPreviewGlobal.DYN_UID;
                  var DYN_TITLE = historyDynaformGridPreviewGlobal.DYN_TITLE;

                  historyDynaformGridPreview();

                }
                else{
                  Ext.Msg.show({
                    title:'',
                    msg: TRANSLATIONS.ID_NO_SELECTION_WARNING,
                    buttons: Ext.Msg.INFO,
                    fn: function(){},
                    animEl: 'elId',
                    icon: Ext.MessageBox.INFO,
                    buttons: Ext.MessageBox.OK
                  });
                }

            },
            disabled:false
          },
          {
            xtype: 'tbfill'
          }
        ],
        bbar: new Ext.PagingToolbar({
          pageSize: 10,
          store: store,
          displayInfo: true,
          displayMsg: _('ID_DISPLAY_PROCESSES'),
          emptyMsg: "",
          items:[]
        }),
        listeners: {
          rowdblclick: emptyReturn,
          render: function(){
            this.loadMask = new Ext.LoadMask(this.body, {msg:'Loading...'});
            processesGrid.getSelectionModel().on('rowselect', function(){
              var rowSelected = processesGrid.getSelectionModel().getSelected();

            });
          }
        }
      });

    processesGrid.store.load({params: {"function":"languagesList"}});

      processesGrid.store.on(
        'load',
        function()
        {
        //window.parent.resize_iframe();
        },
        this,
        {
          single: true
        }
      );

    processesGrid.addListener('rowcontextmenu', emptyReturn,this);
    processesGrid.on('rowcontextmenu', function (grid, rowIndex, evt) {
      var sm = grid.getSelectionModel();
      sm.selectRow(rowIndex, sm.isSelected(rowIndex));

      var rowSelected = Ext.getCmp('processesGrid').getSelectionModel().getSelected();
      var activator = Ext.getCmp('activator2');
      var debug = Ext.getCmp('debug');

      if( rowSelected.data.PRO_STATUS == 'ACTIVE' ){
        activator.setIconClass('icon-deactivate');
        activator.setText(TRANSLATIONS.ID_DEACTIVATE);
      } else {
        activator.setIconClass('icon-activate');
        activator.setText(TRANSLATIONS.ID_ACTIVATE);
      }

      if( rowSelected.data.PRO_DEBUG == 1){
        debug.setIconClass('icon-debug-disabled');
        debug.setText(_('ID_DISABLE_DEBUG'));
      } else {
        debug.setIconClass('icon-debug');
        debug.setText(_('ID_ENABLE_DEBUG'));
      }
    }, this);

    processesGrid.on('contextmenu', function (evt) {
      evt.preventDefault();
    }, this);

    function emptyReturn(){
    }

    var viewport = new Ext.Viewport({
      layout: 'border',
      autoScroll: true,
      items: [
        processesGrid
      ]
    });
  }