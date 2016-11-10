<?php

namespace Lumen\Attribute;

use Lumen\AbstractAttribute;
use Lumen\Attribute\AttributeInterface;
use Lumen\Error\InvalidAttributeError;

class BooleanAttr extends AbstractAttribute implements AttributeInterface {

  /**
   * Set method for boolean attribute
   *
   * @param bool|int|BooleanAttr representing value to be set
   * @return true if the value gets set
   * @throws Lumen\Error\InvalidAttributeError if invalid value is passed
   */
  public function set($value) {
    // Check if the value passed is an integer
    if(is_int($value)) {
      // Check if the integer is representing true or false
      if($value == 1) {        
        $this->attr_value = true;
      } elseif($value == 0) {
        $this->attr_value = false;
      } else {
        // Invalid integer value passed
        throw new InvalidAttributeError(get_class($this) . "::set must be passed value 1 or 0 if integer");
      }

    // Check if the value passed is a boolean   
    } elseif(is_bool($value)) {
      $this->attr_value = $value;

    // Check if the value passed is an instance of the BooleanAttr
    } elseif(is_object($value) && $value instanceof BooleanAttr) {
      $this->attr_value = $value->get();

    // Not supported value passed to the setter
    } else {
      throw new InvalidAttributeError(get_class($this) . "::set has been passed an argument of type " . gettype($value));
    }

    // If we are her, it means that the value has been set
    return true;
  }

  /**
   * Converts boolean attribute to the integer
   * 
   * @return int representing true or false
   */
  public function to_int() {
    return $this->attr_value ? 1 : 0;
  }

  /**
   * Retrieves the boolean value
   *
   * @return boolean representing attribute value
   */
  public function get() {
    return $this->attr_value;
  }

  /**
   * Returns an integer representing boolean expression
   *
   * @return int representing true or false
   */
  public function to_db_format() {
    return $this->to_int();
  }
}