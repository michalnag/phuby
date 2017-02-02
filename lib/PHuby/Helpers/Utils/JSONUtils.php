<?php
/**
 * JSONUtils
 * 
 * @author Michal Nagielski <michal.nagielski@gmail.com>
 * @package PHuby\Helpers\Utils
 */

namespace PHuby\Helpers\Utils;

use PHuby\Helpers\AbstractUtils;
use PHuby\Error\FileError;

class JSONUtils extends AbstractUtils implements FileTypeInterface {

  const ALLOWED_EXTENSIONS = ['json'];

  static function check_extension($filename) {
    return FileUtils::check_file_extension($filename, self::ALLOWED_EXTENSIONS);
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
      $data = json_decode(file_get_contents($filepath));

      // Check if any errors occured during the file reading process
      switch (json_last_error()) {
        case JSON_ERROR_NONE:
          return $data;
          break;
        case JSON_ERROR_DEPTH:
          throw new FileError('Maximum stack depth exceeded. File: ' . $filepath);
          break;
        case JSON_ERROR_STATE_MISMATCH:
          throw new FileError('Underflow or the modes mismatch. File: ' . $filepath);
          break;
        case JSON_ERROR_CTRL_CHAR:
          throw new FileError('Unexpected control character found. File: ' . $filepath);
          break;
        case JSON_ERROR_SYNTAX:
          throw new FileError('Syntax error, malformed JSON. File: ' . $filepath);
          break;
        case JSON_ERROR_UTF8:
          throw new FileError('Malformed UTF-8 characters, possibly incorrectly encoded. File: ' . $filepath);
          break;
        default:
          throw new FileError('Unknown error. File: ' . $filepath);
          break;
      }    
    } else {
      // Throw an exception if non json file is passed to the method
      throw new FileError('Specified file is not a json file. File: ' . $filepath);
    }
  }

}