package com.colosa.qa.automatization.common.controlOptions.selection;

class DropDownOption{
	
	private String key = null;
	private String value = null;

	public DropDownOption(String key, String value){
		this.key = key;
		this.value = value;
	}

	public String getKey(){
		return this.key;
	}

	public void setValue(String value){
		this.value = value;
	}

	public String getValue(){
		return this.value;
	}

}