package com.colosa.qa.automatization.tests.processDesigner;

import org.junit.Assert;
import org.junit.AfterClass;
import org.junit.Test;

import com.colosa.qa.automatization.pages.*;
import com.colosa.qa.automatization.common.*;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.interactions.Actions;
import org.openqa.selenium.By;

import java.io.FileNotFoundException;
import java.io.IOException;
public class TestTaskProperties{

	@Test
	public void testFunctional() throws FileNotFoundException, IOException, Exception{

		Actions action = new Actions(Browser.driver());

		Pages.Login().gotoUrl();
		Pages.Login().loginUser("admin", "admin", "workflow");
		Pages.Main().goDesigner();		
		Pages.ProcessList().openProcess("Test 1");
		TaskFieldData taskProp = new TaskFieldData();
		taskProp.taskName = "Task 1";
		taskProp.caseAssignedBy = "MANUAL";

		Pages.TaskProperties().properties(taskProp);

	}

}