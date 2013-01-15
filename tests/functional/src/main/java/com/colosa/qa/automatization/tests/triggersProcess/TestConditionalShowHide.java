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

public class TestConditionalShowHide{

    @Test
    public void runCase() throws Exception {
        // login the PM
        Pages.Login().gotoUrl();
        Pages.Login().loginUser("admin","admin","cochalo");

        Pages.Main().goHome();

        // create case the process and task : Testeo de funciones PMFNewCase (Creador de casos)
        int numberNewCase = Pages.Home().startCase("Process Conditional Show Hide (Task 1)");

        Pages.DynaformExecution().setFieldValue("opcion", "opcion A");
        Pages.DynaformExecution().setFieldValue("profesion", "Yes");
        Pages.DynaformExecution().setFieldValue("fecha", "201399");
        Pages.DynaformExecution().setFieldValue("porcentaje", "35");

        // get button submit
        WebElement buttonSUBMIT = Pages.DynaformExecution().getField("send");

        // click to button submit
        buttonSUBMIT.click();

        // get button continue
        WebElement buttonContinueSubmit = Pages.DynaformExecution().getObject("//*[@id='btnContinue']");

        // click to button continue
        buttonContinueSubmit.click();

        Pages.Home().gotoInbox();
        Pages.Home().openCase(numberNewCase);

        // get button submit
        buttonSUBMIT = Pages.DynaformExecution().getField("send");

        // click to button submit
        buttonSUBMIT.click();

        // get button continue
        buttonContinueSubmit = Pages.DynaformExecution().getObject("//*[@id='btnContinue']");

        // click to button continue
        buttonContinueSubmit.click();

        ///////////////////////////
        

        Pages.Main().goHome();

        // create case the process and task : Testeo de funciones PMFNewCase (Creador de casos)
        numberNewCase = Pages.Home().startCase("Process Conditional Show Hide (Task 1)");

        Pages.DynaformExecution().setFieldValue("opcion", "opcion B");
        Pages.DynaformExecution().setFieldValue("titulacion][sistemas", "click");
        
        Pages.DynaformExecution().setFieldValue("beca][conv", "click");
        Pages.DynaformExecution().setFieldValue("beca][paa", "click");

        Pages.DynaformExecution().setFieldValue("salario", "1000");

        // get button submit
        buttonSUBMIT = Pages.DynaformExecution().getField("send");

        // click to button submit
        buttonSUBMIT.click();

        // get button continue
        buttonContinueSubmit = Pages.DynaformExecution().getObject("//*[@id='btnContinue']");

        // click to button continue
        buttonContinueSubmit.click();

        Pages.Home().gotoInbox();
        Pages.Home().openCase(numberNewCase);

        // get button submit
        buttonSUBMIT = Pages.DynaformExecution().getField("send");

        // click to button submit
        buttonSUBMIT.click();

        // get button continue
        buttonContinueSubmit = Pages.DynaformExecution().getObject("//*[@id='btnContinue']");

        // click to button continue
        buttonContinueSubmit.click();


    }

    @After
    public void cleanup(){
        Browser.close();
    }
}