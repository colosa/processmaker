/*
Pensado y Desarrollado por Daniel Canedo
para Colosa QA
http://duhnnie.net
last update: 2012-10-22 Hrs. 14:34
*/

package com.colosa.qa.automatization.common.extJs;

import java.util.List;
//import java.util.concurrent.TimeUnit;
import java.lang.Exception;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.By;
//import org.openqa.selenium.Keys;
import org.openqa.selenium.interactions.Actions;
//import org.openqa.selenium.JavascriptExecutor;
import org.openqa.selenium.support.ui.WebDriverWait;
//import org.openqa.selenium.support.ui.ExpectedConditions;
import org.openqa.selenium.support.ui.ExpectedCondition;

public class ExtJSGrid{

	private WebDriver driver;
	private WebElement grid;
	private List<WebElement> pager;
	private WebElement btn_first;
	private WebElement btn_previous;
	private WebElement btn_next;
	private WebElement btn_last;
	private WebElement btn_refresh;
	private int currentPage;
	private int totalPages;
	private int items; //*falta dar utilidad
	private int timeout;
	
	public ExtJSGrid(WebElement grid, WebDriver driver, int timeout){
		this.driver = driver;
		this.grid = grid;
		this.timeout = timeout;
		this.pager = this.grid.findElements(By.xpath("div/div[3]/div/table/tbody/tr/td/table/tbody/tr/td"));
		this.btn_first = this.pager.get(0).findElement(By.xpath("table/tbody/tr[2]/td[2]/em/button"));
		this.btn_previous = this.pager.get(1).findElement(By.xpath("table/tbody/tr[2]/td[2]/em/button"));
		this.btn_next = this.pager.get(7).findElement(By.xpath("table/tbody/tr[2]/td[2]/em/button"));
		this.btn_last = this.pager.get(8).findElement(By.xpath("table/tbody/tr[2]/td[2]/em/button"));
		this.btn_refresh = this.pager.get(10).findElement(By.xpath("table/tbody/tr[2]/td[2]/em/button"));
		this.init();
	}

	public ExtJSGrid(WebElement grid, WebDriver driver){
		this(grid, driver, 30);
	}

	private void init(){
		if(!this.driver.getClass().getName().equals("org.openqa.selenium.firefox.FirefoxDriver"))
		{
			WebDriverWait wait = new WebDriverWait(this.driver, this.timeout);
			//try{
				//wait.until(new PageStatusChanged(this.grid.findElement(By.xpath("div/div[3]/div/table/tbody/tr/td[2]/table/tbody/tr/td[1]/table/tbody/tr/td/div")).getText().trim()));
				//wait.until(new PageStatusChanged(""));
			String[] statuses = {"", "Displaying Empty"};
			wait.until(new PageStatusChanged(statuses));
			//}
		}
		this.updateCurrentPage();
		this.updateTotalPages();
	}

	private void updateTotalPages(){
		this.totalPages = Integer.parseInt(this.pager.get(5).findElement(By.xpath("div")).getText().trim().substring(3));
	}

	private void updateCurrentPage(){
		this.currentPage = Integer.parseInt(this.pager.get(4).findElement(By.xpath("input")).getAttribute("value"));
	}

	public int getCurrentPage(){
		return this.currentPage;
	}

	public int getTotalPages(){
		return this.totalPages;
	}

	public int nextPage(){
		System.out.println("next");
		return this.moveToPage(1);
	}

	public int previousPage(){
		return this.moveToPage(0);
	}

	public int firstPage(){
		return this.moveToPage(2);
	}

	public int lastPage(){
		return this.moveToPage(3);	
	}

	public int refreshPage(){
		return this.moveToPage(4);
	}

	public List<WebElement> getRows(){
		return this.grid.findElements(By.xpath("div/div[2]/div/div[1]/div[2]/div/div"));
	}

	public WebElement getRowByColumnValue(String columnName, String columnValue) throws Exception{
		WebElement header = this.grid.findElement(By.xpath("div/div[2]/div/div[1]/div[1]"));
		List<WebElement> headerFields = header.findElements(By.xpath("div/div/table/thead/tr/td"));
		int columnNumber = 1;
		boolean flag = false;
		for(WebElement field:headerFields)
		{
			if(field.getText().trim().equals(columnName))
			{
				flag = true;
				break;
			}
			columnNumber++;
		}
		if(!flag)
			throw new Exception("No se encontro una columna en el grid con el nombre \""+columnName+"\"");
		flag = false;
		while(this.currentPage <= this.totalPages)
		{	
			headerFields = this.getRows();
			System.out.println(headerFields.size()+"   "+this.currentPage +" de "+ this.totalPages);
			for(WebElement row:headerFields)
				if(row.findElement(By.xpath("table/tbody/tr/td["+Integer.toString(columnNumber)+"]/div")).getText().trim().equals(columnValue))		
					return row;
			if(this.currentPage == this.totalPages)
				break;
			this.nextPage();
		}
		return null;
	}

	private int moveToPage(int option){
		WebDriverWait wait = new WebDriverWait(this.driver, this.timeout);
		PageStatusChanged statusChanged = new PageStatusChanged();
		PageRefreshed pageRefreshed = new PageRefreshed();
		
		switch(option)
		{
			case 0:
				if(this.currentPage == 1)
					return 0;
				this.btn_previous.click();
				break;
			case 1: 
				if(this.currentPage >= this.totalPages)
					return 0;
				this.btn_next.click();
				break;
			case 2: 
				if(this.currentPage == 1)
					return 0;
				this.btn_first.click();
				break;
			case 3: 
				if(this.currentPage == this.totalPages)
					return 0;
				this.btn_last.click();
				break;
			case 4:
				this.btn_refresh.click();
		}
		if(option!=4)
			wait.until(statusChanged);
		else
			wait.until(pageRefreshed);
		this.updateTotalPages();
		this.updateCurrentPage();
		return this.getCurrentPage();
	}

	private int inArray(String[] arr, String str){
		for(int i=0; i<arr.length; i++)
			if(arr[i].equals(str))
				return i;
		return -1;
	}

	private class PageStatusChanged implements ExpectedCondition<Boolean>{

		private String status;
		private String[] statuses = {};

		public PageStatusChanged(String status){
			this.status = status;
			System.out.println("actual "+this.status);
		}

		public PageStatusChanged(String[] statuses){
			this.statuses = statuses;
		}

		public PageStatusChanged(){
			this.status = grid.findElement(By.xpath("div/div[3]/div/table/tbody/tr/td[2]/table/tbody/tr/td[1]/table/tbody/tr/td/div")).getText().trim();
			//System.out.println("actual "+this.status);
		}

		@Override
		public Boolean apply(WebDriver input) {
			String actual = grid.findElement(By.xpath("div/div[3]/div/table/tbody/tr/td[2]/table/tbody/tr/td[1]/table/tbody/tr/td/div")).getText().trim();
			//System.out.println(actual+" / "+this.status+" => "+!actual.equals(this.status));
			if(this.statuses.length == 0)
				return !actual.equals(this.status);
			else
				return !(inArray(this.statuses, actual)>-1);
		}	  
	}

	private class PageRefreshed implements ExpectedCondition<Boolean>{

		@Override
		public Boolean apply(WebDriver input) {
			return pager.get(10).findElement(By.xpath("table")).getAttribute("class").indexOf("x-item-disabled") == -1;
		}	  
	}

}