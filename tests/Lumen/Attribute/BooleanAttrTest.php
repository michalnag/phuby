<?php

require_once __DIR__ . "/../../../lib/autoload.php";
require_once __DIR__ . "/../../../vendor/autoload.php";
require_once __DIR__ . "/AttributeTestInterface.php";

use PHPUnit\Framework\TestCase;
use Lumen\Attribute\BooleanAttr;

class BooleanAttrTest extends TestCase implements AttributeTestInterface {

  public function __construct() {
    $this->booleanAttr = new BooleanAttr();
  }

  public function test_instance() {
    $this->assertInstanceOf('Lumen\Attribute\BooleanAttr', $this->booleanAttr);
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

    $invalid_values = [2, 3, "test", new stdClass];



  }
  
  public function test_get() {}
  
  public function test_to_db_format() {}

  public function test_toString() {}

}