/**
 *  author:		Timothy Groves - http://www.brandspankingnew.net
 *	version:	1.2 - 2006-11-17
 *              1.3 - 2006-12-04
 *              2.0 - 2007-02-07
 *              2.1.1 - 2007-04-13
 *              2.1.2 - 2007-07-07
 *              2.1.3 - 2007-07-19
 *
 */

//static variable for suggest responses.  @By Erik <erik@colosa.com>
var Static_AutosuggestResponseData;
var swStoreEntry = 1;


if (typeof(bsn) == "undefined")
	_b = bsn = {};


if (typeof(_b.Autosuggest) == "undefined")
	_b.Autosuggest = {};
else
	alert("Autosuggest is already set!");

_b.AutoSuggest = function (id, param)
{
	// no DOM - give up!
	//
	if (!document.getElementById)
		return 0;

	// get field via DOM
	//
	this.fld = _b.DOM.gE(id);

	if (!this.fld)
		return 0;

	// init variables
	//
	this.sInp 	= "";
	this.nInpC 	= 0;
	this.aSug 	= [];
	this.iHigh 	= 0;

	// parameters object
	//
	this.oP = param ? param : {};

	// defaults
	//
	var k, def = {minchars:1, meth:"get", varname:"input", className:"autosuggest", timeout:5000, delay:50, offsety:-5, shownoresults: true, noresults: "No results!", maxheight: 250, cache: true, maxentries: 25};
                                                                                      //timeout::2500      //delay was modified of 5000 or 500 by 5, I don't sure.. ;) can't remember... <-erik notes->'
	for (k in def)
	{
		if (typeof(this.oP[k]) != typeof(def[k]))
			this.oP[k] = def[k];
	}

	// set keyup handler for field
	// and prevent autocomplete from client
	//
	var p = this;

	// NOTE: not using addEventListener because UpArrow fired twice in Safari
	//_b.DOM.addEvent( this.fld, 'keyup', function(ev){ return pointer.onKeyPress(ev); } );

	this.fld.onkeypress 	= function(ev){ return p.onKeyPress(ev); };
	this.fld.onkeyup 		= function(ev){ return p.onKeyUp(ev); };

	this.fld.setAttribute("autocomplete","off");
};


_b.AutoSuggest.prototype.onKeyPress = function (ev)
{
    var key = (window.event)? window.event.keyCode : ev.keyCode;

    var RETURN = 13;
    var TAB = 9;
    var ESC = 27;

    var bubble = 1;

    switch (key) {
        case RETURN:
            if (typeof this.oP.storeEntryData != "undefined" && this.oP.storeEntryData[0] == 1) {
                var elem = document.getElementById(this.oP.storeEntryData[1]);

                if (elem.value != "" && swStoreEntry == 1 &&
                    typeof Static_AutosuggestResponseData != "undefined" && Static_AutosuggestResponseData.results.length > 0
                ) {
                    for (var i = 0 ; i <= Static_AutosuggestResponseData.results.length - 1; i++) {
                        if (Static_AutosuggestResponseData.results[i].value == elem.value) {
                            swStoreEntry = 0;
                        }
                    }
                }

                if (elem.value == "") {
                    swStoreEntry = 0;
                }

                if (swStoreEntry == 1) {
                    storeEntryProcessAjax(
                        elem,
                        this.oP.storeEntryData[2],
                        this.oP.storeEntryData[3],
                        this.oP.storeEntryData[4],
                        this.oP.storeEntryData[5],
                        this.oP.storeEntryData[6]
                    );
                }
            }

            this.setHighlightedValue();
            bubble = 0;
            //added by erik, this is for enter press, no new line into text area
            return false;
            break;
        case ESC:
            this.clearSuggestions();
            break;
        default:
            swStoreEntry = 1;
            break;
    }

    return bubble;
};

_b.AutoSuggest.prototype.onKeyUp = function (ev)
{
    var key = (window.event)? window.event.keyCode : ev.keyCode;

    var ARRUP = 38;
    var ARRDN = 40;

    var bubble = 1;

    switch (key) {
        case ARRUP:
            this.changeHighlight(key);
            this.setHighlightedValue2();
            bubble = 0;
            swStoreEntry = 0;
            break;
        case ARRDN:
            this.changeHighlight(key);
            this.setHighlightedValue2();
            bubble = 0;
            swStoreEntry = 0;
            break;
        default:
            this.getSuggestions(this.fld.value);
            break;
    }

    return bubble;
};

_b.AutoSuggest.prototype.getSuggestions = function (val)
{

	// if input stays the same, do nothing
	//
	if (val == this.sInp)
		return 0;

	// kill list
	//
	_b.DOM.remE(this.idAs);


	this.sInp = val;

	// input length is less than the min required to trigger a request
	// do nothing
	//
	if (val.length < this.oP.minchars)
	{
		this.aSug = [];
		this.nInpC = val.length;
		return 0;
	}

	var ol = this.nInpC; // old length
	this.nInpC = val.length ? val.length : 0;

	// if caching enabled, and user is typing (ie. length of input is increasing)
	// filter results out of aSuggestions from last request
	//
	var l = this.aSug.length;
	if (this.nInpC > ol && l && l<this.oP.maxentries && this.oP.cache)
	{
		var arr = [];
		for (var i=0;i<l;i++)
		{
			// if (this.aSug[i].value.substr(0,val.length).toLowerCase() == val.toLowerCase())
			if (this.aSug[i].value.substr(0,val.length).toLowerCase() == val.toLowerCase() ||
			    this.aSug[i].value.toLowerCase().indexOf(val.toLowerCase()) > 0)
				arr.push( this.aSug[i] );
		}
		this.aSug = arr;

		this.createList(this.aSug);
		return false;
	}
	else
	// do new request
	//
	{
		var pointer = this;
		var input = this.sInp;
		clearTimeout(this.ajID);
		this.ajID = setTimeout( function() { pointer.doAjaxRequest(input) }, this.oP.delay );
	}

	return false;
};





_b.AutoSuggest.prototype.doAjaxRequest = function (input)
{
	// check that saved input is still the value of the field
	//
	if (input != this.fld.value)
		return false;

	var pointer = this;

	// create ajax request
	//
	if (typeof(this.oP.script) == "function")
		var url = this.oP.script(encodeURIComponent(this.sInp));
	else
		var url = this.oP.script+this.oP.varname+"="+encodeURIComponent(this.sInp);

	if (!url)
		return false;

	var meth = this.oP.meth;
	var input = this.sInp;

	var onSuccessFunc = function (req) { pointer.setSuggestions(req, input) };
	var onErrorFunc = function (status) { alert("AJAX error: "+status); };

	var myAjax = new _b.Ajax();
	myAjax.makeRequest( url, meth, onSuccessFunc, onErrorFunc );
};

_b.AutoSuggest.prototype.setSuggestions = function (req, input)
{
	// if field input no longer matches what was passed to the request
	// don't show the suggestions
	//
	if (input != this.fld.value)
		return false;

	this.aSug = [];


	if (this.oP.json)
	{

		var jsondata = eval('(' + req.responseText + ')');

    if( jsondata.status == 0 ){

      Static_AutosuggestResponseData = jsondata;

      for (var i=0;i<jsondata.results.length;i++)
      {
        this.aSug.push(  { 'id':jsondata.results[i].id, 'value':jsondata.results[i].value, 'info':jsondata.results[i].info }  );
      }
    } else {
      //alert('\t\t\t --= AUTO SUGGEST PM FIELD ERROR =-- \nYou have a SQL definition error, the database engine response was:\n\n'+jsondata.message);
      return false;
    }
	}
	else
	{

		var xml = req.responseXML;

		// traverse xml
		//
		var results = xml.getElementsByTagName('results')[0].childNodes;

		for (var i=0;i<results.length;i++)
		{
			if (results[i].hasChildNodes())
				this.aSug.push(  { 'id':results[i].getAttribute('id'), 'value':results[i].childNodes[0].nodeValue, 'info':results[i].getAttribute('info') }  );
		}

	}

	this.idAs = "as_"+this.fld.id;


	this.createList(this.aSug);

};

_b.AutoSuggest.prototype.createList = function(arr)
{
	var pointer = this;

	_b.DOM.remE(this.idAs);
	this.killTimeout();


	// if no results, and shownoresults is false, do nothing
	//
	if (arr.length == 0 && !this.oP.shownoresults)
		return false;

	// create holding div
	//
	var div = _b.DOM.cE("div", {id:this.idAs, className:this.oP.className});

	// create and populate ul
	//
	var ul = _b.DOM.cE("ul", {id:"as_ul"});

	// loop throught arr of suggestions
	// creating an LI element for each suggestion
	//
	for (var i = 0; (i < arr.length) && (i < this.oP.maxresults); i++)
	{
		// format output with the input enclosed in a EM element
		// (as HTML, not DOM)
		//
		var val = arr[i].value;
		var st = val.toLowerCase().indexOf( this.sInp.toLowerCase() );
		var output = val.substring(0,st) + "<em>" + val.substring(st, st+this.sInp.length) + "</em>" + val.substring(st+this.sInp.length);


		var span 		= _b.DOM.cE("span", {}, output, true);
		if (arr[i].info != "")
		{
			var br			= _b.DOM.cE("br", {});
			span.appendChild(br);
			var small		= _b.DOM.cE("small", {}, arr[i].info);
			span.appendChild(small);
		}

		var a 			= _b.DOM.cE("a", { href:"#" });

		var tl 		= _b.DOM.cE("span", {className:"tl"}, " ");
		var tr 		= _b.DOM.cE("span", {className:"tr"}, " ");
		a.appendChild(tl);
		a.appendChild(tr);

		a.appendChild(span);

		a.name = i+1;
		a.onclick = function () { pointer.setHighlightedValue(); return false; };
		a.onmouseover = function () {
                     pointer.setHighlight(this.name);
                     // Commented by Bug 6672
                     // pointer.setHighlightedValue2();

                };

		var li = _b.DOM.cE(  "li", {}, a  );

		ul.appendChild( li );
	}


	// no results
	//
	if (arr.length == 0 && this.oP.shownoresults)
	{
		var li = _b.DOM.cE(  "li", {className:"as_warning"}, this.oP.noresults  );
		ul.appendChild( li );

    //By Erik AO <erik@colosa.com>
    Static_AutosuggestResponseData.results.length = 0;
	}

ul.style.cssText = "zoom: 1; padding-top: 4px;";

div.appendChild(ul);

	// get position of target textfield
	// position holding div below it
	// set width of holding div to width of field
	//

var pos = _b.DOM.getPos(this.fld);

var divPosX = pos.x - 3;
var divPosY = pos.y - 4;
var divW = this.fld.offsetWidth;
var divH = 250;

if (navigator.userAgent.toLowerCase().indexOf("msie") != -1) {
    var divPosX = pos.x - 2;
    var divPosY = pos.y - 4;
    var divW = this.fld.offsetWidth + 5;
    //divH = 250;
}

div.style.left = divPosX + "px";
div.style.top  = (divPosY + this.fld.offsetHeight + this.oP.offsety) + "px";
div.style.width  = divW + "px";
div.style.height = divH + "px";

	// set mouseover functions for div
	// when mouse pointer leaves div, set a timeout to remove the list after an interval
	// when mouse enters div, kill the timeout so the list won't be removed
	//
	div.onmouseover 	= function(){ pointer.killTimeout() };
	div.onmouseout 		= function(){ pointer.resetTimeout() };

	// add DIV to document
	//
	document.getElementsByTagName("body")[0].appendChild(div);

	// currently no item is highlighted
	//
	this.iHigh = 0;

	// remove list after an interval
	//
	var pointer = this;
	this.toID = setTimeout(function () { pointer.clearSuggestions() }, this.oP.timeout);
};

_b.AutoSuggest.prototype.changeHighlight = function(key)
{
	var list = _b.DOM.gE("as_ul");
	if (!list)
		return false;

	var n;

	if (key == 40)
		n = this.iHigh + 1;
	else if (key == 38)
		n = this.iHigh - 1;


	if (n > list.childNodes.length)
		n = list.childNodes.length;
	if (n < 1)
		n = 1;


	this.setHighlight(n);
};

_b.AutoSuggest.prototype.setHighlight = function(n)
{
	var list = _b.DOM.gE("as_ul");
	if (!list)
		return false;

	if (this.iHigh > 0)
		this.clearHighlight();

	this.iHigh = Number(n);

	list.childNodes[this.iHigh-1].className = "as_highlight";


	this.killTimeout();
};

_b.AutoSuggest.prototype.clearHighlight = function()
{
	var list = _b.DOM.gE("as_ul");
	if (!list)
		return false;

	if (this.iHigh > 0)
	{
		list.childNodes[this.iHigh-1].className = "";
		this.iHigh = 0;
	}
};


_b.AutoSuggest.prototype.setHighlightedValue = function ()
{
	if (this.iHigh)
	{
		/**
		 * @autor Erik Amaru Ortiz <erik@colosa.com>
		 * This fix when a item on suggestion list have html wntities sent by json like &amp;
		 */
    if(this.aSug[ this.iHigh-1 ])
		this.sInp = this.fld.value = html_entity_decode(this.aSug[ this.iHigh-1 ].value);;
		//

		// move cursor to end of input (safari)
		//
		this.fld.focus();
		if (this.fld.selectionStart)
			this.fld.setSelectionRange(this.sInp.length, this.sInp.length);


		this.clearSuggestions();

		// pass selected object to callback function, if exists
		//
		if (typeof(this.oP.callback) == "function")
			this.oP.callback( this.aSug[this.iHigh-1] );
	}
};

_b.AutoSuggest.prototype.setHighlightedValue2 = function ()
{
	if (this.iHigh)
	{
    if(this.aSug[ this.iHigh-1 ])
		this.sInp = this.fld.value = html_entity_decode(this.aSug[ this.iHigh-1 ].value);

		// move cursor to end of input (safari)
		//
		/*this.fld.focus();
		if (this.fld.selectionStart)
			this.fld.setSelectionRange(this.sInp.length, this.sInp.length);

                        */
		//this.clearSuggestions();

		// pass selected object to callback function, if exists
		//
		/*if (typeof(this.oP.callback) == "function")
			this.oP.callback( this.aSug[this.iHigh-1] );*/
	}
};

_b.AutoSuggest.prototype.killTimeout = function()
{
	clearTimeout(this.toID);
};

_b.AutoSuggest.prototype.resetTimeout = function()
{
	clearTimeout(this.toID);
	var pointer = this;
	this.toID = setTimeout(function () { pointer.clearSuggestions() }, 500);  // was modifiend,.. its original value was 1000 by 10  <-erik notes->
};

_b.AutoSuggest.prototype.clearSuggestions = function ()
{

	this.killTimeout();

	var ele = _b.DOM.gE(this.idAs);
	var pointer = this;
	if (ele)
	{
		var fade = new _b.Fader(ele,1,0,250,function () { _b.DOM.remE(pointer.idAs) });
	}
};

// AJAX PROTOTYPE _____________________________________________


if (typeof(_b.Ajax) == "undefined")
	_b.Ajax = {};

_b.Ajax = function ()
{
	this.req = {};
	this.isIE = false;
};

_b.Ajax.prototype.makeRequest = function (url, meth, onComp, onErr)
{

	if (meth != "POST")
		meth = "GET";

	this.onComplete = onComp;
	this.onError = onErr;

	var pointer = this;

	// branch for native XMLHttpRequest object
	if (window.XMLHttpRequest)
	{
		this.req = new XMLHttpRequest();
		this.req.onreadystatechange = function () { pointer.processReqChange() };
		this.req.open("GET", url, true); //
		this.req.send(null);
	// branch for IE/Windows ActiveX version
	}
	else if (window.ActiveXObject)
	{
		this.req = new ActiveXObject("Microsoft.XMLHTTP");
		if (this.req)
		{
			this.req.onreadystatechange = function () { pointer.processReqChange() };
			this.req.open(meth, url, true);
			this.req.send();
		}
	}
};

_b.Ajax.prototype.processReqChange = function()
{

	// only if req shows "loaded"
	if (this.req.readyState == 4) {
		// only if "OK"
		if (this.req.status == 200)
		{
			this.onComplete( this.req );
		} else {
			this.onError( this.req.status );
		}
	}
};

// DOM PROTOTYPE _____________________________________________

if (typeof(_b.DOM) == "undefined")
	_b.DOM = {};

/* create element */
_b.DOM.cE = function ( type, attr, cont, html )
{
	var ne = document.createElement( type );
	if (!ne)
		return 0;

	for (var a in attr)
		ne[a] = attr[a];

	var t = typeof(cont);

	if (t == "string" && !html)
		ne.appendChild( document.createTextNode(cont) );
	else if (t == "string" && html)
		ne.innerHTML = cont;
	else if (t == "object")
		ne.appendChild( cont );

	return ne;
};

/* get element */
_b.DOM.gE = function ( e )
{
	var t=typeof(e);
	if (t == "undefined")
		return 0;
	else if (t == "string")
	{
		var re = document.getElementById( e );
		if (!re)
			return 0;
		else if (typeof(re.appendChild) != "undefined" )
			return re;
		else
			return 0;
	}
	else if (typeof(e.appendChild) != "undefined")
		return e;
	else
		return 0;
};

/* remove element */
_b.DOM.remE = function ( ele )
{
	var e = this.gE(ele);

	if (!e)
		return 0;
	else if (e.parentNode.removeChild(e))
		return true;
	else
		return 0;
};

/* get position */
_b.DOM.getPos = function ( e )
{
	var e = this.gE(e);

	var obj = e;

	var curleft = 0;
	if (obj.offsetParent)
	{
		while (obj.offsetParent)
		{
			curleft += obj.offsetLeft;
			obj = obj.offsetParent;
		}
	}
	else if (obj.x)
		curleft += obj.x;

	var obj = e;

	var curtop = 0;
	if (obj.offsetParent)
	{
		while (obj.offsetParent)
		{
			curtop += obj.offsetTop;
			obj = obj.offsetParent;
		}
	}
	else if (obj.y)
		curtop += obj.y;

	return {x:curleft, y:curtop};
};

// FADER PROTOTYPE _____________________________________________

if (typeof(_b.Fader) == "undefined")
	_b.Fader = {};

_b.Fader = function (ele, from, to, fadetime, callback)
{
	if (!ele)
		return 0;

	this.e = ele;

	this.from = from;
	this.to = to;

	this.cb = callback;

	this.nDur = fadetime;

	this.nInt = 50;
	this.nTime = 0;

	var p = this;
	this.nID = setInterval(function() { p._fade() }, this.nInt);
};

_b.Fader.prototype._fade = function()
{
	this.nTime += this.nInt;

	var ieop = Math.round( this._tween(this.nTime, this.from, this.to, this.nDur) * 100 );
	var op = ieop / 100;

	if (this.e.filters) // internet explorer
	{
		try
		{
			this.e.filters.item("DXImageTransform.Microsoft.Alpha").opacity = ieop;
		} catch (e) {
			// If it is not set initially, the browser will throw an error.  This will set it if it is not set yet.
			this.e.style.filter = 'progid:DXImageTransform.Microsoft.Alpha(opacity='+ieop+')';
		}
	}
	else // other browsers
	{
		this.e.style.opacity = op;
	}


	if (this.nTime == this.nDur)
	{
		clearInterval( this.nID );
		if (this.cb != undefined)
			this.cb();
	}
};

_b.Fader.prototype._tween = function(t,b,c,d)
{
	return b + ( (c-b) * (t/d) );
};

function storeEntryMessageHide(id)
{
    if (document.getElementById(id)) {
        document.getElementById(id).parentNode.removeChild(document.getElementById(id));
    }
}

function storeEntryProcessAjax(o, cnn, table, pk, pkt, fld)
{
    var myAjax = new _b.Ajax();

    myAjax.makeRequest(
        "../gulliver/genericAjax?request=storeInTmp" + "&cnn=" + cnn + "&table=" + table + "&pk=" + pk + "&pkt=" + pkt + "&fld=" + fld + "&value=" + o.value,
        "POST",
        function (req)
        {
            var response = eval("(" + req.responseText + ")");

            switch (response.status) {
                case 1:
                    var w1 = document.documentElement.clientWidth;
                    var sbX1 = document.documentElement.scrollLeft;
                    var sbY1 = document.documentElement.scrollTop;

                    var w2 = document.body.clientWidth;
                    var sbX2 = document.body.scrollLeft;
                    var sbY2 = document.body.scrollTop;

                    var bodyW = (w1 > 0)? w1 : w2;
                    var scrollbarX = (sbX1 > 0)? sbX1 : sbX2;
                    var scrollbarY = (sbY1 > 0)? sbY1 : sbY2;

                    storeEntryMessageHide("myIframe");

                    var myIframe = document.createElement("iframe");
                    myIframe.setAttribute("id", "myIframe");
                    myIframe.style.position = "absolute";
                    myIframe.style.left = (parseInt(bodyW / 2) - 200 + scrollbarX) + "px";
                    myIframe.style.top  = (scrollbarY + 5) + "px";
                    myIframe.src = "about:blank";
                    myIframe.frameBorder = 0;
                    myIframe.scrolling = "no";
                    myIframe.style.width  = "400px";
                    myIframe.style.height = "45px";

                    leimnud.event.add(
                        myIframe,
                        "load",
                        function (evt)
                        {
                            document.getElementById("myIframe").contentWindow.document.body.style.margin = 0;
                            document.getElementById("myIframe").contentWindow.document.body.style.padding = 0;
                            document.getElementById("myIframe").contentWindow.document.body.innerHTML = "<div style=\"border: 1px solid #808080; width: 398px; height: 43px; background: #ADDCC7; font:0.9em arial, verdana, helvetica, sans-serif;\"><div style=\"margin: 0.5em 0 0 0.5em;\"><img src=\"/images/documents/_accept.png\" alt=\"\" style=\"margin-right: 0.8em; vertical-align: middle;\" />" + _("ID_FIELD_DYNAFORM_SUGGEST_MESSAGE_TEMPORAL") + "</div></div>";
                        }
                    );

                    document.body.appendChild(myIframe);

                    setTimeout("storeEntryMessageHide(\"myIframe\")", 1750);

                    swStoreEntry = 0;
                    break;
                default:
                    alert(response.message);
                    break;
            }
        },
        function (req)
        {
            //alert(req);
        }
    );
}

function html_entity_decode( string, quote_style ) {
    var histogram = {}, symbol = '', tmp_str = '', entity = '';
    tmp_str = string.toString();

    if (false === (histogram = get_html_translation_table('HTML_ENTITIES', quote_style))) {
        return false;
    }

    // &amp; must be the last character when decoding!
    delete(histogram['&']);
    histogram['&'] = '&amp;';

    for (symbol in histogram) {
        entity = histogram[symbol];
        tmp_str = tmp_str.split(entity).join(symbol);
    }

    return tmp_str;
}

