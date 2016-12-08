<?php

namespace PHuby\Attribute;

use PHuby\AbstractAttribute;
use PHuby\Helpers\Validator\IntegerValidator;
use PHuby\Error;

class IntAttr extends AbstractAttribute implements AttributeInterface {

  public function set($value) {
    // Check if this is an attribute object
    if(is_object($value) && $value instanceof IntAttr) {
      $this->attr_value = $value->get();
      return true;       

    // Check if we are deling with an integer
    } elseif(is_int($value) && IntegerValidator::is_valid($value)) {
      $this->attr_value = intval($value);
      return true;  

    // We were unable to set an integer from a provided arguments
    } else {
      throw new Error\InvalidAttributeError("Invalid argument of type " . gettype($value) . " passed.");
    }
  }

  /**
   * Method gets the attribute value as stored inside protced $id attribute
   * @return int|null representing value of the protected $id attribute
   */
  public function get() {
    return $this->attr_value;
  }
  
  public function to_db_format() {
    return $this->to_int();
  }

  public function to_int() {
    return (int) $this->attr_value;
  }

}