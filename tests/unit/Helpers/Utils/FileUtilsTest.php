<?php

use PHuby\Helpers\Utils\FileUtils;

class FileUtilsTest extends TestCase {

  public function test_add_suffix() {
    $filename = "avatar_test.jpg";
    $this->assertEquals("avatar_test_thumb.jpg", FileUtils::add_suffix($filename, "_thumb"));
  }

}