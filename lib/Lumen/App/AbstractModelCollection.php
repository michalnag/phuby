<?php

namespace App;

use Logger;
use Error;

abstract class AbstractModelCollection extends AbstractModel implements BaseModelInterface {

  public $collection = [];

  public function poulate_attributes(Array $array) {
    // Collection method expects an array of arrays so let's check if the right format has been passed
    foreach ($array as $data) {
      // Create an instance of the Collectable class
      $class_name = $this::COLLECT_CLASS['name'];
      $this->collection[] = (new $class_name())->poulate_attributes($data);
    }
  }

}