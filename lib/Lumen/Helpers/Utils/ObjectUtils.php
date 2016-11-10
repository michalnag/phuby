<?php

namespace Lumen\Helpers\Utils;

use Lumen\Helpers\AbstractUtils;
use Lumen\Error;

class ObjectUtils extends AbstractUtils {
 
  public static function check_required_attributes($object, Array $attributes, $attribute_type = "dynamic") {
    
    switch($attribute_type) {
      case "dynamic":
        foreach($attributes as $attribute) {
          if(!isset($object->$attribute)) {
            throw new Error\MissingAttributeError("Attribute $attribute is not set on the object " . get_class($object));
          }
        }
        break;
        
    }

    return true;

  }

}