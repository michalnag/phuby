<?php

namespace Lumen;

abstract class AbstractAttribute {

  protected $attr_value;

  protected $attr_options = array();

  public function __construct(...$args) {
    if(count($args) > 0) {
      $this->set($args[0]);
    }
  }

  public function __toString() {
    return (string) $this->to_db_format();
  }

  public function set_attribute_options(Array $options) {
    $this->attr_options = array_merge($this->attr_options, $options);
  }

}