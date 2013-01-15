package com.colosa.qa.automatization.tests.TestCaseScheduler;

import org.junit.Assert;
import org.junit.AfterClass;
import org.junit.Test;

import com.colosa.qa.automatization.pages.*;
import com.colosa.qa.automatization.common.*;
import org.openqa.selenium.WebElement;

import java.io.FileNotFoundException;
import java.io.IOException;

public class TestCaseSchedulerCron{

	protected static int caseNum;
	protected static String caseStatus;

	@Test
	public void executeCron() throws FileNotFoundException, IOException, Exception{
		//Execute cron
		Pages.Login().gotoUrl();
		Pages.Login().loginUser("admin", "admin", "workflow");
		Pages.Main().goHome();	
		Pages.Main().goAdmin();		
		Pages.Admin().goToLogs();
		Pages.Admin().showCaseScheduler();
		Pages.CronExecute().execute();
		Pages.DynaformExecution().sleep(20000);

	}


}