/**
 * Main events routines
 * @Autor Erik A.O. <erik@colosa.com>
 **/

var eventsNewAction = function(oForm) {
  if(getField('EVN_DESCRIPTION').value.trim() == ''){
    msgBox('Set a description please.', 'alert');
    return false;
  }

  if (oForm.onsubmit()) {
    oRPC = new leimnud.module.rpc.xmlhttp({
      url   : '../events/eventsNewAction',
      method: 'POST',
      args  : 'PRO_UID=' + getField('PRO_UID').value
            + '&EVN_DESCRIPTION=' + getField('EVN_DESCRIPTION').value
            + '&EVN_STATUS=' + getField('EVN_STATUS').value
            + '&EVN_WHEN=' + getField('EVN_WHEN').value
            + '&EVN_WHEN_OCCURS=' + getField('EVN_WHEN_OCCURS').value
            + '&EVN_RELATED_TO=' + getField('EVN_RELATED_TO').value
            + '&TAS_UID=' + getField('TAS_UID').value
            + '&EVN_TAS_UID_FROM=' + getField('EVN_TAS_UID_FROM').value
            + '&EVN_TAS_UID_TO=' + getField('EVN_TAS_UID_TO').value
            + '&EVN_TAS_ESTIMATED_DURATION=' + getField('EVN_TAS_ESTIMATED_DURATION').value
            + '&EVN_TIME_UNIT=' + getField('EVN_TIME_UNIT').value
            + '&EVN_ACTION=' + getField('EVN_ACTION').value
            + '&EVN_CONDITIONS=' + getField('EVN_CONDITIONS').value
            + '&TRI_UID=' + getField('TRI_UID').value
    });
    oRPC.callback = function(oRPC) {
      currentPopupWindow.clearContent();
      currentPopupWindow.addContent(oRPC.xmlhttp.responseText);
      var scs = oRPC.xmlhttp.responseText.extractScript();
      scs.evalScript();
      refreshEventList();

      currentPopupWindow.resize({w:620,h:500});

    }.extend(this);
    oRPC.make();
  }
};

/** Event Actions Composer **/
var EventCompose = function(t){

	this.ie = document.all ? true : false;
	this.target = t;

	this.set= function(v){
		this.taget = v;
	}

	this.add=function(){

		this.deselectAll();
		o = getField(this.target);
		val = getField(this.target+'_SIMPLEADD').value;


		if(this.exists(val)) {
			new leimnud.module.app.alert().make({label: G_STRINGS.EVENT_EMAILEXISTS});
			return false;
		}
		if(val == ''){
			return false;
		}
		if(!this.validEmail(val)){
			new leimnud.module.app.alert().make({label: G_STRINGS.ID_INVALID_EMAIL});
			return false;
		}

		id = val;
		id = id.replace('"', '&quote;');
		id = id.replace('"', '&quote;');


		var newOption = new Option(val, "ext|"+id);
		newOption.selected=true;
		o.options[o.options.length] = newOption;

		getField(this.target+'_SIMPLEADD').value = '';
		return false;
	}
	this.showUsers=function(e){

		oPanel = new leimnud.module.panel();
		oPanel.options = {
		  	size	:{w:400,h:310},
		  	position:{x:e.clientX,y:e.clientY,center:false},
		  	title	:'',
		  	theme	:"processmaker",
		  	statusBar:false,
		  	control	:{resize:false,roll:false,drag:true},
		  	fx	:{modal:true,opacity:true,blinkToFront:false,fadeIn:false,drag:true}
	  	};
	  	oPanel.events = {
	  		remove: function() { delete(oPanel); }.extend(this)
	  	};
		oPanel.make();
		oPanel.loader.show();
		var oRPC = new leimnud.module.rpc.xmlhttp({
		  	url : '../events/eventsAjax',
		  	args: 'request=showUsers'
	  	});
	  	oRPC.callback = function(rpc) {
		  	oPanel.loader.hide();
		  	var scs=rpc.xmlhttp.responseText.extractScript();
		  	oPanel.addContent(rpc.xmlhttp.responseText);
		  	scs.evalScript();
	  	}.extend(this);
		oRPC.make();
	}

	this.showGroups=function(e){

		oPanel = new leimnud.module.panel();
		oPanel.options = {
		  	size	:{w:350,h:310},
		  	position:{x:e.clientX,y:e.clientY,center:false},
		  	title	:'',
		  	theme	:"processmaker",
		  	statusBar:false,
		  	control	:{resize:false,roll:false,drag:true},
		  	fx	:{modal:true,opacity:true,blinkToFront:false,fadeIn:false,drag:true}
	  	};
	  	oPanel.events = {
	  		remove: function() { delete(oPanel); }.extend(this)
	  	};
		oPanel.make();
		oPanel.loader.show();
		var oRPC = new leimnud.module.rpc.xmlhttp({
		  	url : '../events/eventsAjax',
		  	args: 'request=showGroups'
	  	});
	  	oRPC.callback = function(rpc) {
		  	oPanel.loader.hide();
		  	var scs=rpc.xmlhttp.responseText.extractScript();
		  	oPanel.addContent(rpc.xmlhttp.responseText);
		  	scs.evalScript();
	  	}.extend(this);
		oRPC.make();
	}

	this.showDynavars=function(e){

		oPanel = new leimnud.module.panel();
		oPanel.options = {
		  	size	:{w:450,h:400},
		  	position:{x:e.clientX,y:e.clientY,center:false},
		  	title	:'',
		  	theme	:"processmaker",
		  	statusBar:false,
		  	control	:{resize:false,roll:false,drag:true},
		  	fx	:{modal:true,opacity:true,blinkToFront:false,fadeIn:false,drag:true}
	  	};
	  	oPanel.events = {
	  		remove: function() { delete(oPanel); }.extend(this)
	  	};
		oPanel.make();
		oPanel.loader.show();
		var oRPC = new leimnud.module.rpc.xmlhttp({
//		  	url : '../events/eventsAjax',
//		  	args: 'request=showDynavars',
		  	url : '../controls/varsAjax',
		  	args: 'sSymbol=@@&displayOption=event'
	  	});
	  	oRPC.callback = function(rpc) {
		  	oPanel.loader.hide();
		  	var scs=rpc.xmlhttp.responseText.extractScript();
		  	oPanel.addContent(rpc.xmlhttp.responseText);
		  	scs.evalScript();
	  	}.extend(this);
		oRPC.make();
	}
	this.pos=function(e){
		var u = ie ? event.clientY + document.documentElement.scrollTop : e.pageY;
		var l = ie ? event.clientX + document.documentElement.scrollLeft : e.pageX;

		return 0;
	}
	this.toAdd= function(id, value, prefix){

		if(this.exists(id)) {
			new leimnud.module.app.alert().make({label: G_STRINGS.EVENT_EMAILEXISTS});
			return false;
		}

		this.deselectAll();
		o = getField(this.target);

		if(prefix == 'dyn'){
			var newOption = new Option('@#'+value, prefix+"|"+id);
		} else {
			var newOption = new Option(value, prefix+"|"+id);
		}

		newOption.selected=true;
		o.options[o.options.length] = newOption;
	}
        this.insertFormVar= function(id, value, prefix){

		if(this.exists(id)) {
                    new leimnud.module.app.alert().make({label: G_STRINGS.EVENT_EMAILEXISTS});
                    return false;
		}

		this.deselectAll();
		o = getField(this.target);

		if(prefix == 'dyn'){
			var newOption = new Option('@#'+value, prefix+"|"+id);
		} else {
			var newOption = new Option(value, prefix+"|"+id);
		}

		newOption.selected=true;
		o.options[o.options.length] = newOption;
                oPanel.remove();
	}
	this.dropSel= function(){
	    var o = getField(this.target);
		c=0;
		Options = Array();
		if(o.options.length == 0){
			return false;
		}
	    for(i=0; i<o.options.length; i++){
	    	Options.push(o.options[i]);
	    }
	    o.options.length = 0;
	    for(i=0; i<Options.length; i++){

	    	if(!Options[i].selected){
				//var newOption = new Option(id, value);
				o.options[c++] = Options[i]; //newOption;
			}
	    }
	    if(o.options.length>0){
	    	o.options[o.options.length-1].selected = true;
	    }

	}
	this.deselectAll= function(){
		var o = getField(this.target);
		for(i=0; i<o.options.length; i++){
	    	o.options[i].selected = false;
	    }
	}
	this.exists= function(value){
		var o = getField(this.target);
		for(i=0; i<o.options.length; i++){
			v = o.options[i].value;
			if(value == v.substr(4)){
	    		return true;
	    	}
	    }
	    return false;
	}
	this.validEmail = function (val) {
	    if( val.indexOf('<') !=-1 && val.indexOf('>') !=-1 ){
			if (/^([\"\w@\.-_\s]*\s*)?<\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+>$/.test(val)){
				return true;
			} else {
				return false;
			}
		} else {
		  if (/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(val)){
		      return true;
		  } else {
		      return false;
		  }
		}
	}

}