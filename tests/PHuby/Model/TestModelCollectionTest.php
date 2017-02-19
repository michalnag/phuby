<?php

require_once __DIR__ . "/../../../lib/autoload.php";
require_once __DIR__ . "/../../../vendor/autoload.php";
require_once __DIR__ . "/../../../tests/lib/autoload.php";

use PHPUnit\Framework\TestCase;
use Model\TestModel;
use Model\TestModelCollection;
use PHuby\Config;
use PHuby\Helpers\Utils\FileUtils;

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

  public function __construct() {
    Config::set_config_root(__DIR__."/../../config.d");
    $this->obj_tmc = new TestModelCollection();
  }

  public function test_instantiation() {
    $this->obj_tmc->populate_collection($this->example_data);
    foreach ($this->obj_tmc->get_collection() as $obj_test_model) {
      $this->assertInstanceOf("\Model\TestModel", $obj_test_model);
    }
  }

  public function test_get_flat_data() {
    $this->obj_tmc->populate_collection($this->example_data);
    $this->assertEquals(
        $this->example_data,
        $this->obj_tmc->get_flat_data()
      );
  }

}