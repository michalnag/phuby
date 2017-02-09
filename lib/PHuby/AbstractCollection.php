<?php

namespace PHuby;

use PHuby\AbstractCore;

abstract class AbstractCollection extends AbstractCore {

  protected $collection = [];

  /**
   * Method populates the collection on the object, if the object is a collection class.
   * It relies on COLLECT_CLASS constant to be present on caller class, which represents the class to collect.
   * @param mixed[] $arr_collection containg array of arrays|objects representing the collection
   * @return boolean true when collection has finished populating
   * @todo - check if the COLLECT_CLASS is defined
   */
  public function populate_collection(Array $arr_collection) {
  
    // Iterate over the collection and try to add all elements ti the collection
    foreach ($array as $data) {

      // Simply add to the collection
      $this->add_to_collection($data);

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
    $str_collect_class = $this::COLLECT_CLASS;

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

}