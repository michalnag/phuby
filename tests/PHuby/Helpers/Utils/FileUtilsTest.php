<?php

require_once __DIR__ . "/../../../../lib/autoload.php";
require_once __DIR__ . "/../../../../vendor/autoload.php";

use PHPUnit\Framework\TestCase;
use PHuby\Helpers\Utils\FileUtils;
use PHuby\Config;

class FileUtilsTest extends TestCase {

  public function __construct() {
    Config::set_config_root(__DIR__."/../../../config.d");
  }

  public function test_add_suffix() {
    $filename = "avatar_test.jpg";
    $this->assertEquals("avatar_test_thumb.jpg", FileUtils::add_suffix($filename, "_thumb"));
  }

}