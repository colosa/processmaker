(function () {
    var Properties = function (type, ele, owner) {
        this.onGet = new Function();
        this.onSet = new Function();
        this.onClick = new Function();
        this.onClickClearButton = new Function();
        this.ele = ele;
        this.owner = owner;
        this.pf = [];
        Properties.prototype.init.call(this, type);
    };
    Properties.prototype.init = function (type) {
        this.type = {label: "type".translate(), value: type, type: "label"};
        this.id = {
            label: "id".translate(),
            value: "",
            type: "text",
            on: "change",
            regExp: /^[a-zA-Z0-9-_]+$/,
            regExpInv: /[^a-zA-Z0-9-_]/gi,
            required: true
        };
        this.name = {label: "name".translate(), value: "", type: "hidden", labelButton: "...", required: true};
        this.description = {label: "description".translate(), value: "", type: "textarea"};
        this.placeholder = {label: "placeholder".translate(), value: "", type: "textarea"};
        this.colSpan = {
            label: "col-span".translate(),
            value: "12",
            type: "text",
            on: "change",
            helpButton: "Colspan is used to define the size and number of columns of a selected row. Twelve columns can be defined as maximum. ".translate() + "The column sizes are defined with integer numbers separated by spaces. Any combination of column sizes can be defined but all the columns sizes should add 12. <br>".translate() + "ex:<br>two columns of different sizes: 8 4<br>two columns of the same size: 6 6<br>Three columns of the same size: 4 4 4<br>Three columns of different sizes: 2 8 2<br><br>" + "For a better design we recommend using values above 3.<br>If you need more info please visit <a target='_blank' href='http://getbootstrap.com/css/'>Bootstrap grid system</a>.".translate()
        };
        this.label = {label: "label".translate(), value: "", type: "text"};
        this.href = {label: "href".translate(), value: "http://www.processmaker.com/", type: "textarea"};
        this.hint = {label: "hint".translate(), value: "", type: "textarea"};
        this.src = {label: "src".translate(), value: "", type: "text"};
        this.shape = {
            label: "shape".translate(), value: "", type: "select", items: [
                {value: "thumbnail", label: "thumbnail".translate()},
                {value: "rounded", label: "rounded".translate()},
                {value: "circle", label: "circle".translate()}
            ]
        };
        this.alternateText = {label: "alternate text".translate(), value: "", type: "textarea"};
        this.comment = {label: "comment".translate(), value: "", type: "text"};
        this.required = {label: "required".translate(), value: false, type: "checkbox"};
        this.dnd = {label: "drag & drop".translate(), value: false, type: "hidden"};
        this.extensions = {label: "file extensions".translate(), value: "*", type: "text"};
        this.galleryEnabled = {label: "Enable gallery".translate(), value: true, type: "checkbox"};
        this.cameraEnabled = {label: "Enable camera".translate(), value: true, type: "checkbox"};
        this.size = {label: "max file size".translate(), value: 1024, type: "text"};
        this.sizeUnity = {
            label: "size unit".translate(), value: "KB", type: "select", items: [
                {value: "KB", label: "KB".translate()},
                {value: "MB", label: "MB".translate()}
            ]
        };
        this.enableVersioning = {
            label: "versioning".translate(),
            value: "",
            type: "label"
        };
        this.columns = {label: "columns".translate(), value: [], type: "hidden"};
        this.data = {label: "data".translate(), value: [], type: "hidden"};
        this.dataType = {label: "variable data type".translate(), value: "", type: "label"};
        this.value = {label: "value".translate(), value: "", type: "text"};
        this.defaultValue = {label: "default value".translate(), value: "", type: "text"};
        this.textTransform = {
            label: "text transform to".translate(), value: "none", type: "select", items: [
                {value: "none", label: "none".translate()},
                {value: "lower", label: "lowercase".translate()},
                {value: "upper", label: "UPPERCASE".translate()},
                {value: "capitalizePhrase", label: "Capitalize phrase".translate()},
                {value: "titleCase", label: "Title Case".translate()}
            ]
        };
        this.validate = {
            label: "validate".translate(),
            value: "",
            type: "textareabutton",
            labelButton: "help".translate(),
            placeholder: "Use a pattern (to be used in a search).".translate()
        };
        this.validateMessage = {
            label: "validation error message".translate(),
            value: "",
            type: "textarea",
            placeholder: "Error message".translate()
        };
        this.requiredFieldErrorMessage = {
            label: "required field error message".translate(),
            value: "",
            type: "textarea",
            placeholder: "Required field error message".translate()
        };
        this.maxLength = {label: "max length".translate(), value: 1000, type: "text", regExp: /^[0-9]+$/};
        this.formula = {label: "formula".translate(), value: "", type: "button", labelButton: "edit...".translate()};
        this.mode = {
            label: "display mode".translate(), value: "parent", type: "select", items: [
                {value: "parent", label: "parent".translate()},
                {value: "edit", label: "edit".translate()},
                {value: "view", label: "view".translate()},
                {value: "disabled", label: "disabled".translate()}
            ], helpButton: "" +
            "Select the mode of the control:<br>".translate() +
            "<ul style='margin:2px 2px 2px -25px;'>".translate() +
            "<li>Parent: Inherit the mode from parent.</li>".translate() +
            "<li>Edit: Allow user to change the control's value.</li>".translate() +
            "<li>View: Allow user to only view the control's value.</li>".translate() +
            "<li>Disabled: Control is greyed out, but still displays its value.</li>".translate() +
            "</ul>"
        };
        this.variable = {
            label: "variable".translate(),
            value: "",
            type: "labelbutton",
            labelButton: "...",
            clearButton: "clear".translate()
        };
        this.inputDocument = {
            label: "Input Document".translate(),
            value: "",
            type: "labelbutton",
            labelButton: "...",
            clearButton: "clear".translate()
        };
        this.var_uid = {label: "var_uid".translate(), value: "", type: "hidden"};
        this.protectedValue = {label: "protected value".translate(), value: false, type: "checkbox"};
        this.delay = {label: "Delay".translate(), value: 0, type: "text", regExp: /^[0-9]+$/};
        this.alt = {label: "title (mouseover)".translate(), value: "", type: "text"};
        this.multiple = {label: "multiple".translate(), value: false, type: "hidden"};
        this.script = {label: "javascript".translate(), value: "", type: "button", labelButton: "edit...".translate()};
        this.layout = {
            label: "layout".translate(), value: "responsive", type: "select", items: [
                {value: "responsive", label: "responsive".translate()},
                {value: "static", label: "static".translate()}
            ]
        };
        this.pageSize = {
            label: "page size".translate(), value: "0", type: "select", items: [
                {value: "0", label: "none".translate()},
                {value: "5", label: "5"},
                {value: "10", label: "10"},
                {value: "20", label: "20"},
                {value: "50", label: "50"},
                {value: "100", label: "100"},
                {value: "200", label: "200"}
            ]
        };
        this.addRow = {label: "add row".translate(), value: true, type: "checkbox"};
        this.deleteRow = {label: "delete row".translate(), value: true, type: "checkbox"};
        this.columnWidth = {label: "column width".translate(), value: "10", type: "text", helpButton: ""};
        this.operation = {
            label: "function".translate(), value: "", type: "select",
            items: [
                {value: "", label: "none".translate()},
                {value: "sum", label: "sum".translate()},
                {value: "avg", label: "average".translate()}
            ]
        };
        this.datasource = {
            label: "datasource".translate(), value: "database", type: "select",
            items: [
                {value: "database", label: "database".translate()},
                {value: "dataVariable", label: "array variable".translate()}
            ]
        };
        this.dbConnectionLabel = {
            label: "DB Connection".translate(),
            value: "PM Database",
            type: "labelbutton",
            labelButton: "..."
        };
        this.dbConnection = {label: "", value: "workflow", type: "hidden"};
        this.sql = {label: "sql".translate(), value: "", type: "labelbutton", labelButton: "..."};
        this.dataVariable = {
            label: "data variable".translate(),
            value: "",
            type: "hidden",
            labelButton: "@@",
            placeholder: "@@myData".translate()
        };
        this.options = {label: "options".translate(), value: [], type: "labelbutton", labelButton: "..."};
        this.language = {
            label: "language".translate(), value: "en", type: "select", items: [
                {value: "en", label: "English".translate()}
            ]
        };
        this.content = {label: "content".translate(), value: "", type: "button", labelButton: "edit...".translate()};
        this.border = {label: "border".translate(), value: "1px", type: "text"};
        this.externalLibs = {label: "external libs".translate(), value: "", type: "textarea"};
        this.gridStore = {label: "grid store".translate(), value: false, type: "checkbox"};
        this.rows = {label: "rows".translate(), value: "5", type: "text", regExp: /^[0-9]+$/};
        this.inp_doc_uid = {label: "", value: "", type: "hidden"};
        this.printable = {label: "print dynaform".translate(), value: false, type: "hidden"};

        this.format = {
            label: "format".translate(),
            value: "YYYY-MM-DD",
            type: "text",
            helpButton: "Default: false".translate() +
            "<p>See <a href='http://momentjs.com/docs/#/displaying/format/' target='_blank'>http://momentjs.com/docs/#/displaying/format/</a> ".translate() + "for valid datetime formats. If only the date is included in the format then the time picker will not be displayed.</p>".translate() +
            "Examples:".translate() +
            "<ul style='margin:2px 2px 2px -25px;'>" +
            "<li>dddd, MMMM Do YYYY, h:mm:ss a >> \"Sunday, February 14th 2010, 3:25:50 pm\"</li>".translate() +
            "<li>ddd, hA >> \"Sun, 3PM\"</li>".translate() +
            "<li>YYYY MM DD >> \"Valid date\"</li>".translate() +
            "</ul>" +
            "<table border='1' style='border-collapse:collapse;'><tbody>" + "<tr><th></th><th>Token</th><th>Output</th></tr><tr><td><b>Month</b></td><td>M</td><td>1 2 ... 11 12</td></tr>".translate() + "<tr><td></td><td>Mo</td><td>1st 2nd ... 11th 12th</td></tr><tr><td></td><td>MM</td><td>01 02 ... 11 12</td></tr>".translate() + "<tr><td></td><td>MMM</td><td>Jan Feb ... Nov Dec</td></tr><tr><td></td><td>MMMM</td><td>January February ... November December</td></tr>".translate() + "<tr><td><b>Quarter</b></td><td>Q</td><td>1 2 3 4</td></tr><tr><td><b>Day of Month</b></td><td>D</td><td>1 2 ... 30 31</td></tr>".translate() + "<tr><td></td><td>Do</td><td>1st 2nd ... 30th 31st</td></tr><tr><td></td><td>DD</td><td>01 02 ... 30 31</td></tr>".translate() + "<tr><td><b>Day of Year</b></td><td>DDD</td><td>1 2 ... 364 365</td></tr><tr><td></td><td>DDDo</td><td>1st 2nd ... 364th 365th</td></tr>".translate() + "<tr><td></td><td>DDDD</td><td>001 002 ... 364 365</td></tr><tr><td><b>Day of Week</b></td><td>d</td><td>0 1 ... 5 6</td></tr>".translate() + "<tr><td></td><td>do</td><td>0th 1st ... 5th 6th</td></tr><tr><td></td><td>dd</td><td>Su Mo ... Fr Sa</td></tr>".translate() + "<tr><td></td><td>ddd</td><td>Sun Mon ... Fri Sat</td></tr><tr><td></td><td>dddd</td><td>Sunday Monday ... Friday Saturday</td></tr>".translate() + "<tr><td><b>Day of Week (Locale)</b></td><td>e</td><td>0 1 ... 5 6</td></tr><tr><td><b>Day of Week (ISO)</b></td><td>E</td><td>1 2 ... 6 7</td></tr>".translate() + "<tr><td><b>Week of Year</b></td><td>w</td><td>1 2 ... 52 53</td></tr><tr><td></td><td>wo</td><td>1st 2nd ... 52nd 53rd</td></tr>".translate() + "<tr><td></td><td>ww</td><td>01 02 ... 52 53</td></tr><tr><td><b>Week of Year (ISO)</b></td><td>W</td><td>1 2 ... 52 53</td></tr>".translate() + "<tr><td></td><td>Wo</td><td>1st 2nd ... 52nd 53rd</td></tr><tr><td></td><td>WW</td><td>01 02 ... 52 53</td></tr>".translate() + "<tr><td><b>Year</b></td><td>YY</td><td>70 71 ... 29 30</td></tr><tr><td></td><td>YYYY</td><td>1970 1971 ... 2029 2030</td></tr>".translate() + "<tr><td><b>Week Year</b></td><td>gg</td><td>70 71 ... 29 30</td></tr><tr><td></td><td>gggg</td><td>1970 1971 ... 2029 2030</td></tr>".translate() + "<tr><td><b>Week Year (ISO)</b></td><td>GG</td><td>70 71 ... 29 30</td></tr><tr><td></td><td>GGGG</td><td>1970 1971 ... 2029 2030</td></tr>".translate() + "<tr><td><b>AM/PM</b></td><td>A</td><td>AM PM</td></tr><tr><td></td><td>a</td><td>am pm</td></tr>".translate() + "<tr><td><b>Hour</b></td><td>H</td><td>0 1 ... 22 23</td></tr><tr><td></td><td>HH</td><td>00 01 ... 22 23</td></tr>".translate() + "<tr><td></td><td>h</td><td>1 2 ... 11 12</td></tr><tr><td></td><td>hh</td><td>01 02 ... 11 12</td></tr>".translate() + "<tr><td><b>Minute</b></td><td>m</td><td>0 1 ... 58 59</td></tr><tr><td></td><td>mm</td><td>00 01 ... 58 59</td></tr>".translate() + "<tr><td><b>Second</b></td><td>s</td><td>0 1 ... 58 59</td></tr><tr><td></td><td>ss</td><td>00 01 ... 58 59</td></tr>".translate() + "<tr><td><b>Fractional Second</b></td><td>S</td><td>0 1 ... 8 9</td></tr><tr><td></td><td>SS</td><td>0 1 ... 98 99</td></tr>".translate() + "<tr><td></td><td>SSS</td><td>0 1 ... 998 999</td></tr><tr><td><b>Timezone</b></td><td>z or zz</td><td>EST CST ... MST PST<br><b>Note:</b> as of <b>1.6.0</b>, the z/zz format tokens have been deprecated. ".translate() + "<a href=\"https://github.com/moment/moment/issues/162\">Read more about it here.</a></td></tr>".translate() + "<tr><td></td><td>Z</td><td>-07:00 -06:00 ... +06:00 +07:00</td></tr><tr><td></td><td>ZZ</td><td>-0700 -0600 ... +0600 +0700</td></tr>".translate() + "<tr><td><b>Unix Timestamp</b></td><td>X</td><td>1360013296</td></tr><tr><td><b>Unix Millisecond Timestamp</b></td><td>x</td><td>1360013296123</td></tr></tbody></table>".translate(),
            helpButtonCss: "fd-tooltip-date-format"
        };
        this.dayViewHeaderFormat = {label: "day view header format".translate(), value: "MMMM YYYY", type: "hidden"};
        this.extraFormats = {label: "extra formats".translate(), value: false, type: "hidden"};
        this.stepping = {label: "stepping".translate(), value: 1, type: "hidden"};
        this.minDate = {
            label: "min date".translate(),
            value: "",
            type: "datepicker",
            helpButton: "Allows date selection after this date<br>(in YYYY-MM-DD HH:MM:SS format)".translate(),
            clearButton: "clear".translate(),
            regExp: /^[1-9][0-9][0-9][0-9]-(0[1-9]|1[0-2])-(0[1-9]|1[0-9]|2[0-9]|3[0-1])$|^(@@|@#|@%|@=|@\?|@\$)[a-zA-Z]+[0-9a-zA-Z_]*$|^$|^[1-9][0-9][0-9][0-9]-(0[1-9]|1[0-2])-(0[1-9]|1[0-9]|2[0-9]|3[0-1])\s(0[0-9]|1[0-9]|2[0-3]):(0[0-9]|1[0-9]|2[0-9]|3[0-9]|4[0-9]|5[0-9]):(0[0-9]|1[0-9]|2[0-9]|3[0-9]|4[0-9]|5[0-9])$|^[1-9][0-9][0-9][0-9]-(0[1-9]|1[0-2])-(0[1-9]|1[0-9]|2[0-9]|3[0-1])\s(0[0-9]|1[0-9]|2[0-3])$|^[1-9][0-9][0-9][0-9]-(0[1-9]|1[0-2])-(0[1-9]|1[0-9]|2[0-9]|3[0-1])\s(0[0-9]|1[0-9]|2[0-3]):(0[0-9]|1[0-9]|2[0-9]|3[0-9]|4[0-9]|5[0-9])$/
        };
        this.maxDate = {
            label: "max date".translate(),
            value: "",
            type: "datepicker",
            helpButton: "Allows date selection before this date<br>(in YYYY-MM-DD HH:MM:SS format)".translate(),
            clearButton: "clear".translate(),
            regExp: /^[1-9][0-9][0-9][0-9]-(0[1-9]|1[0-2])-(0[1-9]|1[0-9]|2[0-9]|3[0-1])$|^(@@|@#|@%|@=|@\?|@\$)[a-zA-Z]+[0-9a-zA-Z_]*$|^$|^[1-9][0-9][0-9][0-9]-(0[1-9]|1[0-2])-(0[1-9]|1[0-9]|2[0-9]|3[0-1])\s(0[0-9]|1[0-9]|2[0-3]):(0[0-9]|1[0-9]|2[0-9]|3[0-9]|4[0-9]|5[0-9]):(0[0-9]|1[0-9]|2[0-9]|3[0-9]|4[0-9]|5[0-9])$|^[1-9][0-9][0-9][0-9]-(0[1-9]|1[0-2])-(0[1-9]|1[0-9]|2[0-9]|3[0-1])\s(0[0-9]|1[0-9]|2[0-3])$|^[1-9][0-9][0-9][0-9]-(0[1-9]|1[0-2])-(0[1-9]|1[0-9]|2[0-9]|3[0-1])\s(0[0-9]|1[0-9]|2[0-3]):(0[0-9]|1[0-9]|2[0-9]|3[0-9]|4[0-9]|5[0-9])$/
        };
        this.useCurrent = {
            label: "initial selection date".translate(),
            value: "false",
            type: "select",
            items: [
                {
                    value: "false",
                    label: "false".translate()
                },
                {
                    value: "true",
                    label: "true".translate()
                },
                {
                    value: "year",
                    label: "year".translate()
                },
                {
                    value: "month",
                    label: "month".translate()
                },
                {
                    value: "day",
                    label: "day".translate()
                },
                {
                    value: "hour",
                    label: "hour".translate()
                },
                {
                    value: "minute",
                    label: "minute".translate()
                }
            ],
            helpButton: "Default: false<br>On show, will set the picker to:<br>".translate() + "false: No default selection <br>true: current date<br>year: the first day of the current year<br>month: the first day of the month<br>day: the current day<br>hour: the current hour without minutes<br>minute: the current minute".translate()
        };
        this.collapse = {label: "collapse".translate(), value: true, type: "hidden"};
        this.locale = {
            label: "locale".translate(),
            value: "",
            type: "hidden",
            accepts: "string, moment.local('locale')".translate()
        };
        this.defaultDate = {
            label: "default date".translate(),
            value: "",
            type: "datepicker",
            helpButton: "Set the date picker to this date by default<br>(in YYYY-MM-DD HH:MM:SS format)".translate(),
            clearButton: "clear".translate(),
            regExp: /^[1-9][0-9][0-9][0-9]-(0[1-9]|1[0-2])-(0[1-9]|1[0-9]|2[0-9]|3[0-1])$|^(@@|@#|@%|@=|@\?|@\$)[a-zA-Z]+[0-9a-zA-Z_]*$|^$|^[1-9][0-9][0-9][0-9]-(0[1-9]|1[0-2])-(0[1-9]|1[0-9]|2[0-9]|3[0-1])\s(0[0-9]|1[0-9]|2[0-3]):(0[0-9]|1[0-9]|2[0-9]|3[0-9]|4[0-9]|5[0-9]):(0[0-9]|1[0-9]|2[0-9]|3[0-9]|4[0-9]|5[0-9])$|^[1-9][0-9][0-9][0-9]-(0[1-9]|1[0-2])-(0[1-9]|1[0-9]|2[0-9]|3[0-1])\s(0[0-9]|1[0-9]|2[0-3])$|^[1-9][0-9][0-9][0-9]-(0[1-9]|1[0-2])-(0[1-9]|1[0-9]|2[0-9]|3[0-1])\s(0[0-9]|1[0-9]|2[0-3]):(0[0-9]|1[0-9]|2[0-9]|3[0-9]|4[0-9]|5[0-9])$/
        };
        this.disabledDates = {
            label: "disabled dates".translate(),
            value: false,
            type: "hidden",
            accepts: "array of [date, moment, string]".translate()
        };
        this.enabledDates = {
            label: "enabled dates".translate(),
            value: false,
            type: "hidden",
            accepts: "array of [date, moment, string]".translate()
        };
        this.icons = {
            label: "icons".translate(), value: {
                time: 'glyphicon glyphicon-time',
                date: 'glyphicon glyphicon-calendar',
                up: 'glyphicon glyphicon-chevron-up',
                down: 'glyphicon glyphicon-chevron-down',
                previous: 'glyphicon glyphicon-chevron-left',
                next: 'glyphicon glyphicon-chevron-right',
                today: 'glyphicon glyphicon-screenshot',
                clear: 'glyphicon glyphicon-trash'
            }, type: "hidden", accepts: "object with all or some of the parameters above".translate()
        };
        this.useStrict = {label: "useStrict".translate(), value: false, type: "hidden"};
        this.sideBySide = {label: "side by side".translate(), value: false, type: "hidden"};
        this.daysOfWeekDisabled = {
            label: "days of week disabled".translate(),
            value: false,
            type: "hidden",
            accepts: "array of numbers from 0-6".translate()
        };
        this.calendarWeeks = {label: "calendar weeks".translate(), value: false, type: "hidden"};
        this.viewMode = {
            label: "datepicker view mode".translate(),
            value: "days",
            type: "select",
            items: [
                {value: "days", label: "days".translate()},
                {value: "months", label: "months".translate()},
                {value: "years", label: "years".translate()}
            ],
            helpButton: "Select dates by days, months or years by default when the date picker is initially shown.<br>".translate() + "Note: To remove days, months or years from the date picker, use a format which does not have those elements. For example a format of \"MM/YYY\" will not allow the user to select days.".translate()
        };
        this.toolbarPlacement = {
            label: "toolbar placement".translate(),
            value: "default",
            type: "hidden",
            accepts: "'default', 'top', 'bottom'".translate()
        };
        this.showTodayButton = {label: "show today button".translate(), value: false, type: "hidden"};
        this.showClear = {
            label: "show clear button".translate(),
            value: "false",
            type: "select",
            items: [
                {value: "false", label: "hide".translate()},
                {value: "true", label: "show".translate()}
            ],
            helpButton: "Default: false<br>Show the \"Clear\" button in the icon toolbar.<br>".translate() + "Clicking the \"Clear\" button will set the calendar to null.".translate()
        };
        this.widgetPositioning = {
            label: "widget positioning".translate(),
            value: {
                horizontal: 'auto',
                vertical: 'auto'
            },
            type: "hidden",
            accepts: "object with the all or one of the parameters above; horizontal: 'auto', 'left', 'right' or vertical: 'auto', 'top', 'bottom'".translate()
        };
        this.widgetParent = {
            label: "widget parent".translate(),
            value: null,
            type: "hidden",
            accepts: "string or jQuery object".translate()
        };
        this.keepOpen = {label: "keep open".translate(), value: false, type: "hidden"};

        //custom properties
        if (this.owner instanceof FormDesigner.main.GridItem) {
            this.variable.type = "hidden";
            this.dataType.type = "hidden";
        }
        if (type === FormDesigner.main.TypesControl.form) {
            this.pf = ["type", "variable", "var_uid", "dataType", "id", "name", "description", "mode", "script",
                "language", "externalLibs", "printable"];
            this.id.type = "label";
            this.id.required = false;
            this.name.type = "text";
            this.name.on = "change";
            this.name.regExp = !/^\s+|\s+$/g;
            this.language.type = "hidden";
            this.variable.type = "hidden";
            this.dataType.type = "hidden";
        }
        if (type === FormDesigner.main.TypesControl.title) {
            this.pf = ["type", "id", "label"];
            this.name.type = "text";
            this.label.type = "textarea";
        }
        if (type === FormDesigner.main.TypesControl.subtitle) {
            this.pf = ["type", "id", "label"];
            this.name.type = "text";
            this.label.type = "textarea";
        }
        if (type === FormDesigner.main.TypesControl.link) {
            this.pf = ["type", "id", "name", "label", "value", "href", "hint"];
            this.name.type = "text";
            this.label.type = "text";
            this.value.label = "display text".translate();
            if (this.owner instanceof FormDesigner.main.GridItem) {
                this.pf.push("columnWidth");
            }
        }
        if (type === FormDesigner.main.TypesControl.image) {
            this.pf = ["type", "id", "name", "label", "hint", "src", "shape", "alternateText", "comment", "mode", "alt"];
            this.name.type = "text";
            this.label.type = "text";
        }
        if (type === FormDesigner.main.TypesControl.file) {
            this.pf = ["type", "variable", "var_uid", "dataType", "protectedValue", "id", "name", "label", "hint",
                "required", "requiredFieldErrorMessage", "dnd", "extensions", "size", "sizeUnity", "mode", "multiple",
                "inp_doc_uid"];
            this.name.type = "text";
            this.label.type = "text";
            if (this.owner instanceof FormDesigner.main.GridItem) {
                this.pf.push("columnWidth");
            }
        }
        if (type === FormDesigner.main.TypesControl.multipleFile) {
            this.pf = ["type", "variable", "var_uid", "dataType", "protectedValue", "id", "name", "label",
                "inputDocument", "required", "requiredFieldErrorMessage", "dnd", "extensions", "size", "sizeUnity",
                'enableVersioning', "mode", "multiple", "inp_doc_uid"];
            this.name.type = "hidden";
            this.label.type = "text";
            if (this.owner instanceof FormDesigner.main.GridItem) {
                this.pf.push("columnWidth");
            }
        }
        if (type === FormDesigner.main.TypesControl.submit) {
            this.pf = ["type", "id", "name", "label"];
            this.name.type = "text";
            this.label.type = "text";
        }
        if (type === FormDesigner.main.TypesControl.button) {
            this.pf = ["type", "id", "name", "label"];
            this.name.type = "text";
            this.label.type = "text";
        }
        if (type === FormDesigner.main.TypesControl.grid) {
            this.pf = ["type", "variable", "var_uid", "dataType", "protectedValue", "id", "name", "label", "hint",
                "required", "requiredFieldErrorMessage", "columns", "data", "mode", "layout", "pageSize", "addRow",
                "deleteRow"];
            this.label.label = "title".translate();
            this.label.type = "text";
        }
        if (type === FormDesigner.main.TypesControl.subform) {
            this.pf = ["type", "id", "name", "description", "mode"];
        }
        if (type === FormDesigner.main.TypesControl.text) {
            this.pf = ["type", "variable", "var_uid", "dataType", "protectedValue", "id", "name", "label",
                "defaultValue", "placeholder", "hint", "required", "requiredFieldErrorMessage", "textTransform",
                "validate", "validateMessage", "maxLength", "formula", "mode", "operation", "dbConnection",
                "dbConnectionLabel", "sql"];
            if (this.owner instanceof FormDesigner.main.FormItem) {
                this.operation.type = "hidden";
            }
            if (this.owner instanceof FormDesigner.main.GridItem) {
                this.pf.push("columnWidth");
            }
        }
        if (type === FormDesigner.main.TypesControl.textarea) {
            this.pf = ["type", "variable", "var_uid", "dataType", "protectedValue", "id", "name", "label",
                "defaultValue", "placeholder", "hint", "required", "requiredFieldErrorMessage", "validate",
                "validateMessage", "mode", "dbConnection", "dbConnectionLabel", "sql", "rows"];
            if (this.owner instanceof FormDesigner.main.GridItem) {
                this.pf.push("columnWidth");
            }
        }
        if (type === FormDesigner.main.TypesControl.dropdown) {
            this.pf = ["type", "variable", "var_uid", "dataType", "protectedValue", "id", "name", "label",
                "defaultValue", "placeholder", "hint", "required", "requiredFieldErrorMessage", "mode", "datasource",
                "dbConnection", "dbConnectionLabel", "sql", "dataVariable", "options"];
            if (this.owner instanceof FormDesigner.main.GridItem) {
                this.pf.push("columnWidth");
            }
        }
        if (type === FormDesigner.main.TypesControl.checkbox) {
            this.pf = ["type", "variable", "var_uid", "dataType", "protectedValue", "id", "name", "label",
                "defaultValue", "hint", "required", "requiredFieldErrorMessage", "mode", "options"];
            this.defaultValue.type = "checkbox";
            if (this.owner instanceof FormDesigner.main.FormItem) {
                this.options.type = "hidden";
            }
            if (this.owner instanceof FormDesigner.main.GridItem) {
                this.pf.push("columnWidth");
                this.options.type = "hidden";
                this.dbConnectionLabel.type = "hidden";
                this.dbConnection.type = "hidden";
                this.sql.type = "hidden";
            }
        }
        if (type === FormDesigner.main.TypesControl.checkgroup) {
            this.pf = ["type", "variable", "var_uid", "dataType", "protectedValue", "id", "name", "label",
                "defaultValue", "hint", "required", "requiredFieldErrorMessage", "mode", "datasource", "dbConnection",
                "dbConnectionLabel", "sql", "dataVariable", "options"];
        }
        if (type === FormDesigner.main.TypesControl.radio) {
            this.pf = ["type", "variable", "var_uid", "dataType", "protectedValue", "id", "name", "label",
                "defaultValue", "hint", "required", "requiredFieldErrorMessage", "mode", "datasource", "dbConnection",
                "dbConnectionLabel", "sql", "dataVariable", "options"];
        }
        if (type === FormDesigner.main.TypesControl.datetime) {
            this.pf = ["type", "variable", "var_uid", "dataType", "protectedValue", "id", "name", "label",
                "placeholder", "hint", "required", "requiredFieldErrorMessage", "mode", "format", "dayViewHeaderFormat",
                "extraFormats", "stepping", "minDate", "maxDate", "useCurrent", "collapse", "locale", "defaultDate",
                "disabledDates", "enabledDates", "icons", "useStrict", "sideBySide", "daysOfWeekDisabled",
                "calendarWeeks", "viewMode", "toolbarPlacement", "showTodayButton", "showClear", "widgetPositioning",
                "widgetParent", "keepOpen"];
            this.type.helpButton = "Date/time picker widget based on twitter bootstrap <br>" +
                "<a href='http://eonasdan.github.io/bootstrap-datetimepicker/' target='_blank'>" +
                "http://eonasdan.github.io/bootstrap-datetimepicker/</a>".translate();
            if (this.owner instanceof FormDesigner.main.GridItem) {
                this.pf.push("columnWidth");
            }
        }
        if (type === FormDesigner.main.TypesControl.suggest) {
            this.pf = ["type", "variable", "var_uid", "dataType", "protectedValue", "id", "name", "label",
                "defaultValue", "placeholder", "hint", "required", "requiredFieldErrorMessage", "mode", "datasource",
                "dbConnection", "dbConnectionLabel", "sql", "dataVariable", "options", "delay"];
            if (this.owner instanceof FormDesigner.main.GridItem) {
                this.pf.push("columnWidth");
            }
        }
        if (type === FormDesigner.main.TypesControl.annotation) {
            this.pf = ["type", "id", "label"];
            this.name.type = "text";
            this.label.label = "text".translate();
            this.label.type = "textarea";
        }
        if (type === FormDesigner.main.TypesControl.hidden) {
            this.pf = ["type", "variable", "var_uid", "dataType", "protectedValue", "id", "name", "defaultValue",
                "dbConnection", "dbConnectionLabel", "sql"];
        }
        if (type === FormDesigner.main.TypesControl.panel) {
            this.pf = ["type", "id", "content", "border"];
        }
        if (type === FormDesigner.main.TypesControl.msgPanel) {
            this.pf = ["type"];
        }
        if (type === FormDesigner.main.TypesControl.geomap) {
            this.pf = ["type", "variable", "var_uid", "protectedValue", "id", "name", "label", "hint"];
            this.label.type = "text";
        }
        if (type === FormDesigner.main.TypesControl.qrcode) {
            this.pf = ["type", "variable", "var_uid", "protectedValue", "id", "name", "label", "hint"];
            this.label.type = "text";
        }
        if (type === FormDesigner.main.TypesControl.signature) {
            this.pf = ["type", "id", "name", "label", "hint"];
            this.label.type = "text";
        }
        if (type === FormDesigner.main.TypesControl.imagem) {
            this.pf = ["type", "id", "name", "label", "inputDocument", "hint", "required", "requiredFieldErrorMessage",
                "cameraEnabled", "galleryEnabled", "inp_doc_uid"];
            this.label.type = "text";
            if (this.owner instanceof FormDesigner.main.GridItem) {
                this.pf.push("columnWidth");
            }
        }
        if (type === FormDesigner.main.TypesControl.audiom) {
            this.pf = ["type", "id", "name", "label", "inputDocument", "hint", "required", "requiredFieldErrorMessage",
                "inp_doc_uid"];
            this.label.type = "text";
        }
        if (type === FormDesigner.main.TypesControl.videom) {
            this.pf = ["type", "id", "name", "label", "inputDocument", "hint", "required", "requiredFieldErrorMessage",
                "inp_doc_uid"];
            this.label.type = "text";
        }
        if (type === FormDesigner.main.TypesControl.cell) {
            this.pf = ["type", "colSpan"];
        }
    };
    Properties.prototype.set = function (prop, value) {
        var that = this;
        if (this[prop] === undefined)
            return;
        this[prop].oldestValue = this[prop].oldValue;
        this[prop].oldValue = this[prop].value;
        this[prop].value = value;
        that.onSet(prop, value);
        return this[prop];
    };
    Properties.prototype.get = function () {
        var a = {}, s;
        for (var i = 0; i < this.pf.length; i++) {
            a[this.pf[i]] = this[this.pf[i]];
        }
        //stack invalid properties
        for (var i in a) {
            s = a[i].value;
            if (a[i].disabled === false && a[i].regExp && a[i].regExp.test(s) === false) {
                $.globalInvalidProperties.push("- Property '" + i + "' in the '" + a.type.value + "' type is invalid.");
                break;
            }
        }
        //end stack invalid properties
        this.onGet(a);
        return a;
    };
    Properties.prototype.setNode = function (prop, node) {
        this[prop].node = node;
    };
    Properties.prototype.setDisabled = function (disabled) {
        var dt = this.get();
        for (var i in dt) {
            dt[i].disabled = disabled;
        }
    };
    FormDesigner.extendNamespace('FormDesigner.main.Properties', Properties);
}());
