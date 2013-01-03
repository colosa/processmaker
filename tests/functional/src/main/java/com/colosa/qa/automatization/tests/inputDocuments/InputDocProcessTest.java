package com.colosa.qa.automatization.inputDocuments;

import java.util.List;

import org.junit.AfterClass;
import org.junit.BeforeClass;
import org.junit.Test;
import org.junit.Rule;
import org.openqa.selenium.By;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.chrome.ChromeDriver;
import org.openqa.selenium.support.ui.ExpectedConditions;
import org.openqa.selenium.support.ui.Select;
import org.openqa.selenium.support.ui.WebDriverWait;
import com.colosa.qa.automatization.*;

public class InputDocProcessTest{

	public static WebDriver driver;    

    @BeforeClass
    public static void setUpBeforeClass() throws Exception{
        driver = new ChromeDriver();
        //testChrome = new WebDriverFunctionsPMOS2(driver,0);
        driver.get("http://192.168.11.132/sys/en/classic/login/login");

    }

    @Test
    public void testInputDocProcessTest() throws InterruptedException, Exception{

       
        WebDriverFunctionsPMOS2 testChrome = new WebDriverFunctionsPMOS2(driver,0);

        //login("admin","admin","wsqa");


        testChrome.login("admin","admin","wsqa");

        testChrome.selectMenuTreePanelOption("Cases/New case");

        testChrome.startCase("No Category/input docs (enviar formulario y documento)");

        testChrome.waitForElementPresent(By.id("openCaseFrame"),10);

        driver.switchTo().frame("openCaseFrame");

        WebElement elem = driver.findElement(By.name("form[nombre]"));
        elem.sendKeys("Ernesto Vega");

        typeinEditbox(driver,"name","form[nombre]","Ernesto Vega");
        typeinEditbox(driver,"name","form[fechanacimiento]","1987-12-29");
        typeinEditbox(driver,"name","form[CI]","6812789");
        selectValue(driver,"industrial");
        selectRadiobutton(driver,"id","form[beca][par]");
        clickButton(driver, "id", "form[send]");

        clickButton(driver, "id", "form[MNU_NEW]");
        
        typeinEditbox(driver,"name","form[APP_DOC_FILENAME]","D:\\Documents\\Downloads\\krlos_1.doc");
        typeinEditbox(driver,"name","form[APP_DOC_COMMENT]","Documento de Prueba");
        clickButton(driver, "id", "form[SAVE]");

        clickButton(driver, "id", "form[BTN_SUBMIT]");

        clickButton(driver, "id", "form[MNU_NEW]");

        typeinEditbox(driver,"name","form[APP_DOC_FILENAME]","D:\\Documents\\Downloads\\krlos_1.doc");
        typeinEditbox(driver,"name","form[APP_DOC_COMMENT]","Documento de Prueba");
        clickButton(driver, "id", "form[SAVE]");

        clickButton(driver, "id", "form[BTN_SUBMIT]");

        clickButton(driver, "id", "btnContinue");
        
        //testChrome.waitForElementPresent(By.id("gridPanel"),10);
        

        testChrome.selectMenuTreePanelOption("Documents/");
        driver.switchTo().frame("casesSubFrame");

        WebElement grd = driver.findElement(By.id("dirTreePanel"));
        
        List<WebElement> elem2 = grd.findElements(By.xpath("div[2]/div/ul/li/ul/li"));

        for(WebElement el2:elem2)
        if(el2.findElement(By.xpath("div/a/span")).getText().equals("admin"))
        {
            elem = el2;
            break;
        }

        


        if(elem==null)
            System.out.println("no se encontr\u00FB la carpeta"); 
        else
        {
            elem.click();
            //action.perform();
        }

       // testChrome.waitForElementPresent(By.id("gridPanel"),10);
        
        
        

        /*WebDriverWait wait = new WebDriverWait(driver,10);
        wait.until(ExpectedConditions.elementToBeClickable(By.xpath("table/tbody/tr/td[1]/div/b")));


        WebElement grd2 = driver.findElement(By.id("gridpanel"));
        List<WebElement> elem3 = grd2.findElements(By.xpath("div/div[2]/div/div[1]/div[2]/div/div"));
        




        for(WebElement el3:elem3)
            if(el3.findElement(By.xpath("table/tbody/tr/td[1]/div/b")).getText().equals("krlos_1.doc"))
            {
                elem = el3;
                break;
            }
            
            



        if(elem==null)
            System.out.println("no se encontr\u00FB el archivo"); //talvez se deberia lanzar un error
        else
        {
            elem.click();

            //WebElement elem4 = grd2.findElement(By.xpath("div/div[1]/div/table/tbody/tr/td[1]/table/tbody/tr/td[11]/table/tbody/tr[2]/td[2]/em/button"));
            //elem4.click();
            //action.perform();
        }*/

        //System.out.println(elem3.size());


        

        //wait.until(ExpectedConditions.textToBePresentInElementValue(By.xpath("//div[@id='"+grid.getAttribute("id")+"']/div/div[3]/div/table/tbody/tr/td/table/tbody/tr/td[5]/input"),"1"))



        /*try{
             

            elem = driver.findElement(By.xpath("//div[@id='gridpanel']/div/div[2]/div//div[b='Receipts_admin']/b"));
            elem.click();
            elem = driver.findElement(By.xpath("//div[@id='gridpanel']/div/div[2]/div/div[1]/div[2]/div/div//div[b='krlos_1.doc']/b"));
            elem.click();
            
        }catch(java.lang.StringIndexOutOfBoundsException e){
            System.out.println("El elemento no esta disponible");
        }*/
        


        System.out.println("------------------end----------------------");


       
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

    public static void selectRadiobutton(WebDriver driver, String identifyBy, String locator){
        if (identifyBy.equalsIgnoreCase("xpath")){
            driver.findElement(By.xpath(locator)).click();
        }else if (identifyBy.equalsIgnoreCase("id")){
            driver.findElement(By.id(locator)).click();
        }else if (identifyBy.equalsIgnoreCase("name")){
            driver.findElement(By.name(locator)).click();
        }

    }

  
    @AfterClass
    public static void tearDownAfterClass() throws Exception {
        System.out.println("Execution completed.....");
        //driver.quit(); //if you want to stop the webdriver after execution, then remove the comment
    }
}