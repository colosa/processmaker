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
	protected static String state1 = "La Paz";
	protected static String location1 = "Apolo";

	protected static String country2 = "Argentina";
	protected static String state2 = "Jujuy";
	protected static String location2 = "San Pedro";
	protected static String state22 = "Chubut";
	protected static String location22 = "Trelew";

	protected static String userName = "amy";
	protected static String completeName = "Amy Connelly";
	protected static String userDepartment = "Administrative Division";

	protected static String userName1 = "admin";
	protected static String completeName1 = "Administrator  ";
	protected static String userDepartment1 = "";
	@Test
	public void testDependentFieldsCase() throws FileNotFoundException, IOException, Exception{

		Pages.Login().gotoUrl();
		Pages.Login().loginUser("admin", "admin", "workflow");
		Pages.Main().goHome();	
		caseNum = Pages.Home().startCase("TestDependentFields (Task 1)");
		
		Pages.DynaformExecution().intoDynaform();

		//Pages.DynaformExecution().setFieldValue("country", country, FieldType.DROPDOWN);
		Pages.DynaformExecution().setFieldValue("country", country);

		//Pages.DynaformExecution().setGridFieldValue("gridname", numerofila, "country", country, FieldType.DROPDOWN);
		//sleep one second to wait ajax result
		Pages.DynaformExecution().sleep(1000);

		Assert.assertEquals(Pages.DynaformExecution().getFieldText("state"), state);
		Assert.assertEquals(Pages.DynaformExecution().getFieldText("location"), location);

		//Pages.DynaformExecution().setFieldValue("suggest_country", country, FieldType.SUGGEST);
		Pages.DynaformExecution().setFieldValue("suggest_country", country);

		Pages.DynaformExecution().sleep(1000);
		Assert.assertEquals(Pages.DynaformExecution().getDropdownFieldText("suggest_state"), state);
		//Assert.assertEquals(Pages.DynaformExecution().getDropdownFieldText("suggest_location"), location);
		Assert.assertEquals(Pages.DynaformExecution().getDropdownFieldText("suggest_state2"), state);
		//Assert.assertEquals(Pages.DynaformExecution().getDropdownFieldText("suggest_location2"), location);

		//Pages.DynaformExecution().setFieldValue("username", userName, FieldType.TEXTBOX);
		Pages.DynaformExecution().setFieldValue("username", userName);
		//send tab to activate dependent fields
		Pages.DynaformExecution().sendTab("username");
		Pages.DynaformExecution().sleep(1000);

		Assert.assertEquals(Pages.DynaformExecution().getFieldValue("userCompleteName"), completeName);
		Assert.assertEquals(Pages.DynaformExecution().getFieldValue("userDepartment"), userDepartment);
		
		//change values in fields
		//Pages.DynaformExecution().setFieldValue("state", state1, FieldType.DROPDOWN);
		Pages.DynaformExecution().setFieldValue("state", state1);
		Pages.DynaformExecution().sleep(1000);
		Assert.assertEquals(Pages.DynaformExecution().getDropdownFieldText("location"), location1);

		//change fields in suggest
		//Pages.DynaformExecution().setFieldValue("suggest_country", country2, FieldType.SUGGEST);
		Pages.DynaformExecution().setFieldValue("suggest_country", country2);
		Pages.DynaformExecution().sleep(1000);
		//Pages.DynaformExecution().setFieldValue("suggest_state", state2, FieldType.DROPDOWN);
		Pages.DynaformExecution().setFieldValue("suggest_state", state2);
		Pages.DynaformExecution().sleep(1000);
		//Pages.DynaformExecution().setFieldValue("suggest_location", location2, FieldType.DROPDOWN);
		Pages.DynaformExecution().setFieldValue("suggest_location", location2);
		Pages.DynaformExecution().sleep(1000);
		Assert.assertEquals(Pages.DynaformExecution().getDropdownFieldText("suggest_state"), state2);
		Assert.assertEquals(Pages.DynaformExecution().getDropdownFieldText("suggest_location"), location2);
		//Pages.DynaformExecution().setFieldValue("suggest_state2", state22, FieldType.DROPDOWN);
		Pages.DynaformExecution().setFieldValue("suggest_state2", state22);
		Pages.DynaformExecution().sleep(1000);
		//Pages.DynaformExecution().setFieldValue("suggest_location2", location22, FieldType.DROPDOWN);
		Pages.DynaformExecution().setFieldValue("suggest_location2", location22);
		Pages.DynaformExecution().sleep(1000);		
		Assert.assertEquals(Pages.DynaformExecution().getDropdownFieldText("suggest_state2"), state22);
		Assert.assertEquals(Pages.DynaformExecution().getDropdownFieldText("suggest_location2"), location22);

		//change fields in textbox
		//Pages.DynaformExecution().setFieldValue("username", userName1, FieldType.TEXTBOX);
		Pages.DynaformExecution().setFieldValue("username", userName1);
		//send tab to activate dependent fields
		Pages.DynaformExecution().sendTab("username");
		Pages.DynaformExecution().sleep(1000);
		Assert.assertEquals(Pages.DynaformExecution().getFieldValue("userCompleteName"), completeName1);
		Assert.assertEquals(Pages.DynaformExecution().getFieldValue("userDepartment"), userDepartment1);

		//test other dependent fields
		Pages.DynaformExecution().setFieldValue("userName2", userName1); //admin
		Assert.assertEquals(Pages.DynaformExecution().getFieldValue("textAreaField"), completeName1);
		int listBoxCount = Pages.DynaformExecution().getFieldCount("listBoxField");
		System.out.println("ListBox value:" + listBoxCount);
		//Assert.assertFalse(Pages.DynaformExecution().getFieldValue("listBoxField").equals("")); //at least one login
		String totalLogins = Pages.DynaformExecution().getFieldValue("hiddenField");
		System.out.println("Total logins:" + totalLogins);
		Assert.assertEquals(listBoxCount, Integer.parseInt(totalLogins));

		Pages.DynaformExecution().outDynaform();

		Pages.Main().logout();
	}


}