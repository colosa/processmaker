package com.colosa.qa.automatization.tests.testFieldModeView;

import org.junit.Assert;
import org.junit.AfterClass;
import org.junit.Test;

import com.colosa.qa.automatization.pages.*;
import com.colosa.qa.automatization.common.*;
import org.openqa.selenium.WebElement;

import java.io.FileNotFoundException;
import java.io.IOException;

public class TestModeView{

	protected static int caseNum;
	protected static String textVal = "Ernesto";
	protected static String prec = "123,132,132,123.45";
	protected static String perc = "213.13 %";
	protected static String textArea = "Prueba";
	protected static String drpdwn = "Valor4";
	protected static String lstBx = "Valor3";

	@Test
	public void testCase() throws FileNotFoundException, IOException, Exception{

		Pages.Login().gotoUrl();
		Pages.Login().loginUser("admin", "admin", "workflow");
		Pages.Main().goHome();	
		caseNum = Pages.Home().startCase("Testeo Modo Vista (Task 1)");

	
		Pages.DynaformExecution().intoDynaform();
		Pages.DynaformExecution().setFieldValue("Campo1", textVal);
		Pages.DynaformExecution().setFieldValue("Precio1", prec);
		Pages.DynaformExecution().setFieldValue("Porcentaje1", perc);
		//Pages.DynaformExecution().setFieldValue("Text1", textArea);
		Pages.DynaformExecution().setFieldValue("Combo1", drpdwn);
		Pages.DynaformExecution().setFieldValue("Lista1", lstBx);
		Pages.DynaformExecution().setFieldValue("Enviar", "", FieldType.BUTTON);

		Assert.assertTrue(Pages.InputDocProcess().continuebtn());
		Pages.Main().logout();


	/*}

	@Test
	public void continueTestCase() throws FileNotFoundException, IOException, Exception{*/

		Pages.Login().gotoUrl();
		Pages.Login().loginUser("iver", "sample", "");
		Pages.Main().goHome();	
		Pages.Home().openCase(caseNum);

		Pages.DynaformExecution().intoDynaform();
		
		Assert.assertEquals(textVal, Pages.DynaformExecution().getFieldValue("Campo1"));
		Assert.assertEquals(prec, Pages.DynaformExecution().getFieldValue("Precio1"));
		Assert.assertEquals(perc, Pages.DynaformExecution().getFieldValue("Porcentaje1"));
		//Assert.assertEquals(textArea, Pages.DynaformExecution().getFieldValue("Text1"));
		Assert.assertEquals(drpdwn, Pages.DynaformExecution().getDropdownFieldText("Combo1"));
		Assert.assertEquals(lstBx, Pages.DynaformExecution().getFieldValue("List1"));
		Pages.DynaformExecution().setFieldValue("Enviar", "", FieldType.BUTTON);

		Assert.assertTrue(Pages.InputDocProcess().continuebtn());
		Pages.Main().logout();


	}


}