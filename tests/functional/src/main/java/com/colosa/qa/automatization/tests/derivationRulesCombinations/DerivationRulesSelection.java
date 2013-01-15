package com.colosa.qa.automatization.tests.derivationRulesCombinations;

import org.junit.Assert;
import org.junit.AfterClass;
import org.junit.Test;

import com.colosa.qa.automatization.pages.*;
import com.colosa.qa.automatization.common.*;
import org.openqa.selenium.WebElement;

import java.io.FileNotFoundException;
import java.io.IOException;

public class DerivationRulesSelection{

	protected static int caseNum;

	@Test
	public void derivationRulesSelection() throws FileNotFoundException, IOException, Exception{

		Pages.Login().gotoUrl();
		Pages.Login().loginUser("admin", "admin", "workflow");
		Pages.Main().goHome();	
		caseNum = Pages.Home().startCase("Derivation rules - selection (Init)");
		Pages.DynaformExecution().intoDynaform();
		FormFieldData[] fieldArray = new FormFieldData[1];
		fieldArray[0] = new FormFieldData();
		fieldArray[0].fieldPath = "//*[@id='frmDerivation']/div/div[2]/table/tbody/tr/td/table/tbody/tr[8]/td/input";
		fieldArray[0].fieldFindType = FieldKeyType.XPATH;
		fieldArray[0].fieldType = FieldType.BUTTON;
		fieldArray[0].fieldValue = "";
		FormFiller.formFillElements(fieldArray);
		Pages.DynaformExecution().outDynaform();
		Pages.Main().logout();
	    //cyclical task
		Pages.Login().gotoUrl();
		Pages.Login().loginUser("chris", "sample", "workflow");
		Pages.Main().goHome();	
    	Pages.Home().gotoInbox();
		Assert.assertTrue("The case does not exist in Inbox", Pages.Home().existCase(caseNum));
		Pages.Home().openCase(caseNum);
		Pages.DynaformExecution().intoDynaform();
		Pages.DynaformExecution().setFieldValue("TASKS][2][USR_UID", "Swan, William");
		FormFieldData[] fieldArray2 = new FormFieldData[1];
		fieldArray2[0] = new FormFieldData();
		fieldArray2[0].fieldPath = "//*[@id='frmDerivation']/div/div[2]/table/tbody/tr/td/table/tbody/tr[13]/td/input";
		fieldArray2[0].fieldFindType = FieldKeyType.XPATH;
		fieldArray2[0].fieldType = FieldType.BUTTON;
		fieldArray2[0].fieldValue = "";
		FormFiller.formFillElements(fieldArray2);
		Pages.DynaformExecution().outDynaform();
		Pages.Main().logout();
		//Manual task
		Pages.Login().gotoUrl();
		Pages.Login().loginUser("william", "sample", "workflow");
		Pages.Main().goHome();	
    	Pages.Home().gotoInbox();
		Assert.assertTrue("The case does not exist in Inbox", Pages.Home().existCase(caseNum));
		Pages.Home().openCase(caseNum);
		Pages.DynaformExecution().intoDynaform();
		Pages.DynaformExecution().setFieldValue("name", "Charles Puyol");
		Pages.DynaformExecution().setFieldValue("amount", "3000");
		Pages.DynaformExecution().setFieldValue("send", "");
		FormFieldData[] fieldArray3 = new FormFieldData[1];
		fieldArray3[0] = new FormFieldData();
		fieldArray3[0].fieldPath = "//*[@id='frmDerivation']/div/div[2]/table/tbody/tr/td/table/tbody/tr[8]/td/input";
		fieldArray3[0].fieldFindType = FieldKeyType.XPATH;
		fieldArray3[0].fieldType = FieldType.BUTTON;
		fieldArray3[0].fieldValue = "";
		FormFiller.formFillElements(fieldArray3);
		Pages.DynaformExecution().outDynaform();
		Pages.Main().logout();
		//Value based task
		Pages.Login().gotoUrl();
		Pages.Login().loginUser("ezequiel", "sample", "workflow");
		Pages.Main().goHome();	
    	Pages.Home().gotoInbox();
		Assert.assertTrue("The case does not exist in Inbox", Pages.Home().existCase(caseNum));
		Pages.Home().openCase(caseNum);
		Pages.DynaformExecution().intoDynaform();
		FormFieldData[] fieldArray4 = new FormFieldData[1];
		fieldArray4[0] = new FormFieldData();
		fieldArray4[0].fieldPath = "//*[@id='frmDerivation']/div/div[2]/table/tbody/tr/td/table/tbody/tr[8]/td/input";
		fieldArray4[0].fieldFindType = FieldKeyType.XPATH;
		fieldArray4[0].fieldType = FieldType.BUTTON;
		fieldArray4[0].fieldValue = "";
		FormFiller.formFillElements(fieldArray4);
		Pages.DynaformExecution().outDynaform();
		Pages.Main().logout();
		//Report to task
		Pages.Login().gotoUrl();
		Pages.Login().loginUser("zachary", "sample", "workflow");
		Pages.Main().goHome();	
    	Pages.Home().gotoInbox();
		Assert.assertTrue("The case does not exist in Inbox", Pages.Home().existCase(caseNum));
		Pages.Home().openCase(caseNum);
		Pages.DynaformExecution().intoDynaform();
		FormFieldData[] fieldArray5 = new FormFieldData[1];
		fieldArray5[0] = new FormFieldData();
		fieldArray5[0].fieldPath = "//*[@id='frmDerivation']/div/div[2]/table/tbody/tr/td/table/tbody/tr[8]/td/input";
		fieldArray5[0].fieldFindType = FieldKeyType.XPATH;
		fieldArray5[0].fieldType = FieldType.BUTTON;
		fieldArray5[0].fieldValue = "";
		FormFiller.formFillElements(fieldArray5);
		Pages.DynaformExecution().outDynaform();
		Pages.Main().logout();
		//Self service task
		Pages.Login().gotoUrl();
		Pages.Login().loginUser("chris", "sample", "workflow");
		Pages.Main().goHome();	
		Pages.Home().gotoUnassigned();
		Assert.assertTrue("The case does not exist in Unassigned", Pages.Home().existCase(caseNum));
		Pages.Home().openCase(caseNum);
		Pages.DynaformExecution().intoDynaform();
		Pages.DynaformExecution().setFieldValue("BTN_CATCH", "");
		Pages.DynaformExecution().setFieldValue("send", "");
		FormFieldData[] fieldArray6 = new FormFieldData[1];
		fieldArray6[0] = new FormFieldData();
		fieldArray6[0].fieldPath = "//*[@id='frmDerivation']/div/div[2]/table/tbody/tr/td/table/tbody/tr[8]/td/input";
		fieldArray6[0].fieldFindType = FieldKeyType.XPATH;
		fieldArray6[0].fieldType = FieldType.BUTTON;
		fieldArray6[0].fieldValue = "";
		FormFiller.formFillElements(fieldArray6);
		Pages.DynaformExecution().outDynaform();
		Pages.Main().logout();
		//Self Service Value Based task
		Pages.Login().gotoUrl();
		Pages.Login().loginUser("william", "sample", "workflow");
		Pages.Main().goHome();	
		Pages.Home().gotoUnassigned();
		Assert.assertTrue("The case does not exist in Unassigned", Pages.Home().existCase(caseNum));
		Pages.Home().openCase(caseNum);
		Pages.DynaformExecution().intoDynaform();
		Pages.DynaformExecution().setFieldValue("BTN_CATCH", "");
		Pages.DynaformExecution().outDynaform();
		Pages.DynaformExecution().intoDynaform();
		FormFieldData[] fieldArray7 = new FormFieldData[1];
		fieldArray7[0] = new FormFieldData();
		fieldArray7[0].fieldPath = "//*[@id='frmDerivation']/div/div[2]/table/tbody/tr/td/table/tbody/tr[8]/td/input";
		fieldArray7[0].fieldFindType = FieldKeyType.XPATH;
		fieldArray7[0].fieldType = FieldType.BUTTON;
		fieldArray7[0].fieldValue = "";
		FormFiller.formFillElements(fieldArray7);
		Pages.DynaformExecution().outDynaform();
		Pages.Main().logout();
		//Open cases to verify Cyclical assigmnent
		Pages.Login().gotoUrl();
		Pages.Login().loginUser("admin", "admin", "workflow");
		Pages.Main().goHome();	
		caseNum = Pages.Home().startCase("Derivation rules - sequential (Init)");
		Pages.DynaformExecution().intoDynaform();
		FormFieldData[] fieldArray8 = new FormFieldData[1];
		fieldArray8[0] = new FormFieldData();
		fieldArray8[0].fieldPath = "//*[@id='frmDerivation']/div/div[2]/table/tbody/tr/td/table/tbody/tr[8]/td/input";
		fieldArray8[0].fieldFindType = FieldKeyType.XPATH;
		fieldArray8[0].fieldType = FieldType.BUTTON;
		fieldArray8[0].fieldValue = "";
		FormFiller.formFillElements(fieldArray8);
		Pages.DynaformExecution().outDynaform();
		Pages.Main().logout();
		Pages.Login().gotoUrl();
		Pages.Login().loginUser("admin", "admin", "workflow");				
		Pages.Main().goHome();	
		caseNum = Pages.Home().startCase("Derivation rules - sequential (Init)");
		Pages.DynaformExecution().intoDynaform();
		FormFieldData[] fieldArray9 = new FormFieldData[1];
		fieldArray9[0] = new FormFieldData();
		fieldArray9[0].fieldPath = "//*[@id='frmDerivation']/div/div[2]/table/tbody/tr/td/table/tbody/tr[8]/td/input";
		fieldArray9[0].fieldFindType = FieldKeyType.XPATH;
		fieldArray9[0].fieldType = FieldType.BUTTON;
		fieldArray9[0].fieldValue = "";
		FormFiller.formFillElements(fieldArray9);
		Pages.DynaformExecution().outDynaform();
		Pages.Main().logout();
		//Veryfy if the task is assigned succesfuly
		Pages.Login().gotoUrl();
		Pages.Login().loginUser("zachary", "sample", "workflow");
		Pages.Main().goHome();	
    	Pages.Home().gotoInbox();
		Assert.assertTrue("The case does not exist in Inbox", Pages.Home().existCase(caseNum));
		Pages.DynaformExecution().outDynaform();
		Pages.Main().logout();
	}


}