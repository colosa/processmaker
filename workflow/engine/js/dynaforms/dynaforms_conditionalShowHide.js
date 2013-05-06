/**
 * Conditional show hide class
 *
 * @Author Erik A. Ortiz. <erik@colosa.com, aortiz.erik@gmail.com>
 * @date Feb 22, 2010
 */

var conditionEditorPanel;

var Conditional = function(DYN_UID){
    
    this.DYN_UID = DYN_UID;
    this.client = getBrowserClient();

    this.dynavarsPanel = null;
    this.dynavarsPanelNew = null;
    this.fields = Array();
    this.conditionVariables = new Object();
    this.conditionVariables.___IsSET___ = false;
    this.conditionVariablesFromSetup = new Object();
    this.conditionVariablesFromSetup.___IsSET___ = false;
    this.aConditionVariablesFromSetup = new Array();

    this.canSave = false;
    this.conditionTested = false;

    this.setDynUid = function(id){
        this.DYN_UID = id;
    }

    this.showID = function(){
        alert('DYN_UID: '+this.DYN_UID);
    }

    this.editor = function(sType){
        oPanel = new leimnud.module.panel();
        oPanel.options = {
            size     : {w:730, h:595},
            position : {x:0, y:0, center:true},
            title    : G_STRINGS.CONDITIONAL_TITLE,
            statusBar: false,
            control     : {resize:false,roll:false,drag:true},
            fx       : {modal:true,opacity:true,blinkToFront:false,fadeIn:false,drag:true}
        };
        oPanel.events = {
            remove: function() {delete(oPanel);}.extend(this)
        };
        oPanel.make();
        oPanel.loader.show();

        sExtra = '';
        if(sType != 'new'){
            FCD_UID = sType;
            sType = 'edit';
            sExtra = '&FCD_UID='+FCD_UID;
        }

        var oRPC = new leimnud.module.rpc.xmlhttp({
            url : 'conditionalShowHide_Ajax',
            args: 'request='+sType+sExtra
        });
        oRPC.callback = function(rpc) {
            oPanel.loader.hide();
            var scs = rpc.xmlhttp.responseText.extractScript();
            oPanel.addContent(rpc.xmlhttp.responseText);
            scs.evalScript();
        }.extend(this);
        oRPC.make();
        conditionEditorPanel = this.dynavarsPanelNew = oPanel;
    }


    this.populate = function(filter){

        if( typeof(filter) != 'undefined' ){
            if( filter.IN ){
            //
            }
        }

        var oRPC = new leimnud.module.rpc.xmlhttp({
            url : 'conditionalShowHide_Ajax',
            args: 'request=getDynaFieds'
        });
        oRPC.callback = function(rpc) {
            var scs = rpc.xmlhttp.responseText.extractScript();
            scs.evalScript();
            
            oFields = getField('fields');
            oEventOwner = getField('event_owner');
            var response = eval(oRPC.xmlhttp.responseText);
            
            for(i=0; i<response.length; i++){
                this.fields[i] = response[i];
                var newOption = new Option(response[i], response[i]);
                var newOption2 = new Option(response[i], response[i]);
                
                oFields.options[i] = newOption;
                oEventOwner.options[i] = newOption2;
            }
            oFields[0].selected = true;
            oEventOwner[0].selected = true;
        }.extend(this);
        oRPC.make();
    }

    this.populateEdit = function(){
        var oFCD_FIELDS       = getField('FCD_FIELDS');
        var oFCD_EVENT_OWNERS = getField('FCD_EVENT_OWNERS');
        var oFCD_EVENTS       = getField('FCD_EVENTS');

        var oRPC = new leimnud.module.rpc.xmlhttp({
            url : 'conditionalShowHide_Ajax',
            args: 'request=getDynaFieds'
        });
        oRPC.callback = function(rpc) {
            var j, k;

            oFields = getField('fields');
            oEventOwner = getField('event_owner');
            oFieldsSelected = getField('fields_selected');
            oEventOwnerSelected = getField('event_owner_selected');
            oEventOnload = getField('eventOnload');
            oEventOnchange = getField('eventOnchange');

            var response = eval(oRPC.xmlhttp.responseText);

            j = 0;
            k = 0;
            for(i=0; i<response.length; i++){
                this.fields[i] = response[i];
                // if( oFCD_FIELDS.value.indexOf(response[i]) === -1 ){
                aFCD_FIELDS = oFCD_FIELDS.value.split(",");
                if( indexInArray(aFCD_FIELDS, response[i]) === -1 ){
                    var newOption = new Option(response[i], response[i]);
                    oFields.options[j++] = newOption;
                } else {
                    var newOption = new Option(response[i], response[i]);
                    oFieldsSelected.options[k++] = newOption;
                }
            }

            j = 0;
            k = 0;
            for(i=0; i<response.length; i++){
                this.fields[i] = response[i];
                // if( oFCD_EVENT_OWNERS.value.indexOf(response[i]) === -1 ){
                aFCD_EVENT_OWNERS = oFCD_EVENT_OWNERS.value.split(",");
                if( indexInArray(aFCD_EVENT_OWNERS, response[i]) === -1 ){
                    var newOption = new Option(response[i], response[i]);
                    oEventOwner.options[j++] = newOption;
                } else {
                    var newOption = new Option(response[i], response[i]);
                    oEventOwnerSelected.options[k++] = newOption;
                }
            }

            if( oFCD_EVENTS.value.indexOf('onload') !== -1 ){
                oEventOnload.checked = true;
            }

            if( oFCD_EVENTS.value.indexOf('onchange') !== -1 ){
                oEventOnchange.checked = true;
            }

        }.extend(this);
        oRPC.make();
    }

    this.toSelect = function(target, target2){
        var oTarget = getField(target);
        var oTargetSelected = getField(target2);
        this.deselectAll(oTargetSelected);
        
        var aSelectedOptions = Array();
        
        for (i = 0; i<oTarget.length; i++) {
            if ( oTarget.options[i].selected ){
                aSelectedOptions.push(oTarget.options[i]);
            }
        }
        
        c = oTargetSelected.length;
        for (i = 0; i<aSelectedOptions.length; i++) {
            oTargetSelected.options[c] = new Option(aSelectedOptions[i].text, aSelectedOptions[i].value);
            oTargetSelected.options[c].selected = true;
            c++;
        }
    
        this.dropSelectedOption(target);
    }

    this.dropSelectedOption = function(target){
        var o = getField(target);
        var Options = Array();
        var c = 0;
        var selectedIndex = o.selectedIndex;

        if( o.options.length > 0 ){
            if(o.options.length == 0){
                return false;
            }

            for(i=0; i<o.options.length; i++){
                Options.push(o.options[i]);
            }
        
            o.options.length = 0;

            for(i=0; i<Options.length; i++){
                if( !Options[i].selected ){
                    o.options[c++] = Options[i];
                }
            }

            if( selectedIndex >= 0 && selectedIndex < o.options.length ){
                o.options[selectedIndex].selected = true;
            } else if( selectedIndex >= 0 && o.options.length > 0 ){
                o.options[selectedIndex-1].selected = true;
            }
        }
    }

    this.deselectAll = function(list){
        for(i=0; i<list.length; i++){
            list.options[i].selected = false;
        }
    }

    this.setCharacter = function(sItem){
        var f = getField('FCD_CONDITION');

        switch(this.client.browser){
            case 'msie':
                try{
                    if(typeof document.selection != 'undefined' && document.selection) {
                        var str = document.selection.createRange().text;
                        f.focus();
                        var sel = document.selection.createRange();
                        sel.text =  sItem;
                        sel.select();
                        return;
                    }else if(typeof f.selectionStart != 'undefined'){
                        var start = f.selectionStart;
                        var end = f.selectionEnd;
                        var insText = f.value.substring(start, end);
                        f.value = f.value.substr(0, start) +  sItem + f.value.substr(end);
                        f.focus();
                        f.setSelectionRange(start+2+tag.length+insText.length+3+tag.length,start+2+tag.length+insText.length+3+tag.length);
                        return;
                    }else{
                        f.value+=sItem;
                        return;
                    }
                alert(f.value);
                }catch(e){
                    alert(e+"_");
                }
                break;

            case 'opera':
            case 'safari':
            case 'firefox':

                var _ini = f.selectionStart;
                var _fin = f.selectionEnd;
                var inicio = f.value.substr(0, _ini);
                var fin = f.value.substr(_fin, f.value.length);

                f.value = inicio + sItem + fin;
                if (_ini == _fin)    {
                    f.selectionStart = inicio.length + sItem.length;
                    f.selectionEnd = f.selectionStart;
                }
                else    {
                    f.selectionStart = inicio.length;
                    f.selectionEnd = inicio.length + sItem.length;
                }
                f.focus();
                break;
            default:
        }
    }

    this.showDynavars = function(e){

        if( this.dynavarsPanel !== null ){
            return false;
        }
        oPanel = new leimnud.module.panel();
        oPanel.options = {
            size        : {w:150, h:200},
            position    : {x:e.clientX, y:e.clientY, center:false},
            title        : '',
            statusBar    : false,
            control        : {resize:false,roll:false,drag:true},
            fx            : {modal:false,opacity:true,blinkToFront:false,fadeIn:false,drag:true}
        };
        oPanel.events = {
            remove: function() {
                delete(oPanel);
                this.dynavarsPanel = null;
            }.extend(this)
        };
        oPanel.make();
        oPanel.loader.show();

        this.dynavarsPanel = oPanel;

        var oRPC = new leimnud.module.rpc.xmlhttp({
            url : 'conditionalShowHide_Ajax',
            args: 'request=getDynaFieds'
        });

        oRPC.callback = function(rpc) {
            oPanel.loader.hide();

            response = eval(rpc.xmlhttp.responseText);

            sel = document.createElement('select');
            sel.id='vars';
            sel.size='11';
            sel.multiple='true';
            sel.style.className = 'module_app_input___gray';
            sel.style.fontSize = '11px';
            sel.style.height = '150px';


            sel.ondblclick = function(){
                oConditional.dynavarsPanel.remove();
                oConditional.dynavarsPanel = null;
        
                oConditional.setCharacter('@#'+sel.options[sel.selectedIndex].value);
            }

            max = 0;
            maymax = 0;
            for (i=0, j=response.length; i<j; i++) {
                x = response[i];
                sel.appendChild(document.createElement('option'));
                sel.options[i].text = sel.options[i].value = text =x;
                
                if(x.length > max){
                    max = x.length;
                }
                uppercase_letters="ABCDEFGHYJKLMNÑOPQRSTUVWXYZ_";

                may = 0;
                for(k=0; k<text.length; k++){
                  if (uppercase_letters.indexOf(text.charAt(k),0)!=-1){
                    may++;
                  }
                }
                if(may > maymax){
                    maymax = may;
                }
            }
            max = (max < 15)? 140 : ((max > maymax)? (((max - maymax)*6) + (maymax*9) + 15) : ((maymax*9) + 15));
            sel.style.width = ((max-13)+'px');
            oPanel.resize({w:(max),h:215});
            oPanel.addContent(sel);

        }.extend(this);
        oRPC.make();
    }

    this.testCondition = function(saving){
        var oCondition = getField('FCD_CONDITION');
        var sCondition = oCondition.value;
        var sError;
        var result;
        sCondition = sCondition.replace(/@#/g, '');
        sCondition = sCondition.replace(/\n/gi, '');

        saving = (typeof(saving) != 'undefined')? true: false;

        this.createConditionVariables();
        this.mergeConditionVariables();
        var aCVariables = this.getConditionVariables();

        this.conditionTested = true;

        for(i=0; i<aCVariables.length; i++){
            try {
                eval("var "+aCVariables[i]+" = (typeof(this.conditionVariables."+aCVariables[i]+")!='undefined')? this.conditionVariables."+aCVariables[i]+": '';");
            } catch(e) {
                continue;
            }
        }

        try{
            eval('result = (' + sCondition + ')?1:0;');
            this.canSave = true;
        } catch(e){
            sError = e;
            this.canSave = false;
        }

        if(this.canSave){
            if( saving != true ){
                bResult = (result)? _('ID_TRUE'): _('ID_FALSE');
                oResultDiv = document.getElementById("ResultMessageTD");
                oResultDiv.style.display = 'block';
                oResultDiv.style.backgroundColor = '#BFCCC5';
                oResultDiv.style.color = '#000000';
                oResultDiv.innerHTML = _('ID_EVALUATION_RESULT')+": "+bResult;
                fade('ResultMessageTD', 'inOut');
                var o = new input(oResultDiv);
                var oF = new input(getField('FCD_CONDITION'));
                o.passed();
                oF.passed();
            }
        } else{
            oResultDiv = document.getElementById("ResultMessageTD");
            oResultDiv.style.display = 'block';
            oResultDiv.style.backgroundColor = 'red';
            oResultDiv.style.color = '#fff';
            oResultDiv.innerHTML = '[Failed] '+sError;
            fade('ResultMessageTD', 'inOut');
            var o = new input(oResultDiv);
            var oF = new input(getField('FCD_CONDITION'));
            o.failed();
            oF.failed();
        }
    }


    this.testConditionSetup = function(){
        var oCondition = getField('FCD_CONDITION');
        var sCondition = oCondition.value;
        
        var sFields = '';
        for(i=0; i<this.fields.length; i++){
            if( sCondition.indexOf(this.fields[i]) !== -1 ){
                sFields += (sFields == '')? this.fields[i]: ','+this.fields[i];
            }
        }
        
        if( sFields != '' ){
            oPanel = new leimnud.module.panel();
            oPanel.options = {
                size        : {w:420, h:400},
                position    : {x:0, y:0, center:true},
                title        : '',
                control        : {resize:false,roll:false,drag:true},
                fx            : {modal:true,opacity:true,blinkToFront:false,fadeIn:false,drag:true}
            };
            oPanel.events = {
                remove: function() {
                    delete(oPanel);
                    this.dynavarsPanel = null;
                }.extend(this)
            };
            oPanel.make();
            oPanel.loader.show();
            this.dynavarsPanel = oPanel;
            
            var oRPC = new leimnud.module.rpc.xmlhttp({
                url : 'conditionalShowHide_Ajax',
                args: 'request=testSetup&sFields='+sFields
            });
            
            oRPC.callback = function(rpc) {
                oPanel.loader.hide();
                oPanel.addContent(rpc.xmlhttp.responseText);
                scs = rpc.xmlhttp.responseText.extractScript();
                scs.evalScript();
                
            }.extend(this);
            oRPC.make();
        } else {
            msgBox(G_STRINGS.CONDITIONAL_NOFIELDS_IN_CONDITION, 'alert');
        }

    }

    this.getConditionVariables = function(){
        var oCondition = getField('FCD_CONDITION');
        var sCondition = oCondition.value;
        var conditionVariables = new Array();
        var j=0;

        for(i=0; i<this.fields.length; i++){
            if( sCondition.indexOf(this.fields[i]) !== -1 ){
                conditionVariables[j++] = this.fields[i];
            }
        }

        return conditionVariables;
    }

    this.createConditionVariablesFromSetup = function(){
        var sName, sValue;
        var i, j=0;

        for(i=1; i<Number.MAX_VALUE; i++){
            try{
                if( typeof(document.getElementById('form[gFields]['+i+'][dynaid]')) == 'undefined' ){
                    break;
                }
                var oVar = new Object()
                sName = document.getElementById('form[gFields]['+i+'][dynaid]').value;
                sValue = document.getElementById('form[gFields]['+i+'][dynavalue]').value;
                oVar.name = sName;
                oVar.value = sValue;
                this.aConditionVariablesFromSetup[j++] = oVar;
                eval("this.conditionVariablesFromSetup."+sName+"='"+sValue+"';");
            } catch(e){
                break;
            }
        }
        this.dynavarsPanel.remove();
    }

    this.createConditionVariables = function(){
        var conditionVariables = this.getConditionVariables();
        for(i=0; i<conditionVariables.length; i++){
            eval("this.conditionVariables."+conditionVariables[i]+"='';");
        }
        this.conditionVariables.___IsSET___ = true;
    }

    this.getConditionVariable = function (varname){
        var vVar = eval("this.conditionVariables."+conditionVariables+"='';");
        return vVar;
    }

    this.mergeConditionVariables = function(){
        var aCV = this.getConditionVariables();
        for(i=0; i<aCV.length; i++){
            try {
                eval("this.conditionVariables."+aCV[i]+"=(typeof(this.conditionVariablesFromSetup."+aCV[i]+")!='undefined')? this.conditionVariablesFromSetup."+aCV[i]+": '';");
            } catch(e) {
                continue;
            }
        }
    }

    this.populateTestConditionSetup = function(){
        var sName, sValue;
        var i;
        
        for(i=1; i<Number.MAX_VALUE; i++){
            try{
                if( typeof(document.getElementById('form[gFields]['+i+'][dynaid]')) == 'undefined' ){
                    break;
                }
                sName = document.getElementById('form[gFields]['+i+'][dynaid]').value;
                
                eval("document.getElementById('form[gFields]["+i+"][dynavalue]').value = (typeof(this.conditionVariablesFromSetup."+sName+") != 'undefined')? this.conditionVariablesFromSetup."+sName+": '';");
            } catch(e){
                break;
            }
        }
    }

    this.saveCondition = function(){
    	if (!sessionPersits()) {
    	    showPrompt('conditionalShowHide');
    	    return;
    	}
        var oTmp;
        this.canSave = true;
        oUID = getField('FCD_UID');
        oFieldsSelected = getField('fields_selected');
        oEventOwnerSelected = getField('event_owner_selected');
        getField('FCD_CONDITION').value = getField('FCD_CONDITION').value.replace(/\n/gi, '');
        oCondition = getField('FCD_CONDITION');
        oFunction = getField('FCD_FUNCTION');
        sEvents = '';

        oOnChange = getField('eventOnload');
        if(oOnChange.checked){
            sEvents = 'onload';
        }

        oOnLoad = getField('eventOnchange');
        if(oOnLoad.checked){
            sEvents += (sEvents != '')? ',onchange': 'onchange';
        }
        oEnabled = getField('FCD_STATUS');
        sEnabled = (oEnabled.checked)? '1': '0';

        //validations...
        oTmp = new input(oFieldsSelected);
        if( oFieldsSelected.length != 0 || oFunction.value == 'showAll' || oFunction.value == 'hideAll'){
            oTmp.passed();
        } else {
            oTmp.failed();
            this.canSave = false;
        }
        
        oTmp = new input(oEventOwnerSelected);
        if( oEventOwnerSelected.length != 0 ){
            oTmp.passed();
        } else {
            oTmp.failed();
            this.canSave = false;
        }

        oTmp = new input(oCondition);
        if( oCondition.value.trim() != '' ){
            oTmp.passed();
        } else {
            oTmp.failed();
            oResultDiv = document.getElementById("ResultMessageTD");
            oResultDiv.style.display = 'none';
            this.canSave = false;
        }

        
        if( this.canSave ){
            //alert('ok');
        } else {
            msgBox(G_STRINGS.CONDITIONAL_ALERT1, 'alert');
            return false;
        }

        oTmp = new input(oOnChange);
        if( oOnChange.checked || oOnLoad.checked ){
            oTmp.passed();
        } else {
            msgBox(G_STRINGS.CONDITIONAL_ALERT2, 'alert');
            oTmp.failed();
            this.canSave = false;
            return false;
        }
        
        if( !this.conditionTested ){
            msgBox(G_STRINGS.CONDITIONAL_ALERT3, 'confirm', this.doSave, this.cancelSave);
        } else{
            this.testCondition('saving');
            if( this.canSave ){
                this.doSave();
            } else {
                msgBox(G_STRINGS.CONDITIONAL_ALERT4, 'confirm', this.doSave, this.cancelSave);
            }
        }
        
    }

    this.doSave = function(){

        oUID = getField('FCD_UID');
        oFieldsSelected = getField('fields_selected');
        oEventOwnerSelected = getField('event_owner_selected');
        getField('FCD_CONDITION').value = getField('FCD_CONDITION').value.replace(/\n/gi, '');
        oCondition = getField('FCD_CONDITION');
        oFunction = getField('FCD_FUNCTION');
        sEvents = '';

        oOnChange = getField('eventOnload');
        if(oOnChange.checked){
            sEvents = 'onload';
        }

        oOnLoad = getField('eventOnchange');
        if(oOnLoad.checked){
            sEvents += (sEvents != '')? ',onchange': 'onchange';
        }
        oEnabled = getField('FCD_STATUS');
        sEnabled = (oEnabled.checked)? '1': '0';
        
        fields_selected = '';
        for(i=0; i<oFieldsSelected.length; i++){
            fields_selected += (fields_selected == '')? oFieldsSelected[i].value: ','+oFieldsSelected[i].value;
        }
        event_owner_selected = '';
        for(i=0; i<oEventOwnerSelected.length; i++){
            event_owner_selected += (event_owner_selected == '')? oEventOwnerSelected[i].value: ','+oEventOwnerSelected[i].value;
        }
        sCondition = escape(oCondition.value);
        sCondition = sCondition.replace(/\+/g, '%2B');
        
        var oRPC = new leimnud.module.rpc.xmlhttp({
            url : 'conditionalShowHide_Ajax',
            args: 'request=save&fields_selected='+fields_selected+'&event_owner_selected='+event_owner_selected+'&function='+oFunction.value+'&condition='+sCondition+'&events='+sEvents+'&enabled='+sEnabled+'&FCD_UID='+oUID.value
        });
        

        oRPC.callback = function(rpc) {
            conditionEditorPanel.remove();
            var oRPC = new leimnud.module.rpc.xmlhttp({url: 'conditionalShowHide', args: ''});
            oRPC.callback = function(rpc) {
                var scs=rpc.xmlhttp.responseText.extractScript();
                document.getElementById('dynaformEditor[9]').innerHTML = rpc.xmlhttp.responseText;
                scs.evalScript();
            }.extend(this);
            oRPC.make();
        }.extend(this);
        oRPC.make();
    }

    this.cancelSave = function(){
        //alert('calcel');
    }

    this.remove = function(FCD_UID){
        msgBox('Are you sure to delete this condition?', 'confirm', function(){
            var oRPC = new leimnud.module.rpc.xmlhttp({url: 'conditionalShowHide_Ajax', args: 'request=delete&FCD_UID='+FCD_UID});

            oRPC.callback = function(rpc) {
                var oRPC = new leimnud.module.rpc.xmlhttp({url: 'conditionalShowHide', args: ''});
                oRPC.callback = function(rpc) {
                    var scs=rpc.xmlhttp.responseText.extractScript();
                    document.getElementById('dynaformEditor[9]').innerHTML = rpc.xmlhttp.responseText;
                    scs.evalScript();
                }.extend(this);
                oRPC.make();
            }.extend(this);
            oRPC.make(); 
        });
    }

}

function conditionHasChanged(){
    oConditional.testSaveCondition();
}

function indexInArray(arr,val){
	for(var i=0;i<arr.length;i++)
		if(arr[i]==val) return i;
	return -1;
}

 
