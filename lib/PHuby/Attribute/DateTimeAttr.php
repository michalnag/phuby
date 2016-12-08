<?php

namespace PHuby\Attribute;

use PHuby\AbstractAttribute;
use PHuby\Attribute\AttributeInterface;
use PHuby\Error\InvalidAttributeError;
use PHuby\Helpers\Validator\DateTimeValidator;

class DateTimeAttr extends AbstractAttribute implements AttributeInterface {



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

    // UNIX timestamp      
    } elseif(is_int($value)) {
      // TODO
    } else {
      throw new InvalidAttributeError("Invalid value passed to the DateTimeAttr. Got " . gettype($value));
    }
  } 

  public function get() {
    return $this->attr_value;
  }

  public function to_db_format() {
    if($this->attr_value) {
      return $this->attr_value->format("Y-m-d H:i:s");
    } else {
      return null;
    }
  }

}