<?php

use PHuby\Attribute\FloatAttr;

class FloatAttrTest extends TestCase implements AttributeTestInterface {

  public function setUp() {
    $this->FloatAttr = new FloatAttr();
  }

  public function test_instance() {
    $this->assertInstanceOf('PHuby\Attribute\FloatAttr', $this->FloatAttr);
  }

  public function test_set() {

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
  
  public function test_get() {}
  
  public function test_to_db_format() {}

  public function test_toString() {}

}