package com.colosa.qa.automatization.tests.gridFunctionsBetweenColumns;

import org.junit.Assert;
import org.junit.AfterClass;
import org.junit.Test;

import com.colosa.qa.automatization.pages.*;
import com.colosa.qa.automatization.common.*;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.By;

import java.io.FileNotFoundException;
import java.io.IOException;

public class TestGridFunctionsBetweenColumns{

	protected static int caseNum;
	public static int addRow = 3;
	public static String val1 = "Prueba";
	public static int[] cant = {45, 66, 78};
	public static double[] prec1 = {345.33, 654.49, 543.76};
	public static double[] perc1 = {354.84, 646.94, 833.13};
	public static double[] perc2 = {425.38, 734.27, 978.59};
	public static double[] total = {15539.85, 43196.34, 42413.28};
	public static double[] prom = {390.11, 690.61, 905.86};
	public static double sum = 101149.47;


	@Test
	public void testCase() throws FileNotFoundException, IOException, Exception{

		Pages.Login().gotoUrl();
		Pages.Login().loginUser("admin", "admin", "workflow");
		Pages.Main().goHome();	
		caseNum = Pages.Home().startCase("Grillas Funciones Entre Columnas (Task 1)");

		FormFieldData[][] gridData = new FormFieldData[addRow][4];

		for(int rows = 0; rows<gridData.length;rows++)
		{
			gridData[rows][0] = new FormFieldData();
			gridData[rows][1] = new FormFieldData();
			gridData[rows][2] = new FormFieldData();
			gridData[rows][3] = new FormFieldData();
		}
			
		int count = 0;

		for(int rows = 0; rows<gridData.length;rows++)
		{
			count = rows + 1;
			gridData[rows][0].fieldPath = "form[grd1]["+count+"][Cantidad]";
			gridData[rows][0].fieldFindType = FieldKeyType.ID;
			gridData[rows][0].fieldType = FieldType.TEXTBOX;
			gridData[rows][0].fieldValue = Integer.toString(cant[rows]);
			gridData[rows][1].fieldPath = "form[grd1]["+count+"][Precio]";
			gridData[rows][1].fieldFindType = FieldKeyType.ID;
			gridData[rows][1].fieldType = FieldType.TEXTBOX;
			gridData[rows][1].fieldValue = Double.toString(prec1[rows]);
			gridData[rows][2].fieldPath = "form[grd1]["+count+"][Valor1]";
			gridData[rows][2].fieldFindType = FieldKeyType.ID;
			gridData[rows][2].fieldType = FieldType.TEXTBOX;
			gridData[rows][2].fieldValue = Double.toString(perc1[rows]);
			gridData[rows][3].fieldPath = "form[grd1]["+count+"][Valor2]";
			gridData[rows][3].fieldFindType = FieldKeyType.ID;
			gridData[rows][3].fieldType = FieldType.TEXTBOX;
			gridData[rows][3].fieldValue = Double.toString(perc2[rows]);
			
			
		}
		
		FormFieldData[] arrayData = new FormFieldData[1];
		arrayData[0] = new FormFieldData();
		
		arrayData[0].fieldPath = "form[Send]";
		arrayData[0].fieldFindType = FieldKeyType.ID;
		arrayData[0].fieldType = FieldType.BUTTON;
		arrayData[0].fieldValue = "";

		Pages.InputDocProcess().openCaseFrame();
		WebElement btnLink=null;
		btnLink = Browser.driver().findElement(By.id("form[grd1][addLink]"));
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

		FormFieldData[][] gridData2 = new FormFieldData[addRow][6];

		for(int rows2 = 0; rows2<gridData2.length;rows2++)
		{
			gridData2[rows2][0] = new FormFieldData();
			gridData2[rows2][1] = new FormFieldData();
			gridData2[rows2][2] = new FormFieldData();
			gridData2[rows2][3] = new FormFieldData();
			gridData2[rows2][4] = new FormFieldData();
			gridData2[rows2][5] = new FormFieldData();
		}
			
		int count2 = 0;

		for(int rows2 = 0; rows2<gridData2.length;rows2++)
		{
			count2 = rows2 + 1;
			gridData2[rows2][0].fieldPath = "form[grd1]["+count2+"][Cantidad]";
			gridData2[rows2][0].fieldFindType = FieldKeyType.ID;
			gridData2[rows2][1].fieldPath = "form[grd1]["+count2+"][Precio]";
			gridData2[rows2][1].fieldFindType = FieldKeyType.ID;
			gridData2[rows2][2].fieldPath = "form[grd1]["+count2+"][Total]";
			gridData2[rows2][2].fieldFindType = FieldKeyType.ID;
			gridData2[rows2][3].fieldPath = "form[grd1]["+count2+"][Valor1]";
			gridData2[rows2][3].fieldFindType = FieldKeyType.ID;
			gridData2[rows2][4].fieldPath = "form[grd1]["+count2+"][Valor2]";
			gridData2[rows2][4].fieldFindType = FieldKeyType.ID;
			gridData2[rows2][5].fieldPath = "form[grd1]["+count2+"][Promedio]";
			gridData2[rows2][5].fieldFindType = FieldKeyType.ID;
					
		}


		FormFieldData[] arrayData2 = new FormFieldData[1];
		arrayData2[0] = new FormFieldData();
		
		arrayData2[0].fieldPath = "form[Send]";
		arrayData2[0].fieldFindType = FieldKeyType.ID;
		arrayData2[0].fieldType = FieldType.BUTTON;
		arrayData2[0].fieldValue = "";
		
		FormFieldData[] arrayData3 = new FormFieldData[1];
		arrayData3[0] = new FormFieldData();
		
		arrayData3[0].fieldPath = "//*[@id=\"form[SYS_GRID_AGGREGATE_grd1_Total]\"]";
		arrayData3[0].fieldFindType = FieldKeyType.XPATH;

		Pages.InputDocProcess().openCaseFrame();
		for(int values = 0; values<gridData2.length;values++)
		{
			Assert.assertEquals(Integer.toString(cant[values]), Value.getValue(gridData2[values][0].fieldFindType, gridData2[values][0].fieldPath));
			Assert.assertEquals(Double.toString(prec1[values]), Value.getValue(gridData2[values][1].fieldFindType, gridData2[values][1].fieldPath));
			Assert.assertEquals(Double.toString(total[values]), Value.getValue(gridData2[values][2].fieldFindType, gridData2[values][2].fieldPath));
			Assert.assertEquals(Double.toString(perc1[values]), Value.getValue(gridData2[values][3].fieldFindType, gridData2[values][3].fieldPath));
			Assert.assertEquals(Double.toString(perc2[values]), Value.getValue(gridData2[values][4].fieldFindType, gridData2[values][4].fieldPath));
			Assert.assertEquals(Double.toString(prom[values]), Value.getValue(gridData2[values][5].fieldFindType, gridData2[values][5].fieldPath));
		}

		Assert.assertEquals(Double.toString(sum), Value.getValue(arrayData3[0].fieldFindType, arrayData3[0].fieldPath));
		Assert.assertTrue(FormFiller.formFillElements(arrayData2));
		Assert.assertTrue(Pages.InputDocProcess().continuebtn());
		Pages.Main().logout();

	}

}