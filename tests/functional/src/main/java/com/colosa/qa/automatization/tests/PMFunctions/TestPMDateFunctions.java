package com.colosa.qa.automatization.tests.PMFunctions;

import java.lang.Exception;
import java.util.List;

import java.util.Date;
import java.text.DateFormat;
import java.text.SimpleDateFormat;
    
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

public class TestPMDateFunctions{

    @Test
    public void runCase() throws Exception {
        
        Pages.Login().gotoUrl();
        Pages.Login().loginUser("admin","admin","workflow");
				Pages.Main().goHome();
        
        int casenumber = Pages.Home().startCase("Test PMDate Functions (Task 1)");
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
				
				fieldArray[0].fieldPath="form[FORMAT_DATE1]";
		    fieldArray[0].fieldFindType=FieldKeyType.ID;
		    fieldArray[0].fieldType=FieldType.TEXTBOX;
		    
		    fieldArray[1].fieldPath="form[FORMAT_DATE2]";
		    fieldArray[1].fieldFindType=FieldKeyType.ID;
		    fieldArray[1].fieldType=FieldType.TEXTBOX;
		    
		    fieldArray[2].fieldPath="form[FORMAT_DATE3]";
		    fieldArray[2].fieldFindType=FieldKeyType.ID;
		    fieldArray[2].fieldType=FieldType.TEXTBOX;
		    
		    fieldArray[3].fieldPath="form[CURRENT_DATE]";
		    fieldArray[3].fieldFindType=FieldKeyType.ID;
		    fieldArray[3].fieldType=FieldType.TEXTBOX;
		    
		    fieldArray[4].fieldPath="form[CURRENT_TIME]";
		    fieldArray[4].fieldFindType=FieldKeyType.ID;
		    fieldArray[4].fieldType=FieldType.TEXTBOX;
		    
		    fieldArray[5].fieldPath="form[LITERAL_DATE_EN]";
		    fieldArray[5].fieldFindType=FieldKeyType.ID;
		    fieldArray[5].fieldType=FieldType.TEXTBOX;
		    
		    fieldArray[6].fieldPath="form[LITERAL_DATE_ES]";
		    fieldArray[6].fieldFindType=FieldKeyType.ID;
		    fieldArray[6].fieldType=FieldType.TEXTBOX;
		    
				fieldArray[7].fieldPath="form[SUBMIT]";
		    fieldArray[7].fieldFindType=FieldKeyType.ID;
		    fieldArray[7].fieldType=FieldType.BUTTON;
		    fieldArray[7].fieldValue="";
    		
    		String fieldFORMAT_DATE1 = Pages.DynaformExecution().getFieldProperty("FORMAT_DATE1","value");
    		String fieldFORMAT_DATE2 = Pages.DynaformExecution().getFieldProperty("FORMAT_DATE2","value");
    		String fieldFORMAT_DATE3 = Pages.DynaformExecution().getFieldProperty("FORMAT_DATE3","value");
    		String fieldCURRENT_DATE = Pages.DynaformExecution().getFieldProperty("CURRENT_DATE","value");
    		String fieldCURRENT_TIME = Pages.DynaformExecution().getFieldProperty("CURRENT_TIME","value");
    		String fieldLITERAL_DATE_EN = Pages.DynaformExecution().getFieldProperty("LITERAL_DATE_EN","value");
    		String fieldLITERAL_DATE_ES = Pages.DynaformExecution().getFieldProperty("LITERAL_DATE_ES","value");
    		/*
    		@@FORMAT_DATE1 = formatDate('2013-01-09','d m yy');
				@@FORMAT_DATE2 = formatDate('2013-01-09','d de M del yyyy','es');
				@@FORMAT_DATE3 = formatDate('2013-01-09','M dd,yyyy','en');
				
				@@CURRENT_DATE = getCurrentDate();
				@@CURRENT_TIME = getCurrentTime();
				
				@@LITERAL_DATE_EN = literalDate('2013-01-09','en');
				@@LITERAL_DATE_ES = literalDate('2013-01-09','es');
				*/
																											
				DateFormat dateFormat = new SimpleDateFormat("yyyy-MM-dd");
				Date datetest = new Date();
				
				DateFormat timeFormat = new SimpleDateFormat("HH:mm");
				Date timetest = new Date();
				
				String currtime = fieldCURRENT_TIME.substring (0,5);
				
				Assert.assertEquals(" 'd' is not recognized in the formatDate", "9 1 13", fieldFORMAT_DATE1);
		    Assert.assertEquals("Spanish Language is not recognized in the formatDate", "9 de Enero del 2013", fieldFORMAT_DATE2);
		    Assert.assertEquals("English Language is not recognized in the formatDate", "January 09,2013", fieldFORMAT_DATE3);
		    
		    Assert.assertEquals("CurrentDate function", dateFormat.format(datetest),fieldCURRENT_DATE);
		    Assert.assertEquals("CurrentTime function", dateFormat.format(timetest),currtime);
		    
		    Assert.assertEquals("English Language is not recognized in the literalDate", "January 9,2013", fieldLITERAL_DATE_EN);
		    Assert.assertEquals("Spanish Language is not recognized in the literalDate", "9 de Enero de 2013", fieldLITERAL_DATE_ES);
		    
		    Pages.InputDocProcess().continuebtn();
    }

    @After
    public void cleanup(){
        Browser.close();
    }
    
}