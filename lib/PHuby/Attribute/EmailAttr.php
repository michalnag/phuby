<?php
/**
 * @author Michal Nagielski <michal.nagielski@gmail.com>
 * @package PHuby
 * 
 * Email attribute
 */

namespace PHuby\Attribute;

use PHuby\AbstractAttribute;
use PHuby\Error;
use PHuby\Helpers\Validator\EmailValidator;

class EmailAttr extends AbstractAttribute implements AttributeInterface {

  /**
   * Sets the email value as the attribute value
   * 
   * @param string $value containing email address
   * @return boolean true if attribute value is set correctly
   * @throws \PHuby\Error\InvalidAttributeError if invalid value is passed
   */
  public function set($value) {
    if(is_object($value) && $value instanceof EmailAttr) {
      $this->attr_value = $value->get();
      return true;
    } elseif(is_string($value)) {  
      if(EmailValidator::is_valid($value)) {
        $this->attr_value = $value;
        return true;
      }
    }

    // We have failed to set the passed value as an attribute value
    throw new Error\InvalidAttributeError("Invalid argument of type ". gettype($value) ." passed to be set as attribute");

  }

  /**
   * Gets the attribute value
   * 
   * @return string|null representing value of the attribute
   */
  public function get() {
    return $this->attr_value;
  }

  /**
   * Returns DB friendly format
   * 
   * @return string|null representing attribute value in db friendly format
   */
  public function to_db_format() {
    if(is_null($this->attr_value)) {
      return $this->attr_value;
    } else {
      return (string) $this->attr_value;      
    }
  }
}