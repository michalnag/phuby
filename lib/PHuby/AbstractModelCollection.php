<?php

namespace PHuby;

use PHuby\Logger;
use PHuby\Error;
use PHuby\Helpers\Utils\ObjectUtils;

abstract class AbstractModelCollection extends AbstractModel implements BaseModelInterface {

  public $collection = [];

  public function populate_collection(Array $array) {
    // Collection method expects an array of arrays so let's check if the right format has been passed
    foreach ($array as $data) {
      // Create an instance of the Collectable class
      $this->add_to_collection($this->array_to_collactable_object($data));
    }
  }

  protected function array_to_collactable_object(Array $arr_data) {
    $class_name = $this::COLLECT_CLASS['name'];
    $obj_collectable = new $class_name();
    $obj_collectable->populate_attributes($arr_data);
    return $obj_collectable;
  }

  public function add_to_collection($obj_collectable) {
    $str_collect_class = $this::COLLECT_CLASS['name'];
    if($obj_collectable instanceof $str_collect_class) {
      $this->collection[] = $obj_collectable;
    } else {
      throw new Error\InvalidArgumentError(get_class($this) . " expects an instace of $str_collect_class, got " . get_class($obj_collectable));
    }
  }

  public function populate_attribute_to_collection($arr_attributes) {
    if($this->is_collection_populated()) {
      foreach($this->collection as $obj_collectable) {
        $obj_collectable->populate_attributes($arr_attributes);
      }
    } else {
       return false;
    }
  }

  public function is_collection_populated() {
    return !empty($this->collection);
  }

  /**
   * Restarts collection to an empty array
   * 
   * @return boolean true once collection is cleared
   */
  public function clear_collection() {
    $this->collection = [];
    return true;
  }

  public function get_raw_data() {
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
   * Allows to pass an array with data representing a collactable object data
   * creates respective class and adds it to the collection.
   * 
   * @param mixed[] $arr_data Array representing the data to be put to the collection
   * @return boolean true if data is added to the collection
   */
  public function add_raw_to_collection(Array $arr_data) {
    foreach($arr_data as $key => $value) {

    }
  }

}