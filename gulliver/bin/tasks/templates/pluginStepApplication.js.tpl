Ext.namespace("step{className}");

step{className}.application = {
  init:function(){
    storeApplicationProcess = function () {
      var myMask = new Ext.LoadMask(Ext.getBody(), {msg:"Load application data..."});
      myMask.show();

      Ext.Ajax.request({
        url: "../{className}/step{className}ApplicationAjax",
        method: "POST",
                         
        success:function (result, request) {
                  storeApplication.loadData(Ext.util.JSON.decode(result.responseText));
                  myMask.hide();
                },
        failure:function (result, request) {
                  myMask.hide();
                  Ext.MessageBox.alert("Alert", "Failure application data load");
                }
      });
    };
    
    //stores
    var storeApplication = new Ext.data.Store({
      proxy:new Ext.data.HttpProxy({
        url:    "../{className}/step{className}ApplicationAjax",
        method: "POST"
      }),
      
      //baseParams: ,
            
      reader:new Ext.data.JsonReader({
        root: "resultRoot",
        totalProperty: "resultTotal",
        fields: [{name: "VARIABLE"},
                 {name: "VALUE"}
                ]
      }),
      
      //autoLoad: true, //First call
      
      listeners:{
        beforeload:function (store) {
        }
      }      
    });
    
    //
    var cmodel = new Ext.grid.ColumnModel({
      defaults: {
        width:50,
        sortable:true
      },
      columns:[{header: "Variable", dataIndex: "VARIABLE", width: 25},
               {header: "Value", dataIndex: "VALUE"}
              ]
    });
    
    var grdpnlApplication = new Ext.grid.GridPanel({
      id: "grdpnlUser",
      
      store: storeApplication,
      colModel: cmodel,
      
      columnLines: true,
      viewConfig: {forceFit: true},
      enableColumnResize: true,
      enableHdMenu: false, //Menu of the column
      
      tbar: [new Ext.Action({
               text: "&nbsp;< " + CONFIG.previousStepLabel + "&nbsp;",
               handler: function() {
                 window.location.href = CONFIG.previousStep;
               }
             }),
             "->",
             new Ext.Action({
               text: "&nbsp;" + CONFIG.nextStepLabel + " >&nbsp;",
               handler: function() {
                 window.location.href = CONFIG.nextStep;
               }
             })
            ],
      
      style: "margin: 0 auto 0 auto;",
      width: 550,
      height: 350, 
      title: "Application data",      
      
      renderTo: "divMain"
    });
    
    //Initialize events
    storeApplicationProcess();
  }
}

Ext.onReady(step{className}.application.init, step{className}.application);