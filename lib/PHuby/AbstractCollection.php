<?php
/**
 * AbstractCollection sits as a base in every collection object
 * 
 * @author Michal Nagielski <michal.nagielski@gmail.com>
 * @package PHuby
 */

namespace PHuby;

use PHuby\Error;

abstract class AbstractCollection extends AbstractCore {

  protected $collection = [];

  /**
   * Method populates the collection on the object, if the object is a collection class.
   * It relies on COLLECT_CLASS constant to be present on caller class, which represents the class to collect.
   * @param mixed[] $arr_collection containg array of arrays|objects representing the collection
   *    (null is acceptable)
   * @return boolean true when collection has finished populating, false otherwise
   * @throws PHuby\Error\InvalidArgumentError when invalid argument is passed
   * @todo - check if the COLLECT_CLASS is defined
   * @todo - check if array is empty
   */
  public function populate_collection($arr_collection) {
    
    // Check if correct parameter has been passed
    if (is_array($arr_collection)) { 
        
      // Iterate over the collection and try to add all elements ti the collection
      foreach ($arr_collection as $data) {

        // Simply add to the collection
        $this->add_to_collection($data);

      }

    } elseif(is_null($arr_collection)) {

      // No collection has been popualted because null has been passed
      return false;

    } else {
      // Invalid argument passed
      throw new Error\InvalidArgumentError(__METHOD__ . " must be passed an array or null. Got " . gettype($arr_collection));
    }


    // We finished the loop and popualting collection
    return true;
  }


  /**
   * Method adds object to the collection
   * @param mixed[]|object $value representing the data to be put to the collection
   * @return boolean true when the the data is added to the collection
   */
  public function add_to_collection($value) {

    // Get the collect class
    $str_collect_class = $this::COLLECT_CLASS['name'];

    // Check if the data is an array or collectable object
    if (is_object($value) && $value instanceof $str_collect_class) {
      
      // value passed is an object that can be just added to the collection
      $this->collection[] = $value;

    } elseif (is_array($value)) {

      // Create an instance of the collect class and populate the value
      $obj_collectable = new $str_collect_class;
      $obj_collectable->populate_attributes($value);
      $this->collection[] = $obj_collectable;

    } else {
      // Unsupported value type passed
      throw new Error\InvaligArgumentError(get_class($this) . " expects to collect array or an instance of $str_collect_class only. Got " . gettype($value));        
    }
  }

  
  /**
   * Method gets the collection set on the object
   * @return mixed[] Array representing the collection
   */
  public function get_collection() {
    return $this->collection;
  }


  /**
   * Method checks if the collection is populated or not
   * @return boolean true if the collection is populated, false otherwise
   */
  public function is_collection_populated() {
    return !empty($this->collection);
  }


  /**
   * Resets collection to an empty array
   * @return boolean true once collection is cleared
   */
  public function clear_collection() {
    $this->collection = [];
    return true;
  }


  /**
   * Method retrieves raw collection data as a set of array of arrays
   * @return mixed[] Array representing raw collection data, null if not populated
   */
  public function get_raw_collection_data() {
    if($this->is_collection_populated()) {
      $arr_raw_data = [];
      foreach($this->collection as $obj_collectable) {
        $arr_raw_data[] = $obj_collectable->get_raw_data();
      }
      return $arr_raw_data;
    } else {
      return null;
    }
  }


  /**
   * Method will populate same attribute to all objects inside the collection
   * @param mixed[] $arr_attributes Array conatining attributes names and values
   * @todo - build and complete this method
   */
  public function populate_attributes_to_collection(Array $arr_attributes) {}


  /**
   * Method sets a single attribute on all objects inside the collection 
   * @param string $str_attr_name representing a name of the attribute
   * @param mixed $attr_value representing an attribute value
   * @return boolean true when succesfully set, false if collection is not populated
   */
  public function set_attr_on_collection($str_attr_name, $attr_value) {

    // Check if collection is populated first
    if($this->is_collection_populated()) {
      
      foreach($this->collection as $obj_collectable) {
        $obj_collectable->set_attr($str_attr_name, $attr_value);
      }

      // Setting completed
      return true;

    } else {
      // Collection is not populated
      return false;
    }
  }

}