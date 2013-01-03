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

public class ProcessList extends Page{
	WebElement webEl;
	WebElement procName;
	WebElement desc;
	public ProcessList() throws FileNotFoundException, IOException{
		

	}


	public boolean openProcess(String processName) throws FileNotFoundException, IOException, Exception{

		WebElement we = null;
		Actions action = new Actions(Browser.driver());

		Browser.driver().switchTo().frame("frameMain");
				
		WebElement grid = Browser.getElement("designerList.webelement.Process");
		ExtJSGrid extGrid = new ExtJSGrid(grid, Browser.driver());
		WebElement pager = Browser.driver().findElement(By.xpath("//div[@id='processesGrid']/div/div[3]/div/table/tbody/tr/td[1]/table/tbody/tr"));
		List<WebElement> wl;
		int index = 1;
		int pages = Integer.parseInt(pager.findElement(By.xpath("td[6]/div")).getText().trim().substring(3));

		while(extGrid.getCurrentPage()<=extGrid.getTotalPages() && we==null){
			System.out.println("Buscando en pagina "+extGrid.getCurrentPage() +" de "+ extGrid.getTotalPages()+"...");
			wl = grid.findElements(By.xpath("div/div[2]/div/div[1]/div[2]/div/div"));
			for(WebElement we2:wl)
			{
				//System.out.println(we2.findElement(By.xpath("table/tbody/tr[1]/td[5]/div")).getText()+"   "+processName);
				if(we2.findElement(By.xpath("table/tbody/tr[1]/td[5]/div")).getText().equals(processName))
				{
					we = we2;
					System.out.println("Se encontro el proceso \""+processName+"\"");
					break;
				}
			}
			if(extGrid.getCurrentPage()==extGrid.getTotalPages() || we!=null)
				break;
			extGrid.nextPage();
		}		

		if(we==null)
			System.out.println("ERROR al intentar abrir el proceso \""+processName+"\", no se encontró el proceso"); //talvez se deberia lanzar un error
		else
		{
			System.out.println("Abriendo proceso \""+processName+"\"...");
			action.doubleClick(we);
        	action.perform();
		}
		return true;

	}

	public void exportProcess(String processName) throws FileNotFoundException, IOException, Exception{
		WebElement we = null;
		Actions action = new Actions(Browser.driver());

		Browser.driver().switchTo().frame("frameMain");
				
		WebElement grid = Browser.getElement("designerList.webelement.Process");
		List<WebElement> menulist = grid.findElements(By.xpath("div/div[1]/div/table/tbody/tr/td[1]/table/tbody/tr/td"));
		WebElement exportbtn = menulist.get(6).findElement(By.xpath("table/tbody/tr[2]/td[2]/em/button"));

		ExtJSGrid extGrid = new ExtJSGrid(grid, Browser.driver());
		WebElement pager = Browser.driver().findElement(By.xpath("//div[@id='processesGrid']/div/div[3]/div/table/tbody/tr/td[1]/table/tbody/tr"));//no funciona al crear su variable en el default.conf
		List<WebElement> wl;
		int index = 1;
		int pages = Integer.parseInt(pager.findElement(By.xpath("td[6]/div")).getText().trim().substring(3));

		System.out.println("Buscando proceso \""+processName+"\"...");

		while(extGrid.getCurrentPage()<=extGrid.getTotalPages() && we==null){
			System.out.println("Buscando en pagina "+extGrid.getCurrentPage() +" de "+ extGrid.getTotalPages()+"...");
			wl = grid.findElements(By.xpath("div/div[2]/div/div[1]/div[2]/div/div"));
			for(WebElement we2:wl)
			{
				//System.out.println(we2.findElement(By.xpath("table/tbody/tr[1]/td[5]/div")).getText()+"   "+processName);
				if(we2.findElement(By.xpath("table/tbody/tr[1]/td[5]/div")).getText().equals(processName))
				{
					we = we2;
					System.out.println("Se encontro el proceso \""+processName+"\"");
					break;
				}
			}
			if(extGrid.getCurrentPage()==extGrid.getTotalPages() || we!=null)
				break;
			extGrid.nextPage();
		}		

		if(we==null)
			System.out.println("ERROR al intentar abrir el proceso \""+processName+"\", no se encontró el proceso"); //talvez se deberia lanzar un error
		else
		{
			System.out.println("Seleccionando proceso \""+processName+"\"...");
			action.click(we);
        	action.perform();
		}
		
		exportbtn.click();
		
	}

	public void importProcess(String filePath) throws FileNotFoundException, IOException, Exception{
		
		JavascriptExecutor js = (JavascriptExecutor) Browser.driver();
		Actions action = new Actions(Browser.driver());

		Browser.driver().switchTo().frame("frameMain");
		WebElement grid = Browser.getElement("designerList.webelement.Process");
		List<WebElement> menulist = grid.findElements(By.xpath("div/div[1]/div/table/tbody/tr/td[1]/table/tbody/tr/td"));
		WebElement importbtn = menulist.get(7).findElement(By.xpath("table/tbody/tr[2]/td[2]/em/button"));
		importbtn.click();

		js.executeScript("document.getElementById('form-file').value=\"/home/ernesto/Documents/4WD_PROCESS_(Original)1 (1).pm\"");
		js.executeScript("document.getElementById('form-file').style.display = 'none'");
		js.executeScript("document.getElementById('form-file-file').style.opacity = 1");

		WebElement inputfile = Browser.getElement("designerList.webelement.inputfile");
		inputfile.sendKeys(filePath);

		Browser.driver().findElement(By.xpath("//div[@id='uploader']/div[2]/div[2]/div/div/div/div[1]/table/tbody/tr/td[2]/table/tbody/tr/td[1]/table/tbody/tr/td[1]/table/tbody/tr[2]/td[2]/em/button")).click();//no funciona al crear su variable en el default.conf

		System.out.println("-------fin-------");		
		

	}

	public void newProcess(String processName, String  description) throws FileNotFoundException, IOException, Exception{

		Browser.driver().switchTo().frame("frameMain");
		WebElement grid = Browser.getElement("designerList.webelement.Process");		
		List<WebElement> menulist = grid.findElements(By.xpath("div/div[1]/div/table/tbody/tr/td[1]/table/tbody/tr/td"));//no funciona al crear su variable en el default.conf
		WebElement importbtn = menulist.get(0).findElement(By.xpath("table/tbody/tr[2]/td[2]/em/button"));
		importbtn.click();

		Browser.getElement("designerList.webelement.processName").sendKeys(processName);
		Browser.getElement("designerList.webelement.description").sendKeys(description);
		Browser.driver().findElement(By.xpath("//div[@id='newProcessForm']/div/div/div[1]/table/tbody/tr/td[2]/table/tbody/tr/td[1]/table/tbody/tr/td[1]/table/tbody/tr[2]/td[2]/em/button")).click();//no funciona al crear su variable en el default.conf
				
	}

}

