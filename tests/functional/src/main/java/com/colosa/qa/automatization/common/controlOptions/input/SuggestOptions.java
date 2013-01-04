package com.colosa.qa.automatization.common.controlOptions.input;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.ui.Select;
import com.colosa.qa.automatization.common.Browser;
import com.colosa.qa.automatization.common.controlOptions.input.InputControlOptions;

public class SuggestOptions extends InputControlOptions{

	private SaveSelectedOptionAs saveSelectedOptionAs = SaveSelectedOptionAs.LABEL;
	private StoreNewEntryPrimaryKeyType storeNewEntryPrimaryKeyType;
	private int maxResults = 6;
	private Boolean showNoResultsMessage = false;
	private Boolean storeNewEntry = false;
	private String javascript = "";
	private String table = "0";
	private String primaryKey = "0";

	public enum SaveSelectedOptionAs{
		LABEL("1"), VALUE("0");

		private String value;

		SaveSelectedOptionAs(String value){
			this.value = value;
		}

		public String getValue(){
			return this.value;
		}
	}

	public enum StoreNewEntryPrimaryKeyType{
		NONE("0"), INTEGER("int"), VARCHAR("varchar");

		private String value;

		StoreNewEntryPrimaryKeyType(String value){
			this.value = value;
		}

		public String getValue(){
			return this.value;
		}
	}

	public SuggestOptions(){
		this.readOnly = null;
	}
		
	public void setSize(int i){
		super.setSize(i);
	}

	public void setSaveSelectedOptionAs(SaveSelectedOptionAs saveAs){
		this.saveSelectedOptionAs = saveAs;
	}

	public void setMaxResults(int i){
		this.maxResults = i;
	}
	public void setShowNoResultsMessage(Boolean bln){
		this.showNoResultsMessage = bln;
	}
	public void enableStoreNewEntry(String table, String primaryKey, StoreNewEntryPrimaryKeyType type){
		this.storeNewEntry = true;
		this.table = table;
		this.primaryKey = primaryKey;
		this.storeNewEntryPrimaryKeyType = type;
	}

	public void disableStoreNewEntry(){
		this.storeNewEntry = false;
		this.table = "0";
		this.primaryKey = "0";
		this.storeNewEntryPrimaryKeyType = StoreNewEntryPrimaryKeyType.NONE;
	}

	public void setJavascript(String script){
		this.javascript = script;
	}

	public void addDependentField(String fieldName){
		super.addDependentField(fieldName);
	}
	
	public void fillForm() throws Exception{
		super.fillForm();
		(new Select(Browser.getElement("dynaformDesigner.webElement.blankDynaformModal.saveSelectedOptionAs"))).selectByValue(this.saveSelectedOptionAs.getValue());
		Browser.getElement("dynaformDesigner.webElement.blankDynaformModal.maxResults").sendKeys(Integer.toString(this.maxResults));
		WebElement we = Browser.getElement("dynaformDesigner.webElement.blankDynaformModal.showNoResultsMessage");
		if(this.showNoResultsMessage != we.isSelected())
			we.click();
		we = Browser.getElement("dynaformDesigner.webElement.blankDynaformModal.storeNewEntry");
		if(we.isSelected() != this.storeNewEntry)
			we.click();
		(new Select(Browser.getElement("dynaformDesigner.webElement.blankDynaformModal.storeNewEntry.table"))).selectByValue(this.table);
		(new Select(Browser.getElement("dynaformDesigner.webElement.blankDynaformModal.storeNewEntry.primaryKey"))).selectByValue(this.primaryKey);
		(new Select(Browser.getElement("dynaformDesigner.webElement.blankDynaformModal.storeNewEntry.primaryKeyType"))).selectByValue(storeNewEntryPrimaryKeyType.getValue());
		Browser.getElement("dynaformDesigner.webElement.blankDynaformModal.javascriptCallback").sendKeys(this.javascript);
	}

}