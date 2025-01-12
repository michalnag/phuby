<?php

use PHuby\Attribute\FloatAttr;

class FloatAttrTest extends TestCase {

  public function setUp() {
    $this->FloatAttr = new FloatAttr();
  }

  public function testInstantiation() {
    $this->assertInstanceOf('PHuby\Attribute\FloatAttr', $this->FloatAttr);
  }

  public function testSet() {

    foreach([1, 2, 0, -123, 12.12, new FloatAttr(1200)] as $value) {
      $this->assertTrue($this->FloatAttr->set($value));
      if(is_object($value)) {
        $this->assertEquals($this->FloatAttr->to_int(), $value->get()*$this->FloatAttr->get_option('conversion_factor'));   
      } else {
        $this->assertEquals($this->FloatAttr->to_int(), $value*$this->FloatAttr->get_option('conversion_factor'));        
      }
    }

    foreach(["ASD", new stdClass()] as $value) {
      $exception_caught = false;
      try {
        $this->FloatAttr->set($value);
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