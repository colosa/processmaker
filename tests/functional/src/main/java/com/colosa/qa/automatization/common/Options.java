package com.colosa.qa.automatization.common;

import com.colosa.qa.automatization.common.controlOptions.input.*;
import com.colosa.qa.automatization.common.controlOptions.selection.*;

public class Options{
	
	private static Options INSTANCE = null;
	private TextFieldOptions _textFieldOptions = null;
	private NumericOptions _numericOptions = null;
	private PasswordOptions _passwordOptions = null;
	private SuggestOptions _suggestOptions = null;
	private TextAreaOptions _textAreaOptions = null;
	private DropDownOptions _dropDownOptions = null;
	private YesNoOptions _yesNoOptions = null;
	private ListBoxOptions _listBoxOptions = null;

	public static Options getInstance(){
		if(INSTANCE == null)
			INSTANCE = new Options();
		return INSTANCE;
	}

	public TextFieldOptions textFieldOptions(){
		if(this._textFieldOptions == null)
			this._textFieldOptions = new TextFieldOptions();
		return this._textFieldOptions;
	}

	public NumericOptions numericOptions(){
		if(this._numericOptions == null)
			this._numericOptions = new NumericOptions();
		return this._numericOptions;
	}

	public PasswordOptions passwordOptions(){
		if(this._passwordOptions == null)
			this._passwordOptions = new PasswordOptions();
		return this._passwordOptions;
	}

	public SuggestOptions suggestOptions(){
		if(this._suggestOptions == null)
			this._suggestOptions = new SuggestOptions();
		return this._suggestOptions;
	}

	public TextAreaOptions textAreaOptions(){
		if(this._textAreaOptions == null)
			this._textAreaOptions = new TextAreaOptions();
		return this._textAreaOptions;
	}

	public DropDownOptions dropDownOptions(){
		if(this._dropDownOptions == null)
			this._dropDownOptions = new DropDownOptions();
		return this._dropDownOptions;
	}

	public YesNoOptions yesNoOptions(){
		if(this._yesNoOptions == null)
			this._yesNoOptions = new YesNoOptions();
		return this._yesNoOptions;
	}

	public ListBoxOptions listBoxOptions(){
		if(this._listBoxOptions == null)
			this._listBoxOptions = new ListBoxOptions();
		return this._listBoxOptions;
	}

	public void reset(){
		this._textFieldOptions = null;
	}

}