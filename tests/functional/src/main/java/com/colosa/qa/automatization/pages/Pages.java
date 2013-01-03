package com.colosa.qa.automatization.pages;

public class Pages{

	public static Login Login() throws Exception{
		Login loginPage = new Login();
		
		return loginPage;
	}

	public static Main Main() throws Exception{
		Main mainPage = new Main();

		return mainPage;
	}

	public static Home Home() throws Exception{
		Home page = new Home();

		return page;
	}

	public static Admin Admin() throws Exception{
		Admin page = new Admin();

		return page;
	}

	public static Documents Documents() throws Exception{
		Documents document = new Documents();

		return document;
	}

	public static ProcessDesigner ProcessDesigner() throws Exception{
		ProcessDesigner processDesigner = new ProcessDesigner();

		return processDesigner;
	}

	public static ProcessList ProcessList() throws Exception{
		ProcessList processList = new ProcessList();

		return processList;
	}

	public static InputDocProcess InputDocProcess() throws Exception{
		InputDocProcess inputDocProcess = new InputDocProcess();

		return inputDocProcess;
	}

	public static OutputDocProcess OutputDocProcess() throws Exception{
		OutputDocProcess outputDocProcess = new OutputDocProcess();

		return outputDocProcess;
	}	

	public static InputDocumentList InputDocumentList() throws Exception{
		InputDocumentList inputdDocList = new InputDocumentList();

		return inputdDocList;
	}

	public static OutputDocumentList OutputDocumentList() throws Exception{
		OutputDocumentList outputdDocList = new OutputDocumentList();

		return outputdDocList;
	}

	public static DynaformDesigner DynaformDesigner() throws Exception{
		
		return new DynaformDesigner();
	}

	public static TriggersProcess TriggersProcess() throws Exception{
		TriggersProcess triggerProcess = new TriggersProcess();

		return triggerProcess;
	}

	public static Designer Designer() throws Exception{
		Designer designer = new Designer();

		return designer;
	}	

	public static TaskProperties TaskProperties() throws Exception{
		TaskProperties taskProperties = new TaskProperties();

		return taskProperties;
	}

	public static DynaformExecution DynaformExecution() throws Exception{
		DynaformExecution dynaformExecution = new DynaformExecution();

		return dynaformExecution;
	}
}