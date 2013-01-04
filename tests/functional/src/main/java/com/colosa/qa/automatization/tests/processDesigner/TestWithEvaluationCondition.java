package com.colosa.qa.automatization.tests.processDesigner;

import org.junit.Assert;
import org.junit.AfterClass;
import org.junit.Test;

import com.colosa.qa.automatization.pages.*;
import com.colosa.qa.automatization.common.*;
import org.openqa.selenium.WebElement;

import java.io.FileNotFoundException;
import java.io.IOException;

public class TestWithEvaluationCondition{

	@Test
	public void createEvaluationConditionProcess() throws FileNotFoundException, IOException, Exception{

	Pages.Login().gotoUrl();
	Pages.Login().loginUser("admin", "admin", "workflow");
	Pages.Main().goDesigner();	
	Pages.ProcessList().newProcess("Test Process with Evaluation Condition " + new java.util.Date().toString(), "Test Process with Evaluation Condition");
	Assert.assertTrue(Pages.Designer().createTask());
	Pages.Designer().moveTask("Task 1", -100, -300);
	Assert.assertTrue(Pages.Designer().createTask());
	Pages.Designer().moveTask("Task 2", -300, -200);
	Assert.assertTrue(Pages.Designer().createTask());
	Pages.Designer().moveTask("Task 3", -400, -100);
	Assert.assertTrue(Pages.Designer().createTask());
	Pages.Designer().moveTask("Task 4", -200, -100);
	Assert.assertTrue(Pages.Designer().initialTask("Task 1"));	
	String[][] tasksListArray = {{"Task 2","@@aprobar == 0"}, {"End of process",""}};
	Assert.assertTrue(Pages.Designer().evaluation("Task 1", tasksListArray));
	String[][] tasksListArraySecond = {{"Task 3", "@@aprobar == 0"}, {"Task 4","@@aprobar == 1"}};
	Assert.assertTrue(Pages.Designer().evaluation("Task 2", tasksListArraySecond));
	Assert.assertTrue(Pages.Designer().endTask("Task 3"));
	Assert.assertTrue(Pages.Designer().endTask("Task 4"));

}

}