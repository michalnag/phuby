<?php
/**
 * @author Michal Nagielski <michal.nagielski@gmail.com>
 * @package PHuby
 * 
 * Class representing File Attribute
 * It's main value is set as \PHuby\Model\File\Image
 */

namespace PHuby\Attribute;

use PHuby\Attribute\BaseFileAttr;
use PHuby\Error\InvalidAttributeError;
use PHuby\Helpers\Utils\FileUtils;
use PHuby\Helpers\Utils\ImageUtils;

class ImageAttr extends BaseFileAttr {

  public function resize($int_max_width, $int_max_height, $int_quality = 100) {
    if($this->exists()) {
      return ImageUtils::resize([
          "image_path" => $this->get_filepath(),
          "max_width" => $int_max_width,
          "max_height" => $int_max_height,
          "quality" => $int_quality
        ]);
    } else {
      return false;
    }
  }

  public function crop() {}

}