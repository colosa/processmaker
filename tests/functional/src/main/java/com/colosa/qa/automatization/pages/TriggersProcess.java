/*
* TriggersProcess.java
* Created on Wensday 21, 2012
*
*/

/**
* @author Ademar Hurtado
*/
package com.colosa.qa.automatization.pages;

import java.util.List;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.By;
import org.openqa.selenium.interactions.Actions;
import org.openqa.selenium.JavascriptExecutor;
import com.colosa.qa.automatization.common.*;
import com.colosa.qa.automatization.common.extJs.*;
import java.io.FileNotFoundException;
import java.io.IOException;

public class TriggersProcess extends Page{

	public TriggersProcess() throws FileNotFoundException, IOException{
		
	}

	/**
	* @param triggerTitle string, triggerDescription string, triggerWebbot string
	*/
	public void newCustomTrigger(String triggerTitle, String triggerDescription, String triggerWebbot) throws FileNotFoundException, IOException, Exception{
		Browser.driver().switchTo().frame("frameMain");
		Browser.getElement("processDesigner.webElement.panelNewButton").click();
		Browser.getElement("triggers.webElement.newCustomTrigger").click();
		Browser.getElement("triggers.webElemet.title").sendKeys(triggerTitle);
		Browser.getElement("triggers.webElemet.description").sendKeys(triggerDescription);
		Browser.getElement("triggers.webElemet.webbot").sendKeys(triggerWebbot);
		Browser.getElement("triggers.webElemet.save").click();
	}

	/**
	* @param arg Array of arguments. The order is: Template name, Trigger Title, Trigger description... 
	* The arguments must be in order from top to down. 
	*/
	public void newPMFTrigger(String... args) throws FileNotFoundException, IOException, Exception{
		Browser.driver().switchTo().frame("frameMain");
		Browser.getElement("processDesigner.webElement.panelNewButton").click();
		Browser.getElement("triggers.webElement.ProcessmakerFunctions.OpenTree").click();
		Browser.getElement("triggers.webElement." + args[0] + ".New").click();
		Browser.getElement("triggers.webElement.ProcessmakerFunctions.Title").sendKeys(args[1]);
		Browser.getElement("triggers.webElement.ProcessmakerFunctions.Description").sendKeys(args[2]);

		if((args[0].equals("PMFAssignUserToGroup")|| args[0].equals("WSAssignUserToGroup")) && args.length == 6){
			Browser.getElement("triggers.webElement.PMFAssignUserToGroup.UserId").sendKeys(args[3]);
			Browser.getElement("triggers.webElement.PMFAssignUserToGroup.GroupId").sendKeys(args[4]);
		}
		else if((args[0].equals("PMFCancelCase" ) || args[0].equals("WSCancelCase")) && args.length == 7){
			Browser.getElement("triggers.webElement.PMFCancelCase.CaseId").sendKeys(args[3]);
			Browser.getElement("triggers.webElement.PMFCancelCase.DelegationIndex").sendKeys(args[4]);
			Browser.getElement("triggers.webElement.PMFCancelCase.UserUid").sendKeys(args[5]);
		}
		else if(args[0].equals("PMFCaseList") && args.length == 5){
			Browser.getElement("triggers.webElement.CaseList.UserId").sendKeys(args[3]);
		}
		else if((args[0].equals("PMFCreateUser") || args[0].equals("WSCreateUser")) && args.length == 11){
			Browser.getElement("triggers.webElement.PMFCreateUser.UserId").sendKeys(args[3]);
			Browser.getElement("triggers.webElement.PMFCreateUser.Password").sendKeys(args[4]);
			Browser.getElement("triggers.webElement.PMFCreateUser.FirstName").sendKeys(args[5]);
			Browser.getElement("triggers.webElement.PMFCreateUser.LastName").sendKeys(args[6]);			
			Browser.getElement("triggers.webElement.PMFCreateUser.Email").sendKeys(args[7]);
			Browser.getElement("triggers.webElement.PMFCreateUser.Role").sendKeys(args[8]);
			Browser.getElement("triggers.webElement.PMFCreateUser.DueDate").sendKeys(args[9]);
			Browser.getElement("triggers.webElement.PMFCreateUser.Status").sendKeys(args[10]);						
		}	
		else if((args[0].equals("PMFDeleteCase") || args[0].equals("WSDeleteCase")) && args.length == 5){
			Browser.getElement("triggers.webElement.PMFDeleteCase.CaseUid").sendKeys(args[3]);
		}
		else if((args[0].equals("PMFDerivateCase") && args.length == 8) || (args[0].equals("WSDerivateCase") && args.length == 6) ) {
			Browser.getElement("triggers.webElement.PMFDerivateCase.CaseId").sendKeys(args[3]);
			Browser.getElement("triggers.webElement.PMFDerivateCase.DelegationIndex").sendKeys(args[4]);
			if(args[0].equals("PMFDerivateCase")){
				Browser.getElement("triggers.webElement.PMFDerivateCase.Trigger").sendKeys(args[5]);
				Browser.getElement("triggers.webElement.PMFDerivateCase.UserId").sendKeys(args[6]);			
			}
		}
		else if(args[0].equals("PMFGenerateOutputDocument") && args.length == 5){
			Browser.getElement("triggers.webElement.PMFGenerateOutputDocument.OutputID").sendKeys(args[3]);
		}
		else if(args[0].equals("PMFGetCaseNotes") && args.length == 7){
			Browser.getElement("triggers.webElement.PMFGetCaseNotes.ApplicationID").sendKeys(args[3]);
			Browser.getElement("triggers.webElement.PMFGetCaseNotes.UserUid").sendKeys(args[5]);
			Browser.getElement("triggers.webElement.PMFGetCaseNotes.Type").sendKeys(args[4]);			
		}
		else if(args[0].equals("PMFGetNextAssignedUser") && args.length == 6){
			Browser.getElement("triggers.webElement.PMFGetNextAssignedUser.Application").sendKeys(args[3]);
			Browser.getElement("triggers.webElement.PMFGetNextAssignedUser.Task").sendKeys(args[4]);			
		}
		else if(args[0].equals("PMFGetUserEmailAddress") && args.length == 7){
			Browser.getElement("triggers.webElement.PMFGetUserEmailAddress.UserId").sendKeys(args[3]);
			Browser.getElement("triggers.webElement.PMFGetUserEmailAddress.AppUid").sendKeys(args[4]);
			Browser.getElement("triggers.webElement.PMFGetUserEmailAddress.Prefix").sendKeys(args[5]);			
		}
		else if((args[0].equals("PMFGroupList") || args[0].equals("PMFProcessList") || args[0].equals("PMFRoleList") || args[0].equals("PMFRoleList")  
				|| args[0].equals("PMFUserList") || args[0].equals("WSCaseList") || args[0].equals("WSGetSession") || args[0].equals("WSGroupList") 
				 || args[0].equals("WSProcessList") || args[0].equals("WSRoleList") || args[0].equals("WSTaskList")  | args[0].equals("WSUserList")) && args.length == 4){
			//Here add more arguments
		}
		else if(args[0].equals("PMFNewCase") && args.length == 8){
			Browser.getElement("triggers.webElement.PMFNewCase.ProcessId").sendKeys(args[3]);
			Browser.getElement("triggers.webElement.PMFNewCase.UserId").sendKeys(args[4]);
			Browser.getElement("triggers.webElement.PMFNewCase.TaskId").sendKeys(args[5]);
			Browser.getElement("triggers.webElement.PMFNewCase.Variables").sendKeys(args[6]);			
		}
		else if(args[0].equals("PMFNewCaseImpersonate") && args.length == 7){
			Browser.getElement("triggers.webElement.PMFNewCaseImpersonate.ProcessId").sendKeys(args[3]);
			Browser.getElement("triggers.webElement.PMFNewCaseImpersonate.UserId").sendKeys(args[4]);
			Browser.getElement("triggers.webElement.PMFNewCaseImpersonate.Variables").sendKeys(args[5]);			
		}
		else if((args[0].equals("PMFPauseCase") || args[0].equals("WSPauseCase")) && args.length == 8){
			Browser.getElement("triggers.webElement.PMFPauseCase.CaseUid").sendKeys(args[3]);
			Browser.getElement("triggers.webElement.PMFPauseCase.DelIndex").sendKeys(args[4]);
			Browser.getElement("triggers.webElement.PMFPauseCase.UserUid").sendKeys(args[5]);	
			Browser.getElement("triggers.webElement.PMFPauseCase.UnpauseDate").sendKeys(args[6]);		
		}
		else if(args[0].equals("PMFRedirectToStep") && args.length == 8){
			Browser.getElement("triggers.webElement.PMFRedirectToStep.SApplicationUID").sendKeys(args[3]);
			Browser.getElement("triggers.webElement.PMFRedirectToStep.IDelegation").sendKeys(args[4]);
			Browser.getElement("triggers.webElement.PMFRedirectToStep.SStepType").sendKeys(args[5]);	
			Browser.getElement("triggers.webElement.PMFRedirectToStep.SStepUid").sendKeys(args[6]);		
		}
		else if(args[0].equals("PMFSendMessage") && args.length == 11){
			Browser.getElement("triggers.webElement.PMFSendMessage.CaseId").sendKeys(args[3]);
			Browser.getElement("triggers.webElement.PMFSendMessage.SFrom").sendKeys(args[4]);
			Browser.getElement("triggers.webElement.PMFSendMessage.STo").sendKeys(args[5]);
			Browser.getElement("triggers.webElement.PMFSendMessage.SCc").sendKeys(args[6]);			
			Browser.getElement("triggers.webElement.PMFSendMessage.SBcc").sendKeys(args[7]);
			Browser.getElement("triggers.webElement.PMFSendMessage.SSubject").sendKeys(args[8]);
			Browser.getElement("triggers.webElement.PMFSendMessage.STemplate").sendKeys(args[9]);
			Browser.getElement("triggers.webElement.PMFSendMessage.AFields").sendKeys(args[10]);	
			Browser.getElement("triggers.webElement.PMFSendMessage.AAttachment").sendKeys(args[11]);						
		}	
		else if(args[0].equals("PMFSendVariables") && args.length == 6){
			Browser.getElement("triggers.webElement.PMFSendVariables.CaseId").sendKeys(args[3]);
			Browser.getElement("triggers.webElement.PMFSendVariables.Variables").sendKeys(args[4]);
		}
		else if(args[0].equals("PMFTaskCase") && args.length == 5){
			Browser.getElement("triggers.webElement.PMFTaskCase.CaseId").sendKeys(args[3]);
		}
		else if((args[0].equals("PMFTaskList") || args[0].equals("WSTaskCase")) && args.length == 5){
			Browser.getElement("triggers.webElement.PMFTaskList.Userid").sendKeys(args[3]);
		}
		else if((args[0].equals("PMFUnpauseCase") || args[0].equals("WSUnpauseCase")) && args.length == 7){
			Browser.getElement("triggers.webElement.PMFUnpauseCase.CaseUid").sendKeys(args[3]);
			Browser.getElement("triggers.webElement.PMFUnpauseCase.DelIndex").sendKeys(args[4]);
			Browser.getElement("triggers.webElement.PMFUnpauseCase.UserUid").sendKeys(args[5]);
		}
		else if((args[0].equals("PMFUpdateUser") || args[0].equals("WSUpdateUser")) && args.length == 12){
			Browser.getElement("triggers.webElement.PMFUpdateUser.UserUid").sendKeys(args[3]);
			Browser.getElement("triggers.webElement.PMFUpdateUser.UserName").sendKeys(args[4]);
			Browser.getElement("triggers.webElement.PMFUpdateUser.FirstName").sendKeys(args[5]);
			Browser.getElement("triggers.webElement.PMFUpdateUser.LastName").sendKeys(args[6]);			
			Browser.getElement("triggers.webElement.PMFUpdateUser.Email").sendKeys(args[7]);
			Browser.getElement("triggers.webElement.PMFUpdateUser.DueDate").sendKeys(args[8]);
			Browser.getElement("triggers.webElement.PMFUpdateUser.Status").sendKeys(args[9]);
			Browser.getElement("triggers.webElement.PMFUpdateUser.Role").sendKeys(args[10]);	
			Browser.getElement("triggers.webElement.PMFUpdateUser.Password").sendKeys(args[11]);					
		}
		else if(args[0].equals("PMFgetLabelOption") && args.length == 8){
			Browser.getElement("triggers.webElement.PMFgetLabelOption.Process").sendKeys(args[3]);
			Browser.getElement("triggers.webElement.PMFgetLabelOption.DynaformID").sendKeys(args[4]);
			Browser.getElement("triggers.webElement.PMFgetLabelOption.FieldName").sendKeys(args[5]);
			Browser.getElement("triggers.webElement.PMFgetLabelOption.FieldSelected").sendKeys(args[6]);
		}
		else if(args[0].equals("WSLogin") && args.length == 7){
			Browser.getElement("triggers.webElement.WSLogin.User").sendKeys(args[3]);
			Browser.getElement("triggers.webElement.WSLogin.Pass").sendKeys(args[4]);
			Browser.getElement("triggers.webElement.WSLogin.Endpoint").sendKeys(args[5]);
		}
		else if(args[0].equals("WSOpen") && args.length == 5){
			Browser.getElement("triggers.webElement.WSOpen.Force").sendKeys(args[3]);
		}
		else if((args[0].equals("UpperCase") || args[0].equals("LowerCase") || args[0].equals("Capitalize")) && args.length == 5){
			Browser.getElement("triggers.webElement.UpperCase.Force").sendKeys(args[3]);
		}
		else{
			throw new Exception("Invalid number of parameters or no exist Template");
		} 

  		Browser.getElement("triggers.webElement.ProcessmakerFunctions.Answer").sendKeys(args[args.length - 1]);             
		Browser.getElement("triggers.webElement.ProcessmakerFunctions.Save").click();
	}

	/**
	* @param arg Array of arguments. The order is: Template name, Trigger Title, Trigger description, Alfresco server, Alfresco user, Alfresco password... 
	* The arguments must be in order from top to down. 
	*/
	public void newAlfrescoDMTrigger(String... args) throws FileNotFoundException, IOException, Exception{
		Browser.driver().switchTo().frame("frameMain");		
		Browser.getElement("processDesigner.webElement.panelNewButton").click();
		Browser.getElement("triggers.webElement.AlfrescoDM.OpenTree").click();
		Browser.getElement("triggers.webElement." + args[0] + ".New").click();
		Browser.getElement("triggers.webElement.ProcessmakerFunctions.Title").sendKeys(args[1]);
		Browser.getElement("triggers.webElement.ProcessmakerFunctions.Description").sendKeys(args[2]);
		Browser.getElement("triggers.webElement.AlfrescoDM.AlfrescoServerUrl").sendKeys(args[3]);
		Browser.getElement("triggers.webElement.AlfrescoDM.User").sendKeys(args[4]);
		Browser.getElement("triggers.webElement.AlfrescoDM.Password").sendKeys(args[5]);

		if(args[0].equals("Checkin") && args.length == 9){
			Browser.getElement("triggers.webElement.Checkin.DocUid").sendKeys(args[6]);
			Browser.getElement("triggers.webElement.Checkin.Comments").sendKeys(args[7]);	
		}	
		else if((args[0].equals("Checkout") || args[0].equals("cancelCheckout")) && args.length == 8){
			Browser.getElement("triggers.webElement.Checkout.DocUid").sendKeys(args[6]);
		}
		else if(args[0].equals("CreateFolder") && args.length == 9){
			Browser.getElement("triggers.webElement.CreateFolder.ParentFolder").sendKeys(args[6]);
			Browser.getElement("triggers.webElement.CreateFolder.FolderName").sendKeys(args[7]);			
		}
		else if(args[0].equals("DeleteObject") && args.length == 8){
			Browser.getElement("triggers.webElement.DeleteObject.ObjetcId").sendKeys(args[6]);
		}
		else if(args[0].equals("DownloadDoc") && args.length == 10){
			Browser.getElement("triggers.webElement.DownloadDoc.ObjetcId").sendKeys(args[6]);
			Browser.getElement("triggers.webElement.DownloadDoc.ParentFolder").sendKeys(args[7]);
			Browser.getElement("triggers.webElement.DownloadDoc.FolderName").sendKeys(args[8]);						
		}
		else if(args[0].equals("GetFolderChildren") && args.length == 8){
			Browser.getElement("triggers.webElement.GetFolderChildren.FolderId").sendKeys(args[6]);
		}
		else if(args[0].equals("GetCheckedoutFiles") && args.length == 7){
			//Here add arguments for GetCheckedoutFiles. For now no needed
		}		
		else if(args[0].equals("UploadDoc") && args.length == 12){
			Browser.getElement("triggers.webElement.UploadDoc.FileSource").sendKeys(args[6]);
			Browser.getElement("triggers.webElement.UploadDoc.Title").sendKeys(args[7]);
			Browser.getElement("triggers.webElement.UploadDoc.Description").sendKeys(args[8]);	
			Browser.getElement("triggers.webElement.UploadDoc.DocType").sendKeys(args[9]);
			Browser.getElement("triggers.webElement.UploadDoc.Path").sendKeys(args[10]);								
		}																														
		else{
			throw new Exception("Invalid number of parameters or no exist Template");
		} 

  		Browser.getElement("triggers.webElement.ProcessmakerFunctions.Answer").sendKeys(args[args.length - 1]);             
		Browser.getElement("triggers.webElement.ProcessmakerFunctions.Save").click();
	}

	/**
	* @param arg Array of arguments. The order is: Template name, Trigger Title, Trigger description, Sharepint server, Sharepoint auth, Sharepoint DWS Name... 
	* The arguments must be in order from top to down. 
	*/
	public void newSharepointDWSTrigger(String... args) throws FileNotFoundException, IOException, Exception{
		Browser.driver().switchTo().frame("frameMain");		
		Browser.getElement("processDesigner.webElement.panelNewButton").click();
		Browser.getElement("triggers.webElement.SharepointDWS.OpenTree").click();
		Browser.getElement("triggers.webElement." + args[0] + ".New").click();
		Browser.getElement("triggers.webElement.ProcessmakerFunctions.Title").sendKeys(args[1]);
		Browser.getElement("triggers.webElement.ProcessmakerFunctions.Description").sendKeys(args[2]);
		Browser.getElement("triggers.webElement.SharepointDWS.SharepointServer").sendKeys(args[3]);
		Browser.getElement("triggers.webElement.SharepointDWS.Auth").sendKeys(args[4]);

		if(args[0].equals("CreateDWS") && args.length == 10){
			Browser.getElement("triggers.webElement.SharepointDWS.Name").sendKeys(args[5]);				
			Browser.getElement("triggers.webElement.CreateDWS.Users").sendKeys(args[6]);
			Browser.getElement("triggers.webElement.CreateDWS.Title").sendKeys(args[7]);
			Browser.getElement("triggers.webElement.CreateDWS.Documents").sendKeys(args[8]);				
		}	
		else if(args[0].equals("CreateFolderDWS") && args.length == 8){
			Browser.getElement("triggers.webElement.CreateFolderDWS.DwsName").sendKeys(args[5]);				
			Browser.getElement("triggers.webElement.CreateFolderDWS.DwsFolderName").sendKeys(args[6]);
		}
		else if(args[0].equals("DeleteDWS") && args.length == 7){
			Browser.getElement("triggers.webElement.DeleteDWS.DwsName").sendKeys(args[5]);	
		}	
		else if(args[0].equals("DeleteDWSAllDocumentVersion") && args.length == 8){
			Browser.getElement("triggers.webElement.DeleteDWSAllDocumentVersion.FileName").sendKeys(args[5]);
			Browser.getElement("triggers.webElement.DeleteDWSAllDocumentVersion.DwsName").sendKeys(args[6]);	
		}
		else if(args[0].equals("DeleteDWSDocumentVersion") && args.length == 9){
			Browser.getElement("triggers.webElement.DeleteDWSDocumentVersion.FileName").sendKeys(args[5]);
			Browser.getElement("triggers.webElement.DeleteDWSAllDocumentVersion.DwsName").sendKeys(args[6]);	
			Browser.getElement("triggers.webElement.DeleteDWSDocumentVersion.Version").sendKeys(args[7]);	
		}
		else if(args[0].equals("DeleteFolderDWS") && args.length == 8){
			Browser.getElement("triggers.webElement.CreateFolderDWS.DwsName").sendKeys(args[5]);
			Browser.getElement("triggers.webElement.DeleteFolderDWS.FolderName").sendKeys(args[6]);	
		}
		else if(args[0].equals("DownloadDocumentDWS") && args.length == 9){
			Browser.getElement("triggers.webElement.CreateFolderDWS.DwsName").sendKeys(args[5]);
			Browser.getElement("triggers.webElement.DownloadDocumentDWS.FileName").sendKeys(args[6]);	
			Browser.getElement("triggers.webElement.DownloadDocumentDWS.FileLocation").sendKeys(args[7]);
		}
		else if(args[0].equals("GetDWSData") && args.length == 9){
			Browser.getElement("triggers.webElement.GetDWSData.FileName").sendKeys(args[5]);
			Browser.getElement("triggers.webElement.GetDWSData.Dwsname").sendKeys(args[6]);	
			Browser.getElement("triggers.webElement.GetDWSData.LastUpdate").sendKeys(args[7]);
		}
		else if(args[0].equals("GetDWSDocumentVersions") && args.length == 8){
			Browser.getElement("triggers.webElement.GetDWSDocumentVersions.NewFileName").sendKeys(args[5]);
			Browser.getElement("triggers.webElement.GetDWSDocumentVersions.Dwsname").sendKeys(args[6]);	
		}
		else if(args[0].equals("GetDWSFolderItems") && args.length == 8){
			Browser.getElement("triggers.webElement.GetDWSFolderItems.Dwsname").sendKeys(args[5]);
			Browser.getElement("triggers.webElement.GetDWSFolderItems.FolderUrl").sendKeys(args[6]);	
		}
		else if(args[0].equals("GetDWSMetaData") && args.length == 9){
			Browser.getElement("triggers.webElement.GetDWSMetaData.NewFileName").sendKeys(args[5]);
			Browser.getElement("triggers.webElement.GetDWSMetaData.Dwsname").sendKeys(args[6]);	
			Browser.getElement("triggers.webElement.GetDWSMetaData.Id").sendKeys(args[7]);	
		}
		else if(args[0].equals("UploadDocumentDWS") && args.length == 10){
			Browser.getElement("triggers.webElement.UploadDocumentDWS.Dwsname").sendKeys(args[5]);
			Browser.getElement("triggers.webElement.UploadDocumentDWS.FolderName").sendKeys(args[6]);	
			Browser.getElement("triggers.webElement.UploadDocumentDWS.SourceUrl").sendKeys(args[7]);	
			Browser.getElement("triggers.webElement.UploadDocumentDWS.Filename").sendKeys(args[8]);
		}
		else{
			throw new Exception("Invalid number of parameters or no exist Template");
		} 

  		Browser.getElement("triggers.webElement.ProcessmakerFunctions.Answer").sendKeys(args[args.length - 1]);             
		Browser.getElement("triggers.webElement.ProcessmakerFunctions.Save").click();
	}

	/**
	* @param arg Array of arguments. The order is: Template name, Trigger Title, Trigger description, Sugar soap, Sugar user, Sugar Password... 
	* The arguments must be in order from top to down. 
	*/
	public void newSugarCRMTrigger(String... args) throws FileNotFoundException, IOException, Exception{
		Browser.driver().switchTo().frame("frameMain");		
		Browser.getElement("processDesigner.webElement.panelNewButton").click();
		Browser.getElement("triggers.webElement.SugarCRM.OpenTree").click();
		Browser.getElement("triggers.webElement." + args[0] + ".New").click();
		Browser.getElement("triggers.webElement.ProcessmakerFunctions.Title").sendKeys(args[1]);
		Browser.getElement("triggers.webElement.ProcessmakerFunctions.Description").sendKeys(args[2]);
		Browser.getElement("triggers.webElement.SugarCRM.SugarSoap").sendKeys(args[3]);
		Browser.getElement("triggers.webElement.SugarCRM.User").sendKeys(args[4]);
		Browser.getElement("triggers.webElement.SugarCRM.Password").sendKeys(args[5]);		

		if(args[0].equals("CreateSugarAccount") && args.length == 9){
			Browser.getElement("triggers.webElement.CreateSugarAccount.Name").sendKeys(args[6]);
		}	
		else if((args[0].equals("CreateSugarAccount") || args[0].equals("CreateSugarLeads")) && args.length == 14){
			Browser.getElement("triggers.webElement.CreateSugarContact.First_name").sendKeys(args[6]);
			Browser.getElement("triggers.webElement.CreateSugarContact.Last_name").sendKeys(args[7]);
			Browser.getElement("triggers.webElement.CreateSugarContact.Email").sendKeys(args[8]);
			Browser.getElement("triggers.webElement.CreateSugarContact.Title").sendKeys(args[9]);
			Browser.getElement("triggers.webElement.CreateSugarContact.Phone").sendKeys(args[10]);
			Browser.getElement("triggers.webElement.CreateSugarContact.Account_id").sendKeys(args[11]);
		}
		else if(args[0].equals("CreateSugarOpportunity") && args.length == 13){
			Browser.getElement("triggers.webElement.CreateSugarOpportunity.Name").sendKeys(args[6]);
			Browser.getElement("triggers.webElement.CreateSugarOpportunity.Account_id").sendKeys(args[7]);
			Browser.getElement("triggers.webElement.CreateSugarOpportunity.Amount").sendKeys(args[8]);
			Browser.getElement("triggers.webElement.CreateSugarOpportunity.Date_closed").sendKeys(args[9]);
			Browser.getElement("triggers.webElement.CreateSugarOpportunity.Sales_stage").sendKeys(args[10]);
		}
		else if((args[0].equals("GetSugarAccount") || args[0].equals("GetSugarCalls")  || args[0].equals("GetSugarContacts") || args[0].equals("GetSugarLeads") || args[0].equals("GetSugarOpportunities")) && args.length == 13){
			Browser.getElement("triggers.webElement.GetSugarAccount.Query").sendKeys(args[6]);
			Browser.getElement("triggers.webElement.GetSugarAccount.OrderBy").sendKeys(args[7]);
			Browser.getElement("triggers.webElement.GetSugarAccount.SelectedFields").sendKeys(args[8]);
			Browser.getElement("triggers.webElement.GetSugarAccount.MaxResults").sendKeys(args[9]);
		}
		else if(args[0].equals("GetSugarEntries") && args.length == 14){
			Browser.getElement("triggers.webElement.GetSugarEntries.Module").sendKeys(args[6]);
			Browser.getElement("triggers.webElement.GetSugarEntries.Query").sendKeys(args[7]);
			Browser.getElement("triggers.webElement.GetSugarEntries.OrderBy").sendKeys(args[8]);
			Browser.getElement("triggers.webElement.GetSugarEntries.SelectedFields").sendKeys(args[9]);
			Browser.getElement("triggers.webElement.GetSugarEntries.MaxResults").sendKeys(args[10]);
		}
		else if(args[0].equals("GetSugarEntry") && args.length == 13){
			Browser.getElement("triggers.webElement.GetSugarEntry.Module").sendKeys(args[6]);
			Browser.getElement("triggers.webElement.GetSugarEntry.Id").sendKeys(args[7]);
			Browser.getElement("triggers.webElement.GetSugarEntry.SelectedFields").sendKeys(args[8]);
			Browser.getElement("triggers.webElement.GetSugarEntry.LinkNameToFieldsArray").sendKeys(args[9]);
		}
		else{
			throw new Exception("Invalid number of parameters or no exist Template");
		} 
  		Browser.getElement("triggers.webElement.SugarCRM.ResultType").sendKeys(args[args.length - 2]); 
  		Browser.getElement("triggers.webElement.ProcessmakerFunctions.Answer").sendKeys(args[args.length - 1]);             
		Browser.getElement("triggers.webElement.ProcessmakerFunctions.Save").click();
	}

	/**
	* @param arg Array of arguments. The order is: Template name, Trigger Title, Trigger description...
	* The arguments must be in order from top to down. 	
	*/
	public void newTalendTrigger(String... args) throws FileNotFoundException, IOException, Exception{
		Browser.driver().switchTo().frame("frameMain");		
		Browser.getElement("processDesigner.webElement.panelNewButton").click();
		Browser.getElement("triggers.webElement.Talend.OpenTree").click();
		Browser.getElement("triggers.webElement." + args[0] + ".New").click();
		Browser.getElement("triggers.webElement.ProcessmakerFunctions.Title").sendKeys(args[1]);
		Browser.getElement("triggers.webElement.ProcessmakerFunctions.Description").sendKeys(args[2]);

		if(args[0].equals("ExecuteTalendWebservice") && args.length == 7){
			Browser.getElement("triggers.webElement.ExecuteTalendWebservice.Wsdl").sendKeys(args[3]);
			Browser.getElement("triggers.webElement.ExecuteTalendWebservice.Params").sendKeys(args[4]);
			Browser.getElement("triggers.webElement.ExecuteTalendWebservice.Message").sendKeys(args[5]);			
		}	
		else{
			throw new Exception("Invalid number of parameters or no exist Template");
		} 

  		Browser.getElement("triggers.webElement.ProcessmakerFunctions.Answer").sendKeys(args[args.length - 1]);             
		Browser.getElement("triggers.webElement.ProcessmakerFunctions.Save").click();
	}

	/**
	* @param arg Array of arguments. The order is: Template name, Trigger Title, Trigger description, Zimbra server, Zimbra user, Zimbra Auth... 
	* The arguments must be in order from top to down. 
	*/
	public void newZimbraTrigger(String... args) throws FileNotFoundException, IOException, Exception{
		Browser.driver().switchTo().frame("frameMain");		
		Browser.getElement("processDesigner.webElement.panelNewButton").click();
		Browser.getElement("triggers.webElement.Zimbra.OpenTree").click();
		Browser.getElement("triggers.webElement." + args[0] + ".New").click();
		Browser.getElement("triggers.webElement.ProcessmakerFunctions.Title").sendKeys(args[1]);
		Browser.getElement("triggers.webElement.ProcessmakerFunctions.Description").sendKeys(args[2]);
		Browser.getElement("triggers.webElement.Zimbra.ServerUrl").sendKeys(args[3]);
		Browser.getElement("triggers.webElement.Zimbra.Username").sendKeys(args[4]);
		Browser.getElement("triggers.webElement.Zimbra.PreAuthKey").sendKeys(args[5]);		

		if(args[0].equals("CreateZimbraFolder") && args.length == 9){
			Browser.getElement("triggers.webElement.CreateZimbraFolder.FolderName").sendKeys(args[6]);
			Browser.getElement("triggers.webElement.CreateZimbraFolder.ColorFolder").sendKeys(args[7]);
		}	
		else if(args[0].equals("CreateZimbraAppointment") && args.length == 26){
			Browser.getElement("triggers.webElement.CreateZimbraAppointment.Subject").sendKeys(args[6]);
			Browser.getElement("triggers.webElement.CreateZimbraAppointment.AppointmentName").sendKeys(args[7]);
			Browser.getElement("triggers.webElement.CreateZimbraAppointment.FriendlyName").sendKeys(args[8]);
			Browser.getElement("triggers.webElement.CreateZimbraAppointment.UserEmail").sendKeys(args[9]);
			Browser.getElement("triggers.webElement.CreateZimbraAppointment.DomainName").sendKeys(args[10]);
			Browser.getElement("triggers.webElement.CreateZimbraAppointment.Schedule").sendKeys(args[11]);
			Browser.getElement("triggers.webElement.CreateZimbraAppointment.Cutype").sendKeys(args[12]);
			Browser.getElement("triggers.webElement.CreateZimbraAppointment.AllDay").sendKeys(args[13]);
			Browser.getElement("triggers.webElement.CreateZimbraAppointment.IsOrg").sendKeys(args[14]);
			Browser.getElement("triggers.webElement.CreateZimbraAppointment.Rsvp").sendKeys(args[15]);
			Browser.getElement("triggers.webElement.CreateZimbraAppointment.AtFriendlyName").sendKeys(args[16]);
			Browser.getElement("triggers.webElement.CreateZimbraAppointment.Role").sendKeys(args[17]);
			Browser.getElement("triggers.webElement.CreateZimbraAppointment.Location").sendKeys(args[18]);
			Browser.getElement("triggers.webElement.CreateZimbraAppointment.Ptst").sendKeys(args[19]);
			Browser.getElement("triggers.webElement.CreateZimbraAppointment.StartDate").sendKeys(args[20]);
			Browser.getElement("triggers.webElement.CreateZimbraAppointment.EndDate").sendKeys(args[21]);
			Browser.getElement("triggers.webElement.CreateZimbraAppointment.Tz").sendKeys(args[22]);
		}
		else if(args[0].equals("CreateZimbraContacts") && args.length == 12){
			Browser.getElement("triggers.webElement.CreateZimbraContacts.FirstName").sendKeys(args[6]);
			Browser.getElement("triggers.webElement.CreateZimbraContacts.LastName").sendKeys(args[7]);
			Browser.getElement("triggers.webElement.CreateZimbraContacts.Email").sendKeys(args[8]);
			Browser.getElement("triggers.webElement.CreateZimbraContacts.OtherData").sendKeys(args[9]);
			Browser.getElement("triggers.webElement.CreateZimbraContacts.OtherDataValue").sendKeys(args[10]);
		}
		else if(args[0].equals("CreateZimbraTask") && args.length == 18){
			Browser.getElement("triggers.webElement.CreateZimbraTask.Subject").sendKeys(args[6]);
			Browser.getElement("triggers.webElement.CreateZimbraTask.TaskName").sendKeys(args[7]);
			Browser.getElement("triggers.webElement.CreateZimbraTask.FriendlyName").sendKeys(args[8]);
			Browser.getElement("triggers.webElement.CreateZimbraTask.UserEmail").sendKeys(args[9]);
			Browser.getElement("triggers.webElement.CreateZimbraTask.Priority").sendKeys(args[10]);
			Browser.getElement("triggers.webElement.CreateZimbraTask.AllDay").sendKeys(args[11]);
			Browser.getElement("triggers.webElement.CreateZimbraTask.Class").sendKeys(args[12]);
			Browser.getElement("triggers.webElement.CreateZimbraTask.Location").sendKeys(args[13]);
			Browser.getElement("triggers.webElement.CreateZimbraTask.DueDate").sendKeys(args[14]);
			Browser.getElement("triggers.webElement.CreateZimbraTask.Status").sendKeys(args[15]);
			Browser.getElement("triggers.webElement.CreateZimbraTask.Percent").sendKeys(args[16]);
		}
		else if((args[0].equals("GetZimbraAppointmentList") || args[0].equals("GetZimbraContactList") || args[0].equals("GetZimbraTaskList")) && args.length == 7){
			//Here add arguments. For now no needed
		}
		else if(args[0].equals("GetZimbraFolder") && args.length == 8){
			Browser.getElement("triggers.webElement.GetZimbraFolder.FolderName").sendKeys(args[6]);
		}
		else if(args[0].equals("UploadZimbraFile") && args.length == 9){
			Browser.getElement("triggers.webElement.UploadZimbraFile.FolderName").sendKeys(args[6]);
			Browser.getElement("triggers.webElement.UploadZimbraFile.FileLocation").sendKeys(args[7]);
		}
		else{
			throw new Exception("Invalid number of parameters or no exist Template");
		} 

  		Browser.getElement("triggers.webElement.ProcessmakerFunctions.Answer").sendKeys(args[args.length - 1]);             
		Browser.getElement("triggers.webElement.ProcessmakerFunctions.Save").click();
	}


	public void closePopup() throws Exception{
		Browser.driver().switchTo().defaultContent();
		Browser.driver().switchTo().frame("frameMain");
		WebElement btnClose = Browser.getElement("triggers.webElement.close");
		btnClose.click();
		Browser.driver().switchTo().defaultContent();
	}	

}

