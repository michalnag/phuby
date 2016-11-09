<?php

namespace Attribute;

use AbstractAttribute;
use DateTime;
use Helpers\Validator\DateTimeValidator;
use Error\InvalidAttributeError;

class DateTimeAttr extends AbstractAttribute implements AttributeInterface {

  public function set($string) {
    if(DateTimeValidator::is_valid($string)) {
      $this->attr_value = new \DateTime($string);
      return true;
    } else {
      throw new InvalidAttributeError(DateTimeValidator::get_first_validation_error()->getMessage());      
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