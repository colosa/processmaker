package com.colosa.qa.automatization.tests.suggestFields;

import org.junit.Assert;
import org.junit.AfterClass;
import org.junit.Test;

import com.colosa.qa.automatization.pages.*;
import com.colosa.qa.automatization.common.*;
import org.openqa.selenium.WebElement;

import java.io.FileNotFoundException;
import java.io.IOException;

public class TestSuggestFields{

	protected static int caseNum;
	protected static String country = "Albania";
	protected static String state = "Bahia";
	protected static String city = "Baago";

	@Test
	public void testCase() throws FileNotFoundException, IOException, Exception{

		Pages.Login().gotoUrl();
		Pages.Login().loginUser("admin", "admin", "workflow");
		Pages.Main().goHome();	
		caseNum = Pages.Home().startCase("Suggest Fields (Task 1)");

		FormFieldData[] arrayData = new FormFieldData[4];
		arrayData[0] = new FormFieldData();
		arrayData[1] = new FormFieldData();
		arrayData[2] = new FormFieldData();
		arrayData[3] = new FormFieldData();

		arrayData[0].fieldPath = "form[Country_label]";
		arrayData[0].fieldFindType = FieldKeyType.ID;
		arrayData[0].fieldType = FieldType.SUGGEST;
		arrayData[0].fieldValue = country;
		arrayData[1].fieldPath = "form[State_label]";
		arrayData[1].fieldFindType = FieldKeyType.ID;
		arrayData[1].fieldType = FieldType.SUGGEST;
		arrayData[1].fieldValue = state;
		arrayData[2].fieldPath = "form[City_label]";
		arrayData[2].fieldFindType = FieldKeyType.ID;
		arrayData[2].fieldType = FieldType.SUGGEST;
		arrayData[2].fieldValue = city;
		arrayData[3].fieldPath = "form[Send]";
		arrayData[3].fieldFindType = FieldKeyType.ID;
		arrayData[3].fieldType = FieldType.BUTTON;
		arrayData[3].fieldValue = "";

		Pages.InputDocProcess().openCaseFrame();
		Assert.assertTrue(FormFiller.formFillElements(arrayData));
		Assert.assertTrue(Pages.InputDocProcess().continuebtn());
		Pages.Main().logout();


	/*}

	@Test
	public void continueTestCase() throws FileNotFoundException, IOException, Exception{*/

		Pages.Login().gotoUrl();
		Pages.Login().loginUser("iver", "sample", "");
		Pages.Main().goHome();	
		Pages.Home().openCase(caseNum);

		FormFieldData[] arrayData1 = new FormFieldData[3];
		arrayData1[0] = new FormFieldData();
		arrayData1[1] = new FormFieldData();
		arrayData1[2] = new FormFieldData();
		
		arrayData1[0].fieldPath = "form[Country_label]";
		arrayData1[0].fieldFindType = FieldKeyType.ID;
		arrayData1[1].fieldPath = "form[State_label]";
		arrayData1[1].fieldFindType = FieldKeyType.ID;
		arrayData1[2].fieldPath = "form[City_label]";
		arrayData1[2].fieldFindType = FieldKeyType.ID;
				
		FormFieldData[] arrayData2 = new FormFieldData[1];
		arrayData2[0] = new FormFieldData();
		
		arrayData2[0].fieldPath = "form[Send]";
		arrayData2[0].fieldFindType = FieldKeyType.ID;
		arrayData2[0].fieldType = FieldType.BUTTON;
		arrayData2[0].fieldValue = "";

		Pages.InputDocProcess().openCaseFrame();
		String varVal = Value.getValue(arrayData1[0].fieldFindType, arrayData1[0].fieldPath);

		Assert.assertEquals(country, Value.getValue(arrayData1[0].fieldFindType, arrayData1[0].fieldPath));
		Assert.assertEquals(state, Value.getValue(arrayData1[1].fieldFindType, arrayData1[1].fieldPath));
		Assert.assertEquals(city, Value.getValue(arrayData1[2].fieldFindType, arrayData1[2].fieldPath));
		Assert.assertTrue(FormFiller.formFillElements(arrayData2));
		Assert.assertTrue(Pages.InputDocProcess().continuebtn());
		Pages.Main().logout();


	}


}