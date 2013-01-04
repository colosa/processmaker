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
    FormFieldData[] fieldArray=new FormFieldData[3];

    fieldArray[0]=new FormFieldData();
    fieldArray[1]=new FormFieldData();
    fieldArray[2]=new FormFieldData();
    
    fieldArray[0].fieldPath="form[MASK1]";
    fieldArray[0].fieldFindType=FieldKeyType.ID;
    fieldArray[0].fieldType=FieldType.TEXTBOX;
    fieldArray[0].fieldValue="123456789.02";

		fieldArray[1].fieldPath="form[MASK2]";
    fieldArray[1].fieldFindType=FieldKeyType.ID;
    fieldArray[1].fieldType=FieldType.TEXTBOX;
    fieldArray[1].fieldValue="123,02";
    
    fieldArray[2].fieldPath="form[SUBMIT]";
    fieldArray[2].fieldFindType=FieldKeyType.ID;
    fieldArray[2].fieldType=FieldType.BUTTON;
    fieldArray[2].fieldValue="";

    FormFiller.formFillElements(fieldArray);
    Assert.assertTrue("button continue does not exist in this dynaform", Browser.elementExists("inputDocProcess.webelement.continue"));
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

		Assert.assertEquals(Browser.getElement("id___form[MASK1]").getAttribute("value"), "123,456,789.02");
		Assert.assertEquals(Browser.getElement("id___form[MASK2]").getAttribute("value"), "123,02");
		
    FormFiller.formFillElements(fieldArray1);
    Assert.assertTrue("button continue does not exist in this dynaform", Browser.elementExists("inputDocProcess.webelement.continue"));
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