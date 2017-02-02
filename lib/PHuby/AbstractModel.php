<?php
/**
 * AbstractModel sits as a base in every model
 * 
 * @author Michal Nagielski <michal.nagielski@gmail.com>
 * @package PHuby
 */

namespace PHuby;

use PHuby\Helpers\Utils\ObjectUtils;
use PHuby\Logger;
use PHuby\Helpers\Utils\StringUtils;

abstract class AbstractModel implements BaseModelInterface {

  protected $arr_default_raw_data_options = [
    "exclude" => ["id"],
    "include" => "all",
    "include_childs" => false
  ];

  public function __construct() {
    ObjectUtils::create_attributes($this);
  }

  public function populate_attributes($attributes) {
    return ObjectUtils::populate_attributes($this, $attributes);
  }

  public function is_attribute_allowed($attribute) {
    return ObjectUtils::is_attribute_allowed($this, $attribute);
  }

  /**
   * @todo improve this method
   */
  public function has_attribute($attribute) {
    return ObjectUtils::is_attribute_allowed($this, $attribute);
  }

  public function is_attribute_a_child_class($attribute) {
    return ObjectUtils::is_attribute_a_child_class($this, $attribute);
  }

  /**
   * Method returns an array with raw data that is configured on the model
   * 
   * @todo - implement options string if required
   */
  public function get_raw_data() {
    
    $arr_raw_data = [];
    $arr_options = $this->arr_default_raw_data_options;

    Logger::debug("Getting raw data from ".get_class($this)." with following options: " . json_encode($arr_options));

    foreach($this::ATTRIBUTE_MAP as $str_attr_name => $arr_attr_options) {
      // Check if the attribute is excluded
      if(!in_array($str_attr_name, $arr_options["exclude"])) {
        // Not excluded. Check if it is a child class
        if(ObjectUtils::is_attribute_a_child_class($this, $str_attr_name)) {
          // Attribute is a child class. Check if it is supposed to be included
          if($arr_options["include_childs"]) {
            // Include child class
          } else {
            // Do not include child class
          }
        } else {
          // Not a child class. Add the attribute
          $arr_raw_data[$str_attr_name] = $this->$str_attr_name->to_db_format();
        }
      }
    }

    return empty($arr_raw_data) ? null : $arr_raw_data;
    
  }
}