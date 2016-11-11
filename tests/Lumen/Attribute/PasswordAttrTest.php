<?php

require_once __DIR__ . "/../../../lib/autoload.php";
require_once __DIR__ . "/../../../vendor/autoload.php";
require_once __DIR__ . "/AttributeTestInterface.php";

use PHPUnit\Framework\TestCase;
use Lumen\Attribute\PasswordAttr;

class PasswordAttrTest extends TestCase implements AttributeTestInterface {

  public function __construct() {
    $this->passwordAttr = new PasswordAttr();
  }

  public function test_instance() {
    $this->assertInstanceOf('Lumen\Attribute\PasswordAttr', $this->passwordAttr);
  }

  public function test_set() {
    $example_hash = "\$2y\$10\$To1mEE22Aomjw3gmvTkvB.RA2.x72kb4R7lGocnZ3uEMufvRTev4i";
    $this->assertTrue($this->passwordAttr->set($example_hash));
    $this->assertTrue($this->passwordAttr->set(PasswordAttr::hash_password("asdasd")));
    $this->assertTrue($this->passwordAttr->verify("asdasd"));
    $this->assertTrue($this->passwordAttr->set(new PasswordAttr(PasswordAttr::hash_password("test"))));

    foreach(["qweqweqwe", 12, 12.12, new stdClass()] as $value) {
      $exception_caught = false;
      try {
        $this->passwordAttr->set($value);
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