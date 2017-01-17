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

  public function test_add_arrays() {
    $arr1 = ["msg" => ["error" => [["content" => "Error 1"]]]];
    $arr2 = ["msg" => ["error" => [["content" => "Error 2"]]]];

    $arr_expeceted_result = ["msg" => ["error" => [
      ["content" => "Error 1"],
      ["content" => "Error 2"]
    ]]];

    $this->assertEquals(
      $arr_expeceted_result,
      ArrayUtils::add_to_array("msg:error:[]", $arr1, ["content" => "Error 2"])
    );

    $arr_empty = [];

    $this->assertEquals(
      $arr1,
      ArrayUtils::add_to_array("msg:error:[]", $arr_empty, ["content" => "Error 1"])
    ); 

  }

}