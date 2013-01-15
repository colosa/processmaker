package com.colosa.qa.automatization.tests.triggersProcess;

import java.lang.Exception;

import org.junit.Assert;
import org.junit.After;
import org.junit.AfterClass;
import org.junit.Test;

import org.openqa.selenium.By;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;

import com.colosa.qa.automatization.pages.*;
import com.colosa.qa.automatization.common.*;

import com.colosa.qa.automatization.common.controlOptions.input.*;
import com.colosa.qa.automatization.common.controlOptions.selection.*;
import com.colosa.qa.automatization.common.controlOptions.ControlOptions;

import java.io.FileNotFoundException;
import java.io.IOException;

public class TestTriggersSteps{

    @Test
    public void runCase() throws Exception {
        // login the PM
        Pages.Login().gotoUrl();
        Pages.Login().loginUser("admin","admin","cochalo");

        Pages.Main().goDesigner();
        Pages.ProcessList().openProcess("Process Debug");
        Pages.Designer().activeDebug(true);

        // go tab home
        Pages.Main().goHome();

        // create case the process and task : Testeo de funciones PMFNewCase (Creador de casos)
        int numberNewCase = Pages.Home().startCase("Process Debug (Task 1)");
        
        // switch to frame the dynaform
        //Pages.DynaformExecution().intoDynaform();
        Pages.DebugExecution().goTabVariables();

        String beforeDynaform = Pages.DebugExecution().getValue("BEFORE_DYNAFORM");

        // verify if the field BEFORE_DYNAFORM is validate
        Assert.assertEquals("The trigger beforeDynaform form not work", beforeDynaform, "BEFORE_DYNAFORM");
        // trigger after the form working
        
        // get button submit
        WebElement buttonSUBMIT = Pages.DynaformExecution().getField("Submit");

        // click to button submit
        buttonSUBMIT.click();

        // get the value of field AFTER_DYNAFORM after the form
        String afterDynaform = Pages.DebugExecution().getValue("AFTER_DYNAFORM");
        
        // verify if the field AFTER_DYNAFORM is validate
        Assert.assertEquals("The trigger afterDynaform form not work", afterDynaform, "AFTER_DYNAFORM");
        // trigger after the form working
        
        // get button continue
        WebElement buttonContinue = Pages.DynaformExecution().getObject("//*[@id='publisherContent[2]']/div/input");

        // click to button continue
        buttonContinue.click();

        // get the value of field BEFORE_ASSIGNMENT after the form
        String beforeAssignment = Pages.DebugExecution().getValue("BEFORE_ASSIGNMENT");
        
        // verify if the field VARIABLE is validate
        Assert.assertEquals("The trigger beforeAssignment form not work", beforeAssignment, "BEFORE_ASSIGNMENT");
        // trigger after the form working
        
        // get button continue
        WebElement buttonContinueSubmit = Pages.DynaformExecution().getObject("//*[@id='btnContinue']");

        // click to button continue
        buttonContinueSubmit.click();

        // get the value of field AFTER_ROUTING after the form
        String afterRouting = Pages.DebugExecution().getValue("AFTER_ROUTING");
        
        // verify if the field VARIABLE is validate
        Assert.assertEquals("The trigger afterRouting form not work", afterRouting, "AFTER_ROUTING");
        // trigger after the form working
        
        // get the value of field BEFORE_ROUTING after the form
        String beforeRouting = Pages.DebugExecution().getValue("BEFORE_ROUTING");
        
        // verify if the field VARIABLE is validate
        Assert.assertEquals("The trigger beforeRouting form not work", beforeRouting, "BEFORE_ROUTING");
        // trigger after the form working
        
        // get button continue
        buttonContinue = Pages.DynaformExecution().getObject("//*[@id='publisherContent[2]']/div/input");

        // click to button continue
        buttonContinue.click(); 
        
        Pages.Main().goDesigner();
        Pages.ProcessList().openProcess("Process Debug");
        Pages.Designer().activeDebug(false);
    }

    @After
    public void cleanup(){
        //Browser.close();
    }
}