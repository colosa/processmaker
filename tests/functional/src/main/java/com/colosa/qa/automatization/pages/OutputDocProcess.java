package com.colosa.qa.automatization.pages;

import java.util.List;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.By;
import org.openqa.selenium.interactions.Actions;
import org.openqa.selenium.JavascriptExecutor;
import com.colosa.qa.automatization.common.*;
import com.colosa.qa.automatization.common.extJs.*;
import java.io.FileNotFoundException;
import java.io.IOException;

public class OutputDocProcess extends Page{

	public OutputDocProcess() throws FileNotFoundException, IOException{
		
	}

	public void downloadPdfFile() throws Exception{

		Browser.driver().switchTo().defaultContent();
		Browser.driver().switchTo().frame("casesFrame");
		Browser.driver().switchTo().frame("casesSubFrame");
		Browser.driver().switchTo().frame("openCaseFrame");
		Browser.getElement("outputDocProcess.webelement.outputPdfButton").click();

	}

	public void downloadDocFile() throws Exception{



		Browser.driver().switchTo().defaultContent();
		Browser.driver().switchTo().frame("casesFrame");
		Browser.driver().switchTo().frame("casesSubFrame");
		Browser.driver().switchTo().frame("openCaseFrame");
		Browser.getElement("outputDocProcess.webelement.outputDocButton").click();

	}

	public void nextbtn() throws Exception{

		Browser.getElement("outputDocProcess.webelement.nextButton").click();

	}

	public void continuebtn() throws Exception{

		Browser.getElement("inputDocProcess.webelement.continue").click();

	}

}