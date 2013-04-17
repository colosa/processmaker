<body onresize="autoResizeScreen()" onload="autoResizeScreen()">
  <style type="text/css">
   *html body {
      overflow-y: hidden;
   }
  </style>
<iframe name="adminFrame" id="adminFrame" src ="main_init" width="99%" height="768" frameborder="0">
  <p>Your browser does not support iframes.</p>
</iframe>
</body>
<script>
  var content = document.getElementById('logo');
  content = (content != null) ? 60 : 90;
  oClientWinSize = getClientWindowSize();
  if ( document.getElementById('pm_submenu') ) 
    document.getElementById('pm_submenu').style.display = 'none';
  document.documentElement.style.overflowY = 'hidden';
  
  function autoResizeScreen() {
    oCasesFrame    = document.getElementById('adminFrame');
    oClientWinSize = getClientWindowSize();
    height = oClientWinSize.height-content;//90
    oCasesFrame.style.height = height;
    oCasesSubFrame = oCasesFrame.contentWindow.document.getElementById('setup-frame');

    if(oCasesSubFrame)
      oCasesSubFrame.style.height = height-5;
    else
      setTimeout('autoResizeScreen()', 2000);
  }
</script>
</html>