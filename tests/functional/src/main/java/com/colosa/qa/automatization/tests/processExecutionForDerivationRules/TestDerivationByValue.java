package com.colosa.qa.automatization.tests.processExecutionForDerivationRules;

import org.junit.Assert;
import org.junit.AfterClass;
import org.junit.Test;

import com.colosa.qa.automatization.pages.*;
import com.colosa.qa.automatization.common.*;
import org.openqa.selenium.WebElement;

import java.io.FileNotFoundException;
import java.io.IOException;

public class TestDerivationByValue{
	protected static int caseNum;

	@Test
	public void runProcess() throws FileNotFoundException, IOException, Exception{

		Pages.Login().gotoUrl();
		Pages.Login().loginUser("iver", "sample", "workflow");
		Pages.Main().goHome();	
		caseNum = Pages.Home().startCase("Bug 4992 (Task 1)");
		FormFieldData[] arrayData = new FormFieldData[3];
		arrayData[0] = new FormFieldData();
		arrayData[1] = new FormFieldData();
		arrayData[2] = new FormFieldData();

		arrayData[0].fieldPath = "form[nombre]";
		arrayData[0].fieldFindType = FieldKeyType.ID;
		arrayData[0].fieldType = FieldType.TEXTBOX;
		arrayData[0].fieldValue = "Ernesto Vega";
		arrayData[1].fieldPath = "form[comentarios]";
		arrayData[1].fieldFindType = FieldKeyType.ID;
		arrayData[1].fieldType = FieldType.TEXTAREA;
		arrayData[1].fieldValue = "Prueba";
		arrayData[2].fieldPath = "form[next]";
		arrayData[2].fieldFindType = FieldKeyType.ID;
		arrayData[2].fieldType = FieldType.BUTTON;
		arrayData[2].fieldValue = "";
		Pages.InputDocProcess().openCaseFrame();
		Assert.assertTrue(FormFiller.formFillElements(arrayData));
		Pages.InputDocProcess().continuebtn();
		Pages.Main().logout();
	}

	@Test
	public void openAndRunCase() throws FileNotFoundException, IOException, Exception{

		Pages.Login().gotoUrl();
		Pages.Login().loginUser("admin", "admin", "");
		Pages.Main().goHome();
		Pages.Home().openCase(caseNum);
		Pages.InputDocProcess().openCaseFrame();
		Pages.InputDocProcess().continuebtn();
		Pages.Main().logout();

	}

	@Test
	public void openAndEndCase() throws FileNotFoundException, IOException, Exception{

		Pages.Login().gotoUrl();
		Pages.Login().loginUser("iver", "sample", "");
		Pages.Main().goHome();
		Pages.Home().openCase(caseNum);
		Pages.InputDocProcess().openCaseFrame();
		Pages.InputDocProcess().continuebtn();
		Pages.Main().logout();

	}
}