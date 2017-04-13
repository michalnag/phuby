<?php
/**
 * DateTimeValidator
 * 
 * @author Michal Nagielski <michal.nagielski@gmail.com>
 * @package PHuby\Helpers\Validator
 */

namespace PHuby\Helpers\Validator;

use PHuby\Helpers\AbstractValidator;
use PHuby\Helpers\Validator\StringValidator;
use PHuby\Error\ValidationError;

class DateTimeValidator extends AbstractValidator implements ValidatorInterface {
  
  const ALLOWED_VALIDATION_OPTIONS = [

  ];

  const VALID_DATETIME_ARGUMENTS = [
    'description' => ['yesterday', 'today', 'now', 'tomorrow'],
    'regex' => [
      "/^[0-9]{4}\-[0-9]{2}\-[0-9]{2}\s[0-9]{2}\:[0-9]{2}\:[0-9]{2}$/",
      "/^[0-9]{4}\-[0-9]{2}\-[0-9]{2}\s[0-9]{2}\:[0-9]{2}$/",
      "/^[0-9]{4}\/[0-9]{2}\/[0-9]{2}\s[0-9]{2}\:[0-9]{2}(\:[0-9]{2})?$/",
      "/^[0-9]{4}\-[0-9]{2}\-[0-9]{2}$/"
    ]
  ];

  public static function is_valid($value, $args = []) {
    
    // Check if the value passed to the method is valid datetime argument
    if(StringValidator::is_valid($value)) {
      
      // We know that this is a string
      // Check if value is inside valid datetime arguments
      if(in_array($value, self::VALID_DATETIME_ARGUMENTS['description'])) {
        // Argument is inside the array of allowed date time descriptions
        return true;
      }

      // If we are here we need to check if the value passed for the validation matches one of the regex
      foreach(self::VALID_DATETIME_ARGUMENTS['regex'] as $regex) {
        if(preg_match($regex, $value)) {
          return true;
        }
      }

      // If we are her, none, it looks like although we have received a string value, it is not valid argument
      self::add_validation_error('invalid_argument', new ValidationError("Unsupported argument $value passed to the DateTimeValidator"));
      return false;

    } else {
      self::add_validation_error('invalid_value_type', new ValidationError(__CLASS__ . " expects string type value for validation. Got " . gettype($value)));
      return false;
    }
  }

}