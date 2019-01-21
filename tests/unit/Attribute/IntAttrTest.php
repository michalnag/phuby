<?php

use PHuby\Attribute\IntAttr;

class IntAttrTest extends TestCase {

  public function setUp() {
    $this->intAttr = new IntAttr();
  }

  public function testInstantiation() {
    $this->assertInstanceOf('PHuby\Attribute\IntAttr', $this->intAttr);
  }

  public function testSet() {

    foreach([1, 2, 0, -123, new IntAttr(12)] as $value) {
      $this->assertTrue($this->intAttr->set($value));
    }

    $this->intAttr->set(0);
    $this->assertEquals(0, $this->intAttr->get());

    foreach(["ASD", 12.12, new stdClass()] as $value) {
      $exception_caught = false;
      try {
        $this->intAttr->set($value);
      } catch(PHuby\Error\InvalidAttributeError $e) {
        $exception_caught = true;
      }
      $this->assertTrue($exception_caught);
    }

  }
  
  public function testGet() {}
  
  public function testToDbFormat() {}

  public function testToString() {}

}