Ext.form.CheckboxCustom = Ext.extend(Ext.form.Checkbox, {
    constructor: function (config) {
        config = Ext.apply({
            inputValue: 1,
            uncheckedValue: 0
        }, config);
        Ext.form.CheckboxCustom.superclass.constructor.call(this, config);
    },
    getValue: function () {
        if (this.rendered) {
            if (this.el.dom.checked === true) {
                return this.inputValue;
            }
            if (this.el.dom.checked === false) {
                return this.uncheckedValue;
            }
            return this.el.dom.checked;
        }
        if (this.checked === true) {
            return this.inputValue;
        }
        if (this.checked === false) {
            return this.uncheckedValue;
        }
        return this.checked;
    },
    setValue: function (v) {
        if (v === this.inputValue) {
            v = true;
        }
        if (v === this.uncheckedValue) {
            v = false;
        }
        var checked = this.checked,
                inputVal = this.inputValue;

        if (v === false) {
            this.checked = false;
        } else {
            this.checked = (v === true || v === 'true' || v == '1' || (inputVal ? v == inputVal : String(v).toLowerCase() == 'on'));
        }

        if (this.rendered) {
            this.el.dom.checked = this.checked;
            this.el.dom.defaultChecked = this.checked;
        }
        if (checked != this.checked) {
            this.fireEvent('check', this, this.checked);
            if (this.handler) {
                this.handler.call(this.scope || this, this, this.checked);
            }
        }
        return this;
    }
});
Ext.reg('checkboxCustom', Ext.form.CheckboxCustom);

var grdNumRows = 0;
var grdRowLabel = [];
var fieldGridGral = '';
var fieldGridGralVal = '';

Ext.ns("Ext.ux.renderer", "Ext.ux.grid");

Ext.ux.grid.ComboColumn = Ext.extend(Ext.grid.Column, {
    //@cfg {String} gridId
    //The id of the grid this column is in. This is required to be able to refresh the view once the combo store has loaded

    gridId: undefined,

    constructor: function (cfg) {
        Ext.ux.grid.ComboColumn.superclass.constructor.call(this, cfg);

        //Detect if there is an editor and if it at least extends a combobox, otherwise just treat it as a normal column and render the value itself
        this.renderer = (this.editor && this.editor.triggerAction) ? Ext.ux.renderer.ComboBoxRenderer(this.editor, this.gridId) : function (value) {
            return value;
        };
    }
});

Ext.grid.Column.types["combocolumn"] = Ext.ux.grid.ComboColumn;

//A renderer that makes a editorgrid panel render the correct value
Ext.ux.renderer.ComboBoxRenderer = function (combo, gridId) {
    //Get the displayfield from the store or return the value itself if the record cannot be found
    var comboBoxField = combo.getId().substring(3);
    var str = "";
    var getValueComboBox = function (value) {
        var idx = combo.store.find(combo.valueField, value);
        var rec = combo.store.getAt(idx);
        if (rec) {
            if (grdNumRows > 1 || grdNumRows == 0) {
                return rec.get(combo.displayField);
            } else {
                str = rec.get(combo.displayField);
                grdRowLabel[comboBoxField] = str;
                return str;
            }
        }

        if (grdNumRows > 1 || grdNumRows == 0) {
            return value;
        } else {
            if (value) {
                grdRowLabel[comboBoxField] = value;
            } else {
                value = grdRowLabel[comboBoxField];
            }

            return value;
        }
    }

    return function (value) {
        if (combo.store.getCount() == 0 && gridId) {
            combo.store.on(
                    "load",
                    function () {
                        var grid = Ext.getCmp(gridId);
                        if (grid) {
                            grid.getView().refresh();
                        }
                    },
                    {
                        single: true
                    }
            );

            if (grdNumRows > 1 || grdNumRows == 0) {
                return value;
            } else {
                if (typeof (grdRowLabel[comboBoxField]) == "undefined") {
                    grdRowLabel[comboBoxField] = value;
                    return grdRowLabel[comboBoxField];
                } else {
                    return grdRowLabel[comboBoxField];
                }
            }
        }

        str = getValueComboBox(value);
        if (grdNumRows > 1 || grdNumRows == 0) {
            return str;
        } else {
            return grdRowLabel[comboBoxField];
        }
    };
};

Ext.QuickTips.init();

Ext.namespace("Ext.ux");

var browserWidth = 0;
var browserHeight = 0;

//The more standards compliant browsers (mozilla/netscape/opera/IE7) use window.innerWidth and window.innerHeight
if (typeof window.innerWidth != "undefined") {
    browserWidth = window.innerWidth;
    browserHeight = window.innerHeight;
} else {
    //IE6 in standards compliant mode (i.e. with a valid doctype as the first line in the document)
    if (typeof document.documentElement != "undefined" && typeof document.documentElement.clientWidth != "undefined" &&
            document.documentElement.clientWidth != 0) {
        browserWidth = document.documentElement.clientWidth;
        browserHeight = document.documentElement.clientHeight;
    } else {
        if (typeof document.documentElement != "undefined" && typeof document.documentElement.offsetHeight != "undefined") {
            //windows
            browserWidth = document.documentElement.offsetWidth;
            browserHeight = document.documentElement.offsetHeight;
        } else {
            //Older versions of IE
            browserWidth = document.getElementsByTagName("body")[0].clientWidth;
            browserHeight = document.getElementsByTagName("body")[0].clientHeight;
        }
    }
}

new Ext.KeyMap(document, {
    key: Ext.EventObject.F5,
    fn: function (keycode, e) {
        if (!e.ctrlKey) {
            if (Ext.isIE) {
                // IE6 doesn't allow cancellation of the F5 key, so trick it into
                // thinking some other key was pressed (backspace in this case)
                e.browserEvent.keyCode = 8;
            }
            e.stopEvent();
            storeConsolidated.reload();
        } else {
            Ext.Msg.alert(_("ID_REFRESH_LABEL"), _("ID_REFRESH_MESSAGE"));
        }
    }
});

var gridId = "editorGridPanelMain";
var storeAux;

//Global variables
var storeConsolidated;
var toolbarconsolidated;
var consolidatedGrid;
var grid;
var textJump;
var readerCasesList;
var writerCasesList;
var proxyCasesList;
var htmlMessage;
var smodel;
var newCaseNewTab;

function openCase() {
    var rowModel = consolidatedGrid.getSelectionModel().getSelected();
    if (rowModel) {
        var appUid = rowModel.data.APP_UID;
        var delIndex = rowModel.data.DEL_INDEX;
        var caseTitle = (rowModel.data.APP_TITLE) ? rowModel.data.APP_TITLE : rowModel.data.APP_UID;
        if (!isIE) {
            Ext.Msg.show({
                msg: _("ID_OPEN_CASE") + " " + caseTitle,
                width: 300,
                wait: true,
                waitConfig: {
                    interval: 200
                }
            });
        }
        params = '';
        switch (action) {
            case 'consolidated':
            default:
                params += 'APP_UID=' + appUid;
                params += '&DEL_INDEX=' + delIndex;
                requestFile = '../../' + varSkin + '/cases/open';
                break;
        }
        params += '&action=' + 'todo';

        if (isIE) {
            if (newCaseNewTab) {
                newCaseNewTab.close();
            }

            newCaseNewTab = window.open(requestFile + '?' + params);
            newCaseNewTab.name = PM.Sessions.getCookie('PM-TabPrimary');
        } else {
            redirect(requestFile + '?' + params);
        }
    } else {
        msgBox(_("ID_INFORMATION"), _("ID_SELECT_ONE_AT_LEAST"));
    }
}

function jumpToCase(appNumber) {
    if (!isIE) {
        Ext.MessageBox.show({msg: _('ID_PROCESSING'), wait: true, waitConfig: {interval: 200}});
    }
    Ext.Ajax.request({
        url: 'cases_Ajax',
        success: function (response) {
            var res = Ext.decode(response.responseText),
                    nameTab;
            if (res.exists === true) {
                params = 'APP_NUMBER=' + appNumber;
                params += '&action=jump';
                requestFile = '../cases/open';
                if (isIE) {
                    if (newCaseNewTab) {
                        newCaseNewTab.close();
                    }
                    nameTab = PM.Sessions.getCookie('PM-TabPrimary') + '_openCase';
                    newCaseNewTab = window.open(requestFile + '?' + params, nameTab);
                } else {
                    redirect(requestFile + '?' + params);
                }
            } else {
                Ext.MessageBox.hide();
                var message = new Array();
                message['CASE_NUMBER'] = appNumber;
                msgBox(_('ID_INPUT_ERROR'), _('ID_CASE_DOES_NOT_EXIST_JS', appNumber), 'error');
            }
        },
        params: {action: 'previusJump', appNumber: appNumber}
    });
}

function pauseCase(date) {
    rowModel = consolidatedGrid.getSelectionModel().getSelected();
    unpauseDate = date.format('Y-m-d');

    Ext.Msg.confirm(
            _("ID_CONFIRM"),
            _("ID_PAUSE_CASE_TO_DATE") + " " + date.format("M j, Y"),
            function (btn, text) {
                if (btn == 'yes') {
                    Ext.MessageBox.show({
                        msg: _("ID_PROCESSING"),
                        wait: true,
                        waitConfig: {
                            interval: 200
                        }
                    });
                    Ext.Ajax.request({
                        url: '../cases/cases_Ajax',
                        success: function (response) {
                            parent.updateCasesView();
                            parent.updateCasesTree();
                            Ext.MessageBox.hide();
                        },
                        params: {
                            action: 'pauseCase',
                            unpausedate: unpauseDate,
                            APP_UID: rowModel.data.APP_UID,
                            DEL_INDEX: rowModel.data.DEL_INDEX
                        }
                    });
                }
            }
    );
}

function redirect(href) {
    window.location.href = href;
}

function strReplace(strs, strr, str) {
    var expresion = eval("/" + strs + "/gi");
    return (str.replace(expresion, strr));
}

function toolTipTab(str, show) {
    document.getElementById("toolTipTab").innerHTML = str;
    document.getElementById("toolTipTab").style.left = "3px";
    document.getElementById("toolTipTab").style.top = "27px";
    document.getElementById("toolTipTab").style.display = (show == 1) ? "inline" : "none";
}

var pnlMain;

Ext.apply(Ext.form.VTypes, {
    "int": function (value, field) {
        return /^\d*$/.test(value);
    },
    intText: "This field should only contain numbers",
    intMask: /[\d]/,

    real: function (value, field) {
        return /^\d*\.?\d*$/.test(value);
    },
    realText: "This field should only contain numbers and the point",
    realMask: /[\d\.]/
});

Ext.onReady(function () {
    pnlMain = new Ext.Panel({
        title: '',
        renderTo: 'cases-grid',
        layout: 'fit',
        layoutConfig: {
            align: 'stretch'
        }
    });

    parent._action = action;

    optionMenuOpen = new Ext.Action({
        text: _("ID_OPEN_CASE"),
        iconCls: 'ICON_CASES_OPEN',
        handler: openCase
    });

    optionMenuPause = new Ext.Action({
        text: _("ID_PAUSE_CASE"),
        iconCls: 'ICON_CASES_PAUSED',
        menu: new Ext.menu.DateMenu({
            handler: function (dp, date) {
                pauseCase(date);
            }
        })

    });

    var buttonProcess = new Ext.Action({
        text: _("ID_DERIVATED"),
        handler: function () {
            Ext.Msg.confirm(_("ID_CONFIRM_ROUTING"), _("ID_ROUTE_BATCH_ROUTING"),
                    function (btn, text) {
                        if (btn == 'yes') {
                            htmlMessage = "";
                            var selectedRow = Ext.getCmp(gridId).getSelectionModel().getSelections();
                            var maxLenght = selectedRow.length;
                            for (var i in selectedRow) {
                                rowGrid = selectedRow[i].data;
                                for (fieldGrid in rowGrid) {
                                    if (fieldGrid != 'APP_UID' && fieldGrid != 'APP_NUMBER' && fieldGrid != 'APP_TITLE' && fieldGrid != 'DEL_INDEX') {
                                        fieldGridGral = fieldGrid;
                                        fieldGridGralVal = (rowGrid[fieldGrid] != null) ? rowGrid[fieldGrid] : '';
                                    }
                                }
                                if (selectedRow[i].data) {
                                    ajaxDerivationRequest(selectedRow[i].data["APP_UID"], selectedRow[i].data["DEL_INDEX"], maxLenght, selectedRow[i].data["APP_NUMBER"], fieldGridGral, fieldGridGralVal);
                                }
                            }
                        }
                    }
            );
        }
    });
    switch (action) {
        case 'consolidated':
            menuItems = [buttonProcess, optionMenuOpen];
            break;
        default:
            menuItems = [];
            break;
    }

    var tabs = new Ext.TabPanel({
        autoWidth: true,
        enableTabScroll: true,
        activeTab: 0,
        style: {
            height: "1.65em"
        },
        defaults: {
            autoScroll: true
        },
        items: eval(Items),
        plugins: new Ext.ux.TabCloseMenu()
    });

    smodel = new Ext.grid.CheckboxSelectionModel({
        checkOnly: true,
        listeners: {
            selectionchange: function (sm) {
                var count_rows = sm.getCount();
                switch (count_rows) {
                    case 0:
                        break;
                    default:
                        break;
                }
            }
        }
    });

    var textSearch = new Ext.form.TextField({
        allowBlank: true,
        ctCls: 'pm_search_text_field',
        width: 150,
        emptyText: _("ID_EMPTY_SEARCH"),
        listeners: {
            specialkey: function (f, e) {
                if (e.getKey() == e.ENTER) {
                    doSearch();
                }
            }
        }
    });

    var btnSearch = new Ext.Button({
        text: _("ID_SEARCH"),
        handler: doSearch
    });

    function doSearch() {
        searchText = textSearch.getValue();
        storeConsolidated.setBaseParam('search', searchText);
        storeConsolidated.load({
            params: {
                start: 0,
                limit: 20
            }
        });
    }

    var resetSearchButton = {
        text: 'X',
        ctCls: 'pm_search_x_button',
        handler: function () {
            textSearch.setValue('');
            doSearch();
        }
    };

    textJump = {
        xtype: 'numberfield',
        id: 'textJump',
        allowBlank: true,
        width: 50,
        emptyText: _("ID_CASESLIST_APP_UID"),
        listeners: {
            specialkey: function (f, e) {
                if (e.getKey() == e.ENTER) {
                    // defining an id and using the Ext.getCmp method improves the accesibility of Ext components
                    caseNumber = parseFloat(Ext.util.Format.trim(Ext.getCmp('textJump').getValue()));
                    if (caseNumber) {
                        jumpToCase(caseNumber);
                    } else {
                        msgBox('Input Error', 'You have set a invalid Application Number', 'error');
                    }
                }
            }
        }
    };

    var btnJump = new Ext.Button({
        text: _("ID_OPT_JUMP"),
        handler: function () {
            var caseNumber = parseFloat(Ext.util.Format.trim(Ext.getCmp('textJump').getValue()));
            if (caseNumber) {
                jumpToCase(caseNumber);
            } else {
                msgBox('Input Error', 'You have set a invalid Application Number', 'error');
            }
        }
    });

    function enableDisableMenuOption() {
        var rl = Ext.getCmp(gridId).store.getModifiedRecords();
        var rows = consolidatedGrid.getSelectionModel().getSelections();
        optionMenuOpen.setDisabled(true);
        optionMenuPause.setDisabled(true);
        buttonProcess.setDisabled(true);
        if (action === "consolidated" && rows.length === 1) {
            optionMenuOpen.setDisabled(false);
            optionMenuPause.setDisabled(false);
            buttonProcess.setDisabled(false);
        }
        if (action === "consolidated" && rows.length > 1) {
            optionMenuOpen.setDisabled(true);
            optionMenuPause.setDisabled(true);
            buttonProcess.setDisabled(false);
        }
    }

    toolbarconsolidated = [
        {
            xtype: "button",
            text: _("ID_ACTIONS"),
            menu: menuItems,
            listeners: {
                menushow: enableDisableMenuOption
            }
        },
        "->",
        {
            xtype: "checkbox",
            id: "chk_allColumn",
            name: "chk_allColumn",
            boxLabel: "Apply changes to all rows"
        },
        "",
        "-",
        textSearch,
        resetSearchButton,
        btnSearch,
        "-",
        textJump,
        "",
        btnJump
    ];

    var viewport = new Ext.Viewport({
        layout: "fit",
        autoScroll: true,
        items: [tabs]
    });

    //routine to hide the debug panel if it is open
    if (parent.PANEL_EAST_OPEN) {
        parent.PANEL_EAST_OPEN = false;
        var debugPanel = parent.Ext.getCmp('debugPanel');
        debugPanel.hide();
        debugPanel.ownerCt.doLayout();
    }

    _nodeId = '';
    switch (action) {
        case 'consolidated':
            _nodeId = "ID_CASES_CONSOLIDATED";
            break;
    }

    if (_nodeId != '') {
        treePanel1 = parent.Ext.getCmp('tree-panel');
        if (treePanel1) {
            node = treePanel1.getNodeById(_nodeId);
        }
        if (node) {
            node.select();
        }
    }

    parent.updateCasesTree();

    function inArray(arr, obj) {
        for (var i = 0; i < arr.length; i++) {
            if (arr[i] == obj)
                return true;
        }
        return false;
    }

    // Add the additional 'advanced' VTypes -- [Begin]
    Ext.apply(Ext.form.VTypes, {
        daterange: function (val, field) {
            var date = field.parseDate(val);

            if (!date) {
                return;
            }
            if (field.startDateField && (!this.dateRangeMax || (date.getTime() != this.dateRangeMax.getTime()))) {
                var start = Ext.getCmp(field.startDateField);
                start.setMaxValue(date);
                start.validate();
                this.dateRangeMax = date;
            } else if (field.endDateField && (!this.dateRangeMin || (date.getTime() != this.dateRangeMin.getTime()))) {
                var end = Ext.getCmp(field.endDateField);
                end.setMinValue(date);
                end.validate();
                this.dateRangeMin = date;
            }

            //Always return true since we're only using this vtype to set the
            //min/max allowed values (these are tested for after the vtype test)
            return true;
        }
    });
});

function msgBox(title, msg, type) {
    if (typeof ('type') == 'undefined') {
        type = 'info';
    }

    switch (type) {
        case 'error':
            icon = Ext.MessageBox.ERROR;
            break;
        case 'info':
        default:
            icon = Ext.MessageBox.INFO;
            break;
    }

    Ext.Msg.show({
        title: title,
        msg: msg,
        fn: function () {
        },
        animEl: 'elId',
        icon: icon,
        buttons: Ext.MessageBox.OK
    });
}

function renderTitle(val, p, r) {
    return ("<a href=\"javascript:;\" onclick=\"openCase(); return (false);\">" + val + "</a>");
}

function renderSummary(val, p, r) {
    var summaryIcon = '<img src="/images/ext/default/s.gif" class="x-tree-node-icon ss_layout_header" unselectable="off" id="extdd-17" ';
    summaryIcon += 'onclick="openSummaryWindow(' + "'" + r.data['APP_UID'] + "'" + ', ' + r.data['DEL_INDEX'] + ')" title="' + _('ID_SUMMARY') + '" />';
    return summaryIcon;
}
;

function generateGridClassic(proUid, tasUid, dynUid) {

    var pager = 20;
    var pagei = 0;
    Ext.Ajax.request({
        url: '../pmConsolidatedCL/proxyGenerateGrid',
        success: function (response) {
            var dataResponse = Ext.util.JSON.decode(response.responseText);
            var viewConfigObject;
            var textArea = dataResponse.hasTextArea;

            if (textArea == false) {
                viewConfigObject = {
                };
            } else {
                viewConfigObject = {
                    enableRowBody: true,
                    showPreview: true,
                    getRowClass: function (record, rowIndex, p, store) {
                        if (this.showPreview) {
                            p.body = '<p><br /></p>';
                            return 'x-grid3-row-expanded';
                        }
                        return 'x-grid3-row-collapsed';
                    }
                };
            }

            storeConsolidated = new Ext.data.Store({
                id: "storeConsolidatedGrid",
                remoteSort: true,
                proxy: new Ext.data.HttpProxy({
                    url: "../pmConsolidatedCL/proxyConsolidated",
                    api: {
                        read: "../pmConsolidatedCL/proxyConsolidated",
                        update: "../pmConsolidatedCL/consolidatedUpdateAjax"
                    }
                }),

                reader: new Ext.data.JsonReader({
                    fields: dataResponse.readerFields,
                    totalProperty: "totalCount",
                    idProperty: "APP_UID",
                    root: "data",
                    messageProperty: "message"
                }),

                writer: new Ext.data.JsonWriter({
                    encode: true,
                    writeAllFields: false
                }),
                autoSave: true,
                listeners: {
                    beforeload: function (store, options) {
                        grdNumRows = 0;
                    },

                    load: function (store, records, options) {
                        grdNumRows = store.getCount();

                        consolidatedGrid.setDisabled(false);
                    }
                }
            });

            var xColumns = dataResponse.columnModel;
            xColumns.unshift(smodel);

            var cm = new Ext.grid.ColumnModel(xColumns);
            cm.config[2].renderer = renderTitle;
            cm.config[3].renderer = renderTitle;
            cm.config[4].renderer = renderSummary;

            storeConsolidated.setBaseParam("limit", pager);
            storeConsolidated.setBaseParam("start", pagei);
            storeConsolidated.setBaseParam('tasUid', tasUid);
            storeConsolidated.setBaseParam('dynUid', dynUid);
            storeConsolidated.setBaseParam('proUid', proUid);
            storeConsolidated.setBaseParam('dropList', Ext.util.JSON.encode(dataResponse.dropList));
            storeConsolidated.load();

            consolidatedGrid = new Ext.grid.EditorGridPanel({
                id: gridId,
                region: "center",

                store: storeConsolidated,
                cm: cm,
                sm: smodel,
                width: pnlMain.getSize().width,
                height: browserHeight - 35,

                layout: 'fit',
                viewConfig: viewConfigObject,

                listeners: {
                    beforeedit: function (e) {
                        var selRow = Ext.getCmp(gridId).getSelectionModel().getSelected();

                        var swDropdown = 0;
                        for (var i = 0; i <= dataResponse.dropList.length - 1 && swDropdown == 0; i++) {
                            if (dataResponse.dropList[i] == e.field) {
                                swDropdown = 1;
                            }
                        }

                        var swYesNo = 0;
                        for (var i = 0; i <= dataResponse.comboBoxYesNoList.length - 1 && swYesNo == 0; i++) {
                            if (dataResponse.comboBoxYesNoList[i] == e.field) {
                                swYesNo = 1;
                            }
                        }

                        if (swDropdown == 1 && swYesNo == 0) {
                            storeAux = Ext.StoreMgr.get("store" + e.field + "_" + proUid);
                            storeAux.setBaseParam("appUid", selRow.data["APP_UID"]);
                            storeAux.setBaseParam("dynUid", dynUid);
                            storeAux.setBaseParam("proUid", proUid);
                            storeAux.setBaseParam("fieldName", e.field);
                            storeAux.load();
                        }
                    },

                    afteredit: function (e) {
                        if (Ext.getCmp("chk_allColumn").checked) {
                            Ext.Msg.show({
                                title: "",
                                msg: "The modification will be applied to all rows in your selection.",
                                buttons: Ext.Msg.YESNO,
                                fn: function (btn) {
                                    if (btn == "yes") {
                                        consolidatedGrid.setDisabled(true);
                                        var dataUpdate = "";
                                        var strValue = "";
                                        var sw = 0;

                                        if (e.value instanceof Date) {
                                            var mAux = e.value.getMonth() + 1;
                                            var dAux = e.value.getDate();
                                            var hAux = e.value.getHours();
                                            var iAux = e.value.getMinutes();
                                            var sAux = e.value.getSeconds();

                                            strValue = e.value.getFullYear() + "-" + ((mAux <= 9) ? "0" : "") + mAux + "-" + ((dAux <= 9) ? "0" : "") + dAux;
                                            strValue = strValue + " " + ((hAux <= 9) ? "0" + ((hAux == 0) ? "0" : hAux) : hAux) + ":" + ((iAux <= 9) ? "0" + ((iAux == 0) ? "0" : iAux) : iAux) + ":" + ((sAux <= 9) ? "0" + ((sAux == 0) ? "0" : sAux) : sAux);
                                        } else {
                                            strValue = strReplace("\"", "\\\"", e.value + "");
                                        }

                                        storeConsolidated.each(function (record) {
                                            dataUpdate = dataUpdate + ((sw == 1) ? "(sep1 /)" : "") + record.data["APP_UID"] + "(sep2 /)" + e.field + "(sep2 /)" + strValue;
                                            sw = 1;
                                        });

                                        Ext.Ajax.request({
                                            url: "consolidatedUpdateAjax",
                                            method: "POST",
                                            params: {
                                                "option": "ALL",
                                                "dynaformUid": dynUid,
                                                "dataUpdate": dataUpdate
                                            },

                                            success: function (response, opts) {
                                                var dataResponse = eval("(" + response.responseText + ")");

                                                if (dataResponse.status && dataResponse.status == "OK") {
                                                    if (typeof (storeConsolidated.lastOptions.params) != "undefined") {
                                                        pagei = storeConsolidated.lastOptions.params.start;
                                                    }

                                                    storeConsolidated.setBaseParam("start", pagei);
                                                    storeConsolidated.load();
                                                } else {
                                                }
                                            }
                                        });
                                    }
                                },
                                icon: Ext.MessageBox.QUESTION
                            });
                        }
                    },

                    mouseover: function (e, cell) {
                        var rowIndex = consolidatedGrid.getView().findRowIndex(cell);
                        if (!(rowIndex === false)) {
                            var record = consolidatedGrid.store.getAt(rowIndex);
                            var msg = record.get('APP_TITLE');
                            Ext.QuickTips.register({
                                text: msg,
                                target: e.target
                            });
                        } else {
                            Ext.QuickTips.unregister(e.target);
                        }
                    },

                    mouseout: function (e, cell) {
                        Ext.QuickTips.unregister(e.target);
                    }
                },

                tbar: new Ext.Toolbar({
                    height: 33,
                    items: toolbarconsolidated
                }),

                bbar: new Ext.PagingToolbar({
                    pageSize: pager,
                    store: storeConsolidated,
                    displayInfo: true,
                    displayMsg: _("ID_DISPLAY_ITEMS"),
                    emptyMsg: _("ID_DISPLAY_EMPTY")
                })
            });

            pnlMain.removeAll();
            pnlMain.add(consolidatedGrid);
            pnlMain.doLayout();
        },

        failure: function () {
            alert("Failure...");
        },

        params: {
            xaction: 'read',
            tasUid: tasUid,
            dynUid: dynUid,
            proUid: proUid
        }
    });
}

function generateGrid(proUid, tasUid, dynUid) {
    var pager = 20;
    var pagei = 0;

    Ext.Ajax.request({
        url: urlProxy + 'generate/' + proUid + '/' + tasUid + '/' + dynUid,
        headers: {
            'Content-Type': 'application/json',
            'Authorization': 'Bearer ' + credentials.access_token
        },
        success: function (response) {
            var dataResponse = Ext.util.JSON.decode(response.responseText);
            var viewConfigObject;
            var textArea = dataResponse.hasTextArea;

            if (textArea == false) {
                viewConfigObject = {
                };
            } else {
                viewConfigObject = {
                    enableRowBody: true,
                    showPreview: true,
                    getRowClass: function (record, rowIndex, p, store) {
                        if (this.showPreview) {
                            p.body = '<p><br /></p>';
                            return 'x-grid3-row-expanded';
                        }
                        return 'x-grid3-row-collapsed';
                    }
                };
            }
            storeConsolidated = new Ext.data.Store({
                id: "storeConsolidatedGrid",
                remoteSort: true,
                proxy: new Ext.data.HttpProxy({
                    method: 'GET',
                    url: urlProxy + 'cases/' + proUid + '/' + tasUid + '/' + dynUid,
                    api: {
                        read: {
                            method: 'GET',
                            url: urlProxy + 'cases/' + proUid + '/' + tasUid + '/' + dynUid
                        },
                        update: {
                            method: 'PUT',
                            url: urlProxy + 'cases/' + proUid + '/' + tasUid + '/' + dynUid
                        }
                    },
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': 'Bearer ' + credentials.access_token
                    }
                }),
                reader: new Ext.data.JsonReader({
                    fields: dataResponse.readerFields,
                    totalProperty: "totalCount",
                    idProperty: "APP_UID",
                    root: "data",
                    messageProperty: "message"
                }),

                writer: new Ext.data.JsonWriter({
                    encode: false,
                    writeAllFields: false
                }),
                autoSave: true,

                listeners: {
                    beforeload: function (store, options) {
                        grdNumRows = 0;
                    },

                    load: function (store, records, options) {
                        grdNumRows = store.getCount();

                        consolidatedGrid.setDisabled(false);
                    }
                }
            });

            var xColumns = dataResponse.columnModel;
            xColumns.unshift(smodel);

            var cm = new Ext.grid.ColumnModel(xColumns);
            cm.config[2].renderer = renderTitle;
            cm.config[3].renderer = renderTitle;
            cm.config[4].renderer = renderSummary;

            storeConsolidated.load();

            consolidatedGrid = new Ext.grid.EditorGridPanel({
                id: gridId,
                region: "center",

                store: storeConsolidated,
                cm: cm,
                sm: smodel,
                width: pnlMain.getSize().width,
                height: browserHeight - 35,

                layout: 'fit',
                viewConfig: viewConfigObject,

                listeners: {
                    beforeedit: function (e) {
                        var selRow = Ext.getCmp(gridId).getSelectionModel().getSelected();

                        var swDropdown = 0;
                        for (var i = 0; i <= dataResponse.dropList.length - 1 && swDropdown == 0; i++) {
                            if (dataResponse.dropList[i] == e.field) {
                                swDropdown = 1;
                            }
                        }

                        var swYesNo = 0;
                        for (var i = 0; i <= dataResponse.comboBoxYesNoList.length - 1 && swYesNo == 0; i++) {
                            if (dataResponse.comboBoxYesNoList[i] == e.field) {
                                swYesNo = 1;
                            }
                        }
                    },

                    afteredit: function (e) {

                        if (Ext.getCmp("chk_allColumn").checked) {
                            Ext.Msg.show({
                                title: "",
                                msg: "The modification will be applied to all rows in your selection.",
                                buttons: Ext.Msg.YESNO,
                                fn: function (btn) {
                                    if (btn == "yes") {

                                        consolidatedGrid.setDisabled(true);

                                        var dataUpdate = "";
                                        var strValue = "";
                                        var sw = 0;

                                        if (e.value instanceof Date) {
                                            var mAux = e.value.getMonth() + 1;
                                            var dAux = e.value.getDate();
                                            var hAux = e.value.getHours();
                                            var iAux = e.value.getMinutes();
                                            var sAux = e.value.getSeconds();

                                            strValue = e.value.getFullYear() + "-" + ((mAux <= 9) ? "0" : "") + mAux + "-" + ((dAux <= 9) ? "0" : "") + dAux;
                                            strValue = strValue + " " + ((hAux <= 9) ? "0" + ((hAux == 0) ? "0" : hAux) : hAux) + ":" + ((iAux <= 9) ? "0" + ((iAux == 0) ? "0" : iAux) : iAux) + ":" + ((sAux <= 9) ? "0" + ((sAux == 0) ? "0" : sAux) : sAux);
                                        } else {
                                            strValue = strReplace("\"", "\\\"", e.value + "");
                                        }

                                        storeConsolidated.each(function (record) {
                                            dataUpdate = dataUpdate + ((sw == 1) ? "(sep1 /)" : "") + record.data["APP_UID"] + "(sep2 /)" + e.field + "(sep2 /)" + strValue;
                                            sw = 1;
                                        });

                                        Ext.Ajax.request({
                                            url: "consolidatedUpdateAjax",
                                            method: 'PUT',
                                            url: urlProxy + 'cases/' + proUid + '/' + tasUid + '/' + dynUid,
                                            headers: {
                                                'Content-Type': 'application/json',
                                                'Authorization': 'Bearer ' + credentials.access_token
                                            },
                                            jsonData: {
                                                "option": "ALL",
                                                "dynaformUid": dynUid,
                                                "dataUpdate": dataUpdate
                                            },
                                            success: function (response, opts) {
                                                var dataResponse = eval("(" + response.responseText + ")");

                                                if (dataResponse.status && dataResponse.status == "OK") {
                                                    if (typeof (storeConsolidated.lastOptions.params) != "undefined") {
                                                        pagei = storeConsolidated.lastOptions.params.start;
                                                    }

                                                    storeConsolidated.setBaseParam("start", pagei);
                                                    storeConsolidated.load();
                                                } else {
                                                }
                                            }
                                        });
                                    }
                                },
                                icon: Ext.MessageBox.QUESTION
                            });
                        }
                    },

                    mouseover: function (e, cell) {
                        var rowIndex = consolidatedGrid.getView().findRowIndex(cell);
                        if (!(rowIndex === false)) {
                            var record = consolidatedGrid.store.getAt(rowIndex);
                            var msg = record.get('APP_TITLE');
                            Ext.QuickTips.register({
                                text: msg,
                                target: e.target
                            });
                        } else {
                            Ext.QuickTips.unregister(e.target);
                        }
                    },

                    mouseout: function (e, cell) {
                        Ext.QuickTips.unregister(e.target);
                    }
                },

                tbar: new Ext.Toolbar({
                    height: 33,
                    items: toolbarconsolidated
                }),

                bbar: new Ext.PagingToolbar({
                    pageSize: pager,
                    store: storeConsolidated,
                    displayInfo: true,
                    displayMsg: _("ID_DISPLAY_ITEMS"),
                    emptyMsg: _("ID_DISPLAY_EMPTY")
                })
            });

            pnlMain.removeAll();
            pnlMain.add(consolidatedGrid);
            pnlMain.doLayout();
        },

        failure: function () {
            alert("Failure...");
        }
    });
}

function ajaxDerivationRequest(appUid, delIndex, maxLenght, appNumber, fieldGridGral, fieldGridGralVal) {
    if (String(fieldGridGralVal).indexOf("/") != -1) {
        fieldGridGralVal = stringReplace("\\x2F", "__FRASL__", fieldGridGralVal);
    }
    var uri = urlProxy + 'derivate/' + appUid + '/' + appNumber + '/' + delIndex + '/' + fieldGridGral + '/';
    var urlrequest = (fieldGridGralVal == '') ? uri : uri + fieldGridGralVal + '/';
    Ext.Ajax.request({
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Authorization': 'Bearer ' + credentials.access_token
        },
        url: urlrequest,
        success: function (response) {
            var dataResponse;
            var fullResponseText = response.responseText;

            if (fullResponseText.charAt(0) != "<") {
                dataResponse = Ext.util.JSON.decode(response.responseText);
            } else {
                dataResponse = Ext.util.JSON.decode("{message:\"Case Derivated\"}");
                storeConsolidated.reload();
            }

            htmlMessage = htmlMessage + dataResponse.message + "<br />";
            var tmpIndex = htmlMessage.split("<br />");
            index = tmpIndex.length - 1;

            if (index == maxLenght) {
                Ext.MessageBox.show({
                    title: _("ID_DERIVATION_RESULT"),
                    msg: htmlMessage,

                    fn: function (btn, text, opt) {
                        if (maxLenght == storeConsolidated.getCount()) {
                            window.location.reload();
                        }

                        if (fullResponseText.charAt(0) != "<" && parent.document.getElementById("batchRoutingCasesNumRec") != null) {
                            parent.document.getElementById("batchRoutingCasesNumRec").innerHTML = parseInt(dataResponse.casesNumRec);
                        }

                        storeConsolidated.reload();
                    }
                });
            }
        },

        failure: function () {
            index = tmpIndex.length - 1;
            htmlMessage = htmlMessage + "failed: " + appUid;

            if (index == maxLenght) {
                Ext.Msg.show({
                    title: "Derivation Result",
                    msg: htmlMessage
                });
                storeConsolidated.reload();
            }
        }
    });
}

function linkRenderer(value) {
    return "<a href=\"" + value + "\" onclick=\"window.open('" + value + "', '_blank'); return false;\">" + value + "</a>";
}

Ext.EventManager.on(window, 'beforeunload', function () {
    if (newCaseNewTab) {
        newCaseNewTab.close();
    }
});
