package com.colosa.qa.automatization.common.controlOptions.selection;

import com.colosa.qa.automatization.common.Browser;
import com.colosa.qa.automatization.common.controlOptions.ControlOptions;
import com.colosa.qa.automatization.common.controlOptions.selection.DropDownOption;
import com.colosa.qa.automatization.common.controlOptions.DependentFieldsApplicableOption;
import java.util.Map;
import java.util.HashMap;
import java.util.ArrayList;

public class DropDownOptions extends ControlOptions{

	private ArrayList<DropDownOption> options = new ArrayList<DropDownOption>();

	public DropDownOptions(){
		this.dependentFieldsApplicableBehavior = new DependentFieldsApplicableOption();
	}

	public void addOption(String label, String value){
		options.add(new DropDownOption(label, value));
	}

	public void addDependentField(String fieldName){
		super.addDependentField(fieldName);
	}

	public void setDefaultValue(String value){
		this.defaultValue = value;
	}

	@Override
	public void fillForm() throws Exception{
		super.fillForm();
		int i = 1;
		for(DropDownOption opt:this.options)
		{
			if(i>1)
				Browser.getElement("dynaformDesigner.webElement.dropdownModal.gridNewElementButton").click();
			Browser.getElementf("dynaformDesigner.webElement.dropdownModal.gridValueElementLocator", i).sendKeys(opt.getValue());
			Browser.getElementf("dynaformDesigner.webElement.dropdownModal.gridLabelElementLocator", i).sendKeys(opt.getKey());
			i++;
		}
	}
	
}