package com.colosa.qa.automatization.pages;

import java.util.List;
import java.util.ArrayList;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.By;
import org.openqa.selenium.Keys;
import org.openqa.selenium.interactions.Actions;
import org.openqa.selenium.JavascriptExecutor;
import org.openqa.selenium.support.ui.Select;
import com.colosa.qa.automatization.common.*;
import com.colosa.qa.automatization.common.extJs.*;
import java.io.FileNotFoundException;
import java.io.IOException;

public class Designer{

	protected static int divCount = 1;
	protected static String taskPath;
	protected static String divBelowPath;
	protected static String divUpPath;
	protected static String divRightBottomPath;
	protected static int taskNum = 1;
	protected static int taskN = 0;
	protected static String[][] elements = new String[20][4];

	public void Designer() throws IOException, FileNotFoundException, Exception{

		
	}

	public boolean createTask() throws Exception{

		String taskName = "Task ";
		int tNum = taskNum + 1; 
		Actions action = new Actions(Browser.driver());
		Browser.waitForElement(By.className("processmap_title___processmaker"),15);
		WebElement gridPanel = Browser.driver().findElement(By.id("pm_target"));
		action.contextClick(gridPanel).perform();

		WebElement taskElem = Browser.driver().findElement(By.xpath("//div[2]/div[4]/div[2]"));

		if(taskElem.getText().equals("Add task"))
		{
			taskElem.click();
		}
		taskName = taskName + tNum;

		taskList();
		taskN++;

		return true; 

	}

	public boolean initialTask(String taskName) throws Exception{

		WebElement task = getTask(taskName);

		Browser.driver().switchTo().defaultContent();
		Browser.driver().switchTo().frame("frameMain");

		Actions action = new Actions(Browser.driver());
				
		(new Actions(Browser.driver())).dragAndDrop(Browser.getElement("designer.webElement.initialTask"), task).build().perform();

		return true;
	}

	public boolean sequential(String taskName1, String taskName2) throws Exception{

		WebElement task1 = getTask(taskName1);
		WebElement task2 = getTask(taskName2);

		Browser.driver().switchTo().defaultContent();
		Browser.driver().switchTo().frame("frameMain");

		Actions action = new Actions(Browser.driver());
				
		(new Actions(Browser.driver())).dragAndDrop(Browser.getElement("designer.webElement.sequential"), task1).build().perform();

		(new Actions(Browser.driver())).dragAndDropBy(task2, 0, 0).build().perform(); 

		return true;

	}

	public boolean selection(String taskName1, String[][] taskName2) throws Exception{

		WebElement task1 = getTask(taskName1);
		WebElement task2 = getTask(taskName2[0][0]);

		Browser.driver().switchTo().defaultContent();
		Browser.driver().switchTo().frame("frameMain");

		Actions action = new Actions(Browser.driver());
				
		(new Actions(Browser.driver())).dragAndDrop(Browser.getElement("designer.webElement.selection"), task1).build().perform();

		(new Actions(Browser.driver())).dragAndDropBy(task2, 0, 0).build().perform(); 

		editCondition(taskName1, taskName2, 1);

		return true;

	}

	public boolean evaluation(String taskName1, String[][] taskName2) throws Exception{

		WebElement task1 = getTask(taskName1);
		WebElement task2 = getTask(taskName2[0][0]);

		if(task1==null||task2==null)
			throw new Exception();

		Browser.driver().switchTo().defaultContent();
		Browser.driver().switchTo().frame("frameMain");

		Actions action = new Actions(Browser.driver());
				
		(new Actions(Browser.driver())).dragAndDrop(Browser.getElement("designer.webElement.evaluation"), task1).build().perform();

		(new Actions(Browser.driver())).dragAndDropBy(task2, 0, 0).build().perform(); 

		editCondition(taskName1, taskName2, 2);

		return true;

	}

	public boolean parallelFork(String taskName1, String[][] taskName2) throws Exception{

		WebElement task1 = getTask(taskName1);
		WebElement task2 = getTask(taskName2[0][0]);

		if(task1==null||task2==null)
			throw new Exception();

		Browser.driver().switchTo().defaultContent();
		Browser.driver().switchTo().frame("frameMain");

		Actions action = new Actions(Browser.driver());
				
		(new Actions(Browser.driver())).dragAndDrop(Browser.getElement("designer.webElement.parallelFork"), task1).build().perform();

		(new Actions(Browser.driver())).dragAndDropBy(task2, 0, 0).build().perform();

		editCondition(taskName1, taskName2, 3);
				
		return true;
				
	}

	public boolean parallelByEvaluation(String taskName1, String[][] taskName2)  throws Exception{

		WebElement task1 = getTask(taskName1);
		WebElement task2 = getTask(taskName2[0][0]);

		if(task1==null||task2==null)
			throw new Exception();

		Browser.driver().switchTo().defaultContent();
		Browser.driver().switchTo().frame("frameMain");

		Actions action = new Actions(Browser.driver());
				
		(new Actions(Browser.driver())).dragAndDrop(Browser.getElement("designer.webElement.parallelByEvaluation"), task1).build().perform();

		(new Actions(Browser.driver())).dragAndDropBy(task2, 0, 0).build().perform();

		editCondition(taskName1, taskName2, 4);
		
		return true;

	}

	public boolean parallelJoin(String taskName1, String taskName2) throws Exception{

		WebElement task1 = getTask(taskName1);
		WebElement task2 = getTask(taskName2);

		if(task1==null||task2==null)
			throw new Exception();

		Browser.driver().switchTo().defaultContent();
		Browser.driver().switchTo().frame("frameMain");

		Actions action = new Actions(Browser.driver());
				
		(new Actions(Browser.driver())).dragAndDrop(Browser.getElement("designer.webElement.parallelJoin"), task1).build().perform();

		(new Actions(Browser.driver())).dragAndDropBy(task2, 0, 0).build().perform();

		return true;
		
	}

	public boolean endTask(String taskName) throws Exception{

		WebElement task = getTask(taskName);

		if(task==null)
			throw new Exception();

		Browser.driver().switchTo().defaultContent();
		Browser.driver().switchTo().frame("frameMain");

		Actions action = new Actions(Browser.driver());
				
		(new Actions(Browser.driver())).dragAndDrop(Browser.getElement("designer.webElement.endTask"), task).build().perform();
	
		return true;
	}

	public void moveTask(String taskName, int positionX, int positionY) throws Exception{

		WebElement task = getTask(taskName);
		(new Actions(Browser.driver())).dragAndDropBy(task, positionX, positionY).build().perform();

	}


	public WebElement getTask(String taskName) throws Exception{
		WebElement designPanel = Browser.getElement("designer.webElement.designPanel");
		return designPanel.findElement(By.xpath("div[@class='processmap_task___processmaker'][div[1]='"+taskName.trim()+"']"));
	}

	public void taskList() throws Exception{
		taskNum++;
		taskPath = "//div[@id='pm_target']/div[1]/div[1]/div[3]/div["+taskNum+"]";
		taskNum++;
		divBelowPath = "//div[@id='pm_target']/div[1]/div[1]/div[3]/div["+taskNum+"]";
		taskNum++;
		divRightBottomPath = "//div[@id='pm_target']/div[1]/div[1]/div[3]/div["+taskNum+"]";
		taskNum++;
		divUpPath = "//div[@id='pm_target']/div[1]/div[1]/div[3]/div["+taskNum+"]";


		elements[taskN][0] = taskPath;
		elements[taskN][1] = divBelowPath;
		elements[taskN][2] = divRightBottomPath;
		elements[taskN][3] = divUpPath;

	}

	public void editCondition(String taskName, String[][] arrayTasks, int conditionType) throws Exception{


		int i = 0;			
		boolean founded = false;
		WebElement elem = null;
		WebElement btnAdd = null;
		WebElement btnSend = null;
		Select droplist;
		int dropdownCount = 0;
		String namePath = "";

		if(conditionType==1){
			namePath = "GRID_SELECT_TYPE";
		}
		else
		{
			if(conditionType==2){
				namePath = "GRID_EVALUATE_TYPE";
			}
			else
			{
				if (conditionType==3) {
					namePath = "GRID_PARALLEL_TYPE";
				}
				else
				{
					if (conditionType==4) {
						namePath = "GRID_PARALLEL_EVALUATION_TYPE";
					}
				}
			}
		}
		while(!founded && i < elements.length) 
		{
			elem = Browser.driver().findElement(By.xpath(elements[i][0]));
			if(elem.getText().equals(taskName))
			{
				founded = true;
			}
			else
			{
				i++;
			}
		}

		if(founded == true)
		{
			System.out.println("Xpath: "+elements[i][1]);
			Browser.waitForElement(By.xpath(elements[i][1]),5);
			WebElement el = Browser.driver().findElement(By.xpath(elements[i][1]));			
			el.click();
		}
		else
		{
			System.out.println("No se encontro la tarea "+taskName);
		}

		if(arrayTasks.length>0)
		{
			for(int l=0;l<arrayTasks.length-1;l++)
			{
				Browser.waitForElement(By.id("form["+namePath+"][addLink]"),5);
				btnAdd = Browser.driver().findElement(By.id("form["+namePath+"][addLink]"));
				btnAdd.click();
			}

			for(int j=0;j<arrayTasks.length;j++)
			{
				dropdownCount = j + 1;
				elem = Browser.driver().findElement(By.id("form["+namePath+"]["+dropdownCount+"][ROU_NEXT_TASK]"));
				droplist = new Select(elem);
				droplist.selectByVisibleText(arrayTasks[j][0]);

				if(conditionType!=3)
				{
					elem = Browser.driver().findElement(By.id("form["+namePath+"]["+dropdownCount+"][ROU_CONDITION]"));
					elem.sendKeys(arrayTasks[j][1]);
				}
			}

			btnSend = Browser.driver().findElement(By.id("form[SAVE]"));
			btnSend.click();
		}			

	}


	public boolean addEventMessage(String description, String status, String type, String startTask, String taskEnd, String taskDuration, 
			String taskDurationUnti, String exeTime, String exeTimeUnit, String subject, String[] mailto, String[] mailcc, String[] mailbcc) throws Exception{

		openMenuEvent();
		WebElement eventMessageElem = Browser.getElement("designer.webElement.eventMessage");
		if(eventMessageElem.getText().equals("Intermediate message"))
		{
			eventMessageElem.click();
		}
		fillBasicFieldsEvents(description, status, type, startTask, taskEnd, taskDuration, taskDurationUnti, exeTime, exeTimeUnit); 
		Browser.getElement("events.webElement.Continue").click();
		Browser.getElement("events.webElement.Subject").sendKeys(subject);
		addEmails("events.webElement.SendTo", mailto);
		addEmails("events.webElement.CopyCarbon", mailcc);
		addEmails("events.webElement.Blind", mailbcc);
		Browser.getElement("events.webElement.Save").click();
		return true;

	}

	public boolean addEventConditional(String description, String status, String type, String startTask, String taskEnd, String taskDuration, 
			String taskDurationUnti, String exeTime, String exeTimeUnit, String trigger, String condition) throws Exception{
		Browser.driver().switchTo().frame("frameMain");
		openMenuEvent();
		WebElement eventMessageElem = Browser.getElement("designer.webElement.eventConditional");
		if(eventMessageElem.getText().equals("Intermediate Conditional"))
		{
			eventMessageElem.click();
		}
		fillBasicFieldsEvents(description, status, type, startTask, taskEnd, taskDuration, taskDurationUnti, exeTime, exeTimeUnit); 
		//WebElement element = Browser.getElement("events.webElement.Trigger");
		//Select listTrigger = new Select(element);
		//listTrigger.selectByVisibleText(trigger);
		Browser.getElement("events.webElement.Condition").sendKeys(condition);
		Browser.getElement("events.webElement.Continue").click();
		Browser.getElement("events.webElement.Save").click();
		return true;

	}


	public boolean addEventTimer(String description, String status, String type, String startTask, String taskEnd, String taskDuration, 
			String taskDurationUnti, String exeTime, String exeTimeUnit, String trigger) throws Exception{
		//Browser.driver().switchTo().frame("frameMain");
		openMenuEvent();
		WebElement eventMessageElem = Browser.getElement("designer.webElement.eventTimer");
		
		if(eventMessageElem.getText().equals("Intermediate timer"))
		{
			eventMessageElem.click();
		}
		fillBasicFieldsEvents(description, status, type, startTask, taskEnd, taskDuration, taskDurationUnti, exeTime, exeTimeUnit); 
		//WebElement element = Browser.getElement("events.webElement.Trigger");
		//Select listTrigger = new Select(element);
		//listTrigger.selectByVisibleText(trigger);
		Browser.getElement("events.webElement.Continue").click();
		Browser.getElement("events.webElement.Save").click();
		return true;

	}

	public void fillBasicFieldsEvents(String description, String status, String type, String startTask, String taskEnd, String taskDuration, 
			String taskDurationUnti, String exeTime, String exeTimeUnit) throws Exception{

		WebElement elem = null;
		Select droplist = null;
		Browser.driver().switchTo().defaultContent();
		Browser.driver().switchTo().frame("frameMain");
		Browser.getElement("events.webElement.New").click();
		Browser.getElement("events.webElement.Description").sendKeys(description);
		elem = Browser.getElement("events.webElement.Status");
		droplist = new Select(elem);
		droplist.selectByVisibleText(status);
		elem = Browser.getElement("events.webElement.Type");
		droplist = new Select(elem);
		droplist.selectByVisibleText(type);
		if(type.equals("Multiple Tasks")){
			elem = Browser.getElement("events.webElement.TaskFrom");
			droplist = new Select(elem);
			droplist.selectByVisibleText(startTask);
			elem = Browser.getElement("events.webElement.TaskTo");
			droplist = new Select(elem);
			droplist.selectByVisibleText(taskEnd);
		}
		else{
			elem = Browser.getElement("events.webElement.TaskStart");
			droplist = new Select(elem);
			droplist.selectByVisibleText(startTask);	
		}

		Browser.getElement("events.webElement.EstDuration").sendKeys(taskDuration);
		elem = Browser.getElement("events.webElement.EstDurationUnit");
		droplist = new Select(elem);
		droplist.selectByVisibleText(taskDurationUnti);
		Browser.getElement("events.webElement.ExeTimeWhen").sendKeys(exeTime);
		elem = Browser.getElement("events.webElement.ExeTimeWhenOccurs");
		droplist = new Select(elem);
		droplist.selectByVisibleText(exeTimeUnit);
	}

	public void addEmails(String pathElement, String[] emailsList) throws Exception{

		for(int j=0;j<emailsList.length;j++)
		{
			Browser.getElement(pathElement).sendKeys(emailsList[j]);
			Browser.getElement(pathElement + "Add").click();
		}

	}

	public void openMenuEvent() throws Exception{

		Actions action = new Actions(Browser.driver());
		Thread.sleep(5000);
		WebElement gridPanel = Browser.getElement("designer.webElement.gridPanel");
		action.contextClick(gridPanel).perform();
		WebElement eventElem = Browser.getElement("designer.webElement.openMenuEvent");

		if(eventElem.getText().equals("Events"))
		{
			eventElem.click();
		}

	}


}