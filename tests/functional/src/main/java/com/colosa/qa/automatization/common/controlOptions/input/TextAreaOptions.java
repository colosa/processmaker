package com.colosa.qa.automatization.common.controlOptions.input;

public class TextAreaOptions extends InputControlOptions{

	public void fillForm() throws Exception{
		super.fillForm();
	}

	public void setSize(int columns, int rows){
		super.setSize(columns, rows);
	}

	public void setSize(int i){
		this.setSize(i, i);
	}
	
}