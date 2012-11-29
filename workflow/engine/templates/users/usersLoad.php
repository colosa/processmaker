<html>
<style>
.Footer{
  font       : normal 8pt sans-serif,Tahoma,MiscFixed !important;
  color      : #000 !important;
  height     : 0px !important;
  text-align : center !important;
}
.Footer .content{
  color   : black !important;
  padding : 0px !important;
}
</style>
<body onresize="autoResizeScreen()" onload="autoResizeScreen()">
<iframe name="frameMain" id="frameMain" src ="../users/usersInit" width="100%" height="200" frameborder="0">
  <p>Your browser does not support iframes.</p>
</iframe>
</body>
<script>
  oClientWinSize = getClientWindowSize();
  h = getStyle(document.getElementById('pm_menu'),'top');
  h = h.replace("px", "");
  h = parseInt(h) + 18;
  if ( document.getElementById('pm_submenu') ) 
    document.getElementById('pm_submenu').style.display = 'none';
  document.documentElement.style.overflowY = 'hidden';
  function autoResizeScreen()
  {
    oCasesFrame    = document.getElementById('frameMain');
    oClientWinSize = getClientWindowSize();
    height         = oClientWinSize.height - 105;
    oCasesFrame.style.height = height;
  }
  function getStyle(targetElement,styleProp)
  {
    if (targetElement) {
      if (targetElement.currentStyle) return targetElement.currentStyle[styleProp];
      else if (window.getComputedStyle) return document.defaultView.getComputedStyle(targetElement,null).getPropertyValue(styleProp);
    }
  }
</script>
</html>