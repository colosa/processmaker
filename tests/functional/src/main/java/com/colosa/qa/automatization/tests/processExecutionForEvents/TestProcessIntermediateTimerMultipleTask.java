package com.colosa.qa.automatization.tests.processExecutionForEvents;

import org.junit.Assert;
import org.junit.AfterClass;
import org.junit.Test;

import com.colosa.qa.automatization.pages.*;
import com.colosa.qa.automatization.common.*;
import org.openqa.selenium.WebElement;

import java.io.FileNotFoundException;
import java.io.IOException;

public class TestProcessIntermediateTimerMultipleTask{

	protected static int caseNum;

	@Test
	public void initProcess()throws FileNotFoundException, IOException, Exception{

		Pages.Login().gotoUrl();
		Pages.Login().loginUser("Brianna", "sample", "wsqa");
		Pages.Main().goHome();	
		caseNum = Pages.Home().startCase("Event Process - Intermediate Timer_Multiple Task (Task 1)");
		FormFieldData[] arrayData = new FormFieldData[4];
		arrayData[0] = new FormFieldData();
		arrayData[1] = new FormFieldData();
		arrayData[2] = new FormFieldData();
		arrayData[3] = new FormFieldData();
		
		arrayData[0].fieldPath = "form[nombre]";
		arrayData[0].fieldFindType = FieldKeyType.ID;
		arrayData[0].fieldType = FieldType.TEXTBOX;
		arrayData[0].fieldValue = "Ademar";
		arrayData[1].fieldPath = "form[apellido]";
		arrayData[1].fieldFindType = FieldKeyType.ID;
		arrayData[1].fieldType = FieldType.TEXTBOX;
		arrayData[1].fieldValue = "Hurtado";
		arrayData[2].fieldPath = "form[elegir][valor3]";
		arrayData[2].fieldFindType = FieldKeyType.ID;
		arrayData[2].fieldType = FieldType.CHECK;
		arrayData[2].fieldValue = "";
		arrayData[3].fieldPath = "form[guardar]";
		arrayData[3].fieldFindType = FieldKeyType.ID;
		arrayData[3].fieldType = FieldType.BUTTON;
		arrayData[3].fieldValue = "";
		Pages.InputDocProcess().openCaseFrame();
		Assert.assertTrue(FormFiller.formFillElements(arrayData));
		Assert.assertTrue(Pages.InputDocProcess().continuebtn());
		Pages.Main().logout();

	}

	@Test
	public void openTasks() throws FileNotFoundException, IOException, Exception{

		Pages.Login().gotoUrl();
		Pages.Login().loginUser("admin", "admin", "");
		String eventStatus= "";

		//Check if event is Closed
		Pages.Main().goHome();
		Pages.Main().goAdmin();		
		Pages.Admin().goToLogs();
		Thread.sleep(5000);	
		eventStatus = Pages.Admin().eventStatus(caseNum);
		Assert.assertEquals("CLOSE", eventStatus);
		Pages.Main().logout();

		//open task 2
		Pages.Login().gotoUrl();
		Pages.Login().loginUser("admin", "admin", "");
		Pages.Main().goHome();
		Pages.Home().openCase(caseNum);
		Pages.InputDocProcess().openCaseFrame();
		Assert.assertTrue(Pages.InputDocProcess().continuebtn());

		//Open task 3
		//Pages.Main().goHome();
		Pages.Home().openCase(caseNum);
		Pages.InputDocProcess().openCaseFrame();
		Assert.assertTrue(Pages.InputDocProcess().continuebtn());

		//Open task 4
		//Pages.Main().goHome();
		Pages.Home().openCase(caseNum);
		Pages.InputDocProcess().openCaseFrame();
		Assert.assertTrue(Pages.InputDocProcess().continuebtn());
		Pages.Main().logout();
	}

}