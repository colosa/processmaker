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

public class TestPMFRoleList{

    @Test
    public void runCase() throws Exception {
        
        Pages.Login().gotoUrl();
        Pages.Login().loginUser("admin","admin","workflow");
				Pages.Main().goHome();
        
        int casenumber = Pages.Home().startCase("Test PMFRoleList (Task 1)");
        Pages.DynaformExecution().intoDynaform();
        
        Integer fieldROLES = Integer.parseInt(Pages.DynaformExecution().getFieldProperty("ROLES","value"));
        
        FormFieldData[] fieldArray=new FormFieldData[1];
		    fieldArray[0]=new FormFieldData();
				
		    fieldArray[0].fieldPath="form[SUBMIT]";
		    fieldArray[0].fieldFindType=FieldKeyType.ID;
		    fieldArray[0].fieldType=FieldType.BUTTON;
		    fieldArray[0].fieldValue="";
    
        FormFiller.formFillElements(fieldArray);
        Pages.InputDocProcess().continuebtn();
        
        Pages.Main().goAdmin();
        Integer roles = Pages.Admin().countRoles();
        
        Assert.assertEquals("PMFRoleList function not working properly", roles, fieldROLES);
        
        //System.out.println("LOS ROLES  "+roles);
    }

    @After
    public void cleanup(){
        Browser.close();
    }
    
}