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
use PHuby\Helpers\Validator\FloatValidator;
use PHuby\Error;

class FloatAttr extends AbstractAttribute implements AttributeInterface {

  /** @var mixed[] $attr_options representing default attribute options */
  protected $attr_options = [
    "allow_negative" => false,
    "allow_zero" => true,
    "allow_positive" => true,
    "allow_null" => true,
    "conversion_factor" => 100
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
    if(is_object($value) && $value instanceof FloatAttr) {
      $this->attr_value = $value->to_int();
      return true;

    // Check if we are deling with an integer
    } elseif(is_int($value) && IntegerValidator::is_valid($value)) {
      $this->attr_value = $value*$this->get_option("conversion_factor");
      return true;

    // We were unable to set an integer from a provided arguments
    } elseif (is_string($value) && is_numeric($value) || is_float($value)) {
      
      $this->attr_value = floatval($value)*$this->get_option("conversion_factor");
      return true;
 
    } elseif(is_null($value) && $this->get_option("allow_null")) {
      $this->attr_value = $value;
      return true; 
    }

    // No condition have been met
    throw new Error\InvalidAttributeError("Invalid argument of type " . gettype($value) . " passed.");
    
  }

  /**
   * Method gets the attribute value as stored inside protected $id attribute
   * 
   * @return float|null representing value of the protected $id attribute
   */
  public function get() {
    if (!is_null($this->attr_value)) {
      return (float) $this->attr_value / $this->get_option('conversion_factor');
    } else {
      return $this->attr_value;      
    }
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