<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  
  <title></title>
  
  {literal}
  <style type="text/css">
  body {
    margin: 0;
    padding: 0;

    background: #FFFFFF;
    color: #000000;
    font: 85% arial, verdana, helvetica, sans-serif;
  }

  .dataGrid {
    border: 1px solid #48627A;
    padding: 0;

    width: 99%;
    height: 100%;

    background: #FFFFFF;
    text-align: left;
  }

  .dataGridTitle{
    border: 1px solid #0D1115;

    padding-top: 1px;
    padding-right: 3px;
    padding-bottom: 1px;
    padding-left: 3px;

    background: #48627A;
    color: #FFFFFF;
    text-align: center;

    font: bold 0.8em verdana, arial, helvetica, sans-serif;
  }

  .dataGridElement{
    padding-top: 1px;
    padding-right: 3px;
    padding-bottom:1px;
    padding-left: 3px;

    background: #E8EAEA;
    color: #000000;

    font: 0.8em verdana, arial, helvetica, sans-serif;
  }
  </style>
  {/literal}
</head>
<body>

<div class="dataGrid">
  <table width="100%" border="0" cellspacing="3" cellpadding="0">
    <tr>
      <th class="dataGridTitle">User name</th>
      <th class="dataGridTitle">Full name</th>
      <th class="dataGridTitle">Status</th>
    </tr>
  
    {foreach from=$user item=item}
    <tr>
      <td class="dataGridElement">{$item.userName}</td>
      <td class="dataGridElement">{$item.fullName}</td>
      <td class="dataGridElement">{$item.status}</td>
    </tr>
    {/foreach}
  </table>
</div>

<div style="margin-top: 0.25em;"><strong>Note: </strong>{$note}</div>
</html>