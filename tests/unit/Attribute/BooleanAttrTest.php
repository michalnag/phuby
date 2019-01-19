<?php

use PHuby\Attribute\BooleanAttr;

class BooleanAttrTest extends TestCase {

  public function setUp() {
    parent::setUp();
    $this->booleanAttr = new BooleanAttr();
  }

  public function testInstantiation() {
    $this->assertInstanceOf(BooleanAttr::class, $this->booleanAttr);
  }

  public function testSet() {

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
  
  public function testGet() {
    $this->markTestIncomplete();
  }
  
  public function testToDBFormat() {
    $this->markTestIncomplete();
  }

  public function testToString() {
    $this->markTestIncomplete();
  }

}