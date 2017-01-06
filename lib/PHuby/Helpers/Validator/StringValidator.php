<?php

namespace PHuby\Helpers\Validator;

use PHuby\Helpers\AbstractValidator;
use PHuby\Error\ValidationError;

class StringValidator extends AbstractValidator implements ValidatorInterface {
  
  const ALLOWED_VALIDATION_OPTIONS = [
    'length'        => 'array',
    'allow_spaces'  => 'boolean',
    'regex'         => 'string',
    'allow_null'    => 'boolean'
  ];

  public static function is_valid($value, $options = []) {
    // Reset validation errors
    self::reset_validation_errors();

    // Check if the $value is a type of string
    if(is_string($value)) {

      // Check if any options have been passed to the method
      if(!empty($options)) {

        // Some options have been passed. Run validation per method
        foreach($options as $option => $data) {
          // Check if the option is supported
          if(self::is_validation_option_supported($option)) {

            // Validation is supported. Check if the calidation data is the correct type
            if(gettype($options[$option]) == self::ALLOWED_VALIDATION_OPTIONS[$option]) {

              // Correct type has been passed. Perform validation
              $method_name = "validate_{$option}";
              self::$method_name($value, $data);

            } else {

              // Wrong validation option type is passed
              self::add_validation_error(
                'invalid_validation_option', 
                new ValidationError("Validation option {$option} must be a type of " . self::ALLOWED_VALIDATION_OPTIONS[$option] . ". Got " . gettype($options[$option]))
              );
            }
            
          } else {
           
            // Validation option is not supported
            self::add_validation_error('unsupported_validation_option', new ValidationError("Unsupported validation option {$option}"));
          }
        }
      } else {
        
        // No options passed and this is already a string.
        return true;
      }
      
      // All filters have been applied. See if we have any errors and return boolean
      return !self::has_validation_errors();

    } else {
      // Non-string value passed to the method
      self::add_validation_error('type', new ValidationError("Value passed for string validation is a type of " . gettype($value) . ". Must be type of string"));
      return false;
    }
  }

  protected static function validate_allow_spaces($string, $allow_spaces) {
    // @todo
    return true;
  }

  protected static function validate_regex($string, $regex) {
    return preg_match($regex, $string, $matches) == 1;
  }

  protected static function validate_length($string, Array $options) {
    if(array_key_exists('exact', $options)) {
      // We check exact value
      $is_exact_value = (strlen($string) == intval($options['exact']));
      if($is_exact_value) {
        return true;
      } else {
        self::add_validation_error('invalid_length', new ValidationError("Passed string must be " . $options['exact'] . " long but it is " . strlen($string)));
        return false;
      }

    } elseif(array_key_exists('min', $options)) {

      // Check if we need to check maximum length as well
      if(array_key_exists('max', $options)) {

        // Check if the length is within specified range
        return (strlen($string) >= $options['min']) && (strlen($string) <= $options['max']);
      } else {

        // Check minimum length only
        return strlen($string) >= $options['min'];
      }

    } elseif(array_key_exists('max', $options)) {
      // Check maxiumum length only
      return strlen($string) <= $options['max'];
    }
  }

}