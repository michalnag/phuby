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
use PHuby\Traits\SupportsAttributes;
use PHuby\Traits\SupportsFlatData;

abstract class AbstractModel extends AbstractCore {

  use SupportsAttributes;
  use SupportsFlatData;

  const
    CLASS_TYPE = self::CLASS_TYPE_MODEL;

  /**
   * Method initiates attributes
   */
  public function __construct(array $arr_attributes = null) {
    $this->initiate_attributes();
    if ($arr_attributes) {
      $this->populate_attributes($arr_attributes);
    }
  }

  public function __get($str_attr) {
    return $this->get_attr($str_attr);
  }

  public function __set($str_attr, $mix_value) {
    return $this->set_attr($str_attr, $mix_value);
  }

}