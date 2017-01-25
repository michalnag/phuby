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
   * Method groups multiple arrays by an array map.
   * 
   * @param mixed[] $arr_source Array containing array of arrays to be grouped
   * @param mixed[] $arr_map containing map of how the array supposed to be grouped 
   * @return mixed[] representing grouped array
   * @todo describe map structure
   */
  public static function group_by_map(Array $arr_source, Array $arr_map) {

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
  private static function group_by_map_add_data(Array $arr_source, Array &$arr_grouped, Array $arr_map) {

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
        // Standard value. Add data from the source
        $arr_grouped[$map_value] = $arr_source[$map_value];
      
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