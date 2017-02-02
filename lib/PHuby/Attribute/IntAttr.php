<?php
/**
 * Integer attribute
 * 
 * @author Michal Nagielski <michal.nagielski@gmail.com>
 * @package PHuby\Attribute
 */

namespace PHuby\Attribute;

use PHuby\AbstractAttribute;
use PHuby\Helpers\Validator\IntegerValidator;
use PHuby\Error;

class IntAttr extends AbstractAttribute implements AttributeInterface {

  /** @var mixed[] $attr_options representing default attribute options */
  protected $attr_options = [
    "allow_negative" => true,
    "allow_zero" => true,
    "allow_positive" => true,
    "allow_null" => true
  ];

  /**
   * Method sets the attribute value
   * 
   * @param integer|null|\PHuby\Attribute\IntAttr $value representing desired attribute value
   * @return boolean true when succesfully set
   * @throws \PHuby\Error\InvalidAttributeError when invalid value type is passed
   */
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
    } elseif(is_string($value) && is_numeric($value)) {
      $this->attr_value = intval($value);
      return true;
 
    } elseif(is_null($value) && $this->get_option("allow_null")) {
      $this->attr_value = $value;
      return true; 
    }

    // No condition have been met
    throw new Error\InvalidAttributeError("Invalid argument of type " . gettype($value) . " passed.");
    
  }

  /**
   * Method gets the attribute value as stored inside protced $id attribute
   * 
   * @return int|null representing value of the protected $id attribute
   */
  public function get() {
    return $this->attr_value;
  }
  
  /**
   * Method returns db firendly format attribute value
   * 
   * @return null|integer representing attribute value
   */
  public function to_db_format() {
    return $this->to_int();
  }

  /**
   * Method attempts to return integer representing attribute value
   * 
   * @return null|integer representing attribute value
   */ 
  public function to_int() {
    if(is_null($this->attr_value)) {
      return $this->attr_value;
    } else {
      return (int) $this->attr_value;      
    }
  }

}