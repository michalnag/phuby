<?php

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
  public static function keys_exist(Array $arr_keys, Array $arr_haystack) {
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
  public static function add_data($str_keymap, Array &$arr_source, $data) {

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
  public static function get_data($str_keymap, Array $arr_source) {
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
   * Method removes the relevant key from the array based on the keymap
   * 
   * @param string $str_keymap representing keymap
   * @param mixed[] $arr_source 
   * @return boolean true if data has been removed, false otherwise
   */
  public static function remove_data($str_keymap, Array &$arr_source) {
    // Deal with the keymap
    $arr_keys = explode(":", $str_keymap, 2);

    $bol_return = false;

    if(array_key_exists($arr_keys[0], $arr_source)) {
      if(count($arr_keys) == 1) {
        unset($arr_source[$arr_keys[0]]); 
        $bol_return = true;
      } else {
        $bol_return = self::remove_data($arr_keys[1], $arr_source[$arr_keys[0]]);
      }
    }

    return $bol_return;
  }

  /**
   * Splice is a combination of get and remove methods
   * 
   * @param string $str_keymap representing keymap
   * @param mixed[] $arr_source 
   * @return mixed representing the data inside the given keymap, null otherwise
   */
  public static function splice_data($str_keymap, Array &$arr_source) {
    $return_data = self::get_data($str_keymap, $arr_source);
    self::remove_data($str_keymap, $arr_source);
    return $return_data;
  }

  /**
   * Method groups multiple arrays by an array map
   * 
   * @param mixed[] $arr_source Array containing array of arrays to be grouped
   * @param mixed[] $arr_map containing map of how the array supposed to be grouped 
   * @return mixed[] representing grouped array
   */
  public static function group_by_map(Array $arr_source, Array $arr_map) {
    // Create grouped array
    $arr_grouped = [];
    
    // Iterate over arr_source which is an array of arrays
    foreach($arr_source as $arr_record) {

      // Each $arr_record is representing single record with key => value structure.
      // This is what will be grouped. First thing we need to do is to
      // create an array which key is a value of the main group key
      if(!array_key_exists($arr_record[$str_group_key], $arr_grouped)){
        $arr_grouped[$arr_record[$str_group_key]] = [];
      }

      // We have created an array so we can now build a data into it recursively
      self::group_by_map_add_data($arr_record, $arr_grouped[$arr_record[$str_group_key]], $arr_map); 

      // This is what is called a first level data
      //self::group_by_map_first_level_data($arr_map, $arr_record, $arr_grouped, $arr_record[$str_group_key]);

      // Run nesting logic
      //self::group_by_map_nesting($arr_map, $arr_record, $arr_grouped[$arr_record[$str_group_key]]);


    }

    return $arr_grouped;
  
  }

  private static function group_by_map_add_data(Array $arr_source, Array &$arr_grouped, Array $arr_map) {
    //error_log("GROUPED".json_encode($arr_grouped));
    //error_log("MAP". json_encode($arr_map));
    
    // Loop through the map to see values
    foreach($arr_map as $map_key => $map_value) {
      // Now for each entry in the map we want to check whether the value is an array or a string
      if(is_string($map_value)) {
      
        // String value - just add to array by creating another key
        $arr_grouped[$map_value] = $arr_source[$map_value];
      
      } elseif(is_array($map_value)) {
        // This is an array so we perform nesting

        // Before this though, we want to check if the nesting is for subarrays or grouped arrays
        // with specified key

        if(is_int($map_key)) {

          // If we have an integer value, it is possible that we have a subgroup
          // In order to check it, we need to compare at least one value from subarrays
          // Check if any subarrays have already been created
          if($map_key == 0) {
            $arr_grouped[] = [];
            $arr_subgroup =& $arr_grouped[count($arr_grouped)-1];
          } else {
            if(!array_key_exists($map_key, $arr_grouped)) {
              $arr_grouped[$map_key] = [];            
            }
            $arr_subgroup =& $arr_grouped[$map_key];
          }


          // Reference the last element of the array
          // Push an array to grouped
          
          error_log("MAP KEY $map_key and MAP: " . json_encode($arr_map[$map_key]));
          error_log("SUBGROUP: " . json_encode($arr_subgroup));
          self::group_by_map_add_data($arr_source, $arr_subgroup, $arr_map[$map_key]);
        } else {

          // The key is not an integer, so we want to check if we want to just push the array
          // or group it.
          $arr_key_parts = explode(':', $map_key);
          if(count($arr_key_parts) == 2) {
            // We have grouping by, so we will search for the relevant key to reference it
            // Make sure that the correct key exists and create a subarray if needed
            if(!array_key_exists($arr_key_parts[0], $arr_grouped)) {
              $arr_grouped[$arr_key_parts[0]] = [];            
            }

            // Once we are sure it is there, we want to access it
            if(!array_key_exists($arr_source[$arr_key_parts[1]], $arr_grouped[$arr_key_parts[0]])) {
              $arr_grouped[$arr_key_parts[0]][$arr_source[$arr_key_parts[1]]] = [];
            }

            // Re run grouping
            self::group_by_map_add_data($arr_source, $arr_grouped[$arr_key_parts[0]][$arr_source[$arr_key_parts[1]]], $arr_map[$map_key]);

          } else {

            // Simply create another array and push the data into it
            // This is grouped array so create respective key
            if(!array_key_exists($map_key, $arr_grouped)) {
              $arr_grouped[$map_key] = [];            
            }
            self::group_by_map_add_data($arr_source, $arr_grouped[$map_key], $arr_map[$map_key]);            
          }

        }
      
      } else {
        
        // Unsupported type passed. Raise an exception
        throw new Error\InvalidArgumentError("Map can only contain string or array value. Got " . gettype($map_value));
        
      }
    }
  }


}