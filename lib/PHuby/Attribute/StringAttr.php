<?php

namespace PHuby\Attribute;

use PHuby\AbstractAttribute;
use PHuby\Error\InvalidAttributeError;

class StringAttr extends AbstractAttribute implements AttributeInterface {

  public function set($value) {
    if(is_string($value)) {
      $this->attr_value = $value;
      return true;
    } elseif(is_object($value) && $value instanceof StringAttr) {
      $this->attr_value = $value->get();
      return true;
    } else {
      throw new InvalidAttributeError("This attribute accepts strings only. Got " . gettype($value));
    }
  }

  public function get() {
    return $this->attr_value;
  }

  public function to_db_format() {
    return (string) $this->attr_value;
  }
}