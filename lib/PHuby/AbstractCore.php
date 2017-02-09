<?php
/**
 * AbstractCore
 * Every Controller / Model / ModelCollection / Process / Network inherits
 * this class. All functionality found here is available in namespaces listed.
 * 
 * @author Michal Nagielski <michal.nagielski@gmail.com>
 * @package PHuby
 */

namespace PHuby;

abstract class AbstractCore {

  /**
   * Method sets the attribute based on the configuration inside ATTRIBUTE_MAP
   * @param string $str_attr_name representing name of the attribute
   * @param mixed $value representing a vlaue of the parameter to be set
   * @return boolean true once succesfully set
   * @throws \PHuby\Error\InvalidArgumentError when invalid value is passed
   */
  public function set_attr($str_attr_name, $value) {}


  /**
   * Magic method gets the attribute set on the object based on ATTRIBUTE_MAP
   * @param string $str_attr_name representing name of the attribute
   * @return mixed representing a reference to the value set as object attribute
   */
  public function &get_attr($str_attr_name) {}


  /**
   * Method initiates attributes based on the ATTRIBUTE_MAP array configured on
   * the child class, attempting to set a default value, or null if not set.
   * This method can be run on the __contruct but only in the child class.
   * @return boolean true once all attributes are created, false if ATTRIBUTE_MAP is undefined
   */
  public function initiate_attributes() {

    // Check if the ATTRIBUTE_MAP is defined
    $str_caller_class = get_class($this);
    if (defined("$str_caller_class::ATTRIBUTE_MAP")) {

      // We have a map set. Iterate over it and create standard attributes
      foreach ($this::ATTRIBUTE_MAP as $str_attr_name => $arr_attr_details) {
        // Check if this is standard attribute
        if ($this->is_standard_attribute($str_attr_name)) {
          // Standard attribute. Instantiate it
          $str_attr_class = $arr_attr_details['class'];
          $this->$str_attr_name = new $str_attr_class;
        }
      }

      // Process completed. Return true
      return true;

    } else {
      // No ATTRIBUTE_MAP set. Return false
      return false;
    }
  }


  /**
   * Method for populating attributes based on the ATTRIBUTE_MAP array
   * configured on the child class.
   * @param mixed[] $arr_attributes containing data to be populated
   */
  public function populate_attributes(Array $arr_attributes) {}


  /**
   * Method checks is the attribute is a standard attribute and not a child class
   * @param string $str_attr_name representing parameter to be checked
   * @return boolean true if it is a standard attribute, false otherwise
   */
  public function is_standard_attribute($str_attribute_name) {
    $caller_class = get_class($this);
    return defined("$caller_class::ATTRIBUTE_MAP") && array_key_exists($str_attribute_name, $this::ATTRIBUTE_MAP) && array_key_exists("class", $this::ATTRIBUTE_MAP[$str_attribute_name]);
  }

}