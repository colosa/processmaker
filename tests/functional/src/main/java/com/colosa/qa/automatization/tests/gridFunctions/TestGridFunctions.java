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
	public static int addRow = 30;
	public static String val1 = "Prueba";
	public static String[] prec = {"345.32", "654.44", "543.76", "53,434.54234", "4,324.43", "424,232,424,234,234.8978", "34.413241412", "3,123,132", "0.7988799", "234,234,444", "1,231,313.55", "43,243,242.34343444343", "43,243,242.0000000000000000000000000000", "43,242,342.4343434", "78,956,456,498.1647651649", "234,342,432,424.16542132", "244,242,423,423.243424", "32,442,342", "45,435,435,345.156213", "4,564,645,687.66636998", "798,796,546.63564", "79,896,543.3546", "464,656.656456", "465,463.4646578", "6,546,564,897.6346", "78,986,563.35646", "46,546.463213", "23,164,687,896.3656432", "4657893646.416467", "46,532,156,432.2356654"};
	public static String[] perc = {"54.8", "46.9", "2345353.6546465", "33.1", "78979.656", "456463.65648", "789653.6564", "78979632.3565", "5646.35696", "64646.35646", "4656.656464", "45979876653.65465498", "564646.3321", "6464646.6623", "3213212365.64646", "464668797.646546", "98796532169.32465", "2343242342.432423423", "465464646532.3656466", "46464646465.656466465", "87976532169.32132665", "4564646.656989", "466464654679.265646546", "456497983646.64649887", "6546464979643.364979796", "664697931649.561619765", "6467987897621.3164979", "6546439679.136164", "646879833264.3654879", "46456797983.13241564"};
	public static String[] mail = {"asfkasdlh", "&(&/$&(JJHNB", "klñhasdkjfl0/)=&9865", "jfañls&(/&/$%", "lklafja", "?)?=)=(/()kljf", "jlñafjslñf", "¬|·@|·~~~½~·~½¬½[{¬]}{[}{[¬·¬~", "(&)&)(·@~~·", "lkñjlkjklh)(/))·@", "kljlk(&(/&(@||~¬", "klhkjhiy8876546544~·@", "(/&(%&/KHKJH", "đđæß€łŋđð¶€¶ŧ", "→↓←ħĸħµ”nðđŋß»¢", "«»¢““¢n””nn”µđŋħđ¶€¶ßßđðđ", "ßæđðđ@·~~·½·~", "&(/&/(KNJH", "·@|·~·@ðßđß", "klañsfj$$%", "lkñjasfiue~ĸ~łĸ", "ñlajsncx,,@·~·½", "ncladskjfeu", "jñajsdrew#$#@", "lñkajdsflk/()/)", "½¬¬~½¬ðđŋ", "jlñsdfk", "¢»“¢»ßð€ł¶", "/)(/)&@·~·~·", "lkjfñakl#(&@·~@)"};
	public static String[] alf = {"lkjfñakl#(&@·~@)", "¢»“¢»ßð€ł¶", "/)(/)&@·~·~·", "lñkajdsflk/()/)", "jñajsdrew#$#@", "ncladskjfeu", "ñlajsncx,,@·~·½", "lkñjasfiue~ĸ~łĸ", "·@|·~·@ðßđß", "¬|·@|·~~~½~·~½¬½[{¬]}{[}{[¬·¬~", "&(/&/(KNJH", "ßæđðđ@·~~·½·~", "«»¢““¢n””nn”µđŋħđ¶€¶ßßđðđ", "→↓←ħĸħµ”nðđŋß»¢", "đđæß€łŋđð¶€¶ŧ", "(/&(%&/KHKJH", "klhkjhiy8876546544~·@", "kljlk(&(/&(@||~¬", "lkñjlkjklh)(/))·@", "(&)&)(·@~~·", "klañsfj$$%", "jlñafjslñf", "?)?=)=(/()kljf", "lklafja", "jfañls&(/&/$%", "½¬¬~½¬ðđŋ", "jlñsdfk", "klñhasdkjfl0/)=&9865", "&(&/$&(JJHNB", "asfkasdlh"};
	public static String[] alph = {"lkjfñakl#(&@·~@)", "¢»“¢»ßð€ł¶", "/)(/)&@·~·~·", "lñkajdsflk/()/)", "jñajsdrew#$#@", "ncladskjfeu", "ñlajsncx,,@·~·½", "lkñjasfiue~ĸ~łĸ", "·@|·~·@ðßđß", "¬|·@|·~~~½~·~½¬½[{¬]}{[}{[¬·¬~", "&(/&/(KNJH", "ßæđðđ@·~~·½·~", "«»¢““¢n””nn”µđŋħđ¶€¶ßßđðđ", "→↓←ħĸħµ”nðđŋß»¢", "đđæß€łŋđð¶€¶ŧ", "(/&(%&/KHKJH", "klhkjhiy8876546544~·@", "kljlk(&(/&(@||~¬", "lkñjlkjklh)(/))·@", "(&)&)(·@~~·", "klañsfj$$%", "jlñafjslñf", "?)?=)=(/()kljf", "lklafja", "jfañls&(/&/$%", "½¬¬~½¬ðđŋ", "jlñsdfk", "klñhasdkjfl0/)=&9865", "&(&/$&(JJHNB", "asfkasdlh"};
	public static String[] ent = {"lkjfñakl#(&@·~@)", "¢»“¢»ßð€ł¶", "/)(/)&@·~·~·", "lñkajdsflk/()/)", "jñajsdrew#$#@", "4564654512316579879846546541316796794643465463316465789797984613214165469797465431464", "ñlajsncx,,@·~·½", "lkñjasfiue~ĸ~łĸ", "98754643216.1646979845641657946416798745646549879813.13665498798", "¬|·@|·~~~½~·~½¬½[{¬]}{[}{[¬·¬~", "&(/&/(KNJH", "ßæđðđ@·~~·½·~", "465,465,456,346,543,216,764,678,945,647,987,465,454,987,456,479,845", "→↓←ħĸħµ”nðđŋß»¢", "đđæß€łŋđð¶€¶ŧ", "(/&(%&/KHKJH", "4646513649874456464678951216496545941594978765142316574987654", "kljlk(&(/&(@||~¬", "lkñjlkjklh)(/))·@", "(&)&)(·@~~·", "klañsfj$$%", "jlñafjslñf", "?)?=)=(/()kljf", "lklafja", "jfañls&(/&/$%", "½¬¬~½¬ðđŋ", "jlñsdfk", "klñhasdkjfl0/)=&9865", "&(&/$&(JJHNB", "asfkasdlh"};
	public static String[] rel = {"lkjfñakl#(&@·~@)", "¢»“¢»ßð€ł¶", "/)(/)&@·~·~·", "lñkajdsflk/()/)", "jñajsdrew#$#@", "ncladskjfeu", "ñlajsncx,,@·~·½", "lkñjasfiue~ĸ~łĸ", "·@|·~·@ðßđß", "¬|·@|·~~~½~·~½¬½[{¬]}{[}{[¬·¬~", "&(/&/(KNJH", "ßæđðđ@·~~·½·~", "«»¢““¢n””nn”µđŋħđ¶€¶ßßđðđ", "→↓←ħĸħµ”nðđŋß»¢", "đđæß€łŋđð¶€¶ŧ", "(/&(%&/KHKJH", ".3131.135646313,3156.13456,13456", "kljlk(&(/&(@||~¬", "lkñjlkjklh)(/))·@", "(&)&)(·@~~·", "klañsfj$$%", "134654,315469,3132164", "?)?=)=(/()kljf", "1231649879.134654967.135", "4654979.1346479.131654987", "½¬¬~½¬ðđŋ", "000.03213.0364654.13165", "klñhasdkjfl0/)=&9865", "&(&/$&(JJHNB", "54643213.136465.1346548.13216547897"};	
	public static String[] loginF = {"lkjfñakl#(&@·~@)", "¢»“¢»ßð€ł¶", "/)(/)&@·~·~·", "lñkajdsflk/()/)", "jñajsdrew#$#@", "ncladskjfeu", "ñlajsncx,,@·~·½", "lkñjasfiue~ĸ~łĸ", "·@|·~·@ðßđß", "¬|·@|·~~~½~·~½¬½[{¬]}{[}{[¬·¬~", "&(/&/(KNJH", "ßæđðđ@·~~·½·~", "«»¢““¢n””nn”µđŋħđ¶€¶ßßđðđ", "→↓←ħĸħµ”nðđŋß»¢", "đđæß€łŋđð¶€¶ŧ", "(/&(%&/KHKJH", "klhkjhiy8876546544~·@", "kljlk(&(/&(@||~¬", "lkñjlkjklh)(/))·@", "(&)&)(·@~~·", "klañsfj$$%", "jlñafjslñf", "?)?=)=(/()kljf", "lklafja", "jfañls&(/&/$%", "½¬¬~½¬ðđŋ", "jlñsdfk", "klñhasdkjfl0/)=&9865", "&(&/$&(JJHNB", "asfkasdlh"};
	public static double total;
	public static double prom;
	
	@Test
	public void testCase() throws FileNotFoundException, IOException, Exception{

		Pages.Login().gotoUrl();
		Pages.Login().loginUser("admin", "admin", "workflow");
		Pages.Main().goHome();	
		caseNum = Pages.Home().startCase("Grillas Funciones (Task 1)");
		Pages.DynaformExecution().intoDynaform();
		int count = 0;

		for(int rows = 1;rows<addRow;rows++)
		{
			Pages.DynaformExecution().gridAddNewRow("grid1");
		}


		for(int rows = 0; rows<addRow;rows++)
		{
			count = rows + 1;
			Pages.DynaformExecution().setGridFieldValue("grid1", count, "Columna1", val1);
			Pages.DynaformExecution().setGridFieldValue("grid1", count, "Precio1", prec[rows]);
			Pages.DynaformExecution().setGridFieldValue("grid1", count, "Porcentaje1", perc[rows]);
			Pages.DynaformExecution().setGridFieldValue("grid1", count, "Mail", mail[rows]);
			Pages.DynaformExecution().setGridFieldValue("grid1", count, "Alfanumerico", alf[rows]);
			Pages.DynaformExecution().setGridFieldValue("grid1", count, "Alphabetic", alph[rows]);
			Pages.DynaformExecution().setGridFieldValue("grid1", count, "Integer", ent[rows]);
			Pages.DynaformExecution().setGridFieldValue("grid1", count, "Real", rel[rows]);
			Pages.DynaformExecution().setGridFieldValue("grid1", count, "Login", loginF[rows]);
						
		}

		/*for(int rowt = 0; rowt<addRow;rowt++)
		{
			total = total + Double.parseDouble(prec[rowt]);
		}*/
		
		Pages.DynaformExecution().setFieldValue("Send", "", FieldType.BUTTON);
		

	
		Assert.assertTrue(Pages.InputDocProcess().continuebtn());
		Pages.Main().logout();
	
	/*}

	@Test
	public void continueTestCase() throws FileNotFoundException, IOException, Exception{*/

		Pages.Login().gotoUrl();
		Pages.Login().loginUser("iver", "sample", "");
		Pages.Main().goHome();	
		Pages.Home().openCase(caseNum);

		String porcen;

		Pages.DynaformExecution().intoDynaform();
		
		/*for(int values = 0; values<addRow;values++)
		{
			porcen= perc[values];
			porcen= porcen + " %";
			Assert.assertEquals(val1, Pages.DynaformExecution().getGridFieldValue("grid1", values, "Columna1"));
			Assert.assertEquals(prec[values], Pages.DynaformExecution().getGridFieldValue("grid1", values, "Precio1"));
			Assert.assertEquals(porcen[values], Pages.DynaformExecution().getGridFieldValue("grid1", values, "Porcentaje1"));
			Assert.assertEquals(mail[values], Pages.DynaformExecution().getGridFieldValue("grid1", values, "Mail"));
			Assert.assertEquals(alf[values], Pages.DynaformExecution().getGridFieldValue("grid1", values, "Alfanumerico"));
			Assert.assertEquals(alph[values], Pages.DynaformExecution().getGridFieldValue("grid1", values, "Alphabetic"));
			Assert.assertEquals(ent[values], Pages.DynaformExecution().getGridFieldValue("grid1", values, "Integer"));
			Assert.assertEquals(rel[values], Pages.DynaformExecution().getGridFieldValue("grid1", values, "Real"));
			Assert.assertEquals(loginF[values], Pages.DynaformExecution().getGridFieldValue("grid1", values, "Login"));
		}*/
		//Assert.assertEquals(Double.toString(total), getFieldValue("SYS_GRID_AGGREGATE_grid1_Precio1"));
		//Assert.assertEquals(Double.toString(prom), getFieldValue("SYS_GRID_AGGREGATE_grid1_Porcentaje1"));
		//Assert.assertTrue(FormFiller.formFillElements(arrayData2));
		Pages.DynaformExecution().setFieldValue("Send", "", FieldType.BUTTON);
		Assert.assertTrue(Pages.InputDocProcess().continuebtn());
		Pages.Main().logout();

	}

}