<?php

namespace Helpers\Utils;

class FilesUtils {
  
  static function fetch_files_from_dir($dir) {

    // Scan directory
    if(is_dir($dir)) {
      $directory_content = scandir($dir);
      if($directory_content) {
        $files = array_filter($directory_content, function($item) use ($dir) {
          return is_file($dir.DIRECTORY_SEPARATOR.$item);
        });
        return empty($files) ? null : $files; 
      } else {
        return null;
      }
    } else {
      throw new \Exception("passed argument is not a directory");
    }

  }

  static function exists($filepath) {
    return file_exists($filepath);
  }

  static function is_readable($filepath) {
    return is_readable($filepath);
  }

  static function generate_random_string(Array $params = []) {
    $length = 10;
    if(!empty($params)) {
      if(isset($params['length'])) {
        $length = $params['length'];
      }
    }

    return substr(md5(uniqid(rand(),true)), 0, $length);
  }

  /**
   * This method checks the file extension of the filename given
   * @param string $filename containing full filename 
   * @param string[] $extensions Array containing allowed extensions
   * @return bool true|false if filename is a type of any extension
   */
  static function check_file_extension($filename, array $extensions) {
    return in_array(self::get_file_extension($filename), $extensions);
  }

  static function get_file_extension($filename) {
    $filename_parts = explode('.', $filename);
    return strtolower(end($filename_parts));
  }


  static function get_file_name_without_extension($filename) {
    return explode('.', $filename)[0];
  }

  static function get_filename_from_full_path($filepath) {
    $filepath_parts = explode('/', $filepath);
    return end($filepath_parts);
  }


}