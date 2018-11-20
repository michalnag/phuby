<?php
/**
 * IntegerValidator
 * 
 * @author Michal Nagielski <michal.nagielski@gmail.com>
 * @package PHuby\Helpers\Validator
 */

namespace PHuby\Helpers\Validator;
use PHuby\Helpers\AbstractValidator;
use PHuby\Error\ValidationError;

class IntegerValidator extends AbstractValidator implements ValidatorInterface {

  const ALLOWED_VALIDATION_OPTIONS = [
    'length'  => 'array',
    'value'   => 'array'
  ];

  public static function is_valid($value, $args = []) {
    // Check if the value is numeric
    if(is_numeric($value)) {
      // Check if the value is not float
      if(!is_float($value)) {
        // Everything looks ok. Return True
        return true;
      } else {
        self::add_validation_error('invalid_type', new ValidationError("Value passed for validation must be an numberic, but got float."));
      }
    } else {
      self::add_validation_error('invalid_type', new ValidationError("Value passed for validation must be an numberic. Got: " . gettype($value)));
    }

    // If we've got to this point, validation process did not return true.
    return false;
  }

}