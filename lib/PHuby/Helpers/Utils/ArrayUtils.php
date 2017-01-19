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


}