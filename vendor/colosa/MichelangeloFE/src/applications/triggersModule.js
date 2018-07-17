(function () {
    $ctrlSpaceMessage = $("<p class='ctrlSpaceMessage'>" + "Press".translate() + " <strong>ctrl+space</strong> " + "to get the function list".translate() + ".</p>");
    var btnCopyImport, openFormCustom, formEditTriggerCustom;
    var triggerOriginDataForUpdate = {};
    PMDesigner.trigger = function (event) {
        var listTriggers,
            clickedClose = true,
            message_ErrorWindow,
            flagChanges,
            isDirtyFormTrigger,
            gridListTrigger,
            getListTrigger,
            addNumberTriggerWizard,
            newTriggerOptions,
            allTreesItems,
            buttonNew,
            buttonCopy,
            buttonWizard,
            openListTriggers,
            openFormWizard,
            openFormWizardEditMode,
            openFormCopy,
            openFormEditCode,
            openNewTriggerOptions,
            listProjects,
            getListProjects,
            triggerSelectedData,
            formCopyTrigger,
            codMirr,
            formEditTriggerWizard,
            sepInputs,
            labelInputs,
            paramPanel,
            sepOutputs,
            labelOutputs,
            returnPanel,
            parameterItems,
            returnItems,
            treeNewTrigger,
            accordionNewTrigger,
            updateCustom,
            checkIfValuesAreEqual,
            buttonSave,
            copyButton,
            applyButton,
            btnEditSourceCode,
            buttonCancel,
            editCode,
            formEditCode,
            triggerWindow,
            formcustom;

        message_ErrorWindow = new PMUI.ui.MessageWindow({
            id: "errorMessage",
            width: 490,
            windowMessageType: 'error',
            title: "Triggers".translate(),
            message: "This is an error message.".translate(),
            footerItems: [
                {
                    text: 'OK'.translate(),
                    handler: function () {
                        message_ErrorWindow.close();
                    },
                    buttonType: "success"
                }
            ]
        });

        isDirtyFormTrigger = function () {
            $("input,select,textarea").blur();
            $("div.pmui-window-body.pmui-background").css('overflow', '');
            var formcustom = triggerWindow.getItems()[1];
            var formwizard = triggerWindow.getItems()[2];
            var formcopy = triggerWindow.getItems()[3];
            var formeditcode = triggerWindow.getItems()[4];
            var accordionnewtrigger = triggerWindow.getItems()[5];
            var flag = false;

            var finalData = {
                tri_uid: getData2PMUI(formEditTriggerCustom.html).uid,
                tri_title: getData2PMUI(formEditTriggerCustom.html).title,
                tri_description: getData2PMUI(formEditTriggerCustom.html).description,
                tri_webbot: getData2PMUI(formEditTriggerCustom.html).code
            };

            if (formcustom.isVisible() == true) {
                flag = (checkIfValuesAreEqual(triggerOriginDataForUpdate, finalData)) ? false : true;
            } else if (formwizard.isVisible() == true) {
                flag = formEditTriggerWizard.isDirty();
            } else if (formcopy.isVisible() == true) {
                flag = formCopyTrigger.isDirty();
                if (!flag) {
                    $('.ctrlSpaceMessage').remove();
                }
            } else if (formeditcode.isVisible() == true) {
                flag = formeditcode.isDirty();
                var flagFormCustom = (checkIfValuesAreEqual(triggerOriginDataForUpdate, finalData)) ? false : true;
                if (!flag && formeditcode.getItems()[0].controls[0].value != "" || flagFormCustom) {
                    flag = true;
                }
            }

            if (flag == true) {
                var message_window = new PMUI.ui.MessageWindow({
                    id: "cancelMessageTriggers",
                    title: "Triggers".translate(),
                    windowMessageType: 'warning',
                    width: 490,
                    message: 'Are you sure you want to discard your changes?'.translate(),
                    footerItems: [
                        {
                            text: "No".translate(),
                            handler: function () {
                                message_window.close();
                            },
                            buttonType: "error"
                        }, {
                            text: "Yes".translate(),
                            handler: function () {
                                triggerOriginDataForUpdate = {};
                                var formcustom = triggerWindow.getItems()[1];
                                var formwizard = triggerWindow.getItems()[2];
                                var formcopy = triggerWindow.getItems()[3];
                                var formeditcode = triggerWindow.getItems()[4];

                                if (formcustom.isVisible() == true) {
                                    var uidInForm = formEditTriggerCustom.getFields()[0].getValue();
                                    formEditTriggerCustom.reset();
                                    openListTriggers();
                                } else if (formwizard.isVisible() == true) {
                                    var uidInForm = formEditTriggerWizard.getFields()[0].getValue();
                                    formEditTriggerWizard.reset();
                                    openListTriggers();
                                } else if (formcopy.isVisible() == true) {
                                    $('.ctrlSpaceMessage').remove();
                                    formCopyTrigger.reset();
                                    openListTriggers();
                                } else if (formeditcode.isVisible() == true) {
                                    formeditcode.reset();
                                    openFormCustom("Edit".translate());
                                }
                                message_window.close();
                                if (clickedClose) {
                                    triggerWindow.close();
                                }
                            },
                            buttonType: "success"
                        }
                    ]
                });
                message_window.open();
                message_window.showFooter();
            } else {
                var formcustom = triggerWindow.getItems()[1];
                var formwizard = triggerWindow.getItems()[2];
                var formcopy = triggerWindow.getItems()[3];
                var formeditcode = triggerWindow.getItems()[4];
                var accordionnewtrigger = triggerWindow.getItems()[5];

                if (formcustom.isVisible() == true) {
                    var uidInForm = formEditTriggerCustom.getFields()[0].getValue();
                    formEditTriggerCustom.reset();
                    openListTriggers();
                } else if (formwizard.isVisible() == true) {
                    var uidInForm = formEditTriggerWizard.getFields()[0].getValue();
                    formEditTriggerWizard.reset();
                    openListTriggers();
                } else if (formcopy.isVisible() == true) {
                    formCopyTrigger.reset();
                    openListTriggers();
                } else if (formeditcode.isVisible() == true) {
                    formeditcode.reset();
                    openFormCustom("Edit".translate());
                } else if (accordionnewtrigger.isVisible() == true) {
                    openListTriggers();
                }
                if (clickedClose) {
                    triggerWindow.close();
                }
            }
        };
        //GRID List Case Scheduler
        gridListTrigger = new PMUI.grid.GridPanel({
            id: "listTriggers",
            pageSize: 10,
            width: "96%",
            style: {
                cssClasses: ["mafe-gridPanel"]
            },
            filterPlaceholder: 'Search ...'.translate(),
            nextLabel: 'Next'.translate(),
            previousLabel: 'Previous'.translate(),
            tableContainerHeight: 374,
            customStatusBar: function (currentPage, pageSize, numberItems, criteria, filter) {
                return messagePageGrid(currentPage, pageSize, numberItems, criteria, filter);
            },
            columns: [
                {
                    id: 'listTriggersButtonShow',
                    title: '',
                    dataType: 'button',
                    searchable: false,
                    buttonLabel: 'Show ID'.translate(),
                    buttonStyle: {
                        cssClasses: [
                            'mafe-button-show'
                        ]
                    },
                    id: 'copyuid',
                    onButtonClick: function (row, grid) {
                        var dataRow = row.getData();
                        messageWindow = new PMUI.ui.MessageWindow({
                            id: 'dynaformMessageWindowUID',
                            windowMessageType: 'info',
                            width: 490,
                            title: "Triggers".translate(),
                            message: dataRow.tri_uid,
                            footerItems: [
                                {
                                    text: 'OK'.translate(),
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
                    }
                },
                {
                    title: 'Title'.translate(),
                    id: "title",
                    dataType: 'string',
                    width: "407px",
                    visible: true,
                    columnData: "tri_title",
                    searcheable: true,
                    sortable: true,
                    alignmentCell: 'left'
                },
                {
                    title: 'Type'.translate(),
                    id: "type",
                    dataType: 'string',
                    width: "225px",
                    visible: true,
                    alignmentCell: 'left',
                    columnData: function (data) {
                        if (typeof(data.tri_param) == "object" && typeof(data.tri_param.params) == "object") {
                            var wizardData = data.tri_param.params;

                            return wizardData.LIBRARY_NAME;
                        } else {
                            return "Custom";
                        }
                    },
                    searcheable: true,
                    sortable: true
                },
                {
                    id: "editButton",
                    title: '',
                    dataType: 'button',
                    messageTooltip: "Edit".translate(),
                    buttonLabel: "Edit".translate(),
                    buttonStyle: {
                        cssClasses: [
                            'mafe-button-edit'
                        ]
                    },
                    onButtonClick: function (row, grid) {
                        triggerOriginDataForUpdate = {};
                        var data = row.getData();
                        triggerOriginDataForUpdate = data;
                        var fields;
                        if (data.tri_param == "") {

                            openFormCustom("Edit".translate());
                            formEditTriggerCustom.showFooter();

                            var codeMirrorControl, cmControlCopyTrigger;
                            codeMirrorControl = formEditTriggerCustom.getItems()[1].controls[0].cm;

                            if (codeMirrorControl != undefined) {
                                codeMirrorControl.setSize(580, 160);
                            }
                            formEditTriggerCustom.getItems()[1].setHeight(170);
                            fields = formEditTriggerCustom.getItems()[0];
                            fields.getItems()[0].setValue(data.tri_uid);
                            fields.getItems()[1].setValue(data.tri_title);
                            fields.getItems()[2].setValue(data.tri_description);
                            formEditTriggerCustom.getItems()[1].setValue(data.tri_webbot);

                            codeMirrorControl.setValue(data.tri_webbot);
                            codeMirrorControl.refresh();
                        } else {
                            openFormWizardEditMode();

                            var wizardData = data.tri_param.params;

                            var lib = wizardData.LIBRARY_CLASS;
                            lib = lib.split('.');
                            lib = lib[1];
                            fields = formEditTriggerWizard.getFields();
                            fields[0].setValue(data.tri_uid);
                            fields[1].setValue(lib);
                            fields[2].setValue(wizardData.PMFUNTION_NAME);
                            fields[3].setValue(wizardData.LIBRARY_NAME + " (" + wizardData.PMFUNTION_LABEL + ")");
                            fields[4].setValue(data.tri_title);
                            fields[5].setValue(data.tri_description);
                            fields[6].setValue(data.tri_webbot);

                            var inputItems = [];
                            var outputItems = [];

                            restClient = new PMRestClient({
                                endpoint: "trigger-wizard/" + lib + "/" + wizardData.PMFUNTION_NAME,
                                typeRequest: 'get',
                                functionSuccess: function (xhr, response) {
                                    if (response.fn_params.input != undefined) {
                                        for (j = 0; j < response.fn_params.input.length; j++) {
                                            var nameInput = response.fn_params.input[j].name;

                                            var inp = new CriteriaField({
                                                id: nameInput,
                                                pmType: "text",
                                                name: nameInput,
                                                label: response.fn_params.input[j].label,
                                                value: wizardData[nameInput].toString(),
                                                controlsWidth: 400,
                                                labelWidth: '27%',
                                                helper: response.fn_params.input[j].description,
                                                required: response.fn_params.input[j].name === "unpauseDate" ? true : response.fn_params.input[j].required
                                            });

                                            inputItems.push(inp);
                                        }
                                    }
                                    if (response.fn_params.output != undefined) {
                                        for (k = 0; k < response.fn_params.output.length; k++) {
                                            var nameOutput = response.fn_params.output[k].name;

                                            var out = new CriteriaField({
                                                id: nameOutput,
                                                pmType: "text",
                                                name: nameOutput,
                                                label: response.fn_params.output[k].label,
                                                value: wizardData.TRI_ANSWER.toString(),
                                                controlsWidth: 400,
                                                labelWidth: '27%',
                                                helper: response.fn_params.output[k].description,
                                                required: response.fn_params.output[k].required
                                            });

                                            outputItems.push(out);
                                        }
                                    }
                                    triggerWindow.setTitle("Edit".translate() + " " + wizardData.LIBRARY_NAME);
                                    if (inputItems.length > 0) {
                                        formEditTriggerWizard.getItems()[1].setVisible(true);
                                        formEditTriggerWizard.getItems()[2].setVisible(true);
                                        formEditTriggerWizard.getItems()[3].setVisible(true);
                                        formEditTriggerWizard.getItems()[3].clearItems();
                                        formEditTriggerWizard.getItems()[3].setItems(inputItems);
                                    } else {
                                        formEditTriggerWizard.getItems()[1].setVisible(false);
                                        formEditTriggerWizard.getItems()[2].setVisible(false);
                                        formEditTriggerWizard.getItems()[3].setVisible(false);
                                        formEditTriggerWizard.getItems()[3].clearItems();
                                    }
                                    if (outputItems.length > 0) {
                                        formEditTriggerWizard.getItems()[4].setVisible(true);
                                        formEditTriggerWizard.getItems()[5].setVisible(true);
                                        formEditTriggerWizard.getItems()[6].setVisible(true);
                                        formEditTriggerWizard.getItems()[6].clearItems();
                                        formEditTriggerWizard.getItems()[6].setItems(outputItems);
                                    } else {
                                        formEditTriggerWizard.getItems()[4].setVisible(false);
                                        formEditTriggerWizard.getItems()[5].setVisible(false);
                                        formEditTriggerWizard.getItems()[6].setVisible(false);
                                        formEditTriggerWizard.getItems()[6].clearItems();
                                    }
                                    labelInputs.dom.labelTextContainer.style.display = "none";
                                    labelOutputs.dom.labelTextContainer.style.display = "none";
                                },
                                functionFailure: function (xhr, response) {
                                    PMDesigner.msgWinError(response.error.message);
                                },
                                messageError: "There are problems getting the triggers wizard list, please try again.".translate()
                            });
                            restClient.executeRestClient();
                        }
                    }
                },
                {
                    id: "deleteButton",
                    title: '',
                    dataType: 'button',
                    messageTooltip: "Delete".translate(),
                    buttonLabel: "Delete".translate(),
                    buttonStyle: {
                        cssClasses: [
                            'mafe-button-delete'
                        ]
                    },
                    onButtonClick: function (row, grid) {
                        var questionWindow, dataRow = row.getData();

                        questionWindow = new PMUI.ui.MessageWindow({
                            id: "questionDeleteWindow",
                            title: "Triggers".translate(),
                            windowMessageType: 'warning',
                            width: 490,
                            message: "Do you want to delete this Trigger?".translate(),
                            footerItems: [
                                {
                                    id: 'questionDeleteWindowButtonNo',
                                    text: "No".translate(),
                                    visible: true,
                                    handler: function () {
                                        questionWindow.close();
                                    },
                                    buttonType: "error"
                                }, {
                                    id: 'questionDeleteWindowButtonYes',
                                    text: "Yes".translate(),
                                    visible: true,
                                    handler: function () {
                                        questionWindow.close();
                                        restClient = new PMRestClient({
                                            endpoint: "trigger/" + dataRow.tri_uid,
                                            typeRequest: 'remove',
                                            functionSuccess: function (xhr, response) {
                                                grid.removeItem(row);
                                            },
                                            functionFailure: function (xhr, response) {
                                                PMDesigner.msgWinError(response.error.message);
                                            },
                                            messageSuccess: "Trigger deleted successfully".translate(),
                                            flashContainer: gridListTrigger
                                        });
                                        restClient.executeRestClient();
                                    },
                                    buttonType: "success"
                                },
                            ]
                        });

                        questionWindow.open();
                        questionWindow.dom.titleContainer.style.height = "17px";
                        questionWindow.showFooter();
                    }
                }
            ],
            dataItems: listTriggers,
            onRowClick: function (row, data) {

            }
        });

        getListTrigger = function () {
            var restClient = new PMRestClient({
                endpoint: 'triggers',
                typeRequest: 'get',
                functionSuccess: function (xhr, response) {
                    listTriggers = response;
                    gridListTrigger.setDataItems(listTriggers);
                },
                functionFailure: function (xhr, response) {
                    PMDesigner.msgWinError(response.error.message);
                },
                messageError: "There are problems getting the Triggers list, please try again.".translate()
            });
            restClient.executeRestClient();
        };

        addNumberTriggerWizard = function (value, position) {
            coutTriggers = document.createElement("span");
            coutTriggers.textContent = value;
            headerAccordion = triggerWindow.getItems()[5].getItems()[position].header.html;
            headerAccordion.appendChild(coutTriggers);
            coutTriggers.style.float = "right";
            coutTriggers.style.color = "white";
            coutTriggers.style.fontSize = "13px";
            coutTriggers.style.marginRight = "6px";
            coutTriggers.style.marginTop = "3px";
        };

        newTriggerOptions = function () {

            var acItemCustom = accordionNewTrigger.getItems()[0];
            var acItemCopy = accordionNewTrigger.getItems()[1];
            acItemCustom.setIconClosed('pmcustomtrigger');
            acItemCustom.setIconExpanded('pmcustomtrigger');
            acItemCopy.setIconClosed('pmcopytrigger');
            acItemCopy.setIconExpanded('pmcopytrigger');

            var itemCustom = jQuery("#custom > .pmui-accordion-item-header > span")[1];
            itemCustom.style.width = "0px";
            var itemCopy = jQuery("#copy > .pmui-accordion-item-header > span")[1];
            itemCopy.style.width = "0px";

            var numOfItems = accordionNewTrigger.getItems();

            var lengthLibFunctions = [];
            if (numOfItems.length <= 2) {
                restClient = new PMRestClient({
                    endpoint: "trigger-wizards",
                    typeRequest: 'get',
                    functionSuccess: function (xhr, response) {
                        for (i = 0; i < response.length; i++) {
                            var functTree = response[i].lib_functions;
                            lengthLibFunctions[i] = functTree.length;
                            var newTreeItems = [];
                            for (h = 0; h < functTree.length; h++) {
                                newTreeItems.push({
                                    label: functTree[h].fn_label,
                                    id: functTree[h].fn_name,
                                    onClick: function () {
                                        for (s = 0; s < response.length; s++) {
                                            for (p = 0; p < response[s].lib_functions.length; p++) {
                                                if (response[s].lib_functions[p].fn_name == this.id) {
                                                    var fn = response[s].lib_functions[p].fn_params;
                                                    var fnLabel = response[s].lib_functions[p].fn_label;
                                                    var fnName = response[s].lib_functions[p].fn_name;
                                                    var libName = response[s].lib_name;
                                                    var libTitle = response[s].lib_title;
                                                    p = response[s].lib_functions.length;
                                                    s = response.length - 1;
                                                }
                                            }
                                        }
                                        var inputItems = [];
                                        if (fn.input != undefined) {
                                            for (j = 0; j < fn.input.length; j++) {
                                                var val = "";
                                                if (fn.input[j].type == "array") {
                                                    val = "array('')";
                                                }
                                                var inp = new CriteriaField({
                                                    id: fn.input[j].name,
                                                    pmType: "text",
                                                    name: fn.input[j].name,
                                                    label: fn.input[j].label,
                                                    controlsWidth: 400,
                                                    labelWidth: '27%',
                                                    value: val,
                                                    helper: fn.input[j].description,
                                                    required: fn.input[j].name === "unpauseDate" ? true : fn.input[j].required
                                                });

                                                inputItems.push(inp);
                                            }
                                        }

                                        var outputItems = [];
                                        if (fn.output != undefined) {
                                            for (k = 0; k < fn.output.length; k++) {
                                                var out = new CriteriaField({
                                                    id: fn.output[k].name,
                                                    pmType: "text",
                                                    name: fn.output[k].name,
                                                    label: fn.output[k].label,
                                                    controlsWidth: 400,
                                                    labelWidth: '27%',
                                                    helper: fn.output[k].description,
                                                    required: fn.output[k].required
                                                });

                                                outputItems.push(out);
                                            }
                                        }
                                        formEditTriggerWizard.reset();
                                        formEditTriggerWizard.setTitle(fnLabel + ' (' + fnName + ')');
                                        formEditTriggerWizard.getItems()[0].getItems()[1].setValue(libName);
                                        formEditTriggerWizard.getItems()[0].getItems()[2].setValue(fnName);
                                        formEditTriggerWizard.getItems()[0].getItems()[3].setValue(libTitle + ' (' + fnLabel + ')');

                                        if (inputItems.length > 0) {
                                            formEditTriggerWizard.getItems()[1].setVisible(true);
                                            formEditTriggerWizard.getItems()[2].setVisible(true);
                                            formEditTriggerWizard.getItems()[3].setVisible(true);
                                            formEditTriggerWizard.getItems()[3].clearItems();
                                            formEditTriggerWizard.getItems()[3].setItems(inputItems);
                                        } else {
                                            formEditTriggerWizard.getItems()[1].setVisible(false);
                                            formEditTriggerWizard.getItems()[2].setVisible(false);
                                            formEditTriggerWizard.getItems()[3].setVisible(false);
                                            formEditTriggerWizard.getItems()[3].clearItems();
                                        }
                                        if (outputItems.length > 0) {
                                            formEditTriggerWizard.getItems()[4].setVisible(true);
                                            formEditTriggerWizard.getItems()[5].setVisible(true);
                                            formEditTriggerWizard.getItems()[6].setVisible(true);
                                            formEditTriggerWizard.getItems()[6].clearItems();
                                            formEditTriggerWizard.getItems()[6].setItems(outputItems);
                                        } else {
                                            formEditTriggerWizard.getItems()[4].setVisible(false);
                                            formEditTriggerWizard.getItems()[5].setVisible(false);
                                            formEditTriggerWizard.getItems()[6].setVisible(false);
                                            formEditTriggerWizard.getItems()[6].clearItems();
                                        }
                                        openFormWizard();
                                        $('.pmui-pmseparatorlinefield .pmui-field-label').css({display: 'none'});
                                        triggerWindow.setTitle("Create".translate() + " " + libTitle);
                                    }
                                });
                            }
                            labelInputs.dom.labelTextContainer.style.display = "none";
                            labelOutputs.dom.labelTextContainer.style.display = "none";
                            allTreesItems[i] = newTreeItems;

                            var arrayTriggerWizards = [
                                "pmFunctions",
                                "pmSugar",
                                "pmTalend",
                                "pmTrAlfresco",
                                "pmTrSharepoint",
                                "pmZimbra"
                            ];

                            if (arrayTriggerWizards.indexOf(response[i].lib_name) == -1) {
                                $("head").append("<style type=\"text/css\">." + response[i].lib_name + " {background: url(" + response[i].lib_icon + ") no-repeat 50% 50%;}</style>");
                            }

                            //Accordion
                            var newAccordionItem = [];
                            newAccordionItem = {
                                iconClass: response[i].lib_name,
                                id: response[i].lib_name,
                                title: response[i].lib_title,
                                height: 'auto'
                            };
                            accordionNewTrigger.addItem(newAccordionItem);
                        }
                    },
                    functionFailure: function (xhr, response) {
                        PMDesigner.msgWinError(response.error.message);
                    },
                    messageError: "There are problems getting the triggers wizard list, please try again.".translate()
                });
                restClient.executeRestClient();
            }

            openNewTriggerOptions();
            var accordion = triggerWindow.getItems()[5].getItems();
            accordion[0].setVisible(false);
            accordion[1].setVisible(false);
            for (i = 0; i < accordion.length; i++) {
                accordion[i].header.html.style.backgroundColor = "#FFFFFF";
                if (i > 1) {
                    addNumberTriggerWizard(lengthLibFunctions[i - 2], (i));
                }
            }
        };

        allTreesItems = [];
        getListTrigger();

        //Button NEW Trigger
        buttonNew = new PMUI.ui.Button({
            id: 'newTriggerButton',
            text: "Create".translate(),
            height: "36px",
            width: 100,
            handler: function (event) {
                openFormCustom("Create".translate());
                formEditTriggerCustom.getField("code").dom.labelTextContainer.style.width = "23.8%";
                $('.pmui-pmseparatorlinefield .pmui-field-label').css({display: 'none'});
                formEditTriggerCustom.showFooter();

                var codeMirrorControl, cmControlCopyTrigger;
                codeMirrorControl = formEditTriggerCustom.getItems()[1].controls[0].cm;
                formEditTriggerCustom.getItems()[1].html.style.padding = "10px";
                if (codeMirrorControl !== undefined) {
                    codeMirrorControl.setSize(580, 150);
                }
                formEditTriggerCustom.getItems()[1].setHeight(170);

                formEditTriggerCustom.reset();

                fields = formEditTriggerCustom.getItems()[0];
                fields.getItems()[0].setValue("");
                fields.getItems()[1].setValue("");
                fields.getItems()[2].setValue("");
                formEditTriggerCustom.getItems()[1].setValue("");
                formEditTriggerCustom.getItems()[1].controls[0].cm.setValue("");
                codeMirrorControl.refresh();
            },
            style: {
                cssClasses: [
                    'mafe-button-create'
                ]
            }
        });

        //Button COPY Trigger
        buttonCopy = new PMUI.ui.Button({
            id: 'copyTriggerButton',
            text: "Copy".translate(),
            height: "36px",
            width: 100,
            handler: function (event) {
                getListProjects();
                openFormCopy();
                formCopyTrigger.getFields()[1].clearOptions();
                $("#Code .CodeMirror.cm-s-default.CodeMirror-wrap").css({'border': '1px solid #c0c0c0'});
                formCopyTrigger.getItems()[4].setHeight(160);

                var cmControlCopyTrigger = formCopyTrigger.getFields()[4].controls[0].cm;
                cmControlCopyTrigger.setValue("");
                if (cmControlCopyTrigger !== undefined) {
                    cmControlCopyTrigger.setSize(650, 140);
                }
                cmControlCopyTrigger.refresh();
            },
            style: {
                cssClasses: [
                    'mafe-button-create'
                ]
            }
        });

        //Button WIZARD Trigger
        buttonWizard = new PMUI.ui.Button({
            id: 'wizardTriggerButton',
            text: "Wizard".translate(),
            height: "36px",
            width: 100,
            handler: function (event) {
                newTriggerOptions();
            },
            style: {
                cssClasses: [
                    'mafe-button-create'
                ]
            }
        });

        openListTriggers = function () {
            triggerWindow.getItems()[0].setVisible(true);
            triggerWindow.getItems()[1].setVisible(false);
            triggerWindow.getItems()[2].setVisible(false);
            triggerWindow.getItems()[3].setVisible(false);
            triggerWindow.getItems()[4].setVisible(false);
            triggerWindow.getItems()[5].setVisible(false);
            triggerWindow.setTitle("Triggers".translate());
            triggerWindow.hideFooter();
            $(triggerWindow.body).removeClass("pmui-background");
        };

        openFormCustom = function (typeTitle) {
            triggerWindow.getItems()[0].setVisible(false);
            triggerWindow.getItems()[1].setVisible(true);
            triggerWindow.getItems()[2].setVisible(false);
            triggerWindow.getItems()[3].setVisible(false);
            triggerWindow.getItems()[4].setVisible(false);
            triggerWindow.getItems()[5].setVisible(false);
            triggerWindow.footer.getItems()[0].setVisible(true); //button Cancel
            triggerWindow.footer.getItems()[1].setVisible(true); //button Save
            triggerWindow.footer.getItems()[2].setVisible(false); //Edit Source Code
            triggerWindow.footer.getItems()[3].setVisible(false); //button Aply
            triggerWindow.footer.getItems()[4].setVisible(false); //button Copy/import

            triggerWindow.setTitle(typeTitle + " Custom Trigger".translate());
            triggerWindow.showFooter();
            $(triggerWindow.body).addClass("pmui-background");
            formEditTriggerCustom.setFocus();

            $('.CodeMirror.cm-s-default')[0].style.border = "1px solid #c0c0c0";
            if (formEditTriggerCustom.getItems()[0].getItems()[1].visible == false) {
                var fields = formEditTriggerCustom.getItems()[0];
                fields.getItems()[1].setVisible(true);
                fields.getItems()[2].setVisible(true);
                formEditTriggerCustom.footer.getItems()[1].setVisible(true);
            }
            formEditTriggerCustom.getFields()[4].dom.labelTextContainer.style.marginLeft = "10px";
            formEditTriggerCustom.setAlignmentButtons('left');

            $("#code").after($ctrlSpaceMessage.css({
                "margin": "5px 0 0 0",
                "text-align": "center",
                "width": "655px",
                "padding": ""
            }));
        };

        openFormWizard = function () {
            triggerWindow.getItems()[0].setVisible(false);
            triggerWindow.getItems()[1].setVisible(false);
            triggerWindow.getItems()[2].setVisible(true);
            triggerWindow.getItems()[3].setVisible(false);
            triggerWindow.getItems()[4].setVisible(false);
            triggerWindow.getItems()[5].setVisible(false);
            triggerWindow.footer.getItems()[0].setVisible(true); //button Cancel
            triggerWindow.footer.getItems()[1].setVisible(true); //button Save
            triggerWindow.footer.getItems()[2].setVisible(false); //Edit Source Code
            triggerWindow.footer.getItems()[3].setVisible(false); //button Aply
            triggerWindow.footer.getItems()[4].setVisible(false); //button Copy/import
            triggerWindow.showFooter();
            $(triggerWindow.body).addClass("pmui-background");
            formEditTriggerWizard.setFocus();
        };

        openFormWizardEditMode = function () {
            triggerWindow.getItems()[0].setVisible(false);
            triggerWindow.getItems()[1].setVisible(false);
            triggerWindow.getItems()[2].setVisible(true);
            triggerWindow.getItems()[3].setVisible(false);
            triggerWindow.getItems()[4].setVisible(false);
            triggerWindow.getItems()[5].setVisible(false);
            triggerWindow.footer.getItems()[0].setVisible(true); //button Cancel
            triggerWindow.footer.getItems()[1].setVisible(true); //button Save
            triggerWindow.footer.getItems()[2].setVisible(true); //Edit Source Code
            triggerWindow.footer.getItems()[3].setVisible(false); //button Aply
            triggerWindow.footer.getItems()[4].setVisible(false); //button Copy/import
            triggerWindow.showFooter();
            $(triggerWindow.body).addClass("pmui-background");
            formEditTriggerWizard.setFocus();
        };


        openFormCopy = function () {
            triggerWindow.getItems()[0].setVisible(false);
            triggerWindow.getItems()[1].setVisible(false);
            triggerWindow.getItems()[2].setVisible(false);
            triggerWindow.getItems()[3].setVisible(true);
            triggerWindow.getItems()[4].setVisible(false);
            triggerWindow.getItems()[5].setVisible(false);
            triggerWindow.setTitle("Copy Trigger".translate());
            triggerWindow.footer.getItems()[0].setVisible(true); //button Cancel
            triggerWindow.footer.getItems()[1].setVisible(false); //button Save
            triggerWindow.footer.getItems()[2].setVisible(false); //Edit Source Code
            triggerWindow.footer.getItems()[3].setVisible(false); //button Aply
            triggerWindow.footer.getItems()[4].setVisible(true); //button Copy/import
            triggerWindow.showFooter();
            $(triggerWindow.body).addClass("pmui-background");
            formCopyTrigger.setFocus();

            $(".pmui-field").css("float", "left");
            $('#Code .pmui.pmui-pmcodemirrorcontrol').append($ctrlSpaceMessage.css({
                "margin-top": "10px",
                "text-align": "",
                "width": "655px",
                "padding": ""
            }));
        };

        openFormEditCode = function () {
            triggerWindow.getItems()[0].setVisible(false);
            triggerWindow.getItems()[1].setVisible(false);
            triggerWindow.getItems()[2].setVisible(false);
            triggerWindow.getItems()[3].setVisible(false);
            triggerWindow.getItems()[4].setVisible(true);
            triggerWindow.getItems()[5].setVisible(false);
            triggerWindow.setTitle("Editor".translate());
            triggerWindow.footer.getItems()[0].setVisible(true); //button Cancel
            triggerWindow.footer.getItems()[1].setVisible(false); //button Save
            triggerWindow.footer.getItems()[2].setVisible(false); //Edit Source Code
            triggerWindow.footer.getItems()[3].setVisible(true); //button Aply
            triggerWindow.footer.getItems()[4].setVisible(false); //button Copy/import
            triggerWindow.showFooter();
            $(triggerWindow.body).addClass("pmui-background");

            $($('#codeEditor .CodeMirror.cm-s-default.CodeMirror-wrap')[0]).after($ctrlSpaceMessage.css({
                "padding": "5px 0 0 10px",
                "text-align": ""
            }));

        };

        openNewTriggerOptions = function () {
            triggerWindow.getItems()[0].setVisible(false);
            triggerWindow.getItems()[1].setVisible(false);
            triggerWindow.getItems()[2].setVisible(false);
            triggerWindow.getItems()[3].setVisible(false);
            triggerWindow.getItems()[4].setVisible(false);
            triggerWindow.getItems()[5].setVisible(true);
            triggerWindow.setTitle("Create Predefined Trigger".translate());
            triggerWindow.footer.getItems()[0].setVisible(true); //button Cancel
            triggerWindow.footer.getItems()[1].setVisible(false); //button Save
            triggerWindow.footer.getItems()[2].setVisible(false); //Edit Source Code
            triggerWindow.footer.getItems()[3].setVisible(false); //button Aply
            triggerWindow.footer.getItems()[4].setVisible(false); //button Copy/import
            triggerWindow.showFooter();
            $(triggerWindow.body).removeClass("pmui-background");
        };

        //obtaning the list of process to show it on the copy/import of a trigger dropdown
        listProjects = [];
        getListProjects = function () {
            restClient = new PMRestClient({
                typeRequest: 'get',
                functionSuccess: function (xhr, response) {
                    listProjects = [];
                    formCopyTrigger.getFields()[0].clearOptions();
                    listProjects[0] = {
                        label: "- Select a process -".translate(),
                        value: "",
                        disabled: true,
                        selected: true
                    };
                    for (i = 0; i < response.length; i++) {
                        listProjects.push({
                            label: response[i].prj_name,
                            value: response[i].prj_uid
                        });
                    }
                    formCopyTrigger.getFields()[0].setOptions(listProjects);
                },
                functionFailure: function (xhr, response) {
                    PMDesigner.msgWinError(response.error.message);
                },
                messageError: "There are problems getting the list of projects.".translate()
            });
            restClient.setBaseEndPoint('projects');
            restClient.executeRestClient();
        };

        //form Copy/Import Trigger
        triggerSelectedData;
        formCopyTrigger = new PMUI.form.Form({
            id: "formCopyTriggers",
            border: true,
            width: '890px',
            height: 'auto',
            title: "",
            visibleHeader: false,
            items: [
                {
                    pmType: 'dropdown',
                    id: "processField",
                    label: 'Process'.translate(),
                    name: 'prj_uid',
                    required: true,
                    controlsWidth: 300,
                    value: "",
                    options: listProjects,
                    onChange: function (newValue, prevValue) {
                        var formFields = formCopyTrigger.getFields();
                        formFields[2].setValue("");
                        formFields[3].setValue("");
                        formFields[4].controls[0].cm.setValue("");

                        var uidProj = newValue, myForm = this.form;

                        //obtaning the Trigger list of the process SELECTED
                        restClient = new PMRestClient({
                            typeRequest: 'get',
                            functionSuccess: function (xhr, response) {
                                triggerSelectedData = response;
                                var listTriggersCopy = [];
                                listTriggersCopy[0] = {
                                    label: "- Select a trigger -".translate(),
                                    value: "",
                                    disabled: true,
                                    selected: true
                                };

                                for (i = 0; i < response.length; i++) {
                                    listTriggersCopy.push({
                                        label: response[i].tri_title,
                                        value: response[i].tri_uid
                                    });
                                }
                                formFields[1].setOptions(listTriggersCopy);
                            },
                            functionFailure: function (xhr, response) {
                                PMDesigner.msgWinError(response.error.message);
                            },
                            messageError: "There are problems getting the list of triggers.".translate()
                        });
                        restClient.setBaseEndPoint("project/" + uidProj + "/triggers");
                        restClient.executeRestClient();
                    }
                },
                {
                    pmType: 'dropdown',
                    id: "triggerField",
                    label: 'Trigger'.translate(),
                    name: 'tri_uid',
                    required: true,
                    controlsWidth: 300,
                    value: "",
                    options: [],
                    onChange: function (newValue, prevValue) {
                        var formFields = formCopyTrigger.getFields();
                        formFields[2].setValue("");
                        formFields[3].setValue("");
                        formFields[4].controls[0].cm.setValue("");

                        var uidTri = newValue;

                        for (i = 0; i < triggerSelectedData.length; i++) {
                            if (triggerSelectedData[i].tri_uid == uidTri) {
                                formFields[2].setValue(triggerSelectedData[i].tri_title);
                                formFields[3].setValue(triggerSelectedData[i].tri_description);
                                formFields[4].controls[0].cm.setValue(triggerSelectedData[i].tri_webbot);
                            }
                        }
                    }
                },
                {
                    pmType: "text",
                    id: "triTitle",
                    label: "Title of the new trigger".translate(),
                    placeholder: "Insert the title of the new trigger".translate(),
                    name: "tri_title",
                    required: true,
                    valueType: 'string',
                    controlsWidth: 300,
                    style: {
                        cssProperties: {
                            'float': 'left'
                        }
                    }
                },
                {
                    pmType: "textarea",
                    id: "triDescription",
                    rows: 80,
                    name: 'tri_description',
                    label: "Description of the new trigger".translate(),
                    valueType: 'string',
                    controlsWidth: 652,
                    style: {
                        cssClasses: ['mafe-textarea-resize'],
                        cssProperties: {'float': 'left'}
                    }
                }
            ]
        });
        codMirr = new PMCodeMirrorField();
        codMirr.setLabel("Code".translate());
        codMirr.setID("Code");
        codMirr.setName('tri_webbot');
        CodeMirror.commands.autocomplete = function (cm) {
            CodeMirror.showHint(cm, CodeMirror.phpHint);
        };
        formCopyTrigger.addItem(codMirr);

        //Form to Edit the trigger with the WIZARD (we used a Form because we need buttons to save the changes)
        formEditTriggerWizard = new PMUI.form.Form({
            id: "formEditTriggerWizard",
            border: true,
            visibleHeader: false,
            name: "formwizard",
            width: 926,
            title: "New Trigger".translate(),
            items: [
                {
                    id: "panelDetailsWizard",
                    pmType: "panel",
                    layout: 'vbox',
                    fieldset: false,
                    height: 'auto',
                    legend: "DETAILS".translate(),
                    items: [
                        {
                            id: "uid",
                            pmType: "text",
                            label: "ID".translate(),
                            value: "",
                            name: "uid",
                            visible: false,
                            valueType: 'string'
                        },
                        {
                            id: "libName",
                            pmType: "text",
                            label: "",
                            value: "",
                            name: "libName",
                            visible: false,
                            valueType: 'string'
                        },
                        {
                            id: "fnName",
                            pmType: "text",
                            label: "",
                            value: "",
                            name: "fnName",
                            visible: false,
                            valueType: 'string'
                        },
                        {
                            id: "type",
                            pmType: "text",
                            label: "Type".translate(),
                            value: "WIZARD",
                            controlsWidth: 450,
                            labelWidth: '27%',
                            readOnly: true,
                            name: "type",
                            valueType: 'string'
                        },
                        {
                            id: "titleDetailsWiz",
                            pmType: "text",
                            label: "Title".translate(),
                            value: "",
                            controlsWidth: 450,
                            labelWidth: '27%',
                            name: "titleDetails",
                            valueType: 'string',
                            required: true
                        },
                        {
                            id: "descriptionDetailsWiz",
                            pmType: "textarea",
                            label: "Description".translate(),
                            value: "",
                            rows: 90,
                            width: '300px',
                            controlsWidth: 450,
                            labelWidth: '27%',
                            name: "descriptionDetails",
                            valueType: 'string',
                            style: {cssClasses: ['mafe-textarea-resize']}
                        },
                        {
                            id: "webbot",
                            pmType: "text",
                            label: "Webbot".translate(),
                            value: "",
                            name: "webbot",
                            controlsWidth: 300,
                            labelWidth: '27%',
                            visible: false,
                            valueType: 'string'
                        }
                    ]
                }
            ]
        });

        //adding a separator Inputs line
        sepInputs = new PMSeparatorLineField({
            controlHeight: '1px',
            controlColor: "#CDCDCD",
            controlsWidth: "890px",
            marginLeft: '0%'
        });
        formEditTriggerWizard.addItem(sepInputs);

        //adding a label Inputs
        labelInputs = new PMLabelField({
            text: "PARAMETERS".translate(),
            textMode: "plain",
            style: {
                cssProperties: {
                    color: "#AEAEAE",
                    'font-weight': 'bold'
                }
            },
            controlsWidth: 885
        });
        formEditTriggerWizard.addItem(labelInputs);

        //adding the Inputs Items (PARAMETERS) Panel
        paramPanel = new PMUI.form.FormPanel({
            id: "panelParametersWizard",
            layout: 'vbox',
            fieldset: false,
            height: 'auto',
            visible: false,
            legend: "__PARAMETERS__".translate(),
            items: []
        });
        formEditTriggerWizard.addItem(paramPanel);

        //adding a separator Outputs line
        sepOutputs = new PMSeparatorLineField({
            controlHeight: 1,
            controlColor: "#CDCDCD",
            controlsWidth: "890px",
            marginLeft: '0%'
        });
        formEditTriggerWizard.addItem(sepOutputs);

        //adding a label Outputs
        labelOutputs = new PMLabelField({
            text: "RETURN VALUE".translate(),
            textMode: "plain",
            style: {
                cssProperties: {
                    color: "#AEAEAE",
                    'font-weight': 'bold'
                }
            },
            controlsWidth: 885
        });

        formEditTriggerWizard.addItem(labelOutputs);

        //adding the Output Items (Returns) Panel
        returnPanel = new PMUI.form.FormPanel({
            id: "panelReturnValWizard",
            layout: 'vbox',
            fieldset: false,
            height: 'auto',
            visible: false,
            legend: "RETURN VALUE".translate(),
            items: []
        });
        formEditTriggerWizard.addItem(returnPanel);


        //treePanel New Trigger PMF
        treeNewTrigger = new PMUI.panel.TreePanel({
            id: "treeNewTrigger",
            width: 'auto',
            height: 'auto',
            style: {
                cssClasses: ['pmtrigger'],
                cssProperties: {
                    'margin-top': '5px',
                    'margin-bottom': '4px'
                }
            },
            items: []
        });

        //Acordion Panel New Triggers Options
        accordionNewTrigger = new PMUI.panel.AccordionPanel({
            id: "accordionNewTrigger",
            width: 885,
            height: 'auto',
            borderWidth: "0px",
            hiddenTitle: true,
            style: {
                cssProperties: {
                    'margin-left': '30px'
                }
            },
            items: [
                {
                    iconClass: "",
                    id: 'custom',
                    title: "Custom Trigger".translate(),
                    height: '26px',
                    body: "",
                    style: {
                        cssProperties: {
                            "background-color": "#FDFDFD"
                        }
                    }
                },
                {
                    iconClass: "",
                    id: 'copy',
                    title: "Copy Trigger".translate(),
                    height: '26px',
                    body: "",
                    style: {
                        cssProperties: {
                            "background-color": "#FDFDFD"
                        }
                    }
                }
            ],
            listeners: {
                select: function (obj, event) {

                    if (obj.id == "custom") {
                        openFormCustom("Create".translate());
                        $('.pmui-pmseparatorlinefield .pmui-field-label').css({display: 'none'});
                        formEditTriggerCustom.showFooter();

                        var codeMirrorControl, cmControlCopyTrigger;
                        codeMirrorControl = formEditTriggerCustom.getItems()[1].controls[0].cm;

                        if (codeMirrorControl != undefined) {
                            codeMirrorControl.setSize(580, 150); //CodeMirror Size
                        }
                        formEditTriggerCustom.getItems()[1].setHeight(170);

                        formEditTriggerCustom.reset();

                        fields = formEditTriggerCustom.getItems()[0];
                        fields.getItems()[0].setValue("");
                        fields.getItems()[1].setValue("");
                        fields.getItems()[2].setValue("");
                        formEditTriggerCustom.getItems()[1].setValue("");
                        formEditTriggerCustom.getItems()[1].controls[0].cm.setValue("");

                    } else if (obj.id == "copy") {

                        getListProjects();
                        openFormCopy();
                        $($('#Code .CodeMirror.cm-s-default.CodeMirror-wrap')[1]).css({'border': '1px solid #c0c0c0'});
                        $('.pmui-pmseparatorlinefield .pmui-field-label').css({display: 'none'});
                        formCopyTrigger.getItems()[4].setHeight(160);

                        var cmControlCopyTrigger = formCopyTrigger.getFields()[4].controls[0].cm;
                        if (cmControlCopyTrigger != undefined) {
                            cmControlCopyTrigger.setSize(650, 140); //CodeMirror in formCopyTrigger Size
                        }
                    } else {
                        if (treeNewTrigger.getItems() == "" || treeNewTrigger.id != obj.id) {
                            var acItems = accordionNewTrigger.getItems();
                            var accordionActualItem = accordionNewTrigger.getItem(obj.id);

                            for (i = 0; i < acItems.length; i++) {
                                if (acItems[i].id == accordionActualItem.id) {
                                    var positionActualAcItem = i - 2;
                                }
                            }

                            treeNewTrigger.setItems(allTreesItems[positionActualAcItem]);
                            treeNewTrigger.setID(obj.id);

                            accordionActualItem.setBody(treeNewTrigger);
                            treeNewTrigger.defineEvents();
                            applyStyleTreePanel(treeNewTrigger, false);
                        }
                    }
                }
            }
        });

        updateCustom = function (dataToSend, triggerUid) {
            var restClient = new PMRestClient({
                endpoint: "trigger/" + triggerUid,
                typeRequest: 'update',
                data: dataToSend,
                functionSuccess: function (xhr, response) {
                    formEditTriggerCustom.setDirty(false);
                    getListTrigger();
                    openListTriggers();
                    $('.pmui-pmseparatorlinefield .pmui-field-label').css({display: 'none'});
                },
                functionFailure: function (xhr, response) {
                    var msg = response.error.message;
                    var arrayMatch = [];

                    if ((arrayMatch = /^[\w\s]+\:\s*(.*)$/i.exec(msg))) {
                        msg = arrayMatch[1];
                    }

                    PMDesigner.msgWinError(msg);
                },
                messageSuccess: "Trigger updated successfully".translate(),
                flashContainer: gridListTrigger
            });
            restClient.executeRestClient();
        };

        checkIfValuesAreEqual = function (initialData, finalData) {
            if (typeof(initialData['tri_uid']) == "undefined" && finalData['tri_uid'] == '') {
                if (finalData['tri_description'] != '' || finalData['tri_title'] != '' || finalData['tri_webbot'] != '') {
                    return false;
                }
            }
            for (var key1 in initialData) {
                for (var key2 in finalData) {
                    if (typeof(initialData[key1]) != "undefined" &&
                        typeof(finalData[key2]) != "undefined" &&
                        key1 == key2 &&
                        initialData[key1] != finalData[key2]
                    ) {
                        return false;
                    }
                }
            }
            return true;
        }

        //Buttons Save and Cancel for the 'formEditTriggerCustom' and 'formEditTriggerWizard'
        buttonSave = new PMUI.ui.Button({
            id: 'saveTriggerButton',
            text: "Save".translate(),
            handler: function (event) {
                triggerOriginDataForUpdate = {};
                var formcustom = triggerWindow.getItems()[1],
                    formwizard = triggerWindow.getItems()[2],
                    formTriggerData,
                    restClient,
                    dataToSend,
                    formTriggerData,
                    inputFields,
                    message_window,
                    outputV,
                    outputFields,
                    inputV,
                    dataToSend,
                    flagEdit;
                if (formcustom.isVisible() == true) {
                    valuesCustom=formcustom.getItems()[0];
                    if (!formEditTriggerCustom.isValid( )) {
                        flagEdit = formEditTriggerCustom.visible;
                    } else {
                        flagEdit = formEditTriggerCustom.isValid();
                    }
                    if (flagEdit) {
                        if ((navigator.userAgent.indexOf("MSIE") != -1) || (navigator.userAgent.indexOf("Trident") != -1)) {
                            formTriggerData = getData2PMUI(formEditTriggerCustom.html);
                        } else {
                            formTriggerData = formEditTriggerCustom.getData();
                        }
                        if (formTriggerData.title.trim() === "") {
                            return false;
                        }
                        dataToSend = {
                            tri_title: formTriggerData.title,
                            tri_description: formTriggerData.description,
                            tri_webbot: formTriggerData.code,
                            tri_param: ""
                        };

                        if (formTriggerData.uid === "") {
                            restClient = new PMRestClient({
                                endpoint: 'trigger',
                                typeRequest: 'post',
                                data: dataToSend,
                                functionSuccess: function (xhr, response) {
                                    formEditTriggerCustom.setDirty(false);
                                    getListTrigger();
                                    formEditTriggerCustom.getItems()[0].getItems()[0].setValue(response.tri_uid);
                                    openListTriggers();
                                    $('.pmui-pmseparatorlinefield .pmui-field-label').css({display: 'none'});
                                },
                                functionFailure: function (xhr, response) {
                                    var msg = response.error.message,
                                        arrayMatch = [];

                                    if ((arrayMatch = /^[\w\s]+\:\s*(.*)$/i.exec(msg))) {
                                        msg = arrayMatch[1];
                                    }

                                    PMDesigner.msgWinError(msg);
                                },
                                messageSuccess: "New Trigger saved successfully".translate(),
                                flashContainer: gridListTrigger
                            });
                            restClient.executeRestClient();
                        } else {
                            if (formTriggerData.wizzard != "") {
                                if (formEditTriggerCustom.isDirty()) {
                                    message_window = new PMUI.ui.MessageWindow({
                                        id: "wizzardToCustomTriggerWin",
                                        windowMessageType: 'warning',
                                        title: 'Triggers'.translate(),
                                        width: 490,
                                        message: 'Do you want to save the changes? This Trigger will be saved like a custom Trigger.'.translate(),
                                        footerItems: [
                                            {
                                                text: "No".translate(),
                                                handler: function () {
                                                    message_window.close();
                                                    openFormWizardEditMode();
                                                    $('.pmui-pmseparatorlinefield .pmui-field-label').css({display: 'none'});
                                                    formEditTriggerCustom.reset();
                                                },
                                                buttonType: "error"
                                            },
                                            {
                                                text: "Yes".translate(),
                                                handler: function () {
                                                    message_window.close();
                                                    updateCustom(dataToSend, formTriggerData.uid);
                                                },
                                                buttonType: "success"
                                            }
                                        ]

                                    });
                                    message_window.open();
                                    message_window.showFooter();
                                } else {
                                    openListTriggers();
                                    $('.pmui-pmseparatorlinefield .pmui-field-label').css({display: 'none'});
                                }
                            } else {
                                updateCustom(dataToSend, formTriggerData.uid);
                            }
                        }
                    }
                } else if (formwizard.isVisible() == true) {
                    if (formEditTriggerWizard.isValid() || ((navigator.userAgent.indexOf("MSIE") != -1) || (navigator.userAgent.indexOf("Trident") != -1))) {
                        if ((navigator.userAgent.indexOf("MSIE") != -1) || (navigator.userAgent.indexOf("Trident") != -1)) {
                            formTriggerData = getData2PMUI(formEditTriggerWizard.html);
                        } else {
                            formTriggerData = formEditTriggerWizard.getData();
                        }

                        inputV = {};
                        inputFields = formEditTriggerWizard.getItems()[3];
                        for (i = 0; i < inputFields.getItems().length; i++) {
                            inputV[inputFields.getItems()[i].name] = formTriggerData[inputFields.getItems()[i].name];
                        }

                        outputV = {};
                        outputFields = formEditTriggerWizard.getItems()[6];
                        for (j = 0; j < outputFields.getItems().length; j++) {
                            outputV[outputFields.getItems()[j].name] = formTriggerData[outputFields.getItems()[j].name];
                        }

                        dataToSend = {
                            tri_title: formTriggerData.titleDetails,
                            tri_description: formTriggerData.descriptionDetails,
                            tri_type: "SCRIPT",
                            tri_params: {
                                input: inputV,
                                output: outputV
                            }
                        };

                        if (formTriggerData.uid === "") {
                            restClient = new PMRestClient({
                                endpoint: 'trigger-wizard/' + formTriggerData.libName + '/' + formTriggerData.fnName,
                                typeRequest: 'post',
                                data: dataToSend,
                                functionSuccess: function (xhr, response) {
                                    formEditTriggerWizard.setDirty(false);
                                    getListTrigger();
                                    formEditTriggerWizard.getFields()[0].setValue(response.tri_uid);
                                    openListTriggers();
                                },
                                functionFailure: function (xhr, response) {
                                    PMDesigner.msgWinError(response.error.message);
                                },
                                messageSuccess: "New Trigger saved successfully".translate(),
                                flashContainer: gridListTrigger
                            });
                            restClient.executeRestClient();
                        } else {
                            restClient = new PMRestClient({
                                endpoint: 'trigger-wizard/' + formTriggerData.libName + '/' + formTriggerData.fnName + '/' + formTriggerData.uid,
                                typeRequest: 'update',
                                data: dataToSend,
                                functionSuccess: function (xhr, response) {
                                    formEditTriggerWizard.setDirty(false);
                                    getListTrigger();
                                    openListTriggers();
                                },
                                functionFailure: function (xhr, response) {
                                    PMDesigner.msgWinError(response.error.message);
                                },
                                messageSuccess: "Trigger updated successfully".translate(),
                                flashContainer: gridListTrigger
                            });
                            restClient.executeRestClient();
                        }
                    }
                }
            },
            buttonType: 'success'
        });

        //Buttons Save and Cancel for the 'formEditTriggerCustom' and 'formEditTriggerWizard'
        copyButton = new PMUI.ui.Button({
            id: 'copyAndSaveButton',
            text: "Copy Trigger".translate(),
            buttonType: 'success',
            handler: function (event) {
                var data,
                    dataToSend,
                    restClient;
                if (formCopyTrigger.isValid()) {
                    formCopyTrigger.setDirty(false);

                    data = formCopyTrigger.getData();
                    dataToSend = {
                        tri_title: getData2PMUI(formCopyTrigger.html).tri_title.trim(),
                        tri_description: data.tri_description,
                        tri_webbot: data.tri_webbot,
                        tri_param: ""
                    };
                    restClient = new PMRestClient({
                        endpoint: 'trigger',
                        typeRequest: 'post',
                        data: dataToSend,
                        functionSuccess: function (xhr, response) {
                            getListTrigger();
                            formCopyTrigger.reset();
                            openListTriggers();
                        },
                        functionFailure: function (xhr, response) {
                            PMDesigner.msgWinError(response.error.message);
                        },
                        messageSuccess: "New Trigger saved successfully".translate(),
                        flashContainer: gridListTrigger
                    });
                    restClient.executeRestClient();
                }
            }
        });

        //Buttons Apply for the Code Editor
        applyButton = new PMUI.ui.Button({
            id: 'applyButton',
            text: "Apply".translate(),
            buttonType: 'success',
            handler: function (event) {

                $("div.pmui-window-body.pmui-background").css('overflow', '');
                if (formEditCode.isValid()) {
                    var typeTitle = "Edit".translate();
                    if (triggerWindow.getItems()[1].getData().uid == "") {
                        typeTitle = "Create".translate();
                    }
                    openFormCustom(typeTitle);
                    formEditCode.setDirty(false);

                    var editCode = formEditCode.getItems()[0].controls[0].cm;
                    editCodeValue = editCode.getValue();

                    var codeMirrorCustom = formEditTriggerCustom.getItems()[1].controls[0].cm;

                    if (codeMirrorCustom != undefined) {
                        codeMirrorCustom.setSize(580, 150); //CodeMirror Size
                    }
                    codeMirrorCustom.setValue(editCodeValue);
                    formEditTriggerCustom.getItems()[1].setValue(editCodeValue);

                    editCode.setValue("");
                    editCodeValue = "";

                    formEditCode.getItems()[0].setValue("");
                }
            }
        });

        //Button Edit Source Code (this is showed just when the Trigger Wizzard is opened in edition mode)
        btnEditSourceCode = new PMUI.ui.Button({
            id: 'btnEditSourceCode',
            text: "Edit Source Code".translate(),
            buttonType: 'success',
            handler: function (event) {

                var data = formEditTriggerWizard.getData();
                var fields;
                openFormCustom("Edit".translate());
                formEditTriggerCustom.showFooter();

                var codeMirrorControl;
                codeMirrorControl = formEditTriggerCustom.getItems()[1].controls[0].cm;

                if (codeMirrorControl !== undefined) {
                    codeMirrorControl.setSize(650, 280);
                }
                formEditTriggerCustom.getItems()[1].setHeight(170);

                fields = formEditTriggerCustom.getItems()[0];
                fields.getItems()[0].setValue(data.uid);
                fields.getItems()[1].setValue(data.titleDetails);
                fields.getItems()[2].setValue(data.descriptionDetails);
                fields.getItems()[3].setValue(data.webbot);
                fields.getItems()[3].setVisible(false);
                formEditTriggerCustom.getItems()[1].setValue(data.webbot);

                codeMirrorControl.setValue(data.webbot);

                formEditTriggerCustom.getItems()[1].setHeight(300);
                fields.getItems()[1].setVisible(false);
                fields.getItems()[2].setVisible(false);
                formEditTriggerCustom.footer.getItems()[1].setVisible(false);
                formEditTriggerCustom.footer.getItems()[0].style.addProperties({'margin-right': "10px"});
                formEditTriggerCustom.footer.style.addProperties({width: '880px'});
                codeMirrorControl.refresh();
            }
        });

        buttonCancel = new PMUI.ui.Button({
            id: 'cancelTriggerButton',
            text: "Cancel".translate(),
            buttonType: 'error',
            handler: function (event) {
                clickedClose = false;
                isDirtyFormTrigger();
            }
        });

        //Form to Edit the Custom trigger (we used a Form because we need buttons to save the changes)
        formEditTriggerCustom = new PMUI.form.Form({
            id: "formEditTriggerCustom",
            border: true,
            visibleHeader: false,
            width: '900px',
            height: "365px",
            name: "formcustom",
            title: "Custom Trigger".translate(),
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
                            id: "uid",
                            pmType: "text",
                            label: "ID".translate(),
                            value: "",
                            name: "uid",
                            readOnly: true,
                            visible: false,
                            valueType: 'string'
                        },
                        {
                            id: "title",
                            pmType: "text",
                            label: "Title".translate(),
                            value: "",
                            required: true,
                            name: "title",
                            valueType: 'string'
                        },
                        {
                            id: "description",
                            pmType: "textarea",
                            rows: 70,
                            name: 'description',
                            label: "Description".translate(),
                            valueType: 'string',
                            style: {cssClasses: ['mafe-textarea-resize']}
                        },
                        {
                            id: "wizzard",
                            pmType: "text",
                            label: "Wizzard".translate(),
                            value: "",
                            name: "wizzard",
                            readOnly: true,
                            visible: false,
                            valueType: 'string'
                        }
                    ]
                }
            ],
            buttons: [
                {
                    text: "@@",
                    id: "selectPickerButton",
                    handler: function () {
                        var picker = new VariablePicker();
                        picker.open({
                            success: function (variable) {
                                var codeMirror = formEditTriggerCustom.getItems()[1].controls[0].cm;
                                var cursorPos = codeMirror.getCursor();
                                codeMirror.replaceSelection(variable);
                                codeMirror.setCursor(cursorPos.line, cursorPos.ch);
                            }
                        });
                    },
                    style: {
                        cssProperties: {
                            "margin-left": '208px',
                            "background": "rgb(30, 145, 209)",
                            "border": "0px solid rgb(30, 145, 209)"
                        }
                    }
                }, {
                    text: "Open Editor".translate(),
                    id: "openEditorButton",
                    handler: function () {
                        openFormEditCode();
                        formEditCode.showFooter();

                        var codeMirrorCustom = formEditTriggerCustom.getItems()[1].controls[0].cm;
                        var value = codeMirrorCustom.getValue();

                        var codeMirrorCopy = formEditCode.getItems()[0].controls[0].cm;

                        if (codeMirrorCopy != undefined) {
                            codeMirrorCopy.setSize(810, 315); //CodeMirror Size
                            $($('#codeEditor .CodeMirror.cm-s-default.CodeMirror-wrap')[0]).css({'border': '1px solid #c0c0c0'});

                        }

                        formEditCode.getItems()[0].setHeight(325);

                        codeMirrorCopy.setValue(value);
                        formEditCode.getItems()[0].setValue(value);
                        formEditCode.setDirty(false);

                        $('#codeEditor .pmui-field-label').hide();
                        $('#formEditCode').children().last().css({
                            'margin-top': '-352px',
                            'border': '0px',
                            'margin-right': '-15px'
                        }).find('a').css('padding', '10px 5px');
                        $($('#codeEditor .CodeMirror.cm-s-default.CodeMirror-wrap')[0]).css({
                            'margin-left': '10px',
                            'width': '850px',
                            'height': '360px'
                        });
                        $('#triggerWindow .pmui-window-body').css('overflow', 'hidden');
                        codeMirrorCopy.refresh();
                    },
                    style: {
                        cssProperties: {
                            "margin-right": 2
                        }
                    }
                }
            ]
        });
        cd = new PMCodeMirrorField({
            labelWidth: '23.8%'
        });
        cd.setLabel("Code".translate());
        formEditTriggerCustom.addItem(cd);
        formEditTriggerCustom.getItems()[1].setName('code');
        formEditTriggerCustom.getItems()[1].setID('code');

        formEditCode = new PMUI.form.Form({
            id: "formEditCode",
            border: true,
            visibleHeader: false,
            width: '925px',
            name: "formeditcode",
            title: "Editor".translate(),
            items: [],
            buttons: [
                {
                    text: "@@",
                    id: "selectPickerButton",
                    handler: function () {
                        var picker = new VariablePicker();
                        picker.open({
                            success: function (variable) {
                                var codeMirror = formEditCode.getItems()[0].controls[0].cm;
                                var cursorPos = codeMirror.getCursor();
                                codeMirror.replaceSelection(variable);
                                codeMirror.setCursor(cursorPos.line, cursorPos.ch);
                            }
                        });
                    },
                    style: {
                        cssProperties: {
                            "margin-left": '229px',
                            "background": "rgb(45, 62, 80)",
                            "border": "1px solid rgb(45, 62, 80)"
                        }
                    }
                }
            ]
        });
        editCode = new PMCodeMirrorField({
            labelWidth: '9%'
        });
        editCode.setLabel("Code".translate());
        formEditCode.addItem(editCode);
        formEditCode.getItems()[0].setName('codeEditor');
        formEditCode.getItems()[0].setID('codeEditor');

        //Trigger Window
        triggerWindow = new PMUI.ui.Window({
            id: "triggerWindow",
            title: "Triggers".translate(),
            width: DEFAULT_WINDOW_WIDTH,
            height: DEFAULT_WINDOW_HEIGHT,
            footerItems: [
                buttonCancel,
                buttonSave,
                btnEditSourceCode,
                applyButton,
                copyButton
            ],
            buttonPanelPosition: "bottom",
            footerAling: "right",
            onBeforeClose: function () {
                clickedClose = true;
                isDirtyFormTrigger();
            }
        });


        triggerWindow.addItem(gridListTrigger);
        triggerWindow.addItem(formEditTriggerCustom);
        triggerWindow.addItem(formEditTriggerWizard);
        triggerWindow.addItem(formCopyTrigger);
        triggerWindow.addItem(formEditCode);
        triggerWindow.addItem(accordionNewTrigger);

        if (typeof listTriggers !== "undefined") {
            triggerWindow.open();
            codMirr.dom.labelTextContainer.style.width = "23.8%";
            codMirr.html.style.padding = "10px";
            formEditTriggerCustom.panel.html.style.padding = "10px 10px";
            $('#listTriggers .pmui-textcontrol').css({'margin-top': '5px', width: '250px'});
            applyStyleWindowForm(triggerWindow);
            triggerWindow.hideFooter();
            openListTriggers();

            gridListTrigger.dom.toolbar.appendChild(buttonNew.getHTML());
            buttonNew.defineEvents();

            gridListTrigger.dom.toolbar.appendChild(buttonCopy.getHTML());
            buttonCopy.defineEvents();

            gridListTrigger.dom.toolbar.appendChild(buttonWizard.getHTML());
            buttonWizard.defineEvents();

            triggerWindow.defineEvents();
            gridListTrigger.sort('tri_title', 'asc');
            formEditTriggerCustom.panel.style.addProperties({'overflow': 'hidden'});

            formcustom = triggerWindow.getItems()[1];
            valuesCustom=formcustom.getItems()[0];
        }
    };

    PMDesigner.trigger.create = function () {
        var codeMirrorControl, cmControlCopyTrigger;
        openFormCustom("Create".translate());
        formEditTriggerCustom.getField("code").dom.labelTextContainer.style.width = "23.8%";
        $('.pmui-pmseparatorlinefield .pmui-field-label').css({display: 'none'});
        formEditTriggerCustom.showFooter();

        codeMirrorControl = formEditTriggerCustom.getItems()[1].controls[0].cm;
        formEditTriggerCustom.getItems()[1].html.style.padding = "10px";
        if (codeMirrorControl != undefined) {
            codeMirrorControl.setSize(580, 150);
        }
        formEditTriggerCustom.getItems()[1].setHeight(170);
        formEditTriggerCustom.reset();
    };
}());