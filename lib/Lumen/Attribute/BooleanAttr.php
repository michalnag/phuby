<?php

namespace Attribute;

use AbstractAttribute;

class BooleanAttr extends AbstractAttribute implements AttributeInterface {

  public function set($boolean) {
    $this->attr_value = $boolean;
  }

  public function get() {
    return $this->attr_value;
  }

  public function to_db_format() {
    return (int) $this->attr_value;
  }
}