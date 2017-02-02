<?php
/**
 * Class represents string attribute
 * 
 * @author Michal Nagielski <michal.nagielski@gmail.com>
 * @package PHuby\Attribute
 */

namespace PHuby\Attribute;

use PHuby\AbstractAttribute;
use PHuby\Helpers\Validator\StringValidator;
use PHuby\Error\InvalidAttributeError;
use PHuby\Logger;

class StringAttr extends AbstractAttribute implements AttributeInterface {

  /** @var mixed[] $attr_options Array containing default options */
  protected $attr_options = [
    "allow_null" => true,
    "allow_spaces" => true
  ];

  /**
   * Method sets the attribute value
   *
   * @param string|null|\PHuby\Attribute\StringAttr $value containing value to be set
   * @return boolean true if attribute is set
   * @throws \PHuby\Error\InvalidAttributeError is invalid value is passed
   */
  public function set($value) {
    if(is_string($value)) {
      Logger::debug("Starting validation for $value with following options: " . json_encode($this->attr_options));
      if(StringValidator::is_valid($value, $this->attr_options)) {
        $this->attr_value = $value;
        return true;        
      } else {
        throw new InvalidAttributeError("String $value has not passed validation");
      }
    } elseif(is_object($value) && $value instanceof StringAttr) {
      $this->attr_value = $value->get();
      return true;
    } elseif(is_null($value) && $this->get_option('allow_null')) { 
      $this->attr_value = $value;
      return true;
    } else {
      throw new InvalidAttributeError("This attribute accepts strings only. Got " . gettype($value));
    }
  }

  /**
   * Method returns the attribute value
   *
   * @return mixed attribute value
   */
  public function get() {
    return $this->attr_value;
  }

  /**
   * Method returns the attribute value that is db friendly
   *
   * @return mixed attribute value as string or null
   */
  public function to_db_format() {
    if(is_string($this->attr_value)) {
      return (string) $this->attr_value;
    } elseif(is_null($this->attr_value)) {
      return $this->attr_value;
    }
  }

  /**
   * Method generates a random string, that can be used for naming a file
   * 
   * array['length']    Containing int length of the string
   * 
   * @param array $arr_params containing options for the method
   * @return string with a random value
   */
  public static function generate_random_string(Array $arr_params = []) {
    $length = 10;
    if(!empty($arr_params)) {
      if(isset($arr_params['length'])) {
        $length = $arr_params['length'];
      }
    }

    return substr(md5(uniqid(rand(),true)), 0, $length);
  }
  
}