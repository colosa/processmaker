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

public class TestPMFNewCaseTest{

    @Test
    public void runCase() throws Exception {
        // login the PM
        Pages.Login().gotoUrl();
        Pages.Login().loginUser("admin","admin","cochalo");

        // go tab home
        Pages.Main().goHome();
        
        // create case the process and task : Testeo de funciones PMFNewCase (Creador de casos)
        int numberNewCase = Pages.Home().startCase("Testeo de funciones PMFNewCase (Creador de casos)");
        
        // switch to frame the dynaform
        Pages.DynaformExecution().intoDynaform();
        
        // get count value of field UID_CASE
        int countIdCase = Pages.DynaformExecution().getFieldProperty("UID_CASE","value").length();

        // get number of case generated in field NUM_CASE
        String numberCaseGenerated = Pages.DynaformExecution().getFieldProperty("NUM_CASE","value");

        // verify if the code is validate
        Assert.assertTrue("No case has created ", (countIdCase == 32) );
        // case was create correctly
        
        // out of frame of dynaform
        Pages.DynaformExecution().outDynaform();

        // go submenu draft
        Pages.Home().gotoDraft();

        // open the case generated
        Pages.Home().openCase(Integer.parseInt(numberCaseGenerated));

        // switch to frame the dynaform
        Pages.DynaformExecution().intoDynaform();

        // get value of case generated in field NUM_CASE
        String fieldNOMBRE = Pages.DynaformExecution().getFieldProperty("NOMBRE","value");

        // get value of case generated in field APELLIDO
        String fieldAPELLIDO = Pages.DynaformExecution().getFieldProperty("APELLIDO","value");

        // get value of case generated in field CELULAR
        String fieldCELULAR = Pages.DynaformExecution().getFieldProperty("CELULAR","value");

        // get value of case generated in field OBSERVACIONES
        String fieldOBSERVACIONES = Pages.DynaformExecution().getFieldProperty("OBSERVACIONES","value");


        // verify if the field NOMBRE is validate
        Assert.assertEquals("No case has created with field NOMBRE ", fieldNOMBRE, "BRAYAN");
        // case was create with field NOMBRE correctly
        
        // verify if the field APELLIDO is validate
        Assert.assertEquals("No case has created with field APELLIDO ", fieldAPELLIDO, "PEREYRA");
        // case was create with field APELLIDO correctly
        
        // verify if the field CELULAR is validate
        Assert.assertEquals("No case has created with field CELULAR ", fieldCELULAR, "70622818");
        // case was create with field CELULAR correctly
         
        // verify if the field OBSERVACIONES is validate
        Assert.assertEquals("No case has created with field OBSERVACIONES ", fieldOBSERVACIONES, "ESTA ES UNA CREACION DE CASOS VIA TRIGGER");
        // case was create with field OBSERVACIONES correctly
    }

    @After
    public void cleanup(){
        Browser.close();
    }

}