<?php

use PHuby\Attribute\DateTimeAttr;

class DateTimeAttrTest extends TestCase {

  public function setUp() {
    $this->dateTimeAttr = new DateTimeAttr();
  }

  public function testInstantiation() {
    $this->assertInstanceOf('PHuby\Attribute\DateTimeAttr', $this->dateTimeAttr);
  }

  public function testSet() {

    foreach (["2016-12-12", "2016-12-12 00:00:00", 1481500800,
        new \DateTime("2016-12-12 00:00:00"), new DateTimeAttr("2016-12-12 00:00:00")] as $value) {
      $this->dateTimeAttr->set($value);
      $this->assertEquals("2016-12-12 00:00:00", $this->dateTimeAttr->to_db_format());
      $this->assertEquals("2016-12-12 00:00:00", $this->dateTimeAttr->__toString());
      $this->assertInstanceOf('DateTime', $this->dateTimeAttr->get());
    }

    foreach (["qweqwe", "vqweqwev", new \stdCLass(), 12.12] as $value) {
      $exception_caught = false;
      try {
        $this->dateTimeAttr->set($value);        
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