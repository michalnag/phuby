<?php
/**
 * Interface for validators
 * 
 * @author Michal Nagielski <michal.nagielski@gmail.com>
 * @package PHuby\Helpers\Validator
 */

namespace PHuby\Helpers\Validator;

interface ValidatorInterface {

  public static function is_valid($value, $args);
  
}