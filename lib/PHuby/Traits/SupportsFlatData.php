<?php

namespace PHuby\Traits;

use PHuby\Logger;
use PHuby\Helpers\Utils;

trait SupportsFlatData {

  // Set options for default DB formatted data options
  public $arr_default_get_db_formatted_data_options = [
    "include" => [],
    "exclude" => [],
    "nesting" => false
  ];

  /**
   * Method gets the flat data using one of the method available in the attribute interface
   * @param string|array $mix_options representing custom options to get for the method
   * @param integer $int_data_type representing the type of the data to be retrieved
   * @return mixed[] Array representing raw data
   */
  public function get_flat_data($mix_options = null) {

    // Initiate the array to hold the data
    $arr_result = [];

    // Check if we have options passed, and if so, process them
    $arr_options = $this->arr_default_get_db_formatted_data_options;
    // Set custom options
    $arr_custom_options = [];
    if (is_string($mix_options)) {
      $arr_custom_options = Utils\ArrayUtils::keymap_to_array($mix_options);
    } elseif (is_array($mix_options)) {
      $arr_custom_options = $mix_options;
    }

    $arr_options = array_merge($arr_options, $arr_custom_options);

    // First, check if this is a collection class
    if ($this->is_collection_class()) {

      if ($this->is_collection_populated()) {
        // Iterate over the collection
        foreach($this->get_collection() as $obj_collectable) {
          $arr_result[] = $obj_collectable->get_model_flat_data($arr_options);
        }
      }

    } else {

      // Standard child class
      $arr_result = $this->get_model_flat_data($arr_options);
    }

    // Return
    return $arr_result; 
  }


  /**
   * Method gets the flat data from the model type of object
   * @param mixed[] $arr_options representing an array with options (see get_flat_data for more details)
   * @return mixed[] representing requested flat data
   * @todo implement method type recognition
   */
  public function get_model_flat_data($arr_options) {
    // Initiate return data array
    $arr_return_data = [];

    // Iterate over ATTRIBUTE_MAP
    foreach ($this::ATTRIBUTE_MAP as $str_attr_name => $arr_attr_options) {

      // Check if the attribute is excluded from options or if we have any include set
      if (
        (array_key_exists('include', $arr_options) && !empty($arr_options['include']) && !array_key_exists($str_attr_name, array_flip($arr_options['include'])))
        || (array_key_exists('exclude', $arr_options) && array_key_exists($str_attr_name, array_flip($arr_options['exclude'])))
      ) {
        // We want do not want to process this attribute
        continue;
      }

      // Start checking whether this is a standard attribute or not
      if ($this->is_attribute_standard($str_attr_name)) {
        $arr_return_data[$str_attr_name] = $this->$str_attr_name->to_db_format();
      }

      // Check if this is a child class
      if ($this->is_attribute_child_class($str_attr_name) || $this->is_attribute_collection_class($str_attr_name)) {

        // Attribute is a child class. Check if it is supposed to be included
        if (array_key_exists('nesting', $arr_options) && $arr_options["nesting"][0]) {

          // Include child class. Check if this is a collection class or not
          if ($this->get_attr($str_attr_name)
              && $this->get_attr($str_attr_name)->is_collection_class()
              && $this->get_attr($str_attr_name)->is_collection_populated()
            ) {
            // Initiate the array
            $arr_return_data[$str_attr_name] = [];

            // Iterate over the collection
            foreach ($this->$str_attr_name->get_collection() as $obj_collectable) {
              $arr_return_data[$str_attr_name][] = $obj_collectable->get_model_flat_data($arr_options);
            }

          } elseif ($this->get_attr($str_attr_name)) {

            // Include model data
            $arr_return_data[$str_attr_name] = $this->$str_attr_name->get_flat_data($arr_options);
          }

        } else {
          // Do not include child class
        }
      } 


    }

    return $arr_return_data;
  } 
}