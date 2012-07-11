<?php if (isset($_POST['NextStep'])) {?>
    <div class="ui-widget-header ui-corner-all" width="100%" align="center">
    Processmaker - Debugger (Break Point)&nbsp;&nbsp;&nbsp;&nbsp;
    <input type="button" value="Continue" class="module_app_button___gray"
    onclick="javascript:location.href='<?php echo $_POST['NextStep']; ?>'">
    </div>
    <?php
      }

