<?php
/**
 * AbstractModel sits as a base in every model
 * 
 * @author Michal Nagielski <michal.nagielski@gmail.com>
 * @package PHuby
 */

namespace PHuby;

use PHuby\Helpers\Utils\ObjectUtils;
use PHuby\Logger;
use PHuby\Helpers\Utils\StringUtils;
use PHuby\Error;

abstract class AbstractModel extends AbstractCore {

  const
    CLASS_TYPE = self::CLASS_TYPE_MODEL;

  /**
   * Method initiates attributes
   */
  public function __construct() {
    $this->initiate_attributes();
  }

}