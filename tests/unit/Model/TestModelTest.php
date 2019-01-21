<?php

use PHubyTest\Model\TestModel;
use PHubyTest\Model\TestModelCollection;
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

  public function setUp() {
    parent::setUp();
    $this->obj_tm = new TestModel();
  }

  public function testInstantiation() {
    foreach($this->example_test_model_data as $key => $value) {
      switch($key) {
        case 'datetime':
          $this->assertInstanceOf("\PHuby\Attribute\DateTimeAttr", $this->obj_tm->get_attr($key));
          $this->assertEquals(null, $this->obj_tm->get_attr($key)->to_db_format());          
          break;
        case 'string_with_options':
          $this->assertInstanceOf("\PHuby\Attribute\StringAttr", $this->obj_tm->get_attr($key));
          $this->assertEquals("default", $this->obj_tm->get_attr($key)->to_db_format());          
          break;
        default:
          $this->assertInstanceOf("\PHuby\Attribute\\".ucfirst($key)."Attr", $this->obj_tm->get_attr($key));
          $this->assertEquals(null, $this->obj_tm->get_attr($key)->to_db_format());
          break;
      }
    }
  }

  public function testMagicMethods() {
    $this->obj_tm->populate_attributes($this->example_test_model_data);
    $this->assertEquals($this->obj_tm->token, $this->example_test_model_data['token']);
    $this->assertEquals($this->obj_tm->email, $this->example_test_model_data['email']);
    $this->obj_tm->email = 'test@asd.com';
    $this->assertEquals($this->obj_tm->email, 'test@asd.com');    
  }

  public function testPopulateAttributes() {

    $this->obj_tm->populate_attributes($this->example_test_model_data);

    foreach($this->example_test_model_data as $key => $value) {
      switch($key) {
        case 'datetime':
          $this->assertInstanceOf("\PHuby\Attribute\DateTimeAttr", $this->obj_tm->get_attr($key));
          $this->assertEquals($value, $this->obj_tm->get_attr($key)->to_db_format());
          break;
        case 'string_with_options':
          $this->assertInstanceOf("\PHuby\Attribute\StringAttr", $this->obj_tm->get_attr($key));
          $this->assertEquals($value, $this->obj_tm->get_attr($key)->to_db_format());          
          break;
        default:
          $this->assertInstanceOf("\PHuby\Attribute\\".ucfirst($key)."Attr", $this->obj_tm->get_attr($key));
          $this->assertEquals($value, $this->obj_tm->get_attr($key)->to_db_format());
          break;
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

    // We also want to make sure we set location
    $str_assets_location = __DIR__."/../../_support/assets";
    $this->obj_tm->get_attr('image')->set_location($str_assets_location);
    $this->assertEquals("image_01.jpg", $this->obj_tm->image->get());
    $this->assertTrue($this->obj_tm->image->exists());
    $this->obj_tm->get_attr('file')->set_location($str_assets_location);
    $this->assertTrue($this->obj_tm->get_attr('file')->exists());

    // Test if we can copy an image
    $str_copy_image_filepath = $str_assets_location . DIRECTORY_SEPARATOR . "image_01_copy.jpg";
    $obj_copied_image = $this->obj_tm->get_attr('image')->copy($str_copy_image_filepath);

    $this->assertEquals("image_01_copy.jpg", $obj_copied_image->to_db_format());
    $this->assertEquals($str_assets_location, $obj_copied_image->get_location());
    $this->assertTrue(FileUtils::exists($str_copy_image_filepath));

    // If everything is ok, delete the file
    $this->assertTrue($obj_copied_image->exists());
    $this->assertTrue($obj_copied_image->delete());
    $this->assertFalse(FileUtils::exists($str_copy_image_filepath));

    // Check if we can change the value
    $this->assertEquals($this->example_test_model_data['string'], $this->obj_tm->get_attr('string')->to_db_format());
    $this->obj_tm->set_attr('string', 'test');
    $this->assertEquals('test', $this->obj_tm->get_attr('string')->to_db_format());
    $this->obj_tm->set_attr('string', $this->example_test_model_data['string']);
    $this->assertEquals($this->example_test_model_data['string'], $this->obj_tm->get_attr('string')->to_db_format());
  }

  public function testPopulateAttributesWithCollection() {
    $arr_data_with_collection = $this->example_test_model_data;
    $arr_data_with_collection["collection"] = [
      $arr_data_with_collection
    ];

    $this->obj_tm->populate_attributes($arr_data_with_collection);

    $this->assertInstanceOf(TestModelCollection::class, $this->obj_tm->collection);
    $this->assertInstanceOf(TestModel::class, $this->obj_tm->collection->get_collection()[0]);

    foreach($this->example_test_model_data as $key => $value) {
      switch($key) {
        case 'datetime':
          $this->assertInstanceOf("\PHuby\Attribute\DateTimeAttr", $this->obj_tm->get_attr('collection')->get_collection()[0]->get_attr($key));
          $this->assertEquals($value, $this->obj_tm->get_attr('collection')->get_collection()[0]->get_attr($key)->to_db_format());        
          break;
        case 'string_with_options':
          $this->assertInstanceOf("\PHuby\Attribute\StringAttr", $this->obj_tm->get_attr('collection')->get_collection()[0]->get_attr($key));
          $this->assertEquals($value, $this->obj_tm->get_attr('collection')->get_collection()[0]->get_attr($key)->to_db_format());        
          break;
        default:
          $this->assertInstanceOf("\PHuby\Attribute\\".ucfirst($key)."Attr", $this->obj_tm->get_attr('collection')->get_collection()[0]->get_attr($key));
          $this->assertEquals($value, $this->obj_tm->get_attr('collection')->get_collection()[0]->get_attr($key)->to_db_format()); 
          break;
      }
    }
  }

  public function testMultiplePopulation() {
    $this->obj_tm = new TestModel();

    // Add nested model
    $arr_nested_data = $this->example_test_model_data;
    $arr_nested_data['nested_model'] = $this->example_test_model_data;

    $this->obj_tm->populate_attributes($arr_nested_data);

    $this->assertInstanceOf(TestModel::class, $this->obj_tm->get_attr('nested_model'));

    $this->assertEquals($arr_nested_data['nested_model']['email'], $this->obj_tm->get_attr('nested_model')->get_attr('email')->get());
    $this->assertEquals($arr_nested_data['nested_model']['boolean'], $this->obj_tm->get_attr('nested_model')->get_attr('boolean')->to_db_format());

    // Repopulate data
    $this->obj_tm->populate_attributes([
        "nested_model" => [
          "boolean" => 0
        ]
      ]);

    // Check if we can still validate email
    $this->assertEquals($arr_nested_data['nested_model']['email'], $this->obj_tm->get_attr('nested_model')->get_attr('email')->get());
  }

  public function testGetFlatData() {
    $this->obj_tm->populate_attributes($this->example_test_model_data);
    $this->assertEquals(
        $this->example_test_model_data,
        $this->obj_tm->get_flat_data()
      );

    $arr_example_data = $this->example_test_model_data;
    unset($arr_example_data['int']);
    $this->assertEquals(
        $arr_example_data,
        $this->obj_tm->get_flat_data("exclude:int")
      );
  }


}