<?php

namespace PHuby\Traits;
use PHuby\Logger;
use PHuby\Error;
use PHuby\Helpers\Utils\ArrayUtils;
use PHuby\Attribute\AttributeInterface;

trait SupportsAttributes {
  
  protected $__attribute_map = [];

  /**
   * Method returns attribute map by merging ATTRIBUTE_MAP constant and
   * $this->attribute_map = [...] which can be store in trait or parent
   * 
   * @return mixed[] representing an array map
   */
  public function get_attribute_map() {
    $str_caller_class = get_class($this);

    // If already created, return
    if (!empty($this->__attribute_map)) {
      return $this->__attribute_map;
    }
    
    // Add shared attribute maps
    $this->__attribute_map = array_merge_recursive(
      $this->__attribute_map,
      $this->get_shared_attribute_maps()
    );


    // Constant on the class has the priority
    if (defined("$str_caller_class::ATTRIBUTE_MAP")) {
      $this->__attribute_map = array_merge_recursive(
        $this->__attribute_map,
        static::ATTRIBUTE_MAP
      );
    }

    return $this->__attribute_map;

  }

  protected function get_shared_attribute_maps() {
    $attribute_map_vars = array_filter(
      get_object_vars($this),
      function ($element) {
        return preg_match('/^_attribute_map/', $element);
      },
      ARRAY_FILTER_USE_KEY
    );

    $return_map = [];
    foreach ($attribute_map_vars as $attribute_map_name => $attribute_map_data) {
      $return_map = array_merge_recursive(
        $return_map,
        $attribute_map_data
      );
    }
    
    return $return_map;
  }


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
    // We have a map set. Iterate over it and create standard attributes
    foreach ($this->get_attribute_map() as $str_attr_name => $mix_attr_details) {
      // Check if this is standard attribute

      if ($this->is_attribute_standard($str_attr_name)) {
        // Standard attribute. Instantiate it
        $str_attr_class = $this->get_attribute_class($str_attr_name);
        $this->$str_attr_name = new $str_attr_class;

        // Check for options and default value
        if (is_array($mix_attr_details) && array_key_exists('options', $mix_attr_details)) {
          $this->$str_attr_name->set_attribute_options($mix_attr_details['options']);
          if (array_key_exists('default_value', $mix_attr_details['options'])) {
            $this->$str_attr_name->set($mix_attr_details['options']['default_value']);
          }
        }
      } elseif ($this->is_attribute_collection_class($str_attr_name)) {
        // Instantiate only collection class without any elements inside it
        $str_attr_class = $mix_attr_details['collection_class'];
        $this->$str_attr_name = new $str_attr_class;
      }
    }
  }

  /**
   * Method retrieves class name of the attribute configured inside ATTRIBUTE_MAP
   * @param string $str_attr_name representing attribute name
   * @return string representing attribute class name
   * @throws \PHuby\Error\InvalidAttributeError when ATTRIBUTE_MAP is not set or attribute is not configured
   * @throws \PHuby\Error\MissingAttributeConfigError when ATTRIBUTE_MAP[$str_attr_name] does not contain class details
   */
  protected function get_attribute_class($str_attr_class) {
    $mix_attr_details = $this->get_attribute_map()[$str_attr_class];
    if (is_string($mix_attr_details)) {
      return $mix_attr_details;
    }
    if (is_array($mix_attr_details)) {
      switch (true) {
        case array_key_exists(AttributeInterface::ATTRIBUTE_CLASS, $mix_attr_details):
          return $mix_attr_details[AttributeInterface::ATTRIBUTE_CLASS];
        case array_key_exists(AttributeInterface::COLLECTION_CLASS, $mix_attr_details):
          return $mix_attr_details[AttributeInterface::COLLECTION_CLASS];
        case array_key_exists(AttributeInterface::MODEL_CLASS, $mix_attr_details):
          return $mix_attr_details[AttributeInterface::MODEL_CLASS];
      }
    }
    throw new \Exeption();
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
   * Alias method for populate_attributes
   */
  public function populate($arr_attributes) {
    return $this->populate_attributes($arr_attributes);
  }

  /**
   * Method gets attribute options from the ATTRIBUTE_MAP[$str_attr_name]['options']
   * @param string $str_attr_name representing attribute name
   * @return mixed[] Array representing attribute options, null if no options are set
   */
  public function get_attribute_options($str_attr_name) {
    if (is_array($this->get_attribute_map()[$str_attr_name]) && array_key_exists('options', $this->get_attribute_map()[$str_attr_name])) {
      return $this->get_attribute_map()[$str_attr_name]['options'];
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
    return array_key_exists($str_attr_name, $this->get_attribute_map());
  }

  /**
   * Method checks is the attribute is a standard attribute and not a child or collection class
   * @param string $str_attr_name representing parameter to be checked
   * @return boolean true if it is a standard attribute, false otherwise
   */
  public function is_attribute_standard($str_attr_name) {
    $mix_attr_data = $this->get_attribute_map()[$str_attr_name];
    return is_string($mix_attr_data) 
      || (is_array($mix_attr_data) && array_key_exists(AttributeInterface::ATTRIBUTE_CLASS, $mix_attr_data));
  }


  /**
   * Method checks whether this attribute is a child class or not by checking ATTRIBUTE_MAP config
   * @param string $str_attr_name representing attribute name
   * @return boolean true if attribute is a child_class, false otherwise
   */
  public function is_attribute_child_class($str_attr_name) {
    $mix_attr_data = $this->get_attribute_map()[$str_attr_name];
    return is_array($mix_attr_data) && array_key_exists(AttributeInterface::MODEL_CLASS, $mix_attr_data);
  }


  /**
   * Method checks whether this attribute is a collection class or not by checking ATTRIBUTE_MAP config
   * @param string $str_attr_name representing attribute name
   * @return boolean true if attribute is a collection_class, false otherwise
   */
  public function is_attribute_collection_class($str_attr_name) {
    $mix_attr_data = $this->get_attribute_map()[$str_attr_name];
    return is_array($mix_attr_data) && array_key_exists(AttributeInterface::COLLECTION_CLASS, $mix_attr_data);
  }

  /**
   * Method returns an array of parameters to db format
   * @param string $str_params   Comma separated parameters to be returned
   * @return mixed[] Array       Containing keyed and db formatted parameters 
   */
  public function get_formatted_params($str_params, $str_callback_method = 'to_db_format') {
    $arr_return_params = [];
    foreach (ArrayUtils::keymap_to_array($str_params) as $str_param_name) {
      $arr_return_params[$str_param_name] = $this->$str_param_name->{$str_callback_method}();
    }
    return $arr_return_params;
  }


}