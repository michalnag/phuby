<?php
/**
 * Image
 * 
 * @author Michal Nagielski <michal.nagielski@gmail.com>
 * @package PHuby\Model\File
 */

namespace PHuby\Model\File;

use PHuby\Model\AbstractFile;

class Image extends AbstractFile {

  public 
    $dimensions = [
      "width" => null,
      "hwight" => null
    ];

  /**
   * Method resizes the picture
   * 
   * @param
   */
  public function resize() {
    
  }

  public function get_orientation() {
  
  }

  public function compress() {

  }

  public function crop() {

  }

}