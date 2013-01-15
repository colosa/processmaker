package com.colosa.qa.automatization.tests.processDesigner;

import org.junit.Assert;
import org.junit.AfterClass;
import org.junit.Test;

import com.colosa.qa.automatization.pages.*;
import com.colosa.qa.automatization.common.*;
import org.openqa.selenium.WebElement;

import java.io.FileNotFoundException;
import java.io.IOException;

public class TestWithParallelCondition{

	@Test
	public void createProcess() throws FileNotFoundException, IOException, Exception{

		Pages.Login().gotoUrl();
		Pages.Login().loginUser("admin", "admin", "workflow");
		Pages.Main().goDesigner();		
		Pages.ProcessList().newProcess("Test Process with Parallel Condition" + new java.util.Date().toString(),"Test Process with Parallel Condition");
		Assert.assertTrue(Pages.Designer().createTask());
		Pages.Designer().moveTask("Task 1", -100, -300);
		Assert.assertTrue(Pages.Designer().createTask());
		Pages.Designer().moveTask("Task 2", -300, -150);
		Assert.assertTrue(Pages.Designer().createTask());
		Pages.Designer().moveTask("Task 3", 0, -150);
		Assert.assertTrue(Pages.Designer().createTask());
		Pages.Designer().moveTask("Task 4", 300, -150);
		Assert.assertTrue(Pages.Designer().createTask());
		Pages.Designer().moveTask("Task 5", -100, -80);
		Assert.assertTrue(Pages.Designer().initialTask("Task 1"));		
		String[][] tasksListArray = {{"Task 2",""}, {"Task 3",""}, {"Task 4",""}};
		Assert.assertTrue(Pages.Designer().parallelFork("Task 1", tasksListArray));
		Assert.assertTrue(Pages.Designer().parallelJoin("Task 2", "Task 5"));
		Assert.assertTrue(Pages.Designer().parallelJoin("Task 3", "Task 5"));
		Assert.assertTrue(Pages.Designer().parallelJoin("Task 4", "Task 5"));
		Assert.assertTrue(Pages.Designer().endTask("Task 5"));

	}

}