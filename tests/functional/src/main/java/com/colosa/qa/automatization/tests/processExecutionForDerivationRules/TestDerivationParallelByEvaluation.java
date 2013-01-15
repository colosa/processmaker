package com.colosa.qa.automatization.tests.processExecutionForDerivationRules;

import org.junit.Assert;
import org.junit.AfterClass;
import org.junit.Test;

import com.colosa.qa.automatization.pages.*;
import com.colosa.qa.automatization.common.*;
import org.openqa.selenium.WebElement;

import java.io.FileNotFoundException;
import java.io.IOException;

public class TestDerivationParallelByEvaluation{

	protected static int caseNum;
	protected static String aprove = "Yes";

	@Test
	public void runProcess() throws FileNotFoundException, IOException, Exception{

		Pages.Login().gotoUrl();
		Pages.Login().loginUser("admin", "admin", "workflow");
		Pages.Main().goHome();	
		caseNum = Pages.Home().startCase("Pruebas Integrales - Parallel By Evaluation (Fork) y Parallel (Join) (Task 1)");
		FormFieldData[] arrayData = new FormFieldData[7];
		arrayData[0] = new FormFieldData();
		arrayData[1] = new FormFieldData();
		arrayData[2] = new FormFieldData();
		arrayData[3] = new FormFieldData();
		arrayData[4] = new FormFieldData();
		arrayData[5] = new FormFieldData();
		arrayData[6] = new FormFieldData();

		arrayData[0].fieldPath = "form[nombre]";
		arrayData[0].fieldFindType = FieldKeyType.ID;
		arrayData[0].fieldType = FieldType.TEXTBOX;
		arrayData[0].fieldValue = "Ernesto";
		arrayData[1].fieldPath = "form[apellido]";
		arrayData[1].fieldFindType = FieldKeyType.ID;
		arrayData[1].fieldType = FieldType.TEXTBOX;
		arrayData[1].fieldValue = "Vega";
		arrayData[2].fieldPath = "form[monto]";
		arrayData[2].fieldFindType = FieldKeyType.ID;
		arrayData[2].fieldType = FieldType.TEXTBOX;
		arrayData[2].fieldValue = "23565";
		arrayData[3].fieldPath = "form[comentarios]";
		arrayData[3].fieldFindType = FieldKeyType.ID;
		arrayData[3].fieldType = FieldType.TEXTAREA;
		arrayData[3].fieldValue = "Prueba";
		arrayData[4].fieldPath = "form[fecha]";
		arrayData[4].fieldFindType = FieldKeyType.ID;
		arrayData[4].fieldType = FieldType.TEXTBOX;
		arrayData[4].fieldValue = "1987-12-29";
		arrayData[5].fieldPath = "form[aprobado]";
		arrayData[5].fieldFindType = FieldKeyType.ID;
		arrayData[5].fieldType = FieldType.DROPDOWN;
		arrayData[5].fieldValue = aprove;
		arrayData[6].fieldPath = "form[continuar]";
		arrayData[6].fieldFindType = FieldKeyType.ID;
		arrayData[6].fieldType = FieldType.BUTTON;
		arrayData[6].fieldValue = "";
		Pages.InputDocProcess().openCaseFrame();
		Assert.assertTrue(FormFiller.formFillElements(arrayData));
		Assert.assertTrue(Pages.InputDocProcess().continuebtn());
		Pages.Main().logout();


	}

	@Test
	public void openAndRunCase() throws FileNotFoundException, IOException, Exception{

		if(aprove=="Yes"){
			Pages.Login().gotoUrl();
			Pages.Login().loginUser("iver", "sample", "");
			Pages.Main().goHome();
			Pages.Home().openCase(caseNum);
			FormFieldData[] arrayData = new FormFieldData[1];
			arrayData[0] = new FormFieldData();
			arrayData[0].fieldPath = "form[continuar]";
			arrayData[0].fieldFindType = FieldKeyType.ID;
			arrayData[0].fieldType = FieldType.BUTTON;
			arrayData[0].fieldValue = "";
			Pages.InputDocProcess().openCaseFrame();
			Assert.assertTrue(FormFiller.formFillElements(arrayData));
			Assert.assertTrue(Pages.InputDocProcess().continuebtn());
			Pages.Main().logout();
		}
	}

	@Test
	public void continueCase() throws FileNotFoundException, IOException, Exception{

		if(aprove=="Yes"){
			Pages.Login().gotoUrl();
			Pages.Login().loginUser("hector", "sample", "");
			Pages.Main().goHome();
			Pages.Home().openCase(caseNum);
			FormFieldData[] arrayData = new FormFieldData[1];
			arrayData[0] = new FormFieldData();
			arrayData[0].fieldPath = "form[continuar]";
			arrayData[0].fieldFindType = FieldKeyType.ID;
			arrayData[0].fieldType = FieldType.BUTTON;
			arrayData[0].fieldValue = "";
			Pages.InputDocProcess().openCaseFrame();
			Assert.assertTrue(FormFiller.formFillElements(arrayData));
			Assert.assertTrue(Pages.InputDocProcess().continuebtn());
			Pages.Main().logout();
		}
	}

	@Test
	public void openAndContinueCase() throws FileNotFoundException, IOException, Exception{
		if(aprove=="No"){
			Pages.Login().gotoUrl();
			Pages.Login().loginUser("ronald", "sample", "");
			Pages.Main().goHome();
			Pages.Home().openCase(caseNum);
			FormFieldData[] arrayData = new FormFieldData[1];
			arrayData[0] = new FormFieldData();
			arrayData[0].fieldPath = "form[continuar]";
			arrayData[0].fieldFindType = FieldKeyType.ID;
			arrayData[0].fieldType = FieldType.BUTTON;
			arrayData[0].fieldValue = "";
			Pages.InputDocProcess().openCaseFrame();
			Assert.assertTrue(FormFiller.formFillElements(arrayData));
			Assert.assertTrue(Pages.InputDocProcess().continuebtn());
			Pages.Main().logout();
		}	
	}

	@Test
	public void openAndEndCase() throws FileNotFoundException, IOException, Exception{

		
		Pages.Login().gotoUrl();
		Pages.Login().loginUser("admin", "admin", "");
		Pages.Main().goHome();
		Pages.Home().openCase(caseNum);
		FormFieldData[] arrayData = new FormFieldData[1];
		arrayData[0] = new FormFieldData();
		arrayData[0].fieldPath = "form[continuar]";
		arrayData[0].fieldFindType = FieldKeyType.ID;
		arrayData[0].fieldType = FieldType.BUTTON;
		arrayData[0].fieldValue = "";
		Pages.InputDocProcess().openCaseFrame();
		Assert.assertTrue(FormFiller.formFillElements(arrayData));
		Assert.assertTrue(Pages.InputDocProcess().continuebtn());
		Pages.Main().logout();
	
	}
}