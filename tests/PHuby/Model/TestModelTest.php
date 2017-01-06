<?php

require_once __DIR__ . "/../../../lib/autoload.php";
require_once __DIR__ . "/../../../vendor/autoload.php";
require_once __DIR__ . "/../../../tests/lib/autoload.php";

use PHPUnit\Framework\TestCase;
use Model\TestModel;
use PHuby\Config;

class TestModelTest extends TestCase {
  
  private $example_test_model_data = [
    "int" => 1,
    "datetime" => "2016-12-12 12:12:12",
    "email" => "test@test.com",
    "string" => "asdfghjk",
    "password" => '$2y$10$2CFEq2lryIaYkd7M1X0o7ebFxrdpU1H5rPElBifCxPEX/NWb35MVG',
    "token" => "aaaaaabbbbbb",
    "boolean" => 1,
    "text" => "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam maximus egestas tellus id tempor. Cras mollis varius tempor. Maecenas id ante justo. Morbi aliquet elit vel accumsan feugiat. Aliquam hendrerit rhoncus metus a faucibus. Nam a malesuada purus. Donec convallis sapien vel nibh placerat, id porttitor lorem placerat."
  ];

  public function __construct() {
    $this->obj_tm = new TestModel();
    Config::set_config_root(__DIR__."/../../config.d");
  }

  public function test_populate_attributes() {

    $this->obj_tm->populate_attributes($this->example_test_model_data);

    foreach($this->example_test_model_data as $key => $value) {
      if($key == 'datetime') {
        $this->assertInstanceOf("\PHuby\Attribute\DateTimeAttr", $this->obj_tm->$key);
        $this->assertEquals($value, $this->obj_tm->$key->to_db_format());
      } else {
        $this->assertInstanceOf("\PHuby\Attribute\\".ucfirst($key)."Attr", $this->obj_tm->$key);
        $this->assertEquals($value, $this->obj_tm->$key->to_db_format());     
      }
    }
  
    // We also need to test custom values
    $this->obj_tm->populate_attributes([
        "string_with_options" => "asdqweasdqwe"
      ]);

    // And test failures
    try {
      $this->obj_tm->populate_attributes([
          "string_with_options" => "asaqwe"
        ]);      
    } catch(\PHuby\Error\InvalidAttributeError $e) {
      $this->assertTrue(true);
    }


    

  }

}