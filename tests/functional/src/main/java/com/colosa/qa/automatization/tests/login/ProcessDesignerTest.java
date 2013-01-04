package com.colosa.qa.automatization.tests.login;

import java.lang.Exception;
import org.junit.Assert;
import org.junit.Test;
import org.openqa.selenium.By;
import com.colosa.qa.automatization.pages.Home;
import com.colosa.qa.automatization.pages.Pages;
import com.colosa.qa.automatization.pages.ProcessDesigner;
import com.colosa.qa.automatization.common.*;
import com.colosa.qa.automatization.pages.DynaformDesigner;
import com.colosa.qa.automatization.common.controlOptions.input.*;
import com.colosa.qa.automatization.common.controlOptions.selection.*;
import com.colosa.qa.automatization.common.controlOptions.ControlOptions;

public class ProcessDesignerTest{

    @Test
    public void inDesigner() throws Exception{
        Pages.Login().gotoUrl();
        Pages.Login().loginDefaultUser();
        Pages.Main().goDesigner();

        Pages.ProcessList().newProcess("_newTestProcess "+new java.util.Date().toString(), "just another test process");

        Assert.assertTrue(Pages.ProcessDesigner().inPage());
        //Assert.assertTrue(Pages.ProcessDesigner().inPage(<process name>));
    }

    @Test
    public void inDynaformDesigner() throws Exception{
        Pages.ProcessDesigner().newBlankDynaform("new form", ProcessDesigner.DynaformSubType.NORMAL);

        //Assert.assertTrue(Pages.DynaformDesigner().inPage());
        Assert.assertTrue(Pages.DynaformDesigner().inPage("new form"));

        Pages.DynaformDesigner().addTitle("myTitle", "<div style=\"text-align:center\"><img src=\"http://www.colosa.com/images/colosa-logo.png\" /></div>", true);
        Pages.DynaformDesigner().addSubtitle("mySubtitle", "Building Business Process Management, Modeling and Analysis Tools that are simple to use and deliver immediate results.", false);
    }

    @Test
    public void textFieldCreated()throws Exception{
        Options.getInstance().textFieldOptions().setRequired(true);
        Options.getInstance().textFieldOptions().setMaxLength(10);
        Options.getInstance().textFieldOptions().setValidate(TextFieldOptions.Validate.ALPHA_NUMERIC);
        Options.getInstance().textFieldOptions().setMask("");
        Options.getInstance().textFieldOptions().setTextTransform(TextFieldOptions.TextTransform.UPPER_CASE);
        Options.getInstance().textFieldOptions().setReadOnly(false);
        Options.getInstance().textFieldOptions().setDefaultValue("your name here");
        Options.getInstance().textFieldOptions().setHint("Enter a integer");
        Options.getInstance().textFieldOptions().setSize(10);
        Options.getInstance().textFieldOptions().setMode(ControlOptions.Mode.EDIT);
        Pages.DynaformDesigner().addTextField(ConfigurationSettings.getInstance().getSetting("textField.name"), "Enter a text", Options.getInstance().textFieldOptions());

        Assert.assertTrue(Browser.elementExists("textField.id"));
    }

    @Test
    public void numericFieldsCreated() throws Exception{
        Options.getInstance().numericOptions().setValidate(NumericOptions.Validate.REAL_NUMBER);
        Options.getInstance().numericOptions().setDecimalSeparator(NumericOptions.DecimalSeparator.PERIOD);
        Options.getInstance().numericOptions().setMask("");
        Options.getInstance().numericOptions().setRequired(true);
        Options.getInstance().numericOptions().setReadOnly(false);
        Options.getInstance().numericOptions().setDefaultValue("0");
        Options.getInstance().numericOptions().setHint("enter a real number");
        Options.getInstance().numericOptions().setFormula("a formula");
        Pages.DynaformDesigner().addCurrency(ConfigurationSettings.getInstance().getSetting("currency.name"), "enter your salary", Options.getInstance().numericOptions());
        Options.getInstance().numericOptions().setMask("##,##");
        Pages.DynaformDesigner().addPercentage(ConfigurationSettings.getInstance().getSetting("percentage.name"), "enter a percentage", Options.getInstance().numericOptions());

        Assert.assertTrue(Browser.elementExists("currency.id") && Browser.elementExists("percentage.id"));
    }

    @Test
    public void correctPasswordLength() throws Exception{
        Options.getInstance().passwordOptions().setMaxLength(16);
        Options.getInstance().passwordOptions().setRequired(false);
        Options.getInstance().passwordOptions().setAutocomplete(true);
        Options.getInstance().passwordOptions().setReadOnly(false);
        Options.getInstance().passwordOptions().setHint("enter a strong password please");
        Options.getInstance().passwordOptions().setSize(Integer.parseInt(ConfigurationSettings.getInstance().getSetting("password.size")));
        Pages.DynaformDesigner().addPassword(ConfigurationSettings.getInstance().getSetting("password.name"), "enter yout password", Options.getInstance().passwordOptions());

        Assert.assertEquals(Browser.getElement("password.id").getAttribute("size"), ConfigurationSettings.getInstance().getSetting("password.size"));
    }

    @Test
    public void correctDropdownOptionsNumber() throws Exception{
        //create some other controls
        Options.getInstance().suggestOptions().setRequired(false);
        Options.getInstance().suggestOptions().addDependentField("myText");
        Options.getInstance().suggestOptions().setSaveSelectedOptionAs(SuggestOptions.SaveSelectedOptionAs.VALUE);
        Options.getInstance().suggestOptions().setMaxResults(3);
        Options.getInstance().suggestOptions().setShowNoResultsMessage(true);
        Options.getInstance().suggestOptions().setSQL("select ID from TABLAX");
        Options.getInstance().suggestOptions().enableStoreNewEntry("TABLAX", "ID", SuggestOptions.StoreNewEntryPrimaryKeyType.INTEGER);
        Pages.DynaformDesigner().addSuggest("mySuggest", "ingrese un texto", Options.getInstance().suggestOptions());

        Options.getInstance().textAreaOptions().setSize(20, 10);
        Pages.DynaformDesigner().addTextArea("myTextArea", "comment here", Options.getInstance().textAreaOptions());

        Options.getInstance().dropDownOptions().setRequired(true);
        Options.getInstance().dropDownOptions().setSQL("some sql sentence for this dropdown");
        Options.getInstance().dropDownOptions().addDependentField("myText");
        Options.getInstance().dropDownOptions().addOption("Argentina", "arg");
        Options.getInstance().dropDownOptions().addOption("Bolivia", "bol");
        Options.getInstance().dropDownOptions().addOption("Brasil", "bra");
        Pages.DynaformDesigner().addDropDown("myDropdown", "seleccione un país", Options.getInstance().dropDownOptions());

        Assert.assertEquals(Browser.getElements("dropdown.optionsLocator").size(), Integer.parseInt(ConfigurationSettings.getInstance().getSetting("dropdown.optionsNumber")));
    }

    @Test
    public void requiredPropertyTest() throws Exception{
        Options.getInstance().yesNoOptions().setDefaultValue(YesNoOptions.Options.NO);
        Options.getInstance().yesNoOptions().setHint("the YesNo hint");
        Pages.DynaformDesigner().addYesNo("myYesNo", "underage", Options.getInstance().yesNoOptions());

        Options.getInstance().listBoxOptions().setRequired(true);
        Options.getInstance().listBoxOptions().setDefaultValue("hola");
        Options.getInstance().listBoxOptions().setHint("Hint for list box");
        Options.getInstance().listBoxOptions().setSize(3);
        Options.getInstance().listBoxOptions().setMode(ControlOptions.Mode.EDIT);
        Options.getInstance().listBoxOptions().setSQL("a SQL command");
        Options.getInstance().listBoxOptions().setSQLConnection(ControlOptions.SQLConnection.DBARRAY);
        Options.getInstance().listBoxOptions().addOption("Argentina", "arg");
        Options.getInstance().listBoxOptions().addOption("Bolivia", "bol");
        Options.getInstance().listBoxOptions().addOption("Brasil", "bra");
        Pages.DynaformDesigner().addListBox("myListBox", "country", Options.getInstance().listBoxOptions());

        Pages.DynaformDesigner().addButton("myButton", "click me!", "alert(\"hola\");");
        Pages.DynaformDesigner().addReset("myReset", "restore");
        Pages.DynaformDesigner().addSubmit(ConfigurationSettings.getInstance().getSetting("submit.name"), ConfigurationSettings.getInstance().getSetting("submit.value"), "");

        Assert.assertEquals(Browser.getElement("submit.id").getAttribute("value"), ConfigurationSettings.getInstance().getSetting("submit.expectedValue"));
    }

    @Test
    public void dynaformSaved() throws Exception{
        Assert.assertTrue(Pages.DynaformDesigner().save());
    }
}