<?php

namespace Helpers;

abstract class AbstractValidator {

  protected static $validation_errors = [];

  public static function has_validation_errors() {
    return !empty(static::$validation_errors);
  }

  protected static function is_validation_option_supported($option) {
    return in_array($option, array_keys(static::ALLOWED_VALIDATION_OPTIONS));
  }

  protected static function reset_validation_errors() {
    static::$validation_errors = [];
    return true;
  }

  protected static function add_validation_error($type, $error) {
    static::$validation_errors[$type] = $error;
    return true;
  }

  public static function get_validation_errors() {
    return static::$validation_errors;
  }

  public static function get_first_validation_error() {
    return empty(self::$validation_errors) ? [] : array_shift(static::$validation_errors);
  }


}