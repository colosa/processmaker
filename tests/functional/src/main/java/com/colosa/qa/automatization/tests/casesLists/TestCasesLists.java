package com.colosa.qa.automatization.tests.casesLists;

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

public class TestCasesLists{

    @Test
    public void runCase() throws Exception {
        
        Pages.Login().gotoUrl();
        Pages.Login().loginUser("admin","admin","workflow");
				Pages.Main().goHome();
        
        int casenumber = Pages.Home().startCase("TestCasesStatus (Task 1)");
        Pages.DynaformExecution().intoDynaform();
                
        Pages.DynaformExecution().outDynaform();  
        
		    Pages.Home().gotoDraft();                 
		    Pages.Home().openCase(casenumber);
        Pages.DynaformExecution().intoDynaform();
    
        FormFieldData[] fieldArray1=new FormFieldData[2];
		    fieldArray1[0]=new FormFieldData();
		    fieldArray1[1]=new FormFieldData();
		    
				fieldArray1[0].fieldPath="form[EVALUATION]";
		    fieldArray1[0].fieldFindType=FieldKeyType.ID;
		    fieldArray1[0].fieldType=FieldType.TEXTBOX;
		    fieldArray1[0].fieldValue="66";
		    
		    fieldArray1[1].fieldPath="form[SUBMIT]";
		    fieldArray1[1].fieldFindType=FieldKeyType.ID;
		    fieldArray1[1].fieldType=FieldType.BUTTON;
		    fieldArray1[1].fieldValue="";
    		
    		FormFiller.formFillElements(fieldArray1);
		    Pages.InputDocProcess().continuebtn();
    		
    		Pages.Home().gotoParticipated();
		    Assert.assertTrue("The case does not exist in Participated", Pages.Home().existCase(casenumber));
		    Pages.Home().openCase(casenumber);
		    Pages.DynaformExecution().outDynaform();  
		        		
    		Pages.Home().gotoInbox();
		    Assert.assertTrue("The case does not exist in Inbox", Pages.Home().existCase(casenumber));
		    Pages.Home().openCase(casenumber);
		    Pages.DynaformExecution().intoDynaform();
		    
		    FormFieldData[] fieldArray3=new FormFieldData[2];
		    fieldArray3[0]=new FormFieldData();
		    fieldArray3[1]=new FormFieldData();
		    
		    fieldArray3[0].fieldPath="form[EVALUATION]";
		    fieldArray3[0].fieldFindType=FieldKeyType.ID;
		    fieldArray3[0].fieldType=FieldType.TEXTBOX;
		    fieldArray3[0].fieldValue="77";
		              
		    fieldArray3[1].fieldPath="form[SUBMIT]";
		    fieldArray3[1].fieldFindType=FieldKeyType.ID;
		    fieldArray3[1].fieldType=FieldType.BUTTON;
		    fieldArray3[1].fieldValue="";
		    
		    FormFiller.formFillElements(fieldArray3);
		    Pages.InputDocProcess().continuebtn();
		    
		    Pages.Home().gotoUnassigned();
		    Assert.assertTrue("The case does not exist in Unassigned", Pages.Home().existCase(casenumber));
		}

    @After
    public void cleanup(){
        Browser.close();
    }
}