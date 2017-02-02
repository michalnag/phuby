<?php
/**
 * DateTime attribute
 * 
 * @author Michal Nagielski <michal.nagielski@gmail.com>
 * @package PHuby\Attribute
 */

namespace PHuby\Attribute;

use PHuby\AbstractAttribute;
use PHuby\Attribute\AttributeInterface;
use PHuby\Error\InvalidAttributeError;
use PHuby\Helpers\Validator\DateTimeValidator;

class DateTimeAttr extends AbstractAttribute implements AttributeInterface {

  /** @var mixed[] $attr_options Array containing default options for attribute */
  protected $attr_options = [
    "allow_null" => true
  ];

  /**
   * Method sets the value on the instance
   * 
   * @param $value string|null|integer|PHuby\Attribute\DateTimeAttr representing desired datetime
   * @return boolean true if succesfully set
   * @throws \PHuby\Error\InvalidArgumentError if invalid argument is passed
   * @todo - UNIX timestamp case
   */
  public function set($value) {
    if(is_object($value)) {
      // We allow instances of the DateTime and DateTimeAttr
      if($value instanceof \DateTime) {
        $this->attr_value = $value;
      } elseif($value instanceof DateTimeAttr) {
        $this->attr_value = $value->get();
      } else {
        throw new InvalidAttributeError("Invalid object passed to the attribute. Got " . get_class($value));
      }
    } elseif(is_string($value)) {
      if(DateTimeValidator::is_valid($value)) {
        $this->attr_value = new \DateTime($value);
        return true;
      } else {
        throw new InvalidAttributeError(DateTimeValidator::get_first_validation_error()->getMessage());      
      }

    } elseif(is_int($value)) {
      // @todo - UNIX timestamp
    } elseif(is_null($value)) {
      // Check if we allow null to be set
      if($this->get_option("allow_null")) {
        $this->attr_value = $value;
        return true;
      } else {
        throw new InvalidAttributeError("Null value not allowed to be set."); 
      }
    } else {
      throw new InvalidAttributeError("Invalid value passed to the DateTimeAttr. Got " . gettype($value));
    }
  } 

  /**
   * Retrieves the attribute value set on the object
   * 
   * @return object \DateTime that is used as attribute value
   */ 
  public function get() {
    return $this->attr_value;
  }

  /**
   * Returns db formated value
   * 
   * @return null|string in DB friendly format Y-m-d H:i:s
   */
  public function to_db_format() {
    if($this->attr_value instanceof \DateTime) {
      return $this->attr_value->format("Y-m-d H:i:s");
    } else {
      return null;
    }
  }

}