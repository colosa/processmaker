package com.colosa.qa.automatization.tests.PMFields;

import org.junit.Assert;
import org.junit.After;
import org.junit.AfterClass;
import org.junit.Test;

import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import com.colosa.qa.automatization.pages.*;
import com.colosa.qa.automatization.common.*;

import java.io.FileNotFoundException;
import java.io.IOException;

public class CurrencyFieldTest{

@Test
 	public void runProcess() throws Exception{
 		Pages.Login().gotoUrl();
   	Pages.Login().loginUser("admin","admin","workflow");
    Pages.Main().goHome();
    int casenumber=Pages.Home().startCase("TestCurrency (Task 1)");
    Pages.DynaformExecution().intoDynaform();
    
    FormFieldData[] fieldArray=new FormFieldData[2];

    fieldArray[0]=new FormFieldData();
    fieldArray[1]=new FormFieldData();
    
    fieldArray[0].fieldPath="form[CURRENCY]";
    fieldArray[0].fieldFindType=FieldKeyType.ID;
    fieldArray[0].fieldType=FieldType.TEXTBOX;
    fieldArray[0].fieldValue="123456789.02";

    fieldArray[1].fieldPath="form[SUBMIT]";
    fieldArray[1].fieldFindType=FieldKeyType.ID;
    fieldArray[1].fieldType=FieldType.BUTTON;
    fieldArray[1].fieldValue="";

    FormFiller.formFillElements(fieldArray);
    Pages.InputDocProcess().continuebtn();
    
    Pages.Home().gotoInbox();
    Assert.assertTrue("The case does not exist in inbox", Pages.Home().existCase(casenumber));
    Pages.Home().openCase(casenumber);
    Pages.DynaformExecution().intoDynaform();
    FormFieldData[] fieldArray1=new FormFieldData[1];
    fieldArray1[0]=new FormFieldData();
		
    fieldArray1[0].fieldPath="form[SUBMIT]";
    fieldArray1[0].fieldFindType=FieldKeyType.ID;
    fieldArray1[0].fieldType=FieldType.BUTTON;
    fieldArray1[0].fieldValue="";
		
		String fieldCURRENCY = Pages.DynaformExecution().getFieldProperty("CURRENCY","value");
		
	  FormFiller.formFillElements(fieldArray1);
    Assert.assertEquals("Mask does not exist", fieldCURRENCY, "123,456,789.02 $us");
    Pages.InputDocProcess().continuebtn();
  }
  @After
    public void cleanup(){
        Browser.close();
    } 	
}