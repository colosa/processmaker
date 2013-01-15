package com.colosa.qa.automatization.common.controlOptions.selection;

import org.openqa.selenium.support.ui.Select;
import com.colosa.qa.automatization.common.controlOptions.ControlOptions;
import com.colosa.qa.automatization.common.Browser;

public class YesNoOptions extends ControlOptions{
	
	public enum Options{
		YES("1"), NO("");

		private String value;

		Options(String value){
			this.value = value;
		}

		public String getValue(){
			return this.value;
		}
	}

	public void setDefaultValue(Options option){
		this.defaultValue = option.getValue();
	}

	@Override
	protected void fillDefaultValue() throws Exception{
		new Select(Browser.getElement("dynaformDesigner.webElement.blankDynaformModal.defaultValue")).selectByValue(this.defaultValue);
	}

	private void setRequired(){}

	private void setSQL(/* String sql */){}

	private void setSQLConnection(/*SQLConnection connection*/){}

	public void fillForm() throws Exception{
		this.sqlConnection = null;
		this.required = null;
		super.fillForm();
	}
}