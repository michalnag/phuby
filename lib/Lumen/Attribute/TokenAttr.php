<?php

namespace Attribute;

use AbstractAttribute;
use Error;
use Helpers\Validator\StringValidator;

class TokenAttr extends AbstractAttribute implements AttributeInterface {

  public function __construct($value = null) {
    parent::__construct($value);
  }
  
  protected $attr_options = [
              'allow_spaces'  => false,
              'length'        => ['exact' => 32]
            ];

  public function set($string) {
    if(StringValidator::is_valid($string, $this->attr_options)) {
      $this->attr_value = $string;
      return true;      
    } else {
      throw new Error\InvalidAttributeError(StringValidator::get_first_validation_error()->getMessage());
    }
  }

  public function generate_value($length = null) {
    $length = $length ? $length : $this->get_exact_length();
    $this->attr_value = substr(md5(uniqid(rand(),true)), 0, $length);
    return $this->attr_value;
  }

  public function get() {
    return $this->attr_value;
  }

  public function to_db_format() {
    return (string) $this->attr_value;
  }

  public function get_exact_length() {
    return $this->attr_options['length']['exact'];
  }
}