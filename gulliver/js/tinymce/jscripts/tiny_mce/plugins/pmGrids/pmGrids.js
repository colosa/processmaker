 $(document).ready(function () {
     
    /**
     * @function getGridList
     * @description The function executes an ajax call using jquery 
     *              in order to retrieve the grid lists for the current process
     *              in JSON format
     * @param void
     * @return responseData
     */ 
    var getGridList = function(){
        var responseData
        var url = tinyMCE.activeEditor.domainURL+"processes/processes_Ajax"; // action url that processes the ajax call
        responseData = $.ajax({ // jquery ajax call
            url : url,
            type: "POST",
            data: {action : 'getGridList', PRO_UID: tinyMCE.activeEditor.processID}, // parameters
            async: false,
            dataType: "json" // json response type
        }).responseText;
        responseData = eval("(" +responseData+ ")");
        return responseData;
    }
    
    /**
     * @function getGridFieldList
     * @description The function obtains a JSON object that represents the field
     *              list of some grid dynaform, also renders the list in a table
     *              inside the plugin form
     * @param  gridUid
     * @return void
     */
    var getGridFieldList = function(gridUid){
        // jquery ajax call
        var responseData = $.ajax({
            url : tinyMCE.activeEditor.domainURL+"processes/processes_Ajax",
            type: "POST",
            data: {action : 'getVariableGrid', PRO_UID: tinyMCE.activeEditor.processID, DYN_UID: gridUid},
            dataType: "json",
            async:false
        }).responseText;
        
        responseData = eval("("+responseData+")"); 
        // processing the ajax response text and rendering the field list 
        // in a table
        $('#listContainer').html('');
        var divHeader = '<tr>';
        var divCell   = '<tr>';
        $.each(responseData, function(index, element){
            divHeader += "<td><input type='checkbox' class='headerField' name='headerField' value='"+element+"'/><input type='text' style='width:80px;' name='dynafield' class='dynaField' value='"+element+"' id='"+element+"'></td>";
            divCell   += "<td align='center'><input type='hidden' id='field_"+element+"' value='"+element+"'>"+element+"</td>";            
        });
        divHeader += '</tr>';
        divCell   += '</tr>';        
        $('#listContainer').append(divHeader+divCell); //append the result
    };
    /**
     * @function generateListValues
     * @description This function obtains the grid list and also renders the 
     *              grid list dropdown box.
     *              
     */
    var generateListValues = function(){
        var list = getGridList();
        var combo = document.getElementById("gridList");
        var option = document.createElement('option');
        for(i=(combo.length-1); i>=0; i--)
        {
            var aDelete = combo.options[i];
            aDelete.parentNode.removeChild(aDelete);
        }

        if(list.length>0){
            for(i=0; i<list.length; i++)
            {
               option = document.createElement("OPTION");
               option.value = list[i].sXmlForm;
               option.text  = list[i].sName;
               combo.add(option);
            }
        } else {
            option = document.createElement("OPTION");
            option.value = 0;
            option.text = 'No Grids';
            combo.add(option);
        }
    }
    /**
     * @function insertFormatedGrid
     * @description Based on the checked fields, the function assembles the code
     *              to be included inside the tinyMCE editor, using some of the 
     *              plugin functions. Then includes the code in the current 
     *              active TinyMCE editor.
     * 
     */
    var insertFormatedGrid = function(){
        var gridName = $("#gridList option:selected").text();
        var borderSet  = "border='1' cellspacing='0'";
        if ($("#borderCheckbox").attr("checked")!="checked"){
            borderSet = "border='0' cellspacing='0'";
        } 
        var tableCode = "<table "+borderSet+">"
        var gridCode  = "<!--@>"+gridName+"-->";
        var headerCode = "<tr>";
        var fieldCode  = "<tr>";
        
        $('#listContainer .headerField').each(function(i){
            if (this.checked == true) {
                headerCode += "<th>"+$('#'+this.value).val()+"</th>";
                fieldCode += "<td>"+$('#prefixList').val()+$('#field_'+this.value).val()+"</td>";
            }
        });
        headerCode += "</tr>";
        fieldCode  += "</tr>";
        if ($("#headersCheckbox").attr("checked")!="checked"){
            headerCode = '';
        }
        
        
        
        gridCode  += fieldCode+"<!--@<"+gridName+"-->";
        tableCode += headerCode+gridCode+"</table>";
        updateEditorContent(tableCode);
    }
    
    // Whenever the gridList changes a new set of fields is populated.
    $('#gridList').change(function(){
        getGridFieldList($(this).val());
    });
    
    // if the user clicks the insert button the proccessed code is inserted in
    // the editor
    $('#insert').click(function(){
        insertFormatedGrid();
    });
    
    // If the user cancel the action the popup closes
    $('#cancel').click(function(){
        closePluginPopup();
    });
    
    generateListValues();     //generate the list values for the dropdown
    getGridFieldList($("#gridList").val()); // generate the field list for 
    // the first element in the dropdown.
});