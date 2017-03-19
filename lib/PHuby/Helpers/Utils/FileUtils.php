<?php
/**
 * FileUtils
 * 
 * @author Michal Nagielski <michal.nagielski@gmail.com>
 * @package PHuby\Helpers\Utils
 */

namespace PHuby\Helpers\Utils;

use PHuby\Helpers\AbstractUtils;
use PHuby\Error\FileError;
use PHuby\Error\DuplicatedFileError;
use PHuby\Logger;

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

  const 
    DS = DIRECTORY_SEPARATOR;

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
  public static function get_filename_without_extension($str_filename) {
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
    // We also want to lowercase the extenstion
    return self::get_filename_without_extension(end($filepath_parts)) . '.' . self::get_file_extension($str_filepath);
  }

  /**
   * Method retrieves the location from the absolute filepath
   * 
   * @param string $str_filepath holding an absolute path with an extension
   * @return string representing the location
   */
  public static function get_location_from_full_path($str_filepath) {
    return substr($str_filepath, 0, strrpos($str_filepath, DIRECTORY_SEPARATOR));
  }

  /**
   * Method simply copies the file to the destination path
   * 
   * * options["overwrite"]   boolean will overwrite the file if exists
   * 
   * @param string $str_source representing source filepath
   * @param string $str_destination representing destination filepath
   * @param options[] $arr_options optional array with options
   * @throws \PHuby\Error\FileError if file cannot be copied
   * @throws \PHuby\Error\DuplicatedFileError if file already exists and overwrite flag is not set to true
   * @return boolean true if file is copied succesfully
   */
  public static function copy($str_source, $str_destination, $arr_options = null) {
    Logger::debug("Attempting to copy $str_source to $str_destination");
    // Before we attempt to sopy the file, we need to check if the source file exists
    if(self::is_readable($str_source)) {
      // Now check if the destination file exists
      if(!self::exists($str_destination)) {
        if(copy($str_source, $str_destination)) {
          return true;
        } else {
          throw new FileError("Unable to copy file from $str_source to $str_destination");
        }
      } else {
        // File exists. See if we want to overwrite item
        if($arr_options && array_key_exists("overwrite", $arr_options) && $arr_options["overwrite"]) {
          if(copy($str_source, $str_destination)) {
            return true;
          } else {
            throw new FileError("Unable to copy file from $str_source to $str_destination");
          }    
        } else {
          throw new DuplicatedFileError("File $str_destination already exists");
        }
      }
    } else {
      throw new FileError("Unable to copy $str_source - file is not readable.");
    }
  }
  /**
   * @todo
   */
  public static function move($str_source, $str_destination, array $arr_options = []) {
    return rename($str_source, $str_destination);
  }


  /**
   * Method simply removes the file
   * 
   * @param string $str_source representing an abosulte path to the file
   * @throws \PHuby\Error\FileError is file cannot be removed
   * @throws \PHuby\Error\FileNotFoundError is file is not found
   * @return boolean true if file is succesfully deleted
   */
  public static function delete($str_source) {
    // Check if the file exists
    if(self::exists($str_source)) {
      if(unlink($str_source)) {
        return true;
      } else {
        throw new FileError("Unable to remove file $str_source.");
      }

    } else {
      throw new FileNotFoundError("File $str_source does not exist. Unable to remove.");
    }
  }

  /**
   * Adds suffix to the filename
   * 
   * @param string $str_filename representing the file name with extension
   * @param strin $str_suffix representing suffix to be added
   */
  public static function add_suffix($str_filename, $str_suffix) {
    return
      self::get_filename_without_extension($str_filename)
      . $str_suffix . "."
      . self::get_file_extension($str_filename);
  }

  /**
   * Method deals with file upload of a file
   * 
   * @param string $str_tmp_name representing temporary name of the file
   * @param string $str_destination representing an absolute path for the upload including
   *  filename
   * @return boolean true if file has been uploaded sucessfully, false otherwise
   * @todo - improve logic of this method
   */
  public static function upload($str_tmp_name, $str_destination) {
    return move_uploaded_file($str_tmp_name, $str_destination);
  }

}