package com.colosa.qa.automatization.tests.inputDocuments;

import org.junit.Assert;
import org.junit.AfterClass;
import org.junit.Test;

import com.colosa.qa.automatization.pages.*;
import com.colosa.qa.automatization.common.*;

import java.io.FileNotFoundException;
import java.io.IOException;

public class TestInputDocument{

	@Test
	public void runProcess() throws FileNotFoundException, IOException, Exception{

		Pages.Login().gotoUrl();
		Pages.Login().loginUser("admin", "admin", "workflow");
		Pages.Main().goHome();
		int caseNumber = Pages.Home().startCase("Input Process with and without versioning (enviar formulario y documento)");
		Pages.InputDocProcess().openCaseFrame();
		FormFieldData[] fieldArray = new FormFieldData[6];
		fieldArray[0] = new FormFieldData();
		fieldArray[1] = new FormFieldData();
		fieldArray[2] = new FormFieldData();
		fieldArray[3] = new FormFieldData();
		fieldArray[4] = new FormFieldData();
		fieldArray[5] = new FormFieldData();


		fieldArray[0].fieldPath = "form[nombre]";
		fieldArray[0].fieldFindType = FieldKeyType.ID;
		fieldArray[0].fieldType = FieldType.TEXTBOX;
		fieldArray[0].fieldValue = "Ernesto Vega";
		fieldArray[1].fieldPath = "form[fechanacimiento]";
		fieldArray[1].fieldFindType = FieldKeyType.ID;
		fieldArray[1].fieldType = FieldType.TEXTBOX;
		fieldArray[1].fieldValue = "1987-12-29";
		fieldArray[2].fieldPath = "form[CI]";
		fieldArray[2].fieldFindType = FieldKeyType.ID;
		fieldArray[2].fieldType = FieldType.TEXTBOX;
		fieldArray[2].fieldValue = "6812278";
		fieldArray[3].fieldPath = "form[carrera]";
		fieldArray[3].fieldFindType = FieldKeyType.ID;
		fieldArray[3].fieldType = FieldType.DROPDOWN;
		fieldArray[3].fieldValue = "ingenieria electronica";
		fieldArray[4].fieldPath = "form[beca][par]";
		fieldArray[4].fieldFindType = FieldKeyType.ID;
		fieldArray[4].fieldType = FieldType.RADIOBUTTON;
		fieldArray[4].fieldValue = "";
		fieldArray[5].fieldPath = "form[send]";
		fieldArray[5].fieldFindType = FieldKeyType.ID;
		fieldArray[5].fieldType = FieldType.BUTTON;
		fieldArray[5].fieldValue = "";
		FormFiller.formFillElements(fieldArray);
		Pages.InputDocProcess().uploadFile("/home/ernesto/Documents/Prueba_Input_Doc.docx", "Test File");
		Pages.InputDocProcess().uploadFile("/home/ernesto/Documents/Prueba_Input_Doc.docx", "Test File");
		Pages.InputDocProcess().continuebtn();
		Pages.Home().openCase(caseNumber);
		Pages.InputDocProcess().openCaseFrame();
		FormFieldData[] fieldArray2 = new FormFieldData[1];
		fieldArray2[0] = new FormFieldData();

		fieldArray2[0].fieldPath = "form[send]";
		fieldArray2[0].fieldFindType = FieldKeyType.ID;
		fieldArray2[0].fieldType = FieldType.BUTTON;
		fieldArray2[0].fieldValue = "";
		FormFiller.formFillElements(fieldArray2);
		
		Pages.InputDocProcess().uploadFile("/home/ernesto/Documents/Prueba_Input_Doc.docx", "Test File");
		Pages.InputDocProcess().continuebtn();
	}
}