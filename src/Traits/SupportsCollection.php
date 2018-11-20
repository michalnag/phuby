<?php

namespace PHuby\Traits;

use \ArrayIterator;
use PHuby\Error;

trait SupportsCollection {
  

  protected $collection = [];

  public function __construct(array $arr_collection = null) {
    if ($arr_collection) {
      $this->populate_collection($arr_collection);
    }
  }

  /**
   * Allow foreach to be called on the instance
   *
   * @return ArrayIterator
   */
  public function getIterator() {
    return new ArrayIterator($this->get_collection());
  }

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
      throw new Error\InvalidArgumentError(get_class($this) . " expects to collect array or an instance of $str_collect_class only. Got " . gettype($value));        
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
   * Method will populate same attribute to all objects inside the collection
   * @param mixed[] $arr_attributes Array conatining attributes names and values
   * @todo - build and complete this method
   */
  public function populate_attributes_to_collection(array $arr_attributes) {}


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

  /**
   * Method updates collection attributes, based on the collactable element key
   * @param mixed[] $arr_data Array containing collection data
   * @param string $str_key_attr_name String representing an attribute name that will be used as a key for comaprison
   */
  public function update_collection_by_key(array $arr_data, $str_key_attr_name) {
    // Check if the collection is populated
    if ($this->is_collection_populated()) {

      // We now want to scan through the $arr_data and get collectable
      foreach ($arr_data as $arr_details) {
        // Get by the key
        $arr_objects = $this->get_by_attr($str_key_attr_name, $arr_details[$str_key_attr_name]);

        if ($arr_objects) {
          foreach ($arr_objects as &$obj_collectable) {
            $obj_collectable->populate_attributes($arr_details);
          }
        }

      }

      return true;

    } else {
      /** @todo Collection is not populated */

    }
  }


  /**
   * Method returns an object from the collection based on the certain attribute
   *
   * @param string $str_attr_name representing a name of the attribute
   * @param mixed $mix_value representind value that needs matching
   * @return Array of elements from the collection, empty array if not found
   */
  public function get_by_attr($str_attr_name, $mix_value) {

    if($this->is_collection_populated()) {

      $arr_result = [];

      foreach($this->get_collection() as &$obj_collectable) {
        if ($obj_collectable->get_attr($str_attr_name)->get() == $mix_value) {
          $arr_result[] = $obj_collectable;
        }
      }

      return $arr_result;

    } else {
      // Collection not populated
      return [];
    }
  }

  /**
   * Method removes an object from the collection based on the certain attribute
   *
   * @param string $str_attr_name representing a name of the attribute
   * @param mixed $mix_value representind value that needs matching
   * @return integer representing amount of objects got deleted
   */
  public function remove_by_attr($str_attr_name, $mix_value) {
    // Inner counter used for return value
    $int_removed = 0;

    if($this->is_collection_populated()) {
      foreach($this->get_collection() as $key => $obj_collectable) {
        if ($obj_collectable->get_attr($str_attr_name)->get() == $mix_value) {
          unset($this->collection[$key]);
          $int_removed++;
        }
      }
    }

    return $int_removed;
  }

  /**
   * This method simply returns the amount of entries in the first level collection
   * 
   * @return integer representing amount of entires in the collection
   */
  public function get_count() {
    return count($this->get_collection());
  }

}