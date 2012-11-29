Ext.onReady(function(){
    
    var store = new Ext.data.ArrayStore({
        fields: ['name', 'value'],
        idIndex: 0
    });
    var propsGrid = new Ext.grid.GridPanel({
      store : store,
      columns : [{
        id : 'name',
        header : '',
        width : 150,
        sortable : false,
        dataIndex : 'name',
        renderer: function(v){return '<b><font color="#465070">'+v+'</font></b>'},
        align: 'right'
      }, 
      {
        header : '',
        width : 350,
        sortable : false,
        dataIndex : 'value'
      }],
      stripeRows : true,
      autoHeight : true,
      width : 480,
      columnLines: true,
      enableColumnHide: false,
      enableColumnResize: false,
      enableHdMenu: false
    });

    store.loadData(properties);
    propsGrid.render(document.body);

});