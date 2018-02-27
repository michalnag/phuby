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

use PHuby\Logger;
use PHuby\Helpers\Utils;

abstract class AbstractCore {

  const 
    CLASS_TYPE_MODEL = 1,
    CLASS_TYPE_COLLECTION = 2,

    FLAT_DATA_RAW = 1,
    FLAT_DATA_DB_FORMAT = 2;

  // Set options for default DB formatted data options
  protected $arr_default_get_db_formatted_data_options = [
    "include" => [],
    "exclude" => [],
    "nesting" => false
  ];

  /**
   * Method sets the attribute based on the configuration inside ATTRIBUTE_MAP
   * @param string $str_attr_name representing name of the attribute
   * @param mixed $value representing a vlaue of the parameter to be set
   * @return boolean true once succesfully set
   * @throws \PHuby\Error\InvalidArgumentError when invalid value is passed
   * @todo - accomodate $value to be a class as well
   */
  public function set_attr($str_attr_name, $value) {
    Logger::debug("Setting $str_attr_name on " . get_class($this));

    // Check if the attribute is allowed first
    if ($this->is_attribute_allowed($str_attr_name)) {

      // Get attribute class
      $str_attr_class = $this->get_attribute_class($str_attr_name);

      // Check if this is a standard attribute 
      if ($this->is_attribute_standard($str_attr_name)) {
        // Check if there is an instance of the attribute set already
        if (!$this->$str_attr_name) {
          // Create attribute class          
          $this->$str_attr_name = new $str_attr_class;
        }

        // Check if there are some options
        if ($arr_options = $this->get_attribute_options($str_attr_name)) {
          $this->$str_attr_name->set_attribute_options($arr_options);
        }

        // At this stage we definitely have an attribute instantiated so set the value on it
        $this->$str_attr_name->set($value);

      }

      // Check if this is a child class
      if($this->is_attribute_child_class($str_attr_name)) {
        if (!$this->$str_attr_name) {
          // Create an instance of the child class
          $this->$str_attr_name = new $str_attr_class;
        }

        // Check if the value is an object and class matches the one set on the object
        if (is_object($value) && $value instanceof $str_attr_class) {
          $this->$str_attr_name = $value;
        } elseif (is_array($value)) {
          // And now populate attributes
          $this->$str_attr_name->populate_attributes($value);
        }
      }

      // Check if this is a collection class
      if($this->is_attribute_collection_class($str_attr_name)) {
        if (!$this->$str_attr_name) {
          // Create an instance of the child class
          $this->$str_attr_name = new $str_attr_class;
        }

        // Check if value is an instance of the configured class
        if (is_object($value)) {
          if ($value instanceof $str_attr_class) {
            // Assign object to the attribute
            $this->$str_attr_name = $value;
          } else {
            // Invalid instance of the class
            throw new Error\InvalidAttributeError(get_class($this) . "::$str_attr_name received instance of class " . get_class($value));
          }
        } else {
          // And now populate attributes
          $this->$str_attr_name->populate_collection($value);
        }


      }

    } else {
      // Trying to set unsupported attribute
      throw new Error\InvalidAttributeError("Attribute $str_attr_name is not allowed to be set on " . get_class($this));
    }
  }


  /**
   * Magic method gets the attribute set on the object based on ATTRIBUTE_MAP
   * @param string $str_attr_name representing name of the attribute
   * @return mixed representing a reference to the value set as object attribute
   */
  public function &get_attr($str_attr_name) {
    if($this->is_attribute_allowed($str_attr_name)) {
      return $this->$str_attr_name;
    } else {
      throw new Error\InvalidAttributeError("Non allowed attribute $str_attr_name cannot be retrieved on " . get_class($this));
    }
  }


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
        if ($this->is_attribute_standard($str_attr_name)) {
          // Standard attribute. Instantiate it
          $str_attr_class = $arr_attr_details['class'];
          $this->$str_attr_name = new $str_attr_class;

          // Check for options and default value
          if (array_key_exists('options', $arr_attr_details)) {
            $this->$str_attr_name->set_attribute_options($arr_attr_details['options']);
            if (array_key_exists('default_value', $arr_attr_details['options'])) {
              $this->$str_attr_name->set($arr_attr_details['options']['default_value']);
            }
          }
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
  public function populate_attributes($arr_attributes) {
    // Check if this is an array
    if (!is_array($arr_attributes)) {
      throw new Error\InvalidAttributeError(__METHOD__ . " must receive an array. Got " . gettype($arr_attributes));
    }

    // Start iterating over passed attributes
    foreach ($arr_attributes as $str_attr_name => $value) {
      // Set the attribute using dedicated setter method
      $this->set_attr($str_attr_name, $value);
    }

    // Process completed
    return true;
  }


  /**
   * Method retrieves class name of the attribute configured inside ATTRIBUTE_MAP
   * @param string $str_attr_name representing attribute name
   * @return string representing attribute class name
   * @throws \PHuby\Error\InvalidAttributeError when ATTRIBUTE_MAP is not set or attribute is not configured
   * @throws \PHuby\Error\MissingAttributeConfigError when ATTRIBUTE_MAP[$str_attr_name] does not contain class details
   */
  public function get_attribute_class($str_attr_name) {
    // Get caller class name
    $str_caller_class = get_class($this);
    
    // Check if the ATTRIBUTE_MAP is defined
    if (defined("$str_caller_class::ATTRIBUTE_MAP") && array_key_exists($str_attr_name, $this::ATTRIBUTE_MAP)) {
      
      // Check whether it contains class or child_class key
      if (array_key_exists('class', $this::ATTRIBUTE_MAP[$str_attr_name])) {
        return $this::ATTRIBUTE_MAP[$str_attr_name]['class'];

      } elseif (array_key_exists('child_class', $this::ATTRIBUTE_MAP[$str_attr_name])) {
        return $this::ATTRIBUTE_MAP[$str_attr_name]['child_class'];
      
      } elseif (array_key_exists('collection_class', $this::ATTRIBUTE_MAP[$str_attr_name])) {
        return $this::ATTRIBUTE_MAP[$str_attr_name]['collection_class'];

      } else {
        throw new Error\MissingAttributeConfigError("No class has been defined for $str_attr_name on $str_caller_class");
      }

    } else {
      // Not supported attribute
      throw new Error\InvalidAttributeError("$str_attr_class is not configured on ATTRIBUTE_MAP inside $str_caller_class");      
    }
  }


  /**
   * Method gets attribute options from the ATTRIBUTE_MAP[$str_attr_name]['options']
   * @param string $str_attr_name representing attribute name
   * @return mixed[] Array representing attribute options, null if no options are set
   */
  public function get_attribute_options($str_attr_name) {
    if (array_key_exists('options', $this::ATTRIBUTE_MAP[$str_attr_name])) {
      return $this::ATTRIBUTE_MAP[$str_attr_name]['options'];
    } else {
      return null;
    }
  }


  /**
   * Method checks if the attribute is allowed by checking ATTRIUTE_MAP array
   * configured on the caller class.
   * @param string $str_attr_name representing the attribute name
   */
  public function is_attribute_allowed($str_attr_name) {
    $str_caller_class = get_class($this);
    if(defined("$str_caller_class::ATTRIBUTE_MAP") && array_key_exists($str_attr_name, $this::ATTRIBUTE_MAP)) {
      return true;
    } else {
      return false;
    }
  }


  /**
   * Method checks is the attribute is a standard attribute and not a child class
   * @param string $str_attr_name representing parameter to be checked
   * @return boolean true if it is a standard attribute, false otherwise
   */
  public function is_attribute_standard($str_attr_name) {
    $caller_class = get_class($this);
    return defined("$caller_class::ATTRIBUTE_MAP") && array_key_exists($str_attr_name, $this::ATTRIBUTE_MAP) && array_key_exists("class", $this::ATTRIBUTE_MAP[$str_attr_name]);
  }


  /**
   * Method checks whether this attribute is a child class or not by checking ATTRIBUTE_MAP config
   * @param string $str_attr_name representing attribute name
   * @return boolean true if attribute is a child_class, false otherwise
   */
  public function is_attribute_child_class($str_attr_name) {
    $str_caller_class = get_class($this);
    return defined("$str_caller_class::ATTRIBUTE_MAP") && array_key_exists($str_attr_name, $this::ATTRIBUTE_MAP) && array_key_exists("child_class", $this::ATTRIBUTE_MAP[$str_attr_name]);
  }


  /**
   * Method checks whether this attribute is a collection class or not by checking ATTRIBUTE_MAP config
   * @param string $str_attr_name representing attribute name
   * @return boolean true if attribute is a collection_class, false otherwise
   */
  public function is_attribute_collection_class($str_attr_name) {
    $str_caller_class = get_class($this);
    return defined("$str_caller_class::ATTRIBUTE_MAP") && array_key_exists($str_attr_name, $this::ATTRIBUTE_MAP) && array_key_exists("collection_class", $this::ATTRIBUTE_MAP[$str_attr_name]);
  }


  /**
   * Method gets the class type by interpreting CLASS_TYPE constant set on the class
   * @return integer representing a class type (set on the AbsstractCore), null if undefined
   * @todo check if the constant is set on the class
   */
  public function get_class_type() {
    return $this::CLASS_TYPE;
  }


  /**
   * Method checks whether the class is a collection class or not
   * @return boolean true is current class is a collection class, false otherwise
   */
  protected function is_collection_class() {
    return $this->get_class_type() == self::CLASS_TYPE_COLLECTION;
  }


  /**
   * Method gets the flat data using one of the method available in the attribute interface
   * @param string|array $mix_options representing custom options to get for the method
   * @param integer $int_data_type representing the type of the data to be retrieved
   * @return mixed[] Array representing raw data
   */
  public function get_flat_data($mix_options = null, $int_data_type = self::FLAT_DATA_DB_FORMAT) {

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
          $arr_result[] = $obj_collectable->get_model_flat_data($arr_options, $int_data_type);
        }

      }
    } else {

      // Standard child class
      $arr_result = $this->get_model_flat_data($arr_options, $int_data_type);
    }

    // Return
    return empty($arr_result) ? null : $arr_result; 
  }


  /**
   * Method gets the flat data from the model type of object
   * @param mixed[] $arr_options representing an array with options (see get_flat_data for more details)
   * @param integer $int_data_type representing a flat data type (see get_flat_data for more details)
   * @return mixed[] representing requested flat data
   * @todo implement method type recognition
   */
  protected function get_model_flat_data($arr_options, $int_data_type) {
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
              $arr_return_data[$str_attr_name][] = $obj_collectable->get_model_flat_data($arr_options, $int_data_type);
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