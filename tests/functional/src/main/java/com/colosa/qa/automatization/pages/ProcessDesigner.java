package com.colosa.qa.automatization.pages;

import java.util.List;
import java.lang.Exception;
import com.colosa.qa.automatization.common.Browser;
import com.colosa.qa.automatization.common.ConfigurationSettings;
import com.colosa.qa.automatization.pages.DynaformDesigner;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.ui.Select;

public class ProcessDesigner extends Page{

	public enum DynaformType{
		BLANK("blankType"),
		PM_TABLE("pmTableType");

		private String id;

		DynaformType(String id){
			this.id = id;
		}

		public String getId(){
			return this.id;
		}

	};

	public enum DynaformSubType{
		NORMAL("normalType"),
		GRID("gridType");

		private String id;

		DynaformSubType(String id){
			this.id = id;
		}

		public String getId(){
			return this.id;
		}
	}

	public enum MenuOption{
		DYNAFORMS("dynaforms"), 
		INPUT_DOCUMENTS("inputDocuments"), 
		OUTPUT_DOCUMENTS("outputDocuments"), 
		TRIGGERS("triggers"), 
		REPORT_TABLES("reportTables"), 
		DATABASE_CONNECTIONS("databaseConnections"), 
		CASE_SCHEDULER("caseScheduler");

		private String id;

		MenuOption(String id){
			this.id = id;
		}

		public String getId(){
			return this.id;
		}
	};

	public ProcessDesigner() throws Exception{

	}
	
	public void open(MenuOption option) throws Exception{
		Browser.driver().switchTo().defaultContent();
		Browser.driver().switchTo().frame("frameMain");
		Browser.getElement("processDesigner.webElement.menuItems."+option.getId()).click();
		Browser.driver().switchTo().defaultContent();
	}

	private void openDynaforms() throws Exception{
		this.open(ProcessDesigner.MenuOption.DYNAFORMS);
	}

	public void openInputDocuments() throws Exception{
		this.open(ProcessDesigner.MenuOption.INPUT_DOCUMENTS);	
	}

	public void openOutputDocuments() throws Exception{
		this.open(ProcessDesigner.MenuOption.OUTPUT_DOCUMENTS);
	}

	public void openTriggers() throws Exception{
		this.open(ProcessDesigner.MenuOption.TRIGGERS);
	}

	public void openReportTables() throws Exception{
		this.open(ProcessDesigner.MenuOption.REPORT_TABLES);
	}

	public void openDatabaseConnections() throws Exception{
		this.open(ProcessDesigner.MenuOption.DATABASE_CONNECTIONS);
	}

	public void openCaseScheduler() throws Exception{
		this.open(ProcessDesigner.MenuOption.CASE_SCHEDULER);
	}

	private void newDynaform(DynaformType type) throws Exception{
		this.openDynaforms();
		Browser.driver().switchTo().frame("frameMain");
		Browser.getElement("processDesigner.webElement.panelNewButton").click();
		Browser.getElement("processDesigner.webElement.newDynaform."+type.getId()).click();
		Browser.getElement("processDesigner.webElement.newDynaform.selectDynaformTypeButton").click();
	}

	public DynaformDesigner newBlankDynaform(String title, DynaformSubType type, String description) throws Exception{
		Browser.driver().switchTo().defaultContent();
		Browser.driver().switchTo().frame("frameMain");
		this.newDynaform(ProcessDesigner.DynaformType.BLANK);
		Browser.getElement("processDesigner.webElement.newBlankDynaform.title").sendKeys(title);
		new Select(Browser.getElement("processDesigner.webElement.newBlankDynaform.type")).selectByValue(ConfigurationSettings.getInstance().getSetting("processDesigner.webElement.newBlankDynaform.type."+type.getId()));
		Browser.getElement("processDesigner.webElement.newBlankDynaform.description").sendKeys(description);
		Browser.getElement("processDesigner.webElement.newBlankDynaform.saveOpen").click();
		return new DynaformDesigner(Browser.getElement("processDesigner.webElement.newBlankDynaform.designer"));
	}

	public DynaformDesigner newBlankDynaform(String title, DynaformSubType type) throws Exception{
		return this.newBlankDynaform(title, type, "");
	}

	public Boolean inPage() throws Exception{
		return (Browser.driver().getCurrentUrl().indexOf(ConfigurationSettings.getInstance().getSetting("processDesigner.idURL")) >= 0);
	}

	public Boolean inPage(String processName) throws Exception{
		if(Browser.getElements("processDesigner.processNameLocation").size() == 0)
			return false;
		return (Browser.getElement("processDesigner.processNameLocation").getText().trim().equals(processName.trim())) && this.inPage();
	}
}