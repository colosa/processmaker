package com.colosa.qa.automatization.tests.inputDocuments;

import org.junit.Assert;
import org.junit.AfterClass;
import org.junit.Test;

import com.colosa.qa.automatization.pages.*;
import com.colosa.qa.automatization.common.*;

import java.io.FileNotFoundException;
import java.io.IOException;

public class TestInputDocumentList{

	@Test
	public void createInputDocument() throws FileNotFoundException, IOException, Exception{

		Pages.Login().gotoUrl();
		Pages.Login().loginUser("admin", "admin", "workflow");
		Pages.Main().goDesigner();
		Pages.ProcessList().openProcess("Test 1");
		Pages.ProcessDesigner().openInputDocuments();
		Pages.InputDocumentList().createInputDoc("Prueba 3", "Digital", "Prueba 3", "YES", "@#PROCESS", "_Document3_@#TASK");
		//Pages.InputDocumentList().closePopup();

	}

}