package com.colosa.qa.automatization.common;

import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.chrome.ChromeDriver;
import org.openqa.selenium.firefox.FirefoxDriver;
import org.openqa.selenium.htmlunit.HtmlUnitDriver;
import org.openqa.selenium.ie.InternetExplorerDriver;
import org.openqa.selenium.By;
import org.openqa.selenium.support.ui.WebDriverWait;
import org.openqa.selenium.support.ui.ExpectedCondition;
import org.openqa.selenium.Platform;
import org.openqa.selenium.remote.DesiredCapabilities;
import org.openqa.selenium.remote.RemoteWebDriver;
import org.openqa.selenium.support.ui.ExpectedConditions;
import java.net.URL;
import java.io.FileNotFoundException;
import java.io.IOException;
import java.util.List;

public class Browser {

	private static WebDriver _driver = null;
	private static long _timeoutSeconds = 0;

	public static WebDriver driver() throws FileNotFoundException, IOException{
		if(_driver == null){
			String browserMode = ConfigurationSettings.getInstance().getSetting("browser.mode");
			String browserName = "firefox";

			browserName = ConfigurationSettings.getInstance().getSetting("browser.name");

			if(browserMode.equals("local")){
				_driver = (browserName.equals("chrome"))?new ChromeDriver():
					((browserName.equals("ie"))?new InternetExplorerDriver():
						new FirefoxDriver());
			}

			if(browserMode.equals("remote")){
				String browserVersion = ConfigurationSettings.getInstance().getSetting("browser.version");
				String browserPlatform = ConfigurationSettings.getInstance().getSetting("browser.platform");
				String remoteServerUrl = ConfigurationSettings.getInstance().getSetting("remote.server.url");

				System.out.printf("Remote browser:%s, version:%s, platform:%s, url:%s \n", 
					browserName, browserVersion, browserPlatform, remoteServerUrl); 

				DesiredCapabilities desiredCapabilities = new DesiredCapabilities();

				if(browserName.equals("chrome")){
					desiredCapabilities = DesiredCapabilities.chrome();				
				}else if(browserName.equals("internet explorer")){
					desiredCapabilities = DesiredCapabilities.internetExplorer();					
				}else if(browserName.equals("firefox")){
					desiredCapabilities = DesiredCapabilities.firefox();					
				}

				if(browserVersion != null && !browserVersion.equals("")){
					desiredCapabilities.setVersion(browserVersion);
				}

				if(browserPlatform != null && !browserPlatform.equals("")){
					if(browserPlatform.equals("WINDOWS")){
						desiredCapabilities.setPlatform(Platform.WINDOWS);				
					}else if(browserPlatform.equals("LINUX")){
						desiredCapabilities.setPlatform(Platform.LINUX);					
					}else if(browserPlatform.equals("MAC")){
						desiredCapabilities.setPlatform(Platform.MAC);					
					}
				}else{
					desiredCapabilities.setPlatform(Platform.ANY);
				}

				URL url=new URL(remoteServerUrl);

				_driver = new RemoteWebDriver(url, desiredCapabilities);

			}
		}
		return _driver;
	}

	public static void gotoUrl(String url){
		_driver.get(url);		
	}

	public static String title(){
		return _driver.getTitle();
	}

	public static void close(){
		_driver.close();	
	}

	/*
	private static String[] getSearchCriteria(String str, Object... args) throws Exception{		
		String key = str;
		if(str==null)
			throw new Exception("The the search criteria must be specified");
		str = ConfigurationSettings.getInstance().getSetting(str);
		if(str == null)
			throw new Exception("There's no value for the key: "+key);
		str = String.format(str, args);
		if(str.lastIndexOf("___")==-1)
			throw new Exception("The search prefix to find the element must be specified");

		return str.split("___", 2);
	}*/

	public static By getBySearchCriteria(String str, Object... args) throws Exception{
		By by = null;

		String key = str;
		if(str==null)
			throw new Exception("The the search criteria must be specified");

		if(str.lastIndexOf("___")==-1) {
			str = ConfigurationSettings.getInstance().getSetting(str);

			if(str == null)
				throw new Exception("There's no value for the key: "+key);

			str = String.format(str, args);

			if(str.lastIndexOf("___")==-1)
				throw new Exception("The search prefix to find the element must be specified");
		}

		String[] criteria = str.split("___", 2);		
		
		if(criteria[0].equals("id"))
			by = By.id(criteria[1]);
		else if(criteria[0].equals("cssSelector"))
			by = By.cssSelector(criteria[1]);
		else if(criteria[0].equals("className"))
			by = By.className(criteria[1]);
		else if(criteria[0].equals("linkText"))
			by = By.linkText(criteria[1]);
		else if(criteria[0].equals("name"))
			by = By.name(criteria[1]);
		else if(criteria[0].equals("partialLinkText"))
			by = By.partialLinkText(criteria[1]);
		else if(criteria[0].equals("tagName"))
			by = By.tagName(criteria[1]);
		else if(criteria[0].equals("xpath"))
			by = By.xpath(criteria[1]);
		else
			throw new Exception("Invalid search prefix");
		return by;
	}	

	public static WebElement getElement(String str) throws Exception{
		return Browser.findElement(Browser.getBySearchCriteria(str));	
	}

	public static List<WebElement> getElements(String str) throws Exception{
		return Browser.findElements(Browser.getBySearchCriteria(str));		
	}

	private static WebElement findElement(By searchCriteria) throws Exception{
		WebElement we = null;

		we = Browser.driver().findElement(searchCriteria);
		
		return we;
	}

	private static List<WebElement> findElements(By searchCriteria) throws Exception{
		List<WebElement> we = null;

		we = Browser.driver().findElements(searchCriteria);
		
		return we;
	}	

	/*
	private static WebElement findElement(String[] criteria) throws Exception{
		WebElement we = null;
		
		if(criteria[0].equals("id"))
			we = Browser.driver().findElement(By.id(criteria[1]));
		else if(criteria[0].equals("cssSelector"))
			we = Browser.driver().findElement(By.cssSelector(criteria[1]));
		else if(criteria[0].equals("className"))
			we = Browser.driver().findElement(By.className(criteria[1]));
		else if(criteria[0].equals("linkText"))
			we = Browser.driver().findElement(By.linkText(criteria[1]));
		else if(criteria[0].equals("name"))
			we = Browser.driver().findElement(By.name(criteria[1]));
		else if(criteria[0].equals("partialLinkText"))
			we = Browser.driver().findElement(By.partialLinkText(criteria[1]));
		else if(criteria[0].equals("tagName"))
			we = Browser.driver().findElement(By.tagName(criteria[1]));
		else if(criteria[0].equals("xpath"))
			we = Browser.driver().findElement(By.xpath(criteria[1]));
		else
			throw new Exception("Invalid search prefix");

		return we;
	}

	private static List<WebElement> findElements(String[] criteria) throws Exception{
		List<WebElement> we = null;
		
		if(criteria[0].equals("id"))
			we = Browser.driver().findElements(By.id(criteria[1]));
		else if(criteria[0].equals("cssSelector"))
			we = Browser.driver().findElements(By.cssSelector(criteria[1]));
		else if(criteria[0].equals("className"))
			we = Browser.driver().findElements(By.className(criteria[1]));
		else if(criteria[0].equals("linkText"))
			we = Browser.driver().findElements(By.linkText(criteria[1]));
		else if(criteria[0].equals("name"))
			we = Browser.driver().findElements(By.name(criteria[1]));
		else if(criteria[0].equals("partialLinkText"))
			we = Browser.driver().findElements(By.partialLinkText(criteria[1]));
		else if(criteria[0].equals("tagName"))
			we = Browser.driver().findElements(By.tagName(criteria[1]));
		else if(criteria[0].equals("xpath"))
			we = Browser.driver().findElements(By.xpath(criteria[1]));
		else
			throw new Exception("Invalid search prefix");

		return we;
	}*/

	public static WebElement getElementf(String str, Object... args) throws Exception{
		return Browser.findElement(Browser.getBySearchCriteria(str, args));
	}

	public static Boolean elementExists(String key, int ocurrences) throws Exception{
		return (Browser.findElements(Browser.getBySearchCriteria(key)).size()) == ocurrences;
	}

	public static Boolean elementExists(String key) throws Exception{
		return Browser.elementExists(key, 1);
	}

	public static boolean waitForElement(By elementLocator, long timeoutSeconds) throws Exception{
        
        final By elem = elementLocator;

		WebElement myDynamicElement = (new WebDriverWait(_driver, timeoutSeconds))
  		.until(new ExpectedCondition<WebElement>(){
        	@Override
		        public WebElement apply(WebDriver d) {
		        	return d.findElement(elem);
				}
			}

		); 
		
		return true;
     }

	public static void waitForElement(String key, long timeoutSeconds) throws Exception{

		WebDriverWait wait = new WebDriverWait(Browser.driver(), timeoutSeconds); // wait for timeoutSeconds
		wait.until(ExpectedConditions.presenceOfElementLocated(Browser.getBySearchCriteria(key)));
     }     

}