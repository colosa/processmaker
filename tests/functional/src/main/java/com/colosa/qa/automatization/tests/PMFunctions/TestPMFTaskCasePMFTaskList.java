package com.colosa.qa.automatization.tests.PMFunctions;

import org.junit.Assert;
import org.junit.AfterClass;
import org.junit.Test;

import com.colosa.qa.automatization.pages.*;
import com.colosa.qa.automatization.common.*;
import org.openqa.selenium.WebElement;

import java.io.FileNotFoundException;
import java.io.IOException;

public class TestPMFTaskCasePMFTaskList{

	protected static int caseNum;

	@Test
	public void testPMFTaskCasePMFTaskList() throws FileNotFoundException, IOException, Exception{

		Pages.Login().gotoUrl();
		Pages.Login().loginUser("admin", "admin", "workflow");
		Pages.Main().goHome();	
		caseNum = Pages.Home().startCase("PMFTaskCase - PMFTaskList (Init)");
		Pages.DynaformExecution().intoDynaform();
		Pages.DynaformExecution().setFieldValue("name", "Charles Puyol");
		Pages.DynaformExecution().setFieldValue("amount", "3000");
		Pages.DynaformExecution().setFieldValue("send", "");
		Pages.DynaformExecution().setFieldValue("TASKS][2][USR_UID", "Swan, William");
	    Pages.InputDocProcess().continuebtn();
		Pages.DynaformExecution().outDynaform();
		Pages.Main().logout();
		//Open report task for check
		Pages.Login().gotoUrl();
		Pages.Login().loginUser("admin", "admin", "workflow");				
		Pages.Main().goHome();	
    	Pages.Home().gotoInbox();
		Assert.assertTrue("The case does not exist in Inbox", Pages.Home().existCase(caseNum));
		Pages.Home().openCase(caseNum);
		Pages.DynaformExecution().intoDynaform();
		int numListCases = Integer.parseInt(Value.getValue(FieldKeyType.ID, "form[longTasksCases]"));
		for(int i=1; i<numListCases; i++){
			//Assert.assertEquals(Value.getValue(FieldKeyType.ID, "form[taskList]["+ i + "][guid]"), Value.getValue(FieldKeyType.ID, "form[tasksQuery][" + i + "][TAS_UID]"));	
        }
		Pages.DynaformExecution().setFieldValue("continue", "");
		int numTaskList = Integer.parseInt(Value.getValue(FieldKeyType.ID, "form[longTaskList]"));
		for(int i=1; i<numTaskList; i++){
			Assert.assertEquals(Value.getValue(FieldKeyType.ID, "form[userTaskList][" + i + "][guid]"), Value.getValue(FieldKeyType.ID, "form[gridTaskListQuery][" + i + "][TAS_UID]"));	
        }
		Pages.DynaformExecution().setFieldValue("continue", "");
	    Pages.InputDocProcess().continuebtn();
		Pages.DynaformExecution().outDynaform();
		Pages.Main().logout();
	}


}