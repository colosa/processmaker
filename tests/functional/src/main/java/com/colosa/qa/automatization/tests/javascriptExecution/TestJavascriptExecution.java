package com.colosa.qa.automatization.tests.javascriptExecution;

import org.junit.Assert;
import org.junit.AfterClass;
import org.junit.Test;

import com.colosa.qa.automatization.pages.*;
import com.colosa.qa.automatization.common.*;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.By;

import java.io.FileNotFoundException;
import java.io.IOException;

public class TestJavascriptExecution{

	protected static String val1 = "142424234142342412412442414242341414242414";
	protected static String val2 = "~¬·~½¬@·~|·~·";

	@Test
	public void testCase() throws FileNotFoundException, IOException, Exception{

		Pages.Login().gotoUrl();
		Pages.Login().loginUser("admin", "admin", "workflow");
		Pages.Main().goHome();	
		Pages.Home().startCase("Proceso con java script (Task 1)");
		Pages.DynaformExecution().intoDynaform();
		Pages.DynaformExecution().setFieldValue("num1", val1);
		Pages.DynaformExecution().setFieldValue("num2", val2);
		Pages.DynaformExecution().setFieldValue("aceptar", "");


	}

}