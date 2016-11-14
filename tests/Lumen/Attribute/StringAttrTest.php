<?php

require_once __DIR__ . "/../../../lib/autoload.php";
require_once __DIR__ . "/../../../vendor/autoload.php";
require_once __DIR__ . "/AttributeTestInterface.php";

use PHPUnit\Framework\TestCase;
use Lumen\Attribute\StringAttr;

class StringAttrTest extends TestCase implements AttributeTestInterface {

  public function __construct() {
    $this->stringAttr = new StringAttr();
  }

  public function test_instance() {
    $this->assertInstanceOf('Lumen\Attribute\StringAttr', $this->stringAttr);
  }

  public function test_set() {

    foreach(["asdasd", new StringAttr("asdasd")] as $value) {
      $this->assertTrue($this->stringAttr->set($value));
      $this->assertEquals("asdasd", $this->stringAttr->get());
    }

    foreach(["eqweqw", " ", "", "aoamksdo 34567faweq~Adsfad fa"] as $value) {
      $this->assertTrue($this->stringAttr->set($value));
      $this->assertEquals($value, $this->stringAttr->get());
    }

    foreach([12, 12.12, true, new stdClass()] as $value) {
      $exception_caught = false;
      try {
        $this->stringAttr->set($value);
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