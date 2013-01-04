package com.colosa.qa.automatization.common.controlOptions.input;

import java.util.ArrayList;
import org.openqa.selenium.WebElement;
import com.colosa.qa.automatization.common.Browser;
import com.colosa.qa.automatization.common.controlOptions.ControlOptions;
import org.openqa.selenium.support.ui.Select;

class InputControlOptions extends ControlOptions{
	
	private ArrayList<Integer> size = new ArrayList<Integer>();
	protected String validate = null;
	protected String mask = null;
	protected int maxLength = -1;

	protected void fillForm() throws Exception{
		super.fillForm();
		this.fillSize();
		this.fillValidate();
		this.fillMask();
		this.fillMaxLength();
	}

	protected void setSize(int... args){
		this.size.clear();
		for(int i: args)
			this.size.add(i);
	}

	private void fillSize() throws Exception{
		if(this.size.size()==1)
		{
			WebElement we =	Browser.getElement("dynaformDesigner.webElement.blankDynaformModal.size");
			we.clear();
			we.sendKeys(Integer.toString(this.size.get(0)));
		}
		else if(this.size.size()==2)
		{
			WebElement we =	Browser.getElement("dynaformDesigner.webElement.blankDynaformModal.columns");
			we.clear();
			we.sendKeys(Integer.toString(this.size.get(0)));
			we = Browser.getElement("dynaformDesigner.webElement.blankDynaformModal.rows");
			we.clear();
			we.sendKeys(Integer.toString(this.size.get(1)));
		}
	}

	private void fillValidate() throws Exception{
		if(this.validate == null)
			return;
		Select we = new Select(Browser.getElement("dynaformDesigner.webElement.blankDynaformModal.validate"));
		we.selectByValue(this.validate);
	}

	private void fillMask() throws Exception{
		if(this.mask == null)
			return;
		WebElement we =	Browser.getElement("dynaformDesigner.webElement.blankDynaformModal.mask");
		we.clear();
		we.sendKeys(this.mask);
	}

	private void fillMaxLength() throws Exception{
		if(this.maxLength < 0)
			return;
		WebElement we = Browser.getElement("dynaformDesigner.webElement.blankDynaformModal.maxLength");
		we.clear();
		we.sendKeys(Integer.toString(this.maxLength));
	}

}