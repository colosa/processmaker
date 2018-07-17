function ajax_post(action, form, method, callback, asynchronous) {
    document.getElementById("dyn_forward").onclick();
    window.onload = function () {
        method();
    };
}
function dynaFormChanged(frm) {
    return false;
}
/**
 * Create fake submit button to force send data formpost action
 * and after this is destryed
 * @param formStep
 */
function submitNextStep(formStep) {
    $("#" + formStep.id).submitForm();
    return this;
}
$(window).load(function () {
    var delIndexDefault = "0",
        dyn_uid = window.dyn_uid || null;
    if (pm_run_outside_main_app === 'true') {
        if (parent.showCaseNavigatorPanel) {
            parent.showCaseNavigatorPanel('DRAFT');
        }
        if (parent.setCurrent) {
            parent.setCurrent(dyn_uid);

        }
    }
    var sesi = document.location.href;
    function loadAjaxParams () {
        var url;
        var action;
        var method;

        if (filePost) {
            url = location.protocol + '//' + location.host;
            //In case the form is in review
            if (filePost.indexOf('Supervisor') >= 0){
                action = 'cases_SaveDataSupervisor?UID=' + dyn_uid;
                url += '/sys' + workspace + '/en/neoclassic/cases/' + action;
            } else if(filePost.indexOf('Email') >= 0){ //In case the form is sent as Email response
                action = filePost;
                url += '/sys' + workspace + '/en/neoclassic/services/' + action;
            } else { //In case the form is in web entry
                action = prj_uid + '/' + filePost;
                url += '/sys' + workspace + '/en/neoclassic/' + action;
            }
            method = 'POST';
        } else if (app_uid){ //In case the form is in running cases
        	if(sesi.search("gmail") != -1){
        		action = "cases_SaveData?UID=" + dyn_uid + "&APP_UID=" + app_uid + "&gmail=1";
        	}else{
        		action = "cases_SaveData?UID=" + dyn_uid + "&APP_UID=" + app_uid;
        	}
            url = location.protocol + '//' + location.host;

            url += '/sys' + workspace + '/en/neoclassic/cases/' + action;
            method = 'POST';
        }
        return {
            url: url,
            action: action,
            method: method
        };
    }
    var data = jsondata;
    window.dynaform = new PMDynaform.core.Project({
        data: data,
        delIndex: window.delIndex ? window.delIndex :  delIndexDefault,
        dynaformUid: dyn_uid,
        isRTL: window.isRTL,
        onBeforePrintHandler : function () {
            var nodeClone = $(".pmdynaform-container").clone();
            nodeClone.addClass("printing-form");
            $("body").children().hide();
            $("body").append(nodeClone );
        },
        onAfterPrintHandler : function () {
            var nodeClone;
            nodeClone = $(".printing-form");
            nodeClone.remove();
            $("body").children().show();
        },
        formAjax: loadAjaxParams(),
        keys: {
            server: httpServerHostname,
            projectId: prj_uid,
            workspace: workspace
        },
        token: credentials,
        submitRest: false,
        onLoad: function () {
            var dynaformname = document.createElement("input"),
                appuid,
                arrayRequired,
                form,
                dyn_forward;
            dynaformname.type = "hidden";
            dynaformname.name = "__DynaformName__";
            dynaformname.value = __DynaformName__;
            appuid = document.createElement("input");
            appuid.type = "hidden";
            appuid.name = "APP_UID";
            appuid.value = app_uid;
            arrayRequired = document.createElement("input");
            arrayRequired.type = "hidden";
            arrayRequired.name = "DynaformRequiredFields";
            arrayRequired.value = fieldsRequired;
            form = document.getElementsByTagName("form")[0];
            if (form) {
                if(sesi.search("gmail") != -1){
                    form.action = filePost ? filePost : "cases_SaveData?UID=" + dyn_uid + "&APP_UID=" + app_uid + "&gmail=1";
                } else {
                    form.action = filePost ? filePost : "cases_SaveData?UID=" + dyn_uid + "&APP_UID=" + app_uid;
                }
                form.method = "post";
                form.setAttribute("encType", "multipart/form-data");
                form.appendChild(dynaformname);
                form.appendChild(appuid);
                form.appendChild(arrayRequired);
                dyn_forward = document.getElementById("dyn_forward");
                dyn_forward.onclick = function (e) {
                    e.preventDefault();
                    e.stopPropagation();
                    submitNextStep(form);
                };
                if (triggerDebug === true) {
                    showdebug();
                }
            }

        }
    });
});