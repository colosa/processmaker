var SuggestField = function (settings) {
    this.id = settings["id"] || "";
    this.label = settings["label"] || null;
    this.placeholder = settings["placeholder"] || null;
    this.type = "suggest";
    this.value = settings["value"] || "";
    this.form = settings["form"] || null;
    this.required = settings["required"] || null;
    this.disabled = settings["disabled"] || false;
    this.maxLength = settings["maxLength"] || null;
    this.mode = settings["mode"] || "edit";
    this.options = settings["options"] || [];
    this.containerList = null;
    this.width = settings.width || "auto";
    this.messageRequired = null;
    this.searchLoad = null;
    this.helper = settings.helper || null;
    this.html = null;
    this.responseAjax;
    this.data = null;
    this.separatingText = settings.separatingText || null;
    this.setDynamicLoad(settings.dynamicLoad);
    this.dom = {};
    this.dom.fieldRequired = null

};
/**
 * Disables the field. Notice that when a field is disabled it is not validated and it is not returned when its
 * form's getData() method is invoked.
 * @chainable
 */
SuggestField.prototype.disable = function () {

    this.disabled = true;
    this.inputField.prop('disabled', true);

    return this;
};
/**
 * Enables the field. Notice that when a field is disabled it is not validated and it is not returned when its
 * form's getData() method is invoked.
 * @chainable
 */
SuggestField.prototype.enable = function () {

    this.disabled = false;
    this.inputField[0].disabled = false;

    return this;
};

SuggestField.prototype.hideRequired = function (){
    this.dom.fieldRequired.style.display = 'none';
    return this;
};
/**
 * Disables the field. Notice that when a field is disabled it is not validated and it is not returned when its
 * form's getData() method is invoked.
 * @chainable
 */
SuggestField.prototype.disable = function () {
    this.disabled = true;
    this.inputField[0].disabled = true;

    return this;
};

SuggestField.prototype.showRequired = function (){
    this.dom.fieldRequired.style.display = 'inline-block';
    return this;
};
/**
 * Sets if the fields is required or not.
 * @param {Boolean} required
 * @chainable
 */
SuggestField.prototype.setRequired = function (required) {
    this.required = !!required;
    if (this.dom.fieldRequired) {
        if (this.required) {
            this.showRequired();
        } else {
            this.hideRequired();
        }
    }
    return this;
};


SuggestField.prototype.setDynamicLoad = function (dynamicLoad) {
    this.dynamicLoad = !!dynamicLoad ? dynamicLoad : false;
    if (this.dynamicLoad.hasOwnProperty("keys")) {
        this.setKeys(this.dynamicLoad["keys"]);
    }
    return this;
};

SuggestField.prototype.setKeys = function (keys) {
    var endPoints = [];
    this.keys = keys || {};
    return this;
};

SuggestField.prototype.createHTML = function () {
    this.containerLabel;
    this.tooltip;
    this.containerLabel = $(document.createElement("div"));
    this.containerLabel.css({
        width: "23.5%",
        display: "inline-block",
        float: "left",
        "text-align": "left",
        "margin-right": "5px",
        padding: "6px 30px 6px 10px",
        "box-sizing": "border-box",
        "color": "rgb(45, 62, 80)",
        "font-size": "14px"
    });

    this.tooltip = $(document.createElement("span"));
    this.tooltip[0].className = "pmui-icon pmui-icon-help";
    this.tooltip.css({
        "vertical-align": "middle",
        "right": "52px",
        top: "0px",
        width: "18px",
        height: "18px",
        position: "relative",
        "z-index": "auto",
        "float": "right"
    });

    this.requiredTag = $(document.createElement("span"));

    this.colonTag = $(document.createElement("span"));

    this.inputField = $(document.createElement("input"));
    if (this.disabled) {
        this.disable();
    }
    this.inputLabel = $(document.createElement("label"));

    this.inputLabel[0].textContent = this.label || "unlabel :";
    this.requiredTag[0].textContent = "*";
    this.requiredTag.css({
        color: "red"
    });
    this.colonTag[0].textContent = ":";
    this.containerLabel[0].appendChild(this.inputLabel[0]);
    if (this.required) {
        this.containerLabel[0].appendChild(this.requiredTag[0]);
        this.dom.fieldRequired = this.requiredTag[0];
    }
    this.containerLabel[0].appendChild(this.colonTag[0]);
    this.html = $(document.createElement("div"));
    this.html.attr("id", this.id);
    this.html.addClass("suggest-field");
    this.html.addClass("suggest-field pmui-field");
    this.html.append(this.containerLabel[0]);
    this.html.append(this.inputField);
    if (this.helper) {
        this.tooltip[0].setAttribute("title", this.helper);
        this.tooltip.tooltip();
        this.html.append(this.tooltip);
    }
    this.inputField[0].placeholder = this.placeholder.translate();
    $(this.inputLabel).css({
        display: "inline-block",
        float: "left"
    });
    this.containerList = $(document.createElement("ul"));

    this.setStyle();
    this.atachListener();
    this.html.find("input").after(this.containerList);

    if (this.dynamicLoad) {
        this.searchLoad = $(PMUI.createHTMLElement("span"));
        this.searchLoad.addClass("pmui-gridpanel-searchload");
        this.searchLoad.css({
            "margin-left": "-23px",
            "margin-top": "5px"
        });
        this.html.append(this.searchLoad);
    }
    return this.html[0];
};

SuggestField.prototype.atachListener = function () {
    var value = "", that = this, itemLabel;
    this.html.find("input").keyup(function (e) {
        value = this.value;
        if (value) {
            that.hideMessageRequired();
            that.containerList.css({
                top: $(e.target).position().top + 18,
                "margin-left": $(e.target).position().left - 10
            });
            that.containerList.show();
            if (!that.dynamicLoad) {
                that.makeOptions(this.value, 10);
            } else {
                that.containerList.empty();
                that.executeAjax(this.value);
            }
        } else {
            that.containerList.hide();
        }
    });
    $("body").click(function (e) {
        that.containerList.hide();
    });
    return this;
};

SuggestField.prototype.makeOptions = function (val, maxItems) {
    var elements = this.options, i, count = 0, that = this, items = [];
    this.containerList.empty().css({
        "max-width": this.html.find("input").outerWidth()
    });
    if (!this.dynamicLoad) {
        $.grep(elements, function (data, index) {
            itemLabel = data.label;
            if ((itemLabel.toLowerCase().indexOf(val.toLowerCase()) !== -1) && count < maxItems) {
                items.push(data);
            }
        });
    }
    this.createItems(items);
    return this;
};
SuggestField.prototype.executeAjax = function (criteria) {
    var responseAjax, that = this, options = [], endpoints = [], restClient;
    this.searchLoad.addClass("load");
    for (var i = 0; i < this.keys.endpoints.length; i += 1) {
        url = this.keys.endpoints[i]["url"] + "?filter=" + (criteria ? criteria : "") + "&start=0&limit=10";
        method = this.keys.endpoints[i]["method"];
        endpoints.push({
            url: url,
            method: method
        });
    }
    restClient = new PMRestClient({
        typeRequest: 'post',
        multipart: true,
        data: {
            calls: endpoints
        },
        functionSuccess: function (xhr, response) {
            var responseAjax = [], dataItem, i;
            for (i = 0; i < response.length; i += 1) {
                if (that.separatingText) {
                    responseAjax.push(that.separatingText[i]);
                }
                responseAjax = responseAjax.concat(response[i].response);
            }
            data = responseAjax;
            for (i = 0; i < data.length; i += 1) {
                if (typeof data[i] === "object") {
                    for (var j = 0; j < that.dynamicLoad["data"].length; j += 1) {
                        if (data[i][that.dynamicLoad["data"][j]["key"]]) {
                            var showLabel = " ";
                            for (var k = 0; k < that.dynamicLoad["data"][j]["label"].length; k += 1) {
                                if (data[i].hasOwnProperty(that.dynamicLoad["data"][j]["label"][k])) {
                                    showLabel = showLabel + data[i][that.dynamicLoad["data"][j]["label"][k]] + " ";
                                } else {
                                    showLabel = showLabel + that.dynamicLoad["data"][j]["label"][k] + " ";
                                }
                            }
                            dataItem = {
                                label: showLabel,
                                value: data[i][that.dynamicLoad["data"][j]["key"]],
                                data: data[i]
                            }
                            options.push(dataItem);
                        }
                    }
                } else {
                    dataItem = {
                        label: data[i],
                        value: null,
                        data: null
                    }
                    options.push(dataItem);
                }
            }
            that.options = options;
            that.createItems(that.options, that.options.length);
            that.searchLoad.removeClass("load");
            return responseAjax;
        }
    });
    restClient.setBaseEndPoint('');
    restClient.executeRestClient();
    return this;
};
SuggestField.prototype.createItems = function (items) {
    var that = this, i, li, span;
    this.containerList.empty();
    for (i = 0; i < items.length; i += 1) {
        li = document.createElement("li");
        li.className = "list-suggest-item";
        span = document.createElement("span");
        $(span).css({
            width: "auto",
            display: "block",
            paddingLeft: "10px",
            paddingTop: "2px",
            paddingBottom: "2px",
            paddingRight: "2px",
            height: "100%"
        });
        span.innerHTML = items[i].label;
        span.setAttribute("data-value", items[i].value);
        $(span).data({data: items[i]["data"]});
        li.appendChild(span);
        if (items[i].value) {
            $(li).css({
                position: "relative",
                display: "block",
                "background-color": "#fff",
                border: "1px solid #ddd",
                "box-sizing": "border-box",
                "height": "auto",
                "line-height": "20px",
                "width": this.inputField.outerWidth()
            });
            li.onclick = this.onclickItem();
        } else {
            $(li).css({
                position: "relative",
                display: "block",
                "background-color": "#3397e1",
                "color": "white",
                "border": "1px solid #ddd",
                "box-sizing": "border-box",
                "height": "auto",
                "line-height": "20px",
                "width": this.inputField.outerWidth(),
                "text-align": "center",
                "cursor": "default",
                "text-transform": "uppercase",
                "font-weight": "bold"
            }).addClass("single-label");
        }
        this.containerList.append(li);
        if (this.containerList.children.length > 4) {
            this.containerList.css("height", "200px");
        } else {
            this.containerList.css("height", "auto");
        }
    }
    this.containerList.show();
    return this;
};

SuggestField.prototype.onclickItem = function () {
    var that = this;
    return function (e) {
        var item, text;
        item = e.currentTarget;
        text = item.textContent;
        that.html.find("input").val(text);
        that.value = $(item).find("span").data().value;
        that.data = $(item).find("span").data().data;
        that.containerList.hide();
    }
};

SuggestField.prototype.setStyle = function () {
    this.html.find("input").css({
        left: "0px",
        top: "0px",
        width: this.width,
        height: "30px",
        position: "relative",
        "z-index": "auto",
        "box-sizing": "border-box",
        "padding": "2px 10px 2px 10px"
    });
    this.containerList.css({
        position: "absolute",
        "z-index": "3",
        "border-radius": "5px",
        "height": "auto",
        "box-shadow": "2px 10px 29px #818181",
        cursor: "pointer",
        overflow: "scroll",
        "padding-right": "0",
        "margin-bottom": "20px",
        "padding-left": "0",
        "max-height": "200px",
        "display": "none"
    });
    return this;
};

SuggestField.prototype.get = function (prototype) {
    if (this[prototype]) {
        return this[prototype];
    } else {
        return null;
    }
};

SuggestField.prototype.set = function (prototype, value) {
    this[prototype] = value;
    return this;
};

SuggestField.prototype.showMessageRequired = function () {
    if (this.messageRequired == null) {
        var messageRequired = $('<span class="pmui-field-message" style="display: block; margin-left: 24%;"><span  class="pmui pmui-textlabel" style="left: 0px; top: 0px; width:auto; height: auto; position: relative; z-index: auto;">' + "This field is required.".translate() + '</span></span>');
        this.messageRequired = messageRequired;
        this.html.find('ul').after(messageRequired);
    }
    else {
        this.messageRequired.show();
    }
};

SuggestField.prototype.hideMessageRequired = function () {
    if (this.messageRequired != null) {
        this.messageRequired.hide();
    }
};

SuggestField.prototype.isValid = function () {
    if (this.required && this.html.find("input").val() === "") {
        return false
    }
    return true;
};