/**
 * Class representing a Web Entry
 * @param relatedShape
 * @constructor
 */
var WebEntry = function (relatedShape) {
    this.relatedShape = null;
    this.groupType = null;
    this.groupLabel = null;
    this.stepsType = null;
    this.actUid = null;
    this.evenUid = null;
    this.weeUid = null;
    this.stepsAssigned = null;
    this.elementAccordionOpen = null;
    this.configWebEntry = null;
    this.isNewWebEntry = null;
    this.windowWebEntry = null;
    this.windowAlternative = null;
    this.tabForm = null;
    this.tabProperties = null;
    this.tabLink = null;
    this.confirmWindow = null;
    this.tabPanelWindow = null;
    this.suggestUser = null;
    this.stepsAssignTree = null;
    this.stepsAssignAccordion = null;
    this.labelsPanel = null;
    this.userGuest = {};
    WebEntry.prototype.initialize.call(this, relatedShape);
};
/**
 * A module representing a Web Entry
 **/
WebEntry.prototype = {
    /**
     * Sets the actUid
     * @param {string} actUid
     */
    setActUid: function (actUid) {
        this.actUid = actUid;
        return this;
    },

    /**
     * Sets the evenUid
     * @param {string} evenUid
     */
    setEvnUid: function (evenUid) {
        this.evenUid = evenUid;
        return this;
    },

    /**
     * Sets the weeUid
     * @param {string} weeUid
     */
    setWeeUid: function (weeUid) {
        this.weeUid = weeUid;
        return this;
    },

    /**
     * Sets the configWebEntry
     * @param {object} configWebEntry
     */
    setConfigWebEntry: function (configWebEntry) {
        this.configWebEntry = configWebEntry;
        return this;
    },

    /**
     * Sets the isNewWebEntry
     * @param {boolean} isNewWebEntry
     */
    setIsNewWebEntry: function (isNewWebEntry) {
        this.isNewWebEntry = isNewWebEntry;
        return this;
    },

    /**
     * Sets the windowWebEntry
     * @param {object} windowWebEntry
     */
    setWindowWebEntry: function (windowWebEntry) {
        this.windowWebEntry = windowWebEntry;
        return this;
    },

    /**
     * Sets the windowAlternative
     * @param {object} windowAlternative
     */
    setWindowAlternative: function (windowAlternative) {
        this.windowAlternative = windowAlternative;
        return this;
    },

    /**
     * Sets the relatedShape
     * @param {object} relatedShape
     */
    setRelatedShape: function (relatedShape) {
        this.relatedShape = relatedShape;
        return this;
    },

    /**
     * Sets the tabForm
     * @param {object} tabForm
     */
    setTabForm: function (tabForm) {
        this.tabForm = tabForm;
        return this;
    },

    /**
     * Sets the tabProperties
     * @param {object} tabProperties
     */
    setTabProperty: function (tabProperties) {
        this.tabProperties = tabProperties;
        return this;
    },

    /**
     * Sets the tabLink
     * @param {object} tabLink
     */
    setTabLink: function (tabLink) {
        this.tabLink = tabLink;
        return this;
    },

    /**
     * Sets the confirmWindow
     * @param {object} confirmWindow
     */
    setConfirmWin: function (confirmWindow) {
        this.confirmWindow = confirmWindow;
        return this;
    },

    /**
     * Sets the tabPanelWindow
     * @param {object} tabPanelWindow
     */
    setTabPanelWindow: function (tabPanelWindow) {
        this.tabPanelWindow = tabPanelWindow;
        return this;
    },

    /**
     * Sets the suggestUser
     * @param {object} suggestUser
     */
    setSuggestUser: function (suggestUser) {
        this.suggestUser = suggestUser;
        return this;
    },

    /**
     * Sets the stepsAssignTree
     * @param {object} stepsAssignTree
     */
    setStepsTree: function (stepsAssignTree) {
        this.stepsAssignTree = stepsAssignTree;
        return this;
    },

    /**
     * Sets the stepsAssignAccordion
     * @param {object} stepsAssignAccordion
     */
    setStepsAccordion: function (stepsAssignAccordion) {
        this.stepsAssignAccordion = stepsAssignAccordion;
        return this;
    },

    /**
     * Sets the labelsPanel
     * @param {object} labelsPanel
     */
    setLabelPanel: function (labelsPanel) {
        this.labelsPanel = labelsPanel;
        return this;
    },

    /**
     * Sets the userGuest
     * @param userGuest
     */
    setUserGuest: function (userGuest) {
        this.userGuest = userGuest;
        return this;
    },

    /**
     * Get the userGuest
     * @returns {Object} userGuest
     */
    getUserGuest: function () {
        return this.userGuest || {};
    },

    /**
     * Get the actUid value
     * @returns {null|*|string} The actUid value
     */
    getActUid: function () {
        return this.actUid || "";
    },

    /**
     * Get the isNewWebEntry value
     * @returns {null|*|boolean} The isNewWebEntry value
     */
    getRelatedShape: function () {
        return this.relatedShape || [];
    },

    /**
     * Get the evenUid value
     * @returns {null|*|string} The evenUid value
     */
    getEvnUid: function () {
        return this.evenUid || "";
    },

    /**
     * Get the weeUid value
     * @returns {null|*|string} The weeUid value
     */
    getWeeUid: function () {
        return this.weeUid || "";
    },

    /**
     * Get the configWebEntry value
     * @returns {*|null|Array} The configWebEntry value
     */
    getConfigWebEntry: function () {
        return this.configWebEntry || [];
    },

    /**
     * Get the isNewWebEntry value
     * @returns {null|*|boolean} The isNewWebEntry value
     */
    getIsNewWebEntry: function () {
        return this.isNewWebEntry || false;
    },

    /**
     * Get the windowWebEntry value
     * @returns {null|*} The windowWebEntry value
     */
    getWindowWebEntry: function () {
        return this.windowWebEntry || null;
    },

    /**
     * Get the windowAlternative value
     * @returns {*|null} The windowAlternative value
     */
    getWindowAlternative: function () {
        return this.windowAlternative || null;
    },

    /**
     * Get the tabForm value
     * @returns {*|null} The tabForm value
     */
    getTabForm: function () {
        return this.tabForm || null;
    },

    /**
     * Get the tabProperties value
     * @returns {null|*} The tabProperties value
     */
    getTabProperty: function () {
        return this.tabProperties || null;
    },

    /**
     * Get the tablink value
     * @returns {null|*} The tablink value
     */
    getTabLink: function () {
        return this.tabLink || null;
    },

    /**
     * Get the confirmWindow value
     * @returns {null|*} The confirmWindow value
     */
    getConfirmWin: function () {
        return this.confirmWindow || null;
    },

    /**
     * Get the tabPanelWindow
     * @returns {*|null}
     */
    getTabPanelWindow: function () {
        return this.tabPanelWindow || null;
    },

    /**
     * get the suggestUser value
     * @returns {*|null} The suggestUser value
     */
    getSuggestUser: function () {
        return this.suggestUser || null;
    },

    /**
     * Sets the stepsAssignTree
     * @returns {*|null}
     */
    getStepsTree: function () {
        return this.stepsAssignTree || null;
    },

    /**
     * Get the stepsAssignAccordion value
     * @returns {*|null} The stepsAssignAccordion value
     */
    getStepsAccordion: function () {
        return this.stepsAssignAccordion || null;
    },

    /**
     * get the labelsPanel value
     * @returns {null|*} The labelsPanel value
     */
    getLabelPanel: function () {
        return this.labelsPanel || null;
    },

    /**
     * initialize App
     * @param relatedShape
     */
    initialize: function (relatedShape) {
        this.groupType = [
            'DYNAFORM', 'INPUT_DOCUMENT',
            'OUTPUT_DOCUMENT', 'EXTERNAL'
        ];
        this.groupLabel = [
            'Dynaform (s)'.translate(), 'Input Document (s)'.translate(),
            'OutPut Document (s)'.translate(), 'External (s)'.translate()
        ];
        this.stepsType = {
            'DYNAFORM': 'Dynaform'.translate(),
            'INPUT_DOCUMENT': 'Input Document'.translate(),
            'OUTPUT_DOCUMENT': 'Output Document'.translate(),
            'EXTERNAL': 'External'.translate()
        };

        this.setRelatedShape(relatedShape)
            .setEvnUid(relatedShape.evn_uid);

        if (this.getRelatedShape().getPorts().getFirst()) {
            this.setActUid(this.getRelatedShape().getPorts().getFirst().getConnection().getDestPort().getParent()
                .act_uid);
        }
        if (__env.USER_GUEST) {
            $.extend(true, this.userGuest, __env.USER_GUEST);
        }
        this.stepsAssigned = new PMUI.util.ArrayList();
        this.elementAccordionOpen = new PMUI.util.ArrayList();
    },

    /**
     * Render form
     * @returns {WebEntry}
     */
    render: function () {
        this.getWindow().addItem(this.getTabPanel());
        this.getWindow().open();
        this.getWindow().showFooter();
        this.initializeData();
        return this;
    },

    /**
     * Populate Web Entry data
     */
    initializeData: function () {
        //Pupulate web entry data
        this.getInstanceWebEntryData();
        this.setWebEntryConfiguration();
        this.initializeAccordionAndTreepanelData();
    },

    /**
     * Populate accordion and treepanel data
     */
    initializeAccordionAndTreepanelData: function () {
        var that = this;
        //clear global array stepsAssigned
        this.stepsAssigned.clear();
        //get accordion data
        this.getAccordionData(
            function (xhr, response) {
                //populate data accordion tabForms
                that.loadAccordionItems(response);
            }, function (xhr, response) {
                PMDesigner.msgWinError(response.error.message);
            }
        );
        //get getTreePanelData data
        this.getTreePanelData(
            function (xhr, response) {
                //populate data treePanel tabForms
                that.loadTreePanelData(response);
            }, function (xhr, response) {
                PMDesigner.msgWinError(response.error.message);
            }
        );
        this.addEventSortableInAccordionElements();
        this.addEventSortableInTreePanelElements();
    },

    /**
     * Get all dynaforms
     * Execute restClient(GET/project/dynaforms)
     * @param successCallback
     * @param failureCallback
     */
    getDynaforms: function (successCallback, failureCallback) {
        return new PMRestClient({
            typeRequest: 'post',
            multipart: true,
            data: {
                calls: [
                    {
                        url: 'dynaforms',
                        method: 'GET'
                    }
                ]
            },
            functionSuccess: function (xhr, response) {
                successCallback(xhr, response);
            },
            functionFailure: function (xhr, response) {
                failureCallback(xhr, response);
            }
        }).executeRestClient();
    },

    /**
     * Get users
     * Execute restClient(GET/users/uid_usr)
     * @param uidUser
     * @param successCallback
     * @param failureCallback
     */
    getUserData: function (uidUser, successCallback, failureCallback) {
        return new PMRestClient({
            typeRequest: 'post',
            multipart: true,
            data: {
                calls: [
                    {
                        url: 'users/' + uidUser,
                        method: 'GET'
                    }
                ]
            },
            functionSuccess: function (xhr, response) {
                successCallback(xhr, response);
            },
            functionFailure: function (xhr, response) {
                failureCallback(xhr, response);
            },
            messageError: 'There are problems getting the Steps, please try again.'.translate()
        }).setBaseEndPoint("").executeRestClient();
    },

    /**
     * Get WebEntry Configuration
     * Execute restClient(GET/web-entry-event/event/)
     * Execute restClient(POST/web-entry-event/)
     * @param successCallback
     * @returns {Array}
     */
    getWebEntryConfiguration: function (successCallback) {
        var that = this,
            restProxy = new PMRestClient({
                endpoint: 'web-entry-event/event/' + that.getEvnUid(),
                typeRequest: "get",
                functionSuccess: function (xhr, response) {
                    successCallback(response, false);
                    that.initializeSomeVariables(response, false);
                },
                functionFailure: function (xhr, response) {
                    restProxy = new PMRestClient({
                        endpoint: 'web-entry-event',
                        typeRequest: "post",
                        data: {
                            act_uid: that.getActUid(),
                            evn_uid: that.getEvnUid(),
                            wee_title: that.getEvnUid(),
                            we_type: "MULTIPLE",
                            we_authentication: "LOGIN_REQUIRED",
                            we_callback: "PROCESSMAKER",
                            we_callback_url: "",
                            we_show_in_new_case: "0",
                            usr_uid: that.getUserGuest().uid || ''
                        },
                        functionSuccess: function (xhr, response) {
                            successCallback(response, true);
                            that.initializeSomeVariables(response, true);
                        },
                        functionFailure: function (xhr, response) {
                            PMDesigner.msgWinError(response.error.message);
                        }
                    });
                    restProxy.executeRestClient();
                }
            });
        restProxy.executeRestClient();
        return this;
    },

    /**
     * Update data Web Entry Configuration
     * Execute restClient(PUT/web-entry-event/weeuid)
     * @param data
     * @param successCallback
     * @param failureCallback
     * @returns {PMRestClient}
     */
    updateWebEntryConfiguration: function (data, successCallback, failureCallback) {
        return new PMRestClient({
            endpoint: 'web-entry-event/' + this.weeUid,
            typeRequest: 'update',
            data: data,
            functionSuccess: function (xhr, response) {
                successCallback(xhr, response);
            },
            functionFailure: function (xhr, response) {
                failureCallback(xhr, response);
            }
        }).executeRestClient();
    },

    /**
     * Get Accordion Data (Tab Forms)
     * Execute restClient(GET/steps, GET/step/triggers)
     * @returns {Array}
     */
    getAccordionData: function (successCallback, failureCallback) {
        return new PMRestClient({
            typeRequest: 'post',
            multipart: true,
            data: {
                calls: [
                    {
                        url: 'activity/' + this.getConfigWebEntry().tas_uid + '/steps',
                        method: 'GET'
                    }, {
                        url: 'activity/' + this.getConfigWebEntry().tas_uid + '/step/triggers',
                        method: 'GET'
                    }
                ]
            },
            functionSuccess: function (xhr, response) {
                successCallback(xhr, response);
            },
            functionFailure: function (xhr, response) {
                failureCallback(xhr, response);
            },
            messageError: 'There are problems getting the Steps, please try again.'.translate()
        }).executeRestClient();
    },

    /**
     * Get TreePanel Data (TabForm)
     * Execute restClient(GET/available-steps GET/triggers)
     * @returns {Array}
     */
    getTreePanelData: function (successCallback, failureCallback) {
        return new PMRestClient({
            typeRequest: 'post',
            multipart: true,
            data: {
                calls: [
                    {
                        url: 'activity/' + this.getConfigWebEntry().tas_uid + '/available-steps',
                        method: 'GET'
                    }, {
                        url: 'triggers',
                        method: 'GET'
                    }
                ]
            },
            functionSuccess: function (xhr, response) {
                successCallback(xhr, response);
            },
            functionFailure: function (xhr, response) {
                failureCallback(xhr, response);
            }
        }).executeRestClient();
    },

    /**
     * Get steps Availables (TreePanel)
     * Execute restClient(GET/available-steps)
     * @param successCallback
     * @param failureCallback
     * @returns {Array}
     */
    getStepAvailables: function (successCallback, failureCallback) {
        return new PMRestClient({
            typeRequest: 'post',
            multipart: true,
            data: {
                calls: [
                    {
                        url: 'activity/' + this.getConfigWebEntry().tas_uid + '/available-steps',
                        method: 'GET'
                    }
                ]
            },
            functionSuccess: function (xhr, response) {
                successCallback(xhr, response);
            },
            functionFailure: function (xhr, response) {
                failureCallback(xhr, response);
            }
        }).executeRestClient();
    },

    /**
     * Load Skin and Languages
     * Execute restClient(GET/system/languages GET/system/skins)
     * @returns {Array}
     */
    getSkinLanguage: function (successCallback, failureCallback) {
        return new PMRestClient({
            typeRequest: 'post',
            multipart: true,
            data: {
                calls: [
                    {
                        url: 'system/languages',
                        method: 'GET'
                    }, {
                        url: 'system/skins',
                        method: 'GET'
                    }
                ]
            },
            functionSuccess: function (xhr, response) {
                successCallback(xhr, response);
            },
            functionFailure: function (xhr, response) {
                failureCallback(xhr, response);
            },
            messageError: 'There are problems getting the Steps, please try again.'.translate()
        }).setBaseEndPoint("").executeRestClient();
    },

    /**
     * Delete the WebEntry configuration.
     * @param successCallback
     * @param failureCallback
     */
    deleteWebEntryConfiguration: function (successCallback, failureCallback) {
        return new PMRestClient({
            endpoint: 'web-entry-event/' + this.weeUid,
            typeRequest: 'remove',
            functionSuccess: function (xhr, response) {
                successCallback(xhr, response);
            },
            functionFailure: function (xhr, response) {
                failureCallback(xhr, response);
            }
        }).executeRestClient();
    },

    /**
     * Generate webEntry Link
     * @param weeUid
     * @param successCallback
     * @param failureCallback
     */
    generateLink: function (weeUid, successCallback, failureCallback) {
        return new PMRestClient({
            endpoint: 'web-entry-event/' + weeUid + '/generate-link',
            typeRequest: 'get',
            functionSuccess: function (xhr, response) {
                successCallback(xhr, response);
            },
            functionFailure: function (xhr, response) {
                failureCallback(xhr, response);
            }
        }).executeRestClient();
    },

    /**
     * Creates an instance of the WebEntry class
     * @returns {null}
     */
    getInstanceWebEntryData: function () {
        this.getWebEntryConfiguration(
            function (webEntryEvent, isNew) {
                if (isNew) {
                    webEntryEvent.we_type = 'SINGLE';
                    webEntryEvent.we_authentication = 'ANONYMOUS';
                    webEntryEvent.wee_url = '';
                    webEntryEvent.wee_title = '';
                }
            }
        );
        return this;
    },

    /**
     * Get Main Container Window
     * @returns {PMUI.ui.Window}
     */
    getWindow: function () {
        if (this.getWindowWebEntry() === null) {
            this.setWindowWebEntry(this.buildWindow());
        }
        return this.getWindowWebEntry();
    },

    /**
     * Build Window Container
     * @returns {PMUI.ui.Window}
     */
    buildWindow: function () {
        var that = this;
        return new PMUI.ui.Window({
            id: 'windowWebEntry',
            title: 'Web Entry'.translate(),
            height: DEFAULT_WINDOW_HEIGHT,
            width: DEFAULT_WINDOW_WIDTH,
            footerAlign: 'right',
            onBeforeClose: function () {
                if (that.isNewWebEntry) {
                    that.deleteWebEntryConfiguration(
                        function () {
                            that.getWindow().close();
                        },
                        function (xhr, response) {
                            PMDesigner.msgWinError(response.error.message);
                        }
                    );
                } else {
                    that.getWindow().close();
                }
                that.getWindow().close();
            },
            buttonPanelPosition: 'bottom',
            buttonsPosition: 'right',
            buttons: [
                {
                    id: 'windowWebEntryButtonDelete',
                    text: 'Delete'.translate(),
                    handler: function () {
                        that.handlerDeleteWebEntry();
                    },
                    buttonType: "error"
                },
                {
                    id: 'windowWebEntryButtonCancel',
                    text: 'Cancel'.translate(),
                    handler: function () {
                        if (that.isNewWebEntry) {
                            that.deleteWebEntryConfiguration(
                                function () {
                                    that.getWindow().close();
                                },
                                function (xhr, response) {
                                    PMDesigner.msgWinError(response.error.message);
                                }
                            );
                        } else {
                            that.getWindow().close();
                        }
                    },
                    buttonType: "error"
                },
                {
                    id: 'windowWebEntryButtonSave',
                    text: 'Save'.translate(),
                    handler: function (e) {
                        that.checkUserGuest('saveConfig');
                    },
                    buttonType: 'success'
                }
            ]
        });
    },

    /**
     * Return Second Window Container
     * @returns {PMUI.ui.Window}
     */
    getWindowAlternativeForm: function () {
        if (this.getWindowAlternative() === null) {
            this.setWindowAlternative(this.buildWindowAlternative());
        }
        return this.getWindowAlternative();
    },

    /**
     * Build Second Window Container
     * @returns {PMUI.ui.Window}
     */
    buildWindowAlternative: function () {
        return new PMUI.ui.Window({
            visibleFooter: true,
            title: 'Trigger'.translate(),
            footerAlign: 'right',
            footerItems: [{
                text: "@@",
                id: "secondaryWindow-criteria",
                style: {
                    cssProperties: {
                        "background": "rgb(45, 62, 80)",
                        "border": "1px solid rgb(45, 62, 80)"
                    },
                    cssClasses: ["mafe-button-condition-trigger"]
                }
            }, {
                id: 'secondaryWindow-cancel',
                text: 'Cancel'.translate(),
                buttonType: 'error',
                height: 31,
                style: {
                    cssClasses: ["mafe-button-condition-trigger"]
                }
            }, {
                id: 'secondaryWindow-save',
                text: 'Save'.translate(),
                buttonType: 'success',
                height: 31,
                style: {
                    cssClasses: ["mafe-button-condition-trigger"]
                }
            }]
        });
    },

    /**
     * Get TabPanel Container
     * @returns {TabPanel}
     */
    getTabPanel: function () {
        if (this.getTabPanelWindow() === null) {
            this.setTabPanelWindow(this.buildPanelWindow());
        }
        return this.getTabPanelWindow();
    },

    /**
     * Build TabPanel (TabForms, TabProperties, TabLink)
     * @returns {PMUI.panel.TabPanel}
     */
    buildPanelWindow: function () {
        return new PMUI.panel.TabPanel({
            id: 'windowWebEntryTabPanel',
            width: DEFAULT_WINDOW_WIDTH - 50,
            items: [
                {
                    id: 'tabForms',
                    title: 'Forms'.translate(),
                    panel: this.getTabForms()
                },
                {
                    id: 'tabProperties',
                    title: 'Properties'.translate(),
                    panel: this.getTabProperties()
                },
                {
                    id: 'tabLink',
                    title: 'Link'.translate(),
                    panel: this.getTabLinkForm()
                }
            ],
            style: {
                cssProperties: {
                    'margin-left': '10px'
                }
            },
            itemsPosition: {
                position: 'left'
            }
        });
    },

    /**
     * Get Panel TabForms
     * @returns {Panel}
     */
    getTabForms: function () {
        if (this.getTabForm() === null) {
            this.setTabForm(this.buildTabForms());
        }
        return this.getTabForm();
    },

    /**
     * Build Tab Forms
     * @returns {PMUI.core.Panel}
     */
    buildTabForms: function () {
        var that = this,
            singleDynaform,
            stepsMainContainer;

        singleDynaform = new PMUI.form.Form({
            id: 'singleDynaform',
            width: DEFAULT_WINDOW_WIDTH - 220,
            height: 180,
            name: 'singleDynaform',
            visibleHeader: false,
            items: [
                {
                    id: 'singleDynaformRadio',
                    pmType: 'radio',
                    labelVisible: false,
                    value: 'SINGLE',
                    name: 'options',
                    required: false,
                    controlPositioning: 'horizontal',
                    maxDirectionOptions: 4,
                    options: [
                        {
                            id: 'singleDynaform',
                            label: 'Single Dynaform'.translate(),
                            value: 'SINGLE',
                            selected: true
                        }
                    ],
                    onChange: function (newVal, oldVal) {
                        that.weeFormModeChange(newVal, oldVal);
                    },
                    labelWidth: '0%'
                },
                {
                    id: 'weeSelectDynaform',
                    name: 'tabFormsDropdownDyanform',
                    pmType: 'dropdown',
                    label: 'Dynaform'.translate(),
                    helper: 'Select Dynaform use in case.'.translate(),
                    required: true,
                    controlsWidth: 400,
                    labelWidth: '25%',
                    style: {
                        cssProperties: {
                            'padding-left': '100px'
                        }
                    },
                    options: [
                        {
                            label: 'Select Dynaform'.translate(),
                            value: ''
                        }
                    ]

                },
                {
                    id: 'multipleStepsRadio',
                    pmType: 'radio',
                    labelVisible: false,
                    value: '',
                    name: 'options',
                    required: false,
                    controlPositioning: 'vertical',
                    maxDirectionOptions: 4,
                    options: [
                        {
                            id: 'multipleSteps',
                            label: 'Multiple Steps'.translate(),
                            value: 'MULTIPLE'
                        }
                    ],
                    onChange: function (newVal, oldVal) {
                        that.weeFormModeChange(newVal, oldVal);
                    },
                    labelWidth: '0%'

                }
            ]
        });

        stepsMainContainer = new PMUI.core.Panel({
            id: 'stepsMainContainer',
            layout: 'hbox',
            width: DEFAULT_WINDOW_WIDTH - 220,
            items: [
                that.getStepsAssignTree(),
                that.getStepsAssignAccordion()
            ]
        });

        return new PMUI.core.Panel({
            id: 'mainContainer',
            layout: 'vbox',
            width: DEFAULT_WINDOW_WIDTH - 220,
            items: [
                singleDynaform,
                that.getLabelsPanel(),
                stepsMainContainer
            ]
        });
    },

    /**
     * Get Panel TabProperties
     * @returns {Panel}
     */
    getTabProperties: function () {
        if (this.getTabProperty() === null) {
            this.setTabProperty(this.buildTabProperties());
        }
        return this.getTabProperty();
    },

    /**
     * Build TabProperties
     * @returns {PMUI.core.Panel}
     */
    buildTabProperties: function () {
        var that = this,
            propertiesForm;

        propertiesForm = new PMUI.form.Form({
            id: 'idTabFormProperties',
            width: DEFAULT_WINDOW_WIDTH - 220,
            visibleHeader: false,
            items: [
                {
                    id: 'tabPropertiesWebEntryTitle',
                    pmType: 'text',
                    name: 'tabPropertiesWebEntryTitle',
                    valueType: 'string',
                    label: 'Web Entry Title'.translate(),
                    placeholder: 'Enter a title displayed on web entry window (if applies)'.translate(),
                    helper: 'Enter a title displayed on web entry window (if applies).'.translate(),
                    required: false,
                    controlsWidth: 458,
                    labelWidth: '23%'
                },
                {
                    id: 'tabPropRadioAuthentication',
                    pmType: 'radio',
                    labelVisible: true,
                    label: 'Authentication',
                    value: 'ANONYMOUS',
                    name: 'authentication',
                    required: true,
                    controlPositioning: 'horizontal',
                    maxDirectionOptions: 4,
                    options: [
                        {
                            id: 'tabPropertiesOptionRadioAnonymous',
                            label: 'Anonymous'.translate(),
                            value: 'ANONYMOUS',
                            selected: false
                        }
                    ],
                    onChange: function (newVal, oldVal) {
                        that.anonimusProcedure(newVal, oldVal);
                    },
                    labelWidth: '23%'
                },
                {
                    id: 'tabPropertiesRequireUserLogin',
                    pmType: 'radio',
                    labelVisible: false,
                    value: 'LOGIN_REQUIRED',
                    required: false,
                    name: 'authentication',
                    controlPositioning: 'horizontal',
                    maxDirectionOptions: 4,
                    options: [
                        {
                            id: 'tabPropertiesRadioRequireUserLogin',
                            label: 'Require user login'.translate(),
                            value: 'LOGIN_REQUIRED',
                            selected: false
                        }
                    ],
                    onChange: function (newVal, oldVal) {
                        that.loginRequired(newVal, oldVal);
                    },
                    labelWidth: '23%'
                },
                {
                    id: 'tabPropertiesHideLoogedInformationBar',
                    pmType: 'checkbox',
                    name: 'tabPropertiesHideLoogedInformationBar',
                    labelVisible: false,
                    disabled: true,
                    options: [
                        {
                            id: 'hideLoogedInformationBar',
                            label: 'Hide Logged Information Bar'.translate(),
                            value: '1',
                            selected: false
                        }
                    ],
                    style: {
                        cssProperties: {
                            'padding-left': '50px'
                        }
                    }
                },
                {
                    id: 'tabPropertiesRadioCallback',
                    pmType: 'radio',
                    labelVisible: true,
                    label: 'Callback Action',
                    value: 'PROCESSMAKER',
                    required: true,
                    disabled: false,
                    controlPositioning: 'vertical',
                    labelPosition: 'left',
                    helper: 'Callback Action...'.translate(),
                    maxDirectionOptions: 4,
                    options: [
                        {
                            id: 'redirectPM',
                            label: 'Redirect to ProcessMaker predefined response page'.translate(),
                            value: 'PROCESSMAKER',
                            selected: true
                        },
                        {
                            id: 'redirectURL',
                            label: 'Redirect to custom URL'.translate(),
                            value: 'CUSTOM'
                        },
                        {
                            id: 'redirectCustom',
                            label: 'Redirect to custom URL and clear login info'.translate(),
                            value: 'CUSTOM_CLEAR'
                        }
                    ],
                    onChange: function (newVal, oldVal) {
                        that.callbackActionChange(newVal, oldVal);

                    },
                    labelWidth: '23%'
                },
                new CriteriaField({
                    id: 'criteriaFieldCustomUrl',
                    pmType: 'text',
                    name: 'criteriaFieldCustomUrl',
                    label: 'Custom URL'.translate(),
                    placeholder: 'Enter a valid URL to be redirected when entry will be completed'.translate(),
                    labelWidth: '23%',
                    controlsWidth: 455,
                    required: true,
                    disabled: true
                }),
                {
                    id: 'showInNewCase',
                    pmType: 'checkbox',
                    name: 'showInNewCase',
                    label: 'Show task in New Case'.translate(),
                    labelVisible: true,
                    options: [
                        {
                            id: 'showTaskInNewCase',
                            value: 'showCase',
                            selected: false
                        }
                    ],
                    onChange: function (newValue, oldValue) {
                        this.setValue(newValue);
                    }
                }
            ]
        });

        $(propertiesForm.getItem('tabPropRadioAuthentication').getHTML())
            .append($(that.getSuggestField().createHTML()));

        return propertiesForm;
    },

    /**
     * Get Panel TabLink
     * @returns {Panel}
     */
    getTabLinkForm: function () {
        if (this.getTabLink() === null) {
            this.setTabLink(this.buildTabLink());
        }
        return this.getTabLink();
    },

    /**
     * Build TabLink
     * @returns {PMUI.core.Panel}
     */
    buildTabLink: function () {
        var that = this,
            tfromLink;

        tfromLink = new PMUI.form.Form({
            id: 'idTabFormLink',
            width: DEFAULT_WINDOW_WIDTH - 220,
            visibleHeader: false,
            items: [
                {
                    id: 'tabLinkRadioGeneration',
                    pmType: 'radio',
                    labelVisible: true,
                    label: 'Link Generation'.translate(),
                    value: 'DEFAULT',
                    name: 'options',
                    required: true,
                    controlPositioning: 'vertical',
                    labelPosition: 'left',
                    helper: 'Link Generation'.translate(),
                    controlsWidth: 485,
                    labelWidth: '24.5%',
                    maxDirectionOptions: 3,
                    options: [
                        {
                            id: 'generateLinkDefaultValues',
                            label: 'Generate link using workspace default values (skin, language)'.translate(),
                            value: 'DEFAULT',
                            selected: 'true'
                        },
                        {
                            id: 'advancedLinkGeneration',
                            label: 'Advanced link generation'.translate(),
                            value: 'ADVANCED'
                        }
                    ],
                    onChange: function (newVal) {
                        that.linkGenerationOnChange(newVal);
                    },
                    labelWidth: '18%'
                },
                {
                    id: 'tabLinkDropdownSkin',
                    name: 'tabLinkDropdownSkin',
                    pmType: 'dropdown',
                    label: 'Skin'.translate(),
                    helper: 'Select a Skin.'.translate(),
                    required: true,
                    controlsWidth: 485,
                    labelWidth: '24.5%',
                    onChange: function () {
                        that.setLinkText(tfromLink, '');
                    },
                    options: [
                        {
                            label: 'Select a Skin'.translate(),
                            value: ''
                        }
                    ]
                },
                {
                    id: 'tabLinkDropdownLanguage',
                    name: 'tabLinkDropdownLanguage',
                    pmType: 'dropdown',
                    label: 'Language'.translate(),
                    helper: 'Select a language.'.translate(),
                    required: true,
                    controlsWidth: 485,
                    labelWidth: '24.5%',
                    onChange: function () {
                        that.setLinkText(tfromLink, '');
                    },
                    options: [
                        {
                            label: 'Select a language'.translate(),
                            value: ''
                        }
                    ]
                },
                {
                    id: 'tablinkTextCustomDomain',
                    pmType: 'text',
                    name: 'tablinkTextCustomDomain',
                    valueType: 'string',
                    label: 'Custom Hostname'.translate(),
                    placeholder: 'https://example.com:8080'.translate(),
                    helper: 'Protocol and Hostname, port is optional.'.translate(),
                    required: true,
                    controlsWidth: 485,
                    labelWidth: "24.5%",
                    validators: [
                        {
                            pmType: "regexp",
                            criteria: /^(https?:\/\/)?(((\d{1,3}\.){3}\d{1,3})|(([\-\w]+\.)+\w{2,3}(\/[%\-\w]+(\.\w{2,})?)*(([\w\-\.\?\\\/+@&#;`~=%!]*)(\.\w{2,})?)*\/?))(:\d+)?$/i,
                            errorMessage: 'Enter a Protocol and Hostname valid value.'.translate()
                        }
                    ],
                    onChange: function () {
                        that.setLinkText(tfromLink, '');
                    }
                },
                {
                    id: 'panelLinkForm',
                    pmType: 'panel',
                    layout: 'hbox',
                    required: true,
                    width: '155px',
                    style: {
                        cssProperties: {
                            'margin-left': '-10px',
                            'margin-top': '-10px'
                        }
                    },
                    items: [
                        {
                            pmType: 'annotation',
                            text: 'Web Entry URL:'.translate(),
                            id: 'webEntryLinkLabel',
                            required: true,
                            name: 'webEntryLinkLabel'
                        },
                        {
                            pmType: 'annotation',
                            id: 'webEntryLink',
                            name: 'webEntryLink',
                            text: '',
                            required: true,
                            textType: 1,
                            style: {
                                cssProperties: {
                                    'margin-left': '-173px'
                                }
                            }
                        }
                    ]
                },
                new PMUI.field.ButtonField({
                    id: 'buttonFieldGenerateLink',
                    pmType: 'buttonField',
                    value: 'Generate Link'.translate(),
                    labelVisible: false,
                    buttonAlign: 'center',
                    controlsWidth: 180,
                    proportion: 0.6,
                    handler: function (field) {
                        that.checkUserGuest('generateLink');
                    },
                    buttonType: 'success',
                    style: {
                        cssProperties: {
                            'vertical-align': 'top',
                            'padding-top': '10px',
                            'padding-right': '0px',
                            'padding-bottom': '1px',
                            'padding-left': '130px'
                        }
                    }
                })
            ]
        });

        tfromLink.getItem('buttonFieldGenerateLink').getControl(0).button.setButtonType('success');

        return tfromLink;
    },

    /**
     * Get MessageWindow Container
     * @returns {PMUI.ui.MessageWindow}
     */
    getConfirmWindow: function () {
        if (this.getConfirmWin() === null) {
            this.setConfirmWin(this.buildConfirmWindow());
        }
        return this.getConfirmWin();
    },

    /**
     * Build MessageWindow
     * @returns {PMUI.ui.MessageWindow}
     */
    buildConfirmWindow: function () {
        return new PMUI.ui.MessageWindow({
            id: 'confirmWindowDeleteAcceptedValue',
            windowMessageType: 'warning',
            width: 490,
            bodyHeight: 'auto',
            title: '',
            message: '',
            footerItems: [
                {
                    id: 'confirmWindow-footer-no',
                    text: 'No'.translate(),
                    visible: true,
                    buttonType: 'error'
                }, {
                    id: 'confirmWindow-footer-yes',
                    text: 'Yes'.translate(),
                    visible: true,
                    buttonType: 'success'
                }
            ],
            visibleFooter: true
        });
    },

    /**
     * Handler button for delete web entry config
     */
    handlerDeleteWebEntry: function () {
        var that = this,
            confirmWindow,
            yesButton,
            noButton;
        confirmWindow = that.getConfirmWindow()
            .setMessage('Are you sure you want to delete the Web Entry configuration?'.translate());
        yesButton = that.getConfirmWindow().footer.getItem('confirmWindow-footer-yes');
        yesButton.setHandler(function () {
            confirmWindow.close();
            that.deleteWebEntryConfiguration(
                function () {
                    that.getWindow().close();
                },
                function (xhr, response) {
                    PMDesigner.msgWinError(response.error.message);
                }
            );
        });
        noButton = that.getConfirmWindow().footer.getItem('confirmWindow-footer-no');
        noButton.setHandler(function () {
            that.getConfirmWindow().close();
        });
        that.getConfirmWindow().open();
        return this;
    },

    /**
     * Handler button for save web entry config
     * @param method
     */
    checkUserGuest: function (method) {
        var title = 'Web Entry Anonymous Authentication'.translate(),
            message = ('Current selected user to act as anonymous will be replaced by the Guest user. ' +
                'This action cannot be undone. Do you want to proceed?').translate();

        //Validate the data before saving the configuration or generate the link
        if (this.isValidWebEntryData(method)) {
            (method === 'saveConfig') ? this.handlerSaveButton(message, title) : this.handlerGenerateLinkButton(message, title);
        }
        return this;
    },

    /**
     * Check userGuestUID and save Config
     * @param message
     * @param title
     * @returns {WebEntry}
     */
    handlerSaveButton: function (message, title) {
        var that = this,
            messageWindow = this.getConfirmWindow();
        //Validation is done to be compatible with older versions of webEntry (Now exist guest-type user).
        if (this.getUserGuest().uid && this.getSuggestField().value !== this.getUserGuest().uid) {
            messageWindow.setMessage(message);
            messageWindow.setTitle(title);

            //handler for cancel button
            messageWindow.footer.getItem('confirmWindow-footer-no').setHandler(function () {
                messageWindow.close();
            });

            //handler for yes button
            messageWindow.footer.getItem('confirmWindow-footer-yes').setHandler(function () {
                messageWindow.close();
                that.getSuggestField().set('value', that.getUserGuest().uid);
                that.saveWebEntryConfiguration(
                    function () {
                        that.getWindow().close();
                    }, function (xhr, response) {
                        PMDesigner.msgWinError(response.error.message);
                    }
                );
            });
            messageWindow.open();
        } else {
            //For new configurations we save the configuration without any GUEST user validation.
            this.saveWebEntryConfiguration(
                function () {
                    that.getWindow().close();
                }, function (xhr, response) {
                    PMDesigner.msgWinError(response.error.message);
                }
            );
        }
        return this;
    },

    /**
     * Check userGuestUID and generate webEntry Link
     * @param message
     * @param title
     * @returns {WebEntry}
     */
    handlerGenerateLinkButton: function (message, title) {
        var that = this,
            formLink = this.getTabLinkForm(),
            messageWindow = this.getConfirmWindow();
        //Validation is done to be compatible with older versions of webEntry (Now exist guest-type user).
        if (this.getUserGuest().uid && this.getSuggestField().value !== this.getUserGuest().uid) {
            messageWindow.setMessage(message);
            messageWindow.setTitle(title);

            //handler for cancel button
            messageWindow.footer.getItem('confirmWindow-footer-no').setHandler(function () {
                messageWindow.close();
            });
            //handler for yes button

            messageWindow.footer.getItem('confirmWindow-footer-yes').setHandler(function () {
                messageWindow.close();
                that.getSuggestField().set('value', that.getUserGuest().uid);
                that.setLinkText(formLink, '');
                //save Web Entry Configuration
                that.saveWebEntryConfiguration(
                    function (xhr, response) {
                        that.getConfigWebEntry().usr_uid = response.usr_uid || that.getConfigWebEntry().usr_uid;
                        //generate webEntry Link
                        that.generateLink(
                            that.getWeeUid(),
                            function (xhr, response) {
                                that.setLinkText(formLink, (response.link) ? response.link : '');
                                that.initializeAccordionAndTreepanelData();
                                that.getSuggestField().html.find("input")
                                    .val(that.getUserGuest().firstname + " " + "(" + that.getUserGuest().username + ")");
                            },
                            function (xhr, response) {
                                PMDesigner.msgWinError(response.error.message);
                            }
                        );
                    },
                    function (xhr, response) {
                        PMDesigner.msgWinError(response.error.message);
                    }
                );
            });
            messageWindow.open();
        } else {
            //For new configurations we generate the webEntry Link without any GUEST user validation.
            this.saveWebEntryConfiguration(
                function () {
                    that.generateLink(
                        that.getWeeUid(),
                        function (xhr, response) {
                            that.setLinkText(formLink, (response.link) ? response.link : '');
                            that.initializeAccordionAndTreepanelData();
                        },
                        function (xhr, response) {
                            that.setLinkText(formLink, '');
                            PMDesigner.msgWinError(response.error.message);
                        }
                    );
                },
                function (xhr, response) {
                    that.setLinkText(formLink, '');
                    PMDesigner.msgWinError(response.error.message);
                }
            );
        }
        return this;
    },

    /**
     * Get Steps AssignAccordion
     * @returns {PMUI.panel.AccordionPanel}
     */
    getStepsAssignAccordion: function () {
        if (this.getStepsAccordion() === null) {
            this.setStepsAccordion(this.buildStepsAssignAccordion());
        }
        return this.getStepsAccordion();
    },

    /**
     * Build Steps Assign Accordion
     * @returns {PMUI.panel.AccordionPanel}
     */
    buildStepsAssignAccordion: function () {
        var that = this;
        return new PMUI.panel.AccordionPanel({
            id: 'stepsAssignAccordion',
            multipleSelection: true,
            hiddenTitle: true,
            proportion: 1.5,
            style: {
                cssProperties: {
                    margin: '0px 0px 0px 0px'
                },
                cssClasses: ['mafe-border-panel']
            },
            listeners: {
                select: function (accordionItem, event) {
                    var buttonExpand, buttonCollapse, itemsAccod;
                    itemsAccod = that.getStepsAssignAccordion().items;
                    if (accordionItem.collapsed) {
                        if (that.elementAccordionOpen.indexOf(accordionItem) > -1) {
                            that.elementAccordionOpen.remove(accordionItem);
                        }
                    } else {
                        if (that.elementAccordionOpen.indexOf(accordionItem) === -1) {
                            that.elementAccordionOpen.insert(accordionItem);
                        }
                    }
                    buttonCollapse = that.getLabelsPanel().getItem('collapse-button');
                    buttonExpand = that.getLabelsPanel().getItem('expand-button');
                    if (that.elementAccordionOpen.getSize() === 0) {
                        buttonExpand.setDisabled(false);
                        buttonCollapse.setDisabled(true);
                    } else if (that.elementAccordionOpen.getSize() === itemsAccod.getSize()) {
                        buttonExpand.setDisabled(true);
                        buttonCollapse.setDisabled(false);
                    } else {
                        buttonExpand.setDisabled(false);
                        buttonCollapse.setDisabled(false);
                    }
                }
            }
        });
    },

    /**
     * Get steps AssignTree
     * @returns {PMUI.core.Panel}
     */
    getStepsAssignTree: function () {
        if (this.getStepsTree() === null) {
            this.setStepsTree(this.buildStepsAssignTree());
        }
        return this.getStepsTree();
    },

    /**
     * Build steps AssignTree
     * @returns {PMUI.panel.TreePanel}
     */
    buildStepsAssignTree: function () {
        return new PMUI.panel.TreePanel({
            id: 'stepsAssignTree',
            proportion: 0.5,
            height: 475,
            filterable: true,
            autoBind: true,
            filterPlaceholder: 'Search ...'.translate(),
            emptyMessage: 'No records found'.translate(),
            nodeDefaultSettings: {
                labelDataBind: 'obj_title',
                autoBind: true,
                collapsed: false,
                itemsDataBind: 'items',
                childrenDefaultSettings: {
                    labelDataBind: 'obj_title',
                    autoBind: true
                },
                behavior: 'drag'
            },
            style: {
                cssProperties: {
                    margin: '0px 0px 0px 0px',
                    float: 'left',
                    overflow: 'auto'
                },
                cssClasses: ['mafe-border-panel']
            }
        });
    },

    /**
     * Get Label Panel
     * @returns {PMUI.core.Panel}
     */
    getLabelsPanel: function () {
        if (this.getLabelPanel() === null) {
            this.setLabelPanel(this.buildLabelPanel());
        }
        return this.getLabelPanel();
    },

    /**
     * Build Label Panel
     * @returns {PMUI.core.Panel}
     */
    buildLabelPanel: function () {
        var that = this;
        return new PMUI.core.Panel({
            width: DEFAULT_WINDOW_WIDTH - 220,
            layout: "hbox",
            items: [
                new PMUI.field.TextAnnotationField({
                    text: 'Available Elements'.translate(),
                    proportion: 1.12,
                    text_Align: 'center'
                }),
                new PMUI.field.TextAnnotationField({
                    text: 'Assigned Elements (Drop here)'.translate(),
                    proportion: 1.3,
                    text_Align: 'center'
                }),
                new PMUI.ui.Button({
                    buttonType: 'link',
                    text: 'Expand all'.translate(),
                    id: 'expand-button',
                    proportion: 1.2,
                    handler: function () {
                        that.expandAndCollapseHandler('collapse-button', 'expand-button');
                    }
                }),
                new PMUI.ui.Button({
                    buttonType: 'link',
                    text: 'Collapse all'.translate(),
                    id: 'collapse-button',
                    proportion: 1.1,
                    disabled: true,
                    handler: function () {
                        that.expandAndCollapseHandler('expand-button', 'collapse-button');
                    }
                })
            ]
        });
    },

    /**
     * Get SuggestField Control
     * @returns {SuggestField}
     */
    getSuggestField: function () {
        if (this.getSuggestUser() === null) {
            this.setSuggestUser(this.createSugesstField());
        }
        return this.getSuggestUser();
    },

    /**
     * Create Field Suggest (User)
     * @returns {SuggestField}
     */
    createSugesstField: function () {
        return new SuggestField({
            id: 'idSuggestUser',
            label: 'Users'.translate(),
            width: 450,
            placeholder: 'Suggest users'.translate(),
            required: true,
            disabled: true,
            helper: 'When the form is submitted a new case is created with this user account.'.translate(),
            dynamicLoad: {
                data: [{
                    key: 'usr_uid',
                    label: ['usr_firstname', 'usr_lastname', '(', 'usr_username', ')']
                }],
                keys: {
                    url: HTTP_SERVER_HOSTNAME + '/api/1.0/' + WORKSPACE,
                    accessToken: credentials.access_token,
                    endpoints: [{
                        method: 'GET',
                        url: 'users'
                    }]
                }
            }
        });
    },

    /**
     * Generate Web Entry Link
     * @param formLink
     * @param linkText
     */
    setLinkText: function (formLink, linkText) {
        formLink.getItem('panelLinkForm').getItem('webEntryLink')
            .setText('<a href="' + linkText + '" target="_blank" ' + 'class="mafe-webentry-link">' + linkText + '</a>');
        return this;
    },

    /**
     * Initialize Some Variables for web Entry (weeUId, actUid, evnUid, response, isNewWebEntry)
     * @param response
     * @param newWebEntry
     */
    initializeSomeVariables: function (response, newWebEntry) {
        this.setWeeUid(response.wee_uid)
            .setActUid(response.act_uid)
            .setEvnUid(response.evn_uid)
            .setConfigWebEntry(response)
            .setIsNewWebEntry(newWebEntry);
        return this;
    },

    /**
     * Collapse and Expand Accordion Handler
     * @param enableItem
     * @param disableItem
     */
    expandAndCollapseHandler: function (enableItem, disableItem) {
        var items,
            i,
            item;
        items = this.getStepsAssignAccordion().getItems();
        this.getLabelsPanel().getItem(enableItem).setDisabled(false);
        this.getLabelsPanel().getItem(disableItem).setDisabled(true);
        if (enableItem === 'collapse-button') {
            this.elementAccordionOpen.clear();
            for (i = 0; i < items.length; i += 1) {
                item = items[i];
                item.expand();
                this.elementAccordionOpen.insert(item);
            }
        } else {
            for (i = 0; i < items.length; i += 1) {
                item = items[i];
                this.elementAccordionOpen.remove(item);
                item.collapse();
            }
        }
    },

    /**
     * Disable MultipleSteps or Single Dynaform (tabForms)
     * @returns {disableMultipleSteps}
     */
    weeFormModeChange: function (newVal, oldVal) {
        if (newVal === 'SINGLE') {
            this.disableMultipleSteps('SINGLE');
        } else {
            this.disableSingleDynaform('MULTIPLE');
            this.getTabForms().getItem('singleDynaform').getItem('weeSelectDynaform').hideMessage();
            this.getTabForms().getItem('singleDynaform').getItem('weeSelectDynaform')
                .getControl(0).style.removeClasses(['error']);
        }
        this.setLinkText(this.getTabLinkForm(), '');
        return this;
    },

    /**
     * Disable MultipleSteps (tabForms)
     * @param singleMultiple
     * @returns {WebEntry}
     */
    disableMultipleSteps: function (singleMultiple) {
        var singleDyna = this.getTabForms().getItem('singleDynaform');
        singleDyna.getItem('multipleStepsRadio').setValue('');
        singleDyna.getItem('weeSelectDynaform').enable();
        singleDyna.getItem('weeSelectDynaform').setRequired(true);
        singleDyna.getItem('singleDynaformRadio').setValue(singleMultiple);
        singleDyna.getItem('singleDynaformRadio').getControl(0).select();
        //Hide step panel
        this.getLabelsPanel().setVisible(false);
        this.getTabForms().getItem('stepsMainContainer').setVisible(false);
        return this;
    },

    /**
     * Disable Form SingleDynaform (tabForms)
     * @param singleMultiple
     * @returns {WebEntry}
     */
    disableSingleDynaform: function (singleMultiple) {
        var singleDyna = this.getTabForms().getItem('singleDynaform');
        singleDyna.getItem('singleDynaformRadio').setValue('');
        singleDyna.getItem('weeSelectDynaform').disable();
        singleDyna.getItem('weeSelectDynaform').setRequired(false);
        singleDyna.getItem('multipleStepsRadio').setValue(singleMultiple);
        singleDyna.getItem('multipleStepsRadio').getControl(0).select();
        //Show step panel
        this.getLabelsPanel().setVisible(true);
        this.getTabForms().getItem('stepsMainContainer').setVisible(true);
        return this;
    },

    /**
     * Change handler
     * @param newValue
     * @param oldValue
     * @returns {loginRequired}
     */
    anonimusProcedure: function (newValue, oldValue) {
        var propertiesForm = this.getTabProperties(),
            //The Callback Actions (PROCESSMAKER is 0, CUSTOM is 1, and CUSTOM_CLEAR is 2)
            callbackAction = 2;
        propertiesForm.getItem('tabPropertiesRequireUserLogin').setValue('[]');
        propertiesForm.getItem('tabPropertiesHideLoogedInformationBar').disable();
        propertiesForm.getItem('tabPropRadioAuthentication').setRequired(true);
        this.getSuggestField().setRequired(true);
        this.getSuggestField().hideMessageRequired();
        propertiesForm.getItem('tabPropertiesRadioCallback').disableOption(callbackAction);
        if (propertiesForm.getItem('tabPropertiesRadioCallback').getValue() === 'CUSTOM_CLEAR') {
            propertiesForm.getItem('tabPropertiesRadioCallback').getControl(callbackAction).deselect();
            propertiesForm.getItem('tabPropertiesRadioCallback').setValue('');
        }
        propertiesForm.getItem('criteriaFieldCustomUrl').disable();
        propertiesForm.getItem('criteriaFieldCustomUrl').buttonHTML.disable();
        this.setLinkText(this.getTabLinkForm(), '');
        return this;
    },

    /**
     * Change chandler
     * @param newValue
     * @param oldValue
     * @returns {loginRequired}
     */
    loginRequired: function (newValue, oldValue) {
        var propertiesForm = this.getTabProperties(),
            //The Callback Actions (PROCESSMAKER is 0, CUSTOM is 1, and CUSTOM_CLEAR is 2)
            callbackAction = 2;
        propertiesForm.getItem('tabPropRadioAuthentication').setValue('[]');
        propertiesForm.getItem('tabPropRadioAuthentication').setRequired(false);

        this.getSuggestField().setRequired(false);
        this.getSuggestField().hideMessageRequired();
        propertiesForm.getItem('tabPropertiesHideLoogedInformationBar').enable();
        propertiesForm.getItem('tabPropertiesRadioCallback').enableOption(callbackAction);
        this.callbackActionChange(propertiesForm.getItem('tabPropertiesRadioCallback').getValue(), '');
        this.setLinkText(this.getTabLinkForm(), '');
        return this;
    },

    /**
     * Change handler
     * @param newValue
     * @param oldValue
     * @returns {callbackActionChange}
     */
    callbackActionChange: function (newValue, oldValue) {
        var propertiesForm = this.getTabProperties();
        propertiesForm.getItem("tabPropertiesRadioCallback").setValue(newValue);
        switch (newValue) {
            case 'PROCESSMAKER':
                propertiesForm.getItem('criteriaFieldCustomUrl').disable();
                propertiesForm.getItem('criteriaFieldCustomUrl').setRequired(false);
                propertiesForm.getItem('criteriaFieldCustomUrl').buttonHTML.disable();
                break;
            case 'CUSTOM':
            case 'CUSTOM_CLEAR':
                propertiesForm.getItem('criteriaFieldCustomUrl').enable();
                propertiesForm.getItem('criteriaFieldCustomUrl').setRequired(true);
                propertiesForm.getItem('criteriaFieldCustomUrl').buttonHTML.enable();
                break;
        }
        return this;
    },

    /**
     * Change handler
     * @param newValue
     * @returns {linkGenerationOnChange}
     */
    linkGenerationOnChange: function (newValue) {
        var required,
            skin,
            language,
            domain,
            tfromLink;
        tfromLink = this.getTabLinkForm();
        required = newValue === 'ADVANCED';
        skin = tfromLink.getItem('tabLinkDropdownSkin');
        language = tfromLink.getItem('tabLinkDropdownLanguage');
        domain = tfromLink.getItem('tablinkTextCustomDomain');
        this.setLinkText(tfromLink, '');
        skin.setRequired(required);
        language.setRequired(required);
        domain.setRequired(required);
        if (required) {
            skin.enable();
            language.enable();
            domain.enable();
        } else {
            skin.disable();
            domain.hideMessage();
            language.disable();
            domain.disable();
            domain.getControl(0).style.removeClasses(['error']);
        }
        return this;
    },

    /**
     * Save webEntry Configuration
     * @param successCallback
     * @param failureCallback
     */
    saveWebEntryConfiguration: function (successCallback, failureCallback) {
        var data,
            //tabs window web entry
            tabProperties = this.getTabPanel().getItem('tabProperties'),
            tabLink = this.getTabPanel().getItem('tabLink'),
            //form tabs
            dataTabSingleDyn = this.getTabForms().getItem('singleDynaform'),
            dataTabProperties = tabProperties.getPanel('idTabProperties'),
            dataTabLink = tabLink.getPanel('idTabLink');

        //Prepare Data
        data = this.prepareData(dataTabSingleDyn, dataTabProperties, dataTabLink);
        //Save web Entry configuration
        this.updateWebEntryConfiguration(data, successCallback, failureCallback);
        return this;
    },

    /**
     * Validate Web Entry Data
     * @param method
     */
    isValidWebEntryData: function (method) {
        var valid = true,
            //tabs window web entry
            tabForm = this.getTabPanel().getItem('tabForms'),
            tabProperties = this.getTabPanel().getItem('tabProperties'),
            tabLink = this.getTabPanel().getItem('tabLink'),
            //form tabs
            dataTabSingleDyn = this.getTabForms().getItem('singleDynaform'),
            dataTabProperties = tabProperties.getPanel('idTabProperties'),
            dataTabLink = tabLink.getPanel('idTabLink'),
            //selected tab
            selectedTab = null;

        // validate form tab
        if (dataTabSingleDyn instanceof PMUI.form.Form) {
            valid = valid && dataTabSingleDyn.isValid();
            selectedTab = !valid && !selectedTab ? tabForm : selectedTab;
        }
        //validate property tab
        if (dataTabProperties instanceof PMUI.form.Form) {
            valid = valid && dataTabProperties.isValid();
            selectedTab = !valid && !selectedTab ? tabProperties : selectedTab;
        }
        //validate suggestField
        if (!this.getSuggestField().isValid()) {
            this.getSuggestField().showMessageRequired();
            valid = valid && this.getSuggestField().isValid();
            selectedTab = !valid && !selectedTab ? tabProperties : selectedTab;
        }
        //validate link Tab
        if (dataTabLink instanceof PMUI.form.Form) {
            valid = valid && dataTabLink.isValid();
            selectedTab = !valid && !selectedTab ? tabLink : selectedTab;
        }
        if (!valid) {
            selectedTab.select();
        }
        //validate Link Generate Button
        if (!$(dataTabLink.getItem('panelLinkForm').getItem('webEntryLink').text).attr('href') &&
            method !== 'generateLink') {
            PMDesigner.msgFlash('Please press the \"Generate Link\" button.'.translate(),
                this.getWindow(), 'error', 3000, 5);
            valid = false;
            tabLink.select();
        }
        return valid;
    },

    /**
     * Prepare Data
     * @param dataTabSingleDyn
     * @param dataTabProperties
     * @param dataTabLink
     */
    prepareData : function (dataTabSingleDyn, dataTabProperties, dataTabLink) {
        var data = {};
        data['act_uid'] = this.getActUid();
        data['evn_uid'] = this.getEvnUid();
        data['wee_title'] = this.getEvnUid();
        data['we_type'] = (dataTabSingleDyn.getItem('singleDynaformRadio').getValue()) ? 'SINGLE' : 'MULTIPLE';
        data['dyn_uid'] = (data['we_type'] === 'SINGLE') ? dataTabSingleDyn.getItem('weeSelectDynaform')
            .getValue() : '';
        data['we_custom_title'] = dataTabProperties.getItem('tabPropertiesWebEntryTitle').getValue();
        data['we_authentication'] = dataTabProperties.getItem('tabPropRadioAuthentication').getValue() === '[]' ?
            'LOGIN_REQUIRED' : 'ANONYMOUS';
        data['usr_uid'] = this.getSuggestField().value;
        data['we_hide_information_bar'] = dataTabProperties.getItem('tabPropertiesHideLoogedInformationBar')
            .getValue() === '[]' ? '0' : '1';
        data['we_callback'] = dataTabProperties.getItem('tabPropertiesRadioCallback').getValue();
        data['we_callback_url'] = (data['we_callback'] !== 'PROCESSMAKER') ?
            dataTabProperties.getItem('criteriaFieldCustomUrl').getValue() : '';
        data['we_link_generation'] = dataTabLink.getItem('tabLinkRadioGeneration').getValue();
        data['we_link_skin'] = dataTabLink.getItem('tabLinkDropdownSkin').getValue();
        data['we_link_language'] = dataTabLink.getItem('tabLinkDropdownLanguage').getValue();
        data['we_link_domain'] = (data['we_link_generation'] === 'ADVANCED') ?
            dataTabLink.getItem('tablinkTextCustomDomain').getValue() : '';
        data['we_show_in_new_case'] = (dataTabProperties.getItem('showInNewCase').getValue() === '["showCase"]') ? 1 : 0;
        return data;
    },

    /**
     * Populate all tabPanels (TabForms, TabProperties, TabLink) with data config
     * return void
     */
    setWebEntryConfiguration: function () {
        this.setConfigDataTabForms();
        this.setConfigDataTabProperties();
        this.setConfigDataTabLink();
        this.getWindow().getItem('windowWebEntryTabPanel').getItem('tabForms').select();
        return this;
    },

    /**
     * Load and populate Dynaforms Items
     * @returns {*}
     */
    setConfigDataTabForms: function () {
        var that = this,
            i,
            data,
            options = [],
            dynaformsControl,
            dynaforms = [];

        //execute Rest (get Dynaforms)
        this.getDynaforms(
            function (xhr, response) {
                dynaforms = response[0].response;
                //get Controls tab-Forms
                dynaformsControl = that.getTabForms().getItem('singleDynaform').getItem('weeSelectDynaform');

                //Set data Dropdown Single Dynaform
                for (i = 0; i < dynaforms.length; i += 1) {
                    data = {};
                    data.label = dynaforms[i]['dyn_title'];
                    data.value = dynaforms[i]['dyn_uid'];
                    if (that.getConfigWebEntry().dyn_uid === data.value &&
                        that.getConfigWebEntry().evn_uid === that.getRelatedShape()['evn_uid']) {
                        data.selected = true;
                    }
                    options.push(data);
                }
                dynaformsControl.setOptions(options);

                //set Disable/Enable single or multiple steps
                (that.getConfigWebEntry().we_type === 'SINGLE') ?
                    that.disableMultipleSteps(that.getConfigWebEntry().we_type) :
                    that.disableSingleDynaform(that.getConfigWebEntry().we_type);
            },
            function (xhr, response) {
                PMDesigner.msgWinError(response.error.message);
            }
        );
    },

    /**
     * TabProperties Panel
     * Assigns values to tabProperties fields
     */
    setConfigDataTabProperties: function () {
        var that = this,
            webEntryTitle,
            radioAuthentication,
            radioRequiredLogin,
            informationBar,
            radioCollback,
            customUrl,
            user,
            showInNewCase;

        //get Controls Tab-Properties
        this.getTabPanel().getItem('tabProperties').select();
        webEntryTitle = this.getTabProperties().getItem('tabPropertiesWebEntryTitle');
        radioAuthentication = this.getTabProperties().getItem('tabPropRadioAuthentication');
        radioRequiredLogin = this.getTabProperties().getItem('tabPropertiesRequireUserLogin');
        radioCollback = this.getTabProperties().getItem('tabPropertiesRadioCallback');
        customUrl = this.getTabProperties().getItem('criteriaFieldCustomUrl');
        informationBar = this.getTabProperties().getItem('tabPropertiesHideLoogedInformationBar');
        showInNewCase = this.getTabProperties().getItem('showInNewCase');

        //set webentry Title
        webEntryTitle.setValue(this.getConfigWebEntry().we_custom_title);

        //Loggin required or Anonymous
        radioCollback.setValue(this.getConfigWebEntry().we_callback);
        if (this.getConfigWebEntry().we_authentication === 'LOGIN_REQUIRED') {
            radioRequiredLogin.getControl(0).select();
            radioRequiredLogin.setValue(this.getConfigWebEntry().we_authentication);
            this.loginRequired();
        } else {
            radioAuthentication.getControl(0).select();
            radioAuthentication.setValue(this.getConfigWebEntry().we_authentication);
            this.anonimusProcedure();
        }

        //set value suggest user
        this.getSuggestField().html.find('input').val('');
        this.getConfigWebEntry().usr_uid = this.getConfigWebEntry().usr_uid || this.getUserGuest().uid;
        if (this.getConfigWebEntry().usr_uid) {
            this.getUserData(
                this.getConfigWebEntry().usr_uid,
                function (xhr, response) {
                    user = response[0].response;
                    that.getSuggestField().html.find("input").val(user.usr_firstname + " "
                        + user.usr_lastname + " " + "(" + user.usr_username + ")");
                    that.getSuggestField().set("value", user.usr_uid);
                },
                function (xhr, response) {
                    PMDesigner.msgWinError(response.error.message);
                }
            );
        }

        //set Hide Logged Information Bar
        if (this.getConfigWebEntry().we_hide_information_bar === '1') {
            informationBar.setValue('["1"]');
            informationBar.getControl(0).select();
        } else {
            informationBar.setValue('[]');
            informationBar.getControl(0).deselect();
        }

        //set Callback Action
        switch (this.getConfigWebEntry().we_callback) {
            case 'PROCESSMAKER':
                radioCollback.getControl(0).select();
                break;
            case 'CUSTOM':
                radioCollback.getControl(1).select();
                break;
            case 'CUSTOM_CLEAR':
                radioCollback.getControl(2).select();
                break;
        }

        //set custom URL
        customUrl.setValue(this.getConfigWebEntry().we_callback_url);

        //set show task in new case
        if (this.getConfigWebEntry().we_show_in_new_case === '1'){
            showInNewCase.setValue('["showCase"]');
        }else{
            showInNewCase.setValue('[]');
        }

        //customize suggest styles
        this.getSuggestField().inputField[0].style.width = '280px';
        this.getSuggestField().inputLabel[0].parentElement.style.width = '120px';
        this.getSuggestField().inputLabel[0].parentElement.style.marginLeft = '220px';
    },

    /**
     * TabLink Panel
     * Assigns values to tabLink fields
     */
    setConfigDataTabLink: function () {
        var that = this,
            options = [],
            customDomain,
            radioGeneration,
            languages = [],
            skins = [],
            dropDownLanguages,
            dropDownSkins,
            data,
            i;

        //execute Rest (Get languages and Skins)
        this.getSkinLanguage(
            function (xhr, response) {
                languages = response[0].response.data;
                skins = response[1].response.data;
                //get controls Tab-link
                that.getTabPanel().getItem('tabLink').select();
                radioGeneration = that.getTabLinkForm().getItem('tabLinkRadioGeneration');
                dropDownSkins = that.getTabLinkForm().getItem('tabLinkDropdownSkin');
                dropDownLanguages = that.getTabLinkForm().getItem('tabLinkDropdownLanguage');
                customDomain = that.getTabLinkForm().getItem('tablinkTextCustomDomain');

                //set Link generation (Default or Advanced)
                radioGeneration.setValue(that.getConfigWebEntry().we_link_generation);
                (that.getConfigWebEntry().we_link_generation === 'DEFAULT') ? radioGeneration.getControl(0).select() :
                    radioGeneration.getControl(1).select();

                //set Languages Dropdown
                if (jQuery.isArray(languages)) {
                    for (i = 0; i < languages.length; i += 1) {
                        data = {};
                        data.label = languages[i].LANG_NAME;
                        data.value = languages[i].LANG_ID;
                        if (languages[i].LANG_ID === that.getConfigWebEntry().we_link_language) {
                            data.selected = true;
                        }
                        options.push(data);
                    }
                    dropDownLanguages.setOptions(options);
                }
                //set Skins Dropdown
                if (jQuery.isArray(skins)) {
                    options = [];
                    for (i = 0; i < skins.length; i += 1) {
                        data = {};
                        data.label = skins[i].SKIN_NAME;
                        data.value = skins[i].SKIN_FOLDER_ID;
                        if (that.getConfigWebEntry().we_link_skin === skins[i].SKIN_FOLDER_ID) {
                            data.selected = true;
                        }
                        options.push(data);
                    }
                    dropDownSkins.setOptions(options);
                }

                //set Custom Domain
                customDomain.setValue(that.getConfigWebEntry().we_link_domain);
                //Enable or Disable (Skin, Language, CustomDomain)
                that.linkGenerationOnChange(that.getConfigWebEntry().we_link_generation);
                //set Link text
                that.setLinkText(that.getTabLinkForm(), that.getConfigWebEntry().wee_url);
            },
            function (xhr, response) {
                PMDesigner.msgWinError(response.error.message);
            }
        );
    },

    /**
     * Load and populate Accordion Data Items
     * @param response
     */
    loadAccordionItems: function (response) {
        var firstResp = [],
            secondResp = [],
            i,
            item,
            assigmentConfig,
            firstRes = 0,
            secondRes = 1;
        this.getStepsAssignAccordion().clearItems();
        if (jQuery.isArray(response) && response.length) {
            if (typeof response[firstRes] === "object") {
                firstResp = response[firstRes].response ? response[firstRes].response : [];
            }
            if (typeof response[secondRes] === "object") {
                secondResp = response[secondRes].response ? response[secondRes].response : [];
            }
        }
        if (firstResp.length) {
            for (i = 0; i < firstResp.length; i += 1) {
                item = this.createAccordionItem(firstResp[i], true, true);
                this.getStepsAssignAccordion().addItem(item);
                item.dataItem = firstResp[i];
                this.customAccordionItemButtons(item.html, firstResp[i], item);
            }
        }
        assigmentConfig = {
            step_type_obj: "Assignment".translate(),
            triggers: secondResp,
            st_type: "ASSIGNMENT",
            obj_title: "Assignment".translate(),
            step_uid_obj: "Assignment"
        };
        item = this.createAccordionItem(assigmentConfig);
        this.getStepsAssignAccordion().addItem(item);
        item.dataItem = assigmentConfig;
        assigmentConfig = {
            step_type_obj: "Routing".translate(),
            triggers: secondResp,
            obj_title: "Routing".translate(),
            st_type: 'ROUTING',
            step_uid_obj: "Routing"
        };
        item = this.createAccordionItem(assigmentConfig);
        this.getStepsAssignAccordion().addItem(item);
        item.dataItem = assigmentConfig;
        this.getStepsAssignAccordion().defineEvents();
    },

    /**
     * Create Accordion
     * @param data
     * @returns {PMUI.item.AccordionItem}
     */
    createAccordionItem: function (data) {
        var that = this,
            gridBefore,
            gridAfter,
            beforeTitle,
            afterTitle,
            i,
            textLabel;
        if (this.stepsType[data.step_type_obj]) {
            textLabel = this.stepsType[data.step_type_obj];
        } else {
            textLabel = data.step_type_obj;
        }
        beforeTitle = new PMUI.field.TextAnnotationField({
            text: 'Before'.translate() + ' ' + textLabel,
            proportion: 0.5,
            text_Align: 'left'
        });
        afterTitle = new PMUI.field.TextAnnotationField({
            text: 'After'.translate() + ' ' + textLabel,
            proportion: 0.5,
            text_Align: 'left',
            visible: data.st_type === "ASSIGNMENT" ? false : true
        });

        gridBefore = new PMUI.grid.GridPanel({
            behavior: 'dragdropsort',
            filterable: false,
            visibleHeaders: false,
            data: data.triggers,
            st_type: 'BEFORE',
            step_uid: data.step_uid,
            visibleFooter: false,
            width: '96%',
            emptyMessage: 'No records found'.translate(),
            style: {
                cssClasses: ['mafe-gridPanel']
            },
            customStatusBar: function (currentPage, pageSize, numberItems, criteria, filter) {
                return messagePageGrid(currentPage, pageSize, numberItems, criteria, filter);
            },
            columns: [{
                title: '',
                dataType: 'string',
                alignmentCell: 'center',
                columnData: "st_position",
                width: 20
            }, {
                title: 'Before Output Document'.translate(),
                dataType: 'string',
                alignmentCell: 'left',
                columnData: 'tri_title',
                width: 210
            }, {
                title: '',
                dataType: 'button',
                buttonLabel: function (row, data) {
                    return data.st_condition === '' ? 'Condition'.translate() : 'Condition *'.translate();
                },
                buttonStyle: {
                    cssClasses: ['mafe-button-edit']
                },
                onButtonClick: function (row, grid) {
                    var data = row.getData();
                    that.editCondition(grid.step_uid, data.tri_uid, data.st_type, row);
                }
            }, {
                title: '',
                dataType: 'button',
                buttonLabel: 'Edit'.translate(),
                buttonStyle: {
                    cssClasses: ['mafe-button-edit']
                },
                onButtonClick: function (row, grid) {
                    var data = row.getData(),
                        restClient;
                    restClient = new PMRestClient({
                        endpoint: 'trigger/' + data.tri_uid,
                        typeRequest: 'get',
                        functionSuccess: function (xhr, response) {
                            that.editTrigger(response.tri_webbot, response.tri_uid);
                        },
                        functionFailure: function (xhr, response) {
                            PMDesigner.msgWinError(response.error.message);
                        }
                    });
                    restClient.executeRestClient();
                }
            }, {
                title: '',
                dataType: 'button',
                buttonLabel: 'Remove'.translate(),
                buttonStyle: {
                    cssClasses: ['mafe-button-delete']
                },
                onButtonClick: function (row, grid) {
                    that.removeTrigger(row, grid);
                }
            }],
            onDrop: function (container, draggableItem, index) {
                var receiveData = draggableItem.getData();
                if (draggableItem instanceof PMUI.item.TreeNode) {
                    that.receiveTreeNodeItem(receiveData, this, index);
                } else {
                    that.receiveRowItem(receiveData, this, index, draggableItem);
                }
                that.updateIndexToGrid(this);
                return false;
            },
            onSort: function (container, item, index) {
                var receiveData = item.getData();
                that.sortableRowHandler(receiveData, this, index);
                that.updateIndexToGrid(this);
            },
            onDragStart: function (grid, row) {
                var items;
                items = grid.getItems();
                if (jQuery.isArray(items)) {
                    if (items.length === 1) {
                        grid.showEmptyCell();
                    }
                }
            }
        });
        if (data.st_type !== "ROUTING" && data.st_type !== "ASSIGNMENT") {
            gridBefore.st_type = 'BEFORE';
        } else if (data.st_type === "ROUTING") {
            gridBefore.st_type = "BEFORE_ROUTING";
        } else {
            gridBefore.st_type = "BEFORE_ASSIGNMENT";
        }
        gridBefore.step_uid = data.step_uid;
        gridBefore.clearItems();
        if (jQuery.isArray(data.triggers)) {
            for (i = 0; i < data.triggers.length; i += 1) {
                if (gridBefore.st_type === data.triggers[i].st_type) {
                    gridBefore.addDataItem({
                        st_condition: data.triggers[i].st_condition,
                        st_position: data.triggers[i].st_position,
                        st_type: data.triggers[i].st_type,
                        tri_description: data.triggers[i].tri_description,
                        tri_title: data.triggers[i].tri_title,
                        tri_uid: data.triggers[i].tri_uid,
                        obj_title: data.triggers[i].tri_title,
                        obj_uid: data.triggers[i].tri_uid
                    });
                }
            }
        }
        gridAfter = new PMUI.grid.GridPanel({
            behavior: 'dragdropsort',
            filterable: false,
            visibleHeaders: false,
            data: data.triggers,
            visibleFooter: false,
            width: '96%',
            visible: data.st_type === "ASSIGNMENT" ? false : true,
            emptyMessage: 'No records found'.translate(),
            style: {
                cssClasses: ['mafe-gridPanel']
            },
            customStatusBar: function (currentPage, pageSize, numberItems, criteria, filter) {
                return messagePageGrid(currentPage, pageSize, numberItems, criteria, filter);
            },
            columns: [{
                title: '',
                dataType: 'string',
                alignmentCell: 'center',
                columnData: 'st_position',
                width: 20
            }, {
                title: 'Before Output Document'.translate(),
                dataType: 'string',
                alignmentCell: 'left',
                columnData: 'tri_title',
                width: 210
            }, {
                title: '',
                dataType: 'button',
                buttonLabel: function (row, data) {
                    return data.st_condition === '' ? 'Condition'.translate() : 'Condition *'.translate();
                },
                buttonStyle: {
                    cssClasses: ['mafe-button-edit']
                },
                onButtonClick: function (row, grid) {
                    var data = row.getData();
                    that.editCondition(grid.step_uid, data.tri_uid, data.st_type, row);
                }
            }, {
                title: '',
                dataType: 'button',
                buttonLabel: 'Edit'.translate(),
                buttonStyle: {
                    cssClasses: ['mafe-button-edit']
                },
                onButtonClick: function (row, grid) {
                    var data = row.getData(),
                        restClient;
                    restClient = new PMRestClient({
                        endpoint: 'trigger/' + data.tri_uid,
                        typeRequest: 'get',
                        functionSuccess: function (xhr, response) {
                            that.editTrigger(response.tri_webbot, response.tri_uid);
                        },
                        functionFailure: function (xhr, response) {
                            PMDesigner.msgWinError(response.error.message);
                        }
                    });
                    restClient.executeRestClient();
                }
            }, {
                title: '',
                dataType: 'button',
                buttonLabel: 'Remove'.translate(),
                buttonStyle: {
                    cssClasses: ['mafe-button-delete']
                },
                onButtonClick: function (row, grid) {
                    that.removeTrigger(row, grid);
                }
            }],
            onDrop: function (container, draggableItem, index) {
                var receiveData = draggableItem.getData();
                if (draggableItem instanceof PMUI.item.TreeNode) {
                    that.receiveTreeNodeItem(receiveData, this, index);
                } else {
                    that.receiveRowItem(receiveData, this, index, draggableItem);
                }
                that.updateIndexToGrid(this);
                return false;
            },
            onSort: function (container, item, index) {
                var receiveData = item.getData();
                that.sortableRowHandler(receiveData, this, index);
                that.updateIndexToGrid(this);
            },
            onDragStart: function (grid, row) {
                var items;
                items = grid.getItems();
                if (jQuery.isArray(items)) {
                    if (items.length === 1) {
                        grid.showEmptyCell();
                    }
                }
            }
        });
        if (data.st_type !== "ROUTING" && data.st_type !== "ASSIGNMENT") {
            gridAfter.st_type = 'AFTER';
        } else if (data.st_type == "ROUTING") {
            gridAfter.st_type = "AFTER_ROUTING";
        } else {
            gridAfter.st_type = "AFTER_ASSIGNMENT";
        }
        gridAfter.step_uid = data.step_uid;
        if (jQuery.isArray(data.triggers)) {
            for (i = 0; i < data.triggers.length; i += 1) {
                if (gridAfter.st_type === data.triggers[i].st_type) {
                    gridAfter.addDataItem({
                        st_condition: data.triggers[i].st_condition,
                        st_position: data.triggers[i].st_position,
                        st_type: data.triggers[i].st_type,
                        tri_description: data.triggers[i].tri_description,
                        tri_title: data.triggers[i].tri_title,
                        tri_uid: data.triggers[i].tri_uid,
                        obj_title: data.triggers[i].tri_title,
                        obj_uid: data.triggers[i].tri_uid
                    });
                }
            }
        }
        var accordionItem = new PMUI.item.AccordionItem({
            id: 'id' + data.step_uid_obj,
            dataStep: data,
            closeable: true,
            body: new PMUI.core.Panel({
                layout: 'vbox',
                items: [
                    beforeTitle,
                    gridBefore,
                    afterTitle,
                    gridAfter
                ]
            })
        });
        if (this.stepsType[data.step_type_obj]) {
            accordionItem.setTitle(data.step_position + ".  " + data.obj_title + ' ('
                + this.stepsType[data.step_type_obj] + ')');
            this.stepsAssigned.insert(accordionItem);
        } else {
            accordionItem.setTitle((this.getStepsAssignAccordion().items.getSize() + 1) + ". " + data.obj_title);
        }
        return accordionItem;
    },

    /**
     * This method is executed when a row is drop in another grid
     * @param receiveData, data of the droppable item
     * @param grid, the affected grid
     * @param index, the index position row
     * @param draggableItem
     * @returns {*}
     */
    receiveRowItem: function (receiveData, grid, index, draggableItem) {
        var receiveParent = draggableItem.getParent(),
            message,
            restClient,
            that = this;
        if (this.isTriggerAssigned(grid, receiveData.obj_uid)) {
            message = new PMUI.ui.FlashMessage({
                message: 'Trigger is assigned.'.translate(),
                duration: 3000,
                severity: 'error',
                appendTo: this.getWindow()
            });
            index = receiveParent.items.indexOf(draggableItem);
            receiveParent.items.remove(draggableItem);
            receiveParent.addItem(draggableItem, index);
            message.show();
            return false;
        }
        restClient = new PMRestClient({
            typeRequest: 'post',
            multipart: true,
            data: {
                calls: [{
                    url: grid.step_uid === undefined ?
                        'activity/' + that.getConfigWebEntry().tas_uid + '/step/trigger' : 'activity/'
                        + that.getConfigWebEntry().tas_uid + '/step/' + grid.step_uid + '/trigger',
                    method: 'POST',
                    data: {
                        tri_uid: receiveData.obj_uid,
                        st_type: grid.st_type,
                        st_condition: receiveData.st_condition,
                        st_position: index + 1
                    }
                }, {
                    url: receiveParent.step_uid === undefined ?
                        'activity/' + that.getConfigWebEntry().tas_uid + '/step/trigger/' + receiveData.obj_uid
                        + '/' + that.getStepType(receiveParent.st_type) :
                        'activity/' + that.getConfigWebEntry().tas_uid + '/step/' + receiveParent.step_uid
                        + '/trigger/' + receiveData.obj_uid + '/' + receiveParent.st_type.toLowerCase(),
                    method: 'DELETE'
                }]
            },
            functionSuccess: function (xhr, response) {
                var data;
                data = receiveData;
                if (data.hasOwnProperty("st_type")) {
                    data.st_type = grid.st_type;
                    grid.addDataItem(receiveData, index);
                }
                receiveParent.removeItem(draggableItem);
            },
            functionFailure: function (xhr, response) {
                PMDesigner.msgWinError(response.error.message);
            },
            flashContainer: that.getWindow(),
            messageError: [
                'An unexpected error while assigning the trigger, please try again later.'.translate()
            ],
            messageSuccess: [
                'Trigger assigned successfully.'.translate()
            ]
        });
        restClient.executeRestClient();
        return this;
    },

    /**
     * This method is executed when a row is sorted in the grid
     * @param receiveData, data of the droppable item
     * @param grid, the affected grid
     * @param index, the new index position row
     * @returns {stepsTask}
     */
    sortableRowHandler: function (receiveData, grid, index) {
        return new PMRestClient({
            endpoint: grid.step_uid === undefined ?
                'activity/' + this.getConfigWebEntry().tas_uid + "/step/trigger/" + receiveData.tri_uid : 'activity/' +
                this.getConfigWebEntry().tas_uid + "/step/" + grid.step_uid + "/trigger/" + receiveData.tri_uid,
            typeRequest: 'update',
            data: {
                st_type: receiveData.st_type,
                st_condition: receiveData.st_condition,
                st_position: index + 1
            },
            functionFailure: function (xhr, response) {
                PMDesigner.msgWinError(response.error.message);
            },
            flashContainer: this.getWindow(),
            messageError: 'An unexpected error while assigning the trigger, please try again later.'.translate(),
            messageSuccess: 'Trigger assigned successfully.'.translate()
        }).executeRestClient();
    },

    /**
     * Return the not items config.
     * @returns {{obj_title: *, obj_uid: string, id: string}}
     */
    notItemConfig: function () {
        var config = {
            obj_title: 'N/A'.translate(),
            obj_uid: '',
            id: "notItem"
        };
        return config;
    },

    /**
     * Get the steps assigned by a search criterion
     * @param criteria, search filter, after running the endpoint getAccordionData method
     * @returns {Array}, response with criteria
     */
    getStepsAssignedByCriteria: function (criteria) {
        var allAssigned = [],
            i,
            elements,
            j,
            resp,
            data,
            respon = [];

        this.getAccordionData(
            function (xhr, response) {
                allAssigned = response;
                if (jQuery.isArray(allAssigned)) {
                    for (i = 0; i < allAssigned.length; i += 1) {
                        resp = allAssigned[i];
                        if (typeof resp === "object") {
                            elements = resp.response ? resp.response : [];
                            for (j = 0; j < elements.length; j += 1) {
                                data = elements[j];
                                if (typeof data === "object") {
                                    if (data.step_type_obj && data.step_type_obj === criteria) {
                                        respon.push(data);
                                    }
                                }
                            }
                        }
                    }
                }
            },
            function (xhr, response) {
                PMDesigner.msgWinError(response.error.message);
            }
        );
        return respon;
    },

    /**
     * Opens the properties of the selected step with the current settings
     * @param step, is the data of selected step
     * @chainable
     */
    propertiesStepShow: function (step) {
        var that = this,
            form,
            saveButton,
            cancelButton,
            restClient,
            criteriaButton;
        this.setWindowAlternative(null);
        this.getWindowAlternativeForm().setWidth(520);
        this.getWindowAlternativeForm().setHeight(370);
        this.getWindowAlternativeForm().setTitle('Step Properties'.translate());
        form = new PMUI.form.Form({
            id: 'stepsEditCondition',
            width: 500,
            title: 'Condition Trigger'.translate(),
            visibleHeader: false,
            items: [
                {
                    id: 'step_mode',
                    pmType: 'radio',
                    label: 'Mode'.translate(),
                    value: '',
                    visible: step.step_type_obj === "DYNAFORM" ? true : false,
                    name: 'step_mode',
                    options: [{
                        id: 'modeEdit',
                        label: 'Edit'.translate(),
                        value: 'EDIT',
                        selected: true
                    }, {
                        id: 'modeView',
                        label: 'View'.translate(),
                        value: 'VIEW'
                    }]
                },
                new CriteriaField({
                    id: 'step_condition',
                    pmType: 'textarea',
                    name: 'step_condition',
                    valueType: 'string',
                    label: 'Condition'.translate(),
                    placeholder: 'Insert a condition'.translate(),
                    rows: 150,
                    controlsWidth: 250,
                    renderType: 'textarea'
                })
            ]
        });
        this.getWindowAlternativeForm().addItem(form);

        restClient = new PMRestClient({
            endpoint: 'activity/' + that.getConfigWebEntry().tas_uid + '/step/' + step.step_uid,
            typeRequest: 'get',
            functionSuccess: function (xhr, response) {
                form.getField('step_mode').setValue(response.step_mode);
                form.getField('step_condition').setValue(response.step_condition);
            },
            functionFailure: function (xhr, response) {
                PMDesigner.msgWinError(response.error.message);
            }
        });
        restClient.executeRestClient();
        saveButton = this.getWindowAlternativeForm().footer.getItem("secondaryWindow-save");
        cancelButton = this.getWindowAlternativeForm().footer.getItem("secondaryWindow-cancel");
        criteriaButton = this.getWindowAlternativeForm().footer.getItem("secondaryWindow-criteria");
        if (saveButton) {
            saveButton.setHandler(function () {
                var restClient;
                if (form.isValid()) {
                    restClient = new PMRestClient({
                        endpoint: 'activity/' + that.getConfigWebEntry().tas_uid + '/step/' + step.step_uid,
                        typeRequest: 'update',
                        data: form.getData(),
                        functionSuccess: function () {
                            that.getWindowAlternativeForm().close();
                        },
                        functionFailure: function (xhr, response) {
                            PMDesigner.msgWinError(response.error.message);
                        },
                        messageError: 'There are problems update the Step Trigger, please try again.'.translate()
                    });
                    restClient.executeRestClient();
                }
            });
        }
        if (cancelButton) {
            cancelButton.setHandler(function () {
                that.getWindowAlternativeForm().close();
            });
        }
        if (criteriaButton) {
            criteriaButton.handler = null;
            criteriaButton.setVisible(false);
        }
        this.getWindowAlternativeForm().open();
    },

    /**
     * Opens the step of the selected step with the current settings
     * @param step, is the data of selected step
     * @param accordioItem
     * @chainable
     */
    editStepShow: function (step, accordioItem) {
        var that = this,
            inputDocument;
        switch (step.step_type_obj) {
            case 'DYNAFORM':
                var restProxy = new PMRestClient({
                    endpoint: 'dynaform/' + step.step_uid_obj,
                    typeRequest: 'get',
                    functionSuccess: function (xhr, response) {
                        var old = PMUI.activeCanvas,
                            formDesigner;
                        PMUI.activeCanvas = false;
                        formDesigner = PMDesigner.dynaformDesigner(response);
                        formDesigner.onHide = function () {
                            var assignedDynaform,
                                i,
                                data,
                                title;
                            assignedDynaform = that.getStepsAssignedByCriteria("DYNAFORM");
                            if (jQuery.isArray(assignedDynaform)) {
                                for (i = 0; i < assignedDynaform.length; i += 1) {
                                    data = assignedDynaform[i];
                                    if (typeof data === "object") {
                                        if (data.step_uid === step.step_uid) {
                                            title = data.step_position + ". " + data.obj_title;
                                            title = title + ' (' + that.stepsType["DYNAFORM"] + ')';
                                            accordioItem.setTitle(title);
                                            accordioItem.dataItem = data;
                                        }
                                    }
                                }
                            }
                            PMUI.activeCanvas = old;
                        };
                    },
                    functionFailure: function (xhr, response) {
                        PMDesigner.msgWinError(response.error.message);
                    }
                });
                restProxy.executeRestClient();
                break;
            case 'OUTPUT_DOCUMENT':
                PMDesigner.output();
                PMDesigner.output.showTiny(step.step_uid_obj);
                break;
            case 'INPUT_DOCUMENT':
                inputDocument = new InputDocument({
                    onUpdateInputDocumentHandler: function (data, inputDoc) {
                        var position, title;
                        position = accordioItem.dataItem.step_position;
                        title = position + ". " + data.inp_doc_title;
                        title = title + ' (' + this.stepsType["INPUT_DOCUMENT"] + ')';
                        accordioItem.dataItem.obj_title = data.inp_doc_title;
                        accordioItem.setTitle(title);
                        inputDoc.winMainInputDocument.close();
                    }
                });
                inputDocument.build();
                inputDocument.openFormInMainWindow();
                inputDocument.inputDocumentFormGetProxy(step.step_uid_obj);
                break;
        }
    },

    /**
     * Edit the selected trigger condition
     * @param stepID, It is the id of the step to upgrade
     * @param triggerID, is the id of the trigger to update
     * @param stepType, It is the kind of step to update
     * @param row, PMUI.grid.GridPanelRow, is the row affected
     */
    editCondition: function (stepID, triggerID, stepType, row) {
        var that = this,
            saveButton,
            cancelButton,
            criteriaButton,
            form,
            dataRow;
        dataRow = row.getData();
        this.setWindowAlternative(null);
        this.getWindowAlternativeForm().setWidth(500);
        this.getWindowAlternativeForm().setHeight(350);
        this.getWindowAlternativeForm().setTitle('Condition Trigger'.translate());
        this.getWindowAlternativeForm().setTitle("Trigger".translate());
        form = new PMUI.form.Form({
            id: 'idFormEditCondition',
            width: 500,
            title: 'Condition Trigger'.translate(),
            visibleHeader: false,
            items: [
                new CriteriaField({
                    id: 'st_condition',
                    pmType: 'textarea',
                    name: 'st_condition',
                    valueType: 'string',
                    label: 'Condition'.translate(),
                    placeholder: 'Insert a condition'.translate(),
                    rows: 150,
                    controlsWidth: 250,
                    renderType: 'textarea',
                    value: dataRow.st_condition
                })
            ]
        });
        this.getWindowAlternativeForm().addItem(form);
        saveButton = this.getWindowAlternativeForm().footer.getItem("secondaryWindow-save");
        cancelButton = this.getWindowAlternativeForm().footer.getItem("secondaryWindow-cancel");
        criteriaButton = this.getWindowAlternativeForm().footer.getItem("secondaryWindow-criteria");
        if (saveButton) {
            saveButton.setHandler(function () {
                var data,
                    restClient;
                data = form.getData();
                data.st_type = stepType;
                restClient = new PMRestClient({
                    endpoint: 'activity/' + that.getConfigWebEntry().tas_uid + '/step/'
                    + ((typeof (stepID) != "undefined") ? stepID + "/" : "") + 'trigger/' + triggerID,
                    typeRequest: 'update',
                    data: data,
                    messageError: 'There are problems update the Step Trigger, please try again.'.translate(),
                    functionSuccess: function (xhr, response) {
                        dataRow.st_condition = data.st_condition;
                        row.setData(dataRow);
                    },
                    functionFailure: function (xhr, response) {
                        PMDesigner.msgWinError(response.error.message);
                    }
                });
                restClient.executeRestClient();
                that.getWindowAlternativeForm().close();
            });
        }
        if (cancelButton) {
            cancelButton.setHandler(function () {
                that.getWindowAlternativeForm().close();
            });
        }
        if (criteriaButton) {
            criteriaButton.setVisible(false);
            criteriaButton.handler = null;
        }
        this.getWindowAlternativeForm().open();
    },

    /**
     * This method is executed when editing a "trigger" in a row of the grid.
     * secondary window opens with the current configuration of the trigger
     * @param trigger, is the return value when is update 'trigger' action  in the enpoint
     * @param triggerID, is the id of the trigger to update
     * @chainable
     */
    editTrigger: function (trigger, triggerID) {
        var that = this,
            codeMirror,
            saveButton,
            cancelButton,
            criteriaButton;
        this.setWindowAlternative(null);
        codeMirror = new PMCodeMirror({
            id: "codeMirror"
        });
        CodeMirror.commands.autocomplete = function (cm) {
            CodeMirror.showHint(cm, CodeMirror.phpHint);
        };
        codeMirror.setValue(trigger);
        this.getWindowAlternativeForm().setWidth(DEFAULT_WINDOW_WIDTH);
        this.getWindowAlternativeForm().setHeight(DEFAULT_WINDOW_HEIGHT);
        this.getWindowAlternativeForm().setTitle("Trigger".translate());
        saveButton = this.getWindowAlternativeForm().footer.getItem("secondaryWindow-save");
        cancelButton = this.getWindowAlternativeForm().footer.getItem("secondaryWindow-cancel");
        criteriaButton = this.getWindowAlternativeForm().footer.getItem("secondaryWindow-criteria");
        if (saveButton) {
            saveButton.setHandler(function () {
                var restClient = new PMRestClient({
                    endpoint: 'trigger/' + triggerID,
                    typeRequest: 'update',
                    data: {
                        tri_param: '',
                        tri_webbot: codeMirror.getValue()
                    },
                    messageError: 'There are problems updating the trigger, please try again.'.translate(),
                    messageSuccess: 'Trigger updated correctly'.translate(),
                    flashContainer: that.getWindow(),
                    functionSuccess: function () {
                        that.getWindowAlternativeForm().close();
                    },
                    functionFailure: function (xhr, response) {
                        PMDesigner.msgWinError(response.error.message);
                    }
                });
                restClient.executeRestClient();
            });
        }
        if (cancelButton) {
            cancelButton.setHandler(function () {
                that.getWindowAlternativeForm().close();
            });
        }
        if (criteriaButton) {
            criteriaButton.setVisible(true);
            criteriaButton.setHandler(function () {
                var picker = new VariablePicker();
                picker.open({
                    success: function (variable) {
                        var cursorPos,
                            codemirror;
                        codemirror = codeMirror.cm;
                        cursorPos = codemirror.getCursor();
                        codemirror.replaceSelection(variable);
                        codemirror.setCursor(cursorPos.line, cursorPos.ch);
                    }
                });
            });
        }
        this.getWindowAlternativeForm().open();
        this.getWindowAlternativeForm().addItem(codeMirror);
        codeMirror.cm.setSize(this.getWindowAlternativeForm().getWidth(), 380);
        $(".CodeMirror.cm-s-default.CodeMirror-wrap").after($ctrlSpaceMessage.css({
            "padding-left": "10px",
            "margin": "3px 0px 0px 0px"
        }));
    },

    /**
     * Returns the type of step, for the execution of "endpoint"
     * @param st_type, this a step type, the accepted parameters are:
     *  - BEFORE_ASSIGNMENT
     *  - BEFORE_ROUTING
     *  - AFTER_ROUTING
     *  - BEFORE
     *  - AFTER
     * @returns {string}
     */
    getStepType: function (st_type) {
        var value;
        switch (st_type) {
            case 'BEFORE_ASSIGNMENT':
                value = 'before-assignment';
                break;
            case 'BEFORE_ROUTING':
                value = 'before-routing';
                break;
            case 'AFTER_ROUTING':
                value = 'after-routing';
                break;
            case 'BEFORE':
                value = 'before';
                break;
            case 'AFTER':
                value = 'after';
                break;
            default:
                value = '';
                break;
        }
        return value;
    },

    /**
     * Checks whether a trigger is already assigned in a grid
     * @param grid, is instanceof PMUI.grid.Grid, in conducting the search
     * @param tri_uid, search parameter in the rows of the grid
     * @returns {boolean}
     */
    isTriggerAssigned: function (grid, tri_uid) {
        var data, i, exist = false;
        data = grid.getData();
        if (grid && jQuery.isArray(data)) {
            for (i = 0; i < data.length; i += 1) {
                if (data[i].tri_uid === tri_uid) {
                    exist = true;
                    break;
                }
            }
        }
        return exist;
    },

    /**
     * This method eliminates the list of triggers trigger an assigned step
     * @param row, the row affected or selected
     * @param grid, It is affected or grid to remove selected row
     */
    removeTrigger: function (row, grid) {
        var that = this,
            message = 'Do you want to remove the trigger "',
            messageData = row.getData().tri_title ? row.getData().tri_title : "",
            yesButton,
            noButton,
            restClient;
        message = message + messageData + '"?';
        this.getConfirmWindow().setMessage(message.translate());
        yesButton = this.getConfirmWindow().footer.getItem("confirmWindow-footer-yes");
        if (yesButton) {
            yesButton.setHandler(function () {
                restClient = new PMRestClient({
                    endpoint: grid.step_uid === undefined ?
                        'activity/' + that.getConfigWebEntry().tas_uid + '/step/trigger/' + row.getData().tri_uid + '/'
                        + that.getStepType(row.getData().st_type) : 'activity/' + that.getConfigWebEntry().tas_uid
                        + '/step/' + grid.step_uid + '/trigger/' + row.getData().tri_uid
                        + '/' + that.getStepType(row.getData().st_type),
                    typeRequest: 'remove',
                    functionSuccess: function (xhr, response) {
                        grid.removeItem(row);
                        that.getConfirmWindow().close();
                        that.updateIndexToGrid(grid);
                    },
                    functionFailure: function (xhr, response) {
                        PMDesigner.msgWinError(response.error.message);
                    },
                    flashContainer: that.getWindow(),
                    messageError: 'An unexpected error while deleting the trigger, please try again later.'.translate(),
                    messageSuccess: 'Trigger removed successfully'.translate()
                });
                restClient.executeRestClient();
            });
        }
        noButton = this.getConfirmWindow().footer.getItem("confirmWindow-footer-no");
        if (noButton) {
            noButton.setHandler(function () {
                that.getConfirmWindow().close();
            });
        }
        this.getConfirmWindow().open();
    },

    /**
     * Updates indexes of elements selected grid
     * @param grid, It is affected or grid to remove selected row
     * @returns {stepsTask}
     */
    updateIndexToGrid: function (grid) {
        var cell, rows, i, row;
        if (grid) {
            rows = grid.getItems();
            if (jQuery.isArray(rows)) {
                for (i = 0; i < rows.length; i += 1) {
                    row = rows[i];
                    cell = row.cells.find("columnData");
                    if (cell) {
                        cell.setContent(i + 1);
                    }
                }
            }
        }
        return this;
    },

    /**
     * Add custom buttons on the head of an element of stepsAssignAccordion
     * are three buttons
     * properties
     * edit
     * remove
     * @param html, is the html of the header accordion item
     * @param step, the data of the step selected
     * @param accordionItem
     */
    customAccordionItemButtons: function (html, step, accordionItem) {
        var that = this,
            propertiesStep,
            editStep,
            removeStep,
            $html,
            containerButtons,
            title;
        if (html) {
            $html = jQuery(html.getElementsByClassName("pmui-accordion-item-header"));
            title = step.obj_title + ' (' + step.step_type_obj + ')';
            $html.find(".pmui-accordion-item-title").get(0).title = title;
            containerButtons = $('<div></div>');
            containerButtons.addClass("propertiesTask-accordionItem");
            propertiesStep = $('<a>' + 'Properties'.translate() + '</a>');
            propertiesStep.addClass("mafe-button-edit propertiesTask-accordionButton");
            editStep = $('<a>' + 'Edit'.translate() + '</a>');
            editStep.addClass("mafe-button-edit propertiesTask-accordionButton");
            removeStep = $('<a>' + 'Remove'.translate() + '</a>');
            removeStep.addClass("mafe-button-delete propertiesTask-accordionButton");

            propertiesStep.click(function (e) {
                e.preventDefault();
                e.stopPropagation();
                that.propertiesStepShow(step);
                return false;
            });

            editStep.click(function (e) {
                e.preventDefault();
                e.stopPropagation();
                that.editStepShow(step, accordionItem);
                return false;
            });

            removeStep.click(function (e) {
                e.preventDefault();
                e.stopPropagation();
                that.removeStepShow(step);
                return false;
            });
            containerButtons.append(propertiesStep);
            containerButtons.append(editStep);
            containerButtons.append(removeStep);
            $html.append(containerButtons);
        }
    },

    /**
     * Add tooltip in treeNode elements
     * @returns {stepsTask}
     */
    updateIndexPosition: function (treeNode) {
        var items,
            i,
            item,
            $item,
            text,
            data;
        if (treeNode && treeNode.html) {
            items = treeNode.getItems();
            if (jQuery.isArray(items)) {
                for (i = 0; i < items.length; i += 1) {
                    item = items[i];
                    if (item.html) {
                        $item = $(item.html);
                        data = item.getData();
                        text = $item.find("a").get(0);
                        text.title = data.obj_title;
                    }
                }
            }
        }
        return this;
    },

    /**
     * This method loads the data to stepsAssignTree
     * @param response, the answer is an array containing all the elements
     * that will be loaded into the step stepsAssignTree
     * @chainable
     */
    loadTreePanelData: function (response) {
        var data,
            i,
            j,
            type,
            label,
            items = [],
            dataTree = [],
            treeNode;
        data = response[1].response;

        dataTree.push({
            obj_title: 'Trigger (s)'.translate(),
            items: [this.notItemConfig()]
        });

        for (i = 0; i < data.length; i += 1) {
            items.push({
                obj_title: data[i]['tri_title'],
                obj_type: data[i]['tri_type'],
                obj_uid: data[i]['tri_uid']
            });
        }

        if (items.length) {
            dataTree.shift();
            dataTree.push({
                obj_title: 'Trigger (s)'.translate(),
                items: items,
                id: "TRIGGER"
            });
        }

        data = response[0].response;
        type = this.groupType;
        label = this.groupLabel;
        items = [];
        for (i = 0; i < type.length; i += 1) {
            items = [];
            for (j = 0; j < data.length; j += 1) {
                if (type[i] === data[j].obj_type) {
                    items.push({
                        obj_title: data[j]['obj_title'],
                        obj_type: data[j]['obj_type'],
                        obj_uid: data[j]['obj_uid']
                    });
                }
            }
            if (items.length === 0) {
                dataTree.push({
                    obj_title: label[i].translate(),
                    items: [this.notItemConfig()],
                    behavior: '',
                    id: type[i]
                });
            } else {
                dataTree.push({
                    obj_title: label[i].translate(),
                    items: items,
                    behavior: 'drag',
                    id: type[i]
                });
            }
        }
        this.getStepsAssignTree().clearItems();
        for (i = 0; i < dataTree.length; i += 1) {
            this.getStepsAssignTree().addDataItem(dataTree[i]);
            treeNode = this.getStepsAssignTree().getItem(i);
            treeNode.setID(dataTree[i].id);
            this.updateIndexPosition(treeNode);
        }
        return this;
    },

    /**
     * It is an extension to add the "sortable" event "stepsAssignTree".
     * when choosing a node treePanel and you want to add to the accordion or the grid
     * @chainable
     */
    addEventSortableInTreePanelElements: function () {
        var that = this,
            items = this.getStepsAssignTree().getItems(),
            connect,
            i,
            sw,
            nodeItems;
        for (i = 0; i < items.length; i += 1) {
            nodeItems = items[i].getItems();
            if (nodeItems.length && nodeItems[0].getData().obj_type) {
                sw = items[i].getItems()[0].getData().obj_type === "SCRIPT";
                connect = sw ? ".pmui-gridpanel-tbody" : ".pmui-accordion-panel-body";
                $(items[i].html).find('ul').find('>li').draggable({
                    appendTo: document.body,
                    revert: "invalid",
                    helper: "clone".translate(),
                    cursor: "move",
                    zIndex: 1000,
                    connectToSortable: connect,
                    start: function (e) {
                        var i, nodeTag, node, nodeData, accordionItems, item;
                        nodeTag = e.target;
                        node = PMUI.getPMUIObject(nodeTag);
                        nodeData = node.getData();
                        accordionItems = that.getStepsAssignAccordion().getItems();
                        $(that.getStepsAssignAccordion().body).hide();
                        if (nodeData.obj_type !== "SCRIPT") {
                            for (i = 0; i < accordionItems.length; i += 1) {
                                item = accordionItems[i];
                                item.collapse();
                            }
                        }
                        $(that.getStepsAssignAccordion().body).show();
                    },
                    stop: function () {
                        var i = 0,
                            max;
                        if (that.elementAccordionOpen) {
                            max = that.elementAccordionOpen.getSize();
                            for (i = 0; i < max; i += 1) {
                                that.elementAccordionOpen.get(i).expand();
                            }
                        }
                    }
                });
            } else {
                $(nodeItems[0].html).draggable("disable");
            }
        }
    },

    /**
     * It is an extension to add the "sortable" event "stepAssignAccordion".
     * when a node "treePanel" is added to stop runs and is where you choose if it's a sort or aggregation.
     * @chainable
     */
    addEventSortableInAccordionElements: function () {
        var that = this,
            tagContainer,
            newIndex,
            index,
            treeNodeObject,
            treeNodeData;
        if (this.getStepsAssignAccordion() && this.getStepsAssignAccordion().html) {
            tagContainer = this.getStepsAssignAccordion().body;
            $(tagContainer).sortable({
                items: '>div:not(#idAssignment,#idRouting)',
                placeholder: 'steps-placeholder',
                receive: function (event, ui) {
                    var item = ui ? ui.item : null;
                    if (item && item instanceof jQuery && item.length) {
                        treeNodeObject = PMUI.getPMUIObject(item.get(0));
                        treeNodeData = treeNodeObject.getData();
                    }
                },
                stop: function (event, ui) {
                    var itemClone = ui ? ui.item : null,
                        accordionItems,
                        accordionItem,
                        dataEdited,
                        restClientMultipart,
                        restClient;
                    var newIndex = ui.item.index();
                    accordionItems = that.getStepsAssignAccordion().getItems();
                    if (itemClone && itemClone instanceof jQuery && itemClone.length) {
                        if (treeNodeObject) {
                            itemClone.remove();
                            if (newIndex + 1 > accordionItems.length) {
                                newIndex = that.stepsAssigned.getSize();
                            }
                            restClient = new PMRestClient({
                                endpoint: 'activity/' + that.getConfigWebEntry().tas_uid + '/step',
                                typeRequest: 'post',
                                data: {
                                    step_type_obj: treeNodeData.obj_type,
                                    step_uid_obj: treeNodeData.obj_uid,
                                    step_condition: '',
                                    step_position: newIndex + 1,
                                    step_mode: 'EDIT'
                                },
                                functionSuccess: function (xhr, response) {
                                    var item, buttonAfected, treeNode;
                                    that.getStepsAssignTree().removeItem(treeNodeObject);
                                    treeNode = that.getStepsAssignTree().items.find("id", response.step_type_obj);
                                    if (treeNode.items.getSize() === 0) {
                                        treeNode.addDataItem(that.notItemConfig());
                                    }
                                    response.obj_description = '';
                                    response.obj_title = treeNodeData.obj_title;
                                    response.triggers = [];
                                    item = that.createAccordionItem(response, true, true);
                                    item.dataItem = response;
                                    if (that.getStepsAssignAccordion().items.getSize() === 2) {
                                        that.getStepsAssignAccordion().addItem(item, 0);
                                    } else {
                                        that.getStepsAssignAccordion().addItem(item, newIndex);
                                    }
                                    that.getStepsAssignAccordion().defineEvents();
                                    that.customAccordionItemButtons(item.html, response, item);
                                    that.updateItemIndexToAccordion();
                                    that.addEventSortableInAccordionElements();
                                    that.addEventSortableInTreePanelElements();
                                    buttonAfected = that.getLabelsPanel().getItem("expand-button");
                                    buttonAfected.setDisabled(false);
                                },
                                functionFailure: function (xhr, response) {
                                    PMDesigner.msgWinError(response.error.message);
                                },
                                messageError: 'An unexpected error while assigning the step, please try again later.'
                                    .translate(),
                                messageSuccess: 'Step assigned successfully.'.translate(),
                                flashContainer: that.getStepsAssignAccordion().getParent()
                            });
                            restClient.executeRestClient();
                        } else {
                            accordionItem = PMUI.getPMUIObject(ui.item.get(0));
                            index = that.getStepsAssignAccordion().items.indexOf(accordionItem);
                            if (newIndex !== index) {
                                that.getStepsAssignAccordion().items.remove(accordionItem);
                                that.getStepsAssignAccordion().items.insertAt(accordionItem, newIndex);
                                dataEdited = {
                                    step_position: newIndex + 1,
                                    step_uid: accordionItem.dataItem.step_uid,
                                    step_type_obj: accordionItem.dataItem.step_type_obj,
                                    step_uid_obj: accordionItem.dataItem.step_uid_obj
                                };
                                restClientMultipart = new PMRestClient({
                                    endpoint: 'activity/' + that.getConfigWebEntry().tas_uid + '/step/'
                                    + accordionItem.dataItem.step_uid,
                                    typeRequest: 'update',
                                    data: dataEdited,
                                    functionSuccess: function (xhr, response) {
                                        that.updateItemIndexToAccordion();
                                    },
                                    functionFailure: function (xhr, response) {
                                        PMDesigner.msgWinError(response.error.message);
                                    },
                                    messageError: 'An unexpected error while editing the step, please try again later.'
                                        .translate(),
                                    messageSuccess: 'Step editing successfully.'.translate(),
                                    flashContainer: that.getWindow()
                                });
                                restClientMultipart.executeRestClient();
                            }
                        }
                    }
                },
                start: function (e, ui) {
                    newIndex = ui.item.index();
                }
            });
        }
    },

    /**
     * Updates indexes of elements assigned
     * @returns {stepsTask}
     */
    updateItemIndexToAccordion: function () {
        var title,
            i,
            item,
            dataItem,
            items = this.getStepsAssignAccordion().items,
            position,
            max;
        max = items.getSize();
        for (i = 0; i < max; i += 1) {
            item = items.get(i);
            position = items.indexOf(item);
            dataItem = item.dataItem;
            title = (position + 1) + ".  " + dataItem.obj_title;
            if (this.stepsType[dataItem.step_type_obj]) {
                title = title + ' (' + this.stepsType[dataItem.step_type_obj] + ')';
            }
            item.dataItem.step_position = i + 1;
            item.setTitle(title);
        }
        return this;
    },

    /**
     * The window opens for confirmation of the removal step
     * @param step, the current step to remove
     * @chainable
     */
    removeStepShow: function (step) {
        var that = this,
            title,
            yesButton,
            noButton,
            restClient;
        if (this.stepsType[step.step_type_obj] !== undefined) {
            title = "Step {0} ( {1} )".translate([step.obj_title, this.stepsType[step.step_type_obj]]);
            this.getConfirmWindow().setTitle(title);
        } else {
            this.getConfirmWindow().setTitle("Step " + step.step_type_obj.capitalize());
        }
        this.getConfirmWindow().setMessage("Do you want to remove the step '{0}'?".translate([step.obj_title]));
        yesButton = this.getConfirmWindow().footer.getItem("confirmWindow-footer-yes");
        noButton = this.getConfirmWindow().footer.getItem("confirmWindow-footer-no");
        if (yesButton) {
            yesButton.setHandler(function () {
                restClient = new PMRestClient({
                    endpoint: 'activity/' + that.getConfigWebEntry().tas_uid + '/step/' + step.step_uid,
                    typeRequest: 'remove',
                    functionSuccess: function (xhr, response) {
                        that.removingStepTask(step, response);
                        that.getConfirmWindow().close();
                        that.updateItemIndexToAccordion();
                    },
                    functionFailure: function (xhr, response) {
                        PMDesigner.msgWinError(response.error.message);
                    },
                    messageError: 'An unexpected error while deleting the step, please try again later.'.translate(),
                    messageSuccess: 'Step removed successfully'.translate(),
                    flashContainer: that.getWindow().getParent()
                });
                restClient.executeRestClient();
            });
        }
        if (noButton) {
            noButton.setHandler(function () {
                that.getConfirmWindow().close();
            });
        }
        this.getConfirmWindow().open();
    },

    /**
     * Get the steps is not assigned by a criterion
     * @param criteria, It is the filter criteria search
     * @param stepAvailable, all steps Unassigned
     * @returns {Array}, filtered items
     */
    getAvailablesStepsByCriteria: function (criteria, stepAvailable) {
        var items = [],
            i;
        if (jQuery.isArray(stepAvailable)) {
            for (i = 0; i < stepAvailable.length; i += 1) {
                if (stepAvailable[i].obj_type === criteria) {
                    items.push(stepAvailable[i]);
                }
            }
        }
        return items;
    },

    /**
     * Eliminates the step of step Assign Accordion
     * @param step, the current step to remove
     * @param response, data from the endpoint
     */
    removingStepTask: function (step, response) {
        var stepObject,
            stepAvailable,
            treeNodeObject,
            stepAvailables,
            i,
            itemsTreeNode = [],
            items = [];
        stepObject = this.getStepsAssignAccordion().getItem("id" + step.step_uid_obj);
        this.elementAccordionOpen.remove(stepObject);
        this.stepsAssigned.remove(stepObject);
        this.getStepsAssignAccordion().removeItem(stepObject);
        if (stepObject) {
            this.getStepAvailables(
                function (xhr, response) {
                    stepAvailable = response[0] ? response[0].response : [];
                },
                function (xhr, response) {
                    PMDesigner.msgWinError(response.error.message);
                }
            );
            stepAvailables = this.getAvailablesStepsByCriteria(step.step_type_obj, stepAvailable);
            for (i = 0; i < stepAvailables.length; i += 1) {
                items.push({
                    obj_title: stepAvailables[i]['obj_title'],
                    obj_type: stepAvailables[i]['obj_type'],
                    obj_uid: stepAvailables[i]['obj_uid']
                });
            }
            treeNodeObject = this.getStepsAssignTree().getItem(step.step_type_obj);
            itemsTreeNode = treeNodeObject.getItems();
            for (i = 0; i < itemsTreeNode.length; i += 1) {
                treeNodeObject.removeItem(itemsTreeNode[i]);
            }
            treeNodeObject.clearItems();
            treeNodeObject.setDataItems(items);
            this.updateIndexPosition(treeNodeObject);
            this.addEventSortableInTreePanelElements();
            this.addEventSortableInAccordionElements();
        }
    },

    /**
     * This method is executed when an element stepsAssignTree, is assigned in a grid
     * @param receiveData, data of the droppable item
     * @param grid, the affected grid
     * @param index, the index position row
     * @returns {stepsTask}
     */
    receiveTreeNodeItem: function (receiveData, grid, index) {
        var restClient,
            message,
            that = this;
        if (this.isTriggerAssigned(grid, receiveData.obj_uid)) {
            message = new PMUI.ui.FlashMessage({
                message: 'Trigger is assigned.'.translate(),
                duration: 3000,
                severity: 'error',
                appendTo: this.getWindow()
            });
            message.show();
            return;
        }
        restClient = new PMRestClient({
            endpoint: grid.step_uid === undefined ?
                'activity/' + that.getConfigWebEntry().tas_uid + '/step/trigger' : 'activity/'
                + that.getConfigWebEntry().tas_uid + '/step/' + grid.step_uid + '/trigger',
            typeRequest: 'post',
            data: {
                tri_uid: receiveData.obj_uid,
                st_type: grid.st_type,
                st_condition: '',
                st_position: index + 1
            },
            functionSuccess: function (xhr, response) {
                grid.addDataItem({
                    st_condition: '',
                    st_position: index + 1,
                    st_type: grid.st_type,
                    tri_description: '',
                    tri_title: receiveData.obj_title,
                    tri_uid: receiveData.obj_uid,
                    obj_title: receiveData.obj_title,
                    obj_uid: receiveData.obj_uid
                }, index);
            },
            functionFailure: function (xhr, response) {
                PMDesigner.msgWinError(response.error.message);
            }
        });
        restClient.executeRestClient();
        return this;
    }
};