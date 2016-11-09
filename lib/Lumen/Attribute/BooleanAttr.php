<?php

namespace Lumen\Attribute;

use Lumen\AbstractAttribute;
use Lumen\Attribute\AttributeInterface;
use Lumen\Error\InvalidAttributeError;

class BooleanAttr extends AbstractAttribute implements AttributeInterface {

  public function set($value) {
    if(is_int($value)) {
      if($value == 1) {
        $this->attr_value = true;
      } elseif($value == 0) {
        $this->attr_value = false;
      } else {
        throw new InvalidAttributeError(get_class($this) . "::set must be passed value 1 or 0 if integer");
      }
    } elseif(is_bool($value)) {
      $this->attr_value = $value;
    } elseif(is_object($value) && $value instanceof BooleanAttr) {
      $this->attr_value = $value->get();
    } else {
      throw new InvalidAttributeError(get_class($this) . "::set has been passed an argument of type " . gettype($value));
    }
  }

  public function to_int() {
    return $this->attr_value ? 1 : 0;
  }

  public function get() {
    return $this->attr_value;
  }

  public function to_db_format() {
    return (int) $this->attr_value;
  }
}