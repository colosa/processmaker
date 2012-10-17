function abc(panel, txt) {
    /*
     * commonDialog ( '', 'saved' , 'saved', {}, '' ) ; setTimeout (
     * leimnud.closure({instance:myDialog,method:function(panel){
     *
     * myDialog.remove(); panel.tabLastSelected=false; panel.tabSelected=1;
     * panel.makeTab(); },args:panel}) , 1000 );
     */
    var img = document.getElementById('workPeriodGraph');
    img.src = 'workPeriodGraph?b=' + Math.random();

    // panel.clearContent();
    // panel.addContent ( txt );
    return false;
}

function showHideFilterForm(divName) {
    if (document.getElementById(divName).style.display === 'none')
        document.getElementById(divName).style.display = '';
    else
        document.getElementById(divName).style.display = 'none';
}

function newHoliday(ev) {
    var coor = leimnud.dom.mouse(ev);
    var myPanel = new leimnud.module.panel();
    myPanel.options = {
        size : {
            w : 500,
            h : 200
        },
        position : {
            x : coor.x - 200,
            y : coor.y
        },
        title : "New Holiday",
        theme : "panel",
        control : {
            close : true,
            drag : true
        },
        fx : {
            modal : true
        }
    };

    myPanel.make();

    var r = new leimnud.module.rpc.xmlhttp({
        url : "holidayNew.php"
    });
    r.callback = leimnud.closure({
        Function : function(rpc) {
            myPanel.addContent(rpc.xmlhttp.responseText);
        },
        args : r
    })
    r.make();

}

function deleteHoliday(uid) {
    url = "setupAjax.php?action=deleteHoliday&uid=" + uid;
    var r = new leimnud.module.rpc.xmlhttp({
        url : url
    });
    r.callback = leimnud.closure({
        Function : function(rpc) {
            // myPanel.addContent(rpc.xmlhttp.responseText);
            myPanel = setupPanel.panels.control;
            myPanel.tabLastSelected = false;
            myPanel.tabSelected = 0;
            myPanel.makeTab();

        },
        args : r
    })
    r.make();

    // myPanel.clearContent();
    // myPanel.addContent ( uid );

}

