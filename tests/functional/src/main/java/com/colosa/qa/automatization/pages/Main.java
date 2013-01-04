package com.colosa.qa.automatization.pages;

import java.util.List;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.By;
import com.colosa.qa.automatization.common.*;
import java.io.FileNotFoundException;
import java.io.IOException;

public class Main extends Page{

	WebElement weSectionButton;
	WebElement weLogout;
	List<WebElement> wel;

	public Main() throws FileNotFoundException, IOException{

	}

	/*public void goSection(String sectionName) throws FileNotFoundException, IOException, Exception{

		//this.we = null;
		Browser.driver().switchTo().defaultContent();
		System.out.println("Redireccionando a seccion "+sectionName+"...");
		//this.waitForElementPresent(By.cssSelector("ul#pm_menu li a"),60);
		//this.we = Browser.driver().findElement(By.id(ConfigurationSettings.getInstance().getSetting("main.menu")));

		this.weMainMenu = Browser.getElement("main.WebElement.DashboardMenu");

		this.weSectionButton = this.weMainMenu.findElement(By.linkText(sectionName));
		
		
		//System.out.println(this.we); //raro pero se necesita esta linea para que funcione correctamente
		if(this.weSectionButton == null)
			throw new Exception("Invalid section name.");

		this.weSectionButton.click();
	}*/

	public void goHome() throws FileNotFoundException, IOException, Exception{
		this.weSectionButton = Browser.getElement("main.WebElement.HomeMenu");
		
		this.weSectionButton.click();
			
	}

	public void goDesigner() throws FileNotFoundException, IOException, Exception{
		this.weSectionButton = Browser.getElement("main.WebElement.DesignerMenu");
		
		this.weSectionButton.click();
	}

	public void goDashboards() throws FileNotFoundException, IOException, Exception{
		this.weSectionButton = Browser.getElement("main.WebElement.DashboardMenu");

		this.weSectionButton.click();
	}

	public void goAdmin() throws FileNotFoundException, IOException, Exception{
		this.weSectionButton = Browser.getElement("main.WebElement.AdminMenu");
		
		this.weSectionButton.click();
	}

	public void logout() throws FileNotFoundException, IOException, Exception{
		//Browser.driver().switchTo().defaultContent();
		//if(this.skin==0)
		Browser.getElement("main.WebElement.Logout").click();
	}

}