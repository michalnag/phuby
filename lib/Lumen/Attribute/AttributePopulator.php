<?php

namespace Attribute;
use AbstractCore;
use Logger;
use Error;

abstract class AttributePopulator extends AbstractCore {
  
  /**
  * Method is responsible for assigning attributes to the class based on the configuration
  * inside the model that calls it. Attribute mapping is defined in const ATTRIBUTE_MAP
  *
  * @param mixed[] Array containind data to be assigned to attributes
  * @return TODO
  */
  public function poulate_attributes(Array $data) {
    Logger::debug(get_class($this) . " will be built from the array.");
    // Loop through the array and assign correct attributes
    foreach($data as $key => $value) {
      
      // Check is the attribute is allowed to be set on the object
      if($this->is_attribute_allowed($key)) {

        // Check if the passed attribute is a child class or standard attribute
        if($this->is_attribute_a_child_class($key)) {

          // Get class name and build the child from the array
          $child_class_name = $this::CHILD_CLASS_MAP[$key];
          $this->$key = new $child_class_name();
          $this->$key->poulate_attributes($value);

        } else {
          // Attribute is a standard attribute class
          // Get a class name that is configured for this attribute
          $attribute_class = "Attribute\\" . $this::ATTRIBUTE_MAP[$key]["attribute_class"];        
          Logger::debug("Attribute $key is configured on the class " . get_class($this) . " as $attribute_class");

          // Create an instance of the attribute
          $this->$key = new $attribute_class();

          // Check if there are custom options set on the object
          if(array_key_exists("attribute_options", $this::ATTRIBUTE_MAP[$key])) {
            $this->$key->set_attribute_options($this::ATTRIBUTE_MAP[$key]["attribute_options"]);
          }    

          // Assign the value to the attribute
          $this->$key->set($value);
        }

      } else {
        throw new Error\InvalidAttributeError("Attribute $key is not allowed to be set on the class " . get_class($this));
      }
    }

    return $this;
  }

  /**
   * Method checks if the attribute is allowed to be set on the model
   * by checking ATTRIBUTE_MAP constant set on the calling model.
   * 
   * @param string $attr_name represents the attribute name
   * @return boolean true if 
   */
  protected function is_attribute_allowed($attr_name) {
    return array_key_exists($attr_name, $this::ATTRIBUTE_MAP) || $this->is_attribute_a_child_class($attr_name);
  }

  protected function is_attribute_a_child_class($attr_name) {
    $caller_class = get_class($this);
    return defined("$caller_class::CHILD_CLASS_MAP") && array_key_exists($attr_name, $this::CHILD_CLASS_MAP);
  }

  /**
   * Method checks if all attributes passed in the array are set on the object
   *
   * @param string[] Array $attributes containing required attributes
   * @return true if all attributes are set
   * @throws Error\MissingAttributeError if attribute is missing
   */
  protected function check_required_attributes(Array $attributes) {
    foreach($attributes as $attribute) {
      if(isset($this->$attribute)) {
        continue;
      } else {
        throw new Error\MissingAttributeError("Attribute $attribute is not set on the class " . get_class($this));
      }
    }
    // If we have reached this point it means that all attributes are set
    return true;
  }
}