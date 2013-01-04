package com.colosa.qa.automatization.tests.derivationRules;

import org.junit.Assert;
import org.junit.AfterClass;
import org.junit.Test;

import com.colosa.qa.automatization.pages.*;
import com.colosa.qa.automatization.common.*;
import org.openqa.selenium.WebElement;

import java.io.FileNotFoundException;
import java.io.IOException;

public class TestSequential{

	@Test
	public void executeProcess() throws FileNotFoundException, IOException, Exception{

	Pages.Login().gotoUrl();
	Pages.Login().loginUser("admin", "admin", "workflow");
	Pages.Main().goHome();
	Pages.Home().gotoNewCase();	
	int caseNumber = Pages.Home().startCase("Pruebas Integrales - Sequential (Task 1)");	

	Pages.InputDocProcess().openCaseFrame();
	FormFieldData[] fieldArray = new FormFieldData[6];
	fieldArray[0] = new FormFieldData();
	fieldArray[1] = new FormFieldData();
	fieldArray[2] = new FormFieldData();
	fieldArray[3] = new FormFieldData();
	fieldArray[4] = new FormFieldData();
	fieldArray[5] = new FormFieldData();
	fieldArray[0].fieldPath = "form[nombre]";
	fieldArray[0].fieldFindType = FieldKeyType.ID;
	fieldArray[0].fieldType = FieldType.TEXTBOX;
	fieldArray[0].fieldValue = "Ademar";
	fieldArray[1].fieldPath = "form[apellido]";
	fieldArray[1].fieldFindType = FieldKeyType.ID;
	fieldArray[1].fieldType = FieldType.TEXTBOX;
	fieldArray[1].fieldValue = "Hurtado";
	fieldArray[2].fieldPath = "form[monto]";
	fieldArray[2].fieldFindType = FieldKeyType.ID;
	fieldArray[2].fieldType = FieldType.TEXTBOX;
	fieldArray[2].fieldValue = "180.00";
	fieldArray[3].fieldPath = "form[comentarios]";
	fieldArray[3].fieldFindType = FieldKeyType.ID;
	fieldArray[3].fieldType = FieldType.TEXTBOX;
	fieldArray[3].fieldValue = "Payment for material reservation";
	fieldArray[4].fieldPath = "form[fecha]";
	fieldArray[4].fieldFindType = FieldKeyType.ID;
	fieldArray[4].fieldType = FieldType.READONLY;
	fieldArray[4].fieldValue = "2012-12-28";
	fieldArray[5].fieldPath = "form[continuar]";
	fieldArray[5].fieldFindType = FieldKeyType.ID;
	fieldArray[5].fieldType = FieldType.BUTTON;
	fieldArray[5].fieldValue = "";
	FormFiller.formFillElements(fieldArray);
	Assert.assertTrue("The button Continue does not exit in this form", Browser.elementExists("inputDocProcess.webelement.continue"));
	Pages.InputDocProcess().continuebtn();
	Assert.assertTrue("The case does not exist in this Inbox", Pages.Home().existCase(caseNumber));		
	Pages.Home().openCase(caseNumber);
	Pages.InputDocProcess().openCaseFrame();
	FormFieldData[] fieldArray2 = new FormFieldData[1];
	fieldArray2[0] = new FormFieldData();
	fieldArray2[0].fieldPath = "form[continuar]";
	fieldArray2[0].fieldFindType = FieldKeyType.ID;
	fieldArray2[0].fieldType = FieldType.BUTTON;
	fieldArray2[0].fieldValue = "";
	FormFiller.formFillElements(fieldArray2);
	Assert.assertTrue("The button Continue does not exit in this form", Browser.elementExists("inputDocProcess.webelement.continue"));
	Pages.InputDocProcess().continuebtn();
	Assert.assertTrue("The case does not exist in this Inbox", Pages.Home().existCase(caseNumber));
	Pages.Home().openCase(caseNumber);	
	Pages.InputDocProcess().openCaseFrame();
	FormFieldData[] fieldArray3 = new FormFieldData[1];
	fieldArray3[0] = new FormFieldData();
	fieldArray3[0].fieldPath = "form[continuar]";
	fieldArray3[0].fieldFindType = FieldKeyType.ID;
	fieldArray3[0].fieldType = FieldType.BUTTON;
	fieldArray3[0].fieldValue = "";
	FormFiller.formFillElements(fieldArray3);
	Assert.assertTrue("The button Continue does not exit in this form", Browser.elementExists("inputDocProcess.webelement.continue"));
	Pages.InputDocProcess().continuebtn();	

}

}