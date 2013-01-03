package com.colosa.qa.automatization.tests.main;

import org.junit.Assert;
import org.junit.AfterClass;
import org.junit.Test;

import com.colosa.qa.automatization.pages.*;
import com.colosa.qa.automatization.common.*;

import java.io.FileNotFoundException;
import java.io.IOException;

public class TestMain{

	
	

	@Test
	public void changeMenu() throws Exception{
		Pages.Login().gotoUrl();
		Pages.Login().loginUser("admin", "admin", "workflow");
		Pages.Main().goDashboards();
		//Pages.Main().goHome();
		//Pages.Main().goDesigner();
		Pages.Main().goAdmin();
		Pages.Main().logout();

	}


	/*@Test
	public void logout() throws Exception{
		Pages.Main().logout();
	}

	@AfterClass
	public static void cleanup(){
		Browser.close();
	}*/
}