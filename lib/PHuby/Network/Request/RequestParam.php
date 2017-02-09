<?php
/**
 * RequestParam
 * 
 * @author Michal Nagielski <michal.nagielski@gmail.com>
 * @package PHuby\Network\Request
 */

namespace PHuby\Network\Request;
use PHuby\Helpers\Utils\ObjectUtils;
use PHuby\Logger;
use PHuby\AbstractModel;

class RequestParam extends AbstractModel {  

  private
    $name,
    $source,
    $type;

  const ATTRIBUTE_MAP = [
    "name" => [
      "class" => "\PHuby\Attribute\StringAttr"
    ],
    "source" => [
      "class" => "\PHuby\Attribute\StringAttr" 
    ],
    "type" => [
      "class" => "\PHuby\Attribute\StringAttr"      
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

  public function __toString() {
    return $this->value;
  }

  /**
   * Method retrieves the value from the superglobal
   */
  public function retrive_value() {
    switch(strtoupper($this->source->__toString())) {
      case "POST":
        $this->value = isset($_POST[$this->name->__toString()]) ? $_POST[$this->name->__toString()] : null;
        break;
      case "GET":
        $this->value = isset($_GET[$this->name->__toString()]) ? $_GET[$this->name->__toString()] : null;
        break;
      case "FILES":
        $this->value = isset($_FILES[$this->name->__toString()]) ? $_FILES[$this->name->__toString()] : null;
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
    return $this->value;
  }

}