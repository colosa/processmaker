package com.colosa.qa.automatization.tests.gridFunctions;

import org.junit.Assert;
import org.junit.AfterClass;
import org.junit.Test;

import com.colosa.qa.automatization.pages.*;
import com.colosa.qa.automatization.common.*;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.By;

import java.io.FileNotFoundException;
import java.io.IOException;

public class TestGridFunctions{

	protected static int caseNum;
	public static int addRow = 3;
	public static String val1 = "Prueba";
	public static double[] prec = {345.32, 654.44, 543.76};
	public static double[] perc = {54.8, 46.9, 33.1};
	public static double total = 1543.52;
	public static double prom = 44.93;

	@Test
	public void testCase() throws FileNotFoundException, IOException, Exception{

		Pages.Login().gotoUrl();
		Pages.Login().loginUser("admin", "admin", "workflow");
		Pages.Main().goHome();	
		caseNum = Pages.Home().startCase("Grillas Funciones (Task 1)");

		FormFieldData[][] gridData = new FormFieldData[addRow][3];

		for(int rows = 0; rows<gridData.length;rows++)
		{
			gridData[rows][0] = new FormFieldData();
			gridData[rows][1] = new FormFieldData();
			gridData[rows][2] = new FormFieldData();
		}
			
		int count = 0;

		for(int rows = 0; rows<gridData.length;rows++)
		{
			count = rows + 1;
			gridData[rows][0].fieldPath = "form[grid1]["+count+"][Columna1]";
			gridData[rows][0].fieldFindType = FieldKeyType.ID;
			gridData[rows][0].fieldType = FieldType.TEXTBOX;
			gridData[rows][0].fieldValue = val1;
			gridData[rows][1].fieldPath = "form[grid1]["+count+"][Precio1]";
			gridData[rows][1].fieldFindType = FieldKeyType.ID;
			gridData[rows][1].fieldType = FieldType.TEXTBOX;
			gridData[rows][1].fieldValue = Double.toString(prec[rows]);
			gridData[rows][2].fieldPath = "form[grid1]["+count+"][Porcentaje1]";
			gridData[rows][2].fieldFindType = FieldKeyType.ID;
			gridData[rows][2].fieldType = FieldType.TEXTBOX;
			gridData[rows][2].fieldValue = Double.toString(perc[rows]);
			
		}
		
		FormFieldData[] arrayData = new FormFieldData[1];
		arrayData[0] = new FormFieldData();
		
		arrayData[0].fieldPath = "form[Send]";
		arrayData[0].fieldFindType = FieldKeyType.ID;
		arrayData[0].fieldType = FieldType.BUTTON;
		arrayData[0].fieldValue = "";

		Pages.InputDocProcess().openCaseFrame();
		WebElement btnLink=null;
		btnLink = Browser.driver().findElement(By.id("form[grid1][addLink]"));
		for(int rows = 1;rows<addRow;rows++)
		{
			btnLink.click();
		}

		Assert.assertTrue(GridFiller.gridFillElements(gridData));
		Assert.assertTrue(FormFiller.formFillElements(arrayData));
		Assert.assertTrue(Pages.InputDocProcess().continuebtn());
		Pages.Main().logout();
	
	}

	@Test
	public void continueTestCase() throws FileNotFoundException, IOException, Exception{

		Pages.Login().gotoUrl();
		Pages.Login().loginUser("iver", "sample", "");
		Pages.Main().goHome();	
		Pages.Home().openCase(caseNum);

		FormFieldData[][] gridData2 = new FormFieldData[addRow][3];

		for(int rows2 = 0; rows2<gridData2.length;rows2++)
		{
			gridData2[rows2][0] = new FormFieldData();
			gridData2[rows2][1] = new FormFieldData();
			gridData2[rows2][2] = new FormFieldData();
		}
			
		int count2 = 0;

		for(int rows2 = 0; rows2<gridData2.length;rows2++)
		{
			count2 = rows2 + 1;
			gridData2[rows2][0].fieldPath = "form[grid1]["+count2+"][Columna1]";
			gridData2[rows2][0].fieldFindType = FieldKeyType.ID;
			gridData2[rows2][1].fieldPath = "form[grid1]["+count2+"][Precio1]";
			gridData2[rows2][1].fieldFindType = FieldKeyType.ID;
			gridData2[rows2][2].fieldPath = "form[grid1]["+count2+"][Porcentaje1]";
			gridData2[rows2][2].fieldFindType = FieldKeyType.ID;
					
		}


		FormFieldData[] arrayData2 = new FormFieldData[1];
		arrayData2[0] = new FormFieldData();
		
		arrayData2[0].fieldPath = "form[Send]";
		arrayData2[0].fieldFindType = FieldKeyType.ID;
		arrayData2[0].fieldType = FieldType.BUTTON;
		arrayData2[0].fieldValue = "";
		String porcen;

		FormFieldData[] arrayData3 = new FormFieldData[2];
		arrayData3[0] = new FormFieldData();
		arrayData3[1] = new FormFieldData();
		
		arrayData3[0].fieldPath = "//*[@id=\"form[SYS_GRID_AGGREGATE_grid1_Precio1]\"]";
		arrayData3[0].fieldFindType = FieldKeyType.XPATH;
		arrayData3[1].fieldPath = "//*[@id=\"form[SYS_GRID_AGGREGATE_grid1_Porcentaje1]\"]";
		arrayData3[1].fieldFindType = FieldKeyType.XPATH;

		Pages.InputDocProcess().openCaseFrame();
		for(int values = 0; values<gridData2.length;values++)
		{
			porcen= Double.toString(perc[values]);
			porcen= porcen + " %";
			Assert.assertEquals(val1, Value.getValue(gridData2[values][0].fieldFindType, gridData2[values][0].fieldPath));
			Assert.assertEquals(Double.toString(prec[values]), Value.getValue(gridData2[values][1].fieldFindType, gridData2[values][1].fieldPath));
			Assert.assertEquals(porcen, Value.getValue(gridData2[values][2].fieldFindType, gridData2[values][2].fieldPath));
		}
		Assert.assertEquals(Double.toString(total), Value.getValue(arrayData3[0].fieldFindType, arrayData3[0].fieldPath));
		Assert.assertEquals(Double.toString(prom), Value.getValue(arrayData3[1].fieldFindType, arrayData3[1].fieldPath));
		Assert.assertTrue(FormFiller.formFillElements(arrayData2));
		Assert.assertTrue(Pages.InputDocProcess().continuebtn());
		Pages.Main().logout();

	}

}