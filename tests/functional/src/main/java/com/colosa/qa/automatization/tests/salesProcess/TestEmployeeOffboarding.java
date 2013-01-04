package com.colosa.qa.automatization.tests.salesProcess;

import org.junit.Assert;
import org.junit.AfterClass;
import org.junit.Test;

import com.colosa.qa.automatization.pages.*;
import com.colosa.qa.automatization.common.*;
import org.openqa.selenium.WebElement;

import java.io.FileNotFoundException;
import java.io.IOException;

public class TestEmployeeOffboarding{

	protected static int caseNum;

	@Test
	public void startEnployeeBoarding()throws FileNotFoundException, IOException, Exception{

		Pages.Login().gotoUrl();
		Pages.Login().loginUser("admin", "admin", "workflow");
		Pages.Main().goHome();	
		caseNum = Pages.Home().startCase("Employee Offboarding v_1 (Start employee offboarding)");
		FormFieldData[] arrayData = new FormFieldData[8];
		arrayData[0] = new FormFieldData();
		arrayData[1] = new FormFieldData();
		arrayData[2] = new FormFieldData();
		arrayData[3] = new FormFieldData();
		arrayData[4] = new FormFieldData();
		arrayData[5] = new FormFieldData();
		arrayData[6] = new FormFieldData();
		arrayData[7] = new FormFieldData();
		arrayData[0].fieldPath = "form[Emp_First_Name]";
		arrayData[0].fieldFindType = FieldKeyType.ID;
		arrayData[0].fieldType = FieldType.TEXTBOX;
		arrayData[0].fieldValue = "Ademar";
		arrayData[1].fieldPath = "form[Emp_Last_Name]";
		arrayData[1].fieldFindType = FieldKeyType.ID;
		arrayData[1].fieldType = FieldType.TEXTBOX;
		arrayData[1].fieldValue = "Hurtado";
		arrayData[2].fieldPath = "form[Emp_Emp_Number]";
		arrayData[2].fieldFindType = FieldKeyType.ID;
		arrayData[2].fieldType = FieldType.TEXTBOX;
		arrayData[2].fieldValue = "xxxxxx";
		arrayData[3].fieldPath = "form[Emp_Position]";
		arrayData[3].fieldFindType = FieldKeyType.ID;
		arrayData[3].fieldType = FieldType.TEXTBOX;
		arrayData[3].fieldValue = "Aux";
		arrayData[4].fieldPath = "form[Emp_Salary]";
		arrayData[4].fieldFindType = FieldKeyType.ID;
		arrayData[4].fieldType = FieldType.TEXTBOX;
		arrayData[4].fieldValue = "3000";
		arrayData[5].fieldPath = "form[Emp_email]";
		arrayData[5].fieldFindType = FieldKeyType.ID;
		arrayData[5].fieldType = FieldType.TEXTBOX;
		arrayData[5].fieldValue = "emp@test.com";
		arrayData[6].fieldPath = "form[Last_day]";
		arrayData[6].fieldFindType = FieldKeyType.ID;
		arrayData[6].fieldType = FieldType.READONLY;
		arrayData[6].fieldValue = "12/12/2012";
		arrayData[7].fieldPath = "form[Change_Emp_Details_Submit]";
		arrayData[7].fieldFindType = FieldKeyType.ID;
		arrayData[7].fieldType = FieldType.BUTTON;
		arrayData[7].fieldValue = "";
		Pages.InputDocProcess().openCaseFrame();
		Assert.assertTrue(FormFiller.formFillElements(arrayData));
		Assert.assertTrue("The button Continue does not exit in this form", Browser.elementExists("inputDocProcess.webelement.continue"));
		Pages.InputDocProcess().continuebtn();
		Pages.Main().logout();

	}

	@Test
	public void registerOffBoarding() throws FileNotFoundException, IOException, Exception{

		Pages.Login().gotoUrl();
		Pages.Login().loginUser("Jacob", "sample", "");
		Pages.Main().goHome();
		Assert.assertTrue("The case does not exist in Inbox", Pages.Home().existCase(caseNum));	
		Pages.Home().openCase(caseNum);	
		FormFieldData[] arrayData = new FormFieldData[3];
		arrayData[0] = new FormFieldData();
		arrayData[1] = new FormFieldData();
		arrayData[2] = new FormFieldData();
		arrayData[0].fieldPath = "form[off_keys]";
		arrayData[0].fieldFindType = FieldKeyType.ID;
		arrayData[0].fieldType = FieldType.CHECK;
		arrayData[0].fieldValue = "";
		arrayData[1].fieldPath = "form[desk_spec_keys]";
		arrayData[1].fieldFindType = FieldKeyType.ID;
		arrayData[1].fieldType = FieldType.CHECK;
		arrayData[1].fieldValue = "";
		arrayData[2].fieldPath = "form[Change_Emp_Details_Submit]";
		arrayData[2].fieldFindType = FieldKeyType.ID;
		arrayData[2].fieldType = FieldType.BUTTON;
		arrayData[2].fieldValue = "";
		Pages.InputDocProcess().openCaseFrame();
		Assert.assertTrue(FormFiller.formFillElements(arrayData));
		Assert.assertTrue(Pages.InputDocProcess().continuebtn());
		Pages.Main().logout();
//	}

//	@Test
//	public void desactiveCredentialsInventary() throws FileNotFoundException, IOException, Exception{

		Pages.Login().gotoUrl();
		Pages.Login().loginUser("Julian", "sample", "");
		Pages.Main().goHome();
		Assert.assertTrue("The case does not exist in Inbox", Pages.Home().existCase(caseNum));	
		Pages.Home().openCase(caseNum);
		FormFieldData[] arrayData2 = new FormFieldData[10];
		arrayData2[0] = new FormFieldData();
		arrayData2[1] = new FormFieldData();
		arrayData2[2] = new FormFieldData();
		arrayData2[3] = new FormFieldData();
		arrayData2[4] = new FormFieldData();
		arrayData2[5] = new FormFieldData();
		arrayData2[6] = new FormFieldData();
		arrayData2[7] = new FormFieldData();
		arrayData2[8] = new FormFieldData();
		arrayData2[9] = new FormFieldData();
		arrayData2[0].fieldPath = "form[Reassign_Email]";
		arrayData2[0].fieldFindType = FieldKeyType.ID;
		arrayData2[0].fieldType = FieldType.TEXTBOX;
		arrayData2[0].fieldValue = "old@testtest.com";
		arrayData2[1].fieldPath = "form[tech_assigned][1][TA_RETURNED]";
		arrayData2[1].fieldFindType = FieldKeyType.ID;
		arrayData2[1].fieldType = FieldType.DROPDOWN;
		arrayData2[1].fieldValue = "Yes";
		arrayData2[2].fieldPath = "form[furniture_assigned][1][FURNITURE_ASSET_NO]";
		arrayData2[2].fieldFindType = FieldKeyType.ID;
		arrayData2[2].fieldType = FieldType.TEXTBOX;
		arrayData2[2].fieldValue = "785-99";
		arrayData2[3].fieldPath = "form[furniture_assigned][1][FURNITURE_DETAILS]";
		arrayData2[3].fieldFindType = FieldKeyType.ID;
		arrayData2[3].fieldType = FieldType.TEXTBOX;
		arrayData2[3].fieldValue = "Small desk";
		arrayData2[4].fieldPath = "form[furniture_assigned][1][FURNITURE_ITEM_LABEL]";
		arrayData2[4].fieldFindType = FieldKeyType.ID;
		arrayData2[4].fieldType = FieldType.TEXTBOX;
		arrayData2[4].fieldValue = "mm-5566";
		arrayData2[5].fieldPath = "form[furniture_assigned][1][FURNITURE_LOCATION]";
		arrayData2[5].fieldFindType = FieldKeyType.ID;
		arrayData2[5].fieldType = FieldType.TEXTBOX;
		arrayData2[5].fieldValue = "PB";
		arrayData2[6].fieldPath = "form[furniture_assigned][1][FURNITURE_QUAN]";
		arrayData2[6].fieldFindType = FieldKeyType.ID;
		arrayData2[6].fieldType = FieldType.TEXTBOX;
		arrayData2[6].fieldValue = "1";
		arrayData2[7].fieldPath = "form[furniture_assigned][1][F_RETURNED]";
		arrayData2[7].fieldFindType = FieldKeyType.ID;
		arrayData2[7].fieldType = FieldType.DROPDOWN;
		arrayData2[7].fieldValue = "Yes";
		arrayData2[8].fieldPath = "form[systems_assigned][1][SA_RESP]";
		arrayData2[8].fieldFindType = FieldKeyType.ID;
		arrayData2[8].fieldType = FieldType.DROPDOWN;
		arrayData2[8].fieldValue = "Yes";
		arrayData2[9].fieldPath = "form[Change_Emp_Details_Submit]";
		arrayData2[9].fieldFindType = FieldKeyType.ID;
		arrayData2[9].fieldType = FieldType.BUTTON;
		arrayData2[9].fieldValue = "";
		Pages.InputDocProcess().openCaseFrame();
		Assert.assertTrue(FormFiller.formFillElements(arrayData2));
		Assert.assertTrue(Pages.InputDocProcess().continuebtn());
		Pages.Main().logout();
	}

	@Test
	public void removeEnployee() throws FileNotFoundException, IOException, Exception{

		Pages.Login().gotoUrl();
		Pages.Login().loginUser("Jason", "sample", "");
		Pages.Main().goHome();
		Assert.assertTrue("The case does not exist in Inbox", Pages.Home().existCase(caseNum));	
		Pages.Home().openCase(caseNum);
		FormFieldData[] arrayData = new FormFieldData[4];
		arrayData[0] = new FormFieldData();
		arrayData[1] = new FormFieldData();
		arrayData[2] = new FormFieldData();
		arrayData[3] = new FormFieldData();
		arrayData[0].fieldPath = "form[Payroll_update1]";
		arrayData[0].fieldFindType = FieldKeyType.ID;
		arrayData[0].fieldType = FieldType.DROPDOWN;
		arrayData[0].fieldValue = "Yes";
		arrayData[1].fieldPath = "form[Payroll_update2]";
		arrayData[1].fieldFindType = FieldKeyType.ID;
		arrayData[1].fieldType = FieldType.DROPDOWN;
		arrayData[1].fieldValue = "Yes";
		arrayData[2].fieldPath = "form[Payroll_update3]";
		arrayData[2].fieldFindType = FieldKeyType.ID;
		arrayData[2].fieldType = FieldType.DROPDOWN;
		arrayData[2].fieldValue = "Yes";
		arrayData[3].fieldPath = "form[Change_Emp_Details_Submit]";
		arrayData[3].fieldFindType = FieldKeyType.ID;
		arrayData[3].fieldType = FieldType.BUTTON;
		arrayData[3].fieldValue = "";
		Pages.InputDocProcess().openCaseFrame();
		Assert.assertTrue(FormFiller.formFillElements(arrayData));
		Assert.assertTrue(Pages.InputDocProcess().continuebtn());
		Pages.Main().logout();
	}

}