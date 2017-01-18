<?php

require_once __DIR__ . "/../../../../lib/autoload.php";
require_once __DIR__ . "/../../../../vendor/autoload.php";

use PHPUnit\Framework\TestCase;
use PHuby\Helpers\Utils\ArrayUtils;
use PHuby\Config;

class ArrayUtilsTest extends TestCase {

  public function __construct() {
    Config::set_config_root(__DIR__."/../../../config.d");
  }

  public function test_add_data() {
    $arr1 = ["msg" => ["error" => [["content" => "Error 1"]]]];
    $arr2 = ["msg" => ["error" => [["content" => "Error 2"]]]];

    $arr_expeceted_result = ["msg" => ["error" => [
      ["content" => "Error 1"],
      ["content" => "Error 2"]
    ]]];

    $this->assertEquals(
      $arr_expeceted_result,
      ArrayUtils::add_data("msg:error:[]", $arr1, ["content" => "Error 2"])
    );

    $arr1 = [];

    $this->assertEquals(
      ["msg" => ["error" => [["content" => "Error 1"]]]],
      ArrayUtils::add_data("msg:error:[]", $arr1, ["content" => "Error 1"])
    ); 

    $this->assertEquals(
      ["msg" => [
        "error" => [["content" => "Error 1"]],
        "success" => [["content" => "Success 1"]]
        ]
      ],
      ArrayUtils::add_data("msg:success:[]", $arr1, ["content" => "Success 1"])
    ); 


    $this->assertEquals(
      ["msg" => [
        "error" => [["content" => "Error 1"]],
        "success" => [["content" => "Success 1"],["content" => "Success 2"]]
        ]
      ],
      ArrayUtils::add_data("msg:success:[]", $arr1, ["content" => "Success 2"])
    );   
  }

  public function test_get_data() {
    $arr_data = ["msg" => ["error" => [
      ["content" => "Error 1"],
      ["content" => "Error 2"]
    ]]];

    $this->assertEquals(
        [
          ["content" => "Error 1"],
          ["content" => "Error 2"]
        ],
        ArrayUtils::get_data("msg:error", $arr_data)
      );

    $this->assertEquals(null, ArrayUtils::get_data("msg:test", $arr_data));
  }

  public function test_remove_data() {
    $arr_data = ["msg" => ["error" => [
      ["content" => "Error 1"],
      ["content" => "Error 2"]
    ]]];

    $this->assertTrue(ArrayUtils::remove_data("msg:error", $arr_data));
    $this->assertEquals(null, ArrayUtils::get_data("msg:error", $arr_data));
    $this->assertEquals(["msg" => []], $arr_data);
  }

  public function test_splice_data() {
    $arr_data = ["msg" => ["error" => [
      ["content" => "Error 1"],
      ["content" => "Error 2"]
    ]]];

    $this->assertEquals(
      ["error" => [
        ["content" => "Error 1"],
        ["content" => "Error 2"]
      ]], 
      ArrayUtils::splice_data("msg", $arr_data)
    );

    $this->assertEquals([], $arr_data);
  }

}