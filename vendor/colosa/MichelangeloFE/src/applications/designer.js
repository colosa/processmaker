var PMDesigner = {},
    LANG,
    WORKSPACE,
    SKIN,
    DEFAULT_WINDOW_WIDTH,
    DEFAULT_WINDOW_HEIGHT,
    ENABLED_FEATURES,
    DataDictionary,
    enviromentVariables,
    resizingFrame,
    ViewTaskInformation;

PMDesigner.defaultRules = window.defaultRules ? window.defaultRules : {};
PMDesigner.defaultCrown = window.defaultCrown ? defaultCrown : {};
PMDesigner.configCrown = window.configCrown ? configCrown : {};
PMDesigner.modelRules = new ModelRules(PMDesigner.defaultRules);
PMDesigner.modelCrown = new ModelCrown(PMDesigner.defaultCrown);
PMDesigner.remoteUrl = "";
PMDesigner.moddle = new BpmnModdle();
PMDesigner.bpmnFactory = new BpmnFactory(PMDesigner.moddle);
PMDesigner.keyCodeF5 = 116;
PMDesigner.shapeProperties = function (shape) {
    var typeShape = shape.type;
    switch (typeShape) {
        case "PMActivity":
            PMDesigner.activityProperties(shape);
            break;
        case "PMGateway":
            PMDesigner.gatewayProperties(shape);
            break;
        case "PMEvent":
            shape.eventProperties();
            break;
    }
};

/**
 * function to get the enviroment variables (WORKSPACE, LANG, SKIN)
 */
enviromentVariables = function (variable) {
    var url1, variables, WORKSPACE, LANG, SKIN;
    if (window.parent) {
        url1 = window.parent.location.pathname;
        variables = url1.split('/');
        WORKSPACE = variables[1];
        WORKSPACE = WORKSPACE.substring(3);
        LANG = variables[2];
        SKIN = variables[3];

        if (variable == 'WORKSPACE') {
            return WORKSPACE;
        } else if (variable == 'LANG') {
            return LANG;
        } else if (variable == 'SKIN') {
            return SKIN;
        } else {
            return null;
        }
    }
};

LANG = (typeof SYS_LANG !== "undefined") ? SYS_LANG : enviromentVariables('LANG');
WORKSPACE = (typeof SYS_SYS !== "undefined") ? SYS_SYS : enviromentVariables('WORKSPACE');
SKIN = (typeof SYS_SKIN !== "undefined") ? SYS_SKIN : enviromentVariables('SKIN');

DEFAULT_WINDOW_WIDTH = 943;
DEFAULT_WINDOW_HEIGHT = 520;
ENABLED_FEATURES = [];

if (LANG != 'en') {
    if (typeof __TRANSLATIONMAFE != "undefined" && typeof __TRANSLATIONMAFE[LANG] != 'undefined') {
        PMUI.loadLanguage(__TRANSLATIONMAFE.en, 'en');
        PMUI.loadLanguage(__TRANSLATIONMAFE[LANG], LANG);

        PMUI.setDefaultLanguage('en');
        PMUI.setCurrentLanguage(LANG);
    }
}

PMDesigner.resizeFrame = function () {
    if (parent.document.documentElement === document.documentElement) {
        jQuery(".content").css("height", parseInt(jQuery(window).height()));
    } else {
        jQuery(".content").css("height", document.body.clientHeight);

    }
};
resizingFrame = PMDesigner.resizeFrame;
PMDesigner.applyCanvasOptions = function () {
    list = new PMUI.control.DropDownListControl({
        options: [],
        style: {
            cssClasses: [
                "mafe-dropdown-zoom"
            ]
        },
        width: 150,
        onChange: function (newValue, previous) {
            var canvas = PMDesigner.project.diagrams.find('id', newValue);
            PMUI.getActiveCanvas().getHTML().style.display = 'none';
            PMUI.setActiveCanvas(canvas);
            canvas.getHTML().style.display = 'inline';
        }
    });
    //enable to support multidiagram
    //jQuery(jQuery(".navBar li")[6]).append(list.getHTML());
    list.defineEvents();
    PMDesigner.canvasList = list;
};
//Zoom
PMDesigner.ApplyOptionsZoom = function () {
    list = new PMUI.control.DropDownListControl({
        id: '_idListZoom',
        options: [
            {
                label: "50%",
                value: 1
            },
            {
                label: "75%",
                value: 2
            },
            {
                label: "100%",
                value: 3,
                selected: true
            },
            {
                label: "125%",
                value: 4
            },
            {
                label: "150%",
                value: 5
            }
        ],
        style: {
            cssClasses: [
                "mafe-dropdown-zoom"
            ]
        },
        onChange: function (newValue, previous) {
            var i;
            newValue = parseInt(newValue, 10);
            PMUI.getActiveCanvas().applyZoom(newValue);
        }
    });

    //jQuery(jQuery(".navBar li")[4]).append(list.getHTML());
    jQuery(jQuery(".mafe-zoom-options")).append(list.getHTML());

    list.defineEvents();
};
/**
 * hides all requiered TinyControls
 */
PMDesigner.hideAllTinyEditorControls = function () {
    var control,
        i,
        max,
        j,
        mapMax,
        editor,
        controlMap = [
            'tinyeditor_fontselect',
            'tinyeditor_fontsizeselect',
            'tinyeditor_bullist',
            'tinyeditor_numlist',
            'tinyeditor_forecolor',
            'tinyeditor_backcolor'
        ];
    for (i = 0, max = tinymce.editors.length; i < max; i += 1) {
        editor = tinymce.editors[i];
        jQuery.each(editor.controlManager.controls, function (index, val) {
            if (val && jQuery.isFunction(val.hideMenu)) {
                val.hideMenu();
            }
        });
    }

};


jQuery(document).ready(function ($) {
    var setSaveButtonDisabled, s, sidebarCanvas, project, d, downloadLink, handlerExportNormal, handlerExportGranular,
        handler, validatosr, help, option, menu, elem;
    /***************************************************
     * Defines the Process
     ***************************************************/
    if (typeof prj_uid === "undefined") {
        prj_uid = '';
    }
    if (typeof prj_readonly === "undefined") {
        prj_readonly = '';
    }
    if (typeof credentials === "undefined") {
        credentials = '';
    } else {
        credentials = RCBase64.decode(credentials);
        credentials = (credentials == '') ? "" : JSON.parse(credentials);
    }

    if (prj_readonly !== 'true') {
        $("#idContent").find(".content_controls").show();
        $(".bpmn_shapes").show();
        $('.bpmn_shapes_legend').hide();
        $("#idNavBar").show().css('height', '33px');
    }

    PMDesigner.createHTML();
    setSaveButtonDisabled = function (that) {
        if (that.isDirty()) {
            if (document.getElementsByClassName("mafe-save-process").length > 0) {
                document.getElementsByClassName("mafe-save-process")[0].removeAttribute("style");
                document.getElementsByClassName("mafe-save-process")[0].childNodes[0].style.color = "#FFF";

                var mafebuttonMenu = document.getElementsByClassName("mafe-button-menu")[0];
                mafebuttonMenu.style.backgroundColor = "#0C9778";
                mafebuttonMenu.firstChild.src = "/lib/img/caret-down-w.png";
            }
        } else {
            if (document.getElementsByClassName("mafe-save-process").length > 0) {
                document.getElementsByClassName("mafe-save-process")[0].style.backgroundColor = "#e8e8e8";
                document.getElementsByClassName("mafe-save-process")[0].style.color = "#000";
                document.getElementsByClassName("mafe-save-process")[0].childNodes[0].style.color = "#000";
                document.getElementsByClassName("mafe-save-process")[0].childNodes[0].text = "Save".translate();

                var mafebuttonMenu = document.getElementsByClassName("mafe-button-menu")[0];
                mafebuttonMenu.style.backgroundColor = "#e8e8e8";
                mafebuttonMenu.firstChild.src = "/lib/img/caret-down.png";
            }
        }
    };
    sidebarCanvas = [];
    for (s = 0; s < PMDesigner.sidebar.length; s += 1) {
        sidebarCanvas = sidebarCanvas.concat(PMDesigner.sidebar[s].getSelectors());
        jQuery(".bpmn_shapes").append(PMDesigner.sidebar[s].getHTML());
    }
    //Adding Sidebar to DOM
    firstAbsuluteX = jQuery("#div-layout-canvas").offset().left;

    project = new PMProject({
        id: prj_uid,
        name: 'Untitled Process',
        keys: {
            access_token: credentials.access_token,
            expires_in: credentials.expires_in,
            token_type: credentials.token_type,
            scope: credentials.scope,
            refresh_token: credentials.refresh_token,
            client_id: credentials.client_id,
            client_secret: credentials.client_secret
        },
        listeners: {
            create: function (self, element) {
                var sh, i,
                    contDivergent = 0,
                    contConvergent = 0;
                //Updating the background color for connections
                jQuery(".pmui-intersection > div > div").css("background-color", "black");

                if (element.type == "Connection") {
                    ///////////****************Changing the gatDirection*******************//////////////////
                    if (element.relatedObject.srcPort.parent.gat_type === "PARALLEL" ||
                        element.relatedObject.srcPort.parent.gat_type === "INCLUSIVE" ||
                        element.relatedObject.destPort.parent.gat_type === "PARALLEL" ||
                        element.relatedObject.destPort.parent.gat_type === "INCLUSIVE") {
                        if (element.relatedObject.srcPort.parent.gat_type !== undefined) {
                            sh = element.relatedObject.srcPort.parent;
                        } else {
                            sh = element.relatedObject.destPort.parent;
                        }

                        if (sh.gat_direction === "DIVERGING") {
                            for (i = 0; i < sh.ports.asArray().length; i += 1) {
                                if (sh.ports.asArray()[i].connection.flo_element_origin_type === "bpmnActivity") {
                                    contDivergent += 1;
                                }
                                if (contDivergent > 1) {
                                    sh.gat_direction = "CONVERGING";
                                    i = sh.ports.asArray().length;
                                }
                            }
                        }
                        if (sh.gat_direction === "CONVERGING") {
                            for (i = 0; i < sh.ports.asArray().length; i += 1) {
                                if (sh.ports.asArray()[i].connection.flo_element_origin_type === "bpmnGateway") {
                                    contConvergent += 1;
                                }
                                if (contConvergent > 1) {
                                    sh.gat_direction = "DIVERGING";
                                    i = sh.ports.asArray().length;
                                }
                            }
                        }

                    }
                }
                setSaveButtonDisabled(self);
            },
            update: function (self) {
                //Updating the background color for connections
                jQuery(".pmui-intersection > div > div").css("background-color", "black");
                setSaveButtonDisabled(self);
            },
            remove: function (self) {
                setSaveButtonDisabled(self);
            },
            success: function (self, xhr, response) {
                var message;
                self.dirty = false;
                setSaveButtonDisabled(self);
                self.dirtyElements[0] = {
                    laneset: {},
                    lanes: {},
                    activities: {},
                    events: {},
                    gateways: {},
                    flows: {},
                    artifacts: {},
                    lines: {},
                    data: {},
                    participants: {},
                    startMessageEvent: {},
                    startTimerEvent: {}
                };
                self.updateIdentifiers(response);
                PMDesigner.connectValidator.bpmnValidator();
                //if (PMDesigner.currentMsgFlash) {
                PMDesigner.msgFlash('The process was saved successfully.'.translate(), document.body, 'success', 3000, 5);
                PMDesigner.RoutingRuleSetOrder();
                //}

            },
            failure: function (self, xhr, response) {
                var message;
                if (response.error.code === 401) {
                    /*message = new PMUI.ui.FlashMessage({
                     message: "It was not possible to establish a connection with the server".translate(),
                     duration: 5000,
                     appendTo: document.body,
                     severity: 'info'
                     });
                     message.show();*/
                    //self.remoteProxy.setUrl("/"+WORKSPACE+"/oauth2/token");
                    //self.setRefreshToken();
                    //self.remoteProxy.setUrl("/api/1.0/"+WORKSPACE+"/project/"+prj_uid);
                    //self.save();
                } else {
                    PMDesigner.msgFlash('Error saving the process.'.translate(), document.body, 'error', 3000, 5);
                    self.updateIdentifiers(response);
                }
            }
        }
    });
    PMDesigner.project = project;
    //create a new restApi
    PMDesigner.restApi = RestApi.createRestApi({
        serverUrl: '/rest/v10',
        keys: PMDesigner.project.keys
    });
    systemRest = new PMRestClient({
        typeRequest: 'post',
        multipart: true,
        data: {
            calls: [{
                url: 'system/enabled-features',
                method: 'GET'
            }
            ]
        },
        functionSuccess: function (xhr, response) {
            var result = response.pop();
            ENABLED_FEATURES = result.response;
            navbarExportUpdate();
        },
        functionFailure: function (xhr, response) {
            ENABLED_FEATURES = [];
        }
    }).setBaseEndPoint('').executeRestClient();
    PMDesigner.connectValidator = new ConnectValidator();
    for (d = 0; d < PMDesigner.sidebar.length; d += 1) {
        PMDesigner.sidebar[d].activate();
    }

    $('.bpmn_shapes_legend').hide();
    project.remoteProxy.setUrl(HTTP_SERVER_HOSTNAME + "/api/1.0/" + WORKSPACE + "/project/" + prj_uid);
    project.load();

    /*===========================================
     =            ProcessMaker module            =
     ===========================================*/

    //Renders content controls
    PMDesigner.contentControl.show();
    new PMAction({
        selector: ".mafe-menu-eventmessages-create",
        label: {
            selector: ".mafe-menu-eventmessages-create span",
            text: "Create".translate()
        },
        execute: true,
        handler: function () {
            PMDesigner.eventMessages.create();
        }
    });

    /*-----  End of ProcessMaker module  ------*/

    /*========================================
     =            Designer buttons            =
     ========================================*/

    //Renders navBar Panel
    PMDesigner.navbarPanel.show();
    // create Zoom options
    PMDesigner.ApplyOptionsZoom();
    //the action to generate a .bpmn file with the export option.
    downloadLink = $('.mafe-button-export-bpmn-process');
    downloadLink.click(function (e) {
        PMDesigner.moddle.toXML(PMDesigner.businessObject, function (err, xmlStrUpdated) {

            setEncoded(downloadLink, PMDesigner.project.projectName + '.bpmn', xmlStrUpdated);

            // xmlStrUpdated contains new id and the added process
        });

    });
    option = $("<div class='mafe-button-menu-option'>" + "Save as".translate() + "</div>");
    /**
     * Add data tables
     */
    $('body').append('<div class="bpmn_validator"><div class="validator_header"></div><div class="validator_body"></div></div>')
    $('.validator_header').append('<h2> Validator</h2>');
    $('.validator_header').append('<a class="validator-close" href="#"><span class="mafe-validator-close" title=""></span></a>');
    $('.validator_body').html('<table cellpadding="0" cellspacing="0" border="0" class="display" id="validator-table" width="100%"></table>');
    PMDesigner.validTable = $('#validator-table').DataTable({
        paging: false,
        scrollY: 100,
        searching: false,
        "info": false,
        scrollCollapse: true,
        "columns": [
            {
                name: 'numrow',
                "title": "#",
                width: '5%',
                render: function (data, type, row, conf) {
                    return conf.row + 1;
                }
            },
            {
                name: 'id',
                className: 'never'
            },
            {
                "title": "Type".translate(),
                width: '10%',
                name: 'severity',
                render: function (data, type, row, conf) {
                    var clasMap = {
                        Error: 'mafe-icon-error',
                        Warning: 'mafe-icon-warning'
                    };
                    if (type === 'display') {
                        return ' <i class="' + clasMap[data] + '"></i> ' + data;
                    }
                    return data;
                }
            },
            {name: 'element', "title": "Element".translate(), width: '15%'},
            {name: 'element-type', "title": "Element Type".translate(), width: '15%'},
            {name: 'description', "title": "Description".translate(), width: '45%'}
        ]
    });
    jQuery('#validator-table tbody').on('click', 'tr', function () {
        var id = PMDesigner.validTable.row(this).data()[1],
            shape;
        if ($(this).hasClass('selected')) {
            $(this).removeClass('selected');
            PMUI.getActiveCanvas().hideAllCoronas().emptyCurrentSelection();
        }
        else {
            PMDesigner.validTable.$('tr.selected').removeClass('selected');
            $(this).addClass('selected');
            PMUI.getActiveCanvas().hideAllCoronas().emptyCurrentSelection();
            shape = PMUI.getActiveCanvas().items.find('id', id);
            PMUI.getActiveCanvas().addToSelection(shape.relatedObject);
        }
    });
    PMDesigner.validTable.columns([1]).visible(false);
    /********finish datatables********/
    new PMAction({
        selector: ".mafe-validator-close",
        tooltip: "Close Validator".translate(),
        execute: true,
        handler: function () {
            $('.bpmn_validator').css('visibility', 'hidden');
            $('.mafe-toolbar-validation').css('background-color', 'rgb(233, 233, 233)');
            PMDesigner.validator = false;
        }
    });

    menu = $("<div class='mafe-button-menu-container'></div>");
    menu.append(option);
    option.on("mouseout", function (e) {
        menu.hide();
    });
    option.on("click", function (e) {
        var saveas;
        menu.hide();
        PMDesigner.project.remoteProxy.setUrl(HTTP_SERVER_HOSTNAME + "/api/1.0/" + WORKSPACE + "/project/" + PMDesigner.project.id);
        PMDesigner.project.isSave = true;
        PMDesigner.project.save(true);
        saveas = new SaveAs();
        saveas.open();
    });
    $(".mafe-button-menu").on("click", function (e) {
        e.stopPropagation();
        $(".mafe-save-process").append(menu);
        menu.show();
    });

    /*-----  End of Designer buttons  ------*/
    /*=================================================
     =            Full screen functionality            =
     =================================================*/
    if (parent.document.documentElement === document.documentElement) {
        elem = document.documentElement;
    } else {
        elem = parent.document.getElementById("frameMain");
    }
    PMDesigner.fullScreen = new FullScreen({
        element: elem,
        onReadyScreen: function () {
            setTimeout(function () {
                PMDesigner.resizeFrame();
            }, 500);
        },
        onCancelScreen: function () {
            setTimeout(function () {
                PMDesigner.resizeFrame();
            }, 500);
        }
    });
    /*-----  End of Full screen functionality  ------*/

    /*=============================================
     =            Shapes and Controls Box            =
     =============================================*/
    PMDesigner.cookie = {
        name: "PMDesigner",
        object: {},
        get: function (cname) {
            var name = cname + "=", i, c,
                ca = document.cookie.split(';');
            for (i = 0; i < ca.length; i += 1) {
                c = ca[i].trim();
                if (c.indexOf(name) == 0)
                    return c.substring(name.length, c.length);
            }
            return "";
        },
        remove: function (cname) {
            jQuery.each(PMDesigner.cookie.object, function (index, val) {
                if (index === cname) {
                    delete PMDesigner.cookie.object[cname];
                    PMDesigner.cookie.refresh();
                }
            });
        },
        refresh: function () {
            document.cookie = PMDesigner.cookie.name + "=" + JSON.stringify(PMDesigner.cookie.object);
        }
    };
    PMDesigner.localStorage = {
        prefix: "PM_" + WORKSPACE + "_" + prj_uid,
        object: {},
        remove: function (cname) {
            var obj;
            obj = localStorage.getItem(PMDesigner.localStorage.prefix);
            obj = (obj === null) ? {} : JSON.parse(obj);
            if (obj[cname]) {
                delete obj[cname];
                localStorage.setItem(PMDesigner.localStorage.prefix, JSON.stringify(obj));
            }
        }
    };
    if (Modernizr.localstorage) {
        var localDesigner = localStorage.getItem(PMDesigner.localStorage.prefix);
        localDesigner = (localDesigner === null) ? {} : JSON.parse(localDesigner);
        PMDesigner.panelsPosition = localDesigner;
    } else {
        if (PMDesigner.cookie.get(PMDesigner.cookie.name) !== "") {
            var positions, pLeft, pTop, html;
            positions = JSON.parse(PMDesigner.cookie.get(PMDesigner.cookie.name));
            PMDesigner.cookie.object = positions;
            PMDesigner.panelsPosition = positions;
        }
    }
    if (typeof PMDesigner.panelsPosition === "object") {
        var pst = PMDesigner.panelsPosition;
        if (pst.navbar) {
            pLeft = pst.navbar.x;
            pTop = pst.navbar.y;
            html = document.getElementsByClassName("navBar")[0];
        }
        if (pst.bpmn) {
            pLeft = 0;
            pTop = 0;
            html = document.getElementsByClassName("bpmn_shapes")[0];
            html.style.left = pLeft + "px";
            html.style.top = pTop + "px";
        }
        if (pst.controls) {
            pLeft = pst.controls.x;
            pTop = pst.controls.y;
            html = document.getElementsByClassName("content_controls")[0];
            html.style.left = pLeft + "px";
            html.style.top = pTop + "px";
            if (pTop > 503) {
                $("#idContent").find(".content_controls").css({'top': '', 'left': ''});
            }
        }
    }
    jQuery(".bpmn_shapes").draggable({
        handle: "div",
        start: function () {
        },
        drag: function () {
        },
        stop: function (event) {
            var pLeft, pTop, currentObj;
            pLeft = parseInt(event.target.style.left);
            pTop = parseInt(event.target.style.top);
            bpmn = {
                bpmn: {
                    x: pLeft,
                    y: pTop
                }
            };
            if (Modernizr.localstorage) {
                currentObj = localStorage.getItem(PMDesigner.localStorage.prefix);
                currentObj = (currentObj === null) ? {} : JSON.parse(currentObj);
                jQuery.extend(true, currentObj, bpmn);
                localStorage.setItem(PMDesigner.localStorage.prefix, JSON.stringify(currentObj));
            } else {
                jQuery.extend(true, PMDesigner.cookie.object, bpmn);
                document.cookie = PMDesigner.cookie.name + "=" + JSON.stringify(PMDesigner.cookie.object);
            }
        }
    });
    jQuery(".content_controls").draggable({
        handle: "div",
        start: function () {
        },
        drag: function () {
            jQuery("html").css("overflow", "hidden");
        },
        stop: function (event) {
            jQuery("html").css("overflow", "auto");
            if (jQuery(this).position().top > $(window).height()) {
                var x = $(window).height() - 30;
                jQuery(this).css({'top': x + 'px'});
            }
            var pLeft, pTop, currentObj;
            pLeft = parseInt(event.target.style.left);
            pTop = parseInt(event.target.style.top);
            if (pTop < 90)
                pTop = 90;
            event.target.style.setProperty("top", pTop.toString() + "px");
            controls = {
                controls: {
                    x: pLeft,
                    y: pTop
                }
            };
            if (Modernizr.localstorage) {
                currentObj = localStorage.getItem(PMDesigner.localStorage.prefix);
                currentObj = (currentObj === null) ? {} : JSON.parse(currentObj);
                jQuery.extend(true, currentObj, controls);
                localStorage.setItem(PMDesigner.localStorage.prefix, JSON.stringify(currentObj));
            } else {
                jQuery.extend(true, PMDesigner.cookie.object, controls);
                document.cookie = PMDesigner.cookie.name + "=" + JSON.stringify(PMDesigner.cookie.object);
            }
        }
    });
    /*-----  End of Shapes and Controls Box  ------*/

    //Resize window
    PMDesigner.resizeFrame();

    /*==============================================
     =            Autosave functionality            =
     ==============================================*/
    PMDesigner.project.setSaveInterval(40000);
    setInterval(function () {
        if (PMDesigner.project.isDirty()) {
            PMDesigner.project.remoteProxy.setUrl(HTTP_SERVER_HOSTNAME + "/api/1.0/" + WORKSPACE + "/project/" + prj_uid);
            if (PMDesigner.project.isSave === true) {
                PMDesigner.msgFlash('Saving Process'.translate(), document.body, 'success', 5000, 5);
            } else {
                PMDesigner.project.isSave = true;
                PMDesigner.project.save(true);
            }
        }
    }, PMDesigner.project.saveInterval);
    /*-----  End of Autosave functionality  ------*/

    //Reviewing functionalities
    if (!PMDesigner.supportBrowser("fullscreen")) {
        var li = document.getElementsByClassName("mafe-button-fullscreen");
        if (li) {
            li[0].parentElement.style.display = "none";
        }
    }
    jQuery('.mafe-zoom-options').attr('title', 'Zoom'.translate()).tooltip({tooltipClass: "mafe-action-tooltip"});
    jQuery('.mafe-toolbar-lasso').mouseover(function (e) {
        $('.mafe-toolbar-lasso').css('cursor', 'pointer');
    });
    jQuery('.mafe-toolbar-validation').mouseover(function (e) {
        $('.mafe-toolbar-validation').css('cursor', 'pointer');
    });
    jQuery('.mafe-toolbar-lasso').click(function (e) {
        if (!PMUI.getActiveCanvas().lassoEnabled) {
            $('.mafe-toolbar-lasso').css('background-color', 'rgb(207, 207, 207)');
            PMUI.getActiveCanvas().lassoEnabled = true;
        } else {
            $('.mafe-toolbar-lasso').css('background-color', 'rgb(233, 233, 233)');
            PMUI.getActiveCanvas().lassoEnabled = false;
        }
    });

    PMDesigner.helper = new IntroHelper({
        tooltipClass: 'general',
        skipLabel: 'Quit'.translate(),

        nextLabel: 'Next &#8594;'.translate(),
        prevLabel: '&#8592; Back'.translate(),
        doneLabel: 'Done'.translate(),
        steps: [
            {
                intro: '<div class="screencast"/>'
            },
            {
                element: '#idNavBar',
                intro: 'The designer bar displays the process name and is used to control the process view (zoom, full screen view), the export, undo/redo and the save button.'.translate()
            },

            {
                element: '.bpmn_shapes',
                intro: 'Drag and drop the process elements that you want to include in the process design.'.translate() +
                '<br /><img src="../../../lib/img/corona-task.png">' + ' Task: Add to include an action in your process.'.translate() +
                '<br /><img src="../../../lib/img/corona-gateway-exclusive.png"> <img src="../../../lib/img/corona-gateway-parallel.png"> <img src="../../../lib/img/corona-gateway-inclusive.png">' + ' Gateway: Selects a path or divides the process into multiple paths and joins them together.'.translate() +
                '<br /><img src="../../../lib/img/corona-start.png"> <img src="../../../lib/img/corona-start-message.png">' + ' Start Event: The process always begins with a start event.'.translate() +
                '<br /><img src="../../../lib/img/corona-intermediate-receive-message.png"> <img src="../../../lib/img/corona-intermediate-send-message.png">' + ' Intermediate Event: Used to define an event that happens in the middle of the process.'.translate() +
                '<br /><img src="../../../lib/img/corona-end.png"> <img src="../../../lib/img/corona-end-message.png">' + ' End Event: End the execution of the process.'.translate() +
                '<br /><img src="../../../lib/img/corona-pool.png">' + ' Pool: Place each process in a separate pool.'.translate() +
                '<br /><img src="../../../lib/img/corona-lane.png">' + ' Lane: Used to divide a process into different sections.'.translate()
            },
            {
                element: '#div-layout-canvas',
                intro: "In the design area you can drop the process elements and order or arrange them to design your process.".translate()
            },
            {
                element: '.content_controls',
                intro: '<p>' +
                'The process objects are used to add execution features to the current process design.'.translate() +
                '<br/>Variables: Define the process data.'.translate() +
                '<br/>Dynaforms: Create dynamic forms.'.translate() +
                '<br/>Triggers: Create scripts.'.translate() +
                '<br/>Output documents: Generate documents with process data.'.translate() +
                '<br/>DB connections: Connect to external databases.'.translate() +
                '</p>',
                position: 'left'
            },
            {
                intro: '<div class="startcoronahelp"></div><div>' + 'Select an element in the designer to display the quick toolbar with the list of the most used options available for that element.'.translate() + '</div>'
            }
        ],
        onExit: function () {
            var canvas = PMUI.getActiveCanvas();
            if (canvas && canvas.getGridLine() && canvas.getHTML()) {
                canvas.getHTML().classList.add("pmui-pmcanvas");
            }
        }
    });

    jQuery('.mafe-toolbar-validation').click(function (e) {
        if (!PMDesigner.validator) {
            $('.mafe-toolbar-validation').css('background-color', 'rgb(207, 207, 207)');
            PMDesigner.validator = true;
        } else {
            $('.bpmn_validator').css('visibility', 'hidden');
            $('.mafe-toolbar-validation').css('background-color', 'rgb(233, 233, 233)');
            PMDesigner.validator = false;
        }
    });
    if (inArray("jXsSi94bkRUcVZyRStNVExlTXhEclVadGRRcG9xbjNvTWVFQUF3cklKQVBiVT0=", ENABLED_FEATURES)) {
        $("#idNavBar").find(".mafe-button-export-process").html(
            $("#idNavBar").find(".mafe-button-export-process").text() + "  &#x25BC;"
        );
    }

    function inArray(needle, haystack) {
        var i,
            length = haystack.length;
        for (i = 0; i < length; i += 1) {
            if (haystack[i] == needle) return true;
        }
        return false;
    }
});

window.onload = function () {
    //Reset the scroll positions
    window.scrollBy(-window.scrollX, -window.scrollY);
    document.onkeydown = function (e) {
        if (e.keyCode === 8 && e.target === document.body) {
            e.stopPropagation();
            return false;
        }
    };
};
/*==================================================
 =            Components from the Panels            =
 ==================================================*/

PMDesigner.createHTML = function () {
    var minShapes = document.createElement("span"),
        minShapesLegend = document.createElement("span"),
        refreshShapes = document.createElement("span"),
        minControls = document.createElement("span"),
        processObjects = document.createElement("span"),
        refreshControls = document.createElement("span"),
        refreshNavBar = document.createElement("span");
    minShapes.id = "minShapes";
    minShapesLegend.id = "minShapesLegend";
    refreshShapes.id = "resetShapes";
    minControls.id = "minControls";
    refreshControls.id = "resetControls";
    refreshNavBar.id = "resetNavBar";
    minShapes.className = "mafe-shapes-toggle";
    minShapesLegend.className = "mafe-shapes-toggle";
    refreshShapes.className = "mafe-shapes-refresh";
    minControls.className = "mafe-shapes-toggle";
    processObjects.className = "mafe-process-object";
    refreshControls.className = "mafe-shapes-refresh";
    refreshNavBar.className = "mafe-shapes-refresh";
    minShapes.title = "Minimize".translate();
    minShapesLegend.title = "Minimize".translate();
    refreshShapes.title = "reset".translate();
    minControls.title = "Minimize".translate();
    refreshControls.title = "Reset to original position".translate();
    refreshNavBar.title = "reset".translate();

    jQuery(minShapes).tooltip({tooltipClass: "mafe-action-tooltip"});
    jQuery(minShapesLegend).tooltip({tooltipClass: "mafe-action-tooltip"});
    jQuery(refreshShapes).tooltip({tooltipClass: "mafe-action-tooltip"});
    jQuery(minControls).tooltip({tooltipClass: "mafe-action-tooltip"});
    jQuery(refreshControls).tooltip({tooltipClass: "mafe-action-tooltip"});
    jQuery(refreshNavBar).tooltip({tooltipClass: "mafe-action-tooltip"});

    refreshControls.style.backgroundPosition = '0px 0px';
    processObjects.textContent = "Process Objects".translate();

    minShapes.onclick = function () {
        var i,
            items = jQuery(".bpmn_shapes > ul");
        if (items.length > 0) {
            for (i = 0; i < items.length; i += 1) {
                if (jQuery(items[i]).css("display").toLowerCase() !== "none") {
                    jQuery(items[i]).css({
                        display: 'none'
                    });
                } else {
                    jQuery(items[i]).css({
                        display: 'block'
                    });
                }

            }
        }
    };
    minShapesLegend.onclick = function () {
        var i,
            items = jQuery(".bpmn_shapes_legend").children();
        for (i = 1; i < items.length; i += 1) {
            if (jQuery(items[i]).css("display").toLowerCase() !== "none") {
                jQuery(items[i]).css({
                    display: 'none'
                });
            } else {
                jQuery(items[i]).css({
                    display: 'block'
                });
            }
        }
    };
    refreshShapes.onclick = function () {
        jQuery(".bpmn_shapes").removeAttr('style');
        if (Modernizr.localstorage) {
            PMDesigner.localStorage.remove("bpmn");
        } else {
            PMDesigner.cookie.remove("bpmn");
        }
    };
    minControls.onclick = function () {
        var i,
            title = '',
            items = jQuery(".content_controls > ul");

        if (items.length > 0) {
            for (i = 0; i < items.length; i += 1) {
                if (jQuery(items[i]).css("display").toLowerCase() !== "none") {
                    jQuery(items[i]).css({
                        display: 'none'
                    });
                    title = "Maximize";
                    $('#minControls').removeClass('mafe-shapes-toggle');
                    $('#minControls').addClass('mafe-shapes-plus');
                } else {
                    jQuery(items[i]).css({
                        display: 'block'
                    });
                    title = "Minimize";
                    $('#minControls').removeClass('mafe-shapes-plus');
                    $('#minControls').addClass('mafe-shapes-toggle');

                }
            }
        }
        jQuery(minControls).tooltip({content: title.translate()});
    };
    refreshControls.onclick = function () {
        jQuery(".content_controls").css({
            left: "auto",
            right: "20px",
            top: "90px"
        });
        if (Modernizr.localstorage) {
            PMDesigner.localStorage.remove("controls");
        } else {
            PMDesigner.cookie.remove("controls");
        }
    };
    refreshNavBar.onclick = function () {
        jQuery(".navBar").removeAttr('style');
        if (Modernizr.localstorage) {
            PMDesigner.localStorage.remove("navbar");
        } else {
            PMDesigner.cookie.remove("navbar");
        }
    };


    jQuery(".bpmn_shapes>div").append(minShapes);
    jQuery(".bpmn_shapes>div").append(refreshShapes);
    jQuery(".content_controls>div").append(processObjects);
    jQuery(".content_controls>div").append(minControls);
    jQuery(".content_controls>div").append(refreshControls);
    jQuery(".navBar>div").append(refreshNavBar);
    jQuery(".bpmn_shapes_legend>div").append(minShapesLegend);
    jQuery(".bpmn_shapes, .content_controls").on("contextmenu", function (e) {
        e.preventDefault();
    });

    PMDesigner.applyCanvasOptions();

};

/*-----  End of Components from the Panels  ------*/

/*=====================================================
 =            Get information about browser            =
 =====================================================*/
PMDesigner.getBrowser = function () {
    var match,
        ua = navigator.userAgent.toLowerCase();
    if (ua) {
        match = /(chrome)[ \/]([\w.]+)/.exec(ua) ||
            /(webkit)[ \/]([\w.]+)/.exec(ua) ||
            /(opera)(?:.*version|)[ \/]([\w.]+)/.exec(ua) ||
            /(msie) ([\w.]+)/.exec(ua) ||
            ua.indexOf("compatible") < 0 && /(mozilla)(?:.*? rv:([\w.]+)|)/.exec(ua) ||
            [];

        return {
            browser: match[1] || "",
            version: match[2] || "0"
        };
    }
};
/*-----  End of Get information about browser  ------*/

PMDesigner.supportBrowser = function (functionality) {
    var browser, el, module;
    functionality = functionality.toLowerCase();
    switch (functionality) {
        case "fullscreen":
            browser = PMDesigner.getBrowser();
            if ((browser.browser === "msie") && (parseInt(browser.version, 10) <= 10)) {
                try {
                    module = new ActiveXObject("WScript.Shell");
                } catch (e) {
                    module = false;
                }
            } else {
                el = document.documentElement;
                module = el.requestFullScreen || el.webkitRequestFullScreen || el.mozRequestFullScreen || el.msRequestFullScreen;
                if (!module) {
                    module = false;
                }
            }
            break;
        case "":
            break;
    }
    return module;
};

/*============================================================
 =            Leave the current page Functionality            =
 ============================================================*/
window.onbeforeunload = function (e) {
    var message;
    if (PMDesigner.project.isDirty()
        && PMDesigner.project.isClose == false
        && PMDesigner.project.isSave == false) {
        PMDesigner.project.remoteProxy.setUrl(HTTP_SERVER_HOSTNAME + "/api/1.0/" + WORKSPACE + "/project/" + prj_uid);
        if (PMDesigner.project.isSave === true) {
            PMDesigner.msgFlash('Saving Process'.translate(), document.body, 'success', 5000, 5);
        } else {
            PMDesigner.project.verifyAndSave(true);
        }
        message = "There are unsaved changes, if you leave the editor some changes won't be saved.".translate();
        e = e || window.event;
        if (e) {
            e.returnValue = message;
        }
        return message;
    }
};
/*-----  End of Leave the current page Functionality  ------*/

/*=====================================================================
 =            Validating coordinates for create a new shape            =
 =====================================================================*/
PMUI.validCoordinatedToCreate = function (canvas, event, shape) {
    var position, p, width, height, createElem = true, panels = [], message;
    //navBar panel
    position = jQuery(".navBar").offset();
    width = jQuery(".navBar").width();
    height = jQuery(".navBar").height();
    element = {
        x1: position.left,
        y1: position.top,
        x2: position.left + width,
        y2: position.top + height
    };
    panels.push(element);
    //BPMN panel
    position = jQuery(".bpmn_shapes").offset();
    width = jQuery(".bpmn_shapes").width();
    height = jQuery(".bpmn_shapes").height();
    element = {
        x1: position.left,
        y1: position.top,
        x2: position.left + width,
        y2: position.top + height
    };
    if (panels.length > 0) {
        for (p = 0; p < panels.length; p += 1) {
            if (((event.pageX >= panels[p].x1) && (event.pageX <= panels[p].x2))
                && ((event.pageY >= panels[p].y1) && (event.pageY <= panels[p].y2))) {
                PMDesigner.msgFlash('Is not possible create the element in that area'.translate(), document.body, 'info', 3000, 5);
                return false;
            }
        }
    }

    return true;
};
/*-----  End of Validating coordinates for create a new shape  ------*/

PMUI.pageCoordinatesToShapeCoordinates = function (shape, e, xCoord, yCoord, customShape) {
    var coordinates,
        x = (!xCoord) ? e.pageX : xCoord,
        y = (!yCoord) ? e.pageY : yCoord,
        orgX = (!xCoord) ? e.pageX : xCoord,
        orgY = (!yCoord) ? e.pageY : yCoord,
        canvas = shape.getCanvas();
    x += canvas.getLeftScroll() - shape.getAbsoluteX() - canvas.getX();
    y += canvas.getTopScroll() - shape.getAbsoluteY() - canvas.getY();
    coordinates = new PMUI.util.Point(x, y);
    return coordinates;
};

PMDesigner.msgFlash = function (text, container, severity, duration, zorder) {
    var msg;
    if (!PMDesigner.currentMsgFlash) {
        msg = new PMUI.ui.FlashMessage({
            id: '__msgFlashMessage',
            severity: 'success'
        });
    } else {
        msg = PMDesigner.currentMsgFlash;
    }
    if (msg.html)
        jQuery(msg.html).remove();
    msg.setMessage(text || "");
    msg.setAppendTo(container || document.body);
    msg.setSeverity(severity || "success");
    msg.setDuration(duration || 3000);
    msg.setZOrder(zorder || 100);
    msg.show();
    PMDesigner.currentMsgFlash = msg;
};

PMDesigner.msgWinError = function (text) {
    var msgError;
    if (!PMDesigner.currentWinError) {
        msgError = new PMUI.ui.MessageWindow({
            id: 'showMessageWindowFailure',
            width: 490,
            windowMessageType: 'error',
            title: 'Error'.translate(),
            footerItems: [
                {
                    text: 'Ok'.translate(),
                    handler: function () {
                        msgError.close();
                    },
                    buttonType: "success"
                }
            ]
        });
    } else {
        msgError = PMDesigner.currentWinError;
    }
    msgError.setMessage(text || 'Error'.translate());
    msgError.showFooter();
    msgError.open();
    PMDesigner.currentWinError = msgError;
};

PMDesigner.msgWinWarning = function (text) {
    var msgWarning;
    if (!PMDesigner.currentWinWarning) {
        msgWarning = new PMUI.ui.MessageWindow({
            id: 'showMessageWindowWarning',
            windowMessageType: 'warning',
            width: 490,
            title: 'Warning'.translate(),
            footerItems: [{
                text: 'Ok'.translate(),
                buttonType: "success", handler: function () {
                    msgWarning.close();
                }
            }]
        });
    } else {
        msgWarning = PMDesigner.currentWinWarning;
    }
    msgWarning.setMessage(text || 'Warning'.translate());
    msgWarning.showFooter();
    msgWarning.open();
    PMDesigner.currentWinWarning = msgWarning;
};

PMDesigner.modeReadOnly = function () {
    var restClient;
    if (prj_readonly === 'true') {
        restClient = new PMRestClient({
            typeRequest: 'post',
            multipart: true,
            data: {
                calls: [{
                    url: 'cases/' + app_uid + '/tasks',
                    method: 'GET'
                }
                ]
            },
            functionSuccess: function (xhr, response) {
                var viewTaskInformation = new ViewTaskInformation();
                viewTaskInformation.setData(response[0].response);
                viewTaskInformation.setShapes();
                viewTaskInformation.showViewLegendsInformation();
            },
            functionFailure: function (xhr, response) {
                PMDesigner.msgWinError(response.error.message);
            }
        });
        restClient.setBaseEndPoint('');
        restClient.executeRestClient();
    }
};

PMDesigner.reloadDataTable = function () {
    $('.bpmn_validator').css('visibility', 'visible');
};

DataDictionary = function () {
};
DataDictionary.prototype.getColor = function (value) {
    switch (value) {
        case 'TASK_IN_PROGRESS':
            return 'red';
        case 'TASK_COMPLETED':
            return 'green';
        case 'TASK_PENDING_NOT_EXECUTED':
            return 'silver';
        case 'TASK_PARALLEL':
            return 'orange';
        default:
            return 'white';
    }
};
DataDictionary.prototype.getStatus = function (value) {
    switch (value) {
        case 'TASK_IN_PROGRESS':
            return 'Task in Progress'.translate();
        case 'TASK_COMPLETED':
            return 'Completed Task'.translate();
        case 'TASK_PENDING_NOT_EXECUTED':
            return 'Pending Task / Not Executed'.translate();
        case 'TASK_PARALLEL':
            return 'Parallel Task'.translate();
        default:
            return value;
    }
};
DataDictionary.prototype.getTasAssignType = function (value) {
    switch (value) {
        case 'BALANCED':
            return 'Balanced'.translate();
        case 'MANUAL':
            return 'Manual'.translate();
        case 'REPORT_TO':
            return 'Report toO'.translate();
        case 'EVALUATE':
            return 'Evaluate'.translate();
        case 'SELF_SERVICE':
            return 'self Service'.translate();
        case 'SELF_SERVICE_EVALUATE':
            return 'Self Service Evaluate'.translate();
        default:
            return value;
    }
};
DataDictionary.prototype.getTasType = function (value) {
    switch (value) {
        case 'NORMAL':
            return 'Normal'.translate();
        case 'SUBPROCESS':
            return 'Sub Process'.translate();
        default:
            return value;
    }
};
DataDictionary.prototype.getTasDerivation = function (value) {
    switch (value) {
        case 'NORMAL':
            return 'Normal'.translate();
        default:
            return value;
    }
};

ViewTaskInformation = function (settings) {
    ViewTaskInformation.prototype.init.call(this, settings);
};
ViewTaskInformation.prototype.init = function () {
    var that = this,
        panelButton = new PMUI.core.Panel({
            layout: 'hbox',
            items: [
                that.getButton('Information', function () {
                    that.showInformation();
                }),
                that.getButton('Delegations', function () {
                    that.showDelegations();
                }),
                that.getButton('Route', function () {
                    that.showRoute();
                })
            ]
        });
    that.windowAbstract.showFooter();
    that.windowAbstract.addItem(panelButton);
    that.windowAbstract.addItem(that.panelvertical);
};
ViewTaskInformation.prototype.dataDictionary = new DataDictionary();
ViewTaskInformation.prototype.data = null;
ViewTaskInformation.prototype.shapeData = null;
ViewTaskInformation.prototype.panelvertical = new PMUI.core.Panel({layout: 'vbox', width: 400});
ViewTaskInformation.prototype.windowAbstract = new PMUI.ui.Window({id: 'windowAbstract', width: 500, height: 350});
ViewTaskInformation.prototype.setData = function (data) {
    this.data = data;
};
ViewTaskInformation.prototype.setCursor = function (shape) {
    shape.getHTML().onmouseover = function () {
        this.style.cursor = 'pointer';
    };
    shape.getHTML().onmouseout = function () {
        this.style.cursor = '';
    };
};
ViewTaskInformation.prototype.setShapes = function () {
    var that = this,
        shape,
        diagrams,
        i,
        j,
        dt = that.data;
    for (i = 0; i < dt.length; i += 1) {
        diagrams = PMDesigner.project.diagrams.asArray();
        for (j = 0; j < diagrams.length; j += 1) {
            shape = diagrams[j].getCustomShapes().find('id', dt[i].tas_uid);
            if (typeof shape != "undefined" && shape != null) {
                shape.changeColor(that.dataDictionary.getColor(dt[i].status));
                shape.data = dt[i];
                shape.hasClick = function (event) {
                    that.setShapeData(this.data);
                    that.showInformation();
                };
                that.setCursor(shape);
            }
        }
    }
};
ViewTaskInformation.prototype.setShapeData = function (data) {
    this.shapeData = data;
};
ViewTaskInformation.prototype.addRowNewLine = function (label, value) {
    var panelhorizontal = new PMUI.core.Panel({
        layout: 'hbox'
    });
    panelhorizontal.addItem(new PMUI.ui.TextLabel({text: ''}));
    this.panelvertical.addItem(panelhorizontal);
    return panelhorizontal;
};
ViewTaskInformation.prototype.addRow = function (label, value) {
    var field1, field2, field3, panelhorizontal;

    field1 = new PMUI.ui.TextLabel({text: label.translate(), proportion: 0.3});
    field2 = new PMUI.ui.TextLabel({text: ':', proportion: 0.1});
    field3 = new PMUI.ui.TextLabel({text: value ? value + '' : '', proportion: 0.6});

    panelhorizontal = new PMUI.core.Panel({
        layout: 'hbox'
    });

    panelhorizontal.addItem(field1);
    panelhorizontal.addItem(field2);
    panelhorizontal.addItem(field3);
    this.panelvertical.addItem(panelhorizontal);
    return panelhorizontal;
};
ViewTaskInformation.prototype.clearRows = function () {
    this.panelvertical.clearItems();
};
ViewTaskInformation.prototype.showInformation = function () {
    var that = this;
    that.clearRows();
    that.addRow('Title', that.shapeData.tas_title);
    that.addRow('Description', that.shapeData.tas_description);
    that.addRow('Status', that.dataDictionary.getStatus(that.shapeData.status));
    that.addRow('Type', that.dataDictionary.getTasType(that.shapeData.tas_type));
    that.addRow('Assign type', that.dataDictionary.getTasAssignType(that.shapeData.tas_assign_type));
    that.addRow('Derivation', that.dataDictionary.getTasDerivation(that.shapeData.tas_derivation));
    that.addRow('Start', that.shapeData.tas_start);
    that.addRowNewLine();
    that.addRow('User Name', that.shapeData.usr_username);
    that.addRow('User', that.shapeData.usr_firstname + ' ' + that.shapeData.usr_lastname);

    that.windowAbstract.setTitle('Information'.translate() + ' ' + that.shapeData.tas_title);
    that.windowAbstract.open();
    that.windowAbstract.body.style.padding = '20px';
};
ViewTaskInformation.prototype.showDelegations = function () {
    var that = this, i, dt;
    that.clearRows();
    dt = that.shapeData.delegations;
    for (i = 0; i < dt.length; i += 1) {
        that.addRow('User', dt[i].usr_username);
        that.addRow('User Name', dt[i].usr_firstname + ' ' + dt[i].usr_lastname);
        that.addRow('Duration', dt[i].del_duration);
        that.addRow('Finish Date', dt[i].del_finish_date);
        that.addRow('Index', dt[i].del_index);
        that.addRow('Init Date', dt[i].del_init_date);
        that.addRow('Task Due Date', dt[i].del_task_due_date);
        that.addRowNewLine();
    }

    that.windowAbstract.setTitle('Delegations'.translate() + ' ' + that.shapeData.tas_title);
    that.windowAbstract.open();
    that.windowAbstract.body.style.padding = '20px';
};
ViewTaskInformation.prototype.showRoute = function () {
    var that = this, i, dt;
    that.clearRows();
    that.addRow('Type', that.shapeData.route.type);
    that.addRowNewLine();
    dt = that.shapeData.route.to;
    for (i = 0; i < dt.length; i += 1) {
        that.addRow('Condition', dt[i].rou_condition);
        that.addRow('Number', dt[i].rou_number);
        that.addRowNewLine();
    }

    that.windowAbstract.setTitle('Route'.translate() + ' ' + that.shapeData.tas_title);
    that.windowAbstract.open();
    that.windowAbstract.body.style.padding = '20px';
};
ViewTaskInformation.prototype.getButton = function (text, fn) {
    return new PMUI.ui.Button({
        text: text.translate(),
        width: 180,
        height: 50,
        style: {
            cssProperties: {
                marginRight: 10,
                marginBottom: 10,
                backgroundColor: '#474747',
                borderRadius: 5,
                padding: 5
            },
            cssClasses: ['mafeButton']
        },
        handler: fn
    });
};
ViewTaskInformation.prototype.showViewLegendsInformation = function () {
    var i, dt, legend, legendIcon, legendText;
    $('.bpmn_shapes_legend').show();

    i;
    dt = [
        ['red', 'Task in Progress'.translate()],
        ['green', 'Completed Task'.translate()],
        ['silver', 'Pending Task / Not Executed'.translate()],
        ['orange', 'Parallel Task'.translate()]
    ];
    for (i = 0; i < dt.length; i += 1) {
        legend = $("<div></div>");
        legendIcon = $("<div></div>").addClass("mafe-activity-task-" + dt[i][0]).addClass("icon-legend");
        legendText = $("<div>" + dt[i][1] + "</div>").addClass("text-legend");
        legend.append(legendIcon).append(legendText);
        jQuery(".bpmn_shapes_legend").append(legend);
    }

    jQuery(".bpmn_shapes_legend").draggable({
        handle: "div",
        start: function () {
        },
        drag: function (event, e, u) {
        },
        stop: function (event) {

        }
    });
};
