package com.colosa.qa.automatization.tests.derivationRulesCombinations;

import org.junit.Assert;
import org.junit.AfterClass;
import org.junit.Test;

import com.colosa.qa.automatization.pages.*;
import com.colosa.qa.automatization.common.*;
import org.openqa.selenium.WebElement;

import java.io.FileNotFoundException;
import java.io.IOException;

public class DerivationRulesSequential{

	protected static int caseNum;

	@Test
	public void derivationRulesSequential() throws FileNotFoundException, IOException, Exception{

		Pages.Login().gotoUrl();
		Pages.Login().loginUser("admin", "admin", "workflow");
		Pages.Main().goHome();	
		caseNum = Pages.Home().startCase("Derivation rules - sequential (Init)");
		Pages.DynaformExecution().intoDynaform();
	    Pages.InputDocProcess().continuebtn();
		Pages.DynaformExecution().outDynaform();
		Pages.Main().logout();
	    //cyclical task
		Pages.Login().gotoUrl();
		Pages.Login().loginUser("chris", "sample", "workflow");
		Pages.Main().goHome();	
    	Pages.Home().gotoInbox();
		Assert.assertTrue("The case does not exist in Inbox", Pages.Home().existCase(caseNum));
		Pages.Home().openCase(caseNum);
		Pages.DynaformExecution().intoDynaform();
		Pages.DynaformExecution().setFieldValue("TASKS][1][USR_UID", "Swan, William");
	    Pages.InputDocProcess().continuebtn();
		Pages.DynaformExecution().outDynaform();
		Pages.Main().logout();
		//Manual task
		Pages.Login().gotoUrl();
		Pages.Login().loginUser("william", "sample", "workflow");
		Pages.Main().goHome();	
    	Pages.Home().gotoInbox();
		Assert.assertTrue("The case does not exist in Inbox", Pages.Home().existCase(caseNum));
		Pages.Home().openCase(caseNum);
		Pages.DynaformExecution().intoDynaform();
		Pages.DynaformExecution().setFieldValue("name", "Charles Puyol");
		Pages.DynaformExecution().setFieldValue("amount", "3000");
		Pages.DynaformExecution().setFieldValue("send", "");
	    Pages.InputDocProcess().continuebtn();
		Pages.DynaformExecution().outDynaform();
		Pages.Main().logout();
		//Value based task
		Pages.Login().gotoUrl();
		Pages.Login().loginUser("ezequiel", "sample", "workflow");
		Pages.Main().goHome();	
    	Pages.Home().gotoInbox();
		Assert.assertTrue("The case does not exist in Inbox", Pages.Home().existCase(caseNum));
		Pages.Home().openCase(caseNum);
		Pages.DynaformExecution().intoDynaform();
	    Pages.InputDocProcess().continuebtn();
		Pages.DynaformExecution().outDynaform();
		Pages.Main().logout();
		//Report to task
		Pages.Login().gotoUrl();
		Pages.Login().loginUser("zachary", "sample", "workflow");
		Pages.Main().goHome();	
    	Pages.Home().gotoInbox();
		Assert.assertTrue("The case does not exist in Inbox", Pages.Home().existCase(caseNum));
		Pages.Home().openCase(caseNum);
		Pages.DynaformExecution().intoDynaform();
	    Pages.InputDocProcess().continuebtn();
		Pages.DynaformExecution().outDynaform();
		Pages.Main().logout();
		//Self service task
		Pages.Login().gotoUrl();
		Pages.Login().loginUser("chris", "sample", "workflow");
		Pages.Main().goHome();	
		Pages.Home().gotoUnassigned();
		Assert.assertTrue("The case does not exist in Unassigned", Pages.Home().existCase(caseNum));
		Pages.Home().openCase(caseNum);
		Pages.DynaformExecution().intoDynaform();
		Pages.DynaformExecution().setFieldValue("BTN_CATCH", "");
		Pages.DynaformExecution().setFieldValue("send", "");
	    Pages.InputDocProcess().continuebtn();
		Pages.DynaformExecution().outDynaform();
		Pages.Main().logout();
		//Self Service Value Based task
		Pages.Login().gotoUrl();
		Pages.Login().loginUser("william", "sample", "workflow");
		Pages.Main().goHome();	
		Pages.Home().gotoUnassigned();
		Assert.assertTrue("The case does not exist in Unassigned", Pages.Home().existCase(caseNum));
		Pages.Home().openCase(caseNum);
		Pages.DynaformExecution().intoDynaform();
		Pages.DynaformExecution().setFieldValue("BTN_CATCH", "");
		Pages.DynaformExecution().outDynaform();
		Pages.DynaformExecution().intoDynaform();
	    Pages.InputDocProcess().continuebtn();
		Pages.DynaformExecution().outDynaform();
		Pages.Main().logout();
		//Open cases to verify Cyclical assigmnent
		Pages.Login().gotoUrl();
		Pages.Login().loginUser("admin", "admin", "workflow");
		Pages.Main().goHome();	
		caseNum = Pages.Home().startCase("Derivation rules - sequential (Init)");
		Pages.DynaformExecution().intoDynaform();
	    Pages.InputDocProcess().continuebtn();
		Pages.DynaformExecution().outDynaform();
		Pages.Main().logout();
		Pages.Login().gotoUrl();
		Pages.Login().loginUser("admin", "admin", "workflow");				
		Pages.Main().goHome();	
		caseNum = Pages.Home().startCase("Derivation rules - sequential (Init)");
		Pages.DynaformExecution().intoDynaform();
	    Pages.InputDocProcess().continuebtn();
		Pages.DynaformExecution().outDynaform();
		Pages.Main().logout();
		//Veryfy if the task is assigned succesfuly
		Pages.Login().gotoUrl();
		Pages.Login().loginUser("zachary", "sample", "workflow");
		Pages.Main().goHome();	
    	Pages.Home().gotoInbox();
		Assert.assertTrue("The case does not exist in Inbox", Pages.Home().existCase(caseNum));
		Pages.DynaformExecution().outDynaform();
		Pages.Main().logout();
	}


}