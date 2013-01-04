package com.colosa.qa.automatization.common.controlOptions;

import java.util.ArrayList;
import com.colosa.qa.automatization.common.Browser;
import org.openqa.selenium.support.ui.Select;
import org.openqa.selenium.WebElement;

public class ControlOptions{

	//protected String fieldname = null;
	//protected String label = "";
	protected Boolean required = false;
	protected Boolean readOnly = false; 
	private ArrayList<String> dependentFields = new ArrayList<String>();
	protected String defaultValue = null;
	protected String hint = "";
	public Mode mode = Mode.EDIT;
	protected SQLConnection sqlConnection = null; 
	protected String sql = null;
	protected DependentFieldsApplicableBehavior dependentFieldsApplicableBehavior;

	public enum Mode{
		EDIT("edit"),
		VIEW("view");

		private String value;

		Mode(String value){
			this.value = value;
		}

		public String getValue(){
			return this.value;
		}
	}

	public enum SQLConnection{
		NONE(""),
		DBARRAY("dbarray"),
		WORKFLOW("workflow"),
		RBAC("rbac"),
		REPORT("rp");

		private String value;

		SQLConnection(String value){
			this.value = value;
		}

		public String getValue(){
			return this.value;
		}
	}
	
	public ControlOptions(){
		this.dependentFieldsApplicableBehavior = new DependentFieldsNotApplicableOption();
		this.sqlConnection = SQLConnection.NONE;
	}

	protected void fillForm() throws Exception{
		Select ddown = null;
		this.fillRequired();
		this.fillReadOnly();
		this.fillDependentFields();
		this.fillDefaultValue();
		Browser.getElement("dynaformDesigner.webElement.blankDynaformModal.hint").sendKeys(this.hint);
		ddown = new Select(Browser.getElement("dynaformDesigner.webElement.blankDynaformModal.mode"));
		ddown.selectByValue(this.mode.getValue());
		this.fillSQLConnection();
		this.fillSQL();
	}

	protected void addDependentField(String fieldname){
		this.dependentFields.add(fieldname);
	}
/*
	public void setFieldName(String fieldname){
		this.fieldname = fieldname;
	}

	public void setLabel(String label){
		this.label = label;
	}*/

	public void setSQL(String sql){
		this.sql = sql;
	}

	public void setSQLConnection(SQLConnection connection){
		this.sqlConnection = connection;
	}

	public void setReadOnly(Boolean readOnly){
		this.readOnly = readOnly;
	}

	public void setRequired(Boolean required){
		this.required = required;
	}

	public void setHint(String hint){
		this.hint = hint;
	}

	public void setMode(Mode mode){
		this.mode = mode;
	}	

	private void fillRequired() throws Exception{
		if(this.required == null)
			return;
		WebElement we = null;
		we = Browser.getElement("dynaformDesigner.webElement.blankDynaformModal.required"); 
		if(this.required != we.isSelected())
			we.click();

	}

	private void fillDependentFields() throws Exception{
		if(this.dependentFields != null)
			this.dependentFieldsApplicableBehavior.fillDependentFields(this.dependentFields);
	}

	protected void fillDefaultValue() throws Exception{
		if(this.defaultValue == null)
			return;
		Browser.getElement("dynaformDesigner.webElement.blankDynaformModal.defaultValue").sendKeys(this.defaultValue);
	}

	private void fillSQLConnection() throws Exception{
		if(this.sqlConnection == null)
			return;
		(new Select(Browser.getElement("dynaformDesigner.webElement.blankDynaformModal.sqlConnection"))).selectByValue(this.sqlConnection.getValue());
	}

	private void fillSQL() throws Exception{
		if(this.sql == null)
			return;
		Browser.getElement("dynaformDesigner.webElement.blankDynaformModal.sql").sendKeys(this.sql);
	}

	private void fillReadOnly() throws Exception{
		if(this.readOnly == null)
			return;
		WebElement we = Browser.getElement("dynaformDesigner.webElement.blankDynaformModal.readOnly");
		if(this.readOnly != we.isSelected())
			we.click();
	}

}