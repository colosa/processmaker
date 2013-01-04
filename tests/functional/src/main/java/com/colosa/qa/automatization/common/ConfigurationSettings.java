package com.colosa.qa.automatization.common;

import java.util.Properties;
import java.io.FileInputStream;
import java.io.FileOutputStream;
import java.io.InputStreamReader;
import java.io.BufferedReader;
import java.io.FileNotFoundException;
import java.io.IOException;

public class ConfigurationSettings{

	private static ConfigurationSettings INSTANCE = null;
	private Properties applicationProperties;
	private String appConfigurationFile;

	private ConfigurationSettings(String defaultConfFile, String applicationConfFile) throws FileNotFoundException, IOException{
		FileInputStream propertiesFile = new FileInputStream(defaultConfFile);
		Properties defaultProperties = new Properties();

		this.appConfigurationFile = applicationConfFile;
		defaultProperties.load(propertiesFile);
		propertiesFile.close();

		this.applicationProperties = new Properties(defaultProperties);
		propertiesFile = new FileInputStream(this.appConfigurationFile);
		this.applicationProperties.load(propertiesFile);
		propertiesFile.close();
	}

	private static void createInstance(String defaultConfFile, String applicationConfFile) throws FileNotFoundException, IOException{
		if(INSTANCE == null)
			INSTANCE = new ConfigurationSettings(defaultConfFile, applicationConfFile);
	}

	public static ConfigurationSettings getInstance() throws FileNotFoundException, IOException{
		ConfigurationSettings.getInstance("default.conf", "app.conf");
		return INSTANCE;
	}

	public static ConfigurationSettings getInstance(String defaultConfFile, String applicationConfFile) throws FileNotFoundException, IOException{
		ConfigurationSettings.createInstance(defaultConfFile, applicationConfFile);
		return INSTANCE;
	}

	public String getSetting(String key){
		return this.applicationProperties.getProperty(key);
	}

	public void setSetting(String key, String value) throws IOException{
		this.setSetting(key, value, null);
	}

	public void setSetting(String key, String value, String comment) throws IOException{
		FileOutputStream fos = new FileOutputStream(this.appConfigurationFile);
		this.applicationProperties.setProperty(key, value);
		this.applicationProperties.store(fos, comment);
		fos.close();
	}
}