var xmlEditor = null;
var jsEditor  = null;

var clientWinSize = null;

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
    this.changeToJavascripts();
    this.changeToPreview();
  },
  _review:function()
  {

  },
  save:function(){
    /*this.saveProperties();*/
    try {
      this.saveCurrentView();
    } catch (e) {
      alert(e);
    }
    res=this.ajax.save(this.A,this.dynUid);
    if(res=='noSub'){
      alert(G_STRINGS.ID_DONT_SAVE_XMLFORM);
      return false;
    }
    if (res==0) {
      alert(G_STRINGS.ID_SAVED);
    }
    else
    {
                if(typeof(res.innerHTML) == 'undefined')
                  G.alert(res["*message"]);
                else
                  alert(G_STRINGS.ID_LOST_SESSION_XMLFORM);
    }
  },
  save_as:function(){
    /*this.saveProperties();*/
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
          //alert(res["response"]);
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
        this.saveProperties();
        break;
    }
  },

  saveShowHide:function()
  {
    ///-- this.save();
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
        this.saveProperties();
        break;
    }
  },

  saveXmlCode:function()
  {
    //var xmlCode = getField("XML").value;
    var xmlCode = this.getXMLCode();
    var todoRefreshXmlCode = xmlCode === null;
    if (todoRefreshXmlCode) return;
    var res = this.ajax.set_xmlcode(this.A, encodeURIComponent(xmlCode));
    if (res!=="") G.alert(res);
  },
  saveHtmlCode:function()
  {
    var htmlCode = getField("HTML");
    todoRefreshHtmlCode = htmlCode === null;
    if (todoRefreshHtmlCode) return;
    var response=this.ajax.set_htmlcode(this.A,htmlCode.value);
    if (response) G.alert(response["*message"],"Error");
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
  },
  saveProperties:function()
  {
    var form=this.views["properties"].getElementsByTagName("form")[0];
    var post=ajax_getForm(form);
    var response=this.ajax.set_properties(this.A,this.dynUid,post);
                if (response!=0){
                   G.alert(response["*message"]);
    } 
  },
  // Change view point functions
  changeToPreview:function()
  {
    //to adecuate the view perspective @Neyek
    content_div = getElementByPMClass('panel_content___processmaker')
    content_div.style.overflow='auto';
    if (this.currentView!="preview")this.refresh_preview();
    this.currentView="preview";
  },
  changeToXmlCode:function()
  {
    content_div = getElementByPMClass('panel_content___processmaker')
    content_div.style.overflow='auto';
    
    this.refresh_xmlcode();
    this.currentView="xmlcode";
    if( ! xmlEditor ) {
      clientWinSize = getClientWindowSize();

        xmlEditor = CodeMirror.fromTextArea('form[XML]', {
        height: (clientWinSize.height - 120) + "px",
        width: (_BROWSER.name == 'msie' ? '100%' : '98%'),
        parserfile: ["parsexml.js", "parsecss.js", "tokenizejavascript.js", "parsejavascript.js",
                     "../contrib/php/js/tokenizephp.js", "../contrib/php/js/parsephp.js",
                     "../contrib/php/js/parsephphtmlmixed.js"],
        stylesheet: ["css/xmlcolors.css", "css/jscolors.css", "css/csscolors.css", "contrib/php/css/phpcolors.css"],
        path: "js/",
        lineNumbers: true,
        continuousScanning: 500
      });
    }  
  },
  changeToHtmlCode:function()
  {
    //to adecuate the view perspective @Neyek
    content_div = getElementByPMClass('panel_content___processmaker')
    content_div.style.overflow='auto';
    
    this.refresh_htmlcode();
    this.currentView="htmlcode";
  },
  changeToFieldsList:function()
  {
    //to adecuate the view perspective @Neyek
    content_div = getElementByPMClass('panel_content___processmaker')
    content_div.style.overflow='visible';
    
    this.refreshFieldsList();
    this.currentView="fieldslist";
  },
  changeToJavascripts:function()
  {
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
        jsEditor = CodeMirror.fromTextArea('form[JS]', {
          height: (clientWinSize.height - 140) + "px",
          width: (_BROWSER.name == 'msie' ? '100%' : '98%'),
          parserfile: ["tokenizejavascript.js", "parsejavascript.js"],
          stylesheet: ["css/jscolors.css"],
          path: "js/",
          lineNumbers: true,
          continuousScanning: 500
        });
      }
    } else {
      showRowById('JS_TITLE');
      hideRowById('JS');
      hideRowById('JS_LIST');
    }
  },
  changeToProperties:function()
  {
    //to adecuate the view perspective @Neyek
    content_div = getElementByPMClass('panel_content___processmaker')
    content_div.style.overflow='auto';
    
    this.currentView="properties";
    this.refreshProperties();
  },
  changeToShowHide:function()
  {
    //to adecuate the view perspective @Neyek
    content_div = getElementByPMClass('panel_content___processmaker')
    content_div.style.overflow='auto';
    //alert('xxxxxx');
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
      window._editorHTML.doc.body.innerHTML=response.html;
      html_html2();
      html2_html();
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
      if(typeof jsEditor.setCode == 'function')
        jsEditor.setCode(newCode);
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
      xmlEditor.setCode(newCode);
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
  changeJavascriptCode:function()
  {
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
    window._editorHTML.doc.body.innerHTML = this.ajax.restore_html(this.A);
    html_html2();
    html2_html();
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
      popupWindow(G_STRINGS.ID_ADD + ' ' + label , '../dynaforms/fields_Edit?A='+DYNAFORM_URL+'&TYPE='+encodeURIComponent(type) , 510, 400 );

    return false;
  }

