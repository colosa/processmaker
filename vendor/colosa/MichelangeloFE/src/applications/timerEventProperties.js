(function () {
    PMDesigner.timerEventProperties = function (activity) {
        var that = this,
            evnUid = activity.getID(),
            activityType = activity.getEventMarker(),
            uidProj = PMDesigner.project.id,
            oldValues,
            tmrevn_uid = "",
            dataTimer = "",
            buttonCancel,
            restClientNewTimerEvent,
            buttonSave,
            restClientUpdateTimerEvent,
            timerEventPropertiesWindow,
            showHourlyItems,
            showDailyItems,
            showMonthlyItems,
            showOneDateTimeItems,
            showEveryItems,
            showWaitForItems,
            showWaitUntilItems,
            varshowHourlyItems,
            endDate,
            oneDateTime,
            daysGroup,
            monthsGroup,
            radioGroup,
            dateTimeVariablePicker,
            formTimerEvent,
            getFormData,
            getTimerEventData,
            validateItems,
            domSettings,
            eventType = activity.getEventType(),
            regexDay = new RegExp(/^(((0|1|2)?[0-9])|(3[01]))$/),
            regexHour = new RegExp(/^(((0|1)?[0-9])|(2[0-4]))$/),
            regexMinute = new RegExp(/^([0-5]?[0-9])$/);

        /*window*/
        buttonCancel = new PMUI.ui.Button({
            id: 'cancelTimmerButton',
            text: "Cancel".translate(),
            buttonType: 'error',
            handler: function (event) {
                clickedClose = false;
                formTimerEvent.getField('startDate').controls[0].hideCalendar();
                formTimerEvent.getField('endDate').controls[0].hideCalendar();
                formTimerEvent.getField('oneDateTime').controls[0].hideCalendar();
                formTimerEvent.getField('dateTimeVariablePicker').controls[0].hideCalendar();
                timerEventPropertiesWindow.isDirtyFormScript();
            }
        });

        restClientNewTimerEvent = function (dataToSave) {
            var restClient = new PMRestClient({
                endpoint: 'timer-event',
                typeRequest: 'post',
                data: dataToSave,
                functionSuccess: function (xhr, response) {
                    timerEventPropertiesWindow.close();
                    PMDesigner.msgFlash('Timer Event saved correctly'.translate(), document.body, 'success', 3000, 5);
                },
                functionFailure: function (xhr, response) {
                    PMDesigner.msgWinError(response.error.message);
                    PMDesigner.msgFlash('There are problems updating the Timer Event, please try again.'.translate(), document.body, 'error', 3000, 5);
                }
            });
            restClient.executeRestClient();
        };

        restClientUpdateTimerEvent = function (dataToSave) {
            var restClient = new PMRestClient({
                endpoint: 'timer-event/' + formTimerEvent.getField("tmrevn_uid").getValue(),
                typeRequest: 'update',
                data: dataToSave,
                functionSuccess: function (xhr, response) {
                    timerEventPropertiesWindow.close();
                    PMDesigner.msgFlash('Timer Event saved correctly'.translate(), document.body, 'success', 3000, 5);
                },
                functionFailure: function (xhr, response) {
                    PMDesigner.msgWinError(response.error.message);
                    PMDesigner.msgFlash('There are problems updating the Timer Event, please try again.'.translate(), document.body, 'error', 3000, 5);
                }
            });
            restClient.executeRestClient();
        };

        buttonSave = new PMUI.ui.Button({
            id: 'saveTimmerButton',
            text: "Save".translate(),
            handler: function (event) {
                var i,
                    opt,
                    formData;

                formTimerEvent.getField("hourType").setValue(getData2PMUI(formTimerEvent.html).hourType);
                formTimerEvent.getField("minuteType").setValue(getData2PMUI(formTimerEvent.html).minuteType);
                formTimerEvent.getField("dayType").setValue(getData2PMUI(formTimerEvent.html).dayType);
                if (formTimerEvent.isValid()) {
                    opt = formTimerEvent.getField("radioGroup").getValue();
                    formData = formTimerEvent.getData();
                    switch (opt) {
                        case "1": /*hourly*/
                            dataTimer = {
                                evn_uid: evnUid,
                                tmrevn_option: "HOURLY",
                                tmrevn_start_date: formData.startDate.substring(0, 10),
                                tmrevn_end_date: formTimerEvent.getField("endDate").getValue().substring(0, 10),
                                tmrevn_minute: formData.minuteType.length == 1 ? "0" + formData.minuteType : (formData.minuteType.length == 0 ? "00" : formData.minuteType )
                            };
                            break;
                        case "2": /*daily*/
                            dataTimer = {
                                evn_uid: evnUid,
                                tmrevn_option: "DAILY",
                                tmrevn_start_date: formData.startDate.substring(0, 10),
                                tmrevn_end_date: formTimerEvent.getField("endDate").getValue().substring(0, 10),
                                tmrevn_hour: formData.hourType.length == 1 ? "0" + formData.hourType : (formData.hourType.length == 0 ? "00" : formData.hourType ),
                                tmrevn_minute: formData.minuteType.length == 1 ? "0" + formData.minuteType : (formData.minuteType.length == 0 ? "00" : formData.minuteType ),
                                tmrevn_configuration_data: JSON.parse(formData.daysGroup).map(function (n) {
                                    return Number(n);
                                })
                            };
                            break;
                        case "3": /*monthly*/
                            dataTimer = {
                                evn_uid: evnUid,
                                tmrevn_option: "MONTHLY",
                                tmrevn_start_date: formData.startDate.substring(0, 10),
                                tmrevn_end_date: formTimerEvent.getField("endDate").getValue().substring(0, 10),
                                tmrevn_day: formData.dayType.length == 1 ? "0" + formData.dayType : (formData.dayType.length == 0 ? "00" : formData.dayType ),
                                tmrevn_hour: formData.hourType.length == 1 ? "0" + formData.hourType : (formData.hourType.length == 0 ? "00" : formData.hourType ),
                                tmrevn_minute: formData.minuteType.length == 1 ? "0" + formData.minuteType : (formData.minuteType.length == 0 ? "00" : formData.minuteType ),
                                tmrevn_configuration_data: JSON.parse(formData.monthsGroup).map(function (n) {
                                    return Number(n);
                                })
                            };
                            break;
                        case "4": /*one-date-time*/
                            dataTimer = {
                                evn_uid: evnUid,
                                tmrevn_option: "ONE-DATE-TIME",
                                tmrevn_next_run_date: $("#oneDateTime").find("input:eq(0)").val()
                            };
                            for (var i in ENABLED_FEATURES) {
                                if (ENABLED_FEATURES[i] == 'oq3S29xemxEZXJpZEIzN01qenJUaStSekY4cTdJVm5vbWtVM0d4S2lJSS9qUT0=') {
                                    dataTimer.tmrevn_next_run_date = convertDatetimeToIso8601(dataTimer.tmrevn_next_run_date);
                                }
                            }
                            break;
                        case "5": /*every*/
                            dataTimer = {
                                evn_uid: evnUid,
                                tmrevn_option: "EVERY",
                                tmrevn_hour: formData.hourType.length == 1 ? "0" + formData.hourType : (formData.hourType.length == 0 ? "00" : formData.hourType ),
                                tmrevn_minute: formData.minuteType.length == 1 ? "0" + formData.minuteType : (formData.minuteType.length == 0 ? "00" : formData.minuteType )
                            };
                            break;
                        case "6": /*wait for*/
                            if ((formData.dayType === '' || formData.dayType === '00' || formData.dayType === '0') &&
                                (formData.hourType === '' || formData.hourType === '00' || formData.hourType === '0') &&
                                (formData.minuteType === '' || formData.minuteType === '00' || formData.minuteType === '0')) {
                                PMDesigner.msgWinError("The amount of time entered is not valid. Please fill in at least one of the fields (day, hour, or minute)".translate());
                                return;
                            } else {
                                if (!regexDay.test(formData.dayType) || !regexHour.test(formData.hourType) || !regexMinute.test(formData.minuteType)) {
                                    PMDesigner.msgWinError("The amount of time entered is not valid. Please fill in at least one of the fields (day, hour, or minute)".translate());
                                    return;
                                }
                            }
                            dataTimer = {
                                evn_uid: evnUid,
                                tmrevn_option: "WAIT-FOR",
                                tmrevn_day: formData.dayType.length == 1 ? "0" + formData.dayType : (formData.dayType.length == 0 ? "00" : formData.dayType ),
                                tmrevn_hour: formData.hourType.length == 1 ? "0" + formData.hourType : (formData.hourType.length == 0 ? "00" : formData.hourType ),
                                tmrevn_minute: formData.minuteType.length == 1 ? "0" + formData.minuteType : (formData.minuteType.length == 0 ? "00" : formData.minuteType )
                            };
                            break;
                        case "7": /*wait until specified date time*/
                            dataTimer = {
                                evn_uid: evnUid,
                                tmrevn_option: "WAIT-UNTIL-SPECIFIED-DATE-TIME",
                                tmrevn_configuration_data: $("#dateTimeVariablePicker").find("input:eq(0)").val()
                            };
                            break;
                    }
                    if (formTimerEvent.getField("tmrevn_uid").getValue() == "") {
                        restClientNewTimerEvent(dataTimer);
                    } else {
                        restClientUpdateTimerEvent(dataTimer);
                    }
                }
            },
            buttonType: 'success'
        });

        timerEventPropertiesWindow = new PMUI.ui.Window({
            id: "timerEventPropertiesWindow",
            title: "Timer Event Properties".translate(),
            width: DEFAULT_WINDOW_WIDTH,
            height: DEFAULT_WINDOW_HEIGHT,
            footerItems: [
                buttonCancel,
                buttonSave
            ],
            buttonPanelPosition: "bottom",
            footerAling: "right",
            onBeforeClose: function () {
                clickedClose = true;
                formTimerEvent.getField('startDate').controls[0].hideCalendar();
                formTimerEvent.getField('endDate').controls[0].hideCalendar();
                formTimerEvent.getField('oneDateTime').controls[0].hideCalendar();
                formTimerEvent.getField('dateTimeVariablePicker').controls[0].hideCalendar();
                timerEventPropertiesWindow.isDirtyFormScript();
            }
        });

        timerEventPropertiesWindow.isDirtyFormScript = function () {
            var that = this,
                title = "Timer Event".translate(),
                newValues = getFormData($("#formTimerEvent"));
            if (JSON.stringify(oldValues) !== JSON.stringify(newValues)) {
                var message_window = new PMUI.ui.MessageWindow({
                    id: "cancelMessageTriggers",
                    windowMessageType: 'warning',
                    width: 490,
                    title: title,
                    message: 'Are you sure you want to discard your changes?'.translate(),
                    footerItems: [
                        {
                            text: "No".translate(),
                            handler: function () {
                                message_window.close();
                            },
                            buttonType: "error"
                        },
                        {
                            text: "Yes".translate(),
                            handler: function () {
                                message_window.close();
                                that.close();
                            },
                            buttonType: "success"
                        }
                    ]
                });
                message_window.open();
                message_window.showFooter();
            } else {
                that.close();
            }
        };
        /*end window*/

        /*form*/
        showHourlyItems = function () {
            formTimerEvent.getField('startDate').setVisible(true);
            formTimerEvent.getField('startDate').setRequired(true);
            formTimerEvent.getField('endDateCheckbox').setVisible(true);
            formTimerEvent.getField('endDate').setVisible(true);
            formTimerEvent.getField('oneDateTime').setVisible(false);
            formTimerEvent.getField('oneDateTime').setRequired(false);
            formTimerEvent.getField('daysGroup').setVisible(false);
            formTimerEvent.getField('daysGroup').setRequired(false);
            formTimerEvent.getField('monthsGroup').setVisible(false);
            formTimerEvent.getField('monthsGroup').setRequired(false);
            formTimerEvent.getField('dateTimeVariablePicker').setVisible(false);
            formTimerEvent.getItems()[0].items.get(4).getField('dayType').setVisible(false);
            formTimerEvent.getItems()[0].items.get(4).getField('dayType').setRequired(false);
            formTimerEvent.getItems()[0].items.get(4).getField('hourType').setVisible(false);
            formTimerEvent.getItems()[0].items.get(4).getField('hourType').setRequired(false);
            formTimerEvent.getItems()[0].items.get(4).getField('minuteType').setVisible(true);
            formTimerEvent.getItems()[0].items.get(4).getField('minuteType').setRequired(true);
        };

        showDailyItems = function () {
            formTimerEvent.getField('startDate').setVisible(true);
            formTimerEvent.getField('startDate').setRequired(true);
            formTimerEvent.getField('endDateCheckbox').setVisible(true);
            formTimerEvent.getField('endDate').setVisible(true);
            formTimerEvent.getField('oneDateTime').setVisible(false);
            formTimerEvent.getField('oneDateTime').setRequired(false);
            formTimerEvent.getField('daysGroup').setVisible(true);
            formTimerEvent.getField('daysGroup').setRequired(true);
            formTimerEvent.getField('oneDateTime').setRequired(false);
            formTimerEvent.getField('monthsGroup').setVisible(false);
            formTimerEvent.getField('monthsGroup').setRequired(false);
            formTimerEvent.getField('dateTimeVariablePicker').setVisible(false);
            formTimerEvent.getItems()[0].items.get(4).getField('dayType').setVisible(false);
            formTimerEvent.getItems()[0].items.get(4).getField('dayType').setRequired(false);
            formTimerEvent.getItems()[0].items.get(4).getField('hourType').setVisible(true);
            formTimerEvent.getItems()[0].items.get(4).getField('hourType').setRequired(true);
            formTimerEvent.getItems()[0].items.get(4).getField('minuteType').setVisible(true);
            formTimerEvent.getItems()[0].items.get(4).getField('minuteType').setRequired(true);
        };

        showMonthlyItems = function () {
            formTimerEvent.getField('startDate').setVisible(true);
            formTimerEvent.getField('startDate').setRequired(true);
            formTimerEvent.getField('endDateCheckbox').setVisible(true);
            formTimerEvent.getField('endDate').setVisible(true);
            formTimerEvent.getField('oneDateTime').setVisible(false);
            formTimerEvent.getField('oneDateTime').setRequired(false);
            formTimerEvent.getField('daysGroup').setVisible(false);
            formTimerEvent.getField('daysGroup').setRequired(false);
            formTimerEvent.getField('monthsGroup').setVisible(true);
            formTimerEvent.getField('monthsGroup').setRequired(true);
            formTimerEvent.getField('dateTimeVariablePicker').setVisible(false);
            formTimerEvent.getItems()[0].items.get(4).getField('dayType').setVisible(true);
            formTimerEvent.getItems()[0].items.get(4).getField('dayType').setRequired(true);
            formTimerEvent.getItems()[0].items.get(4).getField('hourType').setVisible(true);
            formTimerEvent.getItems()[0].items.get(4).getField('hourType').setRequired(true);
            formTimerEvent.getItems()[0].items.get(4).getField('minuteType').setVisible(true);
            formTimerEvent.getItems()[0].items.get(4).getField('minuteType').setRequired(true);
        };

        showOneDateTimeItems = function () {
            formTimerEvent.getField('startDate').setVisible(false);
            formTimerEvent.getField('startDate').setRequired(false);
            formTimerEvent.getField('endDateCheckbox').setVisible(false);
            formTimerEvent.getField('endDate').setVisible(false);
            formTimerEvent.getField('oneDateTime').setVisible(true);
            formTimerEvent.getField('oneDateTime').setRequired(true);
            formTimerEvent.getField('daysGroup').setVisible(false);
            formTimerEvent.getField('daysGroup').setRequired(false);
            formTimerEvent.getField('monthsGroup').setVisible(false);
            formTimerEvent.getField('monthsGroup').setRequired(false);
            formTimerEvent.getField('dateTimeVariablePicker').setVisible(false);
            formTimerEvent.getItems()[0].items.get(4).getField('dayType').setVisible(false);
            formTimerEvent.getItems()[0].items.get(4).getField('dayType').setRequired(false);
            formTimerEvent.getItems()[0].items.get(4).getField('hourType').setVisible(false);
            formTimerEvent.getItems()[0].items.get(4).getField('hourType').setRequired(false);
            formTimerEvent.getItems()[0].items.get(4).getField('minuteType').setVisible(false);
            formTimerEvent.getItems()[0].items.get(4).getField('minuteType').setRequired(false);
        };

        showEveryItems = function () {
            formTimerEvent.getField('startDate').setVisible(false);
            formTimerEvent.getField('startDate').setRequired(false);
            formTimerEvent.getField('endDateCheckbox').setVisible(false);
            formTimerEvent.getField('endDate').setVisible(false);
            formTimerEvent.getField('oneDateTime').setVisible(false);
            formTimerEvent.getField('oneDateTime').setRequired(false);
            formTimerEvent.getField('daysGroup').setVisible(false);
            formTimerEvent.getField('daysGroup').setRequired(false);
            formTimerEvent.getField('monthsGroup').setVisible(false);
            formTimerEvent.getField('monthsGroup').setRequired(false);
            formTimerEvent.getField('dateTimeVariablePicker').setVisible(false);
            formTimerEvent.getItems()[0].items.get(4).getField('dayType').setVisible(false);
            formTimerEvent.getItems()[0].items.get(4).getField('dayType').setRequired(false);
            formTimerEvent.getItems()[0].items.get(4).getField('hourType').setVisible(true);
            formTimerEvent.getItems()[0].items.get(4).getField('hourType').setRequired(true);
            formTimerEvent.getItems()[0].items.get(4).getField('minuteType').setVisible(true);
            formTimerEvent.getItems()[0].items.get(4).getField('minuteType').setRequired(true);
        };
        /*intermediate*/
        showWaitForItems = function () {
            formTimerEvent.getField('startDate').setVisible(false);
            formTimerEvent.getField('endDateCheckbox').setVisible(false);
            formTimerEvent.getField('endDate').setVisible(false);
            formTimerEvent.getField('oneDateTime').setVisible(false);
            formTimerEvent.getField('daysGroup').setVisible(false);
            formTimerEvent.getField('daysGroup').setRequired(false);
            formTimerEvent.getField('monthsGroup').setVisible(false);
            formTimerEvent.getField('monthsGroup').setRequired(false);
            formTimerEvent.getField('dateTimeVariablePicker').setVisible(false);
            formTimerEvent.getField('dateTimeVariablePicker').setRequired(false);
            formTimerEvent.getItems()[0].items.get(4).getField('dayType').setVisible(true);
            formTimerEvent.getItems()[0].items.get(4).getField('hourType').setVisible(true);
            formTimerEvent.getItems()[0].items.get(4).getField('minuteType').setVisible(true);
        };

        showWaitUntilItems = function () {
            formTimerEvent.getField('startDate').setVisible(false);
            formTimerEvent.getField('endDateCheckbox').setVisible(false);
            formTimerEvent.getField('endDate').setVisible(false);
            formTimerEvent.getField('oneDateTime').setVisible(false);
            formTimerEvent.getField('daysGroup').setVisible(false);
            formTimerEvent.getField('daysGroup').setRequired(false);
            formTimerEvent.getField('monthsGroup').setVisible(false);
            formTimerEvent.getField('monthsGroup').setRequired(false);
            formTimerEvent.getField('dateTimeVariablePicker').setVisible(true);
            formTimerEvent.getField('dateTimeVariablePicker').setRequired(true);
            formTimerEvent.getItems()[0].items.get(4).getField('dayType').setVisible(false);
            formTimerEvent.getItems()[0].items.get(4).getField('hourType').setVisible(false);
            formTimerEvent.getItems()[0].items.get(4).getField('minuteType').setVisible(false);
        };

        radioGroup = new PMUI.field.RadioButtonGroupField({
            id: 'radioGroup',
            controlPositioning: 'horizontal',
            maxDirectionOptions: 7,
            options: [
                {
                    label: "Hourly".translate(),
                    value: "1"
                },
                {
                    label: "Daily".translate(),
                    value: "2"
                },
                {
                    label: "Monthly".translate(),
                    value: "3"
                },
                {
                    label: "One date/time".translate(),
                    value: "4"
                },
                {
                    label: "Every".translate(),
                    value: "5"
                },
                {
                    label: "Wait for".translate(),
                    value: "6"
                },
                {
                    label: "Wait until specified date/time".translate(),
                    value: "7"
                }

            ],
            onChange: function (newVal, oldVal) {
                switch (newVal) {
                    case "1":
                        showHourlyItems();
                        break;
                    case "2":
                        showDailyItems();
                        break;
                    case "3":
                        showMonthlyItems();
                        break;
                    case "4":
                        showOneDateTimeItems();
                        break;
                    case "5":
                        showEveryItems();
                        break;
                    case "6":
                        showWaitForItems();
                        break;
                    case "7":
                        showWaitUntilItems();
                        break;
                }
            },
            value: "1"
        });

        startDate = new PMUI.field.DateTimeField({
            id: 'startDate',
            label: 'Start date'.translate(),
            datetime: false,
            dateFormat: 'yy-mm-dd',
            firstDay: 1,
            controlsWidth: 100,
            required: false,
            readOnly: true,
            minDate: 0,
            maxDate: 1460
        });

        endDate = new PMUI.field.DateTimeField({
            id: 'endDate',
            label: "End date".translate(),
            value: '',
            disabled: true,
            datetime: false,
            dateFormat: 'yy-mm-dd',
            firstDay: 1,
            controlsWidth: 100,
            required: false,
            readOnly: true,
            minDate: 0,
            maxDate: 1460
        });

        oneDateTime = new PMUI.field.DateTimeField({
            id: 'oneDateTime',
            label: 'Date time'.translate(),
            datetime: true,
            dateFormat: 'yy-mm-dd HH:ii:ss',
            firstDay: 1,
            controlsWidth: 150,
            required: false,
            readOnly: true,
            minDate: 0,
            maxDate: 1460
        });

        daysGroup = new PMUI.field.CheckBoxGroupField({
            label: "Days".translate(),
            id: 'daysGroup',
            controlPositioning: 'vertical',
            maxDirectionOptions: 3,
            required: true,
            options: [
                {
                    label: 'Monday'.translate(),
                    value: 1,
                    name: 'monday',
                    selected: true
                },
                {
                    label: 'Tuesday'.translate(),
                    value: 2,
                    name: 'tuesday',
                    selected: true
                },
                {
                    label: 'Wednesday'.translate(),
                    value: 3,
                    name: 'wednesday',
                    selected: true
                },
                {
                    label: 'Thursday'.translate(),
                    value: 4,
                    name: 'thursday',
                    selected: true
                },
                {
                    label: 'Friday'.translate(),
                    value: 5,
                    name: 'friday',
                    selected: true
                },
                {
                    label: 'Saturday'.translate(),
                    value: 6,
                    name: 'saturday',
                    selected: true
                },
                {
                    label: 'Sunday'.translate(),
                    value: 7,
                    name: 'sunday',
                    selected: true
                }
            ],
            onChange: function (newVal, oldVal) {

            }
        });

        monthsGroup = new PMUI.field.CheckBoxGroupField({
            label: "Months".translate(),
            id: 'monthsGroup',
            controlPositioning: 'vertical',
            maxDirectionOptions: 3,
            required: true,
            options: [
                {
                    label: 'January'.translate(),
                    value: 1,
                    name: 'january',
                    selected: true
                },
                {
                    label: 'February'.translate(),
                    value: 2,
                    selected: true
                },
                {
                    label: 'March'.translate(),
                    value: 3,
                    selected: true
                },
                {
                    label: 'April'.translate(),
                    value: 4,
                    selected: true
                },
                {
                    label: 'May'.translate(),
                    value: 5,
                    selected: true
                },
                {
                    label: 'June'.translate(),
                    value: 6,
                    selected: true
                },
                {
                    label: 'July'.translate(),
                    value: 7,
                    selected: true
                },
                {
                    label: 'August'.translate(),
                    value: 8,
                    selected: true
                },
                {
                    label: 'September'.translate(),
                    value: 9,
                    selected: true
                },
                {
                    label: 'October'.translate(),
                    value: 10,
                    selected: true
                },
                {
                    label: 'November'.translate(),
                    value: 11,
                    selected: true
                },
                {
                    label: 'December'.translate(),
                    value: 12,
                    selected: true
                }
            ],
            onChange: function (newVal, oldVal) {

            }
        });

        dateTimeVariablePicker = new PMUI.field.DateTimeField({
            id: 'dateTimeVariablePicker',
            label: 'Date time'.translate(),
            datetime: true,
            dateFormat: 'yy-mm-dd HH:ii:ss',
            firstDay: 1,
            controlsWidth: 150,
            required: false,
            readOnly: true,
            minDate: 0,
            maxDate: 1460
        });

        formTimerEvent = new PMUI.form.Form({
            id: "formTimerEvent",
            border: true,
            visibleHeader: false,
            width: '900px',
            height: "300px",
            name: "formTimerEvent",
            title: '',
            items: [
                {
                    id: "panelDetailsCustom",
                    pmType: "panel",
                    layout: 'vbox',
                    fieldset: false,
                    height: '380px',
                    legend: "DETAILS".translate(),
                    items: [
                        {
                            id: "evnUid",
                            pmType: "text",
                            value: evnUid,
                            name: "evnUid",
                            readOnly: true,
                            visible: false,
                            valueType: 'string'
                        },
                        {
                            id: "activityType",
                            pmType: "text",
                            value: activityType,
                            name: "activityType",
                            readOnly: true,
                            visible: false,
                            valueType: 'string'
                        },
                        radioGroup,
                        {
                            pmType: "panel",
                            id: "datesPanel",
                            layout: 'hbox',
                            items: [
                                startDate,
                                {
                                    pmType: "checkbox",
                                    id: "endDateCheckbox",
                                    label: "End date".translate(),
                                    controlPositioning: 'vertical',
                                    maxDirectionOptions: 2,
                                    value: '',
                                    options: [
                                        {
                                            label: "End date:".translate(),
                                            disabled: false,
                                            value: '1',
                                            selected: false
                                        }
                                    ],
                                    onChange: function (newVal, oldVal) {
                                        if (newVal[2] == "1") {
                                            $('#endDate').find('input:eq(0)').removeProp("disabled");
                                        } else {
                                            $('#endDate').find('input:eq(0)').val('').attr("disabled", "disabled");
                                            formTimerEvent.getField('endDate').setValue('');
                                        }
                                    }
                                },
                                endDate,
                                oneDateTime,
                                dateTimeVariablePicker
                            ]
                        },
                        {
                            pmType: "panel",
                            id: "dayHourMonthPanel",
                            layout: 'hbox',
                            items: [
                                {
                                    id: "dayType",
                                    label: "Day".translate(),
                                    pmType: "text",
                                    value: "",
                                    name: "dayType",
                                    visible: true,
                                    valueType: 'integer',
                                    controlsWidth: 50,
                                    maxLength: 2
                                },
                                {
                                    id: "hourType",
                                    label: "Hour".translate(),
                                    pmType: "text",
                                    value: "",
                                    name: "hourType",
                                    visible: true,
                                    valueType: 'integer',
                                    controlsWidth: 50,
                                    maxLength: 2
                                },
                                {
                                    id: "minuteType",
                                    label: "Minute".translate(),
                                    pmType: "text",
                                    value: "",
                                    name: "minuteType",
                                    visible: true,
                                    valueType: 'integer',
                                    controlsWidth: 50,
                                    maxLength: 2
                                }
                            ]
                        },
                        daysGroup,
                        monthsGroup,
                        {
                            id: "tmrevn_uid",
                            pmType: "text",
                            value: tmrevn_uid,
                            name: "tmrevn_uid",
                            visible: false,
                            valueType: 'string'
                        }
                    ]
                }
            ]
        });

        formTimerEvent.initialData = function () {
            var formElements = this.getItems()[0],
                datesPanelElements,
                radioGroupValues = {'radioGroup': formElements.items.get(2).getValue()};
            oldValues.push(radioGroupValues);
            datesPanelElements = formElements.items.get(3).getItems();

        };

        getFormData = function ($form) {
            var unindexed_array = $form.serializeArray(),
                indexed_array = {};

            $.map(unindexed_array, function (n, i) {
                indexed_array[n['name']] = n['value'];
            });
            return indexed_array;
        };

        getTimerEventData = function () {
            var restClient = new PMRestClient({
                endpoint: 'timer-event/event/' + formTimerEvent.getField("evnUid").getValue(),
                typeRequest: 'get',
                functionSuccess: function (xhr, response) {
                    if (typeof response === "object" && JSON.stringify(response).length > 2) {
                        var opt = response.tmrevn_option.toUpperCase();
                        switch (opt) {
                            case "HOURLY":
                                $("#radioGroup").find("input:eq(0)").trigger("click");
                                formTimerEvent.getField("tmrevn_uid").setValue(response.tmrevn_uid);
                                formTimerEvent.getField("startDate").setValue(response.tmrevn_start_date);
                                if (response.tmrevn_end_date != "") {
                                    formTimerEvent.getField("endDateCheckbox").setValue('["1"]');
                                    formTimerEvent.getField("endDate").setValue(response.tmrevn_end_date);
                                    formTimerEvent.getField("endDate").enable();
                                }
                                formTimerEvent.getField("minuteType").setValue(response.tmrevn_minute);
                                break;
                            case "DAILY":
                                $("#radioGroup").find("input:eq(1)").trigger("click");
                                formTimerEvent.getField("tmrevn_uid").setValue(response.tmrevn_uid);
                                formTimerEvent.getField("startDate").setValue(response.tmrevn_start_date);
                                if (response.tmrevn_end_date != "") {
                                    formTimerEvent.getField("endDateCheckbox").setValue('["1"]');
                                    formTimerEvent.getField("endDate").setValue(response.tmrevn_end_date);
                                    formTimerEvent.getField("endDate").enable();
                                }
                                formTimerEvent.getField("hourType").setValue(response.tmrevn_hour);
                                formTimerEvent.getField("minuteType").setValue(response.tmrevn_minute);
                                formTimerEvent.getField("daysGroup").setValue("");
                                formTimerEvent.getField("daysGroup").setValue(JSON.stringify(response.tmrevn_configuration_data.map(function (n) {
                                    return n.toString();
                                })));
                                break;
                            case "MONTHLY":
                                $("#radioGroup").find("input:eq(2)").trigger("click");
                                formTimerEvent.getField("tmrevn_uid").setValue(response.tmrevn_uid);
                                formTimerEvent.getField("startDate").setValue(response.tmrevn_start_date);
                                if (response.tmrevn_end_date != "") {
                                    formTimerEvent.getField("endDateCheckbox").setValue('["1"]');
                                    formTimerEvent.getField("endDate").setValue(response.tmrevn_end_date);
                                    formTimerEvent.getField("endDate").enable();
                                }
                                formTimerEvent.getField("dayType").setValue(response.tmrevn_day);
                                formTimerEvent.getField("hourType").setValue(response.tmrevn_hour);
                                formTimerEvent.getField("minuteType").setValue(response.tmrevn_minute);
                                formTimerEvent.getField("monthsGroup").setValue("");
                                formTimerEvent.getField("monthsGroup").setValue(JSON.stringify(response.tmrevn_configuration_data.map(function (n) {
                                    return n.toString();
                                })));
                                break;
                            case "ONE-DATE-TIME":
                                $("#radioGroup").find("input:eq(3)").trigger("click");
                                formTimerEvent.getField("tmrevn_uid").setValue(response.tmrevn_uid);
                                var d = response.tmrevn_next_run_date.replace(/-/g, "/");
                                for (var i in ENABLED_FEATURES) {
                                    if (ENABLED_FEATURES[i] == 'oq3S29xemxEZXJpZEIzN01qenJUaStSekY4cTdJVm5vbWtVM0d4S2lJSS9qUT0=') {
                                        d = response.tmrevn_next_run_date;
                                    }
                                }
                                d = new Date(d);
                                formTimerEvent.getField("oneDateTime").setValue(d);
                                break;
                            case "EVERY":
                                $("#radioGroup").find("input:eq(4)").trigger("click");
                                formTimerEvent.getField("tmrevn_uid").setValue(response.tmrevn_uid);
                                formTimerEvent.getField("hourType").setValue(response.tmrevn_hour);
                                formTimerEvent.getField("minuteType").setValue(response.tmrevn_minute);
                                break;
                            case "WAIT-FOR":
                                $("#radioGroup").find("input:eq(5)").trigger("click");
                                formTimerEvent.getField("tmrevn_uid").setValue(response.tmrevn_uid);
                                formTimerEvent.getField("dayType").setValue(response.tmrevn_day);
                                formTimerEvent.getField("hourType").setValue(response.tmrevn_hour);
                                formTimerEvent.getField("minuteType").setValue(response.tmrevn_minute);
                                break;
                            case "WAIT-UNTIL-SPECIFIED-DATE-TIME":
                                $("#radioGroup").find("input:eq(6)").trigger("click");
                                formTimerEvent.getField("tmrevn_uid").setValue(response.tmrevn_uid);
                                var d = response.tmrevn_configuration_data.replace(/-/g, "/");
                                d = new Date(d);
                                formTimerEvent.getField("dateTimeVariablePicker").setValue(d);
                                break;
                        }
                    } else {
                        if (eventType == "START") {
                            $("#radioGroup").find("input:eq(1)").trigger("click");
                        } else {
                            $("#radioGroup").find("input:eq(5)").trigger("click");
                        }
                    }
                },
                functionFailure: function (xhr, response) {
                    PMDesigner.msgWinError(response.error.message);
                    PMDesigner.msgFlash('There are problems updating the Timer Event, please try again.'.translate(), document.body, 'error', 3000, 5);
                }
            });
            restClient.executeRestClient();
        };

        /*end form*/

        validateItems = function (itemId) {
            var regexTest,
                message,
                valueItem,
                regexTest;

            if (itemId === 'dayType') {
                regexTest = regexDay;
                message = "Error value: Day: 0 - 31".translate();
            } else if (itemId === 'hourType') {
                regexTest = regexHour;
                message = "Error value: Hour: 0 - 23".translate();
            } else if (itemId === 'minuteType') {
                regexTest = regexMinute;
                message = "Error value: Minute: 0 - 59".translate();
            }

            valueItem = $('#' + itemId).find('span input:eq(0)').val();

            if (!regexTest.test(valueItem)) {
                PMDesigner.msgFlash(message, timerEventPropertiesWindow, 'error', 3000, 5);
                $('#' + itemId).find('span input:eq(0)').val('');
                return false;
            }
        };

        domSettings = function () {
            var requiredMessage = $(document.getElementById("requiredMessage"));
            timerEventPropertiesWindow.body.appendChild(requiredMessage[0]);
            requiredMessage[0].style['marginTop'] = '70px';

            timerEventPropertiesWindow.footer.html.style.textAlign = 'right';

            $('#hourType, #dayType, #minuteType').find('span input:eq(0)').bind('blur change', function () {
                validateItems($(this).closest('div').attr('id'));
            });

            $("#dayType").find("input").attr({"type": "number", "maxlength": "2", "min": "0", "max": "31"});
            $("#hourType").find("input").attr({"type": "number", "maxlength": "2", "min": "0", "max": "23"});
            $("#minuteType").find("input").attr({"type": "number", "maxlength": "2", "min": "0", "max": "59"});

            $("#radioGroup").css({"text-align": "center", "margin-bottom": "20px"}).find("label:eq(0)").remove();
            $("#endDateCheckbox").css({"width": "170px", "top": "6px", "left": "28px"}).find("label:eq(0)").remove();
            $("#endDateCheckbox").find("table:eq(0)").css("border", "0px");
            $("#startDate").css("width", "");
            $("#endDate").css("width", "104px").find("label:eq(0)").remove();
            $("#oneDateTime").css("width", "");
            $("#datesPanel").css("text-align", "center").find("label").css({
                "width": "",
                "float": "",
                "text-align": "right"
            });
            $("#dayHourMonthPanel").css("text-align", "center").find("label").css({"float": "", "width": "34.5%"});

            $("#daysGroup").css("text-align", "center").find("label:eq(0)").remove();
            $("#monthsGroup").css("text-align", "center").find("label:eq(0)").remove();
            $("#daysGroup").find("input").each(function () {
                $(this).attr("name", $(this).val());
            });

            $("#dateTimeVariablePicker").css("width", "");


            if (eventType == "START") {
                $(formTimerEvent.getField("radioGroup").controls[0].html).parent().show();
                $(formTimerEvent.getField("radioGroup").controls[1].html).parent().show();
                $(formTimerEvent.getField("radioGroup").controls[2].html).parent().show();
                $(formTimerEvent.getField("radioGroup").controls[3].html).parent().show();
                $(formTimerEvent.getField("radioGroup").controls[4].html).parent().show();
                $(formTimerEvent.getField("radioGroup").controls[5].html).parent().hide();
                $(formTimerEvent.getField("radioGroup").controls[6].html).parent().hide();
                $("#radioGroup").find("input:eq(1)").trigger("click");
            } else {
                $(formTimerEvent.getField("radioGroup").controls[0].html).parent().hide();
                $(formTimerEvent.getField("radioGroup").controls[1].html).parent().hide();
                $(formTimerEvent.getField("radioGroup").controls[2].html).parent().hide();
                $(formTimerEvent.getField("radioGroup").controls[3].html).parent().hide();
                $(formTimerEvent.getField("radioGroup").controls[4].html).parent().hide();
                $(formTimerEvent.getField("radioGroup").controls[5].html).parent().show();
                $(formTimerEvent.getField("radioGroup").controls[6].html).parent().show();
                $("#radioGroup").find("input:eq(5)").trigger("click");
            }
        };

        timerEventPropertiesWindow.addItem(formTimerEvent);
        timerEventPropertiesWindow.open();
        formTimerEvent.eventsDefined = false;
        formTimerEvent.defineEvents();
        timerEventPropertiesWindow.showFooter();
        domSettings();
        getTimerEventData();
        oldValues = getFormData($("#formTimerEvent"));
    };
}());

