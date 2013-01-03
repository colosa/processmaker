package com.colosa.qa.automatization;

import java.util.List;

import org.junit.After;
import org.junit.Before;
import org.junit.Test;
import org.junit.Rule;
import org.openqa.selenium.Alert;
import org.openqa.selenium.By;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.chrome.ChromeDriver;
//import org.openqa.selenium.support.ui.Select;
import com.colosa.qa.automatization.*;

public class OutputDocFunctionalTest{
    public static WebDriver driver;
    

    @Before
    public static void setUpBeforeClass() throws Exception{
        driver = new ChromeDriver();
        //testChrome = new WebDriverFunctionsPMOS2(driver,0);
        driver.get("http://192.168.11.132/sys/en/classic/login/login");

    }

    @Test
 	public void testOutputDocFunctionalTest() throws InterruptedException{

        WebDriverFunctionsPMOS2 testChrome = new WebDriverFunctionsPMOS2(driver,0);

        login("admin","admin","wsqa");

		
        testChrome.openProcess("Test List Output Docs");


        testChrome.waitForElementPresent(By.id("OUTPUTDOCS"),10);

        clickButton(driver, "id", "OUTPUTDOCS");

        //testChrome.waitForElementPresent(By.id("MNU_ADD"),15);

        clickLink(driver, "id", "form[MNU_ADD]");

        typeinEditbox(driver,"name","form[OUT_DOC_TITLE]","Prueba 1");
        typeinEditbox(driver,"name","form[OUT_DOC_FILENAME]","Prueba 1");
        typeinEditbox(driver,"name","form[OUT_DOC_DESCRIPTION]","Prueba 1");
        typeinEditbox(driver,"name","form[OUT_DOC_TITLE]","Prueba 1");
        selectValue(driver,"Legal");
        selectValue(driver,"1");
        selectValue(driver,"DOC");
        selectValue(driver,"1");
        selectValue(driver,"1");
        typeinEditbox(driver,"name","form[OUT_DOC_DESTINATION_PATH]","Prueba 1");
        typeinEditbox(driver,"name","form[OUT_DOC_TAGS]","Prueba 1");
        clickButton(driver, "id", "form[ACCEPT]");


        System.out.println("------------------End--------------------");
    
    }

    public static void login(String user, String pwd, String env){

           
        typeinEditbox(driver,"name","form[USR_USERNAME]",user);
        typeinEditbox(driver,"name","form[USR_PASSWORD]",pwd);
        typeinEditbox(driver,"id","form[USER_ENV]",env);
        clickButton(driver,"id","form[BSUBMIT]");
                    


    }

    public static void clickButton(WebDriver driver, String identifyBy, String locator){
        if (identifyBy.equalsIgnoreCase("xpath")){
                driver.findElement(By.xpath(locator)).click();
        }else if (identifyBy.equalsIgnoreCase("id")){
                driver.findElement(By.id(locator)).click();
        }else if (identifyBy.equalsIgnoreCase("name")){
                driver.findElement(By.name(locator)).click();
        }

    }	

    public static void typeinEditbox(WebDriver driver, String identifyBy, String locator, String valuetoType){
        if (identifyBy.equalsIgnoreCase("xpath")){
                driver.findElement(By.xpath(locator)).sendKeys(valuetoType);
        }else if (identifyBy.equalsIgnoreCase("id")){
                driver.findElement(By.id(locator)).sendKeys(valuetoType);
        }else if (identifyBy.equalsIgnoreCase("name")){
                driver.findElement(By.name(locator)).sendKeys(valuetoType);
        }

    }

    public static void clickLink(WebDriver driver, String identifyBy, String locator){
        if (identifyBy.equalsIgnoreCase("xpath")){
            driver.findElement(By.xpath(locator)).click();
        }else if (identifyBy.equalsIgnoreCase("id")){
            driver.findElement(By.id(locator)).click();
        }else if (identifyBy.equalsIgnoreCase("name")){
            driver.findElement(By.name(locator)).click();
        }else if (identifyBy.equalsIgnoreCase("name")){
            driver.findElement(By.linkText(locator)).click();
        }else if (identifyBy.equalsIgnoreCase("name")){
            driver.findElement(By.partialLinkText(locator)).click();
        }
    }

    public static void selectValue(WebDriver driver, String valToBeSelected){
        List <WebElement> options = driver.findElements(By.tagName("option"));
        for (WebElement option : options) {
            if (valToBeSelected.equalsIgnoreCase(option.getText())){
                option.click();
            }
        }
    }

  
    @After
    public static void tearDownAfterClass() throws Exception {
        System.out.println("Execution completed.....");
        //driver.quit(); //if you want to stop the webdriver after execution, then remove the comment
    }
}