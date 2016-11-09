<?php

namespace Attribute;

use AbstractAttribute;

class TextAttr extends AbstractAttribute implements AttributeInterface {

  public function set($text) {
    $this->attr_value = $text;
  }

  public function get() {
    return $this->attr_value;
  }

  public function to_db_format() {
    return (string) $this->attr_value;
  }
}