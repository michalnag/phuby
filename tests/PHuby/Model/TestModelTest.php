<?php

require_once __DIR__ . "/../../../lib/autoload.php";
require_once __DIR__ . "/../../../vendor/autoload.php";
require_once __DIR__ . "/../../../tests/lib/autoload.php";

use PHPUnit\Framework\TestCase;
use Model\TestModel;
use PHuby\Config;
use PHuby\Helpers\Utils\FileUtils;

class TestModelTest extends TestCase {
  
  private $example_test_model_data = [
    "int" => 1,
    "datetime" => "2016-12-12 12:12:12",
    "email" => "test@test.com",
    "string" => "asdfghjk",
    "password" => '$2y$10$2CFEq2lryIaYkd7M1X0o7ebFxrdpU1H5rPElBifCxPEX/NWb35MVG',
    "token" => "aaaaaabbbbbb",
    "boolean" => 1,
    "image" => "image_01.jpg",
    "file" => "file_01.txt",
    'string_with_options' => "asadasdasd"
  ];

  public function __construct() {
    Config::set_config_root(__DIR__."/../../config.d");
    $this->obj_tm = new TestModel();
  }

  public function test_instantiation() {
    foreach($this->example_test_model_data as $key => $value) {
      switch($key) {
        case 'datetime':
          $this->assertInstanceOf("\PHuby\Attribute\DateTimeAttr", $this->obj_tm->$key);
          $this->assertEquals(null, $this->obj_tm->$key->to_db_format());          
          break;
        case 'string_with_options':
          $this->assertInstanceOf("\PHuby\Attribute\StringAttr", $this->obj_tm->$key);
          $this->assertEquals(null, $this->obj_tm->$key->to_db_format());          
          break;
        default:
          $this->assertInstanceOf("\PHuby\Attribute\\".ucfirst($key)."Attr", $this->obj_tm->$key);
          $this->assertEquals(null, $this->obj_tm->$key->to_db_format());
          break;
      }
    }
  }


  public function test_set_attributes() {

    $this->obj_tm->set_attributes($this->example_test_model_data);

    foreach($this->example_test_model_data as $key => $value) {
      switch($key) {
        case 'datetime':
          $this->assertInstanceOf("\PHuby\Attribute\DateTimeAttr", $this->obj_tm->$key);
          $this->assertEquals($value, $this->obj_tm->$key->to_db_format());
          break;
        case 'string_with_options':
          $this->assertInstanceOf("\PHuby\Attribute\StringAttr", $this->obj_tm->$key);
          $this->assertEquals($value, $this->obj_tm->$key->to_db_format());          
          break;
        default:
          $this->assertInstanceOf("\PHuby\Attribute\\".ucfirst($key)."Attr", $this->obj_tm->$key);
          $this->assertEquals($value, $this->obj_tm->$key->to_db_format());
          break;
      }
    }
  
    // We also need to test custom values
    $this->obj_tm->set_attributes([
        "string_with_options" => "asdqweasdqwe"
      ]);

    // And test failures
    try {
      $this->obj_tm->set_attributes([
          "string_with_options" => "asaqwe"
        ]);      
    } catch(\PHuby\Error\InvalidAttributeError $e) {
      $this->assertTrue(true);
    }

    // We also want to make sure we set location
    $str_assets_location = __DIR__."/../../assets";
    $this->obj_tm->image->set_location($str_assets_location);
    $this->assertEquals("image_01.jpg", $this->obj_tm->image->get());
    $this->assertTrue($this->obj_tm->image->exists());
    $this->obj_tm->file->set_location($str_assets_location);
    $this->assertTrue($this->obj_tm->file->exists());

    // Test if we can copy an image
    $str_copy_image_filepath = $str_assets_location . DIRECTORY_SEPARATOR . "image_01_copy.jpg";
    $obj_copied_image = $this->obj_tm->image->copy($str_copy_image_filepath);

    $this->assertEquals("image_01_copy.jpg", $obj_copied_image->to_db_format());
    $this->assertEquals($str_assets_location, $obj_copied_image->get_location());
    $this->assertTrue(FileUtils::exists($str_copy_image_filepath));

    // If everything is ok, delete the file
    $this->assertTrue($obj_copied_image->exists());
    $this->assertTrue($obj_copied_image->delete());
    $this->assertFalse(FileUtils::exists($str_copy_image_filepath));
  }

  public function test_get_raw_data() {
    $this->obj_tm->set_attributes($this->example_test_model_data);
    $this->assertEquals(
        $this->example_test_model_data,
        $this->obj_tm->get_raw_data()
      );
  }

  public function test_set_attributes_with_collection() {
    $arr_data_with_collection = $this->example_test_model_data;
    $arr_data_with_collection["collection"] = [
      $arr_data_with_collection
    ];

    $this->obj_tm->set_attributes($arr_data_with_collection);

    $this->assertInstanceOf("\Model\TestModelCollection", $this->obj_tm->collection);
    $this->assertInstanceOf("\Model\TestModel", $this->obj_tm->collection->collection[0]);

    foreach($this->example_test_model_data as $key => $value) {
      switch($key) {
        case 'datetime':
          $this->assertInstanceOf("\PHuby\Attribute\DateTimeAttr", $this->obj_tm->collection->collection[0]->$key);
          $this->assertEquals($value, $this->obj_tm->collection->collection[0]->$key->to_db_format());        
          break;
        case 'string_with_options':
          $this->assertInstanceOf("\PHuby\Attribute\StringAttr", $this->obj_tm->collection->collection[0]->$key);
          $this->assertEquals($value, $this->obj_tm->collection->collection[0]->$key->to_db_format());        
          break;
        default:
          $this->assertInstanceOf("\PHuby\Attribute\\".ucfirst($key)."Attr", $this->obj_tm->collection->collection[0]->$key);
          $this->assertEquals($value, $this->obj_tm->collection->collection[0]->$key->to_db_format()); 
          break;
      }
    }
  }


  public function test_multiple_population() {
    $this->obj_tm = new TestModel();

    // Add nested model
    $arr_nested_data = $this->example_test_model_data;
    $arr_nested_data['nested_model'] = $this->example_test_model_data;

    $this->obj_tm->set_attributes($arr_nested_data);

    $this->assertInstanceOf("\Model\TestModel", $this->obj_tm->nested_model);

    $this->assertEquals($arr_nested_data['nested_model']['email'], $this->obj_tm->nested_model->email->get());
    $this->assertEquals($arr_nested_data['nested_model']['boolean'], $this->obj_tm->nested_model->boolean->to_db_format());

    // Repopulate data
    $this->obj_tm->set_attributes([
        "nested_model" => [
          "boolean" => 0
        ]
      ]);

    // Check if we can still validate email
    $this->assertEquals($arr_nested_data['nested_model']['email'], $this->obj_tm->nested_model->email->get());

  }



}