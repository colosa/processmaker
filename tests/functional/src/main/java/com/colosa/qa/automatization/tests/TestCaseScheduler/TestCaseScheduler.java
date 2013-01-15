package com.colosa.qa.automatization.tests.TestCaseScheduler;

import org.junit.Assert;
import org.junit.AfterClass;
import org.junit.Test;

import com.colosa.qa.automatization.pages.*;
import com.colosa.qa.automatization.common.*;
import org.openqa.selenium.WebElement;

import java.io.FileNotFoundException;
import java.io.IOException;

public class TestCaseScheduler{

	protected static int caseNum;
	protected static String caseStatus;

	@Test
	public void executeProcess() throws FileNotFoundException, IOException, Exception{
		//Init case
		Pages.Login().gotoUrl();
		Pages.Login().loginUser("admin", "admin", "workflow");
		Pages.Main().goHome();	
		Pages.Main().goAdmin();		
		Pages.Admin().goToLogs();
		Pages.Admin().showCaseScheduler();
		caseStatus = Pages.Admin().lastCreateCaseStatus();
		String[] toArray = caseStatus.split(" ");
		int lastCaseNum = Integer.parseInt(toArray[1]);
		int currentCaseNum = lastCaseNum + 1;
		Pages.DynaformExecution().sleep(20000);
		Pages.CronExecute().execute();
		System.out.println("run cron.php");
		Pages.DynaformExecution().sleep(20000);
		Pages.Login().gotoUrl();
		Pages.Login().loginUser("admin", "admin", "");
		Pages.Main().goHome();
		Pages.Main().goAdmin();		
		Pages.Admin().goToLogs();
		Pages.Admin().showCaseScheduler();
		caseStatus = Pages.Admin().lastCreateCaseStatus();
		Assert.assertEquals("Case " + Integer.toString(currentCaseNum) + " Started successfully", caseStatus);
		Pages.DynaformExecution().sleep(20000);
		Pages.InputDocProcess().switchToDefault();
		Pages.Main().logout();
	}

}