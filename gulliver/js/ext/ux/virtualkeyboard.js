Ext.ns('Ext.ux.plugins');
Ext.ux.plugins.VirtualKeyboard = Ext.extend ( Ext.util.Observable, {
	events: {},
	/**
	 * Init of plugin
	 * @param {Ext.Component} field
	 */
	init: function(field) {
		this.addEvents({
			'expand': true,
			'collapse': true
		});

		if(field.getXType() != 'textfield' && field.getXType() != 'textarea')
			return;
		
		this.component = field;
		field.virtualKeyboardPlugin = this;

		field.enableKeyEvents = true;
		field.on({
			'destroy': this.destroy,
			'focus': function(f){
				this.activeTarget = f;
			},
			'keypress': function(field, e){
				if(e.getKey() == e.ESC)
					field.collapseKeyboard();
			},
			scope: this
		});
		
		Ext.apply(field, {
			onRender: field.onRender.createSequence(function(ct, position) {
				this.wrap = this.el.wrap({cls: "x-form-field-wrap"});
				if(this.fieldLabel && this.keyboardConfig.showIcon) {
					var label = this.el.findParent('.x-form-element', 5, true) || this.el.findParent('.x-form-field-wrap', 5, true);
					
					this.virtualKeyboardIcon = label.createChild({
						cls:(this.keyboardIconCls || 'ux-virtualkeyboard-icon'),
						style:'width:16px; height:18px; position:absolute; left:0; top:0; display:block; background:transparent no-repeat scroll 0 2px;'
					});
					
					this.alignKeyboardIcon = function(){
						var el = this.el; 
						this.virtualKeyboardIcon.alignTo(el, 'tl-tr', [2, 0]);
					}
					//Redefine alignErrorIcon to move the errorIcon (if it exist) to the right of helpIcon
					if(this.alignErrorIcon) {
						this.alignErrorIcon = function() {
							this.errorIcon.alignTo(this.virtualKeyboardIcon, 'tl-tr', [2, 0]);
						}
					}
					
					this.on('resize', this.alignKeyboardIcon, this);
					
					this.virtualKeyboardIcon.on('click', function(e){
						if(this.disabled){
							return;
						}
						this.expandKeyboard();
						this.el.focus();
					}, this);
					
				}
			}), //end of onRender
			
			initKeyboard: function(){
				var cls = 'x-keyboard-container';
				
				this.keyboardContainer = new Ext.Layer({
					shadow: true,
					cls: [cls, this.virtualKeyboardClass].join(' '),
					constrain: false
				});
				
				this.keyboardContainer.setWidth(370);
				
				var config = this.keyboardConfig || {};
				config['showCloseButton'] = true;
				config['keyboardTarget'] = this;
				
				this.keyboard = new Ext.ux.VirtualKeyboard(config);
				this.keyboard.render(this.keyboardContainer);
				
				this.keyboard.getTBar().add('-', new Ext.Button({
					text: this.keyboardConfig.closeButtonText || 'Close',
					listeners: {
						'click': this.collapseKeyboard,
						scope: this
					},
					scope: this
				}));
			},
			
			collapseKeyboardIf : function(e){
				if( !e.within(this.wrap) && !e.within(this.keyboardContainer) && !this.keyboard.selectingLanguage ){
					this.collapseKeyboard();
				}
			},

			expandKeyboard : function(){
				if(this.isKeyboardExpanded() /*|| !this.hasFocus*/){
					return;
				}
				if(!this.keyboard)
					this.initKeyboard();
				this.keyboardContainer.alignTo(this.wrap, this.keyboardAlign || 'tl-bl?');
				this.keyboardContainer.show();
				Ext.getDoc().on('mousewheel', this.collapseKeyboardIf, this);
				Ext.getDoc().on('mousedown', this.collapseKeyboardIf, this);
				this.virtualKeyboardPlugin.fireEvent('expand', this);
			},

			collapseKeyboard : function(){
				if(!this.isKeyboardExpanded()){
					return;
				}
				this.keyboardContainer.hide();
				Ext.getDoc().un('mousewheel', this.collapseKeyboardIf, this);
				Ext.getDoc().un('mousedown', this.collapseKeyboardIf, this);
				this.virtualKeyboardPlugin.fireEvent('collapse', this);
			},

			
			isKeyboardExpanded : function(){
				return this.keyboardContainer && this.keyboardContainer.isVisible();
			}

		}); //end of Ext.apply
	}, // end of function init
	
	destroy: function(component){
		if(component){
			if(component.keyboard){
				component.keyboard.keyboard.remove();
				delete component.keyboard;
			}
			if(component.keyboardContainer){
				component.keyboardContainer.remove();
				delete component.keyboardContainer;
			}
		}
	},
	
	expand: function(){
		if(this.activeTarget){
			this.activeTarget.expandKeyboard();
		}
	}

}); // end of extend

﻿Ext.ns('Ext.ux');

/**
 * Licensed under GNU LESSER GENERAL PUBLIC LICENSE Version 3
 *
 * Inspired from: HTML Virtual Keyboard Interface Script - v1.11
 *   http://www.greywyvern.com/code/js/keyboard.html
 *   Copyright (c) 2008 - GreyWyvern
 *  Licenced for free distribution under the BSDL
 *   http://www.opensource.org/licenses/bsd-license.php

 *
 * @author Edouard Fattal <efattal@gmail.com>
 * @url http://efattal.fr/extjs/examples/virtualkeyboard
 */

/**
 * @class Ext.ux.VirtualKeyboard
 * @extends Ext.Component
 */ 
Ext.ux.VirtualKeyboard = Ext.extend(Ext.Component, {
	/* ***** Create keyboards **************************************** */
	keyboardTarget: null,
	languageSelection: false,
	numpad: true,
	keyCenter: 3,
	layoutDDK: {},
	shift: false,
	capslock: false,
	alternate: false,
	dead: false,
	deadKeysOn: false,
	Languages: {
		Arabic: [ // Arabic Keyboard
			[["\u0630", "\u0651 "], ["1", "!", "\u00a1", "\u00b9"], ["2", "@", "\u00b2"], ["3", "#", "\u00b3"], ["4", "$", "\u00a4", "\u00a3"], ["5", "%", "\u20ac"], ["6", "^", "\u00bc"], ["7", "&", "\u00bd"], ["8", "*", "\u00be"], ["9", "(", "\u2018"], ["0", ")", "\u2019"], ["-", "_", "\u00a5"], ["=", "+", "\u00d7", "\u00f7"], ["Bksp", "Bksp"]],
			[["Tab", "Tab"], ["\u0636", "\u064e"], ["\u0635", "\u064b"], ["\u062b", "\u064f"], ["\u0642", "\u064c"], ["\u0641", "\u0644"], ["\u063a", "\u0625"], ["\u0639", "\u2018"], ["\u0647", "\u00f7"], ["\u062e", "\u00d7"], ["\u062d", "\u061b"], ["\u062c", "\u003c"], ["\u062f", "\u003e"], ["\u005c", "\u007c"]],
			[["Caps", "Caps"], ["\u0634", "\u0650"], ["\u0633", "\u064d"], ["\u064a", "\u005d"], ["\u0628", "\u005b"], ["\u0644", "\u0644"], ["\u0627", "\u0623"], ["\u062a", "\u0640"], ["\u0646", "\u060c"], ["\u0645", "\u002f"], ["\u0643", "\u003a"], ["\u0637", "\u0022"], ["Enter", "Enter"]],
			[["Shift", "Shift"], ["\u0626", "\u007e"], ["\u0621", "\u0652"], ["\u0624", "\u007d"], ["\u0631", "\u007b"], ["\u0644", "\u0644"], ["\u0649", "\u0622"], ["\u0629", "\u2019"], ["\u0648", "\u002c"], ["\u0632", "\u002e"], ["\u0638", "\u061f"], ["Shift", "Shift"]],
			[[" ", " ", " ", " "], ["Alt", "Alt"]]
		],
		Belgian: [ // Belgian Standard Keyboard
			[["\u00b2", "\u00b3"], ["&", "1", "|"], ["\u00e9", "2", "@"], ['"', "3", "#"], ["'", "4"], ["(", "5"], ["\u00a7", "6", "^"], ["\u00e8", "7"], ["!", "8"], ["\u00e7", "9", "{"], ["\u00e0", "0", "}"], [")", "\u00b0"], ["-", "_"], ["Bksp", "Bksp"]],
			[["Tab", "Tab"], ["a", "A"], ["z", "Z"], ["e", "E", "\u20ac"], ["r", "R"], ["t", "T"], ["y", "Y"], ["u", "U"], ["i", "I"], ["o", "O"], ["p", "P"], ["\u005e", "\u00a8", "["], ["$", "*", "]"], ["Enter", "Enter"]],
			[["Caps", "Caps"], ["q", "Q"], ["s", "S"], ["d", "D"], ["f", "F"], ["g", "G"], ["h", "H"], ["j", "J"], ["k", "K"], ["l", "L"], ["m", "M"], ["\u00f9", "%", "\u00b4"], ["\u03bc", "\u00a3", "`"]],
			[["Shift", "Shift"], ["<", ">", "\\"], ["w", "W"], ["x", "X"], ["c", "C"], ["v", "V"], ["b", "B"], ["n", "N"], [",", "?"], [";", "."], [":", "/"], ["=", "+", "~"], ["Shift", "Shift"]],
			[[" ", " ", " ", " "], ["AltGr", "AltGr"]]
		],
		/*Burmese: [ // Burmese Keyboard
			[["\u1039`", "~"], ["\u1041", "\u100D"], ["\u1042", "\u100E"], ["\u1043", "\u100B"], ["\u1044", "\u1000\u103B\u1015\u103A"], ["\u1045", "%"], ["\u1046", "\u002F"], ["\u1047", "\u101B"], ["\u1048", "\u1002"], ["\u1049", "("], ["\u1040", ")"], ["-", "_"], ["=", "+"], ["Bksp", "Bksp"]],
			[["Tab", "Tab"], ["\u1006", "\u1029"], ["\u1010", "\u1040"], ["\u1014", "\u103F"], ["\u1019", "\u1023"], ["\u1021", "\u1024"], ["\u1015", "\u104C"], ["\u1000", "\u1009"], ["\u1004", "\u104D"], ["\u101E", "\u1025"], ["\u1005", "\u100F"], ["\u101F", "\u1027"], ["\u2018", "\u2019"], ["\u104F", "\u100B\u1039\u100C"]],
			[["Caps", "Caps"], ["\u200B\u1031", "\u1017"], ["\u200B\u103B", "\u200B\u103E"], ["\u200B\u102D", "\u200B\u102E"], ["\u200B\u103A","\u1004\u103A\u1039\u200B"], ["\u200B\u102B", "\u200B\u103D"], ["\u200B\u1037", "\u200B\u1036"], ["\u200B\u103C", "\u200B\u1032"], ["\u200B\u102F", "\u200B\u102F"], ["\u200B\u1030", "\u200B\u1030"], ["\u200B\u1038", "\u200B\u102B\u103A"], ["\u1012", "\u1013"], ["Enter", "Enter"]],
			[["Shift", "Shift"], ["\u1016", "\u1007"], ["\u1011", "\u100C"], ["\u1001", "\u1003"], ["\u101C", "\u1020"], ["\u1018", "\u1026"], ["\u100A", "\u1008"], ["\u200B\u102C", "\u102A"], ["\u101A", "\u101B"], ["\u002E", "\u101B"], ["\u104B", "\u104A"], ["Shift", "Shift"]],
			[[" ", " "]]
		],*/
		Dutch: [ // Dutch Standard Keyboard
			[["@", "\u00a7", "\u00ac"], ["1", "!", "\u00b9"], ["2", '"', "\u00b2"], ["3", "#", "\u00b3"], ["4", "$", "\u00bc"], ["5", "%", "\u00bd"], ["6", "&", "\u00be"], ["7", "_", "\u00a3"], ["8", "(", "{"], ["9", ")", "}"], ["0", "'"], ["/", "?", "\\"], ["\u00b0", "~", "\u00b8"], ["Bksp", "Bksp"]],
			[["Tab", "Tab"], ["q", "Q"], ["w", "W"], ["e", "E", "\u20ac"], ["r", "R", "\u00b6"], ["t", "T"], ["y", "Y"], ["u", "U"], ["i", "I"], ["o", "O"], ["p", "P"], ["\u00a8", "^"], ["*", "|"], ["<", ">"]],
			[["Caps", "Caps"], ["a", "A"], ["s", "S", "\u00df"], ["d", "D"], ["f", "F"], ["g", "G"], ["h", "H"], ["j", "J"], ["k", "K"], ["l", "L"], ["+", "\u00b1"], ["\u00b4", "\u0060"], ["Enter", "Enter"]],
			[["Shift", "Shift"], ["]", "[", "\u00a6"], ["z", "Z", "\u00ab"], ["x", "X", "\u00bb"], ["c", "C", "\u00a2"], ["v", "V"], ["b", "B"], ["n", "N"], ["m", "M", "\u00b5"], [",", ";"], [".", ":", "\u00b7"], ["-", "="], ["Shift", "Shift"]],
			[[" ", " ", " ", " "], ["AltGr", "AltGr"]]
		],
		Dvorak: [ // Dvorak Keyboard
			[["`", "~"], ["1", "!"], ["2", "@"], ["3", "#"], ["4", "$"], ["5", "%"], ["6", "^"], ["7", "&"], ["8", "*"], ["9", "("], ["0", ")"], ["[", "{"], ["]", "}"], ["Bksp", "Bksp"]],
			[["Tab", "Tab"],["'", '"'], [",", "<"], [".", ">"], ["p", "P"], ["y", "Y"], ["f", "F"], ["g", "G"], ["c", "C"], ["r", "R"], ["l", "L"], ["/", "?"], ["=", "+"], ["\\", "|"]],
			[["Caps", "Caps"], ["a", "A"], ["o", "O"], ["e", "E"], ["u", "U"], ["i", "I"], ["d", "D"], ["h", "H"], ["t", "T"], ["n", "N"], ["s", "S"], ["-", "_"], ["Enter", "Enter"]],
			[["Shift", "Shift"], [";", ":"], ["q", "Q"], ["j", "J"], ["k", "K"], ["x", "X"], ["b", "B"], ["m", "M"], ["w", "W"], ["v", "V"], ["z", "Z"], ["Shift", "Shift"]],
			[[" ", " "]]
		],
		French: [ // French Standard Keyboard
			[["\u00b2", "\u00b3"], ["&", "1"], ["\u00e9", "2", "~"], ['"', "3", "#"], ["'", "4", "{"], ["(", "5", "["], ["-", "6", "|"], ["\u00e8", "7", "\u0060"], ["_", "8", "\\"], ["\u00e7", "9", "\u005e"], ["\u00e0", "0", "\u0040"], [")", "\u00b0", "]"], ["=", "+", "}"], ["Bksp", "Bksp"]],
			[["Tab", "Tab"], ["a", "A"], ["z", "Z"], ["e", "E", "\u20ac"], ["r", "R"], ["t", "T"], ["y", "Y"], ["u", "U"], ["i", "I"], ["o", "O"], ["p", "P"], ["^", "\u00a8"], ["$", "\u00a3", "\u00a4"], ["Enter", "Enter"]],
			[["Caps", "Caps"], ["q", "Q"], ["s", "S"], ["d", "D"], ["f", "F"], ["g", "G"], ["h", "H"], ["j", "J"], ["k", "K"], ["l", "L"], ["m", "M"], ["\u00f9", "%"], ["*", "\u03bc"]],
			[["Shift", "Shift"], ["<", ">"], ["w", "W"], ["x", "X"], ["c", "C"], ["v", "V"], ["b", "B"], ["n", "N"], [",", "?"], [";", "."], [":", "/"], ["!", "\u00a7"], ["Shift", "Shift"]],
			[[" ", " ", " ", " "], ["AltGr", "AltGr"]]
		],
		German: [ // German Standard Keyboard
			[["\u005e", "\u00b0"], ["1", "!"], ["2", '"', "\u00b2"], ["3", "\u00a7", "\u00b3"], ["4", "$"], ["5", "%"], ["6", "&"], ["7", "/", "{"], ["8", "(", "["], ["9", ")", "]"], ["0", "=", "}"], ["\u00df", "?", "\\"], ["\u00b4", "\u0060"], ["Bksp", "Bksp"]],
			[["Tab", "Tab"], ["q", "Q", "\u0040"], ["w", "W"], ["e", "E", "\u20ac"], ["r", "R"], ["t", "T"], ["z", "Z"], ["u", "U"], ["i", "I"], ["o", "O"], ["p", "P"], ["\u00fc", "\u00dc"], ["+", "*", "~"], ["Enter", "Enter"]],
			[["Caps", "Caps"], ["a", "A"], ["s", "S"], ["d", "D"], ["f", "F"], ["g", "G"], ["h", "H"], ["j", "J"], ["k", "K"], ["l", "L"], ["\u00f6", "\u00d6"], ["\u00e4", "\u00c4"], ["#", "'"]],
			[["Shift", "Shift"], ["<", ">", "\u00a6"], ["y", "Y"], ["x", "X"], ["c", "C"], ["v", "V"], ["b", "B"], ["n", "N"], ["m", "M", "\u00b5"], [",", ";"], [".", ":"], ["-", "_"], ["Shift", "Shift"]],
			[[" ", " ", " ", " "], ["AltGr", "AltGr"]]
		],
		Greek: [ // Greek Standard Keyboard
			[["`", "~"], ["1", "!"], ["2", "@", "\u00b2"], ["3", "#", "\u00b3"], ["4", "$", "\u00a3"], ["5", "%", "\u00a7"], ["6", "^", "\u00b6"], ["7", "&"], ["8", "*", "\u00a4"], ["9", "(", "\u00a6"], ["0", ")", "\u00ba"], ["-", "_", "\u00b1"], ["=", "+", "\u00bd"], ["Bksp", "Bksp"]],
			[["Tab", "Tab"], [";", ":"], ["\u03c2", "^"], ["\u03b5", "\u0395"], ["\u03c1", "\u03a1"], ["\u03c4", "\u03a4"], ["\u03c5", "\u03a5"], ["\u03b8", "\u0398"], ["\u03b9", "\u0399"], ["\u03bf", "\u039f"], ["\u03c0", "\u03a0"], ["[", "{", "\u201c"], ["]", "}", "\u201d"], ["Enter", "Enter"]],
			[["Caps", "Caps"], ["\u03b1", "\u0391"], ["\u03c3", "\u03a3"], ["\u03b4", "\u0394"], ["\u03c6", "\u03a6"], ["\u03b3", "\u0393"], ["\u03b7", "\u0397"], ["\u03be", "\u039e"], ["\u03ba", "\u039a"], ["\u03bb", "\u039b"], ["\u0384", "\u00a8", "\u0385"], ["'", '"'], ["\\", "|", "\u00ac"]],
			[["Shift", "Shift"], ["<", ">"], ["\u03b6", "\u0396"], ["\u03c7", "\u03a7"], ["\u03c8", "\u03a8"], ["\u03c9", "\u03a9"], ["\u03b2", "\u0392"], ["\u03bd", "\u039d"], ["\u03bc", "\u039c"], [",", "<"], [".", ">"], ["/", "?"], ["Shift", "Shift"]],
			[[" ", " ", " ", " "], ["AltGr", "AltGr"]]
		],
		Hebrew: [ // Hebrew Standard Keyboard
			[["~", "`"], ["1", "!"], ["2", "@"], ["3", "#"], ["4" , "$", "\u20aa"], ["5" , "%"], ["6", "^"], ["7", "&"], ["8", "*"], ["9", ")"], ["0", "("], ["-", "_"], ["=", "+"], ["\\", "|"], ["Bksp", "Bksp"]],
			[["Tab", "Tab"], ["/", "Q"], ["'", "W"], ["\u05e7", "E", "\u20ac"], ["\u05e8", "R"], ["\u05d0", "T"], ["\u05d8", "Y"], ["\u05d5", "U", "\u05f0"], ["\u05df", "I"], ["\u05dd", "O"], ["\u05e4", "P"], ["]", "}"], ["[", "{"]],
			[["Caps", "Caps"], ["\u05e9", "A"], ["\u05d3", "S"], ["\u05d2", "D"], ["\u05db", "F"], ["\u05e2", "G"], ["\u05d9", "H", "\u05f2"], ["\u05d7", "J", "\u05f1"], ["\u05dc", "K"], ["\u05da", "L"], ["\u05e3", ":"], ["," , '"'], ["Enter", "Enter"]],
			[["Shift", "Shift"], ["\u05d6", "Z"], ["\u05e1", "X"], ["\u05d1", "C"], ["\u05d4", "V"], ["\u05e0", "B"], ["\u05de", "N"], ["\u05e6", "M"], ["\u05ea", ">"], ["\u05e5", "<"], [".", "?"], ["Shift", "Shift"]],
			[[" ", " ", " ", " "], ["AltGr", "AltGr"]]
		],
		Hungarian: [ // Hungarian Standard Keyboard
			[["0", "\u00a7"], ["1", "'", "\u007e"], ["2", '"', "\u02c7"], ["3", "+", "\u02c6"], ["4", "!", "\u02d8"], ["5", "%", "\u00b0"], ["6", "/", "\u02db"], ["7", "=", "\u0060"], ["8", "(", "\u02d9"], ["9", ")", "\u00b4"], ["\u00f6", "\u00d6", "\u02dd"], ["\u00fc", "\u00dc", "\u00a8"], ["\u00f3", "\u00d3", "\u00b8"], ["Bksp", "Bksp"]],
			[["Tab", "Tab"], ["q", "Q", "\u005c"], ["w", "W", "\u007c"], ["e", "E", "\u00c4"], ["r", "R"], ["t", "T"], ["z", "Z"], ["u", "U", "\u20ac"], ["i", "I", "\u00cd"], ["o", "O"], ["p", "P"], ["\u0151", "\u0150", "\u00f7"], ["\u00fa", "\u00da", "\u00d7"], ["Enter", "Enter"]],
			[["Caps", "Caps"], ["a", "A", "\u00e4"], ["s", "S","\u0111"], ["d", "D","\u0110"], ["f", "F","\u005b"], ["g", "G","\u005d"], ["h", "H"], ["j", "J","\u00ed"], ["k", "K","\u0141"], ["l", "L","\u0142"], ["\u00e9", "\u00c9","\u0024"], ["\u00e1", "\u00c1","\u00df"], ["\u0171", "\u0170","\u00a4"]],
			[["Shift", "Shift"], ["\u00ed", "\u00cd","\u003c"], ["y", "Y","\u003e"], ["x", "X","\u0023"], ["c", "C","\u0026"], ["v", "V","\u0040"], ["b", "B","\u007b"], ["n", "N","\u007d"], ["m", "M","\u003c"], [",", "?","\u003b"], [".", ":","\u003e"], ["-", "_","\u002a"], ["Shift", "Shift"]],
			[[" ", " ", " ", " "], ["AltGr", "AltGr"]]
		],
		Italian: [ // Italian Standard Keyboard
			[["\u005c", "\u007c"], ["1", "!"], ["2", '"'], ["3", "\u00a3"], ["4", "$", "\u20ac"], ["5", "%"], ["6", "&"], ["7", "/"], ["8", "("], ["9", ")"], ["0", "="], ["'", "?"], ["\u00ec", "\u005e"], ["Bksp", "Bksp"]],
			[["Tab", "Tab"], ["q", "Q"], ["w", "W"], ["e", "E", "\u20ac"], ["r", "R"], ["t", "T"], ["y", "Y"], ["u", "U"], ["i", "I"], ["o", "O"], ["p", "P"], ["\u00e8", "\u00e9", "[", "{"], ["+", "*", "]", "}"], ["Enter", "Enter"]],
			[["Caps", "Caps"], ["a", "A"], ["s", "S"], ["d", "D"], ["f", "F"], ["g", "G"], ["h", "H"], ["j", "J"], ["k", "K"], ["l", "L"], ["\u00f2", "\u00e7", "@"], ["\u00e0", "\u00b0", "#"], ["\u00f9", "\u00a7"]],
			[["Shift", "Shift"], ["<", ">"], ["z", "Z"], ["x", "X"], ["c", "C"], ["v", "V"], ["b", "B"], ["n", "N"], ["m", "M"], [",", ";"], [".", ":"], ["-", "_"], ["Shift", "Shift"]],
			[[" ", " ", " ", " "], ["AltGr", "AltGr"]]
		],
		Lithuanian: [ // Lithuanian Standard Keyboard
			[["`", "~"], ["\u0105", "\u0104"], ["\u010D", "\u010C"], ["\u0119", "\u0118"], ["\u0117", "\u0116"], ["\u012F", "\u012E"], ["\u0161", "\u0160"], ["\u0173", "\u0172"], ["\u016B", "\u016A"], ["\u201E", "("], ["\u201C", ")"], ["-", "_"], ["\u017E", "\u017D"], ["Bksp", "Bksp"]],
			[["Tab", "Tab"], ["q", "Q"], ["w", "W"], ["e", "E"], ["r", "R"], ["t", "T"], ["y", "Y"], ["u", "U"], ["i", "I"], ["o", "O"], ["p", "P"], ["[", "{"], ["]", "}"], ["Enter", "Enter"]],
			[["Caps", "Caps"], ["a", "A"], ["s", "S"], ["d", "D"], ["f", "F"], ["g", "G"], ["h", "H"], ["j", "J"], ["k", "K"], ["l", "L"], [";", ":"], ["'", '"'], ["\\", "|"]],
			[["Shift", "Shift"], ["\u2013", "\u20AC"], ["z", "Z"], ["x", "X"], ["c", "C"], ["v", "V"], ["b", "B"], ["n", "N"], ["m", "M"], [",", "<"], [".", ">"], ["/", "?"], ["Shift", "Shift"]],
			[[" ", " "]]
		],
		Norwegian: [ // Norwegian Standard Keyboard
			[["|", "\u00a7"], ["1", "!"], ["2", '"', "@"], ["3", "#", "\u00a3"], ["4", "\u00a4", "$"], ["5", "%"], ["6", "&"], ["7", "/", "{"], ["8", "(", "["], ["9", ")", "]"], ["0", "=", "}"], ["+", "?"], ["\\", "`", "\u00b4"], ["Bksp", "Bksp"]],
			[["Tab", "Tab"], ["q", "Q"], ["w", "W"], ["e", "E", "\u20ac"], ["r", "R"], ["t", "T"], ["y", "Y"], ["u", "U"], ["i", "I"], ["o", "O"], ["p", "P"], ["\u00e5", "\u00c5"], ["\u00a8", "^", "~"], ["Enter", "Enter"]],
			[["Caps", "Caps"], ["a", "A"], ["s", "S"], ["d", "D"], ["f", "F"], ["g", "G"], ["h", "H"], ["j", "J"], ["k", "K"], ["l", "L"], ["\u00f8", "\u00d8"], ["\u00e6", "\u00c6"], ["'", "*"]],
			[["Shift", "Shift"], ["<", ">"], ["z", "Z"], ["x", "X"], ["c", "C"], ["v", "V"], ["b", "B"], ["n", "N"], ["m", "M", "\u03bc", "\u039c"], [",", ";"], [".", ":"], ["-", "_"], ["Shift", "Shift"]],
			[[" ", " ", " ", " "], ["AltGr", "AltGr"]]
		],
		/*Numpad: [ // Number pad
			[["$"], ["\u00a3"], ["\u20ac"], ["\u00a5"], ["/"], ["^"], ["Bksp", "Bksp"]],
			[["."], ["7"], ["8"], ["9"], ["*"], ["<"], ["("], ["["]],
			[["="], ["4"], ["5"], ["6"], ["-"], [">"], [")"], ["]"]],
			[["0"], ["1"], ["2"], ["3"], ["+"], ["Enter", "Enter"]],
			[[" "]]
			],
			"Polish Prog": [ // Polish Programmers Keyboard
			[["`", "~"], ["1", "!"], ["2", "@"], ["3", "#"], ["4", "$"], ["5", "%"], ["6", "^"], ["7", "&"], ["8", "*"], ["9", "("], ["0", ")"], ["-", "_"], ["=", "+"], ["Bksp", "Bksp"]],
			[["Tab", "Tab"], ["q", "Q"], ["w", "W"], ["e", "E", "\u0119", "\u0118"], ["r", "R"], ["t", "T"], ["y", "Y"], ["u", "U"], ["i", "I"], ["o", "O", "\u00f3", "\u00d3"], ["p", "P"], ["[", "{"], ["]", "}"], ["\\", "|"]],
			[["Caps", "Caps"], ["a", "A", "\u0105", "\u0104"], ["s", "S", "\u015b", "\u015a"], ["d", "D"], ["f", "F"], ["g", "G"], ["h", "H"], ["j", "J"], ["k", "K"], ["l", "L", "\u0142", "\u0141"], [";", ":"], ["'", '"'], ["Enter", "Enter"]],
			[["Shift", "Shift"], ["z", "Z", "\u017c", "\u017b"], ["x", "X", "\u017a", "\u0179"], ["c", "C", "\u0107", "\u0106"], ["v", "V"], ["b", "B"], ["n", "N", "\u0144", "\u0143"], ["m", "M"], [",", "<"], [".", ">"], ["/", "?"], ["Shift", "Shift"]],
			[[" ", " ", " ", " "], ["Alt", "Alt"]]
		],*/
		Portuguese: [ // Portuguese Standard Keyboard
			[["`", "\u00ac", "\u00a6"], ["1", "!"], ["2", '"'], ["3", "\u00a3"], ["4", "$", "\u20ac"], ["5", "%"], ["6", "^"], ["7", "&"], ["8", "*"], ["9", "("], ["0", ")"], ["-", "_"], ["=", "+"], ["Bksp", "Bksp"]],
			[["Tab", "Tab"], ["q", "Q"], ["w", "W"], ["e", "E", "\u00e9", "\u00c9"], ["r", "R"], ["t", "T"], ["y", "Y"], ["u", "U", "\u00fa", "\u00da"], ["i", "I", "\u00ed", "\u00cd"], ["o", "O", "\u00f3", "\u00d3"], ["p", "P"], ["[", "{"], ["]", "}"], ["Enter", "Enter"]],
			[["Caps", "Caps"], ["a", "A", "\u00e1", "\u00c1"], ["s", "S"], ["d", "D"], ["f", "F"], ["g", "G"], ["h", "H"], ["j", "J"], ["k", "K"], ["l", "L"], ["\u00e7", "\u00c7"], [";", ":"], ["'", "@"], ["#", "~"]],
			[["Shift", "Shift"], ["\\", "|"], ["z", "Z"], ["x", "X"], ["c", "C"], ["v", "V"], ["b", "B"], ["n", "N"], ["m", "M"], [",", "<"], [".", ">"], ["/", "?"], ["Shift", "Shift"]],
			[[" ", " ", " ", " "], ["AltGr", "AltGr"]]
		],
		Russian: [ // Russian Standard Keyboard
			[["\u0451", "\u0401"], ["1", "!"], ["2", '"'], ["3", "\u2116"], ["4", ";"], ["5", "%"], ["6", ":"], ["7", "?"], ["8", "*"], ["9", "("], ["0", ")"], ["-", "_"], ["=", "+"], ["Bksp", "Bksp"]],
			[["Tab", "Tab"], ["\u0439", "\u0419"], ["\u0446", "\u0426"], ["\u0443", "\u0423"], ["\u043A", "\u041A"], ["\u0435", "\u0415"], ["\u043D", "\u041D"], ["\u0433", "\u0413"], ["\u0448", "\u0428"], ["\u0449", "\u0429"], ["\u0437", "\u0417"], ["\u0445", "\u0425"], ["\u044A", "\u042A"], ["Enter", "Enter"]],
			[["Caps", "Caps"], ["\u0444", "\u0424"], ["\u044B", "\u042B"], ["\u0432", "\u0412"], ["\u0430", "\u0410"], ["\u043F", "\u041F"], ["\u0440", "\u0420"], ["\u043E", "\u041E"], ["\u043B", "\u041B"], ["\u0434", "\u0414"], ["\u0436", "\u0416"], ["\u044D", "\u042D"], ["\\", "/"]],
			[["Shift", "Shift"], ["/", "|"], ["\u044F", "\u042F"], ["\u0447", "\u0427"], ["\u0441", "\u0421"], ["\u043C", "\u041C"], ["\u0438", "\u0418"], ["\u0442", "\u0422"], ["\u044C", "\u042C"], ["\u0431", "\u0411"], ["\u044E", "\u042E"], [".", ","], ["Shift", "Shift"]],
			[[" ", " "]]
		],
		Slovenian: [ // Slovenian Standard Keyboard
			[["\u00a8", "\u00a8", "\u00b8"], ["1", "!", "~"], ["2", '"', "\u02c7"], ["3", "#", "^"], ["4", "$", "\u02d8"], ["5", "%", "\u00b0"], ["6", "&", "\u02db"], ["7", "/", "\u0060"], ["8", "(", "\u00B7"], ["9", ")", "\u00b4"], ["0", "=", "\u2033"], ["'", "?", "\u00a8"], ["+", "*", "\u00b8"], ["Bksp", "Bksp"]],
			[["Tab", "Tab"], ["q", "Q", "\\"], ["w", "W","|"], ["e", "E", "\u20ac"], ["r", "R"], ["t", "T"], ["z", "Z"], ["u", "U"], ["i", "I"], ["o", "O"], ["p", "P"], ["\u0161", "\u0160", "\u00f7"], ["\u0111", "\u0110", "\u00d7"], ["Enter", "Enter"]],
			[["Caps", "Caps"], ["a", "A"], ["s", "S"], ["d", "D"], ["f", "F", "["], ["g", "G", "]"], ["h", "H"], ["j", "J"], ["k", "K", "\u0142"], ["l", "L", "\u0141"], ["\u010D", "\u010C"], ["\u0107", "\u0106", "\u00df"], ["\u017E", "\u017D", "\u00a4"]],
			[["Shift", "Shift"], ["<", ">"], ["y", "Y"], ["x", "X"], ["c", "C"], ["v", "V", "@"], ["b", "B", "{",], ["n", "N", "}"], ["m", "M", "\u00a7"], [",", ";"], [".", ":"], ["-", "_"], ["Shift", "Shift"]],
			[[" ", " ", " ", " "], ["AltGr", "AltGr"]]
		],
		"Spanish-SP": [ // Spanish (Spain) Standard Keyboard
			[["\u00ba", "\u00aa", "\\"], ["1", "!", "|"], ["2", '"', "@"], ["3", "'", "#"], ["4", "$", "~"], ["5", "%", "\u20ac"], ["6", "&","\u00ac"], ["7", "/"], ["8", "("], ["9", ")"], ["0", "="], ["'", "?"], ["\u00a1", "\u00bf"], ["Bksp", "Bksp"]],
			[["Tab", "Tab"], ["q", "Q"], ["w", "W"], ["e", "E"], ["r", "R"], ["t", "T"], ["y", "Y"], ["u", "U"], ["i", "I"], ["o", "O"], ["p", "P"], ["\u0060", "^", "["], ["\u002b", "\u002a", "]"], ["Enter", "Enter"]],
			[["Caps", "Caps"], ["a", "A"], ["s", "S"], ["d", "D"], ["f", "F"], ["g", "G"], ["h", "H"], ["j", "J"], ["k", "K"], ["l", "L"], ["\u00f1", "\u00d1"], ["\u00b4", "\u00a8", "{"], ["\u00e7", "\u00c7", "}"]],
			[["Shift", "Shift"], ["<", ">"], ["z", "Z"], ["x", "X"], ["c", "C"], ["v", "V"], ["b", "B"], ["n", "N"], ["m", "M"], [",", ";"], [".", ":"], ["-", "_"], ["Shift", "Shift"]],
			[[" ", " ", " ", " "], ["AltGr", "AltGr"]]
		],
		"Turkish-F": [ // Turkish F Keyboard Layout
			[['+', "*", "\u00ac"], ["1", "!", "\u00b9", "\u00a1"], ["2", '"', "\u00b2"], ["3", "^", "#", "\u00b3"], ["4", "$", "\u00bc", "\u00a4"], ["5", "%", "\u00bd"], ["6", "&", "\u00be"], ["7", "'", "{"], ["8", "(", '['], ["9", ")", ']'], ["0", "=", "}"], ["/", "?", "\\", "\u00bf"], ["-", "_", "|"], ["Bksp", "Bksp"]],
			[["Tab", "Tab"], ["f", "F", "@"], ["g", "G"], ["\u011f", "\u011e"], ["\u0131", "\u0049", "\u00b6", "\u00ae"], ["o", "O"], ["d", "D", "\u00a5"], ["r", "R"], ["n", "N"], ["h", "H", "\u00f8", "\u00d8"], ["p", "P", "\u00a3"], ["q", "Q", "\u00a8"], ["w", "W", "~"], ["Enter", "Enter"]],
			[["Caps", "Caps"], ["u", "U", "\u00e6", "\u00c6"], ["i", "\u0130", "\u00df", "\u00a7"], ["e", "E", "\u20ac"], ["a", "A", " ", "\u00aa"], ["\u00fc", "\u00dc"], ["t", "T"], ["k", "K"], ["m", "M"], ["l", "L"], ["y", "Y", "\u00b4"], ["\u015f", "\u015e"], ["x", "X", "`"]],
			[["Shift", "Shift"], ["<", ">", "|", "\u00a6"], ["j", "J", "\u00ab", "<"], ["\u00f6", "\u00d6", "\u00bb", ">"], ["v", "V", "\u00a2", "\u00a9"], ["c", "C"], ["\u00e7", "\u00c7"], ["z", "Z"], ["s", "S", "\u00b5", "\u00ba"], ["b", "B", "\u00d7"], [".", ":", "\u00f7"], [",", ";", "-"], ["Shift", "Shift"]],
			[[" ", " ", " ", " "],	["AltGr", "AltGr"]]
		],
		"Turkish-Q": [ // Turkish Q Keyboard Layout
			[['"', "\u00e9", "<"], ["1", "!", ">"], ["2", "'", "\u00a3"], ["3", "^", "#"], ["4", "+", "$"], ["5", "%", "\u00bd"], ["6", "&"], ["7", "/", "{"], ["8", "(", '['], ["9", ")", ']'], ["0", "=", "}"], ["*", "?", "\\"], ["-", "_", "|"], ["Bksp", "Bksp"]],
			[["Tab", "Tab"], ["q", "Q", "@"], ["w", "W"], ["e", "E", "\u20ac"], ["r", "R"], ["t", "T"], ["y", "Y"], ["u", "U"], ["\u0131", "\u0049", "\u0069", "\u0130"], ["o", "O"], ["p", "P"], ["\u011f", "\u011e", "\u00a8"], ["\u00fc", "\u00dc", "~"], ["Enter", "Enter"]],
			[["Caps", "Caps"], ["a", "A", "\u00e6", "\u00c6"], ["s", "S", "\u00df"], ["d", "D"], ["f", "F"], ["g", "G"], ["h", "H"], ["j", "J"], ["k", "K"], ["l", "L"], ["\u015f", "\u015e", "\u00b4"], ["\u0069", "\u0130"], [",", ";", "`"]],
			[["Shift", "Shift"], ["<", ">", "|"], ["z", "Z"], ["x", "X"], ["c", "C"], ["v", "V"], ["b", "B"], ["n", "N"], ["m", "M"], ["\u00f6", "\u00d6"], ["\u00e7", "\u00c7"], [".", ":"], ["Shift", "Shift"]],
			[[" ", " ", " ", " "],	["AltGr", "AltGr"]]
		],
		UK: [ // UK Standard Keyboard
			[["`", "\u00ac", "\u00a6"], ["1", "!"], ["2", '"'], ["3", "\u00a3"], ["4", "$", "\u20ac"], ["5", "%"], ["6", "^"], ["7", "&"], ["8", "*"], ["9", "("], ["0", ")"], ["-", "_"], ["=", "+"], ["Bksp", "Bksp"]],
			[["Tab", "Tab"], ["q", "Q"], ["w", "W"], ["e", "E", "\u00e9", "\u00c9"], ["r", "R"], ["t", "T"], ["y", "Y"], ["u", "U", "\u00fa", "\u00da"], ["i", "I", "\u00ed", "\u00cd"], ["o", "O", "\u00f3", "\u00d3"], ["p", "P"], ["[", "{"], ["]", "}"], ["Enter", "Enter"]],
			[["Caps", "Caps"], ["a", "A", "\u00e1", "\u00c1"], ["s", "S"], ["d", "D"], ["f", "F"], ["g", "G"], ["h", "H"], ["j", "J"], ["k", "K"], ["l", "L"], [";", ":"], ["'", "@"], ["#", "~"]],
			[["Shift", "Shift"], ["\\", "|"], ["z", "Z"], ["x", "X"], ["c", "C"], ["v", "V"], ["b", "B"], ["n", "N"], ["m", "M"], [",", "<"], [".", ">"], ["/", "?"], ["Shift", "Shift"]],
			[[" ", " ", " ", " "], ["AltGr", "AltGr"]]
		],
		US: [ // US Standard Keyboard
			[["`", "~"], ["1", "!"], ["2", "@"], ["3", "#"], ["4", "$"], ["5", "%"], ["6", "^"], ["7", "&"], ["8", "*"], ["9", "("], ["0", ")"], ["-", "_"], ["=", "+"], ["Bksp", "Bksp"]],
			[["Tab", "Tab"], ["q", "Q"], ["w", "W"], ["e", "E"], ["r", "R"], ["t", "T"], ["y", "Y"], ["u", "U"], ["i", "I"], ["o", "O"], ["p", "P"], ["[", "{"], ["]", "}"], ["\\", "|"]],
			[["Caps", "Caps"], ["a", "A"], ["s", "S"], ["d", "D"], ["f", "F"], ["g", "G"], ["h", "H"], ["j", "J"], ["k", "K"], ["l", "L"], [";", ":"], ["'", '"'], ["Enter", "Enter"]],
			[["Shift", "Shift"], ["z", "Z"], ["x", "X"], ["c", "C"], ["v", "V"], ["b", "B"], ["n", "N"], ["m", "M"], [",", "<"], [".", ">"], ["/", "?"], ["Shift", "Shift"]],
			[[" ", " "]]
		],
		"US Int'l": [ // US International Keyboard
			[["`", "~"], ["1", "!", "\u00a1", "\u00b9"], ["2", "@", "\u00b2"], ["3", "#", "\u00b3"], ["4", "$", "\u00a4", "\u00a3"], ["5", "%", "\u20ac"], ["6", "^", "\u00bc"], ["7", "&", "\u00bd"], ["8", "*", "\u00be"], ["9", "(", "\u2018"], ["0", ")", "\u2019"], ["-", "_", "\u00a5"], ["=", "+", "\u00d7", "\u00f7"], ["Bksp", "Bksp"]],
			[["Tab", "Tab"], ["q", "Q", "\u00e4", "\u00c4"], ["w", "W", "\u00e5", "\u00c5"], ["e", "E", "\u00e9", "\u00c9"], ["r", "R", "\u00ae"], ["t", "T", "\u00fe", "\u00de"], ["y", "Y", "\u00fc", "\u00dc"], ["u", "U", "\u00fa", "\u00da"], ["i", "I", "\u00ed", "\u00cd"], ["o", "O", "\u00f3", "\u00d3"], ["p", "P", "\u00f6", "\u00d6"], ["[", "{", "\u00ab"], ["]", "}", "\u00bb"], ["\\", "|", "\u00ac", "\u00a6"]],
			[["Caps", "Caps"], ["a", "A", "\u00e1", "\u00c1"], ["s", "S", "\u00df", "\u00a7"], ["d", "D", "\u00f0", "\u00d0"], ["f", "F"], ["g", "G"], ["h", "H"], ["j", "J"], ["k", "K"], ["l", "L", "\u00f8", "\u00d8"], [";", ":", "\u00b6", "\u00b0"], ["'", '"', "\u00b4", "\u00a8"], ["Enter", "Enter"]],
			[["Shift", "Shift"], ["z", "Z", "\u00e6", "\u00c6"], ["x", "X"], ["c", "C", "\u00a9", "\u00a2"], ["v", "V"], ["b", "B"], ["n", "N", "\u00f1", "\u00d1"], ["m", "M", "\u00b5"], [",", "<", "\u00e7", "\u00c7"], [".", ">"], ["/", "?", "\u00bf"], ["Shift", "Shift"]],
			[[" ", " ", " ", " "], ["Alt", "Alt"]]
		]
	},
	deadKey: {
		'"': [ // Umlaut / Diaeresis / Greek Dialytika
			["a", "\u00e4"], ["e", "\u00eb"], ["i", "\u00ef"], ["o", "\u00f6"], ["u", "\u00fc"], ["y", "\u00ff"], ["\u03b9", "\u03ca"], ["\u03c5", "\u03cb"],
			["A", "\u00c4"], ["E", "\u00cb"], ["I", "\u00cf"], ["O", "\u00d6"], ["U", "\u00dc"], ["Y", "\u0178"], ["\u0399", "\u03aa"], ["\u03a5", "\u03ab"]
		],
		'\u00a8': [ // Umlaut / Diaeresis / Greek Dialytika
			["a", "\u00e4"], ["e", "\u00eb"], ["i", "\u00ef"], ["o", "\u00f6"], ["u", "\u00fc"], ["y", "\u00ff"], ["\u03b9", "\u03ca"], ["\u03c5", "\u03cb"],
			["A", "\u00c4"], ["E", "\u00cb"], ["I", "\u00cf"], ["O", "\u00d6"], ["U", "\u00dc"], ["Y", "\u0178"], ["\u0399", "\u03aa"], ["\u03a5", "\u03ab"]
		],
		'~': [ // Tilde
			["a", "\u00e3"], ["o", "\u00f5"], ["n", "\u00f1"],
			["A", "\u00c3"], ["O", "\u00d5"], ["N", "\u00d1"]
		],
		'^': [ // Circumflex
			["a", "\u00e2"], ["e", "\u00ea"], ["i", "\u00ee"], ["o", "\u00f4"], ["u", "\u00fb"], ["w", "\u0175"], ["y", "\u0177"],
			["A", "\u00c2"], ["E", "\u00ca"], ["I", "\u00ce"], ["O", "\u00d4"], ["U", "\u00db"], ["W", "\u0174"], ["Y", "\u0176"]
		],
		'\u02c7': [ // Baltic caron
			["c", "\u010D"], ["s", "\u0161"], ["z", "\u017E"], ["r", "\u0159"], ["d", "\u010f"], ["t", "\u0165"], ["n", "\u0148"], ["l", "\u013e"], ["e", "\u011b"],
			["C", "\u010C"], ["S", "\u0160"], ["Z", "\u017D"], ["R", "\u0158"], ["D", "\u010e"], ["T", "\u0164"], ["N", "\u0147"], ["L", "\u013d"], ["E", "\u011a"]
		],
		'\u02d8': [ // Romanian and Turkish breve
			["a", "\u0103"], ["g", "\u011f"],
			["A", "\u0102"], ["G", "\u011e"]
		],
		'`': [ // Grave
			["a", "\u00e0"], ["e", "\u00e8"], ["i", "\u00ec"], ["o", "\u00f2"], ["u", "\u00f9"],
			["A", "\u00c0"], ["E", "\u00c8"], ["I", "\u00cc"], ["O", "\u00d2"], ["U", "\u00d9"]
		],
		"'": [ // Acute / Greek Tonos
			["a", "\u00e1"], ["e", "\u00e9"], ["i", "\u00ed"], ["o", "\u00f3"], ["u", "\u00fa"], ["\u03b1", "\u03ac"], ["\u03b5", "\u03ad"], ["\u03b7", "\u03ae"], ["\u03b9", "\u03af"], ["\u03bf", "\u03cc"], ["\u03c5", "\u03cd"], ["\u03c9", "\u03ce"],
			["A", "\u00c1"], ["E", "\u00c9"], ["I", "\u00cd"], ["O", "\u00d3"], ["U", "\u00da"], ["\u0391", "\u0386"], ["\u0395", "\u0388"], ["\u0397", "\u0389"], ["\u0399", "\u038a"], ["\u039f", "\u038c"], ["\u03a5", "\u038e"], ["\u03a9", "\u038f"]
		],
		'\u00b4':[ // Acute / Greek Tonos
			["a", "\u00e1"], ["e", "\u00e9"], ["i", "\u00ed"], ["o", "\u00f3"], ["u", "\u00fa"], ["\u03b1", "\u03ac"], ["\u03b5", "\u03ad"], ["\u03b7", "\u03ae"], ["\u03b9", "\u03af"], ["\u03bf", "\u03cc"], ["\u03c5", "\u03cd"], ["\u03c9", "\u03ce"],
			["A", "\u00c1"], ["E", "\u00c9"], ["I", "\u00cd"], ["O", "\u00d3"], ["U", "\u00da"], ["\u0391", "\u0386"], ["\u0395", "\u0388"], ["\u0397", "\u0389"], ["\u0399", "\u038a"], ["\u039f", "\u038c"], ["\u03a5", "\u038e"], ["\u03a9", "\u038f"]
		],
		'\u0384': [ // Acute / Greek Tonos
			["a", "\u00e1"], ["e", "\u00e9"], ["i", "\u00ed"], ["o", "\u00f3"], ["u", "\u00fa"], ["\u03b1", "\u03ac"], ["\u03b5", "\u03ad"], ["\u03b7", "\u03ae"], ["\u03b9", "\u03af"], ["\u03bf", "\u03cc"], ["\u03c5", "\u03cd"], ["\u03c9", "\u03ce"],
			["A", "\u00c1"], ["E", "\u00c9"], ["I", "\u00cd"], ["O", "\u00d3"], ["U", "\u00da"], ["\u0391", "\u0386"], ["\u0395", "\u0388"], ["\u0397", "\u0389"], ["\u0399", "\u038a"], ["\u039f", "\u038c"], ["\u03a5", "\u038e"], ["\u03a9", "\u038f"]
		],
		'\u02dd': [ // Hungarian Double Acute Accent
			["o", "\u0151"], ["u", "\u0171"],
			["O", "\u0150"], ["U", "\u0170"]
		],
		'\u0385': [ // Greek Dialytika + Tonos
			["\u03b9", "\u0390"], ["\u03c5", "\u03b0"]
		],
		'\u00b0': [ // Ring
			["a", "\u00e5"],
			["A", "\u00c5"]
		],
		'\u00ba': [ // Ring
			["a", "\u00e5"],
			["A", "\u00c5"]
		]
	},

	initComponent : function(options){
                Ext.ux.VirtualKeyboard.superclass.initComponent.call(this);
		
		Ext.apply(this, {
			language: this.language || 'US',
			deadKeysButtonText: this.deadKeysButtonText || 'Type accented letters',
			//deadKeysButtonTip: this.deadKeysButtonTip || 'Dead keys are used to generate accented letters',
			autoDestroy: true
		});
		
		Ext.apply(this, options);
		
		this.addEvents(
			'keypress'
		);
		
		/*this.on('beforedestroy', function(){
			this.keyboard.removeAllListeners();
			delete this.keyboard;
		}, this);*/
		
	},

	onRender: function(ct, position){
		this.initKeyboard(ct);
		Ext.ux.VirtualKeyboard.superclass.onRender.call(this, ct, position);
	},
	
	initKeyboard : function(ct){
		this.keyboardTarget.el.on('click', this.IESel, this);
		this.keyboardTarget.el.on('keyup', this.IESel, this);
		this.keyboardTarget.el.on('select', this.IESel, this);

		this.keyboard = ct.createChild({
			tag: 'div',
			cls: 'x-keyboard x-panel'
		});
		
		this.keyboard.setStyle({width: this.width || 370});

		var layouts = 0;
		for (lang in this.Languages) if (typeof this.Languages[lang] == "object") layouts++;

		var dh = Ext.DomHelper;
		
		var ktbarItems = [];

		if (this.languageSelection) {
		
			var values = [];
			for (ktype in this.Languages) {
				if (typeof this.Languages[ktype] == "object") {
					values.push([ktype, ktype]);
				}
			}

			this.languageSelector = new Ext.form.ComboBox({
                                store: values,
				forceSelection: true,
				triggerAction: 'all',
				editable: false,
				readOnly: true,
				height: 15,
				width: 100,
				value: this.language,
				listeners: {
					'select': function(combo, record) {
						this.language = record.data.value;
						this.buildKeys();
					},
					expand: function(){
						this.selectingLanguage = true;
					},
					collapse: function(){
						this.selectingLanguage = false;
					},
					scope: this
				}
			});
			ktbarItems.push(this.languageSelector);
		}
		else{
			ktbarItems.push(this.language);
		}
		ktbarItems.push('-', {
			text: this.deadKeysButtonText,
                        iconCls: 'ux-accented-icon',
			//tooltip: this.deadKeysButtonTip,
			enableToggle: true,
			listeners: {
				toggle: function(btn, pressed){
					this.deadKeysOn = pressed;
					this.keyModify("");
				},
				scope: this
			}
		});
		
		this.ktbar = new Ext.Toolbar({
			renderTo: this.keyboard,
			items: ktbarItems
		});
		
		if (!this.languageSelection){
			Ext.fly(this.ktbar.items.items[0].getEl()).setStyle({fontWeight: 'bold'});
		}
		
		var wrap = this.keyboard.createChild({
			tag: 'div',
			cls: 'x-panel-bwrap'
		});
		
		var mc = wrap.createChild({
			tag: 'div',
			cls: 'x-panel-body'
		});

		this.keysContainer = mc.createChild({
			tag: 'div'
		});

		this.buildKeys();
	},
	
	getTBar: function(){
		return this.ktbar;
	},
	
	buildKeys: function() {
		this.shift = this.capslock = this.alternate = this.dead = false;
		//this.deadKeysOn = (this.layoutDDK[this.language]) ? false : this.deadCheckbox.checked;
		
		this.keysContainer.update('');

		var dh = Ext.DomHelper;

		for (var x = 0, hasdeadKey = false, lyt; lyt = this.Languages[this.language][x++];) {
			var table = dh.append(this.keysContainer, {
				tag: 'table',
				cellSpacing: '1',
				cellPadding:'0',
				border: '0',
				cls: (lyt.length <= this.keyCenter ? 'keyboardInputCenter' : '') + ' keys',
				align: (lyt.length <= this.keyCenter ? 'center' : ''),
				html: ''
			});

			if(Ext.isIE && table.firstChild){
				table.removeChild(table.firstChild);

			}
			var tbody = dh.append(table, {tag: 'tbody'});
			var tr = dh.append(tbody, {tag: 'tr'});

			for (var y = 0, lkey; lkey = lyt[y++];) {
				if (!this.layoutDDK[this.language] && !hasdeadKey){
					for (var z = 0; z < lkey.length; z++){
						if (this.deadKey[lkey[z]]){
							hasdeadKey = true;
							break;
						}
					}
				}

			var alive = false;
			if (this.deadKeysOn){
				for (key in this.deadKey){
					if (key === lkey[0]){
						alive = true;
					}
				}
			}
			//var cls = ['x-btn', 'x-btn-center'];
			var cls = [];
			if(alive)
				cls.push('alive');
			if (lyt.length > this.keyCenter && y == lyt.length)
				cls.push('last');
				
			var td = dh.append(tr, {
				tag: 'td',
				cls: cls.join(' '),
				html: lkey[0] == " " ? "&nbsp;" : lkey[0]
			});

			if (lkey[0] == " ")
				td.id = "spacebar";

			Ext.fly(td).on('mouseover', function(event, target) { if (!Ext.fly(target).hasClass('dead') && target.firstChild.nodeValue != "\xa0") Ext.fly(target).addClass('hover'); });
			Ext.fly(td).on('mouseout', function(event, target) { if (!Ext.fly(target).hasClass('dead')) Ext.fly(target).removeClass(['hover', 'pressed']); });
			Ext.fly(td).on('mousedown', function(event, target) { if (!Ext.fly(target).hasClass('dead')) Ext.fly(target).addClass('pressed'); });
			Ext.fly(td).on('mouseup', function(event, target) { if (!Ext.fly(target).hasClass('dead') && target.firstChild.nodeValue != "\xa0") Ext.fly(target).removeClass('pressed'); });
			Ext.fly(td). swallowEvent('dblclick', true);
			
			td.modifier = lkey[1];

			switch (lkey[1]) {
				case "Caps":
				case "Shift":
				case "Alt":
				case "AltGr":
					Ext.fly(td).on('click', function(event, target) {
						this.keyModify(target.modifier);
					}, this, {stopEvent: true});
					break;
				case "Tab":
					Ext.fly(td).on('click', function(event, target) {
						this.keyInsert("\t");
					}, this, {stopEvent: true});
					break;
				case "Bksp":
					Ext.fly(td).on('click', function() {
						this.keyboardTarget.focus();
						var dom = this.keyboardTarget.el.dom;
						if (dom.setSelectionRange) {
							var srt = dom.selectionStart;
							var len = dom.selectionEnd;
							if (srt < len) srt++;
							this.keyboardTarget.setValue(dom.value.substr(0, srt - 1) + dom.value.substr(len));
							dom.setSelectionRange(srt - 1, srt - 1);
						} else if (dom.createTextRange) {
							try { this.range.select(); } catch(e) {}
							this.range = document.selection.createRange();
							if (!this.range.text.length)
								this.range.moveStart('character', -1);
							this.range.text = "";
						} else this.keyboardTarget.setValue(dom.value.substr(0, dom.value.length - 1));
						if (this.shift) this.keyModify("Shift");
						if (this.alternate) this.keyModify("AltGr");
						return true;
					}, this);
					break;
				
				case "Enter":
					//if (self.keyboardTarget.nodeName == "TEXTAREA") { this.keyInsert("\n"); } else self.VKI_close();
					Ext.fly(td).on('click', function(event, target) {
						this.keyInsert("\n");
					}, this, {stopEvent: true});
					break;
					
				default:
					Ext.fly(td).on('click', function(event, target) {
						var keyValue = target.firstChild.nodeValue;
						if(keyValue == "\xa0"){
							keyValue = " ";
						}
						if (this.deadKeysOn && this.dead) {
							if (this.dead != keyValue) {
							for (key in this.deadKey) {
								if (key == this.dead) {
								if (keyValue != " ") {
									for (var z = 0, rezzed = false, dk; dk = this.deadKey[key][z++];) {
									if (dk[0] == keyValue) {
										this.keyInsert(dk[1]);
										rezzed = true;
										break;
									}
									}
								} else {
									this.keyInsert(this.dead);
									rezzed = true;
								}
								break;
								}
							}
							} else rezzed = true;
						}
						this.dead = false;

						if (!rezzed && keyValue != "\xa0") {
							if (this.deadKeysOn) {
							for (key in this.deadKey) {
								if (key == keyValue) {
								this.dead = key;
								this.className = "dead";
								if (this.shift) this.keyModify("Shift");
								if (this.alternate) this.keyModify("AltGr");
								break;
								}
							}
							if (!this.dead) this.keyInsert(keyValue);
							} else this.keyInsert(keyValue);
						}
						this.keyModify("");
					}, this, {stopEvent: true});
					delete td;
				}
				delete tr
				for (var z = lkey.length; z < 4; z++) lkey[z] = "\xa0";
			}
			delete table;
			delete tbody;
		}
	},
	
	keyModify: function(type){
		switch (type) {
			case "Alt":
			case "AltGr": this.alternate = !this.alternate; break;
			case "Caps": this.capslock = !this.capslock; break;
			case "Shift": this.shift = !this.shift; break;
		}
		var vchar = 0;
		if (!this.shift != !this.capslock) vchar += 1;
	
		var tables = this.keyboard.select('table.keys');
		for (var x = 0; x < tables.getCount(); x++) {
			var tds = tables.item(x).select('td');
			for (var y = 0; y < tds.getCount(); y++) {
				td = tds.item(y);
				var dead = alive = isTarget = false;
				if(!this.Languages[this.language][x]){
					//alert('stop');
				}
				
				var lkey = this.Languages[this.language][x][y];
		
				switch (lkey[1]) {
					case "Alt":
					case "AltGr":
					if (this.alternate) dead = true;
					break;
					case "Shift":
					if (this.shift) dead = true;
					break;
					case "Caps":
					if (this.capslock) dead = true;
					break;
					case "Tab":
					case "Enter":
					case "Bksp":
						break;
					default:
						var char = lkey[vchar + ((this.alternate && lkey.length == 4) ? 2 : 0)];
						if (type)
							td.update(char);
						if (this.deadKeysOn) {
							if (this.dead) {
								if (char == this.dead)
									dead = true;
								for (var z = 0; z < this.deadKey[this.dead].length; z++){
									if (char == this.deadKey[this.dead][z][0]) {
										isTarget = true;
										break;
									}
								}
							}
							for (key in this.deadKey){
								if (key === char) {
									alive = true;
									break;
								}
							}
						}
				}
				td.dom.className = (dead) ? "dead" : ((isTarget) ? "target" : ((alive) ? "alive" : ""));
				if (y == tds.getCount() - 1 && tds.getCount() > this.keyCenter) td.addClass('last');
			}
		}
	},
	
	keyInsert: function(keyValue){
		this.fireEvent('keyPress', this, keyValue);
		
		if(this.keyboardTarget){
			this.keyboardTarget.focus();
			var dom = this.keyboardTarget.el.dom;
			if (dom.setSelectionRange) {
				var srt = dom.selectionStart;
				var len = dom.selectionEnd;
				dom.value = dom.value.substr(0, srt) + keyValue + dom.value.substr(len);
				if (keyValue == "\n" && window.opera) srt++;
				dom.setSelectionRange(srt + keyValue.length, srt + keyValue.length);
			} else if (dom.createTextRange) {
				try { this.range.select(); } catch(e) {}
				this.range = document.selection.createRange();
				this.range.text = keyValue;
				this.range.collapse(true);
				this.range.select();
			} else
				this.keyboardTarget.setValue(this.keyboardTarget.getValue() + keyValue);
			if (this.shift) this.keyModify("Shift");
			if (this.alternate) this.keyModify("AltGr");
			this.keyboardTarget.focus();
		}
	},

	getXType: function(){
		return 'virtualkeyboard';
	},
	
	IESel: function (event, target){
		if (target.createTextRange){
			this.range = document.selection.createRange();
		}
	}
});

Ext.reg('virtualkeyboard', Ext.ux.VirtualKeyboard);
