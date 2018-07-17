<?php

/**
 *
 * @package gulliver.system
 *
 */

class XmlFormFieldHTML extends XmlFormFieldTextarea
{
    /* //'default','office2003','silver'
    var $skin       = 'default';
    //'Default','Basic'
    var $toolbarSet = 'Default';
    var $width  = '90%';
    var $height = '200' ;
    var $cols   = 40;
    var $rows   = 6;
    function render( $value , $owner=NULL ) {
    if ($this->mode==='edit') {
      if ($this->readOnly)
        $html='<textarea id="form['.$this->name.']" name="form['.$this->name.']" cols="'.$this->cols.'" rows="'.$this->rows.'" style="'.$this->style.'" wrap="'.htmlentities($this->wrap,ENT_QUOTES,'UTF-8').'" class="FormTextArea" readOnly>'.$this->htmlentities( $value ,ENT_COMPAT,'utf-8').'</textarea>';
      else
        $html='<textarea id="form['.$this->name.']" name="form['.$this->name.']" cols="'.$this->cols.'" rows="'.$this->rows.'" style="'.$this->style.'" wrap="'.htmlentities($this->wrap,ENT_QUOTES,'UTF-8').'" class="FormTextArea" >'.$this->htmlentities( $value ,ENT_COMPAT,'utf-8').'</textarea>';
    } elseif ($this->mode==='view') {
      $html='<textarea id="form['.$this->name.']" name="form['.$this->name.']" cols="'.$this->cols.'" rows="'.$this->rows.'" readOnly style="border:0px;backgroud-color:inherit;'.$this->style.'" wrap="'.htmlentities($this->wrap,ENT_QUOTES,'UTF-8').'"  class="FormTextArea" >'.$this->htmlentities( $value ,ENT_COMPAT,'utf-8').'</textarea>';
    } else {
      $html='<textarea id="form['.$this->name.']" name="form['.$this->name.']" cols="'.$this->cols.'" rows="'.$this->rows.'" style="'.$this->style.'" wrap="'.htmlentities($this->wrap,ENT_QUOTES,'UTF-8').'"  class="FormTextArea" >'.$this->htmlentities( $value ,ENT_COMPAT,'utf-8').'</textarea>';
    }
    return $html;
    }*/
    /**
     * attachEvents function is putting its events
     *
     * @access public
     * @param string $element
     * @return string
     *
     */
    function attachEvents ($element)
    {
        $html = 'var _editor_url = "";editor_generate("form[' . $this->name . ']");';
        return $html;
    }
}

