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
   * @param mixed[] $arr_data Array containing array of arrays to be grouped
   * @param mixed[] $arr_map containing map of how the array supposed to be grouped 
   * @param string $str_group_key representing main key that is used for core grouping (optional)
   * @return mixed[] representing grouped array
   */
  public static function group_by_map(Array $arr_data, Array $arr_map, $str_group_key = null) {
    // Create grouped array
    $arr_grouped = [];
    
    // Iterate over arr_data which is an array of arrays
    foreach($arr_data as $arr_record) {

      // First, check if group key is specified
      if($str_group_key) {
        // And now check if the key already exists
        if(array_key_exists($arr_record[$str_group_key], $arr_grouped)) {
          // Grouping key already exists
          // TODO
        } else {
          // Grouping key does not exist. Create a new one in grouped array
          $arr_grouped[$arr_record[$str_group_key]] = [];

          // Once created, ne need to add first level data
          self::group_by_map_first_level_data($arr_map, $arr_record, $arr_grouped[$arr_record[$str_group_key]]);
        } 
      }

      // Run nesting logic
      self::group_by_map_nesting($arr_map, $arr_record, $arr_grouped[$arr_record[$str_group_key]]);

    }

    return $arr_grouped;
  
  }

  private static function group_by_map_first_level_data($arr_map, &$arr_record, &$arr_current_group) {
    foreach($arr_map as $key =>$value) {
      if(!is_array($value)) {
        // Add the singular key to the array.
        $arr_current_group[$value] = $arr_record[$value];
      } else {
        // If the value is an array, we simply create an array with the corresponding key
        $arr_current_group[$key] = [];
      }
    }
  }

  private static function group_by_map_nesting($arr_map, &$arr_record, &$arr_current_group) {
    foreach($arr_map as $key => $value) {
      if(is_array($value)) {
        $arr_subgroup = [];
        foreach($value as $sub_key => $sub_value) {
          if(!is_array($sub_value)) {
            // Standard key
            $arr_subgroup[$sub_value] = $arr_record[$sub_value];            
          } else {
            // We are nesting array
            self::group_by_map_first_level_data($arr_map[$key], $arr_record, $arr_current_group[$key]);
          }
        }
        $arr_current_group[$key][] = $arr_subgroup;
      } 
    }
  }



}