package com.colosa.qa.automatization.tests.salesProcess;

import org.junit.Assert;
import org.junit.AfterClass;
import org.junit.Test;

import com.colosa.qa.automatization.pages.*;
import com.colosa.qa.automatization.common.*;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.By;

import java.io.FileNotFoundException;
import java.io.IOException;

public class TestEmploymentApplicationProcess{

	protected static int caseNum;
	protected static int addJob=3;

	@Test
	public void openAndRunProcess()  throws FileNotFoundException, IOException, Exception{

		Pages.Login().gotoUrl();
		Pages.Login().loginUser("hector", "sample", "workflow");
		Pages.Main().goHome();	
		caseNum = Pages.Home().startCase("aa) Employment Application Process v_1 (Candidate CV uploading)");

		FormFieldData[] arrayData = new FormFieldData[23];
		arrayData[0] = new FormFieldData();
		arrayData[1] = new FormFieldData();
		arrayData[2] = new FormFieldData();
		arrayData[3] = new FormFieldData();
		arrayData[4] = new FormFieldData();
		arrayData[5] = new FormFieldData();
		arrayData[6] = new FormFieldData();
		arrayData[7] = new FormFieldData();
		arrayData[8] = new FormFieldData();
		arrayData[9] = new FormFieldData();
		arrayData[10] = new FormFieldData();
		arrayData[11] = new FormFieldData();
		arrayData[12] = new FormFieldData();
		arrayData[13] = new FormFieldData();
		arrayData[14] = new FormFieldData();
		arrayData[15] = new FormFieldData();
		arrayData[16] = new FormFieldData();
		arrayData[17] = new FormFieldData();
		arrayData[18] = new FormFieldData();
		arrayData[19] = new FormFieldData();
		arrayData[20] = new FormFieldData();
		arrayData[21] = new FormFieldData();
		arrayData[22] = new FormFieldData();

		arrayData[0].fieldPath = "form[candidate_firstname]";
		arrayData[0].fieldFindType = FieldKeyType.ID;
		arrayData[0].fieldType = FieldType.TEXTBOX;
		arrayData[0].fieldValue = "Ernesto";
		arrayData[1].fieldPath = "form[candidate_lastname]";
		arrayData[1].fieldFindType = FieldKeyType.ID;
		arrayData[1].fieldType = FieldType.TEXTBOX;
		arrayData[1].fieldValue = "Vega";
		arrayData[2].fieldPath = "form[candidate_age]";
		arrayData[2].fieldFindType = FieldKeyType.ID;
		arrayData[2].fieldType = FieldType.TEXTBOX;
		arrayData[2].fieldValue = "24";
		arrayData[3].fieldPath = "form[candidate_birthdate]";
		arrayData[3].fieldFindType = FieldKeyType.ID;
		arrayData[3].fieldType = FieldType.READONLY;
		arrayData[3].fieldValue = "1987-12-29";
		arrayData[4].fieldPath = "form[candidate_birthcountry]";
		arrayData[4].fieldFindType = FieldKeyType.ID;
		arrayData[4].fieldType = FieldType.DROPDOWN;
		arrayData[4].fieldValue = "Bolivia";
		arrayData[5].fieldPath = "form[candidate_birthcity]";
		arrayData[5].fieldFindType = FieldKeyType.ID;
		arrayData[5].fieldType = FieldType.DROPDOWN;
		arrayData[5].fieldValue = "La Paz";
		arrayData[6].fieldPath = "form[candidate_current_address]";
		arrayData[6].fieldFindType = FieldKeyType.ID;
		arrayData[6].fieldType = FieldType.TEXTBOX;
		arrayData[6].fieldValue = "Calle A";
		arrayData[7].fieldPath = "form[candidate_phonenumber]";
		arrayData[7].fieldFindType = FieldKeyType.ID;
		arrayData[7].fieldType = FieldType.TEXTBOX;
		arrayData[7].fieldValue = "22222333";
		arrayData[8].fieldPath = "form[candidate_email_address]";
		arrayData[8].fieldFindType = FieldKeyType.ID;
		arrayData[8].fieldType = FieldType.TEXTBOX;
		arrayData[8].fieldValue = "ernesto.vega@colosa.com";
		arrayData[9].fieldPath = "form[candidate_highschool_check]";
		arrayData[9].fieldFindType = FieldKeyType.ID;
		arrayData[9].fieldType = FieldType.DROPDOWN;
		arrayData[9].fieldValue = "Yes";
		arrayData[10].fieldPath = "form[candidate_highschool_graduation_date]";
		arrayData[10].fieldFindType = FieldKeyType.ID;
		arrayData[10].fieldType = FieldType.READONLY;
		arrayData[10].fieldValue = "2004-12-12";
		arrayData[11].fieldPath = "form[candidate_highschool_name]";
		arrayData[11].fieldFindType = FieldKeyType.ID;
		arrayData[11].fieldType = FieldType.TEXTBOX;
		arrayData[11].fieldValue = "America";
		arrayData[12].fieldPath = "form[candidate_college_check]";
		arrayData[12].fieldFindType = FieldKeyType.ID;
		arrayData[12].fieldType = FieldType.DROPDOWN;
		arrayData[12].fieldValue = "Yes";
		arrayData[13].fieldPath = "form[candidate_college_name]";
		arrayData[13].fieldFindType = FieldKeyType.ID;
		arrayData[13].fieldType = FieldType.TEXTBOX;
		arrayData[13].fieldValue = "Udabol";
		arrayData[14].fieldPath = "form[candidate_college_highest_degree]";
		arrayData[14].fieldFindType = FieldKeyType.ID;
		arrayData[14].fieldType = FieldType.DROPDOWN;
		arrayData[14].fieldValue = "Bachellor degree";
		arrayData[15].fieldPath = "form[candidate_college_graduation_date]";
		arrayData[15].fieldFindType = FieldKeyType.ID;
		arrayData[15].fieldType = FieldType.READONLY;
		arrayData[15].fieldValue = "2012-12-12";
		arrayData[16].fieldPath = "form[_candidate_profession_]";
		arrayData[16].fieldFindType = FieldKeyType.ID;
		arrayData[16].fieldType = FieldType.TEXTBOX;
		arrayData[16].fieldValue = "Systems Engineering";
		arrayData[17].fieldPath = "form[candidate_currently_employed]";
		arrayData[17].fieldFindType = FieldKeyType.ID;
		arrayData[17].fieldType = FieldType.DROPDOWN;
		arrayData[17].fieldValue = "Yes";
		arrayData[18].fieldPath = "form[candidate_job_title]";
		arrayData[18].fieldFindType = FieldKeyType.ID;
		arrayData[18].fieldType = FieldType.TEXTBOX;
		arrayData[18].fieldValue = "Developer";
		arrayData[19].fieldPath = "form[candidate_current_yearly_salary]";
		arrayData[19].fieldFindType = FieldKeyType.ID;
		arrayData[19].fieldType = FieldType.TEXTBOX;
		arrayData[19].fieldValue = "2346";
		arrayData[20].fieldPath = "form[candidate_responsibilities]";
		arrayData[20].fieldFindType = FieldKeyType.ID;
		arrayData[20].fieldType = FieldType.TEXTBOX;
		arrayData[20].fieldValue = "Development";
		arrayData[21].fieldPath = "form[candidate_manager_contact_info]";
		arrayData[21].fieldFindType = FieldKeyType.ID;
		arrayData[21].fieldType = FieldType.TEXTBOX;
		arrayData[21].fieldValue = "prueba@prueba.com";
		arrayData[22].fieldPath = "form[candidate_reason_for_leaving]";
		arrayData[22].fieldFindType = FieldKeyType.ID;
		arrayData[22].fieldType = FieldType.TEXTBOX;
		arrayData[22].fieldValue = "Disclaimer";

		FormFieldData[][] gridData = new FormFieldData[addJob][7];

		for(int rows = 0; rows<gridData.length;rows++)
		{
			gridData[rows][0] = new FormFieldData();
			gridData[rows][1] = new FormFieldData();
			gridData[rows][2] = new FormFieldData();
			gridData[rows][3] = new FormFieldData();
			gridData[rows][4] = new FormFieldData();
			gridData[rows][5] = new FormFieldData();
			gridData[rows][6] = new FormFieldData();
		}
		int count = 0;

		for(int rows = 0; rows<gridData.length;rows++)
		{
			count = rows + 1;
			gridData[rows][0].fieldPath = "form[work_experience_grid]["+count+"][work_experience_start_date]";
			gridData[rows][0].fieldFindType = FieldKeyType.ID;
			gridData[rows][0].fieldType = FieldType.TEXTBOX;
			gridData[rows][0].fieldValue = "2008-02-29";
			gridData[rows][1].fieldPath = "form[work_experience_grid]["+count+"][work_experience_ending_date]";
			gridData[rows][1].fieldFindType = FieldKeyType.ID;
			gridData[rows][1].fieldType = FieldType.TEXTBOX;
			gridData[rows][1].fieldValue = "2009-02-28";
			gridData[rows][2].fieldPath = "form[work_experience_grid]["+count+"][work_experience_company_name]";
			gridData[rows][2].fieldFindType = FieldKeyType.ID;
			gridData[rows][2].fieldType = FieldType.TEXTBOX;
			gridData[rows][2].fieldValue = "Empresa A";
			gridData[rows][3].fieldPath = "form[work_experience_grid]["+count+"][work_experience_department]";
			gridData[rows][3].fieldFindType = FieldKeyType.ID;
			gridData[rows][3].fieldType = FieldType.TEXTBOX;
			gridData[rows][3].fieldValue = "Department B";
			gridData[rows][4].fieldPath = "form[work_experience_grid]["+count+"][work_experience_position]";
			gridData[rows][4].fieldFindType = FieldKeyType.ID;
			gridData[rows][4].fieldType = FieldType.TEXTBOX;
			gridData[rows][4].fieldValue = "Position C";
			gridData[rows][5].fieldPath = "form[work_experience_grid]["+count+"][work_experience_responsabilities]";
			gridData[rows][5].fieldFindType = FieldKeyType.ID;
			gridData[rows][5].fieldType = FieldType.TEXTAREA;
			gridData[rows][5].fieldValue = "Prueba...";
			gridData[rows][6].fieldPath = "form[work_experience_grid]["+count+"][work_experience_leave_reason]";
			gridData[rows][6].fieldFindType = FieldKeyType.ID;
			gridData[rows][6].fieldType = FieldType.DROPDOWN;
			gridData[rows][6].fieldValue = "Left for other job";
		}

		FormFieldData[][] gridData2 = new FormFieldData[addJob][6];

		for(int rows = 0; rows<gridData2.length;rows++)
		{
			gridData2[rows][0] = new FormFieldData();
			gridData2[rows][1] = new FormFieldData();
			gridData2[rows][2] = new FormFieldData();
			gridData2[rows][3] = new FormFieldData();
			gridData2[rows][4] = new FormFieldData();
			gridData2[rows][5] = new FormFieldData();
		}
		int count2 = 0;

		for(int rows = 0; rows<gridData2.length;rows++)
		{
			count2 = rows + 1;

			gridData2[rows][0].fieldPath = "form[work_reference]["+count2+"][reference_type]";
			gridData2[rows][0].fieldFindType = FieldKeyType.ID;
			gridData2[rows][0].fieldType = FieldType.DROPDOWN;
			gridData2[rows][0].fieldValue = "Work";
			gridData2[rows][1].fieldPath = "form[work_reference]["+count2+"][reference_name]";
			gridData2[rows][1].fieldFindType = FieldKeyType.ID;
			gridData2[rows][1].fieldType = FieldType.TEXTBOX;
			gridData2[rows][1].fieldValue = "Prueba";
			gridData2[rows][2].fieldPath = "form[work_reference]["+count2+"][reference_company]";
			gridData2[rows][2].fieldFindType = FieldKeyType.ID;
			gridData2[rows][2].fieldType = FieldType.TEXTBOX;
			gridData2[rows][2].fieldValue = "Empresa A";
			gridData2[rows][3].fieldPath = "form[work_reference]["+count2+"][reference_relation]";
			gridData2[rows][3].fieldFindType = FieldKeyType.ID;
			gridData2[rows][3].fieldType = FieldType.TEXTBOX;
			gridData2[rows][3].fieldValue = "Director";
			gridData2[rows][4].fieldPath = "form[work_reference]["+count2+"][reference_phone_number]";
			gridData2[rows][4].fieldFindType = FieldKeyType.ID;
			gridData2[rows][4].fieldType = FieldType.TEXTBOX;
			gridData2[rows][4].fieldValue = "222222222";
			gridData2[rows][5].fieldPath = "form[work_reference]["+count2+"][reference_email]";
			gridData2[rows][5].fieldFindType = FieldKeyType.ID;
			gridData2[rows][5].fieldType = FieldType.TEXTAREA;
			gridData2[rows][5].fieldValue = "consulta@empresa.com";

		}
		
		FormFieldData[] arrayData2 = new FormFieldData[2];
		arrayData2[0] = new FormFieldData();
		arrayData2[1] = new FormFieldData();

		arrayData2[0].fieldPath = "form[_cv_file]";
		arrayData2[0].fieldFindType = FieldKeyType.ID;
		arrayData2[0].fieldType = FieldType.TEXTBOX;
		arrayData2[0].fieldValue = "";
		arrayData2[1].fieldPath = "form[_submit_button_1]";
		arrayData2[1].fieldFindType = FieldKeyType.ID;
		arrayData2[1].fieldType = FieldType.BUTTON;
		arrayData2[1].fieldValue = "";

		Pages.InputDocProcess().openCaseFrame();
		Assert.assertTrue(FormFiller.formFillElements(arrayData));
		WebElement btnLink=null;
		WebElement btnLink2 =null;
		btnLink = Browser.driver().findElement(By.id("form[work_experience_grid][addLink]"));
		for(int rows = 1;rows<addJob;rows++)
		{
			btnLink.click();
		}
		btnLink2 = Browser.driver().findElement(By.id("form[work_reference][addLink]"));
		for(int rows = 1;rows<addJob;rows++)
		{
			btnLink2.click();
		}
		Assert.assertTrue(GridFiller.gridFillElements(gridData));
		Assert.assertTrue(GridFiller.gridFillElements(gridData2));
		Assert.assertTrue(FormFiller.formFillElements(arrayData2));
		Assert.assertTrue(Pages.InputDocProcess().continuebtn());
		Pages.Main().logout();
	}
	
	/*@Test
	public void continueProcess() throws FileNotFoundException, IOException, Exception{

		Pages.Login().gotoUrl();
		Pages.Login().loginUser("hector", "sample", "");
		Pages.Main().goHome();
		Pages.Home().openCase(caseNum);
		FormFieldData[] arrayData = new FormFieldData[2];
		arrayData[0] = new FormFieldData();
		arrayData[1] = new FormFieldData();

		arrayData[0].fieldPath = "form[_candidate_qualification_check]";
		arrayData[0].fieldFindType = FieldKeyType.ID;
		arrayData[0].fieldType = FieldType.DROPDOWN;
		arrayData[0].fieldValue = "Yes";
		arrayData[1].fieldPath = "form[_add_cv_info_form_submit_button]";
		arrayData[1].fieldFindType = FieldKeyType.ID;
		arrayData[1].fieldType = FieldType.BUTTON;
		arrayData[1].fieldValue = "";
		Pages.InputDocProcess().openCaseFrame();
		Assert.assertTrue(FormFiller.formFillElements(arrayData));
		Assert.assertTrue(Pages.InputDocProcess().continuebtn());
		Pages.Main().logout();
	}*/

}