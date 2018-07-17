<?php
/**
 * inc.dynaForms.php
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

class Node {
  var $name = "";
  var $attribute = NULL;
  var $value = NULL;
  var $children = NULL;
}

$header = "";
$tree = NULL;

function read_config_file(){
  global $home;
  $directory = PATH_DYNAFORM;
  $car = substr ($directory, strlen ($directory)-1, 1);
  if ($car=='/' ) $directory = substr( $directory, 0, strlen($directory)- 1 );
  $home = $directory;
  
  global $showlang;
  $showlang = "es,en";
}

function parseNodo1 ( $token ) {
  //parse de  <name atr=1  attr=2 ... > en un nuevo nodo.
  $nodo = new Node;
  $nodo->children = NULL;
  $tok = ""; $n = 0;
  for ($i=0; $i<strlen ($token); $i++) {
    $car = $token[$i];
    if ($car == ' ' || $car == "=") {
      if (strlen($tok) > 0 ) { $tokens [$n++] = $tok; $tok = ""; }
      if ($car == '='      ) { $tokens [$n++] = $car; }
    } 
    else{
      $tok .= $car;
    }
  }
  if (strlen($tok) > 0 ) { $tokens [$n++] = $tok; $tok = ""; }
 
  //asignar los valores para el nombre del nodo y sus attributos   
  $nodo->name = trim($tokens[0]);
  for ($i=1; $i< $n; $i++ ) {
    $aux = $tokens[$i+2];
    if ($aux[0] == $aux[strlen($aux)-1] || $aux[0] == '"') $aux = substr ($aux,1,strlen($aux)-2);
    $key = $tokens[$i]; $val = "true";
    if ($tokens[$i+1]== "=") {$val = $aux; $i+=2;}
//    $nodo->attribute = array_merge ( $nodo->attribute, array ( $key => $val ) );
    $nodo->attribute[$key] = $val;
  }
  return $nodo;
}

function getTag () {
  global $buffer;
  global $bufIndex;
  global $size;
  global $header;
  global $tree;
  $token = ""; 

  //PRIMER BUCLE buscar tag >
  while ($bufIndex <= $size && $car !='>') {
    $token .= $car;
    $car = $buffer [$bufIndex++];
  }
  if ($token[0] == "?") {
    $header = $token;
    return;
  }
  $nodo = parseNodo1 ($token); 

  //SEGUNDO BUCLE buscar VALUE
  $car = $buffer [$bufIndex++]; $token = "";
  while ($bufIndex <= $size && $car !='<') {
    $token .= $car;
    $car = $buffer [$bufIndex++];
  }
  $nodo->value = trim($token);


  //TERCER BUCLE buscar </tag>
  $res = 0;
  while ($res == 0) {
    if ($car != "<") while ( ($car = trim( $buffer [$bufIndex++] )) == "");
    if ($car=="<") {
      $res = 0;
      $car = $buffer [$bufIndex++];
      //ADDCHILD nodo recien creado
      if ($car != "/") { 
        $bufIndex --;
        $n = count($nodo->children);
        $nodo->children[$n] = getTag ();
      } else $res = 1;
    } 
    else die ("se esperaba '<' y se encontro $car");
  }

  //se supone que se ha leido "</" y continuamos con el nombre
  $token = "";
  $car = $buffer [$bufIndex++];
  while ($bufIndex <= $size && $car !='>') {
    $token .= $car;
    $car = $buffer [$bufIndex++];
  }
  if ( strcmp ($nodo->name, trim($token)) != 0) die ("no corresponde fin de tag &lt;/$token> con &lt;" . $nodo->name. ">"); 

  return $nodo;
}

function parseFile ($filename) {
  global $tree;
  global $buffer;
  global $bufIndex;
  global $size;
 
  if (strlen($filename) <= 0) die ("invalid filename");

  if ( ! file_exists ( $filename) ) die ("This file $filename does not exist");
  if ( ! is_file ( $filename) )     die ("$filename is not a file");

  $size = filesize ($filename);
  $fp = fopen ($filename, "rb");
  $i = 0;

  $aux = fread($fp, $size);
  $buffer = "";
  for ($i = 0; $i < $size; $i++) {
    if (!($aux[$i] == "\n" || $aux[$i] == "\r" || $aux[$i] == "\t")) $buffer .= $aux[$i];
  }
  fclose ($fp);
  $bufIndex = 0;
  
  $size = strlen ($buffer);

  while ( $bufIndex <= $size ) {
    $car = $buffer [$bufIndex++];
    if ($car == '<' ) 
      $tree = getTag();
  }
}



function saveXml(){
  global $tree;
  global $header;
  global $filename;
  global $curDir;
  global $onlyName;

  //crear el archivo Xml
//  print "$filename <br>";

  $fp = fopen ($filename, "w+");
  fputs ($fp,"<$header>\n");

  $aux = explode ( '/', $filename);
  $onlyName = $aux[count($aux)-1];
  $curDir = $_POST['curDir'];

  fputs ($fp,"<dynaForm name=\"$onlyName\" basedir=\"$curDir\">$tree->value\n");

  for ($i = 0; $i < count($tree->children); $i++) {
    $nodo = $tree->children[$i];

    fputs ($fp,"<$nodo->name ");
    if ( is_array ($nodo->attribute) )
      foreach ( $nodo->attribute as $attr=>$attrValue )
        fputs ($fp,"$attr=\"$attrValue\" ");
    fputs ($fp,">\n");

    //si es un dropdown-SQL la sentencia select va como valor del nodo
    if (strlen ($nodo->value) > 0) {
      fputs ($fp,"$nodo->value\n");
    }
      
    for ($j = 0; $j < count($nodo->children); $j++) {
      $lang = $nodo->children[$j];
      fputs ($fp, "  <$lang->name>$lang->value");
      
      //si tiene etiquetas del tipo option
      if (is_array($lang->children)) {
        fputs ($fp, "\n");
        for ($k = 0; $k < count($lang->children); $k++) {
          $option = $lang->children[$k];
          fputs ($fp, "<option name=\"" . $option->attribute['name'] . "\">$option->value</option>\n");
        }
      } //if tiene etiquetas del tipo opcion.
      fputs ($fp, "</$lang->name>\n");

    } //for j
    fputs ($fp,"</$nodo->name>\n");

  } //for i
  fputs ($fp,"</dynaForm>\n");
  fclose ($fp);
}

?>
