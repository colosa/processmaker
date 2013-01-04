package com.colosa.qa.automatization.tests.dependentFields;

import org.junit.Assert;
import org.junit.AfterClass;
import org.junit.Test;

import com.colosa.qa.automatization.pages.*;
import com.colosa.qa.automatization.common.*;
import org.openqa.selenium.WebElement;

import java.io.FileNotFoundException;
import java.io.IOException;

public class TestDependentFields{

	protected static int caseNum;
	protected static String country = "Bolivia";
	protected static String state = "Chuquisaca";
	protected static String location = "Sucre";

	protected static String userName = "amy";
	protected static String completeName = "Amy Connelly";
	protected static String userDepartment = "Administrative Division";

	@Test
	public void testDependentFieldsCase() throws FileNotFoundException, IOException, Exception{

		Pages.Login().gotoUrl();
		Pages.Login().loginUser("admin", "admin", "workflow");
		Pages.Main().goHome();	
		caseNum = Pages.Home().startCase("TestDependentFields (Task 1)");
		
		Pages.DynaformExecution().intoDynaform();

		Pages.DynaformExecution().setFieldValue("country", country, FieldType.DROPDOWN);

		//sleep two seconds to wait ajax result
		Pages.DynaformExecution().sleep(1000);

		Assert.assertEquals(Pages.DynaformExecution().getDropdownFieldText("state"), state);
		Assert.assertEquals(Pages.DynaformExecution().getDropdownFieldText("location"), location);

		Pages.DynaformExecution().setFieldValue("suggest_country_label", country, FieldType.SUGGEST);
		Pages.DynaformExecution().sleep(1000);
		Assert.assertEquals(Pages.DynaformExecution().getDropdownFieldText("suggest_state"), state);
		//Assert.assertEquals(DynaformExecution.getFieldValue("suggest_location"), location);
		Assert.assertEquals(Pages.DynaformExecution().getDropdownFieldText("suggest_state2"), state);
		//Assert.assertEquals(DynaformExecution.getFieldValue("suggest_location2"), location);

		Pages.DynaformExecution().setFieldValue("username", userName, FieldType.TEXTBOX);
		//send tab to activate dependent fields
		Pages.DynaformExecution().sendTab("username");
		Pages.DynaformExecution().sleep(1000);
		/*String completeName = Pages.DynaformExecution().getFieldValue("userCompleteName");
		System.out.printf("CompleteName: %s \n", completeName); */

		Assert.assertEquals(Pages.DynaformExecution().getFieldValue("userCompleteName"), completeName);
		/*
		String department = Pages.DynaformExecution().getFieldValue("userDepartment");
		System.out.printf("User Department: %s \n", department);
		department = Pages.DynaformExecution().getFieldValue("userDepartment");
		System.out.printf("User Department: %s \n", department);		*/
		Assert.assertEquals(Pages.DynaformExecution().getFieldValue("userDepartment"), userDepartment);
		
		Pages.DynaformExecution().outDynaform();

		Pages.Main().logout();
	}


}