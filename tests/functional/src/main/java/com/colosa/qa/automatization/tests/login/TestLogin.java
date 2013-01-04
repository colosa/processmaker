package com.colosa.qa.automatization.tests.login;

import org.junit.Assert;
import org.junit.After;
import org.junit.AfterClass;
import org.junit.BeforeClass;
import org.junit.Test;
import org.junit.Rule;

import com.colosa.qa.automatization.pages.Pages;
import com.colosa.qa.automatization.common.*;

import java.io.FileNotFoundException;
import java.io.IOException;

public class TestLogin{
/*
	@Test
	public void canGoToLoginPage(){
		Pages.Login().gotoUrl();
		//System.out.println("se abrio el login\n"); 

		Assert.assertTrue(Pages.Login().isAtLoginPage());
	}
*/

	@Test
	public void canLoginUser() throws Exception{
		Pages.Login().gotoUrl();
		//System.out.println("se abrio el login\n"); 
		Assert.assertTrue(Pages.Login().isAtLoginPage());

		Pages.Login().loginUser("admin", "admin", "workflow");

		//Assert logged user home page
		Assert.assertTrue(Pages.Login().isUserLogged());
	}


	@After
	public void cleanup(){
		Browser.close();
	}
}