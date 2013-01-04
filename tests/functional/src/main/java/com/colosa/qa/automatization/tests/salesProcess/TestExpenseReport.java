package com.colosa.qa.automatization.tests.salesProcess;

import org.junit.Assert;
import org.junit.AfterClass;
import org.junit.Test;

import com.colosa.qa.automatization.pages.*;
import com.colosa.qa.automatization.common.*;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.By;

import java.io.FileNotFoundException;
import java.io.IOException;

public class TestExpenseReport{

	protected static int caseNum;

	@Test
	public void advanceCash()throws FileNotFoundException, IOException, Exception{

		Pages.Login().gotoUrl();
		Pages.Login().loginUser("Benjamin", "sample", "workflow");
		Pages.Main().goHome();	
		caseNum = Pages.Home().startCase("Expense Report v_2 (Cash Advance)");
		FormFieldData[] arrayData = new FormFieldData[4];
		arrayData[0] = new FormFieldData();
		arrayData[1] = new FormFieldData();
		arrayData[2] = new FormFieldData();
		arrayData[3] = new FormFieldData();
		arrayData[0].fieldPath = "form[repSubject]";
		arrayData[0].fieldFindType = FieldKeyType.ID;
		arrayData[0].fieldType = FieldType.TEXTBOX;
		arrayData[0].fieldValue = "Purchase cable";
		arrayData[1].fieldPath = "form[repRelatedto]";
		arrayData[1].fieldFindType = FieldKeyType.ID;
		arrayData[1].fieldType = FieldType.TEXTBOX;
		arrayData[1].fieldValue = "Purchase 100 mts of cable.";
		arrayData[2].fieldPath = "form[repCashAdvanced]";
		arrayData[2].fieldFindType = FieldKeyType.ID;
		arrayData[2].fieldType = FieldType.TEXTBOX;
		arrayData[2].fieldValue = "250";
		arrayData[3].fieldPath = "form[repBut]";
		arrayData[3].fieldFindType = FieldKeyType.ID;
		arrayData[3].fieldType = FieldType.BUTTON;
		arrayData[3].fieldValue = "";
		Pages.InputDocProcess().openCaseFrame();
		FormFiller.formFillElements(arrayData);
		FormFieldData[] arrayData2 = new FormFieldData[2];
		arrayData2[0] = new FormFieldData();
		arrayData2[1] = new FormFieldData();
		arrayData2[0].fieldPath = "form[TASKS][1][USR_UID]";
		arrayData2[0].fieldFindType = FieldKeyType.ID;
		arrayData2[0].fieldType = FieldType.DROPDOWN;
		arrayData2[0].fieldValue = "Ciarleglio, Brianna";
		arrayData2[1].fieldPath = "btnContinue";
		arrayData2[1].fieldFindType = FieldKeyType.ID;
		arrayData2[1].fieldType = FieldType.BUTTON;
		arrayData2[1].fieldValue = "";
		Pages.InputDocProcess().openCaseFrame();
		Assert.assertTrue(FormFiller.formFillElements(arrayData2));
		Pages.Main().logout();

	}

	@Test
	public void approveAdvanceCash() throws FileNotFoundException, IOException, Exception{

		Pages.Login().gotoUrl();
		Pages.Login().loginUser("Brianna", "sample", "");
		Pages.Main().goHome();
		Assert.assertTrue("The case does not exist in Inbox", Pages.Home().existCase(caseNum));	
		Pages.Home().openCase(caseNum);	
		FormFieldData[] arrayData = new FormFieldData[2];
		arrayData[0] = new FormFieldData();
		arrayData[1] = new FormFieldData();
		arrayData[0].fieldPath = "form[repCashApr]";
		arrayData[0].fieldFindType = FieldKeyType.ID;
		arrayData[0].fieldType = FieldType.DROPDOWN;
		arrayData[0].fieldValue = "Yes";
		arrayData[1].fieldPath = "form[repBut]";
		arrayData[1].fieldFindType = FieldKeyType.ID;
		arrayData[1].fieldType = FieldType.BUTTON;
		arrayData[1].fieldValue = "";
		Pages.InputDocProcess().openCaseFrame();
		Assert.assertTrue(FormFiller.formFillElements(arrayData));
		Assert.assertTrue(Pages.InputDocProcess().continuebtn());
		Pages.Main().logout();
	}

	@Test
	public void reportExpenses() throws FileNotFoundException, IOException, Exception{

		Pages.Login().gotoUrl();
		Pages.Login().loginUser("Benjamin", "sample", "");
		Pages.Main().goHome();
		Assert.assertTrue("The case does not exist in Inbox", Pages.Home().existCase(caseNum));	
		Pages.Home().openCase(caseNum);
		FormFieldData[] arrayData = new FormFieldData[17];
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
		arrayData[15] = new FormFieldData();
		arrayData[16] = new FormFieldData();
		arrayData[0].fieldPath = "form[repDetail][1][dateRep]";
		arrayData[0].fieldFindType = FieldKeyType.ID;
		arrayData[0].fieldType = FieldType.READONLY;
		arrayData[0].fieldValue = "12/12/2012";
		arrayData[1].fieldPath = "form[repDetail][1][categoryRep]";
		arrayData[1].fieldFindType = FieldKeyType.ID;
		arrayData[1].fieldType = FieldType.DROPDOWN;
		arrayData[1].fieldValue = "Meals";
		arrayData[2].fieldPath = "form[repDetail][1][expenseRep1]";
		arrayData[2].fieldFindType = FieldKeyType.ID;
		arrayData[2].fieldType = FieldType.DROPDOWN;
		arrayData[2].fieldValue = "Lunch";
		arrayData[3].fieldPath = "form[repDetail][1][descRep]";
		arrayData[3].fieldFindType = FieldKeyType.ID;
		arrayData[3].fieldType = FieldType.TEXTBOX;
		arrayData[3].fieldValue = "Lunch";
		arrayData[4].fieldPath = "form[repDetail][1][receiptRep]";
		arrayData[4].fieldFindType = FieldKeyType.ID;
		arrayData[4].fieldType = FieldType.DROPDOWN;
		arrayData[4].fieldValue = "YES";
		arrayData[5].fieldPath = "form[repDetail][1][amountRep]";
		arrayData[5].fieldFindType = FieldKeyType.ID;
		arrayData[5].fieldType = FieldType.TEXTBOX;
		arrayData[5].fieldValue = "100";
		arrayData[6].fieldPath = "form[repDetail][1][repBillable]";
		arrayData[6].fieldFindType = FieldKeyType.ID;
		arrayData[6].fieldType = FieldType.DROPDOWN;
		arrayData[6].fieldValue = "Yes";
		arrayData[7].fieldPath = "form[repDetail][1][cost_center]";
		arrayData[7].fieldFindType = FieldKeyType.ID;
		arrayData[7].fieldType = FieldType.DROPDOWN;
		arrayData[7].fieldValue = "Sales";
		arrayData[8].fieldPath = "form[repDetail][2][dateRep]";
		arrayData[8].fieldFindType = FieldKeyType.ID;
		arrayData[8].fieldType = FieldType.READONLY;
		arrayData[8].fieldValue = "31/12/2012";
		arrayData[9].fieldPath = "form[repDetail][2][categoryRep]";
		arrayData[9].fieldFindType = FieldKeyType.ID;
		arrayData[9].fieldType = FieldType.DROPDOWN;
		arrayData[9].fieldValue = "Meals";
		arrayData[10].fieldPath = "form[repDetail][2][expenseRep1]";
		arrayData[10].fieldFindType = FieldKeyType.ID;
		arrayData[10].fieldType = FieldType.DROPDOWN;
		arrayData[10].fieldValue = "Lunch";
		arrayData[11].fieldPath = "form[repDetail][2][descRep]";
		arrayData[11].fieldFindType = FieldKeyType.ID;
		arrayData[11].fieldType = FieldType.TEXTBOX;
		arrayData[11].fieldValue = "Lunch";
		arrayData[12].fieldPath = "form[repDetail][2][receiptRep]";
		arrayData[12].fieldFindType = FieldKeyType.ID;
		arrayData[12].fieldType = FieldType.DROPDOWN;
		arrayData[12].fieldValue = "YES";
		arrayData[13].fieldPath = "form[repDetail][2][amountRep]";
		arrayData[13].fieldFindType = FieldKeyType.ID;
		arrayData[13].fieldType = FieldType.TEXTBOX;
		arrayData[13].fieldValue = "150";
		arrayData[14].fieldPath = "form[repDetail][2][repBillable]";
		arrayData[14].fieldFindType = FieldKeyType.ID;
		arrayData[14].fieldType = FieldType.DROPDOWN;
		arrayData[14].fieldValue = "Yes";
		arrayData[15].fieldPath = "form[repDetail][2][cost_center]";
		arrayData[15].fieldFindType = FieldKeyType.ID;
		arrayData[15].fieldType = FieldType.DROPDOWN;
		arrayData[15].fieldValue = "Sales";
		arrayData[16].fieldPath = "form[repBut]";
		arrayData[16].fieldFindType = FieldKeyType.ID;
		arrayData[16].fieldType = FieldType.BUTTON;
		arrayData[16].fieldValue = "";
		Pages.InputDocProcess().openCaseFrame();
		WebElement btnLink= Browser.driver().findElement(By.id("form[repDetail][addLink]"));
		btnLink.click();
		Assert.assertTrue(FormFiller.formFillElements(arrayData));
		FormFieldData[] arrayData2 = new FormFieldData[1];
		arrayData2[0] = new FormFieldData();
		arrayData2[0].fieldPath = "form[BTN_SUBMIT]";
		arrayData2[0].fieldFindType = FieldKeyType.ID;
		arrayData2[0].fieldType = FieldType.BUTTON;
		arrayData2[0].fieldValue = "";
		Pages.InputDocProcess().openCaseFrame();
		Assert.assertTrue(FormFiller.formFillElements(arrayData2));
		Assert.assertTrue(Pages.InputDocProcess().continuebtn());
		Pages.Main().logout();
	}

	@Test
	public void approveReport() throws FileNotFoundException, IOException, Exception{

		Pages.Login().gotoUrl();
		Pages.Login().loginUser("Ian", "sample", "");
		Pages.Main().goHome();
		Assert.assertTrue("The case does not exist in Inbox", Pages.Home().existCase(caseNum));	
		Pages.Home().openCase(caseNum);
		FormFieldData[] arrayData = new FormFieldData[3];
		arrayData[0] = new FormFieldData();
		arrayData[1] = new FormFieldData();
		arrayData[2] = new FormFieldData();
		arrayData[0].fieldPath = "form[repDetail][1][commentRepSup]";
		arrayData[0].fieldFindType = FieldKeyType.ID;
		arrayData[0].fieldType = FieldType.TEXTBOX;
		arrayData[0].fieldValue = "4 people";
		arrayData[1].fieldPath = "form[repDetail][2][commentRepSup]";
		arrayData[1].fieldFindType = FieldKeyType.ID;
		arrayData[1].fieldType = FieldType.TEXTBOX;
		arrayData[1].fieldValue = "6 people";
		arrayData[2].fieldPath = "form[repBut]";
		arrayData[2].fieldFindType = FieldKeyType.ID;
		arrayData[2].fieldType = FieldType.BUTTON;
		arrayData[2].fieldValue = "";
		Pages.InputDocProcess().openCaseFrame();
		Assert.assertTrue(FormFiller.formFillElements(arrayData));
		Assert.assertTrue(Pages.InputDocProcess().continuebtn());
		Pages.Main().logout();
	}

	@Test
	public void reinburseExpenses() throws FileNotFoundException, IOException, Exception{

		Pages.Login().gotoUrl();
		Pages.Login().loginUser("Jason", "sample", "");
		Pages.Main().goHome();
		Assert.assertTrue("The case does not exist in Inbox", Pages.Home().existCase(caseNum));	
		Pages.Home().openCase(caseNum);
		FormFieldData[] arrayData2 = new FormFieldData[2];
		arrayData2[0] = new FormFieldData();
		arrayData2[1] = new FormFieldData();
		arrayData2[0].fieldPath = "form[repPayType]";
		arrayData2[0].fieldFindType = FieldKeyType.ID;
		arrayData2[0].fieldType = FieldType.DROPDOWN;
		arrayData2[0].fieldValue = "Cash";
		arrayData2[1].fieldPath = "form[repBut]";
		arrayData2[1].fieldFindType = FieldKeyType.ID;
		arrayData2[1].fieldType = FieldType.BUTTON;
		arrayData2[1].fieldValue = "";
		Pages.InputDocProcess().openCaseFrame();
		Assert.assertTrue(FormFiller.formFillElements(arrayData2));
		FormFieldData[] arrayData3 = new FormFieldData[1];
		arrayData3[0] = new FormFieldData();
		arrayData3[0].fieldPath = "form[NEXT_STEP]";
		arrayData3[0].fieldFindType = FieldKeyType.ID;
		arrayData3[0].fieldType = FieldType.BUTTON;
		arrayData3[0].fieldValue = "";
		Pages.InputDocProcess().openCaseFrame();
		Assert.assertTrue(FormFiller.formFillElements(arrayData3));
		Assert.assertTrue(Pages.InputDocProcess().continuebtn());
		Pages.Main().logout();
	}

}