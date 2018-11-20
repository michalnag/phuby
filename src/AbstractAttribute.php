<?php
/**
 * AbstractAttribute sits as a base in every attribute
 * 
 * @author Michal Nagielski <michal.nagielski@gmail.com>
 * @package PHuby
 */

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

  public function set_attribute_options(array $arr_options) {
    // We need to merge this separately for validation options
    // IMPORTANT: do not use array_merge_recursive, as it will add multiple values
    // We want to merge only two levels down
    foreach ($this->attr_options as $str_name => $mix_value) {
      // Check if it has been passed or should it fall back to defaults
      if (array_key_exists($str_name, $arr_options)) {
        if (is_array($mix_value)) {
          // This is an array option. Merge with defaults
          if (array_key_exists($str_name, $arr_options)) {
            $this->attr_options[$str_name] = array_merge($this->attr_options[$str_name], $arr_options[$str_name]);
          }
        } else {
          // Not an array. Simply override the value
          $this->attr_options[$str_name] = $arr_options[$str_name];
        }
      }
    }
  }

  /**
   * Method retrieves the option from the array set on the child attribute
   * 
   * @param String $str_option expects the config location colon separated
   *    e.g. length:min
   * @return Mixed option set on the child attribute, null if nothing is found
   */
  public function get_option($str_option) {
    // First, let's check is value passed is a string
    if(is_string($str_option)) {
      // Now create parts from the string
      $arr_option_parts = explode(':', $str_option);
      // Get requested option
      $return_option = $this->attr_options;
      foreach($arr_option_parts as $option) {

        if(array_key_exists($option, $return_option)) {
          // Check if the option contains sub-options
          $return_option = $return_option[$option];
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