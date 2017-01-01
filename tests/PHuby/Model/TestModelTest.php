<?php

require_once __DIR__ . "/../../../lib/autoload.php";
require_once __DIR__ . "/../../../vendor/autoload.php";
require_once __DIR__ . "/../../../tests/lib/autoload.php";

use PHPUnit\Framework\TestCase;
use Model\TestModel;
use PHuby\Config;

class TestModelTest extends TestCase {
  
  public function __construct() {
    $this->obj_tm = new TestModel();
    Config::set_config_root(__DIR__."/../../config.d");
  }

  public function test_populate_attributes() {
    $data = [
      "id" => 1,
      "dtm_added" => "2016-12-12 12:12:12"
    ];

    $this->obj_tm->populate_attributes($data);
    $this->assertInstanceOf('\PHuby\Attribute\IntAttr', $this->obj_tm->id);
    $this->assertEquals(1, $this->obj_tm->id->get());
    $this->assertInstanceOf('\PHuby\Attribute\DateTimeAttr', $this->obj_tm->dtm_added);
    $this->assertEquals('2016-12-12 12:12:12', $this->obj_tm->dtm_added->to_db_format());
  }

  public function test_populate_with_collection() {
    $data = [
      "id" => 1,
      "dtm_added" => "2016-12-12 12:12:12",
      "collection" => [
        [
          "id" => 2,
          "dtm_added" => "2016-12-14 12:12:12",
        ],
        [
          "id" => 3,
          "dtm_added" => "2016-12-15 12:12:12",
        ]
      ]
    ];  

    $this->obj_tm->populate_attributes($data);  
  }



}