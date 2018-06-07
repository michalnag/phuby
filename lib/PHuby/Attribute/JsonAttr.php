<?php
/**
 * Integer attribute
 * 
 * @author Michal Nagielski <michal.nagielski@gmail.com>
 * @package PHuby\Attribute
 */

namespace PHuby\Attribute;

use PHuby\AbstractAttribute;
use PHuby\Error;
use PHuby\Helpers\Utils;

class JsonAttr extends AbstractAttribute implements AttributeInterface {

  /**
   * Sets the value of the attribute
   * 
   * @param string|Array representing json encoded string or array
   * @throws PHuby\Error\InvalidArgumentError
   */
  public function set($value) {
    // Check if the value passed is an array
    if (is_array($value)) {
      $this->attr_value = $value;
    } elseif (is_string($value)) {
      $this->attr_value = Utils\JSONUtils::validate($value, true);
    } elseif (is_object($value) && $value instanceof JsonAttr) {
      $this->attr_value = $value->get();
    } else {
      throw new Error\InvalidArgumentError(__METHOD__ . ": Invalid value passed");
    }
  }


  public function get($bol_as_object = false) {
    return $bol_as_object
      ? json_decode(json_encode($this->attr_value))
      : $this->attr_value;
  }

  public function to_db_format() {
    return $this->__toString();
  }

  public function __toString() {
    if ($this->attr_value) {
      return json_encode($this->attr_value);
    } else {
      return null;
    }
  }

  public function get_by_key($str_key) {
    return Utils\ArrayUtils::get_data($str_key, $this->attr_value);
  }

  public function add_data($str_key, $data) {
    return Utils\ArrayUtils::add_data($str_key, $this->attr_value, $data);
  }

  public function set_data($str_key, $data) {
    return Utils\ArrayUtils::add_data($str_key, $this->attr_value, $data);
  }

  public function remove_data($str_key) {
    return Utils\ArrayUtils::remove_data($str_key, $this->attr_value);
  }



}