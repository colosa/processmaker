package com.colosa.qa.automatization.pages;

import java.util.List;
import java.util.ArrayList;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.By;
import org.openqa.selenium.Keys;
import org.openqa.selenium.interactions.Actions;
import org.openqa.selenium.JavascriptExecutor;
import org.openqa.selenium.support.ui.Select;
import com.colosa.qa.automatization.common.*;
import com.colosa.qa.automatization.common.extJs.*;
import java.io.FileNotFoundException;
import java.io.IOException;
import java.net.URL;

public class CronExecute{

	public static void execute() throws FileNotFoundException, IOException, Exception{

		String wd = "/opt/processmaker/workflow/engine/bin/cron.php";

		Browser.gotoUrl("http://192.168.11.130/cron_exec.php");
		


		/*Process p = Runtime.getRuntime().exec("ssh root@192.168.11.130");
		p = Runtime.getRuntime().exec("qatest+-");
		p = Runtime.getRuntime().exec("php -f /opt/processmaker/workflow/engine/bin/cron.php workflow");
		p = Runtime.getRuntime().exec("exit");*/

		/*ProcessBuilder pb = new ProcessBuilder("php", "-f", "192.168.11.130/opt/processmaker/workflow/engine/bin/cron.php workflow");
		Process p = pb.start();*/

	}


}