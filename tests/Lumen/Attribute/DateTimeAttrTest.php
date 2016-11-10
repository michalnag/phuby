<?php

require_once __DIR__ . "/../../../lib/autoload.php";
require_once __DIR__ . "/../../../vendor/autoload.php";
require_once __DIR__ . "/AttributeTestInterface.php";

use PHPUnit\Framework\TestCase;
use Lumen\Attribute\DateTimeAttr;

class DateTimeAttrTest extends TestCase implements AttributeTestInterface {

  public function __construct() {
    $this->dateTimeAttr = new DateTimeAttr();
  }

  public function test_instance() {
    $this->assertInstanceOf('Lumen\Attribute\DateTimeAttr', $this->dateTimeAttr);
  }

  public function test_set() {

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