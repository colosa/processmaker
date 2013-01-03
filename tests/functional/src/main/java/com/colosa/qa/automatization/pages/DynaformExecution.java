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

public class DynaformExecution extends Page{

    public DynaformExecution() throws FileNotFoundException, IOException{
        
    }

    public void intoDynaform() throws Exception{
        Browser.driver().switchTo().defaultContent();
        Browser.driver().switchTo().frame("casesFrame");
        Browser.driver().switchTo().frame("casesSubFrame");
        Browser.driver().switchTo().frame("openCaseFrame");
    }

    public void intoDebug() throws Exception{
        Browser.driver().switchTo().defaultContent();
        Browser.driver().switchTo().frame("casesFrame");
    }

    public void outDynaform() throws Exception{
        Browser.driver().switchTo().defaultContent();
    }

    public WebElement getField(String nameField) throws Exception{
        String str = "";
        str = ConfigurationSettings.getInstance().getSetting("DynaformExecution.webElement.fieldDynaform");
        str = str.replace("replaceNameFieldDynaform", nameField);
        return Browser.getElement(str);
    }

    public String getFieldProperty(String nameField, String property) throws Exception{
        String str = "";
        str = ConfigurationSettings.getInstance().getSetting("DynaformExecution.webElement.fieldDynaform");
        str = str.replace("replaceNameFieldDynaform", nameField);
        return Browser.getElement(str).getAttribute(property);
    }

    public WebElement getObject(String xpathField) throws Exception{
        String str = "";
        str = ConfigurationSettings.getInstance().getSetting("DynaformExecution.webElement.fieldDebug");
        str = str.replace("replaceXpath", xpathField);
        return Browser.getElement(str);
    }

    public String searchValueVariableDebug(String nameVariable) throws Exception{
        WebElement gridDebug = Browser.getElement("DynaformExecution.webElement.gridDebug");
        List<WebElement> gridDebugDivs = gridDebug.findElements(By.tagName("div"));

        String valueField = "";

        for(WebElement divs:gridDebugDivs)
        {
            if( divs.getAttribute("innerHTML").trim().equals(nameVariable))
            {
                WebElement trParent = divs.findElement(By.xpath("..")).findElement(By.xpath(".."));
                List<WebElement> fieldTds = trParent.findElements(By.tagName("td"));

                for(WebElement tds:fieldTds)
                {
                    if(!tds.findElement(By.xpath(".//div")).getAttribute("innerHTML").trim().equals(nameVariable))
                    {
                        valueField = tds.findElement(By.xpath(".//div")).getAttribute("innerHTML").trim();
                        break;
                    }
                }
                break;
            }
        }
        return valueField;
    }

}