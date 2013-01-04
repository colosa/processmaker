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

public class TaskProperties{


	public void properties(TaskFieldData taskProperties)  throws Exception{

		Actions action = new Actions(Browser.driver());
		WebElement task = Pages.Designer().getTask(taskProperties.taskName);
		action.contextClick(task).perform();

		WebElement property = Browser.getElement("taskProperties.webElement.properties");

		if(property.getText().equals("Properties"))
		{
			property.click();
		}

		definition(taskProperties);
		assignmentRules(taskProperties);
		timingControl(taskProperties);
		permissions(taskProperties);
		caseLabels(taskProperties);
		notifications(taskProperties);
		save();
	}

	public boolean definition(TaskFieldData taskProperties)  throws Exception{

		WebElement def = Browser.getElement("taskProperties.webElement.definition");
		if(def.getText().equals("Definition"))
		{
			def.click();
		}
		else
		{
			throw new Exception("The element is not found");
		}


		WebElement title = Browser.getElement("taskProperties.webElement.taskTitle");
		WebElement description = Browser.getElement("taskProperties.webElement.taskDescription");
		WebElement casePriority = Browser.getElement("taskProperties.webElement.taskPriority");
		WebElement routingScreen = Browser.getElement("taskProperties.webElement.taskDerivation");
		WebElement starting = Browser.getElement("taskProperties.webElement.taskStart");

		title.sendKeys(taskProperties.title);
		description.sendKeys(taskProperties.description);
		casePriority.sendKeys(taskProperties.casePriority);
		Select droplist = new Select(routingScreen);
		droplist.selectByVisibleText(taskProperties.routingScreenTemplate);
		if(taskProperties.startingTask==true){
			starting.click();
		}
		return true;

	}

	public boolean assignmentRules(TaskFieldData taskProperties)  throws Exception{
		WebElement assign = Browser.getElement("taskProperties.webElement.assignmentRules");
		if(assign.getText().equals("Assignment rules"))
		{
			assign.click();
		}
		else
		{
			throw new Exception("The element is not found");
		}
		if(taskProperties.caseAssignedBy=="")
		{
			taskProperties.caseAssignedBy = "BALANCED";
		}
		WebElement assignment = Browser.driver().findElement(By.id("form[TAS_ASSIGN_TYPE]["+taskProperties.caseAssignedBy+"]"));
		assignment.click();

		return true;
	}

	public void timingControl(TaskFieldData taskProperties)  throws Exception{
		WebElement timing = Browser.getElement("taskProperties.webElement.timingControl");
		if(timing.getText().equals("Timing control"))
		{
			timing.click();
		}		
		WebElement allowUser = Browser.getElement("taskProperties.webElement.allowUser");
		WebElement duration = Browser.getElement("taskProperties.webElement.taskDuration");
		WebElement tUnit = Browser.getElement("taskProperties.webElement.timeUnit");
		WebElement countD = Browser.getElement("taskProperties.webElement.typeDay");
		WebElement clndr = Browser.getElement("taskProperties.webElement.calendar");
		if(taskProperties.allowUserDefined==true)
		{
			allowUser.click();
			duration.sendKeys(taskProperties.taskDuration);
			Select droplist = new Select(tUnit);
			droplist.selectByVisibleText(taskProperties.timeUnit);
			Select droplist2 = new Select(countD);
			droplist2.selectByVisibleText(taskProperties.countDays);
			Select droplist3 = new Select(clndr);
			droplist3.selectByVisibleText(taskProperties.calendar);

		}
	}

	public void permissions(TaskFieldData taskProperties)  throws Exception{
		WebElement perm = Browser.getElement("taskProperties.webElement.permisions");
		if(perm.getText().equals("Permissions"))
		{
			perm.click();
		}
		WebElement allowArb = Browser.getElement("taskProperties.webElement.allowArbitrary");

		if(taskProperties.allowArbitrary==true)
			allowArb.click();

	}

	public void caseLabels(TaskFieldData taskProperties)  throws Exception{
		WebElement caselbl = Browser.getElement("taskProperties.webElement.caseLabels");
		if(caselbl.getText().equals("Case Labels"))
		{
			caselbl.click();
		}
		WebElement cTitle = Browser.getElement("taskProperties.webElement.caseTitle");
		WebElement cDescription = Browser.getElement("taskProperties.webElement.caseDescription");
		cTitle.sendKeys(taskProperties.caseTitle);
		cDescription.sendKeys(taskProperties.caseDescription);
	}

	public void notifications(TaskFieldData taskProperties)  throws Exception{
		WebElement notif = Browser.getElement("taskProperties.webElement.notifications");
		if(notif.getText().equals("Notifications"))
		{
			notif.click();
		}
		WebElement afterRoutDer =Browser.getElement("taskProperties.webElement.afterRoute");
		WebElement sbjct = Browser.getElement("taskProperties.webElement.subject");
		WebElement content = Browser.getElement("taskProperties.webElement.content");
		WebElement msg = Browser.getElement("taskProperties.webElement.message");
		if(taskProperties.afterRouting==true){
			afterRoutDer.click();
			sbjct.sendKeys(taskProperties.subject);
			Select droplist = new Select(content);
			droplist.selectByVisibleText(taskProperties.contentType);
			msg.sendKeys(taskProperties.message);
		}

	}

	public void save()  throws Exception{
		WebElement btnSave = Browser.getElement("taskProperties.webElement.btnSave");
		btnSave.click();
		Thread.sleep(1000);
		WebElement btnAccept = Browser.getElement("taskProperties.webElement.btnAccept");
		btnAccept.click();
		
	}

}