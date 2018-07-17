var CriteriaField = function (options) {
    this.renderType = (options && options.renderType) || "text";
    PMUI.field.TextField.call(this, options);
    this.process = null;
    this.workspace = null;
    this.buttonHTML = null;
    this.rows = options.rows;
    CriteriaField.prototype.init.call(this, options);
};

CriteriaField.prototype = new PMUI.field.TextField();

CriteriaField.prototype.setProcess = function (process) {
    this.process = process;
    return this;
};

CriteriaField.prototype.setWorkspace = function (workspace) {
    this.workspace = workspace;
    return this;
};

CriteriaField.prototype.init = function (options) {
    var defaults = {
        process: PMDesigner.project.projectId,
        workspace: WORKSPACE
    };
    jQuery.extend(true, defaults, options);
    this.setProcess(defaults.process)
        .setWorkspace(defaults.workspace);
};

CriteriaField.prototype.createVariablePicker = function () {
    var vp = new VariablePicker({
        relatedField: this,
        processId: this.process
    });
    return vp;
};

CriteriaField.prototype.setControls = function () {
    if (this.controls.length) {
        return this;
    }
    if (this.renderType === 'text') {
        this.controls.push(new PMUI.control.TextControl());
    } else {
        this.controls.push(new PMUI.control.TextAreaControl({style: {cssProperties: {resize: 'vertical'}}}));
    }
    return this;
};

CriteriaField.prototype.createCallBack = function () {
    var that = this,
        newValue,
        init = 0,
        index = 0;
    return {
        success: function (variable) {
            var prevText,
                lastText,
                htmlControl = that.controls[index].html;
            init = htmlControl.selectionStart;
            prevText = htmlControl.value.substr(index, init);
            lastText = htmlControl.value.substr(htmlControl.selectionEnd, htmlControl.value.length);
            newValue = prevText + variable + lastText;
            that.setValue(newValue);
            that.isValid();
            htmlControl.selectionEnd = init + variable.length;
        }
    };
};

CriteriaField.prototype.createHTML = function () {
    var button, that = this, variablePicker;
    PMUI.field.TextField.prototype.createHTML.call(this);
    button = new PMUI.ui.Button({
        id: 'buttonCriteriaField',
        text: '@@',
        handler: function () {
            if (that.process != "") {
                variablePicker = that.createVariablePicker();
                variablePicker.open(that.createCallBack());
            } else {
                return;
            }
        },
        style: {
            cssProperties: {
                background: '#1E91D1',
                fontSize: 18,
                padding: '5px',
                borderRadius: '4px',
                verticalAlign: 'top'
            }
        }
    });

    this.buttonHTML = button;
    $(this.helper.html).before(button.getHTML());
    this.buttonHTML.style.addProperties({"margin-left": "10px"});
    this.buttonHTML.html.tabIndex = -1;

    if (this.rows != null)
        this.controls[0].setHeight(this.rows);
    button.defineEvents();

    return this.html;
};

// Overwrite original init function for FormItemFactory
PMUI.form.FormItemFactory.prototype.init = function () {
    var defaults = {
        products: {
            "criteria": CriteriaField,
            "field": PMUI.form.Field,
            "panel": PMUI.form.FormPanel,
            "text": PMUI.field.TextField,
            "password": PMUI.field.PasswordField,
            "dropdown": PMUI.field.DropDownListField,
            "radio": PMUI.field.RadioButtonGroupField,
            "checkbox": PMUI.field.CheckBoxGroupField,
            "textarea": PMUI.field.TextAreaField,
            "datetime": PMUI.field.DateTimeField,
            "optionsSelector": PMUI.field.OptionsSelectorField,
            "buttonField": PMUI.field.ButtonField,
            "annotation": PMUI.field.TextAnnotationField
        },
        defaultProduct: "panel"
    };
    this.setProducts(defaults.products)
        .setDefaultProduct(defaults.defaultProduct);
};
