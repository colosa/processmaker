package com.colosa.qa.automatization.pages;

import java.lang.Exception;
import com.colosa.qa.automatization.common.*;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.Alert;
import org.openqa.selenium.By;
import org.openqa.selenium.support.ui.Select;
import java.util.*;
import com.colosa.qa.automatization.common.controlOptions.input.*;
import com.colosa.qa.automatization.common.controlOptions.selection.*;
import com.colosa.qa.automatization.common.controlOptions.selection.DropDownOptions;

public class DynaformDesigner{

	private WebElement titleBar;
	private Map<String, WebElement> toolbar = new HashMap<String, WebElement>();
	private WebElement contentPanel;
	private WebElement tabBar;
	private WebElement panelLoader;
	private WebElement statusBar;


	public DynaformDesigner() throws Exception{
		this(Browser.getElement("processDesigner.webElement.newBlankDynaform.designer"));
	}

	public DynaformDesigner(WebElement we) throws Exception{
		WebElement aux = we.findElement(By.cssSelector("#fields_Toolbar"));
		this.titleBar = we.findElement(By.xpath("div[@class='panel_titleBar___processmaker']"));
		this.toolbar.put("save", Browser.getElement("dynaformDesigner.webElement.toolbar.save"));
		this.toolbar.put("save as", Browser.getElement("dynaformDesigner.webElement.toolbar.saveAs"));
		this.toolbar.put("text field", Browser.getElement("dynaformDesigner.webElement.toolbar.textField"));
		this.toolbar.put("currency", Browser.getElement("dynaformDesigner.webElement.toolbar.currency"));
		this.toolbar.put("percentage", Browser.getElement("dynaformDesigner.webElement.toolbar.percentage"));
		this.toolbar.put("password", Browser.getElement("dynaformDesigner.webElement.toolbar.password"));
		this.toolbar.put("suggest", Browser.getElement("dynaformDesigner.webElement.toolbar.suggest"));
		this.toolbar.put("text area", Browser.getElement("dynaformDesigner.webElement.toolbar.textArea"));
		this.toolbar.put("title", Browser.getElement("dynaformDesigner.webElement.toolbar.title"));
		this.toolbar.put("subtitle", Browser.getElement("dynaformDesigner.webElement.toolbar.subtitle"));
		this.toolbar.put("button", Browser.getElement("dynaformDesigner.webElement.toolbar.button"));
		this.toolbar.put("submit", Browser.getElement("dynaformDesigner.webElement.toolbar.submit"));
		this.toolbar.put("reset", Browser.getElement("dynaformDesigner.webElement.toolbar.reset"));
		this.toolbar.put("dropdown", Browser.getElement("dynaformDesigner.webElement.toolbar.dropdown"));
		this.toolbar.put("yes no", Browser.getElement("dynaformDesigner.webElement.toolbar.yesNo"));
		this.toolbar.put("listbox", Browser.getElement("dynaformDesigner.webElement.toolbar.listbox"));
		this.toolbar.put("checkbox", Browser.getElement("dynaformDesigner.webElement.toolbar.checkbox"));
		this.toolbar.put("checkgroup", Browser.getElement("dynaformDesigner.webElement.toolbar.checkgroup"));
		this.toolbar.put("radiogroup", Browser.getElement("dynaformDesigner.webElement.toolbar.radiogroup"));
		this.toolbar.put("date", Browser.getElement("dynaformDesigner.webElement.toolbar.date"));
		this.toolbar.put("hidden", Browser.getElement("dynaformDesigner.webElement.toolbar.hidden"));
		this.toolbar.put("link", Browser.getElement("dynaformDesigner.webElement.toolbar.link"));
		this.toolbar.put("file", Browser.getElement("dynaformDesigner.webElement.toolbar.file"));
		this.toolbar.put("javascript", Browser.getElement("dynaformDesigner.webElement.toolbar.javascript"));
		this.toolbar.put("grid", Browser.getElement("dynaformDesigner.webElement.toolbar.grid"));
	}
/*
	private void addControl(String control, ControlOptions options){
		this.toolbar.get(control).click();
		options.fillForm();
		Browser.getElement("dynaformDesigner.webElement.blankDynaformModal.submitButton").click();
	}*/

	private void setNames(String fieldname, String label) throws Exception{
		if(fieldname == null || fieldname.trim().equals(""))
			throw new Exception("The control name must be specified");
		Browser.getElement("dynaformDesigner.webElement.blankDynaformModal.fieldName").sendKeys(fieldname);
		Browser.getElement("dynaformDesigner.webElement.blankDynaformModal.label").sendKeys(label);
	}

	private void sendModalForm() throws Exception{
		Browser.getElement("dynaformDesigner.webElement.blankDynaformModal.submitButton").click();
	}

	public void addTextField(String fieldname, String label, TextFieldOptions options) throws Exception{
		this.toolbar.get("text field").click();
		this.setNames(fieldname, label);
		options.fillForm();
		this.sendModalForm();
	}

	public void addDropDown(String fieldname, String label, DropDownOptions options) throws Exception{
		this.toolbar.get("dropdown").click();
		this.setNames(fieldname, label);
		options.fillForm();
		this.sendModalForm();
	}

	public void addTextArea(String fieldname, String label, TextAreaOptions options) throws Exception{
		this.toolbar.get("text area").click();
		this.setNames(fieldname, label);
		options.fillForm();
		this.sendModalForm();
	}

	public void addPassword(String fieldname, String label, PasswordOptions options) throws Exception{
		this.toolbar.get("password").click();
		this.setNames(fieldname, label);
		options.fillForm();
		this.sendModalForm();
	}

	public void addSuggest(String fieldname, String label, SuggestOptions options) throws Exception{
		this.toolbar.get("suggest").click();
		this.setNames(fieldname, label);
		options.fillForm();
		this.sendModalForm();
	}

	public void addCurrency(String fieldname, String label, NumericOptions options) throws Exception{
		this.toolbar.get("currency").click();
		this.setNames(fieldname, label);
		options.fillForm();
		this.sendModalForm();
	}

	public void addPercentage(String fieldname, String label, NumericOptions options) throws Exception{
		this.toolbar.get("percentage").click();
		this.setNames(fieldname, label);
		options.fillForm();
		this.sendModalForm();
	}

	public void addButton(String fieldname, String label, String javascript) throws Exception{
		this.toolbar.get("button").click();
		this.setNames(fieldname, label);
		Browser.getElement("dynaformDesigner.webElement.blankDynaformModal.javascriptToExecuteOnClickButton").sendKeys(javascript);
		this.sendModalForm();
	}

	public void addSubmit(String fieldname, String label, String javascript) throws Exception{
		this.toolbar.get("submit").click();
		this.setNames(fieldname, label);
		Browser.getElement("dynaformDesigner.webElement.blankDynaformModal.javascriptToExecuteOnClickButton").sendKeys(javascript);
		this.sendModalForm();
	}

	public void addReset(String fieldname, String label) throws Exception{
		this.toolbar.get("reset").click();
		this.setNames(fieldname, label);
		this.sendModalForm();
	}

	public void addTitle(String fieldname, String label, Boolean enableHTML) throws Exception{
		this.toolbar.get("title").click();
		this.setNames(fieldname, label);
		WebElement we = Browser.getElement("dynaformDesigner.webElement.blankDynaformModal.enableHTML");
		if(enableHTML != we.isSelected())
			we.click();
		this.sendModalForm();
	}

	public void addSubtitle(String fieldname, String content, Boolean enableHTML) throws Exception{
		this.toolbar.get("title").click();
		this.setNames(fieldname, content);
		WebElement we = Browser.getElement("dynaformDesigner.webElement.blankDynaformModal.enableHTML");
		if(enableHTML != we.isSelected())
			we.click();
		this.sendModalForm();
	}

	public void addYesNo(String fieldname, String label, YesNoOptions options) throws Exception{
		this.toolbar.get("yes no").click();
		this.setNames(fieldname, label);
		options.fillForm();
		this.sendModalForm();
	}

	public void addListBox(String fieldname, String label, ListBoxOptions options) throws Exception{
		this.toolbar.get("listbox").click();
		this.setNames(fieldname, label);
		options.fillForm();
		this.sendModalForm();
	}

	public Boolean inPage() throws Exception{
		return (Browser.driver().getCurrentUrl().indexOf(ConfigurationSettings.getInstance().getSetting("dynaformDesigner.idURL")) >= 0);
	}

	public Boolean inPage(String formName) throws Exception{
		if(Browser.getElements("dynaformDesigner.formNameLocation").size() == 0)
			return false;
		return (Browser.getElement("dynaformDesigner.formNameLocation").getText().trim().equals(formName.trim())) && this.inPage() ;
	}
/*
	public void addJavascript(String fieldname, String script){
		this.toolbar.get("title").click();
		if(fieldname == null || fieldname.trim().equals(""))
			throw new Exception("The control name must be specified");
		Browser.getElement("dynaformDesigner.webElement.blankDynaformModal.fieldName").sendKeys(fieldname);
		Browser.getElement("dynaformDesigner.webElement.blankDynaformModal.script").sendKeys(script);
		this.sendModalForm();
	}*/

	public Boolean save() throws Exception{
		this.toolbar.get("save").click();
        Alert alert = Browser.driver().switchTo().alert();
        Boolean res = alert.getText().equals("DynaForm is now saved");
        alert.accept();
        Browser.driver().switchTo().defaultContent();
        Browser.driver().switchTo().frame("frameMain");
        return res;
	}

	public void saveAs(String title, String description) throws Exception{
		this.toolbar.get("save as").click();
		Browser.getElement("dynaformDesigner.webElement.blankDynaformModal.saveAs.title").sendKeys(title);
		Browser.getElement("dynaformDesigner.webElement.blankDynaformModal.saveAs.description").sendKeys(description);
		Browser.getElement("dynaformDesigner.webElement.blankDynaformModal.saveAs.saveButton").click();
	}

}