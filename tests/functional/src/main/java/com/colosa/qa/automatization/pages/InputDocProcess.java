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

public class InputDocProcess extends Page{

	public InputDocProcess() throws FileNotFoundException, IOException{
		

	}

	public void uploadFile(String filePath, String description) throws Exception{

		Browser.driver().switchTo().defaultContent();
		Browser.driver().switchTo().frame("casesFrame");
		Browser.driver().switchTo().frame("casesSubFrame");
		Browser.driver().switchTo().frame("openCaseFrame");
		Browser.getElement("inputDocProcess.webelement.new").click();
	
		Browser.getElement("inputDocProcess.webelement.path").sendKeys(filePath);
		Browser.getElement("inputDocProcess.webelement.comment").sendKeys(description);
		Browser.getElement("inputDocProcess.webelement.save").click();
		Browser.getElement("inputDocProcess.webelement.submit").click();
	

	}

	public boolean continuebtn() throws Exception{

		WebElement nextBtn = Browser.getElement("inputDocProcess.webelement.continue");

		if(nextBtn==null)
			throw new Exception("The element is not found");
		else{
			nextBtn.click();
			return true;
			}
			
	}


	public void openCaseFrame() throws Exception{

		Browser.driver().switchTo().defaultContent();
		Browser.driver().switchTo().frame("casesFrame");
		Browser.driver().switchTo().frame("casesSubFrame");
		Browser.driver().switchTo().frame("openCaseFrame");
		
	 }

	public void switchToDefault() throws Exception{

		Browser.driver().switchTo().defaultContent();
		
	 }

}