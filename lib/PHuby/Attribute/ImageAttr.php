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

class ImageAttr extends BaseFileAttr {

  public function resize() {}

  public function crop() {}

}