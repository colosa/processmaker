package com.colosa.qa.automatization.tests.triggersProcess;

import org.junit.Assert;
import org.junit.AfterClass;
import org.junit.Test;

import com.colosa.qa.automatization.pages.*;
import com.colosa.qa.automatization.common.*;

import java.io.FileNotFoundException;
import java.io.IOException;

public class TestTriggersProcess{


	@Test
	public void createTrigger() throws Exception{

		//Lpgin and create a process for test
		Pages.Login().gotoUrl();
		Pages.Login().loginUser("admin", "admin", "workflow");
		Pages.Main().goDesigner();
		Pages.ProcessList().newProcess("Test ProcessTriggers " + new java.util.Date().toString(), "Test process triggers");

		// Test custom triggers
		Pages.ProcessDesigner().openTriggers();
		Pages.TriggersProcess().newCustomTrigger("New custom trigger", "new custom test triggers", "custom code here");
		Pages.TriggersProcess().closePopup();
		
		//Test Process Maker Functions triggers 
		Pages.ProcessDesigner().openTriggers();		
		Pages.TriggersProcess().newPMFTrigger("PMFAssignUserToGroup","Test PMFAssignUserToGroup", "Test PMFAssignUserToGroup", "userid", "user group", "returnvalue");
		Pages.TriggersProcess().closePopup();

		//Test Alfresco DM triggers 
		Pages.ProcessDesigner().openTriggers();		
		Pages.TriggersProcess().newAlfrescoDMTrigger("Checkin","Test Checkin", "Test Checkin", "http://localhost:8080/alfresco", "Alfresco user", "Alfresco password",  "DocumentID", "Comments", "returnvalue");
		Pages.TriggersProcess().closePopup();	

		//Test Sharepoint DWS Triggers  
		Pages.ProcessDesigner().openTriggers();		
		Pages.TriggersProcess().newSharepointDWSTrigger("CreateDWS","Test CreateDWS", "Test CreateDWS", "http://server:port", "Sharepoint auth", "Name of DWS", "Relevent user", "Title DWS", "@@APPLICATION", "returnvalue");
		Pages.TriggersProcess().closePopup();

		//Test Sugar CRM Triggers   
		Pages.ProcessDesigner().openTriggers();		
		Pages.TriggersProcess().newSugarCRMTrigger("CreateSugarAccount","Test CreateSugarAccount", "Test CreateSugarAccount", "http://www.example.com/sugar/soap.php?wsdl", "Sugar user", "Sugar pass", "Account name", "array", "returnvalue");
		Pages.TriggersProcess().closePopup();

		//Test Talend ETL Integration Triggers
		Pages.ProcessDesigner().openTriggers();		   
		Pages.TriggersProcess().newTalendTrigger("ExecuteTalendWebservice","Test ExecuteTalendWebservice", "Test ExecuteTalendWebservice", "http://www.example.com/talend/soap.php?wsdl", "array(array(a,a1) array(b, b1))", "message", "returnvalue");
		Pages.TriggersProcess().closePopup();	

		//Test  Zimbra Triggers   
		Pages.ProcessDesigner().openTriggers();		
		Pages.TriggersProcess().newZimbraTrigger("CreateZimbraFolder","Test CreateZimbraFolder", "Test CreateZimbraFolder", "http://zimbra.server:port", "Zimbra user", "Zimbra key", "folder name", "Color name", "returnvalue");
		Pages.TriggersProcess().closePopup();					

	}

}