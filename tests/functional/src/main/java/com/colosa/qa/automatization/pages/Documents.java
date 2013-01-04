package com.colosa.qa.automatization.pages;

import java.util.List;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.By;
import com.colosa.qa.automatization.common.*;
import com.colosa.qa.automatization.common.extJs.*;
import java.io.FileNotFoundException;
import java.io.IOException;

public class Documents extends Page{

	WebElement dirRoot;
	List<WebElement> dirs;
	WebElement panel;
	List<WebElement> files;

	public Documents() throws FileNotFoundException, IOException{

	}

	public void selectFolder(String folderName) throws FileNotFoundException, IOException, Exception{


		Browser.driver().switchTo().frame("casesSubFrame");
		this.dirRoot = Browser.getElement("documents.WebElement.Root");
        
        this.dirs = this.dirRoot.findElements(By.xpath("div[2]/div/ul/li/ul/li"));

        for(WebElement elementdir:this.dirs)
        if(elementdir.findElement(By.xpath("div/a/span")).getText().equals(folderName))
        {
            elementdir.click();
            break;
        }
        else
        {
        	System.out.println("No se encontró el directorio");
        	break;
        }


	}

	public void downloadDocument(String fileName) throws FileNotFoundException, IOException, Exception{

		ExtJSGrid grid = new ExtJSGrid(Browser.getElement("documents.WebElement.Panel"),Browser.driver());
		
		this.panel =  grid.getRowByColumnValue("Name", fileName);
		this.panel.click();
		Browser.getElement("documents.WebElement.Download").click();
		
	}
}