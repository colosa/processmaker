package com.colosa.qa.automatization.tests.TestFunctionExecuteQuery;

import org.junit.Assert;
import org.junit.AfterClass;
import org.junit.Test;

import com.colosa.qa.automatization.pages.*;
import com.colosa.qa.automatization.common.*;
import org.openqa.selenium.WebElement;

import java.io.FileNotFoundException;
import java.io.IOException;

public class TestFunctionExecuteQueryMysql{

	@Test
	public void executeProcess() throws FileNotFoundException, IOException, Exception{

		//Open process
		Pages.Login().gotoUrl();
		Pages.Login().loginUser("admin", "admin", "workflow");
		Pages.Main().goHome();
		Pages.Home().gotoNewCase();	
		int caseNumber = Pages.Home().startCase("Execute query mysql (Task 1)");	
		Pages.InputDocProcess().openCaseFrame();
		FormFieldData[] fieldArray = new FormFieldData[2];
		fieldArray[0] = new FormFieldData();
		fieldArray[1] = new FormFieldData();
		fieldArray[0].fieldPath = "form[usern]";
		fieldArray[0].fieldFindType = FieldKeyType.ID;
		fieldArray[0].fieldType = FieldType.TEXTBOX;
		fieldArray[0].fieldValue = "admin";
		fieldArray[1].fieldPath = "form[send]";
		fieldArray[1].fieldFindType = FieldKeyType.ID;
		fieldArray[1].fieldType = FieldType.BUTTON;
		fieldArray[1].fieldValue = "";
		FormFiller.formFillElements(fieldArray);
		Pages.InputDocProcess().openCaseFrame();
		Assert.assertTrue("The button Continue does not exit in this form", Pages.InputDocProcess().continuebtn());	
		//Open the next task .
		Pages.InputDocProcess().switchToDefault();
		Pages.Home().gotoInbox();
		Assert.assertTrue("The case does not exist in Inbox", Pages.Home().existCase(caseNumber));		
		Pages.Home().openCase(caseNumber);	
		Pages.InputDocProcess().openCaseFrame();
		FormFieldData[] fieldArray2 = new FormFieldData[3];
		fieldArray2[0] = new FormFieldData();
		fieldArray2[1] = new FormFieldData();
		fieldArray2[2] = new FormFieldData();
		fieldArray2[0].fieldPath = "form[userFirstName]";
		fieldArray2[0].fieldFindType = FieldKeyType.ID;
		fieldArray2[0].fieldType = FieldType.TEXTBOX;
		fieldArray2[0].fieldValue = "FirtsName-" + Utils.getRandomString(5);
		fieldArray2[1].fieldPath = "form[userLastName]";
		fieldArray2[1].fieldFindType = FieldKeyType.ID;
		fieldArray2[1].fieldType = FieldType.TEXTBOX;
		fieldArray2[1].fieldValue = "LastName-" + Utils.getRandomString(6);
		fieldArray2[2].fieldPath = "form[send]";
		fieldArray2[2].fieldFindType = FieldKeyType.ID;
		fieldArray2[2].fieldType = FieldType.BUTTON;
		fieldArray2[2].fieldValue = "";
		FormFiller.formFillElements(fieldArray2);
		//Form with the updates
		Assert.assertEquals(fieldArray2[0].fieldValue, Value.getValue(FieldKeyType.ID, "form[firstNameUpdate]"));
		Assert.assertEquals(fieldArray2[1].fieldValue, Value.getValue(FieldKeyType.ID, "form[lastNameUpdate]"));
		FormFieldData[] fieldArray3 = new FormFieldData[1];
		fieldArray3[0] = new FormFieldData();
		fieldArray3[0].fieldPath = "form[continue]";
		fieldArray3[0].fieldFindType = FieldKeyType.ID;
		fieldArray3[0].fieldType = FieldType.BUTTON;
		fieldArray3[0].fieldValue = "";
		FormFiller.formFillElements(fieldArray3);
		Pages.InputDocProcess().openCaseFrame();
		Assert.assertTrue("The button Continue does not exit in this form", Pages.InputDocProcess().continuebtn());	
		//Open the last task 
		Pages.InputDocProcess().switchToDefault();
		Pages.Home().gotoInbox();
		Assert.assertTrue("The case does not exist in Inbox", Pages.Home().existCase(caseNumber));		
		Pages.Home().openCase(caseNumber);	
		Pages.InputDocProcess().openCaseFrame();
		Assert.assertTrue("The button Continue does not exit in this form", Pages.InputDocProcess().continuebtn());	
		Pages.Main().logout();
}

}