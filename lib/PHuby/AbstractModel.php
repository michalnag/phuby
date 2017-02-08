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

abstract class AbstractModel implements BaseModelInterface {

  protected $arr_default_raw_data_options = [
    "exclude" => ["id"],
    "include" => "all",
    "include_childs" => false
  ];

  public function &__get($str_attr_name) {
    if(ObjectUtils::is_attribute_allowed($this, $str_attr_name)) {
      return $this->$str_attr_name;
    } else {
      throw new Error\InvalidAttributeError("Non allowed attribute $str_attr_name cannot be retrieved on " . get_class($this));
    }
  }

  public function __construct() {
    ObjectUtils::create_attributes($this);
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

  /**
   * Method is designed to retrieve attributes set on the object
   */
  public function &get_attr($str_attr_name) {
    if(ObjectUtils::is_attribute_allowed($this, $str_attr_name)) {
      return $this->$str_attr_name;
    } else {
      throw new Error\InvalidAttributeError("Non allowed attribute $str_attr_name cannot be retrieved on " . get_class($this));
    }
  }

  /**
   * Method is designed to set the attribute on the object.
   * This method should handle all possible cases of setting up an attribute on the model
   * based on the ATTRIBUTE_MAP set inside each object.
   * 
   * @param string $str_attr_name representing name of the attribute
   * @param mixed $value representing value to be set on the parameter
   * @return boolean true if attribute is set correctly
   * @throws \PHuby\Error\InvalidAttributeError when invalid argument value is passed
   */
  public function set_attr($str_attr_name, $value) {

    // Check if the attribute is allowed
    if (ObjectUtils::is_attribute_allowed($this, $str_attr_name)) {

      // Get attribute data
      $arr_attr_data = $this::ATTRIBUTE_MAP[$str_attr_name];
      Logger::debug();

      // Check if this is a standard attribute
      if (ObjectUtils::is_standard_attribute($this, $str_attr_name)) {

        // Standard attribute. Get the class name of the attribute
        $str_attr_class = $arr_attr_data['class'];

        // Check if the value of the attribute is an object
        if (is_object($value)) {
          // And if so, check if this is the right instance
          if ($value instanceof $str_attr_class) {
            // All good, set the instance as an attribute value
            $this->$str_attr_name = $value;
          } else {
            // Instance of the object not supported
            throw new Error\InvalidAttributeError("Attribute $str_attr_name must be passed an instance of $str_attr_class, but got " . get_class($value));
          }

        } elseif(ObjectUtils::is_attribute_a_child_class($this, $str_attr_name)) {
          // Get the child class name and instantiate it
          $str_child_class_name = $arr_attr_data['child_class'];
          $obj_child_class = new $str_child_class_name;

          if(ObjectUtils::is_collection_class($obj_child_class)) {
            // For a collection class we can have prepopulated collection or a raw data
            if($value instanceof $str_child_class_name) {
              // Simply assign it to the colleciton class
              $this->$str_attr_name = $value;
            } else {
              // Attempt to populate collection
              $obj_child_class->populate_collection($value);
            }
          } else {
            // This is standard child class
            if($value instanceof $str_child_class_name) {
              // Simply assign it
              $this->str_attr_name = $value;
            } else {
              // Populate data to the attribute
              $obj_child_class->set($value);
              $this->str_attr_name = $obj_child_class;
            }
          }
        }        
      }

    } else {
      // Attribute is not configured
      throw new Error\InvalidAttributeError("$str_attr_name is not allowed to be set on " . get_class($this));
    }

    // Finally return true
    return true;

  }

  /**
   * Method creates empty attributes
   */
  public function create_attributes() {

  }

  /**
   * This method populate attributes from raw data and converts it to attribute classes
   */
  public function populate_attributes(Array $arr_attributes) {
    foreach($arr_attributes as $key => $value) {
      $this->set_attr($key, $value);
    }
    return true;
  }


}