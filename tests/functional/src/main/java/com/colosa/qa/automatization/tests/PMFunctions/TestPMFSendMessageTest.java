package com.colosa.qa.automatization.tests.PMFunctions;

import java.lang.Exception;
import java.util.List;

import org.junit.Assert;
import org.junit.After;
import org.junit.AfterClass;
import org.junit.Test;
import org.openqa.selenium.By;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.interactions.Actions;
import org.openqa.selenium.NoSuchElementException;

import com.colosa.qa.automatization.pages.*;
import com.colosa.qa.automatization.common.*;
import com.colosa.qa.automatization.common.controlOptions.input.*;
import com.colosa.qa.automatization.common.controlOptions.selection.*;
import com.colosa.qa.automatization.common.controlOptions.ControlOptions;
import com.colosa.qa.automatization.common.Browser;
import com.colosa.qa.automatization.common.extJs.ExtJSGrid;

import java.io.FileNotFoundException;
import java.io.IOException;

public class TestPMFSendMessageTest{

    @Test
    public void runCase() throws Exception {
        
        Pages.Login().gotoUrl();
        Pages.Login().loginUser("admin","admin","workflow");
				Pages.Main().goHome();
        
        int casenumber = Pages.Home().startCase("TestPMFSendMessage (Task 1)");
        Pages.DynaformExecution().intoDynaform();
        
        FormFieldData[] fieldArray=new FormFieldData[4];
		    fieldArray[0]=new FormFieldData();
		    fieldArray[1]=new FormFieldData();
		    fieldArray[2]=new FormFieldData();
		    fieldArray[3]=new FormFieldData();
				
				fieldArray[0].fieldPath="form[FROM]";
		    fieldArray[0].fieldFindType=FieldKeyType.ID;
		    fieldArray[0].fieldType=FieldType.TEXTBOX;
		    fieldArray[0].fieldValue="norah@colosa.com";
		    
		    fieldArray[1].fieldPath="form[TO]";
		    fieldArray[1].fieldFindType=FieldKeyType.ID;
		    fieldArray[1].fieldType=FieldType.TEXTBOX;
		    fieldArray[1].fieldValue="nmm757@gmail.com";
		    
		    fieldArray[2].fieldPath="form[CC]";
		    fieldArray[2].fieldFindType=FieldKeyType.ID;
		    fieldArray[2].fieldType=FieldType.TEXTBOX;
		    fieldArray[2].fieldValue="norah@colosa.com";
				
				fieldArray[3].fieldPath="form[SUBMIT]";
		    fieldArray[3].fieldFindType=FieldKeyType.ID;
		    fieldArray[3].fieldType=FieldType.BUTTON;
		    fieldArray[3].fieldValue="";
    		
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
				
				String fieldSEND = Pages.DynaformExecution().getFieldProperty("SEND","value");
				Assert.assertEquals("Mail not sent", fieldSEND, "1");
		    FormFiller.formFillElements(fieldArray1);
		    Pages.InputDocProcess().continuebtn();
    }

    @After
    public void cleanup(){
        Browser.close();
    }
    
}