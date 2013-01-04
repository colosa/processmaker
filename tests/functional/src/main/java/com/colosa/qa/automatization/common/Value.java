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

public class Value{

	public static String getValue(FieldKeyType keyType, String pathElem) throws FileNotFoundException, IOException, Exception{
		WebElement elem = null;
		switch(keyType)
			{

				case ID: 		elem = Browser.driver().findElement(By.id(pathElem));
										break;
				
				case XPATH: 	elem = Browser.driver().findElement(By.xpath(pathElem));
											break;
				
				case CSSSELECTOR:	elem = Browser.driver().findElement(By.cssSelector(pathElem));
												break;
				
				case LINKTEXT:	elem = Browser.driver().findElement(By.linkText(pathElem));
											break;
				
				case PARTIALLINKTEXT:	elem = Browser.driver().findElement(By.partialLinkText(pathElem));
													break;
				
				case TAGNAME: 	elem = Browser.driver().findElement(By.tagName(pathElem));
											break;
				
				default:	break;

			}

		return elem.getAttribute("value");	

	}
}
