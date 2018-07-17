/**
 * Updates the granular export feature after enabled features are loaded.
 */
var navbarExportUpdate = function () {
    //Code export - export granular (handler)
    var handlerExportNormal = function () {
        var ws = enviromentVariables('WORKSPACE');
        if (!HTTP_SERVER_HOSTNAME) {
            HTTP_SERVER_HOSTNAME = window.location.protocol + "//" + window.location.hostname + (window.location.port ? ':' + window.location.port : '');
        }
        location.href = HTTP_SERVER_HOSTNAME + "/api/1.0/" + ws + "/project/" + PMDesigner.project.id + "/export?access_token=" + PMDesigner.project.keys.access_token;
    };

    var handlerExportGranular = function () {
        var optionExportNormal = $("<div class='mafe-button-submenu-option normalExport'>" + "Normal".translate() + "</div>"),
            optionExportGranular = $("<div class='mafe-button-submenu-option granularExport'>" + "Granular".translate() + "</div>"),
            menuExport = $("<div class='mafe-button-menu-container sub-nav'></div>").hide(),
            $item = $("#idNavBar").find(".mafe-button-export-process").closest("li");

        menuExport.append(optionExportNormal).append(optionExportGranular);
        if (!$item.find(".mafe-button-menu-container").length) {
            $item.append(menuExport);
        }
        $(".ui-tooltip").hide();
        $(menuExport).slideToggle("slow");
        $item.on("mouseleave", function (e) {
            if ($(this).find(".mafe-button-menu-container").eq(0).is(":visible")) {
                var that = this;
                $(that).find(".mafe-button-menu-container").remove();
            }
        });

        $(".sub-nav").on("click", ".normalExport", function (event) {
            var ws = enviromentVariables('WORKSPACE'),
                locationOrigin;
            if (!window.location.origin) {
                locationOrigin = window.location.protocol + "//" + window.location.hostname +
                    (window.location.port ? ':' + window.location.port : '');
            } else if (typeof HTTP_SERVER_HOSTNAME !== 'undefined') {
                locationOrigin = HTTP_SERVER_HOSTNAME;
            } else {
                locationOrigin = window.location.origin;
            }
            location.href = locationOrigin + "/api/1.0/" + ws + "/project/" + PMDesigner.project.id + "/export?access_token=" + PMDesigner.project.keys.access_token;
        });

        $(".sub-nav").on("click", ".granularExport", function (event) {
            PMDesigner.granularProcessExport();
        });
    };
    if (inArray("jXsSi94bkRUcVZyRStNVExlTXhEclVadGRRcG9xbjNvTWVFQUF3cklKQVBiVT0=", ENABLED_FEATURES)) {
        handler = handlerExportGranular;
    } else {
        handler = handlerExportNormal;
    }

};

function inArray(needle, haystack) {
    var i,
        length = haystack.length;
    for (i = 0; i < length; i += 1) {
        if (haystack[i] == needle) return true;
    }
    return false;
}

var defaultNavbarPanelMenus = {
    array: [],
    init: function () {
        var closeVar = {
                id: 'closeButton',
                name: 'Close',
                htmlProperty: {
                    id: 'closeButton',
                    element: 'li',
                    child: [
                        {
                            element: 'a',
                            class: 'mafe-close',
                            child: [
                                {
                                    element: 'span',
                                    class: 'mafe-button-close'
                                }
                            ]
                        }

                    ]
                },
                actions: {
                    selector: ".mafe-button-close",
                    tooltip: "Close".translate(),
                    execute: true,
                    handler: function () {
                        var message_window,
                            browser = PMDesigner.getBrowser(),
                            url = parent.location.href;

                        if (PMDesigner.project.isDirty() && !PMDesigner.project.isSave) {
                            var message_window = new PMUI.ui.MessageWindow({
                                windowMessageType: 'warning',
                                width: 490,
                                bodyHeight: 'auto',
                                id: "cancelSaveSubprocPropertiesWin",
                                title: PMDesigner.project.projectName,
                                message: 'Save your changes and exit ?'.translate(),
                                footerItems: [
                                    {
                                        pmType: 'label',
                                        text: ' '
                                    }, {
                                        text: "No".translate(),
                                        handler: function () {
                                            PMDesigner.project.isClose = true;
                                            if ((navigator.userAgent.indexOf("MSIE") != -1) || (navigator.userAgent.indexOf("Trident") != -1)) {
                                                window.close();
                                            } else {
                                                parent.location.href = url;
                                            }
                                        },
                                        buttonType: "error"
                                    },

                                    {
                                        text: "Yes".translate(),
                                        handler: function () {
                                            PMDesigner.project.saveClose(true);
                                        },
                                        buttonType: "success"
                                    }
                                ]
                            });
                            message_window.open();
                            message_window.showFooter();
                        } else {
                            if ((navigator.userAgent.indexOf("MSIE") != -1) || (navigator.userAgent.indexOf("Trident") != -1)) {
                                window.close();
                            } else {
                                parent.location.href = url;
                            }
                        }
                    }
                }
            },
            helpVar = {
                id: 'helpButton',
                name: 'Help',
                htmlProperty: {
                    id: 'helpButton',
                    element: 'li',
                    child: [
                        {
                            element: 'a',
                            class: 'mafe-help',
                            child: [
                                {
                                    element: 'span',
                                    class: 'mafe-toolbar-help'
                                }
                            ]
                        }
                    ]
                },
                actions: {
                    selector: ".mafe-toolbar-help",
                    tooltip: "Help".translate(),
                    execute: true,
                    handler: function () {
                        var canvas = PMUI.getActiveCanvas();
                        PMDesigner.helper.startIntro();
                        if (canvas && canvas.getHTML()) {
                            canvas.getHTML().classList.remove("pmui-pmcanvas");
                        }
                    }
                }
            },
            saveVar = {
                id: 'saveButton',
                name: 'Save',
                htmlProperty: {
                    id: 'saveButton',
                    element: 'li',
                    class: 'mafe-save-process',
                    child: [
                        {
                            element: 'a',
                            class: 'mafe-button-save'
                        },
                        {
                            element: 'span',
                            class: 'mafe-button-menu',
                            child: [
                                {
                                    element: 'img',
                                    src: '/lib/img/caret-down.png'
                                }
                            ]
                        }
                    ]
                },
                actions: {
                    selector: ".mafe-button-save",
                    tooltip: "Save process".translate(),
                    label: {
                        text: "Save".translate()
                    },
                    execute: true,
                    handler: function () {
                        if (PMDesigner.project.isDirty() && PMDesigner.project.isSave === false) {
                            PMDesigner.project.isSave = true;
                            document.getElementsByClassName("mafe-save-process")[0].childNodes[0].text = "Saving";
                            PMDesigner.project.save(true);
                        }
                    }
                }
            },
            exportButton = {
                id: 'exportButton',
                name: 'ExportButton',
                htmlProperty: {
                    id: 'exportButton',
                    element: 'li',
                    child: [
                        {
                            element: 'a',
                            class: 'mafe-button-export-process'
                        }
                    ]
                },
                actions: {
                    selector: ".mafe-button-export-process",
                    tooltip: "Export process".translate(),
                    label: {
                        text: "Export Process".translate()
                    },
                    execute: true,
                    handler: function () {
                        handler();
                    }
                }
            },
            exportBpmnButton = {
                id: 'exportBpmnButton',
                name: 'ExportBpmn',
                htmlProperty: {
                    id: 'exportBpmnButton',
                    element: 'li',
                    child: [
                        {
                            element: 'a',
                            class: 'mafe-button-export-bpmn-process'
                        }
                    ]
                },
                actions: {
                    selector: ".mafe-button-export-bpmn-process",
                    tooltip: "Export Diagram ".translate(),
                    label: {
                        text: "Export Diagram".translate()
                    },
                    execute: false,
                    handler: function () {
                    }
                }
            },
            zoomVar = {
                id: 'zoomOptions',
                name: 'Zoom',
                htmlProperty: {
                    id: 'zoomOptions',
                    element: 'li',
                    child: [
                        {
                            element: 'span',
                            class: 'mafe-zoom-options'
                        }
                    ]
                },
                actions: {
                    id: 'zoomOptions',
                    spanclass: 'mafe-zoom-options',
                    actions: 'zoom'
                },
                aditionalAction: {
                    execute: PMDesigner.ApplyOptionsZoom()
                }
            },
            undo = {
                id: 'undoButton',
                name: 'Undo',
                htmlProperty: {
                    id: 'undoButton',
                    element: 'li',
                    class: 'mafe-undo',
                    child: [
                        {
                            element: 'a',
                            child: [
                                {
                                    element: 'span',
                                    class: 'mafe-button-undo'
                                }
                            ]
                        }
                    ]
                },
                actions: {
                    selector: ".mafe-button-undo",
                    tooltip: "Undo Action".translate(),
                    label: {
                        text: ''
                    },
                    execute: true,
                    handler: function () {
                        PMUI.getActiveCanvas().hideDragConnectHandlers();
                        PMUI.getActiveCanvas().commandStack.undo();
                    }
                }
            },
            redo = {
                id: 'redoButton',
                name: 'Redo',
                htmlProperty: {
                    id: 'redoButton',
                    element: 'li',
                    class: 'mafe-redo',
                    child: [
                        {
                            element: 'a',
                            child: [
                                {
                                    element: 'span',
                                    class: 'mafe-button-redo'
                                }
                            ]
                        }
                    ]
                },
                actions: {
                    selector: ".mafe-button-redo",
                    tooltip: "Redo Action".translate(),
                    label: {
                        text: ''
                    },
                    execute: true,
                    handler: function () {
                        PMUI.getActiveCanvas().hideDragConnectHandlers();
                        PMUI.getActiveCanvas().commandStack.redo();
                    }
                }
            },
            fullScreen = {
                id: 'fullScreenButton',
                name: 'FullScreen',
                htmlProperty: {
                    id: 'fullScreenButton',
                    element: 'li',
                    child: [
                        {
                            element: 'a',
                            class: 'mafe-button-fullscreen'
                        }
                    ]
                },
                actions: {
                    selector: ".mafe-button-fullscreen",
                    tooltip: "Full Screen".translate(),
                    execute: true,
                    handler: function () {
                        PMDesigner.fullScreen.toggle(this);
                    }
                }
            };
        navbarExportUpdate();
        this.array = [
            closeVar,
            helpVar,
            saveVar,
            exportButton,
            exportBpmnButton,
            zoomVar,
            undo,
            redo,
            fullScreen
        ];
    },
    /**
     * Get Array of Items of the NavBarPanelMenu
     * @returns {Array}
     */
    getNavBarPanelMenu: function () {
        return this.array;
    },
    /**
     * Adds one Item To NavBarPanelMenu
     * @param item
     */
    addItemToNavBarPanelMenu: function (item) {
        this.array.push(item);
    }
};

defaultNavbarPanelMenus.init();