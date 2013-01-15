package com.colosa.qa.automatization.tests.outputDocuments;

import org.junit.Assert;
import org.junit.After;
import org.junit.AfterClass;
import org.junit.Test;

import org.openqa.selenium.WebDriver;
import com.colosa.qa.automatization.pages.*;
import com.colosa.qa.automatization.common.*;

import java.io.FileNotFoundException;
import java.io.IOException;

public class TestOutputDocument{


	@Test
	public void downloadOutputDoc() throws Exception{

		Pages.Login().gotoUrl();
		Pages.Login().loginUser("admin", "admin", "workflow");
		Pages.Main().goHome();
		Pages.Home().startCase("TestOutputDocument (Task 1)");

		Pages.OutputDocProcess().downloadDocFile();
		Pages.OutputDocProcess().downloadPdfFile();
		Pages.OutputDocProcess().nextbtn();
		Pages.OutputDocProcess().continuebtn();
	}

	@After
    public void cleanup(){
        Browser.close();
    }
}