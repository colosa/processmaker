<html>
<style>
.Footer{
	font		:normal 8pt sans-serif,Tahoma,MiscFixed !important; 
	color		:#000 !important;
	height		:0px !important;
	text-align	:center !important;
}
.Footer .content{
	color		:black !important;
	padding		:0px !important;
}
</style>
<body onresize="autoResizeScreen()" onload="autoResizeScreen()">
<iframe name="frameMain" id="frameMain" src ="../processes/mainInit" width="99%" height="768" frameborder="0">
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
  function autoResizeScreen() {
	  var containerList1,
		  containerList2,
		  oCasesFrame,
		  oClientWinSize,
		  height,
		  oCasesSubFrame;
	  oCasesFrame    = document.getElementById('frameMain');
	  oClientWinSize = getClientWindowSize();

	  containerList1 = document.getElementById("pm_header");
	  if (document.getElementById("mainMenuBG") &&
		  document.getElementById("mainMenuBG").parentNode &&
		  document.getElementById("mainMenuBG").parentNode.parentNode &&
		  document.getElementById("mainMenuBG").parentNode.parentNode.parentNode &&
		  document.getElementById("mainMenuBG").parentNode.parentNode.parentNode.parentNode){
		  containerList2 = document.getElementById("mainMenuBG").parentNode.parentNode.parentNode.parentNode;
	  }
	  if (containerList1 === containerList2) {
		  height = oClientWinSize.height - containerList1.clientHeight;
		  oCasesFrame.style.height = height;
		  if (oCasesFrame.height ) {
			  oCasesFrame.height = height;
		  }
	  } else {
		  height = getClientWindowSize().height-90;
		  oCasesFrame.style.height = height + 'px';
		  oCasesSubFrame = oCasesFrame.contentWindow.document.getElementById('casesSubFrame');
		  if(oCasesSubFrame){
			  oCasesSubFrame.style.height = (height-5) + 'px';
		  } else {
			  setTimeout('autoResizeScreen()', 2000);
		  }
	  }
  }
  function getStyle(targetElement,styleProp) {
    if (targetElement) {
      if (targetElement.currentStyle) return targetElement.currentStyle[styleProp];
      else if (window.getComputedStyle) return document.defaultView.getComputedStyle(targetElement,null).getPropertyValue(styleProp);
    }
  }
</script>
</html>