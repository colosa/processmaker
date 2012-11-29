<html>
  <style type="text/css">
   .Footer .content {
      padding   :0px !important;
   }  
   *html body {
      overflow-y: hidden;
   }
  </style>
  <body onresize="autoResizeScreen()" onload="autoResizeScreen()">
  <iframe name="casesFrame" id="casesFrame" src ="../dashboard" width="99%" height="768" frameborder="0">
    <p>Your browser does not support iframes.</p>
  </iframe>
  </body>
  <script>
    if ( document.getElementById('pm_submenu') )
      document.getElementById('pm_submenu').style.display = 'none';
      document.documentElement.style.overflowY = 'hidden';

      var oClientWinSize = getClientWindowSize();
      

    
    function autoResizeScreen() {
      oCasesFrame    = document.getElementById('casesFrame');
      height = getClientWindowSize().height-90;
      oCasesFrame.style.height = height;
      oCasesSubFrame = oCasesFrame.contentWindow.document.getElementById('casesSubFrame');
        if(oCasesSubFrame){
          oCasesSubFrame.style.height = height-5;
        }
        else {
          setTimeout('autoResizeScreen()', 2000);
        }


    }
  </script>
</html>