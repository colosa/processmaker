<?php
/**
 * install.php
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
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 * For more information, contact Colosa Inc, 2566 Le Jeune Rd.,
 * Coral Gables, FL, 33134, USA, or email info@colosa.com.
 */

/**
 * Default home page view
 *
 * @author MaBoRaK
 * @version 0.1
 */
if ($_POST && isset( $_POST['phpinfo'] )) {
    phpinfo();
    die();
}
echo '<?xml version="1.0" encoding="UTF-8" ?>';
?>
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Processmaker Installer</title>
<script type='text/javascript' src='/js/maborak/core/maborak.js'></script>
<link rel="stylesheet" type="text/css"
	href="/js/maborak/samples/style.css" />

<script type='text/javascript' src='/sys/en/classic/install/install.js'></script>
<script type='text/javascript'>
   var ifr;
   var forceCssLoad = true;
   var grid,winGrill, leimnud = new maborak(forceCssLoad);
   var inWIN = false;
   leimnud.make();
   leimnud.Package.Load("dom,validator,app,rpc,fx,drag,drop,panel,grid,abbr",{Instance:leimnud,Type:"module"});
   leimnud.Package.Load("json",{Type:"file"});
   leimnud.exec(leimnud.fix.memoryLeak);
   var inst;
   leimnud.event.add(window,'load',function(){myload();});
   var myload = function() {
      if (typeof(DOM) === 'undefined') {
        setTimeout("myload();", 1000);
        return;
      }
      inst = new leimnud.module.panel();
      inst.options={
         size:{w:document.body.offsetWidth-50,h:825},
         title :"",
         position:{x:2,y:2,center:true},
         statusBar:false,
         control:{
            roll  :false,
            close :false
         },
         fx:{
            shadow:false,
            fadeIn:false
         }
      };
      inst.setStyle={
         content:{padding:2}
      };
      var classInstaller = new installer();
      inst.tab={
         optWidth:190,
         manualDisabled:true,
         step  :(leimnud.browser.isIE?-1:5),
         options:[{
            title :"Configuration",
            content  :function()
            {
               classInstaller.make({
                  server   :"installServer.php",
                  path_data:"<?php echo defined('PATH_DATA')?PATH_DATA:PATH_TRUNK.'shared';?>",
                  path_compiled:"<?php echo defined('PATH_C')?PATH_C:PATH_TRUNK.'compiled';?>",
                  path_trunk:"<?php echo PATH_CORE;?>"
               });
            },
            selected:true
         },
         {
            title :"Installation",
            noClear : true,
            content  :classInstaller.install
         }

         ]
      };
      inst.make();
   };
   </script>
<style>
input {
	font: normal 8pt sans-serif, Tahoma, MiscFixed;
}

body {
	background-color: white;
	font: normal 8pt sans-serif, Tahoma;
}

.inst_table {
	width: 100%;
	border-collapse: collapse;
	font: normal 8pt Tahoma, sans-serif;
}

.inst_td0 {
	width: 60%;
	text-align: right;
	border: 1px solid #CCC;
	padding: 5px;
}

.inst_td1 {
	font-weight: bold;
	width: 40%;
	padding: 5px;
	border: 1px solid #CCC;
	text-align: center;
}

.tdNormal,.tdOk,.tdFailed {
	font-weight: bold;
	border: 1px solid #CCC;
	text-align: center;
}

.tdOk {
	font-weight: bold;
	color: green;
	padding: 6px;
}

.tdFailed {
	font-weight: bold;
	color: red;
}

.title {
	text-align: left;
	padding-left: 10px;
}

.inputNormal,.inputOk,.inputFailed {
	width: 100%;
	border: 1px solid #666;
	border-left: 3px solid #666;
	font: normal 8pt Tahoma, sans-serif;
	text-align: center;
}

.inputOk {
	border: 1px solid green;
	border-left: 3px solid green;
}

.inputFailed {
	border: 1px solid red;
	border-left: 3px solid red;
}

.button {
	font: normal 8pt Tahoma, MiscFixed, sans-serif;
	border: 1px solid #afafaf;
	margin-left: 2px;
	color: black;
	cursor: pointer;
}

.buttonHover {
	border: 1px solid #666;
	background-position: 0 -8;
}

.buttonDisabled {
	border: 1px solid #9f9f9f;
	background-position: 0 -10;
	color: #9f9f9f;
	cursor: default;
}
</style>
</head>

<body>
<?php
//exec("mkdir /var/www/html/asas",$console);
?>
</body>
</html>

