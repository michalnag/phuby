<?php

require_once __DIR__ . "/../../../lib/autoload.php";
require_once __DIR__ . "/../../../vendor/autoload.php";
require_once __DIR__ . "/../../../tests/lib/autoload.php";

use PHPUnit\Framework\TestCase;
use Model\TestModel;
use Model\TestModelCollection;
use PHuby\Config;
use PHuby\Helpers\Utils\FileUtils;
use PHuby\Helpers\Utils\ArrayUtils;


class TestModelCollectionTest extends TestCase {
  
  private $example_data = [
    [
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
    ],
    [
      "int" => 2,
      "datetime" => "2016-12-12 13:13:13",
      "email" => "test@test2.com",
      "string" => "asdfghjkaaaaaaaaa",
      "password" => '$2y$10$2CFEq2lryIaYkd7M1X0o7ebFxrdpU1H5rPElBifCxPEX/NWb35MVG',
      "token" => "aaabbabccccc",
      "boolean" => 0,
      "image" => "image_02.jpg",
      "file" => "file_02.txt",
      'string_with_options' => "asadasdasd"
    ]
  ];

  private $example_nesting_data = [
    [
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
    ],
    [
      "int" => 2,
      "datetime" => "2016-12-12 13:13:13",
      "email" => "test@test2.com",
      "string" => "asdfghjkaaaaaaaaa",
      "password" => '$2y$10$2CFEq2lryIaYkd7M1X0o7ebFxrdpU1H5rPElBifCxPEX/NWb35MVG',
      "token" => "aaabbabccccc",
      "boolean" => 0,
      "image" => "image_02.jpg",
      "file" => "file_02.txt",
      'string_with_options' => "asadasdasd2"
    ]
  ];

  private $example_update_data = [
    [
      "int" => 1,
      "datetime" => "2016-12-12 12:14:14",
      "email" => "test@test.commm",
      "string" => "asdfghjkmm"
    ],
    [
      "int" => 2,
      "datetime" => "2016-12-12 13:15:15",
      "email" => "test@test2.commm",
      "string" => "asdfghjkaaaaaaaaamm"
    ]
  ];

  public function __construct() {
    Config::set_config_root(__DIR__."/../../config.d");
    $this->obj_tmc = new TestModelCollection();
    // Add collection to nesting model
    $this->example_nesting_data[1]['collection'] = $this->example_data;
    $this->example_nesting_data[0]['collection'] = [];
  }

  public function test_instantiation() {
    $this->obj_tmc->populate_collection($this->example_data);
    foreach ($this->obj_tmc as $obj_test_model) {
      $this->assertInstanceOf("\Model\TestModel", $obj_test_model);
    }
  }

  public function test_get_flat_data() {
    $this->obj_tmc->populate_collection($this->example_data);
    $this->assertEquals(
        $this->example_data,
        $this->obj_tmc->get_flat_data()
      );

    // Another test is to get flat data bot only for desired parameters
    $arr_example_data = [];

    foreach ($this->example_data as $arr_data) {
      $arr_example_data[] = [
        "int" => $arr_data['int'],
        "datetime" => $arr_data['datetime'],
        "email" => $arr_data['email']
      ];
    }

    // Once there is an example data array we can run our test
    $this->assertEquals(
        $arr_example_data,
        $this->obj_tmc->get_flat_data("include:int,datetime,email")
      );

  }

  public function test_get_flat_nested_data() {
    // Test it on nesting as well
    $this->example_nesting_data[1]['collection'][0]['collection'] = [];
    $this->example_nesting_data[1]['collection'][1]['collection'] = [];
    $this->obj_tmc->populate_collection($this->example_nesting_data);
    $this->assertInstanceOf("\Model\TestModelCollection", $this->obj_tmc->get_collection()[1]->get_attr('collection'));
    $this->assertEquals($this->example_nesting_data, $this->obj_tmc->get_flat_data('nesting:true|exclude:nested_model'));
  }

  public function test_update_collection_by_key() {
    $this->obj_tmc->populate_collection($this->example_data);
    // We want to change one attribute value to match the other
    $this->obj_tmc->update_collection_by_key($this->example_update_data, 'int');

    $this->assertEquals(count($this->obj_tmc->get_collection()), 2);

    foreach ($this->example_update_data as $arr_row) {
      $obj_collectable = $this->obj_tmc->get_by_attr('int', $arr_row['int'])[0];
      foreach ($arr_row as $key => $value) {
        $this->assertEquals($obj_collectable->get_attr($key)->to_db_format(), $value);
      }
    }

  }

  public function test_remove_by_attr() {
    $this->obj_tmc->populate_collection($this->example_data);
    $this->assertEquals(2, $this->obj_tmc->get_count());
    // Remove by attribute
    $int_removed = $this->obj_tmc->remove_by_attr('token', 'aaaaaabbbbbb');
    $this->assertEquals(1, $int_removed);
    $this->assertEquals(1, $this->obj_tmc->get_count());
  }

}