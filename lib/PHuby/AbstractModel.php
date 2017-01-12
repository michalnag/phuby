<?php

namespace PHuby;

use PHuby\Helpers\Utils\ObjectUtils;
use PHuby\Logger;

abstract class AbstractModel implements BaseModelInterface {

  public function __construct() {
    ObjectUtils::create_attributes($this);
  }

  public function populate_attributes($attributes) {
    return ObjectUtils::populate_attributes($this, $attributes);
  }

  public function is_attribute_allowed($attribute) {
    return ObjectUtils::is_attribute_allowed($this, $attribute);
  }

  public function is_attribute_a_child_class($attribute) {
    return ObjectUtils::is_attribute_a_child_class($this, $attribute);
  }
}