<?php

namespace PHuby\Network\Request;
use PHuby\Helpers\Utils\ObjectUtils;
use PHuby\Logger;

class RequestParam {  

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

  public function populate_attributes(Array $attributes) {
    return ObjectUtils::populate_attributes($this, $attributes);
  }

  public function __toString() {
    return $this->value;
  }

  /**
   * Method retrieves the value from the superglobal
   */
  public function retrive_value() {
    switch($this->source->__toString()) {
      case "POST":
        $this->value = isset($_POST[$this->name->__toString()]) ? $_POST[$this->name->__toString()] : null;
        break;
      case "GET":
        $this->value = isset($_GET[$this->name->__toString()]) ? $_GET[$this->name->__toString()] : null;
        break;
    }
  }

  public function is_value_set() {
    return $this->value ? true : false;
  }

  public function is_required() {
    return $this->get_options()["required"];
  }

  public function get_name() {
    return $this->name->__toString();
  }

  public function get_value() {
    Logger::debug("Returning value $this->value");
    return $this->value;
  }

}