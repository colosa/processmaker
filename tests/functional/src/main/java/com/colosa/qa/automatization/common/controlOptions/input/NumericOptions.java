package com.colosa.qa.automatization.common.controlOptions.input;

import com.colosa.qa.automatization.common.Browser;
import org.openqa.selenium.support.ui.Select;

public class NumericOptions extends InputControlOptions{

	private Validate validate = Validate.REAL_NUMBER;
	private DecimalSeparator decimalSeparator = DecimalSeparator.PERIOD;
	private String formula = null;

	public enum Validate{
		INTEGER("Int"), REAL_NUMBER("Real");

		private String value;

		Validate(String value){
			this.value = value;
		}

		public String getValue(){
			return this.value;
		}
	};

	public enum DecimalSeparator{
		PERIOD("."), COMMA(",");

		private String value;

		DecimalSeparator(String value){
			this.value = value;
		}

		public String getValue(){
			return this.value;
		}
	}

	public void setMask(String mask){
		this.mask = mask;
	}

	public void setSize(int i){
		super.setSize(i);
	}

	public void setFormula(String formula){
		this.formula = formula;
	}

	public void setValidate(Validate validate){
		this.validate = validate;
	}

	public void setDecimalSeparator(DecimalSeparator decimalSeparator){
		this.decimalSeparator = decimalSeparator;
	}

	public void setDefaultValue(String defaultValue){
		this.defaultValue = defaultValue;
	}

	public void fillForm() throws Exception{
		super.fillForm();
		new Select(Browser.getElement("dynaformDesigner.webElement.blankDynaformModal.validate")).selectByValue(this.validate.getValue());
		new Select(Browser.getElement("dynaformDesigner.webElement.blankDynaformModal.decimalSeparator")).selectByValue(this.decimalSeparator.getValue());
		Browser.getElement("dynaformDesigner.webElement.blankDynaformModal.formula").sendKeys(this.formula);
	}

}