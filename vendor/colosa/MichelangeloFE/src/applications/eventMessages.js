EventMessagesGrid = function () {
    this.onCreate = new Function();
    this.onEdit = new Function();
    this.onDel = new Function();
    Mafe.Grid.call(this);
    EventMessagesGrid.prototype.init.call(this);
};
EventMessagesGrid.prototype = new Mafe.Grid();
EventMessagesGrid.prototype.createHTML = function () {
    Mafe.Grid.prototype.createHTML.call(this);
    this.dom.toolbar.appendChild(this.buttonCreate.getHTML());
    return this.html;
};
EventMessagesGrid.prototype.init = function () {
    var that = this;
    that.buttonCreate = new PMUI.ui.Button({
        id: 'idButtonEventMessagesGrid',
        text: 'Create'.translate(),
        height: '36px',
        width: 100,
        style: {cssClasses: ['mafe-button-create']},
        handler: function (event) {
            that.onCreate(event);
        }
    });
    that.buttonCreate.defineEvents();
    that.setID('idEventMessagesGrid');
    that.setColumns([{
        id: '',
        title: 'Nombre',
        sortable: true,
        width: '460px',
        dataType: 'string',
        alignmentCell: 'left',
        columnData: 'mes_title'
    }, {
        id: 'dynaformGridPanelEdit',
        title: '',
        dataType: 'button',
        buttonStyle: {cssClasses: ['mafe-button-edit']},
        buttonLabel: function (row, data) {
            return 'Edit'.translate();
        },
        onButtonClick: function (row, grid) {
            that.onEdit(row, grid);
        }
    }, {
        id: 'dynaformGridPanelDelete',
        title: '',
        dataType: 'button',
        buttonStyle: {cssClasses: ['mafe-button-delete']},
        buttonLabel: function (row, data) {
            return 'Delete'.translate();
        },
        onButtonClick: function (row, grid) {
            that.onDel(row, grid);
        }
    }
    ]);
    that.load();
};
EventMessagesGrid.prototype.show = function () {

};
EventMessagesGrid.prototype.create = function () {

};
EventMessagesGrid.prototype.load = function () {
    var that = this,
        dt = [
            {mes_title: 'message1-1'},
            {mes_title: 'message1-2'},
            {mes_title: 'message1-3'},
            {mes_title: 'message1-4'}
        ];
    that.setDataItems(dt);
};

EventMessagesForm = function () {
    Mafe.Form.call(this);
    EventMessagesForm.prototype.init.call(this);
};
EventMessagesForm.prototype = new Mafe.Form();
EventMessagesForm.prototype.init = function () {
    var that = this;
    that.setID('idEventMessagesForm');
    that.setItems({
        id: '',
        pmType: 'text',
        label: 'Name'.translate(),
        value: '',
        maxLength: 100,
        placeholder: '',
        name: '',
        required: true,
        controlsWidth: 300
    });
    that.buttons = [
        new PMUI.ui.Button({
            id: 'btnClose',
            text: 'Cancel'.translate(),
            buttonType: 'error',
            height: 31,
            handler: function () {
                that.onCancel();
            }
        }),
        new PMUI.ui.Button({
            id: 'windowDynaformPmtableSave',
            text: 'Save'.translate(),
            buttonType: 'success',
            height: 31,
            handler: function () {
                that.onSave();
            }
        })
    ];
};
EventMessagesForm.prototype.getButtons = function () {
    return this.buttons;
};

EventMessages = function () {
    Mafe.Window.call(this);

    this.list = new EventMessagesGrid();
    this.form = new EventMessagesForm();

    EventMessages.prototype.init.call(this);
};
EventMessages.prototype = new Mafe.Window();
EventMessages.prototype.init = function () {
    var that = this;
    that.list.onCreate = function () {
        that.resetView();
        that.form.setVisible(true);
    };
    that.setTitle("Event Messages");
    that.addItem(that.list);
};
EventMessages.prototype.showForm = function () {
    this.list.show();
};

PMDesigner.eventMessages = function () {
    var a = new EventMessages();
    a.open();
};
PMDesigner.eventMessages.create = function () {
};
