package com.colosa.qa.automatization.tests.processlist;

import org.junit.Assert;
import org.junit.AfterClass;
import org.junit.Test;

import com.colosa.qa.automatization.pages.*;
import com.colosa.qa.automatization.common.*;

import java.io.FileNotFoundException;
import java.io.IOException;

public class TestProcessList{


	@Test
	public void openProc() throws Exception{

		Pages.Login().gotoUrl();
		Pages.Login().loginUser("admin", "admin", "workflow");
		Pages.Main().goDesigner();
		//Pages.ProcessList().openProcess("Test2");
		//Pages.ProcessList().importProcess("/home/ernesto/Documents/Test_6.pm");
		//Pages.ProcessList().exportProcess("Test2");
		Pages.ProcessList().newProcess("Test Process2", "Proceso de prueba 2");
	}

}