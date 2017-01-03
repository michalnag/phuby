<?php

namespace PHuby;

use PHuby\Logger;
use PHuby\Error;
use PHuby\Helpers\Utils\ObjectUtils;

abstract class AbstractModelCollection extends AbstractModel implements BaseModelInterface {

  public $collection = [];

  public function populate_collection(Array $array) {
    // Collection method expects an array of arrays so let's check if the right format has been passed
    foreach ($array as $data) {
      // Create an instance of the Collectable class
      $class_name = $this::COLLECT_CLASS['name'];
      $collectable_object = new $class_name();
      $collectable_object->populate_attributes($data);
      $this->collection[] = $collectable_object;
    }
  }

}