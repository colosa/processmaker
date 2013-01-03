package com.colosa.qa.automatization.tests.outputDocuments;

import org.junit.Assert;
import org.junit.AfterClass;
import org.junit.Test;

import com.colosa.qa.automatization.pages.*;
import com.colosa.qa.automatization.common.*;

import java.io.FileNotFoundException;
import java.io.IOException;

public class TestOutputDocProcess{


	@Test
	public void downloadOutputDoc() throws FileNotFoundException, IOException, Exception{

		Pages.Login().gotoUrl();
		Pages.Login().loginUser("admin", "admin", "workflow");
		Pages.Main().goHome();
		Pages.Home().startCase("outputDocProcess (Task 1)");

		Pages.OutputDocProcess().downloadDocFile();
		Pages.OutputDocProcess().downloadPdfFile();
		Pages.OutputDocProcess().nextbtn();
		Pages.OutputDocProcess().downloadDocFile();
		Pages.OutputDocProcess().downloadPdfFile();
		Pages.OutputDocProcess().nextbtn();
		Pages.OutputDocProcess().continuebtn();
	}

}