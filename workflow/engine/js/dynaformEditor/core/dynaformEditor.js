var xmlEditor = null;
var jsEditor  = null;

var clientWinSize = null;

var strError = "";
var h3OK = 1;

var promptPanel;
var lastActionPerformed = '';
var lastTypeSelected = '';

var sessionPersits = function() {
    var rpc = new leimnud.module.rpc.xmlhttp({
        url: '../services/sessionPersists',
        args: 'dynaformEditorParams=' + dynaformEditorParams,
        async: false
    });
    rpc.make();
    var response = rpc.xmlhttp.responseText.parseJSON();
    return response.status;
};

var verifyLogin = function() {
    if (document.getElementById('thePassword').value.trim() == '') {
        alert(_('ID_WRONG_PASS'));
        return;
    }
	var rpc = new leimnud.module.rpc.xmlhttp({
        url : '../login/authentication',
        args: 'form[USR_USERNAME]=' + __usernameLogged__ + '&form[USR_PASSWORD]=' + document.getElementById('thePassword').value.trim() + '&form[USR_LANG]=' + SYS_LANG
    });
    rpc.callback = function(rpc) {
        if (rpc.xmlhttp.responseText.indexOf('form[USR_USERNAME]') == -1) {
            promptPanel.remove();
            switch (lastActionPerformed) {
                case 'save':
                    dynaformEditor.save();
                    break;
                case 'save_as':
                    dynaformEditor.save_as();
                    break;
                case 'saveJavascript':
                    dynaformEditor.saveJavascript();
                    break;
                case 'changeJavascriptCode':
                    dynaformEditor.changeJavascriptCode(false);
                    break;
                case 'close':
                    dynaformEditor.close();
                    break;
                case 'saveProperties':
                    dynaformEditor.saveProperties(false);
                    break;
                case 'changeFormType':
                    changeFormType(false);
                    break;
                case 'changeToPreview':
                    dynaformEditor.changeToPreview();
                    break;
                case 'changeToXmlCode':
                    dynaformEditor.changeToXmlCode();
                    break;
                case 'changeToHtmlCode':
                    dynaformEditor.changeToHtmlCode();
                    break;
                case 'changeToFieldsList':
                    dynaformEditor.changeToFieldsList();
                    break;
                case 'changeToJavascripts':
                    dynaformEditor.changeToJavascripts();
                    break;
                case 'changeToProperties':
                    dynaformEditor.changeToProperties();
                    break;
                case 'changeToShowHide':
                    dynaformEditor.changeToShowHide();
                    break;
                case 'refreshDynaformEditor':
                    refreshDynaformEditor();
                    break;
                case 'fieldsSave':
                    fieldsSave(getField('PME_XMLNODE_NAME').form);
                    break;
                case 'fieldsAdd':
                    fieldsAdd(lastTypeSelected);
                    break;
                case '__ActionEdit':
                    document.getElementById('dynaframe').contentWindow.__ActionEdit(document.getElementById('dynaframe').contentWindow.lastUidFHSelected);
                    break;
                case '__ActionDelete':
                    document.getElementById('dynaframe').contentWindow.__ActionDelete(document.getElementById('dynaframe').contentWindow.lastUidFHSelected, document.getElementById('dynaframe').contentWindow.lastFTypeFHSelected);
                    break;
            }
            lastActionPerformed = '';
        } else {
            alert(_('ID_WRONG_PASS'));
        }
    }.extend(this);
    rpc.make();
};

var showPrompt = function(lastAction) {
    lastActionPerformed = lastAction;
    promptPanel = new leimnud.module.panel();
    promptPanel.options={
    	statusBarButtons:[{value: _('LOGIN')}],
    	position:{center:true},
        size:{w:300,h:125},
    	control:{
    		close:false,
    		resize:false
    	},
    	fx:{
    		modal:true
    	}
    };
    promptPanel.setStyle={
    	content:{
    		padding:10,
    		paddingBottom:2,
    		textAlign:'left',
    		paddingLeft:50,
    		backgroundRepeat:'no-repeat',
    		backgroundPosition:'10 50%',
    		backgroundColor:'transparent',
    		borderWidth:0
    	}
    };
    promptPanel.make();
    promptPanel.addContent(_('ID_DYNAFORM_EDITOR_LOGIN_AGAIN'));
    promptPanel.addContent('<br />');
    var thePassword = $dce('input');
    thePassword.type = 'password'
    thePassword.id = 'thePassword';
    leimnud.dom.setStyle(thePassword,{
    	font:'normal 8pt Tahoma,MiscFixed',
    	color:'#000',
    	width:'100%',
    	marginTop:3,
    	backgroundColor:'white',
    	border:'1px solid #919B9C'
    });
    promptPanel.addContent(thePassword);
    thePassword.focus();
    thePassword.onkeyup=function(evt)
    {
    	var evt = (window.event)?window.event:evt;
    	var key = (evt.which)?evt.which:evt.keyCode;
    	if(key == 13) {
    		verifyLogin();
    	}
    }.extend(this);
    promptPanel.fixContent();
    promptPanel.elements.statusBarButtons[0].onmouseup = verifyLogin;
};

function checkErrorXML(xmlParse)
{
    strError = "";
    h3OK = 1;
    checkXML(xmlParse);
}

function checkXML(nodeXml)
{
    var line, i, nNode;
    nNode = nodeXml.nodeName;
    if (nNode == "h3") {
        if (h3OK == 0) {
            return;
        }
        h3OK = 0;
    }
    if (nNode == "#text") {
        strError = nodeXml.nodeValue + "\n";
    }
    line = nodeXml.childNodes.length;
    for (i = 0;i < line; i++) {
        checkXML(nodeXml.childNodes[i]);
    }
}

function validateXML(xmlString)
{
    // code for IE
    if (window.ActiveXObject) {
        var xmlDoc = new ActiveXObject("Microsoft.XMLDOM");
        xmlDoc.async = "false";
        xmlDoc.loadXML(xmlString);
        if (xmlDoc.parseError.errorCode != 0) {
            xmlString = "Error Code: " + xmlDoc.parseError.errorCode + "\n";
            xmlString = xmlString + "Error Reason: " + xmlDoc.parseError.reason;
            xmlString = xmlString + "Error Line: " + xmlDoc.parseError.line;
            alert(xmlString);
            return false;
        } else {
            return true;
        }
    } else {
        // code for Mozilla, Firefox, Opera, etc.
        if (document.implementation.createDocument) {
            var parser = new DOMParser();
            var text = xmlString;
            var xmlDoc = parser.parseFromString(text, "text/xml");

            if (xmlDoc.getElementsByTagName("parsererror").length > 0) {
                checkErrorXML(xmlDoc.getElementsByTagName("parsererror")[0]);
                alert(strError);
                return false;
            } else {
                return true;
            }
        } else {
            alert('Your browser cannot handle XML validation');
            return false;
        }
    }
}

if (typeof(dynaformEditor)==="undefined")
{
var dynaformEditor={
  A:"",
  dynUid:"",
  ajax:"",
  currentView:"preview",
  views:{},
  toolbar:{},
  htmlEditorLoaded:false,
  loadPressLoaded:true,
  codePressLoaded:false,
  currentJS:false,
  responseAction:true,
  _run:function()
  {
    //LOADING PARTS
    this.toolbar = document.getElementById("fields_Toolbar")
    mainPanel.elements.headerBar.style.backgroundColor="#CBDAEF";
    mainPanel.elements.headerBar.style.borderBottom="1px solid #808080";
    mainPanel.elements.headerBar.appendChild(this.toolbar);
    mainPanel.events.remove = function(){
    }
// note added by krlos pacha carlos[at]colosa[dot]com
// the following line of code has been commented because it was executing twice the JavaScript code
// when the DynaForm was first loaded.
//    this.refresh_preview();
    this.changeToJavascripts(false);
    this.changeToPreview(false);
  },
  _review:function()
  {

  },
  save:function() {
    if (!sessionPersits()) {
        showPrompt('save');
        return;
    }
    try {
      this.saveCurrentView();
    } catch (e) {
      alert(e);
    }
    if (this.responseAction == true) {
        res = this.ajax.save(this.A,this.dynUid);
        if (res == 'noSub') {
            alert(G_STRINGS.ID_DONT_SAVE_XMLFORM);
            return false;
        }
        if (res==0) {
            document.getElementById('_dynaformsList_').options[document.getElementById('_dynaformsList_').selectedIndex].text = getField('DYN_TITLE', 'dynaforms_Properties').value;
            alert(G_STRINGS.ID_SAVED);
        } else {
            if (typeof(res.innerHTML) == 'undefined') {
                G.alert(res["*message"]);
            }
        }
    }
  },
  save_as:function(){
    if (!sessionPersits()) {
        showPrompt('save_as');
        return;
    }
    try {
      this.saveCurrentView();
    } catch (e) {
      alert(e);
    }
    url='dynaforms_Saveas';
    popupWindow('Save as', url+'?DYN_UID='+this.dynUid+'&AA='+this.A , 500, 350);
  },
  close:function()
  {
    if (!sessionPersits()) {
        showPrompt('close');
        return;
    }
    var modified=this.ajax.is_modified(this.A,this.dynUid);
    if (typeof(modified)==="boolean")
    {
      if (!modified || confirm(G_STRINGS.ID_EXIT_WITHOUT_SAVING))
      {
        res=this.ajax.close(this.A);
        if (res==0) {
          //alert(G_STRINGS.ID_DYNAFORM_NOT_SAVED);
        }
        else
        {
          alert(res["*message"]);
        }
        return true;
      }
      else
      {
        return false;
      }
    }
    else
    {
      if (typeof(modified["*message"])==="string") G.alert(modified["*message"]);
      return false;
    }
  },
  // Save functions
  saveCurrentView:function()
  {
    switch(this.currentView)
    {
      case "xmlcode":
        this.saveXmlCode();
        break;
      case "htmlcode":
        this.saveHtmlCode();
        break;
      case "javascripts":
        this.saveJavascript();
        break;
      case "properties":
        this.saveProperties(false);
        break;
    }
  },

  saveShowHide:function()
  {
    try {
      this.saveCurrentView();
    } catch (e) {
      alert(e);
    }
    res=this.ajax.save(this.A,this.dynUid);
    switch(this.currentView)
    {
      case "xmlcode":
        this.saveXmlCode();
        break;
      case "htmlcode":
        this.saveHtmlCode();
        break;
      case "javascripts":
        this.saveJavascript();
        break;
      case "properties":
        this.saveProperties(false);
        break;
    }
  },

  saveXmlCode:function()
  {
    var xmlCode = this.getXMLCode();
    if (validateXML(xmlCode) == true) {
        var todoRefreshXmlCode = xmlCode === null;
        if (todoRefreshXmlCode) return;
        var res = this.ajax.set_xmlcode(this.A, encodeURIComponent(xmlCode));
        if (res!=="" && typeof(res) == 'string') G.alert(res);
        this.responseAction = true;
    } else {
        this.responseAction = false;
    }
  },
  saveHtmlCode:function()
  {
    //var htmlCode = getField("HTML");
    var response = this.ajax.set_htmlcode(this.A, tinyMCE.activeEditor.getContent());

    if (response) {
        if (typeof(response["*message"]) != 'undefined') {
            G.alert(response["*message"],"Error");
            this.responseAction = false;
        }
    } else {
        this.responseAction = true;
    }
  },
  saveJavascript:function()
  {
    var field=getField("JS_LIST","dynaforms_JSEditor");
    var code=this.getJSCode();
    var meta=jsMeta;

    if (field.value)
    {
      var res = this.ajax.set_javascript(this.A,field.value, encodeURIComponent(code), meta);
      if (typeof(res["*message"])==="string")
      {
        G.alert(res["*message"]);
      }
    }
    this.responseAction = true;
  },
  saveProperties:function(checkSessionPersists)
  {
    checkSessionPersists = typeof(checkSessionPersists) != 'undefined' ? checkSessionPersists : true;
    if (checkSessionPersists) {
        if (!sessionPersits()) {
            showPrompt('saveProperties');
            return;
        }
    }
    var form=this.views["properties"].getElementsByTagName("form")[0];
    var post=ajax_getForm(form);
    var response=this.ajax.set_properties(this.A,this.dynUid,post);
    if (typeof(response["*message"])==="string") {
        G.alert(response["*message"]);
    }
    this.responseAction = true;
  },
  // Change view point functions
  changeToPreview:function(checkSessionPersists)
  {
    checkSessionPersists = typeof(checkSessionPersists) != 'undefined' ? checkSessionPersists : true;
    if (checkSessionPersists) {
        if (!sessionPersits()) {
            showPrompt('changeToPreview');
            return;
        }
    }
    //to adecuate the view perspective @Neyek
    content_div = getElementByPMClass('panel_content___processmaker')
    content_div.style.overflow='auto';
    if (this.currentView!="preview")this.refresh_preview();
    this.currentView="preview";
  },
  changeToXmlCode:function()
  {
    if (!sessionPersits()) {
        showPrompt('changeToXmlCode');
        return;
    }
    content_div = getElementByPMClass('panel_content___processmaker')
    content_div.style.overflow='auto';

    this.refresh_xmlcode();
    this.currentView="xmlcode";
    if( ! xmlEditor ) {
      clientWinSize = getClientWindowSize();

        /*xmlEditor = CodeMirror.fromTextArea('form[XML]', {
        height: (clientWinSize.height - 120) + "px",
        width: (_BROWSER.name == 'msie' ? '100%' : '98%'),
        parserfile: ["parsexml.js", "parsecss.js", "tokenizejavascript.js", "parsejavascript.js",
                     "../contrib/php/js/tokenizephp.js", "../contrib/php/js/parsephp.js",
                     "../contrib/php/js/parsephphtmlmixed.js"],
        stylesheet: ["css/xmlcolors.css", "css/jscolors.css"],
        path: "js/",
        lineNumbers: true,
        continuousScanning: 500 });*/

        xmlEditor = CodeMirror.fromTextArea(document.getElementById("form[XML]"), {
        mode: "application/xml",
        styleActiveLine: true,
        lineNumbers: true,
        lineWrapping: true });
    }
  },
  changeToHtmlCode:function()
  {
    if (!sessionPersits()) {
        showPrompt('changeToHtmlCode');
        return;
    }
    //to adecuate the view perspective @Neyek
    content_div = getElementByPMClass('panel_content___processmaker')
    content_div.style.overflow='auto';

    this.refresh_htmlcode();
    this.currentView="htmlcode";
  },
  changeToFieldsList:function()
  {
    if (!sessionPersits()) {
        showPrompt('changeToFieldsList');
        return;
    }
    //to adecuate the view perspective @Neyek
    content_div = getElementByPMClass('panel_content___processmaker')
    content_div.style.overflow='visible';

    this.refreshFieldsList();
    this.currentView="fieldslist";
  },
  changeToJavascripts:function(checkSessionPersists)
  {
    checkSessionPersists = typeof(checkSessionPersists) != 'undefined' ? checkSessionPersists : true;
    if (checkSessionPersists) {
        if (!sessionPersits()) {
            showPrompt('changeToJavascripts');
            return;
        }
    }
    var field=getField("JS_LIST","dynaforms_JSEditor");
    var res=this.ajax.get_javascripts(this.A,field.value);

    this.currentView="javascripts";
    this.refreshJavascripts();

    if(field.value!='' || typeof(res.aOptions[0])!='undefined'){
      hideRowById('JS_TITLE');
      showRowById('JS');
      showRowById('JS_LIST');

      //to adecuate the view perspective @Neyek
      content_div = getElementByPMClass('panel_content___processmaker')
      content_div.style.overflow='auto';

      //this.currentView="javascripts";
      //this.refreshJavascripts();
      //if (this.loadPressLoaded && !JSCodePress)
      if( ! jsEditor )
      {
        clientWinSize = getClientWindowSize();
        startJSCodePress();

        /*jsEditor = CodeMirror.fromTextArea('form[JS]', {
          height: (clientWinSize.height - 140) + "px",
          width: (_BROWSER.name == 'msie' ? '100%' : '98%'),
          parserfile: ["tokenizejavascript.js", "parsejavascript.js"],
          stylesheet: ["css/jscolors.css"],
          path: "js/",
          lineNumbers: true,
          continuousScanning: 500 });*/

          jsEditor = CodeMirror.fromTextArea(document.getElementById("form[JS]"), {
          mode: "javascript",
          lineNumbers: true,
          lineWrapping: true });
      }
    } else {
      showRowById('JS_TITLE');
      hideRowById('JS');
      hideRowById('JS_LIST');
    }
  },
  changeToProperties:function()
  {
    if (!sessionPersits()) {
        showPrompt('changeToProperties');
        return;
    }
    //to adecuate the view perspective @Neyek
    content_div = getElementByPMClass('panel_content___processmaker')
    content_div.style.overflow='auto';

    this.currentView="properties";
    this.refreshProperties();
  },
  changeToShowHide:function()
  {
    if (!sessionPersits()) {
        showPrompt('changeToShowHide');
        return;
    }
    //to adecuate the view perspective @Neyek
    content_div = getElementByPMClass('panel_content___processmaker')
    content_div.style.overflow='auto';
    this.currentView="showHide";
    this.refreshShowHide();
  },
  // Refresh functions
  refreshCurrentView:function()
  {
    switch(this.currentView)
    {
      case "preview":this.refresh_preview();break;
      case "htmlcode":this.refresh_htmlcode();break;
      case "xmlcode":this.refresh_xmlcode();break;
      case "fieldslist":this.refreshFieldsList();break;
      case "javascripts": this.changeJavascriptCode();
                          this.changeToJavascripts();
                          // this.refreshJavascripts();
                          break;
      case "properties":this.refreshProperties();break;
    }
  },
  refresh_preview:function()
  {
    var editorPreview = document.getElementById("editorPreview");
    var  todoRefreshPreview = editorPreview === null;
    if (todoRefreshPreview) return;
    editorPreview.innerHTML = this.ajax.render_preview(this.A);
    var myScripts = editorPreview.getElementsByTagName("SCRIPT");
    this.runScripts(myScripts);
    delete myScripts;
  },
  refresh_htmlcode:function()
  {
    var dynaformEditorHTML = this.views["htmlcode"];
    if (this.htmlEditorLoaded)
    {
      var response=this.ajax.get_htmlcode(this.A);
      response={"html":response,
        "error":((typeof(response)==="string")?0:response)};
    }
    else
    {
      var response=this.ajax.render_htmledit(this.A);
    }
    if ((response.error==0) && (this.htmlEditorLoaded))
    {
        // window._editorHTML.doc.body.innerHTML=response.html;
        //html_html2();
        //html2_html();
        var htmlContent = this.ajax.get_htmlcode(this.A);
        tinyMCE.activeEditor.execCommand('mceSetContent', false, htmlContent);
    }
    else if ((response.error==0) && (!this.htmlEditorLoaded))
    {
      dynaformEditorHTML.innerHTML=response.html;
      this.runScripts(dynaformEditorHTML.getElementsByTagName("SCRIPT"));
      this.htmlEditorLoaded=true;
    }
    else
    {
      dynaformEditorHTML.innerHTML=response.html;
      this.runScripts(dynaformEditorHTML.getElementsByTagName("SCRIPT"));
      G.alert(response.error["*message"],"Error");
      return;
    }
    getField("PME_HTML_ENABLETEMPLATE","dynaforms_HtmlEditor").checked=this.getEnableTemplate();
  },
  refresh_xmlcode:function()
  {
    var response=this.ajax.get_xmlcode(this.A);
    if (response.error===0)
    {
      //xmlCode.value = response.xmlcode;
      this.setXMLCode(response.xmlcode);
    }
    else
    {
      G.alert(response.error["*message"],"Error");
    }
  },
  refreshFieldsList:function() {
    //fields_List.refresh(); return;
    var oRPC = new leimnud.module.rpc.xmlhttp({
        url : 'fieldsHandler',
        args: ''
      });
    document.getElementById('dynaformEditor[6]').innerHTML = '';
      oRPC.callback = function(rpc) {

        var scs=rpc.xmlhttp.responseText.extractScript();
        document.getElementById('dynaformEditor[6]').innerHTML = rpc.xmlhttp.responseText;
        scs.evalScript();

      }.extend(this);
    oRPC.make();
  },
  refreshShowHide:function() {
    //fields_List.refresh(); return;
    var oRPC = new leimnud.module.rpc.xmlhttp({
        url : 'conditionalShowHide',
        args: ''
      });
    document.getElementById('dynaformEditor[9]').innerHTML = '';

      oRPC.callback = function(rpc) {

        var scs=rpc.xmlhttp.responseText.extractScript();
        document.getElementById('dynaformEditor[9]').innerHTML = rpc.xmlhttp.responseText;
        scs.evalScript();

      }.extend(this);
    oRPC.make();
  },
  getJSCode:function()
  {
    //if (JSCodePress)
    if(jsEditor)
    {
      //return JSCodePress.getCode();
      jsEditor.save();
      return getField("JS","dynaforms_JSEditor").value;
    }
    else
    {
      if (xmlEditor) {
        xmlEditor.save();
      }
      return getField("JS","dynaforms_JSEditor").value;
    }
  },
  setJSCode:function(newCode)
  {
    //if (JSCodePress)
    if( jsEditor ) {
        if(typeof jsEditor.setValue == 'function')
        {
            jsEditor.setValue(newCode);//jsEditor.setCode(newCode);
        }
    }
    else
    {
      var code=getField("JS","dynaforms_JSEditor");
      code.value=newCode;
    }
  },
  getXMLCode:function()
  {
    if (xmlEditor) {
      xmlEditor.save();
    }
    return getField("XML","dynaforms_XmlEditor").value;
  },
  setXMLCode:function(newCode)
  {
    if( xmlEditor ) {
      xmlEditor.setValue(newCode);//setCode(newCode);
    } else {
      var code = getField("XML","dynaforms_XmlEditor");
      code.value = newCode;
    }
  },
  setEnableTemplate:function(value)
  {
    value = value ? "1" : "0";
    this.ajax.set_enabletemplate( this.A , value );
  },
  getEnableTemplate:function()
  {
    var value = this.ajax.get_enabletemplate( this.A );
    return value == "1";
  },
  refreshJavascripts:function()
  {
    var field=getField("JS_LIST","dynaforms_JSEditor");

    for(j=0; j<field.options.length; j++) {
      if( field.options[j].value == '___pm_boot_strap___' ){
        field.remove(j);
      }
    }
    this.currentJS=field.value;
    var res=this.ajax.get_javascripts(this.A,field.value);
    if(field.value == ''){
      if( typeof(res.aOptions[0]) !== "undefined" && res.aOptions[0].value != '___pm_boot_strap___'){
        res = this.ajax.get_javascripts(this.A, res.aOptions[0].value);
        this.currentJS = res.aOptions[0].value;
      }
    }


    if (typeof(res["*message"])==="undefined")
    {
      while(field.options.length>0) field.remove(0);
      for(var i=0;i<res.aOptions.length;i++)
      {
        var optn = document.createElement ("OPTION");
        optn.text = res.aOptions[i].value;
        optn.value = res.aOptions[i].key;
        field.options[i]=optn;
      }
      field.value = this.currentJS;
      this.setJSCode(res.sCode);
    }
    else
    {
      G.alert(response.error["*message"],"Error");
    }

    var field=getField("JS_LIST","dynaforms_JSEditor");
    for(j=0; j<field.options.length; j++) {
      if( field.options[j].value == '___pm_boot_strap___' ){
        field.options[j].text = '';
      }
    }

    if(field.options.length > 0 || typeof(res.aOptions[0])!== "undefined"){
      hideRowById('JS_TITLE');
      showRowById('JS');
      showRowById('JS_LIST');
      if (this.loadPressLoaded && !JSCodePress)
      {
        startJSCodePress();
      }
    }else{
      showRowById('JS_TITLE');hideRowById('JS_LIST');hideRowById('JS');}

  },
  changeJavascriptCode:function(checkSessionPersists)
  {
    checkSessionPersists = typeof(checkSessionPersists) != 'undefined' ? checkSessionPersists : true;
    if (checkSessionPersists) {
      if (!sessionPersits()) {
        showPrompt('changeJavascriptCode');
        return;
      }
    }
    var field=getField("JS_LIST","dynaforms_JSEditor");
    var value=field.value;
    if (this.currentJS)
    {
      field.value=this.currentJS;
      this.saveJavascript();
      field.value=value;
    }
    this.refreshJavascripts();
  },
  refreshProperties:function()
  {
    var form=this.views["properties"].getElementsByTagName("form")[0];
    var prop=this.ajax.get_properties(this.A,this.dynUid);
    getField("A","dynaforms_Properties").value=prop.A;
    getField("DYN_UID","dynaforms_Properties").value=prop.DYN_UID;
    getField("PRO_UID","dynaforms_Properties").value=prop.PRO_UID;
    getField("DYN_TITLE","dynaforms_Properties").value=prop.DYN_TITLE;
    getField("DYN_TYPE","dynaforms_Properties").value=prop.DYN_TYPE;
    getField("DYN_DESCRIPTION","dynaforms_Properties").value=prop.DYN_DESCRIPTION;
    getField("WIDTH","dynaforms_Properties").value=prop.WIDTH;
    /*getField("ENABLETEMPLATE","dynaforms_Properties").checked=(prop.ENABLETEMPLATE=="1");*/
    getField("MODE","dynaforms_Properties").value=prop.MODE;
  },
  // Internal functions
  runScripts:function(scripts)
  {
    var myScripts=[];
    for(var rr=0; rr < scripts.length ; rr++){
      myScripts.push(scripts[rr].innerHTML);
    }
    for(var rr=0; rr < myScripts.length ; rr++){
      try {
        if (myScripts[rr]!=="")
          if (window.execScript) {
            window.execScript( myScripts[rr], "javascript" );}
            else
              window.setTimeout( "try{\n"+myScripts[rr]+"\n}catch(e){\ndynaformEditor.displayError(e,"+rr+")}", 0 );
      } catch (e) {
        dynaformEditor.displayError(e,rr);
      }
    }
    delete myScripts;
  },
  restoreHTML:function()
  {
      var htmlContent = this.ajax.restore_html(this.A);
      tinyMCE.activeEditor.execCommand('mceSetContent', false, htmlContent);
//    window._editorHTML.doc.body.innerHTML = this.ajax.restore_html(this.A);
//    html_html2();
//    html2_html();
  },
  displayError:function(err,rr)
  {
    G.alert(err.message.split("\n").join("<br />"),"Javascript Error");
  }
};
}
else
{
  alert("Donde esta esto!!!");
}


function getElementByPMClass(__class){
  divs = document.getElementsByTagName('div');
  for(i=0; i<divs.length; i++){
    if(divs[i].className == __class){
      return divs[i];
    }
  }
  return false;
}


  /**/

  function fieldsSave( form ) {

    if (!sessionPersits()) {
      showPrompt('fieldsSave');
      return;
    }

    var str    = document.getElementById('form[PME_XMLNODE_NAME]').value;
    var dField = new input(getField('PME_XMLNODE_NAME'));

    if(str.split(" ").length>=2){
      msgBox(_("ID_EMPTY_NODENAME"), "alert");
      dField.failed();
      dField.focus();
      return;
    }

    if (str.length == 0) {
      msgBox(_("DYNAFIELD_EMPTY"), "alert");
      dField.failed();
      dField.focus();
      return;
    }

    if (!(isNaN(parseInt(str.substr(0,1))))) {
      msgBox(_("DYNAFIELD_NODENAME_NUMBER"), "alert");
      dField.failed();
      dField.focus();
      return;
    }

    if (pme_validating) {
      validatingForm=form;
      dField.passed();
      setTimeout('fieldsSave(validatingForm);',100);
      return;
    }

    if (!G.getObject(form).verifyRequiredFields()){
      return;
    }

    //processbar.style.display = '';
    var res=ajax_post( form.action, form, 'POST' , null , false );
    currentPopupWindow.remove();
    dynaformEditor.refreshCurrentView();
  }

  var typePopup = 0;
  function fieldsAdd( type,label )
  {
    lastTypeSelected = type;
    if (!sessionPersits()) {
      showPrompt('fieldsAdd');
      return;
    }
    switch (type){
      case 'text'      : label=TRANSLATIONS.ID_FIELD_DYNAFORM_TEXT;        typePopup = 1;   break;
      case 'currency'  : label=TRANSLATIONS.ID_FIELD_DYNAFORM_CURRENCY;    typePopup = 1;  break;
      case 'percentage': label=TRANSLATIONS.ID_FIELD_DYNAFORM_PERCENTAGE;  typePopup = 1;  break;
      case 'password'  : label=TRANSLATIONS.ID_FIELD_DYNAFORM_PASSWORD;    typePopup = 1;  break;
      case 'suggest'   : label=TRANSLATIONS.ID_FIELD_DYNAFORM_SUGGEST;     typePopup = 1;  break;
      case 'textarea'  : label=TRANSLATIONS.ID_FIELD_DYNAFORM_TEXTAREA;    typePopup = 1;  break;
      case 'title'     : label=TRANSLATIONS.ID_FIELD_DYNAFORM_TITLE;       typePopup = 0;  break;
      case 'subtitle'  : label=TRANSLATIONS.ID_FIELD_DYNAFORM_SUBTITLE;    typePopup = 0;  break;
      case 'button'    : label=TRANSLATIONS.ID_FIELD_DYNAFORM_BUTTON;      typePopup = 0;  break;
      case 'submit'    : label=TRANSLATIONS.ID_FIELD_DYNAFORM_SUBMIT;      typePopup = 0;  break;
      case 'reset'     : label=TRANSLATIONS.ID_FIELD_DYNAFORM_RESET;       typePopup = 0;  break;
      case 'dropdown'  : label=TRANSLATIONS.ID_FIELD_DYNAFORM_DROPDOWN;    typePopup = 1;  break;
      case 'yesno'     : label=TRANSLATIONS.ID_FIELD_DYNAFORM_YESNO;       typePopup = 1;  break;
      case 'listbox'   : label=TRANSLATIONS.ID_FIELD_DYNAFORM_LISTBOX;     typePopup = 1;  break;
      case 'checkbox'  : label=TRANSLATIONS.ID_FIELD_DYNAFORM_CHECKBOX;    typePopup = 1;  break;
      case 'checkgroup': label=TRANSLATIONS.ID_FIELD_DYNAFORM_CHECKGROUP;  typePopup = 1;  break;
      case 'radiogroup': label=TRANSLATIONS.ID_FIELD_DYNAFORM_RADIOGROUP;  typePopup = 1;  break;
      case 'date'      : label=TRANSLATIONS.DATE_LABEL;                    typePopup = 1;  break;
      case 'hidden'    : label=TRANSLATIONS.ID_FIELD_DYNAFORM_HIDDEN;      typePopup = 0;  break;
      case 'link'      : label=TRANSLATIONS.ID_FIELD_DYNAFORM_LINK;        typePopup = 0;  break;
      case 'file'      : label=TRANSLATIONS.ID_FIELD_DYNAFORM_FILE;        typePopup = 0;  break;
      case 'javascript': label=TRANSLATIONS.ID_FIELD_DYNAFORM_JAVASCRIPT;  typePopup = 1;  break;
      case 'grid'      : label=TRANSLATIONS.ID_FIELD_DYNAFORM_GRID;        typePopup = 0;  break;

      default : label=type; break
    }

    if(typePopup == 0)
      popupWindow(G_STRINGS.ID_ADD + ' ' + label , '../dynaforms/fields_Edit?A='+DYNAFORM_URL+'&TYPE='+encodeURIComponent(type) , 510, 650, null,false,true);
    else
      popupWindow(G_STRINGS.ID_ADD + ' ' + label , '../dynaforms/fields_Edit?A='+DYNAFORM_URL+'&TYPE='+encodeURIComponent(type) , 530, 400 );

    return false;
  }

