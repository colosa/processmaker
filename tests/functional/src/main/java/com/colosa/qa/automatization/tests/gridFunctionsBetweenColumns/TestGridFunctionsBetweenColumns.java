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
	public static int addRow = 30;
	public static String val1 = "Prueba";
	public static String[] cant = {"45", "66", "78", "4234234", "234234324", "3243242423", "2442323", "435435", "324243", "543242", "242342434", "23435234324", "324324234324", "324243242", "432432454", "654654645", "4354353", "32123", "235433", "5345242", "5654754", "75434543", "74453543", "34542234", "54464345", "54353242", "6535324", "5345342", "5432424", "65435345", "452423424"};
	public static String[] prec1 = {"345.33", "654.49", "543.76", "53,434.54234", "4,324.43", "424,232,424,234,234.8978", "34.413241412", "3,123,132", "0.7988799", "234,234,444", "1,231,313.55", "43,243,242.34343444343", "43,243,242.0000000000000000000000000000", "43,242,342.4343434", "78,956,456,498.1647651649", "234,342,432,424.16542132", "244,242,423,423.243424", "32,442,342", "45,435,435,345.156213", "4,564,645,687.66636998", "798,796,546.63564", "79,896,543.3546", "464,656.656456", "465,463.4646578", "6,546,564,897.6346", "78,986,563.35646", "46,546.463213", "23,164,687,896.3656432", "4657893646.416467", "46,532,156,432.2356654"};
	public static String[] perc1 = {"354.84", "646.94", "833.13", "33.1", "78979.656", "456463.65648", "789653.6564", "78979632.3565", "5646.35696", "64646.35646", "4656.656464", "45979876653.65465498", "564646.3321", "6464646.6623", "3213212365.64646", "464668797.646546", "98796532169.32465", "2343242342.432423423", "465464646532.3656466", "46464646465.656466465", "87976532169.32132665", "4564646.656989", "466464654679.265646546", "456497983646.64649887", "6546464979643.364979796", "664697931649.561619765", "6467987897621.3164979", "6546439679.136164", "646879833264.3654879", "46456797983.13241564"};
	public static String[] perc2 = {"425.38", "734.27", "978.59", "46545.6564564", "643464.4631", "54645345.547878", "4562145.54648", "1231546.546434", "7534.5468", "78945.448345", "3196.31743", "32134964.32164987", "76532165497.32164987", "32164567.361467487", "33131467.316479", "65432167.31567896", "6463321567.316547", "46465793.315467", "7976467.167478979", "789794643132457.1316579", "4646789.13649679", "79879846.1469798", "646748979.1316547987", "646497.1315749", "6463139.1316549", "321324679.1379796", "4646578931.13646579", "45646546793.31549797", "113213574.13213546", "1312312365.1321646987"};
	public static String[] total;
	public static String[] prom;
	public static double sum = 101149.47;


	@Test
	public void testCase() throws FileNotFoundException, IOException, Exception{

		Pages.Login().gotoUrl();
		Pages.Login().loginUser("admin", "admin", "workflow");
		Pages.Main().goHome();	
		caseNum = Pages.Home().startCase("Grillas Funciones Entre Columnas (Task 1)");
		Pages.DynaformExecution().intoDynaform();
		for(int rows = 1;rows<addRow;rows++)
		{
			Pages.DynaformExecution().gridAddNewRow("grd1");
		}	
		int count = 0;

		for(int rows = 0; rows<addRow;rows++)
		{
			count = rows + 1;
			Pages.DynaformExecution().setGridFieldValue("grd1", count, "Cantidad", cant[rows]);
			Pages.DynaformExecution().setGridFieldValue("grd1", count, "Precio", prec1[rows]);
			Pages.DynaformExecution().setGridFieldValue("grd1", count, "Valor1", perc1[rows]);
			Pages.DynaformExecution().setGridFieldValue("grd1", count, "Valor2", perc2[rows]);
			
		}
		
		Pages.DynaformExecution().setFieldValue("Send", "", FieldType.BUTTON);
		Assert.assertTrue(Pages.InputDocProcess().continuebtn());
		Pages.Main().logout();
	
	}

	@Test
	public void continueTestCase() throws FileNotFoundException, IOException, Exception{

		Pages.Login().gotoUrl();
		Pages.Login().loginUser("iver", "sample", "");
		Pages.Main().goHome();	
		Pages.Home().openCase(caseNum);

		Pages.DynaformExecution().intoDynaform();
		/*for(int values = 0; values<gridData2.length;values++)
		{
			Assert.assertEquals(cant[values], Pages.DynaformExecution().getGridFieldValue("grd1",values, "Cantidad"));
			Assert.assertEquals(prec1[values], Pages.DynaformExecution().getGridFieldValue("grd1",values, "Precio"));
			Assert.assertEquals(total[values], Pages.DynaformExecution().getGridFieldValue("grd1",values, "Total"));
			Assert.assertEquals(perc1[values], Pages.DynaformExecution().getGridFieldValue("grd1",values, "Valor1"));
			Assert.assertEquals(perc2[values], Pages.DynaformExecution().getGridFieldValue("grd1",values, "Valor2"));
			Assert.assertEquals(prom[values], Pages.DynaformExecution().getGridFieldValue("grd1",values, "Promedio"));
			
		}*/

		//Assert.assertEquals(Double.toString(sum), Pages.DynaformExecution().getFieldValue("SYS_GRID_AGGREGATE_grd1_Total"));
		Pages.DynaformExecution().setFieldValue("Send", "", FieldType.BUTTON);
		Assert.assertTrue(Pages.InputDocProcess().continuebtn());
		Pages.Main().logout();

	}

}