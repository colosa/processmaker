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
import java.util.List;
import java.net.URL;
import java.io.FileNotFoundException;
import java.io.IOException;
import org.openqa.selenium.JavascriptExecutor;



public class FormFiller{

	public static boolean formFillElements(FormFieldData[] fieldData) throws FileNotFoundException, IOException, Exception{
		WebElement elem = null;
		System.out.println("Tamaño del elemento: "+fieldData.length);

		for(int i = 0;i<fieldData.length;i++)
		{
			switch(fieldData[i].fieldFindType)
			{

				case ID: 		elem = Browser.driver().findElement(By.id(fieldData[i].fieldPath));
										break;
				
				case XPATH: 	elem = Browser.driver().findElement(By.xpath(fieldData[i].fieldPath));
											break;
				
				case CSSSELECTOR:	elem = Browser.driver().findElement(By.cssSelector(fieldData[i].fieldPath));
												break;
				
				case LINKTEXT:	elem = Browser.driver().findElement(By.linkText(fieldData[i].fieldPath));
											break;
				
				case PARTIALLINKTEXT:	elem = Browser.driver().findElement(By.partialLinkText(fieldData[i].fieldPath));
													break;
				
				case TAGNAME: 	elem = Browser.driver().findElement(By.tagName(fieldData[i].fieldPath));
											break;
				
				default:	break;

			}

			switch(fieldData[i].fieldType)
			{
				case TEXTBOX: elem.sendKeys(fieldData[i].fieldValue);
										break;

				case BUTTON: 	elem.click();
										break;	

				case TEXTAREA: elem.sendKeys(fieldData[i].fieldValue);
										 break;	

				case DROPDOWN: Select droplist = new Select(elem);
										 droplist.selectByVisibleText(fieldData[i].fieldValue);
										 break;

				case RADIOBUTTON:	elem.click();
											break;

				case CHECK: 	elem.click();
										break;	
										
				case READONLY:  ((JavascriptExecutor)Browser.driver()).executeScript("arguments[0].value=arguments[1]", elem, fieldData[i].fieldValue);
          						break;

          		case SUGGEST: 	String cadIns = fieldData[i].fieldValue;
          						char c;
          						WebElement elem2 = null;
          						WebElement sugElem = null;
          						List<WebElement> listEl;          			
          						for(int lon=0;lon<cadIns.length();lon++)	
          						{
          							c = cadIns.charAt(0);
      								elem.sendKeys(Character.toString(c));
      								Browser.waitForElement(By.xpath("//div[1]/ul/li"),5);      								
  									elem2 = Browser.driver().findElement(By.xpath("//div[1]/ul/li"));
      								listEl = elem2.findElements(By.xpath("a"));
      								for(WebElement we2:listEl)
      								{
      									if(we2.findElement(By.xpath("span[3]")).getText().equals(fieldData[i].fieldValue))
      									{
      										sugElem = we2;
      										we2.click();
      										break;
      									}      									
      								}

	      							if(sugElem!=null)
	      							{
	      								break;
	      							}
          							cadIns = cadIns.substring(1, cadIns.length());

          						}
          						Thread.sleep(1000);
          						break;		
         
				default: 	break;																																						
			}
			
		}

		return true;		

	}

}