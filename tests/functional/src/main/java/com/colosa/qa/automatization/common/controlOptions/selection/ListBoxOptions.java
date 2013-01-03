package com.colosa.qa.automatization.common.controlOptions.selection;

import org.openqa.selenium.WebElement;
import com.colosa.qa.automatization.common.Browser;
import com.colosa.qa.automatization.common.controlOptions.selection.DropDownOptions;
import com.colosa.qa.automatization.common.controlOptions.DependentFieldsNotApplicableOption;

public class ListBoxOptions extends DropDownOptions{

	private int size = 4;

	public ListBoxOptions(){

	}

	//protected void setReadOnly(Boolean readOnly){}

	public void setSize(int size){
		this.size = size;
	}

	@Override
	public void fillForm() throws Exception{
		this.dependentFieldsApplicableBehavior = new DependentFieldsNotApplicableOption();;
		this.readOnly = null;
		super.fillForm();
		WebElement we = Browser.getElement("dynaformDesigner.webElement.blankDynaformModal.size");
		we.clear();
		we.sendKeys(Integer.toString(this.size));
	}
	
}