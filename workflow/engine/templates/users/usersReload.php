<html>
<style>

</style>
<body onresize="autoResizeScreen()" onload="autoResizeScreen()">
<iframe name="frameMain" id="frameMain" src ="../users/usersInit" width="100%" height="200" frameborder="0" style >
  <p>Your browser does not support iframes.</p>
</iframe>
</body>
<script>
  function autoResizeScreen() {
    oCasesFrame    = document.getElementById('frameMain');
    oClientWinSize = getClientWindowSize();
    height = oClientWinSize.height-5;
    oCasesFrame.style.height = height;   
  }

 
</script>
</html>