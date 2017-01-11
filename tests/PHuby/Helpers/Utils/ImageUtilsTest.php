<?php

require_once __DIR__ . "/../../../../lib/autoload.php";
require_once __DIR__ . "/../../../../vendor/autoload.php";

use PHuby\Helpers\Utils\ImageUtils;
use PHuby\Helpers\Utils\FileUtils;
use PHPUnit\Framework\TestCase;
use PHuby\Helpers\Utils;
use PHuby\Config;

class ImageUtilsTest extends TestCase {

  public function __construct() {
    Config::set_config_root(__DIR__."/../../../config.d");
  }

  public function test_px_to_cm() {
    $args = [
      'px' => 1200,
      'dpi' => 100
    ];

    $this->assertEquals(30.48, ImageUtils::px_to_cm($args));
  }

  public function test_px_to_cm_by_dpi() {
    $result = ImageUtils::px_to_cm_by_dpi(1200);

    $expected_result = [
      72 => '42.33',
      96 => '31.75',
      120 => '25.40',
      160 => '19.05',
      200 => '15.24',
      240 => '12.70',
      300 => '10.16'
    ];

    $this->assertInternalType('array', $result);
    $this->assertEquals($result, $expected_result);
  }

  public function test_dpi_from_px_and_cm() {
    $result = ImageUtils::dpi_from_px_and_cm([
        'px'  => 1200,
        'cm'  => 30.48
      ]);

    $this->assertEquals(100, $result);
  }

  public function test_resize() {
    // First let's copy the image
    $str_source = __DIR__ . "/../../../assets/image_01.jpg";
    $str_destination = __DIR__ . "/../../../assets/image_01_copy.jpg";
    FileUtils::copy($str_source, $str_destination, ["overwrite" => true]);

    // Resize copied image
    ImageUtils::resize([
        "image_path" => $str_destination,
        "max_width" => 500,
        "max_height" => 400
      ]);

    $arr_image_size = ImageUtils::get_image_size($str_destination);
    $this->assertEquals($arr_image_size[0], 500);
    $this->assertEquals($arr_image_size[1], 235);

    FileUtils::remove($str_destination);
  }

}