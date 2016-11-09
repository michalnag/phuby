<?php

namespace Lumen\Attribute;

class PasswordAttr extends AbstractAttribute implements AttributeInterface {

  const HASHING_ALGORYTHM = PASSWORD_DEFAULT;

  public function set($password_hash) {
    /** @todo check is the hash is correct length */
    $this->attr_value = $password_hash;
    return true;   
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