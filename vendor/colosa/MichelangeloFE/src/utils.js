/**
 * Return an incremental name based of the type of the shape
 * @param {Object} pmCanvas The current canvas
 */
var IncrementNameCanvas = function (pmCanvas) {
    var random,
        elementsName = {
            TASK: "Task".translate(),
            SUB_PROCESS: "Sub-process".translate(),
            START: "Start Event".translate(),
            START_MESSAGE: "Start Message Event".translate(),
            START_TIMER: "Start Timer Event".translate(),
            END: "End Event".translate(),
            END_MESSAGE: "End Message Event".translate(),
            SELECTION: "Selection".translate(),
            EVALUATION: "Evaluation".translate(),
            PARALLEL: "Parallel Gateway".translate(),
            INCLUSIVE: "Inclusive Gateway".translate(),
            EXCLUSIVE: "Exclusive Gateway".translate(),
            PARALLEL_EVALUATION: "Parallel by Evaluation".translate(),
            PARALLEL_JOIN: "Parallel Join".translate(),
            TEXT_ANNOTATION: "Annotation".translate(),
            VERTICAL_LINE: "Vertical line".translate(),
            HORIZONTAL_LINE: "Horizontal line".translate(),
            H_LABEL: "Horizontal Text".translate(),
            V_LABEL: "Vertical Text".translate(),
            DATASTORE: "Data Store".translate(),
            DATAOBJECT: "Data Object".translate(),
            PARTICIPANT: "Black Box Pool".translate(),
            POOL: "Pool".translate(),
            INTERMEDIATE_SENDMESSAGE: "Intermediate Send Message Event".translate(),
            INTERMEDIATE_RECEIVEMESSAGE: "Intermediate Receive Message Event".translate(),
            LANE: "Lane".translate(),
            GROUP: "Group".translate(),
            BOUNDARY_EVENT: ' ',
            END_EMAIL: "End Email Event".translate(),
            INTERMEDIATE_EMAIL: "Intermediate Email Event".translate()
        },
        random = false;
    return {
        id: Math.random(),
        get: function (type) {
            var i,
                j,
                k = pmCanvas.getCustomShapes().getSize(),
                exists,
                index = 1;
            for (i = 0; i < k; i += 1) {
                exists = false;
                for (j = 0; j < k; j += 1) {
                    if (pmCanvas.getCustomShapes().get(j).getName() === elementsName[type] + " " + (i + 1)) {
                        exists = true;
                        break;
                    }
                }
                if (!exists) {
                    break;
                }
            }
            return elementsName[type] + " " + (i + 1);
        }
    };
};
/**
 * It is required overwriting the method, since there custom functionality
 * that should not affect the core of PMUI.
 */
PMUI.ui.Window.prototype.open = function () {
    var the_window, that = this;
    if (this.isOpen) {
        return this;
    }
    the_window = this.getHTML();
    if (this.modal) {
        this.modalObject.appendChild(the_window);
        document.body.appendChild(this.modalObject);
        jQuery(the_window).draggable({
            handle: $(this.header),
            containment: '#' + this.modalObject.id,
            scroll: false});
        if (!$.stackModal) {
            $.stackModal = [];
        }
        $(the_window).find(":tabbable:eq(0)").focus(1);
        $(the_window).find(":tabbable:eq(1)").focus(1);
        $.stackModal.push(the_window);
        $(the_window).on("keydown", function (event) {
            if (event.keyCode !== $.ui.keyCode.TAB) {
                return;
            }
            var tabbables = $(':tabbable', this),
                first = tabbables.filter(':first'),
                last = tabbables.filter(':last');
            if (event.target === last[0] && !event.shiftKey) {
                first.focus(1);
                return false;
            } else if (event.target === first[0] && event.shiftKey) {
                last.focus(1);
                return false;
            }
            if (event.which === PMDesigner.keyCodeF5) {
                this.blur();
                event.preventDefault();
                window.location.reload(true);
            }
        });
    } else {
        document.body.appendChild(the_window);
        jQuery(this.getHTML()).draggable();
    }
    if (typeof this.onOpen === 'function') {
        this.onOpen(this);
    }
    this.isOpen = true;
    this.updateDimensionsAndPosition();
    this.setVisible(true);
    this.defineEvents();
    if (document.body && this.modal) {
        document.body.style.overflow = "hidden"
    }
    $(the_window).find("*").on("keydown", function (e) {
        if (e.which === PMDesigner.keyCodeF5) {
            this.blur();
            e.preventDefault();
            window.location.reload(true);
        }
    });
    return this;
};
/**
 * It is required overwriting the method, since there custom functionality
 * that should not affect the core of PMUI.
 */
PMUI.ui.Window.prototype.close = function () {
    jQuery(this.modalObject).detach();
    jQuery(this.html).detach();
    jQuery(this.closeButton).detach();
    if (typeof this.onClose === 'function') {
        this.onClose(this);
    }
    this.isOpen = false;
    if (document.body && this.modal) {
        document.body.style.overflow = "auto";
    }
    if ($.stackModal) {
        $.stackModal.pop();
        var the_window = $.stackModal[$.stackModal.length - 1];
    }
    return this;
};

var autoResizeScreen = function () {
    var myWidth = 0, myHeight = 0;
    if (typeof(window.innerWidth) === 'number') {
        myWidth = window.innerWidth;
        myHeight = window.innerHeight;
    } else if (document.documentElement &&
        (document.documentElement.clientWidth || document.documentElement.clientHeight)) {
        myWidth = document.documentElement.clientWidth;
        myHeight = document.documentElement.clientHeight;
    } else if (document.body &&
        (document.body.clientWidth || document.body.clientHeight)) {
        myWidth = document.body.clientWidth;
        myHeight = document.body.clientHeight;
    }
    return myWidth;
};

var showUID = function (id) {
    var messageWindow = new PMUI.ui.MessageWindow({
        width: 490,
        bodyHeight: 'auto',
        id: 'showMessageWindowUID',
        windowMessageType: 'info',
        message: id,
        footerItems: [
            {
                text: 'Ok'.translate(),
                handler: function () {
                    messageWindow.close();
                },
                buttonType: "success"
            }
        ]
    });
    messageWindow.setTitle("ID".translate());
    messageWindow.open();
    messageWindow.showFooter();
    $(messageWindow.dom.icon).removeClass();
};

var applyStyleWindowForm = function (win) {
    $(win.body).removeClass("pmui-background");
    win.footer.html.style.textAlign = 'right';
    (function searchForm(items) {
        var i;
        for (i = 0; i < items.length; i += 1) {
            if (items[i].footer && items[i].footer.setVisible) {
                $(win.body).addClass("pmui-background");
                items[i].footer.setVisible(false);
            }
            searchForm(items[i].getItems ? items[i].getItems() :
                (items[i].getPanel ? items[i].getPanel().getItems() : []));
        }
    }(win.getItems()));
};

var QuickMessageWindow = function (html, message) {
    if (html === undefined) {
        return;
    }
    QuickMessageWindow.prototype.show.call(this, html, message);
};

QuickMessageWindow.prototype.show = function (html, message) {
    var that = this,
        factorX = 25,
        factorY = 20;
    if ($('#tooltipmessagecustom')[0]) {
        $('#tooltipmessagecustom').css({
            'top': $(html).offset().top + factorY,
            'left': $(html).offset().left + factorX
        });
        $('#tooltipmessagecustombody').html(message);
    } else {
        var button = $('<div id="header"></div>')
            .append($("<a style='font-size: 14px'></a>")
                .html('X')
                .on('click', function () {
                    $('#tooltipmessagecustom').remove();
                }));
        $('body').append($('<div></div>')
            .append(button)
            .append($('<div></div>')
                .attr('id', 'tooltipmessagecustombody')
                .css({'float': 'left'})
                .html(message))
            .addClass('pmui pmui-pmtooltipmessage')
            .attr('id', 'tooltipmessagecustom')
            .css({
                'box-sizing': 'border-box', 'position': 'absolute',
                'z-index': '100', 'font-size': '10',
                'top': $(html).offset().top + factorY,
                'left': $(html).offset().left + factorX
            })).on('mousedown', function (evt) {
            that.closeEvent(evt);
        }).on('click', function (evt) {
            that.closeEvent(evt);
        }).on('mouseup', function (evt) {
            that.closeEvent(evt);
        });
        $(window).scroll(function () {
            that.close();
        });
    }
};

QuickMessageWindow.prototype.close = function () {
    $('#tooltipmessagecustom').remove();
};

QuickMessageWindow.prototype.closeEvent = function (evt) {
    var element = evt.target || evt.srcElement;
    if ($('#tooltipmessagecustom')[0] && element !== $('#tooltipmessagecustom')[0] && element !== $('#tooltipmessagecustom')[0].children[1]) {
        $('#tooltipmessagecustom').remove();
    }
};

var ButtonFormPanel = function (options) {
    options.labelVisible = false;
    this.onclick = options.onclick;
    this.parameter = options.parameter;
    PMUI.field.TextField.call(this, options);
    ButtonFormPanel.prototype.init.call(this, options);
};

ButtonFormPanel.prototype = new PMUI.field.TextField();

ButtonFormPanel.prototype.init = function (options) {
    var defaults = {};
    jQuery.extend(true, defaults, options);
};

ButtonFormPanel.prototype.createHTML = function () {
    var that = this, button;
    PMUI.field.TextField.prototype.createHTML.call(this);
    button = new PMUI.ui.Button({
        id: this.id,
        text: this.getLabel(),
        handler: function () {
            that.onclick(that);
        }
    });
    this.dom.controlContainer.appendChild(button.getHTML());
    this.dom.controlContainer.getElementsByTagName('input')[0].style.display = 'none';
    button.defineEvents();
    this.button = button;
    return this.html;
};

var LabelFormPanel = function (options) {
    PMUI.field.TextField.call(this, options);
    LabelFormPanel.prototype.init.call(this, options);
};
LabelFormPanel.prototype = new PMUI.field.TextField();

LabelFormPanel.prototype.init = function (options) {
    var defaults = {};
    jQuery.extend(true, defaults, options);
};

LabelFormPanel.prototype.createHTML = function () {
    PMUI.field.TextField.prototype.createHTML.call(this);
    this.dom.controlContainer.getElementsByTagName('input')[0].style.display = 'none';
    return this.html;
};

var messagePageGrid = function (currentPage, pageSize, numberItems, criteria, filter) {
    var msg;
    if (numberItems === 0) {
        return '';
    }
    msg = 'Page'.translate() + ' ' + (currentPage + 1) + ' ' + 'of'.translate() + ' ' + Math.ceil(numberItems / pageSize);
    return msg;
};

/*
 * Function: validateKeysField
 * valid characteres for file name:
 * http://support.microsoft.com/kb/177506/es
 * 
 * (A-z)letter
 * (0-9)number
 *  ^   Accent circumflex (caret)
 *  &   Ampersand
 *  '   Apostrophe (single quotation mark)
 *  @   At sign
 *  {   Brace left
 *  }   Brace right
 *  [   Bracket opening
 *  ]   Bracket closing
 *  ,   Comma
 *  $   Dollar sign
 *  =   Equal sign
 *  !   Exclamation point
 *  -   Hyphen
 *  #   Number sign
 *  (   Parenthesis opening
 *  )   Parenthesis closing
 *  %   Percent
 *  .   Period
 *  +   Plus
 *  ~   Tilde
 *  _   Underscore
 *  
 *  Example: only backspace, number and letter.
 *  validateKeysField(objectHtmlInput, ['isbackspace', 'isnumber', 'isletter']);
 *  
 *  Aditional support:
 *  :   Colon
 *  
 */
var validateKeysField = function (object, validates) {
    object.onkeypress = function (e) {
        var key = document.all ? e.keyCode : e.which,
            isbackspace = key === 8,
            isnumber = key > 47 && key < 58,
            isletter = (key > 96 && key < 123) || (key > 64 && key < 91),
            isaccentcircumflex = key === 94,
            isampersand = key === 41,
            isapostrophe = key === 145,
            isatsign = key === 64,
            isbraceleft = key === 123,
            isbraceright = key === 125,
            isbracketopening = key === 91,
            isbracketclosing = key === 93,
            iscomma = key === 130,
            isdollarsign = key === 36,
            isequalsign = key === 61,
            isexclamationpoint = key === 33,
            ishyphen = key === 45,
            isnumbersign = key === 35,
            isparenthesisopening = key === 40,
            isparenthesisclosing = key === 41,
            ispercent = key === 37,
            isperiod = key === 46,
            isplus = key === 43,
            istilde = key === 126,
            isunderscore = key === 95,
            iscolon = key === 58,
            sw = eval(validates[0]);

        if (key === 0) {
            return true;
        }
        sw = eval(validates[0]);
        for (var i = 1; i < validates.length; i++) {
            sw = sw || eval(validates[i]);
        }
        return sw;
    };
};

var applyStyleTreePanel = function (treePanel, fontStyle) {
    if (fontStyle !== false) {
        $(treePanel.getHTML()).find('a').css('font-weight', 'bold');
        $(treePanel.getHTML()).find('a').css('color', 'black');
        $(treePanel.getHTML()).find('ul li ul li>a').css('font-weight', 'normal');
        $(treePanel.getHTML()).find('ul li ul li>a').css('color', 'black');
    }
};

/*
 * Convert time format HH:MM:SS to decimal value.
 */
var timeToDecimal = function (value) {
    var s = value.toString().replace(/\s/g, '').split(':'), hour, min, sec;
    hour = parseInt(s[0]) || 0;
    min = parseInt(s[1]) || 1;
    sec = parseInt(s[2]) || 1;
    return (hour + min / 60 + sec / 3600);
};

/*
 * Convert decimal to time format HH:MM:SS.
 */
var decimalToTime = function (value, second) {
    var num = typeof value === 'number' ? value : 1, hour, min, sec;

    hour = parseInt(num);
    num = num - parseInt(num);
    num = num.toFixed(13);
    num = num * 60;

    min = parseInt(num);
    num = num - parseInt(num);
    num = num.toFixed(13);
    num = num * 60;

    sec = parseInt(num);

    hour = hour.toString().length === 1 ? '0' + hour : hour;
    min = min.toString().length === 1 ? '0' + min : min;
    sec = sec.toString().length === 1 ? '0' + sec : sec;

    return second === true ? hour + ':' + min + ':' + sec : hour + ':' + min;
};

var Mafe = {};
/**
 *
 * @param {type} settings
 * @returns {undefined}
 */
Mafe.Window = function (settings) {
    this.views = [];
    PMUI.ui.Window.call(this, settings);
    Mafe.Window.prototype.init.call(this, settings);
};

Mafe.Window.prototype = new PMUI.ui.Window();

Mafe.Window.prototype.init = function (settings) {
    this.setHeight(DEFAULT_WINDOW_HEIGHT);
    this.setWidth(DEFAULT_WINDOW_WIDTH);
    this.hideFooter();
    this.setButtonPanelPosition('bottom');
};

Mafe.Window.prototype.createHTML = function () {
    PMUI.ui.Window.prototype.createHTML.call(this);
    this.footer.html.style.textAlign = 'right';
    return this.html;
};

Mafe.Window.prototype.resetView = function () {
    var items;
    this.hideFooter();
    items = this.items.asArray();
    for (var i = 0; i < items.length; i++) {
        if (items[i].setVisible) {
            items[i].setVisible(false);
        }
        if (items[i].reset) {
            items[i].reset();
        }
    }
};

Mafe.Window.prototype.setButtons = function (buttons) {
    this.clearFooterItems();
    this.setFooterItems(buttons);
    this.showFooter();
};

/**
 *
 * @param {type} settings
 * @returns {undefined}
 */
Mafe.Grid = function (settings) {
    var defaults = {
        pageSize: 10,
        width: '96%',
        filterPlaceholder: 'Search ...'.translate(),
        emptyMessage: 'No records found'.translate(),
        nextLabel: 'Next'.translate(),
        previousLabel: 'Previous'.translate(),
        style: {cssClasses: ['mafe-gridPanel']},
        customStatusBar: function (currentPage, pageSize, numberItems, criteria, filter) {
            return messagePageGrid(currentPage, pageSize, numberItems, criteria, filter);
        }
    };
    jQuery.extend(true, defaults, settings);
    PMUI.grid.GridPanel.call(this, defaults);
    Mafe.Grid.prototype.init.call(this, defaults);
};

Mafe.Grid.prototype = new PMUI.grid.GridPanel();

Mafe.Grid.prototype.init = function (settings) {
    var defaults = {};
    jQuery.extend(true, defaults, settings);
};

Mafe.Grid.prototype.createHTML = function () {
    PMUI.grid.GridPanel.prototype.createHTML.call(this);
    $(this.html).find('.pmui-textcontrol').css({'margin-top': '5px', width: '250px'});
    return this.html;
};

/**
 *
 * @param {type} settings
 * @returns {undefined}
 */
Mafe.Form = function (settings) {
    var defaults;
    this.onYesConfirmCancellation = new Function();
    defaults = {
        width: DEFAULT_WINDOW_WIDTH - 3,
        height: 'auto'
    };
    jQuery.extend(true, defaults, settings);
    PMUI.form.Form.call(this, defaults);
    Mafe.Form.prototype.init.call(this);
};
Mafe.Form.prototype = new PMUI.form.Form();

Mafe.Form.prototype.init = function (settings) {
    var defaults = {};
    jQuery.extend(true, defaults, settings);
};

Mafe.Form.prototype.getConfirmCancellationToLoseChanges = function () {
    return this.confirmCancellationToLoseChanges;
};

Mafe.Form.prototype.loseChanges = function (options) {
    var that = this, confirmCancellation;
    if (that.isDirty()) {
        confirmCancellation = new Mafe.ConfirmCancellation(options);
        confirmCancellation.onYes = function () {
            that.onYesConfirmCancellation();
        };
    } else {
        that.onYesConfirmCancellation();
    }
};

Mafe.ConfirmDeletion = function () {
    var that = this, defaults;
    that.onDelete = new Function();
    that.onCancel = new Function();
    defaults = {
        id: 'idConfirmDeletion',
        width: 490,
        bodyHeight: 'auto',
        windowMessageType: 'warning',
        message: 'Do you want to delete this Element?'.translate(),
        footerItems: [{
            id: 'idCancelConfirmDeletion',
            text: 'No'.translate(),
            visible: true,
            handler: function () {
                that.onCancel();
                that.close();
            },
            buttonType: "error"
        }, {
            id: 'idDeleteConfirmDeletion',
            text: 'Yes'.translate(),
            visible: true,
            handler: function () {
                that.onDelete();
                that.close();
            },
            buttonType: "success"
        }
        ]
    };
    PMUI.ui.MessageWindow.call(this, defaults);
    Mafe.ConfirmDeletion.prototype.init.call(this);
};

Mafe.ConfirmDeletion.prototype = new PMUI.ui.MessageWindow();

Mafe.ConfirmDeletion.prototype.init = function () {
    this.open();
    this.showFooter();
};

/**
 *
 * @returns {undefined}
 */
Mafe.ConfirmCancellation = function (options) {
    var that = this, defaults;
    that.onYes = new Function();
    that.onNo = new Function();
    defaults = {
        id: 'idConfirmCancellation',
        title: options["title"] || 'Confirm'.translate(),
        width: 490,
        bodyHeight: 'auto',
        windowMessageType: 'warning',
        message: 'Are you sure you want to discard your changes?'.translate(),
        footerItems: [
            {
                id: 'idCancelConfirmCancellation',
                text: 'No'.translate(),
                visible: true,
                handler: function () {
                    that.onNo();
                    that.close();
                },
                buttonType: "error"
            }, {
                id: 'idDeleteConfirmCancellation',
                text: 'Yes'.translate(),
                visible: true,
                handler: function () {
                    that.onYes();
                    that.close();
                },
                buttonType: "success"
            }
        ]
    };
    PMUI.ui.MessageWindow.call(this, defaults);
    Mafe.ConfirmCancellation.prototype.init.call(this);
};
Mafe.ConfirmCancellation.prototype = new PMUI.ui.MessageWindow();

Mafe.ConfirmCancellation.prototype.init = function () {
    this.open();
    this.showFooter();
};

/**
 *
 * @param {type} settings
 * @returns {undefined}
 */
Mafe.Tree = function (settings) {
    var defaults;
    if (settings && settings.width) {
        this._width = settings.width;
    } else {
        this._width = 210;
    }
    defaults = {
        id: 'idMafeTree',
        filterable: true,
        filterPlaceholder: 'Search ...'.translate(),
        emptyMessage: 'No records found'.translate(),
        autoBind: true,
        nodeDefaultSettings: {
            autoBind: true,
            collapsed: false,
            labelDataBind: 'labelDataBind',
            itemsDataBind: 'itemsDataBind',
            childrenDefaultSettings: {
                labelDataBind: 'labelDataBind',
                autoBind: true
            }
        }
    };
    jQuery.extend(true, defaults, settings);
    PMUI.panel.TreePanel.call(this, defaults);
    Mafe.Tree.prototype.init.call(this, defaults);
};
Mafe.Tree.prototype = new PMUI.panel.TreePanel();

Mafe.Tree.prototype.init = function (defaults) {
    var that = this;
    that.style.addProperties({overflow: 'auto'});
};

Mafe.Tree.prototype.createHTML = function () {
    PMUI.panel.TreePanel.prototype.createHTML.call(this);
    this.setWidth(this._width);
    return this.html;
};

/**
 *
 * @param {type} settings
 * @returns {undefined}
 */
Mafe.Accordion = function (settings) {
    var defaults;
    if (settings && settings.width) {
        this._width = settings.width;
    }
    defaults = {
        id: 'idAccordion',
        hiddenTitle: true,
        heightItem: 'auto',
        title: '',
        multipleSelection: true,
        listeners: {
            select: function (accordionItem, event) {
            }
        }
    };
    jQuery.extend(true, defaults, settings);
    PMUI.panel.AccordionPanel.call(this, defaults);
    Mafe.Accordion.prototype.init.call(this);
};
Mafe.Accordion.prototype = new PMUI.panel.AccordionPanel();

Mafe.Accordion.prototype.init = function () {
    var that = this;
    that.style.addProperties({'vertical-align': 'top'});
};

Mafe.Accordion.prototype.createHTML = function () {
    PMUI.panel.AccordionPanel.prototype.createHTML.call(this);
    this.setWidth(this._width);
    return this.html;
};

/**
 * Failsafe remove an element from a collection
 *
 * @param  {Array<Object>} [collection]
 * @param  {Object} [element]
 *
 * @return {Object} the element that got removed or undefined
 */
var CollectionRemove = function (collection, element) {
    var idx;
    if (!collection || !element) {
        return;
    }
    idx = collection.indexOf(element);
    if (idx === -1) {
        return;
    }
    collection.splice(idx, 1);
    return element;
};

function setEncoded(link, name, data) {
    var encodedData = encodeURIComponent(data);
    if (window.navigator.msSaveBlob) {
        window.navigator.msSaveBlob(new Blob([data], {type: "application/octet-stream"}), name);

    } else {
        if (data) {
            link.addClass('active').attr({
                'href': 'data:application/bpmn20-xml;charset=UTF-8,' + encodedData,
                'download': name
            });
        } else {
            link.removeClass('active');
        }
    }

}

function convertDatetimeToIso8601(datetime) {
    var separate, date, time, timeDifference, signed, hours, minutes;
    separate = datetime.split(' ');
    date = separate[0].split('-');
    time = (separate[1] || '00:00:00').split(':');
    date = new Date(date[0] || 0, (date[1] || 1) - 1, date[2] || 0, time[0] || 0, time[1] || 0, time[2] || 0);
    timeDifference = date.getTimezoneOffset();
    signed = '+';
    if (timeDifference > 0) {
        signed = '-';
    }
    timeDifference = Math.abs(timeDifference);
    hours = Math.floor(timeDifference / 60).toString();
    minutes = Math.floor(timeDifference % 60).toString();
    return datetime.replace(/\s/g, 'T') + signed +
        ('0' + hours).slice(-2) + ':' + ('0' + minutes).slice(-2);
}