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
use PHuby\Error;

abstract class AbstractModel extends AbstractCore {

  const
    CLASS_TYPE = self::CLASS_TYPE_MODEL;

  protected $arr_default_raw_data_options = [
    "exclude" => ["id"],
    "include" => "all",
    "include_childs" => false
  ];

  /**
   * Method initiates attributes
   */
  public function __construct() {
    $this->initiate_attributes();
  }

  /**
   * Method returns an array with raw data that is configured on the model
   * @return 
   * @todo - Support collection model
   */
  public function get_raw_data() {
    
    $arr_raw_data = [];
    $arr_options = $this->arr_default_raw_data_options;

    Logger::debug("Getting raw data from ".get_class($this)." with following options: " . json_encode($arr_options));

    foreach($this::ATTRIBUTE_MAP as $str_attr_name => $arr_attr_options) {
      // Check if the attribute is excluded
      if(!in_array($str_attr_name, $arr_options["exclude"])) {
        
        // Start checking whether this is a standard attribute or not
        if($this->is_attribute_standard($str_attr_name)) {
          $arr_raw_data[$str_attr_name] = $this->$str_attr_name->to_db_format();
        }

        // Check if this is a child class
        if($this->is_attribute_child_class($str_attr_name)) {
          // Attribute is a child class. Check if it is supposed to be included
          if($arr_options["include_childs"]) {
            // Include child class
          } else {
            // Do not include child class
          }
        } 

        // Check if this is a collection class
        if($this->is_attribute_collection_class($str_attr_name)) {
          // TODO
        }

      }
    }

    return empty($arr_raw_data) ? null : $arr_raw_data;
    
  }

}