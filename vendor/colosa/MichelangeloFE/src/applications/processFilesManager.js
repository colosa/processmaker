var openCreateTemplates = false;

PMDesigner.ProcessFilesManager = function (processFileManagerOptionPath, optionCreation) {
    var rowselected = {};
    var rowselectedFile = {};
    var typeSave = '';
    var gridFilesManager;
    var gridTemplate;
    var gridPublic;
    var formEdit;
    var windowEdit;
    var windowCode;
    var editorHTML;
    var formUpload;
    var formUploadField;
    var initTinyMCE = null;
    var flagGridTemplate = true;
    var flagGridPublic = true;
    var presviusValueTiny = "";
    var buttonEditClass = 'mafe-button-edit';
    var buttonDeleteClass = 'mafe-button-delete';
    var buttonPropertiesClass = 'mafe-button-properties';

    var warningTemplate = new PMUI.ui.MessageWindow({
        id: 'warningTemplate',
        windowMessageType: 'warning',
        width: 490,
        title: 'Process Files Manager'.translate(),
        message: 'Do you want to delete this file?'.translate(),
        footerItems: [{
            id: 'warningTemplateButtonNo',
            text: 'No'.translate(),
            handler: function () {
                warningTemplate.close();
            },
            buttonType: "error"
        }, {
            id: 'warningTemplateButtonYes',
            text: 'Yes'.translate(),
            handler: function () {
                (new PMRestClient({
                    endpoint: 'file-manager/' + rowselectedFile.getData().prf_uid,
                    typeRequest: 'remove',
                    messageError: '',
                    functionSuccess: function (xhr, response) {
                        PMDesigner.msgFlash('File deleted successfully'.translate(), gridTemplate);
                        loadTemplate();
                    },
                    functionFailure: function (xhr, response) {
                        PMDesigner.msgWinError(response.error.message);
                    },
                    messageSuccess: 'File deleted successfully'.translate(),
                    flashContainer: gridTemplate
                })).executeRestClient();
                warningTemplate.close();
            },
            buttonType: "success"
        }
        ]
    });
    var isDirtyUpload = function () {
        $("input,select,textarea").blur();
        if (formUpload.isDirty()) {
            var message_window = new PMUI.ui.MessageWindow({
                id: "cancelMessageTriggers",
                windowMessageType: 'warning',
                width: 490,
                title: "Upload File".translate(),
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
                            formUploadField.reset();
                            message_window.close();
                            windowUpload.close();
                        },
                        buttonType: "success"
                    }
                ]
            });
            message_window.open();
            message_window.showFooter();
        } else {
            windowUpload.close();
        }
    };

    var isDirtyFormEdit = function () {
        var fileContent = $(tinyMCE.activeEditor.getContent()).text().trim().length ? tinyMCE.activeEditor.getContent() : formEdit.getField("filecontent").getValue();
        if (formEdit.isDirty() || fileContent !== presviusValueTiny) {
            $(".mceSplitButtonMenu").each(function () {
                if ($(this).is(":visible")) {
                    $(this).addClass("mrdk").hide();
                }
            });
            var message_window = new PMUI.ui.MessageWindow({
                id: "cancelMessageTriggers",
                width: 490,
                windowMessageType: 'warning',
                title: 'Process Files Manager'.translate(),
                message: 'Are you sure you want to discard your changes?'.translate(),
                footerItems: [
                    {
                        text: "No".translate(),
                        handler: function () {
                            $(".mceSplitButtonMenu.mrdk").each(function () {
                                if ($(this).is(":hidden")) {
                                    $(this).removeClass("mrdk").show();
                                }
                            });
                            message_window.close();
                            windowFilesManager.close();
                        },
                        buttonType: "error"
                    }, {
                        text: "Yes".translate(),
                        handler: function () {
                            message_window.close();

                            if (clickedClose) {
                                windowEdit.close();
                            } else {
                                windowEdit.close();
                                windowFilesManager.open();
                            }
                        },
                        buttonType: "success"
                    }
                ]
            });
            message_window.open();
            message_window.showFooter();
        } else {
            if (clickedClose) {
                windowEdit.close();
            } else {
                windowEdit.close();
                windowFilesManager.open();
            }
        }
    };

    var warningPublic = new PMUI.ui.MessageWindow({
        id: 'warningPublic',
        title: 'Process Files Manager'.translate(),
        windowMessageType: 'warning',
        width: 490,
        message: 'Do you want to delete this file?'.translate(),
        footerItems: [{
            id: 'warningPublicButtonNo',
            text: 'No'.translate(),
            handler: function () {
                warningPublic.close();
            },
            buttonType: "error"
        }, {
            id: 'warningPublicButtonYes',
            text: 'Yes'.translate(),
            handler: function () {
                (new PMRestClient({
                    endpoint: 'file-manager/' + rowselectedFile.getData().prf_uid,
                    typeRequest: 'remove',
                    messageError: '',
                    functionSuccess: function (xhr, response) {
                        loadPublic();
                    },
                    functionFailure: function (xhr, response) {
                        PMDesigner.msgWinError(response.error.message);
                    },
                    messageSuccess: 'File deleted successfully'.translate(),
                    flashContainer: gridPublic
                })).executeRestClient();
                warningPublic.close();
            },
            buttonType: "success"
        }
        ]
    });

    var windowFilesManager = new PMUI.ui.Window({
        id: 'windowFilesManager',
        title: 'Process Files Manager'.translate(),
        height: DEFAULT_WINDOW_HEIGHT,
        width: DEFAULT_WINDOW_WIDTH,
        items: [
            gridFilesManager = new PMUI.grid.GridPanel({
                id: 'gridFilesManager',
                pageSize: 10,
                width: "96%",
                style: {
                    cssClasses: ["mafe-gridPanel"]
                },
                emptyMessage: 'No records found'.translate(),
                nextLabel: 'Next'.translate(),
                previousLabel: 'Previous'.translate(),
                customStatusBar: function (currentPage, pageSize, numberItems, criteria, filter) {
                    return messagePageGrid(currentPage, pageSize, numberItems, criteria, filter);
                },
                dataItems: {},
                columns: [
                    {
                        title: 'Main Folders'.translate(),
                        dataType: 'string',
                        columnData: 'prf_path',
                        width: DEFAULT_WINDOW_WIDTH - 200,
                        alignmentCell: 'left',
                        sortable: true
                    },
                    {
                        id: 'filesGridViewCol',
                        title: '',
                        dataType: 'button',
                        buttonLabel: 'View'.translate(),
                        buttonStyle: {cssClasses: ['mafe-button-show']},
                        onButtonClick: function (row, grid) {
                            rowselected = row;
                            openFolder();
                        }
                    }
                ],
                onRowClick: function (row, grid) {
                    rowselected = row;
                    openFolder();
                }
            })
        ]
    });
    var buttonNew = new PMUI.ui.Button({
        id: 'createBtn',
        text: 'Create'.translate(),
        height: "38px",
        width: 100,
        style: {cssClasses: ['mafe-button-create']},
        handler: function (event) {
            newfile();
        }
    });
    var gridTemplate = new PMUI.grid.GridPanel({
        id: 'gridTemplate',
        filterPlaceholder: 'Search ...'.translate(),
        pageSize: 10,
        width: '96%',
        style: {
            cssClasses: ["mafe-gridPanel"]
        },
        tableContainerHeight: 374,
        emptyMessage: 'No records found'.translate(),
        nextLabel: 'Next'.translate(),
        previousLabel: 'Previous'.translate(),
        customStatusBar: function (currentPage, pageSize, numberItems, criteria, filter) {
            return messagePageGrid(currentPage, pageSize, numberItems, criteria, filter);
        },
        dataItems: {},
        columns: [{
            title: "TEMPLATES".translate(),
            dataType: 'string',
            columnData: 'prf_filename',
            width: '500px',
            alignmentCell: 'left',
            sortable: true
        }, {
            dataType: 'string',
            columnData: 'prf_uid',
            alignmentCell: 'left',
            visible: false
        }, {
            dataType: 'string',
            columnData: 'prf_content',
            alignmentCell: 'left',
            visible: false
        }, {
            id: 'gridTemplateButtonHtmlEditor',
            title: '',
            dataType: 'button',
            buttonLabel: 'Html Editor'.translate(),
            width: '106px',
            onButtonClick: function (row, grid) {
                openHtmlEditor(row);
            },
            buttonStyle: {cssClasses: [buttonEditClass]}
        }, {
            id: 'gridTemplateButtonRichTextEditor',
            title: '',
            dataType: 'button',
            buttonLabel: 'Rich Text'.translate(),
            width: '100px',
            onButtonClick: function (row, grid) {
                presviusValueTiny = row.getData().prf_content ? row.getData().prf_content : "";
                rowselectedFile = row;
                editfile();
            },
            buttonStyle: {cssClasses: [buttonEditClass]}
        }, {
            id: 'gridTemplateButtonDownload',
            title: '',
            dataType: 'button',
            buttonLabel: 'Download'.translate(),
            width: '100px',
            onButtonClick: function (row, grid) {
                rowselectedFile = row;
                download();
            },
            buttonStyle: {cssClasses: [buttonPropertiesClass]}
        }, {
            id: 'gridTemplateButtonDelete',
            title: '',
            dataType: 'button',
            buttonLabel: 'Delete'.translate(),
            width: '82px',
            onButtonClick: function (row, grid) {
                rowselectedFile = row;
                warningTemplate.open();
                warningTemplate.showFooter();
            },
            buttonStyle: {cssClasses: [buttonDeleteClass]}
        }
        ]
    });
    var buttonPublicCreate = new PMUI.ui.Button({
        id: 'buttonPublicCreate',
        text: 'Create'.translate(),
        height: "38px",
        width: 100,
        style: {cssClasses: ['mafe-button-create']},
        handler: function (event) {
            newfile();
        }

    });
    var buttonUpload = new PMUI.ui.Button({
        id: 'uploadBtn',
        text: 'Upload'.translate(),
        style: {cssClasses: ['mafe-button-upload'], cssProperties: {'margin-right': '5px', 'float': 'none'}},
        handler: function (event) {
            windowUpload.open();
            formUpload.setFocus();
            applyStyleWindowForm(windowUpload);

        }
    });
    var gridPublic = new PMUI.grid.GridPanel({
        id: 'gridPublic',
        pageSize: 10,
        width: '96%',
        style: {
            cssClasses: ["mafe-gridPanel"]
        },
        emptyMessage: 'No records found'.translate(),
        filterPlaceholder: 'Search ...'.translate(),
        nextLabel: 'Next'.translate(),
        previousLabel: 'Previous'.translate(),
        tableContainerHeight: 374,
        customStatusBar: function (currentPage, pageSize, numberItems, criteria, filter) {
            return messagePageGrid(currentPage, pageSize, numberItems, criteria, filter);
        },
        dataItems: {},
        columns: [{
            title: "PUBLIC".translate(),
            dataType: 'string',
            columnData: 'prf_filename',
            width: '704px',
            alignmentCell: 'left',
            sortable: true
        }, {
            dataType: 'string',
            columnData: 'prf_uid',
            alignmentCell: 'left',
            visible: false
        }, {
            id: 'gridPublicButtonDownload',
            title: '',
            dataType: 'button',
            buttonLabel: 'Download'.translate(),
            width: '111px',
            onButtonClick: function (row, grid) {
                rowselectedFile = row;
                download();
            },
            buttonStyle: {cssClasses: ['mafe-button-properties']}
        }, {
            id: 'gridPublicButtonDelete',
            title: '',
            dataType: 'button',
            buttonLabel: 'Delete'.translate(),
            width: '82px',
            onButtonClick: function (row, grid) {
                rowselectedFile = row;
                warningPublic.open();
                warningPublic.showFooter();
            },
            buttonStyle: {cssClasses: ['mafe-button-delete']}
        }
        ]
    });
    var windowUpload = new PMUI.ui.Window({
        id: 'windowUpload',
        title: 'Upload File'.translate(),
        height: 200,
        width: DEFAULT_WINDOW_WIDTH,
        onBeforeClose: function () {
            isDirtyUpload();
        },
        items: [
            formUpload = new PMUI.form.Form({
                id: 'formUpload',
                visibleHeader: false,
                items: [
                    formUploadField = new PMUI.field.UploadField({
                        id: 'formUploadField',
                        label: 'File'.translate(),
                        name: 'prf_file',
                        multiple: false,
                        labelWidth: '25%',
                        accept: 'text/html',
                        controlsWidth: 300
                    })
                ]
            })
        ],
        visibleFooter: true,
        buttonPanelPosition: 'bottom',
        buttonsPosition: 'right',
        buttons: [{
            id: 'windowUploadButtonCancel',
            text: 'Cancel'.translate(),
            handler: function () {
                isDirtyUpload();
            },
            buttonType: "error"
        }, {
            id: 'windowUploadButtonUpload',
            text: 'Upload'.translate(),
            handler: function () {
                uploadFile();
            },
            buttonType: "success"
        }
        ]
    });
    /**
     * Create window TinyMCE
     */
    function createWindowTinyMCE() {
        windowEdit = new PMUI.ui.Window({
            id: 'windowEdit',
            title: '',
            height: DEFAULT_WINDOW_HEIGHT - 80,
            width: DEFAULT_WINDOW_WIDTH,
            onBeforeClose: function () {
                PMDesigner.hideAllTinyEditorControls();
                clickedClose = true;
                isDirtyFormEdit();
            },
            items: [
                formEdit = new PMUI.form.Form({
                    id: 'formEdit',
                    visibleHeader: false,
                    width: 900,
                    items: [
                        new PMUI.field.TextField({
                            id: 'filename',
                            name: 'filename',
                            label: 'Filename'.translate(),
                            placeholder: 'Insert file name'.translate(),
                            required: true,
                            valueType: 'string',
                            labelWidth: "15%",
                            controlsWidth: 300,
                            validators: [{
                                pmType: "regexp",
                                criteria: /^[a-zA-Z0-9-_ ]*$/,
                                errorMessage: "File name is invalid".translate()
                            }]
                        }),
                        new PMUI.field.TextAreaField({
                            id: 'filecontent',
                            name: 'filecontent',
                            label: 'Content'.translate(),
                            value: '',
                            rows: 210,
                            labelWidth: "15%",
                            controlsWidth: 720,
                            onChange: function (currentValue, previousValue) {
                            },
                            style: {cssClasses: ['mafe-textarea-resize']}
                        })
                    ]
                })
            ],
            buttonPanelPosition: 'bottom',
            buttonsPosition: 'right',
            buttons: [{
                id: 'windowEditButtonCancel',
                text: 'Cancel'.translate(),
                buttonType: 'error',
                handler: function () {
                    PMDesigner.hideAllTinyEditorControls();
                    clickedClose = false;
                    isDirtyFormEdit();
                }
            }, {
                id: 'windowEditButtonSave',
                text: "Save".translate(),
                buttonType: 'success',
                handler: function () {
                    if (formEdit.isValid()) {
                        PMDesigner.hideAllTinyEditorControls();
                        $(".mceSplitButtonMenu").hide();
                        save();
                    }
                }
            }
            ]
        });
        formEdit.getField('filename').enable();
        formEdit.getField('filecontent').getControls()[0].getHTML().className = 'tmceEditor';
        windowEdit.open();
        windowEdit.showFooter();
        applyStyleWindowForm(windowEdit);
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
            theme_advanced_source_editor_width: DEFAULT_WINDOW_WIDTH - 50,
            theme_advanced_source_editor_height: DEFAULT_WINDOW_HEIGHT - 100,
            plugins: "advhr,advimage,advlink,advlist,autolink,autoresize,contextmenu,directionality,emotions,example,example_dependency,fullpage,fullscreen,iespell,inlinepopups,insertdatetime,layer,legacyoutput,lists,media,nonbreaking,noneditable,pagebreak,paste,preview,print,save,searchreplace,style,tabfocus,table,template,visualblocks,visualchars,wordcount,xhtmlxtras,pmSimpleUploader,pmVariablePicker,style",
            theme_advanced_buttons1: 'pmSimpleUploader,|,pmVariablePicker,|,bold,italic,underline,|,justifyleft,justifycenter,justifyright,justifyfull,|,fontselect,fontsizeselect,|,cut,copy,paste',
            theme_advanced_buttons2: 'bullist,numlist,|,outdent,indent,blockquote,|,tablecontrols,|,undo,redo,|,link,unlink,image,|,forecolor,backcolor,styleprops',
            theme_advanced_buttons3: 'hr,removeformat,visualaid,|,sub,sup,|,ltr,rtl,|,code',
            popup_css: "/js/tinymce/jscripts/tiny_mce/themes/advanced/skins/default/dialogTinyBpmn.css",
            oninit: initTinyMCE,
            onchange_callback: function (inst) {
                formEdit.getField('filecontent').setValue(tinyMCE.activeEditor.getContent({format: 'raw'}));
            },
            handle_event_callback: function (e) {
            },
            setup: function (ed) {
                ed.onSetContent.add(function (ed, l) {
                    formEdit.getField('filecontent').setValue(tinyMCE.activeEditor.getContent({format: 'raw'}));
                });
            },
            oninit: function () {
                tinyMCE.activeEditor.processID = PMDesigner.project.id;
            }
        });
        validateKeysField(formEdit.getField('filename').getControls()[0].getHTML(), ['isbackspace', 'isnumber', 'isletter', 'isunderscore', 'ishyphen', 'isparenthesisopening', 'isparenthesisclosing']);
        windowEdit.footer.getItems()[0].setHeight(38);
        windowEdit.footer.getItems()[1].setHeight(38);
        document.getElementById(windowEdit.footer.getItems()[0].id).style.lineHeight = '18px';
        document.getElementById(windowEdit.footer.getItems()[1].id).style.lineHeight = '18px';
    }
    /**
     * Create and open HTML Editor
     */
    function openHtmlEditor(rowSelected) {
        var buttonCancel,
            buttonSave,
            contentHTML,
            rowData = rowSelected.getData(),
            factorWidth = 0.9,
            factorHeight = 0.7;

        //CodeMirror
        editorHTML = new PMCodeMirror({
            id: "editCodeHTML"
        });

        //Footer´s buttons
        buttonSave = new PMUI.ui.Button({
            id: 'saveHtmlEditor',
            text: "Apply".translate(),
            buttonType: 'success',
            handler: function (event) {
                saveHtmlEditor();
            }
        });
        buttonCancel = new PMUI.ui.Button({
            id: 'cancelHtmlEditor',
            text: "Cancel".translate(),
            buttonType: 'error',
            handler: function (event) {
                windowCode.close();
            }
        });
        //Create Window with Code Mirror
        windowCode = new PMUI.ui.Window({
            id: 'windowCode',
            title: 'HTML Editor'.translate(),
            height: DEFAULT_WINDOW_HEIGHT * 0.9,
            width: DEFAULT_WINDOW_WIDTH,
            footerItems: [
                buttonCancel,
                buttonSave
            ],
            buttonPanelPosition: "bottom",
            footerAlign: "right"
        });

        windowCode.open();
        windowCode.showFooter();
        windowCode.addItem(editorHTML);
        contentHTML = $.isPlainObject(rowData) && !$.isEmptyObject(rowData) ? rowData.prf_content : "";
        editorHTML.cm.setSize(windowCode.getWidth() * factorWidth, windowCode.getHeight() * factorHeight);
        editorHTML.cm.setValue(contentHTML);
        editorHTML.related_row = rowData.prf_uid;

        // Apply styles
        $(".CodeMirror.cm-s-default.CodeMirror-wrap").css({
            "margin": "10px 0 0 20px",
            "border": "1px solid #c0c0c0"
        });
        $(".CodeMirror.cm-s-default.CodeMirror-wrap").after($ctrlSpaceMessage.css({
            "margin": "5px 5px 5px 20px"
        }));
        $(".pmui-window-body").css("overflow", "hidden");
        editorHTML.cm.refresh();
    }

    function newfile() {
        windowFilesManager.close();

        initTinyMCE = function () {
            tinyMCE.activeEditor.domainURL = "/sys" + WORKSPACE + "/" + LANG + "/" + SKIN + "/";
            tinyMCE.activeEditor.processID = PMDesigner.project.id;
        };
        createWindowTinyMCE();
        typeSave = 'new';
        var title = (processFileManagerOptionPath == 'templates') ? "Create ".translate() + " " + processFileManagerOptionPath.substring(0, processFileManagerOptionPath.length - 1).translate() : "Create ".translate() + " " + processFileManagerOptionPath.translate() + " " + "file".translate();
        windowEdit.setTitle(title.translate());
        var closeElement = windowEdit.header.childNodes[1];
        if (closeElement.addEventListener) {
            closeElement.addEventListener("click", function () {
                $(".mceSplitButtonMenu").hide();
            }, false);
        } else {
            closeElement.attachEvent("click", function () {
                $(".mceSplitButtonMenu").hide();
            });
        }
        formEdit.setFocus();
    }

    function editfile() {
        var title,
            closeElement;

        windowFilesManager.close();
        initTinyMCE = function () {
            tinyMCE.activeEditor.domainURL = "/sys" + WORKSPACE + "/" + LANG + "/" + SKIN + "/";
            tinyMCE.activeEditor.processID = PMDesigner.project.id;
        };
        createWindowTinyMCE();
        typeSave = 'update';
        title = "Edit ".translate() + " " + processFileManagerOptionPath.substring(0, processFileManagerOptionPath.length - 1).translate();
        if (windowEdit && formEdit) {
            windowEdit.setTitle(title.translate());
            closeElement = windowEdit.header.childNodes[1];
            if (closeElement.addEventListener) {
                closeElement.addEventListener("click", function () {
                    $(".mceSplitButtonMenu").hide();
                }, false);
            } else {
                closeElement.attachEvent("click", function () {
                    $(".mceSplitButtonMenu").hide();
                });
            }
            formEdit.getField('filename').getControl().setStyle({cssProperties: {"background": "#EEEEEE"}});
            formEdit.getField('filename').disable();
            formEdit.getField('filename').setValue(rowselectedFile.getData().prf_filename);
            var a = rowselectedFile.getData().prf_content;
            formEdit.getField('filecontent').setValue(a);
            if (tinyMCE.activeEditor) {
                tinyMCE.activeEditor.setContent(a);
            }
        }
    }

    function save() {
        var flagAux;

        if (!formEdit.isValid()) {
            flagAux = formEdit.visible;
        } else {
            flagAux = formEdit.isValid();
        }

        if (flagAux) {
            if (getData2PMUI(formEdit.html).filename == "") {
                return false;
            }
        }

        if ((navigator.userAgent.indexOf("MSIE") != -1) || (navigator.userAgent.indexOf("Trident") != -1)) {
            var data = getData2PMUI(formEdit.html);
        } else {
            var data = formEdit.getData();
        }

        if (typeSave === 'new') {
            (new PMRestClient({
                endpoint: 'file-manager',
                typeRequest: 'post',
                messageError: '',
                data: {
                    prf_filename: data.filename + ".html",
                    prf_path: processFileManagerOptionPath,
                    prf_content: data.filecontent
                },
                functionSuccess: function (xhr, response) {
                    windowEdit.close();
                    if (processFileManagerOptionPath == "templates") {
                        PMDesigner.msgFlash('File saved successfully'.translate(), gridTemplate);
                        windowFilesManager.open();
                        loadTemplate();
                    }
                    if (processFileManagerOptionPath == "public") {
                        PMDesigner.msgFlash('File saved successfully'.translate(), gridPublic);
                        windowFilesManager.open();
                        loadPublic();
                    }
                    formEdit.getField('filename').setValue('');
                },
                functionFailure: function (xhr, response) {
                    PMDesigner.msgWinError(response.error.message);
                }
            })).executeRestClient();
        }
        if (typeSave === 'update') {
            (new PMRestClient({
                endpoint: 'file-manager/' + rowselectedFile.getData().prf_uid,
                typeRequest: 'update',
                messageError: '',
                data: {
                    prf_content: tinyMCE.activeEditor.getContent()
                },
                functionSuccess: function (xhr, response) {
                    windowEdit.close();
                    if (processFileManagerOptionPath == "templates") {
                        PMDesigner.msgFlash('File updated successfully'.translate(), gridTemplate);
                        windowFilesManager.open();
                        loadTemplate();
                    }
                    if (processFileManagerOptionPath == "public") {
                        PMDesigner.msgFlash('File updated successfully'.translate(), gridPublic);
                        windowFilesManager.open();
                        loadPublic();
                    }
                    formEdit.getField('filename').setValue('');
                },
                functionFailure: function (xhr, response) {
                    PMDesigner.msgWinError(response.error.message);
                }
            })).executeRestClient();
        }
    }
    /**
     * Save Content of the Html Editor
     */
    function saveHtmlEditor() {
        var index = 0,
            codeMirror = windowCode.getItems()[index],
            contentCodeMirror = codeMirror.getValue(),
            rowRelatedUID = codeMirror.related_row,
            request;


        request = new PMRestClient({
            endpoint: 'file-manager/' + rowRelatedUID,
            typeRequest: 'update',
            messageError: 'Error Update File'.translate(),
            data: {
                prf_content: contentCodeMirror
            },
            functionSuccess: function (xhr, response) {
                windowCode.close();
                if (processFileManagerOptionPath == "templates") {
                    PMDesigner.msgFlash('File updated successfully'.translate(), gridTemplate);
                    windowFilesManager.open();
                    loadTemplate();
                }
                if (processFileManagerOptionPath == "public") {
                    PMDesigner.msgFlash('File updated successfully'.translate(), gridPublic);
                    windowFilesManager.open();
                    loadPublic();
                }
            },
            functionFailure: function (xhr, response) {
                PMDesigner.msgWinError(response.error.message);
            }
        });
        request.executeRestClient();
    }

    function loadFileManager() {
        (new PMRestClient({
            endpoint: 'file-manager',
            typeRequest: 'get',
            messageError: '',
            functionSuccess: function (xhr, response) {
                gridFilesManager.clearItems();
                for (var i = 0; i < response.length; i++) {
                    gridFilesManager.addDataItem({prf_path: response[i].name});
                }
                if (openCreateTemplates) {
                    gridFilesManager.onRowClick(gridFilesManager.getItems()[0]);
                    newfile();
                }
            },
            functionFailure: function (xhr, response) {
                PMDesigner.msgWinError(response.error.message);
            }
        })).executeRestClient();
    }

    function loadTemplate() {
        (new PMRestClient({
            endpoint: 'file-manager',
            typeRequest: 'get',
            messageError: '',
            data: {
                path: processFileManagerOptionPath,
                get_content: true
            },
            functionSuccess: function (xhr, response) {
                gridTemplate.clearItems();
                gridTemplate.clearItemsColumns();
                for (var i = 0; i < response.length; i++) {
                    gridTemplate.addDataItem({
                        prf_filename: response[i].prf_filename,
                        prf_content: response[i].prf_content,
                        prf_uid: response[i].prf_uid,
                        prf_assigned_routing_screen: response[i].prf_derivation_screen
                    });
                }
                gridTemplate.sort('prf_filename', 'asc');
            },
            functionFailure: function (xhr, response) {
                PMDesigner.msgWinError(response.error.message);
            }
        })).executeRestClient();
        checkDerivationScreen();
    }

    function loadPublic() {
        (new PMRestClient({
            endpoint: 'file-manager',
            typeRequest: 'get',
            messageError: '',
            data: {
                path: processFileManagerOptionPath,
                get_content: false
            },
            functionSuccess: function (xhr, response) {
                gridPublic.clearItems();
                for (var i = 0; i < response.length; i++) {
                    gridPublic.addDataItem({
                        prf_filename: response[i].prf_filename,
                        prf_uid: response[i].prf_uid
                    });
                }
                gridPublic.sort('prf_filename', 'asc');
            },
            functionFailure: function (xhr, response) {
                PMDesigner.msgWinError(response.error.message);
            }
        })).executeRestClient();
    }

    function download() {
        var xhr,
            win = window,
            value = 'blob',
            url = HTTP_SERVER_HOSTNAME + '/api/1.0/' + WORKSPACE + '/project/' + PMDesigner.project.id + '/file-manager/' + rowselectedFile.getData().prf_uid + '/download';

        if (win.XMLHttpRequest)
            xhr = new XMLHttpRequest();
        else if (win.ActiveXObject)
            xhr = new ActiveXObject('Microsoft.XMLHTTP');
        win.URL = win.URL || win.webkitURL;

        xhr.open('GET', url, true);
        xhr.responseType = value;
        xhr.setRequestHeader('Authorization', 'Bearer ' + PMDesigner.project.keys.access_token);
        xhr.onload = function () {
            if (this.status === 200) {
                if (processFileManagerOptionPath == "templates") {
                    PMDesigner.msgFlash('Start file download successfully'.translate(), gridTemplate);
                }
                if (processFileManagerOptionPath == "public") {
                    PMDesigner.msgFlash('Start file download successfully'.translate(), gridPublic);
                }
                var doc = win.document, a = doc.createElementNS('http://www.w3.org/1999/xhtml', 'a'), event = doc.createEvent('MouseEvents');
                event.initMouseEvent('click', true, false, win, 0, 0, 0, 0, 0, false, false, false, false, 0, null);
                var blob = xhr.response;
                if ((navigator.userAgent.indexOf("MSIE") != -1) || (navigator.userAgent.indexOf("Trident") != -1) || (navigator.userAgent.indexOf("Edge") != -1)) {
                    window.navigator.msSaveBlob(blob, rowselectedFile.getData().prf_filename);
                }
                else {
                    a.href = win.URL.createObjectURL(blob);
                    a.download = rowselectedFile.getData().prf_filename;
                    a.dispatchEvent(event);
                }
            }
        };
        xhr.send()


    }

    function uploadFile() {
        var fileSelector = formUploadField.getHTML().getElementsByTagName('input')[0];
        if (fileSelector.files.length === 0) {
            PMDesigner.msgFlash('Please select a file to upload'.translate(), windowUpload.footer, "info");
            return;
        }
        (new PMRestClient({
            endpoint: 'file-manager',
            typeRequest: 'post',
            messageError: '',
            data: {
                prf_filename: fileSelector.files[0].name,
                prf_path: processFileManagerOptionPath,
                prf_content: null
            },
            functionSuccess: function (xhr, response) {
                var win = window, fd = new FormData(), xhr, val = 'prf_file';
                fd.append(val, fileSelector.files[0]);
                if (win.XMLHttpRequest)
                    xhr = new XMLHttpRequest();
                else if (win.ActiveXObject)
                    xhr = new ActiveXObject('Microsoft.XMLHTTP');
                xhr.open('POST', '/api/1.0/' + WORKSPACE + '/project/' + PMDesigner.project.id + '/file-manager/' + response.prf_uid + '/upload', true);
                xhr.setRequestHeader('Authorization', 'Bearer ' + PMDesigner.project.keys.access_token);
                xhr.onload = function () {
                    if (this.status === 200) {
                        formUploadField.reset();
                        windowUpload.close();
                        if (processFileManagerOptionPath == "templates") {
                            PMDesigner.msgFlash('File uploaded successfully'.translate(), gridTemplate);
                            loadTemplate();
                        }
                        if (processFileManagerOptionPath == "public") {
                            PMDesigner.msgFlash('File uploaded successfully'.translate(), gridPublic);
                            loadPublic();
                        }
                    }
                };
                xhr.send(fd);
            },
            functionFailure: function (xhr, response) {
                PMDesigner.msgWinError(response.error.message);
            }
        })).executeRestClient();
    }

    function styleApp() {
        try {
            gridFilesManager.dom.toolbar.style.display = 'none';
            gridTemplate.dom.toolbar.getElementsByTagName('input')[0].style.visibility = 'hidden';
            gridPublic.dom.toolbar.getElementsByTagName('input')[0].style.visibility = 'hidden';

        } catch (e) {
        }
    }

    function disableAllFields() {
        windowFilesManager.getItems()[0].setVisible(false);

    }

    function openFolder() {
        disableAllFields();
        styleApp();

        if (processFileManagerOptionPath == "templates") {
            windowFilesManager.setTitle("Templates".translate());
            loadTemplate();
            if (flagGridTemplate) {
                windowFilesManager.addItem(gridTemplate);
                flagGridTemplate = false;
            }

            var buttonsTemplate = document.createElement("div");
            buttonsTemplate.id = "buttonsTemplate";
            buttonsTemplate.style.display = "inlineBlock";
            buttonsTemplate.style.float = "right";

            buttonsTemplate.appendChild(buttonUpload.getHTML());
            buttonsTemplate.appendChild(buttonNew.getHTML());
            //buttonsTemplate.appendChild(buttonBack.getHTML());

            buttonUpload.defineEvents();
            buttonNew.defineEvents();
            //buttonBack.defineEvents();

            gridTemplate.dom.toolbar.appendChild(buttonsTemplate);
            gridTemplate.setVisible(true);
            gridTemplate.dom.toolbar.getElementsByTagName('input')[0].style.visibility = 'visible';
            buttonNew.getHTML().style.top = '-3px';
        }
        if (processFileManagerOptionPath == "public") {
            windowFilesManager.setTitle("Public Files".translate());
            loadPublic();
            if (flagGridPublic) {
                windowFilesManager.addItem(gridPublic);
                flagGridPublic = false;
            }
            var buttonsPublic = document.createElement("div");
            buttonsPublic.id = "buttonsPublic";
            buttonsPublic.style.display = "inlineBlock";
            buttonsPublic.style.float = "right";

            buttonsPublic.appendChild(buttonUpload.getHTML());
            buttonsPublic.appendChild(buttonPublicCreate.getHTML());
            //buttonsPublic.appendChild(buttonBack.getHTML());

            buttonUpload.defineEvents();
            buttonPublicCreate.defineEvents();
            //buttonBack.defineEvents();

            gridPublic.dom.toolbar.appendChild(buttonsPublic);
            gridPublic.dom.toolbar.getElementsByTagName('input')[0].style.visibility = 'visible';
            gridPublic.setVisible(true);
            buttonPublicCreate.getHTML().style.top = '-3px';
        }
    }
    /**
     * Check Templates Assigned Derivation Screen
     * @returns {checkDerivationScreen}
     */
    function checkDerivationScreen () {
        var gridTemplateAux = gridTemplate,
            idRichText = "gridTemplateButtonRichTextEditor",
            rowsGrid,
            columnsGrid,
            dataRowGrid,
            columnRichText,
            index = 0,
            cell = {},
            message = "Editor unavailable",
            tooltipMafeClass = "mafe-action-tooltip-black",
            i,
            max;

        rowsGrid = gridTemplateAux.getItems();
        columnsGrid = gridTemplateAux.getColumns();
        columnRichText = $.grep(columnsGrid, function (item, index) {
            return item.id === idRichText;
        });
        for (i = 0, max = rowsGrid.length; i < max; i += 1) {
            dataRowGrid = rowsGrid[i].data.customKeys;
            if (dataRowGrid.prf_assigned_routing_screen && columnRichText.length) {
                columnRichText[index].disableCell(i);
                cell = columnRichText[index].getCells().length ? columnRichText[index].getCells()[i] : cell;
                if (!$.isEmptyObject(cell)) {
                    $(cell.getHTML()).attr('title', message).tooltip({
                        tooltipClass: tooltipMafeClass,
                        position: {
                            my: 'center top',
                            at: 'center bottom+4'
                        }
                    });
                }
            }
        }
        return this;
    }

    if (optionCreation == "CREATION_NORMAL") {
        windowFilesManager.open();
        openFolder();
    } else {
        windowFilesManager.open();
        openFolder();
        newfile();
    }
    checkDerivationScreen();
};

PMDesigner.ProcessFilesManager.createFirst = function (processFileManagerOptionPath, optionCreation) {
    PMDesigner.ProcessFilesManager(processFileManagerOptionPath, optionCreation);
};
