/* PACKAGE : GULLIVER FORMS
 */

function G_Form ( element, id )
{
  var me=this;
  this.info = {
      name:'G_Form',
      version :'1.0'
  };
  /*this.module=RESERVED*/
  this.formula='';
  this.element=element;
  if (!element) return;
  this.id=id;
  this.aElements=[];
  this.ajaxServer='';
  this.getElementIdByName = function (name){
    if (name=='') return -1;
    var j;
    for(j=0;j<me.aElements.length;j++){
      if (me.aElements[j].name===name) return j;
    }
    return -1;
  };
  this.getElementByName = function (name) {
    var i=me.getElementIdByName(name);
    if (i>=0) return me.aElements[i]; else return null;
  };
  this.hideGroup = function( group, parentLevel ){
    if (typeof(parentLevel)==='undefined') parentLevel = 1;
    for( var r=0 ; r < me.aElements.length ; r++ ) {
      if ((typeof(me.aElements[r].group)!=='undefined') && (me.aElements[r].group == group ))
        me.aElements[r].hide(parentLevel);
    }
  };
  this.showGroup = function( group, parentLevel ){
    if (typeof(parentLevel)==='undefined') parentLevel = 1;
    for( var r=0 ; r < me.aElements.length ; r++ ) {
      if ((typeof(me.aElements[r].group)!=='undefined') && (me.aElements[r].group == group ))
        me.aElements[r].show(parentLevel);
    }
  };
  this.verifyRequiredFields=function(){
    var valid=true;
    for(var i=0;i<me.aElements.length;i++){
      var verifiedField=((!me.aElements[i].required)||(me.aElements[i].required && (me.aElements[i].value()!=='')));
      valid=valid && verifiedField;
      if (!verifiedField) {
        me.aElements[i].highLight();
      }
    }
    return valid;
  };
}

function G_Field ( form, element, name )
{
  var me=this;
  this.form=form;
  this.element=element;
  this.name=name;
  this.dependentFields=[];
  this.dependentOf=[];

  this.hide = function( parentLevel ){
    if (typeof(parentLevel)==='undefined') parentLevel = 1;
    var parent = me.element;
    for( var r=0; r< parentLevel ; r++ )
      parent = parent.parentNode;
    parent.style.display = 'none';
  };

  this.show = function( parentLevel ){
    if (typeof(parentLevel)==='undefined') parentLevel = 1;
    var parent = me.element;
    for( var r=0; r< parentLevel ; r++ )
      parent = parent.parentNode;
    parent.style.display = '';
  };
  this.setDependentFields = function(dependentFields) {
    var i;
    if (dependentFields.indexOf(',') > -1) {
      dependentFields = dependentFields.split(',');
    }
    else {
      dependentFields = dependentFields.split('|');
    }
    for(i=0;i<dependentFields.length;i++) {
      if (me.form.getElementIdByName(dependentFields[i])>=0) {
        me.dependentFields[i] = me.form.getElementByName(dependentFields[i]);
        me.dependentFields[i].addDependencie(me);
      }
    }
  };
  this.addDependencie = function (field) {
    var exists = false;
    for (i=0;i<me.dependentOf.length;i++)
      if (me.dependentOf[i]===field) exists = true;
    if (!exists) me.dependentOf[i] = field;
  };

  this.updateDepententFields=function(event) {

  var tempValue;
  if (me.dependentFields.length===0) return true;
  var fields=[],Fields = [],i,grid='',row=0;
  var gridField = "";

  for(i in me.dependentFields) {
    if (me.dependentFields[i].dependentOf) {
      for (var j = 0; j < me.dependentFields[i].dependentOf.length; j++) {
        var oAux = me.dependentFields[i].dependentOf[j];
        if (oAux.name.indexOf('][') > -1) {
          var arrayAux = oAux.name.split("][");
          grid = arrayAux[0];
          row  = parseInt(arrayAux[1]);
          fieldName = arrayAux[2];

          gridField = gridGetAllFieldAndValue(oAux.name, 0); //Not get current field

          if (Fields.length > 0){
            aux = Fields;
            aux.push('?');
            if (aux.join('*').indexOf(fieldName + '*') == -1){
              Fields.push(fieldName);
              eval("var oAux2 = {" + fieldName + ":'" + oAux.value() + "'}");
              fields = fields.concat(oAux2);
            }
          }else{
              Fields.push(fieldName);
              eval("var oAux2 = {" + fieldName + ":'" + oAux.value() + "'}");
              fields = fields.concat(oAux2);
            }
          }
          else {
            aux = Fields;
            aux.push('?');
            oAux = me.dependentFields[i].dependentOf[0];
            if (Fields.length > 0){
              if (aux.join('*').indexOf(oAux.name + '*') == -1){
                Fields.push(oAux.name);
                fields = fields.concat(me.dependentFields[i].dependentOf);
              }
            }else{
              Fields.push(oAux.name);
              fields = fields.concat(me.dependentFields[i].dependentOf);
            }
          }
        }
      }
    }

    var callServer;

    callServer = new leimnud.module.rpc.xmlhttp({
        url: me.form.ajaxServer,
        async: false,
        method: "POST",
        args: "function=reloadField" + "&form=" + encodeURIComponent(me.form.id) + "&fields=" + encodeURIComponent(fields.toJSONString()) + ((grid != "")? "&grid=" + grid + ((gridField != "")? "&gridField=" + encodeURIComponent("{" + gridField + "}") : "") : "") + ((row > 0)? "&row=" + row: "")
    });

    callServer.make();
    var response = callServer.xmlhttp.responseText;

    //Validate the response
    if (response.substr(0,1)==='[') {
      var newcont;
      eval('newcont=' + response + ';');
      if (grid == '') {
        for(var i=0;i<newcont.length;i++) {
          //alert(newcont[i].name + '-' +  newcont[i].value);
          var j=me.form.getElementIdByName(newcont[i].name);
          if (typeof(me.form.aElements[j]) != 'undefined' ) {
            me.form.aElements[j].setValue(newcont[i].value);
            me.form.aElements[j].setContent(newcont[i].content);
            me.form.aElements[j].updateDepententFields();
          }
          /*if (me.form.aElements[j].element.fireEvent) {
            me.form.aElements[j].element.fireEvent("onchange");
          } else {
            var evObj = document.createEvent('HTMLEvents');
            evObj.initEvent( 'change', true, true );
            me.form.aElements[j].element.dispatchEvent(evObj);
          }*/
        }
      }
      else {
        for(var i=0;i<newcont.length;i++) {
          var oAux = me.form.getElementByName(grid);
          if (oAux) {
            var oAux2 = oAux.getElementByName(row, newcont[i].name);
            if (oAux2) {
              oAux2.setValue(newcont[i].value);
              oAux2.setContent(newcont[i].content);
              oAux2.updateDepententFields();
              // this line is also needed to trigger the onchange event to trigger the calculation of
              // sumatory or average functions in text fields
              //if (i == (newcont.length-1)){
              /*  if (oAux2.element.fireEvent) {
                  oAux2.element.fireEvent("onchange");
                } else {
                  var evObj = document.createEvent('HTMLEvents');
                  evObj.initEvent( 'change', true, true );
                  oAux2.element.dispatchEvent(evObj);
                }*/
              //}
            }
          }
        }
      }
    } else {
      alert('Invalid response: '+response);
    }
    // this checks the dependent fields that doesn't have assigned a value
    // but their master yes and their dependence must be fulfilled within one
    // onchange event

    /*
    if (grid!='')
    {
      var checkCallServer;
      var fieldName ;
      var index;
      //fieldName = me.name;
      checkCallServer = new leimnud.module.rpc.xmlhttp({
        url     : '../dynaforms/dynaforms_checkDependentFields',
        async   : false,
        method  : "POST",
        args    : 'function=showDependentFields&fields='+response+'&fieldName='+fieldName+'&DYN_UID='+me.form.id+'&form='+encodeURIComponent(fields.toJSONString()) +(grid!=''?'&grid='+grid:'')+(row>0?'&row='+row:'')
      });
      checkCallServer.make();

      var dependentList = eval(checkCallServer.xmlhttp.responseText);
      var field ;
      var oAuxJs;
      for ( index in dependentList ){
        field = 'form[grid]['+ row +']['+dependentList[index]+']';

        oAuxJs = document.getElementById(field);

        if ( oAuxJs!=null ){
          if (oAuxJs.value!="") {
            if ( oAuxJs.fireEvent ) {
              oAuxJs.fireEvent("onchange");
            } else {
              var evObj = document.createEvent( 'HTMLEvents' );
              evObj.initEvent( 'change', true, true );
              oAuxJs.dispatchEvent(evObj);
            }
          }
        }
      }
    }*/
    return true;
  };
  this.setValue = function(newValue) {
    me.element.value = newValue;
  };
  this.setContent = function(newContent) {

  };

  this.setAttributes = function (attributes) {
    for(var a in attributes) {
      if(a=='formula' && attributes[a]){
        //here we called a this function if it has a formula
        sumaformu(this.element,attributes[a],attributes['mask']);
      }//end formula

      switch (typeof(attributes[a])) {
        case 'string':
        case 'int':
        case 'boolean':
          if (a != 'strTo') {
            switch (true) {
              case typeof(me[a])==='undefined':
              case typeof(me[a])==='object':
              case typeof(me[a])==='function':
              case a==='isObject':
              case a==='isArray':
                break;
              default:
                me[a] = attributes[a];
            }
          }
          else {
            me[a] = attributes[a];
          }
      }

    }
  };
  this.value=function() {
    return me.element.value;
  };
  this.toJSONString=function()  {
    return '{"'+me.name+'":'+me.element.value.toJSONString()+'}';
  };
  this.highLight=function(){
    try{
      G.highLight(me.element);
      if (G.autoFirstField) {
        me.element.focus();
        G.autoFirstField=false;
        setTimeout("G.autoFirstField=true;",1000);
      }
    } catch (e){
    }
  };
}

function G_DropDown( form, element, name )
{
  var me=this;
  this.parent = G_Field;
  this.parent( form, element, name );

  this.setContent = function (content)
  {
      dropDownSetOption(me, content);
  };

  if (!element) return;
  leimnud.event.add(this.element,'change',this.updateDepententFields);
}
G_DropDown.prototype=new G_Field();

function G_Text(form, element, name)
{
  var me = this;
  this.mType = "text";
  this.parent = G_Field;
  this.browser = {};
  this.comma_separator = ".";

  this.checkBrowser = function(){
    var nVer = navigator.appVersion;
    var nAgt = navigator.userAgent;
    //alert(navigator.userAgent);
    var browserName  = navigator.appName;
    var fullVersion  = ''+parseFloat(navigator.appVersion);
    var majorVersion = parseInt(navigator.appVersion,10);
    var nameOffset,verOffset,ix;

    // In Opera, the true version is after "Opera" or after "Version"
    if ((verOffset=nAgt.indexOf("Opera"))!=-1) {
     browserName = "Opera";
     fullVersion = nAgt.substring(verOffset+6);
     if ((verOffset=nAgt.indexOf("Version"))!=-1)
       fullVersion = nAgt.substring(verOffset+8);
    }
    // In MSIE, the true version is after "MSIE" in userAgent
    else if ((verOffset=nAgt.indexOf("MSIE"))!=-1) {
     browserName = "Microsoft Internet Explorer";
     fullVersion = nAgt.substring(verOffset+5);
    }
    // In Chrome, the true version is after "Chrome"
    else if ((verOffset=nAgt.indexOf("Chrome"))!=-1) {
     browserName = "Chrome";
     fullVersion = nAgt.substring(verOffset+7);
    }
    // In Safari, the true version is after "Safari" or after "Version"
    else if ((verOffset=nAgt.indexOf("Safari"))!=-1) {
     browserName = "Safari";
     fullVersion = nAgt.substring(verOffset+7);
     if ((verOffset=nAgt.indexOf("Version"))!=-1)
       fullVersion = nAgt.substring(verOffset+8);
    }
    // In Firefox, the true version is after "Firefox"
    else if ((verOffset=nAgt.indexOf("Firefox"))!=-1) {
     browserName = "Firefox";
     fullVersion = nAgt.substring(verOffset+8);
    }
    // In most other browsers, "name/version" is at the end of userAgent
    else if ( (nameOffset=nAgt.lastIndexOf(' ')+1) <
              (verOffset=nAgt.lastIndexOf('/')) )
    {
     browserName = nAgt.substring(nameOffset,verOffset);
     fullVersion = nAgt.substring(verOffset+1);
     if (browserName.toLowerCase()==browserName.toUpperCase()) {
      browserName = navigator.appName;
     }
    }
    // trim the fullVersion string at semicolon/space if present
    if ((ix=fullVersion.indexOf(";"))!=-1)
       fullVersion=fullVersion.substring(0,ix);
    if ((ix=fullVersion.indexOf(" "))!=-1)
       fullVersion=fullVersion.substring(0,ix);

    majorVersion = parseInt(''+fullVersion,10);
    if (isNaN(majorVersion)) {
     fullVersion  = ''+parseFloat(navigator.appVersion);
     majorVersion = parseInt(navigator.appVersion,10);
    }
    this.browser = {
        name: browserName,
        fullVersion: fullVersion,
        majorVersion: majorVersion,
        userAgent: navigator.userAgent
    };
  };

  this.parent( form, element, name );
  if (element) {
    this.prev = element.value;
  }

  this.validate    = 'Any';
  this.mask        = '';
  this.required    = false;
  this.formula     =  '';
  this.key_Change  = false;
  var doubleChange = false;

  //FUNCTIONS

  function IsUnsignedInteger(YourNumber){
    var Template = /^d+$/; //Formato de numero entero sin signo
    return (Template.test(YourNumber)) ? 1 : 0; //Compara "YourNumber" con el formato "Template" y si coincidevuelve verdadero si no devuelve falso
  }

  function replaceAll( text, busca, reemplaza ){
    while (text.toString().indexOf(busca) != -1){
      text = text.toString().replace(busca,reemplaza);
    }
    return text;
  }

  function isNumberMask (mask){
    for ( var key in mask){
      if (mask[key]!='#'&&mask[key]!=','&&mask[key]!='.'&&typeof(mask[key])=='string'){
        return false;
      }
    }
    return true;
  }

  //function renderNewValue(element, keyCode){
    /*var myField = element;
    var myValue = myField.value;
    var cursorPos = 0;
    var csel;
    var newValue = '';
    var csel = me.getCursorPosition();
    var startPos = csel.selectionStart;
    var endPos   = csel.selectionEnd;
    var newValue2;
    switch(keyCode){
      case 8:
        if (startPos>0) {
          newValue = myValue.substring(0, startPos-1);
          newValue = newValue + myValue.substring(endPos, myField.value.length);
          if (mType !== 'text'){
            newValue2 = G.toMask(newValue, me.mask, startPos);
          }else{
            newValue2 = G.toMask(newValue, me.mask, startPos, 'normal');
          }
          newValue = newValue2.result;
        }
        break;
      case 46:
        newValue = myValue.substring(0, startPos);
        newValue = newValue + myValue.substring(endPos+1, myField.value.length);
        if (mType !== 'text'){
          newValue2 = G.toMask(newValue, me.mask, startPos);
        }else{
          newValue2 = G.toMask(newValue, me.mask, startPos, 'normal');
        }
        newValue = newValue2.result;
        break;
    }
    return {result: newValue, cursor: startPos};*/
  //}

  //MEMBERS
  this.setContent = function(content) {
    me.element.value = '';
    if (content.options) {
      if (content.options[0]) {
        me.element.value = content.options[0].value;
      }
    }
  };

  //this.validateKey = function(event){
    /*
    attributes = elementAttributesNS(element, 'pm');
    if(me.element.readOnly)  return true;
    me.prev = me.element.value;
    if (window.event) event=window.event;
    var keyCode= window.event ? event.keyCode : event.which ;
    me.mask = typeof(me.mask)==='undefined'?'':me.mask;
    if(me.mask=='yyyy-mm-dd'){
      attributes.mask=attributes.mask.replace('%d','dd');
      attributes.mask=attributes.mask.replace('%m','mm');
      attributes.mask=attributes.mask.replace('%y','yy');
      attributes.mask=attributes.mask.replace('%Y','yyyy');
      attributes.mask=attributes.mask.replace('%H','mm');
      attributes.mask=attributes.mask.replace('%M','mm');
      attributes.mask=attributes.mask.replace('%S','mm');
      me.mask=attributes.mask;
    }
    //alert(me.mask);
    if (me.mask !=='' ) {
      if ((keyCode < 48 || keyCode > 57) && (keyCode != 8 && keyCode != 0 && keyCode != 46)) return false;
      if((keyCode===118 || keyCode===86) && event.ctrlKey) return false;
      if (event.ctrlKey) return true;
      if (event.altKey) return true;
      if (event.shiftKey) return true;
    }
    if ((keyCode===0) ) if (event.keyCode===46) return true; else return true;
    if ( (keyCode===8)) return true;
    if (me.mask ==='') {
      if (me.validate == 'NodeName') {
        if (me.getCursorPos() == 0) {
          if ((keyCode >= 48) && (keyCode <= 57)) {
            return false;
          }
        }
        var k=new leimnud.module.validator({
          valid :['Field'],
          key   :event,
          lang  :(typeof(me.language)!=='undefined')?me.language:"en"
        });
        return k.result();
      }else{
        switch(me.validate){
          case "Int":
            if ((keyCode > 47) && (keyCode < 58) || ( keyCode == 118 && event.ctrlKey)|| (keyCode == 120 && event.ctrlKey)) {
              return true;
            }else{
              return false;
            }
            break;
          case "Alpha":
            if (keyCode==8) return true;
            patron =/[A-Za-z\sÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â¡ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â©ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â­ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â³ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚ÂºÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â¤ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â«ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â¯ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â¶ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â¼ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â±ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â§ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬Ãƒâ€šÃ‚Â¡ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬Ãƒâ€¹Ã…â€œÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã‚Â¯Ãƒâ€šÃ‚Â¿Ãƒâ€šÃ‚Â½ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬Ãƒâ€šÃ‚Â°ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã‚Â¯Ãƒâ€šÃ‚Â¿Ãƒâ€šÃ‚Â½ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬Ãƒâ€¦Ã¢â‚¬Å“ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Â¦Ãƒâ€šÃ‚Â¡ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬Ãƒâ€¦Ã‚Â¾ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬Ãƒâ€šÃ‚Â¹ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã‚Â¯Ãƒâ€šÃ‚Â¿Ãƒâ€šÃ‚Â½ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬ÃƒÂ¢Ã¢â€šÂ¬Ã…â€œÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Â¦ÃƒÂ¢Ã¢â€šÂ¬Ã…â€œ]/;
            te = String.fromCharCode(keyCode);
            return patron.test(te);
            break;
          case "AlphaNum":
            if (keyCode==8) return true;
            patron =/[A-Za-z0-9\sÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â¡ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â©ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â­ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â³ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚ÂºÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â¤ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â«ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â¯ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â¶ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â¼ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â±ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â§ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬Ãƒâ€šÃ‚Â¡ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬Ãƒâ€¹Ã…â€œÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã‚Â¯Ãƒâ€šÃ‚Â¿Ãƒâ€šÃ‚Â½ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬Ãƒâ€šÃ‚Â°ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã‚Â¯Ãƒâ€šÃ‚Â¿Ãƒâ€šÃ‚Â½ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬Ãƒâ€¦Ã¢â‚¬Å“ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Â¦Ãƒâ€šÃ‚Â¡ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬Ãƒâ€¦Ã‚Â¾ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬Ãƒâ€šÃ‚Â¹ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã‚Â¯Ãƒâ€šÃ‚Â¿Ãƒâ€šÃ‚Â½ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬ÃƒÂ¢Ã¢â€šÂ¬Ã…â€œÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Â¦ÃƒÂ¢Ã¢â€šÂ¬Ã…â€œ]/;
            te = String.fromCharCode(keyCode);
            return patron.test(te);
            break;
          default:
            var k=new leimnud.module.validator({
              valid :[me.validate],
              key   :event,
              lang  :(typeof(me.language)!=='undefined')?me.language:"en"
            });
          return k.result();
          break;
        }
      }
    }else{
      var csel = me.getCursorPosition();
      var myValue = String.fromCharCode(keyCode);
      var startPos = csel.selectionStart;
      var endPos = csel.selectionEnd;
      var myField = me.element;
      var oldValue = myField.value;
      var newValue = '';

      newValue = oldValue.substring(0, startPos);
      newValue = newValue + myValue;
      newValue = newValue + oldValue.substring(endPos, oldValue.length);



      startPos++;

      var newValue2;
      if (mType !== 'text'){
        newValue2 = G.toMask(newValue, me.mask, startPos);
      }else{
        newValue2 = G.toMask(newValue, me.mask, startPos, 'normal');
      }

      //alert(newValue + ' -> ' + mType + ' -> ' + newValue2.result);
      //alert(newValue2.result);
      me.element.value = newValue2.result;
      //alert(me.element.value);
      me.setSelectionRange(newValue2.cursor, newValue2.cursor);

      if (me.element.fireEvent){
        me.element.fireEvent("onchange");
      }else{
        var evObj = document.createEvent('HTMLEvents');
        evObj.initEvent( 'change', true, true );
        me.element.dispatchEvent(evObj);
      }
      return true;
    }*/
  //};

  this.putFormatNumber =function (evt) {
    /*
    if((typeof(evt)==="undefined" || evt===0) && me.mask!='' ){*/
//      var numberSet=me.element.value.split('.');
//      maskD = me.mask.split(';');
//      maskL = (maskD.length >1)?maskD[1]:maskD[0];
//      if (maskL.search(",")==-1){
//        return false;
//      }
//      maskWithoutC =replaceAll(maskL,",","");
//      maskWithoutC  =replaceAll(maskWithoutC," ","");
//      maskWithoutPto=replaceAll(maskWithoutC,".","");
//      if(numberSet.length >=2){
//        if(maskWithoutPto.substr( (maskWithoutPto.length -1) ,maskWithoutPto.length) =="%")
//          me.element.value  = me.element.value+' '+maskWithoutPto.substr( (maskWithoutPto.length -1) ,maskWithoutPto.length);
//        return;
//      }
//
//      maskElemnts = maskWithoutC.split('.');
//      maskpartInt = maskElemnts[0].split('');
//      numberwc = replaceAll(me.element.value,",", "");
//      numberwc = replaceAll(numberwc,".", "");
//      onlynumber = replaceAll(numberwc,".", "");
//      onlynumber = replaceAll(numberwc," ", "");
//      onlynumber = replaceAll(numberwc,"%", "");
//      if(onlynumber=='') return false;
//      cd = parseInt(Math.log(onlynumber)/Math.LN10+1);
//      var auxnumber = onlynumber;
//      var cdaux=0;
//      while(auxnumber > 0){
//        cdaux++;
//        auxnumber =parseInt(auxnumber / 10);
//      }
//      cd=cdaux;
//
//      if (isNumberMask(maskpartInt)){
//        if(cd < maskpartInt.length && cd >= 4 && cd !=3){
//          var newNumber='';
//          var cc=1;
//          while (onlynumber > 0){
//            lastdigito = onlynumber % 10;
//            if (cc%3==0 && cd != cc){
//              newNumber = ','+lastdigito.toString() + newNumber;
//            } else {
//              newNumber = lastdigito.toString() + newNumber;
//            }
//            onlynumber =parseInt(onlynumber / 10);
//            cc++;
//          }
//          if(maskWithoutPto.substr( (maskWithoutPto.length -1) ,maskWithoutPto.length) =="%")
//            me.element.value = newNumber+' '+maskWithoutPto.substr( (maskWithoutPto.length -1) ,maskWithoutPto.length);
//          else
//            me.element.value = newNumber;
//        }else{
//          if(maskWithoutPto.substr( (maskWithoutPto.length -1) ,maskWithoutPto.length) =="%")
//            var spaceString;
//          if (me.element.value.substr( (me.element.value.length -1) ,me.element.value.length) == '%' ){
//            spaceString ='';
//            me.element.value =  onlynumber + spaceString + maskWithoutPto.substr( (maskWithoutPto.length -1) ,maskWithoutPto.length);
//          } else {
//            spaceString =' ';
//            me.element.value =  onlynumber;
//          }
//        }
//      }
    //}
  };

  //this.preValidateChange=function(event) {
    /*var oNewValue;
    var newValueR;
    me.putFormatNumber(event);
    if(me.element.readOnly)  return true;
    if (me.mask ==='') return true;
    if (event.keyCode === 8){
      oNewValue = renderNewValue(me.element,event.keyCode);
      newValueR = G.toMask(oNewValue.result, me.mask, oNewValue.cursor );
      me.element.value=newValueR.result;
      me.setSelectionRange(oNewValue.cursor - 1, oNewValue.cursor - 1);
      if (me.element.fireEvent){
        me.element.fireEvent("onchange");
      }else{
        var evObj = document.createEvent('HTMLEvents');
        evObj.initEvent( 'change', true, true );
        me.element.dispatchEvent(evObj);
      }
      return false;
    }
    if (event.keyCode === 46){
      oNewValue = renderNewValue(me.element,event.keyCode);
      newValueR = G.toMask(oNewValue.result, me.mask, oNewValue.cursor );
      me.element.value=newValueR.result;
      me.setSelectionRange(oNewValue.cursor, oNewValue.cursor);
      if (me.element.fireEvent){
        me.element.fireEvent("onchange");
      }else{
        var evObj = document.createEvent('HTMLEvents');
        evObj.initEvent( 'change', true, true );
        me.element.dispatchEvent(evObj);
      }
      return false;
    }
    //alert(me.element.value);
    me.prev=me.element.value;
    return true;*/
  //};

  this.execFormula=function(event) {
    if(  me.formula != ''){
      leimnud.event.add(getField('faa'),'keypress',function(){
        alert(getField('faa').value);
      });
    }
    return false;
  };

  /*this.validateChange=function(event) {
    /*if (me.mask ==='') return true;
    var sel=me.getSelectionRange();
    var newValue2=G.cleanMask( me.element.value, me.mask, sel.selectionStart );
    newValue2=G.toMask( newValue2.result, me.mask, newValue2.cursor);
    me.element.value = newValue2.result;
    me.setSelectionRange(newValue2.cursor, newValue2.cursor);
    return true;*/
  //};*/

  this.value = function()
  {
    return me.element.value;
  };

  //Get Cursor Position
  this.getCursorPos = function () {
    var textElement=me.element;
    if (!document.selection) return textElement.selectionStart;
    //save off the current value to restore it later,
    var sOldText = textElement.value;
    //create a range object and save off it's text
    var objRange = document.selection.createRange();
    var sOldRange = objRange.text;
    //set this string to a small string that will not normally be encountered
    var sWeirdString = '#%~';
    //insert the weirdstring where the cursor is at
    objRange.text = sOldRange + sWeirdString;
    objRange.moveStart('character', (0 - sOldRange.length - sWeirdString.length));
    //save off the new string with the weirdstring in it
    var sNewText = textElement.value;
    //set the actual text value back to how it was
    objRange.text = sOldRange;
    //look through the new string we saved off and find the location of
    //the weirdstring that was inserted and return that value
    for (i=0; i <= sNewText.length; i++) {
      var sTemp = sNewText.substring(i, i + sWeirdString.length);
      if (sTemp == sWeirdString) {
        var cursorPos = (i - sOldRange.length);
        return cursorPos;
      }
    }
  };

  this.setSelectionRange = function(selectionStart, selectionEnd) {
    var input=me.element;
    if (input.createTextRange) {
      //IE
      var range = input.createTextRange();
      range.collapse(true);
      range.moveEnd('character', selectionEnd);
      range.moveStart('character', selectionStart);
      range.select();
    }
    else if (input.setSelectionRange) {
      //Firefox and others
      input.focus();
      input.setSelectionRange(selectionStart, selectionEnd);
    }
  };

  //FUNCTION MAYBE IT'S DEPRECATED
  /*this.getSelectionRange = function() {
    if (document.selection) {
      var textElement=me.element;
      var sOldText = textElement.value;
      var objRange = document.selection.createRange();
      var sOldRange = objRange.text;
      var sWeirdString = '#%~';
      objRange.text = sOldRange + sWeirdString;
      objRange.moveStart('character', (0 - sOldRange.length - sWeirdString.length));
      var sNewText = textElement.value;
      objRange.text = sOldRange;
      for (i=0; i <= sNewText.length; i++) {
        var sTemp = sNewText.substring(i, i + sWeirdString.length);
        if (sTemp == sWeirdString) {
          var cursorPos = (i - sOldRange.length);
          return {
            selectionStart: cursorPos,
            selectionEnd: cursorPos+sOldRange.length
          };
        }
      }
    }else{
      var sel={
          selectionStart: 0,
          selectionEnd: 0
      };
      sel.selectionStart = me.element.selectionStart;
      sel.selectionEnd = me.element.selectionEnd;
      return sel;
    }
  };
*/
  //FUNCTION MAYBE IT'S DEPRECATED
  /*this.getCursorP =function (field) {
    if (document.selection) {
      field.focus();
      var oSel = document.selection.createRange();
      oSel.moveStart('character', -field.value.length);
      field.selectionEnd = oSel.text.length;
      oSel.setEndPoint('EndToStart', document.selection.createRange() );
      field.selectionStart = oSel.text.length;
    }
    return {selectionStart: field.selectionStart, selectionEnd: field.selectionEnd};
  };*/

  //Gets cursor position
  this.getCursorPosition = function(){
    if(navigator.appName == 'Microsoft Internet Explorer'){
      var field = me.element;
      if (document.selection) {
        field.focus();
        var oSel = document.selection.createRange();
        oSel.moveStart('character', -field.value.length);
        field.selectionEnd = oSel.text.length;
        oSel.setEndPoint('EndToStart', document.selection.createRange() );
        field.selectionStart = oSel.text.length;
      }
      return {selectionStart: field.selectionStart, selectionEnd: field.selectionEnd};
    }else{
      if (document.selection) {
        var textElement=me.element;
        var sOldText = textElement.value;
        var objRange = document.selection.createRange();
        var sOldRange = objRange.text;
        var sWeirdString = '#%~';
        objRange.text = sOldRange + sWeirdString;
        objRange.moveStart('character', (0 - sOldRange.length - sWeirdString.length));
        var sNewText = textElement.value;
        objRange.text = sOldRange;
        for (i=0; i <= sNewText.length; i++) {
          var sTemp = sNewText.substring(i, i + sWeirdString.length);
          if (sTemp == sWeirdString) {
            var cursorPos = (i - sOldRange.length);
            return {
              selectionStart: cursorPos,
              selectionEnd: cursorPos+sOldRange.length
            };
          }
        }
      }else{
        var sel={
            selectionStart: 0,
            selectionEnd: 0
        };
        sel.selectionStart = me.element.selectionStart;
        sel.selectionEnd = me.element.selectionEnd;
        return sel;
      }
    }
  };

  this.removeMask = function(){
    value     = me.element.value;
    cursor    = me.getCursorPosition();
    chars     = value.split('');
    newValue  = '';
    newCont   = 0;
    newCursor = 0;
    for(c=0; c < chars.length; c++){
      switch(chars[c]){
        case '0': case '1': case '2': case '3': case '4':
        case '5': case '6': case '7': case '8': case '9':
          newValue += chars[c];
          newCont++;
          if (c + 1 == cursor.selectionStart){
            newCursor = newCont;
          }
          break;
        case me.comma_separator:
          if(me.mType != 'date') {
              newValue += chars[c];
              newCont++;
              if (c + 1 == cursor.selectionStart){
                newCursor = newCont;
              }
          }
          break;
        case '-':
          if (me.validate == 'Real' || me.validate == 'Int'){
            newValue += chars[c];
            newCont++;
            if (c + 1 == cursor.selectionStart){
              newCursor = newCont;
            }
          }
          break;
      }
    }
    if (cursor.selectionStart != cursor.selectionEnd){
      return {result: newValue, cursor: cursor};
    }
    else{
      return {result: newValue, cursor: {selectionStart: newCursor, selectionEnd: newCursor}};
    }
  };

  this.replaceMask = function(value, cursor, mask, type, comma){
    switch(type){
      case 'currency':
      case 'percentage':
        dir = 'reverse';
        break;
      default:
        if (me.mType == 'text' && me.validate == 'Real') {
          dir = 'reverse';
        } else {
          dir = 'forward';
        }

        break;
    }
    return G.ApplyMask(value, mask, cursor, dir, comma);
  };

  this.replaceMasks= function(newValue, newCursor){
    masks = me.mask;
    aMasks = masks.split(';');
    aResults = [];
    for(m=0; m < aMasks.length; m++){
      mask = aMasks[m];
      type = me.mType;
      comma_sep = me.comma_separator;
      comma_sep = (comma_sep == '') ? '.' : comma_sep;
      aResults.push(me.replaceMask(newValue, newCursor, mask, type, comma_sep));
      break;
    }
    minIndex = 0;
    minValue = aResults[0].result;
    if (aResults.length > 1){
      for(i=1; i < aResults.length; i++){
        if (aResults[i].result < minValue){
          minValue = aResults[i].result;
          minIndex = i;
        }
      }
    }
    return aResults[minIndex];
  };

  this.getCleanMask = function(){
    aMask = me.mask.split('');
    maskOut = '';
    for(i=0; i < aMask.length; i++){
      if (me.mType == 'currency' || me.mType == 'percentage' || (me.mType == 'text' && me.validate == 'Real')){
        switch(aMask[i]){
          case '0': case '#':
            maskOut += aMask[i];
            break;
          case me.comma_separator:
            maskOut += '_';
            break;
        }
      }
      else{
        switch(aMask[i]){
          case '0': case '#': case 'd': case 'm': case 'y': case 'Y':
            maskOut += aMask[i];
            break;
        }
      }
    }
    return maskOut;
  }

  this.applyMask = function(keyCode){
    if (me.mask != ''){
      dataWOMask = me.removeMask();
      //alert(dataWOMask.result + ', ' + dataWOMask.cursor.selectionStart);
      currentValue = dataWOMask.result;
      currentSel = dataWOMask.cursor;
      cursorStart = currentSel.selectionStart;
      cursorEnd = currentSel.selectionEnd;

      var action = "mask";
      var swPeriod = false;
      var i = 0;

      switch (keyCode) {
        case 0:
          action = 'none';
          break;
        case 8:
          newValue  = currentValue.substring(0, cursorStart - 1);
          newValue += currentValue.substring(cursorEnd, currentValue.length);
          newCursor = cursorStart - 1;
          break;
        case 46:
          newValue  = currentValue.substring(0, cursorStart);
          newValue += currentValue.substring(cursorEnd + 1, currentValue.length);
          newCursor = cursorStart;
          break;
        case 256: case 44:
          swPeriod = true;
          newValue  = currentValue.substring(0, cursorStart);
          if (keyCode == 256)
            newValue += '.';
          else
            newValue += ',';
          newValue += currentValue.substring(cursorEnd, currentValue.length);
          //alert(newValue);
          newCursor = cursorStart + 1;
          break;
        case 35: case 36: case 37: case 38: case 39: case 40:
          newValue  = currentValue;
          switch(keyCode){
            case 36:newCursor = 0;break;
            case 35:newCursor = currentValue.length;break;
            case 37:newCursor = cursorStart - 1;break;
            case 39:newCursor = cursorStart + 1;break;
          }
          action = 'move';
          break;
        case 45:
            if (me.mType == "currency" || (me.mType == "text" && (me.validate == "Real" || me.validate == "Int"))) {
                newValue = currentValue.substring(0, currentValue.length).split("");

                if (newValue.length > 0) {
                    for (i = 0; i <= newValue.length - 1; i++) {
                        var campVal = newValue[i];

                        if ((typeof campVal == "number" || typeof campVal == "string") && campVal != "" && !isNaN(campVal)) {
                            newValue = currentValue.substring(0, i - 1);
                            newValue = newValue + "-" + currentValue.substring(i);
                            i = newValue.length + 1;
                            newCursor = cursorStart + 1;
                        } else {
                            if (campVal == "-") {
                                newValue = currentValue.substring(0, i - 1);
                                newValue = newValue + currentValue.substring(i + 1);
                                newCursor = cursorStart - 1;
                                i = newValue.length + 1;
                            }
                        }
                    }

                    if (newValue.join) {
                        newValue = newValue.join("");
                    }
                } else {
                    //default
                    newKey = String.fromCharCode(keyCode);
                    newValue = currentValue.substring(0, cursorStart);
                    newValue = newValue + newKey;
                    newValue = newValue + currentValue.substring(cursorEnd, currentValue.length);
                    newCursor = cursorStart + 1;
                }
            }
            break;
        default:
            newKey = String.fromCharCode(keyCode);
            newValue = currentValue.substring(0, cursorStart);
            newValue = newValue + newKey;
            newValue = newValue + currentValue.substring(cursorEnd, currentValue.length);
            newCursor = cursorStart + 1;
            break;
      }

      if (newCursor < 0) {
          newCursor = 0;
      }

      if (keyCode != 8 && keyCode != 46 && keyCode != 35 && keyCode != 36 && keyCode != 37 && keyCode != 39){
        var testData = dataWOMask.result;
        var tamData = testData.length;
        var cleanMask = me.getCleanMask();
        var tamMask = cleanMask.length;
        var sw = false;

        if (testData.indexOf(me.comma_separator) == -1){
          aux = cleanMask.split('_');
          tamMask = aux[0].length;
          sw = true;
        }

        if (tamData >= tamMask) {
            var swMinus = false;

            if (/^.*\-.*$/.test(newValue)) {
                swMinus = true;
            }

            if (!(keyCode == 45 || (swMinus && tamMask >= tamData))) {
                if (sw && !swPeriod){
                    action = "none";
                }

                if (!sw) {
                    action = "none";
                }
            }
        }
      }

      switch(action){
        case 'mask': case 'move':
          dataNewMask = me.replaceMasks(newValue, newCursor);
          me.element.value = dataNewMask.result;
          me.setSelectionRange(dataNewMask.cursor,dataNewMask.cursor);
          break;
        //case 'move':
          //alert(newCursor);
          //me.setSelectionRange(newCursor,newCursor);
          //break;
      }
    } else{
      //no mask
      currentValue = me.element.value;
      currentSel = me.getCursorPosition();
      cursorStart = currentSel.selectionStart;
      cursorEnd = currentSel.selectionEnd;
      switch(keyCode){
        case 8:
          newValue  = currentValue.substring(0, cursorStart - 1);
          newValue += currentValue.substring(cursorEnd, currentValue.length);
          newCursor = cursorStart - 1;
          break;
        case 45:
        case 46:
            if (me.validate != "Email") {
                newValue  = currentValue.substring(0, cursorStart);
                newValue += currentValue.substring(cursorEnd + 1, currentValue.length);
                newCursor = cursorStart;
            } else {
                newKey = String.fromCharCode(keyCode);
                newValue  = currentValue.substring(0, cursorStart);
                newValue += newKey;
                newValue += currentValue.substring(cursorEnd, currentValue.length);
                newCursor = cursorStart + 1;
            }
            break;
        case 256:
          newValue  = currentValue.substring(0, cursorStart);
          newValue += '.';
          newValue += currentValue.substring(cursorEnd, currentValue.length);
          newCursor = cursorStart + 1;
          break;
        case 35: case 36: case 37: case 38: case 39: case 40:
          newValue  = currentValue;
          switch(keyCode){
            case 36:newCursor = 0;break;
            case 35:newCursor = currentValue.length;break;
            case 37:newCursor = cursorStart - 1;break;
            case 39:newCursor = cursorStart + 1;break;
          }
          break;
        default:
          newKey = String.fromCharCode(keyCode);
          newValue  = currentValue.substring(0, cursorStart);
          newValue += newKey;
          newValue += currentValue.substring(cursorEnd, currentValue.length);
          newCursor = cursorStart + 1;
          break;
      }
      if (newCursor < 0)  newCursor = 0;

      me.element.value = newValue;
      me.setSelectionRange(newCursor,newCursor);
    }
    //Launch OnChange Event
    /*if (me.element.fireEvent){
      me.element.fireEvent("onchange");
    }else{
      var evObj = document.createEvent('HTMLEvents');
      evObj.initEvent( 'change', true, true );
      me.element.dispatchEvent(evObj);
    }*/
  };

  this.sendOnChange = function(){
    if (me.element.fireEvent){
      me.element.fireEvent("onchange");
    }else{
      var evObj = document.createEvent('HTMLEvents');
      evObj.initEvent( 'change', true, true );
      me.element.dispatchEvent(evObj);
    }
  };

  this.handleKeyDown = function(event){
    if (me.element.readOnly) {
      return true;
    }
    //THIS FUNCTION HANDLE BACKSPACE AND DELETE KEYS
    if (me.validate == 'Any' && me.mask == '') return true;

    var pressKey = (window.event)? window.event.keyCode : event.which;

    switch(pressKey){
      case 8: case 46:  //BACKSPACE OR DELETE
      case 35: case 36: //HOME OR END
      case 37: case 38: case 39: case 40: // ARROW KEYS
        if ((pressKey == 8 || pressKey == 46) && me.validate == "NodeName") {
            return true;
        }

        if (pressKey == 46 && me.validate == "Email") {
            return true;
        }

        me.applyMask(pressKey);

        if ((pressKey == 8 || pressKey == 46) && (me.validate != 'Login' && me.validate != 'NodeName')) me.sendOnChange();
        me.checkBrowser();
        if (me.browser.name == 'Chrome' || me.browser.name == 'Safari'){
          event.returnValue = false;
        }
        else{
          return false;
        }
        break;
      case 9:
        return true;
        break;
      default:
        if (me.mType == 'date' || me.mType == 'currency' || me.mType == 'percentage' || me.validate == 'Real' || me.validate == 'Int') {
          if ((48 <= pressKey && pressKey <= 57) || (pressKey == 109 || pressKey == 190 || pressKey == 188 || pressKey == 189) || (96 <= pressKey && pressKey <= 111)) {
            return true;
          }
          else {
            return false;
          }
        }
        break;
    }
    return true;
  };

  this.handleKeyPress = function(event){
    if (me.element.readOnly) {
      return true;
    }

    if ((me.mType != 'currency' && me.mType != 'percentage' && me.mType != 'date') && (me.element.value.length > me.element.maxLength - 1)) {
      return true;
    }

    if (me.validate == 'Any' && me.mask == '') return true;

    //THIS FUNCTION HANDLE ALL KEYS EXCEPT BACKSPACE AND DELETE
    //keyCode = event.keyCode;
    var keyCode = (window.event)? window.event.keyCode : event.which;

    if (navigator.userAgent.indexOf('MSIE') != -1) { // Microsoft Internet Explorer
      if (keyCode == 0) return true;
    }

    switch (keyCode) {
        case 9:
        case 13:
            return true;
            break;
    }

    var swShiftKey = (
        (me.mType == 'currency') || (me.mType == 'percentage') || (me.validate == 'Real') || (me.validate == 'Int')
    )? false : true;

    if (window.event) {
        if (window.event.altKey) {
            return true;
        }

        if (window.event.ctrlKey) {
            return true;
        }

        //Commented for accept characters with AZERTY keyboard
        //if (window.event.shiftKey) {
        //    return swShiftKey;
        //}
    } else {
        if (event.altKey) {
            return true;
        }

        if (event.ctrlKey) {
            return true;
        }

        //Commented for accept characters with AZERTY keyboard
        //if (event.shiftKey) {
        //    return swShiftKey;
        //}
    }

    me.checkBrowser();

    //if ((me.browser.name == 'Firefox') && (keyCode == 8 || keyCode == 46)) {
    if ((me.browser.name == 'Firefox') && (keyCode == 8) && (me.validate != 'NodeName')) {
      if (me.browser.name == 'Chrome' || me.browser.name == 'Safari'){
        event.returnValue = false;
      }
      else{
        return false;
      }
    }
    else{
      //pressKey = window.event ? event.keyCode : event.which;
      var pressKey = (window.event)? window.event.keyCode : event.which;

      //if (me.mType == 'date') me.validate = 'Int';

      keyValid = true;
      updateOnChange = true;

      switch (me.validate) {
        case 'Any':
          keyValid = true;
          break;
        case 'Int':
          patron = /[0-9\-]/;
          key = String.fromCharCode(pressKey);
          keyValid = patron.test(key);
          break;
        case 'Real':
          if (typeof me.comma_separator != 'undefined') {
            patron = /[0-9\-]/;
          }
          else {
            patron = /[0-9,\.]/;
          }

          key = String.fromCharCode(pressKey);
          keyValid = patron.test(key);
          keyValid = keyValid || (pressKey == 45);

          if (typeof me.comma_separator != 'undefined') {
            if (me.comma_separator == '.'){
              if (me.element.value.indexOf('.')==-1){
                keyValid = keyValid || (pressKey == 46);
              }
            }
            else{
              if (me.element.value.indexOf(',')==-1){
                keyValid = keyValid || (pressKey == 44);
              }
            }
          }
          break;
        case 'Alpha':
          patron =/[a-zA-Z]/; // \sÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â¡ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â©ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â­ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â³ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚ÂºÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â¤ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â«ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â¯ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â¶ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â¼ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â±ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â§ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬Ãƒâ€šÃ‚Â¡ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬Ãƒâ€¹Ã…â€œÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã‚Â¯Ãƒâ€šÃ‚Â¿Ãƒâ€šÃ‚Â½ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬Ãƒâ€šÃ‚Â°ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã‚Â¯Ãƒâ€šÃ‚Â¿Ãƒâ€šÃ‚Â½ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬Ãƒâ€¦Ã¢â‚¬Å“ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Â¦Ãƒâ€šÃ‚Â¡ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬Ãƒâ€¦Ã‚Â¾ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬Ãƒâ€šÃ‚Â¹ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã‚Â¯Ãƒâ€šÃ‚Â¿Ãƒâ€šÃ‚Â½ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬ÃƒÂ¢Ã¢â€šÂ¬Ã…â€œÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Â¦ÃƒÂ¢Ã¢â€šÂ¬Ã…â€œ]/;
          key = String.fromCharCode(pressKey);
          keyValid = patron.test(key);
          break;
        case 'AlphaNum':
          patron =/[a-zA-Z0-9\sÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â¡ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â©ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â­ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â³ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚ÂºÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â¤ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â«ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â¯ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â¶ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â¼ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â±ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â§ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬Ãƒâ€šÃ‚Â¡ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬Ãƒâ€¹Ã…â€œÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã‚Â¯Ãƒâ€šÃ‚Â¿Ãƒâ€šÃ‚Â½ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬Ãƒâ€šÃ‚Â°ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã‚Â¯Ãƒâ€šÃ‚Â¿Ãƒâ€šÃ‚Â½ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬Ãƒâ€¦Ã¢â‚¬Å“ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Â¦Ãƒâ€šÃ‚Â¡ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬Ãƒâ€¦Ã‚Â¾ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬Ãƒâ€šÃ‚Â¹ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã‚Â¯Ãƒâ€šÃ‚Â¿Ãƒâ€šÃ‚Â½ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬ÃƒÂ¢Ã¢â€šÂ¬Ã…â€œÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Â¦ÃƒÂ¢Ã¢â€šÂ¬Ã…â€œ]/;
          key = String.fromCharCode(pressKey);
          keyValid = patron.test(key);
          break;
        case 'NodeName': case 'Login':
          updateOnChange = false;
          if (me.getCursorPos() == 0) {
            if ((pressKey >= 48) && (pressKey <= 57)) {
              keyValid = false;
              break;
            }
          }
          if ((keyCode == 8) && (me.validate == 'NodeName')) {
            keyValid = true;
          } else {
            var k=new leimnud.module.validator({
              valid :['Login'],
              key   : (window.event)? window.event : event,
              lang  :(typeof(me.language)!=='undefined')? me.language:"en"
            });
            keyValid = k.result();
          }
          break;
        default:
          var k = new leimnud.module.validator({
            valid :[me.validate],
            key   :(window.event) ? window.event : event,
            lang  :(typeof(me.language)!=='undefined')? me.language:"en"
          });
          keyValid = k.result();

          break;
      }

      if (keyValid){
        if (me.mask == "" && (me.validate == 'Real' || me.validate == 'Int') && me.mType == 'text') {
          if (key == '-') {
            currentValue = me.element.value;
            if (currentValue.charAt(0) == '-') {
              currentValue = currentValue.substring(1, currentValue.length);
              me.element.value = currentValue;
            } else {
              me.element.value = '-'+currentValue;
            }
          }
        }
        //APPLY MASK
        if ((me.validate == "Login" || me.validate == "NodeName") && me.mask == "") {
            return true;
        }

        if (pressKey == 46){
          me.applyMask(256); //This code send [.] period to the mask
        }
        else{
          me.applyMask(pressKey);
        }

        if (updateOnChange) {
            me.sendOnChange();
        }
      }

      if (me.browser.name == 'Firefox') {
        if (keyCode == 0) return true;
      }

      if (me.browser.name == 'Chrome' || me.browser.name == 'Safari'){
        event.returnValue = false;
      }
      else{
        return false;
      }
    }
  };

  if(this.element) {
    this.element.onblur = function(event)
    {
      var evt = event || window.event;
      var keyPressed = evt.which || evt.keyCode;
      //me.putFormatNumber(keyPressed);

      if ( (me.mask != '') &&  (  (me.mType == 'currency') || (me.mType == 'percentage') ||
                                  ((me.validate == "Real") && (me.mType == 'text')) ) &&
          (me.mask.indexOf('-')==-1) && (me.element.value != '') ) {

        masks = me.mask;
        aMasks = masks.split(';');
        for(m=0; m < aMasks.length; m++) {
          var separatorField = ",";
          if (typeof(me.comma_separator) != 'undefined') {
            separatorField = me.comma_separator;
          } else {
            txtRealMask = aMasks[m].split('');
            p = txtRealMask.length - 1;
            for ( ; p >= 0; p--) {
              if (txtRealMask[p] != '#' && txtRealMask[p] != '%' && txtRealMask[p] != ' ') {
                separatorField = txtRealMask[p];
                break;
              }
            }
          }

          var partsMaskSep = aMasks[m].split(separatorField);
          if (partsMaskSep.length == 2) {
            var countDecimal = 0;
            txtRealMask = aMasks[m].split('');
            p = txtRealMask.length - 1;
            for ( ; p >= 0; p--) {
              if (txtRealMask[p] == '#') {
                countDecimal++;
              }
              if (txtRealMask[p] == separatorField) {
                break;
              }
            }

            var decimalString = '';
            var pluginAfter = '';
            var pluginDecimal = '';
            var numberSet = me.element.value.split(separatorField);

            if (typeof(numberSet[1]) == 'undefined') {
              var decimalSet = '';
              var newInt = '';
              var flagAfter = true;
              var newPluginDecimal = '';
              var decimalCade = numberSet[0].split('');
              for (p = 0; p < decimalCade.length; p++) {
                if ((!isNaN(parseFloat(decimalCade[p])) && isFinite(decimalCade[p])) || (decimalCade[p] == ',') || (decimalCade[p] == '.') ) {
                  newInt += decimalCade[p];
                  flagAfter = false;
                } else {
                  if (flagAfter) {
                    pluginAfter += decimalCade[p];
                  } else {
                    newPluginDecimal += decimalCade[p];
                  }
                }
              }
              numberSet[0] = newInt;
              numberSet[1] = newPluginDecimal;
            }

            var decimalSet = numberSet[1];
            var decimalCade = decimalSet.split('');
            var countDecimalNow = 0;
            for (p = 0; p < decimalCade.length; p++) {
              if (!isNaN(parseFloat(decimalCade[p])) && isFinite(decimalCade[p])) {
                countDecimalNow++;
                decimalString += decimalCade[p];
              } else {
                pluginDecimal += decimalCade[p];
              }
            }

            if(countDecimalNow < countDecimal) {
              for(; countDecimalNow < countDecimal; countDecimalNow++) {
                decimalString += '0';
              }
              me.element.value = pluginAfter + numberSet[0] + separatorField + decimalString + pluginDecimal;
            }
          }
          break;
        }
      }

      if (this.validate == "Email") {
        var pat = /^\w+(?:[\.-]?\w+)*@\w+(?:[\.-]?\w+)*\.\w{2,6}$/;

        if(!pat.test(this.element.value))
        {
          //old|if(this.required=="0"&&this.element.value=="") {
          if(this.element.value=="") {
            this.element.className="module_app_input___gray";
            return;
          }
          else {
            this.element.className=this.element.className.split(" ")[0]+" FormFieldInvalid";
          }
        }
        else
        {
          this.element.className=this.element.className.split(" ")[0]+" FormFieldValid";
        }
      }

      if (this.strTo) {
        switch (this.strTo){
          case 'UPPER':
            this.element.value = this.element.value.toUpperCase();
            break;
          case 'LOWER':
            this.element.value = this.element.value.toLowerCase();
            break;
          case 'TITLE':
        	this.element.value = this.element.value.toLowerCase();
            this.element.value = this.element.value.toInitCap(this.element.value);
            break;
          case 'PHRASE':
            //this.element.value = this.element.value.toLowerCase();
            var phrase = this.element.value.split(' ');
            phrase[0] = phrase[0].toInitCap(phrase[0]);
            this.element.value = phrase.join(' ');
            break;
        }
      }
    }.extend(this);
  }

  if (!element) return;

  if (!window.event){
      //THIS ASSIGN FUNCTIONS FOR FIREFOX/MOZILLA
      this.element.onkeydown  = this.handleKeyDown;
      this.element.onkeypress = this.handleKeyPress;
      this.element.onchange   = this.updateDepententFields;
      //this.element.onblur = this.handleOnChange;
  } else {
      //THIS ASSIGN FUNCTIONS FOR IE/CHROME
      leimnud.event.add(this.element, 'keydown', this.handleKeyDown);
      leimnud.event.add(this.element, 'keypress', this.handleKeyPress);
      leimnud.event.add(this.element, 'change', this.updateDepententFields);
  }

  //leimnud.event.add(this.element,'change',this.updateDepententFields);
};
G_Text.prototype=new G_Field();

function G_Percentage( form, element, name )
{
  var me=this;
  this.parent = G_Text;
  this.parent( form, element, name);
  //this.validate = 'Int'; //Commented for allow enter the character '.'
  this.mType = 'percentage';
  this.mask= '###.##';
  this.comma_separator = ".";
}
G_Percentage.prototype=new G_Field();

function G_Currency( form, element, name )
{
  var me=this;
  this.parent = G_Text;
  this.parent( form, element, name);
  //this.validate = 'Int'; //Commented for allow enter the character '.'
  this.mType = 'currency';
  this.mask= '_###,###,###,###,###;###,###,###,###,###.00';
  this.comma_separator = ".";
}
G_Currency.prototype=new G_Field();

function G_TextArea( form, element, name )
{
  var me=this;
  this.parent = G_Text;
  this.parent( form, element, name );
  this.validate = 'Any';
  this.mask= '';
}
G_TextArea.prototype=new G_Field();

function G_Date( form, element, name )
{
  var me=this;
  this.parent = G_Text;
  this.parent( form, element, name );
  this.mType = 'date';
  this.mask= 'dd-mm-yyyy';
}
G_Date.prototype=new G_Field();

function G()
{
  /*MASK*/
  var reserved=['_',';','#','.','0','d','m','y','-'];
  //Invert String
  function invertir(num)
  {
    var num0='';
    num0=num;
    num="";
    for(r=num0.length-1;r>=0;r--) num+= num0.substr(r,1);
    return num;
  }

  function __toMask(num, mask, cursor)
  {
    var inv=false;
    if (mask.substr(0,1)==='_') {
      mask=mask.substr(1);
      inv=true;
    }
    var re;
    if (inv) {
      mask=invertir(mask);
      num=invertir(num);
    }
    var minAdd=-1;
    var minLoss=-1;
    var newCursorPosition=cursor;
    var betterOut="";
    for(var r0=0;r0< mask.length; r0++){
      var out="";var j=0;
      var loss=0;
      var add=0;
      var cursorPosition=cursor;var i=-1;var dayPosition=0;var mounthPosition=0;
      var dayAnalized ='';var mounthAnalized ='';
      var blocks={}; //Declares empty object
      for(var r=0;r< r0 ;r++) {
        var e=false;
        var m=mask.substr(r,1);
        __parseMask();
      }
      i=0;
      for(r=r0;r< mask.length;r++) {
        j++;
        if (j>200) break;
        e=num.substr(i,1);
        e=(e==='')?false:e;
        m=mask.substr(r,1);
        __parseMask();
      }
      var io=num.length - i;
      io=(io<0)?0:io;
      loss+=io;
      loss=loss+add/1000;
      //var_dump(loss);
      if (loss===0) {
        betterOut=out;
        minLoss=0;
        newCursorPosition=cursorPosition;
        break;
      }
      if ((minLoss===-1)||(loss< minLoss)) {
        minLoss=loss;
        betterOut=out;
        newCursorPosition=cursorPosition;
      }
      //echo('min:');var_dump($minLoss);
    }
    //  var_dump($minLoss);
    out=betterOut;
    if (inv) {
      out=invertir(out);
      mask=invertir(mask);
    }
    return {
      'result':out,
      'cursor':newCursorPosition,
      'value':minLoss,
      'mask':mask
    };
    function searchBlock( where , what )
    {
      for(var r=0; r < where.length ; r++ ) {
        if (where[r].key === what) return where[r];
      }
    }
    function __parseMask()
    {
      var ok=true;
      switch(false) {
        case m==='d':
          dayAnalized='';
        break;
        case m==='m':
          mounthAnalized='';
        break;
        default:
      }
      if ( e!==false ) {
        if (typeof(blocks[m])==='undefined') blocks[m] = e; else blocks[m] += e;
      }
      switch(m) {
        case '0':
          if (e===false) {
            out+='0';
            add++;
            break;
          }
        case 'y':
        case '#':
          if (e===false) {
            out+='';
            break;
          }
          //Use direct comparition to increse speed of processing
          if ((e==='0')||(e==='1')||(e==='2')||(e==='3')||(e==='4')||(e==='5')||(e==='6')||(e==='7')||(e==='8')||(e==='9')||(e==='-')) {
            out+=e;
            i++;
          } else {
            //loss
            loss++;
            i++;
            r--;
          }
          break;
        case '(':
          if (e===false) {
            out+='';
            break;
          }
          out+=m;
          if (i<cursor){
            cursorPosition++;
          }
          break;
        case 'd':
          if (e===false) {
            out+='';
            break;
          }
          if ((e==='0')||(e==='1')||(e==='2')||(e==='3')||(e==='4')||(e==='5')||(e==='6')||(e==='7')||(e==='8')||(e==='9')) ok=true; else ok=false;
          //if (ok) if (dayPosition===0) if (parseInt(e)>3) ok=false
          //dayPosition=(dayPosition+1) | 1;
          if (ok) dayAnalized = dayAnalized + e;
          if ((ok) && (parseInt(dayAnalized)>31)) ok = false;
          if (ok) {
            out+=e;
            i++;

          } else {
            //loss
            loss++;
            i++;
            r--;
          }
          break;
        case 'm':
          if (e===false) {
            out+='';
            break;
          }
          if ((e==='0')||(e==='1')||(e==='2')||(e==='3')||(e==='4')||(e==='5')||(e==='6')||(e==='7')||(e==='8')||(e==='9')) ok=true; else ok=false;
          if (ok) mounthAnalized = mounthAnalized + e;
          if ((ok) && (parseInt(mounthAnalized)>12)) ok=false;
          if (ok) {
            out+=e;
            i++;
          } else {
            //loss
            loss++;
            i++;
            r--;
          }
          break;

        default:
          if (e===false) {
            out+='';
            break;
          }
        if (e===m) {
          out+=e;
          i++;
        }else {

          //if (m==='.') alert(i.toString() +'.'+ cursor.toString());
          out+=m;
          /////here was krlos
          add++;
          if (i<cursor){
            cursorPosition++;
          };
          /*if(e!==false && m==="." && m!=="," ){
                          //alert(m)
                          //out+=m;
                          //alert(m)

                          break
                          }*/
          //alert(m+'\n'+mask+'\n'+out+'\n'+num) }
          /*if(m!='-'){ out+=m;}
          else {out+=String.fromCharCode(45);}
        add++;if (i<cursor){cursorPosition++;};*/
        }
      }
    }
  }

  //Get only numbers including decimal separator
  function _getOnlyNumbers(num, _DEC){
    var _num = '';
    _aNum = num.split('');
    for (var d=0; d < _aNum.length; d++){
      switch(_aNum[d]){
        case '-': case '0': case '1': case '2': case '3': case '4':
        case '5': case '6': case '7': case '8': case '9': case _DEC:
          _num = _num + _aNum[d];
          break;
      }
    }
    return _num;
  }

  //Get only mask characters can be replaced by digits.
  function _getOnlyMask(mask, _DEC){
    var _mask='';
    _aMask = mask.split('');
    for (var d=0; d < _aMask.length; d++){
      switch(_aMask[d]){
        case '0': case '#': case 'd': case 'm': case 'y': case '_': case _DEC:
          _mask += _aMask[d];
          break;
      }
    }
    return _mask;
  }

  //Check if a new digit can be added to this mask
  function _checkNumber(num, mask){
    var __DECIMAL_SEP = '.';
    var aNum = _getOnlyNumbers(num, __DECIMAL_SEP);
    var _outM = aNum;
    var aMask = _getOnlyMask(mask, __DECIMAL_SEP);
    if (aMask.indexOf(__DECIMAL_SEP+'0')>0){ //Mask has .0
      eMask = aMask.replace(__DECIMAL_SEP, '');
      eNum = aNum.replace(__DECIMAL_SEP, '');
      if (eNum.length > eMask.length){
        _outM = aNum.substring(0, eMask.length +1);
      }
    }else{ //Mask hasn't .0
      if (aMask.indexOf(__DECIMAL_SEP)>0){ // Mask contains decimal separator
        iMask = aMask.split(__DECIMAL_SEP);
        if (aNum.indexOf(__DECIMAL_SEP)>0){ //Number has decimal separator
          iNum = aNum.split(__DECIMAL_SEP);
          if (iNum[1].length > iMask[1].length){
            _outM = iNum[0] + __DECIMAL_SEP + iNum[1].substr(0,iMask[1].length);
          }else{
            if (iNum[0].length > iMask[0].length){
              _outM = iNum[0].substr(0, iMask[0].length) + __DECIMAL_SEP + iNum[1];
            }
          }
        }else{ //Number has not decimal separator
          if (aNum.length > iMask[0].length){
            _outM = aNum.substr(0, iMask[0].length);
          }
        }
      }else{ //Mask does not contain decimal separator
        if (aNum.indexOf(__DECIMAL_SEP)>0){
          iNum = aNum.split(__DECIMAL_SEP);
          if (iNum[0].length > aMask.length){
            _outM = iNum[0].substr(0,aMask.length);
          }
        }else{
          if (aNum.length > aMask.length){
            _outM = aNum.substr(0,aMask.length);
          }
        }
      }
    }
    //alert(_outM);
    return _outM;
  }

  //Apply a mask to a number
   this.ApplyMask = function(num, mask, cursor, dir, comma_sep){
     var myOut = "";
     var myCursor = cursor;
     var key = "";

     if (num.length == 0) {
         return {result: "", cursor: 0};
     }

     switch (dir) {
       case 'forward':
         iMask = mask.split("");
         value = _getOnlyNumbers(num, "");
         iNum = value.split("");

         var swMinus = (iNum.length > 0 && iNum[0] == "-")? 1 : 0;

         if (swMinus == 1) {
             key = iNum.shift();
         }

         for(e=0; e < iMask.length && iNum.length > 0; e++){
           switch(iMask[e]){
             case '#': case '0': case 'd': case 'm': case 'y': case 'Y':
              if (iNum.length > 0){
                key = iNum.shift();
                myOut += key;
              }
              break;
            default:
              myOut += iMask[e];
              if (e < myCursor) myCursor++;
              break;
           }
         }

         if (swMinus == 1) {
             myOut = "-" + myOut;
         }
         break;
       case 'reverse':
         var __DECIMAL_SEP = comma_sep;
         var osize = num.length;
         num =  _getOnlyNumbers(num,__DECIMAL_SEP);
         if (num.length == 0) return {result: '', cursor: 0};
         var iNum = invertir(num);
         var iMask = invertir(mask);
         if (iMask.indexOf('0'+__DECIMAL_SEP)> 0){ //Mask has .0 and will applied complete
           aMask = iMask;
           iNum = _getOnlyNumbers(iNum,'*');
           aNum = iNum;
           eMask = aMask.split('');
           eNum = aNum.split('');
           _cout = '';
           for (e=0; e < eMask.length; e++){
             switch(eMask[e]){
               case '#': case '0':
                 if (eNum.length > 0){
                   key = eNum.shift();
                   _cout += key;
                 }
                 break;
               case '.': case ',':
                 if (eMask[e] != __DECIMAL_SEP){
                   if (eNum.length > 0){
                     _cout += eMask[e];
                   }
                 }else{
                   _cout += eMask[e];
                 }
                 break;
               default:
                 _cout += eMask[e];
                 break;
             }
           }
           myOut = _cout;
        }else{
          sw_d = false;
          aMask = iMask.split(__DECIMAL_SEP);
          aNum = iNum.split(__DECIMAL_SEP);
          if (aMask.length==1){
            dMask = '';
            cMask = aMask[0];
          }else{
            dMask = aMask[0];
            cMask = aMask[1];
          }
          if (aNum.length == 1){
            dNum = '';
            cNum = aNum[0];
          }else{
            sw_d = true;
            dNum = aNum[0];
            cNum = aNum[1];
          }
          _dout = '';

          pMask = dMask.split('');
          pNum = dNum.split('');
          for (p=0; p < pMask.length; p++){
            switch(pMask[p]){
              case '#': case '0':
                if (pNum.length > 0){
                  key = pNum.shift();
                  _dout += key;
                }
                break;
              case ',': case '.':
                if (pMask[p] != __DECIMAL_SEP){
                  if (pNum.length > 0){
                    _dout += pMask[p];
                  }
                }else{

                }
                break;
              default:
                _dout += pMask[p];
                break;
            }
          }
          _cout = '';
          sw_c = false;
          pMask = cMask.split('');
          pNum = cNum.split('');
          for (p=0; p < pMask.length; p++){
            switch(pMask[p]){
              case '#': case '0': case 'd': case 'm': case 'y':
                if (pNum.length > 0){
                  key = pNum.shift();
                  _cout += key;
                  sw_c = true;
                }
                break;
              case ',': case '.':
                if (pMask[p] != __DECIMAL_SEP){
                  if (pNum.length > 0 && pNum[0] != '-'){
                    _cout += pMask[p];
                  }
                }
                break;
              default:
                if (pNum.length > 0 && pNum[0] == '-') {
                  key = pNum.shift();
                  _cout += key;
                }
                _cout += pMask[p];
            }
          }
          if (pNum.length > 0 && pNum[0] == '-') {
            key = pNum.shift();
            _cout += key;
          }
          if (sw_c && sw_d){
            myOut = _dout + __DECIMAL_SEP + _cout;
          }else{
            myOut = _dout + _cout;
          }
        }
        myOut = invertir(myOut);
        tmpCursor = 0;
        aOut = myOut.split('');
        if (cursor == 0){
          for(l=0; l < aOut.length; l++){
            switch(aOut[l]){
              case '0': case '1': case '2': case '3': case '4':
              case '5': case '6': case '7': case '8': case '9':
              case __DECIMAL_SEP:
                myCursor = l;
                l = aOut.length;
                break;
            }
          }
        }
        else if(cursor == num.length){
          var last = 0;

          for(l=0; l < aOut.length; l++){
            switch(aOut[l]){
              case '0': case '1': case '2': case '3': case '4':
              case '5': case '6': case '7': case '8': case '9':
              case __DECIMAL_SEP:
                last = l;
                break;
            }
          }
          myCursor = last + 1;
        }
        else{
          aNum = num.split('');
          offset = 0;
          aNewNum = myOut.split('');
          for (a = 0; a < cursor; a++){
             notFinded = false;
             while (aNum[a] != aNewNum[a + offset] && !notFinded){
               offset++;
               if (a + offset > aNewNum.length){
                 offset = -1;
                 notFinded = true;
               }
             }
          }
          myCursor = cursor + offset;
        }

        break;
     }
     //myCursor += 1;
    /*if (dir){
      var osize = num.length;
      _out = '';
      num = _checkNumber(num, mask);
      num =  _getOnlyNumbers(num,'');
      if (num.length == 0) return {result: '', cursor: 0};
      iNum = num;
      iMask = mask;
      eMask = iMask.split('');
      eNum = iNum.split('');
      for (e=0; e < eMask.length; e++){
        switch(eMask[e]){
          case '#': case '0': case 'd': case 'm': case 'y': case 'Y':
            if (eNum.length > 0){
              key = eNum.shift();
              _out += key;
            }
            break;
          default:
            _out += eMask[e];
            break;
        }
      }
    }else{
      var __DECIMAL_SEP = comma_sep;
      var osize = num.length;
      num = _checkNumber(num, mask);
      num =  _getOnlyNumbers(num,__DECIMAL_SEP);
      if (num.length == 0) return {result: '', cursor: 0};
      var iNum = invertir(num);
      var iMask = invertir(mask);
      if (iMask.indexOf('0'+__DECIMAL_SEP)> 0){ //Mask has .0 and will applied complete
        aMask = iMask;
        iNum = _getOnlyNumbers(iNum,'*');
        aNum = iNum;
        eMask = aMask.split('');
        eNum = aNum.split('');
        _cout = '';
        for (e=0; e < eMask.length; e++){
          switch(eMask[e]){
            case '#': case '0': case 'd': case 'm': case 'y':
              if (eNum.length > 0){
                key = eNum.shift();
                _cout += key;
              }
              break;
            case '.': case ',':
              if (eMask[e] != __DECIMAL_SEP){
                if (eNum.length > 0){
                  _cout += eMask[e];
                }
              }else{
                _cout += eMask[e];
              }
              break;
            default:
              _cout += eMask[e];
            break;
          }
        }
        _out = _cout;
      }else{
        sw_d = false;
        aMask = iMask.split(__DECIMAL_SEP);
        aNum = iNum.split(__DECIMAL_SEP);
        if (aMask.length==1){
          dMask = '';
          cMask = aMask[0];
        }else{
          dMask = aMask[0];
          cMask = aMask[1];
        }
        if (aNum.length == 1){
          dNum = '';
          cNum = aNum[0];
        }else{
          sw_d = true;
          dNum = aNum[0];
          cNum = aNum[1];
        }
        _dout = '';

        pMask = dMask.split('');
        pNum = dNum.split('');
        for (p=0; p < pMask.length; p++){
          switch(pMask[p]){
            case '#': case '0':
              if (pNum.length > 0){
                key = pNum.shift();
                _dout += key;
              }
              break;
            case ',': case '.':
              if (pMask[p] != __DECIMAL_SEP){
                if (pNum.length > 0){
                  _dout += pMask[p];
                }
              }else{

              }
              break;
            default:
              _dout += pMask[p];
            break;
          }
        }
        _cout = '';
        sw_c = false;
        pMask = cMask.split('');
        pNum = cNum.split('');
        for (p=0; p < pMask.length; p++){
          switch(pMask[p]){
            case '#': case '0': case 'd': case 'm': case 'y':
              if (pNum.length > 0){
                key = pNum.shift();
                _cout += key;
                sw_c = true;
              }
              break;
            case ',': case '.':
              if (pMask[p] != __DECIMAL_SEP){
                if (pNum.length > 0){
                  _cout += pMask[p];
                }
              }
              break;
            default:
              _cout += pMask[p];
          }
        }
        if (sw_c && sw_d){
          _out = _dout + __DECIMAL_SEP + _cout;
        }else{
          _out = _dout + _cout;
        }
      }
      _out = invertir(_out);
    }
    if (_out.length > osize){
      cursor = cursor + (_out.length - osize);
    }*/
    return {
      'result': myOut,
      'cursor': myCursor
    };
  };

  //Manage Multiple Mask and Integer/Real Number restrictions
  this.toMask = function(num, mask, cursor, direction){
    if (mask==='') return {
      'result': new String(num),
      'cursor': cursor
    };
    num = new String(num);
    var result = [];
    var subMasks=mask.split(';');
    for(var r=0; r<subMasks.length; r++) {
      typedate = mask.indexOf("#");  //if typedate=='0' is current, else typedate=='-1' is date
      if ((direction == 'normal')&&(typedate=='0'))
        result[r]=__toMask(num, subMasks[r], cursor);
      else
        result[r]=_ApplyMask(num, subMasks[r], cursor, direction);
    }
    var betterResult=0;
    for(r=1; r<subMasks.length; r++) {
      if (result[r].value<result[betterResult].value) betterResult=r;
    }
    return result[betterResult];
 };

  //Gets number without mask
  this.getValue = function (elem) {
      return getNumericValue(elem.value(), ((typeof elem.comma_separator != "undefined")? elem.comma_separator : ""));
  };

  //DEPRECATED
  this.toMask2 = function (num, mask, cursor)
  {
    if (mask==='') return {
      'result':new String(num),
      'cursor':cursor
    };
    var subMasks=mask.split(';');
    var result = [];
    num = new String(num);
    for(var r=0; r<subMasks.length; r++) {
      result[r]=__toMask(num, subMasks[r], cursor);
    }
    var betterResult=0;
    for(r=1; r<subMasks.length; r++) {
      if (result[r].value<result[betterResult].value) betterResult=r;
    }
    return result[betterResult];
  };

  //DEPRECATED
  this.cleanMask = function (num, mask, cursor)
  {
    mask = typeof(mask)==='undefined'?'':mask;
    if (mask==='') return {
      'result':new String(num),
      'cursor':cursor
    };
    var a,r,others=[];
    num = new String(num);
    //alert(oDebug.var_dump(num));
    if (typeof(cursor)==='undefined') cursor=0;
    a = num.substr(0,cursor);
    for(r=0; r<reserved.length; r++) mask=mask.split(reserved[r]).join('');
    while(mask.length>0) {
      r=others.length;
      others[r] = mask.substr(0,1);
      mask= mask.split(others[r]).join('');
      num = num.split(others[r]).join('');
      cursor -= a.split(others[r]).length-1;//alert(cursor)
    }
    return {
      'result':num,
      'cursor':cursor
    };
  };
  this.getId=function(element){
    var re=/(\[(\w+)\])+/;
    var res=re.exec(element.id);
    return res?res[2]:element.id;
  };
  this.getObject=function(element){
    var objId=G.getId(element);
    switch (element.tagName){
      case 'FORM':
        return eval('form_' + objId);
        break;
      default:
        if (element.form) {
          var formId=G.getId(element.form);
          return eval('form_'+objId+'.getElementByName("'+objId+'")');
        }
    }
  };

  /*BLINK EFECT*/
  this.blinked=[];
  this.blinkedt0=[];
  this.autoFirstField=true;
  this.pi=Math.atan(1)*4;
  this.highLight = function(element){
    var newdiv = $dce('div');
    newdiv.style.position="absolute";
    newdiv.style.display="inline";
    newdiv.style.height=element.clientHeight+2;
    newdiv.style.width=element.clientWidth+2;
    newdiv.style.background = "#FF5555";
    element.style.backgroundColor='#FFCACA';
    element.parentNode.insertBefore(newdiv,element);
    G.doBlinkEfect(newdiv,1000);
  };
  this.setOpacity=function(e,o){
    e.style.filter='alpha';
    if (e.filters) {
      e.filters['alpha'].opacity=o*100;
    } else {
      e.style.opacity=o;
    }
  };
  this.doBlinkEfect=function(div,T){
    var f=1/T;
    var j=G.blinked.length;
    G.blinked[j]=div;
    G.blinkedt0[j]=(new Date()).getTime();
    for(var i=1;i<=20;i++){
      setTimeout("G.setOpacity(G.blinked["+j+"],0.3-0.3*Math.cos(2*G.pi*((new Date()).getTime()-G.blinkedt0["+j+"])*"+f+"));",T/20*i);
    }
    setTimeout("G.blinked["+j+"].parentNode.removeChild(G.blinked["+j+"]);G.blinked["+j+"]=null;",T/20*i);
  };
  var alertPanel;
  this.alert=function(html, title , width, height, autoSize, modal, showModalColor, runScripts)
  {
    html='<div>'+html+'</div>';
    width = (width)?width:300;
    height = (height)?height:200;
    autoSize = (showModalColor===false)?false:true;
    modal = (modal===false)?false:true;
    showModalColor = (showModalColor===true)?true:false;
    var alertPanel = new leimnud.module.panel();
    alertPanel.options = {
        size:{
          w:width,
          h:height
        },
        position:{
          center:true
        },
        title: title,
        theme: "processmaker",
        control: {
          close :true,
          roll  :false,
          drag  :true,
          resize  :true
        },
        fx: {
          blinkToFront:true,
          opacity :true,
          drag:true,
          modal: modal
        }
    };
    if(showModalColor===false)
    {
      alertPanel.styles.fx.opacityModal.Static='0';
    }
    alertPanel.make();
    alertPanel.addContent(html);
    if(runScripts)
    {
      var myScripts=alertPanel.elements.content.getElementsByTagName('SCRIPT');
      var sMyScripts=[];
      for(var rr=0; rr<myScripts.length ; rr++) sMyScripts.push(myScripts[rr].innerHTML);
      for(var rr=0; rr<myScripts.length ; rr++){
        try {
          if (sMyScripts[rr]!=='')
            if (window.execScript)
              window.execScript( sMyScripts[rr], 'javascript' );
            else
              window.setTimeout( sMyScripts[rr], 0 );
        } catch (e) {
          alert(e.description);
        }
      }
    }
    /* Autosize of panels, to fill only the first child of the
     * rendered page (take note)
     */
    var panelNonContentHeight = 44;
    var panelNonContentWidth  = 28;
    try {
      if (autoSize)
      {
        var newW=alertPanel.elements.content.childNodes[0].clientWidth+panelNonContentWidth;
        var newH=alertPanel.elements.content.childNodes[0].clientHeight+panelNonContentHeight;
        alertPanel.resize({
          w:((newW<width)?width:newW)
        });
        alertPanel.resize({
          h:((newH<height)?height:newH)
        });
      }
    } catch (e) {
      alert(var_dump(e));
    }
    delete newdiv;
    delete myScripts;
    alertPanel.command(alertPanel.loader.hide);
  };
}

var G = new G();


/* PACKAGE : DEBUG
 */
function G_Debugger()
{
  this.var_dump = function(obj)
  {
    var o,dump;
    dump='';
    if (typeof(obj)=='object')
      for(o in obj)
      {
        dump+='<b>'+o+'</b>:'+obj[o]+"<br>\n";
      }
    else
      dump=obj;
    debugDiv = document.getElementById('debug');
    if (debugDiv) debugDiv.innerHTML=dump;
    return dump;
  };
}
var oDebug = new G_Debugger();

/* PACKAGE : date field
 */
var datePickerPanel;

function showDatePicker(ev, formId, idName, value, min, max  ) {
  var coor = leimnud.dom.mouse(ev);
  var coorx = ( coor.x - 50 );
  var coory = ( coor.y - 40 );
  datePickerPanel=new leimnud.module.panel();
  datePickerPanel.options={
      size:{
        w:275,
        h:240
      },
      position:{
        x:coorx,
        y:coory
      },
      title:"Date Picker",
      theme:"panel",
      control:{
        close:true,
        drag:true
      },
      fx:{
        modal:true
      }
  };

  datePickerPanel.setStyle={
      containerWindow:{
        borderWidth:0
      }
  };
  datePickerPanel.make();
  datePickerPanel.idName = idName;
  datePickerPanel.formId = formId;

  var sUrl = "/controls/calendar.php?v="+value+"&d="+value+"&min="+min+"&max="+max;
  var r = new leimnud.module.rpc.xmlhttp({
    url: sUrl
  });
  r.callback=leimnud.closure({
    Function:function(rpc){
      datePickerPanel.addContent(rpc.xmlhttp.responseText);
    },
    args:r
  });
  r.make();

}

function moveDatePicker( n_datetime ) {
  var dtmin_value = document.getElementById ( 'dtmin_value' );
  var dtmax_value = document.getElementById ( 'dtmax_value' );

  var sUrl = "/controls/calendar.php?d="+n_datetime + '&min='+dtmin_value.value + '&max='+dtmax_value.value;
  var r = new leimnud.module.rpc.xmlhttp({
    url:sUrl
  });
  r.callback=leimnud.closure({
    Function:function(rpc){
      datePickerPanel.clearContent();
      datePickerPanel.addContent(rpc.xmlhttp.responseText);
    },
    args:r
  });
  r.make();
}

function selectDate(  day ) {
  var obj = document.getElementById ( 'span['+datePickerPanel.formId+'][' + datePickerPanel.idName + ']' );
  getField(datePickerPanel.idName, datePickerPanel.formId ).value = day;
  obj.innerHTML = day;
  datePickerPanel.remove();
}

function set_datetime(n_datetime, b_close) {
  moveDatePicker(n_datetime);
}

/* Functions for show and hide rows of a simple xmlform.
 * @author David Callizaya <davidsantos@colosa.com>
 */
function getRow( name ){
  try{
    var element = null;
    if (typeof(name)==='string'){
      element = getField(name);
      /** Set to hide/show of objects "checkgroup" and "radiogroup"
                @author: Hector Cortez
       */
      if(element == null){
        aElements = document.getElementsByName('form['+ name +'][]');
        if( aElements.length == 0)
          aElements = document.getElementsByName('form['+ name +']');
        if( aElements.length ){
          element = aElements[aElements.length-1];
        } else
          element = null;
      }
    }
    if( element != null){
      while ( element.tagName !== 'TR' ) {
        element=element.parentNode;
      }
      return element;
    } else {
      return null;
    }
  } catch(e){
    alert(e);
  }

}
var getRowById=getRow;

function hideRow( element ){ //neyek
  var row=getRow(element);
  if (row) row.style.display='none';
  removeRequiredById(element);
  delete row;
}

var hideRowById=hideRow;
function showRow( element ){
  var row=getRow(element);
  requiredFields = [];
  sRequiredFields = document.getElementById('DynaformRequiredFields').value.replace(/%27/gi, '"');

  fields = new String(sRequiredFields);
  fields = stripslashes(fields);
  requiredFieldsList = eval(fields);

  for(i=0; i<requiredFieldsList.length; i++){
    requiredFields[i] = requiredFieldsList[i].name;
  }

  if ( requiredFields.inArray(element) ) {
    enableRequiredById(element);
  }

  if (row) row.style.display='';
  delete row;
}
var showRowById=showRow;
function hideShowControl(element , name){
  var control;
  if (element) {
    control = element.parentNode.getElementsByTagName("div")[0];
    control.style.display=control.style.display==='none'?'':'none';
    if (control.style.display==='none') getField( name ).value='';
    delete control;
  }
}
/*SHOW/HIDE A SUBTITLE CONTENT*/
function contractSubtitle( subTitle ){
  subTitle=getRow(subTitle);
  var c=subTitle.cells[0].className;
  var a=subTitle.rowIndex;
  var t=subTitle.parentNode;
  for(var i=a+1,m=t.rows.length;i<m;i++){
    if (t.rows[i].cells.length==1) break;
    t.rows[i].style.display='none';
    var aAux = getControlsInTheRow(t.rows[i]);
    for (var j = 0; j < aAux.length; j++) {
      removeRequiredById(aAux[j]);
    }
  }
}
function expandSubtitle( subTitle ){
  subTitle = getRow(subTitle);
  var c = subTitle.cells[0].className;
  var a = subTitle.rowIndex;
  var t = subTitle.parentNode;
  for (var i=a+1,m=t.rows.length; i<m; i++) {
    if (t.rows[i].cells.length==1) {
      break;
    }
    t.rows[i].style.display='';
    var aAux = getControlsInTheRow(t.rows[i]);
    for (var j = 0; j < aAux.length; j++) {
      enableRequiredById(aAux[j]);
    }
  }
}
function contractExpandSubtitle(subTitleName){
  subTitle=getRow(subTitleName);
  var c=subTitle.cells[0].className;
  var a=subTitle.rowIndex;
  var t=subTitle.parentNode;
  var contracted=false;
  for(var i=a+1,m=t.rows.length;i<m;i++){
    if (t.rows[i].cells.length==1) break;
    if (t.rows[i].style.display==='none'){
      contracted=true;
    }
  }
  if (contracted) expandSubtitle(subTitleName);
  else contractSubtitle(subTitleName);
}

function concat_collection(obj1, obj2) {
    var i;
    var arr = new Array();
    var len1 = obj1.length;
    var len2 = obj2.length;
    for (i=0; i<len1; i++) {
        arr.push(obj1[i]);
    }
    for (i=0; i<len2; i++) {
        arr.push(obj2[i]);
    }
    return arr;
}
var getControlsInTheRow = function(oRow) {
  var aAux1 = [];
  if (oRow.cells) {
    var i;
    var j;
    var sFieldName;
    for (i = 0; i < oRow.cells.length; i++) {
      var aAux2 = oRow.cells[i].getElementsByTagName('input');

      aAux2 = concat_collection(aAux2, oRow.cells[i].getElementsByTagName('a'));
      aAux2 = concat_collection(aAux2, oRow.cells[i].getElementsByTagName('select'));
      aAux2 = concat_collection(aAux2, oRow.cells[i].getElementsByTagName('textarea'));
      if (aAux2) {
        for (j = 0; j < aAux2.length; j++) {
          sFieldName = aAux2[j].id.replace('form[', '');
          //sFieldName = sFieldName.replace(']', '');
          sFieldName = sFieldName.replace(/]$/, '');
          if (sFieldName != '') {
            aAux1.push(sFieldName);
          }
        }
      }
    }
  }
  return aAux1;
};

var notValidateThisFields = [];

/**
 * @function getElementByClassNameCrossBrowser
 * @sumary independent implementaction of the Firefox getElementsByClassName
 *         for CrossBrowser compatibility
 * @author gustavo cruz gustavo-at-colosa.com
 * @parameter className
 * @parameter node
 * @parameter tag
 * @return array
 * return the elements that are from the className
 */

function getElementsByClassNameCrossBrowser(searchClass,node,tag) {

  var classElements = new Array();
  if ( node == null )
    node = document;
  if ( tag == null )
    tag = '*';
  var els = node.getElementsByTagName(tag);
  var elsLen = els.length;
  var pattern = new RegExp("(^|\\s)"+searchClass+"(\\s|$)");
  for (i = 0, j = 0; i < elsLen; i++) {
    if ( pattern.test(els[i].className) ) {
      classElements[j] = els[i];
      j++;
    }
  }
  return classElements;
}

/**
 * @function validateGridForms
 * @sumary function to validate the elements of a grid form inside a normal
 *         form.
 * @author gustavo cruz gustavo-at-colosa.com
 * @parameter invalidFields
 * @return array
 * with the grid invalid fields added.
 * We need the invalidFields as a parameter
 *
 **/
var validateGridForms = function(invalidFields){

  grids   = getElementsByClassNameCrossBrowser("grid",document,"div");
  Tlabels = getElementsByClassNameCrossBrowser("tableGrid",document,"table");
  // grids = getElementsByClass("grid",document,"div");
  // grids = document.getElementsByClassName("grid");
  nameGrid = "";
  for(cnt=0; cnt<Tlabels.length; cnt++ ){
    if(Tlabels[cnt].getAttribute("name") ) {
      nameGrid = Tlabels[cnt].getAttribute("name");
      if (notValidateThisFields.inArray(nameGrid)) {
        return invalidFields;
      }
    }
  }

  for(j=0; j<grids.length; j++){

    fields = grids[j].getElementsByTagName('input');
    for(i=0; i<fields.length; i++){
      var vtext = new input(fields[i]);
      if (fields[i].getAttribute("pm:required")=="1"&&fields[i].value==''){
        $label = fields[i].name.split("[");
        $labelPM = fields[i].getAttribute("pm:label");
        if ($labelPM == '' || $labelPM == null){
          $fieldName = $label[3].split("]")[0]+ " " + $label[2].split("]")[0];
        }else{
          $fieldName = $labelPM + " " + $label[2].split("]")[0];
        }
        fieldGridName = $label[1] + "[" + $label[2] + "[" + $label[3].split("]")[0];

        if (!notValidateThisFields.inArray(fieldGridName)) {
          invalidFields.push($fieldName);
        }

        vtext.failed();
      } else {
        vtext.passed();
      }
    }

    textAreas = grids[j].getElementsByTagName('textarea');
    for(i=0; i<textAreas.length; i++){
      var vtext = new input(textAreas[i]);
      if (textAreas[i].getAttribute("pm:required")=="1"&&textAreas[i].value==''){
        $label = textAreas[i].name.split("[");
        $labelPM = textAreas[i].getAttribute("pm:label");
        if ($labelPM == '' || $labelPM == null){
          $fieldName = $label[3].split("]")[0]+ " " + $label[2].split("]")[0];
        }else{
          $fieldName = $labelPM + " " + $label[2].split("]")[0];
        }
        fieldGridName = $label[1] + "[" + $label[2] + "[" + $label[3].split("]")[0];

        if (!notValidateThisFields.inArray(fieldGridName)) {
          invalidFields.push($fieldName);
        }

        vtext.failed();
      } else {
        vtext.passed();
      }
    }

    dropdowns = grids[j].getElementsByTagName('select');
    for(i=0; i<dropdowns.length; i++){
      var vtext = new input(dropdowns[i]);

      if (dropdowns[i].getAttribute("pm:required")=="1"&&dropdowns[i].value==''){
        $label = dropdowns[i].name.split("[");
        $labelPM = dropdowns[i].getAttribute("pm:label");
        if ($labelPM == '' || $labelPM == null){
          $fieldName = $label[3].split("]")[0]+ " " + $label[2].split("]")[0];
        }else{
          $fieldName = $labelPM + " " + $label[2].split("]")[0];
        }
        fieldGridName = $label[1] + "[" + $label[2] + "[" + $label[3].split("]")[0];

        if (!notValidateThisFields.inArray(fieldGridName)) {
          invalidFields.push($fieldName);
        }

        vtext.failed();
      } else {
        vtext.passed();
      }
    }
  }

  return (invalidFields);
};

var changeStatusSubmitFields = function(newStatusTo) {
  var newStatus = newStatusTo == 'disabled';
  var formElements = document.getElementsByTagName('form');
  for (var i = 0; i < formElements.length; i++) {
    var inputElements = formElements[i].getElementsByTagName('input');
    for (var j = 0; j < inputElements.length; j++) {
      if (typeof(inputElements[j].type) != 'undefined') {
        if (inputElements[j].type == 'submit') {
            inputElements[j].disabled = newStatus;
        }
      }
    }
  }
};

/**
 *
 * This function validates via javascript
 * the required fields in the Json array in the form tag of a dynaform
 * now the required fields in a grid have a "required" atribute set in 1
 * @param String sRequiredFields
 *
 **/

var validateForm = function(sRequiredFields) {

    sFormName = document.getElementById('__DynaformName__');
    if ((typeof(sFormName) != 'undefined' && sFormName != 'login') && (typeof(__usernameLogged__) != 'undefined' && __usernameLogged__ != '') ) {
        if (!sessionPersits()) {
            showPromptLogin('session');
            return false;
        }
    }

  // Disabling submit buttons
  changeStatusSubmitFields('disabled');

  /**
   *  replacing the %27 code by " character (if exists), this solve the problem that " broke the properties definition into a html
   *  i.ei <form onsubmit="myaction(MyjsString)" ...   with var MyjsString = "some string that is into a variable, so this broke the html";
   */

  if( typeof(sRequiredFields) != 'object' || sRequiredFields.indexOf("%27") > 0 ){
    sRequiredFields = sRequiredFields.replace(/%27/gi, '"');
  }
  if( typeof(sRequiredFields) != 'object' || sRequiredFields.indexOf("%39") > 0 ){
    sRequiredFields = sRequiredFields.replace(/%39/gi, "'");
  }
  aRequiredFields = eval(sRequiredFields);

  var sMessage = '';
  var invalid_fields   = Array();
  var fielEmailInvalid = Array();

      for (var i = 0; i < aRequiredFields.length; i++) {
        aRequiredFields[i].label=(aRequiredFields[i].label=='')?aRequiredFields[i].name:aRequiredFields[i].label;

        if (!notValidateThisFields.inArray(aRequiredFields[i].name)) {

          if (typeof aRequiredFields[i].required != 'undefined'){
            required = aRequiredFields[i].required;
          }
          else {
            required = 1;
          }

          if (typeof aRequiredFields[i].validate != 'undefined') {
            validate = aRequiredFields[i].validate;
          }
          else {
            validate = '';
          }

          if(required == 1)
          {
            switch(aRequiredFields[i].type) {
              case 'suggest':
                var vtext1 = new input(getField(aRequiredFields[i].name+'_label'));
                if(getField(aRequiredFields[i].name).value==''){
                  invalid_fields.push(aRequiredFields[i].label);
                  vtext1.failed();
                } else {
                  vtext1.passed();
                }
              break;
              case 'text':
                var vtext = new input(getField(aRequiredFields[i].name));
                  if(getField(aRequiredFields[i].name).value=='') {
                    invalid_fields.push(aRequiredFields[i].label);
                    vtext.failed();
                  }
                  else {
                    vtext.passed();
                  }
                break;

              case 'dropdown':
                var vtext = new input(getField(aRequiredFields[i].name));
                if(getField(aRequiredFields[i].name).value==''){
                  invalid_fields.push(aRequiredFields[i].label);
                  vtext.failed();
                } else {
                  vtext.passed();
                }
                break;

              case 'textarea':

                var vtext = new input(getField(aRequiredFields[i].name));
                if(getField(aRequiredFields[i].name).value==''){
                  invalid_fields.push(aRequiredFields[i].label);
                  vtext.failed();
                } else {
                  vtext.passed();
                }

              break;

              case 'password':
                var vpass = new input(getField(aRequiredFields[i].name));
                if(getField(aRequiredFields[i].name).value==''){
                  invalid_fields.push(aRequiredFields[i].label);
                  vpass.failed();
                } else {
                  vpass.passed();
                }
                break;

              case 'currency':
                var vcurr = new input(getField(aRequiredFields[i].name));
                if(getField(aRequiredFields[i].name).value==''){
                  invalid_fields.push(aRequiredFields[i].label);
                  vcurr.failed();
                } else {
                  vcurr.passed();
                }
              break;

              case 'percentage':
                var vper = new input(getField(aRequiredFields[i].name));
                if(getField(aRequiredFields[i].name).value==''){
                  invalid_fields.push(aRequiredFields[i].label);
                  vper.failed();
                } else {
                  vper.passed();
                }
              break;

              case 'yesno':
                var vtext = new input(getField(aRequiredFields[i].name));
                if(getField(aRequiredFields[i].name).value==''){
                  invalid_fields.push(aRequiredFields[i].label);
                  vtext.failed();
                } else {
                  vtext.passed();
                }
              break;

              case 'date':
                var vtext = new input(getField(aRequiredFields[i].name));
                if(getField(aRequiredFields[i].name).value==''){
                  invalid_fields.push(aRequiredFields[i].label);
                  vtext.failed();
                } else {
                  vtext.passed();
                }
              break;

              case 'file':
                var vtext = new input(getField(aRequiredFields[i].name));
                if(getField(aRequiredFields[i].name).value==''){
                  invalid_fields.push(aRequiredFields[i].label);
                  vtext.failed();
                } else {
                  vtext.passed();
                }
              break;

              case 'listbox':
                var oAux = getField(aRequiredFields[i].name);
                var bOneSelected = false;
                for (var j = 0; j < oAux.options.length; j++) {
                  if (oAux.options[j].selected) {
                    bOneSelected = true;
                    j = oAux.options.length;
                  }
                }
                if(bOneSelected == false)
                  invalid_fields.push(aRequiredFields[i].label);
              break;

              case 'radiogroup':
                var x=aRequiredFields[i].name;
                var oAux = document.getElementsByName('form['+ x +']');
                var bOneChecked = false;
                for (var k = 0; k < oAux.length; k++) {
                  var r = oAux[k];
                  if (r.checked) {
                    bOneChecked = true;
                    k = oAux.length;
                  }
                }

                if(bOneChecked == false)
                  invalid_fields.push(aRequiredFields[i].label);

              break;

              case 'checkgroup':
                var bOneChecked = false;
                var aAux = document.getElementsByName('form[' + aRequiredFields[i].name + '][]');
                for (var k = 0; k < aAux.length; k++) {
                  if (aAux[k].checked) {
                    bOneChecked = true;
                    k = aAux.length;
                  }
                }
                if(!bOneChecked) {
                  invalid_fields.push(aRequiredFields[i].label);
                }

              break;
            }
          }

          if(validate != '') {
            //validate_fields
              switch(aRequiredFields[i].type) {
                case 'suggest':
                break;

                case 'text':

                  if(validate=="Email") {
                    var vtext = new input(getField(aRequiredFields[i].name));
                      if(getField(aRequiredFields[i].name).value!='') {
                        var email = getField(aRequiredFields[i].name);
                        //var filter = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
                        //var filter = /^[\w\_\-\.ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â§ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â±]{2,255}@[\w\_\-]{2,255}\.[a-z]{1,3}\.?[a-z]{0,3}$/;
                        var filter =/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;
                          if (!filter.test(email.value)&&email.value!="") {
                            fielEmailInvalid.push(aRequiredFields[i].label);
                            vtext.failed();
                            email.focus();
                          }
                          else {
                            vtext.passed();
                          }
                      }
                  }
                  break;
              }
          }
        }
      }

  // call added by gustavo - cruz, gustavo-at-colosa.com validate grid forms
  invalid_fields = validateGridForms(invalid_fields);

  if (invalid_fields.length > 0 ||fielEmailInvalid.length> 0) {
    //alert(G_STRINGS.ID_REQUIRED_FIELDS + ": \n\n" + sMessage);



    // loop for invalid_fields
    for(j=0; j<invalid_fields.length; j++){
      sMessage += (j > 0)? ', ': '';
      sMessage += invalid_fields[j];
    }

    // Loop for invalid_emails
    var emailInvalidMessage = "";
    for(j=0; j<fielEmailInvalid.length; j++){
      emailInvalidMessage += (j > 0)? ', ': '';
      emailInvalidMessage += fielEmailInvalid[j];
    }


    /* new leimnud.module.app.alert().make({
            label:G_STRINGS.ID_REQUIRED_FIELDS + ": <br/><br/>[ " + sMessage + " ]",
            width:450,
            height:140 + (parseInt(invalid_fields.length/10)*10)
        });*/

    //!create systemMessaggeInvalid of field invalids
    var systemMessaggeInvalid = "";

      if(invalid_fields.length > 0) {
        systemMessaggeInvalid += "\n \n"+G_STRINGS.ID_REQUIRED_FIELDS + ": \n \n [ " + sMessage + " ]";
      }

      if(fielEmailInvalid.length > 0) {
        systemMessaggeInvalid += "\n \n" +  G_STRINGS.ID_VALIDATED_FIELDS + ": \n \n [ " + emailInvalidMessage + " ]";
      }


    alert(systemMessaggeInvalid);
    // Enabling submit buttons
    changeStatusSubmitFields('enabled');
    return false;
  }
  else {
    var arrayForm = document.getElementsByTagName("form");
    var inputAux;
    var id = "";
    var i1 = 0;
    var i2 = 0;

    for (i1 = 0; i1 <= arrayForm.length - 1; i1++) {
      var frm = arrayForm[i1];

      for (i2 = 0; i2 <= frm.elements.length - 1; i2++)  {
        var elem = frm.elements[i2];

        if (elem.type == "checkbox" && elem.disabled && elem.checked) {
          id = elem.id + "_";

          if (!document.getElementById(id)) {
              inputAux       = document.createElement("input");
              inputAux.type  = "hidden";
              inputAux.id    = id;
              inputAux.name  = elem.name;
              inputAux.value = elem.value;

              frm.appendChild(inputAux);
          }
        }
      }

      var arrayLink = frm.getElementsByTagName("a");

      for (i2 = 0; i2 <= arrayLink.length - 1; i2++)  {
          var link = arrayLink[i2];

          if (typeof link.id != "undefined" && link.id != "" && link.id != "form[DYN_BACKWARD]" && link.id != "form[DYN_FORWARD]") {
              var strHtml = link.parentNode.innerHTML;

              strHtml = stringReplace("\\x0A", "", strHtml); //\n 10
              strHtml = stringReplace("\\x0D", "", strHtml); //\r 13
              strHtml = stringReplace("\\x09", "", strHtml); //\t  9

              if (/^.*pm:field.*$/.test(strHtml)) {
                  id = link.id + "_";

                  if (!document.getElementById(id)) {
                      var strAux = link.id.replace("form[", "");
                      strAux = strAux.substring(0, strAux.length - 1);

                      inputAux       = document.createElement("input");
                      inputAux.type  = "hidden";
                      inputAux.id    = id;
                      inputAux.name  = link.id;
                      inputAux.value = link.href;

                      frm.appendChild(inputAux);

                      inputAux   = document.createElement("input");
                      inputAux.type  = "hidden";
                      inputAux.id    = id + "label";
                      inputAux.name  = "form[" + strAux + "_label]";
                      inputAux.value = link.innerHTML;

                      frm.appendChild(inputAux);
                  }
              }
          }
      }
    }

    return true;
  }
};


var getObject = function(sObject) {
  var i;
  var oAux = null;
  var iLength = __aObjects__.length;
  for (i = 0; i < iLength; i++) {
    oAux = __aObjects__[i].getElementByName(sObject);
    if (oAux) {
      return oAux;
    }
  }
  return oAux;
};

var saveAndRefreshForm = function(oObject) {
  if (oObject) {
    oObject.form.action += '&_REFRESH_=1';
    oObject.form.submit();
  }
  else {
    var oAux = window.document.getElementsByTagName('form');
    if (oAux.length > 0) {
      oAux[0].action += '&_REFRESH_=1';
      oAux[0].submit();
    }
  }
};


/**
 * @function sessionPersits
 *
 * @returns {@exp;response@pro;status}
 */
var sessionPersits = function() {
    var rpc = new leimnud.module.rpc.xmlhttp({
        url: '../services/sessionPersists',
        args: 'dynaformRestoreValues=' + __dynaformSVal__,
        async: false
    });
    rpc.make();
    var response = rpc.xmlhttp.responseText.parseJSON();
    return response.status;
};

/**
 * @function showPromptLogin
 *
 * @param {type} lastAction
 * @returns {showPrompt}
 */
var showPromptLogin = function(lastAction) {
    lastActionPerformed = lastAction;
    promptPanel = new leimnud.module.panel();
    promptPanel.options={
        statusBarButtons:[{value: _('LOGIN')}],
        position:{center:true},
        size:{w:300,h:130},
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
    thePassword.type = 'password';
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

/**
 * @function verifyLogin
 *
 * @returns {unresolved}
 */
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
            lastActionPerformed = '';
        } else {
            alert(_('ID_WRONG_PASS'));
        }
    }.extend(this);
    rpc.make();
};

/**
 * @function saveForm
 * @author gustavo cruz gustavo[at]colosa[dot]com
 * @param  oObject is a reference to the object which is attached to the event,
 *         for example can be a save button, or anything else.
 * @return This function only makes an ajax post to the form action
 * @desc   saveForm takes a object reference as a parameter, from that extracts
 *         the form and the form action references, then executes an Ajax request
 *         that post the form data to the action url, so the form is never
 *         refreshed or submited, at least not in the traditional sense.
 **/

var saveForm = function(oObject, actionParameter) {
  if (oObject) {
    var actionUrl = actionParameter || oObject.form.action.replace('cases_SaveData', 'saveForm');
    ajax_post(actionUrl, oObject.form, 'POST');
  }
  else {
    var oAux = window.document.getElementsByTagName('form');
    if (oAux.length > 0) {
      var actionUrl = actionParameter || oAux[0].action.replace('cases_SaveData', 'saveForm');
      ajax_post(actionUrl, oAux[0], 'POST');
    }
  }
};


/**
 * @function validateUrl
 * @author  gustavo cruz gustavo[at]colosa[dot]com
 * @param   url is the url to be validated.
 * @return  true/false.
 * @desc    takes a url as parameter and check returning a boolean if it's valid or not.
 **/

var validateURL = function (url){
  var regexp = /http?s?:\/\/([-\w\.]+)+(:\d+)?(\/([\w/_\.]*(\?\S+)?)?)?/;
  if (regexp.test(url)) {
    return true;
  } else {
    return false;
  }
};



/**
 * @function saveAndRedirectForm
 * @author  gustavo cruz gustavo[at]colosa[dot]com
 * @param   oObject is a reference to the object which is attached to the event,
 *          for example can be a save button, or anything else.
 * @param   oLocation is the url of tha redirect location.
 * @return  This function only makes a non refresh save a redirection action.
 * @desc    saveAndRedirectForm takes a object reference as a parameter, and
 *          then invoques the saveForm() function, so after the form data is saved,
 *          validates the url passed as parameter, if it's valid then redirects
 *          the browser to the oLocation url.
 **/

var saveAndRedirectForm = function(oObject, oLocation) {
  saveForm(oObject);
  if (validateURL(oLocation)) {
    if (typeof(parent) != "undefined") {
      parent.location.href = oLocation;
    } else {
      document.location.href = oLocation;
    }
  }
};


var removeRequiredById = function(sFieldName) {
  if (!notValidateThisFields.inArray(sFieldName)) {
    notValidateThisFields.push(sFieldName);
    var oAux = document.getElementById('__notValidateThisFields__');
    if (oAux) {
      oAux.value = notValidateThisFields.toJSONString();
    }
  }
};

var enableRequiredById = function(sFieldName) {
  if (notValidateThisFields.inArray(sFieldName)) {
    var i;
    var aAux = [];
    for(i = 0; i < notValidateThisFields.length; i++) {
      if(notValidateThisFields[i] != sFieldName) {
        aAux.push(notValidateThisFields[i]);
      }
    }
    notValidateThisFields = aAux;
    var oAux = document.getElementById('__notValidateThisFields__');
    if (oAux) {
      oAux.value = notValidateThisFields.toJSONString();
    }
  }
};


function dynaformVerifyFieldName(){
  pme_validating = true;
  setTimeout('verifyFieldName1();',0);
  return true;
}

function verifyFieldName1() {
  if (getField('PME_VALIDATE_NAME').value == '__error_session__') {
    showPrompt('refreshDynaformEditor');
    return;
  }
  verifyFieldNameFunction();
}

function verifyFieldNameFunction() {
  var newFieldName=fieldName.value;
  var msj = _('DYNAFIELD_ALREADY_EXIST');
  var validatedFieldName=getField("PME_VALIDATE_NAME",fieldForm).value;
  var dField = new input(getField('PME_XMLNODE_NAME'));

  var valid=(newFieldName!=='')&&(((newFieldName!==savedFieldName)&&(validatedFieldName===''))||((newFieldName===savedFieldName)));
  if (newFieldName.length == 0) {
    valid = false;
    msj   = _('DYNAFIELD_EMPTY');
  }

  if (!(isNaN(parseInt(newFieldName.substr(0,1))))) {
    valid = false;
    msj   = _('DYNAFIELD_NODENAME_NUMBER');
  }

  if (valid){
    dField.passed();
    getField("PME_ACCEPT",fieldForm).disabled=false;
  }else{
    getField("PME_ACCEPT",fieldForm).disabled=true;
    dField.failed();
    new leimnud.module.app.alert().make({
      label: msj
    });
    dField.focus();
  }
  pme_validating=false;
  return valid;
}

function refreshDynaformEditor() {
  window.location.href = window.location.href.replace('#', '');
}

var objectsWithFormula = Array();

function sumaformu(ee,fma,mask){
  //copy the formula
  afma=fma;
  var operators=['+','-','*','/','(','[','{','}',']',')',',','Math.pow','Math.PI','Math.sqrt'];
  var wos;
  //replace the operators symbols for empty space
  for(var i=0 ; i < operators.length ; i++) {
    var j=0;
    while(j < fma.length){
      nfma=fma.replace(operators[i]," ");
      nfma=nfma.replace("  "," ");
      fma=nfma;
      j++;
    }

  }
  //without spaces in the inicio of the formula
  wos=nfma.replace(/^\s+/g,'');
  nfma=wos.replace(/\s+$/g,'');
  theelemts=nfma.split(" ");

  objectsWithFormula[objectsWithFormula.length]= {ee:ee,fma:afma,mask:mask,theElements:theelemts};

  for (var i = 0; i < theelemts.length; i++) {
    leimnud.event.add(getField(theelemts[i]), 'keyup', function(key) {
      //leimnud.event.add(getField(objectsWithFormula[objectsWithFormula.length-1].theElements[i]),'keyup',function(){

      var eventElement = key.srcElement ? key.srcElement : key.target;
      if ( typeof(this.id) == 'undefined' ) {
        myId = eventElement.id.replace("form[", "").replace("]", "");
      }
      else {
        myId = this.id.replace("form[", "").replace("]", "");
      }

      for(i_elements=0;i_elements < objectsWithFormula.length; i_elements++){
        for(i_elements2=0;i_elements2 < objectsWithFormula[i_elements].theElements.length;i_elements2++){
          if(objectsWithFormula[i_elements].theElements[i_elements2]==myId)
          {
              var formula = objectsWithFormula[i_elements].fma;
              var ans = objectsWithFormula[i_elements].ee;
              var theelemts = objectsWithFormula[i_elements].theElements;

              //Evaluate the formula and replace the value in field
              for (var i = 0; i <= theelemts.length - 1; i++) {
                  var elem = getField(theelemts[i]);
                  var elemAttribute = elementAttributesNS(elem, "pm");
                  var elemValue = getNumericValue(elem.value, ((typeof elemAttribute.decimal_separator != "undefined")? elemAttribute.decimal_separator : ""));

                  formula = formula.replace(theelemts[i], ((elemValue == "")? 0 : parseFloat(elemValue)));
              }

              var result = eval(formula);

              if (mask != "") {
                  var elemAttribute = elementAttributesNS(ans, "pm");

                  putFieldNumericValue(ans, result, mask, ((typeof elemAttribute.decimal_separator != "undefined")? elemAttribute.decimal_separator : ""));
              } else {
                  ans.value = result;
              }
          }
        }
      }
    });
  }
}

function showRowsById(aFields){

  for(i=0; i<aFields.length; i++){
    row = getRow(aFields[i]);
    if( row ){
      row.style.display='';
    }
  }
}

function hideRowsById(aFields){
  for(i=0; i<aFields.length; i++){
    row = getRow(aFields[i]);
    if( row ){
      row.style.display='none';
    }
  }
}

function dateSetMask(mask) {
  if (mask != '') {
    mask = stringReplace("%y", "yy", mask);
    mask = stringReplace("%Y", "yyyy", mask);

    mask = stringReplace("%m", "mm", mask);
    mask = stringReplace("%o", "mm", mask);

    mask = stringReplace("%d", "dd", mask);
    mask = stringReplace("%e", "dd", mask);

    //In the function getCleanMask valid characters for an mask that does not
    //is currency/percentage are: '0 ',' # ',' d ',' m ',' y ',' Y '.
    //For hours, minutes and seconds replace this mask with '#'
    mask = stringReplace("%H", "##", mask);
    mask = stringReplace("%I", "##", mask);
    mask = stringReplace("%k", "##", mask);
    mask = stringReplace("%l", "##", mask);

    mask = stringReplace("%M", "##", mask);
    mask = stringReplace("%S", "##", mask);

    mask = stringReplace("%j", "###", mask);
  }

  return mask;
}

function putFieldNumericValue(elem, num, mask, decimalSeparator)
{
    var strNum = num.toString();
    var arrayAux = [];

    var maskNumber  = "";
    var maskDecimal = "";

    if (decimalSeparator != "" && mask.indexOf(decimalSeparator) != -1) {
        arrayAux = mask.split(decimalSeparator);

        maskNumber  = arrayAux[0];
        maskDecimal = arrayAux[1];
    } else {
        maskNumber  = mask;
        maskDecimal = "";
    }

    var n = "";
    var d = "";

    if (strNum.indexOf(".") != -1) {
        arrayAux = strNum.split(".");

        n = arrayAux[0];
        d = arrayAux[1];
    } else {
        n = strNum;
        d = "";
    }

    var i = 0;
    var cont = 0;
    var pos = maskNumber.indexOf("#");

    if (pos != -1) {
        var mask1 = maskNumber.substring(0, pos);

        var strAux = maskNumber.split("").reverse().join("");
        cont = 0;
        pos = -1;

        for (i = 0; i <= strAux.length - 1; i++) {
            if (strAux.charAt(i) == "#") {
                cont = cont + 1;

                if (cont == n.length) {
                    pos = i;
                    break;
                }
            }
        }

        var mask2 = "";

        if (pos != -1) {
            mask2 = strAux.substring(0, pos + 1);
            mask2 = mask2.split("").reverse().join("");
        } else {
            mask1 = maskNumber;
        }

        maskNumber = mask1 + mask2;
    }

    var newNumber  = putStringMask(n, maskNumber, "reverse");
    var newDecimal = putStringMask(d, maskDecimal, "forward");

    elem.value = newNumber + decimalSeparator + newDecimal;
}

function putStringMask(str, mask, dir)
{
    var newStr = "";
    var i1 = 0;
    var i2 = 0;

    if (dir == "reverse") {
        str = str.split("").reverse().join("");
        mask = mask.split("").reverse().join("");
    }

    for (i1 = 0; i1 <= mask.length - 1; i1++) {
        switch (mask.charAt(i1)) {
            case "#":
                if (i2 <= str.length - 1) {
                    newStr = newStr + str.charAt(i2);

                    i2 = i2 + 1;
                } else {
                    newStr = newStr + "0";
                }
                break;

            default:
                newStr = newStr + mask.charAt(i1);
                break;
        }
    }

    if (dir == "reverse") {
        newStr = newStr.split("").reverse().join("");
    }

    return newStr;
}

function getNumericValue(val, decimalSeparator)
{
    var arrayNum = val.split("");
    var num = "";

    for (var i = 0; i <= arrayNum.length - 1; i++) {
        switch (arrayNum[i]) {
            case "0":
            case "1":
            case "2":
            case "3":
            case "4":
            case "5":
            case "6":
            case "7":
            case "8":
            case "9":
                num = num + arrayNum[i];
                break;
            case decimalSeparator:
                num = num + ".";
                break;
        }
    }

    return num;
}

function gridGetAllFieldAndValue(fieldId, swCurrentField)
{
    var frm = G.getObject(getField(fieldId).form);

    var arrayAux = fieldId.split("][");
    var gridName = arrayAux[0];
    var row = parseInt(arrayAux[1]);
    var fieldName = arrayAux[2];

    var grid;
    var gridField = "";
    var fieldNameAux  = "";
    var fieldValueAux = "";
    var i1 = 0;
    var i2 = 0;

    //Get all fields of grid
    for (i1 = 0; i1 <= frm.aElements.length - 1; i1++) {
        if (frm.aElements[i1].name == gridName) {
            grid = frm.aElements[i1];

            for (i2 = 0; i2 <= grid.aFields.length - 1; i2++) {
                fieldNameAux  = grid.aFields[i2].sFieldName;
                fieldValueAux = grid.getElementByName(row, fieldNameAux).value();

                if ((swCurrentField == 1 || fieldNameAux != fieldName) && typeof fieldValueAux != "undefined") {
                    gridField = gridField + ((gridField != "")? "," : "") + "\"" + fieldNameAux + "\":\"" + fieldValueAux + "\"";
                }
            }
        }
    }

    return gridField;
}

function dropDownSetOption(elem, arrayOption)
{
    var selectdd = elem.element;
    var arraySelectddAttribute = document.getElementById("form[" + elem.name + "]").attributes;
    var optGroupAux;
    var optionAux;
    var swOptGroup = 0;
    var swOptGroupPrev = 0;
    var swAppend = 0;
    var i = 0;

    for (i = 0; i <= arraySelectddAttribute.length - 1; i++) {
        if (arraySelectddAttribute[i].name == "pm:optgroup") {
            swOptGroup = parseInt(arraySelectddAttribute[i].value);
        }
    }

    //selectdd.options.length = 0; //Delete options
    selectdd.innerHTML = "";

    for (i = 0; i <= arrayOption.options.length - 1; i++) {
        if (swOptGroup == 1 && /^optgroup\d+$/.test(arrayOption.options[i].key)) {
            optGroupAux = document.createElement("optgroup");

            //selectdd.appendChild(optGroupAux);

            optGroupAux.label = arrayOption.options[i].value;

            swOptGroupPrev = 1;
            swAppend = 1;
        } else {
            if (swOptGroupPrev == 1) {
                //Append optGroupAux
                if (swAppend == 1) {
                    selectdd.appendChild(optGroupAux);

                    swAppend = 0;
                }

                //Append optionAux
                optionAux = document.createElement("option");

                optGroupAux.appendChild(optionAux);

                optionAux.value = arrayOption.options[i].key;
                optionAux.text = arrayOption.options[i].value;
            } else {
                optionAux = document.createElement("option");

                selectdd.appendChild(optionAux);

                optionAux.value = arrayOption.options[i].key;
                optionAux.text = arrayOption.options[i].value;
            }
        }
    }

    if (selectdd.options.length == 0) {
        selectdd.options[0] = new Option("", "");
    }
}

