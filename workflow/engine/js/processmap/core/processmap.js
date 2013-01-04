var PROCESSMAP_STEP_EDIT = false;
var PROCESSMAP_USER_EDIT = false;

var processmapOutputsPanel;
var _client = getClientWindowSize();

var processmap=function(){
  this.data={
    load:function()
    {
      //setTimeout(this.parent.closure({instance:this,method:this.data.render.base}),1000);
      var r = new leimnud.module.rpc.xmlhttp({
        url:this.options.dataServer,
        args:"action=load&data="+{uid:this.options.uid,mode:this.options.rw,ct:this.options.ct}.toJSONString()
      });
      r.callback=this.data.render.base
      r.make();
    },
    render:{
      buildingBlocks:{
        injector:function(lanzado)
        {
          Wx = (lanzado=='dynaforms' || lanzado=='triggers' || lanzado=='outputs') ?600 : 500;
          Hx = 460;

          if (lanzado == "reportTables") {
            Wx = _client.width - 30;
            Hx = _client.height - 15;
          }

          var bbk = {
            dynaforms:1,
            messages:1,
            inputs:1,
            outputs:1,
            webbots:1
          };
          this.observers.menu.update();
          if(!this.panels.buildingBlocks)
          {
            this.panels.buildingBlocks=new leimnud.module.panel();
            this.panels.buildingBlocks.options={
              limit :true,
              size  :{w:Wx,h:Hx},
              position:{x:0,y:10,center:true},
              title :"",
              theme :"processmaker",
              //target  :this.options.target,
              statusBar:false,
              //titleBar:false,
              control :{drag:false,resize:false,close:true, drag:true},
              fx  :{opacity:false,rolled:false,modal:true, drag:true}
            };
            this.panels.buildingBlocks.make();
            this.panels.buildingBlocks.events={
              remove:function()
              {
                delete this.panels.buildingBlocks;
              }.extend(this)
            };
            //this.panels.buildingBlocks.elements.modal.onmouseup=this.panels.buildingBlocks.remove;
          }
          else
          {
            this.panels.buildingBlocks.clearContent();
          }
          var bbk ={
              outputs:function(){
                var panel = this.panels.buildingBlocks;
                panel.addContentTitle(G_STRINGS.ID_PROCESSMAP_OUTPUT_DOCUMENTS)
                panel.loader.show();
                var r = new this.parent.module.rpc.xmlhttp({
                  url:this.options.dataServer,
                  args:"action=outputs&data="+{pro_uid:this.options.uid}.toJSONString()
                });
                r.callback=function(rpc){
                  panel.loader.hide();
                  var scs=rpc.xmlhttp.responseText.extractScript();
                  panel.addContent(rpc.xmlhttp.responseText);
                  scs.evalScript();
                  this.buildLoading=false;
                }.extend(this);
                r.make();

                processmapOutputsPanel = panel;
              }.extend(this),
              inputs:function()
              {
                var panel = this.panels.buildingBlocks;
                panel.addContentTitle(G_STRINGS.ID_PROCESSMAP_INPUT_DOCUMENTS);
                panel.loader.show();
                var r = new this.parent.module.rpc.xmlhttp({
                  url:this.options.dataServer,
                  args:"action=inputs&data="+{pro_uid:this.options.uid}.toJSONString()
                });
                r.callback=function(rpc){
                  panel.loader.hide();
                  var scs=rpc.xmlhttp.responseText.extractScript();
                  panel.addContent(rpc.xmlhttp.responseText);
                  scs.evalScript();
                  this.buildLoading=false;
                }.extend(this);
                r.make();
              }.extend(this),
              triggers:function(){
                var panel = this.panels.buildingBlocks;
                panel.addContentTitle(G_STRINGS.ID_PROCESSMAP_TRIGGERS);
                panel.clearContent();
                panel.loader.show();
                var r = new this.parent.module.rpc.xmlhttp({
                  url:this.options.dataServer,
                  args:"action=triggers&data="+{pro_uid:this.options.uid}.toJSONString()
                });
                r.callback=function(rpc){
                  panel.loader.hide();
                  var scs=rpc.xmlhttp.responseText.extractScript();
                  panel.addContent(rpc.xmlhttp.responseText);
                  scs.evalScript();
                  this.buildLoading=false;
                }.extend(this);
                r.make();
              }.extend(this),
              messages:function(){
                var panel = this.panels.buildingBlocks;
                panel.addContentTitle(G_STRINGS.ID_PROCESSMAP_MESSAGES);
                panel.clearContent();
                panel.loader.show();
                var r = new this.parent.module.rpc.xmlhttp({
                  url:this.options.dataServer,
                  args:"action=messages&data="+{pro_uid:this.options.uid}.toJSONString()
                });
                r.callback=function(rpc){
                  panel.loader.hide();
                  var scs=rpc.xmlhttp.responseText.extractScript();
                  panel.addContent(rpc.xmlhttp.responseText);
                  scs.evalScript();
                  this.buildLoading=false;
                }.extend(this);
                r.make();
              }.extend(this),
              reportTables2:function(){
                var panel = this.panels.buildingBlocks;
                panel.addContentTitle(G_STRINGS.ID_PROCESSMAP_REPORT_TABLES);
                panel.clearContent();
                panel.loader.show();
                var r = new this.parent.module.rpc.xmlhttp({
                  url:this.options.dataServer,
                  args:"action=reportTables&data="+{pro_uid:this.options.uid}.toJSONString()
                });
                r.callback=function(rpc){
                  panel.loader.hide();
                  var scs=rpc.xmlhttp.responseText.extractScript();
                  panel.addContent(rpc.xmlhttp.responseText);
                  scs.evalScript();
                  this.buildLoading=false;
                }.extend(this);
                r.make();
              }.extend(this),
              reportTables:function(){
                  var url = "../pmTables?PRO_UID=" + this.options.uid;
                  var isIE = (navigator.userAgent.toLowerCase().indexOf("msie") != -1)? 1 : 0;

                  if (isIE == 1) {
                      this.panels.buildingBlocks.remove();

                      //var w = _client.width - 20;
                      //var h = _client.height - 300;
                      var w = screen.width - 150;
                      var h = screen.height - 300;

                      var windowAux = window.open(url, "reportTable", "width=" + w + ", height=" + h + ", resizable=no, toolbar=no, menubar=no, scrollbars=yes, status=no, location=no, left=" + ((screen.width / 2) - (w / 2)) + ", top=" + ((screen.height / 2) - (h / 2) + 50));
                  } else {
                      var panel = this.panels.buildingBlocks;
                      panel.addContentTitle("");
                      panel.clearContent();

                      var iframe = document.createElement("iframe");
                      iframe.setAttribute("id", "reportTablesIframe");
                      iframe.src = url;
                      iframe.frameBorder = 0;
                      iframe.style.width  = _client.width - 40;
                      iframe.style.height = _client.height - 70;

                      panel.addContent(iframe);
                  }
              }.extend(this),
              dynaforms:function(){
                var panel = this.panels.buildingBlocks;
                panel.addContentTitle(G_STRINGS.ID_DYNAFORMS);
                panel.loader.show();
                var r = new this.parent.module.rpc.xmlhttp({
                  url:this.options.dataServer,
                  args:"action=dynaforms&data="+{pro_uid:this.options.uid}.toJSONString()
                });
                r.callback=function(rpc){
                  panel.loader.hide();
                  var scs=rpc.xmlhttp.responseText.extractScript();
                  panel.addContent(rpc.xmlhttp.responseText);
                  scs.evalScript();
                  this.buildLoading=false;
                }.extend(this)
                r.make();
              }.extend(this)
            };
          bbk[lanzado]();
        },
        panel:function()
          {
           /* Toolbar Begin */
           var panel;
           panel = this.panels.toolbar=new leimnud.module.panel();
           this.panels.toolbar.options={
            limit :true,
            size :{w:220,h:31},
            position:{x:this.options.target.clientWidth-278,y:4},
            title :"",
            theme :"processmaker",
            target :this.options.target,
            //limit :true,
            titleBar:false,
            statusBar:false,
            elementToDrag:"content",
            cursorToDrag:"default",
            control :{drag:true,resize:false},
            fx :{opacity:true,shadow:false}
           };
           panel.setStyle={
            containerWindow:{border:"1px solid buttonshadow"},
            frontend:{backgroundColor:"buttonface"},
            content:{border:"1px solid transparent",backgroundColor:"transparent",margin:0,overflow:"hidden",padding:1}
           };
           this.panels.toolbar.make();
           var div = document.createElement("div");
           this.parent.dom.setStyle(div,{
            textAlign:"center"
           });
               var dr1 = document.createElement("img");
               dr1.src = this.options.images_dir + "0.gif";
               dr1.title = _("ID_PROCESSMAP_SEQUENTIAL");
               div.appendChild(dr1);
               //dr1.style.marginTop = 7;
               //div.appendChild(document.createElement("p"));

               var dr2 = document.createElement("img");
               //dr2.style.marginTop = 7;
               dr2.src = this.options.images_dir + "1.gif";
               dr2.title = _("ID_PROCESSMAP_SELECTION");
               div.appendChild(dr2);
               //div.appendChild(document.createElement("p"));

               var dr3 = document.createElement("img");
               dr3.src = this.options.images_dir + "2.gif";
               dr3.title = _("ID_PROCESSMAP_EVALUATION");
               //dr3.style.marginTop = 7;
               div.appendChild(dr3);
               //div.appendChild(document.createElement("p"));

               var dr4 = document.createElement("img");
               dr4.src = this.options.images_dir + "3.gif";
               dr4.title = _("ID_PROCESSMAP_PARALLEL_FORK");
               //dr4.style.marginTop = 7;
               div.appendChild(dr4);
               //div.appendChild(document.createElement("p"));

               var dr5 = document.createElement("img");
               dr5.src = this.options.images_dir + "4.gif";
               dr5.title = _("ID_PROCESSMAP_PARALLEL_EVALUATION_FORK");
               //dr5.style.marginTop = 7;
               div.appendChild(dr5);

               var dr6 = document.createElement("img");
               dr6.src = this.options.images_dir + "5.gif";
               dr6.title = _("ID_PROCESSMAP_PARALLEL_JOIN");
               div.appendChild(dr6);

               var fin = document.createElement("img");
               fin.src = this.options.images_dir + "6.gif";
               fin.title = _("ID_END_OF_PROCESS");
               div.appendChild(fin);

               var ini = document.createElement("img");
               ini.src = this.options.images_dir + "7.gif";
               ini.title = _("ID_START_TASK");
               div.appendChild(ini);

             /*var dis = document.createElement("img");
               dis.src = this.options.images_dir+"8.gif";
               dis.title = "Discriminator";
               div.appendChild(dis);*/

               [dr1,dr2,dr3,dr4,dr5,dr6,fin,ini/*,dis*/].map(function(el){
                el.className ="processmap_toolbarItem___"+this.options.theme
               }.extend(this));
               this.dragables.derivation = new this.parent.module.drag({ //Add to enable dragging of image from panel
               elements:[dr1,dr2,dr3,dr4,dr5,dr6,fin,ini/*,dis*/],
                fx:{
                 type : "clone",
                 target : this.panels.editor.elements.content,
                 zIndex : 11
                }
               });
               this.dragables.derivation.typesDerivation=["simple","double","conditional","conditional1","conditional2","conditional3","final","initial"/*,"discriminator"*/];
               this.dragables.derivation.events={
                init :[function(){
                 this.dragables.derivation.noDrag=true;
                }.extend(this)],
                move:this.dropables.derivation.capture.args(this.dragables.derivation),
                finish : this.parent.closure({instance:this,method:function(){
                 //clearInterval(this.timeToOutControl);

                 this.parent.dom.remove(this.dropables.derivation.drag || this.dragables.derivation.currentElementDrag);
                 this.parent.dom.remove(this.dragables.derivation.currentElementDrag);
                 if(this.dropables.derivation.selected!==false)
                 {
                  this.dropables.derivation.launchEvents(this.dropables.derivation.elements[this.dropables.derivation.selected].events.out);
                  vAux = this.dropables.derivation.launchEvents(this.dropables.derivation.elements[this.dropables.derivation.selected].events.click);
                  this.dropables.derivation.selected = false;
                  return vAux;
                 }
                 else
                 {
                  this.dragables.derivation.noDrag=false;
                 }
                }})
               };
               this.dragables.derivation.make();
               //drg.options.elements=[];
               this.parent.dom.setStyle([dr1,dr2,dr3,dr4,dr5,dr6,fin,ini/*,dis*/],{
                cursor:"move"
               });
           panel.loader.hide();
           panel.addContent(div);

           leimnud._panel=['O'],leimnud.ipanel=0;
           /* Toolbar End  */
          },
          components:{

                                        }
                                       },
      base:function(xml)
      {
        this.panels.editor.loader.hide();
        this.data.db=xml.xmlhttp.responseText.parseJSON().concat({
          //derivations :["Sequential","Evaluate (manual)","Evaluate (auto)","Parallel (fork)","Parallel by evaluation (fork)","Parallel (join)"],
        });
        this.data.db.subprocess=[];

        this.panels.editor.addContentStatus(G_STRINGS.ID_PROCESSMAP_LOADING);
        if(this.options.rw===true)
        {
        this.menu = new this.parent.module.app.menuRight();
        this.menu.make({
          target:this.panels.editor.elements.content,
          width:201,
          theme:this.options.theme,
          menu:[
          {image:"/images/edit.gif",text:G_STRINGS.ID_PROCESSMAP_EDIT_PROCESS,launch:function(event){
            this.tmp.editProcessPanel = panel =new leimnud.module.panel();
            panel.options={
              limit :true,
              size  :{w:700,h:520},
              position:{x:50,y:50,center:true},
              title :G_STRINGS.ID_PROCESSMAP_EDIT_PROCESS+": "+ moldTitle(this.data.db.title.label,700),//this.data.db.title.label,s
              theme :this.options.theme,
              control :{close:true,resize:false},fx:{modal:true},
              statusBar:false,
              fx  :{shadow:true,modal:true}
            };
            panel.make();
            panel.loader.show();
            var r = new leimnud.module.rpc.xmlhttp({
              url:this.options.dataServer,
              args:"action=process_Edit&data="+{
                pro_uid :this.options.uid
              }.toJSONString()
            });
            r.callback=function(rpc,panel)
            {
              panel.loader.hide();
              var scs = rpc.xmlhttp.responseText.extractScript();
              panel.addContent(rpc.xmlhttp.responseText);
              scs.evalScript();
              //Pm.objeto.innerHTML="asdasd";
            }.extend(this,panel);
            r.make();
          }.extend(this)},
          {image:"/images/edit.gif",text:G_STRINGS.ID_PROCESSMAP_EXPORT_PROCESS,launch:function(event){
            this.tmp.exportProcessPanel = panel =new leimnud.module.panel();
            panel.options={
              limit :true,
              size  :{w:600,h:230},
              position:{x:50,y:50,center:true},
              title :G_STRINGS.ID_PROCESSMAP_EXPORT_PROCESS+": "+moldTitle(this.data.db.title.label,600),//this.data.db.title.label,
              theme :this.options.theme,
              control :{close:true,resize:false},fx:{modal:true},
              statusBar:false,
              fx  :{shadow:true,modal:true}
            };
            panel.make();
            panel.loader.show();
            var r = new leimnud.module.rpc.xmlhttp({
              url:this.options.dataServer,
              args:"action=process_Export&processMap=1&data="+{
                pro_uid :this.options.uid
              }.toJSONString()
            });
            r.callback=function(rpc,panel)
            {
              panel.loader.hide();
              var scs = rpc.xmlhttp.responseText.extractScript();
              panel.addContent(rpc.xmlhttp.responseText);
              scs.evalScript();
            }.extend(this,panel);
            r.make();
          }.extend(this)},
          {separator:true},
          {image:"/images/add.png",text:G_STRINGS.ID_PROCESSMAP_ADD_TASK,launch:this.addTask.extend(this,{tp:'task'})},
          {image:"/images/subProcess.png",text:G_STRINGS.ID_PROCESSMAP_ADD_SUBPROCESS,launch:this.addTask.extend(this,{tp:'subprocess'})},  //add subprocess whith blabla
                                        //{image:"/images/add.png",text:"Add Task Hidden",launch:this.addTask.extend(this,{tp:'hidden'})},  //add subprocess whith blabla
          {image:"/images/addtext.png",text:G_STRINGS.ID_PROCESSMAP_ADD_TEXT,launch:this.addText.extend(this)},
          {image:"/images/linhori.png",text:G_STRINGS.ID_PROCESSMAP_HORIZONTAL_LINE,launch:this.addGuide.extend(this,"horizontal")},
          {image:"/images/linver.png",text:G_STRINGS.ID_PROCESSMAP_VERTICAL_LINE,launch:this.addGuide.extend(this,"vertical")},
          {image:"/images/delete.png",text:G_STRINGS.ID_PROCESSMAP_DELETE_ALL_LINES,launch:function(event,index){
            index = this.parent.browser.isIE?event:index;
            new leimnud.module.app.confirm().make({
              label:G_STRINGS.ID_PROCESSMAP_CONFIRM_DELETE_ALL_LINES,
              action:function()
              {
                for(var i=0;i<this.data.db.guide.length;i++)
                {
                  this.parent.dom.remove(this.data.db.guide[i].object.elements.guide);
                }
                var r = new leimnud.module.rpc.xmlhttp({
                  url:this.options.dataServer,
                  args:"action=deleteGuides&data="+{
                    pro_uid:this.options.uid
                  }.toJSONString()
                });
                r.make();
              }.extend(this)
            });
          }.extend(this)},
          {separator:true},
          {image:"/images/object_permission.gif",text:G_STRINGS.ID_OBJECT_PERMISSIONS,launch:function(event){
            this.tmp.editProcessPanel = panel =new leimnud.module.panel();
            panel.options={
              limit :true,
              size  :{w:850,h:480},
              position:{x:50,y:50,center:true},
              title :G_STRINGS.ID_OBJECT_PERMISSIONS,
              theme :this.options.theme,
              control :{close:true,resize:false},fx:{modal:true},
              statusBar:false,
              fx  :{shadow:true,modal:true}
            };
            panel.make();
            panel.loader.show();
            var r = new leimnud.module.rpc.xmlhttp({
              url:this.options.dataServer,
              args:"action=objectPermissions&data="+{
                pro_uid :this.options.uid
              }.toJSONString()
            });
            r.callback=function(rpc,panel)
            {
              panel.loader.hide();
              var scs = rpc.xmlhttp.responseText.extractScript();
              panel.addContent(rpc.xmlhttp.responseText);
              scs.evalScript();
              //Pm.objeto.innerHTML="asdasd";
            }.extend(this,panel);
            r.make();
          }.extend(this)},
          {image:"/images/users.png",text:G_STRINGS.ID_PROCESSMAP_PSUPERVISORS,submenu:[
            {image:"/images/users.png",text:G_STRINGS.ID_PROCESSMAP_PROCESS_SUPERVISORS,launch:function(event){
            this.tmp.editProcessPanel = panel =new leimnud.module.panel();
            panel.options={
              limit :true,
              size  :{w:500,h:300},
              position:{x:50,y:50,center:true},
              title :G_STRINGS.ID_PROCESSMAP_PROCESS_SUPERVISORS,
              theme :this.options.theme,
              control :{close:true,resize:false},fx:{modal:true},
              statusBar:false,
              fx  :{shadow:true,modal:true}
            };
            panel.make();
            panel.loader.show();
            var r = new leimnud.module.rpc.xmlhttp({
              url:this.options.dataServer,
              args:"action=process_User&data="+{
                pro_uid :this.options.uid
              }.toJSONString()
            });
            r.callback=function(rpc,panel)
            {
              panel.loader.hide();
              var scs = rpc.xmlhttp.responseText.extractScript();
              panel.addContent(rpc.xmlhttp.responseText);
              scs.evalScript();
              //Pm.objeto.innerHTML="asdasd";
            }.extend(this,panel);
            r.make();
          }.extend(this)},
          {image:"/images/dynaforms.gif",text:G_STRINGS.ID_PROCESSMAP_SUPERVISORS_DYNAFORMS,launch:function(event){
            this.tmp.editProcessPanel = panel =new leimnud.module.panel();
            panel.options={
              limit :true,
              size  :{w:500,h:380},
              position:{x:50,y:50,center:true},
              title :G_STRINGS.ID_PROCESSMAP_SUPERVISORS_DYNAFORMS,
              theme :this.options.theme,
              control :{close:true,resize:false},fx:{modal:true},
              statusBar:false,
              fx  :{shadow:true,modal:true}
            };
            panel.make();
            panel.loader.show();
            var r = new leimnud.module.rpc.xmlhttp({
              url:this.options.dataServer,
              args:"action=supervisorDynaforms&data="+{
                pro_uid :this.options.uid
              }.toJSONString()
            });
            r.callback=function(rpc,panel)
            {
              panel.loader.hide();
              var scs = rpc.xmlhttp.responseText.extractScript();
              panel.addContent(rpc.xmlhttp.responseText);
              scs.evalScript();
              //Pm.objeto.innerHTML="asdasd";
            }.extend(this,panel);
            r.make();
          }.extend(this)},
          {image:"/images/inputdocument.gif",text:G_STRINGS.ID_PROCESSMAP_SUPERVISORS_INPUTS,launch:function(event){
            this.tmp.editProcessPanel = panel =new leimnud.module.panel();
            panel.options={
              limit :true,
              size  :{w:500,h:380},
              position:{x:50,y:50,center:true},
              title :G_STRINGS.ID_PROCESSMAP_SUPERVISORS_INPUTS,
              theme :this.options.theme,
              control :{close:true,resize:false},fx:{modal:true},
              statusBar:false,
              fx  :{shadow:true,modal:true}
            };
            panel.make();
            panel.loader.show();
            var r = new leimnud.module.rpc.xmlhttp({
              url:this.options.dataServer,
              args:"action=supervisorInputs&data="+{
                pro_uid :this.options.uid
              }.toJSONString()
            });
            r.callback=function(rpc,panel)
            {
              panel.loader.hide();
              var scs = rpc.xmlhttp.responseText.extractScript();
              panel.addContent(rpc.xmlhttp.responseText);
              scs.evalScript();
              //Pm.objeto.innerHTML="asdasd";
            }.extend(this,panel);
            r.make();
          }.extend(this)}
          ]},
          {separator:true},
          {image:"/images/dynaforms.gif",text:G_STRINGS.ID_WEB_ENTRY,launch:function(event){
            this.tmp.editProcessPanel = panel =new leimnud.module.panel();
            panel.options={
              limit :true,
              size  :{w:500,h:380},
              position:{x:50,y:50,center:true},
              title :G_STRINGS.ID_WEB_ENTRY,
              theme :this.options.theme,
              control :{close:true,resize:true},fx:{modal:true},
              statusBar:false,
              fx  :{shadow:true,modal:true}
            };
            panel.make();
            panel.loader.show();
            var r = new leimnud.module.rpc.xmlhttp({
              url:this.options.dataServer,
              args:"action=webEntry&data="+{
                pro_uid :this.options.uid
              }.toJSONString()
            });
            r.callback=function(rpc,panel)
            {
              panel.loader.hide();
              var scs = rpc.xmlhttp.responseText.extractScript();
              panel.addContent(rpc.xmlhttp.responseText);
              scs.evalScript();
              //Pm.objeto.innerHTML="asdasd";
            }.extend(this,panel);
            r.make();
          }.extend(this)},
          {image:"/images/tracker.gif",text:G_STRINGS.ID_CASE_TRACKER,submenu:[
            {image:"/images/tracker.gif",text:G_STRINGS.ID_CASE_TRACKER_PROPERTIES,launch:function(event){
            this.tmp.caseTrackerPanel = panel =new leimnud.module.panel();
            panel.options={
              limit :true,
              size  :{w:300,h:180},
              position:{x:50,y:50,center:true},
              title :G_STRINGS.ID_CASE_TRACKER,
              theme :this.options.theme,
              control :{close:true,resize:false},fx:{modal:true},
              statusBar:false,
              fx  :{shadow:true,modal:true}
            };
            panel.make();
            panel.loader.show();
            var r = new leimnud.module.rpc.xmlhttp({
              url:this.options.dataServer,
              args:"action=caseTracker&data="+{
                pro_uid :this.options.uid
              }.toJSONString()
            });
            r.callback=function(rpc,panel)
            {
              panel.loader.hide();
              var scs = rpc.xmlhttp.responseText.extractScript();
              panel.addContent(rpc.xmlhttp.responseText);
              scs.evalScript();
              //Pm.objeto.innerHTML="asdasd";
            }.extend(this,panel);
            r.make();
          }.extend(this)},

          {image:"/images/tracker.gif",text:G_STRINGS.ID_CASE_TRACKER_OBJECTS,launch:function(event){
            this.tmp.caseTrackerPanel = panel =new leimnud.module.panel();
            panel.options={
              limit :true,
              size  :{w:500,h:380},
              position:{x:50,y:50,center:true},
              title :G_STRINGS.ID_CASE_TRACKER_OBJECTS,
              theme :this.options.theme,
              control :{close:true,resize:false},fx:{modal:true},
              statusBar:false,
              fx  :{shadow:true,modal:true}
            };
            panel.make();
            panel.loader.show();
            var r = new leimnud.module.rpc.xmlhttp({
              url:this.options.dataServer,
              args:"action=caseTrackerObjects&data="+{
                pro_uid :this.options.uid
              }.toJSONString()
            });
            r.callback=function(rpc,panel)
            {
              panel.loader.hide();
              var scs = rpc.xmlhttp.responseText.extractScript();
              panel.addContent(rpc.xmlhttp.responseText);
              scs.evalScript();
              //Pm.objeto.innerHTML="asdasd";
            }.extend(this,panel);
            r.make();
          }.extend(this)}
          ]},
          {image:"/images/folder.gif",text:G_STRINGS.ID_PROCESS_FILES_MANAGER,launch:function(event){
            this.tmp.processFilesManagerPanel = panel =new leimnud.module.panel();
            panel.options={
              limit :true,
              size  :{w:500,h:380},
              position:{x:50,y:50,center:true},
              title :G_STRINGS.ID_PROCESS_FILES_MANAGER,
              theme :this.options.theme,
              control :{close:true,resize:false},fx:{modal:true},
              statusBar:false,
              fx  :{shadow:true,modal:true}
            };
            panel.make();
            panel.loader.show();
            var r = new leimnud.module.rpc.xmlhttp({
              url:this.options.dataServer,
              args:"action=processFilesManager&data="+{
                pro_uid :this.options.uid
              }.toJSONString()
            });
            r.callback=function(rpc,panel)
            {
              panel.loader.hide();
              var scs = rpc.xmlhttp.responseText.extractScript();
              panel.addContent(rpc.xmlhttp.responseText);
              scs.evalScript();
              //Pm.objeto.innerHTML="asdasd";
            }.extend(this,panel);
            r.make();
          }.extend(this)} ,
        {image:"/images/events.gif",text:G_STRINGS.ID_EVENTS,submenu:[
/*
           {image:"/images/event_message.png",text:"start message event",launch:function(event){
             this.tmp.editProcessPanel = panel =new leimnud.module.panel();
             panel.options={
              limit :true,
              size :{w:500,h:380},
              position:{x:50,y:50,center:true},
              title :G_STRINGS.ID_WEB_ENTRY,
              theme :this.options.theme,
              control :{close:true,resize:false},fx:{modal:true},
              statusBar:false,
              fx :{shadow:true,modal:true}
             };
             panel.make();
             panel.loader.show();
             var r = new leimnud.module.rpc.xmlhttp({
              url:this.options.dataServer,
              args:"action=webEntry&data="+{
               pro_uid :this.options.uid
              }.toJSONString()
             });
             r.callback=function(rpc,panel)
             {
              panel.loader.hide();
              var scs = rpc.xmlhttp.responseText.extractScript();
              panel.addContent(rpc.xmlhttp.responseText);
              scs.evalScript();
              //Pm.objeto.innerHTML="asdasd";
             }.extend(this,panel);
             r.make();
            }.extend(this)},

             {image:"/images/event_timer.png",text:"Start timer event",launch:function(event){
             this.tmp.eventsPanel = panel =new leimnud.module.panel();
             panel.options={
              limit :true,
              size :{w:830,h:800},
              position:{x:50,y:50,center:true},
              title :"CASES SCHEDULER",
              theme :this.options.theme,
              control :{close:true,resize:false},fx:{modal:true},
              statusBar:false,
              fx :{shadow:true,modal:true}
             };
             panel.make();
             panel.loader.show();
             var r = new leimnud.module.rpc.xmlhttp({
              url:this.options.dataServer,
              args:"action=case_scheduler&PRO_UID="+this.options.uid
             });
             r.callback=function(rpc,panel)
             {
              panel.loader.hide();
              var scs = rpc.xmlhttp.responseText.extractScript();
              panel.addContent(rpc.xmlhttp.responseText);
              scs.evalScript();
              //Pm.objeto.innerHTML="asdasd";
             }.extend(this,panel);
             r.make();
             }.extend(this)},
*/
            {
              image:"/images/event_messageThrow.png",text:G_STRINGS.ID_INTERMEDIATE_MESSAGE_EVENT,launch:function(event){
              this.tmp.eventsPanel = panel =new leimnud.module.panel();
              panel.options={
                limit :true,
                size :{w:700,h:380},
                position:{x:50,y:50,center:true},
                title :G_STRINGS.ID_EVENT_MESSAGE,
                theme :this.options.theme,
                control :{close:true,resize:false},fx:{modal:true},
                statusBar:false,
                fx :{shadow:true,modal:true}
              };
              panel.make();
              panel.loader.show();
              var r = new leimnud.module.rpc.xmlhttp({
                url:this.options.dataServer,
                args:"action=events&data="+{
                  pro_uid :this.options.uid,
                  type:"message"
                }.toJSONString()
              });
              r.callback=function(rpc,panel)
              {
                panel.loader.hide();
                var scs = rpc.xmlhttp.responseText.extractScript();
                panel.addContent(rpc.xmlhttp.responseText);
                scs.evalScript();
                //Pm.objeto.innerHTML="asdasd";
              }.extend(this,panel);
              r.make();
              }.extend(this)
            },
            {
              image:"/images/event_conditional.png",text:G_STRINGS.ID_INTERMEDIATE_CONDITIONAL_EVENT ,launch:function(event){
              this.tmp.eventsPanel = panel =new leimnud.module.panel();
              panel.options={
                limit :true,
                size :{w:700,h:380},
                position:{x:50,y:50,center:true},
                title :G_STRINGS.ID_EVENT_MESSAGE,
                theme :this.options.theme,
                control :{close:true,resize:false},fx:{modal:true},
                statusBar:false,
                fx :{shadow:true,modal:true}
              };
              panel.make();
              panel.loader.show();
              var r = new leimnud.module.rpc.xmlhttp({
                url:this.options.dataServer,
                args:"action=events&data="+{
                  pro_uid :this.options.uid,
                  type:"conditional"
                }.toJSONString()
              });
              r.callback=function(rpc,panel)
              {
                panel.loader.hide();
                var scs = rpc.xmlhttp.responseText.extractScript();
                panel.addContent(rpc.xmlhttp.responseText);
                scs.evalScript();
                //Pm.objeto.innerHTML="asdasd";
              }.extend(this,panel);
              r.make();
              }.extend(this)
            },
            {
              image:"/images/event_timer.png",text:G_STRINGS.ID_INTERMEDIATE_TIMER_EVENT,launch:function(event){
              this.tmp.eventsPanel = panel =new leimnud.module.panel();
              panel.options={
                limit :true,
                size :{w:700,h:380},
                position:{x:50,y:50,center:true},
                title :G_STRINGS.ID_EVENT_MULTIPLE,
                theme :this.options.theme,
                control :{close:true,resize:false},fx:{modal:true},
                statusBar:false,
                fx :{shadow:true,modal:true}
              };
              panel.make();
              panel.loader.show();
              var r = new leimnud.module.rpc.xmlhttp({
                url:this.options.dataServer,
                args:"action=events&data="+{
                  pro_uid :this.options.uid,
                  type:"multiple"
                }.toJSONString()
              });
              r.callback=function(rpc,panel)
              {
                panel.loader.hide();
                var scs = rpc.xmlhttp.responseText.extractScript();
                panel.addContent(rpc.xmlhttp.responseText);
                scs.evalScript();
                //Pm.objeto.innerHTML="asdasd";
                }.extend(this,panel);
                r.make();
            }.extend(this)}
          ]}

          ]
        });
        this.observers.menu.register(this.parent.closure({instance:this.menu,method:this.menu.remove}),this.menu);
        }
        this.data.render.task();
        if (maximunX > this.options.size.w && document.getElementById('pm_separator_div')) {
          var pm_separator_div = document.getElementById('pm_separator_div');
          pm_separator_div.style.width = maximunX + 200;
        }
      },
      task:function()
        {
         var lngt = this.data.db.task.length;
         for(var i =0;i<lngt;i++)
         {
          //console.log(this.data.db.task[i]);

            var tt = ((this.data.db.task[i].task_type==='NORMAL') || (this.data.db.task[i].task_type==='ADHOC'))?'task':'subprocess';
            if(this.data.db.task[i].task_type==='HIDDEN'){
              tt = 'hidden';
            }
          //this.parent.exec(this.data.build.task,[this.data.db.task[i],i],false,this);
           this.data.build.task(i,{tp:tt});

         }
         this.data.render.taskINI();
         this.data.render.guide();
         //this.parent.exec(this.data.build.derivation,false,false,this);
        },
      taskINI:function()
      {
        var lngt = this.data.db.task.length;
        for(var i =0;i<lngt;i++)
        {
          var task=this.data.db.task[i];
          if(task.taskINI===true)
          {
            this.parent.dom.setStyle(task.object.elements.init,{
              background:"url("+this.options.images_dir+"inicio.gif)"
            });
          }
        }
        return true;
      },
      setTaskINI:function(option)
      {
        var task = this.data.db.task[this.tools.getIndexOfUid(option.task)];
        task.taskINI=option.value;
        this.parent.dom.setStyle(task.object.elements.init,{
          background:((task.taskINI===true)?"url("+this.options.images_dir+"inicio.gif)":"")
        });
      },
      guide:function()
      {
        for(var i=0;i<this.data.db.guide.length;i++)
        {
          //this.parent.exec(this.data.build.guide,[this.data.db.guide[i],i],false,this);
          //this.parent.exec(this.data.build.guide,[i],false,this);
          this.data.build.guide(i);
          //this.parent.exec(this.data.build.guide,[2],false,this);
        }
        this.data.render.title();
      },
      title:function()
      {
        this.data.build.title();
        this.data.render.text();
      },
      deleteDerivation:function(uid,rec,spec)
      {
        var task = this.data.db.task[this.tools.getIndexOfUid(uid)];
        spec   = (typeof spec!=="number")?false:spec;
        var deri = task.derivation;
        for(var i=0;i<deri.to.length;i++)
        {
          if(spec===false || (spec!==false && spec===i))
          {
            if(deri.to[i].task==="-1" || deri.to[i].task==="-2")
            {
              this.parent.dom.setStyle(task.object.elements[(deri.to.length>1)?'derivationBottom':'derivation'],{
                background:""
              });

            }
            else
            {
              deri.to[i].object.line.remove();
              this.observers.lineas.unregister(deri.to[i].object.indexObserver);
            }
            if(deri.type===5 || deri.type===8)
            {
              var toTask = this.data.db.task[this.tools.getIndexOfUid(deri.to[i].task)];
              if (typeof(toTask) != 'undefined') {
                toTask.object.inJoin = toTask.object.inJoin-1;
                if(toTask.object.inJoin===0)
                {
                  this.parent.dom.setStyle(toTask.object.elements.init,{
                    backgroundPosition:"0 0",
                    background:""
                  });
                }
              }
            }
          }
        }
        this.parent.dom.setStyle(task.object.elements.derivation,{
          background:""
        });
        task.derivation={to:[]};

        /* Delete derivation recursive */
        if(rec)
        {
          var tdb = this.data.db.task;
          for(var i=0;i<tdb.length;i++)
          {
            var der = tdb[i].derivation.to || [];
            for(var j=0;j<der.length;j++)
            {
              if(der[j].task===uid)
              {
                this.data.render.deleteDerivation(tdb[i].uid,false,j);
              }
            }
          }
        }
      },
      preDerivation:function(uid)
      {
        var tmS;
        var typeDerivation = this.dragables.derivation.currentElementInArray;
        if(typeDerivation===6){

          var vars=this.data.db.task[uid];
          var vtd = {
            type:0,
            tas_uid:vars.uid,
            pro_uid:this.options.uid,
            data:["-1"],
            next_task:'-1'
          }
                this.data.build.derivation(vtd);
                vtd['delete'] = true;
                var r = new leimnud.module.rpc.xmlhttp({
                      url:this.options.dataServer,
                      args:"action=saveNewPattern&data="+vtd.toJSONString()
                });
                r.make();
          this.inDerivationDrag=false;
          this.dragables.derivation.noDrag=false;
          return false;
        }
        else if(typeDerivation===7) {
          var vars=this.data.db.task[uid];
          if (vars.task_type != 'SUBPROCESS') {
            this.data.render.setTaskINI({task:vars.uid,value:true});
            this.inDerivationDrag=false;
            this.dragables.derivation.noDrag=false;
            var r = new leimnud.module.rpc.xmlhttp({
              url:"../tasks/tasks_Ajax",
              args:"function=saveTaskData&oData="+{
                TAS_START:"TRUE",
                TAS_UID:vars.uid
              }.toJSONString()
            });
            r.make();
          }
          else {
            this.inDerivationDrag=false;
            this.dragables.derivation.noDrag=false;
          }
          return false;
        }
        this.observers.menu.update();
        tmS = this.derivationArrowToDrop = document.createElement("div");
        this.parent.dom.setStyle(tmS,{
          position:"absolute",
          width : 10,
          height  : 10,
          zIndex  : 12,
          overflow: "hidden",
          backgroundColor:"red"
        });
        this.panels.editor.elements.content.appendChild(tmS);
        var ln;
        ln = this.derivationLineToDrop = new this.parent.module.app.line({
          elements  :[this.data.db.task[uid].object.elements.task,tmS],
          target    :this.panels.editor.elements.content,
          color   :"green",
          zIndex    :15
        });
        ln.make();
        this.observers.lineas.register(this.parent.closure({instance:ln,method:ln.update}),ln);
        this.parent.event.add(this.data.db.task[uid].object.elements.task,"mouseover",this.parent.closure({instance:this,method:function(evt,arrow,lin,evi)
        {
          var ec  = this.parent.dom.position(this.panels.editor.elements.content);
          var mou = this.parent.dom.mouse(window.event || evt);
          this.parent.dom.setStyle(arrow,{
            left  : mou.x-(ec.x+6),
            top : mou.y-(ec.y+6)
          });
          this.parent.exec(lin.update,false,false,lin);
          this.parent.event.flushCollection([evi]);
        },event:true,args:[tmS,ln,this.parent.event.db.length]}));

        if(this.parent.browser.isIE){this.data.db.task[uid].object.elements.task.fireEvent("onmouseover");}
        var uidEventMMove=this.parent.event.db.length;

        this.parent.event.add(this.panels.editor.elements.content,"mousemove",function(evt,arrow,lin)
        {
          var ec  = this.parent.dom.position(this.panels.editor.elements.content);
          var mou = this.parent.dom.mouse(window.event || evt);
          this.parent.dom.setStyle(arrow,{
            left  : (mou.x-(ec.x+6)+(this.panels.editor.elements.content.scrollLeft || 0)),
            top : (mou.y-(ec.y+6)+(this.panels.editor.elements.content.scrollTop || 0))
          });
          lin.update();
          this.parent.exec(this.dropables.derivation.capture,{currentElementDrag:arrow},false,this.dropables.derivation);
        }.extend(this,tmS,ln));
        this.parent.event.add(tmS,"click",function(evt,options)
        {
          //options = (window.event)?evt:options;
          this.dropables.derivation.capture({currentElementDrag:options.arrow});
          this.dragables.derivation.noDrag=false;
          if(this.dropables.derivation.selected===false)
          {
            options.line.remove();
          }
          else
          {
            options.line.remove();
            this.patternPanel(false,options.uid,{to:this.dropables.derivation.selected,type:this.dragables.derivation.currentElementInArray});
            if (this.dropables.derivation.elements[this.dropables.derivation.selected])
            {
              this.dropables.derivation.launchEvents(this.dropables.derivation.elements[this.dropables.derivation.selected].events.out);
            }
          }
          this.inDerivationDrag=false;
          this.dropables.derivation.selected=false;
          this.parent.event.flushCollection([options.ue,options.ua]);
          this.parent.dom.remove(options.arrow);
        }.extend(this,{uid:uid,arrow:tmS,line:ln,ue:this.parent.event.db.length,ua:uidEventMMove}));
      },
      derivation:function(uid,type)
      {
        for(var i=0;i<this.data.db.task.length;i++)
        {
          this.data.render.lineDerivation(i);
        }
        return true;
      },
      lineDerivation:function(index)
      {
        var task = this.data.db.task[index];
        for(var j=0;j<task.derivation.to.length;j++)
        {
          var derivation  = task.derivation.to[j];
          //alert(derivation.task);
          if(derivation.task==="-1" || derivation.task==="-2")
          {
            var target=(task.derivation.to.length>1)?'derivationBottom':'derivation';
            this.parent.dom.setStyle(task.object.elements[target],{
            //  background:((task.derivation.type===0)?"":"url("+this.options.images_dir+task.derivation.type+"t.gif?aa="+Math.random()+")")
          //    background:"url("+this.options.images_dir+derivation.task+".gif?aa="+Math.random()+")"
              background:"url("+this.options.images_dir+derivation.task+((target=="derivationBottom")?"bb.jpg":".gif")+"?aa="+Math.random()+")"
            });

          }
          else
          {
            var uid  = this.tools.getIndexOfUid(derivation.task);
            //alert(this.tools.getIndexOfUid)
            //alert(this.tools.getIndexOfUid(derivation.task));
            //var from = (task.derivation.type===0)?task.object.elements.task:task.object.elements.derivation;
            var taskF= task.object.elements;
            var taskT= this.data.db.task[uid].object.elements;
            var from = task.object.elements.derivation;
            var toTask=this.data.db.task[uid];
            var to   = toTask.object.elements.task;

            if(task.derivation.type === 8 || task.derivation.type ===5)
            {
                                                    var ij = toTask.object.inJoin;
              ij = (ij)?ij+1:1;
              toTask.object.inJoin = ij;
              this.parent.dom.setStyle(toTask.object.elements.init,{
                //background:((task.derivation.type===0)?"":"url("+this.options.images_dir+task.derivation.type+"t.gif?aa="+Math.random()+")")
                background:"url("+this.options.images_dir+task.derivation.type+"t.gif?aa="+Math.random()+")",
                backgroundPosition:"2 0",
                backgroundRepeat:"no-repeat"
              });

            }
            else
            {
              this.parent.dom.setStyle(task.object.elements.derivation,{
                //background:((task.derivation.type===0)?"":"url("+this.options.images_dir+task.derivation.type+"t.gif?aa="+Math.random()+")")
                background:"url("+this.options.images_dir+task.derivation.type+"t.gif?aa="+Math.random()+")"
              });
            }

            var line = new this.parent.module.app.line({
              indexRootSize:30,
              indexRootLastSize:35,
              elements:[taskF.task,taskT.task],
              envolve:[
                [taskF.task],
                []
              ],
              target:this.panels.editor.elements.content,
              color:"#228AB0",
              startA:50,
              zIndex:5
            });
            line.make();
            var cE=this.observers.lineas.register(line.update,line);
            derivation.object={
              line    : line,
              indexObserver : cE
            };
          }
        }
      },
      text:function()
      {
        var lngt = this.data.db.text.length;
        for(var i =0;i<lngt;i++)
        {
          this.data.build.text(i);
        }
        this.panels.editor.clearContentStatus();
        this.data.render.derivation();
      }
    },
    build:{
      task:function(index,options_task)
      {
        //console.log(index);
        options_task = {
            tp:'task'
        }.concat(options_task || {});
        /*var options   = {
          color:((options_task.tp==='task')?"auto":"green")
        }.concat(this.data.db[(options_task.tp=='task')?'task':'subprocess'][index] || {});*/
        //var options = this.data.db[(options_task.tp=='task')?'task':'subprocess'][index];
        var options = this.data.db['task'][index];

        //options.color = ((options_task.tp==='task')?"auto":"#9B88CA");
        //options.color = "auto";
        //alert(this.options.rw);

        if (this.options.rw) {
          options.color = ((options_task.tp==='task')?"auto":"#9B88CA");
          if(options_task.tp==='hidden'){
            options.color = "transparent";
          }
        }

        var db    = this.data.db, task=db.task[index];
        var derivation  = task.derivation.to;
        var a = document.createElement("div");
        a.className="processmap_task___"+this.options.theme;
        this.parent.dom.setStyle(a,{
          top:options.position.y,
          left:options.position.x,
          cursor:((this.options.rw===true)?"move":"default"),
          backgroundColor:(options.color ? options.color : 'auto')
        });

            if(options.color == '#9B88CA'){
                                      var subp = ((options_task.tp==='task')?"":"url(/images/subp.png)");
                                    }
                                    if(options.color == 'transparent'){
                                      var subp = ((options_task.tp==='task')?"":"url(/images/0t.gif)");
                                    }
                                    if(options_task.tp==='hidden'){
                                       options_task.tp = 'task';
                                    }
        var b = document.createElement("div");
        b.className="processmap_task_label___"+this.options.theme;
        this.parent.dom.setStyle(b,{
          cursor:((this.options.rw===true)?"move":"default"),
          background:subp,
          backgroundRepeat:"no-repeat",
          backgroundPosition:"center",
          height:40
        });
        b.innerHTML = options.label;

          if(options.color == 'transparent'){
            var b = document.createElement("div");
              b.className="processmap_task_label___"+this.options.theme;
              this.parent.dom.setStyle(b,{
                cursor:((this.options.rw===true)?"move":"default"),
                background:subp,
                backgroundRepeat:"repeat-y",
                backgroundPosition:"top",
                height:100
              });
              b.innerHTML = options.label;
            }

        var b1 = document.createElement("div");
                                if(options.color != 'transparent'){
          this.parent.dom.setStyle(b1,{
          top:'2',
          left:'5',
          border:"0px solid red",
          height:13,
          position:"absolute"
          });
                                }

/**
 * Reviewed by recharge in the process design
 *
 */
/*
         if(task.statusIcons){
          for(var i=0;i<task.statusIcons.length;i++){
            //alert(task.statusIcons[i].icon);
            var icon = document.createElement("img");
            icon.src=task.statusIcons[i].icon;
            icon.height='13';
            icon.width='13';
            icon.alt=task.statusIcons[i].message;
            icon.title=task.statusIcons[i].message;
            icon.style.cursor = "help";

            icon.onmouseover=function(){
              //b11.innerHTML = this.alt;
            }
            icon.onmouseout=function(){
              //b11.innerHTML = "";
            }

            b1.appendChild(icon);

          }
        }
*/
        //clip: "rect(0,0,0,0)"
        /*if (task.derivation.type==5) {
          for(var it=0;it< db.task.length;it++)
          {
            if ( db.task[it].uid == derivation[0].task )
            {
              var joinPosX = db.task[it].position.x + 69;
              var joinPosY = db.task[it].position.y - 30;
            }
          }
        }*/

        var c = document.createElement("div");
        this.parent.dom.setStyle(c,{
          position:"absolute",
          top: /*task.derivation.type==5 ? joinPosY :*/ options.position.y+38,
          left:/*task.derivation.type==5 ? joinPosX  :*/ options.position.x+(81-12),
          height:25,
          width:25,
          //backgroundColor:"black",
          border:"0px solid black",
          overflow:"hidden",
          cursor:(this.options.rw===true ? "pointer" : 'default'),
          zIndex:9
        });
        if (this.options.rw===true) {
          /******************************neyek**************************************
           * Compativility for IE 7, 8
           */
          if (navigator.appName == "Microsoft Internet Explorer"){
            c.onclick=this.patternPanel.args(1, index, null);
          } else {
            c.onclick=this.patternPanel.args(index);
          }
          }
        var d = document.createElement("div");
        this.parent.dom.setStyle(d,{
          position:"absolute",
          top: /*task.derivation.type==5 ? joinPosY :*/ options.position.y+49,
          left:/*task.derivation.type==5 ? joinPosX  :*/ options.position.x+(93),
          height:38,
          width:38,
          //backgr1undColor:"black",
          border:"0px solid black",
          overflow:"hidden",
          zIndex:9
        });

        var t = document.createElement("div");

        this.parent.dom.setStyle(t,{
          position:"absolute",
          top:options.position.y-30,
          left:options.position.x+(81-14),
          height:30,
          width:30,
          //backgroundColor:"black",
          //border:"1px solid black",
          overflow:"hidden",
          zIndex:9
        });
        if(this.options.rw===true)
        {
        /* Change label Begin */
/*        a.ondblclick=function(evt,index)
        {
          var text=this.data.db.task[(window.event)?evt:index];
          var t = prompt(G_STRINGS.ID_PROCESSMAP_PROMPT_RENAME_TEXT,text.label.unescapeHTML());
          if(t && t.trim()!=="" && t!==text.label)
          {
            text.label = text.object.elements.label.innerHTML=t.escapeHTML();
            var r = new leimnud.module.rpc.xmlhttp({
              url : this.options.dataServer,
              args  : "action=updateText&data="+{uid:text.uid,label:text.label.unescapeHTML()}.toJSONString()
            });
            r.make();
          }
        }.extend(this,index);*/
        /* Change label End */
        var menu = new this.parent.module.app.menuRight();
        var textMenu = G_STRINGS.ID_PROCESSMAP_USERS_AND_GROUPS_ADHOC;
        var lengthText = mb_strlen(textMenu) * 0.60;

        menu.make({
          target: a,
          width: (3 + lengthText) + "em",
          theme: this.options.theme,
          menu: ((options_task.tp == "task")?
          [
          {image: "/images/steps.png", text: G_STRINGS.ID_PROCESSMAP_STEPS, launch: function (event, index) {
            this.tmp.stepsPanel = panel =new leimnud.module.panel();
            var data = this.data.db.task[index];
            var iForm=function(panel,index,ifo){
              panel.command(panel.loader.show);
              var r = new this.parent.module.rpc.xmlhttp({
                url:this.options.dataServer,
                args:"action=steps&data="+{proUid:this.options.uid,tasUid:data.uid,option:ifo,index:index}.toJSONString()
              });
              r.callback=this.parent.closure({instance:this,method:function(index,rpc,panel){
                panel.command(panel.loader.hide);
                var scs=rpc.xmlhttp.responseText.extractScript();
                panel.addContent(rpc.xmlhttp.responseText);
                scs.evalScript();
              },args:[index,r,panel]});
              r.make();
            }
            panel.options={
              limit:true,
              size:{w:770,h:450},
              position:{x:50,y:50,center:true},
              title: G_STRINGS.ID_PROCESSMAP_TASK_STEPS+" "+data.label.substr(0,82) + (data.label.length>=82 ? "..." : "") ,
              theme:this.options.theme,
              statusBar:false,
              control:{
                close:true
              },
              fx:{
                modal:true
            }};
            panel.tab={
              width :300,
              optWidth:120,
              step  :(this.parent.browser.isIE?3:4),
              options:[{
                  title :G_STRINGS.ID_PROCESSMAP_STEPS,
                  content :this.parent.closure({instance:this,method:iForm,args:[panel,index,1]}),
                  selected:true
                },{
                  title :G_STRINGS.ID_PROCESSMAP_CONDITIONS,
                  content :this.parent.closure({instance:this,method:iForm,args:[panel,index,2]})
                },{
                  title :G_STRINGS.ID_PROCESSMAP_TRIGGERS,
                  content :this.parent.closure({instance:this,method:iForm,args:[panel,index,3]})
                }]
              };
            panel.events = {
              remove: function() {

                }.extend(this)
            };
            panel.make();
          }.extend(this,index)},
          {image:"/images/users.png",text:G_STRINGS.ID_PROCESSMAP_USERS_AND_GROUPS,launch:function(event,index){
            var panel;
            this.tmp.usersPanel = panel =new leimnud.module.panel();
            panel.options={
              limit :true,
              size  :{w:450,h:300},
              position:{x:50,y:50,center:true},
              title :G_STRINGS.ID_PROCESSMAP_USERS_AND_GROUPS+": "+task.label.substr(0,30) + (task.label.length>=30 ? "..." : "") ,
              theme :this.options.theme,
              control :{close:true,resize:false},fx:{modal:true},
              statusBar:false,
              fx  :{modal:true}
            };
            panel.events = { //neyek
              remove: function() {

                }.extend(this)
            };
            panel.make();
            panel.loader.show();
            var r;
            panel.currentRPC = r = new leimnud.module.rpc.xmlhttp({
              url:this.options.dataServer,
              args:"action=users&data="+{
                tas_uid :task.uid,
                pro_uid :this.options.uid
              }.toJSONString()
            });
            r.callback=function(rpc,panel)
            {
              panel.loader.hide();
              var scs = rpc.xmlhttp.responseText.extractScript();
              panel.addContent(rpc.xmlhttp.responseText);
              scs.evalScript();
            }.extend(this,panel);
            r.make();
          }.extend(this,index)},

          {image:"/images/users.png",text:G_STRINGS.ID_PROCESSMAP_USERS_AND_GROUPS_ADHOC,launch:function(event,index){
            var panel;
            this.tmp.usersPanel = panel =new leimnud.module.panel();
            panel.options={
              limit :true,
              size  :{w:450,h:300},
              position:{x:50,y:50,center:true},
              title :G_STRINGS.ID_PROCESSMAP_USERS_AND_GROUPS_ADHOC+": "+task.label.substr(0,27) + (task.label.length>=27 ? "..." : "") ,
              theme :this.options.theme,
              control :{close:true,resize:false},fx:{modal:true},
              statusBar:false,
              fx  :{modal:true}
            };
            panel.make();
            panel.loader.show();
            var r;
            panel.currentRPC = r = new leimnud.module.rpc.xmlhttp({
              url:this.options.dataServer,
              args:"action=users_adhoc&data="+{
                tas_uid :task.uid,
                pro_uid :this.options.uid
              }.toJSONString()
            });
            r.callback=function(rpc,panel)
            {
              panel.loader.hide();
              var scs = rpc.xmlhttp.responseText.extractScript();
              panel.addContent(rpc.xmlhttp.responseText);
              scs.evalScript();
            }.extend(this,panel);
            r.make();
          }.extend(this,index)},

          {image:"/images/rules.png",text:G_STRINGS.ID_PROCESSMAP_WORKFLOW_PATTERNS,launch:this.patternPanel.args(index)},
          {image:"/images/delete_rules.png",text:G_STRINGS.ID_PROCESSMAP_WORKFLOW_DELETE_PATTERNS,launch:this.parent.closure({instance:this,method:function() {
            var data = this.data.db.task[index];
            if (typeof(data.derivation.type) != 'undefined') {
              new this.parent.module.app.confirm().make({
                label : G_STRINGS.ID_PROCESSMAP_WORKFLOW_CONFIRM_DELETE_PATTERNS + '"' + data.label + '"?',
                action: function() {
                  var db    = this.data.db, task=db.task[index];
                  var vars  = {
                    tas_uid:task.uid,
                    pro_uid:this.options.uid
                  };
                  var aData = {};
                  aData.tas_uid = vars.tas_uid;
                  aData.data    = [];
                  this.data.build.derivation(aData);
                  var r = new leimnud.module.rpc.xmlhttp({
                    url:this.options.dataServer,
                    args:'action=deleteAllRoutes&data='+vars.toJSONString()
                  });
                  r.make();
                }.extend(this)
              });
            }
            else {
              new leimnud.module.app.alert().make({
                label:G_STRINGS.ID_NO_DERIVATIONS_DEFINED
              });
            }
          },args:index})},
          {image:"/images/delete.png",text:G_STRINGS.ID_PROCESSMAP_DELETE_TASK,launch:this.parent.closure({instance:this,method:function(index){
            var data = this.data.db.task[index];

            var r = new leimnud.module.rpc.xmlhttp({
              url: this.options.dataServer,
              args: "action=taskCases&data=" + {
                pro_uid: this.options.uid,
                task_uid: data.uid
              }.toJSONString()
            });
            r.callback = function (rpc) {
              var rs = rpc.xmlhttp.responseText.parseJSON();
              var casesNumRec = rs.casesNumRec;

              if (casesNumRec == 0) {
                new this.parent.module.app.confirm().make({
                  label: G_STRINGS.ID_PROCESSMAP_CONFIRM_DELETE_TASK + " " + data.label,
                  action: function () {
                    data.object.drag.flush();
                    this.dropables.derivation.unregister(data.object.dropIndex);
                    this.data.render.deleteDerivation(data.uid, true);
                    this.parent.dom.remove(data.object.elements);
                    var r2 = new leimnud.module.rpc.xmlhttp({
                      url: this.options.dataServer,
                      args: "action=deleteTask&data=" + {
                        pro_uid: this.options.uid,
                        tas_uid: data.uid
                      }.toJSONString()
                    });
                    r2.make();
                  }.extend(this)
                });
              }
              else {
                var msg = _("ID_TASK_CANT_DELETE");
                msg = msg.replace("{0}", data.label);
                msg = msg.replace("{1}", casesNumRec);

                new this.parent.module.app.info().make({label: msg});
              }
            }.extend(this);
            r.make();

            return;

            if(confirm(G_STRINGS.ID_PROCESSMAP_CONFIRM_DELETE_TASK+" "+ data.label))
            {
              data.object.drag.flush();
              this.dropables.derivation.unregister(data.object.dropIndex);
              this.data.render.deleteDerivation(data.uid);
              this.parent.dom.remove(data.object.elements);
              var r = new leimnud.module.rpc.xmlhttp({
                url:this.options.dataServer,
                args:"action=deleteTask&data="+{pro_uid:this.options.uid,tas_uid:data.uid}.toJSONString()
              });
              r.make();
            }
          },args:index})},
          {simage:"/images/properties.png",text:G_STRINGS.ID_PROCESSMAP_PROPERTIES,launch:this.parent.closure({instance:this,method:function(index){
            var panel;
            var iForm=function(panel,index,ifo){
              //saveDataTaskTemporal(ifo);
              if(typeof(panel.flag) == 'undefined') {
                if (!saveDataTaskTemporal(ifo)) {
                  var tabPass = panel.tabSelected;
                  panel.tabSelected = panel.tabLastSelected;
                  panel.tabLastSelected = tabPass;
                  panel.flag = true;
                  panel.makeTab();
                  return false;
                }
              }
              delete panel.flag;

              panel.command(panel.loader.show);
              var r = new this.parent.module.rpc.xmlhttp({
                url:this.options.dataServer,
                args:"action=editTaskProperties&data="+{uid:data.uid,iForm:ifo,index:index}.toJSONString()
              });
              r.callback=this.parent.closure({instance:this,method:function(index,rpc,panel){
                panel.command(panel.loader.hide);
                panel.clearContent();
                var scs=rpc.xmlhttp.responseText.extractScript(); //capturamos los scripts
                panel.addContent(rpc.xmlhttp.responseText.stripScript());//Eliminamos porque ya no los necesitamos
                scs.evalScript(); //interpretamos los scripts
              },args:[index,r,panel]});
              r.make();
            }

            this.tmp.propertiesPanel = panel =new leimnud.module.panel();
            var data = this.data.db.task[index];

            panel.options={
              limit:true,
              size:{w:600,h:430},
              position:{x:50,y:50,center:true},
              title:G_STRINGS.ID_PROCESSMAP_TASK+": "+data.label.substr(0,75) + (data.label.length>=75 ? "..." : "") ,
              theme:this.options.theme,
              statusBar:true,
              statusBarButtons:[
              {type:"button",value:G_STRINGS.ID_PROCESSMAP_SUBMIT},
              {type:"button",value:G_STRINGS.ID_PROCESSMAP_CANCEL}
              ],
              control:{
                close:true,
                resize:false
              },
              fx:{
                modal:true
              }
            };

            panel.tab={
              width :170,
              optWidth:160,
              widthFixed:false,
              step  :(this.parent.browser.isIE?3:4),
              options:[{
                  title : G_STRINGS.ID_PROCESSMAP_TASK_PROPERTIES_DEFINITION,
                  content : this.parent.closure({instance:this,method:iForm,args:[panel,index,1]}),
                  noClear : true,
                  selected: true
                },{
                  title : G_STRINGS.ID_PROCESSMAP_TASK_PROPERTIES_ASSIGNMENTS,
                  content : this.parent.closure({instance:this,method:iForm,args:[panel,index,2]}),
                  noClear : true
                },{
                  title : G_STRINGS.ID_PROCESSMAP_TASK_PROPERTIES_TIMING,
                  content : this.parent.closure({instance:this,method:iForm,args:[panel,index,3]}),
                  noClear : true
                },{
                  title : G_STRINGS.ID_PROCESSMAP_TASK_PROPERTIES_PERMISSIONS,
                  content : this.parent.closure({instance:this,method:iForm,args:[panel,index,5]}),
                  noClear : true
                },{
                  title : G_STRINGS.ID_PROCESSMAP_TASK_PROPERTIES_LABELS,
                  content : this.parent.closure({instance:this,method:iForm,args:[panel,index,6]}),
                  noClear : true
                },{
                  title : G_STRINGS.ID_PROCESSMAP_TASK_PROPERTIES_NOTIFICATIONS,
                  content : this.parent.closure({instance:this,method:iForm,args:[panel,index,7]}),
                  noClear : true
                }]
              };
            var taskOptions = this.data.db.taskOptions;
            this.loadExtendedProperties = function(){
              for(i=0;i<taskOptions.length;i++){
                anElement={
                    title : taskOptions[i].title,
                    content : this.parent.closure({instance:this,method:iForm,args:[panel,index,taskOptions[i].id]}),
                    noClear : true
                };
                panel.tab.options.push(anElement);
              }
            };
            this.loadExtendedProperties();
            panel.make();

          },args:index})}
          ]:
          [
          {image:"/images/rules.png",text:G_STRINGS.ID_PROCESSMAP_WORKFLOW_PATTERNS,launch:this.patternPanel.args(index)},
          {image:"/images/delete_rules.png",text:G_STRINGS.ID_PROCESSMAP_WORKFLOW_DELETE_PATTERNS,launch:this.parent.closure({instance:this,method:function() {
            var data = this.data.db.task[index];
            if (typeof(data.derivation.type) != 'undefined') {
              new this.parent.module.app.confirm().make({
                label : G_STRINGS.ID_PROCESSMAP_WORKFLOW_CONFIRM_DELETE_PATTERNS + '"' + data.label + '"?',
                action: function() {
                  var db    = this.data.db, task=db.task[index];
                  var vars  = {
                    tas_uid:task.uid,
                    pro_uid:this.options.uid
                  };
                  var aData = {};
                  aData.tas_uid = vars.tas_uid;
                  aData.data    = [];
                  this.data.build.derivation(aData);
                  var r = new leimnud.module.rpc.xmlhttp({
                    url:this.options.dataServer,
                    args:'action=deleteAllRoutes&data='+vars.toJSONString()
                  });
                  r.make();
                }.extend(this)
              });
            }
            else {
              new leimnud.module.app.alert().make({
                label:G_STRINGS.ID_NO_DERIVATIONS_DEFINED
              });
            }
          },args:index})},
          {image:"/images/delete.png",text:G_STRINGS.ID_PROCESSMAP_DELETE_SUBPROCESS,launch:this.parent.closure({instance:this,method:function(index){
            var data = this.data.db.task[index];
            new this.parent.module.app.confirm().make({
              label:G_STRINGS.ID_PROCESSMAP_CONFIRM_DELETE_SUBPROCESS+data.label,
              action:function()
              {
                data.object.drag.flush();
                this.dropables.derivation.unregister(data.object.dropIndex);
                this.data.render.deleteDerivation(data.uid,true);
                this.parent.dom.remove(data.object.elements);
                var r = new leimnud.module.rpc.xmlhttp({
                  url:this.options.dataServer,
                  args:"action=deleteSubProcess&data="+{pro_uid:this.options.uid,tas_uid:data.uid}.toJSONString()
                });
                r.make();
              }.extend(this)
            });
            return;
            if(confirm(G_STRINGS.ID_PROCESSMAP_CONFIRM_DELETE_TASK+data.label))
            {
              data.object.drag.flush();
              this.dropables.derivation.unregister(data.object.dropIndex);
              this.data.render.deleteDerivation(data.uid);
              this.parent.dom.remove(data.object.elements);
              var r = new leimnud.module.rpc.xmlhttp({
                url:this.options.dataServer,
                args:"action=deleteSubProcess="+{pro_uid:this.options.uid,tas_uid:data.uid}.toJSONString()
              });
              r.make();
            }
          },args:index})},
          {simage:"/images/properties.png",text:G_STRINGS.ID_PROCESSMAP_PROPERTIES,launch:function(event,index){
            var panel;
            this.tmp.subProcessPanel = panel =new leimnud.module.panel();
            //alert(this.data.db.task[index].label);+": "+task.label
            panel.options={
              limit :true,
              size  :{w:700,h:550},
              position:{x:50,y:50,center:true},
              title :G_STRINGS.ID_PROCESSMAP_PROPERTIES,
              theme :this.options.theme,
              control :{close:true,resize:false},fx:{modal:true},
              statusBar:false,
              fx  :{modal:true}
            };
            panel.make();
            panel.loader.show();
            var r;
            panel.currentRPC = r = new leimnud.module.rpc.xmlhttp({
              url:this.options.dataServer,
              args:"action=subProcess_Properties&data="+{
                tas_uid :task.uid,
                pro_uid :this.options.uid,
                index: index
              }.toJSONString()
            });
            r.callback=function(rpc,panel)
            {
              panel.loader.hide();
              var scs = rpc.xmlhttp.responseText.extractScript();
              panel.addContent(rpc.xmlhttp.responseText);
              scs.evalScript();
            }.extend(this,panel);
            r.make();
          }.extend(this,index)}
          ])
        });
        this.observers.menu.register(menu.remove,menu);
        }
        else {
          if (this.options.mi) {
            this.parent.dom.setStyle(a,{
              cursor:('pointer')
            });
            a.title = G_STRINGS.ID_CLICK_VIEW_MORE_INFO;
            this.parent.dom.setStyle(b,{
              cursor:('pointer')
            });
            /*b.title = G_STRINGS.ID_CLICK_VIEW_MORE_INFO;
            this.parent.dom.setStyle(c,{
              cursor:('pointer')
            });
            c.title = G_STRINGS.ID_CLICK_VIEW_MORE_INFO;*/
            this.parent.event.add(a, 'click', {instance:this,method:function(evt, index)
            {
              var data = this.data.db.task[index];
              this.oTaskDetailsPanel = new leimnud.module.panel();
              this.oTaskDetailsPanel.options = {
                limit    : true,
                size     : {w:300,h:227},
                position : {x:0,y:0,center:true},
                title    : '',
                theme    : 'processmaker',
                statusBar: false,
                control  : {drag:false,resize:false,close:true},
                fx       : {opacity:true,rolled:false,modal:true}
              };
              this.oTaskDetailsPanel.make();
              this.oTaskDetailsPanel.events = {
                remove:function() {
                  delete this.oTaskDetailsPanel;
                }.extend(this)
              };
              this.oTaskDetailsPanel.loader.show();
              var r = new this.parent.module.rpc.xmlhttp({
                url :'cases_Ajax',
                args:'action=showTaskDetails&sTaskUID=' + data.uid
              });
              r.callback=function(rpc){
                this.oTaskDetailsPanel.loader.hide();
                var scs = rpc.xmlhttp.responseText.extractScript();
                this.oTaskDetailsPanel.addContent(rpc.xmlhttp.responseText);
                scs.evalScript();
              }.extend(this);
              r.make();
            }.extend(this,index)});
          }
        }
        this.panels.editor.elements.content.appendChild(a);
        a.appendChild(b);
        if(this.options.rw===true){
          a.appendChild(b1);
        }

        this.panels.editor.elements.content.appendChild(c);
        this.panels.editor.elements.content.appendChild(d);
        this.panels.editor.elements.content.appendChild(t);

        options['object']={
          elements:{
            task  : a,
            label : b,
            derivation: c,
            derivationBottom: d,
            init  : t,
            statusIcons:b1
          }
        };
        //console.info(index)
        options.object.dropIndex=this.dropables.derivation.register({
          element : a,
          value : index,
          events  : {
            over  :this.parent.closure({instance:this.dropables.derivation,method:function(e,o,u){
              this.parent.dom.setStyle(e,{
                border:"1px solid #006699"
              });
            },args:[a,options,index]}),
            out   :this.parent.closure({instance:this.dropables.derivation,method:function(e,o,u){
              this.parent.dom.setStyle(e,{
                border:"0px solid #006699"
              });
            },args:[a,options,index]}),
            click :this.data.render.preDerivation.args(index)
          }
        });
        if(this.options.rw===true)
        {
          options.object.drag = new this.parent.module.drag({
            //elements:a
            link:{
              elements:a,
              ref:[a,c,d,t]
            },
            limit:true
            //group:[a,c]
          });
          this.observers.lineas.register(this.parent.closure({instance:options.object.drag,method:function(){}}),options.object.drag);
          options.object.drag.events={
            //move  :this.parent.closure({instance:options.object.drag,method:options.object.drag.observer.update}),
            move  :this.parent.closure({instance:this,method:function(div,divC,uid,drag) {
              options.object.drag.observer.update();
              var db = this.data.db;
              },args:[a,c,index,options.object.drag]}),

            finish  :this.parent.closure({instance:this,method:function(div,divC,uid,drag){
              if(!drag.moved){return false;}
              var pos  = this.parent.dom.position(div);
              var h=pos;
              var data = this.data.db.task[uid];
              var db = this.data.db;
              var r = new leimnud.module.rpc.xmlhttp({
                  url:this.options.dataServer,
                  args:"action=saveTaskPosition&data="+{uid:data.uid,position:pos}.toJSONString()
                });
              r.make();
            },args:[a,c,index,options.object.drag]})
          };
          options.object.drag.make();
        }
        //alert(options.object);
      },
      guide:function(index)
      {
        var options = this.data.db.guide[index];
        var scl = {
          x:this.panels.editor.elements.content.scrollLeft,
          y:this.panels.editor.elements.content.scrollTop
        };
        var a =document.createElement("div");
        var pos = {
          top:((options.direction==="vertical")?0+scl.y:options.position),
          left:((options.direction==="horizontal")?0+scl.x:options.position)
        };
        this.parent.dom.setStyle(a,{
          position:"absolute",
          display :"",
          visibility:"visible",
          height  :((options.direction==="vertical")?"100%":5),
          width :((options.direction==="horizontal")?"100%":5),
          backgroundColor:"transparent",
          borderLeft:((options.direction==="vertical")?"1":"0")+"px solid #FE9F0D",
          borderTop:((options.direction==="horizontal")?"1":"0")+"px solid #FE9F0D",
          overflow:'hidden',
          zIndex  :1,
          cursor  :((this.options.rw===true)?"move":"default"),
          left  :pos.left,
          top :pos.top
        });
        //alert(pos.top+":"+pos.left)
/*        if(options.direction==="vertical")
        {
          this.parent.dom.setStyle(a,{
            left:pos.left+1
          });
        }
        else
        {

        }*/
        var menu = new this.parent.module.app.menuRight();
        menu.make({
          target:a,
          width:201,
          theme:this.options.theme,
          menu:[
          {image:"/images/delete.png",text:G_STRINGS.ID_PROCESSMAP_DELETE_LINE,launch:this.parent.closure({instance:this,method:function(index){
            var data = this.data.db.guide[index];
            this.parent.dom.remove(data.object.elements);
            var r = new leimnud.module.rpc.xmlhttp({
                url:this.options.dataServer,
                args:"action=deleteGuide&data="+{
                  uid:data.uid
                }.toJSONString()
            });
            r.make();
          },args:index})}
          ],
          //onFocus:
          //onBlur:
          parent:this.parent
        });
        this.observers.menu.register(this.parent.closure({instance:menu,method:menu.remove}),menu);
        options.object={
          elements:{guide:a}
        };
        var Gdrag=new this.parent.module.drag({
          elements:a,
          limit:((options.direction==="horizontal")?"x":"y")
        });
        Gdrag.events={
          finish:this.parent.closure({instance:this,method:function(index,drag){
            if(!drag.moved){return false;}
            var data = this.data.db.guide[index];
            var pos  = this.parent.dom.position(data.object.elements.guide);
            data.position=(data.direction=="vertical")?pos.x:pos.y;
            var r = new leimnud.module.rpc.xmlhttp({
                url:this.options.dataServer,
                args:"action=saveGuidePosition&data="+{
                  uid:data.uid,
                  position:data.position,
                  direction:data.direction
                }.toJSONString()
            });
            r.make();

          },args:[index,Gdrag]})
        };
        Gdrag.make();       var guideObserver = this.observers.guideLines.register(this.parent.closure({instance:this,method:function(obj,direction){
          if(direction=="horizontal")
          {
            obj.style.left=parseInt(this.panels.editor.elements.content.scrollLeft,10);
          }
          else
          {
            obj.style.top=parseInt(this.panels.editor.elements.content.scrollTop,10);
          }
        },args:[a,options.direction]}));
//        this.panels.editor.elements.content.onscroll = guideObserver.update;
        this.panels.editor.elements.content.onscroll = this.observers.guideLines.update;
        this.panels.editor.elements.content.appendChild(a);
      },
      title:function(index)
      {
        if(this.data.db.title)
        {
          var title=this.data.db.title;
          var t = document.createElement("div");
          t.className="processmap_title___"+this.options.theme;
          this.parent.dom.setStyle(t,{
            top:title.position.y,
            left:title.position.x,
            cursor:((this.options.rw===true)?"move":"default")
          });
          t.innerHTML=title.label;
          if(this.options.rw===true)
          {
            /*t.ondblclick=function(evt,index)
            {
              var text=this.data.db.title.label;
              var t = prompt(G_STRINGS.ID_PROCESSMAP_PROMPT_RENAME_TEXT,text.unescapeHTML());
              if(t && t.trim()!=="" && t!==text)
              {
                text.label = text.object.elements.label.innerHTML=t.escapeHTML();
                var r = new leimnud.module.rpc.xmlhttp({
                  url : this.options.dataServer,
                  args  : "action=updateText&data="+{uid:text.uid,label:text.label.unescapeHTML()}.toJSONString()
                });
                r.make();
              }
            }.extend(this,index);*/
          }
          this.panels.editor.elements.content.appendChild(t);
          title.object={
            elements:{
              label:t
            }
          };

          if(this.options.rw===true)
          {
            title.object.drag = new this.parent.module.drag({
              elements:t,
              limit:true
            });
            title.object.drag.events={
              finish:function(drag)
              {
                if(!drag.moved){return false;}
                var title = this.data.db.title;
                var pos  = this.parent.dom.position(title.object.elements.label);
                title.position=pos;
                var r = new leimnud.module.rpc.xmlhttp({
                    url:this.options.dataServer,
                    args:"action=saveTitlePosition&data="+{
                      pro_uid:this.options.uid,
                      position:title.position
                    }.toJSONString()
                });
                r.make();

              }.extend(this,title.object.drag)
            };
            title.object.drag.make();
          }
        }
      },
      text:function(index)
      {
        var text = this.data.db.text[index];
        var a = document.createElement("div");
        a.className="processmap_text___"+this.options.theme;
        this.parent.dom.setStyle(a,{
          top:text.position.y,
          left:text.position.x,
          cursor:((this.options.rw===true)?"move":"default")
        });
        a.innerHTML=text.label;
        this.panels.editor.elements.content.appendChild(a);
        if(this.options.rw===true)
        {
          var menu = new this.parent.module.app.menuRight();
          menu.make({
            target:a,
            width:201,
            theme:this.options.theme,
            menu:[
              {image:"/images/properties.png",text:G_STRINGS.ID_PROCESSMAP_EDIT_TEXT,launch:function(evt,index){
                var text=this.data.db.text[index];
                /*var t = prompt(G_STRINGS.ID_PROCESSMAP_EDIT_TEXT_CHANGE_TO,text.label.unescapeHTML());
                if(t && t.trim()!=="" && t!==text.label)
                {
                  text.label = text.object.elements.label.innerHTML=t.escapeHTML();
                  var r = new leimnud.module.rpc.xmlhttp({
                    url : this.options.dataServer,
                    args  : "action=updateText&data="+{uid:text.uid,label:text.label.unescapeHTML()}.toJSONString()
                  });
                  r.make();
                }*/
                new this.parent.module.app.prompt().make({
                  label:G_STRINGS.ID_PROCESSMAP_EDIT_TEXT_CHANGE_TO,
                  value:text.label.escapeHTML(),
                  action:function(text,tObj){
                    if(text.trim()!=="" && tObj.label!=text)
                    {
                      tObj.label = tObj.object.elements.label.innerHTML=text.escapeHTML();
                      var r = new leimnud.module.rpc.xmlhttp({
                        url : this.options.dataServer,
                        args  : "action=updateText&data="+{uid:tObj.uid,label:tObj.label.unescapeHTML()}.toJSONString()
                      });
                      r.make();
                    }
                  }.extend(this,text)
                });
              }.extend(this,index)},
              {image:"/images/delete.png",text:G_STRINGS.ID_PROCESSMAP_DELETE_TEXT,launch:function(evt,index){
                var text=this.data.db.text[index];
                var r = new leimnud.module.rpc.xmlhttp({
                  url:this.options.dataServer,
                  args:"action=deleteText&data="+{uid:text.uid}.toJSONString()
                });
                r.make();
                this.parent.dom.remove(text.object.elements.label);
                this.data.db.text[index]=null;
              }.extend(this,index)}
            ]
          });
          this.observers.menu.register(menu.remove,menu);
          text.object={
            elements:{
              label:a
            }
          };
          text.object.drag = new this.parent.module.drag({
            elements:a,
            limit:true
          });
          text.object.drag.events={
            finish:function(index,drag)
            {
              if(!drag.moved){return false;}
              var text = this.data.db.text[index];
              var pos  = this.parent.dom.position(text.object.elements.label);
              text.position=pos;
              var r = new leimnud.module.rpc.xmlhttp({
                  url:this.options.dataServer,
                  args:"action=saveTextPosition&data="+{
                    uid:text.uid,
                    position:text.position
                  }.toJSONString()
              });
              r.make();

            }.extend(this,index,text.object.drag)
          };
          text.object.drag.make();
        }
      },
      derivation:function(options)
      {
        tt=options;
        var index=this.tools.getIndexOfUid(options.tas_uid);
        var from=this.data.db.task[index];
        this.data.render.deleteDerivation(options.tas_uid);
        var affe=options.data;
        from.derivation.type=options.type;
        for(var i=0;i<affe.length;i++)
        {
          from.derivation.to[i]={
            task:affe[i]
          };
        }
        this.data.render.lineDerivation(index);

        //is.parent.dom.remove(from.object.elements);
        //alert(this.tools.getIndexOfUid(from));
      }
    },
    save:function()
    {

    }
  }.expand(this,true);
  this.patternPanel=function(event,index,din){

    var options   = this.data.db.task[index];
    var db    = this.data.db, task=db.task[index];
    var derivation  = task.derivation.to;

    var vars  = {
      tas_uid:task.uid,
      pro_uid:this.options.uid
    }.concat((din)?{
      type   : din.type,
      next_task: this.data.db.task[din.to].uid
    }:{});
    /*
    * Aca se definen  TASK inicio y TASK a la que se deriva.
    */

    if (event)
    {
      if (typeof(this.data.db.task[index].derivation.type) == 'undefined')
      {
        new leimnud.module.app.alert().make(
            {
               label:G_STRINGS.ID_NO_DERIVATIONS_DEFINED
            });
        return false;
      }
      var iWidth, iHeight;
      switch(this.data.db.task[index].derivation.type)
      {
        case 0:
          iWidth  = 450;
          iHeight = 205;
        break;
        case 1:
          iWidth  = 700;
          iHeight = 350;
        break;
        case 2:
          iWidth  = 700;
          iHeight = 350;
        break;
        case 3:
          iWidth  = 350;
          iHeight = 350;
        break;
        case 4:
          iWidth  = 600;
          iHeight = 350;
        break;
        case 5:
          iWidth  = 450;
          iHeight = 205;
        break;
                                case 8:
          iWidth  = 550;
          iHeight = 300;
        break;
      }

      this.tmp.derivationsPanel = panel =new leimnud.module.panel();
      panel.options={
        limit :true,
        size  :{w:iWidth,h:iHeight},
        position:{x:50,y:50,center:true},
        title :G_STRINGS.ID_PROCESSMAP_WORKFLOW_PATTERNS+": "+task.label,
        theme :this.options.theme,
        control :{close:true,resize:true},
        fx  :{modal:true}
      };
      panel.make();
      panel.loader.show();
      var r = new leimnud.module.rpc.xmlhttp({
        url:this.options.dataServer,
        args:"action=derivations&data="+vars.toJSONString()
      });
      r.callback=function(rpc,panel)
      {
        panel.loader.hide();
        var scs = rpc.xmlhttp.responseText.extractScript();
        panel.addContent(rpc.xmlhttp.responseText);
        scs.evalScript();
      }.extend(this,panel);
      r.make();
    }
    else
    {
      if ((this.data.db.task[index].derivation.type != vars.type) && (typeof(this.data.db.task[index].derivation.type) != 'undefined'))
      {
        if (typeof(this.data.db.task[index].derivation.type) != 'undefined')
        {
          new leimnud.module.app.confirm().make({
                label:  G_STRINGS.ID_PROCESSMAP_CONFIRM_WORKFLOW_PATTERN_CHANGE,
                action: function() {
            var aData = {};
              aData.type    = Number(vars.type);
              aData.tas_uid = vars.tas_uid;
              aData.data    = [];
              aData.data.push(vars.next_task);
              this.data.build.derivation(aData);
              vars['delete'] = true;
              var r = new leimnud.module.rpc.xmlhttp({
                    url:this.options.dataServer,
                    args:"action=saveNewPattern&data="+vars.toJSONString() //Save into the db
              });
              r.make();
            }.extend(this)
          });
        }
      }
      else
      {
        var aData = {};
        aData.type    = vars.type;
        aData.tas_uid = vars.tas_uid;
        aData.data    = [];
        aData.data.push(vars.next_task);
        if ((aData.type != 0) && (aData.type != 5))
        {
          for (var i = 0; i < this.data.db.task[index].derivation.to.length; i++)
          {
            if (!aData.data.inArray(this.data.db.task[index].derivation.to[i].task))
            {
              aData.data.push(this.data.db.task[index].derivation.to[i].task);
            }
          }
        }
        Pm.data.build.derivation(aData);
        aData.data.push(vars.next_task);
        vars['delete'] = false;
        var r = new leimnud.module.rpc.xmlhttp({
          url:this.options.dataServer,
          args:"action=saveNewPattern&data="+vars.toJSONString()
        });
        r.make();
      }
    }
  };
  this.tools={
    getIndexOfUid:function(uid)
    {
      for(var i=0;i<this.data.db.task.length;i++)
      {
        if(this.data.db.task[i].uid===uid){return i;}
      }
    },
    getUidOfIndex:function(index)
    {
      return this.data.db.task[index].uid || false;
    }
  }.expand(this);
  this.expand(this);
};

processmap.prototype={
  parent:leimnud,
  tmp:{},
  info:{
    name    : "processmap"
  },
  panels:{},dragables:{},dropables:{},
  make:function()
  {
    this.options = {
      theme :"firefox",
      rw  :true,
      mi  :true,
      ct  :false,
      hideMenu:true
    }.concat(this.options || {});
    this.options.target = this.parent.dom.element(this.options.target);
    if(!this.validate()){return false;}
    this.observers = {
      menu    : this.parent.factory(this.parent.pattern.observer,true),
      lineas    : this.parent.factory(this.parent.pattern.observer,true),
      guideLines  : this.parent.factory(this.parent.pattern.observer,true),
      buildingLineGuides: this.parent.factory(this.parent.pattern.observer,true)
    };
    this.dropables.derivation = new this.parent.module.drop();
    this.dropables.derivation.make();

    /* Hidden processmaker menu-submenu BEGIN*/
    if(this.options.hideMenu===true)
    {
      var h = this.parent.dom.element("pm_header");
      var m = this.parent.dom.element("pm_menu");
      var s = this.parent.dom.element("pm_submenu");
      var sep = this.parent.dom.element("pm_separator");
      sep.className = "pm_separatorOff___"+this.options.theme;
      this.menuRolled=false;
      var dse = document.createElement("div");
      dse.id = 'pm_separator_div';
      dse.className = "pm_separatorDOff___"+this.options.theme;
      sep.appendChild(dse);
      sep.onmouseup=function()
      {
        if(this.menuRolled===true)
        {
          sep.className="pm_separatorOff___"+this.options.theme;
          dse.className="pm_separatorDOff___"+this.options.theme;
          this.parent.dom.setStyle([h,m,s],{
            display:""
          });
          this.menuRolled=false;
        }
        else
        {
          sep.className="pm_separatorOn___"+this.options.theme;
          dse.className="pm_separatorDOn___"+this.options.theme;
          this.menuRolled=true;
          this.parent.dom.setStyle([h,m,s],{
            display:"none"
          });
        }
      }.extend(this,sep);
      dse.onmouseover=function()
      {
        if(this.menuRolled===true)
        {
          dse.className="pm_separatorDOn___"+this.options.theme+" pm_separatorOver___"+this.options.theme;
        }
        else
        {
          dse.className="pm_separatorDOff___"+this.options.theme+" pm_separatorOver___"+this.options.theme;
        }
      }.extend(this,dse);
      dse.onmouseout=function()
      {
        if(this.menuRolled===true)
        {
          dse.className="pm_separatorDOn___"+this.options.theme+" pm_separatorOut___"+this.options.theme;
        }
        else
        {
          dse.className="pm_separatorDOff___"+this.options.theme+" pm_separatorOut___"+this.options.theme;
        }

      }.extend(this,dse);

    }
    /* Hidden processmaker menu-submenu END*/
    /* Change skin fro processmap BEGIN */
    if (this.options.rw === true) {
      var bd = this.parent.dom.capture("tag.body 0");
      var sm = this.parent.dom.element("pm_submenu");
      this.parent.dom.setStyle(bd,{
        backgroundColor:"buttonface"
      });
      this.parent.dom.setStyle(sm,{
        //height:(sm.offsetHeight-21)
        height:25
      });
    }

    /* Change skin fro processmap END */

    /* Panel editor */

    this.panels.editor=new leimnud.module.panel();
    oClientWinSize = getClientWindowSize();
    var heightPanel = this.options.size.h;
    if(heightPanel <= oClientWinSize.height ) heightPanel = heightPanel + 800;

    this.panels.editor.options={
      limit:true,
      size:{w:(maximunX > this.options.size.w ? maximunX + 200 : this.options.size.w),h:heightPanel},
      position:{x:200,y:0,centerX:true},
      title:"",
      titleBar:false,
      control:{
        //drag  :false,
        resize:false
      },
      fx:{
        opacity:false,
        shadow:false,
        blinkToFront:false
      },
      theme:this.options.theme,
      target:this.options.target,
      modal:true,
      limit:true
    };
    this.panels.editor.setStyle={
      content:{
          background:"white url('"+this.options.images_dir+"bg_pm.gif') repeat fixed",
          backgroundPosition:"10 0"
        },
      containerWindow :{borderWidth:0,padding:0,backgroundColor:"buttonface"},
      titleBar  :{background:"transparent",borderWidth:0,height:5},
      frontend  :{backgroundColor:"buttonface"},
      backend   :{backgroundColor:"buttonface"},
      //statusBar :{border:"0px solid red",position:"absolute",bottom:50,right:10},
      status    :{textAlign:"center"}
    };
    this.panels.editor.make();
    this.panels.editor.loader.show();
    this.panels.editor.addContentStatus(G_STRINGS.ID_PROCESSMAP_LOADING);
    //this.panels.editor.command();
    //this.panels.editor.elements.content.onmousemove=function(){window.status="asdasd";}
    //this.parent.exec(this.data.load,false,false,this);
    this.data.load();
    if(this.options.rw===true)
    {
      this.data.render.buildingBlocks.panel();
    }
  },
  validate:function()
  {
    return (!this.options.target || !this.options.dataServer || !this.options.lang)?false:true;
  },
  addTask:function(evt,tp)
  {
    //alert(this.parent.dom.mouse(evt).y)
    //alert(this.menu.cursor.x)
    //var m = this.parent.dom.mouse(evt);
    var options = tp;
    var m = this.menu.cursor;
    var cpos = this.parent.dom.position(this.panels.editor.elements.content);

    //var index = this.data.db[(options.tp=='task')?'task':'subprocess'].length;
    var index = this.data.db['task'].length;
    var scl = {
      x:this.panels.editor.elements.content.scrollLeft,
      y:this.panels.editor.elements.content.scrollTop
    };
    var pos = {x:scl.x+(m.x-cpos.x),y:scl.y+(m.y-cpos.y)};
    this.data.db.task[index]={
      position:pos,
      label :G_STRINGS.ID_PROCESSMAP_NEW_TASK,
      uid   :false,
      //color :((options.tp=='task')?"#006699":"green"),
      task_type:((options.tp=='task')?'NORMAL':'SUBPROCESS'),
      derivation:{to:[]}
    }
    var data = this.data.db.task[index];

    //this.parent.exec(this.data.build.task,[index],false,this);

    if(options.tp=='task')
    {
        //this.data.build.task(index,{tp:'task'});
        //console.log(index);
        //console.log(this.data.db.task[index]);
        var r = new leimnud.module.rpc.xmlhttp({
          url:this.options.dataServer,
          args:"action=addTask&data="+{uid:this.options.uid,position:pos}.toJSONString()
        });
        r.callback=this.parent.closure({instance:this,method:function(index,rpc){
          var rs = rpc.xmlhttp.responseText.parseJSON();
          var data = this.data.db.task[index];
          //console.log(data);
          data.uid=rs.uid || false;
          data.statusIcons=rs.statusIcons;
          this.data.build.task(index,{tp:'task'});
          data.label=data.object.elements.label.innerHTML=rs.label || "";
        },args:[index,r]});
        r.make();
    }
    else
      { if(options.tp=='subprocess'){
          this.data.build.task(index,{tp:'subprocess'});
          var r = new leimnud.module.rpc.xmlhttp({
          url:this.options.dataServer,
          args:"action=addSubProcess&data="+{uid:this.options.uid,position:pos}.toJSONString()
          });
          r.callback=this.parent.closure({instance:this,method:function(index,rpc){
          var rs = rpc.xmlhttp.responseText.parseJSON();
          var data = this.data.db.task[index];
          data.label=data.object.elements.label.innerHTML=rs.label || "";
          data.uid=rs.uid || false;
          },args:[index,r]});
          r.make();
       }
       else{
          this.data.build.task(index,{tp:'hidden'});
          var r = new leimnud.module.rpc.xmlhttp({
          url:this.options.dataServer,
          args:"action=addTaskHidden&data="+{uid:this.options.uid,position:pos}.toJSONString()
          });
          r.callback=this.parent.closure({instance:this,method:function(index,rpc){
          var rs = rpc.xmlhttp.responseText.parseJSON();
          var data = this.data.db.task[index];
          data.label=data.object.elements.label.innerHTML=rs.label || "";
          data.uid=rs.uid || false;
          },args:[index,r]});
          r.make();
       }
      }

  },
  addText:function(evt)
  {
    //var panel =
    new this.parent.module.app.prompt().make({
      label:G_STRINGS.ID_PROCESSMAP_TEXT,
      action:function(text){
        if(text && text.trim()!=="")
        {
          //var m = this.parent.dom.mouse(evt);
          var m = this.menu.cursor;
          var cpos = this.parent.dom.position(this.panels.editor.elements.content);
          var index = this.data.db.task.length;
          var scl = {
            x:this.panels.editor.elements.content.scrollLeft,
            y:this.panels.editor.elements.content.scrollTop
          };
          var pos = {x:scl.x+(m.x-cpos.x),y:scl.y+(m.y-cpos.y)};

          var index=this.data.db.text.length;
          this.data.db.text[index]={
            label :text,
            position:{x:pos.x,y:pos.y},
            uid:false
          };
          this.data.build.text(index);
          var r = new leimnud.module.rpc.xmlhttp({
            url:this.options.dataServer,
            args:"action=addText&data="+{uid:this.options.uid,label:text,position:{x:pos.x,y:pos.y}}.toJSONString()
          });
          r.callback=function(rpc,index){
            var rs = rpc.xmlhttp.responseText.parseJSON();
            this.data.db.text[index].uid=rs.uid;
          }.extend(this,index);
          r.make();
        }

      }.extend(this)
    });
  },
  addGuide:function(evt,dir)
  {
    //var m = this.parent.dom.mouse(evt);
    var m = this.menu.cursor;
    var cpos = this.parent.dom.position(this.panels.editor.elements.content);
    var index = this.data.db.guide.length;
    var scl = {
      x:this.panels.editor.elements.content.scrollLeft,
      y:this.panels.editor.elements.content.scrollTop
    };
    var pos = {x:(m.x-cpos.x),y:(m.y-cpos.y)};
    this.data.db.guide[index]={
      position:((dir==="horizontal")?pos.y+scl.y:pos.x+scl.x),
      uid :false,
      direction:dir
    }
  //  alert(pos.x+":"+pos.y+":::"+scl.x+":"+scl.y)
    var data = this.data.db.guide[index];

    this.data.build.guide(index);
    var r = new leimnud.module.rpc.xmlhttp({
      url:this.options.dataServer,
      args:"action=addGuide&data="+{
        uid:this.options.uid,
        position:data.position,
        direction:data.direction}.toJSONString()
    });
    r.callback=function(rpc,index)
    {
      var rs = rpc.xmlhttp.responseText.parseJSON();
      var data = this.data.db.guide[index];
      data.uid=rs.uid || false;
    }.extend(this,index);
    r.make();
  }
};

/**
* Added By: Erik Amaru Ortiz <erik@colosa.com>
* Comment: This functionality make the window for panel DB Connection.
*/
var mainPanel;

function showDbConnectionsList(PRO_UID)
{
    if (typeof(this.Pm.menu) != undefined) {
        this.Pm.menu.remove();
    }
  mainPanel = new leimnud.module.panel();
  mainPanel.options = {
      size  :{w:640,h:450},
      position:{x:0,y:0,center:true},
      title :G_STRINGS.ID_DBS_LIST,
      theme :"processmaker",
      statusBar:false,
      control :{resize:false,roll:false,drag:true},
      fx  :{modal:true,opacity:true,blinkToFront:false,fadeIn:false,drag:true}
    };
    mainPanel.events = {
      remove: function() { delete(mainPanel); }.extend(this)
    };
  mainPanel.make();
  mainPanel.loader.show();
  var oRPC = new leimnud.module.rpc.xmlhttp({
      url : '../dbConnections/dbConnectionsAjax',
      args: 'action=showDbConnectionsList&PRO_UID='+PRO_UID
    });
    oRPC.callback = function(rpc) {
      mainPanel.loader.hide();
      var scs=rpc.xmlhttp.responseText.extractScript();
      mainPanel.addContent(rpc.xmlhttp.responseText);
      scs.evalScript();
    }.extend(this);
  oRPC.make();
}

function showCaseSchedulerList(PRO_UID)
{
    if (typeof(this.Pm.menu) != undefined) {
        this.Pm.menu.remove();
    }
  mainPanel = new leimnud.module.panel();
  mainPanel.options = {
      size  :{w:850,h:570},
      position:{x:0,y:0,center:true},
      title :G_STRINGS.ID_PROCESSMAP_CASE_SCHEDULER_TITLE,
      theme :"processmaker",
      statusBar:false,
      control :{resize:false,roll:false,drag:true},
      fx  :{modal:true,opacity:true,blinkToFront:false,fadeIn:false,drag:true}
    };
    mainPanel.events = {
      remove: function() { delete(mainPanel); }.extend(this)
    };
  mainPanel.make();
  mainPanel.loader.show();
  var oRPC = new leimnud.module.rpc.xmlhttp({
      url : 'processes_Ajax',
      args: 'action=case_scheduler&PRO_UID='+PRO_UID
    });
    oRPC.callback = function(rpc) {
      mainPanel.loader.hide();
      var scs=rpc.xmlhttp.responseText.extractScript();
      mainPanel.addContent(rpc.xmlhttp.responseText);
      scs.evalScript();
    }.extend(this);
  oRPC.make();
}

function showNewProcessMap(PRO_UID)
{
	window.location = "../bpmnDesigner?id="+PRO_UID;
}

function showLogCaseSchedulerList(PRO_UID)
{
  mainPanel = new leimnud.module.panel();
  mainPanel.options = {
      size  :{w:640,h:450},
      position:{x:0,y:0,center:true},
      title :"Case Scheduler Log List",
      theme :"processmaker",
      statusBar:false,
      control :{resize:false,roll:false,drag:true},
      fx  :{modal:true,opacity:true,blinkToFront:false,fadeIn:false,drag:true}
    };
    mainPanel.events = {
      remove: function() { delete(mainPanel); }.extend(this)
    };
  mainPanel.make();
  mainPanel.loader.show();
  var oRPC = new leimnud.module.rpc.xmlhttp({
      url : 'processes_Ajax',
      args: 'action=log_case_scheduler&PRO_UID='+PRO_UID
    });
    oRPC.callback = function(rpc) {
      mainPanel.loader.hide();
      var scs=rpc.xmlhttp.responseText.extractScript();
      mainPanel.addContent(rpc.xmlhttp.responseText);
      scs.evalScript();
    }.extend(this);
  oRPC.make();
}

function exitEditor()
{
  location.href = '../processes/mainInit';
}
function moldTitle(title, size)
{
  size = parseInt(size);
  chain = parseInt(title.length *6);
  if ((size - chain) < 0)
  {
    chain = parseInt((size/6)-33);
    newTitle = title.substring(0,chain);
    title = newTitle+"...";
  }
  return title;
}
