package com.colosa.qa.automatization.tests.derivationRules;

import org.junit.Assert;
import org.junit.AfterClass;
import org.junit.Test;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import com.colosa.qa.automatization.pages.*;
import com.colosa.qa.automatization.common.*;

import java.io.FileNotFoundException;
import java.io.IOException;

 public class TestDerivSelect{
@Test
 	public void runProcess() throws Exception{
 		Pages.Login().gotoUrl();
   	Pages.Login().loginUser("admin","admin","workflow");
    Pages.Main().goHome();
    int casenumber=Pages.Home().startCase("TestDerivSelect (Task 1)");
    openCaseFrame();
    FormFieldData[] fieldArray=new FormFieldData[4];

    fieldArray[0]=new FormFieldData();
    fieldArray[1]=new FormFieldData();
    fieldArray[2]=new FormFieldData();
    fieldArray[3]=new FormFieldData();

    fieldArray[0].fieldPath="form[nombre]";
    fieldArray[0].fieldFindType=FieldKeyType.ID;
    fieldArray[0].fieldType=FieldType.TEXTBOX;
    fieldArray[0].fieldValue="rodrigo ivan";

    fieldArray[1].fieldPath="form[monto]";
    fieldArray[1].fieldFindType=FieldKeyType.ID;
    fieldArray[1].fieldType=FieldType.TEXTBOX;
    fieldArray[1].fieldValue="5300";

    fieldArray[2].fieldPath="form[tipocuenta]";
    fieldArray[2].fieldFindType=FieldKeyType.ID;
    fieldArray[2].fieldType=FieldType.DROPDOWN;
    fieldArray[2].fieldValue="caja de ahorro";

    fieldArray[3].fieldPath="form[send]";
    fieldArray[3].fieldFindType=FieldKeyType.ID;
    fieldArray[3].fieldType=FieldType.BUTTON;
    fieldArray[3].fieldValue="";

    FormFiller.formFillElements(fieldArray);
    Assert.assertTrue("button continue does not exist in this dynaform", Browser.elementExists("TestDerivSelect.webelement.continue"));
   Pages.InputDocProcess().continuebtn();
   Pages.Home().gotoInbox();
    Assert.assertTrue("The case does not exist in inbox", Pages.Home().existCase(casenumber));
    Pages.Home().openCase(casenumber);
    openCaseFrame();
    FormFieldData[] fieldArray1=new FormFieldData[1];
    fieldArray1[0]=new FormFieldData();

    fieldArray1[0].fieldPath="form[send]";
    fieldArray1[0].fieldFindType=FieldKeyType.ID;
    fieldArray1[0].fieldType=FieldType.BUTTON;
    fieldArray1[0].fieldValue="";

    FormFiller.formFillElements(fieldArray1);
     Assert.assertTrue("button continue does not exist in this dynaform", Browser.elementExists("TestSimpleProcess.webelement.continue"));
    Pages.InputDocProcess().continuebtn();


 	}
 	public void openCaseFrame() throws Exception{
	    Browser.driver().switchTo().defaultContent();
		Browser.driver().switchTo().frame("casesFrame");
		Browser.driver().switchTo().frame("casesSubFrame");
		Browser.driver().switchTo().frame("openCaseFrame");
}


 }