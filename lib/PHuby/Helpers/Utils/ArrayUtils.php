<?php
/**
 * ArrayUtils
 * 
 * @author Michal Nagielski <michal.nagielski@gmail.com>
 * @package PHuby\Helpers\Utils
 */

namespace PHuby\Helpers\Utils;

use PHuby\Helpers\AbstractUtils;
use PHuby\Error;
use PHuby\Logger;

class ArrayUtils extends AbstractUtils {   

  /**
   * Checks if multiple keys exist in the array
   * 
   * @param mixed[] $arr_keys representing keys to check
   * @param mixed[] $arr_haystack representing an array to be checked
   * @return boolean treu if all keys are found, false otherwise
   */
  public static function keys_exist(array $arr_keys, array $arr_haystack) {
    $bol_all_keys_exist = true;
    foreach($arr_keys as $key) {
      if(!array_key_exists($key, $arr_haystack)) {
        $bol_all_keys_exist = false;
        break;
      }
    }
    return $bol_all_keys_exist;
  }

  /**
   * Method adds the data to the array in the specific keymap
   * 
   * @param string $str_keymap representing map in the format key:key:[]
   *    When [] is passed, it will add the data to the array without specific key
   *    When string is passed, it will add data to the key, or create one if does not exist
   * @param mixed[] Array representing an array source
   * @param mixed $data representing data to be added to the particular array
   * @return mixed[] Array with combined data
   */
  public static function add_data($str_keymap, array &$arr_source, $data) {

    $arr_keys = explode(":", $str_keymap, 2);

    if(count($arr_keys) == 1) {
      // This is the last key
      if($str_keymap == "[]") {
        $arr_source[] = $data;
      } else {
        if(!array_key_exists($str_keymap, $arr_source)) {
          $arr_source[$str_keymap] = $data; 
        } else {
          if(is_array($arr_source[$str_keymap])) {
            $arr_source[$str_keymap][] = $data; 
          } else {
            // TODO - check if the key is a duplicate      
          }
        }
      }
    } else {
      if(!array_key_exists($arr_keys[0], $arr_source)) {
        if($arr_keys[0] == "[]") {
          $arr_source = [];
          self::add_to_array($arr_keys[1], $arr_source, $data);
        } else {
          $arr_source[$arr_keys[0]] = [];  
        }
      } 
      self::add_data($arr_keys[1], $arr_source[$arr_keys[0]], $data);
    }

    return $arr_source;
  }

  /**
   * Method gets the data based on the keymap given
   * 
   * @param string $str_keymap representing keymap
   * @param mixed[] $arr_source 
   * @return mixed representing the data inside the given keymap, null otherwise
   */
  public static function get_data($str_keymap, array $arr_source) {
    // Deal with the keymap
    $arr_keys = explode(":", $str_keymap, 2);

    $return_data = null;

    if(array_key_exists($arr_keys[0], $arr_source)) {
      if(count($arr_keys) == 1) {
        $return_data = $arr_source[$arr_keys[0]]; 
      } else {
        $return_data = self::get_data($arr_keys[1], $arr_source[$arr_keys[0]]);
      }
    }

    return $return_data;
  }

  /**
   * Method removes the relevant key from the array based on the keymap.
   *
   * @param string $str_keymap representing keymap (see above for examples)
   * @param mixed[] $arr_source 
   * @return boolean true if data has been removed, false otherwise
   */
  public static function remove_data($str_keymap, array &$arr_source) {

    // First, we want to convert keymap to the array
    $arr_keymap = self::keymap_to_array($str_keymap, []);

    self::remove_data_by_array_keymap(self::keymap_to_array($str_keymap, []), $arr_source);

/*    $arr_keys = explode(":", $str_keymap, 2);



    $bol_return = false;

    if(array_key_exists($arr_keys[0], $arr_source)) {
      if(count($arr_keys) == 1) {
        unset($arr_source[$arr_keys[0]]); 
        $bol_return = true;
      } else {
        $bol_return = self::remove_data($arr_keys[1], $arr_source[$arr_keys[0]]);
      }
    }

    return $bol_return;*/

    return true;
  }


  protected static function remove_data_by_array_keymap(array $arr_keymap, array &$arr_source) {

    foreach($arr_keymap as $key => $value) {

      // Check if the value is an array
      if (is_array($value)) {

        // Nesting. Check if the value contains an array
        if (array_key_exists(0, $value) && is_array($value[0])) {

          // We are removing key from array of arrays
          foreach ($value[0] as $sub_key => $sub_value) {

            // For each key we need to unset the variable from $arr_source
            foreach ($arr_source[$key] as &$arr_target) {
              unset($arr_target[$sub_value]);
            }
          }
        } else {
          // This is just a string so continue removing
          self::remove_data_by_array_keymap($value, $arr_source[$key]);
        }
      } elseif (is_string($value)) {
        // Value check if the value is a string
        unset($arr_source[$value]);
      }
    }

  }


  /**
   * These are examples of valid keymaps
   * * "id"               will remove id key from the array
   * * "id,email"         will remove id and email from the array
   * * "user:id"          will remove id key from user key
   * * "user:id,email"    will remove id and email keys from user key
   * * "user:orders[id,status]" will remove id and status from a order collection under user key
   * * "user:orders[details:id]" will remove id from details child class which is a part of orders collection under user key 
   * * "[id,email]" will remove id and email keys from the array of arrays
   * IMPORTANT: If targeting subarrays, they must be at the last place in the keymap string
   */
  public static function keymap_to_array($str_keymap) {

    // Initiate array
    $arr_keymap = [];
    $arr_reference =& $arr_keymap;

    // We need to extract subarray from the keymap
    list($str_before_subarray, $str_subarray) = self::extract_subarray_from_keymap($str_keymap);

    // Once split, we want to check if we have a string before a subarray
    if ($str_before_subarray) {
      // Check if we have multiple keys
      $arr_before_subarray_parts = explode(':', $str_before_subarray);

      // Initiate counter, ta capture last element
      $cnt = 0;

      foreach ($arr_before_subarray_parts as $str_before_subarray_part) {

        // We only support multiple arguments if they sit as the last argument
        if ($cnt == count($arr_before_subarray_parts) - 1) {

          // This is the last element. We want to check if we are targeting multiple attributes
          $arr_attributes = explode(',', $str_before_subarray_part);

          // Check if we have subarray
          if ($str_subarray) {
            $arr_reference[$str_before_subarray_part] = [];
            $arr_reference =& $arr_reference[$str_before_subarray_part];
          } else {
            foreach ($arr_attributes as $str_attribute) {
              $arr_reference[] = $str_attribute;
            }            
          }

        } else {
          // Add sttribute as a key of new array and reassign reference
          $arr_reference[$str_before_subarray_part] = [];
          $arr_reference =& $arr_reference[$str_before_subarray_part];
        }

        $cnt++;
      }
    }

    // Handle subarrays if any and add them to the last 
    if ($str_subarray) {
      // Repeat the process but pass current reference to the method
      $arr_reference[] = self::keymap_to_array($str_subarray);
    }

    return $arr_keymap;
  }


  /**
   * Checks if the given string contains subarray, which is represented by [...]
   * @param string $str_keymap representing a keymap
   * @return boolean true if contains subarray, false otherwise
   */
  protected static function does_keymap_contain_subarray($str_keymap) {
    return preg_match("/\[.*\]/", $str_keymap);
  }


  protected function extract_subarray_from_keymap($str_keymap) {

    // Check if the keymap contains subarray
    if (self::does_keymap_contain_subarray($str_keymap)) {
      // We need to extract everything that is before the subarray and after it
      $str_before_subarray = substr($str_keymap, 0, strpos($str_keymap, "["));
      $str_subarray = substr($str_keymap, strpos($str_keymap, "[") + 1, strlen($str_keymap) - strpos($str_keymap, "[") - 2);
    } else {
      // No subarrays found
      $str_before_subarray = $str_keymap;
      $str_subarray = null;
    }

    return [
        $str_before_subarray = strlen($str_before_subarray) == 0 ? null : $str_before_subarray,
        $str_subarray = strlen($str_subarray) == 0 ? null : $str_subarray
      ];
  }

  /**
   * Splice is a combination of get and remove methods
   * 
   * @param string $str_keymap representing keymap
   * @param mixed[] $arr_source 
   * @return mixed representing the data inside the given keymap, null otherwise
   */
  public static function splice_data($str_keymap, array &$arr_source) {
    $return_data = self::get_data($str_keymap, $arr_source);
    self::remove_data($str_keymap, $arr_source);
    return $return_data;
  }

  /**
   * Method groups multiple arrays by an array map.
   * 
   * @param mixed[] $arr_source Array containing array of arrays to be grouped
   * @param mixed[] $arr_map containing map of how the array supposed to be grouped 
   * @return mixed[] representing grouped array
   * @todo describe map structure
   */
  public static function group_by_map(array $arr_source, array $arr_map) {

    // Create grouped array
    $arr_grouped = [];
    
    // Iterate over arr_source which is an array of arrays
    foreach($arr_source as $arr_record) {

      // Run grouping
      self::group_by_map_add_data($arr_record, $arr_grouped, $arr_map);

    }

    return $arr_grouped;
  }

  /**
   * Method is used by group_by_map method to group the data by the array map
   * 
   * @param mixed[] $arr_source Array representind source data
   * @param mixed[] $arr_grouped Array referncing array holding grouped data
   * @param mixed[] $arr_map Array  representing the grouping map
   */
  private static function group_by_map_add_data(array $arr_source, array &$arr_grouped, array $arr_map) {

    // Loop through the map to see values
    foreach($arr_map as $map_key => $map_value) {

      // Check the type of the key and values
      if(is_string($map_key)) {
        // First, let's check if the $map_key is a grouping key
        if(preg_match("/^\:.*/", $map_key)) {
          // We are dealing with a grouping key. Check if it exists inside the grouped array
          $str_grouping_key = preg_replace("/^\:/", "", $map_key);
          if(!array_key_exists($arr_source[$str_grouping_key], $arr_grouped)) {
            $arr_grouped[$arr_source[$str_grouping_key]] = [];
          }

          // Rerun grouping
          self::group_by_map_add_data($arr_source, $arr_grouped[$arr_source[$str_grouping_key]], $map_value);

        } else {
          // Not a grouping key
          if(!array_key_exists($map_key, $arr_grouped)) {
            $arr_grouped[$map_key] = [];
          }

          // Rerun grouping
          self::group_by_map_add_data($arr_source, $arr_grouped[$map_key], $map_value);
        }

      } elseif(is_string($map_value)) {

        // Standard value. We also need to check if the translation is set
        $arr_key_parts = explode("|", $map_value);
        if(count($arr_key_parts) == 2) {
          // We need to translate the key
          $str_new_key = $arr_key_parts[1];
        } else {
          $str_new_key = $map_value;
        }

        $arr_grouped[$str_new_key] = $arr_source[$arr_key_parts[0]];
      
      } elseif(is_int($map_key)) {
        // We just need to push to the array as no specific key has been specified
        $arr_grouped[] = [];
        $arr_subgroup =& $arr_grouped[count($arr_grouped)-1];

        // Rerun grouping
        self::group_by_map_add_data($arr_source, $arr_subgroup, $map_value);

      } else {        
        // Unsupported type passed. Raise an exception
        throw new Error\InvalidArgumentError("Map can only contain string or array value. Got " . gettype($map_value));
      }

    }  
  }

}