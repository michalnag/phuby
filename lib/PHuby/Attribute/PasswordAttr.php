<?php

namespace PHuby\Attribute;

use PHuby\AbstractAttribute;
use PHuby\Helpers\Validator\StringValidator;
use PHuby\Error\InvalidAttributeError;

class PasswordAttr extends AbstractAttribute implements AttributeInterface {

  const
    HASHING_ALGORYTHM = PASSWORD_DEFAULT,
    HASH_LENGTH = 60;


  public function set($value) {
    // Hash passed to the setter must be as Long as what's set on constant
    if(is_string($value)) {
      if(StringValidator::is_valid($value, ['length' => ['exact' => self::HASH_LENGTH]])) {
        $this->attr_value = $value;
        return true;
      } else {
        throw new InvalidAttributeError("Provided password hash $value is invalid");
      }
    } elseif(is_object($value) && $value instanceof PasswordAttr) {
      $this->attr_value = $value->get();
      return true;   
    } else {
      throw new InvalidAttributeError("Password attribute accepts string only. Got ".gettype($value));
    }
  }

  public function verify($password) {
    return password_verify($password, $this->attr_value);
  }

  public function get() {
    return $this->attr_value;
  }

  public function to_db_format() {
    return $this->attr_value;
  }

  public static function hash_password($password_string) {
    return password_hash($password_string, self::HASHING_ALGORYTHM);
  }

}