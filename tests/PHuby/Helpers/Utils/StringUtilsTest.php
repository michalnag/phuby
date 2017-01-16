<?php

require_once __DIR__ . "/../../../../lib/autoload.php";
require_once __DIR__ . "/../../../../vendor/autoload.php";

use PHuby\Helpers\Utils\StringUtils;
use PHPUnit\Framework\TestCase;
use PHuby\Config;

class StringUtilsTest extends TestCase {

  public function __construct() {
    Config::set_config_root(__DIR__."/../../../config.d");
  }

  public function test_options_string_to_array() {
    $options_string = "test:{key:{this will be a test}}";
    $arr_expected_options = [
      "test" => [
        "key" => [
          "this will be a test"
        ]
      ]
    ];

    $arr_processed_string = StringUtils::options_string_to_array($options_string);

    $this->assertEquals($arr_expected_options, $arr_processed_string);
  }

}