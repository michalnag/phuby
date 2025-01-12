<?php
/**
 * UUID attribute
 * 
 * @author Michal Nagielski <michal.nagielski@gmail.com>
 * @package PHuby\Attribute
 * @todo Integrate 3rd party UUID generator to handle this type of attribute
 */

namespace PHuby\Attribute;

use PHuby\AbstractAttribute;
use PHuby\Helpers\Validator\StringValidator;
use PHuby\Error;

class UUIDAttr extends AbstractAttribute implements AttributeInterface {

  protected $attr_options = [
              'allow_spaces'  => false,
              'length'        => 32
            ];

  public function set($string) {
    if(StringValidator::is_valid($string, [
        "allow_spaces" => $this->attr_options["allow_spaces"],
        "length" => ["exact" => $this->attr_options["length"]]
      ])) {
      $this->attr_value = $string;
      return true;      
    } else {
      throw new Error\InvalidAttributeError(StringValidator::get_first_validation_error()->getMessage());
    }
  }

  public function generate(array $params = null) {
    if($params) {
      if(array_key_exists('length', $params)) {
        $length = $params['length'];
      } else {
        $length = $this->attr_options['length'];
      }
    }
    $this->attr_value = substr(md5(uniqid(rand(),true)), 0, $length);
    return $this;
  }

  public function get() {
    return $this->attr_value;
  }

  public function to_db_format() {
    return (string) $this->attr_value;
  }

  public function get_length() {
    return $this->attr_options['length'];
  }
}