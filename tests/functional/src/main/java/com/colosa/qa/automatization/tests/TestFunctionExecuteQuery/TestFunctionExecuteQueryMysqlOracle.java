package com.colosa.qa.automatization.tests.TestFunctionExecuteQuery;

import org.junit.Assert;
import org.junit.AfterClass;
import org.junit.Test;

import com.colosa.qa.automatization.pages.*;
import com.colosa.qa.automatization.common.*;
import org.openqa.selenium.WebElement;

import java.io.FileNotFoundException;
import java.io.IOException;

public class TestFunctionExecuteQueryMysqlOracle{

	protected static int caseNum;
	protected static String firstNameMysql;
	protected static String lastNameMysql;
	protected static String firstNameOracle;
	protected static String lastNameOracle;

	@Test
	public void executeProcess() throws FileNotFoundException, IOException, Exception{

		//Init case
		Pages.Login().gotoUrl();
		Pages.Login().loginUser("admin", "admin", "workflow");
		Pages.Main().goHome();	
		caseNum = Pages.Home().startCase("ExecuteQuery Mysql -Oracle (Task 1)");
		Pages.DynaformExecution().intoDynaform();
		Pages.DynaformExecution().setFieldValue("usern", "admin");
		Pages.DynaformExecution().setFieldValue("userNameOracle", "xxx");
		Pages.DynaformExecution().setFieldValue("send", "");
	    Pages.InputDocProcess().continuebtn();
		Pages.DynaformExecution().outDynaform();

		//Open task 2 .
		firstNameMysql = "FNMysql-" + Utils.getRandomString(5);
		lastNameMysql = "LNMysql-" + Utils.getRandomString(5);;
		firstNameOracle = "FNOracle-" + Utils.getRandomString(5);;
		lastNameOracle = "LNOracle-" + Utils.getRandomString(5);;
		Pages.InputDocProcess().switchToDefault();
		Pages.Home().gotoInbox();
		Assert.assertTrue("The case does not exist in Inbox", Pages.Home().existCase(caseNum));		
		Pages.Home().openCase(caseNum);	
		Pages.DynaformExecution().intoDynaform();
		Pages.DynaformExecution().setFieldValue("userFirstName", firstNameMysql);
		Pages.DynaformExecution().setFieldValue("userLastName", lastNameMysql);
		Pages.DynaformExecution().setFieldValue("firstNameOracle", firstNameOracle);
		Pages.DynaformExecution().setFieldValue("lastNameOracle", lastNameOracle);
		Pages.DynaformExecution().setFieldValue("send", "");
		//Form with the updates
		Assert.assertEquals(firstNameMysql, Value.getValue(FieldKeyType.ID, "form[firstNameUpdate]"));
		Assert.assertEquals(lastNameMysql, Value.getValue(FieldKeyType.ID, "form[lastNameUpdate]"));
		Assert.assertEquals(firstNameOracle, Value.getValue(FieldKeyType.ID, "form[firstNameOracleUpdate]"));
		Assert.assertEquals(lastNameOracle, Value.getValue(FieldKeyType.ID, "form[lastNameUpateOracle]"));
		Assert.assertEquals(firstNameMysql, Value.getValue(FieldKeyType.ID, "form[updatesAll][1][firstName]"));
		Assert.assertEquals(lastNameMysql, Value.getValue(FieldKeyType.ID, "form[updatesAll][1][lastName]"));
		Assert.assertEquals(firstNameOracle, Value.getValue(FieldKeyType.ID, "form[updatesAll][2][firstName]"));
		Assert.assertEquals(lastNameOracle, Value.getValue(FieldKeyType.ID, "form[updatesAll][2][lastName]"));
		Pages.DynaformExecution().setFieldValue("continue", "");
		Pages.InputDocProcess().openCaseFrame();
		Assert.assertTrue("The button Continue does not exit in this form", Pages.InputDocProcess().continuebtn());	
		//Open the last task 
		Pages.InputDocProcess().switchToDefault();
		Pages.Home().gotoInbox();
		Assert.assertTrue("The case does not exist in Inbox", Pages.Home().existCase(caseNum));		
		Pages.Home().openCase(caseNum);	
		Pages.InputDocProcess().openCaseFrame();
		Assert.assertTrue("The button Continue does not exit in this form", Pages.InputDocProcess().continuebtn());	
		Pages.Main().logout();
}

}