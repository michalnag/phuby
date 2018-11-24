<?php
/**
 * DirUtils class to sit in the base of each attribute
 * 
 * @author Michal Nagielski <michal.nagielski@gmail.com>
 * @package PHuby\Helpers\Utils
 */

namespace PHuby\Helpers\Utils;

use PHuby\Helpers\AbstractUtils;

class DirUtils extends AbstractUtils {

  /**
   * @param string $path representing path of the directory to check
   * @return boolean
   */
  public static function exists($path) {
    return file_exists($path);
  }

  /**
   * @param string $path representing path of the directory to check
   * @return boolean
   */
  public static function writable($path) {
    return is_writable($path);
  }

  /**
   * @param string $path representing path of the directory to check
   * @return resource|false
   */
  public static function open($path) {
    if (self::exists($path)) {
      return opendir($path);
    } else {
      return false;
    }
  }

  /**
   * @return string[] representing content of the directory
   */
  public static function get_content($handle) {
    if (is_string($handle)) {
      $handle = self::open($handle);
    }
    $arr_content = [];
    while ($entry = readdir($handle)) {
      $arr_content[] = $entry;
    }
    // Remove current and level up
    $arr_content = array_diff($arr_content, ['.', '..']);
    return $arr_content;
  }

}