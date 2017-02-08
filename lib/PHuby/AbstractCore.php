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
   * @return boolean true once all attributes are created
   */
  public function initiate_attributes() {}

  /**
   * Method for populating attributes based on the ATTRIBUTE_MAP array
   * configured on the child class.
   * @param mixed[] $arr_attributes containing data to be populated
   */
  public function populate_attributes(Array $arr_attributes) {}

}