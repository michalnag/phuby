<?php

namespace PHuby\Helpers\Utils;

use PHuby\Helpers\AbstractUtils;
use PHuby\Error;
use PHuby\Logger;

class ArrayUtils extends AbstractUtils {   

  public static function check_multiple_keys(Array $arr_keys, Array $arr_haystack) {
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
   * 
   */
  public static function add_to_array($str_keymap, Array &$arr_source, $data) {

    $arr_keys = explode(":", $str_keymap, 2);

    if(count($arr_keys) == 1) {
      // This is the last key
      if($str_keymap == "[]") {
        $arr_source[] = $data;
      } else {
        if(!array_key_exists($str_keymap, $arr_source)) {
          $arr_source[$str_keymap] = $data;          
        } else {
          // Duplicated key. Should we overwrite?
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
      self::add_to_array($arr_keys[1], $arr_source[$arr_keys[0]], $data);
    }

    return $arr_source;

  }


}