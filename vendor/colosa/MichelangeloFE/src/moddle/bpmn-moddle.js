'use strict';

function Base() { }

Base.prototype.get = function(name) {
    return this.$model.properties.get(this, name);
};

Base.prototype.set = function(name, value) {
    this.$model.properties.set(this, name, value);
};

//
//module.exports = Base;
var parseName = function(name, defaultPrefix) {
    var parts = name.split(/:/),
        localName, prefix;

    // no prefix (i.e. only local name)
    if (parts.length === 1) {
        localName = name;
        prefix = defaultPrefix;
    } else
    // prefix + local name
    if (parts.length === 2) {
        localName = parts[1];
        prefix = parts[0];
    } else {
        throw new Error('expected <prefix:localName> or <localName>, got ' + name);
    }

    name = (prefix ? prefix + ':' : '') + localName;

    return {
        name: name,
        prefix: prefix,
        localName: localName
    };
};




'use strict';


/**
 * A utility that gets and sets properties of model elements.
 *
 * @param {Model} model
 */
function Properties(model) {
    this.model = model;
}

//module.exports = Properties;


/**
 * Sets a named property on the target element
 *
 * @param {Object} target
 * @param {String} name
 * @param {Object} value
 */
Properties.prototype.set = function(target, name, value) {

    var property = this.model.getPropertyDescriptor(target, name);

    if (!property) {
        target.$attrs[name] = value;
    } else {
        Object.defineProperty(target, property.name, {
            enumerable: !property.isReference,
            writable: true,
            value: value
        });
    }
};

/**
 * Returns the named property of the given element
 *
 * @param  {Object} target
 * @param  {String} name
 *
 * @return {Object}
 */
Properties.prototype.get = function(target, name) {

    var property = this.model.getPropertyDescriptor(target, name);

    if (!property) {
        return target.$attrs[name];
    }

    var propertyName = property.name;

    // check if access to collection property and lazily initialize it
    if (!target[propertyName] && property.isMany) {
        Object.defineProperty(target, propertyName, {
            enumerable: !property.isReference,
            writable: true,
            value: []
        });
    }

    return target[propertyName];
};


/**
 * Define a property on the target element
 *
 * @param  {Object} target
 * @param  {String} name
 * @param  {Object} options
 */
Properties.prototype.define = function(target, name, options) {
    Object.defineProperty(target, name, options);
};


/**
 * Define the descriptor for an element
 */
Properties.prototype.defineDescriptor = function(target, descriptor) {
    this.define(target, '$descriptor', { value: descriptor });
};

/**
 * Define the model for an element
 */
Properties.prototype.defineModel = function(target, model) {
    this.define(target, '$model', { value: model });
};
'use strict';

//var _ = require('lodash');
//
//var Base = require('./base');


function Factory(model, properties) {
    this.model = model;
    this.properties = properties;
}

//module.exports = Factory;


Factory.prototype.createType = function(descriptor) {

    var model = this.model;

    var props = this.properties,
        prototype = Object.create(Base.prototype);

    // initialize default values
    _.forEach(descriptor.properties, function(p) {
        if (!p.isMany && p.default !== undefined) {
            prototype[p.name] = p.default;
        }
    });

    props.defineModel(prototype, model);
    props.defineDescriptor(prototype, descriptor);

    var name = descriptor.ns.name;

    /**
     * The new type constructor
     */
    function ModdleElement(attrs) {
        props.define(this, '$type', { value: name, enumerable: true });
        props.define(this, '$attrs', { value: {} });
        props.define(this, '$parent', { writable: true });

        _.forEach(attrs, function(val, key) {
            this.set(key, val);
        }, this);
    }

    ModdleElement.prototype = prototype;

    ModdleElement.hasType = prototype.$instanceOf = this.model.hasType;

    // static links
    props.defineModel(ModdleElement, model);
    props.defineDescriptor(ModdleElement, descriptor);

    return ModdleElement;
};

/**
 * Built-in moddle types
 */
var BUILTINS = {
    String: true,
    Boolean: true,
    Integer: true,
    Real: true,
    Element: true
};

/**
 * Converters for built in types from string representations
 */
var TYPE_CONVERTERS = {
    String: function(s) { return s; },
    Boolean: function(s) { return s === 'true'; },
    Integer: function(s) { return parseInt(s, 10); },
    Real: function(s) { return parseFloat(s, 10); }
};

/**
 * Convert a type to its real representation
 */
var coerceType = function(type, value) {

    var converter = TYPE_CONVERTERS[type];

    if (converter) {
        return converter(value);
    } else {
        return value;
    }
};

/**
 * Return whether the given type is built-in
 */
var isBuiltIn = function(type) {
    return !!BUILTINS[type];
};

/**
 * Return whether the given type is simple
 */
var isSimple = function(type) {
    return !!TYPE_CONVERTERS[type];
};
'use strict';

//var _ = require('lodash');
//
//var Types = require('./types');
//    DescriptorBuilder = require('./descriptor-builder');
//
//var this.parseName = parseName;


function Registry(packages, properties, options) {
    this.options = _.extend({ generateId: 'id' }, options || {});

    this.packageMap = {};
    this.typeMap = {};

    this.packages = [];

    this.properties = properties;

    _.forEach(packages, this.registerPackage, this);
}

//module.exports = Registry;


Registry.prototype.getPackage = function(uriOrPrefix) {
    return this.packageMap[uriOrPrefix];
};

Registry.prototype.getPackages = function() {
    return this.packages;
};


Registry.prototype.registerPackage = function(pkg) {

    // register types
    _.forEach(pkg.types, function(descriptor) {
        this.registerType(descriptor, pkg);
    }, this);

    this.packageMap[pkg.uri] = this.packageMap[pkg.prefix] = pkg;
    this.packages.push(pkg);
};


/**
 * Register a type from a specific package with us
 */
Registry.prototype.registerType = function(type, pkg) {

    var ns = this.parseName(type.name, pkg.prefix),
        name = ns.name,
        propertiesByName = {};

    // parse properties
    _.forEach(type.properties, function(p) {

        // namespace property names
        var propertyNs = this.parseName(p.name, ns.prefix),
            propertyName = propertyNs.name;

        // namespace property types
        if (!isBuiltIn( p.type)) {
            p.type = this.parseName(p.type, propertyNs.prefix).name;
        }

        _.extend(p, {
            ns: propertyNs,
            name: propertyName
        });

        propertiesByName[propertyName] = p;
    },this);

    // update ns + name
    _.extend(type, {
        ns: ns,
        name: name,
        propertiesByName: propertiesByName
    });

    // link to package
    this.definePackage(type, pkg);

    // register
    this.typeMap[name] = type;
};


/**
 * Traverse the type hierarchy from bottom to top.
 */
Registry.prototype.mapTypes = function(nsName, iterator) {

    var type = this.typeMap[nsName.name];

    if (!type) {
        throw new Error('unknown type <' + nsName.name + '>');
    }

    _.forEach(type.superClass, function(cls) {
        var parentNs = this.parseName(cls, nsName.prefix);
        this.mapTypes(parentNs, iterator);
    }, this);

    iterator(type);
};


/**
 * Returns the effective descriptor for a type.
 *
 * @param  {String} type the namespaced name (ns:localName) of the type
 *
 * @return {Descriptor} the resulting effective descriptor
 */
Registry.prototype.getEffectiveDescriptor = function(name) {

    var options = this.options,
        nsName = this.parseName(name);

    var builder = new DescriptorBuilder(nsName);

    this.mapTypes(nsName, function(type) {
        builder.addTrait(type);
    });

    // check we have an id assigned
    var id = this.options.generateId;
    if (id && !builder.hasProperty(id)) {
        builder.addIdProperty(id);
    }

    var descriptor = builder.build();

    // define package link
    this.definePackage(descriptor, descriptor.allTypes[descriptor.allTypes.length - 1].$pkg);

    return descriptor;
};


Registry.prototype.definePackage = function(target, pkg) {
    this.properties.define(target, '$pkg', { value: pkg });
};

Registry.prototype.parseName = function(name, defaultPrefix) {
    var parts = name.split(/:/),
        localName, prefix;

    // no prefix (i.e. only local name)
    if (parts.length === 1) {
        localName = name;
        prefix = defaultPrefix;
    } else
    // prefix + local name
    if (parts.length === 2) {
        localName = parts[1];
        prefix = parts[0];
    } else {
        throw new Error('expected <prefix:localName> or <localName>, got ' + name);
    }

    name = (prefix ? prefix + ':' : '') + localName;

    return {
        name: name,
        prefix: prefix,
        localName: localName
    };
};





'use strict';

//var _ = require('lodash');
//
//var parseNameNs = require('./ns').parseName;


function DescriptorBuilder(nameNs) {
    this.ns = nameNs;
    this.name = nameNs.name;
    this.allTypes = [];
    this.properties = [];
    this.propertiesByName = {};
}

//module.exports = DescriptorBuilder;


DescriptorBuilder.prototype.build = function() {
    return _.pick(this, [ 'ns', 'name', 'allTypes', 'properties', 'propertiesByName', 'bodyProperty' ]);
};

DescriptorBuilder.prototype.addProperty = function(p, idx) {
    this.addNamedProperty(p, true);

    var properties = this.properties;

    if (idx !== undefined) {
        properties.splice(idx, 0, p);
    } else {
        properties.push(p);
    }
};


DescriptorBuilder.prototype.replaceProperty = function(oldProperty, newProperty) {
    var oldNameNs = oldProperty.ns;

    var props = this.properties,
        propertiesByName = this.propertiesByName;

    if (oldProperty.isBody) {

        if (!newProperty.isBody) {
            throw new Error(
                'property <' + newProperty.ns.name + '> must be body property ' +
                'to refine <' + oldProperty.ns.name + '>');
        }

        // TODO: Check compatibility
        this.setBodyProperty(newProperty, false);
    }

    this.addNamedProperty(newProperty, true);

    // replace old property at index with new one
    var idx = props.indexOf(oldProperty);
    if (idx === -1) {
        throw new Error('property <' + oldNameNs.name + '> not found in property list');
    }

    props[idx] = newProperty;

    // replace propertiesByName entry with new property
    propertiesByName[oldNameNs.name] = propertiesByName[oldNameNs.localName] = newProperty;
};


DescriptorBuilder.prototype.redefineProperty = function(p) {

    var nsPrefix = p.ns.prefix;
    var parts = p.redefines.split('#');

    var name = parseNameNs(parts[0], nsPrefix);
    var attrName = parseNameNs(parts[1], name.prefix).name;

    var redefinedProperty = this.propertiesByName[attrName];
    if (!redefinedProperty) {
        throw new Error('refined property <' + attrName + '> not found');
    } else {
        this.replaceProperty(redefinedProperty, p);
    }

    delete p.redefines;
};

DescriptorBuilder.prototype.addNamedProperty = function(p, validate) {
    var ns = p.ns,
        propsByName = this.propertiesByName;

    if (validate) {
        this.assertNotDefined(p, ns.name);
        this.assertNotDefined(p, ns.localName);
    }

    propsByName[ns.name] = propsByName[ns.localName] = p;
};

DescriptorBuilder.prototype.removeNamedProperty = function(p) {
    var ns = p.ns,
        propsByName = this.propertiesByName;

    delete propsByName[ns.name];
    delete propsByName[ns.localName];
};

DescriptorBuilder.prototype.setBodyProperty = function(p, validate) {

    if (validate && this.bodyProperty) {
        throw new Error(
            'body property defined multiple times ' +
            '(<' + this.bodyProperty.ns.name + '>, <' + p.ns.name + '>)');
    }

    this.bodyProperty = p;
};

DescriptorBuilder.prototype.addIdProperty = function(name) {
    var nameNs = parseNameNs(name, this.ns.prefix);

    var p = {
        name: nameNs.localName,
        type: 'String',
        isAttr: true,
        ns: nameNs
    };

    // ensure that id is always the first attribute (if present)
    this.addProperty(p, 0);
};

DescriptorBuilder.prototype.assertNotDefined = function(p, name) {
    var propertyName = p.name,
        definedProperty = this.propertiesByName[propertyName];

    if (definedProperty) {
        throw new Error(
            'property <' + propertyName + '> already defined; ' +
            'override of <' + definedProperty.definedBy.ns.name + '#' + definedProperty.ns.name + '> by ' +
            '<' + p.definedBy.ns.name + '#' + p.ns.name + '> not allowed without redefines');
    }
};

DescriptorBuilder.prototype.hasProperty = function(name) {
    return this.propertiesByName[name];
};

DescriptorBuilder.prototype.addTrait = function(t) {

    var allTypes = this.allTypes;

    if (allTypes.indexOf(t) !== -1) {
        return;
    }

    _.forEach(t.properties, function(p) {

        // clone property to allow extensions
        p = _.extend({}, p, {
            name: p.ns.localName
        });

        Object.defineProperty(p, 'definedBy', {
            value: t
        });

        // add redefine support
        if (p.redefines) {
            this.redefineProperty(p);
        } else {
            if (p.isBody) {
                this.setBodyProperty(p);
            }
            this.addProperty(p);
        }
    }, this);

    allTypes.push(t);
};
'use strict';

//var _ = require('lodash');
//
//var Types = require('./types'),
//    Factory = require('./factory'),
//    Registry = require('./registry'),
//    Properties = require('./properties');
//
//var parseNameNs = require('./ns').parseName;


//// Moddle implementation /////////////////////////////////////////////////

/**
 * @class Moddle
 *
 * A model that can be used to create elements of a specific type.
 *
 * @example
 *
 * var Moddle = require('moddle');
 *
 * var pkg = {
 *   name: 'mypackage',
 *   prefix: 'my',
 *   types: [
 *     { name: 'Root' }
 *   ]
 * };
 *
 * var moddle = new Moddle([pkg]);
 *
 * @param {Array<Package>} packages  the packages to contain
 * @param {Object} options  additional options to pass to the model
 */
function Moddle(packages, options) {

    options = options || {};

    this.properties = new Properties(this);

    this.factory = new Factory(this, this.properties);
    this.registry = new Registry(packages, this.properties, options);

    this.typeCache = {};
}

//module.exports = Moddle;


/**
 * Create an instance of the specified type.
 *
 * @method Moddle#create
 *
 * @example
 *
 * var foo = moddle.create('my:Foo');
 * var bar = moddle.create('my:Bar', { id: 'BAR_1' });
 *
 * @param  {String|Object} descriptor the type descriptor or name know to the model
 * @param  {Object} attrs   a number of attributes to initialize the model instance with
 * @return {Object}         model instance
 */
Moddle.prototype.create = function(descriptor, attrs) {
    var Type = this.getType(descriptor);

    if (!Type) {
        throw new Error('unknown type <' + descriptor + '>');
    }

    return new Type(attrs);
};


/**
 * Returns the type representing a given descriptor
 *
 * @method Moddle#getType
 *
 * @example
 *
 * var Foo = moddle.getType('my:Foo');
 * var foo = new Foo({ 'id' : 'FOO_1' });
 *
 * @param  {String|Object} descriptor the type descriptor or name know to the model
 * @return {Object}         the type representing the descriptor
 */
Moddle.prototype.getType = function(descriptor) {

    var cache = this.typeCache;

    var name = _.isString(descriptor) ? descriptor : descriptor.ns.name;

    var type = cache[name];

    if (!type) {
        descriptor = this.registry.getEffectiveDescriptor(name);
        type = cache[descriptor.name] = this.factory.createType(descriptor);
    }

    return type;
};


/**
 * Creates an any-element type to be used within model instances.
 *
 * This can be used to create custom elements that lie outside the meta-model.
 * The created element contains all the meta-data required to serialize it
 * as part of meta-model elements.
 *
 * @method Moddle#createAny
 *
 * @example
 *
 * var foo = moddle.createAny('vendor:Foo', 'http://vendor', {
 *   value: 'bar'
 * });
 *
 * var container = moddle.create('my:Container', 'http://my', {
 *   any: [ foo ]
 * });
 *
 * // go ahead and serialize the stuff
 *
 *
 * @param  {String} name  the name of the element
 * @param  {String} nsUri the namespace uri of the element
 * @param  {Object} [properties] a map of properties to initialize the instance with
 * @return {Object} the any type instance
 */
Moddle.prototype.createAny = function(name, nsUri, properties) {

    var nameNs = parseNameNs(name);

    var element = {
        $type: name
    };

    var descriptor = {
        name: name,
        isGeneric: true,
        ns: {
            prefix: nameNs.prefix,
            localName: nameNs.localName,
            uri: nsUri
        }
    };

    this.properties.defineDescriptor(element, descriptor);
    this.properties.defineModel(element, this);
    this.properties.define(element, '$parent', { enumerable: false, writable: true });

    _.forEach(properties, function(a, key) {
        if (_.isObject(a) && a.value !== undefined) {
            element[a.name] = a.value;
        } else {
            element[key] = a;
        }
    });

    return element;
};

/**
 * Returns a registered package by uri or prefix
 *
 * @return {Object} the package
 */
Moddle.prototype.getPackage = function(uriOrPrefix) {
    return this.registry.getPackage(uriOrPrefix);
};

/**
 * Returns a snapshot of all known packages
 *
 * @return {Object} the package
 */
Moddle.prototype.getPackages = function() {
    return this.registry.getPackages();
};

/**
 * Returns the descriptor for an element
 */
Moddle.prototype.getElementDescriptor = function(element) {
    return element.$descriptor;
};

/**
 * Returns true if the given descriptor or instance
 * represents the given type.
 *
 * May be applied to this, if element is omitted.
 */
Moddle.prototype.hasType = function(element, type) {
    if (type === undefined) {
        type = element;
        element = this;
    }

    var descriptor = element.$model.getElementDescriptor(element);

    return !!_.find(descriptor.allTypes, function(t) {
        return t.name === type;
    });
};


/**
 * Returns the descriptor of an elements named property
 */
Moddle.prototype.getPropertyDescriptor = function(element, property) {
    return this.getElementDescriptor(element).propertiesByName[property];
};
'use strict';


function capitalize(string) {
    return string.charAt(0).toUpperCase() + string.slice(1);
}

function lower(string) {
    return string.charAt(0).toLowerCase() + string.slice(1);
}

function hasLowerCaseAlias(pkg) {
    return pkg.xml && pkg.xml.alias === 'lowerCase';
}


var aliasToName = function(alias, pkg) {
    if (hasLowerCaseAlias(pkg)) {
        return capitalize(alias);
    } else {
        return alias;
    }
};

var nameToAlias = function(name, pkg) {
    if (hasLowerCaseAlias(pkg)) {
        return lower(name);
    } else {
        return name;
    }
};

var DEFAULT_NS_MAP = {
    'xsi': 'http://www.w3.org/2001/XMLSchema-instance'
};

//
//module.exports.DEFAULT_NS_MAP = {
//    'xsi': 'http://www.w3.org/2001/XMLSchema-instance'
//};
'use strict';

//var sax = require('sax'),
//    _ = require('lodash');
//


//var common = require('./common'),
//    Types = require('moddle').types,
//    Stack = require('tiny-stack'),
//    parseNameNs = require('moddle').ns.parseName,
var parseNameNs = parseName;
var aliasToName = aliasToName;


function parseNodeAttributes(node) {
    var nodeAttrs = node.attributes;

    return _.reduce(nodeAttrs, function(result, v, k) {
        var name, ns;

        if (!v.local) {
            name = v.prefix;
        } else {
            ns = parseNameNs(v.name, v.prefix);
            name = ns.name;
        }

        result[name] = v.value;
        return result;
    }, {});
}

/**
 * Normalizes namespaces for a node given an optional default namespace and a
 * number of mappings from uris to default prefixes.
 *
 * @param  {XmlNode} node
 * @param  {Model} model the model containing all registered namespaces
 * @param  {Uri} defaultNsUri
 */
function normalizeNamespaces(node, model, defaultNsUri) {
    var uri, childUri, prefix;

    uri = node.uri || defaultNsUri;

    if (uri) {
        var pkg = model.getPackage(uri);

        if (pkg) {
            prefix = pkg.prefix;
        } else {
            prefix = node.prefix;
        }

        node.prefix = prefix;
        node.uri = uri;
    }

    _.forEach(node.attributes, function(attr) {
        normalizeNamespaces(attr, model, null);
    });
}

/**
 * A parse context.
 *
 * @class
 *
 * @param {ElementHandler} parseRoot the root handler for parsing a document
 */
function Context(parseRoot) {

    var elementsById = {};
    var references = [];

    var warnings = [];

    this.addReference = function(reference) {
        references.push(reference);
    };

    this.addElement = function(id, element) {

        if (!id || !element) {
            throw new Error('[xml-reader] id or ctx must not be null');
        }

        elementsById[id] = element;
    };

    this.addWarning = function (w) {
        warnings.push(w);
    };

    this.warnings = warnings;
    this.references = references;

    this.elementsById = elementsById;

    this.parseRoot = parseRoot;
}


function BaseHandler() {}

BaseHandler.prototype.handleEnd = function() {};
BaseHandler.prototype.handleText = function() {};
BaseHandler.prototype.handleNode = function() {};

function BodyHandler() {}

BodyHandler.prototype = new BaseHandler();

BodyHandler.prototype.handleText = function(text) {
    this.body = (this.body || '') + text;
};

function ReferenceHandler(property, context) {
    this.property = property;
    this.context = context;
}

ReferenceHandler.prototype = new BodyHandler();

ReferenceHandler.prototype.handleNode = function(node) {

    if (this.element) {
        throw new Error('expected no sub nodes');
    } else {
        this.element = this.createReference(node);
    }

    return this;
};

ReferenceHandler.prototype.handleEnd = function() {
    this.element.id = this.body;
};

ReferenceHandler.prototype.createReference = function() {
    return {
        property: this.property.ns.name,
        id: ''
    };
};

function ValueHandler(propertyDesc, element) {
    this.element = element;
    this.propertyDesc = propertyDesc;
}

ValueHandler.prototype = new BodyHandler();

ValueHandler.prototype.handleEnd = function() {

    var value = this.body,
        element = this.element,
        propertyDesc = this.propertyDesc;

    value = coerceType(propertyDesc.type, value);

    if (propertyDesc.isMany) {
        element.get(propertyDesc.name).push(value);
    } else {
        element.set(propertyDesc.name, value);
    }
};


function BaseElementHandler() {}

BaseElementHandler.prototype = Object.create(BodyHandler.prototype);

BaseElementHandler.prototype.handleNode = function(node) {
    var parser = this;

    if (!this.element) {
        this.element = this.createElement(node);
        var id = this.element.id;

        if (id) {
            this.context.addElement(id, this.element);
        }
    } else {
        parser = this.handleChild(node);
    }

    return parser;
};

/**
 * @class XMLReader.ElementHandler
 *
 */
function ElementHandler(model, type, context) {
    this.model = model;
    this.type = model.getType(type);
    this.context = context;
}

ElementHandler.prototype = new BaseElementHandler();

ElementHandler.prototype.addReference = function(reference) {
    this.context.addReference(reference);
};

ElementHandler.prototype.handleEnd = function() {

    var value = this.body,
        element = this.element,
        descriptor = element.$descriptor,
        bodyProperty = descriptor.bodyProperty;

    if (bodyProperty && value !== undefined) {
        value = coerceType(bodyProperty.type, value);
        element.set(bodyProperty.name, value);
    }
};

/**
 * Create an instance of the model from the given node.
 *
 * @param  {Element} node the xml node
 */
ElementHandler.prototype.createElement = function(node) {
    var attributes = parseNodeAttributes(node),
        Type = this.type,
        descriptor = Type.$descriptor,
        context = this.context,
        instance = new Type({});

    _.forEach(attributes, function(value, name) {

        var prop = descriptor.propertiesByName[name];

        if (prop && prop.isReference) {
            context.addReference({
                element: instance,
                property: prop.ns.name,
                id: value
            });
        } else {
            if (prop) {
                value = coerceType(prop.type, value);
            }

            instance.set(name, value);
        }
    });

    return instance;
};

ElementHandler.prototype.getPropertyForElement = function(nameNs) {
    if (_.isString(nameNs)) {
        nameNs = parseNameNs(nameNs);
    }

    var type = this.type,
        model = this.model,
        descriptor = type.$descriptor;

    var propertyName = nameNs.name;

    var property = descriptor.propertiesByName[propertyName];

    // search for properties by name first
    if (property) {
        return property;
    }

    var pkg = model.getPackage(nameNs.prefix);

    if (pkg) {
        var typeName = nameNs.prefix + ':' + aliasToName(nameNs.localName, descriptor.$pkg),
            elementType = model.getType(typeName);

        // search for collection members later
        property = _.find(descriptor.properties, function(p) {
            return !p.isVirtual && !p.isReference && !p.isAttribute && elementType.hasType(p.type);
        });

        if (property) {
            return _.extend({}, property, { effectiveType: elementType.$descriptor.name });
        }
    } else {
        // parse unknown element (maybe extension)
        property = _.find(descriptor.properties, function(p) {
            return !p.isReference && !p.isAttribute && p.type === 'Element';
        });

        if (property) {
            return property;
        }
    }

    throw new Error('unrecognized element <' + nameNs.name + '>');
};

ElementHandler.prototype.toString = function() {
    return 'ElementDescriptor[' + this.type.$descriptor.name + ']';
};

ElementHandler.prototype.valueHandler = function(propertyDesc, element) {
    return new ValueHandler(propertyDesc, element);
};

ElementHandler.prototype.referenceHandler = function(propertyDesc) {
    return new ReferenceHandler(propertyDesc, this.context);
};

ElementHandler.prototype.handler = function(type) {
    if (type === 'Element') {
        return new GenericElementHandler(this.model, type, this.context);
    } else {
        return new ElementHandler(this.model, type, this.context);
    }
};

/**
 * Handle the child element parsing
 *
 * @param  {Element} node the xml node
 */
ElementHandler.prototype.handleChild = function(node) {
    var nameNs = parseNameNs(node.local, node.prefix);

    var propertyDesc, type, element, childHandler;

    propertyDesc = this.getPropertyForElement(nameNs);
    element = this.element;

    type = propertyDesc.effectiveType || propertyDesc.type;

    if (isSimple(propertyDesc.type)) {
        return this.valueHandler(propertyDesc, element);
    }

    if (propertyDesc.isReference) {
        childHandler = this.referenceHandler(propertyDesc).handleNode(node);
    } else {
        childHandler = this.handler(type).handleNode(node);
    }

    var newElement = childHandler.element;

    // child handles may decide to skip elements
    // by not returning anything
    if (newElement !== undefined) {

        if (propertyDesc.isMany) {
            element.get(propertyDesc.name).push(newElement);
        } else {
            element.set(propertyDesc.name, newElement);
        }

        if (propertyDesc.isReference) {
            _.extend(newElement, {
                element: element
            });

            this.context.addReference(newElement);
        } else {
            // establish child -> parent relationship
            newElement.$parent = element;
        }
    }

    return childHandler;
};


function GenericElementHandler(model, type, context) {
    this.model = model;
    this.context = context;
}

GenericElementHandler.prototype = Object.create(BaseElementHandler.prototype);

GenericElementHandler.prototype.createElement = function(node) {

    var name = node.name,
        prefix = node.prefix,
        uri = node.ns[prefix],
        attributes = node.attributes;

    return this.model.createAny(name, uri, attributes);
};

GenericElementHandler.prototype.handleChild = function(node) {

    var handler = new GenericElementHandler(this.model, 'Element', this.context).handleNode(node),
        element = this.element;

    var newElement = handler.element,
        children;

    if (newElement !== undefined) {
        children = element.$children = element.$children || [];
        children.push(newElement);

        // establish child -> parent relationship
        newElement.$parent = element;
    }

    return handler;
};

GenericElementHandler.prototype.handleText = function(text) {
    this.body = this.body || '' + text;
};

GenericElementHandler.prototype.handleEnd = function() {
    if (this.body) {
        this.element.$body = this.body;
    }
};

/**
 * A reader for a meta-model
 *
 * @class XMLReader
 *
 * @param {Model} model used to read xml files
 */
function XMLReader(model) {
    this.model = model;
}


XMLReader.prototype.fromXML = function(xml, rootHandler, done) {

    var context = new Context(rootHandler);

    var parser = sax.parser(true, { xmlns: true, trim: true });
    var stackInstance =  stack();

    var model = this.model,
        self = this;

    rootHandler.context = context;

    // push root handler
    stackInstance.push(rootHandler);


    function resolveReferences() {

        var elementsById = context.elementsById;
        var references = context.references;

        var i, r;

        for (i = 0; !!(r = references[i]); i++) {
            var element = r.element;
            var reference = elementsById[r.id];
            var property = element.$descriptor.propertiesByName[r.property];

            if (!reference) {
                context.addWarning({
                    message: 'unresolved reference <' + r.id + '>',
                    element: r.element,
                    property: r.property,
                    value: r.id
                });
            }

            if (property.isMany) {
                var collection = element.get(property.name),
                    idx = collection.indexOf(r);

                if (!reference) {
                    // remove unresolvable reference
                    collection.splice(idx, 1);
                } else {
                    // update reference
                    collection[idx] = reference;
                }
            } else {
                element.set(property.name, reference);
            }
        }
    }

    function handleClose(tagName) {
        stackInstance.pop().handleEnd();
    }

    function handleOpen(node) {
        var handler = stackInstance.peek();

        normalizeNamespaces(node, model);

        try {
            stackInstance.push(handler.handleNode(node));
        } catch (e) {

            var line = this.line,
                column = this.column;

            throw new Error(
                'unparsable content <' + node.name + '> detected\n\t' +
                'line: ' + line + '\n\t' +
                'column: ' + column + '\n\t' +
                'nested error: ' + e.message);
        }
    }

    function handleText(text) {
        stackInstance.peek().handleText(text);
    }

    parser.onopentag = handleOpen;
    parser.oncdata = parser.ontext = handleText;
    parser.onclosetag = handleClose;
    parser.onend = resolveReferences;

    // deferred parse XML to make loading really ascnchronous
    // this ensures the execution environment (node or browser)
    // is kept responsive and that certain optimization strategies
    // can kick in
    _.defer(function() {
        var error;

        try {
            parser.write(xml).close();
        } catch (e) {
            error = e;
        }

        done(error, error ? undefined : rootHandler.element, context);
    });
};

XMLReader.prototype.handler = function(name) {
    return new ElementHandler(this.model, name);
};
'use strict';

//var _ = require('lodash');

//var Types = require('moddle').types,
//    common = require('./common'),
    var parseNameNs = parseName;
    var nameToAlias = nameToAlias;

var XML_PREAMBLE = '<?xml version="1.0" encoding="UTF-8"?>\n';

var CDATA_ESCAPE = /[<>"&]+/;

var DEFAULT_NS_MAP = DEFAULT_NS_MAP;


function nsName(ns) {
    if (_.isString(ns)) {
        return ns;
    } else {
        return (ns.prefix ? ns.prefix + ':' : '') + ns.localName;
    }
}

function getElementNs(ns, descriptor) {
    if (descriptor.isGeneric) {
        return descriptor.name;
    } else {
        return _.extend({ localName: nameToAlias(descriptor.ns.localName, descriptor.$pkg) }, ns);
    }
}

function getPropertyNs(ns, descriptor) {
    return _.extend({ localName: descriptor.ns.localName }, ns);
}

function getSerializableProperties(element) {
    var descriptor = element.$descriptor;

    return _.filter(descriptor.properties, function(p) {
        var name = p.name;

        // do not serialize defaults
        if (!element.hasOwnProperty(name)) {
            return false;
        }

        var value = element[name];

        // do not serialize default equals
        if (value === p.default) {
            return false;
        }

        return p.isMany ? value.length : true;
    });
}

/**
 * Escape a string attribute to not contain any bad values (line breaks, '"', ...)
 *
 * @param {String} str the string to escape
 * @return {String} the escaped string
 */
function escapeAttr(str) {
    var escapeMap = {
        '\n': '&#10;',
        '\n\r': '&#10;',
        '"': '&quot;'
    };

    // ensure we are handling strings here
    str = _.isString(str) ? str : '' + str;

    return str.replace(/(\n|\n\r|")/g, function(str) {
        return escapeMap[str];
    });
}

function filterAttributes(props) {
    return _.filter(props, function(p) { return p.isAttr; });
}

function filterContained(props) {
    return _.filter(props, function(p) { return !p.isAttr; });
}


function ReferenceSerializer(parent, ns) {
    this.ns = ns;
}

ReferenceSerializer.prototype.build = function(element) {
    this.element = element;
    return this;
};

ReferenceSerializer.prototype.serializeTo = function(writer) {
    writer
        .appendIndent()
        .append('<' + nsName(this.ns) + '>' + this.element.id + '</' + nsName(this.ns) + '>')
        .appendNewLine();
};

function BodySerializer() {}

BodySerializer.prototype.serializeValue = BodySerializer.prototype.serializeTo = function(writer) {
    var value = this.value,
        escape = this.escape;

    if (escape) {
        writer.append('<![CDATA[');
    }

    writer.append(this.value);

    if (escape) {
        writer.append(']]>');
    }
};

BodySerializer.prototype.build = function(prop, value) {
    this.value = value;

    if (prop.type === 'String' && CDATA_ESCAPE.test(value)) {
        this.escape = true;
    }

    return this;
};

function ValueSerializer(ns) {
    this.ns = ns;
}

ValueSerializer.prototype = new BodySerializer();

ValueSerializer.prototype.serializeTo = function(writer) {

    writer
        .appendIndent()
        .append('<' + nsName(this.ns) + '>');

    this.serializeValue(writer);

    writer
        .append( '</' + nsName(this.ns) + '>')
        .appendNewLine();
};

function ElementSerializer(parent, ns) {
    this.body = [];
    this.attrs = [];

    this.parent = parent;
    this.ns = ns;
}

ElementSerializer.prototype.build = function(element) {
    this.element = element;

    var otherAttrs = this.parseNsAttributes(element);

    if (!this.ns) {
        this.ns = this.nsTagName(element.$descriptor);
    }

    if (element.$descriptor.isGeneric) {
        this.parseGeneric(element);
    } else {
        var properties = getSerializableProperties(element);

        this.parseAttributes(filterAttributes(properties));
        this.parseContainments(filterContained(properties));

        this.parseGenericAttributes(element, otherAttrs);
    }

    return this;
};

ElementSerializer.prototype.nsTagName = function(descriptor) {
    var effectiveNs = this.logNamespaceUsed(descriptor.ns);
    return getElementNs(effectiveNs, descriptor);
};

ElementSerializer.prototype.nsPropertyTagName = function(descriptor) {
    var effectiveNs = this.logNamespaceUsed(descriptor.ns);
    return getPropertyNs(effectiveNs, descriptor);
};

ElementSerializer.prototype.isLocalNs = function(ns) {
    return ns.uri === this.ns.uri;
};

ElementSerializer.prototype.nsAttributeName = function(element) {

    var ns;

    if (_.isString(element)) {
        ns = parseNameNs(element);
    } else
    if (element.ns) {
        ns = element.ns;
    }

    var effectiveNs = this.logNamespaceUsed(ns);

    // strip prefix if same namespace like parent
    if (this.isLocalNs(effectiveNs)) {
        return { localName: ns.localName };
    } else {
        return _.extend({ localName: ns.localName }, effectiveNs);
    }
};

ElementSerializer.prototype.parseGeneric = function(element) {

    var self = this,
        body = this.body,
        attrs = this.attrs;

    _.forEach(element, function(val, key) {

        if (key === '$body') {
            body.push(new BodySerializer().build({ type: 'String' }, val));
        } else
        if (key === '$children') {
            _.forEach(val, function(child) {
                body.push(new ElementSerializer(self).build(child));
            });
        } else
        if (key.indexOf('$') !== 0) {
            attrs.push({ name: key, value: escapeAttr(val) });
        }
    });
};

/**
 * Parse namespaces and return a list of left over generic attributes
 *
 * @param  {Object} element
 * @return {Array<Object>}
 */
ElementSerializer.prototype.parseNsAttributes = function(element) {
    var self = this;

    var genericAttrs = element.$attrs;

    var attributes = [];

    // parse namespace attributes first
    // and log them. push non namespace attributes to a list
    // and process them later
    _.forEach(genericAttrs, function(value, name) {
        var nameNs = parseNameNs(name);

        if (nameNs.prefix === 'xmlns') {
            self.logNamespace({ prefix: nameNs.localName, uri: value });
        } else
        if (!nameNs.prefix && nameNs.localName === 'xmlns') {
            self.logNamespace({ uri: value });
        } else {
            attributes.push({ name: name, value: value });
        }
    });

    return attributes;
};

ElementSerializer.prototype.parseGenericAttributes = function(element, attributes) {

    var self = this;

    _.forEach(attributes, function(attr) {
        try {
            self.addAttribute(self.nsAttributeName(attr.name), attr.value);
        } catch (e) {
            console.warn('[writer] missing namespace information for ', attr.name, '=', attr.value, 'on', element, e);
        }
    });
};

ElementSerializer.prototype.parseContainments = function(properties) {

    var self = this,
        body = this.body,
        element = this.element,
        typeDesc = element.$descriptor;

    _.forEach(properties, function(p) {
        var value = element.get(p.name),
            isReference = p.isReference,
            isMany = p.isMany;

        var ns = self.nsPropertyTagName(p);

        if (!isMany) {
            value = [ value ];
        }

        if (p.isBody) {
            body.push(new BodySerializer().build(p, value[0]));
        } else
        //if (Types.isSimple(p.type)) {
        if (isSimple(p.type)) {
            _.forEach(value, function(v) {
                body.push(new ValueSerializer(ns).build(p, v));
            });
        } else
        if (isReference) {
            _.forEach(value, function(v) {
                body.push(new ReferenceSerializer(self, ns).build(v));
            });
        } else {
            // allow serialization via type
            // rather than element name
            var asType = p.serialize === 'xsi:type';

            _.forEach(value, function(v) {
                var serializer;

                if (asType) {
                    serializer = new TypeSerializer(self, ns);
                } else {
                    serializer = new ElementSerializer(self);
                }

                body.push(serializer.build(v));
            });
        }
    });
};

ElementSerializer.prototype.getNamespaces = function() {
    if (!this.parent) {
        if (!this.namespaces) {
            this.namespaces = {
                prefixMap: {},
                uriMap: {},
                used: {}
            };
        }
    } else {
        this.namespaces = this.parent.getNamespaces();
    }

    return this.namespaces;
};

ElementSerializer.prototype.logNamespace = function(ns) {
    var namespaces = this.getNamespaces();

    var existing = namespaces.uriMap[ns.uri];

    if (!existing) {
        namespaces.uriMap[ns.uri] = ns;
    }

    namespaces.prefixMap[ns.prefix] = ns.uri;

    return ns;
};

ElementSerializer.prototype.logNamespaceUsed = function(ns) {
    var element = this.element,
        model = element.$model,
        namespaces = this.getNamespaces();

    // ns may be
    //
    //   * prefix only
    //   * prefix:uri

    var prefix = ns.prefix;
    var uri = ns.uri || DEFAULT_NS_MAP[prefix] ||
        namespaces.prefixMap[prefix] || (model ? (model.getPackage(prefix) || {}).uri : null);

    if (!uri) {
        throw new Error('no namespace uri given for prefix <' + ns.prefix + '>');
    }

    ns = namespaces.uriMap[uri];

    if (!ns) {
        ns = this.logNamespace({ prefix: prefix, uri: uri });
    }

    if (!namespaces.used[ns.uri]) {
        namespaces.used[ns.uri] = ns;
    }

    return ns;
};

ElementSerializer.prototype.parseAttributes = function(properties) {
    var self = this,
        element = this.element;

    _.forEach(properties, function(p) {
        self.logNamespaceUsed(p.ns);

        var value = element.get(p.name);

        if (p.isReference) {
            value = value.id;
        }

        self.addAttribute(self.nsAttributeName(p), value);
    });
};

ElementSerializer.prototype.addAttribute = function(name, value) {
    var attrs = this.attrs;

    if (_.isString(value)) {
        value = escapeAttr(value);
    }

    attrs.push({ name: name, value: value });
};

ElementSerializer.prototype.serializeAttributes = function(writer) {
    var element = this.element,
        attrs = this.attrs,
        root = !this.parent,
        namespaces = this.namespaces;

    function collectNsAttrs() {
        return _.collect(namespaces.used, function(ns) {
            var name = 'xmlns' + (ns.prefix ? ':' + ns.prefix : '');
            return { name: name, value: ns.uri };
        });
    }

    if (root) {
        attrs = collectNsAttrs().concat(attrs);
    }

    _.forEach(attrs, function(a) {
        writer
            .append(' ')
            .append(nsName(a.name)).append('="').append(a.value).append('"');
    });
};

ElementSerializer.prototype.serializeTo = function(writer) {
    var hasBody = this.body.length;

    writer
        .appendIndent()
        .append('<' + nsName(this.ns));

    this.serializeAttributes(writer);

    writer
        .append(hasBody ? '>' : ' />')
        .appendNewLine();

    writer.indent();

    _.forEach(this.body, function(b) {
        b.serializeTo(writer);
    });

    writer.unindent();

    if (hasBody) {
        writer
            .appendIndent()
            .append('</' + nsName(this.ns) + '>')
            .appendNewLine();
    }
};

/**
 * A serializer for types that handles serialization of data types
 */
function TypeSerializer(parent, ns) {
    ElementSerializer.call(this, parent, ns);
}

TypeSerializer.prototype = new ElementSerializer();

TypeSerializer.prototype.build = function(element) {
    this.element = element;
    this.typeNs = this.nsTagName(element.$descriptor);

    return ElementSerializer.prototype.build.call(this, element);
};

TypeSerializer.prototype.isLocalNs = function(ns) {
    return ns.uri === this.typeNs.uri;
};

function SavingWriter() {
    this.value = '';

    this.write = function(str) {
        this.value += str;
    };
}

function FormatingWriter(out, format) {

    var indent = [''];

    this.append = function(str) {
        out.write(str);

        return this;
    };

    this.appendNewLine = function() {
        if (format) {
            out.write('\n');
        }

        return this;
    };

    this.appendIndent = function() {
        if (format) {
            out.write(indent.join('  '));
        }

        return this;
    };

    this.indent = function() {
        indent.push('');
        return this;
    };

    this.unindent = function() {
        indent.pop();
        return this;
    };
}

/**
 * A writer for meta-model backed document trees
 *
 * @class XMLWriter
 */
function XMLWriter(options) {

    options = _.extend({ format: false, preamble: true }, options || {});

    function toXML(tree, writer) {
        var internalWriter = writer || new SavingWriter();
        var formatingWriter = new FormatingWriter(internalWriter, options.format);

        if (options.preamble) {
            formatingWriter.append(XML_PREAMBLE);
        }

        new ElementSerializer().build(tree).serializeTo(formatingWriter);

        if (!writer) {
            return internalWriter.value;
        }
    }

    return {
        toXML: toXML
    };
}

//module.exports = XMLWriter;
'use strict';
//
//var _ = require('lodash');
//
//var Moddle = require('bpmn-moddle');
    //ModdleXml = require('moddle-xml');
//

function createModel(packages) {
    return new Moddle(packages);
}

/**
 * A sub class of {@link Moddle} with support for import and export of BPMN 2.0 xml files.
 *
 * @class BpmnModdle
 * @extends Moddle
 *
 * @param {Object|Array} packages to use for instantiating the model
 * @param {Object} [options] additional options to pass over
 */
function BpmnModdle(packages, options) {
    var packages = {
        bpmn: {
            "name": "BPMN20",
            "uri": "http://www.omg.org/spec/BPMN/20100524/MODEL",
            "associations": [],
            "types": [
                {
                    "name": "Interface",
                    "superClass": [
                        "RootElement"
                    ],
                    "properties": [
                        {
                            "name": "name",
                            "isAttr": true,
                            "type": "String"
                        },
                        {
                            "name": "operations",
                            "type": "Operation",
                            "association": "A_operations_interface",
                            "isMany": true
                        },
                        {
                            "name": "implementationRef",
                            "type": "String",
                            "isAttr": true
                        }
                    ]
                },
                {
                    "name": "Operation",
                    "superClass": [
                        "BaseElement"
                    ],
                    "properties": [
                        {
                            "name": "name",
                            "isAttr": true,
                            "type": "String"
                        },
                        {
                            "name": "inMessageRef",
                            "type": "Message",
                            "association": "A_inMessageRef_operation",
                            "isAttr": true,
                            "isReference": true
                        },
                        {
                            "name": "outMessageRef",
                            "type": "Message",
                            "association": "A_outMessageRef_operation",
                            "isAttr": true,
                            "isReference": true
                        },
                        {
                            "name": "errorRefs",
                            "type": "Error",
                            "association": "A_errorRefs_operation",
                            "isMany": true,
                            "isReference": true
                        },
                        {
                            "name": "implementationRef",
                            "type": "String",
                            "isAttr": true
                        }
                    ]
                },
                {
                    "name": "EndPoint",
                    "superClass": [
                        "RootElement"
                    ]
                },
                {
                    "name": "Auditing",
                    "superClass": [
                        "BaseElement"
                    ]
                },
                {
                    "name": "GlobalTask",
                    "superClass": [
                        "CallableElement"
                    ],
                    "properties": [
                        {
                            "name": "resources",
                            "type": "ResourceRole",
                            "association": "A_resources_globalTask",
                            "isMany": true
                        }
                    ]
                },
                {
                    "name": "Monitoring",
                    "superClass": [
                        "BaseElement"
                    ]
                },
                {
                    "name": "Performer",
                    "superClass": [
                        "ResourceRole"
                    ]
                },
                {
                    "name": "Process",
                    "superClass": [
                        "FlowElementsContainer",
                        "CallableElement"
                    ],
                    "properties": [
                        {
                            "name": "processType",
                            "type": "ProcessType",
                            "isAttr": true
                        },
                        {
                            "name": "isClosed",
                            "isAttr": true,
                            "type": "Boolean"
                        },
                        {
                            "name": "auditing",
                            "type": "Auditing",
                            "association": "A_auditing_process"
                        },
                        {
                            "name": "monitoring",
                            "type": "Monitoring",
                            "association": "A_monitoring_process"
                        },
                        {
                            "name": "properties",
                            "type": "Property",
                            "association": "A_properties_process",
                            "isMany": true
                        },
                        {
                            "name": "supports",
                            "type": "Process",
                            "association": "A_supports_process",
                            "isMany": true,
                            "isReference": true
                        },
                        {
                            "name": "definitionalCollaborationRef",
                            "type": "Collaboration",
                            "association": "A_definitionalCollaborationRef_process",
                            "isAttr": true,
                            "isReference": true
                        },
                        {
                            "name": "isExecutable",
                            "isAttr": true,
                            "type": "Boolean"
                        },
                        {
                            "name": "resources",
                            "type": "ResourceRole",
                            "association": "A_resources_process",
                            "isMany": true
                        },
                        {
                            "name": "artifacts",
                            "type": "Artifact",
                            "association": "A_artifacts_process",
                            "isMany": true
                        },
                        {
                            "name": "correlationSubscriptions",
                            "type": "CorrelationSubscription",
                            "association": "A_correlationSubscriptions_process",
                            "isMany": true
                        }
                    ]
                },
                {
                    "name": "LaneSet",
                    "superClass": [
                        "BaseElement"
                    ],
                    "properties": [
                        {
                            "name": "lanes",
                            "type": "Lane",
                            "association": "A_lanes_laneSet",
                            "isMany": true
                        },
                        {
                            "name": "name",
                            "isAttr": true,
                            "type": "String"
                        }
                    ]
                },
                {
                    "name": "Lane",
                    "superClass": [
                        "BaseElement"
                    ],
                    "properties": [
                        {
                            "name": "name",
                            "isAttr": true,
                            "type": "String"
                        },
                        {
                            "name": "childLaneSet",
                            "type": "LaneSet",
                            "association": "A_childLaneSet_parentLane"
                        },
                        {
                            "name": "partitionElementRef",
                            "type": "BaseElement",
                            "association": "A_partitionElementRef_lane",
                            "isAttr": true,
                            "isReference": true
                        },
                        {
                            "name": "flowNodeRef",
                            "type": "FlowNode",
                            "association": "A_flowNodeRefs_lanes",
                            "isMany": true,
                            "isReference": true
                        },
                        {
                            "name": "partitionElement",
                            "type": "BaseElement",
                            "association": "A_partitionElement_lane"
                        }
                    ]
                },
                {
                    "name": "GlobalManualTask",
                    "superClass": [
                        "GlobalTask"
                    ]
                },
                {
                    "name": "ManualTask",
                    "superClass": [
                        "Task"
                    ]
                },
                {
                    "name": "UserTask",
                    "superClass": [
                        "Task"
                    ],
                    "properties": [
                        {
                            "name": "renderings",
                            "type": "Rendering",
                            "association": "A_renderings_usertask",
                            "isMany": true
                        },
                        {
                            "name": "implementation",
                            "isAttr": true,
                            "type": "String"
                        }
                    ]
                },
                {
                    "name": "Rendering",
                    "superClass": [
                        "BaseElement"
                    ]
                },
                {
                    "name": "HumanPerformer",
                    "superClass": [
                        "Performer"
                    ]
                },
                {
                    "name": "PotentialOwner",
                    "superClass": [
                        "HumanPerformer"
                    ]
                },
                {
                    "name": "GlobalUserTask",
                    "superClass": [
                        "GlobalTask"
                    ],
                    "properties": [
                        {
                            "name": "implementation",
                            "isAttr": true,
                            "type": "String"
                        },
                        {
                            "name": "renderings",
                            "type": "Rendering",
                            "association": "A_renderings_globalUserTask",
                            "isMany": true
                        }
                    ]
                },
                {
                    "name": "Gateway",
                    "isAbstract": true,
                    "superClass": [
                        "FlowNode"
                    ],
                    "properties": [
                        {
                            "name": "gatewayDirection",
                            "type": "GatewayDirection",
                            "default": "Unspecified",
                            "isAttr": true
                        }
                    ]
                },
                {
                    "name": "EventBasedGateway",
                    "superClass": [
                        "Gateway"
                    ],
                    "properties": [
                        {
                            "name": "instantiate",
                            "default": false,
                            "isAttr": true,
                            "type": "Boolean"
                        },
                        {
                            "name": "eventGatewayType",
                            "type": "EventBasedGatewayType",
                            "isAttr": true,
                            "default": "Exclusive"
                        }
                    ]
                },
                {
                    "name": "ComplexGateway",
                    "superClass": [
                        "Gateway"
                    ],
                    "properties": [
                        {
                            "name": "activationCondition",
                            "type": "Expression",
                            "association": "A_activationCondition_complexGateway"
                        },
                        {
                            "name": "default",
                            "type": "SequenceFlow",
                            "association": "A_default_complexGateway",
                            "isAttr": true,
                            "isReference": true
                        }
                    ]
                },
                {
                    "name": "ExclusiveGateway",
                    "superClass": [
                        "Gateway"
                    ],
                    "properties": [
                        {
                            "name": "default",
                            "type": "SequenceFlow",
                            "association": "A_default_exclusiveGateway",
                            "isAttr": true,
                            "isReference": true
                        }
                    ]
                },
                {
                    "name": "InclusiveGateway",
                    "superClass": [
                        "Gateway"
                    ],
                    "properties": [
                        {
                            "name": "default",
                            "type": "SequenceFlow",
                            "association": "A_default_inclusiveGateway",
                            "isAttr": true,
                            "isReference": true
                        }
                    ]
                },
                {
                    "name": "ParallelGateway",
                    "superClass": [
                        "Gateway"
                    ]
                },
                {
                    "name": "RootElement",
                    "isAbstract": true,
                    "superClass": [
                        "BaseElement"
                    ]
                },
                {
                    "name": "Relationship",
                    "superClass": [
                        "BaseElement"
                    ],
                    "properties": [
                        {
                            "name": "type",
                            "isAttr": true,
                            "type": "String"
                        },
                        {
                            "name": "direction",
                            "type": "RelationshipDirection",
                            "isAttr": true
                        },
                        {
                            "name": "sources",
                            "association": "A_sources_relationship",
                            "isMany": true,
                            "isReference": true,
                            "type": "Element"
                        },
                        {
                            "name": "targets",
                            "association": "A_targets_relationship",
                            "isMany": true,
                            "isReference": true,
                            "type": "Element"
                        }
                    ]
                },
                {
                    "name": "BaseElement",
                    "isAbstract": true,
                    "properties": [
                        {
                            "name": "id",
                            "isAttr": true,
                            "type": "String"
                        },
                        {
                            "name": "extensionDefinitions",
                            "type": "ExtensionDefinition",
                            "association": "A_extensionDefinitions_baseElement",
                            "isMany": true,
                            "isReference": true
                        },
                        {
                            "name": "extensionElements",
                            "type": "ExtensionElements",
                            "association": "A_extensionElements_baseElement"
                        },
                        {
                            "name": "documentation",
                            "type": "Documentation",
                            "association": "A_documentation_baseElement",
                            "isMany": true
                        }
                    ]
                },
                {
                    "name": "Extension",
                    "properties": [
                        {
                            "name": "mustUnderstand",
                            "default": false,
                            "isAttr": true,
                            "type": "Boolean"
                        },
                        {
                            "name": "definition",
                            "type": "ExtensionDefinition",
                            "association": "A_definition_extension"
                        }
                    ]
                },
                {
                    "name": "ExtensionDefinition",
                    "properties": [
                        {
                            "name": "name",
                            "isAttr": true,
                            "type": "String"
                        },
                        {
                            "name": "extensionAttributeDefinitions",
                            "type": "ExtensionAttributeDefinition",
                            "association": "A_extensionAttributeDefinitions_extensionDefinition",
                            "isMany": true
                        }
                    ]
                },
                {
                    "name": "ExtensionAttributeDefinition",
                    "properties": [
                        {
                            "name": "name",
                            "isAttr": true,
                            "type": "String"
                        },
                        {
                            "name": "type",
                            "isAttr": true,
                            "type": "String"
                        },
                        {
                            "name": "isReference",
                            "default": false,
                            "isAttr": true,
                            "type": "Boolean"
                        },
                        {
                            "name": "extensionDefinition",
                            "type": "ExtensionDefinition",
                            "association": "A_extensionAttributeDefinitions_extensionDefinition",
                            "isAttr": true,
                            "isReference": true
                        }
                    ]
                },
                {
                    "name": "ExtensionElements",
                    "properties": [
                        {
                            "name": "valueRef",
                            "association": "A_valueRef_extensionElements",
                            "isAttr": true,
                            "isReference": true,
                            "type": "Element"
                        },
                        {
                            "name": "values",
                            "association": "A_value_extensionElements",
                            "type": "Element",
                            "isMany": true
                        },
                        {
                            "name": "extensionAttributeDefinition",
                            "type": "ExtensionAttributeDefinition",
                            "association": "A_extensionAttributeDefinition_extensionElements",
                            "isAttr": true,
                            "isReference": true
                        }
                    ]
                },
                {
                    "name": "Documentation",
                    "superClass": [
                        "BaseElement"
                    ],
                    "properties": [
                        {
                            "name": "text",
                            "type": "String",
                            "isBody": true
                        },
                        {
                            "name": "textFormat",
                            "default": "text/plain",
                            "isAttr": true,
                            "type": "String"
                        }
                    ]
                },
                {
                    "name": "Event",
                    "isAbstract": true,
                    "superClass": [
                        "FlowNode",
                        "InteractionNode"
                    ],
                    "properties": [
                        {
                            "name": "properties",
                            "type": "Property",
                            "association": "A_properties_event",
                            "isMany": true
                        }
                    ]
                },
                {
                    "name": "IntermediateCatchEvent",
                    "superClass": [
                        "CatchEvent"
                    ]
                },
                {
                    "name": "IntermediateThrowEvent",
                    "superClass": [
                        "ThrowEvent"
                    ]
                },
                {
                    "name": "EndEvent",
                    "superClass": [
                        "ThrowEvent"
                    ]
                },
                {
                    "name": "StartEvent",
                    "superClass": [
                        "CatchEvent"
                    ],
                    "properties": [
                        {
                            "name": "isInterrupting",
                            "default": true,
                            "isAttr": true,
                            "type": "Boolean"
                        }
                    ]
                },
                {
                    "name": "ThrowEvent",
                    "isAbstract": true,
                    "superClass": [
                        "Event"
                    ],
                    "properties": [
                        {
                            "name": "inputSet",
                            "type": "InputSet",
                            "association": "A_inputSet_throwEvent"
                        },
                        {
                            "name": "eventDefinitionRefs",
                            "type": "EventDefinition",
                            "association": "A_eventDefinitionRefs_throwEvent",
                            "isMany": true,
                            "isReference": true
                        },
                        {
                            "name": "dataInputAssociation",
                            "type": "DataInputAssociation",
                            "association": "A_dataInputAssociation_throwEvent",
                            "isMany": true
                        },
                        {
                            "name": "dataInputs",
                            "type": "DataInput",
                            "association": "A_dataInputs_throwEvent",
                            "isMany": true
                        },
                        {
                            "name": "eventDefinitions",
                            "type": "EventDefinition",
                            "association": "A_eventDefinitions_throwEvent",
                            "isMany": true
                        }
                    ]
                },
                {
                    "name": "CatchEvent",
                    "isAbstract": true,
                    "superClass": [
                        "Event"
                    ],
                    "properties": [
                        {
                            "name": "parallelMultiple",
                            "isAttr": true,
                            "type": "Boolean",
                            "default": false
                        },
                        {
                            "name": "outputSet",
                            "type": "OutputSet",
                            "association": "A_outputSet_catchEvent"
                        },
                        {
                            "name": "eventDefinitionRefs",
                            "type": "EventDefinition",
                            "association": "A_eventDefinitionRefs_catchEvent",
                            "isMany": true,
                            "isReference": true
                        },
                        {
                            "name": "dataOutputAssociation",
                            "type": "DataOutputAssociation",
                            "association": "A_dataOutputAssociation_catchEvent",
                            "isMany": true
                        },
                        {
                            "name": "dataOutputs",
                            "type": "DataOutput",
                            "association": "A_dataOutputs_catchEvent",
                            "isMany": true
                        },
                        {
                            "name": "eventDefinitions",
                            "type": "EventDefinition",
                            "association": "A_eventDefinitions_catchEvent",
                            "isMany": true
                        }
                    ]
                },
                {
                    "name": "BoundaryEvent",
                    "superClass": [
                        "CatchEvent"
                    ],
                    "properties": [
                        {
                            "name": "cancelActivity",
                            "default": true,
                            "isAttr": true,
                            "type": "Boolean"
                        },
                        {
                            "name": "attachedToRef",
                            "type": "Activity",
                            "association": "A_boundaryEventRefs_attachedToRef",
                            "isAttr": true,
                            "isReference": true
                        }
                    ]
                },
                {
                    "name": "EventDefinition",
                    "isAbstract": true,
                    "superClass": [
                        "RootElement"
                    ]
                },
                {
                    "name": "CancelEventDefinition",
                    "superClass": [
                        "EventDefinition"
                    ]
                },
                {
                    "name": "ErrorEventDefinition",
                    "superClass": [
                        "EventDefinition"
                    ],
                    "properties": [
                        {
                            "name": "errorRef",
                            "type": "Error",
                            "association": "A_errorRef_errorEventDefinition",
                            "isAttr": true,
                            "isReference": true
                        }
                    ]
                },
                {
                    "name": "TerminateEventDefinition",
                    "superClass": [
                        "EventDefinition"
                    ]
                },
                {
                    "name": "EscalationEventDefinition",
                    "superClass": [
                        "EventDefinition"
                    ],
                    "properties": [
                        {
                            "name": "escalationRef",
                            "type": "Escalation",
                            "association": "A_escalationRef_escalationEventDefinition",
                            "isAttr": true,
                            "isReference": true
                        }
                    ]
                },
                {
                    "name": "Escalation",
                    "properties": [
                        {
                            "name": "structureRef",
                            "type": "ItemDefinition",
                            "association": "A_structureRef_escalation",
                            "isAttr": true,
                            "isReference": true
                        },
                        {
                            "name": "name",
                            "isAttr": true,
                            "type": "String"
                        },
                        {
                            "name": "escalationCode",
                            "isAttr": true,
                            "type": "String"
                        }
                    ],
                    "superClass": [
                        "RootElement"
                    ]
                },
                {
                    "name": "CompensateEventDefinition",
                    "superClass": [
                        "EventDefinition"
                    ],
                    "properties": [
                        {
                            "name": "waitForCompletion",
                            "isAttr": true,
                            "type": "Boolean"
                        },
                        {
                            "name": "activityRef",
                            "type": "Activity",
                            "association": "A_activityRef_compensateEventDefinition",
                            "isAttr": true,
                            "isReference": true
                        }
                    ]
                },
                {
                    "name": "TimerEventDefinition",
                    "superClass": [
                        "EventDefinition"
                    ],
                    "properties": [
                        {
                            "name": "timeDate",
                            "type": "Expression",
                            "association": "A_timeDate_timerEventDefinition"
                        },
                        {
                            "name": "timeCycle",
                            "type": "Expression",
                            "association": "A_timeCycle_timerEventDefinition"
                        },
                        {
                            "name": "timeDuration",
                            "type": "Expression",
                            "association": "A_timeDuration_timerEventDefinition"
                        }
                    ]
                },
                {
                    "name": "LinkEventDefinition",
                    "superClass": [
                        "EventDefinition"
                    ],
                    "properties": [
                        {
                            "name": "name",
                            "isAttr": true,
                            "type": "String"
                        },
                        {
                            "name": "target",
                            "type": "LinkEventDefinition",
                            "association": "A_target_source",
                            "isAttr": true,
                            "isReference": true
                        },
                        {
                            "name": "source",
                            "type": "LinkEventDefinition",
                            "association": "A_target_source",
                            "isMany": true,
                            "isReference": true
                        }
                    ]
                },
                {
                    "name": "MessageEventDefinition",
                    "superClass": [
                        "EventDefinition"
                    ],
                    "properties": [
                        {
                            "name": "messageRef",
                            "type": "Message",
                            "association": "A_messageRef_messageEventDefinition",
                            "isAttr": true,
                            "isReference": true
                        },
                        {
                            "name": "operationRef",
                            "type": "Operation",
                            "association": "A_operationRef_messageEventDefinition",
                            "isAttr": true,
                            "isReference": true
                        }
                    ]
                },
                {
                    "name": "ConditionalEventDefinition",
                    "superClass": [
                        "EventDefinition"
                    ],
                    "properties": [
                        {
                            "name": "condition",
                            "type": "Expression",
                            "association": "A_condition_conditionalEventDefinition",
                            "serialize": "xsi:type"
                        }
                    ]
                },
                {
                    "name": "SignalEventDefinition",
                    "superClass": [
                        "EventDefinition"
                    ],
                    "properties": [
                        {
                            "name": "signalRef",
                            "type": "Signal",
                            "association": "A_signalRef_signalEventDefinition",
                            "isAttr": true,
                            "isReference": true
                        }
                    ]
                },
                {
                    "name": "Signal",
                    "superClass": [
                        "RootElement"
                    ],
                    "properties": [
                        {
                            "name": "structureRef",
                            "type": "ItemDefinition",
                            "association": "A_structureRef_signal",
                            "isAttr": true,
                            "isReference": true
                        },
                        {
                            "name": "name",
                            "isAttr": true,
                            "type": "String"
                        }
                    ]
                },
                {
                    "name": "ImplicitThrowEvent",
                    "superClass": [
                        "ThrowEvent"
                    ]
                },
                {
                    "name": "DataState",
                    "superClass": [
                        "BaseElement"
                    ],
                    "properties": [
                        {
                            "name": "name",
                            "isAttr": true,
                            "type": "String"
                        }
                    ]
                },
                {
                    "name": "ItemAwareElement",
                    "superClass": [
                        "BaseElement"
                    ],
                    "properties": [
                        {
                            "name": "itemSubjectRef",
                            "type": "ItemDefinition",
                            "association": "A_itemSubjectRef_itemAwareElement",
                            "isAttr": true,
                            "isReference": true
                        },
                        {
                            "name": "dataState",
                            "type": "DataState",
                            "association": "A_dataState_itemAwareElement"
                        }
                    ]
                },
                {
                    "name": "DataAssociation",
                    "superClass": [
                        "BaseElement"
                    ],
                    "properties": [
                        {
                            "name": "transformation",
                            "type": "FormalExpression",
                            "association": "A_transformation_dataAssociation"
                        },
                        {
                            "name": "assignment",
                            "type": "Assignment",
                            "association": "A_assignment_dataAssociation",
                            "isMany": true
                        },
                        {
                            "name": "sourceRef",
                            "type": "ItemAwareElement",
                            "association": "A_sourceRef_dataAssociation",
                            "isMany": true,
                            "isReference": true
                        },
                        {
                            "name": "targetRef",
                            "type": "ItemAwareElement",
                            "association": "A_targetRef_dataAssociation",
                            "isReference": true
                        }
                    ]
                },
                {
                    "name": "DataInput",
                    "superClass": [
                        "ItemAwareElement"
                    ],
                    "properties": [
                        {
                            "name": "name",
                            "isAttr": true,
                            "type": "String"
                        },
                        {
                            "name": "isCollection",
                            "default": false,
                            "isAttr": true,
                            "type": "Boolean"
                        },
                        {
                            "name": "inputSetRefs",
                            "type": "InputSet",
                            "association": "A_dataInputRefs_inputSetRefs",
                            "isVirtual": true,
                            "isMany": true,
                            "isReference": true
                        },
                        {
                            "name": "inputSetWithOptional",
                            "type": "InputSet",
                            "association": "A_optionalInputRefs_inputSetWithOptional",
                            "isVirtual": true,
                            "isMany": true,
                            "isReference": true
                        },
                        {
                            "name": "inputSetWithWhileExecuting",
                            "type": "InputSet",
                            "association": "A_whileExecutingInputRefs_inputSetWithWhileExecuting",
                            "isVirtual": true,
                            "isMany": true,
                            "isReference": true
                        }
                    ]
                },
                {
                    "name": "DataOutput",
                    "superClass": [
                        "ItemAwareElement"
                    ],
                    "properties": [
                        {
                            "name": "name",
                            "isAttr": true,
                            "type": "String"
                        },
                        {
                            "name": "isCollection",
                            "default": false,
                            "isAttr": true,
                            "type": "Boolean"
                        },
                        {
                            "name": "outputSetRefs",
                            "type": "OutputSet",
                            "association": "A_dataOutputRefs_outputSetRefs",
                            "isVirtual": true,
                            "isMany": true,
                            "isReference": true
                        },
                        {
                            "name": "outputSetWithOptional",
                            "type": "OutputSet",
                            "association": "A_outputSetWithOptional_optionalOutputRefs",
                            "isVirtual": true,
                            "isMany": true,
                            "isReference": true
                        },
                        {
                            "name": "outputSetWithWhileExecuting",
                            "type": "OutputSet",
                            "association": "A_outputSetWithWhileExecuting_whileExecutingOutputRefs",
                            "isVirtual": true,
                            "isMany": true,
                            "isReference": true
                        }
                    ]
                },
                {
                    "name": "InputSet",
                    "superClass": [
                        "BaseElement"
                    ],
                    "properties": [
                        {
                            "name": "name",
                            "isAttr": true,
                            "type": "String"
                        },
                        {
                            "name": "dataInputRefs",
                            "type": "DataInput",
                            "association": "A_dataInputRefs_inputSetRefs",
                            "isMany": true,
                            "isReference": true
                        },
                        {
                            "name": "optionalInputRefs",
                            "type": "DataInput",
                            "association": "A_optionalInputRefs_inputSetWithOptional",
                            "isMany": true,
                            "isReference": true
                        },
                        {
                            "name": "whileExecutingInputRefs",
                            "type": "DataInput",
                            "association": "A_whileExecutingInputRefs_inputSetWithWhileExecuting",
                            "isMany": true,
                            "isReference": true
                        },
                        {
                            "name": "outputSetRefs",
                            "type": "OutputSet",
                            "association": "A_inputSetRefs_outputSetRefs",
                            "isMany": true,
                            "isReference": true
                        }
                    ]
                },
                {
                    "name": "OutputSet",
                    "superClass": [
                        "BaseElement"
                    ],
                    "properties": [
                        {
                            "name": "dataOutputRefs",
                            "type": "DataOutput",
                            "association": "A_dataOutputRefs_outputSetRefs",
                            "isMany": true,
                            "isReference": true
                        },
                        {
                            "name": "name",
                            "isAttr": true,
                            "type": "String"
                        },
                        {
                            "name": "inputSetRefs",
                            "type": "InputSet",
                            "association": "A_inputSetRefs_outputSetRefs",
                            "isMany": true,
                            "isReference": true
                        },
                        {
                            "name": "optionalOutputRefs",
                            "type": "DataOutput",
                            "association": "A_outputSetWithOptional_optionalOutputRefs",
                            "isMany": true,
                            "isReference": true
                        },
                        {
                            "name": "whileExecutingOutputRefs",
                            "type": "DataOutput",
                            "association": "A_outputSetWithWhileExecuting_whileExecutingOutputRefs",
                            "isMany": true,
                            "isReference": true
                        }
                    ]
                },
                {
                    "name": "Property",
                    "superClass": [
                        "ItemAwareElement"
                    ],
                    "properties": [
                        {
                            "name": "name",
                            "isAttr": true,
                            "type": "String"
                        }
                    ]
                },
                {
                    "name": "DataInputAssociation",
                    "superClass": [
                        "DataAssociation"
                    ]
                },
                {
                    "name": "DataOutputAssociation",
                    "superClass": [
                        "DataAssociation"
                    ]
                },
                {
                    "name": "InputOutputSpecification",
                    "superClass": [
                        "BaseElement"
                    ],
                    "properties": [
                        {
                            "name": "inputSets",
                            "type": "InputSet",
                            "association": "A_inputSets_inputOutputSpecification",
                            "isMany": true
                        },
                        {
                            "name": "outputSets",
                            "type": "OutputSet",
                            "association": "A_outputSets_inputOutputSpecification",
                            "isMany": true
                        },
                        {
                            "name": "dataInputs",
                            "type": "DataInput",
                            "association": "A_dataInputs_inputOutputSpecification",
                            "isMany": true
                        },
                        {
                            "name": "dataOutputs",
                            "type": "DataOutput",
                            "association": "A_dataOutputs_inputOutputSpecification",
                            "isMany": true
                        }
                    ]
                },
                {
                    "name": "DataObject",
                    "superClass": [
                        "FlowElement",
                        "ItemAwareElement"
                    ],
                    "properties": [
                        {
                            "name": "isCollection",
                            "default": false,
                            "isAttr": true,
                            "type": "Boolean"
                        }
                    ]
                },
                {
                    "name": "InputOutputBinding",
                    "properties": [
                        {
                            "name": "inputDataRef",
                            "type": "InputSet",
                            "association": "A_inputDataRef_inputOutputBinding",
                            "isAttr": true,
                            "isReference": true
                        },
                        {
                            "name": "outputDataRef",
                            "type": "OutputSet",
                            "association": "A_outputDataRef_inputOutputBinding",
                            "isAttr": true,
                            "isReference": true
                        },
                        {
                            "name": "operationRef",
                            "type": "Operation",
                            "association": "A_operationRef_ioBinding",
                            "isAttr": true,
                            "isReference": true
                        }
                    ]
                },
                {
                    "name": "Assignment",
                    "superClass": [
                        "BaseElement"
                    ],
                    "properties": [
                        {
                            "name": "from",
                            "type": "Expression",
                            "association": "A_from_assignment"
                        },
                        {
                            "name": "to",
                            "type": "Expression",
                            "association": "A_to_assignment"
                        }
                    ]
                },
                {
                    "name": "DataStore",
                    "superClass": [
                        "RootElement",
                        "ItemAwareElement"
                    ],
                    "properties": [
                        {
                            "name": "name",
                            "isAttr": true,
                            "type": "String"
                        },
                        {
                            "name": "capacity",
                            "isAttr": true,
                            "type": "Integer"
                        },
                        {
                            "name": "isUnlimited",
                            "default": true,
                            "isAttr": true,
                            "type": "Boolean"
                        }
                    ]
                },
                {
                    "name": "DataStoreReference",
                    "superClass": [
                        "ItemAwareElement",
                        "FlowElement"
                    ],
                    "properties": [
                        {
                            "name": "dataStoreRef",
                            "type": "DataStore",
                            "association": "A_dataStoreRef_dataStoreReference",
                            "isAttr": true,
                            "isReference": true
                        }
                    ]
                },
                {
                    "name": "DataObjectReference",
                    "superClass": [
                        "ItemAwareElement",
                        "FlowElement"
                    ],
                    "properties": [
                        {
                            "name": "dataObjectRef",
                            "type": "DataObject",
                            "association": "A_dataObjectRef_dataObject",
                            "isAttr": true,
                            "isReference": true
                        }
                    ]
                },
                {
                    "name": "ConversationLink",
                    "superClass": [
                        "BaseElement"
                    ],
                    "properties": [
                        {
                            "name": "sourceRef",
                            "type": "InteractionNode",
                            "association": "A_sourceRef_outgoingConversationLinks",
                            "isAttr": true,
                            "isReference": true
                        },
                        {
                            "name": "targetRef",
                            "type": "InteractionNode",
                            "association": "A_targetRef_incomingConversationLinks",
                            "isAttr": true,
                            "isReference": true
                        },
                        {
                            "name": "name",
                            "isAttr": true,
                            "type": "String"
                        }
                    ]
                },
                {
                    "name": "ConversationAssociation",
                    "superClass": [
                        "BaseElement"
                    ],
                    "properties": [
                        {
                            "name": "innerConversationNodeRef",
                            "type": "ConversationNode",
                            "association": "A_innerConversationNodeRef_conversationAssociation",
                            "isAttr": true,
                            "isReference": true
                        },
                        {
                            "name": "outerConversationNodeRef",
                            "type": "ConversationNode",
                            "association": "A_outerConversationNodeRef_conversationAssociation",
                            "isAttr": true,
                            "isReference": true
                        }
                    ]
                },
                {
                    "name": "CallConversation",
                    "superClass": [
                        "ConversationNode"
                    ],
                    "properties": [
                        {
                            "name": "calledCollaborationRef",
                            "type": "Collaboration",
                            "association": "A_calledCollaborationRef_callConversation",
                            "isAttr": true,
                            "isReference": true
                        },
                        {
                            "name": "participantAssociations",
                            "type": "ParticipantAssociation",
                            "association": "A_participantAssociations_callConversation",
                            "isMany": true
                        }
                    ]
                },
                {
                    "name": "Conversation",
                    "superClass": [
                        "ConversationNode"
                    ]
                },
                {
                    "name": "SubConversation",
                    "superClass": [
                        "ConversationNode"
                    ],
                    "properties": [
                        {
                            "name": "conversationNodes",
                            "type": "ConversationNode",
                            "association": "A_conversationNodes_subConversation",
                            "isMany": true
                        }
                    ]
                },
                {
                    "name": "ConversationNode",
                    "isAbstract": true,
                    "superClass": [
                        "InteractionNode",
                        "BaseElement"
                    ],
                    "properties": [
                        {
                            "name": "name",
                            "isAttr": true,
                            "type": "String"
                        },
                        {
                            "name": "participantRefs",
                            "type": "Participant",
                            "association": "A_participantRefs_conversationNode",
                            "isMany": true,
                            "isReference": true
                        },
                        {
                            "name": "messageFlowRefs",
                            "type": "MessageFlow",
                            "association": "A_messageFlowRefs_communication",
                            "isMany": true,
                            "isReference": true
                        },
                        {
                            "name": "correlationKeys",
                            "type": "CorrelationKey",
                            "association": "A_correlationKeys_conversationNode",
                            "isMany": true
                        }
                    ]
                },
                {
                    "name": "GlobalConversation",
                    "superClass": [
                        "Collaboration"
                    ]
                },
                {
                    "name": "PartnerEntity",
                    "superClass": [
                        "RootElement"
                    ],
                    "properties": [
                        {
                            "name": "name",
                            "isAttr": true,
                            "type": "String"
                        },
                        {
                            "name": "participantRef",
                            "type": "Participant",
                            "association": "A_partnerEntityRef_participantRef",
                            "isMany": true,
                            "isReference": true
                        }
                    ]
                },
                {
                    "name": "PartnerRole",
                    "superClass": [
                        "RootElement"
                    ],
                    "properties": [
                        {
                            "name": "name",
                            "isAttr": true,
                            "type": "String"
                        },
                        {
                            "name": "participantRef",
                            "type": "Participant",
                            "association": "A_partnerRoleRef_participantRef",
                            "isMany": true,
                            "isReference": true
                        }
                    ]
                },
                {
                    "name": "CorrelationProperty",
                    "superClass": [
                        "RootElement"
                    ],
                    "properties": [
                        {
                            "name": "correlationPropertyRetrievalExpression",
                            "type": "CorrelationPropertyRetrievalExpression",
                            "association": "A_correlationPropertyRetrievalExpression_correlationproperty",
                            "isMany": true
                        },
                        {
                            "name": "name",
                            "isAttr": true,
                            "type": "String"
                        },
                        {
                            "name": "type",
                            "type": "ItemDefinition",
                            "association": "A_type_correlationProperty",
                            "isAttr": true,
                            "isReference": true
                        }
                    ]
                },
                {
                    "name": "Error",
                    "superClass": [
                        "RootElement"
                    ],
                    "properties": [
                        {
                            "name": "structureRef",
                            "type": "ItemDefinition",
                            "association": "A_structureRef_error",
                            "isAttr": true,
                            "isReference": true
                        },
                        {
                            "name": "name",
                            "isAttr": true,
                            "type": "String"
                        },
                        {
                            "name": "errorCode",
                            "isAttr": true,
                            "type": "String"
                        }
                    ]
                },
                {
                    "name": "CorrelationKey",
                    "superClass": [
                        "BaseElement"
                    ],
                    "properties": [
                        {
                            "name": "correlationPropertyRef",
                            "type": "CorrelationProperty",
                            "association": "A_correlationPropertyRef_correlationKey",
                            "isMany": true,
                            "isReference": true
                        },
                        {
                            "name": "name",
                            "isAttr": true,
                            "type": "String"
                        }
                    ]
                },
                {
                    "name": "Expression",
                    "superClass": [
                        "BaseElement"
                    ]
                },
                {
                    "name": "FormalExpression",
                    "superClass": [
                        "Expression"
                    ],
                    "properties": [
                        {
                            "name": "language",
                            "isAttr": true,
                            "type": "String"
                        },
                        {
                            "name": "body",
                            "type": "Element"
                        },
                        {
                            "name": "evaluatesToTypeRef",
                            "type": "ItemDefinition",
                            "association": "A_evaluatesToTypeRef_formalExpression",
                            "isAttr": true,
                            "isReference": true
                        }
                    ]
                },
                {
                    "name": "Message",
                    "superClass": [
                        "RootElement"
                    ],
                    "properties": [
                        {
                            "name": "name",
                            "isAttr": true,
                            "type": "String"
                        },
                        {
                            "name": "itemRef",
                            "type": "ItemDefinition",
                            "association": "A_itemRef_message",
                            "isAttr": true,
                            "isReference": true
                        }
                    ]
                },
                {
                    "name": "ItemDefinition",
                    "superClass": [
                        "RootElement"
                    ],
                    "properties": [
                        {
                            "name": "itemKind",
                            "type": "ItemKind",
                            "isAttr": true
                        },
                        {
                            "name": "structureRef",
                            "type": "String",
                            "isAttr": true
                        },
                        {
                            "name": "isCollection",
                            "default": false,
                            "isAttr": true,
                            "type": "Boolean"
                        },
                        {
                            "name": "import",
                            "type": "Import",
                            "association": "A_import_itemDefinition",
                            "isAttr": true,
                            "isReference": true
                        }
                    ]
                },
                {
                    "name": "FlowElement",
                    "isAbstract": true,
                    "superClass": [
                        "BaseElement"
                    ],
                    "properties": [
                        {
                            "name": "name",
                            "isAttr": true,
                            "type": "String"
                        },
                        {
                            "name": "auditing",
                            "type": "Auditing",
                            "association": "A_auditing_flowElement"
                        },
                        {
                            "name": "monitoring",
                            "type": "Monitoring",
                            "association": "A_monitoring_flowElement"
                        },
                        {
                            "name": "categoryValueRef",
                            "type": "CategoryValue",
                            "association": "A_categorizedFlowElements_categoryValueRef",
                            "isMany": true,
                            "isReference": true
                        }
                    ]
                },
                {
                    "name": "SequenceFlow",
                    "superClass": [
                        "FlowElement"
                    ],
                    "properties": [
                        {
                            "name": "isImmediate",
                            "isAttr": true,
                            "type": "Boolean"
                        },
                        {
                            "name": "conditionExpression",
                            "type": "Expression",
                            "association": "A_conditionExpression_sequenceFlow"
                        },
                        {
                            "name": "sourceRef",
                            "type": "FlowNode",
                            "association": "A_sourceRef_outgoing_flow",
                            "isAttr": true,
                            "isReference": true
                        },
                        {
                            "name": "targetRef",
                            "type": "FlowNode",
                            "association": "A_targetRef_incoming_flow",
                            "isAttr": true,
                            "isReference": true
                        }
                    ]
                },
                {
                    "name": "FlowElementsContainer",
                    "isAbstract": true,
                    "superClass": [
                        "BaseElement"
                    ],
                    "properties": [
                        {
                            "name": "laneSets",
                            "type": "LaneSet",
                            "association": "A_laneSets_flowElementsContainer",
                            "isMany": true
                        },
                        {
                            "name": "flowElements",
                            "type": "FlowElement",
                            "association": "A_flowElements_container",
                            "isMany": true
                        }
                    ]
                },
                {
                    "name": "CallableElement",
                    "isAbstract": true,
                    "superClass": [
                        "RootElement"
                    ],
                    "properties": [
                        {
                            "name": "name",
                            "isAttr": true,
                            "type": "String"
                        },
                        {
                            "name": "ioSpecification",
                            "type": "InputOutputSpecification",
                            "association": "A_ioSpecification_callableElement"
                        },
                        {
                            "name": "supportedInterfaceRefs",
                            "type": "Interface",
                            "association": "A_supportedInterfaceRefs_callableElements",
                            "isMany": true,
                            "isReference": true
                        },
                        {
                            "name": "ioBinding",
                            "type": "InputOutputBinding",
                            "association": "A_ioBinding_callableElement",
                            "isMany": true
                        }
                    ]
                },
                {
                    "name": "FlowNode",
                    "isAbstract": true,
                    "superClass": [
                        "FlowElement"
                    ],
                    "properties": [
                        {
                            "name": "incoming",
                            "type": "SequenceFlow",
                            "association": "A_targetRef_incoming_flow",
                            "isMany": true,
                            "isReference": true
                        },
                        {
                            "name": "outgoing",
                            "type": "SequenceFlow",
                            "association": "A_sourceRef_outgoing_flow",
                            "isMany": true,
                            "isReference": true
                        },
                        {
                            "name": "lanes",
                            "type": "Lane",
                            "association": "A_flowNodeRefs_lanes",
                            "isVirtual": true,
                            "isMany": true,
                            "isReference": true
                        }
                    ]
                },
                {
                    "name": "CorrelationPropertyRetrievalExpression",
                    "superClass": [
                        "BaseElement"
                    ],
                    "properties": [
                        {
                            "name": "messagePath",
                            "type": "FormalExpression",
                            "association": "A_messagePath_correlationset"
                        },
                        {
                            "name": "messageRef",
                            "type": "Message",
                            "association": "A_messageRef_correlationPropertyRetrievalExpression",
                            "isAttr": true,
                            "isReference": true
                        }
                    ]
                },
                {
                    "name": "CorrelationPropertyBinding",
                    "superClass": [
                        "BaseElement"
                    ],
                    "properties": [
                        {
                            "name": "dataPath",
                            "type": "FormalExpression",
                            "association": "A_dataPath_correlationPropertyBinding"
                        },
                        {
                            "name": "correlationPropertyRef",
                            "type": "CorrelationProperty",
                            "association": "A_correlationPropertyRef_correlationPropertyBinding",
                            "isAttr": true,
                            "isReference": true
                        }
                    ]
                },
                {
                    "name": "Resource",
                    "superClass": [
                        "RootElement"
                    ],
                    "properties": [
                        {
                            "name": "name",
                            "isAttr": true,
                            "type": "String"
                        },
                        {
                            "name": "resourceParameters",
                            "type": "ResourceParameter",
                            "association": "A_resourceParameters_resource",
                            "isMany": true
                        }
                    ]
                },
                {
                    "name": "ResourceParameter",
                    "superClass": [
                        "BaseElement"
                    ],
                    "properties": [
                        {
                            "name": "name",
                            "isAttr": true,
                            "type": "String"
                        },
                        {
                            "name": "isRequired",
                            "isAttr": true,
                            "type": "Boolean"
                        },
                        {
                            "name": "type",
                            "type": "ItemDefinition",
                            "association": "A_type_resourceParameter",
                            "isAttr": true,
                            "isReference": true
                        }
                    ]
                },
                {
                    "name": "CorrelationSubscription",
                    "superClass": [
                        "BaseElement"
                    ],
                    "properties": [
                        {
                            "name": "correlationKeyRef",
                            "type": "CorrelationKey",
                            "association": "A_correlationKeyRef_correlationSubscription",
                            "isAttr": true,
                            "isReference": true
                        },
                        {
                            "name": "correlationPropertyBinding",
                            "type": "CorrelationPropertyBinding",
                            "association": "A_correlationPropertyBinding_correlationSubscription",
                            "isMany": true
                        }
                    ]
                },
                {
                    "name": "MessageFlow",
                    "superClass": [
                        "BaseElement"
                    ],
                    "properties": [
                        {
                            "name": "name",
                            "isAttr": true,
                            "type": "String"
                        },
                        {
                            "name": "sourceRef",
                            "type": "InteractionNode",
                            "association": "A_sourceRef_messageFlow",
                            "isAttr": true,
                            "isReference": true
                        },
                        {
                            "name": "targetRef",
                            "type": "InteractionNode",
                            "association": "A_targetRef_messageFlow",
                            "isAttr": true,
                            "isReference": true
                        },
                        {
                            "name": "messageRef",
                            "type": "Message",
                            "association": "A_messageRef_messageFlow",
                            "isAttr": true,
                            "isReference": true
                        }
                    ]
                },
                {
                    "name": "MessageFlowAssociation",
                    "superClass": [
                        "BaseElement"
                    ],
                    "properties": [
                        {
                            "name": "innerMessageFlowRef",
                            "type": "MessageFlow",
                            "association": "A_innerMessageFlowRef_messageFlowAssociation",
                            "isAttr": true,
                            "isReference": true
                        },
                        {
                            "name": "outerMessageFlowRef",
                            "type": "MessageFlow",
                            "association": "A_outerMessageFlowRef_messageFlowAssociation",
                            "isAttr": true,
                            "isReference": true
                        }
                    ]
                },
                {
                    "name": "InteractionNode",
                    "isAbstract": true,
                    "properties": [
                        {
                            "name": "incomingConversationLinks",
                            "type": "ConversationLink",
                            "association": "A_targetRef_incomingConversationLinks",
                            "isVirtual": true,
                            "isMany": true,
                            "isReference": true
                        },
                        {
                            "name": "outgoingConversationLinks",
                            "type": "ConversationLink",
                            "association": "A_sourceRef_outgoingConversationLinks",
                            "isVirtual": true,
                            "isMany": true,
                            "isReference": true
                        }
                    ]
                },
                {
                    "name": "Participant",
                    "superClass": [
                        "InteractionNode",
                        "BaseElement"
                    ],
                    "properties": [
                        {
                            "name": "name",
                            "isAttr": true,
                            "type": "String"
                        },
                        {
                            "name": "interfaceRefs",
                            "type": "Interface",
                            "association": "A_interfaceRefs_participant",
                            "isMany": true,
                            "isReference": true
                        },
                        {
                            "name": "participantMultiplicity",
                            "type": "ParticipantMultiplicity",
                            "association": "A_participantMultiplicity_participant"
                        },
                        {
                            "name": "endPointRefs",
                            "type": "EndPoint",
                            "association": "A_endPointRefs_participant",
                            "isMany": true,
                            "isReference": true
                        },
                        {
                            "name": "processRef",
                            "type": "Process",
                            "association": "A_processRef_participant",
                            "isAttr": true,
                            "isReference": true
                        }
                    ]
                },
                {
                    "name": "ParticipantAssociation",
                    "superClass": [
                        "BaseElement"
                    ],
                    "properties": [
                        {
                            "name": "innerParticipantRef",
                            "type": "Participant",
                            "association": "A_innerParticipantRef_participantAssociation",
                            "isAttr": true,
                            "isReference": true
                        },
                        {
                            "name": "outerParticipantRef",
                            "type": "Participant",
                            "association": "A_outerParticipantRef_participantAssociation",
                            "isAttr": true,
                            "isReference": true
                        }
                    ]
                },
                {
                    "name": "ParticipantMultiplicity",
                    "properties": [
                        {
                            "name": "minimum",
                            "default": 0,
                            "isAttr": true,
                            "type": "Integer"
                        },
                        {
                            "name": "maximum",
                            "default": 1,
                            "isAttr": true,
                            "type": "Integer"
                        }
                    ]
                },
                {
                    "name": "Collaboration",
                    "superClass": [
                        "RootElement"
                    ],
                    "properties": [
                        {
                            "name": "name",
                            "isAttr": true,
                            "type": "String"
                        },
                        {
                            "name": "isClosed",
                            "isAttr": true,
                            "type": "Boolean"
                        },
                        {
                            "name": "choreographyRef",
                            "type": "Choreography",
                            "association": "A_choreographyRef_collaboration",
                            "isMany": true,
                            "isReference": true
                        },
                        {
                            "name": "artifacts",
                            "type": "Artifact",
                            "association": "A_artifacts_collaboration",
                            "isMany": true
                        },
                        {
                            "name": "participantAssociations",
                            "type": "ParticipantAssociation",
                            "association": "A_participantAssociations_collaboration",
                            "isMany": true
                        },
                        {
                            "name": "messageFlowAssociations",
                            "type": "MessageFlowAssociation",
                            "association": "A_messageFlowAssociations_collaboration",
                            "isMany": true
                        },
                        {
                            "name": "conversationAssociations",
                            "type": "ConversationAssociation",
                            "association": "A_conversationAssociations_converstaionAssociations"
                        },
                        {
                            "name": "participants",
                            "type": "Participant",
                            "association": "A_participants_collaboration",
                            "isMany": true
                        },
                        {
                            "name": "messageFlows",
                            "type": "MessageFlow",
                            "association": "A_messageFlows_collaboration",
                            "isMany": true
                        },
                        {
                            "name": "correlationKeys",
                            "type": "CorrelationKey",
                            "association": "A_correlationKeys_collaboration",
                            "isMany": true
                        },
                        {
                            "name": "conversations",
                            "type": "ConversationNode",
                            "association": "A_conversations_collaboration",
                            "isMany": true
                        },
                        {
                            "name": "conversationLinks",
                            "type": "ConversationLink",
                            "association": "A_conversationLinks_collaboration",
                            "isMany": true
                        }
                    ]
                },
                {
                    "name": "ChoreographyActivity",
                    "isAbstract": true,
                    "superClass": [
                        "FlowNode"
                    ],
                    "properties": [
                        {
                            "name": "participantRefs",
                            "type": "Participant",
                            "association": "A_participantRefs_choreographyActivity",
                            "isMany": true,
                            "isReference": true
                        },
                        {
                            "name": "initiatingParticipantRef",
                            "type": "Participant",
                            "association": "A_initiatingParticipantRef_choreographyActivity",
                            "isAttr": true,
                            "isReference": true
                        },
                        {
                            "name": "correlationKeys",
                            "type": "CorrelationKey",
                            "association": "A_correlationKeys_choreographyActivity",
                            "isMany": true
                        },
                        {
                            "name": "loopType",
                            "type": "ChoreographyLoopType",
                            "default": "None",
                            "isAttr": true
                        }
                    ]
                },
                {
                    "name": "CallChoreography",
                    "superClass": [
                        "ChoreographyActivity"
                    ],
                    "properties": [
                        {
                            "name": "calledChoreographyRef",
                            "type": "Choreography",
                            "association": "A_calledChoreographyRef_callChoreographyActivity",
                            "isAttr": true,
                            "isReference": true
                        },
                        {
                            "name": "participantAssociations",
                            "type": "ParticipantAssociation",
                            "association": "A_participantAssociations_callChoreographyActivity",
                            "isMany": true
                        }
                    ]
                },
                {
                    "name": "SubChoreography",
                    "superClass": [
                        "ChoreographyActivity",
                        "FlowElementsContainer"
                    ],
                    "properties": [
                        {
                            "name": "artifacts",
                            "type": "Artifact",
                            "association": "A_artifacts_subChoreography",
                            "isMany": true
                        }
                    ]
                },
                {
                    "name": "ChoreographyTask",
                    "superClass": [
                        "ChoreographyActivity"
                    ],
                    "properties": [
                        {
                            "name": "messageFlowRef",
                            "type": "MessageFlow",
                            "association": "A_messageFlowRef_choreographyTask",
                            "isMany": true,
                            "isReference": true
                        }
                    ]
                },
                {
                    "name": "Choreography",
                    "superClass": [
                        "FlowElementsContainer",
                        "Collaboration"
                    ]
                },
                {
                    "name": "GlobalChoreographyTask",
                    "superClass": [
                        "Choreography"
                    ],
                    "properties": [
                        {
                            "name": "initiatingParticipantRef",
                            "type": "Participant",
                            "association": "A_initiatingParticipantRef_globalChoreographyTask",
                            "isAttr": true,
                            "isReference": true
                        }
                    ]
                },
                {
                    "name": "TextAnnotation",
                    "superClass": [
                        "Artifact"
                    ],
                    "properties": [
                        {
                            "name": "text",
                            "type": "String"
                        },
                        {
                            "name": "textFormat",
                            "default": "text/plain",
                            "isAttr": true,
                            "type": "String"
                        }
                    ]
                },
                {
                    "name": "Group",
                    "superClass": [
                        "Artifact"
                    ],
                    "properties": [
                        {
                            "name": "categoryValueRef",
                            "type": "CategoryValue",
                            "association": "A_categoryValueRef_categoryValueRef",
                            "isAttr": true,
                            "isReference": true
                        }
                    ]
                },
                {
                    "name": "Association",
                    "superClass": [
                        "Artifact"
                    ],
                    "properties": [
                        {
                            "name": "associationDirection",
                            "type": "AssociationDirection",
                            "isAttr": true
                        },
                        {
                            "name": "sourceRef",
                            "type": "BaseElement",
                            "association": "A_sourceRef_outgoing_association",
                            "isAttr": true,
                            "isReference": true
                        },
                        {
                            "name": "targetRef",
                            "type": "BaseElement",
                            "association": "A_targetRef_incoming_association",
                            "isAttr": true,
                            "isReference": true
                        }
                    ]
                },
                {
                    "name": "Category",
                    "superClass": [
                        "RootElement"
                    ],
                    "properties": [
                        {
                            "name": "categoryValue",
                            "type": "CategoryValue",
                            "association": "A_categoryValue_category",
                            "isMany": true
                        },
                        {
                            "name": "name",
                            "isAttr": true,
                            "type": "String"
                        }
                    ]
                },
                {
                    "name": "Artifact",
                    "isAbstract": true,
                    "superClass": [
                        "BaseElement"
                    ]
                },
                {
                    "name": "CategoryValue",
                    "superClass": [
                        "BaseElement"
                    ],
                    "properties": [
                        {
                            "name": "categorizedFlowElements",
                            "type": "FlowElement",
                            "association": "A_categorizedFlowElements_categoryValueRef",
                            "isVirtual": true,
                            "isMany": true,
                            "isReference": true
                        },
                        {
                            "name": "value",
                            "isAttr": true,
                            "type": "String"
                        }
                    ]
                },
                {
                    "name": "Activity",
                    "isAbstract": true,
                    "superClass": [
                        "FlowNode"
                    ],
                    "properties": [
                        {
                            "name": "isForCompensation",
                            "default": false,
                            "isAttr": true,
                            "type": "Boolean"
                        },
                        {
                            "name": "loopCharacteristics",
                            "type": "LoopCharacteristics",
                            "association": "A_loopCharacteristics_activity"
                        },
                        {
                            "name": "resources",
                            "type": "ResourceRole",
                            "association": "A_resources_activity",
                            "isMany": true
                        },
                        {
                            "name": "default",
                            "type": "SequenceFlow",
                            "association": "A_default_activity",
                            "isAttr": true,
                            "isReference": true
                        },
                        {
                            "name": "properties",
                            "type": "Property",
                            "association": "A_properties_activity",
                            "isMany": true
                        },
                        {
                            "name": "ioSpecification",
                            "type": "InputOutputSpecification",
                            "association": "A_ioSpecification_activity"
                        },
                        {
                            "name": "boundaryEventRefs",
                            "type": "BoundaryEvent",
                            "association": "A_boundaryEventRefs_attachedToRef",
                            "isMany": true,
                            "isReference": true
                        },
                        {
                            "name": "dataInputAssociations",
                            "type": "DataInputAssociation",
                            "association": "A_dataInputAssociations_activity",
                            "isMany": true
                        },
                        {
                            "name": "dataOutputAssociations",
                            "type": "DataOutputAssociation",
                            "association": "A_dataOutputAssociations_activity",
                            "isMany": true
                        },
                        {
                            "name": "startQuantity",
                            "default": 1,
                            "isAttr": true,
                            "type": "Integer"
                        },
                        {
                            "name": "completionQuantity",
                            "default": 1,
                            "isAttr": true,
                            "type": "Integer"
                        }
                    ]
                },
                {
                    "name": "ServiceTask",
                    "superClass": [
                        "Task"
                    ],
                    "properties": [
                        {
                            "name": "implementation",
                            "isAttr": true,
                            "type": "String"
                        },
                        {
                            "name": "operationRef",
                            "type": "Operation",
                            "association": "A_operationRef_serviceTask",
                            "isAttr": true,
                            "isReference": true
                        }
                    ]
                },
                {
                    "name": "SubProcess",
                    "superClass": [
                        "Activity",
                        "FlowElementsContainer"
                    ],
                    "properties": [
                        {
                            "name": "triggeredByEvent",
                            "default": false,
                            "isAttr": true,
                            "type": "Boolean"
                        },
                        {
                            "name": "artifacts",
                            "type": "Artifact",
                            "association": "A_artifacts_subProcess",
                            "isMany": true
                        }
                    ]
                },
                {
                    "name": "LoopCharacteristics",
                    "isAbstract": true,
                    "superClass": [
                        "BaseElement"
                    ]
                },
                {
                    "name": "MultiInstanceLoopCharacteristics",
                    "superClass": [
                        "LoopCharacteristics"
                    ],
                    "properties": [
                        {
                            "name": "isSequential",
                            "default": false,
                            "isAttr": true,
                            "type": "Boolean"
                        },
                        {
                            "name": "behavior",
                            "type": "MultiInstanceBehavior",
                            "default": "All",
                            "isAttr": true
                        },
                        {
                            "name": "loopCardinality",
                            "type": "Expression",
                            "association": "A_loopCardinality_multiInstanceLoopCharacteristics"
                        },
                        {
                            "name": "loopDataInputRef",
                            "type": "ItemAwareElement",
                            "association": "A_loopDataInputRef_multiInstanceLoopCharacteristics",
                            "isAttr": true,
                            "isReference": true
                        },
                        {
                            "name": "loopDataOutputRef",
                            "type": "ItemAwareElement",
                            "association": "A_loopDataOutputRef_multiInstanceLoopCharacteristics",
                            "isAttr": true,
                            "isReference": true
                        },
                        {
                            "name": "inputDataItem",
                            "type": "DataInput",
                            "association": "A_inputDataItem_multiInstanceLoopCharacteristics"
                        },
                        {
                            "name": "outputDataItem",
                            "type": "DataOutput",
                            "association": "A_outputDataItem_multiInstanceLoopCharacteristics"
                        },
                        {
                            "name": "completionCondition",
                            "type": "Expression",
                            "association": "A_completionCondition_multiInstanceLoopCharacteristics"
                        },
                        {
                            "name": "complexBehaviorDefinition",
                            "type": "ComplexBehaviorDefinition",
                            "association": "A_complexBehaviorDefinition_multiInstanceLoopCharacteristics",
                            "isMany": true
                        },
                        {
                            "name": "oneBehaviorEventRef",
                            "type": "EventDefinition",
                            "association": "A_oneBehaviorEventRef_multiInstanceLoopCharacteristics",
                            "isAttr": true,
                            "isReference": true
                        },
                        {
                            "name": "noneBehaviorEventRef",
                            "type": "EventDefinition",
                            "association": "A_noneBehaviorEventRef_multiInstanceLoopCharacteristics",
                            "isAttr": true,
                            "isReference": true
                        }
                    ]
                },
                {
                    "name": "StandardLoopCharacteristics",
                    "superClass": [
                        "LoopCharacteristics"
                    ],
                    "properties": [
                        {
                            "name": "testBefore",
                            "default": false,
                            "isAttr": true,
                            "type": "Boolean"
                        },
                        {
                            "name": "loopCondition",
                            "type": "Expression",
                            "association": "A_loopCondition_standardLoopCharacteristics"
                        },
                        {
                            "name": "loopMaximum",
                            "type": "Expression",
                            "association": "A_loopMaximum_standardLoopCharacteristics"
                        }
                    ]
                },
                {
                    "name": "CallActivity",
                    "superClass": [
                        "Activity"
                    ],
                    "properties": [
                        {
                            "name": "calledElement",
                            "type": "String",
                            "association": "A_calledElementRef_callActivity",
                            "isAttr": true
                        }
                    ]
                },
                {
                    "name": "Task",
                    "superClass": [
                        "Activity",
                        "InteractionNode"
                    ]
                },
                {
                    "name": "SendTask",
                    "superClass": [
                        "Task"
                    ],
                    "properties": [
                        {
                            "name": "implementation",
                            "isAttr": true,
                            "type": "String"
                        },
                        {
                            "name": "operationRef",
                            "type": "Operation",
                            "association": "A_operationRef_sendTask",
                            "isAttr": true,
                            "isReference": true
                        },
                        {
                            "name": "messageRef",
                            "type": "Message",
                            "association": "A_messageRef_sendTask",
                            "isAttr": true,
                            "isReference": true
                        }
                    ]
                },
                {
                    "name": "ReceiveTask",
                    "superClass": [
                        "Task"
                    ],
                    "properties": [
                        {
                            "name": "implementation",
                            "isAttr": true,
                            "type": "String"
                        },
                        {
                            "name": "instantiate",
                            "default": false,
                            "isAttr": true,
                            "type": "Boolean"
                        },
                        {
                            "name": "operationRef",
                            "type": "Operation",
                            "association": "A_operationRef_receiveTask",
                            "isAttr": true,
                            "isReference": true
                        },
                        {
                            "name": "messageRef",
                            "type": "Message",
                            "association": "A_messageRef_receiveTask",
                            "isAttr": true,
                            "isReference": true
                        }
                    ]
                },
                {
                    "name": "ScriptTask",
                    "superClass": [
                        "Task"
                    ],
                    "properties": [
                        {
                            "name": "scriptFormat",
                            "isAttr": true,
                            "type": "String"
                        },
                        {
                            "name": "script",
                            "type": "String"
                        }
                    ]
                },
                {
                    "name": "BusinessRuleTask",
                    "superClass": [
                        "Task"
                    ],
                    "properties": [
                        {
                            "name": "implementation",
                            "isAttr": true,
                            "type": "String"
                        }
                    ]
                },
                {
                    "name": "AdHocSubProcess",
                    "superClass": [
                        "SubProcess"
                    ],
                    "properties": [
                        {
                            "name": "completionCondition",
                            "type": "Expression",
                            "association": "A_completionCondition_adHocSubProcess"
                        },
                        {
                            "name": "ordering",
                            "type": "AdHocOrdering",
                            "isAttr": true
                        },
                        {
                            "name": "cancelRemainingInstances",
                            "default": true,
                            "isAttr": true,
                            "type": "Boolean"
                        }
                    ]
                },
                {
                    "name": "Transaction",
                    "superClass": [
                        "SubProcess"
                    ],
                    "properties": [
                        {
                            "name": "protocol",
                            "isAttr": true,
                            "type": "String"
                        },
                        {
                            "name": "method",
                            "isAttr": true,
                            "type": "String"
                        }
                    ]
                },
                {
                    "name": "GlobalScriptTask",
                    "superClass": [
                        "GlobalTask"
                    ],
                    "properties": [
                        {
                            "name": "scriptLanguage",
                            "isAttr": true,
                            "type": "String"
                        },
                        {
                            "name": "script",
                            "isAttr": true,
                            "type": "String"
                        }
                    ]
                },
                {
                    "name": "GlobalBusinessRuleTask",
                    "superClass": [
                        "GlobalTask"
                    ],
                    "properties": [
                        {
                            "name": "implementation",
                            "isAttr": true,
                            "type": "String"
                        }
                    ]
                },
                {
                    "name": "ComplexBehaviorDefinition",
                    "superClass": [
                        "BaseElement"
                    ],
                    "properties": [
                        {
                            "name": "condition",
                            "type": "FormalExpression",
                            "association": "A_condition_complexBehaviorDefinition"
                        },
                        {
                            "name": "event",
                            "type": "ImplicitThrowEvent",
                            "association": "A_event_complexBehaviorDefinition"
                        }
                    ]
                },
                {
                    "name": "ResourceRole",
                    "superClass": [
                        "BaseElement"
                    ],
                    "properties": [
                        {
                            "name": "resourceRef",
                            "type": "Resource",
                            "association": "A_resourceRef_activityResource",
                            "isAttr": true,
                            "isReference": true
                        },
                        {
                            "name": "resourceParameterBindings",
                            "type": "ResourceParameterBinding",
                            "association": "A_resourceParameterBindings_activityResource",
                            "isMany": true
                        },
                        {
                            "name": "resourceAssignmentExpression",
                            "type": "ResourceAssignmentExpression",
                            "association": "A_resourceAssignmentExpression_activityResource"
                        },
                        {
                            "name": "name",
                            "isAttr": true,
                            "type": "String"
                        }
                    ]
                },
                {
                    "name": "ResourceParameterBinding",
                    "properties": [
                        {
                            "name": "expression",
                            "type": "Expression",
                            "association": "A_expression_resourceParameterBinding"
                        },
                        {
                            "name": "parameterRef",
                            "type": "ResourceParameter",
                            "association": "A_parameterRef_resourceParameterBinding",
                            "isAttr": true,
                            "isReference": true
                        }
                    ]
                },
                {
                    "name": "ResourceAssignmentExpression",
                    "properties": [
                        {
                            "name": "expression",
                            "type": "Expression",
                            "association": "A_expression_resourceAssignmentExpression"
                        }
                    ]
                },
                {
                    "name": "Import",
                    "properties": [
                        {
                            "name": "importType",
                            "isAttr": true,
                            "type": "String"
                        },
                        {
                            "name": "location",
                            "isAttr": true,
                            "type": "String"
                        },
                        {
                            "name": "namespace",
                            "isAttr": true,
                            "type": "String"
                        }
                    ]
                },
                {
                    "name": "Definitions",
                    "superClass": [
                        "BaseElement"
                    ],
                    "properties": [
                        {
                            "name": "name",
                            "isAttr": true,
                            "type": "String"
                        },
                        {
                            "name": "targetNamespace",
                            "isAttr": true,
                            "type": "String"
                        },
                        {
                            "name": "expressionLanguage",
                            "default": "http://www.w3.org/1999/XPath",
                            "isAttr": true,
                            "type": "String"
                        },
                        {
                            "name": "typeLanguage",
                            "default": "http://www.w3.org/2001/XMLSchema",
                            "isAttr": true,
                            "type": "String"
                        },
                        {
                            "name": "imports",
                            "type": "Import",
                            "association": "A_imports_definition",
                            "isMany": true
                        },
                        {
                            "name": "extensions",
                            "type": "Extension",
                            "association": "A_extensions_definitions",
                            "isMany": true
                        },
                        {
                            "name": "relationships",
                            "type": "Relationship",
                            "association": "A_relationships_definition",
                            "isMany": true
                        },
                        {
                            "name": "rootElements",
                            "type": "RootElement",
                            "association": "A_rootElements_definition",
                            "isMany": true
                        },
                        {
                            "name": "diagrams",
                            "association": "A_diagrams_definitions",
                            "isMany": true,
                            "type": "bpmndi:BPMNDiagram"
                        },
                        {
                            "name": "exporter",
                            "isAttr": true,
                            "type": "String"
                        },
                        {
                            "name": "exporterVersion",
                            "isAttr": true,
                            "type": "String"
                        }
                    ]
                }
            ],
            "emumerations": [
                {
                    "name": "ProcessType",
                    "literalValues": [
                        {
                            "name": "None"
                        },
                        {
                            "name": "Public"
                        },
                        {
                            "name": "Private"
                        }
                    ]
                },
                {
                    "name": "GatewayDirection",
                    "literalValues": [
                        {
                            "name": "Unspecified"
                        },
                        {
                            "name": "Converging"
                        },
                        {
                            "name": "Diverging"
                        },
                        {
                            "name": "Mixed"
                        }
                    ]
                },
                {
                    "name": "EventBasedGatewayType",
                    "literalValues": [
                        {
                            "name": "Parallel"
                        },
                        {
                            "name": "Exclusive"
                        }
                    ]
                },
                {
                    "name": "RelationshipDirection",
                    "literalValues": [
                        {
                            "name": "None"
                        },
                        {
                            "name": "Forward"
                        },
                        {
                            "name": "Backward"
                        },
                        {
                            "name": "Both"
                        }
                    ]
                },
                {
                    "name": "ItemKind",
                    "literalValues": [
                        {
                            "name": "Physical"
                        },
                        {
                            "name": "Information"
                        }
                    ]
                },
                {
                    "name": "ChoreographyLoopType",
                    "literalValues": [
                        {
                            "name": "None"
                        },
                        {
                            "name": "Standard"
                        },
                        {
                            "name": "MultiInstanceSequential"
                        },
                        {
                            "name": "MultiInstanceParallel"
                        }
                    ]
                },
                {
                    "name": "AssociationDirection",
                    "literalValues": [
                        {
                            "name": "None"
                        },
                        {
                            "name": "One"
                        },
                        {
                            "name": "Both"
                        }
                    ]
                },
                {
                    "name": "MultiInstanceBehavior",
                    "literalValues": [
                        {
                            "name": "None"
                        },
                        {
                            "name": "One"
                        },
                        {
                            "name": "All"
                        },
                        {
                            "name": "Complex"
                        }
                    ]
                },
                {
                    "name": "AdHocOrdering",
                    "literalValues": [
                        {
                            "name": "Parallel"
                        },
                        {
                            "name": "Sequential"
                        }
                    ]
                }
            ],
            "prefix": "bpmn",
            "xml": {
                "alias": "lowerCase"
            }
        },
        bpmndi: {
            "name": "BPMNDI",
            "uri": "http://www.omg.org/spec/BPMN/20100524/DI",
            "types": [
                {
                    "name": "BPMNDiagram",
                    "properties": [
                        {
                            "name": "plane",
                            "type": "BPMNPlane",
                            "association": "A_plane_diagram",
                            "redefines": "di:Diagram#rootElement"
                        },
                        {
                            "name": "labelStyle",
                            "type": "BPMNLabelStyle",
                            "association": "A_labelStyle_diagram",
                            "isMany": true
                        }
                    ],
                    "superClass": [
                        "di:Diagram"
                    ]
                },
                {
                    "name": "BPMNPlane",
                    "properties": [
                        {
                            "name": "bpmnElement",
                            "association": "A_bpmnElement_plane",
                            "isAttr": true,
                            "isReference": true,
                            "type": "bpmn:BaseElement",
                            "redefines": "di:DiagramElement#modelElement"
                        }
                    ],
                    "superClass": [
                        "di:Plane"
                    ]
                },
                {
                    "name": "BPMNShape",
                    "properties": [
                        {
                            "name": "bpmnElement",
                            "association": "A_bpmnElement_shape",
                            "isAttr": true,
                            "isReference": true,
                            "type": "bpmn:BaseElement",
                            "redefines": "di:DiagramElement#modelElement"
                        },
                        {
                            "name": "isHorizontal",
                            "isAttr": true,
                            "type": "Boolean"
                        },
                        {
                            "name": "isExpanded",
                            "isAttr": true,
                            "type": "Boolean"
                        },
                        {
                            "name": "isMarkerVisible",
                            "isAttr": true,
                            "type": "Boolean"
                        },
                        {
                            "name": "label",
                            "type": "BPMNLabel",
                            "association": "A_label_shape"
                        },
                        {
                            "name": "isMessageVisible",
                            "isAttr": true,
                            "type": "Boolean"
                        },
                        {
                            "name": "participantBandKind",
                            "type": "ParticipantBandKind",
                            "isAttr": true
                        },
                        {
                            "name": "choreographyActivityShape",
                            "type": "BPMNShape",
                            "association": "A_choreographyActivityShape_participantBandShape",
                            "isAttr": true,
                            "isReference": true
                        }
                    ],
                    "superClass": [
                        "di:LabeledShape"
                    ]
                },
                {
                    "name": "BPMNEdge",
                    "properties": [
                        {
                            "name": "label",
                            "type": "BPMNLabel",
                            "association": "A_label_edge"
                        },
                        {
                            "name": "bpmnElement",
                            "association": "A_bpmnElement_edge",
                            "isAttr": true,
                            "isReference": true,
                            "type": "bpmn:BaseElement",
                            "redefines": "di:DiagramElement#modelElement"
                        },
                        {
                            "name": "sourceElement",
                            "association": "A_sourceElement_sourceEdge",
                            "isAttr": true,
                            "isReference": true,
                            "type": "di:DiagramElement",
                            "redefines": "di:Edge#source"
                        },
                        {
                            "name": "targetElement",
                            "association": "A_targetElement_targetEdge",
                            "isAttr": true,
                            "isReference": true,
                            "type": "di:DiagramElement",
                            "redefines": "di:Edge#target"
                        },
                        {
                            "name": "messageVisibleKind",
                            "type": "MessageVisibleKind",
                            "isAttr": true,
                            "default": "initiating"
                        }
                    ],
                    "superClass": [
                        "di:LabeledEdge"
                    ]
                },
                {
                    "name": "BPMNLabel",
                    "properties": [
                        {
                            "name": "labelStyle",
                            "type": "BPMNLabelStyle",
                            "association": "A_labelStyle_label",
                            "isAttr": true,
                            "isReference": true,
                            "redefines": "di:DiagramElement#style"
                        }
                    ],
                    "superClass": [
                        "di:Label"
                    ]
                },
                {
                    "name": "BPMNLabelStyle",
                    "properties": [
                        {
                            "name": "font",
                            "type": "dc:Font"
                        }
                    ],
                    "superClass": [
                        "di:Style"
                    ]
                }
            ],
            "emumerations": [
                {
                    "name": "ParticipantBandKind",
                    "literalValues": [
                        {
                            "name": "top_initiating"
                        },
                        {
                            "name": "middle_initiating"
                        },
                        {
                            "name": "bottom_initiating"
                        },
                        {
                            "name": "top_non_initiating"
                        },
                        {
                            "name": "middle_non_initiating"
                        },
                        {
                            "name": "bottom_non_initiating"
                        }
                    ]
                },
                {
                    "name": "MessageVisibleKind",
                    "literalValues": [
                        {
                            "name": "initiating"
                        },
                        {
                            "name": "non_initiating"
                        }
                    ]
                }
            ],
            "associations": [],
            "prefix": "bpmndi"
        },
        dc: {
            "name": "DC",
            "uri": "http://www.omg.org/spec/DD/20100524/DC",
            "types": [
                {
                    "name": "Boolean"
                },
                {
                    "name": "Integer"
                },
                {
                    "name": "Real"
                },
                {
                    "name": "String"
                },
                {
                    "name": "Font",
                    "properties": [
                        {
                            "name": "name",
                            "type": "String",
                            "isAttr": true
                        },
                        {
                            "name": "size",
                            "type": "Real",
                            "isAttr": true
                        },
                        {
                            "name": "isBold",
                            "type": "Boolean",
                            "isAttr": true
                        },
                        {
                            "name": "isItalic",
                            "type": "Boolean",
                            "isAttr": true
                        },
                        {
                            "name": "isUnderline",
                            "type": "Boolean",
                            "isAttr": true
                        },
                        {
                            "name": "isStrikeThrough",
                            "type": "Boolean",
                            "isAttr": true
                        }
                    ]
                },
                {
                    "name": "Point",
                    "properties": [
                        {
                            "name": "x",
                            "type": "Real",
                            "default": "0",
                            "isAttr": true
                        },
                        {
                            "name": "y",
                            "type": "Real",
                            "default": "0",
                            "isAttr": true
                        }
                    ]
                },
                {
                    "name": "Bounds",
                    "properties": [
                        {
                            "name": "x",
                            "type": "Real",
                            "default": "0",
                            "isAttr": true
                        },
                        {
                            "name": "y",
                            "type": "Real",
                            "default": "0",
                            "isAttr": true
                        },
                        {
                            "name": "width",
                            "type": "Real",
                            "isAttr": true
                        },
                        {
                            "name": "height",
                            "type": "Real",
                            "isAttr": true
                        }
                    ]
                }
            ],
            "prefix": "dc",
            "associations": []
        },
        di: {
            "name": "DI",
            "uri": "http://www.omg.org/spec/DD/20100524/DI",
            "types": [
                {
                    "name": "DiagramElement",
                    "isAbstract": true,
                    "properties": [
                        {
                            "name": "owningDiagram",
                            "type": "Diagram",
                            "isReadOnly": true,
                            "association": "A_rootElement_owningDiagram",
                            "isVirtual": true,
                            "isReference": true
                        },
                        {
                            "name": "owningElement",
                            "type": "DiagramElement",
                            "isReadOnly": true,
                            "association": "A_ownedElement_owningElement",
                            "isVirtual": true,
                            "isReference": true
                        },
                        {
                            "name": "modelElement",
                            "isReadOnly": true,
                            "association": "A_modelElement_diagramElement",
                            "isVirtual": true,
                            "isReference": true,
                            "type": "Element"
                        },
                        {
                            "name": "style",
                            "type": "Style",
                            "isReadOnly": true,
                            "association": "A_style_diagramElement",
                            "isVirtual": true,
                            "isReference": true
                        },
                        {
                            "name": "ownedElement",
                            "type": "DiagramElement",
                            "isReadOnly": true,
                            "association": "A_ownedElement_owningElement",
                            "isVirtual": true,
                            "isMany": true
                        }
                    ]
                },
                {
                    "name": "Node",
                    "isAbstract": true,
                    "superClass": [
                        "DiagramElement"
                    ]
                },
                {
                    "name": "Edge",
                    "isAbstract": true,
                    "superClass": [
                        "DiagramElement"
                    ],
                    "properties": [
                        {
                            "name": "source",
                            "type": "DiagramElement",
                            "isReadOnly": true,
                            "association": "A_source_sourceEdge",
                            "isVirtual": true,
                            "isReference": true
                        },
                        {
                            "name": "target",
                            "type": "DiagramElement",
                            "isReadOnly": true,
                            "association": "A_target_targetEdge",
                            "isVirtual": true,
                            "isReference": true
                        },
                        {
                            "name": "waypoint",
                            "isUnique": false,
                            "isMany": true,
                            "type": "dc:Point",
                            "serialize": "xsi:type"
                        }
                    ]
                },
                {
                    "name": "Diagram",
                    "isAbstract": true,
                    "properties": [
                        {
                            "name": "rootElement",
                            "type": "DiagramElement",
                            "isReadOnly": true,
                            "association": "A_rootElement_owningDiagram",
                            "isVirtual": true
                        },
                        {
                            "name": "name",
                            "isAttr": true,
                            "type": "String"
                        },
                        {
                            "name": "documentation",
                            "isAttr": true,
                            "type": "String"
                        },
                        {
                            "name": "resolution",
                            "isAttr": true,
                            "type": "Real"
                        },
                        {
                            "name": "ownedStyle",
                            "type": "Style",
                            "isReadOnly": true,
                            "association": "A_ownedStyle_owningDiagram",
                            "isVirtual": true,
                            "isMany": true
                        }
                    ]
                },
                {
                    "name": "Shape",
                    "isAbstract": true,
                    "superClass": [
                        "Node"
                    ],
                    "properties": [
                        {
                            "name": "bounds",
                            "type": "dc:Bounds"
                        }
                    ]
                },
                {
                    "name": "Plane",
                    "isAbstract": true,
                    "superClass": [
                        "Node"
                    ],
                    "properties": [
                        {
                            "name": "planeElement",
                            "type": "DiagramElement",
                            "subsettedProperty": "DiagramElement-ownedElement",
                            "association": "A_planeElement_plane",
                            "isMany": true
                        }
                    ]
                },
                {
                    "name": "LabeledEdge",
                    "isAbstract": true,
                    "superClass": [
                        "Edge"
                    ],
                    "properties": [
                        {
                            "name": "ownedLabel",
                            "type": "Label",
                            "isReadOnly": true,
                            "subsettedProperty": "DiagramElement-ownedElement",
                            "association": "A_ownedLabel_owningEdge",
                            "isVirtual": true,
                            "isMany": true
                        }
                    ]
                },
                {
                    "name": "LabeledShape",
                    "isAbstract": true,
                    "superClass": [
                        "Shape"
                    ],
                    "properties": [
                        {
                            "name": "ownedLabel",
                            "type": "Label",
                            "isReadOnly": true,
                            "subsettedProperty": "DiagramElement-ownedElement",
                            "association": "A_ownedLabel_owningShape",
                            "isVirtual": true,
                            "isMany": true
                        }
                    ]
                },
                {
                    "name": "Label",
                    "isAbstract": true,
                    "superClass": [
                        "Node"
                    ],
                    "properties": [
                        {
                            "name": "bounds",
                            "type": "dc:Bounds"
                        }
                    ]
                },
                {
                    "name": "Style",
                    "isAbstract": true
                }
            ],
            "associations": [],
            "prefix": "di"
        }
    };
    
    Moddle.call(this, packages, options);
}

BpmnModdle.prototype = Object.create(Moddle.prototype);

//module.exports = BpmnModdle;


/**
 * Instantiates a BPMN model tree from a given xml string.
 *
 * @param {String}   xmlStr
 * @param {String}   [typeName]   name of the root element, defaults to 'bpmn:Definitions'
 * @param {Object}   [options]    options to pass to the underlying reader
 * @param {Function} done         callback that is invoked with (err, result, parseContext) once the import completes
 */
BpmnModdle.prototype.fromXML = function(xmlStr, typeName, options, done) {

    if (!_.isString(typeName)) {
        done = options;
        options = typeName;
        typeName = 'bpmn:Definitions';
    }

    if (_.isFunction(options)) {
        done = options;
        options = {};
    }

    var reader = new XMLReader(this, options);
    var rootHandler = reader.handler(typeName);

    reader.fromXML(xmlStr, rootHandler, done);
};


/**
 * Serializes a BPMN 2.0 object tree to XML.
 *
 * @param {String}   element    the root element, typically an instance of `bpmn:Definitions`
 * @param {Object}   [options]  to pass to the underlying writer
 * @param {Function} done       callback invoked with (err, xmlStr) once the import completes
 */
BpmnModdle.prototype.toXML = function(element, options, done) {

    if (_.isFunction(options)) {
        done = options;
        options = {};
    }

    var writer = new XMLWriter(options);
    try {
        var result = writer.toXML(element);
        done(null, result);
    } catch (e) {
        done(e);
    }
};