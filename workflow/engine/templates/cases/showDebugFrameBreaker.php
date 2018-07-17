<?php

$filter = new InputFilter();
if (isset($_POST['NextStep'])) {
    $nextStep = $filter->xssFilterHard($_POST['NextStep'], "url"); ?>
    <div class="ui-widget-header ui-corner-all" width="100%" align="center">
    Processmaker - Debugger (Break Point)&nbsp;&nbsp;&nbsp;&nbsp;
    <input type="button" value="Continue" class="module_app_button___gray"
    onclick="javascript:location.href='<?php echo $nextStep; ?>'">
    </div>
    <?php
      }

