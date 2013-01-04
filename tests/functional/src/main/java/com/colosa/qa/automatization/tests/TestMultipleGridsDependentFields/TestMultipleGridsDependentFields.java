package com.colosa.qa.automatization.tests.TestMultipleGridsDependentFields;

import org.junit.Assert;
import org.junit.AfterClass;
import org.junit.Test;

import com.colosa.qa.automatization.pages.*;
import com.colosa.qa.automatization.common.*;
import org.openqa.selenium.WebElement;

import java.io.FileNotFoundException;
import java.io.IOException;

public class TestMultipleGridsDependentFields{

	@Test
	public void executeProcess() throws FileNotFoundException, IOException, Exception{

		//Open process
		Pages.Login().gotoUrl();
		Pages.Login().loginUser("admin", "admin", "workflow");
		Pages.Main().goHome();
		Pages.Home().gotoNewCase();	
		int caseNumber = Pages.Home().startCase("Multiple grids with dependent fields (Task 1)");	
		Pages.InputDocProcess().openCaseFrame();
		FormFieldData[] fieldArray = new FormFieldData[10];
		fieldArray[0] = new FormFieldData();
		fieldArray[1] = new FormFieldData();
		fieldArray[2] = new FormFieldData();
		fieldArray[3] = new FormFieldData();
		fieldArray[4] = new FormFieldData();
		fieldArray[5] = new FormFieldData();
		fieldArray[6] = new FormFieldData();
		fieldArray[7] = new FormFieldData();
		fieldArray[8] = new FormFieldData();
		fieldArray[9] = new FormFieldData();
		fieldArray[0].fieldPath = "form[name]";
		fieldArray[0].fieldFindType = FieldKeyType.ID;
		fieldArray[0].fieldType = FieldType.TEXTBOX;
		fieldArray[0].fieldValue = "Richard Born";
		fieldArray[1].fieldPath = "form[university]";
		fieldArray[1].fieldFindType = FieldKeyType.ID;
		fieldArray[1].fieldType = FieldType.TEXTBOX;
		fieldArray[1].fieldValue = "UCLA";
		fieldArray[2].fieldPath = "form[age]";
		fieldArray[2].fieldFindType = FieldKeyType.ID;
		fieldArray[2].fieldType = FieldType.TEXTBOX;
		fieldArray[2].fieldValue = "23";
		fieldArray[3].fieldPath = "form[countryBirth][1][COUNTRY]";
		fieldArray[3].fieldFindType = FieldKeyType.ID;
		fieldArray[3].fieldType = FieldType.DROPDOWN;
		fieldArray[3].fieldValue = "Bolivia";
		fieldArray[4].fieldPath = "form[countryBirth][1][STATE]";
		fieldArray[4].fieldFindType = FieldKeyType.ID;
		fieldArray[4].fieldType = FieldType.DROPDOWN;
		fieldArray[4].fieldValue = "Chuquisaca";
		fieldArray[5].fieldPath = "form[countryBirth][1][LOCATION]";
		fieldArray[5].fieldFindType = FieldKeyType.ID;
		fieldArray[5].fieldType = FieldType.DROPDOWN;
		fieldArray[5].fieldValue = "Sucre";
		fieldArray[6].fieldPath = "form[countryCurrent][1][COUNTRY]";
		fieldArray[6].fieldFindType = FieldKeyType.ID;
		fieldArray[6].fieldType = FieldType.DROPDOWN;
		fieldArray[6].fieldValue = "Argentina";
		fieldArray[7].fieldPath = "form[countryCurrent][1][STATE]";
		fieldArray[7].fieldFindType = FieldKeyType.ID;
		fieldArray[7].fieldType = FieldType.DROPDOWN;
		fieldArray[7].fieldValue = "Salta";
		fieldArray[8].fieldPath = "form[countryCurrent][1][LOCATION]";
		fieldArray[8].fieldFindType = FieldKeyType.ID;
		fieldArray[8].fieldType = FieldType.DROPDOWN;
		fieldArray[8].fieldValue = "Tartagal";
		fieldArray[9].fieldPath = "form[Submit]";
		fieldArray[9].fieldFindType = FieldKeyType.ID;
		fieldArray[9].fieldType = FieldType.BUTTON;
		fieldArray[9].fieldValue = "";
		FormFiller.formFillElements(fieldArray);
		Pages.InputDocProcess().openCaseFrame();
		Assert.assertTrue("The button Continue does not exit in this form", Pages.InputDocProcess().continuebtn());	
		//Open the next task .
		Pages.InputDocProcess().switchToDefault();
		Pages.Home().gotoInbox();
		Assert.assertTrue("The case does not exist in Inbox", Pages.Home().existCase(caseNumber));		
		Pages.Home().openCase(caseNumber);	
		Pages.InputDocProcess().openCaseFrame();
		Assert.assertEquals(Value.getValue(FieldKeyType.ID, "form[birthS]"), "BO");
		Assert.assertEquals(Value.getValue(FieldKeyType.ID, "form[countryResidence]"), "AR");
		FormFieldData[] fieldArray2 = new FormFieldData[1];
		fieldArray2[0] = new FormFieldData();
		fieldArray2[0].fieldPath = "form[send]";
		fieldArray2[0].fieldFindType = FieldKeyType.ID;
		fieldArray2[0].fieldType = FieldType.BUTTON;
		fieldArray2[0].fieldValue = "";
		FormFiller.formFillElements(fieldArray2);
		Pages.InputDocProcess().openCaseFrame();
		Assert.assertTrue("The button Continue does not exit in this form", Pages.InputDocProcess().continuebtn());	
		Pages.Main().logout();
	}

}