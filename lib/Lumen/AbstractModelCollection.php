<?php

namespace Lumen;

use Lumen\Logger;
use Lumen\Error;
use Lumen\Helpers\Utils\ObjectUtils;

abstract class AbstractModelCollection extends AbstractModel implements BaseModelInterface {

  public $collection = [];

  public function populate_collection(Array $array) {
    // Collection method expects an array of arrays so let's check if the right format has been passed
    foreach ($array as $data) {
      // Create an instance of the Collectable class
      $class_name = $this::COLLECT_CLASS['name'];
      $this->collection[] = ObjectUtils::populate_attributes(new $class_name(), $data);
    }
  }

}