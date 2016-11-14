<?php

namespace Network\Request;
use Attribute\AttributePopulator;

class RequestParam extends AttributePopulator {  

  const ATTRIBUTE_MAP = [
    "name" => [
      "type" => "attribute",
      "attribute_class" => "StringAttr"
    ],
    "source" => [
      "type" => "attribute",
      "attribute_class" => "StringAttr" 
    ],
    "type" => [
      "type" => "attribute",
      "attribute_class" => "StringAttr"      
    ]
  ];

  private 
    $value,       // Native value passed to the class
    $options = [
      "required" => false,
      "custom_validator" => null
    ];

  /** @todo */
  public function set_options(Array $options) {
    return array_merge($this->options, $options);
  }

  public function get_options() {
    return $this->options;
  }

  /**
   * Method retrieves the value from the superglobal
   */
  public function retrive_value() {
    $superglobal = "_" . strtoupper($this->source->__toString());
    if(isset($$superglobal[$this->name->__toString()])) {
      $this->value = $$superglobal[$this->name->__toString()];
    } else {
      $this->value = null;
    }
  }

  public function is_value_set() {
    return $this->value ? true : false;
  }

  public function is_required() {
    return $this->get_options()["required"];
  }

}