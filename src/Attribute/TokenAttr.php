<?php
/**
 * Token attribute
 * 
 * @author Michal Nagielski <michal.nagielski@gmail.com>
 * @package PHuby\Attribute
 */

namespace PHuby\Attribute;

use PHuby\AbstractAttribute;
use PHuby\Error;
use PHuby\Helpers\Validator\StringValidator;

class TokenAttr extends AbstractAttribute implements AttributeInterface {

  protected $attr_options = [
    "validation" => [
        'allow_spaces'  => false,
        'length'        => 32,
        'uppercase'     => false,
        'allow_null'    => true
      ]
    ];

  public function set($value) {
    if(is_object($value) && $value instanceof $this) {
      $this->attr_value = $this->get_option('validation:uppercase') ? strtoupper($value) : $value;
      return true;
    } elseif(is_null($value) || empty($value)) {
      if ($this->get_option('validation:allow_null')) {
        $this->attr_value = null;
        return true;
      }
      throw new Error\InvalidAttributeError("Null value passed but not allowed");
    } elseif(StringValidator::is_valid($value, [
        "allow_spaces" => $this->get_option("validation:allow_spaces"),
        "length" => ["exact" => $this->get_option("validation:length")]
      ])) {
      $this->attr_value = $this->get_option('validation:uppercase') ? strtoupper($value) : $value;
      return true;      
    } else {
      throw new Error\InvalidAttributeError(StringValidator::get_first_validation_error()->getMessage());
    }
  }

  public function generate(array $params = null) {
    if($params && array_key_exists('length', $params)) {
        $length = $params['length'];
    } else {
      $length = $this->get_option('validation:length');
    }
    $this->set(substr(md5(uniqid(rand(),true)), 0, $length));
    return $this->attr_value;
  }

  public function get() {
    return $this->attr_value;
  }

  public function to_db_format() {
    return (string) $this->attr_value;
  }

  public function get_exact_length() {
    return $this->get_option('validation:length:exact');
  }
}