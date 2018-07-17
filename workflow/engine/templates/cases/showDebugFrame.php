<?php
/**
 * groups_Tree.php
 *
 * ProcessMaker Open Source Edition
 * Copyright (C) 2004 - 2008 Colosa Inc.23
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * For more information, contact Colosa Inc, 2566 Le Jeune Rd.,
 * Coral Gables, FL, 33134, USA, or email info@colosa.com.
 *
 */

/**
 * New Debugger interface
 *
 * @author Erik A. O. <erik@colosa.com>
 * @Date Wed Mar 17th. 2010
 */

$filter = new InputFilter();
$_SESSION = $filter->xssFilterHard($_SESSION, "url");
$nextStep = $filter->xssFilterHard($_POST['NextStep'], "url");

//variables
$oApp= new Cases();
$aFields = $oApp->loadCase($_SESSION['APPLICATION']);
$aStoredVarNames = array_keys($aFields['APP_DATA']);

$aVariables = Array();

for ($i=0; $i<count($_SESSION['TRIGGER_DEBUG']['DATA']); $i++) {
    $aVariables[$_SESSION['TRIGGER_DEBUG']['DATA'][$i]['key']] = $_SESSION['TRIGGER_DEBUG']['DATA'][$i]['value'];
}

$aVariables = array_merge($aFields['APP_DATA'], $aVariables);
$aVariables = $filter->xssFilterHard($aVariables);
ksort($aVariables);

//triggers

if (isset($_SESSION['TRIGGER_DEBUG']['info'])) {
    $aTriggers = $_SESSION['TRIGGER_DEBUG']['info'];
} else {
    $aTriggers[0] = $_SESSION['TRIGGER_DEBUG'];
}

foreach ($aTriggers as $aTrigger) {
    $triggers_name = Array();
    $triggers_code = Array();

    $t_time = $aTrigger['TIME'];
    if ($aTrigger['NUM_TRIGGERS'] != 0) {
        $triggers_onfly = $aTrigger['NUM_TRIGGERS']." trigger(s) was executed <font color='#641213'><b>".
            strtolower($aTrigger['TIME'])."</b></font><br/>";

        $cnt = 0;
        if (isset($aTrigger['TRIGGERS_NAMES'])) {
            foreach ($aTrigger['TRIGGERS_NAMES'] as $name) {
                $t_code = $aTrigger['TRIGGERS_VALUES'][$cnt]['TRI_WEBBOT'];
                //$t_code = str_replace('"', '\'',$t_code);
                //$t_code = addslashes($t_code);
                $t_code = Only1br($t_code);

                $triggers_name[] = $name;
                $triggers_code[] = $t_code;
                $cnt++;
            }
        }
    } else {
        $triggers_onfly = " No triggers found <font color='#641213'><b>".strtolower($t_time)."</b></font>";
    }
}

//errors
$DEBUG_POST =  array_unique($_SESSION['TRIGGER_DEBUG']['ERRORS']);

/////

function Only1br($string)
{
    return preg_replace("/(\r\n)+|(\n|\r)+/", "<br />", $string);
}

?>

<style>
  table.pmdebugger{
    border: 1px solid #777;
    padding: 0;
    border-spacing: 0;
    color: #000;
  }

  table.pmdebugger td{
    border: 1px solid #777;
    border-width: 0 0 1px 1px;
    padding-left: 0px;
    padding-right: 0px;
    color: #000;
    font:  8pt Tahoma,sans-serif,MiscFixed;
  }

  td.pmdebuggerHeader{
    font-weight: bold;
    color: #000;
    padding-left: 3px;
    padding-right: 3px;
    background-color: #C3C3C3;
    font:  8pt Tahoma,sans-serif,MiscFixed;
  }

  #debugger_vars{
    padding: 3px;
    overflow: auto;
  }

  #debugger_triggers{
    padding: 3px;
    overflow: auto;
  }

  #debugger_errors{
    padding: 3px;
    overflow: auto;
  }

  .data_view{
    padding: 3px;
    display:none;
    background: #F6F3F3;
    font-family: monospace;
  }
</style>

<script>
  function toggle(id){
    var o = document.getElementById(id);
    if(o.style.display != 'block'){
      o.style.display = 'block';
      newimg = new Image();
      newimg.src = '/images/pin-dn-on.gif';
      document.getElementById('img_'+id).src = newimg.src;
    } else {
      o.style.display = 'none';
      newimg = new Image();
      newimg.src = '/images/pin-up-on.gif';
      document.getElementById('img_'+id).src = newimg.src;
    }
  }
</script>

<div class="ui-widget-header ui-corner-all" width="100%" align="center">Processmaker - Debugger</div>

<div class="ui-widget-header ui-corner-all" width="50%" align="center">Triggers</div>
<div id="debugger_triggers" class="ui-accordion-header ui-helper-reset ui-state-active ui-corner-all">
  <table width='100%' cellspacing='0' cellpadding='0' border='1' style='border:0px;'>
    <tr>
      <td width='410px' class='treeNode' style='border:0px;background-color:transparent;'>
        <font color='#0B58B6'><?php echo $triggers_onfly; ?></font><br/>
        <?php if (sizeof($triggers_name) > 0) {?>
        <table class="pmdebugger" width="100%">
          <tr>
            <td width="5" class="pmdebuggerHeader">#</td>
            <td class="pmdebuggerHeader">Name</td>
            <td class="pmdebuggerHeader">&nbsp;</td>
          </tr>
    <?php
    foreach ($triggers_name as $k => $trigger) {?>
          <tr>
            <td>&nbsp;<?php echo ($k+1); ?>&nbsp;</td>
            <td>
              &nbsp;<?php echo $trigger; ?>
              <div id='trigger_<?php echo $k; ?>'
                class='ui-accordion-header ui-helper-reset ui-state-active ui-corner-all data_view'>
                <?php echo $triggers_code[$k]; ?>
              </div>
            </td>
            <td valign="top" width="5">
              <a href='#' onclick="toggle('trigger_<?php echo $k; ?>'); return false;">
                <img id='img_trigger_<?php echo $k; ?>' src='/images/pin-up-on.gif' border='0'>
              </a>
            </td>
          </tr>
    <?php
    } ?>
        </table>
          <?php
} ?>
      </td>
    </tr>
  </table>
</div>
<!---->

<div class="ui-widget-header ui-corner-all" width="50%" align="center">Variables</div>
<div id="debugger_vars" class="ui-accordion-header ui-helper-reset ui-state-active ui-corner-all">
  <table class="pmdebugger" width="100%">
    <tr>
      <td class="pmdebuggerHeader">Name</td><td class="pmdebuggerHeader">Value</td>
    </tr>
<?php
foreach ($aVariables as $sName => $aVariable) {?>
    <tr>
    <?php
    if (is_array($aVariable)) {?>
        <td valign="top"><font color="blue">&nbsp;<?php
        echo $sName; ?></font></td>
        <?php
    } else {?>
        <td>&nbsp;<?php echo $sName; ?></td>
        <?php
    }?>
      <td><?php echo expandVarView($aVariable, $sName)?></td>
    </tr>
    <?php
}?>
  </table>
</div>

<!---->

<?php
if (count($DEBUG_POST) > 0) {?>
    <div class="ui-widget-header ui-corner-all" width="50%" align="center">Errors</div>
    <div id="debugger_errors" class="ui-accordion-header ui-helper-reset ui-state-active ui-corner-all" align="left">

    <?php
    for ($i=0; $i<count($DEBUG_POST); $i++) {
        if (isset($DEBUG_POST[$i]['ERROR']) and $DEBUG_POST[$i]['ERROR'] != '') {?>
            <span class='treeNode'>
            <font color='red'>Error </font>
            <font color='#0B58B6'><?php echo str_replace('<br />', '',$DEBUG_POST[$i]['ERROR']);?></font>
            </span><br/>
            <?php
        }
    }
    for ($i=0; $i<count($DEBUG_POST); $i++) { ?>
        <?php
        if (isset($DEBUG_POST[$i]['FATAL']) and $DEBUG_POST[$i]['FATAL'] != '') {?>
            <span class='treeNode'>
            <font color='red'>Fatal error </font>
            <font color='#0B58B6'> <?php echo str_replace('<br />', '',$DEBUG_POST[$i]['FATAL']); ?></font>
            </span><br/>
            <?php
        }
    }
} else { ?>
    <div class="ui-widget-header ui-corner-all" width="50%" align="center">No errors reported</div>
    <?php
}?>
</div>

<!---->

<?php if (isset($nextStep)) {?>
    <input type="button" value="Continue" class="module_app_button___gray" onclick="javascript:location.href='
    <?php echo $nextStep; ?>'">
    <?php
}?>


<?php

function expandVarView($a, $name)
{
    if (is_array($a)) {
        echo "<a href='#' onclick=\"toggle('data_view_{$name}'); return false;\">
            <img id='img_data_view_{$name}' src='/images/pin-up-on.gif' border='0'></a>
            <div id='data_view_{$name}' class='data_view'><table class='pmdebugger' width='100%'>";
        foreach ($a as $k => $v) {
            echo "<tr><td valign='top'>&nbsp;$k</td><td>";
            expandVarView($v, "{$name}_{$k}");
            echo "</td></tr>";
        }
        echo "</table><div>";
    } else {
        echo ($a=='')? '&nbsp;': "&nbsp;$a";
    }
}

