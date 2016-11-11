<?php

require_once __DIR__ . "/../../../lib/autoload.php";
require_once __DIR__ . "/../../../vendor/autoload.php";
require_once __DIR__ . "/AttributeTestInterface.php";

use PHPUnit\Framework\TestCase;
use Lumen\Attribute\IntAttr;

class IntAttrTest extends TestCase implements AttributeTestInterface {

  public function __construct() {
    $this->intAttr = new IntAttr();
  }

  public function test_instance() {
    $this->assertInstanceOf('Lumen\Attribute\IntAttr', $this->intAttr);
  }

  public function test_set() {

    foreach([1, 2, 0, -123, new IntAttr(12)] as $value) {
      $this->assertTrue($this->intAttr->set($value));
    }

    foreach(["ASD", 12.12, new stdClass()] as $value) {
      $exception_caught = false;
      try {
        $this->intAttr->set($value);
      } catch(Lumen\Error\InvalidAttributeError $e) {
        $exception_caught = true;
      }
      $this->assertTrue($exception_caught);
    }

  }
  
  public function test_get() {}
  
  public function test_to_db_format() {}

  public function test_toString() {}

}