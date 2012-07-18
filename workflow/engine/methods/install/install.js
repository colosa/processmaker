var installer=function()
{
	this.make=function(options)
	{
		this.options={
			target:inst.elements.content,
			vdef:{
				wf:'wf_workflow',
				rb:'rb_workflow',
				rp:'rp_workflow'
			}
		}.concat(options || {});
		this.html();
		this.check();
	};
	this.html=function()
	{
		this.titleBar = document.createElement("div");
		this.titleBar.className="app_grid_headerBar___gray";
		leimnud.dom.setStyle(this.titleBar,{
			height:"auto",
			textAlign:"right"
		});
		this.options.target.appendChild(this.titleBar);

		this.options.button0 = document.createElement("input");
		this.options.button0.type="button";
		this.options.button0.value="Test";
		this.titleBar.appendChild(this.options.button0);

		this.options.button1 = document.createElement("input");
		this.options.button1.type="button";
		this.options.button1.value="Install";
		this.titleBar.appendChild(this.options.button1);

		this.options.button2 = document.createElement("input");
		this.options.button2.type="button";
		this.options.button2.value="Reset";
		this.titleBar.appendChild(this.options.button2);

        this.options.phpinfo = document.createElement("input");
		this.options.phpinfo.type="button";
		this.options.phpinfo.style.fontWeight="bold";
		this.options.phpinfo.value="phpinfo()";
		this.titleBar.appendChild(this.options.phpinfo);
        this.options.phpinfo.onmouseup=this.showPhpinfo;


		this.options.button1.disabled=true;
		this.options.button0.onmouseup=this.check;
		this.options.button1.onmouseup=function(){inst.selectTab(1);}.extend(this);
		this.options.button2.onmouseup=this.reset;

		this.buttonFun(this.options.button0);
		this.buttonFun(this.options.button1);
		this.buttonFun(this.options.button2);
		this.buttonFun(this.options.phpinfo);



		//this.phpVersion =
		this.table = $(document.createElement("table"));
/*		this.table.setStyle({
			cellpadding:23
		});*/
		this.table.className="inst_table";

		var tr = this.table.insertRow(-1);
		$(tr).append(
			new DOM('td',{innerHTML:"<b>Requirements</b>",className:"app_grid_title___gray title",colSpan:4})
		);

		var tr = this.table.insertRow(-1);
		$(tr).append(
			new DOM('td',{innerHTML:"PHP Version > 5.1.0",className:"inst_td0",colSpan:2}),
			this.phpVersion = new DOM('td',{innerHTML:'Loading...',className:"inst_td1",colSpan:2})
		);

		var tr = this.table.insertRow(-1);
		$(tr).append(
			new DOM('td',{innerHTML:"MySQL",className:"inst_td0",colSpan:2}),
			this.mysqlVersion = new DOM('td',{innerHTML:'Loading...',className:"inst_td1",colSpan:2})
		);

		var tr = this.table.insertRow(-1);
		$(tr).append(
			new DOM('td',{innerHTML:"Maximum amount of memory a script may consume (memory_limit) >= 40M",className:"inst_td0",colSpan:2}),
			this.checkMemory = new DOM('td',{innerHTML:'Loading...',className:"inst_td1",colSpan:2})
		);

		var tr = this.table.insertRow(-1);
		$(tr).append(
			new DOM('td',{innerHTML:"Directory "+this.options.path_trunk+"config/<br> permissions: <b>writable</b>",className:"inst_td0",colSpan:2}),
			this.checkPI = new DOM('td',{innerHTML:'Loading...',className:"inst_td1",colSpan:2})
		);

		var tr = this.table.insertRow(-1);
		$(tr).append(
			new DOM('td',{innerHTML:"Directory "+this.options.path_trunk+"content/languages/<br> permissions: <b>writable</b>",className:"inst_td0",colSpan:2}),
			this.checkDL = new DOM('td',{innerHTML:'Loading...',className:"inst_td1",colSpan:2})
		);

		var tr = this.table.insertRow(-1);
		$(tr).append(
			new DOM('td',{innerHTML:"File "+this.options.path_trunk+"js/labels/<br> permissions: <b>writable</b>",className:"inst_td0",colSpan:2}),
			this.checkDLJ = new DOM('td',{innerHTML:'Loading...',className:"inst_td1",colSpan:2})
		);

		var tr = this.table.insertRow(-1);
		$(tr).append(
			new DOM('td',{innerHTML:"File "+this.options.path_trunk+"plugins/<br> permissions: <b>writable</b>",className:"inst_td0",colSpan:2}),
			this.checkPL = new DOM('td',{innerHTML:'Loading...',className:"inst_td1",colSpan:2})
		);

		var tr = this.table.insertRow(-1);
		$(tr).append(
			new DOM('td',{innerHTML:"File "+this.options.path_trunk+"xmlform/<br> permissions: <b>writable</b>",className:"inst_td0",colSpan:2}),
			this.checkXF = new DOM('td',{innerHTML:'Loading...',className:"inst_td1",colSpan:2})
		);

		/* Database  */
		var tr = this.table.insertRow(-1);
		$(tr).append(
			new DOM('td',{innerHTML:"<b>Database</b>",className:"app_grid_title___gray title",colSpan:2}),
			new DOM('td',{className:"app_grid_title___gray title",colSpan:2}).append(
				this.select_ao_db =	new select({data:[
					{value:1,text:"Advanced options by default"},
					{value:2,text:"Change Advanced options"}
				],
				style:{width:"100%",border:"1px solid #919B9C"},
				properties:{onchange:function(){
						if(this.select_ao_db.selected().value==1)
						{
							this.ed_advanced_options({
								sta:"disabled",
								act:'usr',
								def:true
							});
							this.ao_db_wf.passed().value=this.options.vdef.wf;
							this.ao_db_rb.passed().value=this.options.vdef.rb;
							this.ao_db_rp.passed().value=this.options.vdef.rp;
							this.ao_db_drop.checked=false;
						}
						else
						{
							this.ed_advanced_options({
								act:'usr',
								sta:"enabled"
							});
							this.ao_db_wf.focus();
						}
					}.extend(this)}
				})
			)
		);

		var tr = this.table.insertRow(-1);
		$(tr).append(
			new DOM('td',{innerHTML:"Database server Hostname",className:"inst_td0"},{width:"30%"}),
			new DOM('td',{className:"inst_td1"},{width:"30%"}).append(
//				this.databaseHostname =new DOM("input",{value:'localhost',type:"text",onkeyup:this.submit,className:"inputNormal"})
				this.databaseHostname = new input({label:'localhost',properties:{onkeyup:this.submit},style:{width:"100%"}})
			),
			new DOM('td',{innerHTML:"Workflow Database:",className:"inst_td0"},{width:"20%"}),
			new DOM('td',{className:"inst_td1"},{width:"20%"}).append(
				this.ao_db_wf = new input({label:this.options.vdef.wf,properties:{onkeyup:this.submit},style:{width:"100%"},maxlength:16}).passed().disable()
			)

		);

		var tr = this.table.insertRow(-1);
		$(tr).append(
			new DOM('td',{innerHTML:"Username",className:"inst_td0"},{width:"30%"}),
			new DOM('td',{className:"inst_td1"},{width:"30%"}).append(
//				this.databaseUsername =new DOM("input",{value:'root',type:"text",onkeyup:this.submit,className:"inputNormal"})
				this.databaseUsername = new input({label:'root',properties:{onkeyup:this.submit},style:{width:"100%"}})
			),
			new DOM('td',{innerHTML:"Rbac Database:",className:"inst_td0"},{width:"20%"}),
			new DOM('td',{className:"inst_td1"},{width:"20%"}).append(
				this.ao_db_rb = new input({label:this.options.vdef.rb,properties:{onkeyup:this.submit},style:{width:"100%"},maxlength:16}).passed().disable()
			)

		);

		var tr = this.table.insertRow(-1);
		$(tr).append(
			new DOM('td',{innerHTML:"Password",className:"inst_td0"},{width:"30%"}),
			new DOM('td',{className:"inst_td1"},{width:"30%"}).append(
//				this.databasePassword =new DOM("input",{type:"text",onkeyup:this.submit,className:"inputNormal"})
				this.databasePassword = new input({properties:{type:'password',onkeyup:this.submit},style:{width:"100%"}})
			),
			new DOM('td',{innerHTML:"Report Database:",className:"inst_td0"},{width:"20%"}),
			new DOM('td',{className:"inst_td1"},{width:"20%"}).append(
				this.ao_db_rp = new input({label:this.options.vdef.rp,properties:{onkeyup:this.submit},style:{width:"100%"},maxlength:16}).passed().disable()
			)

		);

		var tr = this.table.insertRow(-1);
		$(tr).append(
			new DOM('td',{innerHTML:"Database Access",className:"inst_td0"},{width:"30%"}),
			this.databaseGrant = new DOM('td',{className:"inst_td1"},{width:"30%"}),

			new DOM('td',{innerHTML:"DROP DATABASE IF EXISTS",className:"inst_td0"},{width:"20%"}),
			new DOM('td',{className:"inst_td0"},{width:"20%",textAlign:'left'}).append(
				this.ao_db_drop = new input({
					properties:{type:'checkbox',disabled:true,className:''},style:{border:"1px solid #666"}
				})
			)
		);

		var tr = this.table.insertRow(-1);
		$(tr).append(
			this.databaseStatus = new DOM('td',{innerHTML:"<br>",className:"tdNormal",colSpan:4},{height:50})
		);

		/* Database End  */



		/* Directories Begin  */

		var tr = this.table.insertRow(-1);
		$(tr).append(
			new DOM('td',{innerHTML:"<b>Processmaker Configuration</b>",className:"app_grid_title___gray title",colSpan:2}),
			new DOM('td',{className:"app_grid_title___gray title",colSpan:2}).append(

				this.select_ao_pm =	new select({data:[
					{value:1,text:"Advanced options by default"},
					{value:2,text:"Change Advanced options"}
				],
				style:{width:"100%",border:"1px solid #919B9C"},
				properties:{onchange:function(){
						if(this.select_ao_pm.selected().value==1)
						{
							this.ed_advanced_options({
								act:'pm',
								sta:"disabled",
								def:true
							});
							this.ao_admin.passed().value="admin";
							this.ao_admin_pass1.passed().value="admin";
							this.ao_admin_pass2.passed().value="admin";

						}
						else
						{
							this.ed_advanced_options({
								act:'pm',
								sta:"enabled"
							});
							this.ao_admin.focus();
						}
					}.extend(this)}
				})
			)
		);

		var tr = this.table.insertRow(-1);
		$(tr).append(
			new DOM('td',{innerHTML:"Workflow Data Directory (permissions: <b>writable</b>): ",className:"inst_td0"},{width:"30%"}),
			new DOM('td',{className:"inst_td1"},{width:"30%"}).append(
				this.workflowData = new input({label:this.options.path_data,properties:{onkeyup:this.submit},style:{width:"100%"},maxlength:200})
			),
			new DOM('td',{innerHTML:"Username (Default: admin):",className:"inst_td0"},{width:"20%"}),
			new DOM('td',{className:"inst_td1"},{width:"20%"}).append(
				this.ao_admin = new input({label:'admin',properties:{onkeyup:this.submit},style:{width:"100%"}}).passed().disable()
			)

		);

		var tr = this.table.insertRow(-1);
		$(tr).append(
			new DOM('td',{innerHTML:"",className:"inst_td0"},{width:"30%"}),
			new DOM('td',{className:"inst_td1"},{width:"30%"}).append(
				this.compiled = new input({label:this.options.path_compiled,properties:{onkeyup:this.submit},style:{width:"100%", display:'none'},maxlength:200})
			),
			new DOM('td',{innerHTML:"Username (Default: admin):",className:"inst_td0"},{width:"20%"}),
			new DOM('td',{className:"inst_td1"},{width:"20%"}).append(
				this.ao_admin_pass1 = new input({label:'admin',properties:{onkeyup:this.submit,type:'password'},style:{width:"100%"}}).passed().disable()
			)
		);

		var tr = this.table.insertRow(-1);
		$(tr).append(
			new DOM('td',{className:"inst_td0",colSpan:2}),
			new DOM('td',{innerHTML:"Re-type Password:",className:"inst_td0"},{width:"20%"}),
			new DOM('td',{className:"inst_td1"},{width:"20%"}).append(
				this.ao_admin_pass2 = new input({label:'admin',properties:{onkeyup:this.submit,type:'password'},style:{width:"100%"}}).passed().disable()
			)
		);
		
		var tr = this.table.insertRow(-1);
		$(tr).append(
			new DOM('td',{innerHTML:"<b>HeartBeat Configuration</b><br><i>Heartbeat is an anonymous statistics collector for ProcessMaker. It runs in the background and if you have internet enabled, it will periodically send anonymous information to ProcessMaker servers.<br />No sensitive or private information is collected.<br /><br /> The information collected will allow us to keep improving our software to offer everyone a better user experience</i>",className:"app_grid_title___gray title",colSpan:4})
		);
		this.heartBeatTitle=tr;
		
		var tr = this.table.insertRow(-1);
		$(tr).append(
				
				this.heartBeat = 

				new DOM('td',{innerHTML:"Enable HeartBeat",className:"inst_td0"},{width:"20%"}),
				new DOM('td',{className:"inst_td0",colSpan:2},{textAlign:'left'}).append(
					this.ao_hb_status = new input({
						properties:{type:'checkbox',disabled:false,checked:true,className:''},style:{border:"1px solid #666"}
					})
				)
			);
		
	
		this.heartBeatRow=tr;
		
		
		//alert(this.options.availableProcess);
		//alert(this.options.availableProcess.length);
		var tr = this.table.insertRow(-1);
		$(tr).append(
			new DOM('td',{innerHTML:"<b>Available Processes (auto install)</b>",className:"app_grid_title___gray title",colSpan:4})
		);
		this.availableProcessTitle=tr;
		
		var tr = this.table.insertRow(-1);
		$(tr).append(			
			this.availableProcess = new DOM('td',{innerHTML:'Loading...',className:"inst_td1",colSpan:2})
		);
		this.availableProcessRow=tr;
		
		var tr = this.table.insertRow(-1);
		$(tr).append(
			new DOM('td',{innerHTML:"<b>Available Plugins (auto install)</b>",className:"app_grid_title___gray title",colSpan:4})
		);
		this.availablePluginsTitle=tr;
		
		var tr = this.table.insertRow(-1);
		$(tr).append(
			this.availablePlugins = new DOM('td',{innerHTML:'Loading...',className:"inst_td1",colSpan:2})
		);
		this.availablePluginsRow=tr;		


		leimnud.dom.setStyle([this.workflowData,this.compiled],{
			textAlign:"left"
		});
		this.options.target.appendChild(this.table);
	};
	this.formData=function()
	{
		//alert(this.databaseExe.value.eplace("\\","/"))
		return {
				mysqlH	:escape(this.databaseHostname.value),
				mysqlU	:escape(this.databaseUsername.value),
				mysqlP	:escape(this.databasePassword.value),
//				port	:this.port.value,
				path_data:this.workflowData.value,
				path_compiled:this.compiled.value,
				ao_admin	:escape(this.ao_admin.value),
				ao_admin_pass1	:escape(this.ao_admin_pass1.value),
				ao_admin_pass2	:escape(this.ao_admin_pass2.value),
				ao_db_wf	:this.ao_db_wf.value,
				ao_db_rb	:this.ao_db_rb.value,
				ao_db_rp	:this.ao_db_rp.value,
				ao_db	:parseInt(this.select_ao_db.selected().value),
				ao_pm	:parseInt(this.select_ao_pm.selected().value),
				ao_db_drop	:this.ao_db_drop.checked,
				heartbeatEnabled	:this.ao_hb_status.checked
			};
	};
	this.check=function()
	{
		inst.loader.show();
		this.disabled(true);
		this.ed_advanced_options({sta:'disabled',act:'all'});
		var r = new leimnud.module.rpc.xmlhttp({
			url	:this.options.server,
			method	:"POST",
			args	:"action=check&data="+this.formData().toJSONString()
		});
		r.callback=function(rpc)
		{
			try
			{
				this.cstatus = rpc.xmlhttp.responseText.parseJSON();
			}
			catch(e)
			{
				this.cstatus={
					ao_db_wf:false,
					ao_db_rb:false,
					ao_db_rp:false
				};
			}
			this.phpVersion.className = (!this.cstatus.phpVersion)?"inst_td1 tdFailed":"inst_td1 tdOk";
			this.phpVersion.innerHTML = (!this.cstatus.phpVersion)?"FAILED":"PASSED";

			this.mysqlVersion.className = (!this.cstatus.mysqlVersion)?"inst_td1 tdFailed":"inst_td1 tdOk";
			this.mysqlVersion.innerHTML = (!this.cstatus.mysqlVersion)?"FAILED":"PASSED";

			this.checkMemory.className = (!this.cstatus.checkMemory)?"inst_td1 tdFailed":"inst_td1 tdOk";
			this.checkMemory.innerHTML = (!this.cstatus.checkMemory)?"FAILED":"PASSED";

//			this.checkmqgpc.className = (!this.cstatus.checkmqgpc)?"inst_td1 tdFailed":"inst_td1 tdOk";
//			this.checkmqgpc.innerHTML = (!this.cstatus.checkmqgpc)?"FAILED":"PASSED";

			this.checkPI.className = (!this.cstatus.checkPI)?"inst_td1 tdFailed":"inst_td1 tdOk";
			this.checkPI.innerHTML = (!this.cstatus.checkPI)?"FAILED":"PASSED";

			this.checkDL.className = (!this.cstatus.checkDL)?"inst_td1 tdFailed":"inst_td1 tdOk";
			this.checkDL.innerHTML = (!this.cstatus.checkDL)?"FAILED":"PASSED";

			this.checkDLJ.className = (!this.cstatus.checkDLJ)?"inst_td1 tdFailed":"inst_td1 tdOk";
			this.checkDLJ.innerHTML = (!this.cstatus.checkDLJ)?"FAILED":"PASSED";

			this.checkPL.className = (!this.cstatus.checkPL)?"inst_td1 tdFailed":"inst_td1 tdOk";
			this.checkPL.innerHTML = (!this.cstatus.checkPL)?"FAILED":"PASSED";

			this.checkXF.className = (!this.cstatus.checkXF)?"inst_td1 tdFailed":"inst_td1 tdOk";
			this.checkXF.innerHTML = (!this.cstatus.checkXF)?"FAILED":"PASSED";

			this.databaseHostname[(!this.cstatus.mysqlConnection)?"failed":"passed"]();
			this.databaseUsername[(!this.cstatus.mysqlConnection)?"failed":"passed"]();
			this.databasePassword[(!this.cstatus.mysqlConnection)?"failed":"passed"]();

			this.databaseGrant.className = (!this.cstatus.grantPriv && this.select_ao_db.selected().value==1)?"inst_td1 tdFailed":"inst_td1 tdOk";
			this.databaseGrant.innerHTML = (!this.cstatus.grantPriv)?"FAILED":((this.cstatus.grantPriv==1)?'ALL PRIVILEGES':'USAGE');

			this.databaseStatus.className = (!this.cstatus.grantPriv || !this.cstatus.mysqlConnection)?"tdFailed":"tdOk";
			this.databaseStatus.innerHTML = this.cstatus.databaseMessage;

			this.workflowData[(!this.cstatus.path_data)?"failed":"passed"]();
			this.compiled[(!this.cstatus.path_compiled)?"failed":"passed"]();


			this.ao_db_wf[((!this.cstatus.ao_db_wf['status'])?"failed":"passed")]();
			this.ao_db_rb[((!this.cstatus.ao_db_rb['status'])?"failed":"passed")]();
			this.ao_db_rp[((!this.cstatus.ao_db_rp['status'])?"failed":"passed")]();

			this.ao_admin[(!this.cstatus.ao_admin && this.select_ao_pm.selected().value==2)?"failed":"passed"]();
			this.ao_admin_pass1[(!this.cstatus.ao_admin_pass && this.select_ao_pm.selected().value==2)?"failed":"passed"]();
			this.ao_admin_pass2[(!this.cstatus.ao_admin_pass && this.select_ao_pm.selected().value==2)?"failed":"passed"]();

			if(this.cstatus.ao_db_wf['status'] && this.cstatus.ao_db_rb['status'] && this.cstatus.ao_db_rp['status'] && this.cstatus.ao_admin && this.cstatus.ao_admin_pass && this.cstatus.checkMemory && this.cstatus.checkPI && this.cstatus.checkDL && this.cstatus.checkDLJ && this.cstatus.checkPL && this.cstatus.checkXF && this.cstatus.phpVersion && this.cstatus.mysqlVersion && this.cstatus.mysqlConnection && this.cstatus.grantPriv && this.cstatus.path_data && this.cstatus.path_compiled)
			{
				this.options.button0.disabled=true;
				this.options.button1.disabled=false;
				this.disabled(true);
			}
			else
			{
				this.options.button0.disabled=false;
				this.options.button1.disabled=true;
				this.disabled(false);
        try {
          this.compiled.focus();
        } catch(err) {}
				this.ed_advanced_options({sta:((this.select_ao_db.selected().value==2)?'enabled':'disabled'),act:'usr'});
				this.ed_advanced_options({sta:((this.select_ao_pm.selected().value==2)?'enabled':'disabled'),act:'pm'});
			}
			this.buttonFun(this.options.button0);
			this.buttonFun(this.options.button1);

			this.ao_db_wf.title=this.cstatus.ao_db_wf.message;
			this.ao_db_rb.title=this.cstatus.ao_db_rb.message;
			this.ao_db_rp.title=this.cstatus.ao_db_rp.message;

			this.ao_admin.title=(this.cstatus.ao_admin)?'Username invalid':'PASSED';
			
			
			//*Autoinstall Process and Plugins. By JHL
    	// March 11th. 2009
    	// To enable the way of aoutoinstall process and/or plugins 
    	// at same time of initial PM setup
			if(this.cstatus.availableProcess.length>0){			  
			 this.availableProcess.className = "inst_td1 tdOk";
			 this.availableProcess.innerHTML=""; 
			 for(i_process=0;i_process<this.cstatus.availableProcess.length;i_process++){
			  this.availableProcess.innerHTML+=this.cstatus.availableProcess[i_process]+"<br />";
			 }	  
			}else{
			  //Hide entire Group
			  this.availableProcessTitle.style.display="none";
			  this.availableProcessRow.style.display="none";
			}
			
			if(this.cstatus.availablePlugins.length>0){			  
			 this.availablePlugins.className = "inst_td1 tdOk";
			 this.availablePlugins.innerHTML=""; 
			 for(i_plugin=0;i_plugin<this.cstatus.availablePlugins.length;i_plugin++){
			  this.availablePlugins.innerHTML+=this.cstatus.availablePlugins[i_plugin]+"<br />";
			 }	  
			}else{
			  //Hide entire Group
			  this.availablePluginsTitle.style.display="none";
			  this.availablePluginsRow.style.display="none";
			}
			//End Autoinstall			

			inst.loader.hide();
		}.extend(this);
		r.make();
	};
	this.reset=function()
	{
		this.options.button1.disabled=true;
		this.buttonFun(this.options.button1);
		this.disabled(false);
	};
	this.disabled=function(dis)
	{
		this.databaseHostname[(dis===true)?'disable':'enable']();
		this.databaseUsername[(dis===true)?'disable':'enable']();
		this.databasePassword[(dis===true)?'disable':'enable']();
		this.workflowData[(dis===true)?'disable':'enable']();
		this.compiled[(dis===true)?'disable':'enable']();
		if(this.compiled.disabled===false)
		{
      try {
        this.compiled.focus();
      } catch(err) {}
		}
		this.options.button0.disabled=dis;
		this.buttonFun(this.options.button0);
	};
	this.ed_advanced_options=function(options)
	{
		options = {
			sta:"disabled",
			act:"all",
			def:false
		}.concat(options || {});

		if(options.act=='pm' || options.act=="all")
		{
			this.ao_admin[(options.sta=="disabled")?'disable':'enable']();
			this.ao_admin_pass1[(options.sta=="disabled")?'disable':'enable']();
			this.ao_admin_pass2[(options.sta=="disabled")?'disable':'enable']();
		}
		if(options.act=='usr' || options.act=="all")
		{
			this.ao_db_wf[(options.sta=="disabled")?'disable':'enable']();
			this.ao_db_rb[(options.sta=="disabled")?'disable':'enable']();
			this.ao_db_rp[(options.sta=="disabled")?'disable':'enable']();
			this.ao_db_drop.disabled=(options.sta=="disabled")?true:false;
		}
	};
	this.submit=function(evt)
	{
		var evt = (window.event)?window.event:evt;
		var key = (evt.which)?evt.which:evt.keyCode;
		if(key==13)
		{
			this.check();
		}
		return false;
	};
	this.install=function()
	{
		this.values = this.formData();
		inst.clearContent();
		inst.loader.show();
		this.options.button2.disabled=true;
		this.options.button1.disabled=true;
		var r = new leimnud.module.rpc.xmlhttp({
			url	:this.options.server,
			method	:"POST",
			args	:"action=install&data="+this.values.toJSONString()
		});
		r.callback=this.installation;
		r.make();
	};
	this.installation=function(rpc)
	{
/*		var r = new leimnud.module.rpc.xmlhttp({
			url	:"/sysworkflow/en/classic/tools/updateTranslation",
			method	:"GET"
		});
		r.callback=function(rpc)
		{*/
			inst.loader.hide();
			this.table = document.createElement("table");
			this.table.className="inst_table";

      var success = (rpc.xmlhttp.status == 200);

			var tr = this.table.insertRow(-1);
			var tdtitle = tr.insertCell(0);
			tdtitle.innerHTML="Status";
			tdtitle.className="app_grid_title___gray title";

			var tr = this.table.insertRow(-1);
			var td0 = tr.insertCell(0);
			td0.innerHTML=(success) ? "Success" : "Failed (Check log below)";
			td0.className=(success) ? "tdOk" : "tdFailed";
			this.options.target.appendChild(this.table);

      if (success) {

			var tr = this.table.insertRow(-1);
			var tdS = tr.insertCell(0);
			tdS.colSpan = 2;
			tdS.innerHTML="<br><br>";
			tdS.className="tdNormal";


			this.options.buttong = document.createElement("input");
			this.options.buttong.type="button";
			this.options.buttong.value="Finish Installation";
			this.options.buttong.onmouseup=function()
			{
				window.location = "/sysworkflow/en/classic/login/login";
			}.extend(this);
			tdS.appendChild(this.options.buttong);
			this.buttonFun(this.options.buttong);
			tdS.appendChild(document.createElement("br"));
			tdS.appendChild(document.createElement("br"));
      
      }

			var tr = this.table.insertRow(-1);
			var tdtitle = tr.insertCell(0);
			tdtitle.innerHTML="Installation Log";
			tdtitle.className="app_grid_title___gray title";

			var tr = this.table.insertRow(-1);
			var td0 = tr.insertCell(0);
			var pre = document.createElement('pre');
			pre.style.overflow='scroll';
			pre.style.width=(this.options.target.clientWidth-10)+"px";
			pre.style.height=((this.options.target.clientHeight-this.table.clientHeight)-15)+'px';
			pre.innerHTML=rpc.xmlhttp.responseText;
			td0.appendChild(pre);
//		}.extend(this);
//		r.make();
	};
	this.buttonFun=function(but)
	{
		if(but.disabled==true)
		{
			but.className="app_grid_title___gray button buttonDisabled";
			but.onmouseover=function(){ this.className="app_grid_title___gray button buttonDisabled"};
			but.onmouseout=function(){ this.className="app_grid_title___gray button buttonDisabled"};
			but.onblur=function(){ this.className="app_grid_title___gray button buttonDisabled"};
		}
		else
		{
			but.className="app_grid_title___gray button";
			but.onmouseover=function(){ this.className="app_grid_title___gray button buttonHover"};
			but.onmouseout=function(){ this.className="app_grid_title___gray button"};
			but.onblur=function(){ this.className="app_grid_title___gray button"};
		}
	};
    this.showPhpinfo=function()
    {
        var panel = new leimnud.module.panel();
        panel.options={
            title:"PHP info",
            position:{center:true},
            size:{w:700,h:document.body.clientHeight-50},
            fx:{modal:true}
        };
        panel.make();
  		var r = new leimnud.module.rpc.xmlhttp({
			url	    :"install.php",
			method	:"POST",
			args	:"phpinfo=true"
		});
        r.callback=function(rpc)
        {
            panel.addContent(rpc.xmlhttp.responseText);
        };
        r.make();
    };
	this.expand(this);
}
