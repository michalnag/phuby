<?php

use PHuby\Attribute\BooleanAttr;

class BooleanAttrTest extends TestCase implements AttributeTestInterface {

  public function setUp() {
    $this->booleanAttr = new BooleanAttr();
  }

  public function test_instance() {
    $this->assertInstanceOf('PHuby\Attribute\BooleanAttr', $this->booleanAttr);
  }

  public function test_set() {

    // Test valid values
    foreach([1, true, new BooleanAttr(true)] as $value) {
      $this->booleanAttr->set($value);
      $this->assertTrue($this->booleanAttr->get());
      $this->assertEquals(1, $this->booleanAttr->to_int());
    };

    foreach([0, false, new BooleanAttr(false)] as $value) {
      $this->booleanAttr->set($value);
      $this->assertFalse($this->booleanAttr->get());
      $this->assertEquals(0, $this->booleanAttr->to_int());   
    };

    foreach([2, 3, "test", new stdClass] as $value) {
      $exception_caught = false;
      try {
        $this->booleanAttr->set($value);
      } catch(PHuby\Error\InvalidAttributeError $e) {
        $exception_caught = true;
      }
      $this->assertTrue($exception_caught);
    };

  }
  
  public function test_get() {}
  
  public function test_to_db_format() {}

  public function test_toString() {}

}