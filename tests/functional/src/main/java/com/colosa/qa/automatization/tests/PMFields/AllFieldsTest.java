package com.colosa.qa.automatization.tests.PMFields;

import org.junit.Assert;
import org.junit.AfterClass;
import org.junit.Test;

import com.colosa.qa.automatization.pages.*;
import com.colosa.qa.automatization.common.*;
import org.openqa.selenium.WebElement;

import java.io.FileNotFoundException;
import java.io.IOException;

public class AllFieldsTest{

	protected static int caseNum;
	protected static String country = "Bolivia";
	protected static String state = "Chuquisaca";
	protected static String location = "Sucre";
	protected static String state1 = "La Paz";
	protected static String location1 = "Apolo";

	protected static String country2 = "Argentina";
	protected static String state2 = "Jujuy";
	protected static String location2 = "San Pedro";
	protected static String state22 = "Chubut";
	protected static String location22 = "Trelew";

	protected static String userName = "amy";
	protected static String completeName = "Amy Connelly";
	protected static String userDepartment = "Administrative Division";

	protected static String userName1 = "admin";
	protected static String completeName1 = "Administrator  ";
	protected static String userDepartment1 = "";

	@Test
	public void allFieldsTest() throws FileNotFoundException, IOException, Exception{

		Pages.Login().gotoUrl();
		Pages.Login().loginUser("admin", "admin", "workflow");
		Pages.Main().goHome();	
		caseNum = Pages.Home().startCase("FormularioTodosCampos (Task 1)");
		
		Pages.DynaformExecution().intoDynaform();

		//test textfield
		Pages.DynaformExecution().setFieldValue("textField", "Herbert");
		String fieldValue = Pages.DynaformExecution().getFieldValue("textField");
		Assert.assertEquals(fieldValue, "Herbert");

		//test currencyfield
		Pages.DynaformExecution().setFieldValue("currencyField", "43.14");
		fieldValue = Pages.DynaformExecution().getFieldValue("currencyField");
		Assert.assertEquals(fieldValue, "43.14");

		//test percentageField
		Pages.DynaformExecution().setFieldValue("percentageField", "43");
		fieldValue = Pages.DynaformExecution().getFieldValue("percentageField");
		Assert.assertEquals(fieldValue, "43 %");

		//test passwordField
		Pages.DynaformExecution().setFieldValue("passwordField", "herbert");
		fieldValue = Pages.DynaformExecution().getFieldValue("passwordField");
		Assert.assertEquals(fieldValue, "herbert");
		
		//test suggestField
		Pages.DynaformExecution().setFieldValue("suggestField", "Bolivia");
		fieldValue = Pages.DynaformExecution().getFieldValue("suggestField");
		Assert.assertEquals(fieldValue, "BO");
		fieldValue = Pages.DynaformExecution().getFieldText("suggestField");
		Assert.assertEquals(fieldValue, "Bolivia");

		//test textAreaField
		Pages.DynaformExecution().setFieldValue("textAreaField", "Bolivia linda \\n");
		fieldValue = Pages.DynaformExecution().getFieldValue("textAreaField");
		Assert.assertEquals(fieldValue, "Bolivia linda \\n");

		//test buttonField
		Pages.DynaformExecution().setFieldValue("buttonField", "");
		
		//test dropdownField
		Pages.DynaformExecution().setFieldValue("dropdownField", "dos");
		fieldValue = Pages.DynaformExecution().getFieldValue("dropdownField");
		Assert.assertEquals(fieldValue, "2");		

		//test yesNoField
		Pages.DynaformExecution().setFieldValue("yesNoField", "Yes");
		fieldValue = Pages.DynaformExecution().getFieldValue("yesNoField");
		Assert.assertEquals(fieldValue, "1");

		//test listBoxField
		Pages.DynaformExecution().setFieldValue("listBoxField", "tres");
		fieldValue = Pages.DynaformExecution().getFieldValue("listBoxField");
		Assert.assertEquals(fieldValue, "3");	

		//test checkBoxField
		Pages.DynaformExecution().setFieldValue("checkBoxField", "");
		fieldValue = Pages.DynaformExecution().getFieldValue("checkBoxField");
		Assert.assertEquals(fieldValue, "true");

		//test datefield
		Pages.DynaformExecution().setFieldValue("dateField", "2013-01-18");
		String dateValue = Pages.DynaformExecution().getFieldValue("dateField");
		Assert.assertEquals(dateValue, "2013-01-18");

		//test hiddenField
		Pages.DynaformExecution().setFieldValue("hiddenField", "Herbert");
		fieldValue = Pages.DynaformExecution().getFieldValue("hiddenField");
		Assert.assertEquals(fieldValue, "hiddenField");

		//test linkField
		Pages.DynaformExecution().setFieldValue("linkField", "");

		//test fileField
		//Pages.DynaformExecution().setFieldValue("fileField", "c:\\herbert\\Saal");


		Pages.DynaformExecution().gridAddNewRow("gridField");

		//form[gridField][1][gridTextField]
		Pages.DynaformExecution().setGridFieldValue("gridField", 1, "gridTextField", "hola");

		String gridValue =  Pages.DynaformExecution().getGridFieldValue("gridField", 1, "gridTextField");

		Assert.assertEquals(gridValue, "hola");

		//form[gridField][2][gridCurrencyField]
		Pages.DynaformExecution().setGridFieldValue("gridField", 2, "gridCurrencyField", "43.14");

		gridValue =  Pages.DynaformExecution().getGridFieldValue("gridField", 2, "gridCurrencyField");

		Assert.assertEquals(gridValue, "43.14");

		//form[gridField][1][gridPercentageField]
		Pages.DynaformExecution().setGridFieldValue("gridField", 2, "gridPercentageField", "43.14");

		gridValue =  Pages.DynaformExecution().getGridFieldValue("gridField", 2, "gridPercentageField");

		Assert.assertEquals(gridValue, "43.14 %");

		//form[gridField][1][gridTextAreaField]
		Pages.DynaformExecution().setGridFieldValue("gridField", 2, "gridTextAreaField", "Hola Mundo!!!!");

		gridValue =  Pages.DynaformExecution().getGridFieldValue("gridField", 2, "gridTextAreaField");

		Assert.assertEquals(gridValue, "Hola Mundo!!!!");


		//form[gridField][1][gridDropDownField]
		Pages.DynaformExecution().setGridFieldValue("gridField", 2, "gridDropDownField", "dos");

		gridValue =  Pages.DynaformExecution().getGridFieldValue("gridField", 2, "gridDropDownField");

		Assert.assertEquals(gridValue, "2");

		//form[gridField][1][gridYesNoField]
		Pages.DynaformExecution().setGridFieldValue("gridField", 1, "gridYesNoField", "Yes");

		gridValue =  Pages.DynaformExecution().getGridFieldValue("gridField", 1, "gridYesNoField");

		Assert.assertEquals(gridValue, "1");


		//form[gridField][1][gridCheckBoxField]
		Pages.DynaformExecution().setGridFieldValue("gridField", 1, "gridCheckBoxField", "Yes");
		gridValue =  Pages.DynaformExecution().getGridFieldValue("gridField", 1, "gridCheckBoxField");
		Assert.assertEquals(gridValue, "true");
		gridValue =  Pages.DynaformExecution().getGridFieldValue("gridField", 2, "gridCheckBoxField");
		Assert.assertEquals(gridValue, "false");

		//form[gridField][1][gridDateField]
		Pages.DynaformExecution().setGridFieldValue("gridField", 2, "gridDateField", "2013-04-05");
		gridValue =  Pages.DynaformExecution().getGridFieldValue("gridField", 2, "gridDateField");
		Assert.assertEquals(gridValue, "2013-04-05");


		Pages.DynaformExecution().outDynaform();

		Pages.Main().logout();
	}


}