<?php
/**
 * EmailValidator
 * 
 * @author Michal Nagielski <michal.nagielski@gmail.com>
 * @package PHuby\Helpers\Validator
 */

namespace PHuby\Helpers\Validator;

use PHuby\Helpers\AbstractValidator;
use PHuby\Helpers\Validator\StringValidator;
use PHuby\Error\ValidationError;

class EmailValidator extends AbstractValidator implements ValidatorInterface {
  
  protected static $allowed_email_characters_regex = "/^[a-zA-Z0-9\.@\+]*$/";

  public static function is_valid($email, $args = []) {
    // Check if the string is valid

    if(StringValidator::is_valid($email, ['regex' => self::$allowed_email_characters_regex])) {

      // String appears to be valid. Continue with validation process
      if(!filter_var($email, FILTER_VALIDATE_EMAIL) === false) {

        // This is valid email address
        return true;        
      } else {

        // Looks like the email has not passed PHP filtering
        self::add_validation_error('invalid_email_address', new ValidationError("Invalid email address $email"));
        return false;
      }

    } else {

      // String does not match regex
      self::add_validation_error('invalid_argument_type', new ValidationError("Email must be a type of string. Got " . gettype($email)));
      return false;
    }
  }
}