package com.colosa.qa.automatization.common;

import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.chrome.ChromeDriver;
import org.openqa.selenium.firefox.FirefoxDriver;
import org.openqa.selenium.htmlunit.HtmlUnitDriver;
import org.openqa.selenium.ie.InternetExplorerDriver;
import org.openqa.selenium.By;
import org.openqa.selenium.support.ui.WebDriverWait;
import org.openqa.selenium.support.ui.ExpectedConditions;
import org.openqa.selenium.Platform;
import org.openqa.selenium.remote.DesiredCapabilities;
import org.openqa.selenium.remote.RemoteWebDriver;
import java.net.URL;
import java.io.FileNotFoundException;
import java.io.IOException;

public class TaskFieldData{
	public String taskName = "";
	public String title = "";
	public String description = "";
	public String casePriority = "";	
	public String routingScreenTemplate = "- Default -";
	public Boolean startingTask = false;
	public String caseAssignedBy = "";
	public Boolean allowUserDefined = false;
	public String taskDuration = "";
	public String timeUnit = "";
	public String countDays = "";
	public String calendar = "";
	public Boolean allowArbitrary = false;
	public String caseTitle = "";
	public String caseDescription = "";
	public Boolean afterRouting = false;
	public String subject = "";
	public String contentType = "";
	public String message = "";

	public TaskFieldData(){

	}

}