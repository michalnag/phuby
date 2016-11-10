<?php

namespace Lumen\Attribute;

use Lumen\AbstractAttribute;

class EmailAttr extends AbstractAttribute implements AttributeInterface {

  public function set($string) {
    $this->attr_value = $string;
  }

  public function get() {
    return $this->attr_value;
  }

  public function to_db_format() {
    return (string) $this->attr_value;
  }
}