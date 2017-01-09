<?php

namespace PHuby\Helpers\Utils;

use PHuby\Helpers\AbstractUtils;
use PHuby\Error\FileError;

class YAMLUtils extends AbstractUtils implements FileTypeInterface {

  const ALLOWED_EXTENSIONS = ['yaml', 'yml'];

  static function check_extension($filename) {
    return FileUtils::check_file_extension($filename, self::ALLOWED_EXTENSIONS);
  }

  /**
   * This method reads the yaml file specified in
   * @param string $filepath contains the path to the yaml file
   * @return object with data  
   * @throws Error\FileError 
   */
  static function read($filepath) {
    // Check if we are dealing with the YAML file first
    if(self::check_extension(FileUtils::get_filename_from_full_path($filepath))) {
      
      // Get the data out from the file
      $data = yaml_parse(file_get_contents($filepath));

         
    } else {
      // Throw an exception if non json file is passed to the method
      throw new FileError('Specified file is not a json file. File: ' . $filepath);
    }
  }

}