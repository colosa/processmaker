package com.colosa.qa.automatization.tests.subprocessesTest;

import org.junit.Assert;
import org.junit.AfterClass;
import org.junit.Test;

import com.colosa.qa.automatization.pages.*;
import com.colosa.qa.automatization.common.*;
import org.openqa.selenium.WebElement;

import java.io.FileNotFoundException;
import java.io.IOException;

public class SubprocessesTest{
	protected static int caseNum;
	protected static String name = "Ernesto";
	protected static String lastName = "Vega";
	protected static String salary = "23,564.00";
	protected static String desc = "Prueba.....";

	@Test
	public void runProcess() throws FileNotFoundException, IOException, Exception{

		Pages.Login().gotoUrl();
		Pages.Login().loginUser("admin", "admin", "workflow");
		Pages.Main().goHome();	
		caseNum = Pages.Home().startCase("Subprocess Test 1 (Task 1)");

		FormFieldData[] arrayData = new FormFieldData[4];
		arrayData[0] = new FormFieldData();
		arrayData[1] = new FormFieldData();
		arrayData[2] = new FormFieldData();
		arrayData[3] = new FormFieldData();

		arrayData[0].fieldPath = "form[Nombre]";
		arrayData[0].fieldFindType = FieldKeyType.ID;
		arrayData[0].fieldType = FieldType.TEXTBOX;
		arrayData[0].fieldValue = name;
		arrayData[1].fieldPath = "form[Apellido]";
		arrayData[1].fieldFindType = FieldKeyType.ID;
		arrayData[1].fieldType = FieldType.TEXTBOX;
		arrayData[1].fieldValue = lastName;
		arrayData[2].fieldPath = "form[Salario]";
		arrayData[2].fieldFindType = FieldKeyType.ID;
		arrayData[2].fieldType = FieldType.TEXTBOX;
		arrayData[2].fieldValue = salary;
		arrayData[3].fieldPath = "form[Enviar]";
		arrayData[3].fieldFindType = FieldKeyType.ID;
		arrayData[3].fieldType = FieldType.BUTTON;
		arrayData[3].fieldValue = "";

		caseNum++;
		Pages.InputDocProcess().openCaseFrame();
		Assert.assertTrue(FormFiller.formFillElements(arrayData));
		Assert.assertTrue(Pages.InputDocProcess().continuebtn());
		Pages.Main().logout();
	/*}

	@Test
	public void openCaseNum() throws FileNotFoundException, IOException, Exception{*/

		Pages.Login().gotoUrl();
		Pages.Login().loginUser("iver", "sample", "");
		Pages.Main().goHome();
		Pages.Home().openCase(caseNum);

		FormFieldData[] arrayDataEmp = new FormFieldData[2];
		arrayDataEmp[0] = new FormFieldData();
		arrayDataEmp[1] = new FormFieldData();

		arrayDataEmp[0].fieldPath = "form[lastName]";
		arrayDataEmp[0].fieldFindType = FieldKeyType.ID;
		arrayDataEmp[1].fieldPath = "form[salary]";
		arrayDataEmp[1].fieldFindType = FieldKeyType.ID;
		
		FormFieldData[] arrayData2 = new FormFieldData[2];
		arrayData2[0] = new FormFieldData();
		arrayData2[1] = new FormFieldData();

		arrayData2[0].fieldPath = "form[description]";
		arrayData2[0].fieldFindType = FieldKeyType.ID;
		arrayData2[0].fieldType = FieldType.TEXTAREA;
		arrayData2[0].fieldValue = desc;
		arrayData2[1].fieldPath = "form[Send]";
		arrayData2[1].fieldFindType = FieldKeyType.ID;
		arrayData2[1].fieldType = FieldType.BUTTON;
		arrayData2[1].fieldValue = "";

		caseNum++;
		Pages.InputDocProcess().openCaseFrame();
		Assert.assertEquals(lastName, Value.getValue(arrayDataEmp[0].fieldFindType, arrayDataEmp[0].fieldPath));
		Assert.assertEquals(salary, Value.getValue(arrayDataEmp[1].fieldFindType, arrayDataEmp[1].fieldPath));
		Assert.assertTrue(FormFiller.formFillElements(arrayData2));
		Assert.assertTrue(Pages.InputDocProcess().continuebtn());
		Pages.Main().logout();
	/*}

	@Test
	public void endCase() throws FileNotFoundException, IOException, Exception{*/
		Pages.Login().gotoUrl();
		Pages.Login().loginUser("hector", "sample", "");
		Pages.Main().goHome();
		Pages.Home().openCase(caseNum);

		FormFieldData[] arrayDataEmp2 = new FormFieldData[1];
		arrayDataEmp2[0] = new FormFieldData();
		
		arrayDataEmp2[0].fieldPath = "form[descripcion]";
		arrayDataEmp2[0].fieldFindType = FieldKeyType.ID;

		FormFieldData[] arrayData3 = new FormFieldData[1];
		arrayData3[0] = new FormFieldData();

		arrayData3[0].fieldPath = "form[Send]";
		arrayData3[0].fieldFindType = FieldKeyType.ID;
		arrayData3[0].fieldType = FieldType.BUTTON;
		arrayData3[0].fieldValue = "";
		

		Pages.InputDocProcess().openCaseFrame();
		Assert.assertEquals(desc, Value.getValue(arrayDataEmp2[0].fieldFindType, arrayDataEmp2[0].fieldPath));
		Assert.assertTrue(FormFiller.formFillElements(arrayData3));
		Assert.assertTrue(Pages.InputDocProcess().continuebtn());
		Pages.Main().logout();
	}
}