package com.colosa.qa.automatization.tests.TestProcessExecutionforDerivationRules;

import org.junit.Assert;
import org.junit.AfterClass;
import org.junit.Test;

import com.colosa.qa.automatization.pages.*;
import com.colosa.qa.automatization.common.*;
import org.openqa.selenium.WebElement;

import java.io.FileNotFoundException;
import java.io.IOException;

public class TestProcessExecutionforDerivationRules{

	@Test
	public void executeProcess() throws FileNotFoundException, IOException, Exception{

		//Open process
		Pages.Login().gotoUrl();
		Pages.Login().loginUser("admin", "admin", "wsqa");
		Pages.Main().goHome();
		Pages.Home().gotoNewCase();	
		int caseNumber = Pages.Home().startCase("Pruebas Integrales - Derivation rules (Task 1)");	
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
		fieldArray[2].fieldValue = "3000.00";
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
		Assert.assertTrue("The button Continue does not exit in this form", Pages.InputDocProcess().continuebtn());	
		Pages.Main().logout();
		//Open the task with a user of assigned group through the Trigger.
		Pages.Login().gotoUrl();
		Pages.Login().loginUser("juan", "sample", "");
		Pages.Main().goHome();
		Pages.Home().gotoUnassigned();	
		Assert.assertTrue("The case does not exist in Unassigned", Pages.Home().existCase(caseNumber));		
		Pages.Home().openCase(caseNumber);	
		Pages.InputDocProcess().openCaseFrame();
		FormFieldData[] fieldArray2 = new FormFieldData[1];
		fieldArray2[0] = new FormFieldData();
		fieldArray2[0].fieldPath = "form[BTN_CATCH]";
		fieldArray2[0].fieldFindType = FieldKeyType.ID;
		fieldArray2[0].fieldType = FieldType.BUTTON;
		fieldArray2[0].fieldValue = "";
		FormFiller.formFillElements(fieldArray2);
		Pages.InputDocProcess().openCaseFrame();
		FormFieldData[] fieldArray4 = new FormFieldData[1];
		fieldArray4[0] = new FormFieldData();
		fieldArray4[0].fieldPath = "form[continuar]";
		fieldArray4[0].fieldFindType = FieldKeyType.ID;
		fieldArray4[0].fieldType = FieldType.BUTTON;
		fieldArray4[0].fieldValue = "";
		FormFiller.formFillElements(fieldArray4);
		Assert.assertTrue("The button Continue does not exit in this form", Pages.InputDocProcess().continuebtn());	
		Pages.Main().logout();
		//Open the task with a user of the some group assign with Sekfservice.
		Pages.Login().gotoUrl();
		Pages.Login().loginUser("jason", "sample", "");
		Pages.Main().goHome();
		Pages.Home().gotoUnassigned();	
		Assert.assertTrue("The case does not exist in Unassigned", Pages.Home().existCase(caseNumber));		
		Pages.Home().openCase(caseNumber);	
		Pages.InputDocProcess().openCaseFrame();
		FormFieldData[] fieldArray5 = new FormFieldData[1];
		fieldArray5[0] = new FormFieldData();
		fieldArray5[0].fieldPath = "form[BTN_CATCH]";
		fieldArray5[0].fieldFindType = FieldKeyType.ID;
		fieldArray5[0].fieldType = FieldType.BUTTON;
		fieldArray5[0].fieldValue = "";
		FormFiller.formFillElements(fieldArray5);
		Pages.InputDocProcess().openCaseFrame();
		FormFieldData[] fieldArray6 = new FormFieldData[1];
		fieldArray6[0] = new FormFieldData();
		fieldArray6[0].fieldPath = "form[continuar]";
		fieldArray6[0].fieldFindType = FieldKeyType.ID;
		fieldArray6[0].fieldType = FieldType.BUTTON;
		fieldArray6[0].fieldValue = "";
		FormFiller.formFillElements(fieldArray6);
		Pages.InputDocProcess().openCaseFrame();
		FormFieldData[] fieldArray7 = new FormFieldData[1];
		fieldArray7[0] = new FormFieldData();
		fieldArray7[0].fieldPath = "//*[@id='frmDerivation']/div/div[2]/table/tbody/tr/td/table/tbody/tr[14]/td/input";
		fieldArray7[0].fieldFindType = FieldKeyType.XPATH;
		fieldArray7[0].fieldType = FieldType.BUTTON;
		fieldArray7[0].fieldValue = "";
		FormFiller.formFillElements(fieldArray7);	
		Pages.Main().logout();
		//Check if the case does not exist in other groups
		Pages.Login().gotoUrl();
		Pages.Login().loginUser("juan", "sample", "");
		Pages.Main().goHome();
		Pages.Home().gotoUnassigned();	
		Assert.assertFalse("The case exist in Unassigned", Pages.Home().existCase(caseNumber));		
		//Open the task and execute the rule derivate by value.
		Pages.Login().gotoUrl();
		Pages.Login().loginUser("admin", "admin", "");
		Pages.Main().goHome();
		Pages.Home().gotoInbox();;	
		Assert.assertTrue("The case does not exist in Inbox", Pages.Home().existCase(caseNumber));		
		Pages.Home().openCase(caseNumber);
		Pages.InputDocProcess().openCaseFrame();
		FormFieldData[] fieldArray8 = new FormFieldData[3];
		fieldArray8[0] = new FormFieldData();
		fieldArray8[1] = new FormFieldData();
		fieldArray8[2] = new FormFieldData();
		fieldArray8[0].fieldPath = "form[aprobadopor]";
		fieldArray8[0].fieldFindType = FieldKeyType.ID;
		fieldArray8[0].fieldType = FieldType.TEXTBOX;
		fieldArray8[0].fieldValue = "Ademar Hurtado";
		fieldArray8[1].fieldPath = "form[aprobar]";
		fieldArray8[1].fieldFindType = FieldKeyType.ID;
		fieldArray8[1].fieldType = FieldType.DROPDOWN;
		fieldArray8[1].fieldValue = "SI";
		fieldArray8[2].fieldPath = "form[send]";
		fieldArray8[2].fieldFindType = FieldKeyType.ID;
		fieldArray8[2].fieldType = FieldType.BUTTON;
		fieldArray8[2].fieldValue = "";
		FormFiller.formFillElements(fieldArray8);
		Pages.InputDocProcess().openCaseFrame();
		Assert.assertTrue("The button Continue does not exit in this form", Pages.InputDocProcess().continuebtn());	
		Pages.Home().gotoInbox();
		Assert.assertTrue("The case does not exist in Inbox", Pages.Home().existCase(caseNumber));
		Pages.Home().openCase(caseNumber);
		Pages.InputDocProcess().openCaseFrame();		
		Assert.assertTrue("The button Continue does not exit in this form", Pages.InputDocProcess().continuebtn());	
		Pages.Main().logout();
}

}