/*!
 * Ext JS Library 3.2.1
 * Copyright(c) 2006-2010 Ext JS, Inc.
 * licensing@extjs.com
 * http://www.extjs.com/license
 */

 var weekDays = [['- ALL -','- ' + _('ID_ALL') + ' -'],
                 ['SUN',_('ID_WEEKDAY_ABB_0')],
                 ['MON',_('ID_WEEKDAY_ABB_1')],
                 ['TUE',_('ID_WEEKDAY_ABB_2')],
                 ['WED',_('ID_WEEKDAY_ABB_3')],
                 ['THU',_('ID_WEEKDAY_ABB_4')],
                 ['FRI',_('ID_WEEKDAY_ABB_5')],
                 ['SAT',_('ID_WEEKDAY_ABB_6')]];

  comboStatusStore = new Ext.data.SimpleStore({
    fields: ['id','value'],
    data: weekDays
  });
  var get = '';
  var vars = [], hash;
  var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
  for (var i = 0; i < hashes.length; i++) {
    hash = hashes[i].split('=');
    vars.push(hash[0]);
    vars[hash[0]] = hash[1];
  }
  get = vars[0];
  var message = '';
  function workHourCompositeFieldInterfaz(i){
    //!dataSystem
    var d = new Date();
    var idTime = d.getTime();

    var workHourComposite = 'workHourComposite'+idTime;
    //!

    var CompositeField1 = new Ext.form.CompositeField( {
      xtype       : 'compositefield',
      id          : workHourComposite,
      hideLabel   : true,
      labelWidth  : 100,
      items       : [
        {
          xtype  : 'displayfield',
          width : 10,
          value : ''
        },
        {
          xtype: 'displayfield',
          style: 'text-align:center;color:#808080;font-size:11px;margin-top:5px;',
          value: i-1
        },
        {
          xtype  : 'displayfield',
          width : 5,
          value : ''
        },
        {
          xtype: 'combo',
          fieldLabel: _('ID_STATUS'),
          hiddenName: 'status',
          typeAhead: true,
          mode: 'local',
          store: comboStatusStore,
          displayField: 'value',
          width:  100,
          valueField:'id',
          allowBlank: true,
          triggerAction: 'all',
          emptyText: _('ID_SELECT_STATUS'),
          selectOnFocus:true
        },
        {
          xtype  : 'displayfield',
          width : 10,
          value : ''
        },
        {
         xtype: 'textfield',
         width:  70,
         fieldLabel: _('ID_NAME'),
         name      : 'td1',
         allowBlank: true
        },
        {
          xtype  : 'displayfield',
          width : 10,
          value : ''
        },
        {
         xtype: 'textfield',
         width:  70,
         fieldLabel: _('ID_NAME'),
         name      : 'td2',
         allowBlank: true
        },
        {
          xtype  : 'displayfield',
          width : 10,
          value : ''
        },
        {
          xtype: 'box',
          fieldLabel: _('ID_FILENAME'),
          name: 'filename',
          autoEl: {
            style: 'margin-top:5px',
            html:'<a class="GridLink" style="margin-top:5px;vertical-align:middle;font-size:11px;" href="javascript:fordataWorkDayFieldRemove(\''+workHourComposite+'\','+i+');">Delete</a>'
          }
        }
      ]
    });
    return CompositeField1;
  }

  function fordataWorkDayFieldNew() {

    var dynaformCalendar_ = Ext.getCmp('dynaformCalendar');

    var idWorkHour_= Ext.getCmp('idWorkHour');
    var i = idWorkHour_.items.length;

    idWorkHour_.insert( i, workHourCompositeFieldInterfaz(i) );

    dynaformCalendar_.doLayout();
  }

  function fordataWorkDayFieldRemove(workHourComposite,i) {


    dynaformCalendar_ = Ext.getCmp('dynaformCalendar');
    idWorkHour_= Ext.getCmp('idWorkHour');

    var workHourComposite_ = Ext.getCmp(workHourComposite);

    var idWorkHour_= Ext.getCmp('idWorkHour');

    for( var index = 0 ; index < idWorkHour_.items.length ; index++ ) {
      if( index >= i ) {
        idWorkHour_.items.items[index].items.items[1].value         = index-2;
        idWorkHour_.items.items[index].items.items[1].originalValue = index-2;
      }
    }

    var e = workHourComposite_.el.up( '.x-form-item' );
    idWorkHour_.remove( workHourComposite_ );
    e.remove();
    dynaformCalendar_.doLayout();
  }
  Ext.ux.OrderedFormPanel = Ext.extend( Ext.FormPanel, {
    addAfter : function( a, c ){
      for( var i = 0 ; i < this.items.items.length ; i++ ){
        if( this.items.items[i] == a ){
          this.insert( i + 1, c );
          return;
        }
      }

    },
    addBefore : function( a, c ) {
      for( var i = 0 ; i < this.items.items.length ; i++ ){
        if( this.items.items[i] == a ){
          this.insert( i, c );
          return;
        }
      }
    }
  });

  //[ Global variables
  calendarWorkDayArray = [];
  calendarWorkDayArray[0] = 'SUN';
  calendarWorkDayArray[1] = 'MON';
  calendarWorkDayArray[2] = 'TUE';
  calendarWorkDayArray[3] = 'WED';
  calendarWorkDayArray[4] = 'THU';
  calendarWorkDayArray[5] = 'FRI';
  calendarWorkDayArray[6] = 'SAT';

  calendarWorkDayStatusArray = new Array();
  calendarWorkDayStatusArray['SUN'] = 'On';
  calendarWorkDayStatusArray['MON'] = 'On';
  calendarWorkDayStatusArray['TUE'] = 'On';
  calendarWorkDayStatusArray['WED'] = 'On';
  calendarWorkDayStatusArray['THU'] = 'On';
  calendarWorkDayStatusArray['FRI'] = 'On';
  calendarWorkDayStatusArray['SAT'] = 'On';

  function calendarWorkDayStatusReset() {
    calendarWorkDayStatusArray['SUN'] = 'On';
    calendarWorkDayStatusArray['MON'] = 'On';
    calendarWorkDayStatusArray['TUE'] = 'On';
    calendarWorkDayStatusArray['WED'] = 'On';
    calendarWorkDayStatusArray['THU'] = 'On';
    calendarWorkDayStatusArray['FRI'] = 'On';
    calendarWorkDayStatusArray['SAT'] = 'On';
  }
  //]

Ext.onReady( function() {

  var Employee = Ext.data.Record.create ([
    {
      name: 'name',
      type: 'string'
    },
    {
      name: 'start',
      type: 'string'
    },
    {
      name: 'end',
      type: 'string'
    }
  ]);

  //[ genData
  var genData = function() {

    var data = [];
    bussinesDayArray = fields['BUSINESS_DAY'];

    var rowNameDataArray = new Array();
    rowNameDataArray['7'] = '- ALL -';
    rowNameDataArray['0'] = 'SUN';
    rowNameDataArray['1'] = 'MON' ;
    rowNameDataArray['2'] = 'TUE' ;
    rowNameDataArray['3'] = 'WED' ;
    rowNameDataArray['4'] = 'THU' ;
    rowNameDataArray['5'] = 'FRI' ;
    rowNameDataArray['6'] = 'SAT' ;

    for (i=0;i<bussinesDayArray.length;i++) {
      calendarBusinessDay      = bussinesDayArray[i].CALENDAR_BUSINESS_DAY;
      calendarBusinessStart    = bussinesDayArray[i].CALENDAR_BUSINESS_START;
      calendarBusinessEnd = bussinesDayArray[i].CALENDAR_BUSINESS_END;
      data.push( {
        name : rowNameDataArray[calendarBusinessDay],
        start: calendarBusinessStart,
        end: calendarBusinessEnd
      });
    }
    return data;
  }
  //]

  var store = new Ext.data.GroupingStore({
      reader: new Ext.data.JsonReader(
      {
        fields: Employee
      }
      ),
      data: genData(),
      sortInfo: {field: 'start', direction: 'ASC'}
  });

  //Renderer for Versioning Field
  var horaRender = function(value){
    var out = value;
    if(value==""){
      out = "00:00";
    }
    return out;
  }

  var editor = new Ext.ux.grid.RowEditor( {
    //saveText: 'Update'
  } );

  editor.on( {
    afteredit: function(roweditor, changes, record, rowIndex) {
    },
    validateedit: function(editor, e, options) {
      var gridCalendar_ = Ext.getCmp('gridCalendar');
      var gridRowIndex = editor.rowIndex;
      var gridRecordId = editor.record.id;

      var gridEnd = gridCalendar_.store.data.items[gridRowIndex].data.end;
      var gridName = gridCalendar_.store.data.items[gridRowIndex].data.name;
      var gridStart = gridCalendar_.store.data.items[gridRowIndex].data.start;


      //[ is changed values
      var isChandedValue = 'Off';

      var editorEnd = gridEnd;
      if("end" in e) {
        isChandedValue = 'On';
        editorEnd = e.end;
      }
      var editorName = gridName;
      if("name" in e) {
        isChandedValue = 'On';
        editorName = e.name;
      }
      var editorStart = gridStart;
      if("start" in e) {
        isChandedValue = 'On';
        editorStart = e.start;
      }
      //]

      if(isChandedValue == 'On') {

        var editorStartObject = new Date('07/10/1983 ' + editorStart);
        var editorEndObject   = new Date('07/10/1983 ' + editorEnd);

        var editorStartSecond   = editorStartObject.getTime();
        var editorEndSecond     = editorEndObject.getTime();

        if(editorEndSecond<=editorStartSecond) {
          PMExt.notify( _('ID_ERROR') , _('ID_TIME_STARTING_EXCEED_END'));
          e.name = gridName;
          e.start = gridStart;
          e.end = gridEnd;
        }
        else {

          var dataExist = 'On';
          gridCalendar_.store.each(function(record){
            if(gridRecordId!=record.id) {

              if(editorName == record.data.name) {

                var gridEachStartObject = new Date('07/10/1983 ' + record.data.start);
                var gridEachEndObject   = new Date('07/10/1983 ' + record.data.end);

                var gridEachStartSecond   = gridEachStartObject.getTime();
                var gridEachEndSecond     = gridEachEndObject.getTime();

                if((editorStartSecond > gridEachStartSecond)||(gridEachEndSecond>editorStartSecond))
                  dataExist = 'Off';
                else{
                  if(editorEndSecond>gridEachStartSecond||gridEachEndSecond>editorEndSecond)
                    dataExist = 'Off';
                }
              }
            }
          });
         /* if(dataExist == 'Off') {
            PMExt.notify( _('ID_ERROR') , _('ID_TIME_EXIST_IN_LIST'));
            e.name = gridName;
            e.start = gridStart;
            e.end = gridEnd;
          }*/
        }
      }
      return;
    },
    beforeedit: function(roweditor, rowIndex) {
    }
  });

    grid = new Ext.grid.GridPanel( { //grid work days
      store: store,
      sm: new Ext.grid.RowSelectionModel({
        selectSingle: true,
        listeners:{
          selectionchange: function(sm) {
            if (sm.getCount() == 0) {
              Ext.getCmp('btnRemove').setDisabled(true);
            }
            else {
              Ext.getCmp('btnRemove').setDisabled(false);
            }
          }
        }
      }),
      id: "gridCalendar",
      width: 470,
      height  : 150,
      region:'center',
      margins: '0 5 5 5',
      autoExpandColumn: 'name',
      plugins: [editor],
      view: new Ext.grid.GroupingView({
          markDirty: false
      }),
      tbar: [ {
          iconCls: 'icon-user-add',
          text: _('ID_ADD'),
          handler: function(){
              var e = new Employee({
                  name: '- ALL -',
                  start: '00:00',
                  end: '00:00'

              });
            editor.stopEditing();
            store.insert(0, e);
            grid.getView().refresh();
            grid.getSelectionModel().selectRow(0);
            editor.startEditing(0);
          }
      },
      {
        ref: '../removeBtn',
        iconCls: 'icon-user-delete',
        text: _('ID_REMOVE'),
        id: 'btnRemove',
        disabled: true,
        handler: function(){
          editor.stopEditing();
          var record = grid.getSelectionModel().getSelected();
          records = grid.getStore();

          if (record.data.name == '- ALL -' && records.getCount() === 1)
            PMExt.error( _('ID_ERROR'), _('ID_NODELETEOPTIONALL'));
          else
            store.remove(record);
        }
      }],

        columns: [
          new Ext.grid.RowNumberer(),
          {
            header: _('ID_DAY'),
            id: 'name',
            dataIndex: 'name',
            width: 50,
            sortable: true,
            editor: {
              xtype: 'combo',
              id: 'calendarColumnDayCombo',
              fieldLabel: _('ID_STATUS'),
              hiddenName: 'status',
              typeAhead: true,
              mode: 'local',
              forceSelection: true,
              store: comboStatusStore,
              displayField: 'value',
              width:  150,
              valueField:'id',
              allowBlank: true,
              triggerAction: 'all',
              emptyText: _('ID_SELECT_STATUS'),
              selectOnFocus:true
            },
            renderer: function(value) {
              for(var i = 0; i < weekDays.length; i++) {
                if (weekDays[i].indexOf(value) > -1) {
                  return weekDays[i][1];
                }
              }
              return '';
            }
          },
          {
            header: _('ID_START_HH_MM'),
            dataIndex: 'start',
            width: 150,
            sortable: true,
            editor: {
              xtype: 'timefield',
              minValue: '12:00 AM',
              maxValue: '23:59 PM',
              increment: 30,
              forceSelection: true,
              format: 'H:i'
            },
            renderer: horaRender
          },
          {
            header: _('ID_END_HH_MM'),
            dataIndex: 'end',
            width: 150,
            sortable: true,
            editor: {
              xtype: 'timefield',
              minValue: '12:00 AM',
              maxValue: '23:59 PM',
              increment: 30,
              forceSelection: true,
              format: 'H:i'
            }
          }
        ]
    });

    var EmployeeHoliday = Ext.data.Record.create ([
      {
        name: 'name',
        type: 'string'
      },
      {
        name: 'startDate',
        type: 'date',
        dateFormat: 'n/j/Y'
      },
      {
        name: 'endDate',
        type: 'date',
        dateFormat: 'n/j/Y'
      }
    ]);
    var genDataHoliday = function(){
        var data = [];


      var holidayArray  = fields['HOLIDAY'];
      for (i=0;i<holidayArray.length;i++) {
        holidayArrayName      = holidayArray[i].CALENDAR_HOLIDAY_NAME;
        holidayArrayStart    = holidayArray[i].CALENDAR_HOLIDAY_START;
        holidayArrayEnd      = holidayArray[i].CALENDAR_HOLIDAY_END;

        holidayArrayEnd   = holidayArrayEnd.replace(/-/g,'/');
        holidayArrayStart = holidayArrayStart.replace(/-/g,'/');

        data.push( {
          name : holidayArrayName,
          startDate: Ext.util.Format.date(holidayArrayStart,'m/d/Y'),
          endDate: Ext.util.Format.date(holidayArrayEnd  ,'m/d/Y')
        });
      }
      return data;
    }
    var storeHoliday = new Ext.data.GroupingStore({
        reader: new Ext.data.JsonReader({fields: EmployeeHoliday}),
        data: genDataHoliday(),
        sortInfo: {field: 'startDate', direction: 'ASC'}
    });

    var editorHoliday = new Ext.ux.grid.RowEditor( {
        saveText: _('ID_UPDATE')
    } );


    gridHoliday = new Ext.grid.GridPanel( { //grid holidays
      store: storeHoliday ,
      id: "gridHoliday" ,
      width: 470,
      height  : 150,
      region:'center',
      margins: '0 5 5 5',
      autoExpandColumn: 'name',
      plugins: [editorHoliday],
      view: new Ext.grid.GroupingView( {
        markDirty: false
      } ),
      tbar: [ {
          iconCls: 'icon-user-add',
          text: _('ID_ADD'),
          handler: function(){
              var e = new EmployeeHoliday({
                  name: '',
                  startdt: (new Date()).clearTime(),
                  enddt: (new Date()).clearTime()

              });
              editorHoliday.stopEditing();
              storeHoliday.insert(0, e);
              gridHoliday.getView().refresh();
              gridHoliday.getSelectionModel().selectRow(0);
              editorHoliday.startEditing(0);
          }
      },
      {
        ref: '../removeBtn',
        iconCls: 'icon-user-delete',
        text: _('ID_REMOVE'),
        disabled: false,
        handler: function(){
            editorHoliday.stopEditing();
            var s = gridHoliday.getSelectionModel().getSelections();
            for(var i = 0, r; r = s[i]; i++){
                storeHoliday.remove(r);
            }
        }
      }],

        columns: [
          new Ext.grid.RowNumberer(),
          {
            header: _('ID_NAME'),
            id: 'name',
            dataIndex: 'name',
            width: 50,
            sortable: true,
            editor: {
              xtype: 'textfield',
              allowBlank: true
            }
          },
          {
            header: _('ID_START_DATE_MDY'),
            xtype: 'datecolumn',
            dataIndex: 'startDate',
            width: 150,
            format: 'm/d/Y',
            sortable: true,
            groupRenderer: Ext.util.Format.dateRenderer('M y'),

            editor: {
              xtype: 'datefield',
              allowBlank: true,
              id: 'startdt',
              name: 'startdt',
              listeners: {
                change: function () {
                  Ext.getCmp('enddt').setMinValue(Ext.getCmp('startdt').value);
                }
              }
            }
          } ,
          {
            header: _('ID_END_DATE_MDY'),
            xtype: 'datecolumn',
            dataIndex: 'endDate',
            format: 'm/d/Y',
            width: 150,
            sortable: true,
            groupRenderer: Ext.util.Format.dateRenderer('M y'),
            editor: {
              xtype: 'datefield',
              id: 'enddt',
              name: 'enddt',
              allowBlank: true,
              listeners: {
                change: function () {
                  Ext.getCmp('startdt').setMaxValue(Ext.getCmp('enddt').value);
                }
              }
            }
          }

        ]
    });
    var cstore = new Ext.data.JsonStore ( {
        fields:['month', 'employees', 'salary'],
        data:[],
        refreshData: function(){
        }
    });
    cstore.refreshData();
    storeHoliday.on('add'    , cstore.refreshData, cstore);
    storeHoliday.on('remove' , cstore.refreshData, cstore);
    storeHoliday.on('update' , cstore.refreshData, cstore);


    var gridCalendarDayModification = function (checkboxObject , dayString) {

      var gridCalendar_ = Ext.getCmp('gridCalendar');
      var dayChecked = checkboxObject.checked ;

      if(dayChecked == false) {

        var daySunExist = '0';
        gridCalendar_.store.each(function(record){
          rowData = record.data;
          if(rowData.name == dayString) {
            daySunExist = '1';
          }

        } );

        if(daySunExist == '1') {
          PMExt.confirm (
            _('ID_CONFIRM'),
            _('ID_DELETE_INPUTDOCUMENT_CONFIRM'),
            function() {
              gridCalendar_.store.each(function(record) {
                rowData = record.data;
                if(rowData.name == dayString) {
                  gridCalendar_.store.remove(record);
                }

              });
            },
            function() {
              checkboxObject.setValue(true);
            }
          );
        }

        calendarWorkDayStatusArray [dayString] = 'Off';
      }
      else {
        calendarWorkDayStatusArray [dayString] = 'On';
      }

     //camboDayArray;
     var camboDayArray = [['- ALL -','- ' + _('ID_ALL') + ' -']];
      if (calendarWorkDayStatusArray ['SUN'] == 'On') {
        camboDayArray.push(['SUN',_('ID_WEEKDAY_ABB_0')]);
      }
      if (calendarWorkDayStatusArray ['MON'] == 'On') {
        camboDayArray.push(['MON',_('ID_WEEKDAY_ABB_1')]);
      }
      if (calendarWorkDayStatusArray ['TUE'] == 'On') {
        camboDayArray.push(['TUE',_('ID_WEEKDAY_ABB_2')]);
      }
      if (calendarWorkDayStatusArray ['WED'] == 'On') {
        camboDayArray.push(['WED',_('ID_WEEKDAY_ABB_3')]);
      }
      if (calendarWorkDayStatusArray ['THU'] == 'On') {
        camboDayArray.push(['THU',_('ID_WEEKDAY_ABB_4')]);
      }
      if (calendarWorkDayStatusArray ['FRI'] == 'On') {
        camboDayArray.push(['FRI',_('ID_WEEKDAY_ABB_5')]);
      }
      if (calendarWorkDayStatusArray ['SAT'] == 'On') {
        camboDayArray.push(['SAT',_('ID_WEEKDAY_ABB_6')]);
      }

      var comboStatusStore = new Ext.data.SimpleStore( {
        fields: ['id','value'],
        data: camboDayArray
      } );
      var calendarColumnDayCombo_ = Ext.getCmp('calendarColumnDayCombo');
      calendarColumnDayCombo_.bindStore(comboStatusStore);
    }


    new Ext.Viewport({
      layout: 'fit',
      items: [
         {
          region: 'right',
    //      layout: 'ux.right',
          items: [
            new Ext.ux.OrderedFormPanel({
              id: 'dynaformCalendar',
              labelAlign: "right",
              frame: true,
              autoHeight: true,
              width: 550,
              waitMsgTarget: true,
              bodyStyle: "padding:5px 10px 0 10px;",
              url: "GenerateVDX.ashx",
              standardSubmit: true,
              title: false,
              items: [
                {
                  xtype:'fieldset',
                  title: _('ID_CALENDAR_DEFINITION'),
                  items: [{
                    xtype:'label',
                    text: _('ID_CALENDAR_INVALID_NAME'),
                    name: 'idInvalidCalendarName',
                    id:'idInvalidCalendarName',
                    style:'color:red; padding:125px; ',
                    hidden: true,
                    bodyStyle:'text-align:right;',//'padding:25px',
                    anchor:'90%'
                 },
                    {
                      id         : 'dynaformCalendarName' ,
                      xtype      : 'textfield' ,
                      width      : 200 ,
                      fieldLabel : _('ID_NAME') ,
                      name       : 'name' ,
                      allowBlank : false,
                      listeners: {
                        change: function(){
                          Ext.getCmp('idInvalidCalendarName').hide();
     		                  var validateMessage = '';
                          var canlendarName = Ext.getCmp('dynaformCalendarName').getValue();

                          Ext.Ajax.request( {
                            url: '../adminProxy/calendarValidate',
                            params: {
                              action : 'calendarName',
                              oldName : fields.OLD_NAME,
                              name : canlendarName
                            },
                            success: function(resp){
                              if (resp.responseText != '[]')
                                message = resp.responseText;
                              else
                                message = '';
                            }
                          });
                        },
                        focus: function(){
                          $message = '';
                        }
                      }
                    },
                    {
                      id         : 'dynaformCalendarDescription' ,
                      xtype      : 'textarea' ,
                      border     : true ,
                      name: 'TRI_DESCRIPTION' ,
                      hidden: false ,
                      fieldLabel: _('ID_DESCRIPTION') ,
                      width: 200 ,
                      height: 40
                    } ,
                    {
                      xtype      : 'compositefield',
                      hideLabel  : true,
                      labelWidth : 100,
                      items : [
                        {
                          xtype  : 'displayfield',
                          width  : 192,
                          value  : ''
                        },
                        {
                          id          : 'dynaformCalendarStatus',
                          xtype       : 'checkbox',
                          fieldLabel  : 'd',
                          hideLabel   : true,
                          name        : 'label1',
                          checked     : true,
                          boxLabel    : _('ID_ACTIVE')
                        }
                      ]
                    }
                    ]
                },
                {
                    id   : 'workDays',
                    xtype: 'fieldset',
                    title: _('ID_WORK_DAYS')+'  <i>('+_('ID_3DAYSMINIMUM')+')</i>',
                    items: [
                      {//8
                        id          : 'dynaformCalendarWorkDays',
                        xtype     : 'checkboxgroup',
                        fieldLabel: '&nbsp;',
                        name      : 'SCH_MONTH',
                        hideLabel  : true,
                        allowBlank: true,
                        columns: 4,
                        items: [
                          {
                            boxLabel : _('ID_WEEKDAY_ABB_0'),
                            name: 'M0',
                            readOnly:true,
                            disabled:false,
                            handler : function() {
                              gridCalendarDayModification( this , 'SUN' );
                            }
                          },
                          {
                            boxLabel : _('ID_WEEKDAY_ABB_1'),
                            name: 'M1',
                            checked: true,
                            readOnly:true,
                            disabled:false,
                            handler : function() {
                              gridCalendarDayModification( this , 'MON' );
                            }
                          },
                          {
                            boxLabel : _('ID_WEEKDAY_ABB_2'),
                            name: 'M2',
                            checked: true,
                            readOnly:true,
                            disabled:false,
                            handler : function() {
                              gridCalendarDayModification( this , 'TUE' );
                            }
                          },
                          {
                            boxLabel : _('ID_WEEKDAY_ABB_3'),
                            name: 'M3',
                            checked: true,
                            readOnly:true,
                            disabled:false,
                            handler : function() {
                              gridCalendarDayModification( this , 'WED' );
                            }
                          },
                          {
                            boxLabel : _('ID_WEEKDAY_ABB_4'),
                            name: 'M4',
                            checked: true,
                            readOnly:true,
                            disabled:false,
                            handler : function() {
                              gridCalendarDayModification( this , 'THU' );
                            }
                          },
                          {
                            boxLabel : _('ID_WEEKDAY_ABB_5'),
                            name: 'M5',
                            checked: true,
                            readOnly:true,
                            disabled:false,
                            handler : function() {
                              gridCalendarDayModification( this , 'FRI' );
                            }
                          },
                          {
                            boxLabel : _('ID_SAT'),
                            name: 'M6',
                            readOnly:true,
                            disabled:false,
                            handler : function() {
                              gridCalendarDayModification( this , 'SAT' );
                            }
                          }
                        ]
                      }
                    ]
                },
                {
                  id              : 'idWorkHour',
                  title           : _('ID_WORK_HOURS'),
                  xtype           : 'fieldset',
                  checkboxToggle  : true,
                  autoHeight      : true,
                  defaults        : {width: 560},
                  defaultType     : 'textfield',
                  collapsed       : false,
                  items           : [
                    {
                      xtype       : 'compositefield',
                      hideLabel   : true,
                      layout : 'fit',
                      labelWidth : 100,
                      items : [
                          {
                            xtype  : 'displayfield',
                            width : 1,
                            value : ''
                          },
                          grid
                      ]
                    }
                  ]
                },
                {

                  id: 'idHolidays',
                  title: _('ID_HOLIDAYS'),
                  xtype:'fieldset',
                  checkboxToggle:true,
                  autoHeight:true,
                  defaults: {width: 560},
                  defaultType: 'textfield',
                  collapsed: false,
                  items :[
                    {
                      xtype       : 'compositefield',
                      hideLabel  : true,
                      layout : 'fit',
                      labelWidth : 100,
                      items : [
                          {
                            xtype  : 'displayfield',
                            width : 1,
                            value : ''
                          },
                          gridHoliday
                      ]
                    }
                  ]
                }
              ], //button
      buttons:[
                {
                  text: fields['NEWCALENDAR']=='YES'?_("ID_CREATE") : _("ID_UPDATE"),
                  handler: function() {
                    if(message!=''){
                      Ext.getCmp('idInvalidCalendarName').show();
                      Ext.getCmp('dynaformCalendarName').focus();
                      return false;
                    }
                    var flag = 0;
                    gridHoliday.store.each(function(record) {
                      var start = record.data['startDate'];
                      var end = record.data['endDate'];
                      if ((! start) || (! end))
                        flag = 1;
                    });
                    assignedGrid = grid;
                    var allRows = assignedGrid.getStore();
                    var columns = new Array();
                    var hasSomePrimaryKey = false;

                    //!fordata
                    var canlendarName = Ext.getCmp('dynaformCalendarName').getValue();
                    var calendarDescription = Ext.getCmp('dynaformCalendarDescription').getValue();
                    var calendarStatus = Ext.getCmp('dynaformCalendarStatus').getValue();
                    var calendarStatusString = "INACTIVE";

                    var calendarWorkDays = Ext.getCmp('dynaformCalendarWorkDays').getValue();
                    var calendarWorkDaysArray = new Array();

                    var businessDayStatus = Ext.getCmp('idWorkHour');
                    var businessDayStatusCollapsed = businessDayStatus.collapsed;
                    var businessDayStatusString = "INACTIVE";
                    var businessDay = "";

                    var holidayStatus = Ext.getCmp('idHolidays');
                    var holidayStatusCollapsed = holidayStatus.collapsed;
                    var holidayStatusString = "INACTIVE";
                    var holiday = "";
                    //!fordata

                    var dynaformCalendarWorkDaysArray = Ext.getCmp('dynaformCalendarWorkDays').items.items;
                    var dynaformCalendarWorkDaysArrayLength = dynaformCalendarWorkDaysArray.length;
                    var dynaformCalendarWorkDaysArrayChecked;
                    var dynaformCalendarWorkDaysArrayCheckedArray = new Array();
                    var indexAuxiliar = 0;
                    var arrayDayinCheckbox = new Array();
                    for(i=0;i<dynaformCalendarWorkDaysArrayLength;i++) {
                      dynaformCalendarWorkDaysArrayChecked = dynaformCalendarWorkDaysArray[i].checked;
                      dynaformCalendarWorkDaysArrayName    = dynaformCalendarWorkDaysArray[i].name;
                      if(dynaformCalendarWorkDaysArrayChecked==true) {
                        arrayDayinCheckbox[indexAuxiliar]=(dynaformCalendarWorkDaysArray[i].boxLabel);

                        index = parseInt(dynaformCalendarWorkDaysArrayName.substring(1,2),10);
                        dynaformCalendarWorkDaysArrayCheckedArray[indexAuxiliar] = index;
                        indexAuxiliar++;
                      }
                    }
                    dynaformCalendarWorkDaysArrayCheckedArray = Ext.util.JSON.encode(dynaformCalendarWorkDaysArrayCheckedArray);

                    if( calendarStatus == true ) {
                      calendarStatusString = "ACTIVE";
                    }

                    if( businessDayStatusCollapsed == false ) {
                      businessDayStatusString = "ACTIVE";
                    }

                    if( holidayStatusCollapsed == false ) {
                      holidayStatusString = "ACTIVE";
                    }

                    var gridCalendarColumns = new Array();
                    var gridCalendarColumnsRow = new Array();
                    casesGrid_ = Ext.getCmp('gridCalendar');
                    var rowAllJsonArray = casesGrid_.store.reader.jsonData.data;
                    var rowNameData = '';
                    var arrayDayinGrid = new Array();
                    for (var i = 0; i < allRows.getCount(); i++) {
                      rowData = allRows.data.items[i].data;
                      rowNameData = '';
                      switch(rowData.name) {
                        case '- ALL -':
                          rowNameData = '7';
                        break;
                        case 'SUN':
                          rowNameData = '0';
                        break;
                        case 'MON':
                          rowNameData = '1';
                        break;
                        case 'TUE':
                          rowNameData = '2';
                        break;
                        case 'WED':
                          rowNameData = '3';
                        break;
                        case 'THU':
                          rowNameData = '4';
                        break;
                        case 'FRI':
                          rowNameData = '5';
                        break;
                        case 'SAT':
                          rowNameData = '6';
                        break;
                        default:
                        break;
                      }
                      var gridCalendarColumnsRow = new Object();
                      gridCalendarColumnsRow['CALENDAR_BUSINESS_DAY']   = rowNameData;
                      gridCalendarColumnsRow['CALENDAR_BUSINESS_START'] = rowData.start;
                      gridCalendarColumnsRow['CALENDAR_BUSINESS_END']   = rowData.end;

                      gridCalendarColumns[i+1] = gridCalendarColumnsRow;
                      arrayDayinGrid[i]=rowData.name;
                      if(rowData.name=='- ALL -')
                        var all = 1;
                    }

                    gridCalendarColumns = Ext.util.JSON.encode(gridCalendarColumns);

                    var gridHolidayColumns = new Array();
                    var gridHolidayColumnsRow = new Array();
                    casesGrid_ = Ext.getCmp('gridHoliday');
                    var allRows = casesGrid_.getStore();
                    var rowAllJsonArray = casesGrid_.store.reader.jsonData.data;
                    var rowNameData = '';
                    for (var i = 0; i < allRows.getCount(); i++) {
                      rowData = allRows.data.items[i].data;
                      var gridHolidayColumnsRow = new Object();
                      gridHolidayColumnsRow['CALENDAR_HOLIDAY_NAME']   = rowData.name;
                      gridHolidayColumnsRow['CALENDAR_HOLIDAY_START'] = Ext.util.Format.date(rowData.startDate,'Y-m-d');
                      gridHolidayColumnsRow['CALENDAR_HOLIDAY_END']   = Ext.util.Format.date(rowData.endDate,'Y-m-d');
                      gridHolidayColumns[i+1] = gridHolidayColumnsRow;
                    }
                    gridHolidayColumns = Ext.util.JSON.encode(gridHolidayColumns);
                    var canlendarName = Ext.getCmp('dynaformCalendarName').getValue();
                    var calendarDescription = Ext.getCmp('dynaformCalendarDescription').getValue();
                    var calendarStatus = calendarStatusString;
                    var calendarWorkDays = dynaformCalendarWorkDaysArrayCheckedArray;
                    var businessDayStatus = businessDayStatusString;
                    var businessDay = gridCalendarColumns;
                    var holidayStatus = holidayStatusString;
                    var holiday = gridHolidayColumns;
                    if(flag==0){
                      if (indexAuxiliar>=3){
                        if(all==1){
                          Ext.Ajax.request( {
                            url: '../adminProxy/calendarSave',
                            params: {
                              CALENDAR_UID : CALENDAR_UID,
                              OLD_NAME : "",
                              CALENDAR_NAME : canlendarName,
                              CALENDAR_DESCRIPTION : calendarDescription,
                              CALENDAR_STATUS : calendarStatus,
                              CALENDAR_WORK_DAYS : calendarWorkDays,
                              BUSINESS_DAY_STATUS : businessDayStatus,
                              BUSINESS_DAY : businessDay,
                              HOLIDAY_STATUS : holidayStatus,
                              HOLIDAY : holiday
                            },
                            success: function(resp){
                              if(fields['NEWCALENDAR']=='YES') {
                                PMExt.notify( _('ID_STATUS') , _('ID_CALENDAR_CREATED_SUCCESSFULLY') );
                              }
                              else {
                                PMExt.notify( _('ID_STATUS') , _('ID_CALENDAR_UPDATED_SUCCESSFULLY') );
                              }
                              window.location.href = '../setup/calendarList';
                            }
                          });
                        }
                        else {
                          var flagDay = 0;
                          var indexArray = 0;
                          var arrayDayinGridSize = arrayDayinGrid.length;
                          var arrayDayinCheckboxSize = arrayDayinCheckbox.length;
                          for(var a = 0 ; a<arrayDayinCheckboxSize; a++) {
                            for(var j = 0 ; j<arrayDayinGridSize; j++) {
                              if(arrayDayinCheckbox[a].toUpperCase()==arrayDayinGrid[j]){
                                flagDay = flagDay + 1;
                              }
                              else {
                                var flag = 0;
                              }
                            }
                          }
                          if (flagDay < arrayDayinCheckboxSize)
                            PMExt.error( _('ID_ERROR'), _('ID_SELECT_ALL'));
                          else {
                            Ext.Ajax.request( {
                              url: '../adminProxy/calendarSave',
                              params: {
                                CALENDAR_UID : CALENDAR_UID,
                                OLD_NAME : "",
                                CALENDAR_NAME : canlendarName,
                                CALENDAR_DESCRIPTION : calendarDescription,
                                CALENDAR_STATUS : calendarStatus,
                                CALENDAR_WORK_DAYS : calendarWorkDays,
                                BUSINESS_DAY_STATUS : businessDayStatus,
                                BUSINESS_DAY : businessDay,
                                HOLIDAY_STATUS : holidayStatus,
                                HOLIDAY : holiday
                              },
                              success: function(resp){
                                if(fields['NEWCALENDAR']=='YES') {
                                  PMExt.notify( _('ID_STATUS') , _('ID_CALENDAR_CREATED_SUCCESSFULLY') );
                                }
                                else {
                                  PMExt.notify( _('ID_STATUS') , _('ID_CALENDAR_UPDATED_SUCCESSFULLY') );
                                }
                                window.location.href = '../setup/calendarList';
                              }
                            });
                          }
                        }
                      }
                      else {
                        Ext.Msg.alert( _('ID_ERROR'), _('ID_MOST_AT_LEAST_3_DAY'));
                      }
                    }
                    else {
                      Ext.Msg.alert( _('ID_ERROR'), _('ID_MESSAGE_EMPTY_DATE_FIELD'));
                    }
                    return true;
                  },
                  listeners: {
                    mouseover: function () {
                      Ext.getCmp('idInvalidCalendarName').hide();
                      var validateMessage = '';
                      var canlendarName = Ext.getCmp('dynaformCalendarName').getValue();
                      var oldName = '';
                      if (name!='') {
                        oldName = '&oldName='+name;
                      }
                      Ext.Ajax.request( {
                        url: '../adminProxy/calendarValidate',
                        params: {
                          action : 'calendarName',
                          oldName : fields.OLD_NAME,
                          name : canlendarName
                        },
                        success: function(resp){
                          if (resp.responseText != '[]')
                            message = resp.responseText;
                          else
                            message = '';
                        }
                      });
                    }
                  }
                },
                {
                  text:_("ID_CANCEL"),
                  handler: function() {
                    window.location.href = '../setup/calendarList';
                  }
                }
              ]
            })
          ]
        }
      ]
    });

//Ext.getCmp('idInvalidCalendarName').setVisible(false);
  //[ DATA EDIT
    calendarWorkDayStatusReset();
    var workDayEquivalenceArray = new Array();
    workDayEquivalenceArray['M0'] = 'SUN';
    workDayEquivalenceArray['M1'] = 'MON';
    workDayEquivalenceArray['M2'] = 'TUE';
    workDayEquivalenceArray['M3'] = 'WEN';
    workDayEquivalenceArray['M4'] = 'THU';
    workDayEquivalenceArray['M5'] = 'FRI';
    workDayEquivalenceArray['M6'] = 'SAT';


    var calendarWorkDaysString = fields['CALENDAR_WORK_DAYS'];
    calendarWorkDaysString += '';
    var calendarWorkDaysArray = calendarWorkDaysString.split('|');
    var calendarWorkDaysArraySize = calendarWorkDaysArray.length;
    var day;
    var dynaformCalendarWorkDays_ = Ext.getCmp('dynaformCalendarWorkDays');

    var existDayArray = new Array();
    var existDayKey = 0;
    for(var i = 0 ; i< calendarWorkDaysArraySize; i++) {
      day = calendarWorkDaysArray[i];
      existDayArray[existDayKey] = '"M'+calendarWorkDaysArray[i]+'":"'+'M'+calendarWorkDaysArray[i]+'"';
      existDayKey++;
    }
    eval('var existDayObject = {'+existDayArray.join(',')+'};');
    var dayName;
    dynaformCalendarWorkDays_.items.each(function(dayObject){
      dayName = dayObject.name;
      if(dayName in existDayObject) {
        dayObject.setValue(true);
        calendarWorkDayStatusArray[workDayEquivalenceArray[dayName]]='On';
      }
      else {
        dayObject.setValue(false);
        calendarWorkDayStatusArray[workDayEquivalenceArray[dayName]]='Off';
      }
    });

     //camboDayArray;
     var camboDayArray = [['- ALL -','- ' + _('ID_ALL') + ' -']];
      if (calendarWorkDayStatusArray ['SUN'] == 'On') {
        camboDayArray.push(['SUN',_('ID_WEEKDAY_ABB_0')]);
      }
      if (calendarWorkDayStatusArray ['MON'] == 'On') {
        camboDayArray.push(['MON',_('ID_WEEKDAY_ABB_1')]);
      }
      if (calendarWorkDayStatusArray ['TUE'] == 'On') {
        camboDayArray.push(['TUE',_('ID_WEEKDAY_ABB_2')]);
      }
      if (calendarWorkDayStatusArray ['WED'] == 'On') {
        camboDayArray.push(['WED',_('ID_WEEKDAY_ABB_3')]);
      }
      if (calendarWorkDayStatusArray ['THU'] == 'On') {
        camboDayArray.push(['THU',_('ID_WEEKDAY_ABB_4')]);
      }
      if (calendarWorkDayStatusArray ['FRI'] == 'On') {
        camboDayArray.push(['FRI',_('ID_WEEKDAY_ABB_5')]);
      }
      if (calendarWorkDayStatusArray ['SAT'] == 'On') {
        camboDayArray.push(['SAT',_('ID_WEEKDAY_ABB_6')]);
      }
      var comboStatusStore = new Ext.data.SimpleStore( {
        fields: ['id','value'],
        data: camboDayArray
      } );
      var calendarColumnDayCombo_ = Ext.getCmp('calendarColumnDayCombo');
      calendarColumnDayCombo_.bindStore(comboStatusStore);


    var dynaformCalendarName_ = Ext.getCmp('dynaformCalendarName');
    dynaformCalendarName_.setValue(fields['CALENDAR_NAME']);

    var dynaformCalendarName_ = Ext.getCmp('dynaformCalendarDescription');
    dynaformCalendarName_.setValue(fields['CALENDAR_DESCRIPTION']);

    var dynaformCalendarName_ = Ext.getCmp('dynaformCalendarStatus');
    if(fields['CALENDAR_STATUS']== 'ACTIVE' ) {
      dynaformCalendarName_.setValue(true);
    }
    else if( fields['CALENDAR_STATUS'] == 'INACTIVE' ) {
      dynaformCalendarName_.setValue(false);
    }
    else {
      dynaformCalendarName_.setValue(true);
    }
  //]
  Ext.getCmp('idInvalidCalendarName').hide();
  if (get != "edit") {
    var validateMessage = '';
    var canlendarName = Ext.getCmp('dynaformCalendarName').getValue();
    var oldName = '';

    if (name!='') {
      oldName = '&oldName='+name;
    }

    Ext.Ajax.request( {
      url: '../adminProxy/calendarValidate',
      params: {
        action : 'calendarName',
        oldName : fields.OLD_NAME,
        name : canlendarName
      },
      success: function(resp){
        if (resp.responseText != '[]')
          message = resp.responseText;
        else
          message = '';
      }
    });
  }

});
