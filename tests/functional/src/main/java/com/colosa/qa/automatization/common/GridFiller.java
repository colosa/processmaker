package com.colosa.qa.automatization.common;

import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.chrome.ChromeDriver;
import org.openqa.selenium.firefox.FirefoxDriver;
import org.openqa.selenium.htmlunit.HtmlUnitDriver;
import org.openqa.selenium.ie.InternetExplorerDriver;
import org.openqa.selenium.By;
import org.openqa.selenium.support.ui.WebDriverWait;
import org.openqa.selenium.support.ui.ExpectedConditions;
import org.openqa.selenium.Platform;
import org.openqa.selenium.remote.DesiredCapabilities;
import org.openqa.selenium.remote.RemoteWebDriver;
import org.openqa.selenium.support.ui.Select;
import java.net.URL;
import java.io.FileNotFoundException;
import java.io.IOException;
import org.openqa.selenium.JavascriptExecutor;

public class GridFiller{

	public static boolean gridFillElements(FormFieldData[][] fieldData) throws FileNotFoundException, IOException, Exception{
		WebElement elem = null;
		System.out.println("Tamaño del elemento: "+fieldData.length);

		for(int i = 0;i<fieldData.length;i++)
		{
			for(int j = 0; j<fieldData[i].length;j++)
			{
				
				switch(fieldData[i][j].fieldFindType)
				{

					case ID: 	elem = Browser.driver().findElement(By.id(fieldData[i][j].fieldPath));
											break;
					
					case XPATH: 	elem = Browser.driver().findElement(By.xpath(fieldData[i][j].fieldPath));
												break;
					
					case CSSSELECTOR:	elem = Browser.driver().findElement(By.cssSelector(fieldData[i][j].fieldPath));
													break;
					
					case LINKTEXT:	elem = Browser.driver().findElement(By.linkText(fieldData[i][j].fieldPath));
												break;
					
					case PARTIALLINKTEXT:	elem = Browser.driver().findElement(By.partialLinkText(fieldData[i][j].fieldPath));
														break;
					
					case TAGNAME: 	elem = Browser.driver().findElement(By.tagName(fieldData[i][j].fieldPath));
												break;
					
					default:	break;

				}

				switch(fieldData[i][j].fieldType)
				{
					case TEXTBOX: elem.sendKeys(fieldData[i][j].fieldValue);
											break;

					case BUTTON: 	elem.click();
											break;	

					case TEXTAREA: elem.sendKeys(fieldData[i][j].fieldValue);
											 break;	

					case DROPDOWN: Select droplist = new Select(elem);
											 droplist.selectByVisibleText(fieldData[i][j].fieldValue);;
											 break;

					case RADIOBUTTON:	elem.click();
												break;

					case CHECK: 	elem.click();
											break;	

					case READONLY:  ((JavascriptExecutor)Browser.driver()).executeScript("arguments[0].value=arguments[1]", elem, fieldData[i][j].fieldValue);
          							break;						

					default: 	break;																																						
				}
				
			}			
		}

		return true;		

	}

}