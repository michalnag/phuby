<?php

namespace Lumen\Helpers\Validator;

interface ValidatorInterface {

  public static function is_valid($value, $args);
  
}