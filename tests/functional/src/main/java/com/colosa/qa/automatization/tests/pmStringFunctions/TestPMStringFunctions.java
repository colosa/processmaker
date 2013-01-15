package com.colosa.qa.automatization.tests.pmStringFunctions;

import org.junit.Assert;
import org.junit.AfterClass;
import org.junit.Test;

import com.colosa.qa.automatization.pages.*;
import com.colosa.qa.automatization.common.*;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.By;

import java.io.FileNotFoundException;
import java.io.IOException;

public class TestPMStringFunctions{

	protected static String str1 = "lkjlñasflsfjalsfsadlñfasñfjsañfjaslñfjsadñfjsañfsajñfsfasdñassañdfasñfsadfjsdlfjl";
	protected static String str2 = "JLDSAFJDSÑFJSADLFASÑFJSALDFSAÑFJLKSAFJSALÑFSJAÑFASJFÑSAJDFSAÑLJFSLÑAFJASHFKHSLDFIUWETYRIHKJ";
	protected static String str3 = "jkjHGKJGKjkjljñlGKJHKjkjlHJKHKKHKhkhk HKJHYkhkjhjhIYkhjy";

	@Test
	public void testCase() throws FileNotFoundException, IOException, Exception{

		Pages.Login().gotoUrl();
		Pages.Login().loginUser("admin", "admin", "workflow");
		Pages.Main().goHome();	
		Pages.Home().startCase("PM String Functions (Task 1)");
		Pages.DynaformExecution().intoDynaform();
		Pages.DynaformExecution().setFieldValue("upperC", str1);
		Pages.DynaformExecution().setFieldValue("lowC", str2);
		Pages.DynaformExecution().setFieldValue("capital", str3);
		Pages.DynaformExecution().setFieldValue("Send", "");


	}
}