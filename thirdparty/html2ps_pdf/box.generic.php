<?php
// $Header: /cvsroot/html2ps/box.generic.php,v 1.72 2007/01/24 18:55:44 Konstantin Exp $

require_once(HTML2PS_DIR.'globals.php');

class GenericBox {
  var $_cache;
  var $_css;
  var $_left;
  var $_top;
  var $_parent;
  var $baseline;
  var $default_baseline;

  var $_cached_base_font_size;

  function GenericBox() {
    $this->_cache = array();
    $this->_css   = array();
    $this->_cached_base_font_size = null;

    $this->_left   = 0;
    $this->_top    = 0;

    $this->_parent = null;

    $this->baseline = 0;
    $this->default_baseline = 0;

    /**
     * Assign an unique box identifier
     */
    $GLOBALS['g_box_uid']++;
    $this->uid = $GLOBALS['g_box_uid'];
  } 

  function destroy() {
    unset($this->_cache);
    unset($this->_css);
    unset($this->_left);
    unset($this->_top);
    unset($this->_parent);
    unset($this->baseline);
    unset($this->default_baseline);
  }

  /**
   * see getProperty for optimization description
   */
  function setCSSProperty($code, $value) {
    static $cache = array();
    if (!isset($cache[$code])) {
      $cache[$code] =& CSS::get_handler($code);
    };

    $cache[$code]->replace_array($value, $this->_css);
  }

  /**
   * Optimization: this function is called very often,
   * so even a slight overhead for CSS::get_handler call
   * accumulates in a significiant processing delay.
   */
  function &getCSSProperty($code) {
    static $cache = array();
    if (!isset($cache[$code])) {
      $cache[$code] =& CSS::get_handler($code);
    };

    $value =& $cache[$code]->get($this->_css);
    return $value;
  }

  function show_postponed(&$driver) {
    $this->show($driver);
  } 

  function copy_style(&$box) {
    // TODO: object references 
    $this->_css = $box->_css;
  }

  /**
   * Optimization: _readCSSLength is usually called several times
   * while initializing box object. $base_font_size cound be calculated 
   * only once and stored in a static variable.
   */
  function _readCSSLengths($state, $property_list) {
    if (is_null($this->_cached_base_font_size)) {
      $font =& $this->getCSSProperty(CSS_FONT);
      $this->_cached_base_font_size = $font->size->getPoints();
    };

    foreach ($property_list as $property) {
      $value =& $state->getProperty($property);

      if ($value === CSS_PROPERTY_INHERIT) {
        $value =& $state->getInheritedProperty($property);
      };

      if (is_object($value)) {
        $value =& $value->copy();
        $value->doInherit($state);
        $value->units2pt($this->_cached_base_font_size);
      };

      $this->setCSSProperty($property, $value);
    }
  }

  function _readCSS($state, $property_list) {
    foreach ($property_list as $property) {
      $value = $state->getProperty($property);

      // Note that order is important; composite object-value could be inherited and
      // object itself could contain subvalues with 'inherit' value

      if ($value === CSS_PROPERTY_INHERIT) {
        $value = $state->getInheritedProperty($property);
      };

      if (is_object($value)) {
        $value = $value->copy();
        $value->doInherit($state);
      };

      $this->setCSSProperty($property, $value);
    }
  }

  function readCSS(&$state) {
    /**
     * Determine font size to be used in this box (required for em/ex units)
     */
    $value = $state->getProperty(CSS_FONT);
    if ($value === CSS_PROPERTY_INHERIT) {
      $value = $state->getInheritedProperty(CSS_FONT);
    };
    $base_font_size = $state->getBaseFontSize();

    if (is_object($value)) {
      $value = $value->copy();
      $value->doInherit($state);
      $value->units2pt($base_font_size);
    };

    $this->setCSSProperty(CSS_FONT, $value);

    /**
     * Continue working with other properties
     */

    $this->_readCSS($state,
                    array(CSS_COLOR,
                          CSS_DISPLAY,
                          CSS_VISIBILITY));

    $this->_readCSSLengths($state,
                           array(CSS_VERTICAL_ALIGN));
    
    // '-html2ps-link-destination'
    global $g_config;
    if ($g_config["renderlinks"]) {
      $this->_readCSS($state,
                      array(CSS_HTML2PS_LINK_DESTINATION));
    };

    // Save ID attribute value
    $id = $state->getProperty(CSS_HTML2PS_LINK_DESTINATION);
    if (!empty($id)) {
      if (!isset($GLOBALS['__html_box_id_map'][$id])) {
        $GLOBALS['__html_box_id_map'][$id] =& $this;
      };
    };
  }

  function show(&$driver) {
    // If debugging mode is on, draw the box outline
    global $g_config;
    if ($g_config['debugbox']) {
      // Copy the border object of current box
      $driver->setlinewidth(0.1);
      $driver->setrgbcolor(0,0,0);
      $driver->rect($this->get_left(), $this->get_top(), $this->get_width(), -$this->get_height());
      $driver->stroke();
    }

    // Set current text color
    // Note that text color is used not only for text drawing (for example, list item markers 
    // are drawn with text color)
    $color = $this->getCSSProperty(CSS_COLOR);
    $color->apply($driver);
  }

  /**
   * Render box having position: fixed or contained in such box
   * (Default behaviour)
   */
  function show_fixed(&$driver) {
    return $this->show($driver);
  }

  function pre_reflow_images() {}

  function offset($dx, $dy) {
    $this->_left += $dx;
    $this->_top  += $dy;
  }

  // Calculate the content upper-left corner position in curent flow
  function guess_corner(&$parent) {
    $this->put_left($parent->_current_x + $this->get_extra_left());
    $this->put_top($parent->_current_y - $this->get_extra_top());
  }

  function put_left($value) { 
    $this->_left = $value; 
  }

  function put_top($value)  { 
    $this->_top = $value + $this->getBaselineOffset(); 
  }

  /**
   * Get Y coordinate of the top content area edge
   */
  function get_top() { 
    return 
      $this->_top - 
      $this->getBaselineOffset(); 
  }

  function get_right() { 
    return $this->get_left() + $this->get_width(); 
  }

  function get_left() { 
    return $this->_left; 
  }

  function get_bottom() { 
    return $this->get_top() - $this->get_height();
  }

  function getBaselineOffset() { 
    return $this->baseline - $this->default_baseline; 
  }

  function reflow_anchors(&$driver, &$anchors) {
    if ($this->is_null()) { 
      return; 
    };

    $link_destination = $this->getCSSProperty(CSS_HTML2PS_LINK_DESTINATION);

    if ($link_destination !== "") {

      /**
       * Y=0 designates the bottom edge of the first page (without margins)
       * Y axis is oriented to the bottom.
       *
       * Here we calculate the offset from the bottom edge of first page PRINTABLE AREA
       * to the bottom edge of the current box
       */
      $y2 = $this->get_bottom() - mm2pt($driver->media->margins['bottom']);

      /**
       * Now let's calculate the number of the page corresponding to this offset.
       * Note that $y2>0 for the first page and $y2<0 on all subsequent pages
       */
      $page_fraction = $y2 / mm2pt($driver->media->real_height());

      /**
       * After the last operation we've got the "page fraction" between 
       * bottom of the first page and box bottom edge;
       *
       * it will be equal to:
       * 1 for the top of the first page, 
       * 0 for the bottom of the first page
       * -Epsilon for the top of the first page
       * -1 for the bottom of the second page
       * -n+1 for the bottom of the N-th page.
       */
      $page_fraction2 = -$page_fraction+1;

      /**
       * Here:
       * 0 for the top of the first page, 
       * 1 for the bottom of the first page
       * 1+Epsilon for the top of the first page
       * 2 for the bottom of the second page
       * n for the bottom of the N-th page.
       *
       * Keeping in mind said above, we may calculate the real page number, 
       * rounding it UP after calculation. The reason of rounding UP is simple:
       * pages are numbered starting at 1.
       */
      $page = ceil($page_fraction2);

      /**
       * Now let's calculate the coordinates on this particular page
       *
       * X coordinate calculation is pretty straight forward (and, actually, unused, as it would be 
       * a bad idea to scroll PDF horiaontally).
       */
      $x = $this->get_left();

      /**
       * Y coordinate should be calculated relatively to the bottom page edge 
       */     
      $y = mm2pt($driver->media->real_height()) * ($page - $page_fraction2) + mm2pt($driver->media->margins['bottom']);

      $anchors[$link_destination] = new Anchor($link_destination, 
                                               $page, 
                                               $x, 
                                               $y);
    };
  }

  function reflow(&$parent, &$context) {}

  function reflow_inline() { }

  function out_of_flow() { 
    return false; 
  }

  function get_bottom_margin() { return $this->get_bottom(); }

  function get_top_margin() { 
    return $this->get_top(); 
  }

  function get_full_height() { return $this->get_height(); }
  function get_width() { return $this->width; }

  function get_full_width() {
    return $this->width;
  }

  function get_height() {
    return $this->height;
  }

  function get_baseline() { 
    return $this->baseline;
  }

  function is_container() { return false; }

  function isVisibleInFlow() { return true; }

  function reflow_text() { return true; }

  /**
   * Note that linebox is started by any non-whitespace inline element; all whitespace elements before
   * that moment should be ignored.
   *
   * @param boolean $linebox_started Flag indicating that a new line box have just started and it already contains 
   * some inline elements 
   * @param boolean $previous_whitespace Flag indicating that a previous inline element was an whitespace element.
   */
  function reflow_whitespace(&$linebox_started, &$previous_whitespace) {
    return;
  }

  function is_null() {
    return false;
  }

  function isCell() {
    return false;
  }

  function isTableRow() {
    return false;
  }

  function isTableSection() {
    return false;
  }

  // CSS 2.1:
  // 9.2.1 Block-level elements and block boxes
  // Block-level elements are those elements of the source document that are formatted visually as blocks 
  // (e.g., paragraphs). Several values of the 'display' property make an element block-level: 
  // 'block', 'list-item', 'compact' and 'run-in' (part of the time; see compact and run-in boxes), and 'table'. 
  //
  function isBlockLevel() {
    return false;
  }

  function hasAbsolutePositionedParent() {
    if (is_null($this->parent)) {
      return false;
    };

    return 
      $this->parent->getCSSProperty(CSS_POSITION) == POSITION_ABSOLUTE ||
      $this->parent->hasAbsolutePositionedParent();
  }

  function hasFixedPositionedParent() {
    if (is_null($this->parent)) {
      return false;
    };

    return 
      $this->parent->getCSSProperty(CSS_POSITION) == POSITION_FIXED ||
      $this->parent->hasFixedPositionedParent();
  }

  /**
   * Box can be expanded if it has no width constrains and
   * all it parents has no width constraints
   */
  function mayBeExpanded() {
    $wc = $this->getCSSProperty(CSS_WIDTH);
    if (!$wc->isNull()) { return false; };

    if ($this->getCSSProperty(CSS_FLOAT) <> FLOAT_NONE) {
      return true;
    };

    if ($this->getCSSProperty(CSS_POSITION) <> POSITION_STATIC &&
        $this->getCSSProperty(CSS_POSITION) <> POSITION_RELATIVE) {
      return true;
    };
        
    if (is_null($this->parent)) { 
      return true;
    };

    return $this->parent->mayBeExpanded();
  }

  function isLineBreak() {
    return false;
  }

  function get_min_width_natural($context) {
    return $this->get_min_width($context);
  }

  function is_note_call() {
    return isset($this->note_call);
  }

  /* DOM compatibility */
  function &get_parent_node() {
    return $this->parent;
  }
}
?>