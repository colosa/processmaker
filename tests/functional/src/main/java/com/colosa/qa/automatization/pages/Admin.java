package com.colosa.qa.automatization.pages;

import java.util.List;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.interactions.Actions;
import com.colosa.qa.automatization.common.Browser;
import org.openqa.selenium.By;
import org.openqa.selenium.NoSuchElementException;
import com.colosa.qa.automatization.common.extJs.ExtJSGrid;

public class Admin extends Main{

	public Admin() throws Exception{
	}

	public static void goToLogs() throws Exception{
		Browser.driver().switchTo().defaultContent();
		Browser.driver().switchTo().frame("adminFrame");
		WebElement we = Browser.driver().findElement(By.xpath("//*[@id='west-panel']/div[1]/div[2]/ul/li[@id='west-panel__logs']/a[2]"));
		we.click();
		//Browser.driver().switchTo().defaultContent();

	}


	public static String eventStatus(Integer numCase) throws Exception{

		Browser.driver().switchTo().defaultContent();
		Browser.driver().switchTo().frame("adminFrame");
		Browser.driver().switchTo().frame("setup-frame");
		ExtJSGrid grid = new ExtJSGrid(Browser.driver().findElement(By.id("eventsGrid")), Browser.driver());
		String status;
		WebElement row = grid.getRowByColumnValue("Case Title", "#" + Integer.toString(numCase));
		if(row==null)
			throw new Exception("Case # "+Integer.toString(numCase)+" not found in Event Logs");
		status = row.findElement(By.xpath("table/tbody/tr/td[13]/div")).getText().trim();
		Browser.driver().switchTo().defaultContent();
		return status;
	}



}