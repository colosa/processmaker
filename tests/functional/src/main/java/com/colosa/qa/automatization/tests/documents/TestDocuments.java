package com.colosa.qa.automatization.tests.documents;

import org.junit.Assert;
import org.junit.AfterClass;
import org.junit.Test;

import com.colosa.qa.automatization.pages.*;
import com.colosa.qa.automatization.common.*;

import java.io.FileNotFoundException;
import java.io.IOException;

public class TestDocuments{

	@Test
	public void downloadFile() throws Exception{

		Pages.Login().gotoUrl();
		Pages.Login().loginUser("admin", "admin", "workflow");
		Pages.Main().goHome();
		Pages.Home().gotoDocuments();
		Pages.Documents().selectFolder("Test 1");
		Pages.Documents().downloadDocument("default.conf");
		

	}
}