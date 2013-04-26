<html>
<head>
    <script type="text/javascript" src="{siteUrl}{URL_TRANSLATION_ENV_JS}"></script>
    <script type="text/javascript" src="{siteUrl}{URL_MABORAK_JS}"></script>
    <script type="text/javascript" src="{siteUrl}/js/jscalendar/lang/calendar-{sysLang}.js"></script>
    <script type="text/javascript" src="{siteUrl}/jsform/gulliver/dynaforms_Options.js"></script>

    <script type="text/javascript" src="{siteUrl}/jsform/{dynFileName}.js"></script>
    <!-- START BLOCK : grid_uids -->
    <script type="text/javascript" src="{siteUrl}/jsform/{gridFileName}.js"></script>
    <!-- END BLOCK : grid_uids -->
    <script type="text/javascript">
    var leimnud = new maborak();
    leimnud.make();
    leimnud.Package.Load("panel,validator,app,rpc,fx,drag,drop,dom,abbr",{ Instance:leimnud,Type:"module" });
    leimnud.exec(leimnud.fix.memoryLeak);
    if (leimnud.browser.isIphone) {
        leimnud.iphone.make();
    }
    leimnud.event.add(window,"load",function(){ loadForm_{formId}("{siteUrl}/sys{sysSys}/{sysLang}/{sysSkin}/gulliver/defaultAjaxDynaform")});
    </script>

    <script type="text/javascript">
    var aux1 = window.location.href.split("?");
    if (aux1[1]) {
        if (aux1[1] != "") {
            var aux2 = aux1[1].split("&");

            for (var i = 0; i <= aux2.length; i++) {
                if (aux2[i] == "__flag__=1") {
                    alert("Request sent!");
                }
            }
        }
    }
    </script>
</head>
<body>
{scriptCode}
<input type="hidden" name="PRO_UID"  value="{processUid}">
<input type="hidden" name="TASKS"    value="{taskUid}">
<input type="hidden" name="DYNAFORM" value="{dynaformUid}">
</form>
</body>
</html>

