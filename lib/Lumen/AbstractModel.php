<?php

namespace Lumen;

use Lumen\Helpers\Utils\ObjectUtils;

abstract class AbstractModel implements BaseModelInterface {

  public function populate_attributes(Array $attributes) {
    return ObjectUtils::populate_attributes($this, $attributes);
  }

  public function is_attribute_allowed($attribute) {
    return ObjectUtils::is_attribute_allowed($this, $attribute);
  }

  public function is_attribute_a_child_class($attribute) {
    return ObjectUtils::is_attribute_a_child_class($this, $attribute);
  }
}