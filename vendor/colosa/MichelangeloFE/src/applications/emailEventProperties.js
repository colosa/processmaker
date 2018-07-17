(function () {
    PMDesigner.emailEventProperties = function (activity) {
        var that = this,
            buttonCancel,
            buttonSave,
            emailEventPropertiesWindow,
            emailAcounts,
            emailContent,
            getEmailAccounts,
            formEmailEvent,
            emailWindowTinyMCE,
            getFormData,
            getEmailEventData,
            domSettings,
            activityId = activity.getID(),
            activityType = activity.getEventMarker(),
            uidProj = PMDesigner.project.id,
            initTinyMCE = null,
            oldValues,
            emailEventId = "",
            prf_uid = "",
            ddSize = 21;
        /*options to display in drop down*/

        /*window*/
        buttonCancel = new PMUI.ui.Button({
            id: 'cancelEmailEventsButton',
            text: "Cancel".translate(),
            buttonType: 'error',
            handler: function (event) {
                PMDesigner.hideAllTinyEditorControls();
                clickedClose = false;
                emailEventPropertiesWindow.isDirtyFormScript();
            }
        });

        buttonSave = new PMUI.ui.Button({
            id: 'saveEmailEventsButton',
            text: "Save".translate(),
            handler: function (event) {
                PMDesigner.hideAllTinyEditorControls();
                var dataForm = formEmailEvent.getData();
                if (formEmailEvent.isValid()) {
                    if (dataForm.emailEventId == "") { /*insert*/
                        (new PMRestClient({
                            endpoint: 'file-manager',
                            typeRequest: 'post',
                            messageError: '',
                            data: {
                                prf_filename: "emailEvent_" + new Date().getTime() + ".html",
                                prf_path: "templates",
                                prf_content: dataForm.filecontent
                            },
                            functionSuccess: function (xhr, response) {
                                var restClient;
                                prf_uid = response.prf_uid;
                                if (prf_uid != "" && typeof prf_uid != "undefined") {
                                    restClient = new PMRestClient({
                                        endpoint: 'email-event',
                                        typeRequest: 'post',
                                        data: {
                                            evn_uid: activityId,
                                            email_event_from: $(formEmailEvent.getField('emailAcounts').html).find("select option:selected").text(),
                                            email_event_to: dataForm.ToEmail,
                                            email_event_subject: dataForm.subjectEmail,
                                            email_server_uid: dataForm.emailAcounts,
                                            prf_uid: prf_uid
                                        },
                                        functionSuccess: function () {
                                            emailEventPropertiesWindow.close();
                                            PMDesigner.msgFlash('Email Event saved correctly'.translate(), document.body, 'success', 3000, 5);
                                        },
                                        functionFailure: function (xhr, response) {
                                            PMDesigner.msgWinError(response.error.message);
                                            PMDesigner.msgFlash('There are problems updating the Email Event, please try again.'.translate(), document.body, 'error', 3000, 5);
                                        }
                                    });
                                    restClient.executeRestClient();
                                }
                            },
                            functionFailure: function (xhr, response) {
                                PMDesigner.msgWinError(response.error.message);
                            }
                        })).executeRestClient();


                    } else {
                        (new PMRestClient({
                            endpoint: 'file-manager/' + dataForm.prf_uid,
                            typeRequest: 'update',
                            messageError: '',
                            data: {
                                prf_content: tinyMCE.activeEditor.getContent()
                            },
                            functionSuccess: function (xhr, response) {
                                var restClient;
                                prf_uid = response.prf_uid;
                                if (prf_uid != "" && typeof prf_uid != "undefined") {
                                    restClient = new PMRestClient({
                                        endpoint: 'email-event/' + dataForm.emailEventId,
                                        typeRequest: 'update',
                                        data: {
                                            evn_uid: activityId,
                                            email_event_from: $(formEmailEvent.getField('emailAcounts').html).find("select option:selected").text(),
                                            email_event_to: dataForm.ToEmail,
                                            email_event_subject: dataForm.subjectEmail,
                                            email_server_uid: dataForm.emailAcounts,
                                            prf_uid: prf_uid
                                        },
                                        functionSuccess: function () {
                                            emailEventPropertiesWindow.close();
                                            PMDesigner.msgFlash('Email Event Edited correctly'.translate(), document.body, 'success', 3000, 5);
                                        },
                                        functionFailure: function (xhr, response) {
                                            PMDesigner.msgWinError(response.error.message);
                                            PMDesigner.msgFlash('There are problems Edited the Email Event, please try again.'.translate(), document.body, 'error', 3000, 5);
                                        }
                                    });
                                    restClient.executeRestClient();
                                }
                            },
                            functionFailure: function (xhr, response) {
                                PMDesigner.msgWinError(response.error.message);
                            }
                        })).executeRestClient();
                    }
                }
            },
            buttonType: 'success'
        });

        emailEventPropertiesWindow = new PMUI.ui.Window({
            id: "emailEventPropertiesWindow",
            title: "Email Event Properties".translate(),
            width: DEFAULT_WINDOW_WIDTH,
            height: DEFAULT_WINDOW_HEIGHT,
            footerItems: [
                buttonCancel,
                buttonSave
            ],
            buttonPanelPosition: "bottom",
            footerAling: "right",
            onBeforeClose: function () {
                PMDesigner.hideAllTinyEditorControls();
                clickedClose = true;
                emailEventPropertiesWindow.isDirtyFormScript();
            }
        });

        emailEventPropertiesWindow.isDirtyFormScript = function () {
            var that = this,
                newValues,
                message_window,
                formData = formEmailEvent.getData();
            formData.filecontent = $(tinyMCE.activeEditor.getContent()).text().trim().length ? tinyMCE.activeEditor.getContent() : "";
            newValues = formData;
            if (JSON.stringify(oldValues) !== JSON.stringify(newValues)) {
                message_window = new PMUI.ui.MessageWindow({
                    id: "cancelMessageTriggers",
                    windowMessageType: 'warning',
                    width: 490,
                    title: "Email Event".translate(),
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
        emailAcounts = new PMUI.field.DropDownListField({
            id: "emailAcounts",
            name: "emailAcounts",
            label: "From".translate(),
            options: null,
            controlsWidth: 400,
            required: true,
            labelWidth: "15%",
            onChange: function (newValue, prevValue) {
                var uidTri = newValue, i;
                for (i = 0; i < triggerSelectedData.length; i += 1) {
                    if (triggerSelectedData[i].tri_uid == uidTri) {
                        formScriptTask.getItems()[1].controls[0].cm.setValue(triggerSelectedData[i].tri_webbot);
                        oldValue = triggerSelectedData[i].tri_webbot;
                    }
                }
            }
        });
        emailContent = new PMUI.field.TextAreaField({
            id: 'filecontent',
            name: 'filecontent',
            label: 'Content'.translate(),
            required: true,
            value: '',
            rows: 210,
            labelWidth: "15%",
            controlsWidth: 720,
            onChange: function (currentValue, previousValue) {
            },
            style: {cssClasses: ['mafe-textarea-resize']}
        });
        getEmailAccounts = function (emailAcounts) {
            var restClient = new PMRestClient({
                endpoint: 'email-event/accounts/emailServer',
                typeRequest: 'get',
                functionSuccess: function (xhr, response) {
                    var i, arrayOptions;
                    triggerSelectedData = response;
                    arrayOptions = [];
                    arrayOptions[0] = {
                        label: "- Select an email account -".translate(),
                        value: "",
                        disabled: true,
                        selected: true
                    };
                    for (i = 0; i <= triggerSelectedData.length - 1; i += 1) {
                        arrayOptions.push(
                            {
                                value: triggerSelectedData[i].uid,
                                label: triggerSelectedData[i].email
                            }
                        );
                    }
                    emailAcounts.setOptions(arrayOptions);
                    emailAcounts.setValue(arrayOptions[0].value);
                },
                functionFailure: function (xhr, response) {
                    PMDesigner.msgWinError(response.error.message);
                },
                messageError: "There are problems getting the Triggers list, please try again.".translate()
            });
            restClient.executeRestClient();
        };
        formEmailEvent = new PMUI.form.Form({
            id: "formEmailEvent",
            border: true,
            visibleHeader: false,
            width: '900px',
            height: "300px",
            name: "formEmailEvent",
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
                            id: "activityId",
                            pmType: "text",
                            value: activityId,
                            name: "activityId",
                            visible: false,
                            valueType: 'string'
                        },
                        {
                            id: "activityType",
                            pmType: "text",
                            value: activityType,
                            name: "activityType",
                            visible: false,
                            valueType: 'string'
                        },
                        emailAcounts,
                        {
                            id: "ToEmail",
                            pmType: "text",
                            helper: "The email can be a string or a variable (@@myEmail), comma separated list of emails".translate(),
                            label: "To".translate(),
                            controlsWidth: 400,
                            value: "",
                            name: "ToEmail",
                            required: true,
                            visible: true,
                            labelWidth: "15%",
                            valueType: 'string'
                        },
                        new CriteriaField({
                            id: 'subjectEmail',
                            pmType: 'text',
                            label: "Subject".translate(),
                            controlsWidth: 400,
                            value: "",
                            name: "subjectEmail",
                            required: false,
                            visible: true,
                            labelWidth: "15%",
                            valueType: 'string'
                        }),
                        emailContent,
                        {
                            id: "emailEventId",
                            pmType: "text",
                            value: emailEventId,
                            name: "emailEventId",
                            visible: false,
                            valueType: 'string'
                        },
                        {
                            id: "prf_uid",
                            pmType: "text",
                            value: prf_uid,
                            name: "prf_uid",
                            visible: false,
                            valueType: 'string'
                        }
                    ]
                }
            ]
        });
        emailWindowTinyMCE = function () {
            initTinyMCE = function () {
                tinyMCE.activeEditor.domainURL = "/sys" + WORKSPACE + "/" + LANG + "/" + SKIN + "/";
                tinyMCE.activeEditor.processID = PMDesigner.project.id;
            };
            formEmailEvent.getField('filecontent').getControls()[0].getHTML().className = 'tmceEditor';
            applyStyleWindowForm(emailEventPropertiesWindow);
            tinyMCE.init({
                editor_selector: 'tmceEditor',
                mode: 'specific_textareas',
                directionality: 'ltr',
                verify_html: false,
                skin: 'o2k7',
                theme: 'advanced',
                skin_variant: 'silver',
                relative_urls : false,
                remove_script_host : false,
                plugins: 'advhr,advimage,advlink,advlist,autolink,autoresize,contextmenu,directionality,emotions,example,example_dependency,fullpage,fullscreen,iespell,inlinepopups,insertdatetime,layer,legacyoutput,lists,media,nonbreaking,noneditable,pagebreak,paste,preview,print,save,searchreplace,style,tabfocus,table,template,visualblocks,visualchars,wordcount,xhtmlxtras,pmSimpleUploader,pmVariablePicker,style',
                theme_advanced_buttons1: 'pmSimpleUploader,|,pmVariablePicker,|,bold,italic,underline,|,justifyleft,justifycenter,justifyright,justifyfull,|,fontselect,fontsizeselect,|,cut,copy,paste',
                theme_advanced_buttons2: 'bullist,numlist,|,outdent,indent,blockquote,|,tablecontrols,|,undo,redo,|,link,unlink,image,|,forecolor,backcolor,styleprops',
                theme_advanced_buttons3: 'hr,removeformat,visualaid,|,sub,sup,|,ltr,rtl,|,code',
                popup_css: "/js/tinymce/jscripts/tiny_mce/themes/advanced/skins/default/dialogTinyBpmn.css",
                oninit: initTinyMCE,
                onchange_callback: function (inst) {
                    formEmailEvent.getField('filecontent').setValue(tinyMCE.activeEditor.getContent({format: 'raw'}));
                },
                handle_event_callback: function (e) {
                },
                setup: function (ed) {
                    ed.onSetContent.add(function (ed, l) {
                        formEmailEvent.getField('filecontent').setValue(tinyMCE.activeEditor.getContent({format: 'raw'}));
                    });
                }
            });
        };
        getFormData = function ($form) {
            var unindexed_array = $form.serializeArray(),
                indexed_array = {};

            $.map(unindexed_array, function (n, i) {
                indexed_array[n['name']] = n['value'];
            });
            return indexed_array;
        };
        getEmailEventData = function () {
            var restClient = new PMRestClient({
                endpoint: 'email-event/' + activityId,
                typeRequest: 'get',
                functionSuccess: function (xhr, response) {
                    var valFrom;
                    if (typeof response == "object") {
                        emailEventId = response.email_event_uid;
                        if (emailEventId != "" && typeof emailEventId != "undefined") {
                            formEmailEvent.getField('emailEventId').setValue(response.email_event_uid);

                            valFrom = $(formEmailEvent.getField('emailAcounts').html).find("select option").filter(function () {
                                return this.text == response.email_event_from;
                            }).val();

                            if (valFrom != "" && typeof valFrom != "undefined") {
                                formEmailEvent.getField('emailAcounts').setValue(valFrom);
                            }

                            formEmailEvent.getField('subjectEmail').setValue(response.email_event_subject);
                            formEmailEvent.getField('ToEmail').setValue(response.email_event_to);

                            formEmailEvent.getField('prf_uid').setValue(response.prf_uid);

                            (new PMRestClient({
                                endpoint: 'file-manager',
                                typeRequest: 'get',
                                messageError: '',
                                data: {
                                    path: "templates"
                                },
                                functionSuccess: function (xhr, response) {
                                    for (var i = 0; i < response.length; i += 1) {
                                        if (response[i].prf_uid == formEmailEvent.getField('prf_uid').getValue()) {
                                            formEmailEvent.getField('filecontent').setValue(response[i].prf_content);
                                            if (!$(tinyMCE.activeEditor.getContent()).text().trim().length) {
                                                tinyMCE.activeEditor.setContent(response[i].prf_content);
                                            }
                                            break;
                                        }
                                    }
                                },
                                functionFailure: function (xhr, response) {
                                    PMDesigner.msgWinError(response.error.message);
                                }
                            })).executeRestClient();
                        }
                    }
                },
                functionFailure: function (xhr, response) {
                    PMDesigner.msgWinError(response.error.message);
                }
            });
            restClient.executeRestClient();
        };

        getEmailAccounts(emailAcounts);

        /*end form*/

        this.deleteEmailEventByEventUid = function () {
            (new PMRestClient({
                endpoint: 'email-event/by-event/' + activityId,
                typeRequest: 'remove',
                messageError: '',
                functionSuccess: function (xhr, response) {
                },
                functionFailure: function (xhr, response) {
                }
            })).executeRestClient();
            return this;
        };

        domSettings = function () {
            emailEventPropertiesWindow.footer.html.style.textAlign = 'right';
            $("#emailAcounts").find("select:eq(0)").css("height", "auto").attr({
                "onmousedown": "if(this.options.length>" + ddSize + "){this.size=" + ddSize + ";}",
                "onchange": "this.size=0;",
                "onblur": "this.size=0;"
            });
        };
        emailEventPropertiesWindow.addItem(formEmailEvent);
        emailEventPropertiesWindow.open();
        emailEventPropertiesWindow.showFooter();
        emailWindowTinyMCE();
        domSettings();
        getEmailEventData();
        oldValues = formEmailEvent.getData();
    };
}());

