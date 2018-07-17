<?php
// $Header: /cvsroot/html2ps/tree.navigation.inc.php,v 1.12 2007/03/15 18:37:35 Konstantin Exp $

function &traverse_dom_tree_pdf(&$root) {
  switch ($root->node_type()) {
  case XML_DOCUMENT_NODE:
    $child =& $root->first_child();
    while($child) {
      $body =& traverse_dom_tree_pdf($child);
      if ($body) { 
        return $body; 
      }
      $child =& $child->next_sibling();
    };
    break;
  case XML_ELEMENT_NODE:    
    if (strtolower($root->tagname()) == "body") { 
      return $root; 
    }

    $child =& $root->first_child(); 
    while ($child) {
      $body =& traverse_dom_tree_pdf($child);
      if ($body) { 
        return $body; 
      }
      $child =& $child->next_sibling();
    };
    
    $null = null;
    return $null;
  default:
    $null = null;
    return $null;
  }
};

function dump_tree(&$box, $level) {
  print(str_repeat(" ", $level));
  print(get_class($box).":".$box->uid."\n");

  if (isset($box->content)) {
    for ($i=0; $i<count($box->content); $i++) {
      dump_tree($box->content[$i], $level+1);
    };
  };
};

?>