(function () {
  var Pos = CodeMirror.Pos;

  function forEach(arr, f) {
    for (var i = 0, e = arr.length; i < e; ++i) f(arr[i]);
  }
  
  function arrayContains(arr, item) {
    if (!Array.prototype.indexOf) {
      var i = arr.length;
      while (i--) {
        if (arr[i] === item) {
          return true;
        }
      }
      return false;
    }
    return arr.indexOf(item) != -1;
  }

  function scriptHint(editor, keywords, getToken, options) {
    // Find the token at the cursor
    var cur = editor.getCursor(), token = getToken(editor, cur), tprop = token;
    var sToken = token.string.trim();

    if ( sToken == "(") {
 	token = tprop = getToken(editor, Pos(cur.line, tprop.start));
        return {list: getCompletions(token.string, keywords, options),
                from: Pos(cur.line, token.start),
                to: Pos(cur.line, token.end + 1)};
    }
    if ( sToken == "=") {
        return {list: getCompletions(token.string, keywords, options),
                from: Pos(cur.line, token.start + 1),
                to: Pos(cur.line, token.end)};
    }
    return {list: getCompletions(token.string, keywords, options),
            from: Pos(cur.line, token.start),
            to: Pos(cur.line, token.end)};
  }

  CodeMirror.phpHint = function(editor, options) {
    return scriptHint(editor, phpPMFunctions, function (e, cur) {return e.getTokenAt(cur);}, options);
  };

  var SPACE = " ";

  var formatDateFunction = ("formatDate($date,$format,$language) formatDate($date,$format)").split(SPACE);
  var getCurrentDateFunction = ("getCurrentDate()").split(SPACE);
  var getCurrentTimeFunction = ("getCurrentTime()").split(SPACE);
  var literalDateFunction = ("literalDate($date,$Language) literalDate($date)").split(SPACE);
  var capitalizeFunction = ("capitalize($textToConvert)").split(SPACE);
  var lowerCaseFunction = ("lowerCase($textToConvert)").split(SPACE);
  var upperCaseFunction = ("upperCase($textToConvert)").split(SPACE);
  var userInfoFunction = ("userInfo($USER_ID)").split(SPACE);
  var executeQueryFunction = ("executeQuery($sqlStatement,$DBConnectionUID) executeQuery($sqlStatement)").split(SPACE);
  var orderGridFunction = ("orderGrid($gridName,$field,$criteria) orderGrid($gridName,$field)").split(SPACE);
  var evaluateFunctionFunction = ("evaluateFunction($gridName,$Expression)").split(SPACE);

  var PMFTaskCaseFunction = ("PMFTaskCase($caseId)").split(SPACE);
  var PMFTaskListFunction = ("PMFTaskList($userId)").split(SPACE);
  var PMFUserListFunction = ("PMFUserList()").split(SPACE);
  var PMFGroupListFunction = ("PMFGroupList()").split(SPACE);
  var PMFRoleListFunction = ("PMFRoleList()").split(SPACE);
  var PMFCaseListFunction = ("PMFCaseList($userId) PMFCaseList()").split(SPACE);
  var PMFProcessListFunction = ("PMFProcessList()").split(SPACE);
  var PMFSendVariablesFunction = ("PMFSendVariables($caseId,$variables)").split(SPACE);
  var PMFDerivateCaseFunction = ("PMFDerivateCase($caseId,$delegation,$executeTriggersBeforeAssigment) PMFDerivateCase($caseId,$delegation)").split(SPACE);
  var PMFNewCaseImpersonateFunction = ("PMFNewCaseImpersonate($processId,$userId,$variables)").split(SPACE);
  var PMFNewCaseFunction = ("PMFNewCase($processId,$userId,$taskId,$variables)").split(SPACE);
  var PMFPauseCaseFunction = ("PMFPauseCase($caseUid,$delIndex,$userUid,$unpauseDate) PMFPauseCase($caseUid,$delIndex,$userUid)").split(SPACE);
  var PMFAssignUserToGroupFunction = ("PMFAssignUserToGroup($userId,$groupId)").split(SPACE);
  var PMFCreateUserFunction = ("PMFCreateUser($userId,$password,$firstname,$lastname,$email,$role)").split(SPACE);
  var PMFUpdateUserFunction = ("PMFUpdateUser($userUid,$userName,$firstName,$lastName,$email,$dueDate,$status,$role,$password)").split(SPACE);
  var PMFInformationUserFunction = ("PMFInformationUser($userUid)").split(SPACE);
  var generateCodeFunction = ("generateCode($size,$type)").split(SPACE);
  var setCaseTrackerCodeFunction = ("setCaseTrackerCode($caseId,$code,$pin)").split(SPACE);
  var jumpingFunction = ("jumping($caseId,$delegation)").split(SPACE);
  var PMFRedirectToStepFunction = ("PMFRedirectToStep($caseId,$delegation,$stepType,$stepId)").split(SPACE);
  var pauseCaseFunction = ("pauseCase($caseId,$delegation,$userId,$unpauseDate) pauseCase($caseId,$delegation,$userId)").split(SPACE);
  var PMFSendMessageFunction = ("PMFSendMessage($caseId,$from,$to,$cc,$bcc,$subject,$template,$fields,$attachments) PMFSendMessage($caseId,$from,$to,$cc,$bcc,$subject,$template,$fields) PMFSendMessage($caseId,$from,$to,$cc,$bcc,$subject,$template)").split(SPACE);
  var PMFgetLabelOptionFunction = ("PMFgetLabelOption($processId,$dynaformId,$fieldName,$optionId)").split(SPACE);
  var PMFGenerateOutputDocumentFunction = ("PMFGenerateOutputDocument($outputID)").split(SPACE);
  var PMFGetUserEmailAddressFunction = ("PMFGetUserEmailAddress($id,$APP_UID,$prefix) PMFGetUserEmailAddress($id,$APP_UID) PMFGetUserEmailAddress($id)").split(SPACE);
  var PMFGetNextAssignedUserFunction = ("PMFGetNextAssignedUser($application,$task)").split(SPACE);
  var PMFDeleteCaseFunction = ("PMFDeleteCase($caseId)").split(SPACE);
  var PMFCancelCaseFunction = ("PMFCancelCase($caseUid,$delIndex,$userUid)").split(SPACE);
  var PMFAddInputDocumentFunction = ("PMFAddInputDocument($inputDocumentUid,$appDocUid,$docVersion,$appDocType,$appDocComment,$inputDocumentAction,$caseUid,$delIndex,$taskUid,$userUid,$option,$file) PMFAddInputDocument($inputDocumentUid,$appDocUid,$docVersion,$appDocType,$appDocComment,$inputDocumentAction,$caseUid,$delIndex,$taskUid,$userUid,$option) PMFAddInputDocument($inputDocumentUid,$appDocUid,$docVersion,$appDocType,$appDocComment,$inputDocumentAction,$caseUid,$delIndex,$taskUid,$userUid)").split(SPACE);
  var PMFAddCaseNoteFunction = ("PMFAddCaseNote($caseUid,$processUid,$taskUid,$userUid,$note,$sendMail)").split(SPACE);
  var PMFGetCaseNotesFunction = ("PMFGetCaseNotes($applicationID,$type,$userUid) PMFGetCaseNotes($applicationID,$type) PMFGetCaseNotes($applicationID)").split(SPACE);

  var phpPMFunctions = ("formatDate getCurrentDate getCurrentTime literalDate capitalize lowerCase upperCase userInfo executeQuery orderGrid " +
  "evaluateFunction PMFTaskCase PMFTaskList PMFUserList PMFGroupList PMFRoleList PMFCaseList PMFProcessList PMFSendVariables PMFDerivateCase " +
  "PMFNewCaseImpersonate PMFNewCase PMFPauseCase PMFUnpauseCase PMFAssignUserToGroup PMFCreateUser PMFUpdateUser PMFInformationUser " +        
  "generateCode setCaseTrackerCode jumping PMFRedirectToStep pauseCase PMFSendMessage PMFgetLabelOption PMFGenerateOutputDocument " +    
  "PMFGetUserEmailAddress PMFGetNextAssignedUser PMFDeleteCase PMFCancelCase PMFAddInputDocument PMFAddCaseNote PMFGetCaseNotes").split(SPACE);

  var phpKeywords = ("break case catch continue default delete do else false for function " +
                  "if new return switch throw true try var void while").split(SPACE);

  function getCompletions(functionName, keywords, options) {
    
    var found = [];

    function maybeAdd(str) {// for keywords ?
      if ( str.indexOf(functionName) == 0 && !arrayContains(found, str)) {
         found.push(str);
      }
    }

    function yesAdd(str) {
      if ( !arrayContains(found, str)) {
         found.push(str);
      }
    }

    if (functionName == "formatDate") {
	forEach( formatDateFunction, yesAdd);
    } else if (functionName == "getCurrentDate") {
	forEach( getCurrentDateFunction, yesAdd);
    } else if (functionName == "getCurrentTime") {
        forEach( getCurrentTimeFunction, yesAdd);
    } else if (functionName == "literalDate") {
	forEach( literalDateFunction, yesAdd);
    } else if (functionName == "capitalize") {
	forEach( capitalizeFunction, yesAdd);
    } else if (functionName == "lowerCase") {
	forEach( lowerCaseFunction, yesAdd);
    } else if (functionName == "upperCase") {
	forEach( upperCaseFunction, yesAdd);
    } else if (functionName == "evaluateFunction") {
	forEach( evaluateFunctionFunction, yesAdd);
    } else if (functionName == "userInfo") {
	forEach( userInfoFunction, yesAdd);
    } else if (functionName == "executeQuery") {
	forEach( executeQueryFunction, yesAdd);
    } else if (functionName == "orderGrid") {
	forEach( orderGridFunction, yesAdd);
    } else if (functionName == "PMFTaskCase") {
	forEach( PMFTaskCaseFunction, yesAdd);
    } else if (functionName == "PMFTaskList") {
	forEach( PMFTaskListFunction, yesAdd);
    } else if (functionName == "PMFUserList") {
	forEach( PMFUserListFunction, yesAdd);
    } else if (functionName == "PMFGroupList") {
	forEach( PMFGroupListFunction, yesAdd);
    } else if (functionName == "PMFRoleList") {
	forEach( PMFRoleListFunction, yesAdd);
    } else if (functionName == "PMFCaseList") {
	forEach( PMFCaseListFunction, yesAdd);
    } else if (functionName == "PMFProcessList") {
	forEach( PMFProcessListFunction, yesAdd);
    } else if (functionName == "PMFSendVariables") {
	forEach( PMFSendVariablesFunction, yesAdd);
    } else if (functionName == "PMFNewCase") {
	forEach( PMFNewCaseFunction, yesAdd);
    } else if (functionName == "PMFPauseCase") {
	forEach( PMFPauseCaseFunction, yesAdd);
    } else if (functionName == "PMFUnpauseCase") {
	forEach( PMFUnpauseCaseFunction, yesAdd);
    } else if (functionName == "PMFAssignUserToGroup") {
	forEach( PMFAssignUserToGroupFunction, yesAdd);
    } else if (functionName == "PMFCreateUser") {
	forEach( PMFCreateUserFunction, yesAdd);
    } else if (functionName == "PMFUpdateUser") {
	forEach( PMFUpdateUserFunction, yesAdd);
    } else if (functionName == "PMFInformationUser") {
	forEach( PMFInformationUserFunction, yesAdd);
    } else if (functionName == "generateCode") {
	forEach( generateCodeFunction, yesAdd);
    } else if (functionName == "setCaseTrackerCode") {
	forEach( setCaseTrackerCodeFunction, yesAdd);
    } else if (functionName == "jumping") {
	forEach( jumpingFunction, yesAdd);
    } else if (functionName == "PMFRedirectToStep") {
	forEach( PMFRedirectToStepFunction, yesAdd);
    } else if (functionName == "pauseCase") {
	forEach( pauseCaseFunction, yesAdd);
    } else if (functionName == "PMFSendMessage") {
	forEach( PMFSendMessageFunction, yesAdd);
    } else if (functionName == "PMFgetLabelOption") {
	forEach( PMFgetLabelOptionFunction, yesAdd);
    } else if (functionName == "PMFGenerateOutputDocument") {
	forEach( PMFGenerateOutputDocumentFunction, yesAdd);
    } else if (functionName == "PMFGetUserEmailAddress") {
	forEach( PMFGetUserEmailAddressFunction, yesAdd);
    } else if (functionName == "PMFGetNextAssignedUser") {
	forEach( PMFGetNextAssignedUserFunction, yesAdd);
    } else if (functionName == "PMFDeleteCase") {
	forEach( PMFDeleteCaseFunction, yesAdd);
    } else if (functionName == "PMFCancelCase") {
	forEach( PMFCancelCaseFunction, yesAdd);
    } else if (functionName == "PMFAddInputDocument") {
	forEach( PMFAddInputDocumentFunction, yesAdd);
    } else if (functionName == "PMFAddCaseNote") {
	forEach( PMFAddCaseNoteFunction, yesAdd);
    } else if (functionName == "PMFGetCaseNotes") {
	forEach( PMFGetCaseNotesFunction, yesAdd);
    } else if (functionName == "PMFNewCaseImpersonate") {
        forEach( PMFNewCaseImpersonateFunction, yesAdd);
    } else if (functionName.trim() == "") {
	forEach (phpKeywords, yesAdd);
        forEach (keywords, yesAdd);
    } else if (functionName == "=") {
        forEach (phpPMFunctions, yesAdd);
    } else {
        for (index = 0; index < phpKeywords.length; index++) {
	        if ( phpKeywords[index].indexOf(functionName) == 0 ) {
		        found.push(phpKeywords[index]);
	        }
        } 
        forEach(keywords, maybeAdd);
    }
    return found;
  }
})();
