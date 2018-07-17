/**
 * Crown Class
 * @param options
 * @constructor
 */
var Corona = function (options) {
    /**
     * Call Shape Contructor
     */
    PMUI.draw.Shape.call(this, options);
    /**
     * Define parent crown
     * @type {shape}
     */
    this.parent = null;
    /**
     * Define Type Especific of the parent
     * @type {null}
     */
    this.parentType = null;
    /**
     * Define number rows
     * @type {number}
     */
    this.rows = 1;
    /**
     * Define number cols
     * @type {number}
     */
    this.cols = 1;
    /**
     * Define Items of the Crown
     * @type {PMUI.util.ArrayList}
     */
    this.itemsCrown = new PMUI.util.ArrayList();
    this.init(options);
};
/**
 * Define New Object Shape
 * @type {PMUI.draw.Shape}
 */
Corona.prototype = new PMUI.draw.Shape();
/**
 * Defines the object type
 * @type {String}
 */
Corona.prototype.type = 'Crown';
/**
 * Inicializate Crown
 * @param options
 * @returns {Crown}
 */
Corona.prototype.init = function (options) {
    var config;
    if (typeof options === "object" && !jQuery.isEmptyObject(options)) {
        this.setParent(options.parent);
        this.setParentType(options.parentType);
        config = this.getConfigItems(options.parentType);
        if (config) {
            this.populateItemsCrown(config);
            this.setRows(config.rows);
            this.setCols(config.cols);
        }
    }
    return this;
};
/**
 * Get Parent Crown
 * @returns {shape|PMUI.draw.Shape|*}
 */
Corona.prototype.getParent = function () {
    return this.parent;
};
/**
 * Get Parent Type
 * @returns {null|string|*}
 */
Corona.prototype.getParentType = function () {
    return this.parentType;
};
/**
 * Get Rows
 * @returns {number}
 */
Corona.prototype.getRows = function () {
    return this.rows;
};
/**
 * Get Cols
 * @returns {number}
 */
Corona.prototype.getCols = function () {
    return this.cols;
};
/**
 * Get Items Crown
 * @returns {PMUI.util.ArrayList|*}
 */
Corona.prototype.getItemsCrown = function () {
    return this.itemsCrown;
};
/**
 * Set Parent Crown
 * @param parent
 * @returns {Corona}
 */
Corona.prototype.setParent = function (parent) {
    if (typeof parent === "object" && !jQuery.isEmptyObject(parent)) {
        this.parent = parent;
    }
    return this;
};
/**
 * Set Parent Type
 * @param parentType
 * @returns {Corona}
 */
Corona.prototype.setParentType = function (parentType) {
    if (parentType && typeof parentType === "string") {
        this.parentType = parentType;
    }
    return this;
};
/**
 * Set Number Rows
 * @param rows
 * @returns {Crown}
 */
Corona.prototype.setRows = function (rows) {
    if (rows && rows > 0) {
        this.rows = rows;
    }
    return this;
};
/**
 * Set Number Cols
 * @param cols
 * @returns {Crown}
 */
Corona.prototype.setCols = function (cols) {
    if (cols && cols > 0) {
        this.cols = cols;
    }
    return this;
};
/**
 * Redimention width and height of the Crown
 * @returns {Corona}
 */
Corona.prototype.adjustSize = function () {
    var width,
        height,
        itemFirst,
        margin = 4;
    itemFirst = this.getItemsCrown().get(0);
    width = (itemFirst.getWidth() + margin) * this.getCols();
    height = (itemFirst.getHeight() + margin) * this.getRows();
    if (this.html) {
        this.html.style.width = width + "px";
        this.html.style.height = height + 'px';
    }
    return this;
};
/**
 * Get config Items Crown
 * @param especificType
 * @returns {*}
 */
Corona.prototype.getConfigItems = function (especificType) {
    var configDefault = PMDesigner.configCrown,
        typeShape = this.getParent().getType(),
        configCrown = configDefault[typeShape][especificType];
    return configCrown;
};
/**
 * Populate Crown from previous configuration
 * @param config
 * @returns {Corona}
 */
Corona.prototype.populateItemsCrown = function (config) {
    var order = (config && config.order) || [],
        itemsDefault = PMDesigner.modelCrown.getItemsDefault(),
        itemCrownDefault,
        itemCrown,
        max,
        i;
    if (order && Array.isArray(order) && order.length > 0) {
        max = order.length;
        for (i = 0; i < max; i += 1) {
            itemCrownDefault = itemsDefault.find("id", order[i]);
            itemCrownDefault.parent = this;
            itemCrownDefault.canvas = this.canvas;
            itemCrown = new ItemCrown(itemCrownDefault);
            this.itemsCrown.insert(itemCrown);
        }
    }
    return this;
};
Corona.prototype.isCreatedItems = function () {
    var isCreated = false;
    if (this.countChildrens() === this.getItemsCrown().getSize()) {
        isCreated = true;
    }
    return isCreated
};
/**
 * Paint Crown
 * @returns {Corona}
 */
Corona.prototype.paint = function () {
    if (!this.html) {
        this.createHTML();
    }
    return this;
};
/**
 * Create HTML
 * @returns {Corona}
 */
Corona.prototype.createHTML = function () {
    var htmlParent = this.getParent().html,
        htmlCrown,
        htmlRow,
        itemCrown,
        itemsAux = this.getItemsCrown().asArray(),
        cont = 0,
        i;
    this.html = null;
    htmlCrown = this.createHtmlCrown();
    while (itemsAux.length > 0) {
        if (this.getRows() > cont) {
            htmlRow = this.createHtmlRow(cont);
            for (i = 0; i < this.getCols(); i += 1) {
                if (itemsAux.length > 0) {
                    itemCrown = itemsAux.shift();
                    itemCrown = itemCrown.createHtmlItem();
                    itemCrown = itemCrown.attachListeners();
                    htmlRow.appendChild(itemCrown.html);
                } else {
                    break;
                }
            }
            htmlCrown.appendChild(htmlRow);
            cont += 1;
        }
    }
    htmlParent.appendChild(htmlCrown);
    this.html = htmlCrown;
    this.adjustSize();
    return this;
};
/**
 * Create Html Crown
 * @returns {*}
 */
Corona.prototype.createHtmlCrown = function () {
    var htmlCrown = null,
        classCrown = "crown-container";
    PMUI.draw.Shape.prototype.createHTML.call(this);
    htmlCrown = this.html;
    if (htmlCrown) {
        htmlCrown.className = classCrown;
    }
    return htmlCrown;
};
/**
 * Create Html Row
 * @param index
 * @returns {HTMLElement|*}
 */
Corona.prototype.createHtmlRow = function (index) {
    var htmlRow = null,
        classRow = "row";
    htmlRow = PMUI.createHTMLElement("div");
    htmlRow.className = classRow + " " + classRow  + "-" + index;
    return htmlRow;
};
/**
 * Show Crown
 * @returns {Corona}
 */
Corona.prototype.show = function () {
    if (this.isDirtyParentType()) {
        this.updateCrown();
    }
    if (!this.html) {
        this.createHTML();
    }
    jQuery(this.html).position({
        of: jQuery(this.parent.html),
        my: "left top",
        at: "right top",
        collision: 'none'
    });
    this.html.style.visibility = 'visible';
    return this;
};
/**
 * Hide Crown
 * @returns {Corona}
 */
Corona.prototype.hide = function () {
    if (this && this.html) {
        this.html.style.visibility = 'hidden';
    }
    return this;
};
/**
 * Destroy Crown Content
 * @returns {Corona}
 */
Corona.prototype.destroy = function () {
    if (this && this.html) {
        jQuery(this.html).empty();
        jQuery(this.html).remove();
        this.html = null;
    }
    this.getItemsCrown().clear();
    return this;
};
/**
 * Update Crown when the config changes
 * @returns {Corona}
 */
Corona.prototype.updateCrown = function () {
    var config,
        especificType = this.getParent().getEspecificType();
    this.destroy();
    this.setParentType(especificType);
    config = this.getConfigItems(especificType);
    this.populateItemsCrown(config);
    this.setRows(config.rows);
    this.setCols(config.cols);
    return this;
};
/**
 * Validate if the shape type change
 * @returns {boolean}
 */
Corona.prototype.isDirtyParentType = function () {
    var isDirty = false;
    if (this.getParent().getType() !== "PMActivity" && this.getParentType() !== this.getParent().getEspecificType()) {
        isDirty = true;
    }
    return isDirty;
};