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

        // go tab home
        Pages.Main().goHome();

        // create case the process and task : Testeo de funciones PMFNewCase (Creador de casos)
        int numberNewCase = Pages.Home().startCase("Testeo de triggers en pasos (Tarea inicial)");
        
        // switch to frame the dynaform
        //Pages.DynaformExecution().intoDynaform();
        
        Boolean debugActive = Pages.DebugExecution().verifyDebug();
        
        Assert.assertEquals("The Debug is inactive", debugActive, true);

        Pages.DebugExecution().goTabVariables();

        String valueVARIABLE = Pages.DebugExecution().getValue("VARIABLE");

        // verify if the field VARIABLE is validate
        Assert.assertEquals("The trigger after form not work", valueVARIABLE, "ANTES DE FORM");
        // trigger after the form working
        
        // get button submit
        WebElement buttonSUBMIT = Pages.DynaformExecution().getField("SUBMIT");

        // click to button submit
        buttonSUBMIT.click();

        // get the value of field VARIABLE after the form
        valueVARIABLE = Pages.DebugExecution().getValue("VARIABLE");
        
        // verify if the field VARIABLE is validate
        Assert.assertEquals("The trigger before form not work", valueVARIABLE, "DESPUES DE FORM");
        // trigger after the form working
        
        // get button continue
        WebElement buttonContinue = Pages.DynaformExecution().getObject("//*[@id='publisherContent[2]']/div/input");

        // click to button continue
        buttonContinue.click();

        // get the value of field VARIABLE after the form
        valueVARIABLE = Pages.DebugExecution().getValue("VARIABLE");
        
        // verify if the field VARIABLE is validate
        Assert.assertEquals("The trigger before form not work", valueVARIABLE, "ANTES DE ASIGNAR");
        // trigger after the form working
        
        // get button continue
        WebElement buttonContinueSubmit = Pages.DynaformExecution().getObject("//*[@id='btnContinue']");

        // click to button continue
        buttonContinueSubmit.click();

        // get the value of field VARIABLE after the form
        valueVARIABLE = Pages.DebugExecution().getValue("VARIABLE");
        
        // verify if the field VARIABLE is validate
        Assert.assertEquals("The trigger before form not work", valueVARIABLE, "DESPUES DE DERIVAR");
        // trigger after the form working
    }

    @After
    public void cleanup(){
        Browser.close();
    }
}