<?php
/**
 * ObjectUtils
 * 
 * @author Michal Nagielski <michal.nagielski@gmail.com>
 * @package PHuby\Helpers\Utils
 */

namespace PHuby\Helpers\Utils;

use PHuby\Helpers\AbstractUtils;
use PHuby\Error;
use PHuby\Logger;
use PHuby\Config;

class ObjectUtils extends AbstractUtils {
 
  /**
  * Method is responsible for assigning attributes to the class based on the configuration
  * inside the model that calls it. Attribute mapping is defined in const ATTRIBUTE_MAP
  *
  * @param mixed[] Array containind data to be assigned to attributes
  * @return TODO
  */
  public static function set_attributes(&$object, $data) {
    Logger::debug(get_class($object) . " will be built from the array.");
    // Loop through the array and assign correct attributes
    foreach($data as $key => $value) {
      if(!self::is_collection_class($object) && self::is_attribute_allowed($object, $key)) {

        // Check if the passed attribute is a child class or standard attribute
        if(self::is_attribute_a_child_class($object, $key)) {

          // Get class name and build the child from the array
          $str_child_class_name = $object::ATTRIBUTE_MAP[$key]['child_class'];

          // We need to check if the attribute is set already
          if(!$object->$key instanceof $str_child_class_name) {
            // Attribute not set yet. Create new instance
            Logger::debug("Setting an instance of $str_child_class_name as $key attribute on ".get_class($object)." with values ".json_encode($value));
            $object->set_attr($key, new $str_child_class_name());         
          }

          // Finally populate attributes
          $object->$key->set_attributes($value);

        } else {
          // Attribute is a standard attribute class
          // Get a class name that is configured for this attribute
          $attribute_class = $object::ATTRIBUTE_MAP[$key]["class"];        
          Logger::debug("Attribute $key is configured on the class " . get_class($object) . " as $attribute_class");

          // Create an instance of the attribute
          $object->set_attr($key, new $attribute_class());

          // Check if there are custom options set on the object
          if(array_key_exists("options", $object::ATTRIBUTE_MAP[$key])) {
            $object->$key->set_attribute_options($object::ATTRIBUTE_MAP[$key]["options"]);
          }    

          // Assign the value to the attribute
          $object->$key->set($value);

        }

      // We need to check if we are dealing with a value being an array inside collection class
      } elseif(is_array($value) && self::is_collection_class($object)) {
        // This is a collection class
        Logger::debug(get_class($object) . " is a collection class. Populate data: " . json_encode($data));
        $object->populate_collection($data);
        break;
      // Check is the attribute is allowed to be set on the object
      } else {
        throw new Error\InvalidAttributeError("Attribute $key is not allowed to be set on the class " . get_class($object));
      }
    }

    return $object;
  }

  /**
   * Method creates attributes based on the ATTRIBUTE_MAP constant
   * 
   * @param Object $object representing a model to build attributes for
   * @return Object instance of the passed object with assigned attributes
   * @throws \PHuby\Error\MissingAttributeError 
   */
  public static function create_attributes(&$object) {
    $str_caller_class = get_class($object);
    Logger::debug("Creating attributes for $str_caller_class");

    // Check if the attribute is settable
    if(!self::is_collection_class($object)) {
      // Collection class. We only want to instantiate it
      if(defined("$str_caller_class::ATTRIBUTE_MAP") && is_array($str_caller_class::ATTRIBUTE_MAP)) {
        foreach($str_caller_class::ATTRIBUTE_MAP as $attr_name => $data) {
          // Check if this is a sandard attribute
          if(array_key_exists("class", $data)) {
            $str_attr_class = $data["class"];
            $object->set_attr($attr_name, new $str_attr_class);
          }
        }
      } else {
        throw new Error\MissingAttributeError("ATTRIBUTE_MAP cannot be found insisde $str_caller_class");
      }
    }
  }

  public static function is_collection_class($object) {
    return preg_match("/Collection/", get_class($object)) == 1;
  }

  /**
   * Method checks if the attribute is allowed to be set on the model
   * by checking ATTRIBUTE_MAP constant set on the calling model.
   * 
   * @param string $attr_name represents the attribute name
   * @return boolean true if 
   */
  public static function is_attribute_allowed($object, $attr_name) {
    $caller_class = get_class($object);
    Logger::debug("Checking if $attr_name is allowed to be set on class $caller_class}");
    if(defined("\\$caller_class::ATTRIBUTE_MAP") && array_key_exists($attr_name, $object::ATTRIBUTE_MAP)) {
      return true;
    } elseif(self::is_attribute_a_child_class($object, $attr_name)) {
      return true;
    } else {
      return false;      
    }
  }

  public function is_attribute_a_child_class($object, $attr_name) {
    $caller_class = get_class($object);
    return defined("$caller_class::ATTRIBUTE_MAP") && array_key_exists($attr_name, $object::ATTRIBUTE_MAP) && array_key_exists("child_class", $object::ATTRIBUTE_MAP[$attr_name]);
  }


  public static function check_required_attributes($object, array $arr_attributes) {
    foreach($arr_attributes as $str_attr_name) {
      if(!$object->get_attr($str_attr_name)) {
        throw new Error\MissingAttributeError("Attribute $str_attr_name is not set on the object " . get_class($object));
      }
    }

    return true;

  }

  public static function is_standard_attribute(&$object, $str_attr_name) {
    if(self::is_attribute_allowed($object, $str_attr_name)) {
      return array_key_exists('class', $object::ATTRIBUTE_MAP[$str_attr_name]);
    }
  }

  public static function get_class_name_from_filepath($filepath) {
    // Check if this is inside lib folder
    $parts = explode("lib".Config::DS, $filepath);
    if(count($parts) > 1) {
      // We have a part after lib. Remove extensions and convert it to the class string
      return str_replace("/", "\\", explode('.', end($parts))[0]);
    } else {
      // It looks like this is not a class name that is defined inside lib folder
      return null;
    }
  }

}