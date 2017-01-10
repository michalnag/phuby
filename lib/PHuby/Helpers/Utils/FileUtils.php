<?php
/**
 * @author Michal Nagielski <michal.nagielski@gmail.com>
 * @package PHuby
 * 
 * Class holding helper methods related to files
 */

namespace PHuby\Helpers\Utils;

use PHuby\Helpers\AbstractUtils;

class FileUtils extends AbstractUtils {

  const 
    NO_UPLOAD_ERR = 0,
    UPLOAD_ERR_TOO_LARGE_PARAM = 1,
    UPLOAD_ERR_TOO_LARGE_SERVER = 2,
    UPLOAD_ERR_TOO_PARTIAL_UPLOAD = 3,
    UPLOAD_ERR_NO_FILE_SELECTED = 4,
    UPLOAD_ERR_NO_TEMP_DIR = 6,
    UPLOAD_ERR_CANNOT_WRITE_TO_DISC = 7,
    UPLOAD_ERR_STOPPED_BY_EXTENSION = 8;

  /**
   * Method fetches all files from given directory
   * 
   * @param string $str_dir representing an absolute path to the directory
   * @return mixed[] Array containing files located inside given directory
   * @return null if no files have been found in the directory
   */
  public static function fetch_files_from_dir($str_dir) {

    // Scan directory
    if(is_dir($str_dir)) {
      $directory_content = scandir($str_dir);
      if($directory_content) {
        $files = array_filter($directory_content, function($item) use ($str_dir) {
          return is_file($str_dir.DIRECTORY_SEPARATOR.$item);
        });
        return empty($files) ? null : $files; 
      } else {
        return null;
      }
    } else {
      throw new \Exception("Passed argument $str_dir is not a directory");
    }

  }

  /**
   * Method checks if the file exists
   * 
   * @param string $str_filepath Filepath to the file
   * @return boolean true if file exists, false otherwise
   */
  public static function exists($str_filepath) {
    return file_exists($str_filepath);
  }

  /**
   * Method checks if the file is readable
   * 
   * @param string $str_filepath Filepath to the file
   * @return boolean true if file is readable, false otherwise
   */
  public static function is_readable($str_filepath) {
    return is_readable($str_filepath);
  }

  /**
   * This method checks the file extension of the filename given
   * @param string $filename containing full filename 
   * @param string[] $extensions Array containing allowed extensions
   * @return bool true|false if filename is a type of any extension
   */
  public static function check_file_extension($filename, array $extensions) {
    return in_array(self::get_file_extension($filename), $extensions);
  }

  /**
   * Method retrieves file extension from the filename
   * 
   * @param string $str_filename containing filename
   * @return string extension of the file
   */
  public static function get_file_extension($str_filename) {
    $filename_parts = explode('.', $str_filename);
    return strtolower(end($filename_parts));
  }

  /**
   * Methods gets the filename without the extension
   * 
   * @param string $str_filename containing the filename
   * @return string filename without the extension (or whatever comes before last dot)
   */
  public static function get_file_name_without_extension($str_filename) {
    return substr($str_filename, 0, strrpos($str_filename, '.'));
  }

  /**
   * Method retrieves the filename with extension from the absolute filepath
   * 
   * @param string $str_filepath holding an absolute path with an extension
   * @return string representing the filename
   */
  public static function get_filename_from_full_path($str_filepath) {
    $filepath_parts = explode(DIRECTORY_SEPARATOR, $str_filepath);
    return end($filepath_parts);
  }

  /**
   * Method simply copies the file to the destination path
   * 
   * @param string $str_source representing source filepath
   * @param string $str_destination representing destination filepath
   */
  public static function copy($str_source, $str_destination) {
    // Before we attempt to sopy the file, we need to check if the source file exists
    if(self::is_readable($str_source)) {
      // Now check if the destination file exists
      if(!self::exists($str_destination)) {

      } else {
        // File exists. See if we want to overwrite it
        
      }
    } else {
      throw new FileError("Unable to copy $str_source - file is not readable.");
    }
  }
  /**
   * @todo
   */
  public static function move() {}

}