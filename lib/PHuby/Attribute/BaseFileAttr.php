<?php
/**
 * Class representing Base File Attribute
 * It's main value refers to the file location as an absolute path
 * 
 * @author Michal Nagielski <michal.nagielski@gmail.com>
 * @package PHuby\Attribute
 */

namespace PHuby\Attribute;

use PHuby\AbstractAttribute;
use PHuby\Attribute\AttributeInterface;
use PHuby\Error\InvalidAttributeError;
use PHuby\Helpers\Utils\FileUtils;

abstract class BaseFileAttr extends AbstractAttribute implements AttributeInterface {

  private 
    $str_location = null;

  /**
   * Construct method by default will assign location if is stored on the 
   * DEFAULT_LOCATION constant. This helps when this attribute class is extended
   * by a specific file attribute.
   */
  public function __construct() {
    if(defined(get_class($this)."::DEFAULT_LOCATION")) {
      $this->set_location($this::DEFAULT_LOCATION);
    }
  }

  public function get() {
    return $this->attr_value;
  }

  /**
   * Sets the absolute filepath as an attribute value
   * 
   * @param string $str_filename containing an absolute path to the file
   */
  public function set($str_filename) {
    $this->attr_value = $str_filename;
    return true;
  }

  public function set_location($str_location) {
    $this->str_location = $str_location;
  }
  
  public function get_location() {
    return $this->str_location;
  }

  public function get_filepath() {
    return $this->get_location() . DIRECTORY_SEPARATOR . $this->get();
  }

  public function to_db_format() {
    return $this->attr_value;
  }

  /**
   * Method checks if the file exists in the given location
   */
  public function exists() {
    return FileUtils::exists($this->get_filepath());
  }

  public function move() {}

  public function delete() {
    return FileUtils::delete($this->get_filepath());
  }

  /**
   * Method creates a copy of the file
   * 
   * @param string $str_filepath new filepath location
   * @return Object representing copied file
   */
  public function copy($str_filepath, $arr_options = null) {
    if(FileUtils::copy($this->get_filepath(), $str_filepath, $arr_options)) {
      $obj_copied_file = clone $this;
      $obj_copied_file->set(FileUtils::get_filename_from_full_path($str_filepath));
      $obj_copied_file->set_location(FileUtils::get_location_from_full_path($str_filepath));
      return $obj_copied_file;
    } else {
      // TODO
    }
  }

  /**
   * Uploads the file to the location specified on the class
   * 
   * @param string $str_tmp_name 
   * @return boolean true if upload is succesfull, false otherwise
   */
  public function upload($str_tmp_name) {
    if($this->get()) {
      return FileUtils::upload($str_tmp_name, $this->get_filepath());      
    } else {
      return false;
    }
  }

}