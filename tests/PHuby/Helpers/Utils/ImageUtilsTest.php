<?php

require_once __DIR__ . "/../../../../lib/autoload.php";
require_once __DIR__ . "/../../../../vendor/autoload.php";

use PHuby\Helpers\Utils\ImageUtils as IU;
use PHPUnit\Framework\TestCase;

class ImageUtilsTest extends TestCase {

  public function test_px_to_cm() {
    $args = [
      'px' => 1200,
      'dpi' => 100
    ];

    $this->assertEquals(30.48, IU::px_to_cm($args));
      
  }

  public function test_px_to_cm_by_dpi() {
    $result = IU::px_to_cm_by_dpi(1200);

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
    $result = IU::dpi_from_px_and_cm([
        'px'  => 1200,
        'cm'  => 30.48
      ]);

    $this->assertEquals(100, $result);
  }

  public function test_resize() {
    // First let's copy the image
    
  }

}