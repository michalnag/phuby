<?php

namespace PHuby;

use PHuby\Error\FileError;
use PHuby\Error\MissingConfigError;
use PHuby\Helpers\Utils\FilesUtils;
use PHuby\Helpers\Utils\JSONUtils;

class Config {

  const DS = DIRECTORY_SEPARATOR;

  public static $data;
  private static 
      $config_files,
      $relative_config_root = "/../config.d",
      $config_root;

  public static function set_config_root($config_root) {
    self::$config_root = $config_root;
  }

  public static function set_config(Config $config = null) {
    // Check if the config data is set already 
    if(self::$data) {
      return true;
    } else {
      // Config has no data set yet.
      // Check if the config class has been passed to the method
      if($config) {
        self::$data = $config::$data;
      } else {
        // Get data from config.d json files
        self::get_configuration_files();
      }
    }
  }


  /**
   * Method retrieves the config data from $data attribute
   *
   * @param string $group String representing position in the data, where : represents sub-level, for example log:default
   * @return stdClass object containing desired config
   * @throws Error\MissingConfigError if config group is not found
   */
  public static function get_data($group) {

    // Make sure that the config is set
    self::set_config();

    // Prepare an argument to be split into groups
    $group_parts = explode(':', $group);

    // Iterate over the group parts to find the desired config
    $return_config = self::$data;

    for($i = 0; $i < count($group_parts); $i++) {
      if(property_exists($return_config, $group_parts[$i])) {
        $return_config = $return_config->{$group_parts[$i]};          
      } else {        
        // Missing config - throw an exception
        throw new MissingConfigError("Requested config group $group cannot be found");
      }
    }

    // Return config value
    return $return_config;   

  }

  /**
   * This method fetches all configuration files found in the specified directory
   * and converts their content into the object that will be stored inside $config
   * attribute on the object instance. 
   * @throws Error\FileError if file found in the directory is not a JSON file 
   * @return bool true if process completes successfully
   */
  private static function get_configuration_files() {
    // Fetch all configuration files from the specified directory
    self::$config_files = FilesUtils::fetch_files_from_dir(self::$config_root);

    // Loop through all found files and add them to the config array
    foreach(self::$config_files as $config_file) {

      // Check if this is a json file and if so, add it to the config as an array
      if(JSONUtils::check_extension($config_file)) {
        $config_name = FilesUtils::get_file_name_without_extension($config_file);
        if(!is_object(self::$data)) {
          // $data attribute needs to be converted to the object
          self::$data = new \stdClass(); 
        }        
        self::$data->$config_name = JSONUtils::read(self::$config_root.self::DS.$config_file);
      } else {

        // found configuration in the format different then JSON. 
        /** @todo - does it really need to hrow an error? */ 
        throw new FileError("Config file is not a JSON file");
      }
    }

    return true;
  }

}