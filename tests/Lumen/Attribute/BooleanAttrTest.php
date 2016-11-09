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

  public function test_set() {}
  
  public function test_get() {}
  
  public function test_to_db_format() {}

  public function test_toString() {}

}