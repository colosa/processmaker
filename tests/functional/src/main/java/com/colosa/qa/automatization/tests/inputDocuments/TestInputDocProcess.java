package com.colosa.qa.automatization.tests.inputDocuments;

import org.junit.Assert;
import org.junit.AfterClass;
import org.junit.Test;

import com.colosa.qa.automatization.pages.*;
import com.colosa.qa.automatization.common.*;

import java.io.FileNotFoundException;
import java.io.IOException;

public class TestInputDocProcess{


	@Test
	public void uploadInputDoc() throws FileNotFoundException, IOException, Exception{

		Pages.Login().gotoUrl();
		Pages.Login().loginUser("admin", "admin", "workflow");
		Pages.Main().goHome();
		Pages.Home().startCase("inputDocProcess (Task 1)");
		Pages.InputDocProcess().uploadFile("/home/ernesto/Documents/Prueba_Input_Doc.docx", "Test File");
		Pages.InputDocProcess().uploadFile("/home/ernesto/Documents/Prueba_Input_Doc.docx", "Test File");
		Pages.InputDocProcess().continuebtn();
	}

}