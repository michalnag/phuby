<?php

use PHuby\Attribute\StringAttr;

class StringAttrTest extends TestCase {

  public function setUp() {
    $this->stringAttr = new StringAttr();
  }

  public function test_instance() {
    $this->assertInstanceOf('PHuby\Attribute\StringAttr', $this->stringAttr);
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
      } catch(PHuby\Error\InvalidAttributeError $e) {
        $exception_caught = true;
      }
      $this->assertTrue($exception_caught);
    }

  }
  
  public function test_get() {}
  
  public function test_to_db_format() {}

  public function test_toString() {}

}