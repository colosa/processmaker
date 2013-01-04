package com.colosa.qa.automatization.common.controlOptions;

import java.util.ArrayList;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.ui.Select;
import com.colosa.qa.automatization.common.Browser;

public class DependentFieldsApplicableOption implements DependentFieldsApplicableBehavior{

	public void fillDependentFields(ArrayList<String> fields) throws Exception{
		if(fields == null)
			return;
		Select we = new Select(Browser.getElement("dynaformDesigner.webElement.blankDynaformModal.dependentFields"));
		for(String fieldName:fields)
		{
			we.selectByVisibleText(fieldName);
		}
	}
	
}