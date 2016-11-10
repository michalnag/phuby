<?php

namespace Lumen\Helpers\Utils;

use Lumen\Helpers\AbstractUtils;
use Lumen\Error;
use Lumen\Logger;

class ObjectUtils extends AbstractUtils {
 
  /**
  * Method is responsible for assigning attributes to the class based on the configuration
  * inside the model that calls it. Attribute mapping is defined in const ATTRIBUTE_MAP
  *
  * @param mixed[] Array containind data to be assigned to attributes
  * @return TODO
  */
  public static function populate_attributes(&$object, Array $data) {
    Logger::debug(get_class($object) . " will be built from the array.");
    // Loop through the array and assign correct attributes
    foreach($data as $key => $value) {
      
      // Check is the attribute is allowed to be set on the object
      if($object->is_attribute_allowed($key)) {

        // Check if the passed attribute is a child class or standard attribute
        if($object->is_attribute_a_child_class($key)) {

          // Get class name and build the child from the array
          $child_class_name = $object::CHILD_CLASS_MAP[$key];
          $object->$key = new $child_class_name();
          $object->$key->poulate_attributes($value);

        } else {
          // Attribute is a standard attribute class
          // Get a class name that is configured for this attribute
          $attribute_class = "Lumen\Attribute\\" . $object::ATTRIBUTE_MAP[$key]["attribute_class"];        
          Logger::debug("Attribute $key is configured on the class " . get_class($object) . " as $attribute_class");

          // Create an instance of the attribute
          $object->$key = new $attribute_class();

          // Check if there are custom options set on the object
          if(array_key_exists("attribute_options", $object::ATTRIBUTE_MAP[$key])) {
            $object->$key->set_attribute_options($object::ATTRIBUTE_MAP[$key]["attribute_options"]);
          }    

          // Assign the value to the attribute
          $object->$key->set($value);
        }

      } else {
        throw new Error\InvalidAttributeError("Attribute $key is not allowed to be set on the class " . get_class($object));
      }
    }

    return $object;
  }

  /**
   * Method checks if the attribute is allowed to be set on the model
   * by checking ATTRIBUTE_MAP constant set on the calling model.
   * 
   * @param string $attr_name represents the attribute name
   * @return boolean true if 
   */
  public function is_attribute_allowed(&$object, $attr_name) {
    return array_key_exists($attr_name, $object::ATTRIBUTE_MAP) || self::is_attribute_a_child_class($object, $attr_name);
  }

  public function is_attribute_a_child_class(&$object, $attr_name) {
    $caller_class = get_class($object);
    return defined("$caller_class::CHILD_CLASS_MAP") && array_key_exists($attr_name, $object::CHILD_CLASS_MAP);
  }


  public static function check_required_attributes($object, Array $attributes, $attribute_type = "dynamic") {
    
    switch($attribute_type) {
      case "dynamic":
        foreach($attributes as $attribute) {
          if(!isset($object->$attribute)) {
            throw new Error\MissingAttributeError("Attribute $attribute is not set on the object " . get_class($object));
          }
        }
        break;
        
    }

    return true;

  }


}