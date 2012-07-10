/* function changeVariables
 * @param varType variable type
 * @param proUid process uid
 * @param field process uid
 * @param symbol process uid
 * @param target target Element
 * @desc a rpc call to change the variables that are show in the dynaform window
 *       sorted by the type of the variable
 */
var changeVariables = function(varType,proUid,field,symbol,target) {
//    document.getElementById(target).innerHTML = "response test";
//    alert (document.getElementById(target).innerHTML);
    //G.alert(PRO_UID);
    //G.alert(getField('DYNAFORM').value);
    //G.alert(getField('TASKS').value);
    varType = varType.toLowerCase();

    var oRPC = new leimnud.module.rpc.xmlhttp({
      url   : '../controls/varsAjaxByType',
      async : true,
      method: 'POST',
      args  : "type="+varType+"&sProcess="+proUid+"&sFieldName="+field+"&sSymbol="+symbol+"&bIncMulSelFields=0"
    });

    oRPC.callback = function(rpc){
      var scs=rpc.xmlhttp.responseText.extractScript();
      document.getElementById(target).innerHTML = rpc.xmlhttp.responseText;
      scs.evalScript();
    }.extend(this);
    oRPC.make();
}
