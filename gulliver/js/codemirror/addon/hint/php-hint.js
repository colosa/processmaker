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
  var arrayFunctions = [];
  
  var formatDate = "formatDate";
  var formatDateFunction = [formatDate+"($date,$format,$language);",formatDate+"($date,$format);"];
  arrayFunctions[formatDate] = formatDateFunction;
  
  var getCurrentDate = "getCurrentDate";
  var getCurrentDateFunction = [getCurrentDate+"()"];
  arrayFunctions[getCurrentDate] = getCurrentDateFunction;
  
  var getCurrentTime = "getCurrentTime";
  var getCurrentTimeFunction = [getCurrentTime+"()"];
  arrayFunctions[getCurrentTime] = getCurrentTimeFunction;
  
  var literalDate = "literalDate";
  var literalDateFunction = [literalDate+"($date,$Language)",literalDate+"($date)"];
  arrayFunctions[literalDate] = literalDateFunction;
  
  var capitalize = "capitalize";
  var capitalizeFunction = [capitalize+"($textToConvert)"];
  arrayFunctions[capitalize] = capitalizeFunction;
  
  var lowerCase = "lowerCase";
  var lowerCaseFunction = [lowerCase+"($textToConvert)"];
  arrayFunctions[lowerCase] = lowerCaseFunction;
  
  var upperCase = "upperCase";
  var upperCaseFunction = [upperCase+"($textToConvert)"];
  arrayFunctions[upperCase] = upperCaseFunction;
  
  var userInfo = "userInfo";
  var userInfoFunction = [userInfo+"($USER_ID)"];
  arrayFunctions[userInfo] = userInfoFunction;
  
  var executeQuery = "executeQuery";
  var executeQueryFunction = [executeQuery+"($sqlStatement,$DBConnectionUID)",executeQuery+"($sqlStatement)"];
  arrayFunctions[executeQuery] = executeQueryFunction;
  
  var orderGrid = "orderGrid";
  var orderGridFunction = ("orderGrid($gridName,$field,$criteria) orderGrid($gridName,$field)").split(SPACE);
  arrayFunctions[orderGrid] = orderGridFunction;
  
  var evaluateFunction = "evaluateFunction";
  var evaluateFunctionFunction = [evaluateFunction+"($gridName,$Expression)"];
  arrayFunctions[evaluateFunction] = evaluateFunctionFunction;
  
  var PMFTaskCase = "PMFTaskCase";
  var PMFTaskCaseFunction = [PMFTaskCase+"($caseId)"];
  arrayFunctions[PMFTaskCase] = PMFTaskCaseFunction;
  
  var PMFTaskList = "PMFTaskList";
  var PMFTaskListFunction = [PMFTaskList+"($userId)"];
  arrayFunctions[PMFTaskList] = PMFTaskListFunction;
  
  var PMFUserList = "PMFUserList";
  var PMFUserListFunction = [PMFUserList+"()"];
  arrayFunctions[PMFUserList] = PMFUserListFunction;
  
  var PMFGroupList = "PMFGroupList";
  var PMFGroupListFunction = [PMFGroupList+"()"];
  arrayFunctions[PMFGroupList] = PMFGroupListFunction;
  
  var PMFRoleList = "PMFRoleList";
  var PMFRoleListFunction = [PMFRoleList+"()"];
  arrayFunctions[PMFRoleList] = PMFRoleListFunction;
  
  var PMFCaseList = "PMFCaseList";
  var PMFCaseListFunction = [PMFCaseList+"($userId)",PMFCaseList+"()"];
  arrayFunctions[PMFCaseList] = PMFCaseListFunction;
  
  var PMFProcessList = "PMFProcessList";
  var PMFProcessListFunction = [PMFProcessList+"()"];
  arrayFunctions[PMFProcessList] = PMFProcessListFunction;
  
  var PMFSendVariables = "PMFSendVariables";
  var PMFSendVariablesFunction = [PMFSendVariables+"($caseId,$variables)"];
  arrayFunctions[PMFSendVariables] = PMFSendVariablesFunction;
  
  var PMFDerivateCase = "PMFDerivateCase";
  var PMFDerivateCaseFunction = [PMFDerivateCase+"($caseId,$delegation,$executeTriggersBeforeAssigment)",PMFDerivateCase+"($caseId,$delegation)"];
  arrayFunctions[PMFDerivateCase] = PMFDerivateCaseFunction;
  
  var PMFNewCaseImpersonate = "PMFNewCaseImpersonate";
  var PMFNewCaseImpersonateFunction = [PMFNewCaseImpersonate+"($processId,$userId,$variables)"];
  arrayFunctions[PMFNewCaseImpersonate] = PMFNewCaseImpersonateFunction;
  
  var PMFNewCase = "PMFNewCase";
  var PMFNewCaseFunction = [PMFNewCase+"($processId,$userId,$taskId,$variables)"];
  arrayFunctions[PMFNewCase] = PMFNewCaseFunction;
  
  var PMFPauseCase = "PMFPauseCase";
  var PMFPauseCaseFunction = [PMFPauseCase+"($caseUid,$delIndex,$userUid,$unpauseDate)",PMFPauseCase+"($caseUid,$delIndex,$userUid)"];
  arrayFunctions[PMFPauseCase] = PMFPauseCaseFunction;
  
  var PMFAssignUserToGroup = "PMFAssignUserToGroup";
  var PMFAssignUserToGroupFunction = [PMFAssignUserToGroup+"($userId,$groupId)"];
  arrayFunctions[PMFAssignUserToGroup] = PMFAssignUserToGroupFunction;
  
  var PMFCreateUser = "PMFCreateUser";
  var PMFCreateUserFunction = [PMFCreateUser+"($userId,$password,$firstname,$lastname,$email,$role)"];
  arrayFunctions[PMFCreateUser] = PMFCreateUserFunction;
  
  var PMFUpdateUser = "PMFUpdateUser";
  var PMFUpdateUserFunction = [PMFUpdateUser+"($userUid,$userName,$firstName,$lastName,$email,$dueDate,$status,$role,$password)"];
  arrayFunctions[PMFUpdateUser] = PMFUpdateUserFunction;
  
  var PMFInformationUser = "PMFInformationUser";
  var PMFInformationUserFunction = [PMFInformationUser+"($userUid)"];
  arrayFunctions[PMFInformationUser] = PMFInformationUserFunction;
  
  var generateCode = "generateCode";
  var generateCodeFunction = [generateCode+"($size,$type)"];
  arrayFunctions[generateCode] = generateCodeFunction;
  
  var setCaseTrackerCode = "setCaseTrackerCode";
  var setCaseTrackerCodeFunction = [setCaseTrackerCode+"($caseId,$code,$pin)"];
  arrayFunctions[setCaseTrackerCode] = setCaseTrackerCodeFunction;
  
  var jumping = "jumping";
  var jumpingFunction = [jumping+"($caseId,$delegation)"];
  arrayFunctions[jumping] = jumpingFunction;
   
  var PMFRedirectToStep = "PMFRedirectToStep";
  var PMFRedirectToStepFunction = [PMFRedirectToStep+"($caseId,$delegation,$stepType,$stepId)"];
  arrayFunctions[PMFRedirectToStep] = PMFRedirectToStepFunction;
  
  var pauseCase = "pauseCase";
  var pauseCaseFunction = [pauseCase+"($caseId,$delegation,$userId,$unpauseDate)",pauseCase+"($caseId,$delegation,$userId)"];
  arrayFunctions[pauseCase] = pauseCaseFunction;
  
  var PMFUnpauseCase = "PMFUnpauseCase";
  var PMFUnpauseCaseFunction = [PMFUnpauseCase+"($caseId,$delegation,$userId,$unpauseDate)",PMFUnpauseCase+"($caseId,$delegation,$userId)"];
  arrayFunctions[PMFUnpauseCase] = PMFUnpauseCaseFunction;

  var PMFSendMessage = "PMFSendMessage";
  var PMFSendMessageFunction = [PMFSendMessage+"($caseId,$from,$to,$cc,$bcc,$subject,$template,$fields,$attachments)",PMFSendMessage+"($caseId,$from,$to,$cc,$bcc,$subject,$template,$fields)",PMFSendMessage+"($caseId,$from,$to,$cc,$bcc,$subject,$template)"];
  arrayFunctions[PMFSendMessage] = PMFSendMessageFunction;
  
  var PMFgetLabelOption = "PMFgetLabelOption";
  var PMFgetLabelOptionFunction = [PMFgetLabelOption+"($processId,$dynaformId,$fieldName,$optionId)"];
  arrayFunctions[PMFgetLabelOption] = PMFgetLabelOptionFunction;
  
  var PMFGenerateOutputDocument = "PMFGenerateOutputDocument";
  var PMFGenerateOutputDocumentFunction = [PMFGenerateOutputDocument+"($outputID)"];
  arrayFunctions[PMFGenerateOutputDocument] = PMFGenerateOutputDocumentFunction;
  
  var PMFGetUserEmailAddress = "PMFGetUserEmailAddress";
  var PMFGetUserEmailAddressFunction = [PMFGetUserEmailAddress+"($id,$APP_UID,$prefix)",PMFGetUserEmailAddress+"($id,$APP_UID)",PMFGetUserEmailAddress+"($id)"];
  arrayFunctions[PMFGetUserEmailAddress] = PMFGetUserEmailAddressFunction;
  
  var PMFGetNextAssignedUser = "PMFGetNextAssignedUser";
  var PMFGetNextAssignedUserFunction = (PMFGetNextAssignedUser+"($application,$task)").split(SPACE);
  arrayFunctions[PMFGetNextAssignedUser] = PMFGetNextAssignedUserFunction;
  
  var PMFDeleteCase = "PMFDeleteCase";
  var PMFDeleteCaseFunction = ("PMFDeleteCase($caseId)").split(SPACE);
  arrayFunctions[PMFDeleteCase] = PMFDeleteCaseFunction;
  
  var PMFCancelCase = "PMFCancelCase";
  var PMFCancelCaseFunction = [PMFCancelCase+"($caseUid,$delIndex,$userUid)"];
  arrayFunctions[PMFCancelCase] = PMFCancelCaseFunction;
  
  var PMFAddInputDocument = "PMFAddInputDocument";
  var PMFAddInputDocumentFunction = [PMFAddInputDocument+"($inputDocumentUid,$appDocUid,$docVersion,$appDocType,$appDocComment,$inputDocumentAction,$caseUid,$delIndex,$taskUid,$userUid,$option,$file)",PMFAddInputDocument+"($inputDocumentUid,$appDocUid,$docVersion,$appDocType,$appDocComment,$inputDocumentAction,$caseUid,$delIndex,$taskUid,$userUid,$option)",PMFAddInputDocument+"($inputDocumentUid,$appDocUid,$docVersion,$appDocType,$appDocComment,$inputDocumentAction,$caseUid,$delIndex,$taskUid,$userUid)"];
  arrayFunctions[PMFAddInputDocument] = PMFAddInputDocumentFunction;
  
  var PMFAddCaseNote = "PMFAddCaseNote";
  var PMFAddCaseNoteFunction = [PMFAddCaseNote+"($caseUid,$processUid,$taskUid,$userUid,$note,$sendMail)"];
  arrayFunctions[PMFAddCaseNote] = PMFAddCaseNoteFunction;
  
  var PMFGetCaseNotes = "PMFGetCaseNotes";
  var PMFGetCaseNotesFunction = [PMFGetCaseNotes+"($applicationID,$type,$userUid);",PMFGetCaseNotes+"($applicationID,$type)",PMFGetCaseNotes+"($applicationID)"];
  arrayFunctions[PMFGetCaseNotes] = PMFGetCaseNotesFunction;
  
  var phpPMFunctions = [formatDate,getCurrentDate,getCurrentTime,literalDate,capitalize,lowerCase,upperCase,userInfo,executeQuery,orderGrid,
  evaluateFunction,PMFTaskCase,PMFTaskList,PMFUserList,PMFGroupList,PMFRoleList,PMFCaseList,PMFProcessList,PMFSendVariables,PMFDerivateCase,
  PMFNewCaseImpersonate,PMFNewCase,PMFPauseCase,PMFUnpauseCase,PMFAssignUserToGroup,PMFCreateUser,PMFUpdateUser,PMFInformationUser,        
  generateCode,setCaseTrackerCode,jumping,PMFRedirectToStep,pauseCase,PMFSendMessage,PMFgetLabelOption,PMFGenerateOutputDocument,   
  PMFGetUserEmailAddress,PMFGetNextAssignedUser,PMFDeleteCase,PMFCancelCase,PMFAddInputDocument,PMFAddCaseNote,PMFGetCaseNotes];

  var phpKeywords = ("break case catch continue default do else false for function " +
                  "if new return switch throw true try var while").split(SPACE);

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
    
    arrayFunction = arrayFunctions[functionName];
	
    if (arrayFunction != undefined) {
      forEach( arrayFunction, yesAdd);
    } else {
      if (functionName.trim() == "") {
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
    }
    return found;
  }
})();
