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

public class TestHideShowCaseTitle{

    @Test
    public void runCase() throws Exception {
        // login the PM
        Pages.Login().gotoUrl();
        Pages.Login().loginUser("admin","admin","cochalo");

        Pages.Main().goDesigner();
        Pages.ProcessList().openProcess("Process Hide Title and case number");
        Pages.Designer().showTitleCase(true);

        // go tab home
        Pages.Main().goHome();

        // create case the process and task : Testeo de funciones PMFNewCase (Creador de casos)
        int numberNewCase = Pages.Home().startCase("Process Hide Title and case number (Task 1)");
        
        // switch to frame the dynaform
        //Pages.DynaformExecution().intoDynaform();
        boolean showCaseTitle = Pages.DynaformExecution().activeCaseTitle();

        // verify if the case title is show
        Assert.assertEquals("The case title is hidden :'(", showCaseTitle, true);
        // trigger after the form working
        

        Pages.Main().goDesigner();
        Pages.ProcessList().openProcess("Process Hide Title and case number");
        Pages.Designer().showTitleCase(false);

        // go tab home
        Pages.Main().goHome();

        // create case the process and task : Testeo de funciones PMFNewCase (Creador de casos)
        numberNewCase = Pages.Home().startCase("Process Hide Title and case number (Task 1)");
        
        // switch to frame the dynaform
        //Pages.DynaformExecution().intoDynaform();
        showCaseTitle = Pages.DynaformExecution().activeCaseTitle();

        // verify if the case title is show
        Assert.assertEquals("The case title is show :'(", showCaseTitle, false);
        // trigger after the form working
    }


    @After
    public void cleanup(){
        Browser.close();
    }
}
