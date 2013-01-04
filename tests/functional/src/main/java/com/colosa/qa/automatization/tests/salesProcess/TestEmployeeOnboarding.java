package com.colosa.qa.automatization.tests.salesProcess;

import org.junit.Assert;
import org.junit.AfterClass;
import org.junit.Test;

import com.colosa.qa.automatization.pages.*;
import com.colosa.qa.automatization.common.*;
import org.openqa.selenium.WebElement;

import java.io.FileNotFoundException;
import java.io.IOException;

public class TestEmployeeOnboarding{

	protected static int caseNum;

	@Test
	public void onBoardNewEnployee()throws FileNotFoundException, IOException, Exception{

		Pages.Login().gotoUrl();
		Pages.Login().loginUser("admin", "admin", "workflow");
		Pages.Main().goHome();	
		caseNum = Pages.Home().startCase("Employee Onboarding v-0.2 (Onboard New Employee)");
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
		arrayData[1].fieldPath = "form[Emp_Mid_Name]";
		arrayData[1].fieldFindType = FieldKeyType.ID;
		arrayData[1].fieldType = FieldType.TEXTBOX;
		arrayData[1].fieldValue = "Ricardo";
		arrayData[2].fieldPath = "form[Emp_Last_Name]";
		arrayData[2].fieldFindType = FieldKeyType.ID;
		arrayData[2].fieldType = FieldType.TEXTBOX;
		arrayData[2].fieldValue = "Hurtado";
		arrayData[3].fieldPath = "form[EMP_DOB]";
		arrayData[3].fieldFindType = FieldKeyType.ID;
		arrayData[3].fieldType = FieldType.TEXTBOX;
		arrayData[3].fieldValue = "12/12/2012";
		arrayData[4].fieldPath = "form[Emp_SSN]";
		arrayData[4].fieldFindType = FieldKeyType.ID;
		arrayData[4].fieldType = FieldType.TEXTBOX;
		arrayData[4].fieldValue = "5555";
		arrayData[5].fieldPath = "form[Emp_Address]";
		arrayData[5].fieldFindType = FieldKeyType.ID;
		arrayData[5].fieldType = FieldType.TEXTBOX;
		arrayData[5].fieldValue = "Edif. Victor";
		arrayData[6].fieldPath = "form[Emp_Home_Phone]";
		arrayData[6].fieldFindType = FieldKeyType.ID;
		arrayData[6].fieldType = FieldType.TEXTBOX;
		arrayData[6].fieldValue = "7777777";
		arrayData[7].fieldPath = "form[btnContinue]";
		arrayData[7].fieldFindType = FieldKeyType.ID;
		arrayData[7].fieldType = FieldType.BUTTON;
		arrayData[7].fieldValue = "";
		Pages.InputDocProcess().openCaseFrame();
		FormFiller.formFillElements(arrayData);
		FormFieldData[] arrayData2 = new FormFieldData[2];
		arrayData2[0] = new FormFieldData();
		arrayData2[1] = new FormFieldData();
		arrayData2[0].fieldPath = "form[Emp_DOB]";
		arrayData2[0].fieldFindType = FieldKeyType.ID;
		arrayData2[0].fieldType = FieldType.READONLY;
		arrayData2[0].fieldValue = "12/12/1980";
		arrayData2[1].fieldPath = "form[Emp_Info_Submit]";
		arrayData2[1].fieldFindType = FieldKeyType.ID;
		arrayData2[1].fieldType = FieldType.BUTTON;
		arrayData2[1].fieldValue = "";
		Pages.InputDocProcess().openCaseFrame();
		Assert.assertTrue(FormFiller.formFillElements(arrayData2));
		FormFieldData[] arrayData3 = new FormFieldData[8];
		arrayData3[0] = new FormFieldData();
		arrayData3[1] = new FormFieldData();
		arrayData3[2] = new FormFieldData();
		arrayData3[3] = new FormFieldData();
		arrayData3[4] = new FormFieldData();
		arrayData3[5] = new FormFieldData();
		arrayData3[6] = new FormFieldData();
		arrayData3[7] = new FormFieldData();
		arrayData3[0].fieldPath = "form[Emp_Emp_Number]";
		arrayData3[0].fieldFindType = FieldKeyType.ID;
		arrayData3[0].fieldType = FieldType.TEXTBOX;
		arrayData3[0].fieldValue = "111";
		arrayData3[1].fieldPath = "form[Emp_Position]";
		arrayData3[1].fieldFindType = FieldKeyType.ID;
		arrayData3[1].fieldType = FieldType.TEXTBOX;
		arrayData3[1].fieldValue = "Aux";
		arrayData3[2].fieldPath = "form[Emp_Pay_Grade]";
		arrayData3[2].fieldFindType = FieldKeyType.ID;
		arrayData3[2].fieldType = FieldType.DROPDOWN;
		arrayData3[2].fieldValue = "Grade E";
		arrayData3[3].fieldPath = "form[Emp_Salary]";
		arrayData3[3].fieldFindType = FieldKeyType.ID;
		arrayData3[3].fieldType = FieldType.TEXTBOX;
		arrayData3[3].fieldValue = "2000";
		arrayData3[4].fieldPath = "form[Emp_Start_Date]";
		arrayData3[4].fieldFindType = FieldKeyType.ID;
		arrayData3[4].fieldType = FieldType.READONLY;
		arrayData3[4].fieldValue = "12/12/2012";
		arrayData3[5].fieldPath = "form[Emp_Department]";
		arrayData3[5].fieldFindType = FieldKeyType.ID;
		arrayData3[5].fieldType = FieldType.DROPDOWN;
		arrayData3[5].fieldValue = "Human Resources";
		arrayData3[6].fieldPath = "form[Emp_Reports_To]";
		arrayData3[6].fieldFindType = FieldKeyType.ID;
		arrayData3[6].fieldType = FieldType.DROPDOWN;
		arrayData3[6].fieldValue = "ian";
		arrayData3[7].fieldPath = "form[Positio_Dets_Submit]";
		arrayData3[7].fieldFindType = FieldKeyType.ID;
		arrayData3[7].fieldType = FieldType.BUTTON;
		arrayData3[7].fieldValue = "";
		Pages.InputDocProcess().openCaseFrame();
		Assert.assertTrue(FormFiller.formFillElements(arrayData3));
		Assert.assertTrue("The button Continue does not exit in this form", Browser.elementExists("inputDocProcess.webelement.continue"));
		Pages.InputDocProcess().continuebtn();
		Pages.Main().logout();

	}

	@Test
	public void materialsAllocation() throws FileNotFoundException, IOException, Exception{

		Pages.Login().gotoUrl();
		Pages.Login().loginUser("Julian", "sample", "");
		Pages.Main().goHome();
		Assert.assertTrue("The case does not exist in Inbox", Pages.Home().existCase(caseNum));	
		Pages.Home().openCase(caseNum);	
		FormFieldData[] arrayData = new FormFieldData[15];
		arrayData[0] = new FormFieldData();
		arrayData[1] = new FormFieldData();
		arrayData[2] = new FormFieldData();
		arrayData[3] = new FormFieldData();
		arrayData[4] = new FormFieldData();
		arrayData[5] = new FormFieldData();
		arrayData[6] = new FormFieldData();
		arrayData[7] = new FormFieldData();
		arrayData[8] = new FormFieldData();
		arrayData[9] = new FormFieldData();
		arrayData[10] = new FormFieldData();
		arrayData[11] = new FormFieldData();
		arrayData[12] = new FormFieldData();
		arrayData[13] = new FormFieldData();
		arrayData[14] = new FormFieldData();
		arrayData[0].fieldPath = "form[Furniture_Grid][1][Furniture_Item]";
		arrayData[0].fieldFindType = FieldKeyType.ID;
		arrayData[0].fieldType = FieldType.DROPDOWN;
		arrayData[0].fieldValue = "Large Desk";
		arrayData[1].fieldPath = "form[Furniture_Grid][1][Furniture_Quan]";
		arrayData[1].fieldFindType = FieldKeyType.ID;
		arrayData[1].fieldType = FieldType.TEXTBOX;
		arrayData[1].fieldValue = "1";
		arrayData[2].fieldPath = "form[Furniture_Grid][1][Furniture_Asset_No]";
		arrayData[2].fieldFindType = FieldKeyType.ID;
		arrayData[2].fieldType = FieldType.TEXTBOX;
		arrayData[2].fieldValue = "111";
		arrayData[3].fieldPath = "form[Furniture_Grid][1][Furniture_Location]";
		arrayData[3].fieldFindType = FieldKeyType.ID;
		arrayData[3].fieldType = FieldType.TEXTBOX;
		arrayData[3].fieldValue = "PB";
		arrayData[4].fieldPath = "form[Technology_Grid][1][Technology_Item]";
		arrayData[4].fieldFindType = FieldKeyType.ID;
		arrayData[4].fieldType = FieldType.DROPDOWN;
		arrayData[4].fieldValue = "Desktop PC";
		arrayData[5].fieldPath = "form[Technology_Grid][1][Tech_Quantity]";
		arrayData[5].fieldFindType = FieldKeyType.ID;
		arrayData[5].fieldType = FieldType.TEXTBOX;
		arrayData[5].fieldValue = "1";
		arrayData[6].fieldPath = "form[Technology_Grid][1][Tech_Asset_Number]";
		arrayData[6].fieldFindType = FieldKeyType.ID;
		arrayData[6].fieldType = FieldType.TEXTBOX;
		arrayData[6].fieldValue = "222";
		arrayData[7].fieldPath = "form[Technology_Grid][1][Tech_Location]";
		arrayData[7].fieldFindType = FieldKeyType.ID;
		arrayData[7].fieldType = FieldType.TEXTBOX;
		arrayData[7].fieldValue = "PB";
		arrayData[8].fieldPath = "form[Systems_Grid][1][System_Select]";
		arrayData[8].fieldFindType = FieldKeyType.ID;
		arrayData[8].fieldType = FieldType.DROPDOWN;
		arrayData[8].fieldValue = "LDAP";
		arrayData[9].fieldPath = "form[Systems_Grid][1][System_Perm_Level]";
		arrayData[9].fieldFindType = FieldKeyType.ID;
		arrayData[9].fieldType = FieldType.TEXTBOX;
		arrayData[9].fieldValue = "Admin";
		arrayData[10].fieldPath = "form[Systems_Grid][1][System_Username]";
		arrayData[10].fieldFindType = FieldKeyType.ID;
		arrayData[10].fieldType = FieldType.TEXTBOX;
		arrayData[10].fieldValue = "guest";
		arrayData[11].fieldPath = "form[Systems_Grid][1][System_Password]";
		arrayData[11].fieldFindType = FieldKeyType.ID;
		arrayData[11].fieldType = FieldType.TEXTBOX;
		arrayData[11].fieldValue = "guest";
		arrayData[12].fieldPath = "form[Emp_Email]";
		arrayData[12].fieldFindType = FieldKeyType.ID;
		arrayData[12].fieldType = FieldType.TEXTBOX;
		arrayData[12].fieldValue = "guest@test.com";
		arrayData[13].fieldPath = "form[Emp_IP_Address]";
		arrayData[13].fieldFindType = FieldKeyType.ID;
		arrayData[13].fieldType = FieldType.TEXTBOX;
		arrayData[13].fieldValue = "192.168.11.11";
		arrayData[14].fieldPath = "form[Allocation_Submit]";
		arrayData[14].fieldFindType = FieldKeyType.ID;
		arrayData[14].fieldType = FieldType.BUTTON;
		arrayData[14].fieldValue = "";
		Pages.InputDocProcess().openCaseFrame();
		Assert.assertTrue(FormFiller.formFillElements(arrayData));
		FormFieldData[] arrayData2 = new FormFieldData[1];
		arrayData2[0] = new FormFieldData();
		arrayData2[0].fieldPath = "form[NEXT_STEP]";
		arrayData2[0].fieldFindType = FieldKeyType.ID;
		arrayData2[0].fieldType = FieldType.BUTTON;
		arrayData2[0].fieldValue = "";
		Pages.InputDocProcess().openCaseFrame();
		Assert.assertTrue(FormFiller.formFillElements(arrayData2));
		Assert.assertTrue(Pages.InputDocProcess().continuebtn());
		Pages.Main().logout();
	}

	@Test
	public void trainingArrangement() throws FileNotFoundException, IOException, Exception{

		Pages.Login().gotoUrl();
		Pages.Login().loginUser("Joshua", "sample", "");
		Pages.Main().goHome();
		Assert.assertTrue("The case does not exist in Inbox", Pages.Home().existCase(caseNum));	
		Pages.Home().openCase(caseNum);
		FormFieldData[] arrayData = new FormFieldData[4];
		arrayData[0] = new FormFieldData();
		arrayData[1] = new FormFieldData();
		arrayData[2] = new FormFieldData();
		arrayData[3] = new FormFieldData();
		arrayData[0].fieldPath = "form[Select_Training][1][Training_Course]";
		arrayData[0].fieldFindType = FieldKeyType.ID;
		arrayData[0].fieldType = FieldType.DROPDOWN;
		arrayData[0].fieldValue = "Employee Induction";
		arrayData[1].fieldPath = "form[Select_Training][1][Date_Of_Course]";
		arrayData[1].fieldFindType = FieldKeyType.ID;
		arrayData[1].fieldType = FieldType.READONLY;
		arrayData[1].fieldValue = "12/12/2012";
		arrayData[2].fieldPath = "form[Select_Training][1][Course_Location]";
		arrayData[2].fieldFindType = FieldKeyType.ID;
		arrayData[2].fieldType = FieldType.TEXTBOX;
		arrayData[2].fieldValue = "Show room";
		arrayData[3].fieldPath = "form[Task3_Submit]";
		arrayData[3].fieldFindType = FieldKeyType.ID;
		arrayData[3].fieldType = FieldType.BUTTON;
		arrayData[3].fieldValue = "";
		Pages.InputDocProcess().openCaseFrame();
		Assert.assertTrue(FormFiller.formFillElements(arrayData));
		Assert.assertTrue(Pages.InputDocProcess().continuebtn());
		Pages.Main().logout();
	}

	@Test
	public void threeMonthReview() throws FileNotFoundException, IOException, Exception{

		Pages.Login().gotoUrl();
		Pages.Login().loginUser("Ian", "sample", "");
		Pages.Main().goHome();
		Assert.assertTrue("The case does not exist in Inbox", Pages.Home().existCase(caseNum));	
		Pages.Home().openCase(caseNum);
		FormFieldData[] arrayData = new FormFieldData[3];
		arrayData[0] = new FormFieldData();
		arrayData[1] = new FormFieldData();
		arrayData[2] = new FormFieldData();
		arrayData[0].fieldPath = "form[Performance_Eval_Notes]";
		arrayData[0].fieldFindType = FieldKeyType.ID;
		arrayData[0].fieldType = FieldType.TEXTBOX;
		arrayData[0].fieldValue = "It is the right person for the job";
		arrayData[1].fieldPath = "form[Outcome_Decision][3]";
		arrayData[1].fieldFindType = FieldKeyType.ID;
		arrayData[1].fieldType = FieldType.RADIOBUTTON;
		arrayData[1].fieldValue = "";
		arrayData[2].fieldPath = "form[Review_Form_Submit]";
		arrayData[2].fieldFindType = FieldKeyType.ID;
		arrayData[2].fieldType = FieldType.BUTTON;
		arrayData[2].fieldValue = "";
		Pages.InputDocProcess().openCaseFrame();
		Assert.assertTrue(FormFiller.formFillElements(arrayData));
		Assert.assertTrue(Pages.InputDocProcess().continuebtn());
		Pages.Main().logout();
//	}

//	@Test
//	public void dissmisal() throws FileNotFoundException, IOException, Exception{

		Pages.Login().gotoUrl();
		Pages.Login().loginUser("Jacob", "sample", "");
		Pages.Main().goHome();
		Assert.assertTrue("The case does not exist in Inbox", Pages.Home().existCase(caseNum));	
		Pages.Home().openCase(caseNum);
		FormFieldData[] arrayData2 = new FormFieldData[1];
		arrayData2[0] = new FormFieldData();
		arrayData2[0].fieldPath = "form[NEXT_STEP]";
		arrayData2[0].fieldFindType = FieldKeyType.ID;
		arrayData2[0].fieldType = FieldType.BUTTON;
		arrayData2[0].fieldValue = "";
		Pages.InputDocProcess().openCaseFrame();
		Assert.assertTrue(FormFiller.formFillElements(arrayData2));
		Assert.assertTrue(Pages.InputDocProcess().continuebtn());
		Pages.Main().logout();
	}

}