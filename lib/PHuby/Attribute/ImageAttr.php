<?php
/**
 * @author Michal Nagielski <michal.nagielski@gmail.com>
 * @package PHuby
 * 
 * Class representing File Attribute
 * It's main value is set as \PHuby\Model\File\Image
 */

namespace PHuby\Attribute;

use PHuby\AbstractAttribute;
use PHuby\Attribute\AttributeInterface;
use PHuby\Error\InvalidAttributeError;

class ImageAttr extends AbstractAttribute implements AttributeInterface {


  /**
   * Sets the absolute filepath as an attribute value
   * 
   * @param string $str_filepath containing an absolute path to the file
   */
  public function set($str_filepath) {

  }

  public function get() {

  }

  public function to_db_format() {

  }

  public function __toString() {

  }


}