var G_Grid = function(oForm, sGridName){
  var oGrid = this;
  this.parent = G_Field;
  this.parent(oForm, '', sGridName);
  this.sGridName = sGridName;
  this.sAJAXPage = oForm.ajaxServer || '';
  this.oGrid = document.getElementById(this.sGridName);
  this.onaddrow = function(iRow){};
  this.ondeleterow = function(){};
  this.executeEvent = function (element,event) {
    if ( document.createEventObject ) {
      // IE
      var evt = document.createEventObject();
      return element.fireEvent('on'+event,evt)
    } else {
      // firefox + others
      var evt = document.createEvent("HTMLEvents");
      evt.initEvent(event, true, true ); // event type,bubbling,cancelable
      return !element.dispatchEvent(evt);
    }
  };

  this.aFields = [];
  this.aElements = [];
  this.aFunctions = [];
  this.aFormulas = [];

  this.allDependentFields = ''; //Stores all dependent fields

  this.countRows = function(){
    return this.aElements.length / this.aFields.length;
  };

  this.getObjectName = function(Name){
    var arr = Name.split('][');
    var aux = arr.pop();
    aux = aux.replace(']','');
    return aux;
  };

  //Begin SetFields ---------------------------------------------------------------------
  this.setFields = function (aFields, iRow)
  {
    var tableGrid = document.getElementById(this.sGridName);
    var elem;
    var elemName = "";
    var i = 0;
    var j = 0;

    this.aFields = aFields;

    for (i = iRow || 1; i <= tableGrid.rows.length - 2; i++) {
        for (j = 0; j <= this.aFields.length - 1; j++) {
            elemName = this.sGridName + "][" + i + "][" + this.aFields[j].sFieldName;

            if ((elem = document.getElementById("form[" + elemName + "]"))) {
                switch (this.aFields[j].sType) {
                    case "text":
                        this.aElements.push(new G_Text(oForm, elem, elemName));
                        this.aElements[this.aElements.length - 1].validate = this.aFields[j].oProperties.validate;

                        if (this.aFields[j].oProperties.strTo) {
                            this.aElements[this.aElements.length - 1].strTo = this.aFields[j].oProperties.strTo;
                        }

                        if (this.aFields[j].oProperties) {
                            this.aElements[this.aElements.length - 1].mask = this.aFields[j].oProperties.mask;
                        }
                        break;
                    case "currency":
                        this.aElements.push(new G_Currency(oForm, elem, elemName));

                        if (this.aFields[j].oProperties) {
                            if (this.aFields[j].oProperties.comma_separator) {
                                this.aElements[this.aElements.length - 1].comma_separator = this.aFields[j].oProperties.comma_separator;
                            }

                            this.aElements[this.aElements.length - 1].validate = this.aFields[j].oProperties.validate;
                            this.aElements[this.aElements.length - 1].mask     = this.aFields[j].oProperties.mask;
                        }
                        break;
                    case "percentage":
                        this.aElements.push(new G_Percentage(oForm, elem, elemName));

                        if (this.aFields[j].oProperties) {
                            if (this.aFields[j].oProperties.comma_separator) {
                                this.aElements[this.aElements.length - 1].comma_separator = this.aFields[j].oProperties.comma_separator;
                            }

                            this.aElements[this.aElements.length - 1].validate = this.aFields[j].oProperties.validate;
                            this.aElements[this.aElements.length - 1].mask     = this.aFields[j].oProperties.mask;
                        }
                        break;
                    case "dropdown":
                        this.aElements.push(new G_DropDown(oForm, elem, elemName));

                        if (this.aFields[j].oProperties) {
                            this.aElements[this.aElements.length - 1].mask = this.aFields[j].oProperties.sMask;
                        }
                        break;
                    default:
                        this.aElements.push(new G_Field(oForm, elem, elemName));

                        if (this.aFields[j].oProperties) {
                            this.aElements[this.aElements.length - 1].mask = this.aFields[j].oProperties.sMask;
                        }
                        break;
                }
            }
        }
    }

    //Set dependent fields
    var sw = false;

    if (this.allDependentFields == "") {
        sw = true; //Check if dependent fields are setted.
    }

    for (j = 0; j <= this.aFields.length - 1; j++) {
        i = iRow || 1;

        while ((elem = document.getElementById("form[" + this.sGridName + "][" + i + "][" + this.aFields[j].sFieldName + "]"))) {
            if (this.aFields[j].oProperties.dependentFields != "") {
                this.setDependents(i, this.getElementByName(i, this.aFields[j].sFieldName), aFields[j].oProperties.dependentFields, sw);
            }

            i = i + 1;
        }
    }
  };
  //End Set Fields --------------------------------------------------------

  ///////////////////////////////////////////////////////////////////////

  this.setDependents = function(iRow, me, theDependentFields, sw) {
    //alert('Row:' + iRow + ' me: ' + me.name + ' DP: ' + theDependentFields);
    var i;
    var dependentFields = theDependentFields || '';
    dependentFields = dependentFields.split(',');
    for (i = 0; i < dependentFields.length; i++) {
      var oField = this.getElementByName(iRow, dependentFields[i]);
      if (oField) {
        me.dependentFields[i] = oField;
        me.dependentFields[i].addDependencie(me);
        if (sw){ //Gets all dependent field only first time
          if (this.allDependentFields != '') this.allDependentFields += ',';
          this.allDependentFields += dependentFields[i];
        }
      }
    }
  };

  //////////////////////////////////////////////////////////////////////

  this.unsetFields = function() {
    var i, j = 0, k, l = 0;
    k = this.aElements.length / this.aFields.length;
    for (i = 0; i < this.aFields.length; i++) {

      j += k;
      l++;
      this.aElements.splice(j - l, 1);
    }
  };

  ////////////////////////////////////////////////////////////////////
  this.getElementByName = function(iRow, sName) {
    var i;
    for (i = 0; i < this.aElements.length; i++) {
      if (this.aElements[i].name === this.sGridName + '][' + iRow + '][' + sName) {
        return this.aElements[i];
      }
    }
    return null;
  };

  /////////////////////////////////////////////////////////////////////

  this.getElementValueByName = function(iRow, sName) {
    var oAux = document.getElementById('form[' + this.sGridName + '][' + iRow + '][' + sName + ']');
    if (oAux) {
      return oAux.value;
    } else {
      return 'Object not found!';
    }
  };

  ////////////////////////////////////////////////////////////////////////

  this.getFunctionResult = function(sName) {
    var oAux = document.getElementById('form[SYS_GRID_AGGREGATE_' + this.sGridName + '_' + sName + ']');
    if (oAux) {
      return oAux.value;
    } else {
      return 'Object not found!';
    }
  };

  this.cloneElement = function (elem)
  {
    //input, textarea, select

    var strHtml = elem.parentNode.innerHTML;
    var tag = new String(elem.nodeName);
    var arrayMatch = [];
    var arrayAux1  = [];
    var arrayAux2  = [];
    var strHtmlAux = "";
    var strAux     = "";
    var i = 0;

    strHtml = stringReplace("\\x0A", "", strHtml); //\n 10
    strHtml = stringReplace("\\x0D", "", strHtml); //\r 13
    strHtml = stringReplace("\\x09", "", strHtml); //\t  9

    if ((arrayMatch = eval("/^.*(<" + tag + ".*id=\"" + elem.id + "\".*>).*$/i").exec(strHtml))) {
        strHtml = arrayMatch[1];
    }

    strHtml = stringReplace("<" + tag, "", strHtml);
    strHtml = stringReplace("<" + tag.toLowerCase(), "", strHtml);
    strHtml = stringReplace("\\/>.*", "", strHtml);
    strHtml = stringReplace(">.*", "", strHtml);

    ///////
    strHtmlAux = strHtml;
    strAux = "";

    while ((arrayMatch = /^(.*)(".*")(.*)$/g.exec(strHtmlAux))) {
        strHtmlAux = arrayMatch[1];
        strAux = stringReplace(" ", "__SPACE__", arrayMatch[2]) + arrayMatch[3] + strAux;
    }

    strAux = strHtmlAux + strAux;

    strHtml = strAux;

    ///////
    if (/^.*read[oO]nly.*$/.test(strHtml)) {
        if (!(/^.*read[oO]nly\=.*$/.test(strHtml))) {
            strHtml = stringReplace("read[oO]nly", "readonly=\"\"", strHtml);
        }
    }

    if (/^.*disabled.*$/.test(strHtml)) {
        if (!(/^.*disabled\=.*$/.test(strHtml))) {
            strHtml = stringReplace("disabled", "disabled=\"\"", strHtml);
        }
    }

    if (/^.*checked.*$/i.test(strHtml)) {
        strHtml = stringReplace("CHECKED", "checked", strHtml);

        if (!(/^.*checked\=.*$/.test(strHtml))) {
            strHtml = stringReplace("checked", "checked=\"\"", strHtml);
        }
    }

    ///////
    var arrayAttribute = [];
    var a = "";
    var v = "";
    arrayAux1 = strHtml.split(" ");

    for (i = 0; i <= arrayAux1.length - 1; i++) {
        arrayAux2 = arrayAux1[i].split("=");

        if (typeof(arrayAux2[1]) != "undefined") {
            a = arrayAux2[0].trim();
            v = stringReplace("\\\"", "", arrayAux2[1]);

            v = stringReplace("__SPACE__", " ", v);

            arrayAttribute.push([a, v]);
        }
    }

    ///////
    var newElem = document.createElement(tag.toLowerCase());

    for (i = 0; i <= arrayAttribute.length - 1; i++) {
        a = arrayAttribute[i][0];
        v = arrayAttribute[i][1];

        switch (a.toLowerCase()) {
            case "id":
            case "name":
                newElem.setAttribute("id", elem.id);
                newElem.setAttribute("name", elem.id);
                break;
            case "class":
                newElem.className = v;
                break;
            case "style":
                newElem.style.cssText = ((/^.*display\s*:\s*none.*$/i.test(strHtml))? "display: none;" : "") + v;
                break;
            case "disabled":
                if (elem.disabled) {
                    newElem.disabled = true;
                }
                break;
            case "readonly":
                if (elem.readOnly) {
                    newElem.readOnly = true;
                }
                break;
            case "checked":
                if (elem.checked) {
                    newElem.checked = true;
                }
                break;
            default:
                newElem.setAttribute(a, v);
                break;
        }
    }

    switch (tag.toLowerCase()) {
        case "input":
        case "textarea":
            newElem.value = elem.value;
            break;
        case "select":
            if (elem.options.length > 0) {
                var pos = 0;

                for (i = 0; i <= elem.options.length - 1; i++) {
                    if (elem.options[i].selected) {
                        pos = i;
                    }

                    newElem.options[i] = new Option(elem.options[i].text, elem.options[i].value, elem.options[i].defaultSelected);
                }

                newElem.options[pos].selected = true;
            }
            break;
    }

    return newElem;
  };

  this.replaceHtml = function (el, html) {
    var oldEl = typeof el === "string" ? document.getElementById(el) : el;
    /*Pure innerHTML is slightly faster in IE
        oldEl.innerHTML = html;
        return oldEl;
    */
    if ( this.determineBrowser() == "MSIE" ) {
        oldEl.innerHTML = html;
        return oldEl;
    } else {
        var newEl = oldEl.cloneNode(false);
        newEl.innerHTML = html;
        oldEl.parentNode.replaceChild(newEl, oldEl);
        /* Since we just removed the old element from the DOM, return a reference
        to the new element, which can be used to restore variable references. */
        return newEl;
    }
  };

  this.addGridRow = function() {
    this.oGrid = document.getElementById(this.sGridName);
    var i, aObjects;
    var defaultValue = '';
    var n,a,x;
    var oRow = document.getElementById('firstRow_' + this.sGridName);
    var aCells = oRow.getElementsByTagName('td');
    var oNewRow = this.oGrid.insertRow(this.oGrid.rows.length - 1);
    var currentRow = this.oGrid.rows.length - 2;
    var newID, attributes, img2, gridType;

    oNewRow.onmouseover=function(){
      highlightRow(this, '#D9E8FF');
    };
    oNewRow.onmouseout=function(){
      highlightRow(this, '#fff');
    };

    // Clone Cells Loop
    for (i = 0; i < aCells.length; i++) {
        oNewRow.appendChild(aCells[i].cloneNode(true)); //Clone First Cell exactly.
      switch (i){
        case 0:
          oNewRow.getElementsByTagName('td')[i].innerHTML = currentRow;
          break;
        case aCells.length - 1:
          oNewRow.getElementsByTagName('td')[i].innerHTML = oNewRow.getElementsByTagName('td')[i].innerHTML.replace(/\[1\]/g, '\[' + currentRow + '\]');
          break;
        default:
          var eNodeName = aCells[i].innerHTML.substring(aCells[i].innerHTML.indexOf('<')+1, aCells[i].innerHTML.indexOf(' '));
          eNodeName = eNodeName.toLowerCase();
        switch(eNodeName){
          case 'input':
            aObjects = oNewRow.getElementsByTagName('td')[i].getElementsByTagName('input');
            if (aObjects){
              newID = aObjects[0].id.replace(/\[1\]/g, '\[' + currentRow + '\]');
              aObjects[0].id = newID;
              aObjects[0].name = newID;
              attributes = elementAttributesNS(aObjects[0], 'pm');

              if (typeof(attributes.defaultvalue) != "undefined" && attributes.defaultvalue != "") {
                  defaultValue = attributes.defaultvalue;
              } else {
                  defaultValue = "";
              }

              for(n=0; n < aObjects.length; n++){
                switch(aObjects[n].type){
                  case 'text': //TEXTBOX, CURRENCY, PERCENTAGE, DATEPICKER
                    aObjects[n].className = "module_app_input___gray";

                    tags = oNewRow.getElementsByTagName('td')[i].getElementsByTagName('a');
                    if (tags.length == 2){ //DATEPICKER
                      //Copy Images
                      //img1 = tags[0].innerHTML;
                      img2 = tags[1].innerHTML;
                      //Create new trigger name
                      var datePickerTriggerId = tags[1].id.replace(/\[1\]/g, '\[' + currentRow + '\]');
                      //Remove 'a' tag for date picker trigger
                      oNewRow.getElementsByTagName('td')[i].removeChild(tags[1]);
                      //Capture Script and remove
                      var scriptTags = oNewRow.getElementsByTagName('td')[i].getElementsByTagName('script');
                      oNewRow.getElementsByTagName('td')[i].removeChild(scriptTags[0]);
                      //Create 'a' to remove Date
                      if (tags[0].onclick){
                        var onclickevn = new String(tags[0].onclick);
                        eval('tags[0].onclick = ' + onclickevn.replace(/\[1\]/g, '\[' + currentRow + '\]') + ';');
                      }
                      //Create new 'a' to trigger DatePicker
                      var a2 = document.createElement('a');

                        if( a2.style.setAttribute ) {
                          var styleText = "position:relative;top:0px;left:-19px;";
                          a2.style.setAttribute("cssText", styleText );
                        }
                        else {
                          var styleText = "position:relative;top:0px;left:-22px;";
                          a2.setAttribute("style", styleText );
                        }

                      a2.id = datePickerTriggerId;
                      a2.innerHTML = img2;
                      oNewRow.getElementsByTagName('td')[i].appendChild(a2);

                      //Load DatePicker Trigger
                      datePicker4("", newID, attributes.mask, attributes.start, attributes.end, attributes.time);

                      aObjects[n].value = defaultValue;
                    }else{
                      if (_BROWSER.name == 'msie' && aObjects.length==1){ //Clone new input element if browser is IE
                        var oNewOBJ = this.cloneElement(aObjects[n]);
                        oNewOBJ.value = defaultValue;
                        var parentGG = aObjects[n].parentNode;
                        parentGG.removeChild(aObjects[n]);
                        parentGG.appendChild(oNewOBJ);
                      }else{
                        if ((attributes.gridtype) && attributes.gridtype == "currency") {
                            var attributesCurrency = elementAttributesNS(aObjects[n], "");
                            aObjects[n].value = attributesCurrency.value.replace(/[.,0-9\s]/g, "");
                        } else {
                            aObjects[n].value = defaultValue;
                        }
                      }
                    }

                    var aObjectsScript = oNewRow.getElementsByTagName('td')[i].getElementsByTagName('script');

                    var sObjectType = this.aFields[i-1].sType;

                    if (aObjectsScript[0] != 'undefined' && sObjectType == 'suggest') {
                        if (this.determineBrowser() == "MSIE") {

                            var firstNode = aCells[i];

                            var firstScriptSuggest = firstNode.childNodes[2].innerHTML ;
                            var sScriptAdjustRow = firstScriptSuggest.replace(/\[1\]/g, '\[' + currentRow + '\]');

                            var elementTD = oNewRow.getElementsByTagName('td')[i];

                            var elementLabel = elementTD.childNodes[0];
                            var sNewLabelRow = elementLabel.getAttribute("id").replace(/\[1\]/g, '\[' + currentRow + '\]');

                            var elementHidden = elementTD.childNodes[1];

                            var elementScript = elementTD.childNodes[2];
                            var parentScript = elementScript.parentNode;
                            var scriptElement = document.createElement("script");
                            scriptElement.text = sScriptAdjustRow;
                            parentScript.removeChild(elementScript);
                            parentScript.appendChild(scriptElement);
                        } else {
                            var sObjScript = aObjectsScript[0].innerHTML;
                            var sNewObjScript = sObjScript.replace(/\[1\]/g, "\[" + currentRow + "\]");
                            aObjectsScript[0].innerHTML = sNewObjScript;
                            eval(aObjectsScript[0].innerHTML);
                        }
                    }
                    break;
                  case 'checkbox': //CHECKBOX
                      var attributeCheckBox = elementAttributesNS(aObjects[n], "");

                      if (defaultValue == "" || (typeof(attributeCheckBox.falseValue) != "undefined" && defaultValue == attributeCheckBox.falseValue) || (typeof(attributeCheckBox.falsevalue) != "undefined" && defaultValue == attributeCheckBox.falsevalue)) {
                          aObjects[n].checked = false;
                      } else {
                          aObjects[n].checked = true;
                      }
                      break;
                  case 'hidden': //HIDDEN
                    if ((attributes.gridtype != "yesno" && attributes.gridtype != "dropdown") || typeof(attributes.gridtype) == "undefined") {
                        aObjects[n].value = defaultValue;
                        newID = aObjects[n].id.replace(/\[1\]/g, '\[' + currentRow + '\]');
                        aObjects[n].id = newID;
                        aObjects[n].name = newID;
                    }
                    break;
                  case 'button':
                    if (aObjects[n].onclick){
                        var onclickevn = new String(aObjects[n].onclick);
                        eval('aObjects[n].onclick = ' + onclickevn.replace(/\[1\]/g, '\[' + currentRow + '\]') + ';');
                    }
                    break;
                   case "file":
                        aObjects[n].value = "";
                    break;
                }
              }
            }
            aObjects = null;
            break;
          case 'textarea': //TEXTAREA
            aObjects = oNewRow.getElementsByTagName('td')[i].getElementsByTagName('textarea');
            if (aObjects){
              aObjects[0].className = "module_app_input___gray";

              newID = aObjects[0].id.replace(/\[1\]/g, '\[' + currentRow + '\]');
              aObjects[0].id = newID;
              aObjects[0].name = newID;
              attributes = elementAttributesNS(aObjects[0], 'pm');
              if (attributes.defaultvalue != '' && typeof attributes.defaultvalue != 'undefined'){
                defaultValue = attributes.defaultvalue;
              }else{
                defaultValue = '';
              }
              aObjects[0].innerHTML = defaultValue;
            }
            aObjects = null;
            break;
          case 'select': //DROPDOWN
            var oNewSelect;
            aObjects = oNewRow.getElementsByTagName('td')[i].getElementsByTagName('select');
            if (aObjects){
              newID = aObjects[0].id.replace(/\[1\]/g, '\[' + currentRow + '\]');
              aObjects[0].id = newID;
              aObjects[0].name = newID;

              oNewSelect = document.createElement(aObjects[0].tagName);
              oNewSelect.id = newID;
              oNewSelect.name = newID;
              oNewSelect.setAttribute('class','module_app_input___gray');

              aAttributes = aObjects[0].attributes;
              for (a=0; a < aAttributes.length; a++){
                if (aAttributes[a].name.indexOf('pm:') != -1){
                  oNewSelect.setAttribute(aAttributes[a].name,aAttributes[a].value);
                }
                if (aAttributes[a].name == 'disabled'){
                  if (_BROWSER.name == 'msie'){
                	if (aAttributes[a].value=='true'){
                	  oNewSelect.setAttribute(aAttributes[a].name,aAttributes[a].value);
                	}
                  }
                  else{
                    oNewSelect.setAttribute(aAttributes[a].name,aAttributes[a].value);
                  }
                }
              }

              attributes = elementAttributesNS(aObjects[0], 'pm');
              //var MyAtt = attributes;
              if (attributes.defaultvalue != '' && typeof attributes.defaultvalue != 'undefined'){
                defaultValue = attributes.defaultvalue;
                //Set '' for Default Value when dropdown has dependent fields.
                //if (attributes.dependent == '1') defaultValue = '';
              }else{
                defaultValue = '';
              }
              if (attributes.gridtype != '' && typeof attributes.gridtype != 'undefined'){
                gridType = attributes.gridtype;
              }else{
                gridType = '';
              }
              var aDependents = this.allDependentFields.split(',');
              sObject = this.getObjectName(newID);

              //Check if dropdow is dependent
              var sw = false;
              for (x=0; x < aDependents.length; x++){
                if (aDependents[x] == sObject) sw = true;
              }

              //Delete Options if dropdow is dependent
              //only remains empty value
              if (sw) {
                /*
                oNewSelect.options.length = 0; //Delete options

                var oAux = document.createElement(aObjects[0].tagName);

                for ( var j = 0; j < aObjects[0].options.length; j++) {
                  if (aObjects[0].options[j].value == ''){
                    var oOption = document.createElement('OPTION');
                    oOption.value = aObjects[0].options[j].value;
                    oOption.text = aObjects[0].options[j].text;
                    oAux.options.add(oOption);
                  }
                }

                //aObjects[0].innerHTML = ''; //Delete options

                for (var r =0; r < oAux.options.length; r++){
                  var xOption = document.createElement('OPTION');
                  xOption.value = oAux.options[r].value;
                  xOption.text = oAux.options[r].text;
                  //aObjects[0].options.add(xOption);
                  oNewSelect.options.add(xOption);
                }
                */
                  //oNewSelect.options.length = 0; //Delete options
                  oNewSelect.innerHTML = "";

                  if (oNewSelect.options.length == 0) {
                      oNewSelect.options[0] = new Option("", "");
                  }
              } else {
                  //Set Default Value if it's not a Dependent Field
                  selectCloneOption.call(oNewSelect, aObjects[0], defaultValue);

                  //TODO: Implement Default Value and Dependent Fields Trigger for grid dropdowns
              }

              var parentSelect = aObjects[0].parentNode;
              parentSelect.removeChild(aObjects[0]);
              parentSelect.appendChild(oNewSelect);
            }
            aObjects = oNewRow.getElementsByTagName('td')[i].getElementsByTagName('input');
            if (aObjects.length > 0){
              newID = aObjects[0].id.replace(/\[1\]/g, '\[' + currentRow + '\]');
              aObjects[0].id = newID;
              aObjects[0].name = newID;
            }
            aObjects = null;
            break;
          case 'a': //LINKS
            aObjects = oNewRow.getElementsByTagName('td')[i].getElementsByTagName('a');
            if (aObjects){
              newID = aObjects[0].id.replace(/\[1\]/g, '\[' + currentRow + '\]');
              aObjects[0].id = newID;
              aObjects[0].name = newID;
            }
            aObjects = null;
            break;
        }
        break;
      }
    }

    if (this.aFields.length > 0) {
      this.setFields(this.aFields, currentRow);
    }
    if (this.aFunctions.length > 0) {
      this.assignFunctions(this.aFunctions, 'change', currentRow);
    }

    if (this.aFormulas.length > 0) {
      this.assignFormulas(this.aFormulas, 'change', currentRow);
    }

    //Recalculate functions if are declared
    var oAux;
    if (this.aFunctions.length > 0) {
      for (i = 0; i < this.aFunctions.length; i++) {
        oAux = document.getElementById('form[' + this.sGridName + '][' + currentRow + '][' + this.aFunctions[i].sFieldName + ']');
        if (oAux) {
          switch (this.aFunctions[i].sFunction) {
            case 'sum':
              this.sum(false, oAux);
              break;
            case 'avg':
              this.avg(false, oAux);
              break;
          }
        }
      }
    }
    //Fire Update Dependent Fields for any item with dependentfields and not included in dependencie
    var xIsDependentOf = [];
    var exist = false;
    var m;
    for (i=0; i < this.aFields.length; i++){
      var oAux = this.getElementByName(currentRow, this.aFields[i].sFieldName);
      if (typeof oAux !== 'undefined' && oAux != null)
        if (typeof oAux.dependentFields !== 'undefined'){
          if (oAux.dependentFields.length > 0){
            exist = false;
            for (m=0; m < xIsDependentOf.length; m++)
              if (xIsDependentOf[m] == oAux.name) exist = true;
            for (j=0; j < oAux.dependentFields.length; j++){
              xIsDependentOf.push(oAux.dependentFields[j].name);
            }
            if (!exist){
              oAux.updateDepententFields();
            }
          }
        }
    }

    //Set focus the first element in the grid
    for (var i = 0; i < this.aFields.length; i++) {
      var fieldName = 'form[' + sGridName + '][' + currentRow + '][' + this.aFields[i].sFieldName + ']';
      if (this.aFields[i].sType == 'suggest' ) {
          fieldName = 'form[' + sGridName + '][' + currentRow + '][' + this.aFields[i].sFieldName + '_label]';
      }
      if (this.aFields[i].sType != 'file' && this.aFields[i].sType != 'hidden' && document.getElementById(fieldName).focus) {
        document.getElementById(fieldName).focus();
        break;
      }
    }

    //Fires OnAddRow Event
    if (this.onaddrow) {
      this.onaddrow(currentRow);
    }
  };

  this.deleteGridRow = function (sRow, bWithoutConfirm)
  {
      if (typeof(bWithoutConfirm) == "undefined") {
          bWithoutConfirm = false;
      }

      if (this.oGrid.rows.length == 3) {
          new leimnud.module.app.alert().make({
              label: G_STRINGS.ID_MSG_NODELETE_GRID_ITEM
          });

          return false;
      }

      if (bWithoutConfirm) {
          this.deleteRowWC(this, sRow);
      } else {
          new leimnud.module.app.confirm().make({
              label: G_STRINGS.ID_MSG_DELETE_GRID_ITEM,
              action: function ()
                {
                    this.deleteRowWC(this, sRow);
                }.extend(this)
          });
      }
  };

  this.deleteRowWC = function (oObj, aRow)
  {
    var sRow = new String(aRow);
    sRow = sRow.replace("[", "");
    sRow = sRow.replace("]", "");
    var iRow = Number(sRow);
    var iRowAux = iRow + 1;
    var lastItem = oObj.oGrid.rows.length - 2;
    var elemNodeName = "";
    var elem2ParentNode;
    var elem2Id   = "";
    var elem2Name = "";
    var elemAux;

    deleteRowOnDynaform(oObj, iRow);

    var i = 0;

    while (iRowAux <= (lastItem)) {
      for (i = 1; i < oObj.oGrid.rows[iRowAux - 1].cells.length; i++) {
        var oCell1 = oObj.oGrid.rows[iRowAux - 1].cells[i];
        var oCell2 = oObj.oGrid.rows[iRowAux].cells[i];

        elemNodeName = oCell1.innerHTML.substring(oCell1.innerHTML.indexOf("<") + 1, oCell1.innerHTML.indexOf(" ")).toLowerCase();

        switch (elemNodeName) {
          case "input":
            aObjects1 = oCell1.getElementsByTagName('input');
            aObjects2 = oCell2.getElementsByTagName('input');

            if (aObjects1 && aObjects2) {
                switch (aObjects2[0].type) {
                    case "file":
                        elem2ParentNode = aObjects2[0].parentNode;
                        elem2Id   = aObjects2[0].id;
                        elem2Name = aObjects2[0].name;
                        aObjects2[0].id = aObjects1[0].id;
                        aObjects2[0].name = aObjects1[0].name;
                        aObjects1[0].parentNode.replaceChild(aObjects2[0], aObjects1[0]);
                        elemAux = document.createElement("input");
                        elemAux.type = "file";
                        elemAux.setAttribute("id", elem2Id);
                        elemAux.setAttribute("name", elem2Name);
                        elem2ParentNode.insertBefore(elemAux, elem2ParentNode.firstChild);
                        break;
                    default:
                        if (aObjects2[0].type == "checkbox") {
                            aObjects1[0].checked = aObjects2[0].checked;
                        }
                        aObjects1[0].value = aObjects2[0].value;
                        aObjects1[0].className = aObjects2[0].className;
                        if ( typeof(aObjects1[1]) != 'undefined' && typeof(aObjects2[1]) != 'undefined' ) {
                            aObjects1[1].value = aObjects2[1].value;
                        }
                        break;
                }
            }

            aObjects = oCell1.getElementsByTagName('div');

            if (aObjects.length > 0) {

              if (aObjects[0]) {
                aObjects[0].id = aObjects[0].id.replace('/\['+ (iRowAux -1 ) + '\]/g', '\[' + iRowAux + '\]');
                aObjects[0].name = aObjects[0].id.replace('/\['+ (iRowAux -1 ) + '\]/g', '\[' + iRowAux + '\]');
                if (aObjects[0].onclick) {
                  sAux = new String(aObjects[0].onclick);
                  eval('aObjects[0].onclick = ' + sAux.replace('/\['+ (iRowAux -1 ) + '\]/g', '\[' + iRowAux + '\]') + ';');
                }
              }
              aObjects = oCell1.getElementsByTagName('a');
              if (aObjects) {
                if (aObjects[0]) {
                  if (aObjects[0].onclick) {
                    sAux = new String(aObjects[0].onclick);
                    eval('aObjects[0].onclick = ' + sAux.replace('/\['+ (iRowAux -1 ) + '\]/g', '\[' + iRowAux + '\]') + ';');
                  }
                }
              }
            }

            break;
          case "select":
              aObjects1 = oCell1.getElementsByTagName("select");
              aObjects2 = oCell2.getElementsByTagName("select");

              if (aObjects1 && aObjects2) {
                  selectCloneOption.call(aObjects1[0], aObjects2[0], "");

                  aObjects1[0].value = aObjects2[0].value;
                  aObjects1[0].className = aObjects2[0].className;
              }
              break;
          case "textarea":
            aObjects1 = oCell1.getElementsByTagName('textarea');
            aObjects2 = oCell2.getElementsByTagName('textarea');
            if (aObjects1 && aObjects2) {
              aObjects1[0].value = aObjects2[0].value;
              aObjects1[0].className = aObjects2[0].className;
            }
            break;
          case "a":
              aObjects1 = oCell1.getElementsByTagName("a");
              aObjects2 = oCell2.getElementsByTagName("a");

              if (aObjects1 && aObjects2) {
                  if (oCell1.innerHTML.indexOf("deleteGridRow") == -1) {
                      var iAux = 0;
                      var swLink = 0;

                      for (iAux = 0; iAux <= aObjects1[0].attributes.length - 1; iAux++) {
                          if (aObjects1[0].attributes[iAux].name == "pm:field" && aObjects1[0].attributes[iAux].value == "pm:field") {
                              swLink = 1;
                              break;
                          }
                      }

                      if (swLink == 1) {
                          aObjects1[0].href = aObjects2[0].href;
                          aObjects1[0].innerHTML = aObjects2[0].innerHTML;
                      } else {
                          oCell1.innerHTML = oCell2.innerHTML;
                      }
                  }
              }
              break;
          default:
            if (( oCell2.innerHTML.indexOf('changeValues') == 111 || oCell2.innerHTML.indexOf('changeValues') == 115 ) ) {
              break;
            }
          break;
        }
      }
      iRowAux++;
    }

    //Delete row
    this.oGrid.deleteRow(lastItem);

    for (i = 0; i <= this.aFields.length - 1; i++) {
        this.aElements.pop();
    }

    //Recalculate functions if are declared
    var elem;

    if (oObj.aFunctions.length > 0) {
        for (i = 0; i <= oObj.aFunctions.length - 1; i++) {
            elem = document.getElementById("form[" + oObj.sGridName + "][1][" + oObj.aFunctions[i].sFieldName + "]");

            if (elem) {
                switch (oObj.aFunctions[i].sFunction) {
                    case "sum":
                        oObj.sum(false, elem);
                        break;
                    case "avg":
                        oObj.avg(false, elem);
                        break;
                }
            }
        }
    }

    //Fires OnAddRow Event
    if (oObj.ondeleterow) {
        oObj.ondeleterow(iRow);
    }
  };

  ///////////////////////////////////////////////////////////////////////////////////

  this.assignFunctions = function (aFields, sEvent, iRow)
  {
      var elem;
      var i = 0;
      var j = 0;

      for (j = 0; j <= aFields.length - 1; j++) {
          i = iRow || 1;

          while ((elem = document.getElementById("form[" + this.sGridName + "][" + i + "][" + aFields[j].sFieldName + "]"))) {
              switch (aFields[j].sFunction) {
                  case "sum":
                      leimnud.event.add(elem, sEvent, {
                          method: this.sum,
                          instance: this,
                          event: true
                      });
                      break;
                  case "avg":
                      leimnud.event.add(elem, sEvent, {
                          method: this.avg,
                          instance: this,
                          event: true
                      });
                      break;
                  default:
                      leimnud.event.add(elem, sEvent, {
                          method: aFields[j].sFunction,
                          instance: this,
                          event: true
                      });
                      break;
              }

              i = i + 1;
          }
      }
  };

  ////////////////////////////////////////////////////////////////////////////////

  this.setFunctions = function(aFunctions) {
    this.aFunctions = aFunctions;
    this.assignFunctions(this.aFunctions, 'change');
  };

  this.determineBrowser = function()
  {
    var nAgt = navigator.userAgent;
    var browserName = "";
    // In Opera, the true version is after "Opera" or after "Version"
    if ( nAgt.indexOf("Opera") != -1) {
      browserName = "Opera";
    } else {
      // In MSIE, the true version is after "MSIE" in userAgent - Microsoft Internet Explorer
      if ( nAgt.indexOf("MSIE") != -1) {
        browserName = "MSIE";
      } else {
        // In Chrome, the true version is after "Chrome"
        if ( nAgt.indexOf("Chrome") != -1) {
          browserName = "Chrome";
        } else {
          // In Safari, the true version is after "Safari" or after "Version"
          if ( nAgt.indexOf("Safari") != -1) {
            browserName = "Safari";
          } else {
            // In Firefox, the true version is after "Firefox"
            if ( nAgt.indexOf("Firefox") != -1) {
             browserName = "Firefox";
            }
          }
        }
      }
    }
    return browserName;
  };
  /////////////////////////////////////////////////////////////////////////////////

  this.sum = function(oEvent, oDOM) {
    oDOM = (oDOM ? oDOM : oEvent.target || window.event.srcElement);
    var i, aAux, oAux, fTotal, sMask, nnName;
    aAux = oDOM.name.split('][');
    i = 1;
    fTotal = 0;
    aAux[2] = aAux[2].replace(']', '');

    var j=1;
    for ( var k = 0; k < this.aElements.length; k++) {
      nnName= this.aElements[k].name.split('][');
      if (aAux[2] == nnName[2] && j <= (this.oGrid.rows.length-2)){
        oAux=this.getElementByName(j, nnName[2]);
        var oAux2 = oAux.value().replace(/[$|a-zA-Z\s]/g,'');

        if ((oAux != null) && (oAux.value().trim() != "") && (oAux2)) {
            fTotal = fTotal + parseFloat(G.getValue(oAux));
        }

        j = j + 1;
      }
    }
    /*
    while (oAux = this.getElementByName(i, aAux[2])) {
      fTotal += parseFloat(G.cleanMask(oAux.value() || 0, oAux.mask).result.replace(/,/g, ''));
      sMask = oAux.mask;
      i++;
    }*/
    fTotal = fTotal.toFixed(2);
    oAux = document.getElementById('form[SYS_GRID_AGGREGATE_' + oGrid.sGridName + '_' + aAux[2] + ']');
    oAux.value = fTotal;
    oAux = document.getElementById('form[SYS_GRID_AGGREGATE_' + oGrid.sGridName + '__' + aAux[2] + ']');
    // oAux.innerHTML = G.toMask(fTotal, sMask).result;
    if ( this.determineBrowser() == "MSIE" ) {
      oAux.innerText = fTotal;
    } else {
      oAux.innerHTML = fTotal;
    }
  };

  ////////////////////////////////////////////////////////////////////////////////////
  this.avg = function(oEvent, oDOM) {
    oDOM = (oDOM ? oDOM : oEvent.target || window.event.srcElement);
    var i, aAux, oAux, fTotal, sMask;
    aAux = oDOM.name.split('][');
    i = 1;
    fTotal = 0;
    aAux[2] = aAux[2].replace(']', '');

    while ((oAux = this.getElementByName(i, aAux[2]))) {
      if (oAux.value().trim() != "") {
          fTotal = fTotal + parseFloat(G.getValue(oAux));
      }

      sMask = oAux.mask;
      i = i + 1;
    }

    i--;
    if (fTotal > 0) {
      fTotal = (fTotal / i).toFixed(2);
      oAux = document.getElementById('form[SYS_GRID_AGGREGATE_' + oGrid.sGridName + '_' + aAux[2] + ']');
      oAux.value = fTotal;
      oAux = document.getElementById('form[SYS_GRID_AGGREGATE_' + oGrid.sGridName + '__' + aAux[2] + ']');
      // oAux.innerHTML = G.toMask((fTotal / i), sMask).result;
    if ( this.determineBrowser() == "MSIE" ) {
        oAux.innerText = fTotal;
      } else {
        oAux.innerHTML = fTotal;
      }
    } else {
      oAux = document.getElementById('form[SYS_GRID_AGGREGATE_' + oGrid.sGridName + '_' + aAux[2] + ']');
      oAux.value = 0;
      oAux = document.getElementById('form[SYS_GRID_AGGREGATE_' + oGrid.sGridName + '__' + aAux[2] + ']');
      // oAux.innerHTML = G.toMask(0, sMask).result;
    if ( this.determineBrowser() == "MSIE" ) {
        oAux.innerText = 0;
      } else {
        oAux.innerHTML = 0;
      }
    }
  };

  /////////////////////////////////////////////////////////////////////////////////////////

  this.assignFormulas = function (aFields, sEvent, iRow)
  {
      var elem;
      var i = 0
      var j = 0;

      for (j = 0; j <= aFields.length - 1; j++) {
          i = iRow || 1;

          while ((elem = document.getElementById("form[" + this.sGridName + "][" + i + "][" + aFields[j].sDependentOf + "]"))) {
              leimnud.event.add(elem, sEvent, {
                  method: this.evaluateFormula,
                  instance: this,
                  args: [elem, aFields[j]],
                  event: true
              });

              i = i + 1;
          }
      }
  };

  ////////////////////////////////////////////////////////////////////////////////////////////
  this.setFormulas = function(aFormulas) {
    this.aFormulas = aFormulas;
    this.assignFormulas(this.aFormulas, 'change');
  };

  /////////////////////////////////////////////////////////////////////////////////////////////
  this.evaluateFormula = function(oEvent, oDOM, oField) {
    oDOM = (oDOM ? oDOM : oEvent.target || window.event.srcElement);
    var aAux, sAux, i, oAux;
    var domId = oDOM.id;
    var oContinue = true;
    aAux = oDOM.name.split('][');
    sAux = oField.sFormula.replace(/\+|\-|\*|\/|\(|\)|\[|\]|\{|\}|\%|\$/g, ' ');
    sAux = sAux.replace(/^\s+|\s+$/g, '');
    sAux = sAux.replace(/      /g, ' ');
    sAux = sAux.replace(/     /g, ' ');
    sAux = sAux.replace(/    /g, ' ');
    sAux = sAux.replace(/   /g, ' ');
    sAux = sAux.replace(/  /g, ' ');
    aFields = sAux.split(' ');
    aFields = aFields.unique();
    sAux = oField.sFormula;
    for (i = 0; i < aFields.length; i++) {
    	if (!isNumber(aFields[i])) {
        oAux = this.getElementByName(aAux[1], aFields[i]);
        sAux = sAux.replace(new RegExp(aFields[i], "g"), "parseFloat(G.cleanMask(this.getElementByName(" + aAux[1] + ", '" + aFields[i] + "').value().replace(/[$|a-zA-Z\s]/g,'') || 0, '" + (oAux.sMask ? oAux.sMask : '')
        + "').result.replace(/,/g, ''))");

        eval("if (!document.getElementById('" + aAux[0] + '][' + aAux[1] + '][' + aFields[i] + "]')) { oContinue = false; }");
      }
    }
    eval("if (!document.getElementById('" + aAux[0] + '][' + aAux[1] + '][' + oField.sFieldName + "]')) { oContinue = false; }");

    var swReal=0;

    if (oContinue) {
      //we're selecting the mask to put in the field with the formula
      for (i = 0; i < this.aFields.length; i++) {
        if(oField.sFieldName==this.aFields[i].sFieldName) {
        	maskformula=this.aFields[i].oProperties.mask;

        	if(this.aFields[i].oProperties.validate=='Real')
        		swReal=1;
        }
      }

      if(maskformula!=''){
        maskDecimal=maskformula.split(";");
        if(maskDecimal.length > 1) {
          maskDecimal=maskDecimal[1].split(".");
        } else {
          maskDecimal=maskformula.split(".");
        }

        if(typeof maskDecimal[1] != 'undefined') {
          maskToPut=maskDecimal[1].length;
        } else {
          maskToPut=0;
        }
      } else {
    	  if(swReal==1)
    		  maskToPut=2;
    	  else
    		  maskToPut=0;
      }

      // clean the field and load mask execute event keypress
      document.getElementById(aAux[0]+']['+ aAux[1] + '][' + oField.sFieldName + ']').value = '';
      this.executeEvent(document.getElementById(aAux[0]+']['+ aAux[1] + '][' + oField.sFieldName + ']'), 'keypress');

      // execute formula and set decimal
      eval("document.getElementById('" + aAux[0] + '][' + aAux[1] + '][' + oField.sFieldName + "]').value = (" + sAux + ').toFixed('+maskToPut+');');

      // trim value
      document.getElementById(aAux[0] + '][' + aAux[1] + '][' + oField.sFieldName + ']').value = document.getElementById(aAux[0] + '][' + aAux[1] + '][' + oField.sFieldName + ']').value.replace(/^\s*|\s*$/g,"");

      // set '' to field if response is NaN
      if (document.getElementById(aAux[0] + '][' + aAux[1] + '][' + oField.sFieldName + ']').value =='NaN')
        document.getElementById(aAux[0] + '][' + aAux[1] + '][' + oField.sFieldName + ']').value = '';

      // save var symbol the response
      var symbol = document.getElementById(aAux[0]+']['+ aAux[1] + '][' + oField.sFieldName + ']').value.replace(/[0-9.\s]/g,'');
      this.executeEvent(document.getElementById(aAux[0]+']['+ aAux[1] + '][' + oField.sFieldName + ']'), 'keypress');

      // replace symbol - for ''
      document.getElementById(aAux[0]+']['+ aAux[1] + '][' + oField.sFieldName + ']').value = document.getElementById(aAux[0]+']['+ aAux[1] + '][' + oField.sFieldName + ']').value.replace('-','');

      // set var symbol
      document.getElementById(aAux[0]+']['+ aAux[1] + '][' + oField.sFieldName + ']').value = symbol+''+document.getElementById(aAux[0]+']['+ aAux[1] + '][' + oField.sFieldName + ']').value;

      // return focus the field typed
      if (typeof document.getElementById(domId) != 'undefined') {
        document.getElementById(domId).focus();
      }

      if (this.aFunctions.length > 0) {
        for (i = 0; i < this.aFunctions.length; i++) {
          oAux = document.getElementById('form[' + this.sGridName + '][' + aAux[1] + '][' + this.aFunctions[i].sFieldName + ']');
          if (oAux) {
            if (oAux.name == aAux[0] + '][' + aAux[1] + '][' + oField.sFieldName + ']') {
              switch (this.aFunctions[i].sFunction) {
                case 'sum':
                  this.sum(false, oAux);
                  break;
                case 'avg':
                  this.avg(false, oAux);
                  break;
              }
              if (oAux.fireEvent) {
                oAux.fireEvent('onchange');
              } else {
                var evObj = document.createEvent('HTMLEvents');
                evObj.initEvent('change', true, true);
                oAux.dispatchEvent(evObj);
              }
            }
          }
        }
      }
    } else {
      new leimnud.module.app.alert().make( {
        label : "Check your formula!\n\n" + oField.sFormula
      });
    }
  };

  /*add*/
  this.deleteGridRownomsg = function(sRow) {
    var i, iRow, iRowAux, oAux, ooAux;


    //action : function() {
    //this.aElements = [];
    sRow = sRow.replace('[', '');
    sRow = sRow.replace(']', '');
    iRow = Number(sRow);

    /*
     * delete the respective session row grid variables from
     * Dynaform - by Nyeke <erik@colosa.com
     */

    deleteRowOnDynaform(this, iRow);

    iRowAux = iRow + 1;
    while (iRowAux <= (this.oGrid.rows.length - 2)) {
      for (i = 1; i < this.oGrid.rows[iRowAux - 1].cells.length; i++) {
        var oCell1 = this.oGrid.rows[iRowAux - 1].cells[i];
        var oCell2 = this.oGrid.rows[iRowAux].cells[i];
        switch (oCell1.innerHTML.replace(/^\s+|\s+$/g, '').substr(0, 6).toLowerCase()) {
          case '<input':
            aObjects1 = oCell1.getElementsByTagName('input');
            aObjects2 = oCell2.getElementsByTagName('input');
            if (aObjects1 && aObjects2) {
              if(aObjects1[0].type=='checkbox'){
                aObjects1[0].checked = aObjects2[0].checked;
              }
              aObjects1[0].value = aObjects2[0].value;
              //  if(oCell1.innerHTML.indexOf('<div id=')!=-1)
              //  oCell1.innerHTML = oCell2.innerHTML;
            }

            aObjects = oCell1.getElementsByTagName('div');

            if (aObjects.length > 0) {

              if (aObjects[0]) {
                aObjects[0].id = aObjects[0].id.replace(/\[1\]/g, '\[' + (this.oGrid.rows.length - 2) + '\]');
                aObjects[0].name = aObjects[0].id.replace(/\[1\]/g, '\[' + (this.oGrid.rows.length - 2) + '\]');
                if (aObjects[0].onclick) {
                  sAux = new String(aObjects[0].onclick);
                  eval('aObjects[0].onclick = ' + sAux.replace(/\[1\]/g, '\[' + (this.oGrid.rows.length - 2) + '\]') + ';');
                }
              }
              aObjects = oCell1.getElementsByTagName('a');
              if (aObjects) {
                if (aObjects[0]) {
                  if (aObjects[0].onclick) {
                    sAux = new String(aObjects[0].onclick);
                    eval('aObjects[0].onclick = ' + sAux.replace(/\[1\]/g, '\[' + (this.oGrid.rows.length - 2) + '\]') + ';');
                  }
                }
              }
            }
            //oCell1.innerHTML= aux.innerHTM;
            break;
          case "<selec":
              aObjects1 = oCell1.getElementsByTagName("select");
              aObjects2 = oCell2.getElementsByTagName("select");

              if (aObjects1 && aObjects2) {
                  selectCloneOption.call(aObjects1[0], aObjects2[0], "");

                  aObjects1[0].value = aObjects2[0].value;
              }
              break;
          case '<texta':
            aObjects1 = oCell1.getElementsByTagName('textarea');
            aObjects2 = oCell2.getElementsByTagName('textarea');
            if (aObjects1 && aObjects2) {
              aObjects1[0].value = aObjects2[0].value;
            }
            break;
          default:
            if (( oCell2.innerHTML.indexOf('changeValues')==111 || oCell2.innerHTML.indexOf('changeValues')==115 ) ) {
              //alert('erik2');
              break;
            }
          if (oCell2.innerHTML.toLowerCase().indexOf('deletegridrow') == -1) {
            oCell1.innerHTML = oCell2.innerHTML;
            //alert('erik');
          }
          break;
        }
      }
      iRowAux++;
    }
    this.oGrid.deleteRow(this.oGrid.rows.length - 2);
    if (this.sAJAXPage != '') {
    }
    /* this slice of code was comented because it could be the problem to do that sum function is working wrong
        if (this.aFields.length > 0) {
          this.unsetFields();
        }*/
    //this slice of code was added to fill the grid after to delete some row
    this.aElements = [];
    for (var k=1;k<= this.oGrid.rows.length-2;k++){
      for (var i = 0; i < this.aFields.length; i++) {
        var j = k;
        switch (this.aFields[i].sType) {
          case 'text':
            this.aElements.push(new G_Text(oForm, document.getElementById('form[' + this.sGridName + '][' + j + '][' + this.aFields[i].sFieldName + ']'), this.sGridName + '][' + j + '][' + this.aFields[i].sFieldName));
            this.aElements[this.aElements.length - 1].validate = this.aFields[i].oProperties.validate;
            if(this.aFields[i].oProperties.strTo) {
              this.aElements[this.aElements.length - 1].strTo = this.aFields[i].oProperties.strTo;
            }
            break;
          case 'currency':
            this.aElements.push(new G_Currency(oForm, document.getElementById('form[' + this.sGridName + '][' + j + '][' + this.aFields[i].sFieldName + ']'), this.sGridName + '][' + j + ']['+ this.aFields[i].sFieldName));
            break;
          case 'percentage':
            this.aElements.push(new G_Percentage(oForm, document.getElementById('form[' + this.sGridName + '][' + j + '][' + this.aFields[i].sFieldName + ']'), this.sGridName + '][' + j+ '][' + this.aFields[i].sFieldName));
            break;
          case 'dropdown':
            this.aElements.push(new G_DropDown(oForm, document.getElementById('form[' + this.sGridName + '][' + j + '][' + this.aFields[i].sFieldName + ']'), this.sGridName + '][' + j + ']['+ this.aFields[i].sFieldName));
            break;
        }
        j++;
      }
    }

    if (this.aFunctions.length > 0) {

      for (i = 0; i < this.aFunctions.length; i++) {
        oAux = document.getElementById('form[' + this.sGridName + '][1][' + this.aFunctions[i].sFieldName + ']');
        if (oAux) {
          switch (this.aFunctions[i].sFunction) {
            case 'sum':
              this.sum(false, oAux);
              /*
              aaAux=oAux.name.split('][');
              sNamef=aaAux[2].replace(']', '');
              var sumaSol = 0;
              this.aElements.length;
              var j=1;k=0;
              for ( var i = 0; i < this.aElements.length; i++) {
                nnName= this.aElements[i].name.split('][');
                if (nnName[2] == sNamef && j <= (this.oGrid.rows.length-2)){
                  ooAux=this.getElementByName(j, nnName[2]);

                  if(ooAux!=null){

                  sumaSol += parseFloat(G.cleanMask(ooAux.value() || 0, ooAux.mask).result.replace(/,/g, ''))
                  }
                  j++;
                 }
              }
              sumaSol = sumaSol.toFixed(2);
              oAux = document.getElementById('form[SYS_GRID_AGGREGATE_' + oGrid.sGridName + '_' + sNamef + ']');
              oAux.value = sumaSol;
              oAux = document.getElementById('form[SYS_GRID_AGGREGATE_' + oGrid.sGridName + '__' + sNamef + ']');
              oAux.innerHTML = sumaSol;//return;
               */
              break;
            case 'avg':
              this.avg(false, oAux);
              break;
          }
        }
      }
    }
    if (this.ondeleterow) {
      this.ondeleterow();
    }

    //}.extend(this)

  };
  /*add end*/
};

/**
 * Delete the respective session row grid variables from Dynaform
 *
 * @Param grid
 *            [object: grid]
 * @Param sRow
 *            [integer: row index]
 * @author Erik Amaru Ortiz <erik@colosa.com, aortiz.erik@mail.com>
 */

function deleteRowOnDynaform(grid, sRow) {
  var oRPC = new leimnud.module.rpc.xmlhttp( {
    url : '../gulliver/genericAjax',
    args : 'request=deleteGridRowOnDynaform&gridname=' + grid.sGridName + '&rowpos=' + sRow + '&formID=' + grid.form.id
  });
  oRPC.callback = function(rpc) {
    if(oPanel)
      oPanel.loader.hide();
    scs = rpc.xmlhttp.responseText.extractScript();
    scs.evalScript();

    /**
     * We verify if the debug panel is open, if it is-> update its content
     */
    if ( typeof(oDebuggerPanel) != 'undefined' &&  oDebuggerPanel != null) {
      oDebuggerPanel.clearContent();
      oDebuggerPanel.loader.show();
      var oRPC = new leimnud.module.rpc.xmlhttp( {
        url : 'cases_Ajax',
        args : 'action=showdebug'
      });
      oRPC.callback = function(rpc) {
        oDebuggerPanel.loader.hide();
        var scs = rpc.xmlhttp.responseText.extractScript();
        oDebuggerPanel.addContent(rpc.xmlhttp.responseText);
        scs.evalScript();
      }.extend(this);
      oRPC.make();
    }
  }.extend(this);
  oRPC.make();
}

function selectCloneOption(selectOrigin, defaultValue)
{
    var arrayOption = [];
    var arrayOptGroup = [];
    var arrayOptGroupOption = [];
    var arrayAux = [];
    var optGroupAux;
    var optionAux;
    var i1 = 0;
    var i2 = 0;
    var i3 = 0;
    var swSelected = 0;

    //this.options.length = 0; //Delete options
    this.innerHTML = "";

    for (i1 = 0; i1 <= selectOrigin.options.length - 1; i1++) {
        if (selectOrigin.options[i1].parentNode.nodeName.toLowerCase() != "optgroup") {
            arrayOption.push(["option", selectOrigin.options[i1].value, selectOrigin.options[i1].text]);
        } else {
            if (typeof(arrayAux[selectOrigin.options[i1].parentNode.label]) == "undefined") {
                arrayAux[selectOrigin.options[i1].parentNode.label] = 1;
                arrayOption.push(["optgroup", 1, selectOrigin.options[i1].parentNode.label]);
            }
        }
    }

    for (i1 = 0; i1 <= arrayOption.length - 1; i1++) {
        if (arrayOption[i1][0] == "option") {
            //this.appendChild(new Option(arrayOption[i1][2], arrayOption[i1][1]));
            optionAux = document.createElement("option");

            this.appendChild(optionAux);

            optionAux.value = arrayOption[i1][1];
            optionAux.text = arrayOption[i1][2];

            if (swSelected == 0 && defaultValue != "" && arrayOption[i1][1] == defaultValue) {
                optionAux.setAttribute("selected", "selected");
                swSelected = 1;
            }
        } else {
            arrayOptGroup = selectOrigin.getElementsByTagName("optgroup");

            for (i2 = 0; i2 <= arrayOptGroup.length - 1; i2++) {
                if (arrayOptGroup[i2].label == arrayOption[i1][2]) {
                    arrayOptGroupOption = arrayOptGroup[i2].getElementsByTagName("option");

                    if (arrayOptGroupOption.length > 0) {
                        optGroupAux = document.createElement("optgroup");

                        this.appendChild(optGroupAux);

                        optGroupAux.label = arrayOptGroup[i2].label;

                        for (i3 = 0; i3 <= arrayOptGroupOption.length - 1; i3++) {
                            //optGroupAux.appendChild(new Option(arrayOptGroupOption[i3].text, arrayOptGroupOption[i3].value));
                            optionAux = document.createElement("option");

                            optGroupAux.appendChild(optionAux);

                            optionAux.value = arrayOptGroupOption[i3].value;
                            optionAux.text = arrayOptGroupOption[i3].text;

                            if (swSelected == 0 && defaultValue != "" && arrayOptGroupOption[i3].value == defaultValue) {
                                optionAux.setAttribute("selected", "selected");
                                swSelected = 1;
                            }
                        }
                    }
                }
            }
        }
    }

    if (this.options.length == 0) {
        this.options[0] = new Option("", "");
    }
}

