<?php
// $Header: /cvsroot/html2ps/css.visibility.inc.php,v 1.5 2006/09/07 18:38:15 Konstantin Exp $

define('VISIBILITY_VISIBLE',0);
define('VISIBILITY_HIDDEN',1);
define('VISIBILITY_COLLAPSE',2); // TODO: currently treated is hidden

class CSSVisibility extends CSSPropertyStringSet {
  function CSSVisibility() { 
    $this->CSSPropertyStringSet(false, 
                                false,
                                array('inherit'  => CSS_PROPERTY_INHERIT,
                                      'visible'  => VISIBILITY_VISIBLE,
                                      'hidden'   => VISIBILITY_HIDDEN,
                                      'collapse' => VISIBILITY_COLLAPSE)); 
  }

  function default_value() { return VISIBILITY_VISIBLE; }

  function getPropertyCode() {
    return CSS_VISIBILITY;
  }

  function getPropertyName() {
    return 'visibility';
  }
}

CSS::register_css_property(new CSSVisibility);

?>