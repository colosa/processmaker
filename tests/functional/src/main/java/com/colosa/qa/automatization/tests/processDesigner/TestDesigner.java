package com.colosa.qa.automatization.tests.processDesigner;

import org.junit.Assert;
import org.junit.AfterClass;
import org.junit.Test;

import com.colosa.qa.automatization.pages.*;
import com.colosa.qa.automatization.common.*;
import org.openqa.selenium.WebElement;

import java.io.FileNotFoundException;
import java.io.IOException;

public class TestDesigner{

	@Test
	public void tasks() throws FileNotFoundException, IOException, Exception{

		Pages.Login().gotoUrl();
		Pages.Login().loginUser("admin", "admin", "workflow");
		Pages.Main().goDesigner();		
		Pages.ProcessList().openProcess("Test 1");			
		Pages.Designer().createTask();
		Pages.Designer().moveTask("Task 1", -100, -300);
		Pages.Designer().createTask();
		Pages.Designer().moveTask("Task 2", -300, -150);
		/*Pages.Designer().createTask();
		Pages.Designer().moveTask("Task 3", -200, -150);
		Pages.Designer().createTask();
		Pages.Designer().moveTask("Task 4", -100, -150);
		Pages.Designer().createTask();
		Pages.Designer().moveTask("Task 5", 0, -150);*/
		Pages.Designer().initialTask("Task 1");		
		String[][] tasksListArray = {{"Task 2",""}};
		Pages.Designer().evaluation("Task 1", tasksListArray);					

	}
}