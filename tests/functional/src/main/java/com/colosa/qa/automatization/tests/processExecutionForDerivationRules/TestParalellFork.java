package com.colosa.qa.automatization.tests.processExecutionForDerivationRules;

import org.junit.Assert;
import org.junit.AfterClass;
import org.junit.Test;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import com.colosa.qa.automatization.pages.*;
import com.colosa.qa.automatization.common.*;

import java.io.FileNotFoundException;
import java.io.IOException;

public class TestParalellFork{
@Test
public void runProcess() throws Exception{
	Pages.Login().gotoUrl();
	Pages.Login().loginUser("admin","admin","workflow");
	Pages.Main().goHome();
	int casenumber=Pages.Home().startCase("TestParalellFork (Task 1)");
	openCaseFrame();
	FormFieldData[] fieldArray=new FormFieldData[4];
	fieldArray[0]=new FormFieldData();
	fieldArray[1]=new FormFieldData();
	fieldArray[2]=new FormFieldData();
	fieldArray[3]=new FormFieldData();

	fieldArray[0].fieldPath="form[nombre]";
	fieldArray[0].fieldFindType=FieldKeyType.ID;
	fieldArray[0].fieldType=FieldType.TEXTBOX;
	fieldArray[0].fieldValue="rodrigo ivan";

	fieldArray[1].fieldPath="form[saldo]";
	fieldArray[1].fieldFindType=FieldKeyType.ID;
	fieldArray[1].fieldType=FieldType.TEXTBOX;
	fieldArray[1].fieldValue="4500";

	fieldArray[2].fieldPath="form[cuenta]";
	fieldArray[2].fieldFindType=FieldKeyType.ID;
	fieldArray[2].fieldType=FieldType.DROPDOWN;
	fieldArray[2].fieldValue="bolivianos";

	fieldArray[3].fieldPath="form[send]";
	fieldArray[3].fieldFindType=FieldKeyType.ID;
	fieldArray[3].fieldType=FieldType.BUTTON;
	fieldArray[3].fieldValue="";

	FormFiller.formFillElements(fieldArray);
	Assert.assertTrue(Pages.InputDocProcess().continuebtn());
	Pages.Main().logout();
	openTask2(casenumber);
} 

public void openTask2(int casenumber) throws Exception{
Pages.Login().gotoUrl();
	Pages.Login().loginUser("ronald","sample","");
		Pages.Main().goHome();
		Pages.Home().gotoInbox();
    Assert.assertTrue("The case does not exist in inbox", Pages.Home().existCase(casenumber));
  openCase(casenumber);
  openCaseFrame();
	FormFieldData[] fieldArray1=new FormFieldData[1];
	fieldArray1[0] = new FormFieldData();
	
	fieldArray1[0].fieldPath="form[send]";
	fieldArray1[0].fieldFindType=FieldKeyType.ID;
	fieldArray1[0].fieldType=FieldType.BUTTON;
	fieldArray1[0].fieldValue="";

	FormFiller.formFillElements(fieldArray1);
	Assert.assertTrue(Pages.InputDocProcess().continuebtn());
	Pages.Main().logout();
	openTask3(casenumber);

}

public void openTask3(int casenumber) throws Exception{
	Pages.Login().gotoUrl();
	Pages.Login().loginUser("iver","sample","");
		Pages.Main().goHome();
		Pages.Home().gotoInbox();
    Assert.assertTrue("The case does not exist in inbox", Pages.Home().existCase(casenumber));
      openCase(casenumber);
	openCaseFrame();
	FormFieldData[] fieldArray2=new FormFieldData[2];
	fieldArray2[0]=new FormFieldData();
	fieldArray2[1]=new FormFieldData();


	fieldArray2[0].fieldPath="form[tipocuenta][corriente]";
	fieldArray2[0].fieldFindType=FieldKeyType.ID;
	fieldArray2[0].fieldType=FieldType.RADIOBUTTON;
	fieldArray2[0].fieldValue="";

	fieldArray2[1].fieldPath="form[send]";
	fieldArray2[1].fieldFindType=FieldKeyType.ID;
	fieldArray2[1].fieldType=FieldType.BUTTON;
	fieldArray2[1].fieldValue="";

	FormFiller.formFillElements(fieldArray2);
	Assert.assertTrue(Pages.InputDocProcess().continuebtn());
	Pages.Main().logout();
	openTask4(casenumber);
}

public void openTask4(int casenumber) throws Exception{
	Pages.Login().gotoUrl();
	Pages.Login().loginUser("hector","sample","");
	Pages.Main().goHome();
	Pages.Home().gotoInbox();
    Assert.assertTrue("The case does not exist in inbox", Pages.Home().existCase(casenumber));
	openCase(casenumber);
	openCaseFrame();
	FormFieldData[] fieldArray3=new FormFieldData[2];
	fieldArray3[0]=new FormFieldData();
	fieldArray3[1]=new FormFieldData();

	fieldArray3[0].fieldPath="form[porcentaje]";
	fieldArray3[0].fieldFindType=FieldKeyType.ID;
	fieldArray3[0].fieldType=FieldType.TEXTBOX;
	fieldArray3[0].fieldValue="5%";

	fieldArray3[1].fieldPath="form[send]";
	fieldArray3[1].fieldFindType=FieldKeyType.ID;
	fieldArray3[1].fieldType=FieldType.BUTTON;
	fieldArray3[1].fieldValue="";

	FormFiller.formFillElements(fieldArray3);
	Assert.assertTrue(Pages.InputDocProcess().continuebtn());
	Pages.Main().logout();
	openTask5(casenumber);
}

public void openTask5(int casenumber) throws Exception{
	Pages.Login().gotoUrl();
	Pages.Login().loginUser("admin","admin","");
	Pages.Main().goHome();
	Pages.Home().gotoInbox();
    Assert.assertTrue("The case does not exist in inbox", Pages.Home().existCase(casenumber));
	openCase(casenumber);
	openCaseFrame();
	FormFieldData[] fieldArray4=new FormFieldData[3];
	fieldArray4[0]=new FormFieldData();
	fieldArray4[1]=new FormFieldData();
	fieldArray4[2]=new FormFieldData();

	fieldArray4[0].fieldPath="form[incidencias]";
	fieldArray4[0].fieldFindType=FieldKeyType.ID;
	fieldArray4[0].fieldType=FieldType.TEXTBOX;
	fieldArray4[0].fieldValue="5%";

	fieldArray4[1].fieldPath="form[aprobado]";
	fieldArray4[1].fieldFindType=FieldKeyType.ID;
	fieldArray4[1].fieldType=FieldType.DROPDOWN;
	fieldArray4[1].fieldValue="Yes";

    fieldArray4[2].fieldPath="form[send]";
	fieldArray4[2].fieldFindType=FieldKeyType.ID;
	fieldArray4[2].fieldType=FieldType.BUTTON;
	fieldArray4[2].fieldValue="";

	FormFiller.formFillElements(fieldArray4);
	Assert.assertTrue(Pages.InputDocProcess().continuebtn());
	Pages.Main().logout();
}

public void openCaseFrame() throws Exception{
	    Browser.driver().switchTo().defaultContent();
		Browser.driver().switchTo().frame("casesFrame");
		Browser.driver().switchTo().frame("casesSubFrame");
		Browser.driver().switchTo().frame("openCaseFrame");
}

public void openCase(int casenumber) throws Exception{
	Pages.Home().openCase(casenumber);
}

}