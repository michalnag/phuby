<?php
/**
 * Interface for FileTypeUtils
 * 
 * @author Michal Nagielski <michal.nagielski@gmail.com>
 * @package PHuby\Helpers\Utils
 */

namespace PHuby\Helpers\Utils;

interface FileTypeInterface {
   static function check_extension($filename);
}