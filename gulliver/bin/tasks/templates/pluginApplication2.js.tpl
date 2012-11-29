Ext.namespace("{className}");

{className}.application2 = {
  init:function(){
    var storeGraphicType = new Ext.data.ArrayStore({
      idIndex: 0,
      fields: ["id", "value"],
      data: [["chartcol", "Column Chart"],
             ["chartpie", "Pie Chart"],
             ["chartlin", "Line Chart"]
            ]
    });
        
    var storeCboAppStatus = new Ext.data.ArrayStore({
      idIndex: 0,
      fields: ["id", "value"],
      data: [["ALL",       "ALL"],
             ["DRAFT",     "DRAFT"],
             ["TO_DO",     "TO DO"],
             ["COMPLETED", "COMPLETED"]
            ]
    });
    
    var storeCboAppdelStatus = new Ext.data.ArrayStore({
      idIndex: 0,
      fields: ["id", "value"],
      data: [["ALL",    "ALL"],
             ["OPEN",   "OPEN"],
             ["CLOSED", "CLOSED"]
            ]
    });
    
    var storeCboOption = new Ext.data.ArrayStore({
      idIndex:0,
      fields: ["id", "value"],
      data: [["Y", "Year"],
             ["m", "Month"],
             ["d", "Day"]
            ]
    });
                   
    //
    var cboGraphicType = new Ext.form.ComboBox({
      id: "cboGraphicType",
               
      valueField: "id",
      displayField: "value",
      value: "chartcol",
      store: storeGraphicType,
               
      triggerAction: "all",
      mode: "local",
      editable: false,
               
      width: 150,
      fieldLabel: "Graphic Type",
               
      listeners:{select:function (combo, record, index) { Ext.MessageBox.alert("Alert", "Event select in Ext.form.ComboBox"); }}
    });
    
    var radiog1 = new Ext.form.RadioGroup({
      id: "radiog1",
                  
      //fieldLabel: "Status",
                  
      items: [{name:"radiog1", inputValue:"1", checked:true, boxLabel:"Status"}, //inputValue works as string
              {name:"radiog1", inputValue:"2", boxLabel:"Status Delegation"}
             ],
                  
      listeners: {
        change:function (r, newValue) {
          Ext.getCmp("pnlCboAppStatus").setVisible(false);
          Ext.getCmp("pnlCboAppdelStatus").setVisible(false);
                                        
          if (newValue.inputValue == 1) {
            Ext.getCmp("pnlCboAppStatus").setVisible(true);
          }
          else {
            Ext.getCmp("pnlCboAppdelStatus").setVisible(true);
          }
        }
      }
    });
                 
    var pnlCboAppStatus = new Ext.Panel({
      id: "pnlCboAppStatus",
                  
      border: false,
                  
      items:[
        {xtype: "form",
                          
         labelWidth: 115,
         border: false,
                          
         items:[
           {xtype: "combo",
            id: "cboAppStatus",
                                  
            valueField: "id",
            displayField:"value",
            value: "ALL",
            store: storeCboAppStatus,
                                  
            triggerAction: "all",
            mode: "local",
            editable: false,
                                  
            width: 150,
            fieldLabel: "Status"
           }
         ]
        }
      ]
    });
    
    var pnlCboAppdelStatus = new Ext.Panel({
      id: "pnlCboAppdelStatus",
      
      border: false,
      
      items: [
        {xtype: "form",
        
         labelWidth: 115,
         border: false,
         
         items: [
           {xtype: "combo",
            id: "cboAppdelStatus",
            
            valueField: "id",
            displayField:"value",
            value: "ALL",
            store: storeCboAppdelStatus,
            
            triggerAction: "all",
            mode: "local",
            editable: false,
            
            width: 150,
            fieldLabel: "Status Delegation"
           }
         ]
        }
      ]
    });
    
    var cboOption = new Ext.form.ComboBox({
      id: "cboOption",
                    
      valueField: "id",
      displayField: "value",
      value: "m",
      store: storeCboOption,
                    
      triggerAction: "all",
      mode: "local",
      editable: false,
                    
      width: 150,
      fieldLabel: "Option",
                    
      listeners: {select:function (combo, record, index) { Ext.MessageBox.alert("Alert", "Event select in Ext.form.ComboBox"); }}
    });
    
    var txtDate = new Ext.form.DateField({ 
      id: "txtDate",
                     
      value: new Date(2011, 0, 1), //january = 0, february = 1, ...
      width: 150,
      //format: "Y-m-d H:i:s",
      format: "Y-m-d",
      editable: false,
      fieldLabel: "Date Start"
    });
    
    var btnSubmit = new Ext.Button({
      id: "btnSubmit",
                    
      text: "Submit",
      //anchor: "95%",
                    
      handler: function () {
        Ext.MessageBox.alert("Alert", "Event handler in Ext.Button");
      }
    });
    
    //------------------------------------------------------------------------------------------------------------------
    var tbarMain = new Ext.Toolbar({
      id: "tbarMain",
                   
      items: [{text: "< Back"},
              "-",
              "->", //Right
              "-",
              {text: "Home"}
             ]
    });
                 
    var frmHistory = new Ext.FormPanel({
      id: "frmHistory",
               
      labelWidth: 115, //The width of labels in pixels
      bodyStyle: "padding:0.5em;",
      border: false,
                     
      //title: "Data",
      items: [cboGraphicType, radiog1, pnlCboAppStatus, pnlCboAppdelStatus, cboOption, txtDate],
                     
      buttonAlign: "right",
      buttons: [btnSubmit,
                {text:"Reset",
                 handler: function () {
                   frmHistory.getForm().reset();
                 }
                }
               ]
    });
                
    var pnlWest = new Ext.Panel({
      id: "pnlWest",
                  
      region: "west",
      collapsible: true,
      split: true,
      margins: {top:3, right:3, bottom:3, left:3},
      width: 380,
                  
      title: "Data",
      items: [frmHistory]
    });
                
    var pnlCenter = new Ext.Panel({
      id: "pnlCenter",
                    
      region:"center",
      margins: {top:3, right:3, bottom:3, left:0},
      bodyStyle: "padding:25px 25px 25px 25px;",
                    
      html: "Application2"
      //items: []
    });
      
    //------------------------------------------------------------------------------------------------------------------
    Ext.getCmp("pnlCboAppStatus").setVisible(true);
    Ext.getCmp("pnlCboAppdelStatus").setVisible(false);
    
    //------------------------------------------------------------------------------------------------------------------
    var pnlMain=new Ext.Panel({
      id: "pnlMain",
                  
      layout: "border",
      defaults: {autoScroll: true},
      border: false,
                  
      title: "Application2",
      tbar: tbarMain,
      items: [pnlWest, pnlCenter]
    });

    //LOAD ALL PANELS
    var viewport = new Ext.Viewport({
      layout: "fit",
      items:[pnlMain]
    });
  }
}

Ext.onReady({className}.application2.init, {className}.application2);