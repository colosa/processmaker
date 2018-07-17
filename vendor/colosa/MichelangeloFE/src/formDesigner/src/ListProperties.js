(function () {
    var ListProperties = function () {
        ListProperties.prototype.init.call(this);
    };
    ListProperties.prototype.init = function () {
        this.tbody = $("<tbody class='fd-tbody'></tbody>");
        this.table = $(
            "<table class='fd-table' style='width:100%;'>" +
            "<thead>" +
            "<th class='fd-table-th' style='min-width:30px;width:50px;max-width:100px;'><span>" + "Property".translate() + "</span></th>" +
            "<th class='fd-table-th' style='width:auto;'><span>" + "Value".translate() + "</span>" +
            "</th>" +
            "</thead>" +
            "</table>");
        this.table.append(this.tbody);
        this.body = $("<div></div>");
        this.body.append(this.table);
    };
    ListProperties.prototype.addItem = function (propertiesGot, property, properties) {
        var that = this, input, i, width = "width:100%;border:1px solid gray;box-sizing:border-box;", id = FormDesigner.generateUniqueId();
        switch (propertiesGot[property].type) {
            case "label":
                //improvement changes in value
                var a = propertiesGot[property].value;
                if (property === "type" && a === "cell") {
                    a = "row";
                }
                input = $("<div style='position:relative;width:100%;overflow:hidden;height:16px;'><span style='position:absolute;-webkit-user-select:text;-moz-user-select:text;user-select:text;'>" + a + "</span></div>");
                properties.setNode(property, input[0]);
                break;
            case "text":
                input = $("<input type='text' style='" + width + "' id='" + id + "'>").val(propertiesGot[property].value);
                input.on(propertiesGot[property].on ? propertiesGot[property].on : "keyup", function () {
                    properties.set(property, this.value);
                });
                input.attr("placeholder", propertiesGot[property].placeholder ? propertiesGot[property].placeholder : "");
                properties.setNode(property, input[0]);
                break;
            case "textarea":
                input = $("<textarea style='" + width + "resize: vertical;' id='" + id + "'></textarea>").val(propertiesGot[property].value);
                input.on("keyup", function () {
                    properties.set(property, this.value);
                });
                input.attr("placeholder", propertiesGot[property].placeholder ? propertiesGot[property].placeholder : "");
                properties.setNode(property, input[0]);
                break;
            case "select":
                input = $("<select style='" + width + "' id='" + id + "'></select>");
                for (i = 0; i < propertiesGot[property].items.length; i++)
                    input.append("<option value='" + propertiesGot[property].items[i].value + "'>" + propertiesGot[property].items[i].label + "</option>");
                input.val(propertiesGot[property].value);
                input.on("change", function () {
                    properties.set(property, this.value);
                });
                properties.setNode(property, input[0]);
                break;
            case "checkbox":
                input = $("<input type='checkbox' id='" + id + "'>");
                input.prop("checked", propertiesGot[property].value);
                input.on("change", function () {
                    properties.set(property, this.checked);
                });
                properties.setNode(property, input[0]);
                break;
            case "button":
                input = $("<input type='button' value='" + propertiesGot[property].labelButton + "' id='" + id + "'>");
                input.on("click", function () {
                    properties.onClick(property, this);
                });
                properties.setNode(property, input[0]);
                break;
            case "labelbutton":
                var a = propertiesGot[property].value;
                if (typeof a === "object")
                    a = JSON.stringify(a);
                if (a === "")
                    a = propertiesGot[property].labelButton;
                input = $("<div style='position:relative;width:100%;overflow:hidden;height:16px;'><span style='position:absolute;-webkit-user-select:text;-moz-user-select:text;user-select:text;text-decoration:underline;cursor:pointer;white-space:nowrap;'></span></div>");
                input.find("span").text(a).on("click", function (e) {
                    e.stopPropagation();
                    if ($(e.target).data("disabled") === true)
                        return;
                    properties.onClick(property, input.find("span")[0]);
                });
                properties.setNode(property, input.find("span")[0]);
                break;
            case "textbutton":
                input = $("<input type='text' style='" + width + "' id='" + id + "'><input type='button' value='" + propertiesGot[property].labelButton + "'>");
                $(input[0]).val(propertiesGot[property].value);
                $(input[0]).on("keyup", function () {
                    properties.set(property, this.value);
                });
                $(input[0]).attr("placeholder", propertiesGot[property].placeholder ? propertiesGot[property].placeholder : "");
                $(input[1]).on("click", function () {
                    properties.onClick(property, this);
                });
                properties.setNode(property, input[0]);
                break;
            case "textareabutton":
                input = $("<textarea style='" + width + "resize:vertical;height:50px;' id='" + id + "'></textarea><input type='button' value='" + propertiesGot[property].labelButton + "'>");
                $(input[0]).val(propertiesGot[property].value);
                $(input[0]).on("keyup", function () {
                    properties.set(property, this.value);
                });
                $(input[0]).attr("placeholder", propertiesGot[property].placeholder ? propertiesGot[property].placeholder : "");
                $(input[1]).on("click", function () {
                    properties.onClick(property, this);
                });
                properties.setNode(property, input[0]);
                break;
            case "datepicker":
                input = $("<input type='text' style='" + width + "' id='" + id + "'/>").val(propertiesGot[property].value);
                input.on(propertiesGot[property].on ? propertiesGot[property].on : "keyup", function () {
                    properties.set(property, this.value);
                });
                input.attr("placeholder", propertiesGot[property].placeholder ? propertiesGot[property].placeholder : "");
                properties.setNode(property, input[0]);
                break;
            case "textselect":
                input = $("<input type='text' style='" + width.replace("100%", "50%") + "' id='" + id + "'/><select style='" + width.replace("100%", "50%") + "'></select>");
                $(input[0]).val(propertiesGot[property].value.text);
                $(input[0]).on("keyup", function () {
                    properties.set(property, {text: this.value, select: $(input[1]).val()});
                });
                $(input[0]).attr("placeholder", propertiesGot[property].placeholder ? propertiesGot[property].placeholder : "");
                for (i = 0; i < propertiesGot[property].items.length; i++)
                    $(input[1]).append("<option value='" + propertiesGot[property].items[i].value + "'>" + propertiesGot[property].items[i].label + "</option>");
                $(input[1]).val(propertiesGot[property].value.select);
                $(input[1]).on("change", function () {
                    properties.set(property, {text: $(input[0]).val(), select: this.value});
                });
                properties.setNode(property, {text: $(input[0]).val(), select: $(input[1]).val()});
                break;
        }
        if (propertiesGot[property].type !== "hidden") {
            var cellName = $("<td class='fd-table-td'><label for=" + id + ">" + propertiesGot[property].label + "</label></td>");
            var cellValue = $("<td class='fd-table-td' style='position:relative;'></td>").append(input);
            var row = $("<tr id='" + property + "'></tr>");
            row.append(cellName);
            row.append(cellValue);
            this.tbody.append(row);

            if (propertiesGot[property].required === true) {
                cellName.append("<span style='color:red;float:right;'>*</span>");
            }
            var n = 0;
            if (propertiesGot[property].helpButton) {
                var button = $("<img src='" + $.imgUrl + "fd-help.png' style='cursor:pointer;position:absolute;top:0;right:" + n + ";'>");
                button[0].title = propertiesGot[property].helpButton.translate();
                button.tooltip({
                    tooltipClass: propertiesGot[property].helpButtonCss ? propertiesGot[property].helpButtonCss : null,
                    position: {my: "left+15 center", at: "right center"},
                    content: function () {
                        return $(this).prop('title');
                    },
                    close: function (event, ui) {
                        ui.tooltip.hover(function () {
                            $(this).stop(true).fadeTo(400, 1);
                        }, function () {
                            $(this).fadeOut("400", function () {
                                $(this).remove();
                            });
                        });
                    }
                });
                cellValue.append(button);
                n = n + 16;
                cellValue[0].style.paddingRight = n + "px";
            }
            if (propertiesGot[property].clearButton) {
                var button = $("<img src='" + $.imgUrl + "fd-remove.png' style='cursor:pointer;position:absolute;top:0;right:" + n + "px;' title='" + propertiesGot[property].clearButton + "'>");
                button.on("click", function (e) {
                    e.stopPropagation();
                    if ($(e.target).data("disabled") === true)
                        return;
                    properties.onClickClearButton(property);
                });
                cellValue.append(button);
                n = n + 16;
                cellValue[0].style.paddingRight = n + "px";
            }
            if (propertiesGot[property].type === "datepicker") {
                var button = $("<img src='" + $.imgUrl + "fd-calendar.png' style='cursor:pointer;position:absolute;top:0;right:" + n + "px;' title='" + "datepicker".translate() + "'>");
                button.on("click", function (e) {
                    e.stopPropagation();
                    if ($(e.target).data("disabled") === true)
                        return;
                    if ($(e.target).data("disabledTodayOption") === true)
                        return;
                    var dp = cellValue.find("input[type='text']").datepicker({
                        showOtherMonths: true,
                        selectOtherMonths: true,
                        dateFormat: "yy-mm-dd",
                        showOn: "button",
                        changeMonth: true,
                        changeYear: true,
                        yearRange: "-100:+100",
                        onSelect: function (dateText, inst) {
                            properties.set(property, dateText, cellValue.find("input[type='text']")[0]);
                        },
                        onClose: function (dateText, inst) {
                            dp.datepicker("destroy");
                            $("#ui-datepicker-div").remove();
                        }
                    });
                    dp.datepicker("show");
                    cellValue.find(".ui-datepicker-trigger").hide();
                });
                cellValue.append(button);
                n = n + 16;
                cellValue[0].style.paddingRight = n + "px";
            }
            if (propertiesGot[property].type === "datepicker" && propertiesGot[property].todayOption) {
                cellValue.append("<span style='text-decoration:underline;'><input type='checkbox'/>" + propertiesGot[property].todayOption + "</span>");
                var sw = propertiesGot[property].value === "today";
                var checkboxToday = cellValue.find("input[type='checkbox']");
                checkboxToday.prop("checked", sw);
                checkboxToday.on("change", function () {
                    properties.set(property, this.checked ? "today" : "", cellValue.find("input[type='text']")[0]);
                });
            }
        }
        var disabled = propertiesGot[property].disabled === true;
        var scope = this.tbody.find("#" + property);
        scope.find("input").prop("disabled", disabled);
        scope.find("textarea").prop("disabled", disabled);
        scope.find("select").prop("disabled", disabled);
        scope.find("button").prop("disabled", disabled);
        scope.find("span,img").each(function (i, e) {
            $(e).data("disabled", disabled);
        });
        if (!propertiesGot[property].disabled && property === "requiredFieldErrorMessage"){
            scope.find("textarea").prop("disabled", !propertiesGot["required"].value);
        }
        if (!propertiesGot[property].disabled && propertiesGot.type.value === 'multipleFile' &&
            (property === 'extensions' || property === 'size' || property === 'sizeUnity')) {
            propertiesGot[property].node.disabled = propertiesGot['inputDocument'].value.indexOf("...");
        }
        if (property === 'enableVersioning') {
            propertiesGot[property].node.textContent = (propertiesGot[property].node.textContent === "true") ? 'Yes' : 'No'
        }
        if (cellValue && propertiesGot[property].disabledTodayOption !== undefined) {
            cellValue.find("input[type='text']").prop("disabled", propertiesGot[property].disabledTodayOption);
            scope.find("span,img").each(function (i, e) {
                $(e).data("disabledTodayOption", propertiesGot[property].disabledTodayOption);
            });
        }
    };
    ListProperties.prototype.load = function (properties) {
        var that = this, property;
        var propertiesGot = properties.get();
        for (property in propertiesGot) {
            that.addItem(propertiesGot, property, properties);
        }
    };
    ListProperties.prototype.clear = function () {
        this.tbody.find("tr").remove();
    };
    FormDesigner.extendNamespace('FormDesigner.main.ListProperties', ListProperties);
}());