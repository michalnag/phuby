<?php
/**
 * JSONUtils
 * 
 * @author Michal Nagielski <michal.nagielski@gmail.com>
 * @package PHuby\Helpers\Utils
 */

namespace PHuby\Helpers\Utils;

use PHuby\Helpers\AbstractUtils;
use PHuby\Error;

class JSONUtils extends AbstractUtils implements FileTypeInterface {

  const ALLOWED_EXTENSIONS = ['json'];

  static function check_extension($filename) {
    return FileUtils::check_file_extension($filename, self::ALLOWED_EXTENSIONS);
  }

  /**
   * Validates JSON 
   * 
   * @param string $str_json
   */
  static function validate($str_json, $bol_as_array = false) {
      
    $data = json_decode($str_json, $bol_as_array);

    // Check if any errors occured during the file reading process
    switch (json_last_error()) {
      case JSON_ERROR_NONE:
        return $data;
        break;
      case JSON_ERROR_DEPTH:
        throw new Error\InvalidArgumentError('Maximum stack depth exceeded.' . $str_json);
        break;
      case JSON_ERROR_STATE_MISMATCH:
        throw new Error\InvalidArgumentError('Underflow or the modes mismatch.' . $str_json);
        break;
      case JSON_ERROR_CTRL_CHAR:
        throw new Error\InvalidArgumentError('Unexpected control character found.' . $str_json);
        break;
      case JSON_ERROR_SYNTAX:
        throw new Error\InvalidArgumentError('Syntax error, malformed JSON.' . $str_json);
        break;
      case JSON_ERROR_UTF8:
        throw new Error\InvalidArgumentError('Malformed UTF-8 characters, possibly incorrectly encoded.' . $str_json);
        break;
      default:
        throw new Error\InvalidArgumentError('Unknown error.' . $str_json);
        break;
    }   
  }

  /**
   * This method reads the json file specified in
   * @param string $filepath contains the path to the json file
   * @return object with data  
   * @throws Error\FileError 
   */
  static function read($filepath) {
    // Check if we are dealing with the JSON file first
    if(self::check_extension(FileUtils::get_filename_from_full_path($filepath))) {
      
      // Get the data out from the file
      $file_content = file_get_contents($filepath);

      try {
        return self::validate($file_content);        
      } catch (Error\InvalidArgumentError $e) {
        throw new Error\FileError($e->getMessage() . ' File: ' . $filepath);
      }

    } else {
      // Throw an exception if non json file is passed to the method
      throw new FileError('Specified file is not a json file. File: ' . $filepath);
    }
  }

}