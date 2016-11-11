<?php

namespace Lumen\Attribute;

use Lumen\AbstractAttribute;
use Lumen\Error;
use Lumen\Helpers\Validator\EmailValidator;

class EmailAttr extends AbstractAttribute implements AttributeInterface {

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

  public function get() {
    return $this->attr_value;
  }

  public function to_db_format() {
    return (string) $this->attr_value;
  }
}