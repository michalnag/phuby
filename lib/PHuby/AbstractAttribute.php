<?php

namespace PHuby;

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

  /**
   * Method retrieves the option from the array set on the child attribute
   * 
   * @param String $str_option expects the config location colon separated
   *    e.g. length:min
   * @return Mixed option set on the child attribute, null if nothing is found
   */
  protected function get_option($str_option) {
    // First, let's check is value passed is a string
    if(is_string($str_options)) {
      // Now create parts from the string
      $arr_option_parts = explode(':', $str_option);
      
      // Get requested option
      $arr_options = $this->attr_options;
      foreach($arr_option_parts as $option) {
        if(array_key_exists($option, $arr_options)) {
          // Check if the option contains sub-options
          $return_option = $arr_options[$option];
          $arr_options = $arr_options[$option];
        } else {
          Logger::debug("Option $str_option is not set on " . get_class($this));
          return null;
        }
      }

      return $return_option;

    } else {
      Logger::debug("Option passed must be a string. Got " . gettype($str_option));
      return null;
    }
  }

}