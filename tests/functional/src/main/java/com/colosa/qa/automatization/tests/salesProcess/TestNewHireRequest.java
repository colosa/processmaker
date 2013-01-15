package com.colosa.qa.automatization.tests.salesProcess;

import org.junit.Assert;
import org.junit.AfterClass;
import org.junit.Test;

import com.colosa.qa.automatization.pages.*;
import com.colosa.qa.automatization.common.*;
import org.openqa.selenium.WebElement;

import java.io.FileNotFoundException;
import java.io.IOException;

public class TestNewHireRequest{

	protected static int caseNum;
	protected static String approve = "Yes";
	protected static String approve2 = "Yes";
	@Test
	public void runProcess() throws FileNotFoundException, IOException, Exception{
		Pages.Login().gotoUrl();
		Pages.Login().loginUser("hector", "sample", "workflow");
		Pages.Main().goHome();	
		caseNum = Pages.Home().startCase("New Hire Request v_1 (Request for new hire)");

		FormFieldData[] arrayData = new FormFieldData[7];
		arrayData[0] = new FormFieldData();
		arrayData[1] = new FormFieldData();
		arrayData[2] = new FormFieldData();
		arrayData[3] = new FormFieldData();
		arrayData[4] = new FormFieldData();
		arrayData[5] = new FormFieldData();
		arrayData[6] = new FormFieldData();

		arrayData[0].fieldPath = "form[reqPropDate]";
		arrayData[0].fieldFindType = FieldKeyType.ID;
		arrayData[0].fieldType = FieldType.TEXTBOX;
		arrayData[0].fieldValue = "2012-12-12";
		arrayData[1].fieldPath = "form[reqPosition]";
		arrayData[1].fieldFindType = FieldKeyType.ID;
		arrayData[1].fieldType = FieldType.TEXTBOX;
		arrayData[1].fieldValue = "Prueba...";
		arrayData[2].fieldPath = "form[reqSalary]";
		arrayData[2].fieldFindType = FieldKeyType.ID;
		arrayData[2].fieldType = FieldType.TEXTBOX;
		arrayData[2].fieldValue = "23564";
		arrayData[3].fieldPath = "form[reqJDesc]";
		arrayData[3].fieldFindType = FieldKeyType.ID;
		arrayData[3].fieldType = FieldType.TEXTAREA;
		arrayData[3].fieldValue = "Prueba...";
		arrayData[4].fieldPath = "form[reqJQual]";
		arrayData[4].fieldFindType = FieldKeyType.ID;
		arrayData[4].fieldType = FieldType.TEXTAREA;
		arrayData[4].fieldValue = "Prueba...";
		arrayData[5].fieldPath = "form[reqComments]";
		arrayData[5].fieldFindType = FieldKeyType.ID;
		arrayData[5].fieldType = FieldType.TEXTAREA;
		arrayData[5].fieldValue = "Prueba...";
		arrayData[6].fieldPath = "form[Submit]";
		arrayData[6].fieldFindType = FieldKeyType.ID;
		arrayData[6].fieldType = FieldType.BUTTON;
		arrayData[6].fieldValue = "";
		Pages.InputDocProcess().openCaseFrame();
		Assert.assertTrue(FormFiller.formFillElements(arrayData));
		Assert.assertTrue(Pages.InputDocProcess().continuebtn());
		Pages.Main().logout();

/*	}

	@Test
	public void reviewRequest() throws FileNotFoundException, IOException, Exception{*/
		Pages.Login().gotoUrl();
		Pages.Login().loginUser("pablo", "sample", "");
		Pages.Main().goHome();
		Pages.Home().openCase(caseNum);
		FormFieldData[] arrayData2 = new FormFieldData[2];
		arrayData2[0] = new FormFieldData();
		arrayData2[1] = new FormFieldData();

		arrayData2[0].fieldPath = "form[apRequest]";
		arrayData2[0].fieldFindType = FieldKeyType.ID;
		arrayData2[0].fieldType = FieldType.DROPDOWN;
		arrayData2[0].fieldValue = approve;
		arrayData2[1].fieldPath = "form[Submit]";
		arrayData2[1].fieldFindType = FieldKeyType.ID;
		arrayData2[1].fieldType = FieldType.BUTTON;
		arrayData2[1].fieldValue = "";
		Pages.InputDocProcess().openCaseFrame();
		Assert.assertTrue(FormFiller.formFillElements(arrayData2));
		Assert.assertTrue(Pages.InputDocProcess().continuebtn());
		Pages.Main().logout();
	/*}
	@Test
	public void budgetApproval() throws FileNotFoundException, IOException, Exception{*/

		Pages.Login().gotoUrl();
		Pages.Login().loginUser("iver", "sample", "");
		Pages.Main().goHome();
		Pages.Home().openCase(caseNum);

		FormFieldData[] arrayData3 = new FormFieldData[3];
		arrayData3[0] = new FormFieldData();
		arrayData3[1] = new FormFieldData();
		arrayData3[2] = new FormFieldData();

		arrayData3[0].fieldPath = "form[bgRequest]";
		arrayData3[0].fieldFindType = FieldKeyType.ID;
		arrayData3[0].fieldType = FieldType.DROPDOWN;
		arrayData3[0].fieldValue = approve2;
		arrayData3[1].fieldPath = "form[bgSalary]";
		arrayData3[1].fieldFindType = FieldKeyType.ID;
		arrayData3[1].fieldType = FieldType.TEXTBOX;
		arrayData3[1].fieldValue = "2456";
		arrayData3[2].fieldPath = "form[BGSubmit]";
		arrayData3[2].fieldFindType = FieldKeyType.ID;
		arrayData3[2].fieldType = FieldType.BUTTON;
		arrayData3[2].fieldValue = "";
		Pages.InputDocProcess().openCaseFrame();
		Assert.assertTrue(FormFiller.formFillElements(arrayData3));
		Assert.assertTrue(Pages.InputDocProcess().continuebtn());
		Pages.Main().logout();

	/*}

	@Test
	public void positionPublication() throws FileNotFoundException, IOException, Exception{*/

		Pages.Login().gotoUrl();
		Pages.Login().loginUser("pablo", "sample", "");
		Pages.Main().goHome();
		Pages.Home().openCase(caseNum);

		FormFieldData[] arrayData4 = new FormFieldData[5];
		arrayData4[0] = new FormFieldData();
		arrayData4[1] = new FormFieldData();
		arrayData4[2] = new FormFieldData();
		arrayData4[3] = new FormFieldData();
		arrayData4[4] = new FormFieldData();

		arrayData4[0].fieldPath = "form[propAdvertising][2]";
		arrayData4[0].fieldFindType = FieldKeyType.ID;
		arrayData4[0].fieldType = FieldType.CHECK;
		arrayData4[0].fieldValue = "";
		arrayData4[1].fieldPath = "form[propAdvertising][3]";
		arrayData4[1].fieldFindType = FieldKeyType.ID;
		arrayData4[1].fieldType = FieldType.CHECK;
		arrayData4[1].fieldValue = "";
		arrayData4[2].fieldPath = "form[Position_ID]";
		arrayData4[2].fieldFindType = FieldKeyType.ID;
		arrayData4[2].fieldType = FieldType.TEXTBOX;
		arrayData4[2].fieldValue = "AB-123";
		arrayData4[3].fieldPath = "form[propStartDate]";
		arrayData4[3].fieldFindType = FieldKeyType.ID;
		arrayData4[3].fieldType = FieldType.READONLY;
		arrayData4[3].fieldValue = "2012-12-29";
		arrayData4[4].fieldPath = "form[Submit]";
		arrayData4[4].fieldFindType = FieldKeyType.ID;
		arrayData4[4].fieldType = FieldType.BUTTON;
		arrayData4[4].fieldValue = "";

		FormFieldData[] arrayData5 = new FormFieldData[1];
		arrayData5[0] = new FormFieldData();

		arrayData5[0].fieldPath = "form[submit]";
		arrayData5[0].fieldFindType = FieldKeyType.ID;
		arrayData5[0].fieldType = FieldType.BUTTON;
		arrayData5[0].fieldValue = "";

		Pages.InputDocProcess().openCaseFrame();
		Assert.assertTrue(FormFiller.formFillElements(arrayData4));
		Assert.assertTrue(FormFiller.formFillElements(arrayData5));
		Assert.assertTrue(Pages.InputDocProcess().continuebtn());		
	/*}

	@Test
	public void endPositionPublication() throws FileNotFoundException, IOException, Exception{*/

		Pages.Home().openCase(caseNum);

		FormFieldData[] arrayData6 = new FormFieldData[3];
		arrayData6[0] = new FormFieldData();
		arrayData6[1] = new FormFieldData();
		arrayData6[2] = new FormFieldData();
		
		arrayData6[0].fieldPath = "form[propEndDate]";
		arrayData6[0].fieldFindType = FieldKeyType.ID;
		arrayData6[0].fieldType = FieldType.READONLY;
		arrayData6[0].fieldValue = "2013-12-29";
		arrayData6[1].fieldPath = "form[propStatus]";
		arrayData6[1].fieldFindType = FieldKeyType.ID;
		arrayData6[1].fieldType = FieldType.DROPDOWN;
		arrayData6[1].fieldValue = "Open";
		arrayData6[2].fieldPath = "form[Submit]";
		arrayData6[2].fieldFindType = FieldKeyType.ID;
		arrayData6[2].fieldType = FieldType.BUTTON;
		arrayData6[2].fieldValue = "";

		Pages.InputDocProcess().openCaseFrame();
		Assert.assertTrue(FormFiller.formFillElements(arrayData6));
		Assert.assertTrue(Pages.InputDocProcess().continuebtn());

	}
}