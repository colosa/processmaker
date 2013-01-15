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
    
    FormFieldData[] fieldArray=new FormFieldData[8];

    fieldArray[0]=new FormFieldData();
    fieldArray[1]=new FormFieldData();
    fieldArray[2]=new FormFieldData();
    fieldArray[3]=new FormFieldData();
    fieldArray[4]=new FormFieldData();
    fieldArray[5]=new FormFieldData();
    fieldArray[6]=new FormFieldData();
    fieldArray[7]=new FormFieldData();
    
    fieldArray[0].fieldPath="form[CURRENCY_BEFORE]";
    fieldArray[0].fieldFindType=FieldKeyType.ID;
    fieldArray[0].fieldType=FieldType.TEXTBOX;
    fieldArray[0].fieldValue="123456789.02";
    
    fieldArray[1].fieldPath="form[CURRENCY_AFTER]";
    fieldArray[1].fieldFindType=FieldKeyType.ID;
    fieldArray[1].fieldType=FieldType.TEXTBOX;
    fieldArray[1].fieldValue="123456789.02";
    
    fieldArray[2].fieldPath="form[CURRENCY_BOTH]";
    fieldArray[2].fieldFindType=FieldKeyType.ID;
    fieldArray[2].fieldType=FieldType.TEXTBOX;
    fieldArray[2].fieldValue="123456789.02";
    
    fieldArray[3].fieldPath="form[CURRENCY_OTHERB]";
    fieldArray[3].fieldFindType=FieldKeyType.ID;
    fieldArray[3].fieldType=FieldType.TEXTBOX;
    fieldArray[3].fieldValue="123456789.02";
    
    fieldArray[4].fieldPath="form[CURRENCY_OTHERA]";
    fieldArray[4].fieldFindType=FieldKeyType.ID;
    fieldArray[4].fieldType=FieldType.TEXTBOX;
    fieldArray[4].fieldValue="123456789.02";
    
    fieldArray[5].fieldPath="form[CURRENCY_OTHER]";
    fieldArray[5].fieldFindType=FieldKeyType.ID;
    fieldArray[5].fieldType=FieldType.TEXTBOX;
    fieldArray[5].fieldValue="123456789.02";
    
		fieldArray[6].fieldPath="form[CURRENCY_NEGATIVE]";
    fieldArray[6].fieldFindType=FieldKeyType.ID;
    fieldArray[6].fieldType=FieldType.TEXTBOX;
    fieldArray[6].fieldValue="123456789.02";
    
    fieldArray[7].fieldPath="form[SUBMIT]";
    fieldArray[7].fieldFindType=FieldKeyType.ID;
    fieldArray[7].fieldType=FieldType.BUTTON;
    fieldArray[7].fieldValue="";

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
		
		String fieldCURRENCY_BEFORE = Pages.DynaformExecution().getFieldProperty("CURRENCY_BEFORE","value");
		String fieldCURRENCY_AFTER = Pages.DynaformExecution().getFieldProperty("CURRENCY_AFTER","value");
		String fieldCURRENCY_BOTH = Pages.DynaformExecution().getFieldProperty("CURRENCY_BOTH","value");
		
		String fieldOTHER_BEFORE = Pages.DynaformExecution().getFieldProperty("CURRENCY_OTHERB","value");
		String fieldOTHER_AFTER = Pages.DynaformExecution().getFieldProperty("CURRENCY_OTHERA","value");
		String fieldOTHER_BOTH = Pages.DynaformExecution().getFieldProperty("CURRENCY_OTHER","value"); 
		
		String fieldCURRENCY_NEGATIVE = Pages.DynaformExecution().getFieldProperty("CURRENCY_NEGATIVE","value"); 
		
	  FormFiller.formFillElements(fieldArray1);
    Assert.assertEquals("Mask Before does not exist", "$us 123,456,789.02", fieldCURRENCY_BEFORE);
    Assert.assertEquals("Mask After does not exist", "123,456,789.02 $us", fieldCURRENCY_AFTER);
    Assert.assertEquals("Mask Both does not exist", "$us 123,456,789.02 $us", fieldCURRENCY_BOTH);
    																																		 
    Assert.assertEquals("Numeric Mask does not exist", "123 123,456,789.02", fieldOTHER_BEFORE);
    Assert.assertEquals("Symbol Mask does not exist", "123,456,789.02 @@", fieldOTHER_AFTER);
    Assert.assertEquals("String Mask does not exist", "$us 123,456,789.02 AB", fieldOTHER_BOTH);
    
    Assert.assertEquals("Negative Mask does not exist", "- 123,456,789.02 $", fieldCURRENCY_NEGATIVE);
    
    Pages.InputDocProcess().continuebtn();
  }
  @After
    public void cleanup(){
        Browser.close();
    } 	
}