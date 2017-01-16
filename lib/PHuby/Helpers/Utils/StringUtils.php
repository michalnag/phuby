<?php

namespace PHuby\Helpers\Utils;

use PHuby\Helpers\AbstractUtils;

class StringUtils extends AbstractUtils {

  public static function options_string_to_array($str_options) {
    $arr_options = [];

    // First explode based on the pipe
    $arr_options_parts = explode("|", $str_options);

    if(!empty($arr_options_parts)) {
      foreach($arr_options_parts as $str_option) {
        // we expect it to have key and value based on : character
        // Or just single option
        $arr_single_option = explode(":", $str_option);
        if(count($arr_single_option) == 1) {
          // Single option
          $arr_options[] = $str_option;
        } else {
          // Option with parameter. Check if we will be creating an array
          if(substr($arr_single_option[1], 0, 1) == "{") {
            // This is an array. Create one
            // TODO - accomodate nested arrays
            $arr_options[$arr_single_option[0]] = explode(",", substr($arr_single_option[1], 1, strlen($arr_single_option[1]) - 2));
          } else {
            // Standard value
            $arr_options[$str_option] = $arr_single_option[1];
          }
        }
      }
    }

    return $arr_options;
  }

}