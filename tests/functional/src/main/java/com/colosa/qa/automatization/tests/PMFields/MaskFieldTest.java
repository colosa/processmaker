package com.colosa.qa.automatization.tests.PMFields;
import org.junit.Assert;
import org.junit.AfterClass;
import org.junit.Test;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import com.colosa.qa.automatization.pages.*;
import com.colosa.qa.automatization.common.*;
import java.io.FileNotFoundException;
import java.io.IOException;

public class MaskFieldTest{
@Test
 	public void runProcess() throws Exception{
 		Pages.Login().gotoUrl();
   	Pages.Login().loginUser("admin","admin","workflow");
    Pages.Main().goHome();
    int casenumber=Pages.Home().startCase("TestMask (Task 1)");
    openCaseFrame();
    FormFieldData[] fieldArray=new FormFieldData[7];

    fieldArray[0]=new FormFieldData();
    fieldArray[1]=new FormFieldData();
    fieldArray[2]=new FormFieldData();
    fieldArray[3]=new FormFieldData();
    fieldArray[4]=new FormFieldData();
    fieldArray[5]=new FormFieldData();
    fieldArray[6]=new FormFieldData();
    
    fieldArray[0].fieldPath="form[MASK1]";
    fieldArray[0].fieldFindType=FieldKeyType.ID;
    fieldArray[0].fieldType=FieldType.TEXTBOX;
    fieldArray[0].fieldValue="123456789.02";

		fieldArray[1].fieldPath="form[MASK2]";
    fieldArray[1].fieldFindType=FieldKeyType.ID;
    fieldArray[1].fieldType=FieldType.TEXTBOX;
    fieldArray[1].fieldValue="123,02";
    
    fieldArray[2].fieldPath="form[MASK_BEFORE]";
    fieldArray[2].fieldFindType=FieldKeyType.ID;
    fieldArray[2].fieldType=FieldType.TEXTBOX;
    fieldArray[2].fieldValue="123456789.02";
		
		fieldArray[3].fieldPath="form[MASK_AFTER]";
    fieldArray[3].fieldFindType=FieldKeyType.ID;
    fieldArray[3].fieldType=FieldType.TEXTBOX;
    fieldArray[3].fieldValue="123,12";
    
    fieldArray[4].fieldPath="form[MASK_OTHER1]";
    fieldArray[4].fieldFindType=FieldKeyType.ID;
    fieldArray[4].fieldType=FieldType.TEXTBOX;
    fieldArray[4].fieldValue="123456789.023";

		fieldArray[5].fieldPath="form[MASK_OTHER2]";
    fieldArray[5].fieldFindType=FieldKeyType.ID;
    fieldArray[5].fieldType=FieldType.TEXTBOX;
    fieldArray[5].fieldValue="123,023";
    
    fieldArray[6].fieldPath="form[SUBMIT]";
    fieldArray[6].fieldFindType=FieldKeyType.ID;
    fieldArray[6].fieldType=FieldType.BUTTON;
    fieldArray[6].fieldValue="";

    FormFiller.formFillElements(fieldArray);
    Pages.InputDocProcess().continuebtn();
    
    Pages.Home().gotoInbox();
    Assert.assertTrue("The case does not exist in inbox", Pages.Home().existCase(casenumber));
    Pages.Home().openCase(casenumber);
    Pages.InputDocProcess().openCaseFrame();
    openCaseFrame();
    FormFieldData[] fieldArray1=new FormFieldData[1];
    fieldArray1[0]=new FormFieldData();
		
    fieldArray1[0].fieldPath="form[SUBMIT]";
    fieldArray1[0].fieldFindType=FieldKeyType.ID;
    fieldArray1[0].fieldType=FieldType.BUTTON;
    fieldArray1[0].fieldValue="";

		String fieldMASK1 = Pages.DynaformExecution().getFieldProperty("MASK1","value");
		String fieldMASK2 = Pages.DynaformExecution().getFieldProperty("MASK2","value");
		
		String fieldMASK_BEFORE = Pages.DynaformExecution().getFieldProperty("MASK_BEFORE","value");
		String fieldMASK_AFTER = Pages.DynaformExecution().getFieldProperty("MASK_AFTER","value");
		
		String fieldMASK_OTHER1 = Pages.DynaformExecution().getFieldProperty("MASK_OTHER1","value");
		String fieldMASK_OTHER2 = Pages.DynaformExecution().getFieldProperty("MASK_OTHER2","value");
				
    FormFiller.formFillElements(fieldArray1);
    
    Assert.assertEquals("Mask does not exist", "123,456,789.02", fieldMASK1);
    Assert.assertEquals("Mask does not exist", "123,02", fieldMASK2);
    
    Assert.assertEquals("Mask Before does not exist", "B12,345,678.02", fieldMASK_BEFORE);
    Assert.assertEquals("Mask After does not exist", "123,1B", fieldMASK_AFTER);
    
    Assert.assertEquals("Mask other value does not exist", "123,456,789.02", fieldMASK_OTHER1);
    Assert.assertEquals("Mask other value(,) does not exist", "123,02", fieldMASK_OTHER2);
    
    Pages.InputDocProcess().continuebtn();
  	Browser.close();
  }
 	
 	public void openCaseFrame() throws Exception{
	  Browser.driver().switchTo().defaultContent();
		Browser.driver().switchTo().frame("casesFrame");
		Browser.driver().switchTo().frame("casesSubFrame");
		Browser.driver().switchTo().frame("openCaseFrame");
  }
}