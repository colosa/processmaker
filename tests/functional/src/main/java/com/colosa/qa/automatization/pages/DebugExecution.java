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

public class DebugExecution extends Page {

    public DebugExecution() throws FileNotFoundException, IOException {

    }

    // into level of debug
    public void intoDebug() throws Exception {
        Browser.driver().switchTo().defaultContent();
        Browser.driver().switchTo().frame("casesFrame");
    }

    // verify debug
    public boolean verifyDebug() throws Exception {
    	intoDebug();
    	WebElement debugPanel = Browser.driver().findElement(By.id("debugPanel"));
    	String debugPanelClass = debugPanel.getAttribute("class").trim();

    	if (debugPanelClass.indexOf ("x-hide-display") > -1) {
    		return false;
    	} else {
    		return true;
    	}
    }

    // into tab variables
    public void showAllVariables() throws Exception {
    	intoDebug();
    	WebElement divDebugVariables = Browser.driver().findElement(By.id("debugVariables"));

    	List<WebElement> gridDebugButtons = divDebugVariables.findElements(By.tagName("button"));
    	String valueField = "";

        for(WebElement divs:gridDebugButtons)
        {
        	if (divs.getAttribute("innerHTML").trim() == "All") {
        		divs.click();
				break;
        	}
        }
    }

    // into tab variables
    public void goTabVariables() throws Exception {
    	intoDebug();
    	WebElement tab = Browser.driver().findElement(By.id("debugPanelTabs__debugVariables"));
    	showAllVariables();
    	tab.click();
    }

    // get Value
    public String getValue(String nameVariable) throws Exception{
    	intoDebug();
        Thread.sleep(2000);
        WebElement gridDebug = Browser.driver().findElement(By.id("debugVariables"));

        List<WebElement> gridDebugTrs = gridDebug.findElements(By.tagName("td"));
        String valueField = "";
        
        System.out.println("Looking for variable: " + nameVariable + "... ");
        for(WebElement divs:gridDebugTrs)
        {
            //System.out.println("Contador: " + con + " -> " + divs.getAttribute("innerHTML"));
            if ( (divs.getAttribute("class").indexOf ("x-grid3-td-name") > -1) && 
            	 (divs.getAttribute("innerHTML").indexOf (nameVariable) > -1) ) {

                //System.out.println("Exists variable '" + nameVariable + "' :) ");
                WebElement trElements = divs.findElement(By.xpath("..")).findElement(By.xpath(".."));
            	
            	List<WebElement> gridDebugDivs = trElements.findElements(By.tagName("div"));
		        for(WebElement tds:gridDebugDivs)
		        {
		        	if ( (tds.getAttribute("class").indexOf ("x-grid3-col-value") > -1) ) {
		            	System.out.println("Valor de variable es :'" + tds.getAttribute("innerHTML").trim() + "' :) ");
		            	valueField = tds.getAttribute("innerHTML").trim();
		            	break;
		        	}
		        }
        	}
        }
        return valueField;
    }
}