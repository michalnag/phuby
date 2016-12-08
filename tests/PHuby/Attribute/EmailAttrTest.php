<?php

require_once __DIR__ . "/../../../lib/autoload.php";
require_once __DIR__ . "/../../../vendor/autoload.php";
require_once __DIR__ . "/AttributeTestInterface.php";

use PHPUnit\Framework\TestCase;
use PHuby\Attribute\EmailAttr;

class EmailAttrTest extends TestCase implements AttributeTestInterface {

  public function __construct() {
    $this->emailAttr = new EmailAttr();
  }

  public function test_instance() {
    $this->assertInstanceOf('PHuby\Attribute\EmailAttr', $this->emailAttr);
  }

  public function test_set() {

    // Test valid options
    foreach(["mn@mn.com", "test.asd@domain.co.uk", "m_trev+1@test.org", "m-m_987+12@98dom.com", new EmailAttr("test@gmail.com")] as $value) {
      $this->assertTrue($this->emailAttr->set($value));
      $this->assertEquals($value, $this->emailAttr->__toString());
    }

    // Test invalid options
    foreach(["mnasdas", 123, 12.23, new stdClass()] as $value) {
      $exception_caught = false;
      try {
        $this->emailAttr->set($value);
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