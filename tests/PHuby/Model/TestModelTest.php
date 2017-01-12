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
    "boolean" => 1
  ];

  public function __construct() {
    Config::set_config_root(__DIR__."/../../config.d");
    $this->obj_tm = new TestModel();
  }

  public function test_instantiation() {
    foreach($this->example_test_model_data as $key => $value) {
      if($key == 'datetime') {
        $this->assertInstanceOf("\PHuby\Attribute\DateTimeAttr", $this->obj_tm->$key);
        $this->assertEquals(null, $this->obj_tm->$key->to_db_format());
      } else {
        $this->assertInstanceOf("\PHuby\Attribute\\".ucfirst($key)."Attr", $this->obj_tm->$key);
        $this->assertEquals(null, $this->obj_tm->$key->to_db_format());     
      }
    }
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


  public function test_populate_attributes_with_collection() {
    $arr_data_with_collection = $this->example_test_model_data;
    $arr_data_with_collection["collection"] = [
      $arr_data_with_collection
    ];

    $this->obj_tm->populate_attributes($arr_data_with_collection);

    $this->assertInstanceOf("\Model\TestModelCollection", $this->obj_tm->collection);
    $this->assertInstanceOf("\Model\TestModel", $this->obj_tm->collection->collection[0]);

    foreach($this->example_test_model_data as $key => $value) {
      if($key == 'datetime') {
        $this->assertInstanceOf("\PHuby\Attribute\DateTimeAttr", $this->obj_tm->collection->collection[0]->$key);
        $this->assertEquals($value, $this->obj_tm->collection->collection[0]->$key->to_db_format());
      } else {
        $this->assertInstanceOf("\PHuby\Attribute\\".ucfirst($key)."Attr", $this->obj_tm->collection->collection[0]->$key);
        $this->assertEquals($value, $this->obj_tm->collection->collection[0]->$key->to_db_format());     
      }
    }
  }

}