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

public class TestNotifications{

    @Test
    public void runCase() throws Exception {
        // login the PM
        Pages.Login().gotoUrl();
        Pages.Login().loginUser("admin","admin","cochalo");

        Pages.Main().goHome();

        // create case the process and task : Testeo de funciones PMFNewCase (Creador de casos)
        int numberNewCase = Pages.Home().startCase("Process Notification Email (Task 1)");
        
        // get button submit
        WebElement buttonSUBMIT = Pages.DynaformExecution().getField("submit");

        // click to button submit
        buttonSUBMIT.click();

        // get button continue
        WebElement buttonContinueSubmit = Pages.DynaformExecution().getObject("//*[@id='btnContinue']");

        // click to button continue
        buttonContinueSubmit.click();

        Pages.Home().gotoInbox();
        Pages.Home().openCase(numberNewCase);

        String valorEnviado = Pages.DynaformExecution().getFieldValue("ENVIADO");

        // verify if the field CELULAR is validate
        Assert.assertEquals("No sent the notification :'(", valorEnviado, "sent");
        // case was create with field CELULAR correctly
    }

    @After
    public void cleanup(){
        Browser.close();
    }
}