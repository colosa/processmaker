<?php

class Entity_Base
{
  
  /**
   * this function check if a field is in the data sent in the constructor
   * you can specify an array, and this function will use like alias
   */
  protected function validateField($field, $default = false)
  {
    $fieldIsEmpty = true;
    
    // this is a trick, if $fields is a string, $fields will be an array with
    // one element
    if (is_array ($field)) {
      $fields = $field;
    }
    else {
      $fields = array ();
      $fields [] = $field;
    }
    
    // if there are aliases for this field, evaluate all aliases and take the
    // first occurence
    foreach ($fields as $k => $f) {
      if (isset ($this->temp [$f])) {
        $fieldIsEmpty = false;
        return $this->temp [$f];
      }
    }
    
    // field empty means the user has not sent a value for this Field, so we are
    // using the default value
    if ($fieldIsEmpty) {
      if ($default !== false) {
        return $default;
      }
    }
  }
  
  protected function validateRequiredFields($requiredFields = array())
  {
    foreach ($requiredFields as $k => $field) {
      if ($this->{$field} === NULL) {
        throw (new Exception ("Field $field is required in " . get_class ($this)));
        die ();
      }
    }
  }
  
  /**
   *
   *
   *
   * Copy the values of the Entity to the array of aliases
   * The array of aliases must be defined.
   *
   * @return Array of alias with the Entity values
   */
  public function getAliasDataArray()
  {
    $aAlias = array ();
    // get aliases from class
    $className = get_class ($this);
    if (method_exists ($className, 'GetAliases')) {
      $aliases = call_user_func (array (
          $className,
          'GetAliases' 
      ));
      // $aliases = $className::GetAliases ();
      foreach ($this as $field => $value)
        if (isset ($aliases [$field])) {
          // echo "Field exists in Aliases: " . $field . "\n";
          // echo "Alias Name:" . $aliases[$field] . "\n";
          // echo "Alias value:" . $value . "\n";
          $aAlias [$aliases [$field]] = $value;
        }
    }
    
    return $aAlias;
  }
  
  /**
   *
   *
   *
   * Set the data from array of alias to Entity
   *
   * @param $aAliasData array
   *          of data of aliases
   */
  public function setAliasDataArray($aAliasData)
  {
    // get aliases from class
    $className = get_class ($this);
    if (method_exists ($className, 'GetAliases')) {
      $aliases = call_user_func (array (
          $className,
          'GetAliases' 
      ));
      // $aliases = $className::GetAliases ();
      foreach ($this as $field => $value)
        if (isset ($aliases [$field]))
          $this->{$field} = $aAliasData [$aliases [$field]];
    }
  }
  
  /**
   *
   *
   *
   * Initialize object with values from $data.
   * The values from data use properties or alias array.
   *
   * @param
   *          $data
   */
  protected function initializeObject($data)
  {
    // get aliases from class
    $className = get_class ($this);
    $aliases = array ();
    $swAliases = false;
    if (method_exists ($className, 'GetAliases')) {
      $aliases = call_user_func (array (
          $className,
          'GetAliases' 
      ));
      // $aliases = $className::GetAliases ();
      $swAliases = true;
    }
    // use object properties or aliases to initialize
    foreach ($this as $field => $value)
      if (isset ($data [$field])) {
        $this->$field = $data [$field];
      }
      elseif ($swAliases && isset ($aliases [$field]) && isset ($data [$aliases [$field]])) {
        $this->$field = $data [$aliases [$field]];
      }
  }
  
  public function serialize()
  {
    if (isset ($this->temp))
      unset ($this->temp);
    return serialize ($this);
  }
  
  public function unserialize($str)
  {
    $className = get_class ($this);
    $data = unserialize ($str);
    return new $className ($data);
  }

}