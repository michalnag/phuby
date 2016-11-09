<?php

namespace Attribute;

use AbstractAttribute;
use Helpers\Validator\IntegerValidator;
use Error\InvalidAttributeError;

class IDAttr extends AbstractAttribute implements AttributeInterface {

  public function set($var) {
    
    if(!IntegerValidator::is_valid($var)) {
      throw new InvalidAttributeError(IntegerValidator::get_first_validation_error()->getMessage());
    }

    $this->id = intval($var);
    return true;

  }

  /**
   * Method gets the attribute value as stored inside protced $id attribute
   * @return int|null representing value of the protected $id attribute
   */
  public function get() {
    return $this->id;
  }
  
  public function to_db_format() {
    return $this->to_int();
  }

  public function to_int() {
    return (int) $this->id;
  }

}