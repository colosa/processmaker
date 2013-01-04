package com.colosa.qa.automatization.tests.processDesigner;

import org.junit.Assert;
import org.junit.AfterClass;
import org.junit.Test;

import com.colosa.qa.automatization.pages.*;
import com.colosa.qa.automatization.common.*;
import org.openqa.selenium.WebElement;

import java.io.FileNotFoundException;
import java.io.IOException;

public class TestWithEvents{

	@Test
	public void createEvents() throws FileNotFoundException, IOException, Exception{

	Pages.Login().gotoUrl();
	Pages.Login().loginUser("admin", "admin", "workflow");
	Pages.Main().goDesigner();	
	Pages.ProcessList().newProcess("Test Process with Evaluation Condition " + new java.util.Date().toString(), "Test Process with Evaluation Condition");
	Assert.assertTrue(Pages.Designer().createTask());
	Pages.Designer().moveTask("Task 1", -100, -300);
	Assert.assertTrue(Pages.Designer().createTask());
	Pages.Designer().moveTask("Task 2", -300, -200);
	Assert.assertTrue(Pages.Designer().createTask());
	Pages.Designer().moveTask("Task 3", 100, -200);
	Assert.assertTrue(Pages.Designer().createTask());
	Pages.Designer().moveTask("Task 4", -400, -100);
	Assert.assertTrue(Pages.Designer().createTask());
	Pages.Designer().moveTask("Task 5", -200, -100);
	Assert.assertTrue(Pages.Designer().createTask());
	Pages.Designer().moveTask("Task 6", 100, -100);	
	Assert.assertTrue(Pages.Designer().initialTask("Task 1"));	
	String[][] tasksListArray = {{"Task 2", "@@aprobar == 0"}, {"Task 3", "@@aprobar == 1"}};
	Assert.assertTrue(Pages.Designer().selection("Task 1", tasksListArray));
	String[][] tasksListArraySecond = {{"Task 4", "@@aprobar == 0"}, {"Task 5", "@@aprobar == 1"}};
	Assert.assertTrue(Pages.Designer().selection("Task 2", tasksListArraySecond));
	Assert.assertTrue(Pages.Designer().sequential("Task 3", "Task 6"));	
	Assert.assertTrue(Pages.Designer().endTask("Task 4"));
	Assert.assertTrue(Pages.Designer().endTask("Task 5"));
	Assert.assertTrue(Pages.Designer().endTask("Task 6"));	
	String[] sendToListArray = {"sendto@colosa.com", "sendto2@colosa.com"};
	String[] bccListArray = {"bcc@colosa.com", "bcc2@colosa.com"};
	String[] blindListArray = {"blind@colosa.com", "blind2@colosa.com"};
	//Assert.assertTrue(Pages.Designer().addEventMessage("Test 1","Inactive", "Multiple Tasks", "Task 1", "Task 2", "1", "Days", "2", "After interval starts", "test subject",  sendToListArray,  bccListArray, blindListArray ));
	//Pages.TriggersProcess().closePopup();
	//Assert.assertTrue(Pages.Designer().addEventConditional("test 2","Inactive", "Multiple Tasks", "Task 1", "Task 2", "1", "Days", "2", "After interval starts", "Test 1" , "@@aprobar==1"));
	//Pages.TriggersProcess().closePopup();
	Assert.assertTrue(Pages.Designer().addEventTimer("test 2","Inactive", "Multiple Tasks", "Task 1", "Task 2", "1", "Days", "2", "After interval starts", "Test 1" ));
	Pages.TriggersProcess().closePopup();


}

}