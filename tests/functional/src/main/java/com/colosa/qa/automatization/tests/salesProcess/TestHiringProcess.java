package com.colosa.qa.automatization.tests.salesProcess;

import org.junit.Assert;
import org.junit.AfterClass;
import org.junit.Test;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import com.colosa.qa.automatization.pages.*;
import com.colosa.qa.automatization.common.*;

import java.io.FileNotFoundException;
import java.io.IOException;

public class TestHiringProcess{
	protected static int addJob=5;
	protected static int addJob1=3;
@Test
public void runProcess() throws Exception{
	Pages.Login().gotoUrl();
	Pages.Login().loginUser("hector","sample","workflow");
	Pages.Main().goHome();
	int casenumber=Pages.Home().startCase("Employee Hiring process - v0.2 (Review Candidates list, select top 3.)");
	FormFieldData[][] gridData= new FormFieldData[addJob][1];

	for(int rows=0; rows<gridData.length; rows++){ 
			 gridData[rows][0]=new FormFieldData();
		}
		int cont=0;

     for(int rows=0; rows<gridData.length; rows++) {
     	cont=rows+1;
    if(cont<=2)
    {
		    gridData[rows][0].fieldPath="form[_list_of_candidates_for_hiring_]["+cont+"][CANDIDATE_TOP_3_PLACE]";
			gridData[rows][0].fieldFindType=FieldKeyType.ID;
			gridData[rows][0].fieldType=FieldType.DROPDOWN;
			gridData[rows][0].fieldValue="Second";
		}
		else
		{ gridData[rows][0].fieldPath="form[_list_of_candidates_for_hiring_]["+cont+"][CANDIDATE_TOP_3_PLACE]";
			gridData[rows][0].fieldFindType=FieldKeyType.ID;
			gridData[rows][0].fieldType=FieldType.DROPDOWN;
			gridData[rows][0].fieldValue="First";
		}

				  
		}
		FormFieldData[] fieldarray=new FormFieldData[1];
		fieldarray[0]= new FormFieldData();
		fieldarray[0].fieldPath="form[_review_candidates_list_form_submit_button]";
		fieldarray[0].fieldFindType=FieldKeyType.ID;
		fieldarray[0].fieldType=FieldType.BUTTON;
		fieldarray[0].fieldValue="";

		Pages.InputDocProcess().openCaseFrame();
		Assert.assertTrue(GridFiller.gridFillElements(gridData));
		Assert.assertTrue(FormFiller.formFillElements(fieldarray));
		Assert.assertTrue(Pages.InputDocProcess().continuebtn());
		Pages.Home().logout();
		openTask2(casenumber);
}

public void openTask2(int casenumber) throws Exception{
	Pages.Login().gotoUrl();
	 Pages.Login().loginUser("iver","sample","");
	 Pages.Main().goHome();
	 opencase(casenumber);
    FormFieldData[][] gridData1= new FormFieldData[addJob][1];
    for(int rows=0; rows<gridData1.length;rows++){
    	gridData1[rows][0]=new FormFieldData();
    }
    int cont_2=0;
    for(int rows=0;rows<gridData1.length;rows++){
    	cont_2=rows+1;
    	if(cont_2<=2){
    	        gridData1[rows][0].fieldPath="form[_list_of_top_3_candidates_for_hiring]["+cont_2+"][CANDIDATE_TO_BE_HIRED]";
			    gridData1[rows][0].fieldFindType=FieldKeyType.ID;
			    gridData1[rows][0].fieldType=FieldType.DROPDOWN;
			    gridData1[rows][0].fieldValue="No";
		         }
	 	          else{
			    gridData1[rows][0].fieldPath="form[_list_of_top_3_candidates_for_hiring]["+cont_2+"][CANDIDATE_TO_BE_HIRED]";
			    gridData1[rows][0].fieldFindType=FieldKeyType.ID;
			    gridData1[rows][0].fieldType=FieldType.DROPDOWN;
			    gridData1[rows][0].fieldValue="Yes";
		       }
			}
			FormFieldData[] fieldarray2=new FormFieldData[1];
			fieldarray2[0]=new FormFieldData();
			fieldarray2[0].fieldPath="form[_hiring_decision_submit_button]";
		    fieldarray2[0].fieldFindType=FieldKeyType.ID;
		    fieldarray2[0].fieldType=FieldType.BUTTON;
		    fieldarray2[0].fieldValue="";

		    Pages.InputDocProcess().openCaseFrame();
		    Assert.assertTrue(GridFiller.gridFillElements(gridData1));
		    Assert.assertTrue(FormFiller.formFillElements(fieldarray2));
		    Assert.assertTrue(Pages.InputDocProcess().continuebtn());
		    Pages.Home().logout();
		    openTask3(casenumber);

    }

    public void openTask3(int casenumber) throws Exception{
    	Pages.Login().gotoUrl();
	 Pages.Login().loginUser("ronald","sample","");
	 Pages.Main().goHome();
	 opencase(casenumber);
	FormFieldData[] fieldarray3=new FormFieldData[1];
			fieldarray3[0]=new FormFieldData();
			fieldarray3[0].fieldPath="form[_hr_database_form_submit_button]";
		    fieldarray3[0].fieldFindType=FieldKeyType.ID;
		    fieldarray3[0].fieldType=FieldType.BUTTON;
		    fieldarray3[0].fieldValue="";

		     Pages.InputDocProcess().openCaseFrame();
		    Assert.assertTrue(FormFiller.formFillElements(fieldarray3));
		    Assert.assertTrue(Pages.InputDocProcess().continuebtn());
		    Pages.Home().logout();
		    openTask4(casenumber);
    }

    public void openTask4(int casenumber) throws Exception{
    	Pages.Login().gotoUrl();
	 Pages.Login().loginUser("pablo","sample","");
	 Pages.Main().goHome();
	 opencase(casenumber);
	 FormFieldData[][] gridData2= new FormFieldData[addJob1][1];
    for(int rows=0; rows<gridData2.length;rows++){
    	gridData2[rows][0]=new FormFieldData();
    }
    int cont_3=0;
    for(int rows=0;rows<gridData2.length;rows++){
    	cont_3=rows+1;
    	if (cont_3<=1)
    	{
    	gridData2[rows][0].fieldPath="form[_hirees_list_]["+cont_3+"][negotiated_salary]";
		gridData2[rows][0].fieldFindType=FieldKeyType.ID;
		gridData2[rows][0].fieldType=FieldType.TEXTBOX;
		gridData2[rows][0].fieldValue="55";
	}
	else
	{gridData2[rows][0].fieldPath="form[_hirees_list_]["+cont_3+"][negotiated_salary]";
		gridData2[rows][0].fieldFindType=FieldKeyType.ID;
		gridData2[rows][0].fieldType=FieldType.TEXTBOX;
		gridData2[rows][0].fieldValue="80";

	}
    }
            FormFieldData[] fieldarray4=new FormFieldData[1];
			fieldarray4[0]=new FormFieldData();
			fieldarray4[0].fieldPath="form[_salary_negotiation_submit_button]";
		    fieldarray4[0].fieldFindType=FieldKeyType.ID;
		    fieldarray4[0].fieldType=FieldType.BUTTON;
		    fieldarray4[0].fieldValue="";

		    Pages.InputDocProcess().openCaseFrame();
		    Assert.assertTrue(GridFiller.gridFillElements(gridData2));
		    Assert.assertTrue(FormFiller.formFillElements(fieldarray4));
		    Assert.assertTrue(Pages.InputDocProcess().continuebtn());
		    Pages.Home().logout();
}
public void opencase(int casenumber) throws Exception{ 
	Pages.Home().openCase(casenumber);

}

}