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
              'allow_spaces'  => false,
              'length'        => 32
            ];

  public function set($string) {
    if(is_null($string)) {
      $this->attr_value = $string;
      return true;     
    } elseif(StringValidator::is_valid($string, [
        "allow_spaces" => $this->attr_options["allow_spaces"],
        "length" => ["exact" => $this->attr_options["length"]]
      ])) {
      $this->attr_value = $string;
      return true;      
    } else {
      throw new Error\InvalidAttributeError(StringValidator::get_first_validation_error()->getMessage());
    }
  }

  public function generate(Array $params = null) {
    if($params) {
      if(array_key_exists('length', $params)) {
        $length = $params['length'];
      } else {
        $length = $this->attr_options['length'];
      }
    }
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